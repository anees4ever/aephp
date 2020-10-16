<?php

defined("aeAPP") or die("Restricted Access");

class aeGet {
	
	public static $escape_string = false;
	public static function escape($esc) {
		aeGet::$escape_string= $esc;
	}
	public static function escaped($str) {
		$fx= function_exists("mysqli_real_escape_string")?"mysqli_real_escape_string":"mysqli_escape_string";
		return aeRequest::$escape_string?$fx(aeApp::getDB()->_con,$str):$str;
	}
	/**
	* Sets GET Value
	* @param string GET variable name
	* @param string GET variable value
	*/
	public static function setVar($key, $value) {
		$_GET[$key]= $value;
	}
	
	/**
	* Returns GET Value
	* @param string GET variable name
	* @param string GET variable default value
	* @return GET Value
	*/
	public static function getVar($key, $default = "") {
		if(isset($_GET[$key])) {
			return aeGet::escaped($_GET[$key]);
		}
		else {
			return aeGet::escaped($default);
		}
	}
	
	/**
	* Unset GET variable
	* @param string GET variable name
	*/
	public static function unsetVar($key, $default = "") {
		unset($_GET[$key]);
	}
}