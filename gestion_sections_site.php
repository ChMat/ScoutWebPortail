<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_sections_site.php v 1.1 - Gestion des espaces web des sections de l'unité 
* Les sections doivent avoir été préalablement créés dans gestion_sections.php
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
*	Ajout d'un script pour suggérer l'indicatif web des sections
*	Protection contre les indicatifs en double
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 5)
{
	include('404.php');
}
else
{
	$to_do = (!empty($_GET['do'])) ? $_GET['do'] : $_POST['do'];
	if (empty($to_do) or !$to_do)
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des Sections du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion de l'espace web des Sections du portail</h1>
<p align="center"><a href="index.php?page=gestion_sections">Retour &agrave; 
  la gestion des sections</a> - <a href="index.php?page=gestion_menus">Retour &agrave; la gestion des menus</a></p>
<?php
		if (!is_array($sections))
		{
?>
<div class="msg">
<p align="center" class="rmq">La base de donn&eacute;es ne contient encore aucune 
  section.</p>
<p align="center"><a href="index.php?page=gestion_sections">Tu peux les 
  cr&eacute;er ici</a></p>
</div>
<?php
		}
		else
		{
			if (!empty($_GET['msg']))
			{
?>
<div class="msg">
<?php
				if ($_GET['msg'] == 1)
				{
?><p class="rmqbleu" align="center">Modifications effectu&eacute;es. V&eacute;rifie bien la nouvelle configuration affich&eacute;e.</p><?php
				}
				if ($_GET['msg'] == 4)
				{
?><p class="rmq" align="center">Une erreur s'est produite. Echec de la requ&ecirc;te !</p><?php
				}
?>
</div>
<?php
			}
		}
?>
<?php
		$i = 0;
		$tri = array('position_section', 'numsection');
		$les_sections = super_sort($sections);
?>
<form action="gestion_sections_site.php" method="post" name="form_sections_site" class="form_config_site" id="form_sections_site">
<h2>Activer et trier les espaces web des sections du portail</h2>
<p> Chaque Section et Unit&eacute; pr&eacute;sente dans la base de donn&eacute;es 
  peut se r&eacute;server une partie du portail pour y cr&eacute;er ses propres pages. 
  Ici, tu peux activer ou d&eacute;sactiver les espaces web de chaque section
  et modifier l'ordre d'affichage des Sections dans le menu du portail.</p>

  <input type="hidden" name="do" value="save_modif" />
<table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
  <tr> 
    <th colspan="2">Nom de la section</th>
	<th>Espace web actif</th>
	<th title="Lettre utilis&eacute;e dans les liens index.php?niv=x ou x_page.htm">Indicatif web</th>
	<th title="Position dans le menu du site">Position section</th>
  </tr>
<?php
		$liste_sections = '';
		foreach ($les_sections as $unite)
		{
			$i = 0;
			if (is_unite($unite['numsection']) or $site['modele_menu'] == 'complet_melange')
			{ // affichage d'une unité ou des sections
				$liste_sections .= (empty($liste_sections)) ? '\''.$unite['numsection'].'\'' : ', \''.$unite['numsection'].'\'';
?>
  <tr class="td-1">
    <td colspan="2"><label for="section_<?php echo $unite['numsection']; ?>" class="rmqbleu"><?php echo $unite['nomsection']; ?></label></td>
	<td align="center"><input type="checkbox" name="section_<?php echo $unite['numsection']; ?>" id="section_<?php echo $unite['numsection']; ?>" value="section_online"<?php echo (empty($unite['site_section'])) ? '' : ' checked="checked"'; ?> onclick="if(this.checked) {getElement('site_section_<?php echo $unite['numsection']; ?>').disabled = false; getElement('position_section_<?php echo $unite['numsection']; ?>').disabled = false; propose_indicatif_web('<?php echo $unite['numsection']; ?>', '<?php echo ereg_replace("[^a-z]", '', strtolower((!empty($unite['nomsectionpt'])) ? $unite['nomsectionpt'] : $unite['nomsection'])); ?>');} else {getElement('site_section_<?php echo $unite['numsection']; ?>').disabled = true; getElement('site_section_<?php echo $unite['numsection']; ?>').value = ''; getElement('position_section_<?php echo $unite['numsection']; ?>').disabled = true;}" /></td>
	<td align="center">
	  <input type="hidden" name="old_section_<?php echo $unite['numsection']; ?>" id="old_section_<?php echo $unite['numsection']; ?>" value="<?php echo $unite['site_section']; ?>" />
	  <input type="text" name="site_section_<?php echo $unite['numsection']; ?>" id="site_section_<?php echo $unite['numsection']; ?>" value="<?php echo $unite['site_section']; ?>" maxlength="1" style="width:25px;"<?php echo (empty($unite['site_section'])) ? ' disabled="true"' : ''; ?> onchange="check_indicatif_web('<?php echo $unite['numsection']; ?>');" /></td>
	<td align="center"><input type="text" name="position_section_<?php echo $unite['numsection']; ?>" id="position_section_<?php echo $unite['numsection']; ?>" value="<?php echo ($unite['position_section'] > 0) ? $unite['position_section'] : $unite['numsection']; ?>" style="width:25px;"<?php echo (empty($unite['site_section'])) ? ' disabled="true"' : ''; ?> onchange="check_position('<?php echo $unite['numsection']; ?>');" /></td>
  </tr>
<?php

				if ($site['modele_menu'] != 'complet_melange')
				{// affichage des sections de l'unité
					foreach ($les_sections as $section)
					{
						if ($section['unite'] == $unite['numsection'])
						{
							$liste_sections .= (empty($liste_sections)) ? '\''.$section['numsection'].'\'' : ', \''.$section['numsection'].'\'';						
?>
  <tr class="td-1">
  	<td width="10">&nbsp;</td> 
    <td><label for="section_<?php echo $section['numsection']; ?>" class="rmqbleu"><?php echo $section['nomsection']; ?></label></td>
	<td align="center"><input type="checkbox" name="section_<?php echo $section['numsection']; ?>" id="section_<?php echo $section['numsection']; ?>" value="section_online"<?php echo (empty($section['site_section'])) ? '' : ' checked="checked"'; ?> onclick="if(this.checked) {getElement('site_section_<?php echo $section['numsection']; ?>').disabled = false; getElement('position_section_<?php echo $section['numsection']; ?>').disabled = false; propose_indicatif_web('<?php echo $section['numsection']; ?>', '<?php echo ereg_replace("[^a-z]", '', strtolower((!empty($section['nomsectionpt'])) ? $section['nomsectionpt'] : $section['nomsection'])); ?>');} else {getElement('site_section_<?php echo $section['numsection']; ?>').disabled = true; getElement('site_section_<?php echo $section['numsection']; ?>').value = ''; getElement('position_section_<?php echo $section['numsection']; ?>').disabled = true;}" /></td>
	<td align="center">
	  <input type="hidden" name="old_section_<?php echo $section['numsection']; ?>" id="old_section_<?php echo $section['numsection']; ?>" value="<?php echo $section['site_section']; ?>" />
	  <input type="text" name="site_section_<?php echo $section['numsection']; ?>" id="site_section_<?php echo $section['numsection']; ?>" value="<?php echo $section['site_section']; ?>" maxlength="1" style="width:25px;"<?php echo (empty($section['site_section'])) ? ' disabled="true"' : ''; ?> onchange="check_indicatif_web('<?php echo $section['numsection']; ?>');" /></td>
	<td align="center"><input type="text" name="position_section_<?php echo $section['numsection']; ?>" id="position_section_<?php echo $section['numsection']; ?>" value="<?php echo ($section['position_section'] > 0) ? $section['position_section'] : $section['numsection']; ?>" style="width:25px;"<?php echo (empty($section['site_section'])) ? ' disabled="true"' : ''; ?> onchange="check_position('<?php echo $unite['numsection']; ?>');" /></td>
  </tr>
<?php
						}
					}
				}
			} // fin du if is_unite
		} // fin foreach $les_sections
?>
</table>
  <p align="center">
    <script type="text/javascript" language="JavaScript">
<!--
var liste_sections = new Array(<?php echo $liste_sections; ?>);

function check_indicatif_web(section)
{ // vérifie que l'indicatif web est au bon format et est unique
	var valeur_unique = true;
	// On met l'indicatif en minuscule
	getElement('site_section_'+section).value = getElement('site_section_'+section).value.toLowerCase();

	var expression = new RegExp("^[a-z]{1}$","gi");
	if (!expression.test(getElement('site_section_'+section).value) && getElement('site_section_'+section).value != '')
	{ // Ce n'est pas une lettre de l'alphabet
		alert("L'indicatif web doit être une lettre de l'alphabet.\n(de a à z, sauf le g qui est réservé pour le niveau général du portail).");
		getElement('site_section_'+section).value = getElement('old_section_'+section).value;
	}
	else if (getElement('site_section_'+section).value != 'g' && getElement('site_section_'+section).value != '')
	{ // La lettre est bonne, on vérifie qu'elle n'est pas déjà utilisée
		var i = 0;
		while (i < liste_sections.length && valeur_unique)
		{
			if (valeur_unique && section != liste_sections[i] && getElement('site_section_'+liste_sections[i]).value == getElement('site_section_'+section).value)
			{ // la lettre est déjà prise
				valeur_unique = false;
			}
			i++;
		}	
		if (!valeur_unique)
		{ // La lettre est déjà prise, on le signale
			alert("Attention !\nDeux indicatifs web se chevauchent parmi les sections ! Cela risque de provoquer des erreurs.\n\nMerci de corriger cela.");
			getElement('site_section_'+section).value = '';
		}
	}
	else if (getElement('site_section_'+section).value == 'g')
	{ // La lettre g est réservée
		alert("Désolé, la lettre 'g' est réservée au niveau général du portail.\nChoisis une autre lettre de l'alphabet.");
		getElement('site_section_'+section).value = '';
	}
	else if (getElement('site_section_'+section).value == '')
	{ // L'utilisateur a laissé un indicatif vide, on désactive l'espace web
		alert("Un indicatif vide désactive l'espace web de la section.\nLes pages déjà créées pour cette section seront inaccessibles.");
		getElement('section_'+section).checked = false;
		getElement('site_section_'+section).disabled = true;
		getElement('position_section_'+section).disabled = true;
	}
	if (getElement('site_section_'+section).value != getElement('old_section_'+section).value && getElement('old_section_'+section).value != '')
	{ // L'utilisateur modifie un indicatif web déjà enregistré
	  // Il devra mettre les liens à jour manuellement
		alert("Après avoir modifié l'indicatif web de la section, tu devras modifier tous les liens qui ont été créés manuellement sur le portail.\n\nExemples : \n" + getElement('old_section_'+section).value + "_page.htm devient " + getElement('site_section_'+section).value + "_page.htm ou\nindex.php?niv=" + getElement('old_section_'+section).value + "&page=page devient index.php?niv=" + getElement('site_section_'+section).value + "&page=page\n\nLes liens dynamiques créés par le portail sont mis à jour automatiquement.");
	}
}

function check_position(section)
{ // vérifie que la position est bien une valeur numérique

	if ((isNaN(getElement('position_section_'+section).value) || getElement('position_section_'+section).value <= 0) && getElement('position_section_'+section).value != '')
	{
		alert("La position doit être une valeur numérique plus grande que 0.");
		getElement('position_section_'+section).value = 1;
	}
	else if (getElement('position_section_'+section).value == '')
	{
		getElement('position_section_'+section).value = 1;
	}
}

function is_lettre_prise(lettre)
{ // vérifie qu'un indicatif suggéré est bien libre
	var i = 0;
	var libre = true;
	if (lettre == 'g') return false; // g est réservé
	while (i < liste_sections.length && libre)
	{ // on parcourt les indicatifs utilisés
		if (libre && getElement('site_section_'+liste_sections[i]).value == lettre)
		{ // il est pris
			return false;
		}
		i++;
	}
	return libre;
}

// Le message d'info sur la proposition d'indicatif n'est affiché qu'une fois
var avertissement_sur_proposition = false;

function propose_indicatif_web(section, nom_section)
{ // propose un indicatif web utilisable
	var indicatif_trouve = false;
	var i = 0;
	if (getElement('site_section_'+section).value == '')
	{
		if (!avertissement_sur_proposition)
		{ // affiché une seule fois
			alert("Le portail essaie de te proposer l'indicatif web le plus intuitif et une position pour la section. Tu peux les modifier à ta guise.");
			avertissement_sur_proposition = true;
		}
		if (getElement('old_section_'+section).value != '')
		{
			lettre_suggeree = getElement('old_section_'+section).value;
			indicatif_trouve = is_lettre_prise(lettre_suggeree);
		}
		if (nom_section != '' && !indicatif_trouve)
		{ // si le nom de la section est défini, on prend l'une de ses lettres
			while (!indicatif_trouve && i <= nom_section.length)
			{ // on teste les lettres du nom de la section une par une
				lettre_suggeree = nom_section.substr(i, 1);
				indicatif_trouve = is_lettre_prise(lettre_suggeree);
				i++;
			}
			if (!indicatif_trouve && nom_section.substr(0, 1) != 'z')
			{ // on teste la lettre suivante de l'alphabet (par rapport à la première lettre du nom de section)
				lettre_suggeree = chr(ord(nom_section.substr(0, 1)) + 1);
				indicatif_trouve = is_lettre_prise(lettre_suggeree);
			}
		}
		// Si toutes les lettres du nom de la section sont prises, on passe l'alphabet en revue
		i = 97; // = a
		while (!indicatif_trouve && i <= 122)
		{ // on parcourt l'alphabet lettre par lettre (a = 97, z = 122)
			lettre_suggeree = String.fromCharCode(i);
			indicatif_trouve = is_lettre_prise(lettre_suggeree);
			i++;
		}
		if (indicatif_trouve)
		{ // on a trouvé un indicatif libre
			getElement('site_section_'+section).value = lettre_suggeree;
		}
		else
		{ // aucun indicatif trouvé, on propose au webmaster de contacter l'auteur de SWP
			alert("Apparemment, ton site a épuisé les indicatifs web disponibles...\n\nMerci de le signaler sur le site du Scout Web Portail afin qu'une solution soit trouvée.\n\nhttp://www.scoutwebportail.org\nL'espace de la section que tu souhaitais ouvrir est désactivé.");
			getElement('site_section_'+section).disabled = true;
			getElement('position_section_'+section).disabled = true;
			getElement('section_'+section).checked = false;
		}
	}
}

//-->
</script>
    <noscript class="rmq">
    Merci d'activer le Javascript dans ton navigateur. 
    </noscript>
  </p>
  <p align="center">
    <input type="submit" value="Enregistrer les modifications" id="enregistrer" disabled="true" />
  </p>
<script type="text/javascript" language="JavaScript">
<!--
getElement('enregistrer').disabled = false;
//-->
</script>
</form>
<div class="instructions">
<h2>Quelques informations utiles</h2>
<p>   En activant l'espace web d'une section, un lien sera ajout&eacute; au menu du site. Tu pourras ensuite cr&eacute;er les pages de la section et ajouter des liens vers les pages de cette section. <br />
  - <strong>Deux sections ne peuvent pas porter le m&ecirc;me indicatif web</strong>. L'indicatif web est une lettre de l'alphabet qui est utilis&eacute;e dans l'url des pages : <strong>x</strong>_page.htm ou index.php?niv=<strong>x</strong>&amp;page=page.<br />
  - Si tu modifies l'indicatif web d'une section, n'oublie pas de modifier tous 
  les liens menant vers cette section avec le nouvel indicatif (c&agrave;d tous les liens cr&eacute;&eacute;s manuellement; les liens cr&eacute;&eacute;s par le portail sont mis &agrave; jour automatiquement).<br />
  - La position de la section est simplement la position qu'elle occupe par rapport
  aux autres sections pr&eacute;sentes dans le menu du portail, &agrave; l'int&eacute;rieur 
  d'une Unit&eacute;. (1 pour la premi&egrave;re, 2 pour la suivante, ...) Tu 
  peux passer des num&eacute;ros si tu envisages d'ajouter des sections par apr&egrave;s
  (1, 2, _, _, 5, 6, 7).<br />
  - <span class="petitbleu">Si une section manque dans la liste, <a href="index.php?page=gestion_sections" title="Gestion des Sections de l'Unit&eacute;">tu 
peux l'ajouter ici</a></span>.</p>
</div>
<?php
	}
	else if ($to_do == 'save_modif')
	{
		if (is_array($sections))
		{
			foreach($sections as $section_test)
			{
				$indicatif[$section_test['numsection']] = $_POST['site_section_'.$section_test['numsection']];
			}
			foreach($sections as $section)
			{
				if ($_POST['section_'.$section['numsection']] == 'section_online')
				{ // si l'espace web de la section est activé, il faut satisfaire à plusieurs critères
					$site_section = strtolower($_POST['site_section_'.$section['numsection']]);

					foreach($indicatif as $numsection => $indicatif_section)
					{ // vérification de l'unicité de l'indicatif web
						if ($_POST['site_section_'.$section['numsection']] == $indicatif_section and $section['numsection'] != $numsection)
						{
							$site_section = '';
						}
					}
					// $site_section doit être une lettre de a à z, sauf g qui est le caractère réservé pour le niveau général du portail.
					$site_section = (ereg("^[a-z]{1}$", $site_section) and $site_section != 'g') ? $site_section : '';
					// la position de la section peut ne pas être unique, cela n'a d'effet que sur le triage des sections à l'affichage.
					$position_section = (!empty($site_section) and is_numeric($_POST['position_section_'.$section['numsection']])) ? $_POST['position_section_'.$section['numsection']] : 0;
				}
				else
				{ // l'espace web de la section n'est pas actif.
				  // on met les données à jour en cas de changement (actif -> inactif, par exemple).
					$site_section = '';
					$position_section = 0;
				}
				// enregistrement des données, section par section à chaque tour de foreach
				$sql = "UPDATE ".PREFIXE_TABLES."unite_sections SET 
				site_section = '$site_section', position_section = '$position_section'
				WHERE numsection = '$section[numsection]'";
				send_sql($db, $sql);
			}
			reset_config();
			log_this('Gestion des espaces web des sections', 'gestion_sections_site');
			header('Location: index.php?page=gestion_sections_site&msg=1');
		}
		else
		{
			header('Location: index.php?page=gestion_sections_site&msg=4');
		}
	} // fin du else if $to_do == save_modif
	else
	{
		echo 'perdu';
	}
} // fin du else (numniveau < 5)
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>