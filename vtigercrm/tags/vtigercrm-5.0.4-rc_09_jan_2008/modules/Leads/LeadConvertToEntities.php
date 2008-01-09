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
require_once('modules/Leads/Leads.php');
require_once('include/ComboUtil.php');
//Getting the Parameters from the ConvertLead Form
$id = $_REQUEST["record"];

$module = $_REQUEST["module"];
$createpotential = $_REQUEST["createpotential"];
$potential_name = $_REQUEST["potential_name"];
$close_date = getDBInsertDateValue($_REQUEST["closedate"]);
$current_user_id = $_REQUEST["current_user_id"];
$assigned_user_id = $_REQUEST["assigned_user_id"];
$accountname = $_REQUEST['account_name'];
$potential_amount = $_REQUEST['potential_amount'];
$potential_sales_stage = $_REQUEST['potential_sales_stage'];

global $log,$current_user;
require('user_privileges/user_privileges_'.$current_user->id.'.php');
$log->debug("id = $id \n assigned_user_id = $assigned_user_id \n createpotential = $createpotential \n close date = $close_date \n current user id = $current_user_id \n accountname = $accountname \n module = $module");

$rate_symbol=getCurrencySymbolandCRate($user_info['currency_id']);
$rate = $rate_symbol['rate'];
if($potential_amount != '')
        $potential_amount = convertToDollar($potential_amount,$rate);
	
$check_unit = explode("-",$potential_name);
if($check_unit[1] == "")
        $potential_name = $check_unit[0];

//Retrieve info from all the vtiger_tables related to leads
$focus = new Leads();
$focus->retrieve_entity_info($id,"Leads");

//get all the lead related columns 
$row = $focus->column_fields;

$date_entered = $adb->formatDate(date('YmdHis'), true);
$date_modified = $adb->formatDate(date('YmdHis'), true);

/** Function for getting the custom values from leads and saving to vtiger_account/contact/potential custom vtiger_fields.
 *  @param string $type - Field Type (eg: text, list)
 *  @param integer $type_id - Field Type ID 
*/
function getInsertValues($type,$type_id)
{
	global $id,$adb,$log;
	$log->debug("Entering getInsertValues(".$type.",".$type_id.") method ...");
	$sql_convert_lead="select * from vtiger_convertleadmapping ";
	$convert_result = $adb->pquery($sql_convert_lead, array());
	$noofrows = $adb->num_rows($convert_result);

	$value_cf_array = Array();
	for($i=0;$i<$noofrows;$i++)
	{
		$flag="false";
	 	$log->info("In vtiger_convertleadmapping function");
		$lead_id=$adb->query_result($convert_result,$i,"leadfid");  
		//Getting the relatd customfields for Accounts/Contact/potential from vtiger_convertleadmapping vtiger_table	
		$account_id_val=$adb->query_result($convert_result,$i,"accountfid");
		$contact_id_val=$adb->query_result($convert_result,$i,"contactfid");
		$potential_id_val=$adb->query_result($convert_result,$i,"potentialfid");
		
		$sql_leads_column="select vtiger_field.uitype,vtiger_field.fieldid,vtiger_field.columnname from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name='Leads' and fieldid=?"; //getting the columnname for the customfield of the lead
		 $log->debug("Lead's custom vtiger_field coumn name is ".$sql_leads_column);

		$lead_column_result = $adb->pquery($sql_leads_column, array($lead_id));
		$leads_no_rows = $adb->num_rows($lead_column_result);
		if($leads_no_rows>0)
		{
			$lead_column_name=$adb->query_result($lead_column_result,0,"columnname");
			$lead_uitype=$adb->query_result($lead_column_result,0,"uitype");
			$sql_leads_val="select $lead_column_name from vtiger_leadscf where leadid=?"; //custom vtiger_field value for lead
			$lead_val_result = $adb->pquery($sql_leads_val, array($id));
			$lead_value=$adb->query_result($lead_val_result,0,$lead_column_name);
			 $log->debug("Lead's custom vtiger_field value is ".$lead_value);
		}	
		//Query for getting the column name for Accounts/Contacts/Potentials if custom vtiger_field for lead is mappped
		$sql_type="select vtiger_field.fieldid,vtiger_field.uitype,vtiger_field.columnname from vtiger_field,vtiger_tab where vtiger_field.tabid=vtiger_tab.tabid and generatedtype=2 and vtiger_tab.name="; 
		$params = array();
		if($type=="Accounts")
		{
			if($account_id_val!="" && $account_id_val!=0)	
			{
				$flag="true";
				$log->info("Getting the  Accounts custom vtiger_field column name  ");
				$sql_type.="'Accounts' and fieldid=?";
				array_push($params, $account_id_val);
			}
		}
		else if($type == "Contacts")
		{	
			if($contact_id_val!="" && $contact_id_val!=0)	
			{
				$flag="true";
				$log->info("Getting the  Contacts custom vtiger_field column name  ");
				$sql_type.="'Contacts' and fieldid=?";
				array_push($params, $contact_id_val);
			}
		}
		else if($type == "Potentials")
		{
			if($potential_id_val!="" && $potential_id_val!=0)
            		{
				$flag="true";
                		$log->info("Getting the  Potentials custom vtiger_field column name  ");
				$sql_type.="'Potentials' and fieldid=?";
				array_push($params, $potential_id_val);				
            		}
		}
		if($flag=="true")
		{ 
			$type_result=$adb->pquery($sql_type, $params);
			//To construct the cf array
            		$colname = $adb->query_result($type_result,0,"columnname");
			if(isset($type_insert_column))
				$type_insert_column.=",";
			$type_insert_column.=$colname ;
			$type_uitype =$adb->query_result($type_result,0,"uitype") ;

			//To construct the cf array
                        $ins_val = $adb->query_result($lead_val_result,0,$lead_column_name);
			
			if(isset($insert_value))
				$insert_value.=",";
			
			//This array is used to store the tablename as the key and the value for that table in the custom field of the uitype only for 15 and 33(Multiselect cf)...
			if($lead_uitype == 33 || $lead_uitype == 15)
			{
				$value_cf_array[$colname]=$ins_val;
			}
			
			$insert_value.=$ins_val;
		}
	}

	if(count($value_cf_array) > 0)
	{
		if($type_insert_column != '')
		{
			foreach($value_cf_array as $key => $value)
			{
				$tableName = $key;
				$tableVal = $value;
				if($tableVal != '')
				{
					$tab_val = explode ("|##|",$value);
					if(count($tab_val)>0)
					{
						for($k=0;$k<count($tab_val);$k++)
						{
							$val =$tab_val[$k];
							$sql = "select $tableName from vtiger_$tableName";
							$numRow = $adb->num_rows($adb->query($sql));
							$count=0;
							for($n=0;$n < $numRow;$n++)
							{
								$exist_val = $adb->query_result($adb->query($sql),$n,$tableName);
								if(trim($exist_val) == trim($val))
								{
									$count++;
								}
							}
							if($count == 0)
							{
								$cfId=$adb->getUniqueID("vtiger_$tableName");
								$unique_picklist_value = getUniquePicklistID();

								$qry="insert into vtiger_$tableName values(?,?,?,?)";
								$adb->pquery($qry, array($cfId,trim($val),1,$unique_picklist_value));
								//added to fix ticket#4492
								$picklistId_qry = "select picklistid from vtiger_picklist where name=?";
								$picklistId_res = $adb->pquery($picklistId_qry,array($tableName));
								$picklist_Id = $adb->query_result($picklistId_res,0,'picklistid');
								$role_qry = "select roleid from vtiger_role";
								$numOFRole=$adb->num_rows($adb->query($role_qry));
								for($l=0;$l<$numOFRole;$l++)
								{
									$role_id = $adb->query_result($adb->query($role_qry),$l,'roleid');
									$sort_qry = "select max(sortid)+1 as sortid from vtiger_role2picklist where picklistid=? and roleid=?";
									$sort_qry_res = $adb->pquery($sort_qry,array($picklist_Id,$role_id));
									$sort_id = $adb->query_result($sort_qry_res,0,'sortid');
									$role_picklist = "insert into vtiger_role2picklist values (?,?,?,?)";
									$adb->pquery($role_picklist,array($role_id,$unique_picklist_value,$picklist_Id,$sort_id));
								}
								//end
							}


						}
					}

				}
			}
		}
	}
	$log->debug("columns to be inserted are ".$type_insert_column);
        $log->debug("columns to be inserted are ".$insert_value);
	$values = array ($type_insert_column,$insert_value);
	$log->debug("Exiting getInsertValues method ...");
	return $values;	
}
//function Ends

/**	Function used to get the lead related Notes and Attachments with other entities Account, Contact and Potential
 *	@param integer $id - leadid
 *	@param integer $accountid -  related entity id (accountid)
 */
function getRelatedNotesAttachments($id,$accountid)
{
	global $adb,$log,$id;
	$log->debug("Entering getRelatedNotesAttachments(".$id.",".$accountid.") method ...");
	
	$sql_lead_notes	="select * from vtiger_senotesrel where crmid=?";
	$lead_notes_result = $adb->pquery($sql_lead_notes, array($id));
	$noofrows = $adb->num_rows($lead_notes_result);

	for($i=0; $i<$noofrows;$i++ )
	{

		$lead_related_note_id=$adb->query_result($lead_notes_result,$i,"notesid");
		 $log->debug("Lead related note id ".$lead_related_note_id);
		$sql_delete_lead_notes="delete from vtiger_senotesrel where crmid=?";
		$adb->pquery($sql_delete_lead_notes, array($id));

		$sql_insert_account_notes="insert into vtiger_senotesrel(crmid,notesid) values (?,?)";
		$adb->pquery($sql_insert_account_notes, array($accountid, $lead_related_note_id));
	}

	$sql_lead_attachment="select * from vtiger_seattachmentsrel where crmid=?";
        $lead_attachment_result = $adb->pquery($sql_lead_attachment, array($id));
        $noofrows = $adb->num_rows($lead_attachment_result);

        for($i=0;$i<$noofrows;$i++)
        {
						
                $lead_related_attachment_id=$adb->query_result($lead_attachment_result,$i,"attachmentsid");
		 		$log->debug("Lead related attachment id ".$lead_related_attachment_id);

                $sql_delete_lead_attachment="delete from vtiger_seattachmentsrel where crmid=?";
                $adb->pquery($sql_delete_lead_attachment, array($id));

                $sql_insert_account_attachment="insert into vtiger_seattachmentsrel(crmid,attachmentsid) values (?,?)";                        
                $adb->pquery($sql_insert_account_attachment, array($accountid, $lead_related_attachment_id));
        }
	$log->debug("Exiting getRelatedNotesAttachments method ...");
	
}

/**	Function used to get the lead related activities with other entities Account and Contact 
 *	@param integer $accountid - related entity id
 *	@param integer $contact_id -  related entity id 
 */
function getRelatedActivities($accountid,$contact_id)
{
	global $adb,$log,$id;	
	$log->debug("Entering getRelatedActivities(".$accountid.",".$contact_id.") method ...");
	$sql_lead_activity="select * from vtiger_seactivityrel where crmid=?";
	$lead_activity_result = $adb->pquery($sql_lead_activity, array($id));
        $noofrows = $adb->num_rows($lead_activity_result);
        for($i=0;$i<$noofrows;$i++)
        {

                $lead_related_activity_id=$adb->query_result($lead_activity_result,$i,"activityid");
		 $log->debug("Lead related vtiger_activity id ".$lead_related_activity_id);

		$sql_type_email="select setype from vtiger_crmentity where crmid=?";
		$type_email_result = $adb->pquery($sql_type_email, array($lead_related_activity_id));
                $type=$adb->query_result($type_email_result,0,"setype");
		$log->debug("type of vtiger_activity id ".$type);

                $sql_delete_lead_activity="delete from vtiger_seactivityrel where crmid=?";
                $adb->pquery($sql_delete_lead_activity, array($id));

		if($type != "Emails")
		{
            $sql_insert_account_activity="insert into vtiger_seactivityrel(crmid,activityid) values (?,?)";
	        $adb->pquery($sql_insert_account_activity, array($accountid, $lead_related_activity_id));

			$sql_insert_account_activity="insert into vtiger_cntactivityrel(contactid,activityid) values (?,?)";
            $adb->pquery($sql_insert_account_activity, array($contact_id, $lead_related_activity_id));
		}
		else
		{
			 $sql_insert_account_activity="insert into vtiger_seactivityrel(crmid,activityid) values (?,?)";                                                                                     
			 $adb->pquery($sql_insert_account_activity, array($contact_id, $lead_related_activity_id));
		}
        }
	$log->debug("Exiting getRelatedActivities method ...");

}

/**	Function used to save the lead related products with other entities Account, Contact and Potential
 *	$leadid - leadid
 *	$relatedid - related entity id (accountid/contactid/potentialid)
 *	$setype - related module(Accounts/Contacts/Potentials)
 */
function saveLeadRelatedProducts($leadid, $relatedid, $setype)
{
	global $adb, $log;
	$log->debug("Entering into function saveLeadRelatedProducts($leadid, $relatedid)");

	$product_result = $adb->pquery("select * from vtiger_seproductsrel where crmid=?", array($leadid));
	$noofproducts = $adb->num_rows($product_result);
	for($i = 0; $i < $noofproducts; $i++)
	{
		$productid = $adb->query_result($product_result,$i,'productid');
		$adb->pquery("insert into vtiger_seproductsrel values(?,?,?)", array($relatedid, $productid, $setype));
	}

	$log->debug("Exit from function saveLeadRelatedProducts.");
}

/**     Function used to save the lead related Campaigns with Contact
 *      $leadid - leadid
 *      $relatedid - related entity id (contactid)
 */
function saveLeadRelatedCampaigns($leadid, $relatedid)
{
	global $adb, $log;
	$log->debug("Entering into function saveLeadRelatedCampaigns($leadid, $relatedid)");

	$campaign_result = $adb->pquery("select * from vtiger_campaignleadrel where leadid=?", array($leadid));
	$noofcampaigns = $adb->num_rows($campaign_result);
	for($i = 0; $i < $noofcampaigns; $i++)
	{
		$campaignid = $adb->query_result($campaign_result,$i,'campaignid');

		$adb->pquery("insert into vtiger_campaigncontrel (campaignid, contactid) values(?,?)", array($campaignid, $relatedid));
	}
	$log->debug("Exit from function saveLeadRelatedCampaigns.");
}

/*Code integrated to avoid duplicate Account creation during ConvertLead Operation  START-- by Bharathi*/
$acc_query = "select vtiger_account.accountid from vtiger_account left join vtiger_crmentity on vtiger_account.accountid = vtiger_crmentity.crmid where vtiger_crmentity.deleted=0 and vtiger_account.accountname = ?";
$acc_res = $adb->pquery($acc_query, array($accountname));
$acc_rows = $adb->num_rows($acc_res);
if($acc_rows != 0)
        $crmid = $adb->query_result($acc_res,0,"accountid");
else
{
	$crmid = $adb->getUniqueID("vtiger_crmentity");

	//Saving Account - starts
	$sql_crmentity = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,createdtime,modifiedtime,deleted,description) values(?,?,?,?,?,?,?,?,?)";
	$sql_params = array($crmid, $current_user_id, $assigned_user_id, 'Accounts', 1, $date_entered, $date_modified, 0, $row['description']);
	$adb->pquery($sql_crmentity, $sql_params);
	
	/* Modified by Minnie to fix the convertlead issue -- START*/
	if(isset($row["annualrevenue"]) && !empty($row["annualrevenue"])) $annualrevenue = $row["annualrevenue"];
	else $annualrevenue = 'null';
	
	if(isset($row["noofemployees"]) && !empty($row["noofemployees"])) $employees = $row["noofemployees"];
	else $employees = 'null';
	
	$sql_insert_account = "INSERT INTO vtiger_account (accountid,accountname,industry,annualrevenue,phone,fax,rating,email1,website,employees) VALUES (?,?,?,?,?,?,?,?,?,?)";
	/* Modified by Minnie -- END*/
	$account_params = array($crmid, $accountname, $row["industry"], $annualrevenue, $row["phone"], $row["fax"], $row["rating"], $row["email"], $row["website"], $employees);
	$adb->pquery($sql_insert_account, $account_params);

	$sql_insert_accountbillads = "INSERT INTO vtiger_accountbillads (accountaddressid,bill_city,bill_code,bill_country,bill_state,bill_street,bill_pobox) VALUES (?,?,?,?,?,?,?)";
	$billads_params = array($crmid, $row["city"], $row["code"], $row["country"], $row["state"], $row["lane"], $row["pobox"]);
	$adb->pquery($sql_insert_accountbillads, $billads_params);

	$sql_insert_accountshipads = "INSERT INTO vtiger_accountshipads (accountaddressid,ship_city,ship_code,ship_country,ship_pobox,ship_state,ship_street) VALUES (?,?,?,?,?,?,?)";
	$shipads_params = array($crmid, $row["city"], $row["code"], $row["country"], $row["pobox"], $row["state"], $row["lane"]);
	$adb->pquery($sql_insert_accountshipads, $shipads_params);

	//Getting the custom vtiger_field values from leads and inserting into Accounts if the vtiger_field is mapped - Jaguar
	$insert_values=array($crmid);
	$insert_str = "?";
	$insert_column="accountid";	
	$val= getInsertValues("Accounts",$crmid);
	if($val[0]!="" && $val[1]!="") {
		$insert_column.=",".$val[0];
		$tempval = explode(",",$val[1]);
		$insert_str .= str_repeat(",?", count($tempval));
		array_push($insert_values, $tempval);
	}
	$sql_insert_accountcustomfield = "INSERT INTO vtiger_accountscf (".$insert_column.") VALUES (".$insert_str.")";
	$adb->pquery($sql_insert_accountcustomfield, $insert_values);
	//Saving Account - ends
}
/*Code integrated to avoid duplicate Account creation during ConvertLead Operation  END-- by Bharathi*/

$account_id=$crmid;
getRelatedNotesAttachments($id,$crmid); //To Convert Related Notes & Attachments -Jaguar

//Retrieve the lead related products and relate them with this new account
saveLeadRelatedProducts($id, $crmid, "Accounts");

//Up to this, Account related data save finshed


$date_entered = $adb->formatDate(date('YmdHis'), true);
$date_modified = $adb->formatDate(date('YmdHis'), true);

//Saving Contact - starts
$crmcontactid = $adb->getUniqueID("vtiger_crmentity");
$sql_crmentity1 = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,deleted,description,createdtime,modifiedtime) values(?,?,?,?,?,?,?,?,?)";
$sql_params = array($crmcontactid, $current_user_id, $assigned_user_id, 'Contacts', 0, 0, $row['description'], $date_entered, $date_modified);
$adb->pquery($sql_crmentity1, $sql_params);

$contact_id = $crmcontactid;
$log->debug("contact id is ".$contact_id);

$sql_insert_contact = "INSERT INTO vtiger_contactdetails (contactid,accountid,salutation,firstname,lastname,email,phone,mobile,title,fax,yahooid) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
$contact_params = array($contact_id, $crmid, $row["salutationtype"], $row["firstname"], $row["lastname"], $row["email"], $row["phone"], $row["mobile"], $row["designation"], $row["fax"], $row['yahooid']);
$adb->pquery($sql_insert_contact, $contact_params);


$sql_insert_contactsubdetails = "INSERT INTO vtiger_contactsubdetails (contactsubscriptionid,homephone,otherphone,leadsource) VALUES (?,?,?,?)";
$c_subd_params = array($contact_id, '', '', $row['leadsource']);
$adb->pquery($sql_insert_contactsubdetails, $c_subd_params);

$sql_insert_contactaddress = "INSERT INTO vtiger_contactaddress (contactaddressid,mailingcity,mailingstreet,mailingstate,mailingcountry,mailingpobox,mailingzip) VALUES (?,?,?,?,?,?,?)";
$c_addr_params = array($contact_id, $row["city"], $row["lane"], $row['state'], $row["country"], $row["pobox"], $row['code']);
$adb->pquery($sql_insert_contactaddress, $c_addr_params);

$sql_insert_customerdetails = "INSERT INTO vtiger_customerdetails (customerid) VALUES (?)";
$adb->pquery($sql_insert_customerdetails, array($contact_id));

//Getting the customfield values from leads and inserting into the respected ContactCustomfield to which it is mapped - Jaguar
$insert_column="contactid";
$insert_str = "?";
$insert_values=array($contact_id);

$val= getInsertValues("Contacts",$contact_id);
if($val[0]!="" && $val[1]!="") {
	$insert_column.=",".$val[0];	
	$tempval = explode(",",$val[1]);
	$insert_str .= str_repeat(",?", count($tempval));
	array_push($insert_values, $tempval);
}
$sql_insert_contactcustomfield = "INSERT INTO vtiger_contactscf (".$insert_column.") VALUES (".$insert_str.")";
$adb->pquery($sql_insert_contactcustomfield, $insert_values);
//Saving Contact - ends

getRelatedActivities($account_id,$contact_id); //To convert relates Activites  and Email -Jaguar

//Retrieve the lead related products and relate them with this new contact
saveLeadRelatedProducts($id, $contact_id, "Contacts");

//Retrieve the lead related Campaigns and relate them with this new contact --Minnie
saveLeadRelatedCampaigns($id, $contact_id);

//Up to this, Contact related data save finshed





//Saving Potential - starts
if(! isset($createpotential) || ! $createpotential == "on")
{
	$log->info("createpotential is not set");

	$date_entered = $adb->formatDate(date('YmdHis'), true);
	$date_modified = $adb->formatDate(date('YmdHis'), true);
  

	$oppid = $adb->getUniqueID("vtiger_crmentity");
	$sql_crmentity = "insert into vtiger_crmentity(crmid,smcreatorid,smownerid,setype,presence,deleted,createdtime,modifiedtime,description) values(?,?,?,?,?,?,?,?,?)";
  	$sql_params = array($oppid, $current_user_id, $assigned_user_id, 'Potentials', 0, 0, $date_entered, $date_modified, $row['description']);
	$adb->pquery($sql_crmentity, $sql_params);


	if(!isset($potential_amount) || $potential_amount == null)
	{
		$potential_amount=0;
        }

	$sql_insert_opp = "INSERT INTO vtiger_potential (potentialid,accountid,potentialname,leadsource,closingdate,sales_stage,amount) VALUES (?,?,?,?,?,?,?)";
	$opp_params = array($oppid, $crmid, $potential_name, $row['leadsource'], $close_date, $potential_sales_stage, $potential_amount);
	$adb->pquery($sql_insert_opp, $opp_params);

	//Getting the customfield values from leads and inserting into the respected PotentialCustomfield to which it is mapped - Jaguar
	$insert_column="potentialid";
	$insert_str="?";
	$insert_values=array($oppid);
	$val= getInsertValues("Potentials",$oppid);
	if($val[0]!="" && $val[1]!="") {
		$insert_column.=",".$val[0];		
		$tempval = explode(",",$val[1]);
		$insert_str .= str_repeat(",?", count($tempval));
		array_push($insert_values, $tempval);
	}

	$sql_insert_potentialcustomfield = "INSERT INTO vtiger_potentialscf (".$insert_column.") VALUES (".$insert_str.")";
	$adb->pquery($sql_insert_potentialcustomfield, $insert_values);

	$sql_insert2contpotentialrel ="insert into vtiger_contpotentialrel values(?,?)";
    $adb->pquery($sql_insert2contpotentialrel, array($contact_id, $oppid));

	//Retrieve the lead related products and relate them with this new potential
	saveLeadRelatedProducts($id, $oppid, "Potentials");

}
//Saving Potential - ends
//Up to this, Potential related data save finshed


//Deleting from the vtiger_tracker
$sql_delete_tracker= "DELETE from vtiger_tracker where item_id=?";
$adb->pquery($sql_delete_tracker, array($id));
$category = getParentTab();
//Updating the deleted status
$sql_update_converted = "UPDATE vtiger_leaddetails SET converted = 1 where leadid=?";
$adb->pquery($sql_update_converted, array($id)); 
//updating the campaign-lead relation --Minnie
$sql_update_campleadrel = "delete from vtiger_campaignleadrel where leadid=?";
$adb->pquery($sql_update_campleadrel, array($id));
header("Location: index.php?action=DetailView&module=Accounts&record=$crmid&parenttab=$category");

?>
