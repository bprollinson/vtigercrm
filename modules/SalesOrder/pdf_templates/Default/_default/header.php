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
$companyBlockPositions=array( "10","23","60" );
$companyText=$org_address."\n".$org_city.", ".$org_state." ".$org_code." ".$org_country;
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
$shipLocation = array("10","43","60");
$shipText=$ship_street."\n".$ship_city.", ".$ship_state." ".$ship_code."\n".$ship_country;
$pdf->addTextBlock( "Shipping Address:", $shipText, $shipLocation );

// billing Address
$billPositions = array("147","40","60");
$billText=$bill_street."\n".$bill_city.", ".$bill_state." ".$bill_code."\n".$bill_country;
$pdf->addTextBlock("Billing Address:",$billText, $billPositions);
// ********** End Addresses ******************



/*  ******** Begin Invoice Data ************************ */ 

// issue date block
$issueBlock=array("80","37");
$pdf->addRecBlock(date("Y-m-d"), "Issue Date",$issueBlock);

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
$pdf->addRecBlock($id, "Sales Order Number",$invBlock);

/* ************ End Invoice Data ************************ */



?>
