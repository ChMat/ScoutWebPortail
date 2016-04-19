<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* help.php - Aide à la mise en page avec bbcodes
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
<title>Aide pour la mise en forme du texte</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>

<body leftmargin="1" topmargin="1" marginwidth="3" marginheight="3" class="body_popup">
<?php
}
?>
<h2>Mise en forme du texte</h2> 
<div>
  <p>Tu peux &eacute;ventuellement donner &agrave; ton message quelques petits 
    plus.</p>
  <p><span class="rmq">* Texte gras</span> (alt + b) <br />
    S&eacute;lectionne le texte &agrave; mettre en gras et clique sur le bouton 
  gras ou place le texte entre <code>[b]</code> et <code>[/b]</code></p>
  <p><span class="rmq">* Texte rouge</span> (alt + r)<br />
  M&ecirc;me chose que pour le gras mais avec <code>[rouge]</code> et <code>[/rouge]</code></p>
  <p><span class="rmq">* Texte bleu</span> (alt + e)  <br />
  M&ecirc;me chose que pour le gras mais avec <code>[bleu]</code> et <code>[/bleu]</code> </p>
  <p class="petitbleu">Sauf indication contraire, seules les couleurs bleue et rouge peuvent &ecirc;tre appliqu&eacute;es de cette mani&egrave;re.</p>
  <p><span class="rmq">* Texte italique</span> (alt + i) <br />
    M&ecirc;me chose que pour le gras mais avec <code>[i]</code> et <code>[/i]</code></p>
  <p><span class="rmq">* Texte soulign&eacute;</span> (alt + u) <br />
    Idem mais avec <code>[u]</code> et <code>[/u]</code></p>
  <p>    <span class="rmq">* Texte centr&eacute;</span><br />
    Idem mais avec <code>[c]</code> et <code>[/c]</code></p>
  <p>    <span class="rmq">* Ins&eacute;rer un lien Internet</span> (alt + w) <br />
    R&eacute;dige l'adresse en la faisant pr&eacute;c&eacute;der de <code>http://</code> ou comme ceci :<br />
    <code>[url=</code>adresse_du_lien<code>]</code>texte du lien<code>[/url]</code></p>
  <p>    <span class="rmq">* Ins&eacute;rer un lien email</span> (alt + m) <br />
    suis l'exemple :<br />
    <code>[mail=</code>moi@youpie.com<code>]</code>&eacute;cris-moi<code>[/mail]</code></p>
  <p><span class="rmq">* Ins&eacute;rer une citation</span> (alt + c) <br />
  Emballe la citation entre <code>[quote]</code> et <code>[/quote]</code> </p>
  <p><span class="rmq">* Ins&eacute;rer du code</span> (alt + k) <br />
    Emballe le code entre <code>[code]</code> et <code>[/code]</code> </p>
  <p>Quelques <span class="rmq">balises html</span> sont &eacute;galement autoris&eacute;es si tu souhaites structurer un peu plus ta pens&eacute;e : <code>&lt;h1&gt; &agrave; &lt;h6&gt;, &lt;p&gt;, &lt;i&gt;, &lt;b&gt;, &lt;em&gt;, &lt;strong&gt;, &lt;q&gt;, &lt;u&gt; et &lt;pre&gt;</code></p>
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
