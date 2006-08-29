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
<script type="text/javascript" src="modules/{$MODULE}/Activity.js"></script>
<script type="text/javascript" src="include/js/reflection.js"></script>
<script src="include/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
   <a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>

<script>
function tagvalidate()
{ldelim}
	if(document.getElementById('txtbox_tagfields').value != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
	else
	{ldelim}
		alert("Please enter a tag");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id)
{ldelim}
	$("vtbusy_info").style.display="inline";
	Effect.Fade('tag_'+id);
	new Ajax.Request(
		'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&tagid=" +id,
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
<table  border="0" cellpadding="5" cellspacing="0" width="100%" style="border:1px solid #cccccc">
<tr>
	<td class="lvtHeaderText" style="border-bottom:1px dotted #cccccc">
	
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
			<tr><td>		
				 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span></td><td>&nbsp;
			</td></tr>
			 <tr height=20><td>{$UPDATEINFO}</td></tr>
		 </table>
	</td>
</tr>
<tr><td>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
		<td valign=top align=left >
                           <table border=0 cellspacing=0 cellpadding=3 width=100%>
				<tr>
					<td align=left>
					<!-- content cache -->
					
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                  <tr>
					     <td style="padding:10px">
						     <!-- General details -->
				                     <table border=0 cellspacing=0 cellpadding=0 width=100%>
						     <tr nowrap>
							<td  colspan=4 style="padding:5px">
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
                                                                <input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
                                                                {/if}
								{if $DELETE eq 'permitted'}
                                                                <input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
                                                                {/if}

							</td>
						     </tr>
						     </table>
						     {foreach key=header item=detail from=$BLOCKS}
						     <table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
						     	<tr>{strip}
						     		<td colspan=4 style="border-bottom:1px solid #999999;padding:5px;" bgcolor="#e5e5e5">
									<b>{$header}</b>
								</td>{/strip}
					             	</tr>
						     </table>
						     {/foreach}
						     {if $ACTIVITYDATA.activitytype neq 'Task'}	
						     <table border=0 cellspacing=0 cellpadding=5 width=100% >
               						 <tr>
					                        <td width="20%" align="right"><b>{$MOD.LBL_EVENTTYPE}</b></td>
								<td width="30%"align="left">{$ACTIVITYDATA.activitytype}</td>
								<td width="20%" align="right">&nbsp;</td>
								<td width="30%" align="left">&nbsp;</td>
							 </tr>
							 <tr>
                        					<td width="20%" align="right"><b>{$MOD.LBL_EVENTNAME}</b></td>
					                        <td width="30%" align="left" >{$ACTIVITYDATA.subject}</td>
								<td width="20%" align="right"><b>{$LABEL.visibility}</b></td>
                                                                <td width="30%" align="left" >{$ACTIVITYDATA.visibility}</td>
             						 </tr>
							 <tr>
								<td align="right" width="20%" nowrap valign="top"><b>{$LABEL.description}</b></td>
								<td valign="top" align="left" colspan="3" height="60px">{$ACTIVITYDATA.description}</td>
							 </tr>
							 <tr>
								<td colspan=2 width=80% align="center">
								<table border=0 cellspacing=0 cellpadding=3 width=80%>
									<tr>
										<td ><b>{$LABEL.eventstatus}</b></td>
										<td ><b>{$LABEL.assigned_user_id}</b></td>
									</tr>
									<tr>
										<td>{$ACTIVITYDATA.eventstatus}</td>
										<td>{$ACTIVITYDATA.assigned_user_id}</td>
									</tr>
									<tr>
										<td ><b>{$LABEL.taskpriority}</b></td>
										<td ><b>{$LABEL.sendnotification}</b></td>
									</tr>
									<tr>
										<td >{$ACTIVITYDATA.taskpriority}</td>
										<td >{$ACTIVITYDATA.sendnotification}</td>
									</tr>
								</table>
								</td>
							</tr>
                                                        <tr>
                                                                <td width="20%" align="right"><b>{$LABEL.createdtime}</b></td>
                                                                <td width="30%" align="left">{$ACTIVITYDATA.createdtime}</td>
                                                                <td width="20%" align="right"><b>{$LABEL.modifiedtime}</b></td>
                                                                <td width="30%" align="left">{$ACTIVITYDATA.modifiedtime}</td>
                                                        </tr>
                                                     </table>
						     <hr noshade size=1>
						     <table border=0 cellspacing=0 cellpadding=5 width=90% align=center bgcolor="#FFFFFF">
							<tr>
								<td >
									<table border=0 cellspacing=0 cellpadding=2 width=100%>
									<tr><td width=50% valign=top style="border-right:1p
x solid #dddddd">
										 <table border=0 cellspacing=0 cellpadding=2 width=90%>
											<tr><td><b>{$MOD.LBL_EVENTSTAT}</b></td></tr>
											<tr><td>{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
											<tr><td>{$ACTIVITYDATA.date_start}</td></tr>
										</table></td>
									<td width=50% valign=top >
										<table border=0 cellspacing=0 cellpadding=2 width=90%>
											<tr><td><b>{$MOD.LBL_EVENTEDAT}</b></td></tr>
											<tr><td>{$ACTIVITYDATA.endhr}:{$ACTIVITYDATA.endmin}{$ACTIVITYDATA.endfmt}</td></tr>
											<tr><td>{$ACTIVITYDATA.due_date}</td></tr>
										</table>
									</td></tr>
									</table>
								</td>
							</tr>
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
										<td id="cellTabAlarm" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','on');switchClass('cellTabRepeat','off');switchClass('cellTabRelatedto','off');dispLayer('addEventAlarmUI');ghide('addEventInviteUI');ghide('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REMINDER}</a></td>
										<td class="dvtTabCache" style="width:10px">&nbsp;</td>
										<td id="cellTabRepeat" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabAlarm','off');switchClass('cellTabRepeat','on');switchClass('cellTabRelatedto','off');ghide('addEventAlarmUI');ghide('addEventInviteUI');dispLayer('addEventRepeatUI');ghide('addEventRelatedtoUI');">{$MOD.LBL_REPEAT}</a></td>
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
                                                                                        	        {$username.3}<br>
                                                                                                {/foreach}
											</td>
                                                                                </tr>
									</table>
									</DIV>
									<!-- Reminder UI -->
					                                <DIV id="addEventAlarmUI" style="display:none;width:100%">
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
									</DIV>
									<!-- Repeat UI -->
                                					<div id="addEventRepeatUI" style="display:none;width:100%">
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
                                                                                        <td>{$ACTIVITYDATA.repeat_month_str}</td>
                                                                                </tr>
										{/if}
									</table>
									</div>
									<!-- Relatedto UI -->
									<div id="addEventRelatedtoUI" style="display:none;width:100%">
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
										<tr>
											<td width="30%" align=right valign="top"><b>{$LABEL.parent_id}</b></td>
											<td width="70%" align=left valign="top">{$ACTIVITYDATA.parent_name}</td>
										</tr>
										<tr>
											<td width="30%" valign="top" align=right><b>{$MOD.LBL_CONTACT_NAME}</b></td>	
											<td width="70%" valign="top" align=left>
											{foreach item=contactname key=cntid from=$CONTACTS}
	                                                                                {$contactname.0}&nbsp;{$contactname.1}<br>
                                                                                        {/foreach}
										</tr>
									</table>
									</div>
								</td>
                					 </tr>
						     </table>
						    {else}
					 	     <table border="0" cellpadding="5" cellspacing="0" width="95%">
							<tr>
								<td width="20%" align="right"><b>{$MOD.LBL_TODO}</b></td>
								<td width="80%" align="left">{$ACTIVITYDATA.subject}</td>
							</tr>
							<tr>
								<td align="right" valign="top"><b>{$LABEL.description}</b></td>
                                                                <td align="left" colspan="3" valign="top" height="60px">{$ACTIVITYDATA.description}</td>
                					</tr>
							<tr>
                        					<td colspan="2" align="center" width="80%">
                                					<table border="0" cellpadding="3" cellspacing="0" width="80%">
                                        					<tr>
                                                				<td align="left"><b>{$LABEL.taskstatus}</b></td>
										<td align="left"><b>{$LABEL.taskpriority}</b></td>
										<td align="left"><b>{$LABEL.assigned_user_id}</b></td>
										</tr>
										<tr>
                                                				<td align="left" valign="top">{$ACTIVITYDATA.taskstatus}</td>
										<td align="left" valign="top">{$ACTIVITYDATA.taskpriority}</td>
										<td align="left" valign="top">{$ACTIVITYDATA.assigned_user_id}</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<br>
						<table border=0 cellspacing=0 cellpadding=5 width=95% >
							<tr>
								<td ><b>{$LABEL.createdtime}</b></td>
								<td >{$ACTIVITYDATA.createdtime}</td>
								<td ><b>{$LABEL.modifiedtime}</b></td>
								<td>{$ACTIVITYDATA.modifiedtime}</td>
                                                        </tr>
                                                </table>
						<hr noshade="noshade" size="1">
						<table bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="95%" align=center>
							<tr><td>
								<table border="0" cellpadding="2" cellspacing="0" width="100%" align=center>
									<tr><td width=50% valign=top style="border-right:1px solid #dddddd">
										<table border=0 cellspacing=0 cellpadding=2 width=95% align=center>
											<tr><td><b>{$MOD.LBL_TIMEDATE}</b></td></tr>
											<tr><td>{$ACTIVITYDATA.starthr}:{$ACTIVITYDATA.startmin}{$ACTIVITYDATA.startfmt}</td></tr>
											<tr><td>{$ACTIVITYDATA.date_start}</td></tr>
										</table>
									</td>
									<td width=50% valign="top">
										<table border=0 cellspacing=0 cellpadding=2 width=95% align=center>
											<tr><td><b>{$LABEL.due_date}</b></td></tr>
											<tr><td>{$ACTIVITYDATA.due_date}</td></tr>
											<tr><td>&nbsp;</td></tr>
										</table>
									</td>
								</table>	
							</tr>
						    </table>
						    <br>
						    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<table border="0" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td id="cellTabInvite" class="dvtSelectedCell" align="center" nowrap="nowrap"><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','on');switchClass('cellTabRelatedto','off');dispLayer('addTaskAlarmUI');ghide('addTaskRelatedtoUI');">{$MOD.LBL_NOTIFICATION}</td></a>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;
										<td id="cellTabRelatedto" class="dvtUnSelectedCell" align=center nowrap><a href="javascript:doNothing()" onClick="switchClass('cellTabInvite','off');switchClass('cellTabRelatedto','on');dispLayer('addTaskRelatedtoUI');ghide('addTaskAlarmUI');">{$MOD.LBL_RELATEDTO}</a></td>

										<td class="dvtTabCache" style="width: 100%;">&nbsp;</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr>                                                                                                                              <td width=100% valign=top align=left class="dvtContentSpace" style="padding:10px;height:120px">
								<!-- Notification UI -->
                                                                        <DIV id="addTaskAlarmUI" style="display:block;wid
th:100%">
									<table width="100%" cellpadding="5" cellspacing="0" border="0">
										<tr>
											<td width="30%" align=right><b>{$MOD.LBL_SENDNOTIFICATION}</b></td>
											<td width="70%" align=left>{$ACTIVITYDATA.sendnotification}</td>
										</tr>
                                                                        </table>
                                                                        </DIV>
									<div id="addTaskRelatedtoUI" style="display:none;width:100%">
                                                                        <table width="100%" cellpadding="5" cellspacing="0" border="0">
                                                                                <tr>
                                                                                        <td width="30%" align=right><b>{$LABEL.parent_id}</b></td>
                                                                                        <td width="70%" align=left>{$ACTIVITYDATA.parent_name}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                        <td align=right><b>{$MOD.LBL_CONTACT_NAME}</b></td>
                                                                                        <td align=left>{$ACTIVITYDATA.contact_id}</td>
                                                                                </tr>
									</table>
                                                                        </div>
						  		</td>
							</tr>
                                                     </table>	
						     {/if}

                     	                      </td>
					   </tr>
                </tr>
		<tr>
			<td style="padding:10px">
		           <table border=0 cellspacing=0 cellpadding=0 width=100%>
				     {strip}<tr nowrap>
							<td  colspan=4 style="padding:5px">
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="&nbsp;{$APP.LBL_EDIT_BUTTON_LABEL}&nbsp;">&nbsp;
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
	</table>
	</td>
	<td width=22% valign=top style="border-left:2px dashed #cccccc;padding:13px">
						<!-- right side relevant info -->

		<!-- Add Tag link added just above the tag cloud image -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% >
		<tr>
			<td align="left" class="genHeaderSmall"  nowrap><div id="addtagdiv"><a href="javascript:;" onClick="show('tagdiv'),fnhide('addtagdiv'),document.getElementById('txtbox_tagfields').focus()"><b>{$APP.LBL_ADD_TAG}</b></a></div><div id="tagdiv" style="display:none;"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value=""></input>&nbsp;&nbsp;<input name="button_tagfileds" type="button" class="crmbutton small save" value="{$APP.LBL_TAG_IT}" onclick="return tagvalidate()"/><input name="close" type="button" class="crmbutton small cancel" value="{$APP.LBL_CLOSE}" onClick="fnhide('tagdiv'),show('addtagdiv')"></div></td>
		</tr>
		</table>
		<br>
		<!-- Eng Add Tag Link -->
		<!-- Tag cloud display -->
		<table border=0 cellspacing=0 cellpadding=0 width=100% class="tagCloud">
		<tr>
			<td class="tagCloudTopBg"><img src="{$IMAGE_PATH}tagCloudName.gif" border=0></td>
		</tr>
		<tr>
			<td class="tagCloudDisplay" valign=top> <span id="tagfields">{$ALL_TAG}</span></td>
		</tr>
		</table>
		<!-- End Tag cloud display -->
			<!-- Mail Merge-->
				<br>
				{if $MERGEBUTTON eq 'permitted'}
  				<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
      				<tr>
      					   <td class="rightMailMergeHeader"><b>{$WORDTEMPLATEOPTIONS}</b></td>
      				</tr>
      				<tr style="height:25px">
      						<td class="rightMailMergeContent">
          						<select name="mergefile">{foreach key=templid item=tempflname from=$TOPTIONS}<option value="{$templid}">{$tempflname}</option>{/foreach}</select>
          						<input class="crmbutton small create" value="{$APP.LBL_MERGE_BUTTON_LABEL}" onclick="this.form.action.value='Merge';" type="submit"></input>
      					  </td>
      				</tr>
  				</table>
				{/if}
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

</tr></table></form>
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


