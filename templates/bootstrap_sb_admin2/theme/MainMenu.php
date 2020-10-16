<?php
class MainMenu {
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
            <div class="collapse navbar-collapse pull-right" role="navigation" >
                <ul class="nav navbar-nav navbar-left" id="main-menu">
		';
		
		if(is_array($this->menuItems)&&(count($this->menuItems)>0)) {
			foreach($this->menuItems as $idx => $menu) {
				$html.= $menu->render();
			}
		}
		
		$html.= '
                </ul>
            </div>
            <!-- /.navbar-static-side -->
		';
		
		$html.= '
		<script>
			$(document).ready(function(){
				if($("button.navbar-toggle:visible").length == 0) {
					$("ul#main-menu li.dropdown").hover(function() {
						if($("button.navbar-toggle:visible").length == 0) {
							$(this).find(".dropdown-menu:first").stop(true, true).delay(00).slideDown(300);
						}
					}, function() {
						if($("button.navbar-toggle:visible").length == 0) {
							$(this).find(".dropdown-menu:first").stop(true, true).delay(00).slideUp(200);
						}
					});
				}
				$(document).on("click",".navbar-collapse.in",function(e) {
				    if( $(e.target).is("a:not(\'.dropdown-toggle\')") ) {
				        $("button.navbar-toggle").trigger("click");
				    }
				});
			});
		</script>
		';
		return $html;
	}
	public function renderDropdown($label, $iconClass, $id= "", $align= "dropdown-menu-left") {
		$id= $id==""?"":' id="'.$id.'" ';
		$icon= $iconClass==""?"":'<i class="fa '.$iconClass.' fa-fw"></i>';
		$html= '
			<div class="dropdown" '.$id.' dropdown-parent="body">
			    <button class="dropdown-toggle btn btn-info btn-xs" data-toggle="dropdown" aria-expanded="true">
			        '.$icon.$label.' <i class="fa fa-caret-down"></i>
			    </button>
			    <ul class="dropdown-menu '.$align.'">
		';
		
		if(is_array($this->menuItems)&&(count($this->menuItems)>0)) {
			foreach($this->menuItems as $idx => $menu) {
				$html.= $menu->render(false, "fa-caret-right");
			}
		}
		
		$html.= '
			    </ul>
			</div>
		';
		return $html;
	}
	public static function getSubmenuJS($id, $leftAlign= false, $custom=true) {
		$id= $id==""?"":"#".$id." ";
		return '<script>
$(document).ready(function(){
	$("'.$id.'li.dropdown-submenu a.dropdown-submenu-toggle").on("click mouseenter ", function(e){
	  	var visible= $(this).next("ul:visible").length;
		$("li.dropdown-submenu ul.dropdown-menu", $(this).parent().parent()).hide();
	    if(visible>0) {
	    	$(this).next("ul").hide();
		} else {
			$(this).next("ul").show();
		}
	    e.stopPropagation();
	    e.preventDefault();
	});
	$("'.$id.' li.dropdown-submenu ul.dropdown-menu a").on("click", function(e){
	  	$(this).parent().parent().hide();
	});
	'.($custom?'
	if($(window).width()<=767) {/*Force dropdown to bottom align*/
		  $("'.$id.' .dropdown-submenu .dropdown-menu").each(function(){
		    $(this).css({left: "auto", right: "auto", top: "auto"});
		  });
	} else {
	  	'.($leftAlign?'
	  	$("'.$id.' .dropdown-submenu .dropdown-menu").each(function(){
		  $(this).css({left: "auto", right: $("'.$id.' .dropdown-menu").width() + "px"});
		});
	  	':'').'
	}':'
	  $("'.$id.' .dropdown-submenu .dropdown-menu").each(function(){
	    $(this).css({left: "auto", right: "auto", top: "auto"});
	  });').'
});
</script>';
	}
}

class MainMenuItem {
	public $id= "";
	public $text="";
	public $link= "#";
	public $iconClass="";
	public $active= FALSE;
	public $more= FALSE;
	public $subMenuItems= NULL;
	public $anchor_attrs= "";
	
	public function __construct($id, $text, $link, $iconClass, $active= FALSE) {
		$this->id= $id;
		$this->text= $text;
		$this->link= $link;
		$this->iconClass= $iconClass;
		$this->active= $active;
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
	
	public function render($asMenu= true, $caretClass= "fa-caret-down") {
		if($this->text=='-') {
			$html= '<li role="separator" class="divider"></li>';
			return $html;
		}
		$this->more= is_array($this->subMenuItems)&&(count($this->subMenuItems) > 0);
		$this->link= $this->more?'#':$this->link;
		if($asMenu) {
			$classes= $this->more?' dropdown':'';
			$amore= $this->more?' class="dropdown-toggle" data-toggle="dropdown" ':'';
		} else {
			$classes= $this->more?' dropdown-submenu':'';
			$amore= $this->more?' class="dropdown-submenu-toggle" ':'';
		}
		$classes.= $this->active?' active':'';
		$html= '
                    <li class="'.$classes.'" >
                        <a href="'.$this->link.'" '.$amore.' '.$this->anchor_attrs.' >'.
						($this->iconClass==''?'':'<i class="fa '.$this->iconClass.' fa-fw"></i>').
						' '.$this->text.
						($this->more?'<i class="fa '.$caretClass.'" style="margin-left: 5px;"></i>':'').'</a>
						';
		if($this->more) {
			$html.= '
                                <ul class="dropdown-menu">
								';
			foreach($this->subMenuItems as $idx => $menu) {
				$html.= $menu->render($asMenu, $caretClass);
			}
			$html.= '
								</ul>';
		}
		return $html;
	}
}