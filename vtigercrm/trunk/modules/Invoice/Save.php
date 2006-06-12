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
 * $Header: /cvsroot/vtigercrm/vtiger_crm/modules/Invoice/Save.php,v 1.8 2005/11/17 09:43:48 jerrydgeorge Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/Invoice/Invoice.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
include("modules/Emails/mail.php");

$local_log =& LoggerManager::getLogger('index');

$focus = new Invoice();

setObjectValuesFromRequest(&$focus);

$focus->save("Invoice");

//Checking if vtiger_salesorderid is present and updating the quote status
if($focus->column_fields["salesorder_id"] != '')
{
        $so_id = $focus->column_fields["salesorder_id"];
        $query1 = "update vtiger_salesorder set vtiger_sostatus='Approved' where vtiger_salesorderid=".$so_id;
        $adb->query($query1);
}


$ext_prod_arr = Array();
if($focus->mode == 'edit')
{
	$query2  = "select * from vtiger_invoiceproductrel where vtiger_invoiceid=".$focus->id;
	$result2 = $adb->query($query2);
	$num_rows = $adb->num_rows($result2);
	for($i=0; $i<$num_rows;$i++)
	{
		$pro_id = $adb->query_result($result2,$i,"productid");	
		$pro_qty = $adb->query_result($result2,$i,"quantity");
		$ext_prod_arr[$pro_id] = $pro_qty;	
	}	

        $query1 = "delete from vtiger_invoiceproductrel where vtiger_invoiceid=".$focus->id;
        $adb->query($query1);

}
//Printing the total Number of rows
$tot_no_prod = $_REQUEST['totalProductCount'];
for($i=1; $i<=$tot_no_prod; $i++)
{
        $product_id_var = 'hdnProductId'.$i;
        $status_var = 'hdnRowStatus'.$i;
        $qty_var = 'txtQty'.$i;
        $list_price_var = 'txtListPrice'.$i;

	$vat_var = 'txtVATTax'.$i;
	$sales_var = 'txtSalesTax'.$i;
	$service_var = 'txtServiceTax'.$i;

        $prod_id = $_REQUEST[$product_id_var];
        $prod_status = $_REQUEST[$status_var];
        $qty = $_REQUEST[$qty_var];
        $listprice = $_REQUEST[$list_price_var];
	$vat = $_REQUEST[$vat_var];
	$sales = $_REQUEST[$sales_var];
	$service = $_REQUEST[$service_var];

        if($prod_status != 'D')
        {

                $query ="insert into vtiger_invoiceproductrel values($focus->id, $prod_id, $qty, $listprice, $vat, $sales, $service)";
                $adb->query($query);
		//Updating the Quantity in Stock in the Product Table
		updateStk($prod_id,$qty,$focus->mode,$ext_prod_arr,'Invoice');
        }
}
$return_id = $focus->id;

if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] != "") $parenttab = $_REQUEST['parenttab'];
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = $_REQUEST['return_module'];
else $return_module = "Invoice";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = $_REQUEST['return_action'];
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = $_REQUEST['return_id'];

$local_log->debug("Saved record with id of ".$return_id);

//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=$_REQUEST['return_viewname'];

header("Location: index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&viewname=$return_viewname");

?>
