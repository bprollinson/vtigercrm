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
require_once('modules/Invoice/Invoice.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');


global $app_strings,$list_max_entries_per_page,$currentModule,$theme;

$log = LoggerManager::getLogger('order_list');

// Get _dom arrays from Database
$comboFieldNames = Array('accounttype'=>'account_type_dom'
                      ,'industry'=>'industry_dom');
$comboFieldArray = getComboArray($comboFieldNames);

if (!isset($where)) $where = "";

$url_string = '';

$focus = new Invoice();
$smarty = new vtigerCRM_Smarty;
$other_text = Array();

//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
if($_REQUEST['order_by'] != '')
	$order_by = $_REQUEST['order_by'];
else
	$order_by = (($_SESSION['INVOICE_ORDER_BY'] != '')?($_SESSION['INVOICE_ORDER_BY']):($focus->default_order_by));

if($_REQUEST['sorder'] != '')
	$sorder = $_REQUEST['sorder'];
else
	$sorder = (($_SESSION['INVOICE_SORT_ORDER'] != '')?($_SESSION['INVOICE_SORT_ORDER']):($focus->default_sort_order));

$_SESSION['INVOICE_ORDER_BY'] = $order_by;
$_SESSION['INVOICE_SORT_ORDER'] = $sorder;
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>

if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true')
{
	$where=Search($currentModule);	
	// we have a query
	$url_string .="&query=true";

	$log->info("Here is the where clause for the list view: $where");
}

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Invoice");
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);
//<<<<<customview>>>>>

if(isPermitted('Invoice',2,'') == 'yes')
{
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}

if($viewnamedesc['viewname'] == 'All')
{
$cvHTML = '<td><a href="index.php?module=Invoice&action=CustomView">'.$app_strings['LNK_CV_CREATEVIEW'].'</a>
<span class="small">|</span>
<span class="small" disabled>'.$app_strings['LNK_CV_EDIT'].'</span>
<span class="small">|</span>
<span class="small" disabled>'.$app_strings['LNK_CV_DELETE'].'</span></td>';
}else
{
$cvHTML = '<td><a href="index.php?module=Invoice&action=CustomView">'.$app_strings['LNK_CV_CREATEVIEW'].'</a>
<span class="small">|</span>
<a href="index.php?module=Invoice&action=CustomView&record='.$viewid.'">'.$app_strings['LNK_CV_EDIT'].'</a>
<span class="small">|</span>
<a href="index.php?module=CustomView&action=Delete&dmodule=Invoice&record='.$viewid.'">'.$app_strings['LNK_CV_DELETE'].'</a></td>';
}
	$customstrings = '<td align="right">'.$app_strings[LBL_VIEW].'</td>
			  <td><SELECT NAME="viewname" class="small" onchange="showDefaultCustomView(this,\'Invoice\')">
				'.$customviewcombo_html.'
                          </SELECT></td>
			'.$cvHTML;



$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",'Invoice');
$smarty->assign("CUSTOMVIEW",$customstrings);
$smarty->assign("BUTTONS",$other_text);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
if($viewid != "0")
{
	$listquery = getListQuery("Invoice");
	$query = $oCustomView->getModifiedCvListQuery($viewid,$listquery,"Invoice");
}else
{
	$query = getListQuery("Invoice");
}
//<<<<<<<<customview>>>>>>>>>

if(isset($where) && $where != '')
{
        $query .= ' and '.$where;
}

//$url_qry = getURLstring($focus);

if(isset($order_by) && $order_by != '')
{
	if($order_by == 'smownerid')
        {
                $query .= ' ORDER BY user_name '.$sorder;
        }
        else
        {
		$tablename = getTableNameForField('Invoice',$order_by);
		$tablename = (($tablename != '')?($tablename."."):'');

                $query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
        }
}

$list_result = $adb->query($query);

//Retreiving the no of rows
$noofrows = $adb->num_rows($list_result);
        
//Retreiving the start value from request
if(isset($_REQUEST['start']) && $_REQUEST['start'] != '')
{
        $start = $_REQUEST['start'];

	//added to remain the navigation when sort
	$url_string = "&start=".$_REQUEST['start'];
}
else
{
        $start = 1;
}

//Retreive the Navigation array
$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);


// Setting the record count string
//modified by rdhital
$start_rec = $navigation_array['start'];
$end_rec = $navigation_array['end_val']; 
//By Raju Ends

$record_string= $app_strings[LBL_SHOWING]." " .$start_rec." - ".$end_rec." " .$app_strings[LBL_LIST_OF] ." ".$noofrows;

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

$listview_header = getListViewHeader($focus,"Invoice",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_header_search = getSearchListHeaderValues($focus,"Invoice",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("SEARCHLISTHEADER",$listview_header_search);

$listview_entries = getListViewEntries($focus,"Invoice",$list_result,$navigation_array,"","","EditView","Delete",$oCustomView);
$smarty->assign("LISTENTITY", $listview_entries);
$smarty->assign("SELECT_SCRIPT", $view_script);

$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string,"Invoice","index",$viewid);
$alphabetical = AlphabeticalSearch($currentModule,'index','subject','true','basic',"","","","",$viewid);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_COUNTS", $record_string);


if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else	
	$smarty->display("ListView.tpl");

?>
