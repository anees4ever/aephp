<?php
defined("aeAPP") or die("Restricted Access");

$Config= aeApp::getConfig();

//enable or disable library from library folder
$Config->auto_loads["library"][]= "aephp.database.mysql";				#MySQL Connectivity and DB Functions
$Config->auto_loads["library"][]= "aephp.database.query";				#MySQL Query Object
$Config->auto_loads["library"][]= "aephp.database.table";				#MySQL Table Object

$Config->auto_loads["library"][]= "aephp.application.user";				#User Login and Validations
$Config->auto_loads["library"][]= "aephp.database.paginator";			#Data Paginator Object
$Config->auto_loads["library"][]= "aephp.mail.mailer";					#Email Plugin
$Config->auto_loads["library"][]= "aephp.mail.template";				#Email Template Plugin

$Config->auto_loads["library"][]= "aephp.sms.sms";						#SMS Plugin

//enable or disable helpers from library folder
$Config->auto_loads["helper"][]= "aephp.environment.request";			#$_REQUEST Helper
$Config->auto_loads["helper"][]= "aephp.environment.get";				#$_GET Helper
$Config->auto_loads["helper"][]= "aephp.environment.post";				#$_POST Helper
$Config->auto_loads["helper"][]= "aephp.environment.cookie";			#COOKIE Helper
$Config->auto_loads["helper"][]= "aephp.application.content";			#Content Helper
#$Config->auto_loads["helper"][]= "aephp.application.template";			#Template Helper

//enable or disable scripts from asset folder
$Config->auto_loads["script"][]= "scripts.jquery.jquery-cookie";			#jQuery Cookie Library
$Config->auto_loads["script"][]= "scripts.jquery-ui.jquery-ui-min";			#jQuery UI Library

//CSS Styles from asset folder
$Config->auto_loads["css"][]= "scripts.jquery-ui.jquery-ui-base";		#jQuery UI Library Styles

#$Config->auto_loads["script_files"][]= "/assets/scripts/some_script.js";
#$Config->auto_loads["css"][]= "scripts.jcrop.jquery-jcrop";
#$Config->auto_loads["css"][]= "scripts.jcrop.style";


#$Config->page_catgs["pagename"]= "template_name";
$Config->auto_loads["php"][]= "assets.php.general";			#General Modules

aeApp::getApp()->addScript("var BASE_PATH= '".aeURI::base()."';", "text");
aeApp::getApp()->addScript(aeURI::base()."assets/scripts/application.js", "file");