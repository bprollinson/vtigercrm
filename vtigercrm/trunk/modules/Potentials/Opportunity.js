/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/



function showDefaultCustomView(selectView)
{
		show("status");
		var ajaxObj = new Ajax(ajaxSaveResponse);
		var viewName = selectView.options[selectView.options.selectedIndex].value;
		var urlstring ="module=Potentials&action=PotentialsAjax&file=ListView&ajax=true&viewname="+viewName;
	    ajaxObj.process("index.php?",urlstring);
}

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
		if(confirm("Are you sure you want to delete the selected "+xx+" records ?"))
		{
			show("status");
			var ajaxObj = new Ajax(ajaxSaveResponse);
			var urlstring ="module=Users&action=massdelete&return_module=Potentials&viewname="+viewid+"&idlist="+idstring;
	    	ajaxObj.process("index.php?",urlstring);
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
        
	//getOpenerObj used for DetailView 
        var fldName = getOpenerObj("potential_name");
        var fldId = getOpenerObj("potential_id");
        fldName.value = product_name;
        fldId.value = product_id;
	//window.opener.document.EditView.potential_name.value = product_name;
        //window.opener.document.EditView.potential_id.value = product_id;
}
function add_data_to_relatedlist(entity_id,recordid) 
{
	opener.document.location.href="index.php?module=Emails&action=updateRelations&destination_module=Contacts&entityid="+entity_id+"&parid="+recordid;
}
function set_return_address(potential_id, potential_name, account_id, account_name, bill_street, ship_street, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country,bill_pobox,ship_pobox) {
        window.opener.document.EditView.potential_name.value = potential_name;
        window.opener.document.EditView.potential_id.value = potential_id;
        window.opener.document.EditView.account_name.value = account_name;
        window.opener.document.EditView.account_id.value = account_id;
        window.opener.document.EditView.bill_street.value = bill_street;
        window.opener.document.EditView.ship_street.value = ship_street;
        window.opener.document.EditView.bill_city.value = bill_city;
        window.opener.document.EditView.ship_city.value = ship_city;
        window.opener.document.EditView.bill_state.value = bill_state;
        window.opener.document.EditView.ship_state.value = ship_state;
        window.opener.document.EditView.bill_code.value = bill_code;
        window.opener.document.EditView.ship_code.value = ship_code;
        window.opener.document.EditView.bill_country.value = bill_country;
        window.opener.document.EditView.ship_country.value = ship_country;
        window.opener.document.EditView.bill_pobox.value = bill_pobox;
        window.opener.document.EditView.ship_pobox.value = ship_pobox;
}

