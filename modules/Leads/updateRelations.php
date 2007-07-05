<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('user_privileges/default_module_view.php');
global $adb, $singlepane_view;
$idlist = $_REQUEST['idlist'];
$dest_mod = $_REQUEST['destination_module'];

if($singlepane_view == 'true') $action = "DetailView";
else $action = "CallRelatedList";

if(isset($_REQUEST['idlist']) && $_REQUEST['idlist'] != '')
{
	//split the string and store in an array
	$storearray = explode (";",$idlist);
	foreach($storearray as $id)
	{
		if($id != '')
		{
			if($dest_mod == 'Products')
				$adb->query("insert into vtiger_seproductsrel values (".$_REQUEST["parentid"].",".$id.",'Leads')");
			elseif($dest_mod == 'Campaigns')	
		    		$adb->query("insert into  vtiger_campaignleadrel values(".$id.",".$_REQUEST["parentid"].")");
		}
	}
	$record = $_REQUEST["parentid"];
}
elseif(isset($_REQUEST['entityid']) && $_REQUEST['entityid'] != '')
{
	if($dest_mod == 'Products')
		$adb->query("insert into vtiger_seproductsrel values (".$_REQUEST["parid"].",".$_REQUEST["entityid"].",'Leads')");	
	elseif($dest_mod == 'Campaigns')
		$adb->query("insert into vtiger_campaignleadrel values(".$_REQUEST["entityid"].",".$_REQUEST["parid"].")");
	$record = $_REQUEST["parid"];
}

header("Location: index.php?action=$action&module=Leads&record=".$record);



?>
