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
$return_action = $_REQUEST['return_action'];
$return_module = $_REQUEST['return_module'];
$return_id = $_REQUEST['return_id'];
if(isset($_REQUEST['id']))	$productid = $_REQUEST['id'];
else                            $productid = $_REQUEST['record'];
$query="update products set deleted='1' where id=".$productid;
$adb->query($query); 

$loc = "Location: index.php?action=".$return_action."&module=".$return_module."&record=".$return_id;
header($loc);
?>
