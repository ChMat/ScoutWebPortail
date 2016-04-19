<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* profil_user.php v 1.1.1 - Profil d'un membre du portail
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
*	Correction liens url-rewriting dans les fonctions (variable globale $site)
*	Optimisation de la fonction msgforum
*	Optimisation xhtml
*	Correction de l'activation du lien vers le site du membre
*	Champs vides non affichés
* Modifications v 1.1.1
*	Les animateurs peuvent consulter la date de dernier passage des membres
*/

include_once('connex.php');
include_once('fonc.php');

function dumemeauteur($auteur, $pseudo)
{
	global $db, $site;
	$sql = "SELECT a.numarticle, article_titre, article_datecreation, article_lu, pseudo, article_auteur FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = '$auteur' AND a.article_auteur = b.num AND article_banni != '1' ORDER BY article_datecreation DESC";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			echo '<fieldset><legend>Articles du Tally post&eacute;s par '.$pseudo.'</legend>';
			echo '<table border="0" cellspacing="0" width="100%" align="center" class="cadrenoir">';
			$j = 1;
			while ($ligne = mysql_fetch_assoc($res) and $j <= 5)
			{
				$mm = $j % 2;
				$couleur = ($mm == 0) ? 'td-1' : 'td-2';
				$j++;
				echo '<tr class="'.$couleur.'">';
				$lectures = '';
				if ($ligne['article_lu'] != 0) {$lectures = 'lu '.$ligne['article_lu'].' fois';}
				echo '<td width="20"><img src="templates/default/images/fiche.png" width="18" height="12" alt="'.$lectures.'" /></td>';
				$lien_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$ligne['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$ligne['numarticle'];
				echo '<td width="350"><a href="'.$lien_tally.'" '.$couleur.'>'.$ligne['article_titre'].'</a></td>';
				echo '<td align="right" width="130">'.date_ymd_dmy($ligne['article_datecreation'], 'enlettres').'</td>';
				echo '</tr>';
			}
			if ($num > 5)
			{
				echo '<tr>';
				echo '<td class="petitbleu" width="10%" colspan="3" align="right">'.$pseudo.' a post&eacute; '.$num.' articles.</td>';
				echo '</tr>';
			}
			echo '<tr><td>&nbsp;</td></tr>';
			echo '</table>';
			echo '</fieldset>';
		}
	}
}


if (is_numeric($_GET['user']) or defined('MONPROFIL'))
{
	$numuser = (defined('MONPROFIL')) ? $user['num'] : $_GET['user'];
	$sql = "SELECT * FROM ".PREFIXE_TABLES."auteurs WHERE num = '$numuser' and banni != '1'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$membre = mysql_fetch_assoc($res);
?>
<?php
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Profil membre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<div id="profil_user">
<h1>Profil de <?php echo $membre['pseudo']; ?> </h1>
<div class="panneau">
<h2>Options</h2>
<ul> 
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'listembsite.htm' : 'index.php?page=listembsite'; ?>">Liste des membres</a></li>
    <?php if ($user['niveau']['numniveau'] > 0 and $user['num'] != $numuser) { ?>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>">Voir mon profil</a></li>
    <?php } else if ($user['niveau']['numniveau'] > 0 and $user['num'] == $numuser) { ?>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'modifprofil.htm' : 'index.php?page=modifprofil'; ?>">Modifier mon profil</a></li>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'mailing_liste.htm' : 'index.php?page=mailing_liste'; ?>">Abonnement Newsletter</a></li>
    <?php } if ($user['niveau']['numniveau'] == 5) { ?>
<li><a href="index.php?page=modifmembresite&amp;num=<?php echo $membre['num']; ?>">Modifier la fiche de ce membre</a></li>
<li><a href="index.php?page=gestion_mb_site">Gestion des membres du site</a></li>
	<?php } ?>
</ul>
</div>
<fieldset>
<legend>Infos personnelles</legend>
<?php
		$lavatar = show_avatar($membre['avatar'], 'left'); 
		echo (empty($lavatar)) ? '' : $lavatar;
?>
<ul class="infos_base">
  <li>Pr&eacute;nom et nom : <span class="rmqbleu"><?php echo makehtml($membre['prenom'].' '.$membre['nom']); ?></span></li>
<?php
		if (!empty($membre['totem_scout']) or !empty($membre['quali_scout']))
		{
?>
  <li>Totem : <span class="rmqbleu"><?php echo makehtml($membre['totem_scout'].' '.$membre['quali_scout']); ?></span></li>
<?php
		}
		if (!empty($membre['totem_jungle']))
		{
?>
  <li>Totem de jungle : <span class="rmqbleu"><?php echo makehtml($membre['totem_jungle']); ?></span></li>
<?php
		}
		if ($user['niveau']['numniveau'] > 0)
		{
?>
  <li>Email : <a href="mailto:<?php echo $membre['email']; ?>"><?php echo $membre['email']; ?></a></li>
<?php
		}
?>
  <li>Statut : <span class="petitbleu"><?php echo $niveaux[$membre['niveau']]['nomniveau']; ?></span></li>
<?php
		if ($membre['numsection'] > 0)
		{
?>
  <li>Section : <span class="petitbleu"><?php echo $sections[$membre['numsection']]['nomsection']; ?></span></li>
<?php
		}
		if ($niveaux[$membre['niveau']]['numniveau'] > 2 and $membre['numsection'] != 0 and !empty($sections[$membre['numsection']]['site_section'])) 
		{
			$appellation_section = $sections[$membre['numsection']]['appellation'];
			$lettre_section = $sections[$membre['numsection']]['site_section'];
			$lien_staff = ($site['url_rewriting_actif'] == 1) ? $lettre_section.'_staff.htm' : 'index.php?niv='.$lettre_section.'&amp;page=staff';
?>
  <li class="lien_staff"><?php echo '<a href="'.$lien_staff.'" class="lien petitbleu">Voir le staff '.$appellation_section.'</a>'; ?></li>
<?php
		}
?>
</ul>
<?php
		if (!empty($membre['profilmembre'])) 
		{
?>
<p class="texte_profil"><?php echo makehtml($membre['profilmembre']); ?></p>
<?php
		}
?>
<ul class="infos_plus">
<?php
		if (!empty($membre['loisirs']))
		{
?>
<li>Loisirs : <span class="petitbleu"><?php echo makehtml($membre['loisirs']); ?></span></li>
<?php
		}
		if (!empty($membre['siteweb']))
		{
			$membre['siteweb'] = (ereg('^http://', $membre['siteweb'])) ? $membre['siteweb'] : 'http://'.$membre['siteweb'];
?>
<li>Site web : <?php echo makehtml(' '.$membre['siteweb'].' '); ?></li>
<?php
		}
?>
<li>Membre du portail depuis le <span class="petitbleu"><?php echo date_ymd_dmy($membre['dateinscr'], 'enlettres'); ?></span></li>
<?php
		if ($membre['nbconnex'] > 0 and ($user['niveau']['numniveau'] > 2 or $user['num'] == $_GET['user']))
		{ // nombre de connexions du membre
?>
<li><?php $plconnex = ($membre['nbconnex'] > 1) ? 's' : ''; echo $membre['nbconnex'].' connexion'.$plconnex; ?>
<?php 
			if ($membre['pagesvues'] > 0) 
			{
				$plpg = ($membre['pagesvues'] > 1) ? 's' : ''; 
				echo ' pour '.$membre['pagesvues'].' page'.$plpg.' vue'.$plpg;
			}
?></li>
<?php
			if ($membre['lastconnex'] != '0000-00-00 00:00:00')
			{ // date de la dernière visite
?>
<li>Derni&egrave;re page vue il y a <span class="petitbleu"><?php echo temps_ecoule($membre['lastconnex']); ?></span></li>
<?php
			}
		}
?>
</ul>
<?php
		if ($membre['majprofildone'] == 1)
		{ // date de mise à jour du profil du membre
?>
<p align="right" class="petitbleu">Profil mis &agrave; jour le <?php echo date_ymd_dmy($membre['majprofildate'], 'enlettres'); ?></p>
<?php
		}
		else
		{ // le membre n'a pas encore rempli son profil
?>
<p align="right" class="petitbleu"><?php echo $membre['pseudo']; ?> n'a pas encore rempli son profil</p>
<?php
		}
		if ($user['niveau']['numniveau'] == 5)
		{ // lien vers le log des visites du membres
?>
<p align="right" class="petitbleu"><a href="index.php?page=listevisites&amp;action=bypseudo&amp;pseudo=<?php echo $membre['pseudo']; ?>" class="lienmort">Voir 
  le log des visites de <?php echo $membre['pseudo']; ?></a></p>
<?php
		}
?>
</fieldset>
<?php 
		// Liste des articles du tally
		dumemeauteur($numuser, $membre['pseudo']); 
		
		// La liste des messages du forum a été supprimée suite au nouveau forum
?>
</div>
<?php
	}
	else
	{ // Aucun membre ne correspond au numéro fourni
		$mb_not_exist = true;
		include('listembsite.php');
	}
}
else
{
	include('listembsite.php');
}
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