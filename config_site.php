<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* config_site.php v 1.1.1 - Gestion de la configuration générale du portail
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
*	Répartition de la configuration en plusieurs catégories
*	Paramétrage de nouvelles options : taille en upload, dimensions maximales,
*		dimensions photos et miniatures, avatars, ...
* 	Bug : protection des champs contre une insertion dangereuse
*	Ajout option déployer menu section et unité par défaut
*	Correction bug nbre_dernieres_news (fil 119)
* Modifications v 1.1.1
*	Ajout d'une option pour réécrire le fichier .htaccess (sans désactiver/réactiver l'url-rewriting)
*/

include_once('connex.php');
include_once('fonc.php');
if ($user['niveau']['numniveau'] < 5)
{
	include('404.php');
	exit;
}
if ($_POST['do'] != 'savemodif')
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Infos du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<h1>Configuration du portail </h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; l'accueil Membres</a> - 
<a href="index.php?page=config_site">Retour &agrave; la page de configuration</a></p>
<?php
	if ($_GET['msg'] == 'ok')
	{
?>
<div class="msg">
<p align="center" class="rmqbleu">Configuration mise &agrave; jour</p>
</div>
<?php
	}
	else if (empty($_GET['categorie']))
	{
?>
<div class="form_config_site">
  <h2>Choix d'une cat&eacute;gorie</h2>
  <p>Choisis ci-dessous l'une des cat&eacute;gories de configuration du portail.</p>
  <ul>
    <li><a href="index.php?page=config_site&amp;categorie=general">Param&egrave;tres 
      techniques du portail</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=menus">Param&egrave;tres 
      des menus du portail</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=galerie">Param&egrave;tres 
      des albums photos</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=forum">Param&egrave;tres 
      du forum</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=fichiers">Upload de 
      fichiers</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=webmaster">Donn&eacute;es 
      du webmaster</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=groupe">Donn&eacute;es 
      du groupe scout sur le portail</a></li>
    <li><a href="index.php?page=config_site&amp;categorie=messages">Messages d'accueil</a></li>
    <li><a href="index.php?page=config_site&categorie=divers">R&eacute;glages 
      divers</a></li>
    <li><a href="index.php?page=config_site&categorie=droits_utilisateurs">Droits 
      des utilisateurs</a></li>
  </ul>
</div>
<?php
	}
	else if (!empty($_GET['categorie']))
	{
?>
<script type="text/javascript">
<!--
function check_valeur(element, valeur_defaut)
{
	if (isNaN(getElement(element).value) || getElement(element).value < 1)
	{
		alert("Désolé, ce champ doit avoir une valeur numérique plus grande que 0.");
		getElement(element).value = valeur_defaut;
	}
}
//-->
</script>
<form action="config_site.php" method="post" name="form_config_site" class="form_config_site">
    <input type="hidden" name="categorie" value="<?php echo $_GET['categorie']; ?>" /> 
    <input type="hidden" name="do" value="savemodif" />
    <input type="hidden" name="champ" value="maj" />
<?php
	}
	if ($_GET['categorie'] == 'general')
	{
?>
<h2>Param&egrave;tres techniques du portail</h2>
  <h3>Mise &agrave; jour du portail </h3>
  <p> Date de la mise &agrave; jour : 
    <input name="maj" type="text" id="maj" value="<?php echo htmlspecialchars($site['maj'], ENT_QUOTES);?>" size="40" />
    (format au choix)</p>
  <p>Afficher la version du portail install&eacute;e : 
    <input type="radio" name="show_version" value="1" id="show_version_on"<?php if ($site['show_version'] == 1) {echo ' checked="checked"';} ?> />
    <label for="show_version_on">oui</label>
    <input type="radio" name="show_version" value="0" id="show_version_off"<?php if ($site['show_version'] == 0) {echo ' checked="checked"';} ?> />
    <label for="show_version_off">non</label> 
    (version actuelle : <?php echo $site['version_portail']; ?>) </p>
  <h3>Etat du portail :</h3>
  <p>Quand le portail ou un module est hors ligne ou inactif, le webmaster peut 
    continuer &agrave; l'utiliser &agrave; des fins de tests ou pour des raisons 
    de s&eacute;curit&eacute;. Il est notamment utile de mettre le portail hors 
    ligne pour effectuer une mise &agrave; jour des fichiers du portail.</p>
  <p><input type="radio" name="site_actif" value="1" id="site_actif_on"<?php if ($site['site_actif'] == 1) {echo ' checked="checked"';} ?> />
    <label for="site_actif_on">En ligne</label>
    <input type="radio" name="site_actif" value="0" id="site_actif_off"<?php if ($site['site_actif'] == 0) {echo ' checked="checked"';} ?> />
    <label for="site_actif_off">Hors ligne</label>
    <br />
    <span class="petitbleu">Par mesure de s&eacute;curit&eacute;, le webmaster
     ne peut pas se d&eacute;connecter si le portail est mis hors ligne. Si la
     connexion du webmaster est perdue, un script lui proposera de r&eacute;activer
     le portail. </span></p>
  <h3>URL du portail</h3>
  <p>Url du portail : 
  <input name="adressesite" type="text" id="adressesite" value="<?php echo htmlspecialchars($site['adressesite'], ENT_QUOTES);?>" size="40" />
  </p>
  <h3>Modules du portail :</h3>
  <p class="petitbleu">Si tu d&eacute;sactives un module, la page <a href="index.php?page=module_desactive">module_desactive.php</a> 
    s'affiche pour les <acronym title="Le niveau d'accès 5 est celui du webmaster">utilisateurs
    de niveau d'acc&egrave;s &lt; 5</acronym></p>
  <p><input type="radio" name="galerie_actif" value="1" id="radio3"<?php if ($site['galerie_actif'] == 1) {echo ' checked="checked"';} ?> />
        <label for="radio3"><img src="templates/default/images/ok.png" width="12" height="12" alt="actif" /></label>
    <input type="radio" name="galerie_actif" value="0" id="radio4"<?php if ($site['galerie_actif'] == 0) {echo ' checked="checked"';} ?> />
        <label for="radio4"><img src="templates/default/images/non.png" alt="inactif" width="12" height="12" /></label> 
        Albums photos </p>
  <p><input type="radio" name="forum_actif" value="1" id="radio7"<?php if ($site['forum_actif'] == 1) {echo ' checked="checked"';} ?> />
        <label for="radio7"><img src="templates/default/images/ok.png" width="12" height="12" alt="actif" /></label>
    <input type="radio" name="forum_actif" value="0" id="radio8"<?php if ($site['forum_actif'] == 0) {echo ' checked="checked"';} ?> />
        <label for="radio8"><img src="templates/default/images/non.png" width="12" height="12" alt="inactif" /></label> Forum</p>
  <p>
    <input type="radio" name="gestion_membres_actif" value="1" id="gmembres1"<?php if ($site['gestion_membres_actif'] == 1) {echo ' checked="checked"';} ?> />
    <label for="gmembres1"><img src="templates/default/images/ok.png" width="12" height="12" alt="actif" /></label>
    <input type="radio" name="gestion_membres_actif" value="0" id="gmembres0"<?php if ($site['gestion_membres_actif'] == 0) {echo ' checked="checked"';} ?> />
    <label for="gmembres0"><img src="templates/default/images/non.png" width="12" height="12" alt="inactif" /></label>
    Gestion de l'unit&eacute;</p>
  <h3>Log des visites</h3>
  <p>Le log des visites enregistre les pages vues par toutes les personnes qui 
    ont visit&eacute; le portail durant les 20 derniers jours. Il permet un suivi 
    des utilisateurs utile &agrave; divers usages : d&eacute;bugging, tra&ccedil;age 
    du passage d'un vandale, aide d'un utilisateur perdu. </p>
  <p> 
    <input type="radio" name="log_visites" value="1" id="log_visites_on"<?php if ($site['log_visites'] == 1) {echo ' checked="checked"';} ?> />
    <label for="log_visites_on"><img src="templates/default/images/ok.png" width="12" height="12" alt="actif" /></label>
    <input type="radio" name="log_visites" value="0" id="log_visites_off" onclick="getElement('show_enligne_off').checked = true;"<?php if ($site['log_visites'] == 0) {echo ' checked="checked"';} ?> />
    <label for="log_visites_off"><img src="templates/default/images/non.png" width="12" height="12" alt="inactif" /></label>
  Log des visites</p>
  <p> 
    <input type="radio" name="show_enligne" value="1" id="show_enligne_on" onclick="getElement('log_visites_on').checked = true;"<?php if ($site['show_enligne'] == 1) {echo ' checked="checked"';} ?> />
    <label for="show_enligne_on"><img src="templates/default/images/ok.png" width="12" height="12" alt="actif" /></label>
    <input type="radio" name="show_enligne" value="0" id="show_enligne_off"<?php if ($site['show_enligne'] == 0) {echo ' checked="checked"';} ?> />
    <label for="show_enligne_off"><img src="templates/default/images/non.png" width="12" height="12" alt="inactif" /></label>
    Afficher les visiteurs et les membres en ligne (le log des visites DOIT &ecirc;tre 
    activ&eacute;) </p>
  <h3>Avertissement lien externe :</h3>
  <p> Afficher un avertissement aux visiteurs qui suivent un lien vers un site 
    externe : 
    <input type="radio" name="avert_onclick_lien_externe" value="1" id="avert_onclick_lien_externe_on"<?php if ($site['avert_onclick_lien_externe'] == 1) {echo ' checked="checked"';}?> />
    <label for="avert_onclick_lien_externe_on">Oui</label>
    <input type="radio" name="avert_onclick_lien_externe" value="0" id="avert_onclick_lien_externe_off"<?php if ($site['avert_onclick_lien_externe'] == 0) {echo ' checked="checked"';} ?> />
    <label for="avert_onclick_lien_externe_off">Non</label>
  </p>
  <p class="petitbleu">Le texte de cet avertissement se trouve dans le fichier fonc.js.</p>
  <h3>Url-rewriting </h3>
<script type="text/javascript">
<!--
function check_renew()
{
	if (getElement('url_rew_off').checked && getElement('renew_htaccess').checked)
	{
		alert("Si l'url-rewriting est désactivé, le fichier .htaccess ne sera pas réécrit");
		getElement(<?php echo ($site['url_rewriting_actif'] == 1) ? "'url_rew_on'" : "'url_rew_off'"; ?>).checked = true;
		getElement('renew_htaccess').checked = false;
	}
}
//-->
</script>
  <p>
    <input type="radio" name="url_rewriting_actif" value="1" id="url_rew_on"<?php if ($site['url_rewriting_actif'] == 1) {echo ' checked="checked"';}?> onclick="check_renew()" />
    <label for="url_rew_on">Actif</label>
    <input type="radio" name="url_rewriting_actif" value="0" id="url_rew_off"<?php if ($site['url_rewriting_actif'] == 0) {echo ' checked="checked"';} ?> onclick="check_renew()" />
    <label for="url_rew_off">Inactif</label>
    <br />
    <span class="petitbleu">En (d&eacute;s)activant l'url-rewriting, le fichier 
    .htaccess est enti&egrave;rement r&eacute;&eacute;crit ou supprim&eacute;. 
    <br />
    Son contenu pr&eacute;c&eacute;dent est donc &eacute;limin&eacute;. A toi 
    de veiller &eacute;ventuellement &agrave; faire une sauvegarde des modifications 
    que tu y aurais apport&eacute;es.</span></p>
  <p class="petitbleu">Le <strong>mod&egrave;le du fichier .htaccess</strong> pour SWP 
    se trouve dans le dossier prv/ et porte le nom htaccess (sans le point au 
    d&eacute;but).</p>
  <p class="petitbleu">Si tu <strong>désactives l'url-rewriting</strong>, les liens cr&eacute;&eacute;s manuellement au format <code>x_page.htm</code> (dans le menu g&eacute;n&eacute;ral et les pages du site entre autres) deviendront inaccessibles. A toi de les corriger au format <code>index.php?niv=x&amp;page=page</code>. Les liens g&eacute;n&eacute;r&eacute;s dynamiquement par le portail sont adapt&eacute;s automatiquement &agrave; la nouvelle configuration. </p>
  <p>
    <input type="checkbox" name="renew_htaccess" value="1" id="renew_htaccess" onclick="check_renew()" />
    <label for="renew_htaccess">Réécrire le fichier .htaccess</label>
  </p>
  <h3>Fonction mail() native de php</h3>
<?php 
	if (!function_exists('mail'))
	{ 
?>
<p class="rmq">La fonction mail n'est pas activ&eacute;e sur le serveur.</p>
<?php
	}
?>
  <p><input type="radio" name="envoi_mails_actif" value="1" id="envoi_mails_on"<?php if ($site['envoi_mails_actif'] == 1) {echo ' checked="checked"';}?> />
    <label for="envoi_mails_on">Active</label>
    <input type="radio" name="envoi_mails_actif" value="0" id="envoi_mails_off"<?php if ($site['envoi_mails_actif'] == 0) {echo ' checked="checked"';} ?> />
    <label for="envoi_mails_off">Inactive</label>
    <br />
    <span class="petitbleu">Chez de nombreux h&eacute;bergeurs gratuits, la fonction 
    est d&eacute;sactiv&eacute;e ou modifi&eacute;e.</span></p>
  <h3>Balises Meta du portail :</h3>
  <p>Elles sont utiles pour les moteurs de recherche mais pas obligatoires</p>
    <textarea name="balises_meta" rows="6" cols="70" class="sys"><?php echo htmlspecialchars($site['balises_meta'], ENT_QUOTES);?></textarea>
  <p>Tu peux placer dans cette case toutes les balises qui doivent prendre place 
    dans l'en-t&ecirc;te &lt;head&gt; des pages du portail.</p>
<?php
	}
	else if ($_GET['categorie'] == 'menus')
	{
?>
  <h2>Param&egrave;tres des menus du portail</h2>
  <p>Lorsque le portail ne contient qu'une ou deux sections, il est plus int&eacute;ressant 
    de toujours d&eacute;ployer le menu de la section pour toujours en afficher 
    son contenu.</p>
<p>Mod&egrave;le de menu : 
  <select name="modele_menu" id="modele_menu">
    <option value="complet"<?php if ($site['modele_menu'] == 'complet') {echo ' selected="selected"';}?>>Menu complet par unit&eacute; (par d&eacute;faut)</option>
    <option value="complet_melange"<?php if ($site['modele_menu'] == 'complet_melange') {echo ' selected="selected"';}?>>Menu complet  m&eacute;lang&eacute;</option>
    <!--<option value="progressif"<?php if ($site['modele_menu'] == 'progressif') {echo ' selected="selected"';}?>>Menu progressif</option>-->
  </select>
</p>
<fieldset>
  <legend>Param&egrave;tres des Menus complets</legend>
  <p class="petitbleu">Param&egrave;tres pour le menu complet par unit&eacute; ou m&eacute;lang&eacute;.</p>
  <p>Menu unit&eacute; toujours d&eacute;ploy&eacute; :
    <input type="radio" name="deploy_menu_unite" value="1" id="deploy_menu_unite_on"<?php if ($site['deploy_menu_unite'] == 1) {echo ' checked="checked"';}?> />
    <label for="deploy_menu_unite_on">Oui</label>
    <input type="radio" name="deploy_menu_unite" value="0" id="deploy_menu_unite_off"<?php if ($site['deploy_menu_unite'] == 0) {echo ' checked="checked"';} ?> />
    <label for="deploy_menu_unite_off">Non</label> 
    <span class="petitbleu">(uniquement menu complet par unit&eacute;)</span></p>
  <p>Menus des sections toujours d&eacute;ploy&eacute;s : 
    <input type="radio" name="deploy_menu_section" value="1" id="deploy_menu_section_on"<?php if ($site['deploy_menu_section'] == 1) {echo ' checked="checked"';}?> />
    <label for="deploy_menu_section_on">Oui</label>
    <input type="radio" name="deploy_menu_section" value="0" id="deploy_menu_section_off"<?php if ($site['deploy_menu_section'] == 0) {echo ' checked="checked"';} ?> />
    <label for="deploy_menu_section_off">Non</label>
</p>
  <p>Indiquer &agrave; l'utilisateur que le menu d'une section/unit&eacute; est vide :
    <input type="radio" name="show_menu_vide" value="1" id="show_menu_vide_on"<?php if ($site['show_menu_vide'] == 1) {echo ' checked="checked"';}?> />
    <label for="show_menu_vide_on">Oui</label>
    <input type="radio" name="show_menu_vide" value="0" id="show_menu_vide_off"<?php if ($site['show_menu_vide'] == 0) {echo ' checked="checked"';} ?> />
    <label for="show_menu_vide_off">Non</label>
  </p>
</fieldset>
  <p class="petitbleu"> Les &eacute;l&eacute;ments du menu peuvent &ecirc;tre 
    param&eacute;tr&eacute;s depuis la <a href="index.php?page=gestion_menus">Gestion 
    des menus</a>.</p>
<?php
	}
	else if ($_GET['categorie'] == 'galerie')
	{
?>
  <h2>Param&egrave;tres des albums photos </h2>
  <h3>Liste des albums </h3>
  <p>Afficher par d&eacute;faut la description des
    <input name="galerie_show_nb" type="text" id="galerie_show_nb" onchange="check_valeur('galerie_show_nb', <?php echo htmlspecialchars($site['galerie_show_nb'], ENT_QUOTES); ?>);" value="<?php echo htmlspecialchars($site['galerie_show_nb'], ENT_QUOTES); ?>" size="5" />
    derniers albums <br />
    ou des albums cr&eacute;&eacute;s depuis moins de
    <select name="galerie_show_delai" id="galerie_show_delai">
      <option value="7 DAY"<?php echo ($site['galerie_show_delai'] == '7 DAY') ? ' selected="selected"' : ''; ?>>1 semaine</option>
      <option value="15 DAY"<?php echo ($site['galerie_show_delai'] == '15 DAY') ? ' selected="selected"' : ''; ?>>15 jours</option>
      <option value="1 MONTH"<?php echo ($site['galerie_show_delai'] == '1 MONTH') ? ' selected="selected"' : ''; ?>>1 mois</option>
      <option value="45 DAY"<?php echo ($site['galerie_show_delai'] == '45 DAY') ? ' selected="selected"' : ''; ?>>1 mois et demi</option>
      <option value="2 MONTH"<?php echo ($site['galerie_show_delai'] == '2 MONTH') ? ' selected="selected"' : ''; ?>>2 mois</option>
      <option value="3 MONTH"<?php echo ($site['galerie_show_delai'] == '3 MONTH') ? ' selected="selected"' : ''; ?>>3 mois</option>
      <option value="4 MONTH"<?php echo ($site['galerie_show_delai'] == '4 MONTH') ? ' selected="selected"' : ''; ?>>4 mois</option>
      <option value="5 MONTH"<?php echo ($site['galerie_show_delai'] == '5 MONTH') ? ' selected="selected"' : ''; ?>>5 mois</option>
      <option value="6 MONTH"<?php echo ($site['galerie_show_delai'] == '6 MONTH') ? ' selected="selected"' : ''; ?>>6 mois</option>
    </select>
  </p>
  <h3>Liste des photos</h3>
  <p>Afficher 
    <input name="galerie_nb_par_page" type="text" id="galerie_nb_par_page" value="<?php echo htmlspecialchars($site['galerie_nb_par_page'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('galerie_nb_par_page', <?php echo htmlspecialchars($site['galerie_nb_par_page'], ENT_QUOTES); ?>);" />
  miniatures par page de l'album.  </p>
  <h3>Photos des albums </h3>
  <p>Dimension maximale des photos grand format (en pixels) : 
    <input name="galerie_proportions_photo" type="text" id="galerie_proportions_photo" value="<?php echo htmlspecialchars($site['galerie_proportions_photo'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('galerie_proportions_photo', <?php echo htmlspecialchars($site['galerie_proportions_photo'], ENT_QUOTES); ?>);" />
  </p>
  <p>Dimension maximale des miniatures (en pixels) : 
    <input name="galerie_proportions_mini" type="text" id="galerie_proportions_mini" value="<?php echo htmlspecialchars($site['galerie_proportions_mini'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('galerie_proportions_mini', <?php echo htmlspecialchars($site['galerie_proportions_mini'], ENT_QUOTES); ?>);" />
  </p>
  <?php
	}
	else if ($_GET['categorie'] == 'fichiers')
	{
		function return_bytes($val) 
		{ // renvoie la taille en octets à partir d'une taille au format 8M
		  // Merci http://be2.php.net/manual/fr/function.ini-get.php
			$val = trim($val);
			$last = strtolower($val{strlen($val)-1});
			switch($last) 
			{ // Le modifieur 'G' est disponible depuis PHP 5.1.0
				case 'g':
				  $val *= 1024;
				case 'm':
				  $val *= 1024;
				case 'k':
				  $val *= 1024;
			}
			return $val;
		}
		$upload_max_filesize = @return_bytes(@ini_get('upload_max_filesize'));
?>
  <h2>Upload de fichiers</h2>
  <?php
		if (function_exists('ini_get'))
		{
  			echo (ini_get('file_uploads')) ? '' : '<p class="rmq" align="center">Il semblerait que l\'upload de fichiers soit d&eacute;sactiv&eacute; !</p>';
  ?>
  <p>La <acronym title="param&egrave;tre upload_max_filesize dans php.ini">taille
      maximale en upload</acronym> sur le serveur est de <?php echo $upload_max_filesize; ?> 
  octets (soit <?php echo taille_fichier($upload_max_filesize); ?>).</p>
<?php
		}
?>
  <h3>Taille maximale en upload</h3>
  <p> Taille maximale : 
    <input name="upload_max_filesize" type="text" id="upload_max_filesize" value="<?php echo htmlspecialchars($site['upload_max_filesize'], ENT_QUOTES); ?>" size="15" onchange="check_valeur('upload_max_filesize', <?php echo htmlspecialchars($site['upload_max_filesize'], ENT_QUOTES); ?>); getElement('info_upload_max_filesize').innerHTML = '(soit ' + taille_fichier(this.value) + ')';" />
    octets <span id="info_upload_max_filesize"><?php echo (is_numeric($site['upload_max_filesize'])) ? '(soit '.taille_fichier($site['upload_max_filesize']).')' : ''; ?></span>
  <?php
		if (function_exists('ini_get'))
		{
  ?>
	<input type="button" value="&lt;&lt; <?php echo taille_fichier($upload_max_filesize); ?>" onclick="getElement('upload_max_filesize').value = '<?php echo $upload_max_filesize; ?>'; getElement('info_upload_max_filesize').innerHTML = '(soit ' + taille_fichier('<?php echo $upload_max_filesize; ?>') + ')';" />
<?php
		}
?>
	<br />
    <span class="petitbleu">Ce param&egrave;tre est utilis&eacute; pour l'upload
     des photos (galerie photos et photos des membres
    de  l'unit&eacute;). Elles sont ensuite redimensionn&eacute;es pour prendre moins
    de place. </span></p>
  <h3>Page de t&eacute;l&eacute;chargement de fichiers</h3>
  <p>Taille maximale des fichiers : 
    <input name="download_max_filesize" type="text" id="download_max_filesize" value="<?php echo htmlspecialchars($site['download_max_filesize'], ENT_QUOTES); ?>" size="15" onchange="check_valeur('download_max_filesize', <?php echo htmlspecialchars($site['download_max_filesize'], ENT_QUOTES); ?>); getElement('info_download_max_filesize').innerHTML = '(soit ' + taille_fichier(this.value) + ')';" />
    octets <span id="info_download_max_filesize"><?php echo (is_numeric($site['download_max_filesize'])) ? '(soit '.taille_fichier($site['download_max_filesize']).')' : ''; ?></span>
  <?php
		if (function_exists('ini_get'))
		{
  ?>
	<input type="button" value="&lt;&lt; <?php echo taille_fichier($upload_max_filesize); ?>" onclick="getElement('download_max_filesize').value = '<?php echo $upload_max_filesize; ?>';  getElement('info_download_max_filesize').innerHTML = '(soit ' + taille_fichier('<?php echo $upload_max_filesize; ?>') + ')';" />
<?php
		}
?>
	<br />
    <span class="petitbleu">Les fichiers upload&eacute;s dans la page de t&eacute;l&eacute;chargements
    ne pourront d&eacute;passer cette taille. </span></p>
  <?php
	}
	else if ($_GET['categorie'] == 'webmaster')
	{
?>
  <h2>Donn&eacute;es du webmaster</h2>
  <p>Son pseudo : 
    <input name="webmaster" type="text" id="webmaster" value="<?php echo htmlspecialchars($site['webmaster'], ENT_QUOTES); ?>" size="30" />
  </p>
  <p>Son email : 
    <input name="mailwebmaster" type="text" id="mailwebmaster" value="<?php echo htmlspecialchars($site['mailwebmaster'], ENT_QUOTES);?>" size="40" />
  </p>
  <?php
	}
	else if ($_GET['categorie'] == 'groupe')
	{
?>
  <h2>Donn&eacute;es du groupe scout</h2>
  <p><span class="rmqbleu">Titre du portail : </span> 
    <input name="titre_site" type="text" id="titre_site" value="<?php echo htmlspecialchars($site['titre_site'], ENT_QUOTES);?>" size="40" />
    <br />
    <span class="petitbleu">Utilis&eacute; dans la balise &lt;title&gt; dans l'en-t&ecirc;te 
    des pages du portail</span></p>
  <p><span class="rmqbleu">Nom du groupe scout : </span> 
    <input name="nom_unite" type="text" id="nom_unite" value="<?php echo htmlspecialchars($site['nom_unite'], ENT_QUOTES);?>" size="40" />
    <br />
    <span class="petitbleu">Utilis&eacute; dans les emails envoy&eacute;s aux 
    membres</span></p>
  <p><span class="rmqbleu">Ville de l'Unit&eacute; :</span> 
    <input name="site_ville" type="text" id="site_ville" value="<?php echo htmlspecialchars($site['site_ville'], ENT_QUOTES);?>" size="25" />
    <span class="rmqbleu">Code postal :</span> 
    <input name="site_code_postal" type="text" id="site_code_postal" value="<?php echo htmlspecialchars($site['site_code_postal'], ENT_QUOTES);?>" size="6" />
    <br />
    <span class="petitbleu">Ces donn&eacute;es sont utilis&eacute;es dans la gestion 
    de l'Unit&eacute; pour indiquer un nom de ville et un code postal par d&eacute;faut</span></p>
<?php
	}
	else if ($_GET['categorie'] == 'messages')
	{
?>
  <h2>Messages d'accueil </h2>
  <p>Les messages d'accueil sont affich&eacute;s sur la page d'accueil membre 
    l'un &agrave; la suite de l'autre.</p>
  <h3>Message &agrave; tous les membres</h3>
   
  <p class="petitbleu">Texte au format html uniquement</p>
  <p align="center"> 
    <input type="radio" name="showmsgmembres" value="1" id="showmsgmembres_on"<?php if ($site['showmsgmembres'] == 1) {echo ' checked="checked"';}?> />
    <label for="showmsgmembres_on">Afficher</label>
    <input type="radio" name="showmsgmembres" value="0" id="showmsgmembres_off"<?php if ($site['showmsgmembres'] == 0) {echo ' checked="checked"';} ?> />
    <label for="showmsgmembres_off">Ne pas afficher</label>
  </p>
<?php 
		panneau_html('message_membres');
?>
  <div align="center">
    <textarea name="message_membres" id="message_membres" class="sys" cols="70" rows="10" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo htmlspecialchars($site['message_membres'], ENT_QUOTES); ?></textarea>
  </div>
  <h3>Message pour les animateurs</h3>
  <p class="petitbleu">Texte au format html uniquement</p>
  <p align="center"> 
    <input type="radio" name="showmsganimateurs" value="1" id="showmsganim_on"<?php if ($site['showmsganimateurs'] == 1) {echo ' checked="checked"';}?> />
    <label for="showmsganim_on">Afficher</label>
    <input type="radio" name="showmsganimateurs" value="0" id="showmsganim_off"<?php if ($site['showmsganimateurs'] == 0) {echo ' checked="checked"';} ?> />
    <label for="showmsganim_off">Ne pas afficher</label>
  </p>
  <?php 
		panneau_html('messageanimateurs');
?>
  <div align="center">
    <textarea name="messageanimateurs" id="messageanimateurs" class="sys" rows="10" cols="70" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo htmlspecialchars($site['messageanimateurs'], ENT_QUOTES);?></textarea>
  </div>
    <?php
	}
	else if ($_GET['categorie'] == 'divers')
	{
?>
  <h2>R&eacute;glages divers du portail</h2>
  <?php
		if (function_exists('ini_get'))
		{
  			echo (ini_get('file_uploads')) ? '' : '<p class="rmq" align="center">Il semblerait que l\'upload de fichiers soit d&eacute;sactiv&eacute; !</p>';
		}
  ?>
  <h3>Photos des membres de l'unit&eacute;</h3>
  <p>Dimensions des miniatures (en pixels) : 
    <input name="photo_membre_max_width" type="text" id="photo_membre_max_width" value="<?php echo htmlspecialchars($site['photo_membre_max_width'], ENT_QUOTES); ?>" title="largeur" size="5" onchange="check_valeur('photo_membre_max_width', <?php echo htmlspecialchars($site['photo_membre_max_width'], ENT_QUOTES); ?>);" />
    x 
    <input name="photo_membre_max_height" type="text" id="photo_membre_max_height" value="<?php echo htmlspecialchars($site['photo_membre_max_height'], ENT_QUOTES); ?>" title="hauteur" size="5" onchange="check_valeur('photo_membre_max_height', <?php echo htmlspecialchars($site['photo_membre_max_height'], ENT_QUOTES); ?>);" />
  </p>
  <h3>Avatars des membres</h3>
  <p>Taille maximale : 
    <input name="avatar_max_filesize" type="text" id="avatar_max_filesize" value="<?php echo htmlspecialchars($site['avatar_max_filesize'], ENT_QUOTES); ?>" size="15" title="1 ko = 1024 octets" onchange="check_valeur('avatar_max_filesize', <?php echo htmlspecialchars($site['avatar_max_filesize'], ENT_QUOTES); ?>); getElement('info_avatar_max_filesize').innerHTML = '(soit ' + taille_fichier(this.value) + ')';" />
    octets <span id="info_avatar_max_filesize"><?php echo (is_numeric($site['avatar_max_filesize'])) ? '(soit '.taille_fichier($site['avatar_max_filesize']).')' : ''; ?></span></p>
  <p>Dimensions maximales (en pixels) : 
    <input name="avatar_max_width" type="text" id="avatar_max_width" title="largeur" value="<?php echo htmlspecialchars($site['avatar_max_width'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('avatar_max_width', <?php echo htmlspecialchars($site['avatar_max_width'], ENT_QUOTES); ?>);" />
    x 
    <input name="avatar_max_height" type="text" id="avatar_max_height" title="hauteur" value="<?php echo htmlspecialchars($site['avatar_max_height'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('avatar_max_width', <?php echo htmlspecialchars($site['avatar_max_height'], ENT_QUOTES); ?>);" />
  </p>
  <h3>Autres param&egrave;tres</h3>
  <p>Nombre derni&egrave;res news : 
    <input name="nbre_dernieres_news" type="text" id="nbre_dernieres_news" value="<?php echo htmlspecialchars($site['nbre_dernieres_news'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('nbre_dernieres_news', <?php echo htmlspecialchars($site['nbre_dernieres_news'], ENT_QUOTES); ?>);" />
    <br />
    <span class="petitbleu">Elles sont affich&eacute;es sur la page d'accueil 
    du portail</span></p>
  <p>Record de visiteurs en ligne : 
    <input name="record_enligne" type="text" id="record_enligne" value="<?php echo htmlspecialchars($site['record_enligne'], ENT_QUOTES); ?>" size="6" onchange="check_valeur('record_enligne', <?php echo htmlspecialchars($site['record_enligne'], ENT_QUOTES); ?>);" />
    <br />
    <span class="petitbleu">Cette valeur est l&agrave; pour info. Elle n'&eacute;volue 
    que si le log des visites est activ&eacute;.</span></p>
  <p>Record de membres connect&eacute;s : 
    <input name="record_connectes" type="text" id="record_connectes" value="<?php echo htmlspecialchars($site['record_connectes'], ENT_QUOTES); ?>" size="6" />
    <br />
    <span class="petitbleu">Cette valeur est l&agrave; pour info. Elle n'&eacute;volue 
    que si le log des visites est activ&eacute;.</span></p>
  <?php
	}
	else if ($_GET['categorie'] == 'droits_utilisateurs')
	{
?>
  <h2>Droits des utilisateurs</h2>
  <h3>Valider statut d'animateur</h3>
  <p>Les animateurs de section et d'unit&eacute; peuvent valider le statut d'animateur d'un utilisateur du site.<br />
    
    <input type="radio" name="droits_anim_valide_statut" value="1" id="droits_anim_valide_statut_on"<?php if ($site['droits_anim_valide_statut'] == 1) {echo ' checked="checked"';}?> />
    <label for="droits_anim_valide_statut_on">Oui</label><br />
    
    <input type="radio" name="droits_anim_valide_statut" value="0" id="droits_anim_valide_statut_off"<?php if ($site['droits_anim_valide_statut'] == 0) {echo ' checked="checked"';} ?> />
    <label for="droits_anim_valide_statut_off">Non, seul le webmaster peut valider le statut</label>
  </p>
  <p class="petitbleu">Par mesure de sécurit&eacute;, un animateur de section
     ne peut pas valider le statut d'un animateur d'unit&eacute;. Ceci afin d'&eacute;viter
      l'escalade de droits d'acc&egrave;s. Le statut de webmaster ne peut &ecirc;tre
       attribu&eacute; que par le webmaster, et ce apr&egrave;s une inscription &agrave; 
    un autre statut uniquement.</p>
  <?php
	}
	else if ($_GET['categorie'] == 'forum')
	{
?>
  <h2>Param&egrave;tres du forum</h2>
  <p>Nombre de discussions par page : 
    <input name="forum_nbfils_par_page" type="text" id="forum_nbfils_par_page" value="<?php echo htmlspecialchars($site['forum_nbfils_par_page'], ENT_QUOTES); ?>" size="6" onchange="check_valeur('forum_nbfils_par_page', <?php echo htmlspecialchars($site['forum_nbfils_par_page'], ENT_QUOTES); ?>);" />
  </p>
  <p>Nombre de messages par page  : 
    <input name="forum_nbmsg_par_page" type="text" id="forum_nbmsg_par_page" value="<?php echo htmlspecialchars($site['forum_nbmsg_par_page'], ENT_QUOTES); ?>" size="6" onchange="check_valeur('forum_nbmsg_par_page', <?php echo htmlspecialchars($site['forum_nbmsg_par_page'], ENT_QUOTES); ?>);" />
  </p>
  <p>Nombre derniers messages post&eacute;s sur le forum : 
    <input name="nbre_derniers_msg_forum" type="text" id="nbre_derniers_msg_forum" value="<?php echo htmlspecialchars($site['nbre_derniers_msg_forum'], ENT_QUOTES); ?>" size="5" onchange="check_valeur('nbre_derniers_msg_forum', <?php echo htmlspecialchars($site['nbre_derniers_msg_forum'], ENT_QUOTES); ?>);" />
    <br />
    <span class="petitbleu">Les derniers messages du forum sont affich&eacute;s 
    sur la page d'accueil du portail</span></p>
  <p>Le forum peut &ecirc;tre (d&eacute;s)activ&eacute; depuis la section &quot;<em>Param&egrave;tres techniques du portail</em>&quot; de la configuration.</p>
  <?php
	}
	if (!empty($_GET['categorie']))
	{
?>
  <p align="center"> 
    <input type="submit" name="Submit" value="Enregistrer les modifications" />
    <input type="reset" name="Reset" value="Recommencer" />
  </p>
</form>
<?php
	}
}
else if ($_POST['do'] == 'savemodif')
{ // enregistrement de la nouvelle config
	// pour ajouter un critère, simplement l'ajouter à l'array correspondant
	// tout nouveau critère est automatiquement pris en compte pour la mise à jour
	$listecriteres['general'] = array ('maj', 'show_version', 'adressesite', 'site_actif', 'forum_actif', 'galerie_actif', 'gestion_membres_actif', 
		'balises_meta', 'log_visites', 'show_enligne', 'avert_onclick_lien_externe', 'url_rewriting_actif', 'envoi_mails_actif');	
	$listecriteres['menus'] = array ('deploy_menu_unite', 'deploy_menu_section', 'show_menu_vide', 'modele_menu', 'menu_unite_fixe');
	$listecriteres['galerie'] = array ('galerie_show_nb', 'galerie_show_delai', 'galerie_proportions_photo', 'galerie_proportions_mini', 'galerie_nb_par_page');
	$listecriteres['fichiers'] = array ('upload_max_filesize', 'download_max_filesize');	
	$listecriteres['webmaster'] = array ('webmaster', 'mailwebmaster');	
	$listecriteres['groupe'] = array ('site_ville', 'site_code_postal', 'titre_site', 'nom_unite');
	$listecriteres['messages'] = array ('showmsgmembres', 'message_membres', 'showmsganimateurs', 'messageanimateurs',);
	$listecriteres['divers'] = array ('photo_membre_max_width', 'photo_membre_max_height', 'avatar_max_filesize', 'avatar_max_width', 
		'avatar_max_height', 'nbre_dernieres_news', 'record_enligne', 'record_connectes');
	$listecriters['droits_utilisateurs'] = array('droits_anim_valide_statut');
	$listecriteres['forum'] = array('forum_nbfils_par_page', 'forum_nbmsg_par_page', 'nbre_derniers_msg_forum');
	$listecriteres['droits_utilisateurs'] = array('droits_anim_valide_statut');
	
	if ($_POST['categorie'] == 'general' and ($_POST['url_rewriting_actif'] != $site['url_rewriting_actif'] or $_POST['renew_htaccess'] == '1'))
	{ // on vient de (dés)activer l'url-rewriting
		if ($_POST['url_rewriting_actif'] == 1)
		{ // l'url-rewriting est activé, on met le fichier .htaccess en place
		  // ou on le réécrit si le webmaster a coché la case renew_htaccess
			$furl_rewriter = fopen('prv/htaccess', 'r');
			$url_rewriter = fread($furl_rewriter, @filesize('prv/htaccess'));
			fclose($furl_rewriter);
			$dossier_site = dirname($_SERVER['SCRIPT_NAME']);
			$dossier_site .= (ereg("/$", $dossier_site)) ? '' : '/';
			$url_rewriter = preg_replace('!/scoutwebportail/!', $dossier_site, $url_rewriter);
			@unlink('.htaccess');
			$furl_rewriter = @fopen('.htaccess', 'w');
			@fwrite($furl_rewriter, $url_rewriter);
			@fclose($furl_rewriter);
		}
		else
		{ // l'url-rewriting est désactivé, on supprime le fichier .htaccess
			@unlink('.htaccess');
		}
	}
	// Mise à jour de la table de configuration
	foreach ($listecriteres[$_POST['categorie']] as $champ) 
	{
		$valeur = $_POST[$champ];
		if ($champ == 'adressesite' and !ereg("/$", $valeur))
		{
			$valeur .= '/';
		}
		// On met la db à jour en protégeant les variables
		$sql = 'UPDATE '.PREFIXE_TABLES.'config SET valeur = \''.addslashes(killscriptanimateur($valeur)).'\' WHERE champ = \''.$champ.'\'';
		send_sql($db, $sql);
	}
	log_this('Configuration du portail - catégorie '.$_POST['categorie'].' modifiée', 'config_site');
	reset_config(); // réinitialisation du fichier de config mis en cache
	header('Location: index.php?page=config_site&msg=ok');
}

if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>