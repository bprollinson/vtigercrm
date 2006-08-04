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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/install/3confirmConfig.php,v 1.14 2005/04/25 09:41:26 samk Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/

if (isset($_REQUEST['db_hostname'])) $db_hostname= $_REQUEST['db_hostname'];
if (isset($_REQUEST['db_username'])) $db_username= $_REQUEST['db_username'];
if (isset($_REQUEST['db_password'])) $db_password= $_REQUEST['db_password'];
if (isset($_REQUEST['db_name'])) $db_name= $_REQUEST['db_name'];
if (isset($_REQUEST['db_drop_tables'])) $db_drop_tables = $_REQUEST['db_drop_tables'];
if (isset($_REQUEST['site_URL'])) $site_URL= $_REQUEST['site_URL'];
if (isset($_REQUEST['admin_email'])) $admin_email= $_REQUEST['admin_email'];
if (isset($_REQUEST['admin_password'])) $admin_password = $_REQUEST['admin_password'];
if (isset($_REQUEST['admin_email'])) $admin_email = $_REQUEST['admin_email'];
if (isset($_REQUEST['cache_dir'])) $cache_dir= $_REQUEST['cache_dir'];
if (isset($_REQUEST['mail_server'])) $mail_server= $_REQUEST['mail_server'];
if (isset($_REQUEST['mail_server_username'])) $mail_server_username= $_REQUEST['mail_server_username'];
if (isset($_REQUEST['mail_server_password'])) $mail_server_password= $_REQUEST['mail_server_password'];
if (isset($_REQUEST['root_directory'])) $root_directory = $_REQUEST['root_directory'];
if (isset($_REQUEST['ftpserver'])) $ftpserver= $_REQUEST['ftpserver'];
if (isset($_REQUEST['ftpuser'])) $ftpuser = $_REQUEST['ftpuser'];
if (isset($_REQUEST['ftppassword'])) $ftppassword= $_REQUEST['ftppassword'];
if (isset($_REQUEST['db_type'])) $db_type = $_REQUEST['db_type'];
if (isset($_REQUEST['check_createdb'])) $check_createdb = $_REQUEST['check_createdb'];
if (isset($_REQUEST['root_user'])) $root_user = $_REQUEST['root_user'];
if (isset($_REQUEST['root_password'])) $root_password = $_REQUEST['root_password'];

$db_type_status = false; // is there a db type?
$db_server_status = false; // does the db server connection exist?
$db_creation_failed = false; // did we try to create a database and fail?
$db_exist_status = false; // does the database exist?
$next = false; // allow installation to continue

//Checking for database connection parameters
if($db_type)
{
	include('adodb/adodb.inc.php');
	$conn = &NewADOConnection($db_type);
	$db_type_status = true;
	if(@$conn->Connect($db_hostname,$db_username,$db_password))
	{
		$db_server_status = true;
		if($db_type=='mysql')
		{
			$mysql_conn = mysql_connect($db_hostname,$db_username,$db_password);
			$version = explode('-',mysql_get_server_info($mysql_conn));
			$mysql_server_version=$version[0];
			mysql_close($mysql_conn);
		}
		if(isset($_REQUEST['check_createdb']) && $_REQUEST['check_createdb'] == 'on')
		{
			$root_user = $_REQUEST['root_user'];
			$root_password = $_REQUEST['root_password'];

			// drop the current database if it exists
			$dropdb_conn = &NewADOConnection($db_type);
			if(@$dropdb_conn->Connect($db_hostname, $root_user, $root_password, $db_name))
			{
				$query = "drop database ".$db_name;
				$dropdb_conn->Execute($query);
				$dropdb_conn->Close();
			}

			// create the new database
			$db_creation_failed = true;
			$createdb_conn = &NewADOConnection($db_type);
			if(@$createdb_conn->Connect($db_hostname, $root_user, $root_password)) {
				$query = "create database ".$db_name;
				if($createdb_conn->Execute($query)) {
					$db_creation_failed = false;
				}
				$createdb_conn->Close();
			}
		}

		// test the connection to the database
		if(@$conn->Connect($db_hostname, $db_username, $db_password, $db_name))
		{
			$db_exist_status = true;
		}
		$conn->Close();
	}
}

$error_msg = '';
$error_msg_info = '';

if(!$db_type_status || !$db_server_status)
{
	$error_msg = 'Unable to connect to database Server. Invalid mySQL Connection Parameters specified';
	$error_msg_info = 'This may be due to the following reasons:<br>
			-  specified database user, password, hostname, database type, or port is invalid.<BR>
			-  specified database user does not have access to connect to the database server from the host';
}
elseif($db_type == 'mysql' && $mysql_server_version < '4.1')
{
	$error_msg = 'MySQL version '.$mysql_server_version.' is not supported, kindly connect to MySQL 4.1.x or above';
}
elseif($db_creation_failed)
{
	$error_msg = 'Unable to Create Database '.$db_name;
	$error_msg_info = 'Message: The database User "'. $root_user .'" doesn\'t have permission to Create database. Try changing the Database settings';
}
elseif(!$db_exist_status)
{
	$error_msg = 'The Database "'.$db_name.'" is not found.Try changing the Database settings';
}
else
{
	$next = true;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>vtiger CRM 5 - Configuration Wizard - Confirm Settings</title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<br><br><br>
	<!-- Table for cfgwiz starts -->

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="Configuration Wizard" hspace="20" title="Configuration Wizard"></td>
		<td class="cwHeadBg" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
			<tr>
				<td width=20% valign=top>

				<!-- Left side tabs -->
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Welcome</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Installation Check</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">System Configuration</div></td></tr>
					<tr><td class="small cwSelectedTab" align=right><div align="left"><b>Confirm Settings</b></div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Config File Creation</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Database Generation</div></td></tr>
					<tr><td class="small cwUnSelectedTab" align=right><div align="left">Finish</div></td></tr>
					</table>
					
				</td>
				<td width=80% valign=top class="cwContentDisplay" align=left>
				<table border=0 cellspacing=0 cellpadding=10 width=100%>
				<tr><td class=small align=left><img src="include/install/images/confWizConfirmSettings.gif" alt="Confirm Configuration Settings" title="Confirm Configuration Settings"><br>
					  <hr noshade size=1></td></tr>
				<tr>
					<td align=left class="small" style="padding-left:20px">
					<?php if($error_msg) : ?>
						<div style="background-color:#ff0000;color:#ffffff;padding:5px">
						<b><?php echo $error_msg ?></b>
						</div>
						<?php if($error_msg_info) : ?>
							<p><? echo $error_msg_info ?><p>
						<?php endif ?>
					<?php endif ?>
					<table width="90%" cellpadding="5" border="0" class="small" style="background-color:#cccccc" cellspacing="1">
					<tr>
						<td colspan=2><strong>Database Configuration</strong></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">Host Name</td>
						<td align="left" nowrap> <font class="dataInput"><?php if (isset($db_hostname)) echo "$db_hostname"; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">User Name</td>
						<td align="left" nowrap> <font class="dataInput"><?php if (isset($db_username)) echo "$db_username"; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%" noWrap>Password</td>
						<td align="left" nowrap> <font class="dataInput"><?php if (isset($db_password)) echo ereg_replace('.', '*', $db_password); ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td noWrap bgcolor="#F5F5F5" width="40%">Database Type</td>
						<td align="left" nowrap> <font class="dataInput"><?php if (isset($db_type)) echo "$db_type"; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td noWrap bgcolor="#F5F5F5" width="40%">Database Name</td>
						<td align="left" nowrap> <font class="dataInput"><?php if (isset($db_name)) echo "$db_name"; ?></font></td>
					</tr>
					</table>
					<table width="90%" cellpadding="5" border="0" class="small" cellspacing="1" style="background-color:#cccccc">
					<tr>
						<td colspan=2 ><strong>Site Configuration</strong></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">URL</td>
						<td align="left"> <font class="dataInput"><?php if (isset($site_URL)) echo $site_URL; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">Path</td>
						<td align="left"><font class="dataInput"><?php if (isset($root_directory)) echo $root_directory; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">Cache Path</td>
						<td align="left"> <font class="dataInput"><?php if (isset($cache_dir)) echo $root_directory.''.$cache_dir; ?></font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">Admin Username</td>
						<td align="left"> <font class="dataInput">admin</font></td>
					</tr>
					<tr bgcolor="White">
						<td bgcolor="#F5F5F5" width="40%">Admin Password</td>
						<td align="left"> <font class="dataInput"><?php if (isset($admin_password)) echo ereg_replace('.', '*', $admin_password); ?></font></td>
					</tr>
					</table>

					<br><br>
					<table width="90%" cellpadding="5" border="0" class="small" >
					<tr>
					<td align="left" valign="bottom">
					<form action="install.php" method="post" name="form" id="form">
						<input type="hidden" name="file" value="2setConfig.php">
						<input type="hidden" class="dataInput" name="db_type" value="<?php if (isset($db_type)) echo "$db_type"; ?>" />
						<input type="hidden" class="dataInput" name="db_hostname" value="<?php if (isset($db_hostname)) echo "$db_hostname"; ?>" />
						<input type="hidden" class="dataInput" name="db_username" value="<?php if (isset($db_username)) echo "$db_username"; ?>" />
						<input type="hidden" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" />
						<input type="hidden" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />
						<input type="hidden" class="dataInput" name="db_drop_tables" value="<?php if (isset($db_drop_tables)) echo "$db_drop_tables"; ?>" />
						<input type="hidden" class="dataInput" name="site_URL" value="<?php if (isset($site_URL)) echo "$site_URL"; ?>" />
						<input type="hidden" class="dataInput" name="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" />
						<input type="hidden" class="dataInput" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>" />
						<input type="hidden" class="dataInput" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; ?>" />
						<input type="hidden" class="dataInput" name="cache_dir" value="<?php if (isset($cache_dir)) echo $cache_dir; ?>" />
						<input type="hidden" class="dataInput" name="mail_server" value="<?php if (isset($maill_server)) echo $mail_server; ?>" />
						<input type="hidden" class="dataInput" name="mail_server_username" value="<?php if (isset($maill_server_username)) echo $mail_server_username; ?>" />
						<input type="hidden" class="dataInput" name="mail_server_password" value="<?php if (isset($maill_server_password)) echo $mail_server_password; ?>" />
						<input type="hidden" class="dataInput" name="ftpserver" value="<?php if (isset($ftpserver)) echo "$ftpserver"; ?>" />
						<input type="hidden" class="dataInput" name="ftpuser" value="<?php if (isset($ftpuser)) echo "$ftpuser"; ?>" />
						<input type="hidden" class="dataInput" name="ftppassword" value="<?php if (isset($ftppassword)) echo "$ftppassword"; ?>" />
						<input type="hidden" class="dataInput" name="check_createdb" value="<?php if (isset($check_createdb)) echo "$check_createdb"; ?>" />
						<input type="hidden" class="dataInput" name="root_user" value="<?php if (isset($root_user)) echo "$root_user"; ?>" />
						<input type="hidden" class="dataInput" name="root_password" value="<?php if (isset($root_password)) echo "$root_password"; ?>" />
						<input type="image" name="Change" value="Change" title="Change" src="include/install/images/cwBtnChange.gif"/>
					</form>
					</td>

					<?php if($next) : ?>
					<td align="right" valign="bottom">
					<form action="install.php" method="post" name="form" id="form">
						<input type="hidden" name="file" value="4createConfigFile.php">
							<table class=small>
							<tr>
								<td><input type="checkbox" class="dataInput" name="db_populate" value="1"></td>
								<td>Populate database with demo data</td>
							</tr>
							</table>
						<input type="hidden" class="dataInput" name="db_type" value="<?php if (isset($db_type)) echo "$db_type"; ?>" />
						<input type="hidden" class="dataInput" name="db_hostname" value="<?php if (isset($db_hostname)) echo "$db_hostname"; ?>" />
						<input type="hidden" class="dataInput" name="db_username" value="<?php if (isset($db_username)) echo "$db_username"; ?>" />
						<input type="hidden" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" />
						<input type="hidden" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />
						<input type="hidden" class="dataInput" name="db_drop_tables" value="<?php if (isset($db_drop_tables)) echo "$db_drop_tables"; ?>" />
						<input type="hidden" class="dataInput" name="site_URL" value="<?php if (isset($site_URL)) echo "$site_URL"; ?>" />
						<input type="hidden" class="dataInput" name="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" />
						<input type="hidden" class="dataInput" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>" />
						<input type="hidden" class="dataInput" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; ?>" />
						<input type="hidden" class="dataInput" name="cache_dir" value="<?php if (isset($cache_dir)) echo $cache_dir; ?>" />
						<input type="hidden" class="dataInput" name="mail_server" value="<?php if (isset($mail_server)) echo $mail_server; ?>" />
						<input type="hidden" class="dataInput" name="mail_server_username" value="<?php if (isset($mail_server_username)) echo $mail_server_username; ?>" />
						<input type="hidden" class="dataInput" name="mail_server_password" value="<?php if (isset($mail_server_password)) echo $mail_server_password; ?>" />
						<input type="hidden" class="dataInput" name="ftpserver" value="<?php if (isset($ftpserver)) echo "$ftpserver"; ?>" />
						<input type="hidden" class="dataInput" name="ftpuser" value="<?php if (isset($ftpuser)) echo "$ftpuser"; ?>" />
						<input type="hidden" class="dataInput" name="ftppassword" value="<?php if (isset($ftppassword)) echo "$ftppassword"; ?>" />
						<input type="hidden" class="dataInput" name="check_createdb" value="<?php if (isset($check_createdb)) echo "$check_createdb"; ?>" />
						<input type="hidden" class="dataInput" name="root_user" value="<?php if (isset($root_user)) echo "$root_user"; ?>" />
						<input type="hidden" class="dataInput" name="root_password" value="<?php if (isset($root_password)) echo "$root_password"; ?>" />
						<input type="image" src="include/install/images/cwBtnNext.gif" name="next" title="Next" value="Create" onClick="window.location=('install.php')"/>
					</form>
					</td>
					<?php endif ?>
					</tr>
					</table>

				</td>
				</tr>
			</table>
</td>
		</tr>
	</table>
	<!-- Master display stops -->
	<br>
	</td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>

		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>

      	<tr>
        	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
      	</tr>
    	</table>	
</body>
</html>
