<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* reactiver_portail.php v 1.1 - Ce fichier permet au webmaster de réactiver le portail après avoir été déconnecté sauvagement
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

if ($_POST['logstep'] == 'check' and !defined('IN_SITE') and $site['site_actif'] != '1')
{ // on vérifie le pseudo et le pw du "webmaster"
	if (!empty($_POST['pseudo']) and !empty($_POST['pass']))
	{
		$pw = md5(UN_PEU_DE_SEL.$_POST['pass']);
		$old_pw = md5($_POST['pass']);
		$pseudo = htmlentities($_POST['pseudo'], ENT_QUOTES);
		$sql = "SELECT num, banni, niveau FROM ".PREFIXE_TABLES."auteurs WHERE pseudo = '$pseudo' and (pw = '$pw' or pw = '$old_pw')";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{ // l'utilisateur existe
				$user = mysql_fetch_assoc($res);
				if ($user['banni'] == '1')
				{ // pas de bol, il est banni
					header('Location: reactiver_portail.php?err=2');
					exit;
				}
				if ($niveaux[$user['niveau']]['numniveau'] != '5')
				{ // pas de bol, il n'est pas webmaster
					header('Location: reactiver_portail.php?err=3');
					exit;
				}
				// tout va bien, on réactive le portail pour le distrait
				$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = '1' WHERE champ = 'site_actif' LIMIT 1";
				send_sql($db, $sql);
				// et on recharge la config en cache
				reset_config();
				header('Location: reactiver_portail.php?msg=ok');
			}
			else
			{ // pseudo ou pw incorrect
				header('Location: reactiver_portail.php?err=1');
			}
		}
	}
	else
	{ // un petit malin fait joujou avec le formulaire
		include('404.php');
	}
}
else if (!defined('IN_SITE') and ($site['site_actif'] != '1' or $_GET['msg'] == 'ok'))
{ // On affiche la page normale
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Connexion</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#reactiver {
	width:70%; margin:auto;}
</style>
<script type="text/JavaScript">
<!--
function check_form(form)
{
	if (form.pseudo.value != "" && form.pass.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci d'indiquer ton pseudo et ton mot de passe.");
		return false;
	}
}
//-->
</script>
</head>

<body>
<h1>R&eacute;activer le portail</h1>
<div id="reactiver">
<?php
	if (isset($_GET['err']))
	{
?>
<div class="msg">
<?php
		if ($_GET['err'] == 1)
		{
?>
  <p class="rmq" align="center">Pseudo ou mot de passe incorrect !</p>
<?php
		}
		else if ($_GET['err'] == 2)
		{
?>
  <p class="rmq" align="center">Ce compte est banni !</p>
<?php
		}
		else if ($_GET['err'] == 3)
		{
?>
  <p class="rmq" align="center">Tu n'as pas les droits d'acc&egrave;s suffisants pour r&eacute;activer le portail !</p>
  <p align="center">Seul le webmaster peut faire cette action.</p>
<?php
		}
?>
  <p align="center"><a href="reactiver_portail.php">Recommencer</a> - <a href="index.php">Retour au portail</a></p>
</div>		
<?php
	}
	else if ($_GET['msg'] == 'ok')
	{
?>
<div class="msg">
<p class="rmqbleu" align="center">Le portail est &agrave; nouveau actif !</p>
		
<p align="center">Tu peux maintenant te <a href="index.php">reconnecter sur le 
  portail</a>.</p>
</div>
<?php
	}
	else
	{
?>
<form action="reactiver_portail.php" method="post" name="formulaire" id="formulaire" onsubmit="return check_form(this)" class="action">
  <input type="hidden" name="logstep" value="check" />
  <p class="petitbleu">Tu as d&eacute;sactiv&eacute; le portail et tu n'arrives plus &agrave; te 
    connecter comme webmaster pour r&eacute;activer le portail ? Alors cette page 
    est faite pour toi ;)</p>
  <p>Entre ton pseudo et ton mot de passe et clique sur &quot;R&eacute;activer 
    le portail&quot;. Le portail sera r&eacute;activ&eacute; si tu es webmaster 
    uniquement.</p>
            <p align="center">Pseudo : <?php $pseudo_stocke = ($page == 'inscr') ? $monpseudo : $_COOKIE['pseudo_stocke']; ?>
            <input name="pseudo" type="text" id="pseudo" size="20" maxlength="32" style="width:100px" value="<?php echo (isset($pseudo_stocke)) ? $pseudo_stocke : ''; ?>" /></p>
            <p align="center">Mot de passe : <input name="pass" type="password" id="pass" size="20" maxlength="32" style="width:100px" /></p>
            <p align="center"><input type="submit" name="Submit" value="Réactiver le portail" />
            </p>
</form>
<?php
	$pseudo_stocke = ($page == 'inscr') ? $monpseudo : $_COOKIE['pseudo_stocke'];
?>
<script type="text/JavaScript">
<!--
function givefocus()
{
document.formulaire.<?php echo (isset($pseudo_stocke)) ? 'pass' : 'pseudo'; ?>.focus();
}
givefocus();
//-->
</script>
<?php
	}
?>
</div>
</body>
</html>
<?php
}
else
{
	include('404.php');
}
?>