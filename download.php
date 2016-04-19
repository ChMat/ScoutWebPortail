<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* download.php v 1.1 - Moteur de téléchargement des fichiers du portail
* Fichiers liés : fichiers.php, file_upload.php, fichiers_gestion.php
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
*	Solution d'un bug possible au téléchargement	
*	Ajout d'un message d'erreur
*/

function readfile_chunked($filename)
{ // Fonction proposée par Rob Funk
  // http://be2.php.net/manual/fr/function.readfile.php
  // Cette fonction lit un fichier par tranches afin de ne pas saturer la mémoire
  // du serveur en cas de gros fichier
	$chunk_size = 1*(1024*1024); // Nombre d'octets lus par bloc
	$buffer = '';
	$handle = fopen($filename, 'rb');
	if ($handle === false)
	{
		return false;
	}
	while (!feof($handle))
	{
		$buffer = fread($handle, $chunk_size);
		echo $buffer;
	}
	return fclose($handle);
} 

include_once('connex.php');
include_once('fonc.php');
if (!ereg("^[a-z0-9]{20}$", $_GET['fichier']))
{ // la clé de téléchargement est incorrecte
	if ($_GET['fichier'] != 'erreur')
	{ // on renvoie vers la structure du site pour afficher l'erreur
		header('Location: index.php?page=download&fichier=erreur');
		exit;
	}
	else
	{ // on est déjà dans la structure du site, on peut afficher l'erreur
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Téléchargement des fichiers</title>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
		}
?>
<h1>Page de t&eacute;l&eacute;chargements</h1> 
<div class="msg">
<p align="center" class="rmq">Le fichier demand&eacute; n'a pas &eacute;t&eacute; 
  trouv&eacute;.</p>
<?php 
		if ($_GET['raison'] == 'synchro') 
		{
?>
<p align="center">Merci de signaler une erreur de synchronisation au t&eacute;l&eacute;chargement au webmaster.</p>
<?php
		}
		else if ($_GET['raison'] == 'droits')
		{
?>
<p align="center">Tu n'es pas autoris&eacute; &agrave; t&eacute;l&eacute;charger ce fichier. </p>
<?php
		}
?>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; 
  la page de t&eacute;l&eacute;chargements</a></p>
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
}
else
{ // la clé de téléchargement est correcte, on lance le téléchargement
	$sql = 'SELECT nomserveur, nomoriginal, type_fichier, public FROM '.PREFIXE_TABLES.'fichiers WHERE cledownload = \''.$_GET['fichier'].'\'';
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{ // le fichier existe dans la db
		$ligne = mysql_fetch_assoc($res);
		if ($user['niveau']['numniveau'] >= $ligne['public'] or $ligne['public'] == 0)
		{ // l'utilisateur a le droit de télécharger le fichier
			if (file_exists('fichiers/'.$ligne['nomserveur']))
			{ // le fichier existe sur le serveur
				// on incrémente le nombre de hits
				$sql = 'UPDATE '.PREFIXE_TABLES.'fichiers SET lu = lu + 1 WHERE cledownload = \''.$_GET['fichier'].'\'';
				send_sql($db, $sql);
				$fichier = $ligne['nomserveur'];
				$NomFichier = basename('fichiers/'.$fichier);
				$NomFichierOnDownload = $ligne['nomoriginal'];
				$taille = filesize('fichiers/'.$fichier);
				header("Content-Type: {$ligne['type_fichier']}");
				header('Content-Transfer-Encoding: binary');
				header('Content-Length: '.$taille);
				header('Content-Disposition: attachment; filename="'.$NomFichierOnDownload.'"');
				header('Expires: 0');
				readfile_chunked('fichiers/'.$NomFichier);
				exit;
			}
			else
			{ // le fichier n'est pas sur le serveur mais bien dans la db -> erreur de synchro
				header('Location: index.php?page=download&fichier=erreur&raison=synchro');
			}
		}
		else
		{ // l'utilisateur n'est pas autorisé à télécharger ce fichier
			header('Location: index.php?page=download&fichier=erreur&raison=droits');
		}
	}
	else
	{ // le fichier n'est pas dans la db
		header('Location: index.php?page=download&fichier=erreur');
	}
}
?>