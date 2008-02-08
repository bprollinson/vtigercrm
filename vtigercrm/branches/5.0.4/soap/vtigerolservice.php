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
require_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/nusoap/nusoap.php');
require_once('include/language/en_us.lang.php');

$log = &LoggerManager::getLogger('vtigerolservice');

error_reporting(0);

$NAMESPACE = 'http://www.vtiger.com/products/crm';

$server = new soap_server;

$server->configureWSDL('vtigerolservice');

//ContactDetails SOAP Structure
$server->wsdl->addComplexType(
    'contactdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
	   'id' => array('name'=>'id','type'=>'xsd:string'),
        'title' => array('name'=>'title','type'=>'xsd:string'),
        'firstname' => array('name'=>'firstname','type'=>'xsd:string'),
        'middlename' => array('name'=>'middlename','type'=>'xsd:string'),
        'lastname' => array('name'=>'lastname','type'=>'xsd:string'),
        'birthdate'=> array('name'=>'birthdate','type'=>'xsd:string'),
        'emailaddress' => array('name'=>'emailaddress','type'=>'xsd:string'),
        'jobtitle'=> array('name'=>'jobtitle','type'=>'xsd:string'),
        'department'=> array('name'=>'department','type'=>'xsd:string'),
        'accountname' => array('name'=>'accountname','type'=>'xsd:string'),
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

$server->wsdl->addComplexType(
    'taskdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
			'id'=>array('name'=>'id','type'=>'xsd:string'),
			'subject'=>array('name'=>'subject','type'=>'xsd:string'),
			'startdate'=>array('name'=>'startdate','type'=>'xsd:string'),
			'duedate'=>array('name'=>'duedate','type'=>'xsd:string'),
			'status'=> array('name'=>'status','type'=>'xsd:string'),
			'priority'=>array('name'=>'priority','type'=>'xsd:string'),
			'description'=>array('name'=>'description','type'=>'xsd:string'),
			'contactname'=>array('name'=>'contactname','type'=>'xsd:string'),
			'category'=>array('name'=>'category','type'=>'xsd:string'),
		  )
);

$server->wsdl->addComplexType(
    'taskdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:taskdetail[]')
    ),
    'tns:taskdetail'
);

$server->wsdl->addComplexType(
    'clndrdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
          'id'=>array('name'=>'id','type'=>'xsd:string'),
          'subject'=>array('name'=>'subject','type'=>'xsd:string'),
          'startdate'=>array('name'=>'startdate','type'=>'xsd:string'),
          'duedate'=>array('name'=>'duedate','type'=>'xsd:string'),
          'location'=> array('name'=>'location','type'=>'xsd:string'),
          'description'=>array('name'=>'description','type'=>'xsd:string'),
          'contactname'=>array('name'=>'contactname','type'=>'xsd:string'),
          'category'=>array('name'=>'category','type'=>'xsd:string'),
        )
);

$server->wsdl->addComplexType(
    'clndrdetails',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:clndrdetail[]')
    ),
    'tns:clndrdetail'
);

$server->wsdl->addComplexType(
    'emailmsgdetail',
    'complexType',
    'struct',
    'all',
    '',
    array(
          'subject'=>array('name'=>'subject','type'=>'xsd:string'),
          'body'=>array('name'=>'body','type'=>'xsd:string'),
          'datesent'=>array('name'=>'datesent','type'=>'xsd:string'),
         )
);


$server->register(
    'LoginToVtiger',
    array('userid'=>'xsd:string','password'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'CheckEmailPermission',
    array('username'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'CheckContactPermission',
    array('username'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'CheckActivityPermission',
    array('username'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);



$server->register(
    'SearchContactsByEmail',
    array('username'=>'xsd:string','emailaddress'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
    'AddMessageToContact',
    array('username'=>'xsd:string','contactid'=>'xsd:string','msgdtls'=>'tns:emailmsgdetail'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
    'AddEmailAttachment',
    array('emailid'=>'xsd:string','filedata'=>'xsd:string',
	  'filename'=>'xsd:string','filesize'=>'xsd:string','filetype'=>'xsd:string',
	  'username'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

//For Contacts Sync
$server->register(
		'GetContacts',
    array('username'=>'xsd:string'),
    array('return'=>'tns:contactdetails'),
    $NAMESPACE);

$server->register(
   'AddContacts',
    array('username'=>'xsd:string','cntdtls'=>'tns:contactdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateContacts',
    array('username'=>'xsd:string','cntdtls'=>'tns:contactdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteContacts',
    array('username'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE);   
//End for Contacts Sync

//For Tasks Sync
$server->register(
		'GetTasks',
    array('username'=>'xsd:string'),
    array('return'=>'tns:taskdetails'),
    $NAMESPACE);

$server->register(
   'AddTasks',
    array('username'=>'xsd:string','taskdtls'=>'tns:taskdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateTasks',
    array('username'=>'xsd:string','taskdtls'=>'tns:taskdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteTasks',
    array('username'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE); 
//End for Tasks Sync

//For Calendar Sync
$server->register(
		'GetClndr',
    array('username'=>'xsd:string'),
    array('return'=>'tns:clndrdetails'),
    $NAMESPACE);

$server->register(
   'AddClndr',
    array('username'=>'xsd:string','clndrdtls'=>'tns:clndrdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'UpdateClndr',
    array('username'=>'xsd:string','clndrdtls'=>'tns:clndrdetails'),
    array('return'=>'xsd:string'),
    $NAMESPACE);

$server->register(
   'DeleteClndr',
    array('username'=>'xsd:string','crmid'=>'xsd:string'),
    array('return'=>'xsd:string'),
    $NAMESPACE); 
//End for Calendar Sync

function SearchContactsByEmail($username,$emailaddress)
{
     require_once('modules/Contacts/Contacts.php');
     
     $seed_contact = new Contacts();
     $output_list = Array();
     
     $response = $seed_contact->get_searchbyemailid($username,$emailaddress);
     $contactList = $response['list'];
     
     // create a return array of names and email addresses.
     foreach($contactList as $contact)
     {
          $output_list[] = Array(
               "id" => $contact[contactid],
               "firstname" => decode_html($contact[firstname]),
               "lastname" => decode_html($contact[lastname]),
               "accountname" => decode_html($contact[accountname]),
               "emailaddress" => decode_html($contact[email]),
          );
     }
     
     //to remove an erroneous compiler warning
     $seed_contact = $seed_contact;
     return $output_list;
}    

function AddMessageToContact($username,$contactid,$msgdtls)
{
	global $current_user;
	global $adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Emails/Emails.php');
	
	$current_user = new Users();
	$user_id = $current_user->retrieve_user_id($username);
	$query = "select email1 from vtiger_users where id = ?";
	$result = $adb->pquery($query, array($user_id));
	$user_emailid = $adb->query_result($result,0,"email1");
	$current_user = $current_user->retrieveCurrentUserInfoFromFile($user_id);
	
	foreach($msgdtls as $msgdtl)
	{
    if(isset($msgdtl))
    {    
        $email = new Emails();
        //$log->debug($msgdtls['contactid']);
	$email_body = str_replace("'", "''", $msgdtl['body']);
	$email_body = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'," ", $email_body);
        $email_subject = str_replace("'", "''",$msgdtl['subject']);
        $date_sent = getDisplayDate($msgdtl['datesent']);
        
        $email->column_fields[subject] = $email_subject;
        $email->column_fields[assigned_user_id] = $user_id;
        $email->column_fields[date_start] = $date_sent;
        $email->column_fields[description]  = $email_body;
        $email->column_fields[activitytype] = 'Emails'; 
        $email->plugin_save = true; 
        $email->save("Emails");
	$query = "select fieldid from vtiger_field where fieldname = 'email' and tabid = 4";
	$result = $adb->pquery($query, array());
	$field_id = $adb->query_result($result,0,"fieldid");
        $email->set_emails_contact_invitee_relationship($email->id,$contactid);
        $email->set_emails_se_invitee_relationship($email->id,$contactid);
	$email->set_emails_user_invitee_relationship($email->id,$user_id);
        $sql = "select email from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid where vtiger_crmentity.deleted =0 and vtiger_contactdetails.contactid=?";
        $result = $adb->pquery($sql, array($contactid));
        $camodulerow = $adb->fetch_array($result);
        if(isset($camodulerow))
        {
            $emailid = $camodulerow["email"];

	    //added to save < as $lt; and > as &gt; in the database so as to retrive the emailID
	    $user_emailid = str_replace('<','&lt;',$user_emailid);
	    $user_emailid = str_replace('>','&gt;',$user_emailid);
			$query = 'insert into vtiger_emaildetails values (?,?,?,?,?,?,?,?)';
            $params = array($email->id, $emailid, $user_emailid, "", "", "", $user_id.'@-1|'.$contactid.'@'.$field_id.'|', "OUTLOOK");
			$adb->pquery($query, $params);
        }
        return $email->id;
		}
		else
		{
			return "";
		}
	}
}

function LoginToVtiger($userid,$password,$version)
{
	global $log;
	global $adb;
	$log->DEBUG("Entered into vtigerCRM with userid".$userid." and Version".$version);
	include('vtigerversion.php');	
	if($version != $vtiger_current_version)
	{
		return "VERSION";
	}
	require_once('modules/Users/Users.php');
	$return_access = "FALSE";
	$objuser = new Users();
	if($password != "")
	{
		$objuser->column_fields['user_name'] = $userid;
		$objuser->load_user($password);
		if($objuser->is_authenticated())
		{
			$return_access = "TRUES";
		}else
		{
			$return_access = "LOGIN";
		}
	}else
	{
			//$server->setError("Invalid username and/or password");
			$return_access = "FALSE";
	}
$log->DEBUG("The return access to outlook was ".$return_access." from vtigerCRM");
	$objuser = $objuser;
	return $return_access;
}

function CheckEmailPermission($username)
{
	global $current_user,$log;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($username);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');

	if(isPermitted("Emails","EditView") == "yes" && (isPermitted("Contacts","index") == 'yes'))
	{
		return "allowed";
	}else
	{
		return "denied";
	}
}

function CheckContactPermission($username)
{
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($username);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');

	if(isPermitted("Contacts","Delete") == "yes" && isPermitted("Contacts","EditView") == "yes")
	{
		return "allowed";
	}else
	{
		return "denied";
	}
}

function CheckActivityPermission($username)
{
	global $current_user;
	require_once("modules/Users/Users.php");
	$seed_user=new Users();
	$user_id=$seed_user->retrieve_user_id($username);
	$current_user=$seed_user;
	$current_user->retrieve_entity_info($user_id, 'Users');

	if(isPermitted("Calendar","Delete") == "yes" && isPermitted("Calendar","EditView") == "yes")
	{
		return "allowed";
	}else
	{
		return "denied";
	}
}

function AddEmailAttachment($emailid,$filedata,$filename,$filesize,$filetype,$username)
{
	global $adb;
	require_once('modules/Users/Users.php');
	require_once('include/utils/utils.php');
	$filename = preg_replace('/\s+/', '_', $filename);//replace space with _ in filename
	$date_var = date('YmdHis');

	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);

	$crmid = $adb->getUniqueID("vtiger_crmentity");

	$upload_file_path = decideFilePath();

	$handle = fopen($upload_file_path.$crmid."_".$filename,"wb");
	fwrite($handle,base64_decode($filedata),$filesize);
	fclose($handle);

	$sql1 = "insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values (?,?,?,?,?,?,?)";
	$params1 = array($crmid, $user_id, $user_id, 'Emails Attachment', ' ', $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
	$entityresult = $adb->pquery($sql1, $params1);	

	$filetype="application/octet-stream";

	if($entityresult != false)
	{
		$sql2="insert into vtiger_attachments(attachmentsid, name, description, type, path) values (?,?,?,?,?)";
		$params2 = array($crmid, $filename, ' ', $filetype, $upload_file_path);
		$result=$adb->pquery($sql2, $params2);

		$sql3='insert into vtiger_seattachmentsrel values(?,?)';
		$adb->pquery($sql3, array($emailid, $crmid));

		return $crmid;   
	}
	else
	{
		//$server->setError("Invalid username and/or password"); 
		return "";
	}
}

function GetContacts($username)
{
	global $adb;
	require_once('modules/Contacts/Contacts.php');

	$seed_contact = new Contacts();
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
		$middlename = "";
		if(isset($namelist))
		{
			if(count($namelist) >= 2) 
			{
				$contact["lastname"] = $namelist[count($namelist)-1];       	
				for($i=0; $i<count($namelist)-2; $i++)
				{
				  if($namelist[$i] != '')
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
				"title" => decode_html($contact["salutation"]),
				"firstname" => decode_html($contact["firstname"]),
				"middlename" => decode_html(trim($middlename)),
				"lastname" => decode_html(trim($contact["lastname"])),
				"birthdate" => $contact["birthday"],
				"emailaddress" => decode_html($contact["email"]),
				"jobtitle" => decode_html($contact["title"]),
				"department" => decode_html($contact["department"]),
				"accountname" => decode_html($contact["accountname"]),  
				"officephone" => decode_html($contact["phone"]),
				"homephone" => decode_html($contact["homephone"]),
				"otherphone" => decode_html($contact["otherphone"]), 
				"fax" => decode_html($contact["fax"]),
				"mobile" => decode_html($contact["mobile"]),
				"asstname" => decode_html($contact["assistant"]),
				"asstphone" => decode_html($contact["assistantphone"]),             
				"reportsto" => decode_html($contact["reports_to_name"]),
				"mailingstreet" => decode_html($contact["mailingstreet"]),
				"mailingcity" => decode_html($contact["mailingcity"]),
				"mailingstate" => decode_html($contact["mailingstate"]),
				"mailingzip" => decode_html($contact["mailingzip"]),
				"mailingcountry" => decode_html($contact["mailingcountry"]),              
				"otherstreet" => decode_html($contact["otherstreet"]),
				"othercity" => decode_html($contact["othercity"]),
				"otherstate" => decode_html($contact["otherstate"]),
				"otherzip" => decode_html($contact["otherzip"]),
				"othercountry" => decode_html($contact["othercountry"]),
				"description" => "",
				"category" => "",        
			  );
	}

	//to remove an erroneous compiler warning
	$seed_contact = $seed_contact;
	return $output_list;
}

function AddContacts($username,$cntdtls)
{
	global $adb;
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	$contact = new Contacts();
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=4 and block <> 75 and block <> 6 and block <> 5";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.block <> 75 and vtiger_field.block <> 6 and vtiger_field.block <> 5 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	foreach($cntdtls as $cntrow)
	{
		if(isset($cntrow))
		{
		  		$contact->column_fields[salutationtype]=in_array('salutationtype',$permitted_lists) ? $cntrow["title"] : "";		
     			$contact->column_fields[firstname]=in_array('firstname',$permitted_lists) ? $cntrow["firstname"] : "";
    			
    			if($cntrow["middlename"] != "")
    			{
    				$contact->column_fields[lastname]=$cntrow["middlename"]." ".$cntrow["lastname"];
    			}elseif($cntrow["lastname"] != "")
    			{
    				$contact->column_fields[lastname]=$cntrow["lastname"];
    			}else
    			{
    			   $contact->column_fields[lastname]=$cntrow["firstname"]." ".$cntrow["middlename"]." ".$cntrow["lastname"];
          		}
    
    			$contact->column_fields[birthday]= in_array('birthday',$permitted_lists) ? getDisplayDate($cntrow["birthdate"]) : "";
    			$contact->column_fields[email]=in_array('email',$permitted_lists) ? $cntrow["emailaddress"] : "";
    			$contact->column_fields[title]=in_array('title',$permitted_lists) ? $cntrow["jobtitle"] : "";
    			$contact->column_fields[department]=in_array('department',$permitted_lists) ? $cntrow["department"] : "";
    			$contact->column_fields[account_id]= in_array('account_id',$permitted_lists) ? retrieve_account_id($cntrow["accountname"],$user_id) : "";
    			$contact->column_fields[phone]= in_array('phone',$permitted_lists) ? $cntrow["officephone"] : "";
    			$contact->column_fields[homephone]= in_array('homephone',$permitted_lists) ? $cntrow["homephone"] : "";
    			$contact->column_fields[otherphone]= in_array('otherphone',$permitted_lists) ? $cntrow["otherphone"] : "";
    			$contact->column_fields[fax]= in_array('fax',$permitted_lists) ? $cntrow["fax"] : "";
    			$contact->column_fields[mobile]=in_array('mobile',$permitted_lists) ? $cntrow["mobile"] : "";
    			$contact->column_fields[assistant]= in_array('assistant',$permitted_lists) ? $cntrow["asstname"] : "";
    			$contact->column_fields[assistantphone]= in_array('assistantphone',$permitted_lists) ? $cntrow["asstphone"] : "";     
    			//$contact->column_fields[reports_to_id] =retrievereportsto($reportsto,$user_id,$account_id);// NOT FIXED IN SAVEENTITY.PHP
    			$contact->column_fields[mailingstreet]=in_array('mailingstreet',$permitted_lists) ? $cntrow["mailingstreet"] : "";
    			$contact->column_fields[mailingcity]=in_array('mailingcity',$permitted_lists) ? $cntrow["mailingcity"] : "";
    			$contact->column_fields[mailingstate]=in_array('mailingstate',$permitted_lists) ? $cntrow["mailingstate"] : "";
    			$contact->column_fields[mailingzip]=in_array('mailingzip',$permitted_lists) ? $cntrow["mailingzip"] : "";
    			$contact->column_fields[mailingcountry]=in_array('mailingcountry',$permitted_lists) ? $cntrow["mailingcountry"] : "";    
    			$contact->column_fields[otherstreet]=in_array('otherstreet',$permitted_lists) ? $cntrow["otherstreet"] : "";
    			$contact->column_fields[othercity]=in_array('othercity',$permitted_lists) ? $cntrow["othercity"] : "";
    			$contact->column_fields[otherstate]=in_array('otherstate',$permitted_lists) ? $cntrow["otherstate"] : "";
    			$contact->column_fields[otherzip]=in_array('otherzip',$permitted_lists) ? $cntrow["otherzip"] : "";
    			$contact->column_fields[othercountry]=in_array('othercountry',$permitted_lists) ? $cntrow["othercountry"] : "";    	
    			$contact->column_fields[assigned_user_id]=in_array('assigned_user_id',$permitted_lists) ? $user_id : "";   
    			$contact->column_fields[description]= in_array('description',$permitted_lists) ? $cntrow["description"] : "";
    			$contact->save("Contacts");	
		  
    }	
	}
	$contact = $contact;	
	return $contact->id;
}

function UpdateContacts($username,$cntdtls)
{
	global $adb;
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	$contact = new Contacts();
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=4 and block <> 75 and block <> 6 and block <> 5";
  		$params1 = array();
	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=4 and vtiger_field.block <> 75 and vtiger_field.block <> 6 and vtiger_field.block <> 5 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	foreach($cntdtls as $cntrow)
	{
		if(isset($cntrow))
		{
			$contact->retrieve_entity_info($cntrow["id"],"Contacts");
			$contact->column_fields[salutationtype]=in_array('salutationtype',$permitted_lists) ? $cntrow["title"] : "";		
			$contact->column_fields[firstname]=in_array('firstname',$permitted_lists) ? $cntrow["firstname"] : "";
			if($cntrow["middlename"] != "")
			{
				$contact->column_fields[lastname]=$cntrow["middlename"]." ".$cntrow["lastname"];
			}elseif($cntrow["lastname"] != "")
			{
				$contact->column_fields[lastname]=$cntrow["lastname"];
			}else
			{
				$contact->column_fields[lastname]=$cntrow["firstname"]." ".$cntrow["middlename"]." ".$cntrow["lastname"];
      			}
      
			$contact->column_fields[birthday]= in_array('birthday',$permitted_lists) ? getDisplayDate($cntrow["birthdate"]) : "";
			$contact->column_fields[email]= in_array('email',$permitted_lists) ? $cntrow["emailaddress"] : "";
			$contact->column_fields[title]= in_array('title',$permitted_lists) ? $cntrow["jobtitle"] : "";
			$contact->column_fields[department]= in_array('department',$permitted_lists) ? $cntrow["department"] : "";
			$contact->column_fields[account_id]= in_array('account_id',$permitted_lists) ? retrieve_account_id($cntrow["accountname"],$user_id) : "";
			$contact->column_fields[phone]= in_array('phone',$permitted_lists) ? $cntrow["officephone"] : "";
			$contact->column_fields[homephone]= in_array('homephone',$permitted_lists) ? $cntrow["homephone"] : "";
			$contact->column_fields[otherphone]= in_array('otherphone',$permitted_lists) ? $cntrow["otherphone"] : "";
			$contact->column_fields[fax]= in_array('fax',$permitted_lists) ? $cntrow["fax"] : "";
			$contact->column_fields[mobile]= in_array('mobile',$permitted_lists) ? $cntrow["mobile"] : "";
			$contact->column_fields[assistant]= in_array('assistant',$permitted_lists) ? $cntrow["asstname"] : "";
			$contact->column_fields[assistantphone]= in_array('assistantphone',$permitted_lists) ? $cntrow["asstphone"] : "";     
			//$contact->column_fields[reports_to_id] =retrievereportsto($reportsto,$user_id,$account_id);// NOT FIXED IN SAVEENTITY.PHP
			$contact->column_fields[mailingstreet]= in_array('mailingstreet',$permitted_lists) ? $cntrow["mailingstreet"] : "";
			$contact->column_fields[mailingcity]= in_array('mailingcity',$permitted_lists) ? $cntrow["mailingcity"] : "";
			$contact->column_fields[mailingstate]= in_array('mailingstate',$permitted_lists) ? $cntrow["mailingstate"] : "";
			$contact->column_fields[mailingzip]= in_array('mailingzip',$permitted_lists) ? $cntrow["mailingzip"] : "";
			$contact->column_fields[mailingcountry]= in_array('mailingcountry',$permitted_lists) ? $cntrow["mailingcountry"] : "";    
			$contact->column_fields[otherstreet]= in_array('otherstreet',$permitted_lists) ? $cntrow["otherstreet"] : "";
			$contact->column_fields[othercity]= in_array('othercity',$permitted_lists) ? $cntrow["othercity"] : "";
			$contact->column_fields[otherstate]= in_array('otherstate',$permitted_lists) ? $cntrow["otherstate"] : "";
			$contact->column_fields[otherzip]= in_array('otherzip',$permitted_lists) ? $cntrow["otherzip"] : "";
			$contact->column_fields[othercountry]= in_array('othercountry',$permitted_lists) ? $cntrow["othercountry"] : "";    	
			$contact->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";   
			$contact->column_fields[description]= in_array('description',$permitted_lists) ? $cntrow["description"] : "";
			$contact->id = $cntrow["id"];
			$contact->mode = "edit";
			$contact->save("Contacts");	
		}	
	}	
	$contact = $contact;
	return $contact->id;
}

function DeleteContacts($username,$crmid)
{
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Contacts/Contacts.php');

	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$contact = new Contacts();
	$contact->id = $crmid;
	$contact->mark_deleted($contact->id);

	$contact = $contact;
	return $contact->id;
}

function retrieve_account_id($account_name,$user_id)
{

	if($account_name=="")
	{
		return null;
	}

	$db = new PearDatabase();
	$query = "select vtiger_account.accountname accountname,vtiger_account.accountid accountid from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid where vtiger_crmentity.deleted=0 and vtiger_account.accountname=?";
	$result=  $db->pquery($query, array($account_name)) or die ("Not able to execute insert");

	$rows_count =  $db->getRowCount($result);
	if($rows_count==0)
	{
		require_once('modules/Accounts/Accounts.php');
		$account = new Accounts();
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

function GetTasks($username)
{
	global $adb,$log;
	require_once('modules/Calendar/Activity.php');
		
	$seed_task = new Activity();
	$output_list = Array();
  
	$query = $seed_task->get_tasksforol($username);
	$result = $adb->query($query);
    
	while($task = $adb->fetch_array($result))
	{
  		if($task["date_start"] == "0000-00-00" || $task["date_start"] == NULL)
        	{
		       	$task["date_start"] = "";
        	}
        	if($task["due_date"] == "0000-00-00" || $task["due_date"] == NULL)
        	{
		       	$task["due_date"] = "";
        	}
        
        	if($task["status"] == "Not Started")
        	{
       			$task["status"] = "0";
        	}else if($task["status"] == "In Progress")
        	{
        		$task["status"] = "1";
        	}else if($task["status"] == "Completed")
        	{
        		$task["status"] = "2";
        	}else if($task["status"] == "Deferred")
        	{
        		$task["status"] = "4";
        	}else if($task["status"] == "Pending Input" || $task["status"] == "Planned")
        	{
        		$task["status"] = "3";
        	}else
        	{
        		$task["status"] = "0";
        	}
        
        	if($task["priority"] == "High")
        	{
       			$task["priority"] = "2";
        	}else if($task["priority"] == "Low")
        	{
        		$task["priority"] = "0";
        	}else if($task["priority"] == "Medium")
        	{
        		$task["priority"] = "1";
        	}
        
		$output_list[] = Array(
						"id" => $task["taskid"],
						"subject" => decode_html($task["subject"]),
						"startdate" => $task["date_start"],
						"duedate" => $task["due_date"],
						"status" => decode_html($task["status"]),
						"priority" => decode_html($task["priority"]),
						"description" => decode_html($task["description"]),
						"contactname" => decode_html($task["firstname"])." ".decode_html($task["lastname"]),
						"category" => "",        
						);
	}
	$seed_task = $seed_task;
	return $output_list;
}

function AddTasks($username,$taskdtls)
{
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=9";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=9 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	$task = new Activity();
	
	foreach($taskdtls as $taskrow)
	{
	//Currently only 3 status avail Note ************************************************
   		if(isset($taskrow))
   		{
			if($taskrow["status"] == "0")
			{
				$taskrow["status"] = "Not Started";
			}else if($taskrow["status"] == "1")
			{
				$taskrow["status"] = "In Progress";
			}else if($taskrow["status"] == "2")
			{
				$taskrow["status"] = "Completed";
			}else if($taskrow["status"] == "4")
			{
				$taskrow["status"] = "Deferred";
			}else if($taskrow["status"] == "3")
			{
				$taskrow["status"] = "Planned";
			}else
			{
				$taskrow["status"] = "Not Started";
			}

			if($taskrow["priority"] == "2")
			{
				$taskrow["priority"] = "High";
			}else if($taskrow["priority"] == "0")
			{
				$taskrow["priority"] = "Low";
			}else if($taskrow["priority"] == "1")
			{
				$taskrow["priority"] = "Medium";
			}

			$task->column_fields[subject] = in_array('subject',$permitted_lists) ? $taskrow["subject"] : "";
			$task->column_fields[date_start]= in_array('date_start',$permitted_lists) ? getDisplayDate($taskrow["startdate"]) : "";
			$task->column_fields[due_date]= in_array('due_date',$permitted_lists) ? getDisplayDate($taskrow["duedate"]) : "";         
			$task->column_fields[taskstatus]= in_array('taskstatus',$permitted_lists) ? $taskrow["status"] : "";
			$task->column_fields[taskpriority]= in_array('taskpriority',$permitted_lists) ? $taskrow["priority"] : "";
			$task->column_fields[description]= in_array('description',$permitted_lists) ? $taskrow["description"] : "";
			$task->column_fields[activitytype]="Task";
			//$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
			$task->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$task->save("Calendar");
		  }
	}
	return $task->id;
}

function UpdateTasks($username,$taskdtls)
{
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=9";
  		$params1 = array();	
	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=9 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
  
	$task = new Activity();
	
	foreach($taskdtls as $taskrow)
	{
		if(isset($taskrow))
		{
			if($taskrow["status"] == "0")
			{
				$taskrow["status"] = "Not Started";
			}else if($taskrow["status"] == "1")
			{
				$taskrow["status"] = "In Progress";
			}else if($taskrow["status"] == "2")
			{
				$taskrow["status"] = "Completed";
			}else if($taskrow["status"] == "4")
			{
				$taskrow["status"] = "Deferred";
			}else if($taskrow["status"] == "3")
			{
				$taskrow["status"] = "Planned";
			}else
			{
				$taskrow["status"] = "Not Started";
			}
        
    	if($taskrow["priority"] == "2")
			{
      	$taskrow["priority"] = "High";
   		}else if($taskrow["priority"] == "0")
   		{
   			$taskrow["priority"] = "Low";
   		}else if($taskrow["priority"] == "1")
   		{
   		 	$taskrow["priority"] = "Medium";
   		}
		
			$task->retrieve_entity_info($taskrow["id"],"Calendar");
			$task->column_fields[subject] = in_array('subject',$permitted_lists) ? $taskrow["subject"] : "";
			$task->column_fields[date_start] = in_array('date_start',$permitted_lists) ? getDisplayDate($taskrow["startdate"]) : "";
			$task->column_fields[due_date] = in_array('due_date',$permitted_lists) ? getDisplayDate($taskrow["duedate"]) : "";         
			$task->column_fields[taskstatus] = in_array('taskstatus',$permitted_lists) ? $taskrow["status"] : "";
			$task->column_fields[taskpriority] = in_array('taskpriority',$permitted_lists) ? $taskrow["priority"] : "";
			$task->column_fields[description] = in_array('description',$permitted_lists) ? $taskrow["description"] : "";
			$task->column_fields[activitytype] = "Task";
			//$task->column_fields[contact_id]= retrievereportsto($contact_name,$user_id,null); 
			$task->column_fields[assigned_user_id] = in_array('assigned_user_id',$permitted_lists) ? $user_id : "";

			$task->id = $taskrow["id"];
			$task->mode="edit";

			$task->save("Calendar");
		}
	}
	return $task->id;
}

function DeleteTasks($username,$crmid)
{
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	   
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$task = new Activity();
	$task->id = $crmid;
	$task->mark_deleted($task->id);
	return $task->id;     
}

function GetClndr($username)
{
	global $adb,$log;
	require_once('modules/Calendar/Activity.php');

	$seed_clndr = new Activity();
	$output_list = Array();

	$query = $seed_clndr->get_calendarsforol($username);
	$result = $adb->query($query);
    
	while($clndr = $adb->fetch_array($result))
	{
		if($clndr["date_start"] == "0000-00-00" || $clndr["date_start"] == NULL)
		{
			$clndr["date_start"] = "";
		}
		if($clndr["due_date"] == "0000-00-00" || $clndr["due_date"] == NULL)
		{
			$clndr["due_date"] = "";
		}

		//this seperates the $$clndr["startdate"] into an array - YYYY-MM-DD
		$expldstartdate = explode("-", $clndr["date_start"]);

		$expldtimestart = Array(0,0);
		if(strpos($clndr["time_start"],":"))
			$expldtimestart = explode(":", $clndr["time_start"]);	

		//this makes a timestamp out of the exploded date this number is in seconds
		$startdtm = mktime($expldtimestart[0], $expldtimestart[1], 0, $expldstartdate[1], $expldstartdate[2], $expldstartdate[0]);

		$expldduedate = explode("-", $clndr["due_date"]);

		$expldtimeend = Array(0,0);
		if(strpos($clndr["time_end"],":"))
			$expldtimeend = explode(":", $clndr["time_end"]);
		//this makes a timestamp out of the exploded date this number is in seconds
		$duedtm = mktime($expldtimeend[0], $expldtimeend[1], 0, $expldduedate[1], $expldduedate[2], $expldduedate[0]);

		$clndr["date_start"] = date("Y-m-d H:i:s", $startdtm);
		$clndr["due_date"] = date("Y-m-d H:i:s", $duedtm);

		$output_list[] = Array(
			"id" => $clndr["clndrid"],
			"subject" => decode_html($clndr["subject"]),
			"startdate" => $clndr["date_start"],
			"duedate" => $clndr["due_date"],
			"location" => decode_html($clndr["location"]),
			"description" => decode_html($clndr["description"]),
			"contactname" => decode_html($clndr["firstname"])." ".decode_html($clndr["lastname"]),
			"category" => "",        
		);
	}
	//$log->fatal($output_list);
	$seed_clndr = $seed_clndr;
	return $output_list;
}

function AddClndr($username,$clndrdtls)
{
	global $current_user,$adb;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=16";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=16 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
  
	$clndr = new Activity();
	
	foreach($clndrdtls as $clndrow)
	{
		if(isset($clndrow))
		{
			$astartdtm = explode(" ",$clndrow["startdate"]);
			$aduedtm = explode(" ",$clndrow["duedate"]);
			
			$atimestart = explode(":",trim($astartdtm[1]));
			$atimedue = explode(":",trim($aduedtm[1]));

			$stimestart = $atimestart[0].":".$atimestart[1];
			$stimeend = $atimedue[0].":".$atimedue[1];
		
			/*if( $diff=@get_time_difference($stimestart, $stimeend) )
			{
				$stimeduehr = sprintf('%02d',$diff['hours']);
				$stimeduemin = sprintf('%02d',$diff['minutes']);
			}*/

			$clndr->column_fields[subject] = in_array('subject',$permitted_lists) ? $clndrow["subject"] : "";
			$clndr->column_fields[date_start]= in_array('date_start',$permitted_lists) ? getDisplayDate(trim($astartdtm[0])) : "";
			$clndr->column_fields[due_date]= in_array('due_date',$permitted_lists) ? getDisplayDate(trim($aduedtm[0])) : ""; 
			$clndr->column_fields[time_start]= in_array('time_start',$permitted_lists) ? $stimestart : "";
			$clndr->column_fields[time_end]= in_array('time_end',$permitted_lists) ? $stimeend : "";
			//$clndr->column_fields[duration_hours]= in_array('duration_hours',$permitted_lists) ? $stimeduehr : "";        
			//$clndr->column_fields[duration_minutes]= in_array('duration_minutes',$permitted_lists) ? $stimeduemin : "";
        
			$clndr->column_fields[location]= in_array('location',$permitted_lists) ? $clndrow["location"] : "";
			$clndr->column_fields[description]= in_array('description',$permitted_lists) ? $clndrow["description"] : "";
			$clndr->column_fields[activitytype]="Meeting";
			$clndr->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$clndr->save("Calendar");
		}
	}
	return $clndr->id;
}

function UpdateClndr($username,$clndrdtls)
{
	global $current_user;
	global $adb,$log;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");
	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0) {
    	$sql1 = "select fieldname,columnname from vtiger_field where tabid=16";
		$params1 = array();
  	} else {
    	$profileList = getCurrentUserProfileList();
    	$sql1 = "select fieldname,columnname from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=16 and vtiger_field.displaytype in (1,2,4,3) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0";
  		$params1 = array();
		if (count($profileList) > 0) {
			$sql1 .= " and vtiger_profile2field.profileid in (". generateQuestionMarks($profileList) .")";
			array_push($params1, $profileList);
		}
	}
  	$result1 = $adb->pquery($sql1, $params1);
  
  	for($i=0;$i < $adb->num_rows($result1);$i++)
  	{
      $permitted_lists[] = $adb->query_result($result1,$i,'fieldname');
  	}
	
	$clndr = new Activity();
	
	foreach($clndrdtls as $clndrow)
	{
		if(isset($clndrow))
		{
			$astartdtm = explode(" ",$clndrow["startdate"]);
			$aduedtm = explode(" ",$clndrow["duedate"]);
			
			$atimestart = explode(":",trim($astartdtm[1]));
			$atimedue = explode(":",trim($aduedtm[1]));

			$stimestart = $atimestart[0].":".$atimestart[1];
			$stimeend = $atimedue[0].":".$atimedue[1];
		
			/*if( $diff=@get_time_difference($stimestart, $stimeend) )
			{
				$stimeduehr = sprintf('%02d',$diff['hours']);
				$stimeduemin = sprintf('%02d',$diff['minutes']);
			}*/

			$clndr->retrieve_entity_info($clndrow["id"],"Calendar");
			$clndr->column_fields[subject] = in_array('subject',$permitted_lists) ? $clndrow["subject"] : "";
			$clndr->column_fields[date_start]= in_array('date_start',$permitted_lists) ? getDisplayDate(trim($astartdtm[0])) : "";
			$clndr->column_fields[due_date]= in_array('due_date',$permitted_lists) ? getDisplayDate(trim($aduedtm[0])) : ""; 
			$clndr->column_fields[time_start]= in_array('time_start',$permitted_lists) ? $stimestart : "";
			$clndr->column_fields[time_end]= in_array('time_end',$permitted_lists) ? $stimeend : "";
			//$clndr->column_fields[duration_hours]= in_array('duration_hours',$permitted_lists) ? $stimeduehr : "";       
			//$clndr->column_fields[duration_minutes]= in_array('duration_minutes',$permitted_lists) ? $stimeduemin : "";              
			$clndr->column_fields[location]= in_array('location',$permitted_lists) ? $clndrow["location"] : "";
			$clndr->column_fields[description]= in_array('description',$permitted_lists) ? $clndrow["description"] : "";
			$clndr->column_fields[activitytype]="Meeting";
			$clndr->column_fields[assigned_user_id]= in_array('assigned_user_id',$permitted_lists) ? $user_id : "";
			$clndr->id = $clndrow["id"];
			$clndr->mode="edit";
			$clndr->save("Calendar");
		}
	}
	return $clndr->id;
}

function DeleteClndr($username,$crmid)
{
	global $current_user;
	require_once('modules/Users/Users.php');
	require_once('modules/Calendar/Activity.php');
	   
	$seed_user = new Users();
	$user_id = $seed_user->retrieve_user_id($username);
	$current_user = $seed_user;
	$current_user->retrieve_entity_info($user_id,"Users");

	$clndr = new Activity();
	$clndr->id = $crmid;
	$clndr->mark_deleted($clndr->id);
	return $clndr->id;     
}

//To find the Difference between time
function get_time_difference( $start, $end )
{
	$uts['start'] = strtotime( $start );
	$uts['end'] = strtotime( $end );
	if( $uts['start']!==-1 && $uts['end']!==-1 )
	{
		if( $uts['end'] >= $uts['start'] )
		{
			$diff    =    $uts['end'] - $uts['start'];
			if( $days=intval((floor($diff/86400))) )
			$diff = $diff % 86400;
			if( $hours=intval((floor($diff/3600))) )
			$diff = $diff % 3600;
			if( $minutes=intval((floor($diff/60))) )
			$diff = $diff % 60;
			$diff    =    intval( $diff );            
			return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
		}
	}
	return( false );
}

$server->service($HTTP_RAW_POST_DATA); 
exit();
?>
