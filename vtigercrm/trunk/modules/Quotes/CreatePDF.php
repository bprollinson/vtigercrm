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


require_once('include/fpdf/pdf.php');
require_once('modules/Quotes/Quote.php');
require_once('include/database/PearDatabase.php');

global $adb,$app_strings;

$sql="select currency_symbol from vtiger_currency_info";
$result = $adb->query($sql);
$currency_symbol = $adb->query_result($result,0,'currency_symbol');

// would you like and end page?  1 for yes 0 for no
$endpage="1";
global $products_per_page;
$products_per_page="6";

$focus = new Quote();
$focus->retrieve_entity_info($_REQUEST['record'],"Quotes");
$account_name = getAccountName($focus->column_fields[account_id]);

if($focus->column_fields["hdnTaxType"] == "individual") {
        $product_taxes = 'true';
} else {
        $product_taxes = 'false';
}

// **************** BEGIN POPULATE DATA ********************
$account_id = $focus->column_fields[account_id];
$quote_id=$_REQUEST['record'];

// Quote Information
$valid_till = $focus->column_fields["validtill"];
$valid_till = getDisplayDate($valid_till); 
$bill_street = $focus->column_fields["bill_street"];
$bill_city = $focus->column_fields["bill_city"];
$bill_state = $focus->column_fields["bill_state"];
$bill_code = $focus->column_fields["bill_code"];
$bill_country = $focus->column_fields["bill_country"];

$ship_street = $focus->column_fields["ship_street"];
$ship_city = $focus->column_fields["ship_city"];
$ship_state = $focus->column_fields["ship_state"];
$ship_code = $focus->column_fields["ship_code"];
$ship_country = $focus->column_fields["ship_country"];

$conditions = $focus->column_fields["terms_conditions"];
$description = $focus->column_fields["description"];
$status = $focus->column_fields["quotestage"];

// Company information
$add_query = "select * from vtiger_organizationdetails";
$result = $adb->query($add_query);
$num_rows = $adb->num_rows($result);

if($num_rows == 1)
{
		$org_name = $adb->query_result($result,0,"organizationname");
		$org_address = $adb->query_result($result,0,"address");
		$org_city = $adb->query_result($result,0,"city");
		$org_state = $adb->query_result($result,0,"state");
		$org_country = $adb->query_result($result,0,"country");
		$org_code = $adb->query_result($result,0,"code");
		$org_phone = $adb->query_result($result,0,"phone");
		$org_fax = $adb->query_result($result,0,"fax");
		$org_website = $adb->query_result($result,0,"website");

		$logo_name = $adb->query_result($result,0,"logoname");
}

//getting the Total Array
$price_subtotal = $currency_symbol.number_format(StripLastZero($focus->column_fields["hdnSubTotal"]),2,'.',',');
$price_tax = $currency_symbol.number_format(StripLastZero($focus->column_fields["txtTax"]),2,'.',',');
$price_adjustment = $currency_symbol.number_format(StripLastZero($focus->column_fields["txtAdjustment"]),2,'.',',');
$price_total = $currency_symbol.number_format(StripLastZero($focus->column_fields["hdnGrandTotal"]),2,'.',',');

//getting the Product Data
// DG 15 Aug 2006
// Add "ORDER BY sequence_no to preserve add order in printed version
// Seems not to be strictly necessary as the upstream ORDER BY in EditView appears to produce this behaviour by default
// Having this here makes sure though. 
$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.product_description, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$quote_id." ORDER BY sequence_no";
//$query="select vtiger_products.productname, vtiger_products.unit_price, vtiger_products.product_description, vtiger_inventoryproductrel.* from vtiger_inventoryproductrel inner join vtiger_products on vtiger_products.productid=vtiger_inventoryproductrel.productid where id=".$quote_id;

global $result;
$result = $adb->query($query);
$num_products=$adb->num_rows($result);
for($i=0;$i<$num_products;$i++) {
	$product_name[$i]=$adb->query_result($result,$i,'productname');
	$prod_description[$i]=$adb->query_result($result,$i,'product_description');
	$product_id[$i]=$adb->query_result($result,$i,'productid');
	$qty[$i]=$adb->query_result($result,$i,'quantity');

	$unit_price[$i]= $currency_symbol.number_format($adb->query_result($result,$i,'unit_price'),2,'.',',');
	$list_price[$i]= $currency_symbol.number_format(StripLastZero($adb->query_result($result,$i,'listprice')),2,'.',',');
	$list_pricet[$i]= $adb->query_result($result,$i,'listprice');
	$prod_total[$i]= $qty[$i]*$list_pricet[$i];

        $total_taxes = '0.00';
        if($product_taxes == "true") {
                $q = "SELECT * FROM vtiger_inventoryproductrel WHERE id='".$focus->column_fields["record_id"]."' AND productid='".$product_id[$i]."' AND tax2 IS NOT NULL";
                $trs = $adb->query($q);
                $tax = $adb->query_result($trs,'0','tax2');
                $taxable_total = ($adb->query_result($trs,'0','listprice') * $adb->query_result($trs,'0','quantity'));
                if($tax != "") {
                        $total_taxes = ($taxable_total/$tax);
                        $prod_total[$i] = ($prod_total[$i]+$total_taxes);
                }
        }

        $product_line[] = array( "Product Name"    => $product_name[$i],
                "Description"  => $prod_description[$i],
                "Qty"     => $qty[$i],
                "List Price"      => $list_price[$i],
                "Unit Price" => $unit_price[$i],
                "Tax" => $currency_symbol.$total_taxes,
                "Total" => $currency_symbol.$prod_total[$i]
        );
}

	$total[]=array("Unit Price" => $app_strings['LBL_SUB_TOTAL'],
		"Total" => $price_subtotal);

	$total[]=array("Unit Price" => $app_strings['LBL_ADJUSTMENT'],
		"Total" => $price_adjustment);

	$total[]=array("Unit Price" => $app_strings['LBL_TAX'],
		"Total" => $price_tax);

	$total[]=array("Unit Price" => $app_strings['LBL_GRAND_TOTAL'],
		"Total" => $price_total);


// ************************ END POPULATE DATA ***************************8

$page_num='1';
$pdf = new PDF( 'P', 'mm', 'A4' );
$pdf->Open();

$num_pages=ceil(($num_products/$products_per_page));


$current_product=0;
for($l=0;$l<$num_pages;$l++)
{
	$line=array();
	if($num_pages == $page_num)
		$lastpage=1;

	while($current_product != $page_num*$products_per_page)
	{
		$line[]=$product_line[$current_product];
		$current_product++;
	}

	$pdf->AddPage();
	include("pdf_templates/header.php");
	include("include/fpdf/templates/body.php");
	include("pdf_templates/footer.php");

	$page_num++;

	if (($endpage) && ($lastpage))
	{
		$pdf->AddPage();
		include("pdf_templates/header.php");
		include("pdf_templates/lastpage/body.php");
		include("pdf_templates/lastpage/footer.php");
	}
}


$pdf->Output('Quotes.pdf','D'); //added file name to make it work in IE, also forces the download giving the user the option to save

?>
