<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_sections.php v 1.1 - Gestion (Création, modification, suppression) des sections/unités
* Cet outil est l'élément central de la gestion de l'unité.
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
	$do = (isset($_GET['do'])) ? $_GET['do'] : $_POST['do'];
	if (empty($do) or !$do)
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des Sections</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
  page Gestion de l'Unit&eacute;</a></p>
<?php
		if (!$sections)
		{
			if ($user['niveau']['numniveau'] == 5)
			{
?>
<div class="msg">
<p align="center" class="rmq">La base de donn&eacute;es ne contient aucune section.</p>
<p align="center">Avant de pouvoir utiliser les fonctions du portail, tu dois <a href="index.php?page=gestion_sections&amp;do=creerunite">cr&eacute;er
    une Unit&eacute;</a>. Chaque unit&eacute; que tu cr&eacute;es sera li&eacute;e &agrave; une section Anciens.
    Tu pourras ensuite ajouter autant de sections que tu le souhaites &agrave; cette
  Unit&eacute;.</p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">La base de donn&eacute;es ne contient aucune section.</p>
<p align="center">Seul le webmaster peut les cr&eacute;er.</p>
</div>
<?php
			}
		}
		else
		{
			$nbresections = count($sections);
?>
<div class="menu_flottant">
<h2>Fonctions disponibles</h2>
<div class="icone"><img src="templates/default/images/gestion_section.png" alt="" width="60" height="45" /></div>
<p><?php
			if ($user['niveau']['numniveau'] == 5)
			{
?>
  - <a href="index.php?page=gestion_sections&amp;do=creerunite" class="menumembres">Cr&eacute;er 
  une Unit&eacute;</a><br />
  - <a href="index.php?page=gestion_sections&amp;do=ajoutersection" class="menumembres">Cr&eacute;er 
  une Section</a><br />
<?php 
			}
			if ($nbresections > 0)
			{
				if ($user['niveau']['numniveau'] == 5)
				{
?>
  - <a href="index.php?page=gestion_sections&amp;do=supprimersection" class="menumembres">Supprimer 
  une Section</a><br />
  - <a href="index.php?page=gestion_sections&amp;do=supprimerunite" class="menumembres">Supprimer 
  une Unit&eacute;</a><br />
<?php
				}
				if ($user['niveau']['numniveau'] == 3)
				{
?>
  - <a href="index.php?page=gestion_sections&amp;do=modifiersection&amp;step=2" class="menumembres">Modifier 
  la Section <?php echo $sections[$user['numsection']]['appellation']; ?></a><br />
<?php
					if ($sections[$user['numsection']]['sizaines'] > 0)
					{
?>
  - <a href="index.php?page=gestion_sizpat" class="menumembres">G&eacute;rer 
  les <?php echo $t_sizaines; ?> de la Section <?php echo $sections[$user['numsection']]['appellation']; ?></a><br />
<?php
					}
				}
				else
				{
?>
  - <a href="index.php?page=gestion_sections&amp;do=modifiersection" class="menumembres">Modifier 
  une Section</a><br />
  - <a href="index.php?page=gestion_sections&amp;do=modifierunite" class="menumembres">Modifier 
  une Unit&eacute;</a> 
<?php
				}
			} // fin if ($sections)
			if ($user['niveau']['numniveau'] == 5)
			{
?>
  <br />
  - <a href="index.php?page=gestion_sections_site" class="menumembres">Espaces Web des Sections</a>
<?php
			}
?>
</p>
</div>
<?php
			if ($nbresections > 0)
			{
?>
<?php
				if ($user['niveau']['numniveau'] > 3)
				{
?>
  <p class="introduction">Cette page te permet de g&eacute;rer les diff&eacute;rentes sections qui existent 
  dans l'Unit&eacute;.</p>
<?php
				}
				else
				{
?>
  <p class="introduction">Sur cette page, tu peux modifier certaines informations relatives &agrave; ta 
  section.</p>
<?php
				}
?>
<?php
				if (!empty($_GET['msg']))
				{
?>
<div class="msg">
<?php
					if ($_GET['msg'] == 1)
					{
?><p class="rmqbleu" align="center">Modification effectu&eacute;e avec succ&egrave;s</p><?php
					}
					if ($_GET['msg'] == 2)
					{
?><p class="rmq" align="center">Tu n'as pas les droits suffisants pour cette action.</p><?php
					}
					if ($_GET['msg'] == 3)
					{
?><p class="rmq" align="center">Cette section n'est pas vide. Suppression impossible !</p><?php
					}
					if ($_GET['msg'] == 4)
					{
?><p class="rmq" align="center">Une erreur s'est produite. Echec de la requ&ecirc;te !</p><?php
					}
					if ($_GET['msg'] == 5 and is_numeric($_GET['u']))
					{
?>
<p align="center" class="rmqbleu">L'Unit&eacute; &quot;<?php echo $sections[$_GET['u']]['nomsection']; ?>&quot; 
  a &eacute;t&eacute; cr&eacute;&eacute;e avec succ&egrave;s.</p>
<p align="center">Tu peux maintenant lui adjoindre ses sections.</p>
<?php
					}
					if ($_GET['msg'] == 6)
					{
?><p class="rmq" align="center">Cette Unit&eacute; n'est pas vide. Suppression impossible !</p><?php
					}
?>
</div>
<?php
				}
?>
<div class="form_config_site">
<h2>Liste des sections</h2>
<ol>
<?php
				$i = 0;
				foreach ($sections as $unite)
				{
					if (is_unite($unite['numsection']))
					{
						$i++;
?>
<li><span class="rmqbleu"><?php echo $unite['nomsection']; ?></span><?php echo (!empty($unite['sigle_section'])) ? ' ('.$unite['sigle_section'].')' : ''; ?>
<?php
						$j = 0;
						foreach ($sections as $section)
						{
							if ($section['unite'] == $unite['numsection'])
							{
								$j++;
								echo ($j == 1) ? '<ol><li>' : '<li>';
								echo $section['nomsection'];
								echo (!empty($section['sigle_section'])) ? ' ('.$section['sigle_section'].')' : '';
								echo '</li>';
							}
						} // fin foreach $section
						if ($j == 0)
						{
							echo '<br />Aucune section dans cette Unit&eacute;';
						}
						else
						{
							echo '</ol>';
						}
?>
</li>
<?php
					} // fin is_unite
				} // fin foreach $unite
?>
</ol>
</div>
<?php
			} // fin nbresections > 0
			else
			{
?>
<div class="msg">
<p align="center">Il n'y a encore aucune section dans la base de donn&eacute;es de l'Unit&eacute;.</p>
</div>
<?php
			}
?>
<?php
			if ($user['niveau']['numniveau'] != 5)
			{
?>
<div class="msg">
<p>Chaque Section peut disposer de ses propres pages web sur
  le portail. <br />
  Demande au webmaster d'activer l'espace web de ta section.</p>
</div>
<?php
			}
		}
	}
	else if ($do == 'creerunite')
	{
		if ($_POST['step'] == 2)
		{
			if ($user['niveau']['numniveau'] > 3)
			{
				if (!empty($_POST['nomsection']))
				{
					$nomsection = htmlentities($_POST['nomsection'], ENT_QUOTES);
					$federation = htmlentities($_POST['federation'], ENT_QUOTES);
					$code_unite = htmlentities($_POST['code_unite'], ENT_QUOTES);
					$ville_unite = htmlentities($_POST['ville_unite'], ENT_QUOTES);
					$appellation = htmlentities($_POST['appellation'], ENT_QUOTES);
					$nomsectionpt = htmlentities($_POST['nomsectionpt'], ENT_QUOTES);
					// enregistrement de la nouvelle unité
					$sql = "INSERT INTO ".PREFIXE_TABLES."unite_sections 
					(unite, nomsection, appellation, nomsectionpt, sigle_section, code_unite, federation, ville_unite, aff_totem_meute) 
					values 
					('0', '$nomsection', '$appellation', '$nomsectionpt', '$_POST[sigle_section]', '$code_unite', '$federation', '$ville_unite', '$_POST[aff_totem_meute]')";
					send_sql($db, $sql);
					$sql = "SELECT numsection FROM ".PREFIXE_TABLES."unite_sections WHERE nomsection = '$nomsection' ORDER BY numsection DESC";
					if ($res = send_sql($db, $sql))
					{
						$ligne = mysql_fetch_assoc($res);
						$num_unite = $ligne['numsection'];
					}
					$nomsection_anciens = 'Anciens '.$nomsectionpt;
					$appellation_anciens = $nomsectionpt = 'Anciens';
					// enregistrement de la Section Anciens de l'Unité
					$sql = "INSERT INTO ".PREFIXE_TABLES."unite_sections 
					(unite, anciens, nomsection, appellation, nomsectionpt) 
					values 
					('$num_unite', '1', '$nomsection_anciens', '$appellation_anciens', '$nomsectionpt')";
					send_sql($db, $sql);
					reset_config();
					log_this('Cr&eacute;ation Unit&eacute; : '.$nomsection, 'gestion_sections');
					header('Location: index.php?page=gestion_sections&msg=5&u='.$num_unite);
				}
				else
				{
					header('Location: index.php?page=gestion_sections&msg=4');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomsection.value != "")
	{
		return confirm("Es-tu certain de vouloir créer cette unité ?");
	}
	else
	{
		alert("Merci de donner au moins un nom à l'Unité.");
		return false;
	}
}
//-->
</script>
<form action="gestion_sections.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return check_form(this)">
  <h2>
    <input type="hidden" name="do" value="creerunite" />
    <input type="hidden" name="step" value="2" />
    Ajouter une Unit&eacute;</h2>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td valign="top">Nom complet de l'Unit&eacute;</td>
      <td><input name="nomsection" type="text" id="nomsection" tabindex="1" size="40" maxlength="255" /> 
      </td>
    </tr>
    <tr class="td-gris">
      <td valign="top">F&eacute;d&eacute;ration</td>
      <td><input name="federation" type="text" id="federation" tabindex="2" maxlength="100" />
        (exple : Les Scouts)</td>
    </tr>
    <tr class="td-gris"> 
      <td valign="top">Ville de l'Unit&eacute;</td>
      <td><input name="ville_unite" type="text" id="ville_unite" tabindex="3" value="<?php echo stripslashes($site['site_ville']);?>" maxlength="100" /></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Nom court de l'Unit&eacute;</td>
      <td><input name="nomsectionpt" type="text" id="nomsectionpt" tabindex="4" size="30" maxlength="50" /> 
        <span class="petit">(exple : Unit&eacute; scoute)</span></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Appellation des membres</td>
      <td><input name="appellation" type="text" id="appellation2" tabindex="5" size="30" maxlength="100" /> 
        <span class="petit">(exple : Scouts)</span></td>
    </tr>
    <tr>
      <td height="23" valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris"> 
      <td height="23" valign="top">Code Unit&eacute;</td>
      <td><input name="code_unite" type="text" id="code_unite" tabindex="6" maxlength="10" />
        (exple : NM-005)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Sigle de l'Unit&eacute;</td>
      <td><input name="sigle_section" type="text" id="sigle_section" tabindex="7" size="3" maxlength="2" />
        (exple: M pour Meute, M1 - M2 si plusieurs sections)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Totem &agrave; afficher</td>
      <td><input name="aff_totem_meute" type="radio" id="aff_totem_meute_non" tabindex="8" value="0" checked="checked" />
        <label for="aff_totem_meute_non">Totem et quali</label> 
        <input name="aff_totem_meute" type="radio" id="aff_totem_meute_oui" tabindex="9" value="1" />
        <label for="aff_totem_meute_oui">Totem de Meute</label></td>
    </tr>
  </table>
  <p align="center"> 
    <input name="Submit" type="submit" tabindex="10" value="Créer cette Unité" />
  </p>
</form>
<div class="instructions"> 
  <h2>Infos utiles
  </h2>
  <p> - Le nom court est simplement le nom tronqu&eacute; de l'unit&eacute; (exple
    : Unit&eacute; scoute ND du Chablis Mignon -&gt; Unit&eacute; scoute)<br />
    - L'appellation des membres est le nom g&eacute;n&eacute;rique des membres
    d'une unit&eacute; : scouts, guides, patronn&eacute;s, ...<br />
    - Le sigle de l'Unit&eacute; est un U par exemple. Ce sigle est utilis&eacute; dans
    les listings membres.<br />
    Si le portail g&egrave;re plusieurs unit&eacute;s, elles peuvent avoir un m&ecirc;me
    sigle ou, par exemple, &ecirc;tre distingu&eacute;es par U1 - U2 - ...</p>
<p class="petitbleu">En cr&eacute;ant l'Unit&eacute;, une Section Anciens sera
  automatiquement cr&eacute;&eacute;e pour l'Unit&eacute; enti&egrave;re. <br />
  Les anciens sont g&eacute;r&eacute;s de mani&egrave;re distincte des membres
  actifs.</p>
<p class="petitbleu">Une fois l'Unit&eacute; cr&eacute;&eacute;e, tu pourras
  lui adjoindre diff&eacute;rentes Sections.</p>
</div><?php
		}
	}
	else if ($do == 'ajoutersection')
	{
		if ($_POST['step'] == 2)
		{
			if ($user['niveau']['numniveau'] > 3)
			{
				if (!empty($_POST['nomsection']))
				{
					$nomsection = htmlentities($_POST['nomsection'], ENT_QUOTES);
					$trancheage = htmlentities($_POST['trancheage'], ENT_QUOTES);
					$appellation = htmlentities($_POST['appellation'], ENT_QUOTES);
					$nomsectionpt = htmlentities($_POST['nomsectionpt'], ENT_QUOTES);
					$sql = "INSERT INTO ".PREFIXE_TABLES."unite_sections 
					(unite, nomsection, sexe, trancheage, appellation, nomsectionpt, sizaines, sigle_section, aff_totem_meute) 
					values 
					('$_POST[unite]', '$nomsection', '$_POST[sexe]', '$trancheage', '$appellation', '$nomsectionpt', '$_POST[sizaines]', '$_POST[sigle_section]', '$_POST[aff_totem_meute]')";
					send_sql($db, $sql);
					reset_config();
					log_this('Cr&eacute;ation Section : '.$nomsection, 'gestion_sections');
					header('Location: index.php?page=gestion_sections&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_sections&msg=4');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion
      des Sections de l'Unit&eacute;</a></p>
<p>
  <script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomsection.value != "")
	{
		 return confirm("Es-tu certain de vouloir ajouter cette section ?");
	}
	else
	{
		alert("Merci de donner au moins un nom à la section que tu ajoutes.");
		return false;
	}
}
//-->
</script>
</p>
<form action="gestion_sections.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return check_form(this)">
  <h2>
    <input type="hidden" name="do" value="ajoutersection" />
    <input type="hidden" name="step" value="2" />
    Ajouter une Section</h2>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris"> 
      <td colspan="2" valign="top">Unit&eacute; dont d&eacute;pend la Section 
        <select name="unite" tabindex="1">
          <?php
			$i = 1;
			foreach($sections as $section)
			{
				if (is_unite($section['numsection']))
				{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($i == 1) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
          <?php
					$i++;
				}
			}
?>
        </select> </td>
    </tr>
    <tr> 
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris"> 
      <td valign="top">Nom complet de la Section</td>
      <td><input name="nomsection" type="text" id="nomsection" tabindex="2" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Nom court de la Section</td>
      <td><input name="nomsectionpt" type="text" id="nomsectionpt" tabindex="3" size="30" maxlength="50" /> 
        <span class="petit">(exple : Ribambelle)</span></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Type de Section</td>
      <td><select name="sexe" id="sexe" tabindex="4">
          <option value="m">Gar&ccedil;ons uniquement</option>
          <option value="f">Filles uniquement</option>
          <option value="x" selected="selected">Section mixte</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td height="26" valign="top">Tranche d'&acirc;ge</td>
      <td><input name="trancheage" type="text" id="trancheage" tabindex="5" size="30" maxlength="100" /> 
        <span class="petit">(exple : de 6 &agrave; 8 ans)</span></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Appellation des membres :</td>
      <td><input name="appellation" type="text" id="appellation2" tabindex="6" size="30" maxlength="100" />
        <span class="petit">(exple : baladins, lutins, castors, ...)</span></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Sizaines ou Patrouilles ?</td>
      <td><select name="sizaines" tabindex="7">
          <option value="0" selected="selected">Aucune</option>
          <option value="1">Sizaines</option>
          <option value="2">Patrouilles</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Sigle de la Section</td>
      <td><input name="sigle_section" type="text" id="sigle_section" tabindex="8" size="3" maxlength="2" />
        (exple: M pour Meute, M1 - M2 si plusieurs sections)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Totem &agrave; afficher</td>
      <td><input name="aff_totem_meute" type="radio" id="aff_totem_meute_non" tabindex="9" value="0" checked="checked" />
        <label for="aff_totem_meute_non">Totem et quali</label> 
        <input name="aff_totem_meute" type="radio" id="aff_totem_meute_oui" tabindex="10" value="1" />
        <label for="aff_totem_meute_oui">Totem de Meute</label></td>
    </tr>
  </table>
  <p align="center"> 
    <input name="Submit" type="submit" tabindex="11" value="Ajouter cette Section" />
</p>
</form>
<div class="instructions">
<h2>Infos utiles</h2>
<p> - Le nom court est simplement le nom tronqu&eacute; de la section (exple : Meute
  du Rocher de Bruxelles-sud -&gt; Meute)<br />
  - L'appellation des membres est le nom g&eacute;n&eacute;rique des membres de
  la section : scouts, guides, patronn&eacute;s, ...<br />
  - Le sigle de la Section est utilis&eacute; dans les listings membres. </p>
</div>
<?php
		}
	}
	else if ($do == 'supprimerunite')
	{
		if ($_POST['step'] == 2 and is_numeric($_POST['numsection']) and is_unite($_POST['numsection']))
		{
			if ($user['niveau']['numniveau'] > 3)
			{
				$vide = true;
				foreach($sections as $section)
				{
					if ($section['unite'] == $_POST['numsection'] and $section['anciens'] != 1)
					{
						$vide = false;
					}
					if ($section['anciens'] == 1)
					{
						$numsection_anciens = $section['numsection'];
					}
				}
				$sql = "SELECT nummb FROM ".PREFIXE_TABLES."mb_membres WHERE section = '$_POST[numsection]' or section = '$numsection_anciens'";
				$res = send_sql($db, $sql);
				$sql = "SELECT idniveau FROM ".PREFIXE_TABLES."site_niveaux WHERE section_niveau = '$_POST[numsection]'";
				$res2 = send_sql($db, $sql);
				if ($vide and mysql_num_rows($res) == 0 and mysql_num_rows($res2) == 0)
				{
					log_this('Suppression Unit&eacute; '.$sections[$_POST['numsection']]['nomsection'], 'gestion_sections');
					// suppression de l'unité
					$sql = "DELETE FROM ".PREFIXE_TABLES."unite_sections WHERE numsection = '$_POST[numsection]'";
					send_sql($db, $sql);
					// récupération du numéro de la section anciens
					$sql = "SELECT numsection FROM ".PREFIXE_TABLES."unite_sections WHERE unite = '$_POST[numsection]' AND anciens = '1'";
					$res = send_sql($db, $sql);
					$ligne = mysql_fetch_assoc($res);
					// toutes les pages du portail dépendant de l'unité ou de la section anciens sont mises au niveau général du portail
					$sql = "UPDATE ".PREFIXE_TABLES."pagessections SET specifiquesection = '0' WHERE specifiquesection = '$_POST[numsection]' OR specifiquesection = '$ligne[numsection]'";
					send_sql($db, $sql);
					// suppression des menus de l'unité et de la section anciens
					$sql = "DELETE FROM ".PREFIXE_TABLES."site_menus WHERE section_menu = '$_POST[numsection]' OR section_menu = '$ligne[numsection]'";
					send_sql($db, $sql);
					// suppression de la section anciens
					$sql = "DELETE FROM ".PREFIXE_TABLES."unite_sections WHERE unite = '$_POST[numsection]' AND anciens = '1'";
					send_sql($db, $sql);
					reset_config();
					header('Location: index.php?page=gestion_sections&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_sections&msg=6');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function sectionchoisie(form)
{
	if (form.numsection.value != "") 
	{
		return confirm('Es-tu certain de vouloir supprimer cette Unité ?'); 
	}
	else 
	{
		alert ("N'oublie pas de choisir une Unité.");
		return false;
	}
}
//-->
</script>
<form action="gestion_sections.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">
  <h2>
    <input type="hidden" name="do" value="supprimerunite" />
    <input type="hidden" name="step" value="2" />
    Supprimer une Unit&eacute;</h2>
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%">S&eacute;lectionne l'Unit&eacute; &agrave; supprimer :</td>
      <td><select name="numsection" tabindex="1">
          <option value="" selected="selected"></option>
          <?php
		  	if ($user['niveau']['numniveau'] > 3)
			{
				foreach ($sections as $section)
				{
					if (is_unite($section['numsection']))
					{
?>
          <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; ?></option>
          <?php
		  			}
				}
			}
?>
        </select></td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" tabindex="2" value="Supprimer l'Unité" />
</p>
</form>
<div class="instructions">
<h2>Pour pouvoir supprimer une Unit&eacute;, tu dois d'abord
  :</h2>
<p> - Supprimer tous les membres de toutes les Sections de l'Unit&eacute; (Anciens
  et Unit&eacute; compris)<br />
  - Supprimer toutes les Sections qui font partie de l'Unit&eacute; (la Section
  Ancien est automatiquement supprim&eacute;e en m&ecirc;me temps que l'Unit&eacute;)<br />
  - Supprimer tous les Statuts de membres du portail li&eacute;s &agrave; cette
  Unit&eacute; </p>
</div>
<?php
		
		}
	}
	else if ($do == 'supprimersection')
	{
		if ($_POST['step'] == 2 and is_numeric($_POST['numsection']) and !is_unite($_POST['numsection']))
		{
			if ($user['niveau']['numniveau'] > 3)
			{
				$sql = "SELECT nummb FROM ".PREFIXE_TABLES."mb_membres WHERE section = '$_POST[numsection]'";
				$res = send_sql($db, $sql);
				$sql = "SELECT idniveau FROM ".PREFIXE_TABLES."site_niveaux WHERE section_niveau = '$_POST[numsection]'";
				$res2 = send_sql($db, $sql);
				if (mysql_num_rows($res) == 0 and mysql_num_rows($res2) == 0)
				{
					log_this('Suppression Section '.$sections[$_POST['numsection']]['nomsection'], 'gestion_sections');
					// suppression de la section
					$sql = "DELETE FROM ".PREFIXE_TABLES."unite_sections WHERE numsection = '$_POST[numsection]'";
					send_sql($db, $sql);
					// suppression des sizaines/patrouilles de la section
					$sql = "DELETE FROM ".PREFIXE_TABLES."unite_sizaines WHERE section_sizpat = '$_POST[numsection]'";
					send_sql($db, $sql);
					// toutes les pages du portail dépendant de la section sont mises au niveau général du portail
					$sql = "UPDATE ".PREFIXE_TABLES."pagessections SET specifiquesection = '0' WHERE specifiquesection = '$_POST[numsection]'";
					send_sql($db, $sql);
					// suppression des menus de la section
					$sql = "DELETE FROM ".PREFIXE_TABLES."site_menus WHERE section_menu = '$_POST[numsection]'";
					send_sql($db, $sql);
					reset_config();
					header('Location: index.php?page=gestion_sections&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_sections&msg=3');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function sectionchoisie(form)
{
	if (form.numsection.value != "") 
	{
		return confirm('Es-tu certain de vouloir supprimer cette Section ?'); 
	}
	else 
	{
		alert ("N'oublie pas de choisir une section.");
		return false;
	}
}
//-->
</script>
<form action="gestion_sections.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">
  <h2>
    <input type="hidden" name="do" value="supprimersection" />
    <input type="hidden" name="step" value="2" />
  Supprimer une Section  
  </h2>
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%">S&eacute;lectionne la Section &agrave; supprimer :</td>
      <td><select name="numsection" tabindex="1">
          <option value="" selected="selected"></option>
          <?php
		  	if ($user['niveau']['numniveau'] > 3)
			{
				foreach ($sections as $section)
				{
					if (!is_unite($section['numsection']) and $section['anciens'] != 1)
					{
?>
          <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; ?></option>
          <?php
		  			}
				}
			}
?>
        </select></td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" tabindex="2" value="Supprimer la section" />
  </p>
</form>
<div class="instructions">
<h2>Pour pouvoir supprimer une Section :</h2>
<p> - Elle ne doit plus compter aucun membre. Tant qu'il reste un membre dans
  cette section, la suppression est impossible.<br />
    - Tous les statuts de membres du portail li&eacute;s &agrave; cette section doivent &ecirc;tre
supprim&eacute;s.</p>
</div>
<?php
		
		}
	}
	else if ($do == 'modifierunite')
	{
		if ($_POST['step'] == 3)
		{
			if ($user['niveau']['numniveau'] > 3 or ($user['numsection'] == $_POST['numsection'] and is_numeric($_POST['numsection'])))
			{
				$nomsection = htmlentities($_POST['nomsection'], ENT_QUOTES);
				$appellation = htmlentities($_POST['appellation'], ENT_QUOTES);
				$nomsectionpt = htmlentities($_POST['nomsectionpt'], ENT_QUOTES);
				$code_unite = htmlentities($_POST['code_unite'], ENT_QUOTES);
				$federation = htmlentities($_POST['federation'], ENT_QUOTES);
				$ville_unite = htmlentities($_POST['ville_unite'], ENT_QUOTES);
				$sql = "UPDATE ".PREFIXE_TABLES."unite_sections SET 
				nomsection = '$nomsection', appellation = '$appellation', nomsectionpt = '$nomsectionpt', 
				sigle_section = '$_POST[sigle_section]',
				federation = '$federation', code_unite = '$code_unite', ville_unite = '$ville_unite', aff_totem_meute = '$_POST[aff_totem_meute]' 
				WHERE numsection = '$_POST[numsection]'";
				send_sql($db, $sql);
				reset_config();
				log_this('Modification Unité '.$nomsection, 'gestion_sections');
				header('Location: index.php?page=gestion_sections&msg=1');
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else if ($_POST['step'] == 2 and is_numeric($_POST['numsection']) and is_unite($_POST['numsection']))
		{
			$ligne = $sections[$_POST['numsection']];
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<form action="gestion_sections.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return confirm('Es-tu certain de vouloir modifier les infos de cette section ?')">
  <h2>
    <input type="hidden" name="do" value="modifiersection" />
    <input type="hidden" name="step" value="3" />
    <input type="hidden" name="numsection" value="<?php echo $_POST['numsection']; ?>" />
    Modifier les infos d'une Unit&eacute;</h2>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td>Nom de l'Unit&eacute;</td>
      <td><input name="nomsection" type="text" id="nomsection" tabindex="1" value="<?php echo $ligne['nomsection']; ?>" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">F&eacute;d&eacute;ration</td>
      <td><input name="federation" type="text" id="federation2" tabindex="2" value="<?php echo $ligne['federation']; ?>" maxlength="100" />
        (exple : Les Scouts)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Code Unit&eacute;</td>
      <td><input name="code_unite" type="text" id="code_unite2" tabindex="3" value="<?php echo $ligne['code_unite']; ?>" maxlength="10" />
        (exple : NM-005)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Ville de l'Unit&eacute;</td>
      <td><input name="ville_unite" type="text" id="ville_unite2" tabindex="4" value="<?php echo $ligne['ville_unite']; ?>" maxlength="100" /></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris"> 
      <td>Nom court</td>
      <td><input name="nomsectionpt" type="text" id="nomsectionpt" tabindex="5" value="<?php echo $ligne['nomsectionpt']; ?>" size="30" maxlength="50" /> 
        <span class="petit">(exple : Ribambelle)</span></td>
    </tr>
    <tr class="td-gris">
      <td>Appellation des membres :</td>
      <td><input name="appellation" type="text" id="appellation" tabindex="6" value="<?php echo $ligne['appellation']; ?>" size="30" maxlength="100" /> 
        <span class="petit">(exple : baladins)</span></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Sigle de l'Unit&eacute;</td>
      <td><input name="sigle_section" type="text" id="sigle_section" tabindex="7" value="<?php echo $ligne['sigle_section']; ?>" size="3" maxlength="2" />
        Lettre majuscule pour les listings (M pour Meute)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Totem &agrave; afficher</td>
      <td><input name="aff_totem_meute" type="radio" id="aff_totem_meute_non" tabindex="8" value="0"<?php echo ($ligne['aff_totem_meute'] == 0) ? ' checked="checked"' : ''; ?> />
        <label for="aff_totem_meute_non">Totem et quali</label> 
        <input name="aff_totem_meute" type="radio" id="aff_totem_meute_oui" tabindex="9" value="1"<?php echo ($ligne['aff_totem_meute'] == 1) ? ' checked="checked"' : ''; ?> />
        <label for="aff_totem_meute_oui">Totem de Meute</label></td>
    </tr>
  </table>
  <p align="center"> 
    <input name="Submit" type="submit" tabindex="10" value="Modifier les infos de cette Unit&eacute;" />
  </p>
</form>
<div class="instructions">
  <h2>Infos utiles </h2>
  <p> - Le nom court est simplement le nom tronqu&eacute; de l'unit&eacute; (exple
    : Unit&eacute; scoute ND du Chablis Mignon -&gt; Unit&eacute; scoute)<br />
    - L'appellation des membres est le nom g&eacute;n&eacute;rique des membres
    d'une unit&eacute; : scouts, guides, patronn&eacute;s, ...<br />
    - Le sigle de l'Unit&eacute; est un U par exemple. Ce sigle est utilis&eacute; dans
    les listings membres.<br />
    Si le portail g&egrave;re plusieurs unit&eacute;s, elles peuvent avoir un
    m&ecirc;me sigle ou, par exemple, &ecirc;tre distingu&eacute;es par U1 -
  U2 
  - ...</p>
</div>
<?php
		
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function sectionchoisie(form)
{
	if (form.numsection.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir une Unité.");
		return false;
	}
}
//-->
</script>
<form action="index.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">

  <h2>
    <input type="hidden" name="page" value="gestion_sections" />
    <input type="hidden" name="do" value="modifierunite" />
    <input type="hidden" name="step" value="2" />
    Modifier les infos d'une Unit&eacute;</h2>
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%" height="25">S&eacute;lectionne l'Unit&eacute; &agrave; modifier 
        :</td>
      <td><select name="numsection" tabindex="1">
          <option value="" selected="selected"></option>
<?php
			foreach ($sections as $ligne)
			{
				if (is_unite($ligne['numsection']))
				{
?>
          <option value="<?php echo $ligne['numsection']; ?>"><?php echo $ligne['nomsection']; ?></option>
<?php
				}
			}
?>
        </select></td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" tabindex="2" value="Sélectionner cette Unit&eacute;" />
  </p>
</form>
<?php
		}
	}
	else if ($do == 'modifiersection')
	{
		if ($_POST['step'] == 3)
		{
			if ($user['niveau']['numniveau'] > 3 or ($user['numsection'] == $_POST['numsection'] and is_numeric($_POST['numsection'])))
			{
				if (!empty($_POST['nomsection']))
				{
					$nomsection = htmlentities($_POST['nomsection'], ENT_QUOTES);
					$trancheage = htmlentities($_POST['trancheage'], ENT_QUOTES);
					$appellation = htmlentities($_POST['appellation'], ENT_QUOTES);
					$nomsectionpt = htmlentities($_POST['nomsectionpt'], ENT_QUOTES);
					$sql = "UPDATE ".PREFIXE_TABLES."unite_sections SET 
					unite = '$_POST[unite]', nomsection = '$nomsection', sexe = '$_POST[sexe]', trancheage = '$trancheage', 
					appellation = '$appellation', nomsectionpt = '$nomsectionpt', sizaines = '$_POST[sizaines]', 
					sigle_section = '$_POST[sigle_section]', aff_totem_meute = '$_POST[aff_totem_meute]' 
					WHERE numsection = '$_POST[numsection]'";
					send_sql($db, $sql);
					reset_config();
					log_this('Modification Section '.$nomsection, 'gestion_sections');
					header('Location: index.php?page=gestion_sections&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_sections&msg=4');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sections&msg=2');
			}
		}
		else if ($_POST['step'] == 2 and is_numeric($_POST['numsection']) and !is_unite($_POST['numsection']))
		{
			$ligne = $sections[$_POST['numsection']];
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomsection.value != "")
	{
		 return confirm("Es-tu certain de vouloir modifier les informations de cette Section ?");
	}
	else
	{
		alert("Merci de donner au moins un nom à la Section que tu ajoutes.");
		return false;
	}
}
//-->
</script>
<form action="gestion_sections.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return check_form(this)">
  <h2>
    <input type="hidden" name="do" value="modifiersection" />
    <input type="hidden" name="step" value="3" />
    <input type="hidden" name="numsection" value="<?php echo $_POST['numsection']; ?>" />
  Modifier les infos d'une Section  
  </h2>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td colspan="2" valign="top">Unit&eacute; dont d&eacute;pend la Section 
        <select name="unite" tabindex="1">
          <?php
			foreach($sections as $section)
			{
				if (is_unite($section['numsection']))
				{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $ligne['unite']) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
          <?php
				}
			}
?>
        </select> </td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris"> 
      <td>Nom de la Section</td>
      <td><input name="nomsection" type="text" id="nomsection" tabindex="2" value="<?php echo $ligne['nomsection']; ?>" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td>Nom court</td>
      <td><input name="nomsectionpt" type="text" id="nomsectionpt" tabindex="3" value="<?php echo $ligne['nomsectionpt']; ?>" size="30" maxlength="50" /> 
        <span class="petit">(exple : Ribambelle)</span></td>
    </tr>
    <tr class="td-gris">
      <td>Type de Section</td>
      <td><select name="sexe" id="sexe" tabindex="4">
          <option value=""></option>
          <option value="m"<?php if ($ligne['sexe'] == 'm') echo ' selected'; ?>>Gar&ccedil;ons uniquement</option>
          <option value="f"<?php if ($ligne['sexe'] == 'f') echo ' selected'; ?>>Filles uniquement</option>
          <option value="x"<?php if ($ligne['sexe'] == 'x') echo ' selected'; ?>>Section mixte</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td>Tranche d'&acirc;ge</td>
      <td><input name="trancheage" type="text" id="trancheage" tabindex="5"  value="<?php echo $ligne['trancheage']; ?>" size="30" maxlength="100" /> 
        <span class="petit">(exple : de 6 &agrave; 8 ans)</span></td>
    </tr>
    <tr class="td-gris">
      <td>Appellation des membres :</td>
      <td><input name="appellation" type="text" id="appellation" tabindex="6" value="<?php echo $ligne['appellation']; ?>" size="30" maxlength="100" /> 
        <span class="petit">(exple : baladins)</span></td>
    </tr>
    <tr class="td-gris">
      <td>Sizaines ou Patrouilles ?</td>
      <td><select name="sizaines" tabindex="7">
          <?php
			$i = 0;
			foreach($f_sizaines as $sizaine)
			{
?>
          <option value="<?php echo $i; ?>" <?php if ($ligne['sizaines'] == $i) echo 'selected'; ?>><?php echo $sizaine; ?></option>
          <?php
				$i++;
			}
?>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Sigle de la Section</td>
      <td><input name="sigle_section" type="text" id="sigle_section" tabindex="8" value="<?php echo $ligne['sigle_section']; ?>" size="3" maxlength="2" />
        Lettre majuscule pour les listings (M pour Meute)</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Totem &agrave; afficher</td>
      <td><input name="aff_totem_meute" type="radio" id="aff_totem_meute_non" tabindex="9" value="0"<?php echo ($ligne['aff_totem_meute'] == 0) ? ' checked="checked"' : ''; ?> />
        <label for="aff_totem_meute_non">Totem et quali</label> 
        <input name="aff_totem_meute" type="radio" id="aff_totem_meute_oui" tabindex="10" value="1"<?php echo ($ligne['aff_totem_meute'] == 1) ? ' checked="checked"' : ''; ?> />
        <label for="aff_totem_meute_oui">Totem de Meute</label></td>
    </tr>
  </table>
  <p align="center"> 
    <input name="Submit" type="submit" tabindex="11" value="Modifier les infos de cette Section" />
  </p>
</form>
<div class="instructions">
  <h2>Infos utiles</h2>
  <p> - Le nom court est simplement le nom tronqu&eacute; de la section (exple
    : Meute du Rocher de Bruxelles-sud -&gt; Meute)<br />
    - L'appellation des membres est le nom g&eacute;n&eacute;rique des membres
    de la section : scouts, guides, patronn&eacute;s, ...<br />
    - Le sigle de la Section est utilis&eacute; dans les listings membres.</p>
</div>
<?php
		
		}
		else
		{
?>
<h1>Gestion des Sections de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; la page Gestion des Sections de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function sectionchoisie(form)
{
	if (form.numsection.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir une section.");
		return false;
	}
}
//-->
</script>
<form action="index.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">

  <h2>
    <input type="hidden" name="page" value="gestion_sections" />
    <input type="hidden" name="do" value="modifiersection" />
    <input type="hidden" name="step" value="2" />
  Modifier les infos d'une Section  
  </h2>
  <table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%" height="25">S&eacute;lectionne la Section &agrave; modifier 
        :</td>
      <td><select name="numsection" tabindex="1">
          <option value="" selected="selected"></option>
<?php
			if ($user['niveau']['numniveau'] < 4)
			{
?>
		  <option value="<?php echo $user['numsection']; ?>" selected="selected"><?php echo $sections[$user['numsection']]['nomsection']; ?></option>
<?php
			}
			else
			{
				foreach ($sections as $ligne)
				{
					if (!is_unite($ligne['numsection']))
					{
			?>
          <option value="<?php echo $ligne['numsection']; ?>"><?php echo $ligne['nomsection']; ?></option>
<?php
					}
				}
			}
?>
        </select></td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" tabindex="2" value="Sélectionner cette section" />
  </p>
</form>
<?php
		}
	}
} // fin du else (numniveau <= 2)
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
