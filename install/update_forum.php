<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* update_forum.php v 1.1 - Transfert des forums de 1.0.x vers 1.1
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

require_once('../connex.php');
require_once('../prv/fonc_sql.php');
require_once('../prv/fonc_str.php');
require_once('../prv/fonc_user.php');
require_once('../prv/emailer.php');

$step = (isset($_POST['step'])) ? $_POST['step'] : $_GET['step'];
if ($_POST['step'] == 2)
{ // le webmaster commence le transfert des forums
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
			$site['adressesite'] = 'Installation de SWP';

			/* Transfert des messages du forum public
			***************************************/
			// Si l'url-rewriting est activé,
			// Comme les discussions du forum public sont probablement référencées sur les moteurs de recherche,
			// On les place en premier dans le forum histoire de ne pas changer leur adresse

			$last_fil_id = 0; // Initialisation de l'id la plus grande pour les discussions
			$forum_public_last_msg_id = $forum_staffs_last_msg_id = $forum_last_msg_id = 0;
			
			$sql = "SELECT count(*) as nbre_fils FROM ".PREFIXE_TABLES."filsforum";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre_fils'] > 0)
			{ // Le forum public n'est pas vide
				// On récupère les discussions
				$sql = "SELECT * FROM ".PREFIXE_TABLES."filsforum ORDER BY num ASC";
				$res = send_sql($db, $sql);
				
				$forum_public_nbfils = mysql_num_rows($res); // Nombre de discussions dans le forum
				
				while ($old_d = mysql_fetch_assoc($res))
				{ // On parcourt les discussions et on les insère dans la nouvelle table
					$old_d['statut'] = ($old_d['statut'] == 1) ? 0 : 1; // L'indication d'une discussion bannie a été modifiée (cfr forum.php)
					
					// On renettoie les variables texte (en cas d'édition à la barbare depuis la db)
					$old_d['titre'] = htmlentities(html_entity_decode($old_d['titre'], ENT_QUOTES), ENT_QUOTES);
					
					$sql = "INSERT INTO ".PREFIXE_TABLES."forum_fils 
					(fil_id, forum_id, fil_icone, fil_titre, fil_statut, 
					fil_date, fil_nbmsg, fil_nbvues, fil_last_msg_id, fil_auteur,
					fil_moderateur)
					values
					('".$old_d['num']."', '1', '".$old_d['icone']."', '".$old_d['titre']."', '".$old_d['statut']."', 
					'".$old_d['datecreation']."', '".$old_d['nbmsg']."', '".$old_d['nbvues']."', '0', '".$old_d['auteur']."', 
					'".$old_d['moderateur']."')";
					send_sql($db, $sql);
					$last_fil_id = $old_d['num']; // On conserve l'id la plus grande pour l'insertion suivante
				}
				
				
				// On récupère les messages
				$sql = "SELECT * FROM ".PREFIXE_TABLES."msgforum ORDER BY num ASC";
				$res = send_sql($db, $sql);
				
				$forum_public_nbmsg = 0; // Initialisation du compteur de messages pour le forum public
				$forum_public_last_msg_id = 0; // Initialisation de l'id du dernier message du forum
				
				while ($old_m = mysql_fetch_assoc($res))
				{ // On parcourt les messages et on les insère dans la nouvelle table
					// Compteur de messages pour le forum public
					$forum_public_nbmsg += ($old_m['banni'] == 0) ? 1 : 0;
					
					// On garde l'id du dernier message
					$forum_public_last_msg_id = ($old_m['banni'] == 0) ? $old_m['num'] : $forum_public_last_msg_id;

					// On renettoie les variables texte (en cas d'édition à la barbare depuis la db)
					$old_m['titre'] = htmlentities(html_entity_decode($old_m['titre'], ENT_QUOTES), ENT_QUOTES);
					$old_m['msg'] = htmlentities(html_entity_decode($old_m['msg'], ENT_QUOTES), ENT_QUOTES);
					
					// Structure du message
					$sql = "INSERT INTO ".PREFIXE_TABLES."forum_msg
					(msg_id, fil_id, forum_id, msg_auteur, msg_titre, 
					msg_date, msg_statut, msg_moderateur)
					values
					('".$old_m['num']."', '".$old_m['fil']."', '1', '".$old_m['auteur']."', '".$old_m['titre']."',
					'".$old_m['datecreation']."', '".$old_m['banni']."', '".$old_m['moderateur']."')";
					send_sql($db, $sql);
					// Texte du message
					$sql2 = "INSERT INTO ".PREFIXE_TABLES."forum_msg_txt
					(msg_id, msg_txt)
					values
					('".$old_m['num']."', '".$old_m['msg']."')";
					send_sql($db, $sql2);
				}
				
				// On met à jour les compteurs du forum
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET
				forum_nbfils = '".$forum_public_nbfils."', 
				forum_nbmsg = '".$forum_public_nbmsg."', 
				forum_last_msg_id = '".$forum_public_last_msg_id."' 
				WHERE forum_id = '1'";
				send_sql($db, $sql);
				// C'est terminé pour le forum public.

			}

			/* Transfert des messages du forum des staffs
			***************************************/
			$forum_last_msg_id = $forum_public_last_msg_id;

			$sql = "SELECT count(*) as nbre_fils FROM ".PREFIXE_TABLES."filsforum_staffs";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre_fils'] > 0)
			{ // On renvoie vers la page de gestion de la mise à jour du forum
				// On récupère les discussions
				$sql = "SELECT * FROM ".PREFIXE_TABLES."filsforum_staffs ORDER BY num ASC";
				$res = send_sql($db, $sql);
				
				$forum_staffs_nbfils = mysql_num_rows($res); // Nombre de discussions dans le forum
				
				while ($old_d = mysql_fetch_assoc($res))
				{ // On parcourt les discussions et on les insère dans la nouvelle table
					$old_d['statut'] = ($old_d['statut'] == 1) ? 0 : 1; // L'indication d'une discussion bannie a été modifiée (cfr forum.php)
					
					// On modifie l'id du fil pour l'ajouter à la suite des discussions du forum public
					$fil_id = $old_d['num'] + $last_fil_id;
					
					// On renettoie les variables texte (en cas d'édition à la barbare depuis la db)
					$old_d['titre'] = htmlentities(html_entity_decode($old_d['titre'], ENT_QUOTES), ENT_QUOTES);

					$sql = "INSERT INTO ".PREFIXE_TABLES."forum_fils 
					(fil_id, forum_id, fil_icone, fil_titre, fil_statut, 
					fil_date, fil_nbmsg, fil_nbvues, fil_last_msg_id, fil_auteur,
					fil_moderateur)
					values
					('".$fil_id."', '2', '".$old_d['icone']."', '".$old_d['titre']."', '".$old_d['statut']."', 
					'".$old_d['datecreation']."', '".$old_d['nbmsg']."', '".$old_d['nbvues']."', '0', '".$old_d['auteur']."', 
					'".$old_d['moderateur']."')";
					send_sql($db, $sql);
				}
				
				// On récupère les messages
				$sql = "SELECT * FROM ".PREFIXE_TABLES."msgforum_staffs ORDER BY num ASC";
				$res = send_sql($db, $sql);
				
				$forum_staffs_nbmsg = 0; // Initialisation du compteur de messages pour le forum public
				$forum_staffs_last_msg_id = 0; // Initialisation de l'id du dernier message du forum
				
				while ($old_m = mysql_fetch_assoc($res))
				{ // On parcourt les messages et on les insère dans la nouvelle table
					// On modifie l'id du message
					$msg_id = $old_m['num'] + $forum_last_msg_id;
					
					// On modifie l'id de la discussion
					$fil_id = $old_m['fil'] + $last_fil_id;

					// Compteur de messages pour le forum public
					$forum_staffs_nbmsg += ($old_m['banni'] == 0) ? 1 : 0;
					
					// On garde l'id du dernier message
					$forum_staffs_last_msg_id = ($old_m['banni'] == 0) ? $msg_id : $forum_staffs_last_msg_id;
					
					// On renettoie les variables texte (en cas d'édition à la barbare depuis la db)
					$old_m['titre'] = htmlentities(html_entity_decode($old_m['titre'], ENT_QUOTES), ENT_QUOTES);
					$old_m['msg'] = htmlentities(html_entity_decode($old_m['msg'], ENT_QUOTES), ENT_QUOTES);

					// Structure du message
					$sql = "INSERT INTO ".PREFIXE_TABLES."forum_msg
					(msg_id, fil_id, forum_id, msg_auteur, msg_titre, 
					msg_date, msg_statut, msg_moderateur)
					values
					('".$msg_id."', '".$fil_id."', '2', '".$old_m['auteur']."', '".$old_m['titre']."',
					'".$old_m['datecreation']."', '".$old_m['banni']."', '".$old_m['moderateur']."')";
					send_sql($db, $sql);
					// Texte du message
					$sql2 = "INSERT INTO ".PREFIXE_TABLES."forum_msg_txt
					(msg_id, msg_txt)
					values
					('".$msg_id."', '".$old_m['msg']."')";
					send_sql($db, $sql2);
				}
				
				// On met à jour les compteurs du forum des staffs
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET
				forum_nbfils = '".$forum_staffs_nbfils."', 
				forum_nbmsg = '".$forum_staffs_nbmsg."', 
				forum_last_msg_id = '".$forum_staffs_last_msg_id."' 
				WHERE forum_id = '2'";
				send_sql($db, $sql);
				// C'est terminé pour le forum des staffs

			}
			
			// Il ne reste plus qu'à mettre à jour l'id du dernier message des discussions.
			$sql = "SELECT fil_id, MAX( msg_id ) AS last_msg_id
			FROM ".PREFIXE_TABLES."forum_msg
			WHERE msg_statut != '1'
			GROUP BY fil_id
			ORDER BY fil_id ASC";
			$res = send_sql($db, $sql);
			while ($fil = mysql_fetch_assoc($res))
			{
				$sql = "UPDATE ".PREFIXE_TABLES."forum_fils
				SET fil_last_msg_id = '".$fil['last_msg_id']."'
				WHERE fil_id = '".$fil['fil_id']."'";
				send_sql($db, $sql);
			}
			
			// Le transfert des forums est terminé

			/* Fin de la mise à jour
			***************************************/
			header('Location: update_forum.php?step=done');
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
<title>Mise &agrave; jour des forums du Scout Web Portail - v 1.1</title>
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
	font-size: 14px; color: #C05A27; font-weight: bold;}
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
  <h1>Mise &agrave; jour du Scout Web Portail - Transfert des forums</h1>
  <?php
if ($step != 'done')
{
?>
  <p>Avant d'effectuer le transfert des forums,  nous te conseillons fortement de 
  lire le fichier <a href="../lisez-moi.html" target="_blank">lisez-moi.html</a>.</p>
  <?php
}

$step = ($step == 'done') ? 'done' : 1; // on permet l'affichage du formulaire d'installation

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
	// fin messages d'erreur
	
?>
<script type="text/javascript">
<!--
function check_backup()
{
	return confirm("Nous te rappelons qu'aucun support ne sera fourni si tu n'as pas fait de backup de la base de données.");
}
//-->
</script>
  <form name="install_form" id="install_form" method="post" action="update_forum.php" onsubmit="return check_backup();">
    <h3>Identification du webmaster
      <input name="step" type="hidden" id="step" value="2" />
    </h3>
    <p>Seul le webmaster du portail peut transf&eacute;rer les forums, merci d'entrer 
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
    </table>
    <p align="center"> 
      <input name="installation" type="submit" id="installation" value="Effectuer la mise &agrave; jour">
    </p>
  </form>
  <?php
} // fin if get step != done
else if ($step == 'done')
{
?>
  <p>Les forums ont &eacute;t&eacute; transf&eacute;r&eacute;s avec succ&egrave;s.</p>
  <p>Apr&egrave;s avoir v&eacute;rifi&eacute; si le transfert s'est bien d&eacute;roul&eacute;, tu pourras <span class="rmqbleu">supprimer manuellement les tables des anciens forums</span> : </p>
  <ul>
    <li>swp_filsforum</li>
    <li>swp_filsforum_staffs</li>
    <li>swp_msgforum</li>
    <li>swp_msgforum_staffs</li>
  </ul>
  <p class="petitbleu">La liste des tables est &eacute;galement indiqu&eacute;e dans le fichier lisez-moi.html.</p>
  <div align="center"> 
      <input type="button" name="Submit" value="Terminer la mise à jour" onclick="window.location = 'update_portail.php?step=done';" />
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