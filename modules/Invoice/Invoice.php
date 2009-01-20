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
require_once('user_privileges/default_module_view.php');

// Account is used to store vtiger_account information.
class Invoice extends CRMEntity {
	var $log;
	var $db;

	var $table_name = "vtiger_invoice";
	var $table_index= 'invoiceid';
	var $tab_name = Array('vtiger_crmentity','vtiger_invoice','vtiger_invoicebillads','vtiger_invoiceshipads','vtiger_invoicecf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_invoice'=>'invoiceid','vtiger_invoicebillads'=>'invoicebilladdressid','vtiger_invoiceshipads'=>'invoiceshipaddressid','vtiger_invoicecf'=>'invoiceid');
	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_invoicecf', 'invoiceid');			
	
	var $column_fields = Array();

	var $update_product_array = Array();	

	var $sortby_fields = Array('subject','invoice_no','invoicestatus','smownerid','accountname','lastname');

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				//'Invoice No'=>Array('crmentity'=>'crmid'),
				'Invoice No'=>Array('invoice'=>'invoice_no'),		
				'Subject'=>Array('invoice'=>'subject'),
				'Sales Order'=>Array('invoice'=>'salesorderid'),
				'Status'=>Array('invoice'=>'invoicestatus'),
				'Total'=>Array('invoice'=>'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);
	
	var $list_fields_name = Array(
				        'Invoice No'=>'invoice_no',
				        'Subject'=>'subject',
				        'Sales Order'=>'salesorder_id',
				        'Status'=>'invoicestatus',
				        'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				//'Invoice No'=>Array('crmentity'=>'crmid'),
				'Invoice No'=>Array('invoice'=>'invoice_no'),
				'Subject'=>Array('purchaseorder'=>'subject'), 
				);
	
	var $search_fields_name = Array(
				        'Invoice No'=>'',
				        'Subject'=>'subject',
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	//var $groupTable = Array('vtiger_invoicegrouprelation','invoiceid');
	
	var $mandatory_fields = Array('subject','createdtime' ,'modifiedtime');
	
	/**	Constructor which will set the column_fields in this object
	 */
	function Invoice() {
		$this->log =LoggerManager::getLogger('Invoice');
		$this->log->debug("Entering Invoice() method ...");
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Invoice');
		$this->log->debug("Exiting Invoice method ...");
	}


	/** Function to handle the module specific save operations
	
	*/
	
	function save_module($module)
	{
		//Checking if vtiger_salesorderid is present and updating the quote status
		if($this->column_fields["salesorder_id"] != '')
		{
        		$so_id = $this->column_fields["salesorder_id"];
        		$query1 = "update vtiger_salesorder set sostatus='Approved' where salesorderid=?";
        		$this->db->pquery($query1, array($so_id));
		}

		//in ajax save we should not call this function, because this will delete all the existing product values
		if(isset($_REQUEST)) {
			if($_REQUEST['action'] != 'InvoiceAjax' && $_REQUEST['ajxaction'] != 'DETAILVIEW' && $_REQUEST['action'] != 'MassEditSave')
			{
				//Based on the total Number of rows we will save the product relationship with this entity
				saveInventoryProductDetails(&$this, 'Invoice');
			}
		}
		
		// Update the currency id and the conversion rate for the invoice
		$update_query = "update vtiger_invoice set currency_id=?, conversion_rate=? where invoiceid=?";
		$update_params = array($this->column_fields['currency_id'], $this->column_fields['conversion_rate'], $this->id); 
		$this->db->pquery($update_query, $update_params);
	}


	/**	Function used to get the sort order for Invoice listview
	 *	@return string	$sorder	- first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['INVOICE_SORT_ORDER'] if this session value is empty then default sort order will be returned. 
	 */
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

	/**	Function used to get the order by value for Invoice listview
	 *	@return string	$order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['INVOICE_ORDER_BY'] if this session value is empty then default order by will be returned. 
	 */
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


	/**	function used to get the name of the current object
	 *	@return string $this->name - name of the current object
	 */
	function get_summary_text()
	{
		global $log;
		$log->debug("Entering get_summary_text() method ...");
		$log->debug("Exiting get_summary_text method ...");
		return $this->name;
	}


	/**	function used to get the list of activities which are related to the invoice
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetRelatedList
	 */
	function get_activities($id)
	{
		global $log,$singlepane_view;
		$log->debug("Entering get_activities(".$id.") method ...");
		 global $app_strings;
		require_once('modules/Calendar/Activity.php');
	        $focus = new Activity();

		$button = '';

		if($singlepane_view == 'true')
			$returnset = '&return_module=Invoice&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Invoice&return_action=CallRelatedList&return_id='.$id;

		$query = "SELECT case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name,vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid, vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid where vtiger_seactivityrel.crmid=".$id." and activitytype='Task' and vtiger_crmentity.deleted=0 and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL and vtiger_activity.status != 'Deferred')";
		$log->debug("Exiting get_activities method ...");
		return  GetRelatedList('Invoice','Calendar',$focus,$query,$button,$returnset);
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - invoice id
	 *	@return array - return an array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$query = "SELECT vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.contactid,vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,vtiger_crmentity.createdtime, vtiger_crmentity.description, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
                left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid	
			        where vtiger_activity.activitytype='Task'
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred')
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Invoice',$query,$id);
	}



	/**	Function used to get the Status history of the Invoice
	 *	@param $id - invoice id
	 *	@return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are arrays which contains header values and all column values of all entries
	 */
	function get_invoicestatushistory($id)
	{	
		global $log;
		$log->debug("Entering get_invoicestatushistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_invoicestatushistory.*, vtiger_invoice.* from vtiger_invoicestatushistory inner join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_invoicestatushistory.invoiceid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid where vtiger_crmentity.deleted = 0 and vtiger_invoice.invoiceid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Invoice No'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_INVOICE_STATUS'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];
		
		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Account Name , Amount are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$invoicestatus_access = (getFieldVisibilityPermission('Invoice', $current_user->id, 'invoicestatus') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Invoice');

		$invoicestatus_array = ($invoicestatus_access != 1)? $picklistarray['invoicestatus']: array();
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = ($invoicestatus_access != 1)? 'Not Accessible': '-';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			// Module Sequence Numbering
			//$entries[] = $row['invoiceid'];
			$entries[] = $row['invoice_no'];
			// END
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = (in_array($row['invoicestatus'], $invoicestatus_array))? $row['invoicestatus']: $error_msg;
			$entries[] = getDisplayDate($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_invoicestatushistory method ...");

		return $return_data;
	}

	// Function to get column name - Overriding function of base class
	function get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype) {
		if ($columname == 'salesorderid') {
			if ($fldvalue == '') return null;
		}
		return parent::get_column_value($columname, $fldvalue, $fieldname, $uitype, $datatype);
	}
	
	/*
	 * Function to get the secondary query part of a report 
	 * @param - $module primary module name
	 * @param - $secmodule secondary module name
	 * returns the query string formed on fetching the related data for report for secondary module
	 */
	function generateReportsSecQuery($module,$secmodule){
		$tab = getRelationTables($module,$secmodule);
		
		foreach($tab as $key=>$value){
			$tables[]=$key;
			$fields[] = $value;
		}
		$tabname = $tables[0];
		$prifieldname = $fields[0][0];
		$secfieldname = $fields[0][1];
		$tmpname = $tabname."tmp".$secmodule;
		$condvalue = $tables[1].".".$fields[1];
	
		$query = " left join $tabname as $tmpname on $tmpname.$prifieldname = $condvalue  and $tmpname.$secfieldname IN (SELECT invoiceid from vtiger_invoice)";
		$query .= " left join vtiger_invoice as vtiger_invoiceInvoice on vtiger_invoiceInvoice.invoiceid = $tmpname.$secfieldname
			left join vtiger_crmentity as vtiger_crmentityInvoice on vtiger_crmentityInvoice.crmid=vtiger_invoiceInvoice.invoiceid and vtiger_crmentityInvoice.deleted=0
			left join vtiger_invoice on vtiger_invoice.invoiceid = vtiger_crmentityInvoice.crmid
			left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid 
			left join vtiger_salesorder as vtiger_salesorderInvoice on vtiger_salesorderInvoice.salesorderid=vtiger_invoice.salesorderid
			left join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid
			left join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid
			left join vtiger_groups as vtiger_groupsInvoice on vtiger_groupsInvoice.groupid = vtiger_crmentityInvoice.smownerid
			left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentityInvoice.smownerid
			left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_invoice.contactid = vtiger_contactdetailsInvoice.contactid
			left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid ";

		return $query;
	}

	/*
	 * Function to get the relation tables for related modules 
	 * @param - $secmodule secondary module name
	 * returns the array with table names and fieldnames storing relations between module and this module
	 */
	function setRelationTables($secmodule){
		$rel_tables = array (
			"Calendar" =>array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_invoice"=>"invoiceid"),
			"Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_invoice"=>"invoiceid"),
		);
		return $rel_tables[$secmodule];
	}

}

?>