<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* addimage.php v 1.1 - Sélection d'une image du portail pour divers usages (tally et pages du portail notamment)
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
*	Adaptation du script pour l'ajout d'images dans plusieurs zones de texte
*	Optimisation du script pour le dossier courant
*	Utilisation du 3e paramètre de getimagesize
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 1)
{
	header('Location: 404.php');
	exit;
}
$x = (is_numeric($_GET['x'])) ? $_GET['x'] : 0;
$cible = (empty($_GET['cible']) or !eregi("^[-a-z0-9_]+$", $_GET['cible'])) ? 'contenupage' : $_GET['cible'];

// Les valeurs de x déterminent le type d'insertion
/****************************************************************************/
// Tally ou pages du site
// 0 - [img=bbcode] racine img/ pour insertion dans zone de texte id="message"
// 1 - <img src="html" /> racine img/
// 3 - [img=bbcode] racine img/ zone de texte = contenupage ou $cible

// Sélection photo pour fiche membre de l'unité
// 2 - nomfichier.jpg racine /img/photosmembres/

$dossier = urldecode($_GET['dossier']);
$suppr = array('.', '..', '///', '//');
$dossier = str_replace($suppr, '', $dossier);	
if ($x == 2)
{ // photo pour fiche de membre
	$racine = 'img/photosmembres';
}
else
{ // autre image
	$racine = 'img';
}
// On vérifie que le dossier indiqué est bien à l'intérieur de la racine
if (!ereg("^$racine", $dossier)) {$dossier = $racine;}

$dossier = stripslashes($dossier);
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Insertion d'une image</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="fonc.js"></script>
<?php
if ($x == 0)
{ // image en bbcode pour zone=message
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg, largeur, hauteur)
{
	resultat = '[img=' + nomimg + '] ';
	document.im.image.value = resultat;
	opener.document.formulaire.message.value += resultat;
	alert("L'image a été insérée.");
	opener.document.formulaire.message.focus();
}
//-->
</script>
<?php
}
else if ($x == 1)
{ // image en html pour zone=$cible
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg, largeur, hauteur)
{
	resultat = '<img src="' + nomimg + '" width="' + largeur +'" height="' + hauteur +'" align="" alt="" />';
	document.im.image.value = resultat;
	//opener.document.formulaire.<?php echo $cible; ?>.value += resultat;
	opener.document.forms[0].elements['<?php echo $cible; ?>'].value += resultat;
	alert("L'image a été insérée.");
	opener.document.formulaire.<?php echo $cible; ?>.focus();
}
//-->
</script>
<?php
}
else if ($x == 2)
{ // nom du fichier image
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg, largeur, hauteur)
{
	document.im.image.value = nomimg;
	opener.document.form.photo.value = nomimg;
	opener.document.form.photo.focus();
}
//-->
</script>
<?php
}
else if ($x == 3)
{ // image en bbcode pour zone=$cible
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg, largeur, hauteur)
{
	resultat = '[img=' + nomimg + '] ';
	document.im.image.value = resultat;
	opener.document.formulaire.<?php echo $cible; ?>.value += resultat;
	alert("L'image a été insérée.");
	opener.document.formulaire.<?php echo $cible; ?>.focus();
}
//-->
</script>
<?php
}
?>
</head>
<body class="body_popup">
<?php
if ($x == 0 or $x == 3)
{ // image bbcode pour le tally ou les pages du portail en format texte
?>
<h1>Insertion d'une image</h1>
<p>Voyage dans les dossiers contenant les images du site et clique sur celle
  que tu veux ins&eacute;rer.</p>
<p> Ensuite, <br />
  - l'image est automatiquement ajout&eacute;e &agrave; la fin de ta page (balise <code>[img=cheminversl'image]</code>)<br />
  - Si tu veux aligner la photo &agrave; gauche ou &agrave; droite, <br />
  tu peux modifier la balise : <br />
  en <code>[imgleft=cheminversl'image]</code> pour l'aligner &agrave; gauche <br />
  et en <code>[imgright=cheminversl'image]</code> pour l'aligner &agrave; droite,<br />
  - Si &ccedil;a ne marche pas, recopie le code qui apparait ici : </p>
<?php
}
else if ($x == 1)
{ // image html pour les pages du portail
?>
<h1>Insertion d'une image</h1>
<p>Voyage dans les dossiers contenant les images du site et clique sur celle
  que tu veux ins&eacute;rer dans ta page.</p>
<p> Ensuite, <br />
  - l'image est automatiquement ajout&eacute;e &agrave; la fin de ta page (balise <span class="rmqbleu">&lt;img
  src=&quot;...&quot;&gt;</span>)<br />
  - Si &ccedil;a ne marche pas, recopie le code qui apparait ici : </p>
<?php
}
else if ($x == 2)
{ // sélection photo fiche membre (insertion du nom de fichier)
?>
<h1>S&eacute;lection de la photo d'un membre</h1>
<p>Clique sur la photo du membre.</p>
  <p>Ensuite, <br />
    - l'image est automatiquement ajout&eacute;e dans le formulaire.<br />
    - Si &ccedil;a ne marche pas, recopie le code qui apparait ici :</p>
<?php
}
?>
<form action="" name="im" id="im">
<p align="center">
  <input name="image" type="text" id="codeimg" size="40" />
</p>
</form>
<?php
// récupération du chemin parcouru
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
	$chemin_lien .= ($i < $nb_pas - 1 and ereg("^$racine", $chemin_pas)) ? '<a href="addimage.php?x='.$x.'&amp;cible='.$cible.'&amp;dossier='.urlencode($chemin_pas).'">' : '';
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
	while ($sousdossier = @readdir($liste_dossier))
	{ // on liste les répertoires d'abord
		if (is_dir($dossier.$sousdossier) and $sousdossier != '.' and $sousdossier != '..')
		{
?>
  <li><a href="addimage.php?x=<?php echo $x; ?>&amp;cible=<?php echo $cible; ?>&amp;dossier=<?php echo urlencode($dossier.$sousdossier); ?>"><?php echo $sousdossier; ?></a>
<?php
			// on ajoute une aide intuitive sur le contenu probable des dossiers
			if ($sousdossier == 'pt' or $sousdossier == 'gd')
			{
				echo ($sousdossier == 'pt') ? ' <span class="petitbleu">(Galerie : Miniatures)</span>' : ' <span class="petitbleu">(Galerie : Photos) <img src="img/smileys/007.gif" alt="" /> Peut prendre du temps à charger</span>'; 
			}
			else if ($sousdossier == 'activites')
			{
				echo ' <span class="petitbleu">(Photos des albums du site)</span>';
			}
?></li>
<?php
		}
	}
	closedir($liste_dossier);
?>
</ul>
<?php
}

if ($liste_dossier = @opendir($dossier))
{ // On parcourt le $dossier en cours et on affiche les images qu'il contient
	$i = 0;
	$poids_total = 0;
	$surface = 0;
?>
<?php
	while($fichier = @readdir($liste_dossier))
	{
		if($fichier != '.' and $fichier != '..' and (eregi("\.(jpg|bmp|gif|png)$", $fichier, $regs)))
		{
			$i++;
			$image = $dossier.$fichier;
			$taille = @getimagesize($image);
			$poids = @filesize($image);
			$poids_total += $poids;
			$poids = taille_fichier($poids);
			$l = $taille[0];
			$h = $taille[1];
			$dimensions = $taille[3];
?>
<div class="liste_photo">
<h2><?php echo $fichier; ?> <span class="petitbleu">(<?php echo $l.' x '.$h.' - '.$poids; ?>)</span></h2>
<p align="center"><?php 
			echo '<img src="'.$image.'" onclick="ajoutimg(\''.$image.'\', \''.$l.'\', \''.$h.'\');" style="cursor:pointer" alt="Ins&eacute;rer" title="Ins&eacute;rer cette image" '.$dimensions.' />';
?></p>
</div>
<?php
		}
	}
	// résumé du contenu du dossier
	echo '<p align="center"><strong>';
	$pl = ($i > 1) ? 's' : '';
	echo ($i > 0) ? $i.' image'.$pl.' trouv&eacute;e'.$pl.'</strong> - '.taille_fichier($poids_total).'.' : 'Aucune image trouv&eacute;e';
	echo '</p>';
	closedir($liste_dossier);
}
else
{ // impossible d'ouvrir le dossier (n'existe pas ou fonction désactivée)
?>
<div class="msg">
<p align="center" class="rmq">Impossible de lire le contenu de ce dossier !</p>
<p align="center"><a href="addimage.php?x=<?php echo $x; ?>&amp;cible=<?php echo $cible; ?>&amp;dossier=">Retour &agrave; la
    page pr&eacute;c&eacute;dente</a></p>
</div>
<?php
}
?>
</body>
</html>
