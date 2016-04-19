<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* recapitulatif_droits.php v 1.1 - Tableau montrant les droits des différents niveaux d'utilisateur
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
*	gestion sizaines et patrouilles
*/

include_once('connex.php');
include_once('fonc.php');

if (!defined('IN_SITE'))
{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>R&eacute;capitulatif des droits utilisateur</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
}
?>
<h1>Tableau r&eacute;capitulatif des droits utilisateurs selon leur niveau</h1>
<p align="center"><a href="index.php?page=aide">Retour &agrave; l'aide</a></p>
<div class="introduction">
<p class="petitbleu">Les statuts des membres du portail sont tri&eacute;s en plusieurs 
  niveaux d'acc&egrave;s. A ces niveaux on ajoute un niveau sp&eacute;cial de 
  co-webmaster (Coweb) qui peut &ecirc;tre attribu&eacute; &agrave; certains utilisateurs 
  du portail.</p>
<p>Chaque fonction est soit : </p>
<ul>
  <li>en acc&egrave;s complet <img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" />,</li>
  <li>accessible sous certaines conditions <img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /> 
    indiqu&eacute;es juste en dessous,</li>
  <li>ou interdite d'acc&egrave;s <img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" />.</li>
</ul>
<p>Voici la liste des statuts du portail ainsi que leur niveau de droits correspondant 
  :</p>
</div>
<?php
	if (is_array($niveaux))
	{
		echo '<table cellpadding="2" cellspacing="0" align="center" class="cadrenoir">';
			echo '<tr>
			<th>Statut</th>
			<th>Niveau d\'acc&egrave;s</th>
			</tr>';
		$i = 0;
		foreach($niveaux as $niveau)
		{
			$i++;
			$couleur = ($i % 2 == 0) ? 'td-1' : 'td-2';
			echo '<tr class="'.$couleur.'">
			<td>'.$niveau['nomniveau'].'</td>
			<td>'.$intitules_niveaux[$niveau['numniveau']].'</td>
			</tr>';
		}
		echo '</table>';
	}
?>
<p>&nbsp;</p>
<table align="center" cellpadding="2" cellspacing="0" class="cadrenoir">
  <tr> 
    <th height="25" align="right">&nbsp;</th>
    <th width="40" align="center" title="Visiteur non identifié">Ano</th>
    <th width="40" align="center" title="Visiteur identifié">Vis</th>
    <th width="40" align="center" title="Membre de l'Unité">MbU</th>
    <th width="40" align="center" title="Co-webmaster">Coweb</th>
    <th width="40" align="center" title="Animateur de Section">AnS</th>
    <th width="40" align="center" title="Animateur d'Unité">AnU</th>
    <th width="40" align="center" title="Webmaster">Web</th>
  </tr>
  <tr> 
    <th align="left">Modules d'acc&egrave;s public</th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
  </tr>
  <tr> 
    <td align="right">Signer le livre d'or</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Mod&eacute;rer le livre d'or</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right"><strong>Consulter les albums photos</strong></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Commenter les photos</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Cr&eacute;er/Modifier/Supprimer des albums</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1">
    <td align="right">Mod&eacute;rer les commentaires</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right"><strong>Lire les articles du Tally</strong></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Modifier/Supprimer des articles</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right" class="td-1"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Modification 
      et Suppression pour l'auteur des articles</td>
  </tr>
  <tr> 
    <td align="right"><strong>Page de t&eacute;l&eacute;chargements</strong></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Acc&egrave;s 
      selon le niveau minimum requis pour le fichier</td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Ajouter des fichiers &agrave; t&eacute;l&eacute;charger</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right"><strong>Lire les news</strong></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Poster des news</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right"><strong>Envoyer la newsletter &agrave; ses abonn&eacute;s</strong> 
    </td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <th align="left">Modules de Gestion de l'Unit&eacute;</th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
  </tr>
  <tr> 
    <td align="right"><strong>Gestion de l'Unit&eacute;</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Ajouter un membre</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Modifier un membre</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Ajout 
      et Modification pour l'animateur de la section concern&eacute;e</td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Supprimer un membre</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Ajouter une famille</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Modifier une famille</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Supprimer une famille</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Ajouter un Ancien</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Modifier un Ancien</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Supprimer un Ancien</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Passage de Section &agrave; Section</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Lire l'&eacute;tat de paiement des cotisations</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">G&eacute;rer l'&eacute;tat de paiement des cotisations</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Gestion des sizaines/patrouilles</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Modification 
      pour la Section de l'utilisateur</td>
  </tr>
  <tr> 
    <td align="right"><strong>Gestion des Sections</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Ajouter une Section/Unit&eacute;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Modifier une Section/Unit&eacute;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Modification 
      pour la Section de l'utilisateur</td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Supprimer une Section/Unit&eacute;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-2"> 
    <td align="right"><strong>Lire et poster des pages restreintes</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Modifier et supprimer des pages restreintes</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Modification 
      et Suppression pour l'auteur des pages restreintes</td>
  </tr>
  <tr> 
    <th align="left">Modules de Gestion du portail</th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
  </tr>
  <tr> 
    <td align="right"><strong>Activer/D&eacute;sactiver l'espace web des Sections</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right"><strong>Cr&eacute;er/Editer/Publier des pages sur le portail</strong></td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <td align="right">Supprimer des pages du portail</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr class="td-1"> 
    <td align="right">G&eacute;rer les menus du portail</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <th align="left">Gestion des Membres du portail</th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
  </tr>
  <tr> 
    <td align="right">Reconnaissance de membres (Animateur de Section et d'Unit&eacute;)</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr align="right"> 
    <td colspan="8" align="center" class="petitbleu"><img src="templates/default/images/inconnu.png" alt="Acc&egrave;s conditionnel" width="12" height="12" align="top" />Reconnaissance 
      de membres demandant un niveau d'acc&egrave;s inf&eacute;rieur ou &eacute;gal 
      &agrave; celui de l'utilisateur.</td>
  </tr>
  <tr class="td-1"> 
    <td align="right">Attribuer le statut de co-webmaster &agrave; un membre</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></td>
    <td align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></td>
  </tr>
  <tr> 
    <th align="left">Configuration g&eacute;n&eacute;rale du Portail</th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/supprimer.png" alt="Pas d'acc&egrave;s" width="12" height="12" align="top" /></th>
    <th align="center"><img src="templates/default/images/ok.png" alt="acc&egrave;s complet" width="12" height="12" align="top" /></th>
  </tr>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
	if (!defined('IN_SITE'))
	{
?>
</body>
</html>
<?php
	}
?>