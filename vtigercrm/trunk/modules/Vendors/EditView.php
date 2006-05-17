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
require_once('include/utils/utils.php');
require_once('modules/Vendors/Vendor.php');
require_once('include/FormValidationUtil.php');

global $app_strings,$mod_strings,$theme,$currentModule;

$focus = new Vendor();
$smarty = new vtigerCRM_Smarty();

if(isset($_REQUEST['record']) && $_REQUEST['record'] != '') 
{
	$focus->id = $_REQUEST['record'];
	$focus->mode = 'edit'; 	
	$focus->retrieve_entity_info($_REQUEST['record'],"Vendors");
	$focus->name = $focus->column_fields['vendorname'];
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') 
{
	$focus->id = "";
    	$focus->mode = ''; 	
} 

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$disp_view = getView($focus->mode);
if($disp_view == 'edit_view')
	$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields));
else	
{
	$bas_block = getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'BAS');
	$adv_block = getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'ADV');

	$blocks['basicTab'] = $bas_block;
	if(is_array($adv_block ))
		$blocks['moreTab'] = $adv_block;

	$smarty->assign("BLOCKS",$blocks);
	$smarty->assign("BLOCKS_COUNT",count($blocks));
}
$smarty->assign("OP_MODE",$disp_view);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",$app_strings['Vendor']);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$smarty->assign("ID", $focus->id);
if(isset($focus->name))
        $smarty->assign("NAME", $focus->name);

if(isset($cust_fld))
{
        $smarty->assign("CUSTOMFIELD", $cust_fld);
}

$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));
if($focus->mode == 'edit')
{
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
        $smarty->assign("MODE", $focus->mode);
}

if(isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", $_REQUEST['return_module']);
if(isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", $_REQUEST['return_action']);
if(isset($_REQUEST['return_id'])) $smarty->assign("RETURN_ID", $_REQUEST['return_id']);
if(isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", $_REQUEST['return_viewname']);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);


$vendor_tables = Array('vendor'); 

$validationData = getDBValidationData($vendor_tables);
$data = split_validationdataArray($validationData);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);
$smarty->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);


$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

if($focus->mode == 'edit')
	$smarty->display('InventoryEditView.tpl');
else
	$smarty->display('InventoryCreateView.tpl');

?>
