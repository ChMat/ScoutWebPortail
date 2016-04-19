<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* upload_photomembre.php v 1.1 - Enregistrement et redimensionnement de la photo d'un membre de l'unité
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
*	Lien différent vers formulaire modif fiche membre si le membre est un ancien.
*/

include_once('connex.php');
include_once('fonc.php');

if ($user['niveau']['numniveau'] > 2)
{
	$dossier_photos = 'img/photosmembres/uploaded/'; // dossier de destination des photos de membres
	$destination_provisoire = 'img/photosmembres/'; // le fichier uploadé y est copié pour le redimensionnement puis supprimé
	$taille_maxi_photo = (is_numeric($site['upload_max_filesize'])) ? $site['upload_max_filesize'] : 1048576;
	if ($_POST['do'] == 'send' and is_numeric($_POST['nummb']))
	{
		// Si nécessaire on crée le répertoire des photos membres
		if (!is_dir($destination_provisoire)) {mkdir($destination_provisoire);}
		if ($_FILES['fichier'] != 'none' and $_FILES['fichier']['size'] != 0 and eregi("jpg$", $_FILES['fichier']['name']))
		{
				$newname = 'mb_'.$_POST['nummb'].'_'.cleunique(2).'_'.$user['num'].'.jpg';
				if (@move_uploaded_file($_FILES['fichier']['tmp_name'], $destination_provisoire.$newname))
				{
					$photo_originale = $destination_provisoire.$newname; // adresse relative du fichier à redimensionner
					// calcul de la taille de redimensionnement
					$proportions_largeur_photo = (is_numeric($site['photo_membre_max_width'])) ? $site['photo_membre_max_width'] : 100;
					$proportions_hauteur_photo = (is_numeric($site['photo_membre_max_height'])) ? $site['photo_membre_max_height'] : 130;
					if (!is_dir($dossier_photos)) {mkdir($dossier_photos) or die('Impossible de créer le dossier');}
					$taille_original  = @getimagesize($photo_originale);
					$largeur_original = $taille_original[0];
					$hauteur_original = $taille_original[1];
					if ($largeur_original > $proportions_largeur_photo or $hauteur_original > $proportions_hauteur_photo)
					{ // l'image fournie est trop grande en taille (pixels) pour satisfaire aux proportions exigées
						if ($largeur_original >= $hauteur_original)
						{ // plus large que haute
							// calcul taille photo redimensionnée
							$largeur_photo = $proportions_largeur_photo;
							$rapport_div_photo = $largeur_original / $largeur_photo;
							$hauteur_photo = round($hauteur_original / $rapport_div_photo);
						}
						else
						{ // plus haut que large
							// calcul taille photo redimensionnée
							$hauteur_photo = $proportions_hauteur_photo;
							$rapport_div_photo = $hauteur_original / $hauteur_photo;
							$largeur_photo = round($largeur_original / $rapport_div_photo);
						}
						// redimensionnement de la photo et enregistrement dans le dossier adéquat
						if (!$photo_redimensionnee = @imagecreatetruecolor($largeur_photo, $hauteur_photo))
						{
							header('Location: index.php?page=upload_photomembre&do=erreur&msg=droits');
						}
						$source = imagecreatefromjpeg($photo_originale);
						
						if (!@imagecopyresampled($photo_redimensionnee, $source, 0, 0, 0, 0, $largeur_photo, $hauteur_photo, $largeur_original, $hauteur_original))
						{ // Le fichier n'est pas lisible par les fonctions gd
							@unlink($photo_originale);
							log_this('Fichier JPEG non reconnu '.$photo_originale, 'upload_photomembre');
							header('Location: index.php?page=upload_photomembre&do=erreur&msg=badformat&nummb='.$_POST['nummb']);
							exit;
						}
						$photo_fichier = $photo_originale;
						imagejpeg($photo_redimensionnee, $photo_fichier); 
						if (!@copy($photo_fichier, $dossier_photos.$newname))
						{
							log_this('Ecriture impossible dans le dossier '.$dossierphotos.' (appliquez un chmod(777) dessus)', 'upload_photomembre');
							header('Location: index.php?page=upload_photomembre&do=erreur&msg=droits');
							exit;
						}
						@unlink($photo_fichier);
					}
					else
					{ // l'image rentre dans les proportions demandées, on la copie directement dans le dossier
						if (!@copy($photo_originale, $dossier_photos.$newname))
						{
							log_this('Ecriture impossible dans le dossier '.$dossierphotos.' (appliquez un chmod(777) dessus)', 'upload_photomembre');
							header('Location: index.php?page=upload_photomembre&do=erreur&msg=droits');
							exit;
						}
						@unlink($photo_originale);
					}
					$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET photo = '$dossier_photos$newname' WHERE nummb = '$_POST[nummb]'";
					send_sql($db, $sql);
					// envoi du mail
					log_this('Nouvelle photo membre sur le site ('.$_POST['nummb'].')', 'upload_photomembre');
					if ($_POST['r'] == 'a') {$r = 'ficheancien';} else {$r = 'fichemb';}
					header('Location: index.php?page='.$r.'&nummb='.$_POST['nummb']);
				}
				else
				{
					log_this('Ecriture impossible dans le dossier '.$dossierphotos.' (applique un chmod(777) dessus)', 'upload_photomembre');
					header('Location: index.php?page=upload_photomembre&do=erreur&msg=droits');
				}
		}
		else
		{
		       	header('Location: index.php?page=upload_photomembre&do=erreur&msg=format&nummb='.$_POST['nummb']);
		}
	} // fin if do==send
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Déposer des photos de membres sur le portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" type="text/css" />
</head>

<body>
<?php
	} // fin defined in_site
	if (is_numeric($_GET['nummb']) and empty($_GET['do']))
	{
		$restreindre = ($user['niveau']['numniveau'] == 3) ? "AND section = '$user[numsection]'" : "";
		$sql = "SELECT nom_mb, prenom, section FROM ".PREFIXE_TABLES."mb_membres WHERE nummb = '$_GET[nummb]' $restreindre";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$ligne = mysql_fetch_assoc($res);
			$taille_maxi_photo = (is_numeric($site['upload_max_filesize'])) ? $site['upload_max_filesize'] : 1048576;
?>
<h1>Déposer la photo d'un membre sur le portail</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la Page Gestion de l'Unit&eacute;</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.fichier.value != "" && form.nummb.value != "")
	{
		form.envoi.disabled = true;
		form.envoi.value = "Patience...";
		return true;
	}
	else
	{
		alert("Impossible d'envoyer le formulaire !");
		return false;
	}
}
//-->
</script>
<form action="upload_photomembre.php" method="post" enctype="multipart/form-data" name="form" class="form_gestion_unite" id="form" onsubmit="return check_form(this)">
  <h2>
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $taille_maxi_photo; ?>" />
	<input type="hidden" name="r" value="<?php echo $_GET['r']; ?>" />
    <input type="hidden" name="do" value="send" />
    <input type="hidden" name="nummb" value="<?php echo $_GET['nummb']; ?>" />
  S&eacute;lection de la photo
  </h2>
  <p>Depuis cette page, tu peux attacher la photo d'un membre &agrave; 
    sa fiche en l'envoyant sur le portail depuis ton ordinateur.</p>
  <p align="center">S&eacute;lectionne une photo pour <span class="rmqbleu"><?php echo $ligne['prenom'].' '.$ligne['nom_mb']; ?></span> 
    : <input name="fichier" type="file" id="fichier" tabindex="1" />
  (Max <?php echo taille_fichier($taille_maxi_photo); ?>) </p>
  <p align="center"><input type="submit" name="envoi" value="Envoyer" tabindex="2" /></p>
</form>
<div class="instructions">
<h2>Remarque</h2>
	<p>La photo que tu envoies doit &ecirc;tre au format JPG.<br />
	Elle sera redimensionn&eacute;e pour entrer dans un rectangle de <span class="rmqbleu">100 
	x 130 pixels</span> (largeur x hauteur). Nous te conseillons donc de choisir 
	une photo en gros plan.<br />
	Afin d'acc&eacute;l&eacute;rer le traitement de l'image, tu peux d&eacute;j&agrave; 
	redimensionner toi-m&ecirc;me la photo aux bonnes dimensions.</p>
<p>Si la photo du membre est <strong>d&eacute;j&agrave; pr&eacute;sente sur le 
  portail</strong>, <a href="index.php?page=<?php echo (is_section_anciens($ligne['section'])) ? 'modifancien' : 'modifmembre'; ?>&amp;nummb=<?php echo $_GET['nummb']; ?>">tu 
  peux la choisir ici</a>.</p>
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
		} // fin num_rows == 1
		else
		{
?>
<h1>Déposer la photo d'un membre sur le portail</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la Page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
  <p align="center" class="rmq">Aucun membre ne correspond &agrave; cette requ&ecirc;te !</p>
</div>
<?php
		} // fin else num_rows == 1
	} // fin is numeric nummb
	else if ($_GET['do'] == 'erreur')
	{
?>
<h1>Déposer la photo d'un membre sur le portail</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la Page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<?php
		if ($_GET['msg'] == 'script')
		{
?>
	  <p class="rmq" align="center">Une erreur de script s'est produite !</p>
<?php
		}
		else if ($_GET['msg'] == 'droits')
		{
?>
<p align="center"><span class="rmq">Impossible d'enregistrer la photo.</span><br />
  Droits d'&eacute;criture absents sur le dossier <?php echo $destination_provisoire; ?></p>
<?php
		}
		else if ($_GET['msg'] == 'badformat')
		{
?>
      <p class="rmq" align="center">Redimensionnement impossible, le fichier JPG n'a pas le bon format. <br />
		Il semble contenir des erreurs. </p>
	  <p align="center">Envoie la photo au webmaster (en mentionnant le type d'erreur) ou choisis une autre photo.</p>
      <p align="center"><a href="index.php?page=upload_photomembre&nummb=<?php echo $_GET['nummb']; ?>">R&eacute;essayer</a></p>
      <?php
		}
		else if ($_GET['msg'] == 'format')
		{
?>
      <p class="rmq" align="center">Le fichier n'a pas le bon format. Il doit &ecirc;tre au format JPG. </p>
      <p align="center"><a href="index.php?page=upload_photomembre&nummb=<?php echo $_GET['nummb']; ?>">R&eacute;essayer</a></p>
      <?php
		}
		else if ($_GET['msg'] == 'poids')
		{
?>
      <p class="rmq" align="center">Le fichier ne peut pas faire plus de 1 Mo (1024 Ko).</p>
<?php
		}
		else
		{
?>
      <p class="rmq" align="center">Une erreur s'est produite.</p>
<?php
		}
?>
</div>
<?php
	} // fin if do == erreur
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	} // fin defined in_site
} // fin niveau > 2
else
{
	include('404.php');
}
?>