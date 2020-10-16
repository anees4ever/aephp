<?php
defined("aeAPP") or die("Restricted Access");

class aeTable extends aeQuery {
	var $_tbl= '';
	var $_tbl_key= '';
	var $_db= null;
	var $_fieldInfo= array();
	function __construct($table, $key, $db) {
		$this->_tbl= $table;
		$this->_tbl_key= $key;
		$this->_db= $db;
		$this->_con= $this->_db->_con;
		$this->reset(true);
	}
	function reset($fullreset = false) {
		$key= $this->_tbl_key;
		if($fullreset) {
			$fields= $this->_db->getTableFields($this->_tbl);
			$this->_fieldInfo= array();
			foreach($fields[$this->_tbl] as $afield => $type) {
				$this->_fieldInfo[strtolower($afield)]= array("name"=>$afield,"type"=>$type,"default"=>$this->_db->fieldDefaultValue($type));
				$this->setProperty(strtolower($afield), $this->_fieldInfo[$afield]["default"]);
			}
		}
		foreach($this->getProperties() as $name => $value) {
			if(isset($this->_fieldInfo[$name])) {
				$this->$name= $this->_fieldInfo[$name]["default"];
			}
		}
	}
	function bind($from, $ignore=array()) {
		$fromArray= is_array($from);
		$fromObject= is_object($from);

		if (!$fromArray && !$fromObject) {
			$this->setError(1, get_class($this).' bind failed. Invalid from argument');
			return false;
		}
		if (!is_array($ignore)) {
			$ignore= explode(' ', $ignore);
		}
		foreach ($this->getProperties() as $key => $value) {
			//internal attributes of an object are ignored
			if (!in_array($key, $ignore)) {
				$keyEx= $this->_fieldInfo[$key]["name"];
				if ($fromArray && isset($from[$keyEx])) {
					$this->$key= $from[$keyEx];
				} else if ($fromObject && isset($from->$keyEx)) {
					$this->$key= $from->$keyEx;
				}
			}
		}
		return true;
	}
	function load($oid=null){
		$key= $this->_tbl_key;

		if($oid !== null) {
			$this->$key= $oid;
		}

		$oid= $this->$key;

		if ($oid === null) {
			return false;
		}
		$this->reset();

		$db= &$this->_db;

		$query= 'SELECT *'
		. ' FROM '.$this->_tbl
		. ' WHERE '.$this->_tbl_key.' = '.$db->Quote($oid);
		$db->setQuery($query);
		
		if($result= $db->loadAssoc()) {
			return $this->bind($result);
		}
		else {
			$this->setErrorO($db);
			return false;
		}
	}
	function store($key_val = 0,$updateNulls=false) {
		$key= $this->_tbl_key;
		$val= intval($this->$key);
		if($val>0) {
			$ret= $this->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
		}
		else {
			$ret= $this->insertObject($this->_tbl, $this, $this->_tbl_key, $key_val);
		}
		if(!$ret) {
			$this->setErrorO($this->_db);
			return false;
		}
		else {
			return true;
		}
	}
	function canDelete($oid=null, $joins=null ) {
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		if (is_array( $joins ))
		{
			$select = "$k";
			$join = "";
			foreach( $joins as $table )
			{
				$select .= ', COUNT(DISTINCT '.$table['idfield'].') AS '.$table['idfield'];
				$join .= ' LEFT JOIN '.$table['name'].' ON '.$table['joinfield'].' = '.$k;
			}

			$query = 'SELECT '. $select
			. ' FROM '. $this->_tbl
			. $join
			. ' WHERE '. $k .' = '. $this->_db->Quote($this->$k)
			. ' GROUP BY '. $k
			;
			$this->_db->setQuery( $query );

			if (!$obj = $this->_db->loadObject())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$msg = array();
			$i = 0;
			foreach( $joins as $table )
			{
				$k = $table['idfield'] . $i;
				if ($obj->$k)
				{
					$msg[] = $table['label'];
				}
				$i++;
			}

			if (count( $msg ))
			{
				$this->setError(0, "noDeleteRecord" . ": " . implode( ', ', $msg ));
				return false;
			}
			else
			{
				return true;
			}
		}

		return true;
	}
	function delete($oid=null) {
		//if (!$this->canDelete( $msg ))
		//{
		//	return $msg;
		//}
		$key= $this->_tbl_key;
		if ($oid) {
			$this->$key= intval($oid);
		}

		$query = 'DELETE FROM '.$this->_db->nameQuote($this->_tbl).
				' WHERE '.$this->_tbl_key.' = '. $this->_db->Quote($this->$key);
		$this->_db->setQuery( $query );

		if ($this->_db->query()) {
			return true;
		}
		else {
			$this->setErrorO($this->_db);
			return false;
		}
	}
	function toXML($mapKeysToText=false) {
		$xml= '<record table="' . $this->_tbl . '"';

		if ($mapKeysToText) {
			$xml.= ' mapkeystotext="true"';
		}
		$xml.= '>';
		foreach(get_object_vars($this) as $key => $val) {
			if(is_array($val) or is_object($val) or $val === NULL) {
				continue;
			}
			if ($key[0] == '_') { // internal field
				continue;
			}
			$xml.= '<' . $key . '><![CDATA[' . $val . ']]></' . $key . '>';
		}
		$xml.= '</record>';
		return $xml;
	}
	function insertObject($table, &$object, $keyName = NULL, $key_val = 0) {
		$fmtsql= 'INSERT INTO '.$this->_db->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		$fields= array();
		$values= array();
		foreach(get_object_vars($object) as $key => $val) {
			if (is_array($val) or is_object($val) or $val === NULL) {
				continue;
			}
			if ($key[0] == '_') { // internal field
				continue;
			}
			$fields[]= $this->_db->nameQuote($key);
			$values[]= (($key==$keyName) && ((int)$key_val>0))?(int)$key_val
						:($this->_db->isQuoted($this->_fieldInfo[$key])?$this->_db->Quote($val):(int)$val);
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $fields) , implode(",",$values)));
		if (!$this->_db->query()) {
			return false;
		}
		$id= $this->_db->getLID();
		if($keyName && $id) {
			$object->$keyName= $id;
		}
		return true;
	}
	function updateObject($table, &$object, $keyName, $updateNulls=true){
		$fmtsql= 'UPDATE '.$this->_db->nameQuote($table).' SET %s WHERE %s';
		$tmp= array();
		foreach(get_object_vars($object) as $key => $val) {
			if(is_array($val) or is_object($val) or $key[0] == '_') { // internal or NA field
				continue;
			}
			if($key == $keyName) { // PK not to be updated
				$where= $keyName . '=' . $this->_db->Quote($val);
				continue;
			}
			if($val === null) {
				if ($updateNulls) {
					$val= 'NULL';
				} else {
					continue;
				}
			} else {
				$val= $this->_db->isQuoted($this->_fieldInfo[$key])?$this->_db->Quote($val):(int)$val;
			}
			$tmp[]= $this->_db->nameQuote($key) . '=' . $val;
		}
		$this->_db->setQuery(sprintf($fmtsql, implode(",", $tmp), $where));
		return $this->_db->query();
	}
}

?>
