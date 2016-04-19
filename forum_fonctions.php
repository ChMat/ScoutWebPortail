<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum_fonctions.php v 1.1 - Fonctions spéciales pour le forum
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
*	Nouveau fichier ajouté dans swp v 1.1
* Modifications v 1.1.1
*	Bug 3/12 - Les animateurs ne pouvaient pas modérer le forum
*/

// Paramètres d'accès
/************************************************/

// forum_statut : 0 - fermé, 1 - ouvert, 2 - verrouillé webmaster, 3 - verrouillé modérateurs
// fil_statut : 0 - fermé, 1 - ouvert, 2 - verrouillé webmaster, 3 - verrouillé modérateurs
// msg_statut : 0 - ok, 1 - banni


function limite_acces_forum($forum, $lire_ecrire = 'lire')
{ // en fonction des paramètres d'un forum, dit si l'utilisateur peut lire ou écrire dans le forum
  // renvoie true ou false
	global $user, $sections;
	
	$statut = $forum['forum_statut'];
	$niveau = $forum['forum_acces_niv'];
	$section = $forum['forum_acces_section'];
	$moderation = $forum['forum_moderation'];
	
	if ($user['niveau']['numniveau'] == 5)
	{ // le webmaster peut tout faire
		return true;
	}
	
	if ($niveau == 0 and $statut != 0 and $lire_ecrire == 'lire')
	{ // le forum est en accès public
		return true;
	}
	
	if ($user['niveau']['numniveau'] >= $niveau)
	{ // l'utilisateur a le bon niveau
		if ($section == 0 or $user['numsection'] == (-1 * $section) or $user['numsection'] == $section or (is_unite($section) and $sections[$user['numsection']]['unite'] == $section))
		{ // l'utilisateur est dans la bonne section
			if ($statut == 0)
			{ // le forum est fermé
				return false;
			}
			else if ($statut == 1)
			{ // le forum est ouvert
				return true;
			}
			else if ($statut == 2)
			{ // le forum est en lecture seule
				return ($lire_ecrire == 'lire') ? true : false;
			}
			else if ($statut == 3 and $moderation > 0)
			{ // le forum n'est accessible en écriture que pour les modérateurs
			  // les autres ne peuvent que le lire
				if ($user['niveau']['numniveau'] >= $moderateur or $lire_ecrire == 'lire')
				{ // le modérateur peut écrire et l'utilisateur peut lire
					return true;
				}
				else
				{ // l'utilisateur ne peut pas écrire
					return false;
				}
			}
			else
			{ // seul le webmaster a accès au forum
				return false;
			}
		}
		else
		{ // La section de l'utilisateur n'a pas accès au forum
			return false;
		}
	}
	else
	{ // l'utilisateur a un niveau trop bas
		return false;
	}
}

function limite_acces_fil($fil_et_forum, $lire_ecrire = 'lire')
{ // en fonction des paramètres du fil, dit si l'utilisateur peut y lire ou y écrire
  // renvoie true ou false
	global $user, $sections;
	
	$statut = $fil_et_forum['fil_statut'];
	$forum_moderation = $fil_et_forum['forum_moderation'];
	$fil_auteur = $fil_et_forum['fil_auteur'];
	
	if ($user['niveau']['numniveau'] == 5)
	{ // le webmaster peut tout faire
		return true;
	}
	
	if ($statut == 0)
	{ // le fil est fermé
		return false;
	}
	else if ($statut == 1)
	{ // le fil est ouvert
		return true;
	}
	else if (($statut == 2 or $statut == 3) and $lire_ecrire == 'lire')
	{ // le fil est verrouillé mais peut être affiché
		return true;
	}
	else if ($statut == 2 and $lire_ecrire == 'ecrire')
	{ // le fil est verrouillé (seul le webmaster peut y poster mais il a déjà la réponse)
		return false;
	}
	else if ($statut == 3 and $lire_ecrire == 'ecrire')
	{ // le fil est verrouillé (seuls les modérateurs peuvent y poster)
		return is_moderateur($moderation, $fil_auteur);
	}
	else
	{
		return false;
	}
}

function is_moderateur($moderation, $auteur_discussion = 0)
{ // en fonction des paramètres d'un forum, dit si l'utilisateur peut modérer le fil ou le forum
  // l'accès en lecture/écriture au forum est implicite (vérifié par limite_acces_forum() )
  // quand $auteur_discussion == 0, il s'agit de la modération générale du forum en cours
  // renvoie true ou false
	global $user;
	/* Niveaux de modération
		0 Le webmaster uniquement
		1 L'auteur de la discussion
		2 L'auteur de la discussion et les animateurs
		3 Les animateurs de section
		4 Les animateurs d'unité
	*/
	if ($user['niveau']['numniveau'] == 5)
	{ // le webmaster peut tout faire
		return true;
	}
	else if ($moderation == 0)
	{ // le webmaster est le seul modérateur
		return false;
	}
	else if (($moderation == 3 or $moderation == 4) and $user['niveau']['numniveau'] == 4)
	{ // l'animateur d'unité peut modérer le forum
		return true;
	}
	else if (($moderation == 3 or $moderation == 2) and $user['niveau']['numniveau'] >= 3)
	{ // les animateurs peuvent modérer le forum
		return true;
	}
	else if (($moderation == 2 or $moderation == 1) and $user['num'] == $auteur_discussion)
	{ // l'auteur de la discussion peut modérer le forum
		return true;
	}
	else
	{ // l'utilisateur n'est pas modérateur
		return false;
	}
}
?>