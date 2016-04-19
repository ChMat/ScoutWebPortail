<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* purge_unite.php v 1.1.1 - Suppression de membres de l'Unité
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
*	Prise en compte du nouveau modèle de mot de passe
* Modifications v 1.1.1
*	Les animateurs de section peuvent désormais supprimer les membres et les familles
*		la suppression de tous les membres en une fois reste réservée au webmaster
*	Ajout message de confirmation en javascript
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
}
else
{
	$do = (isset($_GET['do'])) ? $_GET['do'] : $_POST['do'];
	if (empty($do) or !$do)
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Suppression de membres de l'Unit&eacute;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Supprimer des membres de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la 
  Gestion de l'Unit&eacute;</a></p>
<div class="introduction">
<p>Cette page te permet de supprimer des donn&eacute;es de la base des membres 
  de l'Unit&eacute;</p>
</div>
<noscript>
<div class="msg">
<p align="center" class="rmq">Merci d'activer le javascript pour pouvoir utiliser cette page.</p>
</div>
</noscript>
<?php
		$sql = 'SELECT nummb, nom_mb, prenom, rue, numero, section FROM '.PREFIXE_TABLES.'mb_membres as a, '.PREFIXE_TABLES.'mb_adresses as b WHERE a.famille = b.numfamille ORDER BY nom_mb, prenom ASC';
		if ($res = send_sql($db, $sql))
		{
			$nbre_membres = mysql_num_rows($res);
		}
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ok_mb()
{
	if (document.form.nummb.value != "")
	{
		return confirm("Es-tu certain de vouloir supprimer ce membre de la base de données ?\n\nL'opération ne peut pas être annulée.");
	}
	else
	{
		alert("Merci de bien vouloir choisir un membre avant d'envoyer le formulaire.");
		return false;
	}
}
//-->
</script>
<form action="purge_unite.php" method="post" name="form" id="form" onsubmit="return ok_mb()" class="form_config_site">
<h2>Supprimer la fiche d'un membre ou d'un ancien de l'Unit&eacute;</h2>
<?php
		if ($_GET['msg'] == 3)
		{
?>
<div class="msg">
  <p align="center">Le membre a &eacute;t&eacute; supprim&eacute; <?php echo ($_GET['prem'] == 1) ? 'ainsi que sa famille' : ''; echo ($_GET['bis'] == 1) ? ', et sa deuxi&egrave;me adresse' : ''; ?>.</p>
</div>
<?php
		}
		if ($_GET['msg'] == 6)
		{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite !</p>
</div>
<?php
		}
?>
  <input type="hidden" name="do" value="supprimer_membre" />
<p>S&eacute;lectionne le membre que tu souhaites supprimer de la base : 
<?php 
			if ($nbre_membres > 0)
			{
				$pl = ($nbre_membres > 1) ? 's' : '';
				echo '('.$nbre_membres.' membre'.$pl.' trouv&eacute;'.$pl.')';
			}
?></p>
<?php
			if ($nbre_membres > 0)
			{
?>
  <p align="center"> 
    <select name="nummb" size="15">
      <?php
				while ($membre = mysql_fetch_assoc($res))
				{
?>
      <option value="<?php echo $membre['nummb']; ?>"> 
      <?php $bte = (!empty($membre['bte'])) ? '/' : ''; echo $membre['nom_mb'].' '.$membre['prenom'].' ('.$membre['rue'].', '.$membre['numero'].$bte.$membre['bte'].')'; if ($membre['section'] != 0) {echo ' - '.$sections[$membre['section']]['nomsection'];} ?>
      </option>
      <?php
				}
?>
    </select></p>
    <p align="center">
    <input type="checkbox" name="confirmation" value="oui" id="confirmation_membre" onclick="if(this.checked) {getElement('suppr_membre').disabled = false;} else {getElement('suppr_membre').disabled = true;}" />
    <label for="confirmation_membre" class="rmq">Je confirme vouloir supprimer la fiche de ce membre</label>
  </p>
  <p align="center"> 
    <input type="submit" value="Supprimer ce membre" id="suppr_membre" disabled="true" />
  </p>
<?php
			}
			else
			{
?>
<div class="msg">
<p class="rmqbleu" align="center">Il n'y a aucun membre dans la base.</p>
</div>
<?php
			}
?>
</form>
<?php
		$sql = 'SELECT numfamille, nom, rue, numero FROM '.PREFIXE_TABLES.'mb_adresses ORDER BY nom ASC';
		if ($res = send_sql($db, $sql))
		{
			$nbre_familles = mysql_num_rows($res);
		}
?>
  
<script language="JavaScript" type="text/JavaScript">
<!--
function ok()
{
	if (document.form.numfamille.value != "")
	{
		return confirm("En supprimant la famille, tu supprimes aussi tous les membres qui y sont reliés !\nEs-tu certain de vouloir supprimer cette famille ?\n\nL'opération ne peut pas être annulée.");
	}
	else
	{
		alert("Merci de bien vouloir choisir une famille avant d'envoyer le formulaire.");
		return false;
	}
}
//-->
</script>
<form action="purge_unite.php" method="post" name="form" id="form" onsubmit="return ok()" class="form_config_site">
<h2>Supprimer la fiche d'une famille de l'Unit&eacute;</h2>
<?php
		if ($_GET['msg'] == 2)
		{
?>
<div class="msg">
<p align="center">La famille a &eacute;t&eacute; supprim&eacute;e ainsi que les membres qu'elle comptait.</p>
</div>
<?php
		}
		if ($_GET['msg'] == 5)
		{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite !</p>
</div>
<?php
		}
?>
  <input type="hidden" name="do" value="supprimer_famille" />
<p>S&eacute;lectionne la famille &agrave; supprimer : 
<?php 		
		if ($nbre_familles > 0) 
		{
			$pl = ($nbre_familles > 1) ? 's' : '';
			echo '('.$nbre_familles.' famille'.$pl.' trouv&eacute;e'.$pl.')';
		}
?></p>
<?php
		if ($nbre_familles > 0)
		{
?>
                    
  <p align="center"> 
    <select name="numfamille" size="15">
<?php
			while ($famille = mysql_fetch_assoc($res))
			{
?>
      <option value="<?php echo $famille['numfamille']; ?>"> 
<?php $bte = ''; $bte = (!empty($famille['bte'])) ? '/' : ''; echo $famille['nom'].' ('.$famille['rue'].', '.$famille['numero'].$bte.$famille['bte'].')'; ?>
      </option>
<?php
			}
?>
    </select></p>
    <p align="center">
      <input type="checkbox" name="confirmation" value="oui" id="confirmation_famille" onclick="if(this.checked) {getElement('suppr_famille').disabled = false;} else {getElement('suppr_famille').disabled = true;}" />
      <label for="confirmation_famille" class="rmq">Je confirme vouloir supprimer cette famille et les membres qu'elle contient</label>
    </p>
  <p align="center" class="petitbleu">
    <input type="submit" id="suppr_famille" value="Supprimer cette famille" disabled="true" />
  </p>
                    <?php
		}
		else
		{
?>
<div class="msg">
<p class="rmqbleu" align="center">Il n'y a aucune famille dans la base.</p>
</div>
<?php
		}
?>
</form>
<?php
		if ($user['niveau']['numniveau'] == 5)
		{
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function ok_db()
{
	return confirm("Es-tu certain de vouloir supprimer tous les membres de l'unité ?\n\nL'opération ne peut pas être annulée.");
}
//-->
</script>
<form action="purge_unite.php" method="post" name="purge_unite" id="purge_unite" onsubmit="return ok_db()" class="form_config_site">
<h2>Supprimer toutes les fiches des membres et des familles</h2>
<p>Tous les membres et toutes les familles seront supprim&eacute;es.</p>
<?php
			if ($_GET['msg'] == 1)
			{
?>
<div class="msg">
<p class="rmqbleu" align="center">Tous les membres ont &eacute;t&eacute; supprim&eacute;s de la base de donn&eacute;s.</p>
</div>
<?php
			}
			if ($_GET['msg'] == 4)
			{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite ! Aucun membre supprim&eacute;.</p>
</div>
<?php
			}
?>
<div align="center">
   <p>
      <input type="hidden" name="do" value="dopurge" />
      Par mesure de s&eacute;curit&eacute;, merci d'entrer ton mot de passe pour 
      vider la base des membres de l'Unit&eacute;.</p>
    <p>Ton mot de passe : 
      <input name="pw" type="password" id="pw" />
    </p>
    <p>
      <input type="checkbox" name="confirmation" value="oui" id="confirmation" onclick="if(this.checked) {getElement('purge').disabled = false;} else {getElement('purge').disabled = true;}" />
      <label for="confirmation" class="rmq">Je confirme vouloir supprimer tous les membres contenus 
      dans la base de donn&eacute;es</label></p>
  </div>
  <p align="center">
    <input type="submit" value="Supprimer tous les membres" id="purge" disabled="true" />
  </p>
</form>
<?php
		} // fin boucle if numniveau == 5
	}
	else if ($do == 'dopurge' and $user['niveau']['numniveau'] == 5)
	{
		// vérification du mot de passe webmaster afin de s'assurer qu'on a bien le bon au bout du clavier.
		$pw = md5(UN_PEU_DE_SEL.$_POST['pw']);
		if ($pw == untruc(PREFIXE_TABLES.'auteurs', 'pw', 'num', $user['num']))
		{
			$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_adresses';
			send_sql($db, $sql);
			$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_membres';
			send_sql($db, $sql);
			log_this('Purge de la db des membres de l\'unité', 'purge_unite', true);
			header('Location: index.php?page=purge_unite&msg=1');
		}
		else
		{
			header('Location: index.php?page=purge_unite&msg=4');
		}
	}
	else if ($do == 'supprimer_famille')
	{
		// vérification du mot de passe webmaster afin de s'assurer qu'on a bien le bon au bout du clavier.
		if (is_numeric($_POST['numfamille']))
		{
			$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_adresses WHERE numfamille = \''.$_POST['numfamille'].'\'';
			send_sql($db, $sql);
			$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_membres WHERE famille = \''.$_POST['numfamille'].'\' OR famille2 = \''.$_POST['numfamille'].'\'';
			send_sql($db, $sql);
			log_this('Suppression famille '.$_POST['numfamille'], 'purge_unite');
			header('Location: index.php?page=purge_unite&msg=2');
		}
		else
		{
			header('Location: index.php?page=purge_unite&msg=5');
		}
	}
	else if ($do == 'supprimer_membre')
	{
		if (is_numeric($_POST['nummb']))
		{
			// on récupère les données des familles du membre
			$sql = 'SELECT famille, famille2 FROM '.PREFIXE_TABLES.'mb_membres WHERE nummb = \''.$_POST['nummb'].'\'';
			$res = send_sql($db, $sql);
			// on le supprime
			$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_membres WHERE nummb = \''.$_POST['nummb'].'\'';
			send_sql($db, $sql);
			// on vérifie s'il reste des gens dans sa/ses famille(s)
			$ligne = mysql_fetch_assoc($res);
			$bis = $prem = '';
			if ($ligne['famille2'] > 0)
			{
				$sql = 'SELECT nummb FROM '.PREFIXE_TABLES.'mb_membres WHERE famille = \''.$ligne['famille2'].'\' OR famille2 = \''.$ligne['famille2'].'\'';
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 0)
				{ // personne dans sa deuxième famille, on la supprime
					$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_adresses WHERE numfamille = \''.$ligne['famille2'].'\'';
					send_sql($db, $sql);
					log_this('Suppression automatique de la famille '.$ligne['famille2'], 'purge_unite');
					$bis = '&bis=1'; // on signale au webmaster que la famille a été supprimée
				}
			}
			if ($ligne['famille'] > 0)
			{ // et dans la première ?
				$sql = 'SELECT nummb FROM '.PREFIXE_TABLES.'mb_membres WHERE famille = \''.$ligne['famille'].'\' OR famille2 = \''.$ligne['famille'].'\'';
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 0)
				{ // personne, on la supprime
					$sql = 'DELETE FROM '.PREFIXE_TABLES.'mb_adresses WHERE numfamille = \''.$ligne['famille'].'\'';
					send_sql($db, $sql);
					log_this('Suppression automatique de la famille '.$ligne['famille'], 'purge_unite');
					$prem = '&prem=1'; // on signale au webmaster que la famille a été supprimée
				}
			}
			// on verse une petite larme d'émotion dans le log après ce crime parfait
			log_this('Suppression membre '.$_POST['nummb'], 'purge_unite');
			header('Location: index.php?page=purge_unite&msg=3'.$prem.$bis);
		}
		else
		{
			header('Location: index.php?page=purge_unite&msg=6');
		}
	}
} // fin du else (numniveau < 3)
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>