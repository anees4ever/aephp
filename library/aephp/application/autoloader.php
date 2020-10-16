<?php
defined("aeAPP") or die("Restricted Access");
class aeAutoLoader extends aeObject {
	static function makePath(&$file, $initial_path, $post_fix, $sep = DS) {
		$file= $initial_path . $sep . str_replace(".", $sep, $file) . $post_fix;
	}
	static function requires($file, $filename) {
		if(file_exists($file)) {
			require_once($file);
		} else {
			die("Required Library $filename not found...!");
		}
	}
	static function includes($file, $filename) {
		if(file_exists($file)) {
			include_once($file);
		} else {
			die("Required Library $filename not found...!");
		}
	}
	static function requireFile($file, $initial_path) {
		$filename= $file;
		aeAutoLoader::makePath($file, $initial_path, ".php");
		aeAutoLoader::requires($file, $filename);
	}
	static function includeFile($file, $initial_path) {
		$filename= $file;
		aeAutoLoader::makePath($file, $initial_path, ".php");
		aeAutoLoader::includes($file, $filename);
	}
	static function scriptFile($file, $initial_path) {
		$filename= $file;
		aeAutoLoader::makePath($file, $initial_path, ".js", "/");
		aeApp::getApp()->addScript($file, "file");
	}
	static function cssFile($file, $initial_path) {
		$filename= $file;
		aeAutoLoader::makePath($file, $initial_path, ".css", "/");
		aeApp::getApp()->addCSS($file);
	}
	static function loadLibraries() {//loadPlugins
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["library"]))
		foreach($Config->auto_loads["library"] as $id => $library) {
			switch($library) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::requireFile($library, PATH_LIB, ".php");
				break;
			}
		}
	}
	static function loadHelpers() {
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["helper"]))
		foreach($Config->auto_loads["helper"] as $id => $helper) {
			switch($helper) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::requireFile($helper, PATH_LIB, ".php");
				break;
			}
		}
	}
	static function loadContentHelper() {
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["content"]))
		foreach($Config->auto_loads["content"] as $id => $content) {
			switch($content) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::includeFile($content, PATH_LIB, ".php");
				break;
			}
		}
	}
	static function loadIncludes() {
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["php"]))
		foreach($Config->auto_loads["php"] as $id => $inc) {
			switch($inc) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::includeFile($inc, PATH_ROOT, ".php");
				break;
			}
		}
	}
	static function loadScripts() {
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["script"]))
		foreach($Config->auto_loads["script"] as $id => $script) {
			switch($script) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::scriptFile($script, aeURI::base(true)."/assets");
				break;
			}
		}
		
		if(isset($Config->auto_loads["script_files"]))
		foreach($Config->auto_loads["script_files"] as $id => $script) {
			switch($script) {
				//TO DO exceptional files can be included here
				default:
					aeApp::getApp()->addScript(aeURI::base(true)."/".$script, "file");
				break;
			}
		}
	}
	static function loadCSSs() {
		$Config= aeApp::getConfig();
		if(isset($Config->auto_loads["css"]))
		foreach($Config->auto_loads["css"] as $id => $css) {
			switch($css) {
				//TO DO exceptional files can be included here
				default:
					aeAutoLoader::cssFile($css, aeURI::base(true)."/assets");
				break;
			}
		}
	}
}
