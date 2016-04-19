<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* publipostage2.php - Génère un fichier csv contenant les adresses des membres de l'unité
* Une adresse, plusieurs membres : une seule enveloppe
* Divers critères permettent de n'afficher que certains membres
* Fichier lié : publipostage.php
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
if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
	$restreindre = '';
	$champ_en_plus = '';
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
	else if ($_POST['restreint'] == 'attente')
	{
		$restreindre = 'AND actif = \'0\'';
	}
	if ($_POST['coti'] == 1)
	{
		$restreindre .= ' AND cotisation = \'1\'';
	}
	else if ($_POST['coti'] == 0 and $_POST['coti'] != 'non')
	{
		$restreindre .= ' AND cotisation < \'1\'';
		$champ_en_plus = ', count(cotisation) as nbre_cotisations_pas_en_ordre';
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
	if ($_POST['attente'] != 'oui' and $_POST['restreint'] != 'attente' and !is_section_anciens($_POST['section']) and $_POST['section'] != 'tousetanciens')
	{ // ne pas inclure les membres sur liste d'attente
		$restreindre .= ' AND actif != \'0\'';
	}
	if (is_unite($_POST['section']) and $_POST['afftotale'] == 1)
	{ // listing d'une unité au complet
		$res = is_unite($_POST['section'], true); // récupération des sections de l'unité pour les inclure dans la requête
		$multi = ' AND (section = \''.$_POST['section'].'\'';
		if (is_array($res))
		{
			foreach ($res as $a)
			{
				$multi .= ' OR section = \''.$a.'\'';
			}
		}
		$multi .= ')';
		/*
		Avec une version de MySQL > 4.1, on pourrait ajouter la fonction suivante :
		GROUP_CONCAT(prenom ORDER BY ddn SEPARATOR ', ') as prenoms
		Elle permet de lister les prénoms des différents membres d'une même famille dans la même requête.
		Mais comme beaucoup de serveurs web sont encore à une version précédente de MySQL...
		*/
		$sql = 'SELECT numfamille, nom, count(prenom) as prenoms, prenom, section, cotisation, concat(rue, \', \', numero, IF(bte <> \'\', concat(\' bte \', bte), \'\')) as adresse, cp, ville '.$champ_en_plus.' FROM '.PREFIXE_TABLES.'mb_adresses, '.PREFIXE_TABLES.'mb_membres WHERE (numfamille = famille OR numfamille = famille2) '.$multi.' '.$restreindre.' GROUP BY numfamille ORDER BY nom';
	}
	else if (!empty($_POST['section']))
	{
		/*
		Avec une version de MySQL > 4.1, on pourrait ajouter la fonction suivante :
		GROUP_CONCAT(prenom ORDER BY ddn SEPARATOR ', ') as prenoms
		Elle permet de lister les prénoms des différents membres d'une même famille dans la même requête.
		*/
		if ($_POST['section'] == 'tous' or $_POST['section'] == 'tousetanciens')
		{
			// listing de toutes les unités confondues
			$nbre = 0;
			$multi = '';
			foreach($sections as $section)
			{
				if ($section['anciens'] != 1 or $_POST['section'] == 'tousetanciens')
				{
					$nbre++;
					$multi .= ($nbre == 1) ? ' AND (' : ' OR ';
					$multi .= 'section = \''.$section['numsection'].'\'';
				}
			}
			$multi .= ($nbre > 0) ? ')' : '';
		}
		else
		{
			// listing d'une section particulière
			$multi = ' AND section = \''.$_POST['section'].'\'';
		}
		$sql = 'SELECT numfamille, nom, count(prenom) as prenoms, prenom, section, cotisation, concat(rue, \', \', numero, IF(bte <> \'\', concat(\' bte \', bte), \'\')) as adresse, cp, ville '.$champ_en_plus.' FROM '.PREFIXE_TABLES.'mb_adresses, '.PREFIXE_TABLES.'mb_membres WHERE (numfamille = famille OR numfamille = famille2) '.$multi.' '.$restreindre.' GROUP BY numfamille ORDER BY nom';
	}
	else
	{
		echo '<p align="center" class="rmq">Aucune section n\'a &eacute;t&eacute; s&eacute;lectionn&eacute;e.</p>';
	}
	if (!empty($_POST['section']))
	{
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
			$NomFichier = 'publipostage.csv';
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: binary');
			header('Content-Disposition: attachment; filename="'.$NomFichier.'"');
			header('Expires: 0');
			echo '"Nom";"Prénom";"adresse";"Code postal";"ville"';
			$nbre_unites = 0;
			echo ($_POST['coti'] == 0 and $_POST['coti'] != 'non' and $_POST['section'] != 'tousetanciens') ? ';"nbre_cotisations_pas_en_ordre"' : '';
			if ($_POST['coti'] == 0 and $_POST['coti'] != 'non' and $_POST['section'] != 'tousetanciens')
			{
				if (is_numeric($_POST['section']) and is_unite($_POST['section']) and $_POST['section'] == 'tous')
				{
					$nbre_unites++;
					echo ';"pas_en_ordre_'.str_replace(' ', '_', $sections[$_POST['section']]['nomsectionpt']).'"';
				}
				else if ($_POST['section'] == 'tous')
				{
					foreach ($sections as $unite)
					{
						if ($unite['unite'] == 0)
						{
							$nbre_unites++;
							echo ';"pas_en_ordre_'.str_replace(' ', '_', $unite['nomsectionpt']).'"';
						}
					}
				}
			}
			if ($_POST['section'] == 'tous')
			{
				$nbre = 0;
				$multi_coti = '';
				foreach($sections as $section)
				{
					if (!$section['anciens'])
					{
						$nbre++;
						$multi_coti .= ($nbre == 1) ? ' AND (' : ' OR ';
						$multi_coti .= 'c.section = \''.$section['numsection'].'\'';
					}
				}
				$multi_coti .= ($nbre > 0) ? ')' : '';
			}
			while ($membre = mysql_fetch_assoc($res))
			{
				// la boucle ci-dessous sert à récupérer les statuts de cotisation de la famille, par unité
				// elle est remise à zéro à chaque famille
				$nbre_unites = 0;
				foreach ($sections as $unite)
				{
					if ($unite['unite'] == 0)
					{
						$nbre_mb_pas_ok[$unite['numsection']] = 0;
						$nbre_unites++;
					}
				}
				echo "\r\n";
				// nom
					echo '"';
				 	// on récupère les accents non codés en html (sortie en fichier csv)
					echo stripslashes(html_entity_decode($membre['nom'], ENT_QUOTES));
					echo '";';
				// prenoms
					if ($membre['prenoms'] > 1)
					{
						echo '"';
						$sqlbis = 'SELECT prenom, nom_mb, cotisation, section FROM '.PREFIXE_TABLES.'mb_adresses, '.PREFIXE_TABLES.'mb_membres WHERE numfamille = \''.$membre['numfamille'].'\' AND (numfamille = famille OR numfamille = famille2) '.$multi.' '.$restreindre.' ORDER BY nom';
						$liste = send_sql($db, $sqlbis);
						$j = 1;
						$n = mysql_num_rows($liste);
						while ($ligne = mysql_fetch_assoc($liste))
						{
							// récupération des cotisations impayées de la famille
							if ($ligne['cotisation'] < 1)
							{
								$numero_unite = ($sections[$ligne['section']]['unite'] == 0) ? $ligne['section'] : $sections[$ligne['section']]['unite'];
								$nbre_mb_pas_ok[$numero_unite]++;
							}
							// affichage des prénoms de la famille
							if ($j > 1 and $j != $n) {echo ', ';} else if ($j > 1 and $j == $n) {echo ' et ';} // gestion du séparateur de prénom (',' puis 'et' avant le dernier)
							echo stripslashes(html_entity_decode($ligne['prenom'], ENT_QUOTES));
							// affichage du nom de famille du membre s'il est différent du reste de la famille
							if (strtoupper($ligne['nom_mb']) != strtoupper($membre['nom']) and $_POST['aff_autre_nom'] == 'oui') echo ' '.stripslashes(html_entity_decode($ligne['nom_mb']));
							$j++;
						}
						echo '";';
					}
					else
					{
						echo '"'.stripslashes(html_entity_decode($membre['prenom'], ENT_QUOTES)).'";';
						if ($membre['cotisation'] < 1)
						{
							$numero_unite = ($sections[$membre['section']]['unite'] == 0) ? $membre['section'] : $sections[$membre['section']]['unite'];
							$nbre_mb_pas_ok[$numero_unite]++;
						}
					}
				// adresse
					echo '"'.stripslashes(html_entity_decode($membre['adresse'], ENT_QUOTES)).'";';
					echo '"'.stripslashes(html_entity_decode($membre['cp'], ENT_QUOTES)).'";';
					echo '"'.stripslashes(html_entity_decode($membre['ville'], ENT_QUOTES)).'"';
				// cotisation
					if ($_POST['coti'] == 0 and $_POST['coti'] != 'non' and $_POST['section'] != 'tousetanciens')
					{
						echo ';"'.$membre['nbre_cotisations_pas_en_ordre'].'"';
						if ($membre['nbre_cotisations_pas_en_ordre'] > 0 and $_POST['section'] == 'tous')
						{
							foreach($nbre_mb_pas_ok as $cle => $valeur)
							{
								echo ';"'.$valeur.'"';
							}
						}
						else if ($_POST['section'] == 'tous')
						{
							for($i = 1; $i <= $nbre_unites; $i++)
							{
								echo ';"0"';
							}
						}
					}
	/*			// email_mb
					echo '"'.$membre['email_mb'].'";';
				// email famille
					if ($djcase) {echo ';';}
					echo '"'.$membre['email'].'";';
	*/
			}
		}
		else
		{ // aucun membre trouvé dans la section sélectionné
			header('Location: index.php?page=publipostage&msg=aucun');
		}
	}
}
?>