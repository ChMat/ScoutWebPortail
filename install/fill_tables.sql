#
# Contenu de la table `scoutwebportail_articles_categorie`
#

INSERT INTO `scoutwebportail_articles_categorie` VALUES (1, 'R&eacute;cit d&#039;activit&eacute;');
INSERT INTO `scoutwebportail_articles_categorie` VALUES (2, 'Conseils pratiques');
INSERT INTO `scoutwebportail_articles_categorie` VALUES (3, 'Question ouverte');
INSERT INTO `scoutwebportail_articles_categorie` VALUES (4, 'Coup de gueule');
INSERT INTO `scoutwebportail_articles_categorie` VALUES (5, 'Expression libre');

#
# Contenu de la table `scoutwebportail_config`
#

INSERT INTO `scoutwebportail_config` VALUES ('version_portail', '1.1.1');
INSERT INTO `scoutwebportail_config` VALUES ('show_version', '1');
INSERT INTO `scoutwebportail_config` VALUES ('maj', 'INSTALL_DATE');
INSERT INTO `scoutwebportail_config` VALUES ('webmaster', 'PSEUDO_ADMIN');
INSERT INTO `scoutwebportail_config` VALUES ('mailwebmaster', 'EMAIL_ADMIN');
INSERT INTO `scoutwebportail_config` VALUES ('adressesite', 'SITE_ADRESSE');
INSERT INTO `scoutwebportail_config` VALUES ('pagesvues', '0');
INSERT INTO `scoutwebportail_config` VALUES ('edito', '<h2>Bienvenue sur le Scout Web Portail ! </h2>\r\n<p>Ce portail offre tous les outils permettant de g&eacute;rer la vie quotidienne \r\n  d&#039;un groupe scout.<br />De nombreuses fonctionnalit&eacute;s sont offertes, telles \r\n  que notamment :</p>\r\n<h3> Pour la gestion des membres :</h3>\r\n<ul>\r\n  <li>Gestion des membres multi-sections et multi-unit&eacute;s</li>\r\n  <li>Gestion des cotisations</li>\r\n  <li>Gestion des sizaines et patrouilles</li>\r\n  <li>Gestion des passages entre sections</li>\r\n  <li>Gestion des anciens</li>\r\n  <li>Gestion des listes d&#039;attente par section</li>\r\n  <li>Affichage web et exportation vers Excel des listings membres et anciens</li>\r\n  <li>Listings photos des membres</li>\r\n  <li>Outil de publipostage</li>\r\n</ul>\r\n<h3>Pour la gestion du portail :</h3>\r\n<ul>\r\n  <li>Gestion des galeries photos</li>\r\n  <li>Gestion des pages et des menus du site</li>\r\n  <li>Mailing liste synchronis&eacute;e avec la gestion des membres</li>\r\n  <li>Forum public et forum des staffs</li>\r\n  <li>Tally public</li>\r\n  <li>Syst&egrave;me de pages &agrave; acc&egrave;s restreint pour les &eacute;changes entre animateurs</li>\r\n  <li>Page de t&eacute;l&eacute;chargements</li>\r\n  <li>Syst&egrave;me de news</li>\r\n  <li>Livre d&#039;or</li>\r\n</ul>\r\n<p class="petitbleu">Tous ces outils sont enrichis de nombreuses options et fonctions, &agrave; \r\n  toi de les d&eacute;couvrir </p>\r\n<p align="center">Plus d&#039;infos sur <a href="http://www.scoutwebportail.org/">http://www.scoutwebportail.org</a>.</p>');
INSERT INTO `scoutwebportail_config` VALUES ('nbre_dernieres_news', '3');
INSERT INTO `scoutwebportail_config` VALUES ('format_edito', '1');
INSERT INTO `scoutwebportail_config` VALUES ('nom_unite', 'Scout Web Portail');
INSERT INTO `scoutwebportail_config` VALUES ('record_enligne', '0');
INSERT INTO `scoutwebportail_config` VALUES ('date_record_connectes', 'INSTALL_SQL_DATETIME');
INSERT INTO `scoutwebportail_config` VALUES ('message_membres', '<span class="rmqbleu">Bienvenue dans la zone membres !</span><br />\r\nIci, le webmaster peut indiquer un message &agrave; tous les membres du site.<br />\n');
INSERT INTO `scoutwebportail_config` VALUES ('showmsgmembres', '1');
INSERT INTO `scoutwebportail_config` VALUES ('messageanimateurs', '<p class="rmq">Quelques tuyaux pour terminer l&#039;installation du portail</p>\r\n<ul><li><a href="index.php?page=gestion_sections">Cr&eacute;er les Unit&eacute;s et Sections</a> que tu souhaites sur le portail,</li>\r\n<li><a href="index.php?page=gestion_sections_site">Activer leur espace web</a></li>\r\n<li><a href="index.php?page=gestion_menus">Cr&eacute;er les menus de chaque Section</a></li>\r\n<li><a href="index.php?page=pagesection">Cr&eacute;er les pages du site</a></li>\r\n<li><a href="index.php?page=gestion_statuts_site">Cr&eacute;er les diff&eacute;rents statuts d&#039;inscription</a> pour les futurs membres du site,</li>\r\n<li><a href="index.php?page=config_site">Param&eacute;trer le portail &agrave; ton go&ucirc;t sur la page de configuration g&eacute;n&eacute;rale</a>.</li></ul><p>Bon amusement avec le Scout Web Portail !</p>\r\n<p class="petitbleu">Ce texte peut &ecirc;tre modifi&eacute; depuis la page de configuration du portail.</p>');
INSERT INTO `scoutwebportail_config` VALUES ('showmsganimateurs', '1');
INSERT INTO `scoutwebportail_config` VALUES ('envoi_mails_actif', 'OPTION_ENVOI_MAIL');
INSERT INTO `scoutwebportail_config` VALUES ('url_rewriting_actif', 'OPTION_URL_REWRITING');
INSERT INTO `scoutwebportail_config` VALUES ('galerie_actif', '1');
INSERT INTO `scoutwebportail_config` VALUES ('forum_actif', '1');
INSERT INTO `scoutwebportail_config` VALUES ('site_actif', '1');
INSERT INTO `scoutwebportail_config` VALUES ('record_connectes', '0');
INSERT INTO `scoutwebportail_config` VALUES ('date_record_enligne', 'INSTALL_SQL_DATETIME');
INSERT INTO `scoutwebportail_config` VALUES ('site_ville', 'SITE_VILLE');
INSERT INTO `scoutwebportail_config` VALUES ('site_code_postal', 'SITE_CODE_POSTAL');
INSERT INTO `scoutwebportail_config` VALUES ('titre_site', 'Scout Web Portail');
INSERT INTO `scoutwebportail_config` VALUES ('gestion_membres_actif', '1');
INSERT INTO `scoutwebportail_config` VALUES ('balises_meta', '<meta name="keywords" content="" />\r\n<meta name="description" content="" />\r\n<meta name="Author" content="Christian Mattart" />\r\n<meta name="Copyright" content="ChM@ - 2004" />\r\n');
INSERT INTO `scoutwebportail_config` VALUES ('log_visites', '0');
INSERT INTO `scoutwebportail_config` VALUES ('show_enligne', '0');
INSERT INTO `scoutwebportail_config` VALUES ('avert_onclick_lien_externe', '1');
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
INSERT INTO `scoutwebportail_config` VALUES ('menu_standard', '');
INSERT INTO `scoutwebportail_config` VALUES ('droits_anim_valide_statut', '1');
INSERT INTO `scoutwebportail_config` VALUES ('forum_nbfils_par_page', '15');
INSERT INTO `scoutwebportail_config` VALUES ('forum_nbmsg_par_page', '15');

#
# Contenu de la table `scoutwebportail_fichiers_cat`
#

INSERT INTO `scoutwebportail_fichiers_cat` VALUES ('1', 'Tous les fichiers', 'Les fichiers peuvent &ecirc;tre tri&eacute;s par rubriques. Tu peux en cr&eacute;er de nouvelles et y d&eacute;placer les fichiers.');

#
# Contenu de la table `scoutwebportail_pagessections`
#

INSERT INTO scoutwebportail_pagessections VALUES (1, 'moderationforum', 0, 2, 'html', 1, 'INSTALL_SQL_DATETIME', 'Infos compl&eacute;mentaires au sujet du forum', '<h2>Principes g&eacute;n&eacute;raux</h2>\r\n<p>Le forum est un lieu d&#039;expression ouvert &agrave; tous. Si tu as des questions \r\n  &agrave; poser, des propositions &agrave; &eacute;mettre, le forum est l&#039;endroit \r\n  idéal pour t&#039;exprimer. <br />\r\n  Afin de pouvoir ajouter ton grain de sel dans nos discussions, tu dois <a href="index.php?page=login">te \r\n  connecter</a> et pour cela <a href="index.php?page=inscr">devenir membre \r\n  du site</a>.</p>\r\n<p> </p><h2>R&egrave;gles de vie</h2>\r\n<p>Comme partout, tu peux faire beaucoup de choses en restant dans les limites \r\n  du tol&eacute;rable. Aucune politique pr&eacute;cise n&#039;a &eacute;t&eacute; d&eacute;finie \r\n  pour ce forum, cependant nous comptons sur ta bonne volont&eacute;. L&#039;esprit \r\n  scout se doit de r&eacute;gner dans ce forum comme partout ailleurs sur le site.</p>\r\n<h2>Des probl&egrave;mes ?</h2>\r\n<p>Chaque membre qui cr&eacute;e une discussion est responsable de sa gestion \r\n  ainsi que tous les membres inscrits comme animateurs sur le site. Si l&#039;un d&#039;eux estime qu&#039;un \r\n  membre d&eacute;rape il peut supprimer certaines interventions de la discussion \r\n  ou m&ecirc;me retirer la discussion du site.</p>\r\n<p>la mod&eacute;ration (suppression d&#039;un message) doit intervenir le plus rarement \r\n  possible et ce, dans des cas extr&ecirc;mes uniquement. Merci de faire ça \r\n  intelligemment, la libert&eacute; d&#039;expression, &ccedil;a existe aussi <img src="img/smileys/003.gif" width="15" height="15" align="middle"> \r\n</p>\r\n<p> En cas de doute ou de contestation, n&#039;h&eacute;site pas &agrave; contacter \r\n  un animateur.</p>\r\n<p class="petit"><strong>Rmq</strong> : "les paroles s&#039;envolent, les &eacute;crits \r\n  restent." Autrement dit, tout abus de cette fonction sera sanctionn&eacute;... \r\n</p>\r\n', 'INSTALL_SQL_DATE', 1);

INSERT INTO scoutwebportail_pagessections VALUES (2, 'avertissement', 0, 2, 'html', 1, 'INSTALL_SQL_DATETIME', 'Avertissement au sujet des liens, photos et droits d&#039;auteur', '<p>Les liens repris sur ce site ont fait l&#039;objet de v&eacute;rification \r\n  avant leur mise en ligne. Nous d&eacute;clinons n&eacute;anmoins tout responsabilit&eacute; \r\n  sur le contenu et l&#039;information propos&eacute;s par ces sites. N&#039;ayant pu demander \r\n  l&#039;autorisation &agrave; chacun des sites pour pointer un lien vers leur site, \r\n  nous leur demandons en cas de contestation de nous le faire savoir et nous enl&egrave;verons \r\n  celui-ci imm&eacute;diatement. (contacter le webmaster)</p>\r\n  <p>Il n&#039;est pas permis de reproduire ou copier tout ou partie de \r\n    ce site sans l&#039;accord du webmaster ou de l&#039;auteur de l&#039;une des parties de \r\n    ce site.</p>\r\n  \r\n<p>Les \r\n  propos tenus sur ce site via le forum, le livre d&#039;or, les commentaires de photos, \r\n  le tally ou tout autre moyen sont de la responsabilit&eacute; de leur auteur \r\n  ou de leurs ayant-droits. Les informations de connexion des auteurs sont conserv&eacute;es \r\n  sur le site et en cas de litige, pourront &ecirc;tre utilis&eacute;es par les \r\n  autorit&eacute;s comp&eacute;tentes pour retrouver les auteurs. Tout propos \r\n  contraire &agrave; la netiquette sera retir&eacute; du site d&egrave;s que sa \r\n  pr&eacute;sence sera constat&eacute;e. Les visiteurs de ce site sont tenus de \r\n  signaler au webmaster toute infraction &agrave; la netiquette ou au code de \r\n  conduite du pr&eacute;sent site.</p>\r\n  <p>Les illustrations sur ce site restent la propri&eacute;t&eacute; de leur \r\n    auteur. Leur utilisation est conditionn&eacute;e par leur auteur.</p>\r\n  \r\n<p>Les photos reprises sur ce site font l&#039;objet d&#039;un choix minutieux \r\n  afin de respecter le droit &agrave; l&#039;intimit&eacute; et &agrave; ne pas nuire \r\n  aux personnes concern&eacute;es. Si toutefois vous estimiez que une ou des photos \r\n  vous mettant en sc&egrave;ne (ou un enfant dont vous avez la charge) doit ou \r\n  doivent &ecirc;tre retir&eacute;es, il vous suffit d&#039;en faire la demande par \r\n  e-mail (voir lien en bas de page) au webmaster de ce site.</p>\r\n  <p>Votre demande sera trait&eacute;e dans les meilleurs d&eacute;lais, \r\n    moyennant la disponibilit&eacute; des personnes habilit&eacute;es.</p>\r\n  \r\n<p>NB : L&#039;avertissement ci-dessus, extrait du <a href="http://www.nmregion.be.tf" target=\\"_blank\\">site \r\n  de la r&eacute;gion Namur-Meuse</a>, a &eacute;t&eacute; adapt&eacute; avec \r\n  l&#039;autorisation de son auteur.</p>\r\n<p>NB2 : Consulter utilement la <a href="index.php?page=moderationforum">note \r\n  concernant l&#039;utilisation du forum</a>. </p>\r\n<p align="center">Merci et bon surf.</p>', 'INSTALL_SQL_DATE', 1);



#
# Contenu de la table `scoutwebportail_site_niveaux`
#

INSERT INTO `scoutwebportail_site_niveaux` VALUES (1, 1, 0, 'Visiteur', 1);
INSERT INTO `scoutwebportail_site_niveaux` VALUES (2, 5, 0, 'Webmaster', 0);

#
# Contenu de la table `scoutwebportail_unite_fonctions`
#

INSERT INTO `scoutwebportail_unite_fonctions` VALUES (1, 'Anim&eacute;', 'MB');
INSERT INTO `scoutwebportail_unite_fonctions` VALUES (2, 'Animateur', 'A');
INSERT INTO `scoutwebportail_unite_fonctions` VALUES (3, 'Animateur responsable', 'AN');
INSERT INTO `scoutwebportail_unite_fonctions` VALUES (4, 'Assistant d&#039;Unit&eacute;', 'AS');
INSERT INTO `scoutwebportail_unite_fonctions` VALUES (5, 'Animateur d&#039;Unit&eacute;', 'AN');

#
# Création des forums par défaut
#

INSERT INTO scoutwebportail_forum_forums (forum_id, forum_titre, forum_description, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section) values ('1', 'Forum public', 'Ce forum est accessible &agrave; tous les visiteurs du site. Tous les membres peuvent y poster et prendre part aux discussions.

Le webmaster peut cr&eacute;er d&#039;autres forums et choisir qui peut y acc&eacute;der.', '1', '2', '0', '0');

INSERT INTO scoutwebportail_forum_forums (forum_id, forum_titre, forum_description, forum_statut, forum_moderation, forum_acces_niv, forum_acces_section) values ('2', 'Forum des staffs', 'Ce forum n&#039;est visible que des animateurs inscrits sur le portail.

D&#039;autres forums du m&ecirc;me genre peuvent &ecirc;tre cr&eacute;&eacute;s. Tu peux restreindre l&#039;acc&egrave;s par section, par unit&eacute;, par niveau d&#039;acc&egrave;s, param&eacute;trer la mod&eacute;ration en vigueur, ...', '1', '0', '3', '0');

