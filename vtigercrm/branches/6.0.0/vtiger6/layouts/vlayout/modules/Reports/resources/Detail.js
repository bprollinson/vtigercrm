/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Reports_Detail_Js",{},{
	advanceFilterInstance : false,
	detailViewContentHolder : false,
	HeaderContentsHolder : false, 
	
	
	getContentHolder : function() {
		if(this.detailViewContentHolder == false) {
			this.detailViewContentHolder = jQuery('div.contentsDiv');
		}
		return this.detailViewContentHolder;
	},
	
	getHeaderContentsHolder : function(){
		if(this.HeaderContentsHolder == false) {
			this.HeaderContentsHolder = jQuery('div.reportsDetailHeader ');
		}
		return this.HeaderContentsHolder;
	},
	
	calculateValues : function(){
		//handled advanced filters saved values.
		var advfilterlist = this.advanceFilterInstance.getValues();
		return JSON.stringify(advfilterlist);
	},
		
	registerSaveOrGenerateReportEvent : function(){
		var thisInstance = this;
		jQuery('.generateReport').on('click',function(e){
			var advFilterCondition = thisInstance.calculateValues();
			var recordId = thisInstance.getRecordId();
			var currentMode = jQuery(e.currentTarget).data('mode');
			var postData = {
				'advanced_filter': advFilterCondition,
				'record' : recordId,
				'view' : "SaveAjax",
				'module' : app.getModuleName(),
				'mode' : currentMode
			};
			var progressIndicatorElement = jQuery.progressIndicator({
			});
			AppConnector.request(postData).then(
				function(data){
					progressIndicatorElement.progressIndicator({mode:'hide'})
					thisInstance.getContentHolder().find('#reportDetails').replaceWith(data);
				}
			);
		});
	},
	
	registerEvents : function(){
		this._super();
		this.registerSaveOrGenerateReportEvent();
		var container = this.getContentHolder();
		this.advanceFilterInstance = new Vtiger_AdvanceFilter_Js(jQuery('.filterContainer',container));
	}
});