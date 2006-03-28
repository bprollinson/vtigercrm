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
 
   $sql = "select * from emailtemplates order by templateid DESC";
   $result = $adb->query($sql);
   $temprow = $adb->fetch_array($result);
   
$edit="Edit  ";
$del="Del  ";
$bar="  | ";
$cnt=1;

require_once('include/utils/UserInfoUtil.php');
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("UMOD", $mod_strings);
global $current_language;
$smod_strings = return_module_language($current_language,'Settings');
$smarty->assign("MOD", $smod_strings);
$smarty->assign("MODULE", 'Settings');
$smarty->assign("IMAGE_PATH", $image_path);

$return_data=array();
do
{
  $templatearray=array();
  $templatearray['templatename'] = $temprow["templatename"];
  $templatearray['templateid'] = $temprow["templateid"];
  $templatearray['description'] = $temprow["description"];
  $templatearray['foldername'] = $temprow["foldername"];
  $return_data[]=$templatearray;
  $cnt++;
}while($temprow = $adb->fetch_array($result));
$smarty->assign("TEMPLATES",$return_data);
$smarty->display("ListEmailTemplates.tpl");

?>
