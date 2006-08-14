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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/DetailView.php,v 1.12 2005/03/17 11:26:49 ray Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Calendar/Activity.php');
require_once('include/CustomFieldUtil.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('modules/Calendar/calendarLayout.php');
include_once 'modules/Calendar/header.php';
global $mod_strings, $currentModule;
if( $_SESSION['mail_send_error']!="")
{
	echo '<b><font color=red>'. $mod_strings{"LBL_NOTIFICATION_ERROR"}.'</font></b><br>';
}
session_unregister('mail_send_error');
$focus = new Activity();
$smarty =  new vtigerCRM_Smarty();
$activity_mode = $_REQUEST['activity_mode'];
//If activity_mode == null

if($activity_mode =='' || strlen($activity_mode) < 1)
{
	$query = "select activitytype from vtiger_activity where activityid=".$_REQUEST['record'];
	$result = $adb->query($query);
	$actType = $adb->query_result($result,0,'activitytype');
	if( $actType == 'Task')
	{
		$activity_mode = $actType;	
	}
	elseif($actType == 'Meeting' || $actType == 'Call')
	{
		$activity_mode = 'Events';
	}		
}	



if($activity_mode == 'Task')
{
        $tab_type = 'Calendar';
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_TODO']);
}
elseif($activity_mode == 'Events')
{
        $tab_type = 'Events';
	$smarty->assign("SINGLE_MOD",$mod_strings['LBL_EVENT']);
}
$tab_id=getTabid($tab_type);


if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
    $focus->retrieve_entity_info($_REQUEST['record'],$tab_type);
    $focus->id = $_REQUEST['record'];	
    $focus->name=$focus->column_fields['subject'];
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
} 

//needed when creating a new task with default values passed in
if (isset($_REQUEST['contactname']) && is_null($focus->contactname)) {
	$focus->contactname = $_REQUEST['contactname'];
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) {
	$focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['opportunity_name']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['opportunity_name'];
}
if (isset($_REQUEST['opportunity_id']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['opportunity_id'];
}
if (isset($_REQUEST['accountname']) && is_null($focus->parent_name)) {
	$focus->parent_name = $_REQUEST['accountname'];
}
if (isset($_REQUEST['accountid']) && is_null($focus->parent_id)) {
	$focus->parent_id = $_REQUEST['accountid'];
}

$act_data = getBlocks($tab_type,"detail_view",'',$focus->column_fields);
foreach($act_data as $block=>$entry)
{
	foreach($entry as $key=>$value)
	{
		foreach($value as $label=>$field)
		{
			$fldlabel[$field['fldname']] = $label;
			$finaldata[$field['fldname']] = $field['value'];
		}
	}
}

//Start
//To set user selected hour format
if($current_user->hour_format == '')
	$format = 'am/pm';
else
	$format = $current_user->hour_format;
list($stdate,$sttime) = split('&nbsp;',$finaldata['date_start']);
list($enddate,$endtime) = split('&nbsp;',$finaldata['due_date']);
$time_arr = getaddEventPopupTime($sttime,$endtime,$format);
$data['starthr'] = $time_arr['starthour'];
$data['startmin'] = $time_arr['startmin'];
$data['startfmt'] = $time_arr['startfmt'];
$data['endhr'] = $time_arr['endhour'];
$data['endmin'] = $time_arr['endmin'];
$data['endfmt'] = $time_arr['endfmt'];
$data['record'] = $focus->id;
if($activity_mode == 'Task')
{
	$data['task_subject'] = $finaldata['subject'];
	$data['task_date_start'] = $stdate;
	$data['assigned_user_id'] = $finaldata['assigned_user_id'];
	$data['taskstatus'] = $finaldata['taskstatus'];
	$data['priority'] = $finaldata['taskpriority'];
	$data['activitytype'] = $activity_mode;
	$data['modifiedtime'] = $finaldata['modifiedtime'];
	$data['createdtime'] = $finaldata['createdtime'];
	$data['parent_name'] = $finaldata['parent_id'];
	$data['contact_id'] = $finaldata['contact_id'];
	if(isset($finaldata['sendnotification']) && $finaldata['sendnotification'] == 'yes')
		$data['sendnotification'] = 'Yes';
	else
		$data['sendnotification'] = 'No'; 
}
elseif($activity_mode == 'Events')
{
	$data['subject'] = $finaldata['subject'];
	$data['date_start'] = $stdate;
	$data['due_date'] = $enddate;
	$data['visibility'] = $finaldata['visibility'];
	$data['assigned_user_id'] = $finaldata['assigned_user_id'];
	$data['eventstatus'] = $finaldata['eventstatus'];
	$data['priority'] = $finaldata['taskpriority'];
	$data['sendnotification'] = $finaldata['sendnotification'];
	$data['activitytype'] = $finaldata['activitytype'];
	$data['modifiedtime'] = $finaldata['modifiedtime'];
	$data['createdtime'] = $finaldata['createdtime'];
	$data['parent_name'] = $finaldata['parent_id'];
	//Calculating reminder time
	$rem_days = 0;
	$rem_hrs = 0;
	$rem_min = 0;
	if($focus->column_fields['reminder_time'] != null)
	{
		$data['set_reminder'] = 'Yes';
		$data['reminder_str'] = $finaldata['reminder_time'];
	}
	else
		$data['set_reminder'] = 'No';
	//To set recurring details
	$query = 'select vtiger_recurringevents.recurringfreq,vtiger_recurringevents.recurringinfo from vtiger_recurringevents where vtiger_recurringevents.activityid = '.$focus->id;
	$res = $adb->query($query);
	$rows = $adb->num_rows($res);
	if($rows != 0)
	{
		$data['recurringcheck'] = 'Yes';
		$data['repeat_frequency'] = $adb->query_result($res,0,'recurringfreq');
		$recurringinfo =  explode("::",$adb->query_result($res,0,'recurringinfo'));
		$data['recurringtype'] = $recurringinfo[0];
		if($recurringinfo[0] == 'Monthly')
		{   
			$monthrpt_str = '';
			$data['repeatMonth'] = $recurringinfo[1];  
			if($recurringinfo[1] == 'date')
			{
				$data['repeatMonth_date'] = $recurringinfo[2];
				$monthrpt_str .= 'on '.$recurringinfo[2].' day of the month';
			}
			else 
			{ 
				$data['repeatMonth_daytype'] = $recurringinfo[2];
				$data['repeatMonth_day'] = $recurringinfo[3];
				switch($data['repeatMonth_day'])
				{
					case 0 :
						$day = $mod_strings['LBL_DAY0'];
						break;
					case 1 :
						$day = $mod_strings['LBL_DAY1'];
						break;
					case 2 :
						$day = $mod_strings['LBL_DAY2'];
						break;
					case 3 :
						$day = $mod_strings['LBL_DAY3'];
						break;
					case 4 :
						$day = $mod_strings['LBL_DAY4'];
						break;
					case 5 :
						$day = $mod_strings['LBL_DAY5'];
						break;
					case 6 :
						$day = $mod_strings['LBL_DAY6'];
						break;
				}

				$monthrpt_str .= 'on '.$mod_strings[$recurringinfo[2]].' '.$day;
			}
			$data['repeat_month_str'] = $monthrpt_str;
		}
	}
	else 
	{   
		$data['recurringcheck'] = 'No';
		$data['repeat_month_str'] = '';
	}
	$related_array = getRelatedLists("Calendar", $focus);
	$smarty->assign("INVITEDUSERS",$related_array['Users']['entries']);
	$smarty->assign("CONTACTS",$related_array['Contacts']['entries']);


}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$log->info("Calendar-Activities detail view");
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("ACTIVITY_MODE", $activity_mode);

if (isset($focus->name)) 
$smarty->assign("NAME", $focus->name);
else 
$smarty->assign("NAME", "");
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
if (isset($_REQUEST['return_module'])) 
$smarty->assign("RETURN_MODULE", $_REQUEST['return_module']);
if (isset($_REQUEST['return_action'])) 
$smarty->assign("RETURN_ACTION", $_REQUEST['return_action']);
if (isset($_REQUEST['return_id'])) 
$smarty->assign("RETURN_ID", $_REQUEST['return_id']);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string'].'&activity_mode='.$activity_mode);
$smarty->assign("ID", $focus->id);
$smarty->assign("NAME", $focus->name);
$smarty->assign("BLOCKS", $act_data);
$smarty->assign("LABEL", $fldlabel);

$smarty->assign("CUSTOMFIELD", $cust_fld);
$smarty->assign("ACTIVITYDATA", $data);
$smarty->assign("ID", $_REQUEST['record']);

//get Description Information
if(isPermitted("Calendar","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");

if(isPermitted("Calendar","Delete",$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");
  
$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

 $tabid = getTabid($tab_type);
 $validationData = getDBValidationData($focus->tab_name,$tabid);
 $data2 = split_validationdataArray($validationData);

 $smarty->assign("VALIDATION_DATA_FIELDNAME",$data2['fieldname']);
 $smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data2['datatype']);
 $smarty->assign("VALIDATION_DATA_FIELDLABEL",$data2['fieldlabel']);

$smarty->assign("MODULE",$currentModule);
$smarty->assign("EDIT_PERMISSION",isPermitted($currentModule,'EditView',$_REQUEST[record]));
$smarty->display("ActivityDetailView.tpl");

?>
