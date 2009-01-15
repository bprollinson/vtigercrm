<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
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
 * $Header: /advent/projects/wesat/vtiger_crm/vtigercrm/modules/Products/Delete.php,v 1.4 2005/03/22 13:53:55 mickie Exp $
 * Description:  Deletes an Account record and then redirects the browser to the 
 * defined return URL.
 ********************************************************************************/

require_once('modules/Products/Products.php');
global $mod_strings;

require_once('include/logging.php');
$log = LoggerManager::getLogger('product_delete');

$focus = new Products();

	//Added to fix 4600
	$url = getBasic_Advance_SearchURL();

if(!isset($_REQUEST['record']))
	die($mod_strings['ERR_DELETE_RECORD']);
if($_REQUEST['return_module']!="Products" || ($_REQUEST['return_module']=="Products" && !isset($_REQUEST['return_id'])))
	DeleteEntity($_REQUEST['module'],$_REQUEST['return_module'],$focus,$_REQUEST['record'],$_REQUEST['return_id']);
else
	$focus->deleteProduct2ProductRelation($_REQUEST['record'],$_REQUEST['return_id'],$_REQUEST['is_parent']);

if(isset($_REQUEST['activity_mode']))
	$activitymode = '&activity_mode='.$_REQUEST['activity_mode'];

if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] != "") $parenttab = $_REQUEST['parenttab'];

header("Location: index.php?module=".$_REQUEST['return_module']."&action=".$_REQUEST['return_action']."&record=".$_REQUEST['return_id'].$activitymode."&parenttab=".$parenttab."&relmodule=".$_REQUEST['module'].$url);
?>
