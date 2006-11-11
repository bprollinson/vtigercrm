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

if (is_file('../install_lock')) {
    header("Location: ../index.php");
    exit();
}

// TODO: deprecate connection.php file
//require_once("connection.php");

// TODO: introduce MySQL port as parameters to use non-default value 3306
//$sock_path=":" .$mysql_port;
$hostname = $_SERVER['SERVER_NAME'];

// TODO: introduce Apache port as parameters to use non-default value 80
//$web_root = $_SERVER['SERVER_NAME']. ":" .$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
$web_root = $hostname.$_SERVER['PHP_SELF'];
$web_root = str_replace("/install.php", "", $web_root);
$web_root = "http://".$web_root;

$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";
$cache_dir = "cache/";

if (is_file("config.php") && is_file("config.inc.php")) {
  require_once("config.php");
  session_start();

  if(isset($upload_maxsize))
    $_SESSION['upload_maxsize'] = $upload_maxsize;

  if(isset($allow_exports))
    $_SESSION['allow_exports'] = $allow_exports;

  if(isset($default_language))
    $_SESSION['default_language'] = $default_language;

  if(isset($translation_string_prefix))
    $_SESSION['translation_string_prefix'] = $translation_string_prefix;

  if(isset($default_charset))
    $_SESSION['default_charset'] = $default_charset;

  if(isset($languages)) {
    // need to encode the languages in a way that can be retrieved later
    $language_keys = Array();
    $language_values = Array();

    foreach($languages as $key=>$value) {
      $language_keys[] = $key;
      $language_values[] = $value;
    }

    $_SESSION['language_keys'] = urlencode(implode(",",$language_keys));
    $_SESSION['language_values'] = urlencode(implode(",",$language_values));
  }

  global $dbconfig;

  if (isset($_REQUEST['db_hostname']))
    $db_hostname = $_REQUEST['db_hostname'];
  elseif (isset($dbconfig['db_hostname']))
    $db_hostname = $dbconfig['db_hostname'];
  else
    $db_hostname = $hostname;
// TODO: introduce MySQL port as parameters to use non-default value 3306
//  else
//    $db_hostname = $hostname.$sock_path;

  if (isset($_REQUEST['db_username']))
    $db_username = $_REQUEST['db_username'];
  elseif (isset($dbconfig['db_username']))
    $db_username = $dbconfig['db_username'];
// TODO: deprecate connection.php file parameters
//  else
//    $db_username = $mysql_username;

  if (isset($_REQUEST['db_password']))
    $db_password = $_REQUEST['db_password'];
  elseif (isset($dbconfig['db_password']))
    $db_password = $dbconfig['db_password'];
// TODO: deprecate connection.php file parameters
//  else
//    $db_password = $mysql_password;

  if (isset($_REQUEST['db_name']))
    $db_name = $_REQUEST['db_name'];
  elseif (isset($dbconfig['db_name']) && $dbconfig['db_name']!='_DBC_NAME_')
    $db_name = $dbconfig['db_name'];
  else
    $db_name = 'vtigercrm4_2_4';

  !isset($_REQUEST['db_drop_tables']) ? $db_drop_tables = "0" : $db_drop_tables = $_REQUEST['db_drop_tables'];

  if (isset($_REQUEST['site_URL'])) $site_URL = $_REQUEST['site_URL'];
    else $site_URL = $web_root;

  if (isset($_REQUEST['root_directory'])) $root_directory = stripslashes($_REQUEST['root_directory']);
    else $root_directory = $current_dir;

  if (isset($_REQUEST['cache_dir']))
    $cache_dir= $_REQUEST['cache_dir'];

  if (isset($_REQUEST['mail_server']))
    $mail_server= $_REQUEST['mail_server'];

  if (isset($_REQUEST['mail_server_username']))
    $mail_server_username= $_REQUEST['mail_server_username'];

  if (isset($_REQUEST['mail_server_password']))
    $mail_server_password= $_REQUEST['mail_server_password'];

  if (isset($_REQUEST['admin_email']))
    $admin_email = $_REQUEST['admin_email'];

  if (isset($_REQUEST['admin_password']))
    $admin_password = $_REQUEST['admin_password'];
}
else {
  !isset($_REQUEST['db_hostname']) ? $db_hostname = $hostname: $db_hostname = $_REQUEST['db_hostname'];
  /*
  TODO: introduce MySQL port as parameters to use non-default value 3306
  TODO: deprecate connection.php file parameters
  !isset($_REQUEST['db_hostname']) ? $db_hostname = $hostname.$sock_path : $db_hostname = $_REQUEST['db_hostname'];
  !isset($_REQUEST['db_username']) ? $db_username = $mysql_username : $db_username = $_REQUEST['db_username'];
  !isset($_REQUEST['db_password']) ? $db_password= $mysql_password : $db_password = $_REQUEST['db_password'];
  */
  !isset($_REQUEST['db_name']) ? $db_name = "vtigercrm4_2_4" : $db_name = $_REQUEST['db_name'];
  !isset($_REQUEST['db_drop_tables']) ? $db_drop_tables = "0" : $db_drop_tables = $_REQUEST['db_drop_tables'];
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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  <title>vtigerCRM 4.x Installer: Step 2</title>
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

  // decide whether to submit the form
  if (trim(form.db_hostname.value) =='') {
    isError = true;
    errorMessage += "\n database host name";
    form.db_hostname.focus();
  }

  if (trim(form.db_username.value) =='') {
    isError = true;
    errorMessage += "\n database user name";
    form.db_username.focus();
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
/* TODO: add comments or cleanup
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
/* TODO: add comments or cleanup
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
  // decide whether to submit the form
  if (isError == true) {
    alert("Missing required fields: " + errorMessage);
    return false;
  }

  return true;
}
// end hiding contents from old browsers  -->
</script>

<table width="75%" border="0" cellpadding="3" cellspacing="0" align="center" style="border-bottom: 1px dotted #CCCCCC;">
  <tbody>
    <tr>
      <td align="left"><a href="http://www.vtiger.com" target="_blank" title="vtigerCRM"><IMG alt="vtigerCRM" border="0" src="include/images/vtiger_crmlogo.gif"/></a></td>
      <td align="right"><h2>Step 2 of 5</h2></td>
      <td align="right"><IMG alt="vtigerCRM" border="0" src="include/images/spacer.gif" width="10" height="1"/></td>
    </tr>
  </tbody>
</table>

<table width="75%" align="center" border="0" cellpadding="10" cellspacing="0" border="0">
  <tbody>
    <tr>
      <td width="100%">
        <table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tbody>
            <tr>
              <td>
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                  <tbody>
                    <tr>
                      <td nowrap><h3>System Configuration</h3></td>
                      <td width="80%"><hr width="100%"></td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </td>
     </tr>
     <tr>
       <td>
         <P><b>Please enter your database configuration information below...</b></p>
         <P>
           If you do not have root access to your database (for example you are installing in a virtual hosting environment),<br>
           you will need to have your database created for you before you proceed.<br>
           However, this installer will still be able to create the necessary database tables.
         </P>
         <p>If you unsure of your database host, username or password, we suggest that you use the default values below.</P>
       </td>
     </tr>
     <tr>
        <td align="center">
        <form action="install.php" method="post" onsubmit="return verify_data(setConfig);" name="setConfig" id="form">
        <input type="hidden" name="file" value="3confirmConfig.php">
        <div align="left" width="70%"><font color=red>* Required field</font></div>
        <table width="80%" cellpadding="5" border="0" style="border: 1px dotted #666666;">
          <tbody>
            <tr>
              <td bgcolor="#EEEEEE"><h4>Database Configuration</h4></td>
            </tr>
        </table>
        <table width="80%" cellpadding="5" cellspacing="1" border="0" style="border: 1px dotted #666666;">
          <tbody>
            <tr>
              <td nowrap bgcolor="#F5F5F5" width="40%"><strong>Host Name</strong> <sup><font color=red>*</font></sup></td>
              <td align="left">
              <input type="text" class="dataInput" name="db_hostname" value="<?php if (isset($db_hostname)) echo "$db_hostname"; ?>" /></td>
            </tr>
            <tr>
              <td nowrap bgcolor="#F5F5F5"><strong>User Name</strong> <sup><font color=red>*</font></sup></td>
              <td align="left">
              <input type="text" class="dataInput" name="db_username" value="<?php if (isset($db_username)) echo "$db_username"; ?>" /></td>
            </tr>
            <tr>
              <td nowrap bgcolor="#F5F5F5"><strong>Password</strong> <sup><font color=red>*</font></sup></td>
              <td align="left">
              <input type="password" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" /></td>
            </tr>
            <tr>
              <td nowrap bgcolor="#F5F5F5"><strong>Database Name</strong> <sup><font color=red>*</font></sup></td>
              <td align="left"><input type="text" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" /></td>
            </tr>
		<?php if(function_exists('mysql_connect') && function_exists('pg_connect')) : ?>
	    <tr>
	      <td nowrap bgcolor="#F5F5F5"><strong>Database Type</strong> <sup><font color=red>*</font></sup></td>
	      <td align="left">
	        <select class="dataInput" name="dbtype">
		  <option value="mysql" <?php echo $dbconfig['db_type'] == 'mysql' ? 'selected' : '' ?>>MySQL</option>
		  <option value="pgsql" <?php echo $dbconfig['db_type'] == 'pgsql' ? 'selected' : '' ?>>Postgresql</option>
		  <option value="oci8" <?php echo $dbconfig['db_type'] == 'oci8' ? 'selected' : '' ?>>Oracle 8+</option>
	        </select>
	      </td>
	    </tr>
		<?php elseif(function_exists('mysql_connect')) : ?>
			<input type="hidden" name="dbtype" value="mysql" />
		<?php elseif(function_exists('pg_connect')) : ?>
			<input type="hidden" name="dbtype" value="pgsql" />
		<?php else : ?>
	   <tr>
	      <td nowrap bgcolor="#F5F5F5"><strong>Database Type</strong> <sup><font color=red>*</font></sup></td>
	      <td align="left" class="dataInput">ERROR: none detected</td>
	   </tr>
		<?php endif ?>
          </table>
              <!-- tr>
               <td></td><td nowrap><strong>Drop Existing Tables?</strong></td>
               <td align="left"><input type="checkbox" name="db_drop_tables"
         <?php if (isset($db_drop_tables) && $db_drop_tables==true) echo "checked "; ?> value="$db_drop_tables"/></td>
              </tr -->
      <br><table width="80%" cellpadding="5" border="0" style="border: 1px dotted #666666;"><tbody>
                    <tr>
            <td bgcolor="#EEEEEE"><h4>Site Configuration</h4></td>
                    </tr>
              </table>
            <table width="80%" cellpadding="5" border="0" style="border: 1px dotted #666666;">
            <tr>
      <td bgcolor="#F5F5F5" width="40%"><strong>URL</strong> <sup><font color=red>*</font></sup></td>
            <td align="left"><input class="dataInput" type="text" name="site_URL"
      value="<?php if (isset($site_URL)) echo $site_URL; ?>" size="40" />
        </td>
          </tr><tr>
            <td bgcolor="#F5F5F5"><strong>Path</strong> <sup><font color=red>*</font></sup></td>
            <td align="left"><input class="dataInput" type="text" name="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="40" /> </td>
    </tr><tr valign="top">
            <td bgcolor="#F5F5F5"><strong>Path to Cache Directory  <sup><font color=red>*</font></sup><br>(must be writable)</td>
            <td align="left"><?php echo $root_directory; ?><input class="dataInput" type="text" name="cache_dir" size='14' value="<?php if (isset($cache_dir)) echo $cache_dir; ?>" size="40" /> </td>
          </tr>
          </table><br>
            <table width="80%" cellpadding="5" border="0" style="border: 1px dotted #666666;">
    <tr>
      <td bgcolor="#EEEEEE"><h4>Admin Configuration</h4></td>
              </tr>
              </table>
  <table width="80%" cellpadding="5" border="0" style="border: 1px dotted #666666;">
  <tr>

            <td nowrap bgcolor="#F5F5F5" width="40%"><strong>username</strong></td>
            <td align="left">admin</td>
          </tr>

  <tr><td bgcolor="#F5F5F5" nowrap><strong>password</strong><sup><font color=red>*</font></sup></td>
        <td align="left"><input class="dataInput" type="password" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; else echo "admin"; ?>"></td>
          </tr>
  <tr>
  <td colspan="2"><font color=blue> <b>Note:</b> The default password is 'admin'. You can change the password if necessary now or else you can change it later in vtigerCRM </font></td>

        </tr>
    </table>

<table width="70%" cellpadding="5" border="0">
          <tr>
      <td align="right"><br /> <input class="button" type="submit" name="next" value="Next >" /></td>
          </tr>
  </tbody></table>

</form>
</body>
</html>
