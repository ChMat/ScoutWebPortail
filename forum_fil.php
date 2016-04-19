<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum_fil.php v 1.1.1 - Affichage et gestion des discussions du forum
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
* Modifications v 1.1.1
*	Nouveau fichier ajouté dans swp v 1.1
*	bug 07/11 : pagination page suivante/précédente incorrecte sans url-rewriting
*	pagination : ajout d'un lien direct vers la dernière page de la discussion
*	bug 08/11 : mise à jour dernier msg_id des forums après déplacement de discussion
*	ajout du formulaire de connexion
*	pour poster une réponse, l'utilisateur doit se rendre sur la dernière page de la discussion
*/

include_once('connex.php');
include_once('fonc.php');
include_once('forum_fonctions.php');
// Paramètres du forum :
// lien de base pour tous les liens :

if ($site['forum_actif'] != 1 and $user['niveau']['numniveau'] < 5)
{
	include('module_desactive.php');
}
else
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Forum</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<div id="forum">
<h1>Forum de Discussions</h1>
<?php
	$can_read = false;
	$can_post = false;
	$can_moderate = false;
	$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';

	// la vérification se fait ici pour l'affichage du formulaire ainsi qu'à l'envoi de celui-ci
	$fil_id = $msg_id = 0;
	// Affichage d'une discussion
	if (is_numeric($_GET['fil']))
	{
		$fil_id = $_GET['fil'];
	}
	else if (is_numeric($_POST['fil_id']))
	{
		$fil_id = $_POST['fil_id'];
	}
	else if (is_numeric($_GET['fil_id']))
	{
		$fil_id = $_GET['fil_id'];
	}
	// On chipote à un message
	if (is_numeric($_GET['msg_id']))
	{
		$msg_id = $_GET['msg_id'];
	}
	else if (is_numeric($_POST['msg_id']))
	{
		$msg_id = $_POST['msg_id'];
	}
	// On vérifie les autorisations
	if ($fil_id > 0)
	{ // l'utilisateur affiche une discussion
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums as f, ".PREFIXE_TABLES."forum_fils as d WHERE d.forum_id = f.forum_id AND d.fil_id = '".$fil_id."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'lire') and limite_acces_fil($forum, 'lire'))
			{
				$can_lire = true;
				$can_post = (limite_acces_forum($forum, 'ecrire') and limite_acces_fil($forum, 'ecrire')) ? true : false;
				$can_moderate = ($can_post and is_moderateur($forum['forum_moderation'], $forum['fil_auteur'])) ? true : false;
			}
			else if ($forum['fil_statut'] == 0)
			{
				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
				$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a>
   - <span class="titre"><?php echo $forum['fil_titre']; ?></span></h2>
<div class="msg">
<p align="center" class="rmq">Cette discussion est ferm&eacute;e.</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
			}
			else
			{
				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a></h2>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas acc&egrave;s &agrave; cette discussion !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
			}
		}
		else
		{
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a></h2>
<div class="msg">
<p align="center" class="rmq">Ce forum ou cette discussion n'existe pas !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
		}
	}
	// Fin des vérifications d'accès
	if (empty($_GET['do']) and empty($_POST['do']) and $can_lire)
	{ // on affiche le fil
		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
		$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a>
   - <span class="titre"><?php echo $forum['fil_titre']; ?></span>
</h2>
<p class="f_recherche"><a href="index.php?page=forum_search&amp;forum_id=<?php echo $forum['forum_id']; ?>">Rechercher dans le forum</a></p>
<?php
		// On met à jour le compteur de vues
		$sql = 'UPDATE '.PREFIXE_TABLES.'forum_fils SET fil_nbvues = fil_nbvues + 1 WHERE fil_id = \''.$fil_id.'\' LIMIT 1';
		send_sql($db, $sql);
		
		if ($user['niveau']['numniveau'] == 5)
		{ // Pour le webmaster, on récupère le nombre de messages à afficher exactement (en incluant les posts bannis)
			$sql = "SELECT count(*) as nbre_msg FROM ".PREFIXE_TABLES."forum_msg WHERE fil_id = '".$fil_id."'";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			$forum['fil_nbmsg'] = $ligne['nbre_msg'];
		}
		
		$par = $site['forum_nbmsg_par_page'];
		// nombre de pages
		$nb_pg = ceil($forum['fil_nbmsg'] / $par);
		// page en cours
		$pg = (is_numeric($_GET['pg']) and $_GET['pg'] >= 0 and $_GET['pg'] <= $nb_pg) ? $_GET['pg'] : 0;
		// paramètre de la requête sql
		$debut = ($pg > 0) ? (($pg * $par) + 0) : 0;

		// Pagination (ce code est cloné en bas de page)
		if ($nb_pg > 1) 
		{
?><p class="f_pagination">
Aller &agrave; la page <?php
			if ($pg > 0)
			{ // lien vers la page précédente
				$lien_page_pcdte = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($pg - 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($pg - 1);
?><a href="<?php echo $lien_page_pcdte; ?>">Pr&eacute;c&eacute;dente</a> 
<?php
			}
			if (($pg <= 4 and $nb_pg <= 5) or ($pg < 4 and $nb_pg > 5))
			{ // pages 0 à 4
				$max = ($nb_pg >= 5) ? 5 : $nb_pg;
				for ($i = 0; $i < $max; $i++)
				{
					$num_pg = $i + 1;
					if ($i != $pg)
					{
						$lien_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.$i.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.$i;
?><a href="<?php echo $lien_page; ?>"<?php echo ($pg == $i) ? ' class="rmq"' : ''; ?>><?php echo $num_pg; ?></a>
<?php
					}
					else
					{ // page en cours
						echo $num_pg;
					}
					echo ($i < $max - 1) ? ', ' : ''; // séparateur
				}
			}
			else
			{ // autres pages x-2 x-1 x x+1 x+2
				$lien_page_0 = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id;
?><a href="<?php echo $lien_page_0; ?>">1</a>, 
<?php
				if ($pg == $nb_pg - 1) {$max = $pg;}
				else if ($nb_pg == $pg + 2) {$max = $pg + 1;}
				else {$max = $pg + 2;}
				for ($j = ($pg - 2); $j <= $max; $j++)
				{
					$num_pg = $j + 1;
					echo ($j == $pg - 2 and $pg >= 4) ? '..., ' : ''; 
					if ($j >= 0 and $j != $pg)
					{
						$lien_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.$j.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.$j;
?><a href="<?php echo $lien_page; ?>"<?php echo ($pg == $j) ? ' class="rmq"' : ''; ?>><?php echo $num_pg; ?></a>
<?php
					}
					else if ($j == $pg)
					{ // page en cours
						echo $num_pg;
					}
					echo ($j < $max) ? ', ' : ' '; // séparateur
				}
			}
			if ($pg < $nb_pg - 3 and $nb_pg > 5)
			{
				echo ($pg < $nb_pg - 4 and $nb_pg > 5) ? ', ...' : '';
				$lien_last_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($nb_pg - 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($nb_pg - 1);
?>, <a href="<?php echo $lien_last_page; ?>"><?php echo $nb_pg; ?></a>
<?php
			}
			if ($pg < $nb_pg - 1)
			{ // lien page suivante
				$lien_page_svte = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($pg + 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($pg + 1);
?> <a href="<?php echo $lien_page_svte; ?>">Suivante</a>
<?php
			}
?></p>
<?php
		} // fin pagination

		// On affiche le lien pour poster sur le forum
		if ($can_post)
		{
?>
<div class="f_poster">
<p><a href="index.php?page=forum_post&amp;f=<?php echo $forum['forum_id']; ?>"><img src="templates/default/forum/nouveau.png" alt="Nouvelle discussion" width="88" height="20" /></a>
<?php
			if ($forum['fil_statut'] != 0)
			{ // La discussion n'est pas fermée, on peut y poster
?>
 <a href="index.php?page=forum_post&amp;fil_id=<?php echo $fil_id; ?>"><img src="templates/default/forum/repondre.png" alt="R&eacute;pondre" width="88" height="20" /></a>
<?php
			}
?>
</p>
</div>
<?php
		}

		// Le webmaster peut tout voir
		if ($user['niveau']['numniveau'] == 5)
		{
			$banni = '';
		}
		else
		{
			$banni = 'AND m.msg_statut != \'1\'';
		}
		// Les données du fil sont déjà dans la variable $forum
		// On récupère les messages
		$sql = "SELECT prenom, nom, avatar, m.msg_id, m.msg_auteur, m.msg_statut, 
		m.msg_moderateur, a.pseudo as auteur_pseudo, m.msg_titre, t.msg_txt, m.msg_date, a.niveau
		FROM 
		".PREFIXE_TABLES."forum_msg as m, 
		".PREFIXE_TABLES."forum_msg_txt as t, 
		".PREFIXE_TABLES."auteurs as a 
		WHERE m.fil_id = '".$fil_id."' AND m.msg_id = t.msg_id AND m.msg_auteur = a.num $banni 
		ORDER BY m.msg_id LIMIT $debut, $par";
		if ($res = send_sql($db, $sql))
		{
			// On met le nombre de messages et de pages à jour (pour le webmaster)
			$forum['fil_nbmsg'] = mysql_num_rows($res);

			if ($forum['fil_nbmsg'] > 0)
			{
	?>
<div class="fil">
<?php
				for ($i = 0; $i < $forum['fil_nbmsg']; $i++)
				{ // On parcourt les messages
					$post = mysql_fetch_assoc($res);
					if ($post['msg_statut'] == '0' or $user['niveau']['numniveau'] == 5)
					{
?> 
<div class="post<?php echo ($post['msg_statut'] == '1') ? '_banni' : ''; echo ($i == $forum['fil_nbmsg'] - 1) ? ' last' : ''; ?>" id="msg<?php echo $post['msg_id']; ?>">
  <h2><?php echo $post['msg_titre']; ?></h2>
<div class="infos_posteur">
  <p class="pseudo" title="<?php echo $post['prenom'].' '.$post['nom'];?>"><?php echo $post['auteur_pseudo'];?></p>
<?php
						if (!empty($post['avatar'])) 
						{ // on affiche l'avatar du posteur
							echo show_avatar($post['avatar']);
						} 
?>
 <p class="statut"><?php
						echo $niveaux[$post['niveau']]['nomniveau'].'<br />'; 
						if ($niveaux[$post['niveau']]['section_niveau'] > 0) 
						{
							echo $sections[$niveaux[$post['niveau']]['section_niveau']]['nomsection'];
						} 
?></p> 
</div>
<div class="message"><?php
						echo makehtml($post['msg_txt']);
						if ($post['msg_statut'] == 1)
						{ // le post a été banni (le post n'est visible que pour le webmaster)
?>
      <p class="modo">Mod&eacute;r&eacute; par <?php echo untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $post['msg_moderateur']); ?></p>
<?php
						}
						else if ($post['msg_moderateur'] > 0 and $post['msg_statut'] == 0)
						{ // le message a été édité par un modérateur qui l'a marqué comme une modération
?>
      <p class="modo">Edit&eacute; par <?php echo untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $post['msg_moderateur']); ?></p>
<?php
						}
?>
</div>
<div class="infos_post"><?php
						// on affiche un lien vers le profil du membre
						$lien_user = ($site['url_rewriting_actif'] == 1) ? 'membre'.$post['msg_auteur'].'.htm' : 'index.php?page=profil_user&amp;user='.$post['msg_auteur'];
?><a href="<?php echo $lien_user; ?>" target="_blank" title="Voir son profil"><img src="templates/default/images/user.png" width="18" height="12" alt="Voir son profil" align="top" border="0" /></a> <?php
						if (is_moderateur($forum['forum_moderation'], $forum['fil_auteur']) or ($user['num'] == $post['msg_auteur'] and temps_ecoule($post['msg_date'], 0, true) < 3600))
						{ // bouton pour éditer le message (modérateur ou auteur)
							$titre_lien_modif = (!is_moderateur($forum['forum_moderation'], $forum['fil_auteur'])) ? 'Modifier ce message dans l\'heure qui suit' : 'Modifier ce message';
							$lien_edit = 'index.php?page=forum_post&amp;msg_id='.$post['msg_id'];
?><a href="<?php echo $lien_edit; ?>" class="lien" title="<?php echo $titre_lien_modif; ?>"><img src="templates/default/images/autres.png" alt="Modifier" border="0" width="12" height="12" align="top" /></a><?php
						}
						if (is_moderateur($forum['forum_moderation'], $forum['fil_auteur']) and $post['msg_statut'] != '1')
						{ // bouton pour modérer le message
							$lien_moderation = 'index.php?page=forum_post&amp;do=mod&amp;msg_id='.$post['msg_id'].'&amp;fil_id='.$forum['fil_id'];
?> <a href="<?php echo $lien_moderation; ?>" class="lien" title="Supprimer ce message du forum"><img src="templates/default/images/supprimer.png" alt="Modérer" border="0" width="12" height="12" align="top" /></a><?php
						}
						if ($post['msg_statut'] == 1)
						{ // bouton pour supprimer définitivement le message (seul le webmaster le voit)
							$lien_suppr = 'index.php?page=forum_post&amp;do=suppr&amp;msg_id='.$post['msg_id'].'&amp;fil_id='.$forum['fil_id'];
?> <a href="<?php echo $lien_suppr; ?>" title="Supprimer d&eacute;finitivement ce message du forum"><img src="templates/default/images/supprimer.png" alt="Supprimer" border="0" width="12" height="12" align="top" /></a><?php
						}
?>
 Posté le <?php echo date_ymd_dmy($post['msg_date'], 'dateheure'); ?></div>
</div>
<?php
					}
				}
?>
</div>
<?php
			}
			else
			{ // la discussion ne contient aucun message
?>
<div class="msg">
<p align="center">Cette discussion ne contient aucun message !</p>
</div>
<?php			
			}
		}

		// Pagination
		if ($nb_pg > 1) 
		{
?><p class="f_pagination">
Aller &agrave; la page <?php
			if ($pg > 0)
			{ // lien vers la page précédente
				$lien_page_pcdte = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($pg - 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($pg - 1);
?><a href="<?php echo $lien_page_pcdte; ?>">Pr&eacute;c&eacute;dente</a> 
<?php
			}
			if (($pg <= 4 and $nb_pg <= 5) or ($pg < 4 and $nb_pg > 5))
			{ // pages 0 à 4
				$max = ($nb_pg >= 5) ? 5 : $nb_pg;
				for ($i = 0; $i < $max; $i++)
				{
					$num_pg = $i + 1;
					if ($i != $pg)
					{
						$lien_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.$i.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.$i;
?><a href="<?php echo $lien_page; ?>"<?php echo ($pg == $i) ? ' class="rmq"' : ''; ?>><?php echo $num_pg; ?></a>
<?php
					}
					else
					{ // page en cours
						echo $num_pg;
					}
					echo ($i < $max - 1) ? ', ' : ''; // séparateur
				}
			}
			else
			{ // autres pages x-2 x-1 x x+1 x+2
				$lien_page_0 = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id;
?><a href="<?php echo $lien_page_0; ?>">1</a>, 
<?php
				if ($pg == $nb_pg - 1) {$max = $pg;}
				else if ($nb_pg == $pg + 2) {$max = $pg + 1;}
				else {$max = $pg + 2;}
				for ($j = ($pg - 2); $j < ($max + 1); $j++)
				{
					$num_pg = $j + 1;
					echo ($j == $pg - 2 and $pg >= 4) ? '..., ' : ''; 
					if ($j >= 0 and $j != $pg)
					{
						$lien_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.$j.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.$j;
?><a href="<?php echo $lien_page; ?>"<?php echo ($pg == $j) ? ' class="rmq"' : ''; ?>><?php echo $num_pg; ?></a>
<?php
					}
					else if ($j == $pg)
					{ // page en cours
						echo $num_pg;
					}
					echo ($j < $max) ? ', ' : ' '; // séparateur
				}
			}
			if ($pg < $nb_pg - 3 and $nb_pg > 5)
			{
				echo ($pg < $nb_pg - 4 and $nb_pg > 5) ? ', ...' : '';
				$lien_last_page = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($nb_pg - 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($nb_pg - 1);
?>, <a href="<?php echo $lien_last_page; ?>"><?php echo $nb_pg; ?></a>
<?php
			}
			if ($pg < $nb_pg - 1)
			{ // lien page suivante
				$lien_page_svte = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($pg + 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($pg + 1);
?> <a href="<?php echo $lien_page_svte; ?>">Suivante</a>
<?php
			}
?></p>
<?php
		} // fin pagination

		if ($forum['fil_statut'] == 2)
		{
?>
<div class="msg">
<p align="center">Cette discussion est verrouill&eacute;e. Seuls les mod&eacute;rateurs peuvent encore intervenir.</p>
</div>
<?php
		}
		if ($forum['fil_statut'] == 0)
		{
?>
<div class="msg">
<p align="center">Cette discussion est ferm&eacute;e. Seul le webmaster peut encore la consulter. </p>
</div>
<?php
		}
		else if ($forum['forum_statut'] == 2)
		{
?>
<div class="msg">
<p align="center">Le forum est verrouill&eacute;. Seul le webmaster peut encore intervenir.</p>
</div>
<?php
		}
		else if ($forum['forum_statut'] == 3)
		{
?>
<div class="msg">
<p align="center">Le forum est verrouill&eacute;. Seuls les mod&eacute;rateurs peuvent encore intervenir.</p>
</div>
<?php
		}
?>
<div class="f_poster">
<?php

		// On affiche le lien pour poster sur le forum
		if ($can_post)
		{
?>
<p><a href="index.php?page=forum_post&amp;f=<?php echo $forum['forum_id']; ?>"><img src="templates/default/forum/nouveau.png" alt="Nouvelle discussion" width="88" height="20" /></a>

<?php
			if ($forum['fil_statut'] != 0 and $pg == $nb_pg - 1)
			{ // La discussion n'est pas fermée, on peut y poster (depuis la dernière page)
?>
 <a href="index.php?page=forum_post&amp;fil_id=<?php echo $fil_id; ?>"><img src="templates/default/forum/repondre.png" alt="R&eacute;pondre" width="88" height="20" /></a>
<?php
			}
?>
</p>
<?php
			if ($forum['fil_statut'] != 0 and $pg != $nb_pg - 1)
			{ // On n'est pas à la dernière page
?>
<div class="msg">
  <p align="center">Pour poster une r&eacute;ponse, rends-toi &agrave; la <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'_'.($nb_pg - 1).'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil_id.'&amp;pg='.($nb_pg - 1); ?>">derni&egrave;re page</a> de la discussion.</p>
</div>
<?php
			}
			
		}
		
		// On affiche les options de gestion de la discussion
		if ($can_moderate)
		{
?>
<p>
<?php
			if ($user['niveau']['numniveau'] == 5)
			{ // Seul le webmaster peut supprimer une discussion
?>
<a href="index.php?page=forum_fil&amp;do=supprimer&amp;fil_id=<?php echo $fil_id; ?>" title="Supprimer la discussion"><img src="templates/default/forum/supprimer.png" alt="Supprimer" width="20" height="20" /></a> 
<?php
			}
			if ($forum['fil_statut'] == 1)
			{ // La discussion est ouverte, on peut la fermer
?>
<a href="index.php?page=forum_fil&amp;do=fermer&amp;fil_id=<?php echo $fil_id; ?>" title="Fermer la discussion (devient invisible)"><img src="templates/default/forum/fermer.png" alt="Fermer (devient invisible)" width="20" height="20" /></a> 
<a href="index.php?page=forum_fil&amp;do=verrouiller&amp;fil_id=<?php echo $fil_id; ?>" title="Verrouiller la discussion (impossible de poster)"><img src="templates/default/forum/verrouiller.png" alt="Verrouiller (impossible de poster)" width="20" height="20" /></a> 
<?php
			}
			else if ($forum['fil_statut'] == 0 or $forum['fil_statut'] == 2)
			{ // On peut rouvrir la discussion
?>
<a href="index.php?page=forum_fil&amp;do=ouvrir&amp;fil_id=<?php echo $fil_id; ?>" title="Rouvrir la discussion"><img src="templates/default/forum/ouvrir.png" alt="Ouvrir" width="20" height="20" /></a> 
<?php
			}
?>
<a href="index.php?page=forum_fil&amp;do=deplacer&amp;fil_id=<?php echo $fil_id; ?>" title="Déplacer la discussion dans un autre forum"><img src="templates/default/forum/deplacer.png" alt="D&eacute;placer" width="20" height="20" /></a> 
</p>
<?php
		}
?>
<p class="petit"><a href="<?php /* liens forum défini en haut */ echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a>
   - <span class="titre"><?php echo $forum['fil_titre']; ?></span>
</p>
</div>
<div class="f_droits">
<p align="right"><?php
				// On informe l'utilisateur des paramètres du forum en cours
				$liste_statuts[0] = '<span class="rmq">Forum ferm&eacute;</span>';
				$liste_statuts[1] = ''; // le forum est ouvert
				$liste_statuts[2] = '<span class="rmq">Forum verrouill&eacute;</span>';
				$liste_statuts[3] = '<span class="rmq">Forum verrouill&eacute;</span>';
				echo $liste_statuts[$forum['forum_statut']];
				// Niveau d'accès du forum
				$liste_acces[0] = '<br />Forum en <strong>acc&egrave;s public</strong>';
				$liste_acces[1] = '<br />Niveau minimum : <strong>Membre du site</strong>';
				$liste_acces[2] = '<br />Niveau minimum : <strong>Membre de l\'unit&eacute;</strong>';
				$liste_acces[3] = '<br />Niveau minimum : <strong>Animateur</strong>';
				$liste_acces[4] = '<br />Niveau minimum : <strong>Animateur d\'unit&eacute;</strong>';
				$liste_acces[5] = '<br />Niveau minimum : <strong>Webmasters</strong>';
				echo $liste_acces[$forum['forum_acces_niv']];
				// Section cible du forum
				if ($forum['forum_acces_section'] > 0)
				{ // unité complète
					echo '<br />Unit&eacute; : <strong title="L\'unit&eacute; et toutes ses sections">'.$sections[$forum['forum_acces_section']]['nomsectionpt'].'</strong>';
				}
				else if ($forum['forum_acces_section'] < 0)
				{ // section uniquement
					echo '<br />Section : <strong>'.$sections[($forum['forum_acces_section'] * -1)]['nomsectionpt'].'</strong> uniquement';
				}
				// Qui sont les modérateurs du forum
				$liste_moderateurs[0] = 'le webmaster';
				$liste_moderateurs[1] = 'l\'auteur de chaque discussion';
				$liste_moderateurs[2] = 'l\'auteur de la discussion et les animateurs';
				$liste_moderateurs[3] = 'les animateurs de section';
				$liste_moderateurs[4] = 'les animateur d\'unit&eacute;';
				echo '<br />Mod&eacute;ration par <strong>'.$liste_moderateurs[$forum['forum_moderation']].'</strong>';
?>
</p>
</div>
<?php
		if ($user == 0)
		{
?>
<div class="msg">
<p align="center" class="petit">Pour &eacute;crire sur le forum, connecte-toi ci-dessous ou <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">inscris-toi</a> sur le site.</p>
</div>
<?php				
			include('login.php');
		}

	} // fin affichage fil
	else if ($_GET['do'] == 'supprimer' and $user['niveau']['numniveau'] == 5 and is_numeric($_GET['fil_id']))
	{ // Suppression d'une discussion de la db
		if (empty($_GET['ok']))
		{ // On demande confirmation
			$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_GET['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$_GET['fil_id'];
?>
<div class="action">
  <p align="center" class="rmqbleu">Es-tu certain de vouloir supprimer d&eacute;finitivement cette discussion du forum ?</p>
  <p align="center"><a href="<?php echo 'index.php?page=forum_fil&amp;do=supprimer&amp;ok=1&amp;fil_id='.$_GET['fil_id']; ?>" class="bouton">OUI</a>
	<a href="<?php echo $lien_fil; ?>" class="bouton">NON</a></p>
</div>
<?php
		}
		else if ($_GET['ok'] == 1)
		{ // On supprime la discussion de la db
			$update_last = '';
			if (untruc(PREFIXE_TABLES.'forum_msg', 'fil_id', 'msg_id', $forum['forum_last_msg_id']) == $_GET['fil_id'])
			{ // la discussion contient le message le plus récent du forum
				$sql = "SELECT msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE forum_id = '".$forum['forum_id']."' AND fil_id != '".$_GET['fil_id']."' ORDER BY msg_id DESC LIMIT 1";
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 1)
				{
					$ligne = mysql_fetch_assoc($res);
					$update_last = ", forum_last_msg_id = '".$ligne['msg_id']."'";
				}
			}
			// Mise à jour du nombre de messages et de discussions du forum
			$sql = "UPDATE ".PREFIXE_TABLES."forum_forums 
			SET 
			forum_nbfils = forum_nbfils - 1, 
			forum_nbmsg = forum_nbmsg - ".$forum['fil_nbmsg'].$update_last." 
			WHERE forum_id = '".$forum['forum_id']."'";
			send_sql($db, $sql);
			
			// On récupère l'id des messages de la discussion
			$sql = "SELECT msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE fil_id = '".$_GET['fil_id']."'";
			$res = send_sql($db, $sql);
			$liste_id = '';
			while ($ligne = mysql_fetch_assoc($res))
			{ // on prépare la requête de suppression de messages
				$liste_id .= (empty($liste_id)) ? "" : " OR ";
				$liste_id .= "msg_id = '".$ligne['msg_id']."'";
			}
			if (!empty($liste_id))
			{ // On supprime le texte des messages
				$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg_txt WHERE ".$liste_id;
				send_sql($db, $sql);
			}
			// On supprime la structure des messages
			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg WHERE fil_id = '".$_GET['fil_id']."'";
			send_sql($db, $sql);
			// On supprime la discussion
			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_fils WHERE fil_id = '".$_GET['fil_id']."'";
			send_sql($db, $sql);
			// On enregistre la suppression
			log_this('Suppression discussion : '.$forum['fil_titre'].' du forum '.$forum['forum_titre'], 'forum');
			
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
<div class="msg">
  <p align="center" class="rmqbleu">La discussion a &eacute;t&eacute; supprim&eacute;e du forum.</p>
  <p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
		}
	}
	else if ($_GET['do'] == 'fermer' and $can_moderate and is_numeric($_GET['fil_id']))
	{ // Fermeture de la discussion (elle sera invisible aux lecteurs)
		$sql = "UPDATE ".PREFIXE_TABLES."forum_fils
		SET 
		fil_statut = '0', 
		fil_moderateur = '".$user['num']."' 
		WHERE fil_id = '".$_GET['fil_id']."'";
		send_sql($db, $sql);
		
		// On enregistre la fermeture
		log_this('Fermeture discussion : '.$forum['fil_titre'].' sur le forum '.$forum['forum_titre'], 'forum');

		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
<div class="msg">
  <p align="center" class="rmqbleu">La discussion est maintenant ferm&eacute;e. Les utilisateurs ne peuvent plus la consulter.</p>
  <p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'ouvrir' and $can_moderate and is_numeric($_GET['fil_id']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."forum_fils
		SET 
		fil_statut = '1', 
		fil_moderateur = '0' 
		WHERE fil_id = '".$_GET['fil_id']."'";
		send_sql($db, $sql);
		
		// On enregistre la réouverture
		log_this('Réouverture discussion : '.$forum['fil_titre'].' sur le forum '.$forum['forum_titre'], 'forum');

		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
<div class="msg">
  <p align="center" class="rmqbleu">La discussion est &agrave; nouveau ouverte.</p>
  <p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'verrouiller' and $can_moderate and is_numeric($_GET['fil_id']))
	{
		$sql = "UPDATE ".PREFIXE_TABLES."forum_fils
		SET 
		fil_statut = '2', 
		fil_moderateur = '".$user['num']."' 
		WHERE fil_id = '".$_GET['fil_id']."'";
		send_sql($db, $sql);
		
		// On enregistre le verrouillage
		log_this('Discussion verrouillée : '.$forum['fil_titre'].' sur le forum '.$forum['forum_titre'], 'forum');

		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
<div class="msg">
  <p align="center" class="rmqbleu">La discussion est maintenant verrouill&eacute;e. Les utilisateurs peuvent la lire mais pas y poster.</p>
  <p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'deplacer' and $can_moderate and is_numeric($_GET['fil_id']))
	{ // Déplacement d'une discussion
		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : $baselien;
		$sql = "SELECT forum_id, forum_titre FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id != '".$forum['forum_id']."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) > 0)
		{
?>
<script type="text/javascript">
<!--
function certain()
{
	return confirm("Es-tu certain de vouloir déplacer cette discussion dans le forum sélectionné ?");
}
//-->
</script>
<form method="post" action="index.php" name="deplacer_discussion" onsubmit="return certain()" class="form_config_site">
<input type="hidden" name="page" value="forum_fil" />
<input type="hidden" name="do" value="dodeplacer" />
<input type="hidden" name="fil_id" value="<?php echo $_GET['fil_id']; ?>" />
<h2>D&eacute;placer une discussion</h2>
<p>Tu vas d&eacute;placer la discussion : <em><?php echo $forum['fil_titre']; ?></em> dans un autre forum.</p>
<p>Forum de destination :
<select name="new_forum_id">
<?php
			while ($ligne = mysql_fetch_assoc($res))
			{
?>
  <option value="<?php echo $ligne['forum_id']; ?>"><?php echo $ligne['forum_titre']; ?></option>
<?php	
			}
?>
</select></p>
<p class="petitbleu">Chaque forum a ses param&egrave;tres propres. Assure-toi que la discussion peut &ecirc;tre lue par les lecteurs du forum de destination.<br />
Selon ton statut sur le site, il se peut que tu ne sois plus mod&eacute;rateur de la discussion dans le forum de destination.</p>
<p align="center"><input type="submit" value="Déplacer la discussion" /></p>
</form>
<?php
		}
		else
		{
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div class="msg">
<p align="center" class="rmq">Il n'existe pas d'autre forum. Impossible de d&eacute;placer cette discussion !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
		}
?>
<?php
	}
	else if ($_POST['do'] == 'dodeplacer' and $can_moderate and is_numeric($_POST['fil_id']))
	{
		if (is_numeric($_POST['new_forum_id']))
		{ // On peut déplacer la discussion
		
			// On vérifie que le forum existe histoire de ne pas perdre la discussion
			$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_POST['new_forum_id']."'";
			$res = send_sql($db, $sql);
			if (mysql_num_rows($res) == 1)
			{ // le forum existe
				$new_forum = mysql_fetch_assoc($res);
				// On met la discussion à jour
				$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET forum_id = '".$_POST['new_forum_id']."' WHERE fil_id = '".$_POST['fil_id']."'";
				send_sql($db, $sql);
				// On met les messages à jour
				$sql = "UPDATE ".PREFIXE_TABLES."forum_msg SET forum_id = '".$_POST['new_forum_id']."' WHERE fil_id = '".$_POST['fil_id']."'";
				send_sql($db, $sql);

				$new_forum_last_msg_id = ''; // Complément de requête sql
				$old_forum_last_msg_id = ''; // Complément de requête sql

				// On met l'id des messages les plus récents à jour
				// On récupère le message le plus récent du nouveau forum
				$sql = "SELECT msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE forum_id = '".$_POST['new_forum_id']."' ORDER BY msg_date DESC LIMIT 1";
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 1)
				{
					$new_forum = mysql_fetch_assoc($res);
					$new_forum_last_msg_id = ", forum_last_msg_id = '".$new_forum['msg_id']."'";
				}
				// dans le nouveau forum
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET 
				forum_nbfils = forum_nbfils + 1, 
				forum_nbmsg = forum_nbmsg + ".$forum['fil_nbmsg'].$new_forum_last_msg_id."
				WHERE forum_id = '".$_POST['new_forum_id']."'";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET 
				forum_nbfils = forum_nbfils + 1, 
				forum_nbmsg = forum_nbmsg + ".$forum['fil_nbmsg'].$new_forum_last_msg_id."
				WHERE forum_id = '".$_POST['new_forum_id']."'";
				send_sql($db, $sql);

				// On récupère le message le plus récent de l'ancien forum maintenant que l'autre est parti
				$sql = "SELECT msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE forum_id = '".$forum['forum_id']."' ORDER BY msg_date DESC LIMIT 1";
				$res = send_sql($db, $sql);
				if (mysql_num_rows($res) == 1)
				{
					$old_forum = mysql_fetch_assoc($res);
					$old_forum_last_msg_id = ", forum_last_msg_id = '".$old_forum['msg_id']."'";
				}
				//		et dans l'ancien
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET 
				forum_nbfils = forum_nbfils - 1, 
				forum_nbmsg = forum_nbmsg - ".$forum['fil_nbmsg'].$old_forum_last_msg_id." 
				WHERE forum_id = '".$forum['forum_id']."'";
				send_sql($db, $sql);

				log_this('Déplacement discussion '.$_POST['fil_id'].' dans le forum '.$_POST['new_forum_id'], 'forum');
				$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_POST['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil_id='.$_POST['fil_id'];
				$lien_new_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_POST['new_forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$_POST['new_forum_id'];
				$lien_old_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
?>
<div class="msg">
<p align="center" class="rmqbleu">La discussion a &eacute;t&eacute; d&eacute;plac&eacute;e dans le forum <em><?php echo $new_forum['forum_titre']; ?></em></p>
<p align="center"><a href="<?php echo $lien_fil; ?>">Retour &agrave; la discussion</a></p>
<p align="center"><a href="<?php echo $lien_old_forum; ?>">Retour &agrave; l'ancien forum</a> - <a href="<?php echo $lien_new_forum; ?>">Aller au nouveau forum</a></p>
</div>
<?php
			}
			else
			{
				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div class="msg">
<p align="center" class="rmq">Aucun forum s&eacute;lectionn&eacute;. Impossible de d&eacute;placer cette discussion !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
			}
			
		}
		else
		{ // Aucun forum sélectionné, la discussion ne peut pas être déplacée
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div class="msg">
<p align="center" class="rmq">Aucun forum s&eacute;lectionn&eacute;. Impossible de d&eacute;placer cette discussion !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
		}
	
	}
?>
<p align="center" class="petitbleu"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'moderationforum.htm' : 'index.php?page=moderationforum'; ?>" class="lienmort" title="Plus d'infos">Plus 
  d'infos au sujet du forum</a></p> 
<?php
// fin <div id="forum">
?>
</div>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	} // fin defined in_site
} // fin if forum_actif
?>