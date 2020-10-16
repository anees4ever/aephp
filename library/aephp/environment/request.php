<?php

defined("aeAPP") or die("Restricted Access");

class aeRequest {
	public static $escape_string = false;
	public static function escape($esc) {
		aeRequest::$escape_string= $esc;
	}
	public static function escaped($str) {
		$fx= function_exists("mysqli_real_escape_string")?"mysqli_real_escape_string":"mysqli_escape_string";
		return aeRequest::$escape_string?$fx(aeApp::getDB()->_con,$str):$str;
	}
	/**
	* Sets REQUEST Value
	* @param string REQUEST variable name
	* @param string REQUEST variable value
	*/
	public static function setVar($key, $value) {
		$_REQUEST[$key]= $value;
	}
	
	/**
	* Returns REQUEST Value
	* @param string REQUEST variable name
	* @param string REQUEST variable default value
	* @return REQUEST Value
	*/
	public static function getVar($key, $default = "") {
		if(isset($_REQUEST[$key])) {
			return aeRequest::escaped($_REQUEST[$key]);
		}
		else {
			return aeRequest::escaped($default);
		}
	}
	
	/**
	* Unset REQUEST variable
	* @param string REQUEST variable name
	*/
	public static function unsetVar($key, $default = "") {
		unset($_REQUEST[$key]);
	}
}