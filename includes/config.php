<?php
defined("aeAPP") or die("Restricted Access");

ob_start();

class aeConfig {
	static $app_version= 60;
	var $debug=0;//debug mode- 1 - output errors & warnings to screen, 0- no errors & warnings
	var $session_name= 'aephp';
	var $session_timeout= 1800;//in seconds
	var $single_session= true;//single login session for users
	var $tmpl_name = 'bootstrap_sb_admin2';//Default template name - see constructor of lib/php/app/app.php
	var $sef_sufix = '.html';//sufix to be removed from the URI(domain/login.html => domain/index.php?cmd=login)
	var $pagefile_sufix = '.pg';//sufix for page files saved inside /pages/
    var $default_page = 'index';//default page(without sufix)
	
	var $site_down = 0;//set to 1 to de-activate the website
	var $down_message = 'The site is down for Internal Maintenance. Please check back later. <br/>Thank you for visiting here...!';
	
	var $host = 'localhost';
	var $user = 'root';
	var $db = 'aephp';
	var $dbprefix = 'ae_';//database prefix for tables(ie. table `ae_login` can be used like `#_login`)
	var $password = '';
    
    var $user_activation = 1;//require user activation
	
	var $sitename = 'aePHP';
	var $metaAuthor = 'Anees4Ever';
	var $metaRobots= "index, follow";
	var $metaGenerator= "aePHP - An easy PHP framework";
	var $metaKeys= "";
	var $metaDesc= "";
	
	var $sms_engine= "filesms";
    //default mail from name and address - see lib/php/mail/mailer.php
	var $mailer_engine= "phpmailer";//phpmailer|phpmail|filemail

    var $mail_from_name= "YourDomain.com";
    var $mail_from_mail= "You@YourDomain.com";

	var $mail_admin_name= "Site Administrator";
    var $mail_admin_mail= "You@YourDomain.com";

    //SMTP Settings
	var $smtp_adrs= 'localhost';
	var $mail_port= 25;//465;
    var $smtp_user= 'You@YourDomain.com';
	var $smtp_pass= 'yourpassword';
	
	//Leave Rest of the Code Unchanged========================================
    var $page_catgs= array();
	var $auto_loads= array();
    public function __construct() {
        $this->page_catgs= array();//pagename=template
		$this->auto_loads= array();//plugin|helper|script
    }
	static function getInstance() {
		static $conf_instance;
		if(!is_object($conf_instance)) {
			$conf_instance= new aeConfig();
		}
		return $conf_instance;
	}
	function initializePaths() {
		define("PATH_ASSET",	PATH_ROOT . DS . 'assets');
		define("PATH_LIB",		PATH_ROOT . DS . 'library');
		define("PATH_AEPHP",	PATH_ROOT . DS . 'library' . DS . 'aephp');
		define("PATH_INC",		PATH_ROOT . DS . 'includes');
		define("PATH_PGS",		PATH_ROOT . DS . 'pages');
		define("PATH_TMPL",		PATH_ROOT . DS . 'templates');
		define("PATH_TEMP",		PATH_ROOT . DS . 'temp');
		define("LB", "\r\n");
	}
	function includeEssentials() {
		//Essential Files
		require_once(PATH_AEPHP.DS."application".DS."stdbase.php");
		require_once(PATH_AEPHP.DS."application".DS."object.php");
		require_once(PATH_AEPHP.DS."application".DS."application.php");
		require_once(PATH_AEPHP.DS."environment".DS."session.php");
		require_once(PATH_AEPHP.DS."application".DS."autoloader.php");
		require_once(PATH_AEPHP.DS."application".DS."uri.php");
		//For Autoloader Configuration
		require_once(PATH_INC.DS."autoloads.php");
		define("XHR_IMG", aeURI::base()."assets/images/dyna/5.gif");
		define("XHR_LOADS", '<img src="'.XHR_IMG.'">');
		define("APP_VERSION", aeConfig::$app_version);
	}
	function startApplication() {
		$application= aeApp::getApp();
		aeSession::start();
		if($this->debug == 0) {
			error_reporting(E_ALL ^ E_NOTICE);
			ini_set("error_reporting",E_ALL ^ E_NOTICE);
			ini_set("display_errors","0");
		} else {
			error_reporting(E_ALL);
			ini_set("error_reporting",E_ALL);
			ini_set("display_errors","1");
		}
		//TimeZone Fix
		ini_set("date.timezone", "Asia/Kolkata");
		$timezone = "Asia/Kolkata";
		if(function_exists('date_default_timezone_set')){
		    date_default_timezone_set($timezone); 
		}
		//Form Input Fix
		ini_set("max_input_vars", "10000");
		$application->render();
	}
}
 
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
	$_REQUEST["xhr"]= $_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest"?"yes":"no";
}
if((isset($_REQUEST["xhr"])) && ($_REQUEST["xhr"] !== "") && ($_REQUEST["xhr"] !== "no")) {
	define("aeXHR", true);
} else {
	define("aeXHR", false);
}

$application_cnf= aeConfig::getInstance();
$application_cnf->initializePaths();
$application_cnf->includeEssentials();