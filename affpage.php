<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* affpage.php v 1.1 - Affichage de la source d'une page du portail rédigée par un membre
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
*	Affichage des erreurs dans la structure du portail
*/

include_once('connex.php');
include_once('fonc.php');
if (!defined('IN_SITE') and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1) and is_numeric($_GET['numpage']))
{
	$sql = 'SELECT page, contenupage FROM '.PREFIXE_TABLES.'pagessections WHERE numpage = '.$_GET['numpage'];
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$ligne = mysql_fetch_assoc($res);
		if (!empty($ligne['contenupage']))
		{
			echo stripslashes(nl2br(htmlspecialchars($ligne['contenupage'])));
		}
		else
		{
			header("Location: index.php?page=affpage&erreur=vide");
			exit;
		}
	}
	else
	{
		header("Location: index.php?page=affpage&erreur=noexist");
		exit;
	}
}
else if ($_GET['erreur'] == 'vide' or $_GET['erreur'] == 'noexist')
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Erreur</title>
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<div class="msg">
  <p align="center" class="rmq">Impossible d'afficher la source de cette page, elle est vide.</p>
</div>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
}
?>