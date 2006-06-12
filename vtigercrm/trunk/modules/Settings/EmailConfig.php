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
global $app_list_strings;

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mail_error'] != '')
{
        require_once("modules/Emails/mail.php");
        $error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
		$smarty->assign("ERROR_MSG",'<b><font color="purple">Test Mail status : '.$error_msg.'</font></b>');
}

global $adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$sql="select * from vtiger_systems where server_type = 'email'";
$result = $adb->query($sql);
$mail_server = $adb->query_result($result,0,'server');
$mail_server_username = $adb->query_result($result,0,'server_username');
$mail_server_password = $adb->query_result($result,0,'server_password');
$smtp_auth = $adb->query_result($result,0,'smtp_auth');

if (isset($mail_server))
	$smarty->assign("MAILSERVER",$mail_server);
if (isset($mail_server_username))
	$smarty->assign("USERNAME",$mail_server_username);
if (isset($mail_server_password))
	$smarty->assign("PASSWORD",$mail_server_password);
if (isset($smtp_auth))
{
	if($smtp_auth == 'true')
		$smarty->assign("SMTP_AUTH",'checked');
	else
		$smarty->assign("SMTP_AUTH",'');
}

if(isset($_REQUEST['emailconfig_mode']) && $_REQUEST['emailconfig_mode'] != '')
	$smarty->assign("EMAILCONFIG_MODE",$_REQUEST['emailconfig_mode']);
else
	$smarty->assign("EMAILCONFIG_MODE",'view');

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("CMOD", $mod_strings);
$smarty->display("Settings/EmailConfig.tpl");
?>
