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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Contacts/Contact.php,v 1.70 2005/04/27 11:21:49 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');
require_once('data/CRMEntity.php');
require_once('include/utils/utils.php');
require_once('modules/Potentials/Opportunity.php');
require_once('modules/Activities/Activity.php');
require_once('modules/Notes/Note.php');
require_once('modules/Emails/Email.php');
require_once('modules/HelpDesk/HelpDesk.php');


// Contact is used to store customer information.
class Contact extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "contactdetails";
	var $tab_name = Array('vtiger_crmentity','vtiger_contactdetails','vtiger_contactaddress','vtiger_contactsubdetails','vtiger_contactscf','vtiger_customerdetails','vtiger_attachments');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_contactdetails'=>'contactid','vtiger_contactaddress'=>'contactaddressid','vtiger_contactsubdetails'=>'contactsubscriptionid','vtiger_contactscf'=>'contactid','vtiger_customerdetails'=>'customerid','vtiger_attachments'=>'attachmentsid');

	var $module_id = "contactid";
	var $object_name = "Contact";
	
	var $new_schema = true;

	var $column_fields = Array();
	
	var $sortby_fields = Array('lastname','firstname','title','email','phone','smownerid');

	var $list_link_field= 'lastname';

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
	'Last Name' => Array('contactdetails'=>'lastname'),
	'First Name' => Array('contactdetails'=>'firstname'),
	'Title' => Array('contactdetails'=>'title'),
	'Account Name' => Array('account'=>'accountname'),
	'Email' => Array('contactdetails'=>'email'),
	'Phone' => Array('contactdetails'=>'phone'),
	'Assigned To' => Array('crmentity'=>'smownerid')
	);

	var $range_fields = Array(
		'first_name',
		'last_name',
		'primary_address_city',
		'account_name',     
		'account_id',
		'id',   
		'email1',
		'salutation',
		'title',   
		'phone_mobile',
		'reports_to_name',
		'primary_address_street',     
		'primary_address_city',  
		'primary_address_state', 
		'primary_address_postalcode',  
		'primary_address_country',    
		'alt_address_city',     
		'alt_address_street',       
		'alt_address_city',  
		'alt_address_state',    
		'alt_address_postalcode',     
		'alt_address_country',
		'office_phone',
		'home_phone',
		'other_phone',
		'fax',
		'department',
		'birthdate',
		'assistant_name',
		'assistant_phone');

	
	var $list_fields_name = Array(
	'Last Name' => 'lastname',
	'First Name' => 'firstname',
	'Title' => 'title',
	'Account Name' => 'accountid',
	'Email' => 'email',
	'Phone' => 'phone',
	'Assigned To' => 'assigned_user_id'
	);

	var $search_fields = Array(
	'Name' => Array('contactdetails'=>'lastname'),
	'Title' => Array('contactdetails'=>'title')
		);
	
	var $search_fields_name = Array(
	'Name' => 'lastname',
	'Title' => 'title'
	);

	// This is the list of vtiger_fields that are required
	var $required_fields =  array("lastname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'lastname';
	var $default_sort_order = 'ASC';

	function Contact() {
		$this->log = LoggerManager::getLogger('contact');
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Contacts');
	}

    	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	function getSortOrder()
	{	
		global $log;
                $log->debug("Entering getSortOrder() method ...");
		if(isset($_REQUEST['sorder'])) 
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['CONTACTS_SORT_ORDER'] != '')?($_SESSION['CONTACTS_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder method ...");
		return $sorder;
	}

	function getOrderBy()
	{
		global $log;
	        $log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by'])) 
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['CONTACTS_ORDER_BY'] != '')?($_SESSION['CONTACTS_ORDER_BY']):($this->default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	
	// Mike Crowe Mod --------------------------------------------------------

    function getCount($user_name) 
    {
	global $log;
	$log->debug("Entering getCount(".$user_name.") method ...");
        $query = "select count(*) from vtiger_contactdetails  inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where user_name='" .$user_name ."' and vtiger_crmentity.deleted=0";

        $result = $this->db->query($query,true,"Error retrieving contacts count");
        $rows_found =  $this->db->getRowCount($result);
        $row = $this->db->fetchByAssoc($result, 0);

    
	$log->debug("Exiting getCount method ...");
        return $row["count(*)"];
    }       

        function get_contacts1($user_name,$email_address)
    {   
	global $log;
	$log->debug("Entering get_contacts1(".$user_name.",".$email_address.") method ...");
      $query = "select vtiger_users.user_name, vtiger_contactdetails.lastname last_name,vtiger_contactdetails.firstname first_name,vtiger_contactdetails.contactid as id, vtiger_contactdetails.salutation as salutation, vtiger_contactdetails.email as email1,vtiger_contactdetails.title as title,vtiger_contactdetails.mobile as phone_mobile,vtiger_account.accountname as vtiger_account_name,vtiger_account.accountid as vtiger_account_id   from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid  left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid  left join vtiger_contactgrouprelation on vtiger_contactdetails.contactid=vtiger_contactgrouprelation.contactid where user_name='" .$user_name ."' and vtiger_crmentity.deleted=0  and vtiger_contactdetails.email like '%" .$email_address ."%' limit 50";
    
	$log->debug("Exiting get_contacts1 method ...");
      return $this->process_list_query1($query);
    }

    function get_contacts($user_name,$from_index,$offset)
    {   
	global $log;
	$log->debug("Entering get_contacts(".$user_name.",".$from_index.",".$offset.") method ...");
      $query = "select vtiger_users.user_name,vtiger_groups.groupname,vtiger_contactdetails.department department, vtiger_contactdetails.phone office_phone, vtiger_contactdetails.fax fax, vtiger_contactsubdetails.assistant assistant_name, vtiger_contactsubdetails.otherphone other_phone, vtiger_contactsubdetails.homephone home_phone,vtiger_contactsubdetails.birthday birthdate, vtiger_contactdetails.lastname last_name,vtiger_contactdetails.firstname first_name,vtiger_contactdetails.contactid as id, vtiger_contactdetails.salutation as salutation, vtiger_contactdetails.email as email1,vtiger_contactdetails.title as title,vtiger_contactdetails.mobile as phone_mobile,vtiger_account.accountname as vtiger_account_name,vtiger_account.accountid as vtiger_account_id, vtiger_contactaddress.mailingcity as primary_address_city,vtiger_contactaddress.mailingstreet as primary_address_street, vtiger_contactaddress.mailingcountry as primary_address_country,vtiger_contactaddress.mailingstate as primary_address_state, vtiger_contactaddress.mailingzip as primary_address_postalcode,   vtiger_contactaddress.othercity as alt_address_city,vtiger_contactaddress.otherstreet as alt_address_street, vtiger_contactaddress.othercountry as alt_address_country,vtiger_contactaddress.otherstate as alt_address_state, vtiger_contactaddress.otherzip as alt_address_postalcode  from vtiger_contactdetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid left join vtiger_contactgrouprelation on vtiger_contactdetails.contactid=vtiger_contactgrouprelation.contactid left join vtiger_groups on vtiger_groups.groupname=vtiger_contactgrouprelation.groupname left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where user_name='" .$user_name ."' and vtiger_crmentity.deleted=0 limit " .$from_index ."," .$offset;
      
	$log->debug("Exiting get_contacts method ...");
      return $this->process_list_query1($query);
    }



    function process_list_query1($query)
    {
	global $log;
	$log->debug("Entering process_list_query1(".$query.") method ...");
	  
        $result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
        $list = Array();
        $rows_found =  $this->db->getRowCount($result);
        if($rows_found != 0)
        {
		   $contact = Array();
               for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
            
             {
                foreach($this->range_fields as $columnName)
                {
                    if (isset($row[$columnName])) {
			    
                        $contact[$columnName] = $row[$columnName];
                    }   
                    else     
                    {   
                            $contact[$columnName] = "";
                    }   
	     }	
    
// TODO OPTIMIZE THE QUERY ACCOUNT NAME AND ID are set separetly for every vtiger_contactdetails and hence 
// vtiger_account query goes for ecery single vtiger_account row

                    $list[] = $contact;
                }
        }   

        $response = Array();
        $response['list'] = $list;
        $response['row_count'] = $rows_found;
        $response['next_offset'] = $next_offset;
        $response['previous_offset'] = $previous_offset;


	$log->debug("Exiting process_list_query1 method ...");
        return $response;
    }


	/** Returns a list of the associated opportunities
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_opportunities($id)
	{
		global $log;
		$log->debug("Entering get_opportunities(".$id.") method ...");
		global $mod_strings;

		$focus = new Potential();
		$button = '';

		if(isPermitted("Potentials",1,"") == 'yes')
		{

			$button .= '<input title="New Potential" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Potentials\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_POTENTIAL'].'">&nbsp;';
		}
		$returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		$log->info("Potential Related List for Contact Displayed");

		// First, get the list of IDs.
		$query = 'select vtiger_users.user_name,vtiger_groups.groupname,vtiger_contactdetails.accountid, vtiger_contactdetails.contactid , vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_potential.potentialtype, vtiger_potential.sales_stage, vtiger_potential.amount, vtiger_potential.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid from vtiger_contactdetails inner join vtiger_potential on vtiger_contactdetails.accountid = vtiger_potential.accountid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid left join vtiger_potentialgrouprelation on vtiger_potential.potentialid=vtiger_potentialgrouprelation.potentialid left join vtiger_groups on vtiger_groups.groupname=vtiger_potentialgrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_contactdetails.contactid = '.$id.' and vtiger_crmentity.deleted=0';
		if($this->column_fields['account_id'] != 0)
		$log->debug("Exiting get_opportunities method ...");
		return GetRelatedList('Contacts','Potentials',$focus,$query,$button,$returnset);
	}
	
  
	/** Returns a list of the associated tasks
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_activities($id)
	{
	     	global $log;
                $log->debug("Entering get_activities(".$id.") method ...");
		global $mod_strings;

    	$focus = new Activity();

		$button = '';

        if(isPermitted("Activities",1,"") == 'yes')
        {
		$button .= '<input title="New Task" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Activities\';this.form.activity_mode.value=\'Task\';this.form.return_module.value=\'Contacts\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_TASK'].'">&nbsp;';
		$button .= '<input title="New Event" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Activities\';this.form.return_module.value=\'Contacts\';this.form.activity_mode.value=\'Events\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_EVENT'].'">&nbsp;';
		}
		$returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		$log->info("Activity Related List for Contact Displayed");

		$query = "SELECT vtiger_users.user_name,vtiger_contactdetails.lastname, vtiger_contactdetails.firstname,  vtiger_activity.activityid , vtiger_activity.subject, vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.due_date, vtiger_cntactivityrel.contactid, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_recurringevents.recurringtype  from vtiger_contactdetails inner join vtiger_cntactivityrel on vtiger_cntactivityrel.contactid = vtiger_contactdetails.contactid inner join vtiger_activity on vtiger_cntactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_cntactivityrel.activityid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname  where vtiger_contactdetails.contactid=".$id." and vtiger_crmentity.deleted = 0 and (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task') AND ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Completed' ) and ( vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus != 'Held') ";  //recurring type is added in Query -Jaguar
		$log->debug("Exiting get_activities method ...");
		return GetRelatedList('Contacts','Activities',$focus,$query,$button,$returnset);

	}

	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status, vtiger_activity.eventstatus,
		vtiger_activity.activitytype, vtiger_contactdetails.contactid, vtiger_contactdetails.firstname,
		vtiger_contactdetails.lastname, vtiger_crmentity.modifiedtime,
		vtiger_crmentity.createdtime, vtiger_crmentity.description, vtiger_users.user_name
				from vtiger_activity
				inner join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				inner join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
                                left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname
				inner join vtiger_users on vtiger_crmentity.smcreatorid= vtiger_users.id
				where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_cntactivityrel.contactid=".$id;
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('Contacts',$query,$id);
	}
	
	function get_tickets($id)
	{
		global $log;
		global $app_strings;
		$log->debug("Entering get_tickets(".$id.") method ...");
		$focus = new HelpDesk();

		$button = '<td valign="bottom" align="right"><input title="New Ticket" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'HelpDesk\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_TICKET'].'">&nbsp;</td>';
		$returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		$query = "select vtiger_users.user_name,vtiger_crmentity.crmid, vtiger_troubletickets.title, vtiger_contactdetails.contactid, vtiger_troubletickets.parent_id, vtiger_contactdetails.firstname, vtiger_contactdetails.lastname, vtiger_troubletickets.status, vtiger_troubletickets.priority, vtiger_crmentity.smownerid from vtiger_troubletickets inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid left join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_ticketgrouprelation on vtiger_troubletickets.ticketid=vtiger_ticketgrouprelation.ticketid left join vtiger_groups on vtiger_groups.groupname=vtiger_ticketgrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=".$id;
		$log->info("Ticket Related List for Contact Displayed");
		$log->debug("Exiting get_tickets method ...");
		return GetRelatedList('Contacts','HelpDesk',$focus,$query,$button,$returnset);
	}

	function get_attachments($id)
	{
		global $log;
		$log->debug("Entering get_attachments(".$id.") method ...");
		$query = "select vtiger_notes.title,'Notes      ' AS ActivityType,
		vtiger_notes.filename, vtiger_attachments.type AS FileType,crm2.modifiedtime AS lastmodified,
		vtiger_seattachmentsrel.attachmentsid AS vtiger_attachmentsid, vtiger_notes.notesid AS crmid,
			crm2.createdtime, vtiger_notes.notecontent AS description, vtiger_users.user_name
		from vtiger_notes
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_notes.contact_id
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_notes.notesid and crm2.deleted=0
			left join vtiger_seattachmentsrel on vtiger_seattachmentsrel.crmid =vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
		where vtiger_crmentity.crmid=".$id;
		$query .= " union all ";
		$query .= "select vtiger_attachments.description AS title,'Attachments' AS ActivityType,
		vtiger_attachments.name AS filename, vtiger_attachments.type AS FileType,crm2.modifiedtime AS lastmodified,
		vtiger_attachments.attachmentsid AS vtiger_attachmentsid, vtiger_seattachmentsrel.attachmentsid AS crmid,
			crm2.createdtime, vtiger_attachments.description, vtiger_users.user_name
		from vtiger_attachments
			inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid= vtiger_attachments.attachmentsid
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_seattachmentsrel.crmid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_attachments.attachmentsid
			inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
		where vtiger_crmentity.crmid=".$id."
		order by createdtime desc";
	  	$log->info("Notes&Attachmenmts for Contact Displayed");
		$log->debug("Exiting get_attachments method ...");
		return getAttachmentsAndNotes('Contacts',$query,$id);
	  }
	 
	 function get_quotes($id)
	 {	
		global $log;
                $log->debug("Entering get_quotes(".$id.") method ...");
		global $app_strings;
		require_once('modules/Quotes/Quote.php');		
		$focus = new Quote();
	
		$button = '';
		if(isPermitted("Quotes",1,"") == 'yes')
        {
		$button .= '<input title="'.$app_strings['LBL_NEW_QUOTE_BUTTON_TITLE'].'" accessyKey="'.$app_strings['LBL_NEW_QUOTE_BUTTON_KEY'].'" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Quotes\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_QUOTE_BUTTON'].'">&nbsp;</td>';
		}
		$returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;
		$query = "select vtiger_users.user_name,vtiger_crmentity.*, vtiger_quotes.*,vtiger_potential.potentialname,vtiger_contactdetails.lastname from vtiger_quotes inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_quotes.contactid left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid  left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_quotegrouprelation on vtiger_quotes.quoteid=vtiger_quotegrouprelation.quoteid left join vtiger_groups on vtiger_groups.groupname=vtiger_quotegrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_contactdetails.contactid=".$id;
		$log->debug("Exiting get_quotes method ...");
		return GetRelatedList('Contacts','Quotes',$focus,$query,$button,$returnset);
	  }
	 
	 function get_salesorder($id)
	 {	
		 global $log;
                $log->debug("Entering get_salesorder(".$id.") method ...");
		 require_once('modules/SalesOrder/SalesOrder.php');
		 global $app_strings;
		 $focus = new SalesOrder();
		 $button = '';

		 if(isPermitted("SalesOrder",1,"") == 'yes')
		 {

			 $button .= '<input title="'.$app_strings['LBL_NEW_SORDER_BUTTON_TITLE'].'" accessyKey="O" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'SalesOrder\';this.form.return_module.value=\'Contacts\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_SORDER_BUTTON'].'">&nbsp;';
		 }
		 $returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		 $query = "select vtiger_users.user_name,vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename, vtiger_account.accountname, vtiger_contactdetails.lastname from vtiger_salesorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_salesorder.contactid left join vtiger_sogrouprelation on vtiger_salesorder.salesorderid=vtiger_sogrouprelation.salesorderid left join vtiger_groups on vtiger_groups.groupname=vtiger_sogrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_salesorder.contactid = ".$id;
		$log->debug("Exiting get_salesorder method ...");
		 return GetRelatedList('Contacts','SalesOrder',$focus,$query,$button,$returnset);
	 }
	 
	 function get_products($id)
	 {
		 global $log;
		$log->debug("Entering get_products(".$id.") method ...");
		 global $app_strings;
		 require_once('modules/Products/Product.php');
		 $focus = new Product();
		 $button = '';

		 if(isPermitted("Products",1,"") == 'yes')
		 {

			 $button .= '<input title="'.$app_strings['LBL_NEW_PRODUCT'].'" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Products\';this.form.return_module.value=\'Contacts\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_PRODUCT'].'">&nbsp;';
		 }
		 $returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		 $query = 'select vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,vtiger_contactdetails.lastname from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_products.contactid where vtiger_contactdetails.contactid = '.$id.' and vtiger_crmentity.deleted = 0';
		$log->debug("Exiting get_products method ...");
		 return GetRelatedList('Contacts','Products',$focus,$query,$button,$returnset);
	 }
	 
	 function get_purchase_orders($id)
	 {
		global $log;
		$log->debug("Entering get_purchase_orders(".$id.") method ...");
		 global $app_strings;
		 require_once('modules/PurchaseOrder/PurchaseOrder.php');
		 $focus = new Order();

		 $button = '';

		 if(isPermitted("PurchaseOrder",1,"") == 'yes')
		 {

			 $button .= '<input title="'.$app_strings['LBL_PORDER_BUTTON_TITLE'].'" accessyKey="O" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'PurchaseOrder\';this.form.return_module.value=\'Contacts\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_PORDER_BUTTON'].'">&nbsp;';
		 }
		 $returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		 $query = "select vtiger_users.user_name,vtiger_crmentity.*, vtiger_purchaseorder.*,vtiger_vendor.vendorname,vtiger_contactdetails.lastname from vtiger_purchaseorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid left outer join vtiger_vendor on vtiger_purchaseorder.vendorid=vtiger_vendor.vendorid left outer join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_purchaseorder.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_pogrouprelation on vtiger_purchaseorder.purchaseorderid=vtiger_pogrouprelation.purchaseorderid left join vtiger_groups on vtiger_groups.groupname=vtiger_pogrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_purchaseorder.contactid=".$id;
		$log->debug("Exiting get_purchase_orders method ...");
		 return GetRelatedList('Contacts','PurchaseOrder',$focus,$query,$button,$returnset);
	 }

	/** Returns a list of the associated emails
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	*/
	function get_emails($id)
	{
		global $log;
		$log->debug("Entering get_emails(".$id.") method ...");
		global $mod_strings;

		$focus = new Email();

		$button = '';

		if(isPermitted("Emails",1,"") == 'yes')
		{	
			$button .= '<input title="New Email" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Emails\';this.form.email_directing_module.value=\'contacts\';this.form.record.value='.$id.';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_EMAIL'].'">';
		}
		$returnset = '&return_module=Contacts&return_action=DetailView&return_id='.$id;

		$log->info("Email Related List for Contact Displayed");

		$query = "select vtiger_activity.activityid, vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.activitytype, vtiger_users.user_name, vtiger_crmentity.modifiedtime, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_activity.date_start from vtiger_activity, vtiger_seactivityrel, vtiger_contactdetails, vtiger_users, vtiger_crmentity left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_seactivityrel.activityid = vtiger_activity.activityid and vtiger_contactdetails.contactid = vtiger_seactivityrel.crmid and vtiger_users.id=vtiger_crmentity.smownerid and vtiger_crmentity.crmid = vtiger_activity.activityid  and vtiger_contactdetails.contactid = ".$id." and vtiger_activity.activitytype='Emails' and vtiger_crmentity.deleted = 0";
		$log->debug("Exiting get_emails method ...");
		return GetRelatedList('Contacts','Emails',$focus,$query,$button,$returnset);
	}

	



        function create_export_query(&$order_by, &$where)
        {
		global $log;
		$log->debug("Entering create_export_query(".$order_by.",".$where.") method ...");
		if($this->checkIfCustomTableExists('contactscf'))
		{
			$query =  $this->constructCustomQueryAddendum('contactscf','Contacts') ."
                                vtiger_contactdetails.*, vtiger_contactaddress.*,
                                vtiger_account.accountname vtiger_account_name,
                                vtiger_users.user_name assigned_user_name
                                FROM vtiger_contactdetails
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
                                LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
                                LEFT JOIN vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			        left join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
				where vtiger_crmentity.deleted=0 and vtiger_users.status='Active' ";
		}
		else
		{
                  	 $query = "SELECT
                                vtiger_contactdetails.*, vtiger_contactaddress.*,
                                vtiger_account.accountname vtiger_account_name,
                                vtiger_users.user_name assigned_user_name
                                FROM vtiger_contactdetails
                                inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid
                                LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
                                LEFT JOIN vtiger_account on vtiger_contactdetails.accountid=vtiger_account.accountid
				left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid
			        left join vtiger_contactscf on vtiger_contactscf.contactid=vtiger_contactdetails.contactid
				where vtiger_crmentity.deleted=0 and vtiger_users.status='Active' ";
		}
                 $log->info("Export Query Constructed Successfully");
		$log->debug("Exiting create_export_query method ...");
		return $query;
        }

	
//Used By vtigerCRM Word Add-In
function getColumnNames()
{
	global $log;
	$log->debug("Entering getColumnNames() method ...");
	$sql1 = "select vtiger_fieldlabel from vtiger_field where vtiger_tabid=4 and block <> 6 and block <> 75";
	$result = $this->db->query($sql1);
	$numRows = $this->db->num_rows($result);
	for($i=0; $i < $numRows;$i++)
	{
	$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
	$custom_fields[$i] = ereg_replace(" ","",$custom_fields[$i]);
	$custom_fields[$i] = strtoupper($custom_fields[$i]);
	}
	$mergeflds = $custom_fields;
	$log->debug("Exiting getColumnNames method ...");
	return $mergeflds;
}
//End 

//Used By vtigerCRM Outlook Add-In
function get_searchbyemailid($username,$emailaddress)
{
	global $log;
	$log->debug("Entering get_searchbyemailid(".$username.",".$emailaddress.") method ...");
	$query = "select vtiger_contactdetails.lastname as last_name,vtiger_contactdetails.firstname as first_name,
					vtiger_contactdetails.contactid as id, vtiger_contactdetails.salutation as salutation, 
					vtiger_contactdetails.email as email1,vtiger_contactdetails.title as title,
					vtiger_contactdetails.mobile as phone_mobile,vtiger_account.accountname as vtiger_account_name,
					vtiger_account.accountid as vtiger_account_id  from vtiger_contactdetails 
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid 
						inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid  
						left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid 
						left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid 
						where user_name='" .$username ."' and vtiger_crmentity.deleted=0  and vtiger_contactdetails.email like '%".$emailaddress."%'";
						
	$log->debug("Exiting get_searchbyemailid method ...");
	return $this->process_list_query1($query);
}

function get_contactsforol($user_name)
{
	global $log;
	$log->debug("Entering get_contactsforol(".$user_name.") method ...");
	$query = "select vtiger_contactdetails.department department, vtiger_contactdetails.phone, 
					vtiger_contactdetails.fax, vtiger_contactsubdetails.assistant assistant_name,
					vtiger_contactsubdetails.assistantphone,  
					vtiger_contactsubdetails.otherphone, vtiger_contactsubdetails.homephone,
					vtiger_contactsubdetails.birthday birthdate, vtiger_contactdetails.lastname last_name,
					vtiger_contactdetails.firstname first_name,vtiger_contactdetails.contactid as id, 
					vtiger_contactdetails.salutation, vtiger_contactdetails.email,
					vtiger_contactdetails.title,vtiger_contactdetails.mobile,
					vtiger_account.accountname as vtiger_account_name,vtiger_account.accountid as vtiger_account_id, 
					vtiger_contactaddress.mailingcity, vtiger_contactaddress.mailingstreet, 
					vtiger_contactaddress.mailingcountry, vtiger_contactaddress.mailingstate, 
					vtiger_contactaddress.mailingzip, vtiger_contactaddress.othercity,
					vtiger_contactaddress.otherstreet, vtiger_contactaddress.othercountry,
					vtiger_contactaddress.otherstate, vtiger_contactaddress.otherzip   
						from vtiger_contactdetails 
						inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid 
						inner join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid 
						left join vtiger_account on vtiger_account.accountid=vtiger_contactdetails.accountid 
						left join vtiger_contactaddress on vtiger_contactaddress.contactaddressid=vtiger_contactdetails.contactid 
						left join vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
						where vtiger_users.user_name='" .$user_name ."' and vtiger_crmentity.deleted=0";
						
	$log->debug("Exiting get_contactsforol method ...");
	return $query;
}
//End

}

?>
