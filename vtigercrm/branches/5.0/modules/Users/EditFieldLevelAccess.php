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
require_once('XTemplate/xtpl.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');

global $mod_strings;
global $app_strings;
global $app_list_strings;

echo '<form action="index.php" method="post" name="new" id="form">';
echo get_module_title("Users", $_REQUEST['fld_module'].': '.$mod_strings['LBL_FIELD_LEVEL_ACCESS'], true);
echo '<BR>';
//echo get_form_header("Standard Fields", "", false );

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$fld_module = $_REQUEST["fld_module"];
//Retreiving the fields array

$xtpl=new XTemplate ('modules/Users/EditFieldLevelAccess.html');

$profileid = $_REQUEST['profileid'];

$fieldListResult = getProfile2FieldList($fld_module, $profileid);
$noofrows = $adb->num_rows($fieldListResult);
$standCustFld = getStdOutput($fieldListResult, $noofrows, $mod_strings,$profileid);

//Standard PickList Fields
function getStdOutput($fieldListResult, $noofrows, $mod_strings,$profileid)
{
	global $adb;
	$standCustFld= '';
	$standCustFld .= '<input type="hidden" name="fld_module" value="'.$_REQUEST['fld_module'].'">';
	$standCustFld .= '<input type="hidden" name="module" value="Users">';
	$standCustFld .= '<input type="hidden" name="profileid" value="'.$profileid.'">';
	$standCustFld .= '<input type="hidden" name="action" value="UpdateFieldLevelAccess">';
	$standCustFld .= '<BR>';
	$standCustFld .= '<table border="0" cellpadding="0" cellspacing="0" width="40%"><tr><td>';
	$standCustFld .= get_form_header($mod_strings['LBL_FIELD_PERMISSIOM_TABLE_HEADER'], "", false );
	$standCustFld .= '</td></tr></table>';
	$standCustFld .= '<table border="0" cellpadding="5" cellspacing="1" class="FormBorder" width="40%">';
	$standCustFld .=  '<tr class="ModuleListTitle" height=20>';
	$standCustFld .=  '<td width="50%" nowrap class="moduleListTitle" height="21" style="padding:0px 3px 0px 3px;"><b>'.$mod_strings['LBL_FIELD_PERMISSION_FIELD_NAME'].'</b></td>';
	$standCustFld .=  '<td class="moduleListTitle" style="padding:0px 3px 0px 3px;"><div align="center"><b>'.$mod_strings['LBL_FIELD_PERMISSION_VISIBLE'].'</b></div></td>';
	$standCustFld .=  '</tr>';
	
	$row=1;
	for($i=0; $i<$noofrows; $i++,$row++)
	{
		if ($row%2==0)
		{
			$trowclass = 'evenListRow';
		}
		else
		{	
			$trowclass = 'oddListRow';
		}

		$standCustFld .= '<tr class="'.$trowclass.'">';
		
		$uitype = $adb->query_result($fieldListResult,$i,"uitype");
                $mandatory = '';
		$readonly = '';
                if($uitype == 2 || $uitype == 51 || $uitype == 6 || $uitype == 22 || $uitype == 73 || $uitype == 24 || $uitype == 81 || $uitype == 50 || $uitype == 23 || $uitype == 16)
                {
                        $mandatory = '<font color="red">*</font>';
			$readonly = 'disabled';
                }

		$standCustFld .= '<td height="21" style="padding:0px 3px 0px 3px;">'.$mandatory.' '.$adb->query_result($fieldListResult,$i,"fieldlabel").'</td>';
		if($adb->query_result($fieldListResult,$i,"visible") == 0)
		{
			$visible = "checked";
		}
		else
		{
			$visible = "";
		}	
		$standCustFld .= '<td height="21" style="padding:0px 3px 0px 3px;"><div align="center"><input type="checkbox" name="'.$adb->query_result($fieldListResult,$i,"fieldid").'" '.$visible.' '.$readonly.'></div></td></tr>';
		
	}
	$standCustFld .= '</table>';
	$standCustFld .= '<br><div align="center" style="width:40%"><input title="Update" accessKey="C" class="button" type="submit" name="Update" value="'.$mod_strings['LBL_BUTTON_UPDATE'].'"></div>';
	$standCustFld .= '</form>';
	//echo $standCustFld;
	return $standCustFld;
}
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("STANDARDFIELDS", $standCustFld);
$xtpl->parse("main");
$xtpl->out("main");


?>
