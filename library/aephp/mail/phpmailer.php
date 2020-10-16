<?php
defined("aeAPP") or die("Restricted Access");
//============================================================+
// Generate and Send Mail
//============================================================+
function sendmail_phpmailer($__mail, $__heading, $__content) {
	$config= getMailConfig();
	$from= isset($__mail["from"])?$__mail["from"]:$config->smtp_from_mail;
    $fromName= isset($__mail["fromName"])?$__mail["fromName"]:$config->smtp_from_name;
	$to= isset($__mail["to"])?$__mail["to"]:$config->smtp_from_mail;
	$toName= isset($__mail["toName"])?$__mail["toName"]:$to;
	$subject= (isset($__mail["subject"])?$__mail["subject"]:"");
	
	if ( isset($_SERVER["OS"]) && $_SERVER["OS"] == "Windows_NT" ) {
		$hostname = strtolower($_SERVER["COMPUTERNAME"]);
	} else {
		$hostname = `hostname`;
		$hostnamearray = explode('.', $hostname);
		$hostname = $hostnamearray[0];
	}
	ob_clean();
	header("Content-Type: text/plain");
	header("X-Node: $hostname");

	require_once(PATH_LIB.DS.'phpmailer'.DS.'phpmailer.php');
	$mail= new PHPMailer(true);

	try {
	    //Server settings
	    $mail->SMTPDebug = 0;//0-Off,1-Client,2-Client+Server
	    //$mail->IsSMTP();
		if ( strpos($hostname, 'cpnl') === FALSE ) {//if not cPanel
			$mail->Host = 'relay-hosting.secureserver.net';
		} else {
			$mail->Host = 'localhost';
		}
		$mail->SMTPAuth = false;

		$mail->SetFrom($config->smtp_username, $fromName, $auto= false);
		$mail->AddReplyTo($from, $fromName);

	    //Recipients
	    if(is_array($to)) {
	    	foreach($to as $_email => $_name) {
	    		$mail->AddAddress($_email, $_name);
	    	}
	    } else {
	    	$mail->AddAddress($to, $toName);
	    }

	    if(isset($__mail["cc"]) && is_array($__mail["cc"]) && (count($__mail["cc"]) > 0)) {
			foreach($__mail["cc"] as $_email => $_name) {
	    		$mail->AddCC($_email, $_name);
	    	}
		}
		if(isset($__mail["bcc"]) && is_array($__mail["bcc"]) && (count($__mail["bcc"]) > 0)) {
			foreach($__mail["bcc"] as $_email => $_name) {
	    		$mail->AddBCC($_email, $_name);
	    	}
		}

		if(isset($__mail["receipt"])) {
			$mail->ConfirmReadingTo= $__mail["receipt"];
		}
		if(isset($__mail["priority"])) {
			$mail->Priority= $__mail["priority"];
		}

	    // Attachments
	    if(isset($__mail["attachments"]) && is_array($__mail["attachments"]) && (count($__mail["attachments"]) > 0)) {
			foreach($__mail["attachments"] as $idx => $attachment) {
				$mail->AddAttachment($attachment["filename"], $attachment["doc_file"]);
			}
		}
	    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	    // Content
	    if(ob_get_length()>0) { ob_end_flush(); }
		ob_start();
		include(PATH_AEPHP . DS . "mail" . DS . "template-html.php");
		$body= ob_get_clean();
		//filterMailBodyHTML($body, $__message);
	    $mail->isHTML(true);
	    $mail->Subject= $subject;
	    $mail->Body   = $body;
	    $mail->AltBody= strip_tags($body);

		// Send the message
		$msg= "";
	    $result= $mail->send();

	    //$mailconversation = nl2br(htmlspecialchars(ob_get_clean())); //captures the output of PHPMailer and htmlizes it
	    ob_clean();
	    if($result===true) {
	    	$msg='SUCCESS';
	    } else {
	    	$msg= $mail->ErrorInfo;
	    	echo "Message could not be sent. Mailer Error: {$msg}";
	    }

		$__content= "=======MAILER_RESULT-{$msg}=======".$__content;
		aeRequest::setVar("force_fileMail", "yes");
		sendmail_filemail($__mail, $__heading, $__content);
		return $result;
	} catch (Exception $e) {
	    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	    return false;
	}
}
?>