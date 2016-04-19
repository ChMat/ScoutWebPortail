<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* pagesrestreintes.php v 1.1 - Consultation des pages restreintes du portail
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
*	Ajout redirection vers création d'une unité après l'installation
*	Optimisation xhtml
*	Suppression des commentaires sur le forum des staffs
*/

include_once('connex.php');
include_once('fonc.php');

if (!is_array($sections))
{ // les sections n'existent pas encore, il faut les créer d'abord
	include('gestion_sections.php');
}
else if ($user['niveau']['numniveau'] > 2)
{ // Les pages ne sont ouvertes qu'aux animateurs
	$pg = (!is_numeric($_GET['pg'])) ? 0 : $_GET['pg'];
	$par = (!is_numeric($_GET['par'])) ? 20 : $_GET['par'];

?>
<h1 class="grandthstaffs">Pages &agrave; diffusion restreinte</h1>
  <?php
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Pages &agrave; acc&egrave;s restreint</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
	
	function menu()
	{
	global $par, $pg, $user;
?>
<table width="100%" class="cadrenoir pg_restreinte_menu">
  <tr>
    <td><a href="index.php?page=pagesrestreintes&amp;pg=0&amp;par=<?php echo $par; ?>" class="lien">Liste des pages 
      restreintes </a> 
<?php
		$nbreart = nbrearticles();
		$nbrepages = ceil($nbreart/$par); // On calcule le nombre de pages
		$pg = ($pg > $nbrepages) ? $nbrepages - 1 : $pg; // On corrige une page erronée
		if ($nbrepages > 1) 
		{
			echo ' | Page ';
			if (($pg <= 4 and $nbrepages <= 5) or ($pg < 4 and $nbrepages > 5))
			{
				$max = ($nbrepages >= 5) ? 5 : $nbrepages;
				for ($i = 0; $i < $max; $i++)
				{
					echo '<a href="index.php?page=pagesrestreintes&amp;pg='.$i.'&amp;par='.$par.'" class="lien">';
					echo ($pg == $i) ? '<font class="rmq">' : '';
					echo $i;
					echo ($pg == $i) ? '</font>' : '';
					echo '</a>';
					echo ($i < $max - 1) ? '-' : '';
				}
			}
			else if ($nbrepages == 1)
			{
				echo '<a href="index.php?page=pagesrestreintes&amp;pg=0&amp;par='.$par.'" class="lien"><font class="rmq">0</font></a>';
			}
			else
			{
				if ($pg == $nbrepages - 1) {$max = $pg;}
				else if ($nbrepages == $pg + 2) {$max = $pg + 1;}
				else {$max = $pg + 2;}
				for ($j = ($pg - 2); $j < ($max + 1); $j++)
				{
					if ($j >= 0)
					{
						echo '<a href="index.php?page=pagesrestreintes&amp;pg='.$j.'&amp;par='.$par.'" class="lien">';
						echo ($pg == $j) ? '<font class="rmq">' : '';
						echo $j;
						echo ($pg == $j) ? '</font>' : '';
						echo '</a>';
						echo ($j < $max) ? '-' : '';
					}
				}
			}
		}
?>
      | <strong><a href="index.php?page=pagesrestreintes&amp;do=ecrire" class="lien">R&eacute;diger une page</a> </strong>
      | <a href="index.php?page=pagesrestreintes&amp;do=search" class="lien">Chercher</a></td>
  </tr>
</table>
<?php
	} // fin fonction menu()
	
	function affarticle($num)
	{ // On affiche l'article demandé ou le dernier rédigé
		global $user, $db, $mois;
		if ($num > 0)
		{
			$sql = "SELECT a.numpage as idart, pseudo as auteur, auteur as numauteur, titre, article, 
			date_format(datecreation, '%e') jour, date_format(datecreation, '%c') mois, date_format(datecreation, '%Y') annee, lu 
			FROM 
			".PREFIXE_TABLES."pagesrestreintes as a, 
			".PREFIXE_TABLES."auteurs as b 
			WHERE 
			a.auteur = b.num 
			AND a.numpage = '$num' 
			AND pagebannie != '1'";
		}
		else
		{
			$sql = "SELECT a.numpage as idart, pseudo as auteur, auteur as numauteur, titre, article, 
			date_format(datecreation, '%e') jour, date_format(datecreation, '%c') mois, date_format(datecreation, '%Y') annee, lu 
			FROM 
			".PREFIXE_TABLES."pagesrestreintes as a, 
			".PREFIXE_TABLES."auteurs as b 
			WHERE 
			a.auteur = b.num 
			AND pagebannie != '1' 
			ORDER BY datecreation DESC LIMIT 1";
		}
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) > 0)
			{
				$art = mysql_fetch_assoc($res);
				$lectures = ($art['lu'] != 0) ? ' <span class="petit">(lue '.$art['lu'].' fois)</span>' : '';
?>
<div class="page_restreinte">
<h2><?php echo $art['titre']; ?></h2>
<p class="petitbleu" align="right"><?php echo $lectures; ?></p>
<div class="pg_restreinte_texte"><?php
				echo makehtml($art['article']);
?></div>
<p align="right"><span class="petitbleu"><?php echo $art['auteur'].' - le '.$art['jour'].' '.$mois[$art['mois']].' '.$art['annee']; ?></span>
- <span class="petit"><a href="index.php?page=pagesrestreintes&amp;do=auteur&amp;auteur=<?php echo $art['numauteur']; ?>" class="lien">Du m&ecirc;me auteur</a> 
<?php
				if ($user['num'] == $art['numauteur'] or $user['niveau']['numniveau'] == 5)
				{
?> | <a href="index.php?page=pagesrestreintes&amp;do=confirm&amp;article=<?php echo $art['idart']; ?>" class="lien">Supprimer</a> 
 | <a href="index.php?page=pagesrestreintes&amp;do=modif&amp;numpage=<?php echo $art['idart']; ?>" class="lien">Modifier</a>
<?php
				}
?>
</span></p>
</div>
<?php
				$numeroarticle = $art['idart'];
				$sql = "UPDATE ".PREFIXE_TABLES."pagesrestreintes SET lu = lu + 1 WHERE numpage = '$numeroarticle'";
				send_sql($db, $sql);
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Aucune page ne correspond &agrave; cette requ&ecirc;te !</p>
</div>
<?php
			}
		}
	}
	
	function showarticles($debut, $nombre)
	{ // On affiche la liste des pages restreintes
		global $db;
		$sql = "SELECT a.numpage, titre, datecreation, lu, pseudo, auteur 
		FROM 
		".PREFIXE_TABLES."pagesrestreintes as a, 
		".PREFIXE_TABLES."auteurs as b 
		WHERE 
		a.auteur = b.num 
		AND pagebannie != '1' 
		ORDER BY datecreation DESC";
		if ($res = send_sql($db, $sql))
		{
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
?>
<table border="0" cellspacing="0" cellpadding="2" width="100%" align="center">
  <tr>
	<th></th>
	<th></th>
	<th class="petit">Auteur</th>
	<th class="petit">Lectures</th>
	<th class="petit">Cr&eacute;ation le</th>
  </tr>
<?php
				for ($j = 1; $j <= $num; $j++)
				{
					$ligne = mysql_fetch_assoc($res);
					if ($j > $debut and $j <= $debut + $nombre)
					{
						$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
						$classelientitre = ($j == 1) ? 'lienmort rmqbleu' : 'lienmort';
?>
  <tr class="<?php echo $couleur; ?>">
<td width="20"><img src="templates/default/images/fiche.png" width="18" height="12" alt="" /></td>
<td><a href="index.php?page=pagesrestreintes&amp;numero=<?php echo $ligne['numpage']; ?>" class="<?php echo $classelientitre; ?>"><?php echo $ligne['titre']; ?></a></td>
<td align="center"><?php echo $ligne['pseudo']; ?></td>
<td align="center"><?php echo $ligne['lu']; ?></td>
<td align="right"><?php echo date_ymd_dmy($ligne['datecreation'], 'enchiffres'); ?></td>
</tr>
<?php
					}
				}
?>
</table>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucune page restreinte.</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p class="rmq">Une erreur s'est produite !</p>
</div>
<?php
		}
	}

	function dumemeauteur($auteur)
	{
		global $db;
		$ressource = untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $auteur);
?>
<h2>Toutes les pages restreintes r&eacute;dig&eacute;es par <?php echo $ressource; ?></h2>
<?php
		$sql = "SELECT a.numpage, titre, datecreation, lu, pseudo, auteur 
		FROM 
		".PREFIXE_TABLES."pagesrestreintes as a, 
		".PREFIXE_TABLES."auteurs as b 
		WHERE 
		a.auteur = '$auteur' 
		AND a.auteur = b.num 
		AND pagebannie != '1' 
		ORDER BY datecreation DESC";
		if ($res = send_sql($db, $sql))
		{
?>
<table border="0" cellspacing="0" width="100%" align="center">
<?php
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$j = 1;
				while ($ligne = mysql_fetch_assoc($res))
				{
					$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
					$j++;
					$lectures = '';
					if ($ligne['lu'] != 0) {$lectures = 'lu '.$ligne['lu'].' fois';}
?>
<tr class="<?php echo $couleur; ?>">
<td width="20"><img src="templates/default/images/fiche.png" width="18" height="12" alt="" /></td>
<td width="65%"><a href="index.php?page=pagesrestreintes&amp;numero=<?php echo $ligne['numpage']; ?>"><?php echo $ligne['titre']; ?></a></td>
<td width="10%"><?php echo $ligne['pseudo']; ?></td>
<td align="right" width="20%"><?php echo date_ymd_dmy($ligne['datecreation'], 'enchiffres'); ?></td>
</tr>
<?php
				}
			}
			else
			{
?>
	<tr><td colspan="4" align="center">Il n'y a aucune page restreinte.</td></tr>
<?php
			}
?>
</table>
<?php
		}
		else
		{
?>
<div class="msg">
  <p class="rmq" align="center">Une erreur s'est produite !</p>
</div>
<?php
		}
	}

	function affrecherche($search)
	{
		global $db;
		$search = htmlentities(stripslashes($search), ENT_QUOTES);
		$sql = "SELECT a.numpage, titre, article, datecreation, auteur, pseudo 
		FROM 
		".PREFIXE_TABLES."pagesrestreintes as a, 
		".PREFIXE_TABLES."auteurs as b 
		WHERE 
		a.auteur = b.num 
		AND (article LIKE '%$search%' 
			OR titre LIKE '%$search%') 
		AND pagebannie != '1' 
		ORDER BY datecreation DESC";
		if ($res = send_sql($db, $sql))
		{
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$pl = ($num > 1) ? 's' : '';
?>
<div class="msg">
  <p class="rmqbleu">R&eacute;sultat de la recherche : <?php echo $num.' page'.$pl.' restreinte'.$pl.' contenant <q>'.$search.'</q>'; ?></p>
</div>
<table border="0" cellspacing="0" width="100%" align="center">
<?php
				if ($num > 100) {$num = 100; $plus_de_cent = '1';} // On limite à 100 le nombre de résultats
				for ($j = 1; $j<=$num; $j++)
				{
					if ($ligne = mysql_fetch_assoc($res))
					{
						$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
<tr class="<?php echo $couleur; ?>">
  <td width="20"><img src="templates/default/images/fiche.png" alt="" /></td>
  <td><a href="index.php?page=pagesrestreintes&amp;numero=<?php echo $ligne['numpage']; ?>" title="Posté le <?php echo date_ymd_dmy($ligne['datecreation'], 'dateheure'); ?>"><?php echo $ligne['titre']; ?></a></td>
  <td><?php echo $ligne['pseudo']; ?></td>
  </tr>
<?php
					}
				}
?>
</table>
<?php
				if ($plus_de_cent == '1')
				{ // Il y a plus de 100 résultats, on affiche les 100 premiers
?>
<div class="msg">
  <p align="right">Seuls les 100 premiers résultats sont affichés.</p>
</div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
  <p align="center" class="rmq">Aucune page ne contient <q><?php echo $search; ?></q>'.</p>
  <p align="center"><a href="index.php?page=pagesrestreintes">Retour &agrave; la liste des pages restreintes</a></p>
</div>
<?php
			}
		}
		else
		{ // Problème dans la requête
?>
<div class="msg">
  <p align="center" class="rmq">Une erreur s'est produite !</p>
</div>
<?php
		}
	}

	function nbrearticles()
	{ // On compte le nombre de pages existantes
		global $db;
		$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."pagesrestreintes WHERE pagebannie != '1'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$ligne = mysql_fetch_assoc($res);
			return $ligne['nbre'];
		}
		else
		{
			return 0;
		}
	}

/*** Début affichage de la page
************************************************/

	// affichage du menu
	menu();

	if (!isset($_GET['do']) and !isset($_POST['do']))
	{
		$debut = ($pg * $par);
		if (is_numeric($_GET['numero']))
		{
			affarticle($_GET['numero']);
		}
		else
		{
			showarticles($debut, $par);
?>
<div class="instructions">
<h2>C'est quoi ce binz ?</h2>
<p>Les pages restreintes, comme le nom le dit si bien, sont des pages que tout 
  le monde ne voit pas. Dans ce cas-ci, seuls les animateurs ont acc&egrave;s 
  &agrave; ces pages.</p>
<h2>A quoi &ccedil;a sert ?</h2>
<p>Les pages restreintes sont l'endroit id&eacute;al pour stocker les <em>comptes-rendus 
  de r&eacute;unions</em>, des <em>fiches de pr&eacute;paration</em> pour des 
  projets ou tout autre <em>texte qui n'ouvre pas vraiment &agrave; discussion</em> 
  (pour cela, le forum est tr&egrave;s pratique). 
  Etant donn&eacute; que ces fiches ont un int&eacute;r&ecirc;t pour d'autres 
  organisations similaires ou juste pour m&eacute;moire, il est pratique de les 
  centraliser ici; elles seront faciles &agrave; retrouver gr&acirc;ce au moteur 
  de recherche.</p>
<?php
			if (ENVOI_MAILS_ACTIF)
			{
?>
<h2>Pr&eacute;venir les animateurs</h2>
<p>Si tu le souhaites, tu peux automatiquement avertir les animateurs 
  de ton ajout (un mail leur est envoy&eacute;).</p>
<?php
			}
?>
</div>
  <?php
		}
	}
	else if ($_GET['do'] == 'ecrire' or $_GET['do'] == 'modif')
	{
		$quoi = 'article';
		include('pagesrestreintespost.php');
	}
	else if ($_GET['do'] == 'auteur' and is_numeric($_GET['auteur']))
	{
		dumemeauteur($_GET['auteur']);
	}
	else if (($_GET['do'] == 'search' and empty($_GET['search'])) or ($_POST['do'] == 'search' and !empty($_POST['search'])))
	{
?>
<form action="index.php" method="post" name="form1" id="form1" class="form_config_site">
<h2>Rechercher une page restreinte</h2>
<p>Pour rechercher une page ou un th&egrave;me particulier, entre un 
            mot-cl&eacute; ci-dessous et lance la recherche.</p>
<p align="center"><input type="hidden" name="page" value="pagesrestreintes" />
<input type="text" name="search" maxlength="30" size="20" />
<input type="submit" name="act" value="Chercher" />
<input type="hidden" name="do" value="search" /></p>
</form>
<?php
		// affichage du résultat de la recherche d'un message
		if ($_POST['do'] == 'search' and !empty($_POST['search']))
		{
			affrecherche($_POST['search']);	
		}
	} // affichage du résultat de la recherche d'un message
	else if ($_GET['do'] == 'confirm')
	{
?>
<h1>Suppression d'une page</h1>
<div class="action">
<p align="center" class="rmq">Es-tu certain de vouloir supprimer cet article ?</p>
<p align="center"><a href="index.php?page=pagesrestreintes&amp;do=moderate&amp;article=<?php echo $_GET['article']; ?>" class="lien"><b>OUI</b></a> - <a href="index.php?page=pagesrestreintes&numero=<?php echo $_GET['article']; ?>" class="lien"><b>NON</b></a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'moderate')
	{
		if (is_numeric($_GET['article']))
		{
?>
<h1>Suppression d'une page</h1>
<?php
			$sql = "SELECT auteur FROM ".PREFIXE_TABLES."pagesrestreintes WHERE numpage = '".$_GET['article']."'";
			if ($res = send_sql($db, $sql))
			{
				if (mysql_num_rows($res) == 1)
				{
					$art = mysql_fetch_assoc($res);
					if ($user['niveau']['numniveau'] == 5 or $user['num'] == $art['auteur'])
					{
						// On marque la page restreinte comme bannie (la suppression totale de la db n'est pas encore implémentée)
						$sql = "UPDATE ".PREFIXE_TABLES."pagesrestreintes SET pagebannie = '1' WHERE numpage = '".$_GET['article']."'";
						send_sql($db, $sql);
						log_this('Modération page restreinte ('.$_GET['article'].')', 'pagesrestreintes');
?>
<div class="msg">
<p align="center" class="rmq">La page a &eacute;t&eacute; supprim&eacute;e</p>
</div>
<?php
					}
					else
					{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas les droits suffisants pour supprimer cette page !</p>
</div>
<?php
					}
				}
			}
		}
	}

	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
}
else
{
	include('404.php');
}
?>