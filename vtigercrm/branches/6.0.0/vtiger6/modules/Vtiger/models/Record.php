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
 * Vtiger Entity Record Model Class
 */
class Vtiger_Record_Model extends Vtiger_Base_Model {

	protected $module = false;

	/**
	 * Function to get the id of the record
	 * @return <Number> - Record Id
	 */
	public function getId() {
		return $this->get('id');
	}

	/**
	 * Function to set the id of the record
	 * @param <type> $value - id value
	 * @return <Object> - current instance
	 */
	public function setId($value){
		return $this->set('id',$value);
	}

	/**
	 * Fuction to get the Name of the record
	 * @return <String> - Entity Name of the record
	 */
	public function getName() {
		$displayName = $this->get('label');
		if(empty($displayName)) {
			$displayName = $this->getDisplayName();
		}
		return Vtiger_Util_Helper::toSafeHTML(decode_html($displayName));
	}

	/**
	 * Function to get the Module to which the record belongs
	 * @return Vtiger_Module_Model
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Function to set the Module to which the record belongs
	 * @param <String> $moduleName
	 * @return Vtiger_Record_Model or Module Specific Record Model instance
	 */
	public function setModule($moduleName) {
		$this->module = Vtiger_Module_Model::getInstance($moduleName);
		return $this;
	}

	/**
	 * Function to set the Module to which the record belongs from the Module model instance
	 * @param <Vtiger_Module_Model> $module
	 * @return Vtiger_Record_Model or Module Specific Record Model instance
	 */
	public function setModuleFromInstance($module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * Function to get the entity instance of the recrod
	 * @return CRMEntity object
	 */
	public function getEntity() {
		return $this->entity;
	}

	/**
	 * Function to set the entity instance of the record
	 * @param CRMEntity $entity
	 * @return Vtiger_Record_Model instance
	 */
	public function setEntity($entity) {
		$this->entity = $entity;
		return $this;
	}

	/**
	 * Function to get raw data
	 * @return <Array>
	 */
	public function getRawData() {
		return $this->rawData;
	}

	/**
	 * Function to set raw data
	 * @param <Array> $data
	 * @return Vtiger_Record_Model instance
	 */
	public function setRawData($data) {
		$this->rawData = $data;
		return $this;
	}

	/**
	 * Function to get the Detail View url for the record
	 * @return <String> - Record Detail View Url
	 */
	public function getDetailViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId();
	}

	/**
	 * Function to get the Edit View url for the record
	 * @return <String> - Record Edit View Url
	 */
	public function getEditViewUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId();
	}

	/**
	 * Function to get the Update View url for the record
	 * @return <String> - Record Upadte view Url
	 */
	public function getUpdatesUrl() {
		return $this->getDetailViewUrl()."&mode=showRecentActivities&page=1&tab_label=LBL_UPDATES";
	}

	/**
	 * Function to get the Delete Action url for the record
	 * @return <String> - Record Delete Action Url
	 */
	public function getDeleteUrl() {
		$module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&action='.$module->getDeleteActionName().'&record='.$this->getId();
	}

	/**
	 * Function to get the name of the module to which the record belongs
	 * @return <String> - Record Module Name
	 */
	public function getModuleName() {
		return $this->getModule()->get('name');
	}

	/**
	 * Function to get the Display Name for the record
	 * @return <String> - Entity Display Name for the record
	 */
	public function getDisplayName() {
		return getFullNameFromArray($this->getModuleName(),$this->getData());
	}

	/**
	 * Function to retieve display value for a field
	 * @param <String> $fieldName - field name for which values need to get
	 * @return <String>
	 */
	public function getDisplayValue($fieldName,$recordId = false){
		if(empty($recordId)) {
			$recordId = $this->getId();
		}
		$fieldModel = $this->getModule()->getField($fieldName);
		return $fieldModel->getDisplayValue($this->get($fieldName),$recordId);
	}

	/**
	 * Function to save the current Record Model
	 */
	public function save() {
		$this->getModule()->saveRecord($this);
	}

	/**
	 * Function to delete the current Record Model
	 */
	public function delete() {
		$this->getModule()->deleteRecord($this);
	}

	/**
	 * Static Function to get the instance of a clean Vtiger Record Model for the given module name
	 * @param <String> $moduleName
	 * @return Vtiger_Record_Model or Module Specific Record Model instance
	 */
	public static function getCleanInstance($moduleName) {
		//TODO: Handle permissions
		$focus = CRMEntity::getInstance($moduleName);
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		$instance = new $modelClassName();
		return $instance->setData($focus->column_fields)->setModule($moduleName)->setEntity($focus);
	}

	/**
	 * Static Function to get the instance of the Vtiger Record Model given the recordid and the module name
	 * @param <Number> $recordId
	 * @param <String> $moduleName
	 * @return Vtiger_Record_Model or Module Specific Record Model instance
	 */
	public static function getInstanceById($recordId, $module=null) {
		//TODO: Handle permissions
		if(is_object($module) && is_a($module, 'Vtiger_Module_Model')) {
			$moduleName = $module->get('name');
		} elseif (is_string($module)) {
			$module = Vtiger_Module_Model::getInstance($module);
			$moduleName = $module->get('name');
		} elseif(empty($module)) {
			$moduleName = getSalesEntityType($recordId);
			$module = Vtiger_Module_Model::getInstance($moduleName);
		}

		$focus = CRMEntity::getInstance($moduleName);
		$focus->id = $recordId;
		$focus->retrieve_entity_info($recordId, $moduleName);
		$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
		$instance = new $modelClassName();
		return $instance->setData($focus->column_fields)->set('id',$recordId)->setModuleFromInstance($module)->setEntity($focus);
	}

	/**
	 * Static Function to get the list of records matching the search key
	 * @param <String> $searchKey
	 * @return <Array> - List of Vtiger_Record_Model or Module Specific Record Model instances
	 */
	public static function getSearchResult($searchKey, $module=false) {
		$db = PearDatabase::getInstance();

        $query = 'SELECT * FROM vtiger_crmentity WHERE label LIKE ? AND vtiger_crmentity.deleted = 0';
		$params = array("%$searchKey%");

		if($module !== false) {
			$query .= ' AND setype = ?';
			$params[] = $module;
		}

		// TODO: limiting fetch per module is better option
		$query .= ' LIMIT 100';

		$result = $db->pquery($query, $params);
		$noOfRows = $db->num_rows($result);

		$moduleModels = array();
		$matchingRecords = array();
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])){
				$row['id'] = $row['crmid'];
				$moduleName = $row['setype'];
				if(!array_key_exists($moduleName, $moduleModels)) {
					$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
				}
				$moduleModel = $moduleModels[$moduleName];
				$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
				$recordInstance = new $modelClassName();
				$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
			}
		}
		return $matchingRecords;
	}

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isEditable() {
		return Users_Privileges_Model::isPermitted($this->getModuleName(), 'EditView', $this->getId());
	}

	/**
	 * Function to get details for user have the permissions to do actions
	 * @return <Boolean> - true/false
	 */
	public function isDeletable() {
		return Users_Privileges_Model::isPermitted($this->getModuleName(), 'Delete', $this->getId());
	}

    /**
     * Funtion to get Duplicate Record Url
     * @return <String>
     */
    public function getDuplicateRecordUrl(){
        $module = $this->getModule();
		return 'index.php?module='.$this->getModuleName().'&view='.$module->getEditViewName().'&record='.$this->getId().'&isDuplicate=true';

    }

	/**
	 * Function to get Display value for RelatedList
	 * @param <String> $value
	 * @return <String>
	 */
	public function getRelatedListDisplayValue($fieldName) {
		$fieldModel = $this->getModule()->getField($fieldName);
		return $fieldModel->getRelatedListDisplayValue($this->get($fieldName));
	}

	/**
	 * Function to delete corresponding image
	 * @param <type> $imageId
	 */
	public function deleteImage($imageId) {
		$db = PearDatabase::getInstance();

		$checkResult = $db->pquery('SELECT crmid FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', array($imageId));
		$crmId = $db->query_result($checkResult, 0, 'crmid');

		if ($this->getId() === $crmId) {
			$db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', array($imageId));
			$db->pquery('DELETE FROM vtiger_seattachmentsrel WHERE attachmentsid = ?', array($imageId));
			return true;
		}
		return false;
	}

}
