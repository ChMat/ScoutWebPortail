<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum.php v 1.1 - Recherche sur les forums
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
*	Nouveau fichier ajouté dans swp v 1.1
*/

include_once('connex.php');
include_once('fonc.php');
include_once('forum_fonctions.php');

// Les recherches sont effectuées par forum et pas de manière globale

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
<?php
	if ($site['forum_actif'] != 1 and $user['niveau']['numniveau'] == 5)
	{
?>
<p id="message_important">Le forum</p>
<?php
	}

	$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div id="forum">
<h1>Forums de Discussions</h1>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a></h2>
<?php
	$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums ORDER BY forum_titre ASC";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) > 0)
	{ // Il y a des forums, donc on peut faire des recherches dedans :)

?>
<div class="action">
<p class="rmq">Rechercher dans le forum</p>
<p>Pour rechercher un message ou un th&egrave;me particulier 
  dans le forum, s&eacute;lectionne un forum, entre un mot-cl&eacute; ci-dessous et lance la 
  recherche.</p>
  <form action="index.php" method="post" name="form1" id="form1">
	<p align="center">Chercher 
	  <input type="hidden" name="page" value="forum_search" />
	<input type="text" name="search" maxlength="30" size="20" value="<?php echo stripslashes($_POST['search']); ?>" tabindex="1" /> 
	dans le forum <select name="forum_id" tabindex="2">
<?php
		while ($forum = mysql_fetch_assoc($res))
		{
			if (limite_acces_forum($forum, 'lire'))
			{
?>
	<option value="<?php echo $forum['forum_id']; ?>"<?php echo ($_POST['forum_id'] == $forum['forum_id'] or $_GET['forum_id'] == $forum['forum_id']) ? ' selected="selected"' : ''; ?>><?php echo $forum['forum_titre']; ?></option>
<?php
			}
		}
?>
</select>
</p>
<p align="center">Rechercher dans 
  <input name="search_in" type="radio" id="t" tabindex="3" value="titre"<?php echo ($_POST['search_in'] == 'titre' or empty($_POST['search_in'])) ? ' checked="checked"' : ''; ?> />
  <label for="t">le titre</label>
  <input type="radio" name="search_in" value="fulltext" tabindex="4" id="f"<?php echo ($_POST['search_in'] == 'fulltext') ? ' checked="checked"' : ''; ?> />
  <label for="f">le texte complet</label> 
  des messages</p>
<p align="center">
  <input type="submit" name="act" value="Chercher" tabindex="5" />
  <input type="hidden" name="do" value="search" />
  </p>
 <p class="petitbleu">La recherche dans le texte complet prend plus de temps.</p>
  </form>
</div>
<?php
	}
	else
	{ // Le forum est vide
?>
<div class="msg">
<p align="center" class="rmq">Il n'y a aucun forum dans la base de donn&eacute;es !</p>
</div>
<?php		
	}

	// Vérification que l'utilisateur a accès au forum
	/************************************************/
	// Dans une version suivante, on autorisera la recherche à travers tous les forums.
	$forum_id = (is_numeric($_POST['forum_id'])) ? $_POST['forum_id'] : 0;

	// On vérifie les autorisations
	$can_lire = false; // Tant que $can_lire est à false, les résultats ne sont pas affichés
	if ($forum_id > 0 and $_POST['do'] == 'search')
	{ // l'utilisateur a sélectionné un forum
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$forum_id."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'lire'))
			{
				$can_lire = true;
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'es pas autoris&eacute; &agrave; consulter ce forum !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Ce forum n'existe pas !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
		}
	}
	if ($_POST['do'] == 'search' and !empty($_POST['search']) and $can_lire)
	{ // On effectue la recherche proprement dite
		$search = htmlentities($_POST['search'], ENT_QUOTES);
		if ($_POST['search_in'] == 'fulltext')
		{ // Recherche dans le corps des messages
			$sql = "SELECT 
			m.msg_id, m.fil_id, a.pseudo, m.msg_titre, m.msg_date
			FROM 
			".PREFIXE_TABLES."forum_msg as m, 
			".PREFIXE_TABLES."forum_msg_txt as t,
			".PREFIXE_TABLES."auteurs as a 
			WHERE 
			m.msg_auteur = a.num 
			AND m.msg_id = t.msg_id
			AND (
				m.msg_titre LIKE '%$search%' 
				OR t.msg_txt LIKE '%$search%'
				OR a.pseudo LIKE '%$search%') 
			AND m.msg_statut != '1' 
			AND m.forum_id = '".$_POST['forum_id']."'
			GROUP BY fil_id
			ORDER BY m.msg_id DESC";
		}
		else
		{ // Recherche uniquement dans les titres des messages et les pseudos
			$sql = "SELECT 
			m.msg_id, m.fil_id, a.pseudo, m.msg_titre, m.msg_date
			FROM 
			".PREFIXE_TABLES."forum_msg as m, 
			".PREFIXE_TABLES."auteurs as a 
			WHERE 
			m.msg_auteur = a.num 
			AND (
				m.msg_titre LIKE '%$search%' 
				OR a.pseudo LIKE '%$search%') 
			AND m.msg_statut != '1' 
			AND m.forum_id = '".$_POST['forum_id']."'
			GROUP BY fil_id
			ORDER BY m.msg_id DESC";
		}
		if ($res = send_sql($db, $sql))
		{
			$search = stripslashes($search);
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
?>
<div class="msg">
  <p align="center"><?php echo $num; ?> discussions contiennent <q class="rmqbleu"><?php echo $search; ?></q></p>
</div>
<table border="0" cellspacing="1" width="100%" align="center" class="f_liste_fils">
<?php
				if ($num > 100) {$num = 100; $att = '1';}
				for ($j = 1; $j<=$num; $j++)
				{
					if ($ligne = mysql_fetch_assoc($res))
					{
						$couleur = ($j % 2 == 0) ? 'f_1' : 'f_2';
						$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$ligne['fil_id'].'.htm#msg'.$ligne['msg_id'] : 'index.php?page=forum_fil&amp;fil='.$ligne['fil_id'].'#msg'.$ligne['msg_id'];
?>
  <tr class="<?php echo $couleur; ?>">
	<td><a href="<?php echo $lien_fil; ?>" class="f_titre_fil"><?php echo $ligne['msg_titre']; ?></a> <span class="petit">[Fil n° <?php echo $ligne['fil_id']; ?>]</span></td>
	<td align="center">il y a <?php echo temps_ecoule($ligne['msg_date']); ?><br />
	 par <?php echo $ligne['pseudo']; ?></td>
  </tr>
<?php
					}
				}
?>
</table>
<?php
				if ($att == '1')
				{ // On n'affiche que les 100 premiers résultats
?>
<div class="msg">
<p class="rmqbleu" align="center">Seuls les 100 premiers résultats sont affichés.</p>
</div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Aucun message sur le forum ne contient <q><?php echo makehtml($search); ?></q>.</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Une erreur s'est produite !</p>
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