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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/utils/EditViewUtils.php,v 1.188 2005/04/29 05:5 * 4:39 rank Exp  
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new

/** This function returns the vtiger_field details for a given vtiger_fieldname.
  * Param $uitype - UI type of the vtiger_field
  * Param $fieldname - Form vtiger_field name
  * Param $fieldlabel - Form vtiger_field label name
  * Param $maxlength - maximum length of the vtiger_field
  * Param $col_fields - array contains the vtiger_fieldname and values
  * Param $generatedtype - Field generated type (default is 1)
  * Param $module_name - module name
  * Return type is an array
  */

function getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module_name)
{
	global $log;
	$log->debug("Entering getOutputHtml(".$uitype.",". $fieldname.",". $fieldlabel.",". $maxlength.",". $col_fields.",".$generatedtype.",".$module_name.") method ...");
	global $adb,$log;
	global $theme;
	global $mod_strings;
	global $app_strings;
	global $current_user;
	global $noof_group_rows;

	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$fieldlabel = from_html($fieldlabel);
	$fieldvalue = Array();
	$final_arr = Array();
	$value = $col_fields[$fieldname];
	$custfld = '';
	$ui_type[]= $uitype;
	$editview_fldname[] = $fieldname;

	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	$currency= $rate_symbol['symbol'];

	if($generatedtype == 2)
		$mod_strings[$fieldlabel] = $fieldlabel;

	if($uitype == 5 || $uitype == 6 || $uitype ==23)
	{	
		$log->info("uitype is ".$uitype);
		if($value=='')
		{
			if($fieldname != 'birthday')// && $fieldname != 'due_date')//due date is today's date by default
				$disp_value=getNewDisplayDate();

			//Added to display the Contact - Support End Date as one year future instead of today's date -- 30-11-2005
			if($fieldname == 'support_end_date' && $_REQUEST['module'] == 'Contacts')
			{
				$addyear = strtotime("+1 year");
				global $current_user;
				$dat_fmt = (($current_user->date_format == '')?('dd-mm-yyyy'):($current_user->date_format));

				$disp_value = (($dat_fmt == 'dd-mm-yyyy')?(date('d-m-Y',$addyear)):(($dat_fmt == 'mm-dd-yyyy')?(date('m-d-Y',$addyear)):(($dat_fmt == 'yyyy-mm-dd')?(date('Y-m-d', $addyear)):(''))));
			}
		}
		else
		{
			$disp_value = getDisplayDate($value);
		}
		$editview_label[]=$mod_strings[$fieldlabel];
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		if($uitype == 6)
		{
			if($col_fields['time_start']!='')
			{
				$curr_time = $col_fields['time_start'];
			}
			else
			{
				$curr_time = date('H:i');
			}
		}
		$fieldvalue[] = array($disp_value => $curr_time) ;
		if($uitype == 5 || $uitype == 23)
		{
			$fieldvalue[] = array($date_format=>$current_user->date_format);
		}
		else
		{
			$fieldvalue[] = array($date_format=>$current_user->date_format.' '.$app_strings['YEAR_MONTH_DATE']);
		}
	}
	elseif($uitype == 15 || $uitype == 16)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$pick_query="select * from vtiger_".$fieldname;
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,strtolower($fieldname));

			if($value == $pickListValue)
			{
				$chk_val = "selected";	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$options[] = array($pickListValue=>$chk_val );	
		}
		$fieldvalue [] = $options;
	}
	elseif($uitype == 17)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue [] = $value;
	}
	elseif($uitype == 33)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$mulsel="select * from vtiger_".$fieldname;
		$multiselect_result = $adb->query($mulsel);
		$noofoptions = $adb->num_rows($multiselect_result);
		$options = array();
		$found = false;
		$valur_arr = explode(' , ',$value);
		for($j = 0; $j < $noofoptions; $j++)
		{
			$multiselect_combo = $adb->query_result($multiselect_result,$j,strtolower($fieldname));
			if(in_array($multiselect_combo,$valur_arr))
			{
				$chk_val = "selected";
				$found = true;
			}
			else
			{
				$chk_val = '';
			}
			$options[] = array($multiselect_combo=>$chk_val );
		}
		$fieldvalue [] = $options;
	}
	elseif($uitype == 19 || $uitype == 20)
	{
		if(isset($_REQUEST['body']))
		{
			$value = ($_REQUEST['body']);
		}

		if($fieldname == 'terms_conditions')//for default Terms & Conditions
		{
			$value=getTermsandConditions();
		}

		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue [] = $value;
	}
	elseif($uitype == 21 || $uitype == 24)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue [] = $value;
	}
	elseif($uitype == 22)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $value;
	}
	elseif($uitype == 52 || $uitype == 77)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		global $current_user;
		if($value != '')
		{
			$assigned_user_id = $value;	
		}
		else
		{
			$assigned_user_id = $current_user->id;
		}
		if($uitype == 52)
		{
			$combo_lbl_name = 'assigned_user_id';
		}
		elseif($uitype == 77)
		{
			$combo_lbl_name = 'assigned_user_id1';
		}


		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
		}
		else
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
		}
		$fieldvalue [] = $users_combo;
	}
	elseif($uitype == 53)     
	{  
		$editview_label[]=$mod_strings[$fieldlabel];
		//Security Checks
		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$result=get_current_user_access_groups($module_name);
		}
		else
		{ 		
			$result = get_group_options();
		}
		$nameArray = $adb->fetch_array($result);


		global $current_user;
		if($value != '' && $value != 0)
		{
			$assigned_user_id = $value;
			$user_checked = "checked";
			$team_checked = '';
			$user_style='display:block';
			$team_style='display:none';			
		}
		else
		{
			if($value=='0')
			{
				$record = $col_fields["record_id"];
				$module = $col_fields["record_module"];

				$selected_groupname = getGroupName($record, $module);
				$user_checked = '';
				$team_checked = 'checked';
				$user_style='display:none';
				$team_style='display:block';
			}
			else	
			{				
				$assigned_user_id = $current_user->id;
				$user_checked = "checked";
				$team_checked = '';
				$user_style='display:block';
				$team_style='display:none';
			}	
		}
		
		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
		}
		else
		{
			$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
		}


		$GROUP_SELECT_OPTION = '<td width=30%><input type="radio"
			name="assigntype" value="U" '.$user_checked.'
			onclick="toggleAssignType(this.value)">'.$app_strings['LBL_USER'];

		if($noof_group_rows!=0)
		{

			$log->debug("Has a Group, get the Radio button");
			$GROUP_SELECT_OPTION .= '<input
				type="radio" name="assigntype" value="T"'.$team_checked.'
				onclick="toggleAssignType(this.value)">'.$app_strings['LBL_GROUP'];
		}

		$GROUP_SELECT_OPTION .='<br><span
			id="assign_user" style="'.$user_style.'"><select name="assigned_user_id">';

		$GROUP_SELECT_OPTION .= $users_combo;

		$GROUP_SELECT_OPTION .= '</select></span>';

		if($noof_group_rows!=0)
		{
			$log->debug("Has a Group, getting the group names ");
			$GROUP_SELECT_OPTION .='<span id="assign_team" style="'.$team_style.'"><select name="assigned_group_name">';
			
			do
			{
				$groupname=$nameArray["groupname"];
				$selected = '';	
				if($groupname == $selected_groupname[0])
				{
					$selected = "selected";
				}	
				$group_option[] = array($groupname=>$selected);

			}while($nameArray = $adb->fetch_array($result));

		}

		$fieldvalue[]=$users_combo;  
		$fieldvalue[] = $group_option;
	}
	elseif($uitype == 51 || $uitype == 50 || $uitype == 73)
	{
		if($_REQUEST['convertmode'] != 'update_quote_val' && $_REQUEST['convertmode'] != 'update_so_val')
		{
			if(isset($_REQUEST['account_id']) && $_REQUEST['account_id'] != '')
				$value = $_REQUEST['account_id'];	
		}

		if($value != '')
		{		
			$account_name = getAccountName($value);	
		}
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[]=$account_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 54)
	{
		$options =Array();
		$editview_label[]=$mod_strings[$fieldlabel];
		$pick_query="select * from vtiger_groups";
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,"name");

			if($value == $pickListValue)
			{
				$chk_val = "selected";	
			}
			else
			{	
				$chk_val = '';	
			}
			$options[] = array($pickListValue => $chk_val );
		}
		$fieldvalue[] = $options;

	}
	elseif($uitype == 55)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$options = Array();
		$pick_query="select * from vtiger_salutationtype order by sortorderid";
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);
		$salt_value = $col_fields["salutationtype"];
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,"salutationtype");

			if($salt_value == $pickListValue)
			{
				$chk_val = "selected";	
			}
			else
			{	
				$chk_val = '';	
			}
			$options[] = array($pickListValue => $chk_val );
		}
		$fieldvalue[] = $options;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 59)
	{
		if($_REQUEST['module'] == 'HelpDesk')
		{
			if(isset($_REQUEST['product_id']) & $_REQUEST['product_id'] != '')
				$value = $_REQUEST['product_id'];
		}
		elseif(isset($_REQUEST['parent_id']) & $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];

		if($value != '')
		{		
			$product_name = getProductName($value);	
		}
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[]=$product_name;
		$fieldvalue[]=$value;
	}
	elseif($uitype == 63)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		if($value=='')
			$value=1;
		$options = Array();
		$pick_query="select * from vtiger_duration_minutes order by sortorderid";
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);
		$salt_value = $col_fields["duration_minutes"];
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,"duration_minutes");

			if($salt_value == $pickListValue)
			{
				$chk_val = "selected";
			}
			else
			{
				$chk_val = '';
			}
			$options[$pickListValue] = $chk_val;
		}
		$fieldvalue[]=$value;
		$fieldvalue[]=$options;
	}
	elseif($uitype == 64)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
		$fieldvalue[] = $value;
	}
	elseif($uitype == 156)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $value;	
		$fieldvalue[] = $is_admin;
	}
	elseif($uitype == 56)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $value;	
	}
	elseif($uitype == 57)
	{

		if($value != '')
		{
			$contact_name = getContactName($value);
		}
		elseif(isset($_REQUEST['contact_id']) && $_REQUEST['contact_id'] != '')
		{
			if($_REQUEST['module'] == 'Contacts' && $fieldname = 'contact_id')
			{
				$contact_name = '';	
			}
			else
			{
				$value = $_REQUEST['contact_id'];
				$contact_name = getContactName($value);		
			}

		}

		//Checking for contacts duplicate

		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $contact_name;
		$fieldvalue[] = $value;
	}

	elseif($uitype == 58)
	{

		if($value != '')
		{
			$campaign_name = getCampaignName($value);
		}
		elseif(isset($_REQUEST['campaignid']) && $_REQUEST['campaignid'] != '')
		{
			if($_REQUEST['module'] == 'Campaigns' && $fieldname = 'campaignid')
			{
				$campaign_name = '';
			}
			else
			{
				$value = $_REQUEST['campaignid'];
				$campaign_name = getCampaignName($value);
			}

		}
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[]=$campaign_name;
		$fieldvalue[] = $value;
	}
	
	elseif($uitype == 61)
	{
		global $current_user;
		if($value != '')
		{
			$assigned_user_id = $value;
		}
		else
		{
			$assigned_user_id = $current_user->id;
		}
		if($module_name == 'Emails')
		{
			$attach_result = $adb->query("select * from vtiger_seattachmentsrel where crmid = ".$col_fields['record_id']);
			for($ii=0;$ii < $adb->num_rows($attach_result);$ii++)
			{
				$attachmentid = $adb->query_result($attach_result,$ii,'attachmentsid');
				if($attachmentid != '')
				{
					$attachquery = "select * from vtiger_attachments where attachmentsid=".$attachmentid;
					$attachmentsname = $adb->query_result($adb->query($attachquery),0,'name');
					if($attachmentsname != '')	
						$fieldvalue[$attachmentid] = '[ '.$attachmentsname.' ]';
				}

			}
		}else
		{
			if($col_fields['record_id'] != '')
			{
				$attachmentid=$adb->query_result($adb->query("select * from vtiger_seattachmentsrel where crmid = ".$col_fields['record_id']),0,'attachmentsid');
				if($col_fields[$fieldname] == '' && $attachmentid != '')
				{
					$attachquery = "select * from vtiger_attachments where attachmentsid=".$attachmentid;
					$value = $adb->query_result($adb->query($attachquery),0,'name');
				}
			}
			if($value!='')
				$filename=' [ '.$value. ' ]';
			$fieldvalue[] = $filename;
			$fieldvalue[] = $value;
		}
		$editview_label[]=$mod_strings[$fieldlabel];
	}
	elseif($uitype == 69)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		//This query is for Products only
		if($module_name == 'Products')
		{
			$query = 'select vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name from vtiger_products left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_products.productid inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid where productid='.$col_fields['record_id'];
		}
		else
		{
			$query = "select vtiger_attachments.path, vtiger_attachments.attachmentsid, vtiger_attachments.name from vtiger_contactdetails left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid=vtiger_contactdetails.contactid inner join vtiger_attachments on vtiger_attachments.attachmentsid=vtiger_seattachmentsrel.attachmentsid where contactid=".$col_fields['record_id'];
		}
		$result_image = $adb->query($query);
		for($image_iter=0;$image_iter < $adb->num_rows($result_image);$image_iter++)	
		{
			$image_id_array[] = $adb->query_result($result_image,$image_iter,'attachmentsid');
			$image_array[] = $adb->query_result($result_image,$image_iter,'name');
			$image_path_array[] = $adb->query_result($result_image,$image_iter,'path');	
		}
		if(is_array($image_array))
			for($img_itr=0;$img_itr<count($image_array);$img_itr++)
			{
				$fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr].$image_id_array[$img_itr]."_");
			}
		else
			$fieldvalue[] = '';
	}
	elseif($uitype == 62)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];

		if($value != '')
		{
			$parent_module = getSalesEntityType($value);
			if($parent_module == "Leads")
			{
				$sql = "select * from vtiger_leaddetails where leadid=".$value;
				$result = $adb->query($sql);
				$first_name = $adb->query_result($result,0,"firstname");
				$last_name = $adb->query_result($result,0,"lastname");
				$parent_name = $last_name.' '.$first_name;
				$lead_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
			elseif($parent_module == "Potentials")
			{
				$sql = "select * from  vtiger_potential where potentialid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"potentialname");
				$potential_selected = "selected";

			}
			elseif($parent_module == "Products")
			{
				$sql = "select * from  vtiger_products where productid=".$value;
				$result = $adb->query($sql);
				$parent_name= $adb->query_result($result,0,"productname");
				$product_selected = "selected";

			}
			elseif($parent_module == "PurchaseOrder")
			{
				$sql = "select * from  vtiger_purchaseorder where purchaseorderid=".$value;
				$result = $adb->query($sql);
				$parent_name= $adb->query_result($result,0,"subject");
				$porder_selected = "selected";

			}
			elseif($parent_module == "SalesOrder")
			{
				$sql = "select * from  vtiger_salesorder where salesorderid=".$value;
				$result = $adb->query($sql);
				$parent_name= $adb->query_result($result,0,"subject");
				$sorder_selected = "selected";

			}
			elseif($parent_module == "Invoice")
			{
				$sql = "select * from  vtiger_invoice where invoiceid=".$value;
				$result = $adb->query($sql);
				$parent_name= $adb->query_result($result,0,"subject");
				$invoice_selected = "selected";

			}


		}
		$editview_label[] = array($app_strings['COMBO_LEADS'],
                                          $app_strings['COMBO_ACCOUNTS'],
                                          $app_strings['COMBO_POTENTIALS'],
                                          $app_strings['COMBO_PRODUCTS'],
                                          $app_strings['COMBO_INVOICES'],
                                          $app_strings['COMBO_PORDER'],
                                          $app_strings['COMBO_SORDER']
                                         );
                $editview_label[] = array($lead_selected,
                                          $account_selected,
					  $potential_selected,
                                          $product_selected,
                                          $invoice_selected,
                                          $porder_selected,
                                          $sorder_selected
                                         );
                $editview_label[] = array("Leads&action=Popup","Accounts&action=Popup","Potentials&action=Popup","Products&action=Popup","Invoice&action=Popup","PurchaseOrder&action=Popup","SalesOrder&action=Popup");
		$fieldvalue[] =$parent_name;
		$fieldvalue[] =$value;

	}
	elseif($uitype == 66)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];
		// Check for vtiger_activity type if task orders to be added in select option
		$act_mode = $_REQUEST['activity_mode'];

		if($value != '')
		{
			$parent_module = getSalesEntityType($value);
			if($parent_module == "Leads")
			{
				$sql = "select * from vtiger_leaddetails where leadid=".$value;
				$result = $adb->query($sql);
				$first_name = $adb->query_result($result,0,"firstname");
				$last_name = $adb->query_result($result,0,"lastname");
				$parent_name = $last_name.' '.$first_name;
				$lead_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
			elseif($parent_module == "Potentials")
			{
				$sql = "select * from  vtiger_potential where potentialid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"potentialname");
				$potential_selected = "selected";

			}
			elseif($parent_module == "Quotes")
			{
				$sql = "select * from  vtiger_quotes where quoteid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"subject");
				$quote_selected = "selected";

			}
			elseif($act_mode == "Task")
			{
				if($parent_module == "PurchaseOrder")
				{
					$sql = "select * from vtiger_purchaseorder where purchaseorderid=".$value;
					$result = $adb->query($sql);
					$parent_name = $adb->query_result($result,0,"subject");
					$purchase_selected = "selected";
				}
				if($parent_module == "SalesOrder")
				{
					$sql = "select * from vtiger_salesorder where salesorderid=".$value;
					$result = $adb->query($sql);
					$parent_name = $adb->query_result($result,0,"subject");
					$sales_selected = "selected";
				}
				if($parent_module == "Invoice")
				{
					$sql = "select * from vtiger_invoice where invoiceid=".$value;
					$result = $adb->query($sql);
					$parent_name = $adb->query_result($result,0,"subject");
					$invoice_selected = "selected";
				}
				if($parent_module == "Campaigns")
				{
					$sql = "select campaignname from vtiger_campaign where campaignid=".$value;
					$result = $adb->query($sql);
					$parent_name = $adb->query_result($result,0,"campaignname");
					$campaign_selected = "selected";
				}
			}

		}
		if($act_mode == "Task")
                {
                        $editview_label[] = array($app_strings['COMBO_LEADS'],
                                $app_strings['COMBO_ACCOUNTS'],
                                $app_strings['COMBO_POTENTIALS'],
                                $app_strings['COMBO_QUOTES'],
                                $app_strings['COMBO_PORDER'],
                                $app_strings['COMBO_SORDER'],
                                $app_strings['COMBO_INVOICES'],
				$app_strings['COMBO_CAMPAIGNS']
                                        );
			$editview_label[] = array($lead_selected,
                                $account_selected,
                                $potential_selected,
                                $quote_selected,
                                $purchase_selected,
                                $sales_selected,
                                $invoice_selected,
				$campaign_selected
                                        );
                        $editview_label[] = array("Leads&action=Popup","Accounts&action=Popup","Potentials&action=Popup","Quotes&action=Popup","PurchaseOrder&action=Popup","SalesOrder&action=Popup","Invoice&action=Popup","Campaigns&action=Popup");
                }
                else
                {
                        $editview_label[] = array($app_strings['COMBO_LEADS'],
                                $app_strings['COMBO_ACCOUNTS'],
                                $app_strings['COMBO_POTENTIALS'],
                                );
                        $editview_label[] = array($lead_selected,
                                $account_selected,
                                $potential_selected
                                );
                        $editview_label[] = array("Leads&action=Popup","Accounts&action=Popup","Potentials&action=Popup");

                }
		$fieldvalue[] =$parent_name;
		$fieldvalue[] = $value;
	}
	//added by rdhital/Raju for better email support
	elseif($uitype == 357)
	{
		$contact_selected = 'selected';
		$account_selected = '';
		$lead_selected = '';
		if(isset($_REQUEST['emailids']) && $_REQUEST['emailids'] != '')
		{
			$parent_id = $_REQUEST['emailids'];
			$parent_name='';
			$pmodule=$_REQUEST['pmodule'];

			$myids=explode("|",$parent_id);
			for ($i=0;$i<(count($myids)-1);$i++)
			{
				$realid=explode("@",$myids[$i]);
				$entityid=$realid[0];
				$nemail=count($realid);

				if ($pmodule=='Accounts'){
					require_once('modules/Accounts/Account.php');
					$myfocus = new Account();
					$myfocus->retrieve_entity_info($entityid,"Accounts");
					$fullname=br2nl($myfocus->column_fields['accountname']);
					$account_selected = 'selected';
				}
				elseif ($pmodule=='Contacts'){
					require_once('modules/Contacts/Contact.php');
					$myfocus = new Contact();
					$myfocus->retrieve_entity_info($entityid,"Contacts");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
					$contact_selected = 'selected';
				}
				elseif ($pmodule=='Leads'){
					require_once('modules/Leads/Lead.php');
					$myfocus = new Lead();
					$myfocus->retrieve_entity_info($entityid,"Leads");
					$fname=br2nl($myfocus->column_fields['firstname']);
					$lname=br2nl($myfocus->column_fields['lastname']);
					$fullname=$lname.' '.$fname;
					$lead_selected = 'selected';
				}
				for ($j=1;$j<$nemail;$j++){
					$querystr='select columnname from vtiger_field where fieldid='.$realid[$j].';';
					$result=$adb->query($querystr);
					$temp=$adb->query_result($result,0,'columnname');
					$temp1=br2nl($myfocus->column_fields[$temp]);

					//Modified to display the entities in red which don't have email id
					if(strlen($temp_parent_name) > 150)
					{
						$parent_name .= '<br>';
						$temp_parent_name = '';
					}

					if($temp1 != '')
					{
						$parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
						$temp_parent_name .= $fullname.'&lt;'.$temp1.'&gt;; ';
					}
					else
					{
						$parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
						$temp_parent_name .= "<b style='color:red'>".$fullname.'&lt;'.$temp1.'&gt;; '."</b>";
					}

				}
			}
		}
		else
		{
			$parent_name='';
			$parent_id='';
			$myemailid= $_REQUEST['record'];
			$mysql = "select crmid from vtiger_seactivityrel where activityid=".$myemailid;
			$myresult = $adb->query($mysql);
			$mycount=$adb->num_rows($myresult);
			if($mycount >0)
			{
				for ($i=0;$i<$mycount;$i++)
				{	
					$mycrmid=$adb->query_result($myresult,$i,'crmid');
					$parent_module = getSalesEntityType($mycrmid);
					if($parent_module == "Leads")
					{
						$sql = "select firstname,lastname,email from vtiger_leaddetails where leadid=".$mycrmid;
						$result = $adb->query($sql);
						$first_name = $adb->query_result($result,0,"firstname");
						$last_name = $adb->query_result($result,0,"lastname");
						$myemail=$adb->query_result($result,0,"email");
						$parent_id .=$mycrmid.'@0|' ; //make it such that the email adress sent is remebered and only that one is retrived
						$parent_name .= $last_name.' '.$first_name.'<'.$myemail.'>; ';
						$lead_selected = 'selected';
					}
					elseif($parent_module == "Contacts")
					{
						$sql = "select * from  vtiger_contactdetails where contactid=".$mycrmid;
						$result = $adb->query($sql);
						$first_name = $adb->query_result($result,0,"firstname");
						$last_name = $adb->query_result($result,0,"lastname");
						$myemail=$adb->query_result($result,0,"email");
						$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
						$parent_name .= $last_name.' '.$first_name.'<'.$myemail.'>; ';
						$contact_selected = 'selected';
					}
					elseif($parent_module == "Accounts")
					{
						$sql = "select * from  vtiger_account where accountid=".$mycrmid;
						$result = $adb->query($sql);
						$account_name = $adb->query_result($result,0,"accountname");
						$myemail=$adb->query_result($result,0,"email1");
						$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
						$parent_name .= $account_name.'<'.$myemail.'>; ';
						$account_selected = 'selected';
					}elseif($parent_module == "Users")
					{
						$sql = "select user_name,email1 from vtiger_users where id=".$mycrmid;
						$result = $adb->query($sql);
						$account_name = $adb->query_result($result,0,"user_name");
						$myemail=$adb->query_result($result,0,"email1");
						$parent_id .=$mycrmid.'@0|'  ;//make it such that the email adress sent is remebered and only that one is retrived
						$parent_name .= $account_name.'<'.$myemail.'>; ';
						$user_selected = 'selected';
					}
				}
			}
		}
		$custfld .= '<td width="20%" class="dataLabel">To:&nbsp;</td>';
		$custfld .= '<td width="90%" colspan="3"><input name="parent_id" type="hidden" value="'.$parent_id.'"><textarea readonly name="parent_name" cols="70" rows="2">'.$parent_name.'</textarea>&nbsp;<select name="parent_type" >';
		$custfld .= '<OPTION value="Contacts" selected>'.$app_strings['COMBO_CONTACTS'].'</OPTION>';
		$custfld .= '<OPTION value="Accounts" >'.$app_strings['COMBO_ACCOUNTS'].'</OPTION>';
		$custfld .= '<OPTION value="Leads" >'.$app_strings['COMBO_LEADS'].'</OPTION></select><img src="'.$image_path.'select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick=\'$log->debug("Exiting getOutputHtml method ..."); return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&popuptype=set_$log->debug("Exiting getOutputHtml method ..."); return_emails&form=EmailEditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1,top=150,left=200");\' align="absmiddle" style=\'cursor:hand;cursor:pointer\'>&nbsp;<input type="image" src="'.$image_path.'clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.parent_id.value=\'\';this.form.parent_name.value=\'\';$log->debug("Exiting getOutputHtml method ..."); return false;" align="absmiddle" style=\'cursor:hand;cursor:pointer\'></td>';
		$editview_label[] = array(	 
				$app_strings['COMBO_CONTACTS']=>$contact_selected,
				$app_strings['COMBO_ACCOUNTS']=>$account_selected,
				$app_strings['COMBO_LEADS']=>$lead_selected,
				$app_strings['COMBO_USERS']=>$user_selected
				);
		$fieldvalue[] =$parent_name;
		$fieldvalue[] = $parent_id;

	}
	//end of rdhital/Raju
	elseif($uitype == 68)
	{
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '')
			$value = $_REQUEST['parent_id'];

		if($value != '')
		{
			$parent_module = getSalesEntityType($value);
			if($parent_module == "Contacts")
			{
				$sql = "select * from  vtiger_contactdetails where contactid=".$value;
				$result = $adb->query($sql);
				$first_name = $adb->query_result($result,0,"firstname");
				$last_name = $adb->query_result($result,0,"lastname");
				$parent_name = $last_name.' '.$first_name;
				$contact_selected = "selected";

			}
			elseif($parent_module == "Accounts")
			{
				$sql = "select * from  vtiger_account where accountid=".$value;
				$result = $adb->query($sql);
				$parent_name = $adb->query_result($result,0,"accountname");
				$account_selected = "selected";

			}
		}
		$editview_label[] = array($app_strings['COMBO_CONTACTS'],
                                        $app_strings['COMBO_ACCOUNTS']
                                        );
                $editview_label[] = array($contact_selected,
                                        $account_selected
                                        );
                $editview_label[] = array("Contacts","Accounts");
		$fieldvalue[] = $parent_name;
		$fieldvalue[] = $value;
	}
	
	elseif($uitype == 71 || $uitype == 72)
	{
		$editview_label[]=$mod_strings[$fieldlabel].': ('.$currency.')';
		if($value!='')
		        $fieldvalue[] = convertFromDollar($value,$rate);
		else
		        $fieldvalue[] = $value;
	}
	elseif($uitype == 75 || $uitype ==81)
	{
		if($value != '')
		{
			$vendor_name = getVendorName($value);
		}
		elseif(isset($_REQUEST['vendor_id']) && $_REQUEST['vendor_id'] != '')
		{
			$value = $_REQUEST['vendor_id'];
			$vendor_name = getVendorName($value);
		}		 	
		$pop_type = 'specific';
		if($uitype == 81)
		{
			$pop_type = 'specific_vendor_address';
		}
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $vendor_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 76)
	{
		if($value != '')
		{
			$potential_name = getPotentialName($value);
		}
		elseif(isset($_REQUEST['potential_id']) && $_REQUEST['potential_id'] != '')
		{
			$value = $_REQUEST['potental_id'];
			$potential_name = getPotentialName($value);
		}		 	
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $potential_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 78)
	{
		if($value != '')
		{
			$quote_name = getQuoteName($value);
		}
		elseif(isset($_REQUEST['quote_id']) && $_REQUEST['quote_id'] != '')
		{
			$value = $_REQUEST['quote_id'];
			$potential_name = getQuoteName($value);
		}		 	
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $quote_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 79)
	{
		if($value != '')
		{
			$purchaseorder_name = getPoName($value);
		}
		elseif(isset($_REQUEST['purchaseorder_id']) && $_REQUEST['purchaseorder_id'] != '')
		{
			$value = $_REQUEST['purchaseorder_id'];
			$purchaseorder_name = getPoName($value);
		}		 	
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $purchaseorder_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 80)
	{
		if($value != '')
		{
			$salesorder_name = getSoName($value);
		}
		elseif(isset($_REQUEST['salesorder_id']) && $_REQUEST['salesorder_id'] != '')
		{
			$value = $_REQUEST['salesorder_id'];
			$salesorder_name = getSoName($value);
		}		 	
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[] = $salesorder_name;
		$fieldvalue[] = $value;
	}
	elseif($uitype == 30)
	{
		$rem_days = 0;
		$rem_hrs = 0;
		$rem_min = 0;
		if($value!='')
			$SET_REM = "CHECKED";
		$rem_days = floor($col_fields[$fieldname]/(24*60));
		$rem_hrs = floor(($col_fields[$fieldname]-$rem_days*24*60)/60);
		$rem_min = ($col_fields[$fieldname]-$rem_days*24*60)%60;
		$editview_label[]=$mod_strings[$fieldlabel];
		$day_options = getReminderSelectOption(0,31,'remdays',$rem_days);
		$hr_options = getReminderSelectOption(0,23,'remhrs',$rem_hrs);
		$min_options = getReminderSelectOption(1,59,'remmin',$rem_min);
		$fieldvalue[] = array(array(0,32,'remdays',$mod_strings['LBL_DAYS'],$rem_days),array(0,24,'remhrs',$mod_strings['LBL_HOURS'],$rem_hrs),array(1,60,'remmin',$mod_strings['LBL_MINUTES'].'&nbsp;&nbsp;'.$mod_strings['LBL_BEFORE_EVENT'],$rem_min));
		$fieldvalue[] = array($SET_REM,$mod_strings['LBL_YES'],$mod_strings['LBL_NO']);
		$SET_REM = '';
	}
	elseif($uitype == 115)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$pick_query="select * from vtiger_".$fieldname;
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,strtolower($fieldname));

			if($value == $pickListValue)
			{
				$chk_val = "selected";	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$options[] = array($pickListValue=>$chk_val );	
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = $is_admin;
	}
	elseif($uitype == 116)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$pick_query="select * from vtiger_currency_info";
		$pickListResult = $adb->query($pick_query);
		$noofpickrows = $adb->num_rows($pickListResult);

		//Mikecrowe fix to correctly default for custom pick lists
		$options = array();
		$found = false;
		for($j = 0; $j < $noofpickrows; $j++)
		{
			$pickListValue=$adb->query_result($pickListResult,$j,'currency_name');
			$currency_id=$adb->query_result($pickListResult,$j,'id');
			if($value == $currency_id)
			{
				$chk_val = "selected";	
				$found = true;
			}
			else
			{	
				$chk_val = '';
			}
			$options[$currency_id] = array($pickListValue=>$chk_val );	
		}
		$fieldvalue [] = $options;
		$fieldvalue [] = $is_admin;
	}
	elseif($uitype ==98)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
		$fieldvalue[]=$value;
        	$fieldvalue[]=getRoleName($value);
		$fieldvalue[]=$is_admin;
	}
	elseif($uitype == 105)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
			$query = "select attachments.path, attachments.name from contactdetails left join seattachmentsrel on seattachmentsrel.crmid=contactdetails.contactid inner join attachments on attachments.attachmentsid=seattachmentsrel.attachmentsid where contactdetails.imagename=attachments.name and contactid=".$col_fields['record_id'];
		$result_image = $adb->query($query);
		for($image_iter=0;$image_iter < $adb->num_rows($result_image);$image_iter++)	
		{
			$image_array[] = $adb->query_result($result_image,$image_iter,'name');	
			$image_path_array[] = $adb->query_result($result_image,$image_iter,'path');	
		}
		if(is_array($image_array))
			for($img_itr=0;$img_itr<count($image_array);$img_itr++)
			{
				$fieldvalue[] = array('name'=>$image_array[$img_itr],'path'=>$image_path_array[$img_itr]);
			}
		else
			$fieldvalue[] = '';
	}elseif($uitype == 101)
	{
		$editview_label[]=$mod_strings[$fieldlabel];
        $fieldvalue[] = getUserName($value);
        $fieldvalue[] = $value;
	}
	else
	{
		//Added condition to set the subject if click Reply All from web mail
		if($_REQUEST['module'] == 'Emails' && $_REQUEST['mg_subject'] != '')
		{
			$value = $_REQUEST['mg_subject'];
		}
		$editview_label[]=$mod_strings[$fieldlabel];
		if($uitype == 1 && ($fieldname=='expectedrevenue' || $fieldname=='budgetcost' || $fieldname=='actualcost' || $fieldname=='expectedroi' || $fieldname=='actualroi' ) && ($module_name=='Campaigns'))
		{
			$fieldvalue[] = convertFromDollar($value,$rate);
		}
		else
			$fieldvalue[] = $value;
	}

	// Mike Crowe Mod --------------------------------------------------------force numerics right justified.
	if ( !eregi("id=",$custfld) )
		$custfld = preg_replace("/<input/iS","<input id='$fieldname' ",$custfld);

	if ( in_array($uitype,array(71,72,7,9,90)) )
	{
		$custfld = preg_replace("/<input/iS","<input align=right ",$custfld);
	}
	$final_arr[]=$ui_type;
	$final_arr[]=$editview_label;
	$final_arr[]=$editview_fldname;
	$final_arr[]=$fieldvalue;
	$log->debug("Exiting getOutputHtml method ...");
	return $final_arr;
}

/** This function returns the vtiger_invoice object populated with the details from sales order object.
* Param $focus - Invoice object
* Param $so_focus - Sales order focus
* Param $soid - sales order id
* Return type is an object array
*/

function getConvertSoToInvoice($focus,$so_focus,$soid)
{
	global $log;
	$log->debug("Entering getConvertSoToInvoice(".$focus.",".$so_focus.",".$soid.") method ...");
        $log->info("in getConvertSoToInvoice ".$soid);

	$focus->column_fields['salesorder_id'] = $soid;
	$focus->column_fields['subject'] = $so_focus->column_fields['subject'];
	$focus->column_fields['customerno'] = $so_focus->column_fields['customerno'];
	$focus->column_fields['duedate'] = $so_focus->column_fields['duedate'];
	$focus->column_fields['contact_id'] = $so_focus->column_fields['contact_id'];//to include contact name in Invoice
	$focus->column_fields['account_id'] = $so_focus->column_fields['account_id'];
	$focus->column_fields['exciseduty'] = $so_focus->column_fields['exciseduty'];
	$focus->column_fields['salescommission'] = $so_focus->column_fields['salescommission'];
	$focus->column_fields['purchaseorder'] = $so_focus->column_fields['purchaseorder'];
	$focus->column_fields['bill_street'] = $so_focus->column_fields['bill_street'];
	$focus->column_fields['ship_street'] = $so_focus->column_fields['ship_street'];
	$focus->column_fields['bill_city'] = $so_focus->column_fields['bill_city'];
	$focus->column_fields['ship_city'] = $so_focus->column_fields['ship_city'];
	$focus->column_fields['bill_state'] = $so_focus->column_fields['bill_state'];
	$focus->column_fields['ship_state'] = $so_focus->column_fields['ship_state'];
	$focus->column_fields['bill_code'] = $so_focus->column_fields['bill_code'];
	$focus->column_fields['ship_code'] = $so_focus->column_fields['ship_code'];
	$focus->column_fields['bill_country'] = $so_focus->column_fields['bill_country'];
	$focus->column_fields['ship_country'] = $so_focus->column_fields['ship_country'];
	$focus->column_fields['bill_pobox'] = $so_focus->column_fields['bill_pobox'];
	$focus->column_fields['ship_pobox'] = $so_focus->column_fields['ship_pobox'];
	$focus->column_fields['description'] = $so_focus->column_fields['description'];
	$focus->column_fields['terms_conditions'] = $so_focus->column_fields['terms_conditions'];

	$log->debug("Exiting getConvertSoToInvoice method ...");
	return $focus;

}

/** This function returns the vtiger_invoice object populated with the details from quote object.
* Param $focus - Invoice object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/


function getConvertQuoteToInvoice($focus,$quote_focus,$quoteid)
{
	global $log;
	$log->debug("Entering getConvertQuoteToInvoice(".$focus.",".$quote_focus.",".$quoteid.") method ...");
        $log->info("in getConvertQuoteToInvoice ".$quoteid);

	$focus->column_fields['subject'] = $quote_focus->column_fields['subject'];
	$focus->column_fields['account_id'] = $quote_focus->column_fields['account_id'];
	$focus->column_fields['bill_street'] = $quote_focus->column_fields['bill_street'];
	$focus->column_fields['ship_street'] = $quote_focus->column_fields['ship_street'];
	$focus->column_fields['bill_city'] = $quote_focus->column_fields['bill_city'];
	$focus->column_fields['ship_city'] = $quote_focus->column_fields['ship_city'];
	$focus->column_fields['bill_state'] = $quote_focus->column_fields['bill_state'];
	$focus->column_fields['ship_state'] = $quote_focus->column_fields['ship_state'];
	$focus->column_fields['bill_code'] = $quote_focus->column_fields['bill_code'];
	$focus->column_fields['ship_code'] = $quote_focus->column_fields['ship_code'];
	$focus->column_fields['bill_country'] = $quote_focus->column_fields['bill_country'];
	$focus->column_fields['ship_country'] = $quote_focus->column_fields['ship_country'];
	$focus->column_fields['bill_pobox'] = $quote_focus->column_fields['bill_pobox'];
	$focus->column_fields['ship_pobox'] = $quote_focus->column_fields['ship_pobox'];
	$focus->column_fields['description'] = $quote_focus->column_fields['description'];
	$focus->column_fields['terms_conditions'] = $quote_focus->column_fields['terms_conditions'];

	$log->debug("Exiting getConvertQuoteToInvoice method ...");
	return $focus;

}

/** This function returns the sales order object populated with the details from quote object.
* Param $focus - Sales order object
* Param $quote_focus - Quote order focus
* Param $quoteid - quote id
* Return type is an object array
*/

function getConvertQuoteToSoObject($focus,$quote_focus,$quoteid)
{
	global $log;
	$log->debug("Entering getConvertQuoteToSoObject(".$focus.",".$quote_focus.",".$quoteid.") method ...");
        $log->info("in getConvertQuoteToSoObject ".$quoteid);

        $focus->column_fields['quote_id'] = $quoteid;
        $focus->column_fields['subject'] = $quote_focus->column_fields['subject'];
        $focus->column_fields['contact_id'] = $quote_focus->column_fields['contact_id'];
        $focus->column_fields['potential_id'] = $quote_focus->column_fields['potential_id'];
        $focus->column_fields['account_id'] = $quote_focus->column_fields['account_id'];
        $focus->column_fields['carrier'] = $quote_focus->column_fields['carrier'];
        $focus->column_fields['bill_street'] = $quote_focus->column_fields['bill_street'];
        $focus->column_fields['ship_street'] = $quote_focus->column_fields['ship_street'];
        $focus->column_fields['bill_city'] = $quote_focus->column_fields['bill_city'];
        $focus->column_fields['ship_city'] = $quote_focus->column_fields['ship_city'];
        $focus->column_fields['bill_state'] = $quote_focus->column_fields['bill_state'];
        $focus->column_fields['ship_state'] = $quote_focus->column_fields['ship_state'];
        $focus->column_fields['bill_code'] = $quote_focus->column_fields['bill_code'];
        $focus->column_fields['ship_code'] = $quote_focus->column_fields['ship_code'];
        $focus->column_fields['bill_country'] = $quote_focus->column_fields['bill_country'];
        $focus->column_fields['ship_country'] = $quote_focus->column_fields['ship_country'];
        $focus->column_fields['bill_pobox'] = $quote_focus->column_fields['bill_pobox'];
        $focus->column_fields['ship_pobox'] = $quote_focus->column_fields['ship_pobox'];
		$focus->column_fields['description'] = $quote_focus->column_fields['description'];
        $focus->column_fields['terms_conditions'] = $quote_focus->column_fields['terms_conditions'];

	$log->debug("Exiting getConvertQuoteToSoObject method ...");
        return $focus;

}

/** This function returns the detailed list of vtiger_products associated to a given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/


function getAssociatedProducts($module,$focus,$seid='')
{
	global $log;
	$log->debug("Entering getAssociatedProducts($module,$focus,$seid='') method ...");
	global $adb;
	$output = '';
	global $theme,$current_user;
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	$product_Detail = Array();
	if($module == 'Quotes')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'PurchaseOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'SalesOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'Invoice')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'Potentials')
	{
		$query="select vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_seproductsrel.* from vtiger_products inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid where crmid=".$seid;
	}
	elseif($module == 'Products')
	{
		$query="select vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_crmentity.* from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid where vtiger_crmentity.deleted=0 and productid=".$seid;
	}

	$result = $adb->query($query);
	$num_rows=$adb->num_rows($result);
	for($i=1;$i<=$num_rows;$i++)
	{
		$hdnProductId = $adb->query_result($result,$i-1,'productid');
		$productname=$adb->query_result($result,$i-1,'productname');
		$comment=$adb->query_result($result,$i-1,'comment');
		$qtyinstock=$adb->query_result($result,$i-1,'qtyinstock');
		$qty=$adb->query_result($result,$i-1,'quantity');
		$unitprice=$adb->query_result($result,$i-1,'unit_price');
		$listprice=$adb->query_result($result,$i-1,'listprice');

		if($listprice == '')
			$listprice = $unitprice;
		if($qty =='')
			$qty = 1;

		//calculate productTotal
		$productTotal = $qty*$listprice;

		//Delete link in First column
		if($i != 1)
		{
			$product_Detail[$i]['delRow'.$i]="Del";
		}

		$product_Detail[$i]['hdnProductId'.$i] = $hdnProductId;
		$product_Detail[$i]['productName'.$i]= $productname;
		$product_Detail[$i]['comment'.$i]= $comment;

		if($module != 'PurchaseOrder' && $focus->object_name != 'Order')
		{
			$product_Detail[$i]['qtyInStock'.$i]=$qtyinstock;
		}
		$listprice=convertFromDollar($listprice,$rate);
		$productTotal =convertFromDollar($productTotal,$rate);
		$product_Detail[$i]['qty'.$i]=$qty;
		$product_Detail[$i]['listPrice'.$i]=$listprice;
		$product_Detail[$i]['productTotal'.$i]=$productTotal;

		$discount_percent=$adb->query_result($result,$i-1,'discount_percent');
		$discount_amount=$adb->query_result($result,$i-1,'discount_amount');
		$discountTotal = '0.00';
		//Based on the discount percent or amount we will show the discount details
		if($discount_percent != 'NULL' && $discount_percent != '')
		{
			$product_Detail[$i]['discount_type'.$i] = "percentage";
			$product_Detail[$i]['discount_percent'.$i] = $discount_percent;
			$product_Detail[$i]['checked_discount_percent'.$i] = ' checked';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:hidden"';
			$discountTotal = $productTotal*$discount_percent/100;
		}
		elseif($discount_amount != 'NULL' && $discount_amount != '')
		{
			$product_Detail[$i]['discount_type'.$i] = "amount";
			$product_Detail[$i]['discount_amount'.$i] = $discount_amount;
			$product_Detail[$i]['checked_discount_amount'.$i] = ' checked';
			$product_Detail[$i]['style_discount_amount'.$i] = ' style="visibility:visible"';
			$product_Detail[$i]['style_discount_percent'.$i] = ' style="visibility:hidden"';
			$discountTotal = $discount_amount;
		}
		else
		{
			$product_Detail[$i]['checked_discount_zero'.$i] = ' checked';
		}
		$totalAfterDiscount = $productTotal-$discountTotal;
		$product_Detail[$i]['discountTotal'.$i] = $discountTotal;
		$product_Detail[$i]['totalAfterDiscount'.$i] = $totalAfterDiscount;

		//First we will get all associated taxes as array
		$tax_details = getTaxDetailsForProduct($hdnProductId,'all');
		//Now retrieve the tax values from the current query with the name
		for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
		{
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_value = getInventoryProductTaxValue($focus->id, $hdnProductId, $tax_name);
			$product_Detail[$i]['taxes'][$tax_count]['taxname'] = $tax_name;
			$product_Detail[$i]['taxes'][$tax_count]['percentage'] = $tax_value;
		}

		$taxTotal = '0.00';
		$product_Detail[$i]['taxTotal'.$i] = $taxTotal;

		//Calculate netprice
		$netPrice = $totalAfterDiscount+$taxTotal;
		$taxtype = getInventoryTaxType($module,$focus->id);
		if($taxtype == 'individual')
		{
			//Add the tax with product total and assign to netprice
			$netPrice = $netPrice+$taxTotal;
		}
		$product_Detail[$i]['netPrice'.$i] = $netPrice;
	}

	//set the taxtype
	$product_Detail[1]['final_details']['taxtype'] = $taxtype;

	//Get the Final Discount, S&H charge, Tax for S&H and Adjustment values
	//To set the Final Discount details
	$finalDiscount = '0.00';
	$product_Detail[1]['final_details']['discount_type_final'] = 'zero';

	$subTotal = ($focus->column_fields['hdnSubTotal'] != '')?$focus->column_fields['hdnSubTotal']:'0.00';
	$discountPercent = ($focus->column_fields['hdnDiscountPercent'] != '')?$focus->column_fields['hdnDiscountPercent']:'0.00';
	$discountAmount = ($focus->column_fields['hdnDiscountAmount'] != '')?$focus->column_fields['hdnDiscountAmount']:'0.00';

	if($focus->column_fields['hdnDiscountPercent'] != '')
	{
		$finalDiscount = ($subTotal*$discountPercent/100);
		$product_Detail[1]['final_details']['discount_type_final'] = 'percentage';
		$product_Detail[1]['final_details']['discount_percentage_final'] = $discountPercent;
		$product_Detail[1]['final_details']['checked_discount_percentage_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:hidden"';
	}
	elseif($focus->column_fields['hdnDiscountAmount'] != '')
	{
		$finalDiscount = $focus->column_fields['hdnDiscountAmount'];
		$product_Detail[1]['final_details']['discount_type_final'] = 'amount';
		$product_Detail[1]['final_details']['discount_amount_final'] = $discountAmount;
		$product_Detail[1]['final_details']['checked_discount_amount_final'] = ' checked';
		$product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:visible"';
		$product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:hidden"';
	}
	$product_Detail[1]['final_details']['discountTotal_final'] = $finalDiscount;

	//To set the Final Tax values
	if($taxtype == 'group')
	{
		
		$taxtotal = '0.00';
		//First we should get all available taxes and then retrieve the corresponding tax values
		$tax_details = getAllTaxes('available');
		//if taxtype is group then the tax will be same for all products in vtiger_inventoryproductrel table
		for($tax_count=0;$tax_count<count($tax_details);$tax_count++)
		{
			$tax_name = $tax_details[$tax_count]['taxname'];
			$tax_percent = $adb->query_result($result,0,$tax_name);
			$taxamount = ($focus->column_fields['hdnSubTotal']-$finalDiscount)*$tax_percent/100;
			$taxtotal = $taxtotal + $taxamount;
			$product_Detail[1]['final_details']['taxes'][$tax_count]['taxname'] = $tax_name;
			$product_Detail[1]['final_details']['taxes'][$tax_count]['percentage'] = $tax_percent;
			$product_Detail[1]['final_details']['taxes'][$tax_count]['amount'] = $taxamount;
		}
		$product_Detail[1]['final_details']['tax_totalamount'] = $taxtotal;
	}

	//To set the Shipping & Handling charge
	$shCharge = ($focus->column_fields['hdnS_H_Amount'] != '')?$focus->column_fields['hdnS_H_Amount']:'0.00';
	$product_Detail[1]['final_details']['shipping_handling_charge'] = $shCharge;

	//To set the Shipping & Handling tax values
	//calculate S&H tax
	$shtaxtotal = '0.00';
	//First we should get all available taxes and then retrieve the corresponding tax values
	$shtax_details = getAllTaxes('available','sh');
	//if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
	for($shtax_count=0;$shtax_count<count($shtax_details);$shtax_count++)
	{
		$shtax_name = $shtax_details[$shtax_count]['taxname'];
		$shtax_percent = getInventorySHTaxPercent($focus->id,$shtax_name);
		$shtaxamount = $shCharge*$shtax_percent/100;
		$shtaxtotal = $shtaxtotal + $shtaxamount;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxname'] = $shtax_name;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['percentage'] = $shtax_percent;
		$product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['amount'] = $shtaxamount;
	}
	$product_Detail[1]['final_details']['shtax_totalamount'] = $shtaxtotal;

	//To set the Adjustment value
	$adjustment = ($focus->column_fields['txtAdjustment'] != '')?$focus->column_fields['txtAdjustment']:'0.00';
	$product_Detail[1]['final_details']['adjustment'] = $adjustment;

	//To set the grand total
	$grandTotal = ($focus->column_fields['hdnGrandTotal'] != '')?$focus->column_fields['hdnGrandTotal']:'0.00';
	$product_Detail[1]['final_details']['grandTotal'] = $grandTotal;

	$log->debug("Exiting getAssociatedProducts method ...");

	return $product_Detail;

}

/** This function returns the no of vtiger_products associated to the given entity or a record.
* Param $module - module name
* Param $focus - module object
* Param $seid - sales entity id
* Return type is an object array
*/

function getNoOfAssocProducts($module,$focus,$seid='')
{
	global $log;
	$log->debug("Entering getNoOfAssocProducts($module,$focus,$seid='') method ...");
	global $adb;
	$output = '';
	if($module == 'Quotes')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'PurchaseOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'SalesOrder')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'Invoice')
	{
		$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$focus->id;
	}
	elseif($module == 'Potentials')
	{
		$query="select vtiger_products.productname,vtiger_products.unit_price,vtiger_seproductsrel.* from vtiger_products inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid where crmid=".$seid;
	}	
	elseif($module == 'Products')
	{
		$query="select vtiger_products.productname,vtiger_products.unit_price, vtiger_crmentity.* from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid where vtiger_crmentity.deleted=0 and productid=".$seid;
	}


	$result = $adb->query($query);
	$num_rows=$adb->num_rows($result);
	$log->debug("Exiting getNoOfAssocProducts method ...");
	return $num_rows;
}

/** This function returns the detail block information of a record for given block id.
* Param $module - module name
* Param $block - block name
* Param $mode - view type (detail/edit/create)
* Param $col_fields - vtiger_fields array
* Param $tabid - vtiger_tab id
* Param $info_type - information type (basic/advance) default ""
* Return type is an object array
*/

function getBlockInformation($module, $result, $col_fields,$tabid,$block_label)
{
	global $log;
	$log->debug("Entering getBlockInformation(".$module.",". $result.",". $col_fields.",".$tabid.",".$block_label.") method ...");
	global $adb;
	$editview_arr = Array();

	global $current_user,$mod_strings;
	
	$noofrows = $adb->num_rows($result);
	if (($module == 'Accounts' || $module == 'Contacts' || $module == 'Quotes' || $module == 'PurchaseOrder' || $module == 'SalesOrder'|| $module == 'Invoice') && $block == 2)
	{
		 global $log;
                $log->info("module is ".$module);

			$mvAdd_flag = true;
			$moveAddress = "<td rowspan='6' valign='middle' align='center'><input title='Copy billing address to shipping address'  class='button' onclick='return copyAddressRight(EditView)'  type='button' name='copyright' value='&raquo;' style='padding:0px 2px 0px 2px;font-size:12px'><br><br>
				<input title='Copy shipping address to billing address'  class='button' onclick='return copyAddressLeft(EditView)'  type='button' name='copyleft' value='&laquo;' style='padding:0px 2px 0px 2px;font-size:12px'></td>";
	}
	

	for($i=0; $i<$noofrows; $i++)
	{
		$fieldtablename = $adb->query_result($result,$i,"tablename");	
		$fieldcolname = $adb->query_result($result,$i,"columnname");	
		$uitype = $adb->query_result($result,$i,"uitype");	
		$fieldname = $adb->query_result($result,$i,"fieldname");	
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$block = $adb->query_result($result,$i,"block");
		$maxlength = $adb->query_result($result,$i,"maximumlength");
		$generatedtype = $adb->query_result($result,$i,"generatedtype");				

		$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module);
		$editview_arr[$block][]=$custfld;
		if ($mvAdd_flag == true)
		$mvAdd_flag = false;
		$i++;
		if($i<$noofrows)
		{
			$fieldtablename = $adb->query_result($result,$i,"tablename");	
			$fieldcolname = $adb->query_result($result,$i,"columnname");	
			$uitype = $adb->query_result($result,$i,"uitype");	
			$fieldname = $adb->query_result($result,$i,"fieldname");	
			$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
			$block = $adb->query_result($result,$i,"block");
			$maxlength = $adb->query_result($result,$i,"maximumlength");
			$generatedtype = $adb->query_result($result,$i,"generatedtype");
			$custfld = getOutputHtml($uitype, $fieldname, $fieldlabel, $maxlength, $col_fields,$generatedtype,$module);			
			$editview_arr[$block][]=$custfld;
		}
	}
	foreach($editview_arr as $headerid=>$editview_value)
	{
		$editview_data = Array();
		for ($i=0,$j=0;$i<count($editview_value);$i=$i+2,$j++)
		{
			$key1=$editview_value[$i];
			if(is_array($editview_value[$i+1]))
			{
				$key2=$editview_value[$i+1];
			}
			else
			{
				$key2 =array();
			}
			$editview_data[$j]=array(0 => $key1,1 => $key2);
		}
		$editview_arr[$headerid] = $editview_data;
	}
	foreach($block_label as $blockid=>$label)
	{
		if($label == '')
		{
			$returndata[$mod_strings[$curBlock]]=array_merge((array)$returndata[$mod_strings[$curBlock]],(array)$editview_arr[$blockid]);
		}
		else
		{
			$curBlock = $label;
			if(is_array($editview_arr[$blockid]))
				$returndata[$mod_strings[$label]]=array_merge((array)$returndata[$mod_strings[$label]],(array)$editview_arr[$blockid]);
		}
	}
	$log->debug("Exiting getBlockInformation method ...");
	return $returndata;	
	
}

/** This function returns the data type of the vtiger_fields, with vtiger_field label, which is used for javascript validation.
* Param $validationData - array of vtiger_fieldnames with datatype
* Return type array 
*/


function split_validationdataArray($validationData)
{
	global $log;
	$log->debug("Entering split_validationdataArray(".$validationData.") method ...");
	$fieldName = '';
	$fieldLabel = '';
	$fldDataType = '';
	$rows = count($validationData);
	foreach($validationData as $fldName => $fldLabel_array)
	{
		if($fieldName == '')
		{
			$fieldName="'".$fldName."'";
		}
		else
		{
			$fieldName .= ",'".$fldName ."'";
		}
		foreach($fldLabel_array as $fldLabel => $datatype)
		{
			if($fieldLabel == '')
			{
				$fieldLabel = "'".$fldLabel ."'";
			}
			else
			{
				$fieldLabel .= ",'".$fldLabel ."'";
			}
			if($fldDataType == '')
			{
				$fldDataType = "'".$datatype ."'";
			}
			else
			{
				$fldDataType .= ",'".$datatype ."'";
			}
		}
	}
	$data['fieldname'] = $fieldName;
	$data['fieldlabel'] = $fieldLabel;
	$data['datatype'] = $fldDataType;
	$log->debug("Exiting split_validationdataArray method ...");
	return $data;
}


?>
