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
$fld_module=$_REQUEST["fld_module"];
$tableName=$_REQUEST["table_name"];
$fldPickList =  $_REQUEST['listarea'];

//Deleting the already existing values
$delquery="delete from vtiger_".$tableName;
$adb->query($delquery);

$pickArray = explode("\n",$fldPickList);
$count = count($pickArray);

$tabname=explode('cf_',$tableName);

if($tabname[1]!='')
       	$custom=true;

for($i = 0; $i < $count; $i++)
{
	$pickArray[$i] = trim($pickArray[$i]);
	if($pickArray[$i] != '')
	{
		$query = "insert into vtiger_".$tableName." values('','".$pickArray[$i]."',".$i.",1)";
	        $adb->query($query);
	}
}
header("Location:index.php?action=SettingsAjax&module=Settings&directmode=ajax&file=PickList&fld_module=".$fld_module);
?>
