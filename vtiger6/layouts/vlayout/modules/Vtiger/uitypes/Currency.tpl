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
{if $FIELD_MODEL->get('uitype') eq '71'}
<div class="input-prepend">
	<div class="row-fluid">
		<span class="span1"><span class="add-on row-fluid">{$USER_MODEL->get('currency_symbol')}</span></span>
		<span  class="span9"><input type="text" class="row-fluid" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$FIELD_MODEL->get('name')}"
		data-fieldinfo='{$FIELD_INFO}' value="{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}" {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}' /></span>
	</div>
</div>
{else if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
	<div class="input-prepend">
		<div class="row-fluid">
			<span class="span1">
				<span class="add-on row-fluid">{$BASE_CURRENCY_SYMBOL}</span>
			</span>
			<span class="span10 row-fluid">
				<input type="text" class="span6 unitPrice" name="{$FIELD_MODEL->get('name')}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
			data-fieldinfo='{$FIELD_INFO}'  value="{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}" {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}
			data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}'/>
				<a id="moreCurrencies" class="span">{vtranslate('LBL_MORE_CURRENCIES', $MODULE)}>></a>
				<span id="moreCurrenciesContainer"></span>
				<input type="hidden" name="base_currency" value="{$BASE_CURRENCY_NAME}">
			</span>
		</div>
	</div>
{else}
<div class="input-prepend">
	<div class="row-fluid">
		<span class="span1"><span class="add-on row-fluid">{$USER_MODEL->get('currency_symbol')}</span></span>
		<span class="span7"><input type="text" class="row-fluid" name="{$FIELD_MODEL->get('name')}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"
		data-fieldinfo='{$FIELD_INFO}' value="{$FIELD_MODEL->getEditViewDisplayValue($FIELD_MODEL->get('fieldvalue'))}" {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} data-decimal-seperator='{$USER_MODEL->get('currency_decimal_separator')}' data-group-seperator='{$USER_MODEL->get('currency_grouping_separator')}' /></span>
	</div>
</div>
{/if}
{* TODO - UI Type 72 needs to be handled. Multi-currency support also needs to be handled *}
{/strip}