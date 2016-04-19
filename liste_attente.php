<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* liste_attente.php v 1.1 - Gestion de la liste d'attente des sections
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
if ($user['niveau']['numniveau'] > 2)
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Liste d'attente de l'Unit&eacute;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<h1>Gestion de la Liste d'attente</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la Page Gestion de l'Unit&eacute;</a></p>
<p class="introduction">La liste d'attente te permet de pr&eacute;inscrire des <?php echo (!empty($sections[$user['numsection']]['appellation'])) ? $sections[$user['numsection']]['appellation'] : 'Scouts'; ?> dans ta Section. 
  A tout moment, tu peux rejoindre cette page et modifier le statut d'un des membres 
  pour lui faire rejoindre l'effectif de la Section.</p>
<div class="form_config_site">
<?php
	if ($user['niveau']['numniveau'] == 3)
	{
		$sql = "SELECT nummb, nom_mb, prenom, section FROM ".PREFIXE_TABLES."mb_membres WHERE actif = '0' AND section = '$user[numsection]' ORDER BY nom_mb, prenom";
		$res = send_sql($db, $sql);
	}
	else
	{
		$restreindre = '';
		if (count($sections) > 0)
		{
			$restreindre = "WHERE actif = '0'";
			$nbre = 0;
			foreach ($sections as $section)
			{
				if ($section['anciens'] != 1)
				{
					$nbre++;
					$restreindre .= ($nbre > 1) ? ' OR ' : ' AND (';
					$restreindre .= "section = '$section[numsection]'";
				}
			}
			$restreindre .= ($nbre > 0) ? ')' : '';
		}
		$sql = "SELECT nummb, nom_mb, prenom, section FROM ".PREFIXE_TABLES."mb_membres $restreindre ORDER BY section, nom_mb, prenom";
		$res = send_sql($db, $sql);
	}
	$nbre_liste = mysql_num_rows($res);
	if ($nbre_liste > 0)
	{
		$pl = ($nbre_liste > 1) ? 's' : '';
?>
<h2><?php echo $nbre_liste.' membre'.$pl; ?> sur la liste d'attente :</h2>
<table cellpadding="2" cellspacing="0" class="cadrenoir">
<?php
		$j = 0;
		while ($ligne = mysql_fetch_assoc($res))
		{
			$j++;
			$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>">
	<td width="25" align="center"><a href="index.php?page=modifmembre&amp;nummb=<?php echo $ligne['nummb']; ?>" title="Modifier sa fiche membre"><img src="templates/default/images/fichemb.png" border="0" alt="Modifier" /></a></td>
	<td><?php echo $ligne['nom_mb'].' '.$ligne['prenom']; ?></td>
	<td><?php echo $sections[$ligne['section']]['nomsectionpt']; ?></td>
  </tr>
<?php
  		}
?>
</table>
<?php
	}
	else
	{
?>
      <div align="center" class="rmq"><?php echo (!empty($sections[$user['numsection']]['nomsection'])) ? $sections[$user['numsection']]['nomsection'].' : ' : ''; ?>Aucun membre n'est sur la Liste d'attente.</div>
<?php
	}
?>
</div>
<?php
	
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
} // fin numniveau > 2
?>