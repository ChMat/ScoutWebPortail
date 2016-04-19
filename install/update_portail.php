<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* update_portail.php v 1.1.1 - Mise à jour du portail de 1.0.x vers 1.1
* Copyright (C) 2005 ChMat
* http://www.scoutwebportail.org
*
* This file is part of Scout Web Portail.
*
* Scout Web Portail is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* Scout Web Portail is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Scout Web Portail; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA.
*/

// On précise la version du portail que le script s'apprête à installer
$version_portail = '1.1.1';
// On précise les versions du portail depuis lesquelles le script peut effectuer des mises à jour
$can_update_from = array('1.0.x');


require_once('../connex.php');
require_once('../prv/fonc_sql.php');
require_once('../prv/fonc_user.php');
require_once('../prv/fonc_date.php');
require_once('../prv/emailer.php');

$step = (isset($_POST['step'])) ? $_POST['step'] : $_GET['step'];
if ($_POST['step'] == 2)
{ // le webmaster commence la mise à jour
	foreach($_POST as $cle => $valeur)
	{ // $_POST['x'] devient $x
		$$cle = $valeur;
	}
	if (!empty($_POST['admin_pseudo']) and !empty($_POST['admin_pw']))
	{ // l'utilisateur a rempli pseudo et mot de passe
		$erreur = 0;
		// on vérifie son identité à la manière swp 1.0
		$pw = md5($_POST['admin_pw']);
		$pseudo = htmlentities($_POST['admin_pseudo'], ENT_QUOTES);
		$sql = "SELECT a.num, a.email, a.pseudo FROM ".PREFIXE_TABLES."auteurs as a, ".PREFIXE_TABLES."site_niveaux as b WHERE a.pseudo = '$pseudo' AND a.pw = '$pw' AND a.niveau = b.idniveau AND b.numniveau = '5' AND a.banni != '1'";
		$res = @send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$webmaster = @mysql_fetch_assoc($res);
			// On récupère les données du webmaster histoire de gérer le log des erreurs sql si nécessaire
			$site['mailwebmaster'] = $webmaster['email'];
			$site['webmaster'] = $webmaster['pseudo'];
			$site['adressesite'] = 'Update de SWP';
			
			/* Structure de la base de données
			***************************************/
			$fichier_modif_tables = 'update_1.1.sql';
			$delimiter = ';'; 
			$delimiter_basic = ';'; 
			// On charge le parser SQL
			require('sql_parse.php');

			// on met la db à jour avec les nouvelles données
			$sql_query = @fread(@fopen($fichier_modif_tables, 'r'), @filesize($fichier_modif_tables));
			$sql_query = preg_replace('/scoutwebportail_/', PREFIXE_TABLES, $sql_query);

			$sql_query = remove_remarks($sql_query);
			$sql_query = split_sql_file($sql_query, $delimiter);

			for ($i = 0; $i < sizeof($sql_query); $i++)
			{
				if (trim($sql_query[$i]) != '')
				{
					if (!($result = @send_sql($db, $sql_query[$i])))
					{ // erreur lors de la mise à jour des tables dans la db
						header('Location: update_portail.php?erreur=6&requete='.$i);
						exit;
					}
				}
			}
			
			/* Mot de passe utilisateur
			***************************************/
			// Comme le hashage des mots de passe est renforcé, on liste les utilisateurs qui ne l'ont pas encore modifié
			// càd tous au début. Et à chaque connexion de ceux-ci, on met leur mot de passe à jour automatiquement
			// le webmaster peut ainsi suivre l'évolution de la mise à jour.
			$sql = "CREATE TABLE `".PREFIXE_TABLES."auteurs_pw_v11` (`num` INT UNSIGNED NOT NULL, PRIMARY KEY ( `num` )) COMMENT = 'Mise a jour v 1.1';";
			send_sql($db, $sql);
			// récupération des numéros utilisateur
			$sql = "SELECT num FROM ".PREFIXE_TABLES."auteurs ORDER BY num";
			$res = send_sql($db, $sql);
			while ($ligne = mysql_fetch_assoc($res))
			{ // insertion des numéros utilisateur dans la table de mise à jour
				$sql = "INSERT INTO ".PREFIXE_TABLES."auteurs_pw_v11 VALUES ('".$ligne['num']."')";
				send_sql($db, $sql);
			}
			
			// Comme tous les mots de passe doivent être mis à jour, on déconnecte tout le monde.
			$sql = "DELETE FROM ".PREFIXE_TABLES."connectes";
			send_sql($db, $sql);
			
			/* .htaccess
			***************************************/
			if (untruc(PREFIXE_TABLES.'config', 'valeur', 'champ', 'url_rewriting_actif') == 1)
			{ // Mise à jour du fichier .htaccess (pour l'url-rewriting)
				$furl_rewriter = fopen('../prv/htaccess', 'r');
				$url_rewriter = fread($furl_rewriter, @filesize('../prv/htaccess'));
				fclose($furl_rewriter);
				// le dossier à prendre en compte est celui de la racine du site
				$dossier_site = ereg_replace('install$', '', dirname($_SERVER['SCRIPT_NAME']));
				$dossier_site .= (ereg("/$", $dossier_site)) ? '' : '/';
				$url_rewriter = preg_replace('!/scoutwebportail/!', $dossier_site, $url_rewriter);
				@unlink('../.htaccess');
				$furl_rewriter = @fopen('../.htaccess', 'w');
				@fwrite($furl_rewriter, $url_rewriter);
				@fclose($furl_rewriter);
			}
			
			/* Vérification des statuts protégés webmaster et visiteur
			***************************************/
			$protection_statuts = '1';

			$sql = "SELECT * FROM ".PREFIXE_TABLES."site_niveaux WHERE idniveau <= '2' ORDER BY idniveau";
			$res = send_sql($db, $sql);
			while ($u_niveau = mysql_fetch_assoc($res))
			{
				if ($u_niveau['idniveau'] == 1)
				{ // Statut de visiteur
					if ($u_niveau['numniveau'] != '1' or $u_niveau['section_niveau'] != '0')
					{ // Il a été modifié, on le reprotège
						$sql = "UPDATE ".PREFIXE_TABLES."site_niveaux SET nomniveau = 'Visiteur', numniveau = '1', section_niveau = '0' WHERE idniveau = '1'";
						send_sql($db, $sql);
						$protection_statuts = '0';
					}
				}
				if ($u_niveau['idniveau'] == 2)
				{ // Statut de webmaster
					if ($u_niveau['numniveau'] != '5' or $u_niveau['section_niveau'] != '0')
					{ // Il a été modifié, on le reprotège
						$sql = "UPDATE ".PREFIXE_TABLES."site_niveaux SET nomniveau = 'Webmaster', numniveau = '5', section_niveau = '0' WHERE idniveau = '2'";
						send_sql($db, $sql);
						$protection_statuts = '0';
						
						// Afin d'éviter que le webmaster ne puisse ensuite se reconnecter
						// si vraiment il a semé la zizanie dans les statuts, on lui redonne le statut de webmaster officiel
						$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET niveau = '2' WHERE num = '".$webmaster['num']."'";
					}
				}
			}
			
			/* Vérification des statuts d'animateur : ils doivent être liés à une section
			***************************************/
			$sql = "SELECT * FROM ".PREFIXE_TABLES."site_niveaux WHERE numniveau >= '3' AND numniveau <= 4 AND section_niveau = 0";
			$res = send_sql($db, $sql);
			$protection_statuts_anim = (mysql_num_rows($res) > 0) ? '0' : '1';

			/* Suppression éventuelle des fichiers obsolètes de la version précédente
			***************************************/
			$suppression_ok = '1';
			$fichiers_a_supprimer = array(
			'afflisting2.php', 'afflisting.php', 'anciensphoto.php', 'envoi_fichier.php', 'forum_staffs.php',
			'gestionnews.php', 'liste_sections.php', 'liste_staffs.php', 'listinganciens2.php', 'listinganciens.php',
			'sectionphoto2.php', 'update.php', 'update_partie.php', 'prv/fonc_securite.php');
			
			foreach($fichiers_a_supprimer as $fichier)
			{
				if (file_exists('../'.$fichier))
				{
					if (!@unlink('../'.$fichier))
					{
						$suppression_ok = '0';
					}
				}				
			}
			
			/* Date de mise à jour du portail - just for fun
			***************************************/
			$update_date = datedujour('j mois aaaa');
			$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '$update_date' WHERE champ = 'maj'";
			send_sql($db, $sql);

			/* Cache de configuration du portail
			***************************************/
			$new_config = @fopen('config.php', 'w');
			@fclose($new_config);

			/* Transfert des messages du forum public
			***************************************/

			$sql = "SELECT count(*) as nbre_fils FROM ".PREFIXE_TABLES."filsforum";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre_fils'] > 0 and $update_forums == 'oui')
			{ // On renvoie vers le message pour passer au transfert des forums
				header('Location: update_portail.php?step=done_forum&prot_s='.$protection_statuts.'&prot_a='.$protection_statuts_anim.'&old_files='.$suppression_ok);
				exit;
			}

			/* Transfert des messages du forum des staffs
			***************************************/

			$sql = "SELECT count(*) as nbre_fils FROM ".PREFIXE_TABLES."filsforum_staffs";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre_fils'] > 0 and $update_forums == 'oui')
			{ // On renvoie vers le message pour passer au transfert des forums
				header('Location: update_portail.php?step=done_forum&prot_s='.$protection_statuts.'&prot_a='.$protection_statuts_anim.'&old_files='.$suppression_ok);
				exit;
			}
			
			/* Fin de la mise à jour si le webmaster ne récupère pas les messages des forums
			***************************************/
			header('Location: update_portail.php?step=done&prot_s='.$protection_statuts.'&prot_a='.$protection_statuts_anim.'&old_files='.$suppression_ok);
		}
		else
		{
			$erreur = 2;
		}
	}
	else
	{
		$erreur = 1;
	}
?>

<?php
}
?>
<?php echo '<?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mise &agrave; jour du Scout Web Portail - v <?php echo $version_portail; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript" src="../fonc.js"></script>
<style type="text/css">
/* Styles utilisés durant l'installation du portail */
body, #index { 
	font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #000;} 
#index {
	 margin:0px;}
#top_page {
	background: #FFF url('banniere.png') no-repeat; width:100%; height:60px; padding:0px;
	border-bottom:1px #930 solid; text-indent:300px;}
#corps {
	margin-left:5px;}
a {
	color: #339;}
a:hover {
	color: #930;}
h1 {
	font-family: Helvetica, Verdana, Arial, sans-serif; font-size: 18px; font-weight:normal; 
	color: #C05A27; text-indent: 30px; 
	line-height: 35px; border-bottom: 1px #930 solid;}
h2, .titre2 {
	font-size: 14px; color: #C05A27; font-weight: bold; background-color: #FFF8DC; border-bottom: 1px #D19275 solid;}
h3 {
	font-size: 13px; color: #339; font-weight:bold;}
input, select, td, textarea {
	font-size: 11px; color: #666; font-family: Verdana, Arial, Helvetica, sans-serif;}
.cadrenoir {
	border: 1px #930 solid;}
.case_install {
	width:200px;}
.petit, .petitbleu {
	font-size: 10px; font-family: Verdana, Arial, Helvetica, sans-serif;}
.petitbleu {
	color: #69C;}
.rmq, .rmqbleu {  
	font-weight: bold; color: #C30;}
.rmqbleu {
	color: #339;}
.td-1 {
	background-color: #F3F3F3; color: #666; text-decoration: none;}
.td-2 {
	background-color: #FFF; color: #666; text-decoration: none;}
.erreur, .message_info {
	margin:auto; width:80%; border:1px #DADADA solid; padding:1em; margin-bottom:1em;}
</style>
</head>

<body id="index">
<div id="top_page"></div>
<div id="corps"> 
  <h1>Mise &agrave; jour du Scout Web Portail - Passage &agrave; la version <?php echo $version_portail; ?></h1>
  <?php
if ($step != 'done' and $step != 'done_forum')
{
?>
  <p>Bienvenue sur le script de mise &agrave; jour automatique du portail !</p>
  <p>Avant d'effectuer la mise &agrave; jour, nous te conseillons fortement de 
    lire le fichier <a href="../lisez-moi.html" target="_blank">lisez-moi.html</a>.</p>
  <?php
}

$step = ($step == 'done' or $step == 'done_forum') ? $step : 1; // on permet l'affichage du formulaire d'installation

if ($step == 1 and file_exists('../connex.php'))
{
	// Affichage du message d'erreur éventuel
	if ($erreur == 1)
	{
?>
<div class="erreur">
  <p align="center" class="rmq">Tous les champs doivent &ecirc;tre remplis !</p>
</div>
  <?php
	}
	else if ($erreur == 2)
	{
?>
<div class="erreur">
  <p align="center" class="rmq">Le pseudo ou le mot de passe est incorrect, ou
    n'appartient pas au webmaster !</p>
</div>
  <?php
	}
	else if ($erreur == 6)
	{
?>
<div class="erreur">
  <p align="center" class="rmq">Le script d'installation a rencontr&eacute; une 
    erreur lors de la mise &agrave; jour de la base de donn&eacute;es.</p>
  <p align="center" class="petit">Merci de signaler une erreur dans la requête
    U<?php echo $_GET['requete']; ?> sur <a href="http://www.scoutwebportail.org/">www.scoutwebportail.org</a>.</p>
  <p align="center" class="rmq">Echec de la mise &agrave; jour !</p>
</div>
  <?php
	}
	// fin messages d'erreur
	
	$update = false;
	$version_installee = false;

	if (defined('INSTALL_DONE') and INSTALL_DONE)
	{
		// si le champ version_portail n'existe pas, on propose la mise à jour.
		$sql = "SELECT valeur as version_portail FROM ".PREFIXE_TABLES."config WHERE champ = 'version_portail'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 0)
			{ // version antérieure à la 1.1, update ou réinstallation
				$version_installee = '1.0.x';
				$update = true;
			}
			else
			{ // version 1.1 ou postérieure 
				$ligne = mysql_fetch_assoc($res);
				$version_installee = $ligne['version_portail'];
				
				if ($version_installee == $version_portail)
				{ // La version installee est la version de ce script d'installation
				  // On propose une réinstallation
					$update = false;
					$reinstall = true;
				}
				else if (in_array($version_installee, $can_update_from)) 
				{ // Le script de mise à jour peut effectuer l'update de la version installée actuellement
				  // $can_update_from est défini en début de script
					$update = true;
				}
				else
				{ // Le script de mise à jour de cette version n'est pas prévu
				  // pour faire une mise à jour depuis la version installée
					$update = false;
				}
			}
		}
		else
		{ // Il se peut que connex.php soit présent mais que la db soit vide
			$update = false;
		}
	}
	if ($update)
	{ // Il s'agit d'une mise à jour d'une version précédente
	  // Un script de mise à jour est prévu
?>
<script type="text/javascript">
<!--
function check_backup()
{
	return confirm("La mise à jour apporte des modifications importantes à la base de données,\nnous te conseillons fortement de faire un backup de celle-ci avant la mise à jour.\nAucun support ne sera fourni pour récupérer une base de données endommagée suite à une mise à jour sans backup.\nLa mise à jour débute juste après ce message de confirmation.\n\nEs-tu certain de vouloir lancer le script de mise à jour ?");
}
//-->
</script>
  <form name="install_form" id="install_form" method="post" action="update_portail.php" onsubmit="return check_backup();">
    <h3>Identification du webmaster
      <input name="step" type="hidden" id="step" value="2" />
    </h3>
    <p>Seul le webmaster du portail peut lancer la mise &agrave; jour, merci d'entrer 
      pseudo et mot de passe :</p>
    <table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
      <tr class="td-1"> 
        <td width="250" align="right">Pseudo :</td>
        <td><input name="admin_pseudo" type="text" class="case_install" id="admin_pseudo" value="<?php echo $admin_pseudo; ?>" maxlength="32" /></td>
      </tr>
      <tr> 
        <td align="right">Mot de passe :</td>
        <td><input name="admin_pw" type="password" class="case_install" id="admin_pw" value="<?php echo $admin_pw; ?>" title="6 caractères minimum" />
        </td>
      </tr>
      <tr>
        <td align="right">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr class="td-1">
        <td align="right"><input name="update_forums" type="checkbox" id="update_forums" value="oui" checked="checked" /></td>
        <td><label for="update_forums">Transf&eacute;rer les messages des anciens forums vers la nouvelle mouture</label></td>
      </tr>
    </table>
    <p align="center"> 
      <input name="installation" type="submit" id="installation" value="Effectuer la mise &agrave; jour">
</p>
    <h3 class="rmq">Attention</h3>
    <p> Nous te conseillons de proc&eacute;der &agrave; la mise &agrave; jour &agrave; un <span class="rmq">moment calme sur le serveur</span>. D'autant plus si tu r&eacute;cup&egrave;res les messages des forums. En effet, de nombreuses op&eacute;rations sont n&eacute;cessaires sur la base de donn&eacute;es.</p>
  </form>
  <?php
	}
	else
	{ // Le site contient une version du portail pour laquelle aucune mise à jour n'est prévue dans le script
?>
  <h2>Mise &agrave; jour vers la version <?php echo $version_portail; ?></h2>
  <p class="rmq">Ce script n'est pas pr&eacute;vu pour effectuer une mise &agrave; jour depuis la version <?php echo $version_installee; ?> du portail.</p>
  <p class="petit">Effectue d'abord la mise &agrave; jour vers une version sup&eacute;rieure du portail pour laquelle une mise &agrave; jour est pr&eacute;vue depuis la version <?php echo $version_installee; ?>.<br />
Ensuite, tu pourras installer la derni&egrave;re mise &agrave; jour vers la version <?php echo $version_portail; ?>.<br />
Ce script peut mettre &agrave; jour les versions suivantes : <? echo implode(', ', $can_update_from); ?>.</p>
  <?php
	}
	
} // fin if get step != done
if ($_GET['prot_s'] == '0')
{ // Statuts visiteur et webmaster modifiés mais corrigé par l'update
?>
<div class="erreur">
  <p align="center" class="rmqbleu">Tu as modifi&eacute; les statuts Webmaster et/ou Visiteur. Ce sont des statuts prot&eacute;g&eacute;s.<br />
    Ils sont d&eacute;sormais &agrave; nouveau prot&eacute;g&eacute;s. </p>
</div>
  <?php
}
if ($_GET['prot_a'] == '0')
{ // liaison statut animateur à une section
?>
<div class="erreur">
  <p align="center" class="rmq">Un ou plusieurs statuts d'animateur ne sont pas li&eacute;s &agrave; une section ou unit&eacute;.<br />
    Connecte-toi sur le site apr&egrave;s la mise &agrave; jour pour modifier ces statuts </p>
</div>
  <?php
}
if ($_GET['old_files'] == '0')
{ // fichiers obsolètes impossibles à supprimer
?>
<div class="erreur">
  <p align="center"><span class="rmq">Certains fichiers obsol&egrave;tes de la version pr&eacute;c&eacute;dente n'ont pas pu &ecirc;tre supprim&eacute;s par le portail.</span><br />
    Tu peux les supprimer manuellement. Une liste compl&egrave;te se trouve dans le fichier lisez-moi.html. </p>
</div>
 <?php
}
if ($step == 'done')
{
?>
  <p>Et voil&agrave;, la mise &agrave; jour du portail a &eacute;t&eacute; effectu&eacute;e 
    avec succ&egrave;s !</p>
  <p class="petitbleu">Avant de pouvoir utiliser le portail, supprime le dossier
     'install' et le fichier 'install.php' du serveur.</p>
  <p>Une fois que le dossier d'installation sera supprim&eacute;, tu pourras
    entrer  sur le portail et profiter de toutes les nouveaut&eacute;s qu'il propose.</p>
  <p>Bonne continuation !</p>
  <div align="center"> 
      <input type="submit" name="Submit" value="Entrer sur le portail" onclick="window.location = '../index.php';" />
    </p>
  </div>
<?php
}
else if ($step == 'done_forum')
{
?>
  <p>La premi&egrave;re partie de la mise &agrave; jour s'est d&eacute;roul&eacute;e correctement. </p>
  <p>Tu as choisi de r&eacute;cup&eacute;rer les messages des forums de la version pr&eacute;c&eacute;dente, clique sur le bouton ci-dessous pour lancer le transfert.</p>
  <p><span class="rmq">Attention</span><br />
    Si les forums contiennent de nombreux messages, le transfert <span class="rmq">peut prendre un certain temps</span> car l'ensemble des donn&eacute;es est d&eacute;plac&eacute; dans de nouvelles tables. </p>
  <div align="center"> 
      <input type="button" name="Submit" value="Transférer les forums" onclick="window.location = 'update_forum.php';" />
    </p>
  </div>
<?php
}
else if ($step == 1 and !file_exists('../connex.php'))
{
?>
  <h2>Aucun portail trouv&eacute;</h2>
  <p class="rmq">Il semblerait qu'aucun portail ne soit install&eacute; dans ce 
    dossier. </p>
  <p>Pour passer &agrave; l'installation du portail, clique sur le bouton ci-dessous.</p>
  <form name="form1" id="form1" method="post" action="../install.php">
    <div align="center"> 
      <input type="submit" name="Submit" value="Installer le Scout Web Portail" />
    </div>
  </form>
  <p class="petitbleu">Si tu as d&eacute;j&agrave; install&eacute; le Scout Web 
    Portail sur ce serveur, v&eacute;rifie que tu n'as pas supprim&eacute; le 
    fichier <strong>connex.php</strong>.<br />
    Si tu n'as pas de copie de sauvegarde de ton fichier connex.php mais que tu 
    avais d&eacute;j&agrave; install&eacute; le portail, consulte la section r&eacute;installation 
    pour plus d'informations sur la marche &agrave; suivre.</p>
  <?php
}
?>
</div>
</body>
</html>