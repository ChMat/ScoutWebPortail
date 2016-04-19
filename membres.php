<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* membres.php v 1.1.1 - Accueil des membres à leur connexion (Page Accueil Membres)
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
*	chargement fonctions avancées ici plutôt que dans fonc.php à chaque fois
*	ajout script de vérification de la dernière version de SWP publiée
* Modifications v 1.1.1
*	Ajout d'un avertissement si la gestion de l'unité est désactivée
*	bug 20/11 : Avertissement .htaccess manquant affiché aux membres du site
*	bug 12/12 : Correction bug fsockopen détection nouvelle version
*/

include_once('connex.php');
include_once('fonc.php');
include_once('prv/fonc_moteurs.php'); // chargement fonctions avancées du portail
if ($user['niveau']['numniveau'] > 0 and is_array($sections))
{ // affichage page si connecté et que les sections sont créées
?>
<?php
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Accueil membres</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
	}
?>
<div id="membres">
<?php
	// Determine l'affichage ou non de la zone d'Information
	$aff_zone_info = false;
	if ($user['newpw'] == '1')
	{ // l'utilisateur a un nouveau mot de passe
		$aff_zone_info = true;
	}
	if ($user['niveau']['numniveau'] > 0 and $user['majprofildone'] != 1)
	{ // l'utilisateur n'a pas encore rempli son profil
		$aff_zone_info = true;
	}
	if ($user['niveau']['numniveau'] > 2)
	{ // des membres attendent d'être reconnus comme animateur
		$nbre_membres_a_autoriser = membresaautoriser('nbr');
		if ($nbre_membres_a_autoriser > 0) $aff_zone_info = true;
	}
	if (!isset($_COOKIE[COOKIE_ID]))
	{ // l'utilisateur se connecte sans accepter les cookies
		$aff_zone_info = true;
	}
	// l'utilisateur attend d'avoir le statut d'animateur sur le portail
	if ($user['nivdemande'] > 0) $aff_zone_info = true;
	// On vérifie la dernière version publiée du portail
	$swp_new_version = false;
	if ($user['niveau']['numniveau'] == 5)
	{
		// le webmaster peut consulter l'état de mise à jour des mots de passe utilisateur
		if ($site['update_pw'] == 'en_cours') $aff_zone_info = true;
		// On vérifie si le fichier .htaccess est présent au cas où l'url-rewriting est activé
		$url_rewriting_ok = true;
		if ($site['url_rewriting_actif'] == 1 and !@file_exists('.htaccess')) 
		{
			$url_rewriting_ok = false;
			$aff_zone_info = true;
		}
		
		if ($can_check_version or !isset($_COOKIE['last_check_version']))
		{ // La temporisation entre les vérifications est écoulée (5 jours définis dans le cookie créé par index.php),
		  // On vérifie la dernière version de SWP publiée sur le site officiel
		  	$url_fichier_check_last_version = 'http://www.scoutwebportail.org/infos/last_version.txt';
			if ($f_new_version = @fopen($url_fichier_check_last_version, 'r'))
			{ // Essai via la fonction fopen()
				$last_version_swp = fread($f_new_version, 50);
				fclose($f_new_version);
			}
			else if (function_exists('fsockopen'))
			{ // fopen ne fonctionne pas (allow_url_fopen sur off), on essaie autrement
				include_once('prv/fonc_http.php');
				$r = new HTTPRequest($url_fichier_check_last_version);
				$last_version_swp = $r->DownloadToString();
			}
			else
			{
				$last_version_swp = '';
			}

			if (!empty($last_version_swp) and $last_version_swp != $site['version_portail'])
			{ // Une nouvelle version du portail est disponible
				$aff_zone_info = true;
				$swp_new_version = true;
?>
<script type="text/javascript" src="swp_version_cookie.php?last_version=<?php echo $last_version_swp; ?>"></script>
<?php
			}
			else if ($last_version_swp == $site['version_portail'])
			{
				// Rien de spécial, le portail est à jour
?>
<script type="text/javascript" src="swp_version_cookie.php"></script>
<?php
			}
			else
			{
				log_this('membres', 'Impossible de vérifier si une nouvelle version de SWP est sortie, nous t\'invitons &agrave; visiter le site du <a href="http://www.scoutwebportail.org/">Scout Web Portail</a> pour te tenir au courant.');
			}
		} // Fin de la vérification d'une nouvelle version du portail.
		if (!empty($_COOKIE['last_version']) and $_COOKIE['last_version'] != $site['version_portail'])
		{ // Une nouvelle version a été détectée précédemment
			$aff_zone_info = true;
			$swp_new_version = true;
		}
	}
	// fin détermination affichage zone info
	if ($aff_zone_info)
	{ // affichage zone info
?>
<div id="zone_infos">
<?php
		if ($user['niveau']['numniveau'] == 5 and !$url_rewriting_ok)
		{
		?>
<p><span class="rmq">Fichier manquant : .htaccess absent sur le serveur !</span><br />
L'url-rewriting est activ&eacute; sur le portail mais il ne peut pas fonctionner sans fichier .htaccess.<br />
D&eacute;sactive (puis r&eacute;active si n&eacute;cessaire) l'url-rewriting depuis la <a href="index.php?page=config_site&amp;categorie=general">configuration du portail</a>.</p>
            <?php
		}
		if ($swp_new_version)
		{
		?>
<p><span class="rmq">Une nouvelle  version du portail est disponible : version <?php echo (!empty($last_version_swp)) ? htmlentities($last_version_swp, ENT_QUOTES) : htmlentities($_COOKIE['last_version'], ENT_QUOTES); ?>.</span><br />
Tu peux la t&eacute;l&eacute;charger sur le site du <a href="http://www.scoutwebportail.org/">Scout Web Portail</a>.</p>
<?php
		}
		if ($user['newpw'] == '1')
		{
		?>
<p><span class="rmq">Un nouveau mot de passe t'a &eacute;t&eacute; 
  attribu&eacute;</span>, merci de le modifier en <a href="index.php?page=modifprofil&amp;newpw=1">cliquant 
  ici</a>.</p>
            <?php
		}
		if ($user['niveau']['numniveau'] > 0 and $user['majprofildone'] != 1)
		{
		?>
<p><span class="rmq">Tu n'as pas encore rempli ton profil</span>, 
  viens ajouter quelques infos &agrave; ton sujet pour personnaliser 
  un peu ton compte. <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'modifprofil.htm' : 'index.php?page=modifprofil'; ?>">Clique ici</a> pour 
  le remplir.</p>
<?php
		}
		if (($user['niveau']['numniveau'] == 5 or ($site['droits_anim_valide_statut'] == 1 and $user['niveau']['numniveau'] > 2)) and $nbre_membres_a_autoriser > 0)
		{
?>
<p><span class="rmq">Note :</span> Le statut de certains membres 
  n'a pas encore &eacute;t&eacute; approuvé sur le portail. <a href="index.php?page=activ">Clique 
  ici</a> pour examiner leur demande et, le cas échéant, l'approuver. 
</p>
<?php
		}
		if ($user['nivdemande'] > 0)
		{
?>
<p><span class="rmq">Note :</span> Ton statut '<?php echo $niveaux[$user['nivdemande']]['nomniveau']; ?>' 
  n'a pas encore &eacute;t&eacute; valid&eacute;, un peu de patience...<br />
  Si cela fait plus d'une semaine que tu t'es inscrit, envoie un mail 
  au <a href="mailto:<?php echo $site['mailwebmaster']; ?>">webmaster</a>. 
</p>
<?php
		}
		if (!isset($_COOKIE[COOKIE_ID]))
		{
?>
<p><span class="rmq">Ton navigateur n'accepte pas les cookies.</span> 
  <br />
  Par mesure de s&eacute;curit&eacute;, ton identification sera d&eacute;sactiv&eacute;e 
  automatiquement apr&egrave;s 30 minutes d'inactivit&eacute; sur 
  le portail.</p>
<?php
		}
		if ($user['niveau']['numniveau'] == 5 and $site['update_pw'] == 'en_cours')
		{
?>
<p><span class="rmq">Mise &agrave; jour du portail </span> 
  <br />
  <a href="index.php?page=etat_update_pw" title="Consulter la liste des membres">Certains
  utilisateurs</a> doivent encore se connecter avant que la
  mise &agrave; jour du portail ne soit enti&egrave;rement achev&eacute;e. Rien
  ne presse, ce message est juste l&agrave; pour t'en avertir. </p>
<?php
		}
?>
</div>
<?php
  	} // fin affichage zone infos
	if (($user['niveau']['numniveau'] > 2 and $site['showmsganimateurs'] == 1 and $site['messageanimateurs'] != '') or ($site['showmsgmembres'] == 1 and $site['message_membres'] != ''))
	{
?>
<div id="msg_membres">
<?php
		$m = false;
		if ($site['showmsgmembres'] == 1 and !empty($site['message_membres']))
		{
			echo makehtml($site['message_membres'], 'html');
		}
		if ($user['niveau']['numniveau'] > 2 and $site['showmsganimateurs'] == 1 and !empty($site['messageanimateurs']))
		{
			echo makehtml($site['messageanimateurs'], 'html');
		}
?>
</div>
<?php
	}
	if ($user['niveau']['numniveau'] == 5)
	{
?>
<div id="m_webmaster">
<h2>Zone Webmaster</h2>
<ul>
  <li> <a href="index.php?page=config_site" class="menumembres">Configuration du portail</a></li>
  <li>	  <a href="index.php?page=gestion_mb_site" class="menumembres"><img src="templates/default/images/listeusers.png" width="18" height="12" border="0" align="top" alt="" /> 
        G&eacute;rer les membres du portail</a></li>
  <li>	  <a href="index.php?page=mailing_liste" class="menumembres"><img src="templates/default/images/mail.png" width="18" height="12" border="0" align="top" alt="" /> 
        G&eacute;rer la mailing liste</a></li>
  <li>	  <a href="index.php?page=gestion_sections" class="menumembres"><img src="templates/default/images/gestion_sections.png" width="18" height="12" border="0" align="top" alt="" /> 
        G&eacute;rer les sections</a></li>
</ul>
<ul>
  <li><a href="index.php?page=reset_config" class="menumembres" title="Cette fonction renouvelle le fichier de configuration mis en cache.">Recharger 
	      la config</a></li>
  <li><a href="del_files.php" class="menumembres" target="_blank">Suppression de photos du portail</a></li>
  <li><a href="index.php?page=lastbannedcomments" class="menumembres">Commentaires 
	    bannis</a></li>
  <li><a href="index.php?page=listevisites" class="menumembres">Log des 
	    visites</a></li>
  <li><a href="index.php?page=lastactions" class="menumembres">Log des actions visiteurs</a></li>
</ul>
<p style="clear:left;" class="petit">&nbsp;</p>
</div>
<?php
	}
	if ($user['assistantwebmaster'] == 1)
	{
?>
<div id="m_cowebmaster">
<h2>Zone co-webmaster</h2>
 
<p>- <a href="index.php?page=pagesection" class="menumembres"><strong>G&eacute;rer 
les pages du portail</strong></a><br />
- <a href="index.php?page=gestion_menus" class="menumembres">G&eacute;rer 
les menus du portail</a><br />
- <a href="index.php?page=gestion_galerie" class="menumembres">G&eacute;rer 
les albums photos </a></p>
<p>Tu as &eacute;t&eacute; reconnu comme co-webmaster pour le portail. Tu peux modifier de nombreuses pages sur le portail.</p></td>
</div>
<?php
	}
?>
<?php
	if ($user['niveau']['numniveau'] > 2)
	{
?>
<div id="m_gestion_unite" class="cadre_g">
<h2>Gestion de l'Unit&eacute;</h2>
<?php
		if ($site['gestion_membres_actif'] == 1)
		{
?>
<p><a href="index.php?page=gestion_unite" class="lien">G&eacute;rer 
  l'Unit&eacute;, les Sections, les membres, les anciens, ...</a></p>
<?php
			$liste_pages = "page = 'newad' OR page = 'newmb' OR page = 'newancien' OR page = 'modiffamille' OR page = 'modifmembre' OR page = 'modifancien' OR page = 'passage' OR page = 'passageanciens' OR page = 'gestioncotisations'";
			$modif_mb_recentes = gestion_unite_lastmodif($liste_pages);
			if ($modif_mb_recentes['nbre'] > 0)
			{
				$pl_mot = ($modif_mb_recentes['nbre'] > 1) ? 's' : '';
?>
<p class="petitbleu" title="Modifications apport&eacute;es depuis une semaine">Modifications 
r&eacute;centes : <?php echo '<a href="index.php?page=lastmodif">'.$modif_mb_recentes['nbre'].' action'.$pl_mot.'</a>'; ?><br />
(la derni&egrave;re il y a <?php echo temps_ecoule($modif_mb_recentes['last']); ?>).</p>
<?php
			}
			else
			{
?>
<p class="petitbleu" title="Modifications apport&eacute;es depuis une semaine">Aucune modification n'a eu lieu r&eacute;cemment.</p>
<?php
			}
		}
		else
		{
?>			  
<p class="petit">La gestion des membres de l'Unit&eacute; n'est pas activ&eacute;e.</p>
<?php
		}
?>			  

</div>
<?php
	}
?>
<?php
	if ($user['niveau']['numniveau'] > 2)
	{
?>
<div id="m_animateur" class="cadre_d">
<h2>Zone Animateur</h2>
<p> &nbsp;-&nbsp;<a href="index.php?page=pagesrestreintes" class="menumembres">Pages 
restreintes aux Staffs</a><br /> &nbsp;-&nbsp;<a href="index.php?page=gestion_news" class="menumembres">Ajout 
de news</a><br /> &nbsp;-&nbsp;<a href="index.php?page=pagesection" class="menumembres">G&eacute;rer 
les pages du portail</a><br /> &nbsp;-&nbsp;<a href="index.php?page=gestion_menus" class="menumembres">G&eacute;rer 
les menus du portail</a><br /> &nbsp;-&nbsp;<a href="index.php?page=gestion_galerie" class="menumembres">G&eacute;rer 
les albums photos</a><br /> 
<?php
		if ($user['niveau']['numniveau'] > 3)
		{
?>
&nbsp;-&nbsp;<a href="index.php?page=edito" class="menumembres">Editorial du site</a> 
<?php
		}
?>
</p>  
</div>
<?php
	} // fin numniveau > 2
?>
<?php
?>
<?php
	if ($user['niveau']['numniveau'] > 2)
	{
?>
<div id="m_pages_restreintes">
<h2>Pages restreinte r&eacute;centes</h2>
<?php
		// nbre de pages restreintes à afficher
		$nbre_pgres_aff = 3;
		$calcul_pgres = $nbre_pgres_aff + 1;
		$sql = "SELECT numpage, titre, pseudo, datecreation FROM ".PREFIXE_TABLES."pagesrestreintes, ".PREFIXE_TABLES."auteurs WHERE auteur = num AND pagebannie != '1' ORDER BY datecreation DESC LIMIT $calcul_pgres";
		if ($res = send_sql($db, $sql))
		{
			$nbrepagesres = mysql_num_rows($res);
			if ($nbrepagesres > 0)
			{
				if ($nbrepagesres > $nbre_pgres_aff) {$max = $nbre_pgres_aff;} else {$max = $nbrepagesres;}
?>
<table width="100%" cellpadding="2" cellspacing="0">
<?php
				for ($i = 1; $i <= $max; $i++)
				{
					$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
					$restreint = mysql_fetch_assoc($res);
					$lien_couleur = ($i == 1) ? $couleur.' rmqbleu' : $couleur;
?>
  <tr class="<?php echo $couleur; ?>"> 
	<td class="petit"><?php echo '<a href="index.php?page=pagesrestreintes&amp;numero='.$restreint['numpage'].'">'.$restreint['titre'].'</a>'; ?></td>
	<td class="petit"><?php echo $restreint['pseudo']; ?></td>
	<td class="petit" align="right"><?php echo date_ymd_dmy($restreint['datecreation'], 'jourmois'); ?></td>
  </tr>
  <?php
				}
?>
  <tr class="td-f3"> 
	<td colspan="3" align="right"> <a href="index.php?page=pagesrestreintes&do=ecrire" class="lien" title="Ecrire une page restreinte"><span class="petitbleu"><img src="templates/default/images/plus.png" alt="Ecrire une page restreinte" width="12" height="12" border="0" /></span></a> 
<?php
				if ($nbrepagesres > $max)
				{
?>
	<a href="index.php?page=pagesrestreintes" class="lien" title="Voir toutes les pages restreintes"><img src="templates/default/images/go.png" width="12" height="12" border="0" alt="Voir toutes les pages restreintes" /></a> 
<?php
				}
?>
	</td>
  </tr>
</table>
<?php
			}
			else
			{
?>
<p class="petitbleu">Il n'y a aucune page restreinte pour le moment.</p>
<p align="right" class="petit"> <a href="index.php?page=pagesrestreintes" class="lienmort" title="Ecrire une page restreinte"><img src="templates/default/images/plus.png" alt="Ecrire une page restreinte" width="12" height="12" border="0" align="top" /> 
Ecrire une page restreinte</a></p>
<?php
			}
		}
?>
</div>
<?php
	}
?>
<div id="m_tally"> 
<h2>Les derniers articles du Tally</h2>
<?php
	$sql = "SELECT numarticle, article_titre, pseudo, article_datecreation FROM ".PREFIXE_TABLES."articles, ".PREFIXE_TABLES."auteurs WHERE article_auteur = num AND article_banni != '1' ORDER BY article_datecreation DESC LIMIT 10";
	if ($res = send_sql($db, $sql))
	{
		$nbrepagestally = mysql_num_rows($res);
		if ($nbrepagestally > 0)
		{
			$max = ($nbrepagestally > 5) ? 5 : $nbrepagestally;
?>
            <table width="100%" cellpadding="2" cellspacing="0">
              <?php
			for ($i = 1; $i <= $max; $i++)
			{
				$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
				$tally = mysql_fetch_assoc($res);
?>
              <tr class="<?php echo $couleur; ?>"> 
                <td width="65%"><span class="petit"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'tally'.$tally['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$tally['numarticle']; ?>"><?php echo $tally['article_titre']; ?></a></span></td>
                <td align="center"><span class="petit"><?php echo $tally['pseudo']; ?></span></td>
                <td align="right"><span class="petit"><?php echo date_ymd_dmy($tally['article_datecreation'], 'jourmois'); ?></span></td>
              </tr>
<?php
			}
?>
              <tr class="td-f3"> 
                <td colspan="3" align="right"> <a href="index.php?page=tally&do=ecrire" class="lien" title="Ajouter un article au Tally"><img src="templates/default/images/plus.png" width="12" height="12" border="0" alt="Ajouter un article au Tally" /></a> 
<?php
			if ($nbrepagestally > $max)
			{
?>
                  <a href="index.php?page=tally" class="lien" title="Voir le Tally"><img src="templates/default/images/go.png" width="12" height="12" border="0" alt="Voir le Tally" /></a> 
<?php
			}
?>
                </td>
              </tr>
            </table>
<?php
		}
		else
		{
?>
<p class="petitbleu">Le Tally est vide pour le moment</p>
<p align="right" class="petit"> <a href="index.php?page=tally&do=ecrire" class="lienmort"><img src="templates/default/images/plus.png" alt="Ajouter un article" width="12" height="12" border="0" align="top" /> 
  Ecrire un article</a></p> 
<?php
		}
	}
?>
</div>
</div>
<?php
} // fin affichage page si connecté
else if ($user['niveau']['numniveau'] > 0 and !is_array($sections))
{ // aucune section n'existe dans la db, 
  // c'est probablement la première connexion du webmaster après l'installation du portail
	include('premier_demarrage.php');
}
else
{
	include('404.php');
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