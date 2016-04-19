<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fichiers_gestion.php v 1.1 - Upload d'un fichier sur le portail pour la page de téléchargements
* Fichiers liés : fichiers.php, file_upload.php, download.php
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
*	Gestion des rubriques de téléchargement
*	Mise en place des fichiers vedettes
*/

include_once('connex.php');
include_once('fonc.php');

// paramètres du script
$taille_maxi_fichier = (is_numeric($site['upload_max_filesize'])) ? $site['upload_max_filesize'] : 1048576; // en octets

if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
	exit;
}
else
{
	if ($_GET['do'] == 'rubriques' or (empty($_GET['do']) and empty($_POST['do'])))
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Envoi de fichier</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des rubriques de t&eacute;l&eacute;chargement</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page de T&eacute;l&eacute;chargements</a></p>
<?php
		if ($_GET['msg'] == 'erreur')
		{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite !</p>
<p align="center">Les donn&eacute;es sont incorrectes ou insuffisantes.</p>
</div>
<?php
		}
		else if ($_GET['msg'] == 'oknew')
		{
?>
<div class="msg">
<p align="center">La rubrique a bien &eacute;t&eacute; cr&eacute;&eacute;e.</p>
</div>
<?php
		}
		else if ($_GET['msg'] == 'okmodif')
		{
?>
<div class="msg">
<p align="center">Modifications enregistr&eacute;es.</p>
</div>
<?php
		}
		else if ($_GET['msg'] == 'oksuppr')
		{
?>
<div class="msg">
<p align="center">La rubrique a bien &eacute;t&eacute; supprim&eacute;e.</p>
<?php
			if ($_GET['erreur'] == 1)
			{
?>
<p align="center" class="rmq">Certains fichiers n'ont pas pu &ecirc;tre supprim&eacute;s du serveur (voir log pour la liste).</p>
<?php
			}
?>
</div>
<?php
		}
?>
<?php
		$sql = 'SELECT cat_id, cat_titre, cat_description FROM '.PREFIXE_TABLES.'fichiers_cat ORDER BY cat_titre';
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{ // on affiche les rubriques existantes
?>
<div class="form_config_site">
<h2>Rubriques existantes</h2>
<dl>
<?php
			while ($rubrique = mysql_fetch_assoc($res))
			{
?><dt><a href="index.php?page=fichiers_gestion&amp;do=modif_cat&amp;cat=<?php echo $rubrique['cat_id']; ?>" title="Modifier cette rubrique"><img src="templates/default/images/autres.png" alt="Modifier" width="12" height="12" border="0" /></a>
<?php
				if ($user['niveau']['numniveau'] == 5)
				{ // seul le webmaster peut supprimer une rubrique
				  // En effet, il est le seul à voir le contenu complet d'une rubrique
				  // Certains fichiers d'une rubriques peuvent être cachés à un utilisateur
?>
<a href="index.php?page=fichiers_gestion&amp;do=suppr_cat&amp;cat=<?php echo $rubrique['cat_id']; ?>" title="Supprimer cette rubrique"><img src="templates/default/images/supprimer.png" alt="Supprimer" width="12" height="12" border="0" /></a>
<?php
				}
				echo $rubrique['cat_titre']; 
?></dt>
<dd><?php echo makehtml($rubrique['cat_description']); ?></dd>
<?php
			}
?></dl>
</div>
<?php
		}
		else
		{ // Il n'y a aucune rubrique sur le site
?>
<div class="msg">
<p class="rmq" align="center">Il n'y a aucune rubrique pour le moment.</p>
<p align="center">Pour permettre le t&eacute;l&eacute;chargement de fichiers, cr&eacute;e au moins une rubrique.</p>
</div>
<?php
		}
		// on affiche le formulaire de création d'une rubrique
?>

<form action="fichiers_gestion.php" method="post" name="new_cat" id="new_cat" onsubmit="return check_new_cat(this);" class="form_config_site">
  <input type="hidden" name="do" value="new_cat" />
<script language="JavaScript" type="text/JavaScript">
<!--
function check_new_cat(form)
{
	if (form.cat_titre.value == "")
	{
		alert("Merci d'indiquer l'intitulé de la rubrique.");
		return false;
	}
	else
	{
		getElement('envoi').disabled = true;
		getElement('envoi').value = 'Patience...';
		return true;
	}
}
//-->
</script>
<h2>Cr&eacute;er une nouvelle rubrique</h2>
<p>Intitul&eacute; de la rubrique* : 
  <input type="text" name="cat_titre" tabindex="1" maxlength="255" style="width: 250px;" /> 
</p>
<p>Description de la rubrique :</p>
<?php panneau_mise_en_forme('cat_description', true); ?>
<textarea name="cat_description" id="cat_description" cols="50" rows="3" tabindex="2" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Cr&eacute;er la rubrique" tabindex="3" />
<p class="petitbleu">* Case obligatoire</p>
</form>
<?php
	}
	else if ($_GET['do'] == 'upload')
	{
?>
<h1>D&eacute;poser un fichier sur le portail</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page de T&eacute;l&eacute;chargements</a></p>
<form action="file_upload.php" method="post" enctype="multipart/form-data" name="envoi_fichier" id="envoi_fichier" onsubmit="return check_envoi_fichier(this);" class="form_config_site">
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $taille_maxi_fichier; ?>" />
  <input type="hidden" name="do" value="newfichier" />
<p>Pour d&eacute;poser tes fichiers sur le portail, compl&egrave;te le formulaire 
  ci-dessous.</p>
<script language="JavaScript" type="text/JavaScript">
<!--
function check_envoi_fichier(form)
{
	if (form.userfile.value == "")
	{
		alert("Merci de choisir un fichier.");
		return false;
	}
	else
	{
		if (form.titre_fichier.value == '')
		{
			alert("Merci d'indiquer l'intitulé du fichier.");
			return false;
		}
		else if (form.cat_id.value == '')
		{
			alert("Merci de sélectionner une rubrique où placer le fichier.");
			return false;
		}
		else if (form.cat_id.value == 'new' && form.new_cat_titre.value == '')
		{
			getElement('new_cat_titre').style.backgroundColor = '#FFFFCC';
			alert("Merci d'indiquer le nom de la nouvelle rubrique.");
			return false;
		}
		else
		{
			getElement('envoi').disabled = true;
			getElement('envoi').value = 'Patience...';
			return true;
		}
	}
}
//-->
</script>
<h2>Choisis le fichier &agrave; envoyer*</h2>
<p><input type="file" name="userfile" size="30" tabindex="1" /> 
Maximum <?php echo taille_fichier($taille_maxi_fichier); ?> par fichier 
<?php
		if ($user['niveau']['numniveau'] == 5) 
		{
?><a href="index.php?page=config_site&amp;categorie=fichiers" title="Modifier la taille maximale">
  <img src="templates/default/images/autres.png" border="0" alt="Modifier la taille maximale" align="middle" /></a>
<?php
		}
?></p>
<h2>Param&egrave;tres du fichier </h2> 
<p>Intitul&eacute; du fichier* :
    <input type="text" name="titre_fichier" tabindex="2" maxlength="100" style="width: 250px;" />
</p>
<p>Description du fichier :</p>
<?php panneau_mise_en_forme('description_fichier', true); ?>
<textarea name="description_fichier" id="description_fichier" cols="50" rows="3" tabindex="3" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
<p>Niveau d'acc&egrave;s* : 
  <select name="public" id="public" tabindex="4">
<option value="0" selected="selected">Acc&egrave;s public</option>
<option value="1">Membre du portail</option>
<option value="2">Membre de l'Unit&eacute;</option>
<option value="3">Animateur de Section (AnS)</option>
<option value="4">Animateur d'Unit&eacute; (AnU)</option>
<option value="5">Webmaster</option>
</select>
</p>
<p>Seules les personnes ayant un statut sup&eacute;rieur ou &eacute;gal 
&agrave; ce niveau pourront acc&eacute;der au fichier.</p>
<p>
  <input name="vedette" type="checkbox" id="vedette" tabindex="5" onchange="if (this.checked && getElement('public').value > 0) {alert('Seuls les fichiers en accès public peuvent avoir la vedette !'); this.checked = false;}" value="1" />
  <label for="vedette">Fichier vedette</label> 
  - Les fichiers vedettes sont mis en &eacute;vidence sur la page de t&eacute;l&eacute;chargements.</p>
<fieldset>
<legend>Choix de la rubrique</legend>
Rubrique* : 
  <select name="cat_id" id="cat_id" tabindex="6" onchange="if (this.value == 'new') {getElement('new_cat_titre').style.display = 'inline'; getElement('panneau_nouvelle_rubrique').style.display = 'inline'; getElement('new_cat_description').disabled = false;} else {getElement('new_cat_titre').style.display = 'none'; getElement('panneau_nouvelle_rubrique').style.display = 'none'; getElement('new_cat_description').disabled = true;}">
    <option value="">Choisis une rubrique</option>
<?php
		$sql = 'SELECT cat_id, cat_titre FROM '.PREFIXE_TABLES.'fichiers_cat ORDER BY cat_titre';
		$res = send_sql($db, $sql);
		while ($rubrique = mysql_fetch_assoc($res))
		{
			echo '<option value="'.$rubrique['cat_id'].'">'.$rubrique['cat_titre'].'</option>';
		}
?>
    <option value="new">Nouvelle rubrique &gt;&gt;&gt;</option>
  </select>
  <input type="text" name="new_cat_titre" id="new_cat_titre" maxlength="255" title="Indique ici le nom de la nouvelle rubrique que tu souhaites créer" style="display:none;" />
<div id="panneau_nouvelle_rubrique" style="display:none;">
<?php panneau_mise_en_forme('new_cat_description', true); ?>
Description de la nouvelle rubrique :<br />
<textarea name="new_cat_description" cols="50" rows="3" disabled="disabled" id="new_cat_description" tabindex="7" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
</div>
<noscript>
  <p align="center" class="petitbleu">Pour cr&eacute;er une nouvelle rubrique, merci d'activer le javascript.</p>
</noscript>
</fieldset>
<p>&nbsp;</p>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Envoyer" tabindex="8" />
<p class="petitbleu">* Cases obligatoires<br />
  Les fichiers doivent avoir un rapport direct 
avec le fonctionnement de l'unit&eacute;.<br />
Avant de les d&eacute;poser sur le portail, merci de t'assurer qu'ils 
ne sont pas infect&eacute;s par un virus quelconque.</p>
</form>
<div class="instructions">
<h2>Euh, &ccedil;a sert &agrave; quoi m'sieur ?</h2>
<p> Le principe est tr&egrave;s simple, tu veux partager un fichier ou le garder 
  &agrave; disposition et tu veux qu'il ne se perde pas. C'est pour &ccedil;a 
    que cette section est l&agrave;.</p>
<h2>Comment on fait pour mettre un fichier sur le portail 
  ?</h2>
<p>  Rien de plus simple !<br />
    * <em>Choisis le fichier &agrave; envoyer</em> : clique sur le bouton Parcourir,
    le contenu de ton ordinateur s'affiche. S&eacute;lectionne le fichier que tu 
    souhaites ajouter &agrave; la page de t&eacute;l&eacute;chargements.<br />
    * <em>Intitul&eacute; du fichier</em> : L'intitul&eacute; du fichier
     est son titre par exemple.<br />
    * <em>Description du fichier</em> : Ici tu peux donner des d&eacute;tails
    sur le contenu du fichier.<br />
    * <em>Niveau d'acc&egrave;s</em> : Tu peux emp&ecirc;cher certaines personnes 
    d'acc&eacute;der au fichier en choisissant qui peut y acc&eacute;der.<br />
    * <em>Rubrique</em> : Pour simplifier les choses, les fichiers peuvent &ecirc;tre
    plac&eacute;s dans des rubriques, choisis celle qui te convient ou cr&eacute;e une nouvelle
    rubrique en indiquant son nom et &eacute;ventuellement une courte description de
    celle-ci.</p>
</div>
  <?php
	} // fin if $do == upload
	else if ($_GET['do'] == 'modif' and ereg("[a-z0-9]{20}", $_GET['f']))
	{
		$sql = 'SELECT cat_id, nomoriginal, titre_fichier, description_fichier, public, vedette, lu FROM '.PREFIXE_TABLES.'fichiers WHERE cledownload = \''.$_GET['f'].'\'';
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$fichier = mysql_fetch_assoc($res);
?>
<h1>Modifier les informations d'un fichier sur le portail</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page de T&eacute;l&eacute;chargements</a></p>
<?php
			if ($_GET['erreur'] == 1)
			{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite !</p>
<p align="center">Les donn&eacute;es sont incorrectes ou insuffisantes.</p>
</div>
<?php
			}
?>
<form action="fichiers_gestion.php" method="post" name="modif_fichier" id="modif_fichier" onsubmit="return check_modif_fichier(this);" class="form_config_site">
  <input type="hidden" name="do" value="save_modif" />
<script language="JavaScript" type="text/JavaScript">
<!--
function check_modif_fichier(form)
{
	if (form.titre_fichier.value == "")
	{
		alert("Merci d'indiquer l'intitulé du fichier.");
		return false;
	}
	else
	{
		getElement('envoi').disabled = true;
		getElement('envoi').value = 'Patience...';
		return true;
	}
}
//-->
</script>
<h2>Param&egrave;tres du fichier -  <?php echo $fichier['nomoriginal']; ?></h2> 
  <input type="hidden" name="f" value="<?php echo $_GET['f']; ?>" />
<p>Intitul&eacute; du fichier* : 
  <input type="text" name="titre_fichier" tabindex="1" maxlength="100" style="width: 250px;" value="<?php echo $fichier['titre_fichier']; ?>" />
  <label for="public"></label>
</p>
<p>Description du fichier :</p>
<?php panneau_mise_en_forme('description_fichier', true); ?>
<textarea name="description_fichier" id="description_fichier" cols="50" rows="3" tabindex="2" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $fichier['description_fichier']; ?></textarea>
<p>Niveau d'acc&egrave;s* : 
  <select name="public" tabindex="3">
<option value="0"<?php echo ($fichier['public'] == 0) ? ' selected="selected"' : ''; ?>>Acc&egrave;s public</option>
<option value="1"<?php echo ($fichier['public'] == 1) ? ' selected="selected"' : ''; ?>>Membre du portail</option>
<option value="2"<?php echo ($fichier['public'] == 2) ? ' selected="selected"' : ''; ?>>Membre de l'Unit&eacute;</option>
<option value="3"<?php echo ($fichier['public'] == 3) ? ' selected="selected"' : ''; ?>>Animateurs de Section (AnS)</option>
<option value="4"<?php echo ($fichier['public'] == 4) ? ' selected="selected"' : ''; ?>>Animateurs d'Unit&eacute; (AnU)</option>
<option value="5"<?php echo ($fichier['public'] == 5) ? ' selected="selected"' : ''; ?>>Webmaster</option>
</select>
</p>
<p>Seules les personnes ayant un statut sup&eacute;rieur ou &eacute;gal 
&agrave; ce niveau pourront acc&eacute;der au fichier.</p>
<p>
  <input name="vedette" type="checkbox" id="vedette" tabindex="4" value="1"<?php echo ($fichier['vedette'] == 1) ? ' checked="checked"' : ''; ?> />
  <label for="vedette">Fichier vedette</label> 
  - Les fichiers vedettes sont mis en &eacute;vidence sur la page de t&eacute;l&eacute;chargements.</p>
<p>Rubrique* :
  <select name="cat_id" id="cat_id" tabindex="5">
<?php
			$sql = 'SELECT cat_id, cat_titre FROM '.PREFIXE_TABLES.'fichiers_cat ORDER BY cat_titre';
			$res = send_sql($db, $sql);
			while ($rubrique = mysql_fetch_assoc($res))
			{
				$sel_rub = ($rubrique['cat_id'] == $fichier['cat_id']) ? ' selected="selected"' : '';
				echo '<option value="'.$rubrique['cat_id'].'"'.$sel_rub.'>'.$rubrique['cat_titre'].'</option>';
			}
?>
  </select>
  <a href="index.php?page=fichiers_gestion&do=rubriques" title="Modifier les rubriques"><img src="templates/default/images/autres.png" alt="Modifier les rubriques" width="12" height="12" border="0" align="middle" /></a></p>
<p>Nombre de t&eacute;l&eacute;chargements : <input name="lu" type="text" id="lu" tabindex="6" value="<?php echo $fichier['lu']; ?>" size="5" />
</p>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Enregistrer les modifications" tabindex="7" />
<p align="center" class="petitbleu">* Cases obligatoires</p>
</form>
  <?php
		}
		else
		{ // le fichier demandé n'existe pas
?>
<h1>Gestion des fichiers &agrave; t&eacute;l&eacute;charger</h1>
<div align="center" class="msg"> 
  <p class="rmq">Ce fichier n'existe pas !</p>
  <p><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page des t&eacute;l&eacute;chargements</a></p>
</div>
<?php
		}
	} // fin if $do == modif
	else if ($_GET['do'] == 'delete' and !empty($_GET['f']))
	{	
?>
<h1>Gestion des fichiers &agrave; t&eacute;l&eacute;charger</h1>
<div align="center" class="action"> 
  <p class="rmqbleu">Es-tu certain de vouloir supprimer ce fichier ?</p>
  <p><a href="file_upload.php?do=delete&f=<?php echo $_GET['f']; ?>" class="bouton" tabindex="1">OUI</a> 
	<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>" class="bouton" tabindex="2">NON</a></p>
  <p><img src="img/smileys/007.gif" alt="" width="15" height="15" /> Il n'est 
    pas possible d'annuler la suppression du fichier.</p>
</div>
<?php
	}
	else if ($_POST['do'] == 'save_modif' and ereg("[a-z0-9]{20}", $_POST['f']))
	{
		if (!empty($_POST['titre_fichier']) and is_numeric($_POST['public']) and is_numeric($_POST['cat_id']))
		{
			$titre_fichier = htmlentities($_POST['titre_fichier'], ENT_QUOTES);
			$description_fichier = htmlentities($_POST['description_fichier'], ENT_QUOTES);
			$public = $_POST['public'];
			$vedette = (is_numeric($_POST['vedette'])) ? $_POST['vedette'] : 0;
			$cat_id = $_POST['cat_id'];
			$lu = (is_numeric($_POST['lu'])) ? $_POST['lu'] : 0;
			$sql = "UPDATE ".PREFIXE_TABLES."fichiers SET titre_fichier = '$titre_fichier', description_fichier = '$description_fichier', cat_id = '$cat_id', public = '$public', vedette = '$vedette', lu = '$lu' WHERE cledownload = '$_POST[f]'";
			send_sql($db, $sql);
			// log de l'action
			log_this('Modification fichier '.$titre_fichier, 'fichiers', true);
			header("Location: index.php?page=fichiers_gestion&do=okmodif");
			exit;
		}
		else
		{
			header("Location: index.php?page=fichiers_gestion&do=modif&f=$_POST[f]&erreur=1");
			exit;
		}
	}
	else if ($_GET['do'] == 'okmodif')
	{
?>
<h1>Gestion des fichiers &agrave; t&eacute;l&eacute;charger</h1>
<div align="center" class="msg"> 
  <p class="rmqbleu">Les modifications ont &eacute;t&eacute; sauvegard&eacute;es</p>
  <p><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page des t&eacute;l&eacute;chargements</a></p>
</div>
<?php
	}
	else if ($_POST['do'] == 'new_cat')
	{ // création d'une rubrique
		if (!empty($_POST['cat_titre']))
		{
			$cat_titre = htmlentities($_POST['cat_titre'], ENT_QUOTES);
			$cat_description = htmlentities($_POST['cat_description'], ENT_QUOTES);
			$sql = "INSERT INTO ".PREFIXE_TABLES."fichiers_cat (cat_titre, cat_description) values ('$cat_titre', '$cat_description')";
			send_sql($db, $sql);
			// log de l'action
			log_this('Nouvelle rubrique : '.$cat_titre, 'fichiers', true);
			header("Location: index.php?page=fichiers_gestion&do=rubriques&msg=oknew");
			exit;
		}
		else
		{ // le titre de la rubrique n'est pas défini
			header("Location: index.php?page=fichiers_gestion&do=rubriques&msg=erreur");
			exit;
		}
	}
	else if ($_GET['do'] == 'modif_cat' and is_numeric($_GET['cat']))
	{ // formulaire pour modifier une rubrique
?>
<h1>Gestion des rubriques de t&eacute;l&eacute;chargement </h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page de T&eacute;l&eacute;chargements</a></p>
<?php
		$sql = "SELECT cat_titre, cat_description FROM ".PREFIXE_TABLES."fichiers_cat WHERE cat_id = '$_GET[cat]'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // la rubrique existe
			$rubrique = mysql_fetch_assoc($res);
?>
<form action="fichiers_gestion.php" method="post" name="modif_cat" id="modif_cat" onsubmit="return check_cat(this);" class="form_config_site">
  <input type="hidden" name="do" value="save_modif_cat" />
  <input type="hidden" name="cat_id" value="<?php echo $_GET['cat']; ?>" />
<script language="JavaScript" type="text/JavaScript">
<!--
function check_cat(form)
{
	if (form.cat_titre.value == "")
	{
		alert("Merci d'indiquer l'intitulé de la rubrique.");
		return false;
	}
	else
	{
		getElement('envoi').disabled = true;
		getElement('envoi').value = 'Patience...';
		return true;
	}
}
//-->
</script>
<h2>Modifier une rubrique</h2>
<p>Intitul&eacute; de la rubrique* : 
  <input type="text" name="cat_titre" tabindex="1" maxlength="255" style="width: 250px;" value="<?php echo $rubrique['cat_titre']; ?>" /> 
</p>
<p>Description de la rubrique :</p>
<?php panneau_mise_en_forme('cat_description', true); ?>
<textarea name="cat_description" id="cat_description" cols="50" rows="3" tabindex="2" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo $rubrique['cat_description']; ?></textarea>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Enregistrer les modifications" tabindex="3" />
<p class="petitbleu">* Case obligatoire</p>
</form>
<?php
		}
		else
		{ // la rubrique n'existe pas
?>
<div class="msg">
<p class="rmq" align="center">Cette rubrique n'existe pas !</p>
</div>
<?php
		}
	}
	else if ($_POST['do'] == 'save_modif_cat')
	{ // enregistrement des modifications à une rubrique
		if (is_numeric($_POST['cat_id']) and !empty($_POST['cat_titre']))
		{
			$cat_titre = htmlentities($_POST['cat_titre'], ENT_QUOTES);
			$cat_description = htmlentities($_POST['cat_description'], ENT_QUOTES);
			$sql = "UPDATE ".PREFIXE_TABLES."fichiers_cat SET cat_titre = '$cat_titre', cat_description = '$cat_description' WHERE cat_id = '$_POST[cat_id]'";
			send_sql($db, $sql);
			// log de l'action
			log_this('Modification rubrique '.$_POST['cat_id'].' '.$cat_titre, 'fichiers', true);
			header("Location: index.php?page=fichiers_gestion&do=rubriques&msg=okmodif");
			exit;
		}
		else
		{
			header("Location: index.php?page=fichiers_gestion&do=rubriques&msg=erreur");
			exit;
		}
	}
	else if ($user['niveau']['numniveau'] == 5 and $_GET['do'] == 'suppr_cat' and is_numeric($_GET['cat']))
	{ // formulaire de suppression d'une rubrique
?>
<h1>Gestion des rubriques de t&eacute;l&eacute;chargement </h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>">Retour &agrave; la page de T&eacute;l&eacute;chargements</a></p>
<?php
		$sql = "SELECT cat_id, cat_titre FROM ".PREFIXE_TABLES."fichiers_cat";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 1)
		{ // le webmaster doit laisser au moins une rubrique
			while ($ligne = mysql_fetch_assoc($res))
			{
				$rubrique[$ligne['cat_id']] = $ligne['cat_titre'];
			}
?>
<form action="fichiers_gestion.php" method="post" name="suppr_cat" id="suppr_cat" onsubmit="return check_cat(this);" class="form_config_site">
  <input type="hidden" name="do" value="do_suppr_cat" />
  <input type="hidden" name="cat" value="<?php echo $_GET['cat']; ?>" />
<script language="JavaScript" type="text/JavaScript">
<!--
function check_cat(form)
{
	if (form.with_rest_m.checked && form.new_cat_id.value == 'x')
	{
		alert("Merci de choisir la rubrique où tu souhaites déplacer les fichiers.");
		return false;
	}
	else
	{
		if (confirm("Es-tu certain de vouloir supprimer cette rubrique ?"))
		{
			getElement('envoi').disabled = true;
			getElement('envoi').value = 'Patience...';
			return true;
		}
		else
		{
			return false;
		}
	}
}
//-->
</script>
<h2>Supprimer une rubrique</h2>
<p>Es-tu certain de vouloir supprimer la rubrique '<?php echo makehtml($rubrique[$_GET['cat']]); ?>' ?</p>
<p>Que souhaites-tu faire des fichiers de cette rubrique ?</p>
<p><input name="with_rest" type="radio" id="with_rest_s" tabindex="1" onchange="getElement('new_cat_id').value = '';" value="suppr" />
  <label for="with_rest_s">les supprimer</label></p>
<p><input name="with_rest" type="radio" id="with_rest_m" tabindex="2" value="move" checked="checked" /> 
  <label for="with_rest_m">les d&eacute;placer dans la rubrique</label> <select name="new_cat_id" id="new_cat_id" tabindex="3" onchange="if (this.value != '') {getElement('with_rest_m').checked = true;};">
    <option value="x">Choisis une rubrique</option>
<?php
			foreach($rubrique as $cat_id => $cat_titre)
			{
				if ($cat_id != $_GET['cat'])
				{
					echo '<option value="'.$cat_id.'">'.$cat_titre.'</option>';
				}
			}
?>
  </select></p>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Supprimer la rubrique" tabindex="4" />
</p>
</form>
<?php
		}
		else
		{ // Il ne reste que maximum une rubrique. Sans elle, il devient impossible de déposer des fichiers sur le site
?>
<div class="msg">
<p class="rmq" align="center">Suppression impossible ! </p>
<p align="center">La page de t&eacute;l&eacute;chargements doit compter au minimum
    une rubrique.</p>
</div>
<?php
		}
	}
	else if ($user['niveau']['numniveau'] == 5 and $_POST['do'] == 'do_suppr_cat' and is_numeric($_POST['cat']) and !empty($_POST['with_rest']))
	{
		if ($_POST['with_rest'] == 'suppr')
		{
			$liste_delete = '';
			$erreur_delete = false;
			$sql = "SELECT nomserveur FROM ".PREFIXE_TABLES."fichiers WHERE cat_id = '$_POST[cat]'";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) > 0)
			{
				while ($fichier = mysql_fetch_assoc($res))
				{
					if (!@unlink('fichiers/'.$fichier['nomserveur']))
					{
						$erreur_delete = true;
						$liste_delete .= (empty($liste_delete)) ? $fichier['nomserveur'] : ', '.$fichier['nomserveur'];
					}
				}
				$sql = "DELETE FROM ".PREFIXE_TABLES."fichiers WHERE cat_id = '$_POST[cat]'";
				send_sql($db, $sql);
			}
			$sql = "DELETE FROM ".PREFIXE_TABLES."fichiers_cat WHERE cat_id = '$_POST[cat]'";
			send_sql($db, $sql);
			$msg_delete = ($erreur_delete) ? ' - Impossible de supprimer les fichiers du serveur : '.$liste_delete : '';
			$erreur_delete = ($erreur_delete) ? '&erreur=1' : '';
			// log de l'action
			log_this('Suppression rubrique '.$_POST['cat'].' et suppression des fichiers restants'.$erreur_delete, 'fichiers', true);
			header('Location: index.php?page=fichiers_gestion&do=rubriques&msg=oksuppr'.$erreur_delete);
		}
		else if ($_POST['with_rest'] == 'move' and is_numeric($_POST['new_cat_id']))
		{
			$sql = "UPDATE ".PREFIXE_TABLES."fichiers SET cat_id = '$_POST[new_cat_id]' WHERE cat_id = '$_POST[cat]'";
			send_sql($db, $sql);
			$sql = "DELETE FROM ".PREFIXE_TABLES."fichiers_cat WHERE cat_id = '$_POST[cat]'";
			send_sql($db, $sql);
			// log de l'action
			log_this('Suppression rubrique '.$_POST['cat'].' et deplacement des fichiers restants dans la rubrique '.$_POST['new_cat_id'], 'fichiers', true);
			header('Location: index.php?page=fichiers_gestion&do=rubriques&msg=oksuppr');
		}
		else
		{
			echo 'euh, on s\'amuse ?';
		}
	}
}
if (!defined('IN_SITE'))
{
?>

</body>
</html>
<?php
}
?>