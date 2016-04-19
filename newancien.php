<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* newancien.php v 1.1 - Ajout d'un ancien à la Gestion de l'Unité
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
*	ajout présélection de la section anciens selon l'utilisateur
*	optimisation xhtml
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
} // fin numniveau <= 2
else
{
	if ((empty($_POST['step']) and empty($_GET['step'])) or $_GET['step'] == 1)
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Ajout d'un membre dans la base de données</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		} // fin !defined in site
?>
<h1>Ajout d'un ancien dans la base de donn&eacute;es</h1>
<p align="center"> <a href="?page=gestion_unite">Retour &agrave; 
  la page Gestion de l'Unit&eacute;</a></p>
<p>Cette page te permet d'ajouter un ancien de l'Unit&eacute; dans la base de 
  donn&eacute;es.</p>
<p><span class="rmq">Avant toute chose</span>, merci de <span class="rmq">lire 
  les instructions</span> au bas de la page</p>
<?php
		$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
		if ($res2 = $res = send_sql($db, $sql))
		{
			$nbre_familles = mysql_num_rows($res);
		} // fin send_sql
?>
<script type="text/javascript" language="JavaScript">
function check_form(form)
{
	if (form.section.value == 0)
	{
		alert("Merci de choisir une Section Anciens");
		return false;
	}
	if (form.famille.value != 0 && form.nom_mb.value != '' && form.prenom.value != '')
	{
		getElement("envoi").disabled = true;
		getElement("envoi").value = "Patience...";
		return true;
	}
	else
	{
		alert("Merci d'indiquer et de choisir au moins la famille et le prénom de l'ancien à ajouter.");
		return false;
	}
}
</script>
<form action="newancien.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
<h2>Coordonn&eacute;es de l'ancien *</h2>
  <input type="hidden" name="step" value="2" />
<fieldset>
<legend>Famille de l'ancien</legend>
<p>- La famille <span class="rmq">existe d&eacute;j&agrave;</span> dans la base : 
<?php 
		if ($nbre_familles > 0) 
		{ 
			$pl = ($nbre_familles > 1) ? 's' : ''; 
			echo '('.$nbre_familles.' famille'.$pl.' trouv&eacute;e'.$pl.')'; 
		} 

		if ($nbre_familles > 0)
		{
?>
<p align="center"> 
  <select name="famille" onchange="if (this.value > 0) {alert('Vérifions quels membres sont dans cette famille.'); window.location='index.php?page=newancien&numfamille='+this.value;}" tabindex="1">
	<option value="0">&gt;&gt; Choisir un nom dans la liste</option>
<?php
			while ($famille = mysql_fetch_assoc($res))
			{
?>
	<option <?php if ($_GET['numfamille'] == $famille['numfamille']) {echo 'selected'; $lenomchoisi = $famille['nom'];} ?> value="<?php echo $famille['numfamille']; ?>"><?php echo $famille['nom'].' ('.$famille['adresse'].')'; ?></option>
<?php
			} // fin while
?>
  </select>
</p>
<?php
			if (is_numeric($_GET['numfamille']))
			{
				$sql = "SELECT nummb, nom_mb, prenom, ddn, sexe, section FROM ".PREFIXE_TABLES."mb_membres WHERE famille = '$_GET[numfamille]' or famille2 = '$_GET[numfamille]' ORDER BY nom_mb, prenom ASC";
				if ($res = send_sql($db, $sql))
				{
					$nbre_p = mysql_num_rows($res);
					if ($nbre_p > 0)
					{
						$pluriel_mb = ($nbre_p > 1) ? 's' : '';
?>
<p class="rmqbleu">Il y a <?php echo $nbre_p.' membre'.$pluriel_mb.' inscrit'.$pluriel_mb;?> dans cette famille :</p>
<ul>
<?php
						while ($unmembre = mysql_fetch_assoc($res))
						{
							$ddn = '';
							$datenaissance = '';
							$fem = '';
							$ddn = date_ymd_dmy($unmembre['ddn'], 'enchiffres');
							$fem = ($unmembre['sexe'] == 'f') ? 'e' : '';
							$datenaissance = ($ddn != '00/00/0000') ? ' n&eacute;'.$fem.' le '.$ddn : '';
							if ($sections[$unmembre['section']]['anciens'] != 1)
							{
?>
  <li><a href="index.php?page=fichemb&amp;nummb=<?php echo $unmembre['nummb']; ?>" title="Voir sa fiche de membre"><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?></a> <?php echo $datenaissance; ?></li>
<?php
							}
							else
							{
?>
  <li><a href="index.php?page=ficheancien&amp;nummb=<?php echo $unmembre['nummb']; ?>" title="Voir sa fiche de membre"><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?></a> <?php echo $datenaissance; ?> - <span class="rmq">Ancien</span></li>
<?php
							}
						} // fin while
?>
</ul>
<?php
					} // fin if nbre_p > 0 (des membres sont dans la famille sélectionnée)
					else
					{
?>
<div align="center" class="msg">
<p align="center" class="rmq">Aucun membre dans cette famille.</p>
</div>
<?php
					}
				} // fin send sql
			} // fin numfamille != ""
		} // fin nbre_familles > 0
		else
		{
?>
<div class="msg">
<input type="hidden" name="famille" value="0" />
<p>Il n'y a aucune famille dans la base pour le moment.</p>
</div>
<?php
		} // fin else nbre_familles > 0
?>
<p>- La famille n'est <span class="rmq">pas encore</span> dans la base :</p>
<p align="center"><input type="button" name="Button" value="Cr&eacute;er une nouvelle famille" onclick="window.location='index.php?page=newad&suite=newancien';" /></p>
</fieldset>
<h2>Donn&eacute;es de l'ancien</h2>
<fieldset>
<legend>Donn&eacute;es personnelles de l'ancien</legend>
<p class="petitbleu">Encode ici les donn&eacute;es personnelles 
du nouvel Ancien</p>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr class="td-gris"> 
	<td>Pr&eacute;nom *</td>
	<td><input name="prenom" type="text" tabindex="2" size="30" maxlength="100" /></td>
  </tr>
  <tr class="td-gris"> 
	<td>Nom *</td>
	<td><input name="nom_mb" type="text" id="nom_mb" tabindex="3" onchange="this.value=this.value.toUpperCase()" size="30" maxlength="100"<?php echo (!empty($lenomchoisi)) ? ' value="'.$lenomchoisi.'"' : ''; ?> /></td>
  </tr>
  <tr> 
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr class="td-gris"> 
	<td>Section Anciens *</td>
	<td> 
	  <select name="section" tabindex="4">
<?php
	$sections_anciens = liste_sections_anciens();
	// déterminons la section anciens correspondant à l'unité de l'utilisateur
	// pour présélectionner la section où il pourrait placer le membre.
	if (is_unite($user['numsection']))
	{
		$user_section_anciens = $sections_anciens[$user['numsection']];
	}
	else
	{
		$user_section_anciens = $sections_anciens[$sections[$user['numsection']]['unite']];
	}
	foreach ($sections_anciens as $numsection_anciens)
	{ // on affiche les sections anciens
?>
		<option value="<?php echo $numsection_anciens; ?>"<?php echo ($numsection_anciens == $user_section_anciens) ? ' selected="selected"' : ''; ?>><?php echo $sections[$numsection_anciens]['nomsection']; ?></option>
<?php
	} // fin foreach $sections
?>
	  </select> 
	</td>
  </tr>
  <tr> 
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr class="td-gris"> 
	<td>Sexe</td>
	<td><select name="sexe" tabindex="5">
		<option value="m" selected="selected">masculin</option>
		<option value="f">f&eacute;minin</option>
	  </select></td>
  </tr>
  <tr class="td-gris"> 
	<td>Date de naissance</td>
	<td><input name="ddn" type="text" onfocus="if (this.value == 'jj/mm/aaaa') this.value = '';" onblur="if (this.value == '') this.value = 'jj/mm/aaaa';" value="jj/mm/aaaa" maxlength="10" style="width:80px;" tabindex="6" /></td>
  </tr>
  <tr> 
	<td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr class="td-gris"> 
	<td>Totem</td>
	<td><input name="totem" type="text" tabindex="7" size="30" maxlength="100" /></td>
  </tr>
  <tr class="td-gris"> 
	<td>Quali </td>
	<td><input name="quali" type="text" tabindex="8" size="30" maxlength="100" /></td>
  </tr>
  <tr class="td-gris"> 
	<td>Totem de jungle</td>
	<td><input name="totem_jungle" type="text" tabindex="9" size="30" /></td>
  </tr>
</table>
</fieldset>
<fieldset>
<legend>Infos utiles</legend>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
  <tr class="td-gris"> 
	<td>Email personnel</td>
	<td><input name="email_mb" type="text" tabindex="10" size="40" maxlength="255" /></td>
  </tr>
  <tr class="td-gris"> 
	<td>Site web</td>
	<td><input name="siteweb" type="text" tabindex="11" value="http://" size="40" maxlength="255" /></td>
  </tr>
  <tr class="td-gris"> 
	<td>Tel. personnel</td>
	<td><input name="telperso" type="text" maxlength="30" title="Ce numéro est le deuxième numéro qui est affiché dans les listings de staffs si le membre est animateur." onfocus="if (this.value == 'xxx/xx xx xx') this.value = '';" onblur="if (this.value == '') this.value = 'xxx/xx xx xx';" value="xxx/xx xx xx" style="width:120px;" tabindex="12" /></td>
  </tr>
  <tr> 
	<td>&nbsp;</td>
	<td class="petitbleu">Inutile d'indiquer ici un 
	  num&eacute;ro d&eacute;j&agrave; pr&eacute;sent dans la fiche famille. 
	  <br />
	  Ce num&eacute;ro sera plut&ocirc;t un num&eacute;ro de GSM (vraiment 
	  personnel).</td>
  </tr>
</table>
<p align="center">Remarques &eacute;ventuelles</p>
<textarea name="rmq_mb" cols="35" rows="4" tabindex="13"></textarea>
<p class="petitbleu">Tu ne pourras ajouter une photo 
 &agrave; la fiche de l'ancien qu'en modifiant celle-ci.</p>
</fieldset>
<p align="center">Les champs marqu&eacute;s d'une * sont obligatoires, les autres sont conseill&eacute;s 
... fortement <img src="img/smileys/001.gif" alt="" width="15" height="15" /></p>
<p align="center">
<input type="submit" name="Submit" id="envoi" value="Ajouter ce membre" tabindex="14" />
</p>
</form>
<div class="instructions">
<h2>Conseils pour le format des entr&eacute;es</h2>
  <ul>
    <li class="petit">Nom de famille : <span class="rmq">V&eacute;rifie d'abord</span> 
      si la famille n'est pas d&eacute;j&agrave; dans la base de donn&eacute;es. 
      Si elle existe, s&eacute;lectionne-la. Inutile de cr&eacute;er deux fois 
      la m&ecirc;me adresse courrier</li>
    <li class="petit">Nom, Pr&eacute;nom, Totem : Ce sont des noms propres. Qui 
      dit nom propre dit <span class="rmq">majuscule.</span></li>
    <li class="petit">les num&eacute;ros de t&eacute;l&eacute;phone : format xxx/xx 
      xx xx (pas de points ou de / <span class="rmq">inutiles</span>. Ins&eacute;rer 
      des espaces pour la lisibilit&eacute; du num&eacute;ro)</li>
    <li class="petit">Inutile d'<span class="rmq">indiquer deux fois</span> le 
      m&ecirc;me num&eacute;ro de t&eacute;l&eacute;phone. La famille et le membre 
      sont li&eacute;s.</li>
    <li class="petit">Toutes les autres cases : <span class="rmq">laisser vide</span> 
      s'il n'y a pas lieu de les remplir (pas de petite barre ou autre)</li>
  </ul>
</div>
<?php
		if (!defined('IN_SITE'))
		{
?>
</body>
</html>
<?php
		} // fin !defined in_site
	}
	else if ($_POST['step'] == 2)
	{
		if (!empty($_POST['nom_mb']) and !empty($_POST['prenom']) and $_POST['section'] > 0 and $sections[$_POST['section']]['anciens'] == 1 and $_POST['famille'] > 0)
		{
			$nom_mb = htmlentities(strtoupper($_POST['nom_mb']), ENT_QUOTES);
			$nom_mb_son = soundex2($_POST['nom_mb']);
			$prenom = htmlentities($_POST['prenom'], ENT_QUOTES);
			$prenom_son = soundex2($_POST['prenom']);
			$totem = htmlentities($_POST['totem'], ENT_QUOTES);
			$quali = htmlentities($_POST['quali'], ENT_QUOTES);
			$totem_jungle = htmlentities($_POST['totem_jungle'], ENT_QUOTES);
			$siteweb = ($_POST['siteweb'] == 'http://') ? '' : htmlentities($_POST['siteweb'], ENT_QUOTES);
			$email_mb = checkmail($_POST['email_mb']);
			$ddn = $_POST['ddn'];
			if ($ddn != 'jj/mm/aaaa')
			{
				if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $ddn, $regs))
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
			$telperso = ($_POST['telperso'] == 'xxx/xx xx xx') ? '' : htmlentities($_POST['telperso'], ENT_QUOTES);
			$sql = "INSERT INTO ".PREFIXE_TABLES."mb_membres (nom_mb, nom_mb_son, prenom, prenom_son, famille, ddn, dateinscr, section, totem, quali, totem_jungle, rmq_mb, email_mb, siteweb, sexe, telperso, mb_createur, mb_datecreation, mb_lastmodifby, mb_lastmodif) 
			values 
			('$nom_mb', '$nom_mb_son', '$prenom', '$prenom_son', '$_POST[famille]', '$ddn', now(), '$_POST[section]', '$totem', '$quali', '$totem_jungle', '$rmq_mb', '$email_mb', '$siteweb', '$_POST[sexe]', '$telperso', '$user[num]', now(), '$user[num]', now())";
			send_sql($db, $sql);
			if (!empty($email_mb))
			{ // abonnement à la newsletter
				abonnement_newsletter($email_mb, $prenom.' '.$nom_mb);
			}
			log_this("Creation fiche ancien : $nom_mb $prenom", 'newancien');
			header('Location: index.php?page=newancien&step=3');
		}
		else
		{
			header('Location: index.php?page=newancien&step=erreur');
		}
	}
	else if ($_GET['step'] == 3)
	{
		$sql = "SELECT nummb FROM ".PREFIXE_TABLES."mb_membres WHERE mb_createur = '$user[num]' ORDER BY mb_datecreation DESC LIMIT 1";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<h1>Ajout d'un Ancien dans la base de donn&eacute;es</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
  <p align="center">Le nouvel ancien a bien &eacute;t&eacute; ajout&eacute;.</p>
  <p align="center"><a href="index.php?page=newancien">Ajouter un autre ancien</a><br />
  <a href="index.php?page=ficheancien&amp;nummb=<?php echo $membre['nummb']; ?>">Voir sa Fiche</a></p>
</div>
<?php
		}
	}
	else if ($_GET['step'] == 'erreur')
	{
?>
<h1>Ajout d'un Ancien dans la base de donn&eacute;es</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
  <p align="center" class="rmq">Une erreur s'est produite !</p>
  <p align="center"><a href="index.php?page=newancien">Ajouter un Anciens</a></p>
</div>
<?php
	}
	else
	{
		include('404.php');	
	}
}
?>