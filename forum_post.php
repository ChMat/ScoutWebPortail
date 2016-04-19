<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* forum_post.php v 1.1.1 - Gestion des posts (nouvelle discussion, nouveau message, édition)
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
* Modifications v 1.1.1
*	bug 07/11 : A chaque post, la discussion était réouverte (gênant lorsque la discussion est verrouillée)
*	Ajout d'un lien de retour vers la discussion
*	bug 13/11 : Mise à jour dernier message du forum et de la discussion après modération/suppression/réhabilitation
*/

include_once('connex.php');
include_once('fonc.php');
include_once('forum_fonctions.php');

if ($site['forum_actif'] != 1 and $user['niveau']['numniveau'] < 5)
{
	include('module_desactive.php');
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
}
else
{
?>
<div id="forum">
<h1>Forums de discussions</h1>
<?php	
	$afaire = '';
	$can_post = false; // Tant que $can_post est à false, le formulaire n'est pas affiché
	$can_edit_post = false;
	$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
	// Vérification que l'utilisateur peut poster
	/************************************************/
	// la vérification se fait ici pour l'affichage du formulaire ainsi qu'à l'envoi de celui-ci
	$forum_id = $fil_id = $msg_id = 0;
	// Nouvelle discussion
	if (is_numeric($_GET['f']))
	{
		$forum_id = $_GET['f'];
	}
	else if (is_numeric($_POST['forum_id']))
	{
		$forum_id = $_POST['forum_id'];
	}
	// Réponse dans une discussion
	if (is_numeric($_GET['fil_id']))
	{
		$fil_id = $_GET['fil_id'];
	}
	else if (is_numeric($_POST['fil_id']))
	{
		$fil_id = $_POST['fil_id'];
	}
	// Edition d'un message
	if (is_numeric($_GET['msg_id']))
	{
		$msg_id = $_GET['msg_id'];
	}
	else if (is_numeric($_POST['msg_id']))
	{
		$msg_id = $_POST['msg_id'];
	}
	// On vérifie les autorisations
	if ($forum_id > 0)
	{ // l'utilisateur veut créer une discussion
		$afaire = 'newpost';
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$forum_id."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'ecrire'))
			{
				$can_post = true;
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'es pas autoris&eacute; &agrave; poster sur ce forum !</p>
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
	else if ($fil_id > 0)
	{ // l'utilisateur veut répondre à un message
		$afaire = 'post';
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums as f, ".PREFIXE_TABLES."forum_fils as d WHERE d.forum_id = f.forum_id AND d.fil_id = '".$fil_id."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'ecrire'))
			{
				$can_post = true;
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas acc&egrave;s &agrave; ce forum !</p>
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
	else if ($msg_id > 0)
	{ // l'utilisateur veut éditer un message
		$afaire = 'editpost';
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums as f, ".PREFIXE_TABLES."forum_fils as d, ".PREFIXE_TABLES."forum_msg as m WHERE m.forum_id = f.forum_id AND m.fil_id = d.fil_id AND m.msg_id = '".$msg_id."'";
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // Le forum existe
			$forum = mysql_fetch_assoc($res);
			if (limite_acces_forum($forum, 'ecrire'))
			{ // l'utilisateur a accès au forum, on vérifie qu'il peut éditer le message
				$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_msg WHERE msg_id = '".$msg_id."' LIMIT 1";
				$res = send_sql($db, $sql);
				$message = mysql_fetch_assoc($res);
				if (is_moderateur($forum['forum_moderation'], $forum['fil_auteur']) or ($user['num'] == $message['msg_auteur'] and temps_ecoule($message['msg_date'], 0, true) < 3600))
				{ // l'utilisateur peut éditer le message (comme modérateur ou comme simple utilisateur)
					$can_edit_post = true;
					$can_post = true;
				}
				else
				{ // L'utilisateur ne peut pas éditer le message
?>
<?php
					$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a></h2>
<div class="msg">
<p align="center" class="rmq">Tu n'es pas autoris&eacute; &agrave; modifier ce message !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas acc&egrave;s &agrave; ce forum !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
			}
		}
		else if ($_GET['do'] != 'dosuppr')
		{
?>
<div class="msg">
<p align="center" class="rmq">Ce forum n'existe pas !</p>
<p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum  </a></p>
</div>
<?php
		}
	}
	if (((is_numeric($_GET['fil']) and $filouvert and $can_post) or $can_post or ($can_post and $can_edit_post)) and empty($_GET['do']) and empty($_POST['do']))
	{
		if ($user['niveau']['numniveau'] > 0 and ($can_post or $can_edit_post))
		{
?>
<script language='JavaScript' type="text/JavaScript">
<!--
function validate(form) 
{
	if (form.msg_txt.value=="") 
	{
		alert("Sans doute qu'une discussion à propos du vide aurait un intérêt certain,\nmais pourquoi ne rien écrire?");
		return false; 
	}
	else if (form.msg_titre.value=="")
	{
		alert("Merci d'indiquer un titre.");
		return false; 
	}
	else
	{
		getElement('bouton_envoi_post').disabled = true;
		getElement('bouton_envoi_post').value = 'Patience...';
		return true; 
	}
}
//-->
</script>
<?php
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
			$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
			$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$forum['fil_id'].'.htm' : 'index.php?page=forum_fil&fil='.$forum['fil_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a>
<?php
			if ($afaire != 'newpost')
			{
?>
   - <a href="<?php echo $lien_fil; ?>" class="titre"><?php echo $forum['fil_titre']; ?></a>
<?php
			}
?>
</h2>
<?php
			if ($forum['fil_statut'] == 2)
			{
				$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div class="msg">
<p align="center" class="rmq">Cette discussion est verrouill&eacute;e. Seuls les mod&eacute;rateurs peuvent encore intervenir.</p>
</div>
<?php
			}
?>
<form action="index.php" method="post" name="formulaire" id="formulaire" onsubmit="return validate(this)" class="post_message">
<div class="f_droits" style="float:right;">
    <p align="right">
      <?php
			// On informe l'utilisateur des param&egrave;tres du forum en cours
			$liste_statuts[0] = '<span class="rmq">Forum ferm&eacute;</span>';
			$liste_statuts[1] = ''; // le forum est ouvert
			$liste_statuts[2] = '<span class="rmq">Forum verrouill&eacute;</span>';
			$liste_statuts[3] = '<span class="rmq">Forum verrouill&eacute;</span>';
			echo $liste_statuts[$forum['forum_statut']];
			// Niveau d'acc&egrave;s du forum
			$liste_acces[0] = '<br />Forum en <strong>acc&egrave;s public</strong>';
			$liste_acces[1] = '<br />Niveau minimum : <strong>Membre du site</strong>';
			$liste_acces[2] = '<br />Niveau minimum : <strong>Membre de l\'unit&eacute;</strong>';
			$liste_acces[3] = '<br />Niveau minimum : <strong>Animateur</strong>';
			$liste_acces[4] = '<br />Niveau minimum : <strong>Animateur d\'unit&eacute;</strong>';
			$liste_acces[5] = '<br />Niveau minimum : <strong>Webmasters</strong>';
			echo $liste_acces[$forum['forum_acces_niv']];
			// Section cible du forum
			if ($forum['forum_acces_section'] > 0)
			{ // unit&eacute; compl&egrave;te
				echo '<br />Unit&eacute; : <strong title="L\'unit&eacute; et toutes ses sections">'.$sections[$forum['forum_acces_section']]['nomsectionpt'].'</strong>';
			}
			else if ($forum['forum_acces_section'] < 0)
			{ // section uniquement
				echo '<br />Section : <strong>'.$sections[($forum['forum_acces_section'] * -1)]['nomsectionpt'].'</strong> uniquement';
			}
			// Qui sont les mod&eacute;rateurs du forum
			$liste_moderateurs[0] = 'le webmaster';
			$liste_moderateurs[1] = 'l\'auteur de chaque discussion';
			$liste_moderateurs[2] = 'l\'auteur de la discussion et les animateurs';
			$liste_moderateurs[3] = 'les animateurs de section';
			$liste_moderateurs[4] = 'les animateur d\'unit&eacute;';
			echo '<br />Mod&eacute;ration par <strong>'.$liste_moderateurs[$forum['forum_moderation']].'</strong>';
?>
    </p>
  </div>
  <h2><?php 
			if ($afaire == 'newpost')
			{
				echo '<input type="hidden" name="forum_id" value="'.$_GET['f'].'" />';
?>Lancer un nouveau sujet de discussion<?php
			}
			else if ($afaire == 'post')
			{
				echo '<input type="hidden" name="fil_id" value="'.$_GET['fil_id'].'" />';
?>Poster une r&eacute;ponse dans la discussion<?php
			}
			else if ($afaire == 'editpost')
			{
				echo '<input type="hidden" name="msg_id" value="'.$_GET['msg_id'].'" />';
?>Editer un message<?php
			} ?>
<input type="hidden" name="do" value="<?php echo $afaire; ?>" />
<input type="hidden" name="page" value="forum_post" /></h2>
<?php
			if ($afaire == 'editpost')
			{
?>
<p class="rmq">Auteur du message : <span class="pseudo"><?php echo untruc(PREFIXE_TABLES.'auteurs', 'pseudo', 'num', $message['msg_auteur']); ?></span></p>
<?php
			}
		
			if ($afaire == 'newpost')
			{
?>
<p class="icone"><span class="rmq">Ic&ocirc;ne</span><br />
  <input type="radio" name="fil_icone" id="icone065" value="065" checked="checked" />
  <label for="icone065"><img src="img/smileys/065.gif" alt="normale" width="14" height="9" /></label> 
  <input type="radio" name="fil_icone" id="icone001" value="001" />
  <label for="icone001"><img src="img/smileys/001.gif" alt="sourire" /></label> 
  <input type="radio" name="fil_icone" id="icone002" value="002" />
  <label for="icone002"><img src="img/smileys/002.gif" alt="f&agrave;ch&eacute;" /></label> 
  <input type="radio" name="fil_icone" id="icone003" value="003" />
  <label for="icone003"><img src="img/smileys/003.gif" alt="clin d'oeil" /></label> 
  <input type="radio" name="fil_icone" id="icone004" value="004" />
  <label for="icone004"><img src="img/smileys/004.gif" alt="d&eacute;&ccedil;u" /></label> 
  <input type="radio" name="fil_icone" id="icone006" value="006" />
  <label for="icone006"><img src="img/smileys/006.gif" alt="choc" /></label> 
  <input type="radio" name="fil_icone" id="icone007" value="007" />
  <label for="icone007"><img src="img/smileys/007.gif" alt="attention" /></label> 
  <input type="radio" name="fil_icone" id="icone008" value="008" />
  <label for="icone008"><img src="img/smileys/008.gif" alt="question" /></label> 
  <input type="radio" name="fil_icone" id="icone009" value="009" />
  <label for="icone009"><img src="img/smileys/009.gif" alt="g&eacute;nial" /></label> 
  <input type="radio" name="fil_icone" id="icone010" value="010" />
  <label for="icone010"><img src="img/smileys/010.gif" alt="nul" /></label> 
  <input type="radio" name="fil_icone" id="icone016" value="016" />
  <label for="icone016"><img src="img/smileys/016.gif" alt="help" /></label> 
  <input type="radio" name="fil_icone" id="icone020" value="020" />
  <label for="icone020"><img src="img/smileys/020.gif" alt="respect" /></label> 
  <input type="radio" name="fil_icone" id="icone025" value="025" />
  <label for="icone025"><img src="img/smileys/025.gif" alt="coucou" /></label> 
  <input type="radio" name="fil_icone" id="icone026" value="026" />
  <label for="icone026"><img src="img/smileys/026.gif" alt="triste" /></label></p>
<?php
			}
			$msg_titre = '';
			if (is_numeric($_GET['fil_id']))
			{ // Quand l'utilisateur poste une réponse dans une discussion, on ajoute Re : au titre du message
				$sql = 'SELECT msg_titre FROM '.PREFIXE_TABLES.'forum_msg WHERE fil_id = \''.$_GET['fil_id'].'\' AND msg_statut != \'1\' ORDER BY msg_id DESC';
				if ($res = send_sql($db, $sql))
				{
					if (mysql_num_rows($res) > 0)
					{
						$ligne = mysql_fetch_assoc($res);
						$msg_titre = $ligne['msg_titre'];
						if (strpos($msg_titre, 'Re :') === false) 
						{
							$msg_titre = 'Re : '.$msg_titre;
						}
					}
				}
			}
?>
<p class="titre">Titre : 
  <input name="msg_titre" type="text" tabindex="1" value="<?php echo (!empty($msg_titre)) ? $msg_titre : $message['msg_titre']; ?>" size="50" maxlength="50" />
</p>
<div class="texte">
<p class="rmq" style="float:left;"><?php echo ($afaire == 'newpost' or $afaire == 'editpost') ? 'Texte du message :' : 'Texte de la r&eacute;ponse :'; ?></p>
<?php panneau_mise_en_forme('msg_txt', true); ?>
  <textarea name="msg_txt" cols="70" rows="8" id="msg_txt" tabindex="2" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php 
			// On affiche éventuellement le texte du message à éditer
	  		echo ($afaire == 'editpost') ? untruc(PREFIXE_TABLES.'forum_msg_txt', 'msg_txt', msg_id, $_GET['msg_id']) : ''; 
?></textarea>
</div>
<?php
			if ($afaire == 'editpost' and is_moderateur($forum['forum_moderation'], $forum['fil_auteur']))
			{ // L'utilisateur modère un message
?>
<p align="center"> 
   
  <input name="is_moderation" type="checkbox" id="is_moderation" tabindex="3" value="1"<?php echo ($message['msg_moderateur'] != 0) ? ' checked="checked"' : ''; ?> />
  <label for="is_moderation">Marquer le message comme ayant &eacute;t&eacute; &eacute;dit&eacute;</label>
  <br />
  <span class="petitbleu">Coche cette case si tu as modifi&eacute; le texte du message (suppression d'un lien, d'insultes, ...)</span></p>
    <?php
				if ($user['niveau']['numniveau'] == 5 and $message['msg_statut'] == 1)
				{
?>
<p align="center"><span class="rmq">Le message n'appara&icirc;t plus sur le forum</span><br />
  
  <input name="unmoderate" type="checkbox" id="unmoderate" tabindex="4" value="1" />
  <label for="unmoderate">Publier &agrave; nouveau le message</label></p>
<?php
				}
			}
?>
<p align="center"><input type="submit" id="bouton_envoi_post" tabindex="5" value="<?php echo ($afaire == 'editpost') ? 'Enregistrer les modifications' : 'Envoyer'; ?>" />
</p>
<?php panneau_smileys('msg_txt'); ?>
</form>
<?php
		}
		else
		{
			$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';	
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <?php echo $forum['forum_titre']; ?></h2>
<?php		
			if (is_numeric($_GET['msg_id']) and !$can_edit_post and $user['num'] != $message['msg_auteur'])
			{ // l'utilisateur ne peut pas éditer le message
?>
<div class="msg"> 
    <p align="center">Tu n'es pas autoris&eacute; &agrave; modifier ce message !</p>
</div>
<?php
			}
			else if ($_GET['do'] == 'editpost' and !$can_edit_post and $user['num'] == $message['msg_auteur'])
			{ // 1 heure maximum pour éditer son propre message
?>
<div class="msg"> 
    <p align="center">Tu ne peux modifier ton message que pendant une heure apr&egrave;s 
      l'avoir post&eacute;.</p>
</div>
<?php
			}
			else
			{ // L'utilisateur n'est pas connecté
?>
<div class="msg"> 
  <p align="center">Merci de bien vouloir te connecter ou <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">devenir 
    membre</a> pour poster sur le forum.</p>
</div>
<?php
				include('login.php');
			}
		}
	}
	else if ($_POST['do'] == 'post' or $_POST['do'] == 'newpost' and $can_post)
	{
		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
		$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a></h2>
<?php		
		if ($user['niveau']['numniveau'] > 0)
		{
			$msg_titre = htmlentities($_POST['msg_titre'], ENT_QUOTES);
			$h_post = mysql_time_date(); // heure du post (on la prend une fois pour avoir une même heure partout)
			if (!empty($msg_titre))
			{
				$nouveau_fil = '';
				if ($_POST['do'] == 'newpost')
				{
					$sql = "INSERT INTO ".PREFIXE_TABLES."forum_fils (forum_id, fil_auteur, fil_icone, fil_titre, fil_date, fil_statut) values ('$forum_id', '$user[num]', '$_POST[fil_icone]', '$msg_titre', '$h_post', '1')";
					send_sql($db, $sql);
					$sql = "SELECT fil_id FROM ".PREFIXE_TABLES."forum_fils WHERE fil_auteur = '$user[num]' ORDER BY fil_id DESC LIMIT 1";
					$res = send_sql($db, $sql);
					$ligne = mysql_fetch_assoc($res);
					$fil_id = $ligne['fil_id'];
					$nouveau_fil = ", forum_nbfils = forum_nbfils + 1";
					send_sql($db, $sql);
				}
				$msg_txt = htmlentities($_POST['msg_txt'], ENT_QUOTES);
				$fil_id = (is_numeric($_POST['fil_id'])) ? $_POST['fil_id'] : $fil_id;
				$sql = "INSERT INTO ".PREFIXE_TABLES."forum_msg (fil_id, forum_id, msg_auteur, msg_titre, msg_date) values ($fil_id, $forum[forum_id], '$user[num]', '$msg_titre', '$h_post')";
				send_sql($db, $sql);
				$sql = "SELECT msg_id, forum_id FROM ".PREFIXE_TABLES."forum_msg WHERE msg_auteur = '$user[num]' AND msg_titre = '$msg_titre' AND fil_id = '$fil_id' ORDER BY msg_id DESC LIMIT 1";
				$res = send_sql($db, $sql);
				$ligne = mysql_fetch_assoc($res);
				$msg_id = $ligne['msg_id'];
				$forum_id = $ligne['forum_id'];
				$sql = "INSERT INTO ".PREFIXE_TABLES."forum_msg_txt (msg_id, msg_txt) values ('$msg_id', '$msg_txt')";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET fil_last_msg_id = '$msg_id', fil_nbmsg = fil_nbmsg + 1 WHERE fil_id = '$fil_id'";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET forum_last_msg_id = '$msg_id', forum_nbmsg = forum_nbmsg + 1 $nouveau_fil WHERE forum_id = '$forum_id'";
				send_sql($db, $sql);
?>
<div class="msg">
<p align="center">Merci, ton message a bien &eacute;t&eacute; post&eacute;. Tu peux le lire ici : <?php 
				$lien_msg = ($site['url_rewriting_actif'] == 1) ? 'fil'.$fil_id.'.htm#msg'.$msg_id : 'index.php?page=forum_fil&amp;fil='.$fil_id.'#msg'.$msg_id;
				echo '<a href="'.$lien_msg.'">Sujet n° '.$fil_id.'.</a>'; ?></p>
</div>
<?php
				if ($_POST['do'] == 'newpost' and !is_moderateur($forum['forum_moderation']) and is_moderateur($forum['forum_moderation'], $user['num']))
				{ // L'utilisateur peut modérer la discussion qu'il vient de lancer (uniquement)
?>
<div class="msg">
<h2>Attention</h2>
<p>La discussion que tu viens de lancer est maintenant entre tes mains. 
<strong>A toi de la g&eacute;rer</strong> comme tu l'entends.<br />
  Si tu estimes qu'un membre d&eacute;rape tu peux supprimer ses interventions 
  de la discussion.</p>
<p>Bien entendu, nous te demandons de faire &ccedil;a intelligemment, 
  la libert&eacute; d'expression, &ccedil;a existe aussi <img src="img/smileys/003.gif" alt="" width="15" height="15" align="middle" /></p>
<p class="petit"><strong>Rmq</strong> : &quot;les paroles s'envolent, 
  les &eacute;crits restent.&quot; Autrement dit, tout abus de cette fonction 
  sera sanctionn&eacute;... </p></div>
<?php
				}
				else if (is_moderateur($forum['forum_moderation']))
				{ // L'utilisateur peut modérer le forum complet
?>
<div class="msg">
<h2>Attention</h2>
<p>Tu disposes d'un droit de  mod&eacute;ration 
  de ce forum. <strong>A toi de le g&eacute;rer</strong> comme tu l'entends.<br />
  Si tu estimes qu'un membre d&eacute;rape tu peux supprimer ses interventions 
  de la discussion.</p>
<p>Bien entendu, nous te demandons de faire &ccedil;a intelligemment, 
  la libert&eacute; d'expression, &ccedil;a existe aussi <img src="img/smileys/003.gif" alt="" width="15" height="15" align="middle" /></p>
<p class="petit"><strong>Rmq</strong> : &quot;les paroles s'envolent, 
  les &eacute;crits restent.&quot; Autrement dit, tout abus de cette fonction 
  sera sanctionn&eacute;... </p></div>
<?php
				}
			}
			else
			{
?>
<div class="msg">
<p align="center">D&eacute;sol&eacute; ton message n'a pas &eacute;t&eacute; 
enregistr&eacute;</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
  <p align="center">Merci de bien vouloir te connecter ou <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">devenir 
    membre</a> pour poster sur le forum.</p>
</div>
<?php
			include('login.php');
		}
	}
	else if ($_POST['do'] == 'editpost' and is_numeric($_POST['msg_id']))
	{ // un animateur ou l'auteur du message veut modifier un message du forum
		$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
		$lien_forum_x = ($site['url_rewriting_actif'] == 1) ? 'forum'.$forum['forum_id'].'.htm' : 'index.php?page=forum&f='.$forum['forum_id'];
?>
<h2><a href="<?php echo $lien_forum; ?>">Forums</a> - <a href="<?php echo $lien_forum_x; ?>"><?php echo $forum['forum_titre']; ?></a></h2>
<?php		
		$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_msg as m, ".PREFIXE_TABLES."forum_msg_txt as t WHERE m.msg_id = t.msg_id AND m.msg_id = '".$_POST['msg_id']."' LIMIT 1";
		$res = send_sql($db, $sql);
		$message = mysql_fetch_assoc($res);
		if ($can_edit_post or ($user['num'] == $message['msg_auteur'] and temps_ecoule($message['msg_date'], 0, true) < 3900))
		{ // on donne 5 minutes de plus à l'auteur du message s'il était un peu juste :)
			$msg_titre = htmlentities($_POST['msg_titre'], ENT_QUOTES);
			if (!empty($msg_titre))
			{
				// on vérifie si le message à éditer ne serait pas le premier du fil
				$sql = "SELECT MIN(msg_id) as msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE fil_id = '$message[fil_id]'";
				$res = send_sql($db, $sql);
				$premier = mysql_fetch_assoc($res);
				if ($premier['msg_id'] == $_POST['msg_id'])
				{ // Il s'agit du premier post du fil, on met le titre à jour
					$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET 
					fil_titre = '$msg_titre' 
					WHERE fil_id = '$message[fil_id]' LIMIT 1";
					send_sql($db, $sql);
				}

				// On récupère l'id du dernier message du fil
				$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_fils WHERE fil_id = '$message[fil_id]' LIMIT 1";
				$res = send_sql($db, $sql);
				$fil = mysql_fetch_assoc($res);
				$msg_txt = htmlentities($_POST['msg_txt'], ENT_QUOTES);
				$plus = '';
				if (is_moderateur($forum['forum_moderation'], $forum['fil_auteur']) and $_POST['is_moderation'] == 1)
				{ // l'animateur marque ostensiblement le message comme édité
					$plus = ", msg_moderateur = '$user[num]'";
				}
				else if ($user['niveau']['numniveau'] > 2 and $message['msg_statut'] == 0 and empty($_POST['is_moderation']))
				{ // le message n'est pas banni et l'animateur n'en fait pas une modération
					$plus = ", msg_moderateur = '0'";
				}
				if ($user['niveau']['numniveau'] == 5 and $_POST['unmoderate'] == 1 and $message['msg_statut'] == 1)
				{ // le webmaster réhabilite un message
					$plus .= (empty($plus)) ? ", msg_statut = '0', msg_moderateur = '0'" : ", msg_statut = '0'";
					// on remet le compteur de posts du fil à jour
					// on vérifie si le post réhabilité n'est pas le plus récent du fil
					$new_last_msg_id = ($fil['fil_last_msg_id'] != $message['msg_id']) ? ", fil_last_msg_id = '$message[msg_id]'" : '';
					$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET 
					fil_nbmsg = fil_nbmsg + 1 
					$new_last_msg_id 
					WHERE fil_id = '$message[fil_id]' LIMIT 1";
					send_sql($db, $sql);

					// on remet le compteur de posts du forum à jour
					$sql = "SELECT * FROM ".PREFIXE_TABLES."forum_forums WHERE forum_id = '".$message['forum_id']."'";
					$res = send_sql($db, $sql);
					$forum = mysql_fetch_assoc($res);
					$new_last_msg_id = ($forum['forum_last_msg_id'] != $message['msg_id']) ? ", forum_last_msg_id = '$message[msg_id]'" : '';
					$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET 
					forum_nbmsg = forum_nbmsg + 1
					$new_last_msg_id 
					WHERE forum_id = '$message[forum_id]' LIMIT 1";
					send_sql($db, $sql);
				}
				// on met le message à jour
				$sql = "UPDATE ".PREFIXE_TABLES."forum_msg SET msg_titre = '$msg_titre'$plus WHERE msg_id = '$_POST[msg_id]' LIMIT 1";
				send_sql($db, $sql);
				$sql = "UPDATE ".PREFIXE_TABLES."forum_msg_txt SET msg_txt = '$msg_txt' WHERE msg_id = '$_POST[msg_id]' LIMIT 1";
				send_sql($db, $sql);
				// on loggue l'édition du message
				log_this('Edition message '.$_POST['msg_id'].' du forum '.$forum['forum_titre'], 'forum');
				$lien_msg = ($site['url_rewriting_actif'] == 1) ? 'fil'.$message['fil_id'].'.htm#msg'.$_POST['msg_id'] : 'index.php?page=forum_fil&amp;fil='.$message['fil_id'].'#msg'.$_POST['msg_id'];
?>
<div class="msg">
<p align="center">Le message a bien &eacute;t&eacute; modifi&eacute;.</p>
<p align="center"><a href="<?php echo $lien_msg; ?>">Retour au sujet n°<?php echo $message['fil_id']; ?>.</a></p>
</div>
<?php
			}
			else
			{
?>
<div class="msg">
<p align="center">D&eacute;sol&eacute; les modifications n'ont pas &eacute;t&eacute; 
enregistr&eacute;es</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
  <p align="center">Tu n'as pas l'autorisation de modifier ce message.</p>
</div>
<?php
		}
	}
	else if ($_GET['do'] == 'mod' and is_numeric($_GET['fil_id']) and is_numeric($_GET['msg_id']))
	{ // confirmation pour modérer un message d'une discussion
		$lien_msg = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_GET['fil_id'].'.htm#msg'.$_GET['msg_id'] : 'index.php?page=forum_fil&amp;fil='.$_GET['fil_id'].'#msg'.$_GET['msg_id'];
?>
<div class="action">
  <p align="center" class="rmq">Es-tu certain de vouloir retirer ce message du forum ?</p>
  <p align="center">
  <a href="<?php echo 'index.php?page=forum_post&amp;do=domod&amp;msg_id='.$_GET['msg_id'].'&amp;fil_id='.$_GET['fil_id']; ?>" class="bouton">OUI</a>
  <a href="<?php echo $lien_msg; ?>" class="bouton">NON</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'domod' and is_numeric($_GET['msg_id']) and is_numeric($_GET['fil_id']))
	{ // modération d'un message du forum
		if ($user['niveau']['numniveau'] > 0)
		{
			$sql = 'SELECT fil_auteur, fil_nbmsg, fil_last_msg_id FROM '.PREFIXE_TABLES.'forum_fils WHERE fil_id = \''.$_GET['fil_id'].'\'';
			if ($res = send_sql($db, $sql))
			{
				if (mysql_num_rows($res) == 1) 
				{
					$fil = mysql_fetch_assoc($res);
					if (is_moderateur($forum['forum_moderation'], $fil['fil_auteur']))
					{ // L'utilisateur est bien modérateur
						// On bannit le message
						$sql = "UPDATE ".PREFIXE_TABLES."forum_msg SET msg_statut = '1', msg_moderateur = '$user[num]' WHERE msg_id = '$_GET[msg_id]'";
						send_sql($db, $sql);
						if ($fil['fil_nbmsg'] == 1)
						{ // le dernier message de la discussion est banni, on la ferme
							$filmodo = ", fil_statut = '0', fil_moderateur = '$user[num]'";
							log_this('Modération discussion '.$forum['fil_titre'].' ('.$_GET['fil_id'].') sur le forum '.$forum['forum_titre'], 'forum');
						}
						else
						{ // On met à jour le message le plus récent de la discussion
							if ($fil['fil_last_msg_id'] == $_GET['msg_id'])
							{
								$sql = "SELECT max(msg_id) as msg_id FROM ".PREFIXE_TABLES."forum_msg WHERE fil_id = '".$_GET['fil_id']."' and msg_statut != '1'";
								$res = send_sql($db, $sql);
								$ligne = mysql_fetch_assoc($res);
								$fil['fil_last_msg_id'] = $ligne['msg_id'];
							}
							$filmodo = ", fil_last_msg_id = '$fil[fil_last_msg_id]'";
							log_this('Modération message '.$_GET['msg_id'].' dans la discussion '.$forum['fil_titre'].' sur le forum '.$forum['forum_titre'], 'forum');
						}
						// Mise à jour du nombre de messages dans la discussion
						$sql = "UPDATE ".PREFIXE_TABLES."forum_fils SET fil_nbmsg = fil_nbmsg - 1$filmodo WHERE fil_id = '$_GET[fil_id]'";
						send_sql($db, $sql);

						// Mise à jour du nombre de messages et du dernier message dans le forum
						$sql = "SELECT max(fil_last_msg_id) as forum_last_msg_id FROM ".PREFIXE_TABLES."forum_fils WHERE forum_id = '$forum[forum_id]'";
						$res = send_sql($db, $sql);
						if (mysql_num_rows($res) == 1)
						{ // le forum contient au moins une discussion
							$ligne = mysql_fetch_assoc($res);
							$forum_last_msg_id = ", forum_last_msg_id = '".$ligne['forum_last_msg_id']."'";
						}
						else
						{ // le forum est vide
							$forum_last_msg_id = ", forum_last_msg_id = '0'";
						}
						$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET forum_nbmsg = forum_nbmsg - 1 $forum_last_msg_id WHERE forum_id = '$forum[forum_id]'";
						send_sql($db, $sql);
						$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_GET['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$_GET['fil_id'];
?>
<div class="msg">
  <p align="center">Le message est d&eacute;sormais banni.</p>
  <p align="center"><a href="<?php echo $lien_fil; ?>">Retour au fil n°<?php echo $_GET['fil_id']; ?></a></p>
</div>
<?php
					}
					else
					{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas les droits de mod&eacute;rateur sur ce message !</p>
</div>
<?php
					}
				}
				else
				{
					$lien_forum = ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum';
?>
<div class="msg">
  <p align="center" class="rmq">Cette discussion n'existe pas !</p>
  <p align="center"><a href="<?php echo $lien_forum; ?>">Retour au forum</a></p>
</div>
<?php
				}
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Tu n'as pas les droits de mod&eacute;rateur sur ce message !</p>
</div>
<?php
		}
	}
	if ($_GET['do'] == 'suppr' and is_numeric($_GET['msg_id']) and is_numeric($_GET['fil_id']) and $user['niveau']['numniveau'] == 5)
	{ // Demande de confirmation suppression définitive d'un post banni au préalable
		$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_GET['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$_GET['fil_id'];
?>
<div class="action">
  <p align="center">Es-tu certain de vouloir supprimer d&eacute;finitivement ce message du forum ?</p>
  <p align="center"><a href="<?php echo 'index.php?page=forum_post&amp;do=dosuppr&amp;msg_id='.$_GET['msg_id'].'&amp;fil_id='.$_GET['fil_id']; ?>" class="bouton">OUI</a>
	<a href="<?php echo $lien_fil; ?>" class="bouton">NON</a></p>
</div>
<?php
	}
	else if ($_GET['do'] == 'dosuppr' and is_numeric($_GET['msg_id']) and is_numeric($_GET['fil_id']) and $user['niveau']['numniveau'] == 5)
	{ // Suppression définitive d'un post banni au préalable
		// On regarde combien de messages il reste dans la discussion
		$sql = 'SELECT msg_id, forum_id FROM '.PREFIXE_TABLES.'forum_msg WHERE fil_id = \''.$_GET['fil_id'].'\'';
		$res = send_sql($db, $sql);
		if (mysql_num_rows($res) == 1)
		{ // la discussion ne contient plus de messages si on supprime le dernier
			$ligne = mysql_fetch_assoc($res);
			$fil_forum_id = $ligne['forum_id'];
			$sql = "DELETE FROM ".PREFIXE_TABLES."forum_fils WHERE fil_id = '".$_GET['fil_id']."'";
			send_sql($db, $sql);
			$sql = "UPDATE ".PREFIXE_TABLES."forum_forums SET forum_nbfils = forum_nbfils - 1 WHERE forum_id = '".$fil_forum_id."'";
			send_sql($db, $sql);
			log_this('Suppression discussion '.$forum['fil_titre'].' ('.$_GET['fil_id'].') sur le forum '.$forum['forum_titre'], 'forum');
		}
		$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg WHERE msg_id = '".$_GET['msg_id']."'";
		send_sql($db, $sql);
		$sql = "DELETE FROM ".PREFIXE_TABLES."forum_msg_txt WHERE msg_id = '".$_GET['msg_id']."'";
		send_sql($db, $sql);
		log_this('Suppression du message '.$_GET['msg_id'].' sur le forum', 'forum');
		$lien_fil = ($site['url_rewriting_actif'] == 1) ? 'fil'.$_GET['fil_id'].'.htm' : 'index.php?page=forum_fil&amp;fil='.$_GET['fil_id'];
?>
<div class="msg">
  <p align="center">Le message a &eacute;t&eacute; supprim&eacute;.</p>
  <p align="center"><a href="<?php echo $lien_fil; ?>">Retour au fil n&deg;<?php echo $_GET['fil_id']; ?></a></p>
</div>
<?php
	}
?>
<p align="center" class="petitbleu"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'moderationforum.htm' : 'index.php?page=moderationforum'; ?>" class="lienmort" title="Plus d'infos">Plus 
  d'infos au sujet du forum</a></p> 
</div><?php /*Fin div id forum */ ?>
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