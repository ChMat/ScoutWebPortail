<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* ficheancien.php v 1.1 - Fiche d'un ancien de l'unité
* Les anciens de l'unité proviennent de la Gestion de l'Unité
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
		$autrecritere = '';
		foreach ($sections as $section)
		{
			if ($section['anciens'] == 0)
			{
				$autrecritere .= 'AND section != \''.$section['numsection'].'\'';
			}
		}
		$sql = 'SELECT * FROM '.PREFIXE_TABLES.'mb_membres as a, '.PREFIXE_TABLES.'mb_adresses as b WHERE a.nummb = \''.$nummb.'\' AND a.famille = b.numfamille '.$autrecritere;
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
			$nomsection = $sections[$membre['section']]['nomsection'];
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
<p align="center"> <a href="index.php?page=ficheancien">Retour &agrave; la 
  liste des Anciens</a></p>
<script language="JavaScript" type="text/JavaScript">document.title = "<?php echo $mb_ok_javascript; ?> - <?php echo $site['titre_site']; ?>";</script>
<fieldset class="ancien">
<legend>Donn&eacute;es personnelles <a href="index.php?page=modifancien&nummb=<?php echo $membre['nummb']; ?>" class="menumembres rmqbleu" title="Modifier sa fiche personnelle"><img src="templates/default/images/fichemb.png" alt="" border="0" /></a></legend>
<?php 
	  		if (!empty($membre['photo'])) 
			{
				echo '<img src="'.$membre['photo'].'" class="photo_membre" alt="" align="right" />';
			}
	  		else
			{
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section']) ? '<a href="index.php?page=upload_photomembre&amp;r=a&amp;nummb='.$nummb.'" title="T&eacute;l&eacute;charger sa photo sur le portail">' : '';
?>
      <img src="templates/default/images/pasdephoto.gif" class="photo_membre" alt="" width="75" height="100" border="0" /> 
<?php 
				echo ($user['niveau']['numniveau'] > 3 or $user['numsection'] == $membre['section']) ? '</a>' : '';
			}
?>
	<table border="0" align="left" cellpadding="2" cellspacing="1">
<?php 
			if (!empty($nomsection))
			{
?>
        <tr> 
          <td valign="top" class="rmqbleu"><span class="rmqbleu">Unit&eacute;</span></td>
          <td valign="top"><?php echo $sections[$sections[$membre['section']]['unite']]['nomsection']; ?></td>
        </tr>
        <tr> 
          <td valign="top" class="rmqbleu">Section</td>
          <td valign="top"><?php echo $nomsection; ?></td>
        </tr>
        <?php
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
			$champs = array('telperso', 'email_mb', 'ddn', 'age', 'rmq', 'sexe', 'siteweb', 'rmq_mb');
			$txt_champs = array('Tel. perso', 'Email perso', 'DDN', 'Age', 'Remarques', 'Sexe', 'Site web', 'Remarques');
			$nbre_champs = count($champs) - 1;
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
          <td valign="top"><?php echo ($champs[$i] == 'email_mb') ? hidemail($membre['email_mb'], 'lien') : makehtml($membre[$champs[$i]], 'html'); ?></td>
        </tr>
        <?php
				}
			}
?>
  </table>
</fieldset>
<fieldset>
<legend>Adresse <a href="index.php?page=fichefamille&numfamille=<?php echo $membre['famille']; ?>" class="menumembres rmqbleu"><img src="templates/default/images/famille.png" alt="" border="0" /></a> 
  <a href="index.php?page=modiffamille&numfamille=<?php echo $membre['famille']; ?>" class="menumembres rmqbleu" title="Modifier la fiche de la famille"><img src="templates/default/images/fichefamille.png" alt="" border="0" /></a></legend>
		  <table border="0" align="left" cellpadding="2" cellspacing="1">
              <tr> 
                <td valign="top" class="rmqbleu">Adresse</td>
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
			$champs = array('tel1', 'tel2', 'tel3', 'tel4', 'email', 'rmq');
			$txt_champs = array('Tel. 1', 'Tel. 2', 'Tel. 3', 'Tel. 4', 'Email', 'Remarques');
			$nbre_champs = count($champs) - 1;
			for ($i = 0; $i <= $nbre_champs; $i++)
			{
				if (!empty($membre[$champs[$i]]))
				{
?>
              <tr> 
                <td class="rmqbleu"><?php echo $txt_champs[$i]; ?></td>
                <td valign="top"><?php echo ($champs[$i] == 'email') ? hidemail($membre['email'], 'lien') : makehtml($membre[$champs[$i]], 'html'); ?></td>
              </tr>
              <?php
				}
			}
?>
  </table>
</fieldset>
<fieldset>
<legend>Infos fiche ancien</legend>
<p>Fiche cr&eacute;&eacute;e le <?php echo date_ymd_dmy($membre['mb_datecreation'], 'enlettres').' par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $membre['mb_createur']); ?></p>
<?php
			if ($membre['mb_datecreation'] != $membre['mb_lastmodif'])
			{
?>
<p>Derni&egrave;re modification le <?php echo date_ymd_dmy($membre['mb_lastmodif'], 'enlettres').' par '.untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $membre['mb_lastmodifby']); ?></p>
<?php
			}
?>
<p class="petitbleu">Les infos relatives aux modifications des données des familles sont disponibles 
  sur leur fiche respective. </p>
</fieldset>
</div>
<?php
		} // fin if num row == 1
		else
		{
?>
<h1>Afficher les infos d'un ancien de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour à la page 
  Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<p align="center" class="rmq">Aucun ancien ne correspond &agrave; cette requ&ecirc;te.</p>
</div>
<?php
		}
	}
	else // si nummb est vide
	{
?>
<h1>Afficher les infos d'un ancien de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour à la page 
  Gestion de l'Unit&eacute;</a></p>
<?php
		$autrecritere = '';
		foreach ($sections as $section)
		{
			if ($section['anciens'] == 0)
			{
				$autrecritere .= 'AND section != \''.$section['numsection'].'\'';
			}
		}
		$sql = 'SELECT nummb, nom_mb, prenom, rue, numero, section FROM '.PREFIXE_TABLES.'mb_membres as a, '.PREFIXE_TABLES.'mb_adresses as b WHERE a.famille = b.numfamille '.$autrecritere.' ORDER BY nom_mb, prenom ASC';
		if ($res = send_sql($db, $sql))
		{
			$nbre_membres = mysql_num_rows($res);
		}
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
<h2>Choix de l'ancien</h2>
  <input type="hidden" name="page" value="ficheancien" />
<p>Choisis parmi les Anciens de l'Unit&eacute; pr&eacute;sents dans la base : 
<?php if ($nbre_membres > 0) { $pl = ($nbre_membres > 1) ? 's' : ''; echo '('.$nbre_membres.' ancien'.$pl.' trouv&eacute;'.$pl.')'; } ?></p>
<?php
			if ($nbre_membres > 0)
			{
?>
<p align="center"> 
  <select name="nummb" size="15">
	<?php
				while ($membre = mysql_fetch_assoc($res))
				{
?>
	<option value="<?php echo $membre['nummb']; ?>"><?php echo $membre['nom_mb'].' '.$membre['prenom']; ?></option>
<?php
				}
?>
  </select>
</p>
<p align="center">
<input type="submit" value="Voir sa fiche" />
</p>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucun ancien dans la base pour le moment.</p>
</div>
<?php
			}
?>
</form>
<div class="instructions">
<h2>Astuce</h2>
<p>Pour retrouver plus rapidement un ancien, clique dans la liste et tape la premi&egrave;re 
  lettre de son nom pour t'en approcher.</p>
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
?>