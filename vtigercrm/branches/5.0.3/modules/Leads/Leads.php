<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of txhe License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');
require_once('data/CRMEntity.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Campaigns/Campaigns.php');
require_once('modules/Notes/Notes.php');
require_once('modules/Emails/Emails.php');
require_once('include/ComboUtil.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

class Leads extends CRMEntity {
	var $log;
	var $db;

	var $module_id = "leadid";

	var $tab_name = Array('vtiger_crmentity','vtiger_leaddetails','vtiger_leadsubdetails','vtiger_leadaddress','vtiger_leadscf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_leaddetails'=>'leadid','vtiger_leadsubdetails'=>'leadsubscriptionid','vtiger_leadaddress'=>'leadaddressid','vtiger_leadscf'=>'leadid');

	var $entity_table = "vtiger_crmentity";

	//construct this from database;	
	var $column_fields = Array();
	var $sortby_fields = Array('lastname','firstname','email','phone','company','smownerid','website');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('smcreatorid', 'smownerid', 'contactid','potentialid' ,'crmid');

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
		'Last Name'=>Array('leaddetails'=>'lastname'),
		'First Name'=>Array('leaddetails'=>'firstname'),
		'Company'=>Array('leaddetails'=>'company'),
		'Phone'=>Array('leadaddress'=>'phone'),
		'Website'=>Array('leadsubdetails'=>'website'),
		'Email'=>Array('leaddetails'=>'email'),
		'Assigned To'=>Array('crmentity'=>'smownerid')
	);
	var $list_fields_name = Array(
		'Last Name'=>'lastname',
		'First Name'=>'firstname',
		'Company'=>'company',
		'Phone'=>'phone',
		'Website'=>'website',
		'Email'=>'email',
		'Assigned To'=>'assigned_user_id'
	);
	var $list_link_field= 'lastname';

	var $search_fields = Array(
		'Name'=>Array('leaddetails'=>'lastname'),
		'Company'=>Array('leaddetails'=>'company')
	);
	var $search_fields_name = Array(
		'Name'=>'lastname',
		'Company'=>'company'
	);

	var $required_fields =  array("lastname"=>1, 'company'=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'lastname';
	var $default_sort_order = 'ASC';

	var $groupTable = Array('vtiger_leadgrouprelation','leadid');
	
	function Leads()	{
		$this->log = LoggerManager::getLogger('lead');
		$this->log->debug("Entering Leads() method ...");
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Leads');
		$this->log->debug("Exiting Lead method ...");
	}

	/** Function to handle module specific operations when saving a entity
	*/
	function save_module($module)
	{
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/**
	 * Function to get sort order
 	 * return string  $sorder    - sortorder string either 'ASC' or 'DESC'
	 */
	function getSortOrder()
	{	
		global $log;
		$log->debug("Entering getSortOrder() method ...");
		if(isset($_REQUEST['sorder'])) 
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['LEADS_SORT_ORDER'] != '')?($_SESSION['LEADS_SORT_ORDER']):($this->default_sort_order));

		$log->debug("Exiting getSortOrder method ...");
		return $sorder;
	}

	/**
	 * Function to get order by
	 * return string  $order_by    - fieldname(eg: 'leadname')
 	 */
	function getOrderBy()
	{
		global $log;
		$log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by'])) 
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['LEADS_ORDER_BY'] != '')?($_SESSION['LEADS_ORDER_BY']):($this->default_order_by));

		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	
	// Mike Crowe Mod --------------------------------------------------------



	/** Function to export the lead records in CSV Format
	* @param reference variable - order by is passed when the query is executed
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Leads Query.
	*/
	function create_export_query(&$order_by, &$where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(".$order_by.",".$where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Leads", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_leadgrouprelation.groupname as 'Assigned To Group' 
	      			FROM ".$this->entity_table."
				INNER JOIN vtiger_leaddetails
					ON vtiger_crmentity.crmid=vtiger_leaddetails.leadid
				LEFT JOIN vtiger_leadsubdetails
					ON vtiger_leaddetails.leadid = vtiger_leadsubdetails.leadsubscriptionid
				LEFT JOIN vtiger_leadaddress
					ON vtiger_leaddetails.leadid=vtiger_leadaddress.leadaddressid
				LEFT JOIN vtiger_leadscf 
					ON vtiger_leadscf.leadid=vtiger_leaddetails.leadid
				LEFT JOIN vtiger_leadgrouprelation
                	                ON vtiger_leadscf.leadid = vtiger_leadgrouprelation.leadid
	                        LEFT JOIN vtiger_groups
                        	        ON vtiger_groups.groupname = vtiger_leadgrouprelation.groupname
				LEFT JOIN vtiger_users
					ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'
				";


		$where_auto = " vtiger_crmentity.deleted=0 AND vtiger_leaddetails.converted =0";

		if($where != "")
			$query .= "where ($where) AND ".$where_auto;
		else
			$query .= "where ".$where_auto;

		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		//we should add security check when the user has Private Access
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[7] == 3)
		{
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter("Leads");
		}

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

		$log->debug("Exiting create_export_query method ...");
		return $query;
	}


	
	/** Returns a list of the associated tasks
 	 * @param  integer   $id      - leadid
 	 * returns related Task or Event record in array format
	*/
function get_activities($id)
{
	global $log, $singlepane_view;
	$log->debug("Entering get_activities(".$id.") method ...");
	global $app_strings;

	$focus = new Activity();
	$button = '';

	if(isPermitted("Calendar",1,"") == 'yes')
	{
		$button .= '<input title="New Task" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Calendar\';i;this.form.return_module.value=\'Leads\';this.form.activity_mode.value=\'Task\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_TASK'].'">&nbsp;';
		$button .= '<input title="New Event" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Calendar\';this.form.return_module.value=\'Leads\';this.form.activity_mode.value=\'Events\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_EVENT'].'">&nbsp;</td>';
	}
	if($singlepane_view == 'true')
		$returnset = '&return_module=Leads&return_action=DetailView&return_id='.$id;
	else
		$returnset = '&return_module=Leads&return_action=CallRelatedList&return_id='.$id;


	// First, get the list of IDs.
	$query = "SELECT vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_users.user_name,vtiger_recurringevents.recurringtype from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_seactivityrel.crmid=".$id." and (activitytype='Task' or activitytype='Call' or activitytype='Meeting') and ((vtiger_activity.status is not NULL && vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL && vtiger_activity.status != 'Deferred') or (vtiger_activity.eventstatus !='' && vtiger_activity.eventstatus != 'Held'))";
	$log->debug("Exiting get_activities method ...");
	return  GetRelatedList('Leads','Calendar',$focus,$query,$button,$returnset);
}

/** Returns a list of the associated Campaigns
  * @param $id -- campaign id :: Type Integer
  * @returns list of campaigns in array format
  */
function get_campaigns($id)
{
	global $log, $singlepane_view;
	$log->debug("Entering get_campaigns(".$id.") method ...");
	global $mod_strings;
	$focus = new Campaigns();
	$button = '';

	if($singlepane_view == 'true')
		$returnset = '&return_module=Leads&return_action=DetailView&return_id='.$id;
	else
		$returnset = '&return_module=Leads&return_action=CallRelatedList&return_id='.$id;

	$log->info("Campaign Related List for Lead Displayed");
	$query = "SELECT vtiger_users.user_name, vtiger_campaign.campaignid, vtiger_campaign.campaignname, vtiger_campaign.campaigntype, vtiger_campaign.campaignstatus, vtiger_campaign.expectedrevenue, vtiger_campaign.closingdate, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime from vtiger_campaign inner join vtiger_campaignleadrel on vtiger_campaignleadrel.campaignid=vtiger_campaign.campaignid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_campaign.campaignid left join vtiger_campaigngrouprelation on vtiger_campaign.campaignid=vtiger_campaigngrouprelation.campaignid left join vtiger_groups on vtiger_groups.groupname=vtiger_campaigngrouprelation.groupname left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid where vtiger_campaignleadrel.leadid=".$id." and vtiger_crmentity.deleted=0";

	$log->debug("Exiting get_campaigns method ...");
	return GetRelatedList('Leads','Campaigns',$focus,$query,$button,$returnset);

}


	/** Returns a list of the associated emails
 	 * @param  integer   $id      - leadid
 	 * returns related emails record in array format
	*/
function get_emails($id)
{
	global $log, $singlepane_view;	
	$log->debug("Entering get_emails(".$id.") method ...");
	global $mod_strings;
	require_once('include/RelatedListView.php');

	$focus = new Emails();

	$button = '';

	if(isPermitted("Emails",1,"") == 'yes')
	{

		$button .= '<input title="New Email" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Emails\';this.form.email_directing_module.value=\'leads\';this.form.record.value='.$id.';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_EMAIL'].'">&nbsp;';
	}
	if($singlepane_view == 'true')
		$returnset = '&return_module=Leads&return_action=DetailView&return_id='.$id;
	else
		$returnset = '&return_module=Leads&return_action=CallRelatedList&return_id='.$id;

	$query ="select vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.semodule, vtiger_activity.activitytype, vtiger_activity.date_start, vtiger_activity.status, vtiger_activity.priority, vtiger_crmentity.crmid,vtiger_crmentity.smownerid,vtiger_crmentity.modifiedtime, vtiger_users.user_name from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid inner join vtiger_users on  vtiger_users.id=vtiger_crmentity.smownerid where vtiger_activity.activitytype='Emails' and vtiger_crmentity.deleted=0 and vtiger_seactivityrel.crmid=".$id;
	$log->debug("Exiting get_emails method ...");
	return GetRelatedList('Leads','Emails',$focus,$query,$button,$returnset);
}

/**
 * Function to get Lead related Task & Event which have activity type Held, Completed or Deferred.
 * @param  integer   $id      - leadid
 * returns related Task or Event record in array format
 */
function get_history($id)
{
	global $log;
	$log->debug("Entering get_history(".$id.") method ...");
	$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
	vtiger_activity.eventstatus, vtiger_activity.activitytype, vtiger_crmentity.modifiedtime,
	vtiger_crmentity.createdtime, vtiger_crmentity.description, vtiger_users.user_name,vtiger_activitygrouprelation.groupname
			from vtiger_activity
			inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
			left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
			left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname 
			left join vtiger_users on vtiger_crmentity.smownerid= vtiger_users.id
			where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
			and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
			and vtiger_seactivityrel.crmid=".$id;
	//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

	$log->debug("Exiting get_history method ...");
	return getHistory('Leads',$query,$id);
}

/**
 * Function to get Lead related Attachments
 * @param  integer   $id      - leadid
 * returns related Attachment record in array format
 */
function get_attachments($id)
{
	global $log;
	$log->debug("Entering get_attachments(".$id.") method ...");
	// Armando L�scher 18.10.2005 -> �visibleDescription
	// Desc: Inserted crm2.createdtime, vtiger_notes.notecontent description, vtiger_users.user_name
	// Inserted inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
	$query = "select vtiger_notes.title,'Notes      ' ActivityType, vtiger_notes.filename,
	vtiger_attachments.type  FileType,crm2.modifiedtime lastmodified,
	vtiger_seattachmentsrel.attachmentsid attachmentsid, vtiger_notes.notesid crmid,
		crm2.createdtime, vtiger_notes.notecontent description, vtiger_users.user_name
			from vtiger_notes
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid= vtiger_notes.notesid
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_senotesrel.crmid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_notes.notesid and crm2.deleted=0
			left join vtiger_seattachmentsrel  on vtiger_seattachmentsrel.crmid =vtiger_notes.notesid
			left join vtiger_attachments on vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
			inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
			where vtiger_crmentity.crmid=".$id;
	$query .= ' union all ';
	// Armando L�scher 18.10.2005 -> �visibleDescription
	// Desc: Inserted crm2.createdtime, vtiger_attachments.description, vtiger_users.user_name
	// Inserted inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
	// Inserted order by createdtime desc
	$query .= "select vtiger_attachments.description title ,'Attachments' ActivityType,
	vtiger_attachments.name filename, vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,
	vtiger_attachments.attachmentsid attachmentsid, vtiger_seattachmentsrel.attachmentsid crmid,
		crm2.createdtime, vtiger_attachments.description, vtiger_users.user_name
			from vtiger_attachments
			inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid= vtiger_attachments.attachmentsid
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_seattachmentsrel.crmid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_attachments.attachmentsid
			inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
			where vtiger_crmentity.crmid=".$id."
			order by createdtime desc";

	$log->debug("Exiting get_attachments method ...");
	return getAttachmentsAndNotes('Leads',$query,$id);
}
	
/**
* Function to get lead related Products 
* @param  integer   $id      - leadid
* returns related Products record in array format
*/
function get_products($id)
{
	global $log, $singlepane_view;
	$log->debug("Entering get_products(".$id.") method ...");
	require_once('modules/Products/Products.php');
	global $mod_strings;
	global $app_strings;

	$focus = new Products();

	$button = '';

	if(isPermitted("Products",1,"") == 'yes')
	{
		$button .= '<input title="New Product" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Products\';this.form.return_module.value=\'Leads\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_PRODUCT'].'">&nbsp;';
	}
	if($singlepane_view == 'true')
		$returnset = '&return_module=Leads&return_action=DetailView&return_id='.$id;
	else
		$returnset = '&return_module=Leads&return_action=CallRelatedList&return_id='.$id;

	$query = 'select vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price, vtiger_crmentity.crmid, vtiger_crmentity.smownerid from vtiger_products inner join vtiger_seproductsrel on vtiger_products.productid = vtiger_seproductsrel.productid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid inner join vtiger_leaddetails on vtiger_leaddetails.leadid = vtiger_seproductsrel.crmid  where vtiger_leaddetails.leadid = '.$id.' and vtiger_crmentity.deleted = 0';
	$log->debug("Exiting get_products method ...");
	return  GetRelatedList('Leads','Products',$focus,$query,$button,$returnset);
}
	
	/** Function to get the Combo List Values of Leads Field
	 * @param string $list_option 
	 * Returns Combo List Options 
	*/
	function get_lead_field_options($list_option)
	{
		global $log;
		$log->debug("Entering get_lead_field_options(".$list_option.") method ...");
		$comboFieldArray = getComboArray($this->combofieldNames);
		$log->debug("Exiting get_lead_field_options method ...");
		return $comboFieldArray[$list_option];
	}
	
/** Function to get the Columnnames of the Leads Record
* Used By vtigerCRM Word Plugin
* Returns the Merge Fields for Word Plugin
*/
function getColumnNames_Lead()
{
	global $log,$current_user;
	$log->debug("Entering getColumnNames_Lead() method ...");
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
		$sql1 = "select fieldlabel from vtiger_field where tabid=7";
	}else
	{
		$profileList = getCurrentUserProfileList();
		$sql1 = "select fieldlabel from vtiger_field inner join vtiger_profile2field on vtiger_profile2field.fieldid=vtiger_field.fieldid inner join vtiger_def_org_field on vtiger_def_org_field.fieldid=vtiger_field.fieldid where vtiger_field.tabid=7 and vtiger_field.displaytype in (1,2,4) and vtiger_profile2field.visible=0 and vtiger_def_org_field.visible=0 and vtiger_profile2field.profileid in ".$profileList;
	}
	$result = $this->db->query($sql1);
	$numRows = $this->db->num_rows($result);
	for($i=0; $i < $numRows;$i++)
	{
   	$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
   	$custom_fields[$i] = ereg_replace(" ","",$custom_fields[$i]);
   	$custom_fields[$i] = strtoupper($custom_fields[$i]);
	}
	$mergeflds = $custom_fields;
	$log->debug("Exiting getColumnNames_Lead method ...");
	return $mergeflds;
}
//End

}

?>
