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

require_once("config.php");
require_once('include/logging.php');
require_once('include/nusoap/nusoap.php');
require_once('include/database/PearDatabase.php');

$log = &LoggerManager::getLogger('wordplugin');

$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;
$accessDenied = "You are not permitted to perform this action";
$server->configureWSDL('vtigersoap');

$server->wsdl->addComplexType(
    'contact_column_detail',
    'complexType',
    'array',
    '',
    array(
        'email_address' => array('name'=>'email_address','type'=>'xsd:string'),
        'first_name' => array('name'=>'first_name','type'=>'xsd:string'),
        'last_name' => array('name'=>'last_name','type'=>'xsd:string'),
        'primary_address_city' => array('name'=>'primary_address_city','type'=>'xsd:string'),
        'account_name' => array('name'=>'account_name','type'=>'xsd:string'),
        'id' => array('name'=>'id','type'=>'xsd:string'),
        'salutation' => array('name'=>'salutation','type'=>'xsd:string'),
        'title'=> array('name'=>'title','type'=>'xsd:string'),
        'phone_mobile'=> array('name'=>'phone_mobile','type'=>'xsd:string'),
        'reports_to'=> array('name'=>'reports_to','type'=>'xsd:string'),
        'primary_address_city'=> array('name'=>'primary_address_city','type'=>'xsd:string'),
        'primary_address_street'=> array('name'=>'primary_address_street','type'=>'xsd:string'),
        'primary_address_state'=> array('name'=>'primary_address_state','type'=>'xsd:string'),
        'primary_address_postalcode'=> array('name'=>'primary_address_postalcode','type'=>'xsd:string'),
        'primary_address_country'=> array('name'=>'primary_address_country','type'=>'xsd:string'),
        'alt_address_city'=> array('name'=>'alt_address_city','type'=>'xsd:string'),
        'alt_address_street'=> array('name'=>'alt_address_street','type'=>'xsd:string'),
        'alt_address_state'=> array('name'=>'alt_address_state','type'=>'xsd:string'),
        'alt_address_postalcode'=> array('name'=>'alt_address_postalcode','type'=>'xsd:string'),
        'alt_address_country'=> array('name'=>'alt_address_country','type'=>'xsd:string'),
    )
);

$server->wsdl->addComplexType(
    'account_column_detail',
    'complexType',
    'array',
    '',
    array(
        'accountid' => array('name'=>'accountid','type'=>'xsd:string'),
        'accountname' => array('name'=>'accountname','type'=>'xsd:string'),
        'parentid' => array('name'=>'parentid','type'=>'xsd:string'),
        'account_type' => array('name'=>'account_type','type'=>'xsd:string'),
        'industry' => array('name'=>'industry','type'=>'xsd:string'), 
        'annualrevenue' => array('name'=>'annualrevenue','type'=>'xsd:string'),
        'rating'=> array('name'=>'rating','type'=>'xsd:string'), 
        'ownership' => array('name'=>'ownership','type'=>'xsd:string'),
        'siccode' => array('name'=>'siccode','type'=>'xsd:string'),
        'tickersymbol' => array('name'=>'tickersymbol','type'=>'xsd:string'),
        'phone' => array('name'=>'phone','type'=>'xsd:string'),
        'otherphone' => array('name'=>'otherphone','type'=>'xsd:string'),
        'email1' => array('name'=>'email1','type'=>'xsd:string'),
        'email2' => array('name'=>'email2','type'=>'xsd:string'),
        'website' => array('name'=>'website','type'=>'xsd:string'),
        'fax' => array('name'=>'fax','type'=>'xsd:string'),
        //'employees' => array('name'=>'employees','type'=>'xsd:string'),
			)
);

$server->wsdl->addComplexType(
    'lead_column_detail',
    'complexType',
    'array',
    '',
    array(
        'id' => array('name'=>'id','type'=>'xsd:string'), 
        'date_entered' => array('name'=>'date_entered','type'=>'xsd:string'),
        'date_modified' => array('name'=>'date_modified','type'=>'xsd:string'),
        'modified_user_id' => array('name'=>'modified_user_id','type'=>'xsd:string'),
        'assigned_user_id' => array('name'=>'assigned_user_id','type'=>'xsd:string'),
        'salutation' => array('name'=>'salutation','type'=>'xsd:string'),
        'first_name' => array('name'=>'first_name','type'=>'xsd:string'),
        'last_name' => array('name'=>'last_name','type'=>'xsd:string'),
        'company' => array('name'=>'company','type'=>'xsd:string'),
        'designation' => array('name'=>'designation','type'=>'xsd:string'),
        'lead_source' => array('name'=>'lead_source','type'=>'xsd:string'),
        'industry' => array('name'=>'industry','type'=>'xsd:string'),
        'annual_revenue' => array('name'=>'annual_revenue','type'=>'xsd:string'),
        'license_key' => array('name'=>'license_key','type'=>'xsd:string'),
        'phone' => array('name'=>'phone','type'=>'xsd:string'),
        'mobile' => array('name'=>'mobile','type'=>'xsd:string'),
        'fax' => array('name'=>'fax','type'=>'xsd:string'),
        'email' => array('name'=>'email','type'=>'xsd:string'),
        'yahoo_id' => array('name'=>'yahoo_id','type'=>'xsd:string'),
        'website' => array('name'=>'website','type'=>'xsd:string'),
        'lead_status' => array('name'=>'lead_status','type'=>'xsd:string'),
        'rating' => array('name'=>'rating','type'=>'xsd:string'),
        'employees' => array('name'=>'employees','type'=>'xsd:string'),
        'address_street' => array('name'=>'address_street','type'=>'xsd:string'),
        'address_city' => array('name'=>'address_city','type'=>'xsd:string'),
        'address_state' => array('name'=>'address_state','type'=>'xsd:string'),
        'address_postalcode' => array('name'=>'address_postalcode','type'=>'xsd:string'),
        'address_country' => array('name'=>'address_country','type'=>'xsd:string'),
        'description' => array('name'=>'description','type'=>'xsd:string'),
        'deleted' => array('name'=>'deleted','type'=>'xsd:string'),
        'converted' => array('name'=>'converted','type'=>'xsd:string'),
    )
);

$server->wsdl->addComplexType(
    'user_column_detail',
    'complexType',
    'array',
    '',
    array(
 	'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
    	'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
        'username' => array('name'=>'username','type'=>'xsd:string'),
        'yahooid' => array('name'=>'yahooid','type'=>'xsd:string'),
        'title' => array('name'=>'title','type'=>'xsd:string'),
        'workphone' => array('name'=>'workphone','type'=>'xsd:string'),
        'department' => array('name'=>'department','type'=>'xsd:string'), 
        'mobilephone' => array('name'=>'mobilephone','type'=>'xsd:string'),
        'otherphone'=> array('name'=>'otherphone','type'=>'xsd:string'), 
        'fax' => array('name'=>'fax','type'=>'xsd:string'),
        'email' => array('name'=>'email','type'=>'xsd:string'),
        'homephone' => array('name'=>'homephone','type'=>'xsd:string'),
        'otheremail' => array('name'=>'otheremail','type'=>'xsd:string'),
        'street' => array('name'=>'street','type'=>'xsd:string'),
        'city' => array('name'=>'city','type'=>'xsd:string'),
        'state' => array('name'=>'state','type'=>'xsd:string'),
        'code' => array('name'=>'code','type'=>'xsd:string'),
        'country' => array('name'=>'country','type'=>'xsd:string'),
	)
);

$server->wsdl->addComplexType(
	'tickets_list_array',
	'complexType',
	'array',
	'',
	array(
	        'ticketid' => array('name'=>'ticketid','type'=>'xsd:string'),
	        'title' => array('name'=>'title','type'=>'xsd:string'),
        	'groupname' => array('name'=>'groupname','type'=>'xsd:string'),
        	'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
        	'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
	        'parent_id' => array('name'=>'parent_id','type'=>'xsd:string'),
	        'productid' => array('name'=>'productid','type'=>'xsd:string'),
	        'productname' => array('name'=>'productname','type'=>'xsd:string'),
	        'priority' => array('name'=>'priority','type'=>'xsd:string'),
	        'severity' => array('name'=>'severity','type'=>'xsd:string'),
	        'status' => array('name'=>'status','type'=>'xsd:string'),
	        'category' => array('name'=>'category','type'=>'xsd:string'),
	        'description' => array('name'=>'description','type'=>'xsd:string'),
	        'solution' => array('name'=>'solution','type'=>'xsd:string'),
	        'createdtime' => array('name'=>'createdtime','type'=>'xsd:string'),
	        'modifiedtime' => array('name'=>'modifiedtime','type'=>'xsd:string'),
	     )
);

$server->register(
    'get_contacts_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:contact_column_detail'),
    $NAMESPACE);

$server->register(
    'get_accounts_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:account_column_detail'),
    $NAMESPACE);

$server->register(
    'get_leads_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:lead_column_detail'),
    $NAMESPACE);

$server->register(
    'get_user_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:user_column_detail'),
    $NAMESPACE);

$server->register(
    'get_tickets_columns',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'tns:tickets_list_array'),
    $NAMESPACE);

$server->register(
    'create_session',
    array('user_name'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'end_session',
    array('user_name'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

	        
function get_tickets_columns($user_name, $password)
{
	global $current_user;
	require_once("modules/Users/User.php");
	$seed_user=new User();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	if(isPermitted("HelpDesk","index") == "yes")
	{ 
		require_once('modules/HelpDesk/HelpDesk.php');
		$helpdesk = new HelpDesk();
		return $helpdesk->getColumnNames_Hd();
	}
	else
		return null;
}

function get_contacts_columns($user_name, $password)
{
	global $current_user;
	require_once("modules/Users/User.php");
	$seed_user=new User();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	if(isPermitted("Contacts","index") == "yes")
	{
		require_once('modules/Contacts/Contact.php');
		$contact = new Contact();
		return $contact->getColumnNames();	   
	}
	else
		return null;

}


function get_accounts_columns($user_name, $password)
{
	global $current_user;
	require_once("modules/Users/User.php");
	$seed_user=new User();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	if(isPermitted("Accounts","index") == "yes")
	{
		require_once('modules/Accounts/Account.php');
		$account = new Account();
		return $account->getColumnNames_Acnt();
	}
	else
		return null;

}


function get_leads_columns($user_name, $password)
{	
	global $current_user;
	require_once("modules/Users/User.php");
	$seed_user=new User();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');

	if(isPermitted("Leads","index") == "yes")
	{
		require_once('modules/Leads/Lead.php');
		$lead = new Lead();
		return $lead->getColumnNames_Lead();
	}
	else
		return null;
}

function get_user_columns($user_name, $password)
{
	global $current_user;
	require_once('modules/Users/User.php');
	$seed_user=new User();
	$user_id=$seed_user->retrieve_user_id($user_name);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');
	if(isPermitted("Users","index") == "yes")
	{
		$user = new User();
		return $user->getColumnNames_User();
	}
	else
		return null;
}


function create_session($user_name, $password)
{ 
  	global $adb;
	require_once('modules/Users/User.php');
	$objuser = new User();
	if($password != "" && $user_name != '')
	{
		$objuser->column_fields['user_name'] = $user_name;
		$objuser->load_user($password);
		if($objuser->is_authenticated())
		{
		  return "TempSessionID";
		}else
		{
			return "false";
		}
	}else
	{
			return "false";
	}
}

function end_session($user_name)
{
	return "Success";	
}
 



$server->service($HTTP_RAW_POST_DATA); 
exit(); 
?>
