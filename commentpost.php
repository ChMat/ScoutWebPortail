<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* commentpost.php v 1.1 - Gestion de l'enregistrement des commentaires postés par les membres du portail
* Fichier lié : galerie.php
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
*	Externalisation du texte des mails
*/

include_once('connex.php');
include_once('fonc.php');

if ($user['niveau']['numniveau'] < 1)
{
	header('Location: index.php?page=404');
	exit;
}

if (is_numeric($_POST['g']) and is_numeric($_POST['p']))
{
	$g = $_POST['g'];
	$p = $_POST['p'];
	$sql = "SELECT numgalerie FROM ".PREFIXE_TABLES."galerie, ".PREFIXE_TABLES."albums WHERE numgalerie = '$g' AND numalbum = '$g' AND posphoto = '$p'";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) == 1)
		{	
			if (!empty($_POST['commentaire']))
			{
				$commentaire = htmlentities($_POST['commentaire'], ENT_QUOTES); 
				$sql = "INSERT INTO ".PREFIXE_TABLES."commentaires (album, photo, auteur, commentaire, datecreation) values (".$g.", ".$p.", ".$user['num'].", '".$commentaire."', now())";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."albums SET nbcomment = nbcomment + 1 WHERE numalbum = '".$g."' AND posphoto = '".$p."'";
				send_sql($db, $sql);
				header('Location: index.php?page=galerie&show='.$g.'&photo='.$p);
				exit;
			}
			else
			{
				header('Location: index.php?page=galerie&show='.$g.'&photo='.$p);
				exit;
			}
		}
		else
		{
			header('Location: index.php?action=galerie&show=erreur&galerie='.$g.'&photo='.$p);
		}
	} // res = send_sql
	else
	{
		header('Location: index.php?page=404');
		exit;
	}
}
else if ($_GET['do'] == 'mod' and is_numeric($_GET['n']) and $user['niveau']['numniveau'] > 2)
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Enregistrement des commentaires</title>
</head>
<body>
<?php
	}
?>
<h1>Supprimer un commentaire de la galerie photo</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'galerie.htm' : 'index.php?page=galerie'; ?>">Retour &agrave; la Galerie</a></p>
<?php
	if ($_GET['s'] == 2)
	{
		$sql = "SELECT album, photo FROM ".PREFIXE_TABLES."commentaires WHERE numcommentaire = '".$_GET['n']."'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$ligne = mysql_fetch_assoc($res);
				$sql = "UPDATE ".PREFIXE_TABLES."albums SET nbcomment = nbcomment - 1 WHERE numalbum = '".$ligne['album']."' AND posphoto = '".$ligne['photo']."'";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."commentaires SET commentairebanni = '1' WHERE numcommentaire = '".$_GET['n']."'";
				send_sql($db, $sql);
				log_this('Modération commentaire photo '.$_GET['n'].' dans la galerie', 'galerie');
?>
<div class="msg">
<p align="center" class="rmqbleu">Le commentaire a &eacute;t&eacute; supprim&eacute;.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Aucun commentaire ne correspond aux donn&eacute;es fournies.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Erreur de connexion &agrave; la base de donn&eacute;es.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmqbleu">Es-tu certain de vouloir supprimer ce commentaire ?</p>
<p align="center"><a href="index.php?page=commentpost&amp;do=mod&amp;s=2&amp;n=<?php echo $_GET['n'].'&amp;r='.urlencode($_GET['r']); ?>" class="bouton">Oui</a> <a href="<?php echo urldecode($_GET['r']); ?>" class="bouton">Non</a></p>
</div>
<?php
	}
}
else if ($_GET['do'] == 'unmod' and is_numeric($_GET['n']) and $user['niveau']['numniveau'] == 5)
{
?>
<h1>R&eacute;activer un commentaire de la galerie photo</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'galerie.htm' : 'index.php?page=galerie'; ?>">Retour &agrave; la Galerie</a></p>
<?php
	if ($_GET['s'] == 2)
	{
		$sql = "SELECT album, photo FROM ".PREFIXE_TABLES."commentaires WHERE numcommentaire = '".$_GET['n']."'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$ligne = mysql_fetch_assoc($res);
				$sql = "UPDATE ".PREFIXE_TABLES."albums SET nbcomment = nbcomment + 1 WHERE numalbum = '".$ligne['album']."' AND posphoto = '".$ligne['photo']."'";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."commentaires SET commentairebanni = '0' WHERE numcommentaire = '".$_GET['n']."'";
				send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">Le commentaire a &eacute;t&eacute; r&eacute;activ&eacute;.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Aucun commentaire ne correspond aux donn&eacute;es fournies.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Erreur de connexion &agrave; la base de donn&eacute;es.</p>
<p align="center"><a href="<?php echo urldecode($_GET['r']); ?>">Retour &agrave; la Photo</a></p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmqbleu">Es-tu certain de vouloir r&eacute;activer ce commentaire ?</p>
<p align="center"><a href="index.php?page=commentpost&amp;do=unmod&amp;s=2&amp;n=<?php echo $_GET['n'].'&amp;r='.urlencode($_GET['r']); ?>" class="bouton">Oui</a> <a href="<?php echo urldecode($_GET['r']); ?>" class="bouton">Non</a></p>
</div>
<?php
	}
}
else
{
	header('Location: index.php?page=galerie&show=erreur&tada');
	exit;
}
?>
</body>
</html>
