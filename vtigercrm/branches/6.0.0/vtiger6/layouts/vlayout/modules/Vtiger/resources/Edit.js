/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_Edit_Js",{

	//Event that will triggered when reference field is selected
	referenceSelectionEvent : 'Vtiger.Reference.Selection',

	//Event that will triggered when reference field is selected
	referenceDeSelectionEvent : 'Vtiger.Reference.DeSelection',

	//Event that will triggered before saving the record
	recordPreSave : 'Vtiger.Record.PreSave',

    refrenceMultiSelectionEvent : 'Vtiger.MultiReference.Selection',

	getInstance : function() {
		var module = app.getModuleName();;
	    var moduleClassName = module+"_Edit_Js";
		var fallbackClassName = Vtiger_Edit_Js;
		if(typeof window[moduleClassName] != 'undefined'){
			var instance = new window[moduleClassName]();
		}else{
			var instance = new fallbackClassName();
		}
	    return instance;
	}

},{

	formElement : false,

	getForm : function() {
		if(this.formElement == false){
			this.setForm(jQuery('#EditView'));
		}
		return this.formElement;
	},

	setForm : function(element){
		this.formElement = element;
		return this;
	},

    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
        if(sourceRecordElement.length > 0) {
            var sourceRecordId = sourceRecordElement.val();
        }
		var sourceRecordId = '';

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }

		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId
		}

        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },


	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
				var responseData = JSON.parse(data);
                var dataList = new Array();
				for(var id in responseData){
					var data = {
						'name' : responseData[id].name,
						'id' : id
					}
                    dataList.push(data);
                    if(!isMultiple) {
                        thisInstance.setReferenceFieldValue(parentElem, data);
                    }
				}

                if(isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
                }
			});
	},

	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
	},

	proceedRegisterEvents : function(){
		if(jQuery('.recordEditView').length > 0){
			return true;
		}else{
			return false;
		}
	},

	referenceModulePopupRegisterEvent : function(container){
		var thisInstance = this;
		container.find('.relatedPopup').on("click",function(e){
			thisInstance.openPopUp(e);
		});
		container.find('.referenceModulesList').chosen().change(function(e){
			var element = jQuery(e.currentTarget);
			var closestTD = element.closest('td').next();
			var popupReferenceModule = element.val();
			var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
			var prevSelectedReferenceModule = referenceModuleElement.val();
			referenceModuleElement.val(popupReferenceModule);

			//If Reference module is changed then we should clear the previous value
			if(prevSelectedReferenceModule != popupReferenceModule) {
				closestTD.find('.clearReferenceSelection').trigger('click');
			}
		});
	},

	getReferencedModuleName : function(parenElement){
		return jQuery('input[name="popupReferenceModule"]',parenElement).val();
	},

	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}

		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

	/**
	 * Function which will handle the reference auto complete event registrations
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
		container.find('input.autoComplete').autocomplete({
			'minLength' : '3',
			'source' : function(request, response){
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var tdElement = inputElement.closest('td');
				var searchValue = request.term;
				var params = {};
				var searchModule = thisInstance.getReferencedModuleName(tdElement);
				params.search_module = searchModule
				params.search_value = searchValue;
				thisInstance.searchModuleNames(params).then(function(data){
					var reponseDataList = new Array();
					var serverDataFormat = data.result
					if(serverDataFormat.length <= 0) {
						serverDataFormat = new Array({
							//TODO : client translation
							'label' : 'No Results Found',
							'type'  : 'no results'
						});
					}
					for(var id in serverDataFormat){
						var responseData = serverDataFormat[id];
						reponseDataList.push(responseData);
					}
					response(reponseDataList);
				});
			},
			'select' : function(event, ui ){
				var selectedItemData = ui.item;
				//To stop selection if no results is selected
				if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
					return false;
				}
				selectedItemData.name = selectedItemData.value;
				var element = jQuery(this);
				var tdElement = element.closest('td');
				thisInstance.setReferenceFieldValue(tdElement, selectedItemData)
			},
			'change' : function(event, ui) {
				var element = jQuery(this);
				//if you dont have readonly attribute means the user didnt select the item
				if(element.attr('readonly')== undefined) {
					element.closest('td').find('.clearReferenceSelection').trigger('click');
				}
			},
			'open' : function(event,ui) {
				//To Make the menu come up in the case of quick create
				jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

			}
		});
	},


	/**
	 * Function which will register reference field clear event
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerClearReferenceSelectionEvent : function(container) {
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldName = fieldNameElement.attr('name');
			fieldNameElement.val('');
			parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			e.preventDefault();
		})
	},

	/**
	 * Function which will register event to prevent form submission on pressing on enter
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerPreventingEnterSubmitEvent : function(container) {
		container.on('keypress', function(e){
            //Stop the submit when enter is pressed in the form
            var currentElement = jQuery(e.target);
            if(e.which == 13 && (!currentElement.is('textarea'))) {
                e. preventDefault();
            }
		})
	},

	/**
	 * Function which will give you all details of the selected record
	 * @params - an Array of values like {'record' : recordId, 'source_module' : searchModule, 'selectedName' : selectedRecordName}
	 */
	getRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
		AppConnector.request(url).then(
			function(data){
				if(data['success']) {
					aDeferred.resolve(data);
				} else {
					aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},


	registerTimeFields : function(container) {
		app.registerEventForTimeFields(container);
	},

    referenceCreateHandler : function(container) {
        var thisInstance = this;
        var postQuickCreateSave  = function(data) {
            var params = {};
            params.name = data.result._recordLabel;
            params.id = data.result._recordId;
            thisInstance.setReferenceFieldValue(container, params);
        }

        var referenceModuleName = this.getReferencedModuleName(container);
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
        if(quickCreateNode.length <= 0) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
        }
        quickCreateNode.trigger('click',{'callbackFunction':postQuickCreateSave});
    },

	/**
	 * Function which will register event for create of reference record
	 * This will allow users to create reference record from edit view of other record
	 */
	registerReferenceCreate : function(container) {
		var thisInstance = this;
		container.find('.createReferenceRecord').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var controlElementTd = element.closest('td');

			thisInstance.referenceCreateHandler(controlElementTd);
		})
	},

	/**
	 * Function to register the event status change event
	 */
	registerEventStatusChangeEvent : function(container){
		var followupContainer = container.find('.followUpContainer');
		container.find('select[name="eventstatus"]').on('change',function(e){
			var selectedOption = jQuery(e.currentTarget).val();
			if(selectedOption == 'Held'){
				followupContainer.show();
			} else{
				followupContainer.hide();
			}
		});
	},
	
	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
		this.referenceModulePopupRegisterEvent(container);
		this.registerAutoCompleteFields(container);
		this.registerClearReferenceSelectionEvent(container);
		this.registerPreventingEnterSubmitEvent(container);
		this.registerTimeFields(container);
		//Added here instead of register basic event of calendar. because this should be registered all over the places like quick create, edit, list..
		this.registerEventStatusChangeEvent(container);
	},

	/**
	 * Function to register event for image delete
	 */
	registerEventForImageDelete : function(){
		var formElement = this.getForm();
		var recordId = formElement.find('input[name="record"]').val();
		formElement.find('.imageDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var imageData = element.closest('div').find('img').data();
			var params = {
				'module' : app.getModuleName(),
				'action' : 'DeleteImage',
				'imageid' : imageData.imageId,
				'record' : recordId

			}
			AppConnector.request(params).then(
				function(data){
					if(data.success ==  true){
						element.closest('div').remove();
					}
				},
				function(error){
					//TODO : Handle error
				}
			)
		});
	},

	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'wideWidthType');
		if(widthType) {
			var elements = jQuery('#EditView').find('td');
			elements.addClass(widthType);
		}
	},

	registerSubmitEvent: function() {
		var editViewForm = this.getForm();
		
		editViewForm.submit(function(e){
			//Form should submit only once for multiple clicks also
			if(typeof editViewForm.data('submit') != "undefined") {
				return false;
			} else {
				if(editViewForm.validationEngine('validate')) {
					//Once the form is submiting add data attribute to that form element
					editViewForm.data('submit', 'true');
					//on submit form trigger the recordPreSave event
					var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
					editViewForm.trigger(recordPreSaveEvent, {'value' : 'edit'});
					if(recordPreSaveEvent.isDefaultPrevented()) {
						//If duplicate record validation fails, form should submit again
						editViewForm.removeData('submit');
						e.preventDefault();
					}
				} else {
					//If validation fails, form should submit again
					editViewForm.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(editViewForm);
				}
			}
		});
	},

	registerEvents: function(){
		var editViewForm = this.getForm();
		var statusToProceed = this.proceedRegisterEvents();
		if(!statusToProceed){
			return;
		}

		this.registerBasicEvents(this.getForm());
		this.registerEventForImageDelete();
		this.registerSubmitEvent();
		
		app.registerEventForDatePickerFields('#EditView');
		editViewForm.validationEngine(app.validationEngineOptions);

		this.registerReferenceCreate(editViewForm);
		this.triggerDisplayTypeEvent();
	}
})
jQuery(document).ready(function() {
	var editViewInstance = Vtiger_Edit_Js.getInstance();
	editViewInstance.registerEvents();
});

