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

{*<!-- module header -->*}

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/Activity.js"></script>
<script type="text/javascript">
</script>

{*<!-- Contents -->*}
<table width="100%" cellpadding="2" cellspacing="0" border="0">
<form name="EditView" method="POST" action="index.php">
<input type="hidden" name="time_start" id="time_start">
<tr><td>&nbsp;</td>
        <td>
                <table cellpadding="0" cellspacing="5" border="0">
			{include file='EditViewHidden.tpl'}
                </table>
<table  border="0" cellpadding="5" cellspacing="0" width="100%" style="border:1px solid #cccccc">
<tr>
        <td class="lvtHeaderText" style="border-bottom:1px dotted #cccccc">

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
                        <tr><td>
		
				{if $OP_MODE eq 'edit_view'}   
					<span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} - {$APP.LBL_EDITING} {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span> <br>
					{$UPDATEINFO}	 
				{/if}
				{if $OP_MODE eq 'create_view'}
					<span class="lvtHeaderText">{$APP.LBL_CREATING} {$SINGLE_MOD}</span> <br>
				{/if}
			</td></tr>
		</table>
        </td>
</tr>
<tr><td>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
        <tr>
                <td valign=top align=left >
                           <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                <tr>
					<td align=left>
					<!-- content cache -->

					<table border=0 cellspacing=0 cellpadding=0 width=100%>
					  <tr>
					     <td style="padding:10px">
						     <!-- General details -->
						     <table border=0 cellspacing=0 cellpadding=0 width=100% >
						     <tr>
							<td  colspan=4 style="padding:5px">
								<div align="center">
								<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" {if $ACTIVITY_MODE neq 'Task'} onclick="this.form.action.value='Save';  displaydeleted();return maincheck_form();"{else} onclick="this.form.action.value='Save';  displaydeleted(); maintask_check_form();return formValidate();" {/if} type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
								<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
								</div>
							</td>
						     </tr>
						     </table>
						     <!-- included to handle the edit fields based on ui types -->
						     {foreach key=header item=data from=$BLOCKS}
						     <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
						     <tr>
							<td colspan=4 style="border-bottom:1px solid #999999;padding:5px;" bgcolor="#e5e5e5">
								<b>{$header}</b>
							</td>
						     </tr>
						     </table>
						     {/foreach}
						     {if $ACTIVITY_MODE neq 'Task'}
							<input type="hidden" name="time_end" id="time_end">
							<input type=hidden name="inviteesid" id="inviteesid" value="">
							<input type="hidden" name="duration_hours" value="0">
							<input type="hidden" name="duration_minutes" value="0">
						     <table border=0 cellspacing=0 cellpadding=5 width=100% >
							<tr>
								<td nowrap  width=20% align="right"><b>{$MOD.LBL_EVENTTYPE}</b></td>
								<td width=80% align="left">
									<table>
										<tr>
										{foreach key=tyeparrkey item=typearr from=$ACTIVITYDATA.activitytype}
                                                                                {foreach key=sel_value item=value from=$typearr}
                                                                                {if $value eq 'selected' && $sel_value eq 'Meeting'}
                                                                                        {assign var='meetcheck' value='checked'}
                                                                                        {assign var='callcheck' value=''}
                                                                                {else}
                                                                                        {assign var='meetcheck' value=''}
                                                                                        {assign var='callcheck' value='checked'}
                                                                                {/if}
                                                                                {/foreach}
                                                                                {/foreach}
											<td><input type="radio" name='activitytype' value='Call' style='vertical-align: middle;' {$callcheck}></td><td>{$APP.Call}</td><td style="width:10px">
											<td><input type="radio" name='activitytype' value='Meeting' style='vertical-align: middle;' {$meetcheck}></td><td>{$APP.Meeting}</td><td style="width:20px">
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td nowrap align="right"><b>{$MOD.LBL_EVENTNAME}</b></td>
								<td align="left"><input name="subject" type="text" class="textbox" value="{$ACTIVITYDATA.subject}" style="width:50%">&nbsp;&nbsp;&nbsp;
								{foreach key=key_one item=arr from=$ACTIVITYDATA.visibility}
                                                                        {foreach key=sel_value item=value from=$arr}
                                                                        {if $value eq 'selected' && $sel_value eq 'Public'}
                                                                                {assign var="visiblecheck" value="checked"}
                                                                        {else}
                                                                                {assign var="visiblecheck" value=""}
                                                                        {/if}
                                                                        {/foreach}
                                                                        {/foreach}
                                                                        <input name="visibility" value="Public" type="checkbox" {$visiblecheck}>{$MOD.LBL_PUBLIC}
								</td>
							</tr>
							<tr>
                        					<td valign="top" nowrap align="right"><b>{$LABEL.description}</b></td>
								<td align="left"><textarea style="width:100%; height : 60px;" name="description">{$ACTIVITYDATA.description}</textarea></td>
                					</tr>
							<tr>
								<td colspan=2 width=80% align="center">
                                				<table border=0 cellspacing=0 cellpadding=3 width=80%>
                                        				<tr>
										<td ><b>{$LABEL.eventstatus}</b></td>
										<td ><b>{$LABEL.assigned_user_id}</b></td>
									</tr>
									<tr>
										<td valign=top>
                                                                                <select name="eventstatus" id="eventstatus" class=small>
                                                                                        {foreach item=arr from=$ACTIVITYDATA.eventstatus}
                                                                                        {foreach key=sel_value item=value from=$arr}
                                                                                        <option value="{$sel_value}" {$value}>
                                                                                                {if $APP[$sel_value] neq ''}
                                                                                                {$APP[$sel_value]}
                                                                                                {else}
                                                                                                        {$sel_value}
                                                                                                {/if}
                                                                                        </option>
                                                                                        {/foreach}
                                                                                        {/foreach}
                                                                                </select>
                                                                        	</td>
										<td valign=top rowspan=2>
											{assign var=check value=1}
                                        						{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
                                                					{foreach key=sel_value item=value from=$arr}
                                                        					{if $value ne ''}
                                                                					{assign var=check value=$check*0}
                                                        					{else}
                                                                					{assign var=check value=$check*1}
                                                        					{/if}
                                                					{/foreach}
                                        						{/foreach}

                                        						{if $check eq 0}
                                                						{assign var=select_user value='checked'}
                                                						{assign var=style_user value='display:block'}
                                                						{assign var=style_group value='display:none'}
                                        						{else}
                                                						{assign var=select_group value='checked'}
                                                						{assign var=style_user value='display:none'}
                                                						{assign var=style_group value='display:block'}
                                        						{/if}
                                        						<input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_USER}
                                        						{if $secondvalue.assigned_user_id neq ''}
                                                					<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_GROUP}
                                        						{/if}
											<span id="assign_user" style="{$style_user}">
                                     				           			<select name="assigned_user_id">
                                                        					{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
                                                                				{foreach key=sel_value item=value from=$arr}
                                                                        				<option value="{$key_one}" {$value}>{$sel_value}</option>
                                                                				{/foreach}
                                                        					{/foreach}
                                                			   			</select>
                                        			       			</span>

                                        						{if $secondvalue.assigned_user_id neq ''}
                                                					<span id="assign_team" style="{$style_group}">
                                                        					<select name="assigned_group_name" >';
                                                                				{foreach key=key_one item=arr from=$secondvalue.assigned_user_id}
                                                                        			{foreach key=sel_value item=value from=$arr}
                                                                                			<option value="{$sel_value}" {$value}>{$sel_value}</option>
                                                                        			{/foreach}
                                                                				{/foreach}
                                                        					</select>
                                                					</span>
                                        						{/if}
											<br><input type="checkbox" name="sendnotification" >&nbsp;{$LABEL.sendnotification}
										</td>
									</tr>
									<tr>
										<td valign=top><b>{$LABEL.taskpriority}</b>
										<br>
										<select name="taskpriority" id="taskpriority">
                                                                                        {foreach item=arr from=$ACTIVITYDATA.taskpriority}
                                                                                        {foreach key=sel_value item=value from=$arr}
                                                                                        <option value="{$sel_value}" {$value}>
                                                                                                {if $APP[$sel_value] neq ''}
                                                                                                {$APP[$sel_value]}
                                                                                                {else}
                                                                                                        {$sel_value}
                                                                                                {/if}
                                                                                        </option>
                                                                                        {/foreach}
                                                                                        {/foreach}
                                                                                </select>
										</td> 
										
									</tr>
								</table>
							</td></tr>
						     </table>
						     <hr noshade size=1>
						     <table border=0 cellspacing=0 cellpadding=5 width=90% align=center bgcolor="#FFFFFF">
							<tr>
								<td >
									<table border=0 cellspacing=0 cellpadding=2 width=100% align=center>
									<tr><td width=50% valign=top style="border-right:1px solid #dddddd">
										<table border=0 cellspacing=0 cellpadding=2 width=90% align=center>
											<tr><td colspan=3 ><b>{$MOD.LBL_EVENTSTAT}</b></td></tr>
											<tr><td colspan=3>{$STARTHOUR}</td></tr>
											<tr><td>
												{foreach key=date_value item=time_value from=$ACTIVITYDATA.date_start}
                                                                                                        {assign var=date_val value="$date_value"}
                                                                                                        {assign var=time_val value="$time_value"}
	                                                                                        {/foreach}
                                                                                                <input type="text" name="date_start" id="jscal_field_date_start" class="textbox" style="width:90px" value="{$date_val}"></td><td width=100%><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="Set date.." title="Set date.." id="jscal_trigger_date_start">
													{foreach key=date_fmt item=date_str from=$secondvalue.date_start}
													{assign var=date_vl value="$date_fmt"}
													{/foreach}
													<script type="text/javascript">
														Calendar.setup ({ldelim}
														inputField : "jscal_field_date_start", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
														{rdelim})
													</script>
											</td></tr>
										</table></td>
										<td width=50% valign=top >
											<table border=0 cellspacing=0 cellpadding=2 width=90% align=center>
												<tr><td colspan=3><b>{$MOD.LBL_EVENTEDAT}</b></td></tr>
												<tr><td colspan=3>{$ENDHOUR}
												</td></tr>
												<tr><td>
													{foreach key=date_value item=time_value from=$ACTIVITYDATA.due_date}
													{assign var=date_val value="$date_value"}
													{assign var=time_val value="$time_value"}
													{/foreach}
													<input type="text" name="due_date" id="jscal_field_due_date" class="textbox" style="width:90px" value="{$date_val}"></td><td width=100%><img border=0 src="{$IMAGE_PATH}btnL3Calendar.gif" alt="Set date.." title="Set date.." id="jscal_trigger_due_date">
													{foreach key=date_fmt item=date_str from=$secondvalue.due_date}
													{assign var=date_vl value="$date_fmt"}
                                                                                                        {/foreach}
													<script type="text/javascript">
														Calendar.setup ({ldelim}
														inputField : "jscal_field_due_date", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_due_date", singleClick : true, step : 1
														{rdelim})
													</script>
												</td></tr>
											</table>
										</td>
									</tr>
								</table></td>
							</tr>
						     </table>
						     <br>
						     <table border=0 cellspacing=0 cellpadding=0 width=100% align=center>
							<tr><td>
								<table border=0 cellspacing=0 cellpadding=3 width=100%>
									<tr>
										<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
										<td id="cellTabInvite" class="dvtSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');gshow('addEventInviteUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_INVITE}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');gshow('addEventAlarmUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REMINDER}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRepeatUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRelatedtoUI');">{$MOD.LBL_REPEAT}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','on');ghide('addEventAlarmUI');ghide('addEventInviteUI');gshow('addEventRelatedtoUI','',document.EditView.date_start.value,document.EditView.due_date.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value,document.EditView.endhr.value,document.EditView.endmin.value,document.EditView.endfmt.value);ghide('addEventRepeatUI');">{$MOD.LBL_RELATEDTO}</a></td>
										<td class="dvtTabCache" style="width:100%">&nbsp;</td>
									</tr>
								</table>
							</td></tr>
							<tr>
								<td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
								<!-- Invite UI -->
									<DIV id="addEventInviteUI" style="display:block;width:100%">
									<table border=0 cellspacing=0 cellpadding=2 width=100%>
										<tr>
											<td valign=top> 
												<table border=0 cellspacing=0 cellpadding=2 width=100%>
													<tr><td colspan=3>
														<ul style="padding-left:20px">
														<li>{$MOD.LBL_INVITE_INST1}
														<li>{$MOD.LBL_INVITE_INST2}
														</ul>
													</td></tr>
													<tr>
														<td><b>{$MOD.LBL_AVL_USERS}</b></td>
														<td>&nbsp;</td>
														<td><b>{$MOD.LBL_SEL_USERS}</b></td>
													</tr>
													<tr>
														<td width=40% align=center valign=top>
														<select name="availableusers" id="availableusers" class=small size=5 multiple style="height:70px;width:100%">
														{foreach item=username key=userid from=$USERSLIST}
														{if $userid != ''}
														<option value="{$userid}">{$username}</option>
														{/if}
														{/foreach}
														</select>
														</td>
														<td width=20% align=center valign=top>
														<input type=button value="{$MOD.LBL_ADD_BUTTON} >>" class="crm button small save" style="width:100%" onClick="addColumn()"><br>
														<input type=button value="<< {$MOD.LBL_RMV_BUTTON} " class="crm button small cancel" style="width:100%" onClick="delColumn()">
														</td>
														<td width=40% align=center valign=top>
														<select name="selectedusers" id="selectedusers" class=small size=5 multiple style="height:70px;width:100%">
														{foreach item=username key=userid from=$INVITEDUSERS}
														{if $userid != ''}
														<option value="{$userid}">{$username}</option>
                                                                                                                {/if}
                                                                                                                {/foreach}
														</select>
														<div align=left> {$MOD.LBL_SELUSR_INFO}
														</div>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</DIV>
									<!-- Reminder UI -->	
									<DIV id="addEventAlarmUI" style="display:none;width:100%">
										<table>
											{assign var=secondval value=$secondvalue.reminder_time}
											{assign var=check value=$secondval[0]}
											{assign var=yes_val value=$secondval[1]}
											{assign var=no_val value=$secondval[2]}
											
											<tr><td>{$LABEL.reminder_time}</td><td>
											<input type="radio" name="set_reminder" value="Yes" {$check} onClick="showBlock('reminderOptions')">&nbsp;{$yes_val}&nbsp;
											<input type="radio" name="set_reminder" value="No" onClick="fnhide('reminderOptions')">&nbsp;{$no_val}&nbsp;
											</td></tr>
										</table>
									{if $check eq 'CHECKED'}
										{assign var=reminstyle value='style="display:block;width:100%"'}
									{else}
										{assign var=reminstyle value='style="display:none;width:100%"'}
									{/if}
									<DIV id="reminderOptions" {$reminstyle}>
										<table border=0 cellspacing=0 cellpadding=2  width=100%>
											<tr>
												<td nowrap align=right width=20% valign=top><b>{$MOD.LBL_RMD_ON} : </b></td>
												<td width=80%>
													<table border=0>
													<tr>
														<td colspan=2>
														{foreach item=val_arr from=$ACTIVITYDATA.reminder_time}
														{assign var=start value="$val_arr[0]"}
														{assign var=end value="$val_arr[1]"}
														{assign var=sendname value="$val_arr[2]"}
														{assign var=disp_text value="$val_arr[3]"}
														{assign var=sel_val value="$val_arr[4]"}
														<select name="{$sendname}">
														{section name=reminder start=$start max=$end loop=$end step=1 }
														{if $smarty.section.reminder.index eq $sel_val}
														{assign var=sel_value value="SELECTED"}
														{else}
														{assign var=sel_value value=""}
														{/if}
														<OPTION VALUE="{$smarty.section.reminder.index}" "{$sel_value}">{$smarty.section.reminder.index}</OPTION>
														{/section}
														</select>
														&nbsp;{$disp_text}
														{/foreach}
														</td>
													</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td nowrap align=right>
													{$MOD.LBL_SDRMD} :
												</td>
												<td >
													<input type=text name="toemail" class=textbox style="width:90%" value="{$USEREMAILID}">
												</td>
											</tr>
										</table>
									</DIV>
									</DIV>
									<!-- Repeat UI -->
									<div id="addEventRepeatUI" style="display:none;width:100%">

									<table border=0 cellspacing=0 cellpadding=2  width=100%>
										<tr>
											<td nowrap align=right width=20% valign=top>
												<strong>{$MOD.LBL_REPEAT} :</strong>
											</td>
											<td nowrap width=80% valign=top>
												<table border=0 cellspacing=0 cellpadding=0>
												<tr>
							
													<td width=20>
													{if $ACTIVITYDATA.recurringcheck eq 'Yes'}
														{assign var=rptstyle value='style="display:block"'}
														{if $ACTIVITYDATA.eventrecurringtype eq 'Daily'}
															{assign var=rptmonthstyle value='style="display:none"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Weekly'}
															{assign var=rptmonthstyle value='style="display:none"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Monthly'}
															{assign var=rptmonthstyle value='style="display:block"'}
														{elseif $ACTIVITYDATA.eventrecurringtype eq 'Yearly'}
															{assign var=rptmonthstyle value='style="display:none"'}
														{/if}
													<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')" checked>
													{else}
														{assign var=rptstyle value='style="display:none"'}
														{assign var=rptmonthstyle value='style="display:none"'}
													<input type="checkbox" name="recurringcheck" onClick="showhide('repeatOptions')">
													{/if}
													</td>
													<td colspan=2>Enable Repeat</td>
												</tr>
												<tr>
													<td colspan=2>
													<div id="repeatOptions" {$rptstyle}>
													<table border=0 cellspacing=0 cellpadding=2>
													<tr>
													<td>Repeat once in every</td>
													<td><input type="text" name="repeat_frequency" class="textbox" style="width:20px" value="{$ACTIVITYDATA.repeat_frequency}" ></td>
													<td><select name="recurringtype">
													<option value="Daily" onClick="ghide('repeatWeekUI'); ghide('repeatMonthUI');" {if $ACTIVITYDATA.eventrecurringtype eq 'Daily'} selected {/if}>{$MOD.LBL_DAYS}</option>
													<option value="Weekly" onClick="gshow('repeatWeekUI'); ghide('repeatMonthUI');" {if $ACTIVITYDATA.eventrecurringtype eq 'Weekly'} selected {/if}>{$MOD.LBL_WEEKS}</option>
												<option value="Monthly" onClick="ghide('repeatWeekUI'); gshow('repeatMonthUI');" {if $ACTIVITYDATA.eventrecurringtype eq 'Monthly'} selected {/if}>{$MOD.LBL_MONTHS}</option>
													<option value="Yearly" onClick="ghide('repeatWeekUI'); ghide('repeatMonthUI');"; {if $ACTIVITYDATA.eventrecurringtype eq 'Yearly'} selected {/if}>{$MOD.LBL_YEAR}</option>
													</select>
													</td>
												</tr>
												</table>
												<div id="repeatWeekUI" style="display:none;">
												<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td><input name="sun_flag" value="sunday" type="checkbox"></td><td>Sun</td>
													<td><input name="mon_flag" value="monday" type="checkbox"></td><td>Mon</td>
													<td><input name="tue_flag" value="tuesday" type="checkbox"></td><td>Tue</td>
													<td><input name="wed_flag" value="wednesday" type="checkbox"></td><td>Wed</td>
													<td><input name="thu_flag" value="thursday" type="checkbox"></td><td>Thu</td>
													<td><input name="fri_flag" value="friday" type="checkbox"></td><td>Fri</td>
													<td><input name="sat_flag" value="saturday" type="checkbox"></td><td>Sat</td>
												</tr>
												</table>
												</div>
	
												<div id="repeatMonthUI" {$rptmonthstyle}>
												<table border=0 cellspacing=0 cellpadding=2>
												<tr>
													<td>
														<table border=0 cellspacing=0 cellpadding=2>
														<tr>
														<td><input type="radio" checked name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'date'} checked {/if} value="date"></td><td>on</td><td><input type="text" class=textbox style="width:20px" value="{$ACTIVITYDATA.repeatMonth_date}" name="repeatMonth_date" ></td><td>day of the month</td>
														</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td>
														<table border=0 cellspacing=0 cellpadding=2>
														<tr><td>
														<input type="radio" name="repeatMonth" {if $ACTIVITYDATA.repeatMonth eq 'day'} checked {/if} value="day"></td>
														<td>on</td>
														<td>
														<select name="repeatMonth_daytype">
															<option value="first" {if $ACTIVITYDATA.repeatMonth_daytype eq 'first'} selected {/if}>First</option>
															<option value="last" {if $ACTIVITYDATA.repeatMonth_daytype eq 'last'} selected {/if}>Last</option>
														</select>
														</td>
														<td>
														<select name="repeatMonth_day">
															<option value=1 {if $ACTIVITYDATA.repeatMonth_day eq 1} selected {/if}>{$MOD.LBL_DAY1}</option>
															<option value=2 {if $ACTIVITYDATA.repeatMonth_day eq 2} selected {/if}>{$MOD.LBL_DAY2}</option>
															<option value=3 {if $ACTIVITYDATA.repeatMonth_day eq 3} selected {/if}>{$MOD.LBL_DAY3}</option>
															<option value=4 {if $ACTIVITYDATA.repeatMonth_day eq 4} selected {/if}>{$MOD.LBL_DAY4}</option>
															<option value=5 {if $ACTIVITYDATA.repeatMonth_day eq 5} selected {/if}>{$MOD.LBL_DAY5}</option>
															<option value=6 {if $ACTIVITYDATA.repeatMonth_day eq 6} selected {/if}>{$MOD.LBL_DAY6}</option>
														</select>
														</td>
														</tr>
														</table>
													</td>
												</tr>
												</table>
												</div>
								
											</div>
										</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						</div>
						<div id="addEventRelatedtoUI" style="display:none;width:100%">
						<table width="100%" cellpadding="5" cellspacing="0" border="0">
							<tr>
								<td><b>{$MOD.LBL_RELATEDTO} :</b></td>
								<td>
									<input name="parent_id" type="hidden" value="{$secondvalue.parent_id}">
									<select name="parent_type" class="small" id="parent_type" onChange="document.EditView.parent_name.value='';document.EditView.parent_id.value=''">
									{section name=combo loop=$LABEL.parent_id}
										<option value="{$fldlabel_combo.parent_id[combo]}" {$fldlabel_sel.parent_id[combo]}>{$LABEL.parent_id[combo]}</option>
									{/section}
                                             				</select>
								</td>
								<td>
									<div id="eventrelatedto" align="left">
										<input name="parent_name" readonly type="text" class="calTxt small" value="{$ACTIVITYDATA.parent_id}">
										<input type="button" name="selectparent" class="crmButton small edit" value="Select" onclick="return window.open('index.php?module='+document.EditView.parent_type.value+'&action=Popup','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
									</div>
								</td>
							</tr>
							<tr>
								<td><b>{$APP.Contacts} :</b></td>
								<td colspan="2">
									<input name="contactidlist" id="contactidlist" value="{$CONTACTSID}" type="hidden">
									<textarea rows="5" name="contactlist" readonly="readonly" class="calTxt">
									{$CONTACTSNAME}
									</textarea>&nbsp;
									<input type="button" onclick="return window.open('index.php?module=Contacts&action=Popup&return_module=Calendar&popuptype=detailview&select=enable&form=EditView&form_submit=false','test','width=640,height=602,resizable=0,scrollbars=0');" class="crmButton small edit" name="selectcnt" value="Select Contacts">
								</td>
							</tr>
						</table>
					</div>
			</td>
		</tr>
		</table>
		<!-- Alarm, Repeat, Invite stops-->
		{else}
		<table border="0" cellpadding="5" cellspacing="0" width="100%">
			<tr>
                        	<td width="20%" align="right"><b>{$MOD.LBL_TODO}</b></td>
                        	<td width="80%" align="left"><input name="subject" value="{$ACTIVITYDATA.subject}" class="textbox" style="width: 70%;" type="text"></td>
           		</tr>
			<tr>
				<td align="right"><b>{$LABEL.description}</b></td>
				<td align="left"><textarea style="width: 90%; height: 60px;" name="description">{$ACTIVITYDATA.description}</textarea>
			</tr>
			<tr>
		    		<td colspan="2" align="center" width="80%">
					<table border="0" cellpadding="3" cellspacing="0" width="80%">
            					<tr>
							<td align="left"><b>{$LABEL.taskstatus}</td>
              						<td align="left"><b>{$LABEL.taskpriority}</b></td>
              						<td align="left"><b>{$LABEL.assigned_user_id}</b></td>
						</tr>
						<tr>
							<td align="left" valign="top">
								<select name="taskstatus" id="taskstatus" class=small>
                                        			{foreach item=arr from=$ACTIVITYDATA.taskstatus}
                                        			{foreach key=sel_value item=value from=$arr}
                                                			<option value="{$sel_value}" {$value}>
                                                        		{if $APP[$sel_value] neq ''}
                                                                		{$APP[$sel_value]}
                                                        		{else}
                                                                		{$sel_value}
                                                        		{/if}
                                                			</option>
                                        			{/foreach}
                                        			{/foreach}
                                				</select>
							</td>
							<td align="left" valign="top">
								<select name="taskpriority" id="taskpriority" class=small>
        			                                {foreach item=arr from=$ACTIVITYDATA.taskpriority}
                                			        {foreach key=sel_value item=value from=$arr}
			                                                <option value="{$sel_value}" {$value}>
                        		                                {if $APP[$sel_value] neq ''}
                                        		                        {$APP[$sel_value]}
                                                       			{else}
                                                                		{$sel_value}
                                                        		{/if}
                                                			</option>
                                        			{/foreach}
                                        			{/foreach}
                                				</select>
							</td>
							<td align="left" valign="top">
								{assign var=check value=1}
                                        			{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
			                                        {foreach key=sel_value item=value from=$arr}
                        		                              	{if $value ne ''}
                                        		                      	{assign var=check value=$check*0}
                                                        		{else}
                                                                		{assign var=check value=$check*1}
                                                        		{/if}
                                                		{/foreach}
                                        			{/foreach}
								{if $check eq 0}
                                             				{assign var=select_user value='checked'}
                                                			{assign var=style_user value='display:block'}
                                                			{assign var=style_group value='display:none'}
                                        			{else}
                                                			{assign var=select_group value='checked'}
                                                			{assign var=style_user value='display:none'}
                                                			{assign var=style_group value='display:block'}
                                        			{/if}
				                                <input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)">&nbsp;User
				                                {if $secondvalue.assigned_user_id neq ''}
                                			        <input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;Group
                                        			{/if}
                                        			<span id="assign_user" style="{$style_user}">
                                                		<select name="assigned_user_id" class=small>
                                                        	{foreach key=key_one item=arr from=$ACTIVITYDATA.assigned_user_id}
				                                {foreach key=sel_value item=value from=$arr}
                                		                	<option value="{$key_one}" {$value}>{$sel_value}</option>
								{/foreach}
                                                        	{/foreach}
                                                		</select>
								</span>
								{if $secondvalue.assigned_user_id neq ''}
                                                		<span id="assign_team" style="{$style_group}">
                                                        		<select name="assigned_group_name" class=small>';
                                                                		{foreach key=key_one item=arr from=$secondvalue.assigned_user_id}
                                                                       		{foreach key=sel_value item=value from=$arr}
                                                                               		<option value="{$sel_value}" {$value}>{$sel_value}</option>
                                                                       		{/foreach}
                                                                		{/foreach}
                                                        		</select>
				                                </span>
                                				{/if}	
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan="2">    <hr noshade="noshade" size="1"></td></tr>
			</table>
			<table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="90%" align=center>
			<tr><td>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" align=center>
				<tr><td width=50% valign=top style="border-right:1px solid #dddddd">
					<table border=0 cellspacing=0 cellpadding=2 width=90% align=center>
						<tr><td colspan=3 ><b>{$MOD.LBL_TODODATETIME}</b></td></tr>
						<tr><td colspan=3>{$STARTHOUR}</td></tr>
						<tr><td>
							{foreach key=date_value item=time_value from=$ACTIVITYDATA.date_start}
	                                        		{assign var=date_val value="$date_value"}
								{assign var=time_val value="$time_value"}
                                        		{/foreach}
							<input name="date_start" id="date_start" class="textbox" style="width: 90px;" value="{$date_val}" type="text"></td><td width=100%><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="Set date.." title="Set date.." id="jscal_trigger_date_start" align="middle" border="0">
							{foreach key=date_fmt item=date_str from=$secondvalue.date_start}
								{assign var=date_vl value="$date_fmt"}
							{/foreach}				
							<script type="text/javascript">
								Calendar.setup ({ldelim}
	        	                                	inputField : "date_start", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
							{rdelim})
							</script>
						</td></tr>
					</table></td>
					<td width=50% valign="top">
                                                <table border="0" cellpadding="2" cellspacing="0" width="90%" align=center>
							<tr><td colspan=3><b>{$LABEL.due_date}</b></td></tr>
							<tr><td>
								{foreach key=date_value item=time_value from=$ACTIVITYDATA.due_date}
									{assign var=date_val value="$date_value"}
									{assign var=time_val value="$time_value"}
								{/foreach}
								<input name="due_date" id="due_date" class="textbox" style="width: 90px;" value="{$date_val}" type="text"></td><td width=100%><img src="{$IMAGE_PATH}btnL3Calendar.gif" alt="Set date.." title="Set date.." id="jscal_trigger_due_date" border="0">
								{foreach key=date_fmt item=date_str from=$secondvalue.due_date}
                                                			{assign var=date_vl value="$date_fmt"}
                                        			{/foreach}
				      				<script type="text/javascript">
								Calendar.setup ({ldelim}
	                                        			inputField : "date_start", ifFormat : "{$date_vl}", showsTime : false, button : "jscal_trigger_due_date", singleClick : true, step : 1
					   			{rdelim})
								</script>
        						</td></tr>
						</table></td>
					</tr>
				</table>
				</td></tr>
                	<tr><td>&nbsp;</td></tr>
        	</table>
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" bgcolor="#FFFFFF">
			<tr>
				<td>
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
							<td id="cellTabInvite" class="dvtSelectedCell" align="center" nowrap="nowrap"><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabRelatedto','off');Taskshow('addTaskAlarmUI','todo',document.EditView.date_start.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value);ghide('addTaskRelatedtoUI');">{$MOD.LBL_NOTIFICATION}</a></td>
							<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;
                                                        <td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabRelatedto','on');Taskshow('addTaskRelatedtoUI','todo',document.EditView.date_start.value,document.EditView.starthr.value,document.EditView.startmin.value,document.EditView.startfmt.value);ghide('addTaskAlarmUI');">{$MOD.LBL_RELATEDTO}</a></td>
                                                        <td class="dvtTabCache" style="width:100%">
						</tr>

					</table>
				</td>
			</tr>
			<tr>
				<td class="dvtContentSpace" style="padding: 10px; height: 120px;" align="left" valign="top" width="100%">
			<!-- Reminder UI -->
			<div id="addTaskAlarmUI" style="display: block; width: 100%;">
                	<table>
				<tr><td>{$LABEL.sendnotification}</td>
					{if $ACTIVITYDATA.sendnotification eq 1}
                                        <td>
                                                <input name="sendnotification" type="checkbox" checked>
                                        </td>
                                	{else}
                                        <td>
                                                <input name="sendnotification" type="checkbox">
                                        </td>
                                	{/if}
				</tr>
			</table>
			</div>
			<div id="addTaskRelatedtoUI" style="display:none;width:100%">
           		     <table width="100%" cellpadding="5" cellspacing="0" border="0">
                	     <tr>
                        	     <td><b>{$MOD.LBL_RELATEDTO} :</b></td>
                                     <td>
					<input name="parent_id" type="hidden" value="{$secondvalue.parent_id}">
                                             <select name="parent_type" class="small" id="parent_type" onChange="document.EditView.parent_name.value='';document.EditView.parent_id.value=''">
							{section name=combo loop=$LABEL.parent_id}
								<option value="{$fldlabel_combo.parent_id[combo]}" {$fldlabel_sel.parent_id[combo]}>{$LABEL.parent_id[combo]}</option>
							{/section}
					     </select>
                                     </td>
                                     <td>
                              	        <div id="taskrelatedto" align="left">
						<input name="parent_name" readonly type="text" class="calTxt small" value="{$ACTIVITYDATA.parent_id}">
						<input type="button" name="selectparent" class="crmButton small edit" value="Select" onclick="return window.open('index.php?module='+document.EditView.parent_type.value+'&action=Popup','test','width=640,height=602,resizable=0,scrollbars=0,top=150,left=200');">
					 </div>
                                     </td>
			     </tr>
			     <tr>
                                     <td><b>{$LABEL.contact_id} :</b></td>
				     <td colspan="2">
						<input name="contact_name" readonly type="text" class="calTxt" value="{$ACTIVITYDATA.contact_id}"><input name="contact_id" type="hidden" value="{$secondvalue.contact_id}">&nbsp;
						<input type="button" onclick="return window.open('index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView','test','width=640,height=602,resizable=0,scrollbars=0');" class="crmButton small edit" name="selectcnt" value="Select Contact">
				     </td>
                             </tr>
		</table>
              	</div>
                </td></tr></table>

		{/if}
			</td></tr>
			<tr>
				<td  colspan=4 style="padding:5px">
					<div align="center">
                        	        	<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmbutton small save" {if $ACTIVITY_MODE neq 'Task'} onclick="this.form.action.value='Save';  displaydeleted();return maincheck_form();"{else} onclick="this.form.action.value='Save';  displaydeleted(); maintask_check_form();return formValidate();" {/if} type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
						<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmbutton small cancel" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
					</div>
				</td>
			</tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
</td></tr>
</form></table>
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
<script>
{if $ACTIVITY_MODE eq 'Task'}
	var fieldname = new Array('subject','date_start','time_start','taskstatus');
	var fieldlabel = new Array('Subject','Date','Time','Status');
	var fielddatatype = new Array('V~M','D~M~time_start','T~O','V~O');
{else}
	var fieldname = new Array('subject','date_start','due_date','taskpriority','sendnotification','parent_id','contact_id','reminder_time','recurringtype');
	var fieldlabel = new Array('Subject','Start Date','Due Date','Priority','Send Notification','Related To','Contact Name','Send Reminder','Recurrence');
	var fielddatatype = new Array('V~M','D~M','D~M~OTH~GE~date_start~Start Date','V~O','C~O','I~O','I~O','I~O','O~O');
{/if}
</script>
<script>	
	var ProductImages=new Array();
	var count=0;

	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
	{rdelim}

	function displaydeleted()
	{ldelim}
		var imagelists='';
		for(var x = 0; x < ProductImages.length; x++)
		{ldelim}
			imagelists+=ProductImages[x]+'###';
		{rdelim}

		if(imagelists != '')
			document.EditView.imagelist.value=imagelists
	{rdelim}

</script>
<script language="JavaScript" type="text/JavaScript">
	setObjects();
</script>
