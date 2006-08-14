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
require_once('include/database/PearDatabase.php');
global $adb;

if(isset($_REQUEST['hour_format']) && $_REQUEST['hour_format'] != '')
	$hour_format = $_REQUEST['hour_format'];
else
	$hour_format = 'am/pm';
$delquery = "delete from vtiger_sharedcalendar where userid=".$_REQUEST["current_userid"];
$adb->query($delquery);
$sharedid = $_REQUEST['user'];
if(isset($sharedid) && $sharedid != null)
{
        foreach($sharedid as $sid)
        {
                if($sid != '')
                {
			$sql = "insert into vtiger_sharedcalendar values (".$_REQUEST["current_userid"].",".$sid.")";
		        $adb->query($sql);
                }
        }
}
if(isset($_REQUEST['start_hour']) && $_REQUEST['start_hour'] != '')
{
	$sql = "update vtiger_users set start_hour='".$_REQUEST['start_hour']."' where id=".$current_user->id;
        $adb->query($sql);
}

$sql = "update vtiger_users set hour_format='".$hour_format."' where id=".$current_user->id;
$adb->query($sql);
RecalculateSharingRules();
header("Location: index.php?action=index&module=Calendar&view=".$_REQUEST['view']."&hour=".$_REQUEST['hour']."&day=".$_REQUEST['day']."&month=".$_REQUEST['month']."&year=".$_REQUEST['year']."&viewOption=".$_REQUEST['viewOption']."&subtab=".$_REQUEST['subtab']."&parenttab=".$_REQUEST['parenttab']);

?>

