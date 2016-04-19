<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* purgemb.php v 1.1 - suppression des inscriptions en cours
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
*	optimisation xhtml
*	externalisation de l'envoi du mail
*	prise en compte réelle du délai choisi par le webmaster
*/

include_once('connex.php');
include_once('fonc.php');

if ($user['niveau']['numniveau'] != 5)
{
	include('404.php');
}
else
{
	if (!defined('IN_SITE'))
	{
?>
<?php echo '<'.'?xml version="1.0" encoding="iso-8859-1"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>Suppression des inscriptions inachev&eacute;es</title>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="templates/default/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
	}
?>
<h1>Nettoyage de la base de donn&eacute;es</h1>
<p align="center"><a href="index.php?page=membres">Retour &agrave; la page Accueil Membres</a></p>
<?php
	if (empty($_GET['do']) and empty($_POST['do']))
	{
?>
<form action="" method="post" name="form1" id="form1" class="form_config_site">
<h2>Supprimer les inscriptions abandonn&eacute;es</h2>
<p class="petitbleu">Cette fonction &eacute;limine les inscriptions de membres inachev&eacute;es 
  de plus de <input type="text" name="nbrejours" value="15" style="width: 35px;" />
  jours.</p>
<p align="center">
  <input type="hidden" name="do" value="checkpurge" />
  <input type="submit" name="Submit" value="Voir les adresses &agrave; purger" />
</p>
<p align="center">D&eacute;lai par d&eacute;faut : 15 jours. Au minimum 1 jour
  et maximum 365.</p>
</form>
<?php
	}
	else if ($_POST['do'] == 'checkpurge' or $_POST['do'] == 'dopurge')
	{
		$sql = '';
		$res = '';
		$j = 0;
		$erreur = false;
		$nbrejours = $_POST['nbrejours'];
		if ($nbrejours < 1 or $nbrejours > 365 or !isset($nbrejours)) {$nbrejours = 15; $erreur = true;}
		$sql = "SELECT email, dateinscr FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation != '' AND ADDDATE(dateinscr, INTERVAL $nbrejours DAY) < CURRENT_DATE ORDER BY email ASC";
		$res = send_sql($db, $sql);
		$nbremails = mysql_num_rows($res);
		if ($nbremails > 0)
		{
			while ($mb = mysql_fetch_assoc($res))
			{
				$mailok = true;
				if ($j > 0)
				{
					$test = 1; 
					while ($test <= $j and $mailok)
					{
						if ($liste[$test][0] == $mb['email'])
						{
							$mailok = false;
						}
						$test++;
					}
				}
				if ($mailok)
				{
					$j++;
					$dateinscr = $mb['dateinscr'];
					$liste[$j] = array($mb['email'], $dateinscr);
				}
			}
		}
		if ($j > 0)
		{
			if ($_POST['do'] == 'checkpurge')
			{
				$x = 0;
				$listeadresses = '<ul>';
				while ($x < $j)
				{
					$x++;
			 		$listeadresses .= '<li>'.date_ymd_dmy($liste[$x][1], 'enchiffres').' - '.$liste[$x][0].'</li>';
				}
				$listeadresses .= '</ul>';
				if ($j > 1) {$pl_mb = array('s', 'ent');}
?>
<div class="action">
<p align="center"><?php echo $j.' inscription'.$pl_mb[0].' correspond'.$pl_mb[1]; ?></p>
<?php
				echo $listeadresses;
?>
<p align="center">V&eacute;rifie que les inscriptions sont bien p&eacute;rim&eacute;es 
  de <?php echo $nbrejours; ?> jours.</p>
<script language="JavaScript" type="text/JavaScript">
<!--
function confirmation()
{
	if (confirm("Purger les adresses ?"))
	{
		return true;
	}
	else
	{
		return false;
	}
}
//-->
</script>
<form action="" method="post" name="form2" id="form2" onsubmit="return confirmation()">
<p align="center">
<input type="hidden" name="nbrejours" value="<?php echo $_POST['nbrejours']; ?>" />
<input type="hidden" name="do" value="dopurge" />
<input type="submit" name="Submit" value="Purger les adresses" />
</p>
</form>
</div>
<?php
			}
			else if ($_POST['do'] == 'dopurge')
			{
				$x = 0;
				$listeadresses = '';
				$nbrejours = $_POST['nbrejours'];
				if ($nbrejours < 1 or $nbrejours > 365 or !isset($nbrejours)) $nbrejours = 15;
				$sql = "DELETE FROM ".PREFIXE_TABLES."auteurs WHERE clevalidation != '' AND ADDDATE(dateinscr, INTERVAL $nbrejours DAY) < CURRENT_DATE";
				$res = send_sql($db, $sql);
				if (ENVOI_MAILS_ACTIF)
				{
					while ($x < $j)
					{
						$x++;
						include_once('prv/emailer.php');
						$courrier = new emailer();
						$expediteur = (!empty($site['mailwebmaster'])) ? $site['mailwebmaster'] : 'noreply@noreply.be';
						$reponse = $expediteur;
						$courrier->from($expediteur);
						$courrier->to($liste[$x][0]);
						$courrier->reply_to($expediteur);
						$courrier->use_template('inscription_abandonnee', 'fr');
						$courrier->assign_vars(array(
							'INSCRIPTION_DATE' => date_ymd_dmy($liste[$x][1], 'enlettres'),
							'ADRESSE_SITE' => $site['adressesite'],
							'WEBMASTER_PSEUDO' => $site['webmaster'],
							'WEBMASTER_EMAIL' => $site['mailwebmaster']));
						$courrier->send();
						$courrier->reset();
					}
				}
				if ($j > 1) {$pl_mb = array('s', 'ent');}
?>
<div class="msg">
<p align="center"><?php echo $j.' inscription'.$pl_mb[0].' supprim&eacute;e'.$pl_mb[0].'.'; ?>
<?php echo (ENVOI_MAILS_ACTIF) ? '<br /><br />Chacun des membres concern&eacute;s a re&ccedil;u un mail pour le pr&eacute;venir que son inscription &eacute;tait annul&eacute;e.' : 'L\'envoi de mails est inactif sur le portail : Aucun mail envoy&eacute;'; ?>
</p>
</div>
<?php
			}
		}
		else
		{
?>
<div class="msg">
<p align="center" class="rmq">Aucune inscription ne doit &ecirc;tre purg&eacute;e sur un d&eacute;lai de <?php echo $nbrejours; ?> jours.</p>
<?php
			if ($erreur)
			{
?>
<p align="center">Le d&eacute;lai de 15 jours a remplacé la valeur erronée que tu avais entr&eacute;e.</p>
<?php
			}
?>
</div>
<?php
		}
	}
}
if (!defined('IN_SITE'))
{
?>
</body>
</html>
<?php
}
?>