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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Potentials/DetailView.php,v 1.28 2005/04/18 10:37:49 samk Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Potentials/Potentials.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

global $mod_strings;
global $app_strings;
global $currentModule, $singlepane_view;

$focus = new Potentials();
$smarty = new vtigerCRM_Smarty;

if(isset($_REQUEST['record'])  && $_REQUEST['record']!='') {
    $focus->retrieve_entity_info($_REQUEST['record'],"Potentials");
    $focus->id = $_REQUEST['record'];	
    $focus->name=$focus->column_fields['potentialname'];
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
} 

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$log->info("Potential detail view");

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $focus->id);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));

$smarty->assign("ACCOUNTID",$focus->column_fields['account_id']);

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");

$smarty->assign("BLOCKS", getBlocks($currentModule,"detail_view",'',$focus->column_fields));

$smarty->assign("CUSTOMFIELD", $cust_fld);
$smarty->assign("SINGLE_MOD", 'Opportunity');
$category = getParentTab();
$smarty->assign("CATEGORY",$category);


if(isPermitted("Potentials","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");
if(isPermitted("Invoice","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("CONVERTINVOICE","permitted");
if(isPermitted("Potentials","Delete",$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");

$tabid = getTabid("Potentials");
$validationData = getDBValidationData($focus->tab_name,$tabid);
$data = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);
       
$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->assign("CONVERTMODE",'potentoinvoice');
$smarty->assign("MODULE","Potentials");
$smarty->assign("EDIT_PERMISSION",isPermitted($currentModule,'EditView',$_REQUEST[record]));

if($singlepane_view == 'true')
{
	$related_array = getRelatedLists($currentModule,$focus);
	$smarty->assign("RELATEDLISTS", $related_array);
}

$smarty->assign("IS_REL_LIST",isPresentRelatedLists($currentModule));

$smarty->assign("SinglePane_View", $singlepane_view);
$smarty->assign("TODO_PERMISSION",CheckFieldPermission('parent_id','Calendar'));
$smarty->assign("EVENT_PERMISSION",CheckFieldPermission('parent_id','Events'));

if(isset($_SESSION['potentials_listquery'])){
	$arrayTotlist = array();
	$aNamesToList = array(); 
	$forAllCRMIDlist_query=$_SESSION['potentials_listquery'];
	$resultAllCRMIDlist_query=$adb->pquery($forAllCRMIDlist_query,array());
	while($forAllCRMID = $adb->fetch_array($resultAllCRMIDlist_query))
	{
		$arrayTotlist[]=$forAllCRMID['crmid'];
	}
	$_SESSION['listEntyKeymod'] = $module.":".implode(",",$arrayTotlist);
	if(isset($_SESSION['listEntyKeymod']))
	{
	$split_temp=explode(":",$_SESSION['listEntyKeymod']);
		if($split_temp[0] == $module)
		{	
			$smarty->assign("SESMODULE",$split_temp[0]);
			$ar_allist=explode(",",$split_temp[1]);
			
			for($listi=0;$listi<count($ar_allist);$listi++)
			{
				if($ar_allist[$listi]==$_REQUEST[record])
				{
					if($listi-1>=0)
					{
						$privrecord=$ar_allist[$listi-1];
						$smarty->assign("privrecord",$privrecord);
					}else {unset($privrecord);}
					if($listi+1<count($ar_allist))
					{
						$nextrecord=$ar_allist[$listi+1];
						$smarty->assign("nextrecord",$nextrecord);
					}else {unset($nextrecord);}
					break;
				}
				
			}
		}
	}
}
$smarty->display("DetailView.tpl");
?>
