<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_mb_site.php v 1.0.1 - Gestion des membres du portail
* RAPPEL : Les membres du portail ne sont pas les membre de l'unité
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
*	Modification du lien de retour avec le referer php plutôt que par javascript
*	Ajout redirection vers création d'une unité après l'installation
*/

include_once('connex.php');
include_once('fonc.php');

if (!is_array($sections))
{ // les sections n'existent pas encore, il faut les créer d'abord
	include('gestion_sections.php');
}
else if ($user['niveau']['numniveau'] < 5)
{
	include('404.php');
}
else
{
	if (empty($_GET['do']))
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des membres du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des membres du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; l'Accueil
    Membres</a></p>
<div class="menu_flottant">
<h2>Outils divers</h2>
<div class="icone"><img src="templates/default/images/gestion_mb_site.png" alt="" width="60" height="45" /></div>
<p><a href="index.php?page=gestion_statuts_site" class="menumembres"><img src="templates/default/images/ficheuser.png" alt="" width="18" height="12" border="0" align="top" /> 
	Gestion des Statuts des membres</a><br />
	<a href="index.php?page=createmembresite" class="menumembres"><img src="templates/default/images/newuser.png" alt="" width="18" height="12" border="0" align="top" /> 
	Cr&eacute;er un membre</a><br />
	<a href="index.php?page=modifmembresite" class="menumembres"><img src="templates/default/images/newuser.png" alt="" width="18" height="12" border="0" align="top" /> 
	Initier une inscription</a><br />
	<a href="index.php?page=recapitulatif_droits" class="menumembres">Tableau 
	r&eacute;capitulatif des droits</a></p>
</div>
<?php
		// 5 derniers membres inscrits sur le portail
		$sql = "SELECT num, pseudo, prenom, nom, niveau, email FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation = '' ORDER BY dateinscr DESC LIMIT 0, 5";
		if ($res = send_sql($db, $sql))
		{
			$nbre = mysql_num_rows($res);
			if ($nbre > 0)
			{
?>
      
<table border="0" cellpadding="2" cellspacing="0" class="cadrenoir">
  <tr> 
    <th valign="top" class="petit">5 derni&egrave;res inscriptions sur le portail</th>
    <th align="center" class="petit">Statut</th>
  </tr>
  <?php
				$i = 0;
				while ($mb_site = mysql_fetch_assoc($res))
				{
					$i++;
					$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td class="petit"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$mb_site['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$mb_site['num']; ?>" title="Voir son profil complet"><img src="templates/default/images/user.png" alt="Voir son profil complet" width="18" height="12" border="0" align="middle" /></a>
	  <a href="index.php?page=modifmembresite&amp;num=<?php echo $mb_site['num']?>" title="Modifier sa fiche d'inscription"><img src="templates/default/images/ficheuser.png" width="18" height="12" border="0" align="middle" alt="Modifier sa fiche d'inscription" /></a>&nbsp;<span class="rmq"><?php echo $mb_site['pseudo']; ?></span> 
      (<a href="mailto:<?php echo $mb_site['email']; ?>" class="lienmort" title="&Eacute;crire un mail &agrave; <?php echo $mb_site['pseudo']; ?>"><?php echo $mb_site['prenom'].' '.$mb_site['nom']; ?></a>) 
    </td>
    <td class="petit"><?php echo $niveaux[$mb_site['niveau']]['nomniveau']; ?></td>
  </tr>
  <?php
				}
?>
</table> 
<?php
			}
		}

		$sql = "SELECT num, email, TO_DAYS(CURRENT_TIMESTAMP()) - TO_DAYS(dateinscr) as depuis, clevalidation FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation != ''";
		$res = send_sql($db, $sql);
		$nbre = mysql_num_rows($res);
		$pl = ($nbre > 1) ? 's' : '';
		$nbre = ($nbre > 0) ? $nbre : 'Aucun';
		if ($nbre > 0)
		{
?>
<form action="" method="post" name="form1" id="form1" class="form_config_site">
  <table width="100%" border="0" cellpadding="2" cellspacing="0" class="cadrenoir">
	<tr> 
	  <th valign="top" class="petit"><?php echo $nbre; ?> membre<?php echo $pl; ?> 
		en phase d'inscription</th>
	  <th align="center" class="petit">Cl&eacute; validation</th>
	  <th align="center" class="petit" title="Nombre de jours &eacute;coul&eacute;s depuis le lancement de l'inscription">Age</th>
	  <th>&nbsp;</th>
	</tr>
<?php
			while ($ligne = mysql_fetch_assoc($res))
			{
?>
	<tr class="<?php echo $couleur; ?>"> 
	  <td class="petit"><a href="mailto:<?php echo $ligne['email']; ?>" title="Lui &eacute;crire un email"><?php echo $ligne['email']; ?></a></td>
	  <td class="petit"><?php echo $ligne['clevalidation']; ?></td>
	  <td class="petit" align="center" title="Nombre de jours &eacute;coul&eacute;s depuis le lancement de l'inscription"><?php echo ($ligne['depuis'] > 1) ? $ligne['depuis']." jours" : $ligne['depuis']." jour"; ?></td>
	  <td class="petit" align="center"><a href="index.php?page=gestion_mb_site&amp;do=confirmsuppr&amp;num=<?php echo $ligne['num']; ?>" title="Supprimer ce compte"><img src="templates/default/images/supprimer.png" width="12" height="12" align="middle" alt="Supprimer ce compte" border="0" /></a></td>
	</tr>
        <?php
			}
?>
  </table>
  <p align="center" class="petit">Purger les inscriptions non finalisées depuis 
	un certain temps</p>
	<div align="center">
	  <input type="button" name="Button" value="Purger" onclick="window.location='index.php?page=purgemb';" />
	</div>
  </form>
<?php
		}

		// Liste complète des membres inscrits sur le portail
		
		// On prépare le type de tri
		$tri = ($_GET['tri'] == 'desc') ? 'desc' : 'asc';
		// On prépare le critère de tri
		if ($_GET['ordre'] == 'niveau')
		{
			$ordre = 'numniveau, niveau '.$tri;
		}
		else if (ereg("^(pseudo|nom|lastconnex|pagesvues|assistantwebmaster|banni)$", $_GET['ordre']))
		{
			$ordre = $_GET['ordre'].' '.$tri;
		}
		else
		{
			$ordre = 'pseudo ASC';
		}

		$sql = "SELECT num, pseudo, prenom, nom, email, niveau, lastconnex, pagesvues, nivdemande, assistantwebmaster, banni FROM ".PREFIXE_TABLES."auteurs, ".PREFIXE_TABLES."site_niveaux WHERE clevalidation = '' AND niveau = idniveau ORDER BY ".$ordre;
		if ($res = send_sql($db, $sql))
		{
			$nbre = mysql_num_rows($res);
			if ($nbre > 0)
			{
?>
<table border="0" cellpadding="2" cellspacing="0" class="cadrenoir" align="center">
  <tr> 
    <th valign="top" class="petit">
	<a href="index.php?page=gestion_mb_site&amp;ordre=pseudo&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Pseudo</a> 
	(<a href="index.php?page=gestion_mb_site&amp;ordre=nom&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Nom</a>)</th>
    <th valign="top" class="petit">
	<a href="index.php?page=gestion_mb_site&amp;ordre=niveau&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Statut</a></th>
    <th valign="top" class="petit"><a href="index.php?page=gestion_mb_site&amp;ordre=lastconnex&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Derni&egrave;re visite</a></th>
    <th valign="top" class="petit"><a href="index.php?page=gestion_mb_site&amp;ordre=pagesvues&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Pages vues</a></th>
    <th width="30" align="center" class="petit">Anim</th>
    <th width="30" align="center" class="petit"><a href="index.php?page=gestion_mb_site&amp;ordre=assistantwebmaster&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon le champ Co-webmaster">Web</a></th>
    <th width="30" align="center" class="petit"><a href="index.php?page=gestion_mb_site&amp;ordre=banni&amp;tri=<?php echo ($tri == 'desc') ? 'asc' : 'desc'; ?>" title="Trier selon ce champ">Actif</a></th>
    <th width="30" align="center" class="petit">&nbsp;</th>
  </tr>
  <?php
				$i = 0;
				while ($mb_site = mysql_fetch_assoc($res))
				{
					$i++;
					$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td class="petit"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$mb_site['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$mb_site['num']; ?>" title="Voir son profil complet"><img src="templates/default/images/user.png" alt="Voir son profil complet" width="18" height="12" border="0" align="middle" /></a>
	  <a href="index.php?page=modifmembresite&amp;num=<?php echo $mb_site['num']?>" title="Modifier sa fiche d'inscription"><img src="templates/default/images/ficheuser.png" width="18" height="12" border="0" align="middle" alt="Modifier sa fiche d'inscription" /></a>&nbsp;<span class="rmq"><?php echo $mb_site['pseudo']; ?></span> 
      (<a href="mailto:<?php echo $mb_site['email']; ?>" class="lienmort" title="&Eacute;crire un mail &agrave; <?php echo $mb_site['pseudo']; ?>"><?php echo $mb_site['prenom'].' '.$mb_site['nom']; ?></a>) 
    </td>
    <td class="petit" align="center"><?php echo $niveaux[$mb_site['niveau']]['nomniveau']; ?></td>
    <td class="petit" align="center"><?php echo ($mb_site['lastconnex'] != '0000-00-00 00:00:00') ? temps_ecoule($mb_site['lastconnex']) : '<span title="Ce membre ne s\'est jamais connect&eacute;">Aucune</span>'; ?></td>
    <td class="petit" align="center"><?php echo $mb_site['pagesvues']; ?></td>
    <td width="30" align="center" class="petit"> 
      <?php if ($niveaux[$mb_site['niveau']]['numniveau'] >= 3) { ?>
      <span title="<?php echo $mb_site['pseudo']; ?> a le statut d'Animateur sur le portail"><img src="templates/default/images/ok.png" width="12" height="12" border="0" alt="Animateur" /></span> 
      <?php } else { if ($mb_site['nivdemande'] > 0) { ?>
      <span title="<?php echo $mb_site['pseudo']; ?> a demand&eacute; le statut d'Animateur sur le portail"><img src="templates/default/images/inconnu.png" width="12" height="12" border="0" alt="En attente" /></span> 
      <?php } } ?>
    </td>
    <td width="30" align="center" class="petit"> 
      <?php 
				if ($niveaux[$mb_site['niveau']]['numniveau'] < 3)
				{
					if ($mb_site['assistantwebmaster'] == 0) 
					{ ?>
      <a href="gestion_mb_site.php?do=webmaster&num=<?php echo $mb_site['num']; ?>" class="rmq" title="Autoriser <?php echo $mb_site['pseudo']; ?> &agrave; cr&eacute;er des pages sur le portail"><img src="templates/default/images/non.png" alt="Membre normal" width="12" height="12" border="0" /></a> 
      <?php 
					} 
					else 
					{ ?>
      <a href="gestion_mb_site.php?do=dewebmaster&num=<?php echo $mb_site['num']; ?>" class="rmqbleu" title="retirer le statut d'assistant webmaster &agrave; <?php echo $mb_site['pseudo']; ?>"><img src="templates/default/images/ok.png" alt="Co-webmaster" width="12" height="12" border="0" /></a> 
      <?php 
					} 
				}
			?>
    </td>
    <td width="30" align="center" class="petit"> 
      <?php if ($mb_site['banni'] == '0' and $niveaux[$mb_site['niveau']]['numniveau'] != 5) { ?>
      <a href="gestion_mb_site.php?do=bannir&num=<?php echo $mb_site['num']; ?>" class="rmq" title="bannir <?php echo $mb_site['pseudo']; ?>"><img src="templates/default/images/ok.png" alt="Bannir" width="12" height="12" border="0" /></a> 
      <?php } else if ($niveaux[$mb_site['niveau']]['numniveau'] != 5) { ?>
      <a href="gestion_mb_site.php?do=debannir&num=<?php echo $mb_site['num']; ?>" class="rmqbleu" title="activer le compte de <?php echo $mb_site['pseudo']; ?>"><img src="templates/default/images/non.png" alt="Activer" width="12" height="12" border="0" /></a> 
      <?php } else { ?>
      <img src="templates/default/images/ok.png" alt="<?php echo $mb_site['pseudo']; ?> est webmaster du portail, bannissement impossible" title="<?php echo $mb_site['pseudo']; ?> est webmaster du portail, bannissement impossible" width="12" height="12" border="0" /> 
      <?php } ?>
    </td>
    <td width="30" align="center" class="petit"> 
      <?php if ($niveaux[$mb_site['niveau']]['numniveau'] != 5) { ?>
      <a href="index.php?page=gestion_mb_site&amp;do=confirmsuppr&amp;num=<?php echo $mb_site['num']; ?>" class="rmq" title="Supprimer le compte de <?php echo $mb_site['pseudo']; ?>"><img src="templates/default/images/supprimer.png" alt="Supprimer" width="12" height="12" border="0" /></a> 
      <?php } ?>
    </td>
  </tr>
  <?php
				}
?>
</table> 
<?php
			}
		}
	}
	else if ($_GET['do'] == 'bannir' and is_numeric($_GET['num']))
	{ // On bannit l'utilisateur
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET banni = '1' WHERE num = '".$_GET['num']."'";
		send_sql($db, $sql);
		// Et on le déconnecte du site
		$sql = "DELETE FROM ".PREFIXE_TABLES."connectes WHERE user = '".$_GET['num']."'";
		send_sql($db, $sql);
		header('Location: index.php?page=gestion_mb_site');
	}
	else if ($_GET['do'] == 'debannir' and is_numeric($_GET['num']))
	{ // On annule le bannissement d'un utilisateur
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET banni = '0' WHERE num = '".$_GET['num']."'";
		send_sql($db, $sql);
		header('Location: index.php?page=gestion_mb_site');
	}
	else if ($_GET['do'] == 'webmaster' and is_numeric($_GET['num']))
	{ // On donne à un utilisateur le statut d'assistant webmaster
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET assistantwebmaster = '1' WHERE num = '".$_GET['num']."'";
		send_sql($db, $sql);
		header('Location: index.php?page=gestion_mb_site');
	}
	else if ($_GET['do'] == 'dewebmaster' and is_numeric($_GET['num']))
	{ // On retire à un utilisateur le statut d'assistant webmaster
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET assistantwebmaster = '0' WHERE num = '".$_GET['num']."'";
		send_sql($db, $sql);
		header('Location: index.php?page=gestion_mb_site');
	}
	else if ($_GET['do'] == 'confirmsuppr' and is_numeric($_GET['num']))
	{ // On demande confirmation de la suppression d'un membre
	  // S'il est supprimé, toutes ses interventions sur le site ne seront plus visibles
?>
<h1>Gestion des membres du portail</h1>
<p align="center"><a href="index.php?page=gestion_mb_site">Retour &agrave; la gestion des Membres du portail</a></p>
<div class="action">
<p align="center"><span class="rmq">Attention</span>, toutes les interventions de l'utilisateur sur le portail 
  (forum, tally, gestion de l'unit&eacute;, forum des staffs, pages restreintes) 'disparaîtront' avec lui.<br />
  Si tu as un doute, contente-toi de <em>bannir cet utilisateur</em>.</p>
<p align="center" class="rmqbleu">Es-tu certain de vouloir supprimer le compte 
  de <?php $mb_a_supprimer = untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $_GET['num']); echo (!empty($mb_a_supprimer)) ? $mb_a_supprimer : 'ce membre'; ?> 
  ?</p>
<p align="center"><a href="gestion_mb_site.php?do=supprimer&amp;num=<?php echo $_GET['num']; ?>" class="bouton">Oui</a> <a href="index.php?page=gestion_mb_site" class="bouton">Non</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'supprimer' and is_numeric($_GET['num']))
	{
		$sql = "DELETE FROM ".PREFIXE_TABLES."auteurs WHERE num = '".$_GET['num']."'";
		send_sql($db, $sql);
		header('Location: index.php?page=gestion_mb_site');
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<p align="center"><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Retour &agrave; la page pr&eacute;c&eacute;dente</a></p>
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