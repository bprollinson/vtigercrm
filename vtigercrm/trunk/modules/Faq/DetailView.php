<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  TODO To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('modules/Faq/Faq.php');
require_once('include/CustomFieldUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

global $mod_strings;
global $app_strings;

$focus = new Faq();

if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) 
{
    $focus->retrieve_entity_info($_REQUEST['record'],"Faq");
    $focus->id = $_REQUEST['record'];	
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') 
{
	$focus->id = "";
} 

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$log->info("Faq detail view");
$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));

if(isset($focus->column_fields[question]))
	$smarty->assign("FAQ_TITLE", $focus->column_fields[question]);
if(isset($_REQUEST['category']) && $_REQUEST['category'] !='')
{
            $category = $_REQUEST['category'];
}
else
{
            $category = getParentTabFromModule($currentModule);
}
$smarty->assign("CATEGORY",$category);

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("BLOCKS", getBlocks("Faq","detail_view",'',$focus->column_fields));
$smarty->assign("SINGLE_MOD","Faq");
$smarty->assign("MODULE","Faq");

$smarty->assign("ID", $_REQUEST['record']);
if(isPermitted("Faq",1,$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");

if(isPermitted("Faq",2,$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");	

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);
	
$smarty->display("DetailView.tpl");
?>
