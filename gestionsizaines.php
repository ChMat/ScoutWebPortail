<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestionsizaines.php - Gestion de la composition des Sizaines d'une section
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
if ($user['niveau']['numniveau'] == 3)
{
	if ($_GET['do'] != 'updatesizaines' and $_POST['do'] != 'updatesizaines')
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion de la composition des Sizaines et Patrouilles</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Composition des <?php echo $t_sizaines; ?> de ma Section</h1>
<div align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></div>
<?php
	}
	$nbre_sizaines = 0;
	if (is_array($sizaines))
	{
		foreach($sizaines as $sizaine)
		{
			if ($sizaine['section_sizpat'] == $user['numsection'])
			{
				$nbre_sizaines++;
			}
		}
	}
	if (empty($_GET['do']) and empty($_POST['do']) and $nbre_sizaines > 0)
	{
		if (!empty($_GET['msg']) and is_numeric($_GET['nbre']))
		{
			$pl = ($_GET['nbre'] > 1) ? 's' : '';
			$nbre = ($_GET['nbre'] == 0) ? 'Aucune' : $_GET['nbre'];
			echo '<p class="rmqbleu" align="center">Mise &agrave; jour effectu&eacute;e : '.$nbre.' fiche'.$pl.' membre'.$pl.' modifi&eacute;e'.$pl.'.</p>';
		}
?>
<p>Cette fonction n'est accessible qu'aux membres du staff de la Section concern&eacute;e. 
  Elle leur est principalement destin&eacute;e.</p>
<form action="index.php" method="post" name="form" id="form" class="form_gestion_unite">
<h2>G&eacute;rer les <?php echo $t_sizaines; ?> de ma Section</h2>
  <input type="hidden" name="page" value="gestionsizaines" />
  <input type="hidden" name="do" value="affsizaines" />
<p>Trier les membres : <label for="tri1">
  <input type="radio" name="tri" id="tri1" value="age" checked="checked" />
   par l'&acirc;ge</label>
  <label for="tri2">
  <input type="radio" name="tri" id="tri2" value="nom" />
   par le nom</label>
  <label for="tri3">
  <input type="radio" name="tri" id="tri3" value="siz" />
   par la <?php echo $t_sizaine; ?> actuelle</label>
  <label for="tri4">
  <input type="radio" name="tri" id="tri4" value="tot" />
   par le totem</label>
</p>
<p>
  <label for="tot" title="Case non cochée = nom et prénom toujours affichés">
  <input type="checkbox" name="tot" value="1" id="tot" checked="checked" />
  Afficher le totem s'il existe</label></p>
  <p align="center"> 
    <input type="submit" value="Gérer la composition des <?php echo $t_sizaines; ?> de ma Section" />
  </p>
</form>
<form action="index.php" method="post" name="form1" id="form1" onsubmit="return confirm('Es-tu certain de vouloir réinitialiser les <?php echo $t_sizaines; ?> de ta Section ?')" class="form_gestion_unite">
<h2>Réinitialiser les <?php echo $t_sizaines; ?> de ma Section</h2>
<p>Cette fonction te permet de consid&eacute;rer qu'aucune 
<?php echo $t_sizaine; ?> de ta Section n'est compos&eacute;e. <span class="rmq">Attention, 
cette fonction est irr&eacute;versible ! Elle n'affecte que ta Section</span></p>
<input type="hidden" name="page" value="gestionsizaines" />
<input type="hidden" name="do" value="resetsizaines" />
<p align="center"> 
  <input type="submit" name="Button" value="R&eacute;initialiser les <?php echo $t_sizaines; ?>" />
</p>
</form>
<?php
	}
	else if ($_POST['do'] == 'affsizaines')
	{
?>
<form action="gestionsizaines.php" method="post" name="actualisersizaines" id="actualisersizaines" onsubmit="return confirm('Confirme tu les modifications effectuées ?');" class="form_gestion_unite">
<h2>Mettre &agrave; jour la composition des <?php echo $t_sizaines; ?> de ma Section.</h2>
<p><span class="rmq">Section en cours : </span><?php echo $sections[$user['numsection']]['nomsection']; ?></p>
<p>Ci-dessous, tu peux composer les <?php echo $t_sizaines; ?> de ta Section. Il 
  te suffit de s&eacute;lectionner les cases ad&eacute;quates et de valider le 
  formulaire.</p>
  <p class="petitbleu">Les membres sur la liste d'attente de la Section et 
  les animateurs ne sont pas affich&eacute;s.</p>
<?php
		if ($_POST['tri'] == 'age') 
		{
			$tri = 'ddn, ';
		}
		else if ($_POST['tri'] == 'siz')
		{
			$tri = 'siz_pat ASC, cp_sizenier DESC, ';
		}
		else if ($_POST['tri'] == 'tot')
		{
			$tri = 'totem, ';
		}
		else
		{
			$tri = '';
		}
		$sql = "SELECT nummb, prenom, nom_mb, totem, quali, ddn, siz_pat, cp_sizenier FROM ".PREFIXE_TABLES."mb_membres WHERE actif != '0' AND section = '$user[numsection]' AND fonction < 2 ORDER BY $tri nom_mb, prenom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbretrouve = mysql_num_rows($res);
			if ($nbretrouve > 0)
			{
?>
  <input type="hidden" name="nbre" value="<?php echo $nbretrouve; ?>" />
  <input type="hidden" name="do" value="updatesizaines" />
  <input type="hidden" name="section" value="<?php echo $user['numsection']; ?>" />
	<table cellpadding="2" cellspacing="0" align="center">
	  <tr>
		<th>Nom</th>
		<th>DDN</th>
		<th>Statut</th>
	  </tr>
<?php
				$j = 0;
				while ($membre = mysql_fetch_assoc($res))
				{
					$j++;
					$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
	  <tr>
		<td align="left" class="<?php echo $couleur; ?>">
		  <input type="hidden" name="num_<?php echo $j;?>" value="<?php echo $membre['nummb']; ?>" />
		  <input type="hidden" name="oldsiz_pat_<?php echo $membre['nummb']; ?>" value="<?php echo $membre['siz_pat']; ?>" />
		  <input type="hidden" name="oldcp_sizenier_<?php echo $membre['nummb']; ?>" value="<?php echo $membre['cp_sizenier']; ?>" />
		<span class="rmqbleu"><?php echo (!empty($membre['totem']) and $_POST['tot'] == 1) ? '<span title="'.$membre['nom_mb'].' '.$membre['prenom'].'">'.$membre['totem'].' '.$membre['quali'].'</span>' : $membre['nom_mb'].' '.$membre['prenom']; ?></span></td>
		<td class="<?php echo $couleur; ?>" align="center"><?php echo ($membre['ddn'] != '0000-00-00') ? date_ymd_dmy($membre['ddn'], 'enchiffres') : ''; ?></td>
		<td class="<?php echo $couleur; ?>">
		  <select name="cp_sizenier_<?php echo $membre['nummb']; ?>">
<?php
					foreach ($statuts as $key => $valeur)
					{
?>
			<option value="<?php echo $key; ?>"<?php echo ($membre['cp_sizenier'] == $key) ? ' selected' : ''; ?>><?php echo $valeur; ?></option>
<?php
					}
?>
		  </select>
		  <select name="siz_pat_<?php echo $membre['nummb']; ?>">
		  	<option value=""></option>
<?php
					foreach ($sizaines as $sizaine)
					{
						if ($sizaine['section_sizpat'] == $user['numsection'])
						{
?>
			<option value="<?php echo $sizaine['numsizaine']; ?>"<?php echo ($membre['siz_pat'] == $sizaine['numsizaine']) ? ' selected' : ''; ?>><?php echo $sizaine['nomsizaine']; ?></option>
<?php
						}
					}
?>
		  </select>
		</td>
	  </tr>
<?php
				}
?>
	</table>
	
<p align="center">
    <input type="submit" value="Enregistrer ces donn&eacute;es" />
  </p>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmqbleu">La section <?php echo $sections[$user['numsection']]['nomsection']; ?> ne contient aucun membre.</p>
</div>
<?php
			}
		}
?>
</form>
<?php
	}
	else if ($_POST['do'] == 'resetsizaines')
	{
		$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET cp_sizenier = '0', siz_pat = '0' WHERE section = '$user[numsection]'";
		log_this("Réinitialisation des $t_sizaines de la section ".addslashes($sections[$user['numsection']]['nomsection']), 'gestionsizaines');
		send_sql($db, $sql);
?>
<div class="msg">
<p class="rmqbleu" align="center">Les <?php echo $t_sizaines; ?> de ta Section sont toutes remises à zéro.</p>
<p align="center"><a href="index.php?page=gestionsizaines">Retour &agrave; la Gestion des <?php echo $t_sizaines; ?></a></p>
</div>
<?php
	}
	else if ($_POST['do'] == 'updatesizaines')
	{
		$fiches_modifiees = 0;
		for ($i = 1; $i <= $_POST['nbre']; $i++)
		{
			$numuser = $var_siz = $var_exsiz = $var_cp = $var_excp = $champ_siz = $champ_exsiz = $champ_cp = $champ_excp = null;
			$var_numuser = 'num_'.$i; // construction de la variable qui contient le numero en cours
			$numuser = $_POST[$var_numuser] ;
			$var_siz = "siz_pat_".$numuser;
			$var_exsiz = 'old'.$var_siz;
			$var_cp = 'cp_sizenier_'.$numuser;
			$var_excp = 'old'.$var_cp;
			$champ_siz = $_POST[$var_siz];
			$champ_exsiz = $_POST[$var_exsiz];
			$champ_cp = $_POST[$var_cp];
			$champ_excp = $_POST[$var_excp];
			if ($champ_siz != $champ_exsiz or $champ_cp != $champ_excp)
			{
				$fiches_modifiees++;
				$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET cp_sizenier = '$champ_cp', siz_pat = '$champ_siz', mb_lastmodifby = '$user[num]', mb_lastmodif = now() WHERE nummb = '$numuser'";
				send_sql($db, $sql);
			}
		}
		log_this("Modification des $t_sizaines de la section ".addslashes($sections[$user['numsection']]['nomsection']), 'gestionsizaines');
		header('Location: index.php?page=gestionsizaines&msg=ok&nbre='.$fiches_modifiees);	
	}
	else if ($nbre_sizaines == 0)
	{
?>
<div class="msg">
<p align="center">Ta section ne contient encore aucune <?php echo $t_sizaine; ?>.</p>
<p align="center"><a href="index.php?page=gestion_sizpat">Clique ici pour les ajouter</a>.</p>
</div>
<?php
	}
}
else
{
	include('404.php');
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