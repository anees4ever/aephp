<?php

defined("aeAPP") or die("Restricted Access");
//$errorNum, $errorMsg
$_APP= aeApp::getApp();

$status_code= $errorNum;
switch ($status_code) {
    case 100: $status_text = 'Continue'; break;
    case 101: $status_text = 'Switching Protocols'; break;
    case 200: $status_text = 'OK'; break;
    case 201: $status_text = 'Created'; break;
    case 202: $status_text = 'Accepted'; break;
    case 203: $status_text = 'Non-Authoritative Information'; break;
    case 204: $status_text = 'No Content'; break;
    case 205: $status_text = 'Reset Content'; break;
    case 206: $status_text = 'Partial Content'; break;
    case 300: $status_text = 'Multiple Choices'; break;
    case 301: $status_text = 'Moved Permanently'; break;
    case 302: $status_text = 'Moved Temporarily'; break;
    case 303: $status_text = 'See Other'; break;
    case 304: $status_text = 'Not Modified'; break;
    case 305: $status_text = 'Use Proxy'; break;
    case 400: $status_text = 'Bad Request'; break;
    case 401: $status_text = 'Unauthorized Access'; break;
    case 402: $status_text = 'Payment Required'; break;
    case 403: $status_text = 'Access Forbidden'; break;
    case 404: $status_text = 'Page Not Found'; break;
    case 405: $status_text = 'Method Not Allowed'; break;
    case 406: $status_text = 'Not Acceptable'; break;
    case 407: $status_text = 'Proxy Authentication Required'; break;
    case 408: $status_text = 'Request Time-out'; break;
    case 409: $status_text = 'Conflict'; break;
    case 410: $status_text = 'Gone'; break;
    case 411: $status_text = 'Length Required'; break;
    case 412: $status_text = 'Precondition Failed'; break;
    case 413: $status_text = 'Request Entity Too Large'; break;
    case 414: $status_text = 'Request-URI Too Large'; break;
    case 415: $status_text = 'Unsupported Media Type'; break;
    case 500: $status_text = 'Internal Server Error'; break;
    case 501: $status_text = 'Not Implemented'; break;
    case 502: $status_text = 'Bad Gateway'; break;
    case 503: $status_text = 'Service Unavailable'; break;
    case 504: $status_text = 'Gateway Time-out'; break;
    case 505: $status_text = 'HTTP Version not supported'; break;
    default:
        $status_code= 200;     
        $status_text= 'Application Error';
    break;
}
$errorMsg = $status_code == 404? "Page ".aeApp::getApp()->cmd.aeApp::getConfig()->sef_sufix." is not available or not found." : $errorMsg;
$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

header($protocol . ' ' . $status_code . ' ' . $status_text);
if($errorNum !== 503) {
	header("refresh:10;url=".aeURI::base());//To Redirect
}
$_APP->setTitle("{$status_text}");

$_APP->content_html= '
<div class="row">
&nbsp;
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-red">
            <div class="panel-heading">
                Hey... aePHP has stoped working with the following error...!
            </div>
            <div class="panel-body">
                <div class="col-lg-2">
					<img src="'.aeURI::base(true).'/assets/images/aephp_cat.png">
				</div>
				<div class="col-lg-10">
					<p class="text-warning"><strong>Error Code: '.$errorNum.'</strong></p>
					<p class="text-warning"><strong>Error: '.$status_text.'</strong></p>
					<p class="text-danger">'.$errorMsg.'</p>
				</div>
            </div>
            <div class="panel-footer">
                '.($errorNum !== 503?'
<p>You\'ll be redirected in about <span id="redirect_no">10</span> secs or click <a href="'.aeURI::base().'" style="color:#F00;">here</a></p>
				':'').'
            </div>
        </div>
    </div>
</div>';
$_APP->error_exists= true;

$script= 'setInterval(function(){ var i= parseInt($("#redirect_no").text()); i= i>=1?i:1; $("#redirect_no").text(i-1);}, 1000);';
$_APP->addScript($script, 'text');

?>