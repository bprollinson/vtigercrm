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
require_once('Smarty_setup.php');
require_once('include/database/PearDatabase.php');


global $app_strings,$mod_strings,$current_user,$theme,$adb;
$image_path = 'themes/'.$theme.'/images/';
$idlist = $_REQUEST['idlist'];
$pmodule=$_REQUEST['return_module'];
$ids=explode(';',$idlist);
$single_record = false;
if(!strpos($idlist,':'))
{
	$single_record = true;
}
$smarty = new vtigerCRM_Smarty;

$userid =  $current_user->id;

$querystr = "select fieldid, fieldlabel, columnname from vtiger_field where tabid=".getTabid($pmodule)." and uitype=13";

$res=$adb->query($querystr);
$numrows = $adb->num_rows($res);
$returnvalue = Array();
for($i = 0; $i < $numrows; $i++)
{
	$value = Array();
	$fieldname = $adb->query_result($res,$i,"fieldname");
	$permit = getFieldVisibilityPermission($pmodule, $userid, $fieldname);
	if($permit == '0')
	{
		$temp=$adb->query_result($res,$i,'columnname');
		$columnlists [] = $temp;
		$fieldid=$adb->query_result($res,$i,'fieldid');
		$fieldlabel =$adb->query_result($res,$i,'fieldlabel');
		$value[] = getTranslatedString($fieldlabel);
		$returnvalue [$fieldid]= $value;
	}
}

if($single_record && count($columnlists) > 0)
{
	$count = 0;	
	switch($pmodule)
	{
		case 'Accounts':
			$query = 'select accountname,'.implode(",",$columnlists).' from vtiger_account left join vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid where vtiger_account.accountid = '.$idlist;
			$result=$adb->query($query);
		        foreach($columnlists as $columnname)	
			{
				$field_value[$count++] = $adb->query_result($result,0,$columnname);
			}
			$entity_name = $adb->query_result($result,0,'accountname');
			break;
		case 'Leads':
			$query = 'select concat(firstname," ",lastname) as leadname,'.implode(",",$columnlists).' from vtiger_leaddetails left join vtiger_leadscf on vtiger_leadscf.leadid = vtiger_leaddetails.leadid where vtiger_leaddetails.leadid = '.$idlist;
			$result=$adb->query($query);
		        foreach($columnlists as $columnname)	
			{
				$field_value[$count++] = $adb->query_result($result,0,$columnname);
			}
			$entity_name = $adb->query_result($result,0,'leadname');
			break;
		case 'Contacts':
			$query = 'select concat(firstname," ",lastname) as contactname,'.implode(",",$columnlists).' from vtiger_contactdetails left join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid where vtiger_contactdetails.contactid = '.$idlist;
			$result=$adb->query($query);
		        foreach($columnlists as $columnname)	
			{
				$field_value[$count++] = $adb->query_result($result,0,$columnname);
			}	
			$entity_name = $adb->query_result($result,0,'contactname');
			break;	
	}	
}
$smarty->assign('PERMIT',$permit);
$smarty->assign('ENTITY_NAME',$entity_name);
$smarty->assign('ONE_RECORD',$single_record);
$smarty->assign('MAILDATA',$field_value);
$smarty->assign('MAILINFO',$returnvalue);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("IDLIST", $idlist);
$smarty->assign("APP", $app_strings);
$smarty->assign("FROM_MODULE", $pmodule);
$smarty->assign("IMAGE_PATH",$image_path);

if(count($columnlists) > 0)
	$smarty->display("SelectEmail.tpl");
else
	echo "Mail Ids not permitted";	
?>
