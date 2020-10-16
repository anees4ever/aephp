<?php

defined("aeAPP") or die("Restricted Access");

class aePost {
	public static $escape_string = false;
	public static function escape($esc) {
		aePost::$escape_string= $esc;
	}
	public static function escaped($str) {
		$fx= function_exists("mysqli_real_escape_string")?"mysqli_real_escape_string":"mysqli_escape_string";
		return aeRequest::$escape_string?$fx(aeApp::getDB()->_con,$str):$str;
	}
	/**
	* Sets POST Value
	* @param string POST variable name
	* @param string POST variable value
	*/
	public static function setVar($key, $value) {
		$_POST[$key]= $value;
	}
	
	/**
	* Returns POST Value
	* @param string POST variable name
	* @param string POST variable default value
	* @return POST Value
	*/
	public static function getVar($key, $default = "") {
		if(isset($_POST[$key])) {
			return aePost::escaped($_POST[$key]);
		}
		else {
			return aePost::escaped($default);
		}
	}
	
	/**
	* Unset POST variable
	* @param string POST variable name
	*/
	public static function unsetVar($key, $default = "") {
		unset($_POST[$key]);
	}
}