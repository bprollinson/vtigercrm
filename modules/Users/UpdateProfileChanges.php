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
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
global $adb;
$profileid = $_REQUEST['profileid'];
$def_module = $_REQUEST['selected_module'];
$def_tab = $_REQUEST['selected_tab'];

if(isset($_REQUEST['return_action']) && $_REQUEST['return_action']!= '')
	$return_action =$_REQUEST['return_action'];
else
	$return_action = 'ListProfiles';

//Retreiving the vtiger_tabs permission array
$tab_perr_result = $adb->query("select * from vtiger_profile2tab where vtiger_profileid=".$profileid);
$act_perr_result = $adb->query("select * from vtiger_profile2standardpermissions where vtiger_profileid=".$profileid);
$act_utility_result = $adb->query("select * from vtiger_profile2utility where vtiger_profileid=".$profileid);
$num_tab_per = $adb->num_rows($tab_perr_result);
$num_act_per = $adb->num_rows($act_perr_result);
$num_act_util_per = $adb->num_rows($act_utility_result);

	//Updating vtiger_profile2global permissons vtiger_table
	$view_all_req=$_REQUEST['view_all'];
	$view_all = getPermissionValue($view_all_req);

	$edit_all_req=$_REQUEST['edit_all'];
	$edit_all = getPermissionValue($edit_all_req);

	$update_query = "update  vtiger_profile2globalpermissions set globalactionpermission=".$view_all." where globalactionid=1 and vtiger_profileid=".$profileid;
	$adb->query($update_query);
	$update_query = "update  vtiger_profile2globalpermissions set globalactionpermission=".$edit_all." where globalactionid=2 and vtiger_profileid=".$profileid;
	$adb->query($update_query);

	
	//profile2tab permissions
	for($i=0; $i<$num_tab_per; $i++)
	{
		$tab_id = $adb->query_result($tab_perr_result,$i,"tabid");
		$request_var = $tab_id.'_tab';
		if($tab_id != 3 && $tab_id != 16)
		{
			$permission = $_REQUEST[$request_var];
			if($permission == 'on')
			{
				$permission_value = 0;
			}
			else
			{
				$permission_value = 1;
			}
			$update_query = "update vtiger_profile2tab set permissions=".$permission_value." where vtiger_tabid=".$tab_id." and vtiger_profileid=".$profileid;
			$adb->query($update_query);
			if($tab_id ==9)
			{
				$update_query = "update vtiger_profile2tab set permissions=".$permission_value." where vtiger_tabid=16 and vtiger_profileid=".$profileid;
				$adb->query($update_query);
			}
		}
	}
	
	//profile2standard permissions	
	for($i=0; $i<$num_act_per; $i++)
	{
		$tab_id = $adb->query_result($act_perr_result,$i,"tabid");
		if($tab_id != 16)
		{
			$action_id = $adb->query_result($act_perr_result,$i,"operation");
			$action_name = getActionname($action_id);
			if($action_name == 'EditView' || $action_name == 'Delete' || $action_name == 'DetailView')
			{
				$request_var = $tab_id.'_'.$action_name;
			}
			elseif($action_name == 'Save')
			{
				$request_var = $tab_id.'_EditView';
			}
			elseif($action_name == 'index')
			{
				$request_var = $tab_id.'_DetailView';
			}

			$permission = $_REQUEST[$request_var];
			if($permission == 'on')
			{
				$permission_value = 0;
			}
			else
			{
				$permission_value = 1;
			}
			$update_query = "update vtiger_profile2standardpermissions set permissions=".$permission_value." where vtiger_tabid=".$tab_id." and Operation=".$action_id." and vtiger_profileid=".$profileid;
			$adb->query($update_query);
			if($tab_id ==9)
			{
				$update_query = "update vtiger_profile2standardpermissions set permissions=".$permission_value." where vtiger_tabid=16 and Operation=".$action_id." and vtiger_profileid=".$profileid;
				$adb->query($update_query);
			}



		}
	}

	//Update Profile 2 utility
	for($i=0; $i<$num_act_util_per; $i++)
	{
		$tab_id = $adb->query_result($act_utility_result,$i,"tabid");

		$action_id = $adb->query_result($act_utility_result,$i,"activityid");
		$action_name = getActionname($action_id);
		$request_var = $tab_id.'_'.$action_name;


		$permission = $_REQUEST[$request_var];
		if($permission == 'on')
		{
			$permission_value = 0;
		}
		else
		{
			$permission_value = 1;
		}

		$update_query = "update vtiger_profile2utility set permission=".$permission_value." where vtiger_tabid=".$tab_id." and vtiger_activityid=".$action_id." and vtiger_profileid=".$profileid;

		$adb->query($update_query);


	}



	$modArr=getFieldModuleAccessArray(); 

foreach($modArr as $fld_module => $fld_label)
{
	$fieldListResult = getProfile2FieldList($fld_module, $profileid);
	$noofrows = $adb->num_rows($fieldListResult);
	$tab_id = getTabid($fld_module);
	for($i=0; $i<$noofrows; $i++)
	{
		$fieldid =  $adb->query_result($fieldListResult,$i,"fieldid");
		$visible = $_REQUEST[$fieldid];
		if($visible == 'on')
		{
			$visible_value = 0;
		}
		else
		{
			$visible_value = 1;
		}
		//Updating the Mandatory vtiger_fields
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");
		if($uitype == 2 || $uitype == 51 || $uitype == 6 || $uitype == 22 || $uitype == 73 || $uitype				== 24 || $uitype == 81 || $uitype == 50 || $uitype == 23 || $uitype == 16)
		{
			$visible_value = 0;
		}
		//Updating the database
		$update_query = "update vtiger_profile2field set visible=".$visible_value." where vtiger_fieldid='".$fieldid."' and vtiger_profileid=".$profileid." and vtiger_tabid=".$tab_id;
		$adb->query($update_query);

	}
}
	$loc = "Location: index.php?action=".$return_action."&module=Users&mode=view&parenttab=Settings&profileid=".$profileid."&selected_tab=".$def_tab."&selected_module=".$def_module;
	header($loc);

function getPermissionValue($req_per)
{
	if($req_per == 'on')
	{
		$permission_value = 0;
	}
	else
	{
		$permission_value = 1;
	}
	return $permission_value;
}

?>
