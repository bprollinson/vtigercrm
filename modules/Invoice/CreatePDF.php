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
require_once('include/fpdf/pdfconfig.php');
require_once('modules/Invoice/Invoice.php');
require_once('include/database/PearDatabase.php');

global $adb,$app_strings,$focus,$current_user;

$sql="select vtiger_currency_info.currency_symbol from vtiger_currency_info inner join vtiger_users on vtiger_users.currency_id =vtiger_currency_info.id where vtiger_users.id=?";
$result = $adb->pquery($sql, array($current_user->id));
$currency_symbol = $adb->query_result($result,0,'currency_symbol');

// would you like and end page?  1 for yes 0 for no
$endpage="1";

$id = $_REQUEST['record'];

//retreiving the vtiger_invoice info
$focus = new Invoice();
$focus->retrieve_entity_info($_REQUEST['record'],"Invoice");
$account_name = getAccountName($focus->column_fields[account_id]);
$invoice_no = $focus->column_fields[invoice_no];

// **************** BEGIN POPULATE DATA ********************


// populate data
if($focus->column_fields["salesorder_id"] != '')
	$so_name = getSoName($focus->column_fields["salesorder_id"]);
else
	$so_name = '';
$po_name = $focus->column_fields["vtiger_purchaseorder"];

$valid_till = $focus->column_fields["duedate"];
$valid_till = getDisplayDate($valid_till);
$bill_street = $focus->column_fields["bill_street"];
$bill_city = $focus->column_fields["bill_city"];
$bill_state = $focus->column_fields["bill_state"];
$bill_code = $focus->column_fields["bill_code"];
$bill_country = $focus->column_fields["bill_country"];

$contact_name =getContactName($focus->column_fields["contact_id"]);
$ship_street = $focus->column_fields["ship_street"];
$ship_city = $focus->column_fields["ship_city"];
$ship_state = $focus->column_fields["ship_state"];
$ship_code = $focus->column_fields["ship_code"];
$ship_country = $focus->column_fields["ship_country"];

$conditions = from_html($focus->column_fields["terms_conditions"]);
$description = from_html($focus->column_fields["description"]);
$status = $focus->column_fields["invoicestatus"];

// Company information
$add_query = "select * from vtiger_organizationdetails";
$result = $adb->pquery($add_query, array());
$num_rows = $adb->num_rows($result);

if($num_rows > 0)
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





//NOTE : Removed currency symbols and added with Grand Total text. it is enough to show the currency symbol in one place

//we can also get the NetTotal, Final Discount Amount/Percent, Adjustment and GrandTotal from the array $associated_products[1]['final_details']

//get the Associated Products for this Invoice
$focus->id = $focus->column_fields["record_id"];
$associated_products = getAssociatedProducts("Invoice",$focus);
$num_products = count($associated_products);

//This $final_details array will contain the final total, discount, Group Tax, S&H charge, S&H taxes and adjustment
$final_details = $associated_products[1]['final_details'];

//getting the Net Total
$price_subtotal = number_format($final_details["hdnSubTotal"],2,'.',',');

//Final discount amount/percentage
$discount_amount =$final_details["discount_amount_final"];
$discount_percent =$final_details["discount_percentage_final"];

if($discount_amount != "")
	$price_discount = number_format($discount_amount,2,'.',',');
else if($discount_percent != "")
{
	//This will be displayed near Discount label - used in include/fpdf/templates/body.php
	$final_price_discount_percent = "(".number_format($discount_percent,2,'.',',')." %)";
	$price_discount = number_format((($discount_percent*$final_details["hdnSubTotal"])/100),2,'.',',');
}
else
	$price_discount = "0.00";

//Adjustment
$price_adjustment = number_format($final_details["adjustment"],2,'.',',');
//Grand Total
$price_total = number_format($final_details["grandTotal"],2,'.',',');


//To calculate the group tax amount
if($final_details['taxtype'] == 'group')
{
	$group_tax_total = $final_details['tax_totalamount'];
	$price_salestax = number_format($group_tax_total,2,'.',',');

	$group_total_tax_percent = '0.00';
	$group_tax_details = $final_details['taxes'];
	for($i=0;$i<count($group_tax_details);$i++)
	{
		$group_total_tax_percent = $group_total_tax_percent+$group_tax_details[$i]['percentage'];
	}
}

//S&H amount
$sh_amount = $final_details['shipping_handling_charge'];
$price_shipping = number_format($sh_amount,2,'.',',');

//S&H taxes
$sh_tax_details = $final_details['sh_taxes'];
$sh_tax_percent = '0.00';
for($i=0;$i<count($sh_tax_details);$i++)
{
	$sh_tax_percent = $sh_tax_percent + $sh_tax_details[$i]['percentage'];
}
$sh_tax_amount = $final_details['shtax_totalamount'];
$price_shipping_tax = number_format($sh_tax_amount,2,'.',',');


//This is to get all prodcut details as row basis
for($i=1,$j=$i-1;$i<=$num_products;$i++,$j++)
{
	$product_name[$i] = $associated_products[$i]['productName'.$i];
	$prod_description[$i] = $associated_products[$i]['productDescription'.$i];
	$product_id[$i] = $associated_products[$i]['hdnProductId'.$i];
	$qty[$i] = $associated_products[$i]['qty'.$i];
	$unit_price[$i] = number_format($associated_products[$i]['unitPrice'.$i],2,'.',',');
	$list_price[$i] = number_format($associated_products[$i]['listPrice'.$i],2,'.',',');
	$list_pricet[$i] = $associated_products[$i]['listPrice'.$i];
	$discount_total[$i] = $associated_products[$i]['discountTotal'.$i];
	//aded for 5.0.3 pdf changes
	$product_code[$i] = $associated_products[$i]['hdnProductcode'.$i];
	
	$taxable_total = $qty[$i]*$list_pricet[$i]-$discount_total[$i];

	$producttotal = $taxable_total;
	$total_taxes = '0.00';
	if($focus->column_fields["hdnTaxType"] == "individual")
	{
		$total_tax_percent = '0.00';
		//This loop is to get all tax percentage and then calculate the total of all taxes
		for($tax_count=0;$tax_count<count($associated_products[$i]['taxes']);$tax_count++)
		{
			$tax_percent = $associated_products[$i]['taxes'][$tax_count]['percentage'];
			$total_tax_percent = $total_tax_percent+$tax_percent;
			$tax_amount = (($taxable_total*$tax_percent)/100);
			$total_taxes = $total_taxes+$tax_amount;
		}
		$producttotal = $taxable_total+$total_taxes;
		$product_line[$j]["Tax"] = number_format($total_taxes,2,'.',',')."\n ($total_tax_percent %) ";
	}
	$prod_total[$i] = number_format($producttotal,2,'.',',');

	$product_line[$j]["Product Code"] = $product_code[$i];
	$product_line[$j]["Product Name"] = from_html($product_name[$i]);
	$product_line[$j]["Qty"] = $qty[$i];
	$product_line[$j]["Price"] = $list_price[$i];
	$product_line[$j]["Discount"] = $discount_total[$i];
	$product_line[$j]["Total"] = $prod_total[$i];
}

//echo '<pre>Product Details ==>';print_r($product_line);echo '</pre>';
//echo '<pre>';print_r($associated_products);echo '</pre>';

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
	include("modules/Invoice/pdf_templates/header.php");
	include("include/fpdf/templates/body.php");

	//if bottom > 145 then we skip the Description and T&C in every page and display only in lastpage
	//if you want to display the description and T&C in each page then set the display_desc_tc='true' and bottom <= 145 in pdfconfig.php
	if($display_desc_tc == 'true')
	if($bottom <= 145)
	{
		include("modules/Invoice/pdf_templates/footer.php");
	}

	$page_num++;

	if (($endpage) && ($lastpage))
	{
		$pdf->AddPage();
		include("modules/Invoice/pdf_templates/header.php");
		include("modules/Invoice/pdf_templates/lastpage/body.php");
		include("modules/Invoice/pdf_templates/lastpage/footer.php");
	}
}


$pdf->Output('Invoice.pdf','D'); //added file name to make it work in IE, also forces the download giving the user the option to save

// Added to fix annoying bug that includes HTML in your PDF
exit();
?>
