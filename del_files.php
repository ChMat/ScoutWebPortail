<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* del_files.php v 1.0.3 - Suppression de photos dans le dossier img/ et ses sous-dossiers
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
if (!defined('IN_SITE'))
{
	if ($user['niveau']['numniveau'] < 5)
	{
		header('Location: 404.php');
		exit;
	}
	$dossier = urldecode($_GET['dossier']);
	$suppr = array('.', '..', '///', '//');
	$dossier = str_replace($suppr, '', $dossier);	
	if (empty($dossier))
	{
		$racine = 'img/';
	}
	else
	{
		$racine = '';
	}
	$dossier = stripslashes($racine.$dossier);
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Scout Web Portail - Suppression de dossiers et de fichiers</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
<!--
li.t {
	list-style-image: url(templates/default/images/go.png);
}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" class="body_popup">
<h1>Suppression de dossiers et de fichiers</h1>
<div class="introduction">
<p class="petit">Tu peux supprimer ici les diff&eacute;rents fichiers et 
dossiers contenus sur le portail.</p>
</div>
<?php
	if (!empty($_GET['del']))
	{
		$del = urldecode($_GET['del']);
		if (@is_dir($del))
		{
			if (@rmdir($del))
			{
?><div class="msg"><p align="center" class="rmqbleu">Le dossier '<?php echo $del; ?>' a &eacute;t&eacute; supprim&eacute;</p>
</div><?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Le dossier n'a pas pu &ecirc;tre supprim&eacute; !</p>
<p align="center" class="petitbleu">Le dossier n'est peut-être pas vide ou le portail n'a pas le droit d'y acc&eacute;der.</p>
</div>
<?php
			}
		}
		else
		{
			if (@unlink($del))
			{
?><div class="msg"><p align="center" class="rmqbleu">Le fichier '<?php echo $del; ?>' a &eacute;t&eacute; supprim&eacute;</p>
</div><?php
			}
			else
			{
?><div class="msg"><p align="center" class="rmq">Le fichier n'a pas pu &ecirc;tre supprim&eacute;</p>
  <p align="center" class="petitbleu">Le portail n'a peut-&ecirc;tre pas le droit d'y acc&eacute;der.</p>
</div><?php
			}
		}
	}
	$chemin = ereg_replace("/$", "", $dossier);
	$chemin = split('/', $chemin);
	$nb_pas = count($chemin);
	$chemin_lien = '';
	$chemin_pas = '';
	$pcdt_dossier = '';
	for ($i = 0; $i < $nb_pas; $i++)
	{
		$chemin_pas .= $chemin[$i]; 
		$chemin_lien .= ($i < $nb_pas - 1) ? '<a href="del_files.php?dossier='.urlencode($chemin_pas).'">' : '';
		$chemin_lien .= $chemin[$i];
		$chemin_lien .= ($i < $nb_pas - 1) ? '</a>/' : '/';
		$chemin_pas .= ($i < $nb_pas - 1) ? '/' : '';
		if ($i == $nb_pas - 1) $pcdt_dossier = $chemin_pas;
	}
	$dossier .= (ereg("/$", $dossier)) ? '' : '/';
	if ($listedossiers = @opendir($dossier))
	{
		echo 'Dossier en cours : <span class="rmqbleu">'.$chemin_lien.'</span>';
?>
	<ul>
<?php
		while($image = readdir($listedossiers))
		{
			if (is_dir($dossier.$image) and $image != '.' and $image != '..')
			{
?>
			
  <li class="t"><a href="del_files.php?dossier=<?php echo urlencode($dossier.$image); ?>">Dossier 
    <?php echo $image; ?></a> - <a href="javascript:if(confirm('Es-tu certain de vouloir supprimer ce dossier ?')) this.location = 'del_files.php?dossier=<?php echo urlencode($pcdt_dossier); ?>&amp;del=<?php echo urlencode($dossier.$image); ?>';" title="Supprimer ce dossier"><img src="templates/default/images/moins.png" align="middle" border="0" alt="" /></a></li>
<?php
			}
		}
		closedir($listedossiers);
?>
	</ul>
<?php

	}
	if ($open = @opendir($dossier))
	{
?>
	<ul>
<?php
		while($image = readdir($open))
		{
			if($image != '.' and $image != '..' and !is_dir($dossier.$image))
			{
				if (eregi("jpg$|gif$|png$", $dossier.$image))
				{
					echo '<li>'.$image.' <a href="javascript:if(confirm(\'Es-tu certain de vouloir supprimer ce fichier ?\')) this.location = \'del_files.php?dossier='.urlencode($dossier).'&amp;del='.urlencode($dossier.$image).'\';"><img src="templates/default/images/moins.png" align="middle" border="0" alt="Supprimer cette image" /></a><br /><img src="'.$dossier.$image.'" align="middle" border="0" alt="Supprimer cette image" /></li>';
				}
				else
				{
					echo '<li>'.$image.' <a href="javascript:if(confirm(\'Es-tu certain de vouloir supprimer ce fichier ?\')) this.location = \'del_files.php?dossier='.urlencode($dossier).'&amp;del='.urlencode($dossier.$image).'\';"><img src="templates/default/images/moins.png" align="middle" border="0" alt="" /></a></li>';
				}
			}
		}
?>
	</ul>
<?php
		closedir($open);
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Impossible de lire ce dossier ! </p>
<p align="center"><a href="del_files.php">Retour &agrave; la page pr&eacute;c&eacute;dente</a></p>
</div>
<?php
	}
?>
</body>
</html>
<?php
} // fin !defined in_site
else
{
	include('404.php');
}
?>