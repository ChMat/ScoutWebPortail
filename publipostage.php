<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* publipostage.php - Formulaire permettant de générer un publipostage (csv)
* avec les adresses de membres de l'unité
* Fichier lié : publipostage2.php
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
<title>Publipostage - <?php echo $site['titre_site']; ?></title>
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
<h1>Outil de publipostage</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
  page Gestion de l'Unit&eacute;</a></p>
<?php
		if ($_GET['msg'] == 'aucun')
		{
?>
<div class="msg">
<p class="rmq" align="center">Aucun membre correspondant &agrave; cette requ&ecirc;te n'a &eacute;t&eacute; trouv&eacute;</p>
</div>
<?php
		}
?>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.section.value != '')
	{
		if (form.section.value == 'tousetanciens' && !getElement("xcoti").checked)
		{
			alert("Etant donné que les anciens ne paient pas de cotisation, la restriction sur l'état de paiement de la cotisation est annulée.");
			getElement("xcoti").checked = true;
			return true;
		}
		else
		{
			return true;
		}
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
					x = liste.length;
				}
				else
				{
					alert("Seuls les membres du staff d'Unité seront affichés.");
				}
			}
			else
			{
				form.afftotale.value = 0;
			}
			x+=1;
		}
	}
}
//-->
</script>
<p>Le publipostage te permet d'exporter une liste d'adresses pour envoyer un courrier 
  &agrave; certains membres de ta section. Cet outil g&eacute;n&egrave;re un fichier 
  <acronym title="Comma Separated Values - Valeurs séparées par des virgules">CSV</acronym> 
  qui est lisible dans Microsoft Excel.</p>
<form action="publipostage2.php" method="post" name="choixsection" class="form_config_site" id="formulaire" onsubmit="return check_form(this)">
  <p><span class="rmqbleu">1. Afficher</span> 
    <select name="restreint">
      <option value="tous" selected="selected">tous les membres</option>
      <option value="staff">les animateurs uniquement</option>
      <option value="scouts">les anim&eacute;s uniquement</option>
      <option value="sizeniers">les sizeniers/seconds/CP/SP</option>
	  <option value="attente">les membres sur liste d'attente</option>
    </select>
    <br />
    qui sont n&eacute;s 
     
    <input type="radio" name="quand" id="avtannee" value="avant" />
    <label for="avtannee">avant</label>
     
    <input type="radio" name="quand" id"apsannee" value="apres" />
    <label for="apsannee">apr&egrave;s</label>
     
    <input type="radio" name="quand" id="enannee" value="en" checked="checked" />
    <label for="enannee">en</label>
    <input name="annee" type="text" id="annee" size="10" maxlength="4" title="Entre l'année de référence (aaaa)" onchange="if (isNaN(this.value) || this.value < 1) {alert('Merci d\'entrer une année de référence.'); this.value = '';}" />
    (laisser vide si pas d'application)</p>
    
  <p><span class="rmqbleu">2. Restreindre aux membres</span> <br />
     
    <input type="radio" name="coti" id="okcoti" value="1" />
    <label for="okcoti">en ordre</label>
     
    <input type="radio" name="coti" id="noncoti" value="0" />
    <label for="noncoti">pas en ordre de cotisation, ou dont le statut est 
    inconnu</label>
    <br />
	
    <input name="coti" type="radio" id="xcoti" value="non" checked="checked" />
    <label for="xcoti">Ne pas restreindre</label>  
  </p>
  <p class="rmqbleu">3. 
    <label for="attente">Inclure les membres sur liste d'attente</label> 
    <input name="attente" type="checkbox" id="attente" value="oui" />
    
  </p>
  <p><span class="rmqbleu">4.</span>
    <label for="aff_autre_nom">Afficher &eacute;galement le nom des membres dont le nom diffère de celui de la famille</label>
    <input name="aff_autre_nom" type="checkbox" id="aff_autre_nom" value="oui" />
    
  </p>
  <p><span class="rmqbleu">5. S&eacute;lectionne une Section ou l'Unit&eacute; 
    : </span> 
    <input type="hidden" name="afftotale" value="0" />
    <select name="section" id="section" onchange="aff_effectif_complet(this.form)">
      <option value=""></option>
<?php
		$nbre_unites = 0;
		$num_unites = '';
		foreach($sections as $section)
		{
?>
      <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['unite'] == 0) ? ' style="font-weight:bold;"' : ''; ?>><?php echo $section['nomsection']; ?></option>
      <?php
				if (is_unite($section['numsection']))
				{
					$nbre_unites++;
					$num_unites .=  '_'.$section['numsection'];
				}
		}
		if ($nbre_unites > 1)
		{
?>
	  <option value="tous" style="font-weight:bold;">Les <?php echo $nbre_unites; ?> unités ensemble</option>
	  <option value="tousetanciens" style="font-weight:bold;">Les <?php echo $nbre_unites; ?> unités ensemble et les anciens</option>
<?php
		}
		else if ($nbre_unites == 1)
		{
?>
	  <option value="tousetanciens" style="font-weight:bold;">L'unité complète et les anciens</option>
<?php
		}
?>
    </select>
    <input type="hidden" name="num_unites" value="0<?php echo $num_unites; ?>" />
    <input type="submit" name="Submit" value="Go !" />
  </p>
</form>
<div class="instructions">
<h2>Informations utiles</h2>
<p>  <span class="petitbleu">Une fois que tu as t&eacute;l&eacute;charg&eacute; ce 
    fichier sur ton disque dur, il te suffit de lancer Microsoft Word et de d&eacute;marrer 
    l'outil de publipostage (Menu Edition &gt; Fusion et publipostage ou Lettres 
    et publipostage). Le document source est le fichier <acronym title="Comma Separated Values - Valeurs séparées par des virgules">CSV</acronym> 
    que tu viens de t&eacute;l&eacute;charger. Pour le reste, suis les instructions 
    de Word.</span> </p>
<p class="petitbleu">Afin de r&eacute;duire les frais d'envoi, une adresse n'est 
  affich&eacute;e qu'une seule fois. S'il y a plusieurs membres &agrave; une m&ecirc;me 
  adresse, leurs pr&eacute;noms sont list&eacute;s, libre &agrave; toi ensuite 
  de les afficher sur l'enveloppe ou non. Les adresses secondaires des membres 
  sont &eacute;galement list&eacute;es le cas &eacute;ch&eacute;ant. </p>
<h3>Afficher les membres
</h3>
<p class="petitbleu"> Le contenu de cette case est compr&eacute;hensible. Cependant, si pour une raison 
    ou une autre tu souhaites restreindre l'affichage &agrave; des membres d'un 
    certain &acirc;ge, utilise les cases juste en dessous pour pr&eacute;ciser l'ann&eacute;e 
    de r&eacute;f&eacute;rence. </p>
<h3>Restreindre aux membres</h3>
<p class="petitbleu"> Si tu restreins l'affichage aux membres qui ne sont pas
  en ordre de cotisation ou dont le statut de paiement est inconnu, la liste
  de publipostage contiendra un champ avec le nombre de membres qui ne sont pas
  en ordre dans la famille. Tu pourras ainsi r&eacute;diger des courriers personnalis&eacute;s pour les 
    rappels de cotisation en calculant le montant restant d&ucirc;...</p>
<h3>Liste d'attente</h3>
<p class="petitbleu"> Si tu d&eacute;cides d'afficher les membres sur liste d'attente
  (en 1), inutile de cocher la case en 3, le script comprendra.</p>
<h3>Nom des membres</h3>
<p class="petitbleu"> Par d&eacute;faut, lorsque plusieurs membres d'une famille font partie de la 
    s&eacute;lection, leur pr&eacute;nom est affich&eacute; comme ceci : Pr&eacute;nom 
    1, Pr&eacute;nom 2, ... et Pr&eacute;nom n. Si l'un des membres de cette famille 
    porte un nom diff&eacute;rent de celui de la famille, tu peux l'afficher en 
    cochant la case 4. Si la case n'est pas coch&eacute;e, seul leur pr&eacute;nom 
    est affich&eacute; ainsi que le nom de la famille.</p>
</div>				
  <?php
	}
	else
	{
?>
<div class="msg">
  <p align=center class="rmq">Aucune section n'est pr&eacute;sente dans la base 
    de donn&eacute;es</p>
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
