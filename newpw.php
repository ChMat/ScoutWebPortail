<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* newpw.php v 1.1 - Outil permettant à un utilisateur de renouveler son mot de passe
* Cet outil NE FONCTIONNE QUE si la fonction mail() est active
* Copyright (C) 2005 Christian Mattart
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
*	externalisation de l'envoi du mail
*/

include_once('connex.php');
include_once('fonc.php');
if (!ENVOI_MAILS_ACTIF)
{ // fonction mail() inactive, script inutilisable
	include('404.php');
	exit;
}
$step = (!empty($_GET['step'])) ? $_GET['step'] : $_POST['step'];
if (empty($step))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mot de passe perdu</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	}
?>
<h1>Mot de passe oubli&eacute;</h1>
<div class="introduction">
  <p>Sur cette page, tu peux r&eacute;cup&eacute;rer un acc&egrave;s &agrave; 
    ton compte.</p>
  <p>Comme seule l'empreinte crypt&eacute;e de ton mot de passe est conserv&eacute;e 
    sur le portail, un nouveau mot de passe va t'&ecirc;tre attribu&eacute;. Une 
    fois connect&eacute;, tu pourras le modifier.</p>
</div>
<form action="newpw.php" method="post" name="step0" id="step0" class="form_config_site">
<h2>Renouveler ton mot de passe</h2>
<p class="petitbleu">Entre ton pseudo et ton adresse email ci-dessous pour recevoir un nouveau 
mot de passe.</p>
<p><span class="rmq">Attention !</span> Seule l'adresse email que tu as 
  fournie lors de ton inscription sera prise en compte.</p>
<p align="center">Ton pseudo : 
  <input type="text" name="pseudo" maxlength="32" style="width:200px;" value="<?php echo $_COOKIE['pseudo_stocke']; ?>" tabindex="1" />
  </p>
<p align="center">    Ton email : 
    <input type="text" name="email" maxlength="255" style="width:200px;" tabindex="2" />
</p>
<p align="center"> 
 <input type="submit" name="Submit" value="Envoyer" tabindex="3" />
 <input type="hidden" name="step" value="1" />
</p>
<p align="center" class="petitbleu">Tu as oubli&eacute; ton pseudo ? Retrouve-le sur la <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'listembsite.htm' : 'index.php?page=listembsite'; ?>" tabindex="4">liste 
des membres</a></p>
</form>
      <?php
}
elseif ($_POST['step'] == '1')
{
	if (checkmail($_POST['email']))
	{
		$pseudo = htmlentities($_POST['pseudo'], ENT_QUOTES);
		$sql = "SELECT num, pseudo, prenom, nom, email FROM ".PREFIXE_TABLES."auteurs WHERE pseudo = '$pseudo' AND email = '$_POST[email]' AND clevalidation = '' AND banni != '1'";
		if ($res = send_sql($db, $sql))
		{
			if (mysql_num_rows($res) == 1)
			{
				$ligne = mysql_fetch_assoc($res);
				$user = $ligne['num'];
				if ($site['update_pw'] == 'en_cours')
				{ // le portail a été mis à jour. On supprime le cas échéant la référence au membre
					$sql = "DELETE FROM ".PREFIXE_TABLES."auteurs_pw_v11 WHERE num = '".$user."' LIMIT 1";
					send_sql($db, $sql);
				}
				$pwprov = cleunique(8);
				$pwprovisoire = md5(UN_PEU_DE_SEL.$pwprov);
				$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET pw = '$pwprovisoire', newpw = '1' WHERE num = '$user'";
				$res = send_sql($db, $sql);
				include_once('prv/emailer.php');
				$courrier = new emailer();
				$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
				$reponse = $expediteur;
				$courrier->from($expediteur);
				$courrier->to($_POST['email']);
				$courrier->reply_to($expediteur);
				$courrier->use_template('new_pw', 'fr');
				$courrier->assign_vars(array(
					'USER_PRENOM' => makehtml($ligne['prenom']),
					'USER_PSEUDO' => makehtml($ligne['pseudo']),
					'PW_PROVISOIRE' => $pwprov,
					'ADRESSE_SITE' => $site['adressesite'],
					'WEBMASTER_PSEUDO' => $site['webmaster'],
					'WEBMASTER_EMAIL' => $site['mailwebmaster']));
				if ($courrier->send())
				{
					$courrier->reset();
					header('Location: index.php?page=newpw&step=2&email='.$_POST['email']);
					exit;
				}
				else
				{
					header('Location: index.php?page=newpw&step=erreur');
					exit;
				}
			}
			else
			{
				header('Location: index.php?page=newpw&step=erreur&msg=1&email='.$_POST['email']);
				exit;
			}
		}
		else
		{
			header('Location: index.php?page=newpw&step=erreur');
			exit;
		}
	}
	else
	{
		header('Location: index.php?page=newpw&step=erreur&msg=pasmail');
		exit;
	}
}
elseif ($step == '2')
{
?>
<h1>Mot de passe oubli&eacute;</h1>
<div class="msg"> 
 <p align="center" class="rmqbleu">Un email vient de t'&ecirc;tre envoy&eacute; avec ton nouveau mot de passe.</p>
 <p align="center">Il a &eacute;t&eacute; envoy&eacute; &agrave; l'adresse : <span class="rmq"><?php echo $_GET['email']; ?></span></p>
 <p align="center">Tu pourras le modifier une fois connect&eacute;.</p>
</div>
<?php
}
else if ($step == 'erreur')
{
?>
<h1>Mot de passe oubli&eacute;</h1>
<div class="msg">
<?php
	if (empty($_GET['msg']))
	{
?>
	<p align="center" class="rmq">Une erreur s'est produite ! Procédure abandonnée.</p>
<?php
	}
	else if ($_GET['msg'] == '1')
	{
?>
	
<p align="center" class="rmq">Aucune &eacute;quivalence trouv&eacute;e avec l'adresse 
  <span class="rmqbleu"><?php echo $_GET['email']; ?></span>.</p>
<?php
	}
	else if ($_GET['msg'] == 'pasmail')
	{
?>
<p align="center" class="rmq">L'adresse entr&eacute;e n'est pas une adresse email valide.</p>
<?php
	}
?>
      
<p align="center"><a href="index.php?page=newpw" tabindex="1">Retour</a></p>
</div>
<?php
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>