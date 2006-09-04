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
require_once('modules/Contacts/Contact.php');

$log = &LoggerManager::getLogger('thunderbirdplugin');

$accessDenied = "You are not authorized for performing this action";
$NAMESPACE = 'http://www.vtiger.com/vtigercrm/';
$server = new soap_server;

$server->configureWSDL('vtigersoap');

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

$server->register(
    'SearchContactsByEmail',
    array('username'=>'xsd:string','emailaddress'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
		'GetContacts',
    array('username'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
	  'AddContact',
    array('user_name'=>'xsd:string', 
          'first_name'=>'xsd:string', 
          'last_name'=>'xsd:string', 
          'email_address'=>'xsd:string',
          'account_name'=>'xsd:string', 
          'salutation'=>'xsd:string', 
          'title'=>'xsd:string', 
          'phone_mobile'=>'xsd:string', 
          'reports_to'=>'xsd:string', 
          'primary_address_street'=>'xsd:string', 
          'primary_address_city'=>'xsd:string', 
          'primary_address_state'=>'xsd:string' , 
          'primary_address_postalcode'=>'xsd:string', 
          'primary_address_country'=>'xsd:string', 
          'alt_address_city'=>'xsd:string', 
          'alt_address_street'=>'xsd:string',
          'alt_address_state'=>'xsd:string', 
          'alt_address_postalcode'=>'xsd:string', 
          'alt_address_country'=>'xsd:string',
          'office_phone'=>'xsd:string',
          'home_phone'=>'xsd:string',
          'fax'=>'xsd:string',
          'department'=>'xsd:string',
          'description'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

    
$server->register(
	'track_email',
    array('user_name'=>'xsd:string', 'contact_ids'=>'xsd:string', 'date_sent'=>'xsd:date', 'email_subject'=>'xsd:string', 'email_body'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE); 
    
$server->wsdl->addComplexType(
    'contactdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
	      'id' => array('name'=>'id','type'=>'xsd:string'),
        'firstname' => array('name'=>'firstname','type'=>'xsd:string'),        
        'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
        'emailaddress' => array('name'=>'emailaddress','type'=>'xsd:string'),
        'accountname' => array('name'=>'accountname','type'=>'xsd:string'),
        'middlename' => array('name'=>'middlename','type'=>'xsd:string'),
        'birthdate'=> array('name'=>'birthdate','type'=>'xsd:string'),
        'jobtitle'=> array('name'=>'jobtitle','type'=>'xsd:string'),
        'department'=> array('name'=>'department','type'=>'xsd:string'),
        'title' => array('name'=>'title','type'=>'xsd:string'),
        'officephone'=> array('name'=>'officephone','type'=>'xsd:string'),
        'homephone'=> array('name'=>'homephone','type'=>'xsd:string'),
        'otherphone'=> array('name'=>'otherphone','type'=>'xsd:string'),
        'fax'=> array('name'=>'fax','type'=>'xsd:string'),
        'mobile'=> array('name'=>'mobile','type'=>'xsd:string'),
        'asstname'=> array('name'=>'asstname','type'=>'xsd:string'),
        'asstphone'=> array('name'=>'asstphone','type'=>'xsd:string'),
        'reportsto'=> array('name'=>'reportsto','type'=>'xsd:string'),
        'mailingstreet'=> array('name'=>'mailingstreet','type'=>'xsd:string'),
        'mailingcity'=> array('name'=>'mailingcity','type'=>'xsd:string'),
        'mailingstate'=> array('name'=>'mailingstate','type'=>'xsd:string'),
        'mailingzip'=> array('name'=>'mailingzip','type'=>'xsd:string'),
        'mailingcountry'=> array('name'=>'mailingcountry','type'=>'xsd:string'),
        'otherstreet'=> array('name'=>'otherstreet','type'=>'xsd:string'),
        'othercity'=> array('name'=>'othercity','type'=>'xsd:string'),
        'otherstate'=> array('name'=>'otherstate','type'=>'xsd:string'),
        'otherzip'=> array('name'=>'otherzip','type'=>'xsd:string'),
        'othercountry'=> array('name'=>'othercountry','type'=>'xsd:string'),
        'description'=> array('name'=>'description','type'=>'xsd:string'),
        'category'=> array('name'=>'category','type'=>'xsd:string'),
    )
);

$server->wsdl->addComplexType(
    'contactdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:contactdetail[]')
    ),
    'tns:contactdetail'
);
    

function SearchContactsByEmail($username,$emailaddress)
{
     require_once('modules/Contacts/Contact.php');
     
     $seed_contact = new Contact();
     $output_list = Array();
     
     $response = $seed_contact->get_searchbyemailid($username,$emailaddress);
     $contactList = $response['list'];
     
     // create a return array of names and email addresses.
     foreach($contactList as $contact)
     {
          $output_list[] = Array(
               "id" => $contact[contactid],
               "firstname" => $contact[firstname],
               "lastname" => $contact[lastname],
               "emailaddress" => $contact[email],
               "accountname" => $contact[accountname],
          );
     }
     
     //to remove an erroneous compiler warning
     $seed_contact = $seed_contact;
     return $output_list;
}    

function track_email($user_name, $contact_ids, $date_sent, $email_subject, $email_body)
{
	global $adb;
	require_once('modules/Users/User.php');
	require_once('modules/Emails/Email.php');
	
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	
	$email = new Email();
	//$log->debug($msgdtls['contactid']);
	$emailbody = str_replace("'", "''", $email_body);
	$emailsubject = str_replace("'", "''",$email_subject);
	$datesent = getDisplayDate($date_sent);

	$email->column_fields[subject] = $emailsubject;
	$email->column_fields[assigned_user_id] = $user_id;
	$email->column_fields[date_start] = $datesent;
	$email->column_fields[description]  = htmlentities($emailbody);
	$email->column_fields[activitytype] = 'Emails'; 
	$email->plugin_save = true;
	$email->save("Emails");

	$email->set_emails_contact_invitee_relationship($email->id,$contact_ids);
	$email->set_emails_se_invitee_relationship($email->id,$contact_ids);
	$email->set_emails_user_invitee_relationship($email->id,$user_id);
	$sql = "select email from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid where vtiger_crmentity.deleted =0 and vtiger_contactdetails.contactid='".$contact_ids."'";
	$result = $adb->query($sql);
	$camodulerow = $adb->fetch_array($result);
	if(isset($camodulerow))
	{
		$emailid = $camodulerow["email"];
		$query = 'insert into vtiger_emaildetails values ('.$email->id.',"","'.$emailid.'","","","","'.$contact_ids."@77|".'","THUNDERBIRD")';
		$adb->query($query);
	}
	return $email->id;
}

    
function GetContacts($username)
{
	global $adb;
	global $log;
	require_once('modules/Contacts/Contact.php');

	$seed_contact = new Contact();
	$output_list = Array();

	$query = $seed_contact->get_contactsforol($username);
	$result = $adb->query($query);

	while($contact = $adb->fetch_array($result))
	{
		if($contact["birthdate"] == "0000-00-00")
		{
			$contact["birthdate"] = "";
		}
		if($contact["salutation"] == "--None--")
		{
			$contact["salutation"] = "";
		}

		$namelist = explode(" ", $contact["lastname"]);
		if(isset($namelist))
		{
			if(count($namelist) >= 2) 
			{
				$contact["lastname"] = $namelist[count($namelist)-1];       	
				for($i=0; $i<count($namelist)-2; $i++)
				{
					$middlename[] = $namelist[$i];
				}
				if(isset($middlename))
				{
					$middlename = implode(" ",$middlename);
				}
			}
		}

		$output_list[] = Array(
				"id" => $contact["id"],
				"title" => $contact["salutation"],
				"firstname" => $contact["firstname"],
				"middlename" => trim($middlename),
				"lastname" => trim($contact["lastname"]),
				"birthdate" => $contact["birthday"],
				"emailaddress" => $contact["email"],
				"jobtitle" => $contact["title"],
				"department" => $contact["department"],
				"accountname" => $contact["accountname"],                         
				"officephone" => $contact["phone"],
				"homephone" => $contact["homephone"],
				"otherphone" => $contact["otherphone"],           
				"fax" => $contact["fax"],
				"mobile" => $contact["mobile"],
				"asstname" => $contact["assistant"],
				"asstphone" => $contact["assistantphone"],             
				"reportsto" => $contact["reports_to_name"],
				"mailingstreet" => $contact["mailingstreet"],
				"mailingcity" => $contact["mailingcity"],
				"mailingstate" => $contact["mailingstate"],
				"mailingzip" => $contact["mailingzip"],
				"mailingcountry" => $contact["mailingcountry"],              
				"otherstreet" => $contact["otherstreet"],
				"othercity" => $contact["othercity"],
				"otherstate" => $contact["otherstate"],
				"otherzip" => $contact["otherzip"],
				"othercountry" => $contact["othercountry"],
				"description" => "",
				"category" => "",        
			  );
	}
	//to remove an erroneous compiler warning
	$seed_contact = $seed_contact;
	return $output_list;
}

function retrieve_account_id($account_name,$user_id)
{

	if($account_name=="")
	{
		return null;
	}

	$query = "select vtiger_account.accountname accountname,vtiger_account.accountid accountid from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_crmentity.deleted=0 and vtiger_account.accountname='" .$account_name."'";


	$db = new PearDatabase();
	$result=  $db->query($query) or die ("Not able to execute insert");

	$rows_count =  $db->getRowCount($result);
	if($rows_count==0)
	{
		require_once('modules/Accounts/Account.php');
		$account = new Account();
		$account->column_fields[accountname] = $account_name;
		$account->column_fields[assigned_user_id]=$user_id;
		//$account->saveentity("Accounts");
		$account->save("Accounts");
		//mysql_close();
		return $account->id;
	}
	else if ($rows_count==1)
	{
		$row = $db->fetchByAssoc($result, 0);
		//mysql_close();
		return $row["accountid"];	    
	}
	else
	{
		$row = $db->fetchByAssoc($result, 0);
		//mysql_close();
		return $row["accountid"];	    
	}

}

function AddContact($user_name, $first_name, $last_name, $email_address ,$account_name , $salutation , $title, $phone_mobile, $reports_to,$primary_address_street,$primary_address_city,$primary_address_state,$primary_address_postalcode,$primary_address_country,$alt_address_city,$alt_address_street,$alt_address_state,$alt_address_postalcode,$alt_address_country,$office_phone="",$home_phone="",$fax="",$department="",$description="")
{
	global $adb;
	global $current_user;
	require_once('modules/Users/User.php');
	require_once('modules/Contacts/Contact.php');
	
	$seed_user = new User();
	$user_id = $seed_user->retrieve_user_id($user_name);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
  {
    $sql1 = "select fieldname,columnname from vtiger_field where tabid=4 and block <> 75 and block <> 6 and block <> 5";
  }else
  {
    $profileList = getCurrentUserProfileList();
    $sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.block <> 75 and vtiger_field.block <> 6 and vtiger_field.block <> 5 and vtiger_field.displaytype in (1,2,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_profile2field.profileid in ".$profileList;
  }
  $result1 = $adb->query($sql1);
  for($i=0;$i < $adb->num_rows($result1);$i++)
  {
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  }
	
	$contact = new Contact();
	$contact->column_fields[firstname]=in_array('salutation',$permitted_lists) ? $first_name : "";
	$contact->column_fields[lastname]=in_array('lastname',$permitted_lists) ? $last_name : "";
	$contact->column_fields[birthday]=in_array('birthday',$permitted_lists) ? getDisplayDate("0000-00-00") : "";
	$contact->column_fields[email]=in_array('email',$permitted_lists) ? $email_address : "";
	$contact->column_fields[title]=in_array('title',$permitted_lists) ? $title : "";
	$contact->column_fields[department]=in_array('department',$permitted_lists) ? $department : "";
	$contact->column_fields[account_id]=in_array('account_id',$permitted_lists) ? retrieve_account_id($account_name,$user_id) : "";
	$contact->column_fields[phone]=in_array('phone',$permitted_lists) ? $office_phone : "";
	$contact->column_fields[homephone]=in_array('homephone',$permitted_lists) ? $home_phone : "";
	$contact->column_fields[fax]=in_array('fax',$permitted_lists) ? $fax : "";
	$contact->column_fields[mobile]=in_array('mobile',$permitted_lists) ? $phone_mobile : "";
	$contact->column_fields[mailingstreet]=in_array('mailingstreet',$permitted_lists) ? $primary_address_street : "";
	$contact->column_fields[mailingcity]=in_array('mailingcity',$permitted_lists) ? $primary_address_city : "";
	$contact->column_fields[mailingstate]=in_array('mailingstate',$permitted_lists) ? $primary_address_state : "";
	$contact->column_fields[mailingzip]=in_array('mailingzip',$permitted_lists) ? $primary_address_postalcode : "";
	$contact->column_fields[mailingcountry]=in_array('mailingcountry',$permitted_lists) ? $primary_address_country : "";    
	$contact->column_fields[otherstreet]=in_array('otherstreet',$permitted_lists) ? $alt_address_street : "";
	$contact->column_fields[othercity]=in_array('othercity',$permitted_lists) ? $alt_address_city : "";
	$contact->column_fields[otherstate]=in_array('otherstate',$permitted_lists) ? $alt_address_state : "";
	$contact->column_fields[otherzip]=in_array('otherzip',$permitted_lists) ? $alt_address_postalcode : "";
	$contact->column_fields[othercountry]=in_array('othercountry',$permitted_lists) ? $alt_address_country : "";    	
	$contact->column_fields[assigned_user_id]=in_array('assigned_user_id',$permitted_lists) ? $user_id : "";   
	$contact->column_fields[description]= "";
	$contact->save("Contacts");	
	
  $contact = $contact;	
	return $contact->id;
}

function create_session($user_name, $password)
{
        return "TempSessionID";
}

function end_session($user_name)
{
        return "Success";       
}


$server->service($HTTP_RAW_POST_DATA); 
exit(); 
?>
