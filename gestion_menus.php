<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_menus.php v 1.1 - Gestion des menus du portail
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
*	Correction bug modification d'un lien, affichage section et menu anciens
*	Affichage des adresses prédéfinies partout sur la page
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] <= 2 and $user['assistantwebmaster'] != 1)
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
<title>Gestion des Sections</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
		}
?>
<h1>Gestion des menus du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'Accueil Membres</a></p>
<?php
		if (!$site_menus)
		{
?>
<div class="msg">
<p align="center" class="rmq">Les menus du portail ne sont pas d&eacute;finis.</p>
</div>
<?php
		}
		else
		{
			$nbremenus = count($site_menus);
			if ($nbremenus > 0)
			{
?>
<div class="instructions">
  <p>Cette page te permet de g&eacute;rer les diff&eacute;rents menus du site d'Unit&eacute;. Attention de ne pas faire tout et n'importe quoi, les menus sont un &eacute;l&eacute;ment essentiel du site d'Unit&eacute; !</p>
</div>
<div class="form_config_site">
  <h2>Configurer les menus</h2>
  <ul>
    <?php
				if ($user['niveau']['numniveau'] == 5)
				{
?>
    <li><a href="index.php?page=config_site&amp;categorie=menus">Param&egrave;tres d'affichage des menus de sections</a></li>
    <li><a href="index.php?page=gestion_sections_site">G&eacute;rer l'espace web des Sections et leur position dans le menu du portail</a></li>
    <?php
				}
?>
    <li><a href="index.php?page=gestion_menu_standard">Modifier le menu g&eacute;n&eacute;ral</a> <span class="petit">(il appara&icirc;t sous les menus des sections)</span></li>
  </ul>
  <h2>Modifier les menus des sections</h2>
  <p class="petitbleu">Les menus des sections sont sp&eacute;cifiques &agrave; chaque section ou unit&eacute;.</p>
  <?php
				if ($_GET['msg'] == 1)
				{
?>
<div class="msg">
<p align="center">Modification effectu&eacute;e avec succ&egrave;s</p>
</div>
<?php
				}
				if ($_GET['msg'] == 4)
				{
?>
<div class="msg">
<p class="rmq" align="center">Une erreur s'est produite. Echec de la requ&ecirc;te !</p>
</div>
<?php
				}
				if (is_array($sections))
				{
					$i = 0;
					$tri = array('section_menu', 'position_menu');
					$site_menus_bis = super_sort($site_menus);
					foreach ($sections as $section)
					{
?>
  <table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <?php
						if (!empty($section['site_section']))
						{ // espace web de la Section/unité actif
?>
    <tr>
      <th align="left" colspan="3"><?php echo $section['nomsection'].' - <acronym title="Cette lettre est l\'indicatif web de la section">'.$section['site_section'].'</acronym>'; ?></th>
      <th align="right"><?php echo '<a href="index.php?page=gestion_menus&amp;do=ajoutermenu&amp;section='.$section['numsection'].'" title="Ajouter un lien à ce menu"><img src="templates/default/images/plus.png" alt="Ajouter un lien à ce menu" border="0" /></a>'; ?> <?php echo ($user['niveau']['numniveau'] == 5) ? '<a href="index.php?page=gestion_sections_site" title="D&eacute;sactiver l\'espace web de cette Section"><img src="templates/default/images/logoff.png" alt="D&eacute;sactiver l\'espace web de cette Section" border="0" /></a>' : ''; ?></th>
    </tr>
    <?php
						}
						else
						{ // espace web de la Section/unité inactif
?>
    <tr class="td-1">
      <td colspan="3" class="rmq" title="Espace web de la Section inactif"><?php echo $section['nomsection']; ?></td>
      <td align="right"><?php echo ($user['niveau']['numniveau'] == 5) ? '<a href="index.php?page=gestion_sections_site" title="Activer l\'espace web de cette Section"><img src="templates/default/images/login.png" alt="Activer l\'espace web de cette Section" border="0" /></a>' : 'Espace web inactif'; ?></td>
    </tr>
    <?php
						}
						$j = 0;
						foreach ($site_menus as $menu)
						{
							if ($menu['section_menu'] == $section['numsection'])
							{
								$j++;
?>
    <tr>
      <td width="10">&nbsp;</td>
      <td><?php echo $menu['position_menu']; ?> <span title="<?php echo $menu['description_menu']; ?>"><?php echo $menu['texte_menu']; ?></span></td>
      <td><?php echo $menu['lien_menu']; ?></td>
      <td align="right"><a href="index.php?page=gestion_menus&amp;do=modifiermenu&amp;step=2&amp;id_menu=<?php echo $menu['id_menu']; ?>" title="Modifier ce lien"><img src="templates/default/images/fiche.png" alt="Modifier ce lien" border="0" /></a> &nbsp;<a href="index.php?page=gestion_menus&amp;do=supprimermenu&amp;id_menu=<?php echo $menu['id_menu']; ?>" title="Supprimer ce lien"><img src="templates/default/images/moins.png" alt="Supprimer ce lien" border="0" /></a> </td>
    </tr>
    <?php
							}
						} // fin foreach $site_menus
						if ($j == 0)
						{
?>
    <tr>
      <td align="center" colspan="4" class="rmq">Le menu de cette section est vide.</td>
    </tr>
<?php
						}
?>
  </table>
<?php
					} // fin foreach $sections
?>
</div>
<?php
				} // fin is_array
				else
				{
				
				}
			} // fin nbremenus > 0
			else
			{
?>
<div class="msg">
<p align="center">Il n'y a encore aucun lien dans les menus du portail.</p>
</div>
<?php
			}
		}
		if ($nbremenus == 0)
		{
?>
<div class="form_config_site">
  <h2>Configurer les menus</h2>
  <ul>
<?php
			if (is_array($sections))
			{
				if (count_sites_sections() > 0)
				{
?>
    <li><a href="index.php?page=gestion_menus&amp;do=ajoutermenu">Ajouter un lien aux menus des sections</a></li>
<?php
				}
				if ($user['niveau']['numniveau'] == 5)
				{
?>
    <li><a href="index.php?page=config_site&amp;categorie=menus">Param&egrave;tres d'affichage des menus de sections</a></li>
    <li><a href="index.php?page=gestion_sections_site">G&eacute;rer l'espace web des Sections</a></li>
<?php
				}
			}
			else
			{
?>
    <li>Pour g&eacute;rer les menus des sections, il faut d'abord <?php echo ($user['niveau']['numniveau'] == 5) ? '<a href="index.php?page=gestion_sections">' : ''; ?>cr&eacute;er au moins une section<?php echo ($user['niveau']['numniveau'] == 5) ? '</a>' : ''; ?>.</li>
<?php
			}
?>
    <li><a href="index.php?page=gestion_menu_standard">Configurer le menu g&eacute;n&eacute;ral </a></li>
  </ul>
</div>
<?php
		} // fin if ($nbremenus == 0)
	}
	else if ($do == 'ajoutermenu')
	{
		if ($_POST['step'] == 2)
		{
			if (!empty($_POST['texte_menu']) and !empty($_POST['lien_menu']))
			{
				$texte_menu = htmlentities($_POST['texte_menu'], ENT_QUOTES);
				$description_menu = htmlentities($_POST['description_menu'], ENT_QUOTES);
				$lien_menu = htmlentities($_POST['lien_menu'], ENT_QUOTES);
				$sql = "INSERT INTO ".PREFIXE_TABLES."site_menus 
				(section_menu, position_menu, description_menu, texte_menu, lien_menu) 
				values 
				('$_POST[section_menu]', '$_POST[position_menu]', '$description_menu', '$texte_menu', '$lien_menu')";
				send_sql($db, $sql);
				reset_config();
				log_this('Création menu '.$texte_menu, 'gestion_menus');
				header('Location: index.php?page=gestion_menus&msg=1');
			}
			else
			{
				header('Location: index.php?page=gestion_menus&msg=4');
			}
		}
		else
		{
?>
<h1>Gestion des menus du portail</h1>
<p align="center"><a href="index.php?page=gestion_menus">Retour &agrave; la page Gestion des Menus du portail</a></p>
<form action="gestion_menus.php" method="post" name="form1" id="form1" onsubmit="return check_form(this)" class="form_config_site">
  <h2>Ajouter un lien</h2>
  <script type="text/javascript" language="JavaScript">
<!--
function check_form(form)
{
	if (form.texte_menu.value != "" && form.lien_menu.value != "")
	{
		 return true;
	}
	else
	{
		alert("Merci de remplir les champs marqués d'une astérisque.");
		return false;
	}
}
//-->
</script>
  <?php
			foreach ($sections as $section)
			{
				if (!empty($section['site_section']) and $_GET['section'] == $section['numsection'])
				{
?>
  <table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr>
      <th colspan="4">Menu actuel de la section : <span class="rmqbleu"><?php echo $section['nomsection']; ?></span> - <acronym title="Cette lettre est l'indicatif web de la section"><?php echo $section['site_section']; ?></acronym></th>
    </tr>
    <?php
					$j = $derniere_position_menu = 0;
					if (is_array($site_menus))
					{
						foreach ($site_menus as $menu)
						{
							if ($menu['section_menu'] == $section['numsection'])
							{
								$j++;
								echo '<tr>';
								echo '<td width="10">&nbsp;</td>';
								echo '<td>'.$menu['position_menu'].'. <span title="'.$menu['description_menu'].'">'.$menu['texte_menu'].'</span></td>';
								echo '<td>'.$menu['lien_menu'].'</td>';
								echo '<td><a href="index.php?page=gestion_menus&amp;do=modifiermenu&amp;step=2&amp;id_menu='.$menu['id_menu'].'" title="Modifier ce lien"><img src="templates/default/images/fiche.png" alt="Modifier ce lien" border="0" /></a></td>';
								echo '</tr>';
								$derniere_position_menu = ($menu['position_menu'] > $derniere_position_menu) ? $menu['position_menu'] : $derniere_position_menu;
							}
						} // fin foreach $site_menus
					} // fin is_array
					if ($j == 0)
					{
						echo '<tr>';
						echo '<td align="center" colspan="2">Le menu de cette section est vide.</td>';
						echo '</tr>';
					}
?>
  </table>
  <?php
				}
			} // fin foreach $sections
?>
  <input type="hidden" name="do" value="ajoutermenu" />
  <input type="hidden" name="step" value="2" />
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td height="29" colspan="2" valign="top" class="td-gris">Section dont d&eacute;pend le lien
        <select name="section_menu">
          <?php
			$nbre_espaces_web = 0;
			foreach($sections as $section)
			{
				if (!empty($section['site_section']))
				{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $_GET['section']) ? ' selected' : ''; ?>><?php echo $section['nomsection'].' - '.$section['site_section']; ?></option>
          <?php
					$nbre_espaces_web++;
				}
			}
?>
        </select>
      </td>
      <td width="5" rowspan="6">&nbsp;</td>
      <td rowspan="6" valign="top">Pages existantes<br />
        <?php
	$sql = "SELECT page, specifiquesection FROM ".PREFIXE_TABLES."pagessections as a WHERE statut = '2' ORDER BY specifiquesection, page";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
        <select name="page" size="8" onchange="getElement('lien_menu').value = this.value;">
          <?php
			while ($ligne = mysql_fetch_assoc($res))
			{
				// suppression du g pour les pages générales du portail
				$nivcible = $sections[$ligne['specifiquesection']]['site_section'];
				// suppression de l'indication x_ devant la page d'accueil d'une section
				$nivcible_url = ($ligne['page'] == 'index'.$nivcible) ? '' : $nivcible;
				
				$separateur = (!empty($nivcible) and $ligne['page'] != 'index'.$nivcible and $site['url_rewriting_actif'] == 1) ? '_' : '';
								
				$nivcible_sans_url_rewriting = (!empty($nivcible)) ? 'niv='.$nivcible.'&amp;' : '';
				$lien_propose = ($site['url_rewriting_actif'] == 1) ? $nivcible_url.$separateur.$ligne['page'].'.htm' : 'index.php?'.$nivcible_sans_url_rewriting.'page='.$ligne['page'];
				echo '<option value="'.$lien_propose.'">'.$lien_propose.'</option>';
			}
?>
        </select>
        <?php
		}
		else
		{
			echo '<span class="rmq">Aucune pour l\'instant</span>';
		}
	}
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Position du lien</td>
      <td valign="top"><select name="position_menu" id="position_menu">
          <?php
	for ($i = 1; $i <= 20; $i++)
	{
		$c = (isset($derniere_position_menu) and $i == $derniere_position_menu + 1) ? ' selected' : '';
		echo '<option value="'.$i.'"'.$c.'>'.$i.'</option>';
	}
?>
      </select></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Intitul&eacute; du lien*</td>
      <td><input name="texte_menu" type="text" id="texte_menu" size="20" maxlength="255" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Description du lien</td>
      <td><input name="description_menu" type="text" id="description_menu" size="40" maxlength="255" />
      </td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Adresse du lien*</td>
      <td><input name="lien_menu" type="text" id="lien_menu" size="40" maxlength="255" /></td>
    </tr>
  </table>
  <p align="center">
    <input type="submit" name="Submit"<?php echo ($nbre_espaces_web > 0) ? ' value="Ajouter ce lien aux menus"' : ' disabled="true" value="Aucun espace web de section actif"'; ?> />
  </p>
</form>
<div class="instructions">
  <h2>Informations utiles</h2>
  <p class="petit"> - Pour t'aider &agrave; cr&eacute;er le menu, la liste des pages d&eacute;j&agrave; publi&eacute;es sur le portail est affich&eacute;e.<br />
    - Clique sur le nom d'une des pages existantes pour l'ajouter dans la case &quot;Adresse du lien&quot;.<br />
    - Tu peux aussi faire un lien vers n'importe quelle autre page du portail ou du web tout entier, il suffit d'indiquer la bonne adresse.<br />
    <span class="petitbleu">- Si tu cr&eacute;es un lien vers une page inexistante du portail, tu pourras la cr&eacute;er en cliquant sur le lien que tu as cr&eacute;&eacute; dans le menu.<br />
    - Si la section que tu recherches n'est pas dans la liste, c'est que son espace web n'a pas &eacute;t&eacute; activ&eacute; par le webmaster.</span></p>
</div>
<?php
		}
	}
	else if ($do == 'supprimermenu')
	{
		if ($_POST['step'] == 2 and is_numeric($_POST['id_menu']))
		{
			log_this('Suppression menu '.htmlentities($site_menus[$_POST['id_menu']]['texte_menu'], ENT_QUOTES), 'gestion_menus');
			$sql = "DELETE FROM ".PREFIXE_TABLES."site_menus WHERE id_menu = '$_POST[id_menu]'";
			send_sql($db, $sql);
			reset_config();
			header('Location: index.php?page=gestion_menus&msg=1');
		}
		else
		{
?>
<h1>Gestion des menus du portail</h1>
<p align="center"><a href="index.php?page=gestion_menus">Retour &agrave; la page Gestion des Menus du portail</a></p>
<script language="JavaScript" type="text/JavaScript">
<!--
function menuchoisi(form)
{
	if (form.id_menu.value != "") 
	{
		return confirm('Es-tu certain de vouloir supprimer ce lien des menus ?'); 
	}
	else 
	{
		alert ("N'oublie pas de choisir un lien.");
		return false;
	}
}
//-->
</script>
<form action="gestion_menus.php" method="post" name="form" id="form" onsubmit="return menuchoisi(this)" class="form_config_site">
  <h2>Supprimer un lien </h2>
  <p align="center"> S&eacute;lectionne le lien que tu souhaites supprimer des menus :
    <input type="hidden" name="step" value="2" />
    <input type="hidden" name="do" value="supprimermenu" />
    <select name="id_menu">
      <option value="" selected="selected"></option>
      <?php
			foreach ($site_menus as $menu)
			{
?>
      <option value="<?php echo $menu['id_menu']; ?>"<?php echo ($_GET['id_menu'] == $menu['id_menu']) ? ' selected' : ''; ?>><?php echo $sections[$menu['section_menu']]['nomsectionpt'].' - '.$menu['texte_menu']; ?></option>
      <?php
			}
?>
    </select>
  </p>
  <p align="center">
    <input type="submit" value="Supprimer le lien" />
  </p>
</form>
<div class="instructions">
  <p>En supprimant un lien, tu ne supprimes pas la page du portail, tu peux g&eacute;n&eacute;ralement <a href="index.php?page=pagesection">la retrouver ici</a>.<br />
    Si tu te trompes, n'h&eacute;site pas &agrave; recr&eacute;er un lien pour &eacute;viter de perdre les visiteurs ;)</p>
</div>
<?php
		
		}
	}
	else if ($do == 'modifiermenu')
	{
		if ($_POST['step'] == 3 and !empty($_POST['texte_menu']) and !empty($_POST['lien_menu']) and is_numeric($_POST['id_menu']))
		{
			$texte_menu = htmlentities($_POST['texte_menu'], ENT_QUOTES);
			$lien_menu = htmlentities($_POST['lien_menu'], ENT_QUOTES);
			$description_menu = htmlentities($_POST['description_menu'], ENT_QUOTES);
			$sql = "UPDATE ".PREFIXE_TABLES."site_menus SET 
			texte_menu = '$texte_menu', lien_menu = '$lien_menu', description_menu = '$description_menu', 
			position_menu = '$_POST[position_menu]', section_menu = '$_POST[section_menu]'
			WHERE id_menu = '$_POST[id_menu]'";
			send_sql($db, $sql);
			reset_config();
			log_this('Modification menu '.$_POST['id_menu'].' '.$texte_menu, 'gestion_menus');
			header('Location: index.php?page=gestion_menus&msg=1');
		}
		else if ($_GET['step'] == 2 and is_numeric($_GET['id_menu']))
		{
			$menu_a_modifier = $site_menus[$_GET['id_menu']];
?>
<h1>Gestion des menus du portail</h1>
<p align="center"><a href="index.php?page=gestion_menus">Retour &agrave; la page Gestion des Menus du portail</a></p>
<form action="gestion_menus.php" method="post" name="form1" id="form1" onsubmit="return confirm('Es-tu certain de vouloir modifier ce lien ?')" class="form_config_site">
  <h2>Modifier un lien</h2>
  <?php
			foreach ($sections as $section)
			{
				if (!empty($section['site_section']) and $site_menus[$_GET['id_menu']]['section_menu'] == $section['numsection'])
				{ // affichage du menu actuel de la section concernée par le lien
?>
  <table border="0" align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
    <tr>
      <th colspan="4">Menu actuel de la section : <span class="rmqbleu"><?php echo $section['nomsection']; ?></span> - <acronym title="Cette lettre est l'indicatif web de la section"><?php echo $section['site_section']; ?></acronym></th>
    </tr>
    <?php
					$j = 0;
					foreach ($site_menus as $menu)
					{
						if ($menu['section_menu'] == $section['numsection'])
						{
							$j++;
							echo ($menu['id_menu'] == $_GET['id_menu']) ? '<tr class="td-1">' : '<tr>';
							echo '<td width="10">&nbsp;</td>';
							echo '<td>'.$menu['position_menu'].'. <span title="'.$menu['description_menu'].'">'.$menu['texte_menu'].'</span></td>';
							echo '<td>'.$menu['lien_menu'].'</td>';
							echo '<td><a href="index.php?page=gestion_menus&amp;do=modifiermenu&amp;step=2&amp;id_menu='.$menu['id_menu'].'" title="Modifier ce lien"><img src="templates/default/images/fiche.png" alt="Modifier ce lien" border="0" /></a></td>';
							echo '</tr>';
						}
					} // fin foreach $site_menus
					if ($j == 0)
					{
						echo '<tr>';
						echo '<td align="center" colspan="2">Le menu de cette section est vide.</td>';
						echo '</tr>';
					}
?>
  </table>
  <?php
				}
			} // fin foreach $sections
?>
  <p>
    <input type="hidden" name="do" value="modifiermenu" />
    <input type="hidden" name="step" value="3" />
    <input type="hidden" name="id_menu" value="<?php echo $_GET['id_menu']; ?>" />
  </p>
  <table border="0" align="center" cellpadding="2" cellspacing="0">
    <tr class="td-gris">
      <td colspan="2" valign="top">Section dont d&eacute;pend le lien
        <select name="section_menu">
          <?php
			foreach($sections as $section)
			{ // affichage de la liste des sections ayant un espace web actif
				if (!empty($section['site_section']))
				{
?>
          <option value="<?php echo $section['numsection']; ?>"<?php echo ($section['numsection'] == $menu_a_modifier['section_menu']) ? ' selected' : ''; ?>><?php echo $section['nomsection'].' - '.$section['site_section']; ?></option>
          <?php
				}
			}
?>
        </select>
      </td>
      <td rowspan="6" valign="top">Pages existantes<br />
        <?php
	$sql = "SELECT page, specifiquesection FROM ".PREFIXE_TABLES."pagessections as a WHERE statut = '2' ORDER BY specifiquesection, page";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
        <select name="page" size="8" onchange="getElement('lien_menu').value = this.value;">
          <?php
			while ($ligne = mysql_fetch_assoc($res))
			{
				// suppression du g pour les pages générales du portail
				$nivcible = $sections[$ligne['specifiquesection']]['site_section'];
				// suppression de l'indication x_ devant la page d'accueil d'une section
				$nivcible_url = ($ligne['page'] == 'index'.$nivcible) ? '' : $nivcible;
				
				$separateur = (!empty($nivcible) and $ligne['page'] != 'index'.$nivcible and $site['url_rewriting_actif'] == 1) ? '_' : '';
								
				$nivcible_sans_url_rewriting = (!empty($nivcible)) ? 'niv='.$nivcible.'&amp;' : '';
				$lien_propose = ($site['url_rewriting_actif'] == 1) ? $nivcible_url.$separateur.$ligne['page'].'.htm' : 'index.php?'.$nivcible_sans_url_rewriting.'page='.$ligne['page'];
				echo '<option value="'.$lien_propose.'">'.$lien_propose.'</option>';
			}
?>
        </select>
        <?php
		}
		else
		{
			echo '<span class="rmq">Aucune pour l\'instant</span>';
		}
	}
?>
      </td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Position du lien</td>
      <td valign="top"><select name="position_menu" id="position_menu">
          <?php
	for ($i = 1; $i <= 20; $i++)
	{
		$c = ($menu_a_modifier['position_menu'] == $i) ? ' selected' : '';
		echo '<option value="'.$i.'"'.$c.'>'.$i.'</option>';
	}
?>
        </select></td>
    </tr>
    <tr>
      <td valign="top">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Intitul&eacute; du lien*</td>
      <td><input name="texte_menu" type="text" id="texte_menu" size="20" maxlength="255" value="<?php echo stripslashes($menu_a_modifier['texte_menu']); ?>" /></td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Description du lien</td>
      <td><input name="description_menu" type="text" id="description_menu" size="40" maxlength="255" value="<?php echo stripslashes($menu_a_modifier['description_menu']); ?>" />
      </td>
    </tr>
    <tr class="td-gris">
      <td valign="top">Adresse du lien*</td>
      <td><input name="lien_menu" type="text" id="lien_menu" size="40" maxlength="255" value="<?php echo stripslashes($menu_a_modifier['lien_menu']); ?>" /></td>
    </tr>
  </table>
  <p align="center">
    <input type="submit" name="Submit" value="Modifier ce lien" />
  </p>
</form>
<div class="instructions">
  <p>Les modifications que tu apportes &agrave; ce lien sont directement r&eacute;percut&eacute;es sur les menus du portail.</p>
</div>
<?php
		
		}
		else
		{
?>
<h1>Gestion des menus du portail</h1>
<p align="center"><a href="index.php?page=gestion_menus">Retour &agrave; la page Gestion des Menus du portail</a></p>
<form action="index.php" method="get" name="form" id="form" onsubmit="return menuchoisi(this)" class="form_config_site">
  <h2>Modifier un lien</h2>
  <script language="JavaScript" type="text/JavaScript">
<!--
function menuchoisi(form)
{
	if (form.id_menu.value != "") 
	{
		return true; 
	}
	else 
	{
		alert ("N'oublie pas de choisir un des liens.");
		return false;
	}
}
//-->
</script>
  <input type="hidden" name="page" value="gestion_menus" />
  <input type="hidden" name="do" value="modifiermenu" />
  <input type="hidden" name="step" value="2" />
  <p align="center">S&eacute;lectionne le lien &agrave; modifier :
    <select name="id_menu">
      <option value="" selected="selected"></option>
      <?php
			foreach ($site_menus as $ligne)
			{ // affichage des menus existants pour en sélectionner un à modifier
?>
      <option value="<?php echo $ligne['id_menu']; ?>"><?php echo $sections[$ligne['section_menu']]['nomsectionpt'].' - '.$ligne['texte_menu']; ?></option>
      <?php
			}
?>
    </select>
  </p>
  <p align="center">
    <input type="submit" value="Modifier ce lien" />
  </p>
</form>
<?php
		}
	}
	if ($site['url_rewriting_actif'] == 1)
	{
?>
<div class="instructions">
  <p align="center">
    <input type="button" tabindex="9" onclick="if(getElement('txt_instr').style.display == 'none') {getElement('txt_instr').style.display = 'block'; this.value = 'Masquer les conseils';} else {getElement('txt_instr').style.display = 'none'; this.value = 'Afficher les conseils';}" value="Afficher les conseils" />
  </p>
  <div id="txt_instr" style="display:none;">
    <h2>Quelques adresses pr&eacute;d&eacute;finies</h2>
    <p>Certaines pages dynamiques du portail sont d&eacute;j&agrave; pr&eacute;sentes sur le portail, voici une liste non exhaustive de leur adresse :</p>
    <ul>
      <li><code>index.htm</code> - la page d'accueil du portail.</li>
      <li><code>index<span class="rmq">x</span>.htm</code> - la page d'accueil de la SectionUnit&eacute; <span class="rmq">x</span> (o&ugrave; <span class="rmq">x</span> est l'indicatif web de la Section).<br />
        <em class="petitbleu">l'indicatif web </em>: c'est la lettre &agrave; c&ocirc;t&eacute; du nom de la section/unit&eacute; ci-dessus. </li>
      <li><code>contact.htm</code> - les adresses des animateurs responsables de toutes les sections et de l'unit&eacute;.</li>
      <li><code>staff.htm</code> - les adresses de tout l'effectif d'animateurs.</li>
      <li><code><span class="rmq">x</span>_staff.htm</code> - le staff de la Section <span class="rmq">x</span>.<br />
        <em class="petitbleu">note </em>: Pour une Unit&eacute;, &ccedil;a affiche la liste des sections de l'Unit&eacute;.</li>
      <li><code>forum.htm</code> - je crois que c'est clair.</li>
      <li><code>livreor.htm</code> - l&agrave; aussi.</li>
      <li><code>galerie.htm</code> - la galerie photos.</li>
      <li><code><span class="rmq">x</span>_galerie.htm</code> - l'album photos de la Section/Unit&eacute; <span class="rmq">x</span>.</li>
      <li><code><span class="rmq">x</span>_galerie_<span class="rmqbleu">y</span>.htm</code> - l'album photos num&eacute;ro <span class="rmqbleu">y</span> de la Section/Unit&eacute; <span class="rmq">x</span>.</li>
      <li><code>tally<span class="rmq">x</span>.htm</code> - l'article <span class="rmq">x</span> du tally.</li>
      <li><code>tally_<span class="rmq">x</span>.htm</code> - la page <span class="rmq">x</span> de la liste des articles du tally.</li>
      <li><code>news.htm</code> - les news de l'Unit&eacute;.</li>
      <li><code>news_<span class="rmq">x</span>.htm</code> - la page <span class="rmq">x</span> des news de l'Unit&eacute;.</li>
      <li><code>listembsite.htm</code> - la liste des membres du portail.</li>
      <li><code>membre<span class="rmq">x</span>.htm</code> - le profil du membre num&eacute;ro <span class="rmq">x</span>.</li>
      <li><code>annif.htm</code> - la liste des anniversaires r&eacute;cents parmi les membres de l'Unit&eacute;.</li>
      <li><code>fichiers.htm</code> - la page de t&eacute;l&eacute;chargements du portail.</li>
      <li><code>pagesectionmaj.htm</code> - les pages du portail mises &agrave; jour r&eacute;cemment.</li>
      <li>...</li>
    </ul>
    <p>Les pages cr&eacute;&eacute;es par les membres du portail sont accessibles comme suit :</p>
    <ul>
      <li><code><span class="rmq">x</span>_<span class="rmqbleu">y</span>.htm</code> - la page <span class="rmqbleu">y</span> de la Section/Unit&eacute; <span class="rmq">x</span> (u_programme2005.htm, f_camparlon.htm, ... par exemple).</li>
      <li><code><span class="rmq">x</span>.htm</code> - la page <span class="rmq">x</span> au niveau g&eacute;n&eacute;ral du portail.</li>
    </ul>
  </div>
</div>
<?php
	}
	else
	{ // l'url rewriting n'est pas actif sur le portail
?>
<div class="instructions">
  <p align="center">
    <input type="button" tabindex="9" onclick="if(getElement('txt_instr').style.display == 'none') {getElement('txt_instr').style.display = 'block'; this.value = 'Masquer les conseils';} else {getElement('txt_instr').style.display = 'none'; this.value = 'Afficher les conseils';}" value="Afficher les conseils" />
  </p>
  <div id="txt_instr" style="display:none;">
    <h2>Quelques adresses pr&eacute;d&eacute;finies</h2>
    <p>Certaines pages dynamiques du portail sont d&eacute;j&agrave; pr&eacute;sentes sur le portail, voici une liste non exhaustive de leur adresse :</p>
    <ul>
      <li><code>index.php</code> - la page d'accueil du portail.</li>
      <li><code>index.php?niv=<span class="rmq">x</span>.htm</code> - la page d'accueil de la SectionUnit&eacute; <span class="rmq">x</span> (o&ugrave; <span class="rmq">x</span> est l'indicatif web de la Section).<br />
        <em class="petitbleu">l'indicatif web </em>: c'est la lettre &agrave; c&ocirc;t&eacute; du nom de la section/unit&eacute; ci-dessus. </li>
      <li><code>index.php?page=staff&amp;qui=resp</code> - les adresses des animateurs responsables de toutes les sections et de l'unit&eacute;.</li>
      <li><code>staff.htm</code> - les adresses de tout l'effectif d'animateurs.</li>
      <li><code>index.php?niv=<span class="rmq">x</span>&amp;page=staff</code> - le staff de la Section <span class="rmq">x</span>.<br />
        <em class="petitbleu">note </em>: Pour une Unit&eacute;, &ccedil;a affiche la liste des sections de l'Unit&eacute;.</li>
      <li><code>index.php?page=livreor</code> - le livre d'or.</li>
      <li><code>index.php?page=galerie</code> - la galerie photos.</li>
      <li><code>index.php?niv=<span class="rmq">x</span>&amp;page=galerie</code> - l'album photos de la Section/Unit&eacute; <span class="rmq">x</span>.</li>
      <li><code>index.php?niv=<span class="rmq">x</span>&amp;page=galerie&amp;show=<span class="rmqbleu">y</span></code> - l'album photos num&eacute;ro <span class="rmqbleu">y</span> de la Section/Unit&eacute; <span class="rmq">x</span>.</li>
      <li><code>index.php?page=tally&amp;numero=<span class="rmq">x</span></code> - l'article <span class="rmq">x</span> du tally.</li>
      <li><code>index.php?page=tally&amp;pg=<span class="rmq">x</span></code> - la page <span class="rmq">x</span> de la liste des articles du tally.</li>
      <li><code>index.php?page=news</code> - les news de l'Unit&eacute;.</li>
      <li><code>index.php?page=news&amp;pg=<span class="rmq">x</span></code> - la page <span class="rmq">x</span> des news de l'Unit&eacute;.</li>
      <li><code>index.php?page=listembsite</code> - la liste des membres du portail.</li>
      <li><code>index.php?profil_user&amp;user=<span class="rmq">x</span></code> - le profil du membre num&eacute;ro <span class="rmq">x</span>.</li>
      <li><code>index.php?page=annif</code> - la liste des anniversaires r&eacute;cents parmi les membres de l'Unit&eacute;.</li>
      <li><code>index.php?page=fichiers</code> - la page de t&eacute;l&eacute;chargements du portail.</li>
      <li><code>index.php?page=pagesectionmaj</code> - les pages du portail mises &agrave; jour r&eacute;cemment.</li>
      <li>...</li>
    </ul>
    <p>Les pages cr&eacute;&eacute;es par les membres du portail sont accessibles comme suit :</p>
    <ul>
      <li><code>index.php?niv=<span class="rmq">x</span>&amp;page=<span class="rmqbleu">y</span></code> - la page <span class="rmqbleu">y</span> de la Section/Unit&eacute; <span class="rmq">x</span> (index.php?niv=u&amp;page=programme2005 ... par exemple).</li>
      <li><code>index.php?page=<span class="rmq">x</span></code> - la page <span class="rmq">x</span> au niveau g&eacute;n&eacute;ral du portail.</li>
    </ul>
  </div>
</div>
<?php
	}
} // fin du else (numniveau <= 2)
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
