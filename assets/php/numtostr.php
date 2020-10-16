<?php
defined("aeAPP") or die("Restricted Access");
//By Muhammed Anees on 06/03/2013
//Accepts any number and convert it to number string in Indian counting..
/*=============usage=============

echo numtostr($your_number);

=================================*/

$GLOBALS["sOnes"]= array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
$GLOBALS["sTens"]= array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
$GLOBALS["s11p"]= array('ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
$GLOBALS["sAmtN"]= array(' ', 'hundred ', 'thousand ', 'lakh ', 'crore ');
$GLOBALS["sAmtP"]= array(2, 1, 2, 2, -1);
$GLOBALS["MaxNmbr"]= 9999999999.99;//depreciated...

function wordStr($pNum, $pSufix) {
	global $sOnes;
	global $sTens;
	global $s11p;
	global $sAmtN;
	global $sAmtP;
	global $MaxNmbr;
	
	$iWord= 0;
	$iNum1=0;
	$iNum2=0;
    $Result= '';
    if (trim($pSufix) <> '') { $pSufix= ' ' . $pSufix; }
    $iLen= strlen($pNum);
    $iWord= (int)$pNum;
    $iNum1= (int)$pNum[0];
    if ($iLen > 1) { $iNum2= (int)$pNum[1]; } else { $iNum2= -1; }

    if ($iWord > 0) {
		//If the number is greater than 100crores, convert rest to Words
		if ($iLen > 2) { $Result= numtostr($iWord) . $pSufix; }
		else if ($iLen > 1) {
			switch($iNum1) {
				//One two three etc...
				case 0: $Result.= $sOnes[$iNum2] . $pSufix;
				break;
				//Eleven twelwe etc...
				case 1: $Result.= $s11p[$iNum2] . $pSufix;
				break;
				//Ten twenty etc...
				default: $Result.= $sTens[$iNum1] . ' ' . $sOnes[$iNum2] . $pSufix;
				break;
			}
		} else { $Result= $sOnes[$iNum1] . $pSufix; }
    } else { $Result= ''; }
	return $Result;
}
  
function numtostr($nNumber) {
	global $sOnes;
	global $sTens;
	global $s11p;
	global $sAmtN;
	global $sAmtP;
	global $MaxNmbr;

	if($nNumber == 0) {
		return "zero";
	}

	$sTmp= '';	$sTmp2= '';		$sNum= '';  $sPre= ''; $sPost= '';
	$iCnt= 0;
	$Result= '';
	//  if ($nNumber > $MaxNmbr) {
	//    echo 'The number exceeds maximum limit...!';
	//    exit(0);
	//  }
	$nNumber= number_format($nNumber, 2, ".", "");
	$sNum= vsprintf("%10.0f.%02d",explode(".", $nNumber));

	//Decimal Places...
	$sTmp= substr($sNum, strpos($sNum, '.') + 1, 13);
	$sPost= trim(wordStr($sTmp, ' paise '));

	//Rest of Currency
	$sPre= '';  $iCnt= 0;
	$sTmp= substr($sNum, 0, strpos($sNum, '.'));

	while ($sTmp !== '') {
		if ($sAmtP[$iCnt] < 0) { $sTmp2= $sTmp; }
		else { $sTmp2= substr($sTmp, strlen($sTmp) - $sAmtP[$iCnt] , $sAmtP[$iCnt]); }
		$sPre= wordStr($sTmp2, $sAmtN[$iCnt]) . $sPre;
		if ($sAmtP[$iCnt] < 0) { $sTmp= ''; }
		else { $sTmp= substr($sTmp, 0, strlen($sTmp) - $sAmtP[$iCnt]); }
		$iCnt++;
	}

	if (($sPre !== '') && ($sPost !== '')) { $sPost= 'and ' . $sPost; }

	$Result= trim($sPre . $sPost);
	return $Result;
}

/*
//===================Example======================
for ($i = 0; $i <= 100; $i++) {
	echo $i*$i."= ". numtostr($i*$i)."<br />";
}
*/
?>