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
<script type="text/javascript" src="modules/{$MODULE}/Calendar.js"></script>
<script type="text/javascript" src="include/js/reflection.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
   <a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<script>
function tagvalidate()
{ldelim}
	if(trim(document.getElementById('txtbox_tagfields').value) != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
	else
	{ldelim}
		alert("{$APP.PLEASE_ENTER_TAG}");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id,recordid)
{ldelim}
        $("vtbusy_info").style.display="inline";
        Effect.Fade('tag_'+id);
        new Ajax.Request(
                'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&recordid="+recordid+"&tagid=" +id,
                        onComplete: function(response) {ldelim}
                                                getTagCloud();
                                                $("vtbusy_info").style.display="none";
                        {rdelim}
                {rdelim}
        );
{rdelim}

</script>
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<form action="index.php" method="post" name="DetailView" id="form">
<tr><td>&nbsp;</td>
	<td>
                <table cellpadding="0" cellspacing="5" border="0">
			{include file='DetailViewHidden.tpl'}
		</table>	

<!-- Contents -->
<table  border="0" cellpadding="5" cellspacing="0" width="100%" >
<tr>
	<td class="lvtHeaderText" style="border-bottom:1px dotted #cccccc">
	
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
			<tr><td>		
				 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span></td><td>&nbsp;
			</td></tr>
			 <tr height=20><td class=small>{$UPDATEINFO}</td></tr>
		 </table>
	</td>
</tr>
<tr><td>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign=top align=left >
                           <table border=0 cellspacing=0 cellpadding=3 width=100%>
				<tr>
					<td align=left>
					<!-- content cache -->
					
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                  <tr>
					     <td style="padding:3px">
						     <!-- General details -->
				                     <table border=0 cellspacing=0 cellpadding=0 width=100%>
						     <tr nowrap>
							<td  colspan=4 style="padding:5px">
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
								{/if}
							</td>
							<td width=50% align=right>
							{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
                                                        {/if}
							{if $DELETE eq 'permitted'}
                                                                <input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="this.form.return_module.value='{$MODULE}'; {if $VIEWTYPE eq 'calendar'} this.form.return_action.value='index'; {else} this.form.return_action.value='ListView'; {/if}  this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
                                                         {/if}

							</td>
						     </tr>
						     </table>
						     {foreach key=header item=detail from=$BLOCKS}
						     <table border=0 cellspacing=0 cellpadding=5 width=100% class="small">
						     	<tr>{strip}
						     		<td colspan=4 class="tableHeading">
									<b>{$header}</b>
								</td>{/strip}
					             	</tr>
						     </table>
						     {/foreach}
						     {if $ACTIVITYDATA.activitytype neq 'Task'}	
							 <!-- display of fields starts -->
						     <table border=0 cellspacing=0 cellpadding=5 width=100% >
               						 <tr>
								{if $LABEL.activitytype neq ''}
								{assign var=type value=$ACTIVITYDATA.activitytype}
								<td class="cellLabel" width="20%" align="right"><b>{$MOD.LBL_EVENTTYPE}</b></td>
								<td class="cellInfo" width="30%"align="left">{$MOD.$type}</td>
								{/if}
								{if $LABEL.visibility neq ''}
								{assign var=vblty value=$ACTIVITYDATA.visibility}
								<td class="cellLabel" width="20%" align="right"><b>{$LABEL.visibility}</b></td>
                                                                <td class="cellInfo" width="30%" align="left" >{$MOD.$vblty}</td>
								{/if}
							 </tr>
							 <tr>
                        					<td class="cellLabel" align="right"><b>{$MOD.LBL_EVENTNAME}</b></td>
					                        <td class="cellInfo" colspan=3 align="left" >{$ACTIVITYDATA.subject}</td>
             						 </tr>
							 {if $LABEL.description neq ''}
							 <tr>
								<td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.description}</b></td>
								<td class="cellInfo" valign="top" align="left" colspan="3" height="60px">{$ACTIVITYDATA.description}&nbsp;</td>
							 </tr>
							{/if}
							{if $LABEL.location neq ''}
							<tr>
								<td class="cellLabel" align="right" valign="top"><b>{$LABEL.location}</b></td>
								<td class="cellInfo" colspan=3 align="left" >{$ACTIVITYDATA.location}&nbsp;</td>
							</tr>
							{/if}	
							 <tr>
								{if $LABEL.eventstatus neq ''}
								<td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.eventstatus}</b></td>
								<td class="cellInfo" align="left" nowrap valign="top">
									{if $ACTIVITYDATA.eventstatus eq $APP.LBL_NOT_ACCESSIBLE}
										<font color="red">{$ACTIVITYDATA.eventstatus}</font>
										{else}
											{$ACTIVITYDATA.eventstatus}
									{/if}
								</td>
								{/if}
								{if $LABEL.assigned_user_id neq ''}
								<td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.assigned_user_id}</b></td>
								<td class="cellInfo" align="left" nowrap valign="top">{$ACTIVITYDATA.assigned_user_id}</td>
								{/if}
                                                         </tr>
							{if $LABEL.taskpriority neq '' || $LABEL.sendnotification neq ''}
							 <tr>
								{if $LABEL.taskpriority neq ''}
                                                                <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.taskpriority}</b></td>
                                                                <td class="cellInfo" align="left" nowrap valign="top">
									{if $ACTIVITYDATA.taskpriority eq $APP.LBL_NOT_ACCESSIBLE}
										<font color="red" >{$ACTIVITYDATA.taskpriority}</font>
									{else}
										{$ACTIVITYDATA.taskpriority}
									{/if}
								</td>
								{/if}
								{if $LABEL.sendnotification neq ''}
                                                                <td class="cellLabel" align="right" nowrap valign="top"><b>{$LABEL.sendnotification}</b></td>
                                                                <td class="cellInfo" align="left" nowrap valign="top">{$ACTIVITYDATA.sendnotification}</td>
								{/if}
                                                         </tr>
							{/if}
							{if $LABEL.createdtime neq '' || $LABEL.modifiedtime neq ''}
                                                         <tr>
                                                                <td class="cellLabel" align="right" nowrap valign="top"align="right">{if $LABEL.createdtime neq ''}<b>{$LABEL.createdtime}</b>{/if}</td>
                                                                <td class="cellInfo" align="left" nowrap valign="top">{if $LABEL.createdtime neq ''}{$ACTIVITYDATA.createdtime}{/if}</td>
                                                                <td class="cellLabel" align="right" nowrap valign="top"align="right">{if $LABEL.modifiedtime neq ''}<b>{$LABEL.modifiedtime}</b>{/if}</td>
                                                                <td class="cellInfo" align="left" nowrap valign="top">{if $LABEL.modifiedtime neq ''}{$ACTIVITYDATA.modifiedtime}{/if}</td>
                                                         </tr>
							{/if}
						     </table>
						     <table border=0 cellspacing=1 cellpadding=0 width=100%>
							<tr><td width=50% valign=top >
								<table border=0 cellspacing=0 cellpadding=2 width=100%>
                                                                        <tr><td class="mailSubHeader"><b>{$MOD.LBL_EVENTSTAT}</b></td></tr>
                                                                        <tr><td class=small>{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
                                                                        <tr><td class=small>{$ACTIVITYDATA.date_start}</td></tr>
                                                                </table></td>
							<td width=50% valign=top >
                                                                <table border=0 cellspacing=0 cellpadding=2 width=100%>
                                                                        <tr><td  class="mailSubHeader"><b>{$MOD.LBL_EVENTEDAT}</b></td></tr>
                                                                        <tr><td class=small>{$ACTIVITYDATA.endhr}:{$ACTIVITYDATA.endmin}{$ACTIVITYDATA.endfmt}</td></tr>
                                                                        <tr><td class=small>{$ACTIVITYDATA.due_date}</td></tr>
                                                                </table>
                                                        </td></tr>
                                                     </table>
						     <br>
					             <table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
                					 <tr>
                        					<td>
                         				        	<table border=0 cellspacing=0 cellpadding=3 width=100%>
                             						<tr>
                                        					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
					                                        <td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');dispLayer('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_INVITE}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										{if $LABEL.reminder_time neq ''}
										<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REMINDER}</a></td>
										{/if}
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										{if $LABEL.recurringtype neq ''}
										<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REPEAT}</a></td>
										{/if}
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRelatedtoUI');ghide('addEventRepeatUI');">{$MOD.LBL_LIST_RELATED_TO}</a></td>
										<td class="dvtTabCache" style="width:100%">&nbsp;</td>
									</tr>
									</table>
								</td>
							 </tr>
							
							 <tr>
								<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
									<!-- Invite UI -->
									<DIV id="addEventInviteUI" style="display:block;width:100%">
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
										<tr>
                                                                                        <td width="30%" valign="top" align=right><b>{$MOD.LBL_USERS}</b></td>
                                                                                        <td width="70%" align=left valign="top" >
												{foreach item=username key=userid from=$INVITEDUSERS}
                                                                                        	        {$username}<br>
                                                                                                {/foreach}
											</td>
                                                                                </tr>
									</table>
									</DIV>
									<!-- Reminder UI -->
					                                <DIV id="addEventAlarmUI" style="display:none;width:100%">
									{if $LABEL.reminder_time != ''}
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                                <tr>
                                                                                        <td width="30%" align=right><b>{$MOD.LBL_SENDREMINDER}</b></td>
                                                                                        <td width="70%" align=left>{$ACTIVITYDATA.set_reminder}</td>
                                                                                </tr>
										{if $ACTIVITYDATA.set_reminder != 'No'}
										<tr>
                                                                                        <td width="30%" align=right><b>{$MOD.LBL_RMD_ON}</b></td>
											<td width="70%" align=left>{$ACTIVITYDATA.reminder_str}</td>
										</tr>
										{/if}
                                                                        </table>
									{/if}
									</DIV>
									<!-- Repeat UI -->
                                					<div id="addEventRepeatUI" style="display:none;width:100%">
									{if $LABEL.recurringtype neq ''}
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
										<tr>
                                                                                        <td width="30%" align=right><b>{$MOD.LBL_ENABLE_REPEAT}</b></td>
                                                                                        <td width="70%" align=left>{$ACTIVITYDATA.recurringcheck}</td>
                                                                                </tr>
										{if $ACTIVITYDATA.recurringcheck != 'No'}
										<tr>
											<td width="30%" align=right>&nbsp;</td>
											<td>{$MOD.LBL_REPEATEVENT}&nbsp;{$ACTIVITYDATA.repeat_frequency}&nbsp;{$MOD[$ACTIVITYDATA.recurringtype]}</td>
										</tr>
										<tr>
                                                                                        <td width="30%" align=right>&nbsp;</td>
                                                                                        <td>{$ACTIVITYDATA.repeat_str}</td>
                                                                                </tr>
										{/if}
									</table>
									{/if}
									</div>
									<!-- Relatedto UI -->
									<div id="addEventRelatedtoUI" style="display:none;width:100%">
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
										{if $LABEL.parent_id neq ''}
										<tr>
											<td width="30%" align=right valign="top"><b>{$LABEL.parent_id}</b></td>
											<td width="70%" align=left valign="top">{$ACTIVITYDATA.parent_name}</td>
										</tr>
										{/if}
										<tr>
											<td width="30%" valign="top" align=right><b>{$MOD.LBL_CONTACT_NAME}</b></td>	
											<td width="70%" valign="top" align=left>
											{foreach item=contactname key=cntid from=$CONTACTS}
	                                        	{$contactname.0}
	                                            {if $IS_PERMITTED_CNT_FNAME == '0'}
	                                            	&nbsp;{$contactname.1}
	                                            {/if}
	                                            <br>
                                            {/foreach}
										</tr>
									</table>
									</div>
								</td>
                					 </tr>
						     </table>
						    {else}
							<!-- detailed view of a ToDo -->
					 	     <table border="0" cellpadding="5" cellspacing="0" width="100%">
							<tr>
								<td class="cellLabel" width="20%" align="right"><b>{$MOD.LBL_TODO}</b></td>
								<td class="cellInfo" width="80%" align="left">{$ACTIVITYDATA.subject}</td>
							</tr>
							{if $LABEL.description neq ''}
							<tr>
								<td class="cellLabel" align="right" valign="top"><b>{$LABEL.description}</b></td>
                                                                <td class="cellInfo" align="left" colspan="3" valign="top" height="60px">{$ACTIVITYDATA.description}&nbsp;</td>
                					</tr>
							{/if}
							<tr>
                        					<td colspan="2" align="center" style="padding:0px">
                                				<table border="0" cellpadding="5" cellspacing="1" width="100%" >
                                       					<tr>
										{if $LABEL.taskstatus neq ''}
                                                					<td class="cellLabel" width=33% align="left"><b>{$LABEL.taskstatus}</b></td>
										{/if}
										{if $LABEL.taskpriority neq ''}
											<td class="cellLabel" width=33% align="left"><b>{$LABEL.taskpriority}</b></td>
										{/if}
										<td class="cellLabel" width=34% align="left"><b>{$LABEL.assigned_user_id}</b></td>
									</tr>
									<tr>
										{if $LABEL.taskstatus neq ''}
											<td class="cellInfo" align="left" valign="top">
											{if $ACTIVITYDATA.taskstatus eq $APP.LBL_NOT_ACCESSIBLE}
                                                                                	<font color="red">{$ACTIVITYDATA.taskstatus}</font>
											{else} {$ACTIVITYDATA.taskstatus}{/if}
                                                					</td>
										{/if}
										{if $LABEL.taskpriority neq ''}		
											<td class="cellInfo" align="left" valign="top">
											{if $ACTIVITYDATA.taskpriority eq $APP.LBL_NOT_ACCESSIBLE}
											<font color="red">{$ACTIVITYDATA.taskpriority}</font>
											{else}{$ACTIVITYDATA.taskpriority}{/if}
											</td>
										{/if}
										<td class="cellInfo" align="left" valign="top">{$ACTIVITYDATA.assigned_user_id}</td>
									</tr>
								</table>
								</td>
							</tr>
						     </table>
						     <table border="0" cellpadding="0" cellspacing="0" width="100%" align=center>
	                                                <tr><td width=50% valign=top >
								<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr><td class="mailSubHeader" align=left ><b>{$MOD.LBL_TIMEDATE}</b></td></tr>
									<tr><td class="small" >{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
									<tr><td class="cellInfo" style="padding-left:0px">{$ACTIVITYDATA.date_start}</td></tr>
								</table>
							</td>
							<td width=50% valign="top">
								<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr><td class="mailSubHeader"><b>{$LABEL.due_date}</b></td></tr>
									<tr><td class="small">{$ACTIVITYDATA.due_date}</td></tr>
									<tr><td class="cellInfo">&nbsp;</td></tr>
								</table>
							</td>
						     </table>	
						     <table border=0 cellspacing=0 cellpadding=5 width=100% >
							<tr>
								<td class="cellLabel" align=right nowrap width=20%>{if $LABEL.createdtime neq ''}<b>{$LABEL.createdtime}</b>{/if}</td>
                                                                <td class="cellInfo" align=left nowrap width=30%>{if $LABEL.createdtime neq ''}{$ACTIVITYDATA.createdtime}{/if}</td>
                                                                <td class="cellLabel" align=right nowrap width=20%>{if $LABEL.modifiedtime neq ''}<b>{$LABEL.modifiedtime}</b>{/if}</td>
                                                                <td class="cellInfo" align=left  nowrap width=30%>{if $LABEL.modifiedtime neq ''}{$ACTIVITYDATA.modifiedtime}{/if}</td>
                                                        </tr>
                                                     </table>
						     <br>
						     {if $LABEL.sendnotification neq '' || ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') } 
						     <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<table border="0" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										{if $LABEL.sendnotification neq ''}
                                                                                        {assign var='class_val' value='dvtUnSelectedCell'}
	                                                                                <td id="cellTabInvite" class="dvtSelectedCell" align="center" nowrap="nowrap"><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabRelatedto','off');dispLayer('addTaskAlarmUI');ghide('addTaskRelatedtoUI');">{$MOD.LBL_NOTIFICATION}</td></a></td>
										{else}
                                                                                        {assign var='class_val' value='dvtSelectedCell'}
                                                                                {/if}
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										{if ($LABEL.parent_id neq '') || ($LABEL.contact_id neq '') }
                                                                                <td id="cellTabRelatedto" class={$class_val} align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabRelatedto','on');dispLayer('addTaskRelatedtoUI');ghide('addTaskAlarmUI');">{$MOD.LBL_RELATEDTO}</a></td>
										{/if}

                                                                                <td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
                                                                <!-- Notification UI -->
                                                                        <DIV id="addTaskAlarmUI" style="display:block;width:100%">
									{if $LABEL.sendnotification neq ''}
									{assign var='vision' value='none'}
                                                                        <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                                <tr>
                                                                                        <td width="30%" align=right><b>{$MOD.LBL_SENDNOTIFICATION}</b></td>
                                                                                        <td width="70%" align=left>{$ACTIVITYDATA.sendnotification}</td>
                                                                                </tr>
                                                                        </table>
									{else}
                                                                        {assign var='vision' value='block'}
                                                                        {/if}
                                                                        </DIV>
									<div id="addTaskRelatedtoUI" style="display:{$vision};width:100%">
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                                <tr>
										{if $LABEL.parent_id neq ''}
                                                                                        <td width="30%" align=right><b>{$LABEL.parent_id}</b></td>
                                                                                        <td width="70%" align=left>{$ACTIVITYDATA.parent_name}</td>
										{/if}
                                                                                </tr>
                                                                                <tr>
										{if $LABEL.contact_id neq ''}
                                                                                        <td width="30%" align=right><b>{$MOD.LBL_CONTACT_NAME}</b></td>
											<td width="70%" align=left><a href="{$ACTIVITYDATA.contact_idlink}">{$ACTIVITYDATA.contact_id}</a></td>
										{/if}
                                                                                </tr>
                                                                        </table>
                                                                        </div>
								</td>
							</tr>
						     </table>
						     {/if}

                     	                      </td>
					   </tr>
                </table>
		{/if}
		<tr>
			<td style="padding:10px">
		           <table border=0 cellspacing=0 cellpadding=0 width=100%>
				     {strip}<tr nowrap>
							<td  colspan=4 style="padding:5px">
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
								{/if}
							</td>
							<td align=right>
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
                                                                {/if}
								{if $DELETE eq 'permitted'}
                                                                <input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
                                                                {/if}

							</td>
					</tr>{/strip}
			   </table>
			</td>
		</tr>
		</form>
	</table>
	</td>
	<td width=22% valign=top style="border-left:2px dashed #cccccc;padding:13px">
						<!-- right side relevant info -->

		{if $TAG_CLOUD_DISPLAY eq 'true'}
		<!-- Tag cloud display -->
		<table border=0 cellspacing=0 cellpadding=0 width=100% class="tagCloud">
		<tr>
			<td class="tagCloudTopBg"><img src="{$IMAGE_PATH}tagCloudName.gif" border=0></td>
		</tr>
		<tr>
                      	<td><div id="tagdiv" style="display:visible;"><form method="POST" action="javascript:void(0);" onsubmit="return tagvalidate();"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value="" style="width:100px;margin-left:5px;"></input>&nbsp;&nbsp;<input name="button_tagfileds" type="submit" class="crmbutton small save" value="{$APP.LBL_TAG_IT}" /></form></div></td>
                </tr>
		<tr>
			<td class="tagCloudDisplay" valign=top> <span id="tagfields">{$ALL_TAG}</span></td>
		</tr>
		</table>
		<!-- End Tag cloud display -->
		{/if}
				<br>
			</td>
		</tr>
		</table>
		
			
			
		
		</div>
		<!-- PUBLIC CONTENTS STOPS-->
	</td>
</tr>
</table>

{if $MODULE eq 'Products'}
<script language="JavaScript" type="text/javascript" src="modules/Products/Productsslide.js"></script>
<script language="JavaScript" type="text/javascript">Carousel();</script>
{/if}

<script>
function getTagCloud()
{ldelim}
new Ajax.Request(
        'index.php',
        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
        method: 'post',
        postBody: 'module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
        onComplete: function(response) {ldelim}
                                $("tagfields").innerHTML=response.responseText;
                                $("txtbox_tagfields").value ='';
                        {rdelim}
        {rdelim}
);
{rdelim}
getTagCloud();
</script>
<!-- added for validation -->
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
</td>

</tr></table>
</td></tr></table>
</td></tr></table>
</td></tr></table>
        </td></tr></table>
        </td></tr></table>
        </div>
        </td>
        <td valign=top><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
        </tr>
        </table>


