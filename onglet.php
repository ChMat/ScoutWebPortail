<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* onglet.php - Affichage des utilisateurs et membres en ligne sur le portail
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

if (!defined('IN_SITE'))
{
	header('Location: 404.php');
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Personnes connect&eacute;es</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
else
{
?>
<div id="bloc_connectes"> 
  <?php
	// affichage des membres connectés
	// On récupère le nombre de membres connectés effectivement en croisant la table connectes et la table ".PREFIXE_TABLES."log_visites 
	// La table auteurs permet de récupérer le véritable pseudo en cas de cookie forgery
	$sql = "SELECT numuser, pseudo FROM ".PREFIXE_TABLES."log_visites as a, ".PREFIXE_TABLES."auteurs as b, ".PREFIXE_TABLES."connectes as c WHERE numuser = num AND numuser = user AND numuser > 0 AND h_fin >= DATE_SUB(now(), INTERVAL '2' MINUTE)";
	if ($res = send_sql($db, $sql))
	{
		$nb_connectes = mysql_num_rows($res);
		// On enregistre le record de membres connectés le cas échéant
		if ($nb_connectes > $site['record_connectes']) save_record_connectes($nb_connectes);
?>
  <span class="petit">En ligne : 
  <?php
		// On calcule le nombre de visiteurs non membres (ou non connectés)
		$e = $site['enligne'] - $nb_connectes;
		if ($e > 0)
		{ // affichage du nombre de visiteurs
			echo ($e > 1) ? $e.' visiteurs ' : $e.' visiteur ';
		}
		if ($nb_connectes > 0)
		{ // s'il y a des membres connectés, on les affiche
			$plurielconnectes = ($nb_connectes > 1) ? 's' : '';
			echo ($e > 0) ? ' et ' : '';
			echo $nb_connectes.' membre'.$plurielconnectes.' : ';
			$j = 1;
			while ($ligne = mysql_fetch_assoc($res))
			{ 
				// on ajoute le séparateur (une virgule, et la conjonction 'et' avant le dernier membre)
				if ($j > 1 and $j != $nb_connectes) {echo ', ';} else if ($j > 1 and $j == $nb_connectes) {echo ' et ';}
				$lien_user = ($site['url_rewriting_actif'] == 1) ? 'membre'.$ligne['numuser'].'.htm' : 'index.php?page=profil_user&amp;user='.$ligne['numuser'];
				echo '<a href="'.$lien_user.'" title="Voir son profil">'.$ligne['pseudo'].'</a>';
				$j++;
			}
		}
?>
  </span> 
  <?php
	} // fin affichage des membres connectés
?>
</div>
<?php

} // fin if defined in_site
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>