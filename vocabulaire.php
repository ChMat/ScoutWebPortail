<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* vocabulaire.php - Terminologie employée sur le portail
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
<title>Vocabulaire utilis&eacute;sur le portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body leftmargin="1" topmargin="1" marginwidth="3" marginheight="3">
<?php
}
?>
<h1>Vocabulaire utilis&eacute; sur le portail</h1>
<p align="center"><a href="index.php?page=aide">Retour &agrave; l'aide</a></p>
<div class="introduction">
<p>Toute une s&eacute;rie de termes sont utilis&eacute;s sur
  le portail, il est toujours mieux de savoir exactement ce qu'ils signifient
  avant de faire des b&ecirc;tises...</p>
</div>
<dl>
  <dt>Espace web d'une Section/Unit&eacute;</dt>
  <dd>Chaque Section/Unit&eacute; pr&eacute;sente sur le portail peut disposer
    d'un espace web. Cet espace peut contenir autant de pages que les animateurs
    le souhaitent. Pour activer l'espace web d'une Section/Unit&eacute;, il faut &ecirc;tre
    connect&eacute; comme webmaster.</dd>
  <dt>Famille</dt>
  <dd>C'est l'&eacute;l&eacute;ment de base de la Gestion de l'Unit&eacute;.
    Chaque famille peut comporter autant de Membres que n&eacute;cessaire.</dd>
  <dt>Groupe scout</dt>
  <dd>L'ensemble des Sections rassembl&eacute;es sur le portail.</dd>
  <dt>Listing</dt>
  <dd>Tableau reprenant les donn&eacute;es au sujet d'une s&eacute;rie de Membres
    tri&eacute;s selon certains crit&egrave;res.</dd>
  <dt>Membre</dt>
  <dd>C'est une personne physique, elle est toujours li&eacute;e &agrave; une
    Section et &agrave; une ou deux familles.<br />
    A noter qu'il y a les Membres du portail et les Membres de l'Unit&eacute;.
    Ce sont deux choses totalement diff&eacute;rentes qui ne sont pas li&eacute;es
    l'une &agrave; l'autre.</dd>
  <dt>Niveau d'acc&egrave;s</dt>
  <dd>Il existe plusieurs niveaux d'acc&egrave;s au site. Les voici, du moins
    important au plus important : Visiteur anonyme, Visiteur identifi&eacute;,
    Membre de l'Unit&eacute;, <em>Animateur de Section, Animateur d'Unit&eacute;,
    Webmaster</em>. Les niveaux indiqu&eacute;s en italique n&eacute;cessitent
    une reconnaissance par d'autres membres du portail ayant un niveau d'acc&egrave;s
    sup&eacute;rieur ou &eacute;gal &agrave; celui demand&eacute; par le nouveau
    membre du portail.</dd>
  <dt>Passage</dt>
  <dd>Transfert d'un certain nombre de Membres d'une Section &agrave; une autre.</dd>
  <dt>Publipostage</dt>
  <dd>Production d'un fichier contenant les coordonn&eacute;es postales d'une
    s&eacute;rie de familles tri&eacute;es selon certains crit&egrave;res.</dd>
  <dt>Section</dt>
  <dd>La Section est l'&eacute;l&eacute;ment de base, elle peut contenir des
    animateurs et des anim&eacute;s. Une section peut &ecirc;tre subdivis&eacute;e
    en sizaines ou en patrouilles si n&eacute;cessaire.</dd>
  <dt>Staff</dt>
  <dd>Groupe de Membres ayant la Fonction d'Animateur ou d'Animateur responsable
    au sein d'une Section et/ou d'une Unit&eacute;.</dd>
  <dt>Statut d'un Membre du portail</dt>
  <dd>Le webmaster peut cr&eacute;er autant de statuts qu'il le souhaite. Les
    statuts sont une mani&egrave;re conviviale d'afficher qui est qui parmi les
    membres du portail. Chaque Statut &agrave; un certain niveau d'acc&egrave;s
    au portail.</dd>
  <dt>Unit&eacute;</dt>
  <dd>L'unit&eacute; est une Section elle-m&ecirc;me qui regroupe un certain
    groupe de Sections. La premi&egrave;re Section de chaque Unit&eacute; est
    la Section Anciens.</dd>
</dl>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>
