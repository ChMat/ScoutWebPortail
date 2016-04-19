<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* news.php v 1.1 - Affichage des news du portail
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
*	Modification noms de champs (+ titre des news)
*/

if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Les Nouvelles de l'Unité</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
}
?>
<div id="news">
<h1>Les derni&egrave;res News de l'Unit&eacute;</h1>
<div class="panneau">
<h2>Options</h2>
  <p>
<?php
if ($user['niveau']['numniveau'] > 2)
{
?>  - <a href="index.php?page=gestion_news">Ajouter des news sur le portail</a><br />
<?php
}
?>
	- <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'rssnews.htm' : 'index.php?page=rssnews'; ?>" title="Les News du site sur ton ordinateur ou ton site web !">Les news en <img src="img/rss/rss.gif" alt="RSS" width="27" height="15" border="0" /></a></p>
</div>
<?php
$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."news WHERE news_bannie != '1'";
if ($res = send_sql($db, $sql))
{
	$nbre_news = 0;
	if (mysql_num_rows($res) != 0)
	{
		$ligne = mysql_fetch_assoc($res);
		$nbre_news = $ligne['nbre'];
	}
	$par = 5;
	if ($nbre_news > $par)
	{
		$nbre_pages_news = round($nbre_news / $par);
		if ($nbre_pages_news * $par < $nbre_news) $nbre_pages_news++;
	}
	else
	{
		$nbre_pages_news = 1;
	}
}
$pg = $_GET['pg'];
if (isset($pg) and $pg < 1) {$pg = 1;} else if (isset($pg) and $pg > $nbre_pages_news) {$pg = $nbre_pages_news;}
if (!isset($pg)) {$debut = 0; $pg= 1;} // page en cours
else {$debut = $par * ($pg-1);}
$sql = "SELECT a.pseudo, a.num, id_news, titre_news, texte_news, datecreation FROM ".PREFIXE_TABLES."news, ".PREFIXE_TABLES."auteurs as a WHERE news_bannie != '1' AND auteur_news = num ORDER BY datecreation DESC LIMIT $debut, $par";
if ($res = send_sql($db, $sql))
{
	if (mysql_num_rows($res) > 0)
	{
		if ($nbre_pages_news > 1)
		{
?>
<p class="pagination">
<?php
			if ($pg > 1)
			{
				$pgpcdte = $pg - 1;
				$lien_news_pcdte = ($site['url_rewriting_actif'] == 1) ? 'news_'.$pgpcdte.'.htm' : 'index.php?page=news&amp;pg='.$pgpcdte;
?><a href="<?php echo $lien_news_pcdte; ?>" class="pg_pcdte">News plus r&eacute;centes</a><?php
			}
?>
 <span class="pg">Page <?php echo $pg.' de '.$nbre_pages_news; ?></span>
<?php
			if ($pg < $nbre_pages_news)
			{
				$pgsvte = $pg + 1;
				$lien_news_svte = ($site['url_rewriting_actif'] == 1) ? 'news_'.$pgsvte.'.htm' : 'index.php?page=news&amp;pg='.$pgsvte;
?><a href="<?php echo $lien_news_svte; ?>" class="pg_svte">News plus anciennes</a><?php
			}
?>
</p>
<?php
		}
		while ($ligne = mysql_fetch_assoc($res))
		{
			$datecreation = date_ymd_dmy($ligne['datecreation'], 'enlettres');
			$datecreation_news = date_ymd_dmy($ligne['datecreation'], 'jourmois');
?>
<div class="news_msg">
<h2><?php echo (!empty($ligne['titre_news'])) ? $ligne['titre_news'] : 'Actu du '.$datecreation_news; ?></h2>
<p class="news_txt"><?php echo makehtml($ligne['texte_news']); ?></p>
<p class="news_infos">Post&eacute;e le <?php echo $datecreation; ?> par 
<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$ligne['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$ligne['num']; ?>" class="lienmort"><?php echo $ligne['pseudo']; ?></a> 
<?php
			if ($user['niveau']['numniveau'] > 2)
			{
?>
<a href="index.php?page=gestion_news&amp;do=modif&amp;id_news=<?php echo $ligne['id_news']; ?>" title="Editer cette News"><img src="templates/default/images/fiche.png" width="18" height="12" alt="Editer cette News" border="0" align="middle" /></a> 
<a href="index.php?page=gestion_news&amp;do=suppr&amp;id_news=<?php echo $ligne['id_news']; ?>" title="Supprimer cette News"><img src="templates/default/images/moins.png" width="12" height="12" alt="Supprimer cette News" border="0" align="middle" /></a> 
<?php
			}
?>
</p>
</div>
<?php
		}
	}
	else
	{
?>
<div class="msg">
<p align="center">Pas de nouvelles, bonne nouvelle !</p>
</div>
<?php
	}
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
?>