<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Users_Edit_View extends Vtiger_Edit_View {

    public function checkPermission(Vtiger_Request $request) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $record = $request->get('record');

        if(($currentUserModel->isAdminUser() == true && $record != 1) ||
                $currentUserModel->get('id') == $record){
            return true;
        }else{
			throw new AppException('LBL_PERMISSION_DENIED');
        }
	}

    function preProcessTplName(Vtiger_Request $request) {
		return 'UserEditViewPreProcess.tpl';
	}


    public function preProcess (Vtiger_Request $request, $display=true) {
        if($this->checkPermission($request)){
            $viewer = $this->getViewer($request);

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

            $viewer->assign('PAGETITLE', $this->getPageTitle($request));
            $viewer->assign('SCRIPTS',$this->getHeaderScripts($request));
            $viewer->assign('STYLES',$this->getHeaderCss($request));
            $viewer->assign('LANGUAGE_STRINGS', $this->getJSLanguageStrings($request));

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
		$moduleName = $request->getModule();
		$recordId = $request->get('record');

		if (!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
		}

		$viewer = $this->getViewer($request);
		$viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());

		parent::process($request);
	} 
}