<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_str.php v 1.1.1 - Fonctions de travail sur les cha�nes de caract�res : html, bbcodes, ...
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
*	Suppression des guillemets dans la regexp pour les balises [img=]
*	Ajout balises <h1> � <h6> dans le bbcode
*	Correction de la reconnaissance des liens externes
*	Ajout d'espaces autour de certains codes smileys afin d'�viter de smileyser � tout va :)
*	Modification en profondeur de makehtml()
*		- suppression de [span class=""] et remplacement par [bleu] [rouge] [petit] [petitbleu]
*		- reconnaissance correcte des balises [code] et [quote]
*		- le texte n'est pas bbpars� s'il ne contient pas de balises bbcode (laissons souffler le serveur)
*		- mod�ration du stripslashes pour �viter la suppression de \ dans les posts
*	Modification de la fonction html_entity_decode()
*		Mais le hic n'est pas r�gl� (le stripslashes de makehtml)
*		Un probl�me se pose au niveau de l'affichage dans le textarea de pagesectionpost (htmlspecialchars chiant)
*		Remplacement de 'ENT_QUOTES' par ENT_QUOTES pour �viter les non-reconnaissances du param�tre
*	Reconnaissance des majuscules dans les adresses email
*	Nouvelle protection des adresses email via javascript
* Modifications v 1.1.1
*	bug 21/11 : Infobulle de la balise [code] corrig�e ([/code] �tait transform� en </pre>)
*	bug 21/11 : Protection d'un smiley :( souvent interpr�t� dans le code html
*	Ajout de la balise <code> aux balises autoris�es
*/


function checkforbiddenchar($atester)
{
	$interdits = array (' ');
	$max = count($interdits) - 1; // on parcourt les indices et pas le nombre de champs
	$retour = false;
	$i = 0;
	while (($i <= $max) && (!$retour))
	{
		$retour = stristr($atester, $interdits[$i]);
		$i++;
	}
	return $retour;
}

function hidemail($email, $retour = 'adresse', $texte_lien = '')
{ // rend l'adresse email illisible par les spammers automatiques
  // on d�monte l'adresse email et seul le javascript la recompose
  // si $retour vaut lien, la fonction �crit un appel � la fonction qui produit un lien
  // sinon elle renvoie l'adresse masqu�e via un simple document.write o� l'adresse est scind�e
  // Si � l'avenir les moteurs de spam �voluent, on fera �voluer la fonction
  	if (checkmail($email))
	{ // $email est bien un email
		$email = explode('@', $email);
		if ($retour == 'lien')
		{ // on renvoie le lien avec l'adresse email
			$retour = '<script type="text/javascript">aff_email("'.$email[0].'", "'.$email[1].'");</script>';
		}
		else if (!empty($texte_lien))
		{ // on renvoie le lien avec un texte pour le lien
			$retour = '<script type="text/javascript">aff_email("'.$email[0].'", "'.$email[1].'", "'.$texte_lien.'");</script>';
		}
		else
		{ // on renvoie l'adresse email sans lien mais masqu�e.
			$retour = '<script type="text/javascript">document.write("'.$email[0].'"+"@"+"'.$email[1].'");</script>';
		}
	}
	else
	{ // $email n'est pas un email valide
		$retour = $email;
	}
	return $retour;
}

function checkmail($email, $retour = '')
{ // v�rification de la validit� d'une adresse email
  // elle renvoie vrai ou faux si $retour est vide et l'adresse email si $retour vaut string
  // la fonction devra probablement �voluer pour s'adapter aux noms de domaine internationaux (accents, ...)
  	$email = strtolower($email); // on passe l'email en minuscules pour standardiser son apparence
	if (preg_match("!^[-a-z0-9\._]+@[-a-z0-9\._]{2,}\.[a-z]{2,4}$!", $email))
	{
		return ($retour == 'string') ? $email : true;
	}
	else
	{
		return ($retour == 'string') ? '' : false;
	}
}

function nettoie($str, $cols = 60, $cut = "\n")
{ // Cette fonction ajoute des sauts de ligne tous les $cut caract�res si aucun espace n'est trouv� sur ces caract�res
  // Cette fonction n'agit pas � l'int�rieur des balises html (entre < et > )
  // Elle permet � la mise en page du site de ne pas �tre d�truite par des url kilom�triques ou par des petits malins
  // qui postent des xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	$tag_open = '<';
	$tag_close = '>';
	$count = $in_tag = $segment_width = 0;
	$str_len = strlen($str);
	for ($i = 0; $i <= $str_len - 1 ; $i++)
	{
		$espace = 0;
		if ($str[$i] == $tag_open) 
		{ 
			$in_tag++; 
		}
		else if ($str[$i] == $tag_close) 
		{
			$in_tag--; 
		}
		else
		{
			if ($in_tag == 0) 
			{ 
				$segment_width++;
				// on regarde si le caract�re est un espace
				if (preg_match("/(\s|\&|;|\.)/", $str[$i]))
				{
					$espace++;
					$segment_width = 0;
				}
				if ($segment_width == $cols and $espace == 0) 
				{
					$segment_width = 0;
					$espace = 0;
					$str = substr($str, 0, $i + 1).$cut.substr($str, $i + 1, $str_len);		
					$i += strlen($cut);
					$str_len = strlen($str);
				}
			}
		}
	}
	return $str;
}

function url2link($texte) 
{
	// Cette fonction transforme toutes les url contenues dans $texte en des liens cliquables
	// Elle fonctionne aussi pour les adresse mail pr�c�d�es de mailto:
	// On remplace tout d'abord les "&" du texte (par exemple ceux qui peuvent �tre contenus dans une URL
	global $site;
	$texte = str_replace('&', 'a-m-p', $texte);
	$lien_externe = ($site['avert_onclick_lien_externe'] == 1) ? "onclick=\"lien_externe();\"" : "";
	// Ensuite, voici les zolies regexp
	// Elles n'urlisent pas les bbcodes
	// http://, news://, ftp://, https://
	$texte = eregi_replace("([^=\"\';])(http|news|ftp|https)://(([-�a-z0-9\/\.\?_\+=#%@;:~])*)", "\\1<a $lien_externe href=\"\\2://\\3\" target=\"_blank\" class=\"lienmort\">\\2://\\3</a>", $texte);
	// mailto:
	$texte = eregi_replace("([^=\"\';])(mailto):(([-�a-z0-9\/\.\?_=#%@;:~])*)", "\\1<a href=\"\\2:\\3\">\\3</a>", $texte);
	// www. (sans le http://)
	$texte = eregi_replace("([^=\"\';])([^http://])www\.(([-�a-z0-9\/\.\?_\+=#%@;:~])*)", "\\1\\2<a $lien_externe href=\"http://www.\\3\" target=\"_blank\" class=\"lienmort\">www.\\3</a>", $texte);
	// Enfin on renvoie le texte en remettant les "&" comme il faut
	$texte = str_replace('a-m-p', '&', $texte);
	return $texte;
}

function smileys($text)
{
	// Cette fonction active les codes smileys
	// Pour la lisibilit�, mettre max 10 smileys par ligne dans les arrays ci-dessous
	$look = array(
' lol ', ' LOL ', 'mdr', 'MDR', '>:-(', '(fache)', '<:-)', '(chinois)', '&gt;:-(', '&lt;:-)', 
' :-)', ' :) ', ' :D ', ' :-D', ';-)', ' :o)', ' ;) ', ' :-p ', ' :p ', ' :d ', 
'8-)', '(:-()', ':-(', '(bravo)', '(chut)', '(grr)', '(ok)', '(!ok)', '(?)', '(fete)', 
'(coucou)', '(lire)', '(pouce)', '(stnicolas)', '(bonannif)', '(zzz)', '(fou)', '(diable)', '(ptidiable)', '(pikachu)', 
'(ange)', ';o)', ' :( '); // d�finit les codes smileys intuitifs
	$repl = array(
' :001: ', ' :001: ', ':066:', ':066:', ':002:', ':002:', ':020:', ':020:', ':002:', ':020:', 
' :001:', ' :001:', ' :066: ', ' :066:', ':003:', ' :021:', ' :003:', ' :014:', ' :014:', ' :014:', 
':051:', ':045:', ':004:', ':040:', ':023:', ':013:', ':009:', ':010:', ':008:', ':028:', 
':025:', ':041:', ':050:', ':058:', ':062:', ':024:', ':030:', ':032:', ':034:', ':036:', 
':033:', ':021:', ':004:'); // d�finit les smileys correspondant aux codes intuitifs

	// remplace les codes smileys intuitifs dans le $text et les remplace par leur code num�rique
	// le 4e param�tre de la fonction str_replace n'est reconnu qu'� partir de php 5...
	$text = str_replace($look, $repl, $text); // , $nbre_smileys_intuitifs);

	//if ($nbre_smileys_intuitifs > 0 or strpos($text, ':'))
	if (strpos($text, ':'))
	{ // s'il y a des doubles-points, il y a probablement des smileys � mettre � jour
		// remplace les codes smileys num�riques dans le $text
		$text = ereg_replace(":(([0-9]){3}):", " <img src=\"img/smileys/\\1.gif\" border=\"0\" align=\"middle\" alt=\"\" /> ", $text);
	}
	return $text;
}

if (!function_exists('html_entity_decode'))
{ // la fonction html_entity_decode n'est pr�sente qu'� partir de php 4.3.0
  // cette fonction vise donc � la remplacer lorsqu'elle n'existe pas
	function html_entity_decode($string, $format = '')
	{
		$trans_tbl = get_html_translation_table (HTML_ENTITIES);
		$trans_tbl = array_flip ($trans_tbl);
		$string = strtr ($string, $trans_tbl);
		if ($format == ENT_QUOTES)
		{
			$string = str_replace('&#039;', "'", $string);
		}
		return $string;
	}
}

function makehtml($text, $format = 'bbcode', $show_bblinks = true)
{ 	/* Cette fonction met en forme un texte et en retire les balises html interdites */
	/* diff�rents formats de sortie/entr�e pour la fonction :
		- bbcode (par d�faut) : bbcode autoris�, les balises html et les images en bbcode sont interdites
			Quelques balises html sont n�anmoins autoris�es (voir plus bas)
		- noibbcode : bbcode autoris�, pas de graphiques (pour l'envoi de la newsletter)
			ce format doit encore �tre d�velopp� pour afficher l'adresse absolue des images
		- ibbcode : bbcode autoris�, les balises html sont interdites, images en bbcode autoris�es
		- html : html autoris�, bbcode interdit, images en bbcode interdites, smileys autoris�s
		- noimg : html et bbcode autoris�, pas de graphiques
	*/
	global $site;
	if (empty($text)) {return $text;} // la chaine est vide...

	$text = trim($text); // on supprime les sauts de ligne inutiles
	$text = ' '.$text.' '; // on espace pour pouvoir parser les codes en d�but et fin de cha�ne

	// histoire de s'�pargner des efforts, on ne parse le bbcode qui si $text en contient ;)
	// ca devrait acc�l�rer quelque peu le chargement des pages.
	$parse_bbcode = (strpos($text, '[') and strpos($text, ']')) ? true : false;
	if ($format != 'html' and $format != 'noimg')
	{ // format bbcode
		// En th�orie, toutes les entr�es utilisateurs sont pass�es par un htmlentities()
		// Mais � 2 heures du mat', on a parfois des blancs et on laisse passer des champs :) 
		// Keep secure, donc on va quand m�me supprimer les balises html :)
		// NB on ne fait pas un htmlentities sinon �a d�truirait les entit�s d�j� cod�es : &amp;lt;
		$look = array('<', '>', "\r");
		$repl = array('&lt;', '&gt;', '');
		$text = str_replace($look, $repl, $text);
		// maintenant qu'elles sont toutes mortes, on joue � dieu et on en ressuscite quelques-unes pour �tre gentil

		// comme on aime faire de jolies choses on supprime les moultes espaces autour des balises titre <hn> afin d'�viter
		// le remplacement des sauts de ligne par des <br /> autour du titre. <hn> est une balise block quand m�me :-p
		// on fait la m�me chose autour des balises de paragraphes <p> </p> (aucun attribut autoris� ici)
		$text = preg_replace("/(\s+)?(&lt;(p|h[1-6]|pre|code)&gt;.+&lt;\/(p|h1|h2|h3|h4|h5|h6|pre)&gt;)(\s+)?/", "$2",  $text);
		
		// I have a dream !
		// celui o� je trouverais un truc efficace pour parser un texte bbcod� en paragraphes d�limit�s par la balise <p>
		// tenant compte des zones <pre> et <hn>. Ca demande une �tude approfondie des regexp ;)
		
		// on s'appr�te � parser les sauts de ligne et les balises html standard autoris�es en bbcode
		
		// on remplace les sauts de ligne par un marqueur #br# (pars� diff�remment dans et hors de [code])
		$text = str_replace("\n", "#br#", $text);
		
		// on pr�pare les balises html h1 � h6, i, b, em, strong, u, p et q pour ne pas les parser dans [code]
		$text = ereg_replace("&lt;(/)?(p|h[1-6]|pre|code|i|b|em|strong|u|q)&gt;", "#<#\\1\\2#>#", $text);
		if ($parse_bbcode and strpos($text, '[code]')) // il y a des balises [code] � parser
		{
			// ici, on fait les frimeurs pour afficher du code propre dans le forum ou ailleurs
			// comme la balise pre consid�re les \n (chr(10)) comme des sauts de ligne effectifs, on �vite d'y ajouter des br inutiles...
			// en m�me temps on �vite de parser les balises html autoris�es en bbcode
			// Il reste que les smileys sont encore pars�s dans le code. En th�orie, sans collision avec le code
			//   mais les feedback sont bienvenus ;)
			$text = preg_replace("/(.+)?\[code\](.+)\[\/code\](.+)?/Usie", "'\\1[code]'.str_replace('#br#', chr(10), '\\2').'[/code]\\3'", $text);
			$text = preg_replace("/(.+)?\[code\](.+)\[\/code\](.+)?/Usie", "'\\1[code]'.str_replace('#<#', '&lt;', '\\2').'[/code]\\3'", $text);
			$text = preg_replace("/(.+)?\[code\](.+)\[\/code\](.+)?/Usie", "'\\1[code]'.str_replace('#>#', '&gt;', '\\2').'[/code]\\3'", $text);
		}
		if ($parse_bbcode and strpos($text, '[quote]'))
		{
			// on parse les citations
			$text = eregi_replace("\[(\/)?quote\]", "<\\1blockquote>", $text);
		}
		// et on termine en rempla�ant correctement nos balises marqu�es
		// on met des espaces autour des sauts de ligne pour autoriser des smileys en d�but de ligne
		// Comme quoi la maison ne recule devant aucun sacrifice
		// Pour faire joli, ces espaces sont retir�s en fin de fonction makehtml
		$look = array('#br#', '#<#', '#>#');
		$repl = array(" <br />\n ", '<', '>');
		$text = str_replace($look, $repl, $text);
	}
	else
	{ // format html actif
		$lien_externe = ($site['avert_onclick_lien_externe'] == 1) ? 'onclick="lien_externe();"' : '';
		// la pr�sence du http:// implique souvent un lien vers un autre site
		$text = str_replace(' href=\"http://', ' '.$lien_externe.' href=\"http://', $text);
		// au format html, les balises html n'ont pas �t� d�sactiv�es � l'aide de htmlentities()
		//$text = html_entity_decode($text, ENT_QUOTES);
	}
	if ($parse_bbcode)
	{
		if ($format == 'ibbcode')
		{ // bbcode + images autoris�es
			$text = eregi_replace("\[img(left|right)?=(&quot;)?(([-�a-z0-9\/\.\?_#!%@;:~])*)(&quot;)?\]", "<img src=\"\\3\" border=\"0\" align=\"\\1\" class=\"photo\" alt=\"\" />", $text);
		}
		$text = eregi_replace("\[mail=(([-a-z0-9\/\._@~])*)\]", " <a href=\"mailto:\\1\">", $text);
		$look = array(
		'[panneau]', '[/panneau]', '[rouge]', '[/rouge]', '[bleu]', 
		'[/bleu]', '[b]', '[/b]', '[i]', '[/i]', 
		'[u]', '[/u]', '[c]', '[/c]', '[/mail]', 
		'[/code]', '[code]', '[petit]', '[/petit]', '[petitbleu]', 
		'[/petitbleu]', '<blockquote>');
		$repl = array('<div class="panneau">', '</div>', '<span class="rmq">', '</span>', '<span class="rmqbleu">', 
		'</span>', '<b>', '</b>', '<i>', '</i>', 
		'<u>', '</u>', '<div align="center">', '</div>', '</a>', 
		'</pre>', '<span class="info" title="Pour afficher du code, utilise [code][/code]">Code :</span><pre class="code">', '<span class="petit">', '</span>', '<span class="petitbleu">', 
		'</span>', '<span class="info" title="Pour les citations, utilise [quote][/quote]">Citation :</span><blockquote>');
		$text = str_replace($look, $repl, $text);
		if ($show_bblinks)
		{ // on parse les url plac�es dans [url=adresse]texte au choix[/url]
			$lien_externe = ($site['avert_onclick_lien_externe'] == 1) ? "onclick=\"lien_externe();\"" : "";
			// 6 mars 2005 : en l'�tat actuel, les balises ne sont pas prot�g�es contre l'insertion de javascript via la structure suivante :
			// [url=javascript:...]
			// Cet inconv�nient est l�ger �tant donn� que ce code est visible dans la barre d'�tat du navigateur
			// N�anmoins, on d�sactivera �a d�s que possible
			$text = eregi_replace("\[url=(\\\"|&quot;)?(http://www|http://)(([-a-z0-9&\/\.\?_\+=#%@;:~])*)(\\\"|&quot;)?\]", "<a $lien_externe href=\"\\2\\3\" target=\"_blank\">", $text);
			$text = eregi_replace("\[url=(\\\"|&quot;)?www(([-a-z0-9&\/\.\?_\+=#%@;:~])*)(\\\"|&quot;)?\]", "<a $lien_externe href=\"http://www\\2\" target=\"_blank\">", $text);
			$text = eregi_replace("\[url=(\\\"|&quot;)?(news|ftp|https)://(([-a-z0-9&\/\.\?_\+=#%@;:~])*)(\\\"|&quot;)?\]", "<a $lien_externe href=\"\\2://\\3\" target=\"_blank\">", $text);
			$text = eregi_replace("\[url=(\\\"|&quot;)?(([-a-z0-9&\/\.\?_\+=#%@;:~])*)(\\\"|&quot;)?\]", "<a href=\"\\2\">", $text);
			$text = str_replace("[/url]","</a>",$text); 
		}
		else
		{ // les bbliens ne doivent pas �tre affich�s, on supprime la balise mais on laisse l'adresse
		// on retire la balise [url] et on ne laisse que l'intitul� du lien suivi de l'url entre parenth�ses
			$text = preg_replace("/\[url=([-a-z0-9&\/\.\?_=+#%@;:~]*)\](.*)\[\/url\]/Ui", "\\2 (\\1) ", $text);
		}
	}
	if ($format != 'html' and $show_bblinks)
	{ // on active les url
		$text = url2link($text);
	}
	if ($format != 'noimg' and $format != 'noibbcode')
	{ // on active les smileys dans le texte
		$text = smileys($text);
	}
	// tous les segments de cha�ne de plus de x caract�res - hors balises html - sont s�par�s par un saut de ligne
	// protection contre la d�formation de page dans les navigateurs
	$text = nettoie($text);
	// on supprime l'espace qu'on avait ins�r� plus haut pour parser les smileys
	$text = str_replace(" <br />\n ", "<br />\n", $text);
	// le stripslashes pour supprimer les antislashes
	$text = stripslashes($text);
	return $text;
}

function killscriptanimateur($text)
{ // appliqu� au contenu des pages du portail r�dig�es par les membres.
	$f = array('.cookie', 'alert(', '.write', '</textarea', '.location', 'xxx');
	$text = str_replace($f, 'xxx', $text);
	return $text;
}

function cleanvar($texte)
{
	$interdits = array ('|', '&', '#', '(', ')', '[', ']', '{', '}', '<', '>', '=', '+', '*', '$', '%', '\\', 'SELECT', 'DELETE', 'INSERT', 'UPDATE', 'echo', 'write', 'document.write', '"', ';');
	$max = 25;
	$i = 0;
	while ($i <= $max)
	{
		$texte = str_replace($interdits[$i], '', $texte);
		$i++;
	}
	$texte = htmlspecialchars($texte);
	$texte = addslashes($texte);
	$texte = trim($texte);
	return $texte;
}

function desaccentuer($str)
{ // Cette fonction permet de transformer les caract�res accentu�s en leur �quivalent non accentu�.
	// cette fonction est adapt�e depuis un script de Bozzac post� sur http://www.phpscripts-fr.net/
	$chercher =  "�����������������������������������������������������";
	$remplacer = "AAAAAAaaaaaaEEEEeeeeIIIIiiiiOOOOOOooooooUUUUuuuuyCcNn";
	return strtr($str, $chercher, $remplacer);
}

function soundex2( $sIn )
{ // Soundex2 : fonction �crite par F. Bouchery : 
  // http://sqlpro.developpez.com/cours/soundex/
  // Cette fonction donne la prononciation phon�tique fran�aise de la cha�ne re�ue.
   // Si il n'y a pas de mot, on sort imm�diatement
   if ( $sIn === '' ) return '';
   // On met tout en minuscule
   $sIn = strtoupper( $sIn );
   // On supprime les accents
   $sIn = strtr( $sIn, '��������������������������', 'AAAAAAEEEEIIIIOOOOOOUUUUCN' );
   // On supprime tout ce qui n'est pas une lettre
   $sIn = preg_replace( '`[^A-Z]`', '', $sIn );
   // Si la cha�ne ne fait qu'un seul caract�re, on sort avec.
   if ( strlen( $sIn ) === 1 ) return $sIn . '   ';
   // on remplace les consonnances primaires
   $convIn = array( 'GUI', 'GUE', 'GA', 'GO', 'GU', 'CA', 'CO', 'CU', 'Q', 'CC', 'CK' );
   $convOut = array( 'KI', 'KE', 'KA', 'KO', 'K', 'KA', 'KO', 'KU', 'K','K', 'K' );
   $sIn = str_replace( $convIn, $convOut, $sIn );
   // on remplace les voyelles sauf le Y et sauf la premi�re par A
   $sIn = preg_replace( '`(?<!^)[EIOU]`', 'A', $sIn );
   // on remplace les pr�fixes puis on conserve la premi�re lettre
   // et on fait les remplacements compl�mentaires
   $convIn = array( '`^KN`', '`^(PH|PF)`', '`^MAC`', '`^SCH`', '`^ASA`', '`(?<!^)KN`', '`(?<!^)(PH|PF)`', '`(?<!^)MAC`',
                    '`(?<!^)SCH`','`(?<!^)ASA`' );
   $convOut = array( 'NN', 'FF', 'MCC', 'SSS', 'AZA', 'NN', 'FF', 'MCC', 'SSS', 'AZA' );
   $sIn = preg_replace( $convIn, $convOut, $sIn );
   // suppression des H sauf CH ou SH
   $sIn = preg_replace( '`(?<![CS])H`', '', $sIn );
   // suppression des Y sauf pr�c�d�s d'un A
   $sIn = preg_replace( '`(?<!A)Y`', '', $sIn );
   // on supprime les terminaisons A, T, D, S
   $sIn = preg_replace( '`[ATDS]$`', '', $sIn );
   // suppression de tous les A sauf en t�te
   $sIn = preg_replace( '`(?!^)A`', '', $sIn );
   // on supprime les lettres r�p�titives
   $sIn = preg_replace( '`(.)\1`', '$1', $sIn );
   // on ne retient que 4 caract�res ou on compl�te avec des blancs
   return substr( $sIn . '    ', 0, 4);
}
?>