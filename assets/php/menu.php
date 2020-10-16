<?php
defined("aeAPP") or die("Restricted Access");

function getApplicationMenu() {
	$base= aeUri::base();
	ThemeSbAdmin::getTheme()->themeSettings->menuEnabled= true;
	
	$userType= aeUser::getUserType();
	$mainMenu= new MainMenu();

	$menu= $mainMenu->addMenu(new MainMenuItem("menu-home","Home","{$base}", "fa-home"));
	$menu= $mainMenu->addMenu(new MainMenuItem("menu-git","Goto GitHub","https://github.com/anees4ever", "fa-github"));
	$menu->anchor_attrs= ' target="_blank" ';

	$menu= $mainMenu->addMenu(new MainMenuItem("menu-sample","Page Sample","{$base}sample-page.html", "fa-pagelines"));
	$menu= $mainMenu->addMenu(new MainMenuItem("menu-sample2","Sub-Menu Sample","#", "fa-map-marker"));
		$menu->addSubMenu(new MainMenuItem("menu-item1","To edit this menu","#", "fa-bars"));
		$menu->addSubMenu(new MainMenuItem("menu-div-1","-","", ""));
		$menu->addSubMenu(new MainMenuItem("menu-item2","Goto Assets/php/menu.php","#", "fa-bars"));
			
	ThemeSbAdmin::getTheme()->mainMenu= $mainMenu;
}

function getTopDropdownPanel() {
	if(aeUser::id()>0) {
		$base= aeUri::base();
		$dropDowns= new DropDowns();
	
		$dropdown= $dropDowns->addDropDown(new DropDown("user", "User", "fa-user", "dropdown-user", "#", "tooltip-item"));
		$user= aeUser::getUser(aeUser::id());
		$url= aeUser::getUserType()=="R"?"{$base}profile.html":"#";
		$dropdown->addItem(new DropDownItem('username', $url, '', '', false, "<strong>{$user->fullname}</strong>"));
		$dropdown->addItem(new DropDownItem('logout', "{$base}logout.html", '', 'fa-sign-out', false, 'Logout'));
   
		ThemeSbAdmin::getTheme()->dropDowns= $dropDowns;
	}
}