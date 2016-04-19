<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listing_membres_photo.php v 1.1 - Affichage d'un listing photo des membres de l'unité
* Fichier lié : sectionphoto.php
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
*	Suppression popup fiche membre
*	Optimisation xhtml
*	Prise en compte sélection selon année de naissance
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Listing photo</title>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	$restreindre = '';
	if ($_POST['restreint'] == 'tous')
	{
		$restreindre = '';
	}
	else if ($_POST['restreint'] == 'staff')
	{
		$restreindre = "AND fonction > '1'";
	}
	else if ($_POST['restreint'] == 'scouts')
	{
		$restreindre = "AND fonction <= '1'";
	}
	else if ($_POST['restreint'] == 'sizeniers')
	{
		$restreindre = "AND cp_sizenier > '0'";
	}
	if ($_POST['attente'] != 'oui')
	{
		$restreindre .= " AND actif != '0'";
	}
	if (is_numeric($_POST['annee']))
	{
		if ($_POST['quand'] == 'en')
		{
			$restreindre .= ' AND year(ddn) = \''.$_POST['annee'].'\'';
		}
		else if ($_POST['quand'] == 'avant')
		{
			$restreindre .= ' AND year(ddn) < \''.$_POST['annee'].'\'';
		}
		else if ($_POST['quand'] == 'apres')
		{
			$restreindre .= ' AND year(ddn) > \''.$_POST['annee'].'\'';
		}
	}
	$criteretri = '';
	if ($_POST['tri'] == 'nom')
	{
		$criteretri = ', nom_mb, prenom ASC';	
	}
	else if ($_POST['tri'] == 'age')
	{
		$criteretri = ', ddn, nom_mb, prenom ASC';
	}
	else if ($_POST['tri'] == 'cot')
	{
		$criteretri = ', cotisation, nom_mb, prenom ASC';
	}
	else if ($_POST['tri'] == 'siz')
	{
		$criteretri = ', siz_pat, cp_sizenier ASC';
	}
	if (is_unite($_POST['section']) and $_POST['afftotale'] == 1)
	{ // listing d'une unité au complet
		$res = is_unite($_POST['section'], true); // récupération des sections de l'unité pour les inclure dans la requête
		$multi = " (section = '$_POST[section]'";
		if (is_array($res))
		{
			foreach ($res as $a)
			{
				$multi .= " OR section = '$a'";
			}
		}
		$multi .= ')';

		$sql = "SELECT nummb, prenom, nom_mb, actif, photo, ddn FROM ".PREFIXE_TABLES."mb_membres as a LEFT JOIN ".PREFIXE_TABLES."mb_adresses as b ON a.famille = b.numfamille WHERE $multi $restreindre ORDER BY actif DESC $criteretri";
	}
	else if (is_numeric($_POST['section']))
	{
		// listing d'une section particulière
		$sql = "SELECT nummb, prenom, nom_mb, actif, photo, ddn FROM ".PREFIXE_TABLES."mb_membres as a LEFT JOIN ".PREFIXE_TABLES."mb_adresses as b ON a.famille = b.numfamille WHERE section = $_POST[section] $restreindre ORDER BY actif DESC $criteretri";
	}
	if (is_numeric($_POST['section']))
	{
?>
<h1><?php echo $sections[$_POST['section']]['nomsection']; ?> en photos</h1>
<?php
		if ($res = send_sql($db, $sql))
		{
			$nbremembres = mysql_num_rows($res);
			if ($nbremembres > 0)
			{
				if ($nbremembres >= 4)
				{
					//$largeurmoyenne = $largeurtotale / $nbremembres;
					//if ($largeurmoyenne <= 120) {$parligne = 4;} else {$parligne = 4;} // nbre de photos par ligne
					if (!is_numeric($_POST['parligne']))
					{
						$parligne = 6;
					}
					else
					{
						$parligne = $_POST['parligne'];
					}
				}
				else
				{
					$parligne = $nbremembres;
				}
				if ($nbremembres > 0)
				{
					echo '<table cellpadding="2" border="0" cellspacing="2" align="center">';
					for ($numimg = 1; $numimg <= $nbremembres; $numimg++)
					{
						$membre = mysql_fetch_assoc($res);
						if ($numimg == 1 or ($numimg - 1) % $parligne == 0) {echo '<tr>';}
						$couleur = '';
						$couleur = ($membre['actif'] != '1') ? ' bgcolor="#F3F3F3"' : '';
						echo '<td align="center" valign="top"'.$couleur.'>';
						if (!empty($membre['photo']))
						{
							$laphoto = $membre['photo'];
						}
						else
						{
							$laphoto = 'templates/default/images/pasdephoto.gif';
						}
						echo '<a href="index.php?page=fichemb&amp;nummb='.$membre['nummb'].'" target="fichemb">';
						echo '<img src="'.$laphoto.'" border="0" alt="" />';
						echo '</a><br />';
						echo $membre['prenom'].' '.$membre['nom_mb'];
						echo '<br />';
						if ($membre['ddn'] != '0000-00-00') 
						{
							echo date_ymd_dmy($membre['ddn'], 'enchiffres');
							//echo age($membre['ddn'], 0);
						}
						echo '</td>';
						if ($numimg % $parligne != 0 and $numimg == $nbremembres) 
						{
							$position = $numimg;
							while ($position % $parligne != 0)
							{
								echo '<td></td>';
								$position++;
							}
						}
						if ($numimg % $parligne == 0 or $numimg == $nbremembres) {echo '</tr>';}
					}
					echo '</table>';
				}
			}
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
}
?>
</body>
</html>