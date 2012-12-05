<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Detail_View extends Vtiger_Detail_View {

    function checkPermission(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        $record = $request->get('record');

        if(($currentUserModel->isAdminUser() == true && $record != 1) ||
                $currentUserModel->get('id') == $record){
            return true;
        }else{
			throw new AppException('LBL_PERMISSION_DENIED');
        }
	}

    /**
	 * Function to returns the preProcess Template Name
	 * @param <type> $request
     * @return <String>
	 */
    function preProcessTplName(Vtiger_Request $request) {
		return 'UserViewPreProcess.tpl';
	}

    /**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}

    function preProcess(Vtiger_Request $request, $display=true) {
		if($this->checkPermission($request)){
            $recordId = $request->get('record');
            $moduleName = $request->getModule();
            $detailViewModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            $recordModel = $detailViewModel->getRecord();

            $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
            $detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);

            $viewer = $this->getViewer($request);
            $viewer->assign('RECORD', $recordModel);

            $viewer->assign('MODULE_MODEL', $detailViewModel->getModule());
            $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

            $viewer->assign('IS_EDITABLE', $detailViewModel->getRecord()->isEditable($moduleName));
            $viewer->assign('IS_DELETABLE', $detailViewModel->getRecord()->isDeletable($moduleName));

            $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
            $linkModels = $detailViewModel->getSideBarLinks($linkParams);
            $viewer->assign('QUICK_LINKS', $linkModels);
            $viewer->assign('PAGETITLE', $this->getPageTitle($request));
            $viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
            $viewer->assign('STYLES',$this->getHeaderCss($request));
            $viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));

            $menuModelsList = Vtiger_Menu_Model::getAll(true);
            $selectedModule = $request->getModule();
            $menuStructure = Vtiger_MenuStructure_Model::getInstanceFromMenuList($menuModelsList, $selectedModule);

			// Order by pre-defined automation process for QuickCreate.
			uksort($menuModelsList, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
		
            $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
            $companyLogo = $companyDetails->getLogo();

            $viewer->assign('CURRENTDATE', date('Y-n-j'));
            $viewer->assign('MODULE', $selectedModule);
            $viewer->assign('PARENT_MODULE', $request->get('parent'));
            $viewer->assign('MENUS', $menuModelsList);
            $viewer->assign('MENU_STRUCTURE', $menuStructure);
            $viewer->assign('COMPANY_LOGO',$companyLogo);
            $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

            $homeModuleModel = Vtiger_Module_Model::getInstance('Home');
            $viewer->assign('HOME_MODULE_MODEL', $homeModuleModel);
            $viewer->assign('HEADER_LINKS',$this->getHeaderLinks());
            $viewer->assign('ANNOUNCEMENT', $this->getAnnouncement());
            $viewer->assign('CURRENT_VIEW', $request->get('view'));
			$viewer->assign('SKIN_PATH', Vtiger_Theme::getCurrentUserThemePath());
			
            if($display) {
                $this->preProcessDisplay($request);
            }
        }
    }

    protected function preProcessDisplay(Vtiger_Request $request) {
		$viewer = $this->getViewer($request);
		$viewer->view($this->preProcessTplName($request), $request->getModule());
	}

	public function process(Vtiger_Request $request) {
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

		$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);

		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
		
		$dayStartPicklistValues = Users_Record_Model::getDayStartsPicklistValues($recordStructureInstance->getStructure());

		$viewer = $this->getViewer($request);
		$viewer->assign("DAY_STARTS", Zend_Json::encode($dayStartPicklistValues));
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		return parent::process($request);
	}
}
