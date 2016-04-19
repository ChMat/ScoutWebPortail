<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* pagesectionpost.php v 1.1.1 - Enregistrement d'une page du portail rédigée par un utilisateur
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
*	correction lien mort vers le siteduzero (merci à Mangouste) (fil 106)
*	correction [span] et <span> en [bleu|rouge|petit|petitbleu]
*	protection du formulaire contre la destruction par du code html mal écrit
* Modifications v 1.1.1
*	le caractère _ est autorisé dans les noms de page
*	ajout d'une vérification javascript du nom de la page (avant post)
*	Mise à jour du message d'avertissement
*/

include_once('connex.php');
include_once('fonc.php');
if (($_GET['do'] == 'creer' or $_GET['do'] == 'rediger' or $_GET['do'] == 'editer') and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{
	if ($_GET['do'] == 'creer')
	{ // la page n'existe pas, on va la créer
		$afaire = 'save';
	}
	else if ($_GET['do'] == 'rediger' or $_GET['do'] == 'editer')
	{ // la page existe mais on en modifie son contenu
		$sql = "SELECT * FROM ".PREFIXE_TABLES."pagessections as a LEFT JOIN ".PREFIXE_TABLES."unite_sections as b ON specifiquesection = numsection WHERE numpage = '$_GET[num]'";
		if ($res = send_sql($db, $sql))
		{ // on récupère le contenu
			$oldpage = mysql_fetch_assoc($res);
			$afaire = 'savemodif';
			// le format texte a subi un htmlentities à l'enregistrement, on annule son effet
			$oldpage['contenupage'] = ($oldpage['format'] != 'html') ? html_entity_decode($oldpage['contenupage'], ENT_QUOTES) : $oldpage['contenupage'];
		}
	}

	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Gestion des pages du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />

</head>
<body>
<?php
	}
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function addimg(format)
{
	var z = 1;
	// 1 format html pour la balise image
	// 3 format bbcode
	if (format) {z = 1;} else {z = 3;}
	window.open('addimage.php?x='+z, 'image', 'width=650,height=600,menubar=0,resizable=1,scrollbars=1,location=0,status=1');
}

function addtableau(zone)
{
	if (getElement("formathtml").checked)
	{
		var lignes = getElement("lignes").value;
		var colonnes = getElement("colonnes").value;
		if (lignes == "" || isNaN(lignes))
		{
			lignes = 1;
		}
		if (colonnes == "" || isNaN(colonnes))
		{
			colonnes = 1;
		}
		msg = '\n<table cellpadding="0" cellspacing="0" width="0">\n';
		for (var i = 1; i <= lignes; i++)
		{
			msg = msg + '<tr>\n';
			for (var j = 1; j <= colonnes; j++)
			{
				msg = msg + '<td></td>\n';
			}
			msg = msg + '</tr>\n';
		}
		msg = msg + '</table>\n';
		addqqch(zone, msg);
	}
	else
	{
		alert("Cette fonction n'est disponible qu'en format HTML.");
	}
}

function validate(form) 
{
<?	////////////////////////////////////////////////
	if ($afaire == 'savemodif')
	{
?>
		if (form.specifiquesection.value != <?php echo $oldpage[specifiquesection]; ?>)
		{
			return confirm("Tu as modifié l'adresse de la page en changeant la section cible.\nCela signifie que tous les liens qui ont déjà été faits vers cette page sur le portail guideront les visiteurs vers une page d'erreur à moins de tous les modifier manuellement.\nSouhaites-tu continuer ?");
		}
<?	////////////////////////////////////////////////
	}
	else
	{
?>
		if (form.page.value != "")
		{
			return check_nom_page(getElement('nom_page').value);
		}
		else
		{
			alert("N'oublie pas de donner un nom à la page.");
			form.page.focus();
			return false;
		}
<?php /////////////////////////////////////////////////
	}
?>
}

function aide()
{
	window.open('aidecreationpage.php', '', 'width=800,height=600,menubar=0,scrollbars=1,location=0,resize=1');
}

function msg_format()
{
	if (getElement('contenupage').value != '')
	{
		alert("Tu viens de modifier le format de la page.\nSi tu as déjà placé des balises de mise en forme dans la page, n'oublie pas de les adapter au nouveau format.");
	}
}

function check_nom_page(nom_page)
{ // Test du format de nom de page
	var expression = new RegExp("^[-a-z0-9_]{1,20}$","gi");
	var expression2 = new RegExp("^[a-z]{1}_","gi");
	if ((!expression.test(nom_page) || expression.test(nom_page)) && nom_page != '')
	{
		alert("Le nom de la page a un format incorrect !\n\nIl ne peut contenir que les caractères (a-z A-Z 0-9 - _)\net ne peut pas commencer par une lettre suivie de _");
		return false;
	}
}

function change_format(format)
{
	if (format == "html")
	{
		open_tag = "<";
		close_tag = ">";
	}
	else
	{
		open_tag = "[";
		close_tag = "]";
	}
}

<?php
	if ($oldpage['format'] == 'bbcode' or $_GET['do'] == 'creer')
	{
?>
change_format("bbcode");
<?php
	}
	else
	{
?>
change_format("html");
<?php
	}
?>

//-->
</script>
<?php
  	// récupération du numéro de section correspondant à la lettre de $nivcible
	$nivcible = $_GET['nivcible']; // nivcible est une lettre. Si sa valeur est g, aucune section n'y correspond.
	foreach ($sections as $test_niv) 
	{
		if ($test_niv['site_section'] == $nivcible) 
		{	// nivcible ne change de valeur que s'il correspond à une lettre de section
			// c'est toujours le cas, sauf si nivcible = g
			$nivcible = $test_niv['numsection'];
		}
	}
?>
<h1><?php if ($_GET['do'] == 'creer') { ?>Cr&eacute;er une page <?php } else if ($_GET['do'] == 'rediger') { ?>R&eacute;diger une page <?php } else if ($_GET['do'] == 'editer') {?>Editer une page <?php } ?> du portail</h1>
<p align="center"><a href="index.php?page=pagesection">Retour à la Gestion des pages du portail</a></p>
<form action="pagesectionpost.php" method="post" name="formulaire" id="formulaire" onsubmit="return validate(this)" class="form_config_site pages_site">
<h2>Titre de la page : 
<input type="text" name="titre" size="40" maxlength="100" value="<?php echo $oldpage['titre']; ?>" tabindex="1" />
</h2>
  <div align="right" class="petitbleu">
  Pour de l'aide, clique ici <img onclick="aide()" alt="Un peu d'aide ?" src="templates/default/images/aide.png" width="12" height="12" border="0" style="cursor:pointer" align="top" /> 
  </div>
<p align="right"><span class="rmqbleu">Format de la page : </span> 
  <input name="format" type="radio" id="formathtml" tabindex="2" onchange="change_format('html'); msg_format();" value="html"<?php if ($oldpage['format'] == 'html') {echo ' checked="checked"'; } ?> />
  <label for="formathtml">HTML</label>
  <input name="format" type="radio" id="formattexte" tabindex="3" onchange="change_format('texte'); msg_format();" value="bbcode"<?php if ($oldpage['format'] == 'bbcode' or $_GET['do'] == 'creer') {echo ' checked="checked"';}?> />
  <label for="formattexte">Texte</label>
  <br />
  <span class="petit"> Format html : N'ins&eacute;rer dans la page que le <acronym title="entre les balises &lt;body&gt; et &lt;/body&gt;">corps du document</acronym></span>
</p>
<?php
	if ($_GET['do'] == 'rediger' or $_GET['do'] == 'editer') 
	{
		echo '<input type="hidden" name="num" value="'.$_GET['num'].'">';
	}
	if ($_GET['do'] == 'creer')
	{
		if ($_GET['msg'] == 'format')
		{
?>
<div class="msg">
<p align="center" class="rmq">Le nom de la page a un format incorrect !</p>
<p align="center" class="petit"> Il ne peut contenir que les caract&egrave;res (a-z A-Z 0-9 - _)<br />
    et ne peut pas commencer par une lettre suivie de _ </p>
</div>
<?php
		}
		else if ($_GET['msg'] == 'existe')
		{
?>
<div class="msg">
<p align="center" class="rmq">Le nom '<?php echo htmlentities($_GET['nom'], ENT_QUOTES); ?>' ne peut pas &ecirc;tre utilis&eacute;, <br />
il est d&eacute;j&agrave; employ&eacute; par une page existante.</p>
</div>
<?php
		}
?>
        <p class="rmqbleu">Nom de la page* : 
          <input name="page" id="nom_page" type="text" size="20" maxlength="20" value="<?php if ($_GET['do'] != 'creer') {echo $oldpage['page']; } else {echo $_GET['nompage']; } ?>" onchange="if (check_nom_page(this.value)) alert('Petit conseil, afin de t\'éviter de perdre des données.\nLe nom de la page que tu viens de fournir n\'est peut-être pas utilisable. Envoie donc le formulaire sans remplir le contenu de la page, tu pourras le faire plus tard.');" tabindex="4" />
        </p>
<?php
	}
	else
	{
		echo '<p>Nom de la page : <span class="rmqbleu">'.$oldpage['page'].'</span>';
		// on produit l'adresse de la page
		// avec ou sans url-rewriting
		// nivcible ne récupère sa valeur alphabétique que s'il n'est pas à g.
		// g ne correspond à aucune section et serait donc vide.
		// indexX correspond à la page d'accueil d'une section, avec l'url-rewriting, on se permet de ne pas indiquer le niveau
		$nivcible = ($oldpage['specifiquesection'] != 0) ? $sections[$oldpage['specifiquesection']]['site_section'] : 'g';
		$niv_url_rew = ($nivcible != 'g' and $oldpage['page'] != 'index'.$nivcible) ? $nivcible.'_' : '';
		$niv_no_url_rew = ($nivcible != 'g') ? 'niv='.$nivcible.'&amp;' : '';
		$adresse = ($site['url_rewriting_actif'] == 1) ? $niv_url_rew.$oldpage['page'].'.htm' : 'index.php?'.$niv_no_url_rew.'page='.$oldpage['page'];
		echo '<br /><span class="petit">Adresse de la page : <a href="'.$adresse.'" class="rmqbleu">'.$adresse.'</a></p>';
		if ($oldpage['format'] == 'html')
		{ // en format html, le contenu est seulement soumis à un addslashes à l'enregistrement
			$oldpage['contenupage'] = stripslashes($oldpage['contenupage']);
		}
	}
?>
	<p><span class="rmqbleu">Section cible :</span> <select name="specifiquesection" id="specifiquesection" tabindex="5">
          <option value="0"<?php echo ($oldpage['specifiquesection'] == 0 or $nivcible == 'g') ? ' selected' : ''; ?>>Aucune, 
          page générale</option>
<?php
	foreach ($sections as $unesection)
	{
		if (!empty($unesection['site_section']))
		{
			$selectionne = ($oldpage['specifiquesection'] == $unesection['numsection'] or $nivcible == $unesection['numsection']) ? ' selected' : '';
			echo '<option value="'.$unesection['numsection'].'"'.$selectionne.'>'.$unesection['nomsection'].'</option>';
		}
	}
?>
        </select></p>
      <p align="center"> <img src="templates/default/images/imgdroite.png" width="18" height="12" onclick="addimg(getElement('formathtml').checked);" alt="Image" title="Ins&eacute;rer des images dans les pages." style="cursor:pointer" /> 
        <img onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<div align=\"center\">","</div>");} else {add_tag("contenupage", "[c]", "[/c]");}' alt="Centr&eacute;" src="templates/default/images/centre.png" width="18" height="12" border="0" style="cursor:pointer" /> 
        <img onclick='add_tag("contenupage", open_tag+"b"+close_tag,open_tag+"/b"+close_tag)' alt="Gras" src="templates/default/images/gras.png" width="12" height="12" border="0" style="cursor:pointer" /> 
        <img onclick='add_tag("contenupage", open_tag+"i"+close_tag,open_tag+"/i"+close_tag)' alt="Italique" src="templates/default/images/italique.png" width="12" height="12" border="0" style="cursor:pointer" /> 
        <img onclick='add_tag("contenupage", open_tag+"u"+close_tag,open_tag+"/u"+close_tag)' alt="Souligné" src="templates/default/images/souligne.png" width="12" height="12" border="0" style="cursor:pointer" /> 
        <img onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<a href=\"\">","</a>");} else {add_tag("contenupage", "[url=]", "[/url]");}' alt="Lien internet" src="templates/default/images/url.png" width="12" height="12" border="0" style="cursor:pointer" /> 
        <img src="templates/default/images/rmqbleu.png" alt="Bleu" width="12" height="12" title="Sélectionne le texte qui constitue une remarque (en bleu)"  onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<span class=\"rmqbleu\">","</span>");} else {add_tag("contenupage", "[bleu]", "[/bleu]");}' /> 
        <img src="templates/default/images/rmq.png" alt="Rouge" title="Sélectionne le texte qui constitue une remarque (en rouge)" onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<span class=\"rmq\">","</span>");} else {add_tag("contenupage", "[rouge]", "[/rouge]");}' /> 
        <img onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<a href=\"mailto:\">","</a>");} else {add_tag("contenupage", "[mail=]", "[/mail]");}' alt="Email" src="templates/default/images/mail.png" width="18" height="12" border="0" style="cursor:pointer" /> 
        <input type="button" name="Button" value="petit" onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<span class=\"petit\">","</span>");} else {add_tag("contenupage", "[petit]", "[/petit]");}' title="Sélectionne le texte qui apparaîtra en petite taille" /> 
        <input type="button" name="Button" value="petit en bleu" onclick='if (getElement("formathtml").checked) {add_tag("contenupage", "<span class=\"petitbleu\">","</span>");} else {add_tag("contenupage", "[petitbleu]", "[/petitbleu]");}' title="Sélectionne le texte qui sera mis en bleu clair et en petite taille" /> 
		 <span class="petit" title="insérer un tableau (lignes x colonnes)"> Tableau
	    :</span> 
		 <input name="lignes" id="lignes" type="text" value="1" size="2" title="Nombre de lignes" onchange="if (this.value == '') {this.value = 1;}" />
        x 
        <input name="colonnes" id="colonnes" type="text" value="1" size="2" title="Nombre de colonnes" onchange="if(this.value == '') {this.value = 1;}" /> 
        <input type="button" name="Button" value="insérer" onclick="addtableau('contenupage')" /> 
  </p>
        <textarea name="contenupage" id="contenupage" class="sys" rows="15" cols="70" onselect="storeCaret(this);" onclick="storeCaret(this);" onkeyup="storeCaret(this);" tabindex="6" style="width:95%;"><?php echo stripslashes(htmlspecialchars($oldpage['contenupage'], ENT_QUOTES)); ?></textarea> 
    <p align="center">
    <input type="hidden" name="do" value="<?php echo $afaire; ?>" id="afaire" />
    <input type="submit" name="envoi" value="Enregistrer les modifications" tabindex="7" />
  </p>
<?php panneau_smileys('contenupage'); ?>
</form>
<div class="instructions">
  <h2>Rien de compliqu&eacute; !</h2>
  <p>Tu peux cr&eacute;er toi-m&ecirc;me les pages du portail. <strong>Il te suffit 
    d'&eacute;crire le texte</strong>. Comme le portail est collaboratif, les autres 
    membres du staff peuvent compl&eacute;ter tes pages. N'h&eacute;site pas &agrave; 
    simplement faire des &quot;<em>premiers jets</em>&quot; et &agrave; les enregistrer.</p>
  <p>Si tout &agrave; coup, l'envie te prend de faire une mise en page plus avanc&eacute;e, 
    tu peux choisir le format html. N'h&eacute;site pas &agrave; consulter ce 
    <a href="http://www.siteduzero.com/">tr&egrave;s bon site</a> pour en savoir 
    plus sur ce langage plus simple qu'il n'y parait.</p>
</div>

<div class="instructions">
<p align="center">
<input type="button" tabindex="9" onclick="if(getElement('txt_instr').style.display == 'none') {getElement('txt_instr').style.display = 'block'; this.value = 'Masquer les conseils';} else {getElement('txt_instr').style.display = 'none'; this.value = 'Afficher les conseils';}" value="Afficher les conseils" />
</p>
<div id="txt_instr" style="display:none;">
  <p><span class="rmq">Mettre un peu de couleurs</span><br />
	  Pour d&eacute;corer tes pages, s&eacute;lectionne le texte &agrave; 
	  mettre en forme et clique sur l'un des boutons de mise en forme.</p>
	<div>Ou manuellement, pour <strong>mettre en gras</strong> par exemple 
	  : <br />
	  - En format html : <code>&lt;b&gt;</code><strong>Mon texte en gras</strong><code>&lt;/b&gt;</code></div>
	<div>- En format texte :<code> [b]</code><strong>Mon texte en gras</strong><code>[/b]</code> </div>
<?php
	if ($_GET['do'] == 'creer')
	{
?>
	<p><span class="rmq">Nom de la page</span><br />
	  Le nom de la page est le nom du fichier &agrave; enregistrer. Il ne 
	  peut contenir que les lettres de l'alphabet sans accents, ou les chiffres 
	  de 0 &agrave; 9, ainsi que le tiret - et le caract&egrave;re soulign&eacute; _ . Essaie de choisir un nom parlant 
	  pour les visiteurs : si ta page parle des pilotis, tu peux l'appeler 
	  &quot;pilotis&quot; ou &quot;construction-pilotis&quot; ou tout autre 
	  nom.</p> 
  <?php
	}
?>
  <p><span class="rmq">Choisis un titre pour la page</span><br />
    Le titre n'est pas obligatoire. Il indique au visiteur ce que contient la 
    page qu'il s'appr&ecirc;te &agrave; lire. Sois original <img src="img/smileys/003.gif" alt="" width="15" height="15" /><br />
    Tu peux laisser la case titre vide si tu souhaites donner une mise en page 
    sp&eacute;cifique &agrave; la page.</p>
	<p><span class="rmq">La Section cible</span><br />
	  Comme tu l'as certainement remarqu&eacute;, le portail est divis&eacute; 
	  en plusieurs parties. Chaque section pr&eacute;sente dans l'unit&eacute; 
	  peut avoir son propre espace et ses propres pages. Eh bien la section 
	  cible est la section dont d&eacute;pend la page que tu cr&eacute;es.</p>
	
  <p><span class="rmq">Cr&eacute;er un lien dans le menu</span><br />
    La page que tu cr&eacute;es n'est pas automatiquement accessible. Il faut 
    cr&eacute;er des liens vers elle depuis d'autres pages ou depuis le menu. 
    Si tu souhaites ajouter un lien vers la page dans le menu, clique sur le lien 
    &quot;<a href="index.php?page=gestion_menus">G&eacute;rer les menus</a>&quot; 
    dans le menu ou sur le lien &quot;Ajouter un lien&quot; dans la section o&ugrave; 
    tu veux ajouter le lien.</p>
  <p><span class="rmq">Format de la page : texte ou html ?</span><br />
	  Le format html te permet de faire une mise en page tr&egrave;s riche 
	  du texte : tableaux, listes, alignements gauche et droite, ... presque 
	  sans limite.<br />
	  Le format texte est partiellement enrichi gr&acirc;ce &agrave; quelques 
	  balises. Il te permet d&eacute;j&agrave; de r&eacute;aliser des pages 
	  facilement avec un minimum de design.<br />
	  Si tu d&eacute;cides de changer le format de r&eacute;daction de la 
	  page durant la r&eacute;daction (en particulier pour passer du format 
	  html au format texte), pense &agrave; adapter les balises en fonction 
	  du nouveau format sinon tu risques d'avoir quelques surprises...</p>
	<p><span class="rmq">Format html</span><br />
	  Si tu r&eacute;diges une page dans ce format &agrave; l'aide d'un logiciel 
	  de cr&eacute;ation web, fais un copier-coller du code html se trouvant 
	  entre les balises <code>&lt;body&gt;</code> et <code>&lt;/body&gt;</code>. 
	  Pour te faciliter le travail, tu peux t&eacute;l&eacute;charger la <a href="templates/default/style.css" target="_blank">feuille 
	  de styles CSS</a> du portail pour l'exploiter lors de la mise en page.</p>
	<p><span class="rmq">Des images</span><br />
	  Quel que soit le format de r&eacute;daction de la page, tu peux ins&eacute;rer 
	  facilement les images se trouvant sur le portail. Il te suffit de cliquer 
	  sur le bouton &quot;Image&quot;, de retrouver l'image et de cliquer 
	  dessus pour l'ins&eacute;rer automatiquement (une balise <code>[img=adressedelimage]</code> 
	  en format texte, <code>&lt;img src=&quot;adressedelimage&quot; ... /&gt;</code> 
	  en format html).</p>
</div>
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
<?php
}
else if ($_POST['do'] == 'save' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // on crée la nouvelle page
	$page = desaccentuer($_POST['page']); // on est gentil, on retire les lettres accentuées
	if (ereg("^[-a-z0-9_]{1,20}$", $page) and !ereg("^[a-z]{1}_", $page))
	{ // le format du nom de la page est correct
		// on vérifie que la $page n'existe pas dans la db
		$sql = "SELECT numpage FROM ".PREFIXE_TABLES."pagessections WHERE page = '$page'";
		$res = send_sql($db, $sql);
		$titre = htmlentities($_POST['titre'], ENT_QUOTES);
		$page = htmlentities($page, ENT_QUOTES);
		if (!file_exists($page.'.php') and mysql_num_rows($res) == 0)
		{ // on vérifie qu'il n'y a pas un fichier $page.php qui existe (ils ont priorité)
			$statut = (!empty($_POST['contenupage']) or !empty($titre)) ? 1 : 0;
			
			// on balance le code offensif d'un animateur rigolo avec killscriptanimateur()
			if ($_POST['format'] == 'html')
			{ // on se contente d'antislasher les guillemets et apostrophes
				$contenupage = addslashes(killscriptanimateur($_POST['contenupage']));
			}
			else
			{ // on désactive l'ensemble du code html dans le format texte
				$contenupage = htmlentities(killscriptanimateur($_POST['contenupage']), ENT_QUOTES);
			}
			$sql = "INSERT INTO ".PREFIXE_TABLES."pagessections 
			(page, specifiquesection, statut, format, auteur, datecreation, titre, contenupage, lastmodif, lastmodifby) 
			values 
			('$page', '$_POST[specifiquesection]', '$statut', '$_POST[format]', '$user[num]', now(), '$titre', '$contenupage', now(), '$user[num]')";
			send_sql($db, $sql);
			$nivcible = ($_POST['specifiquesection'] > 0) ? $sections[$_POST['specifiquesection']]['site_section'] : 'g';
			log_this("Création page site ($page)", 'pagesection');
			if ($statut == 1)
			{ // la page contient du texte
				header('Location: index.php?niv='.$nivcible.'&page='.$page);
			}
			else
			{ // la page est vide
				header('Location: index.php?page=pagesection');
			}
		}
		else
		{ // la page existe déjà avec ce nom
			header('Location: index.php?page=pagesection&do=creer&msg=existe&nom='.$page);
		}
	}
	else
	{ // $page n'a pas le bon format
		header('Location: index.php?page=pagesection&do=creer&msg=format');
	}
}
else if ($_POST['do'] == 'savemodif' and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
{ // on enregistre les modifications apportées à une page existante
	$statut = (!empty($_POST['contenupage'])) ? 1 : 0;
	// on balance le code offensif d'un animateur rigolo avec killscriptanimateur()
	if ($_POST['format'] == 'html')
	{ // on se contente d'antislasher les guillemets et apostrophes
		$contenupage = addslashes(killscriptanimateur($_POST['contenupage']));
	}
	else
	{ // on désactive l'ensemble du code html dans le format texte
		$contenupage = htmlentities(killscriptanimateur($_POST['contenupage']), ENT_QUOTES);
	}
	$titre = htmlentities($_POST['titre'], ENT_QUOTES);
	$sql = "UPDATE ".PREFIXE_TABLES."pagessections SET specifiquesection = '$_POST[specifiquesection]', statut = '$statut', format = '$_POST[format]', titre = '$titre', contenupage = '$contenupage', lastmodif = now(), lastmodifby = '$user[num]' WHERE numpage = '$_POST[num]'";
	send_sql($db, $sql);
	$sql = "SELECT page, specifiquesection FROM ".PREFIXE_TABLES."pagessections WHERE numpage = '$_POST[num]'";
	$res = send_sql($db, $sql);
	$ligne = mysql_fetch_assoc($res);
	$page = $ligne['page'];
	$specifiquesection = $ligne['specifiquesection'];
	$nivcible = ($_POST['specifiquesection'] > 0) ? $sections[$_POST['specifiquesection']]['site_section'] : 'g';
	log_this("Modification page site ($_POST[num])", 'pagesection');
	header('Location: index.php?niv='.$nivcible.'&page='.$page);
}
else
{
	include('404.php');
}
?>