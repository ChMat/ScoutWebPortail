#
# Update structure db Scout Web Portail v 1.0.x -> v 1.1.1
#

#
# Création des nouvelles tables
#

#
# Structure de la table `scoutwebportail_fichiers_cat`
#

CREATE TABLE `scoutwebportail_fichiers_cat` (
  `cat_id` mediumint(8) NOT NULL auto_increment,
  `cat_titre` varchar(255) NOT NULL default '',
  `cat_description` text NOT NULL,
  PRIMARY KEY  (`cat_id`),
  KEY `cat_titre` (`cat_titre`)
) TYPE=MyISAM;

# --------------------------------------------------------


#
# Modification de la structure des tables
#

# Nouvelle taille des clés de validation
ALTER TABLE `scoutwebportail_auteurs` CHANGE `clevalidation` `clevalidation` CHAR(5) NOT NULL;
ALTER TABLE `scoutwebportail_auteurs` DROP `mbinscrit`, DROP `pwprotected`;

# Nouveau format de la table des news
ALTER TABLE `scoutwebportail_news` CHANGE `numnews` `id_news` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `scoutwebportail_news` CHANGE `newsbannie` `news_bannie` CHAR( 1 ) DEFAULT '0' NOT NULL;
ALTER TABLE `scoutwebportail_news` CHANGE `auteurnews` `auteur_news` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `scoutwebportail_news` CHANGE `news` `texte_news` TEXT NOT NULL;
ALTER TABLE `scoutwebportail_news` ADD `titre_news` VARCHAR(100) NOT NULL AFTER id_news;

UPDATE `scoutwebportail_news` SET `titre_news` = 'Quelques informations';

# Changement de la taille maximale des titres des messages des forums
ALTER TABLE `scoutwebportail_filsforum` CHANGE `titre` `titre` VARCHAR(100) NOT NULL;
ALTER TABLE `scoutwebportail_filsforum_staffs` CHANGE `titre` `titre` VARCHAR(100) NOT NULL;
ALTER TABLE `scoutwebportail_msgforum` CHANGE `titre` `titre` VARCHAR(100) NOT NULL;
ALTER TABLE `scoutwebportail_msgforum_staffs` CHANGE `titre` `titre` VARCHAR(100) NOT NULL;

# Ajout d'une option d'affichage du totem
ALTER TABLE `scoutwebportail_unite_sections` ADD `aff_totem_meute` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ;

#
# Suppression de paramètres de configuration du portail
#

DELETE FROM `scoutwebportail_config` WHERE champ = 'forum_staffs_actif';

#
# Ajout de paramètres de configuration du portail
#

INSERT INTO `scoutwebportail_config` VALUES ('version_portail', '1.1.1');
INSERT INTO `scoutwebportail_config` VALUES ('show_version', '1');
INSERT INTO `scoutwebportail_config` VALUES ('forum_staffs_actif', '1');
INSERT INTO `scoutwebportail_config` VALUES ('show_enligne', '0');
INSERT INTO `scoutwebportail_config` VALUES ('avatar_par_defaut', '');
INSERT INTO `scoutwebportail_config` VALUES ('avatar_max_width', '100');
INSERT INTO `scoutwebportail_config` VALUES ('avatar_max_height', '130');
INSERT INTO `scoutwebportail_config` VALUES ('avatar_max_filesize', '10240');
INSERT INTO `scoutwebportail_config` VALUES ('photo_membre_max_width', '100');
INSERT INTO `scoutwebportail_config` VALUES ('photo_membre_max_height', '130');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_proportions_mini', '140');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_proportions_photo', '500');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_show_nb', '3');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_show_delai', '1 MONTH');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_nb_par_page', '10');
INSERT INTO `scoutwebportail_config` VALUES ('upload_max_filesize', '1048576');
INSERT INTO `scoutwebportail_config` VALUES ('download_max_filesize', '1048576');
INSERT INTO `scoutwebportail_config` VALUES ('nbre_derniers_msg_forum', '10');
INSERT INTO `scoutwebportail_config` VALUES ('deploy_menu_unite', '0');
INSERT INTO `scoutwebportail_config` VALUES ('deploy_menu_section', '0');
INSERT INTO `scoutwebportail_config` VALUES ('show_menu_vide', '1');
INSERT INTO `scoutwebportail_config` VALUES ('modele_menu', 'complet');
INSERT INTO `scoutwebportail_config` VALUES ('droits_anim_valide_statut', '1');
INSERT INTO `scoutwebportail_config` VALUES ('forum_nbfils_par_page', '15');
INSERT INTO `scoutwebportail_config` VALUES ('forum_nbmsg_par_page', '15');


#
# Marquage du portail pour la mise à jour du format de mot de passe
#

INSERT INTO `scoutwebportail_config` VALUES ('update_pw', 'en_cours');

#
# Adaptation du moteur de téléchargements
#

ALTER TABLE `scoutwebportail_fichiers` ADD `cat_id` MEDIUMINT UNSIGNED NOT NULL AFTER `numfichier` ;
ALTER TABLE `scoutwebportail_fichiers` ADD `vedette` TINYINT UNSIGNED NOT NULL AFTER `public` ;
ALTER TABLE `scoutwebportail_fichiers` ADD INDEX ( `cat_id` );

INSERT INTO `scoutwebportail_fichiers_cat` VALUES ('1', 'Tous les fichiers', 'Cette rubrique contient tous les fichiers pr&eacute;sents sur le portail. Tu peux en cr&eacute;er de nouvelles et y d&eacute;placer les fichiers.');

UPDATE `scoutwebportail_fichiers` SET `cat_id` = '1';

# --------------------------------------------------------

#
# Mise en place de la structure du nouveau forum
# le transfert des messages se fait dans update_portail.php

#
# Structure de la table `scoutwebportail_forum_forums`
#

CREATE TABLE `scoutwebportail_forum_forums` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_titre` char(100) NOT NULL default '',
  `forum_description` text NOT NULL,
  `forum_statut` tinyint(1) NOT NULL default '0',
  `forum_nbfils` int(10) unsigned NOT NULL default '0',
  `forum_nbmsg` int(10) unsigned NOT NULL default '0',
  `forum_last_msg_id` int(10) unsigned NOT NULL default '0',
  `forum_last_auteur` int(10) unsigned NOT NULL default '0',
  `forum_moderation` tinyint(3) NOT NULL default '0',
  `forum_acces_niv` tinyint(3) unsigned NOT NULL default '0',
  `forum_acces_section` tinyint(4) NOT NULL default '0',
  `forum_position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`),
  KEY `position` (`forum_position`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_forum_fils`
#

CREATE TABLE `scoutwebportail_forum_fils` (
  `fil_id` int(10) unsigned NOT NULL auto_increment,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `fil_icone` char(3) NOT NULL default '',
  `fil_titre` char(100) NOT NULL default '',
  `fil_statut` tinyint(1) NOT NULL default '0',
  `fil_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `fil_nbmsg` mediumint(8) NOT NULL default '0',
  `fil_nbvues` int(11) NOT NULL default '0',
  `fil_last_msg_id` int(10) unsigned NOT NULL default '0',
  `fil_last_auteur` int(10) unsigned NOT NULL default '0',
  `fil_auteur` int(10) unsigned NOT NULL default '0',
  `fil_moderateur` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fil_id`),
  KEY `forum` (`forum_id`),
  KEY `lastmsg` (`fil_last_msg_id`),
  KEY `auteur` (`fil_auteur`),
  KEY `titre` (`fil_titre`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_forum_msg`
#

CREATE TABLE `scoutwebportail_forum_msg` (
  `msg_id` int(10) unsigned NOT NULL auto_increment,
  `fil_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `msg_auteur` int(10) unsigned NOT NULL default '0',
  `msg_titre` varchar(100) NOT NULL default '',
  `msg_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `msg_statut` tinyint(1) NOT NULL default '0',
  `msg_moderateur` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`msg_id`),
  KEY `fil` (`fil_id`, `forum_id`),
  KEY `msg_date` (`msg_date`),
  KEY `auteur` (`msg_auteur`)
) TYPE=MyISAM;


# --------------------------------------------------------
#
# Structure de la table `scoutwebportail_forum_msg_txt`
#

CREATE TABLE `scoutwebportail_forum_msg_txt` (
  `msg_id` int(10) unsigned NOT NULL,
  `msg_txt` text NOT NULL,
  PRIMARY KEY  (`msg_id`)
) TYPE=MyISAM;


# --------------------------------------------------------


#
# Création des forums où seront placés les messages déjà postés sur les anciens forums
#

INSERT INTO scoutwebportail_forum_forums (forum_id, forum_titre, forum_description, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section) values ('1', 'Forum public', 'Tu retrouveras sur ce forum les messages post&eacute;s sur le forum public du portail avant la mise &agrave; jour.

Le webmaster peut d&eacute;sormais cr&eacute;er d&#039;autres forums et choisir qui peut y acc&eacute;der.', '1', '2', '0', '0');

INSERT INTO scoutwebportail_forum_forums (forum_id, forum_titre, forum_description, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section) values ('2', 'Forum des staffs', 'Tu retrouveras sur ce forum les messages post&eacute;s sur le forum des staffs avant la mise &agrave; jour. Ce forum n&#039;est visible que des animateurs inscrits sur le portail.

D&#039;autres forums du m&ecirc;me genre peuvent &ecirc;tre cr&eacute;&eacute;s. Tu peux restreindre l&#039;acc&egrave;s par section, par unit&eacute;, par niveau d&#039;acc&egrave;s, param&eacute;trer la mod&eacute;ration en vigueur, ...', '1', '0', '3', '0');

