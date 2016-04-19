<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* pagesrestreintespost.php v 1.1 - Enregistrement d'une page restreinte
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
*	externalisation de l'envoi du mail
*	Optimisation xhtml
*	Suppression des commentaires sur le forum des staffs
*/

include_once('connex.php');
include_once('fonc.php');

if (($_GET['do'] == 'ecrire' or $_GET['do'] == 'modif') and $user['niveau']['numniveau'] > 2)
{
	if ($_GET['do'] == 'modif' and is_numeric($_GET['numpage']))
	{ // l'utilisateur édite une page restreinte existante, on récupère son contenu pour l'afficher dans le formulaire
		$sql = "SELECT numpage, titre, article
		FROM 
		".PREFIXE_TABLES."pagesrestreintes 
		WHERE numpage = '".$_GET['numpage']."'";
		if ($res = send_sql($db, $sql))
		{
			$donnees = mysql_fetch_assoc($res);
			$titre = $donnees['titre'];
			$message = $donnees['article'];
			$numero = $donnees['numpage'];
			$afaire = 'savemodif';
		}
	}
	else
	{ // l'utilisateur crée une page restreinte
		$afaire = 'save';
	}

	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>R&eacute;diger une page &agrave; acc&egrave;s restreint</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<script type="text/javascript" language="JavaScript">
<!--
function validate(form) 
{
	if (form.message.value=="") 
	{
		alert("Pourquoi poster des notes vides?");
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
//-->
</script>
<br />
<form action="pagesrestreintespost.php" method="post" name="formulaire" id="formulaire" onsubmit="return validate(this)" class="form_config_site">
  <input type="hidden" name="do" value="<?php echo $afaire; ?>" />
<?php
	if ($_GET['do'] == 'modif') 
	{ // il édite la page restreinte $numero
?>
  <input type="hidden" name="num" value="<?php echo $numero; ?>" />
<?php
	}
?>
<h2><?php echo ($_GET['do'] == 'modif') ? 'Modifier ' : 'R&eacute;diger '; ?>une page &agrave; acc&egrave;s restreint</h2>
<p>Titre : <input name="titre" type="text" tabindex="1" value="<?php echo $titre; ?>" size="50" maxlength="50" />
</p>
<?php 
		echo panneau_mise_en_forme('message', true);
?>
<textarea name="message" cols="70" rows="12" id="message" style="width:95%;" tabindex="2" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo stripslashes($message); ?></textarea>
<?php
	if (ENVOI_MAILS_ACTIF)
	{
?>
<p align="center"><input name="prevenirchefs" type="checkbox" id="prevenirchefs" tabindex="3" value="oui"<?php if ($_GET['do'] != 'modif') {echo ' checked="checked"';} ?> />
<label for="prevenirchefs">Pr&eacute;venir tous les animateurs* de ton ajout</label></p>
<?php
	}
?>
<p align="center"><input name="envoi" type="submit" tabindex="4" value="Envoyer" />
 <input name="reset" type="reset" tabindex="5" value="Recommencer" />
</p>
<?php
	if (ENVOI_MAILS_ACTIF)
	{
?>
<p class="petitbleu">* Seuls les animateurs ayant un email enregistr&eacute; dans la base des 
  membres (Gestion de l'Unit&eacute;) sont pr&eacute;venus...</p>
<?php
	}
?>
<?php panneau_smileys('message'); ?>
</form>
<?php
}
else if ($_POST['do'] == 'save')
{ // nouvelle page restreinte
	$titre = htmlentities($_POST['titre'], ENT_QUOTES);
	if ($user > 0 and !empty($titre))
	{
		$message = htmlentities($_POST['message'], ENT_QUOTES);
		$sql = "INSERT INTO ".PREFIXE_TABLES."pagesrestreintes (auteur, titre, article, datecreation, pagebannie) 
		values ('".$user['num']."', '$titre', '$message', now(), '0')";
		send_sql($db, $sql);
		$sql = "SELECT numpage FROM ".PREFIXE_TABLES."pagesrestreintes WHERE titre = '$titre' ORDER BY datecreation DESC LIMIT 1";
		$res = send_sql($db, $sql);
		$aa = mysql_fetch_assoc($res);	
		$numero = $aa['num'];

		log_this('Cr&eacute;ation page restreinte ('.$numero.')', 'pagesrestreintes');

		if ($_POST['prevenirchefs'] == 'oui' and ENVOI_MAILS_ACTIF)
		{ // on prévient les animateurs
			include_once('prv/emailer.php');
			$courrier = new emailer();
			$expediteur = (!empty($user['email'])) ? $user['email'] : 'noreply@noreply.be';
			$reponse = $expediteur;
			$courrier->from($expediteur);
			$courrier->reply_to($expediteur);
			$courrier->use_template('new_pg_restreinte', 'fr');
			$courrier->set_subject(stripslashes(makehtml($titre, 'noimg')));
			$courrier->assign_vars(array(
				'USER_PSEUDO' => $user['pseudo'],
				'PAGE_TITRE' => stripslashes(makehtml($titre, 'noimg')),
				'PAGE_TEXTE' => stripslashes(makehtml($message, 'noimg')),
				'ADRESSE_SITE' => $site['adressesite'],
				'WEBMASTER_PSEUDO' => $site['webmaster'],
				'WEBMASTER_EMAIL' => $site['mailwebmaster']));
			$sql = "SELECT email_mb FROM ".PREFIXE_TABLES."mb_membres WHERE fonction > '1' AND email_mb != ''";
			$res = send_sql($db, $sql);
			while ($anim = mysql_fetch_assoc($res))
			{
				$courrier->to($anim['email_mb']);
			}
			$courrier->send();
			$courrier->reset();
		}
		header('Location: index.php?page=pagesrestreintes&numero='.$numero);
	}
	else
	{
		header('Location: index.php?page=pagesrestreintes&do=erreur');
		exit;
	}
}
else if ($_POST['do'] == 'savemodif')
{ // on enregistre les modifications apportées à la page restreinte
	$titre = htmlentities($_POST['titre'], ENT_QUOTES);
	$message = htmlentities($_POST['message'], ENT_QUOTES);
	if ($user['niveau']['numniveau'] > 2 and !empty($titre) and is_numeric($_POST['num']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."pagesrestreintes SET titre = '$titre', article = '$message' WHERE numpage = '".$_POST['num']."'";
		send_sql($db, $sql);

		log_this('Modification page restreinte ('.$_POST['num'].')', 'pagesrestreintes');

		if ($_POST['prevenirchefs'] == 'oui' and ENVOI_MAILS_ACTIF)
		{ // on prévient les animateurs
			include_once('prv/emailer.php');
			$courrier = new emailer();
			$expediteur = (!empty($user['email'])) ? $user['email'] : 'noreply@noreply.be';
			$reponse = $expediteur;
			$courrier->from($expediteur);
			$courrier->reply_to($expediteur);
			$courrier->use_template('modif_pg_restreinte', 'fr');
			$courrier->set_subject(stripslashes(makehtml($titre, 'noimg')));
			$courrier->assign_vars(array(
				'USER_PSEUDO' => $user['pseudo'],
				'PAGE_TITRE' => stripslashes(makehtml($titre, 'noimg')),
				'PAGE_TEXTE' => stripslashes(makehtml($message, 'noimg')),
				'ADRESSE_SITE' => $site['adressesite'],
				'WEBMASTER_PSEUDO' => $site['webmaster'],
				'WEBMASTER_EMAIL' => $site['mailwebmaster']));
			$sql = "SELECT email_mb FROM ".PREFIXE_TABLES."mb_membres WHERE fonction > '1' AND email_mb != ''";
			$res = send_sql($db, $sql);
			while ($anim = mysql_fetch_assoc($res))
			{
				$courrier->to($anim['email_mb']);
			}
			$courrier->send();
			$courrier->reset();
		}
		header('Location: index.php?page=pagesrestreintes&numero='.$_POST['num']);
	}
	else
	{
		header('Location: index.php?page=pagesrestreintes&do=erreur');
		exit;
	}
}
else
{
?>
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<?php
}

if (!defined("IN_SITE"))
{
?>
</body>
</html>
<?php
}
?>