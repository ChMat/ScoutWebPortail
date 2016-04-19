<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* portail_desactive.php - Le portail est totalement hors ligne
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
*	update.php devient portail_desactive.php
*	nettoyage code pour xhtml
*/

?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mise &agrave; jour en cours</title>
<meta name="robots" content="noindex, nofollow" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#avertissement_hors_ligne {
	text-align:center; position:absolute; width:50%; left:25%; top:100px; border:1px #666 dotted; padding:5px; background-color:#fffff2}
	h2 {margin-bottom:2em; color:#c30;}
#avertissement_explication {
	text-align:justify; position:absolute; width:50%; left:25%; bottom:100px; border:1px #666 dotted; padding:5px; background-color:#f3f3f3;}
a {text-decoration:none;}
	a strong {
		font-weight:bold;}
</style>
</head>

<body>
<div id="avertissement_hors_ligne">
<h2>Le portail est en cours de mise &agrave; jour...</h2>
<p>Merci de revenir d'ici quelques minutes</p>
      <?php
	  	$variables = $_SERVER['QUERY_STRING'];
		if (empty($variables))
		{
			$variables = 'index.php';
			$txtvar = 'Accueil du site';
		}
		else
		{
			$txtvar = $variables = 'index.php?'.$variables;
		}
	  ?>
  <p><a href="<?php echo $variables; ?>">R&eacute;essayer de charger la page :<br />
    <strong><?php echo $txtvar;?></strong></a></p>
  <p>Si tu es le webmaster, tu peux <a href="reactiver_portail.php">r&eacute;activer 
    le portail depuis cette page</a></p>
</div>
<div id="avertissement_explication"><p>En g&eacute;n&eacute;ral, la mise &agrave; 
  jour consiste en quelques modifications. Elles prennent quelques minutes mais 
  n&eacute;cessitent un test grandeur nature avant de permettre aux visiteurs 
  d'en profiter...</p>
  <p>Bref, le portail n'est pas mort depuis deux semaines !</p></div>
</body>
</html>
