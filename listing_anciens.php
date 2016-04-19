<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listing_anciens.php - Configuration du listing des anciens de l'Unité
* Fichier lié : listing_anciens2.php
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

include_once("connex.php");
include_once("fonc.php");
if ($user["niveau"][numniveau] <= 2)
{
	include("404.php");
}
else
{
?>
<?php
	if (!defined("IN_SITE"))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Listings - <?php echo $site["titre_site"]; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
	}
	if (count($sections) > 0)
	{
?>
<h1>Affichage des listings des Anciens de l'Unit&eacute;.</h1>
<div align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
  page Gestion de l'Unit&eacute;</a><br />
  <br />
</div>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.section.value != '')
	{
		return true;
	}
	else
	{
		alert("N'oublie pas de choisir la section à afficher !");
		return false;
	}
}

//-->
</script>
<form action="listing_anciens2.php" method="post" name="choixsection" target="_blank" class="form_config_site" id="formulaire" onsubmit="return check_form(this)">
  <h2>1. Choisis le format du listing</h2>
  <p><input name="format" type="radio" id="radio5" value="web" onclick="getElement('formulaire').action = 'listing_anciens2.php'; document.choixsection.target = '_blank';" checked="checked" /> 
	<label for="radio5">Afficher le listing en format web</label>
	<br />
	<input type="radio" name="format" id="radio6" value="csv" onclick="getElement('formulaire').action = 'listing_anciens2.php'; document.choixsection.target = '_self';" /> 
	<label for="radio6">Exporter le listing</label> (pour Excel ou le tableur d'Open 
	Office)
	<br />
	<input name="format" type="radio" id="radio7" value="photo" onclick="getElement('formulaire').action = 'listing_anciens_photo.php'; document.choixsection.target = '_blank';" /> 
	<label for="radio7">Afficher le listing photo</label>
	avec <input name="parligne" type="text" id="parligne" value="6" size="2" onchange="if (isNaN(this.value) || this.value < 1) {alert('Merci d\'entrer une valeur numérique supérieure à 0.'); this.value = '6';}" />
	photos par ligne (champs non s&eacute;lectionnables)</p>
  <h2>  2. S&eacute;lectionne les champs &agrave; afficher </h2>
  <table width="500" border="0" cellpadding="2" cellspacing="0">
    <tr> 
      <td width="50%" valign="top">
	  
        <input type="checkbox" name="afflink" id="afflink" value="1" checked="checked" />
         <label for="afflink">Lien vers la fiche membre</label>
        <br />
	   <input type="checkbox" name="show_1" id="show_19" value="1" checked="checked" /> 
        <label for="show_19">Nom</label>
        <br /> 
        <input type="checkbox" name="show_2" id="show_22" value="1" checked="checked" /> 
        <label for="show_22">Pr&eacute;nom</label>
        <br /> 
        <input type="checkbox" name="show_3" id="show_32" value="1" checked="checked" /> 
        <label for="show_32">DDN</label>
        ( 
        <input type="radio" name="show_ddn" value="date" id="radio3" checked="checked" /> 
        <label for="radio3">Date</label> 
        <input type="radio" name="show_ddn" id="radio4" value="age" /> 
        <label for="radio4">Age</label>
        )<br />
        <br /> 
        <input type="checkbox" name="show_4" id="show_42" value="1" checked="checked" /> 
        <label for="show_42">Adresse (+ CP et ville)</label>
        <br /><br /> 
        <input type="checkbox" name="show_5" value="1" id="show_52" checked="checked" /> 
        <label for="show_52">Tel 1</label>
        <br /> 
        <input type="checkbox" name="show_6" value="1" id="show_62" /> 
        <label for="show_62">Tel 2</label>
        <br /> 
        <input type="checkbox" name="show_7" value="1" id="show_72" /> 
        <label for="show_72">Tel 3</label>
        <br /> 
        <input type="checkbox" name="show_8" value="1" id="show_82" /> 
        <label for="show_82">Tel 4</label>
        <br /> </td>
      <td width="50%" valign="top"> <input type="checkbox" name="show_9" value="1" id="show_92" /> 
        <label for="show_92">Totem et quali</label>
        <br /><br /> 
        <input type="checkbox" name="show_10" value="1" id="show_102" /> 
        <label for="show_102">Email membre</label>
        <br /> 
        <input type="checkbox" name="show_11" value="1" id="show_112" /> 
        <label for="show_112">Email famille</label>
        <br /> 
        <input type="checkbox" name="show_12" value="1" id="show_122" /> 
        <label for="show_122">Tel perso membre</label>
        <br /><br /> 
        <input type="checkbox" name="show_13" value="1" id="show_132" /> 
        <label for="show_132">Site web membre</label>
      </td>
    </tr>
  </table>
  <h2>3. Crit&egrave;re de tri</h2>
  <p>Trier par 
    <select name="tri">
      <option value="nom" selected="selected">Nom (par d&eacute;faut)</option>
      <option value="age">Age</option>
        </select>
  </p>
  <h2>4. Choix de la section </h2>
  <p>S&eacute;lectionne une Section d'Anciens :
    <select name="section">
      <option value=""></option>
      <?php
		$nbre_sections = 0;
		foreach($sections as $section)
		{
			if ($section[anciens] == 1)
			{
				$nbre_sections++;
?>
          <option value="<?php echo $section[numsection]; ?>"><?php echo $section["nomsection"]; ?></option>
<?php
			}
		}
		if ($nbre_sections > 1)
		{
?>
	  <option value="tous" style="font-weight:bold;">Les <?php echo $nbre_sections; ?> sections d'Anciens ensemble</option>
<?php
		}
?>
    </select>
  </p>
  <p align="center">
    <input type="submit" name="Submit" value="Go !" />
  </p>
</form>

<div class="instructions">
<h2>Impression du listing en format web :</h2>
<p class="petitbleu">  Le listing sera affich&eacute; dans une nouvelle page 
    afin de permettre l'impression de la page.<br />
    Pour minimiser le nombre de pages n&eacute;cessaires, imprime la page en
    orientation '<em>Paysage</em>'. Pour cela, dans la page du listing, clique sur le menu '<em>Fichier</em>'
    de ton navigateur et s&eacute;lectionne '<em>mise en page</em>'. Ensuite,
    dans la bo&icirc;te de dialogue, coche la case orientation '<em>paysage</em>' et 
    clique sur Ok. Ensuite, tu peux lancer l'impression de la page. </p>
<h2>Exportation vers Excel ou le tableur d'Open Office</h2>
<p class="petitbleu">  Cette option te permet de r&eacute;cup&eacute;rer le 
    listing sur ton ordinateur dans un fichier de format CSV. Ce type de fichier
  est lisible par Microsoft Excel ou le tableur d'<a href="http://fr.openoffice.org/">Open 
    Office</a>, une fois que tu l'as t&eacute;l&eacute;charg&eacute; sur ton disque 
  dur, tu peux le modifier &agrave; ton gr&eacute; et l'enregistrer ensuite. </p>
<h2>Affichage listing en photos</h2>
<p class="petitbleu">  Le listing photo comprend uniquement la photo du membre 
    (si pr&eacute;sente), ses pr&eacute;noms, nom et date de naissance. Tous les 
    autres param&egrave;tres sont r&eacute;glables. </p>
<h2>Incorporation dans une base de donn&eacute;es</h2>
<p class="petitbleu">  A l'heure actuelle, la seule mani&egrave;re d'incorporer 
    les donn&eacute;es dans une base de donn&eacute;es consiste &agrave; cr&eacute;er 
    la base selon le format d'exportation des listings propos&eacute;s ici.</p>
</div>
  <?php
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Aucune section n'est pr&eacute;sente dans la base de donn&eacute;es.</p>
</div>
<?php
	}
}
?>
  <?php
if (!defined("IN_SITE"))
{
?>
</body>
</html>
<?php
}
?>
