<?php
defined("aeAPP") or die("Restricted Access");
//============================================================+
// Generate and Send Mail
//============================================================+
function sendmail_filemail($__mail, $__heading, $__content) {
	$config= getMailConfig();
	$from= isset($__mail["from"])?$__mail["from"]:$config->smtp_from_mail;
    $fromName= isset($__mail["fromName"])?$__mail["fromName"]:$config->smtp_from_name;
	$to= isset($__mail["to"])?$__mail["to"]:$config->smtp_from_mail;
	$toName= isset($__mail["toName"])?$__mail["toName"]:$to;
	$subject= (isset($__mail["subject"])?$__mail["subject"]:"");
	$toFile= "";
	if(is_array($to)) {
		$toName= "";
		foreach($to as $email => $name) {
			$toFile= $toFile==""?$email:$toFile;
			$toName.= "{$name} &lt;{$email}&gt;, ";
		}
	} else {
		$toFile= $to;
		$toName= "{$toName} &lt;{$to}&gt;";
	}
	$cc= "";
	$bcc= "";
	if(isset($__mail["cc"]) && is_array($__mail["cc"])) {
		foreach($__mail["cc"] as $mail => $name) {
			$cc.= "{$name} &lt;{$mail}&gt;, ";
		}
	}
	if(isset($__mail["bcc"]) && is_array($__mail["bcc"])) {
		foreach($__mail["bcc"] as $mail => $name) {
			$bcc.= "{$name} &lt;{$mail}&gt;, ";
		}
	}
	$attachments= "";
	if(isset($__mail["attachments"]) && is_array($__mail["attachments"])) {
		foreach($__mail["attachments"] as $idx => $attachment) {
			$attachments.= "{$attachment["doc_name"]}, ";
		}
	}
	
	$__mail["receipt"]= isset($__mail["receipt"])?$__mail["receipt"]:"";
	$__mail["priority"]= isset($__mail["priority"])?$__mail["priority"]:"0";
	
	if(ob_get_length()>0) { ob_end_flush(); }
	ob_start();
	
	include(PATH_AEPHP . DS . "mail" . DS . "template-html.php");
	$body= ob_get_clean();
	$body= filterMailBodyHTML($body, false);
	$body= str_replace('<body style="background:#f8f8f8;">', "<body style=\"background:#f8f8f8;\">====================================<br />
@".date('d/m/Y h:i:s a')."<br />
From: {$fromName} &lt;{$from}&gt;<br />
To: {$toName}<br />
Cc: {$cc}<br />
Bcc: {$bcc}<br />
Subject: {$subject}<br />
Attachments: {$attachments}<br />
====================================<br />", $body);
	
	$directory= PATH_ROOT . DS . "temp" . DS . "filemail";
	if(!is_dir($directory)) { mkdir($directory); }
	$filename= "sent ".date('d-m-Y h.i.s a')." ".$toFile.".filemail";
	retry:
		if(file_exists($directory . DS . $filename)) {
			$filename= "sent ".date('d-m-Y h.i.s a')." ".$toFile."_".rand().".filemail";
			goto retry;
		}
	$fp= fopen($directory . DS . $filename, "w");
	fwrite($fp, $body);
	fclose($fp);    
	return true;
}
?>