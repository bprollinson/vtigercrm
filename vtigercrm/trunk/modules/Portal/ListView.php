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
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

global $app_strings;
global $mod_strings;
global $currentModule;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Portal');
global $adb;
	
$query="select * from portal";
$result=$adb->query($query);
$no_of_portals=$adb->num_rows($result);
$portal_info=array();
for($i=0 ; $i<$no_of_portals; $i++)
{
	$portalname = $adb->query_result($result,$i,'portalname');
	$portalurl = $adb->query_result($result,$i,'portalurl');
	$portal_array['portalid'] = $adb->query_result($result,$i,'portalid'); 
	$portal_array['portalname'] = $portalname;
	$portal_array['portalurl'] = $portalurl;
	$portal_info[]=$portal_array;
}
$smarty = new vtigerCRM_Smarty;

$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("PORTALS", $portal_info);
$smarty->assign("MODULE", $currentModule);
$smarty->assign("CATEGORY", getParentTab());
if($_REQUEST['datamode'] == 'data')
	$smarty->display("MySitesContents.tpl");
elseif($_REQUEST['datamode'] == 'manage')
	$smarty->display("MySitesManage.tpl");
else
	$smarty->display("MySites.tpl");
	
?>
