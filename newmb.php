<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* newmb.php v 1.1 - Ajout d'un membre de l'Unité
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
*	Choix de l'unité par défaut pour un AnU
*	Choix du sexe par défaut selon le type de section de l'utilisateur
*	Optimisation xhtml
*/

include_once('connex.php');
include_once('fonc.php');

if (!is_array($sections))
{ // les sections n'existent pas encore, il faut les créer d'abord
	include('gestion_sections.php');
}
else if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
} // fin numniveau <= 2
else
{
	if ((empty($_POST['step']) and empty($_GET['step'])) or $_GET['step'] == 1)
	{
		if (!defined('IN_SITE'))
		{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Ajout d'un membre dans la base de données</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		} // fin !defined in site
?>
<h1>Ajout d'un membre dans la base de donn&eacute;es</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; 
  la page Gestion de l'Unit&eacute;</a></p>
<div class="introduction">
<p> 
<?php
		if ($user['niveau']['numniveau'] == 3)
		{
?>
  A partir de cette page, tu peux ajouter les membres de ta section dans la base 
  de donn&eacute;es.<br />
  <?php
		} // fin numniveau == 3
		else if ($user['niveau']['numniveau'] > 3)
		{
?>
  A partir de cette page, tu peux ajouter les membres de l'Unit&eacute; dans la 
  base de donn&eacute;es.<br />
  <?php
		} // fin numniveau > 3
?>
  <br />
  <span class="rmq">Avant toute chose</span>, v&eacute;rifions si le membre que 
  tu veux ajouter n'est pas d&eacute;j&agrave; pr&eacute;sent dans la base.</p>
</div>
<form action="index.php" method="get" name="form_mb" class="form_gestion_unite" id="form1">
  
  <h2>Recherche des membres existants     </h2>
  <p>
    <input name="page" type="hidden" id="page" value="newmb" />
    <input name="step" type="hidden" id="step" value="1" />
    Entre le pr&eacute;nom et le nom du membre ci-dessous</p>
  <p align="center">Pr&eacute;nom :
    <input name="prenom_test" type="text" id="prenom_test" tabindex="1" value="<?php echo stripslashes($_GET['prenom_test']); ?>" size="30" />
Nom :
<input name="nom_test" type="text" id="nom_test" tabindex="2" value="<?php echo stripslashes($_GET['nom_test']); ?>" size="30" />
  </p>
<p align="center"><input type="submit" name="Submit" value="V&eacute;rifier" tabindex="3" />
</p>
</form>
<script type="text/javascript">
<!--
getElement('prenom_test').focus();
//-->
</script>
<?php
		if (!empty($_GET['prenom_test']) and !empty($_GET['nom_test']))
		{
			$prenom_test = soundex2($_GET['prenom_test']);
			$nom_test = soundex2($_GET['nom_test']);
			$sql = "SELECT numfamille, nom, nom_mb, prenom, rue, numero, section, ddn 
			FROM ".PREFIXE_TABLES."mb_membres as a RIGHT JOIN ".PREFIXE_TABLES."mb_adresses as b 
			ON a.famille = b.numfamille OR a.famille2 = b.numfamille
			WHERE (prenom_son = '$prenom_test' OR nom_mb_son = '$nom_test' OR nom_son = '$nom_test') 
			ORDER BY nom, nom_mb, prenom ASC";

			if ($res = send_sql($db, $sql))
			{
				$nbre_membres = mysql_num_rows($res);
				$pl_mb = ($nbre_membres > 1) ? 's' : '';
				$nbre_membres = ($nbre_membres > 0) ? $nbre_membres : 'Aucun';
				$j = 0;
				if ($nbre_membres > 0)
				{
?>
<div class="instructions">
<p>Ci-dessous, si la famille d'un des membres d&eacute;j&agrave; inscrits correspond &agrave; 
  celle du membre que tu veux inscrire, coche la case en face de l'un des membres
     de cette famille.</p>
</div>
<?php
				}
?>
<form action="index.php" method="get" name="form2" class="form_gestion_unite" id="form2">
<h2>R&eacute;sultat de la recherche</h2>
<p><?php echo $nbre_membres.' membre'.$pl_mb.' similaire'.$pl_mb.' trouv&eacute;'.$pl_mb; ?></p>
  <input type="hidden" name="page" value="newmb" />
  <input type="hidden" name="step" value="2" />
<?php
				if ($nbre_membres > 0)
				{
?>
  <table width="95%" align="center" cellpadding="0" cellspacing="0" class="cadrenoir">
    <tr>
      <th>Membre</th>
      <th>Famille du membre</th>
      <th title="Date de naissance">Ddn</th>
    </tr>
    <?php
					while ($ligne = mysql_fetch_assoc($res) and $nbre_membres > 0)
					{
						$j++;
						$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
    <tr class="<?php echo $couleur; ?>"> 
      <td> <span title="<?php echo $sections[$ligne['section']]['nomsection']; ?>"><?php echo $ligne['prenom'].' '.$ligne['nom_mb']; ?></span></td>
      <td> 
        <input type="radio" name="numfamille" value="<?php echo $ligne['numfamille']; ?>" id="f<?php echo $j; ?>" />
		<a href="index.php?page=fichefamille&amp;numfamille=<?php echo $ligne['numfamille']; ?>" title="Voir la fiche de cette famille" target="_blank"><img src="templates/default/images/famille.png" alt="Fiche famille" width="18" height="12" border="0" align="middle" /></a>
        <label for="f<?php echo $j; ?>">famille <?php echo $ligne['nom'].', '.$ligne['rue'].', '.$ligne['numero'];	?></label></td>
      <td><?php echo ($ligne['ddn'] != '0000-00-00') ? date_ymd_dmy($ligne['ddn'], 'enchiffres') : ''; ?></td>
    </tr>
    <?php
					}
?>
    <tr> 
      <td align="right"></td>
      <td colspan="2"> 
        <input type="radio" name="numfamille" value="" id="aucunefamille" checked="checked" />
        <label for="aucunefamille">Aucune famille ne correspond</label></td>
    </tr>
  </table>
<p>Si le membre n'appara&icirc;t pas dans la liste ci-dessus, continue l'inscription 
  : </p>
<?php
				}
				else
				{
?>
  <input type="hidden" name="numfamille" value="" id="aucunefamille" />
<?php
				}
			}
?>
  <p align="center">
    <input name="prenom" type="hidden" id="prenom" value="<?php echo stripslashes($_GET['prenom_test']); ?>" />
    <input name="nom" type="hidden" id="nom" value="<?php echo stripslashes($_GET['nom_test']); ?>" />
    <input type="submit" name="Submit" id="continuer" value="Continuer l'inscription" tabindex="4" />
  </p>
  <p class="petitbleu"><span class="rmq">Deuxi&egrave;me adresse</span><br />
  Si le membre que tu ajoutes a deux adresses, s&eacute;lectionne ici l'adresse principale. Tu pourras choisir la seconde adresse &agrave; l'&eacute;tape suivante.</p>
</form>
<script type="text/javascript">
<!--
getElement('continuer').focus();
//-->
</script>
<?php
		}
?>
<div class="instructions">
<p>La recherche des membres existants se base sur la phon&eacute;tique
  probable des nom et pr&eacute;nom. L'objectif est d'&eacute;viter les doublons
  dans la base de donn&eacute;es.</p>
</div>
<?php
	}
	else if ($_GET['step'] == 2)
	{
?>
<h1>Ajout d'un membre dans la base de donn&eacute;es</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; 
  la page Gestion de l'Unit&eacute;</a></p>
  
<div class="introduction">
<p><?php
		if ($user['niveau']['numniveau'] == 3)
		{
?>A partir de cette page, tu peux ajouter les membres de ta section dans la base 
  de donn&eacute;es.<br /><?php
		} // fin numniveau == 3
		else if ($user['niveau']['numniveau'] > 3)
		{
?>A partir de cette page, tu peux ajouter les membres de l'Unit&eacute; dans la 
  base de donn&eacute;es.<br /><?php
		} // fin numniveau > 3
?><br />
  <span class="rmq">Avant toute chose</span>, merci de <span class="rmq">lire 
  les instructions</span> au bas de la page</p>
</div>
<?php
		$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
		if ($res = send_sql($db, $sql))
		{
			$nbre_familles = mysql_num_rows($res);
		} // fin send_sql
?>
<script type="text/javascript" language="JavaScript">
var nbre = <?php echo $nbre_familles; ?>;
function check_form(form)
{
	if (form.section.value == 0)
	{
		alert("Merci de choisir une section.");
		return false;
	}
	if (nbre > 0)
	{
		if (form.famille.value != '0' && form.prenom.value != '' && form.nom_mb.value != '' && form.section.value != '')
		{
			getElement("envoi").disabled = true;
			getElement("envoi").value = "Patience...";
			return true;
		}
		else
		{
			alert("Merci d'indiquer ou de choisir au moins le nom de famille et le prénom du membre à ajouter.");
			return false;
		}
	}
	else
	{
		alert("Pour ajouter ce membre, tu dois d'abord créer une famille.");
		return false;
	}
}

function check_famille()
{
	if (getElement('famille2').value == getElement('famille').value)
	{
		alert("La deuxième adresse ne peut pas être identique à la famille du membre.");
		getElement('famille2').value = 0;
	}
}
</script>
<form action="newmb.php" method="post" name="form" class="form_gestion_unite" id="form" onsubmit="return check_form(this)">
<h2>Coordonn&eacute;es du membre
  <input type="hidden" name="step" value="3" />
</h2>
<fieldset>
<legend>Famille du membre * (adresse principale)</legend>
<p class="petitbleu">Cette adresse est l'adresse principale, c'est
donc elle qui g&egrave;re la cotisation du membre.</p>
<p>- La famille <span class="rmq">existe d&eacute;j&agrave;</span> dans la base 
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
<select name="famille" id="famille" onchange="if (this.value > 0) {alert('V&eacute;rifions quels membres sont dans cette famille.'); window.location='index.php?page=newmb&step=2&prenom=<?php echo stripslashes(stripslashes($_GET['prenom'])); ?>&nom=<?php echo stripslashes(stripslashes($_GET['nom'])); ?>&numfamille='+this.value;}" tabindex="1">
  <option value="0">&gt;&gt; Choisir un nom dans la liste</option>
<?php
			while ($famille = mysql_fetch_assoc($res))
			{
?>
  <option <?php if ($_GET['numfamille'] == $famille['numfamille']) {echo 'selected'; $lenomchoisi = $famille['nom'];} ?> value="<?php echo $famille['numfamille']; ?>"> <?php echo $famille['nom'].' ('.$famille['adresse'].')'; ?> </option>
<?php
			} // fin while
?>
</select>
<?php
			if (is_numeric($_GET['numfamille']))
			{
				$sql = "SELECT nummb, nom_mb, prenom, ddn, sexe, section FROM ".PREFIXE_TABLES."mb_membres WHERE famille = '$_GET[numfamille]' or famille2 = '$_GET[numfamille]' ORDER BY nom_mb, prenom ASC";
				if ($res = send_sql($db, $sql))
				{
					$nbre_p = mysql_num_rows($res);
					if ($nbre_p > 0)
					{
						$pluriel_mb = ($nbre_p > 1) ? 's' : '';
?></p>
<p class="rmqbleu">Il y a <?php echo $nbre_p.' membre'.$pluriel_mb.' inscrit'.$pluriel_mb;?> dans
  cette famille :</p>
<ul>
<?php
						while ($unmembre = mysql_fetch_assoc($res))
						{
							$ddn = '';
							$datenaissance = '';
							$fem = '';
							$ddn = date_ymd_dmy($unmembre['ddn'], 'enchiffres');
							$fem = ($unmembre['sexe'] == 'f') ? 'e' : '';
							$datenaissance = ($ddn != '00/00/0000') ? ' n&eacute;'.$fem.' le '.$ddn : '';
							if ($sections[$unmembre['section']]['anciens'] != 1)
							{
?>
  <li><a href="index.php?page=fichemb&amp;nummb=<?php echo $unmembre['nummb']; ?>" title="Voir sa fiche de membre"><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?></a><?php echo $datenaissance; ?></li>
<?php
							}
							else
							{
?>
  <li><a href="index.php?page=ficheancien&amp;nummb=<?php echo $unmembre['nummb']; ?>" title="Voir sa fiche de membre"><?php echo $unmembre['prenom'].' '.$unmembre['nom_mb']; ?></a> <?php echo $datenaissance; ?> - <span class="rmq">Ancien</span></li>
<?php
							}
						} // fin while
?>
</ul>
<?php
					} // fin if nbre_p > 0
				} // fin send sql
			} // fin numfamille != ""
		} // fin nbre_familles > 0
		else
		{
?>
<div class="msg">
<p align="center" class="rmq"><input type="hidden" name="famille" value="0">Il n'y a aucune famille dans la base pour le moment.</p>
</div>
<?php
		} // fin else nbre_familles > 0
?>
<p>- La famille n'est <span class="rmq">pas encore</span> dans la base</p>
<p align="center"><input name="Button" type="button" tabindex="2" onclick="window.location='index.php?page=newad&suite=newmb&prenom=<?php echo $_GET['prenom']; ?>&nom=<?php echo $_GET['nom']; echo ($_GET['actif'] == '0') ? '&actif=0' : ''; ?>';" value="Cr&eacute;er une nouvelle famille" />
</p>
</fieldset>
<fieldset>
<legend>Deuxi&egrave;me adresse (facultatif)</legend>
<p class="petitbleu">Cette adresse est destin&eacute;e &agrave; recevoir
  les courriers, mais ne g&egrave;re pas la cotisation du membre.</p>
<p>- La famille <span class="rmq">existe d&eacute;j&agrave;</span> dans la base 
<?php
		if ($nbre_familles > 0) 
		{
			$pl = ($nbre_familles > 1) ? 's' : '';
?>
<?php echo '('.$nbre_familles.' famille'.$pl.' trouv&eacute;e'.$pl.')'; ?></p>
<?php
		} // fin nbre familles > 0
?>
<p align="center"><?php
		if ($nbre_familles > 0)
		{
			$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
			$res2 = send_sql($db, $sql);
?>
<select name="famille2" id="famille2" tabindex="3" onchange="check_famille();">
  <option value="0">Aucune</option>
<?php
			while ($famille2 = mysql_fetch_assoc($res2))
			{
?>
  <option value="<?php echo $famille2['numfamille']; ?>"> <?php echo $famille2['nom'].' ('.$famille2['adresse'].')'; ?> </option>
<?php
			} // fin while famille2
?>
</select>
</p>
<?php
		} // fin nbre familles > 0
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucune famille dans la base pour le moment.</p>
</div>
<?php
		} // fin else nbre familles > 0
?>
<p>- La famille n'est <span class="rmq">pas encore</span> dans la base</p>
<p class="petit">Termine d'ajouter ce membre et modifie ensuite sa fiche pour lui adjoindre une seconde adresse.</p>
</fieldset>
<h2>Donn&eacute;es du membre</h2>
  <p class="petitbleu">Encode ici les donn&eacute;es personnelles du nouveau
    membre </p>
<fieldset><legend>Gestion de l'Unit&eacute;</legend>
  <p align="right"><span title="Participe-t-il d&eacute;j&agrave; aux activit&eacute;s ou est-ce une inscription pr&eacute;alable ?">Inscription
    sur <span class="rmqbleu">liste d'attente</span> ?</span>
  
    <input name="actif" type="radio" id="actifoui" tabindex="4" value="1"<?php if (!isset($_GET['actif'])) echo ' checked="checked"'; ?> />
  <label for="actifoui">Non</label>
    <input type="radio" name="actif" id="actifnon" value="0"<?php if ($_GET['actif'] == 0 and isset($_GET['actif'])) echo ' checked="checked"'; ?> />
  <label for="actifnon">Oui</label>
  </p>
<?php
		if ($user['niveau']['numniveau'] > 3)
		{
?>
<p align="right"><span class="rmqbleu">Cotisation</span> pay&eacute;e :
	
	<input name="cotisation" type="radio" id="cotioui" tabindex="5" value="1" />
	<label for="cotioui">oui</label>
	<input type="radio" name="cotisation" id="cotinon" value="0" />
	<label for="cotinon">non</label>
	<input type="radio" name="cotisation" id="cotiinconnu" value="-1" checked="checked" />
	<label for="cotiinconnu">?</label>
</p>
<?php
		} // fin numniveau > 3
?>
</fieldset>
<fieldset>
<legend>Données personnelles du membre</legend>
  <table border="0" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td>Pr&eacute;nom *</td>
      <td><input name="prenom" type="text" tabindex="6" value="<?php echo stripslashes(stripslashes($_GET['prenom'])); ?>" size="30" maxlength="100" /></td>
    </tr>
    <tr class="td-gris">
      <td>Nom *</td>
      <td><input name="nom_mb" type="text" id="nom_mb" tabindex="7" onchange="this.value=this.value.toUpperCase()" value="<?php echo (empty($_GET['nom'])) ? $lenomchoisi : stripslashes(stripslashes($_GET['nom'])); ?>" size="30" maxlength="100" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Sexe</td>
      <td><select name="sexe" tabindex="8">
          <option value="m"<?php echo ($sections[$user['numsection']]['sexe'] == 'm') ? ' selected="selected"' : ''; ?>>masculin</option>
          <option value="f"<?php echo ($sections[$user['numsection']]['sexe'] == 'f') ? ' selected="selected"' : ''; ?>>f&eacute;minin</option>
      </select></td>
    </tr>
    <tr class="td-gris">
      <td>Date de naissance</td>
      <td><input name="ddn" type="text" onfocus="if (this.value == 'jj/mm/aaaa') this.value = '';" onblur="if (this.value == '') this.value = 'jj/mm/aaaa';" value="jj/mm/aaaa" maxlength="10" style="width:80px;" tabindex="9" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Totem</td>
      <td><input name="totem" type="text" tabindex="10" size="30" maxlength="100" /></td>
    </tr>
    <tr class="td-gris">
      <td>Quali </td>
      <td><input name="quali" type="text" tabindex="11" size="30" maxlength="100" /></td>
    </tr>
    <tr class="td-gris">
      <td>Totem de jungle</td>
      <td><input name="totem_jungle" type="text" tabindex="12" size="30" /></td>
    </tr>
</table>
</fieldset>
<fieldset>
<legend>Dans sa section</legend>
<table border="0" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td>Fonction</td>
      <td><select name="fonction" tabindex="13">
          <?php
		foreach ($fonctions as $ligne)
		{
			if (!$ligne['anciens'])
			{
?>
          <option value="<?php echo $ligne['numfonction']; ?>"<?php echo ($ligne['numfonction'] == 1) ? ' selected' : ''; ?>><?php echo $ligne['nomfonction']; ?></option>
          <?php
			} // fin !anciens
		} // fin foreach $fonctions
?>
      </select></td>
    </tr>
    <tr>
      <td height="25">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Section<?php echo ($user['niveau']['numniveau'] > 3) ? ' *' : ''; ?></td>
      <td><?php
		if ($user['niveau']['numniveau'] > 3)
		{ // l'utilisateur est AnU ou webmaster, il peut choisir la section du membre
?>
          <select name="section" tabindex="14">
            <option value="0"></option>
            <?php
			foreach ($sections as $ligne)
			{
				if ($ligne['anciens'] == 0)
				{
?>
            <option value="<?php echo $ligne['numsection']; ?>"<?php echo ($ligne['numsection'] == $user['numsection']) ? ' selected="selected"' : ''; ?>><?php echo $ligne['nomsection']; ?></option>
            <?php
				}
			} // fin foreach $sections
?>
        </select>
          <?php
		} // fin numniveau > 3
		else
		{ // l'utilisateur ajoute un membre dans SA section
			echo '<input type="hidden" name="section" value="'.$user['numsection'].'" /><span class="rmqbleu">'.$sections[$user['numsection']]['nomsection'].'</span>';
		} // fin else numniveau > 3
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td>Statut</td>
      <td><?php
		if ($sections[$user['numsection']]['sizaines'] > 0 or $user['niveau']['numniveau'] > 3)
		{
?>
          <select name="cp_sizenier" tabindex="15">
            <?php
			foreach ($statuts as $num => $statut)
			{
?>
            <option value="<?php echo $num; ?>"><?php echo $statut; ?></option>
            <?php
			} // fin foreach $statuts
?>
        </select>
          <?php
		} // fin sizaines > 0 and numniveau > 3
		else
		{
			echo '<input type="hidden" name="cp_sizenier" value="0" />Pas de sizaines';
		} // fin else sizaines > 0 and numniveau > 3
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td><?php echo $t_sizaine; ?></td>
      <td><?php
		if ($user['niveau']['numniveau'] > 3 and is_array($sizaines))
		{ // on affiche toutes les sizaines/patrouilles de la db s'il y en a
?>
          <select name="siz_pat" tabindex="16">
            <option value="0" selected="selected"></option>
            <?php
			foreach ($sizaines as $ligne)
			{
?>
            <option value="<?php echo $ligne['numsizaine']; ?>"><?php echo $sections[$ligne['section_sizpat']]['nomsectionpt'].' - '.$ligne['nomsizaine']; ?></option>
            <?php
			} // fin foreach
?>
        </select>
          <?php
		} // fin numniveau > 3
		else if ($user['niveau']['numniveau'] == 3)
		{ // on affiche les sizaines/patrouilles de la section de l'utilisateur
			$nbre_sizaines = 0;
			if (is_array($sizaines))
			{ // on les s&eacute;lectionne
				foreach($sizaines as $sizaine)
				{
					if ($sizaine['section_sizpat'] == $user['numsection'])
					{
						$nbre_sizaines++;
					}
				}
			}
			else 
			{ // aucune sizaine/patrouille dans la db
				$nbre_sizaines = 0;
			}
			if ($sections[$user['numsection']]['sizaines'] > 0 and $nbre_sizaines > 0)
			{ // la section a ses sizaines/patrouilles
?>
          <select name="siz_pat" tabindex="16">
            <option value="0" selected="selected"></option>
            <?php
				foreach ($sizaines as $sizaine)
				{
					if ($sizaine['section_sizpat'] == $user['numsection'])
					{
?>
            <option value="<?php echo $sizaine['numsizaine']; ?>"><?php echo $sizaine['nomsizaine']; ?></option>
            <?php
					}
				} // fin num row > 0
?>
        </select>
          <?php
			}
			else if ($sections[$user['numsection']]['sizaines'] > 0)
			{ // la section n'a pas encore ses sizaines/patrouilles
				echo '<input type="hidden" name="siz_pat" value="0" /><a href="index.php?page=gestion_sizpat" title="Cette section est configur&eacute;e pour contenir des '.$t_sizaines.', tu peux les cr&eacute;er toi-m&ecirc;me ici.">Cr&eacute;er les '.$t_sizaines.' de la section</a>';
			} // fin else sizaines > 0
			else
			{ // la section ne doit pas avoir de sizaines/patrouilles
				echo '<input type="hidden" name="siz_pat" value="0" />Pas de sizaines';
			}
		} // fin else numniveau == 3
		else
		{ // la section ne doit pas avoir de sizaines/patrouilles
			echo '<input type="hidden" name="siz_pat" value="0" />Pas de sizaines';
		}
?>
      </td>
    </tr>
</table>
</fieldset>
<fieldset><legend>Infos utiles</legend>
<table border="0" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td>Email personnel</td>
      <td><input name="email_mb" type="text" tabindex="17" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td>Site web</td>
      <td><input name="siteweb" type="text" tabindex="18" value="http://" size="40" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Tel. personnel</td>
      <td><input name="telperso" type="text" maxlength="30" title="Ce num&eacute;ro est le deuxi&egrave;me num&eacute;ro qui est affich&eacute; dans les listings de staffs si le membre est animateur. format : xxx/xx xx xx" style="width:120px;" tabindex="19" />
        <span class="petitbleu"><br />
        Inutile d'indiquer ici un num&eacute;ro d&eacute;j&agrave; pr&eacute;sent
        dans la fiche famille. <br />
Ce num&eacute;ro sera plut&ocirc;t un num&eacute;ro de GSM (vraiment personnel).</span></td>
    </tr>
</table>
<p align="center">Remarques &eacute;ventuelles<br />
      <textarea name="rmq_mb" cols="35" rows="4" tabindex="20"></textarea>
  </p>
</fieldset>
<p align="center" class="petitbleu">Tu ne pourras ajouter une photo &agrave; la fiche du
  membre qu'en modifiant celle-ci.</p>
  <p align="center">Les champs marqu&eacute;s d'une * sont obligatoires, les autres 
    sont conseill&eacute;s ... fortement <img src="img/smileys/001.gif" alt="" width="15" height="15" /><br />
    <br />
    <input type="submit" name="Submit" id="envoi" value="Ajouter ce membre" tabindex="21" />
  </p>
</form>
<div class="instructions">
  <h2>Adresse principale</h2>
  <p>La gestion de l'unit&eacute; repose sur les familles. Chaque famille peut contenir
    plusieurs membres qui habitent sous le m&ecirc;me toit. Ce principe augmente l'efficacit&eacute;
    de la gestion des membres : un seul courrier par famille, demandes de cotisations
  en une fois, ... </p>
  <h2>Note</h2>
  <p>M&ecirc;me si tu ne disposes pas encore de toutes les coordonn&eacute;es du nouveau membre, cr&eacute;e sa famille. Seul le nom de famille est obligatoire, tu peux ajouter le reste par la suite. </p>
  <h2>Deuxi&egrave;me adresse</h2>
  <p>La deuxi&egrave;me adresse peut &ecirc;tre utilis&eacute;e pour les scouts dont les parents
    sont divorc&eacute;s mais souhaitent tous deux &ecirc;tre inform&eacute;s et recevoir les courriers.<br />
    A noter que <strong>seule l'adresse principale g&egrave;re les cotisations</strong>. </p>
  <h2>Conseils pour le format des entr&eacute;es</h2>
  <ul>
    <li>Nom de famille : <span class="rmq">V&eacute;rifie d'abord</span> 
      si la famille n'est pas d&eacute;j&agrave; dans la base de donn&eacute;es. 
      Si elle existe, s&eacute;lectionne-la. Inutile de cr&eacute;er deux fois 
      la m&ecirc;me adresse courrier</li>
    <li>Nom, Pr&eacute;nom, Totem : Ce sont des noms propres. Qui 
      dit nom propre dit <span class="rmq">majuscule.</span></li>
    <li>les num&eacute;ros de t&eacute;l&eacute;phone : format xxx/xx 
      xx xx (pas de points ou de / <span class="rmq">inutiles</span>. Ins&eacute;rer 
      des espaces pour la lisibilit&eacute; du num&eacute;ro)</li>
    <li>La famille et le membre sont li&eacute;s, inutile d'<span class="rmq">indiquer 
      deux fois</span> le m&ecirc;me num&eacute;ro de t&eacute;l&eacute;phone.</li>
    <li><span class="rmq">Laisser les cases inutiles vides</span> 
      s'il n'y a pas lieu de les remplir (pas de petite barre ou autre).</li>
  </ul>
  <h2>Rmq pour les ajouts d'animateurs :</h2>
  <p>Seules les donn&eacute;es de l'adresse principale sont 
    accessibles au public, c&agrave;d les champs suivants <strong>uniquement</strong> 
    : rue, num&eacute;ro, bo&icirc;te, cp, ville, t&eacute;l&eacute;phone 1, pr&eacute;nom, 
    nom, totem, quali, fonction, email personnel, t&eacute;l&eacute;phone personnel. 
    Toutes les autres donn&eacute;es rel&egrave;vent du domaine priv&eacute; et 
    ne sont accessibles qu'aux animateurs.<br />
    L'email est prot&eacute;g&eacute; contre les moteurs de recherche (protection 
    anti-spam).</p>
</div>
<?php
	}
	else if ($_POST['step'] == 3)
	{
		if (!empty($_POST['nom_mb']) and !empty($_POST['prenom']) and $_POST['section'] > 0 and $_POST['famille'] > 0)
		{
			$nom_mb = htmlentities(strtoupper($_POST['nom_mb']), ENT_QUOTES);
			$nom_mb_son = soundex2($_POST['nom_mb']);
			$prenom = htmlentities($_POST['prenom'], ENT_QUOTES);
			$prenom_son = soundex2($_POST['prenom']);
			$totem = htmlentities($_POST['totem'], ENT_QUOTES);
			$quali = htmlentities($_POST['quali'], ENT_QUOTES);
			$totem_jungle = htmlentities($_POST['totem_jungle'], ENT_QUOTES);
			$siteweb = ($_POST['siteweb'] == 'http://') ? '' : $_POST['siteweb'];
			$email_mb = (checkmail($_POST['email_mb'])) ? $_POST['email_mb'] : '';
			$ddn = $_POST['ddn'];
			if ($ddn != 'jj/mm/aaaa')
			{
				if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $ddn, $regs))
				{
					$ddn = "$regs[3]-$regs[2]-$regs[1]";
				}
				else
				{
					$ddn = '0000-00-00';
				}
			}
			else
			{
				$ddn = '0000-00-00';
			}
			$rmq_mb = htmlentities($_POST['rmq_mb'], ENT_QUOTES);
			$telperso = ($_POST['telperso'] == 'xxx/xx xx xx') ? '' : htmlentities($_POST['telperso'], ENT_QUOTES);
			if ($user['niveau']['numniveau'] > 3)
			{
				$cotisation = $_POST['cotisation'];
			} 
			else
			{
				$cotisation = -1;
			}
			$sql = "INSERT INTO ".PREFIXE_TABLES."mb_membres (nom_mb, nom_mb_son, prenom, prenom_son, famille, famille2, ddn, dateinscr, section, totem, quali, totem_jungle, actif, cotisation, rmq_mb, email_mb, siteweb, sexe, fonction, siz_pat, cp_sizenier, telperso, mb_createur, mb_datecreation, mb_lastmodifby, mb_lastmodif) 
			values 
			('$nom_mb', '$nom_mb_son', '$prenom', '$prenom_son', '$_POST[famille]', '$_POST[famille2]', '$ddn', now(), '$_POST[section]', '$totem', '$quali', '$totem_jungle', '$_POST[actif]', '$cotisation', '$rmq_mb', '$email_mb', '$siteweb', '$_POST[sexe]', '$_POST[fonction]', '$_POST[siz_pat]', '$_POST[cp_sizenier]', '$telperso', '$user[num]', now(), '$user[num]', now())";
			send_sql($db, $sql);
			if (!empty($email_mb))
			{ // abonnement à la newsletter
				abonnement_newsletter($email_mb, $prenom.' '.$nom_mb);
			}
			log_this('Création fiche membre : '.$nom_mb.' '.$prenom, 'newmb');
			header('Location: index.php?page=newmb&step=4');
		}
		else
		{
			header('Location: index.php?page=newmb&step=erreur');
		}
	}
	else if ($_GET['step'] == 4)
	{
		$sql = "SELECT nummb FROM ".PREFIXE_TABLES."mb_membres WHERE mb_createur = '$user[num]' ORDER BY mb_datecreation DESC LIMIT 1";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<h1>Ajout d'un membre dans la base de donn&eacute;es</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion des membres</a></p>
<div class="msg">
<p align="center">Le nouveau membre a bien &eacute;t&eacute; ajout&eacute;.</p>
<p align="center"><a href="index.php?page=newmb" class="bouton" tabindex="1">Ajouter un autre
    membre</a> <a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>" class="bouton" tabindex="2">Voir
  sa fiche membre</a></p>
</div>
<?php
		}
	}
	else if ($_GET['step'] == 'erreur')
	{
?>
<h1>Ajout d'un membre dans la base de donn&eacute;es</h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion des membres</a></p>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite, le membre n'a pas &eacute;t&eacute; 
  enregistr&eacute; !</p>
<p align="center"><a href="index.php?page=newmb" class="bouton" tabindex="1">Ajouter un membre</a></p>
</div>
<?php
	}
	else
	{
		include('404.php');	
	}
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
} // fin !defined in_site
?>