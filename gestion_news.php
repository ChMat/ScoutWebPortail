<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* gestion_news.php v 1.1 - Gestion des News du portail
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
*	Optimisation xhtml
*	modification noms de champs (+ titre des news)
*/

if (!defined('IN_SITE'))
{
	include_once('connex.php');
	include_once('fonc.php');
}
if ($user['niveau']['numniveau'] < 3)
{
	include('404.php');
	exit;
}
if ($_GET['do'] == 'create' or (empty($_GET['do']) and empty($_POST['do'])))
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des News du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
	}
}
if ($_GET['do'] == 'create' or (empty($_GET['do']) and empty($_POST['do'])))
{
?>
<h1>Gestion des News</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
<form action="gestion_news.php" method="post" name="formulaire" id="formulaire" class="form_config_site">
<h2>Ajouter une News</h2>
  <input type="hidden" name="do" value="save" />
<p class="petit">Le texte que tu entres ici apparaitra en premi&egrave;re page du portail aussi longtemps 
  que personne n'ajoutera une nouvelle news.<br />
  V&eacute;rifie bien l'orthographe de ton texte <img src="img/smileys/023.gif" width="20" height="19" alt="" /> 
</p>
<p>Titre : <input type="text" size="50" maxlength="100" name="titre_news" /></p>
<?php panneau_mise_en_forme('texte_news', true); ?>
<textarea name="texte_news" id="texte_news" rows="6" cols="70" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"></textarea>
<p align="center" class="petit">bbcodes autoris&eacute;s mais pas le code html 
pur</p>
<p align="center">
  <input type="submit" name="envoi" value="Envoyer" />
  <input type="reset" name="reset" value="Recommencer" />
</p>
<?php panneau_smileys('texte_news'); ?>
</form>
<?php
}
else if ($_GET['do'] == 'modif' and is_numeric($_GET['id_news']))
{
	$sql = "SELECT texte_news, titre_news FROM ".PREFIXE_TABLES."news WHERE id_news = '$_GET[id_news]'";
	$res = send_sql($db, $sql);
	if (mysql_num_rows($res) == 1)
	{
		$news = mysql_fetch_assoc($res);
?>
<h1>Gestion des News</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
<form action="gestion_news.php" method="post" name="formulaire" id="formulaire" class="form_config_site">
<h2>Editer une News</h2>
<input type="hidden" name="do" value="savemodif" />
<input type="hidden" name="id_news" value="<?php echo $_GET['id_news']; ?>" />
<p class="petit"> V&eacute;rifie bien l'orthographe 
de ton texte <img src="img/smileys/023.gif" width="20" height="19" alt="" /></p>
<p>Titre : <input type="text" size="50" maxlength="100" name="titre_news" value="<?php echo stripslashes($news['titre_news']); ?>" /></p>
<?php panneau_mise_en_forme('texte_news', true); ?>
<textarea name="texte_news" id="texte_news" rows="6" cols="70" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);"><?php echo stripslashes($news['texte_news']); ?></textarea>
<p align="center" class="petit">bbcodes autoris&eacute;s mais pas le code html pur</p>
<p align="center">
  <input type="submit" name="envoi" value="Envoyer" />
  <input type="reset" name="reset" value="Recommencer" />
</p>
<?php panneau_smileys('texte_news'); ?>
</form>
<?php
	}
	else
	{
?>
<h1>Gestion des News</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
<div class="msg">
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<p align="center"><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Retour &agrave; la page 
  pr&eacute;c&eacute;dente</a></p>
</div>
<?php	
	}
}
else if ($_GET['do'] == 'suppr' and is_numeric($_GET['id_news']))
{
?>
<h1>Gestion des News</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
<form action="gestion_news.php" method="post" name="form1" id="form1" class="form_config_site">
<h2>Supprimer une News</h2>
<p align="center">Es-tu certain de vouloir supprimer cette News ?</p>
<p align="center">
    <input type="hidden" name="do" value="dosuppr" />
    <input type="hidden" name="id_news" value="<?php echo $_GET['id_news']; ?>" />
    <input type="submit" name="Submit" value=" Oui " />
    &nbsp;
    <input type="button" name="Button" value=" Non " onclick="window.location='<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>';" />
</p>
</form>
<?php
}
else if ($_POST['do'] == 'save')
{
	if ($user['niveau']['numniveau'] > 2)
	{
		$texte_news = htmlentities($_POST['texte_news'], ENT_QUOTES);
		$titre_news = htmlentities($_POST['titre_news'], ENT_QUOTES);
		$sql = "INSERT INTO ".PREFIXE_TABLES."news (titre_news, texte_news, datecreation, news_bannie, auteur_news) values ('$titre_news', '$texte_news', now(), '0', '$user[num]')";
		send_sql($db, $sql);
		log_this("Nouvelle news : $titre_news", 'gestion_news', true);
		include('rss_maker.php');
		header('Location: index.php?page=gestion_news&do=ok');
		exit;
	}
	else
	{
		header('Location: index.php?page=gestion_news&do=erreur');
		exit;
	}
}
else if ($_POST['do'] == 'savemodif' and is_numeric($_POST['id_news']))
{
	if ($user['niveau']['numniveau'] > 2)
	{
		$titre_news = htmlentities($_POST['titre_news'], ENT_QUOTES);
		$texte_news = htmlentities($_POST['texte_news'], ENT_QUOTES);
		$sql = "UPDATE ".PREFIXE_TABLES."news SET
		titre_news = '$titre_news', texte_news = '$texte_news' WHERE id_news = '$_POST[id_news]'";
		send_sql($db, $sql);
		log_this("Modification news $_POST[id_news] :\n$titre_news", 'gestion_news', true);
		include('rss_maker.php');
		header('Location: index.php?page=gestion_news&do=okmodif');
		exit;
	}
	else
	{
		header('Location: index.php?page=gestion_news&do=erreur');
		exit;
	}
}
else if ($_POST['do'] == 'dosuppr' and is_numeric($_POST['id_news']))
{
	if ($user['niveau']['numniveau'] > 2)
	{
		$sql = "UPDATE ".PREFIXE_TABLES."news SET news_bannie = '1' WHERE id_news = '$_POST[id_news]' LIMIT 1";
		send_sql($db, $sql);
		log_this("Suppression news $_POST[id_news]", 'gestion_news', true);
		include('rss_maker.php');
		header('Location: index.php?page=gestion_news&do=oksuppr');
		exit;
	}
	else
	{
		header('Location: index.php?page=gestion_news&do=erreur');
		exit;
	}
}
else if ($_GET['do'] == 'ok')
{
?>
<h1>Gestion des News</h1>
<div class="msg">
<p align="center">La news a &eacute;t&eacute; ajout&eacute;e</p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'okmodif')
{
?>
<h1>Gestion des News</h1>
<div class="msg">
<p align="center">La news a &eacute;t&eacute; modifi&eacute;e</p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
</div>
<?php
}
else if ($_GET['do'] == 'oksuppr')
{
?>
<h1>Gestion des News</h1>
<div class="msg">
<p align="center">La news a &eacute;t&eacute; supprim&eacute;e</p>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
</div>
<?php
}
else
{
?>
<h1>Gestion des News</h1>
<p align="center"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'news.htm' : 'index.php?page=news'; ?>">Retour &agrave; la page des News</a></p>
<div class="msg">
<p align="center" class="rmq">D&eacute;sol&eacute;, cette action n'est pas possible !</p>
<p align="center"><a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">Retour &agrave; la page 
  pr&eacute;c&eacute;dente</a></p>
</div>
<?php
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>