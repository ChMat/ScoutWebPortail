<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* selectdossier_ftp.php - Sélection d'un dossier distant pour la création d'un album photo distant
* Pour fonctionner, le fichier doit recevoir les paramètres de connexion au serveur ftp distant
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
*	La synchronisation ftp/http est conservée de dossier en dossier
*	Les instructions sont masquées par défaut pour économiser de la place
*	Utilisation du 3e paramètre de getimagesize
*	Autorisation de connexion à un ftp anonyme (mot de passe vide)
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
	$dossier = stripslashes($dossier);
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>S&eacute;lection Dossier</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script language="JavaScript" type="text/JavaScript">
<!--
function ajoutimg(nomimg)
{
	opener.document.formulaire.photoaccueil.value = nomimg;
}

function ajoutgddossier(url_dossier, ftp_dossier)
{
	opener.document.formulaire.gdsource.value = url_dossier;
	opener.document.formulaire.ftp_gd_path.value = ftp_dossier;
}

function ajoutptdossier(url_dossier, ftp_dossier)
{
	opener.document.formulaire.ptsource.value = url_dossier;
	opener.document.formulaire.ftp_pt_path.value = ftp_dossier;
}

//-->
</script>
<script language="javascript" type="text/javascript" src="fonc.js"></script>
</head>
<body class="body_popup">
<h1>Param&eacute;trer un album &agrave; acc&egrave;s distant</h1>
<?php
	if (!function_exists('ftp_connect'))
	{ // le ftp n'est pas activé sur le serveur
?>
<div class="msg">
  <p align="center" class="rmq">Le module FTP n'est pas activ&eacute; sur le
    serveur. Param&eacute;trage automatique impossible !</p>
</div>
<?php   
		exit;
	}
	if (!empty($_GET['u']) and !empty($_GET['l']))
	{ // vérification des paramètres de connexion
?>
<div class="instructions">
  <p align="center">
    <input type="button" value="Afficher les instructions" onclick="if(getElement('txt_instr').style.display == 'none') {getElement('txt_instr').style.display = 'block'; this.value = 'Masquer les instructions';} else {getElement('txt_instr').style.display = 'none'; this.value = 'Afficher les instructions';}" />
  </p>
  <div id="txt_instr" style="display:none; ">
    <h2>Etape 1</h2>
    <p class="petit">Voyage dans les dossiers du serveur distant et <span class="rmqbleu">localise
        le dossier contenant les photos de l'album</span>.</p>
    <h2>Etape 2</h2>
    <p class="petit">Pour pouvoir afficher les photos contenues dans un dossier
      et pour param&eacute;trer ton album, <span class="rmqbleu">synchronise
      l'adresse sur le serveur FTP et l'URL du site distant</span>. Si tu as
      correctement synchronis&eacute; l'url du site, les images contenues dans
      le dossier seront affich&eacute;es ci-dessous (sinon, recommence la synchronisation).</p>
    <h2>Etape 3</h2>
    <p class="petit">Une fois la synchronisation effectu&eacute;e, tu pourras <span class="rmqbleu">s&eacute;lectionner
        les dossiers des miniatures et des grandes photos</span>.</p>
    <h2>Etape 4</h2>
    <p class="petit">Pour terminer, tu pourras <span class="rmqbleu">choisir
        l'image d'accueil de ton album</span> (la photo affich&eacute;e dans
        la liste des albums).</p>
  </div>
</div>
<?php
		$url_synchro .= (!empty($_GET['url_synchro']) and !ereg("/$", $_GET['url_synchro'])) ? '/' : '';
		$chemin = ereg_replace("/$", '', $dossier);
		$chemin = split('/', $chemin);
		$nb_pas = count($chemin);
		$chemin_lien = '<a href="selectdossier_ftp.php?u='.urlencode($_GET['u']).'&amp;l='.urlencode($_GET['l']).'&amp;p='.urlencode($_GET['p']).'">..</a>';
		//$chemin_lien .= ($nb_pas > 1) ? '/' : '';
		$chemin_pas = '';

		if (!empty($url_synchro))
		{ // On tente de faire correspondre l'url synchronisée et le chemin du ftp pour trouver la racine du site
		  // Ca permettra de conserver la synchronisation autant que possible
			
			// On commence par uniformiser la fin de la chaîne
			$dossier_abr = $dossier_abr_bis = ereg_replace("/$", '', $dossier);
			$url_synchro_abr = ereg_replace("/$", '', $url_synchro);
			$decalage = 0; // nombre de caractères en dehors de l'url depuis le début du chemin ftp
			while (!empty($url_synchro) and !empty($dossier_abr) and !ereg("$dossier_abr", $url_synchro_abr) and $decalage < 100)
			{ // on retire à chaque occurence le premier caractère du chemin ftp
			  // tant que le chemin ne correspond pas exactement à une partie de l'url synchro
				$dossier_abr = substr($dossier_abr, 1);
				$car++;		
			}
			// On a trouvé la correspondance, on met en place l'url racine du site distant
			// Si dossier_abr est vide, on est à la racine du site
			$base_url_synchro = (!empty($dossier_abr)) ? ereg_replace($dossier_abr, '', $url_synchro_abr) : $url_synchro_abr;
			// On garde pour mémoire le chemin ftp en dehors de l'url
			$racine_ftp_non_visible = substr($dossier_abr_bis, 0, $car);
		}

		for ($i = 0; $i < $nb_pas; $i++)
		{
			$chemin_pas .= $chemin[$i]; 
			if (!empty($_GET['url_synchro']))
			{
				if (!empty($chemin[$i]) and strpos($racine_ftp_non_visible, $chemin[$i]) === false)
				{
					$base_url_synchro .= '/'.$chemin[$i];
				}
				$chemin_url_synchro =  '&url_synchro='.urlencode($base_url_synchro);
			}
			else
			{
				$chemin_url_synchro = '';
			}
			$chemin_lien .= ($i < $nb_pas - 1) ? '<a href="selectdossier_ftp.php?dossier='.urlencode($chemin_pas).'&amp;u='.urlencode($_GET['u']).'&amp;l='.urlencode($_GET['l']).'&amp;p='.urlencode($_GET['p']).$chemin_url_synchro.'">' : '';
			$chemin_lien .= $chemin[$i];
			$chemin_lien .= ($i < $nb_pas - 1) ? '</a>/' : '/';
			$chemin_lien = ereg_replace("//$", '/', $chemin_lien);
			$chemin_pas .= ($i < $nb_pas - 1) ? '/' : '';
		}

		$dossier .= '/';


		$ftp_host = $_GET['u'];
		$ftp_user = $_GET['l'];
		$ftp_password = $_GET['p'];

		// On ouvre la connexion au serveur ftp
		$conn = @ftp_connect($ftp_host);
		$login = @ftp_login($conn, $ftp_user, $ftp_password);
	
		// On passe en mode passif (uniquement après ftp_login)
		$mode = @ftp_pasv($conn, TRUE);
	
		if ((!$conn) || (!$login) || (!$mode)) 
		{ // Les paramètres ne sont pas corrects
?>
<div class="msg">
  <p align="center" class="rmq">La connexion FTP a &eacute;chou&eacute; !</p>
  <p align="center" class="rmq">Soit les param&egrave;tres sont incorrects, soit
    tu n'as pas acc&egrave;s au ftp depuis ce site.</p>
</div>
<?php   
			exit;
		}
		// la connexion est ouverte
		if (!empty($dossier))
		{ // on change de dossier si nécessaire
			if (!$autre_dossier = @ftp_chdir($conn, $dossier))
			{
?>
<div class="msg">
  <p align="center" class="rmq">Le dossier recherch&eacute; n'existe pas !</p>
</div>
<?php   
				exit;
			}
		}

		// On parcourt le contenu du dossier en cours
		$liste_dossier = @ftp_nlist($conn, '');
	
		echo '<p>Dossier en cours sur <strong>'.$_GET['l'].'@'.$_GET['u'].'</strong> : <span class="rmqbleu">'.$chemin_lien.'</span></p>';
		if (is_array($liste_dossier))
		{
?>
<ul class="dir">
  <?php
			foreach($liste_dossier as $sousdossier)
			{
				if (ftp_size($conn, $sousdossier) == -1 and $sousdossier != '.' and $sousdossier != '..')
				{
	?>
  <li><a href="selectdossier_ftp.php?dossier=<?php echo urlencode($dossier.$sousdossier); ?><?php echo (!empty($_GET['url_synchro'])) ? '&url_synchro='.urlencode($url_synchro.$sousdossier) : ''; ?><?php echo '&amp;u='.urlencode($_GET['u']).'&amp;l='.urlencode($_GET['l']).'&amp;p='.urlencode($_GET['p']); ?>"><?php echo $sousdossier; ?></a>
    <?php
					if (!empty($_GET['url_synchro']))
					{
?>
    <input type="button" name="Button" value="Photos" onclick="ajoutgddossier('<?php echo $url_synchro.$sousdossier; ?>/', '<?php echo $dossier.$sousdossier; ?>/')" title="Sélectionner ce dossier comme dossier source pour les photos" />
    <input type="button" name="Button" value="Miniatures" onclick="ajoutptdossier('<?php echo $url_synchro.$sousdossier; ?>/', '<?php echo $dossier.$sousdossier; ?>/')" title="Sélectionner ce dossier comme dossier source pour les miniatures" />
    <?php
					}
?>
  </li>
  <?php
				}
			}
?>
</ul>
<?php
		}
		$i = 0;
		$poids_total = 0;
		$surface = 0;
		if (is_array($liste_dossier))
		{
			foreach($liste_dossier as $fichier)
			{
				if($fichier != '.' and $fichier != '..' and eregi("\.jpg$|\.bmp$|\.gif|\.png", $fichier))
				{
					$i++;
					if (!empty($_GET['url_synchro']))
					{
						$image = $url_synchro.$fichier;
						$taille = @getimagesize($image);
						$poids = @filesize($image);
						if ($poids) 
						{
							$poids_total += $poids;
							$poids = taille_fichier($poids);
						}
						if ($taille)
						{
							$l = $taille[0];
							$h = $taille[1];
							$taille = $taille[3];
						}
?>
<div class="liste_photo">
  <h2><?php echo $fichier; ?><span class="petitbleu"><?php echo ($taille) ? $l.' x '.$h : ''; ?></span></h2>
  <p align="center">
    <?php 
	echo '<img src="'.$image.'" '.$taille.' style="cursor:pointer" alt="S&eacute;lectionner cette image" title="S&eacute;lectionner cette image" onclick="ajoutimg(\''.$fichier.'\');" />';
?>
  </p>
</div>
<?php
					} // if url_synchro
				}
			} // fin foreach
		} // fin if is_array
		echo '<p align="center"><strong>';
		$pl = ($i > 1) ? 's' : '';
		echo ($i > 0) ? $i.' image'.$pl.' trouv&eacute;e'.$pl : 'Aucune image trouv&eacute;e';
		echo '</strong></p>';
?>
<form action="selectdossier_ftp.php" method="get" class="action">
  <?php
		if (empty($_GET['url_synchro']))
		{
?>
  <p align="center" class="rmqbleu">L'URL du site distant n'est pas encore synchronis&eacute;e</p>
  <?php
		}
?>
  <input type="hidden" name="u" value="<?php echo $_GET['u']; ?>" />
  <input type="hidden" name="l" value="<?php echo $_GET['l']; ?>" />
  <input type="hidden" name="p" value="<?php echo $_GET['p']; ?>" />
  <input type="hidden" name="dossier" value="<?php echo $_GET['dossier']; ?>" />
  <p align="center">URL du dossier en cours :
    <input type="text" name="url_synchro" value="<?php echo $_GET['url_synchro']; ?>" style="width:250px;" />
  </p>
  <p align="center">
    <input type="submit" value="Synchroniser" />
  </p>
</form>
<?php
		//close
		if (function_exists('ftp_close')) {@ftp_close($conn);} else if (function_exists('ftp_quit')) {ftp_quit($conn);}
	} // fin ulp != ""
	else
	{
?>
<div class="msg">
  <p align="center" class="rmq">Les param&egrave;tres de connexion au serveur
    FTP n'ont pas &eacute;t&eacute; d&eacute;finis !</p>
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
