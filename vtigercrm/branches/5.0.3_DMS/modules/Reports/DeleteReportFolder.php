<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;

$local_log =& LoggerManager::getLogger('index');
$rfid = $_REQUEST['record'];
if($rfid != "")
{
	$sql .= "delete from vtiger_reportfolder where folderid=".$rfid;
	$result = $adb->query($sql);
	if($result!=false)
	{
		header("Location: index.php?action=ReportsAjax&mode=ajax&file=ListView&module=Reports");
	}else
	{
		include('themes/'.$theme.'/header.php');
		$errormessage = "<font color='red'><B>Error Message<ul>
		<li><font color='red'>Error while deleting the record</font>
		</ul></B></font> <br>" ;
		echo $errormessage;
	}   
}


   	
?>
