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
<div>
	<form class="form-horizontal recordEditView equalSplit" id="EditView" name="EditView" method="post" enctype="multipart/form-data" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="isPreference" value="{$IS_PREFERENCE}" />
		<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='Single_'|cat:$MODULE}
			<span class="span8">
		{if $RECORD_ID neq ''}
			<span class="span8 font-x-x-large" title='{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} "{$RECORD_STRUCTURE_MODEL->getRecordName()}"'>{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} "{$RECORD_STRUCTURE_MODEL->getRecordName()}"</span>
		{else}
			<span class="span8 font-x-x-large" title="{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</span>
		{/if}
			</span>
			<span class="span4">
				<span class="pull-right" style="padding-right: 15px">
					{if $IS_PREFERENCE && $RECORD_ID == $USER_MODEL->getId()}
						<div class='btn-group' title="{vtranslate('LBL_DISPLAY_TYPE', 'Vtiger')}" style="float:left">
							<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>
								<span id='currentWidthType'><i class='icon-th-list'></i></span>&nbsp;<span class='caret'></span>
							</a>
							<ul class='dropdown-menu pull-right' id='widthType'>
								<li class="cursorPointer" data-class='wideWidthType' title="{vtranslate('LBL_DISPLAY_WIDETYPE', 'Vtiger')}">
									<i class='icon-th-list'></i>  {vtranslate('LBL_DISPLAY_WIDETYPE', 'Vtiger')}
								</li>
								<li class="cursorPointer" data-class='mediumWidthType' title="{vtranslate('LBL_DISPLAY_MEDIUMTYPE', 'Vtiger')}">
									<i class='icon-list'></i>  {vtranslate('LBL_DISPLAY_MEDIUMTYPE', 'Vtiger')}
								</li>
								<li class="cursorPointer" data-class='narrowWidthType' title="{vtranslate('LBL_DISPLAY_NARROWTYPE', 'Vtiger')}">
									<i class='icon-list-alt'></i>  {vtranslate('LBL_DISPLAY_NARROWTYPE', 'Vtiger')}
								</li>
							</ul>
						</div>
						&nbsp;&nbsp;
					{/if}
					<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
					<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
				</span>
			</span>
		</div>
		<div style='padding:10px'>
			{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
				<table class="table table-bordered marginLeftZero">
				{if $BLOCK_FIELDS|@count gt 0}
				<tr class="listViewActionsDiv">
					<th colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
				</tr>
				<tr>
				{assign var=COUNTER value=0}
				{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
					{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
					{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
					{assign var="refrenceListCount" value=count($refrenceList)}
					{if $COUNTER eq 2}
						</tr><tr>
						{assign var=COUNTER value=1}
					{else}
						{assign var=COUNTER value=$COUNTER+1}
					{/if}
					<td class="fieldLabel">
					{if {$isReferenceField} eq "reference"}
						{if $refrenceListCount > 1}
							<select style="width: 150px;" class="chzn-select" id="referenceModulesList">
								<optgroup>
									{foreach key=index item=value from=$refrenceList}
										<option value="{$value}">{$value}</option>
									{/foreach}
								</optgroup>
							</select>
						{/if}
						{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
					{else}
						{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
					{/if}
					{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
					</td>
					<td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						{include file=$FIELD_MODEL->getUITypeModel()->getTemplateName()|@vtemplate_path}
					</td>
				{/foreach}
				</tr>
				{/if}
				</table>
			{/foreach}

			{* tag cloud starts *}
			<table class="table table-bordered marginLeftZero">
				<tr class="listViewActionsDiv">
					<th colspan="4">{vtranslate('LBL_TAG_CLOUD_DISPLAY', $MODULE)}</th>
				</tr>
				<tr>
					<td class="fieldLabel">{vtranslate('LBL_TAG_CLOUD', $MODULE)}</td>
					<td class="fieldValue">
						<input type="checkbox" name="tagcloudview" {if $TAG_CLOUD} checked {/if} />
					</td><td></td><td></td><td></td>
				</tr>
			</table>
			{* tag cloud ends *}

			<div class='pull-right'>
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</div>
		</div>
    </form>
</div>
{/strip}