<?php
	function vtws_extendSession(){
		global $adb,$API_VERSION,$application_unique_key;
		if(isset($_SESSION["authenticated_user_id"]) && $_SESSION["app_unique_key"] == $application_unique_key){
			$userId = $_SESSION["authenticated_user_id"];
			$sessionManager = new SessionManager();
			$sessionManager->set("authenticatedUserId", $userId);
			$crmObject = VtigerWebserviceObject::fromName($adb,"Users");
			$userId = vtws_getId($crmObject->getEntityId(),$userId);
			$vtigerVersion = vtws_getVtigerVersion();
			$resp = array("sessionName"=>$sessionManager->getSessionId(),"userId"=>$userId,"version"=>$API_VERSION,"vtigerVersion"=>$vtigerVersion);
			return $resp;
		}else{
			throw new WebServiceException(WebServiceErrorCode::$AUTHFAILURE,"Authencation Failed");
		}
	}
?>