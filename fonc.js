/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc.js v 1.1 - Ce fichier contient la plupart des fonctions javascript du portail
* A noter que certaines fonctions détiennent leur propre copyright
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
* Modifications v 1.1 : ChMat
*	ajout d'un focus de retour vers la zone de texte après insertion d'une balise sous Mozilla
*/

var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));
var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1)
                && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));
var is_moz = 0;

var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));
var is_mac = (clientPC.indexOf("mac")!=-1);

function getElement(psID) {
   if(document.all) {
      return document.all[psID];
   } else if(document.getElementById) {
      return document.getElementById(psID);
   } else {
      for (iLayer = 1; iLayer < document.layers.length; iLayer++) {
         if(document.layers[iLayer].id == psID)
            return document.layers[iLayer];
      }      

   }

   return Null;
} 

// Fonctions pour l'ajout de balises html ou de bbcodes
// Adaptation par ChMat du script de bbcode des forums phpBB sous licence GPL

// From http://www.massless.org/mozedit/
function mozWrap(txtarea, tag_open, tag_close)
{
	var selLength = txtarea.textLength;
	var selStart = txtarea.selectionStart;
	var selEnd = txtarea.selectionEnd;
	if (selEnd == 1 || selEnd == 2) 
		selEnd = selLength;

	var s1 = (txtarea.value).substring(0,selStart);
	var s2 = (txtarea.value).substring(selStart, selEnd)
	var s3 = (txtarea.value).substring(selEnd, selLength);
	txtarea.value = s1 + tag_open + s2 + tag_close + s3;
	return;
}

// Insert at Claret position. Code from
// http://www.faqts.com/knowledge_base/view.phtml/aid/1052/fid/130
function storeCaret(textEl) {
	if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function addqqch(cible, text)
{
	var txtarea = getElement(cible);
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos) {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	} else {
		txtarea.value += text;
		txtarea.focus();
	}
}

function addsmiley(cible, cmt, smiley)
{
	addqqch(cible, cmt+smiley+cmt+' ');
}

function add_tag(cible, tag_open, tag_close)
{
	var txtarea = getElement(cible);

	if ((clientVer >= 4) && is_ie && is_win) {
		theSelection = document.selection.createRange().text;
		if (!theSelection) {
			txtarea.value += tag_open + tag_close;
			txtarea.focus();
			return;
		}
		document.selection.createRange().text = tag_open + theSelection + tag_close;
		txtarea.focus();
		return;
	}
	else if (txtarea.selectionEnd && (txtarea.selectionEnd - txtarea.selectionStart > 0))
	{
		mozWrap(txtarea, tag_open, tag_close);
		txtarea.focus();
		return;
	}
	else
	{
		txtarea.value += tag_open + tag_close;
		txtarea.focus();
	}
	storeCaret(txtarea);
}

// Fonctions diverses, utilisées par-ci par-là sur le portail

function vocabulaire()
{ // fonction obsolète à revoir
	window.open('vocabulaire.php','','width=450,height=450,menubar=0,scrollbars=1,location=0,resize=1');
}

function lien_externe()
{ // ce message est affiché lorsqu'un utilisateur clique sur un lien menant vers un site extérieur au portail
  // cette fonction peut être désactivée par le webmaster depuis la configuration du portail
	alert("Ce lien t'emmène vers un document se trouvant sur un site extérieur à l'Unité.\nNous ne sommes pas responsables de son contenu.\n\nN'hésite pas à contacter le webmaster en cas de problème.");
}

function aff_email(debut, fin, texte_lien)
{ // cette fonction renvoie un lien complet pour envoyer un mail
  // l'objectif est de masquer l'email aux collecteurs d'adresses
	if (texte_lien == '' || !texte_lien) {texte_lien = debut + '@' + fin;}
	document.write('<a href="mailto:'+debut+'@'+fin+'">'+texte_lien+'</a>');
}

function taille_fichier(taille)
{ // renvoie la taille d'un fichier exprimée en kilo-octets, méga-octets ou giga-octets.
  // 'taille' doit être exprimé en octets
	if (!isNaN(taille))
	{
		if (taille >= 1073741824) {taille = (Math.round(taille / 1073741824 * 100) / 100) + ' Go';}
		else if (taille >= 1048576) {taille = (Math.round(taille / 1048576 * 100) / 100) + ' Mo';}
		else if (taille >= 1024) {taille = (Math.round(taille / 1024 * 100) / 100) + ' Ko';}
		else {taille = taille + ' octets';} 
		if (taille == 0) {taille = '0 octet';}
		taille = taille.replace(/\./, ','); // on remplace le . des décimales par une virgule française :)
	}
	else
	{
		taille = 0;
	}
	return taille;
}
