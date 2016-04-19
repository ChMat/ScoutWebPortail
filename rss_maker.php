<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* rss_maker.php v 1.1.1 - Ce script génère le flux RSS des news du portail et les place dans news.xml
* Inspiré d'un script publié sur http://www.asp-php.net/scripts/asp.net/generationrss.php?page=2
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
*	Ajout d'un Time to live au fil RSS (par défaut à 1440 minutes soit 24 heures)
*	Correction bug d'affichage des accents dans la date d'un item
*	Nouveaux noms de champs (+ titre des news)
* Modifications v 1.1.1
*	bug 07/11 : Correction codage du titre des news
*	Ajout de la date au titre des news (plus simple sous Firefox)
*/

include_once('connex.php');
include_once('fonc.php');

// production de l'en-tête du fil RSS
// ttl (TimeToLive) : plus d'infos sur http://blogs.law.harvard.edu/tech/rss#ltttlgtSubelementOfLtchannelgt
$xml = '<'.'?xml version="1.0" encoding="ISO-8859-1"?'.'>
<rss version="2.0">
   <channel>
      <title>News '.$site['titre_site'].'</title>
      <link>'.$site['adressesite'].'</link>
      <description>Les derni&amp;egrave;res nouvelles</description>
	  <language>fr</language>
	  <managingEditor>'.$site['mailwebmaster'].'</managingEditor>
	  <generator>Scout Web Portail '.$site['version_portail'].'</generator>
	  <copyright>Scout Web Portail - '.date('Y').'</copyright>
	  <webMaster>'.$site['webmaster'].'</webMaster>
	  <lastBuildDate>'.date('D, j M Y G:i:s').' GMT</lastBuildDate>
	  <ttl>1440</ttl>
';

// Sélection des news à inclure
$sql = 'SELECT *, date_format(datecreation, \'%a, %d %b %Y %H:%i:%S\') as datef FROM '.PREFIXE_TABLES.'news, '.PREFIXE_TABLES.'auteurs WHERE auteur_news = num AND news_bannie != \'1\' ORDER BY datecreation DESC LIMIT 0,10';
$res = send_sql($db, $sql);

// Mise en page des news
while($ligne = mysql_fetch_assoc($res)) 
{
	// Comme dans la version 1.0 du portail il n'y avait pas de champ titre,
	// on en donne un par défaut : Actu du date du jour
	$titre = (empty($ligne['titre_news'])) ? 'Actu du '.html_entity_decode(date_ymd_dmy($ligne['datecreation'], 'jourmois')) : htmlentities($ligne['titre_news'], ENT_QUOTES).' ('.html_entity_decode(date_ymd_dmy($ligne['datecreation'], 'jourmois')).')';
	$news = makehtml($ligne['texte_news'], 'bbcode');
	$news = str_replace('<br />', '<br />', $news);
	$news = str_replace('&','&amp;', $news);
	$news = str_replace('<', '&lt;', $news);
	$news = str_replace('>', '&gt;', $news);
	
	$xml .= '<item>';
	$xml .= '<author>'.$ligne['pseudo'].'</author>';
	$xml .= '<title>'.$titre.'</title>';
	$xml .= '<link>'.$site['adressesite'].'</link>';
	$xml .= '<pubDate>'.$ligne['datef'].' GMT</pubDate>';
	$xml .= '<description>';
	
	$xml .= $news; 
	
	$xml .= '</description></item>';
}

// Fin du fil
$xml .= '</channel>
</rss>';

// Mise en cache du fil de news rss
if ($fnews = fopen('news.xml', 'w'))
{
	fwrite($fnews, $xml);
	fclose($fnews);
}
else
{ // Le portail ne peut pas modifier ou créer le fichier news.xml
  // Comme il s'agit d'une tâche de fond exécutée à la création d'une news, seul le webmaster est informé de l'erreur
	log_this('Impossible de cr&eacute;er le fil de news (RSS). Le portail ne peut pas modifier le fichier news.xml.', 'rss_maker', true);
}
?>