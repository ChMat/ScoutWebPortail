<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_user.php v 1.1 - Fonctions permettant de r�cup�rer des infos sur l'utilisateur
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
*	Reconnaissance automatique d'un utilisateur connect� d�s le premier chargement de page
*	Ajout fonction abonnement_newsletter()
*	Ajout param�tre optionnel $align pour la fonction show_avatar()
*	Externalisation de l'envoi d'email
*	Utilisation du 3e param�tre de getimagesize
*/


function actualise($numuser)
{ // Cette fonction met � jour l'identification de l'utilisateur avec l'heure de sa derni�re action	
	global $user, $db;
	if ($user['niveau']['numniveau'] > 0)
	{
		$sql = "UPDATE ".PREFIXE_TABLES."connectes SET connectea = current_timestamp WHERE user = '$numuser'";
		send_sql($db, $sql);
		$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET lastconnex = current_timestamp WHERE num = '$numuser'";
		send_sql($db, $sql);
	}
}

function user_info($idfournie = '')
{
	/* Cette fonction r�cup�re les donn�es de l'utilisateur connect�
	* L'identification de l'utilisateur est � double sens :
	* - Un cookie $_COOKIE['id'] sur l'ordinateur de l'utilisateur contenant une cl� al�atoire.
	*   Cette cl� est renouvel�e � chaque nouvelle identification de l'utilisateur.
	*   La cl� est g�n�r�e par la fonction cleunique() dans prv/fonc_securite.php
	*
	* - Les infos disponibles au sujet de la configuration du navigateur de l'utilisateur 
	*   sont compar�es � celles enregistr�es au moment de la connexion.
	*   Cette seconde 'identification' rend insuffisante l'obtention du cookie de l'utilisateur
	*   Il faut en plus obtenir et se faire passer pour exactement la m�me configuration que l'utilisateur officiel.
	*
	* Dans cette version de SWP, les sessions de PHP ne sont pas utilis�es.
	* Cependant, rien ne vous emp�che d'adapter le portail.
	*
	* Ici, la constante UN_PEU_DE_SEL est inutile en fait, mais �a fait joli :-)
	*
	*/
	global $db, $niveaux, $sections;
	$cookie_login = (isset($_COOKIE['x_login']) or isset($_COOKIE[COOKIE_ID])) ? 1 : 0; // d�termine si l'utilisateur accepte les cookies ou non
	// pc_user emp�che un utilisateur d'utiliser la m�me id en cookie avec deux configurations/navigateurs diff�rents
	$pc_user = $cookie_login.UN_PEU_DE_SEL.$_SERVER['HTTP_ACCEPT_CHARSET'].$_SERVER['HTTP_ACCEPT_ENCODING'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT_LANGUAGE'];
	// pour un utilisateur sans cookie, on ajoute l'ip dans pc_user
	$ip = $_SERVER['REMOTE_ADDR'];
	$pc_user = ($cookie_login == 0) ? md5($ip.$pc_user) : md5($pc_user);
	$idfournie = ereg_replace("[^a-z0-9]", '', $idfournie); // on emp�che de trafiquer l'id du cookie...
	$sql = '';
	if (empty($idfournie))
	{
		$sql = "SELECT num, pseudo, prenom, nom, totem_scout, quali_scout, totem_jungle, email, niveau, nivdemande, assistantwebmaster, numsection, banni, affaide, avatar, majprofildone, majprofildate, newpw FROM ".PREFIXE_TABLES."auteurs as a, ".PREFIXE_TABLES."connectes as b WHERE b.cookie_login = '0' AND b.ip = '$ip' AND b.user = a.num AND a.banni != '1' AND pc_user = '$pc_user' AND b.connectea > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '30' MINUTE)";
	}
	else
	{
		$sql = "SELECT num, pseudo, prenom, nom, totem_scout, quali_scout, totem_jungle, email, niveau, nivdemande, assistantwebmaster, numsection, banni, affaide, avatar, majprofildone, majprofildate, newpw FROM ".PREFIXE_TABLES."auteurs as a, ".PREFIXE_TABLES."connectes as b WHERE b.cookie_login = '1' AND b.id = '$idfournie' AND b.user = a.num AND a.banni != '1' and pc_user = '$pc_user'";
	}
	if (!empty($sql))
	{
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$data = mysql_fetch_assoc($res); // on place les donn�es de l'utilisateur connect� dans un array associatif
				$data['niveau'] = $niveaux[$data['niveau']];
				$data['section'] = $sections[$data['numsection']];
				return $data;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
	else
	{
		return 0;
	}
}

function show_avatar($membre, $align = '')
{ // retourne la balise <img> contenant l'avatar de l'utilisateur $membre
	global $db;
	// $membre peut �tre soit le nom du fichier contenant l'avatar
	// soit le num�ro de membre
	if (ereg ("^[0-9]+$", $membre) and !empty($membre)) // c'est le numero de membre
	{
		$sql = "SELECT avatar FROM ".PREFIXE_TABLES."auteurs WHERE num = '$membre'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$ligne = mysql_fetch_assoc($res);
			$membre = $ligne['avatar'];
		}
	}
	if (!empty($membre))
	{
		$monavatar = 'img/photosmembres/avatars/'.$membre;
		if (@file_exists($monavatar))
		{
			$taille = @getimagesize($monavatar);
			$taille = $taille[3];
			$align = (!empty($align)) ? ' align="'.$align.'"' : '';
			$retour = '<img src="'.$monavatar.'" '.$taille.' border="0" class="avatar" alt=""'.$align.' />';
		}
		else
		{
			$retour = '';
		}
	}
	else
	{
		$retour = '';
	}
	return $retour;
}

function save_record_connectes($nbre)
{
	global $db;
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '$nbre' WHERE champ = 'record_connectes'";
	send_sql($db, $sql);
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = CURRENT_TIMESTAMP() WHERE champ = 'date_record_connectes'";
	send_sql($db, $sql);
	reset_config();
}

function save_record_enligne($nbre)
{
	global $db;
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '$nbre' WHERE champ = 'record_enligne'";
	send_sql($db, $sql);
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = CURRENT_TIMESTAMP() WHERE champ = 'date_record_enligne'";
	send_sql($db, $sql);
	reset_config();
}

function deconnexion($numuser)
{	// cette fonction supprime l'identification d'un utilisateur sur le portail
	global $db;
	// on commence par supprimer son id dans la db des membres connect�s
	$sql = "DELETE FROM ".PREFIXE_TABLES."connectes where user = '$numuser'";	
	send_sql($db, $sql);
	/* on supprime le cookie contenant l'id du visiteur
	// afin d'�viter les doublons d'id ou les incoh�rences de connexion */
	setcookie('id', '', time() - (15 * 24 * 3600));
}

function log_this($event, $page = '', $prevenir_par_mail = false)
{	// Cette fonction enregistre les actions effectu�es par l'utilisateur connect�
	global $db, $user, $site;
	$event_sql = htmlentities(html_entity_decode($event, ENT_QUOTES), ENT_QUOTES);
	$sql = "INSERT INTO ".PREFIXE_TABLES."log_actions (numuser, page, h_action, description_action) values ('$user[num]', '$page', CURRENT_TIMESTAMP(), '$event_sql')";
	send_sql($db, $sql);
	// L'historique du log est all�g� de toutes les entr�es du log qui ont plus de 2 mois
	$sql = "DELETE FROM ".PREFIXE_TABLES."log_actions WHERE h_action < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '2' MONTH)";
	send_sql($db, $sql);
	if ($prevenir_par_mail and ENVOI_MAILS_ACTIF)
	{ // On signale l'�v�nement par mail au webmaster
		if (file_exists('prv/emailer.php'))
		{ // Charg� habituellement lors de l'ex�cution du portail
			@include_once('prv/emailer.php');
		}
		else if (file_exists('../prv/emailer.php'))
		{ // Charg� lors de l'update du portail (portail_update.php se trouve dans un sous-dossier)
			@include_once('../prv/emailer.php');
		}
		$courrier = new emailer();
		$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
		$reponse = $expediteur;
		$courrier->from($expediteur);
		$courrier->to($expediteur);
		$courrier->reply_to($expediteur);
		$courrier->use_template('log_action', 'fr');
		$pseudo_user = ($user > 0) ? $user['pseudo'] : 'Quelqu\'un';
		$courrier->assign_vars(array(
			'USER_PSEUDO' => $pseudo_user,
			'EVENT' => html_entity_decode($event, ENT_QUOTES),
			'ADRESSE_SITE' => $site['adressesite'],
			'WEBMASTER_PSEUDO' => $site['webmaster'],
			'WEBMASTER_EMAIL' => $site['mailwebmaster']));
		return $courrier->send();
		$courrier->reset();
	}
	else
	{
		return true;
	}
}

function abonnement_newsletter($email, $nom = '', $ajout_nouvelle_adresse = true)
{
	global $db, $site;
	if (!empty($email) and checkmail($email) and ENVOI_MAILS_ACTIF)
	{ 
		// on v�rifie si l'adresse est dans la mailing-liste
		$sql = "SELECT email FROM ".PREFIXE_TABLES."site_mailing_liste WHERE email = '".$email."'";
		$res = send_sql($db, $sql);
		$adresse_existante = (mysql_num_rows($res) == 0) ? false : true;

		if ($ajout_nouvelle_adresse and !$adresse_existante)
		{ // abonnement � la newsletter
			$sql = "INSERT INTO ".PREFIXE_TABLES."site_mailing_liste (nom, email, date_ajout, envoi_ok, ip_inscr) values ('$nom', '$email', now(), '1', '$_SERVER[REMOTE_ADDR]')";
			send_sql($db, $sql);
			return true;
		}
		else if ($ajout_nouvelle_adresse and $adresse_existante)
		{
			return false;
		}
		else if (!$ajout_nouvelle_adresse and $adresse_existante)
		{ // r�siliation d'abonnement
			$sql = "DELETE FROM ".PREFIXE_TABLES."site_mailing_liste WHERE email = '$email'";
			send_sql($db, $sql);
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

?>