<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* tally.php v 1.1 - Tally des membres du portail
* Fichier lié à tallypost.php
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
<title>Les Articles des membres</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body bgcolor="#FFFFFF">
<?php
}

function menu()
{
	global $par, $pg, $user, $site;
?>
<table width="100%" class="cadrenoir">
  <tr>
    <td><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally'; ?>" class="lien">Index du Tally</a> 
      <?php
	$nbreart = nbrearticles();
	$nbrepages = round($nbreart/$par);
	if ($nbrepages * $par < $nbreart) {$nbrepages++;}
	if ($pg > $nbrepages) {$pg = $nbrepages - 1;}
	if ($nbrepages > 1) 
	{
	  echo ' | Page ';
	  if (($pg <= 4 and $nbrepages <= 5) or ($pg < 4 and $nbrepages > 5))
	  {
		if ($nbrepages >= 5) {$max = 5;} else {$max = $nbrepages;}
		for ($i = 0; $i < $max; $i++)
		{
			$lien_pg_tally = ($site['url_rewriting_actif'] == 1) ? 'tally_'.$i.'.htm' : 'index.php?page=tally&amp;pg='.$i;
			echo '<a href="'.$lien_pg_tally.'" class="lien">';
			if ($pg == $i) {echo '<span class="rmq">';}
			echo $i;
			if ($pg == $i) {echo '</span>';}
			echo '</a>';
			if ($i < $max - 1) {echo '-';}
		}
	  }
	  else if ($nbrepages == 1)
	  {
	  	$lien_pg_tally = ($site['url_rewriting_actif'] == 1) ? 'tally_0.htm' : 'index.php?page=tally&amp;pg=0';
		echo '<a href="'.$lien_pg_tally.'" class="lien"><span class="rmq">0</span></a>';
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
				$lien_pg_tally = ($site['url_rewriting_actif'] == 1) ? 'tally_'.$j.'.htm' : 'index.php?page=tally&amp;pg='.$j;
				echo '<a href="'.$lien_pg_tally.'" class="lien">';
				if ($pg == $j) {echo '<span class="rmq">';}
				echo $j;
				if ($pg == $j) {echo '</span>';}
				echo '</a>';
				if ($j < $max) {echo '-';}
			}
		}
	  }
	}
	if ($user > 0)
	{
?>
      | <strong><a href="index.php?page=tally&amp;do=ecrire" class="lien">R&eacute;diger un article</a> </strong>
<?php
	}
?>
      | <a href="index.php?page=tally&amp;do=search" class="lien">Chercher</a></td>
  </tr></table>
<?php
} // fin fonction menu()

function affarticle($num, $from)
{
	global $db, $user, $mois, $site;
	if (!empty($num))
	{
		$sql = "SELECT a.numarticle as idart, pseudo as auteur, article_auteur as numauteur, article_titre, article_texte, date_format(article_datecreation, '%e') jour, date_format(article_datecreation, '%c') mois, date_format(article_datecreation, '%Y') annee, article_lu FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = b.num AND a.numarticle = '$num' AND article_banni != '1'";
	}
	else
	{
		$sql = "SELECT a.numarticle as idart, pseudo as auteur, article_auteur as numauteur, article_titre, article_texte, date_format(article_datecreation, '%e') jour, date_format(article_datecreation, '%c') mois, date_format(article_datecreation, '%Y') annee, article_lu FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = b.num AND article_banni != '1' ORDER BY article_datecreation DESC LIMIT 1";
	}
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) != 0)
		{
			$art = mysql_fetch_assoc($res);
			$lectures = '';
			if ($art['article_lu'] != 0) {$lectures = ' <span class="petit">(lu '.$art['article_lu'].' fois)</span>';}
?>
<h1><?php echo $art['article_titre']; ?></h1>
<?php
			menu();
?>
<p class="petitbleu" align="right"><?php echo $lectures; ?></p>
<div class="tally_texte"><?php echo makehtml($art['article_texte'], 'ibbcode'); ?></div>
<p align="right"><span class="petitbleu"><?php echo $art['auteur'].' - le '.$art['jour'].' '.$mois[$art['mois']].' '.$art['annee']; ?></span>
- <span class="petit"><a href="index.php?page=tally&amp;do=auteur&amp;auteur=<?php echo $art['numauteur']; ?>" class="lien">Du m&ecirc;me auteur</a> 
<?php
			if (($user['num'] == $art['numauteur'] and $user['niveau']['numniveau'] > 0) or $user['niveau']['numniveau'] > 2)
			{
?>
 | <a href="index.php?page=tally&amp;do=confirm&amp;article=<?php echo $art['idart']; ?>" class="lien">Supprimer</a> 
 | <a href="index.php?page=tally&amp;do=modif&amp;numpage=<?php echo $art['idart']; ?>" class="lien">Modifier</a>
<?php
			}
?>
</span></p>
<?php
			$numeroarticle = $art['idart'];
			$sql = "UPDATE ".PREFIXE_TABLES."articles SET article_lu = article_lu + 1 WHERE numarticle = '$numeroarticle'";
			send_sql($db, $sql);
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucun article ne correspond &agrave; cette requ&ecirc;te !</p>
</div>
<?php
		}
	}
}

function showarticles($debut, $nombre)
{
	global $user, $db, $site;
	$sql = "SELECT a.numarticle, article_titre, article_datecreation, article_lu, pseudo, article_auteur FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = b.num AND article_banni != '1' ORDER BY article_datecreation DESC";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
?>
<table border="0" cellspacing="0" cellpadding="2" width="100%" align="center">
  <tr>
	<th colspan="2">Titre</th>
	<th class="petit">Auteur</th>
	<th class="petit">Lectures</th>
	<th class="petit">Cr&eacute;ation le</th>
  </tr>
<?php
			for ($j = 1; $j<=$num; $j++)
			{
				if ($ligne = mysql_fetch_assoc($res))
				{
					if ($j > $debut and $j <= $debut + $nombre)
					{
						$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
						$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$ligne['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$ligne['numarticle'];
?>
  <tr class="<?php echo $couleur; ?>">
	<td width="20"><img src="templates/default/images/fiche.png" width="18" height="12" alt="" /></td>
	<td><a href="<?php echo $lien_article_tally; ?>"><?php echo $ligne['article_titre']; ?></a></td>
	<td align="center"><?php echo $ligne['pseudo']; ?></td>
	<td align="center"><?php echo $ligne['article_lu']; ?></td>
	<td align="right"><?php echo date_ymd_dmy($ligne['article_datecreation'], 'enchiffres'); ?></td>
  </tr>
<?php
					}
				}
			}
?>
</table>
<?php
		}
		else
		{
?>
<div class="action">
<p align="center">Le Tally est vide... Viens vite le remplir !</p>
<?php
			if (!$user)
			{
				$lien_inscription = ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr';
?>
<p align="center"><a href="index.php?page=login">Connecte-toi</a> ou <a href="<?php echo $lien_inscription; ?>">deviens membre</a></p>
<?php
			}
?>
</div>
<?php
		}
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

function dumemeauteur($auteur)
{
	global $db, $user, $site;
	$ressource = untruc(PREFIXE_TABLES."auteurs", 'pseudo', 'num', $auteur);
?>
<h2>Le Tally de <?php echo $ressource; ?></h2>
<?php
	$sql = "SELECT a.numarticle, article_titre, article_datecreation, article_lu, pseudo, article_auteur FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = '$auteur' AND a.article_auteur = b.num AND article_banni != '1' ORDER BY article_datecreation DESC";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$j = 1;
?>
<table border="0" cellspacing="0" cellpadding="2" width="100%" align="center">
  <tr>
	<th colspan="2">Titre</th>
	<th class="petit">Cr&eacute;ation le</th>
  </tr>
<?php
			while ($ligne = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
				$j++;
				$lectures = '';
				if ($ligne['article_lu'] != 0) {$lectures = 'lu '.$ligne['article_lu'].' fois';}
				$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$ligne['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$ligne['numarticle'];
?>
  <tr class="<?php echo $couleur; ?>">
	<td width="20"><img src="templates/default/images/fiche.png" width="18" height="12" alt="" /></td>
	<td><a href="<?php echo $lien_article_tally; ?>"><?php echo $ligne['article_titre']; ?></a></td>
	<td align="right"><?php echo date_ymd_dmy($ligne['article_datecreation'], 'enchiffres'); ?></td>
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
<div class="msg">
<p class="rmq" align="center">Aucun article trouv&eacute;.</p>
</div>
<?php
		}
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
	global $db, $site;
	$search = htmlentities($search, ENT_QUOTES);
	$sql = "SELECT a.numarticle, article_titre, article_texte, article_datecreation, article_auteur, pseudo FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE a.article_auteur = b.num AND (article_texte LIKE '%$search%' OR article_titre LIKE '%$search%') AND article_banni != '1' ORDER BY article_datecreation DESC";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$pl = ($num > 1) ? 's' : '';
?>
<h2>R&eacute;sultat de la recherche : <?php echo $num.' article'.$pl.' trouv&eacute;'.$pl; ?></h2>
<table border="0" cellspacing="0" width="100%" align="center">
  <tr>
	<th colspan="2">Titre</th>
	<th class="petit">Auteur</th>
  </tr>
<?php
			if ($num > 100) {$num = 100; $att = '1';}
			for ($j = 1; $j <= $num; $j++)
			{
				if ($ligne = mysql_fetch_assoc($res))
				{
					$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';					
					$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally_'.$ligne['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$ligne['numarticle'];
?>
  <tr class="<?php echo $couleur; ?>">
	<td width="20"><img src="templates/default/images/fiche.png" alt="" /></td>
	<td><a href="<?php echo $lien_article_tally; ?>" title="Posté le <?php echo date_ymd_dmy($ligne['article_datecreation'], 'dateheure'); ?>"><?php echo $ligne['article_titre']; ?></a></td>
	<td><?php echo $ligne['pseudo']; ?></td>
  </tr>
<?php
				}
			}
?>
</table>
<?php
			if ($att == '1')
			{
?>
<div class="msg">
<p align="center">Seuls les 100 premiers résultats sont affichés.</p>
</div>
<?php
			}
		}
		else
		{
			$lien_tally = ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally';
?>
<div class="msg">
<p align="center" class="rmq">Aucun article ne contient &quot;<?php echo stripslashes(cleanvar($search)); ?>&quot;.</p>
<p align="center"><a href="<?php echo $lien_tally; ?>" tabindex="1">Retour au Tally</a></p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
</div>
<?php
	}
}

function nbrearticles()
{
	global $db;
	$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."articles WHERE article_banni != '1'";
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

$pg = (!is_numeric($_GET['pg'])) ? 0 : $_GET['pg'];
$par = (!is_numeric($_GET['par'])) ? 20 : $_GET['par'];

// affichage du menu
if (!is_numeric($_GET['numero']))
{
?>
<h1>Tally &quot;Intersections&quot;</h1>
<br />
<?php
	menu();
}
if (!isset($_GET['do']) and !isset($_POST['do']))
{
	$debut = ($pg * $par);
	if (is_numeric($_GET['numero']))
	{
		affarticle($_GET['numero'], '');
	}
	else
	{
		showarticles($debut, $par);
?>
<div class="instructions">
<h2>Un Tally, kesako ?</h2>
<p>Le Scoutisme, c'est une suite sans fin de bons moments. Il est dommage 
de les oublier. Le Tally, c'est un peu comme un journal, un carnet de 
bord. Tu peux y &eacute;crire ce que tu veux, la derni&egrave;re anecdote 
qui s'est pass&eacute;e &agrave; une r&eacute;union, le r&eacute;cit complet 
d'un camp, des tuyaux ou tout ce qui te passe par la t&ecirc;te. La seule 
limite que nous y mettons, c'est ton imagination...</p>
</div>
<?php
	}
}
else if ($_GET['do'] == 'ecrire' or $_GET['do'] == 'modif')
{
	$quoi = 'article';
	include('tallypost.php');
}
else if ($_GET['do'] == 'auteur')
{
	dumemeauteur($_GET['auteur']);
}
else if (($_GET['do'] == 'search' and empty($_GET['search'])) or ($_POST['do'] == 'search' and !empty($_POST['search'])))
{
?>
<form action="index.php" method="post" name="form1" id="form1" class="form_config_site">
<h2>Recherche sur le tally</h2>		  	
<p>Pour rechercher une article ou un th&egrave;me particulier, entre un 
mot-cl&eacute; ci-dessous et lance la recherche.</p>
<input type="hidden" name="page" value="tally" />
<input type="hidden" name="do" value="search" />
<p align="center">Rechercher : 
  <input name="search" type="text" tabindex="1" value="<?php echo stripslashes(cleanvar($_POST['search'])); ?>" size="20" maxlength="30" />
  <input name="act" type="submit" tabindex="2" value="Chercher" />
</p>			
</form>
<?php
	// affichage du résultat de la recherche d'un message
	if ($_POST['do'] == 'search' and !empty($_POST['search']))
	{
		affrecherche(cleanvar($_POST['search']));
	}
} 
else if ($_GET['do'] == 'confirm')
{
	$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$_GET['article'].'.htm' : 'index.php?page=tally&amp;numero='.$_GET['article'];
?>
<h1>Tally &quot;Intersections&quot; </h1>
<div class="action">
<h2>Suppression d'un article</h2>
<p align="center" class="rmqbleu">Es-tu certain de vouloir supprimer cet article ?</p>
<p align="center"><a href="index.php?page=tally&amp;do=moderate&amp;article=<?php echo $_GET['article']; ?>" class="bouton" tabindex="1">OUI</a> <a href="<?php echo $lien_article_tally; ?>" class="bouton" tabindex="2">NON</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'moderate')
{
	if (is_numeric($_GET['article']))
	{
?>
<h1>Tally &quot;Intersections&quot; </h1>
<?php
		$sql = "SELECT article_auteur FROM ".PREFIXE_TABLES."articles WHERE numarticle = '$_GET[article]'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$art = mysql_fetch_assoc($res);
				$lien_tally = ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally';
				if ($user['niveau']['numniveau'] > 2 or ($user['num'] == $art['article_auteur'] and $user['niveau']['numniveau'] > 0))
				{
					$sql = "UPDATE ".PREFIXE_TABLES."articles SET article_banni = '1' WHERE numarticle = '$_GET[article]'";
					send_sql($db, $sql);
					log_this('Modération article Tally ('.$_GET['article'].')', 'tally');
?>
<div class="msg">
<p align="center" class="rmqbleu">L'article a &eacute;t&eacute; supprim&eacute;</p>
<p align="center"><a href="<?php echo $lien_tally; ?>" tabindex="1">Retour au Tally</a></p>
</div>
<?php
				}
				else
				{
					$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$_GET['article'].'.htm' : 'index.php?page=tally&amp;numero='.$_GET['article'];
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas les droits suffisants pour supprimer cet article !</p>
<p align="center"><a href="<?php echo $lien_article_tally; ?>" tabindex="1">Retour au Tally</a></p>
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
?>