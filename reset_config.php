<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* reset_config.php v 1.1 - Regénération du fichier de configuration du portail
* Mis en cache dans config.php
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
*	Prise en charge des erreurs dans reset_config()
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] == 5)
{
	if (reset_config())
	{
?>
<h1>R&eacute;initialisation de la configuration du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la Page d'Accueil Membres</a></p>
<div class="msg">
<p align="center">La configuration vient d'&ecirc;tre recharg&eacute;e.</p>
</div>
<?php
	}
	else
	{
?>
<h1>R&eacute;initialisation de la configuration du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la Page d'Accueil Membres</a></p>
<div class="msg">
<p class="rmq" align="center">Impossible de r&eacute;&eacute;crire le fichier <strong>config.php</strong> !</p>
</div>
<?php
	}
}
else
{
	include('404.php');
}
?>