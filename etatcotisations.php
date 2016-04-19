<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* etatcotisations.php v 1.1 - Consultation de l'état de paiement des cotisations des membres de l'unité
* Seul le statut payé/non payé/inconnu est utilisé dans ce fichier
* Pour un état plus avancé (montant cotisation, ...), à vos claviers !
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
*	ajout message d'erreur pour section inexistante
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
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
<body>
<?php
	}
	if (!isset($_POST['do']))
	{
?>
<h1>Gestion des cotisations</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function sectionchoisie(form)
{
	if (form.numsection.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir une section.");
		return false;
	}
}
function aff_effectif_complet(form)
{
	if (form.num_unites.value != "") 
	{
		form.afftotale.value = 0;
		liste = form.num_unites.value.split('_');
		var x=0;
		while (x < liste.length)
		{
			if (form.numsection.value == liste[x])
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
<form action="index.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">
<h2>Afficher l'&eacute;tat des cotisations</h2>
  <input type="hidden" name="page" value="etatcotisations" />
  <input type="hidden" name="do" value="affsection" />
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="afftotale" value="0" />
<p align="center">S&eacute;lectionne la section &agrave; afficher : 
<select name="numsection" onchange="aff_effectif_complet(this.form)">
          <option value="" selected="selected"></option>
<?php
		$num_unites = '';
		foreach($sections as $section)
		{
			if (!$section['anciens'])
			{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo (is_unite($section['numsection'])) ? ' style="font-weight:bold;"' : '';?><?php echo ($section['numsection'] == $_GET['section']) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
<?php
				$num_unites .= (is_unite($section['numsection'])) ? '_'.$section['numsection'] : '';
			}
		}
?>
    </select>
		<input type="hidden" name="num_unites" value="<?php echo $num_unites; ?>" />
</p>
<p align="center"> 
<input type="submit" value="Afficher l'&eacute;tat" />
</p>
</form>
<?php
	}
	else if (is_numeric($_POST['numsection']) and $_POST['do'] == 'affsection') 
	{
		if (array_key_exists($_POST['numsection'], $sections))
		{
?>
<h1>Gestion des cotisations</h1>
<p align="center">
  <?php if ($user['niveau']['numniveau'] > 3) { ?>
  <a href="index.php?page=gestioncotisations">Gestion des cotisations</a> - 
  <?php } ?>
  <a href="index.php?page=etatcotisations">Retour &agrave; la page Etat des cotisations</a> 
</p>
<h2>Etat des cotisations <?php echo $sections[$_POST['numsection']]['nomsection']; ?></h2>
<div class="instructions">
<p>Tu peux consulter sur cette page l'&eacute;tat d'avancement des paiements de 
  la cotisation. Passe ta souris sur les ic&ocirc;nes pour conna&icirc;tre leur 
  signification. Les membres qui ne sont pas en ordre de cotisation ou dont l'&eacute;tat 
  de paiement n'est pas encore enregistr&eacute; sont affich&eacute;s en t&ecirc;te 
  de liste et tri&eacute;s par ordre alphab&eacute;tique sur le nom.</p>
</div>
<?php
  			$res = is_unite($_POST['numsection'], true);
			$multi = 'WHERE actif = \'1\' AND section = \''.$_POST['numsection'].'\'';
			if (is_array($res) and $_POST['afftotale'] == 1)
			{
				foreach ($res as $a)
				{
					$multi .= ' OR section = \''.$a.'\'';
				}
				$show_section = true;
			}
			$sql = 'SELECT nummb, nom_mb, prenom, ddn, cotisation, section FROM '.PREFIXE_TABLES.'mb_membres '.$multi.' ORDER BY cotisation, nom_mb, prenom ASC';
			if ($res = send_sql($db, $sql))
			{
				$nbre_mb = mysql_num_rows($res);
				if ($nbre_mb > 0)
				{
?>
<table cellspacing="1" width="90%" align="center">
  <tr>
  	<th></th>
  	<th width="40"></th>
  	<th>Nom</th>
	<th>Pr&eacute;nom</th>
	<th>DDN</th>
<?php
					if ($show_section)
					{
?>
	<th>Section</th>
<?php
					}
?>
  </tr>
<?php
					$nb = 0;
					while ($membre = mysql_fetch_assoc($res))
					{
						$nb++;
						$couleur = ($nb % 2 == 0) ? 'td-1' : 'td-2';
						$ddn = ($membre['ddn'] != '0000-00-00') ? date_ymd_dmy($membre['ddn'], 'enlettres') : '';
						if ($membre['cotisation'] == '1')
						{
							$i = 'ok.png';
							$j = 'Cotisation pay&eacute;e';
							$rmq = '';
						}
						else if ($membre['cotisation'] == '0')
						{
							$i = 'non.png';
							$j = 'Cotisation non pay&eacute;e';
							$rmq = 'rmq';
						}
						else
						{
							$i = 'inconnu.png';
							$j = 'Etat cotisation inconnu';
							$rmq = 'rmqbleu';
						}
?>
  <tr class="<?php echo $couleur; ?>">
	<td align="right"><?php echo $nb; ?></td>
	<td width="40" align="center"><img src="templates/default/images/<?php echo $i; ?>" width="12" height="12" alt="<?php echo $j; ?>" title="<?php echo $j; ?>" />&nbsp;<a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>" target="_blank" title="Voir sa fiche personnelle"><img src="templates/default/images/membre.png" border="0" width="18" height="12" alt="Voir sa fiche personnelle" /></td>	
	<td class="<?php echo $rmq; ?>"><?php echo $membre['nom_mb']; ?></td>
	<td><?php echo ucfirst($membre['prenom']); ?></td>
	<td><?php echo $ddn; ?></td>
<?php
						if ($show_section)
						{
?>
	<td><?php echo $sections[$membre['section']]['nomsectionpt']; ?></td>
<?php
						}
?>
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
<p align="center" class="rmq">Aucun membre de cette section n'est enregistr&eacute; dans la base de donn&eacute;es.</p>
</div>
<?php
				}
			}
		} // fin key exists
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Cette section n'existe pas !</p>
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