<?php
defined("aeAPP") or die("Restricted Access");
$theme= ThemeSbAdmin::getTheme();

/*if($theme->viewMode=="content") {
	//nothing to do
} else {*/
	getApplicationMenu();
	getTopDropdownPanel();
	//$theme->infoTiles= $infoTiles;
/*}*/

$theme->pageTitle= $theme->showTitle?$this->getTitle():'';
$theme->customHeader= $this->getHead();
$theme->customJS= $this->getJS();

$theme->contentNotices= $this->content_notices;
$theme->contentBody= $this->content_html;

echo $theme->render();
