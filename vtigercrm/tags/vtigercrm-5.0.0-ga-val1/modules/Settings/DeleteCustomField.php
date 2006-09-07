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
require_once('include/database/PearDatabase.php');

$fld_module = $_REQUEST["fld_module"];

$id = $_REQUEST["fld_id"];

$colName = $_REQUEST["colName"];
$uitype = $_REQUEST["uitype"];

//Deleting the CustomField from the Custom Field Table
$query='delete from vtiger_field where fieldid="'.$id.'"';
$adb->query($query);

//Deleting from vtiger_profile2field table
$query='delete from vtiger_profile2field where fieldid="'.$id.'"';
$adb->query($query);

//Deleting from vtiger_def_org_field table
$query='delete from vtiger_def_org_field where fieldid="'.$id.'"';
$adb->query($query);

//Drop the column in the corresponding module table
$delete_module_tables = Array(
				"Leads"=>"vtiger_leadscf",
				"Accounts"=>"vtiger_accountscf",
				"Contacts"=>"vtiger_contactscf",
				"Potentials"=>"vtiger_potentialscf",
				"HelpDesk"=>"vtiger_ticketcf",
				"Products"=>"vtiger_productcf",
				"Vendors"=>"vtiger_vendorcf",
				"PriceBooks"=>"vtiger_pricebookcf",
				"PurchaseOrder"=>"vtiger_purchaseordercf",
				"SalesOrder"=>"vtiger_salesordercf",
				"Quotes"=>"vtiger_quotescf",
				"Invoice"=>"vtiger_invoicecf",
				"Campaigns"=>"vtiger_campaignscf",
			     );

$dbquery = 'alter table '.$delete_module_tables[$fld_module].' drop column '.$colName;
$adb->query($dbquery);


//HANDLE HERE - we have to remove the entries in customview and report related tables which have this field ($colName)


//Deleting from convert lead mapping vtiger_table- Jaguar
if($fld_module=="Leads")
{
	$deletequery = 'delete from vtiger_convertleadmapping where leadfid='.$id;
	$adb->query($deletequery);
}

//HANDLE HERE - we have to remove the table for other picklist type values which are text area and multiselect combo box 
if($uitype == 15)
{
	$deltablequery = 'drop table '.$colName;
	$adb->query($deltablequery);
}

header("Location:index.php?module=Settings&action=CustomFieldList&fld_module=".$fld_module."&parenttab=Settings");
?>
