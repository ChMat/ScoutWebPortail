<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* tallypost.php v 1.1 - Enregistrement d'un article du tally
* Fichier lié à tally.php
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
*	Correction bug enregistrer modifications d'un article
*	Optimisation xhtml
*	Prise en compte paramètre ajout_image à la fonction panneau_mise_en_forme()
*/

include_once('connex.php');
include_once('fonc.php');
if (($_GET['do'] == 'ecrire' or $_GET['do'] == 'modif') and $user['niveau']['numniveau'] > 0 and is_array($sections))
{
	if ($_GET['do'] == 'modif')
	{
		$sql = "SELECT * FROM ".PREFIXE_TABLES."articles WHERE numarticle = '$_GET[numpage]'";
		if ($res = send_sql($db, $sql))
		{
			$donnees = mysql_fetch_assoc($res);
			$titre = $donnees['article_titre'];
			$message = $donnees['article_texte'];
			$numero = $donnees['numarticle'];
			$categorie = $donnees['article_categorie'];
			$section = $donnees['article_section'];
			$afaire = 'savemodif';
		}
	}
	else
	{
		$afaire = 'save';
	}
?>
<?php
if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>R&eacute;diger un article</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />

</head>
<body>
<?php
}
?>
<script language='JavaScript' type="text/JavaScript">
<!--
function validate(form) 
{
	if (form.message.value=="") 
	{
		alert("Sans doute qu'un article à propos du vide aurait un\nintérêt certain, mais pourquoi ne rien écrire?");
		return false; 
	}
	else if (form.titre.value=="")
	{
		alert("Merci d'indiquer un titre.");
		return false; 
	}
	else
	{
		return true; 
	}
}

function msg(page)
{
	window.open(page,'message','width=400,height=450,menubar=0,scrollbars=1,location=0,resize=0');
}

//-->
</script>
<form action="tallypost.php" method="post" name="formulaire" id="formulaire" onsubmit="return validate(this)" class="form_config_site">
  <input type="hidden" name="do" value="<?php echo $afaire; ?>" />
<?php
	if ($_GET['do'] == 'modif') 
	{
		echo '<input type="hidden" name="num" value="'.$numero.'">';
	}
?>
<h2>R&eacute;diger un article</h2>
<fieldset>
<legend>Param&egrave;tres de l'article</legend>
<p>Titre : <input name="titre" type="text" tabindex="1" value="<?php echo $titre; ?>" size="50" maxlength="50" />
</p>
<p>Type d'article : <select name="article_categorie" tabindex="2">
<?php
	$sql = "SELECT * FROM ".PREFIXE_TABLES."articles_categorie ORDER BY nomcategorie ASC";
	$res = send_sql($db, $sql);
	while ($cat = mysql_fetch_assoc($res))
	{
?>
  <option value="<?php echo $cat['numcategorie']; ?>"<?php if (($_GET['do'] == 'modif' and $cat['numcategorie'] == $categorie) or ($cat['numcategorie'] == 1 and $_GET['do'] != 'modif')) {echo ' selected';} ?>><?php echo $cat['nomcategorie']; ?></option>
<?php
	}
?>
</select> Section : 
<select name="article_section" tabindex="3">
<?php
		foreach ($sections as $sec)
		{
?>
  <option value="<?php echo $sec['numsection']; ?>"<?php if (($_GET['do'] == 'modif' and $sec['numsection'] == $section) or ($_GET['do'] == 'ecrire' and $user['numsection'] == $sec['numsection'])) {echo ' selected';} ?>><?php echo $sec['nomsectionpt']; ?></option>
<?php
		}
?>
</select></p>
</fieldset>
<?php panneau_mise_en_forme('message', true, true); ?>
<textarea name="message" cols="70" rows="12" id="message" style="width:95%;" tabindex="4" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $message; ?></textarea>
<p align="center">
<input name="envoi" type="submit" tabindex="5" value="Envoyer" />
<input name="reset" type="reset" tabindex="6" value="Recommencer" />
</p>
<?php panneau_smileys('message'); ?>
<div class="instructions">
<p class="rmq">Ins&eacute;rer des images :</p>
<p>Pour ins&eacute;rer des images dans ton Tally, il te suffit d'indiquer 
l'adresse de l'image après le signe = dans la balise <code>[img=]</code></p>
<p>Tu peux ins&eacute;rer une image du portail (clique sur le bouton <img src="templates/default/images/imgdroite.png" width="18" height="12" /><em> Choisir 
une image</em>) ou une image qui se trouve sur un autre site (attention 
aux droits d'auteur).</p>
<p>Exemple : <code>[img=http://www.monsite.com/images/camp2002/maphoto.jpg]</code> 
ou <code>[img=img/activites/camp2002/pt/camp02-231.jpg]</code></p>
<p>Image align&eacute;e &agrave; gauche du texte :  <code>[imgleft=cheminimg]</code> ou &agrave; droite : <code>[imgright=cheminimg]</code></p>
</div>
</form>
<?php
}
else if ($_POST['do'] == 'save')
{
	$titre = htmlentities($_POST['titre'], ENT_QUOTES);
	$message = htmlentities($_POST['message'], ENT_QUOTES);
	$article_categorie = htmlentities($_POST['article_categorie'], ENT_QUOTES);
	$article_section = htmlentities($_POST['article_section'], ENT_QUOTES);
	if ($user != 0 and !empty($titre))
	{
		$sql = "INSERT INTO ".PREFIXE_TABLES."articles (article_categorie, article_section, article_auteur, article_titre, article_texte, article_datecreation, article_banni) 
		values 
		('$article_categorie', '$_POST[article_section]', '$user[num]', '$titre', '$message', now(), '0')";
		send_sql($db, $sql);
		$sql = "SELECT numarticle FROM ".PREFIXE_TABLES."articles WHERE article_auteur = '$user[num]' ORDER BY article_datecreation DESC LIMIT 1";
		$res = send_sql($db, $sql);
		$aa = mysql_fetch_assoc($res);	
		$numero = $aa['numarticle'];
		log_this("Creation article Tally ($numero)", "tally");
		header('Location: index.php?page=tally&numero='.$numero);
	}
	else
	{
		header('Location: index.php?page=tally&do=erreur');
		exit;
	}
}
else if ($_POST['do'] == 'savemodif')
{
	$auteur = untruc(PREFIXE_TABLES.'articles', 'article_auteur', 'numarticle', $_POST['num']);
	$titre = htmlentities($_POST['titre'], ENT_QUOTES);
	$message = htmlentities($_POST['message'], ENT_QUOTES);
	if ((($user != 0 and $auteur == $user['num']) or $user['niveau']['numniveau'] > 2) and !empty($titre))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."articles SET article_categorie = '$_POST[article_categorie]', article_titre = '$titre', 
		article_section = '$_POST[article_section]', article_texte = '$message', article_datecreation = now(), article_modifby = '$user[num]' 
		WHERE numarticle = '$_POST[num]'";
		send_sql($db, $sql);
		log_this('Modification article Tally ('.$_POST['num'].')', 'tally');
		header('Location: index.php?page=tally&numero='.$num);
	}
	else
	{
		header('Location: index.php?page=tally&do=erreur');
		exit;
	}
}
else
{
?>
<div class="msg">
  <p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<?php
	if (!is_array($sections))
	{
?>
  <p align="center">Pour pouvoir poster un article dans le tally, au moins une section doit &ecirc;tre cr&eacute;&eacute;e.</p>
<?php
	}
?>
</div>
<?php
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>