<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* modifprofil.php v 1.1 - Modification du profil de l'utilisateur
* C'est l'utilisateur qui modifie son propre profil sur le portail
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
*	Prise en charge paramètres de taille et de poids définis par le webmaster
*	vérification du nouveau modèle de mot de passe
*/

include_once('connex.php');
include_once('fonc.php');

// configuration du script
$taille_maxi_avatar = (is_numeric($site['avatar_max_filesize'])) ? $site['avatar_max_filesize'] : 10240; // en octets
$w_maxi_avatar = (is_numeric($site['avatar_max_width'])) ? $site['avatar_max_width'] : 100; // en octets
$h_maxi_avatar = (is_numeric($site['avatar_max_height'])) ? $site['avatar_max_height'] : 130; // en octets

if ($user['niveau']['numniveau'] < 1)
{ // l'utilisateur n'est pas identifié
	include('404.php');
}
else
{
	if ($_POST['step'] == '2')
	{ // enregistrement des données du profil du membre
		$prenom = htmlentities($_POST['prenom'], ENT_QUOTES);
		$nom = htmlentities(strtoupper($_POST['nom']), ENT_QUOTES);
		$email = htmlentities($_POST['email'], ENT_QUOTES);
		$siteweb = htmlentities($_POST['siteweb'], ENT_QUOTES);
		$loisirs = htmlentities($_POST['loisirs'], ENT_QUOTES);
		$profilmembre = htmlentities($_POST['profilmembre'], ENT_QUOTES);
		$totem_scout = htmlentities($_POST['totem_scout'], ENT_QUOTES);
		$quali_scout = htmlentities($_POST['quali_scout'], ENT_QUOTES);
		$totem_jungle = htmlentities($_POST['totem_jungle'], ENT_QUOTES);
		$supplsql = '';
		$okpw = 0;
		if ($_POST['newpw'] == 1 and strlen($_POST['pw']) >= 6 and $_POST['pw'] == $_POST['pwconfirm'])
		{ // le nouveau mot de passe est bon
			if (untruc(PREFIXE_TABLES.'auteurs', 'pw', 'num', $user['num']) == md5(UN_PEU_DE_SEL.$_POST['old_pw']))
			{ // et l'actuel a été entré correctement donc on change le pw dans la db
				$pw = md5(UN_PEU_DE_SEL.$_POST['pw']);
				$supplsql = "pw = '$pw', newpw = '0',";
				$okpw = 1;
			}
			else
			{ // par contre le pw actuel n'est pas bon, on le dit à l'utilisateur
				$okpw = 2;
			}
		}
		else if ($_POST['newpw'] == 1 and (strlen($_POST['pw']) < 6 or $_POST['pw'] != $_POST['pwconfirm']))
		{ // le nouveau mot de passe est incorrect
			$okpw = 2;
		}
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET $supplsql prenom = '$prenom', nom = '$nom', siteweb = '$siteweb', profilmembre = '$profilmembre', 
		loisirs = '$loisirs', totem_scout = '$totem_scout', quali_scout = '$quali_scout', totem_jungle = '$totem_jungle', 
		majprofildone = '1', majprofildate = now() WHERE num = '$user[num]'";
		send_sql($db, $sql);
		$redir = 'index.php?page=modifprofil&step=3';
		$redir .= ($okpw != 0) ? '&okpw='.$okpw : '';
		header('Location: '.$redir);
	}
	if ($_GET['step'] == 3)
	{
?>
<h1>Modifier mon profil</h1>
<p align="center"> <a href="index.php?page=membres">Retour &agrave; la page 
  d'accueil des membres</a></p>
<div class="msg">
<p align="center">Ton profil a bien &eacute;t&eacute; mis &agrave; jour<?php echo ($_GET['okpw'] == 1) ? ' ainsi que ton mot de passe' : ''; ?></p>
<?php
		if ($_GET['okpw'] == 2)
		{
			echo '<p align="center" class="rmq">Ton mot de passe n\'a pas pu &ecirc;tre modifi&eacute;</p>';
		}
?>		
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>">Voir 
  mon profil</a></p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'modifprofil.htm' : 'index.php?page=modifprofil'; ?>">Modifier 
  mon profil &agrave; nouveau</a></p>
</div>
<?php
	}
	if ($user['num'] != 0 and !isset($_POST['step']) and $_GET['step'] != 3)
	{	
		$sql = "SELECT * FROM ".PREFIXE_TABLES."auteurs WHERE num = $user[num]";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<?php
			if (!defined('IN_SITE'))
			{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Modifier ma fiche de membre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
			}
?>
<h1>Modifier mon profil</h1>
  <p align="center"><a href="index.php?page=membres">Retour &agrave; la page 
    d'accueil des membres</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function change_pw(form)
{
	if (!form.newpw.checked && (form.pw.value != ""  || form.pwconfirm.value != ""))
	{
		form.newpw.checked = true;
	}
}
function check_form(form)
{
	change_pw(form);
	if (form.newpw.checked && (form.pw.value.length < 6 || form.pwconfirm.value.length < 6))
	{
		alert("Ton nouveau mot de passe doit comporter au moins 6 caractères !");
		form.pw.value = "";
		form.pwconfirm.value = "";
		return false;
	}
	else
	{
		if (form.newpw.checked && form.pw.value != form.pwconfirm.value)
		{
			alert("Ton nouveau mot de passe est incorrect");
			form.pw.value = "";
			form.pwconfirm.value = "";
			form.pw.focus();
			return false;
		}
		else if (form.newpw.checked && form.old_pw.value == '')
		{
			alert("Tu n'as pas entré ton mot de passe actuel");
			form.old_pw.focus();
			return false;
		}
		if (form.prenom.value != "" && form.nom.value != "")
		{
			return true;
		}
		else
		{
			alert("Merci d'indiquer ton prénom et ton nom.");
			form.prenom.focus();
			return false;
		}
	}
}
//-->
</script>
<form action="modifprofil.php" method="post" name="form" class="form_config_site" id="form" onSubmit="return check_form(this);">
<h2>Mon profil<input type="hidden" name="step" value="2" /></h2>
<fieldset>
<legend>Informations de base</legend>
  <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr valign="top" class="td-gris">
      <td>Pseudo : </td>
      <td class="rmqbleu"><?php echo $membre['pseudo'];?></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>adresse email : </td>
      <td class="rmqbleu"><?php echo $membre['email']; ?></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Statut actuel :</td>
      <td class="rmqbleu"><?php
			echo $niveaux[$membre['niveau']]['nomniveau'];
?>
      </td>
    </tr>
<?php
			if ($membre['nivdemande'] > 0)
			{
?>
    <tr valign="top" class="td-gris">
      <td>Statut demand&eacute; :</td>
      <td class="rmqbleu"><?php
				echo $niveaux[$membre['nivdemande']]['nomniveau'];
?>
      </td>
    </tr>
<?php
			}
?>
<?php
			if ($membre['numsection'] > 0)
			{
?>
    <tr valign="top" class="td-gris">
      <td>Section :</td>
      <td class="rmqbleu"><?php
				echo $sections[$membre['numsection']]['nomsection'];
?>
      </td>
    </tr>
<?php
			}
?>
  </table>
<p class="petitbleu">Pour modifier les donn&eacute;es ci-dessus,
 contacte le webmaster.</p>
</fieldset>
<fieldset>
<legend>Donn&eacute;es personnelles</legend>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr valign="top" class="td-gris">
      <td>Pr&eacute;nom :</td>
      <td>
        <input name="prenom" type="text" class="case" tabindex="1" value="<?php echo $membre['prenom'];?>" size="40" maxlength="100" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Nom :</td>
      <td>
        <input name="nom" type="text" class="case" tabindex="1" onchange="this.value=this.value.toUpperCase()" value="<?php echo $membre['nom'];?>" size="40" maxlength="100" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Totem :</td>
      <td>
        <input name="totem_scout" type="text" class="case" tabindex="3" value="<?php echo $membre['totem_scout'];?>" size="40" maxlength="100" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Quali :</td>
      <td>
        <input name="quali_scout" type="text" class="case" tabindex="4" value="<?php echo $membre['quali_scout'];?>" size="40" maxlength="100" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Totem de jungle :</td>
      <td>
        <input name="totem_jungle" type="text" class="case" tabindex="5" value="<?php echo $membre['totem_jungle'];?>" size="40" maxlength="100" />
      </td>
    </tr>
  </table>
</fieldset>
<fieldset>
<legend>Personnalise ton profil</legend>
<p align="center">Pr&eacute;sente-toi en quelques mots
  <textarea name="profilmembre" cols="50" rows="6" tabindex="6"><?php echo stripslashes($membre['profilmembre']); ?></textarea>
</p>
<p>Site web : <input name="siteweb" type="text" id="siteweb" value="<?php echo $membre['siteweb']; ?>" size="40" maxlength="255" tabindex="7" /></p>
<p>Loisirs : <input name="loisirs" type="text" id="loisirs" value="<?php echo $membre['loisirs']; ?>" size="40" maxlength="255" tabindex="8" /></p>
</fieldset>
<fieldset>
<legend><label for="newpw">Modifier mon mot de passe </label>
 <input type="checkbox" name="newpw" id="newpw" value="1"<?php echo (isset($_GET['newpw'])) ? ' checked="checked"' : ''; ?> tabindex="9" /></legend>
<table border="0" cellpadding="2" cellspacing="0">
    <tr valign="top" class="td-gris">
      <td>Mot de passe actuel :</td>
      <td><input type="password" name="old_pw" id="oldpw" class="case"<?php echo (isset($_GET['newpw'])) ? ' style="background-color: #FFFFCC"' : ''; ?> tabindex="10" onchange="change_pw(this.form);" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Nouveau mot de passe :</td>
      <td><input type="password" name="pw" id="pw" class="case"<?php echo (isset($_GET['newpw'])) ? ' style="background-color: #FFFFCC"' : ''; ?> tabindex="11" onchange="change_pw(this.form);" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Confirmation :</td>
      <td><input type="password" name="pwconfirm" id="pwconfirm" maxlength="32" class="case"<?php echo (isset($_GET['newpw'])) ? ' style="background-color: #FFFFCC"' : ''; ?> tabindex="12" onchange="change_pw(this.form);" />
      </td>
    </tr>
  </table>
</fieldset>
<fieldset>
<legend>Informations non modifiables</legend>
<table border="0" cellpadding="2" cellspacing="0">
<?php
			if ($membre['assistantwebmaster'] == 1)
			{
?>
<tr valign="top" class="td-gris">
  <td colspan="2">tu es co-webmaster du portail : tu peux cr&eacute;er et
	modifier les pages du portail</td>
</tr>
<tr valign="top">
  <td colspan="2">&nbsp;</td>
</tr>
<?php
			}
?>
<tr valign="top" class="td-gris">
  <td>Membre du portail depuis le :</td>
  <td class="rmqbleu"><?php echo date_ymd_dmy($membre['dateinscr'], 'enlettres'); ?></td>
</tr>
<tr valign="top" class="td-gris">
  <td>IP inscription :</td>
  <td class="rmqbleu"><?php echo $membre['ipinscription']; ?></td>
</tr>
<?php
			if (!empty($membre['autorise']))
			{
?>
<tr valign="top" class="td-gris">
  <td colspan="2">Statut d'animateur reconnu par <span class="rmqbleu"><?php echo $membre['autorise']; ?></span></td>
</tr>
<?php
			}
?>
<tr valign="top">
  <td colspan="2">&nbsp;</td>
</tr>
<tr valign="top" class="td-gris">
  <td>Nombre de connexions :</td>
  <td class="td-1"><?php echo $membre['nbconnex']; ?></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Derni&egrave;re connexion :</td>
  <td class="td-1"><?php echo date_ymd_dmy($membre['lastconnex'], 'dateheure'); ?></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Nombre de pages vues :</td>
  <td class="td-1"><?php echo $membre['pagesvues']; ?></td>
</tr>
</table>
</fieldset>
<p align="center">
  <input type="submit" name="Submit" value="Enregistrer les modifications ci-dessus*" tabindex="13" />
</p>
<p class="petitbleu">* En cliquant sur ce bouton, tu ne fais 
    aucune modification &agrave; ton avatar</p>
</form>
<form action="avatar_upload.php" method="post" enctype="multipart/form-data" class="form_config_site">
<h2 align="left">Mon avatar
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $taille_maxi_avatar; ?>" />
      <input type="hidden" name="do" value="send" />
</h2>
<div style="float: left; padding:0.5em; margin:0.5em; width:150px; height:150px; text-align:center;">
  <?php $lavatar = show_avatar($membre['avatar']); if (empty($lavatar)) {echo 'pas d\'avatar';} else {echo $lavatar;} ?>
</div>
<p>S&eacute;lectionne ton avatar sur ton ordinateur :<br />
  <input type="file" name="userfile" size="30" tabindex="13" /></p>

<p class="petit">Ton avatar doit respecter les trois conditions suivantes :<br />
- pas plus de <?php echo taille_fichier($taille_maxi_avatar); ?><br />
- taille maximale <?php echo $w_maxi_avatar.' x '.$h_maxi_avatar; ?> pixels<br />
- uniquement fichier GIF, JPG ou PNG</p>
<p align="center"><input type="submit" name="Submit2" value="Enregistrer mon avatar" tabindex="14" />
&nbsp;&nbsp;
	<input type="button" name="Submit3" value="Supprimer mon avatar" onclick="window.location='avatar_upload.php?do=delete';"15 />
</p>
<p class="petitbleu">Si tu viens de changer d'avatar, il se peut que l'ancien
  avatar apparaisse encore. C'est un probl&egrave;me de cache de ton navigateur. <br />
  Actualise ta page et le nouvel avatar devrait appara&icirc;tre.</p>
</form>
<?php
		} // fin num_rows == 1
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