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


require_once('include/utils/UserInfoUtil.php');
require_once("include/utils/utils.php");
require_once("include/ListView/ListViewSession.php");

function GetRelatedList($module,$relatedmodule,$focus,$query,$button,$returnset,$id='',$edit_val='',$del_val='')
{
	$log = LoggerManager::getLogger('account_list');
	$log->debug("Entering GetRelatedList(".$module.",".$relatedmodule.",".$focus.",".$query.",".$button.",".$returnset.",".$edit_val.",".$del_val.") method ...");

	require_once('Smarty_setup.php');
	require_once("data/Tracker.php");
	require_once('include/database/PearDatabase.php');

	global $adb;
	global $app_strings;
	global $current_language;

	$current_module_strings = return_module_language($current_language, $module);

	global $list_max_entries_per_page;
	global $urlPrefix;


	global $currentModule;
	global $theme;
	global $theme_path;
	global $theme_path;
	global $mod_strings;
	// focus_list is the means of passing data to a ListView.
	global $focus_list;
	$smarty = new vtigerCRM_Smarty;
	if (!isset($where)) $where = "";
	
	
	$button = '<table cellspacing=0 cellpadding=2><tr><td>'.$button.'</td></tr></table>';

	// Added to have Purchase Order as form Title
	if($relatedmodule == 'Orders') 
	{
		$smarty->assign('ADDBUTTON',get_form_header($app_strings['PurchaseOrder'],$button, false));
	}
	else
	{
		$smarty->assign('ADDBUTTON',get_form_header($app_strings[$relatedmodule],$button, false));
	}

	require_once('themes/'.$theme.'/layout_utils.php');
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$smarty->assign("MOD", $mod_strings);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("IMAGE_PATH",$image_path);
	$smarty->assign("MODULE",$relatedmodule);


	//Retreive the list from Database
	//$query = getListQuery("Accounts");

		//echo '<BR>*****************'.$relatedmodule.' ***************';
	//Appending the security parameter
	if($relatedmodule != 'Notes' && $relatedmodule != 'Products' && $relatedmodule != 'Faq' && $relatedmodule != 'PriceBook' && $relatedmodule != 'Vendors') //Security fix by Don
	{
		global $current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
        	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		$tab_id=getTabid($relatedmodule);
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3)
        	{
        		$sec_parameter=getListViewSecurityParameter($relatedmodule);
                	$query .= ' '.$sec_parameter;

        	}
	}
	

	if(isset($where) && $where != '')
	{
		$query .= ' and '.$where;
	}
	
	if(!$_SESSION['rlvs'][$module][$relatedmodule])
	{
		$modObj = new ListViewSession();
		$modObj->sortby = $focus->default_order_by;
		$modObj->sorder = $focus->default_sort_order;
		$_SESSION['rlvs'][$module][$relatedmodule] = get_object_vars($modObj);
	}
	if(isset($_REQUEST['relmodule']) && ($_REQUEST['relmodule'] == $relatedmodule))
	{	
		if(method_exists($focus,getSortOrder))
		$sorder = $focus->getSortOrder();
		if(method_exists($focus,getOrderBy))
		$order_by = $focus->getOrderBy();

		if(isset($order_by) && $order_by != '')
		{
			$_SESSION['rlvs'][$module][$relatedmodule]['sorder'] = $sorder;
			$_SESSION['rlvs'][$module][$relatedmodule]['sortby'] = $order_by;
		}

	}
	elseif($_SESSION['rlvs'][$module][$relatedmodule])
	{
		$sorder = $_SESSION['rlvs'][$module][$relatedmodule]['sorder'];
		$order_by = $_SESSION['rlvs'][$module][$relatedmodule]['sortby'];
	}
	else
	{
		$order_by = $focus->default_order_by;
		$sorder = $focus->default_sort_order;
	}
		
	$query .= ' ORDER BY '.$order_by.' '.$sorder;
	$url_qry .="&order_by=".$order_by;
	//Added for PHP version less than 5
	if (!function_exists("stripos"))
	{
		function stripos($query,$needle)
		{
			return strpos(strtolower($query),strtolower($needle));
		}
	}
	
	//Retreiving the no of rows
	$count_query = "select count(*) count ".substr($query, stripos($query,'from'),strlen($query));
	$count_result = $adb->query(substr($count_query, stripos($count_query,'select'),stripos($count_query,'ORDER BY')));
	$noofrows = $adb->query_result($count_result,0,"count");
	
	//Setting Listview session object while sorting/pagination
	if(isset($_REQUEST['relmodule']) && $_REQUEST['relmodule']!='' && $_REQUEST['relmodule'] == $relatedmodule)
	{
		$relmodule = $_REQUEST['relmodule'];
		if($_SESSION['rlvs'][$module][$relmodule])
		{
			setSessionVar($_SESSION['rlvs'][$module][$relmodule],$noofrows,$list_max_entries_per_page,$module,$relmodule);
		}
	}
	$start = $_SESSION['rlvs'][$module][$relatedmodule]['start'];

	$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);
	
	$start_rec = $navigation_array['start'];
	$end_rec = $navigation_array['end_val'];

	//limiting the query
	if ($start_rec ==0) 
		$limit_start_rec = 0;
	else
		$limit_start_rec = $start_rec -1;

	$list_result = $adb->query($query. " limit ".$limit_start_rec.",".$list_max_entries_per_page);

	//Retreive the List View Table Header
	if($noofrows == 0)
	{
		$smarty->assign('NOENTRIES',$app_strings['LBL_NONE_SCHEDULED']);
	}
	else
	{
		$id = $_REQUEST['record'];
		$listview_header = getListViewHeader($focus,$relatedmodule,'',$sorder,$order_by,$id,'',$module);//"Accounts");
		if ($noofrows > 15)
		{
			$smarty->assign('SCROLLSTART','<div style="overflow:auto;height:315px;width:100%;">');
			$smarty->assign('SCROLLSTOP','</div>');
		}
		$smarty->assign("LISTHEADER", $listview_header);
															
		if($module == 'PriceBook' && $relatedmodule == 'Products')
		{
			$listview_entries = getListViewEntries($focus,$relatedmodule,$list_result,$navigation_array,'relatedlist',$returnset,$edit_val,$del_val);
		}
		if($module == 'Products' && $relatedmodule == 'PriceBook')
		{
			$listview_entries = getListViewEntries($focus,$relatedmodule,$list_result,$navigation_array,'relatedlist',$returnset,'EditListPrice','DeletePriceBookProductRel');
		}
		elseif($relatedmodule == 'SalesOrder')
		{
			$listview_entries = getListViewEntries($focus,$relatedmodule,$list_result,$navigation_array,'relatedlist',$returnset,'SalesOrderEditView','DeleteSalesOrder');
		}else
		{
			$listview_entries = getListViewEntries($focus,$relatedmodule,$list_result,$navigation_array,'relatedlist',$returnset);
		}

		$navigationOutput = Array();
		$navigationOutput[] = $app_strings[LBL_SHOWING]." " .$start_rec." - ".$end_rec." " .$app_strings[LBL_LIST_OF] ." ".$noofrows;
		$module_rel = $module.'&relmodule='.$relatedmodule.'&record='.$id;
		$navigationOutput[] = getRelatedTableHeaderNavigation($navigation_array, $url_qry,$module_rel);
		$related_entries = array('header'=>$listview_header,'entries'=>$listview_entries,'navigation'=>$navigationOutput);
		$log->debug("Exiting GetRelatedList method ...");
		return $related_entries;
	}
}

function getAttachmentsAndNotes($parentmodule,$query,$id,$sid='')
{
	global $log;
	$log->debug("Entering getAttachmentsAndNotes(".$parentmodule.",".$query.",".$id.",".$sid.") method ...");
	global $theme;

	$list = '<script>
		function confirmdelete(url)
		{
			if(confirm("Are you sure?"))
			{
				document.location.href=url;
			}
		}
	</script>';
	echo $list;

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once ($theme_path."layout_utils.php");

	global $adb;
	global $mod_strings;
	global $app_strings;

	$result=$adb->query($query);
	$noofrows = $adb->num_rows($result);

	$header[] = $app_strings['LBL_CREATED'];
	$header[] = $app_strings['LBL_SUBJECT'];
	$header[] = $app_strings['LBL_DESCRIPTION'];
	$header[] = $app_strings['LBL_ATTACHMENTS'];
	$header[] = $app_strings['LBL_TYPE'];		
	$header[] = $app_strings['LBL_ACTION'];	

	while($row = $adb->fetch_array($result))
	{
		$entries = Array();
		if(trim($row['activitytype']) == 'Notes')
		{
			$module = 'Notes';
			$editaction = 'EditView';
			$deleteaction = 'Delete';
		}
		elseif($row['activitytype'] == 'Attachments')
		{
			$module = 'uploads';
			$editaction = 'upload';
			$deleteaction = 'deleteattachments';
		}
		if($row['createdtime'] != '0000-00-00 00:00:00')
		{
			$created_arr = explode(" ",getDisplayDate($row['createdtime']));
			$created_date = $created_arr[0];
			$created_time = substr($created_arr[1],0,5);
		}
		else
		{
			$created_date = '';
			$created_time = '';
		}

		$entries[] = $created_date;
		if($module == 'Notes')
		{
			$entries[] = '<a href="index.php?module='.$module.'&action=DetailView&return_module='.$parentmodule.'&return_action='.$return_action.'&record='.$row["crmid"].'&filename='.$row['filename'].'&fileid='.$row['attachmentsid'].'&return_id='.$_REQUEST["record"].'">'.$row['title'].'</a>';
		}
		elseif($module == 'uploads')
		{
			$entries[] = "";
		}

		$entries[] = nl2br($row['description']); 
		$attachmentname = ltrim($row['filename'],$row['attachmentsid'].'_');//explode('_',$row['filename'],2);

		$entries[] = '<a href="index.php?module=uploads&action=downloadfile&entityid='.$id.'&fileid='.$row['attachmentsid'].'">'.$attachmentname.'</a>';

		$entries[] = $row['activitytype'];	

		$del_param = 'index.php?module='.$module.'&action='.$deleteaction.'&return_module='.$parentmodule.'&return_action='.$_REQUEST['action'].'&record='.$row["crmid"].'&filename='.$row['filename'].'&return_id='.$_REQUEST["record"];

		if($module == 'Notes')
		{
			$edit_param = 'index.php?module='.$module.'&action='.$editaction.'&return_module='.$parentmodule.'&return_action='.$_REQUEST['action'].'&record='.$row["crmid"].'&filename='.$row['filename'].'&fileid='.$row['attachmentsid'].'&return_id='.$_REQUEST["record"];

			$entries[] .= '<a href="'.$edit_param.'">'.$app_strings['LNK_EDIT'].'</a> | <a href="javascript:;" onclick=confirmdelete("'.$del_param.'")>'.$app_strings['LNK_DELETE'].'</a>';
		}
		else
		{
			$entries[] = '<a href="javascript:;" onclick=confirmdelete("'.$del_param.'")>'.$app_strings['LNK_DELETE'].'</a>';
		}
		$entries_list[] = $entries;
	}

	if($entries_list !='')
		$return_data = array('header'=>$header,'entries'=>$entries_list);
	$log->debug("Exiting getAttachmentsAndNotes method ...");
	return $return_data;

}

function getHistory($parentmodule,$query,$id)
{
	global $log;
	$log->debug("Entering getHistory(".$parentmodule.",".$query.",".$id.") method ...");
	$parentaction = $_REQUEST['action'];
	global $theme;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once ($theme_path."layout_utils.php");

	global $adb;
	global $mod_strings;
	global $app_strings;

	//Appending the security parameter
	global $current_user;
	$rel_tab_id = getTabid("Activities");

	global $current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
        $tab_id=getTabid('Activities');
       if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3)
       {
       		$sec_parameter=getListViewSecurityParameter('Activities');
                $query .= ' '.$sec_parameter;

        }
	$result=$adb->query($query);
	$noofrows = $adb->num_rows($result);
	
	$button .= '<table cellspacing=0 cellpadding=2><tr><td>';
	$button .= '</td></tr></table>';

	if($noofrows == 0)
	{
	}
	else
	{
		$list .= '<table border="0" cellpadding="0" cellspacing="0" class="FormBorder" width="100%" >';
		$list .= '<tr class="ModuleListTitle" height=20>';

// Armando L�scher 15.07.2005 -> �scrollableTables
// Desc: class="blackLine" deleted because of vertical line in title <tr>

		$class_black="";
		if($noofrows<=15)
		{
			$class_black='class="blackLine"';	
			$colspan = 'colspan=2';
		}

		$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
		$list .= '<td width="90" '.$colspan.' class="moduleListTitle" style="padding:0px 3px 0px 3px;" noWrap>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 25% to 90, inserted noWrap

		$colspan = ($noofrows<=15)?'colspan="3"':''; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Inserted
		$list .= $app_strings['LBL_CREATED'].'</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed LBL_SUBJECT to LBL_CREATED
		$header[] = $app_strings['LBL_CREATED'];
		$list .= '<td WIDTH="1" '.$class_black.'><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
		$list .= '<td '.$colspan.' width="30%" class="moduleListTitle" style="padding:0px 3px 0px 3px;" noWrap>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 10% to 30%, inserted '.$colspan.' noWrap
	
		$list .= $app_strings['LBL_SUBJECT'].'</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed LBL_STATUS to LBL_SUBJECT
		$header[] = $app_strings['LBL_SUBJECT'];
		$list .= '<td WIDTH="1" '.$class_black.'><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
		$list .= '<td width="70%" class="moduleListTitle" style="padding:0px 3px 0px 3px;" noWrap>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 18% to 70%, inserted noWrap

		$list .= $app_strings['LBL_DESCRIPTION'].'</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed LBL_LIST_CONTACT_NAME to LBL_DESCRIPTION
		$header[] = $app_strings['LBL_DESCRIPTION'];
		$list .= '<td WIDTH="1" '.$class_black.'><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
		$list .= '<td width="80" class="moduleListTitle" style="padding:0px 3px 0px 3px;" noWrap>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 18% to 80, inserted noWrap

		$list .= $app_strings['LBL_ACTION'].'</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed LBL_RELATED_TO to LBL_ACTION
		$header[] = $app_strings['LBL_TIME'];
		$header[] = $app_strings['LBL_ACTION'];
		$header[] = $app_strings['LBL_RELATED_TO'];
		$header[] = $app_strings['LBL_ASSIGNED_TO'];
		$list .= '<td WIDTH="1" '.$class_black.'><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
/* // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Commented out because this is not used for the title row
*/
		$list .= '</td>';
		$colspan = 9;
		if($noofrows>15)
		{
			$list .= '<td style="width:20px">&nbsp;&nbsp&nbsp;&nbsp;</td>';
			$colspan = 11;
		}
		$list .= '</tr>';
	
		$list .= '<tr><td COLSPAN="'.$colspan.'" class="blackLine"><IMG SRC="themes/'.$theme.'/images//blank.gif"></td></tr>';

// begin: Armando L�scher 14.07.2005 -> �scrollableTables
// Desc: 'X'
//			 Insert new vtiger_table with 1 cell where all entries are in a new vtiger_table.
//			 This cell will be scrollable when too many entries exist
		$list .= ($noofrows>15) ? '<tr><td colspan="'.$colspan.'"><div style="overflow:auto;height:315px;width:100%;"><table cellspacing="0" cellpadding="0" border="0" width="100%">':'';
// end: Armando L�scher 14.07.2005 -> �scrollableTables

		$i=1;
		while($row = $adb->fetch_array($result))
		{
			$entries = Array();
			if($row['activitytype'] == 'Task')
			{
				$activitymode = 'Task';
				$icon = 'Tasks.gif';
				$status = $row['status'];
			}
			elseif($row['activitytype'] == 'Call' || $row['activitytype'] == 'Meeting')
			{
				$activitymode = 'Events';
				$icon = 'Activities.gif';
				$status = $row['eventstatus'];
			}
			if ($i%2==0)
				$trowclass = 'evenListRow';
			else
				$trowclass = 'oddListRow';
	
			$created_arr = explode(" ",getDisplayDate($row['createdtime']));
			$created_date = $created_arr[0];
			$created_time = substr($created_arr[1],0,5);

			$list .= '<tr class="'. $trowclass.'">';
			$entries[] = $created_date;	
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			$list .= '<td colspan="2" valign="top" class="visibleDescriptionLink" width="90" style="padding:0px 3px 0px 3px;" noWrap>'.$created_date.'</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 4% to 90, inserted colspan="2" align="right" valign="top" class="visibleDescriptionLink" style="padding:0px 3px 0px 3px;" noWrap, replaced <IMG SRC="'.$image_path.'/'.$icon.'"> with $created_date

			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			$list .= '<td valign="top" colspan="3" width="30%" height="21" class="visibleDescriptionLink" style="padding:0px 3px 0px 3px;">'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 25% to 30%, inserted colspan="3" valign="top" class="visibleDescriptionLink"
			$activity = '<a href="index.php?module=Activities&action=DetailView&return_module='.$parentmodule.'&return_action=DetailView&record='.$row["activityid"] .'&activity_mode='.$activitymode.'&return_id='.$_REQUEST['record'].'" title="'.$row['description'].'">'.$row['subject'].'</a></td>';
			$entries[] = $activity;
			$list .= '</td>';
	
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			$list .= '<td valign="top" rowspan="2" width="70%" height="21" class="visibleDescription" style="padding:0px 3px 0px 3px;">'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 10% to 70%, inserted rowspan="2" valign="top" class="visibleDescription"
			$entries[] = nl2br($row['description']);
			$list .= nl2br($row['description']); // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Replaced $status with nl2br($row['description'])
			$list .= '</td>';

			if($row['firstname'] != 'NULL')	
				$contactname = $row['firstname'].' ';
			if($ros['lastname'] != 'NULL')
				$contactname .= $row['lastname'];

			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif">';
			$list .= '<td valign="top" width="80" height="21" style="padding:0px 3px 0px 3px;" noWrap>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 18% to 80, inserted valign="top" noWrap
			// Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: This if-statement replaces the line above
			if(isPermitted("Activities",1,$row["activityid"]) == 'yes')
			{
				$list .= '<a href="index.php?module=Activities&action=EditView&return_module='.$parentmodule.'&return_action='.$parentaction.'&activity_mode='.$activitymode.'&record='.$row["activityid"].'&return_id='.$_REQUEST["record"].'">'.$app_strings['LNK_EDIT'].'</a>';
			
			}
			$list .= '</td>';

			// begin: Armando L�scher 26.09.2005 -> �visibleDescription
			// Desc: Inserted because entries are displayed on 2 rows
			$list .= '</tr><tr class="'.$trowclass.'">';
			// end: Armando L�scher 26.09.2005 -> �visibleDescription 

			$parentname = getRelatedTo('Activities',$result,$i-1);

			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			
			// begin: Armando L�scher 26.09.2005 -> �visibleDescription
			// Desc: Added
			$list .= '<td valign="top" width="20" style="padding:0px 0px 0px 10px;">';
			$list .= '<IMG SRC="'.$image_path.'/'.$icon.'">';
			$list .= '</td>';
			// end: Armando L�scher 26.09.2005 -> �visibleDescription
	
			$list .= '<td align="right" valign="top" width="70" style="padding:0px 3px 0px 3px;">'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 18% to 70, inserted align="right" valign="top"
			$list .= $created_time; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Replaced $parentname with $created_time
			$list .= '</td>';	
			$entries[] = $created_time;
	
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			$list .= '<td valign="top" width="8%" style="padding:0px 3px 0px 3px;">'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 15% to 8%, inserted valign="top"
			$list .= $status; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Replaced $modifiedtime with $status
			$entries[] = $status;
			$list .= '</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Inserted

//			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif">';
			$list .= '<td valign="top" width="18%" style="padding:0px 3px 0px 3px;">'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Changed width from 10% to 18%, inserted valign="top"
			$entries[] = $parentname;
			$list .= $parentname; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Inserted
			$list .= '</td>'; // Armando L�scher 26.09.2005 -> �visibleDescription -> Desc: Inserted
			
			// begin: Armando L�scher 26.09.2005 -> �visibleDescription
			// Desc: Added
			$list .= '<td valign="top" width="4%" style="padding:0px 3px 0px 3px;">';
			if($row['user_name']==NULL && $row['groupname']!=NULL)
			{
				$list .= $row['groupname'];
				$entries[] = $row['groupname'];
			}
			else
			{
				$list .= $row['user_name'];
 				$entries[] = $row['user_name'];
				
			}
			$list .= '</td>';
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			
			// the description is in this space
			
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td>';
			$list .= '<td valign="top" width="80" style="padding:0px 3px 0px 3px;">';
			if(isPermitted("Activities",2,$row["activityid"]) == 'yes')
			{
				$list .= '<a href="index.php?module=Activities&action=Delete&return_module='.$parentmodule.'&return_action='.$parentaction.'&record='.$row["activityid"].'&return_id='.$_REQUEST["record"].'">'.$app_strings['LNK_DELETE'].'</a>';
			}
			$list .= '</td>';
			
			$list .= '<td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif">';

			$list .= '</tr>';

			$list .= '<tr width="'.$colspan.'"><td WIDTH="1" class="blackLine"><IMG SRC="themes/'.$theme.'/images/blank.gif"></td></tr>';

			$i++;
			$entries_list[] = $entries;
		}

// begin: Armando L�scher 14.07.2005 -> �scrollableTables
// Desc: Close vtiger_table from 
		$list .= ($noofrows>15) ? '</table></div></td></tr>':'';
// end: Armando L�scher 14.07.2005 -> �scrollableTables

		$list .= '<tr><td colspan="14" class="blackLine"></td></tr>';

		$list .= '</table>';
		$return_data = array('header'=>$header,'entries'=>$entries_list);
		$log->debug("Exiting getHistory method ...");
		return $return_data; 
	}
}

/**	Function to display the Products which are related to the PriceBook
 *	@param string $query - query to get the list of products which are related to the current PriceBook
 *	@param object $focus - PriceBook object which contains all the information of the current PriceBook
 *	@param string $returnset - return_module, return_action and return_id which are sequenced with & to pass to the URL which is optional
 *	return array $return_data which will be formed like array('header'=>$header,'entries'=>$entries_list) where as $header contains all the header columns and $entries_list will contain all the Product entries
 */
function getPriceBookRelatedProducts($query,$focus,$returnset='')
{
	global $log;
	$log->debug("Entering getPriceBookRelatedProducts(".$query.",".$focus.",".$returnset.") method ...");

	global $adb;
	global $app_strings;
	global $mod_strings;
	global $current_language;
	$current_module_strings = return_module_language($current_language, 'PriceBook');

	global $list_max_entries_per_page;
	global $urlPrefix;

	global $theme;
	$pricebook_id = $_REQUEST['record'];
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once($theme_path.'layout_utils.php');

	//Retreive the list from Database
	$list_result = $adb->query($query);
	$num_rows = $adb->num_rows($list_result);

	$header=array();
	$header[]=$mod_strings['LBL_LIST_PRODUCT_NAME'];
	$header[]=$mod_strings['LBL_PRODUCT_CODE'];
	$header[]=$mod_strings['LBL_PRODUCT_UNIT_PRICE'];
	$header[]=$mod_strings['LBL_PB_LIST_PRICE'];
	$header[]=$mod_strings['LBL_ACTION'];

	for($i=0; $i<$num_rows; $i++)
	{
		$entity_id = $adb->query_result($list_result,$i,"crmid");

		$unit_price = 	$adb->query_result($list_result,$i,"unit_price");
		$listprice = $adb->query_result($list_result,$i,"listprice");
		$field_name=$entity_id."_listprice";
		
		$entries = Array();
		$entries[] = $adb->query_result($list_result,$i,"productname");
		$entries[] = $adb->query_result($list_result,$i,"productcode");
		$entries[] = $unit_price;
		$entries[] = $listprice;
		$entries[] = '<img src="'.$image_path.'editfield.gif" border="0" onClick="editProductListPrice(\''.$entity_id.'\',\''.$pricebook_id.'\',\''.$listprice.'\')" alt="Edit" title="Edit"/><!--a href="index.php?module=Products&action=EditListPrice&record='.$entity_id.'&pricebook_id='.$pricebook_id.'&listprice='.$listprice.'">edit</a-->&nbsp;|&nbsp;<img src="'.$image_path.'delete.gif" onclick="if(confirm(\'Are you sure?\')) deletePriceBookProductRel('.$entity_id.','.$pricebook_id.');" alt="Delete" title="Delete" border="0">';

		$entries_list[] = $entries;
	}
	if($num_rows>0)
	{
		$return_data = array('header'=>$header,'entries'=>$entries_list);

		$log->debug("Exiting getPriceBookRelatedProducts method ...");
		return $return_data; 
	}
}

?>
