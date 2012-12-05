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
	<span class="span2">
		{foreach key=ITER item=IMAGE_INFO from=$RECORD->getImageDetails()} 
			{if !empty($IMAGE_INFO.path)} 
				<img src="../{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="150" height="80" align="left"><br> 
			{else}
				<img src="{vimage_path('summary_Contact.png')}" class="summaryImg"/>
			{/if} 
		{/foreach} 
	</span>
	<span class="span8 margin0px">
		<span class="row-fluid">
			<span class="recordLabel font-x-x-large textOverflowEllipsis pushDown span" title="{$RECORD->getName()}">{$RECORD->getName()}</span>
		</span>
		<span class="row-fluid">
			<span class="title_label">{$RECORD->getDisplayValue('title')}</span>
			{if $RECORD->getDisplayValue('account_id') && $RECORD->getDisplayValue('title') }
				&nbsp;{vtranslate('LBL_AT')}&nbsp;
			{/if}
			{$RECORD->getDisplayValue('account_id')}
		</span>
	</span>
{/strip}