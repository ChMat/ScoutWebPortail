<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fichefamille.php v 1.1 - Fiche d'une famille de l'Unité
* Chaque famille peut contenir plusieurs membres liés à cette famille
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
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
	if (is_numeric($_GET['numfamille']))
	{
		$numfamille = $_GET['numfamille'];
	}
	else if (is_numeric($_POST['numfamille']))
	{
		$numfamille = $_POST['numfamille'];
	}
	if (!empty($numfamille))
	{
		$sql = 'SELECT *, concat(rue, \', \', numero, IF(bte <> \'\', concat(\' bte \', bte), \'\')) as adresse FROM '.PREFIXE_TABLES.'mb_adresses WHERE numfamille = \''.$numfamille.'\'';
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$famille = mysql_fetch_assoc($res);
			if (!defined('IN_SITE'))
			{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Fiche d'une famille</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
			}
?>
<div id="gestion_u_f_famille">
<h1>Famille <?php echo $famille['nom']; ?></h1>
<?php
			if (defined('IN_SITE'))
			{
?>
<p align="center"> <a href="index.php?page=fichefamille">Retour &agrave; 
  la liste des familles</a> - <a href="index.php?page=modiffamille">Modifier 
  une fiche famille</a></p>
<?php
			}
			// On affiche les infos de la famille
?>
<fieldset>
<legend>Donn&eacute;es de la famille 
  <a href="index.php?page=newmb&amp;numfamille=<?php echo $famille['numfamille']; ?>&amp;step=2" title="Ajouter un membre à cette famille"><img src="templates/default/images/newmb.png" alt="ajouter membre" width="18" height="12" border="0" /></a> 
  <a href="index.php?page=modiffamille&amp;numfamille=<?php echo $famille['numfamille']; ?>" title="Modifier les informations de la famille"><img src="templates/default/images/fichefamille.png" alt="modifier fiche famille" width="18" height="12" border="0" /></a></legend>
<table border="0" cellpadding="2" cellspacing="1">
  <tr> 
	<td valign="top" class="rmqbleu">Adresse</td>
	<td valign="top" align="center"> 
<?php
			echo $famille['adresse'].'<br />'.$famille['cp'].' '.$famille['ville']; 
?>
	</td>
  </tr>
<?php
			$champs = array('nom_pere', 'nom_mere', 'profession_pere', 'profession_mere', 'tel1', 'tel2', 'tel3', 'tel4', 'email', 'email2', 'rmq');
			$txt_champs = array('Nom du p&egrave;re', 'Nom de la m&egrave;re', 'Profession du p&egrave;re', 'Profession de la m&egrave;re', 'Tel. 1', 'Tel. 2', 'Tel. 3', 'Tel. 4', 'Email', 'Email 2', 'Remarques');
			$nbre_champs = count($champs) - 1;
			for ($i = 0; $i <= $nbre_champs; $i++)
			{
				if (!empty($famille[$champs[$i]]))
				{
?>
  <tr> 
	<td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
	<td valign="top"><?php echo ($champs[$i] == 'email' or $champs[$i] == 'email2') ? hidemail($famille[$champs[$i]], 'lien') : makehtml($famille[$champs[$i]]); ?></td>
  </tr>
<?php
				}
			}
?>
</table>
</fieldset>
<?php
			// On affiche les membres de la famille
			$sql = 'SELECT * FROM '.PREFIXE_TABLES.'mb_membres WHERE famille = \''.$numfamille.'\' or famille2 = \''.$numfamille.'\' ORDER BY nom_mb, prenom ASC';
			if ($res = send_sql($db, $sql))
			{
				$nbre_p = mysql_num_rows($res);
				if ($nbre_p > 0)
				{
					if ($nbre_p > 1) {$pluriel_mb = 's';} else {$pluriel_mb = '';}
?>
<div class="msg">
<p align="center">Cette famille compte <?php echo $nbre_p.' membre'.$pluriel_mb.' inscrit'.$pluriel_mb;?> chez nous :</p>
</div>
<?php
					while ($unmembre = mysql_fetch_assoc($res))
					{
						if ($sections[$unmembre['section']]['anciens'] == 0)
						{ // le membre est actif dans l'unité (il n'est pas dans une section anciens)
							$nomsection = $sections[$unmembre['section']]['nomsection'];
							$nomfonction = $fonctions[$unmembre['fonction']]['nomfonction'];
							$nomsizaine = $sizaines[$unmembre['siz_pat']]['nomsizaine'];
							$statut = $statuts[$unmembre['cp_sizenier']];
							$info_siz = $statut.' '.$nomsizaine; 
?>
<fieldset>
<legend class="rmqbleu"><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?> 
<a href="index.php?page=fichemb&nummb=<?php echo $unmembre['nummb']; ?>" title="Voir sa fiche"><img src="templates/default/images/membre.png" border="0" alt="Voir sa fiche" /></a> 
<?php
							if (($user['niveau']['numniveau'] == 3 and $user['numsection'] == $unmembre['section']) or $user['niveau']['numniveau'] > 3)
							{
?>
<a href="index.php?page=modifmembre&nummb=<?php echo $unmembre['nummb']; ?>" title="Modifier sa fiche"><img src="templates/default/images/fichemb.png" border="0" alt="Modifier sa fiche" /></a> 
<?php
							}
?></legend>
<?php
	  		if (!empty($unmembre['photo']))
			{ // le membre a une photo
				echo '<img src="'.$unmembre['photo'].'" alt="" class="photo_membre" align="right" />';
			} 
	  		else 
			{ // le membre n'a pas de photo, on affiche un lien vers le form d'ajout
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $unmembre['section']) ? '<a href="index.php?page=upload_photomembre&amp;nummb='.$unmembre['nummb'].'" title="Télécharger sa photo sur le portail">' : '';
?>
  <img src="templates/default/images/pasdephoto.gif" alt="" class="photo_membre" width="75" height="100" border="0" align="right" /> 
<?php 
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $unmembre['section']) ? '</a>' : '';
			}
?>
<table border="0" cellpadding="2" cellspacing="1">
<?php
							if (!empty($unmembre['totem']) or !empty($unmembre['quali']))
							{ 
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem</td>
  <td valign="top"><?php echo $unmembre['totem'].' '.$unmembre['quali']; ?></td>
</tr>
<?php
							}
							if (!empty($unmembre['totem_jungle']))
							{
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem de jungle</td>
  <td valign="top"><?php echo $unmembre['totem_jungle']; ?></td>
</tr>
<?php
							}
							if (!empty($nomsection))
							{
								if (!is_unite($unmembre['section']))
								{
?>
<tr> 
  <td valign="top" class="rmqbleu">Unit&eacute;</td>
  <td valign="top"><?php echo $sections[$sections[$unmembre['section']]['unite']]['nomsection']; ?></td>
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
  <td valign="top"><?php echo $sections[$unmembre['section']]['nomsection']; ?></td>
</tr>
<?php
								}
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
  <td valign="top" class="rmqbleu"><?php echo $f_sizaine[$sections[$unmembre['section']]['sizaines']]; ?></td>
  <td valign="top"><?php echo $info_siz; ?></td>
</tr>
<?php
							}
							$champs = array('telperso', 'email_mb', 'rmq_mb', 'ddn', 'age', 'rmq', 'sexe', 'actif');
							$txt_champs = array('Tel. perso', 'Email perso', 'Remarques', 'DDN', 'Age', 'Remarques', 'Sexe', 'En attente');
							$nbre_champs = count($champs) - 1;
							$unmembre['actif'] = ($unmembre['actif'] == 1) ? '' : '<span class="rmq">Sur liste d\'attente</span>';
							if ($unmembre['ddn'] != '0000-00-00')
							{
								$unmembre['age'] = age($unmembre['ddn'], 1);
								$unmembre['ddn'] = date_ymd_dmy($unmembre['ddn'], 'enchiffres');
							} 
							else
							{
								$unmembre['ddn'] = $unmembre['age'] = '';
							}
							for ($i = 0; $i <= $nbre_champs; $i++)
							{
								if (!empty($unmembre[$champs[$i]]))
								{
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
  <td valign="top"><?php echo ($champs[$i] == 'email_mb') ? hidemail($unmembre[$champs[$i]], 'lien') : makehtml($unmembre[$champs[$i]], 'html'); ?></td>
</tr>
<?php
								}
							}
?>
<tr> 
  <td valign="top" class="rmqbleu">Cotisation</td>
  <td> 
<?php if ($unmembre['cotisation'] == 1) { ?>
<span title="en ordre de cotisation"><font color="#009900">pay&eacute;e</font></span> 
<?php } else if ($unmembre['cotisation'] == 0) { ?>
<span title="La cotisation n'est pas encore payée"><font color="#FF0000">pas 
pay&eacute;e</font></span> 
<?php } else { ?>
<span title="Etat de la cotisation inconnu"><font color="#0000FF">inconnu</font></span> 
<?php } ?>
  </td>
</tr>
<?php 
							if ($unmembre['famille2'] != 0) 
							{
?>
<tr> 
  <td height="18" valign="top" class="rmqbleu"><strong>Autre 
	Adresse</strong></td>
  <td> 
<?php
								if ($unmembre['famille2'] == $numfamille)
								{
?>
<a href="index.php?page=fichefamille&amp;numfamille=<?php echo $unmembre['famille']; ?>" class="menumembres">Voir son adresse principale</a>
<?php
								}
								else
								{
?>
<a href="index.php?page=fichefamille&amp;numfamille=<?php echo $unmembre['famille2']; ?>" class="menumembres">Voir sa deuxi&egrave;me adresse</a>
<?php
								}
?>
  </td>
</tr>
<?php
							}
?>
</table>
</fieldset>
<?php
						}
						else
						{ // Le membre est un ancien de l'unité
							$nomsection = $sections[$unmembre['section']]['nomsection'];
?>
<fieldset class="ancien">
<legend><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?> 
 <a href="index.php?page=ficheancien&nummb=<?php echo $unmembre['nummb']; ?>"><img src="templates/default/images/membre.png" border="0" alt="Voir sa fiche" /></a> 
 <a href="index.php?page=modifancien&nummb=<?php echo $unmembre['nummb']; ?>" title="Modifier sa fiche"><img src="templates/default/images/fichemb.png" border="0" alt="Modifier sa fiche" /></a></legend>
<?php
	  		if (!empty($unmembre['photo']))
			{ // le membre a une photo
				echo '<img src="'.$unmembre['photo'].'" alt="" class="photo_membre" align="right" />';
			} 
	  		else 
			{ // le membre n'a pas de photo, on affiche un lien vers le form d'ajout
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $unmembre['section']) ? '<a href="index.php?page=upload_photomembre&amp;nummb='.$unmembre['nummb'].'" title="Télécharger sa photo sur le portail">' : '';
?>
  <img src="templates/default/images/pasdephoto.gif" alt="" class="photo_membre" width="75" height="100" border="0" align="right" /> 
<?php 
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $unmembre['section']) ? '</a>' : '';
			}
?>
<table border="0" cellpadding="2" cellspacing="1">
<?php 
							if (!empty($unmembre['totem']) or !empty($unmembre['quali']))
							{ 
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem</td>
  <td valign="top"><?php echo $unmembre['totem'].' '.$unmembre['quali']; ?></td>
</tr>
<?php
							}
							if (!empty($unmembre['totem_jungle']))
							{
?>
<tr> 
  <td valign="top" class="rmqbleu">Totem 
	jungle </td>
  <td valign="top"><?php echo $unmembre['totem_jungle']; ?></td>
</tr>
<?php
							}
							if (!empty($nomsection))
							{
?>
<tr> 
  <td valign="top" class="rmqbleu">Unit&eacute;</td>
  <td valign="top"><?php echo $sections[$sections[$unmembre['section']]['unite']]['nomsection']; ?></td>
</tr>
<tr> 
  <td valign="top" class="rmqbleu">Section</td>
  <td valign="top" class="rmqbleu"><?php echo $nomsection; ?></td>
</tr>
<?php
							}
							$champs = array('telperso', 'email_mb', 'rmq_mb', 'ddn', 'age', 'rmq', 'sexe');
							$txt_champs = array('Tel. perso', 'Email perso', 'Remarques', 'DDN', 'Age', 'Remarques', 'Sexe');
							$nbre_champs = count($champs) - 1;
							if ($unmembre['ddn'] != '0000-00-00')
							{
								$unmembre['age'] = age($unmembre['ddn'], 1);
								$unmembre['ddn'] = date_ymd_dmy($unmembre['ddn'], 'enchiffres');
							} 
							else
							{
								$unmembre['ddn'] = $unmembre['age'] = '';
							}
							for ($i = 0; $i <= $nbre_champs; $i++)
							{
								if (!empty($unmembre[$champs[$i]]))
								{
?>
<tr> 
  <td valign="top" class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
  <td valign="top"><?php echo ($champs[$i] == 'email_mb') ? hidemail($unmembre[$champs[$i]], 'lien') : makehtml($unmembre[$champs[$i]]); ?></td>
</tr>
<?php
								}
							}
?>
</table>
</fieldset>
<?php
						}
					}
				}
			}
?>
<fieldset>
<legend>Infos Fiche famille</legend>
<p>Fiche famille cr&eacute;&eacute;e le <?php echo date_ymd_dmy($famille['ad_datecreation'], 'enchiffres').' (par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $famille['ad_createur']).')'; ?>
<?php
			if ($famille['ad_datecreation'] != $famille['ad_lastmodif'])
			{
?><br />
Derni&egrave;re modification le <?php echo date_ymd_dmy($famille['ad_lastmodif'], 'enchiffres').' (par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $famille['ad_lastmodifby']).')'; ?></p>
<?php
			}
?>
<p class="petitbleu">Les infos relatives au mises à jour des fiches des membres sont disponibles sur 
leur fiche personnelle.</p>
</fieldset>
<?php
		}
?>
</div>
<?php
	} // fin de l'affichage de la fiche famille
	else
	{
?>
<h1>Afficher les infos d'une famille</h1>
	
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion 
  de l'Unit&eacute;</a></p>
<?php
		$sql = 'SELECT numfamille, nom, rue, numero FROM '.PREFIXE_TABLES.'mb_adresses ORDER BY nom ASC';
		if ($res = send_sql($db, $sql))
		{
			$nbre_familles = mysql_num_rows($res);
		}
		if ($nbre_familles > 0)
		{
?>
<script language="JavaScript" type="text/JavaScript">
function ok()
{
	if (document.form.numfamille.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de bien vouloir choisir une famille avant d'envoyer le formulaire.");
		return false;
	}
}
</script>
<form action="index.php" method="get" name="form" id="form" onsubmit="return ok()" class="form_gestion_unite">
  <input type="hidden" name="page" value="fichefamille" />
<h2>Choix de la famille</h2>
<p>Choisis parmi les familles d&eacute;j&agrave; pr&eacute;sentes dans la base : 
<?php 		
		if ($nbre_familles > 0) { if ($nbre_familles > 1) {$pl = 's';} else {$pl = '';} echo '('.$nbre_familles.' famille'.$pl.' trouv&eacute;e'.$pl.')'; } 
?></p>
<p align="center"><select name="numfamille" size="15">
<?php
			while ($famille = mysql_fetch_assoc($res))
			{
?>
	<option value="<?php echo $famille['numfamille']; ?>"> 
	<?php $bte = ''; $bte = (!empty($famille['bte'])) ? '/' : ''; echo $famille['nom'].' ('.$famille['rue'].', '.$famille['numero'].$bte.$famille['bte'].')'; ?>
	</option>
<?php
			}
?>
  </select>
</p>
<p align="center">
  <input type="submit" value="Voir la fiche" />
</p>
</form>
<div class="instructions">
<h2>Astuce</h2>
<p>Pour retrouver plus rapidement une famille, clique dans la liste et tape la premi&egrave;re 
  lettre du son nom pour t'en approcher.</p>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucune famille dans la base pour le moment.</p>
</div>
<?php
		}
		if (!defined('IN_SITE'))
		{
?>
</body>
</html>
<?php
		}
	}
}
?>