<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* backupnewpage.php - Moteur permettant d'enregistrer le code source d'une page du portail
* Cette page est affichée dans une iframe depuis la page index.php
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

include_once('connex.php');
include_once('fonc.php');

/* en cas d'erreur, la page étant appelée dans une iframe, aucun message d'erreur ne peut être donné. */
if (($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1) and is_numeric($_GET['numpage']) and empty($_GET['do']))
{
	$sql = "SELECT page, contenupage FROM ".PREFIXE_TABLES."pagessections WHERE numpage = '".$_GET['numpage']."'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$ligne = @mysql_fetch_assoc($res);
		if (!empty($ligne['contenupage']))
		{
			$NomFichier = $ligne['page'].'.txt';
			$taille = strlen($ligne['contenupage']);
			header('Content-Type: application/force-download; name="'.$NomFichier.'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.$taille);
			header('Content-Disposition: attachment; filename="'.$NomFichier.'"');
			header('Expires: 0');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
			echo stripslashes($ligne['contenupage']);
		}
		else
		{ // la page est vide
			header("Location: index.php?page=backupnewpage&do=vide");
			exit;
		}
	}
	else
	{ // la page n'existe pas
		header("Location: index.php?page=backupnewpage&do=notexist");
		exit;
	}
}
else if ($_GET['do'] == 'vide')
{ // la personne n'est pas connectée
?>
<div class="msg">
<p align="center" class="rmq">La page est vide...</p>
<p align="center"><a href="index.php?page=pagesection">Gestion des pages du site</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'notexist')
{ // la personne n'est pas connectée
?>
<div class="msg">
<p align="center" class="rmq">Cette page n'existe pas...</p>
<p align="center"><a href="index.php?page=pagesection">Gestion des pages du site</a></p>
</div>
<?php
}
else
{ // la personne n'est pas connectée
	header("Location: index.php?page=404");
	exit;
}
?>