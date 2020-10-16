<?php
class InfoTiles {
	public $infoTiles= array();
	public function __construct() {
		
	}
	public function addTile($infoTile) {
		$this->infoTiles= isset($this->infoTiles)?$this->infoTiles:array();
		$this->infoTiles[$infoTile->id]= $infoTile;
		return $this->infoTiles[$infoTile->id];
	}
	public function getDropDown($id) {
		return isset($this->infoTiles[$id])?$this->infoTiles[$id]:NULL;
	}
	public function render() {
		$html= '';
		if(is_array($this->infoTiles)&&(count($this->infoTiles)>0)) {
			$html.= '
            <div class="row">
			';
			foreach($this->infoTiles as $idx => $infoTile) {
				$html.= $infoTile->render();
			}
			$html.= '
            </div>
            <!-- /.row -->
			';
		}
		return $html;
	}
}

class InfoTile {
	public $id= "";
	public $tileColor= "";
	public $tileIcon= "";
	public $tileClass= "";
	public $infoCount= 0;
	public $infoText= "";
	public $linkText= "";
	public $link= "#";
	public function __construct($id, $tileColor, $tileIcon, $infoCount, $infoText, 
								$linkText, $link) {
		$this->id= $id;
		$this->tileColor= $tileColor;
		$this->tileIcon= $tileIcon;
		$this->infoCount= $infoCount;
		$this->infoText= $infoText;
		$this->linkText= $linkText;
		$this->link= $link;
	}
	public function render() {
		$html= '
                <div class="col-lg-3 col-md-6">
                    <div class="panel '.$this->tileColor.'">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa '.$this->tileIcon.' fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">'.$this->infoCount.'</div>
                                    <div>'.$this->infoText.'</div>
                                </div>
                            </div>
                        </div>
                        <a href="'.$this->link.'">
                            <div class="panel-footer">
                                <span class="pull-left">'.$this->linkText.'</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
		';
		return $html;
	}
}