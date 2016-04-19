<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* selectdossier.php v 1.1 - Sélection d'un dossier pour la création d'un album photo local
* Fichier lié : gestion_galerie.php
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
*	Optimisation du script pour le dossier courant
*	Utilisation du 3e paramètre de getimagesize
*/

include_once('connex.php');
include_once('fonc.php');
if (!defined('IN_SITE'))
{
	if ($user['niveau']['numniveau'] < 1)
	{
		header('Location: 404.php');
		exit;
	}
	$dossier = urldecode($_GET['dossier']);
	$suppr = array('.', '..', '///', '//');
	$dossier = str_replace($suppr, '', $dossier);	
	$racine = 'img';
	if (!ereg("^$racine", $dossier)) {$dossier = $racine;}
	$dossier = stripslashes($dossier);
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Selection Dossier</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg)
{
	document.im.image.value = nomimg;
	opener.document.formulaire.photoaccueil.value = nomimg;
}

function ajoutgddossier(nomdossier)
{
	document.im.image.value = nomdossier;
	opener.document.formulaire.gdsource.value = nomdossier;
}

function ajoutptdossier(nomdossier)
{
	document.im.image.value = nomdossier;
	opener.document.formulaire.ptsource.value = nomdossier;
}

//-->
</script>
</head>
<body class="body_popup">
<h1>Gestion des galeries</h1>
<?php
	if ($_GET['x'] != 'show')
	{
?>
<p class="petit">Pour s&eacute;lectionner l'image d'accueil de ton album, clique 
  sur la photo (grande ou miniature, ce sera toujours la miniature qui sera affich&eacute;e).</p>
<p class="petit"> L'image est automatiquement ajout&eacute;e dans le formulaire. 
  Si &ccedil;a ne marche pas, recopie le code qui apparait ici : </p>
<form action="" name="im" id="im">
  <div align="center">
    <input name="image" type="text" id="codeimg" size="40" />
  </div>
</form>
</p>
<?php
	}
	else
	{
?>
<p class="rmqbleu">Aper&ccedil;u du contenu d'un dossier</p>
<?php
	}
	$chemin = ereg_replace("/$", '', $dossier);
	$chemin = split('/', $chemin);
	$nb_pas = count($chemin); // nbre de sous-dossiers
	$chemin_lien = ''; // texte du chemin à afficher ( lien/lien/...)
	$chemin_pas = ''; // chemin
	for ($i = 0; $i < $nb_pas; $i++)
	{ // liens de déplacement entre les dossiers
	  // on ne fait pas de lien vers le dossier en cours (càd à la dernière itération)
		$chemin_pas .= $chemin[$i]; // chemin en cours de traitement
		// on ajoute le lien à la liste
		$chemin_lien .= ($i < $nb_pas - 1 and ereg("^$racine", $chemin_pas)) ? '<a href="selectdossier.php?dossier='.urlencode($chemin_pas).'&amp;x='.$_GET['x'].'">' : '';
		$chemin_lien .= $chemin[$i];
		$chemin_lien .= ($i < $nb_pas - 1 and ereg("^$racine", $chemin_pas)) ? '</a>/' : '/';
		// on ajoute le / pour le sous-dossier suivant (sauf à la dernière itération
		$chemin_pas .= ($i < $nb_pas - 1) ? '/' : '';
	}
	// pour avoir un nom de dossier valide, on ajoute un / à la fin si nécessaire
	$dossier .= (!ereg("/$", $dossier)) ? '/' : '';

	if ($liste_dossier = @opendir($dossier))
	{ // On parcourt le $dossier en cours et on affiche les sous-dossiers qu'il contient
		echo '<p>Dossier en cours : <span class="rmqbleu">'.$chemin_lien.'</span></p>';
?>
<ul class="dir">
  <?php
		while($sousdossier = @readdir($liste_dossier))
		{ // on liste les répertoires d'abord
			if (is_dir($dossier.$sousdossier) and $sousdossier != '.' and $sousdossier != '..')
			{
?>
  <li><a href="selectdossier.php?dossier=<?php echo urlencode($dossier.$sousdossier); ?>&amp;x=<?php echo $_GET['x']; ?>">Dossier <?php echo $sousdossier; ?></a>
<?php
				if ($_GET['x'] != 'show')
				{
?>
    <input type="button" name="Button" value="Photos" onclick="ajoutgddossier('<?php echo $dossier.$sousdossier; ?>/')" title="Clique ici si le dossier <?php echo $sousdossier; ?> contient les grandes photos" />
    <input type="button" name="Button" value="Miniatures" onclick="ajoutptdossier('<?php echo $dossier.$sousdossier; ?>/')" title="Clique ici si le dossier <?php echo $image; ?> contient les miniatures" />
<?php
				}
				// on ajoute une aide intuitive sur le contenu probable des dossiers
				if ($sousdossier == 'pt' or $sousdossier == 'gd')
				{
					echo ($sousdossier == 'pt') ? '<span class="petitbleu">(Galerie : Miniatures)</span>' : '<span class="petitbleu">(Galerie : Photos) <img src="img/smileys/007.gif" alt="" /> Peut prendre du temps à charger</span>'; 
				}
				else if ($sousdossier == 'activites')
				{
					echo '<span class="petitbleu">(Photos des albums du site)</span>';
				}
?></li>
<?php
			}
		}
		closedir($liste_dossier);
	}
?>
</ul>
<?php
	if ($liste_dossier = @opendir($dossier))
	{
		$i = 0;
		$poids_total = 0;
		$surface = 0;
		while($fichier = @readdir($liste_dossier))
		{
			if($fichier != '.' and $fichier != '..' and eregi("\.jpg$|\.bmp$|\.gif|\.png", $fichier,$regs))
			{
				$i++;
				$image = $dossier.$fichier;
				$taille = @getimagesize($image);
				$poids = @filesize($image);
				$poids_total += $poids;
				$poids = taille_fichier($poids);
				$l = $taille[0];
				$h = $taille[1];
				$taille = $taille[3];
?>
<div class="liste_photo">
<h2><?php echo $fichier; ?><span class="petitbleu"><?php echo $l.' x '.$h.' - '.$poids; ?></span></h2>
<p align="center"><?php 
				$ajout = ($_GET['x'] != 'show') ? ' style="cursor:pointer" alt="S&eacute;lectionner cette image" title="S&eacute;lectionner cette image" onclick="ajoutimg(\''.$fichier.'\');"' : ' alt=""';
				echo '<img src="'.$image.'" '.$taille.' '.$ajout.' />';
?></p>
</div>
<?php
			}
		}
		echo '<p align="center"><strong>';
		$pl = ($i > 1) ? 's' : '';
		echo ($i > 0) ? $i.' image'.$pl.' trouv&eacute;e'.$pl.'</b> - '.taille_fichier($poids_total).'.' : 'Aucune image trouv&eacute;e';
		echo '</p>';
		closedir($liste_dossier);
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Ce dossier n'existe pas ou n'est pas accessible!</p>
<p align="center"><a href="?dossier=">Retour &agrave; la page pr&eacute;c&eacute;dente</a></p>
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