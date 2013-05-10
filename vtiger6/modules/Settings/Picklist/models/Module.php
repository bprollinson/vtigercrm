<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_Picklist_Module_Model extends Vtiger_Module_Model {
    
    public function getPickListTableName($fieldName) {
        return 'vtiger_'.$fieldName;
    }
    
    public function getFieldsByType($type) {
        $hardCodedPickFields = array('hdnTaxType','email_flag');
        
        $fieldModels = parent::getFieldsByType($type);
        $fields = array();
        foreach($fieldModels as $fieldName=>$fieldModel) {
            if(in_array($fieldName,$hardCodedPickFields)){
                continue;
            }
            $fields[$fieldName] = Settings_Picklist_Field_Model::getInstanceFromFieldObject($fieldModel);
        }
        return $fields;
    }
    
    public function addPickListValues($fieldModel, $newValue, $rolesSelected = array()) {
        $db = PearDatabase::getInstance();
        $pickListFieldName = $fieldModel->getName();
        $id = $db->getUniqueID("vtiger_$pickListFieldName");
        vimport('~~/include/ComboUtil.php');
        $picklist_valueid = getUniquePicklistID();
		$tableName = 'vtiger_'.$pickListFieldName;
		$maxSeqQuery = 'SELECT max(sortorderid) as maxsequence FROM '.$tableName;
		$result = $db->pquery($maxSeqQuery, array());
		$sequence = $db->query_result($result,0,'maxsequence');
		
        if($fieldModel->isRoleBased()) {
            $sql = 'insert into '.$tableName.' values (?,?,?,?,?)';
            $db->pquery($sql, array($id, $newValue, 1, $picklist_valueid,++$sequence));
        }else{
            $sql = 'INSERT INTO '.$tableName.' VALUES (?,?,?,?)';
            $db->pquery($sql, array($id, $newValue, 1, ++$sequence));
        }
        
        if($fieldModel->isRoleBased() && !empty($rolesSelected)) {
            $sql = "select picklistid from vtiger_picklist where name=?";
            $result = $db->pquery($sql, array($pickListFieldName));
            $picklistid = $db->query_result($result,0,"picklistid");
            //add the picklist values to the selected roles
            for($j=0;$j<count($rolesSelected);$j++){
                $roleid = $rolesSelected[$j];

                $sql ="SELECT max(sortid)+1 as sortid 
                       FROM vtiger_role2picklist left join vtiger_$pickListFieldName 
                           on vtiger_$pickListFieldName.picklist_valueid=vtiger_role2picklist.picklistvalueid 
                       WHERE roleid=? and picklistid=?";
                $sortid = $db->query_result($db->pquery($sql, array($roleid, $picklistid)),0,'sortid');

                $sql = "insert into vtiger_role2picklist values(?,?,?,?)";
                $db->pquery($sql, array($roleid, $picklist_valueid, $picklistid, $sortid));
            }
        
        }
        return $picklist_valueid;
    }
    
    public function renamePickListValues($pickListFieldName, $oldValue, $newValue, $moduleName) {
        $db = PearDatabase::getInstance();
        
        $query = 'SELECT tablename,columnname FROM vtiger_field WHERE fieldname=? and presence IN (0,2)';
        $result = $db->pquery($query, array($pickListFieldName));
        $row = $db->query_result_rowdata($result,'0');
        $tableName = $row['tablename'];
        $columnName = $row['columnname'];
        
        
        $query = 'UPDATE '.$this->getPickListTableName($pickListFieldName).' SET '.$pickListFieldName.'=? WHERE '.$pickListFieldName.'=?';
        $db->pquery($query, array($newValue, $oldValue));
        
        $query = 'UPDATE '.$tableName.' SET '.$columnName.'=? WHERE '.$columnName.'=?';
        $db->pquery($query, array($newValue, $oldValue));
        
        $query = "UPDATE vtiger_field SET defaultvalue=? WHERE defaultvalue=? AND tablename=? AND columnname=?";
        $db->pquery($query, array($newValue, $oldValue, $tableName, $columnName));
        
        vimport('~~/include/utils/CommonUtils.php');
        
        $query = "UPDATE vtiger_picklist_dependency SET sourcevalue=? WHERE sourcevalue=? AND sourcefield=? AND tabid=?";
        $db->pquery($query, array($newValue, $oldValue, $tableName, getTabid($moduleName)));
        return true;
    }
    
    public function remove($pickListFieldName , $valueToDelete, $replaceValue , $moduleName) {
        $db = PearDatabase::getInstance();
		if(!is_array($valueToDelete)) {
			$valueToDelete = array($valueToDelete);
		}
        
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListFieldName,$this);
        //if role based then we need to delete all the values in role based picklist
        if($fieldModel->isRoleBased()) {
            $picklistValueIdToDelete = array();
            $query = 'SELECT picklist_valueid FROM '.$this->getPickListTableName($pickListFieldName).
                     ' WHERE '.$pickListFieldName.' IN ('.generateQuestionMarks($valueToDelete).')';
            $result = $db->pquery($query,$valueToDelete);
            $num_rows = $db->num_rows($result);
            for($i=0;$i<$num_rows;$i++) {
                $picklistValueIdToDelete[] = $db->query_result($result,$i,'picklist_valueid');
            }
            $query = 'DELETE FROM vtiger_role2picklist WHERE picklistvalueid IN ('.generateQuestionMarks($picklistValueIdToDelete).')';
            $db->pquery($query,$picklistValueIdToDelete);
        }
		
        $query = 'DELETE FROM '. $this->getPickListTableName($pickListFieldName).
					' WHERE '.$pickListFieldName.' IN ('.  generateQuestionMarks($valueToDelete).')';
        $db->pquery($query, $valueToDelete);
        
        vimport('~~/include/utils/CommonUtils.php');
        $tabId = getTabId($moduleName);
        $query = 'DELETE FROM vtiger_picklist_dependency WHERE sourcevalue IN ('. generateQuestionMarks($valueToDelete) .')'.
				' AND sourcefield=? AND tabid=?';
		$params = $valueToDelete;
		array_push($params, $pickListFieldName, $tabId);
        $db->pquery($query, $params);
        
        $query='SELECT tablename,columnname FROM vtiger_field WHERE fieldname=? AND presence in (0,2) AND tabid=?';
        $result = $db->pquery($query, array($pickListFieldName, $tabId));
        $num_row = $db->num_rows($result);
        
        for($i=0; $i<$num_row; $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $tableName = $row['tablename'];
            $columnName = $row['columnname'];
            
            $query = 'UPDATE '.$tableName.' SET '.$columnName.'=? WHERE '.$columnName.' IN ('.  generateQuestionMarks($valueToDelete).')';
			$params = array($replaceValue);
			$params = array_merge($params, $valueToDelete);
            $db->pquery($query, $params);
            $query = 'UPDATE vtiger_field SET defaultvalue=? WHERE defaultvalue IN ('. generateQuestionMarks($valueToDelete) .')AND tablename=? AND columnname=?';
			$params = array($replaceValue);
			$params = array_merge($params, $valueToDelete);
			array_push($params, $tableName, $columnName);
            $db->pquery($query, $params);
        }
        return true;
    }
    
    public function enableOrDisableValuesForRole($picklistFieldName, $valuesToEnables, $valuesToDisable, $roleIdList) {
        $db = PearDatabase::getInstance();
        //To disable die On error since we will be doing insert without chekcing
        $dieOnErrorOldValue = $db->dieOnError;
        $db->dieOnError = false;
        
		$sql = "select picklistid from vtiger_picklist where name=?";
		$result = $db->pquery($sql, array($picklistFieldName));
		$picklistid = $db->query_result($result,0,"picklistid");;
        
        $pickListValueList = array_merge($valuesToEnables,$valuesToDisable);
        $pickListValueDetails = array();
        $query = 'SELECT picklist_valueid,'. $picklistFieldName.
                 ' FROM '.$this->getPickListTableName($picklistFieldName).
                 ' WHERE '.$picklistFieldName .' IN ('.  generateQuestionMarks($pickListValueList).')';
        $result = $db->pquery($query, $pickListValueList);
        $num_rows = $db->num_rows($result);
        
        for($i=0; $i<$num_rows; $i++) {
            $row = $db->query_result_rowdata($result,$i);
            
            $pickListValueDetails[$row[$picklistFieldName]] =array('picklistvalueid'=>$row['picklist_valueid'],
																	'picklistid'=>$picklistid);
        }
        $insertValueList = array();
        $deleteValueList = array();
        foreach($roleIdList as $roleId) {
            foreach($valuesToEnables  as $picklistValue) {
                $valueDetail = $pickListValueDetails[$picklistValue];
                $pickListValueId = $valueDetail['picklistvalueid'];
                $picklistId = $valueDetail['picklistid'];
                $insertValueList[] = '("'.$roleId.'","'.$pickListValueId.'","'.$picklistId.'")';
            }
            
            foreach($valuesToDisable as $picklistValue) {
                $valueDetail = $pickListValueDetails[$picklistValue];
                $pickListValueId = $valueDetail['picklistvalueid'];
                $picklistId = $valueDetail['picklistid'];
                $deleteValueList[] = ' ( roleid = "'.$roleId.'" AND '.'picklistvalueid = "'.$pickListValueId.'") ';
            }
        }
        $query = 'INSERT IGNORE INTO vtiger_role2picklist (roleid,picklistvalueid,picklistid) VALUES '.implode(',',$insertValueList);
        $result = $db->pquery($query,array());

		$deleteQuery = 'DELETE FROM vtiger_role2picklist WHERE '.implode(' OR ',$deleteValueList);

		$result = $db->pquery($deleteQuery,array());
        
        //retaining to older value
        $db->dieOnError = $dieOnErrorOldValue;
         
    }
    
    public function updateSequence($pickListFieldName , $picklistValues) {
        $db = PearDatabase::getInstance();
        
        $query = 'UPDATE '.$this->getPickListTableName($pickListFieldName).' SET sortorderid = CASE ';
        foreach($picklistValues as $values => $sequence) {
            $query .= ' WHEN '.$pickListFieldName.'="'.$values.'" THEN "'.$sequence.'"';
        }
		$query .= ' END';
        $result = $db->pquery($query, array());
    }
 

    public static function getPicklistSupportedModules() {
         $db = PearDatabase::getInstance();
        
        // vtlib customization: Ignore disabled modules.
        $query = 'SELECT distinct vtiger_tab.tablabel, vtiger_tab.name as tabname 
                  FROM vtiger_tab 
                        inner join vtiger_field on vtiger_tab.tabid=vtiger_field.tabid 
                  WHERE uitype IN (15,33,16) and vtiger_field.tabid NOT IN (29,10)  and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2) 
                  ORDER BY vtiger_tab.tabid ASC';
        // END
        $result = $db->pquery($query, array());
        
        $modulesModelsList = array();
        while($row = $db->fetch_array($result)){
            $moduleLabel = $row['tablabel'];
            $moduleName  = $row['tabname'];
            $instance = new self();
            $instance->name = $moduleName;
            $instance->label = $moduleLabel;
            $modulesModelsList[] = $instance;
        }
        return $modulesModelsList;
    }
    
    
    /**
	 * Static Function to get the instance of Vtiger Module Model for the given id or name
	 * @param mixed id or name of the module
	 */
	public static function getInstance($value) {
		//TODO : add caching
		$instance = false;
		    $moduleObject = parent::getInstance($value);
		    if($moduleObject) {
			$instance = self::getInstanceFromModuleObject($moduleObject);
		    }
		return $instance;
	}

	/**
	 * Function to get the instance of Vtiger Module Model from a given Vtiger_Module object
	 * @param Vtiger_Module $moduleObj
	 * @return Vtiger_Module_Model instance
	 */
	public static function getInstanceFromModuleObject(Vtiger_Module $moduleObj){
		$objectProperties = get_object_vars($moduleObj);
		$moduleModel = new self();
		foreach($objectProperties as $properName=>$propertyValue){
			$moduleModel->$properName = $propertyValue;
		}
		return $moduleModel;
	}
}