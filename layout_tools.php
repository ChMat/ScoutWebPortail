<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* layout_tools.php v 1.1 - Outils de mise en page pour les outils du portail (forum, tally, ...)
* Fichier lié : fonc.js, prv/fonc_str.php
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
*	Emballage des panneaux dans un div pour le design css
*	Ajout bouton insertion image
*/

function panneau_smileys($cible)
{
?>
<div class="smileys">
<p class="rmq">Smileys (Clique dessus pour les ins&eacute;rer)</p>
<img src="img/smileys/001.gif" alt="lol ou :-)" width="15" height="15" title="lol ou :-)" onclick="addsmiley('<?php echo $cible; ?>', '', 'lol')" /> 
<img src="img/smileys/002.gif" alt="(fache)" width="15" height="15" title="(fache)" onclick="addsmiley('<?php echo $cible; ?>', '', '(fache)')" /> 
<img src="img/smileys/003.gif" alt=";-)" width="15" height="15" title=";-)" onclick="addsmiley('<?php echo $cible; ?>', '', ';-)')" /> 
<img src="img/smileys/004.gif" alt=":-(" width="15" height="15" title=":-(" onclick="addsmiley('<?php echo $cible; ?>', '', ':-(')" /> 
<img src="img/smileys/005.gif" alt=":005:" width="15" height="22" title=":005:" onclick="addsmiley('<?php echo $cible; ?>', ':', '005')" /> 
<img src="img/smileys/066.gif" alt=":D ou mdr" width="15" height="15" title=":D ou mdr" onclick="addsmiley('<?php echo $cible; ?>', '', ':D')" /> 
<img src="img/smileys/006.gif" alt=":006:" width="19" height="19" title=":006:" onclick="addsmiley('<?php echo $cible; ?>', ':', '006')" /> 
<img src="img/smileys/007.gif" alt=":007:" width="15" height="15" title=":007:" onclick="addsmiley('<?php echo $cible; ?>', ':', '007')" /> 
<img src="img/smileys/008.gif" alt="(?)" width="15" height="15" title="(?)" onclick="addsmiley('<?php echo $cible; ?>', '', '(?)')" /> 
<img src="img/smileys/009.gif" alt="(ok)" width="15" height="15" title="(ok)" onclick="addsmiley('<?php echo $cible; ?>', '', '(ok)')" /> 
<img src="img/smileys/010.gif" alt="(!ok)" width="15" height="15" title="(!ok)" onclick="addsmiley('<?php echo $cible; ?>', '', '(!ok)')" /> 
<img src="img/smileys/011.gif" alt=":011:" width="15" height="15" title=":011:" onclick="addsmiley('<?php echo $cible; ?>', ':', '011')" /> 
<img src="img/smileys/012.gif" alt=":012:" width="16" height="16" title=":012:" onclick="addsmiley('<?php echo $cible; ?>', ':', '012')" /> 
<img src="img/smileys/013.gif" alt=":013:" width="33" height="36" title=":013:" onclick="addsmiley('<?php echo $cible; ?>', ':', '013')" /> 
<img src="img/smileys/014.gif" alt=":-p" width="15" height="15" title=":-p" onclick="addsmiley('<?php echo $cible; ?>', '', ':-p')" /> 
<img src="img/smileys/015.gif" alt=":015:" width="14" height="14" title=":015:" onclick="addsmiley('<?php echo $cible; ?>', ':', '015')" /> 
<img src="img/smileys/016.gif" alt=":016:" width="23" height="15" title=":016:" onclick="addsmiley('<?php echo $cible; ?>', ':', '016')" /> 
<img src="img/smileys/017.gif" alt=":017:" width="15" height="15" title=":017:" onclick="addsmiley('<?php echo $cible; ?>', ':', '017')" /> 
<img src="img/smileys/018.gif" alt=":018:" width="15" height="22" title=":018:" onclick="addsmiley('<?php echo $cible; ?>', ':', '018')" /> 
<img src="img/smileys/019.gif" alt=":019:" width="15" height="15" title=":019:" onclick="addsmiley('<?php echo $cible; ?>', ':', '019')" /> 
<img src="img/smileys/020.gif" alt="(chinois)" width="20" height="20" title="(chinois)" onclick="addsmiley('<?php echo $cible; ?>', '', '(chinois)')" /> 
<img src="img/smileys/021.gif" alt=":021:" width="15" height="15" title=":021:" onclick="addsmiley('<?php echo $cible; ?>', ':', '021')" /> 
<img src="img/smileys/022.gif" alt=":022:" width="15" height="15" title=":022:" onclick="addsmiley('<?php echo $cible; ?>', ':', '022')" /> 
<img src="img/smileys/023.gif" alt="(chut)" width="20" height="19" title="(chut)" onclick="addsmiley('<?php echo $cible; ?>', '', '(chut)')" /> 
<img src="img/smileys/024.gif" alt="(zzz)" width="27" height="25" title="(zzz)" onclick="addsmiley('<?php echo $cible; ?>', '', '(zzz)')" /> 
<img src="img/smileys/025.gif" alt=":025:" width="25" height="15" title=":025:" onclick="addsmiley('<?php echo $cible; ?>', ':', '025')" /> 
<img src="img/smileys/026.gif" alt=":026:" width="15" height="15" title=":026:" onclick="addsmiley('<?php echo $cible; ?>', ':', '026')" /> 
<img src="img/smileys/027.gif" alt=":027:" width="20" height="24" title=":027:" onclick="addsmiley('<?php echo $cible; ?>', ':', '027')" /> 
<img src="img/smileys/028.gif" alt="(fete)" width="19" height="22" title="(fete)" onclick="addsmiley('<?php echo $cible; ?>', '', '(fete)')" /> 
<img src="img/smileys/029.gif" alt=":029:" width="15" height="21" title=":029:" onclick="addsmiley('<?php echo $cible; ?>', ':', '029')" /> 
<img src="img/smileys/030.gif" alt="(fou)" width="25" height="24" title="(fou)" onclick="addsmiley('<?php echo $cible; ?>', '', '(fou)')" /> 
<img src="img/smileys/031.gif" alt=":031:" width="19" height="24" title=":031:" onclick="addsmiley('<?php echo $cible; ?>', ':', '031')" /> 
<img src="img/smileys/032.gif" alt="(diable)" width="15" height="15" title="(diable)" onclick="addsmiley('<?php echo $cible; ?>', '', '(diable)')" /> 
<img src="img/smileys/033.gif" alt="(ange)" width="25" height="20" title="(ange)" onclick="addsmiley('<?php echo $cible; ?>', '', '(ange)')" /> 
<img src="img/smileys/034.gif" alt="(ptidiable)" width="25" height="26" title="(ptidiable)" onclick="addsmiley('<?php echo $cible; ?>', '', '(ptidiable)')" /> 
<img src="img/smileys/035.gif" alt=":035:" width="15" height="15" title=":035:" onclick="addsmiley('<?php echo $cible; ?>', ':', '035')" /> 
<img src="img/smileys/036.gif" alt="(pikachu)" width="22" height="20" title="(pikachu)" onclick="addsmiley('<?php echo $cible; ?>', '', '(pikachu)')" /> 
<img src="img/smileys/040.gif" alt="(bravo)" width="31" height="23" title="(bravo)" onclick="addsmiley('<?php echo $cible; ?>', '', '(bravo)')" /> 
<img src="img/smileys/041.gif" alt="(lire)" width="24" height="23" title="(lire)" onclick="addsmiley('<?php echo $cible; ?>', '', '(lire)')" /> 
<img src="img/smileys/042.gif" alt=":042:" width="32" height="32" title=":042:" onclick="addsmiley('<?php echo $cible; ?>', ':', '042')" /> 
<img src="img/smileys/043.gif" alt=":043:" width="17" height="17" title=":043:" onclick="addsmiley('<?php echo $cible; ?>', ':', '043')" /> 
<img src="img/smileys/044.gif" alt=":044:" width="30" height="30" title=":044:" onclick="addsmiley('<?php echo $cible; ?>', ':', '044')" /> 
<img src="img/smileys/045.gif" alt=":045:" width="25" height="20" title=":045:" onclick="addsmiley('<?php echo $cible; ?>', ':', '045')" /> 
<img src="img/smileys/046.gif" alt=":046:" width="15" height="17" title=":046:" onclick="addsmiley('<?php echo $cible; ?>', ':', '046')" /> 
<img src="img/smileys/047.gif" alt=":047:" width="15" height="15" title=":047:" onclick="addsmiley('<?php echo $cible; ?>', ':', '047')" /> 
<img src="img/smileys/048.gif" alt=":048:" width="15" height="15" title=":048:" onclick="addsmiley('<?php echo $cible; ?>', ':', '048')" /> 
<img src="img/smileys/049.gif" alt=":049:" width="15" height="15" title=":049:" onclick="addsmiley('<?php echo $cible; ?>', ':', '049')" /> 
<img src="img/smileys/050.gif" alt="(pouce)" width="25" height="18" title="(pouce)" onclick="addsmiley('<?php echo $cible; ?>', '', '(pouce)')" /> 
<img src="img/smileys/051.gif" alt="8-)" width="21" height="15" title="8-)" onclick="addsmiley('<?php echo $cible; ?>', '', '8-)')" /> 
<img src="img/smileys/054.gif" alt=":054:" width="30" height="30" title=":054:" onclick="addsmiley('<?php echo $cible; ?>', ':', '054')" /> 
<img src="img/smileys/059.gif" alt=":059:" width="43" height="28" title=":059:" onclick="addsmiley('<?php echo $cible; ?>', ':', '059')" /> 
</div>
<?php
}

function panneau_mise_en_forme($cible, $aide = false, $ajout_image = false)
{ // Ce panneau de mise en forme n'offre que le bbcode
  // un panneau changeant (html/bbcode) est fourni plus bas
?>
<div class="layout_tools">
<a accesskey="b" onclick="add_tag('<?php echo $cible; ?>','[b]','[/b]')" title="Mettre le texte en gras (Alt + b)"><img src="templates/default/images/gras.png" alt="Gras" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="r" onclick="add_tag('<?php echo $cible; ?>','[rouge]','[/rouge]')" title="Mettre le texte en rouge (Alt + r)"><img alt="Rouge" src="templates/default/images/rmq.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="e" onclick="add_tag('<?php echo $cible; ?>','[bleu]','[/bleu]')" title="Mettre le texte en bleu (Alt + e)"><img alt="Bleu" src="templates/default/images/rmqbleu.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="i" onclick="add_tag('<?php echo $cible; ?>','[i]','[/i]')" title="Mettre le texte en italique (Alt + i)"><img alt="Italique" src="templates/default/images/italique.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="u" onclick="add_tag('<?php echo $cible; ?>','[u]','[/u]')" title="Souligner le texte (Alt + u)"><img alt="Soulign&eacute;" src="templates/default/images/souligne.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="c" onclick="add_tag('<?php echo $cible; ?>','[quote]','[/quote]')" title="Insérer une citation (Alt + c)"><img alt="Citation" src="templates/default/images/quote.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="w" onclick="add_tag('<?php echo $cible; ?>','[url=]','[/url]')" title="Insérer un lien internet (Alt + w)"><img alt="Lien internet" src="templates/default/images/url.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="m" onclick="add_tag('<?php echo $cible; ?>','[mail=]','[/mail]')" title="Insérer une adresse email (Alt + m)"><img alt="Lien email" src="templates/default/images/mail.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	if ($ajout_image)
	{
?>
<script type="text/javascript" language="JavaScript">
<!--
function addimg()
{
	window.open('addimage.php?x=0', 'image', 'width=650,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}
//-->
</script>
<a accesskey="g" onclick="addimg();" title="Insérer une image (Alt + g)"><img src="templates/default/images/imgdroite.png" alt="Ins&eacute;rer une image" style="cursor:pointer" /></a>
<?php
	}
	if ($aide)
	{
?>
<script type="text/javascript" language="JavaScript">
<!--
function aide()
{
	window.open('help.php','','width=350,height=400,menubar=0,scrollbars=1,location=0,resize=1');
}
//-->
</script>
<a onclick="aide()" title="Un peu d'aide ?"><img alt="aide" src="templates/default/images/aide.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	}
?>
</div>
<?php
}

function panneau_html($cible, $aide = false)
{ // Ce panneau de mise en forme n'offre que le html
  // un panneau changeant (html/bbcode) est fourni plus bas
?>
<div class="layout_tools">
<script type="text/javascript" language="JavaScript">
<!--
function addimg_<?php echo $cible; ?>()
{
	var z = 1;
	// 1 format html pour la balise image
	// 3 format bbcode
	window.open('addimage.php?x='+z+'&cible=<?php echo $cible; ?>', 'image', 'width=650,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}
//-->
</script>
<a onclick='addimg_<?php echo $cible; ?>();' title="Ins&eacute;rer des images dans les pages."><img src="templates/default/images/imgdroite.png" width="18" height="12" alt="Image" style="cursor:pointer" /></a>
<a accesskey="b" onclick='add_tag("<?php echo $cible; ?>","<b>","</b>")' title="Mettre le texte en gras (Alt + b)"><img src="templates/default/images/gras.png" alt="Gras" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="r" onclick='add_tag("<?php echo $cible; ?>","<span class=\"rmq\">","</span>")' title="Mettre le texte en rouge (Alt + r)"><img alt="Rouge" src="templates/default/images/rmq.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="e" onclick='add_tag("<?php echo $cible; ?>","<span class=\"rmqbleu\">","</span>")' title="Mettre le texte en bleu (Alt + e)"><img alt="Bleu" src="templates/default/images/rmqbleu.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="i" onclick='add_tag("<?php echo $cible; ?>","<i>","</i>")' title="Mettre le texte en italique (Alt + i)"><img alt="Italique" src="templates/default/images/italique.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="u" onclick='add_tag("<?php echo $cible; ?>","<u>","</u>")' title="Souligner le texte (Alt + u)"><img alt="Soulign&eacute;" src="templates/default/images/souligne.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="c" onclick='add_tag("<?php echo $cible; ?>","<div align=\"center\">","</div>")' title="Centrer le texte (Alt + c)"><img alt="Centr&eacute;" src="templates/default/images/centre.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="w" onclick='add_tag("<?php echo $cible; ?>","<a href=\"\">","</a>")' title="Insérer un lien internet (Alt + w)"><img alt="Lien internet" src="templates/default/images/url.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="m" onclick='add_tag("<?php echo $cible; ?>","<a href=\"mailto:\">","</a>")' title="Insérer une adresse email (Alt + m)"><img alt="Lien email" src="templates/default/images/mail.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	if ($aide)
	{
?>
<script type="text/javascript" language="JavaScript">
<!--
function aide()
{
	window.open('help.php','','width=350,height=400,menubar=0,scrollbars=1,location=0,resize=1');
}
//-->
</script>
<a onclick="aide()" title="Un peu d'aide ?"><img alt="aide" src="templates/default/images/aide.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	}
?>
</div>
<?php
}

function panneau_mef_mixte($cible, $format_defaut = 'html', $id_format = 'formathtml', $aide = false)
{ // Ce panneau de mise en forme offre une mise en forme multi-formats
  // html et bbcodes
?>
<div class="layout_tools">
<script type="text/javascript" language="JavaScript">
<!--
<?php
	echo ($id_format == 'htmlonly') ? 'var htmlonly_'.$cible.' = true;' : 'var htmlonly_'.$cible.' = false;';
?>
var open_tag_<?php echo $id_format; ?> = "<";
var close_tag_<?php echo $id_format; ?> = ">";

function change_format_<?php echo $id_format; ?>(format)
{
	if (format == "html")
	{
		open_tag_<?php echo $id_format; ?> = "<";
		close_tag_<?php echo $id_format; ?> = ">";
	}
	else
	{
		open_tag_<?php echo $id_format; ?> = "[";
		close_tag_<?php echo $id_format; ?> = "]";
	}
}

function addimg(format)
{
	var z = 1;
	// 1 format html pour la balise image
	// 3 format bbcode
	if (format) {z = 1;} else {z = 3;}
	window.open('addimage.php?x='+z+'&cible=<?php echo $cible; ?>', 'image', 'width=650,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}

<?php
	echo "change_format_".$id_format."(\"".$format_defaut."\");\n";
?>
//-->
</script>
<a onclick='addimg(getElement("<?php echo $id_format; ?>").checked);' title="Ins&eacute;rer des images dans les pages."><img src="templates/default/images/imgdroite.png" width="18" height="12" alt="Image" style="cursor:pointer" /></a>
<a accesskey="b" onclick='add_tag("<?php echo $cible; ?>", open_tag_<?php echo $id_format; ?>+"b"+close_tag_<?php echo $id_format; ?>,open_tag_<?php echo $id_format; ?>+"/b"+close_tag_<?php echo $id_format; ?>)' title="Mettre le texte en gras (alt + b)"><img alt="Gras" src="templates/default/images/gras.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="r" onclick='if (htmlonly_<?php echo $cible; ?> || getElement("<?php echo $id_format; ?>").checked) {add_tag("<?php echo $cible; ?>", "<span class=\"rmq\">","</span>");} else {add_tag("<?php echo $cible; ?>", "[rouge]", "[/rouge]");}' title="Mettre le texte en rouge (Alt + r)"><img alt="Rouge" src="templates/default/images/rmq.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="e" onclick='if (htmlonly_<?php echo $cible; ?> || getElement("<?php echo $id_format; ?>").checked) {add_tag("<?php echo $cible; ?>", "<span class=\"rmqbleu\">","</span>");} else {add_tag("<?php echo $cible; ?>", "[bleu]", "[/bleu]");}' title="Mettre le texte en bleu (Alt + e)"><img alt="Bleu" src="templates/default/images/rmqbleu.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="i" onclick='add_tag("<?php echo $cible; ?>", open_tag_<?php echo $id_format; ?>+"i"+close_tag_<?php echo $id_format; ?>,open_tag_<?php echo $id_format; ?>+"/i"+close_tag_<?php echo $id_format; ?>)' title="Mettre le texte en italique (Alt + i)"><img alt="Italique" src="templates/default/images/italique.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="u" onclick='add_tag("<?php echo $cible; ?>", open_tag_<?php echo $id_format; ?>+"u"+close_tag_<?php echo $id_format; ?>,open_tag_<?php echo $id_format; ?>+"/u"+close_tag_<?php echo $id_format; ?>)' title="Souligner le texte (Alt + u)"><img alt="Souligné" src="templates/default/images/souligne.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="c" onclick='if (htmlonly_<?php echo $cible; ?> || getElement("<?php echo $id_format; ?>").checked) {add_tag("<?php echo $cible; ?>", "<div align=\"center\">","</div>");} else {add_tag("<?php echo $cible; ?>", "[c]", "[/c]");}' title="Centrer le texte (Alt + c)"><img alt="Centr&eacute;" src="templates/default/images/centre.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="w" onclick='if (htmlonly_<?php echo $cible; ?> || getElement("<?php echo $id_format; ?>").checked) {add_tag("<?php echo $cible; ?>", "<a href=\"\">","</a>");} else {add_tag("<?php echo $cible; ?>", "[url=]", "[/url]");}' title="Insérer un lien internet (Alt + w)"><img alt="Lien internet" src="templates/default/images/url.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<a accesskey="m" onclick='if (htmlonly_<?php echo $cible; ?> || getElement("<?php echo $id_format; ?>").checked) {add_tag("<?php echo $cible; ?>", "<a href=\"mailto:\">","</a>");} else {add_tag("<?php echo $cible; ?>", "[mail=]", "[/mail]");}' title="Insérer une adresse email (Alt + m)"><img alt="Email" src="templates/default/images/mail.png" width="18" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	if ($aide)
	{
?>
<script type="text/javascript" language="JavaScript">
<!--
function aide()
{
	window.open('help.php','','width=350,height=400,menubar=0,scrollbars=1,location=0,resize=1');
}
//-->
</script>
<a onclick="aide()" title="Un peu d'aide ?"><img alt="aide" src="templates/default/images/aide.png" width="12" height="12" border="0" style="cursor:pointer" /></a> 
<?php
	}
?>
</div>
<?php
}

?>