<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* effectif_sections.php v 1.1 - Liste des membres des Sections de l'unité
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
*	ajout de la préférence d'affichage du totem selon la section
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
	exit;
}
else
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Liste des membres de l'Unite</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	}
?>
<h1>Liste des membres des Sections</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la Page Gestion 
  de l'Unit&eacute;</a></p>
<div class="instructions">
<p>Pour voir la liste des membres de chaque section de l'Unit&eacute;, s&eacute;lectionne 
  la section que tu veux afficher.<br />
  <span class="petitbleu">Pour un listing plus complet, utilise l'outil <a href="index.php?page=listing_membres">Listing 
  membres</a>.</span> </p>
</div>
<form action="index.php" method="get" id="g">
  <p align="center">
    <input type="hidden" name="page" value="effectif_sections" />
  Afficher la section : 
  <select name="section" onchange="if (this.value > 0) {getElement('g').submit();}">
      <option value="0">Choisis dans la liste</option>
      <?php
	if (!isset($_GET['section']))
	{
		$montrer_section = $user['numsection'];
	}
	else
	{
		$montrer_section = $_GET['section'];
	}
	foreach($sections as $section)
	{
		$selectionne = ($montrer_section == $section['numsection']) ? ' selected' : '';
		echo '<option value="'.$section['numsection'].'"'.$selectionne.'>'.$section['nomsection'].'</option>';
	}
?>
  </select>
  <input type="submit" name="Submit" value="Afficher" />
  </p>
</form></p>
<?php
	if ($montrer_section > 0)
	{
		$sql = "SELECT nummb, prenom, nom_mb, totem, quali, totem_jungle, section, fonction, actif FROM ".PREFIXE_TABLES."mb_membres WHERE section = '".$montrer_section."' ORDER BY fonction, nom_mb, prenom ASC";
		// affichage des staffs demandés
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) > 0)
			{
?>
<div class="form_gestion_unite">
<table border="0" cellspacing="1" align="center">
<?php
				$section_liste = '';
				$fonction_liste_changed = false;
				$fonction_liste = 1;
				while ($membre = mysql_fetch_assoc($res))
				{
					if ($section_liste != $membre['section'])
					{
						$i = 0; // compteur de membres pour la section
						$section_liste = $membre['section']; // nom de la section à afficher
?>
  <tr> 
    <td colspan="4" class="rmq"><?php echo $sections[$membre['section']]['nomsection']; ?></td>
  </tr>
<?php
					}
					if ($fonction_liste != $membre['fonction'] and !$fonction_liste_changed)
					{
						$fonction_liste = $membre['fonction']; // permet d'afficher le staff dans une catégorie à part
						$fonction_liste_changed = true;
						$i = 0;
?>
  <tr> 
    <td colspan="4" class="rmq">Staff <?php echo $sections[$montrer_section]['nomsectionpt']; ?></td>
  </tr>
<?php
					}
					$i++;
					$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>">
    <td width="30" align="right"><?php echo $i.'. '; ?></td>
    <td>
<?php
					if ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section'] or is_section_anciens($membre['section']))
					{
?>
	  <a href="index.php?page=modifmembre&amp;nummb=<?php echo $membre['nummb']; ?>" class="lienmort" title="Modifier sa fiche"><img src="templates/default/images/fichemb.png" alt="" width="18" height="12" border="0" /></a> 
<?php
					}
?>
      <a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>" title="Voir sa fiche"><img src="templates/default/images/membre.png" alt="" width="18" height="12" border="0" /></a> 
<?php 					echo $membre['prenom'].' '.$membre['nom_mb']; ?></td>
    <td>
<?php 
					if (!empty($membre['totem_jungle']) and $sections[$membre['section']]['aff_totem_meute'] == 1)
					{
						echo $membre['totem_jungle'];
					}
					else if (!empty($membre['totem']) or !empty($membre['quali'])) 
					{
						echo $membre['totem'].' '.$membre['quali']; 
					} 
?>
    </td>
    <td class="petitbleu">
<?php
					if ($membre['actif'] == 0 and !is_section_anciens($membre['section']))
					{
?>sur liste d'attente<?php
					}
?>
    </td>
  </tr>
  <?php
				}
?>
</table>
</div>
<div class="instructions">
<h2>Note</h2>
<p>
  Pour consulter la fiche d'un membre, clique sur l'ic&ocirc;ne <img src="templates/default/images/membre.png" alt="" width="18" height="12" />.<br />
  Pour modifier la fiche d'un membre, clique sur l'ic&ocirc;ne <img src="templates/default/images/fichemb.png" alt="" width="18" height="12" />.</p>
</div>
<?php
			}
			else
			{
?>
 <div class="msg">
  <p class="rmq" align="center">Aucun membre n'a &eacute;t&eacute; trouv&eacute;</p>
 </div>
<?php
			}
		}
	}	
} // fin condition connecté
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>