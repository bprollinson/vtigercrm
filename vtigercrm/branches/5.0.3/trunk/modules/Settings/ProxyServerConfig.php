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

global $mod_strings;
global $app_strings;
global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
if($_REQUEST['error'] != '')
{
		$smarty->assign("ERROR_MSG",'<b><font color="purple">'.$_REQUEST["error"].'</font></b>');
}
$sql="select * from vtiger_systems where server_type = 'proxy'";
$result = $adb->query($sql);
$server = $adb->query_result($result,0,'server');
$server_port = $adb->query_result($result,0,'server_port');
$server_username = $adb->query_result($result,0,'server_username');
$server_password = $adb->query_result($result,0,'server_password');

if(isset($_REQUEST['proxy_server_mode']) && $_REQUEST['proxy_server_mode'] != '')
	$smarty->assign("PROXY_SERVER_MODE",$_REQUEST['proxy_server_mode']);
else
	$smarty->assign("PROXY_SERVER_MODE",'view');

if (isset($server))
	$smarty->assign("PROXYSERVER",$server);
if (isset($server_port))
        $smarty->assign("PROXYPORT",$server_port);
if (isset($server_username))
	$smarty->assign("PROXYUSER",$server_username);
if (isset($server_password))
	$smarty->assign("PROXYPASSWORD",$server_password);


$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/ProxyServer.tpl");
?>
