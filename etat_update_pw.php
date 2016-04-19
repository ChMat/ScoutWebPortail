<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* etat_update_pw.php v 1.1 - Gestion de la mise à jour des mots de passe à la version 1.1 du portail
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
if ($user['niveau']['numniveau'] < 5 or $site['update_pw'] != 'en_cours')
{
	include('404.php');
	exit;
}
if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Suivi de la mise à jour des mots de passe utilisateur</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
}
?>
<h1>Suivi de la mise &agrave; jour des mots de passe utilisateur</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'accueil Membres</a></p>
<div class="instructions">
  <p>Dans le cadre de la mise &agrave; jour &agrave; la version <?php echo $site['version_portail']; ?> du
    portail, le hashage des mots de passe utilisateur est renforc&eacute;.</p>
  <p>Ce hashage ne peut se produire qu'&agrave; la connexion d'un utilisateur
    car le cryptage des mots de passe est &agrave; sens unique (un hashage ne
    permet pas de retrouver la cha&icirc;ne de caract&egrave;res originale).</p>
  <p>Ci-dessous, tu peux voir la liste des utilisateurs dont le mot de passe
    n'est pas encore &agrave; jour.</p>
  <p><span class="rmq">Note :</span> Pour se mettre &agrave; jour, il leur suffit de s'identifier une fois sur
    le portail et la mise &agrave; jour se fait de mani&egrave;re totalement transparente.
    Ils n'ont rien de sp&eacute;cial &agrave; faire. </p>
</div>
<div class="form_config_site update_pw">
<h2>Utilisateurs pas encore &agrave; jour </h2>
<?php
$sql = "SELECT b.pseudo, b.email FROM ".PREFIXE_TABLES."auteurs_pw_v11 as a, ".PREFIXE_TABLES."auteurs as b WHERE a.num = b.num ORDER BY b.pseudo ASC";
$res = @send_sql($db, $sql);
if (@mysql_num_rows($res) > 0)
{
?>
<p>Les utilisateurs dans la liste ci-dessous ne se sont pas encore connect&eacute;s depuis la mise &agrave; jour du portail.</p>
<ol>
<?php
	while ($ligne = mysql_fetch_assoc($res))
	{
?>
  <li><a href="mailto:<?php echo $ligne['email']; ?>" title="lui écrire un mail"><img src="templates/default/images/mail.png" alt="lui écrire un mail" border="0" align="middle" /></a> <?php echo $ligne['pseudo']; ?></li>
<?php
	}
?>
</ol>
<p>Inutile de presser les utilisateurs &agrave; se connecter. Le script
  sera disponible jusqu'&agrave; la prochaine version du portail. Et le cas &eacute;ch&eacute;ant,
  tu peux modifier leur mot de passe depuis l'interface de Gestion des Membres
  du portail. </p>
<?php
}
else
{
?>
<p align="center" class="rmqbleu">Tous les utilisateurs sont  &agrave; jour. </p>
<?php
}
?>
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