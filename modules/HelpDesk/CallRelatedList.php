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


require_once('Smarty_setup.php');
require_once('modules/HelpDesk/HelpDesk.php');
$focus = new HelpDesk();
$currentmodule = $_REQUEST['module'];
$RECORD = $_REQUEST['record'];
if(isset($_REQUEST['record']) && $_REQUEST['record']!='') {
    $focus->retrieve_entity_info($_REQUEST['record'],"HelpDesk");
    $focus->id = $_REQUEST['record'];
    $focus->name=$focus->column_fields['groupname'];

$log->debug("id is ".$focus->id);

$log->debug("name is ".$focus->name);

}

$smarty = new vtigerCRM_Smarty;

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
        $focus->id = "";
}

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
$related_array=getRelatedLists("HelpDesk",$focus);
$smarty->assign("RELATEDLISTS", $related_array);
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$smarty->assign("id",$focus->id);
$smarty->assign("ID",$RECORD );
$smarty->assign("MODULE",$currentmodule);
$smarty->assign("SINGLE_MOD",$currentmodule);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));
$smarty->display("RelatedLists.tpl");
?>
