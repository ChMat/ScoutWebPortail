<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* edito.php v 1.1 - Gestion du mot d'accueil sur la page d'accueil du portail (accueil.php)
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
* Modifications v 1.1 : ChMat
*	autorisation aux animateurs de section et aux cowebmasters de modifier l'édito
*	ajout de styles
*	protection du formulaire contre la destruction par du code html mal écrit
*/


include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 3 and $user['assistantwebmaster'] != 1)
{
	include('404.php');
	exit;
}
if (empty($_GET['do']) and empty($_POST['do']))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Edito de la page Unit&eacute;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<h1>Editorial du site </h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'accueil Membres</a></p>
<form action="edito.php" method="post" name="formulaire" class="form_config_site" id="formulaire">
  <h2>Modifier l'&eacute;ditorial</h2>
  <p><input type="hidden" name="do" value="savemodif" />Modifie le texte ci-dessous. Si tu n'y connais rien au 
    HTML, coche la case &quot;Texte&quot;, le texte sera automatiquement mis en 
    forme. </p>
<p align="center" class="petit">Format de l'&eacute;ditorial : 
    <label for="format_edito1"><input name="format_edito" type="radio" id="format_edito1" tabindex="1" onchange="change_format_format_edito1('html');" value="1"<?php if ($site['format_edito'] == 1) {echo ' checked="checked"';}?> />
    HTML pur</label>
    <label for="format_edito0"><input name="format_edito" type="radio" id="format_edito0" tabindex="2" onchange="change_format_format_edito1('bbcode');" value="0"<?php if ($site['format_edito'] == 0) {echo ' checked="checked"';} ?> />
    Texte</label></p>
    <?php 
	$format_defaut = ($site['format_edito'] == 1) ? 'html' : 'bbcode';
	panneau_mef_mixte('valeur', $format_defaut, 'format_edito1');
?>
    <p align="center">
    <textarea name="valeur" cols="70" rows="12" id="valeur" style="width:95%;" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php 
	// le htmlspecialchars ne détruit pas mais affiche de manière plus standard le code html et empêche qu'une 
	// balise </textarea> ne viennent semer la zizanie dans le script
	echo htmlspecialchars($site['edito'], ENT_QUOTES); 
?></textarea>
    </p>
	<p align="center">
    <input name="envoi" type="submit" tabindex="4" value="Sauvegarder" />
        
    <input name="reset" type="reset" tabindex="5" value="Recommencer" />
	</p>
</form>
<?php
}
else if ($_POST['do'] == 'savemodif')
{
		$valeur = addslashes($_POST['valeur']);
		$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '".$valeur."' WHERE champ = 'edito'";
		send_sql($db, $sql);
		$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '".$_POST['format_edito']."' WHERE champ = 'format_edito'";
		send_sql($db, $sql);
		reset_config();
		log_this("Mise à jour de l'éditorial", 'edito');
		header('Location: index.php?page=edito&do=ok');
}
else if ($_GET['do'] == 'ok')
{
?>
<h1>Editorial du site </h1>
<div class="msg">
  <p align="center">Modifications enregistr&eacute;es. </p>
  <p align="center"><a href="index.php" tabindex="1">Retour &agrave; l'accueil du site</a></p>
</div>
<?php
}
else
{
?>
<h1>Editorial du site </h1>
<div class="msg">
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<p align="center"><a href="index.php" tabindex="1">Retour &agrave; l'accueil du site</a></p>
</div>
<?php
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>