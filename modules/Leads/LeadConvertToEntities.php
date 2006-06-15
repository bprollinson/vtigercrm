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

require_once('include/database/PearDatabase.php');
require_once('modules/Leads/Lead.php');
//Getting the Parameters from the ConvertLead Form
$id = $_REQUEST["record"];


$module = $_REQUEST["module"];
$assigned_user_id = $_REQUEST["smowerid"];
$createpotential = $_REQUEST["createpotential"];
$potential_name = $_REQUEST["potential_name"];
$close_date = getDBInsertDateValue($_REQUEST["closedate"]);
$current_user_id = $_REQUEST["current_user_id"];
$assigned_user_id = $_REQUEST["assigned_user_id"];
$accountname = $_REQUEST['account_name'];
$potential_amount = $_REQUEST['potential_amount'];
$potential_sales_stage = $_REQUEST['potential_sales_stage'];

global $log;
$log->debug("id is ".$id);
$log->debug("assigned_user_id is ".$assigned_user_id);
$log->debug("createpotential is ".$createpotential);
$log->debug("close date is ".$close_date);
$log->debug("current user id is ".$current_user_id);
$log->debug("assigned user id is ".$assigned_user_id);
$log->debug("accountname is ".$accountname);
$log->debug("module is ".$module);

$check_unit = explode("-",$potential_name);
if($check_unit[1] == "")
        $potential_name = $check_unit[0];

//Retrieve info from all the vtiger_tables related to leads
  $focus = new Lead();
 $focus->retrieve_entity_info($id,"Leads");

//get all the lead related columns 
$row = $focus->column_fields;
$date_entered;
$date_modified;

$date_entered = date('YmdHis');
$date_modified = date('YmdHis');

$crmid = $adb->getUniqueID("crmentity");

//function for getting the custom values from leads and saving to vtiger_account/contact/potential custom vtiger_fields -Jag
function getInsertValues($type,$type_id)
{
	global $id,$adb,$log;
	$log->debug("Entering getInsertValues(".$type.",".$type_id.") method ...");
	$sql_convert_lead="select * from vtiger_convertleadmapping ";
	$convert_result = $adb->query($sql_convert_lead);
	$noofrows = $adb->num_rows($convert_result);

	for($i=0;$i<$noofrows;$i++)
	{
		$flag="false";
	 	$log->info("In vtiger_convertleadmapping function");
		$lead_id=$adb->query_result($convert_result,$i,"leadfid");  
		//Getting the relatd customfields for Accounts/Contact/potential from vtiger_convertleadmapping vtiger_table	
		$account_id_val=$adb->query_result($convert_result,$i,"accountfid");
		$contact_id_val=$adb->query_result($convert_result,$i,"contactfid");
		$potential_id_val=$adb->query_result($convert_result,$i,"potentialfid");
		
		$sql_leads_column="select vtiger_field.fieldid,vtiger_field.columnname from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name='Leads' and fieldid=".$lead_id; //getting the columnname for the customfield of the lead

		 $log->debug("Lead's custom vtiger_field coumn name is ".$sql_leads_column);

		$lead_column_result = $adb->query($sql_leads_column);
		$leads_no_rows = $adb->num_rows($lead_column_result);
		if($leads_no_rows>0)
		{
			$lead_column_name=$adb->query_result($lead_column_result,0,"columnname");
			$sql_leads_val="select ".$lead_column_name." from vtiger_leadscf where leadid=".$id; //custom vtiger_field value for lead
			$lead_val_result = $adb->query($sql_leads_val);
			$lead_value=$adb->query_result($lead_val_result,0,$lead_column_name);
			 $log->debug("Lead's custom vtiger_field value is ".$lead_value);
		}	
		//Query for getting the column name for Accounts/Contacts/Potentials if custom vtiger_field for lead is mappped
		$sql_type="select vtiger_field.fieldid,vtiger_field.columnname from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name="; 
		if($type=="Accounts")
		{
			if($account_id_val!="" && $account_id_val!=0)	
			{
				$flag="true";
				 $log->info("Getting the  Accounts custom vtiger_field column name  ");
				$sql_type.="'Accounts' and fieldid=".$account_id_val;
			}
		}
		else if($type == "Contacts")
		{	
			if($contact_id_val!="" && $contact_id_val!=0)	
			{
				$flag="true";
				 $log->info("Getting the  Contacts custom vtiger_field column name  ");
				$sql_type.="'Contacts' and fieldid=".$contact_id_val;
			}
		}
		else if($type == "Potentials")
		{
			if($potential_id_val!="" && $potential_id_val!=0)
                        {
				$flag="true";
                                  $log->info("Getting the  Potentials custom vtiger_field column name  ");
				$sql_type.="'Potentials' and fieldid=".$potential_id_val;
                        }

		}
		if($flag=="true")
		{ 
			$type_result=$adb->query($sql_type);
		
			if(isset($type_insert_column))
				$type_insert_column.=",";
                	$type_insert_column.=$adb->query_result($type_result,0,"columnname") ;

			if(isset($insert_value))
				$insert_value.=",";
			
			$insert_value.="'".$adb->query_result($lead_val_result,0,$lead_column_name)."'";
		}

	}
	 $log->debug("columns to be inserted are ".$type_insert_column);
        $log->debug("columns to be inserted are ".$insert_value);
	$values = array ($type_insert_column,$insert_value);
	$log->debug("Exiting getInsertValues method ...");
	return $values;	
}
//function Ends

function getRelatedNotesAttachments($id,$accountid)
{
	global $adb,$log,$id;
	$log->debug("Entering getRelatedNotesAttachments(".$id.",".$accountid.") method ...");
	
	$sql_lead_notes	="select * from vtiger_senotesrel where crmid=".$id;
	$lead_notes_result = $adb->query($sql_lead_notes);
	$noofrows = $adb->num_rows($lead_notes_result);

	for($i=0; $i<$noofrows;$i++ )
	{

		$lead_related_note_id=$adb->query_result($lead_notes_result,$i,"notesid");
		 $log->debug("Lead related note id ".$lead_related_note_id);
		$sql_delete_lead_notes="delete from vtiger_senotesrel where crmid=".$id;
		$adb->query($sql_delete_lead_notes);

		$sql_insert_account_notes="insert into vtiger_senotesrel(crmid,notesid) values (".$accountid.",".$lead_related_note_id.")";
		$adb->query($sql_insert_account_notes);
	}

	$sql_lead_attachment="select * from vtiger_seattachmentsrel where crmid=".$id;
        $lead_attachment_result = $adb->query($sql_lead_attachment);
        $noofrows = $adb->num_rows($lead_attachment_result);

        for($i=0;$i<$noofrows;$i++)
        {
						
                $lead_related_attachment_id=$adb->query_result($lead_attachment_result,$i,"attachmentsid");
		 $log->debug("Lead related attachment id ".$lead_related_attachment_id);

                $sql_delete_lead_attachment="delete from vtiger_seattachmentsrel where crmid=".$id;
                $adb->query($sql_delete_lead_attachment);

                $sql_insert_account_attachment="insert into vtiger_seattachmentsrel(crmid,attachmentsid) values (".$accountid.",".$lead_related_attachment_id.")";                        
                $adb->query($sql_insert_account_attachment);
        }
	$log->debug("Exiting getRelatedNotesAttachments method ...");
	
}

function getRelatedActivities($accountid,$contact_id)
{
	global $adb,$log,$id;	
	$log->debug("Entering getRelatedActivities(".$accountid.",".$contact_id.") method ...");
	$sql_lead_activity="select * from vtiger_seactivityrel where crmid=".$id;
	$lead_activity_result = $adb->query($sql_lead_activity);
        $noofrows = $adb->num_rows($lead_activity_result);
        for($i=0;$i<$noofrows;$i++)
        {

                $lead_related_activity_id=$adb->query_result($lead_activity_result,$i,"activityid");
		 $log->debug("Lead related vtiger_activity id ".$lead_related_activity_id);

		$sql_type_email="select setype from vtiger_crmentity where crmid=".$lead_related_activity_id;
		$type_email_result = $adb->query($sql_type_email);
                $type=$adb->query_result($type_email_result,0,"setype");
		$log->debug("type of vtiger_activity id ".$type);

                $sql_delete_lead_activity="delete from vtiger_seactivityrel where crmid=".$id;
                $adb->query($sql_delete_lead_activity);

		if($type != "Emails")
		{
                	$sql_insert_account_activity="insert into vtiger_seactivityrel(crmid,activityid) values (".$accountid.",".$lead_related_activity_id.")";
	                $adb->query($sql_insert_account_activity);

			$sql_insert_account_activity="insert into vtiger_cntactivityrel(contactid,activityid) values (".$contact_id.",".$lead_related_activity_id.")";
                	$adb->query($sql_insert_account_activity);
		}
		else
		{
			 $sql_insert_account_activity="insert into vtiger_seactivityrel(crmid,activityid) values (".$contact_id.",".$lead_related_activity_id.")";                                                                                     $adb->query($sql_insert_account_activity);
		}
        }
	$log->debug("Exiting getRelatedActivities method ...");
	
}

$sql_crmentity = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,createdtime,modifiedtime,deleted,description) values(".$crmid.",".$current_user_id.",".$assigned_user_id.",'Accounts',1,".$date_entered.",".$date_modified.",0,'".$row['description']."')";

$adb->query($sql_crmentity);


$sql_insert_account = "INSERT INTO vtiger_account (accountid,accountname,industry,annualrevenue,phone,fax,rating,email1,website,employees) VALUES (".$crmid.",'".$accountname ."','".$row["industry"] ."','" .$row["annualrevenue"] ."','" .$row["phone"] ."','".$row["fax"] ."','" .$row["rating"] ."','" .$row["email"] ."','" .$row["website"] ."','" .$row["noofemployees"] ."')";


$adb->query($sql_insert_account);

$sql_insert_accountbillads = "INSERT INTO vtiger_accountbillads (accountaddressid,city,code,country,state,street) VALUES (".$crmid.",'".$row["city"] ."','" .$row["code"] ."','" .$row["country"] ."','".$row["state"] ."','" .$row["lane"]."')";

 $adb->query($sql_insert_accountbillads);


$sql_insert_accountshipads = "INSERT INTO vtiger_accountshipads (accountaddressid,city,code,country,state,street) VALUES (".$crmid.",'".$row["city"] ."','" .$row["code"] ."','" .$row["country"] ."','".$row["state"] ."','" .$row["lane"]."')";


 $adb->query($sql_insert_accountshipads);

//Getting the custom vtiger_field values from leads and inserting into Accounts if the vtiger_field is mapped - Jaguar
$insert_value=$crmid;
$insert_column="accountid";	
$val= getInsertValues("Accounts",$insert_value);
if($val[0]!="")
	$insert_column.=",";
if($val[1]!="")
	$insert_value.=",";

$insert_column.=$val[0];
$insert_value.=$val[1];
$sql_insert_accountcustomfield = "INSERT INTO vtiger_accountscf (".$insert_column.") VALUES (".$insert_value.")";

$adb->query($sql_insert_accountcustomfield);

//


$acccount_id=$crmid;
getRelatedNotesAttachments($id,$crmid); //To Convert Related Notes & Attachments -Jaguar

 $date_entered = date('YmdHis');
 $date_modified = date('YmdHis');

$crmcontactid = $adb->getUniqueID("crmentity");
$sql_crmentity1 = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,deleted,description,createdtime) values(".$crmcontactid.",".$current_user_id.",".$assigned_user_id.",'Contacts',0,0,'".$row['description']."','".$date_entered."')";

$adb->query($sql_crmentity1);


$contact_id = $crmcontactid;
$log->debug("contact id is ".$contact_id);

 $sql_insert_contact = "INSERT INTO vtiger_contactdetails (contactid,accountid,salutation,firstname,lastname,email,phone,mobile,title,fax,yahooid) VALUES (".$contact_id.",".$crmid.",'".$row["salutationtype"] ."','" .$row["firstname"] ."','" .$row["lastname"] ."','" .$row["email"] ."','" .$row["phone"]. "','" .$row["mobile"] ."','" .$row["designation"] ."','".$row["fax"] ."','".$row['yahooid']."')";

$adb->query($sql_insert_contact);


 $sql_insert_contactsubdetails = "INSERT INTO vtiger_contactsubdetails (contactsubscriptionid,homephone,otherphone,leadsource) VALUES (".$contact_id.",'','','".$row['leadsource']."')";

$adb->query($sql_insert_contactsubdetails);

 $sql_insert_contactaddress = "INSERT INTO vtiger_contactaddress (contactaddressid,mailingcity,mailingstreet,mailingstate,mailingcountry,mailingzip) VALUES (".$contact_id.",'".$row["city"] ."','" .$row["lane"] ."','".$row['state']."','" .$row["country"] ."','".$row['code']."')";

$adb->query($sql_insert_contactaddress);


//Getting the customfield values from leads and inserting into the respected ContactCustomfield to which it is mapped - Jaguar
$insert_column="contactid";
$insert_value=$contact_id;
$val= getInsertValues("Contacts",$contact_id);

if($val[0]!="")
	$insert_column.=",";	
if($val[1]!="")
	$insert_value.=",";	

$insert_column.=$val[0];
$insert_value.=$val[1];
$sql_insert_contactcustomfield = "INSERT INTO vtiger_contactscf (".$insert_column.") VALUES (".$insert_value.")";

$adb->query($sql_insert_contactcustomfield);
//

getRelatedActivities($acccount_id,$contact_id); //To convert relates Activites  and Email -Jaguar

if(! isset($createpotential) || ! $createpotential == "on")
{
   $log->info("createpotential is not set");
  $date_entered = date('YmdHis');
  $date_modified = date('YmdHis');
  

  $oppid = $adb->getUniqueID("crmentity");
  $sql_crmentity = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,deleted,createdtime,description) values(".$oppid.",".$current_user_id.",".$assigned_user_id.",'Potentials',0,0,'".$date_entered."','".$row['description']."')";
  
  $adb->query($sql_crmentity);


	if(!isset($potential_amount) || $potential_amount == null)
	{
		$potential_amount=0;
        }

	$sql_insert_opp = "INSERT INTO vtiger_potential (potentialid,accountid,potentialname,leadsource,closingdate,sales_stage,amount) VALUES (".$oppid.",".$crmid .",'".$potential_name."','".$row['leadsource']."','".$close_date."','".$potential_sales_stage."',".$potential_amount.")";

	$adb->query($sql_insert_opp);

//Getting the customfield values from leads and inserting into the respected PotentialCustomfield to which it is mapped - Jaguar
	$insert_column="potentialid";
	$insert_value=$oppid;
	$val= getInsertValues("Potentials",$oppid);
	if($val[0]!="")
		$insert_column.=",";		
	if($val[1]!="")
		$insert_value.=",";		
	
	$insert_column.=$val[0];
	$insert_value.=$val[1];

	$sql_insert_potentialcustomfield = "INSERT INTO vtiger_potentialscf (".$insert_column.") VALUES (".$insert_value.")";
//

	$adb->query($sql_insert_potentialcustomfield);
        $sql_insert2contpotentialrel ="insert into vtiger_contpotentialrel values(".$contact_id.",".$oppid .")";
        
        $adb->query($sql_insert2contpotentialrel);

	
}

//Deleting from the vtiger_tracker
$sql_delete_tracker= "DELETE from vtiger_tracker where item_id='" .$id ."'";
$adb->query($sql_delete_tracker);

//Updating the deleted status
$sql_update_converted = "UPDATE vtiger_leaddetails SET converted = 1 where leadid='" .$id ."'";
$adb->query($sql_update_converted); 

header("Location: index.php?action=DetailView&module=Accounts&record=$crmid");

?>
