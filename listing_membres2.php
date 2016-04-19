<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* listing_membres2.php v 1.1 - Affichage web ou csv du listing membres (appelé par listing_membres.php)
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
	if (empty($_POST['format']))
	{
		header('Location: index.php?page=listing_membres');
		exit;
	}
	if ($_POST['format'] == 'web')
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Affichage listings - <?php echo $site['nom_unite']; ?></title>
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
	$restreindre = '';
	if ($_POST['restreint'] == 'tous')
	{
		$restreindre = '';
	}
	else if ($_POST['restreint'] == 'staff')
	{
		$restreindre = 'AND fonction > \'1\'';
	}
	else if ($_POST['restreint'] == 'scouts')
	{
		$restreindre = 'AND fonction <= \'1\'';
	}
	else if ($_POST['restreint'] == 'sizeniers')
	{
		$restreindre = 'AND cp_sizenier > \'0\'';
	}
	if ($_POST['attente'] != 'oui')
	{ // inclure les membres sur liste d'attente
		$restreindre .= ' AND actif != \'0\'';
	}
	if (is_numeric($_POST['annee']))
	{
		if ($_POST['quand'] == 'en')
		{
			$restreindre .= ' AND year(ddn) = \''.$_POST['annee'].'\'';
		}
		else if ($_POST['quand'] == 'avant')
		{
			$restreindre .= ' AND year(ddn) < \''.$_POST['annee'].'\'';
		}
		else if ($_POST['quand'] == 'apres')
		{
			$restreindre .= ' AND year(ddn) > \''.$_POST['annee'].'\'';
		}
	}
	$criteretri = '';
	if ($_POST['tri'] == 'nom')
	{
		$criteretri = ', nom_mb, prenom ASC';	
	}
	else if ($_POST['tri'] == 'age')
	{
		$criteretri = ', ddn, nom_mb, prenom ASC';
	}
	else if ($_POST['tri'] == 'cot')
	{
		$criteretri = ', cotisation, nom_mb, prenom ASC';
	}
	else if ($_POST['tri'] == 'siz')
	{
		$criteretri = ', siz_pat ASC, cp_sizenier DESC';
	}
	else if ($_POST['tri'] == 'fonc')
	{
		$criteretri = ', fonction, nom_mb, prenom ASC';
	}
	if (is_unite($_POST['section']) and $_POST['afftotale'] == 1 and $_POST['section'] != 'tous')
	{ // listing d'une unité au complet
		$res = is_unite($_POST['section'], true); // récupération des sections de l'unité pour les inclure dans la requête
		$multi = ' (section = \''.$_POST['section'].'\'';
		if (is_array($res))
		{
			foreach ($res as $a)
			{
				$multi .= ' OR section = \''.$a.'\'';
			}
		}
		$multi .= ')';
		$nom_section = $sections[$_POST['section']]['nomsection'];
		$sql = "SELECT *, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_membres as a LEFT JOIN ".PREFIXE_TABLES."mb_adresses as b ON a.famille = b.numfamille WHERE $multi $restreindre ORDER BY actif DESC $criteretri";
	}
	else if (!empty($_POST['section']))
	{
		if ($_POST['section'] == 'tous')
		{
			// listing de toutes les unités confondues
			$nbre = 0;
			$multi = '';
			foreach($sections as $section)
			{
				if (!$section['anciens'])
				{
					$nbre++;
					$multi .= ($nbre == 1) ? ' (' : ' OR ';
					$multi .= 'section = \''.$section['numsection'].'\'';
				}
			}
			$multi .= ($nbre > 0) ? ')' : '';
			$nom_section = 'Les Unit&eacute;s du site';
		}
		else
		{
			// listing d'une section particulière
			$multi = ' section = \''.$_POST['section'].'\'';
			$nom_section = $sections[$_POST['section']]['nomsection'];
		}
		$sql = "SELECT *, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_membres as a LEFT JOIN ".PREFIXE_TABLES."mb_adresses as b ON a.famille = b.numfamille WHERE $multi $restreindre ORDER BY actif DESC $criteretri";
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Aucune section n'a &eacute;t&eacute; s&eacute;lectionn&eacute;e.</p>
</div>
<?php
	}
	if (!empty($_POST['section']))
	{
		if ($_POST['format'] == 'web')
		{
?>
<h1><?php echo $nom_section; ?></h1>
<?php
			if ($res = send_sql($db, $sql))
			{ // détermination des champs à afficher
				if (mysql_num_rows($res) > 0)
				{
					$c = 0;
					for ($k = 1; $k <= 18; $k++)
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
					$show = Array('', 'Nom', 'Pr&eacute;nom', 'DDN', 'Adresse', 'Tel 1', 'Tel 2', 'Tel 3', 'Tel 4', 'Totem + quali', 'Email membre', 'Email famille', 'Email famille 2', 'Tel perso', 'Site web', 'Statut', 'Sizaine', 'Fonc.', 'Coti.');
					for ($i = 1; $i <= 18; $i++)
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
						if ($i == 18 and is_unite($_POST['section']) and $_POST['show_section'] == 1)
						{
							echo '<th>Section</th>';
							$nbrecolonnes++;
						}
					}
?>
  </tr>
  <?php
					$j = 0;
					$liste_attente_on = false;
					$animateur_on = false;
					while ($membre = mysql_fetch_assoc($res))
					{
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
						if ($membre['actif'] == 0 and !$liste_attente_on)
						{ // permet d'afficher une séparation entre les membres actifs et ceux sur liste d'attente
							$j = 0;
							$liste_attente_on = true;
							echo '<tr>';
							echo '<td colspan="'.$nbrecolonnes.'" class="rmq">Liste d\'attente</td>';
							echo '</tr>';
						}
						if ($membre['fonction'] > 1 and !$animateur_on and $_POST['tri'] == 'fonc')
						{ // permet d'afficher une séparation entre les animés et les animateurs lorsque le tri est sur animateur/animé
							$j = 0;
							$animateur_on = true;
							echo '<tr>';
							echo '<td colspan="'.$nbrecolonnes.'" class="rmq">Animateurs</td>';
							echo '</tr>';
						}
						$j++;
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
								echo '<a href="index.php?page=fichemb&amp;nummb='.$membre['nummb'].'" target="_blank" title="Voir sa fiche personnelle"><img src="templates/default/images/membre.png" width="18" height="12" border="0" alt="Voir sa fiche personnelle" /></a>&nbsp;';
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
							echo '<td>'.$membre['adresse'].'&nbsp;</td>';
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
						// email famille 2
						if ($_POST['show_12'] == 1)
						{
							echo '<td>'.$membre['email2'].'&nbsp;</td>';
						}
						// tel perso
						if ($_POST['show_13'] == 1)
						{
							echo '<td>'.$membre['telperso'].'&nbsp;</td>';
						}
						// siteweb
						if ($_POST['show_14'] == 1)
						{
							echo '<td>'.$membre['siteweb'].'&nbsp;</td>';
						}
						// statut
						if ($_POST['show_15'] == 1)
						{
							echo '<td>'.$statuts[$membre['cp_sizenier']].'&nbsp;</td>';
						}
						// sizaine
						if ($_POST['show_16'] == 1)
						{
							echo '<td>'.$sizaines[$membre['siz_pat']]['nomsizaine'].'&nbsp;</td>';
						}
						// fonction
						if ($_POST['show_17'] == 1)
						{
							echo '<td>'.$fonctions[$membre['fonction']]['sigle_fonction'].'&nbsp;</td>';
						}
						// cotisation
						if ($_POST['show_18'] == 1)
						{
							echo '<td align="center">';
							if ($membre['cotisation'] == '1')
							{
								$m = 'Cotisation pay&eacute;e';
								echo '<img src="templates/default/images/ok.png" width="12" height="12" alt="'.$m.'" title="'.$m.'" />';
							}
							else if ($membre['cotisation'] == 0)
							{
								$m = 'Cotisation non pay&eacute;e';
								echo '<img src="templates/default/images/non.png" width="12" height="12" alt="'.$m.'" title="'.$m.'" />';
							}
							else
							{
								$m = 'Etat cotisation inconnu';
								echo '<img src="templates/default/images/inconnu.png" width="12" height="12" alt="'.$m.'" title="'.$m.'" />';
							}
							echo '&nbsp;</td>';
						}
						// section
						if (is_unite($_POST['section']) and $_POST['show_section'] == 1)
						{
							echo '<td>'.$sections[$membre['section']]['sigle_section'].'&nbsp;</td>';
						}
						echo '</tr>
';
						if ($membre['famille2'] != 0 and $_POST['show_adresse2'] == 1)
						{
							echo '<tr class="'.$couleur2.'">';
							$numfamille2 = $membre['famille2'];
							$deuxieme = "SELECT *, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses WHERE numfamille = $numfamille2";
							$data = send_sql($db, $deuxieme);
							if (mysql_num_rows($data) == 1)
							{
								$adresse = mysql_fetch_assoc($data);
								echo '<td>&nbsp;</td>';
								// nom
								if ($_POST['show_1'] == 1)
								{
									echo '<td>&nbsp;</td>';
								}
								// prenom
								if ($_POST['show_2'] == 1)
								{
									echo '<td>&nbsp;</td>';
								}
								// ddn
								if ($_POST['show_3'] == 1)
								{
									echo '<td align="center">&nbsp;</td>';
								}
								// adresse
								if ($_POST['show_4'] == 1)
								{
									echo '<td>'.$adresse['adresse'].'&nbsp;</td>';
									echo '<td>'.$adresse['cp'].'&nbsp;</td>';
									echo '<td>'.$adresse['ville'].'&nbsp;</td>';
								}
								// tel 1
								if ($_POST['show_5'] == 1)
								{
									echo '<td>'.$adresse['tel1'].'&nbsp;</td>';
								}
								// tel 2
								if ($_POST['show_6'] == 1)
								{
									echo '<td>'.$adresse['tel2'].'&nbsp;</td>';
								}
								// tel 3
								if ($_POST['show_7'] == 1)
								{
									echo '<td>'.$adresse['tel3'].'&nbsp;</td>';
								}
								// tel 4
								if ($_POST['show_8'] == 1)
								{
									echo '<td>'.$adresse['tel4'].'&nbsp;</td>';
								}
								// totem + quali
								if ($_POST['show_9'] == 1)
								{
									echo '<td>&nbsp;</td>';
								}
								// email_mb
								if ($_POST['show_10'] == 1)
								{
									echo '<td>&nbsp;</td>';
								}
								// email famille
								if ($_POST['show_11'] == 1)
								{
									echo '<td>'.$adresse['email'].'&nbsp;</td>';
								}
								// email famille
								if ($_POST['show_12'] == 1)
								{
									echo '<td>'.$adresse['email2'].'&nbsp;</td>';
								}
								for ($i = 13; $i <= 18; $i++)
								{
									echo ($_POST['show_'.$i] == 1) ? '<td>&nbsp;</td>' : '';
								}
								if (is_unite($_POST['section']) and $_POST['show_section'] == 1)
								{
									echo '<td>&nbsp;</td>';
								}
								echo '</tr>
';
							}
						}
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
			$show = array('', 'Nom', 'Prénom', 'DDN', 'Adresse', 'Tel 1', 'Tel 2', 'Tel 3', 'Tel 4', 'Totem + quali', 'Email membre', 'Email famille', 'Email famille 2', 'Tel perso', 'Site web', 'Statut', 'Sizaine', 'Fonction', 'Coti', 'Actif');
			$djfait = false;
			for ($i = 1; $i <= 19; $i++)
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
				if ($i == 18 and is_unite($_POST['section']) and $_POST['show_section'] == 1)
				{
					if ($djfait) {echo ';';}
					echo '"Section"';
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
					echo stripslashes(html_entity_decode($membre['nom_mb'], ENT_QUOTES));
					echo '"';
					$djcase = true;
				}
				// prenom
				if ($_POST['show_2'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['prenom'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// ddn
				if ($_POST['show_3'] == 1)
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
					echo '"'.stripslashes(html_entity_decode($membre['adresse'], ENT_QUOTES)).'"';
					echo ';"'.$membre['cp'].'"';
					echo ';"'.stripslashes(html_entity_decode($membre['ville'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// tel 1
				if ($_POST['show_5'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['tel1'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// tel 2
				if ($_POST['show_6'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['tel2'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// tel 3
				if ($_POST['show_7'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['tel3'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// tel 4
				if ($_POST['show_8'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['tel4'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// totem + quali
				if ($_POST['show_9'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"';
					echo (!empty($membre['totem_jungle'])) ? stripslashes(html_entity_decode($membre['totem_jungle'], ENT_QUOTES)) : '';
					echo (!empty($membre['totem_jungle']) and (!empty($membre['totem']) or !empty($membre['quali']))) ? ', ' : '';
					echo stripslashes(html_entity_decode($membre['totem'].' '.$membre['quali'], ENT_QUOTES));
					echo '"';
					$djcase = true;
				}
				// email_mb
				if ($_POST['show_10'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.$membre['email_mb'].'"';
					$djcase = true;
				}
				// email famille
				if ($_POST['show_11'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.$membre['email'].'"';
					$djcase = true;
				}
				// email famille 2
				if ($_POST['show_12'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.$membre['email2'].'"';
					$djcase = true;
				}
				// tel perso
				if ($_POST['show_13'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['telperso'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// siteweb
				if ($_POST['show_14'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($membre['siteweb'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// statut
				if ($_POST['show_15'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($statuts[$membre['cp_sizenier']], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// sizaine
				if ($_POST['show_16'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($sizaines[$membre['siz_pat']]['nomsizaine'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// fonction
				if ($_POST['show_17'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($fonctions[$membre['fonction']]['sigle_fonction'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				// cotisation
				if ($_POST['show_18'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"';
					if ($membre['cotisation'] == 1)
					{
						echo 'V';
					}
					else if ($membre['cotisation'] == 0)
					{
						echo 'X';
					}
					else
					{
						echo '?';
					}
					echo '"';
					$djcase = true;
				}
				// section
				if (is_unite($_POST['section']) and $_POST['show_section'] == 1)
				{
					if ($djcase) {echo ';';}
					echo '"'.stripslashes(html_entity_decode($sections[$membre['section']]['sigle_section'], ENT_QUOTES)).'"';
					$djcase = true;
				}
				echo "\n";
				if ($membre['famille2'] != 0 and $_POST['show_adresse2'] == 1)
				{
					$numfamille2 = $membre['famille2'];
					$deuxieme = "SELECT *, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses WHERE numfamille = $numfamille2";
					$data = send_sql($db, $deuxieme);
					if (mysql_num_rows($data) == 1)
					{
						$adresse = mysql_fetch_assoc($data);
						$djcase2 = false;
						// nom
						if ($_POST['show_1'] == 1)
						{
							echo '"';
							echo stripslashes(html_entity_decode($membre['nom_mb'], ENT_QUOTES));
							echo '"';
							$djcase2 = true;
						}
						// prenom
						if ($_POST['show_2'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"'.stripslashes(html_entity_decode($membre['prenom'], ENT_QUOTES)).'"';
							$djcase2 = true;
						}
						// ddn
						if ($_POST['show_3'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// adresse
						if ($_POST['show_4'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"'.stripslashes(html_entity_decode($adresse['adresse'], ENT_QUOTES)).'"';
							echo ';"'.$adresse['cp'].'"';
							echo ';"'.stripslashes(html_entity_decode($adresse['ville'], ENT_QUOTES)).'"';
							$djcase2 = true;
						}
						// tel 1
						if ($_POST['show_5'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['tel1'].'"';
							$djcase2 = true;
						}
						// tel 2
						if ($_POST['show_6'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['tel2'].'"';
							$djcase2 = true;
						}
						// tel 3
						if ($_POST['show_7'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['tel3'].'"';
							$djcase2 = true;
						}
						// tel 4
						if ($_POST['show_8'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['tel4'].'"';
							$djcase2 = true;
						}
						// totem + quali
						if ($_POST['show_9'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							$djcase2 = true;
						}
						// email_mb
						if ($_POST['show_10'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// email famille
						if ($_POST['show_11'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['email'].'"';
							$djcase2 = true;
						}
						// email famille 2
						if ($_POST['show_12'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '"';
							echo $adresse['email2'].'"';
							$djcase2 = true;
						}
						// tel perso
						if ($_POST['show_13'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// siteweb
						if ($_POST['show_14'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// statut
						if ($_POST['show_15'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// sizaine
						if ($_POST['show_16'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// fonction
						if ($_POST['show_17'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// cotisation
						if ($_POST['show_18'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						// section
						if (is_unite($_POST['section']) and $_POST['show_section'] == 1)
						{
							if ($djcase2) {echo ';';}
							echo '""';
							$djcase2 = true;
						}
						echo "\n";
					}
				}
			}
		}
	}
}
?>