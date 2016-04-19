<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestioncotisations.php v 1.1 - Gestion des Cotisations des membres de l'unité (accès réservé aux animateurs d'unité)
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
*	Modification des icônes
*	Optimisation de l'ergonomie et ajout de la vérification du pw utilisateur
*	  pour la réinitialisation des cotisations
*	Ajout prise en charge montant à virgule française pour la cotisation
*	Prise en compte nouveau format de mot de passe
*/

include_once('connex.php');
include_once('fonc.php');
$coti_1st = str_replace(',', '.', $_POST['coti_1st']);
$coti_svt = str_replace(',', '.', $_POST['coti_svt']);
if ($user['niveau']['numniveau'] > 3)
{
	if ($_GET['do'] != 'updatecotisation' and $_POST['do'] != 'updatecotisation')
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des Cotisations</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des Cotisations</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a> - 
<a href="index.php?page=gestioncotisations">Retour &agrave; la page Gestion des Cotisations</a></p>
<?php
	}
	if (empty($_GET['do']) and empty($_POST['do']))
	{
		if (!empty($_GET['msg']) and is_numeric($_GET['nbre']))
		{
			$pl = ($_GET['nbre'] > 1) ? 's' : '';
			$nbre = ($_GET['nbre'] == 0) ? 'Aucune' : $_GET['nbre'];
?>
<div class="msg">
<p class="rmqbleu" align="center">Mise &agrave; jour effectu&eacute;e : <?php echo $nbre.' fiche'.$pl.' membre'.$pl.' modifi&eacute;e'.$pl; ?></p>
</div>
<?php
		}
?>
<div class="introduction">
<p>Cette fonction n'est accessible qu'aux membres du staff d'Unit&eacute;. Les 
  animateurs de section ne disposent que d'un droit de lecture de l'&eacute;tat 
  de paiement des cotisations.</p>
</div>
<form action="index.php" method="post" name="form" class="form_config_site" id="form">
	<h2>G&eacute;rer l'&eacute;tat des cotisations par Famille </h2>
	<p class="petit">La gestion des cotisation par famille affiche toutes 
	les familles stock&eacute;es dans la base de donn&eacute;es avec chaque 
	membre de cette famille affich&eacute; en regard. Il te suffira ensuite 
	d'indiquer l'&eacute;tat de paiement et de valider. On ne peut plus simple&nbsp;!</p>
    <p>
        <input type="hidden" name="page" value="gestioncotisations" />
        <input type="hidden" name="do" value="affcotisation_famille" />
      Prix &agrave; payer pour le premier enfant : 
        <input name="coti_1st" type="text" id="coti_1st" style="width:50px;" value="0" />
      et pour les suivants : 
        <input name="coti_svt" type="text" id="coti_svt" style="width:50px;" value="0" />
        <br />
      Placer les boutons de cotisation 
      <select name="position_boutons" id="position_boutons">
        <option value="g">&agrave; gauche</option>
        <option value="d" selected="selected">&agrave; droite</option>
      </select> </p>
      <p align="center"> 
          <input type="submit" value="Afficher les familles" />
          <br />
	<span class="petitbleu">Indique le montant des cotisations pour avoir un calcul du montant &agrave; payer</span>
  </p>
</form>
<form action="index.php" method="post" name="form" id="form" class="form_config_site">
<h2>G&eacute;rer l'&eacute;tat des cotisations par Section</h2>
<p class="petit">Apr&egrave;s avoir choisi la section, ses membres seront affich&eacute;s. Il 
  te suffira ensuite d'indiquer l'&eacute;tat de paiement et de valider. On ne 
  peut plus simple&nbsp;!</p>
<p>
        <input type="hidden" name="page" value="gestioncotisations" />
        <input type="hidden" name="do" value="affcotisation" />
Choisis la Section &agrave; g&eacute;rer : 
        <select name="section">
          <option value="" selected="selected"></option>
<?php
		foreach ($sections as $section)
		{
			if (!$section['anciens'])
			{
?>
          <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; ?></option>
          <?php
			}
		}
?>
        </select></p>
  <p align="center"> 
          <input type="submit" value="Afficher cette section" />
  </p>
</form>
<form action="index.php" method="post" name="form1" id="form1" onsubmit="return check_form_reset(this)" class="form_config_site">
<h2>Réinitialiser tous les membres de l'Unit&eacute;</h2>
<p class="petit">Cette fonction te permet de remettre tous les compteurs 
        des cotisations d'une Unit&eacute; &agrave; z&eacute;ro et de consid&eacute;rer 
        que personne n'est en ordre de paiement. <span class="rmq">Attention, 
        cette fonction est irr&eacute;versible&nbsp;!</span></p>
	  <script type="text/javascript" language="JavaScript">
	  <!--
	  function check_form_reset(form)
	  {
	  	if (form.section.value != "" && form.pw.value != "")
		{
			return confirm('Es-tu certain de vouloir réinitialiser les cotisations de tous les membres de cette Unité ?');
		}
		else
		{
			alert("N'oublie pas de choisir une Unité et d'indiquer ton mot de passe !");
			return false;
		}
	  }	  
	  //-->
	  </script>
	    <input type="hidden" name="page" value="gestioncotisations" />
	    <input type="hidden" name="do" value="resetcotisations" />
        <p align="center">Choisis l'Unit&eacute; dans la liste 
          <select name="section">
            <?php
		echo (count_unites() > 1) ? '<option value=""></option>' : '';
		foreach($sections as $section)
		{
			if (is_unite($section['numsection']))
			{
?>
            <option value="<?php echo $section['numsection']; ?>"><?php echo $section['nomsection']; ?></option>
            <?php
			}
		}
		echo (count_unites() > 1) ? '<option value="tous">Tous les membres</option>' : '';
?>
          </select>
        </p>
        <p align="center">Ton mot de passe : 
          <input name="pw" type="password" id="pw" size="20" />
        </p>
        <p align="center"> 
          <input type="submit" name="Button" value="R&eacute;initialiser tous les membres" />
        </p>
</form>
<?php
	}
	else if ($_POST['do'] == 'affcotisation' and is_numeric($_POST['section']))
	{
?>
<form action="gestioncotisations.php" method="post" name="actualisercotisations" id="actualisercotisations" onsubmit="return confirm('Confirme tu les modifications effectuées ?');" class="form_gestion_unite">
<h2>Mettre &agrave; jour le statut des cotisations des <?php echo $sections[$_POST['section']]['appellation']; ?>.</h2>
<p class="rmqbleu">Section en cours : <?php echo $sections[$_POST['section']]['nomsection']; ?></p>
  <p>Ci-dessous, tu peux d&eacute;terminer l'&eacute;tat de paiement de la cotisation 
  des membres. Il te suffit de cocher la case ad&eacute;quate.<br />
  <span class="petitbleu">Les membres sur la liste d'attente de la Section ne sont 
  pas affich&eacute;s.</span></p>
<?php
		$sql = "SELECT nummb, prenom, nom_mb, ddn, cotisation FROM ".PREFIXE_TABLES."mb_membres WHERE actif = '1' AND section = '$_POST[section]' ORDER BY nom_mb, prenom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbretrouve = mysql_num_rows($res);
			if ($nbretrouve > 0)
			{
?>
  <input type="hidden" name="nbre" value="<?php echo $nbretrouve; ?>" />
  <input type="hidden" name="do" value="updatecotisation" />
  <input type="hidden" name="section" value="<?php echo $_POST['section']; ?>" />
<table cellpadding="0" cellspacing="0">
<tr>
<th colspan="3" width="120">Cotisation</th>
<th>Nom</th>
<th>Pr&eacute;nom</th>
<th>DDN</th>
</tr>
<?php
				$j = 0;
				while ($membre = mysql_fetch_assoc($res))
				{
					$j++;
					$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
	  <tr class="<?php echo $couleur; ?>">
		<td width="40" align="left">
		  <input type="hidden" name="num_<?php echo $j; ?>" value="<?php echo $membre['nummb']; ?>" />
		  <input type="hidden" name="oldmb_<?php echo $membre['nummb']; ?>" value="<?php echo $membre['cotisation']; ?>" />
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="1" id="mb_<?php echo $membre['nummb']; ?>1"<?php echo ($membre['cotisation'] == 1) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>1" title="Cotisation pay&eacute;e"><img src="templates/default/images/ok.png" alt="" width="12" height="12" align="middle" /></label></td>
		<td width="40" align="left">
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="0" id="mb_<?php echo $membre['nummb']; ?>0"<?php echo ($membre['cotisation'] == 0) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>0" title="Cotisation non payée"><img src="templates/default/images/non.png" alt="" width="12" height="12" align="middle" /></label></td>
		<td width="40" align="left">
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="-1" id="mb_<?php echo $membre['nummb']; ?>x"<?php echo ($membre['cotisation'] == -1) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>x" title="Etat de paiement inconnu"><img src="templates/default/images/inconnu.png" alt="" width="12" height="12" align="middle" /></label></td>
		<td><span class="rmqbleu"><?php echo $membre['nom_mb']; ?></span></td>
		<td><span class="rmqbleu"><?php echo $membre['prenom']; ?></span></td>
		<td align="center"><?php echo ($membre['ddn'] != '0000-00-00') ? date_ymd_dmy($membre['ddn'], 'enchiffres') : ''; ?></td>
	  </tr>
<?php
				}
?>
</table>
<p align="center">
    <input type="submit" value="Enregistrer ces donn&eacute;es" />
</p>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmqbleu">Cette section  ne contient aucun membre.</p>
</div>
<?php
			}
		}
?>
</form>
<?php
	}
	else if ($_POST['do'] == 'affcotisation_famille' and is_numeric($coti_1st) and is_numeric($coti_svt))
	{
?>
<div class="introduction">
  <p>Ci-dessous, tu peux d&eacute;terminer l'&eacute;tat de paiement de la cotisation 
    des membres. Il te suffit de cocher la case ad&eacute;quate.<br />
    <span class="petitbleu">Les membres sur la liste d'attente ne sont pas affich&eacute;s.</span></p>
</div>
<?php
		// on pourrait éviter cette requête qui fait double emploi avec la précédente.
		// le javascript pourrait être utilisé pour comptabiliser le nombre de membres dans une famille
		// et ainsi calculer le montant de la cotisation à payer.
		// mais je n'ai pas envie de faire de javascript ce soir ;)
		$sql = "SELECT numfamille, count(*) as nbre FROM ".PREFIXE_TABLES."mb_adresses, ".PREFIXE_TABLES."mb_membres WHERE numfamille = famille AND actif = '1' GROUP BY famille";
		if ($res = send_sql($db, $sql))
		{
			$nbre_mb_famille = array(mysql_num_rows($res)); // $nbre_mb_famille[0] contient le nombre de familles dans la base
			if (mysql_num_rows($res) > 0)
			{
				while ($ligne = mysql_fetch_assoc($res))
				{ // chaque élément de l'array contient le nombre de membres de la famille
					$nbre_mb_famille[$ligne['numfamille']] = $ligne['nbre'];
				}
			}
		}
		$sql = "SELECT nummb, prenom, nom_mb, ddn, cotisation, nom, numfamille, famille, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse, section FROM ".PREFIXE_TABLES."mb_adresses, ".PREFIXE_TABLES."mb_membres WHERE numfamille = famille AND actif = '1' ORDER BY nom, famille, nom_mb, prenom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbretrouve = mysql_num_rows($res);
			if ($nbretrouve > 0)
			{
?>
<form action="gestioncotisations.php" method="post" name="actualisercotisations" id="actualisercotisations" onsubmit="return confirm('Confirme tu les modifications effectuées ?');" class="form_gestion_unite">
<h2>Mettre &agrave; jour le statut des cotisations des membres, par famille.</h2>
  <input type="hidden" name="nbre" value="<?php echo $nbretrouve; ?>" />
  <input type="hidden" name="do" value="updatecotisation" />
<p>La base de donn&eacute;es contient <?php echo $nbretrouve; ?> membres dans <?php echo $nbre_mb_famille[0]; ?> familles</p>
	<table width="90%" cellpadding="0" cellspacing="0">
<?php
				$j = 0;
				$famille_pcdte = 0;
				while ($membre = mysql_fetch_assoc($res))
				{
					$j++; // on génère le numéro du membre en cours
					if ($famille_pcdte != $membre['famille']) 
					{ // C'est une nouvelle famille, on affiche les données de base de la famille
						$famille_pcdte = $membre['famille'];
?>
	  <tr class="td-1">
	  	
      <td colspan="5"> <span class="rmqbleu"><?php echo $membre['nom']; ?></span><?php echo ' ('.$membre['adresse'].')'; ?> 
        <?php
						if (is_numeric($coti_1st) and $coti_1st > 0 and is_numeric($coti_svt) and $coti_svt > 0)
						{
							$montant_coti = $coti_1st + (($nbre_mb_famille[$membre['famille']] - 1) * $coti_svt);
?>
        Montant total de la cotisation : <?php echo number_format($montant_coti, 2, ',', ' '); ?> 
        &euro; 
        <?php
						}
?>
        <a href="index.php?page=fichefamille&amp;numfamille=<?php echo $membre['famille']; ?>" title="Voir la fiche de la famille" target="_blank"> 
        <img src="templates/default/images/famille.png" alt="Voir la fiche de la famille" width="18" height="12" border="0" /></a> 
      </td>
	  </tr>
<?php
					}
?>
	  <tr class="td-2">
		<td width="40">&nbsp;</td>
<?php
					$zone = '
		<td class="petit">
		  <a href="index.php?page=fichemb&amp;nummb='.$membre['nummb'].'" title="Voir sa fiche membre" target="_blank">
		  <img src="templates/default/images/membre.png" alt="Voir sa fiche membre" width="18" height="12" border="0" /></a> 
		  <span class="petitbleu">'.$membre['prenom'].' '.$membre['nom_mb'].'</span>';
		   			$zone .= ($membre['ddn'] != '0000-00-00') ? ', '.age($membre['ddn']) : ''; 
					$zone .= ' - '.$sections[$membre['section']]['nomsectionpt'].'</td>';
					// positions_boutons permet de placer les cases à cocher à gauche (g) ou à droite (d) du tableau
					// cela permet d'améliorer la visibilité des infos
					// ce paramètre est réglable par l'utilisateur
					if ($_POST['position_boutons'] == 'd') {echo $zone;}
?>
		<td width="40" align="left">
		  <input type="hidden" name="num_<?php echo $j;?>" value="<?php echo $membre['nummb']; ?>" />
		  <input type="hidden" name="oldmb_<?php echo $membre['nummb']; ?>" value="<?php echo $membre['cotisation']; ?>" />
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="1" id="mb_<?php echo $membre['nummb']; ?>1"<?php echo ($membre['cotisation'] == 1) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>1" title="Cotisation pay&eacute;e"><img src="templates/default/images/ok.png" alt="" width="12" height="12" align="middle" /></label></td>
		<td width="40" align="left">
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="0" id="mb_<?php echo $membre['nummb']; ?>0"<?php echo ($membre['cotisation'] == 0) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>0" title="Cotisation non payée"><img src="templates/default/images/non.png" alt="" width="12" height="12" align="middle" /></label></td>
		<td width="40" align="left">
        <input type="radio" name="mb_<?php echo $membre['nummb']; ?>" value="-1" id="mb_<?php echo $membre['nummb']; ?>x"<?php echo ($membre['cotisation'] == -1) ? ' checked="checked"' : ''; ?> />
        <label for="mb_<?php echo $membre['nummb']; ?>x" title="Etat de paiement inconnu"><img src="templates/default/images/inconnu.png" alt="" width="12" height="12" align="middle" /></label></td>
<?php
					if ($_POST['position_boutons'] == 'g') {echo $zone;}
?>
	  </tr>
<?php
				}
?>
	</table>
	
<p align="center">
    <input type="submit" value="Enregistrer ces donn&eacute;es" />
</p>
</form>
<div class="instructions">
  <p><span class="rmq">Note</span> : Le montant total de la cotisation est toujours 
    le <strong>montant &agrave; payer si personne n'a pay&eacute;</strong> la 
    cotisation. Si des parents ne paient qu'une partie de la cotisation, ce montant 
    n'est <strong>pas recalcul&eacute;</strong> pour refl&eacute;ter le montant 
    restant d&ucirc;.<br />
    <em>Valeurs prises en compte</em> pour calculer le montant total : <strong><?php echo number_format($coti_1st, 2, ',', ' '); ?> &euro;</strong> 
    pour le premier et <strong><?php echo number_format($coti_svt, 2, ',', ' '); ?> &euro;</strong> pour les suivants.</p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmqbleu">La base de donn&eacute;es ne contient aucun membre.</p>
</div>
<?php
			}
		}
	}
	else if ($_POST['do'] == 'resetcotisations' and is_numeric($_POST['section']) and is_unite($_POST['section']))
	{ // mise à 0 des cotisations d'une unité
		$pw = md5(UN_PEU_DE_SEL.$_POST['pw']); // vérification du mot de passe de l'utilisateur
		if ($pw == untruc(PREFIXE_TABLES.'auteurs', 'pw', 'num', $user['num']))
		{
			$res = is_unite($_POST['section'], true); // récupération des sections de l'unité pour les inclure dans la requête
			$multi = ' (section = '.$_POST['section'];
			if (is_array($res))
			{
				foreach ($res as $a)
				{
					$multi .= " OR section = '$a'";
				}
			}
			$multi .= ')';
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET cotisation = '-1' WHERE $multi";
			send_sql($db, $sql);
			log_this("Réinitialisation des cotisations de l'Unité : ".$sections[$_POST['section']]['nomsection'], 'gestioncotisations');
?>
<div class="msg">
<p class="rmqbleu" align="center">Les cotisations sont toutes remises à zéro.</p>
<p align="center"><a href="index.php?page=gestioncotisations">Retour &agrave; la Gestion des cotisations</a></p>
</div>
<?php
		}
		else
		{ // Mot de passe incorrect
?>
<div class="msg">
<p class="rmq" align="center">Mot de passe incorrect !</p>
<p align="center"><a href="index.php?page=gestioncotisations">Retour &agrave; la Gestion des cotisations</a></p>
</div>
<?php
		}
	}
	else if ($_POST['do'] == 'resetcotisations' and $_POST['section'] == 'tous')
	{ // toutes les cotisations sont mises à 0
		$pw = md5($_POST['pw']); // vérification du mot de passe de l'utilisateur
		if ($pw == untruc(PREFIXE_TABLES.'auteurs', 'pw', 'num', $user['num']))
		{
			$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET cotisation = '-1'";
			send_sql($db, $sql);
			log_this('R&eacute;initialisation de toutes les cotisations de la base de donn&eacute;es', 'gestioncotisations');
?>
<div class="msg">
<p class="rmqbleu" align="center">Les cotisations sont toutes remises à zéro.</p>
<p align="center"><a href="index.php?page=gestioncotisations">Retour &agrave; la Gestion des cotisations</a></p>
</div>
<?php
		}
		else
		{ // Mot de passe incorrect
?>
<div class="msg">
<p class="rmq" align="center">Mot de passe incorrect !</p>
<p align="center"><a href="index.php?page=gestioncotisations">Retour &agrave; la Gestion des cotisations</a></p>
</div>
<?php
		}
	}
	else if ($_POST['do'] == 'updatecotisation')
	{
		$fiches_modifiees = 0;
		for ($i = 1; $i <= $_POST[nbre]; $i++)
		{
			$numuser = $var_coti = $var_excoti = $champ_coti = $champ_excoti = null;
			$var_numuser = 'num_'.$i; // construction de la variable qui contient le numero en cours
			$numuser = $_POST[$var_numuser] ;
			$var_coti = 'mb_'.$numuser;
			$var_excoti = 'old'.$var_coti;
			$champ_coti = $_POST[$var_coti];
			$champ_excoti = $_POST[$var_excoti];
			if ($champ_coti != $champ_excoti)
			{
				$fiches_modifiees++;
				if ($champ_coti != 1 and $champ_coti != 0 and $champ_coti != -1) {$champ_coti = -1;}
				$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET cotisation = '$champ_coti', mb_lastmodifby = '$user[num]', mb_lastmodif = now() WHERE nummb = '$numuser'";
				send_sql($db, $sql);
			}
			
		}
		log_this('Mise à jour des cotisations ('.$fiches_modifiees.' modifications)', 'gestioncotisations');
		header('Location: index.php?page=gestioncotisations&msg=ok&nbre='.$fiches_modifiees);	
	}
	else
	{
?>
<div class="msg">
<p align="center" class="rmq">Tu as du te tromper quelque part, on recommence...</p>
<p align="center"><a href="index.php?page=gestioncotisations">Retour</a></p>
</div>
<?php
	}
}
else
{
	include('404.php');
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