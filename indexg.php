<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* indexg.php v 1.1 - Page affichée à l'arrivée de l'utilisateur sur le portail
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
*	Chargement fonctions avancées ici plutôt que dans fonc.php à chaque fois
*	Déplacement des div de la page et adaptation des noms de classes
*/

include_once('prv/fonc_moteurs.php'); // chargement fonctions avancées du portail

if (!defined('IN_SITE'))
{
	exit;
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Accueil du site</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
}
?>
<div id="indexg_colonne_gauche">
<?php 
if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
{
?>
  <div id="menu_outils_page" align="right">
  <a href="index.php?page=edito" title="Mettre le mot d'accueil &agrave; jour"><img src="templates/default/images/autres.png" border="0" alt="Mettre l'édito à jour" /></a> 
  </div>
<?php
}
?>
<?php
// Affichage de l'éditorial du site
echo ($site['format_edito'] == 1) ? makehtml($site['edito'], 'html') : makehtml($site['edito'], 'ibbcode');

// Affichage du dernier commentaire de la galerie photo
aff_last_comment();

if ($user == 0)
{
?>
<div id="deviens_membre"><h2>Deviens membre du site !</h2>
<p align="center">En t'inscrivant sur le portail 
tu pourras commenter les photos, <br />
participer au forum et bien d'autres choses encore.<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>"><br />
  Je m'inscris !</a></p>
</div>
<?php
}
?>
<?php
	if ($user == 0 and ENVOI_MAILS_ACTIF)
	{
?>
<div id="abonnement_newsletter">
<h2>Newsletter</h2>
<p align="center">Suis l'actualit&eacute; de l'unit&eacute; 
par mail, <a href="<?php echo ($site['url_rewriting_actif']) ? 'mailing_liste.htm' : 'index.php?page=mailing_liste'; ?>">abonne-toi &agrave; notre lettre d'information</a>.</p>
</div>
<?php
	}
?>
</div>
<div id="indexg_colonne_droite">
      <?php
	// affichage dernières news
	// nombre de news à afficher
	$nbre_dernieres_news = (isset($site['nbre_dernieres_news'])) ? $site['nbre_dernieres_news'] : 0;
	$sql = 'SELECT * FROM '.PREFIXE_TABLES.'news, '.PREFIXE_TABLES.'auteurs WHERE news_bannie != \'1\' AND auteur_news = num ORDER BY datecreation DESC LIMIT '.$nbre_dernieres_news;
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) > 0)
	{
?>
<div id="dernieres_news">
<h2>Notre actualit&eacute;
<?php
		if ($user['niveau']['numniveau'] > 2)
		{
?>
<a href="index.php?page=gestion_news" title="Ajouter des News sur le portail"><img src="templates/default/images/plus.png" width="12" height="12" border="0" alt="Ajouter des News" align="middle" /></a> 
<?php
		}
?>
<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>" title="Voir toutes les News"><img src="templates/default/images/go.png" width="12" height="12" alt="Voir les News" border="0" align="middle" /></a> 
<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'rssnews.htm' : 'index.php?page=rssnews'; ?>" title="Les News du site sur ton ordinateur ou ton site web !"><img src="img/rss/rss.gif" alt="" width="27" height="15" border="0" align="middle" /></a> 
</h2>
<?php
		$hr_news_done = false;
		$nombre_news = 0;
		while ($ligne = mysql_fetch_assoc($res))
		{
			$nombre_news++;
			$auteur_news = $ligne['pseudo'];
			$numauteur_news = $ligne['num'];
			$texte_news = $ligne['texte_news'];
			$datecreation_news = date_ymd_dmy($ligne['datecreation'], 'jourmois');
			$first_news = ($nombre_news == 1) ? 'first_news' : 'item_news';
		?>
            <div class="<?php echo $first_news; ?>">
			<h3><?php echo (!empty($ligne['titre_news'])) ? $ligne['titre_news'] : 'Actu du '.$datecreation_news; ?></h3>
			<p class="auteur_news"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'membre'.$numauteur_news.'.htm' : 'index.php?page=profil_user&amp;user='.$numauteur_news; ?>" class="lienmort" title="Voir son profil"><?php echo $auteur_news; ?></a>, 
              le <span class="<?php echo ($hr_news_done) ? '' : 'rmq'; ?>"><?php echo $datecreation_news; ?></span></p>
            <?php
			$longueur_maxi_news = 500;
			if (strlen($texte_news) > $longueur_maxi_news)
			{
				$texte_news = substr_replace($texte_news, ' ...', $longueur_maxi_news);
			}
			$texte_news = makehtml($texte_news);
			if (strlen($texte_news) > $longueur_maxi_news)
			{
				$lien_news = ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news';
				$texte_news .= '<span class="petitbleu"> [<a href="'.$lien_news.'">la suite</a>] </span>';
			}
?>
<p><?php echo $texte_news; ?></p>
</div>
<?php
		}
?>
</div>
<br />
<?php
	}
	derniersmessagesforum('280');
?>
</div>
<?php
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>