<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum_gestion.php v 1.1 - Gestion des forums du site
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
*	Nouveau fichier ajouté dans swp v 1.1
*/

include_once('connex.php');
include_once('fonc.php');

if ($user['niveau']['numniveau'] == 5)
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Forum</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
	$action = (!empty($_GET['action'])) ? $_GET['action'] : $_POST['action'];
?>
<div id="forum">
<h1>Gestion des forums</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum'; ?>" class="bouton">Retour au forum</a>
<?php
	if (!empty($action))
	{
?>
<a href="index.php?page=forum_gestion" class="bouton">Gestion du forum</a>
<?php	
	}
	else
	{
?>
<a href="index.php?page=forum_gestion&amp;action=nouveau" class="bouton">Cr&eacute;er un forum</a>
<?php	
	}
?>
</p>
<?php
	
	if ($action == 'move' and is_numeric($_GET['forum_id']) and is_numeric($_GET['new_pos']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET forum_position = forum_position + ".$_GET['new_pos']." WHERE forum_id = ".$_GET['forum_id'];
		$res = send_sql($db, $sql);
		$sql = "SELECT forum_id FROM ".PREFIXE_TABLES."forum_forums ORDER BY forum_position";
		$res = send_sql($db, $sql);
		$new_pos = 10;
		while ($forum = mysql_fetch_assoc($res))
		{
			$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET forum_position = ".$new_pos." WHERE forum_id = ".$forum['forum_id'];
			send_sql($db, $sql);
			$new_pos += 10;
		}
		$action = false;
	}
	
	if (empty($action) or !$action)
	{ // On affiche la liste des forums existants
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums ORDER BY forum_position";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{ // Il y a des forums disponibles
?>
<table class="f_liste_forums">
  <tr>
    <th>Forum</th>
    <th colspan="2">Options</th>
  </tr>
<?php
			while ($forum = mysql_fetch_assoc($res))
			{ // On liste les forums
				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
  <tr class="f_forum">
    <td valign="top" class="f_titre <?php echo 'f_'.$forum['forum_acces_niv']; ?>"><h3><?php echo $forum['forum_titre']; ?></h3>
      <?php echo makehtml($forum['forum_description']); ?></td>
    <td width="80" align="center" class="f_outils_forum">
	<a href="index.php?page=forum_gestion&amp;action=modifier&amp;forum_id=<?php echo $forum['forum_id']; ?>">Modifier</a><br />
	<a href="index.php?page=forum_gestion&amp;action=supprimer&amp;forum_id=<?php echo $forum['forum_id']; ?>">Supprimer</a>
	</td>
    <td width="80" align="center" class="f_outils_forum">
	 <a href="index.php?page=forum_gestion&amp;action=move&amp;forum_id=<?php echo $forum['forum_id']; ?>&amp;new_pos=-15" title="Remonter le forum">Monter <img src="templates/default/images/haut.png" width="12" height="12" alt="" /></a><br />
	 <a href="index.php?page=forum_gestion&amp;action=move&amp;forum_id=<?php echo $forum['forum_id']; ?>&amp;new_pos=15" title="Descendre le forum">Descendre <img src="templates/default/images/bas.png" width="12" height="12" alt="" /></a>
	</td>
  </tr>
<?php
			}
?>
</table>
<?php
		} // num_rows
		else
		{ // Aucun forum n'existe, on propose un lien pour créer les forums
			$action = 'nouveau';
		}
	} // fin affichage des forums existants

	if ($action == 'nouveau')
	{ // formulaire pour créer un forum
?>
<form action="index.php" method="post" class="form_config_site" onsubmit="return check_form();">
  <input name="page" type="hidden" id="page" value="forum_gestion" />
  <input name="action" type="hidden" id="action" value="creer" />
<script type="text/javascript">
<!--
function check_form()
{
	if (getElement('forum_titre').value == '')
	{
		alert("Merci de donner un titre au forum !");
		return false;
	}
	getElement('envoi').value = 'Patience...';
	getElement('envoi').disabled = true;
}

function check_droits()
{
	if (getElement('forum_acces_section').value != 0 && getElement('forum_acces_niv').value == 0)
	{
		getElement('forum_acces_section').value = 0;
		alert("Si le forum est en accès public, tout le monde peut y poster.");
	}
	else if (getElement('forum_acces_section').value != 0 && getElement('forum_acces_niv').value < 2)
	{
		getElement('forum_acces_niv').value = 2;
		alert("Comme seuls les membres d'une ou plusieurs sections peuvent accéder au forum, le niveau d'accès minimum a été adapté.");
	}
}
//-->
</script>
<h2>Cr&eacute;er un forum</h2>
<p>
  Titre du forum : 
  <input name="forum_titre" type="text" id="forum_titre" size="50" tabindex="1" />
</p>
<p>Description du forum :</p>
<p>  
  <textarea name="forum_description" rows="5" tabindex="2"></textarea> 
</p>
<p>Niveau d'acc&egrave;s minimum pour consulter le forum : 
  <select name="forum_acces_niv" id="forum_acces_niv" tabindex="3" onchange="check_droits();">
    <option value="0" selected="selected">Acc&egrave;s public</option>
    <option value="1">Visiteur</option>
    <option value="2">Membre de l'unit&eacute;</option>
    <option value="3">Animateur de section</option>
    <option value="4">Animateur d'unit&eacute;</option>
    <option value="5">Webmaster</option>
  </select>
</p>
<p>Sections autoris&eacute;es &agrave; acc&eacute;der au forum : 
  <select name="forum_acces_section" id="forum_acces_section" tabindex="4" onchange="check_droits();">
	<option value="0" selected="selected">Toutes les sections</option>
	<optgroup label="L'unit&eacute; et ses sections">
<?php
		foreach ($sections as $section)
		{
			if (is_unite($section['numsection']))
			{ 
				echo '<option value="'.$section['numsection'].'">'.$section['nomsection'].'</option>'."\n";
			}
		}
?>
	</optgroup>
	<optgroup label="La section uniquement">
<?php
		foreach ($sections as $section)
		{
			$valeur = $section['numsection'] * (-1);
			echo '<option value="'.$valeur.'">'.$section['nomsection'].'</option>'."\n";
		}
?>
	</optgroup>
  </select>
</p>
<p>Personnes charg&eacute;es de mod&eacute;rer le forum :
  <select name="forum_moderation" id="forum_moderation" tabindex="5">
    <option value="0" selected="selected">Le webmaster uniquement</option>
    <!--<option value="1">L'auteur de la discussion</option>-->
    <option value="2">L'auteur de la discussion et les animateurs</option>
    <option value="3">Les animateurs de section</option>
    <option value="4">Les animateurs d'unit&eacute;</option>
  </select>
</p>
<fieldset>
<legend>Etat du forum</legend>
<ul style="list-style-type:none; ">
  <li> 
    <input name="forum_statut" type="radio" id="forum_statut1" tabindex="6" value="1" checked="checked" />
    <label for="forum_statut1">Ouvert</label>
</li>
  <li>
    <input type="radio" name="forum_statut" value="0" tabindex="7" id="forum_statut0" />
    <label for="forum_statut0">Fermé / Masqu&eacute;</label> (seul le webmaster peut le voir et y poster) 
  </li>
  <li>
    <input type="radio" name="forum_statut" value="2" tabindex="8" id="forum_statut2" />
    <label for="forum_statut2">Verrouill&eacute; / Lecture seule</label> (seul le webmaster peut y poster)
</li>
  <li>
    <input type="radio" name="forum_statut" value="3" tabindex="9" id="forum_statut3" />
    <label for="forum_statut3">Verrouill&eacute; / Lecture seule</label> (seuls les modérateurs peuvent y poster)
</li>
  </ul>
</fieldset>
<p align="center">
  <input type="submit" id="envoi" value="Créer le forum" tabindex="10" />
</p>
</form>
<?php	
	}
	else if ($action == 'creer')
	{ // création du forum proprement dit
		$forum_titre = htmlentities($_POST['forum_titre'], ENT_QUOTES);
		$forum_description = htmlentities($_POST['forum_description'], ENT_QUOTES);
		$sql = "INSERT INTO ".PREFIXE_TABLES."forum_forums (forum_titre, forum_description, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section) 
		values ('$forum_titre', '$forum_description', '$_POST[forum_statut]', '$_POST[forum_moderation]', '$_POST[forum_acces_niv]', '$_POST[forum_acces_section]')";
		if ($res = @send_sql($db, $sql))
		{
?>
<div class="msg">
<p align="center">Le forum &quot;<strong><?php echo $forum_titre; ?></strong>&quot; a bien &eacute;t&eacute; cr&eacute;&eacute;.</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
	}
	else if ($action == 'modifier' and is_numeric($_GET['forum_id']))
	{ // Modifier les infos d'un forum
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_GET['forum_id']."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$forum = mysql_fetch_assoc($res);
?>
<form action="index.php" method="post" class="form_config_site" onsubmit="return check_form();">
  <input name="page" type="hidden" id="page" value="forum_gestion" />
  <input name="action" type="hidden" id="action" value="sauvegarder" />
  <input name="forum_id" type="hidden" id="forum_id" value="<?php echo $_GET['forum_id']; ?>" />
<script type="text/javascript">
<!--
function check_form()
{
	if (getElement('form_titre').value == '')
	{
		alert("Merci de donner un titre au forum !");
		return false;
	}
	getElement('envoi').value = 'Patience...';
	getElement('envoi').disabled = true;
}
//-->
</script>
<h2>Modifier un forum</h2>
<p>
  Titre du forum : 
  <input name="forum_titre" type="text" id="forum_titre" size="50" tabindex="1" value="<?php echo $forum['forum_titre']; ?>" />
</p>
<p>Description du forum :</p>
<p>  
  <textarea name="forum_description" rows="5" tabindex="2"><?php echo $forum['forum_description']; ?></textarea> 
</p>
<p>Niveau d'acc&egrave;s minimum pour consulter le forum : 
  <select name="forum_acces_niv" id="forum_acces_niv" tabindex="3">
    <option value="0"<?php echo ($forum['forum_acces_niv'] == 0) ? ' selected="selected"' : ''; ?>>Acc&egrave;s public</option>
    <option value="1"<?php echo ($forum['forum_acces_niv'] == 1) ? ' selected="selected"' : ''; ?>>Visiteur</option>
    <option value="2"<?php echo ($forum['forum_acces_niv'] == 2) ? ' selected="selected"' : ''; ?>>Membre de l'unit&eacute;</option>
    <option value="3"<?php echo ($forum['forum_acces_niv'] == 3) ? ' selected="selected"' : ''; ?>>Animateur de section</option>
    <option value="4"<?php echo ($forum['forum_acces_niv'] == 4) ? ' selected="selected"' : ''; ?>>Animateur d'unit&eacute;</option>
    <option value="5"<?php echo ($forum['forum_acces_niv'] == 5) ? ' selected="selected"' : ''; ?>>Webmaster</option>
  </select>
</p>
<p>Sections autoris&eacute;es &agrave; acc&eacute;der au forum : 
  <select name="forum_acces_section" id="forum_acces_section" tabindex="4">
	<option value="0"<?php echo ($forum['forum_acces_section'] == 0) ? ' selected="selected"' : ''; ?>>Toutes les sections</option>
	<optgroup label="L'unit&eacute; et ses sections">
<?php
			foreach ($sections as $section)
			{
				if (is_unite($section['numsection']))
				{ 
					$selectionne = ($forum['forum_acces_section'] == $section['numsection']) ? ' selected="selected"' : '';
					echo '<option value="'.$section['numsection'].'"'.$selectionne.'>'.$section['nomsection'].'</option>'."\n";
				}
			}
?>
	</optgroup>
	<optgroup label="La section uniquement">
<?php
			foreach ($sections as $section)
			{
				$valeur = $section['numsection'] * (-1);
				$selectionne = ($forum['forum_acces_section'] == $valeur) ? ' selected="selected"' : '';
				echo '<option value="'.$valeur.'"'.$selectionne.'>'.$section['nomsection'].'</option>'."\n";
			}
?>
	</optgroup>
  </select>
</p>
<p>Personnes charg&eacute;es de mod&eacute;rer le forum :
  <select name="forum_moderation" id="forum_moderation" tabindex="5">
    <option value="0"<?php echo ($forum['forum_moderation'] == 0) ? ' selected="selected"' : ''; ?>>Le webmaster uniquement</option>
    <!--<option value="1"<?php echo ($forum['forum_moderation'] == 1) ? ' selected="selected"' : ''; ?>>L'auteur de la discussion</option>-->
    <option value="2"<?php echo ($forum['forum_moderation'] == 2) ? ' selected="selected"' : ''; ?>>L'auteur de la discussion et les animateurs</option>
    <option value="3"<?php echo ($forum['forum_moderation'] == 3) ? ' selected="selected"' : ''; ?>>Les animateurs de section</option>
    <option value="4"<?php echo ($forum['forum_moderation'] == 4) ? ' selected="selected"' : ''; ?>>Les animateurs d'unit&eacute;</option>
  </select>
</p>
<fieldset>
<legend>Etat du forum</legend>
<ul style="list-style-type:none; ">
  <li> 
    <input name="forum_statut" type="radio" id="forum_statut1" tabindex="6" value="1"<?php echo ($forum['forum_statut'] == 1) ? ' checked="checked"' : ''; ?> />
    <label for="forum_statut1">Ouvert</label>
</li>
  <li>
    <input type="radio" name="forum_statut" value="0" tabindex="7" id="forum_statut0"<?php echo ($forum['forum_statut'] == 0) ? ' checked="checked"' : ''; ?> />
    <label for="forum_statut0">Fermé / Masqu&eacute;</label> (seul le webmaster peut le voir et y poster) 
  </li>
  <li>
    <input type="radio" name="forum_statut" value="2" tabindex="8" id="forum_statut2"<?php echo ($forum['forum_statut'] == 2) ? ' checked="checked"' : ''; ?> />
    <label for="forum_statut2">Verrouill&eacute; / Lecture seule</label> (seul le webmaster peut y poster)
</li>
  <li>
    <input type="radio" name="forum_statut" value="3" tabindex="9" id="forum_statut3"<?php echo ($forum['forum_statut'] == 3) ? ' checked="checked"' : ''; ?> />
    <label for="forum_statut3">Verrouill&eacute; / Lecture seule</label> (seuls les modérateurs peuvent y poster)
</li>
  </ul>
</fieldset>
<p align="center">
  <input type="submit" value="Enregistrer les modifications" tabindex="10" />
</p>
</form>
<?php	
		}
		else
		{ // le forum n'existe pas
?>
<div class="msg">
<p align="center" class="rmq">Ce forum n'existe pas !</p>
</div>
<?		
		}
	}
	else if ($action == 'sauvegarder' and is_numeric($_POST['forum_id']))
	{ // Enregistrement des modifications au forum
		$forum_titre = htmlentities($_POST['forum_titre'], ENT_QUOTES);
		$forum_description = htmlentities($_POST['forum_description'], ENT_QUOTES);
		$sql = "UPDATE ".PREFIXE_TABLES."forum_forums 
		SET 
		forum_titre = '$forum_titre', forum_description = '$forum_description', forum_statut = '$_POST[forum_statut]', 
		forum_moderation = '$_POST[forum_moderation]', forum_acces_niv = '$_POST[forum_acces_niv]', 
		forum_acces_section = '$_POST[forum_acces_section]' 
		WHERE forum_id = '$_POST[forum_id]'";
		if ($res = @send_sql($db, $sql))
		{
?>
<div class="msg">
<p align="center">Les modifications au forum &quot;<strong><?php echo $forum_titre; ?></strong>&quot; ont bien &eacute;t&eacute; enregistr&eacute;es.</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
	}
	else if ($action == 'new_pos')
	{ // Déplacer le forum
	
	}
	else if ($action == 'supprimer' and is_numeric($_GET['forum_id']))
	{ // formulaire de confirmation pour supprimer un forum
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_GET['forum_id']."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$forum = mysql_fetch_assoc($res);
?>
<form action="index.php" method="post" class="form_config_site" onsubmit="return check_form();">
  <input name="page" type="hidden" id="page" value="forum_gestion" />
  <input name="action" type="hidden" id="action" value="supprimer_action" />
  <input name="forum_id" type="hidden" id="forum_id" value="<?php echo $_GET['forum_id']; ?>" />
<script type="text/javascript">
<!--
function check_form()
{
	return confirm("Es-tu certain de vouloir supprimer ce forum ?");
}
//-->
</script>
<h2>Supprimer un forum</h2>
<p>
  Tu vas supprimer le forum &quot;<span class="rmqbleu"><?php echo $forum['forum_titre']; ?></span>&quot;.
</p>
<?php
		$sql = "SELECT forum_id, forum_titre FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id != '$_GET[forum_id]'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
?>
<fieldset>
<legend>Discussions du forum</legend>
<ul style="list-style-type:none; ">
<li> 
    <input name="contenu" type="radio" id="contenu1" tabindex="1" value="supprimer" checked="checked" />
    <label for="contenu1">Supprimer les discussions </label>
</li>
<li>
    <input type="radio" name="contenu" value="deplacer" tabindex="2" id="contenu2" />
    <label for="contenu2">D&eacute;placer les discussions dans le forum</label> 
    : 
  <select name="new_forum" id="new_forum" tabindex="3" onchange="getElement('contenu2').checked = true;">
<?php
			while ($forum = mysql_fetch_assoc($res))
			{
				echo '<option value="'.$forum['forum_id'].'">'.$forum['forum_titre'].'</option>'."\n";
			}
?>
  </select>
</li>
  </ul>
</fieldset>
<?php
		}
		else
		{
?>
<input type="hidden" name="contenu" id="contenu1" value="1" />
<?php		
		}
?>
<p align="center">
  <input type="submit" value="Supprimer le forum" tabindex="4" />
</p>
</form>
<?php
		}
		else
		{ // le forum demandé à la suppression n'existe pas
?>
<div class="msg">
<p align="center" class="rmq">Ce forum n'existe pas !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
	}
	else if ($action == 'supprimer_action' and is_numeric($_POST['forum_id']))
	{ // Suppression d'un forum
		$titre_forum = untruc(PREFIXE_TABLES.'forum_forums', 'forum_titre', 'forum_id', $_POST['forum_id']);
		if ($_POST['contenu'] == 'deplacer')
		{ // on déplace les discussions dans un autre forum
			if (is_numeric($_POST['new_forum']))
			{
				// Mise à jour des compteurs du forum cible
				
				// ... reste à insérer la mise à jour du dernier message
				$sql = "SELECT forum_nbfils, forum_nbmsg, forum_last_msg_id FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_POST['forum_id']."'";
				$res = send_sql($db, $sql);
				$forum = mysql_fetch_assoc($res);
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums 
				SET 
				forum_nbfils = forum_nbfils + '".$forum['forum_nbfils']."', 
				forum_nbmsg = forum_nbmsg + '".$forum['forum_nbmsg']."', 
				forum_last_msg_id = if (forum_last_msg_id > '".$forum['forum_last_msg_id']."', forum_last_msg_id, '".$forum['forum_last_msg_id']."') 
				WHERE 
				forum_id = '".$_POST['new_forum']."'";
				send_sql($db, $sql);

				// Suppression du forum
				$sql = "DELETE FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_POST['forum_id']."'";
				send_sql($db, $sql);
				
				// Déplacement des discussions
				$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET forum_id = '".$_POST['new_forum']."' WHERE forum_id = '".$_POST['forum_id']."'";
				send_sql($db, $sql);
		
				$sql = "UPDATE ".PREFIXE_TABLES."forum_msg SET forum_id = '".$_POST['new_forum']."' WHERE forum_id = '".$_POST['forum_id']."'";
				send_sql($db, $sql);
?>
<div class="msg">
<p align="center">Le forum &quot;<span class="rmqbleu"><?php echo $titre_forum; ?></span>&quot; a bien &eacute;t&eacute; supprim&eacute; !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas s&eacute;lectionn&eacute; le forum o&ugrave; placer les discussions de l'ancien forum !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
			}
		}
		else
		{ // on supprime les discussions du forum
			// Suppression du forum
			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_POST['forum_id']."'";
			send_sql($db, $sql);

			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_fils WHERE forum_id = '".$_POST['forum_id']."'";
			send_sql($db, $sql);
	
			$sql = "SELECT msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE forum_id = '".$_POST['forum_id']."'";
			$res = send_sql($db, $sql);
			$liste_msg = '';
			while ($ligne = mysql_fetch_assoc($res))
			{
				$liste_msg .= (!empty($liste_msg)) ? ' OR ' : '';
				$liste_msg .= "msg_id = '".$ligne['msg_id']."'";
			}
			if (!empty($liste_msg))
			{
				$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg_txt WHERE $liste_msg";
				send_sql($db, $sql);
			}
			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg WHERE forum_id = '".$_POST['forum_id']."'";
			send_sql($db, $sql);
?>
<div class="msg">
<p align="center">Le forum &quot;<span class="rmqbleu"><?php echo $titre_forum; ?></span>&quot; a bien &eacute;t&eacute; supprim&eacute; !</p>
<p align="center"><a href="index.php?page=forum_gestion">Gestion du forum</a> </p>
</div>
<?php
		}
	}
	








// fin <div id="forum">
?>
</div>
<?php
} // fin niveau == 5
else
{
	include('404.php');
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
} // fin defined in_site
?>