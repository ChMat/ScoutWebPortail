<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* aidecreationpage.php v 1.1 - Informations pour la création de pages sur le portail
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
*	Correction lien mort vers le siteduzero (merci à Mangouste) (fil 106)
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
<title>Un peu d'aide</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body class="body_popup">
<?php
}
?>
<h1>Un peu d'aide pour r&eacute;diger les pages </h1>
<p align="center"><a href="index.php?page=aide">Retour &agrave; l'aide</a></p>
<p>Tu peux r&eacute;diger les pages dans deux formats diff&eacute;rents : format 
  HTML et format texte.</p>
<ul>
  <li>Le plus simple est le format texte : tu &eacute;cris ton texte et il est 
    affich&eacute; comme tu l'as &eacute;crit. Seul inconv&eacute;nient, il est 
    tr&egrave;s limit&eacute;.</li>
  <li>Un peu plus compliqu&eacute; mais sans limite : le format HTML. Avec lui, 
    tu peux tout faire pour obtenir des pages qui ont de la gueule.</li>
</ul>
<h2>Le format texte</h2>
<p>En format texte, seules quelques fonctions te sont propos&eacute;es mais tu 
  peux d&eacute;j&agrave; r&eacute;diger de nombreuses choses.</p>
<ul>
  <li>Mettre du texte en forme : gras, soulign&eacute;, italique, en rouge et 
    en bleu, en petit, ...</li>
  <li>Insertion de liens email et vers d'autres pages.</li>
  <li>Insertion de liens hypertexte dans et en dehors du portail.</li>
  <li>Insertion de smileys.</li>
</ul>
<h2>Le format HTML</h2>
<p>Dans ce format, tu peux r&eacute;diger vraiment tout ce qui te pla&icirc;t 
  en HTML. Diverses fonctions te sont propos&eacute;es, tu peux les utiliser :</p>
<ul>
  <li>Les fonctions propos&eacute;es pour le format texte</li>
  <li>Insertion de tableaux</li>
  <li>Insertion d'images</li>
  <li>Utilisation de toutes les classes CSS du portail (<a href="templates/default/style.css" target="_blank">t&eacute;l&eacute;charger 
    style.css ici</a> <span class="petit">via un clic sur le bouton de droite 
    et ensuite : Enregistrer sous</span>)</li>
  <li>ainsi que toutes les possibilit&eacute;s qu'offre le langage HTML</li>
  <li>La seule chose qui te soit interdite est de r&eacute;cup&eacute;rer les 
    cookies des utilisateurs et ce pour des raisons de s&eacute;curit&eacute;.</li>
</ul>
<p>Pour am&eacute;liorer la compatibilit&eacute; avec certains navigateurs, et 
  si tu r&eacute;diges tes pages &agrave; l'avance &agrave; l'aide d'un logiciel 
  de conception de sites web, merci de ne copier dans la page que le code html 
  contenu entre les balises <span class="petitbleu">&lt;body&gt;</span> et <span class="petitbleu">&lt;/body&gt;</span>.</p>
<h2>Cr&eacute;er des liens internes au portail</h2>
<p>L'ajout de liens internes au portail ressemble &agrave; ceci : <br />
  <span class="petit">En format texte, un lien vers le forum ressemblerait donc 
  &agrave; ceci : </span><code>[url=forum.htm]Le forum[/url]</code></p>
<p>En format HTML, la balise normale peut &ecirc;tre utilis&eacute;e.<br />
  <span class="petit">En format html, un lien vers le forum ressemblerait donc 
  &agrave; ceci : </span><code>&lt;a href=&quot;forum.htm&quot;&gt;Le forum&lt;/a&gt;</code></p>
<p>Mais tout d'abord, voici comment fonctionnent les liens internes au portail :</p>
<p>Pour le portail, presque tous les fichiers sont dans le m&ecirc;me 
  dossier (exception faite des images). De nombreuses pages sont accessibles simplement 
  via un lien : livreor.htm, forum.htm, tally.htm, staff.htm, 
  ... </p>
<p>Les pages que tu r&eacute;diges sont accessibles simplement via une astuce 
  dans le nom de fichier :</p>
<p>Le portail est divis&eacute; en plusieurs zones correspondant aux sections pr&eacute;sentes 
  sur le portail. A chaque section correspond une lettre de l'alphabet. Cette 
  lettre se trouve en t&ecirc;te de nom du fichier. Par exemple, le programme 
  loup serait &agrave; la page m_programmemeute.htm si la Meute avait pour indicatif 
  web la lettre m.</p>
<p>La plupart des pages du portail fonctionnent sur ce principe, &agrave; toi de 
  visiter le portail et d'afficher la page vers laquelle tu fais un lien et de recopier 
  son adresse dans ta page.</p>
<p>Selon la configuration du portail, l'astuce ci-dessus peut ne pas fonctionner. 
  Dans ce cas tu devras utiliser un syst&egrave;me l&eacute;g&egrave;rement plus 
  compliqu&eacute;. <br />
  Pour afficher une page, on doit faire un lien vers index.php et lui donner deux 
  variables : la zone du portail et la page demand&eacute;e. Le nom du fichier est 
  s&eacute;par&eacute; des variables par un point d'interrogation et chaque variable 
  est suivie du signe = et de sa valeur; les variables sont s&eacute;par&eacute;es 
  entre elles par le caract&egrave;re &amp;.</p>
<p>Ce qui donne : <code>index.php?niv=x&amp;page=lapagedemandee</code><br />
  <br />
  dans ce cas, on appelle la page index.php et on lui fournit les variables niv 
  et page. niv a la valeur &quot;x&quot; et page a la valeur &quot;lapagedemandee&quot;.</p>
<p>niv ou la premi&egrave;re lettre du nom de fichier d&eacute;termine l'affichage 
  du menu du portail. Comme une lettre correspond &agrave; une section, c'est le 
  menu de cette section qui sera affich&eacute;.</p>
<h2>Derniers tuyaux : </h2>
<ul>
  <li>Si tu souhaites que la page s'ouvre dans une nouvelle fen&ecirc;tre, tu 
    peux ajouter un param&egrave;tre &agrave; la balise de lien : <br />
    <code>&lt;a href=&quot;fichier.zip&quot; <span class="rmq">target=&quot;cible&quot;</span>&gt;texte 
    du lien&lt;/a&gt;</code>. cible est le nom de la fen&ecirc;tre cible ou un 
    indicatif : &quot;_blank&quot; pour une nouvelle fen&ecirc;tre. Si tu cr&eacute;es 
    plusieurs liens vers une cible &quot;pageexterne&quot;, chaque lien s'ouvrira 
    dans la page nomm&eacute;e &quot;pageexterne&quot;.<br />
    Test : <code>&lt;a href=&quot;http://www.google.be&quot; target=&quot;pagepourgoogle&quot;&gt;Google&lt;/a&gt;</code> 
    : <a href="http://www.google.be" target="pagepourgoogle">Google</a> - <a href="http://www.google.be" target="pagepourgoogle">Google 
    2</a></li>
  <li>Un lien menant vers une page &agrave; acc&egrave;s restreint (gestion des 
    membres, fiches de membres, ...) ne sera accessible qu'aux personnes autoris&eacute;es.</li>
</ul>
<p><span class="rmq">Pour plus d'informations</span> sur les principes de base 
  du langage HTML, consulte ce site assez bien fait sur le sujet : <a href="http://www.siteduzero.com/">http://www.siteduzero.com/</a> 
  ou cherche sur <a href="http://www.google.be" target="_blank">Google</a> avec 
  les mots-cl&eacute;s : <code>apprendre html</code></p>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>