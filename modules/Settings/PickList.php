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
require_once('database/DatabaseConnection.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_language, $currentModule;

if(isset($_REQUEST['fld_module']) && $_REQUEST['fld_module'] != '')
{
	$fld_module = $_REQUEST['fld_module'];
	$roleid = $_REQUEST['roleid'];
}
else
{
	$fld_module = 'Potentials';
	$roleid='H2';
}

if(isset($_REQUEST['uitype']) && $_REQUEST['uitype'] != '')
	$uitype = $_REQUEST['uitype'];

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MODULE_LISTS",getPickListModules());
$smarty->assign("ROLE_LISTS",getrole2picklist());

$picklists_entries = getUserFldArray($fld_module,$roleid);
if((sizeof($picklists_entries) %3) != 0)
	$value = (sizeof($picklists_entries) + 3 - (sizeof($picklists_entries))%3); 
else
	$value = sizeof($picklists_entries);

if($fld_module == 'Events')

	$temp_module_strings = return_module_language($current_language, 'Calendar');
else
	$temp_module_strings = return_module_language($current_language, $fld_module);

$smarty->assign("TEMP_MOD", $temp_module_strings);
$picklist_fields = array_chunk(array_pad($picklists_entries,$value,''),3);
$smarty->assign("MODULE",$fld_module);
$smarty->assign("PICKLIST_VALUES",$picklist_fields);
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("UITYPE", $uitype);

if($_REQUEST['directmode'] != 'ajax')
	$smarty->display("Settings/PickList.tpl");
else
	$smarty->display("Settings/PickListContents.tpl");
	
	/** Function to get picklist fields for the given module 
	 *  @ param $fld_module
	 *  It gets the picklist details array for the given module in the given format
	 *  			$fieldlist = Array(Array('fieldlabel'=>$fieldlabel,'generatedtype'=>$generatedtype,'columnname'=>$columnname,'fieldname'=>$fieldname,'value'=>picklistvalues))	
	 */

function getUserFldArray($fld_module,$roleid)
{
	global $adb;
	$user_fld = Array();
	$tabid = getTabid($fldmodule);
	$query="select vtiger_field.fieldlabel,vtiger_field.columnname,vtiger_field.fieldname, vtiger_field.uitype, vtiger_role2picklist.* from vtiger_field inner join vtiger_picklist on vtiger_field.fieldname = vtiger_picklist.name inner join vtiger_role2picklist on vtiger_role2picklist.picklistid = vtiger_picklist.picklistid where displaytype in(1,5) and vtiger_field.tabid=? and vtiger_field.uitype in (15,16,111,55,33) or  (vtiger_field.tabid=? and fieldname='salutationtype' and fieldname !='vendortype') and vtiger_role2picklist.roleid=? group by vtiger_field.fieldname order by vtiger_picklist.picklistid ASC";
	//$query = "select fieldlabel,generatedtype,columnname,fieldname,uitype from vtiger_field where displaytype = 1 and (tabid = ".getTabid($fld_module)." AND uitype IN (15,16, 111,33)) OR (tabid = ".getTabid($fld_module)." AND fieldname='salutationtype')";
	$params = array(getTabid($fld_module), getTabid($fld_module), $roleid);
	$result = $adb->pquery($query, $params);
	$noofrows = $adb->num_rows($result);
    if($noofrows > 0)
    {
		$fieldlist = Array();
    	for($i=0; $i<$noofrows; $i++)
    	{
			$user_fld = Array();
			$fld_name = $adb->query_result($result,$i,"fieldname");
			if($fld_module == 'Events')	
			{
				if($adb->query_result($result,$i,"fieldname") != 'recurringtype' && $adb->query_result($result,$i,"fieldname") != 'activitytype' && $adb->query_result($result,$i,"fieldname") != 'visibility')	
				{	
					$user_fld['fieldlabel'] = $adb->query_result($result,$i,"fieldlabel");	
					$user_fld['generatedtype'] = $adb->query_result($result,$i,"generatedtype");	
					$user_fld['columnname'] = $adb->query_result($result,$i,"columnname");	
					$user_fld['fieldname'] = $adb->query_result($result,$i,"fieldname");	
					$user_fld['uitype'] = $adb->query_result($result,$i,"uitype");	
					$user_fld['value'] = getPickListValues($user_fld['fieldname'],$roleid); 
					$fieldlist[] = $user_fld;
				}
			}
			else
			{
					$user_fld['fieldlabel'] = $adb->query_result($result,$i,"fieldlabel");	
					$user_fld['generatedtype'] = $adb->query_result($result,$i,"generatedtype");	
					$user_fld['columnname'] = $adb->query_result($result,$i,"columnname");	
					$user_fld['fieldname'] = $adb->query_result($result,$i,"fieldname");	
					$user_fld['uitype'] = $adb->query_result($result,$i,"uitype");	
					$user_fld['value'] = getPickListValues($user_fld['fieldname'],$roleid); 
					$fieldlist[] = $user_fld;
			}
    	}
    }
    return $fieldlist;
}

	/** Function to get picklist values for the given field  
	 *  @ param $tablename
	 *  It gets the picklist values for the given fieldname
	 *  			$fldVal = Array(0=>value,1=>value1,-------------,n=>valuen)	
	 */

function getPickListValues($tablename,$roleid)
{
	global $adb;
	$query = "select $tablename from vtiger_$tablename inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$tablename.picklist_valueid where roleid=? and picklistid in (select picklistid from vtiger_$tablename) order by sortid";
	$result = $adb->pquery($query, array($roleid));
	$fldVal = Array();
	while($row = $adb->fetch_array($result))
	{
		$fldVal []= $row[$tablename];
	}
	return $fldVal;
}
	/** Function to get modules which has picklist values  
	 *  It gets the picklist modules and return in an array in the following format 
	 *  			$modules = Array($tabid=>$tablabel,$tabid1=>$tablabel1,$tabid2=>$tablabel2,-------------,$tabidn=>$tablabeln)	
	 */
function getPickListModules()
{
	global $adb;
	$query = 'select distinct vtiger_field.fieldname,vtiger_field.tabid,tablabel,uitype from vtiger_field inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid where uitype IN (15,16, 111,33) and vtiger_field.tabid != 29 order by vtiger_field.tabid ASC';
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result))
	{
		$modules[$row['tabid']] = $row['tablabel']; 
	}
	return $modules;
}
function getrole2picklist()
{
	global $adb;
	$query = "select rolename,roleid from vtiger_role where roleid not in('H1') order by roleid";
	$result = $adb->pquery($query, array());
	while($row = $adb->fetch_array($result))
	{
		$role[$row['roleid']] = $row['rolename'];
	}
	return $role;

}
?>
