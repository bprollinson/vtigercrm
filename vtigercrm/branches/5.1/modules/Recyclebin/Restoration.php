<?php
/*********************************************************************************
 *** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 ** ("License"); You may not use this file except in compliance with the License
 ** The Original Code is:  vtiger CRM Open Source
 ** The Initial Developer of the Original Code is vtiger.
 ** Portions created by vtiger are Copyright (C) vtiger.
 ** All Rights Reserved.
 **
 *********************************************************************************/

if(isset($_REQUEST['idlist']) && $_REQUEST['idlist'] != '')
{
	$idlists = explode(',',$_REQUEST[idlist]);
}elseif(isset($_REQUEST['entityid']) && $_REQUEST['entityid'] != '')
{
	$idlists = Array($_REQUEST['entityid']);
}

$selected_module = $_REQUEST['selectmodule'];

require_once("modules/$selected_module/$selected_module.php");

if($selected_module != 'Calendar')
{
	$mod_name = $selected_module;
}else
{
	$mod_name = 'Activity';
}
$focus = new $mod_name();
$focus->restore($mod_name, $idlists);

if(isset($_REQUEST['parenttab']) && $_REQUEST['parenttab'] != '')
	$parenttab = $_REQUEST['parenttab'];
else 
	$parenttab = 'Tools';
	

header("Location: index.php?module=Recyclebin&action=RecyclebinAjax&file=index&parenttab=$parenttab&mode=ajax&selected_module=$selected_module");
?>

