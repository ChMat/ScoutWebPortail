<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* annif.php v 1.1 - Affichage des anniversaires récents parmi les membres de l'unité
* Les membres du portail n'ont pas de date de naissance sur le portail à moins que tu n'en ajoutes une.
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
*	ajout préférence d'affichage du totem selon la section
*/

if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Les annifs de l'Unité</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Bon anniversaire !</h1>
<div class="introduction">
<p> Chaque jour de l'ann&eacute;e, ou presque, un membre de l'Unit&eacute; f&ecirc;te 
  son anniversaire. Pensez &agrave; le lui souhaiter, &ccedil;a lui fera plaisir !</p>
</div>
<?php
include_once('connex.php');
include_once('fonc.php');
// nbre de jours avant et après aujourd'hui pour afficher les anniversaires
if (!is_numeric($_GET['delai']) or $_GET['delai'] < 1 or $_GET['delai'] > 183) 
{
	$nbrejours_delai_annif = (isset($site['show_annif_days'])) ? $site['show_annif_days'] : 10;
}
else
{
	$nbrejours_delai_annif = $_GET['delai'];
}
$inverse_nbrejours_delai_annif = -1 * $nbrejours_delai_annif;
// Préparation de la requête SQL
// Elle renvoie les membres ayant leur anniversaire autour d'une date déterminée
$sql = "SELECT concat(prenom, ' ', nom_mb) as nom, totem_jungle, concat(totem, ' ', quali) as totem, ddn, section, 
if
(
	to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) <= -183,
	to_days(concat(year(curdate())+1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
	if
	(
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) >= 183,
		to_days(concat(year(curdate())-1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate())
	)
)
AS delai_annif 
FROM ".PREFIXE_TABLES."mb_membres 
WHERE ddn != '0000-00-00' AND actif != 0 AND 
if
(
	to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) <= -183,
	to_days(concat(year(curdate())+1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
	if
	(
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) >= 183,
		to_days(concat(year(curdate())-1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate())
	)
)
 <= $nbrejours_delai_annif 
 AND 
if
(
	to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) <= -183,
	to_days(concat(year(curdate())+1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
	if
	(
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()) >= 183,
		to_days(concat(year(curdate())-1,'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate()),
		to_days(concat(year(curdate()),'-',month (ddn),'-',dayofmonth(ddn))) - to_days(curdate())
	)
)
 >= $inverse_nbrejours_delai_annif 
 ORDER BY delai_annif DESC";
$res = send_sql($db, $sql);
if (mysql_num_rows($res) > 0)
{
?>
<div class="anniversaires">
<?php
	$i = 1;
	while ($ligne = mysql_fetch_assoc($res))
	{ // on met les annifs dans un tableau pour pouvoir comparer les dates précédentes
		$listeannifs[$i] = $ligne;
		$i++;
	}
	$position = 1;
	$num_tableau = 1;
	$division_faite = false;
	while ($position < $i)
	{
		if ($position == 1) 
		{ // c'est la première date à afficher donc aucune ne la précède.
			$delai_pcdt = $listeannifs[1]['delai_annif'];
		}
		else
		{ // on récupère l'annif précédent
			$delai_pcdt = $listeannifs[$position - 1]['delai_annif'];
		}
		if ($delai_pcdt != $listeannifs[$position]['delai_annif'] or $position == 1)
		{ // on passe à une autre date d'anniversaire
			$avant_apres = ($listeannifs[$position]['delai_annif'] <= 0) ? 'avant' : 'apres';
?>
<div class="jour <?php echo $avant_apres; ?>">
<p>
<?php
			// affichage délai annif
			$delai = $listeannifs[$position]['delai_annif'];
			if ($delai < 0) // annif déjà passé
			{
				$delai = $delai * -1; // on positivise le délai pour l'exprimer dans le titre du tableau
				echo ($delai == 1) ? '<span class="rmqbleu">Hier</span> ' : '<span class="rmqbleu">Il y a '.$delai.' jours</span>';
				$verbe = ' a eu ';
			}
			else if ($delai == 0) // annif aujourd'hui
			{
				echo '<span class="rmq">Aujourd\'hui</span>';
				$verbe = ' f&ecirc;te ses ';
			}
			else // annif plus tard
			{
				echo ($delai == 1) ? '<span class="rmqbleu">Demain</span> ' : '<span class="rmqbleu">Dans '.$delai.' jours</span>';
				$verbe = ' aura ';
			}
?> - <span class="petit">le <?php echo date_ymd_dmy($listeannifs[$position]['ddn'], 'jourmois'); ?></span></p>
<ul>
<?php
		} // fin if delai_pcdt
		if (!empty($listeannifs[$position]['totem_jungle']) and $sections[$listeannifs[$position]['section']]['aff_totem_meute'] == 1)
		{ // affichage du totem, sinon du nom (en titre du totem on affiche le nom)
			echo '<li><span title="'.$listeannifs[$position]['nom'].'">'.$listeannifs[$position]['totem_jungle'].'</span>';
		}
		else if (strlen($listeannifs[$position]['totem']) > 1)
		{ // strlen et pas empty car totem contient le totem et le quali (concaténés dans la requête sql)
			echo '<li><span title="'.$listeannifs[$position]['nom'].'">'.$listeannifs[$position]['totem'].'</span>';
		}
		else
		{
			echo '<li><span>'.$listeannifs[$position]['nom'].'</span>';
		}
		echo $verbe; // a eu, a ou aura en fonction du delai
		$newage = age($listeannifs[$position]['ddn'], 2);
		if ($listeannifs[$position]['delai_annif'] > 0) $newage++;
		echo $newage.' ans</li>';
		if ($listeannifs[$position]['delai_annif'] != $listeannifs[$position+1]['delai_annif'] or $position == $i)
		{
?>
</ul>
</div>
<?php
		} // fin if delaiannif suivant
		$position++;
	} // fin boucle position
?>
</div>
<?php
} // fin mysql_num_rows
else
{
?>
<div class="msg">
<p align="center" class="rmqbleu">Pas de chance, personne ne f&ecirc;te son anniversaire ces derniers 
  temps dans l'Unit&eacute;</p>
</div>
<?php
}
?>
<div class="msg">
<p class="petitbleu">
  Seuls sont affich&eacute;s pour le moment les anniversaires qui ont eu lieu 
  durant ces <?php echo $nbrejours_delai_annif; ?> derniers jours ou qui seront f&ecirc;t&eacute;s 
  dans les <?php echo $nbrejours_delai_annif; ?> prochains jours. Si ton anniversaire 
  n'est pas mentionn&eacute; ici, il se peut que tu ne nous aies pas communiqu&eacute; 
  ta date de naissance.</p>
</div>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>