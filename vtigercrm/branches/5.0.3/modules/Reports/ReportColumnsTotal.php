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
require_once("data/Tracker.php");
require_once('Smarty_setup.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Reports/Reports.php');
require_once('include/database/PearDatabase.php');

global $app_strings;
global $app_list_strings;
global $mod_strings;
$current_module_strings = return_module_language($current_language, 'Reports');

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('report_type');

global $currentModule;
global $image_path;
global $theme;
$report_column_tot=new vtigerCRM_Smarty;
$report_column_tot->assign("MOD", $mod_strings);
$report_column_tot->assign("APP", $app_strings);
$report_column_tot->assign("IMAGE_PATH",$image_path);

if(isset($_REQUEST["record"]))
{
        $recordid = $_REQUEST["record"];
        $oReport = new Reports($recordid);
        $BLOCK1 = $oReport->sgetColumntoTotalSelected($oReport->primodule,$oReport->secmodule,$recordid);
		$report_column_tot->assign("BLOCK1",$BLOCK1);
}else
{
        $primarymodule = $_REQUEST["primarymodule"];
        $secondarymodule = $_REQUEST["secondarymodule"];
        $oReport = new Reports();
        $BLOCK1 = $oReport->sgetColumntoTotal($primarymodule,$secondarymodule);
		$report_column_tot->assign("BLOCK1",$BLOCK1);
}
$report_column_tot->assign("ROWS_COUNT", count($BLOCK1[0]));
$report_column_tot->display('ReportColumnsTotal.tpl');
?>
