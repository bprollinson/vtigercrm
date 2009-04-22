<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('include/database/PearDatabase.php');
@include_once('user_privileges/default_module_view.php');

global $adb, $singlepane_view, $currentModule;
$idlist = $_REQUEST['idlist'];
$dest_mod = $_REQUEST['destination_module'];
$parenttab = $_REQUEST['parenttab'];

$forCRMRecord = $_REQUEST['parentid'];
$mode = $_REQUEST['mode'];

if($singlepane_view == 'true')
	$action = "DetailView";
else
	$action = "CallRelatedList";

$storearray = array();
if(!empty($_REQUEST['idlist'])) {
	// Split the string of ids
	$storearray = explode (";",trim($idlist,";"));
} else if(!empty($_REQUEST['entityid'])){
	$storearray = array($_REQUEST['entityid']);
}
foreach($storearray as $id)
{
	if($id != '')
	{
		if($dest_mod == 'Documents')
			$adb->pquery("insert into vtiger_senotesrel values (?,?)", array($forCRMRecord, $id));
		elseif($dest_mod =='Leads' || $dest_mod =='Accounts' ||$dest_mod =='Contacts' ||$dest_mod =='Potentials' || $dest_mod=='Products'){
			$query = $adb->pquery("SELECT * from vtiger_seproductsrel WHERE crmid=? and productid=?",array($forCRMRecord,$id));
			if($adb->num_rows($query)==0){
				$adb->pquery("insert into vtiger_seproductsrel values (?,?,?)", array($id, $forCRMRecord, $dest_mod));
			}
		}
		else {						
			checkFileAccess("modules/$currentModule/$currentModule.php");
			require_once("modules/$currentModule/$currentModule.php");
			$focus = new $currentModule();
			$focus->save_related_module($currentModule, $forCRMRecord, $dest_mod, $id);
		}
	}
}

header("Location: index.php?action=$action&module=$currentModule&record=".$forCRMRecord."&parenttab=".$parenttab);

?>