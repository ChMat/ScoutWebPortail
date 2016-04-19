<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listevisites.php v 1.1 - Log des visites du portail
* Activé depuis config_site.php
* Inactif par défaut
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
if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Les visiteurs du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Les visiteurs du portail</h1>
<p align="right" class="petitbleu">Sur le serveur, il est actuellement <?php echo date('G:i:s'); ?>.</p>
<?php 
if ($site['log_visites'] == 1 and $user['niveau']['numniveau'] == 5)
{
	if (!empty($pascemembre))
	{
		echo $pascemembre;
	}
	if ($_GET['action'] == 'show' or empty($_GET['action']))
	{
		$unite_temps = (ereg("^(MINUTE|HOUR|DAY|MONTH)$", $_GET['unite_temps'])) ? $_GET['unite_temps'] : 'HOUR';
		$temps = (is_numeric($_GET['temps']) and $_GET['temps'] > 0) ? $_GET['temps'] : 5;
		$membres = ($_GET['visiteurs'] == 'membres') ? true : false;
?>
<p class="petit">Ci-dessous, tu peux consulter le log des visites sur le portail.<br />
  Clique sur les ic&ocirc;nes pour plus d'informations.</p>
<p class="petit">Voir <a href="index.php?page=listevisites&amp;action=infos_pc">tous 
  les mod&egrave;les de pc</a></p>
<form action="index.php" method="get" name="form" id="form">

  <input type="hidden" name="page" value="listevisites" />
  <input type="hidden" name="action" value="show" />
  Afficher tous 
  <label for="visiteurs">
  <input type="radio" name="visiteurs" id="visiteurs" value="tous"<?php echo (!$membres) ? ' checked="checked"' : ''; ?> />
  les visiteurs</label> et les 
  <label for="membres">
  <input type="radio" name="visiteurs" id="membres" value="membres"<?php echo ($membres) ? ' checked="checked"' : ''; ?> />
  membres</label>
   qui ont visit&eacute;
le portail depuis 
  <input type="text" name="temps" id="temps" value="<?php echo $temps; ?>" style="width:30px;" />
  <select name="unite_temps" id="unite_temps">
    <option value="MINUTE"<?php echo ($unite_temps == 'MINUTE') ? ' selected' : ''; ?>>minutes</option>
    <option value="HOUR"<?php echo ($unite_temps == 'HOUR') ? ' selected' : ''; ?>>heures</option>
    <option value="DAY"<?php echo ($unite_temps == 'DAY') ? ' selected' : ''; ?>>jours</option>
    <option value="MONTH"<?php echo ($unite_temps == 'MONTH') ? ' selected' : ''; ?>>mois</option>
  </select>
  <input type="submit" value="Go" />
</form>
<?php
		// dernières visites
		$limit_membres = ($membres) ? 'numuser > 0 AND' : '';
		$sql = "SELECT 
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byvisiteur&amp;visiteur=', visiteur, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites effectuées par ce visiteur (IP changeante)\" class=\"lienmort\"><img src=\"templates/default/images/fiche.png\" border=\"0\" alt=\"\" /></a>') as '#',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byip&amp;ip=', ip, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de cette adresse IP\" class=\"lienmort\">', ip, '</a>') as IP,
		IF(pseudo_stocke != '', 
			IF (numuser > 0, 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', pseudo_stocke, ' (', numuser, ')</a>'), 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=bypseudo&amp;pseudo=', pseudo_stocke, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre non connect&eacute;\" class=\"lienmort\">', pseudo_stocke, ' (', 0, ')</a>')
			),
			IF(numuser > 0, 
				CONCAT('<em title=\"Connexion sans cookie\">', pseudo, '</em> (<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', numuser, '</a>)'),
				''
			)
		) as 'Membre',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=pagesip&amp;ip=', ip, '&amp;d=', h_dbt, '&amp;f=', h_fin, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les pages consult&eacute;es &agrave; ce moment-l&agrave; depuis cette adresse IP\" class=\"lienmort\">', nbre_clics, ' pages vues</a>') as 'Pages vues',
		IF(h_dbt != h_fin, CONCAT(FLOOR((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) / 60), ' min ', ((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) % 60), ' s'), '') as 'Visite',
		IF(h_dbt != h_fin, CONCAT('le ', date_format(h_dbt, '%d/%c'), ' de ', date_format(h_dbt, '%H:%i:%s'), ' &agrave; ', date_format(h_fin, '%H:%i:%s')), date_format(h_fin, 'le %d/%c &agrave; %H:%i:%s')) as 'Heure visite'
		FROM ".PREFIXE_TABLES."log_visites LEFT JOIN ".PREFIXE_TABLES."auteurs
		ON ".PREFIXE_TABLES."log_visites.numuser = ".PREFIXE_TABLES."auteurs.num
		WHERE ".$limit_membres." h_fin > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '".$temps."' ".$unite_temps.") ORDER BY h_fin DESC";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else if ($_GET['action'] == 'byip' and ereg("([0-9]{1,3}.?){4}", $_GET['ip']))
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<h2>Les visites de l'ip <?php echo $_GET['ip']; ?></h2>
<p class="petit">Ci-dessous, tu peux consulter le log des visites effectu&eacute;es 
  depuis l'adresse ip <strong><?php echo $_GET['ip']; ?></strong>.</p>
<?php
		$sql = "SELECT
		COUNT(*) as visites,
		SUM(nbre_clics) as pagesvues,
		SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) as visite,
		SEC_TO_TIME(SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt))) as temps_total
		FROM ".PREFIXE_TABLES."log_visites
		WHERE ip = '".$_GET['ip']."' GROUP BY ip";
		if ($res = send_sql($db, $sql))
		{
			$stats_ip = mysql_fetch_assoc($res);
			$temps_visite = floor($stats_ip['visite'] / 60).' minutes et '.($stats_ip['visite'] % 60).' secondes';
?>
<p><strong><?php echo $stats_ip['visites']; ?></strong> visites : <strong><?php echo $stats_ip['pagesvues']; ?></strong> 
  pages vues<?php if ($stats_ip['visite'] > 0) { ?> durant <strong><?php echo $temps_visite; ?></strong><?php echo ($stats_ip['visite'] > 0) ? ' ('.$stats_ip['temps_total'].')' : ''; } ?>.</p>
<?php
		}
	
		$sql = "SELECT 
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byvisiteur&amp;visiteur=', visiteur, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites effectuées par ce visiteur (IP changeante)\" class=\"lienmort\"><img src=\"templates/default/images/fiche.png\" border=\"0\" alt=\"\" /></a>') as '#',
		IF(pseudo_stocke != '', 
			IF (numuser > 0, 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', pseudo_stocke, ' (', numuser, ')</a>'), 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=bypseudo&amp;pseudo=', pseudo_stocke, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre non connect&eacute;\" class=\"lienmort\">', pseudo_stocke, ' (', 0, ')</a>')
			),
			IF(numuser > 0, 
				CONCAT('<em title=\"Connexion sans cookie\">', pseudo, '</em> (<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', numuser, '</a>)'),
				''
			)
		) as 'Membre',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=pagesip&amp;ip=', ip, '&amp;d=', h_dbt, '&amp;f=', h_fin, '&amp;unite_temps=$_GET[unite_temps]&amp;temps=$_GET[temps]&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les pages consult&eacute;es &agrave; ce moment-l&agrave; depuis cette adresse IP\" class=\"lienmort\">', nbre_clics, ' pages vues</a>') as 'Pages vues',
		IF(h_dbt != h_fin, CONCAT(FLOOR((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) / 60), ' min ', ((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) % 60), ' s'), '') as 'Visite', 
		IF(h_dbt != h_fin, CONCAT('le ', date_format(h_dbt, '%d/%c'), ' de ', date_format(h_dbt, '%H:%i:%s'), ' &agrave; ', date_format(h_fin, '%H:%i:%s')), date_format(h_fin, 'le %d/%c &agrave; %H:%i:%s')) as 'Heure visite'
		FROM ".PREFIXE_TABLES."log_visites LEFT JOIN ".PREFIXE_TABLES."auteurs
		ON ".PREFIXE_TABLES."log_visites.numuser = ".PREFIXE_TABLES."auteurs.num
		WHERE ip = '".$_GET['ip']."' ORDER BY h_fin DESC";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else if ($_GET['action'] == 'byvisiteur')
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<h2>Les visites d'un visiteur sp&eacute;cifique</h2>
<p class="petit">Ci-dessous, tu peux consulter le log des visites effectu&eacute;es 
  par ce visiteur (depuis un m&ecirc;me ordinateur).</p>
<?php
		$sql = "SELECT
		COUNT(*) as visites,
		SUM(nbre_clics) as pagesvues,
		SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) as visite,
		SEC_TO_TIME(SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt))) as temps_total
		FROM ".PREFIXE_TABLES."log_visites
		WHERE visiteur = '".$_GET['visiteur']."' GROUP BY visiteur";
		if ($res = send_sql($db, $sql))
		{
			$stats_ip = mysql_fetch_assoc($res);
			$temps_visite = floor($stats_ip['visite'] / 60).' minutes et '.($stats_ip['visite'] % 60).' secondes';
?>
<p><strong><?php echo $stats_ip['visites']; ?></strong> visites : <strong><?php echo $stats_ip['pagesvues']; ?></strong> 
  pages vues<?php if ($stats_ip['visite'] > 0) { ?> durant <strong><?php echo $temps_visite; ?></strong><?php echo ($stats_ip['visite'] > 0) ? ' ('.$stats_ip['temps_total'].')' : ''; } ?>.</p>
<?php
		}
	
		$sql = "SELECT 
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byip&amp;ip=', ip, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de cette adresse IP\" class=\"lienmort\">', ip, '</a>') as IP,
		IF(pseudo_stocke != '', 
			IF (numuser > 0, 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', pseudo_stocke, ' (', numuser, ')</a>'), 
				CONCAT('<a href=\"index.php?page=listevisites&amp;action=bypseudo&amp;pseudo=', pseudo_stocke, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre non connect&eacute;\" class=\"lienmort\">', pseudo_stocke, ' (', 0, ')</a>')
			),
			IF(numuser > 0, 
				CONCAT('<em title=\"Connexion sans cookie\">', pseudo, '</em> (<a href=\"index.php?page=listevisites&amp;action=byuser&amp;user=', numuser, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de ce membre\" class=\"lienmort\">', numuser, '</a>)'),
				''
			)
		) as 'Membre',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=pagesip&amp;ip=', ip, '&amp;d=', h_dbt, '&amp;f=', h_fin, '&amp;unite_temps=$_GET[unite_temps]&amp;temps=$_GET[temps]&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les pages consult&eacute;es &agrave; ce moment-l&agrave; depuis cette adresse IP\" class=\"lienmort\">', nbre_clics, ' pages vues</a>') as 'Pages vues',
		IF(h_dbt != h_fin, CONCAT(FLOOR((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) / 60), ' min ', ((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) % 60), ' s'), '') as 'Visite', 
		IF(h_dbt != h_fin, CONCAT('le ', date_format(h_dbt, '%d/%c'), ' de ', date_format(h_dbt, '%H:%i:%s'), ' &agrave; ', date_format(h_fin, '%H:%i:%s')), date_format(h_fin, 'le %d/%c &agrave; %H:%i:%s')) as 'Heure visite'
		FROM ".PREFIXE_TABLES."log_visites LEFT JOIN ".PREFIXE_TABLES."auteurs
		ON ".PREFIXE_TABLES."log_visites.numuser = ".PREFIXE_TABLES."auteurs.num
		WHERE visiteur = '".$_GET['visiteur']."' ORDER BY h_fin DESC";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else if ($_GET['action'] == 'byuser')
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<h2>Les visites de <?php echo untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $_GET['user']); ?></h2>
<p class="petit">Ci-dessous, tu peux consulter le log des visites effectu&eacute;es 
  par ce membre.</p>
<?php
		$sql = "SELECT
		COUNT(*) as visites,
		SUM(nbre_clics) as pagesvues,
		SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) as visite,
		SEC_TO_TIME(SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt))) as temps_total
		FROM ".PREFIXE_TABLES."log_visites
		WHERE numuser = '".$_GET['user']."' GROUP BY numuser";
		if ($res = send_sql($db, $sql))
		{
			$stats_ip = mysql_fetch_assoc($res);
			$temps_visite = floor($stats_ip['visite'] / 60).' minutes et '.($stats_ip['visite'] % 60).' secondes';
?>
<p><strong><?php echo $stats_ip['visites']; ?></strong> visites : <strong><?php echo $stats_ip['pagesvues']; ?></strong> 
  pages vues<?php if ($stats_ip['visite'] > 0) { ?> durant <strong><?php echo $temps_visite; ?></strong><?php echo ($stats_ip['visite'] > 0) ? ' ('.$stats_ip['temps_total'].')' : ''; } ?>.</p>
<?php
		}
	
		$sql = "SELECT 
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byvisiteur&amp;visiteur=', visiteur, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites effectuées par ce visiteur (IP changeante)\" class=\"lienmort\"><img src=\"templates/default/images/fiche.png\" border=\"0\" alt=\"\" /></a>') as '#',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byip&amp;ip=', ip, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de cette adresse IP\" class=\"lienmort\">', ip, '</a>') as IP,
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=pagesip&amp;ip=', ip, '&amp;d=', h_dbt, '&amp;f=', h_fin, '&amp;unite_temps=$_GET[unite_temps]&amp;temps=$_GET[temps]&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les pages consult&eacute;es &agrave; ce moment-l&agrave; depuis cette adresse IP\" class=\"lienmort\">', nbre_clics, ' pages vues</a>') as 'Pages vues',
		IF(h_dbt != h_fin, CONCAT(FLOOR((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) / 60), ' min ', ((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) % 60), ' s'), '') as 'Visite', 
		IF(h_dbt != h_fin, CONCAT('le ', date_format(h_dbt, '%d/%c'), ' de ', date_format(h_dbt, '%H:%i:%s'), ' &agrave; ', date_format(h_fin, '%H:%i:%s')), date_format(h_fin, 'le %d/%c &agrave; %H:%i:%s')) as 'Heure visite'
		FROM ".PREFIXE_TABLES."log_visites LEFT JOIN ".PREFIXE_TABLES."auteurs
		ON ".PREFIXE_TABLES."log_visites.numuser = ".PREFIXE_TABLES."auteurs.num
		WHERE numuser = '".$_GET['user']."' ORDER BY h_fin DESC";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else if ($_GET['action'] == 'bypseudo')
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<h2>Les visites de <?php echo $_GET['pseudo']; ?></h2>
<p class="petit">Ci-dessous, tu peux consulter le log des visites effectu&eacute;es 
  par ce membre m&ecirc;me s'il n'&eacute;tait pas connect&eacute;.</p>
<?php
		$sql = "SELECT
		COUNT(*) as visites,
		SUM(nbre_clics) as pagesvues,
		SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) as visite,
		SEC_TO_TIME(SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt))) as temps_total
		FROM ".PREFIXE_TABLES."log_visites
		WHERE pseudo_stocke = '".$_GET['pseudo']."' GROUP BY pseudo_stocke";
		if ($res = send_sql($db, $sql))
		{
			$stats_ip = mysql_fetch_assoc($res);
			$temps_visite = floor($stats_ip['visite'] / 60).' minutes et '.($stats_ip['visite'] % 60).' secondes';
?>
<p><strong><?php echo $stats_ip['visites']; ?></strong> visites : <strong><?php echo $stats_ip['pagesvues']; ?></strong> 
  pages vues<?php if ($stats_ip['visite'] > 0) { ?> durant <strong><?php echo $temps_visite; ?></strong><?php echo ($stats_ip['visite'] > 0) ? ' ('.$stats_ip['temps_total'].')' : ''; } ?>.</p>
<?php
		}
	
		$sql = "SELECT 
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byvisiteur&amp;visiteur=', visiteur, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites effectuées par ce visiteur (IP changeante)\" class=\"lienmort\"><img src=\"templates/default/images/fiche.png\" border=\"0\" alt=\"\" /></a>') as '#',
		IF (numuser > 0, '<span class=\"rmqbleu\">oui</span>', 'non') as 'Connect&eacute;',
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=byip&amp;ip=', ip, '&amp;unite_temps=$unite_temps&amp;temps=$temps&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les visites de cette adresse IP\" class=\"lienmort\">', ip, '</a>') as IP,
		CONCAT('<a href=\"index.php?page=listevisites&amp;action=pagesip&amp;ip=', ip, '&amp;d=', h_dbt, '&amp;f=', h_fin, '&amp;unite_temps=$_GET[unite_temps]&amp;temps=$_GET[temps]&amp;visiteurs=$_GET[visiteurs]\" title=\"Voir les pages consult&eacute;es &agrave; ce moment-l&agrave; depuis cette adresse IP\" class=\"lienmort\">', nbre_clics, ' pages vues</a>') as 'Pages vues',
		IF(h_dbt != h_fin, CONCAT(FLOOR((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) / 60), ' min ', ((UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) % 60), ' s'), '') as 'Visite', 
		IF(h_dbt != h_fin, CONCAT('le ', date_format(h_dbt, '%d/%c'), ' de ', date_format(h_dbt, '%H:%i:%s'), ' &agrave; ', date_format(h_fin, '%H:%i:%s')), date_format(h_fin, 'le %d/%c &agrave; %H:%i:%s')) as 'Heure visite'
		FROM ".PREFIXE_TABLES."log_visites LEFT JOIN ".PREFIXE_TABLES."auteurs
		ON ".PREFIXE_TABLES."log_visites.numuser = ".PREFIXE_TABLES."auteurs.num
		WHERE pseudo_stocke = '".$_GET['pseudo']."' ORDER BY h_fin DESC";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else if ($_GET['action'] == 'pagesip')
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<h2>Les Pages vues par l'ip <?php echo $_GET['ip']; ?></h2>
<p class="petit">Ci-dessous, tu peux voir les pages consult&eacute;es depuis l'adresse 
  ip <strong><?php echo $_GET['ip']; ?></strong> le <?php echo date_ymd_dmy($_GET['d'], 'dateheure'); ?>.</p>
<?php
		$sql = "SELECT
		COUNT(*) as visites,
		SUM(nbre_clics) as pagesvues,
		SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt)) as visite,
		SEC_TO_TIME(SUM(UNIX_TIMESTAMP(h_fin) - UNIX_TIMESTAMP(h_dbt))) as temps_total
		FROM ".PREFIXE_TABLES."log_visites
		WHERE ip = '".$_GET['ip']."' AND h_dbt = '".$_GET['d']."' AND h_fin = '".$_GET['f']."' GROUP BY ip";
		if ($res = send_sql($db, $sql))
		{
			$stats_ip = mysql_fetch_assoc($res);
			$temps_visite = floor($stats_ip['visite'] / 60).' minutes et '.($stats_ip['visite'] % 60).' secondes';
?>
<p><strong><?php echo $stats_ip['visites']; ?></strong> visites : <strong><?php echo $stats_ip['pagesvues']; ?></strong> 
  pages vues<?php if ($stats_ip['visite'] > 0) { ?> durant <strong><?php echo $temps_visite; ?></strong><?php echo ($stats_ip['visite'] > 0) ? ' ('.$stats_ip['temps_total'].')' : ''; } ?>.</p>
<?php
		}
	
		$sql = "SELECT
		url,
		date_format(time, 'le %d/%c &agrave; %H:%i:%s') as 'Heure',
		pc
		FROM ".PREFIXE_TABLES."log_actions_visiteur
		WHERE ip = '".$_GET['ip']."' AND time >= '".$_GET['d']."' AND time <= '".$_GET['f']."' ORDER BY time DESC";
		if ($res = send_sql($db, $sql))
		{
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
  	<th>URL</th>
	<th>Heure</th>
  </tr>
<?php
			$j = 0;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$j++;
				$info_pc = $ligne['pc'];
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
				echo '<tr class="'.$couleur.'">';
				echo '<td><a href="http://'.$_SERVER['HTTP_HOST'].$ligne['url'].'" class="lien">'.makehtml($ligne['url']).'</a></td>';
				echo '<td width="120">'.$ligne['Heure'].'</td>';
				echo '</tr>';
			}
			if ($j == 0)
			{
				echo '<tr class="'.$couleur.'">';
				echo '<td colspan="2" class="rmq" align="center">Aucune page enregistr&eacute;e sur cette p&eacute;riode</td>';
				echo '</tr>';
			}
?>
</table>
<?php
			if ($j > 0)
			{
?>
<div class="petit">Infos sur le visiteur : <?php echo makehtml($info_pc); ?></div>
<?php
			}
		}
	}
	else if ($_GET['action'] == 'infos_pc')
	{
?>
<div align="center"><a href="index.php?page=listevisites<?php echo '&amp;unite_temps='.$_GET['unite_temps'].'&amp;temps='.$_GET['temps'].'&amp;visiteurs='.$_GET['visiteurs']; ?>">Retour &agrave; la liste des visiteurs du portail</a></div>
<?php
		$sql = "SELECT 
		count(*) as 'Nbre',
		pc as 'Modèle de pc'
		FROM ".PREFIXE_TABLES."log_actions_visiteur
		GROUP BY pc";
		if ($res = send_sql($db, $sql))
		{
			tab_out($res, 2, 1);
		}
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Cette fonction n'est pas accessible.</p>
</div>
<?php
	}
} // fin if log_visites == 1
else if ($user['niveau']['numniveau'] == 5)
{
?>
<div class="msg">
<p align="center" class="rmq">Le log des visites n'est pas activ&eacute; !</p>
<p align="center">Tu peux l'activer depuis la <a href="index.php?page=config_site&amp;categorie=general">page de configuration du portail</a></p>
</div>
<?php
}
else
{
?>
<div class="msg">
<p align="center" class="rmq">Cette fonction n'est accessible qu'au webmaster.</p>
</div>
<?php
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>
