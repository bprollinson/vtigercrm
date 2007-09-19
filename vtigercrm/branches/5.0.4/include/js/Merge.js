/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

//to merge a list of acounts with a template
function massMerge(module)
{
       var select_options  =  document.getElementsByName('selected_id');
	var x = select_options.length;
	var viewid =getviewId();		
	idstring = "";

	if(getObj('mergefile').value == '')
	{
	         alert(alert_arr.SELECT_TEMPLATE_TO_MERGE);
           	 return false;   
        }
	xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked)
		{
			idstring = select_options[i].value +";"+idstring
				xx++
		}
	}
	if (xx != 0)
	{
		document.getElementById('idlist').value=idstring;
	}
	else
	{
		alert(alert_arr.SELECT);
		return false;
	}
        
	document.massdelete.action.value = 'Merge';
	document.getElementById('massdelete').action="index.php?module="+module+"&action=Merge&return_module="+module+"&return_action=index";
}
