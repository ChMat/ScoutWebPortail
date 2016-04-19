<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listembsite.php v 1.1 - Liste des membres du portail (en accès public)
* l'adresse email des membres n'est pas publiée aux visiteurs non identifiés
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
*	Message d'erreur si membre inexistant
*	Optimisation xhtml
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
<title>Les membres du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Les Membres du portail</h1>
<?php 
if ($user > 0) 
{ // le membre est connecté, on affiche les actions qu'il peut faire
?>
<div class="panneau">
<h2>Options</h2>
<ul>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>">Voir mon profil</a></li>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'modifprofil.htm' : 'index.php?page=modifprofil'; ?>">Modifier mon profil</a></li>
<?php if ($user['niveau']['numniveau'] == 5) { ?>
<li><a href="index.php?page=gestion_mb_site">Gestion des membres du site</a></li>
<?php } ?>
</ul>
</div>
<?php 
} 
if ($mb_not_exist)
{ // page incluse dans la page profil membre
  // le profil recherché n'existe pas, on affiche le message d'avertissement
?>
<div class="msg">
  <p align="center" class="rmq">Aucun membre ne correspond &agrave; cette requ&ecirc;te !</p>
</div>
<?php
}
?>
<div style="float:left; width:300px;"><span class="rmqbleu">Nouveaux membres</span> 
      <?php
	// Nouveaux membres inscrits sur le portail
	$sql = "SELECT num, pseudo, dateinscr FROM ".PREFIXE_TABLES."auteurs WHERE banni != '1' and clevalidation = '' ORDER BY dateinscr DESC LIMIT 5";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
      <table border="0" cellspacing="2" cellpadding="0" class="cadrenoir">
<?php
			$j = 0;
			while ($membre = mysql_fetch_assoc($res))
			{
				$j++;
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$membre['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$membre['num']; ?>" title="Découvrir le profil de <?php echo $membre['pseudo']; ?>" class="lienmort"><?php echo $membre['pseudo']; ?></a>
     est membre depuis le <?php echo date_ymd_dmy($membre['dateinscr'], 'jourmois'); ?></td>
  </tr>
<?php
			}
		}
?>
</table>
<?php
	}
?>
</div>
<div style="float:left; width:200px;"><span class="rmqbleu">Membres les plus actifs 
      :</span> 
      <?php
	// Membres les plus actifs
	$sql = "SELECT num, pseudo FROM ".PREFIXE_TABLES."auteurs WHERE banni != '1' ORDER BY pagesvues DESC LIMIT 5";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
      <table width="150" border="0" cellspacing="2" cellpadding="0" class="cadrenoir">
<?php
			$j = 0;
			while ($membre = mysql_fetch_assoc($res))
			{
				$j++;
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$membre['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$membre['num']; ?>" title="Découvrir le profil de <?php echo $membre['pseudo']; ?>"><?php echo $membre['pseudo']; ?></a></td>
  </tr>
<?php
			}
		}
?>
</table>
<?php
	}
?>
	</div>
  <p class="petit" style="clear:both; ">Ci-dessous, tu peux consulter la liste des membres du portail.<br /> 
  Clique sur leur pseudo pour voir leur profil.</p>
<?php
	// Liste complète des membres du site
	$sql = "SELECT num, pseudo, nom, prenom, niveau, numsection, email, siteweb, clevalidation, dateinscr, pagesvues, nbconnex, lastconnex FROM ".PREFIXE_TABLES."auteurs WHERE banni != '1' ORDER BY clevalidation, pseudo ASC";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
			$pl_membres = (mysql_num_rows($res) > 1) ? 's' : '';
?>
<span class="petitbleu" id="nbmb"><?php echo mysql_num_rows($res).' membre'.$pl_membres.' inscrit'.$pl_membres; ?></span>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <th>Pseudo</th>
    <th>Nom</th>
    <th>Site web</th>
    <th>Dans l'Unit&eacute;</th>
  </tr>
<?php
			$j = 0;
			$deja = false;
			while ($membre = mysql_fetch_assoc($res))
			{
				$j++;
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
				if (empty($membre['clevalidation']))
				{
					if ($membre['lastconnex'] != '0000-00-00 00:00:00') {$last = "\nDernière visite le ".date_ymd_dmy($membre['lastconnex'], 'dateheure');} else {$last = '';}
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$membre['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$membre['num']; ?>" title="Découvrir le profil de <?php echo $membre['pseudo'].$last; ?>" class="lienmort"><?php echo $membre['pseudo']; ?></a></td>
    <td align="center"><?php echo $membre['prenom'].' '.$membre['nom']; ?></td>
    <td align="center"><?php 
	  // on ajoute le http à l'url du site perso au cas où
	  $membre['siteweb'] = (!empty($membre['siteweb']) and !ereg('^http://', $membre['siteweb'])) ? 'http://'.$membre['siteweb'] : $membre['siteweb'];
	  // on affiche le lien s'il n'est pas vide
	  echo (!empty($membre['siteweb'])) ? ' <a href="'.$membre['siteweb'].'" title="Voir son site web" target="_blank"><img src="templates/default/images/url.png" alt="site web" border="0" /></a>' : ''; ?></td>
    <td>
<?php 
					echo $niveaux[$membre['niveau']]['nomniveau'];
?>
	</td>
  </tr>
<?php
				}
				else
				{
					if (!empty($membre['clevalidation']) and $deja == false)
					{
						$deja = true;
?>
  <tr> 
      <td colspan="4"><span class="rmqbleu"><br />
        En cours d'inscription :</span> (le pseudo provisoire est bas&eacute; 
        sur l'email) 
        <script language="JavaScript" type="text/JavaScript">getElement("nbmb").innerHTML = "<?php echo mysql_num_rows($res).' membre'.$pl_membres.' inscrit'.$pl_membres; ?> dont <?php echo mysql_num_rows($res) - $j + 1; ?> en cours d'inscription";</script>
	</td>
  </tr>
<?php
					}
					// Pour ne pas afficher l'email du membre en cours d'inscription (seule info disponible)
					// on affiche simplement la première partie de l'adresse email
					list($pseudosuppose,$extra)= split ('@', $membre['email'], 2);
?>
  <tr class="<?php echo $couleur; ?>"> 
    <td align="center"><?php echo $pseudosuppose; ?></td>
    <td colspan="3" align="right"><?php echo 'email envoy&eacute; le '.date_ymd_dmy($membre['dateinscr'], 'enlettres'); ?></td>
  </tr>
<?php
				}
			}
		}
?>
</table>
<?php
	}
?>

<?php
	// On affiche la liste des avatars des membres
	$sql = "SELECT num, pseudo, avatar FROM ".PREFIXE_TABLES."auteurs WHERE banni != '1' AND avatar != '' ORDER BY pseudo";
	if ($res = send_sql($db, $sql))
	{
		$nbre_avatars = mysql_num_rows($res);
		$parligne = 5;
		if ($nbre_avatars > 0)
		{
?>
      <table width="150" border="0" cellspacing="2" cellpadding="0" class="cadrenoir" align="center">
	  	<tr>
		<td class="rmqbleu" colspan="<?php echo $parligne; ?>">Les avatars des membres du portail</td>
		</tr>
<?php
			$j = 0;
			while ($membre = mysql_fetch_assoc($res))
			{
				$j++;
				if ($j == 1 or ($j-1) % $parligne == 0) {echo '<tr>';}
				echo '<td align="center">';
				echo '<img src="img/photosmembres/avatars/'.$membre['avatar'].'" alt="" /><br />';
				$lien_user = ($site['url_rewriting_actif'] == 1) ? 'membre'.$membre['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$membre['num'];
				echo '<a href="'.$lien_user.'" title="Découvrir le profil de '.$membre['pseudo'].'" class="lienmort">'.$membre['pseudo'].'</a>';
				echo '</td>';
				if ($j % $parligne != 0 and $j == $nbre_avatars) 
				{
					$position = $j;
					while ($position % $parligne != 0)
					{
						echo '<td></td>';
						$position++;
					}
				}
				if ($j % $parligne == 0 or $j == $nbre_avatars) {echo '</tr>';}
			}
?>
	</table>
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