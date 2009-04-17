<?PHP
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

global $mod_strings,$app_strings,$theme,$adb;
$smarty = new vtigerCRM_Smarty;

$module = $_REQUEST['formodule'];

$menu_array = Array();

//if(layout editor is permitted)
$menu_array['LayoutEditor']['location'] = 'index.php?module=Settings&action=LayoutBlockList&parenttab=Settings&formodule='.$module;
$menu_array['LayoutEditor']['image_src'] = vtiger_imageurl('orgshar.gif', $theme);
$menu_array['LayoutEditor']['desc'] = getTranslatedString('LBL_LAYOUT_EDITOR_DESCRIPTION');
$menu_array['LayoutEditor']['label'] = getTranslatedString('LBL_LAYOUT_EDITOR');

if(vtlib_isModuleActive('FieldFormulas')) {
	$sql_result = $adb->pquery("select * from vtiger_settings_field where name = ? and active=0",array('LBL_FIELDFORMULAS'));
	if($adb->num_rows($sql_result) > 0) {
		$menu_array['FieldFormulas']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&formodule='.$module;
		$menu_array['FieldFormulas']['image_src'] = $adb->query_result($sql_result, 0, 'iconpath');
		$menu_array['FieldFormulas']['desc'] = getTranslatedString($adb->query_result($sql_result, 0, 'description'));
		$menu_array['FieldFormulas']['label'] = getTranslatedString($adb->query_result($sql_result, 0, 'name'));
			}
}

$sql_result = $adb->pquery("select * from vtiger_settings_field where name = ? and active=0",array('LBL_TOOLTIP_MANAGEMENT'));
if($adb->num_rows($sql_result) > 0) {
	$menu_array['Tooltip']['location'] = $adb->query_result($sql_result, 0, 'linkto').'&formodule='.$module;
	$menu_array['Tooltip']['image_src'] = vtiger_imageurl($adb->query_result($sql_result, 0, 'iconpath'), $theme);
	$menu_array['Tooltip']['desc'] = $mod_strings[$adb->query_result($sql_result, 0, 'description')];
	$menu_array['Tooltip']['label'] = $mod_strings[$adb->query_result($sql_result, 0, 'name')];
}

$menu_array['LeadsMapping']['location'] = 'index.php?module=Settings&action=CustomFieldList&parenttab=Settings&formodule='.$module;
$menu_array['LeadsMapping']['image_src'] = vtiger_imageurl('custom.gif', $theme);
$menu_array['LeadsMapping']['desc'] = getTranslatedString('LEADS_CUSTOM_FIELD_MAPPING_DESCRIPTION');
$menu_array['LeadsMapping']['label'] = getTranslatedString('LEADS_CUSTOM_FIELD_MAPPING');


//add blanks for 3-column layout
$count = count($menu_array)%3;
if($count>0) {
	for($i=0;$i<3-$count;$i++) {
		$menu_array[] = array();
	}
}
	
$smarty->assign("MOD",$mod_strings);
$smarty->assign("APP",$app_strings);
$smarty->assign("IMAGE_PATH", "themes/$theme/images/");
$smarty->assign('MODULE',$module);
$smarty->assign('MODULE_LBL',getTranslatedString($module));
$smarty->assign('MENU_ARRAY', $menu_array);

$smarty->display(vtlib_getModuleTemplate('Vtiger','Settings.tpl'));


?>
