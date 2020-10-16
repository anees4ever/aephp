<?php
defined("aeAPP") or die("Restricted Access");

class aeURI {
	var $_uri = null;
	var $_scheme = null;
	var $_host = null;
	var $_port = null;
	var $_user = null;
	var $_pass = null;
	var $_path = null;
	var $_query = null;
	var $_fragment = null;
	var $_vars = array ();

	function __construct($uri = null)
	{
		if ($uri !== null) {
			$this->parse($uri);
		}
	}
	
	public static function &getInstance($uri = 'SERVER')
	{
		static $instances = array();

		if (!isset ($instances[$uri]))
		{
			if ($uri == 'SERVER')
			{
				if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
					$https = 's://';
				} else {
					$https = '://';
				}
				if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI'])) {

					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

				 	if (strlen($_SERVER['QUERY_STRING']) && strpos($_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING']) === false) {
						$theURI .= '?'.$_SERVER['QUERY_STRING'];
					}

				}
				 else
				 {
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

					if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
						$theURI .= '?' . $_SERVER['QUERY_STRING'];
					}
				}

				$halt	= 0;
				while (true)
				{
					$last	= $theURI;
					$theURI = urldecode($theURI);

					if ($theURI == $last) {
						break;
					}
					else if (++$halt > 10) {
						exit();
					}
				}

				$theURI = str_replace('"', '&quot;',$theURI);
				$theURI = str_replace('<', '&lt;',$theURI);
				$theURI = str_replace('>', '&gt;',$theURI);
				$theURI = preg_replace('/eval\((.*)\)/', '', $theURI);
				$theURI = preg_replace('/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $theURI);
			}
			else
			{
				$theURI = $uri;
			}

			$instances[$uri] = new aeURI($theURI);
		}
		return $instances[$uri];
	}

	public static function base($pathonly = false)
	{
		static $base;

		if (!isset($base))
		{
			$live_site = '';
			if(trim($live_site) != '') {
				$uri =& aeURI::getInstance($live_site);
				$base['prefix'] = $uri->toString( array('scheme', 'host', 'port'));
				$base['path'] = rtrim($uri->toString( array('path')), '/\\');
				if(JPATH_BASE == JPATH_ADMINISTRATOR) {
					$base['path'] .= '/administrator';
				}
			} else {
				$uri	         =& aeURI::getInstance();
				$base['prefix'] = $uri->toString( array('scheme', 'host', 'port'));

				if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI']) &&
				    (!ini_get('cgi.fix_pathinfo') || version_compare(PHP_VERSION, '5.2.4', '<'))) {

					$base['path'] =  rtrim(dirname(str_replace(array('"', '<', '>', "'"), '', $_SERVER["PHP_SELF"])), '/\\');
				} else {
					$base['path'] =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
				}
			}
		}

		return $pathonly === false ? $base['prefix'].$base['path'].'/' : $base['path'];
	}

	public static function root($pathonly = false, $path = null)
	{
		static $root;

		if(!isset($root))
		{
			$uri	        =& aeURI::getInstance(aeURI::base());
			$root['prefix'] = $uri->toString( array('scheme', 'host', 'port') );
			$root['path']   = rtrim($uri->toString( array('path') ), '/\\');
		}

		if(isset($path)) {
			$root['path']    = $path;
		}

		return $pathonly === false ? $root['prefix'].$root['path'].'/' : $root['path'];
	}

	public static function current()
	{
		static $current;

		if (!isset($current))
		{
			$uri	 = & aeURI::getInstance();
			$current = $uri->toString( array('scheme', 'host', 'port', 'path'));
		}

		return $current;
	}

	function parse($uri)
	{
		$retval = false;

		$this->_uri = $uri;

		if ($_parts = $this->_parseURL($uri)) {
			$retval = true;
		}

		if(isset ($_parts['query']) && strpos($_parts['query'], '&amp;')) {
			$_parts['query'] = str_replace('&amp;', '&', $_parts['query']);
		}

		$this->_scheme = isset ($_parts['scheme']) ? $_parts['scheme'] : null;
		$this->_user = isset ($_parts['user']) ? $_parts['user'] : null;
		$this->_pass = isset ($_parts['pass']) ? $_parts['pass'] : null;
		$this->_host = isset ($_parts['host']) ? $_parts['host'] : null;
		$this->_port = isset ($_parts['port']) ? $_parts['port'] : null;
		$this->_path = isset ($_parts['path']) ? $_parts['path'] : null;
		$this->_query = isset ($_parts['query'])? $_parts['query'] : null;
		$this->_fragment = isset ($_parts['fragment']) ? $_parts['fragment'] : null;

		if(isset ($_parts['query'])) parse_str($_parts['query'], $this->_vars);
		return $retval;
	}

	function toString($parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'))
	{
		$query = $this->getQuery();

		$uri = '';
		$uri .= in_array('scheme', $parts)  ? (!empty($this->_scheme) ? $this->_scheme.'://' : '') : '';
		$uri .= in_array('user', $parts)	? $this->_user : '';
		$uri .= in_array('pass', $parts)	? (!empty ($this->_pass) ? ':' : '') .$this->_pass. (!empty ($this->_user) ? '@' : '') : '';
		$uri .= in_array('host', $parts)	? $this->_host : '';
		$uri .= in_array('port', $parts)	? (!empty ($this->_port) ? ':' : '').$this->_port : '';
		$uri .= in_array('path', $parts)	? $this->_path : '';
		$uri .= in_array('query', $parts)	? (!empty ($query) ? '?'.$query : '') : '';
		$uri .= in_array('fragment', $parts)? (!empty ($this->_fragment) ? '#'.$this->_fragment : '') : '';

		return $uri;
	}
	
	static function withVersion($url="") {
		if($url=="") {
			return "?v=".APP_VERSION;
		} else {
			return (strpos($url, "?")>0?"&":"?")."v=".APP_VERSION;
		}
	}

	function setVar($name, $value)
	{
		$tmp = @$this->_vars[$name];
		$this->_vars[$name] = $value;

		$this->_query = null;

		return $tmp;
	}

	function getVar($name = null, $default=null)
	{
		if(isset($this->_vars[$name])) {
			return $this->_vars[$name];
		}
		return $default;
	}

	function delVar($name)
	{
		if (in_array($name, array_keys($this->_vars)))
		{
			unset ($this->_vars[$name]);

			$this->_query = null;
		}
	}

	function setQuery($query)
	{
		if(!is_array($query)) {
			if(strpos($query, '&amp;') !== false)
			{
			   $query = str_replace('&amp;','&',$query);
			}
			parse_str($query, $this->_vars);
		}

		if(is_array($query)) {
			$this->_vars = $query;
		}

		$this->_query = null;
	}

	function getQuery($toArray = false)
	{
		if($toArray) {
			return $this->_vars;
		}

		if(is_null($this->_query)) {
			$this->_query = $this->buildQuery($this->_vars);
		}

		return $this->_query;
	}

	function buildQuery ($params, $akey = null)
	{
		if ( !is_array($params) || count($params) == 0 ) {
			return false;
		}

		$out = array();

		if( !isset($akey) && !count($out) )  {
			unset($out);
			$out = array();
		}

		foreach ( $params as $key => $val )
		{
			if ( is_array($val) ) {
				$out[] = aeURI::buildQuery($val,$key);
				continue;
			}

			$thekey = ( !$akey ) ? $key : $akey.'['.$key.']';
			$out[] = $thekey."=".urlencode($val);
		}

		return implode("&",$out);
	}
	function getScheme() {
		return $this->_scheme;
	}

	function setScheme($scheme) {
		$this->_scheme = $scheme;
	}

	function getUser() {
		return $this->_user;
	}

	function setUser($user) {
		$this->_user = $user;
	}

	function getPass() {
		return $this->_pass;
	}

	function setPass($pass) {
		$this->_pass = $pass;
	}

	function getHost() {
		return $this->_host;
	}

	function setHost($host) {
		$this->_host = $host;
	}

	function getPort() {
		return (isset ($this->_port)) ? $this->_port : null;
	}

	function setPort($port) {
		$this->_port = $port;
	}

	function getPath() {
		return $this->_path;
	}

	function setPath($path) {
		$this->_path = $this->_cleanPath($path);
	}

	function getFragment() {
		return $this->_fragment;
	}

	function setFragment($anchor) {
		$this->_fragment = $anchor;
	}

	function isSSL() {
		return $this->getScheme() == 'https' ? true : false;
	}

	function isInternal($url) {
		$uri =& aeURI::getInstance($url);
		$base = $uri->toString(array('scheme', 'host', 'port', 'path'));
		$host = $uri->toString(array('scheme', 'host', 'port'));
		if(stripos($base, aeURI::base()) !== 0 && !empty($host)) {
			return false;
		}
		return true;
	}
	function _cleanPath($path)
	{
		$path = explode('/', preg_replace('#(/+)#', '/', $path));

		for ($i = 0; $i < count($path); $i ++) {
			if ($path[$i] == '.') {
				unset ($path[$i]);
				$path = array_values($path);
				$i --;

			}
			elseif ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != ''))) {
				unset ($path[$i]);
				unset ($path[$i -1]);
				$path = array_values($path);
				$i -= 2;

			}
			elseif ($path[$i] == '..' AND $i == 1 AND $path[0] == '') {
				unset ($path[$i]);
				$path = array_values($path);
				$i --;

			} else {
				continue;
			}
		}

		return implode('/', $path);
	}
	function _parseURL($uri)
	{
		$parts = array();
		if (version_compare( phpversion(), '4.4' ) < 0)
		{
			$regex = "<^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?>";
			$matches = array();
			preg_match($regex, $uri, $matches, PREG_OFFSET_CAPTURE);

			$authority = @$matches[4][0];
			if (strpos($authority, '@') !== false) {
				$authority = explode('@', $authority);
				@list($parts['user'], $parts['pass']) = explode(':', $authority[0]);
				$authority = $authority[1];
			}

			if (strpos($authority, ':') !== false) {
				$authority = explode(':', $authority);
				$parts['host'] = $authority[0];
				$parts['port'] = $authority[1];
			} else {
				$parts['host'] = $authority;
			}

			$parts['scheme'] = @$matches[2][0];
			$parts['path'] = @$matches[5][0];
			$parts['query'] = @$matches[7][0];
			$parts['fragment'] = @$matches[9][0];
		}
		else {
			$parts= @parse_url($uri);
		}
		return $parts;
	}
	public static function sef_safe_callback($uri, $exp) {
		return preg_replace_callback($exp, "replace_uri_safe", $uri);
	}
	public static function parse_server_var($uri) {
		$uri= preg_replace_callback("#(.*?)index.php(.*?)#", "replace_uri_safe", $uri);
		return aeURI::SefSafeUri($uri);
	}
	public static function parse_uri_safe($uri, $script) {
		$script= $script==""?"index.php":$script;
		$uri= aeURI::sef_safe_callback($uri, "#(.*?)index.php(.*?)#");
		$uri= aeURI::sef_safe_callback($uri, "#(.*?)$script(.*?)#");
		$uri= explode('?', $uri);
		$uri= aeURI::sef_safe_callback($uri[0], "#(.*?)".aeApp::getConfig()->sef_sufix."#");
		return aeURI::SefSafeUri($uri);
	}
	public static function SefSafeUri($uri) {
		$uri= trim($uri, '/');
		$uri= aeURI::sef_safe_callback($uri, "#(.*?)//(.*?)#");
		return $uri;
	}
	public static function SefToUri() {
		$scriptFile= aeURI::parse_server_var($_SERVER["SCRIPT_NAME"]);
		$requestURI= aeURI::parse_uri_safe($_SERVER["REQUEST_URI"], $scriptFile);
		return $requestURI;
	}
	public static function FullURI($uri) {
		return aeURI::base(true)."/{$uri}";
	}
	public static function addToURI(&$uri, $val) {
		$url= explode("?", $uri);
		if(count($url)==2) {
			$uri.= "&$val";
		} else {
			$uri.= "?$val";
		}
		
	}
}
if(!function_exists("replace_uri_safe")) {
	function replace_uri_safe($matches) {
		return $matches[1];
	}
}
?>