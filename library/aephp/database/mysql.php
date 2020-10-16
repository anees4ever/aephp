<?php
defined("aeAPP") or die("Restricted Access");
/**
 * aeMySQL - PHP MySQL Interface class
 * NOTE: Requires PHP version 5 or later
 * @package aePHP
 * @author Muhammed Anees C.A
 * @copyright 2012 - 2015 Muhammed Anees C.A
 * @version $Id: mysql.php 23 2013-09-26 13:34:33Z a4e $
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
class aeMySQL extends aeObject {
	/**
	* MySQL Connection Reference
	* @var resource
	* @access private
	*/
	var $_con= null;
	/**
	* Database name prefix to be replaced
	* @var string
	* @access private
	*/
	var $_dbprefix= "";
	/**
	* Mysql query assigned
	* @var string
	* @access private
	*/
	var $_qry= null;
	/**
	* Mysql query result
	* @var resource
	* @access private
	*/
	var $_resource= null;
	/**
	* List of Keys to avoid Quotes
	* @var array
	* @access private
	*/
	var $_noQuotes= array('now()', 'current_timestamp()');
	/**
	* Return an Instance of aeMySQL
	* @access public
	* @return object instance
	*/

	static $mysql_instance;
	public static function getInstance() {
		if(!is_object(aeMySQL::$mysql_instance)) {
			aeMySQL::$mysql_instance= new aeMySQL();
			aeMySQL::$mysql_instance->getConnected(true);
		}
		return aeMySQL::$mysql_instance;
	}
	/**
	* Constructor
	* @access public
	* @return object instance
	*/
	function __construct() {
		return $this;
	}
	/**
	* Prepare and Connect to mysql
	* @access public
	* @return boolean true on success, false if failed
	*/
	function getConnected($force= false) {
		if(is_object(aeMySQL::$mysql_instance)) {
			if(!$force) {
				$this->_con= aeMySQL::$mysql_instance->_con;
				$this->_dbprefix= aeMySQL::$mysql_instance->_dbprefix;
				return true;
			}
		} else {
			$db= aeMySQL::getInstance();
			$this->_con= $db->_con;
			$this->_dbprefix= $db->_dbprefix;
			return true;
		}

		$config= aeApp::getConfig();
		if(is_object($config)) {
			if($this->connect($config) !== false) {
				if($this->selectdb($config->db) !== false) {
					$this->_con->set_charset("utf8");
					return $this;
				} else { return false; }
			}
			else { return false; }
		}
		else {
			$this->setError(-1, "Invalid Database Configurations");
			return false;
		}
	}

	function reConnect() {
		$db= aeMySQL::getInstance();
		mysqli_close($db->_con);
		unset($this->_con);
		$db->getConnected(true);
	}
	/**
	* Connect to mysql
	* @param object $options mysql preperties object
	* @access private
	* @return boolean true on success, false if failed
	*/
	function connect($options) {
		$this->_dbprefix= $options->dbprefix;
		$this->_con= mysqli_connect($options->host, $options->user, $options->password);
		if($this->_con !== false) { return true; }
		else {
			$this->setError(mysqli_connect_errno(), mysqli_connect_error());
			return false;
		}
	}
	/**
	* Select a database
	* @param string $database database name
	* @access private
	* @return boolean true on success, false if failed
	*/
	function selectdb($database) {
		$res= mysqli_select_db($this->_con, $database);
		if($res !== false) {
			$this->query("SET SESSION time_zone = '+05:30'");
			return true;
		} else {
			$this->setError(mysqli_errno($this->_con), mysqli_error($this->_con));
			return false;
		}	
	}
	/**
	* Set a mysql query
	* @param string $query mysql query string
	* @access public
	* @return object $this
	*/
	function setQuery($query) {
		$this->_qry= $query;
		return $this;
	}
	/**
	* Get the mysql query
	* @access public
	* @return string $this->_qry
	*/
	function getQuery() {
		return $this->_qry;
	}
	/**
	* Begin a mysql transaction
	* @access public
	* @return boolean true on success, false if failed
	*/
	function begin() {
		$sql='start transaction';
		$res= $this->query($sql);
		if(!$this->getError()) { return true; }
		else { return false; }
	}
	/**
	* Commit a mysql transaction
	* @access public
	* @return boolean true on success, false if failed
	*/
	function commit()
	{
		$sql='commit';
		$res= $this->query($sql);
		if(!$this->getError()) { return true; }
		else { return false; }
	}
	/**
	* Rollback a mysql transaction
	* @access public
	* @return boolean true on success, false if failed
	*/
	function rollback()
	{
		$sql='rollback';
		$res= $this->query($sql);
		if(!$this->getError()) { return true; }
		else { return false; }
	}
	/**
	* Returns last inserted id
	* @param mysqli_resource $resource mysql result resource
	* @access public
	* @return integer last inserted id
	*/
	function getLID() {
		return mysqli_insert_id($this->_con);
	}
	/**
	* Return number of afftected rows
	* @param mysql_resource $resource mysql result resource
	* @access public
	* @return integer number of afftected rows
	*/
	function affectedRows($resource=null) {
		return mysqli_affected_rows($resource!==null?$resource:$this->_resource);
	}
	/**
	* Return number of rows in a result
	* @param mysqli_resource $resource mysql result resource
	* @access public
	* @return integer number of rows in a result
	*/
	function numRows($resource=null) {
		return mysqli_num_rows($resource!==null?$resource:$this->_resource);
	}
	/**
	* Return result according to mode
	* @param string $query mysql query to execute
	* @param string $mode execution mode(result type)
	* @access private 
	* @return variant result according to mode
	*/
	function _loadResult($query, $mode = 'assoc') {
		$result= false;
		$cur= $this->query($query);
		if(!$this->getError()) {
			if($this->numRows($cur) > 0) {
				switch($mode) {
					case 'result':
						$row= mysqli_fetch_row($cur);
						$result= $row[0];
					break;
					case 'assoc': 
						$result= mysqli_fetch_assoc($cur);
					break;
					case 'assoclist':
						$result= array();
						while($row= mysqli_fetch_assoc($cur)) {
							$result[]= $row;
						}
					break;
					case 'object': 
						$result= mysqli_fetch_object($cur);
					break;
					case 'objectlist':
						$result= array();
						while($row= mysqli_fetch_object($cur)) {
							$result[]= $row;
						}
					break;
					case 'array': 
						$result= mysqli_fetch_array($cur);
					break;
					case 'arraylist':
						$result= array();
						while($row= mysqli_fetch_assoc($cur)) {
							$result[]= $row;
						}
					break;
				}
			}
		}
		if(is_resource($cur)) { mysqli_free_result($cur); }
		return $result;
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return variant single field value from a query
	*/
	function loadResult($query = '') {
		return $this->_loadResult($query, 'result');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return array single row as associative array from a query
	*/
	function loadAssoc($query = '') {
		return $this->_loadResult($query, 'assoc');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return array all rows as associative array from a query
	*/
	function loadAssocList($query = '') {
		return $this->_loadResult($query, 'assoclist');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return object single row as associative array from a query
	*/
	function loadObject($query = '') {
		return $this->_loadResult($query, 'object');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return object all rows as associative array from a query
	*/
	function loadObjectList($query = '') {
		return $this->_loadResult($query, 'objectlist');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return array single row as associative and or indexed array from a query
	*/
	function loadArray($query = '') {
		return $this->_loadResult($query, 'array');
	}
	/**
	* Execute Query and Return Result
	* @param string $query
	* @access public
	* @return array all rows as associative and or indexed array from a query
	*/
	function loadArrayList($query = '') {
		return $this->_loadResult($query, 'arraylist');
	}
	/**
	* Execute Query and Return Result Resource
	* @param string $query
	* @access public
	* @return resource
	*/
	function query($query = '', $virtual_error_count= 0) {
		$this->_qry= ($query=='')?$this->_qry:$query;
		$this->remPrefix();
		$this->resetError();
		$this->_resource= mysqli_query($this->_con, $this->_qry);
		if(!$this->_resource) {
			$errorNo= mysqli_errno($this->_con);
			$errorMsg= mysqli_error($this->_con);
			//handle some known errors here.... :)
			if($virtual_error_count < 10) {
				switch($errorNo) {
					case 2013://Lost connection to MySQL server during query.
					case 2006://MySQL server has gone away.
						$this->logAnError($errorNo, $errorMsg." [ec{$virtual_error_count}]");
						$this->reConnect();
						$this->query($query, $virtual_error_count+1);
					break;
				}
			}
			$this->setError($errorNo, $errorMsg.".\r\nSQL=".$this->_qry);
			return false;
		} else {
			return $this->_resource;
		}
	}
	/**
	* Get table fields and field types
	* @param array $tables table names to fetch fields
	* @param boolean $typeonly return only type of fields if set to true
	* @access public
	* @return array
	*/
	function getTableFields($tables, $typeonly = true) {
		settype($tables, 'array');//force to array
		$result = array();
		foreach($tables as $tblval) {
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();

			if($typeonly) {
				foreach ($fields as $field) {
					$result[$tblval][$field->Field]= preg_replace("/[(0-9)]/",'', $field->Type );
				}
			}
			else {
				foreach ($fields as $field) {
					$result[$tblval][$field->Field]= $field;
				}
			}
		}
		return $result;
	}
	/**
	* Get Default value of the field by its type
	* @param string $typ type of field
	* @access public
	* @return variant of default values
	*/
	function fieldDefaultValue($typ) {
		switch(strtolower($typ)) {
			case "int": return(0); break;
			case "string": return(""); break;
			case "real": return(0); break;
			case "blob": return(""); break;
			case "date": return("0000-00-00"); break;
			case "time": return("00:00:00"); break;
			case "datetime": return("0000-00-00 00:00:00"); break;
			case "boolean": return(0); break;
			case "": default: return(""); break;
		}
	}
	/**
	* Remove table name prefix
	* @access private
	* @return void
	*/
	function remPrefix() {
		$this->_qry= str_replace("#__", $this->_dbprefix, $this->_qry);
	}
	/**
	* Quote fields with ``
	* @param string $text field name
	* @access protected
	* @return string quoted field name
	*/
	function nameQuote($text) {
		$text= $text=='*'?$text:'`'.$text.'`';
		return str_replace(' AS ', '` AS `', $text);
	}
	/**
	* Quote fields with `` for alised fields
	* @param string $text field name like (db.table.field)
	* @param string $sep field seperator
	* @access protected
	* @return string quoted field name
	*/
	function nameQuoted($text, $sep = '.') {
		if((strpos($text, '(') >= 0) || (strpos($text, "'") >= 0)) {
			return $text;
		}
		$str= explode($sep, $text);
		if(is_array($str) && (count($str)==2)) {
			$str[0]= $this->nameQuote($str[0]);
			$str[1]= $this->nameQuote($str[1]);
			$text= implode($sep, $str);
		} else {
			if($sep==' ') {
				$text= $this->nameQuote($text);
			} else {
				$text= $this->nameQuoted($text, ' ');
			}
		}
		return $text;
	}
	/**
	* Quote values with ''
	* @param string $text field value
	* @param boolean $escaped whether to use escape string
	* @access protected
	* @return string quoted field value
	*/
	function Quote($text, $escaped = true) {
    if(array_search(strtolower($text), $this->_noQuotes)!==false) {
			return $text;
		} else {
			return '\''.($escaped?$this->getEscaped($text):$text).'\'';
		}
	}
	/**
	* Checks whether a field type require quoting
	* @param string $type field type
	* @access protected
	* @return boolean true on success, false if failed
	*/
	function isQuoted($type) {
		return (!in_array($type, array("int")));
	}
	/**
	* Returns an escaped text
	* @param string $text text to escape
	* @param boolean $extra whether to use C style escaping
	* @access private
	* @return string escaped text
	*/
	function getEscaped($text, $extra = false) {
		$fx= function_exists("mysqli_real_escape_string")?"mysqli_real_escape_string":"mysqli_escape_string";
		$result = $fx(aeApp::getDB()->_con, $text);
		if ($extra) {
			$result= addcslashes($result, '%_');
		}
		return $result;
	}
}

?>
