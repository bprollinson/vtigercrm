<?php
/********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
* 
 ********************************************************************************/

require_once('config.php');
require_once('include/database/PearDatabase.php');

global $adb;
global $fileId;

$attachmentsid = $_REQUEST['fileid'];
$entityid = $_REQUEST['entityid'];

$returnmodule=$_REQUEST['return_module'];

$dbQuery = "SELECT * FROM vtiger_attachments WHERE attachmentsid = " .$attachmentsid ;

$result = $adb->query($dbQuery) or die("Couldn't get file list");
if($adb->num_rows($result) == 1)
{
	$fileType = @$adb->query_result($result, 0, "type");
	$name = @$adb->query_result($result, 0, "name");
	$filepath = @$adb->query_result($result, 0, "path");

	$saved_filename = $attachmentsid."_".$name;
	$filesize = filesize($filepath.$saved_filename);
	$fileContent = fread(fopen($filepath.$saved_filename, "r"), $filesize);

	header("Content-type: $fileType");
	header("Content-length: $filesize");
	header("Cache-Control: private");
	header("Content-Disposition: attachment; filename=$name");
	header("Content-Description: PHP Generated Data");
	echo $fileContent;
}
else
{
	echo "Record doesn't exist.";
}
?>
