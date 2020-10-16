<?php
defined("aeAPP") or die("Restricted Access");
//============================================================+
// Generate and Send Mail
//============================================================+
function sendmail_phpmail($__mail, $__heading, $__content) {
	$config= getMailConfig();
	$from= isset($__mail["from"])?$__mail["from"]:$config->smtp_from_mail;
	$fromName= isset($__mail["fromName"])?$__mail["fromName"]:$config->smtp_from_name;
	$to= isset($__mail["to"])?$__mail["to"]:$config->smtp_from_mail;
	$toName= isset($__mail["toName"])?$__mail["toName"]:$to;
	$subject= (isset($__mail["subject"])?$__mail["subject"]:"");
	
	$__mail["receipt"]= isset($__mail["receipt"])?$__mail["receipt"]:"";
	$__mail["priority"]= isset($__mail["priority"])?$__mail["priority"]:"0";
	
	$cc= "";
	$bcc= "";
	if(isset($__mail["cc"]) && is_array($__mail["cc"])) {
		foreach($__mail["cc"] as $mail => $name) {
			$cc.= "{$mail};";
		}
	}
	if(isset($__mail["bcc"]) && is_array($__mail["bcc"])) {
		foreach($__mail["bcc"] as $mail => $name) {
			$bcc.= "{$mail};";
		}
	}
	//$bcc.= "anees4ever@gmail.com;";
	
	$header= "From: {$fromName}<{$from}>"."\r\n";
	$header.= $cc==""?"":"Cc: {$cc}"."\r\n";
	$header.= $bcc==""?"":"Bcc: {$bcc}"."\r\n";
	$header.= "receipt: {$__mail["receipt"]}"."\r\n";
	$header.= "priority: {$__mail["priority"]}"."\r\n";
	if(is_array($to)) {
		foreach($to as $email) {
			$result= mail($email, $subject, $__content, $header);
		}
	} else {
		$result= mail($to, $subject, $__content, $header);
	}
	$__content= "=======PHP_RESULT-{$result}=======".$__content;
	aeRequest::setVar("force_fileMail", "yes");
	sendmail_filemail($__mail, $__heading, $__content);
	return $result;
}