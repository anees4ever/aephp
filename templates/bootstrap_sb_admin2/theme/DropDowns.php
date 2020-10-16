<?php
class DropDowns {
	public $dropdowns= array();
	public function __construct() {
		
	}
	public function addDropDown($dropdown) {
		$this->dropdowns= isset($this->dropdowns)?$this->dropdowns:array();
		$this->dropdowns[$dropdown->id]= $dropdown;
		return $this->dropdowns[$dropdown->id];
	}
	public function getDropDown($id) {
		return isset($this->dropdowns[$id])?$this->dropdowns[$id]:NULL;
	}
	public function render() {
		$html= '';
		if(is_array($this->dropdowns)&&(count($this->dropdowns)>0)) {
			$html.= '
			<div class="collapse navbar-collapse pull-right" role="navigation" style="overflow-y: unset;">
            <ul class="nav navbar-top-links navbar-right">
			';
			foreach($this->dropdowns as $idx => $dropdown) {
				$html.= $dropdown->render();
			}
			$html.= '
            </ul>
            </div>
            <!-- /.navbar-top-links -->
			';
		}
		return $html;
	}
}

class DropDown {
	public $id= "";
	public $text= "";
	public $iconClass= "";
	public $listClass= "";
	public $listItems= NULL;
	public $link= "#";
	public $linkClass= "";
	public function __construct($id, $text, $iconClass, $listClass, 
								$link, $linkClass) {
		$this->id= $id;
		$this->text= $text;
		$this->iconClass= $iconClass;
		$this->listClass= $listClass;
		$this->link= $link;
		$this->linkClass= $linkClass;
		$this->listItems= NULL;
	}
	public function addItem($item) {
		$this->listItems= isset($this->listItems)?$this->listItems:array();
		$this->listItems[$item->id]= $item;
		return $this->listItems[$item->id];
	}
	public function getItem($id) {
		return isset($this->listItems[$id])?$this->listItems[$id]:NULL;
	}
	public function render() {
		$html= '
                <li class="dropdown">
                    <a class="dropdown-toggle '.$this->linkClass.'" data-toggle="dropdown" href="'.$this->link.'"
						title="'.$this->text.'">
                        <i class="fa '.$this->iconClass.' fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
		';
		if(is_array($this->listItems)) {
			$html.= '
                    <ul class="dropdown-menu '.$this->listClass.'">';
			foreach($this->listItems as $idx => $item) {
				$html.= $item->render();
			}
			$html.= '
                    </ul>
                    <!-- /.dropdown-messages -->
			';
		}
		$html.= '
                </li>
                <!-- /.dropdown -->
		';
		return $html;
	}
}

class DropDownItem {
	public $id= "";
	public $link= "#";
	public $class= "";
	public $icon= "";
	public $divider= true;
	public $content= "";
	public function __construct($id, $link, $class, $icon, $divider, $content) {
		$this->id= $id;
		$this->link= $link;
		$this->class= $class;
		$this->icon= $icon;
		$this->content= $content;
		$this->divider= $divider;
	}
	public function render() {
		$html= '
						<li>
							'.($this->link==''?'':'<a href="'.$this->link.'" '.($this->class==''?'':'class="'.$this->class.'"').'>').'
							'.($this->icon==''?'':'<i class="fa '.$this->icon.' fa-fw"></i> ').'
							'.$this->content.'
							</a>
						</li>
				'.($this->divider?'
						<li class="divider"></li>
				':'');
		return $html;
	}
}