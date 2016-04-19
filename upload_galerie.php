<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* upload_galerie.php v 1.1 - Enregistrement et redimensionnement des photos des albums du portail
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
*	Prise en charge paramètres de configuration du portail
*	Utilisation du 3e paramètre de getimagesize
*/

include_once('connex.php');
include_once('fonc.php');
if ($fghgfha)
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Création d'albums photos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" type="text/css" />
</head>

<body>
<?php
}

if (($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1) and ($site['galerie_actif'] == 1 or $user['niveau']['numniveau'] == 5))
{
	if ($_POST['step'] == 3)
	{
		$dossier = urldecode($_POST['dossier']);
		$suppr = array('.', '..', '///', '//');
		$dossier = str_replace($suppr, '', $dossier);	
		$racine = (empty($dossier)) ? 'img/' : '';
		$dossier = stripslashes($racine.$dossier);
		$destination_provisoire = $dossier; // c'est le dossier de base de l'album en cours
		if ($_FILES['fichier'] != 'none' and $_FILES['fichier']['size'] != 0 and eregi("jpg$", $_FILES['fichier']['name']) and $_POST['next'] == 1)
		{
			// $newname sera le nom de la photo sur le serveur.
			$newname = date('YmdHi').'_'.cleunique(2).'_'.$user['num'].'.jpg';
			if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination_provisoire.$newname))
			{
				// 26/12/2004 : createthumbnail pourrait prendre la place de ce script énorme
				// a voir selon la version mini de php
				$img_original = $destination_provisoire.$newname; // adresse relative du fichier à redimensionner
				// calcul de la taille de redimensionnement
				// ** l'objectif est de faire rentrer l'image dans un carré de 500 par 500 pixels et dans un autre de 140 par 140 pixels
				$proportions_gde_img = (is_numeric($site['galerie_proportions_photo'])) ? $site['galerie_proportions_photo'] : 500;
				$proportions_pte_img = (is_numeric($site['galerie_proportions_mini'])) ? $site['galerie_proportions_mini'] : 140;
				$dossier_gd = $destination_provisoire.'gd/';
				$dossier_pt = $destination_provisoire.'pt/';
				if (!is_dir($dossier_gd)) 
				{
					if (!@mkdir($dossier_gd)) 
					{ // impossible de créer le dossier gd
						@unlink($img_original);
						log_this('Impossible de créer le dossier '.$dossier_gd, 'galerie', true);
						header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=2&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
						exit;
					}
				}
				if (!is_dir($dossier_pt)) 
				{
					if (!@mkdir($dossier_pt))
					{ // impossible de créer le dossier pt
						@unlink($img_original);
						log_this('Impossible de créer le dossier '.$dossier_pt, 'galerie', true);
						header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=2&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
						exit;
					}			
				}
				$taille_original  = @getimagesize($img_original);
				$largeur_original = $taille_original[0];
				$hauteur_original = $taille_original[1];
				if ($largeur_original >= $proportions_pte_img or $hauteur_original >= $proportions_pte_img)
				{ // le script de redimensionnement pourrait être optimisé afin de ne s'exécuter que si c'est nécessaire
				  // dans une prochaine version pitêtre ;) ou tu es volontaire ? oui, toi qui lis ceci ;)
					if ($largeur_original >= $hauteur_original)
					{ // image plus large que haute ou carrée
						// calcul taille grande image
						if ($largeur_original >= $proportions_gde_img or $hauteur_original >= $proportions_gde_img)
						{
							$largeur_gde_img = $proportions_gde_img;
							$rapport_div_gde_img = $largeur_original / $largeur_gde_img;
							$hauteur_gde_img = round($hauteur_original / $rapport_div_gde_img);
						}
						else
						{
							$largeur_gde_img = $largeur_original;
							$hauteur_gde_img = $hauteur_original;
						}
						// calcul taille miniature
						$largeur_pte_img = $proportions_pte_img;
						$rapport_div_pte_img = $largeur_original / $largeur_pte_img;
						$hauteur_pte_img = round($hauteur_original / $rapport_div_pte_img);
					}
					else
					{ // image plus haute que large
						// calcul taille grande image
						if ($largeur_original >= $proportions_gde_img or $hauteur_original >= $proportions_gde_img)
						{
							$hauteur_gde_img = $proportions_gde_img;
							$rapport_div_gde_img = $hauteur_original / $hauteur_gde_img;
							$largeur_gde_img = round($largeur_original / $rapport_div_gde_img);
						}
						else
						{
							$largeur_gde_img = $largeur_original;
							$hauteur_gde_img = $hauteur_original;
						}
						// calcul taille miniature
						$hauteur_pte_img = $proportions_pte_img;
						$rapport_div_pte_img = $hauteur_original / $hauteur_pte_img;
						$largeur_pte_img = round($largeur_original / $rapport_div_pte_img);
					}
				}
				else
				{ // l'image téléchargée est plus petite que la miniature
					$largeur_gde_img = $largeur_original;
					$hauteur_gde_img = $hauteur_original;
					$largeur_pte_img = $largeur_original;
					$hauteur_pte_img = $hauteur_original;
				}
				// On entame le redimensionnement pour la grande image
				if (!function_exists('ImageCreateTrueColor'))
				{ // impossible de créer l'image, les photos doivent être déposées via le ftp
					@unlink($img_original); // on supprime la photo originale
					log_this('La bibliothèque GD n\'est pas chargée. Impossible de redimensionner les photos', 'galerie', true);
					header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=1&show='.$_GET['show']);
					exit;
				}
				$gde_img = @imagecreatetruecolor($largeur_gde_img, $hauteur_gde_img);
				$pte_img = @imagecreatetruecolor($largeur_pte_img, $hauteur_pte_img);
				$source = @imagecreatefromjpeg($img_original);
				if (!$source)
				{
					@unlink($img_original);
					log_this('Fichier JPEG non reconnu '.urldecode($newname).' dans le dossier '.$destination_provisoire, 'galerie', true);
					header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=3&show='.$_GET['show']);
					exit;
				}
				else
				{
					// génération de la grande photo proprement dite
					if (!@imagecopyresampled($gde_img, $source, 0, 0, 0, 0, $largeur_gde_img, $hauteur_gde_img, $largeur_original, $hauteur_original))
					{ // Fichier corrompu
						@unlink($img_original);
						log_this('Fichier JPEG non reconnu '.urldecode($newname).' dans le dossier '.$destination_provisoire, 'galerie', true);
						header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=3&show='.$_GET['show']);
						exit;
					}
					imagejpeg($gde_img, $dossier_gd.$newname); // on l'enregistre directement dans son dossier
					chmod($dossier_gd.$newname, 0666);
				
					// génération de la miniature proprement dite
					if (!imagecopyresampled($pte_img, $source, 0, 0, 0, 0, $largeur_pte_img, $hauteur_pte_img, $largeur_original, $hauteur_original))
					{ // Fichier corrompu
						@unlink($img_original);
						log_this('Fichier JPEG non reconnu '.urldecode($newname).' dans le dossier '.$destination_provisoire, 'galerie', true);
						header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=3&show='.$_GET['show']);
						exit;
					}
					imagejpeg($pte_img, $dossier_pt.$newname); 
					chmod($dossier_pt.$newname, 0666);
					
					// on vérifique que les deux fichiers sont bien créés
					if (!file_exists($dossier_gd.$newname) or !file_exists($dossier_pt.$newname))
					{ // ce n'est pas le cas
						@unlink($img_original);
						log_this('Enregistrement impossible de la photo '.urldecode($newname).' dans le dossier '.$dossier_pt.' et dans le dossier '.$dossier_gd, 'galerie', true);
						header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=2&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
						exit;
					}
					
					// suppression de la photo originale
					@unlink($img_original);
					$dossier = urlencode($dossier);
					$nomfichier = urlencode($_FILES['fichier']['name']);
					log_this('Nouvelle photo deposee sur le site dans le dossier '.urldecode($dossier), 'galerie');
					header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&ok='.$nomfichier.'&n='.$newname.'&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
				}
			}
			else
			{ // impossible d'enregistrer la photo, il faut les déposer par ftp
				log_this('Enregistrement impossible de la photo : '.urldecode($dossier), 'galerie');
				header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=2&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
			}
		}
		else if ($_POST['next'] == 2)
		{
			$dossier = urlencode($dossier);
			if ($_POST['suite'] == 'add')
			{
				header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=1&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
			}
			else
			{
				header('Location: index.php?page=gestion_galerie&a=create&step=1&dossier='.$dossier.'&show='.$_POST['show']);
			}
		}
		else
		{
			$dossier = urlencode($dossier);
			header('Location: index.php?page=upload_galerie&step=2&dossier='.$dossier.'&erreur=1&show='.$_POST['show'].'&suite='.$_POST['suite'].'&num='.$_POST['num']);
		}
	}
	else if ($_GET['step'] == 2)
	{
		if ($_GET['suite'] == 'add')
		{
?>
<h1>Ajout de photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la Page Gestion des albums photos</a></p>
<?php
		}
		else
		{
?>
<h1>Cr&eacute;er un album photos - 1-B T&eacute;l&eacute;chargement des photos </h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la Page Gestion des Albums photos</a></p>
<?php
		}
?>
<div class="instructions">
<?php
		$dossier = urldecode($_GET['dossier']);
		$suppr = array('.', '..', '///', '//');
		$dossier = str_replace($suppr, '', $dossier);	
		$racine = (empty($dossier)) ? 'img/' : '';
		$dossier = stripslashes($racine.$dossier);
		if ($_GET['suite'] == 'add')
		{
?>
<p>Ici, tu peux d&eacute;poser toutes les nouvelles photos de ton album sur le 
  portail.<br />
  S&eacute;lectionne une photo en cliquant sur le bouton &quot;<em>Parcourir</em>&quot;, 
  puis clique sur le bouton &quot;<em>Enregistrer la photo</em>&quot;. R&eacute;p&egrave;te 
  cette op&eacute;ration pour toutes les photos que tu veux ajouter. Quand tu 
  as termin&eacute;, clique sur le bouton &quot;<em>J'ai termin&eacute;</em>&quot; 
  pour passer &agrave; l'&eacute;tape suivante.</p>
<script type="text/javascript" language="JavaScript">
<!--
function launch()
{
	getElement("nextstep").value = "2";
	getElement("formsend").action = "index.php";
	getElement("formsend").submit();
}
//-->
</script>
<?php
		}
		else
		{
?>
<p>Tu peux maintenant d&eacute;poser toutes les photos de ton album sur le portail.<br />
  S&eacute;lectionne une photo en cliquant sur le bouton &quot;<em>Parcourir</em>&quot;, 
  puis clique sur le bouton &quot;<em>Enregistrer la photo</em>&quot;. R&eacute;p&egrave;te 
  cette op&eacute;ration pour toutes les photos que tu veux ajouter. Quand tu 
  as termin&eacute;, clique sur le bouton &quot;<em>J'ai termin&eacute;</em>&quot; 
  pour passer &agrave; l'&eacute;tape suivante. </p>
<script type="text/javascript" language="JavaScript">
<!--
function launch()
{
	getElement("nextstep").value = "2";
	getElement("formsend").submit();
}
//-->
</script>
<?php
		}
		$taille_maxi_photo = (is_numeric($site['upload_max_filesize'])) ? $site['upload_max_filesize'] : 1048576;
?>
<p><span class="rmq">Remarques :</span><br />
  <span class="petitbleu">- L'ordre dans lequel tu d&eacute;poses les photos sur 
  le portail est l'ordre dans lequel elles appara&icirc;tront dans l'album photo.<br />
  - Les photos sont automatiquement mises &agrave; la bonne taille pour le portail.<br />
  - A tout moment, tu peux interrompre le t&eacute;l&eacute;chargement et reprendre 
  l'ajout des photos plus tard.<br />
  - Poids maximum d'une photo : <?php echo taille_fichier($taille_maxi_photo); ?><br />
  - Format de fichier : JPG uniquement (en minuscules ou majuscules)</span></p>
</div>
<?php
		if (!@extension_loaded('gd'))
		{
?>
<div class="msg">
  <p class="rmq">Redimensionnement des photos impossible</p>
  <p>Tu ne pourras pas d&eacute;poser tes photos sur le site toi-m&ecirc;me. Donne un CD avec tes photos au webmaster.</p>
  <p class="petit">Note technique : la biblioth&egrave;que <acronym title="Cette bibliothèque contient les fonctions de redimensionnement des fichiers de type JPEG.">GD</acronym> n'est pas charg&eacute;e.</p>
</div>
<?php
		}
		if (function_exists('ini_get') and !@ini_get('file_uploads')) 
		{ 
?>
<div class="msg">
  <p class="rmq">L'upload de fichiers est d&eacute;sactiv&eacute; </p>
  <p>Tu ne pourras pas d&eacute;poser tes photos sur le site toi-m&ecirc;me. Donne un CD avec tes photos au webmaster.</p>
</div>
<?php
		} 
		else if (!function_exists('ini_get'))
		{
?>
<div class="msg">
  <p class="rmq">Upload de fichiers : &eacute;tat inconnu </p>
  <p>Le portail n'arrive pas &agrave; d&eacute;terminer si tu pourras d&eacute;poser toi-m&ecirc;me tes photos sur le portail.<br />
    Essaie ou pose la question au webmaster.
  </p>
</div>
<?php
		}
	
		if ($_GET['erreur'] == 1)
		{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite !</p>
</div>
<?php
		}
		else if ($_GET['erreur'] == 2)
		{
?>
<div class="msg">
<p align="center"><span class="rmq">Une erreur s'est produite ! </span></p>
<p align="center">Le portail ne peut pas &eacute;crire dans le dossier. </p>
<p align="center">Contacte le webmaster pour en savoir plus.</p>
</div>
<?php
		}
		else if ($_GET['erreur'] == 3)
		{
?>
<div class="msg">
      <p class="rmq" align="center">Redimensionnement impossible, le fichier JPG n'a pas le bon format. <br />
		Il semble contenir des erreurs. </p>
	  <p align="center">Envoie la photo au webmaster (en mentionnant le type d'erreur) ou choisis une autre photo.</p>
</div>
      <?php
		}
?>
<form action="upload_galerie.php" method="post" enctype="multipart/form-data" id="formsend" onsubmit="getElement('envoi').disabled = true; getElement('envoi').value = 'Un peu de patience...'" class="form_config_site">
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $taille_maxi_photo; ?>" />
  <input type="hidden" name="page" value="gestion_galerie" />
  <input type="hidden" name="mode_creation" value="local" />
  <input type="hidden" name="a" value="addimg" />
  <input type="hidden" name="next" value="1" id="nextstep" />
  <input type="hidden" name="suite" value="<?php echo $_GET['suite']; ?>" />
  <input type="hidden" name="num" value="<?php echo $_GET['num']; ?>" />
  <input type="hidden" name="step" value="3" />
  <input type="hidden" name="dossier" value="<?php echo urlencode($dossier); ?>" />
<?php
		if (!empty($_GET['ok']))
		{
?>
  <img src="<?php echo $dossier.'pt/'.$_GET['n']; ?>" align="right" alt="" title="Derni&egrave;re photo t&eacute;l&eacute;charg&eacute;e" style=" padding:5px; border:1px solid #CCC; background-color:#F3F3F3; margin:5px;" /> 
<?php
		}
?>
<p>Dossier en cours : <span class="rmqbleu"><?php echo $dossier; ?></span></p>
<?php
		if (!empty($_GET['ok']))
		{
?>
<p align="center"><span class="rmqbleu">Le fichier <?php echo $_GET['ok']; ?> a bien &eacute;t&eacute; 
  enregistr&eacute;.</span><br />
  Il a &eacute;t&eacute; renomm&eacute; en <strong><?php echo $_GET['n']; ?>.</strong></p>
<?php
		}
?>
<p align="center">S&eacute;lectionne une photo : 
<input name="fichier" type="file" id="fichier" />
Max <?php echo taille_fichier($taille_maxi_photo); ?></p>
<p align="center">
<input name="show" type="checkbox" id="show" value="oui"<?php echo ($_GET['show'] == 'oui') ? ' checked="checked"' : ''; ?> />
<label for="show">Montrer les photos d&eacute;j&agrave; enregistr&eacute;es</label></p>
<p align="center" style=" clear:right;"><input type="submit" name="Submit" value="Enregistrer la photo" id="envoi" /> 
<input type="button" name="bla" value="J'ai termin&eacute;" onclick="if (getElement('fichier').value == '') {launch();} else {alert('Enregistre d\'abord la photo que tu as sélectionnée.');}" /></p>
</form>
<?php
		if ($open = @opendir($dossier.'pt/'))
		{ // On parcourt le dossier pour calculer le nombre de photos qui s'y trouvent
		  // et éventuellement pour afficher les miniatures
			$i = 0;
			$poidstotal = 0;
			$surface = 0;
			while($image = readdir($open))
			{
				if($image != '.' and $image != '..' && eregi("\.jpg$", $image,$regs))
				{
					$i++;
					if ($_GET['show'] == 'oui')
					{
						$limage = $dossier.'pt/'.$image;
						$taille = @getimagesize($limage);
						$taille= $taille[3];
						$taille_zone = (is_numeric($site['galerie_proportions_mini'])) ? $site['galerie_proportions_mini'] + 20 : 170;
?>
<div class="liste_photos">
  <img src="<?php echo $limage; ?>" <?php echo $taille; ?> alt="" title="<?php echo $image; ?>" />
</div>
<?php
					}
				}
			}
?>
<div class="msg">
<p align="center"><b><?php
			$pl = ($i > 1) ? 's' : '';
			echo ($i > 0) ? $i.' image'.$pl.' dans ce dossier</b>.' : 'Aucune image trouv&eacute;e dans ce dossier';
?></p>
</div>
<?php
			closedir($open);
		} // fin lecture du dossier
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucune photo trouv&eacute;e dans ce dossier.</p>
<p align="center">Soit le dossier est vide, soit il est inaccessible.</p>
</div>
<?php
		}
	}
	else if ($_GET['step'] == 1)
	{
?>
<h1>Cr&eacute;er un album photos - 1-A Choix du dossier de l'album </h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la Page Gestion des Albums photos</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function addphoto(dossier)
{
	window.open('selectdossier.php?dossier='+dossier+'&x=show', 'choixphoto', 'width=550,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}
//-->
</script>
<div class="instructions"> 
  <p>Pour cr&eacute;er un album, s&eacute;lectionne un dossier dans lequel tu 
    vas placer les photos ou cr&eacute;e un nouveau dossier.</p>
  <p class="petitbleu"> Pour s&eacute;lectionner un dossier, <strong>entre dedans</strong> 
    et clique sur le bouton &quot;S&eacute;lectionner le dossier en cours&quot;.<br />
    Chaque album est contenu dans un dossier, et un dossier ne contient qu'un 
    album.<br />
    Essaie d'organiser la r&eacute;partition des dossiers : activites &gt; meute 
    &gt; 2004 &gt; camp</p>
</div>
<div class="form_config_site">
<?php
		$dossier = urldecode($_GET['dossier']);
		$suppr = array('.', '..', '///', '//');
		$dossier = str_replace($suppr, '', $dossier);	
		$racine = 'img';
		if (!ereg("^$racine", $dossier)) {$dossier = $racine;}
		$dossier = stripslashes($dossier);
		//@chmod($dossier, 0777);
	
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
			$chemin_lien .= ($i < $nb_pas - 1 and ereg("^$racine", $chemin_pas)) ? '<a href="index.php?page=upload_galerie&amp;step=1&amp;dossier='.urlencode($chemin_pas).'">' : '';
			$chemin_lien .= $chemin[$i];
			$chemin_lien .= ($i < $nb_pas - 1 and ereg("^$racine", $chemin_pas)) ? '</a>/' : '/';
			// on ajoute le / pour le sous-dossier suivant (sauf à la dernière itération
			$chemin_pas .= ($i < $nb_pas - 1) ? '/' : '';
		}
	
		// pour avoir un nom de dossier valide, on ajoute un / à la fin si nécessaire
		$dossier .= (!ereg("/$", $dossier)) ? '/' : '';
	
		if ($liste_dossier = @opendir($dossier))
		{ // On parcourt le $dossier en cours et on affiche les sous-dossiers qu'il contient
?>
<h2>S&eacute;lection du dossier</h2>
<p>Dossier en cours : <span class="rmqbleu"><?php echo $chemin_lien; ?></span></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form()
{
	return confirm("Es-tu certain de vouloir créer ton album dans ce dossier ?\nDossier en cours : <?php echo $dossier; ?> ?");
}
//-->
</script>
<form action="index.php" method="get" name="selectdossier" id="selectdossier" onsubmit="return check_form()">
 <input type="hidden" name="page" value="upload_galerie" />
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="dossier" value="<?php echo urlencode($dossier); ?>" />
<p align="center">
    <input type="submit" id="seldossier" value="Sélectionner le dossier en cours" />
    <input type="button" value="Voir le contenu du dossier" onclick="addphoto('<?php echo urlencode($dossier); ?>');" />
</p>
<p align="center" class="petit" id="seldossierinfo"></p>
</form>
<ul class="dir">
<?php
			$dossier_vide = true;
			$check_pt_gd = false;
			$nbre_elements = 0;
			while($sousdossier = @readdir($liste_dossier))
			{
				if ($sousdossier != '.' and $sousdossier != '..')
				{
					$nbre_elements++;
					$dossier_vide = false;
				}
				if (is_dir($dossier.$sousdossier) and $sousdossier != '.' and $sousdossier != '..')
				{
?>
	<li><a href="index.php?page=upload_galerie&amp;step=1&amp;dossier=<?php echo urlencode($dossier.$sousdossier); ?>"><?php echo $sousdossier; ?></a> 
	<span class="petitbleu">
<?php 
					if ($sousdossier == 'pt' or $sousdossier == 'gd')
					{
						$check_pt_gd = true;
						echo ($sousdossier == 'pt') ? ' - Miniatures ' : ' - Photos '; 
						echo ' - dossier cr&eacute;&eacute; automatiquement';
					}
					else if ($sousdossier == 'activites')
					{
						echo '(Photos d\'activit&eacute;s : Cr&eacute;e un sous-dossier ici pour tes albums)';
					}
					else if ($sousdossier == $_GET['newdir'])
					{
						echo '(Ouvre ce dossier pour pouvoir le s&eacute;lectionner)';
					}
?></span></li>
<?php
				}
			}
			@closedir($liste_dossier);
?>
</ul>
<?php
			// Un dossier vide contient 2 fichiers : ./ et ../
			if (!$dossier_vide and $nbre_elements != 2 and !$check_pt_gd)
			{
?>
<script type="text/javascript" language="JavaScript">
<!--
getElement("seldossier").disabled = true;
getElement("seldossier").value = "Dossier en cours non sélectionnable : pas vide";
getElement("seldossierinfo").innerHTML = "Tu peux créer un sous-dossier dans le dossier courant en utilisant le formulaire en bas de page.";
//-->
</script>
<?php
			}
			if ($nbre_elements == 2 and $check_pt_gd)
			{
?>
<script type="text/javascript" language="JavaScript">
<!--
getElement("seldossierinfo").innerHTML = "Ce dossier semble d&eacute;j&agrave; contenir un album mais tu peux y ajouter des photos.";
//-->
</script>
<?php
			}
		}
		else
		{
?>
<p align="center" class="rmq">Impossible de lire le dossier <?php echo htmlspecialchars(urldecode($_GET['dossier']), ENT_QUOTES); ?> !</p>
<?php
		}
?>
</div>
<?php
		if (!empty($_GET['mkdir']))
		{
?>
<div class="msg">
<?php
			if ($_GET['mkdir'] == 'ok')
			{ ?>
  <p align="center" class="rmqbleu">Le dossier &quot;<?php echo $_GET['newdir']; ?>&quot; a bien &eacute;t&eacute; cr&eacute;&eacute;.</p>
<?php
			}
			else if ($_GET['mkdir'] == 'erreur')
			{ ?>
  <p align="center" class="rmq">Le dossier n'a pas &eacute;t&eacute; cr&eacute;&eacute;. Caract&egrave;res interdits dans le nom de dossier.</p>
<?php
			}
			else if ($_GET['mkdir'] == 'ecrire')
			{ ?>
  <p align="center" class="rmq">Le dossier n'a pas &eacute;t&eacute; cr&eacute;&eacute;, le portail ne peut pas &eacute;crire dans ce dossier.</p>
<?php
			}
?>
</div>
<?php
		}
?>  
<form action="upload_galerie.php" method="post" name="makedir" id="makedir" class="form_config_site">
	
  <input type="hidden" name="dossier" value="<?php echo urlencode(ereg_replace("/$", '', $dossier)); ?>" />
	
  <input type="hidden" name="step" value="makedir" />	
  <p align="center"> Cr&eacute;er un sous-dossier : 
    <br />
    <input type="text" name="new_dossier" style="width:100px;" maxlength="30" />
    <input type="submit" value="Créer ce dossier" /><br />
	<span class="petitbleu">Caract&egrave;res autoris&eacute;s : a-z, 0-9, _ et - </span></p>
	</form>
<?php
	} // fin step = 1
	else if ($_POST['step'] == 'makedir')
	{ // création d'un dossier
		if (ereg("^[-_a-z0-9]{1,20}$", $_POST['new_dossier']))
		{
			$new_dossier = urldecode($_POST['dossier']).'/'.$_POST['new_dossier'].'/';
			if (!is_dir($new_dossier)) 
			{
				@mkdir($new_dossier);
				clearstatcache();
				if (!is_dir($new_dossier))
				{ // impossible de créer le dossier
					log_this('Ecriture impossible dans le dossier '.urldecode(htmlspecialchars($_POST['dossier'], ENT_QUOTES)).' (applique un chmod(777) dessus)', 'upload_galerie');
					header('Location: index.php?page=upload_galerie&step=1&dossier='.$_POST['dossier'].'&mkdir=ecrire');
					exit;
				}
			}
			else
			{ // on s'assure que le dossier est bien accessible en écriture
				if (!@chmod($new_dossier, 0777))
				{ // impossible de modifier les droits sur le dossier
					header('Location: index.php?page=upload_galerie&step=1&dossier='.$_POST['dossier'].'&mkdir=ecrire');
					exit;
				}
			}
			log_this('Création nouveau dossier : '.$new_dossier, 'galerie', true);
			header('Location: index.php?page=upload_galerie&step=1&dossier='.$_POST['dossier'].'&mkdir=ok&newdir='.$_POST['new_dossier']);
		}
		else
		{ // le format du nom de dossier est incorrect
			header('Location: index.php?page=upload_galerie&step=1&dossier='.$_POST['dossier'].'&mkdir=erreur');
		}
	}
} // fin numniveau > 2
else
{ // l'utilisateur n'a pas accès à cette page
	include('404.php');
}
if ($agdfgsfgf)
{
?>
</body>
</html>
<?php
}
?>