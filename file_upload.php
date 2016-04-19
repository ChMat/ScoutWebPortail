<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* file_upload.php v 1.1 - Gestion des fichiers téléchargés sur le portail 
* Fichiers liés à cet outil fichiers.php, fichiers_gestion.php, download.php
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
*	Prise en charge taille de fichier maxi définie par le webmaster
*	Prise en charge des rubriques de téléchargement
*	Mise en place des fichiers vedettes
* Modifications v 1.1.1
*	Prise en compte des erreurs de téléchargement de fichier
*/

include_once('connex.php');
include_once('fonc.php');

// paramètres du script
$dossierfichiers = 'fichiers/'; // dossier où sont stockés les fichiers uploadés
$taille_maxi_fichier = (is_numeric($site['upload_max_filesize'])) ? $site['upload_max_filesize'] : 1048576; // en octets

if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
}
else
{
	if (ereg("[a-z0-9]{20}", $_GET['f']) and isset($_GET['do'])) 
	{
		if ($_GET['do'] == 'delete')
		{ // suppression d'un fichier
			$sql = 'SELECT numfichier, nomserveur FROM '.PREFIXE_TABLES.'fichiers WHERE cledownload = \''.$_GET['f'].'\'';
			$del = send_sql($db, $sql);
			if (mysql_num_rows($del) == 1)
			{
				$ligne = mysql_fetch_assoc($del);
				if (file_exists($dossierfichiers.$ligne['nomserveur']))
				{
					unlink($dossierfichiers.$ligne['nomserveur']);
					$sql = 'DELETE FROM '.PREFIXE_TABLES.'fichiers WHERE cledownload = \''.$_GET['f'].'\'';
					send_sql($db, $sql);
					log_this('Suppression fichier sur page téléchargement', 'fichiers', true);
					header('Location: index.php?page=file_upload&do=ok&msg=delete');
					exit;
				}
			}
		}
	}
	if ($_POST['do'] == 'newfichier' and (is_numeric($_POST['cat_id']) or $_POST['cat_id'] == 'new'))
	{ // dépôt d'un fichier sur le serveur
		$destination = $dossierfichiers;
		$titre_fichier = htmlentities($_POST['titre_fichier'], ENT_QUOTES); 
		$description_fichier = htmlentities($_POST['description_fichier'], ENT_QUOTES); 
		if ($_FILES['userfile'] != 'none' && $_FILES['userfile']['size'] != 0 && !empty($titre_fichier))
		{
				// composition du nom de stockage et de la clé de téléchargement
				$cledownload = cleunique(); // clé de téléchargement
				$prefixefichier = date('Ymd');
				$suffixefichier = cleunique(4);
				/* afin d'empêcher l'upload de fichiers offensifs sur le serveur (.php notamment)
				// on remplace le nom et l'extension du fichier par une valeur connue du serveur uniquement */
				$newname = $prefixefichier.$suffixefichier; // nom du fichier sur le serveur
				$nomoriginal = $_FILES['userfile']['name'];
				$typefichier = $_FILES['userfile']['type'];
				if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $dossierfichiers.$newname))
				{ // enregistrement du fichier téléchargé
					if (filesize($dossierfichiers.$newname) > $taille_maxi_fichier)
					{ // Erreur : taille du fichier supérieure à la taille autorisée
						@unlink($dossierfichiers.$newname);
						log_this('Fichier déposé trop gros', 'file_upload');
						header('Location: index.php?page=file_upload&do=erreur&msg=poids');
					}
					else
					{ // enregistrement effectué
						if ($_POST['cat_id'] == 'new')
						{ // l'utilisateur crée une nouvelle rubrique
							$cat_titre = (!empty($_POST['new_cat_titre'])) ? htmlentities($_POST['new_cat_titre'], ENT_QUOTES) : 'Rubrique sans nom';
							$cat_description = (!empty($_POST['new_cat_description'])) ? htmlentities($_POST['new_cat_description'], ENT_QUOTES) : '';
							$sql = "INSERT INTO ".PREFIXE_TABLES."fichiers_cat (cat_titre, cat_description) values ('$cat_titre', '$cat_description')";
							send_sql($db, $sql);
							$sql = "SELECT cat_id FROM ".PREFIXE_TABLES."fichiers_cat WHERE cat_titre = '$cat_titre' LIMIT 1";
							$res = send_sql($db, $sql);
							$new_cat = mysql_fetch_assoc($res);
							$cat_id = $new_cat['cat_id'];
							// log de l'action
							log_this('Nouvelle rubrique (on upload) : '.$cat_titre, 'fichiers', true);
						}
						else
						{
							$cat_id = $_POST['cat_id'];
						}
						// $public est le niveau d'accès minimum requis pour télécharger le fichier
						$public = (is_numeric($_POST['public'])) ? $_POST['public'] : 5;
						$vedette = (is_numeric($_POST['vedette'])) ? $_POST['vedette'] : 0;
						$sql = "INSERT INTO ".PREFIXE_TABLES."fichiers 
						(cledownload, cat_id, dateupload, nomoriginal, nomserveur, type_fichier, titre_fichier, description_fichier, public, vedette, lu, file_auteur) 
						values 
						('$cledownload', '$cat_id', now(), '$nomoriginal', '$newname', '$typefichier', '$titre_fichier', '$description_fichier', '$public', '$vedette', '0', '$user[num]')";
						send_sql($db, $sql);
						// log de l'action
						log_this('nouveau fichier sur le site : '.$nomoriginal.' ('.$titre_fichier.')', 'fichiers', true);
						header('Location: index.php?page=file_upload&do=ok&msg=upload');
					}
				}
				else
				{ // Erreur : dossier d'enregistrement non chmodé pour écriture
					log_this('Ecriture impossible dans '.$dossierfichiers, 'file_upload');
					header('Location: index.php?page=file_upload&do=erreur&msg=droits');
				}
		}
		else
		{
		       	header('Location: index.php?page=file_upload&do=erreur&msg='.$_FILES['userfile']['error']);
		}
	} // fin if do==send
	if ($_GET['do'] == 'erreur' or $_GET['do'] == 'ok')
	{ // Affichage du retour : succès ou erreur.
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Déposer un fichier à télécharger sur le portail</title>
<link href="templates/default/style.css" type="text/css" rel="stylesheet" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
		}
?>
<br />
<h1>D&eacute;poser un fichier sur le portail</h1>
<div class="msg">
	<?php
		if ($_GET['msg'] == 'script')
		{
	?>
	  <p align="center" class="rmq">Une erreur de script s'est produite !</p>
	  <?php
		}
		else if ($_GET['msg'] == 'droits')
		{
	?>
      <p align="center"><span class="rmq">Impossible d'enregistrer le fichier dans le dossier 
        de stockage des fichiers t&eacute;l&eacute;charg&eacute;s.</span><br />
        Droits d'&eacute;criture absents sur le dossier <?php echo $dossierfichiers; ?></p>
	<?php
		}
		else if ($_GET['msg'] == 'format')
		{
	?>
      <p align="center" class="rmq">Le fichier n'a pas le bon format. Il doit &ecirc;tre au format GIF ou 
        JPG. </p>
	<?php
		}
		else if ($_GET['msg'] == 'poids' or $_GET['msg'] == '1' or $_GET['msg'] == '2')
		{
	?>
      <p align="center" class="rmq">Le fichier ne peut pas faire plus de <?php echo taille_fichier($taille_maxi_fichier); ?>.</p>
	<?php
		}
		else if ($_GET['msg'] == 'delete')
		{
	?>
      <p align="center" class="rmqbleu">Le fichier a &eacute;t&eacute; supprim&eacute; avec succ&egrave;s</p>
	<?php
		}
		else if ($_GET['msg'] == 'upload')
		{
	?>
      <p align="center" class="rmqbleu">Le fichier a bien &eacute;t&eacute; enregistr&eacute;</p>
	<?php
		}
		else
		{
	?>
      <p align="center" class="rmq">Une erreur s'est produite.</p>
	<?php
		}
	?>
      <p align="center"> <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour 
        &agrave; la page des fichiers </a><br />
      </p>
<?php
	} // fin if do == erreur
?>
</div>
<?php
} // fin if connecte
?>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>