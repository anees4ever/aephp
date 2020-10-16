<?php
defined("aeAPP") or die("Restricted Access");


function getComboBoxSQL($name, $sql, $class= "", $attr= "", $null=false, $nullData= array("", "select"), 
				   		$fields= array("id", "value"), $more= false/*array(fieldname1, fieldname2, ...)*/) {
	$db= aeApp::getDB();
	$data= $db->loadAssocList($sql);
	return getComboBox($name, $data, $class, $attr, $null, $nullData, $fields, $more);
}

function getComboBox($name, $data, $class= "", $attr= "", $null=false, $nullData= array("", "select"), 
					 $fields= array("id", "value"), $more= false) {
	$nullData= is_array($nullData)&& count($nullData)==2?$nullData:array("id", "value");
	$class= $class==""?"":' class="'.$class.'" ';
	$fields= is_array($fields)&& count($fields)==2?$fields:array("id", "value");
	$value= aeRequest::getVar($name, $nullData[0]);
	$selected= $value==$nullData[0]?' selected':'';
	$html= '<select name="'.$name.'" id="'.$name.'" '.$class.' '.$attr.'>';
	if($null===true) {
		$html.= '<option value="'.$nullData[0].'" ';
		if(is_array($more) && (count($more) > 0)) {
			foreach($more as $idx => $moreFld) {
				$html.= ' '.$moreFld.'="" ';
			}
		}
		$html.= $selected.'>'.$nullData[1].'</option>';
	}
	if(is_array($data) && (count($data)>0)) {
		foreach($data as $idx => $row) {
			$selected= $value==$row[$fields[0]]?' selected':'';
			$html.= '<option value="'.$row[$fields[0]].'" ';
			if(is_array($more) && (count($more) > 0)) {
				foreach($more as $idx => $moreFld) {
					$html.= ' '.$moreFld.'="'.$row[$moreFld].'" ';
				}
			}
			$html.= $selected.'>'.$row[$fields[1]].'</option>';
		}
	}
	$html.= '</select>';
	return $html;
}