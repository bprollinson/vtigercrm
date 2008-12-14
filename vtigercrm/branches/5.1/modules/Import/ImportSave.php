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

$count = 0;
$skip_required_count = 0;

/**	function used to save the records into database
 *	@param array $rows - array of total rows of the csv file
 *	@param array $rows1 - rows to be saved
 *	@param object $focus - object of the corresponding import module
 *	@param int $ret_field_count - total number of fields(columns) available in the csv file
 *	@param int $col_pos_to_field - field position in the mapped array
 *	@param int $start - starting row count value to import
 *	@param int $recordcount - count of records to be import ie., number of records to import
 *	@param string $module - import module
 *	@param int $totalnoofrows - total number of rows available
 *	@param int $skip_required_count - number of records skipped
 	This function will redirect to the ImportStep3 if the available records is greater than the record count (ie., number of records import in a single loop) otherwise (total records less than 500) then it will be redirected to import step last
 */
function InsertImportRecords($rows,$rows1,$focus,$ret_field_count,$col_pos_to_field,$start,$recordcount,$module,$totalnoofrows,$skip_required_count)
{
	global $current_user;
	global $adb;
	global $mod_strings;
	global $dup_ow_count;
	global $process_fields;

// MWC ** Getting vtiger_users
$temp = get_user_array(FALSE);
foreach ( $temp as $key=>$data)
	$my_users[$data] = $key;
p(print_r(my_users,1));
$adb->println("Users List : ");
$adb->println($my_users);
$dup_count = 0;
$count = 0;
$dup_ow_count = 0;
$process_fields='false';
if($start == 0)
{
	$_SESSION['totalrows'] = $rows;
	$_SESSION['return_field_count'] = $ret_field_count;
	$_SESSION['column_position_to_field'] = $col_pos_to_field;
}
$ii = $start;
// go thru each row, process and save()
foreach ($rows1 as $row)
{
	$adb->println("Going to Save the row ".$ii." =====> ");
	$adb->println($row);
	global $mod_strings;

	$do_save = 1;
	//MWC
	$my_userid = $current_user->id;

	//If we want to set default values for some fields for each entity then we have to set here
	if($module == 'Products')//discontinued is not null. if we unmap active, NULL will be inserted and query will fail
		$focus->column_fields['discontinued'] = 'on';

	for($field_count = 0; $field_count < $ret_field_count; $field_count++)
	{
		p("col_pos[".$field_count."]=".$col_pos_to_field[$field_count]);

		if ( isset( $col_pos_to_field[$field_count]) )
		{
			p("set =".$field_count);
			if (! isset( $row[$field_count]) )
			{
				continue;
			}

			p("setting");

			// TODO: add check for user input
			// addslashes, striptags, etc..
			$field = $col_pos_to_field[$field_count];

			//picklist function is added to avoid duplicate picklist entries
			$pick_orginal_val = getPicklist($field,$row[$field_count]);

			if($pick_orginal_val != null)
			{
				$focus->column_fields[$field]=$pick_orginal_val;
			}
			elseif (substr(trim($field), 0, 3) == "CF_") 
        		{
				p("setting custfld".$field."=".$row[$field_count]);
				$resCustFldArray[$field] = $row[$field_count]; 
        		}
			//MWC
			elseif ( $field == "assignedto" || $field == "assigned_user_id" )
			{
				//Here we are assigning the user id in column fields, so in function assign_user (ImportLead.php and ImportProduct.php files) we should use the id instead of user name when query the user
				//or we can use $focus->column_fields['smownerid'] = $my_users[$row[$field_count]];
				$focus->column_fields[$field] = $my_users[$row[$field_count]];	
				p("setting my_userid=$my_userid for user=".$row[$field_count]);
			}
			else
			{
				//$focus->$field = $row[$field_count];
				$focus->column_fields[$field] = $row[$field_count];
				p("Setting ".$field."=".$row[$field_count]);
			}
			
		}

	}
	if($focus->column_fields['notify_owner'] == '')
	{
		$focus->column_fields['notify_owner'] = '0';
	}	
	if($focus->column_fields['reference'] == '')
	{
		$focus->column_fields['reference'] = '0';
	}
	if($focus->column_fields['emailoptout'] == '')
	{
		$focus->column_fields['emailoptout'] = '0';
	}
	if($focus->column_fields['donotcall'] == '')
	{
		$focus->column_fields['donotcall'] = '0';
	}
	if($focus->column_fields['discontinued'] == '')
	{
		$focus->column_fields['discontinued'] = '0';
	}
	if($focus->column_fields['active'] == '')
	{
		$focus->column_fields['active'] = '0';
	}
	p("setting done");
	
	p("do save before req vtiger_fields=".$do_save);

	$adb->println($focus->required_fields);

	foreach ($focus->required_fields as $field=>$notused) 
	{ 
		$fv = trim($focus->column_fields[$field]);
		if (! isset($fv) || $fv == '') 
		{
			// Leads Import does not allow an empty lastname because the link is created on the lastname
			// Without lastname the Lead could not be opened.
			// But what if the import file has only company and telefone information?
			// It would be stupid to skip all the companies which don't have a contact person yet!
			// So we set lastname ="?????" and the user can later enter a name.
			// So the lastname is still mandatory but may be empty.
			if ($field == 'lastname' && $module == 'Leads')
			{
				$focus->column_fields[$field] = '?????';
			}
			else
			{
				p("fv ".$field." not set");	
				$do_save = 0; 
				$skip_required_count++; 
				break;
			}       	
		} 
	}

	//added for duplicate handling
	if(is_record_exist($module,$focus))
	{
		if($do_save != 0)
		{
			$do_save = 0;
			$dup_count++;
		}
	}
	p("do save=".$do_save);

	if ($do_save)
	{
		p("saving..");
	
		if ( ! isset($focus->column_fields["assigned_user_id"]) || $focus->column_fields["assigned_user_id"]=='')
		{
			//$focus->column_fields["assigned_user_id"] = $current_user->id;
			//MWC
			$focus->column_fields["assigned_user_id"] = $my_userid;
		}	

		// now do any special processing for ex., map account with contact and potential
		if($process_fields == 'false')
			$focus->process_special_fields();
	
		$focus->save($module);
		//$focus->saveentity($module);
		$return_id = $focus->id;

		if(count($resCustFldArray)>0)
		{

			if($_REQUEST['module'] == 'Contacts')
			{
				$_REQUEST['module']='contactdetails';
			}
			$dbquery="select * from vtiger_field where vtiger_tablename=?";
			$custresult = $adb->pquery($dbquery, array($_REQUEST['module']));
			if($adb->num_rows($custresult) != 0)
			{
				if (! isset( $_REQUEST['module'] ) || $_REQUEST['module'] == 'Contacts')
				{
					$columns = 'contactid';
					$custTabName = 'contactscf';
				}
				else if ( $_REQUEST['module'] == 'Accounts')
				{
					$columns = 'accountid';
					$custTabName = 'accountscf';	
				}
				else if ( $_REQUEST['module'] == 'Potentials')
				{
					$columns = 'potentialid';
					$custTabName = 'potentialscf';
				}
				else if ( $_REQUEST['module'] == 'Leads')
				{
					$columns = 'leadidid';
					$custTabName = 'leadscf';
				}
				else if ( $_REQUEST['module'] == 'Products')
				{
					$columns = 'productid';
					$custTabName = 'productcf';
				}
				else if ( $_REQUEST['module'] == 'Helpdesk')
				{
					$columns = 'ticketid';
					$custTabName = 'ticketcf';
				}
				else if ( $_REQUEST['module'] == 'Vendors')
				{
					$columns = 'vendorid';
					$custTabName = 'vendorcf';
				}
				$noofrows = $adb->num_rows($custresult);
				$params = array($focus->id);
				
				for($j=0; $j<$noofrows; $j++)
				{
					$colName=$adb->query_result($custresult,$j,"columnname");
					if(array_key_exists($colName, $resCustFldArray))
					{
						$value_colName = $resCustFldArray[$colName];

						$columns .= ', '.$colName;
						array_push($params, $value_colName);
					}
				}
				
				$insert_custfld_query = 'insert into '.$custTabName.' ('.$columns.') values('. generateQuestionMarks($params) .')';
				$adb->pquery($insert_custfld_query, $params);

			}
		}	
		
		$last_import = new UsersLastImport();		
		$last_import->assigned_user_id = $current_user->id;
		$last_import->bean_type = $_REQUEST['module'];
		$last_import->bean_id = $focus->id;
		$last_import->save();
		array_push($saved_ids,$focus->id);
		$count++;
	}
$ii++;	
}

$_REQUEST['count'] = $ii;
if(isset($_REQUEST['module']))
	$modulename = $_REQUEST['module'];

$end = $start+$recordcount;
$START = $start + $recordcount;
$RECORDCOUNT = $recordcount;
$dup_check_type = $_REQUEST['dup_type'];

if($end >= $totalnoofrows)
{
	$module = 'Import';//$_REQUEST['module'];
	$action = 'ImportSteplast';
	//exit;
	$imported_records = $ii - $skip_required_count;
	if($imported_records == $ii)
		$skip_required_count = 0;
	 if($dup_check_type == "auto")
	 {
		 $auto_dup_type = $_REQUEST['auto_type'];
		 if($auto_dup_type == "ignore")
			 $dup_info = $mod_strings['Duplicate_Records_Skipped_Info'].$dup_count;
		 else if($auto_dup_type == "overwrite")
			 $dup_info = $mod_strings['Duplicate_Records_Overwrite_Info'].$dup_ow_count;
	 }
	 else
		 $dup_info = "";

	 $message= urlencode("<b>".$mod_strings['LBL_SUCCESS']."</b>"."<br><br>" .$mod_strings['LBL_SUCCESS_1']."  $count" ."<br><br>" .$mod_strings['LBL_SKIPPED_1']."  $skip_required_count <br><br>".$dup_info );
}
else
{
	$module = 'Import';
	$action = 'ImportStep3';
}
?>

<script>
setTimeout("b()",1000);
function b()
{
	document.location.href="index.php?action=<?php echo $action?>&module=<?php echo $module?>&modulename=<?php echo $modulename?>&startval=<?php echo $end?>&recordcount=<?php echo $RECORDCOUNT?>&noofrows=<?php echo $totalnoofrows?>&message=<?php echo $message?>&skipped_record_count=<?php echo $skip_required_count?>&parenttab=<?php echo $_SESSION['import_parenttab']?>&dup_type=<?php echo $dup_check_type?>";
}
</script>

<?php
$_SESSION['import_display_message'] = '<br>'.$start.' '.$mod_strings['to'].' '.$end.' '.$mod_strings['of'].' '.$totalnoofrows.' '.$mod_strings['are_imported_succesfully'];
//return $_SESSION['import_display_message'];
}

function is_record_exist($module,$focus)
{
	global $adb;
	global $dup_ow_count;
	$dup_check_type = $_REQUEST['dup_type'];
	$auto_dup_type = "";
	$sec_parameter = "";
	if($dup_check_type == 'auto')
	{
	       $auto_dup_type = $_REQUEST['auto_type'];
	}
	if(($module == "Leads" || $module == "Accounts" || $module == "Contacts" || $module == "Products" || $module == "HelpDesk" || $module == "Potentials" || $module == "Vendors") && $auto_dup_type == "ignore")
	{
		$sec_parameter = getSecParameterforMerge($module);
		if($module == "Leads")
		{
			$sel_qry = "select count(*) as count from vtiger_leaddetails
		       		inner join vtiger_crmentity  on vtiger_crmentity.crmid = vtiger_leaddetails.leadid	
				inner join vtiger_leadsubdetails on vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid 
				inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid 
				LEFT JOIN vtiger_leadgrouprelation
						ON vtiger_leaddetails.leadid = vtiger_leadgrouprelation.leadid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
				where vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0 $sec_parameter";
		}
		else if($module == "Accounts")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_account
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_account.accountid
				INNER JOIN vtiger_accountbillads
					ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
				INNER JOIN vtiger_accountshipads
					ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
				LEFT JOIN vtiger_accountgrouprelation
					ON vtiger_account.accountid = vtiger_accountgrouprelation.accountid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_accountgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Contacts")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_contactdetails
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
				INNER JOIN vtiger_contactaddress
					ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
				INNER JOIN vtiger_contactsubdetails
					ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
				LEFT JOIN vtiger_customerdetails
						ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid 
				LEFT JOIN vtiger_contactgrouprelation
						ON vtiger_contactgrouprelation.contactid = vtiger_contactdetails.contactid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_contactgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Products")
		{
			$sel_qry = "SELECT count(*) as count FROM vtiger_products
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_products.productid	
				WHERE vtiger_crmentity.deleted = 0 ";
		}
		else if($module == "HelpDesk")
		{
			$sel_qry = "select count(*) as count from vtiger_troubletickets
		       		INNER JOIN vtiger_crmentity
						ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
					LEFT JOIN vtiger_ticketgrouprelation
					ON vtiger_troubletickets.ticketid = vtiger_ticketgrouprelation.ticketid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_crmentity.smownerid = vtiger_users.id
				WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Potentials")
		{
			$sel_qry = "select count(*) as count from vtiger_potential
		       		INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_potential.potentialid
				LEFT JOIN vtiger_potentialgrouprelation
					ON vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid	 
				WHERE vtiger_crmentity.deleted = 0 $sec_parameter";
		}
		else if($module == "Vendors")
		{
			$sel_qry = "select count(*) as count from vtiger_vendor
		       		INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid = vtiger_vendor.vendorid	 
				WHERE vtiger_crmentity.deleted = 0";
		}
		$sel_qry .= get_where_clause($module,$focus->column_fields);
		$result = $adb->query($sel_qry);
		$cnt = $adb->query_result($result,0,"count");
		if($cnt > 0)
			return true;
		else
			return false;
	}
	else if($auto_dup_type == "overwrite")
	{
		return overwrite_duplicate_records($module,$focus);
	}
	else
		return false;
}
//function to get the where clause for the duplicate - select query
function get_where_clause($module,$column_fields)
{
	global $current_user, $dup_ow_count;
	$where_clause = "";
	$field_values_array=getFieldValues($module);
	$field_values=$field_values_array['fieldnames_list'];
	$tblname_field_arr = explode(",",$field_values);
	$uitype_arr = $field_values_array['fieldname_uitype'];
	foreach($tblname_field_arr as $val)
	{
		list($tbl,$col,$fld) = explode(".",$val);
		$col_name = $tbl ."." . $col;
		$field_value=$column_fields[$fld];
		if($fld == 'account_id' && $column_fields['account_id'] !=''  && !is_integer($column_fields['account_id']))
			$field_value=getAccountId($column_fields['account_id']);
		elseif($fld == 'contact_id' && $column_fields['contact_id'] != '' && !is_integer($column_fields['contact_id']))
			$field_value=getContactId($column_fields['contact_id']);
		elseif($fld == 'vendor_id' && $column_fields['vendor_id'] !='' && !is_integer($column_fields['vendor_id']))
			$field_value=getVendorId($column_fields['vendor_id']);
		elseif($fld == 'parent_id' && $column_fields['parent_id'] !='' && !is_integer($column_fields['parent_id']))
			$field_value=getParentId($column_fields['parent_id']);
		elseif($fld == 'product_id' && $column_fields['product_id'] !='' && !is_integer($column_fields['product_id']))
			$field_value=getProductId($column_fields['product_id']);
		
		if(is_uitype($uitype_arr[$fld],'_users_list_') && $field_value == '') {
			$field_value = $current_user->id;
		}

		if(is_uitype($uitype_arr[$fld],'_date_')== true)
			$where_clause .= " AND ifnull(". $col_name .",'') = ifnull('".$field_value."','') ";
		else
			$where_clause .= " AND ". $col_name ." = '".$field_value."' ";
	}
	return $where_clause;	
}
//function to overwrite the existing duplicate records with the importing record's values
function overwrite_duplicate_records($module,$focus)
{
	global $adb;
	global $dup_ow_count;
	global $process_fields;
	//$module_id_array = Array("Leads"=>"leadid","Accounts"=>"accountid","Contacts"=>"contactid","Products"=>"productid","HelpDesk"=>"ticketid","Potentials"=>"potentialid","Vendors"=>"vendorid");
	//$field_values_array=getFieldValues($module);
	//$field_values=$field_values_array['fieldnames_list'];
	//$tblname_field_arr = explode(",",$field_values);
	
	$where_clause = "";
	$where = get_where_clause($module,$focus->column_fields);
	$sec_parameter = getSecParameterforMerge($module);
	if($module == "Leads")
	{
		$sel_qry = "select vtiger_leaddetails.leadid from vtiger_leaddetails
			inner join vtiger_crmentity  on vtiger_crmentity.crmid = vtiger_leaddetails.leadid
			inner join vtiger_leadsubdetails on vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
			inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid = vtiger_leaddetails.leadid
			LEFT JOIN vtiger_leadgrouprelation
						ON vtiger_leaddetails.leadid = vtiger_leadgrouprelation.leadid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
			where vtiger_crmentity.deleted = 0 AND vtiger_leaddetails.converted = 0 $where $sec_parameter order by vtiger_leaddetails.leadid ASC";
	}
	else if($module == "Accounts")
	{
		$sel_qry = "SELECT vtiger_account.accountid FROM vtiger_account
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_account.accountid
			INNER JOIN vtiger_accountbillads
			ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
			INNER JOIN vtiger_accountshipads
			ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
			LEFT JOIN vtiger_accountgrouprelation
					ON vtiger_account.accountid = vtiger_accountgrouprelation.accountid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_accountgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_account.accountid ASC";
	}
	else if($module == "Contacts")
	{
		$sel_qry = "SELECT vtiger_contactdetails.contactid FROM vtiger_contactdetails
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactaddress
			ON vtiger_contactaddress.contactaddressid = vtiger_contactdetails.contactid
			INNER JOIN vtiger_contactsubdetails
			ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
			LEFT JOIN vtiger_customerdetails
						ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
			LEFT JOIN vtiger_contactgrouprelation
						ON vtiger_contactgrouprelation.contactid = vtiger_contactdetails.contactid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_contactgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_contactdetails.contactid ASC";
	}
	else if($module == "Products")
	{
		$sel_qry = "SELECT vtiger_products.productid FROM vtiger_products
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_products.productid
			WHERE vtiger_crmentity.deleted = 0 $where order by vtiger_products.productid ASC";
	}
	else if($module == "HelpDesk")
	{
		$sel_qry = "SELECT vtiger_troubletickets.ticketid FROM vtiger_troubletickets
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid
			LEFT JOIN vtiger_ticketgrouprelation
					ON vtiger_troubletickets.ticketid = vtiger_ticketgrouprelation.ticketid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_crmentity.smownerid = vtiger_users.id
			WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_troubletickets.ticketid ASC";
	}
	else if($module == "Potentials")
	{
		$sel_qry = "SELECT vtiger_potential.potentialid FROM vtiger_potential
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			LEFT JOIN vtiger_potentialgrouprelation
					ON vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
			WHERE vtiger_crmentity.deleted = 0 $where $sec_parameter order by vtiger_potential.potentialid ASC";
	}
	else if($module == "Vendors")
	{
		$sel_qry = "SELECT vtiger_vendor.vendorid FROM vtiger_vendor
			INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
			WHERE vtiger_crmentity.deleted = 0 $where order by vtiger_vendor.vendorid ASC";
	}
	$result = $adb->query($sel_qry);
	$no_rows = $adb->num_rows($result);
	// now do any special processing for ex., map account with contact and potential
		$focus->process_special_fields();
		$process_fields='true';
	$moduleObj = new $module();
	if($no_rows > 0)
	{
		for($i=0;$i<$no_rows;$i++)
		{
			$id_field = $moduleObj->table_index;
			$id_value = $adb->query_result($result,$i,$id_field);
			if($i == 0)
			{
				$moduleObj->mode = "edit";
				$moduleObj->id = $id_value;
				$moduleObj->column_fields = $focus->column_fields;
				$moduleObj->save($module);
			}
			else{
				DeleteEntity($module,$module,$moduleObj,$id_value,"");
			}
		}
		$dup_ow_count = $dup_ow_count+$no_rows;
		return true;
	}
	else
		return false;
}

?>

