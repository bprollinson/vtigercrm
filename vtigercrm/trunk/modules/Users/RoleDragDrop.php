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

require_once('include/utils/UserInfoUtil.php');
$toid=$_REQUEST['parentId'];
$fromid=$_REQUEST['childId'];


global $adb;
$query = "select * from vtiger_role where vtiger_roleid='".$toid."'";
$result=$adb->query($query);
$parentRoleList=$adb->query_result($result,0,'parentrole');
$replace_with=$parentRoleList;
$orgDepth=$adb->query_result($result,0,'depth');

//echo 'replace with is '.$replace_with;
//echo '<BR>org depth '.$orgDepth;
$parentRoles=explode('::',$parentRoleList);

if(in_array($fromid,$parentRoles))
{
	echo 'You cannot move a Parent Node under a Child Node';
        die;
}


$roleInfo=getRoleAndSubordinatesInformation($fromid);

$fromRoleInfo=$roleInfo[$fromid];
$replaceToStringArr=explode('::'.$fromid,$fromRoleInfo[1]);
$replaceToString=$replaceToStringArr[0];
//echo '<BR>to be replaced string '.$replaceToString;


$stdDepth=$fromRoleInfo['2'];
//echo '<BR> std depth '.$stdDepth;

//Constructing the query
foreach($roleInfo as $mvRoleId=>$mvRoleInfo)
{
	$subPar=explode($replaceToString,$mvRoleInfo[1]);
	$mvParString=$replace_with.$subPar[1];
	$subDepth=$mvRoleInfo[2];
	$mvDepth=$orgDepth+(($subDepth-$stdDepth)+1);
	$query="update vtiger_role set parentrole='".$mvParString."',depth=".$mvDepth." where vtiger_roleid='".$mvRoleId."'";
	//echo $query;
	$adb->query($query);
		
}



header("Location: index.php?action=UsersAjax&module=Users&file=listroles&ajax=true");
?>
