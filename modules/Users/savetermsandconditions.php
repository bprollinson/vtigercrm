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
require_once('include/utils/utils.php');

$inv_type='Inventory';
$inv_tandc=$_REQUEST['inventory_tandc'];

$sql="select * from vtiger_inventory_tandc where type='".$inv_type."'";
$result = $adb->query($sql);
$inv_id = $adb->query_result($result,0,'id');
if($inv_id == '')
{
        $inv_id=$adb->getUniqueID('inventory_tandc');
        $sql="insert into vtiger_inventory_tandc values( '".$inv_id ."','".$inv_type."','". $inv_tandc."')";
}
else
{
	$sql="update vtiger_inventory_tandc set type = '".$inv_type."', tandc = '".$inv_tandc."' where id = ".$inv_id;
}
$adb->query($sql);

header("Location: index.php?module=Users&action=OrganizationTermsandConditions&parenttab=Settings");
?>
