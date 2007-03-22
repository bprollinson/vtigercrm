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
<script type="text/javascript" src="modules/{$MODULE}/{$MODULE}.js"></script>
<!-- Activity createlink layer start  -->
{if $MODULE eq 'Calendar'}
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
{/if}
<!-- Activity createlink layer end  -->


<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
<tr><td style="height:2px"></td></tr>
<tr>
	<td style="padding-left:10px;padding-right:50px" class="moduleName" nowrap>{$APP.$CATEGORY} > <a class="hdrLink" href="index.php?action=ListView&module={$MODULE}&parenttab={$CATEGORY}">{$APP.$MODULE}</a></td>
	<td width=100% nowrap>
	
		<table border="0" cellspacing="0" cellpadding="0" >
		<tr>
		<td class="sep1" style="width:1px;"></td>
		<td class=small >
			<!-- Add and Search -->
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					{if $CHECK.EditView eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
			        		{if $MODULE eq 'Calendar'}
		                      	        	<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}btnL3Add-Faded.gif" border=0></td>
                	   			 {else}
	                        		       	<td style="padding-right:0px;padding-left:10px;"><a href="index.php?module={$MODULE}&action=EditView&return_action=DetailView&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="{$APP.LBL_CREATE_BUTTON_LABEL} {$APP.$SINGLE_MOD}..." title="{$APP.LBL_CREATE_BUTTON_LABEL} {$APP.$SINGLE_MOD}..." border=0></a></td>
			                       	{/if}
					{else}
						<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}btnL3Add-Faded.gif" border=0></td>	
					{/if}
									
					{if $CHECK.index eq 'yes' && $MODULE neq 'Emails' && $MODULE neq 'Webmails'}
						 <td style="padding-right:10px"><a href="javascript:;" onClick="moveMe('searchAcc');searchshowhide('searchAcc','advSearch')" ><img src="{$IMAGE_PATH}btnL3Search.gif" alt="{$APP.LBL_SEARCH_ALT}{$APP.$MODULE}..." title="{$APP.LBL_SEARCH_TITLE}{$APP.$MODULE}..." border=0></a></a></td>
					{else}
						<td style="padding-right:10px"><img src="{$IMAGE_PATH}btnL3Search-Faded.gif" border=0></td>
					{/if}
					
				</tr>
				</table>
			</td>
			</tr>
			</table>
		</td>
		<td style="width:20px;">&nbsp;</td>
		<td class="small">
			<!-- Calendar Clock Calculator and Chat -->
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
						{if $CALENDAR_DISPLAY eq 'true'} 
 		                                                {if $CATEGORY eq 'Settings' || $CATEGORY eq 'Tools' || $CATEGORY eq 'Analytics'} 
 		                                                        {if $CHECK.Calendar eq 'yes'} 
 		                                                                <td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab=My Home Page");'><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></a></td> 
 		                                                        {else} 
 		                                                                <td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}btnL3Calendar-Faded.gif"></td> 
 		                                                        {/if} 
						{else}
						{if $CHECK.Calendar eq 'yes'} 
 		                                                                <td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick='fnvshobj(this,"miniCal");getMiniCal("parenttab={$CATEGORY}");'><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="{$APP.LBL_CALENDAR_ALT}" title="{$APP.LBL_CALENDAR_TITLE}" border=0></a></a></td> 
 		                                                        {else} 
 		                                                                <td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}btnL3Calendar-Faded.gif"></td> 
 		                                                        {/if} 
						{/if}
					{/if}
					{if $WORLD_CLOCK_DISPLAY eq 'true'} 
 		                                                <td style="padding-right:0px"><a href="javascript:;"><img src="{$IMAGE_PATH}btnL3Clock.gif" alt="{$APP.LBL_CLOCK_ALT}" title="{$APP.LBL_CLOCK_TITLE}" border=0 onClick="fnvshobj(this,'wclock');"></a></a></td> 
 		                                        {/if} 
 		                                        {if $CALCULATOR_DISPLAY eq 'true'} 
 		                                                <td style="padding-right:0px"><a href="#"><img src="{$IMAGE_PATH}btnL3Calc.gif" alt="{$APP.LBL_CALCULATOR_ALT}" title="{$APP.LBL_CALCULATOR_TITLE}" border=0 onClick="fnvshobj(this,'calculator_cont');fetch_calc();"></a></td> 
 		                                        {/if} 
 		                                        {if $CHAT_DISPLAY eq 'true'} 
 		                                                <td style="padding-right:10px"><a href="javascript:;" onClick='return window.open("index.php?module=Contacts&action=vtchat","Chat","width=600,height=450,resizable=1,scrollbars=1");'><img src="{$IMAGE_PATH}tbarChat.gif" alt="{$APP.LBL_CHAT_ALT}" title="{$APP.LBL_CHAT_TITLE}" border=0></a> 
 		                                        {/if} 
                    </td>	
					<td style="padding-right:10px"><img src="{$IMAGE_PATH}btnL3Tracker.gif" alt="{$APP.LBL_LAST_VIEWED}" title="{$APP.LBL_LAST_VIEWED}" border=0 onClick="fnvshobj(this,'tracker');">
                    			</td>	
				</tr>
				</table>
		</td>
		<td style="width:20px;">&nbsp;</td>
		<td class="small">
			<!-- Import / Export -->
			<table border=0 cellspacing=0 cellpadding=5>
			<tr>
			{if $MODULE eq 'Contacts' || $MODULE eq 'Leads' || $MODULE eq 'Accounts' || $MODULE eq 'Potentials' || $MODULE eq 'Products' }
		   		{if $CHECK.Import eq 'yes'}	
					<td style="padding-right:0px;padding-left:10px;"><a href="index.php?module={$MODULE}&action=Import&step=1&return_module={$MODULE}&return_action=index&parenttab={$CATEGORY}"><img src="{$IMAGE_PATH}tbarImport.gif" alt="{$APP.LBL_IMPORT} {$APP.$MODULE}" title="{$APP.LBL_IMPORT} {$APP.$MODULE}" border="0"></a></td>	
				{else}	
					<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}tbarImport-Faded.gif" border="0"></td>	
				{/if}	
				{if $CHECK.Export eq 'yes'}	
	    			<td style="padding-right:10px"><a href="index.php?module={$MODULE}&action=Export&all=1"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$APP.$MODULE}" title="{$APP.LBL_EXPORT} {$APP.$MODULE}" border="0"></a></td>
				{else}	
					<td style="padding-right:10px"><img src="{$IMAGE_PATH}tbarExport-Faded.gif" border="0"></td>
                {/if}
			{elseif $MODULE eq 'Notes' || $MODULE eq 'Emails'}	
				
				{if $CHECK.Export eq 'yes'}
					<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}tbarImport-Faded.gif" border="0"></td>
					<td style="padding-right:10px"><a href="index.php?module={$MODULE}&action=Export&all=1"><img src="{$IMAGE_PATH}tbarExport.gif" alt="{$APP.LBL_EXPORT} {$APP.$MODULE}" title="{$APP.LBL_EXPORT} {$APP.$MODULE}" border="0"></a></td>
				{else}	 
					<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}tbarImport-Faded.gif" border="0"></td>
					<td style="padding-right:10px"><img src="{$IMAGE_PATH}tbarExport-Faded.gif" border="0"></td>
				{/if}
			{else}
				<td style="padding-right:0px;padding-left:10px;"><img src="{$IMAGE_PATH}tbarImport-Faded.gif" border="0"></td>
                <td style="padding-right:10px"><img src="{$IMAGE_PATH}tbarExport-Faded.gif" border="0"></td>
			{/if}
			</tr>
			</table>	
		<td style="width:20px;">&nbsp;</td>
		<td class="small">
			<!-- All Menu -->
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
				<td style="padding-left:10px;"><a href="javascript:;" onmouseout="fninvsh('allMenu');" onClick="fnvshobj(this,'allMenu')"><img src="{$IMAGE_PATH}btnL3AllMenu.gif" alt="{$APP.LBL_ALL_MENU_ALT}" title="{$APP.LBL_ALL_MENU_TITLE}" border="0"></a></td>
				</tr>
				</table>
		</td>			
		</tr>
		</table>
	</td>
</tr>
<tr><td style="height:2px"></td></tr>
</TABLE>
