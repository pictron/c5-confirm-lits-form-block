<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$submitbody = '';
foreach($formList as $flist){
	$submitbody .= $flist['label'].":\r\n".$input[$flist['name']]. "\r\n";
} 
$formDisplayUrl=URL::to($url_path) . '?qsid='.$questionSetId;
//echo $formDisplayUrl;
$body = t("
Received from the form \"%s\" through website.
-----
%s
-----
Thank you.
", $formname, $submitbody);