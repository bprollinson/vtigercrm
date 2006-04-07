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
 * $Header: /cvsroot/vtigercrm/vtiger_crm/modules/SalesOrder/Save.php,v 1.1 2005/12/16 04:13:15 mangai Exp $
 * Description:  Saves an Account record and then redirects the browser to the 
 * defined return URL.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('modules/SalesOrder/SalesOrder.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
include("modules/Emails/mail.php");

$local_log =& LoggerManager::getLogger('index');

$focus = new SalesOrder();

setObjectValuesFromRequest(&$focus);

$focus->save("SalesOrder");

//Checking if quote_id is present and updating the quote status
if($focus->column_fields["quote_id"] != '')
{
        $qt_id = $focus->column_fields["quote_id"];
        $query1 = "update quotes set quotestage='Accepted' where quoteid=".$qt_id;
        $adb->query($query1);
}

$ext_prod_arr = Array();
if($focus->mode == 'edit')
{
	$query2  = "select * from soproductrel where salesorderid=".$focus->id;
        $result2 = $adb->query($query2);
        $num_rows = $adb->num_rows($result2);
        for($i=0; $i<$num_rows;$i++)
        {
                $pro_id = $adb->query_result($result2,$i,"productid");
                $pro_qty = $adb->query_result($result2,$i,"quantity");
                $ext_prod_arr[$pro_id] = $pro_qty;
        }

	
        $query1 = "delete from soproductrel where salesorderid=".$focus->id;
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

        $prod_id = $_REQUEST[$product_id_var];
        $prod_status = $_REQUEST[$status_var];
        $qty = $_REQUEST[$qty_var];
        $listprice = $_REQUEST[$list_price_var];
        if($prod_status != 'D')
        {

                $query ="insert into soproductrel values(".$focus->id.",".$prod_id.",".$qty.",".$listprice.")";
                $adb->query($query);
		updateStk($prod_id,$qty,$focus->mode,$ext_prod_arr,'SalesOrder');
        }
}
$return_id = $focus->id;

if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] != "") $parenttab = $_REQUEST['parenttab'];
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = $_REQUEST['return_module'];
else $return_module = "SalesOrder";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = $_REQUEST['return_action'];
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = $_REQUEST['return_id'];

$local_log->debug("Saved record with id of ".$return_id);

//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=$_REQUEST['return_viewname'];

header("Location: index.php?action=$return_action&module=$return_module&parenttab=$parenttab&record=$return_id&viewname=$return_viewname&smodule=SO");

?>
