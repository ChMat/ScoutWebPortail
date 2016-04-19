<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* passage.php v 1.1 - Passage de membres d'une section vers une autre section
* seuls les animateurs d'une section peuvent faire passer leurs membres dans une autre section
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
*	Ajout de la liste des membres qui passent dans le log et dans l'email d'avertissement à l'animateur
*	optimisation xhtml
*	externalisation du texte des mails
*	L'animateur d'unité et le webmaster peuvent effectuer des passages depuis toutes les sections.
*	Le webmaster et l'animateur d'unité reviennent à la section après le passage
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
}
else
{
	if (!is_numeric($_POST['step']))
	{
	
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<title>Passage</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body>
<?php
		}
?>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.nbre_membres.value > 0)
	{
		if (form.newsection.value != 0)
		{
			if (confirm("Le fait de changer la Section des membres que tu as sélectionnés entraine leur disparition des listings de ta Section.\nA moins de faire la demande à un animateur de leur nouvelle Section, tu ne peux pas les récupérer.\n\nSouhaites-tu continuer ?"))
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
			alert("Merci de bien vouloir choisir la nouvelle Section avant d'envoyer le formulaire.");
			return false;
		}
	}
	else
	{
		alert("Le passage ne peut avoir lieu. Aucun membre n'est enregistré dans cette Section");
		return false;
	}
}
</script>
<h1>Gestion des passages d'une section &agrave; une autre</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
page Gestion de l'Unit&eacute;</a></p>
<?php
		if ($_GET['msg'] == 'ok')
		{
?>
<div class="msg">
<p align="center" class="rmqbleu">Le passage a eu lieu correctement. V&eacute;rifie 
ci-dessous le nouvel effectif (<?php echo $_GET['nbre'].' '.$sections[$user['numsection']]['appellation']; ?> 
en moins).</p>
</div>
    <?php
		}
		else if ($_GET['msg'] == 'no')
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucun membre n'&eacute;tait s&eacute;lectionn&eacute;, passage annul&eacute;.</p>
</div>
    <?php
		}
		$tri = ($_GET['tri'] == 'nom') ? '' : 'ddn, ';
		if (is_numeric($_GET['section']) and $user['niveau']['numniveau'] > 3)
		{ // L'utilisateur a choisi d'afficher une section particulière
			$section_a_afficher = $_GET['section'];
		}
		else if ($user['numsection'] != 0)
		{ // L'utilisateur ne peut effectuer de passage que depuis sa section
			$section_a_afficher = $user['numsection'];
		}
		else
		{ // L'utilisateur n'a pas choisi de section
			$x = 0;
			$section_a_afficher = 0;
			while ($section_a_afficher == 0)
			{ // On prend la première section qu'on croise pour en sélectionner au moins une qui existe
				if ($sections[$x]['nomsection'] != '') {$section_a_afficher = $x;}
				$x++;
			}
		}
		$sql = "SELECT nummb, nom_mb, prenom, ddn, a.sexe, nomsectionpt FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."unite_sections as b WHERE a.section = b.numsection AND a.section = '$section_a_afficher' ORDER BY $tri nom_mb, prenom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbre_membres = mysql_num_rows($res);
		}
?>
<form action="passage.php" method="post" name="form" class="form_gestion_unite" id="form" onsubmit="return check_form(this)">
<h2>Param&egrave;tres du passage</h2>  <input type="hidden" name="step" value="2" />
<fieldset>
<legend>Choix de la section</legend>
<p><span class="rmqbleu">Avant passage : </span> <?php 
		if ($user['niveau']['numniveau'] < 4)
		{
			echo $sections[$user['numsection']]['nomsection'];
			$choix_section = '';
		}
		else
		{
			if (count($sections) > 1)
			{
				$choix_section = (is_numeric($_GET['section'])) ? '&amp;section='.$_GET['section'] : '';
?>
<select name="section" onchange="window.location='index.php?page=passage&amp;section='+this.value;">
<?php
				foreach ($sections as $section)
				{
					if ($section['sexe'] == 'm') {$sexe = 'pour gar&ccedil;ons';} else if ($section['sexe'] == 'f') {$sexe = 'pour filles';} else if ($section['sexe'] == 'x') {$sexe = 'mixte';} else {$sexe = '';}
					if ($section['anciens'] != 1)
					{

?>
  <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $_GET['section']) ? ' selected="selected"' : ''; ?>><?php echo $section['nomsection']; echo ($sexe != '') ? ' - '.$sexe.' ' : ''; echo ($section['trancheage'] != '') ? $section['trancheage'] : ''; ?></option>
<?php
					}
				}
?>
</select>
<?php
			}
		
		}
 ?></p>
<p><span class="rmqbleu">Apr&egrave;s passage : </span> <?php
		if (count($sections) > 1)
		{
?>
<select name="newsection">
  <option value="0" selected="selected">Aucune Section s&eacute;lectionn&eacute;e</option>
<?php
			foreach ($sections as $section)
			{
				if ($section['numsection'] != $user['numsection'] or $user['niveau']['numniveau'] > 3)
				{
					if ($section['sexe'] == 'm') {$sexe = 'pour gar&ccedil;ons';} else if ($section['sexe'] == 'f') {$sexe = 'pour filles';} else if ($section['sexe'] == 'x') {$sexe = 'mixte';} else {$sexe = '';}
?>
  <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; echo ($sexe != '') ? ' - '.$sexe.' ' : ''; echo ($section['trancheage'] != '') ? $section['trancheage'] : ''; ?></option>
<?php
				}
			}
?>
</select>
<?php
		}
?></p>
</fieldset>
<p align="center">Trier les Membres
  <?php if ($_GET['tri'] == 'nom') { ?><a href="index.php?page=passage&amp;tri=age<?php echo $choix_section; ?>">par &acirc;ge</a><?php } else { ?>
  <a href="index.php?page=passage&amp;tri=nom<?php echo $choix_section; ?>">par nom</a><?php } ?></p>
<?php 
		if ($nbre_membres > 0) 
		{ 
			if ($nbre_membres > 1) 
			{
				$pl = 's';
			} 
			else 
			{
				$pl = '';
			} 
?>
<fieldset>
<legend><span class="rmqbleu"><?php echo (is_numeric($section_a_afficher) and $user['niveau']['numniveau'] >= 4) ? $sections[$section_a_afficher]['nomsection'] : $sections[$user['numsection']]['nomsection']; ?></span> - <?php echo $nbre_membres.' membre'.$pl; ?></legend>
<p>S&eacute;lection des membres - 
<?php
			echo 'liste tri&eacute;e par ';
			echo ($_GET['tri'] == 'nom') ? 'nom' : '&acirc;ge';
?>
</p>
<input type="hidden" name="nbre_membres" value="<?php echo $nbre_membres; ?>" />
<table cellspacing="0" cellpadding="2" width="100%">
<?php
			$j = 1;
			while ($membre = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
<tr class="<?php echo $couleur; ?>">
  <td><a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>"><img src="templates/default/images/membre.png" alt="Voir sa fiche personnelle" width="18" height="12" border="0" /></a> 
	<input type="checkbox" name="mb-<?php echo $j; ?>" id="mb-<?php echo $j; ?>" value="<?php echo $membre['nummb']; ?>" />
<?php 
				echo '<label for="mb-'.$j.'">'.$membre['nom_mb'].' '.$membre['prenom'].'</label>';
?>
  </td>
  <td align="right">
<?php
				if ($membre['ddn'] != '0000-00-00') 
				{ 
					if ($membre['sexe'] == 'f') {$fem = 'e';} else {$fem = '';}
					$ageaff = age($membre['ddn'], 1); 
					echo '<label for="mb-'.$j.'">n&eacute;'.$fem.' le <span title="'.$ageaff.'">'.date_ymd_dmy($membre['ddn'], 'enchiffres').'</span></label>';
				}
				$j++;
?>
  </td>
</tr>
<?php
			}
?>
</table>
</fieldset>
<?php
		}
		else
		{
?>
<div class="msg">
<p class="rmq">Il n'y a aucun membre enregistr&eacute; dans cette Section pour le moment.</p>
</div>
<input type="hidden" name="nbre_membres" value="<?php echo $nbre_membres; ?>" />
<?php
		}
		if (ENVOI_MAILS_ACTIF)
		{
?>
<p align="center">
<input name="prevenir" type="checkbox" id="prevenir" value="oui" checked="checked" />
<label for="prevenir">Signaler le passage au staff de la section &quot;cible&quot;.</label>
</p>
<?php
		}
?>
<p align="center"><input type="submit" name="Submit" value="Effectuer le passage" /></p>
</form>
<div class="instructions">
<p>Ci-dessous, s&eacute;lectionne dans la liste des membres de la Section tous 
ceux qui passent dans une autre section.<br />
Seuls les animateurs de la section ou les animateurs d'unit&eacute; peuvent effectuer des passages.</p>
</div>
<?php
	}
	else
	{
		$requete = '';
		$nbre_nouveaux = 0;
		for ($i = 1; $i <= $_POST['nbre_membres']; $i++)
		{
			$membre = 'mb-'.$i;
			if (!empty($_POST[$membre])) 
			{
				$nbre_nouveaux++;
				if (!empty($requete)) {$requete .= ' OR ';}
				$valeur = $_POST[$membre];
				// composition de la requête sql
				$requete .= 'nummb = \''.$valeur.'\'';
			}
		}
		if (!empty($requete))
		{
			// on récupère le nom et le prénom des membres qui passent pour le log
			$sql = "SELECT nom_mb, prenom FROM ".PREFIXE_TABLES."mb_membres WHERE $requete";
			$res = send_sql($db, $sql);
			$liste_noms = '';
			while ($membre = mysql_fetch_assoc($res))
			{
				$liste_noms .= (!empty($liste_noms)) ? ', ' : '';
				$liste_noms .= $membre['prenom'].' '.$membre['nom_mb'];
			}
			// On effectue le passage proprement dit
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET section = '".$_POST['newsection']."', fonction = '1', siz_pat = '0', cp_sizenier = '0', actif = '0', mb_lastmodifby = '".$user['num']."', mb_lastmodif = now() WHERE ".$requete;
			send_sql($db, $sql);
			$ancienne_section = (is_numeric($_POST['section'])) ? $_POST['section'] : $user['numsection'];
			log_this('Passage effectué des '.$sections[$ancienne_section]['appellation'].' chez les '.$sections[$_POST['newsection']]['appellation'].' ('.$nbre_nouveaux.' personnes : '.$liste_noms.')', 'passage');
			if ($_POST['prevenir'] == 'oui' and $sections[$_POST['newsection']]['anciens'] != 1 and !defined('LOCAL_SITE') and ENVOI_MAILS_ACTIF)
			{ // On avertit les animateurs de la section cible
				include_once('prv/emailer.php');
				$courrier = new emailer();
				$expediteur = (!empty($user['email'])) ? $user['email'] : 'noreply@noreply.be';
				$reponse = $expediteur;
				$courrier->from($expediteur);
				$courrier->reply_to($expediteur);
				$courrier->use_template('passage', 'fr');
				$courrier->assign_vars(array(
					'USER_PSEUDO' => $user['pseudo'],
					'NBRE_NOUVEAUX' => $nbre_nouveaux,
					'APPELLATION_MEMBRES' => $sections[$_POST['newsection']]['appellation'],
					'LISTE_NOMS' => $liste_noms,
					'ADRESSE_SITE' => $site['adressesite'],
					'WEBMASTER_PSEUDO' => $site['webmaster'],
					'WEBMASTER_EMAIL' => $site['mailwebmaster']));
				$sql = "SELECT email_mb FROM ".PREFIXE_TABLES."mb_membres WHERE fonction > 1 AND email_mb != '' AND section = '$_POST[newsection]'";
				$res = send_sql($db, $sql);
				while ($anim = mysql_fetch_assoc($res))
				{
					$courrier->to($anim['email_mb']);
				}
				$courrier->send();
				$courrier->reset();
			}
			header('Location: index.php?page=passage&msg=ok&nbre='.$nbre_nouveaux.'&section='.$ancienne_section);
		}
		else
		{
			header('Location: index.php?page=passage&msg=no');
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