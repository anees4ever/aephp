<?php
defined("aeAPP") or die("Restricted Access");

include(PATH_ASSET.DS."php".DS."combo.php");
include(PATH_ASSET.DS."php".DS."menu.php");
//include(PATH_ASSET.DS."php".DS."numtostr.php");


function floated($number) {
	return number_format($number, 2, ".", ",");
}
function floatedEx($number) {
	if($number !== 0) {
		return number_format($number, 2, ".", ",");
	} else {
		return "";
	}
}

function formatSize($size) {
	$sizes= array("bytes", "KB", "MB", "GB", "TB");
	$aSize= $size;
	foreach($sizes as $I => $sizeStr) {
		if($size < 1024) {
			return round($size, 0, 0) . $sizeStr;
		} else {
			$size= $size/1024;
		}
	}
	return round($aSize, 0, 0) . "bytes";
}

function fmtDT($dt) {
	return date("d/m/Y g:i A", strtotime($dt));
}

function time_elapsed_string($datetime, $level = 7) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        //'s' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    $string = array_slice($string, 0, $level);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function isAMobile() {
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}