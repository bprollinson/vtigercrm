/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function copyAddressRight(form) {

	if(typeof(form.bill_street) != 'undefined' && typeof(form.ship_street) != 'undefined')
		form.ship_street.value = form.bill_street.value;

	if(typeof(form.bill_city) != 'undefined' && typeof(form.ship_city) != 'undefined')
		form.ship_city.value = form.bill_city.value;

	if(typeof(form.bill_state) != 'undefined' && typeof(form.ship_state) != 'undefined')
		form.ship_state.value = form.bill_state.value;

	if(typeof(form.bill_code) != 'undefined' && typeof(form.ship_code) != 'undefined')
		form.ship_code.value = form.bill_code.value;

	if(typeof(form.bill_country) != 'undefined' && typeof(form.ship_country) != 'undefined')
		form.ship_country.value = form.bill_country.value;

	if(typeof(form.bill_pobox) != 'undefined' && typeof(form.ship_pobox) != 'undefined')
		form.ship_pobox.value = form.bill_pobox.value;
	
	return true;

}

function copyAddressLeft(form) {

	if(typeof(form.bill_street) != 'undefined' && typeof(form.ship_street) != 'undefined')
		form.bill_street.value = form.ship_street.value;
	
	if(typeof(form.bill_city) != 'undefined' && typeof(form.ship_city) != 'undefined')
		form.bill_city.value = form.ship_city.value;

	if(typeof(form.bill_state) != 'undefined' && typeof(form.ship_state) != 'undefined')
		form.bill_state.value = form.ship_state.value;

	if(typeof(form.bill_code) != 'undefined' && typeof(form.ship_code) != 'undefined')
		form.bill_code.value =	form.ship_code.value;

	if(typeof(form.bill_country) != 'undefined' && typeof(form.ship_country) != 'undefined')
		form.bill_country.value = form.ship_country.value;

	if(typeof(form.bill_pobox) != 'undefined' && typeof(form.ship_pobox) != 'undefined')
		form.bill_pobox.value = form.ship_pobox.value;

	return true;

}

function settotalnoofrows() {
	var max_row_count = document.getElementById('proTab').rows.length;
        max_row_count = eval(max_row_count)-2;

	//set the total number of products
	document.EditView.totalProductCount.value = max_row_count;	
}

function productPickList(currObj,module, row_no) {
	var trObj=currObj.parentNode.parentNode
	var rowId=row_no;//parseInt(trObj.id.substr(trObj.id.indexOf("w")+1,trObj.id.length))

	popuptype = 'inventory_prod';
	if(module == 'PurchaseOrder')
		popuptype = 'inventory_prod_po';
	var record_id = ''
        if(document.getElementsByName("account_id").length != 0)
                record_id= document.EditView.account_id.value;
        if(record_id != '')
                window.open("index.php?module=Products&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype="+popuptype+"&curr_row="+rowId+"&relmod_id="+record_id+"&parent_module=Accounts","productWin","width=640,height=600,resizable=0,scrollbars=0,status=1,top=150,left=200");
        else
		window.open("index.php?module=Products&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype="+popuptype+"&curr_row="+rowId,"productWin","width=640,height=600,resizable=0,scrollbars=0,status=1,top=150,left=200");
}

function priceBookPickList(currObj, row_no) {
	var trObj=currObj.parentNode.parentNode
	var rowId=row_no;//parseInt(trObj.id.substr(trObj.id.indexOf("w")+1,trObj.id.length))
	window.open("index.php?module=PriceBooks&action=Popup&html=Popup_picker&form=EditView&popuptype=inventory_pb&fldname=listPrice"+rowId+"&productid="+getObj("hdnProductId"+rowId).value,"priceBookWin","width=640,height=565,resizable=0,scrollbars=0,top=150,left=200");
}


function getProdListBody() {
	if (browser_ie) {
		var prodListBody=getObj("productList").children[0].children[0]
	} else if (browser_nn4 || browser_nn6) {
		if (getObj("productList").childNodes.item(0).tagName=="TABLE") {
			var prodListBody=getObj("productList").childNodes.item(0).childNodes.item(0)
		} else {
			var prodListBody=getObj("productList").childNodes.item(1).childNodes.item(1)
		}
	}
	return prodListBody;
}


function deleteRow(module,i)
{
	rowCnt--;
	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;
//	document.getElementById('proTab').deleteRow(i);
	document.getElementById("row"+i).style.display = 'none';
	document.getElementById("hdnProductId"+i).value = "";
	//document.getElementById("productName"+i).value = "";
	document.getElementById('deleted'+i).value = 1;
	calcTotal()
}
/*  End */



function calcTotal() {

	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//Because the table has two header rows. so we will reduce two from row length
	var netprice = 0.00;
	for(var i=1;i<=max_row_count;i++)
	{
		rowId = i;
		
		if(document.getElementById('deleted'+rowId).value == 0)
		{
			
			var total=eval(getObj("qty"+rowId).value*getObj("listPrice"+rowId).value);
			getObj("productTotal"+rowId).innerHTML=roundValue(total.toString())

			var totalAfterDiscount = eval(total-document.getElementById("discountTotal"+rowId).innerHTML);
			getObj("totalAfterDiscount"+rowId).innerHTML=roundValue(totalAfterDiscount.toString())

			
			var tax_type = document.getElementById("taxtype").value;
			//if the tax type is individual then add the tax with net price
			if(tax_type == 'individual')
			{	
				callTaxCalc(i);
				netprice = totalAfterDiscount+eval(document.getElementById("taxTotal"+rowId).innerHTML);
			}
			else
				netprice = totalAfterDiscount;
			
			getObj("netPrice"+rowId).innerHTML=roundValue(netprice.toString())

		}
	}
	calcGrandTotal();
}

function calcGrandTotal() {
	var netTotal = 0.0, grandTotal = 0.0;
	var discountTotal_final = 0.0, finalTax = 0.0, sh_amount = 0.0, sh_tax = 0.0, adjustment = 0.0;

	var taxtype = document.getElementById("taxtype").value;

	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//Because the table has two header rows. so we will reduce two from row length

	for (var i=1;i<=max_row_count;i++) 
	{
		if(document.getElementById('deleted'+i).value == 0)
		{
			
			if (document.getElementById("netPrice"+i).innerHTML=="") 
				document.getElementById("netPrice"+i).innerHTML = 0.0
			if (!isNaN(document.getElementById("netPrice"+i).innerHTML))
				netTotal += parseFloat(document.getElementById("netPrice"+i).innerHTML)
		}
	}
//	alert(netTotal);
	document.getElementById("netTotal").innerHTML = netTotal;
	document.getElementById("subtotal").value = netTotal;
	setDiscount(this,'_final');
	calcGroupTax();
	//Tax and Adjustment values will be taken when they are valid integer or decimal values
	//if(/^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("txtTax").value))
	//	txtTaxVal = parseFloat(getObj("txtTax").value);	
	//if(/^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("txtAdjustment").value))
	//	txtAdjVal = parseFloat(getObj("txtAdjustment").value);

	discountTotal_final = document.getElementById("discountTotal_final").innerHTML

	//get the final tax based on the group or individual tax selection
	var taxtype = document.getElementById("taxtype").value;
	if(taxtype == 'group')
		finalTax = document.getElementById("tax_final").innerHTML

	sh_amount = getObj("shipping_handling_charge").value
	sh_tax = document.getElementById("shipping_handling_tax").innerHTML

	adjustment = getObj("adjustment").value

	//Add or substract the adjustment based on selection
	adj_type = document.getElementById("adjustmentType").value;
	if(adj_type == '+')
		grandTotal = eval(netTotal)-eval(discountTotal_final)+eval(finalTax)+eval(sh_amount)+eval(sh_tax)+eval(adjustment)
	else
		grandTotal = eval(netTotal)-eval(discountTotal_final)+eval(finalTax)+eval(sh_amount)+eval(sh_tax)-eval(adjustment)

	document.getElementById("grandTotal").innerHTML = roundValue(grandTotal.toString())
	document.getElementById("total").value = roundValue(grandTotal.toString())
}

//Method changed as per advice by jon http://forums.vtiger.com/viewtopic.php?t=4162
function roundValue(val) {
   val = parseFloat(val);
   val = Math.round(val*100)/100;
   val = val.toString();
   
   if (val.indexOf(".")<0) {
      val+=".00"
   } else {
      var dec=val.substring(val.indexOf(".")+1,val.length)
      if (dec.length>2)
         val=val.substring(0,val.indexOf("."))+"."+dec.substring(0,2)
      else if (dec.length==1)
         val=val+"0"
   }
   
   return val;
} 

//This function is used to validate the Inventory modules 
function validateInventory(module) 
{
	if(!formValidate())
		return false

	//for products, vendors and pricebook modules we won't validate the product details. here return the control
	if(module == 'Products' || module == 'Vendors' || module == 'PriceBooks')
	{
		return true;
	}

	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//As the table has two header rows, we will reduce two from table row length

	if(!FindDuplicate())
		return false;

	if(max_row_count == 0)
	{
		alert(alert_arr.NO_PRODUCT_SELECTED);
		return false;
	}

	for (var i=1;i<=max_row_count;i++) 
	{
		//if the row is deleted then avoid validate that row values
		if(document.getElementById("deleted"+i).value == 1)
			continue;

		if (!emptyCheck("productName"+i,"Product","text")) return false
		if (!emptyCheck("qty"+i,"Qty","text")) return false
		if (!numValidate("qty"+i,"Qty","any")) return false
		if (!numConstComp("qty"+i,"Qty","G","0")) return false
		if (!emptyCheck("listPrice"+i,"List Price","text")) return false
		if (!numValidate("listPrice"+i,"List Price","any")) return false           
	}

	//Product - Discount validation - not allow negative values
	if(!validateProductDiscounts())
		return false;

	//Final Discount validation - not allow negative values
	discount_checks = document.getElementsByName("discount_final");

	//Percentage selected, so validate the percentage
	if(discount_checks[1].checked == true)
	{
		temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("discount_percentage_final").value);
		if(!temp)
		{
			alert(alert_arr.VALID_FINAL_PERCENT);
			return false;
		}
	}
	if(discount_checks[2].checked == true)
	{
		temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("discount_amount_final").value);
		if(!temp)
		{
			alert(alert_arr.VALID_FINAL_AMOUNT);
			return false;
		}
	}

	//Shipping & Handling validation - not allow negative values
	temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("shipping_handling_charge").value);
	if(!temp)
	{
		alert(alert_arr.VALID_SHIPPING_CHARGE);
		return false;
	}

	//Adjustment validation - allow negative values
	temp = /^-?(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("adjustment").value)
	if(!temp)
	{
		alert(alert_arr.VALID_ADJUSTMENT);
		return false;
	}
	
	//Group - Tax Validation  - not allow negative values
	//We need to validate group tax only if taxtype is group.
	var taxtype=document.getElementById("taxtype").value;
	if(taxtype=="group")
	{
		var tax_count=document.getElementById("group_tax_count").value;
		for(var i=1;i<=tax_count;i++)
		{

			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("group_tax_percentage"+i).value);
			if(!temp)
			{
				alert(alert_arr.VALID_TAX_PERCENT);
				return false;
			}
		}
	}
	
	//Taxes for Shippring and Handling  validation - not allow negative values
		var shtax_count=document.getElementById("sh_tax_count").value;
		for(var i=1;i<=shtax_count;i++)
		{

			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("sh_tax_percentage"+i).value);
			if(!temp)
			{
				alert(alert_arr.VALID_SH_TAX);
				return false;
			}
		}


	return true;    
}

function FindDuplicate()
{
	var max_row_count = document.getElementById('proTab').rows.length;
        max_row_count = eval(max_row_count)-2;//As the table has two header rows, we will reduce two from row length

	var duplicate = false, iposition = '', positions = '', duplicate_products = '';

	var product_id = new Array(max_row_count-1);
	var product_name = new Array(max_row_count-1);
	product_id[1] = getObj("hdnProductId"+1).value;
	product_name[1] = getObj("productName"+1).value;
	for (var i=1;i<=max_row_count;i++)
	{
		iposition = ""+i;
		for(var j=i+1;j<=max_row_count;j++)
		{
			if(i == 1)
			{
				product_id[j] = getObj("hdnProductId"+j).value;
			}
			if(product_id[i] == product_id[j] && product_id[i] != '')
			{
				if(!duplicate) positions = iposition;
				duplicate = true;
				if(positions.search(j) == -1) positions = positions+" & "+j;

				if(duplicate_products.search(getObj("productName"+j).value) == -1)
					duplicate_products = duplicate_products+getObj("productName"+j).value+" \n";
			}
		}
	}
	if(duplicate)
	{
		//alert("You have selected < "+duplicate_products+" > more than once in line items  "+positions+".\n It is advisable to select the product just once but change the Qty. Thank You");
		if(!confirm(alert_arr.SELECTED_MORE_THAN_ONCE+"\n"+duplicate_products+"\n "+alert_arr.WANT_TO_CONTINUE))
			return false;
	}
        return true;
}

function fnshow_Hide(Lay){
    var tagName = document.getElementById(Lay);
   	if(tagName.style.display == 'none')
   		tagName.style.display = 'block';
	else
		tagName.style.display = 'none';
}

function ValidateTax(txtObj)
{
	temp= /^\d+(\.\d\d*)*$/.test(document.getElementById(txtObj).value);
	if(temp == false)
		alert(alert_arr.ENTER_VALID_TAX);
}

function loadTaxes_Ajax(curr_row)
{
	//Retrieve all the tax values for the currently selected product
	new Ajax.Request(
		'index.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'module=Products&action=ProductsAjax&file=InventoryTaxAjax&productid='+document.getElementById("hdnProductId"+curr_row).value+'&curr_row='+curr_row+'&productTotal='+document.getElementById('totalAfterDiscount'+curr_row).innerHTML,
			onComplete: function(response)
				{
					$("tax_div"+curr_row).innerHTML=response.responseText;
					document.getElementById("taxTotal"+curr_row).innerHTML = getObj('hdnTaxTotal'+curr_row).value;
					calcTotal();
				}
		}
	);

}


function fnAddTaxConfigRow(sh){

	var table_id = 'add_tax';
	var td_id = 'td_add_tax';
	var label_name = 'addTaxLabel';
	var label_val = 'addTaxValue';
	var add_tax_flag = 'add_tax_type';

	if(sh != '' && sh == 'sh')
	{
		table_id = 'sh_add_tax';
		td_id = 'td_sh_add_tax';
		label_name = 'sh_addTaxLabel';
		label_val = 'sh_addTaxValue';
		add_tax_flag = 'sh_add_tax_type';
	}
	var tableName = document.getElementById(table_id);
	var prev = tableName.rows.length;
    	var count = rowCnt;

    	var row = tableName.insertRow(0);

	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);

	colone.className = "cellLabel small";
	coltwo.className = "cellText small";

	colone.innerHTML="<input type='text' id='"+label_name+"' name='"+label_name+"' value='"+tax_labelarr.TAX_NAME+"' class='txtBox' onclick=\"this.form."+label_name+".value=''\";/>";
	coltwo.innerHTML="<input type='text' id='"+label_val+"' name='"+label_val+"' value='"+tax_labelarr.TAX_VALUE+"' class='txtBox' onclick=\"this.form."+label_val+".value=''\";/>";

	document.getElementById(td_id).innerHTML="<input type='submit' name='Save' value=' "+tax_labelarr.SAVE_BUTTON+" ' class='crmButton small save' onclick=\"this.form.action.value='TaxConfig'; this.form."+add_tax_flag+".value='true'; this.form.parenttab.value='Settings'; return validateNewTaxType('"+label_name+"','"+label_val+"');\">&nbsp;<input type='submit' name='Cancel' value=' "+tax_labelarr.CANCEL_BUTTON+" ' class='crmButton small cancel' onclick=\"this.form.action.value='TaxConfig'; this.form.module.value='Settings'; this.form."+add_tax_flag+".value='false'; this.form.parenttab.value='Settings';\">";
}

function validateNewTaxType(fieldname, fieldvalue)
{
	if(trim(document.getElementById(fieldname).value)== '')
	{
		alert(alert_arr.VALID_TAX_NAME);
		return false;
	}
	if(trim(document.getElementById(fieldvalue).value)== '')
	{
		alert(alert_arr.CORRECT_TAX_VALUE);
		return false;
	}
	else
	{
		var temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById(fieldvalue).value);
		if(!temp)
		{
			alert(alert_arr.ENTER_POSITIVE_VALUE);
			return false;
		}
	}

	return true;
}

function validateTaxes(countname)
{
	taxcount = eval(document.getElementById(countname).value)+1;

	if(countname == 'tax_count')
	{
		taxprefix = 'tax';
		taxLabelPrefix = 'taxlabel_tax';
	}
	else
	{
		taxprefix = 'shtax';
		taxLabelPrefix = 'taxlabel_shtax';
	}

	for(var i=1;i<=taxcount;i++)
	{
		taxval = document.getElementById(taxprefix+i).value;
		taxLabelVal = document.getElementById(taxLabelPrefix+i).value;
		document.getElementById(taxLabelPrefix+i).value = taxLabelVal.replace(/^\s*|\s*$/g,'').replace(/\s+/g,'');

		if(document.getElementById(taxLabelPrefix+i).value.length == 0)
		{
			alert(alert_arr.LABEL_SHOULDNOT_EMPTY);
			return false
		} 

		//Tax value - numeric validation	
		var temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(taxval);
		if(!temp)
		{
			alert("'"+taxval+"' "+alert_arr.NOT_VALID_ENTRY);
			return false;
		}
	}
	return true;
}



//Function used to add a new product row in PO, SO, Quotes and Invoice
function fnAddProductRow(module,image_path){
	rowCnt++;

	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;
    	var count = eval(prev)-1;//As the table has two headers, we should reduce the count
    	var row = tableName.insertRow(prev);
		row.id = "row"+count;
		row.style.verticalAlign = "top";

	
	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	if(module == "PurchaseOrder"){
		var colfour = row.insertCell(2);
		var colfive = row.insertCell(3);
		var colsix = row.insertCell(4);
		var colseven = row.insertCell(5);
	}
	else{
		var colthree = row.insertCell(2);
		var colfour = row.insertCell(3);
		var colfive = row.insertCell(4);
		var colsix = row.insertCell(5);
		var colseven = row.insertCell(6);
	}
	//Delete link
	colone.className = "crmTableRow small";
	colone.innerHTML='<img src="'+image_path+'delete.gif" border="0" onclick="deleteRow(\''+module+'\','+count+')"><input id="deleted'+count+'" name="deleted'+count+'" type="hidden" value="0">';

	//Product Name with Popup image to select product
	coltwo.className = "crmTableRow small"
	coltwo.innerHTML= '<table border="0" cellpadding="1" cellspacing="0" width="100%"><tr><td class="small"><input id="productName'+count+'" name="productName'+count+'" class="small" style="width: 70%;" value="" readonly="readonly" type="text"><input id="hdnProductId'+count+'" name="hdnProductId'+count+'" value="" type="hidden"><img src="'+image_path+'search.gif" style="cursor: pointer;" onclick="productPickList(this,\''+module+'\','+count+')" align="absmiddle"></td></tr><tr><td class="small" id="setComment'+count+'"><textarea id="comment'+count+'" name="comment'+count+'" class=small style="width:70%;height:40px"></textarea><br>[<a href="javascript:;" onclick="getObj(\'comment'+count+'\').value=\'\'";>'+product_labelarr.CLEAR_COMMENT+'</a>]</td></tr></tbody></table>';	
	
	//Quantity In Stock - only for SO, Quotes and Invoice
	if(module != "PurchaseOrder"){
	colthree.className = "crmTableRow small"
	colthree.innerHTML='<span id="qtyInStock'+count+'">&nbsp;</span>';
	}
	
	//Quantity
	var temp='';
	colfour.className = "crmTableRow small"
	temp='<input id="qty'+count+'" name="qty'+count+'" type="text" class="small " style="width:50px" onfocus="this.className=\'detailedViewTextBoxOn\'" onBlur="settotalnoofrows(); calcTotal(); loadTaxes_Ajax('+count+');';
	if(module == "Invoice")
        {
		temp+='stock_alert('+count+');';
	}
        temp+='" onChange="setDiscount(this,'+count+')" value=""/><br><span id="stock_alert'+count+'"></span>';
	colfour.innerHTML=temp;
	//List Price with Discount, Total after Discount and Tax labels
	colfive.className = "crmTableRow small"
	colfive.innerHTML='<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="right"><input id="listPrice'+count+'" name="listPrice'+count+'" value="0.00" type="text" class="small " style="width:70px" onBlur="calcTotal();setDiscount(this,'+count+');callTaxCalc('+count+'); calcTotal();"/>&nbsp;<img src="'+image_path+'pricebook.gif" onclick="priceBookPickList(this,'+count+')"></td></tr><tr><td align="right" style="padding:5px;" nowrap>		(-)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,\'discount_div'+count+'\',\'discount\','+count+')" >'+product_labelarr.DISCOUNT+'</a> : </b><div class=\"discountUI\" id=\"discount_div'+count+'"><input type="hidden" id="discount_type'+count+'" name="discount_type'+count+'" value=""><table width="100%" border="0" cellpadding="5" cellspacing="0" class="small"><tr><td id="discount_div_title'+count+'" nowrap align="left" ></td><td align="right"><img src="'+image_path+'close.gif" border="0" onClick="fnHidePopDiv(\'discount_div'+count+'\')" style="cursor:pointer;"></td></tr><tr><td align="left" class="lineOnTop"><input type="radio" name="discount'+count+'" checked onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; '+product_labelarr.ZERO_DISCOUNT+'</td><td class="lineOnTop">&nbsp;</td></tr><tr><td align="left"><input type="radio" name="discount'+count+'" onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; % '+product_labelarr.PERCENT_OF_PRICE+' </td><td align="right"><input type="text" class="small" size="2" id="discount_percentage'+count+'" name="discount_percentage'+count+'" value="0" style="visibility:hidden" onBlur="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp;%</td></tr><tr><td align="left" nowrap><input type="radio" name="discount'+count+'" onclick="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();">&nbsp; '+product_labelarr.DIRECT_PRICE_REDUCTION+'</td><td align="right"><input type="text" id="discount_amount'+count+'" name="discount_amount'+count+'" size="5" value="0" style="visibility:hidden" onBlur="setDiscount(this,'+count+'); callTaxCalc('+count+');calcTotal();"></td></tr></table></div></td></tr><tr> <td align="right" style="padding:5px;" nowrap><b>'+product_labelarr.TOTAL_AFTER_DISCOUNT+' :</b></td></tr><tr id="individual_tax_row'+count+'" class="TaxShow"><td align="right" style="padding:5px;" nowrap>(+)&nbsp;<b><a href="javascript:doNothing();" onClick="displayCoords(this,\'tax_div'+count+'\',\'tax\','+count+')" >'+product_labelarr.TAX+' </a> : </b><div class="discountUI" id="tax_div'+count+'"></div></td></tr></table> ';

	//Total and Discount, Total after Discount and Tax details
	colsix.className = "crmTableRow small"
	colsix.innerHTML = '<table width="100%" cellpadding="5" cellspacing="0"><tr><td id="productTotal'+count+'" align="right">&nbsp;</td></tr><tr><td id="discountTotal'+count+'" align="right">0.00</td></tr><tr><td id="totalAfterDiscount'+count+'" align="right">&nbsp;</td></tr><tr><td id="taxTotal'+count+'" align="right">0.00</td></tr></table>';

	//Net Price
	colseven.className = "crmTableRow small";
	colseven.align = "right";
	colseven.style.verticalAlign = "bottom";
	colseven.innerHTML = '<span id="netPrice'+count+'"><b>&nbsp;</b></span>';
	
	//This is to show or hide the individual or group tax
	decideTaxDiv();

	calcTotal();
}

function decideTaxDiv()
{
	var taxtype = document.getElementById("taxtype").value

	calcTotal();

	if(taxtype == 'group')
	{
		//if group tax selected then we have to hide the individual taxes and also calculate the group tax
		hideIndividualTaxes()
		calcGroupTax();
	}
	else if(taxtype == 'individual')
		hideGroupTax()

}

function hideIndividualTaxes()
{
	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//Because the table has two header rows. so we will reduce two from row length

	for(var i=1;i<=max_row_count;i++)
	{
		document.getElementById("individual_tax_row"+i).className = 'TaxHide';
		document.getElementById("taxTotal"+i).style.display = 'none';
	}
	document.getElementById("group_tax_row").className = 'TaxShow';
}

function hideGroupTax()
{
	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//Because the table has two header rows. so we will reduce two from table row length

	for(var i=1;i<=max_row_count;i++)
	{
		document.getElementById("individual_tax_row"+i).className = 'TaxShow';
		document.getElementById("taxTotal"+i).style.display = 'block';
	}
	document.getElementById("group_tax_row").className = 'TaxHide';
}

function setDiscount(currObj,curr_row)
{
	var discount_checks = new Array();

	discount_checks = document.getElementsByName("discount"+curr_row);

	if(discount_checks[0].checked == true)
	{
		document.getElementById("discount_type"+curr_row).value = 'zero';
		document.getElementById("discount_percentage"+curr_row).style.visibility = 'hidden';
		document.getElementById("discount_amount"+curr_row).style.visibility = 'hidden';
		document.getElementById("discountTotal"+curr_row).innerHTML = 0.00;
	}
	if(discount_checks[1].checked == true)
	{
		document.getElementById("discount_type"+curr_row).value = 'percentage';
		document.getElementById("discount_percentage"+curr_row).style.visibility = 'visible';
		document.getElementById("discount_amount"+curr_row).style.visibility = 'hidden';

		var discount_amount = 0.00;
		//This is to calculate the final discount
		if(curr_row == '_final')
		{
			discount_amount = eval(document.getElementById("netTotal").innerHTML)*eval(document.getElementById("discount_percentage"+curr_row).value)/eval(100);
		}
		else//This is to calculate the product discount
		{
			discount_amount = eval(document.getElementById("productTotal"+curr_row).innerHTML)*eval(document.getElementById("discount_percentage"+curr_row).value)/eval(100);
		}

		//Rounded the decimal part of discount amount to two digits
		document.getElementById("discountTotal"+curr_row).innerHTML = roundValue(discount_amount.toString());
	}
	if(discount_checks[2].checked == true)
	{
		document.getElementById("discount_type"+curr_row).value = 'amount';
		document.getElementById("discount_percentage"+curr_row).style.visibility = 'hidden';
		document.getElementById("discount_amount"+curr_row).style.visibility = 'visible';
		//Rounded the decimal part of discount amount to two digits
		document.getElementById("discountTotal"+curr_row).innerHTML = roundValue(document.getElementById("discount_amount"+curr_row).value.toString());
	}

}

//This function is added to call the tax calculation function
function callTaxCalc(curr_row)
{
	//when we change discount or list price, we have to calculate the taxes again before calculate the total
	if(getObj('tax_table'+curr_row))
	{
		tax_count = eval(document.getElementById('tax_table'+curr_row).rows.length-1);//subtract the title tr length
		for(var i=0, j=i+1;i<tax_count;i++,j++)
		{
			var tax_hidden_name = "hidden_tax"+j+"_percentage"+curr_row;
			var tax_name = document.getElementById(tax_hidden_name).value;
			calcCurrentTax(tax_name,curr_row,i);
		}
	}
}

function calcCurrentTax(tax_name, curr_row, tax_row)
{
	//we should calculate the tax amount only for the total After Discount
	var product_total = getObj("totalAfterDiscount"+curr_row).innerHTML
	//var product_total = document.getElementById("productTotal"+curr_row).innerHTML
	var new_tax_percent = document.getElementById(tax_name).value;

	var new_amount_lbl = document.getElementsByName("popup_tax_row"+curr_row);

	//calculate the new tax amount
	var new_tax_amount = eval(product_total)*eval(new_tax_percent)/eval(100);

	//Rounded the decimal part of tax amount to two digits
	new_tax_amount = roundValue(new_tax_amount.toString());

	//assign the new tax amount in the corresponding text box
	new_amount_lbl[tax_row].value = new_tax_amount;

	var tax_total = 0.00;
	for(var i=0;i<new_amount_lbl.length;i++)
	{
		tax_total = tax_total + eval(new_amount_lbl[i].value);
	}
	document.getElementById("taxTotal"+curr_row).innerHTML = roundValue(tax_total);

}

function calcGroupTax()
{
	var group_tax_count = document.getElementById("group_tax_count").value;
	var net_total_after_discount = eval(document.getElementById("netTotal").innerHTML)-eval(document.getElementById("discountTotal_final").innerHTML);
	var group_tax_total = 0.00, tax_amount=0.00;

	for(var i=1;i<=group_tax_count;i++)
	{
		tax_amount = eval(net_total_after_discount)*eval(document.getElementById("group_tax_percentage"+i).value)/eval(100);
		document.getElementById("group_tax_amount"+i).value = tax_amount;
		group_tax_total = eval(group_tax_total) + eval(tax_amount);
	}

	document.getElementById("tax_final").innerHTML = roundValue(group_tax_total);

}

function calcSHTax()
{
	var sh_tax_count = document.getElementById("sh_tax_count").value;
	var sh_charge = document.getElementById("shipping_handling_charge").value;
	var sh_tax_total = 0.00, tax_amount=0.00;

	for(var i=1;i<=sh_tax_count;i++)
	{
		tax_amount = eval(sh_charge)*eval(document.getElementById("sh_tax_percentage"+i).value)/eval(100);
		//Rounded the decimal part of S&H Tax amount to two digits
		document.getElementById("sh_tax_amount"+i).value = roundValue(tax_amount.toString());
		sh_tax_total = eval(sh_tax_total) + eval(tax_amount);
	}

	//Rounded the decimal part of Total S&H Tax amount to two digits
	document.getElementById("shipping_handling_tax").innerHTML = roundValue(sh_tax_total.toString());

	calcTotal();
}

function validateProductDiscounts()
{
	var max_row_count = document.getElementById('proTab').rows.length;
	max_row_count = eval(max_row_count)-2;//As the table has two header rows, we will reduce two from table row length

	for(var i=1;i<=max_row_count;i++)
	{
		//if the row is deleted then avoid validate that row values
		if(document.getElementById("deleted"+i).value == 1)
			continue;

		discount_checks = document.getElementsByName("discount"+i);

		//Percentage selected, so validate the percentage
		if(discount_checks[1].checked == true)
		{
			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("discount_percentage"+i).value);
			if(!temp)
			{
				alert(alert_arr.VALID_DISCOUNT_PERCENT);
				return false;
			}
		}
		if(discount_checks[2].checked == true)
		{
			temp = /^(0|[1-9]{1}\d{0,})(\.(\d{1}\d{0,}))?$/.test(document.getElementById("discount_amount"+i).value);
			if(!temp)
			{
				alert(alert_arr.VALID_DISCOUNT_AMOUNT);
				return false;
			}
		}
	}
	return true;
}

function stock_alert(curr_row)
{
        var stock=getObj("qtyInStock"+curr_row).innerHTML;
        var qty=getObj("qty"+curr_row).value;
        if (!isNaN(qty))
        {
                if(eval(qty) > eval(stock))
                getObj("stock_alert"+curr_row).innerHTML='<font color="red" size="1">'+alert_arr.STOCK_IS_NOT_ENOUGH+'</font>';
                else
                        getObj("stock_alert"+curr_row).innerHTML='';
        }
        else
     getObj("stock_alert"+curr_row).innerHTML='<font color="red" size="1">'+alert_arr.INVALID_QTY+'</font>';
}
