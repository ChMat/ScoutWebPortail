<?php
/*
* 12/12/2005 - 22:40:00 - Scout Web Portail - v 1.1.1
*
* fonc_http.php v 1.1.1 - Classe php permettant de lire un fichier distant si allow_url_fopen = false
* Script trouvé sur http://php.belnet.be/manual/fr/function.fopen.php
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
*	Fichier ajouté dans la version 1.1.1
*	bug 12/12 : Correction bug fsockopen détection nouvelle version
*/

/* Syntaxe :
*	$r = new HTTPRequest('http://www.php.net');
*	echo $r->DownloadToString();
*/

class HTTPRequest
{
   var $_fp;		// HTTP socket
   var $_url;		// full URL
   var $_host;		// HTTP host
   var $_protocol;	// protocol (HTTP/HTTPS)
   var $_uri;		// request URI
   var $_port;		// port
  
   // scan url
   function _scan_url()
   {
       $req = $this->_url;
      
       $pos = strpos($req, '://');
       $this->_protocol = strtolower(substr($req, 0, $pos));
      
       $req = substr($req, $pos+3);
       $pos = strpos($req, '/');
       if ($pos === false) $pos = strlen($req);
       $host = substr($req, 0, $pos);
      
       if (strpos($host, ':') !== false)
       {
           list($this->_host, $this->_port) = explode(':', $host);
       }
       else
       {
           $this->_host = $host;
           $this->_port = ($this->_protocol == 'https') ? 443 : 80;
       }
      
       $this->_uri = substr($req, $pos);
       if ($this->_uri == '') $this->_uri = '/';
   }
  
   // constructor
   function HTTPRequest($url)
   {
       $this->_url = $url;
       $this->_scan_url();
   }
  
   // download URL to string
   function DownloadToString()
   {
       $crlf = "\r\n";
      
       // generate request
       $req = 'GET ' . $this->_uri . ' HTTP/1.0' . $crlf
           .    'Host: ' . $this->_host . $crlf
           .    $crlf;
      
		// fetch
		if ($this->_fp = @fsockopen(($this->_protocol == 'https' ? 'ssl://' : '') . $this->_host, $this->_port))
		{
			fwrite($this->_fp, $req);
			while(is_resource($this->_fp) && $this->_fp && !feof($this->_fp))
				$response .= fread($this->_fp, 1024);
			fclose($this->_fp);
		}
      
       // split header and body
       $pos = strpos($response, $crlf . $crlf);
       if($pos === false)
           return($response);
       $header = substr($response, 0, $pos);
       $body = substr($response, $pos + 2 * strlen($crlf));
      
       // parse headers
       $headers = array();
       $lines = explode($crlf, $header);
       foreach($lines as $line)
           if(($pos = strpos($line, ':')) !== false)
               $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos+1));

       // redirection?
       if(isset($headers['location']))
       {
           $http = new HTTPRequest($headers['location']);
           return($http->DownloadToString($http));
       }
       else
       {
           return($body);
       }
   }
}
?>