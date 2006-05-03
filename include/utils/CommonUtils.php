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
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

  require_once('include/database/PearDatabase.php');
  require_once('include/ComboUtil.php'); //new
  require_once('include/utils/utils.php'); //new

/**
 * Check if user id belongs to a system admin.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function is_admin($user) {
	global $log;
	$log->debug("Entering is_admin(".$user.") method ...");
	
	if ($user->is_admin == 'on')
	{
		$log->debug("Exiting is_admin method ..."); 
		return true;
	}
	else
	{
		$log->debug("Exiting is_admin method ...");
		 return false;
	}
}

/**
 * THIS FUNCTION IS DEPRECATED AND SHOULD NOT BE USED; USE get_select_options_with_id()
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options (&$option_list, $selected, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options (".$option_list.",".$selected.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options  method ...");
	return get_select_options_with_id($option_list, $selected, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The values is an array of the datas 
 * param $option_list - the array of strings to that contains the option list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_select_options_with_id (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_with_id (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_with_id  method ...");
	return get_select_options_with_id_separate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */
function get_select_options_array (&$option_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_select_options_array (".$option_list.",".$selected_key.",".$advsearch.") method ...");
	$log->debug("Exiting get_select_options_array  method ...");
        return get_options_array_seperate_key($option_list, $option_list, $selected_key, $advsearch);
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.  The value is an array of data
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_options_array_seperate_key (&$label_list, &$key_list, $selected_key, $advsearch='false') {
	global $log;
	$log->debug("Entering get_options_array_seperate_key (".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
	global $app_strings;
	if($advsearch=='true')
	$select_options = "\n<OPTION value=''>--NA--</OPTION>";
	else
	$select_options = "";

	//for setting null selection values to human readable --None--
	$pattern = "/'0?'></";
	$replacement = "''>".$app_strings['LBL_NONE']."<";
	if (!is_array($selected_key)) $selected_key = array($selected_key);

	//create the type dropdown domain and set the selected value if $opp value already exists
	foreach ($key_list as $option_key=>$option_value) {
		$selected_string = '';
		// the system is evaluating $selected_key == 0 || '' to true.  Be very careful when changing this.  Test all cases.
		// The reported bug was only happening with one of the users in the drop down.  It was being replaced by none.
		if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
		{
			$selected_string = 'selected ';
		}

		$html_value = $option_key;

		$select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
		$options[$html_value]=array($label_list[$option_key]=>$selected_string);
	}
	$select_options = preg_replace($pattern, $replacement, $select_options);

	$log->debug("Exiting get_options_array_seperate_key  method ...");
	return $options;
}

/**
 * Create HTML to display select options in a dropdown list.  To be used inside
 * of a select statement in a form.   This method expects the option list to have keys and values.  The keys are the ids.
 * The values are the display strings.
 */

function get_select_options_with_id_separate_key(&$label_list, &$key_list, $selected_key, $advsearch='false')
{
	global $log;
    $log->debug("Entering get_select_options_with_id_separate_key(".$label_list.",".$key_list.",".$selected_key.",".$advsearch.") method ...");
    global $app_strings;
    if($advsearch=='true')
    $select_options = "\n<OPTION value=''>--NA--</OPTION>";
    else
    $select_options = "";

    $pattern = "/'0?'></";
    $replacement = "''>".$app_strings['LBL_NONE']."<";
    if (!is_array($selected_key)) $selected_key = array($selected_key);

    foreach ($key_list as $option_key=>$option_value) {
        $selected_string = '';
        if (($option_key != '' && $selected_key == $option_key) || ($selected_key == '' && $option_key == '') || (in_array($option_key, $selected_key)))
        {
            $selected_string = 'selected ';
        }

        $html_value = $option_key;

        $select_options .= "\n<OPTION ".$selected_string."value='$html_value'>$label_list[$option_key]</OPTION>";
    }
    $select_options = preg_replace($pattern, $replacement, $select_options);
    $log->debug("Exiting get_select_options_with_id_separate_key method ...");
    return $select_options;

}

/**
 * Converts localized date format string to jscalendar format
 * Example: $array = array_csort($array,'town','age',SORT_DESC,'name');
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function parse_calendardate($local_format) {
	global $log;
	$log->debug("Entering parse_calendardate(".$local_format.") method ...");
	global $current_user;
	if($current_user->date_format == 'dd-mm-yyyy')
	{
		$dt_popup_fmt = "%d-%m-%Y";
	}
	elseif($current_user->date_format == 'mm-dd-yyyy')
	{
		$dt_popup_fmt = "%m-%d-%Y";
	}
	elseif($current_user->date_format == 'yyyy-mm-dd')
	{
		$dt_popup_fmt = "%Y-%m-%d";
	}
	$log->debug("Exiting parse_calendardate method ...");
	return $dt_popup_fmt;
	//return "%Y-%m-%d";
}

/**
 * Decodes the given set of special character 
 * input values $string - string to be converted, $encode - flag to decode
 * returns the decoded value in string fromat
 */

function from_html($string, $encode=true){
	global $log;
	$log->debug("Entering from_html(".$string.",".$encode.") method ...");
        global $toHtml;
        //if($encode && is_string($string))$string = html_entity_decode($string, ENT_QUOTES);
        if($encode && is_string($string)){
                $string = str_replace(array_values($toHtml), array_keys($toHtml), $string);
        }
	$log->debug("Exiting from_html method ...");
        return $string;
}

/** To get the Currency of the specified user
  * @param $id -- The user Id:: Type integer
  * @returns  currencyid :: Type integer
 */
function fetchCurrency($id)
{
	global $log;
	$log->debug("Entering fetchCurrency(".$id.") method ...");
        global $adb;
        $sql = "select currency_id from users where id=" .$id;
        $result = $adb->query($sql);
        $currencyid=  $adb->query_result($result,0,"currency_id");
	$log->debug("Exiting fetchCurrency method ...");
        return $currencyid;
}

/** Function to get the Currency name from the currency_info
  * @param $currencyid -- currencyid:: Type integer
  * @returns $currencyname -- Currency Name:: Type varchar
  *
 */
function getCurrencyName($currencyid)
{
	global $log;
	$log->debug("Entering getCurrencyName(".$currencyid.") method ...");
        global $adb;
        $sql1 = "select * from currency_info where id=".$currencyid;
        $result = $adb->query($sql1);
        $currencyname = $adb->query_result($result,0,"currency_name");
        $curr_symbol = $adb->query_result($result,0,"currency_symbol");
	$log->debug("Exiting getCurrencyName method ...");
        return $currencyname.' : '.$curr_symbol;
}


/**
 * Function to fetch the list of groups from group table 
 * Takes no value as input 
 * returns the query result set object
 */

function get_group_options()
{
	global $log;
	$log->debug("Entering get_group_options() method ...");
	global $adb,$noof_group_rows;;
	$sql = "select groupname from groups";
	$result = $adb->query($sql);
	$noof_group_rows=$adb->num_rows($result);
	$log->debug("Exiting get_group_options method ...");
	return $result;
}

/**
 * Function to get the tabid 
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getTabid($module)
{
	global $log;
	$log->debug("Entering getTabid(".$module.") method ...");

	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) 
	{
		include('tabdata.php');
		$tabid= $tab_info_array[$module];
	}
	else
	{	

        $log->info("module  is ".$module);
        global $adb;
	$sql = "select tabid from tab where name='".$module."'";
	$result = $adb->query($sql);
	$tabid=  $adb->query_result($result,0,"tabid");
	}
	$log->debug("Exiting getTabid method ...");
	return $tabid;

}

/**
 * Function to get the tabid 
 * Takes the input as $module - module name
 * returns the tabid, integer type
 */

function getSalesEntityType($crmid)
{
	global $log;
	$log->debug("Entering getSalesEntityType(".$crmid.") method ...");
	$log->info("in getSalesEntityType ".$crmid);
	global $adb;
	$sql = "select * from crmentity where crmid=".$crmid;
        $result = $adb->query($sql);
	$parent_module = $adb->query_result($result,0,"setype");
	$log->debug("Exiting getSalesEntityType method ...");
	return $parent_module;
}

/**
 * Function to get the AccountName when a account id is given 
 * Takes the input as $acount_id - account id
 * returns the account name in string format.
 */

function getAccountName($account_id)
{
	global $log;
	$log->debug("Entering getAccountName(".$account_id.") method ...");
	$log->info("in getAccountName ".$account_id);

	global $adb;
	if($account_id != '')
	{
		$sql = "select accountname from account where accountid=".$account_id;
        	$result = $adb->query($sql);
		$accountname = $adb->query_result($result,0,"accountname");
	}
	$log->debug("Exiting getAccountName method ...");
	return $accountname;
}

/**
 * Function to get the ProductName when a product id is given 
 * Takes the input as $product_id - product id
 * returns the product name in string format.
 */

function getProductName($product_id)
{
	global $log;
	$log->debug("Entering getProductName(".$product_id.") method ...");

	$log->info("in getproductname ".$product_id);

	global $adb;
	$sql = "select productname from products where productid=".$product_id;
        $result = $adb->query($sql);
	$productname = $adb->query_result($result,0,"productname");
	$log->debug("Exiting getProductName method ...");
	return $productname;
}

/**
 * Function to get the Potentail Name when a potential id is given 
 * Takes the input as $potential_id - potential id
 * returns the potential name in string format.
 */

function getPotentialName($potential_id)
{
	global $log;
	$log->debug("Entering getPotentialName(".$potential_id.") method ...");
	$log->info("in getPotentialName ".$potential_id);

	global $adb;
	$potentialname = '';
	if($potential_id != '')
	{
		$sql = "select potentialname from potential where potentialid=".$potential_id;
        	$result = $adb->query($sql);
		$potentialname = $adb->query_result($result,0,"potentialname");
	}
	$log->debug("Exiting getPotentialName method ...");
	return $potentialname;
}

/**
 * Function to get the Contact Name when a contact id is given 
 * Takes the input as $contact_id - contact id
 * returns the Contact Name in string format.
 */

function getContactName($contact_id)
{
	global $log;
	$log->debug("Entering getContactName(".$contact_id.") method ...");
	$log->info("in getContactName ".$contact_id);

        global $adb;
        $sql = "select * from contactdetails where contactid=".$contact_id;
        $result = $adb->query($sql);
        $firstname = $adb->query_result($result,0,"firstname");
        $lastname = $adb->query_result($result,0,"lastname");
        $contact_name = $lastname.' '.$firstname;
	$log->debug("Exiting getContactName method ...");
        return $contact_name;
}

/**
 * Function to get the Vendor Name when a vendor id is given 
 * Takes the input as $vendor_id - vendor id
 * returns the Vendor Name in string format.
 */

function getVendorName($vendor_id)
{
	global $log;
	$log->debug("Entering getVendorName(".$vendor_id.") method ...");
	$log->info("in getVendorName ".$vendor_id);
        global $adb;
        $sql = "select * from vendor where vendorid=".$vendor_id;
        $result = $adb->query($sql);
        $vendor_name = $adb->query_result($result,0,"vendorname");
	$log->debug("Exiting getVendorName method ...");
        return $vendor_name;
}

/**
 * Function to get the Quote Name when a vendor id is given 
 * Takes the input as $quote_id - quote id
 * returns the Quote Name in string format.
 */

function getQuoteName($quote_id)
{
	global $log;
	$log->debug("Entering getQuoteName(".$quote_id.") method ...");
	$log->info("in getQuoteName ".$quote_id);
        global $adb;
        $sql = "select * from quotes where quoteid=".$quote_id;
        $result = $adb->query($sql);
        $quote_name = $adb->query_result($result,0,"subject");
	$log->debug("Exiting getQuoteName method ...");
        return $quote_name;
}

/**
 * Function to get the PriceBook Name when a pricebook id is given 
 * Takes the input as $pricebook_id - pricebook id
 * returns the PriceBook Name in string format.
 */

function getPriceBookName($pricebookid)
{
	global $log;
	$log->debug("Entering getPriceBookName(".$pricebookid.") method ...");
	$log->info("in getPriceBookName ".$pricebookid);
        global $adb;
        $sql = "select * from pricebook where pricebookid=".$pricebookid;
        $result = $adb->query($sql);
        $pricebook_name = $adb->query_result($result,0,"bookname");
	$log->debug("Exiting getPriceBookName method ...");
        return $pricebook_name;
}

/** This Function returns the  Purchase Order Name.
  * The following is the input parameter for the function
  *  $po_id --> Purchase Order Id, Type:Integer
  */
function getPoName($po_id)
{
	global $log;
	$log->debug("Entering getPoName(".$po_id.") method ...");
        $log->info("in getPoName ".$po_id);
        global $adb;
        $sql = "select * from purchaseorder where purchaseorderid=".$po_id;
        $result = $adb->query($sql);
        $po_name = $adb->query_result($result,0,"subject");
	$log->debug("Exiting getPoName method ...");
        return $po_name;
}
/**
 * Function to get the Sales Order Name when a salesorder id is given 
 * Takes the input as $salesorder_id - salesorder id
 * returns the Salesorder Name in string format.
 */

function getSoName($so_id)
{
	global $log;
	$log->debug("Entering getSoName(".$so_id.") method ...");
	$log->info("in getSoName ".$so_id);
	global $adb;
        $sql = "select * from salesorder where salesorderid=".$so_id;
        $result = $adb->query($sql);
        $so_name = $adb->query_result($result,0,"subject");
	$log->debug("Exiting getSoName method ...");
        return $so_name;
}

/**
 * Function to get the Group Information for a given groupid  
 * Takes the input $id - group id and $module - module name
 * returns the group information in an array format.
 */

function getGroupName($id, $module)
{
	global $log;
	$log->debug("Entering getGroupName(".$id.",".$module.") method ...");
	$group_info = Array();
        $log->info("in getGroupName, entityid is ".$id.'  module is    '.$module);
        global $adb;
        if($module == 'Leads')
        {
               $sql = "select leadgrouprelation.groupname,groups.groupid from leadgrouprelation inner join groups on groups.groupname=leadgrouprelation.groupname where leadgrouprelation.leadid=".$id;
        }
        elseif($module == 'Accounts')
        {
               $sql = "select accountgrouprelation.groupname,groups.groupid from accountgrouprelation inner join groups on groups.groupname=accountgrouprelation.groupname where accountgrouprelation.accountid=".$id;
        }
        elseif($module == 'Contacts')
        {
               $sql = "select contactgrouprelation.groupname,groups.groupid from contactgrouprelation inner join groups on groups.groupname=contactgrouprelation.groupname where contactgrouprelation.contactid=".$id;
        }
	elseif($module == 'Potentials')
        {
               $sql = "select potentialgrouprelation.groupname,groups.groupid from potentialgrouprelation inner join groups on groups.groupname=potentialgrouprelation.groupname where potentialgrouprelation.potentialid=".$id;
        }
	elseif($module == 'Quotes')
        {
               $sql = "select quotegrouprelation.groupname,groups.groupid from quotegrouprelation inner join groups on groups.groupname=quotegrouprelation.groupname where quotegrouprelation.quoteid=".$id;
        }
	elseif($module == 'SalesOrder')
        {
               $sql = "select sogrouprelation.groupname,groups.groupid from sogrouprelation inner join groups on groups.groupname=sogrouprelation.groupname where sogrouprelation.salesorderid=".$id;
        }
	elseif($module == 'Invoice')
        {
               $sql = "select invoicegrouprelation.groupname,groups.groupid from invoicegrouprelation inner join groups on groups.groupname=invoicegrouprelation.groupname where invoicegrouprelation.invoiceid=".$id;
        }
	elseif($module == 'PurchaseOrder')
        {
               $sql = "select pogrouprelation.groupname,groups.groupid from pogrouprelation inner join groups on groups.groupname=pogrouprelation.groupname where pogrouprelation.purchaseorderid=".$id;
        }
        elseif($module == 'HelpDesk')
        {
               $sql = "select ticketgrouprelation.groupname,groups.groupid from ticketgrouprelation inner join groups on groups.groupname=ticketgrouprelation.groupname where ticketgrouprelation.ticketid=".$id;
        }
	elseif($module == 'Campaigns')
	{
	       $sql = "select campaigngrouprelation.groupname,groups.groupid from campaigngrouprelation inner join groups on groups.groupname=campaigngrouprelation.groupname where campaigngrouprelation.campaignid=".$id;
        }
        elseif($module == 'Activities' || $module == 'Emails' || $module == 'Events')
        {
               $sql = "select activitygrouprelation.groupname,groups.groupid from activitygrouprelation inner join groups on groups.groupname=activitygrouprelation.groupname where activitygrouprelation.activityid=".$id;
	}
	$result = $adb->query($sql);
        $group_info[] = $adb->query_result($result,0,"groupname");
        $group_info[] = $adb->query_result($result,0,"groupid");
	$log->debug("Exiting getGroupName method ...");
        return $group_info;

}

/**
 * Get the username by giving the user id.   This method expects the user id
 * param $label_list - the array of strings to that contains the option list
 * param $key_list - the array of strings to that contains the values list
 * param $selected - the string which contains the default value
 */
     
function getUserName($userid)
{
	global $log;
	$log->debug("Entering getUserName(".$userid.") method ...");
	$log->info("in getUserName ".$userid);

	global $adb;
	if($userid != '')
	{
		$sql = "select user_name from users where id=".$userid;
		$result = $adb->query($sql);
		$user_name = $adb->query_result($result,0,"user_name");
	}
	$log->debug("Exiting getUserName method ...");
	return $user_name;	
}

/**
 * Creates and returns database query. To be used for search and other text links.   This method expects the module object.
 * param $focus - the module object contains the column fields
 */
   
function getURLstring($focus)
{
	global $log;
	$log->debug("Entering getURLstring(".$focus.") method ...");
	$qry = "";
	foreach($focus->column_fields as $fldname=>$val)
	{
		if(isset($_REQUEST[$fldname]) && $_REQUEST[$fldname] != '')
		{
			if($qry == '')
			$qry = "&".$fldname."=".$_REQUEST[$fldname];
			else
			$qry .="&".$fldname."=".$_REQUEST[$fldname];
		}
	}
	if(isset($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] !='')
	{
		$qry .="&current_user_only=".$_REQUEST['current_user_only'];
	}
	if(isset($_REQUEST['advanced']) && $_REQUEST['advanced'] =='true')
	{
		$qry .="&advanced=true";
	}

	if($qry !='')
	{
		$qry .="&query=true";
	}
	$log->debug("Exiting getURLstring method ...");
	return $qry;

}

/** This function returns the date in user specified format.
  * param $cur_date_val - the default date format
 */
    
function getDisplayDate($cur_date_val)
{
	global $log;
	$log->debug("Entering getDisplayDate(".$cur_date_val.") method ...");
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '')
	{
		$dat_fmt = 'dd-mm-yyyy';
	}

		$date_value = explode(' ',$cur_date_val);
		list($y,$m,$d) = split('-',$date_value[0]);
		if($dat_fmt == 'dd-mm-yyyy')
		{
			$display_date = $d.'-'.$m.'-'.$y;
		}
		elseif($dat_fmt == 'mm-dd-yyyy')
		{

			$display_date = $m.'-'.$d.'-'.$y;
		}
		elseif($dat_fmt == 'yyyy-mm-dd')
		{

			$display_date = $y.'-'.$m.'-'.$d;
		}

		if($date_value[1] != '')
		{
			$display_date = $display_date.' '.$date_value[1];
		}
	$log->debug("Exiting getDisplayDate method ...");
	return $display_date;
 			
}

/** This function returns the date in user specified format.
  * Takes no param, receives the date format from current user object
  */
    
function getNewDisplayDate()
{
	global $log;
	$log->debug("Entering getNewDisplayDate() method ...");
        $log->info("in getNewDisplayDate ");

	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '')
        {
                $dat_fmt = 'dd-mm-yyyy';
        }
	//echo $dat_fmt;
	//echo '<BR>';
	$display_date='';
	if($dat_fmt == 'dd-mm-yyyy')
	{
		$display_date = date('d-m-Y');
	}
	elseif($dat_fmt == 'mm-dd-yyyy')
	{
		$display_date = date('m-d-Y');
	}
	elseif($dat_fmt == 'yyyy-mm-dd')
	{
		$display_date = date('Y-m-d');
	}
		
	//echo $display_date;
	$log->debug("Exiting getNewDisplayDate method ...");
	return $display_date;
}

/** This function returns the default currency information.
  * Takes no param, return type array.
    */
    
function getDisplayCurrency()
{
	global $log;
	global $adb;
	$log->debug("Entering getDisplayCurrency() method ...");
        $curr_array = Array();
        $sql1 = "select * from currency_info where currency_status='Active'";
        $result = $adb->query($sql1);
        $num_rows=$adb->num_rows($result);
        for($i=0; $i<$num_rows;$i++)
        {
                $curr_id = $adb->query_result($result,$i,"id");
                $curr_name = $adb->query_result($result,$i,"currency_name");
                $curr_symbol = $adb->query_result($result,$i,"currency_symbol");
                $curr_array[$curr_id] = $curr_name.' : '.$curr_symbol;
        }
	$log->debug("Exiting getDisplayCurrency method ...");
        return $curr_array;
}

/** This function returns the amount converted to dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
      
function convertToDollar($amount,$crate){
	global $log;
	$log->debug("Entering convertToDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertToDollar method ...");
        return $amount / $crate;
}

/** This function returns the amount converted from dollar.
  * param $amount - amount to be converted.
    * param $crate - conversion rate.
      */
function convertFromDollar($amount,$crate){
	global $log;
	$log->debug("Entering convertFromDollar(".$amount.",".$crate.") method ...");
	$log->debug("Exiting convertFromDollar method ...");
        return $amount * $crate;
}

/** This function returns the conversion rate and currency symbol
  * in array format for a given id.
  * param $id - currency id.
  */
      
function getCurrencySymbolandCRate($id)
{
	global $log;
	$log->debug("Entering getCurrencySymbolandCRate(".$id.") method ...");
        global $adb;
        $sql1 = "select conversion_rate,currency_symbol from currency_info where id=".$id;
        $result = $adb->query($sql1);
	$rate_symbol['rate'] = $adb->query_result($result,0,"conversion_rate");
	$rate_symbol['symbol'] = $adb->query_result($result,0,"currency_symbol");
	$log->debug("Exiting getCurrencySymbolandCRate method ...");
	return $rate_symbol;
}

/** This function returns the terms and condition from the database.
  * Takes no param and the return type is text.
  */
	    
function getTermsandConditions()
{
	global $log;
	$log->debug("Entering getTermsandConditions() method ...");
        global $adb;
        $sql1 = "select * from inventory_tandc";
        $result = $adb->query($sql1);
        $tandc = $adb->query_result($result,0,"tandc");
	$log->debug("Exiting getTermsandConditions method ...");
        return $tandc;
}

/**
 * Create select options in a dropdown list.  To be used inside
  *  a reminder select statement in a activity form. 
   * param $start - start value
   * param $end - end value
   * param $fldname - field name 
   * param $selvalue - selected value 
   */
    
function getReminderSelectOption($start,$end,$fldname,$selvalue='')
{
	global $log;
	$log->debug("Entering getReminderSelectOption(".$start.",".$end.",".$fldname.",".$selvalue=''.") method ...");
	global $mod_strings;
	global $app_strings;
	
	$def_sel ="";
	$OPTION_FLD = "<SELECT name=".$fldname.">";
	for($i=$start;$i<=$end;$i++)
	{
		if($i==$selvalue)
		$def_sel = "SELECTED";
		$OPTION_FLD .= "<OPTION VALUE=".$i." ".$def_sel.">".$i."</OPTION>\n";
		$def_sel = "";
	}
	$OPTION_FLD .="</SELECT>";
	$log->debug("Exiting getReminderSelectOption method ...");
	return $OPTION_FLD;
}

/** This function returns the List price of a given product in a given price book.
  * param $productid - product id.
  * param $pbid - pricebook id.
  */
  
function getListPrice($productid,$pbid)
{
	global $log;
	$log->debug("Entering getListPrice(".$productid.",".$pbid.") method ...");
        $log->info("in getListPrice productid ".$productid);

	global $adb;
	$query = "select listprice from pricebookproductrel where pricebookid=".$pbid." and productid=".$productid;
	$result = $adb->query($query);
	$lp = $adb->query_result($result,0,'listprice');
	$log->debug("Exiting getListPrice method ...");
	return $lp;
}

/** This function returns a string with removed new line character, single quote, and back slash double quoute.
  * param $str - string to be converted.
  */
      
function br2nl($str) {
   global $log;
   $log->debug("Entering br2nl(".$str.") method ...");
   $str = preg_replace("/(\r\n)/", " ", $str);
   $str = preg_replace("/'/", " ", $str);
   $str = preg_replace("/\"/", " ", $str);
   $log->debug("Exiting br2nl method ...");
   return $str;
}

/** This function returns a text, which escapes the html encode for link tag/ a href tag
*param $text - string/text
*/

function make_clickable($text)
{
   global $log;
   $log->debug("Entering make_clickable(".$text.") method ...");
   $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);
   // pad it with a space so we can match things at the start of the 1st line.
   $ret = ' ' . $text;

   // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
   // xxxx can only be alpha characters.
   // yyyy is anything up to the first space, newline, comma, double quote or <
   $ret = preg_replace("#(^|[\n ])([\w]+?://.*?[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
   // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
   // zzzz is optional.. will contain everything up to the first space, newline,
   // comma, double quote or <.
   $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\-]+\.[\w\-.\~]+(?:/[^ \"\t\n\r<]*)?)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

   // matches an email@domain type address at the start of a line, or after a space.
   // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
   $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

   // Remove our padding..
   $ret = substr($ret, 1);

   //remove comma, fullstop at the end of url
   $ret = preg_replace("#,\"|\.\"|\)\"|\)\.\"|\.\)\"#", "\"", $ret);

   $log->debug("Exiting make_clickable method ...");
   return($ret);
}
/**
 * This function returns the blocks and its related information for given module.
 * Input Parameter are $module - module name, $disp_view = display view (edit,detail or create),$mode - edit, $col_fields - * column fields/
 * This function returns an array
 */

function getBlocks($module,$disp_view,$mode,$col_fields='',$info_type='')
{
	global $log;
	$log->debug("Entering getBlocks(".$module.",".$disp_view.",".$mode.",".$col_fields.",".$info_type.") method ...");
        global $adb,$current_user;
        global $mod_strings;
        $tabid = getTabid($module);
        $block_detail = Array();
        $getBlockinfo = "";
        $query="select blockid,blocklabel,show_title from blocks where tabid=$tabid and $disp_view=0 and visible = 0 order by sequence";

        $result = $adb->query($query);
        $noofrows = $adb->num_rows($result);
        $prev_header = "";
	$blockid_list ='(';
	for($i=0; $i<$noofrows; $i++)
	{
		$blockid = $adb->query_result($result,$i,"blockid");
		if($i != 0)
			$blockid_list .= ', ';
		$blockid_list .= $blockid;
		$block_label[$blockid] = $adb->query_result($result,$i,"blocklabel");
	}
	$blockid_list .= ')';
	//retreive the profileList from database
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($disp_view == "detail_view")
	{
		if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
		{
			$sql = "select field.* from field where field.tabid=".$tabid." and field.block in $blockid_list and field.displaytype in (1,2) order by block,sequence";
		}
		else
		{
			$profileList = getCurrentUserProfileList();
			$sql = "select field.* from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid where field.tabid=".$tabid." and field.block in ".$blockid_list." and field.displaytype in (1,2) and profile2field.visible=0 and def_org_field.visible=0 and profile2field.profileid in ".$profileList." group by field.fieldid order by block,sequence";
		}
		$result = $adb->query($sql);
		$getBlockInfo=getDetailBlockInformation($module,$result,$col_fields,$tabid,$block_label);
	}
	else
	{
		if ($info_type != '')
		{
			if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2]== 0)
			{
				$sql = "select field.* from field where field.tabid=".$tabid." and field.block in ".$blockid_list ." and field.displaytype=1 and info_type = '".$info_type."' order by block,sequence";
			}
			else
			{
				$profileList = getCurrentUserProfileList();
				$sql = "select field.* from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid  where field.tabid=".$tabid." and field.block in ".$blockid_list." and field.displaytype=1 and info_type = '".$info_type."' and profile2field.visible=0 and def_org_field.visible=0 and profile2field.profileid in ".$profileList.=" group by field.fieldid order by block,sequence";
			}
		}
		else
		{
			if($is_admin==true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
			{
				$sql = "select field.* from field where field.tabid=".$tabid." and field.block in ".$blockid_list." and field.displaytype=1 order by block,sequence";
			}
			else
			{
				$profileList = getCurrentUserProfileList();
				$sql = "select field.* from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid  where field.tabid=".$tabid." and field.block in ".$blockid_list." and field.displaytype=1 and profile2field.visible=0 and def_org_field.visible=0 and profile2field.profileid in ".$profileList.=" group by field.fieldid order by block,sequence";
			}
		}
		$result = $adb->query($sql);
                $getBlockInfo=getBlockInformation($module,$result,$col_fields,$tabid,$block_label);	
	}
	$log->debug("Exiting getBlocks method ...");
	return $getBlockInfo;
}	
/**
 * This function is used to get the display type.
 * Takes the input parameter as $mode - edit  (mostly)
 * This returns string type value
 */

function getView($mode)
{
	global $log;
	$log->debug("Entering getView(".$mode.") method ...");
        if($mode=="edit")
	        $disp_view = "edit_view";
        else
	        $disp_view = "create_view";
	$log->debug("Exiting getView method ...");
        return $disp_view;
}
/**
 * This function is used to get the blockid of the customblock for a given module.
 * Takes the input parameter as $tabid - module tabid and $label - custom label
 * This returns string type value
 */

function getBlockId($tabid,$label)
{
	global $log;
	$log->debug("Entering getBlockId(".$tabid.",".$label.") method ...");
        global $adb;
        $blockid = '';
        $query = "select blockid from blocks where tabid=$tabid and blocklabel = '$label'";
        $result = $adb->query($query);
        $noofrows = $adb->num_rows($result);
        if($noofrows == 1)
        {
                $blockid = $adb->query_result($result,0,"blockid");
        }
	$log->debug("Exiting getBlockId method ...");
        return $blockid;
}

/**
 * This function is used to get the Parent and Child tab relation array.
 * Takes no parameter and get the data from parent_tabdata.php and tabdata.php
 * This returns array type value
 */

function getHeaderArray()
{
	global $log;
	$log->debug("Entering getHeaderArray() method ...");
	global $adb;
	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	include('parent_tabdata.php');
	include('tabdata.php');
	$noofrows = count($parent_tab_info_array);
	foreach($parent_tab_info_array as $parid=>$parval)
	{
		$subtabs = Array();
		$tablist=$parent_child_tab_rel_array[$parid];
		$noofsubtabs = count($tablist);

		foreach($tablist as $childTabId)
		{
			$module = array_search($childTabId,$tab_info_array);
			
			if($is_admin)
			{
				$subtabs[] = $module;
			}	
			elseif($profileGlobalPermission[2]==0 ||$profileGlobalPermission[1]==0 || $profileTabsPermission[$childTabId]==0) 
			{
				$subtabs[] = $module;
			}	
		}

		$parenttab = getParentTabName($parid);

		if($parenttab == 'Settings' && $is_admin)
		{
			$subtabs[] = 'Settings';
		}
		if($parenttab != 'Settings' ||($parenttab == 'Settings' && $is_admin))
		{
			if(!empty($subtabs))
				$relatedtabs[$parenttab] = $subtabs;
		}
	}
	$log->debug("Exiting getHeaderArray method ...");
	return $relatedtabs;
}

/**
 * This function is used to get the Parent Tab name for a given parent tab id.
 * Takes the input parameter as $parenttabid - Parent tab id
 * This returns value string type 
 */

function getParentTabName($parenttabid)
{
	global $log;
	$log->debug("Entering getParentTabName(".$parenttabid.") method ...");
	global $adb;
	if (file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
	{
		include('parent_tabdata.php');
		$parent_tabname= $parent_tab_info_array[$parenttabid];
	}
	else
	{
		$sql = "select parenttab_label from parenttab where parenttabid=".$parenttabid;
		$result = $adb->query($sql);
		$parent_tabname=  $adb->query_result($result,0,"parenttab_label");
	}
	$log->debug("Exiting getParentTabName method ...");
	return $parent_tabname;
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes the input parameter as $module - module name
 * This returns value string type 
 */


function getParentTabFromModule($module)
{
	global $log;
	$log->debug("Entering getParentTabFromModule(".$module.") method ...");
	global $adb;
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0) && file_exists('parent_tabdata.php') && (filesize('parent_tabdata.php') != 0))
	{
		include('tabdata.php');
		include('parent_tabdata.php');
		$tabid=$tab_info_array[$module];
		foreach($parent_child_tab_rel_array as $parid=>$childArr)
		{
			if(in_array($tabid,$childArr))
			{
				$parent_tabname= $parent_tab_info_array[$parid];
			}
		}
		$log->debug("Exiting getParentTabFromModule method ...");
		return $parent_tabname;
	}
	else
	{
		$sql = "select parenttab.* from parenttab inner join parenttabrel on parenttabrel.parenttabid=parenttab.parenttabid inner join tab on tab.tabid=parenttabrel.tabid where tab.name='".$module."'";
		$result = $adb->query($sql);
		$tab =  $adb->query_result($result,0,"parenttab_label");
		$log->debug("Exiting getParentTabFromModule method ...");
		return $tab;
	}
}

/**
 * This function is used to get the Parent Tab name for a given module.
 * Takes no parameter but gets the parenttab value from form request
 * This returns value string type 
 */

function getParentTab()
{
    global $log;	
    $log->debug("Entering getParentTab() method ...");
    if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] !='')
    {
     	       $log->debug("Exiting getParentTab method ...");
               return $_REQUEST['parenttab'];
    }
    else
    {
		$log->debug("Exiting getParentTab method ...");
                return getParentTabFromModule($_REQUEST['module']);
    }

}
/**
 * This function is used to get the days in between the current time and the modified time of an entity .
 * Takes the input parameter as $id - crmid  it will calculate the number of days in between the
 * the current time and the modified time from the crmentity table and return the result as a string.
 * The return format is updated <No of Days> day ago <(date when updated)>
 */

function updateInfo($id)
{
    global $log;
    $log->debug("Entering updateInfo(".$id.") method ...");

    global $adb;
    global $app_strings;
    $query='select modifiedtime from crmentity where crmid ='.$id ;
    $result = $adb->query($query);
    $modifiedtime = $adb->query_result($result,0,'modifiedtime');
    $values=explode(' ',$modifiedtime);
    $date_info=explode('-',$values[0]);
    $time_info=explode(':',$values[1]);
    $date = $date_info[2].' '.date("M", mktime(0, 0, 0, $date_info[1], $date_info[2],$date_info[0])).' '.$date_info[0];
    $time_modified = mktime($time_info[0], $time_info[1], $time_info[2], $date_info[1], $date_info[2],$date_info[0]);
    $time_now = time();
    $days_diff = (int)(($time_now - $time_modified) / (60 * 60 * 24));
    if($days_diff == 0)
        $update_info = $app_strings['LBL_UPDATED_TODAY']." (".$date.")";
    elseif($days_diff == 1)
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAY_AGO']." (".$date.")";
    else
        $update_info = $app_strings['LBL_UPDATED']." ".$days_diff." ".$app_strings['LBL_DAYS_AGO']." (".$date.")";

    $log->debug("Exiting updateInfo method ...");
    return $update_info;
}


/**
 * This function is used to get the Product Images for the given Product  .
 * It accepts the product id as argument and returns the Images with the script for 
 * rotating the product Images
 */

function getProductImages($id)
{
	global $log;
	$log->debug("Entering getProductImages(".$id.") method ...");
	global $adb;
	$image_lists=array();
	$script_images=array();
	$script = '<script>var ProductImages = new Array(';
   	$i=0;
	$query='select imagename from products where productid='.$id;
	$result = $adb->query($query);
	$imagename=$adb->query_result($result,0,'imagename');
	$image_lists=explode('###',$imagename);
	for($i=0;$i<count($image_lists);$i++)
	{
		$script_images[] = '"'.$image_lists[$i].'"';
	}
	$script .=implode(',',$script_images).');</script>';
	if($imagename != '')
	{
		$log->debug("Exiting getProductImages method ...");
		return $script;
	}
}	

/**
 * This function is used to save the Images .
 * It acceps the File lists,modulename,id and the mode as arguments  
 * It returns the array details of the upload
 */

function SaveImage($_FILES,$module,$id,$mode)
{
	global $log;
	$log->debug("Entering SaveImage(".$_FILES.",".$module.",".$id.",".$mode.") method ...");
	global $adb;
	$uploaddir = $root_directory."test/".$module."/" ;//set this to which location you need to give the contact image
	$log->info("The Location to Save the Contact Image is ".$uploaddir);
	$file_path_name = $_FILES['imagename']['name'];
	$image_error="false";
	$saveimage="true";
	$file_name = basename($file_path_name);
	if($file_name!="")
	{

		$log->debug("Contact Image is given for uploading");
		$image_name_val=file_exist_fn($file_name,0);

		$encode_field_values="";
		$errormessage="";

		$move_upload_status=move_uploaded_file($_FILES["imagename"]["tmp_name"],$uploaddir.$image_name_val);
		$image_error="false";

		//if there is an error in the uploading of image

		$filetype= $_FILES['imagename']['type'];
		$filesize = $_FILES['imagename']['size'];

		$filetype_array=explode("/",$filetype);

		$file_type_val_image=strtolower($filetype_array[0]);
		$file_type_val=strtolower($filetype_array[1]);
		$log->info("The File type of the Contact Image is :: ".$file_type_val);
		//checking the uploaded image is if an image type or not
		if(!$move_upload_status) //if any error during file uploading
		{
			$log->debug("Error is present in uploading Contact Image.");
			$errorCode =  $_FILES['imagename']['error'];
			if($errorCode == 4)
			{
				$errorcode="no-image";
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 2)
			{
				$errormessage = 2;
				$saveimage="false";
				$image_error="true";
			}
			else if($errorCode == 3 )
			{
				$errormessage = 3;
				$saveimage="false";
				$image_error="true";
			}
		}
		else
		{
			$log->debug("Successfully uploaded the Contact Image.");
			if($filesize != 0)
			{
				if (($file_type_val == "jpeg" ) || ($file_type_val == "png") || ($file_type_val == "jpg" ) || ($file_type_val == "pjpeg" ) || ($file_type_val == "x-png") || ($file_type_val == "gif") ) //Checking whether the file is an image or not
				{
					$saveimage="true";
					$image_error="false";
				}
				else
				{
					$savelogo="false";
					$image_error="true";
					$errormessage = "image";
				}
			}
			else
			{       
				$savelogo="false";
				$image_error="true";
				$errormessage = "invalid";
			}

		}
	}
	else //if image is not given
	{
		$log->debug("Contact Image is not given for uploading.");
		if($mode=="edit" && $image_error=="false" )
		{
			if($module='contact')
			$image_name_val=getContactImageName($id);
			elseif($module='user')
			$image_name_val=getUserImageName($id);
			$saveimage="true";
		}
		else
		{
			$image_name_val="";
		}
	}
	$return_value=array('imagename'=>$image_name_val,
	'imageerror'=>$image_error,
	'errormessage'=>$errormessage,
	'saveimage'=>$saveimage,
	'mode'=>$mode);
	$log->debug("Exiting SaveImage method ...");
	return $return_value;
}

 /**
 * This function is used to generate file name if more than one image with same name is added to a given Product.
 * Param $filename - product file name
 * Param $exist - number time the file name is repeated.
 */

function file_exist_fn($filename,$exist)
{
	global $log;
	$log->debug("Entering file_exist_fn(".$filename.",".$exist.") method ...");
	global $uploaddir;

	if(!isset($exist))
	{
		$exist=0;
	}
	$filename_path=$uploaddir.$filename;
	if (file_exists($filename_path)) //Checking if the file name already exists in the directory
	{
		if($exist!=0)
		{
			$previous=$exist-1;
			$next=$exist+1;
			$explode_name=explode("_",$filename);
			$implode_array=array();
			for($j=0;$j<count($explode_name); $j++)
			{
				if($j!=0)
				{
					$implode_array[]=$explode_name[$j];
				}
			}
			$implode_name=implode("_", $implode_array);
			$test_name=$implode_name;
		}
		else
		{
			$implode_name=$filename;
		}
		$exist++;
		$filename_val=$exist."_".$implode_name;
		$testfilename = file_exist_fn($filename_val,$exist);
		if($testfilename!="")
		{
			$log->debug("Exiting file_exist_fn method ...");
			return $testfilename;
		}
	}	
	else
	{
		$log->debug("Exiting file_exist_fn method ...");
		return $filename;
	}
}

/**
 * This function is used get the User Count.
 * It returns the array which has the total users ,admin users,and the non admin users 
 */

function UserCount()
{
	global $log;
	$log->debug("Entering UserCount() method ...");
	global $adb;
	$result=$adb->query("select * from users where deleted =0;");
	$user_count=$adb->num_rows($result);
	$result=$adb->query("select * from users where deleted =0 AND is_admin != 'on';");
	$nonadmin_count = $adb->num_rows($result);
	$admin_count = $user_count-$nonadmin_count;
	$count=array('user'=>$user_count,'admin'=>$admin_count,'nonadmin'=>$nonadmin_count);
	$log->debug("Exiting UserCount method ...");
	return $count;
}

/**
 * This function is used to create folders recursively.
 * Param $dir - directory name
 * Param $mode - directory access mode
 * Param $recursive - create directory recursive, default true
 */

function mkdirs($dir, $mode = 0777, $recursive = true)
{
	global $log;
	$log->debug("Entering mkdirs(".$dir.",".$mode.",".$recursive.") method ...");
	if( is_null($dir) || $dir === "" ){
		$log->debug("Exiting mkdirs method ...");
		return FALSE;
	}
	if( is_dir($dir) || $dir === "/" ){
		$log->debug("Exiting mkdirs method ...");
		return TRUE;
	}
	if( mkdirs(dirname($dir), $mode, $recursive) ){
		$log->debug("Exiting mkdirs method ...");
		return mkdir($dir, $mode);
	}
	$log->debug("Exiting mkdirs method ...");
	return FALSE;
}

/**This function returns the module name which has been set as default home view for a given user.
 * Takes no parameter, but uses the user object $current_user.
 */
function DefHomeView()
{
		global $log;
		$log->debug("Entering DefHomeView() method ...");
		global $adb;
		global $current_user;
		$query="select defhomeview from users where id = ".$current_user->id;
		$result=$adb->query($query);
		$defaultview=$adb->query_result($result,0,'defhomeview');
		$log->debug("Exiting DefHomeView method ...");
		return $defaultview;

}


/**
 * This function is used to set the Object values from the REQUEST values.
 * @param  object reference $focus - reference of the object
 */
function setObjectValuesFromRequest($focus)
{
	global $log;
	$log->debug("Entering setObjectValuesFromRequest(".$focus.") method ...");
	if(isset($_REQUEST['record']))
	{
		$focus->id = $_REQUEST['record'];
	}
	if(isset($_REQUEST['mode']))
	{
		$focus->mode = $_REQUEST['mode'];
	}
	foreach($focus->column_fields as $fieldname => $val)
	{
		if(isset($_REQUEST[$fieldname]))
		{
			$value = $_REQUEST[$fieldname];
			$focus->column_fields[$fieldname] = $value;
		}
	}
	$log->debug("Exiting setObjectValuesFromRequest method ...");
}

 /**
 * Function to write the tabid and name to a flat file tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */

function create_tab_data_file()
{
	global $log;
	$log->debug("Entering create_tab_data_file() method ...");
        $log->info("creating tabdata file");
        global $adb;
        $sql = "select * from tab";
        $result = $adb->query($sql);
        $num_rows=$adb->num_rows($result);
        $result_array=Array();
	$seq_array=Array();
        for($i=0;$i<$num_rows;$i++)
        {
                $tabid=$adb->query_result($result,$i,'tabid');
                $tabname=$adb->query_result($result,$i,'name');
		$presence=$adb->query_result($result,$i,'presence');
                $result_array[$tabname]=$tabid;
		$seq_array[$tabid]=$presence;

        }

        $filename = 'tabdata.php';
	
	
if (file_exists($filename)) {

        if (is_writable($filename))
        {

                if (!$handle = fopen($filename, 'w+')) {
                        echo "Cannot open file ($filename)";
                        exit;
                }
	require_once('modules/Users/CreateUserPrivilegeFile.php');
                $newbuf='';
                $newbuf .="<?php\n\n";
                $newbuf .="\n";
                $newbuf .= "//This file contains the commonly used variables \n";
                $newbuf .= "\n";
                $newbuf .= "\$tab_info_array=".constructArray($result_array).";\n";
                $newbuf .= "\n";
                $newbuf .= "\$tab_seq_array=".constructArray($seq_array).";\n";
                $newbuf .= "?>";
                fputs($handle, $newbuf);
                fclose($handle);

        }
        else
        {
                echo "The file $filename is not writable";
        }

}
else
{
	echo "The file $filename does not exist";
	$log->debug("Exiting create_tab_data_file method ...");
	return;
}
}


 /**
 * Function to write the parenttabid and name to a flat file parent_tabdata.txt so that the data
 * is obtained from the file instead of repeated queries
 * returns null
 */

function create_parenttab_data_file()
{
	global $log;
	$log->debug("Entering create_parenttab_data_file() method ...");
	$log->info("creating parent_tabdata file");
	global $adb;
	$sql = "select parenttabid,parenttab_label from parenttab order by sequence";
	$result = $adb->query($sql);
	$num_rows=$adb->num_rows($result);
	$result_array=Array();
	for($i=0;$i<$num_rows;$i++)
	{
		$parenttabid=$adb->query_result($result,$i,'parenttabid');
		$parenttab_label=$adb->query_result($result,$i,'parenttab_label');
		$result_array[$parenttabid]=$parenttab_label;

	}

	$filename = 'parent_tabdata.php';


	if (file_exists($filename)) {

		if (is_writable($filename))
		{

			if (!$handle = fopen($filename, 'w+'))
			{
				echo "Cannot open file ($filename)";
				exit;
			}
			require_once('modules/Users/CreateUserPrivilegeFile.php');
			$newbuf='';
			$newbuf .="<?php\n\n";
			$newbuf .="\n";
			$newbuf .= "//This file contains the commonly used variables \n";
			$newbuf .= "\n";
			$newbuf .= "\$parent_tab_info_array=".constructSingleStringValueArray($result_array).";\n";
			$newbuf .="\n";
			

			$parChildTabRelArray=Array();

			foreach($result_array as $parid=>$parvalue)
			{
				$childArray=Array();
				$sql = "select * from parenttabrel where parenttabid=".$parid." order by sequence";
				$result = $adb->query($sql);
				$num_rows=$adb->num_rows($result);
				$result_array=Array();
				for($i=0;$i<$num_rows;$i++)
				{
					$tabid=$adb->query_result($result,$i,'tabid');
					$childArray[]=$tabid;
				}
				$parChildTabRelArray[$parid]=$childArray;

			}
			$newbuf .= "\n";
			$newbuf .= "\$parent_child_tab_rel_array=".constructTwoDimensionalValueArray($parChildTabRelArray).";\n";
			$newbuf .="\n";
			 $newbuf .="\n";
                        $newbuf .="\n";
                        $newbuf .= "?>";
                        fputs($handle, $newbuf);
                        fclose($handle);

		}
		else
		{
			echo "The file $filename is not writable";
		}

	}
	else
	{
		echo "The file $filename does not exist";
		$log->debug("Exiting create_parenttab_data_file method ...");
		return;
	}
}

/**
 * This function is used to get the File Storage Path in the server.
 * @param int $attachmentid - file attachment id ie., crmid of the attachment
 * @param string $filename  - file name
 * return string $filepath  - filepath inwhere the file stored in the server will be return
*/
function getFilePath($attachmentid,$filename)
{
	global $log;
	$log->debug("Entering getFilePath(".$attachmentid.",".$filename.") method ...");
	global $adb;
	global $root_directory;

	$query = 'select crmid, setype, smownerid, users.user_name from crmentity inner join users on crmentity.smownerid=users.id where crmid='.$attachmentid;
	$res = $adb->query($query);

	$user_name = $adb->query_result($res,0,'user_name');

	if(is_file($root_directory.'storage/user_'.$user_name.'/attachments/'.$filename))
		$filepath = $root_directory.'storage/user_'.$user_name.'/attachments/';
	else
		$filepath = $root_directory.'test/upload/';

	$log->debug("Exiting getFilePath method ...");
	return $filepath;
}

/**
 * This function is used to get the all the modules that have Quick Create Feature.
 * Returns Tab Name and Tablabel.
 */

function getQuickCreateModules()
{
	global $log;
	$log->debug("Entering getQuickCreateModules() method ...");
         global $adb;
         global $mod_strings;


	$new_label=Array('Leads'=>'LNK_NEW_LEAD',
			 'Accounts'=>'LNK_NEW_ACCOUNT',
			 'Activities'=>'LNK_NEW_TASK',
			 'Campaigns'=>'LNK_NEW_CAMPAIGN',
			 'Emails'=>'LNK_NEW_EMAIL',
			 'Events'=>'LNK_NEW_EVENT',
			 'HelpDesk'=>'LNK_NEW_HDESK',
			 'Notes'=>'LNK_NEW_NOTE',
			 'Potentials'=>'LNK_NEW_OPPORTUNITY',
			 'PriceBooks'=>'LNK_NEW_PRICEBOOK',
			 'Products'=>'LNK_NEW_PRODUCT',
			 'Contacts'=>'LNK_NEW_CONTACT',
			 'Vendors'=>'LNK_NEW_VENDOR'); 	

$qc_query = "select distinct tablabel,tab.name from field inner join tab on tab.tabid = field.tabid where quickcreate=0 order by tab.tablabel";
$result = $adb->query($qc_query);
$noofrows = $adb->num_rows($result);
$qcmodule_array = Array();
for($i = 0; $i < $noofrows; $i++)
{
         $tablabel = $adb->query_result($result,$i,'tablabel');

         $tabname = $adb->query_result($result,$i,'name');
	 $tablabel = $new_label[$tabname];
	 if(isPermitted($tabname,'EditView','') == 'yes')
	 {
         	$return_qcmodule[] = $tablabel;
	        $return_qcmodule[] = $tabname;
	}	
}
        $return_qcmodule = array_chunk($return_qcmodule,2);
	$log->debug("Exiting getQuickCreateModules method ...");
        return $return_qcmodule;
}
																					   
/**
 * This function is used to get the Quick create form field parameters for a given module.
 * Param $module - module name 
 * returns the value in array format
 */


function QuickCreate($module)
{
	global $log;
	$log->debug("Entering QuickCreate(".$module.") method ...");
    global $adb;
    global $current_user;
    global $mod_strings;

$tabid = getTabid($module);

//Adding Security Check
require('user_privileges/user_privileges_'.$current_user->id.'.php');
           if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
           {
                 $quickcreate_query = "select * from field where quickcreate=0 and tabid = ".$tabid." order by quickcreatesequence";
           }
           else
           {
                 $profileList = getCurrentUserProfileList();
                 $quickcreate_query = "select field.* from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid where field.tabid=".$tabid." and quickcreate=0 and profile2field.visible=0 and def_org_field.visible=0  and profile2field.profileid in ".$profileList." order by quickcreatesequence";
           }
																					     
$category = getParentTab();
$result = $adb->query($quickcreate_query);
$noofrows = $adb->num_rows($result);
$fieldName_array = Array();
for($i=0; $i<$noofrows; $i++)
{
      $fieldtablename = $adb->query_result($result,$i,'tablename');
      $uitype = $adb->query_result($result,$i,"uitype");
      $fieldname = $adb->query_result($result,$i,"fieldname");
      $fieldlabel = $adb->query_result($result,$i,"fieldlabel");
      $maxlength = $adb->query_result($result,$i,"maximumlength");
      $generatedtype = $adb->query_result($result,$i,"generatedtype");
      $typeofdata = $adb->query_result($result,$i,"typeofdata");

      //to get validationdata
      $fldLabel_array = Array();
      $fldLabel_array[$fieldlabel] = $typeofdata;
      $fieldName_array[$fieldname] = $fldLabel_array;
      $custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module);
      $qcreate_arr[]=$custfld;
}
for ($i=0,$j=0;$i<count($qcreate_arr);$i=$i+2,$j++)
{
       $key1=$qcreate_arr[$i];
       if(is_array($qcreate_arr[$i+1]))
       {
               $key2=$qcreate_arr[$i+1];
       }
       else
       {
                $key2 =array();
       }
                $return_data[$j]=array(0 => $key1,1 => $key2);
}
	$form_data['form'] = $return_data;
	$form_data['data'] = $fieldName_array;
	$log->debug("Exiting QuickCreate method ...");
	return $form_data;
}

/**	Function to send the Notification mail to the assigned to owner about the entity creation or updation
  *	@param string $module -- module name
  *	@param object $focus  -- reference of the object
**/
function sendNotificationToOwner($module,$focus)
{
	global $log;
	$log->debug("Entering sendNotificationToOwner(".$module.",".$focus.") method ...");
	require_once("modules/Emails/mail.php");
	global $current_user;

	$ownername = getUserName($focus->column_fields['assigned_user_id']);
	$ownermailid = getUserEmailId('id',$focus->column_fields['assigned_user_id']);

	if($module == 'Contacts')
	{
		$objectname = $focus->column_fields['lastname'].' '.$focus->column_fields['firstname'];
		$mod_name = 'Contact';
		$object_column_fields = array(
						'lastname'=>'Last Name',
						'firstname'=>'First Name',
						'leadsource'=>'Lead Source',
						'department'=>'Department',
						'description'=>'Description',
					     );
	}
	if($module == 'Accounts')
	{
		$objectname = $focus->column_fields['accountname'];
		$mod_name = 'Account';
		$object_column_fields = array(
						'accountname'=>'Account Name',
						'rating'=>'Rating',
						'industry'=>'Industry',
						'accounttype'=>'Account Type',
						'description'=>'Description',
					     );
	}
	if($module == 'Potentials')
	{
		$objectname = $focus->column_fields['potentialname'];
		$mod_name = 'Potential';
		$object_column_fields = array(
						'potentialname'=>'Potential Name',
						'amount'=>'Amount',
						'closingdate'=>'Expected Close Date',
						'opportunity_type'=>'Opportunity Type',
						'description'=>'Description',
			      		     );
	}	
	
	$description = 'Dear '.$ownername.',<br><br>';

	if($focus->mode == 'edit')
	{
		$subject = 'Regarding '.$mod_name.' updation - '.$objectname;
		$description .= 'The '.$mod_name.' has been updated.';
	}
	else
	{
		$subject = 'Regarding '.$mod_name.' assignment - '.$objectname;
		$description .= 'The '.$mod_name.' has been assigned to you.';
	}
	$description .= 'The '.$mod_name.' details are:<br><br>';
	$description .= $mod_name.' Id : '.$focus->id.'<br>';

	foreach($object_column_fields as $fieldname => $fieldlabel)
	{
		$description .= $fieldlabel.' : <b>'.$focus->column_fields[$fieldname].'</b><br>';
	}

	$description .= '<br><br>Thanks <br>'.$current_user->user_name;
	$status = send_mail($module,$ownermailid,$current_user->user_name,'',$subject,$description);

	$log->debug("Exiting sendNotificationToOwner method ...");
	return $status;
}
function getUserslist()
{
	global $log;
	$log->debug("Entering getUserslist() method ...");
	global $adb;
	$result=$adb->query("select * from users");
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
	       $useridlist[$i]=$adb->query_result($result,$i,'id');
	       $usernamelist[$useridlist[$i]]=$adb->query_result($result,$i,'user_name');
	}
	$change_owner = get_select_options_with_id($usernamelist,'admin');
	$log->debug("Exiting getUserslist method ...");
	return $change_owner;
}


/**
  *	Function to Check for Security whether the Buttons are permitted in List/Edit/Detail View of all Modules
  *	@param string $module -- module name
  *	Returns an array with permission as Yes or No
**/
function Button_Check($module)
{
	global $log;
	$log->debug("Entering Button_Check(".$module.") method ...");
        $permit_arr = array ('EditView' => '',
                             'index' => '',
                             'Import' => '',
                             'Export' => '' );

          foreach($permit_arr as $action => $perr)
          {
                 $tempPer=isPermitted($module,$action,'');
                 $permit_arr[$action] = $tempPer;
          }

	$log->debug("Exiting Button_Check method ...");
	  return $permit_arr;

}

/**
  *	Function to Check whether the User is allowed to delete a particular record from listview of each module using   
  *	mass delete button.
  *	@param string $module -- module name
  *	@param array $ids_list -- Record id 
  *	Returns the Record Names of each module that is not permitted to delete
**/
function getEntityName($module, $ids_list)
{
	$list = implode(",",$ids_list);
	global $adb;
	global $log;
	$log->debug("Entering getEntityName(".$module.") method ...");
		
	switch ($module)
	{
		case "Accounts" : $query = "select accountname from account where accountid in (".$list.")";
				  $result = $adb->query($query);
				  $numrows = $adb->num_rows($result);
				  $account_name = array();	
				  	for ($i = 0; $i < $numrows; $i++)
				  	{
				 		$acc_id = $ids_list[$i];
						$account_name[$acc_id] = $adb->query_result($result,$i,'accountname');
				  	}
					return $account_name;
					break;

		  case "Leads" :  $query = "select concat(firstname,' ',lastname) as leadname from leaddetails where leadid in (".$list.")";
				  $result = $adb->query($query);
				  $numrows = $adb->num_rows($result);
				  $lead_name = array();
					for($i = 0; $i < $numrows; $i++)
					{
						$lead_id = $ids_list[$i];
						$lead_name[$lead_id] = $adb->query_result($result,$i,'leadname');
					}	
								
					return $lead_name;
					break;
		
	       case "Contacts" : $query = "select concat(firstname,' ',lastname) as contactname from contactdetails where contactid in (".$list.")"; 
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $contact_name = array();
					for($i=0; $i < $numrows; $i++)
					{
						$cont_id = $ids_list[$i];
						$contact_name[$cont_id] = $adb->query_result($result,$i,'contactname');
					}
					
					return $contact_name;
					break;

	    case "Potentials"  : $query = "select potentialname from potential where potentialid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $potential_name = array();
					for($i=0; $i < $numrows; $i++)
					{
						$pot_id = $ids_list[$i];
						$potential_name[$pot_id] = $adb->query_result($result,$i,'potentialname');
					}
					
					return $potential_name;
					break;

	        case "Quotes"  : $query = "select subject from quotes where quoteid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $quote_subject = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$quote_id = $ids_list[$i];
						$quote_subject[$quote_id] = $adb->query_result($result,$i,'subject'); 
				 	}
					
					return $quote_subject;
					break;	

	    case "SalesOrder"  : $query = "select subject from salesorder where salesorderid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $so_subject = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$so_id = $ids_list[$i];
						$so_subject[$so_id] = $adb->query_result($result,$i,'subject'); 
				 	}
					
					return $so_subject;
					break;
	
	       case "Invoice"  : $query = "select subject from invoice where invoiceid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $inv_subject = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$inv_id = $ids_list[$i];
						$inv_subject[$inv_id] = $adb->query_result($result,$i,'subject'); 
				 	}
					
					return $inv_subject;
					break;
		
	      case "Products"  : $query = "select productname from products where productid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $product_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$prod_id = $ids_list[$i];
						$product_name[$prod_id] = $adb->query_result($result,$i,'productname'); 
				 	}
					
					return $product_name;
					break;

	   case "PriceBooks"  :  $query = "select bookname from pricebook where pricebookid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $pbook_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$pbook_id = $ids_list[$i];
						$pbook_name[$pbook_id] = $adb->query_result($result,$i,'bookname'); 
				 	}
					
					return $pbook_name;
					break;

	        case "Notes"  :  $query = "select title from notes where notesid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $notes_title = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$note_id = $ids_list[$i];
						$notes_title[$note_id] = $adb->query_result($result,$i,'title'); 
				 	}
					
					return $notes_title;
					break;
		
	  case "Activities"  :  $query = "select subject from activity where activityid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $activity_subject = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$act_id = $ids_list[$i];
						$activity_subject[$act_id] = $adb->query_result($result,$i,'subject'); 
				 	}
					
					return $activity_subject;
					break;

	    case "Campaigns"  :  $query = "select campaignname from campaign where campaignid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $campaign_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$cmpn_id = $ids_list[$i];
						$campaign_name[$cmpn_id] = $adb->query_result($result,$i,'campaignname'); 
				 	}
					
					return $campaign_name;
					break;

	          case "Faq"  :  $query = "select question from faq where id in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $faq_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$faq_id = $ids_list[$i];
						$faq_name[$faq_id] = $adb->query_result($result,$i,'question'); 
				 	}
					
					return $faq_name;
					break;
		
	      case "Vendors"  :  $query = "select vendorname from vendor where vendorid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $vendor_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$ven_id = $ids_list[$i];
						$vendor_name[$ven_id] = $adb->query_result($result,$i,'vendorname'); 
				 	}
					
					return $vendor_name;
					break;

	case "PurchaseOrder"  :  $query = "select subject from purchaseorder where purchaseorderid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $po_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$po_id = $ids_list[$i];
						$po_name[$po_id] = $adb->query_result($result,$i,'subject'); 
				 	}
					
					return $po_name;
					break;

	     case "HelpDesk"  :  $query = "select title from troubletickets where ticketid in (".$list.")";
				 $result = $adb->query($query);
				 $numrows = $adb->num_rows($result);
				 $ticket_name = array();		    	
					for($i=0; $i < $numrows; $i++)
					{
						$tick_id = $ids_list[$i];
						$ticket_name[$tick_id] = $adb->query_result($result,$i,'title'); 
				 	}
					
					return $ticket_name;
					break;
	}
	$log->debug("Exiting getEntityName method ...");
}


function getAllParenttabmoduleslist()
{
        global $adb;
	global $current_user;
        $resultant_array = Array();
        $query = 'select name,tablabel,parenttab_label,tab.tabid from parenttabrel inner join tab on parenttabrel.tabid = tab.tabid inner join parenttab on parenttabrel.parenttabid = parenttab.parenttabid order by parenttab.sequence';
        $result = $adb->query($query);
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
        for($i=0;$i<$adb->num_rows($result);$i++)
        {
                $parenttabname = $adb->query_result($result,$i,'parenttab_label');
                $modulename = $adb->query_result($result,$i,'name');
                $tablabel = $adb->query_result($result,$i,'tablabel');
		$tabid = $adb->query_result($result,$i,'tabid');
		if($is_admin)
		{
			$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
		}	
		elseif($profileGlobalPermission[2]==0 || $profileGlobalPermission[1]==0 || $profileTabsPermission[$tabid]==0)		     {
                	$resultant_array[$parenttabname][] = Array($modulename,$tablabel);
		}
        }
	
	if($is_admin)
	{
               	$resultant_array['Settings'][] = Array('Settings','Settings');
	}			

	        return $resultant_array;
}


?>
