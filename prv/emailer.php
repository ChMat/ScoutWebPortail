<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* emailer.php v 1.1 - Classe pour l'envoi de mails
* Classe basée sur celle fournie dans phpBB et adaptée par ChMat
*
* Copyright (C) 2001 The phpBB Group email : support@phpbb.com
* Copyright (C) 2005 ChMat
* 
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
*	bug 24/11 - quand $this->vars n'est pas un array, suppression de l'erreur
*/


//
// This does exactly what preg_quote() does in PHP 4-ish
// If you just need the 1-parameter preg_quote call, then don't bother using this.
//
function phpbb_preg_quote($str, $delimiter)
{
	$text = preg_quote($str);
	$text = str_replace($delimiter, '\\' . $delimiter, $text);
	
	return $text;
}

class emailer
{
	var $msg, $subject, $extra_headers;
	var $addresses, $reply_to, $from;
	var $use_smtp;
	var $content_type = 'text/plain';

	var $tpl_msg = array();

	function emailer()
	{
		$this->reset();
		$this->reply_to = $this->from = '';
	}

	function reset()
	{ // Réinitialise toutes les données
		$this->addresses = array();
		$this->vars = $this->msg = $this->extra_headers = '';
	}

	function to($address)
	{ // Définit le destinataire du mail
		$this->addresses['to'][] = trim($address);
	}

	function cc($address)
	{ // Définit un destinataire en copie
		$this->addresses['cc'][] = trim($address);
	}

	function bcc($address)
	{ // Définit un destinataire en copie cachée
		$this->addresses['bcc'][] = trim($address);
	}

	function reply_to($address)
	{ // Définit l'adresse de réponse
		$this->reply_to = trim($address);
	}

	function from($address)
	{ // Définit l'adresse de l'expéditeur
		$this->from = trim($address);
	}

	function set_subject($subject = '')
	{ // Définit le sujet du mail
		$this->subject = trim(preg_replace('#[\n\r]+#s', '', $subject));
	}

	function extra_headers($headers)
	{ // Définit les headers supplémentaires
		$this->extra_headers .= trim($headers) . "\n";
	}

	function set_content_type($type)
	{ // Définit le format du mail
		$this->content_type = (!empty($type)) ? $type : 'text/plain';
	}

	function use_template($template_file, $template_lang = '')
	{ // Initialise un template de mail
		if (trim($template_file) == '')
		{
			echo 'Aucun template renseign&eacute; ('.__FILE__.' ligne '.__LINE__.')';
			exit;
		}

		if (trim($template_lang) == '')
		{
			$template_lang = 'fr';
		}

		if (empty($this->tpl_msg[$template_lang . $template_file]))
		{ // lang/fr/mails/
			$tpl_file = 'lang/'.$template_lang.'/mails/'.$template_file.'.txt';

			if (!@file_exists($tpl_file))
			{
				$tpl_file = 'lang/fr/mails/'.$template_file.'.txt';

				if (!@file_exists($tpl_file))
				{
					$tpl_file = '../lang/fr/mails/'.$template_file.'.txt';

					if (!@file_exists($tpl_file))
					{
						$tpl_file = '../lang/fr/mails/'.$template_file.'.txt';
						echo 'Le template '.$tpl_file.' n\'existe pas ('.__FILE__.' ligne '.__LINE__.')';
						exit;
					}
				}
			}

			if (!($fd = @fopen($tpl_file, 'r')))
			{
				echo 'Impossible d\'ouvrir le template '.$tpl_file.' ('.__FILE__.' ligne '.__LINE__.')';
				exit;
			}

			$this->tpl_msg[$template_lang.$template_file] = fread($fd, filesize($tpl_file));
			fclose($fd);
		}

		$this->msg = $this->tpl_msg[$template_lang.$template_file];

		return true;
	}

	function assign_vars($vars)
	{ // Définit les variables du mail
		$this->vars = (empty($this->vars)) ? $vars : $this->vars . $vars;
	}

	function send()
	{ // Envoie le mail aux destinataires paramétrés auparavant
		global $site;

    	// On retire les guillemets et apostrophes pour éviter les erreurs
		$this->msg = str_replace ("'", "\'", $this->msg);
		$this->msg = preg_replace('#\{([a-z0-9\-_]*?)\}#is', "' . $\\1 . '", $this->msg);

		// On définit les variables
		@reset ($this->vars);
		while (list($key, $val) = @each($this->vars)) 
		{
			$$key = $val;
		}

		eval("\$this->msg = '$this->msg';");

		// On vide les variables
		@reset ($this->vars);
		while (list($key, $val) = @each($this->vars)) 
		{
			unset($$key);
		}

		// Récupération du sujet du mail s'il est présent dans le template
		$drop_header = '';
		$match = array();
		if (preg_match('#^(Subject:(.*?))$#m', $this->msg, $match))
		{
			$this->subject = (trim($match[2]) != '') ? trim($match[2]) : (($this->subject != '') ? $this->subject : 'Pas de sujet');
			$drop_header .= '[\r\n]*?' . phpbb_preg_quote($match[1], '#');
		}
		else
		{
			$this->subject = (($this->subject != '') ? $this->subject : 'Pas de sujet');
		}

		if (preg_match('#^(Charset:(.*?))$#m', $this->msg, $match))
		{
			$this->encoding = (trim($match[2]) != '') ? trim($match[2]) : 'iso-8859-1';
			$drop_header .= '[\r\n]*?' . phpbb_preg_quote($match[1], '#');
		}
		else
		{
			$this->encoding = 'iso-8859-1';
		}

		if ($drop_header != '')
		{
			$this->msg = trim(preg_replace('#' . $drop_header . '#s', '', $this->msg));
		}

		//$to = $this->addresses['to'];
		// On autorise l'utilisation de plusieurs destinataires
		$to = (count($this->addresses['to'])) ? implode(', ', $this->addresses['to']) : '';
		
		$cc = (count($this->addresses['cc'])) ? implode(', ', $this->addresses['cc']) : '';
		$bcc = (count($this->addresses['bcc'])) ? implode(', ', $this->addresses['bcc']) : '';

		// Construction de l'en-tête du message
		$this->extra_headers = (($this->reply_to != '') ? "Reply-to: $this->reply_to\n" : '') . (($this->from != '') ? "From: $this->from\n" : "From: " . $site['mailwebmaster'] . "\n") . "Return-Path: " . $site['mailwebmaster'] . "\nMessage-ID: <" . md5(uniqid(time())) . "@" . $_SERVER['SERVER_NAME'] . ">\nMIME-Version: 1.0\nContent-type: " . $this->content_type . "; charset=" . $this->encoding . "\nContent-transfer-encoding: 8bit\nDate: " . date('r', time()) . "\nX-Priority: 3\nX-MSMail-Priority: Normal\nX-Mailer: PHP\nX-MimeOLE: Produced By Scout Web Portail ".$site['version_portail']."\n" . $this->extra_headers . (($cc != '') ? "Cc: $cc\n" : '')  . (($bcc != '') ? "Bcc: $bcc\n" : ''); 

		// Envoi du message
		$empty_to_header = ($to == '') ? true : false;
		$to = ($to == '') ? 'Undisclosed-recipients:;' : $to;

		$result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
		
		if (!$result && $empty_to_header)
		{
			$to = ' ';
			$result = @mail($to, $this->subject, preg_replace("#(?<!\r)\n#s", "\n", $this->msg), $this->extra_headers);
		}

		// Did it work?
		return $result;
	}

	// Encodes the given string for proper display for this encoding ... nabbed 
	// from php.net and modified. There is an alternative encoding method which 
	// may produce lesd output but it's questionable as to its worth in this 
	// scenario IMO
	function encode($str)
	{
		if ($this->encoding == '')
		{
			return $str;
		}

		// define start delimimter, end delimiter and spacer
		$end = "?=";
		$start = "=?$this->encoding?B?";
		$spacer = "$end\r\n $start";

		// determine length of encoded text within chunks and ensure length is even
		$length = 75 - strlen($start) - strlen($end);
		$length = floor($length / 2) * 2;

		// encode the string and split it into chunks with spacers after each chunk
		$str = chunk_split(base64_encode($str), $length, $spacer);

		// remove trailing spacer and add start and end delimiters
		$str = preg_replace('#' . phpbb_preg_quote($spacer, '#') . '$#', '', $str);

		return $start . $str . $end;
	}

	//
	// Attach files via MIME.
	//
	function attachFile($filename, $mimetype = "application/octet-stream", $szFromAddress, $szFilenameToDisplay)
	{
		global $lang;
		$mime_boundary = "--==================_846811060==_";

		$this->msg = '--' . $mime_boundary . "\nContent-Type: text/plain;\n\tcharset=\"" . $lang['ENCODING'] . "\"\n\n" . $this->msg;

		if ($mime_filename)
		{
			$filename = $mime_filename;
			$encoded = $this->encode_file($filename);
		}

		$fd = fopen($filename, "r");
		$contents = fread($fd, filesize($filename));

		$this->mimeOut = "--" . $mime_boundary . "\n";
		$this->mimeOut .= "Content-Type: " . $mimetype . ";\n\tname=\"$szFilenameToDisplay\"\n";
		$this->mimeOut .= "Content-Transfer-Encoding: quoted-printable\n";
		$this->mimeOut .= "Content-Disposition: attachment;\n\tfilename=\"$szFilenameToDisplay\"\n\n";

		if ( $mimetype == "message/rfc822" )
		{
			$this->mimeOut .= "From: ".$szFromAddress."\n";
			$this->mimeOut .= "To: ".$this->emailAddress."\n";
			$this->mimeOut .= "Date: ".date("D, d M Y H:i:s") . " UT\n";
			$this->mimeOut .= "Reply-To:".$szFromAddress."\n";
			$this->mimeOut .= "Subject: ".$this->mailSubject."\n";
			$this->mimeOut .= "X-Mailer: PHP/".phpversion()."\n";
			$this->mimeOut .= "MIME-Version: 1.0\n";
		}

		$this->mimeOut .= $contents."\n";
		$this->mimeOut .= "--" . $mime_boundary . "--" . "\n";

		return $out;
		// added -- to notify email client attachment is done
	}

	function getMimeHeaders($filename, $mime_filename="")
	{
		$mime_boundary = "--==================_846811060==_";

		if ($mime_filename)
		{
			$filename = $mime_filename;
		}

		$out = "MIME-Version: 1.0\n";
		$out .= "Content-Type: multipart/mixed;\n\tboundary=\"$mime_boundary\"\n\n";
		$out .= "This message is in MIME format. Since your mail reader does not understand\n";
		$out .= "this format, some or all of this message may not be legible.";

		return $out;
	}

	//
   // Split string by RFC 2045 semantics (76 chars per line, end with \r\n).
	//
	function myChunkSplit($str)
	{
		$stmp = $str;
		$len = strlen($stmp);
		$out = "";

		while ($len > 0)
		{
			if ($len >= 76)
			{
				$out .= substr($stmp, 0, 76) . "\r\n";
				$stmp = substr($stmp, 76);
				$len = $len - 76;
			}
			else
			{
				$out .= $stmp . "\r\n";
				$stmp = "";
				$len = 0;
			}
		}
		return $out;
	}

	//
   // Split the specified file up into a string and return it
	//
	function encode_file($sourcefile)
	{
		if (is_readable($sourcefile))
		{
			$fd = fopen($sourcefile, "r");
			$contents = fread($fd, filesize($sourcefile));
	      $encoded = $this->myChunkSplit(base64_encode($contents));
	      fclose($fd);
		}

		return $encoded;
	}

} // class emailer

?>