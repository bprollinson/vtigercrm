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
{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $FIELD_MODEL->get('uitype') eq '53'}
	{assign var=ACCESSIBLE_USER_LIST value=$USER_MODEL->getAccessibleUsers()}
	{assign var=ACCESSIBLE_GROUP_LIST value=$USER_MODEL->getAccessibleGroups()}
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
	{if $FIELD_VALUE eq ''}
		{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
	{/if}
	<select class="chzn-select {$ASSIGNED_USER_ID}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}>
		<optgroup label="{vtranslate('LBL_USERS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_USER_LIST}
                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if}>
                    {$OWNER_NAME}
                    </option>
			{/foreach}
		</optgroup>
		<optgroup label="{vtranslate('LBL_GROUPS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ACCESSIBLE_GROUP_LIST}
				<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_MODEL->get('fieldvalue') eq $OWNER_ID} selected {/if}>
				{$OWNER_NAME}
				</option>
			{/foreach}
		</optgroup>
	</select>
{/if}
{* TODO - UI type 52 needs to be handled *}
{/strip}