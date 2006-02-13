<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/install/2setConfig.php,v 1.41 2005/04/29 06:44:13 samk Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/
//require_once("connection.php");

$web_root = $_SERVER['SERVER_NAME']. ":" .$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
$web_root = str_replace("/install.php", "", $web_root);
$web_root = "http://$web_root";
$current_dir = pathinfo(dirname(__FILE__));
$current_dir=$current_dir['dirname']."/";
$cache_dir = "cache/";

// To make MySQL run in desired port
$sock_path=":" .$mysql_port;

$H_NAME=gethostbyaddr($_SERVER['SERVER_ADDR']);
if (is_file("config.php")) {
	require_once("config.php");

	session_start();
	if(isset($upload_maxsize))
                 $_SESSION['upload_maxsize'] = $upload_maxsize;
         if(isset($allow_exports))
                 $_SESSION['allow_exports'] = $allow_exports;
	if(isset($disable_persistent_connections))
		$_SESSION['disable_persistent_connections'] = $disable_persistent_connections;
	if(isset($default_language))
		$_SESSION['default_language'] = $default_language;
	if(isset($translation_string_prefix))
		$_SESSION['translation_string_prefix'] = $translation_string_prefix;
	if(isset($default_charset))
		$_SESSION['default_charset'] = $default_charset;
	if(isset($languages))
	{
		// We need to encode the languages in a way that can be retrieved later.
		$language_keys = Array();
		$language_values = Array();

		foreach($languages as $key=>$value)
		{
			$language_keys[] = $key;
			$language_values[] = $value;
		}

		$_SESSION['language_keys'] = urlencode(implode(",",$language_keys));
		$_SESSION['language_values'] = urlencode(implode(",",$language_values));
	}

	global $dbconfig;
	if (isset($_REQUEST['db_host_name'])) {
		$db_host_name = $_REQUEST['db_host_name'];
	}
	elseif (isset($dbconfig['db_host_name'])) {
		$db_host_name = $dbconfig['db_host_name'];
	}
	else {
		$db_host_name = $H_NAME.$sock_path;
	}

	if (isset($_REQUEST['db_user_name'])) {
		$db_user_name = $_REQUEST['db_user_name'];
	}
	elseif (isset($dbconfig['db_user_name'])) {
		$db_user_name = $dbconfig['db_user_name'];
	}
	else {
		$db_user_name = $mysql_username;
	}

	if (isset($_REQUEST['db_password'])) {
		$db_password = $_REQUEST['db_password'];
	}
	elseif (isset($dbconfig['db_password'])) {
		$db_password = $dbconfig['db_password'];
	}
	else {
		$db_password = $mysql_password;
	}

	if (isset($_REQUEST['db_name'])){
		$db_name = $_REQUEST['db_name'];
	}
	elseif (isset($dbconfig['db_name'])) {
		$db_name = $dbconfig['db_name'];
	}
	else {
		$db_name = 'vtigercrm5_beta';
	}
	!isset($_REQUEST['db_drop_tables']) ? $db_drop_tables = "0" : $db_drop_tables = $_REQUEST['db_drop_tables'];

	if (isset($_REQUEST['host_name'])) $host_name = $_REQUEST['host_name'];
	else $host_name = $_SERVER['SERVER_NAME'];
	if (isset($_REQUEST['site_URL'])) $site_URL = $_REQUEST['site_URL'];
	else $site_URL = $web_root;
	if (isset($_REQUEST['root_directory'])) $root_directory = stripslashes($_REQUEST['root_directory']);
	else $root_directory = $current_dir;
	if (isset($_REQUEST['cache_dir'])) $cache_dir= $_REQUEST['cache_dir'];
	if (isset($_REQUEST['mail_server'])) $mail_server= $_REQUEST['mail_server'];
	if (isset($_REQUEST['mail_server_username'])) $mail_server_username= $_REQUEST['mail_server_username'];
	if (isset($_REQUEST['mail_server_password'])) $mail_server_password= $_REQUEST['mail_server_password'];
	if (isset($_REQUEST['admin_email'])) $admin_email = $_REQUEST['admin_email'];
	if (isset($_REQUEST['admin_password'])) $admin_password = $_REQUEST['admin_password'];
}
else {
	!isset($_REQUEST['db_host_name']) ? $db_host_name = $H_NAME.$sock_path : $db_host_name = $_REQUEST['db_host_name'];
	!isset($_REQUEST['db_user_name']) ? $db_user_name = $mysql_username : $db_user_name = $_REQUEST['db_user_name'];
	!isset($_REQUEST['db_password']) ? $db_password= $mysql_password : $db_password = $_REQUEST['db_password'];
	!isset($_REQUEST['db_name']) ? $db_name = "vtigercrm4_5" : $db_name = $_REQUEST['db_name'];
	!isset($_REQUEST['db_drop_tables']) ? $db_drop_tables = "0" : $db_drop_tables = $_REQUEST['db_drop_tables'];
	!isset($_REQUEST['host_name']) ? $host_name= $_SERVER['SERVER_NAME'] : $host_name= $_REQUEST['host_name'];
	!isset($_REQUEST['site_URL']) ? $site_URL = $web_root : $site_URL = $_REQUEST['site_URL'];
	!isset($_REQUEST['root_directory']) ? $root_directory = $current_dir : $root_directory = stripslashes($_REQUEST['root_directory']);
	!isset($_REQUEST['cache_dir']) ? $cache_dir = $cache_dir : $cache_dir = stripslashes($_REQUEST['cache_dir']);

	!isset($_REQUEST['mail_server']) ? $mail_server = $mail_server : $mail_server = stripslashes($_REQUEST['mail_server']);
	!isset($_REQUEST['mail_server_username']) ? $mail_server_username = $mail_server_username : $mail_server_username = stripslashes($_REQUEST['mail_server_username']);
	!isset($_REQUEST['mail_server_password']) ? $mail_server_password = $mail_server_password : $mail_server_password = stripslashes($_REQUEST['mail_server_password']);
	!isset($_REQUEST['admin_email']) ? $admin_email = "" : $admin_email = $_REQUEST['admin_email'];
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>vtiger CRM 5.0 Beta Installer: Step 2</title>
<link rel="stylesheet" href="install/install.css" type="text/css" />

</head>
<body leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">

<script type="text/javascript" language="Javascript">
<!--  to hide script contents from old browsers
function trim(s) {
        while (s.substring(0,1) == " ") {
                s = s.substring(1, s.length);
        }
        while (s.substring(s.length-1, s.length) == ' ') {
                s = s.substring(0,s.length-1);
        }

        return s;
}

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	// Here we decide whether to submit the form.
	if (trim(form.db_host_name.value) =='') {
		isError = true;
		errorMessage += "\n database host name";
		form.db_host_name.focus();
	}
	if (trim(form.db_user_name.value) =='') {
		isError = true;
		errorMessage += "\n database user name";
		form.db_user_name.focus();
	}
	if (trim(form.db_name.value) =='') {
		isError = true;
		errorMessage += "\n database name";
		form.db_name.focus();
	}
	if (trim(form.site_URL.value) =='') {
		isError = true;
		errorMessage += "\n site url";
		form.site_URL.focus();
	}
	if (trim(form.root_directory.value) =='') {
		isError = true;
		errorMessage += "\n path";
		form.root_directory.focus();
	}
/*
	 if (trim(form.admin_email.value) =='') {
                isError = true;
                errorMessage += "\n admin email";
                form.admin_email.focus();
        }
*/
	if (trim(form.admin_password.value) =='') {
		isError = true;
		errorMessage += "\n admin password";
		form.admin_password.focus();
	}
	if (trim(form.cache_dir.value) =='') {
                isError = true;
                errorMessage += "\n temp directory path";
                form.root_directory.focus();
        }
/*
	if (trim(form.mail_server.value) =='') {
                isError = true;
                errorMessage += "\n mail server name";
                form.mail_server.focus();
        }

 if (trim(form.mail_server_username.value) =='') {
                isError = true;
                errorMessage += "\n mail server username";
                form.mail_server_username.focus();
        }


 if (trim(form.mail_server_password.value) =='') {
                isError = true;
                errorMessage += "\n mail server password";
                form.mail_server_password.focus();
        }

*/




	// Here we decide whether to submit the form.
	if (isError == true) {
		alert("Missing required fields: " + errorMessage);
		return false;
	}

	return true;
}
// end hiding contents from old browsers  -->
</script>


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
	
	
	
	<!-- 1 of 5 header -->
	<table border="0" cellspacing="0" cellpadding="5" width="75%" class=small> 
	<tr>	
		<td valign=top><img src="install/images/cwIcoSystem.gif" alt="System Check" title="System Check"></td>
		<td width=98% valign=top>
			<table border=0 cellspacing=0 cellpadding=0 width=100%>
			<tr>
				<td><img src="install/images/cwHdrVtConfWiz.gif" alt="vtiger CRM Configuration Wizard" title="vtiger CRM Configuration Wizard"></td>
				<td align=right><img src="install/images/cwStep2of5.gif" alt="Step 2 of 5" title="Step 2 of 5"></td>
			</tr>
			<tr>
				<td colspan=2><img src="install/images/cwHdrSysConf.gif" alt="System Check" title="System Check"></td>
			</tr>
			</table>
			<hr noshade size=1>
		</td>

	</tr>
	<tr>
		<td></td>
		<td valign="top" align=center>
		<!-- System Configuration-->

			<form action="install.php" method="post" name="form" id="form" name="setConfig" id="form">

			<table width="100%" cellpadding="5"  cellspacing="1" border="0" class=small><tbody>
			<tr>
				<td >
		          <b>Please enter your database configuration information below...</b><br>

					  If you do not have root access to your database (for example you are installing in a virtual
					  hosting environment), you will need to have your database created for you before you proceed.
					  However, this installer will still be able to create the necessary database tables. <br><br>
			
					  If you unsure of your database host, username or password, we suggest that you use the default
					  values below. <br><br>
					  
					  *- required information
					  

				</td>
			</tr>
			</table>
			
			<br>
			
			<table width="90%" cellpadding="5"  cellspacing="1" border="0" class=small style="background-color:#cccccc"><tbody>
			<tr><td colspan=2><strong>Database Configuration</strong></td></tr>
			<tr>
               <td width="25%" nowrap bgcolor="#F5F5F5" ><strong>Host Name</strong> <sup><font color=red>*</font></sup></td>
               <td width="75%" bgcolor="white" align="left"><input type="text" class="dataInput" name="db_host_name" value="<?php if (isset($db_host_name)) echo "$db_host_name"; ?>" /></td>
              </tr>
              <tr>
               <td nowrap bgcolor="#F5F5F5"><strong>User Name</strong> <sup><font color=red>*</font></sup></td>
               <td bgcolor="white" align="left"><input type="text" class="dataInput" name="db_user_name" value="<?php if (isset($db_user_name)) echo "$db_user_name"; ?>" /></td>
              </tr>
              <tr>
               <td nowrap bgcolor="#F5F5F5"><strong>Password</strong> <sup><font color=red>*</font></sup></td>
               <td bgcolor="white" align="left"><input type="password" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" /></td>
              </tr>
              <tr>
               <td nowrap bgcolor="#F5F5F5"><strong>Database Name</strong> <sup><font color=red>*</font></sup></td>
               <td bgcolor="white" align="left"><input type="text" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" /></td>

			<input type="hidden" name="dbtype" value="<?php
                if(isset($dbconfig['db_type']) && $dbconfig['db_type'] != '')
                {
                        echo $dbconfig['db_type'];
                }
                elseif(isset($databasetype) && $databasetype != '')
                {
                        echo $databasetype;

                } ?>">
              </tr>
              </table>
			
			<br><br>
			
		  <!-- Web site configuration -->
		<table width="90%" cellpadding="5" border="0" style="background-color:#cccccc" cellspacing="1" class="small"><tbody>
            <tr>
				<td ><strong>Site Configuration</strong></td>
            </tr>
			<tr>
				<td width="25%" bgcolor="#F5F5F5" ><strong>URL</strong> <sup><font color=red>*</font></sup></td>
				<td width="75%" bgcolor=white align="left"><input class="dataInput" type="text" name="site_URL"
				value="<?php if (isset($site_URL)) echo $site_URL; ?>" size="40" />
				</td>
			</tr>
			<tr>
				<td bgcolor="#F5F5F5"><strong>Path</strong> <sup><font color=red>*</font></sup></td>
				<td align="left" bgcolor="white"><input class="dataInput" type="text" name="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="40" /> </td>
			</tr>
			<tr valign="top">
				<td bgcolor="#F5F5F5"><strong>Path to Cache Directory  <sup><font color=red>*</font></sup><br>(must be writable)</td>
				<td align="left" bgcolor="white"><?php echo $root_directory; ?><input class="dataInput" type="text" name="cache_dir" size='14' value="<?php if (isset($cache_dir)) echo $cache_dir; ?>" size="40" /> </td>
          </tr>
          </table>
			<br><br>
			
			<!-- Admin Configuration -->
            <table width="90%" cellpadding="5" border="0" class="small" cellspacing="1" style="background-color:#cccccc">
			<tr>
				<td colspan=2><strong>Admin Configuration</strong></td>
            </tr>
			<tr>
				<td nowrap width=25% bgcolor="#F5F5F5" ><strong>User name</strong></td>
				<td width=75% bgcolor="white" align="left">admin</td>
			</tr>
			<tr>
				<td bgcolor="#F5F5F5" nowrap><strong>Password</strong><sup><font color=red>*</font></sup></td>
				<td bgcolor="white" align="left"><input class="dataInput" type="password" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; else echo "admin"; ?>"></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="white"><font color=blue> <b>Note:</b> The default password is 'admin'. You can change the password if necessary now or else you can change it later in vtiger CRM </font></td>
			</tr>
			</table>

		<!-- System Configuration -->
			
		</td>
	</tr>
	</table>
	
	<br><br>

	
<!-- -->
	<table border=0 cellspacing=0 cellpadding=0 width=70% class=small>
	<tr>
		<td><img src="install/images/cwURL.gif"></td>
		<td align=right>
			<input type="hidden" name="file" value="3confirmConfig.php" />

			<input type="hidden" name="dbtype" value="<?php 
									if(isset($dbconfig['db_type']) && $dbconfig['db_type'] != '')
									{
										echo $dbconfig['db_type'];
									}
									elseif(isset($databasetype) && $databasetype != '')
									{
										echo $databasetype;
									} ?>"> 

			<input type="image" src="install/images/cwBtnNext.gif" border="0" onclick="return verify_data(this.form); window.location=('install.php')">
		</td>
	</tr>
	</table>
	<br><br>
	<!-- Horizontal Shade -->
	<table border="0" cellspacing="0" cellpadding="0" width="80%" style="background:url(install/images/cwShadeBg.gif) repeat-x;">
	<tr>
		<td><img src="install/images/cwShadeLeft.gif"></td>
		<td align=right><img src="install/images/cwShadeRight.gif"></td>
	</tr>
	</table>


</td>
</tr>
</table>
<!-- Master table closes -->

<br><br><br>

</form>
</body>
</html>
