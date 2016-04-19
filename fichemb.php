<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fichemb.php v 1.1 - Fiche d'un membre de l'Unité
* Un membre de l'unité n'est pas un ancien ni un membre du portail
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
*	Correction bug lien site web
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	if (headers_sent())
	{
		header('Location: index.php?page=404');
		exit;
	}
	else
	{
		include('404.php');
	}
}
else
{
	if (is_numeric($_GET['nummb']))
	{
		$nummb = $_GET['nummb'];
	}
	else if (is_numeric($_POST['nummb']))
	{
		$nummb = $_POST['nummb'];
	}
	else
	{
		$nummb = '';
	}
	if (!empty($nummb))
	{
		$restreindre = '';
		foreach ($sections as $section)
		{
			if ($section['anciens'] == 1)
			{
				$restreindre .= ' AND section != \''.$section['numsection'].'\' ';
			}
		}
		$sql = 'SELECT * FROM '.PREFIXE_TABLES.'mb_membres as a, '.PREFIXE_TABLES.'mb_adresses as b WHERE a.nummb = '.$nummb.' AND a.famille = b.numfamille '.$restreindre;
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
			$nomsection = $sections[$membre['section']]['nomsection'];
			$nomfonction = $fonctions[$membre['fonction']]['nomfonction'];
			$nomsizaine = $sizaines[$membre['siz_pat']]['nomsizaine'];
			$statut = $statuts[$membre['cp_sizenier']];
			$info_siz = $statut.' '.$nomsizaine; 
			if (!defined('IN_SITE'))
			{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Fiche d'un membre - <?php echo $site['titre_site']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
			}
			$mb_ok_javascript = str_replace('"', '', html_entity_decode($membre['prenom'].' '.$membre['nom_mb']));
?>
<div id="gestion_u_f_mb">
<h1>Fiche de <?php echo $membre['prenom'].' '.$membre['nom_mb']; ?></h1>
<script language="JavaScript" type="text/JavaScript">document.title = "<?php echo $mb_ok_javascript; ?> - <?php echo $site['titre_site']; ?>";</script>
<?php
			if (defined('IN_SITE'))
			{
?>
<p align="center"> <a href="index.php?page=fichemb">Retour &agrave; la 
  liste des membres</a> - <a href="index.php?page=modifmembre">Modifier 
  une fiche membre</a></p>
<?php
			}
?>
<fieldset>
<legend>Donn&eacute;es personnelles 
<?php
			if (defined('IN_SITE') and (($user['niveau']['numniveau'] == 3 and $user['numsection'] == $membre['section']) or $user['niveau']['numniveau'] > 3))
			{
?>
<a href="index.php?page=modifmembre&amp;nummb=<?php echo $membre['nummb']; ?>" title="Modifier sa fiche personnelle"> 
<img src="templates/default/images/fichemb.png" alt="Modifier sa fiche" width="18" height="12" border="0" align="middle" /></a>
<?php
			}
?></legend>
<?php
	  		if (!empty($membre['photo']))
			{ // le membre a une photo
				echo '<img src="'.$membre['photo'].'" alt="" class="photo_membre" align="right" />';
			} 
	  		else 
			{ // le membre n'a pas de photo, on affiche un lien vers le form d'ajout
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section']) ? '<a href="index.php?page=upload_photomembre&amp;nummb='.$nummb.'" title="Télécharger sa photo sur le portail">' : '';
?>
  <img src="templates/default/images/pasdephoto.gif" alt="" class="photo_membre" width="75" height="100" border="0" align="right" /> 
<?php 
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section']) ? '</a>' : '';
			}
?>
<table border="0" cellpadding="2" cellspacing="1">
<?php 
			if (!empty($nomsection))
			{
				if (!is_unite($membre['section']))
				{
?>
<tr>
  <td valign="top" class="rmqbleu">Unit&eacute;</td>
  <td valign="top"><?php echo $sections[$sections[$membre['section']]['unite']]['nomsection']; ?></td>
</tr>
<tr>
  <td valign="top" class="rmqbleu">Section</td>
  <td valign="top"><?php echo $nomsection; ?></td>
</tr>
<?php
				}
				else
				{
?>
<tr>
  <td valign="top" class="rmqbleu">Unit&eacute;</td>
  <td valign="top"><?php echo $sections[$membre['section']]['nomsection']; ?></td>
</tr>
<?php
				}
			}
			if (!empty($membre['totem']) or !empty($membre['quali']))
			{ 
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem</td>
  <td valign="top"><?php echo $membre['totem'].' '.$membre['quali']; ?></td>
</tr>
        <?php
			}
			if (!empty($membre['totem_jungle']))
			{
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem 
	jungle </td>
  <td valign="top"><?php echo $membre['totem_jungle']; ?></td>
</tr>
        <?php
			}
			if (!empty($nomfonction))
			{
?>
<tr> 
  <td valign="top" class="rmqbleu">Fonction</td>
  <td valign="top"><?php echo $nomfonction; ?></td>
</tr>
        <?php
			}
			if (strlen($info_siz) > 1)
			{
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $f_sizaine[$sections[$membre['section']]['sizaines']]; ?></td>
  <td valign="top"><?php echo $info_siz; ?></td>
</tr>
        <?php
			}
			$champs = array('telperso', 'email_mb', 'ddn', 'age', 'rmq_mb', 'sexe', 'actif', 'siteweb', 'rmq');
			$txt_champs = array('Tel. perso', 'Email perso', 'DDN', 'Age', 'Remarques', 'Sexe', 'En attente', 'Site web', 'Remarques');
			$nbre_champs = count($champs) - 1;
			$membre['actif'] = ($membre['actif'] == 1) ? '' : '<span class="rmq">Sur liste d\'attente</span>';
			if ($membre['ddn'] != '0000-00-00')
			{
				$membre['age'] = age($membre['ddn'], 1);
				$membre['ddn'] = date_ymd_dmy($membre['ddn'], 'enchiffres');
			} 
			else
			{
				$membre['ddn'] = $membre['age'] = '';
			}
			for ($i = 0; $i <= $nbre_champs; $i++)
			{
				if (!empty($membre[$champs[$i]]))
				{
					if ($champs[$i] == 'siteweb') 
					{
					  // on ajoute le http à l'url du site perso au cas où
					  $membre[$champs[$i]] = (!empty($membre[$champs[$i]]) and !ereg('^http://', $membre[$champs[$i]])) ? 'http://'.$membre[$champs[$i]] : $membre[$champs[$i]];
					  // on affiche le lien s'il n'est pas vide
					  $membre[$champs[$i]] = (!empty($membre[$champs[$i]])) ? ' <a href="'.$membre[$champs[$i]].'" title="Voir son site web" target="_blank">'.$membre[$champs[$i]].'</a>' : '';
					}
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
  <td valign="top"><?php echo ($champs[$i] == 'email_mb') ? hidemail($membre[$champs[$i]], 'lien') : makehtml($membre[$champs[$i]], 'html'); ?></td>
</tr>
<?php
				}
			}
?>
<tr> 
  <td valign="top" class="rmqbleu">Cotisation</td>
  <td> 
            <?php if ($membre['cotisation'] == 1) { ?>
	<span title="en ordre de cotisation"><font color="#009900">pay&eacute;e</font></span> 
            <?php } else if ($membre['cotisation'] == 0) { ?>
	<span title="La cotisation n'est pas encore payée"><font color="#FF0000">pas 
	pay&eacute;e</font></span> 
            <?php } else { ?>
	<span title="Etat de la cotisation inconnu"><font color="#0000FF">inconnu</font></span> 
            <?php } ?>
  </td>
</tr>
</table>
</fieldset>
<fieldset>
<?php
			// On affiche l'adresse du membre
?>
<legend>Adresse <a href="index.php?page=fichefamille&amp;numfamille=<?php echo $membre['famille']; ?>" title="Voir la fiche de la famille"><img src="templates/default/images/famille.png" alt="fiche famille" border="0" align="middle" /></a> 
  <a href="index.php?page=modiffamille&amp;numfamille=<?php echo $membre['famille']; ?>" title="Modifier la fiche de la famille"><img src="templates/default/images/fichefamille.png" alt="modifier fiche famille" border="0" align="middle" /></a></legend>
<table border="0" cellpadding="2" cellspacing="1">
<tr> 
  <td height="22" valign="top" class="rmqbleu">Adresse</td>
  <td valign="top" align="center"> 
<?php
			if (!empty($membre['rue']) and !empty($membre['numero'])) 
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
			echo $membre['rue'].$virgule.$membre['numero'].$slash.$membre['bte'].'<br />'.$membre['cp'].' '.$membre['ville']; 
?>
  </td>
</tr>
              <?php
				$champs = array('nom_pere', 'nom_mere', 'profession_pere', 'profession_mere', 'tel1', 'tel2', 'tel3', 'tel4', 'email', 'email2', 'rmq');
				$txt_champs = array('Nom du p&egrave;re', 'Nom de la m&egrave;re', 'Profession du p&egrave;re', 'Profession de la m&egrave;re', 'Tel. 1', 'Tel. 2', 'Tel. 3', 'Tel. 4', 'Email', 'Email 2', 'Remarques');
			$nbre_champs = count($champs) - 1;
			for ($i = 0; $i <= $nbre_champs; $i++)
			{
				if (!empty($membre[$champs[$i]]))
				{
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
  <td valign="top"><?php echo ($champs[$i] == 'email' or $champs[$i] == 'email2') ? hidemail($membre[$champs[$i]], 'lien') : makehtml($membre[$champs[$i]]); ?></td>
</tr>
<?php
				}
			}
?>
</table>
</fieldset>
<?php
			// On affiche éventuellement la deuxième adresse
			if ($membre['famille2'] != 0)
			{
				$numfamille2 = $membre['famille2'];
				$sql = 'SELECT * FROM '.PREFIXE_TABLES.'mb_adresses WHERE numfamille = \''.$numfamille2.'\'';
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 1)
				{
					$adresse2 = mysql_fetch_assoc($res);
				}
?>
<fieldset>
<legend>Deuxi&egrave;me adresse : Famille <?php echo $adresse2['nom']; ?>
<a href="index.php?page=fichefamille&amp;numfamille=<?php echo $membre['famille2']; ?>" title="Voir la fiche de la famille"><img src="templates/default/images/famille.png" alt="fiche famille" width="18" height="12" border="0" /></a> 
<a href="index.php?page=modiffamille&amp;numfamille=<?php echo $membre['famille2']; ?>" title="Modifier la fiche de la famille"><img src="templates/default/images/fichefamille.png" alt="modifier fiche famille" width="18" height="12" border="0" /></a></legend>
<table border="0" cellpadding="2" cellspacing="1">
  <tr> 
	<td height="22" valign="top" class="rmqbleu">Adresse</td>
	<td valign="top" align="center"> 
<?php
				if (!empty($adresse2['rue']) and !empty($adresse2['numero'])) 
				{
					$virgule = ', ';
					if (!empty($adresse2['bte']))
					{
						$slash = ' bte ';
					}
				} 
				else 
				{
					$virgule = '';
					$slash = '';
				} 
				echo $adresse2['rue'].$virgule.$adresse2['numero'].$slash.$adresse2['bte'].'<br />'.$adresse2['cp'].' '.$adresse2['ville']; 
?>
  </td>
</tr>
<?php
				$champs = array('nom_pere', 'nom_mere', 'profession_pere', 'profession_mere', 'tel1', 'tel2', 'tel3', 'tel4', 'email', 'email2', 'rmq');
				$txt_champs = array('Nom du p&egrave;re', 'Nom de la m&egrave;re', 'Profession du p&egrave;re', 'Profession de la m&egrave;re', 'Tel. 1', 'Tel. 2', 'Tel. 3', 'Tel. 4', 'Email', 'Email 2', 'Remarques');
				$nbre_champs = count($champs) - 1;
				for ($i = 0; $i <= $nbre_champs; $i++)
				{
					if (!empty($adresse2[$champs[$i]]))
					{
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
  <td valign="top"><?php echo ($champs[$i] == 'email' or $champs[$i] == 'email2') ? hidemail($adresse2[$champs[$i]], 'lien') : makehtml($adresse2[$champs[$i]]); ?></td>
</tr>
<?php
					}
				}
?>
</table>
</fieldset>
<?php
			} // fin famille2 != 0
?>
<fieldset>
<legend>Infos Fiche membre</legend>
<p>Fiche cr&eacute;&eacute;e le <?php echo date_ymd_dmy($membre['mb_datecreation'], 'enlettres').' par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $membre['mb_createur']); ?>
<?php
			if ($membre['mb_datecreation'] != $membre['mb_lastmodif'])
			{
?>
<br />Derni&egrave;re modification le <?php echo date_ymd_dmy($membre['mb_lastmodif'], 'enlettres').' par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $membre['mb_lastmodifby']); ?>
<?php
			}
?></p>
<p class="petitbleu">Les infos relatives aux modifications des données des familles sont disponibles 
  sur leur fiche respective.</p> 
</fieldset>
  <?php
		} // fin if num row == 1
		else
		{
?>
<h1>Afficher les infos d'un membre de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
  page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<p class="rmq" align="center">D&eacute;sol&eacute;, Aucun membre actif de l'Unit&eacute; 
  ne correspond &agrave; cette requ&ecirc;te !</p>
</div>
<?php
		}
?>
</div>
<?php
	}
	else // si nummb est vide
	{
?>
<h1>Afficher les infos d'un membre de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page 
  Gestion de l'Unit&eacute;</a></p>
<?php
		$autrecritere = '';
		foreach ($sections as $section)
		{
			if ($section['anciens'] == 1)
			{
				$autrecritere .= 'AND section != \''.$section['numsection'].'\' ';
			}
		}
		$sql = 'SELECT nummb, nom_mb, prenom, rue, numero, section FROM '.PREFIXE_TABLES.'mb_membres as a, '.PREFIXE_TABLES.'mb_adresses as b WHERE a.famille = b.numfamille '.$autrecritere.' ORDER BY nom_mb, prenom ASC';
		if ($res = send_sql($db, $sql))
		{
			$nbre_membres = mysql_num_rows($res);
		}
		if ($nbre_membres > 0)
		{
?>
<script language="JavaScript" type="text/JavaScript">
function ok()
{
	if (document.form.nummb.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de bien vouloir choisir un membre avant d'envoyer le formulaire.");
		return false;
	}
}
</script>
<form action="index.php" method="get" name="form" id="form" onsubmit="return ok()" class="form_gestion_unite">
  <input type="hidden" name="page" value="fichemb" />
<h2>Choix du membre</h2>
<p>Choisis parmi les membres de l'Unit&eacute; pr&eacute;sents dans la base : 
<?php if ($nbre_membres > 0) { $pl = ($nbre_membres > 1) ? 's' : ''; echo '('.$nbre_membres.' membre'.$pl.' trouv&eacute;'.$pl.')'; } ?></p>
<p align="center">
  <select name="nummb" size="15">
<?php
			while ($membre = mysql_fetch_assoc($res))
			{
?>
<option value="<?php echo $membre[nummb]; ?>">
<?php $bte = (!empty($membre['bte'])) ? '/' : ''; echo $membre['nom_mb'].' '.$membre['prenom'].' ('.$membre['rue'].', '.$membre['numero'].$bte.$membre['bte'].')'; if ($membre['section'] != 0) {echo ' - '.$sections[$membre['section']]['nomsection'];} ?>
</option>
<?php
			}
?>
  </select>
</p>
<p align="center"> 
  <input type="submit" value="Voir sa fiche" />
</p>
</form>
<div class="instructions">
<h2>Astuce</h2>
<p>Pour retrouver plus rapidement un membre, clique dans la liste et tape la premi&egrave;re 
  lettre de son nom pour t'en approcher.</p>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucun membre dans la base pour le moment.</p>
</div>
<?php
		}
	}
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
}
?>