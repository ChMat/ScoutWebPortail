<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* modifmembre.php v 1.1 - Modification de la fiche d'un membre de l'Unité
* Gestion de l'Unité
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
*	Lien vers la page de passage pour l'AnU et le webmaster
*/

include_once('connex.php');
include_once('fonc.php');

if ($user['niveau']['numniveau'] <= 2)
{
	include('404.php');
}
else
{
	if (!isset($_GET['step']) and !isset($_POST['step']))
	{	
		$nummb = (is_numeric($_GET['nummb'])) ? $_GET['nummb'] : $_POST['nummb'];
		if (!empty($nummb))
		{
			$restreindre = '';
			if ($user['niveau']['numniveau'] == 3)
			{
				$restreindre .= " AND section = '$user[numsection]' ";
			}
			foreach ($sections as $section)
			{
				if ($section['anciens'] == 1)
				{
					$restreindre .= " AND section != '$section[numsection]' ";
				}
			}
			$sql = "SELECT * FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."mb_adresses as b WHERE a.nummb = '$nummb' AND a.famille = b.numfamille $restreindre";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{
				$membre = mysql_fetch_assoc($res);
				if (!defined('IN_SITE'))
				{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Modifier la fiche d'un membre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
				}
?>
<h1>Modifier la fiche de <?php echo $membre['prenom'].' '.$membre['nom_mb']; ?></h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.prenom.value != "" && form.nom_mb.value != "" && form.famille.value != 0)
	{
		return true;
	}
	else
	{
		alert("Merci de remplir les cases marquées d'une astérisque (Prénom, nom et Adresse).");
		return false;
	}
}

function addphoto()
{
	window.open('addimage.php?x=2', 'choixphoto', 'width=350,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
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
<form action="modifmembre.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
  <h2>Informations personnelles</h2>
  <input type="hidden" name="step" value="2" />
  <input type="hidden" name="nummb" value="<?php echo $nummb; ?>" />
  <fieldset>
  <legend>Gestion de l'Unit&eacute;</legend>
  <p align="right"><span title="Participe-t-il déjà aux activités ou est-ce une inscription préalable ?">Sur liste d'attente :</span>
    <input type="radio" name="actif" id="actifnon" value="1"<?php if ($membre['actif'] == '1') {echo ' checked="checked"'; } ?> />
    <label for="actifnon">Non</label>
    <input name="actif" type="radio" id="actifoui" value="0"<?php if ($membre['actif'] == '0') {echo ' checked="checked"'; } ?> />
    <label for="actifoui">Oui</label>
  </p>
  <?php
				if ($user['niveau']['numniveau'] > 3)
				{
?>
  <p align="right"> Cotisation pay&eacute;e :
    <input type="radio" name="cotisation" id="cotioui" value="1"<?php if ($membre['cotisation'] == '1') {echo ' checked="checked"';}?> />
    <label for="cotioui">oui</label>
    <input type="radio" name="cotisation" id="cotinon" value="0"<?php if ($membre['cotisation'] == '0') {echo ' checked="checked"';}?> />
    <label for="cotinon">non</label>
    <input type="radio" name="cotisation" id="cotiinconnu" value="-1"<?php if ($membre['cotisation'] == '-1') {echo ' checked="checked"';}?> />
    <label for="cotiinconnu">?</label>
  </p>
  <?php
				}
?>
  </fieldset>
  <fieldset>
  <legend>Donn&eacute;es personnelles du membre</legend>
  <table border="0" cellpadding="0" cellspacing="1">
    <tr class="td-gris">
      <td>Pr&eacute;nom * </td>
      <td><input name="prenom" type="text" id="prenom3" size="30" maxlength="100" value="<?php echo $membre['prenom']; ?>" tabindex="1" /></td>
    </tr>
    <tr class="td-gris">
      <td>Nom *</td>
      <td><input name="nom_mb" type="text" id="nom_mb6" size="30" maxlength="100" value="<?php echo $membre['nom_mb']; ?>" onchange="this.value=this.value.toUpperCase()" tabindex="2" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Sexe</td>
      <td><select name="sexe" id="select7" tabindex="3">
          <option value="m"<?php if ($membre['sexe'] == 'm') {echo ' selected'; } ?>>masculin</option>
          <option value="f"<?php if ($membre['sexe'] == 'f') {echo ' selected'; } ?>>f&eacute;minin</option>
        </select></td>
    </tr>
    <tr class="td-gris">
      <td>Date de naissance</td>
      <td><input name="ddn" type="text" id="ddn2" onfocus="if (this.value == 'jj/mm/aaaa') this.value = '';" onblur="if (this.value == '') this.value = 'jj/mm/aaaa';" value="<?php if ($membre['ddn'] != "0000-00-00") {echo date_ymd_dmy($membre['ddn'], "enchiffres"); } else {echo "jj/mm/aaaa";} ?>" maxlength="10" tabindex="4" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td>Totem</td>
      <td><input name="totem" type="text" id="totem" maxlength="100" value="<?php echo $membre['totem']; ?>" tabindex="6" />
      </td>
    </tr>
    <tr class="td-gris">
      <td>Quali</td>
      <td><input name="quali" type="text" id="quali" maxlength="100" value="<?php echo $membre['quali']; ?>" tabindex="7" />
      </td>
    </tr>
    <tr class="td-gris">
      <td>Totem de jungle</td>
      <td><input name="totem_jungle" type="text" id="quali" maxlength="100" value="<?php echo $membre['totem_jungle']; ?>" tabindex="8" /></td>
    </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Dans sa section</legend>
  <table border="0" cellpadding="0" cellspacing="1">
    <tr class="td-gris">
      <td>Fonction</td>
      <td><select name="fonction" tabindex="5">
          <option value="0"<?php if ($membre['fonction'] == 0) {echo ' selected'; } ?>></option>
          <?php
				$sql = "SELECT nomfonction, numfonction FROM ".PREFIXE_TABLES."unite_fonctions ORDER BY nomfonction ASC";
				$res = send_sql($db, $sql);
				while ($ligne = mysql_fetch_assoc($res))
				{
?>
          <option value="<?php echo $ligne['numfonction']; ?>"<?php if ($membre['fonction'] == $ligne['numfonction']) {echo ' selected'; } ?>><?php echo $ligne['nomfonction']; ?></option>
<?php
				}
?>
        </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" class="petitbleu"><?php if ($user['niveau']['numniveau'] == 3 or $user['numsection'] == $membre['section']) { ?>
        Pour faire passer ce membre dans une autre section, <a href="javascript:if(confirm('Les modifications que tu as déjà apportées à cette page ne seront pas enregistrées.\nEs-tu certain de vouloir effectuer le passage maintenant ?')) window.location='index.php?page=passage';">effectue un passage</a>.
        <?php } else if ($user['niveau']['numniveau'] > 3) { ?>
        Pour faire passer ce membre dans une autre section, <a href="javascript:if(confirm('Les modifications que tu as déjà apportées à cette page ne seront pas enregistrées.\nEs-tu certain de vouloir effectuer le passage maintenant ?')) window.location='index.php?page=passage&amp;section=<?php echo $membre['section']; ?>';">effectue un passage</a>.
        <?php } else { ?>
        Seuls les animateurs de ce membre ou les animateurs d'unit&eacute; peuvent le faire passer dans une autre section.
        <?php } ?></td>
    </tr>
    <tr class="td-gris">
      <td>Section</td>
      <td class="rmqbleu"><?php echo $sections[$membre['section']]['nomsection']; ?> </td>
    </tr>
    <tr class="td-gris">
      <td>Statut</td>
      <td><?php
				if ($sections[$membre['section']]['sizaines'] > 0)
				{
?>
        <select name="cp_sizenier" tabindex="9">
<?php
					foreach ($statuts as $num => $statut)
					{
?>
          <option value="<?php echo $num; ?>"<?php if ($membre['cp_sizenier'] == $num) {echo ' selected'; } ?>><?php echo $statut; ?></option>
<?php
					} // fin foreach $statuts
?>
        </select>
<?php
				} // fin sizaines > 0
				else
				{
					echo '<input type="hidden" name="cp_sizenier" value="0">Pas de sizaines/patrouilles dans cette section';
				} // fin else sizaines > 0
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td><?php echo $t_sizaine; ?></td>
      <td><?php
				$nbre_sizaines = 0;
				if (is_array($sizaines))
				{
					foreach($sizaines as $sizaine)
					{
						if ($sizaine['section_sizpat'] == $membre['section'])
						{
							$nbre_sizaines++;
						}
					}
				}
				else
				{
					$nbre_sizaines = 0;
				}
				if ($sections[$membre['section']]['sizaines'] > 0 and $nbre_sizaines > 0)
				{
?>
        <select name="siz_pat" tabindex="10">
          <option value="0" selected="selected"></option>
<?php
					foreach ($sizaines as $sizaine)
					{
						if ($sizaine['section_sizpat'] == $membre['section'])
						{
?>
          <option value="<?php echo $sizaine['numsizaine']; ?>"<?php echo ($membre['siz_pat'] == $sizaine['numsizaine']) ? ' selected' : ''; ?>><?php echo $sizaine['nomsizaine']; ?></option>
<?php
						}
					} // fin num row > 0
?>
        </select>
<?php
				} // fin sizaines > 0
				else if ($sections[$membre['section']]['sizaines'] > 0)
				{
					echo '<input type="hidden" name="siz_pat" value="0"><a href="index.php?page=gestion_sizpat" title="Cette section est configurée pour contenir des '.$t_sizaines.', tu peux les créer toi-même ici.">Cr&eacute;er les '.$t_sizaines.' de la section</a>';
				} // fin else sizaines > 0
				else
				{
					echo '<input type="hidden" name="siz_pat" value="0">Pas de sizaines/patrouilles dans cette section';
				} // fin else sizaines > 0
?>
      </td>
    </tr>
  </table>
  </fieldset>
  <fieldset>
  <legend>Infos utiles</legend>
  <table border="0" cellpadding="0" cellspacing="1">
    <tr class="td-gris">
      <td>Email personnel</td>
      <td><input name="email_mb" type="text" id="email_mb2" size="30" maxlength="255" value="<?php echo $membre['email_mb']; ?>" tabindex="11" /></td>
    </tr>
    <tr class="td-gris">
      <td>Site web</td>
      <td><input name="siteweb" type="text" id="siteweb2" value="<?php if (!empty($membre['siteweb'])) {echo $membre['siteweb']; } else {echo 'http://';}?>" size="30" maxlength="255" tabindex="12" /></td>
    </tr>
    <tr class="td-gris">
      <td>Tel. personnel</td>
      <td><input name="telperso" type="text" id="telperso2" size="20" maxlength="30" value="<?php echo $membre['telperso']; ?>" tabindex="13" /></td>
    </tr>
    <tr>
      <td colspan="2"><span class="petitbleu">Inutile d'indiquer ici un num&eacute;ro d&eacute;j&agrave; pr&eacute;sent dans la fiche famille.<br />
        Ce num&eacute;ro sera plut&ocirc;t un num&eacute;ro de GSM (vraiment personnel).</span></td>
    </tr>
  </table>
<p align="center">Remarques &eacute;ventuelles</p>
<textarea name="rmq_mb" cols="35" rows="4" id="rmq_mb" tabindex="14"><?php echo stripslashes($membre['rmq_mb']); ?></textarea>
  </fieldset>
  <fieldset>
  <legend>Photo du membre</legend>
  <p align="center">
    <input name="photo" type="text" id="photo" size="50" maxlength="255" value="<?php echo $membre['photo']; ?>" tabindex="15" />
    <input name="choisir" type="button" id="choisir" value="Choisir la photo" onclick="addphoto();" tabindex="16" />
  </p>
  <p align="right">Sa photo n'est pas encore sur le portail ? <a href="javascript:if(confirm('Les modifications que tu as déjà apportées à cette page ne seront pas enregistrées.\nEs-tu certain de vouloir déposer la photo maintenant ?')) window.location='index.php?page=upload_photomembre&amp;nummb=<?php echo $nummb; ?>';">D&eacute;poser la photo depuis ton ordinateur</a></p>
  </fieldset>
  <fieldset>
  <legend>Famille du membre *</legend>
  <p align="center">
    <select name="famille" id="famille" tabindex="18">
      <option value="0"<?php if ($membre['famille'] == 0) {echo ' selected';} ?>>Aucune</option>
<?php
				$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
				$res = send_sql($db, $sql);	
				while ($famille = mysql_fetch_assoc($res))
				{
?>
      <option<?php if ($membre['famille'] == $famille['numfamille']) {echo ' selected';} ?> value="<?php echo $famille['numfamille']; ?>"> <?php echo $famille['nom'].' ('.$famille['adresse'].')'; ?> </option>
<?php
				}
?>
    </select>
  </p>
  </fieldset>
  <fieldset>
  <legend>Deuxi&egrave;me adresse</legend>
  <p align="center">
    <select name="famille2" id="famille2" tabindex="19" onchange="check_famille()">
      <option value="0"<?php if ($membre['famille2'] == 0) {echo ' selected';} ?>>Aucune</option>
<?php
				$sql = "SELECT numfamille, nom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse FROM ".PREFIXE_TABLES."mb_adresses ORDER BY nom ASC";
				$res = send_sql($db, $sql);
				while ($famille2 = mysql_fetch_assoc($res))
				{
?>
      <option<?php if ($membre['famille2'] == $famille2['numfamille']) {echo ' selected';} ?> value="<?php echo $famille2['numfamille']; ?>"> <?php echo $famille2['nom'].' ('.$famille2['adresse'].')'; ?> </option>
<?php
				}
?>
    </select>
  </p>
  <p align="center">
    <input type="button" onclick="if(confirm('As-tu vérifié que l\'adresse secondaire n\'existe pas encore ?\nLes modifications que tu as déjà apportées à cette page ne seront pas enregistrées.')) window.location='index.php?page=newad&nummb=<?php echo $nummb; ?>&suite=newad';" value="Créer une deuxième adresse pour ce membre" tabindex="20" />
  </p>
  </fieldset>
  <p align="center">Les champs marqu&eacute;s d'une * sont obligatoires, les autres sont conseill&eacute;s ... fortement <img src="img/smileys/001.gif" alt="" width="15" height="15" /></p>
  <p align="center">
    <input type="submit" name="Submit" value="Enregistrer les modifications" tabindex="21" />
  </p>
</form>
<div class="instructions">
  <h2>Rmq pour les infos d'animateurs :</h2>
  <p>Seules les donn&eacute;es de l'adresse principale sont accessibles au public, c&agrave;d les champs suivants <strong>uniquement</strong> : rue, num&eacute;ro, bo&icirc;te, cp, ville, t&eacute;l&eacute;phone 1, pr&eacute;nom, nom, totem, quali, fonction, email personnel, t&eacute;l&eacute;phone personnel. Toutes les autres donn&eacute;es rel&egrave;vent du domaine priv&eacute; et ne sont accessibles qu'aux animateurs.</p>
</div>
<?php
			}
			else
			{
?>
<h1>Modifier la fiche d'un membre de l'Unit&eacute;</h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
  <p class="rmq" align="center">D&eacute;sol&eacute;, tu ne peux pas modifier cette fiche membre ou cette fiche n'existe pas.</p>
</div>
<?php
			}
		}
		else
		{
?>
<h1>Modifier la fiche d'un membre de l'Unit&eacute;</h1>
<?php
			$autrecritere = '';
			if ($user['niveau']['numniveau'] == 3) 
			{
				$autrecritere = "AND section = '$user[numsection]'";
			}
			else
			{
				foreach ($sections as $section)
				{
					if ($section['anciens'] == 1)
					{
						$autrecritere .= " AND section != '$section[numsection]'";
					}
				}
			}
			$sql = "SELECT nummb, nom_mb, prenom, concat(rue, ', ', numero, IF(bte <> '', concat(' bte ', bte), '')) as adresse, section FROM ".PREFIXE_TABLES."mb_membres as a, ".PREFIXE_TABLES."mb_adresses as b WHERE a.famille = b.numfamille $autrecritere ORDER BY nom_mb, prenom ASC";
			if ($res = send_sql($db, $sql))
			{
				$nbre_membres = mysql_num_rows($res);
			}
?>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<script language="JavaScript" type="text/JavaScript">
function check_form(form)
{
	if (form.nummb.value != "")
	{
		return true;
	}
	else
	{
		alert("Merci de bien vouloir choisir un membre avant d'envoyer le formulaire.");
		return false;
	}
}
</script>
<form action="index.php" method="post" name="form" id="form" onsubmit="return check_form(this)" class="form_gestion_unite">
  <h2>Choix du membre</h2>
  <input type="hidden" name="page" value="modifmembre" />
  <p>Choisis parmi les membres de
    <?php if ($user['niveau']['numniveau'] > 3) {echo 'l\'Unit&eacute;';} else {echo 'ta section';} ?>
    pr&eacute;sents dans la base :
    <?php if ($nbre_membres > 0) { if ($nbre_membres > 1) {$pl = 's';} else {$pl = '';} echo '('.$nbre_membres.' membre'.$pl.' trouv&eacute;'.$pl.')'; } ?>
  </p>
  <?php
			if ($nbre_membres > 0)
			{
?>
  <p align="center">
    <select name="nummb" size="15" tabindex="1">
      <?php
				while ($membre = mysql_fetch_assoc($res))
				{
?>
      <option value="<?php echo $membre['nummb']; ?>"> <?php echo $membre['nom_mb'].' '.$membre['prenom'].' ('.$membre['adresse'].')'; 
					if ($connected > 3 and !empty($sections[$membre['section']]['nomsectionpt'])) {echo ' - '.$sections[$membre['section']]['nomsectionpt'];} ?> </option>
      <?php
				}
?>
    </select>
  </p>
  <p align="center">
    <input type="submit" value="Modifier sa fiche" tabindex="2" />
  </p>
  <?php
			}
			else
			{
?>
  <div class="msg">
    <p align="center" class="rmq">Il n'y a aucun membre dans la base pour le moment.</p>
  </div>
  <?php
			}
?>
</form>
<div class="instructions">
  <h2>Astuce</h2>
  <p>Pour retrouver plus rapidement un membre, clique dans la liste et tape la premi&egrave;re lettre de son nom pour t'en approcher.</p>
</div>
<?php
		}
	}
	else if ($_POST['step'] == 2)
	{
		$can_modif = false;
		if (is_numeric($_POST['nummb']))
		{
			$section_mb = untruc(PREFIXE_TABLES.'mb_membres', 'section', 'nummb', $_POST['nummb']);
			if ($user['niveau']['numniveau'] > 3)
			{
				$can_modif = true;
			}
			else
			{
				$can_modif = ($user['numsection'] == $section_mb) ? true : false;
			}
		}
		if (!empty($_POST['nom_mb']) and !empty($_POST['prenom']) and is_numeric($_POST['famille']) and is_numeric($_POST['nummb']) and $can_modif)
		{
			$nom_mb = htmlentities(strtoupper($_POST['nom_mb']), ENT_QUOTES); 
			$nom_mb_son = soundex2($_POST['nom_mb']);
			$prenom = htmlentities($_POST['prenom'], ENT_QUOTES); 
			$prenom_son = soundex2($_POST['prenom']);
			$totem_jungle = htmlentities($_POST['totem_jungle'], ENT_QUOTES); 
			$totem = htmlentities($_POST['totem'], ENT_QUOTES); 
			$quali = htmlentities($_POST['quali'], ENT_QUOTES); 
			$siteweb = ($_POST['siteweb'] == 'http://') ? '' : htmlentities($_POST['siteweb'], ENT_QUOTES);
			if ($_POST['ddn'] != 'jj/mm/aaaa')
			{
				if (ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $_POST['ddn'], $regs))
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
			$telperso = htmlentities($_POST['telperso'], ENT_QUOTES); 
			$email_mb = (checkmail($_POST['email_mb'])) ? $_POST['email_mb'] : '';
			$famille2 = (is_numeric($_POST['famille2'])) ? $_POST['famille2'] : 0 ;
			if ($user['niveau']['numniveau'] > 3)
			{
				$cotisation = (is_numeric($_POST['cotisation'])) ? $_POST['cotisation'] : -1;
				$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET 
				nom_mb = '$nom_mb', nom_mb_son = '$nom_mb_son', prenom = '$prenom', prenom_son = '$prenom_son', famille = '$_POST[famille]', famille2 = '$_POST[famille2]', ddn = '$ddn', 
				totem_jungle = '$totem_jungle', totem = '$totem', quali = '$quali', photo = '$_POST[photo]', actif = '$_POST[actif]', 
				cotisation = '$cotisation', 
				rmq_mb = '$rmq_mb', email_mb = '$email_mb', siteweb = '$siteweb', sexe = '$_POST[sexe]', fonction = '$_POST[fonction]', 
				siz_pat = '$_POST[siz_pat]', cp_sizenier = '$_POST[cp_sizenier]', telperso = '$telperso', mb_lastmodifby = '$user[num]', 
				mb_lastmodif = now() WHERE nummb = '$_POST[nummb]'";
			}
			else
			{
				$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET 
				nom_mb = '$nom_mb', nom_mb_son = '$nom_mb_son', prenom = '$prenom', prenom_son = '$prenom_son', famille = '$_POST[famille]', famille2 = '$_POST[famille2]', ddn = '$ddn', 
				totem_jungle = '$totem_jungle', totem = '$totem', quali = '$quali', photo = '$_POST[photo]', actif = '$_POST[actif]', 
				rmq_mb = '$rmq_mb', email_mb = '$email_mb', siteweb = '$siteweb', sexe = '$_POST[sexe]', fonction = '$_POST[fonction]', 
				siz_pat = '$_POST[siz_pat]', cp_sizenier = '$_POST[cp_sizenier]', telperso = '$telperso', mb_lastmodifby = '$user[num]', 
				mb_lastmodif = now() WHERE nummb = '$_POST[nummb]'";
			}
			send_sql($db, $sql);
			if (!empty($email_mb))
			{ // abonnement à la newsletter
				abonnement_newsletter($email_mb, $prenom.' '.$nom_mb);
			}
			log_this('Modification fiche membre : '.$nom_mb.' '.$prenom.' ('.$_POST['nummb'].')', 'modifmembre');
			header('Location: index.php?page=modifmembre&step=3');
		}
		else
		{
			header('Location: index.php?page=modifmembre&nummb='.$_POST['nummb']);
		}
	}
	else if ($_GET['step'] == 3)
	{
		$sql = "SELECT nummb, prenom, nom_mb FROM ".PREFIXE_TABLES."mb_membres WHERE mb_lastmodifby = '$user[num]' ORDER BY mb_lastmodif DESC LIMIT 1";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{
			$membre = mysql_fetch_assoc($res);
?>
<h1>Modifier la fiche de <?php echo $membre['prenom'].' '.$membre['nom_mb']; ?></h1>
<p align="center"> <a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<div class="msg">
<p align="center">Les donn&eacute;es ont bien &eacute;t&eacute; mises &agrave; jour.</p>
<p align="center"><a href="index.php?page=fichemb&amp;nummb=<?php echo $membre['nummb']; ?>">Voir sa fiche membre</a></p>
</div>
<?php
		}
	}
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
