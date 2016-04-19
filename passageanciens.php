<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* passageanciens.php v 1.1 - Récupération d'un ancien de l'unité dans l'effectif de l'unité
* Il s'agit, dans la db, d'un simple changement de section
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
*	Correction du lien vers la fiche ancien
*	optimisation xhtml
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
			if (confirm("Es-tu certain des changements que tu vas apporter ?"))
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
<h1>Gestion des passages d'une section Anciens &agrave; une autre</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour à la 
page Gestion de l'Unit&eacute;</a></p>
<?php
		if ($_GET['msg'] == 'ok')
		{
?>
<div class="msg">
  <p align="center">Le passage a eu lieu correctement. V&eacute;rifie 
    ci-dessous le nouvel effectif des Anciens (<?php echo $_GET['nbre']; ?> membres sont pass&eacute;s). </p>
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
		$a = '';
		$n = 0;
		foreach ($sections as $section)
		{ // composition de la requête sql
			if ($section['anciens'] == 1)
			{
				$n++;
				if ($n == 1) $a .= ' AND (';
				if ($n > 1) $a .= ' OR ';
				$a .= "section = '$section[numsection]'";
			}
		}
		if ($n > 0) $a .= ')';
		$tri =  ($_GET['tri'] == 'nom') ? '' : 'ddn, ';
		$sql = "SELECT nummb, nom_mb, prenom, ddn, a.sexe, nomsection FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."unite_sections as b WHERE a.section = b.numsection $a ORDER BY $tri nom_mb, prenom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbre_membres = mysql_num_rows($res);
		}
?>
<form action="passageanciens.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
<h2>Param&egrave;tres du passage</h2><input type="hidden" name="step" value="2" />
<p class="petitbleu">Ci-dessous, s&eacute;lectionne dans la liste des Anciens tous ceux qui passent 
    dans une autre section.</p>
<fieldset>
<legend>Choix de la section</legend>

<p><span class="rmqbleu">Apr&egrave;s le passage :</span>
<?php
		if (count($sections) > 1)
		{
?>
<select name="newsection">
  <option value="0" selected="selected">Aucune Section sélectionn&eacute;e</option>
<?php
			foreach ($sections as $section)
			{
					if ($section['sexe'] == 'm') {$sexe = 'pour gar&ccedil;ons';} else if ($section['sexe'] == 'f') {$sexe = 'pour filles';} else if ($section['sexe'] == 'x') {$sexe = 'mixte';} else {$sexe = '';}
?>
  <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; echo (!empty($sexe)) ? ' - '.$sexe.' ' : ''; echo (!empty($section['trancheage'])) ? $section['trancheage'] : ''; ?></option>
<?php
			}
?>
</select>
<?php
		}
?></p>
</fieldset>
<p align="center">Trier les Anciens 
  <?php if ($_GET['tri'] == 'nom') { ?><a href="index.php?page=passageanciens&tri=age">par 
    &acirc;ge</a><?php } else { ?><a href="index.php?page=passageanciens&tri=nom">par 
    nom</a><?php } ?></p>
<?php 
		if ($nbre_membres > 0) 
		{ 
?>
<fieldset>
<legend>S&eacute;lection des anciens</legend>
<?php
			if ($nbre_membres > 1) 
			{
				$pl = 's';
			} 
			else 
			{
				$pl = '';
			} 
			echo $nbre_membres.' membre'.$pl.' trouv&eacute;'.$pl.' (liste tri&eacute;e par '; 
			echo ($_GET['tri'] == 'nom') ? 'nom)' : '&acirc;ge)';
?>
<input type="hidden" name="nbre_membres" value="<?php echo $nbre_membres; ?>" />
<table cellspacing="0" cellpadding="2">
<?php
			$j = 1;
			while ($membre = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
<tr class="<?php echo $couleur; ?>">
  <td><a href="index.php?page=ficheancien&amp;nummb=<?php echo $membre['nummb']; ?>" title="Voir sa fiche personnelle"><img src="templates/default/images/membre.png" border="0" alt="Voir sa fiche" /></a> 
  <input type="checkbox" name="mb-<?php echo $j; ?>" id="mb-<?php echo $j; ?>" value="<?php echo $membre['nummb']; ?>" />
<?php 
				echo '<label for="mb-'.$j.'">'.$membre['nom_mb'].' '.$membre['prenom'].'</label>';
				$j++;
?>
  </td>
  <td><?php echo $membre['nomsection']; ?></td>
  <td align="right">
<?php
				if ($membre['ddn'] != '0000-00-00')
				{ 
					if ($membre['sexe'] == 'f') {$fem = 'e';} else {$fem = '';}
					$ageaff = age($membre['ddn'], 1); 
					echo ' n&eacute;'.$fem.' le <span title="'.$ageaff.'">'.date_ymd_dmy($membre['ddn'], 'enchiffres').'</span>';
				}
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
<p class="rmq">Il n'y a aucun membre dans les sections Anciens.</p>
</div>
<input type="hidden" name="nbre_membres" value="<?php echo $nbre_membres; ?>">
<?php
		}
?>
<p align="center">
 <input type="submit" name="Submit" value="Effectuer le passage" />
</p>
</form>
<?php
	}
	else
	{
		$requete = '';
		$nbre_nouveaux = 0;
		for ($i = 1; $i <= $_POST['nbre_membres']; $i++)
		{ // on compose la requête sql
			$membre = 'mb-'.$i;
			if (!empty($_POST[$membre])) 
			{
				$nbre_nouveaux++;
				if (!empty($requete)) {$requete .= ' OR ';}
				$valeur = $_POST[$membre];
				$requete .= "nummb = '$valeur'";
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
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET section = '$_POST[newsection]', fonction = '1', siz_pat = '0', cp_sizenier = '0', mb_lastmodifby = '$user[num]', mb_lastmodif = now() WHERE $requete";
			send_sql($db, $sql);
			log_this('Passage effectué chez les '.$sections[$_POST['newsection']]['appellation'].' ('.$nbre_nouveaux.' personnes : '.$liste_noms.')', 'passageanciens');
			header('Location: index.php?page=passageanciens&msg=ok&nbre='.$nbre_nouveaux);
		}
		else
		{ // Aucun ancien n'était sélectionné
			header('Location: index.php?page=passageanciens&msg=no');
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