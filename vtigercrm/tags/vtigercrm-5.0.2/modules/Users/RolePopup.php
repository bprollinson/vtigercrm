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
require_once('Smarty_setup.php');
$smarty = new vtigerCRM_Smarty;

global $mod_strings;
global $app_strings;
global $app_list_strings;



global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";


//Retreiving the hierarchy
$hquery = "select * from vtiger_role order by parentrole asc";
$hr_res = $adb->query($hquery);
$num_rows = $adb->num_rows($hr_res);
$hrarray= Array();

for($l=0; $l<$num_rows; $l++)
{
	$roleid = $adb->query_result($hr_res,$l,'roleid');
	$parent = $adb->query_result($hr_res,$l,'parentrole');
	$temp_list = explode('::',$parent);
	$size = sizeof($temp_list);
	$i=0;
	$k= Array();
	$y=$hrarray;
	if(sizeof($hrarray) == 0)
	{
		$hrarray[$temp_list[0]]= Array();
	}
	else
	{
		while($i<$size-1)
		{
			$y=$y[$temp_list[$i]];
			$k[$temp_list[$i]] = $y;
			$i++;

		}
		$y[$roleid] = Array();
		$k[$roleid] = Array();

		//Reversing the Array
		$rev_temp_list=array_reverse($temp_list);
		$j=0;
		//Now adding this into the main array
		foreach($rev_temp_list as $value)
		{
			if($j == $size-1)
			{
				$hrarray[$value]=$k[$value];
			}
			else
			{
				$k[$rev_temp_list[$j+1]][$value]=$k[$value];
			}
			$j++;
		}
	}

}
//Constructing the Roledetails array
$role_det = getAllRoleDetails();
$query = "select * from vtiger_role";
$result = $adb->query($query);
$num_rows=$adb->num_rows($result);
$mask_roleid=Array();
$del_roleid=$_REQUEST['maskid'];
if($del_roleid != '' && strlen($del_roleid) >0)
{
	$mask_roleid= getRoleAndSubordinatesRoleIds($del_roleid);
}	
$roleout ='';
$roleout .= indent($hrarray,$roleout,$role_det,$mask_roleid);

/** recursive function to construct the role tree ui 
  * @param $hrarray -- Hierarchial role tree array with only the roleid:: Type array
  * @param $roleout -- html string ouput of the constucted role tree ui:: Type varchar 
  * @param $role_det -- Roledetails array got from calling getAllRoleDetails():: Type array
  * @param $mask_roleid -- role id to be masked from selecting in the tree:: Type integer 
  * @returns $role_out -- html string ouput of the constucted role tree ui:: Type string
  *
 */
function indent($hrarray,$roleout,$role_det,$mask_roleid='')
{
	global $theme;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	foreach($hrarray as $roleid => $value)
	{
	
		//retreiving the vtiger_role details
		$role_det_arr=$role_det[$roleid];
		$roleid_arr=$role_det_arr[2];
		$rolename = $role_det_arr[0];
		$roledepth = $role_det_arr[1]; 
		$roleout .= '<ul class="uil" id="'.$roleid.'" style="display:block;list-style-type:none;">';
		$roleout .=  '<li >';
		if(sizeof($value) >0 && $roledepth != 0)
		{	
			$roleout .= '<img src="'.$image_path.'/minus.gif" id="img_'.$roleid.'" border="0"  alt="Expand/Collapse" title="Expand/Collapse" align="absmiddle" onClick="showhide(\''.$roleid_arr.'\',\'img_'.$roleid.'\')" style="cursor:pointer;">';
		}
		else if($roledepth != 0){
			$roleout .= '<img src="'.$image_path.'/vtigerDevDocs.gif" id="img_'.$roleid.'" border="0"  alt="Expand/Collapse" title="Expand/Collapse" align="absmiddle">';	
		}
		else
		{
			$roleout .= '<img src="'.$image_path.'/menu_root.gif" id="img_'.$roleid.'" border="0"  alt="Root" title="Root" align="absmiddle">';
		}
		if($roledepth == 0 || in_array($roleid,$mask_roleid))
		{
			$roleout .= '&nbsp;<b class="genHeaderGray">'.$rolename.'</b>';
		}
		else
		{
			$roleout .= '&nbsp;<a href="javascript:loadValue(\'user_'.$roleid.'\',\''.$roleid.'\');" class="x" id="user_'.$roleid.'">'.$rolename.'</a>';
		}
 		$roleout .=  '</li>';
		if(sizeof($value) > 0 )
		{
			$roleout = indent($value,$roleout,$role_det,$mask_roleid);
		}

		$roleout .=  '</ul>';

	}

	return $roleout;
}
$smarty->assign("THEME",$theme_path);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("ROLETREE", $roleout);
$smarty->display("RolePopup.tpl");
?>
