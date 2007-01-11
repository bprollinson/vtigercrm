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
global $adb;
$dest_mod = $_REQUEST['destination_module'];

//if select Lead, Account, Contact, Potential from Product RelatedList we have to insert in vtiger_seproductsrel
if($dest_mod =='Leads' || $dest_mod =='Accounts' ||$dest_mod =='Contacts' ||$dest_mod =='Potentials')
{
	//For Bulk updates
	if($_REQUEST['idlist'] != '')
	{
		$entityids = explode(';',trim($_REQUEST['idlist'],';'));
		$productid = $_REQUEST['parentid'];
	}
	else
	{
		$entityids[] = $_REQUEST['entityid'];
		$productid = $_REQUEST['parid'];
	}
	
	foreach($entityids as $ind => $crmid)
	{
		if($crmid != '' && $productid != '')
		{
			$sql = "insert into vtiger_seproductsrel values ($crmid,$productid)";
			$adb->query($sql);
		}
	}
}

$return_action = 'DetailView';
$return_module = 'Vendors';
if($_REQUEST['return_action'] != '') $return_action = $_REQUEST['return_action'];
if($_REQUEST['return_module'] != '') $return_module = $_REQUEST['return_module'];

header("Location:index.php?action=$return_action&module=$return_module&record=".$_REQUEST["parid"]);






?>
