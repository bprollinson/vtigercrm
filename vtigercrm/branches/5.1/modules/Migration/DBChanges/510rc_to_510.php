<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

//5.1.0 RC to 5.1.0 database changes

//we have to use the current object (stored in PatchApply.php) to execute the queries
$adb = $_SESSION['adodb_current_object'];
$conn = $_SESSION['adodb_current_object'];

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Starts \n\n");

ExecuteQuery("DELETE vtiger_cvcolumnlist FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview WHERE vtiger_cvcolumnlist.columnname LIKE '%vtiger_notes:filename%' AND vtiger_customview.cvid = vtiger_cvcolumnlist.cvid AND vtiger_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE vtiger_cvcolumnlist FROM vtiger_cvcolumnlist INNER JOIN vtiger_customview WHERE (vtiger_cvcolumnlist.columnname LIKE '%parent_id%' OR vtiger_cvcolumnlist.columnname LIKE '%vtiger_contactdetails%') AND vtiger_customview.cvid = vtiger_cvcolumnlist.cvid AND vtiger_customview.entitytype='Documents'");

ExecuteQuery("DELETE vtiger_cvadvfilter FROM vtiger_cvadvfilter INNER JOIN vtiger_customview WHERE vtiger_cvadvfilter.columnname LIKE '%vtiger_notes:filename%' AND vtiger_customview.cvid = vtiger_cvadvfilter.cvid AND vtiger_customview.entitytype='HelpDesk'");
ExecuteQuery("DELETE vtiger_cvadvfilter FROM vtiger_cvadvfilter INNER JOIN vtiger_customview WHERE (vtiger_cvadvfilter.columnname LIKE '%parent_id%' OR vtiger_cvadvfilter.columnname LIKE '%vtiger_contactdetails%') AND vtiger_customview.cvid = vtiger_cvadvfilter.cvid AND vtiger_customview.entitytype='Documents'");

$migrationlog->debug("\n\nDB Changes from 5.1.0 RC to 5.1.0 -------- Ends \n\n");

?>