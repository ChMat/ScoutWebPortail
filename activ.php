<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* activ.php v 1.1 - Formulaire permettant aux membres du portail de reconnaître le statut des nouveaux membres
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
*	Chargement fonctions avancées ici plutôt que dans fonc.php à chaque fois
*/

include_once('prv/fonc_moteurs.php'); // chargement fonctions avancées du portail

if (defined('IN_SITE') and ($user['niveau']['numniveau'] == 5 or ($site['droits_anim_valide_statut'] == 1 and $user['niveau']['numniveau'] > 2)))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Activation de membres</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body bgcolor="#FFFFFF">
<?php
	}
?>
<h1>Valider le statut d'animateur</h1>
<?php
	if ($_GET['done'] == 1)
	{
?>
<div class="msg">
  <p align="center" class="rmq">L'op&eacute;ration de validation a &eacute;t&eacute; 
  effectu&eacute;e avec succ&egrave;s.</p>
  <p align="center"><a href="index.php?page=membres">Retour à la page d'accueil 
    Membres</a></p>
</div>
<?php
	}
	else
	{
		if (membresaautoriser('nbr'))
		{
?>
<p align="center"><a href="index.php?page=membres">Retour à la page d'accueil Membres</a></p>
<form action="activation.php" method="post" name="validationanimateurs" id="validationanimateurs" onsubmit="return confirm('Es-tu certain de ton choix ?');" class="form_config_site">
<h2>Instructions</h2>
<p>Le statut d'animateur sur le site donne acc&egrave;s aux donn&eacute;es 
  priv&eacute;es des membres de l'Unit&eacute;. C'est pourquoi les membres 
  qui demandent ce statut doivent &ecirc;tre reconnus avant de l'obtenir.</p>
<p>Consulte la liste des membres en attente de reconnaissance ci-dessous et 
  donne les autorisations aux utilisateurs de bonne foi.</p>
<p class="rmq">Remarques</p>
<p>En reconnaissant un utilisateur, tu lui donnes acc&egrave;s &agrave; tous 
  les outils destin&eacute;s aux animateurs.<br />
  Si tu d&eacute;couvres un fraudeur dans la liste, coche la case 'Suppr.', 
  son compte sera banni.</p>
<?php
		if (ENVOI_MAILS_ACTIF)
		{
?>
<p>L'adresse email des utilisateurs est v&eacute;rifi&eacute;e.</p>
<p>Comment reconna&icirc;tre un fraudeur ?</p>
<ul>
  <li>l'adresse email ne te dit rien ou ne correspond pas au nom et au 
	pr&eacute;nom qui y sont associ&eacute;s.</li>
  <li>les nom et pr&eacute;nom te semblent farfelus ou n'appartiennent 
	pas &agrave; l'Unit&eacute;.</li>
  <li>...</li>
</ul>
<?php
		}
		else
		{
?>
<p><span class="rmq">Les adresses email ne sont pas v&eacute;rifi&eacute;es</span>. 
  Pour v&eacute;rifier si la personne qui s'est inscrite est effectivement 
  qui elle dit &ecirc;tre, envoie lui un email ou demande-lui de vive voix 
  si c'est bien elle qui s'est inscrite.</p>
<?php
		}
?>
<p><span class="rmq">Dans le doute</span>, ne coche aucune des deux cases 
  et laisse la t&acirc;che &agrave; un autre animateur.</p>
<h2>Membres &agrave; reconna&icirc;tre</h2>
<?php
			membresaautoriser('show');
?>
<p class="petitbleu" align="center"> En cliquant sur 'valider', tu te portes garant de la bonne foi des 
  membres que tu as reconnus.</p>
<p align="center">
  <input type="submit" value="Valider" class="bouton" />
  <input type="reset" name="Reset" value="Recommencer" />
</p>
</form>

<?php
		}
		else
		{
?>
<div class="msg">
<p align="center">Il n'y a aucun membre en attente.</p>
</div>
<?php
		}
	}
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
}
else
{
	include('404.php');
}
?>