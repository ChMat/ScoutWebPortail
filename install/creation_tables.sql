#
# Structure de la table `scoutwebportail_albums`
#

CREATE TABLE `scoutwebportail_albums` (
  `numphoto` int(10) unsigned NOT NULL auto_increment,
  `nomfichier` varchar(255) NOT NULL default '',
  `numalbum` smallint(5) unsigned NOT NULL default '0',
  `posphoto` smallint(5) unsigned NOT NULL default '0',
  `nbcomment` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numphoto`),
  KEY `posphoto` (`posphoto`),
  KEY `numalbum` (`numalbum`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_articles`
#

CREATE TABLE `scoutwebportail_articles` (
  `numarticle` int(10) unsigned NOT NULL auto_increment,
  `article_section` int(10) unsigned NOT NULL default '0',
  `article_categorie` int(10) unsigned NOT NULL default '0',
  `article_auteur` int(10) unsigned NOT NULL default '0',
  `article_titre` varchar(100) NOT NULL default '',
  `article_texte` text NOT NULL,
  `article_datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_lu` int(10) unsigned NOT NULL default '0',
  `article_banni` char(1) NOT NULL default '0',
  `article_modifby` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numarticle`),
  KEY `datecreation` (`article_datecreation`),
  KEY `auteur` (`article_auteur`),
  KEY `titre` (`article_titre`),
  KEY `categorie` (`article_categorie`),
  KEY `section` (`article_section`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_articles_categorie`
#

CREATE TABLE `scoutwebportail_articles_categorie` (
  `numcategorie` tinyint(3) unsigned NOT NULL auto_increment,
  `nomcategorie` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`numcategorie`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_auteurs`
#

CREATE TABLE `scoutwebportail_auteurs` (
  `num` int(10) NOT NULL auto_increment,
  `pw` varchar(35) NOT NULL default '',
  `pseudo` varchar(32) NOT NULL default '',
  `prenom` varchar(100) NOT NULL default '',
  `nom` varchar(100) NOT NULL default '',
  `totem_scout` varchar(100) NOT NULL default '',
  `quali_scout` varchar(100) NOT NULL default '',
  `totem_jungle` varchar(100) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `niveau` tinyint(1) NOT NULL default '0',
  `nivdemande` tinyint(1) NOT NULL default '0',
  `assistantwebmaster` tinyint(1) NOT NULL default '0',
  `numsection` tinyint(3) unsigned NOT NULL default '0',
  `dateinscr` datetime NOT NULL default '0000-00-00 00:00:00',
  `banni` tinyint(1) NOT NULL default '0',
  `clevalidation` varchar(5) NOT NULL default '',
  `autorise` varchar(32) NOT NULL default '',
  `ipinscription` varchar(15) NOT NULL default '',
  `nbconnex` mediumint(8) unsigned NOT NULL default '0',
  `affaide` tinyint(1) NOT NULL default '1',
  `pagesvues` int(10) unsigned NOT NULL default '0',
  `lastconnex` datetime NOT NULL default '0000-00-00 00:00:00',
  `siteweb` varchar(255) NOT NULL default '',
  `profilmembre` text NOT NULL,
  `avatar` varchar(100) NOT NULL default '',
  `loisirs` varchar(255) NOT NULL default '',
  `majprofildone` tinyint(1) NOT NULL default '0',
  `majprofildate` date NOT NULL default '0000-00-00',
  `newpw` tinyint(1) NOT NULL default '0',
  `conditions_acceptees` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`num`),
  KEY `pw` (`pw`,`pseudo`,`niveau`,`nivdemande`,`numsection`,`clevalidation`)
) TYPE=MyISAM;

#
# Structure de la table `scoutwebportail_commentaires`
#

CREATE TABLE `scoutwebportail_commentaires` (
  `numcommentaire` int(10) unsigned NOT NULL auto_increment,
  `album` smallint(5) unsigned NOT NULL default '0',
  `photo` smallint(5) unsigned NOT NULL default '0',
  `auteur` int(10) unsigned NOT NULL default '0',
  `commentaire` text NOT NULL,
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `commentairebanni` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`numcommentaire`),
  KEY `datecreation` (`datecreation`),
  KEY `num` (`numcommentaire`,`auteur`,`datecreation`,`commentairebanni`),
  KEY `album` (`album`,`photo`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_config`
#

CREATE TABLE `scoutwebportail_config` (
  `champ` varchar(50) NOT NULL default '',
  `valeur` text NOT NULL,
  UNIQUE KEY `champ` (`champ`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_connectes`
#

CREATE TABLE `scoutwebportail_connectes` (
  `id` varchar(50) NOT NULL default '',
  `user` int(10) unsigned NOT NULL default '0',
  `connectea` timestamp(14) NOT NULL,
  `ip` varchar(15) NOT NULL default '',
  `pc_user` varchar(35) NOT NULL default '',
  `cookie_login` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_fichiers`
#

CREATE TABLE `scoutwebportail_fichiers` (
  `numfichier` int(10) unsigned NOT NULL auto_increment,
  `cat_id` mediumint(8) unsigned NOT NULL default '0',
  `cledownload` varchar(20) NOT NULL default '',
  `dateupload` datetime NOT NULL default '0000-00-00 00:00:00',
  `nomoriginal` varchar(255) NOT NULL default '',
  `nomserveur` varchar(12) NOT NULL default '',
  `type_fichier` varchar(100) NOT NULL default '',
  `titre_fichier` varchar(100) NOT NULL default '',
  `description_fichier` text NOT NULL,
  `public` tinyint(1) NOT NULL default '0',
  `vedette` tinyint(3) unsigned NOT NULL default '0',
  `lu` int(10) unsigned NOT NULL default '0',
  `file_auteur` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numfichier`),
  UNIQUE KEY `cledownload` (`cledownload`),
  KEY `cat_id` (`cat_id`),
  KEY `file_auteur` (`file_auteur`),
  KEY `public` (`public`),
  KEY `vedette` (`vedette`)
) TYPE=MyISAM;


# --------------------------------------------------------

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
# Structure de la table `scoutwebportail_galerie`
#

CREATE TABLE `scoutwebportail_galerie` (
  `numgalerie` int(10) unsigned NOT NULL auto_increment,
  `titre` varchar(255) NOT NULL default '',
  `nbrephotos` smallint(5) unsigned NOT NULL default '0',
  `dossiergd` varchar(255) NOT NULL default '',
  `dossierpt` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `description2` text NOT NULL,
  `photoaccueil` varchar(255) NOT NULL default '',
  `statutgalerie` tinyint(1) NOT NULL default '1',
  `galerie_section` tinyint(3) unsigned NOT NULL default '0',
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `dateactivite` date NOT NULL default '0000-00-00',
  `auteurphotos` varchar(100) NOT NULL default '',
  `mode_creation` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`numgalerie`),
  KEY `galerie_section` (`galerie_section`),
  KEY `dateactivite` (`dateactivite`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_livreor`
#

CREATE TABLE `scoutwebportail_livreor` (
  `num` int(10) unsigned NOT NULL auto_increment,
  `auteur` varchar(100) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip` varchar(15) NOT NULL default '000.000.000.000',
  `banni` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`num`),
  KEY `datecreation` (`datecreation`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_log_actions`
#

CREATE TABLE `scoutwebportail_log_actions` (
  `numuser` int(10) unsigned NOT NULL default '0',
  `page` varchar(100) NOT NULL default '',
  `h_action` datetime NOT NULL default '0000-00-00 00:00:00',
  `description_action` text NOT NULL,
  KEY `numuser` (`numuser`),
  KEY `page` (`page`, `h_action`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_log_actions_visiteur`
#

CREATE TABLE `scoutwebportail_log_actions_visiteur` (
  `ip` varchar(15) NOT NULL default '',
  `url` text NOT NULL,
  `pc` varchar(255) NOT NULL default '',
  `time` timestamp(14) NOT NULL,
  KEY `time` (`time`),
  KEY `ip` (`ip`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_log_visites`
#

CREATE TABLE `scoutwebportail_log_visites` (
  `numuser` int(10) unsigned NOT NULL default '0',
  `pseudo_stocke` varchar(32) NOT NULL default '',
  `visiteur` varchar(35) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `h_dbt` datetime default NULL,
  `h_fin` datetime default NULL,
  `nbre_clics` int(10) unsigned NOT NULL default '0',
  KEY `numuser` (`numuser`),
  KEY `pseudo_stocke` (`pseudo_stocke`),
  KEY `heure` (`h_dbt`, `h_fin`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_mb_adresses`
#

CREATE TABLE scoutwebportail_mb_adresses (
  numfamille int(10) unsigned NOT NULL auto_increment,
  nom varchar(100) NOT NULL default '',
  nom_son varchar(25) NOT NULL default '',
  rue varchar(100) NOT NULL default '',
  numero varchar(10) NOT NULL default '',
  bte varchar(10) NOT NULL default '',
  cp varchar(5) NOT NULL default '',
  ville varchar(100) NOT NULL default '',
  tel1 varchar(30) NOT NULL default '',
  tel2 varchar(30) NOT NULL default '',
  tel3 varchar(30) NOT NULL default '',
  tel4 varchar(30) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  email2 varchar(255) NOT NULL default '',
  rmq text NOT NULL,
  nom_pere varchar(255) NOT NULL default '',
  nom_mere varchar(255) NOT NULL default '',
  profession_pere varchar(255) NOT NULL default '',
  profession_mere varchar(255) NOT NULL default '',
  ad_createur int(10) unsigned NOT NULL default '0',
  ad_datecreation datetime NOT NULL default '0000-00-00 00:00:00',
  ad_lastmodifby int(10) unsigned NOT NULL default '0',
  ad_lastmodif datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (numfamille),
  KEY nom (nom),
  KEY nom_son (nom_son)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_mb_membres`
#

CREATE TABLE scoutwebportail_mb_membres (
  nummb int(10) unsigned NOT NULL auto_increment,
  nom_mb varchar(100) NOT NULL default '',
  prenom varchar(100) NOT NULL default '',
  nom_mb_son varchar(25) NOT NULL default '',
  prenom_son varchar(25) NOT NULL default '',
  famille int(10) unsigned NOT NULL default '0',
  famille2 int(10) unsigned NOT NULL default '0',
  ddn date NOT NULL default '0000-00-00',
  dateinscr date NOT NULL default '0000-00-00',
  section tinyint(3) unsigned NOT NULL default '0',
  totem varchar(100) NOT NULL default '',
  quali varchar(100) NOT NULL default '',
  totem_jungle varchar(100) NOT NULL default '',
  photo varchar(255) NOT NULL default '',
  actif tinyint(1) NOT NULL default '0',
  cotisation tinyint(1) NOT NULL default '0',
  rmq_mb text NOT NULL,
  email_mb varchar(255) NOT NULL default '',
  siteweb varchar(255) NOT NULL default '',
  sexe char(1) NOT NULL default '',
  fonction tinyint(1) NOT NULL default '0',
  siz_pat smallint(5) unsigned NOT NULL default '0',
  cp_sizenier tinyint(1) NOT NULL default '0',
  telperso varchar(30) NOT NULL default '',
  mb_createur int(10) unsigned NOT NULL default '0',
  mb_datecreation datetime NOT NULL default '0000-00-00 00:00:00',
  mb_lastmodifby int(10) unsigned NOT NULL default '0',
  mb_lastmodif datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (nummb),
  KEY famille (famille,famille2),
  KEY nom_son (nom_mb_son,prenom_son),
  KEY section (section,fonction,siz_pat)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_news`
#

CREATE TABLE `scoutwebportail_news` (
  `id_news` int(10) unsigned NOT NULL auto_increment,
  `titre_news` varchar(100) NOT NULL default '',
  `texte_news` text NOT NULL,
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `news_bannie` tinyint(1) NOT NULL default '0',
  `auteur_news` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_news`),
  KEY `datecreation` (`datecreation`),
  KEY `auteur_news` (`auteur_news`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_pagesrestreintes`
#

CREATE TABLE `scoutwebportail_pagesrestreintes` (
  `numpage` int(10) unsigned NOT NULL auto_increment,
  `auteur` int(11) unsigned NOT NULL default '0',
  `titre` varchar(100) NOT NULL default '',
  `article` text NOT NULL,
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `lu` int(10) unsigned NOT NULL default '0',
  `pagebannie` tinyint(1) NOT NULL default '0',
  `commentaires_forum` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numpage`),
  KEY `auteur` (`auteur`),
  KEY `titre` (`titre`),
  KEY `datecreation` (`datecreation`),
  KEY `commentaires_forum` (`commentaires_forum`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_pagessections`
#

CREATE TABLE `scoutwebportail_pagessections` (
  `numpage` int(10) unsigned NOT NULL auto_increment,
  `page` varchar(20) NOT NULL default '',
  `specifiquesection` tinyint(3) unsigned NOT NULL default '0',
  `statut` tinyint(1) NOT NULL default '0',
  `format` varchar(6) NOT NULL default 'html',
  `auteur` int(10) NOT NULL default '0',
  `datecreation` datetime NOT NULL default '0000-00-00 00:00:00',
  `titre` varchar(100) NOT NULL default '',
  `contenupage` text NOT NULL,
  `lastmodif` date NOT NULL default '0000-00-00',
  `lastmodifby` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numpage`),
  UNIQUE KEY `page` (`page`),
  KEY `auteur` (`auteur`),
  KEY `titre` (`titre`),
  KEY `specifiquesection` (`specifiquesection`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_site_mailing_liste`
#

CREATE TABLE `scoutwebportail_site_mailing_liste` (
  `num` int(10) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `date_ajout` date NOT NULL default '0000-00-00',
  `envoi_ok` tinyint(1) NOT NULL default '1',
  `ip_inscr` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`num`),
  KEY `email` (`email`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_site_menus`
#

CREATE TABLE `scoutwebportail_site_menus` (
  `id_menu` int(10) unsigned NOT NULL auto_increment,
  `section_menu` int(10) unsigned NOT NULL default '0',
  `position_menu` int(10) unsigned NOT NULL default '0',
  `description_menu` varchar(255) NOT NULL default '',
  `texte_menu` varchar(255) NOT NULL default '',
  `lien_menu` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_menu`),
  KEY `position_menu` (`section_menu`, `position_menu`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_site_niveaux`
#

CREATE TABLE `scoutwebportail_site_niveaux` (
  `idniveau` tinyint(3) unsigned NOT NULL auto_increment,
  `numniveau` tinyint(1) NOT NULL default '0',
  `section_niveau` tinyint(3) unsigned NOT NULL default '0',
  `nomniveau` varchar(100) NOT NULL default '',
  `show_at_inscr` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`idniveau`),
  KEY `numniveau` (`numniveau`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_unite_fonctions`
#

CREATE TABLE `scoutwebportail_unite_fonctions` (
  `numfonction` tinyint(1) NOT NULL auto_increment,
  `nomfonction` varchar(100) NOT NULL default '',
  `sigle_fonction` char(2) NOT NULL default '',
  PRIMARY KEY  (`numfonction`)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_unite_sections`
#

CREATE TABLE `scoutwebportail_unite_sections` (
  `numsection` tinyint(3) unsigned NOT NULL auto_increment,
  `unite` tinyint(3) unsigned NOT NULL default '0',
  `anciens` tinyint(1) NOT NULL default '0',
  `nomsection` varchar(255) NOT NULL default '',
  `sexe` char(1) NOT NULL default '',
  `trancheage` varchar(100) NOT NULL default '',
  `appellation` varchar(100) NOT NULL default '',
  `nomsectionpt` varchar(50) NOT NULL default '',
  `sizaines` tinyint(1) NOT NULL default '0',
  `sigle_section` char(2) NOT NULL default '',
  `site_section` char(1) NOT NULL default '',
  `position_section` tinyint(3) unsigned NOT NULL default '0',
  `code_unite` varchar(10) NOT NULL default '',
  `federation` varchar(100) NOT NULL default '',
  `ville_unite` varchar(100) NOT NULL default '',
  `aff_totem_meute` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`numsection`),
  KEY `unite` (`unite`),
  KEY `position_section` (`numsection`, `unite`, `position_section`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `scoutwebportail_unite_sizaines`
#

CREATE TABLE `scoutwebportail_unite_sizaines` (
  `numsizaine` smallint(5) unsigned NOT NULL auto_increment,
  `nomsizaine` varchar(100) NOT NULL default '',
  `section_sizpat` tinyint(3) unsigned NOT NULL default '0',
  `cri` text NOT NULL,
  PRIMARY KEY  (`numsizaine`),
  KEY `section_sizpat` (`section_sizpat`)
) TYPE=MyISAM;
