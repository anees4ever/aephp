<?php
defined("aeAPP") or die("Restricted Access");

function sendsms($to, $message, $resort_id= 0) {
	$numbers= is_array($to)?implode(",", $to):$to;
	require_once(PATH_AEPHP.DS.'sms'.DS.'sms_file.php');
	
	$engine= aeApp::getConfig()->sms_engine;
	
	$response= "";
	if($engine=="some_other") {
		//require_once(PATH_AEPHP.DS.'sms'.DS.'sms_xxx.php');
		//sendFx($numbers, $message);
	}
	sendsms_file($numbers, $message, $response);
	return true;
}