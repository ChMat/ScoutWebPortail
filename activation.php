<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* activation.php v 1.1 - reconnaissance du statut des nouveaux membres (appelé par activ.php)
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
*	Externalisation du texte des mails
*/


include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] > 2 and isset($_POST['nbre']))
{
	$nbre = $_POST['nbre']; // nbre d'utilisateurs à reconnaître ou à rejeter
	if ($nbre > 0)
	{
		$niveau_user = $user['niveau']['numniveau']; // récupération du niveau de l'utilisateur connecté
		$garant = $user['pseudo']; // récupération de son pseudo
		$mb_ok = $mb_banni = '';
		for ($x = 1; $x <= $nbre; $x++)
		{ // on parcourt tous les utilisateurs à valider 
			// $x correspond au $x eme membre à autoriser dans le formulaire
			// on récupère ici le nom des champs du formulaire dynamique
			// leur valeur est étudiée dans le if qui suit.
			$caseencours = 'util-'.$x;
			$p = $_POST[$caseencours];
			$regs = '';
			if (ereg("^([0-9x]+)-([0-9]+)$", $p, $regs))
			{ 
				// la valeur postée est sous la forme m-n
				// m est soit un chiffre (niveau demandé accordé), soit la lettre x (niveau refusé -> user banni)
				// n est le numéro d'utilisateur
				if ($regs[1] != 'x' and $niveaux[$regs[1]]['numniveau'] <= $niveau_user)
				{
					// l'utilisateur est reconnu dans le statut qu'il a demandé
					$section_user = $niveaux[$regs[1]]['section_niveau'];
					$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET niveau = \''.$regs[1].'\', nivdemande = \'0\', numsection = \''.$section_user.'\', banni = \'0\', autorise = \''.$garant.'\' WHERE num = \''.$regs[2].'\'';
					send_sql($db, $sql);
					$sql = 'SELECT email, prenom, pseudo FROM '.PREFIXE_TABLES.'auteurs WHERE num = \''.$regs[2].'\'';
					$res = send_sql($db, $sql);
					$ligne = mysql_fetch_assoc($res);
					log_this('Statut de '.$ligne['pseudo'].' reconnu (Membre '.$regs[2].')', 'activation');
					if (ENVOI_MAILS_ACTIF)
					{ // envoi d'un mail au membre reconnu
						include_once('prv/emailer.php');
						$courrier = new emailer();
						$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
						$reponse = $expediteur;
						$courrier->from($expediteur);
						$courrier->to($ligne['email']);
						$courrier->reply_to($expediteur);
						$courrier->use_template('validation_ok', 'fr');
						$courrier->assign_vars(array(
							'USER_PRENOM' => $ligne['prenom'],
							'USER_STATUT' => $niveaux[$regs[1]]['nomniveau'],
							'ADRESSE_SITE' => $site['adressesite'],
							'WEBMASTER_PSEUDO' => $site['webmaster'],
							'WEBMASTER_EMAIL' => $site['mailwebmaster']));
						$courrier->send();
						$courrier->reset();
					}
				}
				else
				{
					// le membre n'est pas reconnu au niveau qu'il demande, son compte est banni
					$sql = 'UPDATE '.PREFIXE_TABLES.'auteurs SET niveau = \'0\', nivdemande = \'0\', banni = \'1\', autorise = \''.$garant.'\' WHERE num = \''.$regs[2].'\'';
					send_sql($db, $sql);
					$sql = 'SELECT email, prenom, pseudo FROM '.PREFIXE_TABLES.'auteurs WHERE num = \''.$regs[2].'\'';
					$res = send_sql($db, $sql);
					$ligne = mysql_fetch_assoc($res);
					log_this('Statut de '.$ligne['pseudo'].' non reconnu (Membre '.$regs[2].' banni)', 'activation');
					if (ENVOI_MAILS_ACTIF)
					{ // envoi mail au membre rejeté
						include_once('prv/emailer.php');
						$courrier = new emailer();
						$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
						$reponse = $expediteur;
						$courrier->from($expediteur);
						$courrier->to($ligne['email']);
						$courrier->reply_to($expediteur);
						$courrier->use_template('validation_echec', 'fr');
						$courrier->assign_vars(array(
							'USER_PRENOM' => $ligne['prenom'],
							'GARANT_PSEUDO' => $garant,
							'ADRESSE_SITE' => $site['adressesite'],
							'WEBMASTER_PSEUDO' => $site['webmaster'],
							'WEBMASTER_EMAIL' => $site['mailwebmaster']));
						$courrier->send();
						$courrier->reset();
					}
				}
			}
		}
		header('Location: index.php?page=activ&done=1');
		exit;
	}
	else
	{
		header('Location: index.php?page=membres');
		exit;
	}
}
else
{
	header('Location: index.php');
	exit;
}
?>