/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Products_Edit_Js",{
	
},{
	baseCurrency : '',
	
	baseCurrencyName : '',
	//Container which stores the multi currency element
	multiCurrencyContainer : false,
	
	//Container which stores unit price
	unitPrice : false,
	
	/**
	 * Function to get unit price
	 */
	getUnitPrice : function(){
		if(this.unitPrice == false) {
			this.unitPrice = jQuery('input.unitPrice',this.getForm());
		}
		return this.unitPrice;
	},
	
	/**
	 * Function to get more currencies container
	 */
	getMoreCurrenciesContainer : function(){
		if(this.multiCurrencyContainer == false) {
			this.multiCurrencyContainer = jQuery('.multiCurrencyEditUI');
		}
		return this.multiCurrencyContainer;
	},
	
	/**
	 * Function which aligns data just below global search element
	 */
	alignBelowUnitPrice : function(dataToAlign) {
		var parentElem = jQuery('input[name="unit_price"]',this.getForm());
		dataToAlign.position({
			'of' : parentElem,
			'my': "left top",
			'at': "left bottom",
			'collision' : 'flip'
		});
		return this;
	},
	
	/**
	 * Function to get current Element
	 */
	getCurrentElem : function(e){
		return jQuery(e.currentTarget);
	},
	/**
	 *Function to register events for taxes
	 */
	registerEventForTaxes : function(){
		var thisInstance = this;
		var formElem = this.getForm();
		jQuery('.taxes').on('change',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var taxBox  = elem.data('taxName');
			if(elem.is(':checked')) {
				jQuery('input[name='+taxBox+']',formElem).removeClass('hide').show();
			}else{
				jQuery('input[name='+taxBox+']',formElem).addClass('hide');
			}

		});
		return this;
	},
	
	/**
	 * Function to register event for enabling base currency on radio button clicked
	 */
	registerEventForEnableBaseCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.baseCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentElem = elem.closest('tr');
			if(elem.is(':checked')) {
				var convertedPrice = jQuery('.convertedPrice',parentElem).val();
				thisInstance.baseCurrencyName = parentElem.data('currencyId');
				thisInstance.baseCurrency = convertedPrice;
			}
		});
		return this;
	},
	
	/**
	 * Function to register event for reseting the currencies
	 */
	registerEventForResetCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('click','.currencyReset',function(e){
			var parentElem = thisInstance.getCurrentElem(e).closest('tr');
			var	unitPrice = thisInstance.getDataBaseFormatUnitPrice();
			var conversionRate = jQuery('.conversionRate',parentElem).val();
			var price = parseFloat(unitPrice) * parseFloat(conversionRate);
			jQuery('.convertedPrice',parentElem).val(price.toFixed(2));
		});
		return this;
	},
	
	/**
	 *  Function to return stripped unit price
	 */
		getDataBaseFormatUnitPrice : function(){
			var field = this.getUnitPrice();
			var unitPrice = field.val();
			if(unitPrice == ''){
				unitPrice = 0;
			}else{
				var fieldData = field.data();
				var strippedValue = unitPrice.replace(fieldData.groupSeperator, '');
				var strippedValue = strippedValue.replace(fieldData.decimalSeperator, '.');
				unitPrice = strippedValue;
			}
			return unitPrice;
		},
        
    calculateConversionRate : function() {
        var container = this.getMoreCurrenciesContainer();
        var baseCurrencyRow = container.find('.baseCurrency').filter(':checked').closest('tr');
        var baseCurrencyConvestationRate = baseCurrencyRow.find('.conversionRate');
        //if basecurrency has conversation rate as 1 then you dont have caliculate conversation rate
        if(baseCurrencyConvestationRate.val() == "1") {
            return;
        }
        var baseCurrencyRatePrevValue = baseCurrencyConvestationRate.val();
        
        container.find('.conversionRate').each(function(key,domElement) {
            var element = jQuery(domElement);
            if(!element.is(baseCurrencyConvestationRate)){
                var prevValue = element.val();
                element.val((prevValue/baseCurrencyRatePrevValue));
            }
        });
        baseCurrencyConvestationRate.val("1");
    },
	/**
	 * Function to register event for enabling currency on checkbox checked
	 */
	
	registerEventForEnableCurrency : function(){
		var container = this.getMoreCurrenciesContainer();
		var thisInstance = this;
		jQuery(container).on('change','.enableCurrency',function(e){
			var elem = thisInstance.getCurrentElem(e);
			var parentRow = elem.closest('tr');
			
			if(elem.is(':checked')) {
				elem.attr('checked',"checked");
				var conversionRate = jQuery('.conversionRate',parentRow).val();
				var unitPrice = thisInstance.getDataBaseFormatUnitPrice();
				var price = parseFloat(unitPrice)*parseFloat(conversionRate);
				jQuery('input',parentRow).attr('disabled', true).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', true).removeAttr('disabled');
				jQuery('input.convertedPrice',parentRow).val(price)
			}else{
				jQuery('input',parentRow).attr('disabled', true);
				jQuery('input.enableCurrency',parentRow).removeAttr('disabled');
				jQuery('button.currencyReset', parentRow).attr('disabled', 'disabled');
			}
		})
		return this;
	},
	
	/**
	 * Function to get more currencies UI
	 */
	getMoreCurrenciesUI : function(){
		var aDeferred = jQuery.Deferred();
		var moduleName = app.getModuleName();
		var baseCurrency = jQuery('input[name="base_currency"]').val();
		var recordId = jQuery('input[name="record"]').val();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesUi = moreCurrenciesContainer.find('.multiCurrencyEditUI');
		var moreCurrenciesUi;
			
		if(moreCurrenciesUi.length == 0){
			var moreCurrenciesParams = {
				'module' : moduleName,
				'view' : "MoreCurrenciesList",
				'currency' : baseCurrency,
				'record' : recordId
			}

			AppConnector.request(moreCurrenciesParams).then(
				function(data){
					moreCurrenciesContainer.html(data);
					aDeferred.resolve(data);
				},
				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		} else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},
	
	/*
	 * function to register events for more currencies link
	 */
	registerEventForMoreCurrencies : function(){
		var thisInstance = this;
		var form = this.getForm();
		jQuery('#moreCurrencies').on('click',function(e){
			var progressInstance = jQuery.progressIndicator();
			thisInstance.getMoreCurrenciesUI().then(function(data){
				var moreCurrenciesUi;
				moreCurrenciesUi = jQuery('#moreCurrenciesContainer').find('.multiCurrencyEditUI');
				if(moreCurrenciesUi.length > 0){
					moreCurrenciesUi = moreCurrenciesUi.clone(true,true);
					progressInstance.hide();
					var css = {'text-align' : 'left','width':'65%'};
					var callback = function(data){
						var params = app.validationEngineOptions;
						var form = data.find('#currencyContainer');
						params.onValidationComplete = function(form, valid){
							if(valid) {
								thisInstance.saveCurrencies();
							}
							return false;
						}
						form.validationEngine(params);
						app.showScrollBar(data.find('.currencyContent'), {'height':'400px'});
						thisInstance.baseCurrency = thisInstance.getUnitPrice().val();
						var multiCurrencyEditUI = jQuery('.multiCurrencyEditUI');
						thisInstance.multiCurrencyContainer = multiCurrencyEditUI;
                        thisInstance.calculateConversionRate();
						thisInstance.registerEventForEnableCurrency().registerEventForEnableBaseCurrency()
											.registerEventForResetCurrency().triggerForBaseCurrencyCalc();
					}
					var contentInsideForm = moreCurrenciesUi.find('.multiCurrencyContainer').html();
					moreCurrenciesUi.find('.multiCurrencyContainer').remove();
					var form = '<form id="currencyContainer"></form>'
					jQuery(form).insertAfter(moreCurrenciesUi.find('.modal-header'));
					moreCurrenciesUi.find('form').html(contentInsideForm);

					var modalWindowParams = {
						data : moreCurrenciesUi,
						css : css,
						cb : callback
					}
					app.showModalWindow(modalWindowParams)
				}
			})
		});
	},
	/**
	 * Function to calculate base currency price value if unit
	 * present on click of more currencies
	 */
	triggerForBaseCurrencyCalc : function(){
		var multiCurrencyEditUI = this.getMoreCurrenciesContainer();
		var baseCurrency = multiCurrencyEditUI.find('.enableCurrency');
		jQuery.each(baseCurrency,function(key,val){
			if(jQuery(val).is(':checked')){
				var baseCurrencyRow = jQuery(val).closest('tr');
				baseCurrencyRow.find('.currencyReset').trigger('click');
			}else{
                var baseCurrencyRow = jQuery(val).closest('tr');
                baseCurrencyRow.find('.convertedPrice').val('');
            }
		})
	},
	
	/**
	 * Function to register onchange event for unit price
	 */
	registerEventForUnitPrice : function(){
		var thisInstance = this;
		var unitPrice = this.getUnitPrice();
		unitPrice.on('change',function(){
			thisInstance.triggerForBaseCurrencyCalc();
		})
	},

	registerRecordPreSaveEvent : function(form) {
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var unitPrice = form.find('[name="unit_price"]').val();
			var baseCurrencyName = form.find('[name="base_currency"]').val();
			form.find('[name="'+ baseCurrencyName +'"]').val(unitPrice);
			form.find('#requstedUnitPrice').attr('name',baseCurrencyName).val(unitPrice);
		})
	},
	
	saveCurrencies : function(){
		var thisInstance = this;
		var editViewForm = thisInstance.getForm();
		var modalContainer = jQuery('#globalmodal');
		var selectedBaseCurrency = modalContainer.find('.baseCurrency').filter(':checked');
		selectedBaseCurrency.attr('checked',"checked");
		modalContainer.find('.baseCurrency').filter(":not(:checked)").removeAttr('checked');
		var enabledBaseCurrency = modalContainer.find('.enableCurrency').filter(':checked');
		enabledBaseCurrency.attr('checked',"checked");
		modalContainer.find('.enableCurrency').filter(":not(:checked)").removeAttr('checked');
		var parentElem = selectedBaseCurrency.closest('tr');
		var convertedPrice = jQuery('.convertedPrice',parentElem).val();
		thisInstance.baseCurrencyName = parentElem.data('currencyId');
		thisInstance.baseCurrency = convertedPrice;
		
		thisInstance.getUnitPrice().val(thisInstance.baseCurrency);
		jQuery('input[name="base_currency"]',editViewForm).val(thisInstance.baseCurrencyName);
		
		var savedValuesOfMultiCurrency = modalContainer.find('.currencyContent').html();
		var moreCurrenciesContainer = jQuery('#moreCurrenciesContainer');
		moreCurrenciesContainer.find('.currencyContent').html(savedValuesOfMultiCurrency);
		app.hideModalWindow();
	},
	
	registerEvents : function(){
		this._super();
		this.registerEventForMoreCurrencies();
		this.registerEventForTaxes();
		this.registerEventForUnitPrice();
		this.registerRecordPreSaveEvent();
	}
})