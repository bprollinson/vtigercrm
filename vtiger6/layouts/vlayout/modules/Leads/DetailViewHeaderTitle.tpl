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
		<img src="{vimage_path('summary_Leads.png')}" class="summaryImg" />
	</span>
	<span class="span8 margin0px">
		<span class="row-fluid">
			<span class="recordLabel font-x-x-large textOverflowEllipsis pushDown span" title="{$RECORD->getName()}">{$RECORD->getName()}</span>
		</span>
		<span class="row-fluid">
			<span class="designation_label">{$RECORD->getDisplayValue('designation')}</span>
			{if $RECORD->getDisplayValue('company') && $RECORD->getDisplayValue('designation')}
				&nbsp;{vtranslate('LBL_AT')}&nbsp;
			{/if}
			<span class="company_label">{$RECORD->get('company')}</span>
		</span>
	</span>
{/strip}