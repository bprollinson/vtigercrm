/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_AdvanceFilter_Js",{},{

	filterContainer : false,
	//Hold the conditions for a particular field type
	fieldTypeConditionMapping : false,
	//Hold the condition and their label translations
	conditonOperatorLabelMapping : false,

	fieldModelInstance : false,
	//Holds fields type and conditions for which it needs validation
	validationSupportedFieldConditionMap : {
				'email' : ['e','n']
	},
	//Hols field type for which there is validations always needed
	allConditionValidationNeededFieldList : ['double', 'integer', 'currency'],
	//used to eliminate mutiple times validation registrations
	validationForControlsRegistered : false,


	init : function(container) {
		if(typeof container == 'undefined') {
			container = jQuery('.filterContainer');
		}

		if(container.is('.filterContainer')) {
			this.setFilterContainer(container);
		}else{
			this.setFilterContainer(jQuery('.filterContainer',container));
		}
		this.intialize();
	},

	/**
	 * Function  to intialize the advance filter
	 */
	intialize : function() {
		this.registerEvents();
		this.intializeOperationMappingDetails();
		this.loadFieldSpecificUiForAll();
	},

	/**
	 * Function which will save the field condition mapping condition label mapping
	 */
	intializeOperationMappingDetails : function() {
		var filterContainer = this.getFilterContainer();
		this.fieldTypeConditionMapping = jQuery('input[name="advanceFilterOpsByFieldType"]',filterContainer).data('value');
		this.conditonOperatorLabelMapping = jQuery('input[name="advanceFilterOptions"]',filterContainer).data('value');
		return this;
	},

	/**
	 * Function to get the container which holds all the filter elements
	 * @return jQuery object
	 */
	getFilterContainer : function() {
		return this.filterContainer;
	},

	/**
	 * Function to set the filter container
	 * @params : element - which represents the filter container
	 * @return : current instance
	 */
	setFilterContainer : function(element) {
		this.filterContainer = element;
		return this;
	},

	/**
	 * Function which will return set of condition for the given field type
	 * @return array of conditions
	 */
	getConditionListFromType : function(fieldType){
		return this.fieldTypeConditionMapping[fieldType];
	},

	/**
	 * Function to get the condition label
	 * @param : key - condition key
	 * @reurn : label for the condition or key if it doest not contain in the condition label mapping
	 */
	getConditionLabel : function(key) {
		if(key in this.conditonOperatorLabelMapping){
			return this.conditonOperatorLabelMapping[key];
		}
		return key;
	},

	/**
	 * Function to check if the field selected is empty field
	 * @params : select element which represents the field
	 * @return : boolean true/false
	 */
	isEmptyFieldSelected : function(fieldSelect) {
		var selectedOption = fieldSelect.find('option:selected');
		//assumption that empty field will be having value none
		if(selectedOption.val() == 'none'){
			return true;
		}
		return false;
	},

	/**
	 * Function to get the add condition elements
	 * @returns : jQuery object which represents the add conditions elements
	 */
	getAddConditionElement : function() {
		var filterContainer = this.getFilterContainer();
		return jQuery('.addCondition button', filterContainer);
	},

	/**
	 * Function to add new condition row
	 * @params : condtionGroupElement - group where condtion need to be added
	 * @return : current instance
	 */
	addNewCondition : function(conditionGroupElement){
		var basicElement = jQuery('.basic',conditionGroupElement);
		var newRowElement = basicElement.find('.conditionRow').clone(true,true);
		jQuery('select',newRowElement).addClass('chzn-select');
		var conditionList = jQuery('.conditionList', conditionGroupElement);
		conditionList.append(newRowElement);
		//change in to chosen elements
		app.changeSelectElementView(newRowElement);
		return this;
	},

	/**
	 * Function/Handler  which will triggered when user clicks on add condition
	 */
	addConditionHandler : function(e) {
		var element = jQuery(e.currentTarget);
		var conditionGroup = element.closest('div.conditionGroup');
		this.addNewCondition(conditionGroup);
	},

	/**
	 * Function to load condition list for the selected field
	 * @params : fieldSelect - select element which will represents field list
	 * @return : select element which will represent the condition element
	 */
	loadConditions : function(fieldSelect) {
		var row = fieldSelect.closest('div.conditionRow');
		var conditionSelectElement = row.find('select[name="comparator"]');
		var conditionSelected = conditionSelectElement.val();
		var fieldSelected = fieldSelect.find('option:selected');
		var conditionSelected = conditionSelectElement.val();
		var fieldType = fieldSelected.data('fieldtype');
		var fieldInfo = fieldSelected.data('fieldinfo');
		var type = fieldInfo.type;
		if(type == 'reference'){
			fieldType = 'V';
		}
		var conditionList = this.getConditionListFromType(fieldType);

		//for none in field name
		if(typeof conditionList == 'undefined') {
			conditionList = {};
			conditionList['none'] = 'None';
		}

		var options = '';
		for(var key in conditionList) {
			//IE Browser consider the prototype properties also, it should consider has own properties only.
			if(conditionList.hasOwnProperty(key)) {
				var conditionValue = conditionList[key];
				var conditionLabel = this.getConditionLabel(conditionValue);
				options += '<option value="'+conditionValue+'"';
				if(conditionValue == conditionSelected){
					options += ' selected="selected" ';
				}
				options += '>'+conditionLabel+'</option>';
			}
		}
		conditionSelectElement.empty().html(options).trigger("liszt:updated");
		return conditionSelectElement;
	},

	/**
	 * Functiont to get the field specific ui for the selected field
	 * @prarms : fieldSelectElement - select element which will represents field list
	 * @return : jquery object which represents the ui for the field
	 */
	getFieldSpecificUi : function(fieldSelectElement) {
		var selectedOption = fieldSelectElement.find('option:selected');
		var fieldModel = this.fieldModelInstance;
		if(fieldModel.getType().toLowerCase() == "boolean") {
			var conditionRow = fieldSelectElement.closest('.conditionRow');
			var selectedValue = conditionRow.find('[name="value"]').val();
			var html = '<select class="chzn-select" name="'+fieldModel.getName()+'">';
			html += '<option value="0"';
			if(selectedValue == '0') {
				html +=  ' selected="selected" ';
			}
			html += '>'+app.vtranslate('JS_IS_DISABLED')+'</option>';

			html += '<option value="1"';
			if(selectedValue == '1') {
				html +=  ' selected="selected" ';
			}
			html += '>'+app.vtranslate('JS_IS_ENABLED')+'</option>';
			html += '</select>'
			return jQuery(html);
		}else{
			return  jQuery(fieldModel.getUiTypeSpecificHtml())
		}
	},

	/**
	 * Function which will load the field specific ui for a selected field
	 * @prarms : fieldSelect - select element which will represents field list
	 * @return : current instance
	 */
	loadFieldSpecificUi : function(fieldSelect) {
		var selectedOption = fieldSelect.find('option:selected');
		var row = fieldSelect.closest('div.conditionRow');
		var fieldUiHolder = row.find('.fieldUiHolder');
		var conditionSelectElement = row.find('select[name="comparator"]');
		var fieldInfo = selectedOption.data('fieldinfo');
		var fieldType = fieldInfo.type;
		if(fieldType == 'picklist' || fieldType == 'owner' || fieldType == 'date' || fieldType == 'datetime'){
			fieldInfo.comparatorElementVal = conditionSelectElement.val();
		}
		var fieldModel = Vtiger_Field_Js.getInstance(fieldInfo,'AdvanceFilter');
		this.fieldModelInstance = fieldModel;
		var fieldSpecificUi = this.getFieldSpecificUi(fieldSelect);

		//remove validation since we dont need validations for all eleements
		// Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency
		fieldSpecificUi.filter('[name="'+ fieldModel.getName() +'"]').attr('name', 'value').addClass('row-fluid').removeAttr('data-validation-engine');
		fieldSpecificUi.find('[name="'+ fieldModel.getName() +'"]').attr('name','value').addClass('row-fluid').removeAttr('data-validation-engine');

		fieldUiHolder.html(fieldSpecificUi);

		if(fieldSpecificUi.is('input.select2')){
			var tagElements = fieldSpecificUi.data('tags');
			var params = {tags : tagElements,tokenSeparators: [","]}
			app.showSelect2ElementView(fieldSpecificUi,params)
		} else if(fieldSpecificUi.is('select')){
			if(fieldSpecificUi.hasClass('chzn-select')) {
				app.changeSelectElementView(fieldSpecificUi)
			}else{
				app.showSelect2ElementView(fieldSpecificUi);
			}
		} else if (fieldSpecificUi.is('input.dateField')){
			var calendarType = fieldSpecificUi.data('calendarType');
			if(calendarType == 'range'){
				var customParams = {
					calendars: 3,
					mode: 'range',
					className : 'rangeCalendar',
					onChange: function(formated) {
						fieldSpecificUi.val(formated.join(','));
					}
				}
				app.registerEventForDatePickerFields(fieldSpecificUi,false,customParams);
			}else{
				app.registerEventForDatePickerFields(fieldSpecificUi);
			}
		}
		this.addValidationToFieldIfNeeded(fieldSelect);

		var comparatorContainer = conditionSelectElement.closest('[class^="span"]');
		//if it is check box then we need hide the comprator
		if(fieldModel.getType().toLowerCase() == 'boolean') {
			//making the compator as equal for check box
			conditionSelectElement.find('option[value="e"]').attr('selected','selected');
			comparatorContainer.hide();
		}else{
			comparatorContainer.show();
		}
		return this;
	},

	/**
	 * Function to load field specific ui for all the select elements - this is used on load
	 * to show field specific ui for all the fields
	 */
	loadFieldSpecificUiForAll : function() {
		var conditionsContainer = jQuery('.conditionList');
		var fieldSelectElement = jQuery('select[name="columnname"]',conditionsContainer);
		if(fieldSelectElement.val() != 'none'){
			fieldSelectElement.trigger('change');
		}
		return this;
	},

	/**
	 * Function to add the validation if required
	 * @prarms : selectFieldElement - select element which will represents field list
	 */
	addValidationToFieldIfNeeded : function(selectFieldElement) {
		var selectedOption = selectFieldElement.find('option:selected');
		var row = selectFieldElement.closest('div.conditionRow');
		var fieldSpecificElement = row.find('[name="value"]');

		if(this.isFieldSupportsValidation(selectFieldElement)) {
			//data attribute will not be present while attaching validation engine events . so we are
			//depending on the fallback option which is class
			//TODO : remove the hard coding and get it from field element data-validation-engine
			fieldSpecificElement.addClass('validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]')
								.attr('data-validation-engine','validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]')
								.attr('data-fieldinfo', JSON.stringify(selectedOption.data('fieldinfo')));
		}else{
			fieldSpecificElement.removeClass('validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]')
								.removeAttr('data-validation-engine')
								.removeAttr('data-fieldinfo');
		}
		return this;
	},

	/**
	 * Check if field supports validation
	 * @prarms : selectFieldElement - select element which will represents field list
	 * @return - boolen true/false
	 */
	isFieldSupportsValidation : function(fieldSelect) {
		var selectedOption = fieldSelect.find('option:selected');

		var fieldModel = this.fieldModelInstance;
		var type = fieldModel.getType();

		if(jQuery.inArray(type,this.allConditionValidationNeededFieldList) >= 0) {
			return true;
		}

		var row = fieldSelect.closest('div.conditionRow');
		var conditionSelectElement = row.find('select[name="comparator"]');
		var selectedCondition = conditionSelectElement.find('option:selected');

		var conditionValue = conditionSelectElement.val();

		if(type in this.validationSupportedFieldConditionMap) {
			if( jQuery.inArray(conditionValue, this.validationSupportedFieldConditionMap[type]) >= 0 ){
				return true;
			}
		}
		return false;
	},

	/**
	 * Function to retrieve the values of the filter
	 * @return : object
	 */
	getValues : function() {
		var thisInstance = this;
		var filterContainer = this.getFilterContainer();

		var fieldList = new Array('columnname', 'comparator', 'value', 'column_condition');

		var values = {};
		var columnIndex = 0;
		var conditionGroups = jQuery('.conditionGroup', filterContainer);
		conditionGroups.each(function(index,domElement){
			var groupElement = jQuery(domElement);
			values[index+1] = {};
			var conditions = jQuery('.conditionList .conditionRow',groupElement);
			values[index+1]['columns'] = {};
			conditions.each(function(i, conditionDomElement){
				var rowElement = jQuery(conditionDomElement);
				var fieldSelectElement = jQuery('[name="columnname"]', rowElement);
				var valueSelectElement = jQuery('[name="value"]',rowElement);
				//To not send empty fields to server
				if(thisInstance.isEmptyFieldSelected(fieldSelectElement)) {
					return true;
				}
				var fieldDataInfo = fieldSelectElement.find('option:selected').data('fieldinfo');
				var fieldType = fieldDataInfo.type;
				var rowValues = {};
				if(fieldType == 'owner'){
					for(var key in fieldList) {
						var field = fieldList[key];
						if(field == 'value' && valueSelectElement.is('select')){
							rowValues[field] = valueSelectElement.find('option:selected').text();
						} else {
							rowValues[field] = jQuery('[name="'+field+'"]', rowElement).val();
						}
					}
				} else if (fieldType == 'picklist') {
					for(var key in fieldList) {
						var field = fieldList[key];
						if(field == 'value' && valueSelectElement.is('input')) {
							var commaSeperatedValues = valueSelectElement.val();
							var pickListValues = valueSelectElement.data('picklistvalues');
							var valuesArr = commaSeperatedValues.split(',');
							var newvaluesArr = [];
							for(i=0;i<valuesArr.length;i++){
								if(typeof pickListValues[valuesArr[i]] != 'undefined'){
									newvaluesArr.push(pickListValues[valuesArr[i]]);
								} else {
									newvaluesArr.push(valuesArr[i]);
								}
							}
							var reconstructedCommaSeperatedValues = newvaluesArr.join(',');
							rowValues[field] = reconstructedCommaSeperatedValues;
						} else {
							rowValues[field] = jQuery('[name="'+field+'"]', rowElement).val();
						}
					}

				} else {
					for(var key in fieldList) {
						var field = fieldList[key];
						rowValues[field] = jQuery('[name="'+field+'"]', rowElement).val();
					}
				}

				if(rowElement.is(":last-child")) {
					rowValues['column_condition'] = '';
				}
				values[index+1]['columns'][columnIndex] = rowValues;
				columnIndex++;
			});
			if(groupElement.find('div.groupCondition').length > 0) {
				values[index+1]['condition'] = conditionGroups.find('div.groupCondition [name="condition"]').val();
			}
		});
		return values;

	},

	/**
	 * Event handle which will be triggred on deletion of a condition row
	 */
	deleteConditionHandler : function(e) {
		var element = jQuery(e.currentTarget);
		var row = element.closest('.conditionRow');
		row.remove();
	},

	/**
	 * Event handler which is invoked on add condition
	 */
	registerAddCondition : function() {
		var thisInstance = this;
		this.getAddConditionElement().on('click',function(e){
			thisInstance.addConditionHandler(e);
		});
	},

	/**
	 * Function which will register field change event
	 */
	registerFieldChange : function() {
		var filterContainer = this.getFilterContainer();
		var thisInstance = this;
		filterContainer.on('change','select[name="columnname"]',function(e){
			thisInstance.loadConditions(jQuery(e.currentTarget));
			thisInstance.loadFieldSpecificUi(jQuery(e.currentTarget));
		});
	},

	/**
	 * Function which will register condition change
	 */
	registerConditionChange : function() {
		var filterContainer = this.getFilterContainer();
		var thisInstance = this;
		filterContainer.on('change','select[name="comparator"]', function(e){
			var comparatorSelectElement = jQuery(e.currentTarget);
			var row = comparatorSelectElement.closest('div.conditionRow');
			var fieldSelectElement = row.find('select[name="columnname"]');
			var selectedOption = fieldSelectElement.find('option:selected');
			//To handle the validation depending on condtion
			var fieldInfo = selectedOption.data('fieldinfo');
			var fieldType = fieldInfo.type;
			if(fieldType == 'picklist' || fieldType == 'owner' || fieldType == 'date' || fieldType == 'datetime'){
				thisInstance.loadFieldSpecificUi(fieldSelectElement);
			}
			thisInstance.addValidationToFieldIfNeeded(fieldSelectElement);
		});
	},

	/**
	 * Function to regisgter delete condition event
	 */
	registerDeleteCondition : function() {
		var thisInstance = this;
		var filterContainer = this.getFilterContainer();
		filterContainer.on('click', '.deleteCondition', function(e){
			thisInstance.deleteConditionHandler(e);
		});
	},

	/**
	 * Function which will regiter all events for this page
	 */
	registerEvents : function(){
		this.registerAddCondition();
		this.registerFieldChange();
		this.registerDeleteCondition();
		this.registerConditionChange();
	}
});

Vtiger_Field_Js('AdvanceFilter_Field_Js',{},{

	getUiTypeSpecificHtml : function() {
		var uiTypeModel = this.getUiTypeModel();
		return uiTypeModel.getUi();
	},
	getUiTypeModel : function() {
		var currentModule = app.getModuleName();

		var type = this.getType();
		if(type == 'picklist' || type == 'owner' || type == 'date' || type == 'datetime'){
			currentModule = 'AdvanceFilter';
		}
		var typeClassName = type.charAt(0).toUpperCase() + type.slice(1).toLowerCase();

		var moduleUiTypeClassName = window[currentModule + "_" + typeClassName+"_Field_Js"];
		var BasicUiTypeClassName = window["Vtiger_"+ typeClassName + "_Field_Js"];

		if(typeof moduleUiTypeClassName != 'undefined') {
			var instance =  new moduleUiTypeClassName();
			return instance.setData(this.getData());
		} else if (typeof BasicUiTypeClassName != 'undefined') {
			var instance =  new BasicUiTypeClassName();
			return instance.setData(this.getData());
		}
		return this;
	}
});

Vtiger_Picklist_Field_Js('AdvanceFilter_Picklist_Field_Js',{},{

	getUi : function(){
		var comparatorSelectedOptionVal = this.get('comparatorElementVal');
		if(comparatorSelectedOptionVal != 'e' && comparatorSelectedOptionVal !='n' ){
			var selectedOption = this.getValue();
			var pickListValues = this.getPickListValues();
			var tagsArray = new Array();
			jQuery.map( pickListValues, function(val, i) {
				tagsArray.push(val);
			});
			var pickListValuesArrayFlip = {};
			for(var key in pickListValues){
				var pickListValue = pickListValues[key];
				pickListValuesArrayFlip[pickListValue] = key;
			}
			var html = '<input type="hidden" class="row-fluid select2" name="'+ this.getName() +'">';
			var selectContainer = jQuery(html).val(selectedOption);
			selectContainer.data('tags', tagsArray).data('picklistvalues', pickListValuesArrayFlip);
			this.addValidationToElement(selectContainer);
			return selectContainer;
		} else {
			return this._super();
		}
	}
});

Vtiger_Owner_Field_Js('AdvanceFilter_Owner_Field_Js',{},{

	getUi : function(){
		var comparatorSelectedOptionVal = this.get('comparatorElementVal');
		if(comparatorSelectedOptionVal != 'e' && comparatorSelectedOptionVal !='n' ){
			var selectedOption = this.getValue();
			var pickListValues = this.getPickListValues();
			var tagsArray = new Array();
			jQuery.each( pickListValues, function(groups, blocks) {
				jQuery.each(blocks,function(i,e){
					tagsArray.push(e);
				})
			});
			var html = '<input data-tags="'+tagsArray +'" type="hidden" class="row-fluid select2" name="'+ this.getName() +'">';
			var selectContainer = jQuery(html).val(selectedOption);
			selectContainer.data('tags', tagsArray);
			this.addValidationToElement(selectContainer);
			return selectContainer;
		} else {
			return this._super();
		}
	}
});

Vtiger_Date_Field_Js('AdvanceFilter_Date_Field_Js',{},{
	/**
	 * Function to get the ui
	 * @return - input text field
	 */
	getUi : function() {
		var comparatorSelectedOptionVal = this.get('comparatorElementVal');
		if(comparatorSelectedOptionVal == 'bw'){
			var html = '<input class="dateField" data-calendar-type="range" name="'+ this.getName() +'" data-date-format="'+ this.getDateFormat() +'" type="text" ReadOnly="true" value="'+  this.getValue() + '">';
			var element = jQuery(html);
			return this.addValidationToElement(element);
		} else {
			return this._super();
		}
	}
});


AdvanceFilter_Date_Field_Js('AdvanceFilter_Datetime_Field_Js',{},{

});