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

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('modules/Potentials/Potentials.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/ComboUtil.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');
require_once('include/database/Postgres8.php');
require_once('include/DatabaseUtil.php');


global $app_strings,$list_max_entries_per_page,$mod_strings;

$log = LoggerManager::getLogger('potential_list');

global $currentModule,$theme;

$category = getParentTab();

if (!isset($where)) $where = "";

$url_string = ''; // assigning http url string

$focus = new Potentials();
$smarty = new vtigerCRM_Smarty();
$other_text = Array();

if(!$_SESSION['lvs'][$currentModule])
{
	unset($_SESSION['lvs']);
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	$_SESSION['lvs'][$currentModule] = get_object_vars($modObj);
}

if($_REQUEST['errormsg'] != '')
{
        $errormsg = $_REQUEST['errormsg'];
        $smarty->assign("ERROR","The User does not have permission to Change/Delete ".$errormsg." ".$currentModule);
}else
{
        $smarty->assign("ERROR","");
}
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
$sorder = $focus->getSortOrder();
$order_by = $focus->getOrderBy();

$_SESSION['POTENTIALS_ORDER_BY'] = $order_by;
$_SESSION['POTENTIALS_SORT_ORDER'] = $sorder;
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true')
{
	list($where, $ustring) = split("#@@#",getWhereCondition($currentModule));
	// we have a query
	$url_string .="&query=true".$ustring;
	$log->info("Here is the where clause for the list view: $where");
	$smarty->assign("SEARCH_URL",$url_string);
				
	//Added for Custom Field Search
/*	$sql="select * from vtiger_field where vtiger_tablename='potentialscf' order by vtiger_fieldlabel";
	$result=$adb->query($sql);
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
		$column[$i]=$adb->query_result($result,$i,'columnname');
		$fieldlabel[$i]=$adb->query_result($result,$i,'fieldlabel');
		$uitype[$i]=$adb->query_result($result,$i,'uitype');
		if (isset($_REQUEST[$column[$i]])) $customfield[$i] = $_REQUEST[$column[$i]];

		if(isset($customfield[$i]) && $customfield[$i] != '')
		{
			if($uitype[$i] == 56)
				$str = " vtiger_potentialscf.".$column[$i]." = 1";
			elseif($uitype[$i] == 15)//Added to handle the picklist customfield - after 4.2 patch2
				$str = " vtiger_potentialscf.".$column[$i]." = '".$customfield[$i]."'";
			else
				$str = " vtiger_potentialscf.".$column[$i]." like '$customfield[$i]%'";
			array_push($where_clauses, $str);
			$url_string .="&".$column[$i]."=".$customfield[$i];
		}
	}

*/
}

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Potentials");
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);
//<<<<<customview>>>>>
$smarty->assign("CHANGE_OWNER",getUserslist());
$smarty->assign("CHANGE_GROUP_OWNER",getGroupslist());
if(isPermitted('Potentials','Delete','') == 'yes')
{
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}
if(isPermitted('Potentials','EditView','') == 'yes')
{
        $other_text['c_owner'] = $app_strings[LBL_CHANGE_OWNER];
}

if($viewnamedesc['viewname'] == 'All')
{
	$smarty->assign("ALL", 'All');
}

//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
if($viewid != "0")
{
	$listquery = getListQuery("Potentials");
	$list_query = $oCustomView->getModifiedCvListQuery($viewid,$listquery,"Potentials");
}else
{
	$list_query = getListQuery("Potentials");
}
//<<<<<<<<customview>>>>>>>>>
if(isset($where) && $where != '')
{
	if(isset($_REQUEST['from_dashboard']) && $_REQUEST['from_dashboard'] == 'true')
		$list_query .= " AND vtiger_potential.sales_stage = '".$mod_strings['Closed Won']."' AND ".$where;
	elseif(isset($_REQUEST['from_homepagedb']) && $_REQUEST['from_homepagedb'] == 'true')
		$list_query .= " AND vtiger_potential.sales_stage not in( '".$mod_strings['Closed Won']."' , '".$mod_strings['Closed Lost']."' )AND ".$where;
	else
		$list_query .= " AND ".$where;

	$_SESSION['export_where'] = $where;
}
else
   unset($_SESSION['export_where']);


if(isset($order_by) && $order_by != '')
{
	if($order_by == 'smownerid')
        {
		if( $adb->dbType == "pgsql")
 		    $list_query .= ' GROUP BY user_name';
                $list_query .= ' ORDER BY user_name '.$sorder;
        }
        else
        {
		$tablename = getTableNameForField('Potentials',$order_by);
		$tablename = (($tablename != '')?($tablename."."):'');

		if( $adb->dbType == "pgsql")
 		    $list_query .= ' GROUP BY '.$tablename.$order_by;

                $list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
        }

}
//Constructing the list view

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",'Opportunity');
$smarty->assign("BUTTONS",$other_text);
$smarty->assign("CATEGORY",$category);


//Retreiving the no of rows
$count_result = $adb->query( mkCountQuery( $list_query));
$noofrows = $adb->query_result($count_result,0,"count");

//Storing Listview session object
if($_SESSION['lvs'][$currentModule])
{
	setSessionVar($_SESSION['lvs'][$currentModule],$noofrows,$list_max_entries_per_page);
}

$start = $_SESSION['lvs'][$currentModule]['start'];

//Retreive the Navigation array
$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);
//Postgres 8 fixes
if( $adb->dbType == "pgsql")
    $list_query = fixPostgresQuery( $list_query, $log, 0);


// Setting the record count string
//modified by rdhital
$start_rec = $navigation_array['start'];
$end_rec = $navigation_array['end_val']; 
//By Raju Ends
$_SESSION['nav_start']=$start_rec;
$_SESSION['nav_end']=$end_rec;

//limiting the query
if ($start_rec ==0) 
	$limit_start_rec = 0;
else
	$limit_start_rec = $start_rec -1;
	
if( $adb->dbType == "pgsql")
     $list_result = $adb->pquery($list_query. " OFFSET $limit_start_rec LIMIT $list_max_entries_per_page", array());
 else
     $list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

$record_string= $app_strings[LBL_SHOWING]." " .$start_rec." - ".$end_rec." " .$app_strings[LBL_LIST_OF] ." ".$noofrows;

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

$listview_header = getListViewHeader($focus,"Potentials",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_header_search=getSearchListHeaderValues($focus,"Potentials",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("SEARCHLISTHEADER", $listview_header_search);


$listview_entries = getListViewEntries($focus,"Potentials",$list_result,$navigation_array,"","","EditView","Delete",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);
$smarty->assign("LISTENTITY", $listview_entries);


$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string,"Potentials","index",$viewid);
$alphabetical = AlphabeticalSearch($currentModule,'index','potentialname','true','basic',"","","","",$viewid);
$fieldnames = getAdvSearchfields($module);
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_COUNTS", $record_string);
$smarty->assign("SELECT_SCRIPT", $view_script);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else	
	$smarty->display("ListView.tpl");

?>
