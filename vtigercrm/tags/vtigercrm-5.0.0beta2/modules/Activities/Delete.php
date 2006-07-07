<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.mozilla.org/MPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Activities/Delete.php,v 1.11 2005/04/18 10:37:49 samk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('modules/Activities/Activity.php');

require_once('include/logging.php');
$log = LoggerManager::getLogger('task_delete');

$focus = new Activity();

if(!isset($_REQUEST['record']))
	die($mod_strings['ERR_DELETE_RECORD']);

DeleteEntity($_REQUEST['module'],$_REQUEST['return_module'],$focus,$_REQUEST['record'],$_REQUEST['return_id']);

 //code added for returning back to the current view after delete from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=$_REQUEST['return_viewname'];
if($_REQUEST['parenttab'] != '')$parenttab=$_REQUEST['parenttab'];
header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id']."&viewname=".$return_viewname."&parenttab=".$parenttab);
?>
