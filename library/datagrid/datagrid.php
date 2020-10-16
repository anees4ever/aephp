<?php
defined("aeAPP") or die("Restricted Access");

require_once("datagrid_column.php");
class DataGrid extends aePaginator {
	public $name= "datagrid";
	public $width= "auto";
	
	public $mode= "ajax";//ajaxdata/data/sql
	//super public $_data= array();//if $mode==data
	
	public $ajax_url= "";
	public $ajax_data= "data";
	public $ajax_type= "POST";
	public $ajax_first= true;//true to do ajax first time if no data
	public $ajax_request_data= array();
	
	public $columns= NULL;//Obj:s
	
	public $responsive= true;
	public $jQueryUI= false;
	public $lengthChange= true;
	public $searching= true;
	public $searchDelay= 200;
	public $info= true;
	public $ordering= true;
	public $orderMulti= true;
	public $orderClasses= false;
	public $stateSave= true;
	
	public $stripedRows= true;
	public $borders= true;
	public $hoverEffect= true;
	
	public $rowSelect= false;
	public $multiRowSelect= false;
	
	public $paging= true;
	public $pagingType= 'full_numbers';//simple/simple_numbers/full/full_numbers
	public $lengthMenu= '[[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]';
	public $pageLength= 10;
	public $order= "[]";//[[1,'asc']],
	
	public $processing= true;
	
	public $scrollX= false;
	public $scrollXInner= "100%";
	public $scrollY= "";
	public $scrollCollapse= true;
	
	public $deferRender= true;
	public $autoWidth= true;
	
	public $retrieve= true;
	public $deferLoading= 0;
	
	public $selectorClass= "active";//selected/active. active for bootstrap
	
	public $events= array(
		"drawCallback"=> "",//function(settings) {}
		"fheaderCallback"=> "",//function(head, data, start, end, display) { }
		"footerCallback"=> "",//function(tfoot, data, start, end, display) { }
	 	"formatNumber"=> "",//function (toFormat) { }
	 	"infoCallback"=> "",//function(settings, start, end, max, total, pre) { }
	 	"initComplete"=> "",//function(settings, json) { }
		"rowCallback"=> "",//function(row, data, displayIndex, displayIndexFull) { }
		"createdRow"=> "",//function(row, data, dataIndex) {}
		
		"customSearch"=> "",//function(data, dataIndex) {},
		"onClick"=> "",//function(event, sender){}
		"onCellClick"=> ""//function(event, sender){}
	);
	
	public function __construct($name) {
		parent::__construct('mysql');
		$this->name= $name;
	}
	
	public function setMode($mode, $ajax_url= "", $ajax_request_data= array()) {
		$this->mode= $mode;
		$this->ajax_url= $ajax_url;
		$this->ajax_request_data= $ajax_request_data;
		return $this;
	}
	
	public function setData($data) {
		$this->_data= $data;
		return $this;
	}
	
	public function setColumns($columns) {
		$this->columns= $columns;
		return $this;
	}
	
	public function addEvent($event, $function) {
		$this->events[$event]= $function;
		return $this;
	}
	
	public function getData() {
		$this->prepareData()
			 ->prepareColumns();
		return $this->_data;
	}
	public function render($echo= true) {
		$this->getData();
		if($this->isDataCallBack()) {
			ob_clean();
			echo json_encode(array($this->ajax_data => $this->_data));
			exit(0);
		}
		$this->ajax_request_data["datagrid_request_data"]= $this->name;
		$html= '<table width="'.$this->width.'" class="table';
		$html.= $this->stripedRows?' table-striped':'';
		$html.= $this->borders?' table-bordered':'';
		$html.= $this->hoverEffect?' table-hover':'';
		$html.= '" id="'.$this->name.'"></table>';
		$html.= '<script>';
		$html.= 'var datagrid_'.$this->name.'= undefined;
var datagrid_'.$this->name.'_ajax_data= '.json_encode($this->ajax_request_data).';
var oTable_'.$this->name.'= undefined;
$(document).ready( function () {
	oTable_'.$this->name.'= $("#'.$this->name.'").dataTable( {
		'./*(($this->mode=='ajax'||$this->mode=='ajaxdata')?'
		ajax: {
			url: "'.$this->ajax_url.'", 
			'.$this->prepareAjaxData().'
			dataSrc: "'.$this->ajax_data.'", 
			type: "'.$this->ajax_type.'" 
		},':'
		data: '.json_encode($this->_data).',')*/
		(is_array($this->_data)?'data: '.json_encode($this->_data).',':'')
		.'
		'.$this->columns->render().'
		responsive: '.($this->responsive?'true':'false').',
			
		jQueryUI: '.($this->jQueryUI?'true':'false').',
		lengthChange: '.($this->lengthChange?'true':'false').',
		searching: '.($this->searching?'true':'false').',
		searchDelay: '.$this->searchDelay.',
		info: '.($this->info?'true':'false').',
		ordering: '.($this->ordering?'true':'false').',
		orderMulti: '.($this->orderMulti?'true':'false').',
		orderClasses: '.($this->orderClasses?'true':'false').',
		stateSave: '.($this->stateSave?'true':'false').',

		paging: '.($this->paging?'true':'false').',
		pagingType: "'.$this->pagingType.'",
		lengthMenu: '.$this->lengthMenu.',
		pageLength: '.$this->pageLength.',
		order: '.$this->order.',

		"language": {
		    "paginate": {
		      "previous": "&lt;&lt;",
		      "next": "&gt;&gt;"
		    }
		},
		
		processing: '.($this->processing?'true':'false').',
		
		scrollX: '.($this->scrollX?'true':'false').',
		'.($this->scrollX?'scrollXInner:"'.$this->scrollXInner.'",':'').'
		scrollY: "'.$this->scrollY.'",
		scrollCollapse: '.($this->scrollCollapse?'true':'false').',

		deferRender: '.($this->deferRender?'true':'false').',
		autoWidth: '.($this->autoWidth?'true':'false').',
		
		retrieve: '.($this->retrieve?'true':'false').',

		deferLoading: '.$this->deferLoading.',
		
		'.$this->prepareEvents().'
	} );

} );';
		$html.= $this->processEvents();
		$html.= '</script>';
		if($echo) {
			echo $html;
		} else {
			return $html;
		}
	}
	
	private function isDataCallBack() {
		return isset($_REQUEST["datagrid_request_data"]) && ($_REQUEST["datagrid_request_data"]==$this->name);
	}
	
	private function prepareData() {
		if(!is_object($this->columns)) {
			$this->columns= new DataGridColumns();
		}
		if($this->mode=="ajax" || $this->mode=="ajaxdata") {
			if(!$this->isDataCallBack()) {
				return $this;
			}
		}
		if(($this->mode=="sql") || ( ($this->mode=="ajax") && ($this->isDataCallBack())) ) {
			$this->_query_type= 'SELECT';
			$this->_fields= $this->columns->getFields();
			$this->nav_exec();
		}
		$this->deferLoading= is_array($this->_data)?count($this->_data):0;
		return $this;
	}
	
	private function prepareColumns() {
		if(!is_object($this->columns)) {
			$this->columns= new DataGridColumns();
		}
		if((count($this->columns->columns)==0) && (is_array($this->_data)) && (count($this->_data)>0)) {
			$fields= $this->_data[0];
			foreach($fields as $field => $fieldValue) {
				$this->columns->addColumn($field);
			}
		}
		return $this;
	}
	private function prepareAjaxData() {
		$var= 'datagrid_'.$this->name.'_ajax_data';
		$html= 'data: function(data) { for(var i in '.$var.') { ';
		$html.= 'data[i]= '.$var.'[i]; ';
		$html.= '}}, ';
		return $html;
	}
	private function prepareEvents() {
		$html= '';
		$this->events= is_array($this->events)?$this->events:array();
		foreach($this->events as $event => $function) {
			$argsList= '';
			switch($event) {
				case 'createdRow': $argsList= $function==""?"":'row, data, dataIndex'; break;
				case 'drawCallback': $argsList= $function==""?"":'settings'; break;
				case 'fheaderCallback': $argsList= $function==""?"":'head, data, start, end, display'; break;
				case 'footerCallback': $argsList= $function==""?"":'tfoot, data, start, end, display'; break;
				case 'formatNumber': $argsList= $function==""?"":'toFormat'; break;
				case 'infoCallback': $argsList= $function==""?"":'settings, start, end, max, total, pre'; break;
				case 'initComplete': $argsList= 'settings, json'; break;
				case 'rowCallback': $argsList= $function==""?"":'row, data, displayIndex, displayIndexFull'; break;
			}
			if($argsList=='') { continue; }
			$fn= $this->name.'_'.$event;
			$functionInit= ($event=='initComplete'?$this->name.'_initializeEvents();'."\r\n":'');
			$html.= '
		'.$event.':'.$fn.',';
			$this->events[$event]= '
function '.$fn.'('.$argsList.'){
	'.$functionInit.'	return '.($function==''?'true;':'('.$function.')('.$argsList.');').'
}
';
		}
		return $html;
	}
	
	private function processEvents() {
		$html= '';
		$this->events= is_array($this->events)?$this->events:array();
		foreach($this->events as $event => $function) {
			if($function=="") { continue; }
			switch($event) {
				case "onClick":
				case "onCellClick":
				case "customSearch":
				
				break;
				default:
					$html.= $function;
				break;
			}
		}
		$customSearch= (isset($this->events["customSearch"])&&($this->events["customSearch"]!=""));
		$clickEvent= (isset($this->events["onClick"])&&($this->events["onClick"]!=""));
		$cellClickEvent= (isset($this->events["onCellClick"])&&($this->events["onCellClick"]!=""));
		$clickable= $this->rowSelect||$this->multiRowSelect||$clickEvent;
		$html.= '
var '.$this->name.'_last_active_row= undefined;
function '.$this->name.'_initializeEvents(){
	datagrid_'.$this->name.'= $("#'.$this->name.'").DataTable();
	
	datagrid_'.$this->name.'.on("draw.dt",function(e, settings){
		$("#'.$this->name.' tbody tr:last").trigger("click");
		var activeRow= $("#'.$this->name.' tbody tr:first");
		if('.$this->name.'_last_active_row!=undefined) {
			$("#reservations tbody tr").each(function(){
				var dataThen= datagrid_reservations.row(this).data();
				if(JSON.stringify('.$this->name.'_last_active_row)==JSON.stringify(dataThen)) {
					activeRow= $(this);
			    }
			});
		}
		activeRow.trigger("click");
		'.$this->name.'_last_active_row;
	});

	'.(($this->mode=='ajax'||$this->mode=='ajaxdata')?'
	datagrid_'.$this->name.'.ajax= {
		url: "'.$this->ajax_url.'", 
		'.$this->prepareAjaxData().'
		dataSrc: "'.$this->ajax_data.'", 
		type: "'.$this->ajax_type.'",
		callback: undefined,
		reload: function(callback) {
			try {
				'.$this->name.'_last_active_row= datagrid_'.$this->name.'.row($("#'.$this->name.' tr.active")).data();
			} catch(e) {
				'.$this->name.'_last_active_row= undefined;
			}
			datagrid_'.$this->name.'.ajax.callback= callback;
			$("#'.$this->name.'").dataTable()._fnProcessingDisplay(true);
			var url= datagrid_'.$this->name.'.ajax.url;
			if(datagrid_'.$this->name.'.ajax.type=="GET") {
				url+= (ur.indexOf("?")>0?"&":"?") + $.param(datagrid_'.$this->name.'_ajax_data);
				jQuery.get(url, '.$this->name.'_ajax_callback);
			} else {
				var postdata= datagrid_'.$this->name.'_ajax_data;
				jQuery.post(url, postdata, '.$this->name.'_ajax_callback);
			}
		}
	};
	'.($this->ajax_first&&(!is_array($this->_data) || (count($this->_data)==0))?'datagrid_'.$this->name.'.ajax.reload();':'').'
	':'').'

	'.($customSearch?'$.fn.dataTable.ext.search.push(
		function(settings, data, dataIndex) {
			var jData= datagrid_'.$this->name.'.data()[dataIndex];
			return ('.$this->events["customSearch"].')(jData, dataIndex);
		}
	);
	datagrid_'.$this->name.'.draw();
	':'').'
	'.($clickable?'$("#'.$this->name.' tbody").on("click", "tr[role=row]", function (event) {
		'.($this->multiRowSelect?'$(this).toggleClass("'.$this->selectorClass.'");':
		($this->rowSelect?'if($(this).hasClass("'.$this->selectorClass.'")) {
			//$(this).removeClass("'.$this->selectorClass.'");
		} else {
			$("#'.$this->name.' tbody tr.'.$this->selectorClass.'").removeClass("'.$this->selectorClass.'");
			$(this).addClass("'.$this->selectorClass.'");
		}':'')).'
		'.($clickEvent?'('.$this->events["onClick"].')(event, this);':'').'
	} );
	':'').'
	
	var click_blocked= false;
	$("#'.$this->name.' tbody").on("click", "tr[role=row] td:first-child", function (event) {
		if(click_blocked) return true;
		var tr= $(this).closest("tr");
		var row= datagrid_'.$this->name.'.row(tr);
		//$("#'.$this->name.' tbody tr.child").remove();
		click_blocked= true;
		try {
			if(!tr.hasClass("parent")) {
				$("#'.$this->name.' tbody tr.parent td:first-child").trigger("click");
			}
		} catch(e) {}
		click_blocked= false;
	} );
	
	'.($cellClickEvent?'$("#'.$this->name.' tbody tr[role=row]").on("click", "td", function (event) {
		('.$this->events["onCellClick"].')(event, this);
	});':'').'
	'.($this->rowSelect?'
	$("#'.$this->name.' tbody tr:last").trigger("click");
	$("#'.$this->name.' tbody tr:first").trigger("click");
	':'').'
}
'.(($this->mode=='ajax'||$this->mode=='ajaxdata')?'
function '.$this->name.'_ajax_callback(response) {
	try {
		var responseData= {};
		try {
			responseData= JSON.parse(response);
		} catch(e1) {
			try {
				responseData= eval("(" + response + ")");
			} catch (e) {
				responseData= {result: "casterror", msg: response};
			}
		}
		if(responseData.hasOwnProperty(datagrid_'.$this->name.'.ajax.dataSrc)) {
			var data= responseData[datagrid_'.$this->name.'.ajax.dataSrc];
			datagrid_'.$this->name.'.clear();
			datagrid_'.$this->name.'.rows.add(data);
			datagrid_'.$this->name.'.draw();
		}
		if(datagrid_'.$this->name.'.ajax.callback != undefined) {
			datagrid_'.$this->name.'.ajax.callback();
		}
	} catch (e) {
		alert("Invalid Data response. Try again...!" + e);
	}
	$("#'.$this->name.'").dataTable()._fnProcessingDisplay(false);
}':'').'
function '.$this->name.'_ajax() {
	return datagrid_'.$this->name.'_ajax_data;
}
';
		return $html;
	}
}
