/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
function change(obj,divid)
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
	fnvshobj(obj,divid);
}
function massDelete(module)
{
        x = document.massdelete.selected_id.length;
		var viewid = document.massdelete.viewname.value;
        idstring = "";

        if ( x == undefined)
        {

                if (document.massdelete.selected_id.checked)
                {
                        idstring = document.massdelete.selected_id.value+':';
                		xx = 1;
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
			var ajaxObj = new VtigerAjax(ajaxSaveResponse);
			var urlstring ="module=Users&action=massdelete&return_module="+module+"&viewname="+viewid+"&idlist="+idstring;
	    	ajaxObj.process("index.php?",urlstring);
		}
		else
		{
			return false;
		}

}

function showDefaultCustomView(selectView,module)
{

		show("status");
		var ajaxObj = new VtigerAjax(ajaxSaveResponse);
		var viewName = selectView.options[selectView.options.selectedIndex].value;
		var urlstring ="module="+module+"&action="+module+"Ajax&file=ListView&ajax=true&start=1&viewname="+viewName;
	    ajaxObj.process("index.php?",urlstring);
}


function getListViewEntries_js(module,url)
{
        show("status");
        var ajaxObj = new VtigerAjax(ajaxSaveResponse);
        var urlstring ="module="+module+"&action="+module+"Ajax&file=index&ajax=true&"+url;
	if(document.getElementById('search_url').value!='')
        	urlstring = urlstring+document.getElementById('search_url').value;
        ajaxObj.process("index.php?",urlstring);

}

