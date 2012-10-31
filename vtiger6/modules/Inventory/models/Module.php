<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/**
 * Inventory Module Model Class
 */
class Inventory_Module_Model extends Vtiger_Module_Model {

	/**
	 * Function to check whether the module is an entity type module or not
	 * @return <Boolean> true/false
	 */
	public function isQuickCreateSupported(){
		//SalesOrder module is not enabled for quick create
		return false;
	}

	static function getAllCurrencies() {
		return getAllCurrencies();
	}

	static function getAllProductTaxes() {
		return getAllTaxes('available');
	}

	static function getAllShippingTaxes() {
		return getAllTaxes('available', 'sh');
	}
}
