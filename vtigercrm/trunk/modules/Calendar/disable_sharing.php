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
$idlist = $_POST['idlist'];
$returnmodule=$_REQUEST['return_module'];
$returnaction=$_REQUEST['return_action'];
//split the string and store in an array
$storearray = explode(";",$idlist);
foreach($storearray as $id)
{
        $sql="delete from vtiger_sharedcalendar where sharedid='" .$id ."'";
        $result = $adb->query($sql);
}
header("Location:index.php?module=".$returnmodule."&action=".$returnaction);
?>
