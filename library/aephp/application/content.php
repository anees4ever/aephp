<?php
defined("aeAPP") or die("Restricted Access");
class aeContent extends aeObject {
	/**
	* @var array of loaded plugins
	* @access private
	*/
	var $loadedPlugins;
	/**
	* @function returns an instance of aeConent
	* @return an instance of aeConent
	*/
	static function getInstance() {
		static $content_instance;
		if(!is_object($content_instance)) {
			$content_instance= new aeContent();
		}
		return $content_instance;
	}
	/**
	* @aeContent Constructor
	*/
    function __construct() {
		/*"plg_function"=>array("plg_call_type(onContent|onTemplate)");*/
        $this->loadedPlugins= array();
    }
	static function registerContentPlugin($plg_func) {
		$ct= aeContent::getInstance();
		$ct->registerPlugin($plg_func, "onContent");
	}
	static function registerTemplatePlugin($plg_func) {
		$ct= aeContent::getInstance();
		$ct->registerPlugin($plg_func, "onTemplate");
	}
	static function callContentPlugins() {
		$ct= aeContent::getInstance();
		$App= aeApp::getApp();
		$ct->callPlugins("onContent", $App->content_html);
	}
	static function callTemplatePlugins() {
		$ct= aeContent::getInstance();
		$App= aeApp::getApp();
		$ct->callPlugins("onTemplate", $App->content_html);
	}
	function registerPlugin($plg_func, $call_method = "onContent") {
		$this->loadedPlugins[$plg_func]= $call_method;
	}
	function callPlugin($plg_func, &$content_text) {
		if( (isset($this->loadedPlugins[$plg_func])) && function_exists($plg_func) ) {
			$content_text= call_user_func($plg_func, $content_text);
		}
	}
	function callPlugins($plg_method, &$content_text) {
		if(is_array($this->loadedPlugins) && (count($this->loadedPlugins)>0)) {
			foreach($this->loadedPlugins as $plg_func => $method) {
				if($method==$plg_method) {
					$this->callPlugin($plg_func, $content_text);
				}
			}
		}
	}
}
