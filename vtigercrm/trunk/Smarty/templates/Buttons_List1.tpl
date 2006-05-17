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
<script type="text/javascript" src="modules/{$MODULE}/{$SINGLE_MOD}.js"></script>
<!-- Activity createlink layer start  -->
<div id="reportLay" style="width: 125px; right: 159px; top: 260px; display: none; z-index:50" onmouseout="fninvsh('reportLay')" onmouseover="fnvshNrm('reportLay')">
        <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                        <td>
                                <a href="index.php?module={$MODULE}&action=EditView&return_module={$MODULE}&activity_mode=Events&return_action=DetailView&parenttab={$CATEGORY}" class="calMnu">{$NEW_EVENT}</a>
                                <a href="index.php?module={$MODULE}&action=EditView&return_module={$MODULE}&activity_mode=Task&return_action=DetailView&parenttab={$CATEGORY}" class="calMnu">{$NEW_TASK}</a>
                        </td>
                </tr>
        </table>

</div>
<!-- Activity createlink layer end  -->


<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>

<tr><td style="height:2px"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:30px" class="moduleName" nowrap>{$APP.$CATEGORY} > <a class="hdrLink" href="index.php?action=ListView&module={$MODULE}">{$APP.$MODULE}</a></td>
	<td class="sep1" style="width:1px;padding-right:1px"></td>
	<td class=small >
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					{if $CHECK.EditView eq 'yes'}
				        	{if $MODULE eq 'Activities'}
                                        	        <td style="padding-right:0px"><a href="#" id="showSubMenu"  onMouseOver="fnvshobj(this,'reportLay');" onMouseOut="fninvsh('reportLay');"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$MODULE}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$MODULE}..." border=0></a></td>
                                       		 {else}
        	                                	<td style="padding-right:0px"><a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$MODULE}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$MODULE}..." border=0></a></td>
                                        	{/if}
					{/if}
									
					<td style="padding-right:0px"><a href="#" onClick='return window.open("index.php?module=Contacts&action=vtchat","Chat","width=450,height=400,resizable=1,scrollbars=1");'><img src="{$IMAGE_PATH}tbarChat.gif" alt="{$APP.LBL_CHAT_ALT}" title="{$APP.LBL_CHAT_TITLE}" border=0></a>
                    			 </td>	
				</tr>
				</table>
			</td>
			<td nowrap width=50>&nbsp;</td>
			<td>
				<table border=0 cellspacing=0 cellpadding=5>

				<tr>
					<td style="padding-right:0px"><a href="#" onClick='fnvshobj(this,"miniCal");getMiniCal();'><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></a></td>
					<td style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock')"></a></a></td>
					<td style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calc')"></a></a></td>
			<td nowrap="nowrap" width="25">&nbsp;</td>
			<td style="padding-right: 0px;"><a href="javascript:;" onmouseout="fninvsh('allMenu');" onClick="fnvshobj(this,'allMenu')"><img src="{$IMAGE_PATH}btnL3AllMenu.gif" alt="{$APP.LBL_ALL_MENU_ALT}" title="{$APP.LBL_ALL_MENU_TITLE}" border="0"></a></td>
				</tr>
				</table>
			</td>
			
			<td>
				<table border=0 cellspacing=0 cellpadding=5>

				<tr>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
	
	
	{if $MODULE eq 'Contacts' || $MODULE eq 'Leads' || $MODULE eq 'Accounts' || $MODULE eq 'Potentials' || $MODULE eq 'Products' }
	<td class="sep1" style="width:1px;padding-right:1px"></td>
	<td nowrap style="width:50%;padding:10px">
	   	{if $CHECK.Import eq 'yes' && $CHECK.Export eq 'yes'}	
			<a href="index.php?module={$MODULE}&action=Import&step=2&return_module={$MODULE}&return_action=index">{$APP.LBL_IMPORT} {$APP.$MODULE}</a> |	
	    		<a href="index.php?module={$MODULE}&action=Export&all=1">{$APP.LBL_EXPORT} {$APP.$MODULE}</a>
	   	{elseif $CHECK.Import eq 'yes' && $CHECK.Export eq 'no'} 	
			<a href="index.php?module={$MODULE}&action=Import&step=2&return_module={$MODULE}&return_action=index">{$APP.LBL_IMPORT} {$APP.$MODULE}</a> 	
	   	{elseif $CHECK.Import eq 'no' && $CHECK.Export eq 'yes' } 	
	    		<a href="index.php?module={$MODULE}&action=Export&all=1">{$APP.LBL_EXPORT} {$APP.$MODULE}</a>
	   	{/if}	
	{elseif $MODULE eq 'Notes' || $MODULE eq 'Emails'}	
		<td class="sep1" style="width:1px"></td>
		<td nowrap style="width:50%;padding:10px">
		{if $CHECK.Export eq 'yes'}
			 <a href="index.php?module={$MODULE}&action=Export&all=1">{$APP.LBL_EXPORT} {$APP.$MODULE}</a>
		{/if}
	</td>
	{else}
	<td nowrap style="width:50%;padding:10px">&nbsp;</td>
	{/if}

	
</tr>
<tr><td style="height:2px"></td></tr>

</TABLE>
