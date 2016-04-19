<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_unite.php v 1.1.1 - Accueil de la Gestion de l'unité avec les liens vers tous les outils liés
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
*	Ajout lien gestion des sizaines et passages pour le webmaster
*	Chargement fonctions avancées ici plutôt que dans fonc.php à chaque fois
* Modifications v 1.1.1
*	Les animateurs de section peuvent maintenant supprimer les membres et les familles un par un
*/

include_once('connex.php');
include_once('fonc.php');
if (!is_array($sections))
{ // les sections doivent être définies pour avoir accès à la gestion de l'unité
	include('gestion_sections.php');
}
else if ($site['gestion_membres_actif'] != 1 and $user['niveau']['numniveau'] < 5)
{ // et la gestion de l'unité doit être activée sur le site
	include('module_desactive.php');
}
else
{ // on peut lancer la gestion de l'unité
	if ($user['niveau']['numniveau'] <= 2)
	{ // tout compte fait non, ce con n'est pas animateur donc on le jette.
		include('404.php');
	}
	else
	{ // allez m'fi tu es animateur, bonne lecture !
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des membres</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
		if ($site['gestion_membres_actif'] != 1)
		{ // le webmaster visite la gestion de l'unité alors qu'elle est inactive. On le prévient gentiment
?>
<p id="message_important">Gestion des membres inactive <a href="index.php?page=config_site&amp;categorie=general" title="Activer la gestion des membres"><img src="templates/default/images/autres.png" alt="Activer la gestion des membres" /></a></p>
<?php
		}
?>
<div id="gestion_unite">
<h1>Gestion de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'accueil Membres</a></p>
<div id="infos_generales">
<h2>Informations g&eacute;n&eacute;rales</h2>
  <?php
		if ($user['niveau']['numniveau'] == 3)
		{ // affichage de l'effectif de la section (AnS) : nombre d'actifs + liste d'attente
?>
<p><span class="rmqbleu"><?php echo $sections[$user['numsection']]['nomsection']; ?></span> : 
<?php
			$sql = "SELECT count(*) as nombre, actif FROM ".PREFIXE_TABLES."mb_membres WHERE section = '$user[numsection]' GROUP BY section, actif ORDER BY actif DESC";
			$res = send_sql($db, $sql);
			$nb = mysql_num_rows($res);
			while ($ligne = mysql_fetch_assoc($res) and $nb > 0)
			{
				echo '<a href="index.php?page=effectif_sections" class="lienmort" title="Voir les membres de la Section">';
				if ($ligne['actif'] == 1)
				{
					$actif = $ligne['nombre'];
					if ($actif > 0)
					{
						$pl = ($actif > 1) ? 's' : '';
						echo $actif.' membre'.$pl.' actif'.$pl;
					}
					else
					{
						echo 'Aucun membre actif';
					}
				}
				else
				{
					$attente = $ligne['nombre'];
					if ($nb > 1) echo ' et ';
					if ($attente > 0)
					{
						$pl2 = ($attente > 1) ? 's' : '';
						echo '<a href="index.php?page=liste_attente" title="Voir les membres sur liste d\'attente" class="lien rmq">'.$attente.' membre'.$pl2.' sur liste d\'attente</a>';
					}
				}
				echo '</a>';
			}
			if ($nb == 0)
			{
				echo 'La base de donn&eacute;es ne contient aucun membre actif dans cette section';
			}
?></p>
<?php
		}
		else if ($user['niveau']['numniveau'] > 3)
		{ // affichage de l'effectif de l'unité (AnU) : nombre d'actifs + liste d'attente
?>
<?php
			$restreindre = '';
			if (count($sections) > 0)
			{
				$restreindre = 'WHERE ';
				$nbre = 0;
				foreach ($sections as $section)
				{
					if ($section['anciens'] != 1)
					{
						$nbre++;
						$restreindre .= ($nbre > 1) ? ' OR ' : '';
						$restreindre .= "section = '$section[numsection]'";
					}
				}
			}
			$sql = "SELECT count(*) as nombre, actif FROM ".PREFIXE_TABLES."mb_membres $restreindre GROUP BY actif ORDER BY actif DESC";
			$res = send_sql($db, $sql);
			$nb = mysql_num_rows($res);
?>
<p>
<?php
			while ($ligne = mysql_fetch_assoc($res) and $nb > 0)
			{
				if ($ligne['actif'] == 1)
				{
					echo '<a href="index.php?page=effectif_sections" class="lienmort" title="Voir la liste des membres">';
					$actif = $ligne['nombre'];
					if ($actif > 0)
					{
						$pl = ($actif > 1) ? 's' : '';
						echo 'La base de donn&eacute;es contient '.$actif.' membre'.$pl.' actif'.$pl;
					}
					else
					{
						echo '<span class="rmq">La base de donn&eacute;es ne contient aucun membre actif</span>';
					}
					echo '</a>';
				}
				else
				{
					$attente = $ligne['nombre'];
					if ($nb > 1) echo ' et ';
					if ($attente > 0)
					{
						$pl2 = ($attente > 1) ? 's' : '';
						echo '<a href="index.php?page=liste_attente" title="Voir les membres sur liste d\'attente" class="lien rmq">'.$attente.' membre'.$pl2.' sur liste d\'attente</a>';
					}
				}
			}
			if ($nb == 0)
			{
				echo 'La base de donn&eacute;es ne contient aucun membre.';
			}
?></p>
<?php
		}
		if ($user['niveau']['numniveau'] == 3 and $nb > 0)
		{ // affichage de l'état de paiement des cotisations : en ordre + pas en ordre (AnS)
?>
<p><span class="rmqbleu">Cotisation</span> : 
<?php
			$sql = "SELECT count(*) as nombre, cotisation FROM ".PREFIXE_TABLES."mb_membres WHERE section = '$user[numsection]' GROUP BY section, cotisation ORDER BY cotisation DESC";
			$res = send_sql($db, $sql);
			$nb = mysql_num_rows($res);
			$ok = $pasok = 0;
			while ($ligne = mysql_fetch_assoc($res) and $nb > 0)
			{
				if ($ligne['cotisation'] == 1)
				{
					$ok = $ligne['nombre'];
					if ($ok > 0)
					{
						$pl = ($ok > 1) ? 's' : '';
						echo $ok.' membre'.$pl.' en ordre';
					}
				}
				else
				{
					$pasok += $ligne['nombre'];
				}
			}
			if ($pasok > 0)
			{
				if ($ok > 0) echo ' et ';
				$pl2 = ($pasok > 1) ? 's' : '';
				echo '<a href="index.php?page=etatcotisations&amp;section='.$user['numsection'].'" title="Voir l\'Etat des Cotisations" class="lien rmq">'.$pasok.' membre'.$pl2.' pas encore en ordre</a>';
			}
			if ($nb == 0)
			{
				echo 'Aucun membre dans la base pour l\'instant.';
			}
?></p>
<?php
		}
		else if ($user['niveau']['numniveau'] > 3 and $nb > 0)
		{ // affichage de l'état de paiement des cotisations (AnU)
?>
<p><span class="rmqbleu">Cotisation</span> : 
<?php
			$restreindre = '';
			if (count($sections) > 0)
			{
				$restreindre = "WHERE actif = '1' ";
				$nbre = 0;
				foreach ($sections as $section)
				{ // on compose la liste des sections
					if ($section['anciens'] != 1)
					{ // les anciens ne paient pas de cotisation, ils sont donc exclus du compte
						$nbre++;
						$restreindre .= ($nbre == 1) ? ' AND (' : '';
						$restreindre .= ($nbre > 1) ? ' OR ' : '';
						$restreindre .= "section = '$section[numsection]'";
					}
				}
				$restreindre .= ($nbre >= 1) ? ')' : ''; 
			}
			$sql = "SELECT count(*) as nombre, cotisation FROM ".PREFIXE_TABLES."mb_membres $restreindre GROUP BY cotisation ORDER BY cotisation DESC";
			$res = send_sql($db, $sql);
			$nb = mysql_num_rows($res);
			$ok = $pasok = 0;
			while ($ligne = mysql_fetch_assoc($res) and $nb > 0)
			{
				if ($ligne['cotisation'] == 1)
				{
					$ok = $ligne['nombre'];
					if ($ok > 0)
					{
						$pl = ($ok > 1) ? 's' : '';
						echo $ok.' membre'.$pl.' en ordre';
					}
				}
				else
				{
					$pasok += $ligne['nombre'];
				}
			}
			if ($pasok > 0)
			{
				if ($ok > 0) echo ' et ';
				$pl2 = ($pasok > 1) ? 's' : '';
				echo '<a href="index.php?page=etatcotisations" title="Voir l\'Etat des Cotisations" class="lien rmq">'.$pasok.' membre'.$pl2.' pas encore en ordre</a>';
			}
			if ($nb == 0)
			{
				echo 'Aucun membre dans la base pour l\'instant.';
			}
?></p>
<?php
		}
		// affichage des modifications récentes dans la gestion de l'unité
		include_once('prv/fonc_moteurs.php'); // chargement fonctions avancées du portail
		$liste_pages = "page = 'newad' OR page = 'newmb' OR page = 'newancien' OR page = 'modiffamille' OR page = 'modifmembre' OR page = 'modifancien' OR page = 'passage' OR page = 'passageanciens' OR page = 'gestioncotisations'";
		$modif_mb_recentes = gestion_unite_lastmodif($liste_pages);
		if ($modif_mb_recentes['nbre'] > 0)
		{
			$pl_mot = ($modif_mb_recentes['nbre'] > 1) ? 's' : '';
?>
<p><span class="rmqbleu">Modifications r&eacute;centes : </span><?php echo '<a href="index.php?page=lastmodif">'.$modif_mb_recentes['nbre'].' action'.$pl_mot.'</a> depuis une semaine (derni&egrave;re il y a '.temps_ecoule($modif_mb_recentes['last']).').'; ?></p>
<?php
		}
		else
		{
?>
<p>Aucune modification n'a eu lieu r&eacute;cemment.</p>
<?php
		}
?>
</div>

<div id="gestion_membres" class="cadre_g">
<h2>Gestion Membres</h2>
<p><a href="index.php?page=newmb" class="menumembres"><img src="templates/default/images/newmb.png" alt="" width="18" height="12" border="0" align="top" /> 
	Nouveau membre</a><br />
	<a href="index.php?page=liste_attente" class="menumembres"><img src="templates/default/images/liste_attente.png" alt="Modifier une fiche Membre" width="18" height="12" border="0" align="top" /> 
	Liste d'attente</a> <br />
	<br />
	<a href="index.php?page=effectif_staffs" class="menumembres"><img src="templates/default/images/fiche.png" alt="Consulter la liste des Staffs" width="18" height="12" border="0" align="top" /> 
	Liste des Staffs simplifi&eacute;e</a><br />
	<a href="index.php?page=effectif_sections" class="menumembres"><img src="templates/default/images/fiche.png" alt="Consulter la liste des Membres" width="18" height="12" border="0" align="top" /> 
	Liste des membres simplifi&eacute;e </a><br />
	<a href="index.php?page=listing_membres" class="menumembres"><img src="templates/default/images/fiche.png" alt="Consulter les listings" width="18" height="12" border="0" align="top" /> 
	Listings  complets</a><br />	<span class="rmqbleu">Consulter</span><br />
	<a href="index.php?page=fichemb" class="menumembres"> <img src="templates/default/images/membre.png" alt="Consulter une fiche Membre" width="18" height="12" border="0" align="top" /> 
	Une fiche Membre</a> <br />
	<a href="index.php?page=fichefamille" class="menumembres"><img src="templates/default/images/famille.png" alt="Consulter une fiche Famille" width="18" height="12" border="0" align="top" /> 
	Une fiche Famille</a><br />
	<span class="rmqbleu">Modifier</span><br />
	<a href="index.php?page=modifmembre" class="menumembres"><img src="templates/default/images/fichemb.png" alt="Modifier une fiche Membre" width="18" height="12" border="0" align="top" /> 
	Une fiche Membre</a><br />
	<a href="index.php?page=modiffamille" class="menumembres"><img src="templates/default/images/fichefamille.png" alt="Modifier une fiche Famille" width="18" height="12" border="0" align="top" /> 
	Une fiche Famille</a><br />
	<br />
	<a href="index.php?page=lastmodif" class="menumembres"><span class="rmqbleu"><img src="templates/default/images/modif_recentes.png" alt="Modifications r&eacute;centes" width="18" height="12" border="0" align="top" /> 
	Modifications r&eacute;centes</span></a><br /> </p>
</div>	  
<div id="gestion_section" class="cadre_d">
<h2>Gestion <?php echo ($user['niveau']['numniveau'] > 3) ? 'des Sections' : 'de ma Section'; ?></h2>
<p><?php
		if ($user['niveau']['numniveau'] > 4)
		{
?>
	<a href="index.php?page=gestion_sections" class="menumembres"><span class="rmqbleu">Les 
	Sections</span></a><br />
	- <a href="index.php?page=gestion_sections&amp;do=creerunite" class="menumembres">Ajouter 
	une Unit&eacute;</a><br />
	- <a href="index.php?page=gestion_sections&amp;do=ajoutersection" class="menumembres">Ajouter 
	une Section</a><br />
            <?php 
		}
		if (count($sections) > 0)
		{
			if ($user['niveau']['numniveau'] > 4)
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
	<a href="index.php?page=gestion_unite" class="menumembres"><span class="rmqbleu">Ma 
	Section</span></a><br />
	- <a href="index.php?page=gestion_sections&amp;do=modifiersection&amp;step=2" class="menumembres">Modifier 
	la Section <?php echo $sections[$user['numsection']]['appellation']; ?></a><br />
            <?php
				if ($sections[$user['numsection']]['sizaines'] > 0)
				{
?>
	- <a href="index.php?page=gestion_sizpat" class="menumembres">Gestion 
	des <?php echo $t_sizaines; ?></a><br />
	<br />
	<span class="rmqbleu">Vie quotidienne</span><br />
	- <a href="index.php?page=gestionsizaines" class="menumembres">Composition 
	des <?php echo $t_sizaines; ?></a><br />
<?php
				}
			}
			else
			{
?>
	- <a href="index.php?page=gestion_sections&amp;do=modifiersection" class="menumembres">Modifier 
	une Section</a><br />
	- <a href="index.php?page=gestion_sections&amp;do=modifierunite" class="menumembres">Modifier 
	une Unit&eacute;</a><br />
	- <a href="index.php?page=gestion_sizpat" class="menumembres">Gestion 
	des <?php echo $t_sizaines; ?></a><br />
            <?php
			}
		} // fin if ($sections)
?></p>
</div>
<div id="outils_generaux" class="cadre_g">
<h2>Outils G&eacute;n&eacute;raux</h2>
<p><a href="index.php?page=passage" class="menumembres">
	<img src="templates/default/images/passage.png" width="18" height="12" alt="Passage" border="0" align="top" /> 
	Passage</a><br />
	<a href="index.php?page=publipostage" class="menumembres"><img src="templates/default/images/mail.png" alt="" width="18" height="12" border="0" align="top" /> 
	Publipostage</a><br />
	<a href="index.php?page=etatcotisations" class="menumembres"><img src="templates/default/images/cotisations.png" alt="" width="18" height="12" border="0" align="top" /> 
	Etat des cotisations</a> 
	<br />
	<br />
	<a href="index.php?page=purge_unite" class="menumembres"><img src="templates/default/images/supprimer_membre.png" alt="" width="18" height="12" border="0" align="top" /> 
	Supprimer des membres</a> 
<?php
		if ($user['niveau']['numniveau'] > 3)
		{
?>
	<br />
	<a href="index.php?page=gestioncotisations" class="menumembres"><img src="templates/default/images/cotisations.png" alt="" width="18" height="12" border="0" align="top" /> 
	Gestion des cotisations</a> 
            <?php
		}
?>
</p>
</div>
<div id="gestion_anciens" class="cadre_d">
<h2>Les Anciens</h2>
<p><a href="index.php?page=listing_anciens" class="menumembres"><img src="templates/default/images/fiche.png" alt="" width="18" height="12" border="0" align="top" /> 
	Listings Anciens complets</a> <br />
	<a href="index.php?page=newancien" class="menumembres"><img src="templates/default/images/newmb.png" alt="" width="18" height="12" border="0" align="top" /> 
	Ajouter un Ancien</a><br />
	<br />
	<a href="index.php?page=ficheancien" class="menumembres"><img src="templates/default/images/membre.png" alt="" width="18" height="12" border="0" align="top" /> 
	Consulter la fiche d'un Ancien</a><br />
	<a href="index.php?page=modifancien" class="menumembres"><img src="templates/default/images/fichemb.png" alt="" width="18" height="12" border="0" align="top" /> 
	Modifier la fiche d'un Ancien</a> <br />
	<a href="index.php?page=passageanciens" class="menumembres"><img src="templates/default/images/passage.png" alt="" width="18" height="12" border="0" align="top" /> 
	Passage Anciens dans une autre Section</a></p>
</div>
<div class="instructions">
<p class="petitbleu">Sur cette page sont rassembl&eacute;s tous les outils de 
  Gestion de l'Unit&eacute; en ligne. <br />
  Chaque encart a une sp&eacute;cialit&eacute; :</p>
<p class="petit"><span class="rmqbleu">Gestion Membres</span><br />
  Ce cadre te permet de g&eacute;rer chaque membre individuellement au sein de 
  la Section.</p>
<p class="petit"><span class="rmqbleu">Gestion <?php echo ($user['niveau']['numniveau'] > 3) ? 'des Sections' : 'de ma Section'; ?></span><br />
  Ce cadre te permet de g&eacute;rer ta Section ou les Unit&eacute;s et les Sections 
  pr&eacute;sentes dans la base de donn&eacute;es.</p>
<p class="petit"><span class="rmqbleu">Outils G&eacute;n&eacute;raux</span><br />
  Ici, tu peux consulter des informations sur la vie quotidienne des Sections. Tu peux aussi g&eacute;n&eacute;rer des 
  listes d'adresses pour envoyer tes courriers gr&acirc;ce &agrave; la fonction 
  publipostage.</p>
<p class="petit"><span class="rmqbleu">Les Anciens</span><br />
  Ce cadre te permet de g&eacute;rer les Anciens de toutes les Unit&eacute;s g&eacute;r&eacute;es 
  dans la base de donn&eacute;es. Tu peux les ajouter, modifier leurs informations, 
  consulter les listings des Anciens et les r&eacute;cup&eacute;rer s'ils reviennent 
  dans l'Unit&eacute;.</p>
</div>  
</div>
<?php
		if (!defined('IN_SITE'))
		{
?>
</body>
</html>
<?php
		}
	}
}
?>