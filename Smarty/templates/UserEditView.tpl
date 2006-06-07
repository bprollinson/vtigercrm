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
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/ColorPicker2.js"></script>
<style type="text/css">@import url(themes/blue/style.css);</style>

<script language="JavaScript" type="text/javascript">

 	var cp2 = new ColorPicker('window');
	
function pickColor(color)
{ldelim}
	ColorPicker_targetInput.value = color;
        ColorPicker_targetInput.style.backgroundColor = color;
{rdelim}	

function openPopup(){ldelim}
		window.open("index.php?module=Users&action=UsersAjax&file=RolePopup&parenttab=Settings","roles_popup_window","height=425,width=640,toolbar=no,menubar=no,dependent=yes,resizable =no");
	{rdelim}	
</script>	

<script language="javascript">
function check_duplicate()
{ldelim}
	var user_name = $('user_name').value;
	new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: 'module=Users&action=UsersAjax&file=Save&ajax=true&dup_check=true&userName='+user_name,
                        onComplete: function(response) {ldelim}
				if(response.responseText == 'SUCCESS')
			                document.EditView.submit();
       				else
			                alert(response.responseText);
                        {rdelim}
                {rdelim}
        );

{rdelim}

</script>



<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
{if $PARENTTAB neq ''}	
	{include file='SettingsMenu.tpl'}
{/if}
<td width="75%" valign="top">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr><td align="left">
			<table width="100%" cellpadding="5" cellspacing="0" border="0">
			<tr>
			<td colspan="2" style="padding:5px;">
				<span class="lvtHeaderText">
					{if $PARENTTAB neq ''}	
					<b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> > {$MOD.LBL_USER_MANAGEMENT} > {$MOD.LBL_USERS}</b></span>
					{else}
                                            <span class="lvtHeaderText">
                                            <b>{$APP.LBL_MY_PREFERENCES}</b>
                                            </span>
                                        {/if}
				<hr noshade="noshade" size="1" />
			</td>
		  </tr>
		<tr>
											<td width="5%"><img src="{$IMAGE_PATH}user.gif" align="absmiddle"></td>
										{if $MODE eq 'edit'}
												<td width="95%"><span class="genHeaderGrayBig">{$USER_NAME}</span><br>
												<b class="small">{$UMOD.LBL_EDIT_VIEW} {$FIRST_NAME} {$LAST_NAME}</b>
											</td>
										{else}
												<td width="95%"><span class="genHeaderGrayBig">{$UMOD.LBL_NEW_USER_BUTTON_LABEL}</span><br>
												<b class="small">{$UMOD.LBL_CREATE_NEW_USER}</b>
											</td>
										{/if}
											
									</tr>
							</table>
	</td></tr>
	
	<tr><td class="padTab" align="left">
		<form name="EditView" method="POST" action="index.php" ENCTYPE="multipart/form-data">
			<input type="hidden" name="module" value="Users">
			<input type="hidden" name="record" value="{$ID}">
			<input type="hidden" name="mode" value="{$MODE}">
			<input type='hidden' name='parenttab' value='{$PARENTTAB}'>
			<input type="hidden" name="activity_mode" value="{$ACTIVITYMODE}">
			<input type="hidden" name="action">
			<input type="hidden" name="return_module" value="{$RETURN_MODULE}">
			<input type="hidden" name="return_id" value="{$RETURN_ID}">
			<input type="hidden" name="return_action" value="{$RETURN_ACTION}">			
			<input type="hidden" name="tz" value="Europe/Berlin">			
			<input type="hidden" name="holidays" value="de,en_uk,fr,it,us,">			
			<input type="hidden" name="workdays" value="0,1,2,3,4,5,6,">			
			<input type="hidden" name="namedays" value="">			
			<input type="hidden" name="weekstart" value="1">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">

		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2">
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
		  <tr>
			<td>
			  <table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
			  <tr>
			     <td class="dvtTabCache" width="10" nowrap="nowrap">&nbsp;</td>
			        <td id="prof" width="25%" align="center" nowrap="nowrap" class="dvtSelectedCell" onclick="fnVis('prof')" ><b>{$UMOD.LBL_USER_LOGIN_ROLE}</b></td>
				 <td id="more" width="25%" align="center" nowrap="nowrap" class="dvtUnSelectedCell" onclick="fnVis('more')"><b>{$UMOD.LBL_USER_MORE_INFN}</b></td>
				 <td id="addr" width="25%" align="center" nowrap="nowrap" class="dvtUnSelectedCell" onclick="fnVis('addr')"><b>{$UMOD.LBL_USER_ADDR_INFN}</b></td>
				<td class="dvtTabCache" nowrap="nowrap" width="10">&nbsp;</td>
			  </tr>
			  </table>
			</td>
		  </tr>
			<tr>
			    <td align="left" valign="top">
			        <div id="mnuTab">
			             <table class="dvtContentSpace" border="0" cellpadding="0" cellspacing="0" width="100%">
				        <tr><td height="35">&nbsp;</td></tr>
					<tr><td align="left">
					   <table width="99%"  border="0" cellspacing="0" cellpadding="5" align="center" class="small">
  		                           <tr>
						{if $MODE eq 'edit'}
					           <td colspan="4" class="detailedViewHeader"><b>{$APP.LBL_EDIT} {$UMOD.LBL_USER_INFORMATION}</b></td>
						{else}
					           <td colspan="4" class="detailedViewHeader"><b>{$UMOD.LBL_NEW_FORM_TITLE}</b></td>
						{/if}	
					   </tr>  
				<tr>
				    <td class="dvtCellLabel" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_USER_NAME} </td>
				    <td class="dvtCellInfo"><input type="text" id="user_name" name="user_name" value='{$USER_NAME}' class="detailedViewTextBox"  onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"{$USERNAME_READONLY} /></td>
				     <td class="dvtCellLabel" align="right">{$UMOD.LBL_ADMIN} </td>
                                     <td class="dvtCellInfo"><input type="checkbox" name="is_admin" {$DISABLED} {$IS_ADMIN}/></td>
				</tr>
				{if $MODE neq 'edit'}
				<tr>
				    <td class="dvtCellLabel" align="right" width="20%"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_PASSWORD} </td>
				    <td class="dvtCellInfo" width="20%"><input name="new_password" type="password" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
				    <td class="dvtCellLabel" width="20%" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_CONFIRM_PASSWORD} </td>
				    <td class="dvtCellInfo" width="20%"><input name="confirm_new_password" type="password" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
			        </tr>
				{/if}	
			        <tr>
				    <td class="dvtCellLabel" align="right">{$UMOD.LBL_FIRST_NAME} </td>
				    <td class="dvtCellInfo"><input type="text" name="first_name" value='{$FIRST_NAME}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
				    <td class="dvtCellLabel" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_LAST_NAME}</td>
				    <td class="dvtCellInfo"><input type="text" name="last_name" value='{$LAST_NAME}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
				 </tr>
				 <tr>
				    <td class="dvtCellLabel" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_USER_ROLE}</td>
				    <td class="dvtCellInfo"><!-- {$USER_ROLE} -->
							<input type="text" name="role_name"  id="role_name" value="{$ROLENAME}" class="txtBox" readonly="readonly">&nbsp;
							{$ROLEPOPUPBUTTON}
							<input type="hidden" name="user_role" id="user_role" value="{$USERROLE}">
					</td>


				   <td class="dvtCellLabel" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_EMAIL}</td>
                                    <td class="dvtCellInfo"><input type="text" name="email1" value='{$EMAIL1}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
			         </tr>
				 <tr>

				  <td class="dvtCellLabel" align="right"><span class="style1"><font color='red'>{$APP.LBL_REQUIRED_SYMBOL}</font></span>{$UMOD.LBL_STATUS}</td>
				 {$USER_STATUS_OPTIONS}
				<td class="dvtCellInfo" colspan="2" >&nbsp;</td>	
				 </tr>
				 <tr><td colspan="4" class="dvtCellInfo" height="30">&nbsp;</td></tr>
				 <tr>
				    <td class="dvtCellLabel" align="right">{$UMOD.LBL_ACTIVITY_VIEW}</td>
				    <td class="dvtCellInfo">{$ACTIVITY_VIEW}</td>
				    <td class="dvtCellLabel" align="right">{$UMOD.LBL_LEAD_VIEW}</td>
				    <td span class="dvtCellInfo">{$LEAD_VIEW}</td>
				 </tr>
				 <tr>
				   <td class="dvtCellLabel" align="right">{$UMOD.LBL_CURRENCY_NAME}</td>
                                   <td span class="dvtCellInfo">{$CURRENCY_NAME}</td>
				   <td class="dvtCellLabel" align="right"></td>
			 	   <td class="dvtCellInfo">&nbsp;</td>
				 </tr>
				 <tr><td colspan="4" height="30">&nbsp;</td></tr>
				 </table>
				 </td></tr>
				 </table>
				  </div>
					<div id="mnuTab1">
					  <table class="dvtContentSpace" border="0" cellpadding="0" cellspacing="0" width="100%">
					   <tr><td height="35">&nbsp;</td></tr>
					   <tr><td align="left">
					   <table width="99%"  border="0" cellspacing="0" cellpadding="5" align="center" class="small">
					     <tr>
						{if $MODE eq 'edit'}
					           <td colspan="4" class="detailedViewHeader"><b>{$APP.LBL_EDIT} {$UMOD.LBL_USER_INFORMATION}</b></td>
						{else}
					           <td colspan="4" class="detailedViewHeader"><b>{$UMOD.LBL_NEW_FORM_TITLE}</b></td>
						{/if}	
					     </tr>  
					     <tr>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_TITLE}</td>
						<td class="dvtCellInfo"><input type="text" name="title" value='{$TITLE}' class="detailedViewTextBox"  onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" /></td>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_OFFICE_PHONE}</td>
         					<td class="dvtCellInfo"><input type="text" name="phone_work" value='{$PHONE_WORK}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
					     </tr>
					     <tr>
						<td class="dvtCellLabel" align="right" width="20%">{$UMOD.LBL_DEPARTMENT}</td>
						<td class="dvtCellInfo" width="20%"><input type="text" name="department" value='{$DEPARTMENT}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
						<td class="dvtCellLabel" width="20%" align="right">{$UMOD.LBL_MOBILE_PHONE}</td>

						<td class="dvtCellInfo" width="20%"><input type="text" value='{$PHONE_MOBILE}' name="phone_mobile" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
					     </tr>
					     <tr>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_REPORTS_TO}</td>
						<td class="dvtCellInfo" width="20%"><input readonly name='reports_to_name' class="small" type="text" value='{$REPORTS_TO_NAME}'><input name='reports_to_id' type="hidden" value='{$REPORTS_TO_ID}'>&nbsp;<input title="Change [Alt+C]" accessKey="C" type="button" class="small" value='{$UMOD.LBL_CHANGE}' name=btn1 LANGUAGE=javascript onclick='return window.open("index.php?module=Users&action=Popup&form=UsersEditView&form_submit=false","test","width=640,height=522,resizable=0,scrollbars=0");'></td>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_OTHER_PHONE}</td>
						<td class="dvtCellInfo"><input type="text" name="phone_other" value='{$PHONE_OTHER}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
					     </tr>
					     <tr>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_OTHER_EMAIL}</td>
						<td class="dvtCellInfo"><input type="text" name="email2" value='{$EMAIL2}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_FAX}</td>
						<td class="dvtCellInfo"><input type="text" name="phone_fax" value='{$PHONE_FAX}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
					     </tr>
					     <tr>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_YAHOO_ID}</td>
						<td class="dvtCellInfo"><input type="text" name="yahoo_id" value='{$YAHOO_ID}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_HOME_PHONE}</td>
						<td class="dvtCellInfo"><input type="text" name="phone_home" value='{$PHONE_HOME}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
					     </tr>
						
					     <tr>
					     <td class="dvtCellLabel" align="right">{$UMOD.LBL_USER_IMAGE}</td
>
					<td class="dvtCellInfo"><input name="imagename" value="" type="file" class="small"></td>
					<td class="dvtCellLabel" align="right">{$UMOD.LBL_DATE_FORMAT}</td>
                    <td class="dvtCellInfo" width="30%">{$DATE_FORMAT}</td>
				       	</tr>	
						
						
						 <tr><td class="dvtCellLabel" align="right">
						 {$UMOD.LBL_TAG_CLOUD}
						 </td>
						 <td colspan=3 class="dvtCellInfo"><input name='tagcloud' type="text" size='40' maxlength='250' value="{$CLOUD_TAG}" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'"></td>
						 </tr>
					     <tr>
					    <td class="dvtCellLabel" align="right">{$UMOD.LBL_SIGNATURE}</td>
						<td class="dvtCellInfo"><textarea name="signature" rows="3" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" >{$SIGNATURE}</textarea></td>
						<td class="dvtCellLabel" align="right">{$UMOD.LBL_NOTES}</td>
						<td span class="dvtCellInfo"><textarea name="description" rows="3" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" >{$DESCRIPTION}</textarea>
						</td>
					     </tr>
					     <tr><td colspan="4" height="30">&nbsp;</td></tr>
					     </table>
					     </td></tr>
					     </table>
					       </div>

						  <div id="mnuTab2">
 						     <table class="dvtContentSpace" border="0" cellpadding="0" cellspacing="0" width="100%">
						       <tr><td height="35">&nbsp;</td></tr>
						       <tr><td align="left">
							   <table width="99%"  border="0" cellspacing="0" cellpadding="5" align="center" class="small">
						       <tr>
							{if $MODE eq 'edit'}
					           		<td colspan="4" class="detailedViewHeader"><b>{$APP.LBL_EDIT} {$UMOD.LBL_USER_INFORMATION}</b></td>
							{else}
					           		<td colspan="4" class="detailedViewHeader"><b>{$UMOD.LBL_NEW_FORM_TITLE}</b></td>
							{/if}	
						       </tr>  
						       <tr>
							   <td class="dvtCellLabel" align="right">{$UMOD.LBL_ADDRESS}</td>
							   <td class="dvtCellInfo"><textarea name="address_street" rows="3" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" >{$ADDRESS_STREET}</textarea></td>
							   <td class="dvtCellInfo" >&nbsp;</td>
							   <td class="dvtCellInfo">&nbsp;</td>
							</tr>
							<tr>
							   <td class="dvtCellLabel" align="right" width="20%">{$UMOD.LBL_CITY}</td>
							   <td class="dvtCellInfo" width="20%"><input type="text" name="address_city" value='{$ADDRESS_CITY}' class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
							   <td class="dvtCellInfo" width="20%">&nbsp;</td>
							   <td class="dvtCellInfo" width="20%">&nbsp;</td>
							</tr>
							<tr>
							   <td class="dvtCellLabel" align="right">{$UMOD.LBL_STATE}</td>
							   <td class="dvtCellInfo"><input type="text" value='{$ADDRESS_STATE}' name="address_state" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
							   <td class="dvtCellInfo">&nbsp;</td>
							   <td class="dvtCellInfo">&nbsp;</td>
							</tr>
							<tr>
							   <td class="dvtCellLabel" align="right">{$UMOD.LBL_POSTAL_CODE}</td>
							   <td class="dvtCellInfo"><input type="text" value='{$ADDRESS_POSTALCODE}' name="address_postalcode" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>

							    <td class="dvtCellInfo">&nbsp;</td>
							    <td class="dvtCellInfo">&nbsp;</td>
							</tr>
							<tr>
							    <td class="dvtCellLabel" align="right">{$UMOD.LBL_COUNTRY}</td>
							    <td class="dvtCellInfo"><input type="text" value='{$ADDRESS_COUNTRY}' name="address_country" class="detailedViewTextBox"  onfocus="this.className='detailedViewTextBoxOn'" onblur="this.className='detailedViewTextBox'" /></td>
							    <td class="dvtCellInfo" >&nbsp;</td>
							    <td class="dvtCellInfo">&nbsp;</td>
							 </tr>
							 <tr><td colspan="4" height="30">&nbsp;</td></tr>
							 </table>
						 	  </td></tr>
							 </table>
						</div>
						    </td>
						     </tr>

							        <tr><td>&nbsp;</td></tr>
							        <tr>
							            <td><div align="center">
								    <input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accesskey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small"  name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  "  onclick="this.form.action.value='Save'; return verify_data(EditView)" style="width: 70px;" type="button" />
								    <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accesskey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " onclick="window.history.back()" style="width: 70px;" type="button" />
								        </div></td>
								</tr>
							  </table>
							  </td></tr>
						</table>
						</form>
			  	   </td></tr>
				   </table>
				    </td></tr>
				</table>
</td>
</tr>
</table>

{$JAVASCRIPT}
{if $PARENTTAB neq ''}
	{include file='SettingsSubMenu.tpl'}
{/if}
