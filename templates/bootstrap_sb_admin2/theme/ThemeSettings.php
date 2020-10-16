<?php
class ThemeSettings {
	public static $templateDir= "";
	public static $templateUri= "";
	public $menuEnabled= false;
	public $flotChartEnabled= false;
	public $morrisChartEnabled= false;
	public $dataTableEnabled= false;
	public $sufixVer= "";
	public function __construct() {
		$this->menuEnabled= false;
		$this->flotChartEnabled= false;
		$this->morrisChartEnabled= false;
		$this->dataTableEnabled= false;
		$this->sufixVer= aeUri::withVersion();
	}
		
	public function getMetaHeader() {
		return '
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		';
	}
	private function getFeatureHeaders() {
		$result= '';
		if($this->menuEnabled) {
			$result.= '
    <!-- MetisMenu CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/metisMenu/metisMenu.min.css'.$this->sufixVer.'" rel="stylesheet">
			';
		}
		if($this->dataTableEnabled) {
			$result.= '
    <!-- DataTables CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/dataTables.bootstrap.css'.$this->sufixVer.'" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/datatables-responsive/dataTables.responsive.css'.$this->sufixVer.'" rel="stylesheet">
    <link href="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/rowGroup.dataTables.min.css'.$this->sufixVer.'" rel="stylesheet">
			';
		}
		if($this->flotChartEnabled) {
			$result.= '
    <!-- Morris Charts CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/morrisjs/morris.css'.$this->sufixVer.'" rel="stylesheet">
			';
		}
		if($this->morrisChartEnabled) {
			$result.= '
    <!-- Morris Charts CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/morrisjs/morris.css'.$this->sufixVer.'" rel="stylesheet">
			';
		}
		return $result;
	}

	public function getCSSHeaders() {
		return $this->getFeatureHeaders().'
	<!-- Bootstrap Core CSS -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/bootstrap/css/bootstrap.css'.$this->sufixVer.'" rel="stylesheet">
	<!-- Custom CSS -->
    <link href="'.ThemeSettings::$templateUri.'/dist/css/sb-admin-2.css'.$this->sufixVer.'" rel="stylesheet">
    <link href="'.ThemeSettings::$templateUri.'/dist/css/sb-admin-2-input.css'.$this->sufixVer.'" rel="stylesheet">
	<!-- Custom Fonts -->
    <link href="'.ThemeSettings::$templateUri.'/vendor/font-awesome/css/font-awesome.min.css'.$this->sufixVer.'" rel="stylesheet" type="text/css">
		';
	}
	
	public function getCompatibilityHeaders() {
		return '
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js does not work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js'.$this->sufixVer.'"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js'.$this->sufixVer.'"></script>
    <![endif]-->
		';
	}
	
	private function getFeatureFooterJS() {
		$result= '';
		if($this->menuEnabled) {
			$result.= '
    <!-- Metis Menu Plugin JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/metisMenu/metisMenu.min.js'.$this->sufixVer.'"></script>
			';
		}
		if($this->dataTableEnabled) {
			$result.= '
    <!-- DataTables JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables/js/jquery.dataTables.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/dataTables.bootstrap.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-responsive/dataTables.responsive.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/dataTables.rowGroup.min.js'.$this->sufixVer.'"></script>
    
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/type-detection/formatted-num.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/type-detection/numeric-comma.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/type-detection/num-html.js'.$this->sufixVer.'"></script>
    
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/sorting/formatted-numbers.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/sorting/natural.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/datatables-plugins/sorting/num-html.js'.$this->sufixVer.'"></script>
			';
		}
		if($this->flotChartEnabled) {
			$result.= '
    <!-- Flot Charts JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot/excanvas.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot/jquery.flot.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot/jquery.flot.pie.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot/jquery.flot.resize.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot/jquery.flot.time.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/flot-tooltip/jquery.flot.tooltip.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/data/flot-data.js'.$this->sufixVer.'"></script>
			';
		}
		if($this->morrisChartEnabled) {
			$result.= '
    <!-- Morris Charts JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/raphael/raphael.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/vendor/morrisjs/morris.min.js'.$this->sufixVer.'"></script>
    <script src="'.ThemeSettings::$templateUri.'/data/morris-data.js'.$this->sufixVer.'"></script>
			';
		}
		return $result;
	}
	public function getJSHeader() {
		return '
	<!-- jQuery -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/jquery/jquery.min.js'.$this->sufixVer.'"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/vendor/bootstrap/js/bootstrap.min.js'.$this->sufixVer.'"></script>
	
    <!-- Custom Theme JavaScript -->
    <script src="'.ThemeSettings::$templateUri.'/dist/js/sb-admin-2.js'.$this->sufixVer.'"></script>
		'.$this->getFeatureFooterJS();
	}
}