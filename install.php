<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* install.php v 1.1.1 - Script d'installation du portail
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
/*
* Modifications v 1.1
*	Ajout de la redirection vers le fichier d'update
*	Prise en charge du nouveau modèle de mot de passe
*	Mise en place du numéro de version du portail
*	Script d'évaluation du serveur
*	Premier jet de détection de l'url-rewriting : fonction foireuse pour l'instant => à revoir
*	Gestion plus poussée des erreurs
*	Possibilité de supprimer les tables conflictuelles à l'installation (installation sur une ancienne)
*/
/*
 * Modification v 1.1.1
 * 	build 091109 : correction d'un bug de compatibilité avec nouveaux paramètres PHP 5
 */

// On précise la version du portail que le script s'apprête à installer
$version_portail = '1.1.1';
// On précise les versions du portail depuis lesquelles le script peut effectuer des mises à jour
$can_update_from = array('1.0.x');

// on désactive l'affichage des notice pour les variables non initialisées
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// on ne charge pas fonc.php (qui charge tous les fichiers de fonctions)
// car fonc.php fait des requêtes dans la db. Or avant l'installation, la db est vide.
require_once('prv/fonc_str.php');

function return_bytes($val) 
{ // renvoie la taille en octets à partir d'une taille au format 8M
  // Merci http://be2.php.net/manual/fr/function.ini-get.php
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch($last) 
	{ // Le modifieur 'G' est disponible depuis PHP 5.1.0
		case 'g':
		  $val *= 1024;
		case 'm':
		  $val *= 1024;
		case 'k':
		  $val *= 1024;
	}
	return $val;
}

function taille_fichier($fichier)
{ // renvoie la taille d'un fichier exprimée en octets, kilo-octets, méga-octets ou giga-octets.
  // $fichier peut être le chemin vers un fichier ou son poids en octets
	if (!ereg ("^[0-9\.]+$", $fichier)) {$taille = @filesize($fichier) or $taille = 0;} else {$taille = $fichier;}
	if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . ' Go';}
	elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . ' Mo';}
	elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . ' Ko';}
	else {$taille = $taille.' octets';} 
	if($taille == 0) {$taille = '0 octet';}
	$taille = str_replace('.', ',', $taille); // on remplace le . des décimales par une virgule française :)
	return $taille;
}

$upload_max_filesize = @return_bytes(@ini_get('upload_max_filesize'));
$upload_max_filesize = taille_fichier($upload_max_filesize);

function cleunique($taille = 20)
{ // retourne une clé aléatoire de $taille caractères
	$cle = '';
	// avec la taille 'mini', on ne met pas les lettres o, i et l.
	// Elles sont parfois difficiles à différencier selon les polices de caractères
	$lettres = ($taille != 'mini') ? 'oil' : ''; 
	$lettres .= 'abcdefghjkmnpqrstuvwxyz0123456789';
	$taille = ($taille == 'mini') ? 5 : $taille;
	srand(time());
	for ($i=0; $i < $taille; $i++)
	{
		$cle .= substr($lettres,(rand()%(strlen($lettres))), 1);
	}
	return $cle;
}

// La détection de l'url-rewriting est problématique et ne réagi
function detect_urlrewriting()
{ // Détecte si l'url-rewriting est activé sur le serveur et renvoie true ou false
	return false;
	// on met phpinfo() en mémoire tampon et on recherche la chaîne mod_rewrite.
	// si elle est présente, le module d'url-rewriting est probablement chargé.
	/*if (@ob_start())
	{
		phpinfo(); 
		if ($string = @ob_get_contents())
		{
			ob_end_clean();
			ob_end_flush();
			return preg_match('/mod_rewrite/', $string);
		}
	}
	else
	{
		return false;
	}*/
}

function mysql_table_exists($table)
{ // Fonction proposée par Tim sur http://php.belnet.be/manual/fr/function.mysql-list-tables.php
     $exists = mysql_query("SELECT 1 FROM $table LIMIT 0");
     if ($exists) return true;
     return false;
}

foreach($_GET as $cle => $valeur)
{ // $_GET['x'] devient $x
	$$cle = $valeur;
}
foreach($_POST as $cle => $valeur)
{ // $_POST['x'] devient $x
	$$cle = $valeur;
}

if ($step == 2)
{ // le webmaster a rempli le formulaire, on traite ses entrées
	if (!empty($_POST['adresse_site']) and !empty($_POST['db_url']) and !empty($_POST['db_user']) and !empty($_POST['db_pw']) and !empty($_POST['db_pw_confirm']) and !empty($_POST['db_name']) and !empty($_POST['db_prefix']) and !empty($_POST['admin_pseudo']) and !empty($_POST['admin_pw']) and !empty($_POST['admin_pw_confirm']) and !empty($_POST['admin_prenom']) and !empty($_POST['admin_nom']) and !empty($_POST['admin_email']))
	{ // Tous les champs sont remplis, on les analyse
		$erreur = 0;
		if ($admin_pw != $admin_pw_confirm or strlen($admin_pw) < 6)
		{ // mot de passe du webmaster incorrect
			$admin_pw = $admin_pw_confirm = '';
			$erreur = 5;
		}
		if (!checkmail($admin_email))
		{ // l'email du webmaster n'est pas une vraie adresse email
			$admin_email = '';
			$erreur = 5;
		}
		if (checkforbiddenchar($admin_pseudo))
		{ // le pseudo du webmaster contient des caractères interdits
			$admin_pseudo = '';
			$erreur = 5;
		}
		$admin_pseudo = htmlentities($admin_pseudo, ENT_QUOTES);
		$admin_prenom = htmlentities($admin_prenom, ENT_QUOTES);
		$admin_nom = htmlentities($admin_nom, ENT_QUOTES);
		$site_ville = htmlentities($site_ville, ENT_QUOTES);
		$site_code_postal = htmlentities($site_code_postal, ENT_QUOTES);

		if ($db_pw == $db_pw_confirm and $erreur == 0)
		{ // pas d'erreur jusqu'à présent, on continue la vérification des données
			if ($link = @mysql_connect($db_url, $db_user, $db_pw))
			{ // lancement du lien vers le serveur MySQL
				// Sélection de la DB
				$db_ok = @mysql_select_db($db_name, $link);
				if ($db_ok)
				{ // On peut passer à la création des tables
					$fichier_creation_tables = 'install/creation_tables.sql';
					$fichier_fill_tables = 'install/fill_tables.sql';
					$delimiter = ';'; 
					$delimiter_basic = ';'; 
					// On charge le parser SQL
					require('install/sql_parse.php');
					// On charge le fichier de fonctions sur les dates
					require_once('prv/fonc_date.php');
	
					// On crée les tables de base du portail
					// et on les remplit avec quelques données de base
					$sql_query = @fread(@fopen($fichier_creation_tables, 'r'), @filesize($fichier_creation_tables));
					$sql_query = preg_replace('/scoutwebportail_/', $db_prefix, $sql_query);
	
					$sql_query = remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, $delimiter);
	
					for ($i = 0; $i < sizeof($sql_query); $i++)
					{ // on exécute chacune des requêtes sql
						if (trim($sql_query[$i]) != '')
						{
							if (ereg("$CREATE TABLE `?([-a-zA-Z0-9_]*)`?", $sql_query[$i], $data))
							{ // si la requête crée une table, on vérifie qu'elle n'existe pas
								if (mysql_table_exists($data[1]))
								{ // la table existe, on la supprime pour pouvoir la créer à nouveau
									if ($_POST['on_conflit'] == 'drop')
									{
										$sql = "DROP TABLE $data[1]";
										@mysql_db_query($db_name, $sql);
									}
									else
									{
										header('Location: install.php?step=1&erreur=9&table='.$data[1]);
										exit;
									}
								}
							}
							if (!$result = @mysql_db_query($db_name, $sql_query[$i]))
							{ // erreur lors de la création des tables dans la db
								header('Location: install.php?step=1&erreur=6&requete='.$i);
								exit;
							}
						}
					}
					
					// Les tables ont été créées avec succès, on les remplit avec les données de base
					$sql_query = @fread(@fopen($fichier_fill_tables, 'r'), @filesize($fichier_fill_tables));
					$sql_query = preg_replace('/scoutwebportail_/', $db_prefix, $sql_query);
					$sql_query = preg_replace('/INSTALL_DATE/', datedujour('j mois aaaa'), $sql_query);
					$sql_query = preg_replace('/PSEUDO_ADMIN/', $admin_pseudo, $sql_query);
					$sql_query = preg_replace('/EMAIL_ADMIN/', $admin_email, $sql_query);
					$sql_query = preg_replace('/SITE_ADRESSE/', $adresse_site, $sql_query);
					$sql_query = preg_replace('/SITE_VILLE/', $site_ville, $sql_query);
					$sql_query = preg_replace('/SITE_CODE_POSTAL/', $site_code_postal, $sql_query);

					// fonction mail() active ?
					$mail_actif = ($mail_actif == 1) ? '1' : '0';
					$sql_query = preg_replace('/OPTION_ENVOI_MAIL/', $mail_actif, $sql_query);
					// url_rewriting activé ?
					$url_rewriting_actif = ($_POST['u_rew_actif'] == 1) ? '1' : '0';
					$sql_query = preg_replace('/OPTION_URL_REWRITING/', $url_rewriting_actif, $sql_query);

					$sql_query = preg_replace('/INSTALL_SQL_DATETIME/', mysql_time_date(), $sql_query);
					$sql_query = preg_replace('/INSTALL_SQL_DATE/', mysql_date(), $sql_query);
	
					$sql_query = remove_remarks($sql_query);
					$sql_query = split_sql_file($sql_query, $delimiter_basic);
	
					for($i = 0; $i < sizeof($sql_query); $i++)
					{
						if (trim($sql_query[$i]) != '')
						{
							if (!($result = @mysql_db_query($db_name, $sql_query[$i])))
							{ // erreur dans l'enregistrement des données dans la db
								header('Location: install.php?step=1&erreur=7&requete='.$i);
								exit;
							}
						}
					}
					// Les tables sont remplies avec les données de base
					// voici les champs de la table auteurs au 20/05/2005 :
					/*
					num, pw, pseudo, prenom, nom, totem_scout, quali_scout, totem_jungle, email, niveau, nivdemande,
					assistantwebmaster, numsection, dateinscr, banni, clevalidation, autorise, ipinscription, nbconnex, affaide, pagesvues,
					lastconnex, siteweb, profilmembre, avatar, loisirs, majprofildone, majprofildate, newpw, conditions_acceptees
					*/
					// on définit la dose de sel à ajouter dans les mots de passe
					// L'objectif est ici de créer un hash du pw spécifique au portail afin de réduire le risque d'être victime d'un reverse engineering
					// en brute force sur les hash créés par la fonction md5.
					$dose_de_sel = cleunique(200);
					// Création du compte administrateur
					$sql = "INSERT INTO ".$db_prefix."auteurs VALUES (1, '".md5($dose_de_sel.$admin_pw)."', '".$admin_pseudo."', '".$admin_prenom."', '".$admin_nom."', '', '', '', '".$admin_email."', 2, 0, 0, 0, CURRENT_TIMESTAMP(), '0', '', '".$admin_pseudo."', '".$_SERVER['REMOTE_ADDR']."', 0, '1', 1, '0000-00-00 00:00:00', '', '', '', '', 0, '0000-00-00', '0', '0')";
					if (!@mysql_db_query($db_name, $sql))
					{ // erreur dans la requête
						header('Location: install.php?step=1&erreur=8');
						exit;
					}
					// Il reste à remplacer le fichier connex.php
					$fconnex = fopen('connex.php', 'w');
					fwrite($fconnex, chr(60)."?php\n");
					fwrite($fconnex, "// Fichier généré automatiquement à l'installation du Scout Web Portail ".$version_portail."\n");
					fwrite($fconnex, "// lancement du lien vers le serveur MySQL\n");
					fwrite($fconnex, "\$link = mysql_connect('".$db_url."', '".$db_user."', '".$db_pw."') or die('Impossible de se connecter à la base de données');\n");
					fwrite($fconnex, "// Sélection de la DB\n");
					fwrite($fconnex, "\$db = '".$db_name."';\n");
					fwrite($fconnex, "// Définition du préfixe des noms de tables\n");
					fwrite($fconnex, "define('PREFIXE_TABLES', '".$db_prefix."');\n");
					// UN_PEU_DE_SEL est utilisé pour compliquer artificiellement la complexité du mot de passe utilisateur en cas de kidnapping de la db
					fwrite($fconnex, "define('UN_PEU_DE_SEL', '".$dose_de_sel."');\n");
					fwrite($fconnex, "define('INSTALL_DONE', true);\n");
					// COOKIE_ID est le nom du cookie qui contient l'identifiant utilisateur pour cette installation du portail
					// Chaque installation a un cookie id différent, permettant ainsi à plusieurs portails de cohabiter
					// sur un même domaine.
					fwrite($fconnex, "define('COOKIE_ID', 'id_swp_".cleunique(2)."');\n");
					fwrite($fconnex, "?".chr(62)."\n");
					fclose($fconnex);
					@chmod('connex.php', 0644);

					if ($_POST['u_rew_actif'] == 1)
					{ // l'url-rewriting est activé 
						// Adaptation du fichier .htaccess pour l'url-rewriting
						$furl_rewriter = fopen('prv/htaccess', 'r'); // on lit le fichier modèle
						$url_rewriter = fread($furl_rewriter, @filesize('prv/htaccess'));
						fclose($furl_rewriter);
						$dossier_site = dirname($_SERVER['SCRIPT_NAME']); // on récupère le dossier du portail
						$dossier_site .= (ereg("/$", $dossier_site)) ? '' : '/';
						$url_rewriter = preg_replace('!/scoutwebportail/!', $dossier_site, $url_rewriter); // en met à jour les chemins
						@unlink('.htaccess'); // on supprime le .htaccess actuel
						$furl_rewriter = fopen('.htaccess', 'w'); // et on crée la nouvelle version
						@fwrite($furl_rewriter, $url_rewriter);
						@fclose($furl_rewriter);
						// pour terminer on initialise le menu général du portail avec l'url-rewriting activé
						$sql = "UPDATE ".$db_prefix."config SET valeur = '<ul id=\"menu_general\">
<li><a href=\"staff.htm\">Les Staffs</a></li>
<li><a href=\"galerie.htm\">Albums photos</a></li>
<li><a href=\"news.htm\">Actu de l&#039;Unit&eacute;</a></li>
<li><a href=\"pagesectionmaj.htm\">Pages r&eacute;centes</a></li>
<li><a href=\"fichiers.htm\">T&eacute;l&eacute;chargements</a></li>
<li><a href=\"annif.htm\">Les anniversaires</a></li>
<li><a href=\"listembsite.htm\">Liste des membres</a></li>
<li><a href=\"livreor.htm\">Livre d&#039;or</a></li>
<li><a href=\"index.php?page=aide\">Aide</a></li>
</ul>' WHERE champ = 'menu_standard'";
						@mysql_db_query($db_name, $sql);
					}
					else
					{ // l'url-rewriting n'est pas activé, on crée le menu général sachant cela
						$sql = "UPDATE ".$db_prefix."config SET valeur = '<ul id=\"menu_general\">
<li><a href=\"index.php?page=staff\">Les Staffs</a></li>
<li><a href=\"index.php?page=galerie\">Albums photos</a></li>
<li><a href=\"index.php?page=news\">Actu de l&#039;Unit&eacute;</a></li>
<li><a href=\"index.php?page=pagesectionmaj\">Pages r&eacute;centes</a></li>
<li><a href=\"index.php?page=fichiers\">T&eacute;l&eacute;chargements</a></li>
<li><a href=\"index.php?page=annif\">Les anniversaires</a></li>
<li><a href=\"index.php?page=listembsite\">Liste des membres</a></li>
<li><a href=\"index.php?page=livreor\">Livre d&#039;or</a></li>
<li><a href=\"index.php?page=aide\">Aide</a></li>
</ul>' WHERE champ = 'menu_standard'";
						@mysql_db_query($db_name, $sql);
					}
					
					// rediriger le webmaster vers la page succès de l'installation
					// lui signaler qu'il doit supprimer le dossier install, install.php et update_portail.php
					header('Location: install.php?step=done&u='.$_POST['u_rew_actif']);
				}
				else
				{ // impossible de sélectionner cette db
					$db_exname = $db_name;
					$db_name = '';
					$erreur = 4;
				}
			}
			else
			{ // impossible de se connecter au serveur mysql avec les infos fournies
				$erreur = 3;
			}
		}
		else if ($erreur == 0)
		{ // le pw d'accès à la db est incorrect
			$db_pw = $db_pw_confirm = '';
			$erreur = 2;
		}
	}
	else
	{ // un des champs n'a pas été rempli
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
<title>Installation du Scout Web Portail v <?php echo $version_portail; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" language="JavaScript" src="fonc.js"></script>
<style type="text/css">
/* Styles utilisés durant l'installation du portail */
body, #index { 
	font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #000;} 
#index {
	 margin:0px;}
#top_page {
	background: #FFF url('install/banniere.png') no-repeat; width:100%; height:60px; padding:0px;
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
.erreur, .message_info {
	margin:auto; width:80%; border:1px #DADADA solid; padding:1em; margin-bottom:1em;}
.rmq, .rmqbleu {  
	font-weight: bold; color: #C30;}
.rmqbleu {
	color: #339;}
.td-1 {
	background-color: #F3F3F3; color: #666; text-decoration: none;}
.td-2 {
	background-color: #FFF; color: #666; text-decoration: none;}
</style>
</head>

<body id="index">
<div id="top_page"></div>
<div id="corps"> 
  <h1>Installation du Scout Web Portail v <?php echo $version_portail; ?></h1>
<?php
if ($step != 'done')
{ // l'installation n'est pas terminée
?>
  <p>Merci d'avoir choisi notre portail ! Nous esp&eacute;rons qu'il te plaira 
    et que ton groupe scout en b&eacute;n&eacute;ficiera.</p>
  <p>Avant d'installer le portail, nous te conseillons fortement de lire le fichier 
    <a href="lisez-moi.html" target="_blank">lisez-moi.html</a>.</p>
  <p>Le script du portail est sous <a href="http://www.gnu.org/licenses/licenses.html#GPL" target="_blank">licence 
    GNU GPL</a>, tu peux donc y apporter les modifications qui te semblent utiles 
    et l'adapter selon les besoins de ton groupe scout.</p>
  <p>L'installation du portail est en grande partie automatis&eacute;e afin de 
    r&eacute;duire les op&eacute;rations complexes dans la base de donn&eacute;es.</p>
  <h2 id="requirements">Rappel de la configuration n&eacute;cessaire</h2>
  <p>Cette version du portail n&eacute;cessite une <strong>base de donn&eacute;es
       de type MySQL</strong> et un h&eacute;bergement supportant le <strong>PHP</strong> 
    (version 4.3 ou sup&eacute;rieure recommand&eacute;e). Id&eacute;alement,
     il est conseill&eacute; de disposer &eacute;galement des fonctionnalit&eacute;s
      suivantes : <em>upload de fichiers, url-rewriting, fonction mail(), biblioth&egrave;que
      gd (redimensionnement des images) </em>. </p>
  <p class="rmqbleu">Regarde ci-dessous l'&eacute;valuation
      r&eacute;alis&eacute;e automatiquement :
<script type="text/javascript">
<!--
function evaluation()
{
	if (getElement('txt_instr').style.display == 'none')
	{
		getElement('txt_instr').style.display = 'block';
		getElement('bouton_eval').value = 'Masquer l\'évaluation';
	}
	else
	{
		getElement('txt_instr').style.display = 'none';
		getElement('bouton_eval').value = 'Afficher l\'évaluation';
	}
}
//-->
</script>
<input type="button" id="bouton_eval" onclick="evaluation()" value="Afficher l'évaluation" />
</p>
<div id="txt_instr" style="display:none;">
<?php
/*
	On propose ici une évaluation des fonctionnalités actives ou non sur le serveur où sera installé SWP
	Cette évaluation peut encore évoluer afin de déterminer avec plus de précision les différentes options
	que l'on peut activer sur le portail
*/
?>
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr class="td-1">
      <td width="30%" align="right">PHP actif :</td>
      <td><?php if (1 > 0) { echo '<span class="rmqbleu">OK</span>'; echo ' - PHP version '.PHP_VERSION; } else { ?><span class="rmq">Inactif</span> - <strong>SWP ne fonctionne pas sans PHP</strong><?php } ?></td>
    </tr>
    <tr class="td-2">
      <td width="30%" align="right">MySQL charg&eacute;  :</td>
      <td><?php 
	if (@extension_loaded('mysql'))
	{
?><span class="rmqbleu">OK</span><?php
	}
	else
	{
?><span class="rmq">Inactif</span> - <strong>SWP ne fonctionne pas sans MySQL</strong><?php
	}
?></td>
    </tr>
  </table>
  <h3>Fonctionnalit&eacute;s utiles</h3>
  <p>Les fonctions ci-dessous apportent un plus au portail et &eacute;tendent ses options.
    Si l'une d'entre elles est d&eacute;sactiv&eacute;e, le portail peut fonctionner mais certains
    outils ne seront pas disponibles. Les outils non disponibles sont mentionn&eacute;s
    en regard des fonctions  inactives. </p>
  <table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr class="td-1">
      <td width="30%" align="right">Upload de fichiers :</td>
      <td><?php 
	if (function_exists('ini_get') and @ini_get('file_uploads')) 
	{ 
?>
  <span class="rmqbleu">Actif</span>
<?php
	} 
	else if (function_exists('ini_get'))
	{
?>
<span class="rmq">Inactif</span> - Outils non disponibles : <em>page de t&eacute;l&eacute;chargements, cr&eacute;ation d'albums photos par upload, upload des avatars, upload de photos des membres de l'unit&eacute;.</em>
<?php
	}
	else
	{
?>
<span class="rmq">Le serveur ne permet pas de v&eacute;rifier si cette fonction est active ou non</span>
<?php
	}
?><?php echo ($upload_max_filesize) ? ' (Max : '.$upload_max_filesize.')' : ''; ?></td>
    </tr>
    <tr>
      <td align="right">Url-rewriting : </td>
      <td><?php 
  	if (detect_urlrewriting()) 
	{
?><span class="rmqbleu">Actif</span> - l'url-rewriting semble actif<?php
	}
	else
	{
?>Etat inconnu (Le portail n'est pas encore en mesure de détecter l'url-rewriting)<?php
	}
?></td>
    </tr>
    <tr class="td-1">
      <td width="30%" align="right">Envoi de mails :</td>
      <td><?php 
	if (@function_exists('mail'))
	{
?><span class="rmqbleu">Actif</span><?php
	}
	else
	{
?><span class="rmq">Inactif</span> - Outils non disponibles : <em>newsletter, v&eacute;rification de l'email &agrave; l'inscription.</em><?php
	}
?></td>
    </tr>
    <tr>
      <td width="30%" align="right">Redimensionnement des photos :</td>
      <td><?php 
	if (@extension_loaded('gd')) 
	{
?><span class="rmqbleu">OK</span><?php
	}
	else
	{
?><span class="rmq">Inactif</span> - Outils non disponibles : <em>cr&eacute;ation d'albums photos par upload, upload de photos de membres de l'unit&eacute;.</em><?php
	}
?></td>
    </tr>
  </table>
</div>
<?php
}
/*
	Test préalable à l'installation. Le portail doit pouvoir écrire dans le dossier d'installation
	même si d'autres dossiers doivent être accessibles, seul le dossier principal est nécessaire
	pour pouvoir installer le portail. Les autres dossiers peuvent être chmodés plus tard.
	Des messages d'erreur sont prévus dans les scripts où ces dossiers sont utilisés.
*/
if (!$ftest = @fopen('swp_test_file.txt', 'w') or !$ftest2 = @fopen('img/swp_test_file.txt', 'w') or !$ftest3 = @fopen('config.php', 'w'))
{
?>
<h2>Etape pr&eacute;alable &agrave; l'installation</h2>
<div class="msg">
  <p><span class="rmq">Pour permettre l'installation du portail, applique un chmod 
    sur les dossiers suivants afin d'autoriser le portail &agrave; y &eacute;crire 
    : </span></p>
  <ul>
    <li><strong>dossier principal du portail</strong> (au minimum)</li>
    <li>fichier <strong>config.php</strong> </li>
    <li>dossier <strong>cache/</strong></li>
    <li>dossier <strong>img/</strong></li>
    <li>dossier <strong>img/activites/</strong></li>
    <li>dossier <strong>img/photosmembres/</strong></li>
    <li>dossier <strong>img/photosmembres/avatars/</strong></li>
    <li>dossier <strong>fichiers/</strong></li>
  </ul>
  <p>Pour cela, utilise ton logiciel FTP.</p>
  <p>Une fois que tu as termin&eacute;, poursuis l'installation en cliquant ci-dessous.</p>
  <form name="form1" id="form1" method="post" action="install.php">
    <div align="center"> 
      <input type="submit" name="Submit" value="Poursuivre l'installation" />
    </div>
  </form>
</div>
<?php
}
else
{ // le test est concluant, on supprime le fichier test qui vient d'être créé
	@fclose($ftest);
	@fclose($ftest2);
	@fclose($ftest3); // config.php sera utilisé pour mettre la config en cache
	@unlink('swp_test_file.txt');
	@unlink('img/swp_test_file.txt');
	$step = ($step == 'done') ? 'done' : 1; // on permet l'affichage du formulaire d'installation
}

if ($step == 1 and !file_exists('connex.php'))
{
?>
<h2>Passons &agrave; l'installation proprement dite</h2>
<?php
	if (!empty($erreur))
	{
?>
<div class="erreur">
<?php
		// Affichage du message d'erreur éventuel
		if ($erreur == 1)
		{
?>
  <p align="center" class="rmq">Tous les champs doivent &ecirc;tre remplis !</p>
  <?php
		}
		else if ($erreur == 2)
		{
?>
  <p align="center" class="rmq">Le mot de passe MySQL est incorrect !</p>
  <?php
		}
		else if ($erreur == 3)
		{
?>
  <p align="center" class="rmq">Une erreur s'est produite lors de la connexion 
    au serveur MySQL !</p>
  <?php
		}
		else if ($erreur == 4)
		{
?>
  <p align="center" class="rmq">La base de donn&eacute;es "<?php echo $db_exname; ?>" 
    ne semble pas exister...</p>
  <?php
		}
		else if ($erreur == 5)
		{
?>
  <p align="center" class="rmq">Merci d'encoder des donn&eacute;es correctes</p>
  <?php
		}
		else if ($erreur == 6)
		{
			// $requete affiche l'indice correspondant à la position de la requête dans le fichier d'installation
?>
  <p align="center" class="rmq">Le script d'installation a rencontr&eacute; une 
    erreur lors de la cr&eacute;tion des tables dans la base de donn&eacute;es.</p>
  <p align="center" class="petit">Merci de signaler une erreur dans la requête
   A<?php echo $requete; ?> sur <a href="http://www.scoutwebportail.org/">www.scoutwebportail.org</a>.</p>
  <p align="center" class="petit">Si tu corriges l'erreur, supprime les tables
    d&eacute;j&agrave; cr&eacute;&eacute;es avant de relancer l'installation.</p>
  <p align="center" class="rmq">Echec de l'installation !</p>
  <?php
		}
		else if ($erreur == 7)
		{
			// $requete affiche l'indice correspondant à la position de la requête dans le fichier d'installation
?>
  <p align="center" class="rmq">Le script d'installation a rencontr&eacute; une 
    erreur lors de l'enregistrement des donn&eacute;es dans la base de donn&eacute;es.</p>
  <p align="center" class="petit">Merci de signaler une erreur dans la requête 
    B<?php echo $requete; ?> sur <a href="http://www.scoutwebportail.org/">www.scoutwebportail.org</a>.</p>
  <p align="center" class="petit">Si tu corriges l'erreur, supprime les tables
    d&eacute;j&agrave; cr&eacute;&eacute;es avant de relancer l'installation.</p>
  <p align="center" class="rmq">Echec de l'installation !</p>
  <?php
		}
		else if ($erreur == 8)
		{
?>
  <p align="center" class="rmq">Impossible de cr&eacute;er le compte du webmaster</p>
  <p align="center" class="rmq">Echec de l'installation !</p>
  <?php
		}
		else if ($erreur == 9)
		{
?>
  <p align="center" class="rmq">La base de donn&eacute;es contient d&eacute;j&agrave; une table nomm&eacute;e
    '<?php echo $table; ?>'. </p>
  <p align="center" class="rmq">Echec de l'installation !</p>
  <?php
		}
?>
</div>
<?php
	}	// fin messages d'erreur
	
	// Formulaire de création
	
	// On tente de deviner le nom de l'utilisateur MySQL en prenant le nom de l'utilisateur php
	// Il est affiché par défaut comme utilisateur MySQL et comme nom de base de données.
	$db_user_devine = (function_exists(get_current_user)) ? get_current_user() : '';
	
	// On tente de deviner l'URL du site
	$adresse_site_devinee = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']);
	$adresse_site_devinee .= (ereg("/$", $adresse_site_devinee)) ? '' : '/';
?>
<script type="text/javascript">
<!--
function check_pw(lequel)
{
	if (getElement(lequel+'_pw').value != '' && getElement(lequel+'_pw_confirm').value != '')
	{
		if (getElement(lequel+'_pw').value != getElement(lequel+'_pw_confirm').value)
		{
			alert("Le mot de passe est incorrect !");
			getElement(lequel+'_pw').value = '';
			getElement(lequel+'_pw_confirm').value = '';
			getElement(lequel+'_pw').focus();
		}
	}
}
//-->
</script>
  <form name="install_form" id="install_form" method="post" action="install.php">
    <h3> 
      <input name="step" type="hidden" id="step" value="2" />
      Configuration du serveur</h3>
    <table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
      <tr class="td-1"> 
        <td width="250" align="right">Adresse du portail :</td>
        <td><input name="adresse_site" type="text" class="case_install" id="adresse_site" tabindex="1" value="<?php echo (empty($adresse_site)) ? $adresse_site_devinee : $adresse_site; ?>" />        </td>
      </tr>
      <tr> 
        <td align="right">Adresse du serveur MySQL :</td>
        <td><input name="db_url" type="text" class="case_install" id="db_url" tabindex="2" value="<?php echo (empty($db_url)) ? 'localhost' : $db_url; ?>" />
        </td>
      </tr>
      <tr class="td-1"> 
        <td align="right">Utilisateur MySQL :</td>
        <td><input name="db_user" type="text" class="case_install" id="db_user" tabindex="3" value="<?php echo (empty($db_user)) ? $db_user_devine : $db_user; ?>" /> 
        </td>
      </tr>
      <tr> 
        <td align="right">Mot de passe MySQL :</td>
        <td><input name="db_pw" type="password" class="case_install" id="db_pw" tabindex="4" value="<?php echo $db_pw; ?>" onchange="check_pw('db');" /></td>
      </tr>
      <tr class="td-1"> 
        <td align="right">Confirmation Mot de passe MySQL :</td>
        <td><input name="db_pw_confirm" type="password" class="case_install" id="db_pw_confirm" tabindex="5" value="<?php echo $db_pw_confirm; ?>" onchange="check_pw('db');" /></td>
      </tr>
      <tr> 
        <td align="right">Nom de la base de donn&eacute;es MySQL :</td>
        <td><input name="db_name" type="text" class="case_install" id="db_name" tabindex="6" value="<?php echo (empty($db_name)) ? $db_user_devine : $db_name; ?>" /></td>
      </tr>
      <tr class="td-1"> 
        <td align="right">Pr&eacute;fixe des tables Scout Web Portail :</td>
        <td><input name="db_prefix" type="text" class="case_install" id="db_prefix" tabindex="7" value="<?php echo (empty($db_prefix)) ? 'swp_' : $db_prefix; ?>" /></td>
      </tr>
      <tr> 
        <td align="right">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr class="td-1"> 
        <td align="right" valign="top"><label for="u_rew_actif">URL Rewriting 
          activ&eacute; 
          <input name="u_rew_actif" type="checkbox" id="u_rew_actif" tabindex="8" onchange="if (this.checked) {alert('Tu as activé l\'url-rewriting.\nLis bien la petite note en bas de la page avant de continuer l\'installation.');}" value="1"<?php echo (detect_urlrewriting() or $u_rew_actif == 1) ? ' checked="checked"' : ''; ?> />
          </label></td>
        <td><p>l'url-rewriting permet de r&eacute;&eacute;crire certaines des 
            adresses du portail :<br />
            <strong>exple :</strong><em> index.php?page=forum&amp;do=afffil&amp;fil=2</em> 
            devient <em>fil2.htm</em> par exemple.</p>
          <p>Les deux adresses aboutissent &agrave; la m&ecirc;me page mais la 
            deuxi&egrave;me est nettement plus simple &agrave; retenir et &agrave; 
            &eacute;crire. Comme les animateurs peuvent r&eacute;diger les pages 
        du portail eux-m&ecirc;me, cette fonction est tr&egrave;s pratique.</p></td>
      </tr>
      <tr class="td-1"> 
        <td align="right"><label for="mail_actif">Fonction mail() native de php 
          active 
          <input name="mail_actif" type="checkbox" id="mail_actif" tabindex="9" value="1"<?php echo ($mail_actif == 1 or function_exists('mail')) ? ' checked="checked"' : ''; ?> />
          </label></td>
        <td>&nbsp;</td>
      </tr>
      <tr class="td-1">
        <td align="right">&nbsp;</td>
        <td><p><span class="rmq">Note : </span>l'url-rewriting et la fonction
            mail() ne sont pas toujours activ&eacute;es sur tous les serveurs
            web. Pour en savoir plus, consulte l'<a href="#requirements" onclick="evaluation();">&eacute;valuation
           automatique</a> propos&eacute;e ci-dessus. </p>
          <p><span class="rmq">Note :</span> les informations pr&eacute;remplies ci-dessus
            sont des propositions du portail. Assure-toi qu'elles sont correctes. </p></td>
      </tr>
    </table>
    <h3>Compte Administrateur</h3>
    <table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
      <tr class="td-1"> 
        <td width="250" align="right">Pseudo :</td>
        <td><input name="admin_pseudo" type="text" class="case_install" id="admin_pseudo" tabindex="10" value="<?php echo $admin_pseudo; ?>" maxlength="32" /></td>
      </tr>
      <tr> 
        <td align="right">Mot de passe :</td>
        <td><input name="admin_pw" type="password" class="case_install" id="admin_pw" tabindex="11" title="6 caractères minimum" value="<?php echo $admin_pw; ?>" onchange="check_pw('admin');" />
          (minimum 6 caract&egrave;res)</td>
      </tr>
      <tr class="td-1"> 
        <td align="right">Confirmation :</td>
        <td><input name="admin_pw_confirm" type="password" class="case_install" id="admin_pw_confirm" tabindex="12" title="6 caractères minimum" value="<?php echo $admin_pw_confirm; ?>" onchange="check_pw('admin');" /></td>
      </tr>
      <tr> 
        <td align="right">Pr&eacute;nom :</td>
        <td><input name="admin_prenom" type="text" class="case_install" id="admin_prenom" tabindex="13" value="<?php echo $admin_prenom; ?>" maxlength="100" /></td>
      </tr>
      <tr class="td-1"> 
        <td align="right">Nom :</td>
        <td><input name="admin_nom" type="text" class="case_install" id="admin_nom" tabindex="14" value="<?php echo $admin_nom; ?>" maxlength="100" /></td>
      </tr>
      <tr> 
        <td align="right">Email :</td>
        <td><input name="admin_email" type="text" class="case_install" id="admin_email" tabindex="15" value="<?php echo $admin_email; ?>" maxlength="255" /></td>
      </tr>
    </table>
<h3>Param&egrave;tres g&eacute;n&eacute;raux de ton unit&eacute;/groupe scout</h3>
<table width="90%" border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
  <tr class="td-1">
    <td width="250" align="right">Ville :</td>
    <td><input name="site_ville" type="text" class="case_install" id="site_ville" tabindex="16" value="<?php echo $site_ville; ?>" maxlength="32" /></td>
  </tr>
  <tr>
    <td align="right">Code postal :</td>
    <td><input name="site_code_postal" type="text" class="case_install" id="site_code_postal" tabindex="17" value="<?php echo $site_code_postal; ?>" maxlength="255" /></td>
  </tr>
</table>
<h3>Option technique</h3>
    <p>En cas de conflit entre une table dans la db et une table du portail :<br />
      <label for="on_conflit_drop"><input name="on_conflit" id="on_conflit_drop" type="radio" value="drop"<?php echo ($on_conflit == 'drop') ? ' checked="checked"' : ''; ?> />
      Supprimer la table conflictuelle</label> 
      (aucun avertissement)<br />
      <label for="on_conflit_stop"><input name="on_conflit" id="on_conflit_stop" type="radio" value="stop"<?php echo ($on_conflit == 'stop' or $on_conflit != 'drop') ? ' checked="checked"' : ''; ?> />
      Interrompre l'installation</label></p>
    <p align="left"><span class="rmq">Tu as activ&eacute; l'url-rewriting ?<br />
      </span>Si une <span class="rmqbleu">erreur 500</span> appara&icirc;t apr&egrave;s 
      avoir cliqu&eacute; sur le bouton ci-dessous, supprime le fichier .htaccess cr&eacute;&eacute; 
	  par le portail &agrave; la racine du portail.<br />
      Ensuite, actualise la page o&ugrave; s'est affich&eacute;e l'erreur 500.</p>
    <p align="center"> 
      <input name="installation" type="submit" id="installation" tabindex="18" value="Continuer l'installation">
    </p>
  </form>
  <?php
} // fin if get step != done
else if ($step == 'done')
{
?>
<div class="msg">
  <p align="center">Félicitations, l'installation du portail a &eacute;t&eacute; effectu&eacute;e 
    avec succ&egrave;s !</p>
  <p align="center" class="rmqbleu">Avant de pouvoir utiliser le portail, supprime le dossier 
    'install' et le fichier 'install.php' du serveur.</p>
  <p align="center" class="petitbleu">Ensuite, tu pourras entrer 
    sur le portail et le configurer de mani&egrave;re plus approfondie.</p>
  <p align="center">Bonne continuation !</p>
    <p align="center" class="petitbleu">
      <input name="Submit" type="submit" tabindex="1" onclick="window.location = 'index.php';" value="Entrer sur le portail" />
	</p>
<?php
	if ($_GET['u'] == 1)
	{ // url-rewriting activé, on affiche un avertissement en cas de problème.
	  // Généralement, si l'url-rewriting ou le fichier .htaccess n'est pas supporté, une erreur 500 est générée.
	  // Dans ce cas, il y a de fortes chances pour qu'il n'ait même pas confirmation de l'installation du portail
	  // mais plutôt une erreur 500.
?>
<p align="center" class="petitbleu">Si une erreur se produit avec l'url-rewriting, supprime 
      le fichier .htaccess &agrave; la racine du portail.<br />
      Tu pourras &eacute;ventuellement le d&eacute;sactiver depuis le panneau 
      de configuration du Portail.</p>
      <?php
	}
?>

</div>
 <?php
}
else if ($step == 1 and file_exists('connex.php'))
{ // il semblerait que le portail soit déjà installé
  // on détermine la version du portail afin de savoir si on propose une réinstallation ou une mise à jour
	include_once('connex.php');
	$update = false;
	$version_installee = false;
	if (defined('INSTALL_DONE') and INSTALL_DONE)
	{
		function send_sql($db, $sql)
		{ // soumet une requête sql à la base de données et renvoie le résultat
			if (!$res = @mysql_db_query($db, $sql))
			{
				return false;
			}
			return $res;
		}

		// si le champ version_portail existe, c'est une réinstallation qui est nécessaire
		// s'il n'existe pas, on propose uniquement la mise à jour.
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
		  // On propose donc une réinstallation
			$update = false;
			$reinstall = true;
		}
	}
	if ($update)
	{ // Il s'agit d'une mise à jour d'une version précédente
	  // Un script de mise à jour est prévu
?>
  <h2>Mise &agrave; jour vers la version <?php echo $version_portail; ?></h2>
  <p>Le script a d&eacute;tect&eacute; une installation de SWP v <?php echo $version_installee; ?>.</p>
  <p>La mise &agrave; jour dispose d'un script automatique. Clique sur le 
      bouton ci-dessous pour t'y rendre.</p>
  <form name="form1" id="form1" method="get" action="install/update_portail.php">
    <div align="center"> 
      <input type="submit" value="Update du portail" />
    </div>
  </form>
<?php
	}
	else if (!$update and $version_installee != $version_portail and !$reinstall)
	{ // Le site contient une version du portail pour laquelle aucune mise à jour n'est prévue dans le script
?>
  <h2>Mise &agrave; jour vers la version <?php echo $version_portail; ?></h2>
  <p class="rmq">Ce script n'est pas pr&eacute;vu pour effectuer une mise &agrave; jour depuis la version <?php echo $version_installee; ?> du portail.</p>
  <p class="petit">Effectue d'abord la mise &agrave; jour vers une version supérieure du portail pour laquelle une mise &agrave; jour est pr&eacute;vue depuis la version <?php echo $version_installee; ?>.<br />
Ensuite, tu pourras installer la derni&egrave;re mise &agrave; jour vers la version <?php echo $version_portail; ?>.<br />
Ce script peut mettre &agrave; jour les versions suivantes : <?php echo implode(', ', $can_update_from); ?>.</p>
  <?php
	}
	else
	{ // Il s'agit d'une réinstallation du portail sur une version identique
?>
  <h2>R&eacute;installation du portail</h2>
<div class="msg">
  <p class="rmq">Il semblerait qu'une installation du portail existe d&eacute;j&agrave; 
    dans ce dossier.</p>
  <p>Pour relancer l'installation correctement, tu dois supprimer les &eacute;l&eacute;ments 
    suivants :</p>
<ul>
  <li><strong>connex.php</strong></li>
  <li>&eacute;ventuellement <strong>config.php</strong></li>
  <li>&eacute;ventuellement <strong>.htaccess</strong></li>
  <li>&eacute;ventuellement les fichiers <strong>.cache</strong> contenus dans le dossier <strong>cache/</strong></li>
  </ul>
  <p>Une fois que tu as v&eacute;rifi&eacute; ces &eacute;l&eacute;ments,</p>
  <form name="form1" id="form1" method="get" action="install.php">
    <div align="center"> 
      <input type="submit" value="Poursuis l'installation" />
    </div>
  </form>
</div>
<div class="msg">
  <p class="petit"><strong>R&eacute;installation depuis une sauvegarde des donn&eacute;es</strong><br />
      Si tu r&eacute;installes le portail et que tu as fait une sauvegarde des 
      donn&eacute;es du portail, installe simplement le portail comme si c'&eacute;tait 
      la premi&egrave;re installation et ensuite, remplace le fichier connex.php 
      et le contenu de la base de donn&eacute;es.</p>
    <p class="petit"><strong>Tu viens d'installer le portail ?</strong><br />
      Si tu viens de terminer l'installation du portail, tu dois supprimer le 
      <strong>dossier install</strong> et le fichier <strong>install.php</strong> 
      pour pouvoir d&eacute;marrer le portail. Une fois que c'est fait, tu peux
      <a href="index.php">entrer sur le portail</a>. </p>
</div>
<?php
	}
}
?>
</div>
</body>
</html>