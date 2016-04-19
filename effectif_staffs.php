<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* effectif_staffs.php v 1.1.1 - Liste des membres des staffs de l'Unité
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
* Modifications v 1.1.1
*	Tri des sections par leur position dans le menu (probablement plus logique que l'ordre de création)
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
<title>Les Staffs de l'Unite</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	}
?>
<h1>Liste des staffs</h1>
<p align="center"><a href="?page=gestion_unite">Retour &agrave; la Page Gestion
    de l'Unit&eacute;</a></p>
<?php
	$sql = "SELECT nummb, prenom, nom_mb, section, fonction, totem, quali, totem_jungle FROM ".PREFIXE_TABLES."mb_membres, ".PREFIXE_TABLES."unite_sections WHERE section = numsection AND fonction > '1' ORDER BY position_section ASC, fonction DESC, nom_mb ASC";
	// affichage des staffs demandés
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
<div class="form_gestion_unite">
<table border="0" align="center" cellspacing="1">
<?php
			$section_liste = '';
			while ($membre = mysql_fetch_assoc($res))
			{
				if ($section_liste != $membre['section'])
				{
					$i = 0;$section_liste = $membre['section'];
?>
  <tr>
    <td colspan="4" class="rmq"><?php echo $sections[$membre['section']]['nomsection']; ?></td>
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
				if ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section'])
				{
?>
      <a href="index.php?page=modifmembre&amp;nummb=<?php echo $membre['nummb']; ?>" class="lienmort" title="Modifier sa fiche"><img src="templates/default/images/fichemb.png" alt="" width="18" height="12" border="0" /></a>
<?php
				}
?>
      <a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>" title="Voir sa fiche"><img src="templates/default/images/membre.png" alt="" width="18" height="12" border="0" /></a> 
<?php
				echo $membre['prenom'].' '.$membre['nom_mb']; 
?></td>
    <td><?php 
				if (!empty($membre['totem_jungle']) and $sections[$membre['section']]['aff_totem_meute'] == 1)
				{
					echo $membre['totem_jungle'];
				}
				else if (!empty($membre['totem']) or !empty($membre['quali'])) 
				{
					echo $membre['totem'].' '.$membre['quali']; 
				} 
?></td>
    <td class="petitbleu">
<?php
				if (strlen($fonctions[$membre['fonction']]['nomfonction']) > 1)
				{
					echo $fonctions[$membre['fonction']]['nomfonction'];
				}
?></td>
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
<p class="petitbleu">Pour un listing plus complet , utilise l'outil <a href="index.php?page=listing_membres">Listing 
  membres</a>.</p>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucun membre n'a &eacute;t&eacute; trouv&eacute;</p>
</div>
<?php
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