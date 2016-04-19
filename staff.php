<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* staff.php v 1.1 - Affichage public des staffs présents dans la gestion de l'unité
* Ce fichier génère automatiquement la liste des adresses des staffs
* Il n'exploite pas les données des membres du portail mais bien celles des membres de l'unité
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
*	ajout préférence d'affichage du totem selon la section
* Modifications v 1.1.1
*	message d'avertissement sur la différence entre inscription sur le site et dans la gestion de l'unité.
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
<title>Les Staffs</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
if (is_array($sections))
{
	foreach ($sections as $test_niv) 
	{ // récupération du numéro de section correspondant à la lettre de $niv
	   if ($test_niv['site_section'] == $niv) 
	   {
		   $section_en_cours = $test_niv['numsection'];
	   }
	}
}
if (is_unite($section_en_cours) and empty($_GET['qui']) and !empty($section_en_cours))
{
?>
<h1>Les Staffs</h1>
<p>S&eacute;lectionne ci-dessous le staff de ton choix. </p>
      <?php
	if (is_array($sections))
	{
		foreach ($sections as $unite)
		{
			if (is_unite($unite['numsection']) and $unite['site_section'] == $niv)
			{
				echo '<span class="rmqbleu"><a href="index.php?niv='.$niv.'&amp;page=staff&amp;qui=section&amp;section='.$unite['numsection'].'" class="lienmort">Staff '.$unite['nomsection'].'</a> ('.$unite['federation'].', '.$unite['code_unite'].')</span>';
?>
      <ul>
        <?php
				foreach ($sections as $ligne)
				{
					if ($ligne['unite'] == $unite['numsection'] and !$ligne['anciens'])
					{
						if (!empty($ligne['site_section']))
						{ // l'espace web de la section est activé
							$lien_staff = ($site['url_rewriting_actif'] == 1) ? $ligne['site_section'].'_staff.htm' : 'index.php?niv='.$ligne['site_section'].'&amp;page=staff';
							echo '<li><a href="'.$lien_staff.'">Staff '.$ligne['nomsection'].'</a> ('.$ligne['appellation'].', '.$ligne['trancheage'].')</li>';
						}
						else
						{ // l'espace web de la section concernée n'est pas activé
							echo '<li><a href="index.php?niv='.$niv.'&amp;page=staff&amp;qui=section&amp;section='.$ligne['numsection'].'">Staff '.$ligne['nomsection'].'</a> ('.$ligne['appellation'].', '.$ligne['trancheage'].')</li>';
						}
					}
				}
?>
      </ul>
<?php
			}
		}
	}
	else
	{
?>
<div class="msg">
<p align="center">La base de donn&eacute;es ne contient aucune section.</p>
</div>
<?php
	}
?>
<?php
}
else
{
	if ($_GET['qui'] == 'resp')
	{
		$titre = 'Personnes de contact';
		$fonction = '(a.fonction = \'3\' OR a.fonction = \'5\')';
		$section = '';
	}
	else if ($_GET['qui'] == 'section' and is_numeric($_GET['section']))
	{
		$fonction = "a.fonction > '1'";
		$titre = 'Staff '.$sections[$_GET['section']]['nomsectionpt'];
		$section = " AND a.section = '$_GET[section]'";
	}
	else
	{
		$ssection = '';
		if (!is_numeric($_GET['section']))
		{
			if (is_array($sections))
			{
				foreach ($sections as $test_niv) 
				{ // récupération du numéro de section correspondant à la lettre de $niv
				   if ($test_niv['site_section'] == $niv) 
				   {
					   $ssection = $test_niv['numsection'];
				   }
				}
			}
		}
		else
		{
			$ssection = $_GET['section'];
		}
		$fonction = "a.fonction > '1'";
		$nomsectionpt = $sections[$ssection]['nomsectionpt'];
		$titre = (!empty($nomsectionpt)) ? 'Staff '.$nomsectionpt : 'Tous les staffs';
		$section = '';
		if (!empty($ssection))
		{
			$section = "AND a.section = '$ssection'";
		}
	}
	$sql = "SELECT prenom, nom_mb, section, fonction, totem, quali, totem_jungle, photo, rue, numero, bte, cp, ville, tel1, telperso, email_mb, siteweb FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."mb_adresses as b WHERE actif = '1' AND a.famille = b.numfamille AND $fonction $section ORDER BY fonction DESC, nom_mb ASC";
	// affichage des staffs demandés
?>
<h1><?php echo $titre; ?></h1>
  <?php
	if ($_GET['qui'] == 'resp')
	{
?>
<p>
  Pour nous contacter, choisis le responsable qui t'int&eacute;resse. Si tu h&eacute;sites 
  sur qui contacter, l'Animateur d'Unit&eacute; est la personne de r&eacute;f&eacute;rence 
  pour toutes les sections, il pourra te renseigner utilement.</p>
  <?php
	}
	else if (empty($ssection))
	{
?>
<p> Ci-dessous, tu trouveras tous les animateurs. Pour nous contacter, choisis 
  le responsable qui t'int&eacute;resse. Si tu h&eacute;sites sur qui contacter, 
  l'Animateur d'Unit&eacute; est la personne de r&eacute;f&eacute;rence pour toutes 
  les sections, il pourra te renseigner utilement.</p>
  <?php
	}
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
<table width="80%" border="0" align="center" cellspacing="1">
<?php
			while ($membre = mysql_fetch_assoc($res))
			{
?>
  <tr class="td-gris"> 
    <td colspan="2"> <p align="center">
<?php
				if (!empty($membre['totem_jungle']) and $sections[$membre['section']]['aff_totem_meute'] == 1)
				{
					echo '<span class="titre2">'.$membre['totem_jungle'].'</span> - '.$membre['prenom'].' '.$membre['nom_mb'];
				}
				else if (!empty($membre['totem']))
				{
					echo '<span class="titre2">'.$membre['totem'].' '.$membre['quali'].'</span> - '.$membre['prenom'].' '.$membre['nom_mb'];
				}
				else
				{
					echo '<span class="titre2">'.$membre['prenom'].' '.$membre['nom_mb'].'</span>';
				}
				if ($membre['section'] != 0)
				{
					$nomsection = $sections[$membre['section']]['nomsection'];
				}
				else
				{
					$nomsection = '';
				}
				if ($membre['fonction'] != 0)
				{
					$nomfonction = $fonctions[$membre['fonction']]['nomfonction'];
					$appellation = $sections[$membre['section']]['appellation'];
					if ($appellation == 'erreur')
					{
						$appellation = '';
					}
				}
				else
				{
					$nomfonction = '';
					$appellation = '';
				}
				$infos = $nomfonction.' '.$appellation.' '.$statut; 
				if (strlen($infos) > 2)
				{
					echo '<br /><span class="petitbleu">'.$infos.'</span>';
				}
?>
			</p></td>
  </tr>
  <tr> 
    <td width="150">
	<div align="center">
        <?php if (!empty($membre['photo'])) {echo '<img src="'.$membre['photo'].'" alt="'.$membre['prenom'].' '.$membre['nom_mb'].'" title="'.$membre['prenom'].' '.$membre['nom_mb'].'" />';} else {?>
        <img src="templates/default/images/pasdephoto.gif" alt="" width="75" height="100" title="La chasse aux photos est ouverte !" />
        <?php } ?>
      </div></td>
    <td class="td-gris"> 
	  <p align="center"><em><strong>Adresse :</strong></em>
<?php
				$slash = '';
				$virgule = '';
				if (!empty($membre['rue'])  and !empty($membre['numero'])) 
				{
					$virgule = ', ';
					if (!empty($membre['bte']))
					{
						$slash = ' bte ';
					}
				} 
				else 
				{
					$virgule = '';
					$slash = '';
				} 
				echo $membre['rue'].$virgule.$membre['numero'].$slash.$membre['bte'].'<br />'.$membre['cp'].' '.$membre['ville']; ?>
          <?php if (!empty($membre['tel1'])) {echo '<br /><br />'.$membre['tel1'];} else {echo '<br /><br />';} ?>
		  <?php if (!empty($membre['tel1']) and !empty($membre['telperso'])) {echo ' ou ';} ?>
		  <?php if (!empty($membre['telperso'])) {echo $membre['telperso'];} ?>
		  <?php
				if (!empty($membre['email_mb'])) {echo '<br /><em><strong>Email :</strong></em> '.hidemail($membre['email_mb'], 'lien');}
				$lien_externe = (!empty($membre['siteweb']) and $site['avert_onclick_lien_externe'] == 1) ? 'onclick="lien_externe();" ' : '';
				if (!empty($membre['siteweb'])) {echo '<br /><em><strong>Site web :</strong> </em><a '.$lien_externe.'href="'.$membre['siteweb'].'" target="_blank">'.$membre['siteweb'].'</a><br />'; }
		  ?>
      </p></td>
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
  <p align="center" class="rmq">Aucun membre du staff n'est pr&eacute;sent dans la base de donn&eacute;es.</p>
</div>
<?php
		}
		if ($user['niveau']['numniveau'] > 2)
		{
?>
<div class="msg_anim">
<p align="center">Le staff affich&eacute; ici est directement extrait des donn&eacute;es 
  de la <a href="index.php?page=gestion_unite">Gestion de l'Unit&eacute;</a>. </p>
<p align="center">En tant qu'animateur, tu peux ajouter et modifier ces informations.</p>
<p align="center"><a href="index.php?page=newmb">Ajouter les membres des Staffs</a></p>
<p align="center"><strong>Note :</strong> L'<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'listembsite.htm' : 'index.php?page=listembsite'; ?>">inscription sur le site</a> est totalement s&eacute;par&eacute;e de la Gestion de l'Unit&eacute;. </p>
</div>
<?php
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