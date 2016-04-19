<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum.php v 1.1.1 - Affichage des forums du site
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
*	Nouveau forum
* Modifications v 1.1.1
*	Ajout d'un message "connectez-vous pour poster sur le forum"
*	bug 07/11 : pagination page suivante/précédente incorrecte sans url-rewriting
*	bug 07/11 : tri des discussions par la date plutôt que par l'id du dernier post
*		c'est problématique si on déplace les discussions d'un forum à l'autre après l'update
*	ajout du formulaire de connexion
*/
/*
 * Modification v 1.1.1
 * 	build 091109 : correction d'un bug de compatibilité avec nouveaux paramètres PHP 5
 */

include_once('connex.php');
include_once('fonc.php');
include_once('forum_fonctions.php');

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
<title>Forums</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
?>
<?php
	if ($site['forum_actif'] != 1 and $user['niveau']['numniveau'] == 5)
	{
?>
<p id="message_important">Le forum est inactif <a href="index.php?page=config_site&amp;categorie=general" title="Activer le forum"><img src="templates/default/images/autres.png" alt="Activer le forum" /></a></p>
<?php
	}
?>
<div id="forum">
<h1>Forums de discussions</h1>
<?php
	/* Liste des forums
	*****************************************/

	// Niveau d'accès du forum
	$liste_acces[0] = 'Forum en acc&egrave;s public';
	$liste_acces[1] = 'Forum ouvert aux membres du site';
	$liste_acces[2] = 'Forum ouvert aux Membre de l\'unit&eacute;';
	$liste_acces[3] = 'Forum ouvert aux Animateurs de sections';
	$liste_acces[4] = 'Forum ouvert aux Animateurs d\'unit&eacute;';
	$liste_acces[5] = 'Forum ouvert aux Webmasters';
	
	if (empty($_GET['f']) and empty($_GET['do']))
	{ // On montre la liste des forums accessibles à l'utilisateur
		$sql = "SELECT a.forum_id, a.forum_titre, a.forum_description, a.forum_nbfils, 
		a.forum_nbmsg, a.forum_last_msg_id, b.pseudo, c.msg_date, a.forum_moderation, 
		a.forum_statut, a.forum_acces_niv, a.forum_acces_section, a.forum_position,
		c.fil_id
		FROM ((".PREFIXE_TABLES."forum_forums as a
		LEFT JOIN ".PREFIXE_TABLES."forum_msg as c ON c.msg_id = a.forum_last_msg_id)
		LEFT JOIN ".PREFIXE_TABLES."auteurs as b ON b.num = c.msg_auteur)
		ORDER BY forum_position";
		$res = send_sql($db, $sql);
		$nbre_forums_affiches = 0;
		if (mysql_num_rows($res) > 0)
		{ // Il y a des forums disponibles
			while ($forum = mysql_fetch_assoc($res))
			{ // On liste les forums
				if (limite_acces_forum($forum, 'lire'))
				{
					$nbre_forums_affiches++;
					if ($nbre_forums_affiches == 1)
					{ // On affiche le début du tableau avant le premier forum
?>
<p class="f_recherche"><a href="index.php?page=forum_search">Rechercher dans le forum</a></p>
<table class="f_liste_forums">
  <tr>
    <th>Forum</th>
    <th>Discussions</th>
    <th>Messages</th>
    <th>Dernier message </th>
  </tr>
<?php
					}
					$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&amp;f='.$forum['forum_id'];
					$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$forum['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$forum['fil_id'];
?>
  <tr class="f_forum" title="<?php echo $liste_acces[$forum['forum_acces_niv']]; /* Info sur qui peut lire le forum */ ?>">
    <td class="f_titre <?php echo 'f_'.$forum['forum_acces_niv']; ?>"><h3><a href="<?php echo $lien_forum; ?>"><?php echo $forum['forum_titre']; ?></a></h3>
      <p><?php echo makehtml($forum['forum_description']); ?></p></td>
    <td width="50" align="center"><?php echo $forum['forum_nbfils']; ?></td>
    <td width="50" align="center"><?php echo $forum['forum_nbmsg']; ?></td>
    <td width="120" align="center">
<?php 
					if ($forum['msg_date'])
					{ // On affiche le lien vers le dernier message du forum
						$fil_nbmsg = untruc(PREFIXE_TABLES.'forum_fils', 'fil_nbmsg', 'fil_id', $forum['fil_id']);
						$fil_last_page = ceil($fil_nbmsg / $site['forum_nbmsg_par_page']) - 1; // la première page est la page 0
						if ($site['url_rewriting_actif'] == 1)
						{
							$lien_latest_msg = ($nb_pg_fil == 1) ? 'fil'.$forum['fil_id'].'.htm' : 'fil'.$forum['fil_id'].'_'.$fil_last_page.'.htm';
						}
						else
						{
							$lien_latest_msg = ($nb_pg_fil == 1) ? 'index.php?page=forum_fil&amp;fil='.$forum['fil_id'] : 'index.php?page=forum_fil&amp;fil='.$forum['fil_id'].'&amp;pg='.$fil_last_page;
						}
						echo 'il y a '.temps_ecoule($forum['msg_date']).'<br />par '.$forum['pseudo']; 
?>
	  <a href="<?php echo $lien_latest_msg.'#msg'.$forum['forum_last_msg_id']; ?>" title="Voir le dernier message"><img src="templates/default/images/go.png" align="middle" border="0" alt="" /></a>
<?php
					}
					else
					{
						echo 'Aucun message';
					}
?>
    </td>
  </tr>
<?php
				}
			}
			if ($nbre_forums_affiches > 0)
			{ // on ferme le tableau si on a affiché des forums
?>
</table>
<?php
			}
		} // num_rows

		if ($nbre_forums_affiches == 0)
		{ // Aucun forum accessible
			// On regarde s'il y a des forums dans la db pour prévenir l'utilisateur
			$sql = "SELECT count(*) as nbre FROM ".PREFIXE_TABLES."forum_forums";
			$res = send_sql($db, $sql);
			$ligne = mysql_fetch_assoc($res);
			if ($ligne['nbre'] > 0)
			{ // Il y a des forums mais l'utilisateur n'y a pas accès
?>
<div class="msg">
<p align="center">Tu n'as acc&egrave;s &agrave; aucun des forums existants</p>
</div>
<?php
			}
			else
			{ // Aucun forum n'existe
?>
<div class="msg">
<p align="center">La base de donn&eacute;es ne contient aucun forum ! </p>
</div>
<?php
			}
		}
		
		if ($user == 0)
		{ // L'utilisateur n'est pas connecté
?>
<div class="msg">
<p align="center" class="petit">Pour &eacute;crire sur le forum ou avoir acc&egrave;s aux forums restreints, connecte-toi ci-dessous ou <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">inscris-toi</a> sur le site.</p>
</div>
<?php				
			include('login.php');
		}
	}
	/* Liste des discussions du forum sélectionné
	*********************************************/
	else if (is_numeric($_GET['f']) and empty($_GET['do']))
	{ // On montre la liste des discussions du forum demandé si l'utilisateur a accès au forum
		$pg = (is_numeric($_GET['pg'])) ? $_GET['pg'] : 0;
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$_GET['f']."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'lire'))
			{
				$par = $site['forum_nbfils_par_page'];
				// nombre de pages
				$nb_pg = ceil($forum['forum_nbfils'] / $par);
				// page en cours
				$pg = (is_numeric($_GET['pg']) and $_GET['pg'] >= 0 and $_GET['pg'] <= $nb_pg) ? $_GET['pg'] : 0;
				// paramètre de la requête sql
				$debut = ($pg > 0) ? (($pg * $par) + 0) : 0;

				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
				$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a></h2>
<p class="f_recherche"><a href="index.php?page=forum_search&amp;forum_id=<?php echo $forum['forum_id']; ?>">Rechercher dans le forum</a></p>
<?php
				// Pagination
				if ($nb_pg > 1) 
				{
?>
<p class="f_pagination">Aller &agrave; la page <?php
					if ($pg > 0)
					{ // lien vers la page précédente
						$lien_page_pcdte = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'_'.($pg - 1).'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'].'&amp;pg='.($pg - 1);
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
								$lien_page = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'_'.$i.'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'].'&amp;pg='.$i;
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
						$lien_page_0 = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'];
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
								$lien_page = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'_'.$j.'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'].'&amp;pg='.$j;
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
						$lien_last_page = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'_'.($nb_pg - 1).'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'].'&amp;pg='.($nb_pg - 1);
?>, <a href="<?php echo $lien_last_page; ?>"><?php echo $nb_pg; ?></a>
<?php
					}
					if ($pg < $nb_pg - 1)
					{ // lien page suivante
						$lien_page_svte = ($site['url_rewriting_actif'] == 1) ? 'forum'.$_GET['f'].'_'.($pg + 1).'.htm' : 'index.php?page=forum&amp;f='.$_GET['f'].'&amp;pg='.($pg + 1);
?> <a href="<?php echo $lien_page_svte; ?>">Suivante</a>
<?php
					}
?></p>
<?php
				} // fin pagination

				if (limite_acces_forum($forum, 'ecrire'))
				{ // bouton pour lancer une discussion
?>
<div class="f_nouveau">
<p><a href="index.php?page=forum_post&amp;f=<?php echo $_GET['f']; ?>"><img src="templates/default/forum/nouveau.png" alt="Nouvelle discussion" width="88" height="20" /></a></p>
</div>
<?php
				}

				// On récupère la liste des sujets récents dans le forum demandé
				$sql = "SELECT *
				FROM ((".PREFIXE_TABLES."forum_fils as d
				LEFT JOIN ".PREFIXE_TABLES."forum_msg as m ON d.fil_last_msg_id = m.msg_id)
				LEFT JOIN ".PREFIXE_TABLES."auteurs as a ON m.msg_auteur = a.num)
				WHERE
				d.forum_id = '".$_GET['f']."'
				ORDER BY m.msg_date DESC
				LIMIT $debut, $par";
				// d.fil_last_msg_id
				if ($res = send_sql($db, $sql))
				{
					$num = mysql_num_rows($res);
					if ($num > 0)
					{
						$j = 1;
?>
<table border="0" cellspacing="1" width="100%" align="center" class="f_liste_fils">
  <tr>
	<th colspan="2">&nbsp;</th>
	<th class="petit" title="Nombre de messages dans la discussion"># msg</th>
	<th class="petit" title="Nombre de lectures de la discussion"># lectures</th>
	<th class="petit" title="Le dernier message a &eacute;t&eacute; post&eacute; il y a">Dernier msg</th>
  </tr>
<?php
						while ($fil = mysql_fetch_assoc($res))
						{ // On affiche la liste des discussions du forum
						  // On affiche tous les fils, le contrôle d'accès se fait dans forum_fil.php
							$couleur = ($j % 2 == 0) ? 'f_1' : 'f_2';
							$plus = '';
							$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'];
?>
  <tr class="<?php echo $couleur; echo ($ligne['fil_statut'] == '1') ? ' f_fil_banni' : ''; ?>">
	<td align="center" class="f_icone"><img src="img/smileys/<?php echo $fil['fil_icone'].'.gif'; ?>" alt="fil n° <?php echo $fil['fil_id']; ?>" /></td>
	<td class="f_titre_fil"><a href="<?php echo $lien_fil; ?>" title="Discussion cr&eacute;&eacute;e le <?php echo date_ymd_dmy($fil['fil_date'], 'jourheure'); ?>" class="f_titre_fil"><?php echo $fil['fil_titre']; ?></a> 
<?php
							$nb_pg_fil = 1; // Il y a d'office une page
							if ($fil['fil_nbmsg'] > $site['forum_nbmsg_par_page'])
							{
								$nb_pg_fil = ceil($fil['fil_nbmsg'] / $site['forum_nbmsg_par_page']);
								$fil_last_page = $nb_pg_fil - 1; // la première page est la page 0
								// 1e page
								$lien = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'];
?><br /><span class="fil_pg">
(Aller &agrave; la page <a href="<?php echo $lien; ?>">1</a><?php
								if ($nb_pg_fil >= 2)
								{ // 2e page
									$lien = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil['fil_id'].'_1.htm' : 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'].'&amp;pg=1';
?>, <a href="<?php echo $lien; ?>">2</a><?php
								}
								if ($nb_pg_fil >= 3)
								{ // dernière page
									echo ($nb_pg_fil > 3) ? ', ...' : '';
									$lien = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil['fil_id'].'_'.$fil_last_page.'.htm' : 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'].'&amp;pg='.$fil_last_page;
?>, <a href="<?php echo $lien; ?>"><?php echo $nb_pg_fil; ?></a><?php
								}
?>
)</span>
<?php
							}
							if ($site['url_rewriting_actif'] == 1)
							{
								$lien_latest_msg = ($nb_pg_fil == 1) ? 'fil'.$fil['fil_id'].'.htm' : 'fil'.$fil['fil_id'].'_'.$fil_last_page.'.htm';
							}
							else
							{
								$lien_latest_msg = ($nb_pg_fil == 1) ? 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'] : 'index.php?page=forum_fil&amp;fil='.$fil['fil_id'].'&amp;pg='.$fil_last_page;
							}
?>	
	</td>
	<td align="center"><?php echo $fil['fil_nbmsg']; ?></td>
	<td align="center"><?php echo $fil['fil_nbvues']; ?></td>
	<td title="Dernier message post&eacute; le <?php echo date_ymd_dmy($fil['msg_date'], 'dateheure'); ?>" align="center">
	  il y a <?php echo temps_ecoule($fil['msg_date']); ?><br />
	  <?php echo $fil['pseudo']; ?> <a href="<?php echo $lien_latest_msg.'#msg'.$fil['fil_last_msg_id']; ?>" title="Voir le dernier message"><img src="templates/default/images/go.png" align="middle" border="0" alt="" /></a>
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
					{ // Aucune discussion dans le forum
?>
<div class="msg">
<p align="center">Ce forum est encore vide.</p>
</div>
<?php
					}
				}
				else
				{ // Problème avec la requête sql
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
</div>
<?php
				}
				if (limite_acces_forum($forum, 'ecrire'))
				{ // Bouton pour lancer une discussion
?>
<div class="f_nouveau">
<p><a href="index.php?page=forum_post&amp;f=<?php echo $_GET['f']; ?>"><img src="templates/default/forum/nouveau.png" alt="Nouvelle discussion" width="88" height="20" /></a></p>
</div>
<?php
				}
				else if ($user == 0)
				{
?>
<div class="msg">
<p align="center" class="petit">Pour &eacute;crire sur le forum, connecte-toi ci-dessous ou <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">inscris-toi</a> sur le site.</p>
</div>
<?php				
					include('login.php');
				}
?>
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
			}
			else
			{ // l'utilisateur n'a pas le droit de consulter le forum
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas acc&egrave;s &agrave; ce forum !</p>
</div>
<?php
			}
		}
		else
		{ // le forum n'existe pas
?>
<div class="msg">
<p align="center" class="rmq">Ce forum n'existe pas !</p>
</div>
<?php
		}
	} // fin affichage des discussions d'un forum
	else if (!is_numeric($_GET['f']) and empty($_GET['do']))
	{ // l'utilisateur s'amuse à faire n'importe quoi
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
</div>
<?php
	}
?>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'moderationforum.htm' : 'index.php?page=moderationforum'; ?>" title="Plus d'infos">Plus 
  d'infos au sujet du forum</a> 
<?php
	if ($user['niveau']['numniveau'] == 5 and empty($_GET['do']))
	{
?>
- <a href="index.php?page=forum_gestion">G&eacute;rer les forums</a>

<?php
	}
?></p> 
</div><?php /* fin <div id="forum"> */ ?>
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