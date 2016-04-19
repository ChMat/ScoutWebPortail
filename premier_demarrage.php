<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* premier_demarrage.php - Cette page ne s'affiche que lorsque la gestion de l'unité
* ne contient aucune section ou unité.
* NE PAS supprimer ce fichier, même après création d'une unité
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
if ($user['niveau']['numniveau'] > 0 and !is_array($sections))
{ // affichage page si connecté
?>
<?php
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Premier d&eacute;marrage</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	}
?>
<h1>Bienvenue sur le Scout Web Portail !</h1>
<div class="introduction">
<p class="petitbleu">Dans quelques minutes, tu pourras profiter de toute la puissance 
  des outils du portail. Il ne te reste plus qu'une &eacute;tape &agrave; franchir&nbsp;: 
  la <strong>cr&eacute;ation d'une Unit&eacute;</strong> et, le cas &eacute;ch&eacute;ant, 
  des Sections qui en d&eacute;pendent.</p>
</div>
<div class="instructions">
<h2>Principe de fonctionnement du Portail</h2>
<h3>Pour une Unit&eacute; ou un groupe scout complet</h3>
<p>Le portail est con&ccedil;u pour accueillir un groupe scout ou guide compos&eacute; 
  de plusieurs Unit&eacute;s et Sections. Tu peux donc cr&eacute;er autant d'unit&eacute;s 
  que tu le souhaites sur le portail. Ensuite, chaque unit&eacute; peut comporter 
  autant de sections que tu le souhaites. La premi&egrave;re section cr&eacute;&eacute;e 
  dans l'unit&eacute; est la section &quot;Anciens&quot;. Elle est automatiquement 
  ajout&eacute;e au moment de la cr&eacute;ation de l'unit&eacute;. Tu pourras 
  y transf&eacute;rer tous les anciens membres de l'unit&eacute;.</p>
<p>Pour pouvoir fonctionner, le portail doit compter au moins une Unit&eacute;.</p>
<h3>Pour une seule Section</h3>
<p>Si tu comptes ne g&eacute;rer qu'une Section sur le portail, cr&eacute;e une 
  Unit&eacute; et ajoute ensuite la Section que tu veux g&eacute;rer. Une Unit&eacute; 
  ne fonctionne pas comme une Section, les fonctions sont donc adapt&eacute;es 
  en cons&eacute;quence.</p>
</div>
<form action="index.php" method="get" name="form1" id="form1">
  
  <input type="hidden" name="page" value="gestion_sections" />
  <input type="hidden" name="do" value="creerunite" />
  <div align="center">
    <input type="submit" name="Submit" value="Allez, c'est parti ! Cr&eacute;er une Unit&eacute;" />
  </div>
</form>
<?php
} // fin affichage page si connecté
else
{
	include('404.php');
}
?>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
?>