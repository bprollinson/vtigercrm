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


define('USD',"$");
define('EURO', chr(128) );

// ************** Begin company information *****************
$imageBlock=array("10","3","0","0");
$pdf->addImage( $logo_name, $imageBlock);

// x,y,width
if($org_phone != '')
  $phone ="\nPhone:".$org_phone;
$companyBlockPositions=array( "10","23","62" );
$companyText=$org_address."\n".$org_city.", ".$org_state." ".$org_code." ".$org_country." ".$phone."\n".$org_website;
$pdf->addTextBlock( $org_name, $companyText ,$companyBlockPositions );

// ************** End company information *******************



// ************* Begin Top-Right Header ***************
// title
$titleBlock=array("140","7");
$pdf->title( "SalesOrder","", $titleBlock );

$soBubble=array("161","17","20");
$pdf->addBubbleBlock($quote_name, "Quote Name", $soBubble);

$poBubble=array("99","17","20");
$pdf->addBubbleBlock($subject, "Subject", $poBubble);

// page number
$pageBubble=array("140","17",0);
$pdf->addBubbleBlock($page_num, "Page", $pageBubble);
// ************** End Top-Right Header *****************



// ************** Begin Addresses **************
// shipping Address
$shipLocation = array("147","40","61");
if(trim($ship_street)!='')
	$shipText = $ship_street."\n";
if(trim($ship_city) !='')
	$shipText .= $ship_city.", ";
if(trim($ship_state)!='' || trim($ship_code)!= '')
	$shipText .= $ship_state." ".$ship_code."\n";

	$shipText .=$ship_country;
$pdf->addTextBlock( "Shipping Address:", $shipText, $shipLocation );

// billing Address
$billPositions = array("10","51","61");
if(trim($bill_street)!='')
	$billText = $bill_street."\n";
if(trim($bill_city) !='')
	$billText .= $bill_city.", ";
if(trim($bill_state)!='' || trim($bill_code)!= '')
	$billText .= $bill_state." ".$bill_code."\n";

	$billText .=$bill_country;
$pdf->addTextBlock("Billing Address:",$billText, $billPositions);
// ********** End Addresses ******************



/*  ******** Begin Invoice Data ************************ */ 

// issue date block
$issueBlock=array("80","37");
$pdf->addRecBlock(getDisplayDate(date("Y-m-d")), "Issue Date",$issueBlock);

// due date block
$dueBlock=array("81","52");
$pdf->addRecBlock($valid_till, "Due Date",$dueBlock);

// terms block
$termBlock=array("10","67");
$pdf->addRecBlock($account_name, "Customer Name", $termBlock);

// Contact Name block
$conBlock=array("79","67");
$pdf->addRecBlock($contact_name, "Contact Name",$conBlock);



// vtiger_invoice number block
$invBlock=array("145","67");
$pdf->addRecBlock($id, "SO Number",$invBlock);

/* ************ End Invoice Data ************************ */



?>
