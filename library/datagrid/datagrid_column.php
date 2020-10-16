<?php
defined("aeAPP") or die("Restricted Access");

class DataGridColumns extends aeObject {
	public $columns= array();
	public function __construct() {
		$this->columns= array();
	}
	
	public function addColumn() {
		$args_cnt= func_num_args();
		if($args_cnt > 0) {
			$id= "";
			if(is_string(func_get_arg(0))) {
				$field= func_get_arg(0);
				$title= $args_cnt>1?func_get_arg(1):$field;
				$id= $args_cnt>2?func_get_arg(2):$field;
				$this->columns[$id]= new DataGridColumn($field, $title, $id);
				return $this->columns[$id];
			} else if(is_object(func_get_arg(0))) {
				$id= func_get_arg(0)->id;
				$this->columns[$id]= func_get_arg(0);
				return $this->columns[$id];
			}
		}
		return NULL;
	}
	
	public function getColumn($field, $create = true) {
		if(isset($this->columns[$field])) {
			return $this->columns[$field];
		}
		return ($create?addColumn($field):NULL);
	}
	
	public function getFields() {
		$fields= array();
		if(count($this->columns) == 0) {
			$fields[]= "*";
		} else {
			$select= "";
			foreach($this->columns as $column) {
				if($column->type!=="calculated") {
					$ca= $column->fieldAlias!==""&&$column->fieldAlias!==$column->field?" AS {$column->fieldAlias}":"";
					$fields[]= ($column->tableAlias==""?"":"{$column->tableAlias}.").$column->field.$ca;
				}
			}
		}
		return $fields;
	}
	
	public function render() {
		$columns= "";
		$columnDefs= "";
		if(count($this->columns) >= 0) {
			$index= 0;
			foreach($this->columns as $idx => $column) {
				$columns.= '{'.
					'"title": "'.$column->title.'",'.
					'"data": "'.$column->field.'",'.
					'"type": "'.$column->type.'",'.
					'"width": "'.$column->width.'",'.
					'"searchable": '.($column->searchable?"true":"false").','.
					'"orderable": '.($column->orderable?"true":"false").','.
					'"visible": '.($column->visible?"true":"false").','.
					'"className": "'.$column->class.($column->visible?"":" never").'",'.
					//$column->prepareEvents().
					'},';
				$events= $column->prepareEvents();
				$columnDefs.= '{
					"aTargets": ['.$index.'],
					"sTitle": "'.$column->title.'",
					"sType": "'.$column->type.'",
					"sWidth": "'.$column->width.'",
					"bSearchable": '.($column->searchable?"true":"false").',
					"bSortable": '.($column->orderable?"true":"false").',
					"bVisible": '.($column->visible?"true":"false").',
					"sClass": "'.$column->class.($column->visible?"":" never").'",
					"mData": "'.$column->field.'",
					'.$events.'
				}, ';
				$index++;
			}
		}
		return 'columnDefs: ['.$columnDefs.'],';
		/*return 'columns: ['.$columns.'],
				columnDefs: ['.$columnDefs.'],';*/
	}
}

class DataGridColumn extends aeObject {
	public $id= "";
	public $title= "";
	public $field= "";
	public $fieldAlias= "";
	public $tableAlias= "";
	
	public $type= "natural";//string, numeric, date, time, datetime, html, natural, num-html
	public $orderDataType= "dom-text";//dom-text,dom-select,dom-checkbox
	
	public $width= "";//px or %
	public $searchable= true;
	public $orderable= true;
	public $visible= true;
	
	public $events= array(
		"render"=> "",//function(data,type,row,meta)
		"createdCell"=> ""//function(td,cellData,rowData,row,col)
	);
	
	public $class= "";
	
	public function __construct($field, $title= "", $id= "") {
		$id= $id==""?$field:$id;
		$title= $title==""?$field:$title;
		$this->id= $id;
		$this->field= $field;
		$this->title= $title;
	}
	
	public function addEvent($event, $function) {
		$this->events[$event]= $function;
		return $this;
	}
	
	
	public function prepareEvents() {
		$html= '';
		$this->events= is_array($this->events)?$this->events:array();
		foreach($this->events as $event => $function) {
			if($function=='') { continue; }
			switch($event) {
				case 'render': $html.= '"mRender": '.$function.', '; break;
				case 'createdCell': $html.= '"fnCreatedCell": '.$function.', '; break;
			}
		}
		return $html;
	}
	/*public function prepareEvents() {
		$html= '';
		$this->events= is_array($this->events)?$this->events:array();
		foreach($this->events as $event => $function) {
			$argsList= '';
			switch($event) {
				case 'render': $argsList= $function==""?"":'data,type,row,meta'; break;
				case 'createdCell': $argsList= $function==""?"":'td,cellData,rowData,row,col'; break;
			}
			if($argsList=='') { continue; }
			$html.= '
		'.$event.':'.$function.',';
		}
		return $html;
	}*/
}
