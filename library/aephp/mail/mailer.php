<?php
defined("aeAPP") or die("Restricted Access");

function sendmail($__mail, $__heading, $__content, $__extra_bcc= true) {
	$mailer= aeApp::getConfig()->mailer_engine;

	$mailer_func= "sendmail_{$mailer}";
	require_once(PATH_AEPHP.DS.'mail'.DS.$mailer.'.php');
	require_once(PATH_AEPHP.DS.'mail'.DS.'filemail.php');
	
	return call_user_func($mailer_func, $__mail, $__heading, $__content);
}

function getMailConfig() {
	$config= aeApp::getConfig();
	$object= new aeObject();
	$object->setProperty("smtp_from_mail", $config->mail_from_mail)
		->setProperty("smtp_from_name", $config->mail_from_name)
		->setProperty("smtp_host", $config->smtp_adrs)
		->setProperty("smtp_port", $config->mail_port)
		->setProperty("smtp_username", $config->smtp_user)
		->setProperty("smtp_password", $config->smtp_pass);
	return $object;
}