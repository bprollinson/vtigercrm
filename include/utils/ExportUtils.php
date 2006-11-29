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


/**	function used to get the permitted blocks
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $blockid_list - list of block ids within the paranthesis with comma seperated
 */
function getPermittedBlocks($module, $disp_view)
{
	global $adb, $log;
	$log->debug("Entering into the function getPermittedBlocks($module, $disp_view)");
	
        $tabid = getTabid($module);
        $block_detail = Array();
        $query="select blockid,blocklabel,show_title from vtiger_blocks where tabid=$tabid and $disp_view=0 and visible = 0 order by sequence";

        $result = $adb->query($query);
        $noofrows = $adb->num_rows($result);
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

	$log->debug("Exit from the function getPermittedBlocks($module, $disp_view). Return value = $blockid_list");
	return $blockid_list;
}

/**	function used to get the query which will list the permitted fields 
 *	@param string $module - module name
 *	@param string $disp_view - view name, this may be create_view, edit_view or detail_view
 *	@return string $sql - query to get the list of fields which are permitted to the current user
 */
function getPermittedFieldsQuery($module, $disp_view)
{
	global $adb, $log;
	$log->debug("Entering into the function getPermittedFieldsQuery($module, $disp_view)");

	global $current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	//To get the permitted blocks
	$blockid_list = getPermittedBlocks($module, $disp_view);
	
        $tabid = getTabid($module);
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0 || $module == "Users")
	{
 		$sql = "SELECT vtiger_field.columnname, vtiger_field.fieldlabel, vtiger_field.tablename FROM vtiger_field WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN $blockid_list AND vtiger_field.displaytype IN (1,2,4) ORDER BY block,sequence";
  	}
  	else
  	{
		$profileList = getCurrentUserProfileList();
		$sql = "SELECT vtiger_field.columnname, vtiger_field.fieldlabel, vtiger_field.tablename FROM vtiger_field INNER JOIN vtiger_profile2field ON vtiger_profile2field.fieldid=vtiger_field.fieldid INNER JOIN vtiger_def_org_field ON vtiger_def_org_field.fieldid=vtiger_field.fieldid WHERE vtiger_field.tabid=".$tabid." AND vtiger_field.block IN ".$blockid_list." AND vtiger_field.displaytype IN (1,2,4) AND vtiger_profile2field.visible=0 AND vtiger_def_org_field.visible=0 AND vtiger_profile2field.profileid IN ".$profileList." GROUP BY vtiger_field.fieldid ORDER BY block,sequence";
	}

	$log->debug("Exit from the function getPermittedFieldsQuery($module, $disp_view). Return value = $sql");
	return $sql;
}

/**	function used to get the list of fields from the input query as a comma seperated string 
 *	@param string $query - field table query which contains the list of fields 
 *	@return string $fields - list of fields as a comma seperated string
 */
function getFieldsListFromQuery($query)
{
	global $adb, $log;
	$log->debug("Entering into the function getFieldsListFromQuery($query)");

	$result = $adb->query($query);
	$num_rows = $adb->num_rows($result);

	for($i=0; $i < $num_rows;$i++)
	{
		$columnName = $adb->query_result($result,$i,"columnname");
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$tablename = $adb->query_result($result,$i,"tablename");

		//HANDLE HERE - Mismatch fieldname-tablename in field table, in future we have to avoid these if elses
		if($columnName == 'smownerid')//for all assigned to user name
		{
			$fields .= "vtiger_users.user_name as '".$fieldlabel."', ";
		}
		elseif($tablename == 'vtiger_account' && $columnName == 'parentid')//Account - Member Of
		{
			 $fields .= "vtiger_account2.accountname as '".$fieldlabel."', ";
		}
		elseif($tablename == 'vtiger_contactdetails' && $columnName == 'accountid')//Contact - Account Name
		{
			$fields .= "vtiger_account.accountname as '".$fieldlabel."', ";
		}
		elseif($tablename == 'vtiger_contactdetails' && $columnName == 'reportsto')//Contact - Reports To
		{
			$fields .= " concat(vtiger_contactdetails2.lastname,' ',vtiger_contactdetails2.firstname) as 'Reports To Contact', ";
		}
		elseif($tablename == 'vtiger_potential' && $columnName == 'accountid')//Potential - Account Name
		{
			$fields .= "vtiger_account.accountname as '".$fieldlabel."',";
		}
		elseif($tablename == 'vtiger_potential' && $columnName == 'campaignid')//Potential - Campaign Source
		{
			$fields .= "vtiger_campaign.campaignname as '".$fieldlabel."',";
		}
		elseif($tablename == 'vtiger_seproductsrel' && $columnName == 'crmid')//Product - Related To
		{
			$fields .= "case vtiger_crmentityRelatedTo.setype 
					when 'Leads' then concat('Leads ::: ',vtiger_ProductRelatedToLead.lastname,' ',vtiger_ProductRelatedToLead.firstname) 
					when 'Accounts' then concat('Accounts ::: ',vtiger_ProductRelatedToAccount.accountname) 
					when 'Potentials' then concat('Potentials ::: ',vtiger_ProductRelatedToPotential.potentialname) 
				    End as 'Related To',";
			//This will export as 3 seperate columns for each Leads, Accounts and Potentials
			//$fields .= "  case vtiger_crmentityRelatedTo.setype when 'Leads' then vtiger_ProductRelatedToLead.lastname End as 'Lead Name', case vtiger_crmentityRelatedTo.setype when 'Accounts' then vtiger_ProductRelatedToAccount.accountname End as 'Account Name', case vtiger_crmentityRelatedTo.setype when 'Potentials' then vtiger_ProductRelatedToPotential.potentialname End as 'Potential Name',";
		}
		elseif($tablename == 'vtiger_products' && $columnName == 'contactid')//Product - Contact
		{
			$fields .= " concat(vtiger_contactdetails.lastname,' ',vtiger_contactdetails.firstname) as 'Contact Name',";
		}
		elseif($tablename == 'vtiger_products' && $columnName == 'vendor_id')//Product - Vendor Name
		{
			$fields .= "vtiger_vendor.vendorname as '".$fieldlabel."',";
		}
		elseif($tablename == 'vtiger_producttaxrel' && $columnName == 'taxclass')//avoid product - taxclass
		{
			$fields .= "";
		}
		elseif($tablename == 'vtiger_notes' && $columnName == 'contact_id')//Notes - contact_id
		{
			$fields .= " concat(vtiger_contactdetails.lastname,' ',vtiger_contactdetails.firstname) as 'Contact Name',";
		}
		elseif($tablename == 'vtiger_senotesrel' && $columnName == 'crmid')//Notes - Related To
		{
			$fields .= "case vtiger_crmentityRelatedTo.setype 
					when 'Leads' then concat('Leads ::: ',vtiger_NoteRelatedToLead.lastname,' ',vtiger_NoteRelatedToLead.firstname) 
					when 'Accounts' then concat('Accounts ::: ',vtiger_NoteRelatedToAccount.accountname) 
					when 'Potentials' then concat('Potentials ::: ',vtiger_NoteRelatedToPotential.potentialname) 
					when 'Products' then concat('Products ::: ',vtiger_NoteRelatedToProduct.productname) 
					when 'Invoice' then concat('Invoice ::: ',vtiger_NoteRelatedToInvoice.subject) 
					when 'PurchaseOrder' then concat('PurchaseOrder ::: ',vtiger_NoteRelatedToPO.subject) 
					when 'SalesOrder' then concat('SalesOrder ::: ',vtiger_NoteRelatedToSO.subject) 
				     End as 'Related To',";
		}
		elseif($tablename == 'vtiger_attachments' && $columnName == 'filename')//Emails filename
		{
			$fields .= $tablename.".name '".$fieldlabel."',";
		}
		else
		{
			$fields .= $tablename.".".$columnName. " '" .$fieldlabel."',";
		}
	}
	$fields = trim($fields,",");

	$log->debug("Exit from the function getFieldsListFromQuery($query). Return value = $fields");
	return $fields;
}



?>
