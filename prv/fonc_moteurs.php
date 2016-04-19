<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_moteurs.php v 1.1 - Quelques outils prédéfinis (derniers messages, derniers articles, ...)
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
*	Makehtml sur le titre des derniers posts du forum
*	Correction lien vers forum si url_rewriting désactivé
*	Utilisation du 3e paramètre de getimagesize
*	Adaptation du script au nouveau forum
*	Log détaillé des reconnaissances de statuts utilisateur
*/

function derniersmessagesforum($largeur = '100%')
{
	include_once('forum_fonctions.php');
	global $db, $site;
	// On récupère les forums existants
	$sql = "SELECT forum_id, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section FROM ".PREFIXE_TABLES."forum_forums";
	$res = send_sql($db, $sql);
	$liste_forums_accessibles = '';
	while ($forum = mysql_fetch_assoc($res))
	{ // On compose la restriction de la requête pour les derniers messages du forum
	  // De cette manière l'utilisateur ne verra que les forums auxquels il a accès
		if (limite_acces_forum($forum, 'lire'))
		{
			$liste_forums_accessibles .= (empty($liste_forums_accessibles)) ? "WHERE d.forum_id = '".$forum['forum_id']."'" : " OR d.forum_id = '".$forum['forum_id']."'";
		}
	}
	$nbre_msg_a_afficher = (is_numeric($site['nbre_derniers_msg_forum'])) ? $site['nbre_derniers_msg_forum'] : 5;
	$sql = "SELECT *
	FROM ((".PREFIXE_TABLES."forum_fils as d
	LEFT JOIN ".PREFIXE_TABLES."forum_msg as m ON d.fil_last_msg_id = m.msg_id)
	LEFT JOIN ".PREFIXE_TABLES."auteurs as a ON m.msg_auteur = a.num)
	".$liste_forums_accessibles."
	ORDER BY m.msg_date DESC
	LIMIT $nbre_msg_a_afficher";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			echo '<div id="derniers_messages_forum">';
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
			echo '<h2>Forums : Derniers messages <a href="'.$lien_forum.'" title="Rejoindre le forum"><img src="templates/default/images/go.png" width="12" height="12" border="0" alt="" /></a></h2>';
			echo '<table border="0" cellspacing="0" cellpadding="2">';
			$j = 0;
			while ($ligne = mysql_fetch_assoc($res))
			{
				$j++;
				$couleur = ($j % 2 == 0) ? 'td-1' : 'td-2';
				echo '<tr class="'.$couleur.'">';
				if (strlen($ligne['fil_titre']) > 35)
				{ // On réduit le titre à 35 caractères maximum
					$titre = html_entity_decode($ligne['fil_titre'], ENT_QUOTES);
					$titre = htmlentities(substr_replace($titre, '...', 35), ENT_QUOTES);
				}
				else
				{
					$titre = $ligne['fil_titre'];
				}
				$nb_pg_fil = ceil($ligne['fil_nbmsg'] / $site['forum_nbmsg_par_page']);
				$fil_last_page = $nb_pg_fil - 1; // la première page est la page 0
				if ($site['url_rewriting_actif'] == 1)
				{
					$lien_latest_msg = ($nb_pg_fil == 1) ? 'fil'.$ligne['fil_id'].'.htm' : 'fil'.$ligne['fil_id'].'_'.$fil_last_page.'.htm';
				}
				else
				{
					$lien_latest_msg = ($nb_pg_fil == 1) ? 'index.php?page=forum_fil&amp;fil='.$ligne['fil_id'] : 'index.php?page=forum_fil&amp;fil='.$ligne['fil_id'].'&amp;pg='.$fil_last_page;
				}
				echo '<td><a href="'.$lien_latest_msg.'#msg'.$ligne['fil_last_msg_id'].'" class="lienmort" title="Posté par '.$ligne['pseudo'].'">'.$titre.'</a></td><td align="right">'.date_ymd_dmy($ligne['msg_date'], 'date').'</td>';
				echo '</tr>';
			}
			echo '</table></div>';
		}
	}
}

function dernierarticletally()
{ // Cette fonction affiche le début du dernier article posté dans le tally
  // Elle n'est pas utilisée à l'heure actuelle, mais peut être intégrée assez facilement dans indexg.php
	global $db, $site;
	$sql = "SELECT numarticle, article_titre, article_texte, article_datecreation, pseudo FROM ".PREFIXE_TABLES."articles as a, ".PREFIXE_TABLES."auteurs as b WHERE article_auteur = b.num AND a.article_banni != '1' ORDER BY article_datecreation DESC LIMIT 1";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			echo '<table border="0" cellspacing="0" width="100%">';
			echo '<tr><td class="rmqbleu">Tally</td></tr><tr><td><table border="0" cellspacing="0" cellpadding="2" class="cadrenoir" width="100%">';
			if ($ligne = mysql_fetch_assoc($res))
			{
				echo '<tr>';
				if (strlen($ligne['article_titre']) > 30)
				{
					//$titre = substr_replace($ligne['article_titre'], '...', 30);
					$titre = html_entity_decode($ligne['article_titre'], ENT_QUOTES);
					$titre = htmlentities(substr_replace($titre, '...', 30), ENT_QUOTES);
				}
				else
				{
					$titre = $ligne['article_titre'];
				}
				echo '<td class="td-gris">'.$titre.'</td>';
				echo '</tr>';
				echo '<tr>';
				$limite = 50;
				$taille = strlen($ligne['article_texte']);
				if ($taille > $limite)
				{
					$texte = $ligne['article_texte'];
					$espace = strpos($texte, ' ', $limite);
					if ($espace === false) {$espace = $limite; } else {$espace = $limite + $espace + 1;} 
					$texte = substr_replace($texte, '...', $espace);
					$texte = makehtml($texte);
					$lien_article_tally = ($site['url_rewriting_actif'] == 1) ? 'tally'.$ligne['numarticle'].'.htm' : 'index.php?page=tally&amp;numero='.$ligne['numarticle'];
					$texte .= ' [<a href="'.$lien_article_tally.'" class="petitbleu">lire la suite</a>]';
				}
				else
				{
					$texte = $ligne['article_texte'];
				}
				echo '<td class="petit">'.$texte.'</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td class="td-gris petit" align="right">par '.$ligne['pseudo'].'</td>';
				echo '</tr>';
			}
			echo '</table></td></tr></table>';
		}
		else
		{
			echo '<table border="0" cellspacing="0" width="100%">';
			echo '<tr><td class="rmqbleu">Tally</td></tr><tr><td><table border="0" cellspacing="0" cellpadding="2" class="cadrenoir" width="100%">';
			echo '<tr>';
			$lien_tally = ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally';
			echo '<td class="petit" align="center">Le Tally est vide.<br /><br /><a href="'.$lien_tally.'">Ajouter un article</a></td>';
			echo '</tr>';
			echo '</table></td></tr></table>';
		}
	}
}


function membresaautoriser($do)
{
	global $db, $user, $sections, $niveaux;
	// un membre ne peut reconnaitre qu'un niveau inférieur ou égal au sien.
	$niveau_user = $user['niveau']['numniveau'];
	$sql = "SELECT num, pseudo, prenom, nom, email, niveau, nivdemande, ipinscription, numsection 
	FROM ".PREFIXE_TABLES."auteurs as a, ".PREFIXE_TABLES."site_niveaux as b
	WHERE a.nivdemande != '0' and clevalidation = '' and a.nivdemande = b.idniveau and b.numniveau <= '$niveau_user' 
	ORDER BY dateinscr DESC";
	if ($res = send_sql($db, $sql))
	{
		$num = mysql_num_rows($res);
		if ($num > 0 and $do == 'show')
		{
			echo '<input type="hidden" name="nbre" value="'.$num.'">';
			echo '<table class="cadrenoir">';
			echo '<tr>';
			echo '<th>Pr&eacute;nom</th>';
			echo '<th>Nom</th>';
			echo '<th>email</th>';
			echo '<th>D&eacute;cision</th>';
			echo '</tr>';
			for ($j = 1; $j <= $num; $j++)
			{
				if ($ligne = mysql_fetch_assoc($res))
				{
					$x = $ligne['numsection'];
					$ip = $ligne['ipinscription'];
					$niv = $niveaux[$ligne['nivdemande']]['nomniveau']; //} else {$niv = "Pas de section sélectionnée";}
					echo '<tr class="td-1">';
					echo '<td class="rmqbleu">'.$ligne['prenom'].'</td>';
					echo '<td class="rmqbleu">'.$ligne['nom'].'</td>';
					echo '<td><a href="mailto:'.$ligne['email'].'" class="lien" title="lui &eacute;crire un email">'.$ligne['email'].'</a></td>';
					echo '<td align="center"><input type="checkbox" name="util-'.$j.'" id="util-'.$j.'_ok" value="'.$ligne['nivdemande'].'-'.$ligne['num'].'"> <label for="util-'.$j.'_ok">Ok</label> ';
					echo '<input type="checkbox" name="util-'.$j.'" id="util-'.$j.'_suppr" value="x-'.$ligne['num'].'"> <label for="util-'.$j.'_suppr">Bannir</label></td>';
					echo '</tr>';
					echo '<tr class="td-2">';
					$lien_membre = ($site['url_rewriting_actif'] == 1) ? 'membre'.$ligne['num'].'.htm' : 'index.php?page=profil_user&amp;user='.$ligne['num'];
					echo '<td colspan="3" align="center">Pseudo : <a href="'.$lien_membre.'" title="Voir son profil">'.$ligne['pseudo'].'</a> - Niveau demandé : '.$niv.'</td>';
					echo '<td align="right">ip : '.$ip.'</td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		}
		elseif ($num > 0 and $do == 'nbr')
		{
			return true;
		}
	}
}

function aff_last_comment()
{
	global $db, $sections, $site;
	// Affichage des commentaires de la photo en cours
	$sql = "SELECT *, c.numcommentaire as numcom, c.commentaire as txtcom, c.datecreation as datecom, g.dossierpt as path FROM ".PREFIXE_TABLES."commentaires as c, ".PREFIXE_TABLES."albums as a, ".PREFIXE_TABLES."galerie as g, ".PREFIXE_TABLES."auteurs as x WHERE c.auteur = x.num AND c.album = a.numalbum AND c.album = g.numgalerie AND c.photo = a.posphoto AND commentairebanni != 1 ORDER BY datecom DESC LIMIT 1";
	$commentaire = send_sql($db, $sql);
	if (mysql_num_rows($commentaire) == 1)
	{
		$comment = mysql_fetch_assoc($commentaire);
		$ref = $comment['dossierpt'].$comment['nomfichier'];
		$taille = @getimagesize($ref);
		$taille = $taille[3];
		$niv_dest = ($comment['galerie_section'] > 0) ? $sections[$comment['galerie_section']]['site_section'] : 'g';
		$lien_photo = ($site['url_rewriting_actif'] == 1) ? $niv_dest.'_galerie_'.$comment['numalbum'].'_'.$comment['posphoto'].'.htm' : 'index.php?niv='.$niv_dest.'&amp;page=galerie&amp;show='.$comment['numalbum'].'&amp;photo='.$comment['posphoto'];
?>
<div id="dernier_commentaire">
<h2>Albums photos : Dernier commentaire <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'lastcomments.htm' : 'index.php?page=lastcomments'; ?>" title="Voir les autres commentaires"><img src="templates/default/images/go.png" width="12" height="12" border="0" alt="Voir les autres commentaires" /></a></h2>
<p><?php
		echo '<a href="'.$lien_photo.'"><img src="'.$comment['dossierpt'].$comment['nomfichier'].'" '.$taille.' class="photo" alt="" /></a>'; ?>
<span class="auteur"><?php echo $comment['pseudo']; ?></span><br />
<?php echo makehtml($comment['txtcom']); ?></p>
<p align="right" class="date">Ajout&eacute; le <?php echo date_ymd_dmy($comment['datecom'], 'jourmois'); ?></p>
</div>
<?php
	}
	// fin affichage des commentaires
}

function gestion_unite_lastmodif($liste_pages)
{ // détermine le nombre de modifications enregistrées dans le log des actions depuis une semaine
	global $db;
	$sql = "SELECT COUNT(*) as nbre, MAX(h_action) as last FROM ".PREFIXE_TABLES."log_actions WHERE ($liste_pages) AND h_action > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '7' DAY)";
	if ($res = send_sql($db, $sql))
	{
		$resultat = mysql_fetch_assoc($res);
		if (mysql_num_rows($res) == 0)
		{
			$resultat = array('nbre' => 0, 'last' => '0000-00-00');
		}
		return $resultat;
	}
	else
	{
		return array('nbre' => 0, 'last' => '0000-00-00');
	}
}

?>