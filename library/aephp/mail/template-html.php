<?php
defined("aeAPP") or die("Restricted Access");
//PHP Based E-Mail Template(Letter Pad Format)
$mailer= aeRequest::getVar("force_fileMail", "no")=="yes"?"filemail":aeApp::getConfig()->mailer_engine;
aeRequest::setVar("force_fileMail", "no");

if(!function_exists("filterMailBodyHTML")) {
function filterMailBodyHTML($body, $__message = false) {
	return $body;
}
}

$Cr = "\n\r";
$CompanyName = "Company Name";
$CompanyWebAddress = "YourDomain.com";
$CompanyTitle = "Company Name";
$CompanyLogoAddress = 'assets/images/aephp_cat.png';
$CompanyAddress1 = "Edit library/aephp/mail/template-html.php";
$CompanyAddress2 = "";
$CompanyContact1 = "phone number";
$CompanyContact2 = 'email id';

$Contents = isset($Contents)?$Contents:'';
/*Prepare Company Link */
if($CompanyWebAddress !== '') {
  $AStart = '<a title="' . $CompanyTitle . '" href="' . $CompanyWebAddress . '" target="_blank" style="text-decoration:none;color:#333;">' . $Cr;
  $AEnd = '</a>' . $Cr;
}
else {
  $AStart = '';
  $AEnd = '';
}
/*Prepare Company Logo*/
if($CompanyLogoAddress !== '') {
  $imgCID= $mailer=='swiftmail'?$__message->embed(Swift_Image::fromPath(PATH_ROOT."/".$CompanyLogoAddress)):aeURI::base().$CompanyLogoAddress;
  $Logo = '<img title="' . $CompanyTitle . '" src="' . $imgCID . '" alt="' . $CompanyTitle . '" width="480" height="111" border="0" />' . $Cr;
}
else {
  $Logo = '';
}
/*Prepare Company Address*/
$Address = '';
if($CompanyAddress1 !== '') {
  $Address .= $CompanyAddress1 . $Cr;
}
if($CompanyAddress2 !== '') {
  $Address .= $CompanyAddress2 . $Cr;
}
if($CompanyContact1 !== '') {
  $Address .= ($Address==""?"":"<br />"). $CompanyContact1 . $Cr;
}
if($CompanyContact2 !== '') {
  $Address .= ($Address==""?"":($CompanyContact1==""?"<br />":"")). $CompanyContact2 . $Cr;
}

//$BodyBgCID= $mailer=='swiftmail'?$__message->embed(Swift_Image::fromPath('/tmpl/default/images/bg-body.jpg')):'/templates/default/images/bg-body.jpg';

?>
<!DOCTYPE HTML>
<html>
<head>
</head>
<body style="background:#f8f8f8;"><?php /*<body style="background:url(<?php echo $BodyBgCID; ?>);">*/ ?>
    <table style="width: 98%; padding: 0px; margin: 15px 10px 10px; border-collapse: collapse; width: 97%; height:100%; border:1px solid #999;" 
           border="1" cellspacing="0" cellpadding="0">
        <tbody>
        	<?php if($Logo!='') { ?>
            <tr valign="top">
                <td style="padding: 10px; vertical-align: middle; text-align: center; width: 100%; background: #dff0d8;" valign="middle">
                	<?php echo $AStart . $Logo . $AEnd; ?></td>
            </tr>
            <?php } else if($CompanyName!='' || $Address!='') { ?>
            <tr valign="top">
                <td style="padding: 10px; vertical-align: middle; text-align: center; width: 100%; background: #dff0d8;" valign="middle">
                	<?php echo $CompanyName==""?"":"<h3 style='margin:3px;padding:3px;'>{$AStart}{$CompanyName}{$AEnd}</h3>"; ?>
                	<?php echo $Address==""?"":"<p style='margin:3px;padding:3px;'>{$Address}</p>"; ?>
                </td>
            </tr>
            <?php } ?>
            <tr valign="top">
                <td style="padding: 3px; vertical-align: middle;" valign="middle">
                	<form name="mailer" method="post" action="<?php echo aeURI::base(); ?>" target="_blank">
                    	<div style="padding: 3px;"><?php echo $__content; ?></div>
                    </form>
                </td>
            </tr>
            <tr valign="top">
                <td style="background-color:#591434;vertical-align: middle; text-align: center; background: #dff0d8" valign="middle">
                    <span style="font-family: book antiqua,palatino; font-size: small;">
						<?php echo $AStart; ?>
                        <span style="color: #333;"><?php echo $CompanyName; ?></span>
                    <?php echo $AEnd; ?></span></td>
            </tr>
        </tbody>
    </table>
</body></html>