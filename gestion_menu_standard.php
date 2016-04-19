<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_menu_standard.php v 1.1 - Gestion du menu Général du portail
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
*	Optimisation xhtml
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2 and $user['assistantwebmaster'] != 1)
{
	include('404.php');
}
if ((!isset($_POST['do']) and !isset($_GET['do'])) or $_GET['do'] == 'modif')
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion du menu g&eacute;n&eacute;ral</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<h1>Gestion du menu g&eacute;n&eacute;ral</h1>
<p align="center"><a href="index.php?page=gestion_menus">Retour 
  &agrave; la gestion des menus</a></p>
<div class="instructions">
<p>Le menu g&eacute;n&eacute;ral est le menu qui est toujours affich&eacute; en 
  dessous des menus des sections. Le contenu de ce menu est au format html.<br />
</p>
</div>
<?php
	if ($_GET['msg'] == 'ok')
	{
?>
<div class="msg">
  <p align="center" class="rmqbleu">Modification effectu&eacute;e avec succ&egrave;s !</p>
</div>
<?php
	}
?>
<form action="gestion_menu_standard.php" method="post" name="formulaire" class="form_config_site" id="formulaire">
<h2>Modifier le menu g&eacute;n&eacute;ral</h2>
  <input type="hidden" name="do" value="savemodif" />
<p align="center">
<textarea name="valeur" cols="70" rows="10" class="sys"><?php echo $site['menu_standard']; ?></textarea>
</p>  <p align="center"> 
    <input type="submit" name="envoi" value="Enregistrer" />
    <input type="reset" name="reset" value="Recommencer" />
  </p>
  <p>Nous te sugg&eacute;rons de mettre le menu dans une liste 
    non ordonn&eacute;e comme ci-dessous :</p>
  <pre class="code" align="center">&lt;ul id=&quot;menu_general&quot;&gt;
  &lt;li&gt;&lt;a href=&quot;lien&quot;&gt;texte du lien&lt;/a&gt;&lt;/li&gt; 
&lt;/ul&gt; </pre>
</form>
<?php
}
else if ($_POST['do'] == 'savemodif')
{
	$valeur = addslashes($_POST['valeur']);
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '$valeur' WHERE champ = 'menu_standard'";
	send_sql($db, $sql);
	reset_config();
	header('Location: index.php?page=gestion_menu_standard&msg=ok');
}
else
{
?>
<div class="msg">
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<p align="center"><a href="index.php?page=gestion_menus">Retour 
  &agrave; la gestion des menus</a></p>
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