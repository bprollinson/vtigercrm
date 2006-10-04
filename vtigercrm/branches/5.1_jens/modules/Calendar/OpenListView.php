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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/OpenListView.php,v 1.22 2005/04/19 17:00:30 ray Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

/**
 * Function to get Pending/Upcoming activities
 * @param integer  $mode     - number to differentiate upcoming and pending activities
 * return array    $values   - activities record in array format
 */
function getPendingActivities($mode)
{
	global $log;
        $log->debug("Entering getPendingActivities() method ...");
	require_once("data/Tracker.php");
	require_once("include/utils/utils.php");

	global $currentModule;

	global $theme;
	global $focus;
	global $action;
	global $adb;
	global $app_strings;
	global $current_language;
	global $current_user;
	$current_module_strings = return_module_language($current_language, 'Calendar');

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once($theme_path.'layout_utils.php');
	//code added to customize upcomming and pending activities
	if($_REQUEST['activity_view']=='')
	{	
		$query = "select activity_view from vtiger_users where id ='$current_user->id'";
		$result=$adb->query($query);
		$activity_view=$adb->query_result($result,0,'activity_view');
	}
	else
		$activity_view=$_REQUEST['activity_view'];

	$today = date("Y-m-d", time());

	if($activity_view == 'Today')
	{	
		$later = date("Y-m-d",strtotime("$today +1 day"));
	}	
	else if($activity_view == 'This Week')
	{
		$later = date("Y-m-d", strtotime("$today +7 days"));
	}
	else if($activity_view == 'This Month')
	{	
		$later = date("Y-m-d", strtotime("$today +1 month"));
	}	
	else if($activity_view == 'This Year')	
	{
		$later = date("Y-m-d", strtotime("$today +1 year"));
	}
	else if($activity_view == 'OverDue')	
	{
		$later = date("Y-m-d", strtotime("$today +1 day"));
	}
	$last_tendays = date("Y-m-d",strtotime("$today -10 days"));
	if($mode != 1)
	{
		//for upcoming avtivities
		$list_query = " select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity.setype, vtiger_activity.*, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_account.accountid, vtiger_account.accountname, vtiger_recurringevents.recurringtype,vtiger_recurringevents.recurringdate from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid left outer join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid inner join vtiger_salesmanactivityrel on vtiger_salesmanactivityrel.activityid=vtiger_activity.activityid WHERE vtiger_crmentity.deleted=0 and (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task') AND ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Completed' ) and ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Deferred') and  (  vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus != 'Held') and (vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus != 'Not Held' ) AND ((date_start > '$today' AND date_start < '$later')  OR (vtiger_recurringevents.recurringdate between '$today' and '$later') ) AND vtiger_crmentity.smownerid !=0 AND vtiger_salesmanactivityrel.smid ='$current_user->id'";
	}	
	else
	{
		//for pending activities for the last 10 days
		$list_query = " select vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity.setype, vtiger_activity.*, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_account.accountid, vtiger_account.accountname, vtiger_recurringevents.recurringtype,vtiger_recurringevents.recurringdate from vtiger_activity inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid left outer join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid inner join vtiger_salesmanactivityrel on vtiger_salesmanactivityrel.activityid=vtiger_activity.activityid WHERE vtiger_crmentity.deleted=0 and (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task') AND ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Completed' ) and ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Deferred') and (  vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus != 'Held') and (vtiger_activity.eventstatus is NULL OR  vtiger_activity.eventstatus != 'Not Held' ) AND ((due_date > '$last_tendays' AND due_date <= '$today') OR (vtiger_recurringevents.recurringdate > '$last_tendays' AND vtiger_recurringevents.recurringdate <= '$today')) AND vtiger_crmentity.smownerid !=0 AND vtiger_salesmanactivityrel.smid ='$current_user->id'";
	}
	$res = $adb->query($list_query);
	$noofrecords = $adb->num_rows($res);
	$list_result = $adb->limitQuery($list_query,0,5);
	$open_activity_list = array();
	$noofrows = $adb->num_rows($list_result);
	if (count($list_result)>0)
		for($i=0;$i<$noofrows;$i++) 
		{
			$parent_name=getRelatedTo("Calendar",$list_result,$i);
			$open_activity_list[] = Array('name' => $adb->query_result($list_result,$i,'subject'),
					'id' => $adb->query_result($list_result,$i,'activityid'),
					'type' => $adb->query_result($list_result,$i,'activitytype'),
					'module' => $adb->query_result($list_result,$i,'setype'),
					'status' => $adb->query_result($list_result,$i,'status'),
					'firstname' => $adb->query_result($list_result,$i,'firstname'),
					'lastname' => $adb->query_result($list_result,$i,'lastname'),
					'accountname' => $adb->query_result($list_result,$i,'accountname'),
					'accountid' => $adb->query_result($list_result, $i, 'accountid'),
					'contactid' => $adb->query_result($list_result,$i,'contactid'),
					'date_start' => getDisplayDate($adb->query_result($list_result,$i,'date_start')),
					'due_date' => getDisplayDate($adb->query_result($list_result,$i,'due_date')),
					'recurringtype' => getDisplayDate($adb->query_result($list_result,$i,'recurringtype')),
					'recurringdate' => getDisplayDate($adb->query_result($list_result,$i,'recurringdate')),
					'parent'=> $parent_name,
					// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
					'priority' => $adb->query_result($list_result,$i,'priority'), // Armando L�scher 04.07.2005 -> �priority -> Desc: Get vtiger_priority from db
					);
		}
	$later_day = getDisplayDate(date("Y-m-d", strtotime("$later -1 day ")));
	
	$title=array();
	$title[]=$activity_view;
	$title[]='myUpcoPendAct.gif';
	//.'('.$current_module_strings["LBL_TODAY"].' '.$later_day.')';
	$title[]='home_myact';
	$title[]=getActivityView($activity_view);
	$title[]='showActivityView';		
	$title[]='MyUpcumingFrm';
	$title[]='activity_view';

	$header=array();
	$header[] =$current_module_strings['LBL_LIST_SUBJECT'];
	$header[] ='Type';
	$header[] =$current_module_strings['LBL_LIST_CLOSE'];
	$header[] =$current_module_strings['LBL_LIST_CONTACT'];
	$header[] =$current_module_strings['LBL_LIST_ACCOUNT'];
	$header[] =$current_module_strings['LBL_LIST_RELATED_TO'];
	$header[] =$current_module_strings['LBL_LIST_DATE'];
	$header[] =$current_module_strings['LBL_LIST_RECURRING_TYPE'];
	//activity select options

	// Stick the form header out there.

	$return_url="&return_module=$currentModule&return_action=DetailView&return_id=" . ((is_object($focus)) ? $focus->id : "");
	$oddRow = true;
	$entries=array();

	foreach($open_activity_list as $event)
	{
		$recur_date=ereg_replace('--','',$event['recurringdate']);
		if($recur_date!="")
			$event['date_start']=$event['recurringdate'];
			// Fredy Klammsteiner, 4.8.2005: changes from 4.0.1 migrated to 4.2
		// begin: Armando L�scher 04.07.2005 -> �priority
		// Desc: Set vtiger_priority colors
		$font_color_high = "color:#00DD00;";
		$font_color_medium = "color:#DD00DD;";

		switch ($event['priority'])
		{
			case 'High':
				$font_color=$font_color_high;
				break;
			case 'Medium':
				$font_color=$font_color_medium;
				break;
			default:
				$font_color='';
		}
		// end: Armando L�scher 04.07.2005 -> �priority


		$end_date=$event['due_date']; //included for getting the OverDue Activities in the Upcoming Activities
		$start_date=$event['date_start'];

		switch ($event['type']) {
			case 'Call':
				$activity_fields = "<a href='index.php?return_module=Home&return_action=index&return_id=$focus->activityid&action=Save&module=Calendar&record=".$event['id']."&activity_type=".$event['type']."&change_status=true&eventstatus=Held' style='".$font_color."'>X</a>"; // Armando L�scher 05.07.2005 -> �priority -> Desc: inserted style="$P_FONT_COLOR"
				break;
			case 'Meeting':
				$activity_fields = "<a href='index.php?return_module=Home&return_action=index&return_id=$focus->activityid&action=Save&module=Calendar&record=".$event['id']."&activity_type=".$event['type']."&change_status=true&eventstatus=Held' style='".$font_color."'>X</a>"; // Armando L�scher 05.07.2005 -> �priority -> Desc: inserted style="$P_FONT_COLOR"

			case 'Task':
				$activity_fields = "<a href='index.php?return_module=Home&return_action=index&return_id=$focus->activityid&action=Save&module=Calendar&record=".$event['id']."&activity_type=".$event['type']."&change_status=true&status=Completed' style='".$font_color."'>X</a>"; // Armando L�scher 05.07.2005 -> �priority -> Desc: inserted style="$P_FONT_COLOR"
				break;
		}

		if($event['type'] == 'Call' || $event['type'] == 'Meeting')
			$activity_type = 'Events';
		else
			$activity_type = 'Task';



		//Code included for showing Overdue Activities in Upcoming Activities -Jaguar
		$end_date=getDBInsertDateValue($end_date);
		if($end_date== '0000-00-00' OR $end_date =="")
		{
			$end_date=$start_date;
		}
		if($recur_date!="")
		{
			$recur_date=getDBInsertDateValue($recur_date);	
			$end=explode("-",$recur_date);
		}
		else
		{
			$end=explode("-",$end_date);
		}

		$current_date=date("Y-m-d",mktime(date("m"),date("d"),date("Y")));
		$curr=explode("-",$current_date);
		$date_diff= mktime(0,0,0,date("$curr[1]"),date("$curr[2]"),date("$curr[0]")) - mktime(0,0,0,date("$end[1]"),date("$end[2]"),date("$end[0]"));

		if($date_diff>0)
		{
			$x="pending";
		}
		else
		{
			if($oddRow)
			{
				$x="oddListRow";
			}
			else
			{
				$x="evenListRow";
			}
		}
		// Code by Jaguar Ends
		$entries['noofactivities'] = $noofrecords;
		$entries[$event['id']] = array(
				'0' => '<a href="index.php?action=DetailView&module='.$event["module"].'&activity_mode='.$activity_type.'&record='.$event["id"].''.$return_url.'" style="'.$font_color.';">'.$event["name"].'</a>',
				'IMAGE' => '<IMG src="'.$image_path.$event["type"].'s.gif">',
				'ACTIVITY' => $activity_fields,
				'CONTACT_NAME' => '<a href="index.php?action=DetailView&module=Contacts&record='.$event['contactid'].''.$return_url.'" style="'.$font_color.';">'.$event['firstname'].' '.$event['lastname'].'</a>',
				'ACCOUNT_NAME' => '<a href="index.php?action=DetailView&module=Accounts&record='.$event['accountid'].'" style="'.$font_color.';">'.$event['accountname'].'</a>',
				'PARENT_NAME' => $event['parent'],
				'TIME' => $event['date_start'],
				'RECURRINGTYPE' => ereg_replace('--','',$event['recurringtype']),
				);
	}
	$values=Array('Title'=>$title,'Header'=>$header,'Entries'=>$entries);
	$log->debug("Exiting getPendingActivities method ...");
		return $values;
}

/**
 * Function creates HTML to display ActivityView selection box
 * @param string   $activity_view                 - activity view 
 * return string   $ACTIVITY_VIEW_SELECT_OPTION   - HTML selection box
 */
function getActivityview($activity_view)	
{	
	global $log;
	$log->debug("Entering getActivityview(".$activity_view.") method ...");
	$today = date("Y-m-d", time());

	if($activity_view == 'Today')
	{	
		$selected1 = 'selected';
	}	
	else if($activity_view == 'This Week')
	{
		$selected2 = 'selected';
	}
	else if($activity_view == 'This Month')
	{	
		$selected3 = 'selected';
	}	
	else if($activity_view == 'This Year')	
	{
		$selected4 = 'selected';
	}

	//constructing the combo values for activities
	$ACTIVITY_VIEW_SELECT_OPTION = '<select class=small name="activity_view" onchange="showActivityView(this)">';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="Today" '.$selected1.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'Today';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Week" '.$selected2.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Week';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Month" '.$selected3.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Month';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '<option value="This Year" '.$selected4.'>';
	$ACTIVITY_VIEW_SELECT_OPTION .= 'This Year';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</option>';
	$ACTIVITY_VIEW_SELECT_OPTION .= '</select>';
	
	$log->debug("Exiting getActivityview method ...");
	return $ACTIVITY_VIEW_SELECT_OPTION;
}
?>
