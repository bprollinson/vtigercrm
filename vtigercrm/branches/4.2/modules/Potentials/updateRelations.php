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

//if($_REQUEST['module']=='Users')
//	$sql = "insert into salesmanactivityrel values (". $_REQUEST["entityid"] .",".$_REQUEST["parid"] .")";
//else
if($_REQUEST['module']=='Potentials')
	$sql = "insert into contpotentialrel values (". $_REQUEST["entityid"] .",".$_REQUEST["parid"] .")";
else
	$sql = "insert into seproductsrel values (". $_REQUEST["parid"] .",".$_REQUEST["entityid"] .")";
$adb->query($sql);
 header("Location: index.php?action=DetailView&module=Potentials&record=".$_REQUEST["parid"]);






?>
