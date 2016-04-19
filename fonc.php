<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc.php v 1.1 - Chargement de la configuration du portail + fonctions de base du portail
* Nécessite d'avoir la connexion à la db ouverte (par le fichier connex.php appelé au préalable)
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
*	Ajout des fonctions count_sections() et count_unites()
*	Ajout du niveau d'error_reporting de php (on supprime les notice)
*	Passage des require en require_once pour install.php
*	Suppression de prv/fonc_securite.php. La fonction qu'il contenait est intégrée à fonc.php
*	prv/fonc_moteurs.php n'est plus appelé que si c'est nécessaire et ce aux endroits où il est nécessaire
*		via la fonction include_once()
*	Ajout paramètre mini (pour la validation des inscriptions membres) dans la fonction cleunique()
*	Modification de la fonction pour recharger la configuration du portail
*/
/*
 * Modification v 1.1.1
 * 	build 091109 : correction d'un bug de compatibilité avec nouveaux paramètres PHP 5
 */

// on désactive l'affichage des notice pour les variables non initialisées
error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once('prv/fonc_sql.php'); // fonctions SQL de base
require_once('prv/fonc_str.php'); // fonctions texte
include_once('layout_tools.php'); // fonctions de mise en page du portail (boutons gras, italique, liens, ...)
require_once('prv/fonc_date.php'); // fonctions sur les dates
require_once('prv/fonc_user.php'); // fonctions sur les utilisateurs

// Constantes du portail

$intitules_niveaux = array('Anonyme', 'Visiteur ext&eacute;rieur', 'Membre de l\'Unit&eacute;', 'Animateur de Section', 'Animateur d\'Unit&eacute;', 'Webmaster');
$statuts = array('', 'Second', 'Sizenier', 'SP', 'CP');
$f_sizaine = array('', 'Sizaine', 'Patrouille');
$f_sizaines = array('', 'Sizaines', 'Patrouilles');
$ip = $local_addr = $_SERVER['REMOTE_ADDR'];

if ($ip == '127.0.0.1')
{ // Le site est consultÃ© en local
	define('LOCAL_SITE', true);
	$requetes = 0; // nbre de requÃªtes
	$txtrequetes = ''; // texte des requÃªtes
}

/** fonctions diverses
********************************/

function cleunique($taille = 20)
{ // retourne une clé aléatoire de $taille caractères
	$cle = '';
	// avec la taille 'mini', on ne met pas les lettres o, i et l.
	// Elles sont parfois difficiles à différencier selon les polices de caractères
	$lettres = ($taille != 'mini') ? 'oil' : ''; 
	$lettres .= 'abcdefghjkmnpqrstuvwxyz0123456789';
	$taille = ($taille == 'mini') ? 5 : $taille;
	srand(time());
	for ($i=0; $i < $taille; $i++)
	{
		$cle .= substr($lettres,(rand()%(strlen($lettres))), 1);
	}
	return $cle;
}

function check_cle_unique($cle)
{ // cette fonction vérifie que la clé unique est réellement unique pour identifier l'utilisateur
	if (!empty($cle))
	{
		global $db;
		$sql = "SELECT id FROM ".PREFIXE_TABLES."connectes WHERE id = '$cle'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}

function taille_fichier($fichier)
{ // renvoie la taille d'un fichier exprimée en octets, kilo-octets, méga-octets ou giga-octets.
  // $fichier peut être le chemin vers un fichier ou son poids en octets
	if (!ereg ("^[0-9\.]+$", $fichier)) {$taille = @filesize($fichier) or $taille = 0;} else {$taille = $fichier;}
	if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . ' Go';}
	elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . ' Mo';}
	elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . ' Ko';}
	else {$taille = $taille.' octets';} 
	if($taille == 0) {$taille = '0 octet';}
	$taille = str_replace('.', ',', $taille); // on remplace le . des décimales par une virgule française :)
	return $taille;
}

function aff_array($tableau)
{ // Affiche le code php permettant de définir un array à deux dimensions maximum
  // Cette fonction est utilisée dans la mise en cache de la config du site
	if (is_array($tableau))
	{
		$liste_config = 'Array(';
		$dejavaleur = false;
		foreach($tableau as $cle => $valeur)
		{
			$liste_config .= ($dejavaleur) ? ', ' : '';
			$dejavaleur = true;
			$liste_config .= "\"$cle\" => ";
			if (is_array($valeur))
			{
				$dejavaleur_bis = false;
				foreach ($valeur as $cle_bis => $valeur_bis)
				{
					$liste_config .= ($dejavaleur_bis) ? ",\n " : "Array(";
					$dejavaleur_bis = true;
					$liste_config .= "\"$cle_bis\" => \"".addslashes($valeur_bis)."\"";
				}
				$liste_config .= ")";
			}
			else
			{
				$liste_config .= "\"".addslashes($valeur)."\"";
			}
		}
		$liste_config .= ")";
		return $liste_config;
	}
	else
	{
		return 0;
	}
}

function strip_clean_array($tableau)
{ // nettoyage des slashes d'un array à deux dimensions max
	if (is_array($tableau))
	{
		foreach($tableau as $cle => $valeur)
		{
			if (is_array($valeur))
			{
				foreach ($valeur as $cle_bis => $valeur_bis)
				{
					$tableau[$cle][$cle_bis] = stripslashes(stripslashes($valeur_bis));
				}
			}
			else
			{
				$tableau[$cle] = stripslashes(stripslashes($valeur));
			}
		}
		return $tableau;
	}
	else
	{
		return false;
	}
}

function cmp_bis($a, $b)
{ // fonctions de tri à deux conditions pour array associatif
	// cette fonction ne doit pas être appelée directement
	// elle est appelée par la fonction super_sort($tableau)
	// $tri est un array à deux valeurs : ce sont les noms des champs de tri de l'array associatif
	// le tri de l'array se fait d'abord sur $tri[0] et ensuite sur $tri[1]
	global $tri;
	if ($a[$tri[0]] == $b[$tri1[0]]) 
	{
		return ($a[$tri[1]] > $b[$tri[1]]) ? 1 : -1;
	}
	else
	{
		return ($a[$tri[0]] > $b[$tri[0]]) ? 1 : -1;
	}
}

function super_sort($tableau)
{ 
	// Cette fonction permet de trier un array associatif sur deux critères
	// L'exemple le plus clair est le tri d'une liste de personnes sur leur nom puis leur prénom
	// Pour appeler cette fonction, deux actions doivent être réalisées :
	// - définir l'array $tri = array('nom', 'prenom');
	// - appeler la fonction $tableau_trie = super_sort($tableau_a_trier);
	usort($tableau, 'cmp_bis');
	reset($tableau);
	return $tableau;
}

function is_unite($index, $retour_numsection = false)
{ // is_unite détermine si une section de la db est une unité et a donc des sections enfantes)
  // si c'est le cas et que $retour_numsection est true, un array est renvoyé avec le num de chaque section
  // sinon, une simple valeur booléenne est renvoyée
  	global $sections;
	if ($sections[$index]['unite'] == 0)
	{
		if ($retour_numsection)
		{
			foreach($sections as $section)
			{
				if ($section['unite'] == $sections[$index]['numsection'] and !$section['anciens'])
				{
					// on inclut dans l'array le num de chaque section active
					// la section "anciens" n'est pas incluse
					$array[] = $section['numsection'];
				}
			}
			return $array;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}

function is_section_anciens($num)
{ // renvoie true si le $num de section est une section anciens
	global $sections;
	return ($sections[$num]['anciens'] == 1) ? true : false;
}

function liste_sections_anciens()
{ // cette fonction renvoie la liste des sections d'anciens par unité avec l'index de l'unité en indice
	global $sections;
	$liste = false;
	foreach ($sections as $section)
	{
		if ($section['anciens'] == 1)
		{
			$liste[$section['unite']] = $section['numsection'];
		}
	}
	return (is_array($liste)) ? $liste : false;
}

function count_unites()
{ // retourne le nombre d'unités présentes sur le portail
	global $sections;
	$nbre_unites = 0;
	foreach($sections as $section)
	{
		if (is_unite($section['numsection']))
		{
			$nbre_unites++;
		}
	}
	return $nbre_unites;
}

function count_sections($avec_anciens = false)
{ // retourne le nombre de sections présentes sur le portail
  // et inclut ou non les sections anciens
	global $sections;
	$nbre_sections = 0;
	foreach($sections as $section)
	{
		if (!is_unite($section['numsection']))
		{
			if (($avec_anciens and is_section_anciens($section['numsection'])) or !is_section_anciens($section['numsection']))
			{
				$nbre_sections++;
			}
		}
	}
	return $nbre_sections;
}

function count_sites_sections()
{ // retourne le nombre d'espaces web de sections activés
	global $sections;
	$nbre_sites_sections = 0;
	foreach($sections as $section)
	{
		if (!empty($section['site_section']))
		{
			$nbre_sites_sections++;
		}
	}
	return $nbre_sites_sections;
}

function reset_config()
{ // pour recharger le cache de la config du site, il suffit de vider le fichier de cache (config.php)
  // la remise en cache se fait automatiquement ci-dessous
	if ($new_config = @fopen('config.php', 'w'))
	{
		@fclose($new_config);
		return true;
	}
	else
	{
		return false;
	}
}


if (!file_exists('config.php') or filesize('config.php') <= 50)
{ // Chargement de la configuration du portail
	// 
	// configuration de base du portail
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'config';
	$site = array('loaded' => true);
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			// chaque champ de configuration est intégré dans l'array $site
			$site[$ligne['champ']] = $ligne['valeur'];
		}
		if (mysql_num_rows($res) == 0)
		{
			$site = false;
		}
	}

	// les menus du portail
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'site_menus ORDER BY section_menu, position_menu';
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			$site_menus[$ligne['id_menu']] = $ligne;
		}
		if (mysql_num_rows($res) == 0)
		{
			$site_menus = false;
		}
	}

	// Chargement de la configuration de l'Unité
	// les sections des unités
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'unite_sections';
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			$sections[$ligne['numsection']] = $ligne;
		}
		if (mysql_num_rows($res) == 0)
		{
			$sections = false;
		}
	}

	// les patrouilles et sizaines des unités
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'unite_sizaines ORDER BY section_sizpat, nomsizaine ASC';
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			$sizaines[$ligne['numsizaine']] = $ligne;
		}
		if (mysql_num_rows($res) == 0)
		{
			$sizaines = false;
		}
	}

	// les fonctions dans les unités
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'unite_fonctions';
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			$fonctions[$ligne['numfonction']] = $ligne;
		}
		if (mysql_num_rows($res) == 0)
		{
			$fonctions = false;
		}
	}

	// les statuts des membres du portail
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'site_niveaux';
	if ($res = send_sql($db, $sql))
	{
		while ($ligne = mysql_fetch_assoc($res))
		{
			$niveaux[$ligne['idniveau']] = $ligne;
		}
		if (mysql_num_rows($res) == 0)
		{
			$niveaux = false;
		}
	}
	// on met la configuration du portail en cache histoire de ménager la db
	if ($fconfig = @fopen('config.php', 'w'))
	{ // mise en cache de la configuration du portail
		@fwrite($fconfig, "<"."?"."php\n");
		@fwrite($fconfig, '// Fichier de configuration genere automatiquement par Scout Web Portail v '.$site['version_portail']);
		@fwrite($fconfig, "\n\$site = ".aff_array($site).';');
		@fwrite($fconfig, "\n\$site_menus = ".aff_array($site_menus).';');
		@fwrite($fconfig, "\n\$sections = ".aff_array($sections).';');
		@fwrite($fconfig, "\n\$fonctions = ".aff_array($fonctions).';');
		@fwrite($fconfig, "\n\$sizaines = ".aff_array($sizaines).';');
		@fwrite($fconfig, "\n\$niveaux = ".aff_array($niveaux).";\n?".">");
		@fclose($fconfig);
		@chmod('config.php', 0644);
	}
	else
	{ // mise en cache impossible, le webmaster est averti depuis la page d'accueil membres
		define('NO_WRITE_DOSSIER_PORTAIL', true);
	}
}
@require_once('config.php');
$site = strip_clean_array($site);
$site_menus = strip_clean_array($site_menus);
$sections = strip_clean_array($sections);
$niveaux = strip_clean_array($niveaux);
$fonctions = strip_clean_array($fonctions);
$sizaines = strip_clean_array($sizaines);

// La fonction mail() native de php est-elle active ?
$etat_mail = ($site['envoi_mails_actif'] == 1) ? true : false;
define('ENVOI_MAILS_ACTIF', $etat_mail);

// Préalable de sécurité on déconnecte les connexions sans cookies qui sont inactives depuis plus de 30 minutes
$sql = 'DELETE FROM '.PREFIXE_TABLES.'connectes WHERE connectea < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'30\' MINUTE) AND cookie_login = \'0\'';
send_sql($db, $sql);

/////////////////////////////
// Et pour terminer : Chargement des données utilisateur
/////////////////////////////

if (!defined('COOKIE_ID'))
{ // avant la version 1.1 du portail, le cookie d'identification s'appelait 'id'
  // COOKIE_ID est désormais défini dans le fichier connex.php créé à l'installation
	define('COOKIE_ID', 'id');
}

$user = 0; // on met tout à 0 pour empêcher les fausses connexions

// et c'est parti
$user = user_info($_COOKIE[COOKIE_ID]);
if ($user != 0)
{ // l'utilisateur est reconnu on rafraichit ses données de connexion
	actualise($user['num']);
}

// Constantes du portail postconnexion
$t_sizaines = ($user['niveau']['numniveau'] == 3 and $sections[$user['numsection']]['sizaines'] > 0) ? $f_sizaines[$sections[$user['numsection']]['sizaines']] : 'sizaines/patrouilles';
$t_sizaine = ($user['niveau']['numniveau'] == 3 and $sections[$user['numsection']]['sizaines'] > 0) ? $f_sizaine[$sections[$user['numsection']]['sizaines']] : 'sizaine/patrouille';

if ($site['log_visites'] == 1)
{ // calcul des personnes en ligne sur le site (l'affichage de ces infos se fait via onglet.php)
	include_once('enligne.php');
}
?>