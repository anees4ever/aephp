<?php
defined("aeAPP") or die("Restricted Access");
//============================================================+
// Generate and Send Mail
//============================================================+
function sendsms_file($numbers, $message, $response="") {
	$body= '
<html>
<head><title>SMS Receipt @ '.date('d/m/Y h:i:s a').'</title></head>
<body>
To: <code>'.$numbers.'</code><br/><br/>
Message: <pre>'.$message.'</pre>
'.($response!=""?'Response: <strong>'.$response.'</strong>':'').'
</body>
</html>
	';
	$toFile= "sms_at_".date('Ymd_His');
	$directory= PATH_ROOT . DS . "temp" . DS . "filesms";
	if(!is_dir($directory)) { mkdir($directory); }
	$filename= $toFile.".filesms";
	retry:
		if(file_exists($directory . DS . $filename)) {
			$filename= $toFile."_".rand().".filesms";
			goto retry;
		}
	$fp= fopen($directory . DS . $filename, "w");
	fwrite($fp, $body);
	fclose($fp);    
	return true;
}
?>