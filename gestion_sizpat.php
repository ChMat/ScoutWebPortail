<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_sizpat.php v 1.1 - Gestion (création, modification, suppression) des sizaines d'une section
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
*	Correction bug modification sizaine par AnS (Merci à Tarpan pour l'info)
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2)
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
<title>Gestion des <?php echo $t_sizaines; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des <?php echo $t_sizaines; ?></h1>
<p align="center"><a href="index.php?page=gestion_unite">Retour &agrave; la page Gestion de l'Unit&eacute;</a></p>
<?php
		if ($user['niveau']['numniveau'] == 3)
		{
?>
<div class="introduction">
<p>Cette page te permet de g&eacute;rer les diff&eacute;rentes <?php echo $t_sizaines; ?> qui existent 
dans ta Section. Attention de ne pas faire tout et n'importe quoi, les <?php echo $t_sizaines; ?> 
sont un &eacute;l&eacute;ment pivot de la gestion des membres de l'Unit&eacute; !</p>
</div>
<?php
		}
		else if ($user['niveau']['numniveau'] == 5)
		{
?>
<div class="introduction">
<p>Cette page te permet de g&eacute;rer les diff&eacute;rentes <?php echo $t_sizaines; ?> qui existent 
dans l'Unit&eacute;. Attention de ne pas faire tout et n'importe quoi, les <?php echo $t_sizaines; ?> 
sont un &eacute;l&eacute;ment pivot de la gestion des membres de l'Unit&eacute; !</p>
</div>
<?php
		}
		else
		{
?>
<div class="introduction">
<p>Sur cette page, tu peux consulter les informations relatives aux <?php echo $t_sizaines; ?> des diff&eacute;rentes 
sections.</p>
</div>
<?php
		}
		$nbresizaines = 0;
		if (is_array($sizaines))
		{
			foreach($sizaines as $sizaine)
			{
				if ($user['numsection'] == $sizaine['section_sizpat'] or $user['niveau']['numniveau'] > 3)
				{
					$nbresizaines++;
				}
			}
		}
		if ($nbresizaines > 0 and ($sections[$user['numsection']]['sizaines'] > 0 or $user['niveau']['numniveau'] > 3))
		{
?>
<p class="rmqbleu">Voici les <?php echo $t_sizaines; ?> pr&eacute;sentes dans la base de donn&eacute;es</p>
<?php
			if ($user['niveau']['numniveau'] == 3)
			{
?>
<p>Section en cours : <span class="rmq"><?php echo $sections[$user['numsection']]['nomsection']; ?></span></p>
<?php
			}
				
?>
<table width="80%" border="0" align="center" cellpadding="2" cellspacing="0">
  <tr> 
<?php
			if ($user['niveau']['numniveau'] > 3)
			{
?>
    <th>Section</th>
<?php
			}
?>
    <th><?php echo $t_sizaine; ?></th>
    <th>Cri</th>
  </tr>
<?php
			$i = 0;
			foreach ($sizaines as $sizaine)
			{
				if ($user['numsection'] == $sizaine['section_sizpat'] or $user['niveau']['numniveau'] > 3)
				{
					$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
?>
  <tr class="<?php echo $couleur; ?>">
<?php
					$i++;
					if ($user['niveau']['numniveau'] > 3)
					{
?>
	<td><?php echo $sections[$sizaine['section_sizpat']]['nomsection']; ?></td>
<?php
					}
?>
	<td><?php echo $sizaine['nomsizaine']; ?></td>
	<td><?php echo $sizaine['cri']; ?></td>
  </tr>
<?php
				}
			}
?>
</table>
<?php
			if ($msg == 1)
			{
?>
<div class="msg">
<p class="rmq" align="center">Modification effectu&eacute;e avec succ&egrave;s</p>
</div>
<?php 
			}
			if ($msg == 2)
			{
?>
<div class="msg">
<p class="rmq" align="center">Tu n'as pas les droits suffisants pour cette action.</p>
</div>
<?php 
			}
			if ($msg == 3)
			{
?>
<div class="msg">
<p class="rmq" align="center">Cette <?php echo $t_sizaine; ?> n'est pas vide. Suppression impossible !</p>
</div>
<?php 
			}
		}
		else
		{
			if ($user['niveau']['numniveau'] == 3)
			{
				if ($sections[$user['numsection']]['sizaines'] == 0)
				{
?>
<div class="msg">
<p align="center" class="rmq">D'apr&egrave;s les infos de ta section, tu n'utilises pas les <?php echo $t_sizaines; ?>.</p>
<p align="center"><a href="index.php?page=gestion_sections&amp;do=modifiersection&amp;step=2&amp;numsection=<?php echo $masection; ?>">Modifier ces infos</a>.</p>
</div>
<?php
				}
				else
				{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a encore aucune <?php echo $t_sizaine; ?> pour ta section dans la base de donn&eacute;es.</p>
</div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a encore aucune <?php echo $t_sizaine; ?> dans la base de donn&eacute;es de l'Unit&eacute;.</p>
</div>
<?php
			}
		}
?>
<?php
		if (($user['niveau']['numniveau'] == 3 and $sections[$user['numsection']]['sizaines'] > 0) or $user['niveau']['numniveau'] == 5)
		{
?>
<div class="menu_flottant">
<h2>G&eacute;rer les <?php echo $t_sizaines; ?></h2>
<p>
  - <a href="index.php?page=gestion_sizpat&amp;do=ajoutersizaine">Ajouter une <?php echo $t_sizaine; ?></a><?php 
			if ($nbresizaines > 0)
			{
?><br />
  - <a href="index.php?page=gestion_sizpat&amp;do=supprimersizaine">Supprimer une <?php echo $t_sizaine; ?></a><br />
  - <a href="index.php?page=gestion_sizpat&amp;do=modifiersizaine">Modifier les infos d'une <?php echo $t_sizaine; ?></a>
<?php
			}
?>
</p>
</div>
<?php
		}
// juste ici au-dessus placer </body></html>
	}
	else if ($do == 'ajoutersizaine')
	{
		if ($_POST['step'] == 2)
		{
			if ((($user['niveau']['numniveau'] == 3 and $user['numsection'] == $_POST['section']) or $user['niveau']['numniveau'] == 5) and is_numeric($_POST['section']))
			{
				$nomsizaine = htmlentities($_POST['nomsizaine'], ENT_QUOTES);
				$cri = htmlentities($_POST['cri'], ENT_QUOTES);
				$sql = "INSERT INTO ".PREFIXE_TABLES."unite_sizaines (nomsizaine, section_sizpat, cri) values ('$nomsizaine', '$_POST[section]', '$cri')";
				send_sql($db, $sql);
				reset_config();
				log_this("Ajout $nomsizaine", 'gestion_sizpat');
				header('Location: index.php?page=gestion_sizpat&msg=1');
			}
			else
			{
				header('Location: index.php?page=gestion_sizpat&msg=2');
			}
		}
		else
		{
?>
<h1>Ajouter une <?php echo $t_sizaine; ?> dans la base de donn&eacute;es</h1>
<p align="center"><a href="index.php?page=gestion_sizpat">Retour &agrave; la page Gestion des <?php echo $t_sizaines; ?></a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function clean(form)
{
	if (form.nomsizaine.value == "<?php echo $t_sizaine; ?> des ...") 
	{
		form.nomsizaine.value = "";
	}
}

function sectionchoisie(form)
{
	if (form.section.value != '' && form.section.value != 'erreur' && form.nomsizaine.value != '' && form.nomsizaine.value != '<?php echo $t_sizaine; ?> des ...') 
	{
		return true; 
	}
	else if (form.section.value == 'erreur')
	{
		alert("Tu ne peux choisir aucune section pour créer la <?php echo $t_sizaine; ?>.");
		return false;
	}
	else if (form.nomsizaine.value == '' || form.nomsizaine.value == '<?php echo $t_sizaine; ?> des ...')
	{
		alert("N'oublie pas de donner un nom à la <?php echo $t_sizaine; ?> que tu crées.");
		return false;
	}
	else
	{
		alert ("N'oublie pas de choisir une section.");
		return false;
	}
}
//-->
</script>
<form action="gestion_sizpat.php" method="post" name="form" class="form_config_site" id="form" onsubmit="return sectionchoisie(this)">
  <input type="hidden" name="do" value="ajoutersizaine" />
  <input type="hidden" name="step" value="2" />
  <table width="80%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%">Nom de la <?php echo $t_sizaine; ?> *</td>
      <td width="50%"><input name="nomsizaine" type="text" id="nomsizaine" size="40" value="<?php echo $t_sizaine; ?> des ..." onfocus="clean(this.form);" maxlength="100" /></td>
    </tr>
    <tr class="td-gris">
      <td width="50%">Section *</td>
      <td width="50%">
<?php
			if ($user['niveau']['numniveau'] == 5)
			{
?>
		<select name="section" id="section">
				  <option value="" selected="selected"></option>
<?php
				foreach ($sections as $ligne)
				{
					if (!is_unite($ligne['numsection']) and $ligne['sizaines'] > 0)
					{
?>
					<option value="<?php echo $ligne['numsection']; ?>"><?php echo $ligne['nomsection']; ?></option>
<?php
					}
				}
?>
		</select>
<?php
			}
			else if ($user['niveau']['numniveau'] == 3)
			{
				echo $sections[$user['numsection']]['nomsection'];
				?>
        <input type="hidden" name="section" value="<?php echo $user['numsection']; ?>" />
        <?php
			}
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td width="50%">Cri de <?php echo $t_sizaine; ?> :</td>
      <td width="50%"><input name="cri" type="text" id="cri" size="40" maxlength="400" /></td>
    </tr>
  </table>
  <p align="center">
    <input type="submit" value="Ajouter cette <?php echo $t_sizaine; ?>" />
  </p>
</form>
<?php
		}
	}
	else if ($do == 'supprimersizaine')
	{
		if ($_POST['step'] == 2 and is_numeric($_POST['sizaine']))
		{
			if ($user['niveau']['numniveau'] == 5 or $user['numsection'] == $sizaines[$_POST['sizaine']]['section_sizpat'])
			{
				$sql = "SELECT nummb FROM ".PREFIXE_TABLES."mb_membres WHERE siz_pat = '$_POST[sizaine]'";
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 0)
				{
					log_this('Suppression '.$sizaines[$_POST['sizaine']]['nomsizaine'], 'gestion_sizpat');
					$sql = "DELETE FROM ".PREFIXE_TABLES."unite_sizaines WHERE numsizaine = '$_POST[sizaine]'";
					send_sql($db, $sql);
					reset_config();
					header('Location: index.php?page=gestion_sizpat&msg=1');
				}
				else
				{
					header('Location: index.php?page=gestion_sizpat&msg=3');
				}
			}
			else
			{
				header('Location: index.php?page=gestion_sizpat&msg=2');
			}
		}
		else
		{
?>
<h1>Supprimer une <?php echo $t_sizaine; ?></h1>
<p align="center"><a href="index.php?page=gestion_sizpat">Retour &agrave; la page Gestion des <?php echo $t_sizaines; ?></a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function choix(section)
{
	var v = getElement("sizaine"+section).value;
	getElement("formsuppr").reset();
	getElement("sizaine"+section).value = v;
	getElement("sizaine").value = v;
}

function sizainechoisie(form)
{
	if (getElement('sizaine').value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir une <?php echo $t_sizaine; ?>.");
		return false;
	}
}
//-->
</script>
<noscript>
<div class="msg">
<p class="rmq">Javascript d&eacute;sactiv&eacute;</p>
<p>Ce script utilise le javascript. Merci de l'activer pour utiliser cette page. </p>
</div>
</noscript>
<form action="gestion_sizpat.php" method="post" name="form" class="form_config_site" id="formsuppr" onsubmit="return sizainechoisie(this)">
  <input type="hidden" name="do" value="supprimersizaine" />
  <input type="hidden" name="step" value="2" />
  <table width="80%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris"> 
      <td colspan="2">S&eacute;lectionne la <?php echo $t_sizaine; ?> que tu souhaites 
          supprimer :</td>
	</tr>
<?php
			if ($user['niveau']['numniveau'] == 3)
			{
?>
	<tr class="td-gris">
	  <td><?php 	echo $sections[$user['numsection']]['nomsection']; ?></td>
	  <td>
	  	<select name="sizaine">
          <option value=""></option>
<?php
				foreach ($sizaines as $ligne)
				{
					if ($ligne['section_sizpat'] == $user['numsection'])
					{
			?>
          <option value="<?php echo $ligne['numsizaine']; ?>"><?php echo $ligne['nomsizaine']; ?></option>
<?php
					}
				}
?>
        </select>
	  </td>
    </tr>
<?php
			}
			if ($user['niveau']['numniveau'] == 5)
			{
?>
		
    <input type="hidden" name="sizaine" id="sizaine" />
<?php
				foreach ($sections as $section)
				{
					if ($section['sizaines'] > 0)
					{
						echo "\n";							
?>
	  <tr class="td-gris">
	  	<td><?php 			echo $section['nomsection']; ?></td>
		<td>
	<select name="sizaine<?php echo $section['numsection']; ?>" onchange="choix(<?php echo $section['numsection']; ?>)" id="sizaine<?php echo $section['numsection']; ?>">
		<option value=""></option>
<?php
						foreach ($sizaines as $sizaine)
						{
							if ($sizaine['section_sizpat'] == $section['numsection'])
							{
?>
		<option value="<?php 		echo $sizaine['numsizaine']; ?>"><?php echo $sizaine['nomsizaine']; ?></option>
<?php
							}
						}
?>
	</select>
		</td>
	  </tr>
<?php
					} // fin if sizaines > 0
				} // fin foreach $sections
			} // fin else if
?>
    <tr class="td-1">
      <td class="petitbleu" colspan="2">Pour pouvoir supprimer une <?php echo $t_sizaine; ?>, aucun membre ne doit 
        y appartenir dans la base de donn&eacute;es. Si c'est le cas, la suppression 
        sera refus&eacute;e.</td>
    </tr>
  </table>
  <p align="center"> 
    <input type="submit" id="envoi_form" value="Supprimer la <?php echo $t_sizaine; ?>" disabled="disabled" />
  </p>
</form>
<script type="text/javascript">
<!--
getElement('envoi_form').disabled = false;
//-->
</script>
<?php
		
		}
	}
	else if ($do == 'modifiersizaine')
	{
		if ($_POST['step'] == 3 and is_numeric($_POST['numsizaine']))
		{
			if ($user['niveau']['numniveau'] == 3 and $user['numsection'] == $sizaines[$_POST['numsizaine']]['section_sizpat'])
			{
				$nomsizaine = htmlentities($_POST['nomsizaine'], ENT_QUOTES);
				$cri = htmlentities($_POST['cri'], ENT_QUOTES);
				$sql = "UPDATE ".PREFIXE_TABLES."unite_sizaines SET nomsizaine = '$nomsizaine', cri = '$cri' WHERE numsizaine = '$_POST[numsizaine]'";
				send_sql($db, $sql);
				reset_config();
				log_this('Modification '.$_POST['numsizaine'].' '.$nomsizaine, 'gestion_sizpat');
				header('Location: index.php?page=gestion_sizpat&msg=1');
			}
			else if ($user['niveau']['numniveau'] == 5)
			{
				$nomsizaine = htmlentities($_POST['nomsizaine'], ENT_QUOTES);
				$cri = htmlentities($_POST['cri'], ENT_QUOTES);
				$sql = "UPDATE ".PREFIXE_TABLES."unite_sizaines SET nomsizaine = '$nomsizaine', cri = '$cri', section_sizpat = '$_POST[section]' WHERE numsizaine = '$_POST[numsizaine]'";
				send_sql($db, $sql);
				if ($_POST['section'] != $_POST['oldsection'])
				{
					$sql = "UPDATE ".PREFIXE_TABLES."mb_membres SET siz_pat = '0' WHERE siz_pat = '$_POST[numsizaine]'";
					send_sql($db, $sql);
				}
				reset_config();
				log_this('Modification '.$_POST['numsizaine'].' '.$nomsizaine, 'gestion_sizpat');
				header('Location: index.php?page=gestion_sizpat&msg=1');
			}
			else
			{
				header('Location: index.php?page=gestion_sizpat&msg=2');
			}
		}
		else if ($_POST['step'] == 2 and is_numeric($_POST['sizaine']))
		{
			foreach($sizaines as $ligne)
			{
				if ($ligne['numsizaine'] == $_POST['sizaine'])
				{
?>
<h1>Modifier les infos d'une <?php echo $t_sizaine; ?></h1>
<p align="center"><a href="index.php?page=gestion_sizpat">Retour &agrave; la page Gestion des <?php echo $t_sizaines; ?></a></p>
<script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.nomsizaine.value != '') 
	{
		return confirm("Es-tu certain de vouloir modifier les infos de cette <?php echo $t_sizaine; ?> ?"); 
	}
	else
	{
		alert("N'oublie pas de donner un nom à la <?php echo $t_sizaine; ?> !");
		return false;
	}
}
//-->
</script>
<form action="gestion_sizpat.php" method="post" name="form1" class="form_config_site" id="form1" onsubmit="return check_form(this)">
  <input type="hidden" name="do" value="modifiersizaine" />
  <input type="hidden" name="step" value="3" />
  <input type="hidden" name="numsizaine" value="<?php echo $_POST['sizaine']; ?>" />
<table width="75%" border="0" align="center" cellpadding="2" cellspacing="0">
    <?php
					if ($user['niveau']['numniveau'] == 5)
					{
?>
    <tr class="td-gris">
      <td width="30%">Section de la <?php echo $t_sizaine; ?> *
        <input type="hidden" name="oldsection" value="<?php echo $_POST['section']; ?>" /></td>
      <td width="70%"> 
	  	<script type="text/javascript" language="JavaScript">
		<!--
		  function note(form)
		  {
		  	if (form.section.value != form.oldsection.value)
			{
				alert("Rappel !\n\nLes membres appartenant à cette <?php echo $t_sizaine; ?> seront détachés de celle-ci.");
			}
			else
			{
				alert("En sélectionnant la section d'origine de cette <?php echo $t_sizaine; ?>, les membres ne seront pas détachés de celle-ci.");
			}
		  }
		//-->
		</script>
	  	<select name="section" id="section" onchange="note(this.form)">
          <?php
						foreach ($sections as $section)
						{
							if (!is_unite($section['numsection']) and $section['sizaines'] > 0)
							{
				?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $ligne['section_sizpat']) ? ' selected' : ''; ?>><?php echo $section['nomsection']; ?></option>
          <?php
							}
						}
?>
        </select> </td>
    </tr>
    <?php
					}
?>
    <tr class="td-gris">
      <td width="30%">Nom de la <?php echo $t_sizaine; ?> *</td>
      <td width="70%"><input name="nomsizaine" type="text" id="nomsizaine" size="40" value="<?php echo $ligne['nomsizaine']; ?>" maxlength="100" /></td>
    </tr>
    <tr class="td-gris">
      <td width="30%">Cri de <?php echo $t_sizaine; ?></td>
      <td width="70%"><input name="cri" type="text" id="cri" size="30"  value="<?php echo $ligne['cri']; ?>" /></td>
    </tr>
<?php
					if ($user['niveau']['numniveau'] == 5)
					{
?>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td colspan="2" class="petitbleu"><span class="rmq">Attention !</span><br />
        Si tu changes la Section &agrave; laquelle appartient cette <?php echo $t_sizaine; ?>, 
        des erreurs risquent de se produire dans la gestion des membres qui s'y 
        trouvent. Afin d'emp&ecirc;cher ce probl&egrave;me, les membres concern&eacute;s 
        seront d&eacute;tach&eacute;s de leur <?php echo $t_sizaine; ?>. A toi ensuite, 
        d'effectuer les modifications n&eacute;cessaires.</td>
    </tr>
<?php
					}
?>
  </table>
  <p align="center">
    <input type="submit" name="Submit" value="Modifier les infos de cette <?php echo $t_sizaine; ?>" />
  </p>
</form>
<?php
				}
			}
		}
		else
		{
?>
<h1>Modifier les infos d'une <?php echo $t_sizaine; ?></h1>
<p align="center"><a href="index.php?page=gestion_sizpat">Retour &agrave; la page Gestion des <?php echo $t_sizaines; ?></a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function choix(section)
{
	var v = getElement("sizaine"+section).value;
	getElement("formmodif").reset();
	getElement("sizaine"+section).value = v;
	getElement("sizaine").value = v;
}

function sizainechoisie(form)
{
	if (form.sizaine.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir une <?php echo $t_sizaine; ?>.");
		return false;
	}
}
//-->
</script>
<noscript>
<div class="msg">
<p class="rmq">Javascript d&eacute;sactiv&eacute;</p>
<p>Ce script utilise le javascript. Merci de l'activer pour utiliser cette page. </p>
</div>
</noscript><form action="index.php" method="post" name="form" class="form_config_site" id="formmodif" onsubmit="return sizainechoisie(this)">

  <input type="hidden" name="page" value="gestion_sizpat" />
  <input type="hidden" name="do" value="modifiersizaine" />
  <input type="hidden" name="step" value="2" />
  <table width="80%" border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td width="50%" height="25" colspan="2" class="rmqbleu">S&eacute;lectionne la <?php echo $t_sizaine; ?> &agrave; modifier :</td>
	</tr>

<?php
			if ($user['niveau']['numniveau'] == 3)
			{
?>
	<tr class="td-gris">
	  <td><?php 	echo $sections[$user['numsection']]['nomsection']; ?></td>
	  <td>
	  	<select name="sizaine">
          <option value="" selected="selected"></option>
<?php
				foreach ($sizaines as $ligne)
				{
					if ($ligne['section_sizpat'] == $user['numsection'])
					{
			?>
          <option value="<?php echo $ligne['numsizaine']; ?>"><?php echo $ligne['nomsizaine']; ?></option>
<?php
					}
				}
?>
        </select>
	  </td>
    </tr>
<?php
			}
			else if ($user['niveau']['numniveau'] == 5)
			{
?>
		
    <input type="hidden" name="sizaine" id="sizaine" />
<?php
				foreach ($sections as $section)
				{
					if ($section['sizaines'] > 0)
					{
						echo "\n";							
?>
	  <tr class="td-gris">
	  	<td><?php 			echo $section['nomsection']; ?></td>
		<td>
	<select name="sizaine<?php echo $section['numsection']; ?>" onchange="choix(<?php echo $section['numsection']; ?>)" id="sizaine<?php echo $section['numsection']; ?>">
		<option value=""></option>
<?php
						foreach ($sizaines as $sizaine)
						{
							if ($sizaine['section_sizpat'] == $section['numsection'])
							{
?>
		<option value="<?php 		echo $sizaine['numsizaine']; ?>"><?php echo $sizaine['nomsizaine']; ?></option>
<?php
							}
						}
?>
	</select>
		</td>
	  </tr>
<?php
					} // fin if sizaines > 0
				} // fin foreach $sections
			} // fin else if
?>
  </table>
  <p align="center"> 
    <input type="submit" value="Sélectionner cette <?php echo $t_sizaine; ?>" />
  </p>
</form>
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