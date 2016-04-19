<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* modiffamille.php v 1.1 - Modification de la fiche d'une famille de l'Unité
* Gestion de l'Unité
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
	if (!isset($_GET['step']) and !isset($_POST['step']))
	{
		$numfamille = (is_numeric($_POST['numfamille'])) ? $_POST['numfamille'] : $_GET['numfamille'];
		if ($numfamille != '')
		{
			$sql = "SELECT * FROM ".PREFIXE_TABLES."mb_adresses WHERE numfamille = '$numfamille'";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{
				$famille = mysql_fetch_assoc($res);
				if (!defined('IN_SITE'))
				{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Modifier les infos d'une famille</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
				}
?>
<h1>Modifier les infos de la famille <?php echo $famille['nom']; ?></h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.nom.value != "")
	{
		return true;
	}
	else
	{
		alert("Il est nécessaire d'indiquer un nom à la famille.");
		return false;
	}
}
</script>
<form action="modiffamille.php" method="post" name="form1" id="form1" onsubmit="return check_form(this)" class="form_gestion_unite">
  <h2>Informations g&eacute;n&eacute;rales sur la famille <?php echo $famille['nom']; ?></h2>
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="numfamille" value="<?php echo $numfamille; ?>" />
  <fieldset>
  <legend>Nom de famille</legend>
  <table border="0" cellpadding="2" cellspacing="0">
    <td>Nom de famille* : </td>
      <td><input name="nom" type="text" id="nom2" size="40" maxlength="100" value="<?php echo $famille['nom']; ?>" style="width:160px" onchange="this.value = this.value.toUpperCase()" tabindex="1" />
        (NOM) </td>
  </table>
  <p class="petitbleu">Tu peux modifier le nom de famille ci-dessus, ce n'est pas lui qui sert &agrave; faire le lien avec le membre.</p>
  </fieldset>
  <fieldset>
  <legend>Coordonn&eacute;es de la famille</legend>
  <table border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td colspan="2">Adresse :
        <input name="rue" type="text" id="rue" size="30" maxlength="100" value="<?php echo $famille['rue']; ?>" tabindex="6" />
        N&deg;
        <input name="numero" type="text" id="numero" size="4" maxlength="10" value="<?php echo $famille['numero']; ?>" tabindex="7" />
        Bte
        <input name="bte" type="text" id="bte" size="4" maxlength="10" value="<?php echo $famille['bte']; ?>" tabindex="8" />
      </td>
    </tr>
    <tr>
      <td colspan="2">CP :
        <input name="cp" type="text" id="cp" size="5" maxlength="5" value="<?php if ($famille['cp'] != '') {echo $famille['cp'];} else {echo $site['site_code_postal']; }?>" tabindex="9" />
        Ville :
        <input name="ville" type="text" id="ville" size="30" maxlength="100" value="<?php if ($famille['ville'] != '') {echo $famille['ville'];} else {echo $site['site_ville']; }?>" tabindex="10" />
        <?php
	if ($user['niveau']['numniveau'] == 5) 
	{
?>
        <a href="index.php?page=config_site&amp;categorie=groupe" title="D&eacute;finir une ville et un code postal par d&eacute;faut"> <img src="templates/default/images/autres.png" border="0" alt="D&eacute;finir une ville et un code postal par d&eacute;faut" align="middle" /></a>
        <?php
	}
?>
      </td>
    </tr>
    <tr>
      <td>Tel 1 :
        <input name="tel1" type="text" id="tel12" size="20" maxlength="30" value="<?php echo $famille['tel1']; ?>" tabindex="11" />
      </td>
      <td>Tel 2 :
        <input name="tel2" type="text" id="tel22" size="20" maxlength="30" value="<?php echo $famille['tel2']; ?>" tabindex="12" />
      </td>
    </tr>
    <tr>
      <td>Tel 3 :
        <input name="tel3" type="text" id="tel32" size="20" maxlength="30" value="<?php echo $famille['tel3']; ?>" tabindex="13" />
      </td>
      <td>Tel 4 :
        <input name="tel4" type="text" id="tel42" size="20" maxlength="30" value="<?php echo $famille['tel4']; ?>" tabindex="14" />
      </td>
    </tr>
    <tr>
      <td colspan="2">Email :
        <input name="email" type="text" id="email2" size="40" maxlength="255" value="<?php echo $famille['email']; ?>" tabindex="15" /></td>
    </tr>
    <tr>
      <td colspan="2">Email 2 :
        <input name="email2" type="text" id="email22" size="40" maxlength="255" value="<?php echo $famille['email2']; ?>" tabindex="16" /></td>
    </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Infos sur les parents</legend>
  <table border="0" cellpadding="2" cellspacing="0">
    <tr>
      <td>Nom complet du p&egrave;re : </td>
      <td><input name="nom_pere" type="text" id="nom2" maxlength="255" value="<?php echo $famille['nom_pere']; ?>" style="width:160px" tabindex="2" />
        (Pr&eacute;nom + NOM)</td>
    </tr>
    <tr>
      <td>Nom complet de la m&egrave;re : </td>
      <td><input name="nom_mere" type="text" id="nom2" maxlength="255" value="<?php echo $famille['nom_mere']; ?>" style="width:160px" tabindex="3" />
        (Pr&eacute;nom + NOM)</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Profession du p&egrave;re : </td>
      <td><input name="profession_pere" type="text" id="nom2" maxlength="255" value="<?php echo $famille['profession_pere']; ?>" style="width:160px" tabindex="4" /></td>
    </tr>
    <tr>
      <td>Profession de la m&egrave;re : </td>
      <td><input name="profession_mere" type="text" id="nom2" maxlength="255" value="<?php echo $famille['profession_mere']; ?>" style="width:160px" tabindex="5" /></td>
    </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Remarques &eacute;ventuelles</legend>
  <textarea name="rmq" cols="35" rows="4" id="textarea" tabindex="17"><?php echo $famille['rmq']; ?></textarea>
  </fieldset>
  <p align="center">
    <input type="submit" name="Submit" value="Enregistrer les modifications" tabindex="18" />
  </p>
</form>
<?php
			}
		}
		else
		{
?>
<h1>Modifier les infos d'une famille</h1>
<?php
			$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
			if ($res = send_sql($db, $sql))
			{
				$nbre_familles = mysql_num_rows($res);
			}
?>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.numfamille.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de bien vouloir choisir une famille avant d'envoyer le formulaire.");
		return false;
	}
}
</script>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<form action="index.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
  <h2>Choix de la famille</h2>
  <input type="hidden" name="page" value="modiffamille" />
  <p>Choisis parmi les familles pr&eacute;sentes dans la base :
    <?php if ($nbre_familles > 0) { $pl = ($nbre_familles > 1) ? 's' : ''; echo "($nbre_familles famille$pl trouv&eacute;e$pl)"; } ?>
  </p>
  <?php
				if ($nbre_familles > 0)
				{
?>
  <p align="center">
    <select name="numfamille" size="15" tabindex="1">
      <?php
					while ($famille = mysql_fetch_assoc($res))
					{
?>
      <option value="<?php echo $famille[numfamille]; ?>"><?php echo $famille['nom']." (".$famille['adresse'].")"; ?></option>
      <?php
					}
?>
    </select>
  </p>
  <p align="center">
    <input type="submit" value="Modifier la fiche" tabindex="2" />
  </p>
  <?php
				}
				else
				{
?>
  <div class="msg">
    <p class="rmq">Il n'y a aucune famille dans la base pour le moment.</p>
  </div>
  <?php
				}
?>
</form>
<div class="instructions">
  <h2>Astuce</h2>
  <p>Pour retrouver plus rapidement un membre, clique dans la liste et tape la premi&egrave;re lettre de son nom pour t'en approcher.</p>
</div>
<?php
		}
	}
	else if ($_POST['step'] == 2)
	{
		if ($_POST['nom'] != '' and is_numeric($_POST['numfamille']))
		{
			$tel1 = htmlentities($_POST['tel1'], ENT_QUOTES); 
			$tel2 = htmlentities($_POST['tel2'], ENT_QUOTES); 
			$tel3 = htmlentities($_POST['tel3'], ENT_QUOTES); 
			$tel4 = htmlentities($_POST['tel4'], ENT_QUOTES); 
			$nom = htmlentities(strtoupper($_POST['nom']), ENT_QUOTES);
			$nom_son = soundex2($_POST['nom']);
			$rue = htmlentities($_POST['rue'], ENT_QUOTES);
			$ville = htmlentities($_POST['ville'], ENT_QUOTES); 
			$rmq = htmlentities($_POST['rmq'], ENT_QUOTES);
			$nom_pere = htmlentities($_POST['nom_pere'], ENT_QUOTES);
			$nom_mere = htmlentities($_POST['nom_mere'], ENT_QUOTES);
			$profession_pere = htmlentities($_POST['profession_pere'], ENT_QUOTES);
			$profession_mere = htmlentities($_POST['profession_mere'], ENT_QUOTES);
			$email = (checkmail($_POST['email'])) ? $_POST['email'] : '';
			$email2 = (checkmail($_POST['email2'])) ? $_POST['email2'] : '';
			$sql = "UPDATE ".PREFIXE_TABLES."mb_adresses 
			SET nom = '$nom', nom_son = SOUNDEX('$nom_son'), rue = '$rue', numero = '$_POST[numero]', bte = '$_POST[bte]', cp = '$_POST[cp]', 
			ville = '$ville', tel1 = '$tel1', tel2 = '$tel2', tel3 = '$tel3', tel4 = '$tel4', email = '$email', 
			email2 = '$email2', rmq = '$rmq', nom_pere = '$nom_pere', nom_mere = '$nom_mere', 
			profession_pere = '$profession_pere', profession_mere = '$profession_mere', ad_lastmodifby = '$user[num]', ad_lastmodif = now() 
			WHERE numfamille = '$_POST[numfamille]'";
			send_sql($db, $sql);
			if (!empty($email))
			{ // abonnement à la newsletter
				abonnement_newsletter($email, 'Famille '.$nom);
			}
			if (!empty($email2))
			{ // abonnement à la newsletter
				abonnement_newsletter($email2, 'Famille '.$nom);
			}
			log_this("Modification fiche famille : $nom ($_POST[numfamille])", "modiffamille");
			header("Location: index.php?page=modiffamille&step=3");
		}
		else
		{
			header("Location: index.php?page=modiffamille&numfamille=$numfamille");
		}
	}
	else if ($_GET['step'] == 3)
	{
		$sql = "SELECT numfamille, nom FROM ".PREFIXE_TABLES."mb_adresses WHERE ad_lastmodifby = '$user[num]' ORDER BY ad_lastmodif DESC LIMIT 1";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$famille = mysql_fetch_assoc($res);
?>
<h1>Modifier la fiche de la famille <?php echo $famille['nom']; ?></h1>
<div align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></div>
<p align="center">Les donn&eacute;es ont bien &eacute;t&eacute; mises &agrave; jour.</p>
<p align="center"><a href="index.php?page=fichefamille&amp;numfamille=<?php echo $famille['numfamille']; ?>">Voir la fiche de la famille <?php echo $famille['nom']; ?></a></p>
<?php
		}
	}
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
}
?>
