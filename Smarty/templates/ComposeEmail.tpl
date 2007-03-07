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


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$APP.LBL_CHARSET}">
<title>{$MOD.TITLE_COMPOSE_MAIL}</title>
<link REL="SHORTCUT ICON" HREF="include/images/vtigercrm_icon.ico">	
<style type="text/css">@import url("themes/{$THEME}/style.css");</style>
<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script src="include/js/general.js" type="text/javascript"></script>
<script type="text/javascript" src="include/fckeditor/fckeditor.js"></script>
</head>
<body marginheight="0" marginwidth="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<form name="EditView" method="POST" ENCTYPE="multipart/form-data" action="index.php">
<input type="hidden" name="form">
<input type="hidden" name="send_mail">
<input type="hidden" name="contact_id" value="{$CONTACT_ID}">
<input type="hidden" name="user_id" value="{$USER_ID}">
<input type="hidden" name="filename" value="{$FILENAME}">
<input type="hidden" name="old_id" value="{$OLD_ID}">
<input type="hidden" name="module" value="{$MODULE}">
<input type="hidden" name="record" value="{$ID}">
<input type="hidden" name="mode" value="{$MODE}">
<input type="hidden" name="action">
<input type="hidden" name="popupaction" value="create">
<input type="hidden" name="hidden_toid">
<table class="small mailClient" border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
   
   <tr>
	<td colspan=3 >
	<!-- Email Header -->
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="mailClientWriteEmailHeader">
	<tr>
		<td >{$MOD.LBL_COMPOSE_EMAIL}</td>
	</tr>
	</table>
	
	
	</td>
</tr>
	{foreach item=row from=$BLOCKS}
	{foreach item=elements from=$row}
	{if $elements.2.0 eq 'parent_id'}
   <tr>
	<td class="mailSubHeader" align="right"><b>{$MOD.LBL_TO}</b></td>
	<td class="cellText" style="padding: 5px;">
 		<input name="{$elements.2.0}" type="hidden" value="{$IDLISTS}">
		<input type="hidden" name="saved_toid" value="{$TO_MAIL}">
		<textarea id="parent_name" readonly cols="70">{$TO_MAIL}</textarea>
	</td>
	<td class="cellText" style="padding: 5px;" align="left" nowrap>
		<select name="parent_type">
			{foreach key=labelval item=selectval from=$elements.1.0}
				<option value="{$labelval}" {$selectval}>{$APP[$labelval]}</option>
			{/foreach}
		</select>
		&nbsp;
		<span  class="mailClientCSSButton">
		<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=set_return_emails","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
		</span><span class="mailClientCSSButton" ><input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.parent_id.value=''; this.form.hidden_toid.value='';this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
		</span>
	</td>
   </tr>
   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_CC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="ccmail" id ="cc_name" class="txtBox" type="text" value="{$CC_MAIL}" style="width:99%">&nbsp;
	</td>	
	<td valign="top" class="cellLabel" rowspan="4"><div id="attach_cont" class="addEventInnerBox" style="overflow:auto;height:110px;width:100%;position:relative;left:0px;top:0px;"></div>
   </tr>
   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right">{$MOD.LBL_BCC}</td>
	<td class="cellText" style="padding: 5px;">
		<input name="bccmail" id="bcc_name" class="txtBox" type="text" value="{$BCC_MAIL}" style="width:99%">&nbsp;
	</td>
   </tr>
	{elseif $elements.2.0 eq 'subject'}
   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap><font color="red">*</font>{$elements.1.0}  :</td>
        {if $WEBMAIL eq 'true'}
                <td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$SUBJECT}" id="{$elements.2.0}" style="width:99%"></td>
        {else}
                <td class="cellText" style="padding: 5px;"><input type="text" class="txtBox" name="{$elements.2.0}" value="{$elements.3.0}" id="{$elements.2.0}" style="width:99%"></td>
        {/if}
   </tr>
	{elseif $elements.2.0 eq 'filename'}

   <tr>
	<td class="mailSubHeader" style="padding: 5px;" align="right" nowrap>{$elements.1.0}  :</td>
	<td class="cellText" style="padding: 5px;">
		<input name="{$elements.2.0}"  type="file" class="small txtBox" value="" size="78"/>
		<div id="attach_temp_cont" style="display:none;">
		<table class="small" width="100% ">
		{foreach item="attach_files" key="attach_id" from=$elements.3}	
			<tr id="row_{$attach_id}"><td width="90%">{$attach_files}</td><td><img src="{$IMAGE_PATH}no.gif" onClick="delAttachments({$attach_id})" alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;"></td></tr>	
		{/foreach}	
		</table>	
		</div>	
		{$elements.3.0}
	</td>
   </tr>
   <tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}  ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
    </tr>
	{elseif $elements.2.0 eq 'description'}
   <tr>
	<td colspan="3" align="center" valign="top" height="320">
        {if $WEBMAIL eq 'true'}
                <textarea style="display: none;" class="detailedViewTextBox" name="description" cols="90" rows="8">{$DESCRIPTION}</textarea>
        {else}
                <textarea style="display: none;" class="detailedViewTextBox" id="description" name="description" cols="90" rows="16">{$elements.3.0}</textarea>        {/if}
	</td>
   </tr>
	{/if}
	{/foreach}
	{/foreach}

   <tr>
	<td colspan="3" class="mailSubHeader" style="padding: 5px;" align="center">
		 <input title="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_KEY}" class="crmbutton small edit" onclick="window.open('index.php?module=Users&action=lookupemailtemplates','emailtemplate','top=100,left=200,height=400,width=500,menubar=no,addressbar=no,status=yes')" type="button" name="button" value=" {$APP.LBL_SELECTEMAILTEMPLATE_BUTTON_LABEL}  ">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" onclick="return email_validate(this.form,'save');" type="button" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL} " >&nbsp;
		<input name="{$MOD.LBL_SEND}" value=" {$APP.LBL_SEND} " class="crmbutton small save" type="button" onclick="return email_validate(this.form,'send');">&nbsp;
		<input name="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="crmbutton small cancel" type="button" onClick="window.close()">
	</td>
   </tr>
</tbody>
</table>
</form>
</body>
<script>
var cc_err_msg = '{$MOD.LBL_CC_EMAIL_ERROR}';
var no_rcpts_err_msg = '{$MOD.LBL_NO_RCPTS_EMAIL_ERROR}';
var bcc_err_msg = '{$MOD.LBL_BCC_EMAIL_ERROR}';
var conf_mail_srvr_err_msg = '{$MOD.LBL_CONF_MAILSERVER_ERROR}';
{literal}
function email_validate(oform,mode)
{
	if(oform.parent_name.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		//alert('No recipients were specified');
				alert(no_rcpts_err_msg);
		return false;
	}
	if(document.EditView.ccmail.value.length >= 1)
	{
		var str = document.EditView.ccmail.value;
		arr = new Array();
		arr = str.split(",");
		for(var i=0; i<=arr.length-1; i++)
		{
			if(arr[i] != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(arr[i]))
			{
				//alert("Your CC Email Id for "+ arr[i] +" is not correct");
				alert(cc_err_msg);
				return false;
			}
		}
	}
	
	if(document.EditView.bccmail.value.length >= 1)
	{
		var str = document.EditView.bccmail.value;
		arr = new Array();
		arr = str.split(",");
		for(var i=0; i<=arr.length-1; i++)
		{
			if(arr[i] != "" && !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,4})+$/.test(arr[i]))
			{
				//alert("Your BCC Email Id for "+ arr[i] +" is not correct");
				alert(bcc_err_msg);

				return false;	
			}
		}	
	}
	if(oform.subject.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		if(email_sub = prompt('You did not specify a subject from this email. If you would like to provide one, please type it now','(no-Subject)'))
		{
			oform.subject.value = email_sub;
		}else
		{
			return false;
		}
	}
	if(mode == 'send')
	{
		server_check()	
	}else
	{
		oform.action.value='Save';
		oform.submit();
	}
}
function server_check()
{
	var oform = window.document.EditView;
        new Ajax.Request(
        	'index.php',
                {queue: {position: 'end', scope: 'command'},
                	method: 'post',
                        postBody:"module=Emails&action=EmailsAjax&file=Save&ajax=true&server_check=true",
			onComplete: function(response) {
			if(response.responseText == 'SUCESS')
			{
				oform.send_mail.value='true';
				oform.action.value='Save';
				oform.submit();
			}else
			{
				//alert('Please Configure Your Mail Server');
				alert(conf_mail_srvr_err_msg);
				return false;
			}
               	    }
                }
        );
}
$('attach_cont').innerHTML = $('attach_temp_cont').innerHTML;
function delAttachments(id)
{
    new Ajax.Request(
        'index.php',
        {queue: {position: 'end', scope: 'command'},
            method: 'post',
            postBody: 'module=Contacts&action=ContactsAjax&file=DelImage&attachmodule=Emails&recordid='+id,
            onComplete: function(response)
            {
		Effect.Fade('row_'+id);	                
            }
        }
    );

}
{/literal}
</script>
<script type="text/javascript" defer="1">
var oFCKeditor = null;
oFCKeditor = new FCKeditor( "description" ,"100%","370") ;
oFCKeditor.BasePath   = "include/fckeditor/" ;
oFCKeditor.ReplaceTextarea();
</script>
</html>
