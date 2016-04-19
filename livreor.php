<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* livreor.php v 1.1 - Livre d'or du portail
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
*	Les messages du livre d'or sont affichés par défaut
*/
/*
 * Modification v 1.1.1
 * 	build 091109 : correction d'un bug de compatibilité avec nouveaux paramètres PHP 5
 */


include_once('connex.php');
include_once('fonc.php');
if (defined('IN_SITE'))
{
?>
<div id="livreor">
<?php
}
if ($_POST['do'] == 'send')
{ // Enregistrement du message posté par l'utilisateur
	$auteur = htmlentities($_POST['auteur'], ENT_QUOTES);
	$email = htmlentities($_POST['email'], ENT_QUOTES);
	$message = htmlentities($_POST['message'], ENT_QUOTES);
	if (!empty($auteur) and (checkmail($email) or empty($email)))
	{ // l'utilisateur doit entrer au moins son pseudo
	  // s'il encode une adresse email, elle doit être valide
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = "INSERT INTO ".PREFIXE_TABLES."livreor (auteur, email, message, datecreation, ip, banni) values ('$auteur', '$email', '$message', now(), '$ip', '0')";
		send_sql($db, $sql);
		if ($_POST['abo'] == 1)
		{ // abonnement à la newsletter
			abonnement_newsletter($email, $auteur);
		}
		header('Location: index.php?page=livreor&do=merci');
	}
	else
	{ // pseudo ou mail incorrect
		header('Location: index.php?page=livreor&erreur=1');
	}
}
else if ($_GET['do'] == 'doban' and is_numeric($_GET['num']) and $user['niveau']['numniveau'] > 2)
{ // bannissement d'un message du livre d'or
	$sql = "UPDATE ".PREFIXE_TABLES."livreor SET banni = '1' WHERE num = '$_GET[num]'";
	send_sql($db, $sql);
	header('Location: index.php?page=livreor&do=okban');
}
else if ($_GET['do'] == 'dosuppr' and is_numeric($_GET['num']) and $user['niveau']['numniveau'] == 5)
{ // bannissement d'un message du livre d'or
	$sql = "DELETE FROM ".PREFIXE_TABLES."livreor WHERE num = '$_GET[num]'";
	send_sql($db, $sql);
	header('Location: index.php?page=livreor&do=oksuppr');
}
else if ($_GET['do'] == 'ecrire')
{
?>
<?php
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Livre d'or</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	}
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function validate(form) 
{
	if (form.elements['message'].value=="") 
	{
		alert("C'est autant ne pas signer le livre d'or... pour un message vide.");
		return false; 
	}
	else if (form.elements['auteur'].value=="")
	{
		alert("Merci d'indiquer ton pseudo ou ton nom.");
		return false; 
	}
	else
	{
		return true; 
	}
}
//-->
</script>
<h1>Livre d'or</h1> 
<form action="livreor.php" method="post" name="signerlivreor" id="signerlivreor" onsubmit="return validate(this);" class="form_config_site livreor">
<h2>Signer le livre d'or</h2>
<?php
	if ($_GET['erreur'] == 1)
	{
?>
<div class="msg">
<p align="center" class="rmq">Ton message n'a pas &eacute;t&eacute; enregistr&eacute; !<br />
Ton pseudo est vide ou ton email est invalide</p>
</div>
<?php
	}
?>
<p> Tu peux laisser ici un petit message, en souvenir de ton passage sur le site.</p>
<?php panneau_mise_en_forme('message', true); ?>
<p align="center">
<strong><input type="hidden" name="do" value="send" /></strong></p>
<textarea name="message" id="message" tabindex="1" cols="45" rows="6" class="case" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
<p>Ton nom ou ton pseudo :
<input type="text" name="auteur" maxlength="100" tabindex="2" class="case" style="width:250px" /></p>
<p>Ton adresse email :
<input type="text" name="email" class="case" style="width:250px" tabindex="3" /></p>
<p class="petitbleu">Ton adresse email ne sera pas publi&eacute;e sur le site ni exploit&eacute;e 
&agrave; d'autres usages que pour le fonctionnement de l'Unit&eacute;.</p>
<p align="center"><input type="checkbox" name="abo" value="1" id="abo" tabindex="4" />
  <label for="abo">Je souhaite recevoir la newsletter de l'Unit&eacute; de temps 
  &agrave; autre.</label></p>
<p align="center"><input type="submit" name="Submit" value="Envoyer" class="bouton" tabindex="5" /></p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>" class="bouton" tabindex="6">Voir 
 le livre d'or</a></p>
</form>
<?php panneau_smileys('message'); ?>
<?php
	$sql = "SELECT * FROM ".PREFIXE_TABLES."livreor WHERE banni != '1' ORDER BY datecreation DESC LIMIT 0,1";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
			$ligne = mysql_fetch_assoc($res);
?> 
<div class="livreor_msg">
<p class="rmq">Dernier message :</p>
<p><img src="templates/default/images/carrejaune.png" alt="" align="top" /> 
  <?php echo '<span class="rmqbleu">'.$ligne['auteur'].'</span>'; ?> a &eacute;crit : </p>
<p class="livreor_txt"><?php echo makehtml($ligne['message']); ?></p>
<p class="livreor_infos">
<?php if (!empty($ligne['email']) and $user['niveau']['numniveau'] > 2) {echo '<a href="mailto:'.$ligne['email'].'" class="lienmort">'.$ligne['email'].'</a> ';} ?>
<?php echo date_ymd_dmy($ligne['datecreation'], "enlettres"); ?></p>
</div>
<?php
		}
	}
}
else if ($_GET['do'] == 'ban' and is_numeric($_GET['num']) and $user['niveau']['numniveau'] > 2)
{
?>
<h1>Bannir un message du livre d'or</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">Retour au Livre d'or</a></p>
<div class="action">
<p align="center">Es-tu certain de vouloir bannir ce message du livre d'or ?</p>
<p align="center"><a href="livreor.php?do=doban&amp;num=<?php echo $_GET['num']; ?>">OUI</a> 
  - <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">NON</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'suppr' and is_numeric($_GET['num']) and $user['niveau']['numniveau'] == 5)
{
?>
<h1>Supprimer un message du livre d'or</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">Retour au Livre d'or</a></p>
<div class="action">
<p align="center">Es-tu certain de vouloir supprimer d&eacute;finitivement ce message du livre d'or ?</p>
<p align="center"><a href="livreor.php?do=dosuppr&amp;num=<?php echo $_GET['num']; ?>">OUI</a> 
  - <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">NON</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'lire' or (empty($_GET['do']) and empty($_POST['do'])))
{
	$include_banni = ($user['niveau']['numniveau'] == 5) ? '': "WHERE banni != '1'";
	$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."livreor $include_banni";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) == 1)
		{
			$ligne = mysql_fetch_assoc($res);
			$nbre_msg = $ligne['nbre'];
		}
		else
		{
			$nbre_msg = 0;
		}
		$par = 5;
		if ($nbre_msg > $par)
		{
			$nbre_pages = round($nbre_msg / $par);
			if ($nbre_pages * $par < $nbre_msg) $nbre_pages++;
		}
		else
		{
			$nbre_pages = 1;
		}
	}
	$pg = $_GET['pg'];
	if (isset($pg) and $pg < 1) {$pg = 1;} else if (isset($pg) and $pg > $nbre_pages) {$pg = $nbre_pages;}
	if (!isset($pg)) {$debut = 0; $pg = 1;} // page en cours
	else {$debut = $par * ($pg - 1);}
	$sql = "SELECT * FROM ".PREFIXE_TABLES."livreor $include_banni ORDER BY datecreation DESC LIMIT $debut, $par";
	if ($res = send_sql($db, $sql))
	{
		if ($nbre_msg > 0)
		{
			$i = 0;
?>
<h1>Livre d'or</h1>
<div class="panneau">
<h2>Menu du livre d'or</h2>
<p class="petitbleu"><?php echo ($nbre_msg > 1) ? $nbre_msg.' messages' : $nbre_msg.' message';?></p>
<p><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'signerlivreor.htm' : 'index.php?page=livreor&amp;do=ecrire'; ?>">Signer le livre d'or</a></p>
</div>
<?php
			if ($nbre_pages > 1)
			{
?>
<p class="pagination">
<?php
				if ($pg > 1)
				{
					$pgpcdte = $pg - 1;
					$lien_livreor_pcdte = ($site['url_rewriting_actif'] == 1) ? 'livreor_'.$pgpcdte.'.htm' : 'index.php?page=livreor&amp;pg='.$pgpcdte;
?>
  <a href="<?php echo $lien_livreor_pcdte; ?>" class="pg_pcdte">Messages plus r&eacute;cents</a>
<?php
				}
?>
  <span class="pg">Page <?php echo $pg.' de '.$nbre_pages; ?></span>
<?php
				if ($pg < $nbre_pages)
				{
					$pgsvte = $pg + 1;
					$lien_livreor_svte = ($site['url_rewriting_actif'] == 1) ? 'livreor_'.$pgsvte.'.htm' : 'index.php?page=livreor&amp;pg='.$pgsvte;
?>
  <a href="<?php echo $lien_livreor_svte; ?>" class="pg_svte">Messages plus anciens</a>
<?php
				}
?>
</p>
<?php
			}
			while ($ligne = mysql_fetch_assoc($res))
			{
				$i++;
?>
<div class="livreor_msg" style="clear:both;">
<p class="rmqbleu"><img src="templates/default/images/carrejaune.png" alt="" width="12" height="12" align="top" /> 
  <?php echo $ligne['auteur']; ?></p>
<p class="livreor_txt"><?php echo makehtml($ligne['message']); ?></p>
<p class="livreor_infos">
<?php if (!empty($ligne['email']) and $user['niveau']['numniveau'] > 2) {echo '<a href="mailto:'.$ligne['email'].'" class="lienmort">'.$ligne['email'].'</a> ';} ?>
<?php 
				if ($user['niveau']['numniveau'] > 2) 
				{
?><a href="index.php?page=livreor&amp;do=suppr&amp;num=<?php echo $ligne['num']; ?>" title="Supprimer ce message"><img src="templates/default/images/supprimer.png" width="12" height="12" alt="Supprimer ce message" border="0" /></a> <?php
				}
				echo date_ymd_dmy($ligne['datecreation'], 'enlettres'); 
?></p>
</div>
<?php
			}
		}
		else
		{
?>
<h1>Livre d'or </h1>
<div class="msg">
<p align="center">Personne n'a encore &eacute;crit dans le livre d'or.</p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'signerlivreor.htm' : 'index.php?page=livreor&amp;do=ecrire'; ?>" title="Signer le livre d'or">Eh bien je serai le premier !</a></p>
</div>
<?php
		}
	}
}
else if ($_GET['do'] == 'merci')
{
?>
<h1>Livre d'or </h1>
<div class="msg">
<p align="center" class="rmqbleu">Merci d'avoir signé notre livre d'or !</p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">Tu peux voir ton message ici</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'okban')
{
?>
<h1>Bannir un message du livre d'or</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">Retour au Livre d'or</a></p>
<div class="msg">
<p align="center">Le message a bien &eacute;t&eacute; banni du livre d'or</p>
</div>
<?php
}
else if ($_GET['do'] == 'oksuppr')
{
?>
<h1>Supprimer un message du livre d'or</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'livreor.htm' : 'index.php?page=livreor'; ?>">Retour au Livre d'or</a></p>
<div class="msg">
<p align="center">Le message a bien &eacute;t&eacute; supprim&eacute; du livre d'or</p>
</div>
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