<?php
class ThemeSbAdmin {
	public $themeSettings= NULL;
	public $siteConfig= NULL;
	
	public $showTitle= TRUE;
	public $pageTitle= "";
	public $customHeader= "";
	public $customJS= "";
	
	public $mainMenu= NULL;
	public $dropDowns= NULL;
	public $metisMenu= NULL;
	public $infoTiles= NULL;
	
	public $viewMode= "template";//content
	public $contentNotices= array();
	public $contentBody= "";
	
	/**
	* Returns an Instance of aeAppTmpl
	* @return instance of aeAppTmpl.
	*/
	public static function getTheme() {
		static $themeInstance;
		if(!is_object($themeInstance)) {
			$themeInstance= new ThemeSbAdmin();
		}
		return $themeInstance;
	}
	
	public function __construct() {
		$this->themeSettings= new ThemeSettings();
	}
	
	public function render() {
		$bodyWraper= $this->viewMode=="content"?'container':'wrapper';
		$pageWraper= $this->viewMode=="content"?'':'';//page-
		$html= '<!DOCTYPE html>
<html lang="en">

<head>
'.
		$this->getHeaders()
.'
<style>
.navbar-default {
	background: #dff0d8 !important;
}
.navbar-default .navbar-nav > .active > a,
.navbar-default .navbar-nav > .active > a:hover,
.navbar-default .navbar-nav > .active > a:focus {
	color: #fff !important;
	background: #337ab7 !important;
}
</style>
</head>

<body>

    <div id="'.$bodyWraper.'">
	'.
		$this->getNavigation()
	.'
	
		'.($pageWraper==''?'<div class="row rowNoMargin"><div class="col-xs-12">':'<div id="page-wrapper">').'
		'.
			$this->getPageTitle().
			$this->getContentNotices().
			$this->getInfoTiles().
			$this->getContentBody()
		.'
        '.($pageWraper==''?'</div></div>
        <!-- /#page-row -->':'</div>
        <!-- /#page-wrapper -->').'
	</div>
	
</body>

</html>
		';
		return $html;
	}

	private function getHeaders() {
		return $this->themeSettings->getMetaHeader() .
				$this->themeSettings->getCSSHeaders() .
				$this->customHeader.
				$this->themeSettings->getJSHeader().
				$this->customJS.
				$this->themeSettings->getCompatibilityHeaders();
	}
	private function getNavigation() {
		$mainMenu= $this->getMainMenu();
		if($this->viewMode=="content") {
			return '';
		}
		$html= '
        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0;background: #dff0d8;">
            <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="'.aeUri::base().'index.html" style="padding: 0px 15px !important;">aePHP</a>
            </div>
            <!-- /.navbar-header -->
			'.
			$this->getdropDowns().
			$mainMenu.
			$this->getSideMenu()
			.'
				<div class="clearfix"></div>
            </div>
			<!-- /.container-fluid -->
		</nav>
		';
		
		return $html;
	}
	private function getMainMenu() {
		return is_object($this->mainMenu)?$this->mainMenu->render():"";
	}
	private function getdropDowns() {
		return is_object($this->dropDowns)?$this->dropDowns->render():"";
	}
	private function getSideMenu() {
		return is_object($this->metisMenu)?$this->metisMenu->render():"";
	}
	private function getContentNotices() {
		$html= '
                        <div class="notice-panel" id="notice-panel">
		';
		if(is_array($this->contentNotices) && (count($this->contentNotices) > 0)) {
			foreach($this->contentNotices as $idx => $notice) {
				//type=>'notice'/*message/error/warning*/
				//notice
				$class= $notice["type"];
				$class= $class=="notice"?"info":$class;
				$class= $class=="message"?"success":$class;
				$class= $class=="error"?"danger":$class;
				$class= $class=="warning"?"warning":$class;
				$html.= '
                            <div class="alert alert-'.$class.' alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                '.$notice["notice"].' '.
								($notice["link"]==''?'':'<a href="'.$notice["link"].'" class="alert-link">More</a>').'
                            </div>
				';
			}
		}
		$html.= '
                        </div>
                        <!-- .notice-panel -->
		';
		return $html;
	}
	private function getPageTitle() {
		if(!$this->showTitle || $this->pageTitle=="") {
			return '';
		}
		$html= '
		    <div class="row">
                <div class="col-xs-12">
                    <h3 class="page-header" style="padding-bottom: 3px; margin: 10px 0 5px;">'.$this->pageTitle.'</h3>
                </div>
                <!-- /.col-xs-12 -->
            </div>
		';
		return $html;
	}
	private function getInfoTiles() {
		if($this->viewMode=="content") {
			return '';
		}
		return is_object($this->infoTiles)?$this->infoTiles->render():"";
	}
	private function getContentBody() {
		return $this->contentBody;
	}
}