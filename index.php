<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* index.php v 1.1.1 - Page principale du portail
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
*	Remplacement de include() par get_file_contents() pour les pages en cache
*	      afin d'empêcher l'exécution de code php depuis une page utilisateur
*	Suppression affichage barre d'outils sur page publiée mais pas en cache pour visiteur
*	Correction lien vers profil de l'auteur de la page consultée
*	correction bug specifiquesection (fil 105)
*	transfert du menu dans un fichier externe (menu_site.php)
*	Gestion des styles de menus
*	suppression du tableau du footer
*	optimisation affichage page non mise en cache (encore quelques détails à régler)
*	page mise à jour : on exploite la date de modif du fichier en cache
*	simplification de la détection de mise à jour ou d'installation
* Modifications v 1.1.1
*	Ajout d'un cookie de temporisation pour la vérification des nouvelles versions du portail sur membres.php
*	Ajout d'un message si la page est publiée mais pas en cache.
*/

error_reporting(E_ERROR | E_WARNING | E_PARSE);

define('IN_SITE', true); // IN_SITE permet de garantir l'ouverture de certaines pages autrement que par l'index
$message_important = ''; // variable contenant l'avertissement portail offline
$s = explode(' ', microtime()); // démarrage du chrono pour calculer le temps d'exécution de la page
if (!isset($_COOKIE['x_login']))
{ // Génération d'un cookie prélogin pour déterminer si le visiteur accepte les cookies
	setcookie('x_login', 'Je suis un bête cookie... super non ?');
}
@include_once('connex.php'); // le @ est là car connex.php est créé à l'installation
if (defined('INSTALL_DONE') and INSTALL_DONE) // paramètres de connexion à la DB
{ // le portail est installé
	if (@file_exists('install.php') and @is_dir('install'))
	{ // le fichier d'installation est présent. Probablement pour une mise à jour
		header('Location: install.php');
		exit;
	}
}
else
{
	if (@file_exists('install.php') and @is_dir('install'))
	{
		header('Location: install.php');
		exit;
	}
	else
	{
		echo 'Installation du portail impossible : les fichiers d\'installation n\'ont pas été trouvés.';
	}
}
include_once('fonc.php'); // bibliothèques de fonctions, connexion du membre, chargement paramètres du portail, ...

if ($_GET['page'] == 'membres' and !isset($_COOKIE['last_check_version']) and $user['niveau']['numniveau'] == 5 and is_array($sections))
{ // le webmaster n'a pas vérifié la dernière version du portail depuis 5 jours
	setcookie('last_check_version', time(), time() + 432000);
	$can_check_version = true;
}


// si le portail est en local, les requêtes SQL sont affichées en bas de page pour permettre le débogage
$message_important .= (defined('LOCAL_SITE')) ? 'Site local' : ''; // message affiché au webmaster

if ($site['site_actif'] != '1' and $user['niveau']['numniveau'] < 5)
{ // le portail est offline, on renvoie les visiteurs vers la page portail_desactive.php
  // un lien leur proposera de revenir à la page qu'ils cherchaient
	header('Location:portail_desactive.php?'.$_SERVER['QUERY_STRING']);
	exit;
}

// le webmaster est connecté, un message l'avertit que le portail est offline
$message_important .= ($site['site_actif'] != '1' and $user['niveau']['numniveau'] == 5) ? '<strong>Portail hors ligne</strong> - Tous les visiteurs voient la page portail_desactive.php au lieu du site.  <a href="index.php?page=config_site&amp;categorie=general" title="Activer le portail"><img src="templates/default/images/autres.png" alt="Activer le portail" /></a>' : '';
if (defined('NO_WRITE_DOSSIER_PORTAIL') and $user['niveau']['numniveau'] == 5)
{ // impossible de mettre la config du site en cache, on avertit le webmaster
	$message_important .= '
	<span class="rmq">Le portail ne parvient pas à placer le fichier 
	  de configuration en cache.</span><br />
	  Pour cela, cr&eacute;e un fichier \'config.php\' dans le dossier principal 
	  du portail et autorise le portail &agrave; &eacute;crire dans ce 
	  fichier (chmod du fichier).';
}
// $page et $niv déterminent la page à afficher et la section en cours.
$page = (!empty($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$niv = (!empty($_GET['niv'])) ? $_GET['niv'] : $_POST['niv'];
// définition de la page à charger par défaut (accueil du portail indexg.php)
$niv = (empty($niv)) ? 'g' : $niv;
$page = (empty($page)) ? 'index' : $page;

$deconnexion_interdite = false;
if ($page == 'logoff')
{ // l'utilisateur souhaite se déconnecter
	if ($user['niveau']['numniveau'] == 5 and $site['site_actif'] != '1')
	{ // la déconnexion est refusée au webmaster s'il a désactivé le portail.
	  // ca permet d'éviter de travailler directement dans la db pour réactiver le portail manuellement.
		define('NO_LOGOFF', true);
		$page = 'membres';
	}
	else
	{ // déconnexion du membre
		deconnexion($user['num']);
		$user = 0;
	}
}

// Récupération du titre de la page
$titre_site = '';
if (eregi("^[-a-z0-9_]+$", $page))
{ // Ca ne récupère le titre que des pages publiées par les membres (dans la db)
	$sql = "SELECT titre FROM ".PREFIXE_TABLES."pagessections WHERE page = '$page' AND statut = '2'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{ // le titre de la $page est défini
		$lapage = mysql_fetch_assoc($res);
		$titre_site = $lapage['titre'];
	}
}
$titre_site = (empty($titre_site)) ? $site['titre_site'] : $titre_site;

$txtrequetes .= (defined('LOCAL_SITE')) ? '<br />d&eacute;but corps index.php<br />' : '';
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php echo makehtml($titre_site); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="<?php echo $site['adressesite']; ?>favicon.ico" />
<?php echo $site['balises_meta']; ?>
<meta name="URL" content="<?php echo $site['adressesite']; ?>" />
<link type="text/css" media="screen" rel="stylesheet" href="templates/default/style.css" />
<?php
// On charge la feuille de style propre au menu
if (@file_exists('templates/default/menus/'.$site['modele_menu'].'.css'))
{
	echo '<link type="text/css" media="screen" rel="stylesheet" href="templates/default/menus/'.$site['modele_menu'].'.css" />';
}
else
{
?>
<link type="text/css" media="screen" rel="stylesheet" href="templates/default/menus/complet.css" />
<?php
}
?>

<link type="text/css" media="print" rel="stylesheet" href="templates/default/style_print.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="templates/default/style_ie.css">
<![endif]-->
<link rel="alternate" type="application/rss+xml" title="Actualité - <?php echo $site['titre_site']; ?>" href="news.xml" />
<?php
if (defined('NO_LOGOFF'))
{
?><script type="text/javascript" language="JavaScript">alert("Le portail est inactif. Déconnexion du webmaster impossible.\n\nSi tu te déconnectes, il te sera impossible de réactiver le portail ou de faire quoi que ce soit.");</script><?php
}
?>
<script type="text/javascript" language="JavaScript" src="fonc.js"></script>
</head>
<body>
<div id="index">
<a name="sommetpage" id="sommetpage"></a> 
<div id="top_page">
<h1><?php echo makehtml($site['titre_site']); ?></h1>
</div>
<?php
// Insertion du menu du site
include_once('menu_site.php');
?>
<div id="corps">
<?php
if (!empty($message_important))
{
?>
<p id="message_important"><?php echo $message_important; ?></p>
<?php
}
if (defined('LOCAL_SITE'))
{ // suivi des requêtes sql lorsque le portail est exécuté en local
	$txtrequetes .= '<br />d&eacute;but contenu<br />';
}
// sécurité pour empêcher le chargement de pages se trouvant en dehors de la racine du portail
$page = urldecode($page);
$page = (eregi("^[-a-z0-9_]+$", $page)) ? $page : ''; // on vire tout ce qui n'est pas acceptable
// fin sécurité
if (empty($page) or $page == 'index')
{
	$page = 'index'.$niv;
}
/* affichage de la page demandée
si un fichier existe au nom de la variable $page, celui-ci est inclus
autrement, la page est recherchée dans la base de données.
Si la page a un statut d'édition provisoire, l'utilisateur doit être
connecté pour la consulter en version bêta.
*/
if (file_exists($page.'.php'))
{ // le fichier $page qui se trouve à la racine du portail est inclus en priorité.
	include($page.'.php');
}
else
{ // le fichier $page n'est pas un fichier officiel du portail
	$nivcible = 0;
	if (is_array($sections))
	{ // récupération du numéro de section correspondant à la lettre de $niv
		foreach ($sections as $test_niv) 
		{ 
		   if ($test_niv['site_section'] == $niv) 
		   {
			   $nivcible = $test_niv['numsection'];
		   }
		}
	}
	$pg_cache = @file_exists('cache/'.$page.'.cache');
	if ($pg_cache and function_exists('file_get_contents'))
	{ // $page est en cache
		// on récupère la date de mise à jour de la page en cache (dernière modif du fichier)
		$last_modif_page = date("Y-m-d", @filectime('cache/'.$page.'.cache'));

		if ($user['niveau']['numniveau'] < 3 and $user['assistantwebmaster'] != 1)
		{ // l'utilisateur ne peut pas modifier $page, il voit la $page en cache
			echo @file_get_contents('cache/'.$page.'.cache'); // plutôt que include() car include() exécute le code php
			$afficher_page_db = false;
		}
		else
		{ // l'utilisateur peut modifier la $page, on vérifie le statut de celle-ci.
			$sql = "SELECT numpage, statut FROM ".PREFIXE_TABLES."pagessections WHERE page = '$page' LIMIT 1";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{
				$lapage = mysql_fetch_assoc($res);
				$statut_page = $lapage['statut'];
				if ($statut_page == 2)
				{ // la page est publiée, l'utilisateur voit la page en cache
?>
<div id="menu_outils_page"> <a href="javascript:if (confirm('Es-tu certain de vouloir &eacute;diter la page ?\n\nLes visiteurs continueront &agrave; voir l\'ancienne version jusqu\'&agrave; la prochaine publication.')) this.location = 'index.php?page=pagesection&amp;do=retirer&amp;num=<?php echo $lapage['numpage']; ?>';" class="rmqbleu" title="D&eacute;sactiver la page pour l'&eacute;diter"><img src="templates/default/images/logoff.png" width="12" height="12" border="0" align="top" alt="éditer" /></a> 
  <a href="backupnewpage.php?numpage=<?php echo $lapage['numpage']; ?>" title="Sauvegarder le contenu de la page"><img src="templates/default/images/bas.png" width="12" height="12" border="0" align="top" alt="sauvegarder" /></a> 
  <a href="affpage.php?numpage=<?php echo $lapage['numpage']; ?>" target="_blank" title="Afficher la source de la page"><img src="templates/default/images/fiche.png" border="0" width="18" height="12" align="top" alt="source" /></a> 
</div>
<?php
					echo @file_get_contents('cache/'.$page.'.cache'); // plutôt que include() car include() exécute le code php
					$afficher_page_db = false;
				}
				else if ($statut_page == 1)
				{ // la page est en cours de modification
				  // l'utilisateur voit la page de la db
					$afficher_page_db = true;
				}
				else if ($statut_page == 0)
				{ // la page est vide, inutile de la charger dans la db
				  // Mais on affiche quand même le message d'avertissement
				  // $afficher_page_db reviendra à false un peu plus bas
					$afficher_page_db = true;
				}
			}
			else
			{ // la page est en cache mais n'existe plus.
				$afficher_page_db = true;
			}
		}
	} // fin $page en cache
	if (!$pg_cache or $afficher_page_db or !function_exists('file_get_contents'))
	{ // la $page n'est pas en cache, on regarde dans la db
		$sql = "SELECT numpage, statut, lastmodif, pseudo, lastmodifby FROM ".PREFIXE_TABLES."pagessections as a, ".PREFIXE_TABLES."auteurs as b WHERE a.lastmodifby = b.num AND a.page = '$page'";
		if ($res = send_sql($db, $sql))
		{
			$lapageexiste = mysql_num_rows($res);
			$afficher_page_db = false;

			if ($lapageexiste == 1)
			{ // La $page est dans la db
				$lapage = mysql_fetch_assoc($res);
				$statut_page = $lapage['statut'];
				if ($statut_page == 2)
				{ // la $page est publiée mais pas en cache
					$afficher_pg_db = true;
					if ($user['niveau']['numniveau'] > 3 or $user['assistantwebmaster'] == 1)
					{
?>
<div id="menu_outils_page"> <a href="index.php?page=pagesection&amp;do=retirer&amp;num=<?php echo $lapage['numpage']; ?>" class="rmqbleu" title="D&eacute;sactiver la page pour l'&eacute;diter"><img src="templates/default/images/logoff.png" width="12" height="12" border="0" align="top" alt="éditer" /></a> 
  <a href="backupnewpage.php?numpage=<?php echo $lapage['numpage']; ?>" title="Sauvegarder le contenu de la page"><img src="templates/default/images/bas.png" width="12" height="12" border="0" align="top" alt="sauvegarder" /></a> 
  <a href="affpage.php?numpage=<?php echo $lapage['numpage']; ?>" target="_blank" title="Afficher la source de la page"><img src="templates/default/images/fiche.png" border="0" width="18" height="12" align="top" alt="source" /></a> 
</div>
<?php
  					}
				}
				else if ($statut_page == 1 and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
				{ // la $page est en cours d'édition et l'utilisateur peut la modifier
					$afficher_pg_db = true;
					$lien_edition = (@file_exists('cache/'.$page.'.cache')) ? 'index.php?page=pagesection&amp;do=editer&amp;num='.$lapage['numpage'].'" onclick="alert(\'Les visiteurs continueront &agrave; voir l\\\'ancienne version de la page en cache jusqu\\\'&agrave; la prochaine publication.\');"' : 'index.php?page=pagesection&amp;do=editer&amp;num='.$lapage['numpage'];
?>
<div class="info_pgsection"> 
  <p align="center" class="rmqbleu">Page en cours d'&eacute;dition - Affichage 
	provisoire</p>
  <p class="petit">La page n'est pas encore visible pour les visiteurs du 
	portail (except&eacute; pour tous les animateurs).<br />
	Tu peux la modifier ou la laisser telle quelle.</p>
  <p align="center"> <a href="<?php echo $lien_edition; ?>" class="bouton" tabindex="1">Editer 
	la page</a> <a href="index.php?page=pagesection&amp;do=publier&amp;num=<?php echo $lapage['numpage']; ?>" class="bouton" tabindex="2">Publier 
	la page</a> </p>
<p align="center"><span class="petit">Tu peux <a href="backupnewpage.php?numpage=<?php echo $lapage['numpage']; ?>" tabindex="3">enregistrer 
  la page sur ton disque dur</a>. Cela permettra de <br />
  reconstituer le portail rapidement en cas de probl&egrave;me</span></p>
  <div class="petit" align="right">Derni&egrave;re modification par : <?php echo $lapage['pseudo'].' '; if ($lapage['lastmodif'] != '0000-00-00') {echo 'le '.date_ymd_dmy($lapage['lastmodif'], 'enlettres');}?></div>
</div>
<?php
				}
				else if ($statut_page == 0 and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
				{ // la $page est vide, l'utilisateur peut la remplir
					$afficher_pg_db = false;
?>
<div class="info_pgsection"> 
  <p align="center" class="rmqbleu">Cette page est encore vide</p>
  <p class="petit">Tu peux participer &agrave; sa r&eacute;daction  cliquant sur le bouton ci-dessous.</p>
  <p class="petit">Une fois la page remplie,  publie-la pour la 
	rendre accessible &agrave; tous les visiteurs.</p>
  <p align="center"> <a href="index.php?page=pagesection&amp;do=editer&amp;num=<?php echo $lapage['numpage']; ?>" class="bouton" tabindex="1">Editer 
	la page</a> </p>
</div>
<?php
				}
				else
				{ // la $page est en cours d'édition mais l'utilisateur ne peut pas la modifier
					$afficher_pg_db = false;
					include('404.php'); // on envoie la 404
				}
			}
			else if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
			{ // La $page n'existe pas du tout
				if  (ereg("^[-a-z0-9_]{1,20}$", $page))
				{ // l'utilisateur peut créer la $page
?>
<div class="info_pgsection"> 
  <p align="center" class="rmqbleu">Cette page n'existe pas encore</p>
  <p class="petit">A l'heure actuelle, le lien sur lequel tu as cliqu&eacute; 
	m&egrave;ne &agrave; une page d'erreur. Ton statut sur le portail t'autorise 
	&agrave; cr&eacute;er cette page. </p>
  <p class="petit">Cr&eacute;e la page en cliquant sur le lien &quot;Cr&eacute;er 
	la page&quot; ci-dessous.</p>
  <p align="center"> <a href="index.php?page=pagesection&amp;do=creer&amp;nompage=<?php echo $page; ?>&amp;nivcible=<?php echo $niv; ?>" class="bouton" tabindex="1">Cr&eacute;er 
	la page</a> </p>
  <p align="center" class="petitbleu">Si le lien sur lequel tu as cliqu&eacute; 
	&eacute;tait cens&eacute; t'afficher une autre page, il est incorrect. 
	<br />
	N'h&eacute;site pas &agrave; le corriger ou &agrave; le signaler au webmaster. 
  </p>
</div>
<?php
				}
				else
				{ // la $page n'existe pas et le format de page n'est pas autorisé
					include('404.php');
?>
<div class="msg">
<p align="center" class="rmq">Le format du nom de la page ne te permet pas 
  de cr&eacute;er une page &agrave; cette adresse.</p>
</div>
    <?php
				}
			}
			else
			{ // la $page n'existe pas et l'utilisateur ne peut pas la créer
				$afficher_pg_db = false;
				include('404.php'); // on envoie la 404
			}
			if ($afficher_pg_db)
			{ // Si la page doit être affichée depuis la db, on l'affiche
				$sql = "SELECT titre, contenupage, format, statut FROM ".PREFIXE_TABLES."pagessections WHERE numpage = '$lapage[numpage]'";
				$res = send_sql($db, $sql);
				$page_a_afficher = mysql_fetch_assoc($res);
				if (!empty($page_a_afficher['titre']))
				{ // le titre de la $page est défini
?>
<h1><?php echo makehtml($page_a_afficher['titre'], 'html'); ?></h1>
<?php
				}
				if ($page_a_afficher['format'] == 'html')
				{ // la $page est au format html
					echo makehtml($page_a_afficher['contenupage'], 'html');
				}
				else
				{ // la $page est au format texte (bbcodes + images)
					echo makehtml($page_a_afficher['contenupage'], 'ibbcode');
				}
				if ($page_a_afficher['statut'] == 2 and $user['niveau']['numniveau'] > 2)
				{
?>
<div class="msg">
<p align="center" class="petit">La page pourrait &ecirc;tre affich&eacute;e plus rapidement si elle &eacute;tait mise en cache.</p>
<?php
					if ($user['niveau']['numniveau'] < 5)
					{
?>
<p class="petit" align="center">Merci de le signaler au webmaster.</p>
<?php
					}
					if (!function_exists('file_get_contents'))
					{
?>
<p class="petit" align="center">La version de php sur le serveur (<?php echo PHP_VERSION; ?>) commence &agrave; dater un peu,<br />
ou la fonction <span title="disponible depuis php 4.3.0">file_get_contents()</span> a &eacute;t&eacute; d&eacute;sactiv&eacute;e sur le serveur.</p>
<?php
					}
?>
</div>
<?php
				}
				echo "\n";
			}
		}
		else
		{ // pas de connexion à la db
			include('404.php'); // on envoie la 404
		}
	} // fin !$pg_cache or $afficher_pg_db
} /////////////////////////////////////////////////////////////////////////

// Lien vers le profil de l'auteur de la page
$lien_user = ($site['url_rewriting_actif'] == 1) ? 'membre'.$lapage['lastmodifby'].'.htm' : 'index.php?page=profil_user&amp;user='.$lapage['lastmodifby'];
?>
<div id="footer">
<span class="web">Webmaster : <?php echo hidemail($site['mailwebmaster'], '', $site['webmaster']); ?></span>
<span class="maj"><?php 
	// C'est la date de modif dans la db qui a priorité (stockée dans $lapage)
	$lapage['lastmodif'] = (empty($lapage['lastmodif']) and isset($last_modif_page)) ? $last_modif_page : $lapage['lastmodif'];
	if (!empty($lapage['lastmodif'])) 
	{
		echo 'Page mise &agrave; jour le '.date_ymd_dmy($lapage['lastmodif'], 'enlettres');
		echo (!empty($lapage['pseudo'])) ? ' par <a href="'.$lien_user.'" target="_blank" title="Voir son profil">'.$lapage['pseudo'].'</a>' : '';
	}
	else 
	{
		echo 'Site mis &agrave; jour le '.$site['maj'];
	} ?></span>
<span class="divers"><a href="index.php?page=aide">A propos du portail</a> - <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'avertissement.htm' : 'index.php?page=avertissement'; ?>">&copy; 
Avertissement</a> - <a href="http://www.scoutwebportail.org/" target="_blank">Scout Web Portail</a> <?php echo ($site['show_version'] == 1) ? 'v '.$site['version_portail'] : ''; ?></span>
<span class="top"><a href="#sommetpage" title="Retour au sommet de la page">Haut de la page</a></span>
</div><?php /* fin div footer */ ?>
</div><?php /* fin div corps */ ?>
<div id="webmaster_data"> 
<?php
$e = explode(' ', microtime());
$tempspage = round(($e[0] + $e[1] - $s[0] - $s[1]), 2);
echo '<p style="color: #999999;">Page g&eacute;n&eacute;r&eacute;e en '.$tempspage.' secondes ('.$requetes.' requ&ecirc;tes)</p>';
if (defined('LOCAL_SITE')) 
{
	echo '<p>'.$txtrequetes.'</p>';
}
?>
</div><?php /* fin div webmaster_data */ ?>
</div><?php /* fin div index */ ?>
</body>
</html>
<?php
if ($user['niveau']['numniveau'] > 0)
{
	$sql = "UPDATE ".PREFIXE_TABLES."auteurs SET pagesvues = pagesvues + 1 WHERE num = $user[num]";
	send_sql($db, $sql);
}
else
{
	$sql = "UPDATE ".PREFIXE_TABLES."config SET valeur = valeur + 1 WHERE champ = 'pagesvues'";
	send_sql($db, $sql);
}
?>