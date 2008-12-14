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
 * $Header$
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/



/** This function returns the name of the person.
  * It currently returns "first last".  It should not put the space if either name is not available.
  * It should not return errors if either name is not available.
  * If no names are present, it will return ""
  * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
  * All Rights Reserved.
  * Contributor(s): ______________________________________..
  */

  require_once('include/database/PearDatabase.php');
  require_once('include/ComboUtil.php'); //new
  require_once('include/utils/ListViewUtils.php');	
  require_once('include/utils/EditViewUtils.php');
  require_once('include/utils/DetailViewUtils.php');
  require_once('include/utils/CommonUtils.php');
  require_once('include/utils/InventoryUtils.php');
  require_once('include/utils/DeleteUtils.php');
  require_once('include/utils/SearchUtils.php');
  require_once('include/FormValidationUtil.php');
  require_once('include/utils/RecyclebinUtils.php');
 
// Constants to be defined here

// For Migration status.
define("MIG_CHARSET_PHP_UTF8_DB_UTF8", 1);
define("MIG_CHARSET_PHP_NONUTF8_DB_NONUTF8", 2);
define("MIG_CHARSET_PHP_NONUTF8_DB_UTF8", 3);
define("MIG_CHARSET_PHP_UTF8_DB_NONUTF8", 4);

// For Customview status.
define("CV_STATUS_DEFAULT", 0);				
define("CV_STATUS_PRIVATE", 1);
define("CV_STATUS_PENDING", 2);
define("CV_STATUS_PUBLIC", 3);

/** Function to return a full name
  * @param $row -- row:: Type integer
  * @param $first_column -- first column:: Type string
  * @param $last_column -- last column:: Type string
  * @returns $fullname -- fullname:: Type string 
  *
*/
function return_name(&$row, $first_column, $last_column)
{
	global $log;
	$log->debug("Entering return_name(".$row.",".$first_column.",".$last_column.") method ...");
	$first_name = "";
	$last_name = "";
	$full_name = "";

	if(isset($row[$first_column]))
	{
		$first_name = stripslashes($row[$first_column]);
	}

	if(isset($row[$last_column]))
	{
		$last_name = stripslashes($row[$last_column]);
	}

	$full_name = $first_name;

	// If we have a first name and we have a last name
	if($full_name != "" && $last_name != "")
	{
		// append a space, then the last name
		$full_name .= " ".$last_name;
	}
	// If we have no first name, but we have a last name
	else if($last_name != "")
	{
		// append the last name without the space.
		$full_name .= $last_name;
	}

	$log->debug("Exiting return_name method ...");
	return $full_name;
}

/** Function to return language 
  * @returns $languages -- languages:: Type string 
  *
*/

function get_languages()
{
	global $log;
	$log->debug("Entering get_languages() method ...");
	global $languages;
	$log->debug("Exiting get_languages method ...");
	return $languages;
}

/** Function to return language 
  * @param $key -- key:: Type string
  * @returns $languages -- languages:: Type string 
  *
*/

//seems not used
function get_language_display($key)
{
	global $log;
	$log->debug("Entering get_language_display(".$key.") method ...");
	global $languages;
	$log->debug("Exiting get_language_display method ...");
	return $languages[$key];
}

/** Function returns the user array 
  * @param $assigned_user_id -- assigned_user_id:: Type string
  * @returns $user_list -- user list:: Type array 
  *
*/

function get_assigned_user_name(&$assigned_user_id)
{
	global $log;
	$log->debug("Entering get_assigned_user_name(".$assigned_user_id.") method ...");
	$user_list = &get_user_array(false,"");
	if(isset($user_list[$assigned_user_id]))
	{
		$log->debug("Exiting get_assigned_user_name method ...");
		return $user_list[$assigned_user_id];
	}

	$log->debug("Exiting get_assigned_user_name method ...");
	return "";
}

/** Function returns the user key in user array 
  * @param $add_blank -- boolean:: Type boolean
  * @param $status -- user status:: Type string
  * @param $assigned_user -- user id:: Type string
  * @param $private -- sharing type:: Type string
  * @returns $user_array -- user array:: Type array 
  *
*/

//used in module file
function get_user_array($add_blank=true, $status="Active", $assigned_user="",$private="")
{
	global $log;
	$log->debug("Entering get_user_array(".$add_blank.",". $status.",".$assigned_user.",".$private.") method ...");
	global $current_user;
	if(isset($current_user) && $current_user->id != '')
	{
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
	}
	static $user_array = null;
	$module=$_REQUEST['module'];

	if($user_array == null)
	{
		require_once('include/database/PearDatabase.php');
		$db = new PearDatabase();
		$temp_result = Array();
		// Including deleted vtiger_users for now.
		if (empty($status)) {
				$query = "SELECT id, user_name from vtiger_users";
				$params = array();
		}
		else {
				if($private == 'private')
				{
					$log->debug("Sharing is Private. Only the current user should be listed");
					$query = "select id as id,user_name as user_name from vtiger_users where id=? and status='Active' union select vtiger_user2role.userid as id,vtiger_users.user_name as user_name from vtiger_user2role inner join vtiger_users on vtiger_users.id=vtiger_user2role.userid inner join vtiger_role on vtiger_role.roleid=vtiger_user2role.roleid where vtiger_role.parentrole like ? and status='Active' union select shareduserid as id,vtiger_users.user_name as user_name from vtiger_tmp_write_user_sharing_per inner join vtiger_users on vtiger_users.id=vtiger_tmp_write_user_sharing_per.shareduserid where status='Active' and vtiger_tmp_write_user_sharing_per.userid=? and vtiger_tmp_write_user_sharing_per.tabid=?";	
					$params = array($current_user->id, $current_user_parent_role_seq."::%", $current_user->id, getTabid($module));	
				}
				else
				{
					$log->debug("Sharing is Public. All vtiger_users should be listed");
					$query = "SELECT id, user_name from vtiger_users WHERE status=?";
					$params = array($status);
				}
		}
		if (!empty($assigned_user)) {
			 $query .= " OR id=?";
			 array_push($params, $assigned_user);
		}

		$query .= " order by user_name ASC";

		$result = $db->pquery($query, $params, true, "Error filling in user array: ");

		if ($add_blank==true){
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while($row = $db->fetchByAssoc($result))
		{
			$temp_result[$row['id']] = $row['user_name'];
		}

		$user_array = &$temp_result;
	}

	$log->debug("Exiting get_user_array method ...");
	return $user_array;
}

function get_group_array($add_blank=true, $status="Active", $assigned_user="",$private="")
{
	global $log;
	$log->debug("Entering get_user_array(".$add_blank.",". $status.",".$assigned_user.",".$private.") method ...");
	global $current_user;
	if(isset($current_user) && $current_user->id != '')
	{
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
	}
	static $group_array = null;
	$module=$_REQUEST['module'];

	if($group_array == null)
	{
		require_once('include/database/PearDatabase.php');
		$db = new PearDatabase();
		$temp_result = Array();
		// Including deleted vtiger_users for now.
		if (empty($status)) {
				$query = "SELECT groupid, groupname from vtiger_groups";
				$params = array();
		}
		else {
				if($private == 'private')
				{
					$log->debug("Sharing is Private. Only the current user should be listed");
					$query = "select groupid as groupid,groupname as groupname from vtiger_groups where id=? union select vtiger_group2role.groupid as groupid,vtiger_groups.groupname as groupname from vtiger_group2role inner join vtiger_groups on vtiger_groups.groupid=vtiger_group2role.groupid inner join vtiger_role on vtiger_role.roleid=vtiger_group2role.roleid where vtiger_role.parentrole like ? union select sharedgroupid as groupid,vtiger_groups.groupname as groupname from vtiger_tmp_write_group_sharing_per inner join vtiger_groups on vtiger_groups.groupid=vtiger_tmp_write_group_sharing_per.sharedgroupid where vtiger_tmp_write_group_sharing_per.groupid=? and vtiger_tmp_write_group_sharing_per.tabid=?";	
					$params = array($current_user->id, $current_user_parent_role_seq."::%", $current_user->id, getTabid($module));	
				}
				else
				{
					$log->debug("Sharing is Public. All vtiger_users should be listed");
					$query = "SELECT groupid, groupname from vtiger_groups";
					$params = array();
				}
		}
		if (!empty($assigned_user)) {
			 $query .= "WHERE groupid=?";
			 array_push($params, $assigned_user);
		}

		$query .= " order by groupname ASC";

		$result = $db->pquery($query, $params, true, "Error filling in user array: ");

		if ($add_blank==true){
			// Add in a blank row
			$temp_result[''] = '';
		}

		// Get the id and the name.
		while($row = $db->fetchByAssoc($result))
		{
			$temp_result[$row['groupid']] = $row['groupname'];
		}

		$group_array = &$temp_result;
	}

	$log->debug("Exiting get_user_array method ...");
	return $group_array;
}
/** Function skips executing arbitary commands given in a string
  * @param $string -- string:: Type string
  * @param $maxlength -- maximun length:: Type integer
  * @returns $string -- escaped string:: Type string 
  *
*/

function clean($string, $maxLength)
{
	global $log;
	$log->debug("Entering clean(".$string.",". $maxLength.") method ...");
	$string = substr($string, 0, $maxLength);
	$log->debug("Exiting clean method ...");
	return escapeshellcmd($string);
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function safe_map($request_var, & $focus, $always_copy = false)
{
	global $log;
	$log->debug("Entering safe_map(".$request_var.",".get_class($focus).",".$always_copy.") method ...");
	safe_map_named($request_var, $focus, $request_var, $always_copy);
	$log->debug("Exiting safe_map method ...");
}

/**
 * Copy the specified request variable to the member variable of the specified object.
 * Do no copy if the member variable is already set.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function safe_map_named($request_var, & $focus, $member_var, $always_copy)
{
	global $log;
	$log->debug("Entering safe_map_named(".$request_var.",".get_class($focus).",".$member_var.",".$always_copy.") method ...");
	if (isset($_REQUEST[$request_var]) && ($always_copy || is_null($focus->$member_var))) {
		$log->debug("safe map named called assigning '{$_REQUEST[$request_var]}' to $member_var");
		$focus->$member_var = $_REQUEST[$request_var];
	}
	$log->debug("Exiting safe_map_named method ...");
}

/** This function retrieves an application language file and returns the array of strings included in the $app_list_strings var.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * If you are using the current language, do not call this function unless you are loading it for the first time */

function return_app_list_strings_language($language)
{
	global $log;
	$log->debug("Entering return_app_list_strings_language(".$language.") method ...");
	global $app_list_strings, $default_language, $log, $translation_string_prefix;
	$temp_app_list_strings = $app_list_strings;
	$language_used = $language;

	@include("include/language/$language.lang.php");
	if(!isset($app_list_strings))
	{
		$log->warn("Unable to find the application language file for language: ".$language);
		require("include/language/$default_language.lang.php");
		$language_used = $default_language;
	}

	if(!isset($app_list_strings))
	{
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_app_list_strings_language method ...");
		return null;
	}


	$return_value = $app_list_strings;
	$app_list_strings = $temp_app_list_strings;

	$log->debug("Exiting return_app_list_strings_language method ...");
	return $return_value;
}

/** This function retrieves an application language file and returns the array of strings included.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * If you are using the current language, do not call this function unless you are loading it for the first time */
function return_application_language($language)
{
	global $log;
	$log->debug("Entering return_application_language(".$language.") method ...");
	global $app_strings, $default_language, $log, $translation_string_prefix;
	$temp_app_strings = $app_strings;
	$language_used = $language;

	@include("include/language/$language.lang.php");
	if(!isset($app_strings))
	{
		$log->warn("Unable to find the application language file for language: ".$language);
		require("include/language/$default_language.lang.php");
		$language_used = $default_language;
	}

	if(!isset($app_strings))
	{
		$log->fatal("Unable to load the application language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_application_language method ...");
		return null;
	}

	// If we are in debug mode for translating, turn on the prefix now!
	if($translation_string_prefix)
	{
		foreach($app_strings as $entry_key=>$entry_value)
		{
			$app_strings[$entry_key] = $language_used.' '.$entry_value;
		}
	}

	$return_value = $app_strings;
	$app_strings = $temp_app_strings;

	$log->debug("Exiting return_application_language method ...");
	return $return_value;
}

/** This function retrieves a module's language file and returns the array of strings included.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * If you are in the current module, do not call this function unless you are loading it for the first time */
function return_module_language($language, $module)
{
	global $log;
	$log->debug("Entering return_module_language(".$language.",". $module.") method ...");
	global $mod_strings, $default_language, $log, $currentModule, $translation_string_prefix;

	if($currentModule == $module && isset($mod_strings) && $mod_strings != null)
	{
		// We should have already loaded the array.  return the current one.
		$log->debug("Exiting return_module_language method ...");
		return $mod_strings;
	}

	$temp_mod_strings = $mod_strings;
	$language_used = $language;

	@include("modules/$module/language/$language.lang.php");
	if(!isset($mod_strings))
	{
		$log->warn("Unable to find the module language file for language: ".$language." and module: ".$module);
		require("modules/$module/language/$default_language.lang.php");
		$language_used = $default_language;
	}

	if(!isset($mod_strings))
	{
		$log->fatal("Unable to load the module($module) language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_module_language method ...");
		return null;
	}

	// If we are in debug mode for translating, turn on the prefix now!
	if($translation_string_prefix)
	{
		foreach($mod_strings as $entry_key=>$entry_value)
		{
			$mod_strings[$entry_key] = $language_used.' '.$entry_value;
		}
	}

	$return_value = $mod_strings;
	$mod_strings = $temp_mod_strings;

	$log->debug("Exiting return_module_language method ...");
	return $return_value;
}

/*This function returns the mod_strings for the current language and the specified module
*/

function return_specified_module_language($language, $module)
{
	global $log;
	global $default_language, $translation_string_prefix;

	@include("modules/$module/language/$language.lang.php");
	if(!isset($mod_strings))
	{
		$log->warn("Unable to find the module language file for language: ".$language." and module: ".$module);
		require("modules/$module/language/$default_language.lang.php");
		$language_used = $default_language;
	}

	if(!isset($mod_strings))
	{
		$log->fatal("Unable to load the module($module) language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_module_language method ...");
		return null;
	}

	$return_value = $mod_strings;

	$log->debug("Exiting return_module_language method ...");
	return $return_value;
}

/** This function retrieves an application language file and returns the array of strings included in the $mod_list_strings var.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * If you are using the current language, do not call this function unless you are loading it for the first time */
function return_mod_list_strings_language($language,$module)
{
	global $log;
	$log->debug("Entering return_mod_list_strings_language(".$language.",".$module.") method ...");
	global $mod_list_strings, $default_language, $log, $currentModule,$translation_string_prefix;

	$language_used = $language;
	$temp_mod_list_strings = $mod_list_strings;

	if($currentModule == $module && isset($mod_list_strings) && $mod_list_strings != null)
	{
		$log->debug("Exiting return_mod_list_strings_language method ...");
		return $mod_list_strings;
	}

	@include("modules/$module/language/$language.lang.php");

	if(!isset($mod_list_strings))
	{
		$log->fatal("Unable to load the application list language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_mod_list_strings_language method ...");
		return null;
	}

	$return_value = $mod_list_strings;
	$mod_list_strings = $temp_mod_list_strings;

	$log->debug("Exiting return_mod_list_strings_language method ...");
	return $return_value;
}

/** This function retrieves a theme's language file and returns the array of strings included.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function return_theme_language($language, $theme)
{
	global $log;
	$log->debug("Entering return_theme_language(".$language.",". $theme.") method ...");
	global $mod_strings, $default_language, $log, $currentModule, $translation_string_prefix;

	$language_used = $language;

	@include("themes/$theme/language/$current_language.lang.php");
	if(!isset($theme_strings))
	{
		$log->warn("Unable to find the theme file for language: ".$language." and theme: ".$theme);
		require("themes/$theme/language/$default_language.lang.php");
		$language_used = $default_language;
	}

	if(!isset($theme_strings))
	{
		$log->fatal("Unable to load the theme($theme) language file for the selected language($language) or the default language($default_language)");
		$log->debug("Exiting return_theme_language method ...");
		return null;
	}

	// If we are in debug mode for translating, turn on the prefix now!
	if($translation_string_prefix)
	{
		foreach($theme_strings as $entry_key=>$entry_value)
		{
			$theme_strings[$entry_key] = $language_used.' '.$entry_value;
		}
	}

	$log->debug("Exiting return_theme_language method ...");
	return $theme_strings;
}



/** If the session variable is defined and is not equal to "" then return it.  Otherwise, return the default value.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
*/
function return_session_value_or_default($varname, $default)
{
	global $log;
	$log->debug("Entering return_session_value_or_default(".$varname.",". $default.") method ...");
	if(isset($_SESSION[$varname]) && $_SESSION[$varname] != "")
	{
		$log->debug("Exiting return_session_value_or_default method ...");
		return $_SESSION[$varname];
	}

	$log->debug("Exiting return_session_value_or_default method ...");
	return $default;
}

/**
  * Creates an array of where restrictions.  These are used to construct a where SQL statement on the query
  * It looks for the variable in the $_REQUEST array.  If it is set and is not "" it will create a where clause out of it.
  * @param &$where_clauses - The array to append the clause to
  * @param $variable_name - The name of the variable to look for an add to the where clause if found
  * @param $SQL_name - [Optional] If specified, this is the SQL column name that is used.  If not specified, the $variable_name is used as the SQL_name.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
  */
function append_where_clause(&$where_clauses, $variable_name, $SQL_name = null)
{
	global $log;
	$log->debug("Entering append_where_clause(".$where_clauses.",".$variable_name.",".$SQL_name.") method ...");
	if($SQL_name == null)
	{
		$SQL_name = $variable_name;
	}

	if(isset($_REQUEST[$variable_name]) && $_REQUEST[$variable_name] != "")
	{
		array_push($where_clauses, "$SQL_name like '$_REQUEST[$variable_name]%'");
	}
	$log->debug("Exiting append_where_clause method ...");
}

/**
  * Generate the appropriate SQL based on the where clauses.
  * @param $where_clauses - An Array of individual where clauses stored as strings
  * @returns string where_clause - The final SQL where clause to be executed.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
  */
function generate_where_statement($where_clauses)
{
	global $log;
	$log->debug("Entering generate_where_statement(".$where_clauses.") method ...");
	$where = "";
	foreach($where_clauses as $clause)
	{
		if($where != "")
		$where .= " and ";
		$where .= $clause;
	}

	$log->info("Here is the where clause for the list view: $where");
	$log->debug("Exiting generate_where_statement method ...");
	return $where;
}

/**
 * A temporary method of generating GUIDs of the correct format for our DB.
 * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
 *
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
*/
function create_guid()
{
	global $log;
	$log->debug("Entering create_guid() method ...");
        $microTime = microtime();
	list($a_dec, $a_sec) = explode(" ", $microTime);

	$dec_hex = sprintf("%x", $a_dec* 1000000);
	$sec_hex = sprintf("%x", $a_sec);

	ensure_length($dec_hex, 5);
	ensure_length($sec_hex, 6);

	$guid = "";
	$guid .= $dec_hex;
	$guid .= create_guid_section(3);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= create_guid_section(4);
	$guid .= '-';
	$guid .= $sec_hex;
	$guid .= create_guid_section(6);

	$log->debug("Exiting create_guid method ...");
	return $guid;

}

/** Function to create guid section for a given character
  * @param $characters -- characters:: Type string
  * @returns $return -- integer:: Type integer``
  */
function create_guid_section($characters)
{
	global $log;
	$log->debug("Entering create_guid_section(".$characters.") method ...");
	$return = "";
	for($i=0; $i<$characters; $i++)
	{
		$return .= sprintf("%x", rand(0,15));
	}
	$log->debug("Exiting create_guid_section method ...");
	return $return;
}

/** Function to ensure length
  * @param $string -- string:: Type string
  * @param $length -- length:: Type string
  */

function ensure_length(&$string, $length)
{
	global $log;
	$log->debug("Entering ensure_length(".$string.",". $length.") method ...");
	$strlen = strlen($string);
	if($strlen < $length)
	{
		$string = str_pad($string,$length,"0");
	}
	else if($strlen > $length)
	{
		$string = substr($string, 0, $length);
	}
	$log->debug("Exiting ensure_length method ...");
}
/*
function microtime_diff($a, $b) {
	global $log;
	$log->debug("Entering microtime_diff(".$a.",". $b.") method ...");
	list($a_dec, $a_sec) = explode(" ", $a);
	list($b_dec, $b_sec) = explode(" ", $b);
	$log->debug("Exiting microtime_diff method ...");
	return $b_sec - $a_sec + $b_dec - $a_dec;
}
 */

/**
 * Return the display name for a theme if it exists.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_theme_display($theme) {
	global $log;
	$log->debug("Entering get_theme_display(".$theme.") method ...");
	global $theme_name, $theme_description;
	$temp_theme_name = $theme_name;
	$temp_theme_description = $theme_description;

	if (is_file("./themes/$theme/config.php")) {
		@include("./themes/$theme/config.php");
		$return_theme_value = $theme_name;
	}
	else {
		$return_theme_value = $theme;
	}
	$theme_name = $temp_theme_name;
	$theme_description = $temp_theme_description;

	$log->debug("Exiting get_theme_display method ...");
	return $return_theme_value;
}

/**
 * Return an array of directory names.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_themes() {
	global $log;
	$log->debug("Entering get_themes() method ...");
   if ($dir = @opendir("./themes")) {
		while (($file = readdir($dir)) !== false) {
           if ($file != ".." && $file != "." && $file != "CVS" && $file != "Attic" && $file != "akodarkgem" && $file != "bushtree" && $file != "coolblue" && $file != "Amazon" && $file != "busthree" && $file != "Aqua" && $file != "nature" && $file != "orange" && $file != "blue") {
			   if(is_dir("./themes/".$file)) {
				   if(!($file[0] == '.')) {
				   	// set the initial theme name to the filename
				   	$name = $file; 

				   	// if there is a configuration class, load that.
				   	if(is_file("./themes/$file/config.php"))
				   	{
				   		require_once("./themes/$file/config.php");
				   		$name = $theme_name;
				   	}

				   	if(is_file("./themes/$file/header.php"))
					{
						$filelist[$file] = $name;
					}
				   }
			   }
		   }
	   }
	   closedir($dir);
   }

   ksort($filelist);
   $log->debug("Exiting get_themes method ...");
   return $filelist;
}



/**
 * Create javascript to clear values of all elements in a form.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_clear_form_js () {
global $log;
$log->debug("Entering get_clear_form_js () method ...");
$the_script = <<<EOQ
<script type="text/javascript" language="JavaScript">
<!-- Begin
function clear_form(form) {
	for (j = 0; j < form.elements.length; j++) {
		if (form.elements[j].type == 'text' || form.elements[j].type == 'select-one') {
			form.elements[j].value = '';
		}
	}
}
//  End -->
</script>
EOQ;

$log->debug("Exiting get_clear_form_js  method ...");
return $the_script;
}

/**
 * Create javascript to set the cursor focus to specific vtiger_field in a form
 * when the screen is rendered.  The vtiger_field name is currently hardcoded into the
 * the function.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function get_set_focus_js () {
global $log;
$log->debug("Entering set_focus() method ...");
//TODO Clint 5/20 - Make this function more generic so that it can take in the target form and vtiger_field names as variables
$the_script = <<<EOQ
<script type="text/javascript" language="JavaScript">
<!-- Begin
function set_focus() {
	if (document.forms.length > 0) {
		for (i = 0; i < document.forms.length; i++) {
			for (j = 0; j < document.forms[i].elements.length; j++) {
				var vtiger_field = document.forms[i].elements[j];
				if ((vtiger_field.type == "text" || vtiger_field.type == "textarea" || vtiger_field.type == "password") &&
						!field.disabled && (vtiger_field.name == "first_name" || vtiger_field.name == "name")) {
				vtiger_field.focus();
                    if (vtiger_field.type == "text") {
                        vtiger_field.select();
                    }
					break;
	    		}
			}
      	}
   	}
}
//  End -->
</script>
EOQ;

$log->debug("Exiting get_set_focus_js  method ...");
return $the_script;
}

/**
 * Very cool algorithm for sorting multi-dimensional arrays.  Found at http://us2.php.net/manual/en/function.array-multisort.php
 * Syntax: $new_array = array_csort($array [, 'col1' [, SORT_FLAG [, SORT_FLAG]]]...);
 * Explanation: $array is the array you want to sort, 'col1' is the name of the column
 * you want to sort, SORT_FLAGS are : SORT_ASC, SORT_DESC, SORT_REGULAR, SORT_NUMERIC, SORT_STRING
 * you can repeat the 'col',FLAG,FLAG, as often you want, the highest prioritiy is given to
 * the first - so the array is sorted by the last given column first, then the one before ...
 * Example: $array = array_csort($array,'town','age',SORT_DESC,'name');
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function array_csort() {
   global $log;
   $log->debug("Entering array_csort() method ...");
   $args = func_get_args();
   $marray = array_shift($args);
   $i = 0;

   $msortline = "return(array_multisort(";
   foreach ($args as $arg) {
	   $i++;
	   if (is_string($arg)) {
		   foreach ($marray as $row) {
			   $sortarr[$i][] = $row[$arg];
		   }
	   } else {
		   $sortarr[$i] = $arg;
	   }
	   $msortline .= "\$sortarr[".$i."],";
   }
   $msortline .= "\$marray));";

   eval($msortline);
   $log->debug("Exiting array_csort method ...");
   return $marray;
}

/** Function to set default varibles on to the global variable
  * @param $defaults -- default values:: Type array
       */
function set_default_config(&$defaults)
{
	global $log;
	$log->debug("Entering set_default_config(".$defaults.") method ...");

	foreach ($defaults as $name=>$value)
	{
		if ( ! isset($GLOBALS[$name]) )
		{
			$GLOBALS[$name] = $value;
		}
	}
	$log->debug("Exiting set_default_config method ...");
}

$toHtml = array(
        '"' => '&quot;',
        '<' => '&lt;',
        '>' => '&gt;',
        '& ' => '&amp; ',
        "'" =>  '&#039;',
	'' => '\r',
        '\r\n'=>'\n',

);

/** Function to convert the given string to html
  * @param $string -- string:: Type string
  * @param $ecnode -- boolean:: Type boolean
    * @returns $string -- string:: Type string 
      *
       */
function to_html($string, $encode=true)
{
	global $log,$default_charset;
	//$log->debug("Entering to_html(".$string.",".$encode.") method ...");
	global $toHtml;
	$action = $_REQUEST['action'];
	$search = $_REQUEST['search'];

	$doconvert = false;

	if($_REQUEST['module'] != 'Settings' && $_REQUEST['file'] != 'ListView' && $_REQUEST['module'] != 'Portal' && $_REQUEST['module'] != "Reports")// && $_REQUEST['module'] != 'Emails')
		$ajax_action = $_REQUEST['module'].'Ajax';

	if(is_string($string))
	{
		if($action != 'CustomView' && $action != 'Export' && $action != $ajax_action && $action != 'LeadConvertToEntities' && $action != 'CreatePDF' && $action != 'ConvertAsFAQ' && $_REQUEST['module'] != 'Dashboard' && $action != 'CreateSOPDF' && $action != 'SendPDFMail' && (!isset($_REQUEST['submode'])) )
		{
			$doconvert = true;
		}
		else if($search == true)
		{
			// Fix for tickets #4647, #4648. Conversion required in case of search results also.
			$doconvert = true;
		}
		if ($doconvert == true)
		{
			if(strtolower($default_charset) == 'utf-8') 
				$string = htmlentities($string, ENT_QUOTES, $default_charset);
			else
				$string = preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $string);
		}
	}

	//$log->debug("Exiting to_html method ...");
	return $string;
}

/** Function to get the tabname for a given id
  * @param $tabid -- tab id:: Type integer
    * @returns $string -- string:: Type string 
      *
       */

function getTabname($tabid)
{
	global $log;
	$log->debug("Entering getTabname(".$tabid.") method ...");
        $log->info("tab id is ".$tabid);
        global $adb;
	$sql = "select tablabel from vtiger_tab where tabid=?";
	$result = $adb->pquery($sql, array($tabid));
	$tabname=  $adb->query_result($result,0,"tablabel");
	$log->debug("Exiting getTabname method ...");
	return $tabname;

}

/** Function to get the tab module name for a given id
  * @param $tabid -- tab id:: Type integer
    * @returns $string -- string:: Type string 
      *
       */

function getTabModuleName($tabid)
{
	global $log;
	$log->debug("Entering getTabModuleName(".$tabid.") method ...");
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0))
        {
                include('tabdata.php');
		$tabname = array_search($tabid,$tab_info_array);
        }
        else
        {
	global $log;
        $log->info("tab id is ".$tabid);
        global $adb;
        $sql = "select name from vtiger_tab where tabid=?";
        $result = $adb->pquery($sql, array($tabid));
        $tabname=  $adb->query_result($result,0,"name");
	}
	$log->debug("Exiting getTabModuleName method ...");
        return $tabname;
}

/** Function to get column fields for a given module
  * @param $module -- module:: Type string
    * @returns $column_fld -- column field :: Type array 
      *
       */

function getColumnFields($module)
{
	global $log;
	$log->debug("Entering getColumnFields(".$module.") method ...");
	$log->info("in getColumnFields ".$module);
	global $adb;
	$column_fld = Array();
        $tabid = getTabid($module);
	$sql = "select * from vtiger_field where tabid=?";
        $result = $adb->pquery($sql, array($tabid));
        $noofrows = $adb->num_rows($result);
	for($i=0; $i<$noofrows; $i++)
	{
		$fieldname = $adb->query_result($result,$i,"fieldname");
		$column_fld[$fieldname] = ''; 
	}
	$log->debug("Exiting getColumnFields method ...");
	return $column_fld;	
}

/** Function to get a users's mail id
  * @param $userid -- userid :: Type integer
    * @returns $email -- email :: Type string 
      *
       */

function getUserEmail($userid)
{
	global $log;
	$log->debug("Entering getUserEmail(".$userid.") method ...");
	$log->info("in getUserEmail ".$userid);

        global $adb;
        if($userid != '')
        {
                $sql = "select email1 from vtiger_users where id=?";
                $result = $adb->pquery($sql, array($userid));
                $email = $adb->query_result($result,0,"email1");
        }
	$log->debug("Exiting getUserEmail method ...");
        return $email;
}		

/** Function to get a userid for outlook
  * @param $username -- username :: Type string
    * @returns $user_id -- user id :: Type integer 
       */

//outlook security
function getUserId_Ol($username)
{
	global $log;
	$log->debug("Entering getUserId_Ol(".$username.") method ...");
	$log->info("in getUserId_Ol ".$username);

	global $adb;
	$sql = "select id from vtiger_users where user_name=?";
	$result = $adb->pquery($sql, array($username));
	$num_rows = $adb->num_rows($result);
	if($num_rows > 0)
	{
		$user_id = $adb->query_result($result,0,"id");
    	}
	else
	{
		$user_id = 0;
	}
	$log->debug("Exiting getUserId_Ol method ...");
	return $user_id;
}	


/** Function to get a action id for a given action name
  * @param $action -- action name :: Type string
    * @returns $actionid -- action id :: Type integer 
       */

//outlook security

function getActionid($action)
{
	global $log;
	$log->debug("Entering getActionid(".$action.") method ...");
	global $adb;
	$log->info("get Actionid ".$action);
	$actionid = '';
	if(file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) 
	{
		include('tabdata.php');
		$actionid= $action_id_array[$action];
	}
	else
	{
		$query="select * from vtiger_actionmapping where actionname=?";
        	$result =$adb->pquery($query, array($action));
        	$actionid=$adb->query_result($result,0,'actionid');
		
	}
	$log->info("action id selected is ".$actionid );
	$log->debug("Exiting getActionid method ...");	
	return $actionid;
}

/** Function to get a action for a given action id
  * @param $action id -- action id :: Type integer
    * @returns $actionname-- action name :: Type string 
       */


function getActionname($actionid)
{
	global $log;
	$log->debug("Entering getActionname(".$actionid.") method ...");
	global $adb;

	$actionname='';
	
	if (file_exists('tabdata.php') && (filesize('tabdata.php') != 0)) 
	{
		include('tabdata.php');
		$actionname= $action_name_array[$actionid];
	}
	else
	{
	
		$query="select * from vtiger_actionmapping where actionid=? and securitycheck=0";
		$result =$adb->pquery($query, array($actionid));
		$actionname=$adb->query_result($result,0,"actionname");
	}	
	$log->debug("Exiting getActionname method ...");
	return $actionname;
}

/** Function to get a assigned user id for a given entity
  * @param $record -- entity id :: Type integer
    * @returns $user_id -- user id :: Type integer 
       */

function getUserId($record)
{
	global $log;
	$log->debug("Entering getUserId(".$record.") method ...");
        $log->info("in getUserId ".$record);

	global $adb;
        $user_id=$adb->query_result($adb->pquery("select * from vtiger_crmentity where crmid = ?", array($record)),0,'smownerid');
	$log->debug("Exiting getUserId method ...");
	return $user_id;	
}

/** Function to get a user id or group id for a given entity
  * @param $record -- entity id :: Type integer
    * @returns $ownerArr -- owner id :: Type array 
       */

function getRecordOwnerId($record)
{
	global $log;
	$log->debug("Entering getRecordOwnerId(".$record.") method ...");
	global $adb;
	$ownerArr=Array();
	$query="select * from vtiger_crmentity where crmid = ?";
	$result=$adb->pquery($query, array($record));
	if($adb->num_rows($result) > 0)
	{
		$user_id=$adb->query_result($result,0,'smownerid');
		if($user_id != 0)
		{
			$ownerArr['Users']=$user_id;
		}
		elseif($user_id == 0)
		{
			$module=$adb->query_result($result,0,'setype');
			if($module == 'Leads')
			{
				$query1="select vtiger_groups.groupid from vtiger_leadgrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_leadgrouprelation.groupname where leadid=?";
			}
			elseif($module == 'Calendar' || $module == 'Emails')
			{
				$query1="select vtiger_groups.groupid from vtiger_activitygrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_activitygrouprelation.groupname where activityid=?";
			}
			elseif($module == 'HelpDesk')
			{
				$query1="select vtiger_groups.groupid from vtiger_ticketgrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname where ticketid=?";
			}
			elseif($module == 'Accounts')
			{
				$query1="select vtiger_groups.groupid from vtiger_accountgrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_accountgrouprelation.groupname where accountid=?";
			}
			elseif($module == 'Contacts')
			{
				$query1="select vtiger_groups.groupid from vtiger_contactgrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_contactgrouprelation.groupname where contactid=?";
			}
			elseif($module == 'Potentials')
			{
				$query1="select vtiger_groups.groupid from vtiger_potentialgrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname where potentialid=?";
			}
			elseif($module == 'Quotes')
			{
				$query1="select vtiger_groups.groupid from vtiger_quotegrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_quotegrouprelation.groupname where quoteid=?";
			}
			elseif($module == 'PurchaseOrder')
			{
				$query1="select vtiger_groups.groupid from vtiger_pogrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_pogrouprelation.groupname where purchaseorderid=?";
			}
			elseif($module == 'SalesOrder')
			{
				$query1="select vtiger_groups.groupid from vtiger_sogrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_sogrouprelation.groupname where salesorderid=?";
			}
			elseif($module == 'Invoice')
			{
				$query1="select vtiger_groups.groupid from vtiger_invoicegrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_invoicegrouprelation.groupname where invoiceid=?";
			}
			elseif($module == 'Campaigns')
			{
				$query1="select vtiger_groups.groupid from vtiger_campaigngrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_campaigngrouprelation.groupname where campaignid=?";
			}
			elseif($module == 'Documents')
			{
				$query1="select vtiger_groups.groupid from vtiger_notegrouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_notegrouprelation.groupname where notesid=?";
			}			
			else
			{
				require_once("modules/$module/$module.php");
				$modObj = new $module();
				$query1="select vtiger_groups.groupid from vtiger_".$module."grouprelation inner join vtiger_groups on vtiger_groups.groupname = vtiger_".$module."grouprelation.groupname where ".$modObj->groupTable[1]."=?";
			}

			$result1=$adb->pquery($query1, array($record));
			$groupid=$adb->query_result($result1,0,'groupid');
			$ownerArr['Groups']=$groupid;
		}
	}	
	$log->debug("Exiting getRecordOwnerId method ...");
	return $ownerArr;

}

/** Function to insert value to profile2field table
  * @param $profileid -- profileid :: Type integer
       */


function insertProfile2field($profileid)
{
	global $log;
	$log->debug("Entering insertProfile2field(".$profileid.") method ...");
        $log->info("in insertProfile2field ".$profileid);

	global $adb;
	$adb->database->SetFetchMode(ADODB_FETCH_ASSOC); 
	$fld_result = $adb->pquery("select * from vtiger_field where generatedtype=1 and displaytype in (1,2,3) and tabid != 29", array());
        $num_rows = $adb->num_rows($fld_result);
        for($i=0; $i<$num_rows; $i++)
        {
                 $tab_id = $adb->query_result($fld_result,$i,'tabid');
                 $field_id = $adb->query_result($fld_result,$i,'fieldid');
				 $params = array($profileid, $tab_id, $field_id, 0, 1);
                 $adb->pquery("insert into vtiger_profile2field values (?,?,?,?,?)", $params);
	}
	$log->debug("Exiting insertProfile2field method ...");
}

/** Function to insert into default org field
       */

function insert_def_org_field()
{
	global $log;
	$log->debug("Entering insert_def_org_field() method ...");
	global $adb;
	$adb->database->SetFetchMode(ADODB_FETCH_ASSOC); 
	$fld_result = $adb->pquery("select * from vtiger_field where generatedtype=1 and displaytype in (1,2,3) and tabid != 29", array());
        $num_rows = $adb->num_rows($fld_result);
        for($i=0; $i<$num_rows; $i++)
        {
                 $tab_id = $adb->query_result($fld_result,$i,'tabid');
                 $field_id = $adb->query_result($fld_result,$i,'fieldid');
				 $params = array($tab_id, $field_id, 0, 1);
                 $adb->pquery("insert into vtiger_def_org_field values (?,?,?,?)", $params);
	}
	$log->debug("Exiting insert_def_org_field() method ...");
}

/** Function to insert value to profile2field table
  * @param $fld_module -- field module :: Type string
  * @param $profileid -- profileid :: Type integer
  * @returns $result -- result :: Type string
  */
	 
function getProfile2FieldList($fld_module, $profileid)
{
	global $log;
	$log->debug("Entering getProfile2FieldList(".$fld_module.",". $profileid.") method ...");
        $log->info("in getProfile2FieldList ".$fld_module. ' vtiger_profile id is  '.$profileid);

	global $adb;
	$tabid = getTabid($fld_module);
	
	$query = "select vtiger_profile2field.visible,vtiger_field.* from vtiger_profile2field inner join vtiger_field on vtiger_field.fieldid=vtiger_profile2field.fieldid where vtiger_profile2field.profileid=? and vtiger_profile2field.tabid=?";
	$result = $adb->pquery($query, array($profileid, $tabid));
	$log->debug("Exiting getProfile2FieldList method ...");
	return $result;
}

/** Function to insert value to profile2fieldPermissions table
  * @param $fld_module -- field module :: Type string
  * @param $profileid -- profileid :: Type integer
  * @returns $return_data -- return_data :: Type string
  */

//added by jeri

function getProfile2FieldPermissionList($fld_module, $profileid)
{
	global $log;
	$log->debug("Entering getProfile2FieldPermissionList(".$fld_module.",". $profileid.") method ...");
        $log->info("in getProfile2FieldList ".$fld_module. ' vtiger_profile id is  '.$profileid);

	global $adb;
	$tabid = getTabid($fld_module);
	
	$query = "select vtiger_profile2field.visible,vtiger_field.* from vtiger_profile2field inner join vtiger_field on vtiger_field.fieldid=vtiger_profile2field.fieldid where vtiger_profile2field.profileid=? and vtiger_profile2field.tabid=?";
	$qparams = array($profileid, $tabid);
	$result = $adb->pquery($query, $qparams);
	$return_data=array();
    for($i=0; $i<$adb->num_rows($result); $i++)
    {
		$return_data[]=array($adb->query_result($result,$i,"fieldlabel"),$adb->query_result($result,$i,"visible"),$adb->query_result($result,$i,"uitype"),$adb->query_result($result,$i,"visible"),$adb->query_result($result,$i,"fieldid"),$adb->query_result($result,$i,"displaytype"),$adb->query_result($result,$i,"typeofdata"));
	}	
	$log->debug("Exiting getProfile2FieldPermissionList method ...");
	return $return_data;
}

/** Function to getProfile2allfieldsListinsert value to profile2fieldPermissions table
  * @param $mod_array -- mod_array :: Type string
  * @param $profileid -- profileid :: Type integer
  * @returns $profilelist -- profilelist :: Type string
  */

function getProfile2AllFieldList($mod_array,$profileid)
{
	global $log;
     $log->debug("Entering getProfile2AllFieldList(".$mod_array.",".$profileid.") method ...");
     $log->info("in getProfile2AllFieldList vtiger_profile id is " .$profileid);

	global $adb;
	$profilelist=array();
	for($i=0;$i<count($mod_array);$i++)
	{
		$profilelist[key($mod_array)]=getProfile2FieldPermissionList(key($mod_array), $profileid);
		next($mod_array);
	}
	$log->debug("Exiting getProfile2AllFieldList method ...");
	return $profilelist;	
}

/** Function to getdefaultfield organisation list for a given module
  * @param $fld_module -- module name :: Type string
  * @returns $result -- string :: Type object
  */

//end of fn added by jeri

function getDefOrgFieldList($fld_module)
{
	global $log;
	$log->debug("Entering getDefOrgFieldList(".$fld_module.") method ...");
        $log->info("in getDefOrgFieldList ".$fld_module);

	global $adb;
	$tabid = getTabid($fld_module);
	
	$query = "select vtiger_def_org_field.visible,vtiger_field.* from vtiger_def_org_field inner join vtiger_field on vtiger_field.fieldid=vtiger_def_org_field.fieldid where vtiger_def_org_field.tabid=?";
	$qparams = array($tabid);
	$result = $adb->pquery($query, $qparams);
	$log->debug("Exiting getDefOrgFieldList method ...");
	return $result;
}

/** Function to getQuickCreate for a given tabid
  * @param $tabid -- tab id :: Type string
  * @param $actionid -- action id :: Type integer
  * @returns $QuickCreateForm -- QuickCreateForm :: Type boolean
  */

function getQuickCreate($tabid,$actionid)
{
	global $log;
	$log->debug("Entering getQuickCreate(".$tabid.",".$actionid.") method ...");
	$module=getTabModuleName($tabid);
	$actionname=getActionname($actionid);
        $QuickCreateForm= 'true';

	$perr=isPermitted($module,$actionname);
	if($perr = 'no')
	{
                $QuickCreateForm= 'false';
	}	
	$log->debug("Exiting getQuickCreate method ...");
	return $QuickCreateForm;

}

/** Function to getQuickCreate for a given tabid
  * @param $tabid -- tab id :: Type string
  * @param $actionid -- action id :: Type integer
  * @returns $QuickCreateForm -- QuickCreateForm :: Type boolean
  */

function ChangeStatus($status,$activityid,$activity_mode='')
 {
	global $log;
	$log->debug("Entering ChangeStatus(".$status.",".$activityid.",".$activity_mode."='') method ...");
        $log->info("in ChangeStatus ".$status. ' vtiger_activityid is  '.$activityid);

        global $adb;
        if ($activity_mode == 'Task')
        {
                $query = "Update vtiger_activity set status=? where activityid = ?";
        }
        elseif ($activity_mode == 'Events')
        {
                $query = "Update vtiger_activity set eventstatus=? where activityid = ?";
        }
		if($query) {
        	$adb->pquery($query, array($status, $activityid));
		}
	$log->debug("Exiting ChangeStatus method ...");
 }

/** Function to set date values compatible to database (YY_MM_DD)
  * @param $value -- value :: Type string
  * @returns $insert_date -- insert_date :: Type string
  */

function getDBInsertDateValue($value)
{
	global $log;
	$log->debug("Entering getDBInsertDateValue(".$value.") method ...");
	global $current_user;
	$dat_fmt = $current_user->date_format;
	if($dat_fmt == '') {
		$dat_fmt = 'dd-mm-yyyy';
	}
	$insert_date='';
	if($dat_fmt == 'dd-mm-yyyy')
	{
		list($d,$m,$y) = split('-',$value);
	}
	elseif($dat_fmt == 'mm-dd-yyyy')
	{
		list($m,$d,$y) = split('-',$value);
	}
	elseif($dat_fmt == 'yyyy-mm-dd')
	{
		list($y,$m,$d) = split('-',$value);
	}

	if(!$y && !$m && !$d) {
		$insert_date = '';
	} else {
		$insert_date=$y.'-'.$m.'-'.$d;
	}
	$log->debug("Exiting getDBInsertDateValue method ...");
	return $insert_date;
}

/** Function to get unitprice for a given product id
  * @param $productid -- product id :: Type integer
  * @returns $up -- up :: Type string
  */

function getUnitPrice($productid)
{
	global $log,$current_user;
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	$log->debug("Entering getUnitPrice(".$productid.") method ...");
        $log->info("in getUnitPrice productid ".$productid);

        global $adb;
        $query = "select unit_price from vtiger_products where productid=?";
        $result = $adb->pquery($query, array($productid));
        $up = $adb->query_result($result,0,'unit_price');
	$up = convertFromDollar($up,$rate);
	$log->debug("Exiting getUnitPrice method ...");
        return $up;
}

/** Function to upload product image file 
  * @param $mode -- mode :: Type string
  * @param $id -- id :: Type integer
  * @returns $ret_array -- return array:: Type array
  */

function upload_product_image_file($mode,$id)
{
	global $log;
	$log->debug("Entering upload_product_image_file(".$mode.",".$id.") method ...");
	global $root_directory;
        $log->debug("Inside upload_product_image_file. The id is ".$id);
	$uploaddir = $root_directory ."/test/product/";

	$file_path_name = $_FILES['imagename']['name'];
	if (isset($_REQUEST['imagename_hidden'])) {
		$file_name = $_REQUEST['imagename_hidden'];
	} else {
		//allowed file pathname like UTF-8 Character 
		$file_name = ltrim(basename(" ".$file_path_name)); // basename($file_path_name);
	}
	$file_name = $id.'_'.$file_name;
	$filetype= $_FILES['imagename']['type'];
	$filesize = $_FILES['imagename']['size'];

	$ret_array = Array();

	if($filesize > 0)
	{

		if(move_uploaded_file($_FILES["imagename"]["tmp_name"],$uploaddir.$file_name))
		{

			$upload_status = "yes";
			$ret_array["status"] = $upload_status;
			$ret_array["file_name"] = $file_name;
			

		}
		else
		{
			$errorCode =  $_FILES['imagename']['error'];
			$upload_status = "no";
			$ret_array["status"] = $upload_status;
			$ret_array["errorcode"] = $errorCode;
			
			
		}

	}
	else
	{
		$upload_status = "no";
                $ret_array["status"] = $upload_status;
	}
	$log->debug("Exiting upload_product_image_file method ...");
	return $ret_array;		

}

/** Function to upload product image file 
  * @param $id -- id :: Type integer
  * @param $deleted_array -- images to be deleted :: Type array
  * @returns $imagename -- imagelist:: Type array
  */

function getProductImageName($id,$deleted_array='')
{
	global $log;
	$log->debug("Entering getProductImageName(".$id.",".$deleted_array."='') method ...");
	global $adb;
	$image_array=array();	
	$query = "select imagename from vtiger_products where productid=?";
	$result = $adb->pquery($query, array($id));
	$image_name = $adb->query_result($result,0,"imagename");
	$image_array=explode("###",$image_name);
	$log->debug("Inside getProductImageName. The image_name is ".$image_name);
	if($deleted_array!='')
	{
		$resultant_image = array();
		$resultant_image=array_merge(array_diff($image_array,$deleted_array));
		$imagelists=implode('###',$resultant_image);	
		$log->debug("Exiting getProductImageName method ...");
		return	$imagelists;
	}
	else
	{
		$log->debug("Exiting getProductImageName method ...");
		return $image_name;	
	}
}

/** Function to get Contact images 
  * @param $id -- id :: Type integer
  * @returns $imagename -- imagename:: Type string
  */

function getContactImageName($id)
{
	global $log;
	$log->debug("Entering getContactImageName(".$id.") method ...");
        global $adb;
        $query = "select imagename from vtiger_contactdetails where contactid=?";
        $result = $adb->pquery($query, array($id));
        $image_name = $adb->query_result($result,0,"imagename");
        $log->debug("Inside getContactImageName. The image_name is ".$image_name);
	$log->debug("Exiting getContactImageName method ...");
        return $image_name;

}

/** Function to update sub total in inventory 
  * @param $module -- module name :: Type string
  * @param $tablename -- tablename :: Type string
  * @param $colname -- colname :: Type string
  * @param $colname1 -- coluname1 :: Type string
  * @param $entid_fld -- entity field :: Type string
  * @param $entid -- entid :: Type integer
  * @param $prod_total -- totalproduct :: Type integer
  */

function updateSubTotal($module,$tablename,$colname,$colname1,$entid_fld,$entid,$prod_total)
{
	global $log;
	$log->debug("Entering updateSubTotal(".$module.",".$tablename.",".$colname.",".$colname1.",".$entid_fld.",".$entid.",".$prod_total.") method ...");
        global $adb;
        //getting the subtotal
        $query = "select ".$colname.",".$colname1." from ".$tablename." where ".$entid_fld."=?";
        $result1 = $adb->pquery($query, array($entid));
        $subtot = $adb->query_result($result1,0,$colname);
        $subtot_upd = $subtot - $prod_total;

        $gdtot = $adb->query_result($result1,0,$colname1);
        $gdtot_upd = $gdtot - $prod_total;

        //updating the subtotal
        $sub_query = "update $tablename set $colname=?, $colname1=? where $entid_fld=?";
        $adb->pquery($sub_query, array($subtot_upd, $gdtot_upd, $entid));
	$log->debug("Exiting updateSubTotal method ...");
}

/** Function to get Inventory Total 
  * @param $return_module -- return module :: Type string
  * @param $id -- entity id :: Type integer
  * @returns $total -- total:: Type integer
  */

function getInventoryTotal($return_module,$id)
{
	global $log;
	$log->debug("Entering getInventoryTotal(".$return_module.",".$id.") method ...");
	global $adb;
	if($return_module == "Potentials")
	{
		$query ="select vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_seproductsrel.* from vtiger_products inner join vtiger_seproductsrel on vtiger_seproductsrel.productid=vtiger_products.productid where crmid=?";
	}
	elseif($return_module == "Products")
	{
		$query="select vtiger_products.productid,vtiger_products.productname,vtiger_products.unit_price,vtiger_products.qtyinstock,vtiger_crmentity.* from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid where vtiger_crmentity.deleted=0 and productid=?";
	}
	$result = $adb->pquery($query, array($id));
	$num_rows=$adb->num_rows($result);
	$total=0;
	for($i=1;$i<=$num_rows;$i++)
	{
		$unitprice=$adb->query_result($result,$i-1,'unit_price');
		$qty=$adb->query_result($result,$i-1,'quantity');
		$listprice=$adb->query_result($result,$i-1,'listprice');
		if($listprice == '')
		$listprice = $unitprice;
		if($qty =='')
		$qty = 1;
		$total = $total+($qty*$listprice);
	}
	$log->debug("Exiting getInventoryTotal method ...");
	return $total;
}

/** Function to update product quantity 
  * @param $product_id -- product id :: Type integer
  * @param $upd_qty -- quantity :: Type integer
  */

function updateProductQty($product_id, $upd_qty)
{
	global $log;
	$log->debug("Entering updateProductQty(".$product_id.",". $upd_qty.") method ...");
	global $adb;
	$query= "update vtiger_products set qtyinstock=? where productid=?";
    $adb->pquery($query, array($upd_qty, $product_id));
	$log->debug("Exiting updateProductQty method ...");

}

/** Function to get account information 
  * @param $parent_id -- parent id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function get_account_info($parent_id)
{
	global $log;
	$log->debug("Entering get_account_info(".$parent_id.") method ...");
        global $adb;
        $query = "select accountid from vtiger_potential where potentialid=?";
        $result = $adb->pquery($query, array($parent_id));
        $accountid=$adb->query_result($result,0,'accountid');
	$log->debug("Exiting get_account_info method ...");
        return $accountid;
}

/** Function to get quick create form fields 
  * @param $fieldlabel -- field label :: Type string
  * @param $uitype -- uitype :: Type integer
  * @param $fieldname -- field name :: Type string
  * @param $tabid -- tabid :: Type integer
  * @returns $return_field -- return field:: Type string
  */

//for Quickcreate-Form

function get_quickcreate_form($fieldlabel,$uitype,$fieldname,$tabid)
{
	global $log;
	$log->debug("Entering get_quickcreate_form(".$fieldlabel.",".$uitype.",".$fieldname.",".$tabid.") method ...");
	$return_field ='';
	switch($uitype)	
	{
		case 1: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 2: $return_field .=get_textmanField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 6: $return_field .=get_textdateField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 11: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 13: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;	
			break;
		case 15: $return_field .=get_textcomboField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;	
			break;
		case 16: $return_field .=get_textcomboField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;	
			break;
		case 17: $return_field .=get_textwebField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 19: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;	
			break;
		case 22: $return_field .=get_textmanField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 23: $return_field .=get_textdateField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 50: $return_field .=get_textaccField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 51: $return_field .=get_textaccField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 55: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 63: $return_field .=get_textdurationField($fieldlabel,$fieldname,$tabid);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
		case 71: $return_field .=get_textField($fieldlabel,$fieldname);
			$log->debug("Exiting get_quickcreate_form method ...");
			return $return_field;
			break;
	}
}	

/** Function to get quick create form fields 
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @param $tid -- tabid :: Type integer
  * @returns $form_field -- return field:: Type string
  */

function get_textmanField($label,$name,$tid)
{
	global $log;
	$log->debug("Entering get_textmanField(".$label.",".$name.",".$tid.") method ...");
	$form_field='';
	if($tid == 9)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_T_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
		$log->debug("Exiting get_textmanField method ...");
		return $form_field;	
	}
	if($tid == 16)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_E_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
		$log->debug("Exiting get_textmanField method ...");
		return $form_field;	
	}
	else
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
		$log->debug("Exiting get_textmanField method ...");
		return $form_field;	
	}	
	
}	

/** Function to get textfield for website field  
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @returns $form_field -- return field:: Type string
  */

function get_textwebField($label,$name)
{
	global $log;
	$log->debug("Entering get_textwebField(".$label.",".$name.") method ...");

	$form_field='';
	$form_field .='<td>';
	$form_field .= $label.':<br>http://<br>';
	$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
	$log->debug("Exiting get_textwebField method ...");
	return $form_field;
	
}

/** Function to get textfield   
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @returns $form_field -- return field:: Type string
  */

function get_textField($label,$name)
{
	global $log;
	$log->debug("Entering get_textField(".$label.",".$name.") method ...");	
	$form_field='';
	if($name == "amount")
	{
		$form_field .='<td>';
		$form_field .= $label.':(U.S Dollar:$)<br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
		$log->debug("Exiting get_textField method ...");
		return $form_field;
	}
	else
	{
		
		$form_field .='<td>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="20" maxlength="" value=""></td>';
		$log->debug("Exiting get_textField method ...");
		return $form_field;
	}
	
}

/** Function to get account textfield   
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @param $tid -- tabid :: Type integer
  * @returns $form_field -- return field:: Type string
  */

function get_textaccField($label,$name,$tid)
{
	global $log;
	$log->debug("Entering get_textaccField(".$label.",".$name.",".$tid.") method ...");
	
	global $app_strings;

	$form_field='';
	if($tid == 2)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="account_name" type="text" size="20" maxlength="" id="account_name" value="" readonly><br>';
		$form_field .='<input name="account_id" id="QCK_'.$name.'" type="hidden" value="">&nbsp;<input title="'.$app_strings[LBL_CHANGE_BUTTON_TITLE].'" accessKey="'.$app_strings[LBL_CHANGE_BUTTON_KEY].'" type="button" tabindex="3" class="button" value="'.$app_strings[LBL_CHANGE_BUTTON_LABEL].'" name="btn1" LANGUAGE=javascript onclick=\'return window.open("index.php?module=Accounts&action=Popup&popuptype=specific&form=EditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1");\'></td>';
		$log->debug("Exiting get_textaccField method ...");
		return $form_field;
	}
	else
	{	
		$form_field .='<td>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="account_name" type="text" size="20" maxlength="" value="" readonly><br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="hidden" value="">&nbsp;<input title="'.$app_strings[LBL_CHANGE_BUTTON_TITLE].'" accessKey="'.$app_strings[LBL_CHANGE_BUTTON_KEY].'" type="button" tabindex="3" class="button" value="'.$app_strings[LBL_CHANGE_BUTTON_LABEL].'" name="btn1" LANGUAGE=javascript onclick=\'return window.open("index.php?module=Accounts&action=Popup&popuptype=specific&form=EditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1");\'></td>';
		$log->debug("Exiting get_textaccField method ...");
		return $form_field;
	}	
		
}

/** Function to get combo field values   
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @returns $form_field -- return field:: Type string
  */

function get_textcomboField($label,$name)
{
	global $log;
	$log->debug("Entering get_textcomboField(".$label.",".$name.") method ...");
	$form_field='';
	if($name == "sales_stage")
	{
		$comboFieldNames = Array('leadsource'=>'leadsource_dom'
                      ,'opportunity_type'=>'opportunity_type_dom'
                      ,'sales_stage'=>'sales_stage_dom');
		$comboFieldArray = getComboArray($comboFieldNames);
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<select name="'.$name.'">';
		$form_field .=get_select_options_with_id($comboFieldArray['sales_stage_dom'], "");
		$form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
		return $form_field;
		
	}
	if($name == "productcategory")
	{
		$comboFieldNames = Array('productcategory'=>'productcategory_dom');
		$comboFieldArray = getComboArray($comboFieldNames);
		$form_field .='<td>';
		$form_field .= $label.':<br>';
		$form_field .='<select name="'.$name.'">';
		$form_field .=get_select_options_with_id($comboFieldArray['productcategory_dom'], "");
		$form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
		return $form_field;	
		
	}
	if($name == "ticketpriorities")
	{
		$comboFieldNames = Array('ticketpriorities'=>'ticketpriorities_dom');
		$comboFieldArray = getComboArray($comboFieldNames);	
		$form_field .='<td>';
		$form_field .= $label.':<br>';
		$form_field .='<select name="'.$name.'">';
		$form_field .=get_select_options_with_id($comboFieldArray['ticketpriorities_dom'], "");
		$form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
		return $form_field;
	}
	if($name == "activitytype")
	{
		$comboFieldNames = Array('activitytype'=>'activitytype_dom',
			 'duration_minutes'=>'duration_minutes_dom');
		$comboFieldArray = getComboArray($comboFieldNames);
		$form_field .='<td>';
		$form_field .= $label.'<br>';
		$form_field .='<select name="'.$name.'">';
		$form_field .=get_select_options_with_id($comboFieldArray['activitytype_dom'], "");
		$form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
		return $form_field;
		
		
	}
        if($name == "eventstatus")
        {
                $comboFieldNames = Array('eventstatus'=>'eventstatus_dom');
                $comboFieldArray = getComboArray($comboFieldNames);
                $form_field .='<td>';
                $form_field .= $label.'<br>';
                $form_field .='<select name="'.$name.'">';
                $form_field .=get_select_options_with_id($comboFieldArray['eventstatus_dom'], "");
                $form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
                return $form_field;


        }
        if($name == "taskstatus")
        {
                $comboFieldNames = Array('taskstatus'=>'taskstatus_dom');
                $comboFieldArray = getComboArray($comboFieldNames);
                $form_field .='<td>';
                $form_field .= $label.'<br>';
                $form_field .='<select name="'.$name.'">';
                $form_field .=get_select_options_with_id($comboFieldArray['taskstatus_dom'], "");
                $form_field .='</select></td>';
		$log->debug("Exiting get_textcomboField method ...");
                return $form_field;
        }


	
}

/** Function to get date field    
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @param $tid -- tabid :: Type integer
  * @returns $form_field -- return field:: Type string
  */


function get_textdateField($label,$name,$tid)
{
	global $log;
	$log->debug("Entering get_textdateField(".$label.",".$name.",".$tid.") method ...");
	global $theme;
	global $app_strings;
	global $current_user;

	$ntc_date_format = $app_strings['NTC_DATE_FORMAT'];
	$ntc_time_format = $app_strings['NTC_TIME_FORMAT'];
	
	$form_field='';
	$default_date_start = date('Y-m-d');
	$default_time_start = date('H:i');
	$dis_value=getNewDisplayDate();
	
	if($tid == 2)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<font size="1"><em old="ntc_date_format">('.$current_user->date_format.')</em></font><br>';
		$form_field .='<input name="'.$name.'"  size="12" maxlength="10" id="QCK_'.$name.'" type="text" value="">&nbsp';
	       	$form_field .='<img src="themes/'.$theme.'/images/calendar.gif" id="jscal_trigger"></td>';
		$log->debug("Exiting get_textdateField method ...");
		return $form_field;
			
	}
	if($tid == 9)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_T_'.$name.'" tabindex="2" type="text" size="10" maxlength="10" value="'.$default_date_start.'">&nbsp';
		$form_field.= '<img src="themes/'.$theme.'/images/calendar.gif" id="jscal_trigger_date_start">&nbsp';
		$form_field.='<input name="time_start" id="task_time_start" tabindex="1" type="text" size="5" maxlength="5" type="text" value="'.$default_time_start.'"><br><font size="1"><em old="ntc_date_format">('.$current_user->date_format.')</em></font>&nbsp<font size="1"><em>'.$ntc_time_format.'</em></font></td>';
		$log->debug("Exiting get_textdateField method ...");
		return $form_field;	
	}
	if($tid == 16)
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_E_'.$name.'" tabindex="2" type="text" size="10" maxlength="10" value="'.$default_date_start.'">&nbsp';
		$form_field.= '<img src="themes/'.$theme.'/images/calendar.gif" id="jscal_trigger_event_date_start">&nbsp';
		$form_field.='<input name="time_start" id="event_time_start" tabindex="1" type="text" size="5" maxlength="5" type="text" value="'.$default_time_start.'"><br><font size="1"><em old="ntc_date_format">('.$current_user->date_format.')</em></font>&nbsp<font size="1"><em>'.$ntc_time_format.'</em></font></td>';
		$log->debug("Exiting get_textdateField method ...");
		return $form_field;	
	}
	
	else
	{
		$form_field .='<td>';
		$form_field .= '<font color="red">*</font>';
		$form_field .= $label.':<br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="10" maxlength="10" value="'.$default_date_start.'">&nbsp';
		$form_field.= '<img src="themes/'.$theme.'/images/calendar.gif" id="jscal_trigger">&nbsp';
		$form_field.='<input name="time_start" type="text" size="5" maxlength="5" type="text" value="'.$default_time_start.'"><br><font size="1"><em old="ntc_date_format">('.$current_user->date_format.')</em></font>&nbsp<font size="1"><em>'.$ntc_time_format.'</em></font></td>';
		$log->debug("Exiting get_textdateField method ...");
		return $form_field;	
	}
	
}

/** Function to get duration text field in activity  
  * @param $label -- field label :: Type string
  * @param $name -- field name :: Type string
  * @param $tid -- tabid :: Type integer
  * @returns $form_field -- return field:: Type string
  */

function get_textdurationField($label,$name,$tid)
{
	global $log;
	$log->debug("Entering get_textdurationField(".$label.",".$name.",".$tid.") method ...");
	$form_field='';
	if($tid == 16)
	{
		
		$comboFieldNames = Array('activitytype'=>'activitytype_dom',
			 'duration_minutes'=>'duration_minutes_dom');
		$comboFieldArray = getComboArray($comboFieldNames);
	
		$form_field .='<td>';
		$form_field .= $label.'<br>';
		$form_field .='<input name="'.$name.'" id="QCK_'.$name.'" type="text" size="2" value="1">&nbsp;';
		$form_field .='<select name="duration_minutes">';
		$form_field .=get_select_options_with_id($comboFieldArray['duration_minutes_dom'], "");
		$form_field .='</select><br>(hours/minutes)<br></td>';
		$log->debug("Exiting get_textdurationField method ...");
		return $form_field;
	}	
}

/** Function to get email text field  
  * @param $module -- module name :: Type name
  * @param $id -- entity id :: Type integer
  * @returns $hidden -- hidden:: Type string
  */

//Added to get the parents list as hidden for Emails -- 09-11-2005
function getEmailParentsList($module,$id)
{
	global $log;
	$log->debug("Entering getEmailParentsList(".$module.",".$id.") method ...");
        global $adb;
	if($module == 'Contacts')
		$focus = new Contacts();
	if($module == 'Leads')
		$focus = new Leads();
        
	$focus->retrieve_entity_info($id,$module);
        $fieldid = 0;
        $fieldname = 'email';
        if($focus->column_fields['email'] == '' && $focus->column_fields['yahooid'] != '')
                $fieldname = 'yahooid';

        $res = $adb->pquery("select * from vtiger_field where tabid = ? and fieldname= ?", array(getTabid($module), $fieldname));
        $fieldid = $adb->query_result($res,0,'fieldid');

        $hidden .= '<input type="hidden" name="emailids" value="'.$id.'@'.$fieldid.'|">';
        $hidden .= '<input type="hidden" name="pmodule" value="'.$module.'">';

	$log->debug("Exiting getEmailParentsList method ...");
	return $hidden;
}

/** This Function returns the current status of the specified Purchase Order.
  * The following is the input parameter for the function
  *  $po_id --> Purchase Order Id, Type:Integer
  */
function getPoStatus($po_id)
{
	global $log;
	$log->debug("Entering getPoStatus(".$po_id.") method ...");

	global $log;
        $log->info("in getPoName ".$po_id);

        global $adb;
        $sql = "select postatus from vtiger_purchaseorder where purchaseorderid=?";
        $result = $adb->pquery($sql, array($po_id));
        $po_status = $adb->query_result($result,0,"postatus");
	$log->debug("Exiting getPoStatus method ...");
        return $po_status;
}

/** This Function adds the specified product quantity to the Product Quantity in Stock in the Warehouse 
  * The following is the input parameter for the function:
  *  $productId --> ProductId, Type:Integer
  *  $qty --> Quantity to be added, Type:Integer
  */
function addToProductStock($productId,$qty)
{
	global $log;
	$log->debug("Entering addToProductStock(".$productId.",".$qty.") method ...");
	global $adb;
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck + $qty;
	$sql = "UPDATE vtiger_products set qtyinstock=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting addToProductStock method ...");
	
}

/**	This Function adds the specified product quantity to the Product Quantity in Demand in the Warehouse 
  *	@param int $productId - ProductId
  *	@param int $qty - Quantity to be added
  */
function addToProductDemand($productId,$qty)
{
	global $log;
	$log->debug("Entering addToProductDemand(".$productId.",".$qty.") method ...");
	global $adb;
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck + $qty;
	$sql = "UPDATE vtiger_products set qtyindemand=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting addToProductDemand method ...");
	
}

/**	This Function subtract the specified product quantity to the Product Quantity in Stock in the Warehouse 
  *	@param int $productId - ProductId
  *	@param int $qty - Quantity to be subtracted
  */
function deductFromProductStock($productId,$qty)
{
	global $log;
	$log->debug("Entering deductFromProductStock(".$productId.",".$qty.") method ...");
	global $adb;
	$qtyInStck=getProductQtyInStock($productId);
	$updQty=$qtyInStck - $qty;
	$sql = "UPDATE vtiger_products set qtyinstock=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting deductFromProductStock method ...");
	
}

/**	This Function subtract the specified product quantity to the Product Quantity in Demand in the Warehouse 
  *	@param int $productId - ProductId
  *	@param int $qty - Quantity to be subtract
  */
function deductFromProductDemand($productId,$qty)
{
	global $log;
	$log->debug("Entering deductFromProductDemand(".$productId.",".$qty.") method ...");
	global $adb;
	$qtyInStck=getProductQtyInDemand($productId);
	$updQty=$qtyInStck - $qty;
	$sql = "UPDATE vtiger_products set qtyindemand=? where productid=?";
	$adb->pquery($sql, array($updQty, $productId));
	$log->debug("Exiting deductFromProductDemand method ...");
	
}


/** This Function returns the current product quantity in stock.
  * The following is the input parameter for the function:
  *  $product_id --> ProductId, Type:Integer
  */
function getProductQtyInStock($product_id)
{
	global $log;
	$log->debug("Entering getProductQtyInStock(".$product_id.") method ...");
        global $adb;
        $query1 = "select qtyinstock from vtiger_products where productid=?";
        $result=$adb->pquery($query1, array($product_id));
        $qtyinstck= $adb->query_result($result,0,"qtyinstock");
	$log->debug("Exiting getProductQtyInStock method ...");
        return $qtyinstck;


}

/**	This Function returns the current product quantity in demand.
  *	@param int $product_id - ProductId
  *	@return int $qtyInDemand - Quantity in Demand of a product
  */
function getProductQtyInDemand($product_id)
{
	global $log;
	$log->debug("Entering getProductQtyInDemand(".$product_id.") method ...");
        global $adb;
        $query1 = "select qtyindemand from vtiger_products where productid=?";
        $result = $adb->pquery($query1, array($product_id));
        $qtyInDemand = $adb->query_result($result,0,"qtyindemand");
	$log->debug("Exiting getProductQtyInDemand method ...");
        return $qtyInDemand;
}

/** Function to seperate the Date and Time
  * This function accepts a sting with date and time and
  * returns an array of two elements.The first element
  * contains the date and the second one contains the time
  */
function getDateFromDateAndtime($date_time)
{
	global $log;
	$log->debug("Entering getDateFromDateAndtime(".$date_time.") method ...");
	$result = explode(" ",$date_time);
	$log->debug("Exiting getDateFromDateAndtime method ...");
	return $result;
}


/** Function to get header for block in edit/create and detailview  
  * @param $header_label -- header label :: Type string
  * @returns $output -- output:: Type string
  */

function getBlockTableHeader($header_label)
{
	global $log;
	$log->debug("Entering getBlockTableHeader(".$header_label.") method ...");
	global $mod_strings;
	$label = $mod_strings[$header_label];
	$output = $label;
	$log->debug("Exiting getBlockTableHeader method ...");
	return $output;

}



/**     Function to get the vtiger_table name from 'field' vtiger_table for the input vtiger_field based on the module
 *      @param  : string $module - current module value
 *      @param  : string $fieldname - vtiger_fieldname to which we want the vtiger_tablename
 *      @return : string $tablename - vtiger_tablename in which $fieldname is a column, which is retrieved from 'field' vtiger_table per $module basis
 */
function getTableNameForField($module,$fieldname)
{
	global $log;
	$log->debug("Entering getTableNameForField(".$module.",".$fieldname.") method ...");
	global $adb;
	$tabid = getTabid($module);

	$sql = "select tablename from vtiger_field where tabid=? and columnname like ?";
	$res = $adb->pquery($sql, array($tabid, '%'.$fieldname.'%'));

	$tablename = '';
	if($adb->num_rows($res) > 0)
	{
		$tablename = $adb->query_result($res,0,'tablename');
	}

	$log->debug("Exiting getTableNameForField method ...");
	return $tablename;
}

/** Function to get parent record owner  
  * @param $tabid -- tabid :: Type integer
  * @param $parModId -- parent module id :: Type integer
  * @param $record_id -- record id :: Type integer
  * @returns $parentRecOwner -- parentRecOwner:: Type integer
  */

function getParentRecordOwner($tabid,$parModId,$record_id)
{
	global $log;
	$log->debug("Entering getParentRecordOwner(".$tabid.",".$parModId.",".$record_id.") method ...");
	$parentRecOwner=Array();
	$parentTabName=getTabname($parModId);
	$relTabName=getTabname($tabid);
	$fn_name="get".$relTabName."Related".$parentTabName;
	$ent_id=$fn_name($record_id);
	if($ent_id != '')
	{
		$parentRecOwner=getRecordOwnerId($ent_id);	
	}
	$log->debug("Exiting getParentRecordOwner method ...");
	return $parentRecOwner;
}

/** Function to get potential related accounts   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function getPotentialsRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getPotentialsRelatedAccounts(".$record_id.") method ...");
	global $adb;
	$query="select accountid from vtiger_potential where potentialid=?";
	$result=$adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result,0,'accountid');
	$log->debug("Exiting getPotentialsRelatedAccounts method ...");
	return $accountid;
}

/** Function to get email related accounts   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */
function getEmailsRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getEmailsRelatedAccounts(".$record_id.") method ...");
	global $adb;
	$query = "select vtiger_seactivityrel.crmid from vtiger_seactivityrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid where vtiger_crmentity.setype='Accounts' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$accountid=$adb->query_result($result,0,'crmid');
	$log->debug("Exiting getEmailsRelatedAccounts method ...");
	return $accountid;
}
/** Function to get email related Leads   
  * @param $record_id -- record id :: Type integer
  * @returns $leadid -- leadid:: Type integer
  */

function getEmailsRelatedLeads($record_id)
{
	global $log;
	$log->debug("Entering getEmailsRelatedLeads(".$record_id.") method ...");
	global $adb;
	$query = "select vtiger_seactivityrel.crmid from vtiger_seactivityrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seactivityrel.crmid where vtiger_crmentity.setype='Leads' and activityid=?";
	$result = $adb->pquery($query, array($record_id));
	$leadid=$adb->query_result($result,0,'crmid');
	$log->debug("Exiting getEmailsRelatedLeads method ...");
	return $leadid;
}

/** Function to get HelpDesk related Accounts   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function getHelpDeskRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getHelpDeskRelatedAccounts(".$record_id.") method ...");
	global $adb;
        $query="select parent_id from vtiger_troubletickets inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.parent_id where ticketid=? and vtiger_crmentity.setype='Accounts'";
        $result=$adb->pquery($query, array($record_id));
        $accountid=$adb->query_result($result,0,'parent_id');
	$log->debug("Exiting getHelpDeskRelatedAccounts method ...");
        return $accountid;
}

/** Function to get Quotes related Accounts   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function getQuotesRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getQuotesRelatedAccounts(".$record_id.") method ...");
	global $adb;
        $query="select accountid from vtiger_quotes where quoteid=?";
        $result=$adb->pquery($query, array($record_id));
        $accountid=$adb->query_result($result,0,'accountid');
	$log->debug("Exiting getQuotesRelatedAccounts method ...");
        return $accountid;
}

/** Function to get Quotes related Potentials   
  * @param $record_id -- record id :: Type integer
  * @returns $potid -- potid:: Type integer
  */

function getQuotesRelatedPotentials($record_id)
{
	global $log;
	$log->debug("Entering getQuotesRelatedPotentials(".$record_id.") method ...");
	global $adb;
        $query="select potentialid from vtiger_quotes where quoteid=?";
        $result=$adb->pquery($query, array($record_id));
        $potid=$adb->query_result($result,0,'potentialid');
	$log->debug("Exiting getQuotesRelatedPotentials method ...");
        return $potid;
}

/** Function to get Quotes related Potentials   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function getSalesOrderRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getSalesOrderRelatedAccounts(".$record_id.") method ...");
	global $adb;
        $query="select accountid from vtiger_salesorder where salesorderid=?";
        $result=$adb->pquery($query, array($record_id));
        $accountid=$adb->query_result($result,0,'accountid');
	$log->debug("Exiting getSalesOrderRelatedAccounts method ...");
        return $accountid;
}

/** Function to get SalesOrder related Potentials   
  * @param $record_id -- record id :: Type integer
  * @returns $potid -- potid:: Type integer
  */

function getSalesOrderRelatedPotentials($record_id)
{
	global $log;
	$log->debug("Entering getSalesOrderRelatedPotentials(".$record_id.") method ...");
	global $adb;
        $query="select potentialid from vtiger_salesorder where salesorderid=?";
        $result=$adb->pquery($query, array($record_id));
        $potid=$adb->query_result($result,0,'potentialid');
	$log->debug("Exiting getSalesOrderRelatedPotentials method ...");
        return $potid;
}
/** Function to get SalesOrder related Quotes   
  * @param $record_id -- record id :: Type integer
  * @returns $qtid -- qtid:: Type integer
  */

function getSalesOrderRelatedQuotes($record_id)
{
	global $log;
	$log->debug("Entering getSalesOrderRelatedQuotes(".$record_id.") method ...");
	global $adb;
        $query="select quoteid from vtiger_salesorder where salesorderid=?";
        $result=$adb->pquery($query, array($record_id));
        $qtid=$adb->query_result($result,0,'quoteid');
	$log->debug("Exiting getSalesOrderRelatedQuotes method ...");
        return $qtid;
}

/** Function to get Invoice related Accounts   
  * @param $record_id -- record id :: Type integer
  * @returns $accountid -- accountid:: Type integer
  */

function getInvoiceRelatedAccounts($record_id)
{
	global $log;
	$log->debug("Entering getInvoiceRelatedAccounts(".$record_id.") method ...");
	global $adb;
        $query="select accountid from vtiger_invoice where invoiceid=?";
        $result=$adb->pquery($query, array($record_id));
        $accountid=$adb->query_result($result,0,'accountid');
	$log->debug("Exiting getInvoiceRelatedAccounts method ...");
        return $accountid;
}
/** Function to get Invoice related SalesOrder   
  * @param $record_id -- record id :: Type integer
  * @returns $soid -- soid:: Type integer
  */

function getInvoiceRelatedSalesOrder($record_id)
{
	global $log;
	$log->debug("Entering getInvoiceRelatedSalesOrder(".$record_id.") method ...");
	global $adb;
        $query="select salesorderid from vtiger_invoice where invoiceid=?";
        $result=$adb->pquery($query, array($record_id));
        $soid=$adb->query_result($result,0,'salesorderid');
	$log->debug("Exiting getInvoiceRelatedSalesOrder method ...");
        return $soid;
}


/** Function to get Days and Dates in between the dates specified
        * Portions created by vtiger are Copyright (C) vtiger.
        * All Rights Reserved.
        * Contributor(s): ______________________________________..
 */
function get_days_n_dates($st,$en)
{
	global $log;
	$log->debug("Entering get_days_n_dates(".$st.",".$en.") method ...");
        $stdate_arr=explode("-",$st);
        $endate_arr=explode("-",$en);

        $dateDiff = mktime(0,0,0,$endate_arr[1],$endate_arr[2],$endate_arr[0]) - mktime(0,0,0,$stdate_arr[1],$stdate_arr[2],$stdate_arr[0]);//to get  dates difference

        $days   =  floor($dateDiff/60/60/24)+1; //to calculate no of. days
        for($i=0;$i<$days;$i++)
        {
                $day_date[] = date("Y-m-d",mktime(0,0,0,date("$stdate_arr[1]"),(date("$stdate_arr[2]")+($i)),date("$stdate_arr[0]")));
        }
        if(!isset($day_date))
                $day_date=0;
        $nodays_dates=array($days,$day_date);
	$log->debug("Exiting get_days_n_dates method ...");
        return $nodays_dates; //passing no of days , days in between the days
}//function end


/** Function to get the start and End Dates based upon the period which we give
        * Portions created by vtiger are Copyright (C) vtiger.
        * All Rights Reserved.
        * Contributor(s): ______________________________________..
 */
function start_end_dates($period)
{
	global $log;
	$log->debug("Entering start_end_dates(".$period.") method ...");
        $st_thisweek= date("Y-m-d",mktime(0,0,0,date("n"),(date("j")-date("w")),date("Y")));
        if($period=="tweek")
        {
                $st_date= date("Y-m-d",mktime(0,0,0,date("n"),(date("j")-date("w")),date("Y")));
                $end_date = date("Y-m-d",mktime(0,0,0,date("n"),(date("j")-1),date("Y")));
                $st_week= date("w",mktime(0,0,0,date("n"),date("j"),date("Y")));
                if($st_week==0)
                {
                        $start_week=explode("-",$st_thisweek);
                        $st_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-7),date("$start_week[0]")));
                        $end_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-1),date("$start_week[0]")));
                }
                $period_type="week";
                $width="360";
        }
        else if($period=="lweek")
        {
                $start_week=explode("-",$st_thisweek);
                $st_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-7),date("$start_week[0]")));
                $end_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-1),date("$start_week[0]")));
                $st_week= date("w",mktime(0,0,0,date("n"),date("j"),date("Y")));
                if($st_week==0)
                {
                        $start_week=explode("-",$st_thisweek);
                        $st_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-14),date("$start_week[0]")));
                        $end_date = date("Y-m-d",mktime(0,0,0,date("$start_week[1]"),(date("$start_week[2]")-8),date("$start_week[0]")));
                }
                $period_type="week";
                $width="360";
        }
        else if($period=="tmon")
        {
		$period_type="month";
		$width="840";
		$st_date = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));	
		$end_date = date("Y-m-t");

        }
        else if($period=="lmon")
        {
                $st_date=date("Y-m-d",mktime(0,0,0,date("n")-1,date("1"),date("Y")));
                $end_date = date("Y-m-d",mktime(0, 0, 1, date("n"), 0,date("Y")));
                $period_type="month";
                $start_month=date("d",mktime(0,0,0,date("n"),date("j"),date("Y")));
                if($start_month==1)
                {
                        $st_date=date("Y-m-d",mktime(0,0,0,date("n")-2,date("1"),date("Y")));
                        $end_date = date("Y-m-d",mktime(0, 0, 1, date("n")-1, 0,date("Y")));
                }

                $width="840";
        }
        else
        {
                $curr_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
                $today_date=explode("-",$curr_date);
                $lastday_date=date("Y-m-d",mktime(0,0,0,date("$today_date[1]"),date("$today_date[2]")-1,date("$today_date[0]")));
                $st_date=$lastday_date;
                $end_date=$lastday_date;
                $period_type="yday";
		 $width="250";
        }
        if($period_type=="yday")
                $height="160";
        else
                $height="250";
        $datevalues=array($st_date,$end_date,$period_type,$width,$height);
	$log->debug("Exiting start_end_dates method ...");
        return $datevalues;
}//function ends


/**   Function to get the Graph and vtiger_table format for a particular date
        based upon the period
        * Portions created by vtiger are Copyright (C) vtiger.
        * All Rights Reserved.
        * Contributor(s): ______________________________________..
 */
function Graph_n_table_format($period_type,$date_value)
{
	global $log;
	$log->debug("Entering Graph_n_table_format(".$period_type.",".$date_value.") method ...");
        $date_val=explode("-",$date_value);
        if($period_type=="month")   //to get the vtiger_table format dates
        {
                $table_format=date("j",mktime(0,0,0,date($date_val[1]),(date($date_val[2])),date($date_val[0])));
                $graph_format=date("D",mktime(0,0,0,date($date_val[1]),(date($date_val[2])),date($date_val[0])));
        }
        else if($period_type=="week")
        {
                $table_format=date("d/m",mktime(0,0,0,date($date_val[1]),(date($date_val[2])),date($date_val[0])));
                $graph_format=date("D",mktime(0,0,0,date($date_val[1]),(date($date_val[2])),date($date_val[0])));
        }
        else if($period_type=="yday")
        {
                $table_format=date("j",mktime(0,0,0,date($date_val[1]),(date($date_val[2])),date($date_val[0])));
                $graph_format=$table_format;
        }
        $values=array($graph_format,$table_format);
	$log->debug("Exiting Graph_n_table_format method ...");
        return $values;
}

/** Function to get image count for a given product   
  * @param $id -- product id :: Type integer
  * @returns count -- count:: Type integer
  */

function getImageCount($id)
{
	global $log;
	$log->debug("Entering getImageCount(".$id.") method ...");
	global $adb;
	$image_lists=array();
	$query="select imagename from vtiger_products where productid=?";
	$result=$adb->pquery($query, array($id));
	$imagename=$adb->query_result($result,0,'imagename');
	$image_lists=explode("###",$imagename);
	$log->debug("Exiting getImageCount method ...");
	return count($image_lists);

}

/** Function to get user image for a given user   
  * @param $id -- user id :: Type integer
  * @returns $image_name -- image name:: Type string
  */

function getUserImageName($id)
{
	global $log;
	$log->debug("Entering getUserImageName(".$id.") method ...");
	global $adb;
	$query = "select imagename from vtiger_users where id=?";
	$result = $adb->pquery($query, array($id));
	$image_name = $adb->query_result($result,0,"imagename");
	$log->debug("Inside getUserImageName. The image_name is ".$image_name);
	$log->debug("Exiting getUserImageName method ...");
	return $image_name;

}

/** Function to get all user images for displaying it in listview   
  * @returns $image_name -- image name:: Type array
  */

function getUserImageNames()
{
	global $log;
	$log->debug("Entering getUserImageNames() method ...");
	global $adb;
	$query = "select imagename from vtiger_users where deleted=0";
	$result = $adb->pquery($query, array());
	$image_name=array();
	for($i=0;$i<$adb->num_rows($result);$i++)
	{
		if($adb->query_result($result,$i,"imagename")!='')
			$image_name[] = $adb->query_result($result,$i,"imagename");
	}
	$log->debug("Inside getUserImageNames.");
	if(count($image_name) > 0)
	{
		$log->debug("Exiting getUserImageNames method ...");
		return $image_name;
	}
}

/**   Function to remove the script tag in the contents
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 */
function strip_selected_tags($text, $tags = array())
{
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
    foreach ($tags as $tag){
        if(preg_match_all('/<'.$tag.'[^>]*>(.*)<\/'.$tag.'>/iU', $text, $found)){
            $text = str_replace($found[0],$found[1],$text);
        }
    }

    return $text;
}

/** Function to check whether user has opted for internal mailer
  * @returns $int_mailer -- int mailer:: Type boolean
    */
function useInternalMailer() {
	global $current_user,$adb;
	return $adb->query_result($adb->pquery("select int_mailer from vtiger_mail_accounts where user_id=?", array($current_user->id)),0,"int_mailer");
}

/**
* the function is like unescape in javascript
* added by dingjianting on 2006-10-1 for picklist editor
*/
function utf8RawUrlDecode ($source) {
    global $default_charset;
    $decodedStr = "";
    $pos = 0;
    $len = strlen ($source);
    while ($pos < $len) {
        $charAt = substr ($source, $pos, 1);
        if ($charAt == '%') {
            $pos++;
            $charAt = substr ($source, $pos, 1);
            if ($charAt == 'u') {
                // we got a unicode character
                $pos++;
                $unicodeHexVal = substr ($source, $pos, 4);
                $unicode = hexdec ($unicodeHexVal);
                $entity = "&#". $unicode . ';';
                $decodedStr .= utf8_encode ($entity);
                $pos += 4;
            }
            else {
                // we have an escaped ascii character
                $hexVal = substr ($source, $pos, 2);
                $decodedStr .= chr (hexdec ($hexVal));
                $pos += 2;
            }
        } else {
            $decodedStr .= $charAt;
            $pos++;
        }
    }
    if(strtolower($default_charset) == 'utf-8')
	    return html_to_utf8($decodedStr);
    else
	    return $decodedStr;
    //return html_to_utf8($decodedStr);
}

/**
*simple HTML to UTF-8 conversion:
*/
function html_to_utf8 ($data)
{
	return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", '_html_to_utf8("\\1")', $data);
}

function _html_to_utf8 ($data)
{
	if ($data > 127)
	{
		$i = 5;
		while (($i--) > 0)
		{
			if ($data != ($a = $data % ($p = pow(64, $i))))
			{
				$ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p));
				for ($i; $i > 0; $i--)
					$ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p));
				break;
			}
		}
	}
	else
		$ret = "&#$data;";
	return $ret;
}

// Return Question mark
function _questionify($v){
	return "?";
}

/**
* Function to generate question marks for a given list of items
*/
function generateQuestionMarks($items_list) {
	// array_map will call the function specified in the first parameter for every element of the list in second parameter
	if (is_array($items_list)) {
		return implode(",", array_map("_questionify", $items_list));	
	} else {	
		return implode(",", array_map("_questionify", explode(",", $items_list)));
	}
}

/**
* Function to find the UI type of a field based on the uitype id
*/
function is_uitype($uitype, $reqtype) {
	$ui_type_arr = array(
		'_date_' => array(5, 6, 23, 70),
		'_picklist_' => array(15, 16, 52, 53, 54, 55, 59, 62, 63, 66, 68, 76, 77, 78, 80, 98, 101, 111, 115, 357),
		'_users_list_' => array(52),
	);

	if ($ui_type_arr[$reqtype] != null) {
		if (in_array($uitype, $ui_type_arr[$reqtype])) {
			return true;
		}
	}
	return false;
}
/**
 * Function to escape quotes
 * @param $value - String in which single quotes have to be replaced.
 * @return Input string with single quotes escaped.
 */
function escape_single_quotes($value) {
	if (isset($value)) $value = str_replace("'", "\'", $value);	
	return $value;
}

/**
 * Function to format the input value for SQL like clause.
 * @param $str - Input string value to be formatted.
 * @param $flag - By default set to 0 (Will look for cases %string%). 
 *                If set to 1 - Will look for cases %string.
 *                If set to 2 - Will look for cases string%.
 * @return String formatted as per the SQL like clause requirement
 */
function formatForSqlLike($str, $flag=0) {
	if (isset($str)) {
		$str = str_replace('%', '\%', $str);
		$str = str_replace('_', '\_', $str);
		
		if ($flag == 0) {
			$str = '%'. $str .'%';			
		} elseif ($flag == 1) {
			$str = '%'. $str;
		} elseif ($flag == 2) {
			$str = $str .'%';
		} 
	}
	return mysql_real_escape_string($str);
}

/**
 * Get Current Module (global variable or from request)
 */
function getCurrentModule($perform_set=false) {
	global $currentModule;
	if(isset($currentModule)) return $currentModule;

	// Do some security check and return the module information
	if(isset($_REQUEST['module']))
	{
		$is_module = false;
		$module = $_REQUEST['module'];
		$dir = @scandir($root_directory."modules");
		$temp_arr = Array("CVS","Attic");
		$res_arr = @array_intersect($dir,$temp_arr);
		if(count($res_arr) == 0  && !ereg("[/.]",$module)) {
			if(@in_array($module,$dir))
				$is_module = true;
		}

		if($is_module) {
			if($perform_set) $currentModule = $module;
			return $module;
		}
	}
	return null;
}


/**
 * Set the language strings.
 */
function setCurrentLanguage($active_module=null) {
	global $current_language, $default_language, $app_strings, $app_list_strings, $mod_strings, $currentModule;

	if($active_module==null) {
		if (!isset($currentModule))
			$active_module = getCurrentModule();
		else
			$active_module = $currentModule;
	}

	if(isset($_SESSION['authenticated_user_language']) && $_SESSION['authenticated_user_language'] != '')
	{
		$current_language = $_SESSION['authenticated_user_language'];
	}
	else
	{
		$current_language = $default_language;
	}

	//set module and application string arrays based upon selected language
	if (!isset($app_strings))
		$app_strings = return_application_language($current_language);
	if (!isset($app_list_strings))
		$app_list_strings = return_app_list_strings_language($current_language);
	if (!isset($mod_strings) && isset($active_module))
		$mod_strings = return_module_language($current_language, $active_module);
}

/**	Function used to get all the picklists and their values for a module
	@param string $module - Module name to which the list of picklists and their values needed
	@return array $fieldlists - Array of picklists and their values
**/
function getAccessPickListValues($module)
{
	global $adb, $log;
	global $current_user;
	$log->debug("Entering into function getAccessPickListValues($module)");
	
	$id = getTabid($module);
	$query = "select fieldname,columnname,fieldid,fieldlabel,tabid,uitype from vtiger_field where tabid = ? and uitype in ('15','111','33','55')";
	$result = $adb->pquery($query, array($id));
	
	$roleid = $current_user->roleid;
	$subrole = getRoleSubordinates($roleid);
	
	if(count($subrole)> 0)
	{
		$roleids = $subrole;
		array_push($roleids, $roleid);
	}
	else
	{
		$roleids = $roleid;
	}

	$temp_status = Array();
	for($i=0;$i < $adb->num_rows($result);$i++)
	{
		$fieldname = $adb->query_result($result,$i,"fieldname");
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		$columnname = $adb->query_result($result,$i,"columnname");
		$tabid = $adb->query_result($result,$i,"tabid");
		$uitype = $adb->query_result($result,$i,"uitype");

		$keyvalue = $columnname;
		$fieldvalues = Array();
		if (count($roleids) > 1)
		{
			$mulsel="select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where roleid in (\"". implode($roleids,"\",\"") ."\") and picklistid in (select picklistid from vtiger_$fieldname) order by sortid asc";
		}
		else
		{
			$mulsel="select distinct $fieldname from vtiger_$fieldname inner join vtiger_role2picklist on vtiger_role2picklist.picklistvalueid = vtiger_$fieldname.picklist_valueid where roleid ='".$roleid."' and picklistid in (select picklistid from vtiger_$fieldname) order by sortid asc";
		}
		if($fieldname != 'firstname')
			$mulselresult = $adb->query($mulsel);
		for($j=0;$j < $adb->num_rows($mulselresult);$j++)
		{
			$fieldvalues[] = $adb->query_result($mulselresult,$j,$fieldname);
		}
		$field_count = count($fieldvalues);
		if($uitype == 111 && $field_count > 0 && ($fieldname == 'taskstatus' || $fieldname == 'eventstatus'))
		{
			$temp_count =count($temp_status[$keyvalue]);
			if($temp_count > 0)
			{
				for($t=0;$t < $field_count;$t++)
				{
					$temp_status[$keyvalue][($temp_count+$t)] = $fieldvalues[$t];
				}
				$fieldvalues = $temp_status[$keyvalue];
			}
			else
				$temp_status[$keyvalue] = $fieldvalues;
		}
		if($uitype == 33)
			$fieldlists[1][$keyvalue] = $fieldvalues;
		else if($uitype == 55 && $fieldname == 'salutationtype')
			$fieldlists[$keyvalue] = $fieldvalues;
		else if($uitype == 15 || $uitype == 111)
			$fieldlists[$keyvalue] = $fieldvalues;
	}
	$log->debug("Exit from function getAccessPickListValues($module)");

	return $fieldlists;
}

//Added to check database charset and $default_charset are set to UTF8.
//If both are not set to be UTF-8, Then we will show an alert message.
function check_db_utf8_support($conn) 
{ 
	$dbvarRS = &$conn->query("show variables like '%_database' "); 
	$db_character_set = null; 
	$db_collation_type = null; 
	while(!$dbvarRS->EOF) { 
		$arr = $dbvarRS->FetchRow(); 
		$arr = array_change_key_case($arr); 
		switch($arr['variable_name']) { 
		case 'character_set_database' : $db_character_set = $arr['value']; break; 
		case 'collation_database'     : $db_collation_type = $arr['value']; break; 
		}
		// If we have all the required information break the loop. 
		if($db_character_set != null && $db_collation_type != null) break; 
	} 
	return (stristr($db_character_set, 'utf8') && stristr($db_collation_type, 'utf8')); 
}

function get_db_charset($conn) {
	$dbvarRS = &$conn->query("show variables like '%_database' "); 
	$db_character_set = null; 
	while(!$dbvarRS->EOF) { 
		$arr = $dbvarRS->FetchRow(); 
		$arr = array_change_key_case($arr); 
		if($arr['variable_name'] == 'character_set_database') {
			$db_character_set = $arr['value']; 
			break;
		}
	}	
	return $db_character_set;
}

function get_config_status() {
	global $default_charset;
	if(strtolower($default_charset) == 'utf-8')	
		$config_status=1;
	else
		$config_status=0;
	return $config_status;
}

function getMigrationCharsetFlag() {
	global $adb;
	
	if(!$adb->isPostgres())
		$db_status=check_db_utf8_support($adb);
	$config_status=get_config_status();	
	
	if ($db_status == $config_status) {
		if ($db_status == 1) { // Both are UTF-8
			$db_migration_status = MIG_CHARSET_PHP_UTF8_DB_UTF8;
		} else { // Both are Non UTF-8
			$db_migration_status = MIG_CHARSET_PHP_NONUTF8_DB_NONUTF8;		
		}
		} else {
			if ($db_status == 1) { // Database charset is UTF-8 and CRM charset is Non UTF-8
				$db_migration_status = MIG_CHARSET_PHP_NONUTF8_DB_UTF8;
		} else { // Database charset is Non UTF-8 and CRM charset is UTF-8
			$db_migration_status = MIG_CHARSET_PHP_UTF8_DB_NONUTF8;		
		}	
	}
	return $db_migration_status;
}

/************ Function to convert a given time string to Minutes **************/
function ConvertToMinutes($time_string)
{
	$interval = split(' ', $time_string);
	$interval_minutes = intval($interval[0]);
	$interval_string = strtolower($interval[1]);
	if($interval_string == 'hour' || $interval_string == 'hours')
	{
		$interval_minutes = $interval_minutes * 60;
	}
	elseif($interval_string == 'day' || $interval_string == 'days')
	{
		$interval_minutes = $interval_minutes * 1440;
	}		
	return $interval_minutes;
}

//added to find duplicates
/** To get the converted record values which have to be display in duplicates merging tpl*/
function getRecordValues($id_array,$module)
{
	global $adb,$current_user;
	global $app_strings;
	$tabid=getTabid($module);	
	$query="select fieldname,fieldlabel from vtiger_field where tabid=".$tabid." and fieldname  not in ('createdtime','modifiedtime')";
	$result=$adb->query($query);
	$no_rows=$adb->num_rows($result);

	for($i=0;$i<$no_rows;$i++)
		{
			$fld_name=$adb->query_result($result,$i,"fieldname");
			if(getFieldVisibilityPermission($module,$current_user->id,$fld_name) == '0')
				$fld_array []= $fld_name;
	
		}
	$js_arr = $fld_array;
	$focus = new $module();
	if(isset($id_array) && $id_array !='') 
	{      
		foreach($id_array as $value)
			{
				$focus->id=$value;
				$focus->retrieve_entity_info($value,$module);
				$field_values[]=$focus->column_fields;
			
			}
	}
	$tabid=getTabid($module);	
	$query="select fieldname,uitype,fieldlabel from vtiger_field where tabid=".$tabid." and fieldname not in ('createdtime','modifiedtime')" ;

	$result=$adb->query($query);
	$no_rows=$adb->num_rows($result);
	$labl_array=array();
	$value_pair = array();
	$c = 0;
	for($i=0;$i<$no_rows;$i++)
	{
		$fld_name=$adb->query_result($result,$i,"fieldname");
		$fld_label=$adb->query_result($result,$i,"fieldlabel");
		$ui_type=$adb->query_result($result,$i,"uitype");
		
		if(getFieldVisibilityPermission($module,$current_user->id,$fld_name) == '0')
		{
			$record_values[$c][$fld_label] = Array();
			$ui_value[]=$ui_type;
			for($j=0;$j < count($field_values);$j++)
			{
				
				if($ui_type ==56)
				{
					if($field_values[$j][$fld_name] == 0)
						$value_pair['disp_value']=$app_strings['no'];
					else
						$value_pair['disp_value']=$app_strings['yes'];
					
				}
				elseif($ui_type == 51 || $ui_type == 50)
				{
					$entity_id=$field_values[$j][$fld_name];
					if($module !='Products')
						$entity_name=getAccountName($entity_id);
					else
						$entity_name=getProductName($entity_id);
					
					$value_pair['disp_value']=$entity_name;	
				}	
				elseif($ui_type == 53)
				{
					$user_id=$field_values[$j][$fld_name];
					$username=getUserName($user_id);
					$group_info=getGroupName($field_values[$j]['record_id'],$module);
					$groupname = $group_info[0];
					$groupid = $group_info[1];
					if($user_id != 0)
						$value_pair['disp_value']=$username;
					else
						$value_pair['disp_value']=$groupname;
				}				
				elseif($ui_type ==57)
				{
					$contact_id= $field_values[$j][$fld_name];		
					if($contact_id != '')
						{
							$contactname=getContactName($contact_id);
						}
						
					$value_pair['disp_value']=$contactname;
				}
				elseif($ui_type == 75 || $ui_type ==81)
				{
					$vendor_id=$field_values[$j][$fld_name];
					if($vendor_id != '')
						{
							$vendor_name=getVendorName($vendor_id);
						}	
					$value_pair['disp_value']=$vendor_name;
				}	
						
				elseif($ui_type == 52)
				{
					$user_id = $field_values[$j][$fld_name];
					$user_name=getUserName($user_id);
					$value_pair['disp_value']=$user_name;

				}
				elseif($ui_type ==68)
				{
					$parent_id = $field_values[$j][$fld_name];
					$value_pair['disp_value'] = getAccountName($parent_id);
					if($value_pair['disp_value'] == '' || $value_pair['disp_value'] == NULL)
						$value_pair['disp_value'] = getContactName($parent_id);
					
				}
				elseif($ui_type ==59)
				{
					$product_name=getProductName($field_values[$j][$fld_name]);
					if($product_name != '')
						$value_pair['disp_value']=$product_name;
					else $value_pair['disp_value']='';
				}
				elseif($ui_type==58)
				{
					$campaign_name=getCampaignName($field_values[$j][$fld_name]);
					if($campaign_name != '')
						$value_pair['disp_value']=$campaign_name;
					else $value_pair['disp_value']='';
				}
				else
					$value_pair['disp_value']=$field_values[$j][$fld_name];
				$value_pair['org_value'] = $field_values[$j][$fld_name];

				array_push($record_values[$c][$fld_label],$value_pair);
			}
			$c++;
		}

	}
	$parent_array[0]=$record_values;
	$parent_array[1]=$js_arr;
	$parent_array[2]=$fld_array;
	return $parent_array;
}

/** To save the parent record after merging with child records*/
function mergeSave($module,$return_module,$parent_tab,$merge_id,$recordids)
{
	
	global $adb;
	global $app_strings;
	$result =  $adb->query("select count(*) count from vtiger_crmentity where crmid='".$merge_id."' and deleted=0");
	$count = $adb->query_result($result,0,count);
	if($count != 0)
	{	
		$focus = new $module();	
		$focus->mode="edit";
		setObjectValuesFromRequest($focus);
		$focus->save($module);
		$rec_values=$focus->column_fields;
		
		$del_value=explode(",",$recordids,-1);
		$offset = array_search($merge_id,$del_value);
		unset($del_value[$offset]);
		save_relatedrecords($module,$del_value,$merge_id);
		foreach($del_value as $value)
			{
				DeleteEntity($_REQUEST['module'],$_REQUEST['return_module'],$focus,$value,"");
			}
		
		return $rec_values;
	}
}

/** Specifying related tables for each and every module which supports duplicates handling*/
function save_relatedrecords($module,$del_ids,$recordid)
{
	$related_module_table_array = Array (
		"Accounts" => Array("Contacts"=>"vtiger_contactdetails","Potentials"=>"vtiger_potential","Quotes"=>"vtiger_quotes","SalesOrder"=>"vtiger_salesorder","Invoice"=>"vtiger_invoice","Activities"=>"vtiger_seactivityrel","Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel","HelpDesk"=>"vtiger_troubletickets","Products"=>"vtiger_seproductsrel"),

		"Contacts" => Array("Potentials"=>"vtiger_contpotentialrel","Activities"=>"vtiger_cntactivityrel","Emails"=>"vtiger_seactivityrel","HelpDesk"=>"vtiger_troubletickets","Quotes"=>"vtiger_quotes","PurchaseOrder"=>"vtiger_purchaseorder","SalesOrder"=>"vtiger_salesorder","Products"=>"vtiger_seproductsrel","Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel","Campaigns"=>"vtiger_campaigncontrel"),

		"Leads" => Array("Activities"=>"vtiger_seactivityrel","Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel","Products"=>"vtiger_seproductsrel","Campaigns"=>"vtiger_campaignleadrel"),

		"Products"=> Array("HelpDesk"=>"vtiger_troubletickets","Products"=>"vtiger_seproductsrel","Attachments"=>"vtiger_seattachmentsrel","Quotes"=>"vtiger_inventoryproductrel","PurchaseOrder"=>"vtiger_inventoryproductrel","SalesOrder"=>"vtiger_inventoryproductrel","Invoice"=>"vtiger_inventoryproductrel","PriceBooks"=>"vtiger_pricebookproductrel","Leads"=>"vtiger_seproductsrel","Accounts"=>"vtiger_seproductsrel","Potentials"=>"vtiger_seproductsrel","Contacts"=>"vtiger_seproductsrel"),
		
		"HelpDesk"=> Array("Activities"=>"vtiger_seactivityrel","Attachments"=>"vtiger_seattachmentsrel"),
		
		"Potentials"=>Array("Activities"=>"vtiger_seactivityrel","Contacts"=>"vtiger_contactdetails","Products"=>"vtiger_seproductsrel","Attachments"=>"vtiger_seattachmentsrel","Quotes"=>"vtiger_quotes","SalesOrder"=>"vtiger_salesorder"),
		
		"Vendors"=>Array("Products"=>"vtiger_seproductsrel","PurchaseOrder"=>"vtiger_purchaseorder","Contacts"=>"vtiger_vendorcontactrel")
	);
	$func = "save_".$module."_related_entries";
	$func($related_module_table_array[$module],$del_ids,$recordid);
}

/** Function to merge the related entries of accounts module*/
function save_Accounts_related_entries($rel_table_arr,$del_ids,$saved_id)
{
	global $adb;
	$tbl_field_arr = Array("vtiger_contactdetails"=>"contactid","vtiger_potential"=>"potentialid","vtiger_quotes"=>"quoteid","vtiger_salesorder"=>"salesorderid","vtiger_invoice"=>"invoiceid","vtiger_seactivityrel"=>"activityid","vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_troubletickets"=>"ticketid","vtiger_seproductsrel"=>"productid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "Contacts" || $rel_module == "Potentials" || $rel_module == "Quotes" || $rel_module == "SalesOrder" || $rel_module == "vtiger_invoice")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where accountid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set accountid='".$saved_id."' where $id_field='".$id_field_value."'");
					}
				}
			}
			else if($rel_module == "Activities" || $rel_module == "Documents" || $rel_module == "Attachments")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");
					}
				}
			}
			else if($rel_module == "Products")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field,setype) values($saved_id,$id_field_value,'Accounts')");
					}
				}
			}
			else if($rel_module == "HelpDesk")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where parent_id='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set parent_id=$saved_id where $id_field=$id_field_value");
					}
				}
			}

		}
	}
}

/** Function to save the related entries of contacts during duplicate merge */
function save_Contacts_related_entries($rel_table_arr,$del_ids,$saved_id)
{
	global $adb;
	$tbl_field_arr = Array("vtiger_contpotentialrel"=>"potentialid","vtiger_cntactivityrel"=>"activityid","vtiger_seactivityrel"=>"activityid","vtiger_troubletickets"=>"ticketid","vtiger_quotes"=>"quoteid","vtiger_salesorder"=>"salesorderid","vtiger_invoice"=>"invoiceid","vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_troubletickets"=>"ticketid","vtiger_seproductsrel"=>"productid","vtiger_campaigncontrel"=>"campaignid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "Quotes" || $rel_module == "SalesOrder" || $rel_module == "Invoice")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where contactid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set contactid='".$saved_id."' where $id_field='".$id_field_value."'");
					}
				}
			}
			else if($rel_module == "Activities" || $rel_module == "Potentials" || $rel_module == "Campaigns")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where contactid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$check_res = $adb->query("select * from $rel_table where $id_field=$id_field_value and contactid=$saved_id");
						$count = $adb->num_rows($check_res);
					        if($count == 0)	
							$adb->query("insert into $rel_table (contactid,$id_field) values($saved_id,$id_field_value)");		
					}			
				}
			}
			else if($rel_module == "Documents" || $rel_module == "Emails" || $rel_module == "Attachments")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");

					}
				}
			}
			else if($rel_module == "Products")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field,setype) values($saved_id,$id_field_value,'Contacts')");
					}
				}
			}
			else if($rel_module == "HelpDesk")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where parent_id='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set parent_id=$saved_id where $id_field=$id_field_value");
					}
				}
			}
		}
	}
}

/** Function to save the related entries of leads during duplicate merge */
function save_Leads_related_entries($rel_table_arr,$del_ids,$saved_id) 
{
	global $adb;
	$tbl_field_arr = Array("vtiger_seactivityrel"=>"activityid","vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_seproductsrel"=>"productid","vtiger_campaignleadrel"=>"campaignid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "Activities" || $rel_module == "Documents" || $rel_module == "Attachments")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
						{
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");
							if($rel_module == "Activities")
								$adb->query("delete from vtiger_seactivityrel where crmid = $id_tobe_del");
						}

					}
				}
			}
			else if($rel_module == "Products")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field,setype) values($saved_id,$id_field_value,'Leads')");
					}
				}
			}
			else if($rel_module == "Campaigns")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where leadid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$check_res = $adb->query("select * from $rel_table where $id_field=$id_field_value and leadid=$saved_id");
						$count = $adb->num_rows($check_res);
						if($count == 0)
							$adb->query("insert into $rel_table (leadid,$id_field) values($saved_id,$id_field_value)");
					}
				}
			}
		}
	}	
}

/** Function to save the related entries of Products during duplicate merge */
function save_Products_related_entries($rel_table_arr,$del_ids,$saved_id)
{
	global $adb;
	$tbl_field_arr = Array("vtiger_troubletickets"=>"ticketid","vtiger_seproductsrel"=>"crmid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_inventoryproductrel"=>"id","vtiger_pricebookproductrel"=>"pricebookid","vtiger_seproductsrel"=>"crmid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "HelpDesk")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where product_id='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set product_id=$saved_id where $id_field=$id_field_value");
					}
				}
			}
			else if($rel_module == "Quotes" || $rel_module == "SalesOrder" || $rel_module == "Invoice")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select distinct $id_field from $rel_table where productid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set productid=$saved_id where $id_field=$id_field_value");
					}
				}
			}
			else if($rel_module == "Documents" || $rel_module == "Attachments")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");
					}
				}
			}
			else if($rel_module == "Leads" || $rel_module == "Accounts" || $rel_module == "Potentials" || $rel_module == "Contacts")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where productid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$check_res = $adb->query("select * from $rel_table where $id_field=$id_field_value and productid=$saved_id");
						$count = $adb->num_rows($check_res);
						if($count == 0)
							$adb->query("insert into $rel_table (productid,$id_field,setype) values($saved_id,$id_field_value,'$rel_module')");
					}
				}
			}
			else if($rel_module == "PriceBooks")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field,listprice from $rel_table where productid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$listprice = $adb->query_result($sel_result,$i,"listprice");
						$check_res = $adb->query("select * from $rel_table where $id_field=$id_field_value and productid=$saved_id");
						$count = $adb->num_rows($check_res);
						if($count == 0)
							$adb->query("insert into $rel_table ($id_field,productid,listprice) values($id_field_value,$saved_id,$listprice)");
					}
				}	
			}
		}
	}
	
}

//For potentials,troubletickets and vendors -- Pavani
/** Function to save the related entries of Trouble Tickets during duplicate merge */
function save_HelpDesk_related_entries($rel_table_arr,$del_ids,$saved_id)
{
	global $adb;
	$tbl_field_arr = Array("vtiger_seactivityrel"=>"activityid","vtiger_seattachmentsrel"=>"attachmentsid");
	foreach($del_ids as $id_tobe_del)
	{
		if($rel_module == "Documents" || $rel_module == "Attachments")
		{
			$id_field = $tbl_field_arr[$rel_table];
			$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
			$res_cnt = $adb->num_rows($sel_result);
			if($res_cnt > 0)
			{
				for($i=0;$i<$res_cnt;$i++)
				{
					$id_field_value = $adb->query_result($sel_result,$i,$id_field);
					if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
						$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");
				}
			}
		}
	}	
}

/** Function to save the related entries of Potentials during duplicate merge */
function save_Potentials_related_entries($rel_table_arr,$del_ids,$saved_id) 
{
	global $adb;
	$tbl_field_arr = Array("vtiger_seactivityrel"=>"activityid","vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid","vtiger_seproductsrel"=>"productid","vtiger_contactdetails"=>"contactid","vtiger_quotes"=>"quoteid","vtiger_salesorder"=>"salesorderid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "Quotes" || $rel_module == "SalesOrder")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where potentialid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set potentialid='".$saved_id."' where $id_field='".$id_field_value."'");
					}
				}
			}
			else if($rel_module == "Activities" || $rel_module == "Documents" || $rel_module == "Attachments" || $rel_module == "Products")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");

					}
				}
			}
		}
	}
}

/** Function to save the related entries of Vendors during duplicate merge */
function save_Vendors_related_entries($rel_table_arr,$del_ids,$saved_id) 
{
	global $adb;
	$tbl_field_arr = Array("vtiger_seproductsrel"=>"productid","vtiger_vendorcontactrel"=>"contactid","vtiger_purchaseorder"=>"purchaseorderid");
	foreach($del_ids as $id_tobe_del)
	{
		foreach($rel_table_arr as $rel_module=>$rel_table)
		{
			if($rel_module == "Contacts" || $rel_module == "PurchaseOrder")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where vendorid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						$adb->query("update $rel_table set vendorid='".$saved_id."' where $id_field='".$id_field_value."'");
					}
				}
			}
			else if($rel_module == "Products")
			{
				$id_field = $tbl_field_arr[$rel_table];
				$sel_result =  $adb->query("select $id_field from $rel_table where crmid='".$id_tobe_del."'");
				$res_cnt = $adb->num_rows($sel_result);
				if($res_cnt > 0)
				{
					for($i=0;$i<$res_cnt;$i++)
					{
						$id_field_value = $adb->query_result($sel_result,$i,$id_field);
						if(!is_related($rel_table,$id_field,$id_field_value,$saved_id))
							$adb->query("insert into $rel_table (crmid,$id_field) values($saved_id,$id_field_value)");

					}
				}
			}
		}
	}
}

/** Function to check whether the relationship entries are exist or not on elationship tables */
function is_related($relation_table,$crm_field,$related_module_id,$crmid)
{
	global $adb;
	$check_res = $adb->query("select * from $relation_table where $crm_field=$related_module_id and crmid=$crmid");
	$count = $adb->num_rows($check_res);
	if($count > 0)
		return true;
	else
		return false;	
}

/** Function to get a to find duplicates in a particular module*/
function getDuplicateQuery($module,$field_values,$ui_type_arr)
{
	global $current_user;
	$tbl_col_fld = explode(",", $field_values);
	$i=0;
	foreach($tbl_col_fld as $val) {
		list($tbl[$i], $cols[$i], $fields[$i]) = explode(".", $val);
		$tbl_cols[$i] = $tbl[$i]. "." . $cols[$i];
		$i++;
	}
	$table_cols = implode(",",$tbl_cols);
	$sec_parameter = getSecParameterforMerge($module);
	if( stristr($_REQUEST['action'],'ImportStep') || ($_REQUEST['action'] == $_REQUEST['module'].'Ajax' && $_REQUEST['current_action'] == 'ImportSteplast'))
	{	
		if($module == 'Contacts')
		{
			$ret_arr = get_special_on_clause($table_cols);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			$nquery="select vtiger_contactdetails.contactid as recordid,
					vtiger_users_last_import.deleted,$table_cols FROM vtiger_contactdetails
					INNER JOIN vtiger_crmentity
						ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid
					INNER JOIN vtiger_contactaddress
						ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					INNER JOIN vtiger_contactsubdetails
						ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
					LEFT JOIN vtiger_users_last_import
						ON vtiger_users_last_import.bean_id=vtiger_contactdetails.contactid
					LEFT JOIN vtiger_account
						ON vtiger_account.accountid=vtiger_contactdetails.accountid
					LEFT JOIN vtiger_customerdetails
						ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
					LEFT JOIN vtiger_contactgrouprelation
						ON vtiger_contactgrouprelation.contactid = vtiger_contactdetails.contactid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_contactgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
					INNER JOIN (select $select_clause from vtiger_contactdetails t
							INNER JOIN vtiger_crmentity crm
								ON crm.crmid=t.contactid
							INNER JOIN vtiger_contactaddress addr
								ON t.contactid = addr.contactaddressid
							INNER JOIN vtiger_contactsubdetails subd
								ON addr.contactaddressid = subd.contactsubscriptionid
    						LEFT JOIN vtiger_account acc
		                        ON acc.accountid=t.accountid
							LEFT JOIN vtiger_customerdetails custd
						ON custd.customerid=t.contactid
							WHERE crm.deleted=0 group by $select_clause  HAVING COUNT(*)>1) as temp
						ON ".get_on_clause($field_values,$ui_type_arr,$module)."
					WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_contactdetails.contactid ASC";
			
		}

	else if($module == 'Accounts')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];	
			$nquery="SELECT vtiger_account.accountid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_account
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_account.accountid
				INNER JOIN vtiger_accountbillads
					ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
				INNER JOIN vtiger_accountshipads
					ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
				LEFT JOIN vtiger_users_last_import
                    ON vtiger_users_last_import.bean_id=vtiger_account.accountid
				LEFT JOIN vtiger_accountgrouprelation
					ON vtiger_account.accountid = vtiger_accountgrouprelation.accountid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_accountgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
				INNER JOIN (select $select_clause from vtiger_account t
							INNER JOIN vtiger_crmentity crm
								ON crm.crmid=t.accountid
							INNER JOIN vtiger_accountbillads badd
								ON t.accountid = badd.accountaddressid
							INNER JOIN vtiger_accountshipads sadd
								ON t.accountid = sadd.accountaddressid
							WHERE crm.deleted=0 group by $select_clause HAVING COUNT(*)>1) as temp 
					ON ".get_on_clause($field_values,$ui_type_arr,$module)."
				WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_account.accountid ASC";
				
		}
	else if($module == 'Leads')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			$nquery="select vtiger_leaddetails.leadid as recordid, 
					vtiger_users_last_import.deleted,$table_cols FROM vtiger_leaddetails 
					INNER JOIN vtiger_crmentity 
						ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
					INNER JOIN vtiger_leadsubdetails 
						ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid 
					INNER JOIN vtiger_leadaddress 
						ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid 
					LEFT JOIN vtiger_leadgrouprelation
						ON vtiger_leaddetails.leadid = vtiger_leadgrouprelation.leadid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
					LEFT JOIN vtiger_users_last_import 
						ON vtiger_users_last_import.bean_id=vtiger_leaddetails.leadid 
					INNER JOIN (select $select_clause from vtiger_leaddetails t 
							INNER JOIN vtiger_crmentity crm 
								ON crm.crmid=t.leadid 
							INNER JOIN vtiger_leadsubdetails subd
								ON subd.leadsubscriptionid = t.leadid 
							INNER JOIN vtiger_leadaddress addr 
								ON addr.leadaddressid = subd.leadsubscriptionid 
							WHERE crm.deleted=0 and t.converted = 0 group by $select_clause HAVING COUNT(*)>1) as temp 
						ON ".get_on_clause($field_values,$ui_type_arr,$module)." 
				WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted = 0 $sec_parameter ORDER BY $table_cols,vtiger_leaddetails.leadid ASC";
				
		}	
	else if($module == 'Products')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			
			$nquery="SELECT vtiger_products.productid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_products
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_products.productid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_products.productid
				INNER JOIN (select $select_clause from vtiger_products t
						INNER JOIN vtiger_crmentity crm
						ON crm.crmid=t.productid
						WHERE crm.deleted=0 group by $select_clause HAVING COUNT(*)>1) as temp
					ON ".get_on_clause($field_values,$ui_type_arr,$module)."
				WHERE vtiger_crmentity.deleted=0 ORDER BY $table_cols,vtiger_products.productid ASC";
							
		}	
		else if($module == 'HelpDesk')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			$nquery="SELECT vtiger_troubletickets.ticketid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_troubletickets
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
				LEFT JOIN vtiger_account 
					ON vtiger_account.accountid = vtiger_troubletickets.parent_id 
				LEFT JOIN vtiger_contactdetails 
					ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
				LEFT JOIN vtiger_ticketgrouprelation
					ON vtiger_troubletickets.ticketid = vtiger_ticketgrouprelation.ticketid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_crmentity.smownerid = vtiger_users.id
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_troubletickets.ticketid
				LEFT JOIN vtiger_attachments
					ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
				LEFT JOIN vtiger_ticketcomments
					ON vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid
				INNER JOIN (select $select_clause from vtiger_troubletickets t
						INNER JOIN vtiger_crmentity crm
						ON crm.crmid=t.ticketid
						LEFT JOIN vtiger_account acc
							ON acc.accountid = t.parent_id 
						LEFT JOIN vtiger_contactdetails contd
							ON contd.contactid = t.parent_id
						WHERE crm.deleted=0 group by $select_clause HAVING COUNT(*)>1) as temp
					ON ".get_on_clause($field_values,$ui_type_arr,$module)."
				WHERE vtiger_crmentity.deleted=0". $sec_parameter ." ORDER BY $table_cols,vtiger_troubletickets.ticketid ASC";
											
		}
		else if($module == 'Potentials')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			$nquery="SELECT vtiger_potential.potentialid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_potential
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_potential.potentialid
				LEFT JOIN vtiger_potentialgrouprelation
					ON vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_potential.potentialid
				INNER JOIN (select $select_clause from vtiger_potential t
						INNER JOIN vtiger_crmentity crm
						ON crm.crmid=t.potentialid
						WHERE crm.deleted=0 group by $select_clause HAVING COUNT(*)>1) as temp
					ON ".get_on_clause($field_values,$ui_type_arr,$module)."
				WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_potential.potentialid ASC";
							
		}	
		else if($module == 'Vendors')
		{
			$ret_arr = get_special_on_clause($field_values);
			$select_clause = $ret_arr['sel_clause'];
			$on_clause = $ret_arr['on_clause'];
			$nquery="SELECT vtiger_vendor.vendorid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_vendor
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_vendor.vendorid
				INNER JOIN (select $select_clause from vtiger_vendor t
						INNER JOIN vtiger_crmentity crm
						ON crm.crmid=t.vendorid
						WHERE crm.deleted=0 group by $select_clause HAVING COUNT(*)>1) as temp
					ON ".get_on_clause($field_values,$ui_type_arr,$module)."
				WHERE vtiger_crmentity.deleted=0 ORDER BY $table_cols,vtiger_vendor.vendorid ASC";
							
		}		
	}
	else
	{
		
		if($module == 'Contacts')
		{			
			$nquery = "SELECT vtiger_contactdetails.contactid AS recordid,
					vtiger_users_last_import.deleted,".$table_cols."
					FROM vtiger_contactdetails
					INNER JOIN vtiger_crmentity
						ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid
					INNER JOIN vtiger_contactaddress
						ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					INNER JOIN vtiger_contactsubdetails
						ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
					LEFT JOIN vtiger_users_last_import
						ON vtiger_users_last_import.bean_id=vtiger_contactdetails.contactid
					LEFT JOIN vtiger_account
						ON vtiger_account.accountid=vtiger_contactdetails.accountid
					LEFT JOIN vtiger_customerdetails
						ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
					LEFT JOIN vtiger_contactgrouprelation
						ON vtiger_contactgrouprelation.contactid = vtiger_contactdetails.contactid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_contactgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
					INNER JOIN (SELECT $table_cols
							FROM vtiger_contactdetails
							INNER JOIN vtiger_crmentity
								ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
							INNER JOIN vtiger_contactaddress
								ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
							INNER JOIN vtiger_contactsubdetails
								ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
							LEFT JOIN vtiger_account
								ON vtiger_account.accountid=vtiger_contactdetails.accountid
							LEFT JOIN vtiger_customerdetails
								ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
							LEFT JOIN vtiger_contactgrouprelation
						ON vtiger_contactgrouprelation.contactid = vtiger_contactdetails.contactid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_contactgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted=0 $sec_parameter
							GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
						ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
	                                WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_contactdetails.contactid ASC";
				
		}
		else if($module == 'Accounts')
		{
			$nquery="SELECT vtiger_account.accountid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_account
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_account.accountid
				INNER JOIN vtiger_accountbillads
					ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
				INNER JOIN vtiger_accountshipads
					ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_account.accountid
				LEFT JOIN vtiger_accountgrouprelation
					ON vtiger_account.accountid = vtiger_accountgrouprelation.accountid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_accountgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
				INNER JOIN (SELECT $table_cols
					FROM vtiger_account
					INNER JOIN vtiger_crmentity
						ON vtiger_crmentity.crmid = vtiger_account.accountid
					INNER JOIN vtiger_accountbillads
					ON vtiger_account.accountid = vtiger_accountbillads.accountaddressid
					INNER JOIN vtiger_accountshipads
						ON vtiger_account.accountid = vtiger_accountshipads.accountaddressid 
					LEFT JOIN vtiger_accountgrouprelation
						ON vtiger_account.accountid = vtiger_accountgrouprelation.accountid
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_accountgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
					WHERE vtiger_crmentity.deleted=0 $sec_parameter
					GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
                                WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_account.accountid ASC";			
		}
		else if($module == 'Leads')
		{
			$nquery = "SELECT vtiger_leaddetails.leadid AS recordid, vtiger_users_last_import.deleted,
					$table_cols FROM vtiger_leaddetails 
					INNER JOIN vtiger_crmentity 
						ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
					INNER JOIN vtiger_leadsubdetails 
						ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid 
					INNER JOIN vtiger_leadaddress 
						ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid 
					LEFT JOIN vtiger_leadgrouprelation 
						ON vtiger_leaddetails.leadid = vtiger_leadgrouprelation.leadid 
					LEFT JOIN vtiger_groups
						ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
					LEFT JOIN vtiger_users
						ON vtiger_users.id = vtiger_crmentity.smownerid
					LEFT JOIN vtiger_users_last_import 
						ON vtiger_users_last_import.bean_id=vtiger_leaddetails.leadid 
					INNER JOIN (SELECT $table_cols 
							FROM vtiger_leaddetails 
							INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
							INNER JOIN vtiger_leadsubdetails 
								ON vtiger_leadsubdetails.leadsubscriptionid = vtiger_leaddetails.leadid 
							INNER JOIN vtiger_leadaddress 
								ON vtiger_leadaddress.leadaddressid = vtiger_leadsubdetails.leadsubscriptionid 
							LEFT JOIN vtiger_leadgrouprelation 
								ON vtiger_leaddetails.leadid = vtiger_leadgrouprelation.leadid 
							LEFT JOIN vtiger_groups
								ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
							LEFT JOIN vtiger_users
								ON vtiger_users.id = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted = 0 $sec_parameter
							GROUP BY $table_cols HAVING COUNT(*)>1) as temp 
					ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
					WHERE vtiger_crmentity.deleted=0  AND vtiger_leaddetails.converted = 0 $sec_parameter ORDER BY $table_cols,vtiger_leaddetails.leadid ASC";		
						
		}	
		else if($module == 'Products')
		{
			$nquery = "SELECT vtiger_products.productid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_products
				INNER JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_products.productid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_products.productid
					INNER JOIN (SELECT $table_cols
							FROM vtiger_products
							INNER JOIN vtiger_crmentity
								ON vtiger_crmentity.crmid = vtiger_products.productid WHERE vtiger_crmentity.deleted=0
							GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
                                WHERE vtiger_crmentity.deleted=0  ORDER BY $table_cols,vtiger_products.productid ASC";
		}	
		else if($module == "HelpDesk")
		{
			$nquery = "SELECT vtiger_troubletickets.ticketid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_troubletickets
				Inner JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_troubletickets.ticketid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_troubletickets.ticketid
				LEFT JOIN vtiger_attachments
					ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
				LEFT JOIN vtiger_ticketgrouprelation
					ON vtiger_troubletickets.ticketid = vtiger_ticketgrouprelation.ticketid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname
				LEFT JOIN vtiger_contactdetails 
					ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
				LEFT JOIN vtiger_ticketcomments
								ON vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid
				INNER JOIN (SELECT $table_cols FROM vtiger_troubletickets
							INNER JOIN vtiger_crmentity
								ON vtiger_crmentity.crmid = vtiger_troubletickets.ticketid 
							LEFT JOIN vtiger_attachments
								ON vtiger_attachments.attachmentsid=vtiger_crmentity.crmid
							LEFT JOIN vtiger_contactdetails 
								ON vtiger_contactdetails.contactid = vtiger_troubletickets.parent_id
							LEFT JOIN vtiger_ticketcomments
								ON vtiger_ticketcomments.ticketid = vtiger_crmentity.crmid
							LEFT JOIN vtiger_ticketgrouprelation
								ON vtiger_troubletickets.ticketid = vtiger_ticketgrouprelation.ticketid
							LEFT JOIN vtiger_groups
								ON vtiger_groups.groupname = vtiger_ticketgrouprelation.groupname
							LEFT JOIN vtiger_contactdetails contd
								ON contd.contactid = vtiger_troubletickets.parent_id
				WHERE vtiger_crmentity.deleted=0 $sec_parameter
							GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
                                WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_troubletickets.ticketid ASC";
		}
		else if($module == "Potentials")
		{
			$nquery = "SELECT vtiger_potential.potentialid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_potential
				Inner JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_potential.potentialid
				LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_potential.potentialid
				LEFT JOIN vtiger_potentialgrouprelation
					ON vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_users.id = vtiger_crmentity.smownerid
					INNER JOIN (SELECT $table_cols
							FROM vtiger_potential
							INNER JOIN vtiger_crmentity
								ON vtiger_crmentity.crmid = vtiger_potential.potentialid 
							LEFT JOIN vtiger_potentialgrouprelation
								ON vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid
							LEFT JOIN vtiger_groups
								ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
							LEFT JOIN vtiger_users
								ON vtiger_users.id = vtiger_crmentity.smownerid	
							WHERE vtiger_crmentity.deleted=0 $sec_parameter
							GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
                                WHERE vtiger_crmentity.deleted=0 $sec_parameter ORDER BY $table_cols,vtiger_potential.potentialid ASC";
		}
		else if($module == "Vendors")
		{
			$nquery = "SELECT vtiger_vendor.vendorid AS recordid,
				vtiger_users_last_import.deleted,".$table_cols."
				FROM vtiger_vendor
				Inner JOIN vtiger_crmentity
					ON vtiger_crmentity.crmid=vtiger_vendor.vendorid
					LEFT JOIN vtiger_users_last_import
					ON vtiger_users_last_import.bean_id=vtiger_vendor.vendorid
					INNER JOIN (SELECT $table_cols
							FROM vtiger_vendor
							INNER JOIN vtiger_crmentity
								ON vtiger_crmentity.crmid = vtiger_vendor.vendorid WHERE vtiger_crmentity.deleted=0
							GROUP BY ".$table_cols." HAVING COUNT(*)>1) as temp
				ON ".get_on_clause($field_values,$ui_type_arr,$module) ."
                                WHERE vtiger_crmentity.deleted=0  ORDER BY $table_cols,vtiger_vendor.vendorid ASC";
		}				
	}
	return $nquery;
}

/** Function to return the duplicate records data as a formatted array */
function getDuplicateRecordsArr($module)
{
	global $adb,$app_strings,$list_max_entries_per_page,$theme;
	$field_values_array=getFieldValues($module);
	$field_values=$field_values_array['fieldnames_list'];
	$fld_arr=$field_values_array['fieldnames_array'];
	$col_arr=$field_values_array['columnnames_array'];
	$fld_labl_arr=$field_values_array['fieldlabels_array'];
	$ui_type=$field_values_array['fieldname_uitype'];

	$dup_query = getDuplicateQuery($module,$field_values,$ui_type);
	// added for page navigation
	$dup_count_query = substr($dup_query, strpos($dup_query,'FROM'),strlen($dup_query));
	$dup_count_query = "Select count(*) as count ".$dup_count_query;
	$count_res = $adb->query($dup_count_query);
	$no_of_rows = $adb->query_result($count_res,0,"count");

	if($no_of_rows <= $list_max_entries_per_page)
		$_SESSION['dup_nav_start'.$module] = 1;
	else if(isset($_REQUEST["start"]) && $_REQUEST["start"] != "" && $_SESSION['dup_nav_start'.$module] != $_REQUEST["start"])
		$_SESSION['dup_nav_start'.$module] = $_REQUEST["start"];
	$start = ($_SESSION['dup_nav_start'.$module] != "")?$_SESSION['dup_nav_start'.$module]:1;
	$navigation_array = getNavigationValues($start, $no_of_rows, $list_max_entries_per_page);
	$start_rec = $navigation_array['start'];
	$end_rec = $navigation_array['end_val'];
	$navigationOutput = getTableHeaderNavigation($navigation_array, "",$module,"FindDuplicate","");
	if ($start_rec == 0)
		$limit_start_rec = 0;
	else
		$limit_start_rec = $start_rec -1;
	$dup_query .= " LIMIT $limit_start_rec, $list_max_entries_per_page";
	//ends
	
	$nresult=$adb->query($dup_query);
	$no_rows=$adb->num_rows($nresult);
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	require_once($theme_path.'layout_utils.php');	
	if($no_rows == 0)
	{
		if ($_REQUEST['action'] == 'FindDuplicate'.$module)
		{
			//echo "<br><br><center>".$app_strings['LBL_NO_DUPLICATE']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>";
			//die;
			echo "<link rel='stylesheet' type='text/css' href='themes/$theme/style.css'>";	
			echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
			echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>
		
				<table border='0' cellpadding='5' cellspacing='0' width='98%'>
				<tbody><tr>
				<td rowspan='2' width='11%'><img src='themes/$theme/images/empty.jpg' ></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span class='genHeaderSmall'>$app_strings[LBL_NO_DUPLICATE]</span></td>
				</tr>
				<tr>
				<td class='small' align='right' nowrap='nowrap'>			   	
				<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>     </td>
				</tr>
				</tbody></table> 
				</div>";
			echo "</td></tr></table>";
			exit();
		}
		else
		{
			echo "<br><br><table align='center' class='reportCreateBottom big' width='95%'><tr><td align='center'>".$app_strings['LBL_NO_DUPLICATE']."</td></tr></table>";
			die;
		}
	}	

	$rec_cnt = 0;
	$temp = Array();
	$sl_arr = Array();
	$grp = "group0";
	$gcnt = 0;
	$ii = 0; //ii'th record in group 
	while ( $rec_cnt < $no_rows )
	{			
		$result = $adb->fetchByAssoc($nresult);
		//echo '<pre>';print_r($result);echo '</pre>';	
		if($rec_cnt != 0)
		{
			$sl_arr = array_slice($result,2);	
			array_walk($temp,'lower_array');
			array_walk($sl_arr,'lower_array');
			$arr_diff = array_diff($temp,$sl_arr);
			if(count($arr_diff) > 0)
			{
				$gcnt++;	
				$temp = $sl_arr;
				$ii = 0;
			}
			$grp = "group".$gcnt;
		}
		$fld_values[$grp][$ii]['recordid'] = $result['recordid'];	
		for($k=0;$k<count($col_arr);$k++)
		{
			if($rec_cnt == 0)
			{
				$temp[$fld_labl_arr[$k]] = $result[$col_arr[$k]];
			}
			if($ui_type[$fld_arr[$k]] == 56)
			{
				if($result[$col_arr[$k]] == 0)
				{
					$result[$col_arr[$k]]=$app_strings['no'];
				}
				else
					$result[$col_arr[$k]]=$app_strings['yes'];
			}
			if($ui_type[$fld_arr[$k]] ==75 || $ui_type[$fld_arr[$k]] ==81)
			{
				$vendor_id=$result[$col_arr[$k]];
				if($vendor_id != '')
					{
						$vendor_name=getVendorName($vendor_id);
					}	
				$result[$col_arr[$k]]=$vendor_name;	
			}
			if($ui_type[$fld_arr[$k]] ==57)
			{
				$contact_id= $result[$col_arr[$k]];
				if($contact_id != '')
				{
					$contactname=getContactName($contact_id);
				}
						
				$result[$col_arr[$k]]=$contactname;
			}	
			if($ui_type[$fld_arr[$k]] ==68)
			{
				$parent_id= $result[$col_arr[$k]];
				if($parent_id != '')
				{
					$parentname=getParentName($parent_id);
				}
						
				$result[$col_arr[$k]]=$parentname;
			}
			if($ui_type[$fld_arr[$k]] ==53 || $ui_type[$fld_arr[$k]] ==52)
			{
				$assigned_to= $result[$col_arr[$k]];
				if($assigned_to != '')
				{
					$user=getUserName($assigned_to);
				}
				if($assigned_to == 0 and $module != 'Products')
				{
					$group_info = Array();
					$sql_group="select setype from vtiger_crmentity where crmid=?";
					$result_group=$adb->pquery($sql_group,array($fld_values[$grp][$ii]['recordid']));
					$group_info=getGroupName($fld_values[$grp][$ii]['recordid'],$adb->query_result($result_group,'setype'));
					$user=$group_info[0];
				}
				$result[$col_arr[$k]]=$user;
			}	
			if($ui_type[$fld_arr[$k]] ==50 or $ui_type[$fld_arr[$k]] ==51)
			{
				if($module!='Products') {
					$entity_name=getAccountName($result[$col_arr[$k]]);
				} else {
					$entity_name=getProductName($result[$col_arr[$k]]);
				}
				if($entity_name != '') {
					$result[$col_arr[$k]]=$entity_name;
				} else {
					$result[$col_arr[$k]]='';
				}
			}
			if($ui_type[$fld_arr[$k]] ==58)
			{
				$campaign_name=getCampaignName($result[$col_arr[$k]]);
				if($campaign_name != '')
					$result[$col_arr[$k]]=$campaign_name;
				else $result[$col_arr[$k]]='';
			}
			if($ui_type[$fld_arr[$k]] == 59)
			{
				$product_name=getProductName($result[$col_arr[$k]]);
				if($product_name != '')
					$result[$col_arr[$k]]=$product_name;
				else $result[$col_arr[$k]]='';
			}
			$fld_values[$grp][$ii][$fld_labl_arr[$k]] = $result[$col_arr[$k]];
			
		}
		$fld_values[$grp][$ii]['Entity Type'] = $result['deleted'];
		$ii++;	
		$rec_cnt++;
	}

	$gro="group";
	for($i=0;$i<$no_rows;$i++)
	{
		$ii=0;
		$dis_group[]=$fld_values[$gro.$i][$ii];
		$count_group[$i]=count($fld_values[$gro.$i]);
		$ii++;
		$new_group[]=$dis_group[$i];
	}
	$fld_nam=$new_group[0];
	$ret_arr[0]=$fld_values;
	$ret_arr[1]=$fld_nam;
	$ret_arr[2]=$ui_type;
	$ret_arr["navigation"]=$navigationOutput;
	return $ret_arr;
}

/** Function to get on clause criteria for sub tables like address tables to construct duplicate check query */
function get_special_on_clause($field_list)
{
	$field_array = explode(",",$field_list);
	$ret_str = '';
	$sel_clause = '';
	$i=1;
	$cnt = count($field_array);
	$spl_chk = ($_REQUEST['modulename'] != '')?$_REQUEST['modulename']:$_REQUEST['module'];
	foreach($field_array as $fld)
	{
		$sub_arr = explode(".",$fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];
		$fld_name = $sub_arr[2];
		
		//need to handle aditional conditions with sub tables for further modules of duplicate check
		if($tbl_name == 'vtiger_leadsubdetails' || $tbl_name == 'vtiger_contactsubdetails')
			$tbl_alias = "subd";
		else if($tbl_name == 'vtiger_leadaddress' || $tbl_name == 'vtiger_contactaddress')
			$tbl_alias = "addr";
		else if($tbl_name == 'vtiger_account' && $spl_chk == 'Contacts')
			$tbl_alias = "acc";
		else if($tbl_name == 'vtiger_accountbillads')
			$tbl_alias = "badd";
		else if($tbl_name == 'vtiger_accountshipads')
			$tbl_alias = "sadd";
		else if($tbl_name == 'vtiger_crmentity')
			$tbl_alias = "crm";
		else if($tbl_name == 'vtiger_customerdetails')
			$tbl_alias = "custd";
		else if($tbl_name == 'vtiger_contactdetails' && spl_chk == 'HelpDesk')
			$tbl_alias = "contd";
		else 
			$tbl_alias = "t";
			
		$sel_clause .= $tbl_alias.".".$col_name.",";	
		$ret_str .= " $tbl_name.$col_name = $tbl_alias.$col_name";
		if ($cnt != $i) $ret_str .= " and ";
		$i++;
	}
	$ret_arr['on_clause'] = $ret_str;
	$ret_arr['sel_clause'] = trim($sel_clause,",");
	return $ret_arr;
}

/** Function to get on clause criteria for duplicate check queries */
function get_on_clause($field_list,$uitype_arr,$module)
{
	$field_array = explode(",",$field_list);
	$ret_str = '';
	$i=1;
	foreach($field_array as $fld)
	{
		$sub_arr = explode(".",$fld);
		$tbl_name = $sub_arr[0];
		$col_name = $sub_arr[1];
		$fld_name = $sub_arr[2];
		if(is_uitype($uitype_arr[$fld_name],'_date_')== true)
			$ret_str .= " ifnull($tbl_name.$col_name,'null') = ifnull(temp.$col_name,'null')";
		else
			$ret_str .= " $tbl_name.$col_name = temp.$col_name";
		if (count($field_array) != $i) $ret_str .= " and ";
		$i++;
	}
	return $ret_str;
}

/** call back function to change the array values in to lower case */
function lower_array(&$string){
	    $string = strtolower(trim($string));
}

/** Function to get recordids for subquery where condition */
// TODO - Need to check if this method is used anywhere? 
function get_subquery_recordids($sub_query)
{
	global $adb;
	//need to update this module whenever duplicate check tool added for new modules
	$module_id_array = Array("Accounts"=>"accountid","Contacts"=>"contactid","Leads"=>"leadid","Products"=>"productid","HelpDesk"=>"ticketid","Potentials"=>"potentialid","Vendors"=>"vendorid");
	$id = ($module_id_array[$_REQUEST['modulename']] != '')?$module_id_array[$_REQUEST['modulename']]:$module_id_array[$_REQUEST['module']]; 
	$sub_res = '';
	$sub_result = $adb->query($sub_query);
	$row_count = $adb->num_rows($sub_result);
	$sub_res = '';
	if($row_count > 0)
	{
		while($rows = $adb->fetchByAssoc($sub_result))
		{
			$sub_res .= $rows[$id].",";
		}
		$sub_res = trim($sub_res,",");
	}
	else
		$sub_res .= "''";
	return $sub_res;
}

/** Function to get tablename, columnname, fieldname, fieldlabel and uitypes of fields of merge criteria for a particular module*/
function getFieldValues($module)
{
	global $adb,$current_user;

	//In future if we want to change a id mapping to name or other string then we can add that elements in this array.
	//$fld_table_arr = Array("vtiger_contactdetails.account_id"=>"vtiger_account.accountname");
	//$special_fld_arr = Array("account_id"=>"accountname");
	$fld_table_arr = Array();
	$special_fld_arr = Array();
	$tabid=getTabid($module);
	$sql="select distinct(userid) from vtiger_user2mergefields where tabid=?";
	$sql_result=$adb->pquery($sql,array($tabid));
	$num_rows=$adb->num_rows($sql_result);
	for($i=0; $i<$num_rows;$i++)
	{
		if($adb->query_result($sql_result,$i,"userid") == $current_user->id)
		{
			$user_id=$current_user->id;
			break;
		}
		$user_id=0;
	}
	$query="select fieldid from vtiger_user2mergefields where tabid=? and userid=? and visible=?";
	$result= $adb->pquery($query,array($tabid,$user_id,1));
	$num_rows=$adb->num_rows($result);
	for($i=0; $i<$num_rows;$i++)
	{
		$field_id = $adb->query_result($result,$i,"fieldid");
		$res_val.=$field_id.",";
				
	}
	$res_val=rtrim($res_val,",");
	if($res_val != "")
	{
		$fieldname_query="select fieldname,fieldlabel,uitype,tablename,columnname from vtiger_field where fieldid in (".$res_val.")";
		$fieldname_result = $adb->query($fieldname_query);
		$field_num_rows = $adb->num_rows($fieldname_result);
	}
	$fld_arr = array();
	$col_arr = array();
	for($j=0;$j< $field_num_rows;$j ++)
	{
		$tablename = $adb->query_result($fieldname_result,$j,'tablename');
		$column_name = $adb->query_result($fieldname_result,$j,'columnname');
		$field_name = $adb->query_result($fieldname_result,$j,'fieldname');
		$field_lbl = $adb->query_result($fieldname_result,$j,'fieldlabel');
		$ui_type = $adb->query_result($fieldname_result,$j,'uitype');
		$table_col = $tablename.".".$column_name;
		if(getFieldVisibilityPermission($module,$current_user->id,$field_name) == 0)
		{
			$fld_name = ($special_fld_arr[$field_name] != '')?$special_fld_arr[$field_name]:$field_name;			 
			
			$fld_arr[] = $fld_name;
			$col_arr[] = $column_name;
			if($fld_table_arr[$table_col] != '')
				$table_col = $fld_table_arr[$table_col];
			
			$field_values_array['fieldnames_list'][] = $table_col . "." . $fld_name;
			$fld_labl_arr[]=$field_lbl;
			$uitype[$field_name]=$ui_type;
		}
	}
	$field_values_array['fieldnames_list']=implode(",",$field_values_array['fieldnames_list']);
	$field_values=implode(",",$fld_arr);
	$field_values_array['fieldnames']=$field_values;
	$field_values_array["fieldnames_array"]=$fld_arr;
	$field_values_array["columnnames_array"]=$col_arr;
	$field_values_array['fieldlabels_array']=$fld_labl_arr;
	$field_values_array['fieldname_uitype']=$uitype;

	//echo "<pre>";print_r($field_values_array);echo "</pre>";
	return $field_values_array;	
}

/** To get security parameter for a particular module -- By Pavani*/
function getSecParameterforMerge($module)
{
	global $current_user;
	$tab_id = getTabid($module);
	$sec_parameter="";
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	if($is_admin == false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3)
	{
		if($module == "Leads" || $module == "Contacts" || $module == "HelpDesk" || $module == "Potentials")			
			$sec_parameter=getListViewSecurityParameter($module);
		else if($module == "Accounts")
		{
			$sec_parameter .= " AND (vtiger_crmentity.smownerid IN (".$current_user->id.")
		   		 OR vtiger_crmentity.smownerid IN (
					 SELECT vtiger_user2role.userid
					 FROM vtiger_user2role
					 INNER JOIN vtiger_users
						 ON vtiger_users.id = vtiger_user2role.userid
					 INNER JOIN vtiger_role
						 ON vtiger_role.roleid = vtiger_user2role.roleid
					 WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%')
					 OR vtiger_crmentity.smownerid IN (
						 SELECT shareduserid
						 FROM vtiger_tmp_read_user_sharing_per
						 WHERE userid=".$current_user->id."
						 AND tabid=".$tab_id.")
					 OR (vtiger_crmentity.smownerid in (0)
					 AND (";

                        if(sizeof($current_user_groups) > 0)
                        {
                              $sec_parameter .= " vtiger_accountgrouprelation.groupname IN (
				      		SELECT groupname
						FROM vtiger_groups
						WHERE groupid IN (". implode(",", getCurrentUserGroupList()) ."))
					OR ";
                        }
                         $sec_parameter .= " vtiger_accountgrouprelation.groupname IN (
				 	SELECT vtiger_groups.groupname
					FROM vtiger_tmp_read_group_sharing_per
					INNER JOIN vtiger_groups
						ON vtiger_groups.groupid = vtiger_tmp_read_group_sharing_per.sharedgroupid
					WHERE userid=".$current_user->id."
					AND tabid=".$tab_id.")))) ";

		}
		else if($module == "Products" || $module == "Vendors")
			$sec_parameter = "";
	}	
	return $sec_parameter;
}

// Update all the data refering to currency $old_cur to $new_cur
function transferCurrency($old_cur, $new_cur) {
		
	// Transfer User currency to new currency
	transferUserCurrency($old_cur, $new_cur);
	
	// Transfer Product Currency to new currency
	transferProductCurrency($old_cur, $new_cur);
	
	// Transfer PriceBook Currency to new currency
	transferPriceBookCurrency($old_cur, $new_cur);
}

// Function to transfer the users with currency $old_cur to $new_cur as currency
function transferUserCurrency($old_cur, $new_cur) {
	global $log, $adb, $current_user;
	$log->debug("Entering function transferUserCurrency...");
	
	$sql = "update vtiger_users set currency_id=? where currency_id=?";
	$adb->pquery($sql, array($new_cur, $old_cur));
	
	$current_user->retrieve_entity_info($current_user->id,"Users");
	$log->debug("Exiting function transferUserCurrency...");	
}

// Function to transfer the products with currency $old_cur to $new_cur as currency
function transferProductCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug("Entering function updateProductCurrency...");
	$prod_res = $adb->pquery("select productid from vtiger_products where currency_id = ?", array($old_cur));
	$numRows = $adb->num_rows($prod_res);
	$prod_ids = array();
	for($i=0;$i<$numRows;$i++) {
		$prod_ids[] = $adb->query_result($prod_res,$i,'productid');
	}
	if(count($prod_ids) > 0) {
		$prod_price_list = getPricesForProducts($new_cur,$prod_ids);
	
		for($i=0;$i<count($prod_ids);$i++) {
			$product_id = $prod_ids[$i];
			$unit_price = $prod_price_list[$product_id];
			$query = "update vtiger_products set currency_id=?, unit_price=? where productid=?";
			$params = array($new_cur, $unit_price, $product_id);
			$adb->pquery($query, $params);
		}	
	}
	$log->debug("Exiting function updateProductCurrency...");
}

// Function to transfer the pricebooks with currency $old_cur to $new_cur as currency 
// and to update the associated products with list price in $new_cur currency
function transferPriceBookCurrency($old_cur, $new_cur) {
	global $log, $adb;
	$log->debug("Entering function updatePriceBookCurrency...");
	$pb_res = $adb->pquery("select pricebookid from vtiger_pricebook where currency_id = ?", array($old_cur));
	$numRows = $adb->num_rows($pb_res);
	$pb_ids = array();
	for($i=0;$i<$numRows;$i++) {
		$pb_ids[] = $adb->query_result($pb_res,$i,'pricebookid');
	}
	
	if(count($pb_ids) > 0) {	
		require_once('modules/PriceBooks/PriceBooks.php');
		
		for($i=0;$i<count($pb_ids);$i++) {
			$pb_id = $pb_ids[$i];
			$focus = new PriceBooks();
			$focus->id = $pb_id;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($pb_id, "PriceBooks");
			$focus->column_fields['currency_id'] = $new_cur;
			$focus->save("PriceBooks");
		}	
	}
	
	$log->debug("Exiting function updatePriceBookCurrency...");
}

//functions for asterisk integration start
/**
 * this function returns the caller name based on the phone number that is passed to it
 * @param $from - the number which is calling
 * returns caller information in name(type) format :: for e.g. Mary Smith (Contact)
 * if no information is present in database, it returns :: Unknown Caller (Unknown)
 */
function getCallerName($from) {
	global $adb;

	//information found
	$callerInfo = getCallerInfo(getStrippedNumber($from));

	if($callerInfo != false){
		$callerName = $callerInfo[name];
		$module = $callerInfo[module];
		$callerModule = " (<a href='index.php?module=$module&action=index'>$module</a>)";
		$callerID = $callerInfo[id];
		
		$caller = "<a href='index.php?module=$module&action=DetailView&record=$callerID'>$callerName</a>$callerModule";
	}else{
		$caller = $caller."<br>
						<a target='_blank' href='index.php?module=Leads&action=EditView&phone=$from'>Create Lead</a><br>
						<a target='_blank' href='index.php?module=Contacts&action=EditView&phone=$from'>Create Contact</a><br>
						<a target='_blank' href='index.php?module=Accounts&action=EditView&phone=$from'>Create Account</a>";
	}
	return $caller;
}

/**
 * this function searches for a given number in vtiger and returns the callerInfo in an array format
 * currently the search is made across only leads, accounts and contacts modules
 * 
 * @param $number - the number whose information you want
 * @return array in format array(name=>callername, module=>module, id=>id);
 */
function getCallerInfo($number){
	global $adb, $log;
	$caller = "Unknown Number (Unknown)"; //declare caller as unknown in beginning
	
	$name['Contacts'] = array('name'=>"concat(firstname,' ',lastname)", 'table'=>'vtiger_contactdetails', 'field'=>"phone,mobile,fax", 'id'=>'contactid');
	$name['Accounts'] = array('name'=>"accountname", 'table'=>"vtiger_account", 'field'=>"phone, otherphone, fax", 'id'=>'accountid');
	$name['Leads'] = array('name'=>"concat(firstname,' ',lastname)", 'table'=>"vtiger_leaddetails inner join vtiger_leadaddress on vtiger_leaddetails.leadid = vtiger_leadaddress.leadaddressid", 'field'=>"phone,mobile,fax", 'id'=>'leadid'); 
	
	foreach ($name as $module => $info) {
		$phones = explode(",",$info[field]);
		
		$sql = "select *,".$info[name]." as name from ".$info[table]." inner join vtiger_crmentity on ".$info['id']."=crmid where deleted=0";
		$result = $adb->query($sql);
		
		$id = searchPhoneNumber($number, $phones, $result, 1);

		if($id){
			$callerName = $adb->query_result($result, $id, "name");
			$callerID = $adb->query_result($result,$id,$info['id']);
			$data = array("name"=>$callerName, "module"=>$module, "id"=>$callerID);
			return $data;			
		}
	}
	return false;
}

/**
 * this function searches the given record in the result for the given fields
 * @param integer $record - the value to search
 * @param array $fields - the fields to search
 * @param object $result - the adodb result set in which to search
 * @param integer $flag - if on, it removes the non-integers from the fields before comparison - 1 to set
 */
function searchPhoneNumber($record, $fields, $result, $flag=0){
	global $adb;
	$count = $adb->num_rows($result);

	for($i=0;$i<$count;$i++){
		foreach($fields as $field){
			$number = $adb->query_result($result, $i, $field);
			if($flag == 1){
				$number = getStrippedNumber($number);
			}
			if($number == $record){
				return $i;			
			}
		}
	}
	return false;
}

/**
 * this function removes any pre-codes like SIP:, PSTN:, etc added to a number
 * it also removes any braces or spaces present in a number
 * @param string $number - the number to be processed
 */
function getStrippedNumber($number){
	$number = preg_replace("/[^\d]/i","",$number);
	return $number;
}

/**
 * this function returns the tablename and primarykeys for a given module in array format
 * @param object $adb - peardatabase type object
 * @param string $module - module name for  which you want the array
 * @return array(tablename1=>primarykey1,.....)
 */
function get_tab_name_index($adb, $module){
	$tabid = getTabid($module);
	$sql = "select * from vtiger_tab_name_index where tabid = ?";
	$result = $adb->pquery($sql, array($tabid));
	$count = $adb->num_rows($result);
	$data = array();
	
	for($i=0; $i<$count; $i++){
		$tablename = $adb->query_result($result, $i, "tablename");
		$primaryKey = $adb->query_result($result, $i, "primarykey");
		$data[$tablename] = $primaryKey;
	}
	return $data;
}

/**
 * this function returns the value of use_asterisk from the database for the current user
 * @param string $id - the id of the current user
 */
function get_use_asterisk($id){
	global $adb;
	$sql = "select * from vtiger_asteriskextensions where userid = ?";
	$result = $adb->pquery($sql, array($id));
	if($adb->num_rows($result)>0){
		$use_asterisk = $adb->query_result($result, 0, "use_asterisk");
		$asterisk_extension = $adb->query_result($result, 0, "asterisk_extension");
		if($use_asterisk == 0 || empty($asterisk_extension)){
			return 'false';
		}else{
			return 'true';
		}
	}else{
		return 'false';
	}
}

/**
 * this function adds a record to the callhistory module
 * 
 * @param string $userExtension - the extension of the current user
 * @param string $callfrom - the caller number
 * @param string $callto - the called number
 * @param string $status - the status of the call (outgoing/incoming/missed)
 * @param object $adb - the peardatabase object
 */
function addToCallHistory($userExtension, $callfrom, $callto, $status, $adb){
	$sql = "select * from vtiger_asteriskextensions where asterisk_extension=".$userExtension;
	$result = $adb->pquery($sql,array());
	$userID = $adb->query_result($result, 0, "userid");
	
	$crmID = $adb->getUniqueID('vtiger_crmentity');
	$timeOfCall = date('Y-m-d H:i:s');
	
	$sql = "insert into vtiger_crmentity values (?,?,?,?,?,?,?,?,?,?,?,?,?)";
	$params = array($crmID, $userID, $userID, 0, "PBXManager", "", $timeOfCall, $timeOfCall, NULL, NULL, 0, 1, 0);
	$adb->pquery($sql, $params);
	
	if(empty($callfrom)){
		$callfrom = "Unknown";
	}
	if(empty($callto)){
		$callto = "Unknown";
	}
	
	if($status == 'outgoing'){
		//call is from user to record
		$sql = "select * from vtiger_asteriskextensions where asterisk_extension=$callfrom";
		$result = $adb->query($sql);
		if($adb->num_rows($result)>0){
			$userid = $adb->query_result($result, 0, "userid");
			$callerName = getUserFullName($userid);
		}
		
		$receiver = getCallerInfo($callto);
		if($receiver == false){
			$receiver = getCallerInfo(getStrippedNumber($callto));
		}
		if(empty($receiver)){
			$receiver = "Unknown";
		}else{
			$receiver = "<a href='index.php?module=".$receiver[module]."&action=DetailView&record=".$receiver[id]."'>".$receiver[name]."</a>";
		}
	}else{
		//call is from record to user
		$sql = "select * from vtiger_asteriskextensions where asterisk_extension=$callto";
		$result = $adb->query($sql);
		if($adb->num_rows($result)>0){
			$userid = $adb->query_result($result, 0, "userid");
			$receiver = getUserFullName($userid);
		}
		$callerName = getCallerInfo($callfrom);
		if($callerName == false){
			$callerName = getCallerInfo(getStrippedNumber($callfrom));
		}
		if(empty($callerName)){
			$callerName = "Unknown";
		}else{
			$callerName = "<a href='index.php?module=".$callerName[module]."&action=DetailView&record=".$callerName[id].">'".$callerName[name]."</a>";
		}
	}
	
	$sql = "insert into vtiger_pbxmanager values (?,?,?,?,?)";
	$params = array($crmID, $callerName, $receiver, $timeOfCall, $status);
	$adb->pquery($sql, $params);
}
//functions for asterisk integration end

/* Function to get the name of the Field which is used for Module Specific Sequence Numbering, if any 
 * @param module String - Module label
 * return Array - Field name and label are returned */
function getModuleSequenceField($module) {
	global $adb, $log;
	$log->debug("Entering function getModuleSequenceFieldName ($module)...");
	$field = null;
	
	if (!empty($module)) {
		//uitype 4 points to Module Numbering Field
		$seqColRes = $adb->pquery("SELECT fieldname, fieldlabel FROM vtiger_field WHERE uitype=? AND tabid=?", array('4', getTabid($module)));
		if($adb->num_rows($seqColRes) > 0) {
			$fieldname = $adb->query_result($seqColRes,0,'fieldname');
			$fieldlabel = $adb->query_result($seqColRes,0,'fieldlabel');
			$field['name'] = $fieldname;
			$field['label'] = $fieldlabel;
		}
	}
	
	$log->debug("Exiting getModuleSequenceFieldName...");
	return $field;
}
?>
