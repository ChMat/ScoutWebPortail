<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* pagesectionmaj.php v 1.1 - Liste des pages modifiées par ordre chronologique
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
if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Les pages du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body bgcolor="#FFFFFF">
<?php
}
?>
<h1>Les pages du portail mises &agrave; jour derni&egrave;rement</h1>
<div class="introduction">
  <p>Le portail est mis &agrave; jour par les animateurs eux-m&ecirc;mes. Tu trouveras ci-dessous les pages modifi&eacute;es r&eacute;cemment. Il se peut que certaines disparaissent de la liste de temps &agrave; autre lorsqu'elles sont en cours de mise &agrave; jour. Si tu souhaites proposer tes textes, n'h&eacute;site pas ! Contacte le webmaster. </p>
</div>
<?php
	$sql = "SELECT titre, specifiquesection, lastmodif, page FROM ".PREFIXE_TABLES."pagessections as a, ".PREFIXE_TABLES."auteurs as b WHERE a.lastmodifby = b.num AND statut = 2 ORDER BY lastmodif DESC";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0 and is_array($sections))
		{
?>
<div class="form_config_site">
<table border="0" align="center" cellspacing="0" class="cadrenoir">
  <tr>
    <th>Titre de la page</th>
    <th>Section</th>
    <th>Mise &agrave; jour le</th>
  </tr>
  <?php
			$j = 1;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>">
    <td><img src="templates/default/images/fiche.png" border="0" width="18" height="12" alt="" />
      <?php
				$titrepage = '';
				if (!empty($ligne['titre'])) {$titrepage = stripslashes($ligne['titre']);} else {$titrepage = $ligne['page'];}
				$nivcible = '';
				foreach ($sections as $test_niv) 
				{ // récupération du numéro de section correspondant à la lettre de $niv
				   if ($test_niv['numsection'] == $ligne['specifiquesection']) 
				   {
					   $nivcible = $test_niv['site_section'];
				   }
				}
				$niv_url_rew = (!empty($nivcible)) ? $nivcible.'_' : '';
				$niv_non_url_rew = (!empty($nivcible)) ? 'niv='.$nivcible.'&amp;' : '';
				$adresse = ($site['url_rewriting_actif'] == 1) ? $niv_url_rew.$ligne['page'].'.htm' : 'index.php?'.$niv_non_url_rew.'page='.$ligne['page'];
?>
      <a href="<?php echo $adresse; ?>"><?php echo $titrepage; ?></a> </td>
    <td><?php echo $sections[$ligne['specifiquesection']]['nomsectionpt']; ?></td>
    <td><?php echo date_ymd_dmy($ligne['lastmodif'], 'enlettres'); ?></td>
  </tr>
  <?php
				$j++;
			}
?>
</table>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
  <p align="center" class="rmq">Aucune page n'a &eacute;t&eacute; publi&eacute;e sur le portail récemment</p>
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
