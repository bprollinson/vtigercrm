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
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');

global $adb;

$local_log =& LoggerManager::getLogger('index');
$focus = new Reports();

$rfid = $_REQUEST['record'];
$mode = $_REQUEST['savemode'];
$foldername = addslashes($_REQUEST["foldername"]);
$folderdesc = addslashes($_REQUEST["folderdesc"]);
$foldername = str_replace('*amp*','&',$foldername);
$folderdesc = str_replace('*amp*','&',$folderdesc);
if($mode=="Save")
{
	if($rfid=="")
	{
		$sql = "INSERT INTO reportfolder ";
		$sql .= "(FOLDERID,FOLDERNAME,DESCRIPTION,STATE) ";
		$sql .= "VALUES ('','".$foldername."','".$folderdesc."','CUSTOMIZED')";
		$result = $adb->query($sql);
		if($result!=false)
		{
			header("Location: index.php?action=ReportsAjax&file=ListView&mode=ajax&module=Reports");
		}else
		{
			include('themes/'.$theme.'/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while inserting the record</font>
			</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}elseif($mode=="Edit")
{
	if($rfid != "")
	{
		$sql = "update reportfolder set ";
		$sql .= "FOLDERNAME='".$foldername."', ";
		$sql .= "DESCRIPTION='".$folderdesc."' ";
		$sql .= "where folderid=".$rfid;
		$result = $adb->query($sql);
		if($result!=false)
		{
			header("Location: index.php?action=ReportsAjax&file=ListView&mode=ajax&module=Reports");
		}else
		{
			include('themes/'.$theme.'/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while updating the record</font>
			</ul></B></font> <br>" ;
			echo $errormessage;
		}   
	}
}

   	
?>
