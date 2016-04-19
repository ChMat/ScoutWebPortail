<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* menu_site.php v 1.1.1 - Cette page génère le menu du portail
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
* Modifications v 1.1.1
*	Affichage ou non du lien vers la gestion de l'unité
*	Ajout des classes unite_x et section_x pour un design avancé des menus de section
*/

if (!defined('IN_SITE') or $page == 'menu_site')
{
	header('location: index.php?page=404');
	exit;
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Menu du portail</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="templates/default/style.css" />
</head>
<body>
<?php
}
if ($site['modele_menu'] == 'complet')
{
/////////////////////////////////////////////////////////
// Menu complet par unité
// Ce menu regroupe les menus des sections à l'intérieur
// des unités.
/////////////////////////////////////////////////////////
?>
<div id="menu"> 
<?php
	if ($user['niveau']['numniveau'] > 0)
	{
?>
<a id="pseudo_membre" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>" title="Tu es connect&eacute; sur le portail sous ce pseudo. Voir ton profil"><?php echo $user['pseudo']; ?></a> 
<?php
	}
	if ($user['niveau']['numniveau'] > 0)
	{
?>
<ul id="menu_membre">
  <li><a class="accueil" href="index.php?page=membres">Accueil Membres</a></li> 
  <li><a class="profil" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>">Mon profil</a></li>
<?php
		if ($user['niveau']['numniveau'] > 2)
		{
			if ($site['gestion_membres_actif'] == 1)
			{
?>
  <li><a class="gestion_unite" href="index.php?page=gestion_unite">Gestion de l'Unit&eacute;</a></li>
<?php
			}
?>
  <li><a class="pages_restreintes" href="index.php?page=pagesrestreintes">Pages restreintes</a></li>
<?php
		}
		if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
		{
?>
  <li><a class="pages_sections" href="index.php?page=pagesection">Les pages du site</a></li>
<?php
		}
?>
</ul>
<?php
	}
?>
<ul id="menu_base">
<li><a class="accueil" href="index.php">Accueil Site</a></li>
<?php
	if ($user == 0)
	{
?>
  <li><a class="login" href="index.php?page=login">Connexion</a></li>
  <li><a class="inscription" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">Inscription</a></li>
<?php
	}
?>
  <li><a class="forum" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum'; ?>">Forum</a></li>
  <li><a class="tally" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally'; ?>">Tally</a></li>
</ul>
<?php
	if (!empty($niv))
	{
	/////////////////////////////////////////////////////////////////
	// Affichage des Menus par section et unité
	/////////////////////////////////////////////////////////////////
	// On parcourt chaque section de l'array. Si c'est une unité, on l'affiche puis son menu propre.
	// Ensuite, on parcourt chaque section appartenant à cette unité et on affiche leur menu propre.
	// Ensuite, on passe éventuellement à chaque autre unité et on recommence.
		if (is_array($sections))
		{
			$tri = array('position_section', 'numsection');
			$liste_sections = super_sort($sections);
			foreach ($liste_sections as $unite)
			{
				$i = 0; // indique si une liste de sections est ouverte (<ul> non fermé)
				if (is_unite($unite['numsection']))
				{
					/////////////////////////////////////////////////////////////////
					// Menu unité
					/////////////////////////////////////////////////////////////////
					if (!empty($unite['site_section']))
					{ 
						echo ($i == 0) ? '<ul class="sections_unite">'."\n" : '';
						$i++;
						// nom de l'unité
?>
<li class="<?php echo 'unite_'.$unite['site_section']; ?>"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'index'.$unite['site_section'].'.htm' : 'index.php?niv='.$unite['site_section']; ?>" class="unite"><?php echo $unite['nomsectionpt']; ?></a><?php
						$j = 0; // indique si un menu de section est ouvert (<ul> non fermé)
						if (($unite['site_section'] == $niv or $site['deploy_menu_unite'] == 1) and is_array($site_menus))
						{ // items du menu de l'unité
							foreach ($site_menus as $menu)
							{
								if ($menu['section_menu'] == $unite['numsection'])
								{
									echo ($j == 0) ? '<ul>' : ''; // début liste des items du menu de l'unité
									$j++;
									$menu['description_menu'] = (!empty($menu['description_menu'])) ? ' title="'.$menu['description_menu'].'"' : '';
									echo "\n".'<li><a href="'.$menu['lien_menu'].'"'.$menu['description_menu'].'>'.$menu['texte_menu'].'</a></li>';
								}
							}
						} // fin if is_array($site_menus)
						if ($site['show_menu_vide'] and $j == 0 and ($unite['site_section'] == $niv or $site['deploy_menu_unite'] == 1))
						{ // si le menu actif ne comporte aucun lien, on le signale au visiteur
							echo "\n".'<ul>'; // début du menu de l'unité
							echo "\n".'<li class="aucun_lien" title="Ce menu n\'a pas encore été créé.">Aucun lien</li>';
						}
						if ($unite['site_section'] == $niv and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
						{ // ajouter un lien dans le menu de la section en cours
							echo "\n".'<li><a class="new_lien" href="index.php?page=gestion_menus&amp;do=ajoutermenu&amp;section='.$unite['numsection'].'" title="Ajouter un lien au menu de la Section '.$section['appellation'].'">Ajouter un lien</a></li>';
						}
						//echo ($unite['site_section'] == $niv or $site['deploy_menu_unite'] == 1) ? '</ul>'."\n" : ''; // fin du menu de l'unité
						// A vérifier si menu vide et niveau > 2
						echo (($unite['site_section'] == $niv or $site['deploy_menu_unite'] == 1) and ($site['show_menu_vide'] or $user['niveau']['numniveau'] > 2)) ? '</ul>'."\n" : ''; // fin du menu de l'unité
?></li>
<?php
					}
					/////////////////////////////////////////////////////////////////
					// fin du menu unité
					/////////////////////////////////////////////////////////////////
	
					// affichage des menus par unité et par section
					foreach ($liste_sections as $section)
					{
						if (!empty($section['site_section']) and $section['unite'] == $unite['numsection'])
						{ // on parcourt les sections de l'unité en cours pour afficher leur menu
						  // affichage de l'en-tête d'une section
							echo ($i == 0) ? '<ul class="sections_unite">'."\n" : ''; // on initialise le menu des noms de sections si l'unité n'est pas affichée
							$i++;
?>
<li class="<?php echo 'section_'.$section['site_section']; ?>"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'index'.$section['site_section'].'.htm' : 'index.php?niv='.$section['site_section']; ?>" class="section"><?php echo $section['nomsectionpt']; ?></a><?php
							$j = 0;
							if (($section['site_section'] == $niv or $site['deploy_menu_section']) and is_array($site_menus))
							{
								foreach ($site_menus as $menu)
								{ // on parcourt les menus à la recherche de ceux de l'unité/section en cours
									if ($menu['section_menu'] == $section['numsection'])
									{ // affichage d'un lien de la section en cours
										echo ($j == 0) ? "\n".'<ul>' : '';
										$j++;
										$menu['description_menu'] = (!empty($menu['description_menu'])) ? ' title="'.$menu['description_menu'].'"' : '';
										echo "\n".'<li><a href="'.$menu['lien_menu'].'"'.$menu['description_menu'].'>'.$menu['texte_menu'].'</a></li>';
									}
								} // fin foreach $site_menus
							} // fin if is_array($site_menus)
							if ($site['show_menu_vide'] and $j == 0 and ($section['site_section'] == $niv or $site['deploy_menu_section']))
							{ // si le menu actif ne comporte aucun lien, on le signale au visiteur
								echo "\n".'<ul>';
								echo "\n".'<li class="aucun_lien" title="Ce menu n\'a pas encore été créé.">Aucun lien</li>';
							}
							if ($section['site_section'] == $niv and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
							{ // ajouter un lien dans le menu de la section en cours
								echo "\n".'<li><a class="new_lien" href="index.php?page=gestion_menus&amp;do=ajoutermenu&amp;section='.$section['numsection'].'" title="Ajouter un lien au menu de la Section '.$section['appellation'].'">Ajouter un lien</a></li>';
							}
							// echo ($section['site_section'] == $niv or $site['deploy_menu_section']) ? '</ul>'."\n" : '';
							// A vérifier si niveau > 2
							echo (($section['site_section'] == $niv or $site['deploy_menu_section']) and ($site['show_menu_vide'] or $user['niveau']['numniveau'] > 2)) ? '</ul>'."\n" : '';
							
?></li>
<?php
						}
					} // fin affichage menu par unités, sections (foreach $sections)
					echo ($i > 0) ? "\n".'</ul>' : '';
				} // fin du if is_unite
			} // fin du foreach unite
		} // fin du if is_array($sections)
	}
	if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
	{
?>
  <a id="gestion_menu" href="index.php?page=gestion_menus" title="G&eacute;rer les menus">G&eacute;rer les menus</a> 
<?php
	}
	// Affichage du menu général
	echo $site['menu_standard'];
?>
<?php
	if ($user != 0)
	{
?>
<div id="logoff"> 
<input type="button" value="Déconnexion" onclick="if (confirm('Es-tu certain de vouloir te déconnecter ?')) window.location = '<?php echo ($site['url_rewriting_actif'] == 1) ? 'logoff.htm' : 'index.php?page=logoff'; ?>';" />
</div>
<?php
	}
?>
<?php
	if ($site['log_visites'] == 1 and $site['show_enligne'] == 1)
	{ // inclure l'onglet affichant les visiteurs/membres connectés sur le portail
		include_once('onglet.php');
	}
?>
</div>
<?php
} // Fin du menu complet
else if ($site['modele_menu'] == 'complet_melange')
{
/////////////////////////////////////////////////////////
// Menu complet mélangé
// Ce menu permet de mélanger les section de plusieurs
// unités, ce que ne permet pas le menu complet
/////////////////////////////////////////////////////////
?>
<div id="menu"> 
<?php
	if ($user['niveau']['numniveau'] > 0)
	{
?>
<a id="pseudo_membre" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>" title="Tu es connect&eacute; sur le portail sous ce pseudo. Voir ton profil"><?php echo $user['pseudo']; ?></a> 
<?php
	}
	if ($user['niveau']['numniveau'] > 0)
	{
?>
<ul id="menu_membre">
  <li><a class="accueil" href="index.php?page=membres">Accueil Membres</a></li> 
  <li><a class="profil" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'monprofil.htm' : 'index.php?page=monprofil'; ?>">Mon profil</a></li>
<?php
		if ($user['niveau']['numniveau'] > 2)
		{
			if ($site['gestion_membres_actif'] == 1)
			{
?>
  <li><a class="gestion_unite" href="index.php?page=gestion_unite">Gestion de l'Unit&eacute;</a></li>
<?php
			}
?>
  <li><a class="pages_restreintes" href="index.php?page=pagesrestreintes">Pages restreintes</a></li>
<?php
		}
		if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
		{
?>
  <li><a class="pages_sections" href="index.php?page=pagesection">Les pages du site</a></li>
<?php
		}
?>
</ul>
<?php
	}
?>
<ul id="menu_base">
<li><a class="accueil" href="index.php">Accueil Site</a></li>
<?php
	if ($user == 0)
	{
?>
  <li><a class="login" href="index.php?page=login">Connexion</a></li>
  <li><a class="inscription" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'inscr.htm' : 'index.php?page=inscr'; ?>">Inscription</a></li>
<?php
	}
?>
  <li><a class="forum" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'forum.htm' : 'index.php?page=forum'; ?>">Forum</a></li>
  <li><a class="tally" href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'tally.htm' : 'index.php?page=tally'; ?>">Tally</a></li>
</ul>
<?php
	if (!empty($niv))
	{
	/////////////////////////////////////////////////////////////////
	// Affichage des Menus par section et unité
	/////////////////////////////////////////////////////////////////
	// On parcourt chaque section/unité et on affiche son menu.
		if (is_array($sections))
		{
			$tri = array('position_section', 'numsection');
			$liste_sections = super_sort($sections);
			$i = 0; // indique si une liste de sections est ouverte (<ul> non fermé)
			foreach ($liste_sections as $section)
			{
				// affichage des menus par unité et par section
				if (!empty($section['site_section']))
				{ // on parcourt les sections de l'unité en cours pour afficher leur menu
				  // affichage de l'en-tête d'une section
					echo ($i == 0) ? '<ul class="sections_unite">'."\n" : ''; // on initialise le menu des noms de sections si l'unité n'est pas affichée
					$i++;
?>
<li class="<?php echo 'section_'.$section['site_section']; ?>"><a href="<?php echo ($site['url_rewriting_actif'] == 1) ? 'index'.$section['site_section'].'.htm' : 'index.php?niv='.$section['site_section']; ?>" class="section"><?php echo $section['nomsectionpt']; ?></a><?php
					$j = 0;
					if (($section['site_section'] == $niv or $site['deploy_menu_section']) and is_array($site_menus))
					{
						foreach ($site_menus as $menu)
						{ // on parcourt les menus à la recherche de ceux de l'unité/section en cours
							if ($menu['section_menu'] == $section['numsection'])
							{ // affichage d'un lien de la section en cours
								echo ($j == 0) ? "\n".'<ul>' : '';
								$j++;
								$menu['description_menu'] = (!empty($menu['description_menu'])) ? ' title="'.$menu['description_menu'].'"' : '';
								echo "\n".'<li><a href="'.$menu['lien_menu'].'"'.$menu['description_menu'].'>'.$menu['texte_menu'].'</a></li>';
							}
						} // fin foreach $site_menus
					} // fin if is_array($site_menus)
					if ($site['show_menu_vide'] and $j == 0 and ($section['site_section'] == $niv or $site['deploy_menu_section']))
					{ // si le menu actif ne comporte aucun lien, on le signale au visiteur
						echo "\n".'<ul>';
						echo "\n".'<li class="aucun_lien" title="Ce menu n\'a pas encore été créé.">Aucun lien</li>';
					}
					if ($section['site_section'] == $niv and ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1))
					{ // ajouter un lien dans le menu de la section en cours
						echo "\n".'<li><a class="new_lien" href="index.php?page=gestion_menus&amp;do=ajoutermenu&amp;section='.$section['numsection'].'" title="Ajouter un lien au menu de la Section '.$section['appellation'].'">Ajouter un lien</a></li>';
					}
					// echo ($section['site_section'] == $niv or $site['deploy_menu_section']) ? '</ul>'."\n" : '';
					// A vérifier si menu vide et niveau > 2
					echo (($section['site_section'] == $niv or $site['deploy_menu_section']) and ($site['show_menu_vide'] or $user['niveau']['numniveau'] > 2)) ? '</ul>'."\n" : '';
?></li>
<?php
				} // fin du !empty(site_section)
			} // fin du foreach section
			echo ($i > 0) ? "\n".'</ul>' : '';
		} // fin du if is_array($sections)
	}
	if ($user['niveau']['numniveau'] > 2 or $user['assistantwebmaster'] == 1)
	{
?>
  <a id="gestion_menu" href="index.php?page=gestion_menus" title="G&eacute;rer les menus">G&eacute;rer les menus</a> 
<?php
	}
	// Affichage du menu général
	echo $site['menu_standard'];
?>
<?php
	if ($user != 0)
	{
?>
<div id="logoff"> 
<input type="button" value="Déconnexion" onclick="if (confirm('Es-tu certain de vouloir te déconnecter ?')) window.location = '<?php echo ($site['url_rewriting_actif'] == 1) ? 'logoff.htm' : 'index.php?page=logoff'; ?>';" />
</div>
<?php
	}
?>
<?php
	if ($site['log_visites'] == 1 and $site['show_enligne'] == 1)
	{ // inclure l'onglet affichant les visiteurs/membres connectés sur le portail
		include_once('onglet.php');
	}
?>
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