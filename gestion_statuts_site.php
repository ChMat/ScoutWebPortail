<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_statuts_site.php v 1.1 - Gestion des statuts du portail (par le webmaster)
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
*	Choix de la section liée obligatoire pour les niveaux d'accès 3 et 4.
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 5)
{
	include('404.php');
}
else
{
	if (!isset($_GET['do']) and !isset($_POST['do']))
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des Statuts</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des statuts des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_mb_site">Retour &agrave; la page de Gestion des 
  Membres</a></p>
<?php
		$nbreniveaux = count($niveaux);
		if ($nbreniveaux > 0)
		{
?>
<div class="introduction">
<p>Cette page te permet de g&eacute;rer les diff&eacute;rents statuts des membres 
  du portail. De ces statuts d&eacute;pendent les privil&egrave;ges des utilisateurs, 
  sois donc prudent quand tu les modifies. </p>
</div>
<h2>Voici les statuts actuellement disponibles</h2>
<?php
			$i = 0;
			$tri = array('numniveau', 'nomniveau');
			$n_niveaux = super_sort($niveaux);
?>
<table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
<tr>
  <th>Nom Statut</th>
  <th>Section li&eacute;e</th>
    <th title="Niveau de privilèges">Niveau</th>
    <th title="Visible à l'inscription">Visible ?</th>
</tr>
<?php
			foreach ($n_niveaux as $niveau)
			{
				$i++;
				$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
				echo '<tr class="'.$couleur.'">';
				if ($niveau['idniveau'] <= 2)
				{ // Protection du statut visiteur. Ce statut est donné aux membres qui demandent un statut d'animateur sur le site
					echo '<td title="Ce statut ne peut pas être modifié"><em>'.stripslashes($niveau['nomniveau']).'</em></a></td>';
				}
				else
				{
					echo '<td><a href="index.php?page=gestion_statuts_site&amp;do=modifierniveau&amp;idniveau='.$niveau['idniveau'].'&amp;step=2" title="Editer ce Statut">'.stripslashes($niveau['nomniveau']).'</a></td>';
				}
				if ($niveau['section_niveau'] == 0 and $niveau['numniveau'] >= 3 and $niveau['numniveau'] <= 4)
				{ // Le statut d'animateur doit être lié à une section.
					echo '<td class="rmq">Statut mal param&eacute;tr&eacute;</td>';
				}
				else
				{
					echo '<td>'.$sections[$niveau['section_niveau']]['nomsection'].'</td>';
				}
				echo '<td>';
				echo ($niveau['numniveau'] > 2) ? '<strong>' : '';
				echo $intitules_niveaux[$niveau['numniveau']];
				echo ($niveau['numniveau'] > 2) ? '</strong>' : '';
				echo '</td>';
				echo '<td align="center">';
				echo ($niveau['show_at_inscr'] == 1) ? 'oui' : 'non';
				echo '</td>';
				echo '</tr>';
			} // fin foreach $n_niveaux
?>
</table>
<?php
			if ($_GET['msg'] == 1)
			{
?>
<div class="msg">
  <p class="rmqbleu" align="center">Modification effectu&eacute;e avec succ&egrave;s</p>
</div>
<?php
			}
			if ($_GET['msg'] == 2)
			{
?>
<div class="msg">
  <p class="rmq" align="center">Tu n'as pas les droits suffisants pour cette action.</p>
</div>
<?php
			}
			if ($_GET['msg'] == 3)
			{
?>
<div class="msg">
  <p class="rmq" align="center">Des membres du portail ont ce statut. Suppression impossible !</p>
</div>
<?php
			}
			if ($_GET['msg'] == 4)
			{
?>
<div class="msg">
 <p class="rmq" align="center">Une erreur s'est produite. Echec de la requ&ecirc;te !</p>
</div>
<?php
			}
			if ($_GET['msg'] == 5 and is_numeric($_GET['u']))
			{
?>
<div class="msg">
  <p align="center" class="rmqbleu">Le statut &quot;<?php echo $sections[$_GET['u']]['nomniveau']; ?>&quot; 
  a &eacute;t&eacute; cr&eacute;&eacute; avec succ&egrave;s.</p>
</div>
<?php
			}
			if ($_GET['msg'] == 6)
			{
?>
<div class="msg">
  <p class="rmq" align="center">Des membres ont ce statut actuellement. Suppression impossible !</p>
</div>
<?php
			}
			if ($_GET['msg'] == 7)
			{
?>
<div class="msg">
 <p class="rmq" align="center">Cette action est interdite !</p>
</div>
<?php
			}
		} // fin nbresections > 0
		else
		{
?>
<div class="msg">
  <p align="center">Il n'y a encore aucun statut dans la base de donn&eacute;es.</p>
</div>
<?php
		}
?>
<div class="menu_flottant">
  <h2>Gestion des Statuts</h2>
  <p class="icone"><img src="templates/default/images/gestion_mb_site.png" alt="" width="60" height="45" /></p>
  <p><a href="index.php?page=gestion_statuts_site&amp;do=ajouterniveau" class="menumembres"><img src="templates/default/images/newuser.png" alt="" width="18" height="12" border="0" align="top" /> Ajouter
      un statut</a><br />
    <a href="index.php?page=gestion_statuts_site&amp;do=supprimerniveau" class="menumembres"><img src="templates/default/images/supprimer_statut.png" alt="" width="18" height="12" border="0" align="top" /> Supprimer
    un Statut</a><br />
    <a href="index.php?page=recapitulatif_droits" class="menumembres">Tableau
    r&eacute;capitulatif des droits</a></p>
</div>
<div class="msg">
<p>Les statuts <em>Visiteur</em> et <em>Webmaster</em> ne
  sont pas modifiables pour des raisons de s&eacute;curit&eacute;. Le statut visiteur est utilis&eacute;
  comme statut provisoire &agrave; l'inscription d'un animateur; pour ce qui est du
  statut de Webmaster, modifier son statut risquerait de rendre le portail impossible
  &agrave; g&eacute;rer.</p>
</div>
<?php
	}
	else if ($_POST['do'] == 'ajouterniveau' or $_GET['do'] == 'ajouterniveau')
	{ // création d'un statut membre
		if ($_POST['step'] == 2)
		{ // enregistrement dans la db
			if ($user['niveau']['numniveau'] == 5)
			{
				// les niveaux d'accès 3 et 4 doivent être liés à une section.
				$lien_ok = (($_POST['numniveau'] == 3 or $_POST['numniveau'] == 4) and $_POST['section_niveau'] == 0) ? false : true;
				if (!empty($_POST['nomniveau']) and $lien_ok)
				{
					$nomniveau = htmlentities($_POST['nomniveau'], ENT_QUOTES);
					$nomniveau_interne = htmlentities($_POST['trancheage'], ENT_QUOTES);
					$sql = "INSERT INTO ".PREFIXE_TABLES."site_niveaux (numniveau, section_niveau, nomniveau, show_at_inscr) values ('$_POST[numniveau]', '$_POST[section_niveau]', '$nomniveau', '$_POST[show_at_inscr]')";
					send_sql($db, $sql);
					reset_config();
					header('Location: index.php?page=gestion_statuts_site&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_statuts_site&msg=4');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_statuts_site&msg=2');
			}
		}
		else
		{ // formulaire de création d'un statut membre
?>
<h1>Gestion des statuts des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_statuts_site">Retour 
  &agrave; la page Gestion des Statuts des membres</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomniveau.value != "")
	{
		if ((form.numniveau.value == 3 || form.numniveau.value == 4) && form.section_niveau.value == 0)
		{
			alert("Ce niveau d'accès nécessite de lier le statut à une section/unité !");
			return false;
		}
		else if (form.numniveau.value == 5 && form.section_niveau.value != 0)
		{
			alert("Le niveau d'accès Webmaster ne doit pas être lié à une section/unité.");
			form.section_niveau.value = 0;
			return false;
		}
		else
		{
			return confirm("Es-tu certain de vouloir ajouter ce statut ?");
		}
	}
	else
	{
		alert("Merci de donner au moins un nom au statut que tu ajoutes.");
		return false;
	}
}
//-->
</script>
<form action="gestion_statuts_site.php" method="post" name="form1" id="form1" onsubmit="return check_form(this)" class="form_config_site">
<h2>Ajouter un Statut dans la base de donn&eacute;es</h2>
  <input type="hidden" name="do" value="ajouterniveau" />
  <input type="hidden" name="step" value="2" />
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td valign="top">Nom du Statut</td>
      <td><input name="nomniveau" type="text" id="nomniveau" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Niveau d'acc&egrave;s</td>
      <td><select name="numniveau" id="numniveau">
          <option value="0">0 - Anonyme</option>
		  <option value="1">1 - Visiteur extérieur</option>
		  <option value="2">2 - Membre de l'Unité</option>
		  <option value="3" style="font-weight:bold;">3 - Animateur de Section</option>
		  <option value="4" style="font-weight:bold;">4 - Animateur d'Unité</option>
		  <option value="5" style="font-weight:bold;">5 - Webmaster</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Section li&eacute;e</td>
      <td><select name="section_niveau">
			<option value="0">Aucune</option>
<?php
			foreach ($sections as $section)
			{
				echo '<option value="'.$section['numsection'].'">'.$section['nomsection'].'</option>';
			}
?>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Visible &agrave; l'inscription ?</td>
      <td>
        <input name="show_at_inscr" type="radio" value="1" id="show-1" checked="checked" />
         <label for="show-1">Oui</label> - 
        <input name="show_at_inscr" type="radio" value="0" id="show-0" />
         <label for="show-0">Non</label>
      </td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" name="Submit" value="Ajouter ce Statut" />
  </p>
</form>
<div class="instructions">
  <ul>
    <li>Les niveaux d'acc&egrave;s de 3 &agrave; 
      5 donnent acc&egrave;s aux donn&eacute;es des membres de l'Unit&eacute;.</li>
    <li>Les niveaux d'acc&egrave;s 3 et 4 doivent &ecirc;tre li&eacute;s &agrave; 
      une section ou une unit&eacute;.</li>
    <li>Pour cette raison, leur statut doit &ecirc;tre reconnu par un autre membre 
      du portail ayant ce niveau d'acc&egrave;s ou un niveau sup&eacute;rieur.</li>
    <li>La reconnaissance se fait depuis l'accueil membres.</li>
  </ul>
</div>
<?php
		}
	}
	else if ($_POST['do'] == 'supprimerniveau' or $_GET['do'] == 'supprimerniveau')
	{ // suppression d'un statut de membre sur le portail
		if ($_POST['step'] == 2 and is_numeric($_POST['idniveau']))
		{ // suppression proprement dite de la db
			if ($user['niveau']['numniveau'] == 5)
			{
				if ($_POST['idniveau'] > 2)
				{ // Protection contre la suppression du statut visiteur (utilisé à l'inscription comme statut provisoire) et webmaster
					$sql = "SELECT num FROM ".PREFIXE_TABLES."auteurs WHERE niveau = '$_POST[idniveau]'";
					$res = send_sql($db, $sql);
					if (mysql_num_rows($res) == 0)
					{ // aucun membre du portail n'a ce statut, on peut le supprimer
						$sql = "DELETE FROM ".PREFIXE_TABLES."site_niveaux WHERE idniveau = '$_POST[idniveau]'";
						send_sql($db, $sql);
						reset_config();
						header('Location: index.php?page=gestion_statuts_site&msg=1');
					}
					else
					{ // un ou plusieurs membres ont encore ce statut sur le site, suppression impossible
						header('Location: index.php?page=gestion_statuts_site&msg=3');
					}
				}
				else
				{ // on ne supprime pas le statut visiteur ou webmaster
					header('Location: index.php?page=gestion_statuts_site&msg=7');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_statuts_site&msg=2');
			}
		}
		else
		{ // formulaire de sélection du statut à supprimer
?>
<h1>Gestion des statuts des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_statuts_site">Retour 
  &agrave; la page Gestion des Statuts des membres</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function niveauchoisi(form)
{
	if (form.idniveau.value != "") 
	{
		return confirm('Es-tu certain de vouloir supprimer ce Statut ?'); 
	}
	else 
	{
		alert ("N'oublie pas de choisir un Statut.");
		return false;
	}
}
//-->
</script>
<form action="gestion_statuts_site.php" method="post" name="form" id="form" onsubmit="return niveauchoisi(this)" class="form_config_site">
<h2>Supprimer un Statut</h2>
  <input type="hidden" name="do" value="supprimerniveau" />
  <input type="hidden" name="step" value="2" />
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%">S&eacute;lectionne le Statut &agrave; supprimer :</td>
      <td><select name="idniveau">
          <option value="" selected="selected"></option>
          <?php
				foreach ($niveaux as $niveau)
				{
					if ($niveau['idniveau'] > 2)
					{ // protection du statut visiteur (utilisé comme statut provisoire) et webmaster
?>
          <option value="<?php echo $niveau['idniveau']; ?>"><?php echo $niveau['nomniveau']; ?></option>
          <?php
					}
				}
?>
        </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td colspan="2" class="petitbleu">Pour pouvoir supprimer un Statut, aucun 
        membre du portail ne doit le porter. Tant qu'il reste un membre dans cette 
        section, la suppression est impossible.</td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" value="Supprimer le Statut" />
  </p>
</form>
<?php
		
		}
	}
	else if ($_POST['do'] == 'modifierniveau' or $_GET['do'] == 'modifierniveau')
	{ // modification d'un statut membre
		if ($_POST['step'] == 3 and is_numeric($_POST['idniveau']))
		{ // enregistrement des modifications apportées au statut
			if ($user['niveau']['numniveau'] == 5)
			{
				if ($_POST['idniveau'] > 2)
				{ // protection du statut visiteur (utilisé comme statut provisoire) et webmaster
					// les niveaux d'accès 3 et 4 doivent être liés à une section.
					$lien_ok = (($_POST['numniveau'] == 3 or $_POST['numniveau'] == 4) and $_POST['section_niveau'] == 0) ? false : true;
					if (!empty($_POST['nomniveau']) and $lien_ok)
					{
						$nomniveau = htmlentities($_POST['nomniveau'], ENT_QUOTES);
						// enregistrement des modifications
						$sql = "UPDATE ".PREFIXE_TABLES."site_niveaux SET 
						numniveau = '$_POST[numniveau]', section_niveau = '$_POST[section_niveau]', nomniveau = '$nomniveau',
						show_at_inscr = '$_POST[show_at_inscr]'
						WHERE idniveau = '$_POST[idniveau]'";
						send_sql($db, $sql);
						// mise à jour de la section des membres du portail
						// en effet, histoire de s'économiser des requêtes, la section est enregistrée dans la fiche du membre aussi
						$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET numsection = '$_POST[section_niveau]' WHERE niveau = '$_POST[numniveau]'";
						send_sql($db, $sql);
						reset_config();
						header('Location: index.php?page=gestion_statuts_site&msg=1');
					}
					else
					{
						header('Location: index.php?page=gestion_statuts_site&msg=4');
					}
				}
				else
				{ // tatata, on touche pas aux statuts visiteur et webmaster mifi !
					header('Location: index.php?page=gestion_statuts_site&msg=7');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_statuts_site&msg=2');
			}
		}
		else if ($_GET['step'] == 2 and is_numeric($_GET['idniveau']))
		{ // formulaire de modification du statut sélectionné
			$ligne = $niveaux[$_GET['idniveau']]; // on récupère les données du statut dans la config du site
?>
<h1>Gestion des statuts des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_statuts_site">Retour 
  &agrave; la page Gestion des Statuts des membres</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomniveau.value != "")
	{
		if ((form.numniveau.value == 3 || form.numniveau.value == 4) && form.section_niveau.value == 0)
		{
			alert("Ce niveau d'accès nécessite de lier le statut à une section/unité !");
			return false;
		}
		else if (form.numniveau.value == 5 && form.section_niveau.value != 0)
		{
			alert("Le niveau d'accès Webmaster ne doit pas être lié à une section/unité.");
			form.section_niveau.value = 0;
			return false;
		}
		else
		{
			return confirm("Es-tu certain de vouloir modifier ce statut ?");
		}
	}
	else
	{
		alert("Merci de donner au moins un nom au statut.");
		return false;
	}
}
//-->
</script>
<form action="gestion_statuts_site.php" method="post" name="form1" id="form1" onsubmit="return check_form(this)" class="form_config_site">
<h2>Modifier les infos d'un Statut</h2>
  <input type="hidden" name="do" value="modifierniveau" />
  <input type="hidden" name="step" value="3" />
  <input type="hidden" name="idniveau" value="<?php echo $_GET['idniveau']; ?>" />
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td valign="top">Nom du Statut</td>
      <td><input name="nomniveau" type="text" id="nomniveau" size="40" maxlength="255" value="<?php echo stripslashes($ligne['nomniveau']); ?>" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Niveau d'acc&egrave;s</td>
      <td><select name="numniveau" id="numniveau">
          <option value="0"<?php echo ($ligne['numniveau'] == 0) ? ' selected' : ''; ?>>0 - Anonyme</option>
		  <option value="1"<?php echo ($ligne['numniveau'] == 1) ? ' selected' : ''; ?>>1 - Visiteur extérieur</option>
		  <option value="2"<?php echo ($ligne['numniveau'] == 2) ? ' selected' : ''; ?>>2 - Membre de l'Unité</option>
		  <option value="3" style="font-weight:bold;"<?php echo ($ligne['numniveau'] == 3) ? ' selected' : ''; ?>>3 - Animateur de Section</option>
		  <option value="4" style="font-weight:bold;"<?php echo ($ligne['numniveau'] == 4) ? ' selected' : ''; ?>>4 - Animateur d'Unité</option>
		  <option value="5" style="font-weight:bold;"<?php echo ($ligne['numniveau'] == 5) ? ' selected' : ''; ?>>5 - Webmaster</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Section li&eacute;e</td>
      <td><select name="section_niveau">
			<option value="0">Aucune</option>
<?php
			foreach ($sections as $section)
			{
?>
			<option value="<?php echo $section['numsection']; ?>"<?php echo ($ligne['section_niveau'] == $section['numsection']) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
<?php
			}
?>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Visible &agrave; l'inscription ?</td>
      <td>
        <input name="show_at_inscr" type="radio" value="1" id="show-1"<?php echo ($ligne['show_at_inscr'] == 1) ? ' checked="checked"' : ''; ?> />
         <label for="show-1">Oui</label> - 
        <input name="show_at_inscr" type="radio" value="0" id="show-0"<?php echo ($ligne['show_at_inscr'] == 0) ? ' checked="checked"' : ''; ?> />
         <label for="show-0">Non</label>
      </td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" name="Submit" value="Modifier ce Statut" />
  </p>
</form>
<div class="instructions">
  <ul>
    <li>Les niveaux d'acc&egrave;s de 3 &agrave; 
      5 donnent acc&egrave;s aux donn&eacute;es des membres de l'Unit&eacute;.</li>
    <li>Les niveaux d'acc&egrave;s 3 et 4 doivent &ecirc;tre li&eacute;s &agrave; 
      une section ou une unit&eacute;.</li>
    <li>Pour cette raison, leur statut doit &ecirc;tre reconnu par un autre membre 
      du portail ayant ce niveau d'acc&egrave;s ou un niveau sup&eacute;rieur.</li>
    <li>La reconnaissance se fait depuis l'accueil membres.</li>
  </ul>
</div>
<?php
		
		}
		else
		{ // formulaire de sélection du statut à modifier
?>
<h1>Gestion des statuts des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_statuts_site">Retour 
  &agrave; la page Gestion des Statuts des membres</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function niveauchoisi(form)
{
	if (form.idniveau.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir un Statut.");
		return false;
	}
}
//-->
</script>
<form action="index.php" method="get" name="form" id="form" onsubmit="return niveauchoisi(this)" class="form_config_site">
<h2>Modifier les infos d'un Statut</h2>
  <input type="hidden" name="page" value="gestion_statuts_site" />
  <input type="hidden" name="do" value="modifierniveau" />
  <input type="hidden" name="step" value="2" />
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%" height="25">S&eacute;lectionne le Statut &agrave; modifier 
        :</td>
      <td><select name="idniveau">
          <option value="" selected="selected"></option>
<?php
				foreach ($niveaux as $ligne)
				{
					if ($niveau['idniveau'] > 2)
					{ // protection du statut visiteur (utilisé comme statut provisoire)
			?>
          <option value="<?php echo $ligne['idniveau']; ?>"><?php echo $ligne['nomniveau']; ?></option>
<?php
					}
				}
?>
        </select></td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" value="Sélectionner ce Statut" />
  </p>
</form>
<?php
		}
	}
} // fin du else (numniveau == 5)

if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>
