<?php
	defined("aeAPP") or die("Restricted Access");
	
	class aeTmpl {
		
		/** var $contents
		  * array (section => array(id=>content_data))
		  */
		private $contents= array();
		
		/*
		 * constructor __construct
		 */
		public function __construct() {
			$this->contents= array();
		}
		
		/*
		 * function getInstance
		 * @return instance of aeTmpl object
		 */
		public static function getInstance() {
			static $instance;
			if(!is_object($instance)) {
				$instance= new aeTmpl();
			}
			return $instance;
		}
		
		/*
		 * function set : sets a content
		 * @param $section string : section name
		 * @param $data string : section content
		 * @param $id integer : section sub id(for multiple content in same section)
		 * @return instance
		 */
		public static function set($section, $data, $id = 0){
			$tmpl= aeTmpl::getInstance();
			$tmpl->contents[$section][$id]= $data;
			return $tmpl;
		}
		
		/*
		 * function get : get a content
		 * @param $section string : section name
		 * @param $id integer : section sub id(for multiple content in same section)
		 * @return content
		 */
		public static function get($section, $id = 0){
			$tmpl= aeTmpl::getInstance();
			return(isset($tmpl->contents[$section][$id])?$tmpl->contents[$section][$id]:"");
		}
		
		/*
		 * function append : append content to existing one
		 * @param $section string : section name
		 * @param $data string : section content
		 * @param $id integer : section sub id(for multiple content in same section)
		 * @return instance
		 */
		public static function append($section, $data, $id = 0){
			$tmpl= aeTmpl::getInstance();
			$tmpl->contents[$section][$id]= (isset($tmpl->contents[$section][$id])?$tmpl->contents[$section][$id]:"") . $data;
			return $tmpl;
		}
		
		/*
		 * function prepend : prepend content to existing one
		 * @param $section string : section name
		 * @param $data string : section content
		 * @param $id integer : section sub id(for multiple content in same section)
		 * @return instance
		 */
		public static function prepend($section, $data, $id = 0){
			$tmpl= aeTmpl::getInstance();
			$tmpl->contents[$section][$id]= $data . (isset($tmpl->contents[$section][$id])?$tmpl->contents[$section][$id]:"");
			return $tmpl;
		}
		
		/*
		 * function remove : remove a content
		 * @param $section string : section name
		 * @param $id integer : section sub id(for multiple content in same section)
		 * @return instance
		 */
		public static function remove($section, $id = 0){
			$tmpl= aeTmpl::getInstance();
			$tmpl->contents[$section][$id]= "";
			return $tmpl;
		}
		
		/*
		 * function isSection : check for a section exists or not
		 * @param $section string : section name
		 * @return true or false
		 */
		public static function isSection($section, $id = -1){
			$tmpl= aeTmpl::getInstance();
			if($id >= 0) {
				return(isset($tmpl->contents[$section][$id])?(trim($tmpl->contents[$section][$id])!==''?true:false):false);
			}		
			if(isset($tmpl->contents[$section])) {
				foreach($tmpl->contents[$section] as $id => $content_data) {
					if(trim($content_data) !== '') {
						return true;
						break;
					}
				}
				return false;
			} else {
				return false;
			}
		}
		
		/*
		 * function getSection : get all contents in a section
		 * @param $section string : section name
		 * @param $prefix string : prefix for content data
		 * @param $sufix string : sufix for content data
		 * @param $sep string : seperator for each content
		 * @return all_content
		 */
		public static function getSection($section, $prefix = "", $sufix = "", $sep = ""){
			$return= "";
			$tmpl= aeTmpl::getInstance();
			if(isset($tmpl->contents[$section])) {
				foreach($tmpl->contents[$section] as $id => $data) {
					if(trim($data) !== "") {
						if($return !== "") {
							$return.= $sep;
						}
						$return.= "{$prefix}{$data}{$sufix}\r\n";
					}
				}
			}
			return $return;
		}
	}


?>
