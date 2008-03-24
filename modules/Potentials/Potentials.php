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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Potentials/Potentials.php,v 1.65 2005/04/28 08:08:27 rank Exp $ 
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
require_once('modules/Contacts/Contacts.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Notes/Notes.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// vtiger_potential is used to store customer information.
class Potentials extends CRMEntity {
	var $log;
	var $db;

	var $module_name="Potentials";
	var $module_id = "potentialid";
	var $table_name = "vtiger_potential";

	var $tab_name = Array('vtiger_crmentity','vtiger_potential','vtiger_potentialscf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_potential'=>'potentialid','vtiger_potentialscf'=>'potentialid');

	var $column_fields = Array();

	var $sortby_fields = Array('potentialname','amount','closingdate','smownerid','accountname');


	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
			'Potential'=>Array('potential'=>'potentialname'),
			'Account Name'=>Array('account'=>'accountname'),	  			
			'Sales Stage'=>Array('potential'=>'sales_stage'),
			'Amount'=>Array('potential'=>'amount'),
			'Expected Close Date'=>Array('potential'=>'closingdate'),
			'Assigned To'=>Array('crmentity','smownerid')
			);

	var $list_fields_name = Array(
			'Potential'=>'potentialname',
			'Account Name'=>'account_id',	  			
			'Sales Stage'=>'sales_stage',	  			
			'Amount'=>'amount',
			'Expected Close Date'=>'closingdate',
			'Assigned To'=>'assigned_user_id');

	var $list_link_field= 'potentialname';

	var $search_fields = Array(
			'Potential'=>Array('potential'=>'potentialname'),
			'Account Name'=>Array('potential'=>'account_id'),
			'Expected Close Date'=>Array('potential'=>'closedate')
			);

	var $search_fields_name = Array(
			'Potential'=>'potentialname',
			'Account Name'=>'account_id',
			'Expected Close Date'=>'closingdate'
			);

	var $required_fields =  array(
			"potentialname"=>1,
			"account_id"=>1,
			"closingdate"=>1,
			"sales_stage"=>1
			);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'potentialname';
	var $default_sort_order = 'ASC';

	var $groupTable = Array('vtiger_potentialgrouprelation','potentialid');
	function Potentials() {
		$this->log = LoggerManager::getLogger('potential');
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Potentials');
	}

	function save_module($module)
	{
	}	
	
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
			$sorder = (($_SESSION['POTENTIALS_SORT_ORDER'] != '')?($_SESSION['POTENTIALS_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	/**
	* Function to get order by
	* return string  $order_by    - fieldname(eg: 'Potentialname')
	*/
	function getOrderBy()
	{
		global $log;
                $log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by'])) 
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['POTENTIALS_ORDER_BY'] != '')?($_SESSION['POTENTIALS_ORDER_BY']):($this->default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	

	/** Function to create list query 
	* @param reference variable - where condition is passed when the query is executed
	* Returns Query.
	*/
	function create_list_query($order_by, $where)
	{
		global $log,$current_user;
		require('user_privileges/user_privileges_'.$current_user->id.'.php');
	        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
        	$tab_id = getTabid("Potentials");
		$log->debug("Entering create_list_query(".$order_by.",". $where.") method ...");
		// Determine if the vtiger_account name is present in the where clause.
		$account_required = ereg("accounts\.name", $where);

		if($account_required)
		{
			$query = "SELECT vtiger_potential.potentialid,  vtiger_potential.potentialname, vtiger_potential.dateclosed FROM vtiger_potential, vtiger_account ";
			$where_auto = "account.accountid = vtiger_potential.accountid AND vtiger_crmentity.deleted=0 ";
		}
		else
		{
			$query = 'SELECT vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_crmentity.smcreatorid, vtiger_potential.closingdate FROM vtiger_potential inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid LEFT JOIN vtiger_potentialgrouprelation on vtiger_potential.potentialid = vtiger_potentialgrouprelation.potentialid LEFT JOIN vtiger_groups on vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid ';
			$where_auto = ' AND vtiger_crmentity.deleted=0';
		}

		if($where != "")
			$query .= " where $where ".$where_auto;
		else
			$query .= " where ".$where_auto;
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3)
                {
                                $sec_parameter=getListViewSecurityParameter("Potentials");
                                $query .= $sec_parameter;

                }

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY vtiger_potential.potentialname ";



		$log->debug("Exiting create_list_query method ...");
		return $query;
	}

	/** Function to export the Opportunities records in CSV Format
	* @param reference variable - order by is passed when the query is executed
	* @param reference variable - where condition is passed when the query is executed
	* Returns Export Potentials Query.
	*/
	function create_export_query($where)
	{
		global $log;
		global $current_user;
		$log->debug("Entering create_export_query(". $where.") method ...");

		include("include/utils/ExportUtils.php");

		//To get the Permitted fields query and the permitted fields list
		$sql = getPermittedFieldsQuery("Potentials", "detail_view");
		$fields_list = getFieldsListFromQuery($sql);

		$query = "SELECT $fields_list, vtiger_potentialgrouprelation.groupname as 'Assigned To Group',case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
				FROM vtiger_potential 
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid 
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_account on vtiger_potential.accountid=vtiger_account.accountid  
				LEFT JOIN vtiger_potentialscf on vtiger_potentialscf.potentialid=vtiger_potential.potentialid 
				LEFT JOIN vtiger_potentialgrouprelation
                	                ON vtiger_potentialscf.potentialid = vtiger_potentialgrouprelation.potentialid
	                        LEFT JOIN vtiger_groups
                        	        ON vtiger_groups.groupname = vtiger_potentialgrouprelation.groupname
				LEFT JOIN vtiger_campaign
					ON vtiger_campaign.campaignid = vtiger_potential.campaignid";

		$where_auto = "  vtiger_crmentity.deleted = 0 ";

                if($where != "")
                   $query .= "  WHERE ($where) AND ".$where_auto;
                else
                   $query .= "  WHERE ".$where_auto;

		require('user_privileges/user_privileges_'.$current_user->id.'.php');
		require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
		//we should add security check when the user has Private Access
		if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[2] == 3)
		{
			//Added security check to get the permitted records only
			$query = $query." ".getListViewSecurityParameter("Potentials");
		}

		$log->debug("Exiting create_export_query method ...");
		return $query;

	}



	/** Returns a list of the associated contacts
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_contacts($id)
	{
		global $log, $singlepane_view;
		$log->debug("Entering get_contacts(".$id.") method ...");
		global $app_strings;

		$focus = new Contacts();

		$button = '';

		if(isPermitted("Contacts",3,"") == 'yes')
		{

			$button .= '<input title="Change" accessKey="" tabindex="2" type="button" class="button" value="'.$app_strings['LBL_SELECT_CONTACT_BUTTON_LABEL'].'" name="Button" LANGUAGE=javascript onclick=\'return window.open("index.php?module=Contacts&action=Popup&return_module=Potentials&popuptype=detailview&form=EditView&form_submit=false&recordid='.$_REQUEST["record"].'","test","width=600,height=400,resizable=1,scrollbars=1");\'>&nbsp;';
		}
		if($singlepane_view == 'true')
			$returnset = '&return_module=Potentials&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Potentials&return_action=CallRelatedList&return_id='.$id;

		$query = 'select case when (vtiger_users.user_name not like "") then vtiger_users.user_name else vtiger_groups.groupname end as user_name,vtiger_contactdetails.accountid,vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_contactdetails.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.title, vtiger_contactdetails.department, vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime , vtiger_account.accountname from vtiger_potential inner join vtiger_contpotentialrel on vtiger_contpotentialrel.potentialid = vtiger_potential.potentialid inner join vtiger_contactdetails on vtiger_contpotentialrel.contactid = vtiger_contactdetails.contactid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid left join vtiger_contactgrouprelation on vtiger_contactdetails.contactid=vtiger_contactgrouprelation.contactid left join vtiger_groups on vtiger_groups.groupname=vtiger_contactgrouprelation.groupname left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id where vtiger_potential.potentialid = '.$id.' and vtiger_crmentity.deleted=0';
		
		$log->debug("Exiting get_contacts method ...");
		return GetRelatedList('Potentials','Contacts',$focus,$query,$button,$returnset);
	}

	/** Returns a list of the associated calls
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function get_activities($id)
	{
		global $log, $singlepane_view;
		$log->debug("Entering get_activities(".$id.") method ...");
		global $mod_strings;

		$focus = new Activity();

		$button = '';

		if(isPermitted("Calendar",1,"") == 'yes')
		{

			$button .= '<input title="New Task" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Calendar\';this.form.activity_mode.value=\'Task\';this.form.return_module.value=\'Potentials\'" type="submit" name="button" value="'.$mod_strings['LBL_NEW_TASK'].'">&nbsp;';
			$button .= '<input title="New Event" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.return_action.value=\'DetailView\';this.form.module.value=\'Calendar\';this.form.return_module.value=\'Potentials\';this.form.activity_mode.value=\'Events\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_EVENT'].'">&nbsp;';
		}
		if($singlepane_view == 'true')
			$returnset = '&return_module=Potentials&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Potentials&return_action=CallRelatedList&return_id='.$id;

		$query = "SELECT vtiger_activity.*,vtiger_seactivityrel.*, vtiger_contactdetails.lastname,vtiger_contactdetails.firstname, vtiger_cntactivityrel.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, vtiger_recurringevents.recurringtype from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid = vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid inner join vtiger_potential on vtiger_potential.potentialid=vtiger_seactivityrel.crmid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0 and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred')) or (vtiger_activity.activitytype in ('Meeting','Call') and  vtiger_activity.eventstatus not in ('','Held'))) ";
		$log->debug("Exiting get_activities method ...");
		return GetRelatedList('Potentials','Calendar',$focus,$query,$button,$returnset);

	}

	 /**
	 * Function to get Contact related Products 
	 * @param  integer   $id  - contactid
	 * returns related Products record in array format
	 */
	function get_products($id)
	{
		global $log, $singlepane_view;
		$log->debug("Entering get_products(".$id.") method ...");
		require_once('modules/Products/Products.php');
		global $app_strings;

		$focus = new Products();

		$button = '';

		if(isPermitted("Products",1,"") == 'yes')
		{


			$button .= '<input title="New Product" accessyKey="F" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Products\';this.form.return_module.value=\'Potentials\';this.form.return_action.value=\'DetailView\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_PRODUCT'].'">&nbsp;';
		}
		if(isPermitted("Products",3,"") == 'yes')
		{
			$button .= '<input title="Change" accessKey="" tabindex="2" type="button" class="button" value="'.$app_strings['LBL_SELECT_PRODUCT_BUTTON_LABEL'].'" name="Button" LANGUAGE=javascript onclick=\'return window.open("index.php?module=Products&action=Popup&return_module=Potentials&popuptype=detailview&form=EditView&form_submit=false&recordid='.$_REQUEST["record"].'","test","width=600,height=400,resizable=1,scrollbars=1");\'>&nbsp;';
		}
		if($singlepane_view == 'true')
			$returnset = '&return_module=Potentials&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Potentials&return_action=CallRelatedList&return_id='.$id;

		$query = "SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode, 
				vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price, 
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid 
			   FROM vtiger_products 
			   INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype = 'Potentials' 
			   INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid 
			   INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_seproductsrel.crmid  
			   WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.potentialid = $id";

		$log->debug("Exiting get_products method ...");
		return GetRelatedList('Potentials','Products',$focus,$query,$button,$returnset);
	}

	/**	Function used to get the Sales Stage history of the Potential
	 *	@param $id - potentialid
	 *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
	 */
	function get_stage_history($id)
	{	
		global $log;
		$log->debug("Entering get_stage_history(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_potstagehistory.*, vtiger_potential.potentialname from vtiger_potstagehistory inner join vtiger_potential on vtiger_potential.potentialid = vtiger_potstagehistory.potentialid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid where vtiger_crmentity.deleted = 0 and vtiger_potential.potentialid = ?';
		$result=$adb->pquery($query, array($id));
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['LBL_SALES_STAGE'];
		$header[] = $app_strings['LBL_PROBABILITY'];
		$header[] = $app_strings['LBL_CLOSE_DATE'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		//Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
		//Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.
		global $current_user;

		//If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
		$amount_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'amount') != '0')? 1 : 0;
		$probability_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'probability') != '0')? 1 : 0;
		$picklistarray = getAccessPickListValues('Potentials');

		$potential_stage_array = $picklistarray['sales_stage'];
		//- ==> picklist field is not permitted in profile
		//Not Accessible - picklist is permitted in profile but picklist value is not permitted
		$error_msg = 'Not Accessible';

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			$entries[] = ($amount_access != 1)? $row['amount'] : 0;
			$entries[] = (in_array($row['stage'], $potential_stage_array))? $row['stage']: $error_msg;
			$entries[] = ($probability_access != 1) ? $row['probability'] : 0;
			$entries[] = getDisplayDate($row['closedate']);
			$entries[] = getDisplayDate($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_stage_history method ...");

		return $return_data;
	}
	
	/**
	* Function to get Potential related Task & Event which have activity type Held, Completed or Deferred.
	* @param  integer   $id 
	* returns related Task or Event record in array format
	*/
	function get_history($id)
	{
			global $log;
			$log->debug("Entering get_history(".$id.") method ...");
			$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
		vtiger_activity.eventstatus, vtiger_activity.activitytype,vtiger_activity.date_start, 
		vtiger_activity.due_date, vtiger_activity.time_start,vtiger_activity.time_end,
		vtiger_crmentity.modifiedtime, vtiger_crmentity.createdtime, 
		vtiger_crmentity.description,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name 
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
				left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Potentials',$query,$id);
	}

	/**
	* Function to get Potential related Attachments
	* @param  integer   $id
	* returns related Attachment record in array format
	*/
	function get_attachments($id)
	{
		 global $log;
		$log->debug("Entering get_attachments(".$id.") method ...");
		// Armando L�scher 18.10.2005 -> �visibleDescription
		// Desc: Inserted crm2.createdtime, vtiger_notes.notecontent description, vtiger_users.user_name
		// Inserted inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
		$query = "select vtiger_notes.title,'Notes      '  ActivityType, vtiger_notes.filename,
		vtiger_attachments.type  FileType, crm2.modifiedtime lastmodified,
		vtiger_seattachmentsrel.attachmentsid, vtiger_notes.notesid crmid,
		vtiger_notes.notecontent description, vtiger_users.user_name
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
		$query .= "select vtiger_attachments.subject as title ,'Attachments'  ActivityType,
		vtiger_attachments.name filename, vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,
		vtiger_attachments.attachmentsid, vtiger_seattachmentsrel.attachmentsid crmid,
		vtiger_attachments.description, vtiger_users.user_name
				from vtiger_attachments
				inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid= vtiger_attachments.attachmentsid
				inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_seattachmentsrel.crmid
				inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_attachments.attachmentsid
				inner join vtiger_users on crm2.smcreatorid= vtiger_users.id
				where vtiger_crmentity.crmid=".$id;

		$log->debug("Exiting get_attachments method ...");
		return getAttachmentsAndNotes('Potentials',$query,$id);
	}

	  /**
	  * Function to get Potential related Quotes
	  * @param  integer   $id  - potentialid
	  * returns related Quotes record in array format
	  */
	function get_quotes($id)
	{
		 global $log, $singlepane_view;
		$log->debug("Entering get_quotes(".$id.") method ...");
		global $app_strings;
		require_once('modules/Quotes/Quotes.php');

		if($this->column_fields['account_id']!='')
			$focus = new Quotes();

		$button = '';
		if(isPermitted("Quotes",1,"") == 'yes')
		{
			$button .= '<input title="'.$app_strings['LBL_NEW_QUOTE_BUTTON_TITLE'].'" accessyKey="'.$app_strings['LBL_NEW_QUOTE_BUTTON_KEY'].'" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'Quotes\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_QUOTE_BUTTON'].'">&nbsp;</td>';
		}
		if($singlepane_view == 'true')
			$returnset = '&return_module=Potentials&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Potentials&return_action=CallRelatedList&return_id='.$id;


		$query = "select case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name, vtiger_account.accountname, vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname from vtiger_quotes inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid left join vtiger_quotegrouprelation on vtiger_quotes.quoteid=vtiger_quotegrouprelation.quoteid left join vtiger_groups on vtiger_groups.groupname=vtiger_quotegrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid inner join vtiger_account on vtiger_account.accountid=vtiger_quotes.accountid where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid=".$id;
		$log->debug("Exiting get_quotes method ...");
		return  GetRelatedList('Potentials','Quotes',$focus,$query,$button,$returnset);
	}

	/**
	 * Function to get Potential related SalesOrder 
 	 * @param  integer   $id  - potentialid
	 * returns related SalesOrder record in array format
	 */	 
	function get_salesorder($id)
	{
		global $log, $singlepane_view;
		$log->debug("Entering get_salesorder(".$id.") method ...");
		require_once('modules/SalesOrder/SalesOrder.php');
		global $mod_strings;
		global $app_strings;

		$focus = new SalesOrder();

		$button = '';
		if(isPermitted("SalesOrder",1,"") == 'yes')
		{
			$button .= '<input title="'.$app_strings['LBL_NEW_SORDER_BUTTON_TITLE'].'" accessyKey="'.$app_strings['LBL_NEW_SORDER_BUTTON_KEY'].'" class="button" onclick="this.form.action.value=\'EditView\';this.form.module.value=\'SalesOrder\'" type="submit" name="button" value="'.$app_strings['LBL_NEW_SORDER_BUTTON'].'">&nbsp;</td>';
		}

		if($singlepane_view == 'true')
			$returnset = '&return_module=Potentials&return_action=DetailView&return_id='.$id;
		else
			$returnset = '&return_module=Potentials&return_action=CallRelatedList&return_id='.$id;


		$query = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename, vtiger_account.accountname, vtiger_potential.potentialname,case when (vtiger_users.user_name not like '') then vtiger_users.user_name else vtiger_groups.groupname end as user_name
			from vtiger_salesorder 
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
			left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid 
			left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid 
			left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_salesorder.potentialid 
			left join vtiger_sogrouprelation on vtiger_salesorder.salesorderid=vtiger_sogrouprelation.salesorderid 
			left join vtiger_groups on vtiger_groups.groupname=vtiger_sogrouprelation.groupname
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			 where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid = ".$id;
		$log->debug("Exiting get_salesorder method ...");
		return GetRelatedList('Potentials','SalesOrder',$focus,$query,$button,$returnset);

	}




}



?>
