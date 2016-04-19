<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* enligne.php v 1.1 - Affichage des utilisateurs et des membres en ligne
* Cette page est affichée dans la zone d'id 'onglet'
* Cette page n'est exploitée que si le log des visites est activé depuis la configuration du portail
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
*	persistance du log réduite à 7 jours
*/


include_once('connex.php');
// Le script de cette page est librement adapté depuis http://www.siteduzero.com/php/tp/connectes.php
// on récupère le cookie du visiteur
// s'il n'a pas de cookie, on lui en offre un pour l'identifier par la suite.
if (!isset($_COOKIE['visiteur']))
{
	$cle_v = md5(cleunique(4).time());
	setcookie('visiteur', $cle_v, time()+65*24*3600);
}
else
{
	$cle_v = $_COOKIE['visiteur'];
}
// On essaie d'éviter les spammers, les bots et les visiteurs qui n'acceptent pas les cookies
// Si une même IP a plus de 1 inscription dans le log, on considère le visiteur comme un spammer (bot ou no_cookie user)
// Si le script détecte un spammer, il n'update qu'une seule ligne dans le log.
$sql = 'SELECT COUNT(*) AS nbre_entrees FROM '.PREFIXE_TABLES.'log_visites WHERE ip=\''.$_SERVER['REMOTE_ADDR'].'\' AND h_fin > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'20\' MINUTE)';
$res = send_sql($db, $sql);
$donnees = mysql_fetch_assoc($res);
$spammer = ($donnees['nbre_entrees'] > 1) ? true : false;
// log de la visite
$requete = 'INSERT INTO '.PREFIXE_TABLES.'log_actions_visiteur VALUES (\''.$_SERVER['REMOTE_ADDR'].'\', \''.$_SERVER['REQUEST_URI'].'\', \''.$_SERVER['HTTP_USER_AGENT'].'\', CURRENT_TIMESTAMP())';
send_sql($db, $requete);
// On vérifie si le visiteur se trouve déjà dans la table avec trois criteres : 
// 		IP, dernière action il y a moins de 20 minutes, pseudo_stocke
// Pour faire ça, on n'a qu'à compter le nombre d'entrées sur ces trois critères
$check_num = ($user['num'] > 0) ? $user['num'] : 0;
$plus = (!$spammer) ? 'AND visiteur = \''.$_COOKIE['visiteur'].'\'' : '';
$sql = 'SELECT COUNT(*) AS nbre_entrees FROM '.PREFIXE_TABLES.'log_visites WHERE ip=\''.$_SERVER['REMOTE_ADDR'].'\' AND h_fin > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'20\' MINUTE) '.$plus.' ORDER BY h_dbt DESC';
$res = send_sql($db, $sql);
$donnees = mysql_fetch_assoc($res);
if ($donnees['nbre_entrees'] == 0)
{ // Le visiteur est nouveau, on l'ajoute
	$sql = 'INSERT INTO '.PREFIXE_TABLES.'log_visites VALUES(\''.$check_num.'\', \''.$_COOKIE['pseudo_stocke'].'\', \''.$cle_v.'\', \''.$_SERVER['REMOTE_ADDR'].'\', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), \'1\')';
	send_sql($db, $sql);
}
else
{ // Le visiteur se trouve déjà dans la table, on met l'heure et le nombre de clics à jours
	$pseudo = (!empty($_COOKIE['pseudo_stocke'])) ? 'pseudo_stocke = \''.$_COOKIE['pseudo_stocke'].'\', ' : '';
	$plus = (!$spammer) ? 'AND (visiteur = \''.$_COOKIE['visiteur'].'\' OR visiteur = \''.$cle_v.'\')' : '';
	$connecte = ($check_num > 0) ? 'numuser = \''.$check_num.'\',' : '';
	$sql = 'UPDATE '.PREFIXE_TABLES.'log_visites SET '.$pseudo.' '.$connecte.' h_fin = CURRENT_TIMESTAMP(), nbre_clics = nbre_clics + 1 WHERE ip = \''.$_SERVER['REMOTE_ADDR'].'\' AND h_fin > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'20\' MINUTE) '.$plus.' LIMIT 1';
	send_sql($db, $sql);
}
// On supprime les visiteurs qui sont passés il y a plus de 7 jours
$sql = 'DELETE FROM '.PREFIXE_TABLES.'log_visites WHERE h_fin < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'7\' DAY)';
send_sql($db, $sql);

// On supprime les actions des visiteurs d'il y a plus de 20 jours
$requete = 'DELETE FROM '.PREFIXE_TABLES.'log_actions_visiteur WHERE time < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'7\' DAY)';
send_sql($db, $requete);


// On compte le nombre de visiteurs présents sur le portail il y a moins de 2 minutes
$sql = 'SELECT COUNT(*) AS nbre_entrees FROM '.PREFIXE_TABLES.'log_visites WHERE h_fin > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL \'2\' MINUTE)';
$retour = send_sql($db, $sql);
$donnees = mysql_fetch_assoc($retour);

// On enregistre le record de visiteurs en ligne le cas échéant
$site['enligne'] = $donnees['nbre_entrees'];
if ($donnees['nbre_entrees'] > $site['record_enligne'])
{
	save_record_enligne($donnees['nbre_entrees']);
}
?>