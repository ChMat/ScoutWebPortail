<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* modifmembresite.php v 1.1 - Modification du profil d'un utilisateur du portail
* C'est le webmaster qui modifie un profil
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
*	Ajout gestion des avatars des membres par le webmaster
*	Prise en charge paramètres de taille et de poids définis par le webmaster
*	vérification du mot de passe webmaster pour modifier le mot de passe utilisateur
*	passage au nouveau modèle de mot de passe
*/

include_once('connex.php');
include_once('fonc.php');

// configuration du script
$taille_maxi_avatar = (is_numeric($site['avatar_max_filesize'])) ? $site['avatar_max_filesize'] : 10240; // en octets
$w_maxi_avatar = (is_numeric($site['avatar_max_width'])) ? $site['avatar_max_width'] : 100; // en octets
$h_maxi_avatar = (is_numeric($site['avatar_max_height'])) ? $site['avatar_max_height'] : 130; // en octets

if ($user['niveau']['numniveau'] < 5)
{
	include('404.php');
}
else
{
	$tri = array('numniveau', 'nomniveau');
	$n_niveaux = super_sort($niveaux);
	if (empty($_GET['step']) and empty($_POST['step']))
	{
		$num = (is_numeric($_GET['num'])) ? $_GET['num'] : '';
		if (!empty($num))
		{
			$sql = "SELECT * FROM ".PREFIXE_TABLES."auteurs WHERE num = '$num'";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{
				$membre = mysql_fetch_assoc($res);
				if (!defined('IN_SITE'))
				{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Modifier la fiche d'un membre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
				}
?>
<h1>Modifier l'inscription de <?php echo $membre['prenom'].' '.$membre['nom']; ?> 
  sur le portail</h1>
<p align="center"> <a href="index.php?page=gestion_mb_site">Retour &agrave; la Gestion des membres 
  du portail</a></p>
<form action="modifmembresite.php" method="post" name="form" class="form_config_site" id="form">
  <h2>
    <input type="hidden" name="step" value="2" />
    <input type="hidden" name="num" value="<?php echo $membre['num']; ?>" />
Modifier le profil de <?php echo $membre['pseudo']; ?> </h2>
<fieldset>
<legend>Son statut sur le site</legend>
<table border="0" cellpadding="0" cellspacing="1">
<?php
				$can_change = true;
				if ($niveaux[$membre['niveau']]['numniveau'] == 5)
				{ // afin d'&eacute;viter que le portail se retrouve sans webmaster, on v&eacute;rifie qu'il existe un autre webmaster sur le portail
				  // avant d'autoriser le webmaster &agrave; changer le statut de webmaster d'un membre
					$sql = "SELECT * FROM ".PREFIXE_TABLES."auteurs, ".PREFIXE_TABLES."site_niveaux WHERE idniveau = niveau AND numniveau = '5'";
					$res = send_sql($db, $sql);
					$can_change = (mysql_num_rows($res) > 1) ? true : false;
				}
				if ($can_change)
				{
?>
<tr valign="top" class="td-gris">
  <td>Statut actuel :</td>
  <td><select name="niveau" tabindex="2">
	<option value="0"></option>
<?php
					foreach ($n_niveaux as $ligne)
					{
?>
	<option value="<?php echo $ligne['idniveau']; ?>"<?php if ($ligne['idniveau'] == $membre['niveau']) echo ' selected'; ?>><?php echo $ligne['nomniveau']; ?></option>
<?php
					}
?>
  </select></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Statut demand&eacute; :</td>
  <td><select name="nivdemande" tabindex="3">
  <option value="0"></option>
<?php
					foreach ($n_niveaux as $ligne)
					{
?>
  <option value="<?php echo $ligne['idniveau']; ?>"<?php if ($ligne['idniveau'] == $membre['nivdemande']) echo ' selected'; ?>><?php echo $ligne['nomniveau']; ?></option>
<?php
					}
?>
  </select></td>
</tr>
<?php
				}
				else
				{
?>
<tr valign="top" class="td-gris">
  <td>Statut actuel :</td>
  <td class="rmqbleu"><input type="hidden" name="niveau" value="<?php echo $membre['niveau']; ?>" />
	  <input type="hidden" name="nivdemande" value="<?php echo $membre['nivdemande']; ?>" />
  Webmaster </td>
</tr>
<tr valign="top">
  <td colspan="2" class="petitbleu">Tu es le seul webmaster du portail, changer
	ton statut t'emp&ecirc;cherait de reprendre la main sur le portail.</td>
</tr>
<?php
				}
?>
</table>
</fieldset>
<fieldset>
<legend>Donn&eacute;es personnelles</legend>
<table border="0" cellpadding="0" cellspacing="1">
<tr valign="top" class="td-gris">
  <td>Son adresse email : </td>
  <td><input name="email" type="text" value="<?php echo $membre['email']; ?>" size="40" maxlength="255" tabindex="1" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Pseudo : </td>
  <td><input name="monpseudo" type="text" class="case" tabindex="4" value="<?php echo $membre['pseudo'];?>" size="40" maxlength="32" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Pr&eacute;nom :</td>
  <td><input name="prenom" type="text" class="case" tabindex="5" value="<?php echo $membre['prenom'];?>" size="40" maxlength="100" />
  </td>
</tr>
<tr valign="top" class="td-gris">
  <td>Nom :</td>
  <td><input name="nom" type="text" class="case" tabindex="6" value="<?php echo $membre['nom'];?>" size="40" maxlength="100" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Totem</td>
  <td><input name="totem_scout" type="text" id="totem" tabindex="7" value="<?php echo $membre['totem_scout'];?>" size="40" maxlength="100" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Quali</td>
  <td><input name="quali_scout" type="text" id="quali" tabindex="8" value="<?php echo $membre['quali_scout'];?>" size="40" maxlength="100" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Totem de jungle</td>
  <td><input name="totem_jungle" type="text" id="totem_jungle" tabindex="9" value="<?php echo $membre['totem_jungle'];?>" size="40" maxlength="100" /></td>
</tr>
</table>
</fieldset>
<fieldset>
<legend>Profil personnalis&eacute;</legend>
<table border="0" cellpadding="0" cellspacing="1">
<tr valign="top" class="td-gris">
  <td>Site web</td>
  <td><input name="siteweb" type="text" id="siteweb" tabindex="10" value="<?php echo $membre['siteweb'];?>" size="40" maxlength="255" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Loisirs</td>
  <td><input name="loisirs" type="text" class="case" id="loisirs" tabindex="11" value="<?php echo stripslashes($membre['loisirs']); ?>" size="40" maxlength="255" />
  </td>
</tr>
</table>
<p align="center">Message personnel : <br />
  <textarea name="profilmembre" rows="6" tabindex="12"><?php echo stripslashes($membre['profilmembre']);?></textarea>
</p>
</fieldset>
<fieldset>
<legend>Donn&eacute;es techniques du profil</legend>
<table border="0" cellpadding="0" cellspacing="1">
<tr valign="top" class="td-gris">
  <td colspan="2">co-webmaster :
	  <input type="radio" name="assistantwebmaster" value="1" id="assistantwebmasteroui"<?php if ($membre['assistantwebmaster'] == 1) {echo ' checked="checked"';} ?> tabindex="13" />
  <label for="assistantwebmasteroui">Oui</label>
	  <input type="radio" name="assistantwebmaster" value="0" id="assistantwebmasternon"<?php if ($membre['assistantwebmaster'] == 0) {echo ' checked="checked"';} ?> tabindex="14" />
  <label for="assistantwebmasternon">Non</label></td>
</tr>
<tr valign="top" class="td-gris">
  <td colspan="2">Banni :
	  <input type="radio" name="banni" value="1" id="bannioui"<?php if ($membre['banni'] == '1') {echo ' checked="checked"';} ?><?php echo ($user['num'] == $membre['num']) ? ' title="Tu ne peux pas te bannir toi-m&ecirc;me" disabled="true"' : ''; ?> tabindex="15" />
  <label for="bannioui">Oui</label>
	  <input type="radio" name="banni" value="0" id="banninon"<?php if ($membre['banni'] == '0') {echo ' checked="checked"';} ?> tabindex="16" />
  <label for="banninon">Non</label></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Date inscription</td>
  <td><input type="text" name="dateinscr" maxlength="32" class="case" value="<?php echo $membre['dateinscr']; ?>" tabindex="17" title="Format aaaa-mm-jj hh:mm:ss" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>IP inscription</td>
  <td><input name="ipinscription" type="text" class="case" tabindex="18" title="Format : xxx.xxx.xxx.xxx" value="<?php echo $membre['ipinscription']; ?>" maxlength="32" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Garant</td>
  <td><input type="text" name="autorise" maxlength="32" class="case" value="<?php echo $membre['autorise']; ?>" tabindex="19" title="Pseudo du membre qui a accord&eacute; le statut d'animateur &agrave; ce membre" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td colspan="2">Afficher aide :
	  <input type="radio" name="affaide" value="1" id="radio3"<?php if ($membre['affaide'] == '1') {echo ' checked="checked"';} ?> tabindex="20" />
 <label for="radio3">Oui</label>
	  <input type="radio" name="affaide" value="" id="radio4"<?php if ($membre['affaide'] == '') {echo ' checked="checked"';} ?> tabindex="21" />
  <label for="radio4">Non</label>
  </td>
</tr>
<tr valign="top" class="td-gris">
  <td>Nombre de connexions</td>
  <td><input type="text" name="nbconnex" maxlength="32" class="case" value="<?php echo $membre['nbconnex']; ?>" tabindex="22" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Derni&egrave;re connexion</td>
  <td><input type="text" name="lastconnex" maxlength="32" class="case" value="<?php echo $membre['lastconnex']; ?>" tabindex="23" title="Format aaaa-mm-jj hh:mm:ss" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Pages vues</td>
  <td><input type="text" name="pagesvues" maxlength="32" class="case" value="<?php echo $membre['pagesvues']; ?>" tabindex="24" /></td>
</tr>
<tr valign="top" class="td-gris">
  <td>Cl&eacute; d'activation </td>
  <td><input type="text" name="clevalidation" value="<?php echo $membre['clevalidation']; ?>" tabindex="25" title="Cette case est vide si le membre a termin&eacute; son inscription" /></td>
</tr>
</table>
</fieldset>
<fieldset>
<legend><label for="newpw">Modifier son mot de passe</label> <input type="checkbox" name="newpw" id="newpw" value="1" tabindex="26" /></legend>
<p class="petitbleu">Si le mot de passe est modifi&eacute;, il sera 
  propos&eacute; au membre de s'en choisir un nouveau, plus personnel. 
  Cependant tu dois communiquer toi-m&ecirc;me le nouveau mot de passe 
  au membre.</p>
<p>Nouveau mot de passe : 
	  <input type="text" name="pw" maxlength="100" class="case" value="" tabindex="27" onchange="if (this.value != '') {getElement('newpw').checked = true;} else {getElement('newpw').checked = true;}" />
</p>
<p><strong>Mesure de s&eacute;curit&eacute;</strong><br />
  Entre ici ton mot de passe (Webmaster) :
    <input type="password" name="webmaster_pw" maxlength="100" class="case" value="" tabindex="28" />
</p>
</fieldset>
<p align="center">Les modifications ont effet imm&eacute;diat sur le membre.<br />
  <input type="submit" name="Submit" value="Enregistrer les modifications" tabindex="29" />
</p>
</form>
<form action="avatar_upload.php" method="post" enctype="multipart/form-data" class="form_config_site">
  <h2>Son avatar
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $taille_maxi_avatar; ?>" />
      <input type="hidden" name="do" value="send" />
      <input type="hidden" name="mb" value="<?php echo $membre['num']; ?>" />
  </h2>
<div style="float: left; padding:0.5em; margin:0.5em; width:150px; height:150px; text-align:center;">
  <?php $lavatar = show_avatar($membre['avatar']); if (empty($lavatar)) {echo 'pas d\'avatar';} else {echo $lavatar;} ?>
</div>

<p>S&eacute;lectionne l'avatar du membre sur ton ordinateur :<br />
  <input type="file" name="userfile" size="30" tabindex="30" /></p>
<p class="petit">L'avatar doit respecter les trois conditions suivantes :<br />
- pas plus de <?php echo taille_fichier($taille_maxi_avatar); ?><br />
- taille maximale <?php echo $w_maxi_avatar.' x '.$h_maxi_avatar; ?> pixels<br />
- uniquement fichier GIF, JPG ou PNG</p>
<p align="center"><input type="submit" name="Submit2" value="Enregistrer son avatar" tabindex="31" />
&nbsp;&nbsp;<input type="button" name="Submit3" value="Supprimer son avatar" onclick="window.location='avatar_upload.php?do=delete&amp;mb=<?php echo $membre['num']; ?>';" tabindex="32" />
</p>
<p class="petitbleu">Si tu viens de changer l'avatar, il se peut que l'ancien
  apparaisse encore. C'est un probl&egrave;me de cache de ton navigateur. <br />
  Actualise la page et le nouvel avatar devrait appara&icirc;tre. </p>
</form>
<?php
			}
			else
			{
?>
<div class="msg">
  <p align="center" class="rmq">Ce membre n'existe pas !</p>
</div>
<?php
			}
		}
		else
		{
?>
<h1>Gestion des membres du portail</h1>
<?php
			$sql = "SELECT num, pseudo, email FROM ".PREFIXE_TABLES."auteurs ORDER BY pseudo ASC";
			if ($res = send_sql($db, $sql))
			{
				$nbre_membres = mysql_num_rows($res);
			}
?>
<p align="center"><a href="?page=gestion_mb_site">Retour &agrave; la Gestion des 
  membres du portail</a></p>
<div class="instructions">
  <p>Tu peux cr&eacute;er un nouveau compte en entrant l'adresse email du futur 
  membre ci-dessous. Le fait de lancer une inscription par cette page-ci est en 
  tous points similaire &agrave; une inscription normale; c&agrave;d que le futur 
  membre va recevoir un mail contenant la cl&eacute; d'activation du compte et 
  pourra continuer son inscription lui-m&ecirc;me.</p>
</div>
<form action="inscr.php" method="post" name="inscription1" class="form_config_site" id="inscription1" onsubmit="return check_form2(this);">
<h2>Initier une nouvelle inscription</h2>
<script language="JavaScript" type="text/JavaScript">
<!--
function check_form2(form)
{
	if (form.email.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci d'indiquer une adresse email.");
		return false;
	}
}
//-->
</script>
<p align="center">L'adresse email du futur membre : 
  <input name="email" type="text" id="email" maxlength="255" style="width:140px;" tabindex="1" />
  <input name="go" type="submit" id="go" value="Envoyer" tabindex="2" />
  <input name="step" type="hidden" id="step" value="2" />
</p>
</form>
<?php
		}
	}
	else if ($_POST['step'] == 2)
	{ // enregistrement des modifications apportées au profil du membre
		$plus = '';
		if (!empty($_POST['monpseudo']) and !empty($_POST['nom']) and !empty($_POST['prenom']) and !empty($_POST['num']))
		{
			$okpw = '';
			if ($_POST['newpw'] == 1 and !empty($_POST['pw']))
			{ // Le webmaster modifie le mot de passe de l'utilisateur
				if (untruc(PREFIXE_TABLES.'auteurs', 'pw', 'num', $user['num']) == md5(UN_PEU_DE_SEL.$_POST['webmaster_pw']) and $user['niveau']['numniveau'] == 5)
				{ // Après vérification du mot de passe du webmaster, on accepte la modification du mot de passe
					$pw = md5(UN_PEU_DE_SEL.$_POST['pw']);
					$plus = ", pw = '$pw', newpw = '1'";
					$okpw = '&pw=ok';
					if ($site['update_pw'] == 'en_cours')
					{ // le portail a été mis à jour depuis la 1.0. On supprime le cas échéant la référence au membre
						$sql = "DELETE FROM ".PREFIXE_TABLES."auteurs_pw_v11 WHERE num = '".$_POST['num']."' LIMIT 1";
						send_sql($db, $sql);
					}
				}
				else
				{
					$okpw = '&pw=erreur';
				}
			}
			else
			{
				$plus = '';			
			}
			$monpseudo = htmlentities($_POST['monpseudo'], ENT_QUOTES);
			$prenom = htmlentities($_POST['prenom'], ENT_QUOTES); 
			$nom = htmlentities(strtoupper($_POST['nom']), ENT_QUOTES); 
			$email = htmlentities($_POST['email'], ENT_QUOTES); 
			$totem_scout = htmlentities($_POST['totem_scout'], ENT_QUOTES); 
			$quali_scout = htmlentities($_POST['quali_scout'], ENT_QUOTES); 
			$totem_jungle = htmlentities($_POST['totem_jungle'], ENT_QUOTES); 
			$profilmembre = htmlentities($_POST['profilmembre'], ENT_QUOTES); 
			$loisirs = htmlentities($_POST['loisirs'], ENT_QUOTES); 
			$section = $niveaux[$_POST['niveau']]['section_niveau'];
			$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET 
			pseudo = '$monpseudo', prenom = '$prenom', nom = '$nom', email = '$email', 
			niveau = '$_POST[niveau]', nivdemande = '$_POST[nivdemande]', assistantwebmaster = '$_POST[assistantwebmaster]', 
			numsection = '$section', dateinscr = '$_POST[dateinscr]', banni = '$_POST[banni]', autorise = '$_POST[autorise]', 
			ipinscription = '$_POST[ipinscription]', nbconnex = '$_POST[nbconnex]', affaide = '$_POST[affaide]', pagesvues = '$_POST[pagesvues]', 
			lastconnex = '$_POST[lastconnex]', siteweb = '$_POST[siteweb]', profilmembre = '$profilmembre',
			loisirs = '$loisirs', totem_scout = '$totem_scout', quali_scout = '$quali_scout', totem_jungle = '$totem_jungle', clevalidation = '$_POST[clevalidation]' $plus
			WHERE num = '$_POST[num]'";
			send_sql($db, $sql);
			header('Location: index.php?page=modifmembresite&step=3&num='.$_POST['num'].$okpw);
		}
		else
		{
			header('Location: index.php?page=modifmembresite&num='.$_POST['num']);
		}
	}
	else if ($_GET['step'] == 3)
	{ // Message confirmant que les données ont bien été modifiées
		$sql = "SELECT num, pseudo FROM ".PREFIXE_TABLES."auteurs WHERE num = $_GET[num]";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<h1>Modifier la fiche de <?php echo $membre['pseudo']; ?></h1>
		
<p align="center"> <a href="index.php?page=membres">Retour &agrave; la page d'accueil 
  membres</a> - <a href="index.php?page=gestion_mb_site">Retour &agrave; la Gestion des 
  membres du portail</a></p>
<div class="msg">
		<p align="center">Les donn&eacute;es ont bien &eacute;t&eacute; mises &agrave; jour.</p>
<?php
			if ($_GET['pw'] == 'ok')
			{
?>
		<p align="center">Et le mot de passe a bien &eacute;t&eacute; modifi&eacute;.</p>
<?php
			}
			else if ($_GET['pw'] == 'erreur')
			{
?>
		<p align="center" class="rmq">Le mot de passe n'a pas &eacute;t&eacute; modifi&eacute;.</p>
<?php
			}
?>
<p align="center"><a href="?page=modifmembresite&amp;num=<?php echo $membre['num']; ?>">Modifier 
  &agrave; nouveau sa fiche</a><br />
  <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$membre['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$membre['num']; ?>">Voir sa fiche membre</a></p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
  <p align="center" class="rmq">Une erreur s'est produite</p>
</div>
<?php
	}
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