<?php

define("aeAPP", 1);
define('DS', DIRECTORY_SEPARATOR);
define('PATH_ROOT', dirname(__FILE__));

require_once(PATH_ROOT . DS . "includes" . DS . "config.php");

$application_cnf->startApplication();

?>