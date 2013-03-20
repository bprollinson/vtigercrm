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
{strip}
<div class="SendEmailFormStep2" name="emailPreview">
	<input type="hidden" name="parentRecord" value="{$PARENT_RECORD}"/>
	<input type="hidden" name="recordId" value="{$RECORD_ID}"/>
	<div class="row-fluid padding-bottom1per pushDown2per">
		<span class="span4">&nbsp;</span>
		<span class="span8">
			<span class="pull-right btn-toolbar"> 
				<span class="btn-group"> 
					<button type="button" name="previewForward" class="btn" data-mode="emailForward"> 
							<strong>{vtranslate('LBL_FORWARD',$MODULE)}</strong> 
					</button> 
				</span> 
				{if !($RECORD->isSentMail())} 
					<span class="btn-group"> 
						<button type="button" name="previewEdit" class="btn" data-mode="emailEdit"> 
								<strong>{vtranslate('LBL_EDIT',$MODULE)}</strong> 
						</button> 
					</span> 
				{/if} 
				<span class="btn-group"> 
					<button type="button" name="previewPrint" class="btn" onClick='window.location.href="{$RECORD->getPrintViewUrl()}"'> 
						<strong>{vtranslate('LBL_PRINT',$MODULE)}</strong> 
					</button> 
				</span> 
			</span>
		</span>
	</div>
	<div class="well well-large zeroPaddingAndMargin">
		<div class="modal-header blockHeader emailPreviewHeader">
			<h3>{vtranslate('SINGLE_Emails', $MODULE)} {vtranslate('LBL_INFO', $MODULE)}</h3>
		</div>
		<form class="form-horizontal emailPreview">
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_TO',$MODULE)}</span>
					</span>
					<span class="span9">
						{assign var=TO_EMAILS value=","|implode:$TO}
						<span class="row-fluid">{$TO_EMAILS}</span>
					</span>
				</span>
			</div>
			{if !empty($CC)}
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_CC',$MODULE)}</span>
					</span>
					<span class="span9">
						<span class="row-fluid">
							{$CC}
						</span>
					</span>
				</span>
			</div>
			{/if}
			{if !empty($BCC)}
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_BCC',$MODULE)}</span>
					</span>
					<span class="span9">
						<span class="row-fluid">
							{$BCC}
						</span>
					</span>
				</span>
			</div>
			{/if}
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_SUBJECT',$MODULE)}</span>
					</span>
					<span class="span9">
						<span class="row-fluid">
							{$RECORD->get('subject')}
						</span>
					</span>
				</span>
			</div>
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_ATTACHMENT',$MODULE)}</span>
					</span>
					<span class="span9">
						<span class="row-fluid">
							{foreach item=ATTACHMENT_DETAILS  from=$RECORD->getAttachmentDetails()}
                                <a &nbsp;
                                {if array_key_exists('docid',$ATTACHMENT_DETAILS)}
                                    &nbsp; href="index.php?module=Documents&action=DownloadFile&record={$ATTACHMENT_DETAILS['docid']}
                                            &fileid={$ATTACHMENT_DETAILS['fileid']}"
                                {else}
                                    &nbsp; href="index.php?module=Emails&action=DownloadFile&attachment_id={$ATTACHMENT_DETAILS['fileid']}"
                                {/if}
								>{$ATTACHMENT_DETAILS['attachment']}</a>&nbsp;&nbsp;
							{/foreach}
						</span>
					</span>
				</span>
			</div>
			<div class="row-fluid padding-bottom1per">
				<span class="span12 row-fluid">
					<span class="span2">
						<span class="pull-right muted">{vtranslate('LBL_DESCRIPTION',$MODULE)}</span>
					</span>
					<span class="span9">
						<span class="row-fluid">
							{decode_html($RECORD->get('description'))}
						</span>
					</span>
				</span>
			</div>
			<div class="row-fluid">
				<span class="span1">&nbsp;</span>
				<span class="span10 margin0px"><hr/></span>
			</div>
			<div class="row-fluid">
				<span class="span4">&nbsp;</span>
				<span class="span4 textAlignCenter">
					<span class="muted">
						{if $RECORD->get('email_flag') eq "SAVED"}
							<small><em>{vtranslate('LBL_DRAFTED_ON',$MODULE)}</em></small>
							<span><small><em>&nbsp;{$RECORD->getDisplayValue('createdtime')}</em></small></span>
						{elseif $RECORD->get('email_flag') eq "SENT"}
							<small><em>{vtranslate('LBL_SENT_ON',$MODULE)}</em></small>
                            {assign var="SEND_TIME" value=$RECORD->get('date_start')|@cat:' '|@cat:$RECORD->get('time_start')}
                            <span><small><em>&nbsp;{Vtiger_Datetime_UIType::getDisplayDateTimeValue($SEND_TIME)}</em></small></span>
						{/if}
					</span>
				</span>
			</div>
			<div class="row-fluid">
				<span class="span3">&nbsp;</span>
				<span class="span5 textAlignCenter">
					<span><strong> {vtranslate('LBL_OWNER',$MODULE)} : {getOwnerName($RECORD->get('assigned_user_id'))}</strong></span>
				</span>
			</div>
		</form>
	</div>
</div>
{/strip}