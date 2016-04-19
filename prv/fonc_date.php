<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_date.php v 1.1 - Fonctions utiles sur les dates
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
*	Nouveaux formats de date dans date_ymd_dmy()
*/

$mois = array('', 'janvier', 'f&eacute;vrier', 'mars', 'avril', 'mai', 'juin', 'juillet', 'ao&ucirc;t', 'septembre', 'octobre', 'novembre', 'd&eacute;cembre');
$mo = array('', 'Jan', 'F&eacute;v', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Ao&ucirc;', 'Sep', 'Oct', 'Nov', 'D&eacute;c');

function date_ymd_dmy($date, $format)
{ // cette fonction renvoie une date au format sql dans divers formats plus humains
  // $format détermine le format de retour
	global $mois, $mo;
	if (ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs) or ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date, $regs) or ereg("([0-9]{4})([0-9]{1,2})([0-9]{1,2})([0-9]{2})([0-9]{2})([0-9]{2})", $date, $regs)) 
	{
		if ($format == 'enchiffres') // jj/mm/aaaa
		{
    		return "$regs[3]/$regs[2]/$regs[1]";
		}
		else if ($format == 'enlettres') // jj mois aaaa
		{
			$j = $regs[3]+0;
			return $j.' '.$mois[$regs[2]+0].' '.$regs[1];
		}
		else if ($format == 'jourmois') // j mois
		{
			$j = $regs[3]+0;
			return $j.' '.$mois[$regs[2]+0];
		}
		elseif ($format == 'dateheure') // jj/mm/aaaa à hh:mm:ss
		{
    		return "$regs[3]/$regs[2]/$regs[1] &agrave; $regs[4]:$regs[5]:$regs[6]";
		}
		elseif ($format == 'jourheure') // j mo à hh:mm
		{
    		return $regs[3].' '.$mo[$regs[2]+0].' &agrave; '.$regs[4].':'.$regs[5];
		}
		elseif ($format == 'date') // j/mm
		{
			$j = $regs[3]+0;
    		return "$j/$regs[2]";
		}
		else // l'élément demandé (le chiffre correspond : 1 : aaaa, 2 : mm, 3 : jj, 4 : hh, 5 : mm, 6 : ss
		{
			return "$regs[$format]";
		}
	}
	else
	{
    	return 'Format de date incorrect : '.$date;
	}
}

function age($ddn, $params = 0)
// cette fonction calcule l'âge d'une date de naissance
// $params :
// 0 retourne x ans
// 1 retourne x ans et y mois
// 2 retourne x
{
	$age = '';
	if ($ddn != '0000-00-00')
	{
		ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $ddn, $regs);
		$jour = $regs[3];
		$mois = $regs[2];
		$annee = $regs[1];
		$today = getdate();
		$cetteannee = $today['year'];
		$cemois = $today['mon'];
		$cejour = $today['mday'];
		$ageans = $cetteannee - $annee - 1;
		$agemois = $cemois - $mois;
		if ($agemois < 0) {$agemois = 12 + $agemois;}		
		if ($cemois > $mois)
		{
			$ageans++;
		}
		else if ($cemois == $mois)
		{
			if ($cejour >= $jour)
			{
				$ageans++;
			}
			else
			{
				$agemois = 11;
			}
		}
	  	$age = $ageans;
		if ($params == 0)
		{
			$age .= ' ans';
		}
		if ($params == 1) 
		{
			if ($agemois > 0)
			{
				$age .= ' ans et '.$agemois.' mois';
			}
			else
			{
				$age .= ' ans';
			}
		}
	}
	return $age;
}

function datedujour($format = 'j/mm/aaaa')
{ // faut-il expliquer ça ?
	global $mois;
	$nomjour = date('l');
	$jour = date('d');
	$mini_jour = date('j');
	$nummois = date('n');
	$annee = date('Y'); 
	switch ($nomjour)
	{
		case 'Monday': $nomjour = 'lundi'; break;
		case 'Tuesday': $nomjour = 'mardi'; break;
		case 'Wednesday': $nomjour = 'mercredi'; break;
		case 'Thursday': $nomjour = 'jeudi'; break;
		case 'Friday': $nomjour = 'vendredi'; break;
		case 'Saturday': $nomjour = 'samedi'; break;
		case 'Sunday': $nomjour = 'dimanche';	break;
	} 
	$nommois = $mois[$nummois];
	if ($format == 'j mois aaaa')
	{
		$retour = $mini_jour.' '.$nommois.' '.$annee;
	}
	else if ($format == 'j/mm/aaaa')
	{
		$retour = $jour.'/'.$nummois.'/'.$annee;
	}
	return $retour;
}

function temps_ecoule($debut, $fin = 0, $retour_en_secondes = false)
{ // Calcul du temps écoulé entre deux dates
	// si fin n'est pas déterminé, on calcule le temps écoulé jusqu'à aujourd'hui
  	if ($fin == 0) {$fin = date("Y-m-d H:i:s");}
	
	ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $debut, $a);
	$dbt_timestamp = mktime($a[4], $a[5], $a[6], $a[2], $a[3], $a[1]);
	
	ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})", $fin, $b);
	$fin_timestamp = mktime($b[4], $b[5], $b[6], $b[2], $b[3], $b[1]);
	
	$temps_s = $fin_timestamp - $dbt_timestamp; // temps écoulé en secondes
	if ($retour_en_secondes)
	{ // on renvoie le temps écoulé en secondes
		return $temps_s;
	}
	else
	{ // on renvoie le temps écoulé exprimé au format texte (x secondes, minutes, ...)
		$nb_annees = $nb_mois = $nb_jours = $nb_heures = $nb_minutes = $nb_secondes = 0;
		// calcul des divers nombres
		if ($temps_s > 86400 * 365)
		{ // nombre d'annnées
			$nb_annees = floor(($temps_s / (86400 * 365)));
			$temps_s -= $temps_s % (86400 * 365);
		}
		if ($temps_s > 86400 * 30)
		{ // nombre de mois
			$nb_mois = floor(($temps_s / (86400 * 30)));
			$temps_s -= $temps_s % (86400 * 30);
		}
		if ($temps_s > 86400)
		{ // nombre de jours
			$nb_jours = floor(($temps_s / 86400));
			$temps_s -= $temps_s % 86400;
		}
		if ($temps_s > 3600)
		{ // nombre d'heures
			$nb_heures = floor(($temps_s / 3600));
			$temps_s -= $temps_s % 3600;
		}
		if ($temps_s > 60)
		{ // nombre de minutes
			$nb_minutes = floor(($temps_s / 60));
			$temps_s -= $temps_s % 60;
		}
		if ($temps_s > 0)
		{ // nombre de secondes
			$nb_secondes = $temps_s;
		}
	
		// Construction du retour
		if ($nb_annees > 0)
		{ // x ans
			$retour = ($nb_annees > 1) ? $nb_annees.' ans' : $nb_annees.' an';
		}
		else if ($nb_mois > 0)
		{ // x mois
			$retour = $nb_mois.' mois';
		}
		else if ($nb_jours > 0)
		{ // x jours
			$nb_semaines = floor($nb_jours / 7);
			if ($nb_semaines > 0)
			{
				$retour = ($nb_semaines > 1) ? $nb_semaines.' semaines' : $nb_semaines.' semaine';
			}
			else
			{
				$retour = ($nb_jours > 1) ? $nb_jours.' jours' : $nb_jours.' jour';
			}
		}
		else if ($nb_heures > 0)
		{ // x heures
			$retour = ($nb_heures > 1) ? $nb_heures.' heures' : $nb_heures.' heure';
		}
		else
		{
			if ($nb_minutes > 0)
			{ // x minutes
				$retour = ($nb_minutes > 1) ? $nb_minutes.' minutes' : $nb_minutes.' minute';
			}
			else
			{
				$retour = ($nb_secondes > 1) ? $nb_secondes.' secondes' : $nb_secondes.' seconde';
			}
		}
		return ($retour != '') ? $retour : 'erreur';
	}
}

function mysql_time_date()
{ // renvoie la date et l'heure au format MySQL
	return date("Y-m-d H:i:s");
}

function mysql_date()
{ // renvoie la date au format MySQL
	return date("Y-m-d");
}
?>