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

include_once('config.php');
require_once('include/logging.php');
require_once('data/SugarBean.php');
require_once('include/logging.php');
require_once('include/ListView/ListView.php');
require_once('include/database/PearDatabase.php');

// Contact is used to store customer information.
class LoginHistory {
	var $log;
	var $db;

	// Stored vtiger_fields
	var $login_id;
	var $user_name;
	var $user_ip;
	var $login_time;
	var $logout_time;
	var $status;
	var $module_name = "Users";

	var $table_name = "vtiger_loginhistory";

	var $object_name = "LoginHistory";
	
	var $new_schema = true;

	var $column_fields = Array("id"
		,"login_id"
		,"user_name"
		,"user_ip"
		,"login_time"
		,"logout_time"
		,"status"
		);
	
	function LoginHistory() {
		$this->log = LoggerManager::getLogger('loginhistory');
		$this->db = new PearDatabase();
	}
	
	var $sortby_fields = Array('user_name', 'user_ip', 'login_time', 'logout_time', 'status');	 
       	
	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			'User Name'=>Array('vtiger_loginhistory'=>'user_name'), 
			'User IP'=>Array('vtiger_loginhistory'=>'user_ip'), 
			'Signin Time'=>Array('vtiger_loginhistory'=>'login_time'),
		        'Signout Time'=>Array('vtiger_loginhistory'=>'logout_time'), 
			'Status'=>Array('vtiger_loginhistory'=>'status'),
		);	
	
	var $list_fields_name = Array(
		'User Name'=>'user_name',
		'User IP'=>'user_ip',
		'Signin Time'=>'login_time',
		'Signout Time'=>'logout_time',
		'Status'=>'status'
		);	
	var $default_order_by = "login_time";
	var $default_sort_order = 'DESC';

/**
 * Function to get the Header values of Login History.
 * Returns Header Values like UserName, IP, LoginTime etc in an array format.
**/
	function getHistoryListViewHeader()
	{
		global $log;
		$log->debug("Entering getHistoryListViewHeader method ...");
		global $app_strings;
		
		$header_array = array($app_strings['LBL_LIST_USER_NAME'], $app_strings['LBL_LIST_USERIP'], $app_strings['LBL_LIST_SIGNIN'], $app_strings['LBL_LIST_SIGNOUT'], $app_strings['LBL_LIST_STATUS']);

		$log->debug("Exiting getHistoryListViewHeader method ...");
		return $header_array;
		
	}

/**
  * Function to get the Login History values of the User.
  * @param $navigation_array - Array values to navigate through the number of entries.
  * @param $sortorder - DESC
  * @param $orderby - login_time
  * Returns the login history entries in an array format.
**/
	function getHistoryListViewEntries($navigation_array, $sorder='', $orderby='')
	{
		global $log;
		$log->debug("Entering getHistoryListViewEntries() method ...");
		global $adb, $current_user;	

		if($sorder != '' && $order_by != '')
		{
			if(is_admin($current_user))
	       			$list_query = "Select * from vtiger_loginhistory order by ".$order_by." ".$sorder;
			else	
	       			$list_query = "Select * from vtiger_loginhistory where user_name=".$current_user->user_name." order by ".$order_by." ".$sorder;
				
		}	
		else
		{
			if(is_admin($current_user))
				$list_query = "Select * from vtiger_loginhistory order by ".$this->default_order_by." ".$this->default_sort_order;
			else	
				$list_query = "Select * from vtiger_loginhistory where user_name='".$current_user->user_name."' order by ".$this->default_order_by." ".$this->default_sort_order;
				
		}
		$result = $adb->query($list_query);
		$entries_list = array();
		
		for($i = $navigation_array['start']; $i <= $navigation_array['end_val']; $i++)
		{
			$entries = array();
			$loginid = $adb->query_result($result, $i-1, 'login_id');

			$entries[] = $adb->query_result($result, $i-1, 'user_name');
			$entries[] = $adb->query_result($result, $i-1, 'user_ip');
			$entries[] = $adb->query_result($result, $i-1, 'login_time');
			$entries[] = $adb->query_result($result, $i-1, 'logout_time');
			$entries[] = $adb->query_result($result, $i-1, 'status');

			$entries_list[] = $entries;
		}	
		log->debug("Exiting getHistoryListViewEntries() method ...");
		return $entries_list;
		
	}
	
	/** Records the Login info */
	function user_login(&$usname,&$usip,&$intime)
	{
		$query = "Insert into vtiger_loginhistory (user_name, user_ip, logout_time, login_time, status) values ('$usname','$usip',null,".$this->db->formatDate($intime).",'Signed in')";
		$result = $this->db->query($query)
                        or die("MySQL error: ".mysql_error());
		
		return $result;
	}
	
	function user_logout(&$usname,&$usip,&$outtime)
	{
		$logid_qry = "SELECT max(login_id) AS login_id from vtiger_loginhistory where user_name='$usname' and user_ip='$usip'";
		$result = $this->db->query($logid_qry);
		$loginid = $this->db->query_result($result,0,"login_id");
		if ($loginid == '')
                {
                        return;
                }
		// update the user login info.
		$query = "Update vtiger_loginhistory set logout_time =".$this->db->formatDate($outtime).", status='Signed off' where login_id = $loginid";
		$result = $this->db->query($query)
                        or die("MySQL error: ".mysql_error());
	}

  	function create_list_query(&$order_by, &$where)
  	{
		// Determine if the vtiger_account name is present in the where clause.
		global $current_user;
		$query = "SELECT user_name,user_ip, status,
				".$this->db->getDBDateString("login_time")." AS login_time,
				".$this->db->getDBDateString("logout_time")." AS logout_time
			FROM ".$this->table_name;
		if($where != "")
		{
			if(!is_admin($current_user))
			$where .=" AND user_name = '".$current_user->user_name."'";
			$query .= " WHERE ($where)";
		}
		else
		{
			if(!is_admin($current_user))
			$query .= " WHERE user_name = '".$current_user->user_name."'";
		}
		
		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

                return $query;
	}

}



?>
