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

//Getting the Parameters from the Convert Form
$id = $_REQUEST["record"];
$module = $_REQUEST["module"];
$assigned_user_id = $_REQUEST["assigned_user_id"];
$createpotential = $_REQUEST["createpotential"];
$potential_name = $_REQUEST["potential_name"];
$close_date = $_REQUEST["closedate"];
$current_user_id = $_REQUEST["current_user_id"];

//$id=$adb->query_result($adb->query('select * from vtiger_users where user_name="'.$_REQUEST['assigned_user_id'].'"'),0,"id");

$idlist=split(';',$_REQUEST['idlist']);

$query='select * from vtiger_troubletickets';
$rs=$adb->query($query);
for($i=0;$i<count($idlist);$i++)
{
	$id=$idlist[$i];
	if(isset($_REQUEST['change_status']))
	{
		$changevalue=$_REQUEST['status'];
		$changekey='status';
	}
	if(isset($_REQUEST['change_owner']))
	{
		$resultset=$adb->query('select * from vtiger_users where user_name="'.$_REQUEST['assigned_user_id'].'"');
		$changevalue=$adb->query_result($resultset,0,"id");
		$changekey='assigned_user_id';
	}
	$sql="update vtiger_troubletickets set ".$changekey." = '".$changevalue."' where id = '".$id."'";
	$adb->query($sql);
}
header("Location: index.php?action=index&module=HelpDesk");

?>
