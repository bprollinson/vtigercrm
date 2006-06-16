/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/


document.write("<script type='text/javascript' src='include/js/Mail.js'></"+"script>");
document.write("<script type='text/javascript' src='include/js/Merge.js'></"+"script>");
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

//Function added for Mass select in Popup - Philip
function SelectAll()
{

        x = document.selectall.selected_id.length;
        var entity_id = window.opener.document.getElementById('parent_id').value
        var module = window.opener.document.getElementById('return_module').value
        document.selectall.action.value='updateRelations'
        idstring = "";

        if ( x == undefined)
        {

                if (document.selectall.selected_id.checked)
                {
                        document.selectall.idlist.value=document.selectall.selected_id.value;
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
                        if(document.selectall.selected_id[i].checked)
                        {
                                idstring = document.selectall.selected_id[i].value +";"+idstring
                        xx++
                        }
                }
                if (xx != 0)
                {
                        document.selectall.idlist.value=idstring;
                }
		else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
        if(confirm("Are you sure you want to add the selected "+xx+" records ?"))
        {
                opener.document.location.href="index.php?module="+module+"&parentid="+entity_id+"&action=updateRelations&destination_module=Leads&idlist="+idstring;
                self.close();
        }
        else
        {
                return false;
        }
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


