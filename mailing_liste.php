<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* mailing_liste.php v 1.1 - Gestion de la mailing-liste du portail
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
*	réécriture abonnement newsletter avec la fonction abonnement_newsletter()
*	Optimisation xhtml
*/

include_once('connex.php');
include_once('fonc.php');
if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Mailing liste de l'Unit&eacute;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" type="text/css" />
</head>

<body>
<?php
}
if ($page == 'mailing_liste')
{
?>
<h1>Mailing liste de l'Unit&eacute;</h1>
<?php
}
if ($user['niveau']['numniveau'] == 5 and !empty($_GET['do']))
{
?>
<p align="center"><a href="index.php?page=mailing_liste">Retour &agrave; la page Mailing liste de l'Unit&eacute;</a></p>
<?php
	if ($_GET['do'] == 'confirm' and is_numeric($_GET['num']))
	{
?>
<div class="action">
<p align="center">Es-tu certain de vouloir supprimer cette adresse email de la base de donn&eacute;es ?<br />Cette action est irréversible.</p>
<p align="center" class="rmqbleu"><a href="index.php?page=mailing_liste&amp;do=supprimer&amp;num=<?php echo $_GET['num']; ?>" class="bouton">Oui</a>
  <a href="index.php?page=mailing_liste" class="bouton">Non</a></p>
</div>
<?php
	} // fin confirm
	if ($_GET['do'] == 'supprimer' and is_numeric($_GET['num']))
	{
		$sql = "DELETE FROM ".PREFIXE_TABLES."site_mailing_liste WHERE num = '$_GET[num]'";
		send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">L'adresse email a bien &eacute;t&eacute; supprim&eacute;e de la base de donn&eacute;es</p>
</div>
<?php
	} // fin supprimer
	else if ($_GET['do'] == 'desactiver' and is_numeric($_GET['num']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."site_mailing_liste SET envoi_ok = '0' WHERE num = '$_GET[num]'";
		send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">L'adresse email est d&eacute;sactiv&eacute;e</p>
</div>
<?php
	} // fin desactiver
	else if ($_GET['do'] == 'activer' and is_numeric($_GET['num']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."site_mailing_liste SET envoi_ok = '1' WHERE num = '$_GET[num]'";
		send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">L'adresse email est activ&eacute;e</p>
</div>
<?php
	} // fin activer
	else if ($_GET['do'] == 'superactiver')
	{
		$sql = "UPDATE ".PREFIXE_TABLES."site_mailing_liste SET envoi_ok = '1'";
		send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">Toutes les adresses sont actives.</p>
</div>
<?php
	} // fin superactiver
	else if ($_GET['do'] == 'superdesactiver' and is_numeric($_GET['num']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."site_mailing_liste SET envoi_ok = '0' WHERE num != '$_GET[num]'";
		send_sql($db, $sql);
?>
<div class="msg">
<p align="center" class="rmqbleu">Toutes les adresses email sont d&eacute;sactiv&eacute;es sauf une.</p>
</div>
<?php
	} // fin activer
} // fin niveau == 5 and do !vide
else
{
	if ($user > 0)
	{
?>
<p align="center"><a href="index.php?page=membres">Retour &agrave; l'Accueil Membres</a></p>
<?php
	}
	else
	{
?>
<p align="center"><a href="index.php">Retour &agrave; l'Accueil du portail</a></p>
<?php
	}
	if ($user['niveau']['numniveau'] == 5)
	{
		// gestion mailing liste
		$sql = 'SELECT * FROM '.PREFIXE_TABLES.'site_mailing_liste ORDER BY nom';
		$res = send_sql($db, $sql);
		$nbre_mails = mysql_num_rows($res);
		if ($nbre_mails > 0)
		{
			$pl = ($nbre_mails > 1) ? 's' : '';
	?>
<div class="panneau">
<h2>Mailing liste (<?php echo $nbre_mails; ?> abonn&eacute;<?php echo $pl; ?>)</h2>
<p>- <a href="index.php?page=mailing">Envoyer une newsletter</a> 
<br />
- <a href="index.php?page=mailing_liste&do=superactiver">Activer 
  toutes les adresses</a></p>
</div>
<p>La mailing liste contient actuellement .</p>
<p>Plusieurs actions sont possibles sur les adresses email : <br />
  <img src="templates/default/images/plus.png" alt="" width="12" height="12" /> 
  L'adresse est active. Tu peux d&eacute;sactiver l'envoi vers cette adresse.<br />
  <img src="templates/default/images/go.png" alt="" width="12" height="12" /> 
  L'adresse est active, tu peux d&eacute;sactiver toutes les autres sauf celle-ci 
  (Utile pour des tests de la fonction mailing).<br />
  <img src="templates/default/images/moins.png" alt="" width="12" height="12" /> 
  L'adresse est inactive. Tu peux activer l'envoi vers cette adresse.<br />
  <img src="templates/default/images/supprimer.png" alt="" width="12" height="12" />Supprimer 
  cette adresse de la liste d&eacute;finitivement.<br />
  Lorsqu'un utilisateur se d&eacute;sabonne, il supprime son adresse de la base. 
  L'(in)activation de l'adresse est utilis&eacute;e &agrave; des fins de tests.</p>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
	<th>&nbsp;</th>
	<th>N&deg;</th>
	<th>Nom</th>
	<th>Email</th>
	<th>Date Ajout</th>
  </tr>
  <?php
			$j = 0;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$j++;
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
				echo '<tr class="'.$couleur.'">';
				if ($ligne['envoi_ok'] == 1)
				{
					echo '<td><a href="index.php?page=mailing_liste&amp;do=desactiver&amp;num='.$ligne['num'].'" title="Désactiver cet email"><img src="templates/default/images/plus.png" width="12" height="12" border="0" alt="D&eacute;sactiver cet email" /></a> <a href="index.php?page=mailing_liste&amp;do=superdesactiver&amp;num='.$ligne['num'].'" title="Désactiver toutes les adresses sauf celle-ci"><img src="templates/default/images/go.png" width="12" height="12" border="0" alt="Désactiver toutes les adresses sauf celle-ci"></a></td>';
				}
				else
				{
					echo '<td><a href="index.php?page=mailing_liste&amp;do=activer&amp;num='.$ligne['num'].'" title="Activer cet email"><img src="templates/default/images/moins.png" width="12" height="12" border="0" alt="Activer cet email" /></a></td>';
				}
				echo '<td>'.$j.'.</td>';
				echo '<td>';
				echo (!empty($ligne['ip_inscr'])) ? '<span title="'.$ligne['ip_inscr'].'" class="rmqbleu">IP</span> - ' : '';
				echo $ligne['nom'];
				echo '</td>';
				echo '<td><a href="index.php?page=mailing_liste&amp;do=confirm&amp;num='.$ligne['num'].'" title="Supprimer cet email de la liste"><img src="templates/default/images/supprimer.png" width="12" height="12" border="0" alt="Supprimer cet email de la liste" align="top" /></a>';
				echo ' <a href="mailto:'.$ligne['email'].'" class="lienmort">'.$ligne['email'].'</a></td>';
				echo '<td>'.date_ymd_dmy($ligne['date_ajout'], 'enlettres').'</td>';
				echo '</tr>';
			} // fin while
	?>
</table>
	<?php
		} // fin nbre_mails > 0
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">La mailing liste ne contient aucun abonn&eacute;</p>
</div>
<?php
		}
	} // fin user == 5
	else if ($user != 0)
	{
		// l'utilisateur est connecté
		// vérification de son statut dans la mailing liste
		// si abonné on lui propose de se désabonner
		// sinon, on lui propose de s'abonner
		if ($_GET['do'] == 'sub')
		{
			abonnement_newsletter($user['email'], $user['prenom'].' '.$user['nom'], true);
			log_this('Abonnement Newsletter : '.$user['prenom'].' '.$user['nom'].' '.$user['email'], 'mailing');
		}
		else if ($_GET['do'] == 'unsub')
		{
			abonnement_newsletter($user['email'], '', false);
			log_this('résiliation Abonnement Newsletter : '.$user['email'], 'mailing');
		}
		$sql = "SELECT * FROM ".PREFIXE_TABLES."site_mailing_liste WHERE email = '$user[email]'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
?>
<div class="msg">
<p align="center" class="rmqbleu">Tu es abonn&eacute; &agrave; la mailing liste.<br />A tout moment, tu peux résilier ton abonnement en cliquant sur le lien ci-dessous.</p>
<p align="center"><a href="index.php?page=mailing_liste&amp;do=unsub">Je ne souhaite plus recevoir la newsletter</a></p>
</div>
<?php
		} // fin num_rows > 0
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'es pas abonn&eacute; &agrave; la mailing liste.<br />A tout moment, tu peux t'y abonner en cliquant sur le lien ci-dessous.</p>
<p align="center"><a href="index.php?page=mailing_liste&amp;do=sub">Je souhaite recevoir la newsletter</a></p>
</div>
<?php
		} // fin else num_rows > 0
	}
	else
	{
		// l'utilisateur n'est pas connecté
		// on lui propose le formulaire d'abonnement/désabonnement standard
		$email = htmlentities($_GET['email'], ENT_QUOTES);
		$nom = htmlentities($_GET['nom'], ENT_QUOTES);
		if (($_GET['do'] == "abo" or $_GET['do'] == 'stop') and checkmail($email))
		{
			$lien_retour = ($site['url_rewriting_actif']) ? 'mailing_liste.htm' : 'index.php?page=mailing_liste';
			if ($_GET['do'] == 'abo' and strlen($nom) > 0)
			{
				if (abonnement_newsletter($email, $nom))
				{
?>
<div class="msg">
<p align="center" class="rmqbleu">Te voil&agrave; abonn&eacute; !<br />A tout moment, tu peux r&eacute;silier ton abonnement en revenant sur cette page.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
				}
				else
				{
?>
<div class="msg">
<p align="center" class="rmq">Tu est d&eacute;j&agrave; abonn&eacute; avec cette adresse !<br />Une seule inscription par email est possible.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
				}
			}
			else if ($_GET['do'] == 'abo' and strlen($_GET['nom']) == 0)
			{
?>
<div class="msg">
<p align="center" class="rmq">Merci d'indiquer ton nom.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
			}
			else if ($_GET['do'] == 'stop')
			{
				if (abonnement_newsletter($email, '', false))
				{
?>
<div class="msg">
<p align="center" class="rmqbleu">Ton adresse a &eacute;t&eacute; supprim&eacute;e.<br />A tout moment, tu peux te r&eacute;abonner en visitant cette page.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
				}
				else
				{
?>
<div class="msg">
<p align="center" class="rmq">Cette adresse ne se trouve pas dans la liste des abonn&eacute;s.<br />Impossible de r&eacute;silier l'abonnement.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu est d&eacute;j&agrave; abonn&eacute; avec cette adresse !<br />Une seule inscription par email est possible.</p>
<p align="center"><a href="<?php echo $lien_retour; ?>">Retour</a></p>
</div>
<?php
			}
		} // fin abo / stop
		else
		{
?>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (((getElement("abo").checked && form.nom.value != "") || (!getElement("abo").checked)) && form.email.value != "")
	{
		if (!getElement("abo").checked)
		{
			return confirm("Es-tu certain de vouloir résilier ton abonnement ?");
		}
		else
		{
			return true;
		}
	}
	else
	{
		alert("Merci de remplir les champs nécessaires !");
		return false;
	}
}

function change(valeur)
{
		getElement("etoilenom").innerHTML = valeur;
}
//-->
</script>
<form action="index.php" method="get" name="formulaire" class="form_config_site" id="formulaire" onsubmit="return check_form(this)">
<input type="hidden" name="page" value="mailing_liste" />
<h2>Abonnement &agrave; la newsletter</h2>
  <p align="center" class="petit">La Newsletter de l'Unit&eacute; t'est envoy&eacute;e environ 
	une fois par mois. Remplis le formulaire ci-dessous pour t'y abonner ou t'en 
	d&eacute;sabonner.</p>
<?php
			if (($_GET['do'] == 'abo' or $_GET['do'] == 'stop') and !checkmail($email))
			{
?>
<p align="center" class="rmq">Merci d'indiquer un email correct.</p>
<?php
			}
?>
  <p align="center"> 
	<input type="radio" name="do" value="abo" id="abo" onclick="change('*')"<?php echo ($page == 'mailing_liste') ? ' checked="checked"' : ' checked="checked"'; ?> />
	<label for="abo">Je m'abonne</label> - 
	<input type="radio" name="do" value="stop" id="desabo" onclick="change('')" />
	<label for="desabo">Je me d&eacute;sabonne</label>
  </p>
  <p>Pr&eacute;nom et nom<span id="etoilenom">*</span> :
    <input name="nom" type="text" id="nom" size="30" maxlength="255" />
  </p>
  <p>Adresse email* :
    <input name="email" type="text" id="email" size="30" maxlength="255" />
  </p>
  <p align="center">
	<input type="submit" value="Envoyer" />
  </p>
</form>
<?php
		} // fin do vide
	} // fin user = 0
} // fin else niveau = 5 and do !vide
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>