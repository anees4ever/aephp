<?php

defined("aeAPP") or die("Restricted Access");

class PageRights {
	public $view= true;
	public $add= true;
	public $modify= true;
	public $delete= true;
	public function addable($abort= false){
		return $this->add||($abort?aeUser::terminate():false);
	}
	public function editable($abort= false){
		return $this->modify||($abort?aeUser::terminate():false);
	}
	public function deletable($abort= false){
		return $this->delete||($abort?aeUser::terminate():false);
	}
	public function alterable($abort= false){
		return $this->modify||$this->delete||($abort?aeUser::terminate():false);
	}
    public function allowed($id, $abort= false){
		return (($id==-1) && ($this->add||$this->modify))||($id>0&&$this->modify)||($id==0&&$this->add)||($abort?aeUser::terminate():false);
	}
}
class aeApp {
	
	/**
	* @array page_catgs[pagename]=template name
	* @access private
	* @new pages to be added here(see constructor fx)    
	*/
	private $page_catgs= array();
	
	/**
	* @string current page title
	* @access private
	*/
	private $title= "";
	
	/**
	* @array current page metadata
	* @access private
	*/
	private $metadata= array();
	
	/**
	* @array current page scripts
	* @access private
	*/
	private $scripts= array();
	
	/**
	* @array current page css
	* @access private
	*/
	private $css= array();
	
	/**
	* @array current page custom header
	* @access private
	*/
	private $custom= array();
	
	/**
	* @string current page cmd
	* @access public
	*/
	public $cmd;
	
	/**
	* @string current page content
	*/
	public $content_html;
	
	/**
	* @array current page notices
	*/
	public $content_notices;
	
	/**
	* @boolean raise_error called
	*/
	public $error_exists;
	
	/**
	* Rights store
	* 
	* @object PageRights 
	*/
	public $oPageRights;
	
    /**
	* @aeAPP Constructor
	*/
    public function __construct() {
        $this->page_catgs= $this->getConfig()->page_catgs;
        $this->oPageRights= new PageRights();
    } 
	
	/**
	* Returns an Instance of MySQL Database.
	* @return instance of aeMySQL.
	*/
	public static function getDB($errorValidation= true) {
		$db= aeMySQL::getInstance();
		if($errorValidation) {
			aeApp::raiseErrorIf($db);
		}
		return $db;
	}
	public static function getRights() {
		return aeApp::getApp()->oPageRights;
	}
	
	/**
	* Returns an Instance of aeAppTmpl
	* @return instance of aeAppTmpl.
	*/
	public static function getApp() {
		static $appinstance;
		if(!is_object($appinstance)) {
			$appinstance= new aeApp();
			$appinstance->error_exists= false;
		}
		return $appinstance;
	}
	
	/**
	* Returns an Instance of aeConfig
	* @return instance of aeConfig.
	*/
	public static function getConfig() {
		return aeConfig::getInstance();
	}

	/**
	* Process page navigation
	*/	
	function process() {
		$config= $this->getConfig();
		$this->error_exists= false;
		//if SEF url(ie. var cmd is not set, (domain/<pagealias/args>))
		if(aeRequest::getVar("cmd", "") == "") {
			$this->cmd= aeURI::SefToUri();
		}
		//if not SEF url(ie. var cmd is set, (domain/index.php?cmd=<pagealias/args>))
		else {
			$this->cmd= aeRequest::getVar("cmd", "");
		}
		$this->cmd= $this->cmd == "" ? $config->default_page : $this->cmd;
		$this->cmd= aeURI::SefSafeUri($this->cmd);
		
		if(class_exists("aeUser")) {
			$this->oPageRights->view= aeUser::hasRights(0, true, $this->cmd);
			$this->oPageRights->add= $this->oPageRights->view && aeUser::hasRights(1, false, $this->cmd);
			$this->oPageRights->modify= $this->oPageRights->view && aeUser::hasRights(2, false, $this->cmd);
			$this->oPageRights->delete= $this->oPageRights->view && aeUser::hasRights(3, false, $this->cmd);
		}
		
        //You can add your code to load meta data and information,
        //Also, you can add them from page, (can ignore this section)
		if(($this->cmd !== false) && ($this->cmd != "")){
			$this->setMeta("robots", $this->getMeta("robots"));
			$this->setMeta("generator", $this->getMeta("generator"));
			$this->setMeta("author", $this->getMeta("author"));
			
			$this->setMeta("keywords",$config->metaKeys);
			$this->setMeta("description", $config->metaKeys);
			$this->setTitle($config->sitename);
		} else {
			aeApp::raiseError(404);
		}
	}
	
	/**
	* Process page inclusion
	*/	
	function includePage() {
		if(($this->cmd == false) || ($this->cmd == "")){
			aeApp::raiseError(401);
		} else {
			$config= $this->getConfig();
			$pagefile= PATH_PGS.DS."{$this->cmd}{$config->pagefile_sufix}.php";
			if (!file_exists($pagefile)) {
				aeApp::raiseError(404);
			} else if ($this->error_exists !== true) {
				$this->content_html= "";
				ob_clean();
				include($pagefile);
				
				if($this->error_exists !== true) {
					$this->content_html= ob_get_clean();
				}
							
				$this->processNotices(false);
	            //send true to echo notices on the content area with jQuery animation,
	            //and call the below commented line. 
				//$this->content_html= ob_get_clean() . $this->content_html;
			}
		}
	}
	
	/**
	* Process page render
	*/	
	function render() {
		$config= $this->getConfig();
		//Process Auto Loader
		aeAutoLoader::loadLibraries();
		aeAutoLoader::loadHelpers();
		aeAutoLoader::loadContentHelper();
		aeAutoLoader::loadScripts();
		aeAutoLoader::loadCSSs();
		aeAutoLoader::loadIncludes();
		
		if($config->site_down == 0) {
			//Process URI
			$this->process();
			
			//Look for Template and Process
			if(array_key_exists($this->cmd, $this->page_catgs)) {
				$tmpl_name= $this->page_catgs[$this->cmd];
			} else { $tmpl_name= $config->tmpl_name; }
			
			require_once(PATH_TMPL.DS.$tmpl_name.DS."theme-init.php");
			
			//Include Requested Page
			$this->includePage();
			//Call Content Renderers
			if(class_exists("aeContent")) {
				aeContent::callContentPlugins();
			}
		} else {
			aeApp::raiseError(503, $config->down_message);
		}
		aeApp::raiseErrorIf(aeApp::getDB());
		if(aeXHR!==false) {
			//Exit with echoing contents
			echo $this->content_html;
		} else {
			require_once(PATH_TMPL.DS.$tmpl_name.DS."theme-apply.php");
			$this->content_html= ob_get_clean();
			if(class_exists("aeContent")) {
				aeContent::callTemplatePlugins();
			}
			echo $this->content_html;
		}
		if(ob_get_length()>0) ob_end_flush();
	}

	/**
	* set Page Title.
	* @param string page title.
	*/	
	function setTitle($title) {
		$this->title= $title;
	}
	/**
	* Returns Page Title.
	* @return string page title.
	*/	
	function getTitle($sufixSitename= true) {
		$config= $this->getConfig();
		if($this->title == "") {
			return $config->sitename;
		} else {
			return $this->title.($sufixSitename?" | ".$config->sitename:"");
		}
	}
	/**
	* sets meta tags
	* @param string meta name
	* @param string meta value
	*/	
	function setMeta($type, $value) {
		$this->metadata[$type]= $value;
	}
	/**
	* Returns Page meta data.
	* @param string meta name
	* @return string meta data.
	*/
	function getMeta($type) {
		$config= $this->getConfig();
		if(isset($this->metadata[$type]) && $this->metadata[$type] !== "") {
			return $this->metadata[$type];
		}
		else {
			switch($type) {
				case 'robots':
					return $config->metaRobots;
				break;
				case 'generator':
					return $config->metaGenerator;
				break;
				case 'author':
					return $config->metaAuthor;
				break;
				case 'keywords':
					return $config->metaKeys;
				break;
				case 'description':
					return $config->metaDesc;
				break;
			}
		}
	}
	/**
	* include content sub Php
	* @param string php file
	*/	
	function addContentHelpers($type, $content = "") {
		$content= ($content!=="")?$content:$this->currentpage["content"];
		if(file_exists(PATH_COM.DS."com_".$content.DS.$type.".php")) {
			require_once(PATH_COM.DS."com_".$content.DS.$type.".php");
		}
	}
	/**
	* include Php
	* @param string php file
	*/	
	function addPhp($file) {
        if(file_exists($file)) {
			include($file);
		}
	}
	/**
	* set Scripts
	* @param string script file
	*/	
	function addScript($file, $type="file") {
		if(!in_array(array("type"=> $type, "script"=> $file), $this->scripts)) {
			$this->scripts[]= array("type"=> $type, "script"=> $file);
		}
	}
	/**
	* set Scripts
	* @param string script file
	*/	
	function addCSS($file) {
		if(!in_array($file, $this->css)) {
			$this->css[]= $file;
		}
	}
	
	/**
	* set Custom headers
	* @param string custom header
	*/	
	function addCustom($custom) {
		if(!in_array($custom, $this->custom)) {
			$this->custom[]= $custom;
		}
	}
	function getHead() {
		$head= LB;//<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'.LB;
		foreach($this->metadata as $metaname => $value) {
			$head.= '    <meta name="'.$metaname.'" content="'.$this->getMeta($metaname).'" />'.LB;
		}
		foreach($this->custom as $idx => $custom) {
			$head.= $custom.LB;
		}
		foreach($this->css as $idx => $file) {
			$sufix= aeUri::withVersion($file);
			$head.= '    <link href="'.$file.$sufix.'" rel="stylesheet" type="text/css" />'.LB;
		}
		$head.= '    <title>'.$this->getTitle().'</title>'.LB;
		return $head;
	}
	function getJS() {
		$head= LB;
		$scripts= '';
		foreach($this->scripts as $idx => $ascript) {
			if($ascript["type"] == "file") {
				$sufix= aeUri::withVersion($ascript["script"]);
				$head.= '    <script type="text/javascript" language="javascript" src="'.$ascript["script"].$sufix.'"></script>'.LB;
			}
			else {
				$scripts.= '
    <script type="text/javascript" language="javascript" >
		'.$ascript["script"].'
	</script>';
			}
		}
		$head.= $scripts;
		return $head;
	}
	
	/**
	* Raise an Error if no token found.
	*/
	public static function checkToken() {
		if(class_exists("aeUser")) {
			$tkn= aeRequest::getVar(aeUser::getToken(2), "");
			if(($tkn == "") || ($tkn !== aeUser::getToken(1))) {
				aeApp::raiseError("10", "Invalid Token for current session.");
			}
		} else {
			aeApp::raiseError("11", "Token security is currently disabled.");
		}
	}
	
	/**
	* Raise an Error and abort page loading.
	* @param int error num
	* @param string error desc
	*/
	public static function raiseErrorIf($obj) {
		if($obj->getError()) {
			aeApp::raiseError($obj->_errorNum, $obj->_errorMsg);
		}
	}
	public static function raiseError($errorNum, $errorMsg = "") {
		$adb= aeApp::getDB(false);
		$adb->rollback();
		ob_clean();
		if(aeXHR !== false) {
			$errorMsg= html_entity_decode($errorMsg);
			echo json_encode(array('result'=>'error', 'msg'=>$errorNum.", ".$errorMsg));
			exit(0);
		} else {
			include(PATH_AEPHP.DS."application".DS."error.php");
		}
	}
	
	/**
	* add a notice message to page.
	* @param notice message
	*/
	public static function addNotice($notice, $type = 'notice'/*message/error/warning*/, $link= "") {
		$config= aeApp::getConfig();
		$notice_count= (int)aeSession::getVar($config->session_name."_ntc_cnt", 0) + 1;
		aeSession::setVar($config->session_name."_ntc_".$notice_count, $notice);
		aeSession::setVar($config->session_name."_ntc_type_".$notice_count, $type);
		aeSession::setVar($config->session_name."_ntc_link_".$notice_count, $link);
		aeSession::setVar($config->session_name."_ntc_cnt", $notice_count);
	}
	/**
	* process notice message to page.
	*/
	function processNotices() {
		$config= $this->getConfig();
		$this->content_notices= array();
		$notice_count= (int)aeSession::getVar($config->session_name."_ntc_cnt", 0);
		for($I=1; $I<=$notice_count; $I++) {
			if(aeSession::getVar($config->session_name."_ntc_".$I, "") !== '') {
				$this->content_notices[]= array(
					"type"=> aeSession::getVar($config->session_name."_ntc_type_".$I, "notice"),
					"link"=> aeSession::getVar($config->session_name."_ntc_link_".$I, ""),
					"notice"=> aeSession::getVar($config->session_name."_ntc_".$I, "-")
				);
				aeSession::unsetVar($config->session_name."_ntc_".$I);
				aeSession::unsetVar($config->session_name."_ntc_type_".$I);
			}
		}
	}
	
	/**
	* Array Helper
	* @param delimeted string
	* @param first delimeted string
	* @param second delimeted string
	* @return associated array
	*/
	public static function explore($params, $slicer = "\r\n", $subslicer = "=") {
		$result= array();
		$params= explode($slicer, $params);
		foreach($params as $id => $aparam) {
			$aparam= explode($subslicer, $aparam);
			if(count($aparam) > 1) {
				$result[$aparam[0]]= $aparam[1];
			}
		}
		return $result;
	}
	/**
	* Array Helper
	* @param associative array
	* @param first delimeted string
	* @param second delimeted string
	* @return delimeted string
	*/
	public static function implore($params, $glue = "\r\n", $subglue = "=") {
		$result= "";
		foreach($params as $key => $val) {
			if($key !== '') {
				$result .= $key . $subglue . $val . $glue;
			}
		}
		return $result;
	}
	/**
	* Redirect Utility
	* @param url string
	* @param msgType string
	* @param moved boolean
	* @param extras array - for xhr results
	*/
	public static function redirect($url, $msg='', $msgType='message', $moved = false, $extras= array()) {
		if(aeXHR == true) {
			ob_clean();
			$msgType= $msgType=="message"||$msgType=="notice"?"success":$msgType;
			$msgType= $msgType==""||$msgType!="error"?"success":$msgType;
			$data= array("result"=> $msgType, "msg"=> $msg, "data"=> $extras);
			echo json_encode($data);
			//echo "{result: '{$msgType}', msg: '{$msg}', data}";//Page Redirect Occured. 
			exit(0);
		}
		// check for relative internal links
		if (preg_match( '#^index[2]?.php#', $url )) {
			$url = aeURI::base() . $url;
		}

		// Strip out any line breaks
		$url = preg_split("/[\r\n]/", $url);
		$url = $url[0];

		// If we don't start with a http we need to fix this before we proceed
		// We could validly start with something else (e.g. ftp), though this would
		// be unlikely and isn't supported by this API
		if (!preg_match( '#^http#i', $url )) {
			$uri =& aeURI::getInstance();
			$prefix = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));

			if ($url[0] == '/') {
				// we just need the prefix since we have a path relative to the root
				$url = $prefix . $url;
			}
			else {
				// its relative to where we are now, so lets add that
				$parts = explode('/', $uri->toString(array('path')));
				array_pop($parts);
				$path = implode('/',$parts).'/';
				$url = $prefix . $path . $url;
			}
		}
		//add content mode[ae_view=content]
		$co= aeRequest::getVar('ae_view', '');
		if($co !== '') {
			aeURI::addToURI($url, "ae_view={$co}");
		}

		// If the message exists, enqueue it
		if (trim( $msg )) {
			aeApp::addNotice($msg, $msgType);
		}

		// If the headers have been sent, then we cannot send an additional location header
		// so we will output a javascript redirect statement.
		if (headers_sent()) {
			echo "<script>document.location.href='$url';</script>\n";
		}
		else {
			if (!$moved && strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'webkit') !== false) {
				// WebKit browser - Do not use 303, as it causes subresources reload (https://bugs.webkit.org/show_bug.cgi?id=38690)
				echo '<html><head><meta http-equiv="refresh" content="0;'. $url .'" /></head><body></body></html>';
			}
			else {
				// All other browsers, use the more efficient HTTP header method
				header($moved ? 'HTTP/1.1 301 Moved Permanently' : 'HTTP/1.1 303 See other');
				header('Location: '.$url);
			}
		}
		exit(0);
	}
}

?>
