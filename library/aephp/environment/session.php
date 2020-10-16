<?php

defined("aeAPP") or die("Restricted Access");

class aeSession {
    
    /**
	* Start SESSION
	*/
	public static function start() {
		session_name(aeApp::getConfig()->session_name);
		session_start();
	}
	
	/**
	* SESSION ID
	*/
	public static function id() {
		return session_id();
	}
	
	/**
	* Stop SESSION
	*/
	public static function stop() {
		$_SESSION= array();
		if(isset($_COOKIE[session_name()])) {
			setcookie(session_name(), "", -42000, "/");
		}
		session_destroy();
	}
	
	/**
	* Sets SESSION Value
	* @param string SESSION variable name
	* @param string SESSION variable value
	*/
	public static function setVar($key, $value) {
		$_SESSION[$key]= $value;
	}
	
	/**
	* Returns SESSION Value
	* @param string SESSION variable name
	* @param string SESSION variable default value
	* @return SESSION Value
	*/
	public static function getVar($key, $default = "") {
		if(isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		else {
			return $default;
		}
	}
	
	/**
	* Unset SESSION variable
	* @param string SESSION variable name
	*/
	public static function unsetVar($key) {
		unset($_SESSION[$key]);
	}
}