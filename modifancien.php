<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* modifancien.php v 1.1 - Modification de la fiche d'un ancien de l'Unité
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
		$nummb = (is_numeric($_GET['nummb'])) ? $_GET['nummb'] : $_POST['nummb'];
		if (!empty($nummb))
		{
			$autrecritere = '';
			$j = 0;
			foreach ($sections as $section)
			{
				if ($section['anciens'] == 1)
				{
					$j++;
					$autrecritere .= ($j == 1) ? ' AND (' : ' OR ';
					$autrecritere .= "section = '$section[numsection]' ";
				}
			}
			$autrecritere .= ($j > 0) ? ')' : '';
			$sql = "SELECT * FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."mb_adresses as b WHERE a.nummb = '$nummb' AND a.famille = b.numfamille $autrecritere";
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
<title>Modifier la fiche d'un Ancien</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
				}
?>
<h1>Modifier la fiche d'un Ancien</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
var changed = false;
function check_form(form)
{
	if (form.prenom.value != "" && form.nom_mb.value != "" && form.famille.value != 0)
	{
		if (changed)
		{
			if (confirm("Es-tu certain de vouloir Modifier la Section dans laquelle <?php echo $membre['prenom'];?> se trouve ?"))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return true;
		}
	}
	else
	{
		alert("Merci de remplir les cases marquées d'une astérisque (Prénom, nom et Adresse).");
		return false;
	}
}

function addphoto()
{
	window.open('addimage.php?x=2', 'choixphoto', 'width=350,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}
</script>
<form action="modifancien.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
  <h2>Donn&eacute;es de l'ancien</h2>
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="nummb" value="<?php echo $nummb; ?>" />
  <fieldset>
  <legend>Donn&eacute;es personnelles de l'ancien</legend>
  <table width="100%" border="0" cellpadding="0" cellspacing="1">
    <tr class="td-gris">
      <td>Pr&eacute;nom * </td>
      <td><input name="prenom" type="text" id="prenom3" size="30" maxlength="100" value="<?php echo $membre['prenom']; ?>" tabindex="1" /></td>
    </tr>
    <tr class="td-gris">
      <td>Nom *</td>
      <td><input name="nom_mb" type="text" id="nom_mb6" size="30" maxlength="100" value="<?php echo $membre['nom_mb']; ?>" onchange="this.value=this.value.toUpperCase()" tabindex="2" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Sexe</td>
      <td><select name="sexe" id="select7" tabindex="3">
          <option value="m"<?php if ($membre['sexe'] == 'm') {echo ' selected'; } ?>>masculin</option>
          <option value="f"<?php if ($membre['sexe'] == 'f') {echo ' selected'; } ?>>f&eacute;minin</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td>Date de naissance</td>
      <td><input name="ddn" type="text" id="ddn2" onfocus="if (this.value == 'jj/mm/aaaa') this.value = '';" onblur="if (this.value == '') this.value = 'jj/mm/aaaa';" value="<?php if ($membre['ddn'] != "0000-00-00") {echo date_ymd_dmy($membre['ddn'], "enchiffres"); } else {echo "jj/mm/aaaa";} ?>" maxlength="10" tabindex="4" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Totem</td>
      <td><input name="totem" type="text" id="totem" maxlength="100" value="<?php echo $membre['totem']; ?>" tabindex="5" />
      </td>
    </tr>
    <tr class="td-gris">
      <td>Quali</td>
      <td><input name="quali" type="text" id="quali" maxlength="100" value="<?php echo $membre['quali']; ?>" tabindex="6" />
      </td>
    </tr>
    <tr class="td-gris">
      <td>Totem de jungle</td>
      <td><input name="quali" type="text" id="quali" maxlength="100" value="<?php echo $membre['totem_jungle']; ?>" tabindex="7" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Section</td>
      <td><input type="hidden" name="oldsection" value="<?php echo $membre['section']; ?>" />
        <select name="section" onchange="if (this.value == <?php echo $membre['section']; ?>) {changed = false;} else {changed = true;}" tabindex="8">
          <option value=""></option>
          <?php
				foreach ($sections as $section)
				{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $membre['section']) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
          <?php
				}
?>
        </select>
      </td>
    </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Infos utiles</legend>
  <table width="100%" border="0" cellpadding="0" cellspacing="1">
    <tr class="td-gris">
      <td>Email personnel</td>
      <td><input name="email_mb" type="text" id="email_mb2" size="30" maxlength="255" value="<?php echo $membre['email_mb']; ?>" tabindex="9" /></td>
    </tr>
    <tr class="td-gris">
      <td>Site web</td>
      <td><input name="siteweb" type="text" id="siteweb2" value="<?php if ($membre['siteweb'] != '') {echo $membre['siteweb']; } else {echo 'http://';}?>" size="30" maxlength="255" tabindex="10" /></td>
    </tr>
    <tr class="td-gris">
      <td>Tel. personnel</td>
      <td><input name="telperso" type="text" id="telperso2" size="20" maxlength="30" value="<?php echo $membre['telperso']; ?>" tabindex="11" /></td>
    </tr>
    <tr>
      <td colspan="2"><span class="petitbleu">Inutile d'indiquer ici un num&eacute;ro d&eacute;j&agrave; pr&eacute;sent dans la fiche famille. <br />
        Ce num&eacute;ro sera plut&ocirc;t un num&eacute;ro de GSM (vraiment personnel).</span></td>
    </tr>
  </table>
  <p>Remarques &eacute;ventuelles</p>
  <textarea name="rmq_mb" cols="35" rows="4" id="rmq_mb" style="width:400px;" tabindex="12"><?php echo stripslashes($membre['rmq_mb']); ?></textarea>
  </fieldset>
  <fieldset>
  <legend>Photo du membre</legend>
  <p align="center">
    <input name="photo" type="text" id="photo" size="50" maxlength="255" value="<?php echo $membre['photo']; ?>" tabindex="13" />
    <input name="choisir" type="button" id="choisir" value="Choisir la photo" onclick="addphoto();" tabindex="14" />
  </p>
  <p>Sa photo n'est pas sur le portail : <a href="index.php?page=upload_photomembre&amp;r=a&amp;nummb=<?php echo $nummb; ?>" tabindex="15">D&eacute;poser la photo depuis ton ordinateur</a></p>
  </fieldset>
  <fieldset>
  <legend>Coordonn&eacute;es de l'ancien</legend>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr>
      <td class="rmqbleu">Adresse * : </td>
      <td><select name="famille" tabindex="16">
          <option value="0"<?php if ($membre['famille'] == 0) {echo ' selected';} ?>>Aucune</option>
          <?php
				$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
				$res = send_sql($db, $sql);	
				while ($famille = mysql_fetch_assoc($res))
				{
?>
          <option<?php if ($membre['famille'] == $famille['numfamille']) {echo ' selected';} ?> value="<?php echo $famille['numfamille']; ?>"> <?php echo $famille['nom']." (".$famille['adresse'].")"; ?> </option>
          <?php
				}
?>
        </select>
      </td>
    </tr>
  </table>
  </fieldset>
  <p align="center">Les champs marqu&eacute;s d'une * sont obligatoires, les autres sont conseill&eacute;s ... fortement <img src="img/smileys/001.gif" alt="" width="15" height="15" /></p>
  <p align="center"><input type="submit" name="Submit" value="Enregistrer les modifications" tabindex="17" /></p>
</form>
<?php
			}
			else
			{
?>
<h1>Modifier la fiche d'un Ancien</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<p class="rmq" align="center">D&eacute;sol&eacute;, tu ne peux pas modifier cette fiche ou cette fiche n'existe pas.</p>
</div>
<?php
			}
		}
		else
		{
?>
<h1>Modifier la fiche d'un Ancien</h1>
<?php
			$autrecritere = '';
			$j = 0;
			foreach ($sections as $section)
			{
				if ($section['anciens'] == 1)
				{
					$j++;
					$autrecritere .= ($j == 1) ? ' AND (' : ' OR ';
					$autrecritere .= "section = '$section[numsection]' ";
				}
			}
			$autrecritere .= ($j > 0) ? ')' : '';
			$sql = "SELECT nummb, nom_mb, prenom, section, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."mb_adresses as b WHERE a.famille = b.numfamille $autrecritere ORDER BY nom_mb, prenom ASC";
			if ($res = send_sql($db, $sql))
			{
				$nbre_membres = mysql_num_rows($res);
			}
?>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.nummb.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de bien vouloir choisir un membre avant d'envoyer le formulaire.");
		return false;
	}
}
</script>
<form action="index.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
<h2>Choix de l'ancien</h2>
  <input type="hidden" name="page" value="modifancien" />
<p>Choisis parmi les Anciens de l'Unit&eacute; pr&eacute;sents dans la base :
<?php if ($nbre_membres > 0) { if ($nbre_membres > 1) {$pl = 's';} else {$pl = '';} echo "($nbre_membres membre$pl trouv&eacute;$pl)"; } ?></p>
<?php
			if ($nbre_membres > 0)
			{
?>
<p align="center">
<select name="nummb" size="15" tabindex="1">
<?php
				while ($membre = mysql_fetch_assoc($res))
				{
?>
  <option value="<?php echo $membre['nummb']; ?>"><?php echo $membre['nom_mb'].' '.$membre['prenom'].' ('.$membre['adresse'].')'; 
					if ($connected > 3 and !empty($sections[$membre['section']]['nomsectionpt'])) {echo ' - '.$sections[$membre['section']]['nomsectionpt'];} ?></option>
<?php
				}
?>
</select>
</p>
<p class="petitbleu">Astuce : Pour retrouver plus rapidement un membre, clique dans la liste et tape la premi&egrave;re lettre de son nom pour t'en approcher.</p>
<p align="center">
  <input type="submit" value="Modifier sa fiche" tabindex="2" />
</p>
<?php
			}
			else
			{
?>
<div class="msg">
<p class="rmq">Il n'y a aucun membre dans la base pour le moment.</p>
</div>
<?php
			}
?>
</form>
<?php
		}
	}
	else if ($_POST['step'] == '2')
	{
		if (!empty($_POST['nom_mb']) and !empty($_POST['prenom']) and $_POST['section'] > 0 and is_numeric($_POST['famille']) and is_numeric($_POST['nummb']))
		{
			$nom_mb = htmlentities(strtoupper($_POST['nom_mb']), ENT_QUOTES); 
			$nom_mb_son = soundex2($_POST['nom_mb']);
			$prenom = htmlentities($_POST['prenom'], ENT_QUOTES); 
			$prenom_son = soundex2($_POST['prenom']);
			$totem = htmlentities($_POST['totem'], ENT_QUOTES); 
			$quali = htmlentities($_POST['quali'], ENT_QUOTES); 
			$siteweb = ($_POST['siteweb'] == 'http://') ? '' : htmlentities($_POST['siteweb'], ENT_QUOTES);
			if ($_POST['ddn'] != 'jj/mm/aaaa')
			{
				if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $_POST['ddn'], $regs))
				{
					$ddn = "$regs[3]-$regs[2]-$regs[1]";
				}
				else
				{
					$ddn = '0000-00-00';
				}
			}
			else
			{
				$ddn = '0000-00-00';
			}
			$rmq_mb = htmlentities($_POST['rmq_mb'], ENT_QUOTES); 
			$email_mb = (checkmail($_POST['email_mb'])) ? $_POST['email_mb'] : '';
			$plus = '';
			if ($_POST['oldsection'] != $_POST['section'])
			{
				$plus = "fonction = '0', siz_pat = '0', cp_sizenier = '0', ";
			}
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres 
			SET $plus
			nom_mb = '$nom_mb', nom_mb_son = '$nom_mb_son', prenom = '$prenom', prenom_son = '$prenom_son', famille = '$_POST[famille]', ddn = '$ddn', section = '$_POST[section]', totem = '$totem', 
			quali = '$quali', photo = '$_POST[photo]', rmq_mb = '$rmq_mb', email_mb = '$email_mb', siteweb = '$siteweb', sexe = '$_POST[sexe]', 
			telperso = '$_POST[telperso]', mb_lastmodifby = '$user[num]', mb_lastmodif = now() WHERE nummb = '$_POST[nummb]'";
			send_sql($db, $sql);
			if (!empty($email_mb))
			{ // abonnement à la newsletter
				abonnement_newsletter($email_mb, $prenom.' '.$nom_mb);
			}
			log_this('Modification fiche ancien : '.$nom_mb.' '.$prenom.' ('.$_POST['nummb'].')', 'modifanciens');
			header('Location: index.php?page=modifancien&step=3');
		}
		else
		{
			header('Location: index.php?page=modifancien&nummb='.$_POST['nummb']);
		}
	}
	else if ($_GET['step'] == 3)
	{
		$sql = "SELECT nummb, prenom, nom_mb FROM ".PREFIXE_TABLES."mb_membres WHERE mb_lastmodifby = '$user[num]' ORDER BY mb_lastmodif DESC LIMIT 1";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<h1>Modifier la fiche d'un Ancien</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<p align="center">Les donn&eacute;es ont bien &eacute;t&eacute; mises &agrave; jour.</p>
<p align="center"><a href="index.php?page=ficheancien&amp;nummb=<?php echo $membre['nummb']; ?>">Voir sa fiche membre</a></p>
</div>
<?php
		}
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
