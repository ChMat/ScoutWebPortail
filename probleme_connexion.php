<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* probleme_connexion.php - Quelques infos utiles en cas de problèmes pour se connecter comme membre du portail
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

if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Les News du site en RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Des probl&egrave;mes de connexion ?</h1>
<p class="petitbleu">Plusieurs personnes ont dit avoir des probl&egrave;mes pour s'identifier sur 
  le portail afin d'avoir acc&egrave;s aux fonctions avanc&eacute;es. Voici quelques 
  pistes pour r&eacute;soudre ces probl&egrave;mes.</p>
<p><span class="rmq">Probl&egrave;me 1</span> - <strong>Je voudrais me connecter 
  mais je ne sais pas ce que je dois faire.</strong></p>
<p>Pour te connecter, tu dois d'abord <a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">t'inscrire sur 
  le portail comme membre</a>.</p>
<p><span class="rmq">Probl&egrave;me 2</span> - <strong>J'ai oubli&eacute; mon 
  pseudo et mon de passe.</strong></p>
<p>Consulte la page &quot;<a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'listembsite.htm' : 'index.php?page=listembsite'; ?>">Liste des Membres</a>&quot; 
  et retrouve ton pseudo dans la liste. Ensuite, dirige-toi vers la page &quot;<a href="index.php?page=newpw">Mot 
  de passe oubli&eacute;</a>&quot; pour le renouveler.</p>
<p><span class="rmq"></span><span class="rmq">Probl&egrave;me 3</span> - <strong>J'ai 
  beau faire, la <a href="index.php?page=login">page de connexion</a> me renvoie un message 
  d'erreur : &quot;pseudo ou mot de passe incorrect&quot;.</strong></p>
<p>Dans ce cas, passe par la page &quot;<a href="index.php?page=newpw">Mot 
  de passe oubli&eacute;</a>&quot; pour le renouveler.</p>
<p><span class="rmq">Probl&egrave;me 4</span> - <strong>Quand je me connecte, 
  apr&egrave;s avoir entr&eacute; mon pseudo et mon mot de passe sur la <a href="index.php?page=login">page 
  de connexion</a>, j'arrive sur une <a href="404.html">page d'erreur</a>.</strong></p>
<p>Sur Internet Explorer (le programme que tu utilises probablement pour voir 
  cette page), dirige-toi dans le menu &quot;Outils&quot; &gt; &quot;Options Internet&quot;. 
  Dans l'onglet &quot;G&eacute;n&eacute;ral&quot;, on parle quelque part de &quot;Cookies&quot; 
  et un bouton propose de les supprimer. Fais-le. Ensuite, ferme toutes les fen&ecirc;tres 
  Internet et ouvre &agrave; nouveau le portail. R&eacute;essaie de 
  te connecter.</p>
<p class="petit">Les cookies n'ont rien de dangereux, ce sont des informations sur tes pr&eacute;f&eacute;rences 
  que de nombreux sites web laissent sur ton ordinateur. Les virus informatiques 
  ne voyagent pas par l'interm&eacute;diaire des cookies. Les cookies du portail
  servent &agrave; d&eacute;terminer si tu es d&eacute;j&agrave; 
  venu, &agrave; se souvenir de ton pseudo et &agrave; 
  d&eacute;terminer si tu es connect&eacute; sur le portail comme membre.</p>
<p><span class="rmq">Probl&egrave;me 5</span> <strong>- Depuis que mon &eacute;pouse 
  est connect&eacute;e sur le portail, je ne sais plus me connecter avec mon propre 
  pseudo.</strong></p>
<p>En haut de la page, tu devrais avoir un bouton intitul&eacute; &quot;D&eacute;connexion&quot;. Clique dessus. Un message devrait te demander 
  une confirmation, clique sur ok. Un message s'affiche pour te dire que tu t'es 
  d&eacute;connect&eacute; du portail. Rejoins ensuite la <a href="index.php?page=login">page 
  de Connexion</a> et essaie de te connecter avec ton propre pseudo.<br />
  Si cette premi&egrave;re solution ne fonctionne pas, applique la solution propos&eacute;e 
au <em>probl&egrave;me 4.</em></p>
<h2>Aucune des solutions propos&eacute;es ne r&eacute;pond &agrave; mon probl&egrave;me</h2>
<p>Dans ce cas, envoie un email au webmaster en cliquant sur son pseudo en bas 
  &agrave; gauche de la page. D&eacute;cris ton probl&egrave;me de la mani&egrave;re 
  la plus pr&eacute;cise qui soit en mentionnant toutes les &eacute;tapes qui 
  te font retomber sur ce probl&egrave;me.</p>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
?>