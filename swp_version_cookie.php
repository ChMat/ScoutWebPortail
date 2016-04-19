<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* swp_version_cookie v 1.1.1 - Enregistrement sur un cookie de la dernière version publiée du portail
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
* Modifications v 1.1.1
*	Fichier ajouté dans la version 1.1.1 du portail
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] == 5)
{
	if (!empty($_GET['last_version']) and $_GET['last_version'] != $site['version_portail'])
	{ // Une mise à jour est disponible 
		// Le cookie reste valide 5 jours
		setcookie('last_version', $_GET['last_version'], time() + (3600 * 24 * 5));
	}
	else
	{ // La version du portail est à jour
		// On supprime le cookie avec le signalement éventuel d'une nouvelle version
		setcookie('last_version', $_GET['last_version'], time() - (3600 * 24 * 365));
	}
}
?>
