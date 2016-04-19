<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* createmembresite.php v 1.1 - Création d'un membre du portail
* Cette fonction peut être utilisée par le webmaster pour faire des tests 
* ou aider un utilisateur en difficulté à l'inscription
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
* Modifications v 1.1 : ChMat
*	adaptation cleunique() en cleunique('mini')
*	protection $_POST['niveau']
*	adaptation du hashage du mot de passe
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 4)
{
	include('404.php');
}
else
{
	$tri = array('numniveau', 'nomniveau');
	$n_niveaux = super_sort($niveaux);
	if (!isset($_GET['step']) and !isset($_POST['step']))
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Cr&eacute;er un membre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des membres du portail </h1>
<p align="center"> <a href="index.php?page=gestion_mb_site">Retour &agrave; la Gestion des membres 
  du portail</a></p>
<p class="introduction">Cette page te permet de cr&eacute;er un compte sur le portail sans passer par 
  le processus d'inscription des membres. Tu peux donc facilement cr&eacute;er 
  des comptes de test ou donner un coup de main &agrave; un membre qui n'arrive 
  pas &agrave; s'inscrire.</p>
<form action="createmembresite.php" method="post" name="form" class="form_config_site" id="form">
<h2>Cr&eacute;er un nouveau membre</h2>
<fieldset>
<legend><input type="hidden" name="step" value="2" />
    Informations du membre </legend>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td colspan="2">Son adresse email* :
          <input name="email" type="text" size="40" maxlength="255" tabindex="1" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Statut actuel* :</td>
      <td><select name="niveau" tabindex="2">
          <option value="0">Aucun</option>
          <?php
		foreach ($n_niveaux as $ligne)
		{
?>
          <option value="<?php echo $ligne['idniveau']; ?>"><?php echo $ligne['nomniveau']; ?></option>
          <?php
		}
?>
      </select></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Statut demand&eacute; :</td>
      <td><select name="nivdemande" tabindex="3">
          <option value="0">Aucun</option>
          <?php
		foreach ($n_niveaux as $ligne)
		{
?>
          <option value="<?php echo $ligne['idniveau']; ?>"><?php echo $ligne['nomniveau']; ?></option>
          <?php
		}
?>
      </select></td>
    </tr>
</table>
  <p>Pour cr&eacute;er un membre pas encore
        reconnu comme animateur, donne-lui un statut actuel de visiteur et le
        statut demand&eacute; correspond &agrave; celui qu'il souhaite obtenir. </p>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td>Pseudo* : </td>
      <td><input name="monpseudo" type="text" class="case" tabindex="4" size="40" maxlength="32" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Mot de passe* :</td>
      <td><input name="pw" type="password" class="case" tabindex="5" size="40" maxlength="100" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Pr&eacute;nom* :</td>
      <td><input name="prenom" type="text" class="case" tabindex="6" size="40" maxlength="100" />
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Nom* :</td>
      <td><input name="nom" type="text" class="case" tabindex="7" size="40" maxlength="100" />
      </td>
    </tr>
  </table>
</fieldset>
<fieldset>
<legend>Profil personnalis&eacute;</legend>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td>Totem</td>
      <td><input name="totem_scout" type="text" id="totem" tabindex="8" size="40" maxlength="100" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Quali</td>
      <td><input name="quali_scout" type="text" id="quali" tabindex="9" size="40" maxlength="100" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Totem de jungle</td>
      <td><input name="totem_jungle" type="text" id="totem_jungle" tabindex="10" size="40" maxlength="100" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Site web</td>
      <td><input name="siteweb" type="text" id="siteweb" tabindex="11" size="40" maxlength="255" /></td>
    </tr>
    <tr valign="top" class="td-1">
      <td>Loisirs</td>
      <td><input name="loisirs" type="text" class="case" id="loisirs" tabindex="12" size="40" maxlength="255" />
      </td>
    </tr>
    <tr align="center" valign="top">
      <td colspan="2">Message personnel : <br />
          <textarea name="profilmembre" rows="6" style="width:400px;" tabindex="13"></textarea>
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Avatar</td>
      <td><input name="avatar" type="text" class="case" id="avatar" tabindex="14" size="40" maxlength="100" />
      </td>
    </tr>
  </table>
  <p>Si tu le souhaites, tu pourras uploader un avatar pour ce membre apr&egrave;s avoir cr&eacute;&eacute; sa fiche.</p>
</fieldset>
  <p align="center">* Champs obligatoires<br />
    <br />
    <input type="submit" name="Submit" value="Cr&eacute;er ce membre" tabindex="15" />
  </p>
  <p class="instructions">Pour un membre normal, aucune donn&eacute;e ne doit &ecirc;tre modifi&eacute;e
    ci-dessous.  </p>
<fieldset>
<legend>Donn&eacute;es techniques diverses</legend>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td height="16" colspan="2">co-webmaster :
          <label for="assistantwebmasteroui">
          <input type="radio" name="assistantwebmaster" value="1" id="assistantwebmasteroui" tabindex="16" />
      Oui </label>
          <label for="assistantwebmasternon">
          <input type="radio" name="assistantwebmaster" value="0" id="assistantwebmasternon" checked="checked" tabindex="17" />
      Non</label></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td colspan="2">Banni :
          <label for="bannioui">
          <input type="radio" name="banni" value="1" id="bannioui" tabindex="18" />
      Oui </label>
          <label for="banninon">
          <input type="radio" name="banni" value="0" id="banninon" checked="checked" tabindex="19" />
      Non</label></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Date inscription</td>
      <td><input type="text" name="dateinscr" maxlength="32" class="case" value="<?php echo date('Y-m-d H:i:s'); ?>" tabindex="20" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>IP inscription</td>
      <td><input type="text" name="ipinscription" maxlength="32" class="case" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" tabindex="21" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Garant</td>
      <td><input type="text" name="autorise" maxlength="32" class="case" tabindex="22" /></td>
    </tr>
  </table>
<p>Le garant est simplement l'utilisateur
  qui a reconnu le statut de l'utilisateur concern&eacute;</p>
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
    <tr valign="top" class="td-gris">
      <td colspan="2">Afficher aide :
          <label for="radio3">
          <input type="radio" name="affaide" value="1" id="radio3" checked="checked" tabindex="23" />
      Oui </label>
          <label for="radio4">
          <input type="radio" name="affaide" value="" id="radio4" tabindex="24" />
      Non</label>
      </td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Nombre de connexions</td>
      <td><input type="text" name="nbconnex" maxlength="32" class="case" value="0" tabindex="25" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Derni&egrave;re connexion</td>
      <td><input name="lastconnex" type="text" class="case" value="0000-00-00 00:00:00" maxlength="32" tabindex="26" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Pages vues</td>
      <td><input type="text" name="pagesvues" maxlength="32" class="case" value="0" tabindex="27" /></td>
    </tr>
    <tr valign="top" class="td-gris">
      <td>Cl&eacute; d'activation</td>
      <td><input type="text" name="clevalidation" tabindex="28" />
          <input type="button" name="autphotos" value="&lt;&lt;&lt;" onclick="form.clevalidation.value = '<?php echo cleunique('mini'); ?>'" title="Cr&eacute;er une cl&eacute; de validation" />
      </td>
    </tr>
  </table>
  <p>La cl&eacute; d'activation n'a une
      valeur que pendant l'inscription. Si tu veux cr&eacute;er une demande d'inscription
  artificielle, n'encode que l'adresse email et la cl&eacute; d'activation.</p>
</fieldset>
  <p align="center">* Champs obligatoires<br />
    <br />
    <input type="submit" name="Submit" value="Cr&eacute;er ce membre" tabindex="29" />
  </p>
</form>
<?php
	}
	else if ($_POST['step'] == 2)
	{
		if (!empty($_POST['monpseudo']) and !empty($_POST['nom']) and !empty($_POST['prenom']) and is_numeric($_POST['niveau']))
		{
			$pw = md5(UN_PEU_DE_SEL.$_POST['pw']);
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
			$sql = "INSERT INTO ".PREFIXE_TABLES."auteurs (pseudo, prenom, nom, email, niveau, nivdemande, assistantwebmaster, numsection, dateinscr, banni, autorise, ipinscription, nbconnex, affaide, pagesvues,	lastconnex, siteweb, profilmembre, avatar, loisirs, totem_scout, quali_scout, totem_jungle, clevalidation, pw) values ('$monpseudo', '$prenom', '$nom', '$email', '$_POST[niveau]', '$_POST[nivdemande]', '$_POST[assistantwebmaster]', '$section', '$_POST[dateinscr]', '$_POST[banni]', '$_POST[autorise]', '$_POST[ipinscription]', '$_POST[nbconnex]', '$_POST[affaide]', '$_POST[pagesvues]', '$_POST[lastconnex]', '$_POST[siteweb]', '$profilmembre', '$_POST[avatar]', '$loisirs', '$totem_scout', '$quali_scout', '$totem_jungle', '$_POST[clevalidation]', '$pw')";
			send_sql($db, $sql);
			header('Location: index.php?page=createmembresite&step=3');
		}
		else
		{
			header('Location: index.php?page=createmembresite');
		}
	}
	else if ($_GET['step'] == 3)
	{
?>
<h1>Cr&eacute;ation d'un membre du portail</h1>
<p align="center"> <a href="?page=membres">Retour &agrave; la page d'accueil 
  membres</a> - <a href="index.php?page=gestion_mb_site">Retour &agrave; la Gestion des 
  membres du portail</a></p>
<div class="msg">
<p align="center">Le membre a bien &eacute;t&eacute; cr&eacute;&eacute;</p>
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