/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/		
		
document.write("<script type='text/javascript' src='include/js/Mail.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");

function copyAddressRight(form) {

	if(typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined')
		form.otherstreet.value = form.mailingstreet.value;

	if(typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined')
		form.othercity.value = form.mailingcity.value;

	if(typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined')
		form.otherstate.value = form.mailingstate.value;

	if(typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined')
		form.otherzip.value = form.mailingzip.value;

	if(typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined')
		form.othercountry.value = form.mailingcountry.value;

	if(typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined')
		form.otherpobox.value = form.mailingpobox.value;
	
	return true;

}

function copyAddressLeft(form) {

	if(typeof(form.otherstreet) != 'undefined' && typeof(form.mailingstreet) != 'undefined')
		form.mailingstreet.value = form.otherstreet.value;

	if(typeof(form.othercity) != 'undefined' && typeof(form.mailingcity) != 'undefined')
		form.mailingcity.value = form.othercity.value;

	if(typeof(form.otherstate) != 'undefined' && typeof(form.mailingstate) != 'undefined')
		form.mailingstate.value = form.otherstate.value;

	if(typeof(form.otherzip) != 'undefined' && typeof(form.mailingzip) != 'undefined')
		form.mailingzip.value =	form.otherzip.value;

	if(typeof(form.othercountry) != 'undefined' && typeof(form.mailingcountry) != 'undefined')
		form.mailingcountry.value = form.othercountry.value;

	if(typeof(form.otherpobox) != 'undefined' && typeof(form.mailingpobox) != 'undefined')
		form.mailingpobox.value = form.otherpobox.value;
	
	return true;

}

	
function toggleDisplay(id){
		
if(this.document.getElementById( id).style.display=='none'){
	this.document.getElementById( id).style.display='inline'
	this.document.getElementById(id+"link").style.display='none';
					
}else{
	this.document.getElementById(  id).style.display='none'
	this.document.getElementById(id+"link").style.display='none';
	}
}

function set_return(product_id, product_name) {
        window.opener.document.EditView.parent_name.value = product_name;
        window.opener.document.EditView.parent_id.value = product_id;
}

function add_data_to_relatedlist_incal(id,name)
{
	var idval = window.opener.document.EditView.contactidlist.value;
	var nameval = window.opener.document.EditView.contactlist.value;
	if(idval != '')
	{
		if(idval.indexOf(id) != -1)
                {
                        window.opener.document.EditView.contactidlist.value = idval;
                        window.opener.document.EditView.contactlist.value = nameval;
                }
                else
                {
                        window.opener.document.EditView.contactidlist.value = idval+';'+id;
			if(name != '')
                	{
				// this has been modified to provide delete option for Contacts in Calendar
				//this function is defined in script.js ------- Jeri
				window.opener.addOption(id,name);
                	}

                }
	}
	else
	{
		window.opener.document.EditView.contactidlist.value = id;
		if(name != '')
		{
			window.opener.addOption(id,name);
		}
		//end
	}
}
function set_return_specific(product_id, product_name) {
        //Used for DetailView, Removed 'EditView' formname hardcoding
        var fldName = getOpenerObj("contact_name");
        var fldId = getOpenerObj("contact_id");
        fldName.value = product_name;
        fldId.value = product_id;
}
//only for Todo
function set_return_toDospecific(product_id, product_name) {
        var fldName = getOpenerObj("task_contact_name");
        var fldId = getOpenerObj("task_contact_id");
        fldName.value = product_name;
        fldId.value = product_id;
}

function submitform(id){
		document.massdelete.entityid.value=id;
		document.massdelete.submit();
}	

function searchMapLocation(addressType)
{
        var mapParameter = '';
        if (addressType == 'Main')
        {
		if(fieldname.indexOf('mailingstreet') > -1)
			mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingstreet')]).innerHTML+' ';
		if(fieldname.indexOf('mailingpobox') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingpobox')]).innerHTML+' ';
		if(fieldname.indexOf('mailingcity') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingcity')]).innerHTML+' ';
		if(fieldname.indexOf('mailingstate') > -1)
                        mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingstate')]).innerHTML+' ';
		if(fieldname.indexOf('mailingcountry') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingcountry')]).innerHTML+' ';
		if(fieldname.indexOf('mailingzip') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('mailingzip')]).innerHTML;
        }
        else if (addressType == 'Other')
        {
		if(fieldname.indexOf('otherstreet') > -1)
			mapParameter = document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('otherstreet')]).innerHTML+' ';
		if(fieldname.indexOf('otherpobox') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('otherpobox')]).innerHTML+' ';
		if(fieldname.indexOf('otherstate') > -1)
                        mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('otherstate')]).innerHTML+' ';
                if(fieldname.indexOf('othercountry') > -1)
			mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('othercountry')]).innerHTML+' ';
                if(fieldname.indexOf('otherzip') > -1)
                        mapParameter = mapParameter + document.getElementById("dtlview_"+fieldlabel[fieldname.indexOf('otherzip')]).innerHTML;
        }
	mapParameter = removeHTMLFormatting(mapParameter);
	window.open('http://maps.google.com/maps?q='+mapParameter,'goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}
