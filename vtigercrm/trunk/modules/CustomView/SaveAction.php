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

$cvid = $_REQUEST["cvid"];
$cvmodule = $_REQUEST["cvmodule"];
$mode = $_REQUEST["mode"];
$subject = addslashes($_REQUEST["subject"]);
$body = addslashes($_REQUEST["body"]);

if($cvid != "")
{
	if($mode == "new")
	{
		$customactionsql = "insert into customaction(cvid,subject,module,content)";
		$customactionsql .= " values(".$cvid.",'".$subject."','".$cvmodule."','".$body."')";
		$customactionresult = $adb->query($customactionsql);
		if($customactionresult == false)
		{
			include('themes/'.$theme.'/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;

		}

	}elseif($mode == "edit")
	{
		$updatecasql = "update customaction set subject='".$subject."',content='".$body."' where cvid=".$cvid;
		$updatecaresult = $adb->query($updatecasql);
		if($updatecaresult == false)
		{
			include('themes/'.$theme.'/header.php');
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
		}
	}
}
header("Location: index.php?action=index&module=$cvmodule&viewname=$cvid");
?>
