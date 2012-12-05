/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Base_Validator_Js("Vtiger_Email_Validator_Js",{},{

	/**
	 * Function to validate the email field data
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		return this.validateValue(fieldValue);
	},
	/**
	 * Function to validate the email field data
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validateValue : function(fieldValue){
		var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/ ;
		var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/ ;

		if (!emailFilter.test(fieldValue)) {
			var errorInfo = app.vtranslate('JS_PLEASE_ENTER_VALID_EMAIL_ADDRESS');
			this.setError(errorInfo);
			return false;

		} else if (fieldValue.match(illegalChars)) {
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');
			this.setError(errorInfo);
			return false;
		}
        return true;
	}
});

Vtiger_Base_Validator_Js("Vtiger_Phone_Validator_Js",{},{

	/**
	 * Function to validate the Phone field data
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		var strippedValue = fieldValue.replace(/[\(\)\.\-\+\ ]/g, '');
		strippedValue = strippedValue.replace(/[a-z0-9]/gi,'');
		// TODO : need to review this validation
	   /*if (isNaN(strippedValue)) {
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');
			this.setError(errorInfo);
			return false;
		} else*/ if (fieldValue.length > 30) {
			var errorInfo = app.vtranslate('JS_PHONE_NUMBER_LENGTH_EXCEEDED');
			this.setError(errorInfo);
			return false;
		}
        return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_Percentage_Validator_Js",{},{

	/**
	 * Function to validate the percentage field data
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		if (isNaN(fieldValue)) {
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');
			this.setError(errorInfo);
			return false;
		}
        return true;
	}
});

Vtiger_Base_Validator_Js('Vtiger_Url_Validator_Js',{},{

	/**
	 * Function to validate the Url
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		var regexp = /(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
		var result = regexp.test(fieldValue);
		if (!result ) {
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');//"Please enter valid url";
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_MultiSelect_Validator_Js",{

	invokeValidation : function(field, rules, i, options) {
		var validatorInstance = new Vtiger_MultiSelect_Validator_Js ();
		validatorInstance.setElement(field);
		var result = validatorInstance.validate();
		if(result == true){
			return result;
		} else {
			return validatorInstance.getError();
		}
	}
},{
	/**
	 * Function to validate the Multi select
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldInstance = this.getElement();
		var selectElementValue = fieldInstance.val();
		if(selectElementValue == null){
			var errorInfo = app.vtranslate('JS_PLEASE_SELECT_ATLEAST_ONE_OPTION');
			this.setError(errorInfo);
			return false;
		}
		return true;
	}

})


Vtiger_Email_Validator_Js ("Vtiger_MultiEmails_Validator_Js",{
	invokeValidation : function(field) {
		var validatorInstance = new Vtiger_MultiEmails_Validator_Js ();
		validatorInstance.setElement(field);
		var result = validatorInstance.validate();
		if(!result){
			return validatorInstance.getError();
		}
	}
},{
	/**
	 * Function to validate the Multi select
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldInstance = this.getElement();
		var fieldInstanceValue = fieldInstance.val();
		if(fieldInstanceValue != ''){
			var emailsArr = fieldInstanceValue.split(',');
			var i;
			for (i = 0; i < emailsArr.length; ++i) {
				var result =  this.validateValue(emailsArr[i]);
				if(result == false){
					return result;
				}
			}
			return true;
		}
	}

});

Vtiger_Base_Validator_Js("Vtiger_PositiveNumber_Validator_Js",{

	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function(field, rules, i, options){
		var positiveNumberInstance = new Vtiger_PositiveNumber_Validator_Js();
		positiveNumberInstance.setElement(field);
		var response = positiveNumberInstance.validate();
		if(response != true){
			return positiveNumberInstance.getError();
		}
	}

},{

	/**
	 * Function to validate the Positive Numbers
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		if(isNaN(fieldValue) || fieldValue < 0){
			var errorInfo = app.vtranslate('JS_ACCEPT_POSITIVE_NUMBER');
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})



Vtiger_PositiveNumber_Validator_Js("Vtiger_GreaterThanZero_Validator_Js",{

	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function(field, rules, i, options){

		var GreaterThanZeroInstance = new Vtiger_GreaterThanZero_Validator_Js();
		GreaterThanZeroInstance.setElement(field);
		var response = GreaterThanZeroInstance.validate();
		if(response != true){
			return GreaterThanZeroInstance.getError();
		}
	}

},{

	/**
	 * Function to validate the Positive Numbers and greater than zero value
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){

		var response = this._super();
		if(response != true){
			return response;
		}else{
			var fieldValue = this.getFieldValue();
			if(fieldValue == 0){
				var errorInfo = app.vtranslate('JS_VALUE_SHOULD_BE_GREATER_THAN_ZERO');
				this.setError(errorInfo);
				return false;
			}
		}
		return true;
	}
})

Vtiger_PositiveNumber_Validator_Js("Vtiger_WholeNumber_Validator_Js",{},{

	/**
	 * Function to validate the Positive Numbers and whole Number
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var response = this._super();
		if(response != true){
			return response;
		}
		var field = this.getElement();
		var fieldData = field.data();
		var fieldInfo = fieldData.fieldinfo;
		var fieldValue = this.getFieldValue();
		if((fieldValue % 1) != 0){
			var errorInfo = app.vtranslate('INVALID_NUMBER_OF')+" "+fieldInfo.label;
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_lessThanToday_Validator_Js",{},{

	/**
	 * Function to validate the birthday field
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var field = this.getElement();
		var fieldData = field.data();
		var fieldDateFormat = fieldData.dateFormat;
		var fieldInfo = fieldData.fieldinfo;
		var fieldValue = this.getFieldValue();
		try{
			var fieldDateInstance = Vtiger_Helper_Js.getDateInstance(fieldValue,fieldDateFormat);
		}
		catch(err){
			this.setError(err);
			return false;
		}
		fieldDateInstance.setHours(0,0,0,0);
		var todayDateInstance = new Date();
		todayDateInstance.setHours(0,0,0,0);
		var comparedDateVal =  todayDateInstance - fieldDateInstance;
		if(comparedDateVal <= 0){
			var errorInfo = fieldInfo.label+" "+app.vtranslate('JS_SHOULD_BE_LESS_THAN_CURRENT_DATE');
			this.setError(errorInfo);
			return false;
		}
        return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_greaterThanDependentField_Validator_Js",{},{

	/**
	 * Function to validate the birthday field
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(dependentFieldList){
		var field = this.getElement();
		var fieldLabel = field.data('fieldinfo').label;
		var contextFormElem = field.closest('form');
		for(var i=0; i<dependentFieldList.length; i++){
			var dependentField = dependentFieldList[i];
			var dependentFieldInContext = jQuery('input[name='+dependentField+']',contextFormElem);
			var dependentFieldLabel = dependentFieldInContext.data('fieldinfo').label;
			var fieldDateInstance = this.getDateTimeInstance(field);
			var dependentFieldDateInstance = this.getDateTimeInstance(dependentFieldInContext);
			var comparedDateVal =  fieldDateInstance - dependentFieldDateInstance;
			if(comparedDateVal < 0){
				var errorInfo = fieldLabel+' '+app.vtranslate('JS_SHOULD_BE_GREATER_THAN_OR_EQUAL_TO')+' '+dependentFieldLabel+'';
				this.setError(errorInfo);
				return false;
			}
		}
        return true;
	},

	getDateTimeInstance : function(field) {
		var dateFormat = field.data('dateFormat');
		var fieldValue = field.val();
		try{
			var dateTimeInstance = Vtiger_Helper_Js.getDateInstance(fieldValue,dateFormat);
		}
		catch(err){
			this.setError(err);
			return false;
		}
		return dateTimeInstance;
	}
})

Vtiger_Base_Validator_Js('Vtiger_Currency_Validator_Js',{

	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function(field, rules, i, options){
		var currencyValidatorInstance = new Vtiger_Currency_Validator_Js();
		currencyValidatorInstance.setElement(field);
		var response = currencyValidatorInstance.validate();
		if(response != true){
			return currencyValidatorInstance.getError();
		}
	}
},{

	/**
	 * Function to validate the Currency Field
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var field = this.getElement();
		var fieldValue = this.getFieldValue();
		var fieldData = field.data();
		var strippedValue = fieldValue.replace(fieldData.decimalSeperator, '');
		strippedValue = strippedValue.replace(fieldData.groupSeperator, '');
		strippedValue = strippedValue.replace(/[0-9]/g, '');
		//Note: Need to review if we should allow only positive values in currencies
		/*if(strippedValue < 0){
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');//"currency value should be greater than or equal to zero";
			this.setError(errorInfo);
			return false;
		}*/
		if(isNaN(strippedValue)){
			var errorInfo = app.vtranslate('JS_CONTAINS_ILLEGAL_CHARACTERS');
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_ReferenceField_Validator_Js",{},{

	/**
	 * Function to validate the Positive Numbers and whole Number
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var field = this.getElement();
		var parentElement = field.closest('.fieldValue');
		var referenceField = parentElement.find('.sourceField');
		var referenceFieldValue = referenceField.val();
		var fieldInfo = referenceField.data().fieldinfo;
		if(referenceFieldValue == ""){
			var errorInfo = app.vtranslate('JS_REQUIRED_FIELD');
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})

Vtiger_Base_Validator_Js("Vtiger_Integer_Validator_Js",{},{

	/**
	 * Function to validate the Integre field data
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		var integerRegex= /^\d+$/ ;
		if (!fieldValue.match(integerRegex)) {
			var errorInfo = app.vtranslate("JS_PLEASE_ENTER_INTEGER_VALUE");
			this.setError(errorInfo);
			return false;
		}
		return true;
	}
})

Vtiger_Integer_Validator_Js("Vtiger_Double_Validator_Js",{},{

	/**
	 * Function to validate the Decimal field data
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var response = this._super();
		if(response == false){
			var fieldValue = this.getFieldValue();
			var doubleRegex= /^\d+.\d+$/ ;
			if (!fieldValue.match(doubleRegex)) {
				var errorInfo = app.vtranslate("JS_PLEASE_ENTER_DECIMAL_VALUE");
				this.setError(errorInfo);
				return false;
			}
			return true;
		}
		return response;
	}
})

Vtiger_Base_Validator_Js("Vtiger_Date_Validator_Js",{
	
	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function(field, rules, i, options){
		var dateValidatorInstance = new Vtiger_Date_Validator_Js();
		dateValidatorInstance.setElement(field);
		var response = dateValidatorInstance.validate();
		if(response != true){
			return dateValidatorInstance.getError();
		}
	}
	
},{

	/**
	 * Function to validate the Positive Numbers and whole Number
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var field = this.getElement();
		var fieldData = field.data();
		var fieldDateFormat = fieldData.dateFormat;
		var fieldValue = this.getFieldValue();
		try{
			Vtiger_Helper_Js.getDateInstance(fieldValue,fieldDateFormat);
		}
		catch(err){
			var errorInfo = app.vtranslate("JS_PLEASE_ENTER_VALID_DATE");
			this.setError(errorInfo);
			return false;
		}
		return true;
	}


})



//Calendar Specific validators
// We have placed it here since quick create will not load module specific validators

Vtiger_greaterThanDependentField_Validator_Js("Calendar_greaterThanDependentField_Validator_Js",{},{

	getDateTimeInstance : function(field) {
		if(field.attr('name') == 'date_start') {
			var timeField = jQuery('[name="time_start"]');
		}else if(field.attr('name') == 'due_date') {
			var timeField = jQuery('[name="time_end"]');
		}

		var dateFieldValue = field.val()+" "+ timeField.val();
		var dateFormat = field.data('dateFormat');
		return Vtiger_Helper_Js.getDateInstance(dateFieldValue,dateFormat);
	}

});

Vtiger_Base_Validator_Js('Calendar_greaterThanToday_Validator_Js',{},{

	/**
	 * Function to validate the birthday field
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var field = this.getElement();
		var fieldData = field.data();
		var fieldDateFormat = fieldData.dateFormat;
		var fieldInfo = fieldData.fieldinfo;
		var fieldValue = this.getFieldValue();
		try{
			var fieldDateInstance = Vtiger_Helper_Js.getDateInstance(fieldValue,fieldDateFormat);
		}
		catch(err){
			this.setError(err);
			return false;
		}
		fieldDateInstance.setHours(0,0,0,0);
		var todayDateInstance = new Date();
		todayDateInstance.setHours(0,0,0,0);
		var comparedDateVal =  todayDateInstance - fieldDateInstance;
		if(comparedDateVal >= 0){
			var errorInfo = fieldInfo.label+" "+app.vtranslate('JS_SHOULD_BE_GREATER_THAN_CURRENT_DATE');
			this.setError(errorInfo);
			return false;
		}
        return true;
	}
})

Vtiger_Base_Validator_Js("Calendar_RepeatMonthDate_Validator_Js",{
	
	/**
	 *Function which invokes field validation
	 *@param accepts field element as parameter
	 * @return error if validation fails true on success
	 */
	invokeValidation: function(field, rules, i, options){
		var repeatMonthDateValidatorInstance = new Calendar_RepeatMonthDate_Validator_Js();
		repeatMonthDateValidatorInstance.setElement(field);
		var response = repeatMonthDateValidatorInstance.validate();
		if(response != true){
			return repeatMonthDateValidatorInstance.getError();
		}
	}
	
},{

	/**
	 * Function to validate the Positive Numbers and whole Number
	 * @return true if validation is successfull
	 * @return false if validation error occurs
	 */
	validate: function(){
		var fieldValue = this.getFieldValue();
		
		if((parseInt(parseFloat(fieldValue))) != fieldValue || fieldValue == '' || parseInt(fieldValue) > '31' || parseInt(fieldValue) <= 0) {
			var result = app.vtranslate('JS_NUMBER_SHOULD_BE_LESS_THAN_32');
			this.setError(result);
			return false;
		}
		return true;
	}
})