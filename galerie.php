<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* galerie.php v 1.1 - Ensemble des albums photos du portail
* Fichier lié : commentpost.php
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
*	Ajout redirection vers création d'une unité après l'installation
*	Correction lien photo précédente et suivante en bas de page
*	Utilisation du 3e paramètre de getimagesize
*	On abandonne la vérification des dimensions de la photo pour l'afficher ou non.
*/


include_once('connex.php');
include_once('fonc.php');
if (!is_array($sections))
{ // les sections n'existent pas encore, il faut les créer d'abord
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<link rel="stylesheet" href="templates/default/style.css" />
<title>Galerie photos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>
<body bgcolor="#FFFFFF">
<?php
	}
	if ($user['niveau']['numniveau'] == 5)
	{
		include('gestion_sections.php');
	}
	else
	{
?>
<h1>Nos albums photos </h1>
<div class="msg">
  <p class="rmq" align="center">La galerie photo n'est pas encore ouverte !</p>
  <p align="center">Les sections du site n'ont pas encore &eacute;t&eacute; cr&eacute;&eacute;es, et seul le webmaster peut le faire.</p>
</div>	
<?php
	}	
}
else if ($site['galerie_actif'] != 1 and $user['niveau']['numniveau'] < 5)
{
	include('module_desactive.php');
}
else
{
	if ($site['galerie_actif'] != 1 and $user['niveau']['numniveau'] == 5)
	{
?>
<p id="message_important">Les albums photos sont inactifs <a href="index.php?page=config_site&amp;categorie=general" title="Activer les albums photos"><img src="templates/default/images/autres.png" alt="Activer les albums photos" /></a></p>
<?php
	}
$niv = (!empty($_GET['niv'])) ? $_GET['niv'] : 'g';
$baselien = 'index.php?niv='.$_GET['niv'].'&amp;page=galerie&amp;';
$show = $_GET['show'];
$photo = $_GET['photo'];
if ($_GET['show'] == 'erreur')
{
?>
<h1>Nos albums photos </h1>
<div class="msg">
  <p class="rmq" align="center">Ton commentaire n'a pas &eacute;t&eacute; enregistr&eacute; !</p>
  <p align="center">Des donn&eacute;es incorrectes ont &eacute;t&eacute; introduites.</p>
  <p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'galerie.htm' : 'index.php?page=galerie'; ?>">retour &agrave; la galerie</a></p>
</div>	
<?php
}
// Aucun album n'est sélectionné. Affichage par défaut
if (empty($_GET['show']))
{
?>
<div id="galerie">
<h1>Nos albums photos</h1>
<div class="introduction">
<p>Ci-dessous, tu trouveras nos albums photos.<br />
  Si tu  souhaites publier tes photos sur le portail, prend contact avec 
    le webmaster, il te renseignera utilement.<br />
    Tu peux ajouter des commentaires aux photos apr&egrave;s t'&ecirc;tre inscrit 
  sur le portail, &ccedil;a prend deux minutes.</p>
</div>
<div class="menu_flottant">
<h2>Options des albums</h2>
<p class="icone"><img src="templates/default/images/gestion_galerie.png" alt="" width="60" height="45" /></p>
<p>
- Tri : <a href="<?php echo $baselien; ?>ordre=chrono<?php echo (!empty($_GET['deploy'])) ? '&amp;deploy='.$_GET['deploy'] : ''; ?>" class="lienmort" title="Trier les albums par ordre chronologique (selon la date de l'activité)"><img src="templates/default/images/chrono.png" alt="Chrono" /></a>
  <a href="<?php echo $baselien; ?>ordre=creation<?php echo (!empty($_GET['deploy'])) ? '&amp;deploy='.$_GET['deploy'] : ''; ?>" class="lienmort" title="Trier les albums par date de création"><img src="templates/default/images/creation.png" alt="Cr&eacute;ation" /></a>
  <a href="<?php echo $baselien; ?>ordre=melange<?php echo (!empty($_GET['deploy'])) ? '&amp;deploy='.$_GET['deploy'] : ''; ?>" class="lienmort" title="Mélanger les albums (ordre différent à chaque clic)"><img src="templates/default/images/melange.png" alt="M&eacute;langer" /></a> 
<br />
<?php
	if (!isset($_GET['deploy']))
	{
?>
  - <a href="<?php echo $baselien; ?>deploy=tous" class="lienmort" title="Afficher la description de tous les albums">Afficher 
  les descriptions</a><br />
<?php
	}
	else
	{
?>
  - <a href="<?php echo $baselien; ?>" class="lienmort" title="N'afficher que quelques descriptions d'albums">R&eacute;duire 
  les descriptions</a><br />
<?php
	}
?>
  - <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'lastcomments.htm' : 'index.php?page=lastcomments'; ?>" class="lienmort" title="Voir les derniers commentaires postés, tous albums confondus">Derniers 
  commentaires</a>
<?php
	if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
	{
?>
  <br />
- <a href="index.php?page=gestion_galerie" class="lienmort" title="Gérer les albums photos de la galerie">Gestion 
  des albums photos</a>
<?php
	}
?>
</p>
</div>
<form method="get" id="choix_section" action="">
<?php
// Affichage des différents albums de la galerie par odre décroissant selon l'ordre de création
	$tri = '';
	$galerie_numsection = 0;
	if (is_array($sections) and !empty($_GET['niv']) and $_GET['niv'] != 'g')
	{ // Les sections sont configurées, on peut les parcourir
		foreach($sections as $section) // récupération du numéro de section en fonction de la lettre site_section
		{
			if ($section['site_section'] == $_GET['niv'])
			{
				$galerie_numsection = $section['numsection'];
			}
		}
	}
	else
	{ // les sections ne sont pas encore configurées
		$galerie_numsection = 0;
	}
	// Option d'afficher les albums les plus récents
	$site['galerie_show_delai'] = (ereg("^([0-9]+) (DAY|MONTH)$", $site['galerie_show_delai'])) ? $site['galerie_show_delai'] : '1 MONTH';	
	$show_defaut = ", if (datecreation > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL ".$site['galerie_show_delai']."), '1', '0') as show_defaut";
	
	$restrict = ($galerie_numsection > 0) ? "WHERE galerie_section = '$galerie_numsection' OR galerie_section = '0'" : '';

	if ($_GET['ordre'] == 'chrono')
	{ // On trie par ordre chronologique de l'activité de l'album
		$sql = "SELECT * $show_defaut FROM ".PREFIXE_TABLES."galerie $restrict ORDER BY dateactivite DESC";
	}
	else if ($_GET['ordre'] == 'melange')
	{ // On mélange au hasard
		$sql = "SELECT * $show_defaut FROM ".PREFIXE_TABLES."galerie $restrict ORDER BY RAND()";
	}
	else
	{ // On affiche par ordre de création des albums
		$sql = "SELECT * $show_defaut FROM ".PREFIXE_TABLES."galerie $restrict ORDER BY datecreation DESC";
	}
	$res = send_sql($db, $sql);
	echo '<span class="titre2">';
	echo ($galerie_numsection > 0) ? 'Photos '.$sections[$galerie_numsection]['nomsectionpt'] : 'Toutes nos photos';
	echo '</span>';

	if (count_sites_sections() > 0)
	{ // Si le webmaster a activé l'espace web d'une section au moins
?>
  <span class="petitbleu"> - Galerie d'une section : <?php 
  	$lien_galerie_a = ($site['url_rewriting_actif'] == 1) ? '\'+this.value+\'_galerie.htm' : 'index.php?niv=\'+this.value+\'&amp;page=galerie';
	$lien_galerie_b = ($site['url_rewriting_actif'] == 1) ? 'galerie.htm' : 'index.php?page=galerie';
?>
  <select name="goto" onchange="if (this.value != 'g') {window.location='<?php echo (isset($par)) ? 'index.php?niv=\'+this.value+\'&page=galerie&par='.$par : $lien_galerie_a; ?>'; } else {window.location='<?php echo (isset($par)) ? 'index.php?page=galerie&par='.$par : $lien_galerie_b; ?>';}">
    <option value="g">Tous les albums</option>
<?php
		if (is_array($sections))
		{ // affichage des sections de l'unité dans le menu déroulant si elles sont configurées
			foreach($sections as $section)
			{
				if (!empty($section['site_section']))
				{ // l'espace web de la section est activé, l'indicatif web fonctionne
					$selected = ($_GET['niv'] == $section['site_section']) ? 'selected' : '';
					echo '<option value="'.$section['site_section'].'" '.$selected.'>'.$section['nomsectionpt'].'</option>';
				}
			}
		}
?>
  </select>
  </span>
<?php
	}
?>
</form>
  <?php
  	$nbre_albums = mysql_num_rows($res);
	if ($nbre_albums != 0)
	{	
?>
<dl class="liste_albums">
<?php
		$i = 1; // permet d'indiquer au script que c'est le i-ème album de la sélection
		$deploy_defaut = $site['galerie_show_nb'];
		while ($album = mysql_fetch_assoc($res))
		{ // On parcourt les albums existants
			$nivcible = (!empty($_GET['niv'])) ? $_GET['niv'] : 'g';
			$lien_album = ($site['url_rewriting_actif'] == 1) ? $nivcible.'_galerie_'.$album['numgalerie'].'.htm' : 'index.php?niv='.$nivcible.'&amp;page=galerie&amp;show='.$album['numgalerie'];
			if ($i <= $deploy_defaut or $nbre_albums < ($deploy_defaut * 1.75) or $album['show_defaut'] == '1' or $_GET['deploy'] == $album['numgalerie'] or $_GET['deploy'] == 'tous')
			{ // On affiche la description de l'album (déployé)
?><dt class="deploye"><a href="<?php echo $lien_album; ?>" class="liengalerie"><?php echo $album['titre']; ?></a></dt>
<dd>
<p><?php
				if (!empty($album['photoaccueil']))
				{ // Une photo d'accueil a été sélectionnée pour l'album
					$imagedaccueil = $album['dossierpt'].$album['photoaccueil'];
					$taille = @getimagesize($imagedaccueil);
					$taille = $taille[3];
?>
<a href="<?php echo $lien_album; ?>" title="Voir les photos de cet album"><img src="<?php echo $imagedaccueil; ?>" alt="" align="left" class="photo_accueil" <?php echo $taille; ?> /></a>
<?php
				}
?>
<?php echo makehtml($album['description']); ?></p>
<?php
				if (!empty($album['description2']))
				{
?>
<p class="petitbleu"><?php echo makehtml($album['description2'], 'html'); ?></p>
<?php
				}
				$pl_photos = ($album['nbrephotos'] > 1) ? 's' : '';
?><p align="right" class="petitbleu infos"><?php echo $album['nbrephotos'].' photo'.$pl_photos;
				echo (!empty($album['auteurphotos'])) ? ' prise'.$pl_photos.' par '.$album['auteurphotos'] : '';
				echo ' - cr&eacute;&eacute; le '.date_ymd_dmy($album['datecreation'], 'enlettres');
?></p>
<?php
			}
			else
			{ // On affiche juste le titre de l'album (non déployé)
				$lien_show = $baselien.'deploy='.$album['numgalerie'];
?>
<dt class="masque">
  <a href="<?php echo $lien_show; ?>" title="Afficher plus d'infos"><img src="img/smileys/008.gif" width="15" height="15" border="0" alt="plus d'infos" /></a>
  <a href="<?php echo $lien_album; ?>" class="liengalerie"><?php echo $album['titre']; ?></a></dt>
<?php
			}
			$i++; // indique le i-ème album affiché
		}
?>
</dl>
<?php
	}
	else
	{ // Aucun album
		if ($niv == 'g')
		{ // la db est vide
?>
<div class="msg">
  <p align="center" class="rmq">Il n'y a pas encore d'album en ligne.</p>
</div>
<?php
		}
		else
		{ // aucun album de section
?>
<div class="msg">
  <p align="center" class="rmq">Il n'y a pas encore d'album en ligne pour cette section.</p>
</div>
<?php
		}
	}
?>   
</div>
<?php
}
else if (is_numeric($_GET['show']))
{ // un album est sélectionné

	$sql = "SELECT * FROM ".PREFIXE_TABLES."galerie WHERE numgalerie = '".$_GET['show']."'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{ // Affichage de l'album sélectionné
		$aff_menu_galerie = true;
		$album = mysql_fetch_assoc($res);
		if ($album['nbrephotos'] > 0)
		{ // Il y a des photos dans l'album
			// Calcul du nombre de pages de l'album
			$par = (is_numeric($site['galerie_nb_par_page'])) ? $site['galerie_nb_par_page'] : 10; // nombre de photos par page
			if ($album['nbrephotos'] > $par)
			{
				$nbrepages = round($album['nbrephotos'] / $par);
				if ($nbrepages * $par < $album['nbrephotos']) $nbrepages++;
			}
			else
			{
				$nbrepages = 1;
			}
			$pg = $_GET['pg'];
			$photo = $_GET['photo'];
			if (isset($pg) and $pg < 1) {$pg = 1;} else if (isset($pg) and $pg > $nbrepages) {$pg = $nbrepages;}
			if (!isset($pg) and !isset($photo)) {$debut = 0; $pg= 1;} // page en cours
			else {$debut = $par * ($pg-1);}
		?>
<div id="galerie">
<h1><?php echo $album['titre']; ?></h1>
<?php
			if (empty($photo))
			{ // aucune photo n'est sélectionnée, affichage des miniatures + commentaires
				// menu de l'album (retour, pg pcdte, pg svte)
				$lien_galerie = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie.htm' : 'index.php?niv='.$niv.'&amp;page=galerie';
				echo '<p align="center"><a href="'.$lien_galerie.'">Retour &agrave; la liste des albums photos</a></p>';
				if ($nbrepages > 1)
				{
					echo '<table width="100%"><tr><td align="left" width="33%">';
					if ($pg > 1)
					{
						$pgpcdte = $pg - 1;
						$lien_galerie_pgpcdte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pgpcdte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pgpcdte;
						echo '<a href="'.$lien_galerie_pgpcdte.'">Page pr&eacute;c&eacute;dente</a>';
					}
					echo '</td><td align="center" width="34%">';
					echo 'Page '.$pg.' de '.$nbrepages;
					echo '</td><td align="right" width="33%">';
					if ($pg < $nbrepages)
					{
						$pgsvte = $pg + 1;
						$lien_galerie_pgsvte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pgsvte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pgsvte;
						echo '<a href="'.$lien_galerie_pgsvte.'">Page suivante</a>';
					}
					echo '</td></tr>';
					// menu de déplacement par numéro de page
					echo '<tr><td colspan="3" align="center"> Aller &agrave; la page : ';
					$j = 1;
					while ($j <= $nbrepages)
					{
						if ($pg != $j)
						{
							$lien_galerie_pgj = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$j.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$j;
							echo '<a href="'.$lien_galerie_pgj.'" class="lienmort">';
						}
						else
						{
							echo '<span class="rmq">&lt;';
						}
						echo $j;
						if ($pg != $j)
						{
							echo '</a>';
						}
						else
						{
							echo '&gt;</span>';
						}
						echo ' ';
						$j++;
					}
					echo '</td></tr>';
					// fin menu par numéro de page
					echo '</table>';
				}
				if (($intro != 0 or empty($intro)) and $pg == 1)
				{ // affichage de la description de l'album sur la page 1.
		?>
<div class="intro">
<?php
					$interligne = (!empty($album['description2'])) ? '<br />' : '';
					echo makehtml($album['description']).' '.$interligne.'<span class="petitbleu">'.html_entity_decode(makehtml($album['description2'], 'html'), ENT_QUOTES).'</span>';
					$pl_photos = ($album['nbrephotos'] > 1) ? 's' : '';
					echo '<p align="right" class="petitbleu">'.$album['nbrephotos'].' photo'.$pl_photos;
					echo (!empty($album['auteurphotos'])) ? ' prise'.$pl_photos.' par '.$album['auteurphotos'] : '';
					echo '</p>';
					if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
					{ // lien vers la gestion de l'album en cours
?>
<form action="index.php" method="post">
  <p align="center">
	<input type="hidden" name="niv" value="g" />
	<input type="hidden" name="page" value="gestion_galerie" />
	<input type="hidden" name="a" id="a" value="modifalbum" />
	<input type="hidden" name="num" value="<?php echo $show; ?>" />
	<input type="button" value="Modifier les infos de cet album" onclick="getElement('a').value = 'modifalbum'; submit();" />
	<input type="button" value="Ajouter des photos" onclick="getElement('a').value = 'addimg'; submit();" />
  </p>
</form>
<?php
					}
?>
</div>
<?php
				}
				// Affichage de la liste des photos en miniature avec leurs commentaires
				$sql = "SELECT * FROM ".PREFIXE_TABLES."albums WHERE numalbum = '".$album['numgalerie']."' ORDER BY posphoto LIMIT $debut, $par";
				$liste = send_sql($db, $sql);
				$pos_max = $debut + $par;
				$photocommentaires = "SELECT * FROM ".PREFIXE_TABLES."commentaires, ".PREFIXE_TABLES."auteurs WHERE auteur = num AND album = '".$album['numgalerie']."' and photo >= '".$debut."' and photo <= '".$pos_max."' and commentairebanni != '1' ORDER BY photo, datecreation ASC";
				$photocommentaires = send_sql($db, $photocommentaires);
				$index_commentaire = 1;
				while ($commentaire_courant = mysql_fetch_assoc($photocommentaires))
				{
					$liste_commentaires_page[$index_commentaire] = $commentaire_courant;
					$index_commentaire++;
				}
				$position = 1;
				$nbreimgsanscomment = 0;
				$largeurtotale = 0;
				while ($pic = mysql_fetch_assoc($liste))
				{
					$ref = $album['dossierpt'].$pic['nomfichier'];
					$taille = @getimagesize($ref);
					$taille = $taille[3];
					if (!empty($pic['commentaire']) or $pic['nbcomment'] > 0)
					{
						// la photo a au moins un commentaire, elle est insérée dans un tableau et les commentaires lui sont joints
						if ($position % 2 != 0) { $classe = 'com_impair';} else { $classe = 'com_pair';}
						$lien_galerie_photo = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$album['numgalerie'].'_'.$pic['posphoto'].'_'.$pg.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$album['numgalerie'].'&amp;photo='.$pic['posphoto'].'&amp;pg='.$pg;
?>
<div class="<?php echo $classe; ?>">
<?php
?><a href="<?php echo $lien_galerie_photo; ?>" title="Voir la photo en grand format">
  <img src="<?php echo $ref; ?>" <?php echo $taille; ?> border="1" class="miniature" align="<?php echo $align; ?>" alt="" /></a>
<?php  
?> <p class="num_photo">Photo <?php echo $pic['posphoto']; ?>
<?php
						if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
						{ // lien vers la suppression d'une photo de l'album
?> <a href="index.php?page=gestion_galerie&amp;a=supprphoto&num=<?php echo $pic['numphoto']; ?>&amp;r=<?php echo urlencode('index.php?'.$_SERVER['QUERY_STRING']); ?>" title="Supprimer cette photo de l'album">
  <img src="templates/default/images/supprimer.png" alt="Supprimer cette photo" border="0" width="12" height="12" /></a>
<?php
						}
?></p><?php
						if ($pic['nbcomment'] > 0)
						{
							// Affichage des commentaires de la photo en cours
							foreach ($liste_commentaires_page as $comment)
							{
								if ($comment['photo'] == $pic['posphoto'])
								{
									$lien_user = ($site['url_rewriting_actif'] == 1) ? 'membre'.$comment['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$comment['num']; 
?>
<div class="commentaire_mini">
<p class="commentaire_mini_auteur">
  <a href="<?php echo $lien_user; ?>" target="_blank" title="Voir son profil"><?php echo $comment['pseudo']; ?></a>
<?php
									if ($user['niveau']['numniveau'] > 2)
									{ // lien pour modérer le commentaire
?>
  <a href="index.php?page=commentpost&amp;do=mod&amp;n=<?php echo $comment['numcommentaire']; ?>&amp;r=<?php echo urlencode('index.php?'.$_SERVER['QUERY_STRING']); ?>" title="Supprimer ce commentaire"><img src="templates/default/images/moins.png" alt="Supprimer ce commentaire" width="12" height="12" border="0" /></a> 
<?php
									}
?></p>
<p class="commentaire_mini_texte">
  <?php echo makehtml($comment['commentaire']); ?></p>
</div>
<?php
								}
							}
						}
?>		
</div>
<?php
						$position++;
					}
					else
					{
						// la photo n'a pas de commentaires, seule la miniature est ajoutée en bas de page
						$nbreimgsanscomment++;
						$alt = ($user['niveau']['numniveau'] > 0) ? 'Ajouter un commentaire ou voir la photo en grand format' : 'Voir la photo en grand format';
						$lien_galerie_photo = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$album['numgalerie'].'_'.$pic['posphoto'].'_'.$pg.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$album['numgalerie'].'&amp;photo='.$pic['posphoto'].'&amp;pg='.$pg;
						$imgsanscomment[$nbreimgsanscomment] = '<a href="'.$lien_galerie_photo.'" title="'.$alt.'"><img src="'.$ref.'" '.$taille.' border="1" style="border-color:#000000;" alt="" /></a>';
					}
				}
				$liste_commentaires_page = '';
				if ($nbreimgsanscomment > 0)
				{
					// affichage des photos sans commentaires en bas de page
					if ($nbreimgsanscomment >= 4)
					{
						$largeurmoyenne = $largeurtotale / $nbreimgsanscomment;
						$parligne = ($largeurmoyenne <= 120) ? 4 : 4; // nbre de photos par ligne
						// ce calcul peut être approfondi pour estimer de meilleure manière le nombre de photos à mettre sur une ligne
					}
					else
					{
						$parligne = $nbreimgsanscomment;
					}
					if ($user['niveau']['numniveau'] > 0)
					{
?>
<div class="msg">
  <p align="center">Un clic de souris sur les photos pour ajouter ton commentaire</p>
</div>
<?php
					}
					else
					{
?>
<div class="msg">
  <p align="center">Pour ajouter des commentaires, inscris-toi ou connecte-toi</p>
</div>
<?php
					}
	
					if ($nbreimgsanscomment > 1)
					{
						for ($numimg = 1; $numimg <= $nbreimgsanscomment; $numimg++)
						{
?>
<div class="liste_photos">
  <?php echo $imgsanscomment[$numimg]; ?>
</div>
<?php
						}
					}
					else
					{
?>
<div class="liste_photos">
  <?php echo $imgsanscomment[1]; ?>
</div>
<?php
					}
				}
			}
			if(!empty($photo))
			{ // affichage d'une photo en grand format
				$sql = "SELECT * FROM ".PREFIXE_TABLES."albums WHERE posphoto = '".$photo."' AND numalbum = '".$show."'";
				$data = send_sql($db, $sql);
				if (mysql_num_rows($data) == 1)
				{
					$aff_menu_galerie = true;
					$pic = mysql_fetch_assoc($data);
					$ref = $album['dossiergd'].$pic['nomfichier'];
					$taille = @getimagesize($ref);
					$taille = $taille[3];
					// menu de la galerie
?>
<?php
					if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
					{ // lien pour supprimer la photo de l'album
?>
<div id="menu_outils_page">
  <a href="index.php?page=gestion_galerie&amp;a=supprphoto&num=<?php echo $pic['numphoto']; ?>&amp;r=<?php echo urlencode('index.php?'.$_SERVER['QUERY_STRING']); ?>" title="Supprimer cette photo de l'album"><img src="templates/default/images/supprimer.png" alt="Supprimer cette photo" border="0" width="12" height="12" /></a>
</div>
<?php
					}
?>
<p class="pagination_photo">
<?php
					if ($photo > 1)
					{
						$photopcdte = $photo - 1;
						$lien_galerie_photo_pcdte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'_'.$photopcdte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;photo='.$photopcdte;
?>
  <a href="<?php echo $lien_galerie_photo_pcdte; ?>" class="photo_pcdte">Photo pr&eacute;c&eacute;dente</a>
<?php
					}
?>
<span class="pg">Photo <?php echo $photo.'/'.$album['nbrephotos']; ?></span>
<?php
					if ($photo < $album['nbrephotos'])
					{
						$photosvte = $photo + 1;
						$lien_galerie_photo_svte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'_'.$photosvte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;photo='.$photosvte;
?>
 <a href="<?php echo $lien_galerie_photo_svte; ?>" class="photo_svte">Photo suivante</a>
<?php
					}

					if (!isset($pg)) 
					{
						$testpage = 1;
						$trouve = false;
						while (!$trouve)
						{
							if ($photo <= $testpage * $par)
							{
								$pg = $testpage;
								$trouve = true;
							}
							else 
							{
								$testpage++;
							}
						}
					}
					$lien_galerie_pg = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pg.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pg;
?>
</p>
<p class="retour_pg"><a href="<?php echo $lien_galerie_pg; ?>">Retour &agrave; la page <?php echo $pg; ?></a></p>
<?php
					// Affichage de la photo en grand format
?>
<div class="galerie_photo">
  <img src="<?php echo $ref; ?>" <?php echo $taille; ?> border="1" style="bordercolor:#000000;" alt="" />
</div>
<?php
					// Affichage des commentaires visiteurs de la photo en cours
					$sql = "SELECT * FROM ".PREFIXE_TABLES."commentaires, ".PREFIXE_TABLES."auteurs WHERE auteur = num AND album = '$show' and photo = '$photo' and commentairebanni != '1' ORDER BY datecreation ASC";
					$commentaires = send_sql($db, $sql);
					if (mysql_num_rows($commentaires) > 0)
					{
						while ($comment = mysql_fetch_assoc($commentaires))
						{
?>
<div class="galerie_comment">
<p class="comment_auteur"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$comment['auteur'].'.htm' : 'index.php?page=profil_user&amp;user='.$comment['auteur']; ?>" target="_blank" title="Voir son profil" class="lienmort"><?php echo $comment['pseudo']; ?></a>
<?php
							if ($user['niveau']['numniveau'] > 2)
							{
?>
      <a href="index.php?page=commentpost&amp;do=mod&amp;n=<?php echo $comment['numcommentaire']; ?>&amp;r=<?php echo urlencode('index.php?'.$_SERVER['QUERY_STRING']); ?>" title="Supprimer ce commentaire"><img src="templates/default/images/moins.png" alt="Supprimer ce commentaire" width="12" height="12" border="0" /></a> 
<?php
							}
?>
</p>
<p class="comment_date">post&eacute; le <?php echo date_ymd_dmy($comment['datecreation'], 'enchiffres'); ?></p>
<p class="comment_texte"><?php echo makehtml($comment['commentaire']); ?></p>
</div>
<?php
						}
					}
					// fin affichage des commentaires
					// formulaire d'ajout de commentaires
					if ($user['niveau']['numniveau'] > 0)
					{
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function validate(form) 
{
	if (form.commentaire.value=="")
	{
		alert("même un 'no comment' peut avoir son petit effet ;-)\nMerci de remplir la case commentaire");
		return false;
	}
	else
	{
		return true; 
	}
}
//-->
</script>
<form action="commentpost.php" method="post" name="comment" id="comment" onsubmit="return validate(this)" class="comment_post">
  <input type="hidden" name="niv" value="<?php echo $_GET['niv']; ?>" />
  <input type="hidden" name="g" value="<?php echo $show; ?>" />
  <input type="hidden" name="p" value="<?php echo $photo; ?>" />
<h2>Ajouter un commentaire</h2>
<p class="comment_post_auteur">Ton pseudo : <span class="rmqbleu"><?php echo $user['pseudo']; ?></p>
<p>Ton commentaire* : </p>
<textarea name="commentaire" rows="5" cols="45" class="comment_post_text"></textarea>
<p class="petit" align="center">Merci de respecter la <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'avertissement.htm' : 'index.php?page=avertissement'; ?>">netiquette</a></p>
<p align="center"><input type="submit" name="Submit" value="Envoyer" /></p>
</form>
<?php
					}
					else
					{
?>
<div class="msg">
<p align="center">Pour poster des commentaires, merci de te 
  connecter ou de t'inscrire sur le portail.</p>
</div>
<?php
						include('login.php');
					}
					// fin formulaire
?><p class="pagination">
<?php
					if ($photo > 1)
					{
						$photopcdte = $photo - 1;
						$lien_galerie_photopcdte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'_'.$photopcdte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;photo='.$photopcdte;
?><a href="<?php echo $lien_galerie_photopcdte; ?>" class="pg_pcdte">Photo pr&eacute;c&eacute;dente</a><?php
					}
					$lien_galerie_pg = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pg.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pg;
?>
  <a href="<?php echo $lien_galerie_pg; ?>" class="pg">Retour &agrave; la page <?php echo $pg; ?></a>
<?php
					if ($photo < $album['nbrephotos'])
					{
						if ($photo % $par == 0) {$pppage = $pg;}
						$photosvte = $photo + 1;
						$lien_galerie_photosvte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'_'.$photosvte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;photo='.$photosvte;
?><a href="<?php echo $lien_galerie_photosvte; ?>" class="pg_svte">Photo suivante</a><?php
					}
?>
</p><?php
				}
				else
				{
					$aff_menu_galerie = false;
					$lien_album = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show;
?>
<div class="msg">
<p align="center" class="rmq">Cette photo ne semble pas exister ! </p>
<p align="center"><a href="<?php echo $lien_album; ?>">Retour &agrave; l'album</a></p>
</div>
<?php
				}
			}
			if ($nbrepages > 1 and $aff_menu_galerie)
			{ // affichage menu page du bas
?>
<p class="pagination">
<?php
				if ($pg > 1)
				{
					$pgpcdte = $pg - 1;
					$lien_galerie_pgpcdte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pgpcdte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pgpcdte;
?><a href="<?php echo $lien_galerie_pgpcdte; ?>" class="pg_pcdte">Page pr&eacute;c&eacute;dente</a><?php
				}
?>
  <span class="pg">Page <?php echo $pg; ?> de <?php echo $nbrepages; ?></span>
<?php
				if ($pg < $nbrepages)
				{
					$pgsvte = $pg + 1;
					$lien_galerie_pgsvte = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie_'.$show.'__'.$pgsvte.'.htm' : 'index.php?niv='.$niv.'&amp;page=galerie&amp;show='.$show.'&amp;pg='.$pgsvte;
?><a href="<?php echo $lien_galerie_pgsvte; ?>" class="pg_svte">Page suivante</a><?php
				}
?>
</p>
<?php
			}
?>
<?php
		} // fin nbrephotos > 0
		else
		{ // l'album est vide.
			$lien_galerie = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie.htm' : 'index.php?niv='.$niv.'&amp;page=galerie';
?>
<h1><?php echo $album['titre']; ?></h1>
<div class="msg">
  <p align="center" class="rmq">Cet album ne contient aucune photo ! </p>
  <p align="center"><a href="<?php echo $lien_galerie; ?>">Retour &agrave; la galerie</a></p>
</div>
<?php
		}
?>
</div>
<?php
	} // fin galerie existe
	else
	{
		$lien_galerie = ($site['url_rewriting_actif'] == 1) ? $niv.'_galerie.htm' : 'index.php?niv='.$niv.'&amp;page=galerie';
?>
<div id="galerie">
<h1>Nos albums photos</h1>
<div class="msg">
  <p align="center" class="rmq">Cet album n'existe pas ! </p>
  <p align="center"><a href="<?php echo $lien_galerie; ?>">Retour &agrave; la galerie</a></p>
</div>
</div>
<?php
	}
}
} // fin if galerie_actif
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>