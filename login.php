<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* login.php v 1.1 - Identification d'un utilisateur du portail
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
*	passage au nouveau modèle de mot de passe
*	gestion de la mise à jour du mot de passe
*/

include_once('connex.php');
include_once('fonc.php');

if ((!isset($_GET['logstep']) or $_GET['logstep'] == 1) and !isset($_POST['logstep']) and defined('IN_SITE'))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Connexion</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	}
	if ($user == 0)
	{
?>
<script language="JavaScript" type="text/JavaScript">
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
<?php
		if ($page == 'login')
		{
?>
  <h1>Connexion &agrave; la zone membres</h1>
  <?php
		}
?>
<form action="login.php" method="post" name="formulaire" id="formulaire" onsubmit="return check_form(this)" class="action form_login">
  <input type="hidden" name="logstep" value="2" />
<?php
		if ($page != 'login')
		{
?>
<h2>Connexion &agrave; la zone membres</h2>
<?php
		}
		// Messages d'erreur
		if ($_GET['err'] == 1)
		{
?>
<p class="rmq" align="center">Pseudo ou mot de passe incorrect !</p>
<p align="center"><?php
		if (!ENVOI_MAILS_ACTIF)
		{
?>
<a href="#" onclick="alert('Si tu as oublié ton mot de passe, écris un mail au webmaster (<?php echo $site['mailwebmaster']; ?>).\nIl en créera un nouveau qu\'il t\'enverra par mail à l\'adresse email de ton inscription. Tu pourras ensuite le modifier sur la page de ton profil.');">Mot de passe oubli&eacute; ?</a> 
<?php
		}
		else
		{
?>
<a href="index.php?page=newpw">Mot de passe oubli&eacute; ?</a>
<?php
		}
?></p>
<?php
		}
		else if ($_GET['err'] == 3)
		{
?>
<p class="rmq" align="center">Ce compte a &eacute;t&eacute; banni !</p>
<?php
		}
?>
        <table border="0" align="center" cellpadding="2" cellspacing="0">
          <tr> 
            <td>Pseudo :</td>
			<?php $pseudo_stocke = ($page == 'inscr') ? $monpseudo : $_COOKIE['pseudo_stocke']; ?>
            <td align="right"><input name="pseudo" type="text" id="pseudo" size="20" maxlength="32" style="width:100px" value="<?php echo (isset($pseudo_stocke)) ? $pseudo_stocke : ''; ?>" /></td>
          </tr>
          <tr> 
            <td>Mot de passe : </td>
            <td align="right"><input name="pass" type="password" id="pass" size="20" maxlength="32" style="width:100px" /></td>
          </tr>
        </table>
<p align="center">
  <input name="saveid" type="checkbox" id="saveid" value="1" checked="checked" />
  <label for="saveid" title="Tu resteras connecté lors de tes prochaines visites.">Conserver mon identification</label></p>
<p align="center"><?php
		if (($page != 'login' or isset($_GET['retoursurpage'])) and $page != 'inscr')
		{
			$retoursurpage = (!isset($_GET['retoursurpage'])) ? str_replace('&', 'a-m-p', $_SERVER['QUERY_STRING']) : $_GET['retoursurpage'];
?>
<input type="hidden" name="retoursurpage" value="<?php echo $retoursurpage; ?>" />
<?php
		}
?>
<input type="submit" name="Submit" value="Connexion" /></p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">Devenir 
membre</a> 
<?php
		if (!ENVOI_MAILS_ACTIF)
		{
?>
 - <a href="#" onclick="alert('Si tu as oublié ton mot de passe, écris un mail au webmaster (<?php echo $site['mailwebmaster']; ?>).\nIl en créera un nouveau qu\'il t\'enverra par mail à l\'adresse email de ton inscription. Tu pourras ensuite le modifier sur la page de ton profil.');">J'ai 
oubli&eacute; mon mot de passe</a> 
<?php
		}
		else
		{
?>
 - <a href="index.php?page=newpw">J'ai oubli&eacute; mon mot de passe</a>
<?php
		}
?></p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'probleme_connexion.htm' : 'index.php?page=probleme_connexion'; ?>">
  Je n'arrive pas &agrave; me connecter</a></p>
</form>
<?php
		if ($page == 'login' or $page == 'inscr') 
		{
			$pseudo_stocke = ($page == 'inscr') ? $monpseudo : $_COOKIE['pseudo_stocke'];
?>
<script language="JavaScript" type="text/JavaScript">
function givefocus()
{
document.formulaire.<?php echo (isset($pseudo_stocke)) ? 'pass' : 'pseudo'; ?>.focus();
}
givefocus();
</script>
<?php
		}
	}
	else
	{
?>
<div class="msg">
  <p align="center"><span class="rmq">Tu es connect&eacute; sous le pseudo </span><span class="rmqbleu"><?php echo $user['pseudo']; ?></span></p>
  <p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'logoff.htm' : 'index.php?page=logoff'; ?>">Me d&eacute;connecter</a></p>
</div>
<?php 	
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
<?php
}
else
{
	if (!empty($_POST['pseudo']) and !empty($_POST['pass']))
	{
		$pw = md5(UN_PEU_DE_SEL.$_POST['pass']);
		$pseudo = htmlentities($_POST['pseudo'], ENT_QUOTES);
		if ($site['update_pw'] == 'en_cours')
		{ // Préalable de mise à jour du mot de passe pour la version 1.1
		  // sur la base d'une installation préalable avec swp v 1.0
			$old_pw = md5($_POST['pass']);
			$sql = "SELECT num FROM ".PREFIXE_TABLES."auteurs WHERE pseudo = '$pseudo' and pw = '$old_pw'";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{
				$ligne = mysql_fetch_assoc($res);
				// passage automatique au mot de passe sécurisé
				$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET pw = '$pw' WHERE pseudo = '$pseudo' and pw = '$old_pw' LIMIT 1";
				send_sql($db, $sql);
				// suppression depuis la liste des membres non à jour
				$sql = "DELETE FROM ".PREFIXE_TABLES."auteurs_pw_v11 WHERE num = '".$ligne['num']."' LIMIT 1";
				send_sql($db, $sql);
			}
			// on regarde s'il reste des comptes à mettre à jour
			$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."auteurs_pw_v11";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre'] == 0)
			{ // Tout le monde est à jour, on désactive le script de mise à jour et on supprime la table concernée
				$sql = "DELETE FROM ".PREFIXE_TABLES."config WHERE champ = 'update_pw' LIMIT 1";
				send_sql($db, $sql);
				$sql = "DROP TABLE ".PREFIXE_TABLES."auteurs_pw_v11";
				send_sql($db, $sql);
				log_this('La mise à jour du portail est terminée - Tous les utilisateurs se sont connectés au moins une fois', 'login', true);
				reset_config();
			}
		} // Fin de la mise à jour
		$sql = "SELECT num, banni, pseudo FROM ".PREFIXE_TABLES."auteurs WHERE pseudo = '$pseudo' and pw = '$pw'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$id = '';
				$user = '';
				while (!check_cle_unique($id)) // check_cle_unique est défini en haut de cette page
				{ // on produit une clé unique vraiment unique (vérif dans la db si $id n'existe pas encore)
					$id = cleunique();
				}
				$user = mysql_fetch_assoc($res);
				if ($user['banni'] == '1')
				{
					header('Location: index.php?page=login&err=3');
					exit;
				}
				$numuser = $user['num'];
				$sql = "DELETE FROM ".PREFIXE_TABLES."connectes WHERE user = '$numuser'";
				send_sql($db, $sql);
				$ip = $_SERVER['REMOTE_ADDR'];
				$cookie_login = (!isset($_COOKIE['x_login'])) ? 0 : 1;
				$pc_user = $cookie_login.UN_PEU_DE_SEL.$_SERVER['HTTP_ACCEPT_CHARSET'].$_SERVER['HTTP_ACCEPT_ENCODING'].$_SERVER["HTTP_USER_AGENT"].$_SERVER['HTTP_ACCEPT_LANGUAGE'];
				$pc_user = ($cookie_login == 0) ? md5($_SERVER['REMOTE_ADDR'].$pc_user) : md5($pc_user);
				$sql = "INSERT INTO ".PREFIXE_TABLES."connectes (id, user, connectea, ip, pc_user, cookie_login) values ('$id', '$numuser', now(), '$ip', '$pc_user', '$cookie_login')";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET nbconnex = nbconnex + 1 WHERE num = '$numuser'";
				send_sql($db, $sql);
				if ($cookie_login == 1)
				{
					setcookie('pseudo_stocke', $user['pseudo'], time()+365*24*3600);
					if ($_POST['saveid'] == 1) {setcookie(COOKIE_ID, $id, time()+365*24*3600);} else {setcookie(COOKIE_ID, $id);}
				}
				else
				{
					log_this("Connexion sans cookie de l'utilisateur $numuser depuis l'ip $ip", 'login');
				}
				if (!empty($_POST['retoursurpage']))
				{
					$retoursurpage = str_replace("a-m-p", "&", $_POST['retoursurpage']);
					header('Location: index.php?'.$retoursurpage);
				}
				else
				{
					header('Location: index.php?page=membres');
				}
			}
			else
			{
				header('Location: index.php?page=login&err=1');
			}
		}
	}
	else
	{
		if ($page != 'login')
		{
			header('Location: index.php?page=login');
		}
		else
		{
			include('404.php');
		}
	}
}
?>