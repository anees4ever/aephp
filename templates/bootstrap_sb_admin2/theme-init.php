<?php
defined("aeAPP") or die("Restricted Access");
$themeFolder= "bootstrap_sb_admin2";
require_once(PATH_TMPL.DS."$themeFolder".DS."theme".DS."required.php");

ThemeSettings::$templateDir= PATH_TMPL.DS.$themeFolder;
ThemeSettings::$templateUri= ThemeSettings::$templateUri= aeURI::base()."templates/".$themeFolder;

$theme= ThemeSbAdmin::getTheme();
$theme->viewMode= aeRequest::getVar("ae_view","template");//content
