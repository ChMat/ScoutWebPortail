<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listing_membres.php v 1.0.3 - Formulaire pour afficher un listing membres (appelle listing_membres2.php)
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

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
?>
<?php
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Listings - <?php echo $site['titre_site']; ?></title>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
	}
	if (count($sections) > 0)
	{
?>
<h1>Affichage des listings de l'Unit&eacute;.</h1>
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

function aff_effectif_complet(form)
{
	if (form.num_unites.value != "") 
	{
		liste = form.num_unites.value.split('_');
		var x=0;
		while (x < liste.length)
		{
			if (form.section.value == liste[x])
			{
				if (confirm("Souhaites-tu afficher l'effectif complet de l'Unité ?"))
				{
					form.afftotale.value = 1;
				}
				else
				{
					alert("Seuls les membres du staff d'Unité seront affichés.");
				}
			}
			x+=1;
		}
	}
}
//-->
</script>
<form action="listing_membres2.php" method="post" name="choixsection" target="_blank" class="form_config_site" id="formulaire" onsubmit="return check_form(this)">
  <h2>1. Choisis le format du listing
  </h2>
  <p>
	<input name="format" type="radio" id="radio5" value="web" onclick="getElement('formulaire').action = 'listing_membres2.php'; document.choixsection.target = '_blank';" checked="checked" /> 
	<label for="radio5">Afficher le listing en format web</label>
	<br />
	<input type="radio" name="format" id="radio6" value="csv" onclick="getElement('formulaire').action = 'listing_membres2.php'; document.choixsection.target = '_self';" /> 
	<label for="radio6">Exporter le listing</label> (pour Excel ou le tableur d'Open 
	Office)
	<br />
	<input name="format" type="radio" id="radio7" value="photo" onclick="getElement('formulaire').action = 'listing_membres_photo.php'; document.choixsection.target = '_blank';" /> 
	<label for="radio7">Afficher le listing photo</label>
	avec <input name="parligne" type="text" id="parligne" value="6" size="2" onchange="if (isNaN(this.value) || this.value < 1) {alert('Merci d\'entrer une valeur numérique supérieure à 0.'); this.value = '6';}" />
  photos par ligne (champs non s&eacute;lectionnables)</p>
  <h2>2. S&eacute;lectionne les champs &agrave; afficher </h2>
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
        )<br /><br /> 
        <input type="checkbox" name="show_4" id="show_42" value="1" checked="checked" /> 
        <label for="show_42">Adresse (+ CP et ville)</label>
        <br /> 
        <input type="checkbox" name="show_adresse2" id="show_adresse23" value="1" checked="checked" /> 
        <label for="show_adresse23">Coordonn&eacute;es deuxi&egrave;me famille</label> 
        <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(si n&eacute;cessaire)
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
        <br /><br />
        <input name="show_section" type="checkbox" id="show_section" value="1" checked="checked" /> 
         <label for="show_section" title="S'affiche uniquement si tu affiches l'Unité au complet">Nom de la Section</label>
	  </td>
      <td width="50%" valign="top"><input type="checkbox" name="show_9" value="1" id="show_92" /> 
		<label for="show_92">Totem et quali</label>
		<br /><br /> 
		<input type="checkbox" name="show_10" value="1" id="show_102" /> 
		<label for="show_102">Email membre</label>
		<br /> 
		<input type="checkbox" name="show_11" value="1" id="show_112" /> 
		<label for="show_112">Email famille</label>
		<br /> 
		<input type="checkbox" name="show_12" value="1" id="show_122" /> 
		<label for="show_122">Email famille 2</label>
		<br /> 
		<input type="checkbox" name="show_13" value="1" id="show_132" /> 
		<label for="show_132">Tel perso membre</label>
		<br /><br /> 
		<input type="checkbox" name="show_14" value="1" id="show_142" /> 
		<label for="show_142">Site web membre</label>
		<br /><br /> 
		<input type="checkbox" name="show_15" value="1" id="show_152" /> 
		<label for="show_152">Statut (sizenier, second, cp, sp)</label>
		<br /> 
		<input type="checkbox" name="show_16" value="1" id="show_162" /> 
		<label for="show_162">Sizaine</label>
		<br /> 
		<input type="checkbox" name="show_17" value="1" id="show_172" /> 
		<label for="show_172">Fonction (A, AN, MB)</label>
		<br /><br /> 
		<input type="checkbox" name="show_18" value="1" id="show_182" checked="checked" /> 
		<label for="show_182">Etat cotisation</label></td>
    </tr>
  </table>
  <h2>    3. Qui afficher ? </h2>
  <p>Afficher
        <select name="restreint">
          <option value="tous" selected="selected">tous les membres</option>
          <option value="staff">les animateurs uniquement</option>
          <option value="scouts">les anim&eacute;s uniquement</option>
          <option value="sizeniers">les sizeniers/seconds/CP/SP</option>
        </select>
    <br />
      qui sont n&eacute;s
  <input type="radio" name="quand" id="avtannee" value="avant" />
    <label for="avtannee">avant</label>
  <input type="radio" name="quand" id="apsannee" value="apres" />
    <label for="apsannee">apr&egrave;s</label>
  <input type="radio" name="quand" id="enannee" value="en" checked="checked" />
    <label for="enannee">en</label>
  <input name="annee" type="text" id="annee" size="10" maxlength="4" title="Entre l'année de référence (aaaa)" onchange="if (isNaN(this.value) || this.value < 1) {alert('Merci d\'entrer une année de référence.'); this.value = '';} else if (this.value == '2007') {if (confirm('Petit lien surprise de la part de l\'auteur du Scout Web Portail ;-)\nSi tu ne veux pas le suivre, clique sur Annuler.')) window.location= 'http://www.scouting2007.org/';}" />
    (laisser vide si pas d'application)
  </p>
  <p><label for="attente">Inclure les membres sur liste d'attente</label>       
    <input name="attente" type="checkbox" id="attente" value="oui" />
    </p>
  <h2>4. Crit&egrave;re de tri
  </h2>
<p>Trier par
<select name="tri">
  <option value="nom" selected="selected">Nom (par d&eacute;faut)</option>
  <option value="age">Age</option>
  <option value="siz">Sizaine/patrouille</option>
  <option value="cot">Etat de cotisation</option>
  <option value="fonc">Anim&eacute;s/Animateurs</option>
</select>
</p>
<h2>5. Choix de la section   </h2>
<p> S&eacute;lectionne une Section ou l'Unit&eacute; :
<input type="hidden" name="afftotale" value="0" />
<select name="section" onchange="aff_effectif_complet(this.form)">
  <option value=""></option>
      <?php
		$nbre_unites = 0;
		$num_unites = '';
		foreach($sections as $section)
		{
			if (!$section['anciens'])
			{
?>
  <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['unite'] == 0) ? ' style="font-weight:bold;"' : ''; ?><?php echo ($section['numsection'] == $user['numsection']) ? ' selected="selected"' : ''; ?>><?php echo $section['nomsection']; ?></option>
<?php
				if (is_unite($section['numsection']))
				{
					$nbre_unites++;
					$num_unites .=  '_'.$section['numsection'];
				}
			}
		}
		if ($nbre_unites > 1)
		{
?>
  <option value="tous" style="font-weight:bold;">Les <?php echo $nbre_unites; ?> unités ensemble</option>
<?php
		}
?>
</select>
<input type="hidden" name="num_unites" value="0<?php echo $num_unites; ?>" />
</p>
<p align="center">
  <input type="submit" name="Submit" value="Go !" />
</p>
</form>
<div class="instructions">
<h2>Impression du listing en format web :</h2>
<p class="petitbleu">Le listing sera affich&eacute; dans une nouvelle page 
  afin de permettre l'impression de la page.<br />
  Pour minimiser le nombre de pages n&eacute;cessaires, imprime la page en orientation 
  '<em>Paysage</em>'. Pour cela, dans la page du listing, clique sur le menu '<em>Fichier</em>' 
  de ton navigateur et s&eacute;lectionne '<em>mise en page</em>'. Ensuite, dans 
  la bo&icirc;te de dialogue, coche la case orientation '<em>paysage</em>' et 
  clique sur Ok. Ensuite, tu peux lancer l'impression de la page.</p>
<h2>Exportation vers Excel ou le tableur d'Open Office</h2>
<p class="petitbleu">  Cette option te permet de r&eacute;cup&eacute;rer le 
    listing sur ton ordinateur dans un fichier de format <acronym title="Comma Separated Values - Valeurs séparées par des virgules">CSV</acronym>. 
    Ce type de fichier est lisible par Microsoft Excel ou le tableur d'<a href="http://fr.openoffice.org/">Open 
    Office</a>, une fois que tu l'as t&eacute;l&eacute;charg&eacute; sur ton disque 
    dur, tu peux le modifier &agrave; ton gr&eacute; et l'enregistrer ensuite
    comme une table Excel. </p>
<h2>Affichage listing en photos</h2>
<p class="petitbleu">    Le listing photo comprend uniquement la photo du membre 
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
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>
