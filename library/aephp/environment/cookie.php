<?php

defined("aeAPP") or die("Restricted Access");

class aeCookie {
	static $validityPlus = 2592000;
	/**
	* Sets COOKIE Value
	* @param string COOKIE variable name
	* @param string COOKIE variable value
	*/
	public static function setVar($key, $value) {
		setcookie($key,$value,time()+self::$validityPlus);
	}
	
	/**
	* Returns COOKIE Value
	* @param string COOKIE variable name
	* @param string COOKIE variable default value
	* @return COOKIE Value
	*/
	public static function getVar($key, $default = "") {
		if(isset($_COOKIE[$key])) {
			return $_COOKIE[$key];
		}
		else {
			return $default;
		}
	}
	
	/**
	* Unset COOKIE variable
	* @param string COOKIE variable name
	*/
	public static function unsetVar($key, $default = "") {
		setcookie($key,false,-3600);
	}
}