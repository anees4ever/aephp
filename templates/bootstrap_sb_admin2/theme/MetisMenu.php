<?php
class MetisMenu {
	public $searchEnabled= TRUE;
	public $menuItems= array();
	public function __construct() {
		
	}
	public function addMenu($menu) {
		$this->menuItems= isset($this->menuItems)?$this->menuItems:array();
		$this->menuItems[$menu->id]= $menu;
		return $this->menuItems[$menu->id];
	}
	public function getMenu($id) {
		return isset($this->menuItems[$id])?$this->menuItems[$id]:NULL;
	}
	public function render() {
		$html= '
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse collapse" aria-expanded="false">
                    <ul class="nav" id="side-menu">
		';
		if($this->searchEnabled) {
			$html.= '
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
			';
		}
		
		if(is_array($this->menuItems)&&(count($this->menuItems)>0)) {
			foreach($this->menuItems as $idx => $menu) {
				$html.= $menu->render(1);
			}
		}
		
		$html.= '
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
		';
		return $html;
	}
}

class MetisMenuItem {
	public $id= "";
	public $text="";
	public $link= "#";
	public $iconClass="";
	public $more= FALSE;
	public $subMenuItems= NULL;
	
	public function __construct($id, $text, $link, $iconClass) {
		$this->id= $id;
		$this->text= $text;
		$this->link= $link;
		$this->iconClass= $iconClass;
		$this->more= FALSE;
		$this->subMenuItems= NULL;
	}
	
	public function addSubmenu($menu) {
		$this->subMenuItems= isset($this->subMenuItems)?$this->subMenuItems:array();
		$this->subMenuItems[$menu->id]= $menu;
		$this->more= TRUE;
		return $this->subMenuItems[$menu->id];
	}
	public function getSubmenu($id) {
		return isset($this->subMenuItems[$id])?$this->subMenuItems[$id]:NULL;
	}
	public function setSubmenuItems($items) {
		try {
			$this->subMenuItems= $items;
			$this->more= is_array($this->subMenuItems)&&(count($this->subMenuItems) > 0);
		} catch(Throwable $t) {
			$this->more= FALSE;
		} catch(Exception $e) {
			$this->more= FALSE;
		}
	}
	
	public function render($level) {
		$this->more= is_array($this->subMenuItems)&&(count($this->subMenuItems) > 0);
		$html= '
                        <li>
                            <a href="'.$this->link.'">'.
							($this->iconClass==''?'':'<i class="fa '.$this->iconClass.' fa-fw"></i>').
							' '.$this->text.
							($this->more?'<span class="fa arrow"></span>':'').'</a>
							';
		if($this->more) {
			$levelClass= $level==1?"second":"third";
			$html.= '
                                    <ul class="nav nav-'.$levelClass.'-level">
									';
			foreach($this->subMenuItems as $idx => $menu) {
				$html.= $menu->render($level+1);
			}
			$html.= '
									</ul>
                                    <!-- /.nav-'.$levelClass.'-level -->';
		}
		return $html;
	}
}