<?php
defined("aeAPP") or die("Restricted Access");
define("LOG_FILE", PATH_ROOT.DS."temp".DS."app.error");
class aeSTDClass {
	public $_errorNum= 0;
	public $_errorMsg= "";
	var $_log_errors= true;
	var $_LOG_FILE= LOG_FILE;

	function __construct() {
		$this->resetError();
	}
	
	function getError() {
		return ($this->_errorNum > 0);
	}
	function getErrorMsg() {
		return ($this->_errorNum."::".$this->_errorMsg);
	}
	function setError($errorNo, $error) {
		$this->_errorNum= $errorNo;
		$this->_errorMsg= $error;
		$this->logError();
	}
	function setErrorO($obj) {
		$this->setError($obj->_errorNum, $obj->_errorMsg);
	}
	function logError() {
		if( ($this->_errorNum==0) && ($this->_errorMsg=="") ) {
			return true;
		}
		if($this->_log_errors) {
			$fp= fopen($this->_LOG_FILE,"a");
			fwrite($fp, date("Y-m-d g:i:a")." :: ".$this->_errorNum." :: ".$this->_errorMsg."\n");
			fclose($fp);
		}
	}
	function resetError() {
		$this->_errorNum= 0;
		$this->_errorMsg= "";
	}
}

if(!function_exists('echopre')) {
	function echopre($vartoecho, $dump = false, $die = false, $lang = 'php', $lines = 'true') {
		if($die) { ob_clean(); }
		echo ' <pre class="prelines" lang="'.$lang.'" lines="'.$lines.'">';
		if($dump) { var_dump($vartoecho); }
		else { echo $vartoecho; }
		echo "</pre>";
		if($die) { exit(0); }
	}
}
