<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* inscr.php v 1.1.1 - Inscription comme membres sur le portail
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
*	Correction lien vers étape suivante (url-rewriting inactif) (fil 70)
*	inscription.php renommé en inscr.php
*	passage au nouveau modèle de mot de passe
*	externalisation du texte des mails
* Modifications v 1.1.1
*	Correction lien activation quand url-rewriting désactivé (&amp; -> & dans un mail au format texte)
*/

include_once('connex.php');
include_once('fonc.php');

/*
* Différentes valeurs de $step
*	1 formulaire email
*	2 envoi de la clé de validation
*	3 message de confirmation de l'envoi de l'email
*	4 formulaire inscription
*	5 enregistrement du profil
*	6 confirmation inscription
*/

if (empty($_POST['step']) and !defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Inscription comme membre sur le portail</title>
<link rel="stylesheet" href="templates/default/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
}

if ((empty($_GET['step']) and empty($_POST['step'])) or $_GET['step'] == 1)
{
	if (is_array($sections))
	{ // Les inscriptions ne sont ouvertes qu'une fois que le webmaster a créé des sections
?>
<h1>Inscription sur le portail</h1>
<form action="inscr.php" method="post" name="inscription1" id="inscription1" onsubmit="return check_form(this);" class="form_config_site">
<h2>V&eacute;rification de l'email - 1/3</h2>
<p>Pour participer &agrave; la vie du portail, indique ton adresse email ci-dessous.</p>
<?php
		if ($_GET['err'] == 1)
		{
?>
<p align="center" class="rmq">L'adresse email n'est pas une adresse valide.</p>
<?php
		}
		else if ($_GET['err'] == 2)
		{
?>
<p align="center" class="rmq">Impossible d'envoyer l'email avec la cl&eacute; d'activation, contacte le webmaster.</p>
<?php
		}
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function check_form(form)
{
	if (form.email.value != "")
	{
		getElement("go").disabled = true;
		getElement("go").value = "Patience...";
		return true;
	}
	else
	{
		alert("Merci d'indiquer ton adresse email.");
		return false;
	}
}
//-->
</script>
<p align="center">Ton adresse email : 
<input name="email" type="text" id="email" style="width:140px;" tabindex="1" maxlength="255" />
<input name="go" type="submit" id="go" tabindex="2" value="Envoyer" />
<input name="step" type="hidden" id="step" value="2" /></p>
<?php
		if (ENVOI_MAILS_ACTIF)
		{
			$lien = ($site['url_rewriting_actif'] == 1) ? 'inscr2.php' : 'index.php?page=inscr&amp;step=3';
?>
<p align="center">Tu as re&ccedil;u l'email avec la cl&eacute; d'activation ? 
<a href="<?php echo $lien; ?>" tabindex="3">Passe &agrave; la suite</a></p>
<?php
		}

		if (isset($_COOKIE['pseudo_stocke']) and !ENVOI_MAILS_ACTIF)
		{ // L'utilisateur s'est déjà connecté sur le site, on lui propose éventuellement de retrouver son mot de passe
?>
<p align="center">
  <a href="#" tabindex="4" onclick="alert('Si tu as oublié ton mot de passe, écris un mail au webmaster (<?php echo $site['mailwebmaster']; ?>).\nIl en créera un nouveau qu\'il t\'enverra par mail à l\'adresse email de ton inscription. Tu pourras ensuite le modifier sur la page de ton profil.');">J'ai 
  oubli&eacute; mon mot de passe</a> </p>
<?php
		}
		else
		{
?>
<p align="center">
  <a href="index.php?page=newpw" tabindex="5">J'ai oubli&eacute; mon mot de passe</a></p>
<?php
		}
?>
</form>
<?php
		if (ENVOI_MAILS_ACTIF)
		{
?>        
<div class="instructions">
  <h2>Quelques infos</h2>
<p>L'inscription sur le portail se fait en <em><strong>deux &eacute;tapes</strong></em>.</p>
  <ol>
	<li class="petit">Tu entres ton adresse email et tu re&ccedil;ois une 
	  cl&eacute; d'activation par mail. Cette cl&eacute; te permet de passer 
	  &agrave; l'&eacute;tape suivante.</li>
	<li class="petit">Ensuite, tu remplis un formulaire et c'est fait !</li>
  </ol>
  <h2>Remarques</h2>
  <ul class="petit">
	<li>Si tu <strong>filtres</strong> ton courrier &eacute;lectronique <strong>contre 
	  le spam</strong>, ajoute <?php echo hidemail($site['mailwebmaster'], 'lien'); ?> 
	  &agrave; ton carnet pour recevoir les emails,</li>
	<li>Si tu t'inscris en tant qu'<strong>animateur</strong>, ton statut 
	  d'animateur ne sera reconnu qu'une fois qu'un autre animateur aura approuv&eacute; 
	  ton inscription,</li>
	<li>Tu as commenc&eacute; ton inscription depuis plus d'une heure et tu 
	  n'as toujours <strong>pas re&ccedil;u de mail ?</strong> Contacte le 
	  <?php echo hidemail($site['mailwebmaster'], 'lien_texte', 'webmaster'); ?>.</li>
	<li>Les <strong>donn&eacute;es</strong> que tu fournis lors de ton inscription 
	  ne seront transmises &agrave; personne. Elles restent au sein de l'Unit&eacute; 
	  pour son <strong>usage interne</strong>. Si tu souhaites consulter ou 
	  modifier ces informations, merci de contacter le webmaster. </li>
  </ul>
</div>
<?php
		}
	} // is_array($sections)
	else
	{ // Le webmaster n'a pas encore créé de sections sur le site
?>
<h1>Inscription sur le portail</h1>
<div class="msg">
<p align="center" class="rmq">Les inscriptions ne sont pas encore ouvertes sur 
  le portail.</p>
<p align="center">Le webmaster doit d'abord terminer l'installation 
  du portail.</p>
</div>
<?php
	}
}
//////////////////////////
// Enregistrement phase 1 : clé de validation et envoi du mail
//////////////////////////
else if ($_POST['step'] == 2)
{
	$email = htmlspecialchars($_POST['email']);
	if (checkmail($email, ''))
	{
		$cle = '';
		while (!check_cle_unique($cle)) // check_cle_unique est défini en haut de cette page
		{ // on produit une clé de validation unique histoire d'éviter les bugs
			$cle = cleunique('mini');
		}
		$ip = $_SERVER['REMOTE_ADDR'];
		// enregistrement dans la db
		$sql = "INSERT INTO ".PREFIXE_TABLES."auteurs (email, dateinscr, clevalidation, ipinscription) values ('$email', now(), '$cle', '$ip')";
		send_sql($db, $sql);
		if (ENVOI_MAILS_ACTIF)
		{ // envoi du mail
			if ($site['url_rewriting_actif'] == 1)
			{
				$base = $site['adressesite'].'inscr2';
				$lien = $base.'_'.$cle.'.php';
			}
			else
			{
				$base = $site['adressesite'].'index.php?page=inscr';
				$lien = $base.'&step=3&validation='.$cle;
			}
			include_once('prv/emailer.php');
			$courrier = new emailer();
			$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
			$reponse = $expediteur;
			$courrier->from($expediteur);
			$courrier->to($email);
			$courrier->reply_to($expediteur);
			$courrier->use_template('inscription_validation', 'fr');
			$courrier->assign_vars(array(
				'URL_VALIDATION' => $lien,
				'CLE_VALIDATION' => $cle,
				'ADRESSE_SITE' => $site['adressesite'],
				'WEBMASTER_PSEUDO' => $site['webmaster'],
				'WEBMASTER_EMAIL' => $site['mailwebmaster']));
			if ($courrier->send())
			{
				$courrier->reset();
				header('Location: index.php?page=inscr&step=3&email='.$email.'&maildone=1');
			}
			else
			{
				header('Location: index.php?page=inscr&step=1&err=2');
			}
		}
		else
		{ // l'envoi de mail est inactif sur le portail
		  // on passe à l'étape suivante de l'inscription
			header('Location: index.php?page=inscr&step=4&validation='.$cle);
		}
	}
	else
	{
		if ($page == 'inscr')
		{
			include('404.php');
		}
		else
		{
			header('Location: index.php?page=inscr&step=1&err=1');
			exit;
		}
	}
}
else if ($_GET['step'] == 3)
{
?>
<h1>Inscription sur le portail</h1>
<?php 
	if ($_GET['maildone'] == 1)
	{
?>
<div class="msg">
<p align="center">Voil&agrave;, un mail vient d'être envoy&eacute; &agrave; l'adresse : 
<span class="rmq"><?php echo $_GET['email']; ?></span></p>
<p align="center" class="petitbleu"> Tu pourras continuer ton inscription apr&egrave;s avoir 
  re&ccedil;u ce mail.</p>
</div>
<?php
	}
?>
<form action="index.php" method="get" name="inscription1" id="inscription1" onsubmit="return check_form(this);" class="form_config_site">
<h2>Activation du compte -  2/3 </h2>
<input type="hidden" name="page" value="inscr" />
<script language="JavaScript" type="text/JavaScript">
<!--
function check_form(form)
{
	if (form.validation.value.length == 5 || form.validation.value.length == 20)
	{
		getElement("go2").disabled = true;
		getElement("go2").value = "Patience...";
		return true;
	}
	else
	{
		alert("La clé d'activation n'a pas le bon format");
		return false;
	}
}
//-->
</script>
<p align="center">
  Entre ici la cl&eacute; d'activation : 
  <input name="validation" type="text" id="validation" style="width:140px;" tabindex="1" maxlength="20"<?php echo (!empty($_GET['validation'])) ? ' value="'.$_GET['validation'].'"' : ''; ?> />
  <input name="go" type="submit" id="go2" tabindex="2" value="Continuer" />
  <input name="step" type="hidden" id="step2" value="4" />
</p>
<p align="center">Tu devrais avoir re&ccedil;u cette cl&eacute; par mail.</p>
</form>
<?php
}
else if ($_GET['step'] == 4)
{ // L'utilisateur vient d'entrer sa clé d'activation
	// Préalable suite à la mise à jour, on réduit la taille de la clé à 5 caractères.
	$validation = substr($_GET['validation'], 0, 5);
	
	if (eregi("^[a-z0-9]{5}$", $validation))
	{
		$sql = "SELECT email FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation = '".$validation."'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) != 1)
			{
				$step = 'erreur';
			}
			$ligne = mysql_fetch_assoc($res);
			$email = $ligne['email'];
		}
	}
	else
	{
		$step = 'erreur';
	}

	if ($step != 'erreur')
	{
		if (!empty($_GET['monpseudo']) and $_GET['err'] == 1 and !empty($_GET['pseudo']))
		{
			$monpseudo = $_GET['pseudo'];
		}
?>
<h1>Inscription sur le portail</h1>
<script language="JavaScript" type="text/JavaScript">
<!--
function check_form(form)
{
	if (form.niveau.value != "" && form.monpseudo.value != "" && form.pw.value != "" && form.pwconfirm.value != "" && form.prenom.value != "" && form.nom.value != "")
	{
		if (form.pw.value == form.pwconfirm.value)
		{
			if (form.pw.value.length < 6)
			{
				alert("Ton mot de passe doit comporter au moins 6 caractères");
				form.pw.value = '';
				form.pwconfirm.value = '';
				return false;
			}
			else
			{
				if (form.monpseudo.value.length < 4)
				{
					alert("Ton pseudo doit comporter au moins 4 caractères");
					form.monpseudo.value = '';
					return false;
				}
				else
				{
					if (getElement("conditionsok").value == "ok")
					{
						return true;
					}
					else
					{
						alert("Tu dois avoir lu et accepté les conditions d'utilisation du portail pour pouvoir poursuivre ton inscription.");
						return false;
					}
				}
			}
		}
		else
		{
			alert("Le mot de passe que tu as encodé est incorrect.");
			form.pw.value = '';
			form.pwconfirm.value = '';
			return false;
		}
	}
	else
	{
		alert("Toutes les cases sont obligatoires.");
		return false;
	}
}

// dejadit évite d'afficher l'avertissement pour l'acceptation des conditions plusieurs fois
var dejadit = false;

//-->
</script>
<div class="introduction">
<p>Pour terminer ton inscription, remplis 
  le formulaire ci-dessous. Apr&egrave;s ton inscription, tu pourras enrichir
  ton profil  en ajoutant photo, description, loisirs, date de naissance, 
  ... </p>
</div>
<?php
		$err = $_GET['err'];
		if ($err > 1)
		{
?>
<div class="msg">
<?php
			if ($err % 59 == 0) {echo '<p class="rmq">N\'oublies pas de pr&eacute;ciser ton statut dans l\'Unit&eacute;.</p>';}
			if ($err % 53 == 0) {echo '<p class="rmq">Merci de pr&eacute;ciser dans quelle section tu te trouves.</p>';}
			if ($err % 3 == 0) {echo '<p class="rmq">D&eacute;sol&eacute;, le pseudo "'.htmlentities($_GET['fpseudo'], ENT_QUOTES).'" est d&eacute;j&agrave; utilis&eacute;.</p>';}
			if ($err % 5 == 0) {echo '<p class="rmq">Tu dois indiquer ton pr&eacute;nom.</p>';}
			if ($err % 7 == 0) {echo '<p class="rmq">N\'oublies pas de mettre ton nom.</p>';}
			if ($err % 11 == 0) {echo '<p class="rmq">Tu dois pr&eacute;ciser ton adresse email.</p>';}
			if ($err % 13 == 0) {echo '<p class="rmq">Merci d\'indiquer une adresse email correcte.</p>';}
			if ($err % 17 == 0) {echo '<p class="rmq">Es-tu un homme ou une femme?</p>';}
			if ($err % 19 == 0) {echo '<p class="rmq">Ton mot de passe doit contenir au moins 6 caract&egrave;res.</p>';}
			if ($err % 23 == 0) {echo '<p class="rmq">Mot de passe incorrect.</p>';}
			if ($err % 29 == 0) {echo '<p class="rmq">Ton pseudo doit contenir minimum 4 caract&egrave;res.</p>';}
			if ($err % 43 == 0) {echo '<p class="rmq">L\'indicatif de section est incorrect.</p>';}
			if ($err % 31 == 0) {echo '<p class="rmq">D&eacute;sol&eacute;, la validation de ton inscription a &eacute;chou&eacute;, merci de recommencer.</p>';}
			if ($err % 41 == 0) {echo '<p class="rmq">Une erreur s\'est produite ! Activation abandonn&eacute;e, merci de contacter le webmaster et de lui signaler l\'erreur 41.</p>';}
			if ($err % 47 == 0) {echo '<p class="rmq">Ton pseudo ne peut pas contenir d\'espaces.</p>';}
			// nombres premiers suivants :  37, 61, 67, 71, 73, 79, 83, 89, 97
?>
</div>
<?php
		}
?>
<form action="inscr.php" method="post" name="forminscr" id="forminscr" onsubmit="return check_form(this);" class="form_config_site">
  <h2>
    <input type="hidden" name="validation" value="<?php echo $validation; ?>" />
    <input type="hidden" name="step" value="5" /> 
    Ton profil sur le site - 3/3</h2>
  <fieldset>
<legend>Ton adresse email</legend>
<p>Ton adresse email : <span class="rmq"><?php echo $ligne['email']; ?></span></p>
</fieldset>
<fieldset>
<legend>Ton statut dans l'Unit&eacute;</legend>
<p class="petitbleu">Les <strong>statuts pr&eacute;c&eacute;d&eacute;s 
  d'une ast&eacute;risque</strong> doivent &ecirc;tre valid&eacute;s 
  par un membre du portail ayant un statut similaire.</p>
<p>Statut dans l'Unit&eacute; :
  <select name="niveau" tabindex="1">
	<option value="" <?php if (empty($_GET['niveau'])) echo 'selected'; ?>></option>
<?php
		$tri = array('numniveau', 'nomniveau');
		$niveaux = super_sort($niveaux);
		foreach ($niveaux as $ligne)
		{
			if ($ligne['show_at_inscr'])
			{
?>
    <option value="<?php echo $ligne['idniveau']; ?>" <?php if ($ligne['idniveau'] == $_GET['niveau']) echo 'selected'; echo ($ligne['numniveau'] >= 3) ? ' style="font-weight:bold;"' : ''; ?>><?php echo ($ligne['numniveau'] >= 3) ? '*' : ''; echo stripslashes($ligne['nomniveau']); ?></option>
<?php
			}
		}
?>
</select></p>
</fieldset>
<fieldset>
<legend>Donn&eacute;es personnelles</legend>
<p class="petitbleu">Ton pseudo sera ta signature sur le portail. Prends ton totem par exemple, ou un 
surnom qu'on te donne.</p>
<p>Pseudo :
  <input name="monpseudo" type="text" class="case" style="width:100px;" tabindex="2" value="<?php echo $monpseudo;?>" maxlength="32" />
</p>
<p>Ton pr&eacute;nom :
  <input name="prenom" type="text" class="case" style="width:110px;" tabindex="3" value="<?php echo $_GET['prenom'];?>" maxlength="100" />
Ton nom :
<input name="nom" type="text" class="case" style="width:110px;" tabindex="4" onchange="this.value=this.value.toUpperCase()" value="<?php echo $_GET['nom'];?>" maxlength="100" />
</p>
</fieldset>
<fieldset>
<legend>Mot de passe</legend>
<p class="petitbleu">Ton mot de passe doit &ecirc;tre assez complexe (m&eacute;langer chiffres 
et lettres, majuscules et minuscules) mais facile &agrave; retenir.</p>
<p>Mot de passe :
  <input name="pw" type="password" class="case" style="width:100px;" tabindex="5" maxlength="32" /> 
 Confirmation
 :
 <input name="pwconfirm" type="password" class="case" style="width:100px;" tabindex="6" maxlength="32" /> 
</p>
</fieldset>
<fieldset>
<legend>Divers</legend>
<p> <input name="mailing" type="checkbox" id="mailing" tabindex="7" value="sub" checked="checked" />
<label for="mailing">Je souhaite recevoir de temps &agrave; autre  la <span class="rmqbleu">lettre
d'information de l'Unit&eacute;</span>.</label></p>
<p class="petitbleu">Pas pour les convocations et autres  courriers importants.</p>
</fieldset>
<fieldset>
<legend class="rmqbleu">Conditions d'utilisation</legend>
<p>Ton inscription sur le portail entra&icirc;ne ton acceptation des conditions d'utilisation suivantes :</p>
<ul>
<li>Tu t'engages &agrave; ne pas divulguer &agrave; un tiers les informations &agrave; caract&egrave;re priv&eacute; 
auxquelles un acc&egrave;s t'es donn&eacute; apr&egrave;s avoir &eacute;t&eacute; identifi&eacute; sur le portail, 
&agrave; moins de disposer de l'accord des personnes concern&eacute;es par ces donn&eacute;es.</li>
<li>Tu t'engages &agrave; respecter la l&eacute;gislation en vigueur sur le territoire belge.</li>
<li>Tu t'engages &agrave; respecter <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'avertissement.htm' : 'index.php?page=avertissement'; ?>" tabindex="8">la 
netiquette</a> telle qu'elle est appliqu&eacute;e en g&eacute;n&eacute;ral sur le web.</li>
<li>Tu t'engages &agrave; conserver une attitude scoute &agrave; tout moment lors de l'utilisation de ce portail 
ou des donn&eacute;es qui y sont mises &agrave; ta disposition.</li>
</ul>
<p>Toute infraction aux pr&eacute;sentes conditions d'utilisation ou tout comportement jug&eacute; comme &eacute;tant inadmissible 
par le webmaster ou l'un des animateurs provoquera, apr&egrave;s avertissement, le bannissement du membre concern&eacute; 
ou toute autre mesure ad&eacute;quate &agrave; la situation.</p>
<p align="center" class="rmqbleu">
<input name="conditionsok" type="checkbox" id="conditionsok" tabindex="9" onclick="if(this.checked) {getElement('suiteinscription').disabled = false;} else {getElement('suiteinscription').disabled = true;}" value="ok" />
<label for="conditionsok">J'ai lu et j'accepte les conditions d'utilisation de ce 
site.</label></p>
</fieldset>
<p align="center"><strong>Toutes les cases doivent &ecirc;tre remplies.</strong></p>
<noscript>
<p class="rmq" align="center">Pour activer le bouton ci-dessous, active le javascript dans ton navigateur web.</p>
</noscript>
<p align="center" onmouseover="if(getElement('suiteinscription').disabled && !dejadit) {alert('Tu dois accepter les conditions d\'utilisation du site pour terminer ton inscription.'); dejadit = true;}"><input name="Submit" type="submit" disabled="true" class="bouton" id="suiteinscription" tabindex="10" value="S'inscrire" />
  <input name="annuler" type="reset" class="bouton" tabindex="11" onclick="getElement('suiteinscription').disabled = true;" value="Recommencer" />
</p>
<p align="center" class="petitbleu">Ton mot de passe ne sera pas conserv&eacute; 
  dans la base de donn&eacute;es. Seule son empreinte crypt&eacute;e 
  est enregistr&eacute;e.<br />
  A tout moment, tu peux modifier ton mot de passe et d'autres 
  informations personnelles sur la page de ton profil.</p>
</form>
<?php
	} // Pas besoin de else ici car on renvoie directement à step = erreur
}
else if ($_POST['step'] == 5)
{ // le membre finalise son inscription, vérification des entrées du formulaire
	include_once('connex.php');
	include_once('fonc.php');
	// Il faudra placer un petit substr pour permettre aux inscriptions commencées avant l'update du portail à la version 1.1
	// de se dérouler sans difficulté (la clé de validation passe de 20 à 5 caractères)
	if (eregi("^[a-z0-9]{5}$", $_POST['validation']))
	{ // sa clé de validation est-elle correcte ?
		$sql = "SELECT email FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation = '".$_POST['validation']."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) != 1)
		{ // aucune clé correspondante
			header('Location: index.php?page=inscr&step=erreur');
			exit;
		}
		else
		{ // la clé est valide
			$ligne = mysql_fetch_assoc($res);
			$email = $ligne['email'];
		}
	}
	else
	{ // c'est même pas une clé de validation...
		header('Location: index.php?page=inscr&step=erreur');
		exit;
	}
	$err = 1;
	if (strlen($_POST['monpseudo']) < 4)
	{ // le pseudo doit faire au moins 4 caractères
		$err = $err * 29;
		header('Location: index.php?page=inscr&step=4&err='.$err.'&validation='.$_POST['validation']);
		exit;
	}
	if (checkforbiddenchar($_POST['monpseudo']))
	{ // le pseudo ne peut pas contenir d'espaces ou certains caractères
		$err = $err * 47;
		header('Location: index.php?page=inscr&step=4&err='.$err.'&validation='.$_POST['validation']);
		exit;
	}
	else
	{ // le pseudo a le bon format
		// on teste si le pseudo est encore libre
		$monpseudo = htmlentities($_POST['monpseudo'], ENT_QUOTES);
		$sql = "SELECT num FROM ".PREFIXE_TABLES."auteurs WHERE pseudo = '$monpseudo'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) != 0)
		{ // pas de bol, le pseudo est déjà pris par un membre
			$err = $err * 3;
			$fpseudo = $_POST['monpseudo'];
			$monpseudo = '';
		}
	}
	// test prenom
	if (empty($_POST['prenom'])) {$err = $err * 5;}
	// test nom
	if (empty($_POST['nom'])) {$err = $err * 7;}
	if ($_POST['pw'] != $_POST['pwconfirm'])
	{ // le mot de passe n'est pas correct
		$err = $err * 23;
		$pw = $pwconfirm = '';
	}
	else
	{ // le mot de passe est bon
		if (strlen($_POST['pw']) < 6)
		{ // mais fait moins de 6 caractères
			$err = $err * 19;
			$pw = $pwconfirm = '';
		}
	}
	if (empty($_POST['niveau']) or !is_numeric($_POST['niveau']))
	{ // le membre n'a pas choisi de statut
		$err = $err * 59;
	}

	// Fin des vérifications d'usage

	if ($err == 1)
	{ // aucune erreur trouvée, on enregistre les données du membre
		// cryptage du pw avec un peu de sel pour réduire les risques de reverse engineering
		// En effet, des dictionnaires de hash md5 sont en cours de construction afin de 'casser' le md5
		// au brute force en listant les hash md5 de mots simples, et souvent courts.
		// Ici, on allonge artificiellement le mot de passe, modifiant ainsi fortement son hashage md5.
		// De cette manière, même en récupérant le sel et le hash md5 du mot de passe, le dictionnaire existant est impuissant.
		$pw = md5(UN_PEU_DE_SEL.$_POST['pw']);
		$niveau = $_POST['niveau'];
		$numsection = $niveaux[$_POST['niveau']]['section_niveau'];
		if ($niveaux[$_POST['niveau']]['numniveau'] > 2) 
		{ // si le nouveau membre demande un statut avec un droit d'accès d'animateur
		  // il reçoit provisoirement un statut de visiteur et nivdemande prend la valeur
		  // correspondant au niveau qu'il a demandé
			$nivdemande = $_POST['niveau'];
			$niveau = 1; // normalement le statut 1 de visiteur est protégé contre les modifications (swp v 1.0.3)
		}
		else
		{ // sinon, nivdemande prend la valeur 0
			$nivdemande = 0;
		}
		$monpseudo = htmlentities($_POST['monpseudo'], ENT_QUOTES);
		$prenom = htmlentities($_POST['prenom'], ENT_QUOTES);
		$nom = htmlentities($_POST['nom'], ENT_QUOTES);
		if ($_POST['mailing'] == 'sub')
		{ // abonnement à la newsletter
			abonnement_newsletter($email, $prenom.' '.$nom);
		}
		// inscription dans la db
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET pw = '$pw', pseudo = '$monpseudo', prenom = '$prenom', 
		nom = '$nom', niveau = '$niveau', nivdemande = '$nivdemande', assistantwebmaster = '0', numsection = '$numsection', banni = '0', 
		clevalidation = '' WHERE clevalidation = '".$_POST['validation']."'";
		$res = send_sql($db, $sql);
		header('Location: index.php?page=inscr&step=6&niveau='.$_POST['niveau'].'&monpseudo='.$monpseudo);
		exit;
	}
	else
	{ // une erreur s'est produite on renvoie au formulaire
		header('Location: index.php?page=inscr&step=4&err='.$err.'&monpseudo='.$monpseudo.'&fpseudo='.$fpseudo.'&prenom='.$prenom.'&nom='.$nom.'&email='.$email.'&niveau='.$niveau.'&numsection='.$numsection.'&validation='.$_POST['validation']);
		exit;
	}
}
else if ($_GET['step'] == 6)
{
?>
 <h1>Inscription sur le portail </h1>
 <div class="msg">
<p align="center" class="rmqbleu">Merci de t'&ecirc;tre inscrit <?php echo $_GET['monpseudo']; ?>.</p>
<p>D&egrave;s maintenant, tu peux d&eacute;poser sur le site des r&eacute;cits 
d'activit&eacute;s, les bons tuyaux pour la lessive des affaires sales, 
les bonnes adresses, ... et tout ce qui concerne le scoutisme.</p>
<?php
	if ($niveaux[$_GET['niveau']]['numniveau'] > 2)
	{
?>
<p class="petitbleu">Cependant, avant d'acc&eacute;der aux fonctions r&eacute;serv&eacute;es 
aux animateurs de l'Unit&eacute;, ton inscription doit &ecirc;tre confirm&eacute;e 
par un autre animateur.</p> 
<?php
	}
?>
</div>
<?php
	include('login.php');
}
if ($_GET['step'] == 'erreur' or $step == 'erreur')
{
?>
<h1>Inscription sur le portail </h1>
<div class="msg" align="center">
  <p class="rmq">D&eacute;sol&eacute; cette cl&eacute; d'activation est p&eacute;rim&eacute;e 
    ou incorrecte.</p>
  <p> Inscription interrompue !</p>
</div>
<?php 
}
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