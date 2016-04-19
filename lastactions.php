<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* lastactions.php - Liste des dernières actions faites par les utilisateurs sur le portail
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

if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Actions r&eacute;centes sur le portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
}
if ($user['niveau']['numniveau'] == 5)
{
?>
<h1>Actions r&eacute;centes sur le portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; 
  l'Accueil Membres</a></p>
<div class="introduction">
<p>Ci-dessous, tu trouveras la liste des diverses op&eacute;rations effectu&eacute;es 
  par les membres sur le site d'Unit&eacute;. Les actions sont class&eacute;es 
  de la plus r&eacute;cente &agrave; la plus ancienne.<br />
  <span class="petitbleu">A noter que les actions affectant la Gestion de l'Unit&eacute; 
  sont consign&eacute;es sur une <a href="index.php?page=lastmodif">page 
  sp&eacute;ciale</a>.</span></p>
</div>
<?php
	// On supprime les actions membres qui ont plus de 20 jours
	$requete = "DELETE FROM ".PREFIXE_TABLES."log_actions WHERE h_action < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '20' DAY)";
	send_sql($db, $requete);

	$liste_pages = "page != 'newad' AND page != 'newmb' AND page != 'newancien' AND page != 'modiffamille' AND page != 'modifmembre' AND page != 'modifancien' AND page != 'passage' AND page != 'passageanciens'";
	$sql = "SELECT * FROM ".PREFIXE_TABLES."log_actions WHERE $liste_pages";
	if ($res = send_sql($db, $sql))
	{
		$nbre_actions_log = mysql_num_rows($res);
		$par = 20;
		if ($nbre_actions_log > $par)
		{
			$nbre_pages_log = floor($nbre_actions_log / $par);
			if ($nbre_pages_log * $par < $nbre_actions_log) $nbre_pages_log++;
		}
		else
		{
			$nbre_pages_log = 1;
		}
	}
	$pg = $_GET['pg'];
	if (isset($pg) and $pg < 1) {$pg = 1;} else if (isset($pg) and $pg > $nbre_pages_log) {$pg = $nbre_pages_log;}
	if (!isset($pg)) {$debut = 0; $pg= 1;} // page en cours
	else {$debut = $par * ($pg-1);}
	$sql = "SELECT *, IF(h_action > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '7' DAY), '1', '0') as 'recent' FROM ".PREFIXE_TABLES."log_actions LEFT JOIN ".PREFIXE_TABLES."auteurs ON numuser = num WHERE ($liste_pages) ORDER BY h_action DESC LIMIT $debut, $par";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
			if ($nbre_pages_log > 1)
			{
?>
<p class="pagination">
<?php
				if ($pg > 1)
				{
					$pgpcdte = $pg - 1;
?><a href="index.php?page=lastactions&amp;pg=<?php echo $pgpcdte; ?>" class="pg_pcdte">Actions plus r&eacute;centes</a> - <?php
				}
?>
<span class="pg">Page <?php echo $pg.' de '.$nbre_pages_log; ?></span>
<?php
				if ($pg < $nbre_pages_log)
				{
					$pgsvte = $pg + 1;
?> - <a href="index.php?page=lastactions&amp;pg=<?php echo $pgsvte; ?>" class="pg_svte">Actions plus anciennes</a><br /><?php
				}
?>
</p>
<?php
			}
?>
		<table width="80%" border="0" cellspacing="0" cellpadding="2" align="center" class="cadrenoir" style="clear:both; ">
			<tr>
			  <th>Action enregistr&eacute;e</th>
			  <th>Auteur</th>
			  <th>Date</th>
			</tr>
<?php
			$j = 0;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2'; 
				$j++;
?>
			<tr class="<?php echo $couleur; ?>">
			  <td><?php echo $debut+$j.'. '; ?><?php echo $ligne['page'].' - '.makehtml($ligne['description_action']); ?></td>
			  <td><?php echo $ligne['pseudo']; ?></td>
			  <td align="right"<?php echo ($ligne['recent'] == 1) ? ' class="petitbleu" title="L\'action date de moins d\'une semaine"' : 'class="petit"'; ?>><?php echo date_ymd_dmy($ligne['h_action'], 'dateheure'); ?></td>
			</tr>
<?php
			}
?>
		</table>
		<p align="center" class="petitbleu">Les dates marqu&eacute;es en bleu datent de moins d'une semaine</p>
<?php
		}
		else
		{
?>
<p align="center" class="rmq">Aucune action n'a &eacute;t&eacute; enregistr&eacute;e r&eacute;cemment</p>
<?php
		}
	}
}
else
{
	include('404.php');
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>