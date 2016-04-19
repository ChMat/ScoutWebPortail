<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listing_anciens2.php v 1.1 - Affichage du listing des anciens de l'unité
* Fichier lié : listing_anciens.php
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
	if ($_POST['format'] == 'web')
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Affichage listings - <?php echo $site['nom_unite']; ?></title>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	}
	else if ($_POST['format'] == 'csv')
	{
		$NomFichier = 'listing.csv';
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename="'.$NomFichier.'"');
		header('Expires: 0');
	}
	$criteretri = '';
	if ($_POST['tri'] == 'nom')
	{
		$criteretri = 'nom_mb, prenom';	
	}
	else if ($_POST['tri'] == 'age')
	{
		$criteretri = 'ddn, nom_mb, prenom';
	}
	if (!empty($_POST['section']))
	{
		if ($_POST['section'] == 'tous')
		{
			$nbre_sections = 0;
			$plus = '';
			foreach($sections as $section)
			{
				if ($section['anciens'] == 1)
				{
					$nbre_sections++;
					$plus .= ($nbre_sections > 1) ? ' OR ' : '';
					$plus .= 'section = \''.$section['numsection'].'\'';
				}
			}
			$plus = ($nbre_sections > 0) ? 'WHERE '.$plus : '';
		}
		else if (is_numeric($_POST['section']))
		{
			$plus = 'WHERE section = \''.$_POST['section'].'\'';
		}
		// listing d'une section particulière
		$sql = "SELECT * FROM ".PREFIXE_TABLES."mb_membres as a LEFT JOIN ".PREFIXE_TABLES."mb_adresses as b ON a.famille = b.numfamille $plus ORDER BY $criteretri ASC";
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Aucune section n'a &eacute;t&eacute; s&eacute;lectionn&eacute;e.</p>
</div>
<?php
	}
	if (is_numeric($_POST['section']) or $_POST['section'] == 'tous')
	{
		if ($_POST['format'] == 'web')
		{
?>
<h1><?php echo (is_numeric($_POST['section'])) ? $sections[$_POST['section']]['nomsection'] : 'Les anciens'; ?></h1>
<?php
			if ($res = send_sql($db, $sql))
			{ // détermination des champs à afficher
				if (mysql_num_rows($res) > 0)
				{
					$c = 0;
					for ($k = 1; $k <= 17; $k++)
					{
						$c = 'show_'.$k;
						$nbre += $_POST[$c];
					}
?>
<table border="0" cellspacing="0" cellpadding="0">
  <tr> 

<?php
					$nbrecolonnes = 1;
					echo '<th>N°</th>';
					$show = Array('', 'Nom', 'Pr&eacute;nom', 'DDN', 'Adresse', 'Tel 1', 'Tel 2', 'Tel 3', 'Tel 4', 'Totem + quali', 'Email membre', 'Email famille', 'Tel perso', 'Site web', 'Statut', 'Sizaine', 'Fonc.', 'Coti.');
					for ($i = 1; $i <= 17; $i++)
					{
						$colonne = 'show_'.$i;
						if ($_POST[$colonne] == 1)
						{
							$nbrecolonnes++;
							echo '<th>'.$show[$i].'</th>';
							if ($i == 4)
							{
								echo '<th>CP</th>';
								echo '<th>Ville</th>';
								$nbrecolonnes += 2;
							}
						}
					}
?>
  </tr>
  <?php
					$j = 0;
					while ($membre = mysql_fetch_assoc($res))
					{
						$j++;
						if ($j % 2 != 0) 
						{
							$couleur = 'td-1';
							if ($membre['famille2'] != 0 and $_POST['show_adresse2'] == 1) {$couleur = 'td-3'; $couleur2 = 'td-1';}
						}
						else
						{
							$couleur = 'td-2';
							if ($membre['famille2'] != 0 and $_POST['show_adresse2'] == 1) {$couleur = 'td-4'; $couleur2 = 'td-2';}
						}
						echo '<tr class="'.$couleur.'">';
						echo '<td>'.$j.'.&nbsp;</td>';
						$link_displayed = false;
						// nom
						if ($_POST['show_1'] == 1)
						{
							echo '<td>';
							if ($_POST['afflink'] == 1)
							{
								$link_displayed = true;
								echo '<a href="index.php?page=ficheancien&amp;nummb='.$membre['nummb'].'" target="_blank" title="Voir sa fiche personnelle"><img src="templates/default/images/membre.png" width="18" height="12" border="0" alt="Voir sa fiche personnelle" /></a>&nbsp;';
							}
							echo $membre['nom_mb'];
							echo '&nbsp;</td>';
						}
						// prenom
						if ($_POST['show_2'] == 1)
						{
							echo '<td>';
							if ($_POST['afflink'] == 1 and !$link_displayed)
							{
								echo '<a href="index.php?page=fichemb&amp;nummb='.$membre['nummb'].'" target="_blank" title="Voir sa fiche de membre"><img src="templates/default/images/fiche.png" width="18" height="12" border="0" alt="Voir sa fiche de membre" /></a>&nbsp;';
							}
							echo $membre['prenom'].'&nbsp;</td>';
						}
						// ddn
						if ($_POST['show_3'] == 1)
						{
							echo '<td align="center">';
							if ($membre['ddn'] != '0000-00-00') 
							{
								if ($_POST['show_ddn'] == 'date')
								{
									echo date_ymd_dmy($membre['ddn'], 'enchiffres');
								}
								else if ($_POST['show_ddn'] == 'age')
								{
									echo age($membre['ddn'], 0);
								}
							}
							echo '&nbsp;</td>';
						}
						// adresse
						if ($_POST['show_4'] == 1)
						{
							echo '<td>';
							$slash = '';
							$virgule = '';
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
							echo $membre['rue'].$virgule.$membre['numero'].$slash.$membre['bte'];
							echo '&nbsp;</td>';
							echo '<td>'.$membre['cp'].'&nbsp;</td>';
							echo '<td>'.$membre['ville'].'&nbsp;</td>';
						}
						// tel 1
						if ($_POST['show_5'] == 1)
						{
							echo '<td>'.$membre['tel1'].'&nbsp;</td>';
						}
						// tel 2
						if ($_POST['show_6'] == 1)
						{
							echo '<td>'.$membre['tel2'].'&nbsp;</td>';
						}
						// tel 3
						if ($_POST['show_7'] == 1)
						{
							echo '<td>'.$membre['tel3'].'&nbsp;</td>';
						}
						// tel 4
						if ($_POST['show_8'] == 1)
						{
							echo '<td>'.$membre['tel4'].'&nbsp;</td>';
						}
						// totem de jungle + totem + quali
						if ($_POST['show_9'] == 1)
						{
							echo '<td>';
							echo (!empty($membre['totem_jungle'])) ? $membre['totem_jungle'] : '';
							echo (!empty($membre['totem_jungle']) and (!empty($membre['totem']) or !empty($membre['quali']))) ? ', ' : '';
							echo $membre['totem'].' '.$membre['quali'].'&nbsp;</td>';
						}
						// email_mb
						if ($_POST['show_10'] == 1)
						{
							echo '<td>'.$membre['email_mb'].'&nbsp;</td>';
						}
						// email famille
						if ($_POST['show_11'] == 1)
						{
							echo '<td>'.$membre['email'].'&nbsp;</td>';
						}
						// tel perso
						if ($_POST['show_12'] == 1)
						{
							echo '<td>'.$membre['telperso'].'&nbsp;</td>';
						}
						// siteweb
						if ($_POST['show_13'] == 1)
						{
							echo '<td>'.$membre['siteweb'].'&nbsp;</td>';
						}
						echo '</tr>';
					}
?>
</table>
</body>
</html>
<?php
				}
				else
				{
?>
<div class="msg">
<p align="center" class="rmq">Aucun membre n'a &eacute;t&eacute; trouv&eacute;</p>
</div>
<?php
				}
			}
		}
		else if ($_POST['format'] == 'csv')
		{
			$res = send_sql($db, $sql);
			$show = array('', 'Nom', 'Prénom', 'DDN', 'Adresse', 'Tel 1', 'Tel 2', 'Tel 3', 'Tel 4', 'Totem + quali', 'Email membre', 'Email famille', 'Tel perso', 'Site web', 'Statut', 'Sizaine', 'Fonction', 'Coti', 'Actif');
			$djfait = false;
			for ($i = 1; $i <= 18; $i++)
			{
				$colonne = 'show_'.$i;
				if ($_POST[$colonne] == 1)
				{
					if ($djfait) {echo ';';}
					$djfait = true;
					echo '"'.$show[$i].'"';
					if ($i == 4)
					{
						echo ';"CP"';
						echo ';"Ville"';
					}
				}
			}
			echo "\n";
			$j = 0;
			while ($membre = mysql_fetch_assoc($res))
			{
				$djcase = false;
				$j++;
				// nom
				if ($_POST['show_1'] == 1)
				{
					echo '"';
					echo html_entity_decode($membre['nom_mb'], ENT_QUOTES);
					echo '"';
					$djcase = true;
				}
				// prenom
				if ($_POST['show_2'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['prenom'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// ddn
				if ($_POST[show_3] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"';
					if ($membre['ddn'] != '0000-00-00') 
					{
						if ($_POST['show_ddn'] == 'date')
						{
							echo date_ymd_dmy($membre['ddn'], 'enchiffres');
						}
						else if ($_POST['show_ddn'] == 'age')
						{
							echo age($membre['ddn'], 0);
						}
					}
					echo '"';
					$djcase = true;
				}
				// adresse
				if ($_POST['show_4'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"';
					$slash = '';
					$virgule = '';
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
					echo html_entity_decode($membre['rue'].$virgule.$membre['numero'].$slash.$membre['bte'], ENT_QUOTES);
					echo '"';
					echo ';"'.html_entity_decode($membre['cp'], ENT_QUOTES).'"';
					echo ';"'.html_entity_decode($membre['ville'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// tel 1
				if ($_POST['show_5'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['tel1'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// tel 2
				if ($_POST['show_6'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['tel2'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// tel 3
				if ($_POST['show_7'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['tel3'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// tel 4
				if ($_POST['show_8'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['tel4'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// totem + quali
				if ($_POST['show_9'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"';
					echo (!empty($membre['totem_jungle'])) ? html_entity_decode($membre['totem_jungle'], ENT_QUOTES) : '';
					echo (!empty($membre['totem_jungle']) and (!empty($membre['totem']) or !empty($membre['quali']))) ? ', ' : '';
					echo html_entity_decode($membre['totem'].' '.$membre['quali'], ENT_QUOTES);
					echo '"';
					$djcase = true;
				}
				// email_mb
				if ($_POST['show_10'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['email_mb'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// email famille
				if ($_POST['show_11'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['email'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// tel perso
				if ($_POST['show_12'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['telperso'], ENT_QUOTES).'"';
					$djcase = true;
				}
				// siteweb
				if ($_POST['show_13'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.html_entity_decode($membre['siteweb'], ENT_QUOTES).'"';
					$djcase = true;
				}
				echo "\n";
			}
		}
	}
}
?>