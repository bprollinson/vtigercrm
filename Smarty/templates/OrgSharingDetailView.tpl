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
<style type="text/css">@import url(themes/blue/style.css);</style>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	{include file='SettingsMenu.tpl'}
<td width="75%" valign="top">

<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
<tr>
<td class="showPanelBg" valign="top" width="100%" colspan="3" style="padding-left:20px; "><br/>
<span class="lvtHeaderText"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS} </a> > {$MOD.LBL_USER_MANAGEMENT} > {$CMOD.LBL_ORG_SHARING_PRIVILEGES}</b></span>
<hr noshade="noshade" size="1" />
</td>
</tr>
<tr>
<td width="75%" style="padding-left:20px;" valign="top">
	
			<!-- module Select Table -->
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tbody><tr>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="7"><img src="{$IMAGE_PATH}top_left.jpg" align="top"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif; height: 6px;" bgcolor="#ebebeb"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="8"><img src="{$IMAGE_PATH}top_right.jpg" align="top" height="6" width="8"></td>
			  </tr>

			  <tr>
				<td bgcolor="#ebebeb" width="7"></td>
				<td style="padding-left: 10px; height: 20px; vertical-align: middle;" bgcolor="#ececec">
						{$CMOD.LBL_VIEW} :&nbsp;<a href="javascript:show('customdiv');show('globaldiv');">{$CMOD.LBL_BOTH}</a>&nbsp;|&nbsp;<a href="javascript:hide('customdiv');show('globaldiv');" >{$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</a>&nbsp;|&nbsp;
						<a href="javascript:show('customdiv');hide('globaldiv');">{$CMOD.LBL_CUSTOM_ACCESS_PRIVILEGES}</a>
				</td>

				<td bgcolor="#ebebeb" width="8"></td>
			  </tr>
			  <tr>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="7"><img src="{$IMAGE_PATH}bottom_left.jpg" align="bottom"></td>
				<td style="font-size: 1px;" bgcolor="#ececec" height="8"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="8"><img src="{$IMAGE_PATH}bottom_right.jpg" align="bottom"></td>
			  </tr>
		  </tbody></table><br>
		  <!-- end of module select -->

		  <!-- GLOBAL ACCESS MODULE -->
		  	<div id="globaldiv">
		  	  <table border="0" cellpadding="0" cellspacing="0" width="100%">
			  <tbody><tr>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="7"><img src="{$IMAGE_PATH}top_left.jpg" align="top"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif; height: 6px;" bgcolor="#ebebeb"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="8"><img src="{$IMAGE_PATH}top_right.jpg" align="top" height="6" width="8"></td>
			  </tr>
			  <tr>
				<td bgcolor="#ebebeb" width="7"></td>

				<td bgcolor="#ebebeb">	
						<table border="0" cellpadding="3" cellspacing="0" width="100%">
							<tbody>
							<form action="index.php" method="post" name="new" id="form">
							<input type="hidden" name="module" value="Users">
							<input type="hidden" name="action" value="OrgSharingEditView">
							<input type="hidden" name="parenttab" value="Settings">
							<tr>
								<td class="genHeaderSmall" height="25" valign="middle">{$CMOD.LBL_GLOBAL_ACCESS_PRIVILEGES}</td>
								<td align="right"><input class=small title="{$CMOD.LBL_RECALCULATE_BUTTON}"  type="submit" name="recalculate" value="{$CMOD.LBL_RECALCULATE_BUTTON}" onclick="this.form.action.value='RecalculateSharingRules'; return confirm('Recalculate Sharing Rules will calculate the sharing rules for the whole organization. This Operation will take some time. Do you want to contunue? ')">	
	&nbsp;<input class=small type="submit" name="Edit" value={$CMOD.LBL_EDIT_PERMISSIONS}></td>
								
							</tr>
							<tr><td colspan="2"></td></tr>
							 <tr>
						  		<td colspan="2" style="padding: 0px 0px 0px 1px;" bgcolor="#ffffff">

									<table class="globTab" cellpadding="0" cellspacing="0">
									<tbody>
									{foreach item=module from=$DEFAULT_SHARING}	
										<tr class="prvPrfHoverOff" onmouseover="this.className='prvPrfHoverOn'" onmouseout="this.className='prvPrfHoverOff'">
									    <th width="20%">{$APP[$module.0]}</th>
									    <td width="30%">
										{if $module.1 neq 'Private' && $module.1 neq 'Hide Details'}
											<img src="{$IMAGE_PATH}public.gif" align="absmiddle">
										{else}
											<img src="{$IMAGE_PATH}private.gif" align="absmiddle">
										{/if}
										{$CMOD[$module.1]}</td>
									    <td width="50%">{$module.2}</td>
								      	</tr>
								 	{/foreach} 
								  	</tbody>
									</table>
								</td>
						  </tr>
						</form>
						</tbody>
						</table>
						 <!-- End of Module Display -->
			         </td>
				<td bgcolor="#ebebeb" width="8"></td>
			  </tr>

			  <tr>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="7"><img src="{$IMAGE_PATH}bottom_left.jpg" align="bottom"></td>
				<td style="font-size: 1px;" bgcolor="#ebebeb" height="8"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="8"><img src="{$IMAGE_PATH}bottom_right.jpg" align="bottom"></td>
			  </tr>
		  </tbody></table><br>
		</div>

		  <!-- END OF GLOBAL -->
		  
		  
		  <!-- Custom Access Module Display Table -->
		  <div id="customdiv">
		  <table border="0" cellpadding="0" cellspacing="0" width="100%">
		  <tbody>
		  <tr>
		  	<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="7"><img src="{$IMAGE_PATH}top_left.jpg" align="top"></td>
			<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif; height: 6px;" bgcolor="#ebebeb"></td>
			<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="6" width="8"><img src="{$IMAGE_PATH}top_right.jpg" align="top" height="6" width="8"></td>
		 </tr>
		 <tr>
			<td bgcolor="#ebebeb" width="7"></td>
			<td bgcolor="#ebebeb">	
				<table border="0" cellpadding="3" cellspacing="0" width="100%">
				<tbody>
				<tr>
					<td class="genHeaderSmall" colspan="2" height="25" valign="middle">{$CMOD.LBL_CUSTOM_ACCESS_PRIVILEGES}</td>
				</tr>
				<tr><td colspan="2"></td></tr>
				<tr>
					<td colspan="2" style="padding: 0px 0px 0px 1px;" bgcolor="#ebebeb">
						
				<!-- Start of Module Display -->
						{foreach key=modulename item=details from=$MODSHARING}
						<div align="right"><a href="#">{$CMOD.LBL_GO_TO_TOP}</a></div>
						{if $details.0 neq ''}
						<table class="orgTab" cellpadding="0" cellspacing="0">
						<tbody>
						<tr bgcolor="#cccccc">
							<td colspan=3 style="border: 1px solid rgb(204, 204, 204); padding-left: 5px;">
							<img src="{$IMAGE_PATH}arrow.jpg" align="absmiddle">&nbsp;
							<b>{$APP.$modulename}</b>&nbsp; 
							</td>
							<td align="right" colspan=2><input title="New" class="small" type="button" name="Create" value="{$CMOD.LBL_ADD_PRIVILEGES_BUTTON}" onClick="callEditDiv('{$modulename}','create','')"></td>
						</tr>
					  	<tr>
							<th class="lvtCol" nowrap width="9%">{$CMOD.LBL_RULE_NO}</th>
							<th class="lvtCol" width="20%">{$APP.$modulename} {$CMOD.LBL_OF} </th>
							<th class="lvtCol" width="25%">{$CMOD.LBL_CAN_BE_ACCESSED}</th>
							<th class="lvtCol" width="40%">{$CMOD.LBL_PRIVILEGES}</th>
							<th class="lvtCol" width="6%">{$APP.Tools}</th>
						</tr>
						{foreach key=sno item=elements from=$details}
						<tr class="prvPrfHoverOut" onmouseover="this.className='prvPrfHoverOn'" onmouseout="this.className='prvPrfHoverOut'">
							<td>{$sno+1}</td>
							<td>{$elements.1}</td>
							<td>{$elements.2}</td>
							<td>{$elements.3}</td>
							<td align="center"><a href="javascript:onClick=callEditDiv('{$modulename}','edit','{$elements.0}')"><img src="{$IMAGE_PATH}editfield.gif" title='edit' align="absmiddle" height="15" width="16" border=0></a>|<a href="index.php?module=Users&action=DeleteSharingRule&shareid={$elements.0}"><img src="{$IMAGE_PATH}delete.gif" title='del' align="absmiddle" height="15" width="16" border=0></a></td>
					    </tr>
						{/foreach}
				  		</tbody></table>
				<!-- End of Module Display -->
					{else}
				<!-- Start FOR NO DATA -->
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tbody>
					<tr><td colspan="2"></td></tr>
					<tr bgcolor="#cccccc">
					  	<td style="border: 1px solid rgb(204, 204, 204); padding-left: 5px;">
					  	<img src="{$IMAGE_PATH}arrow.jpg" align="absmiddle">&nbsp;
						<b>{$APP.$modulename}</b>&nbsp;&nbsp; 
						</td>
					 	<td align="right"><input title="New" class="small" type="button" name="Create" value="Add Privileges" onClick="callEditDiv('{$modulename}','create','')"></td>
				  	</tr>
				  	<tr>
				  		<td colspan="2" style="padding: 20px;" bgcolor="#ffffff" align="center">
						{$CMOD.LBL_CUSTOM_ACCESS_MESG} 
						<a href="javascript:onClick=callEditDiv('{$modulename}','create','')">{$CMOD.LNK_CLICK_HERE}</a>
						{$CMOD.LBL_CREATE_RULE_MESG}
					    </td>
				  	</tr>
					</tbody>
					</table>
			    <!-- END OF NO DATA -->
					{/if}
					<br><br>
					{/foreach}
					</td>
					</tr>
					</tbody></table>
				</td>
				<td bgcolor="#ebebeb" width="8"></td>
			  </tr>
			  <tr>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="7"><img src="{$IMAGE_PATH}bottom_left.jpg" align="bottom"></td>
				<td style="font-size: 1px;" bgcolor="#ebebeb" height="8"></td>
				<td style="font-size: 1px; font-family: Arial,Helvetica,sans-serif;" height="8" width="8"><img src="{$IMAGE_PATH}bottom_right.jpg" align="bottom"></td>
			  </tr>
		  </tbody></table><br>
			</div>		
		  		<!-- END OF CUSTOM ACCESS -->


</td>
<td width="1%" style="border-right:1px dotted #CCCCCC;">&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<div id="tempdiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
function ajaxSaveResponse(response)
{ldelim}
	hide("status");
	document.getElementById("tempdiv").innerHTML=response.responseText;
{rdelim}

function callEditDiv(modulename,mode,id)
{ldelim}
	show("status");
	var ajaxObj = new VtigerAjax(ajaxSaveResponse);
	var urlstring = "module=Users&action=UsersAjax&orgajax=true&mode="+mode+"&sharing_module="+modulename+"&shareid="+id;
	ajaxObj.process("index.php?",urlstring);
{rdelim}

function fnwriteRules(module,related)
{ldelim}
		var modulelists = new Array();
		modulelists = related.split('###');
		var relatedstring ='';
		var relatedtag;
		var relatedselect;
		var modulename;
		for(i=0;i < modulelists.length-1;i++)
		{ldelim}
			modulename = modulelists[i]+"_accessopt";
			relatedtag = document.getElementById(modulename);
			relatedselect = relatedtag.options[relatedtag.selectedIndex].text;
			relatedstring += modulelists[i]+':'+relatedselect+' ';
		{rdelim}	
		var tagName = document.getElementById(module+"_share");
		var tagName2 = document.getElementById(module+"_access");
		var tagName3 = document.getElementById('share_memberType');
		var soucre =  document.getElementById("rules");
		var soucre1 =  document.getElementById("relrules");
		var select1 = tagName.options[tagName.selectedIndex].text;
		var select2 = tagName2.options[tagName2.selectedIndex].text;
		var select3 = tagName3.options[tagName3.selectedIndex].text;
		soucre.innerHTML = module +" of <b>\"" + select1 + "\"</b> can be accessed by <b>\"" +select2 + "\"</b> in the permission "+select3;
		soucre1.innerHTML = "<b>Related Module Rights</b> "+ relatedstring;
{rdelim}

</script>
{include file='SettingsSubMenu.tpl'}

