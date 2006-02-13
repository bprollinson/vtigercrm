<?php
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *
 ***************************************************************************/

define('IN_PHPBB', 1);
if( !empty($setmodules) )
{
        $filename = basename(__FILE__);
        $module['Users']['Add_new'] = $filename;
        return;
}

$phpbb_root_path = "modules/MessageBoard/";
require($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.php');
#require($phpbb_root_path . 'admin/pagestart.' . $phpEx);

$unhtml_specialchars_match = array('#>#', '#<#', '#"#', '#&#');
$unhtml_specialchars_replace = array('>', '<', '"', '&');

$error = FALSE;
$page_title = $lang['Register'];

$coppa = ( empty($HTTP_POST_VARS['coppa']) && empty($HTTP_GET_VARS['coppa']) ) ? 0 : TRUE;
$sql = "SELECT config_value
	FROM " . CONFIG_TABLE . "
	WHERE config_name = 'board_timezone'";
if ( !($result = $db->sql_query($sql)) )
{
	message_die(GENERAL_ERROR, 'Could not select default dateformat', '', __LINE__, __FILE__, $sql);
}
$row = $db->sql_fetchrow($result);
$board_config['board_timezone'] = $row['config_value'];
$db->sql_freeresult($result);

//
// Check and initialize some variables if needed
//
$mode = $_POST['mode'];
$username = $_POST['user_name'];
$email = $_POST['email1'];
$is_save = $_POST['button'];

if (trim($is_save) == 'Save')
{
	$cur_password = $username;
	$password_confirm = $username;
	$new_password = $username;
}
if (isset($HTTP_POST_VARS['button']) || $mode == 'register' )
{
	include($phpbb_root_path . 'includes/functions_validate.'.$phpEx);
	include($phpbb_root_path . 'includes/bbcode.'.$phpEx);
	include($phpbb_root_path . 'includes/functions_post.'.$phpEx);

	$strip_var_list = array('username' => 'username', 'email' => 'email', 'new_password' => 'new_password', 'password_confirm' => 'password_confirm');

	// Strip all tags from data ... may p**s some people off, bah, strip_tags is
	// doing the job but can still break HTML output ... have no choice, have
	// to use htmlspecialchars ... be prepared to be moaned at.
	while( list($var, $param) = @each($strip_var_list) )
	{
		if ( !empty($HTTP_POST_VARS[$param]) )
		{
			$$var = trim(htmlspecialchars($HTTP_POST_VARS[$param]));
		}
	}

	$user_style = ( isset($HTTP_POST_VARS['style']) ) ? intval($HTTP_POST_VARS['style']) : $board_config['default_style'];

	if ( !empty($HTTP_POST_VARS['language']) )
	{
		if ( preg_match('/^[a-z_]+$/i', $HTTP_POST_VARS['language']) )
		{
			$user_lang = htmlspecialchars($HTTP_POST_VARS['language']);
		}
		else
		{
			$error = true;
			$error_msg = $lang['Fields_empty'];
		}
	}
	else
	{
		$user_lang = $board_config['default_lang'];
	}

	$user_timezone = ( isset($HTTP_POST_VARS['timezone']) ) ? doubleval($HTTP_POST_VARS['timezone']) : $board_config['board_timezone'];
	$sql = "SELECT config_value
		FROM " . CONFIG_TABLE . "
		WHERE config_name = 'default_dateformat'";
	if ( !($result = $db->sql_query($sql)) )
	{
		message_die(GENERAL_ERROR, 'Could not select default dateformat', '', __LINE__, __FILE__, $sql);
	}
	$row = $db->sql_fetchrow($result);
	$board_config['default_dateformat'] = $row['config_value'];
	$db->sql_freeresult($result);

	$user_dateformat = ( !empty($HTTP_POST_VARS['dateformat']) ) ? trim(htmlspecialchars($HTTP_POST_VARS['dateformat'])) : $board_config['default_dateformat'];

	if ( !isset($HTTP_POST_VARS['button']) )
	{
		$username = stripslashes($username);
		$email = stripslashes($email);
		$cur_password = htmlspecialchars(stripslashes($cur_password));
		$new_password = htmlspecialchars(stripslashes($new_password));
		$password_confirm = htmlspecialchars(stripslashes($password_confirm));

		$user_lang = stripslashes($user_lang);
		$user_dateformat = stripslashes($user_dateformat);
	}
}
//
// Let's make sure the user isn't logged in while registering,
// and ensure that they were trying to register a second time
// (Prevents double registrations)
//
if ($mode == 'register' && $username == $userdata['username'])
{
	message_die(GENERAL_MESSAGE, $lang['Username_taken'], '', __LINE__, __FILE__);
}

//
// Did the user submit? In this case build a query to update the users profile in the DB
//
if ( isset($HTTP_POST_VARS['button']) )
{
	$passwd_sql = '';
	if ( empty($username) || empty($new_password) || empty($password_confirm) || empty($email) )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Fields_empty'];
	}
	else if ( ( empty($new_password) && !empty($password_confirm) ) || ( !empty($new_password) && empty($password_confirm) ) || ( $new_password != $password_confirm ) )
	{
		$error = TRUE;
		$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $lang['Password_mismatch'];
	}

	//
	// Do a ban check on this email address
	//
	if ( $email != $userdata['user_email'] || $mode == 'register' )
	{
		$result = validate_email($email);
		if ( $result['error'] )
		{
			$email = $userdata['user_email'];

			$error = TRUE;
			$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $result['error_msg'];
		}
	}

	$username_sql = '';
	if ( empty($username) )
	{
		$error = TRUE;
	}
	else if ( $username != $userdata['username'] || $mode == 'register' )
	{
		if (strtolower($username) != strtolower($userdata['username']))
		{
			$result = validate_username($username);
			if ( $result['error'] )
			{
				$error = TRUE;
				$error_msg .= ( ( isset($error_msg) ) ? '<br />' : '' ) . $result['error_msg'];
			}
		}

		if (!$error)
		{
			$username_sql = "username = '" . str_replace("\'", "''", $username) . "', ";
		}
	}

	if ( !$error )
	{
		$sql = "SELECT MAX(user_id) AS total
			FROM " . USERS_TABLE;
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
		}

		if ( !($row = $db->sql_fetchrow($result)) )
		{
			message_die(GENERAL_ERROR, 'Could not obtain next user_id information', '', __LINE__, __FILE__, $sql);
		}
		$user_id = $row['total'] + 1;

		$new_password = md5($new_password);

		$sql = "INSERT INTO " . USERS_TABLE . "	(user_id, username, user_regdate, user_password, user_email, user_style, user_timezone, user_dateformat, user_lang, user_level, user_active, user_actkey)
			VALUES ($user_id, '" . str_replace("\'", "''", $username) . "',	" . time() . ",	'" . str_replace("\'", "''", $new_password) . "',	'" . str_replace("\'", "''", $email) . "', $user_style, $user_timezone, '" . str_replace("\'", "''", $user_dateformat) . "', '" . str_replace("\'", "''", $user_lang) . "', 0, 1, 'user_actkey')";
		if ( !($result = $db->sql_query($sql, BEGIN_TRANSACTION)) )
		{
			message_die(GENERAL_ERROR, 'Could not insert data into users table', '', __LINE__, __FILE__, $sql);
		}

		$sql = "INSERT INTO " . GROUPS_TABLE . " (group_name, group_description, group_single_user, group_moderator)
			VALUES ('', 'Personal User', 1, 0)";
		if ( !($result = $db->sql_query($sql)) )
		{
			message_die(GENERAL_ERROR, 'Could not insert data into groups table', '', __LINE__, __FILE__, $sql);
		}

		$group_id = $db->sql_nextid();

		$sql = "INSERT INTO " . USER_GROUP_TABLE . " (user_id, group_id, user_pending)
			VALUES ($user_id, $group_id, 0)";
		if( !($result = $db->sql_query($sql, END_TRANSACTION)) )
		{
			message_die(GENERAL_ERROR, 'Could not insert data into user_group table', '', __LINE__, __FILE__, $sql);
		}


		$message = $lang['Account_added'];
		//message_die(GENERAL_MESSAGE, $message);
	}
} // End of submit

if ( $error )
{
	//
	// If an error occured we need to stripslashes on returned data
	//
	$username = stripslashes($username);
	$email = stripslashes($email);
	$new_password = '';
	$password_confirm = '';

	$user_lang = stripslashes($user_lang);
	$user_dateformat = stripslashes($user_dateformat);

}

//
// Default pages
//
/*
include($phpbb_root_path . 'includes/functions_selects.'.$phpEx);

$coppa = FALSE;

if ( !isset($user_template) )
{
	$selected_template = $board_config['system_template'];
}

$s_hidden_fields = '<input type="hidden" name="mode" value="' . $mode . '" /><input type="hidden" name="agreed" value="true" /><input type="hidden" name="coppa" value="' . $coppa . '" />';

if ( $error )
{
	$template->set_filenames(array(
		'reg_header' => 'error_body.tpl')
	);
	$template->assign_vars(array(
		'ERROR_MESSAGE' => $error_msg)
	);
	$template->assign_var_from_handle('ERROR_BOX', 'reg_header');
}

$template->set_filenames(array(
	'body' => 'admin/admin_add_user_body.tpl')
);

//
// Let's do an overall check for settings/versions which would prevent
// us from doing file uploads....
$template->assign_vars(array(
	'USERNAME' => $username,
	'CUR_PASSWORD' => $cur_password,
	'NEW_PASSWORD' => $new_password,
	'PASSWORD_CONFIRM' => $password_confirm,
	'EMAIL' => $email,
	'LANGUAGE_SELECT' => language_select($board_config['default_lang'], 'language'),
	'STYLE_SELECT' => style_select($board_config['default_style'], 'style'),
	'TIMEZONE_SELECT' => tz_select($board_config['board_timezone'], 'timezone'),
	'DATE_FORMAT_SELECT' => dateformatselect($board_config['default_dateformat'], $user_timezone),

	'L_USERNAME' => $lang['Username'],
	'L_CURRENT_PASSWORD' => $lang['Current_password'],
	'L_NEW_PASSWORD' => ( $mode == 'register' ) ? $lang['Password'] : $lang['New_password'],
	'L_CONFIRM_PASSWORD' => $lang['Confirm_password'],
	'L_CONFIRM_PASSWORD_EXPLAIN' => ( $mode == 'editprofile' ) ? $lang['Confirm_password_explain'] : '',
	'L_PASSWORD_IF_CHANGED' => ( $mode == 'editprofile' ) ? $lang['password_if_changed'] : '',
	'L_PASSWORD_CONFIRM_IF_CHANGED' => ( $mode == 'editprofile' ) ? $lang['password_confirm_if_changed'] : '',
	'L_SUBMIT' => $lang['Submit'],
	'L_RESET' => $lang['Reset'],
	'L_BOARD_LANGUAGE' => $lang['Board_lang'],
	'L_BOARD_STYLE' => $lang['Board_style'],
	'L_TIMEZONE' => $lang['Timezone'],
	'L_DATE_FORMAT' => $lang['Date_format'],
	'L_DATE_FORMAT_EXPLAIN' => $lang['Date_format_explain'],
	'L_YES' => $lang['Yes'],
	'L_NO' => $lang['No'],

	'L_ITEMS_REQUIRED' => $lang['Items_required'],
	'L_PREFERENCES' => $lang['Preferences'],
	'L_REGISTRATION_INFO' => $lang['Registration_info'],
	'L_PROFILE_INFO' => $lang['Profile_info'],
	'L_PROFILE_INFO_NOTICE' => $lang['Profile_info_warn'],
	'L_EMAIL_ADDRESS' => $lang['Email_address'],
	'L_VALIDATION' => $lang['Validation'],
	'L_VALIDATION_EXPLAIN' => $lang['Validation_explain'],

	'S_HIDDEN_FIELDS' => $s_hidden_fields,
	'S_PROFILE_ACTION' => append_sid("admin_user_register.$phpEx"))
);

$template->pparse('body');

include($phpbb_root_path . 'admin/page_footer_admin.'.$phpEx);

function dateformatselect($default, $timezone, $select_name = 'dateformat')
{
	global $board_config;

	// Include any valid PHP date format strings here, in your preferred order
	$date_formats = array(
		'D d.M, Y',
		'D d.M, Y g:i a',
		'D d.M, Y H:i',
		'D M d, Y',
		'D M d, Y g:i a',
		'D M d, Y H:i',
		'n.F Y',
		'n.F Y, g:i a',
		'n.F Y, H:i',
		'F jS Y',
		'F jS Y, g:i a',
		'F jS Y, H:i',
		'j/n/Y',
		'j/n/Y, g:i a',
		'j/n/Y, H:i',
		'n/j/Y',
		'n/j/Y, g:i a',
		'n/j/Y, H:i',
		'Y-m-d',
		'Y-m-d, g:i a',
		'Y-m-d, H:i'
	);

	if ( !isset($timezone) )
	{
		$timezone == $board_config['board_timezone'];
	}
	$now = time() + (3600 * $timezone);

	$df_select = '<select name="' . $select_name . '">';
	for ($i = 0; $i < sizeof($date_formats); $i++)
	{
		$format = $date_formats[$i];
		$display = date($format, $now);
		$df_select .= '<option value="' . $format . '"';
		if (isset($default) && ($default == $format))
		{
			$df_select .= ' selected';
		}
		$df_select .= '>' . $display . '</option>';
	}
	$df_select .= '</select>';

	return $df_select;
}
*/
?>
