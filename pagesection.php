<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* pagesection.php v 1.1 - Gestion des pages du portail créées par des utilisateurs
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
*	Ajout d'un lien pour publier la page directement
*	Prise en compte des erreurs de mise en cache et affichage des pages à erreur
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
<title>Gestion des pages du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body bgcolor="#FFFFFF">
<?php
}
if (!is_array($sections))
{ // les sections n'existent pas encore, il faut les créer d'abord
	include('gestion_sections.php');
}
else if (!isset($_GET['do']) and !isset($_POST['do']) and $_GET['page'] == 'pagesection' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page d'Accueil Membres</a></p>
<div class="instructions">
  <p>Ci-dessous, tu trouveras toutes les pages &eacute;ditables du portail. En tant 
    qu'animateur, il te revient de les r&eacute;diger. Une collaboration est possible 
    entre animateurs, les pages r&eacute;dig&eacute;es ne sont pas disponibles 
    automatiquement aux visiteurs du portail.</p>
  <p>Tu peux toujours lire quelques <span class="rmqbleu">explications</span> 
    au bas de cette page</p>
</div>
<form action="index.php" method="get">

  <input type="hidden" name="page" value="pagesection" />
  <input type="hidden" name="do" value="creer" />
<p align="center">
    <input type="submit" tabindex="1" value="Créer une nouvelle page" />
<?php
	if ($user['niveau']['numniveau'] == 5)
	{ // seul le webmaster peut recréer le cache du site, ça évite d'effrayer les utilisateurs lambda avec un bouton supplémentaire
?>
    &nbsp;
    <input name="Button" type="button" tabindex="2" onclick="if (confirm('Es-tu certain de vouloir recréer le cache des pages du site ?\nLe cache est vidé et seules les pages publiées sont remises en cache.')) window.location = 'index.php?page=pagesection&do=rebuild_cache'" value="Recr&eacute;er le cache" />
<?php
	}
?></p>
</form>
<?php
	$sql = "SELECT numpage, page, pseudo, lastmodif, statut, titre, specifiquesection FROM ".PREFIXE_TABLES."pagessections as a, ".PREFIXE_TABLES."auteurs as b WHERE a.lastmodifby = b.num ORDER BY lastmodif DESC";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
?>
<br />
<table width="80%" border="0" align="center" cellspacing="0" class="cadrenoir">
  <tr>
	<th colspan="2" title="Le nom de la page est affich&eacute; si le titre n'est pas d&eacute;fini">Nom - Titre de la page</th>
	<th>Auteur</th>
	<th>Modifi&eacute;e le</th>
	<th>Options</th>
  </tr>
<?php
			$j = 1;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
?>
<tr class="<?php echo $couleur; ?>">
<td width="15" align="center">
<?php
				if ($ligne['statut'] < 2)
				{ // page non publiée
?><img src="templates/default/images/fiche.png" border="0" alt="&Eacute;dition" title="La page n'est pas encore publi&eacute;e" /><?php
				}
				else
				{ // page publiée
?><img src="templates/default/images/ok.png" border="0" alt="Publi&eacute;e" title="La page est publi&eacute;e" /><?php
				}
?>
</td><td><?php
				$titrepage = '';
				if (!empty($ligne['titre'])) {$titrepage = stripslashes($ligne['titre']);} else {$titrepage = $ligne['page'];}
				$nivcible = ($ligne['specifiquesection'] != 0) ? $sections[$ligne['specifiquesection']]['site_section'] : 'g';
				// suppression du g des pages de base du portail
				$nivcible = ($nivcible != 'g') ? $nivcible : '';
				$separateur = ($nivcible != '') ? '_' : '';
				$separateur_no_url_rew = ($nivcible != '') ? 'niv='.$nivcible.'&amp;' : '';
				$lien_page = ($site['url_rewriting_actif'] == 1) ? $nivcible.$separateur.$ligne['page'].'.htm' : 'index.php?'.$separateur_no_url_rew.'page='.$ligne['page'];
				if ($ligne['statut'] == 0)
				{ // la page est vide, il faut la rédiger
					echo '<a href="index.php?page=pagesection&amp;do=rediger&amp;num='.$ligne['numpage'].'" '.$couleur.' title="Rédiger la page">'.$titrepage.'</a>';
				}
				else if ($ligne['statut'] == 1)
				{ // la page est en cours de rédaction, on peut la prévisualiser
					echo '<a href="'.$lien_page.'" '.$couleur.' title="Prévisualiser la page avant publication">'.$titrepage.'</a>';
				}
				else if ($ligne['statut'] == 2)
				{ // la page est publiée
					echo '<a href="'.$lien_page.'" '.$couleur.' title="Afficher la page telle qu\'elle est visible par les visiteurs du portail">'.$titrepage.'</a>';
				}
				// données utiles : dernier auteur et date de modification
?></td>
<td title="<?php echo $ligne['pseudo']; ?> est la dernière personne qui a modifié cette page"><?php echo $ligne['pseudo']; ?></td>
<td align="center" title="Date de la dernière modification de la page"><?php echo date_ymd_dmy($ligne['lastmodif'], 'enchiffres'); ?></td>
<td>
<?php
				if ($ligne['statut'] == 0)
				{ // lien pour rédiger la page
					echo '<a href="index.php?page=pagesection&amp;do=rediger&amp;num='.$ligne['numpage'].'" title="&Eacute;diter la page"><img src="templates/default/images/page_vide.png" align="top" border="0" alt="&Eacute;diter" /></a>';
				}
				else if ($ligne['statut'] == 1)
				{ // lien pour publier et éditer la page ou pour sauvegarder le code source
					echo '<a href="index.php?page=pagesection&amp;do=publier&num='.$ligne['numpage'].'" title="Publier la page"><img src="templates/default/images/login.png" border="0" alt="Publier" /></a>';
					echo ' <a href="index.php?page=pagesection&amp;do=editer&amp;num='.$ligne['numpage'].'" title="&Eacute;diter la page"><img src="templates/default/images/autres.png" border="0" alt="&Eacute;diter" /></a>';
					echo ' <a href="backupnewpage.php?numpage='.$ligne['numpage'].'" title="Sauvegarder le contenu de la page"><img src="templates/default/images/bas.png" width="12" height="12" border="0" alt="Sauvegarder" /></a>';
				}
				else if ($ligne['statut'] == 2)
				{ // page publiée : lien pour remettre une page en édition, sauvegarder le code source et voir le code source
					echo '<a href="index.php?page=pagesection&amp;do=retirer&amp;num='.$ligne['numpage'].'" title="&Eacute;diter la page"><img src="templates/default/images/logoff.png" border="0" alt="&Eacute;diter" /></a>';
					echo ' <a href="backupnewpage.php?numpage='.$ligne['numpage'].'" title="Sauvegarder le contenu de la page"><img src="templates/default/images/bas.png" width="12" height="12" border="0" alt="Sauvegarder" /></a>';
					echo ' <a href="affpage.php?numpage='.$ligne['numpage'].'" title="Voir la source de la page" target="_blank"><img src="templates/default/images/fiche.png" border="0" alt="Source" width="18" height="12" /></a>';
				}
				if ($user['niveau']['numniveau'] == 5)
				{ // le webmaster peut définitivement supprimer une page de la base de données
					echo ' <a href="index.php?page=pagesection&amp;do=supprimer&amp;num='.$ligne['numpage'].'" title="Supprimer la page du portail"><img src="templates/default/images/supprimer.png" border="0" alt="Supprimer" /></a>';
				}
?>
</td>
</tr>
<?php
				$j++;
			}
?>
</table>
<?php
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucune page n'existe pour l'instant</p>
</div>
<?php
		}
?>
<div class="instructions">
<p align="center">
<input type="button" onclick="if(getElement('txt_instr').style.display == 'none') {getElement('txt_instr').style.display = 'block'; this.value = 'Masquer les conseils';} else {getElement('txt_instr').style.display = 'none'; this.value = 'Afficher les conseils';}" value="Afficher les conseils" />
</p>
<div id="txt_instr" style="display:none;">
<h2>Statuts des pages</h2>
<ul>
  <li><img src="templates/default/images/page_vide.png" alt="" width="12" height="12" align="top" /> 
    R&eacute;diger une page vide.</li>
  <li><img src="templates/default/images/autres.png" alt="" width="12" height="12" align="top" /> 
    Editer la page en cours de r&eacute;daction.</li>
  <li><img src="templates/default/images/login.png" alt="" width="12" height="12" /> 
    Publier la page. Tu as termin&eacute; les modifications et tu rends la nouvelle 
    version de la page accessible au public.</li>
  <li><img src="templates/default/images/logoff.png" alt="" width="12" height="12" align="top" /> 
    Mettre la page en &eacute;dition. En cliquant sur ce bouton, tu places la 
    page en mode &eacute;dition. L'ancienne version reste visible pour les visiteurs 
    du site mais les membres qui peuvent la modifier voient la version en cours 
    d'&eacute;dition.</li>
  <li> <img src="templates/default/images/bas.png" alt="" width="12" height="12" align="middle" /> 
    Enregistrer le code source de la page sur ton disque dur.</li>
  <li><img src="templates/default/images/fiche.png" alt="" width="18" height="12" align="middle" /> 
    Consulter le code source de la page.</li>
<?php
			if ($user['niveau']['numniveau'] == 5)
			{
?>
  <li><img src="templates/default/images/supprimer.png" alt="" width="12" height="12" /> 
    Supprimer la page du portail (les liens menant vers elle depuis le reste du 
    portail ne sont pas supprim&eacute;s).</li>
<?php
			}
?>
</ul>
<p class="petitbleu">Les pages du portail qui ne sont pas 
  pr&eacute;sentes dans la liste ci-dessus sont soit g&eacute;n&eacute;r&eacute;es 
  automatiquement soit ne sont pas modifiables via l'interface web. Consulte le 
  webmaster pour plus de d&eacute;tails.</p>
<h2>Mise en cache</h2>
<p>Quand tu publies une page, son contenu est mis en cache (copi&eacute; dans 
  un fichier, afin d'acc&eacute;l&eacute;rer le chargement des pages) et c'est 
  la page en cache qui est visible pour les visiteurs du site. Si tu &eacute;dites 
  la page, la version en cache continue &agrave; &ecirc;tre affich&eacute;e pour 
  les membres non connect&eacute;s mais c'est la page en cours d'&eacute;dition 
  qui s'affiche pour les membres qui ont le droit de l'&eacute;diter.</p>
</div>
</div>
<?php
	}
}
/* Divers termes sont utilisés pour la variable do :
- creer : la page n'est pas encore présente dans la base de données (INSERT)
- rediger : la page est vide, il faut la remplir (UPDATE) (statut == 0)
- editer : la page est en prépublication (UPDATE) (statut == 1)
*/
else if (($_GET['do'] == 'creer' or (($_GET['do'] == 'rediger' or $_GET['do'] == 'editer') and is_numeric($_GET['num']))) and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // modifier, rédiger la page
	include('pagesectionpost.php');
}
else if ($_GET['do'] == 'retirer' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // on remet la page en édition
	if (is_numeric($_GET['num']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."pagessections SET statut = '1' WHERE numpage = '$_GET[num]'";
		send_sql($db, $sql);
		log_this('Désactivation page site ('.$_GET['num'].')', 'pagesection');
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection" align="center">
  <p class="rmq">Tu peux maintenant modifier la page.</p>
  <p class="petit">Pour rendre les modifications visibles aux visiteurs du site, publie la page.<br />
    En attendant, 
  ils voient l'ancienne version.</p>
  <p><a href="index.php?page=pagesection&amp;do=editer&amp;num=<?php echo $_GET['num']; ?>" class="bouton" tabindex="1">Editer la page</a></p>
</div>
<?php
	}
}
else if ($_GET['do'] == 'publier' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // on republie la page (confirmation)
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection" align="center">
  <p class="rmq">Es-tu certain de vouloir publier cette page et la rendre accessible 
  à tous ?</p>
  <p><a href="index.php?page=pagesection&amp;do=publierok&amp;num=<?php echo $_GET['num']; ?>" class="bouton" tabindex="1">Oui</a> 
  <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="bouton" tabindex="2">Annuler</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'publierok' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // publication de la page
	if (is_numeric($_GET['num']))
	{
		// maj dans la db
		$sql = "UPDATE ".PREFIXE_TABLES."pagessections SET statut = '2' WHERE numpage = '$_GET[num]'";
		send_sql($db, $sql);
		log_this('Publication page site ('.$_GET['num'].')', 'pagesection');
		$sql = "SELECT page, specifiquesection, format, contenupage, titre FROM ".PREFIXE_TABLES."pagessections WHERE numpage = '$_GET[num]'";
		$res = send_sql($db, $sql);
		$ligne = mysql_fetch_assoc($res);
		$page = $ligne['page'];
		$nivcible = $ligne['specifiquesection'];
		$niv = $sections[$nivcible]['site_section'];
		// on empêche l'apparition du préfixe de section pour les pages générales du portail (specifiquesection = 0)
		$niv_url_rew = ($page == 'index'.$niv or empty($niv)) ? '' : $niv.'_';
		$niv_non_url_rew = (!empty($niv)) ? 'niv='.$niv.'&amp;' : '';
		// On met la page en cache (ou on renouvelle le cache de cette page, c'est la même chose)
		$ok_pg_cache = false;
		if ($pg_cache = @fopen('cache/'.$page.'.cache', 'w'))
		{
			$ligne['contenupage'] = ($ligne['format'] == 'html') ? makehtml($ligne['contenupage'], 'html') : makehtml($ligne['contenupage'], 'ibbcode');
			if (!empty($ligne['titre']))
			{
				@fwrite($pg_cache, '<h1>'.makehtml($ligne['titre']).'</h1>');
			}
			@fwrite($pg_cache, $ligne['contenupage']);
			@fwrite($pg_cache, '<!-- Page en cache -->');
			fclose($pg_cache);
			$ok_pg_cache = true;
		}
		
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection">
<p align="center"><span class="rmqbleu">La page est d&eacute;sormais accessible &agrave; 
tous les visiteurs du portail.</span></p>
<?php
		if (!$ok_pg_cache)
		{
			log_this('Impossible de créer le cache des pages du site', 'index');
?>
<p align="center"><span class="rmq">Impossible de mettre la page en cache !</span><br />
  Demande au webmaster d'autoriser le portail &agrave; &eacute;crire dans le dossier 
  cache/<br />
  La page que tu as publi&eacute;e est accessible mais pas de mani&egrave;re optimale.</p>
<?php
		}
?>
        
<p align="center"><span class="petit">Merci d'<a href="backupnewpage.php?numpage=<?php echo $_GET['num']; ?>">enregistrer 
  la page sur ton disque dur</a>. Cela permettra de <br />
  reconstituer le portail rapidement en cas de probl&egrave;me</span></p>
<p align="center">Son adresse : <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? $niv_url_rew.$page.'.htm' : 'index.php?'.$niv_non_url_rew.'page='.$page; ?>" class="rmqbleu" tabindex="1"><?php echo ($site['url_rewriting_actif'] == 1) ? $niv_url_rew.$page.'.htm' : 'index.php?'.$niv_non_url_rew.'page='.$page; ?></a></p>
</div>
<?php
	}
}
else if ($_GET['do'] == 'supprimer' and $user['niveau']['numniveau'] == 5)
{ // le webmaster confirme vouloir supprimer une page
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection">
<p align="center" class="rmq">Es-tu certain de vouloir supprimer d&eacute;finitivement 
  cette page du portail ?</p>
<p class="petit">Cette action est irr&eacute;versible; tous les liens menant 
  vers cette page aboutiront &agrave; une page d'erreur.</p>
<p align="center"><a href="index.php?page=pagesection&amp;do=supprimerok&amp;num=<?php echo $_GET['num']; ?>" class="bouton" tabindex="1">Oui</a>  <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="bouton" tabindex="2">Annuler</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'supprimerok' and $user['niveau']['numniveau'] == 5)
{ // on supprime la page
	if (is_numeric($_GET['num']))
	{
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection" align="center">
<?php
		$nompage = untruc(PREFIXE_TABLES.'pagessections', 'page', 'numpage', $_GET['num']);
		// de la db
		$sql = "DELETE FROM ".PREFIXE_TABLES."pagessections WHERE numpage = '$_GET[num]'";
		send_sql($db, $sql);
?>
<p class="rmqbleu">La page a &eacute;t&eacute; supprim&eacute;e avec succ&egrave;s du portail.</p>
<?php
		if (file_exists('cache/'.$nompage.'.cache') and !@unlink('cache/'.$nompage.'.cache'))
		{ // éventuellement du cache
?>
<p class="rmq">Cependant, la page en cache n'a pas pu &ecirc;tre supprim&eacute;e. Supprime toi-m&ecirc;me le fichier cache/<?php echo $nompage; ?>.cache.</p>
<?php
		}
?>
<p><a href="index.php?page=pagesection" tabindex="1">Retour &agrave; la Gestion du portail</a></p>
</div>
<?php
	}
}
else if ($_GET['do'] == 'rebuild_cache' and $user['niveau']['numniveau'] == 5)
{ // petit rafraichissement du cache du site
?>
<h1>Gestion des pages du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour &agrave; la Gestion des pages du portail</a></p>
<div class="info_pgsection">
<p class="rmqbleu">Cr&eacute;ation du cache en cours ...</p>
<?php
	if ($contenu_cache = @OpenDir('cache/'))
	{
		while($old_file = ReadDir($contenu_cache))
		{
			if (eregi("\.cache$", $old_file))
			{ // suppression des fichiers du cache
				echo '- Suppression de <strong>'.$old_file."</strong> dans le cache<br />\n";
				flush();
				@unlink($old_file);
			}
		}
	}
	// on renouvelle le cache en prenant les pages publiées uniquement
	$sql = "SELECT page, contenupage, format, titre FROM ".PREFIXE_TABLES."pagessections WHERE statut = '2'";
	$res = send_sql($db, $sql);
	$nbre_pages = mysql_num_rows($res); // nombre de pages à remettre en cache
	while ($ligne = mysql_fetch_assoc($res))
	{
		if ($pg_cache = @fopen('cache/'.$ligne['page'].'.cache', 'w'))
		{ // on met la page en cache
			$ligne['contenupage'] = ($ligne['format'] == 'html') ? makehtml($ligne['contenupage'], 'html') : makehtml($ligne['contenupage'], 'ibbcode');
			if (!empty($ligne['titre']))
			{
				@fwrite($pg_cache, '<h1>'.makehtml($ligne['titre'], 'html').'</h1>');
			}
			@fwrite($pg_cache, $ligne['contenupage']);
			@fwrite($pg_cache, '<!-- Version en cache de la page -->');
			@fclose($pg_cache);
			$nbre_pages--; // il ne reste plus que x pages à mettre en cache
			echo '- Mise en cache de <strong>'.$ligne['page'].".cache</strong><br />\n";
			flush();
		}
		else
		{ // une page n'est pas remise en cache
			echo '- Impossible de mettre <span class="rmq">'.$ligne['page'].".cache</span> en cache<br />\n";
		}
	}
	echo ($nbre_pages == 0) ? '<p align="center" class="rmqbleu">Le cache a &eacute;t&eacute; recr&eacute;&eacute; avec succ&egrave;s.</p>' : '<p align="center" class="rmq">Impossible de mettre '.$nbre_pages.' des pages en cache dans le dossier <strong>cache/</strong>.</p>';
?>
<p align="center"><a href="index.php?page=pagesection" tabindex="1">Retour &agrave; la Gestion du portail</a></p>
</div>
<?php
}
else
{
	include('404.php');
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>