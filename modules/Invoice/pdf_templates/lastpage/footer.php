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



// draw a broken line
$width=3;
$area=216;
$pad=2;

for ($i=10;$i<200;$i++) {
	$linePos=array($i,$area,$width);
	$pdf->drawLine($linePos);
	$i = (($i+$width)+$pad)-1;
}

// company addy
if($org_phone != '')
$phone="\nPhone:	".$org_phone;	
if($org_fax != '')
  $fax ="\nFax:		".$org_fax;	
$companyBlockPositions=array( "10","220","60" );
$companyText=$org_address."\n".$org_city.", ".$org_state." ".$org_code." ".$org_country." ".$phone." ".$fax."\n".$org_website ;
$pdf->addTextBlock( $org_name, $companyText ,$companyBlockPositions );


// billing Address
$billPositions = array("85","235","60");
if(trim($bill_street)!='')
	$billText = $bill_street."\n";
if(trim($bill_city) !='')
	$billText .= $bill_city.", ";
if(trim($bill_state)!='' || trim($bill_code)!= '')
	$billText .= $bill_state." ".$bill_code."\n";

	$billText .=$bill_country;
$pdf->addTextBlock("Billing Address:",$billText, $billPositions);

// totals
$totalBlock=array("145","235","10", "110");
$totalText="SubTotal:      ".$price_subtotal."\n".
	   "Tax:              ".$price_salestax."\n".
	   "Adjustment:  ".$price_adjustment."\n".
	   "Total:            ".$price_total;
$pdf->addDescBlock($totalText, "Total Due", $totalBlock);

$blurbBlock=array("10","265","150", "60");
$blockText="Detach on above line and send a check, money order or cashiers check in the provided envelope";
$pdf->addDescBlock($blockText, "Instructions", $blurbBlock);

?>
