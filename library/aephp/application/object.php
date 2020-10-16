<?php
defined("aeAPP") or die("Restricted Access");
class aeObject extends aeSTDClass {
	function __construct() {
		
	}
	function getProperty($property, $default=null) {
		if(isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}
	function getProperties($public = true) {
		$vars= get_object_vars($this);
        if($public) {
			foreach($vars as $key => $value) {
				if ('_' == substr($key, 0, 1)) {
					unset($vars[$key]);
				}
			}
		}
        return $vars;
	}
	function setProperty($property, $value = null) {
		$this->$property= $value;
		return $this;
	}
	function setProperties($properties) {
		$properties=(array)$properties;
		if (is_array($properties)) {
			foreach ($properties as $key => $val) {
				$this->$key = $val;
			}
			return $this;
		}
		return $this;
	}
	function toString() {
		return get_class($this);
	}
	function getPublicProperties()
	{
		return $this->getProperties();
	}
}
