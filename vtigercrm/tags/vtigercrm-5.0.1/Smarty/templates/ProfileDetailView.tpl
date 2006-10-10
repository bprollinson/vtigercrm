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
{literal}
<style>
.showTable{
	display:inline-table;
}
.hideTable{
	display:none;
}
</style>
{/literal}
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
        <br>
	<div align=center>
			{include file='SetMenu.tpl'}
				<form action="index.php" method="post" name="new" id="form">
			        <input type="hidden" name="module" value="Users">
			        <input type="hidden" name="action" value="profilePrivileges">
			        <input type="hidden" name="parenttab" value="Settings">
			        <input type="hidden" name="return_action" value="profilePrivileges">
			        <input type="hidden" name="mode" value="edit">
			        <input type="hidden" name="profileid" value="{$PROFILEID}">
				<!-- DISPLAY -->
				<table class="settingsSelUITopLine" border="0" cellpadding="5" cellspacing="0" width="100%">
				<tbody><tr>
					<td rowspan="2" valign="top" width="50"><img src="{$IMAGE_PATH}ico-profile.gif" alt="{$MOD.LBL_PROFILES}" title="{$MOD.LBL_PROFILES}" border="0" height="48" width="48"></td>
					<td class="heading2" valign="bottom"><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Users&action=ListProfiles&parenttab=Settings">{$CMOD.LBL_PROFILE_PRIVILEGES}</a> &gt; {$CMOD.LBL_VIEWING} &quot;{$PROFILE_NAME}&quot;</b></td>
				</tr>
				<tr>
					<td class="small" valign="top">{$CMOD.LBL_PROFILE_MESG} &quot;{$PROFILE_NAME}&quot; </td>
				</tr>
				</tbody></table>
				
				
				<table border="0" cellpadding="10" cellspacing="0" width="100%">
				<tbody><tr>
				<td valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tbody><tr>
                        <td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr class="small">
                              <td><img src="{$IMAGE_PATH}prvPrfTopLeft.gif"></td>
                              <td class="prvPrfTopBg" width="100%"></td>
                              <td><img src="{$IMAGE_PATH}prvPrfTopRight.gif"></td>
                            </tr>
                          </tbody></table>
                            <table class="prvPrfOutline" border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody><tr>
                                <td><!-- tabs -->
                                    
                                    <!-- Headers -->
                                    <table border="0" cellpadding="5" cellspacing="0" width="100%">
                                      <tbody><tr>
                                        <td><table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
                                            <tbody><tr>
                                              <td><!-- Module name heading -->
                                                  <table class="small" border="0" cellpadding="2" cellspacing="0">
                                                    <tbody><tr>
                                                      <td valign="top"><img src="{$IMAGE_PATH}prvPrfHdrArrow.gif"> </td>
                                                      <td class="prvPrfBigText"><b> {$CMOD.LBL_DEFINE_PRIV_FOR} &lt;{$PROFILE_NAME}&gt; </b><br>
                                                      <font class="small">{$CMOD.LBL_USE_OPTION_TO_SET_PRIV}</font> </td>
                                                      <td class="small" style="padding-left: 10px;" align="right"></td>

                                                    </tr>
                                                </tbody></table></td>
                                              <td align="right" valign="bottom">&nbsp;<input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" class="crmButton small edit" name="edit">											  </td>
                                            </tr>
                                          </tbody></table>
                                            <!-- privilege lists -->
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td style="height: 10px;" align="center"><img src="{$IMAGE_PATH}prvPrfLine.gif" style="width: 100%; height: 1px;"></td>
                                              </tr>
                                            </tbody></table>
                                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                              <tbody><tr>
                                                <td>
						<table border="0" cellpadding="5" cellspacing="0" width="100%">
  						<tbody>
							<tr>
    							<td class="cellLabel big"> {$CMOD.LBL_SUPER_USER_PRIV} </td>
						       </tr>
						</tbody>
						</table>
						<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
                                                <tbody><tr>
                                                    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
                                                    <td valign="top" width="97%"><table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
                                                      <tbody>
				                         <tr id="gva">
                                                          <td valign="top">{$GLOBAL_PRIV.0}</td>
                                                          <td ><b>{$CMOD.LBL_VIEW_ALL}</b> </td>
                                                        </tr>
                                                        <tr >
                                                          <td valign="top"></td>
                                                          <td width="100%" >{$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_VIEW}</td>
                                                        </tr>
                                                        <tr>
                                                          <td>&nbsp;</td>
                                                        </tr>
							<tr>
							<td valign="top">{$GLOBAL_PRIV.1}</td>
							<td ><b>{$CMOD.LBL_EDIT_ALL}</b> </td>
							</tr>
                                                        <tr>
                                                          <td valign="top"></td>
                                                          <td > {$CMOD.LBL_ALLOW} "{$PROFILE_NAME}" {$CMOD.LBL_MESG_EDIT}</td>
                                                        </tr>

                                                      </tbody></table>
						</td>
                                                  </tr>
                                                </tbody></table>
<br>

			<table border="0" cellpadding="5" cellspacing="0" width="100%">
			  <tbody><tr>
			    <td class="cellLabel big"> {$CMOD.LBL_SET_PRIV_FOR_EACH_MODULE} </td>
			  </tr>
			</tbody></table>
			<table class="small" align="center" border="0" cellpadding="5" cellspacing="0" width="90%">
			  <tbody><tr>
			    <td class="prvPrfTexture" style="width: 20px;">&nbsp;</td>
			    <td valign="top" width="97%">
				<table class="small listTable" border="0" cellpadding="5" cellspacing="0" width="100%">
			        <tbody>
				<tr id="gva">
			          <td colspan="2" rowspan="2" class="small colHeader"><strong> {$CMOD.LBL_TAB_MESG_OPTION} </strong><strong></strong></td>
			          <td colspan="3" class="small colHeader"><div align="center"><strong> {$CMOD.LBL_EDIT_PERMISSIONS} </strong></div></td>
			          <td rowspan="2" class="small colHeader" nowrap="nowrap"> {$CMOD.LBL_FIELDS_AND_TOOLS_SETTINGS} </td>
			        </tr>
			        <tr id="gva">
			          <td class="small colHeader"><div align="center"><strong>{$CMOD.LBL_CREATE_EDIT}
			          </strong></div></td>
			          <td class="small colHeader"> <div align="center"><strong>{$CMOD.LBL_VIEW} </strong></div></td>
			          <td class="small colHeader"> <div align="center"><strong>{$CMOD.LBL_DELETE}</strong></div></td>
			        </tr>
					
				<!-- module loops-->
			        {foreach key=tabid item=elements from=$TAB_PRIV}	
			        <tr>
                                        {assign var=modulename value=$TAB_PRIV[$tabid][0]}
			          <td class="small cellLabel" width="3%"><div align="right">
					{$TAB_PRIV[$tabid][1]}
			          </div></td>
			          <td class="small cellLabel" width="40%"><p>{$APP[$modulename]}</p></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][1]}
			          </div></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][3]}
			          </div></td>
			          <td class="small cellText" width="15%">&nbsp;<div align="center">
					{$STANDARD_PRIV[$tabid][2]}
        			  </div></td>
			          <td class="small cellText" width="22%">&nbsp;<div align="center">
				{if $FIELD_PRIVILEGES[$tabid] neq NULL || $modulename eq 'Emails'}
				<img src="{$IMAGE_PATH}showDown.gif" alt="{$APP.LBL_EXPAND_COLLAPSE}" title="{$APP.LBL_EXPAND_COLLAPSE}" onclick="fnToggleVIew('{$modulename}_view')" border="0" height="16" width="40">
				{/if}
				</div></td>
				  </tr>
		                  <tr class="hideTable" id="{$modulename}_view" className="hideTable">
				          <td colspan="6" class="small settingsSelectedUI">
						<table class="small" border="0" cellpadding="2" cellspacing="0" width="100%">
			        	    	<tbody>
						{if $FIELD_PRIVILEGES[$tabid] neq ''}
						<tr>
							{if $modulename eq 'Calendar'}
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_SELECT_DESELECT} ({$APP.Tasks})</td>
							{else}
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_SELECT_DESELECT}</td>
							{/if}
					        </tr>
						{/if}
						{foreach item=row_values from=$FIELD_PRIVILEGES[$tabid]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.1}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{if $modulename eq 'Calendar'}
						<tr>
				                	<td class="small colHeader" colspan="6" valign="top">{$CMOD.LBL_FIELDS_SELECT_DESELECT}  ({$APP.Events})</td>
					        </tr>
						{foreach item=row_values from=$FIELD_PRIVILEGES[16]}
				            	<tr>
						      {foreach item=element from=$row_values}
					              <td valign="top">{$element.1}</td>
					              <td>{$element.0}</td>
						      {/foreach}
				                </tr>
						{/foreach}
						{/if}
						{if $UTILITIES_PRIV[$tabid] neq ''}
					        <tr>
					              <td colspan="6" class="small colHeader" valign="top">{$CMOD.LBL_TOOLS_TO_BE_SHOWN} </td>
						</tr>
						{/if}
						{foreach item=util_value from=$UTILITIES_PRIV[$tabid]}
						<tr>
							{foreach item=util_elements from=$util_value}
					              		<td valign="top">{$util_elements.1}</td>
						                <td>{$APP[$util_elements.0]}</td>
							{/foreach}
				               	</tr>
						{/foreach}
					        </tbody>
						</table>
					</td>
			          </tr>
				  {/foreach}	
			    	  </tbody>
				  </table>
			  </td>
			  </tr>
                          </tbody>
			</table>
		</td>
                </tr>
		<table border="0" cellpadding="2" cellspacing="0">
			<tr>
				<td align="left"><font color="red" size=5>*</font>{$CMOD.LBL_MANDATORY_MSG}</td>
			</tr>
			<tr>
				<td align="left"><font color="blue" size=5>*</font>{$CMOD.LBL_DISABLE_FIELD_MSG}</td>
			</tr>
		</table>
		<tr>
		<td style="border-top: 2px dotted rgb(204, 204, 204);" align="right">
		<!-- wizard buttons -->
		<table border="0" cellpadding="2" cellspacing="0">
		<tbody>
			<tr>
				<td><input type="submit" value="{$APP.LBL_EDIT_BUTTON_LABEL}" class="crmButton small edit" name="edit"></td>
				<td>&nbsp;</td>
			</tr>
			
		</tbody>
		</table>
		</td>
		</tr>
          </tbody>
	  </table>
	</td>
        </tr>
        </tbody>
	</table>
      </td>
      </tr>
      </tbody></table>
      <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
           <tbody><tr>
                <td><img src="{$IMAGE_PATH}prvPrfBottomLeft.gif"></td>
                <td class="prvPrfBottomBg" width="100%"></td>
                <td><img src="{$IMAGE_PATH}prvPrfBottomRight.gif"></td>
                </tr>
            </tbody>
      </table></td>
      </tr>
      </tbody></table>
	<p>&nbsp;</p>
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tbody><tr><td class="small" align="right" nowrap="nowrap"><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
	</tbody></table>
					
	</td>
	</tr>
	</tbody></table>
	</form>	
	<!-- End of Display -->
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	</div>

	</td>
	<td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
	</tr>
</tbody>
</table>
<script language="javascript" type="text/javascript">
{literal}
function fnToggleVIew(obj){
	var tagStyle = document.getElementById(obj).className;
	if(tagStyle == 'hideTable')
		document.getElementById(obj).className = 'showTable';
	else
		document.getElementById(obj).className = 'hideTable';
}
{/literal}
</script>
