<?php
include("theme/required.php");

ThemeSettings::$templateDir= PATH_TMPL.DS."bootstrap_sb_admin2";
ThemeSettings::$templateUri= ThemeSettings::$templateUri= aeURI::base()."/templates/bootstrap_sb_admin2";

$theme= ThemeSbAdmin::getTheme();
$theme->themeSettings->menuEnabled= true;

$dropDowns= new DropDowns();
$dropdown= $dropDowns->addDropDown(new DropDown("messages", "Messages", "fa-envelope", "dropdown-messages", "#", "tooltip-item");
$dropdown->addItem(new DropDownItem('messages1', '#', '', '', true, '<div><strong>John Smith</strong><span class="pull-right text-muted"><em>Yesterday</em></span></div><div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>'));
$dropdown->addItem(new DropDownItem('messages2', '#', '', '', true, '<div><strong>John Smith</strong><span class="pull-right text-muted"><em>Yesterday</em></span></div><div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>'));
$dropdown->addItem(new DropDownItem('gotomessages', '#', 'text-center', '', false, '<strong>Read All Messages</strong><i class="fa fa-angle-right"></i>'));


$dropdown= $dropDowns->addDropDown(new DropDown("alerts", "Alerts", "fa-bell", "dropdown-alerts", "#", "tooltip-item"));
$dropdown->addItem(new DropDownItem('messages1', '#', '', 'fa-comment', true, '<span><strong>Anees</strong><span class="pull-right text-muted small"><em>3 minutes ago</em></span></span><div>Pellentesque eleifend...</div>'));
$dropdown->addItem(new DropDownItem('messages2', '#', '', 'fa-envelope', true, '<span><strong>Anees</strong><span class="pull-right text-muted small"><em>4 minutes ago</em></span></span><div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>'));
$dropdown->addItem(new DropDownItem('messages3', '#', '', 'fa-tasks', true, '<span><strong>Anees</strong><span class="pull-right text-muted small"><em>1 day ago</em></span></span><div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>'));
$dropdown->addItem(new DropDownItem('gotoalerts', '#', 'text-center', '', false, '<strong>See All Alerts</strong><i class="fa fa-angle-right"></i>'));

$dropdown= $dropDowns->addDropDown(new DropDown("user", "User", "fa-user", "dropdown-user", "#", "tooltip-item"));
$dropdown->addItem(new DropDownItem('userprofile', '#', '', 'fa-user', false, 'User Profile'));
$dropdown->addItem(new DropDownItem('settings', '#', '', 'fa-gear', true, 'Settings'));
$dropdown->addItem(new DropDownItem('logout', 'logout.html', '', 'fa-sign-out', false, 'Logout'));

$metisMenu= new MetisMenu();
$metisMenu->searchEnabled= TRUE;
$metisMenu->addMenu(new MetisMenuItem("dashboard","Dashboard","index.php", "fa-dashboard"));
$menu= $metisMenu->addMenu(new MetisMenuItem("charts","Charts","#", "fa-bar-chart-o"));
	$menu->addSubmenu(new MetisMenuItem("flot-charts","Flot Charts","flot.html", ""));
	$menu->addSubmenu(new MetisMenuItem("Morris-charts","Morris.js Charts","morris.html", ""));
$menu= $metisMenu->addMenu(new MetisMenuItem("multidropdown","Multi-Level Dropdown","#", "fa-sitemap"));
	$menu->addSubmenu(new MetisMenuItem("secondlevel1","Second Level Item","#", ""));
	$menu->addSubmenu(new MetisMenuItem("secondlevel2","Second Level Item","#", ""));
	$menu= $menu->addSubmenu(new MetisMenuItem("secondlevel3","Third Level","#", ""));
		$menu->addSubmenu(new MetisMenuItem("thirdlevel1","Third Level Item","#", ""));
		$menu->addSubmenu(new MetisMenuItem("thirdlevel2","Third Level Item","#", ""));
		$menu->addSubmenu(new MetisMenuItem("thirdlevel3","Third Level Item","#", ""));


$infoTiles= new InfoTiles();
$infoTiles->addTile(new InfoTile("it1", "panel-primary", "fa-comments", "26", "New Comments!", "View Details", "#"));
$infoTiles->addTile(new InfoTile("it2", "panel-green", "fa-tasks", "12", "New Tasks!", "View Details", "#"));
$infoTiles->addTile(new InfoTile("it3", "panel-yellow", "fa-shopping-cart", "124", "New Orders!", "View Details", "#"));
$infoTiles->addTile(new InfoTile("it4", "panel-red", "fa-support", "13", "Support Tickets!", "View Details", "#"));

$theme->pageTitle= $this->getTitle();
$theme->customHeader= $this->getHead();
$theme->customFooterJS= $this->getFooterJS();

$theme->metisMenu= $metisMenu;
$theme->dropDowns= $dropDowns;
$theme->infoTiles= $infoTiles;
$theme->contentBody= $this->content_notices.$this->content_html;

echo $theme->render();