<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_sql.php - Fonctions de travail dans la base de données MySQL
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

function send_sql($db, $sql)
{ // soumet une requête sql à la base de données et renvoie le résultat
  // si un erreur se produit, elle est loguée
	global $user, $requetes, $txtrequetes, $ip;
	$requetes++;
	if (defined('LOCAL_SITE'))
	{
		$txtrequetes .= 'requete '.$requetes.' : '.$sql.'<br />'."\n";
	}
	if (!$res = @mysql_db_query($db, $sql))
	{
		if (defined('LOCAL_SITE') or $user['niveau']['numniveau'] == 5)
		{ // cette option ne fonctionne que lorsque le portail est exécuté en local
			echo '<span class="info">Erreur SQL :</span>
<pre class="code">'.mysql_error().'
'.$sql.'</pre>';
		}
		else
		{
			log_this('Erreur SQL a la page '.$_SERVER['REQUEST_URI'].' - Erreur :::: '.mysql_error().' : '.$sql, 'sql', true);
		}
		exit;
	}
	return $res;
}

function tab_out($result, $style = 2, $altcouleur = 0, $titre = '')
// tab_out affiche le résultat d'une requête sql dans un tableau html
// $result : données à afficher
// $style : titre unique (1), en-têtes de colonnes (2), les deux (3)
// $altcouleur : couleur alternée pour les lignes (1 pour on et 0 pour off)
// $titre : texte du titre unique
{
	$nb = mysql_numfields($result);
	$largeur_totale = '100%';
	$largeur= 100 / $nb.'%';
	echo '<table border="0" cellspacing="1" width="'.$largeur_totale.'">';
	if ($style == '1' or $style == '3')
	{
		echo '<tr class="thbleu">';
		echo '<th colspan="'.$nb.'">'.$titre.'</th>';
		echo '</tr>';
	}
	if ($style == '2' or $style == '3')
	{
		echo '<tr class="thbleu">';
		for ($i=0; $i < $nb; $i++)
		{
			echo '<th>';
			echo mysql_field_name($result, $i);
			echo '</th>';
		}
		echo '</tr>';
	}
	$num = mysql_num_rows($result);
	for ($j = 0; $j < $num; $j++)
	{
		$ligne = mysql_fetch_assoc($result);
		if ($altcouleur == '1')
		{
			$mm = $j % 2;
			$couleur = ($mm == 0) ? 'td-1' : 'td-2';
		}
		echo '<tr class="'.$couleur.'">';
		for ($k = 0; $k < $nb; $k++)
		{
			$fn = mysql_field_name($result, $k);
			echo '<td>'.$ligne[$fn].'</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
}

function tab_out_xlignes($result, $style, $altcouleur, $titre, $dbt, $qte)
// tab_out affiche $qte résultats d'une requête sql dans un tableau html à partir du résultat $dbt
// $result : données à afficher
// $style : titre unique (1), en-têtes de colonnes (2), les deux (3)
// $altcouleur : couleur alternée pour les lignes (1 pour on et 0 pour off)
// $titre : texte du titre unique
{
	$nb = mysql_numfields($result);
	$largeur_totale = '100%';
	$largeur= 100 / $nb.'%';
	echo '<table border="0" cellspacing="1" width="'.$largeur_totale.'">';
	if ($style == '1' or $style == '3')
	{
		echo '<tr class="thbleu">';
		echo '<th colspan="'.$nb.'">'.$titre.'</th>';
		echo '</tr>';
	}
	if ($style == '2' or $style == '3')
	{
		echo '<tr class="thbleu">';
		for ($i= 0; $i < $nb; $i++)
		{
			echo '<th>';
			echo mysql_field_name($result, $i);
			echo '</th>';
		}
		echo '</tr>';
	}
	$num = mysql_num_rows($result);
	for ($j = $dbt; $j <= $qte; $j++)
	{
		if ($ligne = mysql_fetch_assoc($result))
		{
			if ($altcouleur == '1')
			{
			$mm = $j % 2;
			$couleur = ($mm == 0) ? 'td-1' : 'td-2';
			}
			echo '<tr class="'.$couleur.'">';
			for ($k = 0; $k < $nb; $k++)
			{
				$fn = mysql_field_name($result, $k);
				echo '<td>'.$ligne[$fn].'</td>';
			}
			echo '</tr>';
		}
	}
	echo '</table>';
}

function untruc($latable, $lechamp, $champtri, $chercher)
{ // cette fonction récupère une valeur dans la base de données selon les critères fournis
	global $db;
	$sql = "SELECT $lechamp FROM $latable WHERE $champtri = '$chercher'";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) == 1)
		{
			$resultat = mysql_fetch_assoc($res);
			return $resultat[$lechamp];
		}
		else
		{
			return 'erreur';
		}
	}
}

function laliste($latable, $lindex, $lechamp, $criteres, $limit = '') 
// cette fonction retourne dans un array le contenu d'un table pour un champ spécifique
// $latable est la table dans laquelle la requête est exécutée
// $lindex est l'index d'array (exple : $latable[$lindex] = $lechamp
// $lechamp est la valeur recherchée
// $criteres peut contenir une requete restrictive
// l'index est prévu pour un accès direct et non séquentiel. en effet, n'importe quel champ peut servir d'index d'array.
// c'est pourquoi, les valeurs peuvent ne pas se suivre.
{
	global $db;
	$sql = "SELECT $lechamp, $lindex FROM $latable $criteres ORDER BY $lindex ASC $limit";
	if ($res = send_sql($db, $sql))
	{
		if (mysql_num_rows($res) > 0)
		{
			while ($ligne = mysql_fetch_assoc($res))
			{
				$retour[$ligne[$lindex]] = $ligne[$lechamp];
			}
			return $retour;
		}
	}
	else
	{
		return false;
	}
}

?>