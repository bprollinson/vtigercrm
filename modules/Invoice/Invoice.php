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
 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
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

// Account is used to store vtiger_account information.
class Invoice extends CRMEntity {
	var $log;
	var $db;
		
	var $table_name = "vtiger_invoice";
	var $tab_name = Array('vtiger_crmentity','vtiger_invoice','vtiger_invoicebillads','vtiger_invoiceshipads','vtiger_invoicecf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_invoice'=>'invoiceid','vtiger_invoicebillads'=>'invoicebilladdressid','vtiger_invoiceshipads'=>'invoiceshipaddressid','vtiger_invoicecf'=>'invoiceid');
				
	
	var $entity_table = "vtiger_crmentity";
	
	var $billadr_table = "vtiger_invoicebillads";

	var $object_name = "Invoice";

	var $new_schema = true;
	
	var $module_id = "invoiceid";

	var $column_fields = Array();

	var $sortby_fields = Array('subject','crmid','invoicestatus','smownerid');		

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				'Invoice Id'=>Array('crmentity'=>'crmid'),
				'Subject'=>Array('invoice'=>'subject'),
				'Sales Order'=>Array('invoice'=>'salesorderid'),
				'Status'=>Array('invoice'=>'invoicestatus'),
				'Total'=>Array('invoice'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);
	
	var $list_fields_name = Array(
				        'Invoice Id'=>'',
				        'Subject'=>'subject',
				        'Sales Order'=>'salesorder_id',
				        'Status'=>'invoicestatus',
				        'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Invoice Id'=>Array('crmentity'=>'crmid'),
				'Subject'=>Array('purchaseorder'=>'subject'), 
				);
	
	var $search_fields_name = Array(
				        'Invoice Id'=>'',
				        'Subject'=>'subject',
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	function Invoice() {
		$this->log =LoggerManager::getLogger('Invoice');
		$this->log->debug("Entering Invoice() method ...");
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Invoice');
		$this->log->debug("Exiting Invoice method ...");
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	function getSortOrder()
	{
		global $log;
                $log->debug("Entering getSortOrder() method ...");	
		if(isset($_REQUEST['sorder'])) 
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['INVOICE_SORT_ORDER'] != '')?($_SESSION['INVOICE_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	function getOrderBy()
	{
		global $log;
                $log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by'])) 
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['INVOICE_ORDER_BY'] != '')?($_SESSION['INVOICE_ORDER_BY']):($this->default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	

	function get_summary_text()
	{
		global $log;
		$log->debug("Entering get_summary_text() method ...");
		$log->debug("Exiting get_summary_text method ...");
		return $this->name;
	}
	function get_activities($id)
	{
		global $log;
		$log->debug("Entering get_activities(".$id.") method ...");
		 global $app_strings;
		require_once('modules/Activities/Activity.php');
	        $focus = new Activity();

		$button = '';

		$returnset = '&return_module=Invoice&return_action=DetailView&return_id='.$id;

		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_users.user_name from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_seactivityrel.crmid=".$id." and (activitytype='Task' or vtiger_activitytype='Call' or vtiger_activitytype='Meeting') and vtiger_crmentity.deleted=0 and (vtiger_activity.status is not NULL && vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL && vtiger_activity.status != 'Deferred') or (vtiger_activity.eventstatus != '' &&  vtiger_activity.eventstatus = 'Planned')";
		$log->debug("Exiting get_activities method ...");
		return  GetRelatedList('Invoice','Activities',$focus,$query,$button,$returnset);
	}
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid,
			vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.createdtime, vtiger_crmentity.description, vtiger_users.user_name
			from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
				inner join vtiger_users on vtiger_crmentity.smcreatorid= vtiger_users.id
				left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
                                left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname	
			where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus != 'Planned' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id;
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Invoice',$query,$id);
	}
	function get_attachments($id)
	{
		global $log;
		$log->debug("Entering get_attachments(".$id.") method ...");
		// Armando L�scher 18.10.2005 -> �visibleDescription
		// Desc: Inserted crm2.createdtime, vtiger_notes.notecontent description, vtiger_users.user_name
		// Inserted inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
		$query = "select vtiger_notes.title,'Notes      '  ActivityType, vtiger_notes.filename,
		vtiger_attachments.type  FileType,crm2.modifiedtime lastmodified,
		vtiger_seattachmentsrel.attachmentsid vtiger_attachmentsid, vtiger_notes.notesid crmid,
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
		$query .= "select vtiger_attachments.description  title ,'Attachments'  ActivityType,
		vtiger_attachments.name filename, vtiger_attachments.type FileType, crm2.modifiedtime lastmodified,
		vtiger_attachments.attachmentsid vtiger_attachmentsid, vtiger_seattachmentsrel.attachmentsid crmid,
			crm2.createdtime, vtiger_attachments.description, vtiger_users.user_name
		from vtiger_attachments
			inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid= vtiger_attachments.attachmentsid
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_seattachmentsrel.crmid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_attachments.attachmentsid
			inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
		where vtiger_crmentity.crmid=".$id;
		$log->debug("Exiting get_attachments method ...");
		return getAttachmentsAndNotes('Invoice',$query,$id);
	}



}

?>
