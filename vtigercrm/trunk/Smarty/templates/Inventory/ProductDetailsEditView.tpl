{*<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->*}

<script type="text/javascript" src="include/js/Inventory.js"></script>
<script type="text/javascript" src="include/js/general.js"></script>
<!-- Added to display the Product Details -->
<script type="text/javascript">
if(!e)
	window.captureEvents(Event.MOUSEMOVE);

//  window.onmousemove= displayCoords;
//  window.onclick = fnRevert;
  
function displayCoords(event,obj,mode,curr_row) 
{ldelim}
	if(mode != 'discount_final' && mode != 'sh_tax_div_title' && mode != 'group_tax_div_title')
	{ldelim}
		var curr_productid = document.getElementById("hdnProductId"+curr_row).value;
		if(curr_productid == '')
		{ldelim}
			alert("Please select a Product");
			return false;
		{rdelim}
	{rdelim}

	//Set the Header value for Discount
	if(mode == 'discount')
	{ldelim}
		document.getElementById("discount_div_title"+curr_row).innerHTML = '<b>Set Discount for : '+document.getElementById("productTotal"+curr_row).innerHTML;
	{rdelim}
	else if(mode == 'discount_final')
	{ldelim}
		document.getElementById("discount_div_title_final").innerHTML = '<b>Set Discount for : '+document.getElementById("netTotal").innerHTML;
	{rdelim}
	else if(mode == 'sh_tax_div_title')
	{ldelim}
		document.getElementById("sh_tax_div_title").innerHTML = '<b>Set S&H Tax for : '+document.getElementById("shipping_handling_charge").value;
	{rdelim}
	else if(mode == 'group_tax_div_title')
	{ldelim}
		var net_total_after_discount = eval(document.getElementById("netTotal").innerHTML)-eval(document.getElementById("discountTotal_final").innerHTML);
		document.getElementById("group_tax_div_title").innerHTML = '<b>Set Group Tax for : '+net_total_after_discount;
	{rdelim}

	var move_Element = document.getElementById(obj).style;
	if(!event)
	{ldelim}
		move_Element.left = e.pageX +'px' ;
		move_Element.top = e.pageY + 'px';	
	{rdelim}
	else
	{ldelim}
		move_Element.left = event.clientX +'px' ;
		move_Element.top = event.clientY + 'px';	
	{rdelim}

	move_Element.display = 'block';
{rdelim}
  
	function doNothing(){ldelim}
	{rdelim}
	
	function fnHidePopDiv(obj){ldelim}
		document.getElementById(obj).style.display = 'none';
	{rdelim}
</script>




<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable" id="proTab">
   <tr>
   	{if $MODULE neq 'PurchaseOrder'}
			<td colspan="5" class="detailedViewHeader">
	{else}
			<td colspan="4" class="detailedViewHeader">
	{/if}
		<b>{$APP.LBL_PRODUCT_DETAILS}</b>
	</td>
	<td class="detailedViewHeader" align="right">
		<b>{$APP.LBL_TAX_MODE}</b>
	</td>
	<td class="detailedViewHeader">
		<select id="taxtype" name="taxtype" onchange="decideTaxDiv();">
			<OPTION value="individual" selected>{$APP.LBL_INDIVIDUAL}</OPTION>
			<OPTION value="group">{$APP.LBL_GROUP}</OPTION>
		</select>
	</td>
   </tr>

   <!-- Header for the Product Details -->
   <tr>
	<td width=5% valign="top" class="small crmTableColHeading" align="right"><b>{$APP.LBL_TOOLS}</b></td>
	<td width=40% class="small crmTableColHeading"><font color='red'>*</font><b>{$APP.LBL_PRODUCT_NAME}</b></td>
	{if $MODULE neq 'PurchaseOrder'}
		<td width=10% class="small crmTableColHeading"><b>{$APP.LBL_QTY_IN_STOCK}</b></td>
	{/if}
	<td width=10% class="small crmTableColHeading"><b>{$APP.LBL_QTY}</b></td>
	<td width=10% class="small crmTableColHeading" align="right"><b>{$APP.LBL_LIST_PRICE}</b></td>
	<td width=12% nowrap class="small crmTableColHeading" align="right"><b>{$APP.LBL_TOTAL}</b></td>
	<td width=13% valign="top" class="small crmTableColHeading" align="right"><b>{$APP.LBL_NET_PRICE}</b></td>
   </tr>

   {foreach key=row_no item=data from=$ASSOCIATEDPRODUCTS}
	{assign var="productName" value="productName"|cat:$row_no}
	{assign var="comment" value="comment"|cat:$row_no}
	{assign var="qtyInStock" value="qtyInStock"|cat:$row_no}
	{assign var="qty" value="qty"|cat:$row_no}
	{assign var="listPrice" value="listPrice"|cat:$row_no}
	{assign var="total" value="total"|cat:$row_no}
	{assign var="hdnProductId" value="hdnProductId"|cat:$row_no}
	{assign var="hdnRowStatus" value="hdnRowStatus"|cat:$row_no}
	{assign var="hdnTotal" value="hdnTotal"|cat:$row_no}


   <tr id="row{$row_no}" valign="top">
	<td  class="crmTableRow small lineOnTop">&nbsp;
		<input type="hidden" id="{$hdnRowStatus}" name="{$hdnRowStatus}">
	</td>

	<!-- column 2 - Product Name - starts -->
	<td class="crmTableRow small lineOnTop">
		<table width="100%"  border="0" cellspacing="0" cellpadding="1">
		   <tr>
			<td class="small" valign="top">
				<input type="text" id="{$productName}" name="{$productName}" value="{$data.$productName}" class="small" readonly />
				<input type="hidden" id="{$hdnProductId}" name="{$hdnProductId}" value="{$data.$hdnProductId}">
				<img src="{$IMAGE_PATH}search.gif" style="cursor: pointer;" align="absmiddle" onclick="productPickList(this,'{$MODULE}','{$row_no}')" />
			</td>
		   </tr>
		   <tr>
			<td class="small" id="setComment">
				<textarea id="{$comment}" name="{$comment}" class=small style="width:70%;height:40px"></textarea>
				<br>
				[<a href="javascript:;" onclick="getObj('comment1').value='';";>{$APP.LBL_CLEAR_COMMENT}</a>]
			</td>
		   </tr>
		</table>
	</td>
	<!-- column 2 - Product Name - ends -->

	<!-- column 3 - Quantity in Stock - starts -->
	{if $MODULE eq 'Quotes' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
	   <td class="crmTableRow small lineOnTop" valign="top"><span id="{$qtyInStock}">{$data.$qtyInStock}</span></td>
	{/if}
	<!-- column 3 - Quantity in Stock - ends -->


	<!-- column 4 - Quantity - starts -->
	<td class="crmTableRow small lineOnTop" valign="top">
		<input id="{$qty}" name="{$qty}" type="text" class="small " style="width:50px" onfocus="this.className='detailedViewTextBoxOn'" onBlur="FindDuplicate(); settotalnoofrows(); calcTotal(); loadTaxes_Ajax('{$row_no}');" value="{$data.$qty}"/>
	</td>
	<!-- column 4 - Quantity - ends -->

	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - starts -->
	<td class="crmTableRow small lineOnTop" align="right" valign="top">
		<table width="100%" cellpadding="0" cellspacing="0">
		   <tr>
			<td align="right">
				<input id="{$listPrice}" name="{$listPrice}" value="{$data.$listPrice}" type="text" class="small " style="width:70px" onBlur="calcTotal()"/>&nbsp;<img src="{$IMAGE_PATH}pricebook.gif" onclick="priceBookPickList(this,'{$row_no}')">
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(event,'discount_div{$row_no}','discount','{$row_no}')" >{$APP.LBL_DISCOUNT}</a> : </b>
				<div class="discountUI" id="discount_div{$row_no}">
					<input type="hidden" id="discount_type{$row_no}" name="discount_type{$row_no}" value="">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					   <tr>
						<td id="discount_div_title{$row_no}" nowrap align="left" ></td>
						<td align="right"><img src="{$IMAGE_PATH}close.gif" border="0" onClick="fnHidePopDiv('discount_div{$row_no}')" style="cursor:pointer;"></td>
					   </tr>
					   <tr>
						<td align="left" class="lineOnTop"><input type="radio" name="discount{$row_no}" checked onclick="setDiscount(this,'{$row_no}')">&nbsp; {$APP.LBL_ZERO_DISCOUNT}</td>
						<td class="lineOnTop">&nbsp;</td>
					   </tr>
					   <tr>
						<td align="left"><input type="radio" name="discount{$row_no}" onclick="setDiscount(this,'{$row_no}')">&nbsp; % {$APP.LBL_OF_PRICE}</td>
						<td align="right"><input type="text" class="small" size="2" id="discount_percentage{$row_no}" name="discount_percentage{$row_no}" value="0" style="visibility:hidden" onBlur="setDiscount(this,'{$row_no}')">&nbsp;%</td>
					   </tr>
					   <tr>
						<td align="left" nowrap><input type="radio" name="discount{$row_no}" onclick="setDiscount(this,'{$row_no}')">&nbsp;{$APP.LBL_DIRECT_PRICE_REDUCTION}</td>
						<td align="right"><input type="text" id="discount_amount{$row_no}" name="discount_amount{$row_no}" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,{$row_no})"></td>
					   </tr>
					</table>
				</div>
			</td>
		   </tr>
		   <tr>
			<td align="right" style="padding:5px;" nowrap>
				<b>{$APP.LBL_TOTAL_AFTER_DISCOUNT} :</b>
			</td>
		   </tr>
		   <tr id="individual_tax_row{$row_no}" class="TaxShow">
			<td align="right" style="padding:5px;" nowrap>
				(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(event,'tax_div{$row_no}','tax','{$row_no}')" >{$APP.LBL_TAX} </a> : </b>
				<div class="discountUI" id="tax_div{$row_no}">
				</div>
			</td>
		   </tr>
		</table>
	</td>
	<!-- column 5 - List Price with Discount, Total After Discount and Tax as table - ends -->


	<!-- column 6 - Product Total - starts -->
	<td class="crmTableRow small lineOnTop" align="right">
		<table width="100%" cellpadding="5" cellspacing="0">
		   <tr>
			<td id="productTotal{$row_no}" align="right">&nbsp;</td>
		   </tr>
		   <tr>
			<td id="discountTotal{$row_no}" align="right">0.00</td>
		   </tr>
		   <tr>
			<td id="totalAfterDiscount{$row_no}" align="right">0.00</td>
		   </tr>
		   <tr>
			<td id="taxTotal{$row_no}" align="right">0.00</td>
		   </tr>
		</table>
	</td>
	<!-- column 6 - Product Total - ends -->

		<!-- Added to calculate the tax and total values when page loads -->
		<script>loadTaxes_Ajax("{$row_no}");</script>
		<!-- This above div is added to display the tax informations --> 


	<!-- column 7 - Net Price - starts -->
	<td valign="bottom" class="crmTableRow small lineOnTop" align="right">
		<span id="netPrice{$row_no}"><b>&nbsp;</b></span>
	</td>
	<!-- column 7 - Net Price - ends -->

   </tr>
   <!-- Product Details First row - Ends -->
   {/foreach}
</table>



<table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable">
   <!-- Add Product Button -->
   <tr>
	<td colspan="3">
			<input type="button" name="Button" class="small" value="{$APP.LBL_ADD_PRODUCT}" onclick="fnAddProductRow('{$MODULE}','{$IMAGE_PATH}');" />
	</td>
   </tr>



   <!-- Product Details Final Total Discount, Tax and Shipping&Hanling  - Starts -->
   <tr valign="top">
	<td width="88%" class="crmTableRow small lineOnTop" align="right"><b>{$APP.LBL_NET_TOTAL}</b></td>
	<td width="12%" id="netTotal" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>

   <tr valign="top">
	<td class="crmTableRow small lineOnTop" align="right">
		(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(event,'discount_div_final','discount_final','1')">{$APP.LBL_DISCOUNT}</a>
		<!-- Popup Discount DIV -->
		<div class="discountUI" id="discount_div_final">
			<input type="hidden" id="discount_type_final" name="discount_type_final" value="">
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
			   <tr>
				<td id="discount_div_title_final" nowrap align="left" ></td>
				<td align="right"><img src="{$IMAGE_PATH}close.gif" border="0" onClick="fnHidePopDiv('discount_div_final')" style="cursor:pointer;"></td>
			   </tr>
			   <tr>
				<td align="left" class="lineOnTop"><input type="radio" name="discount_final" checked onclick="setDiscount(this,'_final')">&nbsp; {$APP.LBL_ZERO_DISCOUNT}</td>
				<td class="lineOnTop">&nbsp;</td>
			   </tr>
			   <tr>
				<td align="left"><input type="radio" name="discount_final" onclick="setDiscount(this,'_final')">&nbsp; % {$APP.LBL_OF_PRICE}</td>
				<td align="right"><input type="text" class="small" size="2" id="discount_percentage_final" name="discount_percentage_final" value="0" style="visibility:hidden" onBlur="setDiscount(this,'_final')">&nbsp;%</td>
			   </tr>
			   <tr>
				<td align="left" nowrap><input type="radio" name="discount_final" onclick="setDiscount(this,'_final')">&nbsp;{$APP.LBL_DIRECT_PRICE_REDUCTION}</td>
				<td align="right"><input type="text" id="discount_amount_final" name="discount_amount_final" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,'_final')"></td>
			   </tr>
			</table>
		</div>
		<!-- End Div -->
	</td>
	<td id="discountTotal_final" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>


   <!-- Group Tax - starts -->
   <tr id="group_tax_row" valign="top" class="TaxHide">
	<td class="crmTableRow small lineOnTop" align="right">
		(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(event,'group_tax_div','group_tax_div_title',''); calcGroupTax();" >{$APP.LBL_TAX}</a></b>
				<!-- Pop Div For Group TAX -->
				<div class="discountUI" id="group_tax_div">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					   <tr>
						<td id="group_tax_div_title" colspan="2" nowrap align="left" ></td>
						<td align="right"><img src="{$IMAGE_PATH}close.gif" border="0" onClick="fnHidePopDiv('group_tax_div')" style="cursor:pointer;"></td>
					   </tr>

					{foreach item=tax_detail name=group_tax_loop key=loop_count from=$GROUP_TAXES}

					   <tr>
						<td align="left" class="lineOnTop">
							<input type="text" class="small" size="5" name="{$tax_detail.taxname}_group_percentage" id="group_tax_percentage{$smarty.foreach.group_tax_loop.iteration}" value="{$tax_detail.percentage}" onBlur="calcGroupTax()">&nbsp;%
						</td>
						<td align="center" class="lineOnTop">{$tax_detail.taxname}</td>
						<td align="right" class="lineOnTop">
							<input type="text" class="small" size="6" name="{$tax_detail.taxname}_group_amount" id="group_tax_amount{$smarty.foreach.group_tax_loop.iteration}" style="cursor:pointer;" value="0.00" readonly>
						</td>
					   </tr>

					{/foreach}
					<input type="hidden" id="group_tax_count" value="{$smarty.foreach.group_tax_loop.iteration}">

					</table>

				</div>
				<!-- End Popup Div Group Tax -->

	</td>
	<td id="tax_final" class="crmTableRow small lineOnTop" align="right">0.00</td>
   </tr>
   <!-- Group Tax - ends -->


   <tr valign="top">
	<td class="crmTableRow small" align="right">
		(+)&nbsp;<b>{$APP.LBL_SHIPPING_AND_HANDLING_CHARGES} </b>
	</td>
	<td class="crmTableRow small" align="right">
		<input id="shipping_handling_charge" name="shipping_handling_charge" type="text" class="small" style="width:40px" align="right" value="0.00" onBlur="calcSHTax();">
	</td>
   </tr>

   <tr valign="top">
	<td class="crmTableRow small" align="right">
		(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(event,'shipping_handling_div','sh_tax_div_title',''); calcSHTax();" >{$APP.LBL_TAX_FOR_SHIPPING_AND_HANDLING} </a></b>

				<!-- Pop Div For Shipping and Handlin TAX -->
				<div class="discountUI" id="shipping_handling_div">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
					   <tr>
						<td id="sh_tax_div_title" colspan="2" nowrap align="left" ></td>
						<td align="right"><img src="{$IMAGE_PATH}close.gif" border="0" onClick="fnHidePopDiv('shipping_handling_div')" style="cursor:pointer;"></td>
					   </tr>

					{foreach item=tax_detail name=sh_loop key=loop_count from=$SH_TAXES}

					   <tr>
						<td align="left" class="lineOnTop">
							<input type="text" class="small" size="3" name="{$tax_detail.taxname}_sh_percent" id="sh_tax_percentage{$smarty.foreach.sh_loop.iteration}" value="{$tax_detail.percentage}" onBlur="calcSHTax()">&nbsp;%
						</td>
						<td align="center" class="lineOnTop">{$tax_detail.taxname}</td>
						<td align="right" class="lineOnTop">
							<input type="text" class="small" size="4" name="{$tax_detail.taxname}_sh_amount" id="sh_tax_amount{$smarty.foreach.sh_loop.iteration}" style="cursor:pointer;" value="0.00" readonly>
						</td>
					   </tr>

					{/foreach}
					<input type="hidden" id="sh_tax_count" value="{$smarty.foreach.sh_loop.iteration}">

					</table>
				</div>
				<!-- End Popup Div for Shipping and Handling TAX -->

	</td>
	<td id="shipping_handling_tax" class="crmTableRow small" align="right">0.00</td>
   </tr>
   <tr valign="top">
	<td class="crmTableRow small" align="right">
		{$APP.LBL_ADJUSTMENT}
		<select id="adjustmentType" name="adjustmentType" class=small onchange="calcTotal();">
			<option value="+">Add</option>
			<option value="-">Deduct</option>
		</select>
	</td>
	<td class="crmTableRow small" align="right">
		<input id="adjustment" name="adjustment" type="text" class="small" style="width:40px" align="right" value="{$ADJUSTMENTVALUE}" onBlur="calcTotal();">
	</td>
   </tr>
   <tr valign="top">
	<td class="crmTableRow big lineOnTop" align="right"><b>{$APP.LBL_GRAND_TOTAL}</b></td>
	<td id="grandTotal" name="grandTotal" class="crmTableRow big lineOnTop" align="right">{$GRANDTOTAL}</td>
   </tr>
</table>
		<input type="hidden" name="totalProductCount" id="totalProductCount" value="">
		<input type="hidden" name="subtotal" id="subtotal" value="">
		<input type="hidden" name="total" id="total" value="">

<!-- Upto this Added to display the Product Details -->



