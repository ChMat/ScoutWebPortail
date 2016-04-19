<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fichiers.php v 1.1 - Liste des fichiers disponibles au téléchargement
* Fichiers liés à cet outil : file_upload.php, download.php, fichiers_gestion.php
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
*	Ajout mention niveau minimum pour télécharger le fichier
*	Gestion des rubriques de téléchargement
*	Mise en place des fichiers vedettes
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
<title>Page de t&eacute;l&eacute;chargements</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Page de t&eacute;l&eacute;chargements</h1>
<div class="instructions">
<p>Sur cette page, tu trouveras les fichiers que nous te proposons de t&eacute;l&eacute;charger. 
  Normalement ils ne contiennent pas de virus. Cependant, tu prends tes responsabilit&eacute;s 
  en les t&eacute;l&eacute;chargeant. Les antivirus sont l&agrave; pour &ecirc;tre 
  utilis&eacute;s, profites-en ! </p>
<p>Clique sur le nom du fichier pour 
  le t&eacute;l&eacute;charger.</p>
</div>
<?php
if ($user['niveau']['numniveau'] > 2)
{ // l'ajout de fichiers est disponible pour les animateurs uniquement
?>
<div class="action">
<p>Tu peux d&eacute;poser des fichiers sur le site. </p>
<p align="center"><a href="index.php?page=fichiers_gestion&amp;do=upload" class="bouton">D&eacute;poser
    un fichier sur le portail</a> <a href="index.php?page=fichiers_gestion&amp;do=rubriques" class="bouton">G&eacute;rer les rubriques</a> </p>
</div>
<?php
}
?>
<?php
if (!is_numeric($_GET['rub']))
{ // aucune rubrique sélectionnée, on les affiches toutes
	// On commence par afficher les fichiers vedettes
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'fichiers, '.PREFIXE_TABLES.'auteurs WHERE vedette = \'1\' AND file_auteur = num AND public = \'0\' ORDER BY dateupload DESC LIMIT 5';
	if ($res = send_sql($db, $sql))
	{ // on liste les fichiers vedettes
		if (mysql_num_rows($res) > 0)
		{
?>
<h2>Fichiers en vedette</h2>
<div id="download_liste">
<ul class="download_fichier">
<?php
			while ($ligne = mysql_fetch_assoc($res))
			{	
?>
<li><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers_'.$ligne['cat_id'].'.htm#'.$ligne['cledownload'] : 'index.php?page=fichiers&amp;rub='.$ligne['cat_id'].'#'.$ligne['cledownload']; ?>" title="Plus d'infos" class="lienmort">
<?php echo makehtml($ligne['titre_fichier']); ?></a> <span class="petit">(<strong><?php echo $ligne['pseudo']; ?></strong> le <?php echo date_ymd_dmy($ligne['dateupload'], 'enlettres'); ?>)</span></li>
<?php
			}
?>
</ul>
</div>
<?php
		}
	} // fin des vedettes
	
	// on récupère le nombre de fichiers par rubrique et accessibles à l'utilisateur en cours
	$niveau_minimum = ($user != 0) ? $user['niveau']['numniveau'] : 0;
	$sql = 'SELECT cat_id, count(*) as nbre FROM '.PREFIXE_TABLES.'fichiers WHERE public <= \''.$niveau_minimum.'\' GROUP BY cat_id';
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) > 0)
	{ // Des fichiers sont disponibles, on affiche la liste des rubriques correspondantes
		while ($ligne = mysql_fetch_assoc($res))
		{ // 
			$nbre_par_rubrique[$ligne['cat_id']] = $ligne['nbre'];
		}
		// On liste les rubriques existantes
		$sql = 'SELECT cat_id, cat_titre, cat_description FROM '.PREFIXE_TABLES.'fichiers_cat ORDER BY cat_titre';
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
?>
<h2>Rubriques de t&eacute;l&eacute;chargement</h2>
<dl class="rubriques">
<?php
			while ($rubrique = mysql_fetch_assoc($res))
			{
				if ($nbre_par_rubrique[$rubrique['cat_id']] > 0)
				{ // on affiche les rubriques qui contiennent des fichiers intéressants pour l'utilisateur
?><dt><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers_'.$rubrique['cat_id'].'.htm' : 'index.php?page=fichiers&amp;rub='.$rubrique['cat_id']; ?>"><?php echo $rubrique['cat_titre']; ?></a></dt>
<dd><p><?php echo makehtml($rubrique['cat_description']); ?></p>
<p class="petitbleu">Cette rubrique contient <?php 
  echo ($nbre_par_rubrique[$rubrique['cat_id']] > 1) ? $nbre_par_rubrique[$rubrique['cat_id']].' fichiers' : '1 fichier'; 
?></p></dd>
<?php
				}
			}
?></dl>
<?php
		}
		else
		{ // aucune rubrique (en théorie ce message ne doit jamais apparaître)
?>
<div class="msg">
<p class="rmq" align="center">Il n'y a aucune rubrique pour le moment.</p>
<?php
			if ($user['niveau']['numniveau'] > 2)
			{
?>
<p align="center">Pour permettre le t&eacute;l&eacute;chargement de fichiers, cr&eacute;e au moins une rubrique.</p>
<?php
			}
?></div>
<?php
		}
	}
	else
	{ // aucun fichier n'est disponible
?>
<div class="msg">
<p class="rmq" align="center">Aucun fichier n'est disponible pour l'instant.</p>
<?php
?></div>
<?php
		
	}
}
else
{ // une rubrique est sélectionnée
?>
<h2><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fichiers.htm' : 'index.php?page=fichiers'; ?>" title="Revenir &agrave; la liste des rubriques" class="lienmort">
	Rubriques</a> &gt;
	<?php echo untruc(PREFIXE_TABLES.'fichiers_cat', 'cat_titre', 'cat_id', $_GET['rub']); ?></h2>
<?php
	$niveau_minimum = ($user != 0) ? $user['niveau']['numniveau'] : 0;
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'fichiers, '.PREFIXE_TABLES.'auteurs WHERE cat_id = \''.$_GET['rub'].'\' AND file_auteur = num AND public <= \''.$niveau_minimum.'\' ORDER BY vedette DESC, dateupload DESC';
	if ($res = send_sql($db, $sql))
	{ // on liste les fichiers de la rubrique en cours
		if (mysql_num_rows($res) > 0)
		{
?>
<div id="download_liste">
<?php
			while ($ligne = mysql_fetch_assoc($res))
			{	
?>
<div class="download_fichier">
<h3 id="<?php echo $ligne['cledownload']; ?>"><?php 
				if ($user['niveau']['numniveau'] > 2)
				{
?>
<span>
<a href="index.php?page=fichiers_gestion&amp;do=modif&amp;f=<?php echo $ligne['cledownload']; ?>" title="Modifier les informations de ce fichier"><img src="templates/default/images/autres.png" alt="Modifier" width="12" height="12" border="0" align="middle" /></a> 
<a href="index.php?page=fichiers_gestion&amp;do=delete&amp;f=<?php echo $ligne['cledownload']; ?>" class="lien" title="Supprimer ce fichier"><img src="templates/default/images/supprimer.png" alt="Supprimer" width="12" height="12" border="0" align="middle" /></a> 
</span>
<?php
				}
?>
<?php echo makehtml($ligne['titre_fichier']); ?></h3>
    <div class="infos_fichier">
	Taille : <?php echo taille_fichier('fichiers/'.$ligne['nomserveur']); ?><br />
<?php
				if ($ligne['lu'] > 0) 
				{
?>
      T&eacute;l&eacute;charg&eacute; <?php echo $ligne['lu']; ?> fois<br /> 
<?php 
				}
?>
    Nom du fichier : <a href="download.php?fichier=<?php echo $ligne['cledownload']; ?>" title="Télécharger ce fichier" class="lienmort"><strong><?php echo $ligne['nomoriginal']; ?></strong></a><br />
    D&eacute;pos&eacute; par <strong><?php echo $ligne['pseudo']; ?></strong> le <?php echo date_ymd_dmy($ligne['dateupload'], 'enlettres'); ?><br />
<?php
				// Cette variable est définie dans fonc.php à partir de la version 1.1
				$statuts = array(
					'Acc&egrave;s public', 
					'Membre du portail', 
					'Membre de l\'Unit&eacute;', 
					'Animateur de section (AnS)', 
					'Animateur d\'Unit&eacute; (AnU)', 
					'Webmaster (Web)');
				if ($ligne['public'] > 0)
				{
?>
	Niveau minimum : <strong><?php echo ereg_replace("\([a-zA-Z]{3}\)$", '', $statuts[$ligne['public']]); ?></strong>
<?php
				}
?>
    </div>
    <div class="desc_fichier"><?php echo makehtml($ligne['description_fichier']); ?></div>
</div>
<?php
			}
?>
</div>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Cette rubrique ne contient aucun fichier.</p>
</div>
<?php
		}
	}
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>