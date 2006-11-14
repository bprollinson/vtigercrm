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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/Logout.php,v 1.8 2005/03/21 04:51:21 ray Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('include/logging.php');
require_once('database/DatabaseConnection.php');
require_once('modules/Users/LoginHistory.php');
require_once('modules/Users/Users.php');
require_once('config.php');
require_once('include/db_backup/backup.php');
require_once('include/db_backup/ftp.php');
require_once('include/database/PearDatabase.php');
require_once('user_privileges/enable_backup.php');

global $adb, $enable_backup;

if($enable_backup == 'true')
{
	$ftpserver = '';
	$ftpuser = '';
	$ftppassword = '';
	$query = "select * from vtiger_systems where server_type='backup'";
	$result = $adb->query($query);
	$num_rows = $adb->num_rows($result);
	if($num_rows > 0)
	{
		$ftpserver = $adb->query_result($result,0,'server');
		$ftpuser = $adb->query_result($result,0,'server_username');
		$ftppassword = $adb->query_result($result,0,'server_password');
	}

	//Taking the Backup of DB
	$currenttime=date("Ymd_His");
	if($ftpserver != '' && $ftpuser != '' && $ftppassword != '')
	{	$backupFileName="backup_".$currenttime.".sql";
	save_structure($backupFileName, $root_directory);
	$source_file=$backupFileName;	
	ftpBackupFile($source_file, $ftpserver, $ftpuser, $ftppassword);
	if(file_exists($source_file)) unlink($source_file);	

	}
}
// Recording Logout Info
	$usip=$_SERVER['REMOTE_ADDR'];
        $outtime=date("Y/m/d H:i:s");
        $loghistory=new LoginHistory();
        $loghistory->user_logout($current_user->user_name,$usip,$outtime);


$local_log =& LoggerManager::getLogger('Logout');

//Calendar Logout
//include('modules/Calendar/logout.php');

// clear out the autthenticating flag
session_destroy();

define("IN_LOGIN", true);
	
// define('IN_PHPBB', true);
// include($phpbb_root_path . 'extension.inc');
// include($phpbb_root_path . 'common.'.$phpEx);

//
// Set page ID for session management
//
//$userdata = session_pagestart($user_ip, PAGE_LOGIN);
//init_userprefs($userdata);
//
// End session management
//

// session id check
/*
if (!empty($HTTP_POST_VARS['sid']) || !empty($HTTP_GET_VARS['sid']))
{
        $sid = (!empty($HTTP_POST_VARS['sid'])) ? $HTTP_POST_VARS['sid'] : $HTTP_GET_VARS['sid'];
}
else
{
        $sid = '';
}
if( $userdata['session_logged_in'] )
	{
		if( $userdata['session_logged_in'] )
		{
			session_end($userdata['session_id'], $userdata['user_id']);
		}

	}
*/
// go to the login screen.
header("Location: index.php?action=Login&module=Users");
?>
