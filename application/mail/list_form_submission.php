<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$submitbody = '';
foreach($formList as $flist){
	$submitbody .= $flist['label'].":\r\n".$input[$flist['name']]. "\r\n";
} 
$formDisplayUrl=URL::to($url_path) . '?qsid='.$questionSetId;
//echo $formDisplayUrl;
$body = t("
You have submitted the following message from the form \"%s\" through our website.
-----
%s
-----
Thank you.
", $formname, $submitbody);