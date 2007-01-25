<?php
// watermark based on status
// this is the postion of the watermark before the rotate
$waterMarkPositions=array("50","180");
// this is the rotate amount (todo)
$waterMarkRotate=array("45","50","180");
$pdf->watermark( $status, $waterMarkPositions, $waterMarkRotate );

include("include/fpdf/pdfconfig.php");

// blow a bubble around the table
$Bubble=array("10",$body_top,"170","$bottom");
$pdf->tableWrapper($Bubble);

/* ************ Begin Table Setup ********************** */
// Each of these arrays needs to have matching keys
// "key" => "Length"
// total of columns needs to be 190 in order to fit the table
// correctly
$prodTable=array("10","60");

//if taxtype is individual
if($focus->column_fields["hdnTaxType"] == "individual")
{
	$colsAlign["Product Name"] = "L";
	$colsAlign["Description"] = "L";
	$colsAlign["Qty"] = "R";
	$colsAlign["Price"] = "R";
	$colsAlign["Discount"] = "R";
	$colsAlign["Tax"] = "R";
	$colsAlign["Total"] = "R";

	$cols["Product Name"] = "25";
	$cols["Description"] = "70";
	$cols["Qty"] = "10";
	$cols["Price"] = "25";
	$cols["Discount"] = "15";
	$cols["Tax"] = "20";
	$cols["Total"] = "25";
}
else
{
	//if taxtype is group
	$colsAlign["Product Name"] = "L";
	$colsAlign["Description"] = "L";
	$colsAlign["Qty"] = "R";
	$colsAlign["Price"] = "R";
	$colsAlign["Discount"] = "R";
	$colsAlign["Total"] = "R";

	$cols["Product Name"] = "25";
	$cols["Description"] = "70";
	$cols["Qty"] = "15";
	$cols["Price"] = "30";
	$cols["Discount"] = "20";
	$cols["Total"] = "30";
}


$pdf->addCols( $cols,$prodTable,$bottom, $focus->column_fields["hdnTaxType"]);
$pdf->addLineFormat( $colsAlign);

/* ************** End Table Setup *********************** */



/* ************* Begin Product Population *************** */
$ppad=3;
$y    = $body_top+10;

for($i=0;$i<count($line);$i++)
{
	$size = $pdf->addProductLine( $y, $line[$i] );
	$y   += $size+$ppad;
}

/* ******************* End product population ********* */


/* ************* Begin Totals ************************** */
$t=$bottom+56;
$pad=6;
for($i=0;$i<count($total);$i++)
{
	$size = $pdf->addProductLine( $t, $total[$i], $total[$i] );
	$t   += $pad;
}

//Set the x and y positions to place the NetTotal, Discount, S&H charge
//if taxtype is not individual ie., group tax
if($focus->column_fields["hdnTaxType"] != "individual")
{
	$lineData=array("105",$bottom+37,"94");
	$pdf->drawLine($lineData);
	$data= $app_strings['LBL_NET_TOTAL'].":                                                                  ".$price_subtotal."";
	$pdf->SetXY( 105 , ($nettotal_y+(0*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

	$lineData=array("105",$bottom+43,"94");
	$pdf->drawLine($lineData);

	//For alignment
	if($final_price_discount_percent != '')
		$data= $app_strings['LBL_DISCOUNT'].":   $final_price_discount_percent                                                ".$price_discount."";
	else
		$data= $app_strings['LBL_DISCOUNT'].":                                                                  ".$price_discount."";
	$pdf->SetXY( 105 , ($nettotal_y+(1*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

	$lineData=array("105",$bottom+49,"94");
	$pdf->drawLine($lineData);
	$data= $app_strings['LBL_TAX'].":  ($group_total_tax_percent %)                                                                  ".$price_salestax."";
	$pdf->SetXY( 105 , ($nettotal_y+(2*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

	$lineData=array("105",$bottom+55,"94");
	$pdf->drawLine($lineData);
	$data = $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGES'].":                                  ".$price_shipping;
	$pdf->SetXY( 105 , ($nettotal_y+(3*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

}
else
{
	//if taxtype is individual
	$lineData=array("105",$bottom+43,"94");
	$pdf->drawLine($lineData);
	$data= $app_strings['LBL_NET_TOTAL'].":                                                                  ".$price_subtotal."";
	$pdf->SetXY( 105 , ($nettotal_y+(1*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

	$lineData=array("105",$bottom+49,"94");
	$pdf->drawLine($lineData);

	//For alignment
	if($final_price_discount_percent != '')
		$data= $app_strings['LBL_DISCOUNT'].":   $final_price_discount_percent                                                 ".$price_discount."";
	else
		$data= $app_strings['LBL_DISCOUNT'].":                                                                   ".$price_discount."";

	$pdf->SetXY( 105 , ($nettotal_y+(2*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);

	$lineData=array("105",$bottom+55,"94");
	$pdf->drawLine($lineData);
	$data = $app_strings['LBL_SHIPPING_AND_HANDLING_CHARGES'].":                                  ".$price_shipping;
	$pdf->SetXY( 105 , ($nettotal_y+(3*$next_y)) );
	$pdf->SetFont( "Helvetica", "", 10);
	$pdf->MultiCell(110, 4, $data);
}

//Set the x and y positions to place the S&H Tax, Adjustment and Grand Total
$lineData=array("105",$bottom+61,"94");
$pdf->drawLine($lineData);
$data = $app_strings['LBL_TAX_FOR_SHIPPING_AND_HANDLING'].":  ($sh_tax_percent %)               ".$price_shipping_tax;
$pdf->SetXY( 105 , ($nettotal_y+(4*$next_y)) );
$pdf->SetFont( "Helvetica", "", 10);
$pdf->MultiCell(110, 4, $data);

$lineData=array("105",$bottom+67,"94");
$pdf->drawLine($lineData);
$data = $app_strings['LBL_ADJUSTMENT'].":                                                                   ".$price_adjustment;
$pdf->SetXY( 105 , ($nettotal_y+(5*$next_y)) );
$pdf->SetFont( "Helvetica", "", 10);
$pdf->MultiCell(110, 4, $data);

$lineData=array("105",$bottom+73,"94");
$pdf->drawLine($lineData);
$data = $app_strings['LBL_GRAND_TOTAL'].":(in $currency_symbol)                                                    ".$price_total;
$pdf->SetXY( 105 , ($nettotal_y+(6*$next_y)) );
$pdf->SetFont( "Helvetica", "", 10);
$pdf->MultiCell(110, 4, $data);

/* ************** End Totals *********************** */


?>
