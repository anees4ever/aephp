<?php
defined("aeAPP") or die("Restricted Access");
$this->setTitle("Sample Page");


ThemeSbAdmin::getTheme()->themeSettings->dataTableEnabled= true;
aeApp::getApp()->addPhp(PATH_LIB.DS."datagrid".DS."datagrid.php");



$columns= new DataGridColumns();
$columns->addColumn("emp_no", "Emp. No")->setProperty("class", "all");
$columns->addColumn("birth_date", "DOB")->setProperty("width", "110px");
$columns->addColumn("first_name", "First Name")->setProperty("class", "all");
$columns->addColumn("last_name", "Last Name")->setProperty("class", "all");
$columns->addColumn("gender", "Gender")->setProperty("width", "70px")->setProperty("class", "all");
$columns->addColumn("hire_date", "Hired On")->setProperty("width", "110px");

$employeeTable= new DataGrid("employeeTable");
$employeeTable->setMode("sql", aeUri::current(), $postExtras= array())
	->setProperty("_tablename", "ae_employees")//"SELECT emp_no, birth_date, first_name, last_name, gender, hire_date FROM ae_employees")
	->setProperty("width", "100%")
	->setProperty("lengthChange", true)
	->setProperty("paging", true)
	->setProperty("info", true)
	->setProperty("rowSelect", true)
	->setProperty("order", '[[2,"asc"]]');

$employeeTable->setColumns($columns);

$employeeTable->render();