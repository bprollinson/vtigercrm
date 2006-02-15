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

 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/install/4createConfigFile.php,v 1.26 2005/04/25 05:40:50 samk Exp $

 * Description:  Executes a step in the installation process.

 ********************************************************************************/



require_once('include/utils/utils.php');



session_start();



// vtiger CRM version number; do not edit!

$vtiger_version = "4.2 GA";
$release_date = "18 July 2005";


if (isset($_REQUEST['db_host_name'])) 	$db_host_name = 	$_REQUEST['db_host_name'];

if (isset($_REQUEST['db_user_name'])) 	$db_user_name = 	$_REQUEST['db_user_name'];

if (isset($_REQUEST['db_password'])) 	$db_password = 		$_REQUEST['db_password'];

if (isset($_REQUEST['db_name'])) 		$db_name  	= 		$_REQUEST['db_name'];

if (isset($_REQUEST['db_drop_tables'])) $db_drop_tables = 	$_REQUEST['db_drop_tables'];

if (isset($_REQUEST['db_create'])) 		$db_create = 		$_REQUEST['db_create'];

if (isset($_REQUEST['db_populate']))	$db_populate = 		$_REQUEST['db_populate'];

if (isset($_REQUEST['site_URL'])) 		$site_URL = 		$_REQUEST['site_URL'];
 
if (isset($_REQUEST['admin_email'])) 	$admin_email = 		$_REQUEST['admin_email'];

if (isset($_REQUEST['admin_password'])) $admin_password = 	$_REQUEST['admin_password'];

if (isset($_REQUEST['mail_server'])) $mail_server = 	$_REQUEST['mail_server'];

if (isset($_REQUEST['mail_server_username'])) $mail_server_username = 	$_REQUEST['mail_server_username'];

if (isset($_REQUEST['mail_server_password'])) $mail_server_password = 	$_REQUEST['mail_server_password'];

if (isset($_REQUEST['ftpserver'])) $ftpserver = 	$_REQUEST['ftpserver'];

if (isset($_REQUEST['ftpuser'])) $ftpuser = 	$_REQUEST['ftpuser'];

if (isset($_REQUEST['ftppassword'])) $ftppassword = 	$_REQUEST['ftppassword'];

$cache_dir = 'cache/';



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML>

<HEAD>

<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

<title>vtiger CRM 5.0 Beta Installer: Step 4</title>

<link rel="stylesheet" href="install/install.css" type="text/css" />

</head>

<body leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">

<!-- Master table -->
<table border=0 cellspacing=0 cellpadding=0 width=100%>
<tr>
	<td align=center>
	<br><br>
	<!--  Top Header -->
	<table border="0" cellspacing="0" cellpadding="0" width="80%" style="background:url(install/images/cwTopBg.gif) repeat-x;">
	<tr>
		<td><img src="install/images/cwTopLeft.gif" alt="vtiger CRM" title="vtiger CRM"></td>
		<td align=right><img src="install/images/cwTopRight.gif" alt="v5beta" title="v5beta"></td>
	</tr>
	</table>
	
	
	
	<!-- 4 of 5 header -->
	<table border="0" cellspacing="0" cellpadding="5" width="75%" class=small> 
	<tr>	
		<td valign=top><img src="install/images/cwIcoConfFile.gif" alt="System Check" title="System Check"></td>
		<td width=98% valign=top>
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
			<tr>
				<td><img src="install/images/cwHdrVtConfWiz.gif" alt="vtiger CRM Configuration Wizard" title="vtiger CRM Configuration Wizard"></td>
				<td align=right><img src="install/images/cwStep4of5.gif" alt="Step 4 of 5" title="Step 4 of 5"></td>
			</tr>
			<tr>
				<td colspan=2><img src="install/images/cwHdrCrConfFile.gif" alt="Create Configuration File" title="Create Configuration File"></td>
			</tr>
			</table>
			<hr noshade size=1>
		</td>

	</tr>
	<tr>
		<td></td>
		<td valign="top" align=center>
		<!--Create Configuration File-->
<?php
	
if (isset($_REQUEST['root_directory'])) 
{
	$root_directory = $_REQUEST['root_directory'];
}
if (is_file('config.php')) 
{
	$is_writable = is_writable('config.php');
}
else 
{
	$is_writable = is_writable('.');
}

$config = "<?php \n";
$config .= "/*********************************************************************************\n";
$config .= " * The contents of this file are subject to the SugarCRM Public License Version 1.1.2\n";
$config .= " * (\"License\"); You may not use this file except in compliance with the \n";
$config .= " * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL\n";
$config .= " * Software distributed under the License is distributed on an  \"AS IS\"  basis,\n";
$config .= " * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for\n";
$config .= " * the specific language governing rights and limitations under the License.\n";
$config .= " * The Original Code is:  SugarCRM Open Source\n";
$config .= " * The Initial Developer of the Original Code is SugarCRM, Inc.\n";
$config .= " * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;\n";
$config .= " * All Rights Reserved.\n";
$config .= " * Contributor(s): ______________________________________.\n";
$config .= "********************************************************************************/\n\n";
$config .= "\$vtiger_version = '".$vtiger_version."';\n\n";
$config .= "\$release_date = '".$release_date."';\n\n";
$config .= "//World clock & Calculator & FCKEditor will be displayed if these variables are 'true' otherwise will not be displayed.\n";
$config .= "\$WORLD_CLOCK_DISPLAY = 'true';\n";
$config .= "\$CALCULATOR_DISPLAY = 'true';\n";
$config .= "\$FCKEDITOR_DISPLAY = 'true';\n\n";

$config .= "//This is the URL for customer portal. (Ex. http://vtiger.com/portal)\n";
$config .= "\$PORTAL_URL = 'http://yourdomain.com/customerportal';\n\n";
$config .= "//These two are the HelpDesk support email id and the support name. ";
$config .= "(Ex. 'support@vtiger.com' and 'vtiger Support')\n";
$config .= "\$HELPDESK_SUPPORT_EMAIL_ID = 'support@yourdomain.com';\n";
$config .= "\$HELPDESK_SUPPORT_NAME = 'yourdomain Name';\n\n";

$config .= "/* Database configuration\n";
$config .= "      db_host_name:     MySQL Database Hostname\n";
$config .= "      db_user_name:    	MySQL Username\n";
$config .= "      db_password:     	MySQL Password\n";
$config .= "      db_name:     		MySQL Database Name\n*/\n";
$config .= "\$dbconfig['db_host_name'] = 	'".$db_host_name."';\n";
$config .= "\$dbconfig['db_user_name'] = 	'".$db_user_name."';\n";
$config .= "\$dbconfig['db_password'] = 		'".$db_password."';\n";
$config .= "\$dbconfig['db_name'] = 			'".$db_name."';\n";
$config .= "\$dbconfig['db_type'] = 'mysql';\n\n";
$config .= "\$dbconfig['log_sql'] = false;\n";
$config .= "\$dbconfigoption['persistent'] = true;\n";
$config .= "\$dbconfigoption['autofree'] = false;\n";
$config .= "\$dbconfigoption['debug'] = 0;\n";
$config .= "\$dbconfigoption['seqname_format'] = '%s_seq';\n";
$config .= "\$dbconfigoption['portability'] = 0;\n";
$config .= "\$dbconfigoption['ssl'] = false;\n\n";
$config .= "\$host_name = '".$db_host_name."';\n\n";
$config .= "\$site_URL = '".$site_URL."';\n\n";
$config .= "\$root_directory = '".$root_directory."';\n\n";
$config .= "\$cache_dir = '$cache_dir';\n";
$config .= "\$mail_server = '$mail_server';\n";
$config .= "\$mail_server_username = '$mail_server_username';\n";
$config .= "\$mail_server_password = '$mail_server_password';\n";
//$config .= "\$ftpserver = '$ftpserver';\n";
//$config .= "\$ftpuser = '$ftpuser';\n";
//$config .= "\$ftppassword = '$ftppassword';\n";
$config .= "\$tmp_dir = '" . ($cache_dir . "images/") . "';\n";
$config .= "\$import_dir = '" . ($cache_dir . "import/") . "';\n\n";
$config .= "// Maximum file size for uploaded files (in bytes)\n";
$config .= "// also used when uploading import files\n";
$config .= "\$upload_maxsize = ".return_session_value_or_default('upload_maxsize', '3000000').";\n";
$config .= "// Flag to allow export functionality\n";
$config .= "// use 'all' to allow anyone to use exports \n";
$config .= "// use 'admin' to only allow admins to export \n";
$config .= "// use 'none' to block exports completely \n";
$config .= "\$allow_exports = '".return_session_value_or_default('allow_exports', 'all')."';\n";
$config .= "\$upload_dir = '" . ($cache_dir . "upload/") . "';\n";
$config .= "// Files with one of these extensions will have '.txt' appended to their filename on upload\n";
$config .= "\$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm');\n\n";
$config .= "// This is the full path to the include directory including the trailing slash\n";
$config .= "\$includeDirectory = \$root_directory.'include/';\n\n";
$config .= "\$list_max_entries_per_page = '20';\n\n";
$config .= "\$history_max_viewed = '5';\n\n";

$config .= "// change this number to whatever you want. This is the number of pages that will appear in the pagination. by Raju \n";
$config .= "\$limitpage_navigation = '9';\n\n";

$config .= "// Map Sugar language codes to jscalendar language codes\n";
$config .= "// Unimplemented until jscalendar language files are fixed\n";
$config .= "// \$cal_codes = array('en_us'=>'en', 'ja'=>'jp', 'sp_ve'=>'sp', 'it_it'=>'it', 'tw_zh'=>'zh', 'pt_br'=>'pt', 'se'=>'sv', 'cn_zh'=>'zh', 'ge_ge'=>'de', 'ge_ch'=>'de', 'fr'=>'fr');\n\n";
$config .= "\$default_module = 'Home';\n";
$config .= "\$default_action = 'index';\n\n";
$config .= "//set default theme\n";
$config .= "\$default_theme = 'blue';\n\n";
$config .= "// If true, the time to compose each page is placed in the browser.\n";
$config .= "\$calculate_response_time = true;\n";
$config .= "// Default Username - The default text that is placed initially in the login form for user name.\n";
$config .= "\$default_user_name = '';\n";
$config .= "// Default Password - The default text that is placed initially in the login form for password.\n";
$config .= "\$default_password = '';\n";
$config .= "// Create default user - If true, a user with the default username and password is created.\n";
$config .= "\$create_default_user = false;\n";
$config .= "\$default_user_is_admin = false;\n";
$config .= "// disable persistent connections - If your MySQL/PHP configuration does not support persistent connections set this to true to avoid a large performance slowdown\n";
$config .= "\$disable_persistent_connections = ".return_session_value_or_default('disable_persistent_connections', 'false').";\n";
$config .= "// Defined languages available.  The key must be the language file prefix.  E.g. 'en_us' is the prefix for every 'en_us.lang.php' file. \n";

$language_value = "Array('en_us'=>'US English',)";
if(isset($_SESSION['language_keys']) && isset($_SESSION['language_values']))
{
	$language_value = 'Array(';
	$language_keys = explode(',', urldecode($_SESSION['language_keys']));
	$language_values = explode(',', urldecode($_SESSION['language_values']));
	$size = count($language_keys);
	for($i = 0; $i < $size; $i+=1)
	{
		$language_value .= "'$language_keys[$i]'=>'$language_values[$i]',";
	}
	$language_value .= ')';
}
$config .= "\$languages = $language_value;\n";
$config .= "// Default charset if the language specific character set is not found.\n";
$config .= "\$default_charset = '".return_session_value_or_default('default_charset','ISO-8859-1')."';\n";
$config .= "// Default language in case all or part of the user's language pack is not available.\n";
$config .= "\$default_language = '".return_session_value_or_default('default_language','en_us')."';\n";
$config .= "// Translation String Prefix - This will add the language pack name to every translation string in the display.\n";
$config .= "\$translation_string_prefix = ".return_session_value_or_default('translation_string_prefix','false').";\n";

$config .= "// Generating Unique Application Key\n";
$config .= "\$application_unique_key = '".md5($root_directory)."';\n\n";

$config .= "//Option to cache tabs permissions for speed.\n";
$config .= "\$cache_tab_perms = true;\n\n";

$config .= "//Option to hide empty home blocks if no entries.\n";
$config .= "\$display_empty_home_blocks = false;\n\n";
//$config .= "// Forum Configurations\n";
//$config .= "\$dbms = 'mysql';\n";
//$config .= "\$dbhost = '".$db_host_name."';\n\n";
//$config .= "\$dbuser = '".$db_user_name."';\n\n";
//$config .= "\$dbpasswd = '".$db_password."';\n\n";
//$config .= "\$dbname= '".$db_name."';\n\n";
//$config .= "\$table_prefix = 'phpbb_';\n";
//$config .= "define('PHPBB_INSTALLED', true);\n";
$config .= "?>";
			
if ($is_writable && ($config_file = @ fopen("config.php", "w"))) 
{
	fputs($config_file, $config, strlen($config));
	fclose($config_file);
	echo "<br><table class=\"small\" width=\"100%\" cellspacing=\"0\" cellpadding=\"15\" border=\"0\" style=\"border:1px dashed #cccccc\" align=\"center\"><tbody><tr><td align=\"left\">";
	echo "<b>Successfully created configuration file (config.php) in :";
	echo "<font color=\"00CC00\">".$root_directory."</font>\n";
	echo "</td></tr></table>";
}
else 
{
	echo "<br><table class=\"small\" width=\"100%\" cellspacing=\"0\" cellpadding=\"15\" border=\"0\" style=\"border:2px dashed #cc0000\" align=\"center\"><tbody><tr><td align=\"left\">";
	echo "<b>Warning! Cannot write to config.php file in the directory</b> <font color=red>".$root_directory."</font> \n";
	echo "<P>You can continue this installation by manually creating the config.php file and pasting the configuration information below into the config.php file.  However, you <strong>must </strong>create the config.php file before you continue to the next step.  <P>\n";
	echo  "<TEXTAREA class=\"dataInput\" rows=\"15\" cols=\"80\">".$config."</TEXTAREA>";
	echo "<P>Did you remember to create the config.php file?</td></tr>";
}
?>


	<tr>
		<td colspan=2 >
		<br><br>
		<table border=0 cellspacing=0 cellpadding=0 width=100% class=small>
			<tr>
				<td><img src="install/images/cwURL.gif"></td>
				<td align=right>
				 <form action="install.php" method="post" name="form" id="form">
				 <!--<form action="install.php" method="post" name="form" id="form"> -->
				 <input type="hidden" name="file" value="5createTables.php">
				 <input type="hidden" class="dataInput" name="db_host_name" value="<?php if (isset($db_host_name)) echo "$db_host_name"; ?>" />
				 <input type="hidden" class="dataInput" name="db_user_name" value="<?php if (isset($db_user_name)) echo "$db_user_name"; ?>" />
				 <input type="hidden" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" />
				 <input type="hidden" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />
				 <input type="hidden" class="dataInput" name="db_drop_tables" value="<?php if (isset($db_drop_tables)) echo "$db_drop_tables"; ?>" />
				 <input type="hidden" class="dataInput" name="db_create" value="<?php if (isset($db_create)) echo "$db_create"; ?>" />
				 <input type="hidden" class="dataInput" name="db_populate" value="<?php if (isset($db_populate)) echo "$db_populate"; ?>" />
				 <input type="hidden" class="dataInput" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>" />
				 <input type="hidden" class="dataInput" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; ?>" />
				 <input  type="image" name="next" value="Next" src="install/images/cwBtnNext.gif" onClick="window.location=('install.php')" />
				 </form>
					
				</td>
			</tr>
		</table>
		<br><br>
		<!-- Horizontal Shade -->
		<table border="0" cellspacing="0" cellpadding="0" width="100%" style="background:url(install/images/cwShadeBg.gif) repeat-x;">
			<tr>
				<td><img src="install/images/cwShadeLeft.gif"></td>
				<td align=right><img src="install/images/cwShadeRight.gif"></td>
			</tr>
		</table>
				

	
	</td>
	</tr>

 </tbody>
</table>

<!-- Create Configuration File -->
</td>
</tr>
</table>
		
<br><br>
	
			








</td>
</tr>
</table>
<!-- Master table closes -->

</body>

</html>

