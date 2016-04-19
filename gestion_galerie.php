<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_galerie.php v 1.1 - Gestion des albums photos
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
*	Affichage message si ftp désactivé
*	Autorisation de connexion à un ftp anonyme (mot de passe vide)
*	On permet au webmaster de modifier le mode de création de l'album
*/


include_once('connex.php');
include_once('fonc.php');

function destroy_structure($numalbum)
{ // cette fonction sert à détruire la structure d'un album en cours de création
	global $db;
	$sql = "DELETE FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '$numalbum'";
	send_sql($db, $sql);
}

if (($user['niveau']['numniveau'] < 3 and $user['assistantwebmaster'] != 1) or ($site['galerie_actif'] != 1 and $user['niveau']['numniveau'] < 5)) 
{ // l'utilisateur ne peut pas gérer la galerie photos
	if ($site['galerie_actif'] != 1)
	{ // la galerie photos n'est pas activée
		include('module_desactive.php');
	}
	else
	{ // l'utilisateur n'est pas animateur ou cowebmaster
		include('404.php');
	}
	exit;
}
if ($_POST['a'] == 'docreate')
{ // création d'un album proprement dite
	if (empty($_POST['ptsource']) or empty($_POST['gdsource']) or empty($_POST['albtitre']) or empty($_POST['photoaccueil']))
	{ // les infos essentielles ne sont pas fournies
		header('Location: index.php?page=gestion_galerie&a=msg&x=nodata');
		exit;
	}
	if (!empty($_POST['dateactivite']))
	{ // la date est définie
		if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $_POST['dateactivite'], $regs))
		{
			$dateactivite = $regs[3].'-'.$regs[2].'-'.$regs[1];
		}
		else
		{
			$dateactivite = date('Y-m-d');
		}
	}
	else
	{
		$dateactivite = date('Y-m-d');
	}
	$description = htmlentities($_POST['description'], ENT_QUOTES);
	$description2 = htmlentities($_POST['description2'], ENT_QUOTES);
	$auteurphotos = htmlentities($_POST['auteurphotos'], ENT_QUOTES);
	$albtitre = htmlentities($_POST['albtitre'], ENT_QUOTES);
	$gdsource = (!ereg("/$", $_POST['gdsource'])) ? $_POST['gdsource'].'/' : $_POST['gdsource'];
	$ptsource = (!ereg("/$", $_POST['ptsource'])) ? $_POST['ptsource'].'/' : $_POST['ptsource'];
	// on vérifie comment l'album va être alimenté (à distance ou sur le site)
	$mode_creation = (ereg("^(local|distant)$", $_POST['mode_creation'])) ? $_POST['mode_creation'] : '';
	// on crée la trame de base de l'album
	$sql = "INSERT INTO ".PREFIXE_TABLES."galerie 
	(titre, dossiergd, dossierpt, description, description2, photoaccueil, galerie_section, datecreation, dateactivite, auteurphotos, mode_creation) 
	values 
	('$albtitre', '$gdsource', '$ptsource', '$description', '$description2', '$_POST[photoaccueil]', '$_POST[galerie_section]', now(), '$dateactivite', '$auteurphotos', '$mode_creation')";
	send_sql($db, $sql);
	// on récupère son numéro
	$sql = "SELECT numgalerie FROM ".PREFIXE_TABLES."galerie WHERE titre = '$albtitre'";
	if ($res = send_sql($db, $sql))
	{
		$resultat = mysql_fetch_assoc($res);
		$numeroalbum = $resultat['numgalerie'];
		$nbre_photos_inserees = 0;
		if ($_POST['mode_creation'] == 'local')
		{ // les photos sont sur le serveur
			if ($open = @opendir($gdsource))
			{
				$i=0;
				while($image = readdir($open))
				{ // on parcourt les fichiers dans le dossier des photos
					// C'est ici que devra venir la reconnaissance des vidéos et autres joyeusetés
					if($image != '.' and $image != '..' and eregi("jpg$", $image))
					{
						$i++;
						$fichier = str_replace($gdsource, '', $image);
						$sql = "INSERT INTO ".PREFIXE_TABLES."albums (nomfichier, numalbum) values ('$fichier', '$numeroalbum')";
						send_sql($db, $sql);
					}
				}
				closedir($open);
				$nbre_photos_inserees = $i;
			} // fin if opendir
			else
			{ // lecture du dossier source impossible, on supprime l'album en cours de création
				destroy_structure($numeroalbum);
				header('Location: index.php?page=gestion_galerie&a=msg&x=erreurdossier');
				exit;
			}
		}
		else if ($_POST['mode_creation'] == 'distant')
		{ // les photos sont sur un autre serveur
			if ($_POST['acces_ftp'] == 'oui')
			{ // on récupère les noms des fichiers depuis le serveur FTP
			  // le processus est automatisé
				if (!function_exists('ftp_connect'))
				{
					destroy_structure($numeroalbum); // on détruit la structure primaire de l'album
					header('Location: index.php?page=gestion_galerie&a=msg&x=noftp');
					exit;
				}
				if (empty($_POST['ftp_gd_path']) or empty($_POST['ftp_pt_path']))
				{
					destroy_structure($numeroalbum); // on détruit la structure primaire de l'album
					header('Location: index.php?page=gestion_galerie&a=msg&x=nodata');
					exit;
				}

				// on ajoute le cas échéant les / à la fin des chemins
				$ftp_gd_path = (!ereg("/$", $_POST['ftp_gd_path'])) ? $_POST['ftp_gd_path'].'/' : $_POST['ftp_gd_path'];
				$ftp_pt_path = (!ereg("/$", $_POST['ftp_pt_path'])) ? $_POST['ftp_pt_path'].'/' : $_POST['ftp_pt_path'];

				// on récupère les paramètres de connexion au serveur ftp
				$ftp_host = $_POST['ftp_url'];
				$ftp_user = $_POST['ftp_login'];
				$ftp_password = $_POST['ftp_pw'];

				// Connexion au serveur
				$conn = @ftp_connect($ftp_host);
				$login = @ftp_login($conn, $ftp_user, $ftp_password);
			
				// On passe en mode passif
				$mode = @ftp_pasv($conn, TRUE);
			
				// Si le login échoue on renvoie un message d'erreur
				if ((!$conn) || (!$login) || (!$mode)) 
				{ // échec de la connexion au serveur ftp
					destroy_structure($numeroalbum); // on détruit la structure primaire de l'album
					header('Location: index.php?page=gestion_galerie&a=msg&x=noftp');
					exit;
				}
				if (!empty($ftp_pt_path))
				{ // on change de dossier si nécessaire
					if (!$autre_dossier = @ftp_chdir($conn, $ftp_pt_path))
					{
						destroy_structure($numeroalbum);
						header('Location: index.php?page=gestion_galerie&a=msg&x=nodossier');
						exit;
					}
				}
				// On liste le contenu du dossier et on l'insère dans la db
				$file_list = @ftp_nlist($conn, '');
				$i = 0; // indice des photos insérées dans l'album
				if (is_array($file_list))
				{ // si le dossier contient des fichiers
					foreach($file_list as $image)
					{
						if($image != '.' and $image != '..' and eregi("jpg$", $image))
						{
							$i++;
							$fichier = $image;
							$sql = "INSERT INTO ".PREFIXE_TABLES."albums (nomfichier, numalbum) values ('$fichier', '$numeroalbum')";
							send_sql($db, $sql);
							// l'album a été créé avec succès, la fin de la création continue plus bas
						}
					} // fin foreach
				}
				$nbre_photos_inserees = $i;
				// fermeture de la connexion au serveur FTP
				if (function_exists('ftp_close')) {@ftp_close($conn);} else if (function_exists('ftp_quit')) {ftp_quit($conn);}
			} // fin acces_ftp = oui
			else
			{ // l'utilisateur a rempli manuellement la liste des fichiers en accès distant
				if (is_numeric($_POST['nbre_photos']) and $_POST['nbre_photos'] > 0)
				{
					for ($i = 1; $i <= $_POST['nbre_photos']; $i++)
					{
						if (!empty($_POST['photo-'.$i]))
						{
							$fichier = $_POST['photo-'.$i];
							$sql = "INSERT INTO ".PREFIXE_TABLES."albums (nomfichier, numalbum) values ('$fichier', '$numeroalbum')";
							send_sql($db, $sql);
							// l'album a été créé avec succès, la fin de la création continue plus bas
						}
						$nbre_photos_inserees = $_POST['nbre_photos'];
					}
				}
				else
				{
					destroy_structure($numeroalbum);
					header('Location: index.php?page=gestion_galerie&a=msg&x=nodata');
					exit;
				}
			}
		} // fin mode_creation = distant
		else
		{
			header('Location: index.php?page=gestion_galerie&a=msg&x=erreur');
			exit;
		}
		if ($nbre_photos_inserees > 0)
		{
			//////////////////////////////////
			// Fin de la création de l'album, on trie les photos par ordre croissant et on met l'album à jour
			//////////////////////////////////
			$sql = "UPDATE ".PREFIXE_TABLES."galerie SET nbrephotos = '$nbre_photos_inserees' WHERE numgalerie = '$numeroalbum'";
			send_sql($db, $sql);
			$sql = "SELECT * FROM ".PREFIXE_TABLES."albums WHERE numalbum = '$numeroalbum' ORDER BY nomfichier";
			if ($res = send_sql($db, $sql))
			{
				$j = 0;
				while ($ligne = mysql_fetch_assoc($res))
				{
					$j++;
					$sql = "UPDATE ".PREFIXE_TABLES."albums SET posphoto = '$j' WHERE numphoto = '$ligne[numphoto]'";
					send_sql($db, $sql);
				}
			}
			// log de la création de l'album
			log_this('Création album photo '.$albtitre, 'galerie');
			// on envoie vers le message de confirmation de la création
			header('Location: index.php?page=gestion_galerie&a=msg&x=okcreation&nbre='.$j.'&num='.$numeroalbum);
			exit;
		}
		else
		{ // l'album ne contient aucune photo, on détruit sa structure
			destroy_structure($numeroalbum);
			header('Location: index.php?page=gestion_galerie&a=msg&x=noimage');
			exit;
		}
	}
	else
	{
		header('Location: index.php?page=gestion_galerie&a=msg&x=erreur');
		exit;
	}
}
else if ($_POST['a'] == 'domodifalbum')
{
	if (!empty($_POST['dateactivite']))
	{
		if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $_POST['dateactivite'], $regs))
		{
			$dateactivite = "$regs[3]-$regs[2]-$regs[1]";
		}
		else
		{
			$dateactivite = date(Y-m-d);
		}
	}
	else
	{
		$dateactivite = date(Y-m-d);
	}
	$description = htmlentities($_POST['description'], ENT_QUOTES);
	$description2 = htmlentities($_POST['description2'], ENT_QUOTES);
	$auteurphotos = htmlentities($_POST['auteurphotos'], ENT_QUOTES);
	$albtitre = htmlentities($_POST['albtitre'], ENT_QUOTES);
	// Seul le webmaster peut modifier le mode de création d'un album
	$mode_creation = ($user['niveau']['numniveau'] == 5 and ($_POST['mode_creation'] == 'local' or $_POST['mode_creation'] == 'distant')) ? ", mode_creation = '".$_POST['mode_creation']."'" : '';
	
	$sql = "UPDATE ".PREFIXE_TABLES."galerie 
	SET 
	titre = '$albtitre', dossiergd = '$_POST[gdsource]', 
	dossierpt = '$_POST[ptsource]', description = '$description', description2 = '$description2', 
	photoaccueil = '$_POST[photoaccueil]', galerie_section = '$_POST[galerie_section]', dateactivite = '$dateactivite', 
	auteurphotos = '$auteurphotos'$mode_creation
	WHERE numgalerie = '$_POST[num]'";
	send_sql($db, $sql);
	// envoi du mail
	log_this('Modification album photo '.$_POST['num'].' : '.$albtitre, 'galerie');
	header('Location: index.php?page=gestion_galerie&a=msg&x=okmodif');
}
else if ($_POST['a'] == 'doaddimg' and empty($_GET['a']) and is_numeric($_POST['num']))
{ // Ajout des photos uploadées dans l'album
  // L'upload est géré dans upload_galerie.php
	$sql = "SELECT dossiergd, nomfichier, posphoto, titre FROM ".PREFIXE_TABLES."galerie, ".PREFIXE_TABLES."albums WHERE numgalerie = '$_POST[num]' AND numalbum = '$_POST[num]' ORDER BY posphoto DESC";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
			$nbre = 0;
			$lesnoms = '';
			$dernier = 'x';
			for ($i = 1; $i <= mysql_num_rows($res); $i++)
			{
				$donnees = mysql_fetch_assoc($res);
				if ($nbre == 0) {$dernier = $donnees['posphoto']; $titre = $donnees['titre']; $gdsource = $donnees['dossiergd'];}
				$lesnoms .= $donnees['nomfichier'].'# #';
				$nbre++;
			}
			$open=OpenDir($gdsource);
			$erreur = 0;
			$nbrenew = 0;
			while($image=ReadDir($open))
			{
				if($image != '.' and $image != '..' and eregi("jpg$", $image))
				{
					$fichier = str_replace($gdsource, '', $image);
					if (!eregi($fichier, $lesnoms))
					{
						$nbrenew++;
						$newnbrephotos = $nbre + $nbrenew;
						$sql = "INSERT INTO ".PREFIXE_TABLES."albums (nomfichier, numalbum, posphoto) values ('$fichier', '$_POST[num]', '$newnbrephotos')";
						send_sql($db, $sql);
					}
				}
			}
			closedir($open);
			if ($nbrenew > 0)
			{
				$sql = "UPDATE ".PREFIXE_TABLES."galerie SET nbrephotos = '$newnbrephotos' WHERE numgalerie = '$_POST[num]'";
				send_sql($db, $sql);
			}
			// On enregistre l'action qui vient d'avoir lieu.
			log_this('Ajout de '.$nbrenew.' photos à l\'album '.$albtitre, 'galerie');
			header('Location: index.php?page=gestion_galerie&a=msg&x=okadd&nbre='.$nbrenew.'&num='.$_POST['num']);
		}
		else
		{
			header('Location: index.php?page=gestion_galerie&a=msg&x=erreur');
		}
	}
	else
	{
		header('Location: index.php?page=gestion_galerie&a=msg&x=erreur');
	}
}
else if ($_POST['a'] == 'dosuppr')
{
	$sql = "DELETE FROM ".PREFIXE_TABLES."albums WHERE numalbum = '$_POST[num]'";
	send_sql($db, $sql);
	$sql = "DELETE FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '$_POST[num]'";
	send_sql($db, $sql);
	$sql = "DELETE FROM ".PREFIXE_TABLES."commentaires WHERE album = '$_POST[num]'";
	send_sql($db, $sql);
	// envoi du mail
	log_this('Suppression album photo '.$_POST['num'], 'galerie');
	header('Location: index.php?page=gestion_galerie&a=msg&x=oksuppr');
}
else if ($_POST['a'] == 'dosupprphoto')
{
	$sql = "SELECT * FROM ".PREFIXE_TABLES."galerie as a, ".PREFIXE_TABLES."albums as b WHERE b.numphoto = '$_POST[num]' AND a.numgalerie = b.numalbum";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$p = mysql_fetch_assoc($res);
		$album = $p['numalbum'];
		$nbrephotos = $p['nbrephotos'];
		$posphoto = $p['posphoto'];
		$nomfichierpt = $p['dossierpt'].$p['nomfichier'];
		$nomfichiergd = $p['dossiergd'].$p['nomfichier'];
		// suppression de la photo dans l'album proprement dit
		$sql = "DELETE FROM ".PREFIXE_TABLES."albums WHERE numphoto = '$_POST[num]'";
		send_sql($db, $sql);
		// Suppression des commentaires de la photo supprimée
		$sql = "DELETE FROM ".PREFIXE_TABLES."commentaires WHERE album = '$album' AND photo = '$posphoto'";
		send_sql($db, $sql);
		if ($nbrephotos > 1)
		{
			// Mise à jour du nombre de photos dans l'album
			$sql = "UPDATE ".PREFIXE_TABLES."galerie SET nbrephotos = nbrephotos - 1 WHERE numgalerie = '$album'";
			send_sql($db, $sql);
			// Mise à jour de la numérotation des photos après la photo supprimée (x-1)
			$sql = "UPDATE ".PREFIXE_TABLES."albums SET posphoto = posphoto - 1 WHERE numalbum = '$album' AND posphoto > '$posphoto'";
			send_sql($db, $sql);
			// Mise à jour de la numérotation des commentaires pour conserver la relation photo - commentaire
			$sql = "UPDATE ".PREFIXE_TABLES."commentaires SET photo = photo - 1 WHERE album = '$album' AND photo > '$posphoto'";
			send_sql($db, $sql);
			log_this('Suppression de la photo '.$nomfichierpt.' - album '.$_POST['num'], 'galerie');
			header('Location: index.php?page=gestion_galerie&a=msg&x=oksupprphoto&r='.urlencode($_POST['r']));
		}
		else 
		{
			destroy_structure($album);
			log_this('Suppression dernière photo - album '.$_POST['num'], 'galerie');
			header('Location: index.php?page=gestion_galerie&a=msg&x=oksupprlastphoto&r='.urlencode($_POST['r']));
		}
	}
}
else if (empty($_POST['a']) and empty($_GET['a']))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion galeries</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" type="text/css" />
</head>
<body bgcolor="#FFFFFF">
<?php
	}
?>
<h1>Gestion des albums photos du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la Page
    d'Accueil Membres</a> - <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'galerie.htm' : 'index.php?page=galerie';?>">Retour aux albums photos</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.num.value == "")
	{
		alert("N'oublie pas de choisir un album !");
		return false;
	}
	else
	{
		return true;
	}
}
//-->
</script>
<div class="instructions">
  <p>Sur cette page, tu peux g&eacute;rer les diff&eacute;rents albums photos
    de la galerie. Tu peux cr&eacute;er tes albums, y ajouter tes propres photos,
    r&eacute;diger une description, bref, tu peux faire tout ce que tu veux.</p>
</div>
<form action="index.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return check_form(this)">
  <h2>Cr&eacute;er un album</h2>
  <p> Tu peux cr&eacute;er un nouvel album de diverses mani&egrave;res : en t&eacute;l&eacute;chargeant
    toi-m&ecirc;me tes photos, en donnant un cd avec les photos au webmaster
    ou en utilisant des photos depuis un autre site web. Tout se fait tr&egrave;s
    simplement.</p>
  <p align="center"><a href="index.php?page=gestion_galerie&amp;a=create" class="bouton">Cr&eacute;er
      un album</a> </p>
  <?php
	$sql = "SELECT titre, numgalerie FROM ".PREFIXE_TABLES."galerie ORDER BY numgalerie DESC";
	$res = send_sql($db, $sql);
	$liste = '';
	if (mysql_num_rows($res) > 0)
	{
?>
  <h2>Autres op&eacute;rations </h2>
  <p>Choisis l'album que tu souhaites g&eacute;rer. </p>
    <p align="center">
    <select name="num" size="15">
      <?php
		while ($album = mysql_fetch_assoc($res))
		{
			$choisi = '';
			if ($album['numgalerie'] == $_POST['num']) {$choisi = 'selected';}
			$liste .= '<input type="hidden" name="album'.$album['numgalerie'].'" value="'.$album['statutgalerie'].'">';
			echo '<option value="'.$album['numgalerie'].'">'.$album['titre'].'</option>';
		}
?>
    </select>
  </p>
  <p align="center"><span class="rmqbleu">Action :</span>
    <input name="a" type="radio" id="modifalb" value="modifalbum" checked="checked" onclick="getElement('gestion_suite').value = 'Modifier l\'album';" />
    <label for="modifalb">Modifier</label>
    <input type="radio" name="a" value="suppr" id="suppr" onclick="getElement('gestion_suite').value = 'Supprimer l\'album';" />
    <label for="suppr">Supprimer</label>
    <input type="radio" name="a" value="addimg" id="ajout" onclick="getElement('gestion_suite').value = 'Ajouter des photos';" />
    <label for="ajout">Ajout de photos</label></p>
  <p align="center"><span class="rmqbleu">
    <input type="hidden" name="page" value="gestion_galerie" />
    </span>
    <input name="Envoyer" type="submit" value="Modifier l'album" id="gestion_suite" />
  </p>
  <?php
	echo $liste;
?>
  <?php
	}
?>
<h2>Param&egrave;tres de la galerie</h2>
<p>Divers param&egrave;tres peuvent &ecirc;tre modifi&eacute;s pour la galerie.</p>
<p align="center"><a href="index.php?page=config_site&categorie=galerie">Configuration de la galerie</a> </p>
</form>
<?php
	echo $msg;
}
else if ($_GET['a'] == 'create')
{
	if (empty($_GET['step']))
	{ // première étape : choix entre deux solutions
	  // - les photos sont déjà sur le portail
	  // - les photos ne sont pas encore sur le portail
?>
<h1>Cr&eacute;ation d'un album photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<div class="instructions">
  <p>Pour cr&eacute;er ton album photo, deux techniques sont mises &agrave; ta
    disposition, choisis celle qui te convient le mieux.</p>
  <p class="petitbleu">Dans tous les cas, tu dois &ecirc;tre le d&eacute;tenteur
    des droits des photos et/ou avoir l'autorisation expresse de leur propri&eacute;taire
    et, dans la mesure du possible des personnes qui y figurent.</p>
</div>
<div class="form_config_site">
  <h2>Technique 1 : Je veux d&eacute;poser mes photos sur le portail</h2>
  <p>Dans un premier temps, t&eacute;l&eacute;charge les photos depuis ton ordinateur
    sur le portail. Il s'occupe de les mettre aux bonnes dimensions, pas de soucis.</p>
  <p>Si toutes les photos sont d&eacute;j&agrave; sur le portail, tu peux passer
  directement &agrave; l'&eacute;tape 2.</p>
  <p><strong>Etape 1 :</strong> <a href="index.php?page=upload_galerie&amp;step=1" class="bouton">T&eacute;l&eacute;charger
      les photos *</a>
<strong>Etape 2 :</strong> <a href="index.php?page=gestion_galerie&amp;a=create&amp;step=1&amp;mode_creation=local" class="bouton">Cr&eacute;er
      l'album</a>
  <p class="petitbleu">* Tu peux aussi donner un CD contenant les photos au webmaster. </p>
  <p class="petitbleu">Plusieurs personnes peuvent d&eacute;poser des photos
    dans un m&ecirc;me album. Il suffit de les d&eacute;poser dans le m&ecirc;me
    dossier puis de cr&eacute;er l'album. </p>
</div>
<?php
	if (!@extension_loaded('gd'))
	{
?>
<div class="msg">
  <p class="rmq">Redimensionnement des photos impossible</p>
  <p>Tu ne pourras pas d&eacute;poser tes photos sur le site toi-m&ecirc;me. Demande au webmaster ce que tu dois faire.</p>
  <p class="petit">Note technique : la biblioth&egrave;que GD n'est pas charg&eacute;e.</p>
</div>
<?php
	}
	if (function_exists('ini_get') and !@ini_get('file_uploads')) 
	{ 
?>
<div class="msg">
  <p class="rmq">L'upload de fichiers est d&eacute;sactiv&eacute; </p>
  <p>Tu ne pourras pas d&eacute;poser tes photos sur le site toi-m&ecirc;me.
    Demande au webmaster ce que tu dois faire.</p>
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
?>
<div class="form_config_site">
  <h2>Technique 2 : Mes photos sont sur un autre serveur internet</h2>
  <p>Tu peux cr&eacute;er un album photos en utilisant des photos qui se trouvent
    sur un autre site web*. Elles resteront sur l'autre site et seront utilis&eacute;es &agrave; distance.<br />
    De cette mani&egrave;re, tu peux stocker des photos sur autant de sites que
    tu le souhaites et les afficher sur un seul et m&ecirc;met site.</p>
  <p class="rmqbleu">Technique au choix :</p>
<?php
	if (!function_exists('ftp_connect'))
	{ // le module ftp n'est pas chargé sur le serveur
?>
    <p><strong>Indexation automatique des photos :</strong> <acronym title="Le module FTP est désactivé, fonction inaccessible">Fonction inaccessible</acronym></p>
    <?php
	}
	else
	{
?>
    <p><strong>Indexation automatique des photos :</strong> <a href="index.php?page=gestion_galerie&amp;a=create&step=1&amp;mode_creation=distant&amp;ftp=oui" class="bouton">Avec
    un acc&egrave;s FTP</a></p>
    <?php
	}
?>
    <p><strong>Indexation manuelle des photos :</strong> <a href="index.php?page=gestion_galerie&amp;a=create&step=1&amp;mode_creation=distant&amp;ftp=non" class="bouton">Sans
    acc&egrave;s FTP</a></p>
  <p class="petitbleu">* Certains serveurs gratuits interdisent les connexions
    par FTP ou emp&ecirc;chent l'utilisation de photos par un autre site.<br />
    ** Pour acc&eacute;der au FTP du serveur, il te faudra les informations de
    connexion n&eacute;cessaires (url, login et mot de passe).</p>
</div>
<?php
	if (!function_exists('ftp_connect'))
	{ // le module ftp n'est pas chargé sur le serveur
?>
<div class="msg">
  <p class="rmq">Albums distants - Module FTP d&eacute;sactiv&eacute; </p>
  <p>Le module FTP n'est pas activ&eacute; sur le serveur. Le portail ne pourra donc pas r&eacute;cup&eacute;rer les infos des photos sur le serveur distant o&ugrave; elles sont stock&eacute;es.</p>
  <p>N&eacute;anmoins, tu peux indexer manuellement
    tes photos.</p>
</div>
<?php   
	}
?>
<?php
	}
	else if ($_GET['step'] == 1)
	{ // les photos sont déjà sur le portail, création de la galerie
		$gdsource = $ptsource = '';
		if (!empty($_GET['dossier']))
		{
			$gdsource = urldecode($_GET['dossier']).'gd/';
			$ptsource = urldecode($_GET['dossier']).'pt/';
		}
?>
<script type="text/javascript" language="JavaScript">
<!--
function addphoto()
{
	window.open('selectdossier.php<?php echo (!empty($ptsource)) ? '?dossier='.urlencode($ptsource) : ''; ?>', 'choixphoto', 'width=550,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}

function add_ftp(u, l, p)
{ // "U"rl du serveur FTP, "L"ogin, "P"assword
	window.open('selectdossier_ftp.php?'+'u='+u+'&l='+l+'&p='+p, 'choixphoto', 'width=700,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}

function check_form(form)
{
	if (form.albtitre.value != "" && form.gdsource.value != "" && form.ptsource.value != "" && form.photoaccueil.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de remplir les cases marquées d'une astérisque.");
		return false;
	}
}
//-->
</script>
<h1>Cr&eacute;er un album photos - 2 Cr&eacute;ation proprement dite </h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<?php
		if ($_GET['mode_creation'] != 'distant')
		{
?>
<div class="instructions">
<p>Maintenant que <span class="rmqbleu">les photos sont sur le portail</span>,
  tu vas pouvoir cr&eacute;er l'album proprement dit. Pour cela, remplis le formulaire
  ci-dessous de la mani&egrave;re la plus compl&egrave;te possible en suivant
  les indications.</p>
<p class="petitbleu">Si les photos ne sont pas encore sur le portail, merci de te
  rendre sur <a href="index.php?page=gestion_galerie&a=create">cette page</a>.</p>
</div>
<?php
		}
		else
		{
?>
<div class="instructions">
<p>Tu vas cr&eacute;er l'album en utilisant des photos se trouvant sur un autre
  serveur. </p>
<p><strong>Les photos restent sur cet autre serveur</strong> et ne sont
    pas t&eacute;l&eacute;charg&eacute;es ici. Elles seront affich&eacute;es &agrave; distance
    gr&acirc;ce &agrave; la magie d'internet...</p>
</div>
<?php
		}
?>
<form action="gestion_galerie.php" method="post" name="formulaire" class="form_config_site" id="formulaire" onsubmit="return check_form(this)">
<h2>Param&egrave;tres techniques</h2>
<?php
		if ($_GET['mode_creation'] == 'distant')
		{ // les photos sont sur un serveur distant
		  // création de l'album à distance (les photos restent sur le serveur distant)
?>
<p class="petitbleu">- Les dossiers sont
  les &eacute;l&eacute;ments les plus importants. Par principe, les photos
  d'un album sont dans un m&ecirc;me dossier. Ce dossier est divis&eacute; en
  deux sous-dossiers, le premier contenant les photos en grand format
  et le second les miniatures. A noter que <span class="rmq">les grandes
  photos et les miniatures doivent porter le m&ecirc;me nom</span>.<br />
  <br />
  Les param&egrave;tres de connexion au serveur FTP ne sont pas conserv&eacute;s
  sur le portail, ils sont exploit&eacute;s uniquement &agrave; la cr&eacute;ation
  de l'album pour indexer les photos automatiquement.</p>
<h2>Acc&egrave;s FTP </h2>
<p>
  <input name="acces_ftp" type="checkbox" id="acces_ftp" value="oui" onclick="if(this.checked){manuelle(false);} else {manuelle(true);}"<?php echo ($_GET['ftp'] == 'oui' and function_exists('ftp_connect')) ? ' checked="checked"' : ''; ?> tabindex="1" />
  <label for="acces_ftp">J'ai acc&egrave;s au serveur FTP</label>
<?php
			if (!function_exists('ftp_connect'))
			{ // le module ftp n'est pas chargé sur le serveur
?>
          <span class="rmq">(Le module FTP est d&eacute;sactiv&eacute;, pas d'acc&egrave;s
          au ftp)</span>
<?php  
			}
?></p>
<div id="ftp_config">
<table>
  <tr valign="top" class="td-gris">
	<td>Serveur FTP :</td>
	<td><input name="ftp_url" type="text" id="ftp_url" tabindex="2" onchange="active_bouton_param_auto()" /></td>
  </tr>
  <tr valign="top" class="td-gris">
	<td>Nom d'utilisateur FTP :</td>
	<td><input name="ftp_login" type="text" id="ftp_login" tabindex="3" onchange="active_bouton_param_auto()" /></td>
  </tr>
  <tr valign="top" class="td-gris">
	<td>Mot de passe FTP :</td>
	<td><input name="ftp_pw" type="password" id="ftp_pw" tabindex="4" onchange="active_bouton_param_auto()" /></td>
  </tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function active_bouton_param_auto()
{
	if (getElement('ftp_url').value != '' && getElement('ftp_login').value != '')
	{ // le mot de passe peut être vide si le ftp est un ftp public
		getElement('choisir').disabled = false;
	}
}
//-->
</script>
<noscript>
<p align="center" class="rmq">Pour r&eacute;gler automatiquement les autres param&egrave;tres,
  active le javascript dans ton navigateur.
</p>
</noscript>
<p align="center">  <input name="choisir" type="button" id="choisir" value="Réglage automatique du reste des données techniques" onclick="add_ftp(getElement('ftp_url').value, getElement('ftp_login').value, getElement('ftp_pw').value);" tabindex="5" disabled="disabled" />
</p>
<p class="petitbleu">Le chemin HTTP d'un dossier est diff&eacute;rent du chemin
  FTP de ce m&ecirc;me dossier. Indique ci-dessous le chemin complet &agrave; parcourir
  sur le serveur FTP pour atteindre les deux dossiers.</p>
<table>
  <tr valign="top" class="td-gris">
	<td height="19">Chemin FTP des Grandes photos :</td>
	<td><input name="ftp_gd_path" type="text" id="ftp_gd_path" style="width:280px;" title="Indique le chemin depuis la racine du serveur FTP" /></td>
  </tr>
  <tr valign="top" class="td-gris">
	<td>Chemin FTP des Miniatures :</td>
	<td><input name="ftp_pt_path" type="text" id="ftp_pt_path" style="width:280px;" title="Indique le chemin depuis la racine du serveur FTP" /></td>
  </tr>
</table>
<p class="petitbleu">Clique sur le bouton &quot;Param&eacute;trer&quot; pour
 r&eacute;gler facilement les trois param&egrave;tres suivants.</p>
</div>
<p class="petitbleu">La photo d'accueil est
  la photo affich&eacute;e dans la liste des albums quand la description
  de l'album est d&eacute;ploy&eacute;e.</p>
<table>
<tr valign="top" class="td-gris">
  <td>Dossier Grandes photos* : </td>
  <td><input name="gdsource" type="text" style="width:280px;" value="http://" maxlength="255"<?php echo (!empty($gdsource)) ? ' value="'.$gdsource.'"' : ''; ?> />
	(url compl&egrave;te)
	<input name="mode_creation" type="hidden" id="mode_creation" value="distant" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Dossier Miniatures* : </td>
  <td><input name="ptsource" type="text" style="width:280px;" value="http://" maxlength="255"<?php echo (!empty($ptsource)) ? ' value="'.$ptsource.'"' : ''; ?> />
	(url compl&egrave;te)</td>
</tr>
<tr valign="top" class="td-gris">
  <td valign="top">Photo d'accueil* :</td>
  <td><input type="text" name="photoaccueil" maxlength="255" />
	(nom du fichier seul) </td>
</tr>
</table>
    <?php
		}
		else if ($_GET['mode_creation'] == 'local' or empty($_GET['mode_creation']))
		{ // les photos sont sur le portail
			if (empty($_GET['dossier']))
			{
?>
<p class="petitbleu">- Les dossiers sont les &eacute;l&eacute;ments
les plus importants. Par principe, les photos d'un album sont dans un
m&ecirc;me dossier. Ce dossier est divis&eacute; en deux sous-dossiers,
le premier (gd) contenant les photos en grand format et le second (pt)
les miniatures. A noter que <strong>les photos contenues dans gd et dans
pt doivent porter le m&ecirc;me nom</strong>.<br />
- Clique sur le bouton &quot;<strong>Param&eacute;trer</strong>&quot; pour
r&eacute;gler facilement les trois param&egrave;tres ci-dessous.</p>
<table>
<tr valign="top" class="td-gris">
  <td>Dossier Grandes photos* : </td>
  <td><input type="text" name="gdsource" maxlength="255"<?php echo (!empty($gdsource)) ? ' onfocus="alert(\'Ce paramètre a été défini automatiquement, assure-toi de savoir ce que tu fais avant de le modifier.\');" value="'.$gdsource.'"' : ''; ?> style="width:280px;" />
	<input name="choisir" type="button" id="choisir" value="Paramétrer" onclick="addphoto();" />
	<input name="mode_creation" type="hidden" id="mode_creation" value="local" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Dossier Miniatures* : </td>
  <td><input type="text" name="ptsource" maxlength="255"<?php echo (!empty($ptsource)) ? ' onfocus="alert(\'Ce paramètre a été défini automatiquement, assure-toi de savoir ce que tu fais avant de le modifier.\');" value="'.$ptsource.'"' : ''; ?> style="width:280px;" /></td>
</tr>
</table>
<?php
			}
?>
<p class="petitbleu">La photo d'accueil est la photo affich&eacute;e dans la liste des albums quand la description
de l'album est d&eacute;ploy&eacute;e.
<?php
			if (!empty($_GET['dossier']))
			{
?>
        <input type="hidden" name="gdsource"<?php echo (!empty($gdsource)) ? ' value="'.$gdsource.'"' : ''; ?> />
        <input name="mode_creation" type="hidden" id="mode_creation" value="local" />
        <input type="hidden" name="ptsource"<?php echo (!empty($ptsource)) ? ' value="'.$ptsource.'"' : ''; ?> />
<?php
			}
?>
</p>
<table>
<tr valign="top" class="td-gris">
  <td valign="top">Photo d'accueil* :</td>
  <td><input type="text" name="photoaccueil" maxlength="255" title="Nom du fichier seul" />
<?php
			if (!empty($_GET['dossier']))
			{
?>
  <input name="choisir" type="button" id="choisir" value="S&eacute;lectionner la photo" onclick="addphoto();" />
<?php
			}
?>
  </td>
</tr>
</table>
<?php
		}
?>
<h2>Personnalisation de l'album</h2>
<p class="petitbleu">Le titre de l'album est
  celui qui sera affich&eacute; dans la liste des albums, choisis un titre clair.</p>
<table>
<tr valign="top" class="td-gris">
  <td>Titre de l'album* :</td>
  <td><input type="text" name="albtitre" size="50" maxlength="255" />
  </td>
</tr>
<tr valign="top" class="td-gris">
  <td>Date de l'activit&eacute; :</td>
  <td><input type="text" name="dateactivite" />
	(jj/mm/aaaa)
	<input type="button" name="ddj" value="Date du jour" onclick="formulaire.dateactivite.value = '<?php echo datedujour(); ?>'" /></td>
</tr>
</table>
<h3>Description de l'album : (bbcodes seulement)</h3>
<p class="petitbleu">D&eacute;cris
    ici en quelques mots le contenu de l'album.</p>
<?php panneau_mise_en_forme('description', true); ?>
<textarea name="description" id="description" cols="50" rows="5"></textarea>
<h3>Description secondaire : (bbcodes + html)</h3>
<p class="petitbleu">La mise en forme de la description
    secondaire est l&eacute;g&egrave;rement diff&eacute;rente de la premi&egrave;re.
    Tu peux y placer des infos de second plan, moins importantes, ou destin&eacute;es &agrave; un
    autre public (placez par exemple les liens vers les infos de location de
    l'endroit de camp ou de week-end).</p>
<?php panneau_mise_en_forme('description2', true); ?>
<textarea name="description2" id="description2" cols="50" rows="5"></textarea>
<h3>Infos diverses </h3>
<p class="petitbleu">Choisis ci-dessous la Section
    dans laquelle cet album sera affich&eacute;. Les activit&eacute;s communes
    sont rassembl&eacute;es au niveau de l'Unit&eacute;.<br />
    Ensuite, tu peux &eacute;crire la liste des auteurs des photos de cet
    album ou simplement cliquer sur &quot;Moi&quot; si tu es le seul auteur
    des photos.</p>
<table>
<tr valign="top" class="td-gris">
  <td valign="top">Section concern&eacute;e</td>
  <td valign="top"><select name="galerie_section">
	  <option value="0">Toutes les sections</option>
<?php
		foreach ($sections as $section)
		{
			if (!empty($section['site_section']))
			{
?>
          <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; ?></option>
<?php
			 }
		}
?>
        </select>
  </td>
</tr>
<tr valign="top" class="td-gris">
  <td height="28" valign="top">Photos prises par :</td>
  <td valign="top"><input name="auteurphotos" type="text" id="auteurphotos" maxlength="100" />
	<input type="button" name="autphotos" value="Moi" onclick="formulaire.auteurphotos.value = '<?php echo $user['pseudo']; ?>'" />
  </td>
</tr>
</table>
<?php
	if ($_GET['mode_creation'] == 'distant')
	{
?>
<div id="selection_manuelle">
<h3>S&eacute;lection manuelle des photos</h3>
<p>Encode manuellement le nom des fichiers
  ci-dessous. Clique autant de fois que n&eacute;cessaire sur le
  bouton <img src="templates/default/images/plus.png" alt="" width="12" height="12" /> pour
  ajouter les champs n&eacute;cessaires</p>
<p align="center"><span class="petitbleu">Format standard
  des noms de fichiers :</span>
  <input type="text" name="prefixe" id="prefixe" title="A chaque ajout de fichier, le préfixe sera placé dans la case pour te simplifier la vie" />
  (exemple : <strong>camp2004-</strong>.jpg)</p>
<p><span id="Container0"></span> 
<img src="templates/default/images/plus.png" width="12" height="12" border="0" style="cursor:pointer;" onclick="add();" alt="Ajouter une photo" title="Ajouter une photo" /> <img src="templates/default/images/moins.png" width="12" height="12" border="0" style="cursor:pointer;" onclick="suppr();" alt="Supprimer une photo" title="Supprimer une photo" />
</p>
<script type="text/javascript" language="JavaScript">
<!--
var i=0;
function add()
{
   var NewInput ="<br />"+(i+1)+". <input type='text' name='photo-"+(i+1)+"' value='"+getElement("prefixe").value+"' style='width:150px;'> <span id='Container"+(i+1)+"'></span> " ;
   getElement(('Container'+i)).innerHTML = NewInput ;
   var texte;
   if (i >= 1)
   {
	texte = (i+1)+" photos s&eacute;lectionn&eacute;es";
   }
   else
   {
	texte = (i+1)+" photo s&eacute;lectionn&eacute;e";
   }
   getElement("nb_photos").innerHTML = texte;
   getElement("nbre_photos").value = i+1;
   i++;
}
function suppr()
{
   if (i > 1)
   {
	   var NewInput ="" ;
	   getElement(('Container'+(i-1))).innerHTML = NewInput ;
	   var texte;
	   if (i >= 1)
	   {
		texte = (i-1)+" photos s&eacute;lectionn&eacute;es";
	   }
	   else
	   {
		texte = (i-1)+" photo s&eacute;lectionn&eacute;e";
	   }
	   getElement("nb_photos").innerHTML = texte;
	   getElement("nbre_photos").value = i-1;
	   i--;
	}
	else
	{
		alert('Ton album doit au moins contenir une photo...');
	}
}
//-->
</script>
<input type="hidden" name="nbre_photos" id="nbre_photos" value="0" />
<span id="nb_photos"></span>
</div>
<script type="text/javascript" language="JavaScript">
<!--
function manuelle(statut)
{
	if (statut)
	{
		getElement("selection_manuelle").style.display = "inline";
		getElement("ftp_config").style.display = "none";
	}
	else
	{
		getElement("selection_manuelle").style.display = "none";
		getElement("ftp_config").style.display = "inline";
	}
}
manuelle(<?php echo ($_GET['ftp'] == 'oui' and function_exists('ftp_connect')) ? 'false' : 'true'; ?>);
//-->
</script>
<?php
	}
?>
<p align="center"><input type="hidden" name="a" value="docreate" />
<input type="submit" name="Submit2" value="Cr&eacute;er" /></p>
</form>
<?php
	} // fin step 1
}
else if ($_POST['a'] == 'modifalbum')
{
	$sql = "SELECT * FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '$_POST[num]'";
	if ($res = send_sql($db, $sql))
	{
		$album = mysql_fetch_assoc($res);
	}
	else
	{
		echo 'une erreur s\'est produite !';
		exit;
	}
?>
<script type="text/javascript" language="JavaScript">
<!--
function addphoto()
{
	window.open('selectdossier.php?dossier=<?php echo urlencode($album[dossierpt]); ?>', 'choixphoto', 'width=300,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}

function check_form(form)
{
	if (form.albtitre.value != "" && form.gdsource.value != "" && form.ptsource.value != "" && form.photoaccueil.value != "")
	{
		if (form.mode_creation == "<?php echo $album['mode_creation']; ?>")
		{
			return true;
		}
		else
		{ // Le webmaster a changé le mode de création local/distant
			return confirm("Tu as modifié le mode de création local/distant de l'album.\nPour que l'album continue à être fonctionnel, tu dois avoir déplacé les photos depuis/vers le serveur distant et avoir mis à jour le chemin des photos et des miniatures.\n\nEs-tu certain de vouloir continuer ?");
		}
	}
	else
	{
		alert("Merci de remplir les cases marquées d'une astérisque.");
		return false;
	}
}
//-->
</script>
<h1>Modification des informations d'un album photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<div class="instructions">
<p>Modifie ici les informations au sujet d'un album photos.<br />
  Attention que si tu modifies les informations relatives aux dossiers utilis&eacute;s
  par l'album, les photos risquent fort de ne plus &ecirc;tre affich&eacute;es
  correctement (Aucune v&eacute;rification n'a lieu). </p>
</div>
<form action="gestion_galerie.php" method="post" name="formulaire" id="formulaire" onsubmit="return check_form(this)" class="form_config_site">
  <h2>
    <input type="hidden" name="num" value="<?php echo $album['numgalerie']; ?>" /> 
  Param&egrave;tres techniques</h2>
  <p class="petitbleu">Le titre de l'album est celui qui sera affich&eacute; dans la liste des albums, choisis un titre
        clair.</p>
<table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr valign="top" class="td-gris">
      <td width="160">Titre de l'album* :</td>
      <td><input type="text" name="albtitre" size="50" maxlength="255" value="<?php echo $album['titre']; ?>" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Date de l'activit&eacute; :</td>
      <td><input type="text" name="dateactivite" value="<?php echo ($album['dateactivite'] != '0000-00-00') ? date_ymd_dmy($album['dateactivite'], 'enchiffres') : ''; ?>" />
        (jj/mm/aaaa)
        <input type="button" name="ddj2" value="Date du jour" onclick="formulaire.dateactivite.value = '<?php echo datedujour(); ?>'" /></td>
    </tr>
  </table>
<p class="petitbleu">- Les dossiers sont les &eacute;l&eacute;ments
	les plus importants. Par principe, les photos d'un album sont dans un
	dossier portant le nom de l'album. Ce dossier est divis&eacute; en deux
	sous-dossiers, le premier (gd) contenant les photos en grand format et
	le second (pt) les miniatures. A noter que <strong>les photos contenues
	dans gd et dans pt doivent porter le m&ecirc;me nom</strong>.<br />
	- La photo d'accueil est la photo affich&eacute;e dans la liste des albums
	quand la description de l'album est d&eacute;ploy&eacute;e.<br />
	- Clique sur le bouton &quot;Param&eacute;trer&quot; pour r&eacute;gler
	facilement les trois param&egrave;tres suivants.</p>
<table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr valign="top" class="td-gris">
      <td>Grandes photos* :</td>
      <td><input type="text" name="gdsource" maxlength="255" value="<?php echo $album['dossiergd']; ?>" style="width:280px;" />
        <input name="choisir2" type="button" id="choisir2" value="Param&eacute;trer" onclick="addphoto();" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Miniatures* :</td>
      <td><input type="text" name="ptsource" maxlength="255" value="<?php echo $album['dossierpt']; ?>" style="width:280px;" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td valign="top">Photo d'accueil* :</td>
      <td><input type="text" name="photoaccueil" maxlength="255" value="<?php echo $album['photoaccueil']; ?>" />
      </td>
    </tr>
<?php
	if ($user['niveau']['numniveau'] == 5)
	{ // Seul le webmaster peut modifier le mode de création local/distant de l'album
?>
    <tr valign="top" class="td-gris">
      <td valign="top">Mode de cr&eacute;ation*&nbsp;:</td>
      <td><input type="radio" name="mode_creation" id="mode_creation_local" value="local"<?php echo ($album['mode_creation'] == 'local') ? ' checked="checked"' : ''; ?> />
        <label for="mode_creation_local">Local</label>
        <input type="radio" name="mode_creation" id="mode_creation_distant" value="distant"<?php echo ($album['mode_creation'] == 'distant') ? ' checked="checked"' : ''; ?> />
        <label for="mode_creation_distant">Distant</label><br />
        <span class="petitbleu">Si tu modifies le mode de cr&eacute;ation de l'album, adapte le chemin des miniatures et des photos, et n'oublie pas de d&eacute;placer les photos vers le nouvel emplacement.</span> </td>
    </tr>
<?php
	}
?>
  </table>
<?php
	if ($user['niveau']['numniveau'] != 5)
	{
?>
<input type="hidden" name="mode_creation" id="mode_creation_local" value="<?php echo $album['mode_creation']; ?>" />
<?php
	}
?>
<h2>Personnalisation de l'album </h2>
<p class="petitbleu">&nbsp;</p>
<h3>Description de l'album : (bbcodes seulement)</h3>
<span class="petitbleu">D&eacute;cris ici en quelques mots le contenu de l'album.</span>
<?php panneau_mise_en_forme('description', true); ?>
<textarea name="description" id="description" cols="50" rows="5"><?php echo stripslashes($album['description']); ?></textarea>
<p class="petitbleu">&nbsp;</p>
<h3>Description de l'album : (bbcodes + html)</h3>
<p class="petitbleu">La mise en forme de la description
	secondaire est l&eacute;g&egrave;rement diff&eacute;rente de la premi&egrave;re.
	Tu peux y placer des infos de second plan, moins importantes, ou destin&eacute;es &agrave; un
	autre public (j'y place des liens vers les infos de location d'un endroit
	de camp ou de hike, par exemple).</p>
<p>
  <?php panneau_mise_en_forme('description2', true); ?>
  <textarea name="description2" id="description2" cols="50" rows="5"><?php echo stripslashes($album['description2']); ?></textarea>
</p>
<h3>Infos diverses </h3>
<p class="petitbleu">Choisis ci-dessous la Section
  dans laquelle cet album sera affich&eacute;. Les activit&eacute;s communes
  sont rassembl&eacute;es au niveau de l'Unit&eacute;.<br />
  Ensuite, tu peux &eacute;crire la liste des auteurs des photos de cet
  album ou simplement cliquer sur &quot;Moi&quot; si tu es le seul auteur
  des photos.</p>
<table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr valign="top" class="td-gris">
      <td valign="top">Section concern&eacute;e</td>
      <td valign="top"><select name="galerie_section">
          <option value="0">Toutes les sections</option>
          <?php
	foreach ($sections as $section)
	{
		if (!empty($section['site_section']))
		{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php if ($album['galerie_section'] == $section['numsection']) { echo ' selected'; }?>><?php echo $section['nomsection']; ?></option>
          <?php
		}
	}
?>
        </select>
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td valign="top">Photos prises par :</td>
      <td valign="top"><input name="auteurphotos" type="text" id="auteurphotos" maxlength="100" value="<?php echo $album['auteurphotos']?>" />
        <input type="button" name="autphotos" value="Moi" onclick="formulaire.auteurphotos.value = '<?php echo $user['pseudo']; ?>'" />
      </td>
    </tr>
  </table>
<p align="center"><input type="hidden" name="a" value="domodifalbum" />
<input type="submit" name="Submit" value="Enregistrer les modifications" />
</p>
</form>
<?php
}
else if ($_POST['a'] == 'addimg')
{
	$sql = "SELECT * FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '$_POST[num]'";
	if ($res = send_sql($db, $sql))
	{
		$nbre = mysql_num_rows($res);
		$album = mysql_fetch_assoc($res);
	}
	else
	{
		echo 'une erreur s\'est produite !';
		exit;
	}
?>
<h1>Ajout de photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<?php
	if ($album['mode_creation'] != 'distant')
	{
		if ($nbre == 1)
		{
			$open=OpenDir($album['dossiergd']);
			$nbre_dossier = 0;
			while($image=ReadDir($open))
			{
				if($image != '.' and $image != '..' and eregi("jpg$", $image))
				{
					$nbre_dossier++;
				}
			}
			closedir($open);
			$nbre_new = $nbre_dossier - $album['nbrephotos'];
?>
<div class="instructions">
  <p>Sur cette page, tu peux ajouter des photos &agrave; un album.<br />
    Pour ajouter des photos &agrave; un album existant, il suffit de les ajouter
    dans le dossier de l'album et ensuite d'actualiser l'album.</p>
  <p class="rmqbleu">Album en cours : <span class="rmq"><?php echo $album['titre']; ?></span></p>
</div>
<form action="index.php" method="get" name="form2" id="form2" class="form_config_site">
  <h2>Etape 1 : D&eacute;poser les photos sur le portail</h2>
  <p> Les nouvelles photos ne sont pas encore sur le portail.</p>
  <input type="hidden" name="page" value="upload_galerie" />
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="show" value="oui" />
  <input type="hidden" name="suite" value="add" />
  <input type="hidden" name="num" value="<?php echo $album['numgalerie']; ?>" />
  <input type="hidden" name="dossier" value="<?php echo urlencode(ereg_replace("/pt/$", "/", $album['dossierpt'])); ?>" />
  <p align="center">
    <input type="submit" name="Submit" value="D&eacute;poser des photos pour cet album" />
  </p>
</form>
<?php
			if ($nbre_new > 0)
			{
?>
<form action="gestion_galerie.php" method="post" class="form_config_site">
  <h2>Etape 2 : Actualiser l'album</h2>
  <p>Les nouvelles photos sont sur le portail.</p>
  <input type="hidden" name="num" value="<?php echo $album['numgalerie']; ?>" />
  <table border="0" align="center" cellpadding="2" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td width="150">Nombre de photos : </td>
      <td><?php echo $album['nbrephotos']; ?> actuellement dans l'album (<strong><?php echo $nbre_new; ?> en
          attente</strong>)</td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Date de l'activit&eacute; :</td>
      <td><?php echo ($album['dateactivite'] != '0000-00-00') ? date_ymd_dmy($album['dateactivite'], 'enlettres') : 'Non encod&eacute;e'; ?></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td valign="top">Description de l'album :</td>
      <td><?php echo makehtml($album['description'], 'html'); ?> </td>
    </tr>
    <?php
				if ($album['description2'] != '')
				{
?>
    <tr valign="top" class="td-gris">
      <td valign="top">Description de l'album :</td>
      <td><?php echo makehtml($album['description2'], 'html'); ?> </td>
    </tr>
    <?php
				}
?>
  </table>
  <p align="center"><br />
    <input type="hidden" name="a" value="doaddimg" />
    <input type="submit" name="Submit" value="Mettre l'album &agrave; jour" />
  </p>
</form>
<?php
			}
		}
		else
		{
?>
<div class="msg">
  <p align="center" class="rmq">D&eacute;sol&eacute; mais cet album ne semble
    pas exister...</p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
  <p align="center" class="rmq">D&eacute;sol&eacute;, l'ajout de photos apr&egrave;s
    la cr&eacute;ation d'un album distant n'a pas encore &eacute;t&eacute; d&eacute;velopp&eacute;.</p>
  <p align="center">Le webmaster peut transf&eacute;rer les photos de l'album en local, de cette mani&egrave;re tu pourras ajouter des photos &agrave; l'album. </p>
</div>
<?php
	}
}
else if ($_POST['a'] == 'suppr')
{
	$sql = "SELECT * FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '$_POST[num]'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$album = mysql_fetch_assoc($res);
?>
<h1>Suppression d'un album photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<form action="gestion_galerie.php" method="post" onsubmit="return confirm('Es-tu certain de vouloir supprimer cet album ?')" class="form_config_site">
  <p align="center" class="rmq">Es-tu certain de vouloir supprimer l'album <?php echo $album['titre']; ?> ? </p>
  <p align="center" class="petit">Tous les commentaires post&eacute;s dans cet
    album seront &eacute;galement supprim&eacute;s.</p>
  <input type="hidden" name="a" value="dosuppr" />
  <input type="hidden" name="num" value="<?php echo $_POST['num']; ?>" />
  <p align="center">
    <input type="submit" value="Supprimer cet album" />
  </p>
</form>
<?php
	}
	else
	{
?>
<h1>Suppression d'un album photos</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<div class="msg">
  <p align="center" class="rmq">D&eacute;sol&eacute; suppression de cet album
    impossible !</p>
</div>
<?php
	}
}
else if ($_GET['a'] == 'supprphoto')
{
	$sql = "SELECT * FROM ".PREFIXE_TABLES."albums as a, ".PREFIXE_TABLES."galerie as b WHERE a.numphoto = '$_GET[num]' AND a.numalbum = b.numgalerie";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$album = mysql_fetch_assoc($res);
?>
<h1>Suppression d'une photo</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<form action="gestion_galerie.php" method="post" onsubmit="return confirm('Es-tu certain de vouloir supprimer cette photo ?')" class="form_config_site">
  <p align="center" class="rmq">Es-tu certain de vouloir supprimer cette photo
    de l'album <?php echo $album['titre']; ?> ?</p>
  <p align="center"><img src="<?php echo $album['dossierpt'].$album['nomfichier']; ?>" border="0" alt="Photo &agrave; supprimer" /></p>
  <p align="center" class="petit">Tous les commentaires post&eacute;s pour cette
    photo seront &eacute;galement supprim&eacute;s.</p>
  <p align="center">
    <input type="hidden" name="a" value="dosupprphoto" />
    <input type="hidden" name="num" value="<?php echo $_GET['num']; ?>" />
    <input type="hidden" name="r" value="<?php echo urlencode($_GET['r']); ?>" />
    <input type="submit" value="Supprimer cette photo" />
    <input name="Cancel" type="button" id="Cancel" onclick="window.location='<?php echo $_GET['r']; ?>';" value="Annuler" />
  </p>
</form>
<?php
	}
	else
	{
?>
<h1>Suppression d'une photo</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<div class="msg">
  <p align="center" class="rmq">D&eacute;sol&eacute; suppression de cette photo
    impossible !</p>
</div>
<?php
	}
}
else if ($_GET['a'] == 'msg')
{
?>
<h1>Gestion des albums photos du portail</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<div class="msg">
  <?php
	if ($_GET['x'] == 'erreur')
	{
?>
  <p align="center" class="rmq">Une erreur s'est produite !</p>
  <?php
	}
	else if ($_GET['x'] == 'okcreation')
	{
		$pl = ($_GET['nbre'] > 1) ? 's' : '';
		$nbre = ($_GET['nbre'] > 0) ? $_GET['nbre'] : 'Aucune';
		$lien_album = ($site['url_rewriting_actif'] == 1) ? 'g_galerie_'.$album['numgalerie'].'.htm' : 'index.php?page=galerie&amp;show='.$album['numgalerie'];
?>
  <p align="center" class="rmqbleu">L'album a &eacute;t&eacute; cr&eacute;&eacute; avec
    succ&egrave;s (<?php echo $nbre.' photo'.$pl.' ins&eacute;r&eacute;e'.$pl; ?>).</p>
  <p align="center"><a href="<?php echo $lien_album; ?>">Voir l'album</a></p>
  <?php
	}
	else if ($_GET['x'] == 'noftp')
	{
?>
  <p align="center" class="rmq">La connexion FTP a &eacute;chou&eacute; !</p>
  <p align="center" class="rmq">Soit les param&egrave;tres sont incorrects, soit
    tu n'as pas acc&egrave;s au FTP depuis ce portail <br />
    ou alors le serveur distant n'autorise pas les connexions FTP.</p>
  <?php
	}
	else if ($_GET['x'] == 'nodossier')
	{
?>
  <p align="center" class="rmq">Le dossier recherch&eacute; n'existe pas !</p>
  <?php   
	}
	else if ($_GET['x'] == 'erreurdossier')
	{
?>
  <p align="center" class="rmq">Une erreur s'est produite !<br />
    Lecture du dossier source impossible.</p>
  <?php
	}
	else if ($_GET['x'] == 'nodata')
	{
?>
  <p align="center" class="rmq">Cr&eacute;ation impossible !<br />
    Toutes les donn&eacute;es n&eacute;cessaires n'ont pas &eacute;t&eacute; fournies.</p>
  <?php
	}
	else if ($_GET['x'] == 'noimage')
	{
?>
  <p align="center" class="rmq">Cr&eacute;ation impossible !<br />
    Aucune photo trouv&eacute;e avec les donn&eacute;es fournies.</p>
  <?php
	}
	else if ($_GET['x'] == 'okmodif')
	{
?>
  <p align="center" class="rmqbleu">Modifications enregistr&eacute;es.</p>
  <?php
	}
	else if ($_GET['x'] == 'okadd')
	{
		$pl = ($_GET['nbre'] > 1) ? 's' : '';
		$nbre = ($_GET['nbre'] > 0) ? $_GET['nbre'] : 'Aucune';
		$lien_album = ($site['url_rewriting_actif'] == 1) ? 'g_galerie_'.$_GET['num'].'.htm' : 'index.php?page=galerie&amp;show='.$_GET['num'];
?>
  <p align="center" class="rmqbleu"><?php echo $nbre.' photo'.$pl.' ajout&eacute;e'.$pl.' &agrave; l\'album.'; ?></p>
  <p align="center"><a href="<?php echo $lien_album; ?>">Voir l'album</a></p>
  <?php
	}
	else if ($_GET['x'] == 'oksuppr')
	{
?>
  <p align="center" class="rmqbleu">L'album a bien &eacute;t&eacute; supprim&eacute; ainsi
    que tous les commentaires post&eacute;s dans cet album.</p>
  <?php
	}
	else if ($_GET['x'] == 'oksupprphoto')
	{
?>
  <p align="center" class="rmqbleu">La photo a bien &eacute;t&eacute; supprim&eacute;e
    de l'album ainsi que tous les commentaires post&eacute;s pour cette photo.</p>
  <p align="center" class="petitbleu">Attention, la photo en tant que telle est
    toujours sur le serveur (mais plus dans l'album dont tu viens de la supprimer).<br />
    Pour la supprimer d&eacute;finitivement, contacte le webmaster.</p>
  <p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la
      Galerie</a></p>
  <?php
	}
	else if ($_GET['x'] == 'oksupprlastphoto')
	{
?>
  <p align="center" class="rmqbleu">Tu viens de supprimer la derni&egrave;re
    photo de l'album, il a donc &eacute;t&eacute; supprim&eacute; compl&egrave;tement.</p>
  <p align="center" class="petitbleu">Attention, la photo en tant que telle est
    toujours sur le serveur.<br />
    Pour la supprimer d&eacute;finitivement, contacte le webmaster.</p>
  <p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la
      Galerie</a></p>
  <?php
	}
?>
</div>
<?php
}
else
{
?>
<h1>Gestion des albums photos du portail</h1>
<p align="center"><a href="index.php?page=gestion_galerie">Retour &agrave; la
    Page Gestion des Albums photos</a></p>
<?php
}
?>
</body>
</html>
