<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* newad.php v 1.1 - Création d'une famille pour un membre ou un ancien de l'unité
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
*	Optimisation xhtml
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
	if ((!isset($_GET['step']) and !isset($_POST['step'])) or $_GET['step'] == 1)
	{
		if (is_numeric($_GET['nummb']))
		{
			$sql = "SELECT nom_mb, prenom, nummb, section FROM ".PREFIXE_TABLES."mb_membres WHERE nummb = '$_GET[nummb]' LIMIT 1";
			$res = send_sql($db, $sql);
			$membre = mysql_fetch_assoc($res);
			// la variable ci-dessous permet de déterminer le retour après l'envoi du formulaire
		}
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Cr&eacute;ation d'une famille</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Cr&eacute;ation d'une famille<?php 
		if (is_array($membre)) 
		{
			echo ' pour '.$membre['prenom'].' '.$membre['nom_mb'];
		}
		else if (!empty($_GET['prenom']) and !empty($_GET['nom'])) 
		{
			echo ' pour '.stripslashes($_GET['prenom']).' '.stripslashes($_GET['nom']);
		}
?></h1>
<p align="center"> <a href="?page=gestion_unite">Retour &agrave; 
  la page Gestion de l'Unit&eacute;</a></p>
<div class="introduction">
<p>Cette page te permet de cr&eacute;er une famille de l'Unit&eacute; pour un 
  membre de l'Unit&eacute;.<br /><br />
  Il te suffit de remplir le formulaire en suivant les instructions.
</p>
</div>
<?php
		if ($_GET['msg'] == 1)
		{
?>
<div class="msg">
<p align="center" class="rmq">Merci d'indiquer le nom de famille.</p>
</div>
<?php
		}
?>
<script language="JavaScript" type="text/JavaScript">
	function check_form(form)
	{
		if (form.nom.value != '')
		{
			getElement("envoi").disabled = true;
			getElement("envoi").value = "Patience...";
			return true;
		}
		else
		{
			alert("Merci d'indiquer au moins un nom pour la famille à ajouter.");
			return false;
		}
	}
</script>
<form action="newad.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="prenom" value="<?php echo stripslashes($_GET['prenom']); ?>" />
  <input type="hidden" name="nom_mb" value="<?php echo stripslashes($_GET['nom']); ?>" />
  <input type="hidden" name="suite" value="<?php echo $_GET['suite']; ?>" />
  <input type="hidden" name="nummb" value="<?php echo (is_array($membre)) ? $membre['nummb'] : ''; ?>" />
  <h2>Informations g&eacute;n&eacute;rales sur la famille </h2>
<fieldset>
<legend>Nom de famille</legend>
<table border="0" align="center" cellpadding="2" cellspacing="0">
<tr> 
  <td>Nom de famille* : </td>
  <td><input name="nom" id="nom" type="text" size="40" maxlength="100" style="width:160px;" value="<?php echo (is_array($membre)) ? $membre['nom_mb'] : stripslashes($_GET['nom']); ?>" onchange="this.value=this.value.toUpperCase()" tabindex="1" /> 
  </td>
</tr>
</table>
<script type="text/javascript">
<!--
getElement('nom').focus();
//-->
</script>
<?php
	if (is_array($membre))
	{
?>
<p class="petitbleu">Tu peux modifier le nom de famille ci-dessus, 
  ce n'est pas lui qui sert &agrave; faire le lien avec le membre.</p>
<?php
	}
?>
</fieldset>
<fieldset>
<legend>Coordonn&eacute;es de la famille</legend>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" scope="col">Adresse :
      <input name="rue" type="text" size="40" maxlength="100" style="width:150px;" tabindex="2" />
N&deg;
<input name="numero" type="text" maxlength="10" style="width:30px;" tabindex="3" />
Bte
<input name="bte" type="text" maxlength="10" style="width:30px;" tabindex="4" /></td>
  </tr>
  <tr>
	<td colspan="2" scope="col">CP : <input name="cp" type="text" value="<?php echo $site['site_code_postal']; ?>" maxlength="5" style="width:40px;" tabindex="5" />
Ville : <input name="ville" type="text" value="<?php echo $site['site_ville']; ?>" maxlength="100" style="width:130px;" tabindex="6" />
<?php
	if ($user['niveau']['numniveau'] == 5) 
	{
?><a href="index.php?page=config_site&amp;categorie=groupe" title="D&eacute;finir une ville et un code postal par d&eacute;faut">
  <img src="templates/default/images/autres.png" border="0" alt="D&eacute;finir une ville et un code postal par d&eacute;faut" align="middle" /></a>
<?php
	}
?> </td>
  </tr>
  <tr>
	<td>Tel 1 :
	<input name="tel1" type="text" maxlength="30" style="width:100px;" title="Format conseill&eacute; : xxx/xx xx xx" tabindex="7" /></td>
	<td>Tel 2 :
	  <input name="tel2" type="text" maxlength="30" title="Format conseill&eacute; : xxx/xx xx xx" style="width:100px;" tabindex="8" /></td>
  </tr>
  <tr>
	<td>Tel 3 :
	<input name="tel3" type="text" maxlength="30" title="Format conseill&eacute; : xxx/xx xx xx" style="width:100px;" tabindex="9" /></td>
	<td>Tel 4 :
	  <input name="tel4" type="text" size="20" maxlength="30" title="Format conseill&eacute; : xxx/xx xx xx" style="width:100px;" tabindex="10" /></td>
  </tr>
  <tr>
	<td colspan="2">Email :
	  <input name="email" type="text" size="40" maxlength="255" style="width:220px;" tabindex="11" /></td>
  </tr>
  <tr>
	<td colspan="2">Email 2 :
	  <input name="email2" type="text" size="40" maxlength="255" style="width:220px;" tabindex="12" /></td>
  </tr>
</table>
</fieldset>
<fieldset>
<legend>Infos sur les parents</legend>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr> 
	  <td>Nom complet du p&egrave;re :</td>
	  <td><input name="nom_pere" type="text" id="nom_pere" style="width:160px" maxlength="255" tabindex="13" />
      (pr&eacute;nom + NOM)</td>
    </tr>
    <tr> 
	  <td>Nom complet de la m&egrave;re :</td>
	  <td><input name="nom_mere" type="text" id="nom_mere" style="width:160px" maxlength="255" tabindex="14" />
      (pr&eacute;nom + NOM)</td>
    </tr>
    <tr> 
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
    <tr> 
	  <td>Profession du p&egrave;re :</td>
	  <td><input name="profession_pere" type="text" id="profession_pere" style="width:160px" maxlength="255" tabindex="15" /></td>
    </tr>
    <tr>
	  <td>Profession de la m&egrave;re :</td>
	  <td><input name="profession_mere" type="text" id="profession_mere" style="width:160px" maxlength="255" tabindex="16" /></td>
    </tr>
  </table>
</fieldset>
<fieldset>
<legend>Remarques &eacute;ventuelles</legend>
<p><textarea name="rmq" cols="35" rows="4" tabindex="17"></textarea></p>
</fieldset>
<p align="center">Les champs marqu&eacute;s d'une * sont obligatoires, les autres 
  sont conseill&eacute;s ... fortement <img src="img/smileys/001.gif" alt="" width="15" height="15" /></p>
<p align="center">
  <input type="submit" name="Submit" id="envoi" value="Cr&eacute;er cette famille" tabindex="18" />
</p>
</form>
<div class="instructions">
  <h2>Adresse principale</h2>
  <p>La gestion de l'unit&eacute; repose sur les familles. Chaque famille peut contenir
    plusieurs membres qui habitent sous le m&ecirc;me toit. Ce principe augmente l'efficacit&eacute;
    de la gestion des membres : un seul courrier par famille, demandes de cotisations
  en une fois, ... </p>
  <h2>Note</h2>
  <p>M&ecirc;me si tu ne disposes pas encore de toutes les coordonn&eacute;es du nouveau membre, cr&eacute;e sa famille. Seul le nom de famille est obligatoire, tu peux ajouter le reste par la suite. </p>
  <h2>Rmq pour les ajouts d'animateurs :</h2>
  <p>Seules les donn&eacute;es de l'adresse principale sont 
    accessibles au public, c&agrave;d les champs suivants <strong>uniquement</strong> 
    : rue, num&eacute;ro, bo&icirc;te, cp, ville, t&eacute;l&eacute;phone 1, pr&eacute;nom, 
    nom, totem, quali, fonction, email personnel, t&eacute;l&eacute;phone personnel. 
    Toutes les autres donn&eacute;es rel&egrave;vent du domaine priv&eacute; et 
    ne sont accessibles qu'aux animateurs.<br />
    L'email est prot&eacute;g&eacute; contre les moteurs de recherche (protection 
    anti-spam).</p>
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
	else if ($_POST['step'] == 2)
	{
		if (!empty($_POST['nom']))
		{
			$nom = htmlentities(strtoupper($_POST['nom']), ENT_QUOTES);
			$nom_son = soundex2($_POST['nom']);
			$rue = htmlentities($_POST['rue'], ENT_QUOTES);
			$numero = htmlentities($_POST['numero'], ENT_QUOTES);
			$bte = htmlentities($_POST['bte'], ENT_QUOTES);
			$cp = htmlentities($_POST['cp'], ENT_QUOTES);
			$ville = htmlentities($_POST['ville'], ENT_QUOTES);
			$tel1 = htmlentities($_POST['tel1'], ENT_QUOTES);
			$tel2 = htmlentities($_POST['tel2'], ENT_QUOTES);
			$tel3 = htmlentities($_POST['tel3'], ENT_QUOTES);
			$tel4 = htmlentities($_POST['tel4'], ENT_QUOTES);
			$email = (checkmail($_POST['email'])) ? $_POST['email'] : '';
			$email2 = (checkmail($_POST['email2'])) ? $_POST['email2'] : '';
			$nom_pere = htmlentities($_POST['nom_pere'], ENT_QUOTES);
			$nom_mere = htmlentities($_POST['nom_mere'], ENT_QUOTES);
			$profession_pere = htmlentities($_POST['profession_pere'], ENT_QUOTES);
			$profession_mere = htmlentities($_POST['profession_mere'], ENT_QUOTES);
			$rmq = cleanvar($_POST['rmq']);
			$sql = "INSERT INTO ".PREFIXE_TABLES."mb_adresses 
			(nom, nom_son, rue, numero, bte, cp, ville, tel1, tel2, tel3, tel4, email, email2, nom_pere, nom_mere, profession_pere, profession_mere, rmq, ad_createur, ad_datecreation, ad_lastmodifby, ad_lastmodif) 
			values 
			('$nom', '$nom_son', '$rue', '$numero', '$bte', '$cp', '$ville', '$tel1', '$tel2', '$tel3', '$tel4', '$email', '$email2', '$nom_pere', '$nom_mere', '$profession_pere', '$profession_mere', '$rmq', '$user[num]', now(), '$user[num]', now())";
			send_sql($db, $sql);
			$sql = "SELECT numfamille FROM ".PREFIXE_TABLES."mb_adresses WHERE ad_createur = '$user[num]' AND nom = '$nom' AND rue = '$rue' ORDER BY ad_datecreation DESC LIMIT 1";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			$numfamille = $ligne['numfamille'];
			log_this("Cr&eacute;ation fiche famille : $nom ($ligne[numfamille])", "newad");
			if (!empty($email))
			{ // abonnement à la newsletter
				abonnement_newsletter($email, 'Famille '.$nom);
			}
			if (!empty($email2))
			{ // abonnement à la newsletter
				abonnement_newsletter($email2, 'Famille '.$nom);
			}
		}
		else
		{
			header('Location: index.php?page=newad&nummb='.$_POST['nummb'].'&suite='.$_POST['suite'].'&msg=1&prenom='.$_POST['prenom'].'&nom='.$_POST['nom_mb']);
		}
		if ($_POST['suite'] == 'newad')
		{
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET famille2 = '$numfamille', mb_lastmodifby = '$user[num]', mb_lastmodif = now() WHERE nummb = '$_POST[nummb]'";
			send_sql($db, $sql);
			header('Location: index.php?page=fichemb&nummb='.$_POST['nummb']);
		}
		else if ($_POST['suite'] == 'newmb')
		{
			header('Location: index.php?page=newmb&step=2&numfamille='.$numfamille.'&prenom='.$_POST['prenom'].'&nom='.$_POST['nom_mb']);
		}
		else if ($_POST['suite'] == 'newancien')
		{
			header('Location: index.php?page=newancien&numfamille='.$numfamille.'&prenom='.$_POST['prenom'].'&nom='.$_POST['nom_mb']);
		}
	}
	else
	{
		include('404.php');	
	}
}
?>