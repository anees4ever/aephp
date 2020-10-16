<?php
defined("aeAPP") or die("Restricted Access");
/**
 * aePaginator - Data Pagination Interface class
 * NOTE: Requires PHP version 5 or later
 * @package aePHP
 * @author Muhammed Anees C.A
 * @copyright 2012 - 2015 Muhammed Anees C.A
 * @version $Id: mysql.php 23 2013-09-26 13:34:33Z a4e $
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */
class aePaginator extends aeQuery {
	/**
	* Paginator Daya Type (mysql/array)
	* @var string
	*/
	protected $_datatype;
	/**
	* Number of Current Page Records
	* @var int
	*/
	protected $_numRows;
	/**
	* Number of Total Records
	* @var int
	*/
	protected $_numRecords;
	/**
	* Starting Number of Records
	* @var int
	*/
	protected $_recStart;
	/**
	* Ending Number of Records
	* @var int
	*/
	protected $_recStop;
	/**
	* Current Page Number
	* @var int
	*/
	protected $_currentPage;
	/**
	* Navigate to Page
	* @var int
	*/
	protected $_navPage;
	/**
	* Maximum Number of Pages
	* @var int
	*/
	protected $_maxPages;
	/**
	* Status Message array
	* @var array
	*/
	protected $_status;
	/**
	* Constructor
	* @param string $datatype
	* @return object instance
	*/
	function __construct($datatype = 'mysql') {
		if($datatype=='mysql') {
			parent::__construct();
		}
		$this->_datatype= $datatype;
		$this->_numRows= 0;
		$this->_numRecords= 0;
		$this->_recStart= 0;
		$this->_recStop= 0;
		$this->_currentPage= 0;
		$this->_navPage= 0;
		$this->_maxPages= 0;
		$this->_status= array("large"=>"{start} to {end} of {total}, Page {page}/{pages}",
							  "small"=>"{total} Records");
		return $this;
	}
	public function setPage($current, $navto) {
		$this->_currentPage= $current;
		$this->_navPage= $navto;
	}
	public function data() {
		if(func_num_args()==1) {
			$this->_data= func_get_arg(0);
			return $this;
		} else {
			return $this->_data;
		}
	}
	public function nav_exec() {
		$callee= "nav_process_".$this->_datatype;
		$this->$callee();
		if($this->_data !== false) {
			$this->process_nav();
		}
		$this->nav_process_status();
		return $this;
	}
	public function nav_status($large = true) {
		return $this->_status[$large?"large":"small"];
	}
	public function nav_info() {
		return array(
			"current"	=> $this->_numRows,
			"total"		=> $this->_numRecords,
			"start"		=> $this->_recStart,
			"end"		=> $this->_recStop,
			"page"		=> $this->_currentPage,
			"pages"		=> $this->_maxPages,
			"status"	=> $this->_status,
			"limit"		=> $this->_limit
		);
	}
	private function process_nav() {
		$data= is_array($this->_data)?$this->_data:array();
		$this->_numRecords= count($data);
		$this->_maxPages= $this->_limit==-1?1:
							(($this->_numRecords % $this->_limit) > 0
							?((int)($this->_numRecords/$this->_limit)) + 1
							:((int)($this->_numRecords/$this->_limit)));
		$this->_maxPages= $this->_maxPages==0?1:$this->_maxPages;
		switch($this->_navPage) {
			case 'f':
				$this->_currentPage= 1;
				break;
			case 'p':
				$this->_currentPage= ($this->_currentPage > 1)?($this->_currentPage-1):1;
				break;
			case 'n':
				$this->_currentPage= ($this->_currentPage == $this->_maxPages)?
											$this->_maxPages:($this->_currentPage+1);
				break;
			case 'l':
				$this->_currentPage= $this->_maxPages;
				break;
			default:
				$this->_currentPage= (intval($this->_navPage)) >$this->_maxPages?
											$this->_maxPages:intval($this->_navPage);
				break;
		}
		$this->_currentPage= intval(intval($this->_currentPage-1)<0?1:$this->_currentPage);
		$limitpage= ($this->_currentPage-1)*$this->_limit;
		$this->_limitStart= $this->_limit==-1?-1:$limitpage;
		
		$this->_numRows= 0;
		
		if($this->_limit>=0) {
			if($this->_limit < $this->_numRecords) {
				$this->_data= array();
				$start= $this->_limitStart<$this->_numRecords?$this->_limitStart:$this->_numRecords;
				$end= $this->_numRecords-$this->_limitStart<$this->_limit?$this->_numRecords-$this->_limitStart:$this->_limit;
				$this->_data= array_slice($data, $start, $end);
				/*for($i= 0; $i < $this->_limit; $i++) {
					if(isset($data[$i+$this->_limitStart])) {
						array_push($this->_data, $data[$i+$this->_limitStart]);
					}
				}*/
			}
		}
		$this->_numRows= count($this->_data);
		$this->_recStart= $this->_numRows==0?0:($this->_limit==-1?1:$this->_limitStart+1);
		$this->_recStop= $this->_recStart+$this->_numRows;
		$this->_recStop-= (($this->_limit==-1)||($this->_numRows<=$this->_limit))?1:0;
		$this->_recStop= $this->_numRows==0?0:$this->_recStop;
	}
	private function nav_process_status() {
		$this->_status["large"]= str_replace("{total}", $this->_numRecords, $this->_status["large"]);
		$this->_status["large"]= str_replace("{start}", $this->_recStart, $this->_status["large"]);
		$this->_status["large"]= str_replace("{end}", $this->_recStop, $this->_status["large"]);
		$this->_status["large"]= str_replace("{page}", $this->_currentPage, $this->_status["large"]);
		$this->_status["large"]= str_replace("{pages}", $this->_maxPages, $this->_status["large"]);
		
		$this->_status["small"]= str_replace("{total}", $this->_numRecords, $this->_status["small"]);
		$this->_status["small"]= str_replace("{start}", $this->_recStart, $this->_status["small"]);
		$this->_status["small"]= str_replace("{end}", $this->_recStop, $this->_status["small"]);
		$this->_status["small"]= str_replace("{page}", $this->_currentPage, $this->_status["small"]);
		$this->_status["small"]= str_replace("{pages}", $this->_maxPages, $this->_status["small"]);
	}
	private function nav_process_mysql() {
		$this->execute(true);
	}
	private function nav_process_array() {
		//nothing to to
	}
}