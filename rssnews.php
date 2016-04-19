<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* rssnews.php - Page d'informations sur les news du site au format RSS 2.0
* Fichiers liés : gestionnews.php, news.xml (généré dynamiquement)
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
<title>Les News du site en RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
$url_rss = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
$url_rss .= (ereg("/$", $url_rss)) ? 'news.xml' : '/news.xml';
?>
<h1>Les News du Site en RSS 2.0</h1>
<p class="petitbleu">Le RSS, pour afficher les News du Site sur ton site web ou 
  sur ton ordinateur.</p>
<h2>Le fil RSS se trouve ici :</h2>
<p align="center"><a href="<?php echo $url_rss; ?>"><?php echo $url_rss; ?></a></p>
<h2>Principe de fonctionnement</h2>
<p> Le RSS (pour Real Simple Syndication) utilise le langage XML. Le XML est, 
  comme le langage HTML, un langage orient&eacute; pour les applications web. 
  Le format des fils RSS est standardis&eacute; afin que chacun puisse les r&eacute;cup&eacute;rer 
  pour son usage personnel ou les diffuser sur son propre site web. Actuellement, 
  les RSS ont un grand succ&egrave;s aux USA, mais ils sont en train d'appara&icirc;tre 
  en Europe : la <a href="http://www.lalibre.be/article.phtml?id=12&subid=179&art_id=156215" target="_blank">Libre Belgique</a> 
  et le <a href="http://www.monde-diplomatique.fr/recents" target="_blank">Monde 
  diplomatique</a>, par exemple, diffusent leur actualit&eacute; &agrave; l'aide 
  des fils RSS. On rep&egrave;re souvent la pr&eacute;sence de ces fils gr&acirc;ce 
  au logo suivant ou &agrave; un autre : </p>
<p align="center"><img src="img/rss/rss2.gif" width="80" height="15" align="middle" alt="" /></p>
<h2>Les logiciels &agrave; utiliser</h2>
<p>Pour lire nos fils RSS sur ton ordinateur, il te faut un &quot;lecteur de fils 
  RSS&quot;. Il en existe plusieurs et ils ont chacun leurs qualit&eacute;s.</p>
<p>Pour <strong>tous les syst&egrave;mes d'exploitation </strong>, le jeune concurrent de Internet
   Explorer, le navigateur gratuit <a href="http://frenchmozilla.sourceforge.net/firefox/" target="_blank">Mozilla
    Firefox</a> est capable de lire les fils RSS gr&acirc;ce &agrave; sa fonctionnalit&eacute; de
    marque-pages dynamiques. Le logiciel de courrier <a href="http://frenchmozilla.sourceforge.net/thunderbird/" target="_blank">Mozilla
   Thunderbird</a>  dispose lui aussi d'un lecteur RSS int&eacute;gr&eacute;.</p>
<p>Pour <strong>Microsoft Windows</strong>, il existe &eacute;galement entre autres : <a href="http://www.rssreader.com/" target="_blank">RSSReader</a>, 
  <a href="http://www.disobey.com/amphetadesk/" target="_blank">AmphetaDesk</a>, 
  <a href="http://www.feedreader.com/" target="_blank">FeedReader</a>, ...</p>
<p>Pour <strong>Mac</strong>, on peut trouver <a href="http://ranchero.com/netnewswire/" target="_blank">NetNewsWire</a></p>
<p>Et enfin pour <strong>Linux</strong> : <a href="http://hyperlinkextractor.free.fr/rss.html" target="_blank">Hyperlink</a></p>
<p>Il en existe bien d'autres, la liste n'est pas exhaustive. </p>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
?>