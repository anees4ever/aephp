<?php
defined("aeAPP") or die("Restricted Access");
/**
 * aeQuery - PHP MySQL Table Interface class
 * NOTE: Requires PHP version 5 or later
 * @package aePHP
 * @author Muhammed Anees C.A
 * @copyright 2012 - 2015 Muhammed Anees C.A
 * @version $Id: mysql.php 23 2013-09-26 13:34:33Z a4e $
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
class aeQuery extends aeMySQL {
	/**
	* Table name
	* @var string
	*/
	protected $_tablename;
	/**
	* Query Type (SELECT|INSERT|REPLACE|UPDATE|DELETE)
	* @var string
	*/
	protected $_query_type;
	/**
	* Collection of fields as array
	* @var array
	*/
	protected $_fields;
	/**
	* Collection of field values as array
	* @var array
	*/
	protected $_values;
	/**
	* Collection of fields and values to update as associative array
	* @var array
	*/
	protected $_updates;
	/**
	* Collection of Table Joins and Relations as array
	* @var array
	*/
	protected $_joins;
	/**
	* Where condition for query
	* @var string
	*/
	protected $_where;
	/**
	* Group by field
	* @var string
	*/
	protected $_groupby;
	/**
	* Having condition for query
	* @var string
	*/
	protected $_havings;
	/**
	* Order by field name
	* @var string
	*/
	protected $_orderby;
	/**
	* Order by mode (ASC|DESC)
	* @var string
	*/
	protected $_order;
	/**
	* Limit Length(-1 for no limit)
	* @var int
	*/
	protected $_limit;
	/**
	* Limit Offset(-1 for no limit)
	* @var int
	*/
	protected $_limitStart;
	/**
	* MySQL result data as associative array
	* @var array
	*/
	protected $_data;
	/**
	* Constructor
	* @access public
	* @return object instance
	*/
	function __construct() {
		$this->getConnected();
		$this->resetQuery();
		$this->_data= false;
		return $this;
	}
	/**
	* Reset query properties
	* @return self
	*/
	public function resetQuery() {
		$this->_query_type= '';
		$this->_fields= array();
		$this->_values= array();
		$this->_updates= array();
		$this->_joins= '';
		$this->_where= '';
		$this->_groupby= '';
		$this->_havings= '';
		$this->_orderby= '';
		$this->_order= '';
		$this->_limit= -1;
		$this->_limitStart= -1;
		return $this;
	}
	/**
	* Set fields for SELECT Query
	* Note: arguiments can be given as a single array like select(array("field1","field2","field3")) or
	* 		like select("field1","field2","field3")
	* @return self
	*/
	public function select() {
		$this->_query_type= 'SELECT';
		$this->_fields= array();
		$args_cnt= func_num_args();
		for($I=0; $I<$args_cnt;$I++) {
			if(is_string(func_get_arg($I))) {
				$this->_fields[]= func_get_arg($I);
			} else if(is_array(func_get_arg($I))) {
				foreach(func_get_arg($I) as $idx => $field) {
					$this->_fields[]= func_get_arg($field);
				}
			}
		}
		return $this;
	}
	/**
	* Set table name for INSERT Query
	* @param string $_tblname
	* @return self
	*/
	public function insert($_tblname) {
		$this->_query_type= 'INSERT';
		$this->_tablename= $_tblname;
		return $this;
	}
	/**
	* Set table name for REPLACE Query
	* @param string $_tblname
	* @return self
	*/
	public function replace($_tblname) {
		$this->_query_type= 'REPLACE';
		$this->_tablename= $_tblname;
		return $this;
	}
	/**
	* Set fields for INSERT or REPLACE Query
	* Note: arguiments can be given as a single array like select(array("field1","field2","field3")) or
	* 		like select("field1","field2","field3")
	* @return self
	*/
	public function fields() {
		$this->_fields= array();
		$args_cnt= func_num_args();
		if(is_string(func_get_arg(0)) && ($args_cnt>1)) {
			for($I=0; $I<$args_cnt;$I++) {
				if(is_string(func_get_arg($I))) {
					$this->_fields[]= func_get_arg($I);
				}
			}
		} else {
			if(is_array(func_get_arg(0))) {
				$this->_fields= func_get_arg(0);
			}
		}
		return $this;
	}
	/**
	* Set fields for INSERT or REPLACE Query
	* Note: arguiments can be given as a single array like select(array("value1","value2","value3")) or
	* 		like select("value1","value2","value3")
	* @return self
	*/
	public function values() {
		$this->_values= array();
		$args_cnt= func_num_args();
		if(is_string(func_get_arg(0)) && ($args_cnt>1)) {
			for($I=0; $I<$args_cnt;$I++) {
				if(is_string(func_get_arg($I))) {
					$this->_values[]= func_get_arg($I);
				}
			}
		} else {
			if(is_array(func_get_arg(0))) {
				$this->_values= func_get_arg(0);
			}
		}
		return $this;
	}
	/**
	* Set table name for UPDATE Query
	* @param string $_tblname
	* @return self
	*/
	public function update($_tblname) {
		$this->_query_type= 'UPDATE';
		$this->_tablename= $_tblname;
		return $this;
	}
	/**
	* Set fields for INSERT or REPLACE Query
	* Note: arguiments can be given as an array like select(array("field1"=>"value1","field2"=>"value2","field3"=>"value3"))
	* @return self
	*/
	public function set() {
		$this->_updates= array();
		$args_cnt= func_num_args();
		if(is_string(func_get_arg(0)) && ($args_cnt>1)) {
			$this->_updates[func_get_arg(0)]= func_get_arg(1);
		} else {
			if(is_array(func_get_arg(0))) {
				$this->_updates= func_get_arg(0);
			}
		}
		return $this;
	}
	/**
	* Set table name for DELETE Query
	* @param string $_tblname
	* @return self
	*/
	public function delete($_tblname) {
		$this->_query_type= 'DELETE';
		$this->_tablename= $_tblname;
		return $this;
	}
	/**
	* Set table name for SELECT Query
	* @param string $_tblname
	* @return self
	*/
	public function from($_tblname) {
		$this->_tablename= $_tblname;
		return $this;
	}
	/**
	* Set the Condition
	* @param string $_condition
	* @return self
	*/
	public function where($_condition) {
		$this->_where= $_condition;
		return $this;
	}
	/**
	* Add a Join Table Record
	* @param string $_table
	* @param string $_field
	* @param string $_on_field
	* @param string $_operation
	* @param string $_mode
	* @return self
	*/
	public function join($_table, $_field, $_on_field, $_others = "", $_operation = "=", $_mode = "LEFT") {
		$this->_joins[]= array("table"=>$_table,
							  "field"=>$_field,
							  "on"=>$_on_field,
							  "operation"=>$_operation,
							  "mode"=>$_mode,
							  "others"=>$_others
		);
		return $this;
	}
	/**
	* Set the Groud by Field
	* @param string $_field
	* @return self
	*/
	public function groupby($_field) {
		$this->_groupby= $_field;
		return $this;
	}
	/**
	* Set the Having condition
	* @param string $_havings
	* @return self
	*/
	public function having($_havings) {
		$this->_havings= $_havings;
		return $this;
	}
	/**
	* Set the Order by fields and mode
	* @param string $_orderby
	* @param string $_order
	* @return self
	*/
	public function orderby($_orderby, $_order = 'asc') {
		$this->_orderby= $_orderby;
		$this->_order= $_order;
		return $this;
	}
	/**
	* Set the limit and limit offset
	* @param string $_limit
	* @param string $_limitStart
	* @return self
	*/
	public function limit($_limit, $_limitStart = -1) {
		$this->_limit= $_limit;
		$this->_limitStart= $_limitStart;
		return $this;
	}
	/**
	* Prepare Query and Execute. Return Result according to type.
	* Note: The parameter $withoutLimit is used only for SELECT query 
	* 		which will ignore the limit and limit offset.
	* @param string $withoutLimit
	* @return string|int|real if _query_type is SELECT and single field in query and single row returned
	* @return array if _query_type is SELECT and multiple rows returned
	* @return boolean true if _query_type is not SELECT and query success
	* @return boolean false if _query_type is not SELECT and query failed
	*/
	public function execute($withoutLimit = false) {
		$this->processSQL($withoutLimit);
		if($this->getError()) {
			$this->_data= false;
		} else {
			$cur= $this->query($this->_qry);
			if(!$this->getError()) {
				switch($this->_query_type) {
					case 'SELECT':
						if($this->numRows($cur) > 0) {
							if((mysqli_num_fields($cur)>1) || ($this->numRows($cur)>1)) {
								$this->_data= array();
								while($row= mysqli_fetch_assoc($cur)) {
									$this->_data[]= $row;
								}
							} else {
								$row= mysqli_fetch_row($cur);
								$this->_data= $row[0];
							}
						} else {
							$this->_data= false;
						}
					break;
					default:
						$this->_data= true;
					break;
				}
			} else {
				$this->_data= false;
			}
			if(is_resource($cur)) { mysqli_free_result($cur); }
			return $this->_data;
		}
	}
	/**
	* Process and Return MySQL String
	* @return string
	*/
	public function getSQL() {
		$this->processSQL();
		return $this->_qry;
	}
	/**
	* Process MySQL String
	* @return void
	*/
	private function processSQL($withoutLimit = false) {
		$this->_qry= '';
		$this->resetError();
		$tableName= $this->nameQuoted($this->_tablename);
		$where= $this->_where==""?"":"
WHERE {$this->_where}";
		$groupby= $this->_groupby==""?"":"
GROUP BY {$this->_groupby}";
		$having= $this->_havings==""?"":"
HAVING {$this->_havings}";
		$order= $this->_orderby==""?"":"
ORDER BY ".$this->nameQuoted($this->_orderby)." {$this->_order}";
		$limit= $withoutLimit?"":($this->_limit>=0?("
LIMIT ".($this->_limitStart>=0?"{$this->_limitStart},".($this->_limit-1):"{$this->_limit}")):"");

		switch($this->_query_type) {
			case 'SELECT':
				$fields= $this->processFieldStr();
				$joins= $this->processJoinStr();
				$this->_qry= "SELECT {$fields}
FROM {$tableName} {$joins} {$where} {$groupby} {$having} {$order} {$limit};";
			break;
			
			case 'INSERT':
			case 'REPLACE':
				$fields= $this->processFieldStr(false);
				$values= $this->processValueStr();
				$this->_qry= "{$this->_query_type} INTO {$tableName}
({$fields})
VALUES ({$values});";
			break;
			
			case 'UPDATE':
				$updates= $this->processUpdateStr();
				$this->_qry= "UPDATE {$tableName}
SET {$updates} {$where};";
			break;
			
			case 'DELETE':
				$updates= $this->processUpdateStr();
				$table= explode(" AS ", $tableName);
				$table= explode(" ", $tableName);
				$this->_qry= "DELETE FROM {$table[0]} {$where};";
			break;
			
			default:
				$this->_qry= 'QUERY PROPERTIES UNDEFINED';
				$this->setError(1, 'QUERY PROPERTIES UNDEFINED');
			break;
		}
	}
	/**
	* Process and return fields for SELECT | INSERT | REPLACE query
	* @param boolean $addStar true add a * field if _fields is empty
	* @return string
	*/
	private function processFieldStr($addStar = true){
		if(is_array($this->_fields) && (count($this->_fields)>0)) {
			$fieldStr= '';
			foreach($this->_fields as $field => $fieldData) {
				$fieldStr.= ($fieldStr==""?"":", ").$this->nameQuoted($fieldData);
			}
		} else {
			$fieldStr= $addStar?'*':'';
		}
		return $fieldStr;
	}
	/**
	* Process and return values for INSERT | REPLACE query
	* @return string
	*/
	private function processValueStr(){
		if(is_array($this->_values) && (count($this->_values)>0)) {
			$valueStr= '';
			foreach($this->_values as $value) {
				$valueStr.= ($valueStr==""?"":", ").$this->Quote($value);
			}
		} else {
			$valueStr= '';
		}
		return $valueStr;
	}
	/**
	* Process and return fields and values for UPDATE query
	* @return string
	*/
	private function processUpdateStr(){
		if(is_array($this->_updates) && (count($this->_updates)>0)) {
			$updateStr= '';
			foreach($this->_updates as $idx => $data) {
				$updateStr.= ($updateStr==""?"":", ").$this->nameQuoted($idx)."=".$this->Quote($data);
			}
		} else {
			$updateStr= '';
		}
		return $updateStr;
	}
	/**
	* Process and return JOIN query
	* @return string
	*/
	private function processJoinStr(){
		$joinStr= "";
		if(is_array($this->_joins) && (count($this->_joins)>0)) {
			foreach($this->_joins as $idx => $aJoin) {
				$joinStr.= "
	".$aJoin["mode"]." JOIN ".$this->nameQuoted($aJoin["table"]).
						" ON (".$this->nameQuoted($aJoin["field"]).$aJoin["operation"].$this->nameQuoted($aJoin["on"])." {$aJoin["others"]})";
			}
		}
		return $joinStr;
	}
}