/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/


function verify_data(form) {
	if(! form.createpotential.checked == true)
	{
        	if (form.potential_name.value == "")
		{
                	alert("Opportunity Name field cannot be empty");
			return false;	
		}
		if (form.closedate.value == "")
		{
                	alert("Close Date field cannot be empty");
			return false;	
		}
		x = dateValidate('closedate','Potential Close Date','GECD');
		intval= intValidate('potential_amount','Potential Amount');

		if(!x)
		{
			return false;
		}
		if(!intval)
		{
			return false;
		}
        }
	else
	{	

		return true;
	}
	
}

function togglePotFields(form)
{
	if (form.createpotential.checked == true)
	{
		form.potential_name.disabled = true;
		form.closedate.disabled = true;
		form.potential_amount.disabled = true;
		form.potential_sales_stage.disabled = true;
		
	}
	else
	{
		form.potential_name.disabled = false;
		form.closedate.disabled = false;
		form.potential_amount.disabled = false;
		form.potential_sales_stage.disabled = false;
		form.potential_sales_stage.value="";
	}	

}

function showDefaultCustomView(selectView)
{
		show("status");
		var ajaxObj = new Ajax(ajaxSaveResponse);
		var viewName = selectView.options[selectView.options.selectedIndex].value;
		var urlstring ="module=Leads&action=LeadsAjax&file=ListView&ajax=true&viewname="+viewName;
	    ajaxObj.process("index.php?",urlstring);
	
}
//code added by raju for better emailing
function eMail()
{
    x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;
	idstring = "";

        if ( x == undefined)
        {

                if (document.massdelete.selected_id.checked)
                {
                        document.massdelete.idlist.value=document.massdelete.selected_id.value;
                }
                else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
        else
        {
                xx = 0;
                for(i = 0; i < x ; i++)
                {
                        if(document.massdelete.selected_id[i].checked)
                        {
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                                xx++
                        }
                }
                if (xx != 0)
                {
                        document.massdelete.idlist.value=idstring;
                }
                else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
        document.massdelete.action="index.php?module=Emails&action=SelectEmails&return_module=Leads&return_action=index";
}


//end of code added by raju
function massDelete()
{

	x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;
	idstring = "";

	if ( x == undefined)
	{
	
		if (document.massdelete.selected_id.checked)
		{
			document.massdelete.idlist.value=document.massdelete.selected_id.value;
		}
		else 
		{
			alert("Please select atleast one entity");
			return false;
		}
	}
	else
	{
		xx = 0;
		for(i = 0; i < x ; i++)
		{
			if(document.massdelete.selected_id[i].checked)
			{
				idstring = document.massdelete.selected_id[i].value +";"+idstring
				xx++;	
			}
		}
		if (xx != 0)
		{
			document.massdelete.idlist.value=idstring;
		}
		else
		{
			alert("Please select atleast one entity");
			return false;
		}
	}
	if(confirm("Are you sure you want to delete the selected "+xx+" records ?"))
	{
		show("status");
		var ajaxObj = new Ajax(ajaxSaveResponse);
		var urlstring ="module=Users&action=massdelete&return_module=Leads&viewname="+viewid+"&idlist="+idstring;
	    ajaxObj.process("index.php?",urlstring);
	}
	else
	{
		return false;
	}
}

function massMail()
{

        x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;
	idstring = "";
	
        if ( x == undefined)
        {

                if (document.massdelete.selected_id.checked)
                {
                        document.massdelete.idlist.value=document.massdelete.selected_id.value;
                }
                else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
        else
        {
                xx = 0;
                for(i = 0; i < x ; i++)
                {
                        if(document.massdelete.selected_id[i].checked)
                        {
                                idstring = document.massdelete.selected_id[i].value +";"+idstring
                                xx++
                        }
                }
                if (xx != 0)
                {
                        document.massdelete.idlist.value=idstring;
                }
                else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
        document.massdelete.action="index.php?module=CustomView&action=SendMailAction&return_module=Leads&return_action=index&viewname="+viewid;
}

function changeStatus()
{
	x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;

	idstring = "";
	if ( x == undefined)
	{
	
		if (document.massdelete.selected_id.checked)
		{
			document.massdelete.idlist.value=document.massdelete.selected_id.value;
		}
		else 
		{
			alert("Please select atleast one entity ");
			return false;
		}
	}
	else
	{
		xx = 0;
		for(i = 0; i < x ; i++)
		{
			if(document.massdelete.selected_id[i].checked)
			{
				idstring = document.massdelete.selected_id[i].value +";"+idstring
			xx++	
			}
		}
		if (xx != 0)
		{
			document.massdelete.idlist.value=idstring;
		}
		else
		{
			alert("Please select atleast one entity");
			return false;
		}
	}	

	document.massdelete.action="index.php?module=Users&action=massChangeStatus&parenttab=Sales&viewname="+viewid;
}

//to merge to a list of leads
function massMerge()
{

	x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;
	idstring = "";

	if ( x == undefined)
	{
	
		if (document.massdelete.selected_id.checked)
		{
			document.massdelete.idlist.value=document.massdelete.selected_id.value;
		}
		else 
		{
			alert("Please select atleast one entity");
			return false;
		}
	}
	else
	{
		xx = 0;
		for(i = 0; i < x ; i++)
		{
			if(document.massdelete.selected_id[i].checked)
			{
				idstring = document.massdelete.selected_id[i].value +";"+idstring
			xx++	
			}
		}
		if (xx != 0)
		{
			document.massdelete.idlist.value=idstring;
		}
		else
		{
			alert("Please select atleast one entity");
			return false;
		}
	}	
	if(getObj('selectall').checked == true)
		{
				getObj('idlist').value = getObj('allids').value;
		}
	document.massdelete.action="index.php?module=Leads&action=Merge&return_module=Leads&return_action=index&parenttab=Sales&viewname="+viewid;
}

//added for massemail by raju
function set_return_emails(entity_id,email_id,parentname,emailadd){
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+email_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+emailadd+'>; ';
}		

function set_return(product_id, product_name) {
        window.opener.document.EditView.parent_name.value = product_name;
        window.opener.document.EditView.parent_id.value = product_id;
}
function set_return_specific(product_id, product_name) {
        //Used for DetailView, Removed 'EditView' formname hardcoding
        var fldName = getOpenerObj("lead_name");
        var fldId = getOpenerObj("lead_id");
        fldName.value = product_name;
        fldId.value = product_id;
        //window.opener.document.EditView.lead_name.value = product_name;
        //window.opener.document.EditView.lead_id.value = product_id;
}
function add_data_to_relatedlist(entity_id,recordid) {
	
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=leads&entityid="+entity_id+"&parid="+recordid;
}
//added by rdhital/Raju for emails
function submitform(id){
		document.massdelete.entityid.value=id;
		document.massdelete.submit();
}	

function searchMapLocation(addressType)
{
        var mapParameter = '';
        if (addressType == 'Main')
        {
                mapParameter = document.getElementById("dtlview_Street").innerHTML+' '
                           +document.getElementById("dtlview_Po Box").innerHTML+' '
                           +document.getElementById("dtlview_City").innerHTML+' '
                           +document.getElementById("dtlview_State").innerHTML+' '
                           +document.getElementById("dtlview_Country").innerHTML+' '
                           +document.getElementById("dtlview_Postal Code").innerHTML
        }
        window.open('http://maps.google.com/maps?q='+mapParameter,'goolemap','height=450,width=700,resizable=no,titlebar,location,top=200,left=250');
}


