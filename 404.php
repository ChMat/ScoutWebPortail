<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* 404.php v 1.1 - Page d'erreur affichée pour les utilisateurs non connectés
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
*	Modification du lien de retour avec le referer php plutôt que par javascript
*/


if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Erreur</title>
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
}
?>
<div class="msg">
<p align="center"><img src="templates/default/images/construction.gif" width="202" height="27" alt="Page encore à construire" /></p>
<p align="center" class="rmq">Ooops, cette page n'est pas disponible !</p>
<p align="center"><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Retour &agrave; la page
    pr&eacute;c&eacute;dente</a></p>
</div>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>
