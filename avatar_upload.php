<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* avatar_upload.php v 1.1 - Enregistrement ou suppression de l'avatar d'un membre du portail
* Dans une prochaine version, ce script pourra redimensionner les avatars
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
* 17 janvier 2005 : ChMat
*	Correction suppression ancien avatar quand dépose avatar de même format
*	Ajout suppression avatars uploadés ne respectant pas le format demandé
* 8 février 2005 : ChMat
*	Ajout gestion des avatars des membres par le webmaster
*	Prise en charge paramètres de taille et de poids définis par le webmaster
* Modifications v 1.1.1
*	Prise en compte des erreurs de téléchargement de l'avatar
*/

require_once('connex.php');
require_once('fonc.php');

// configuration du script
$dossieravatars = 'img/photosmembres/avatars/';
$taille_maxi_avatar = (is_numeric($site['avatar_max_filesize'])) ? $site['avatar_max_filesize'] : 10240; // en octets
$w_maxi_avatar = (is_numeric($site['avatar_max_width'])) ? $site['avatar_max_width'] : 100; // en octets
$h_maxi_avatar = (is_numeric($site['avatar_max_height'])) ? $site['avatar_max_height'] : 130; // en octets

if ($user['niveau']['numniveau'] < 1)
{
	include('404.php');
}
else
{
	if ($_GET['do'] == 'delete')
	{
		if ($user['niveau']['numniveau'] == 5 and is_numeric($_GET['mb']))
		{ // le webmaster supprime l'avatar d'un membre du site
			$avatar_membre = untruc(PREFIXE_TABLES.'auteurs', 'avatar', 'num', $_GET['mb']);
			if (!empty($avatar_membre))
			{
				@unlink($dossieravatars.$avatar_membre);
				$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET avatar = \'\' WHERE num = \''.$_GET['mb'].'\'';
				send_sql($db, $sql);
				log_this('Suppression avatar membre '.$_GET['mb'], 'avatar_upload');
			}
			header('Location: index.php?page=modifmembresite&num='.$_GET['mb']);
		}
		else
		{ // l'utilisateur supprime son avatar
			if (!empty($user['avatar']))
			{
				@unlink($dossieravatars.$user['avatar']);
				$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET avatar = \'\' WHERE num = \''.$user['num'].'\'';
				send_sql($db, $sql);
				log_this('Suppression avatar', 'avatar_upload');
			}
			header('Location: index.php?page=modifprofil');
		}
	}
	if ($_POST['do'] == 'send')
	{ // l'utilisateur ou le webmaster uploade un nouvel avatar
		// si une erreur se produit, le message dans avatar_upload sera différent si user est webmaster ou pas.
		$retour_page = ($user['niveau']['numniveau'] == 5 and is_numeric($_POST['mb'])) ? '&num='.$_POST['mb'] : '';
		if ($_FILES['userfile'] != 'none' && $_FILES['userfile']['size'] != 0)
		{
			if (eregi("jpg$|gif$|png$", $_FILES['userfile']['name'], $regs))
			{
				// on génère le nom de l'avatar sur le serveur (numéro utilisateur).extension
				$newname = ($user['niveau']['numniveau'] == 5 and is_numeric($_POST['mb'])) ? $_POST['mb'].'.'.$regs[0] : $user['num'].'.'.$regs[0];
				if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $dossieravatars.$newname))
				{
					// on récupère le numéro du membre à qui sera l'avatar
					$nummb_avatar = ($user['niveau']['numniveau'] == 5 and is_numeric($_POST['mb'])) ? $_POST['mb'] : $user['num'];
					// on détermine l'avatar actuel du membre concerné
					$avatar_membre = ($user['niveau']['numniveau'] == 5 and is_numeric($_POST['mb'])) ? untruc(PREFIXE_TABLES.'auteurs', 'avatar', 'num', $_GET['mb']) : $user['avatar'];
					$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET avatar = \'\' WHERE num = \''.$nummb_avatar.'\'';
					send_sql($db, $sql);
					if (filesize($dossieravatars.$newname) > $taille_maxi_avatar)
					{
						log_this('Avatar trop lourd', 'avatar_upload');
						@unlink($dossieravatars.$newname);
						header('Location: index.php?page=avatar_upload&do=erreur&msg=poids'.$retour_page);
					}
					else
					{
						$taille = @getimagesize($dossieravatars.$newname);
						// [0] = largeur, [1] = hauteur
						if ($taille[0] > $w_maxi_avatar or $taille[1] > $h_maxi_avatar)
						{
							log_this('Avatar trop grand', 'avatar_upload');
							@unlink($dossieravatars.$newname);
							header('Location: index.php?page=avatar_upload&do=erreur&msg=taille'.$retour_page);
						}
						else
						{
							if (!empty($avatar_membre) and $newname != $avatar_membre)
							{ // suppression de l'ancien avatar s'il existe et qu'il porte un nom différent du nouveau
								@unlink($dossieravatars.$avatar_membre);
							}
							$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET avatar = \''.$newname.'\', majprofildone = \'1\', majprofildate = now() WHERE num = \''.$nummb_avatar.'\'';
							send_sql($db, $sql);
							log_this('Nouvel avatar', 'avatar_upload');
							if ($user['niveau']['numniveau'] == 5 and is_numeric($_POST['mb']))
							{
								header('Location: index.php?page=modifmembresite&num='.$_POST['mb']);
							}
							else
							{
								header('Location: index.php?page=avatar_upload&msg=ok');
							}
						}
					}
				}
				else
				{
					log_this('Ecriture impossible dans '.$dossieravatars, 'avatar_upload');
					header('Location: index.php?page=avatar_upload&do=erreur&msg=droits'.$retour_page);
				}
			}
			else
			{
				log_this('Erreur de format de fichier', 'avatar_upload');
				header('Location: index.php?page=avatar_upload&do=erreur&msg=format'.$retour_page);
			}
		}
		else
		{
			header('Location: index.php?page=avatar_upload&do=erreur&msg='.$_FILES['userfile']['error'].$retour_page);
		}
	} // fin if do==send
	else if ($_GET['do'] == 'erreur' or empty($_GET['do']))
	{
?>
<?php
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion avatar - Erreur</title>
<link href="templates/default/style.css" type="text/css" rel="stylesheet" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
		}
?>
<h1>Modifier l'avatar</h1>
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
<p align="center"><span class="rmq">Impossible d'enregistrer l'avatar</span><br />
Le webmaster devrait v&eacute;rifier les droits d'&eacute;criture du dossier 
<?php echo $dossieravatars; ?>.</p>
<?php
		}
		else if ($_GET['msg'] == 'format')
		{
?>
<p align="center" class="rmq">Le fichier n'a pas le bon format. Il doit &ecirc;tre 
  au format GIF, JPG ou PNG. </p>
<?php
		}
		else if ($_GET['msg'] == 'poids' or $_GET['msg'] == '1' or $_GET['msg'] == '2')
		{
?>
<p align="center" class="rmq">Le fichier ne peut pas faire plus de <?php echo taille_fichier($taille_maxi_avatar); ?>.</p>
<?php
		}
		else if ($_GET['msg'] == 'taille')
		{
?>
<p align="center" class="rmq">Le fichier ne peut pas faire plus de <?php echo $w_maxi_avatar.' x '.$h_maxi_avatar; ?> pixels.</p>
<?php
		}
		else if ($_GET['msg'] == 'ok')
		{
?>
<p align="center">Ton nouvel avatar a bien &eacute;t&eacute; enregistr&eacute;.</p>
<p align="center"><?php echo show_avatar($user['num']); ?></p>
<p class="petitbleu">Il se peut que l'ancien avatar apparaisse encore. 
  Actualise ta page et le nouvel avatar devrait appara&icirc;tre.</p>

<?php
		}
		else
		{
?>
<p align="center" class="rmq">Une erreur s'est produite.</p>
<?php
		}
		// on génère le lien de retour selon la valeur de $_GET['num']
		if (isset($_GET['num']))
		{
?>
<p align="center"> <a href="<?php echo 'index.php?page=modifmembresite&amp;num='.$_GET['num']; ?>">Retour au profil du membre</a></p>
<?php
		}
		else
		{
?>
<p align="center"> <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'modifprofil.htm' : 'index.php?page=modifprofil'; ?>">Retour 
  &agrave; mon profil</a></p>
<?php
		}
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