<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* mailing.php v 1.1 - Envoi de la newsletter aux abonnés.
* Fichier lié : mailing_liste.php
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
*	externalisation de l'email
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] != 5)
{
	include('404.php');
}
else
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mailing - <?php echo $site['titre_site']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	}
	if ($_GET['d'] == 'redigermailing' or (!isset($_GET['d']) and !isset($_POST['d'])))
	{
		$sql = "SELECT nom, email FROM ".PREFIXE_TABLES."site_mailing_liste WHERE envoi_ok = '1'";
		$res = send_sql($db, $sql);
		$nbre_mails = mysql_num_rows($res);
?>
<h1>Envoi de mailings</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'Accueil Membres</a></p>
<?php
		if ($nbre_mails > 0)
		{
			$pl = ($nbre_mails > 1) ? 's' : '';
?>
<div class="panneau">
<h2>Mailing liste (<?php echo $nbre_mails; ?> abonn&eacute;<?php echo $pl; ?>)</h2>
<p><a href="index.php?page=mailing_liste">G&eacute;rer 
        la mailing liste</a></p>
</div>
<div class="introduction">
<p class="petitbleu">Envoyer un mailing &agrave; tous les membres inscrits dans la mailing liste</p>
</div>
<?php
			if (!ENVOI_MAILS_ACTIF)
			{
?>
<div class="msg">
<p align="center" class="rmq">L'envoi de mails est d&eacute;sactiv&eacute; sur le portail.</p>
</div>
<?php
			}
			else
			{
?>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.titremailing.value != "" && form.textemailing.value != "")
	{
		if (confirm("As-tu bien vérifié tout ton texte ?\n\nIl va être lu par <?php echo $nbre_mails; ?> personnes, c'est mieux s'il ne reste aucune erreur."))
		{
			getElement("envoi").disabled = true;
			getElement("envoi").value = 'Envoi en cours...';
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert("Oups, tu as oublié de remplir un champ !");
		return false;
	}
}
//-->
</script>
<form action="index.php" method="post" name="formulairemailing" id="formulairemailing" onsubmit="return check_form(this)" class="form_config_site">
<h2>Param&egrave;tres du mailing</h2>
  <input type="hidden" name="page" value="mailing" />
  <input type="hidden" name="d" value="sendmailing" />
<p align="center"> Titre du mailing : 
    <input name="titremailing" type="text" size="40" />
     </p>
<p align="center">Texte du mailing : format html pur (contenu entre &lt;body&gt; 
    et &lt;/body&gt;)<br />
    <textarea name="textemailing" rows="12" cols="70"></textarea></p>
<p align="center"><input type="submit" name="envoi" id="envoi" value="Envoyer" />
  <input type="reset" name="reset" value="Recommencer" />
</p>
</form>
<div class="instructions">
<p>A leur inscription sur le portail, les membres ont la possibilit&eacute; 
    de s'inscrire &agrave; la mailing liste.<br />
    La gestion des membres alimente automatiquement la mailing liste.<br />
  Chaque membre &agrave; la possibilit&eacute; de se d&eacute;sinscrire 
  de cette mailing liste.</p>
</div>
<?php
			} // fin if ENVOI_MAILS_ACTIF
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Actuellement, personne n'est abonn&eacute; &agrave; la newsletter.</p>
</div>
<?php
		}
	}
	else if ($_POST['d'] == 'sendmailing')
	{
?>
<h1>Envoi de mailings</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'Accueil Membres</a></p>
<?php
		$sql = "SELECT nom, email FROM ".PREFIXE_TABLES."site_mailing_liste WHERE envoi_ok = '1'";
		$res = send_sql($db, $sql);
		$nbremails = mysql_num_rows($res);
		if ($nbremails > 0 and !empty($_POST['titremailing']) and !empty($_POST['textemailing']))
		{
			if (!defined('LOCAL_SITE') and ENVOI_MAILS_ACTIF)
			{
				include_once('prv/emailer.php');
				$courrier = new emailer();
				$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
				$reponse = $expediteur;
				$courrier->from($expediteur);
				$courrier->to($expediteur);
				$courrier->reply_to($expediteur);
				$courrier->set_content_type('text/html');
				$courrier->msg = stripslashes($_POST['textemailing']);
				$titremailing = cleanvar($titremailing);
				$courrier->set_subject(stripslashes($titremailing));
				while ($mb = mysql_fetch_assoc($res))
				{
					$courrier->bcc($mb['email']);
				}
				$courrier->send();
				$courrier->reset();
				if ($nbremails > 1) {$pl_mailing = 's';}
				log_this('Envoi d\'un mailing', 'mailing');
?>
<div class="msg">
<p align="center">Le message vient d'&ecirc;tre envoy&eacute; &agrave; <?php echo $nbremails.' adresse'.$pl_mailing; ?> email.</p>
</div>
<?php
			}
			else
			{ // portail local ou envoie de mails désactivé : mail pas envoyé ;)
?>
<div class="msg">
<p align="center" class="rmq">Le mailing n'a pas &eacute;t&eacute; envoy&eacute;, l'envoi de mails est d&eacute;sactiv&eacute; pour tout le portail.</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
  <p align="center" class="rmq">Le message n'a &eacute;t&eacute; envoy&eacute; 
    &agrave; personne.</p>
  <p align="center" class="petit">Aucune adresse trouv&eacute;e dans la base mailing.</p>
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
?>