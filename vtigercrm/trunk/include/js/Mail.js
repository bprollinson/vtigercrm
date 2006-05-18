/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
//added by raju for emails

function eMail(module,oButton)
{
    x = document.massdelete.selected_id.length;
	var viewid = document.massdelete.viewname.value;

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
				var idstring= new Array();
                xx = 0;
                for(i = 0; i < x ; i++)
                {
                        if(document.massdelete.selected_id[i].checked)
                        {
                                idstring[xx]= document.massdelete.selected_id[i].value;
                                xx++
                        }
                }
                if (xx != 0)
                {
                        document.massdelete.idlist.value=idstring.join(':');
                }
                else
                {
                        alert("Please select atleast one entity");
                        return false;
                }
        }
	allids = document.massdelete.idlist.value;	
	fnvshobj(oButton,'sendmail_cont');
	sendmail(module,allids);
}


function massMail(module)
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
        document.massdelete.action="index.php?module=CustomView&action=SendMailAction&return_module="+module+"&return_action=index&viewname="+viewid;
}

//added by rdhital for better emails
function set_return_emails(entity_id,email_id,parentname,emailadd){
	if(emailadd != '')
	{
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+email_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+emailadd+'>; ';
		window.opener.document.EditView.hidden_toid.value = emailadd+','+window.opener.document.EditView.hidden_toid.value;
		window.close();
	}else
	{
		alert('The Record '+parentname+' does\'t have email id' );
		return false;
	}
}	
//added by raju for emails

function validate_sendmail(idlist,module)
{
	var j=0;
	var chk_emails = document.SendMail.elements.length;
	var oFsendmail = document.SendMail.elements
	email_type = new Array();
	for(var i=0 ;i < chk_emails ;i++)
	{
		if(oFsendmail[i].type != 'button')
		{
			if(oFsendmail[i].checked != false)
			{
				email_type [j++]= oFsendmail[i].value;
			}
		}
	}
	if(email_type != '')
	{
		var field_lists = email_type.join(':');
		var url= 'index.php?module=Emails&action=EmailsAjax&pmodule='+module+'&file=EditView&sendmail=true&idlist='+idlist+'&field_lists='+field_lists;
		openPopUp('xComposeEmail',this,url,'createemailWin',797,652,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
		fninvsh('roleLay');
		return true;
	}
	else
	{
		alert('Please Select a mailid');
	}
}
function sendmail(module,idstrings)
{
	var ajaxObj = new Ajax(ajaxSendmailResponse);
	var urlstring ="module=Emails&return_module="+module+"&action=EmailsAjax&file=mailSelect&idlist="+idstrings;
	ajaxObj.process("index.php?",urlstring);
}
function ajaxSendmailResponse(response)
{
	getObj('sendmail_cont').innerHTML=response.responseText;
}
	
