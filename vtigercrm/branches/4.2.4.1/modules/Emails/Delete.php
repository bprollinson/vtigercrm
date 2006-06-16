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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/Delete.php,v 1.6 2005/03/10 09:29:42 shaw Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('modules/Emails/Email.php');

require_once('include/logging.php');
$log = LoggerManager::getLogger('email_delete');

$focus = new Email();

if(!isset($_REQUEST['record']))
	die("A record number must be specified to delete the email.");

$sql='delete from seactivityrel where activityid="'.$_REQUEST['record'].'" and "'.$_REQUEST['record'].'"';
$adb->query($sql);

$sql_recentviewed ='delete from tracker where user_id = '.$current_user->id.' and item_id = '.$_REQUEST['record'];
$adb->query($sql_recentviewed);
if($_REQUEST['module'] == $_REQUEST['return_module'])
	$focus->mark_deleted($_REQUEST['record']);

header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id']);
?>
