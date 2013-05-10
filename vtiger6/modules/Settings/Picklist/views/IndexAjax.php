<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Picklist_IndexAjax_View extends Settings_Vtiger_IndexAjax_View {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showEditView');
        $this->exposeMethod('showDeleteView');
        $this->exposeMethod('getPickListDetailsForModule');
        $this->exposeMethod('getPickListValueForField');
        $this->exposeMethod('getPickListValueByRole');
		$this->exposeMethod('showAssignValueToRoleView');
    }
    
    public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if($this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
        }
    }
    
    public function showEditView(Vtiger_Request $request) {
        $module = $request->get('source_module');
        $pickListFieldId = $request->get('pickListFieldId');
        $fieldModel = Vtiger_Field_Model::getInstance($pickListFieldId);
        $valueToEdit = $request->get('fieldValue');
        
		$selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
        $qualifiedName = $request->getModule(false);
        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
        $viewer->assign('SOURCE_MODULE', $module);
        $viewer->assign('SOURCE_MODULE_NAME',$module);
        $viewer->assign('FIELD_MODEL',$fieldModel);
        $viewer->assign('FIELD_VALUE',$valueToEdit);
		$viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        echo $viewer->view('EditView.tpl', $qualifiedName, true);
    }
    
    public function showDeleteView(Vtiger_Request $request) {
        $module = $request->get('source_module');
        $pickListFieldId = $request->get('pickListFieldId');
        $fieldModel = Vtiger_Field_Model::getInstance($pickListFieldId);
        $valueToDelete = $request->get('fieldValue');
        
		$selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
        $qualifiedName = $request->getModule(false);
        $viewer = $this->getViewer($request);
		$moduleName = $request->getModule();
        $viewer->assign('SOURCE_MODULE', $module);
        $viewer->assign('SOURCE_MODULE_NAME',$module);
        $viewer->assign('FIELD_MODEL',$fieldModel);

		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
		$viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
        $viewer->assign('FIELD_VALUES',$valueToDelete);
        echo $viewer->view('DeleteView.tpl', $qualifiedName, true);
    }
    
    public function getPickListDetailsForModule(Vtiger_Request $request) {
        $sourceModule = $request->get('source_module');
        $moduleModel = Settings_Picklist_Module_Model::getInstance($sourceModule);
        //TODO: see if you needs to optimize this , since its will gets all the fields and filter picklist fields
        $pickListFields = $moduleModel->getFieldsByType(array('picklist','multipicklist'));
        
        $qualifiedName = $request->getModule(false);
        
        $viewer = $this->getViewer($request);
        $viewer->assign('PICKLIST_FIELDS',$pickListFields);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->view('ModulePickListDetail.tpl',$qualifiedName);
    }
    
    public function getPickListValueForField(Vtiger_Request $request) {
        $sourceModule = $request->get('source_module');
        $pickFieldId = $request->get('pickListFieldId');
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);
        
		$moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);
       
        $selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
        $viewer->view('PickListValueDetail.tpl',$qualifiedName);
    }
    
    
    public function getPickListValueByRole(Vtiger_Request $request) {
        $sourceModule = $request->get('source_module');
        $pickFieldId = $request->get('pickListFieldId');
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);
        $moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);
        
        $userSelectedRoleId = $request->get('rolesSelected');
		
        $pickListValuesForRole = $fieldModel->getPicklistValues(array($userSelectedRoleId),'CONJUNCTION');
        $allPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
        
        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLE_PICKLIST_VALUES',$pickListValuesForRole);
        $viewer->assign('ALL_PICKLIST_VALUES', $allPickListValues);
        $viewer->view('PickListValueByRole.tpl',$qualifiedName);
    }
	
	 /**
     * Function which will assign existing values to the roles
     * @param Vtiger_Request $request
     */
    public function showAssignValueToRoleView(Vtiger_Request $request) {
		$sourceModule = $request->get('source_module');
        $pickFieldId = $request->get('pickListFieldId');
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);
        
		$moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);
       
        $selectedFieldAllPickListValues = Vtiger_Util_Helper::getPickListValues($fieldModel->getName());
        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
		$viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
		$viewer->assign('MODULE',$moduleName);
		$viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$selectedFieldAllPickListValues);
        $viewer->view('AssignValueToRole.tpl',$qualifiedName);
	}
}