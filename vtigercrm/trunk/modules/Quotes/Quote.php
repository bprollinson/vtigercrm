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
require_once('include/RelatedListView.php');
// Account is used to store vtiger_account information.
class Quote extends CRMEntity {
	var $log;
	var $db;
		
	var $table_name = "vtiger_quotes";
	var $tab_name = Array('vtiger_crmentity','vtiger_quotes','vtiger_quotesbillads','vtiger_quotesshipads','vtiger_quotescf');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_quotes'=>'quoteid','vtiger_quotesbillads'=>'quotebilladdressid','vtiger_quotesshipads'=>'quoteshipaddressid','vtiger_quotescf'=>'quoteid');
	
	var $entity_table = "vtiger_crmentity";
	
	var $billadr_table = "vtiger_quotesbillads";

	var $object_name = "Quote";

	var $new_schema = true;
	
	var $module_id = "quoteid";

	var $column_fields = Array();

	var $sortby_fields = Array('subject','crmid','smownerid');		

	// This is used to retrieve related vtiger_fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'smownerid', 'opportunity_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id' );

	// This is the list of vtiger_fields that are in the lists.
	var $list_fields = Array(
				'Quote Id'=>Array('crmentity'=>'crmid'),
				'Subject'=>Array('quotes'=>'subject'),
				'Quote Stage'=>Array('quotes'=>'quotestage'), 
				'Potential Name'=>Array('quotes'=>'potentialid'),
				'Account Name'=>Array('account'=> 'accountid'),
				'Total'=>Array('quotes'=> 'total'),
				'Assigned To'=>Array('crmentity'=>'smownerid')
				);
	
	var $list_fields_name = Array(
				        'Quote Id'=>'',
				        'Subject'=>'subject',
				        'Quote Stage'=>'quotestage',
				        'Potential Name'=>'potential_id',
					'Account Name'=>'account_id',
					'Total'=>'hdnGrandTotal',
				        'Assigned To'=>'assigned_user_id'
				      );
	var $list_link_field= 'subject';

	var $search_fields = Array(
				'Quote Id'=>Array('crmentity'=>'crmid'),
				'Subject'=>Array('quotes'=>'subject'),
				'Account Name'=>Array('quotes'=>'accountid'),
				'Quote Stage'=>Array('quotes'=>'quotestage'), 
				);
	
	var $search_fields_name = Array(
					'Quote Id'=>'',
				        'Subject'=>'subject',
				        'Account Name'=>'account_id',
				        'Quote Stage'=>'quotestage',
				      );

	// This is the list of vtiger_fields that are required.
	var $required_fields =  array("accountname"=>1);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'ASC';

	/**	Constructor which will set the column_fields in this object
	 */
	function Quote() {
		$this->log =LoggerManager::getLogger('quote');
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('Quotes');
	}
	
	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	/**	Function used to get the sort order for Quote listview
	 *	return string	$sorder	- first check the $_REQUEST['sorder'] if request value is empty then check in the $_SESSION['QUOTES_SORT_ORDER'] if this session value is empty then default sort order will be returned. 
	 */
	function getSortOrder()
	{
		global $log;
                $log->debug("Entering getSortOrder() method ...");	
		if(isset($_REQUEST['sorder'])) 
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['QUOTES_SORT_ORDER'] != '')?($_SESSION['QUOTES_SORT_ORDER']):($this->default_sort_order));
		$log->debug("Exiting getSortOrder() method ...");
		return $sorder;
	}

	/**	Function used to get the order by value for Quotes listview
	 *	return string	$order_by  - first check the $_REQUEST['order_by'] if request value is empty then check in the $_SESSION['QUOTES_ORDER_BY'] if this session value is empty then default order by will be returned. 
	 */
	function getOrderBy()
	{
		global $log;
                $log->debug("Entering getOrderBy() method ...");
		if (isset($_REQUEST['order_by'])) 
			$order_by = $_REQUEST['order_by'];
		else
			$order_by = (($_SESSION['QUOTES_ORDER_BY'] != '')?($_SESSION['QUOTES_ORDER_BY']):($this->default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	
	// Mike Crowe Mod --------------------------------------------------------

	/**	function used to get the list of sales orders which are related to the Quotes
	 *	@param int $id - quote id
	 *	return array - array which will be returned from the function GetRelatedList
	 */
	function get_salesorder($id)
	{
		global $log;
		$log->debug("Entering get_salesorder(".$id.") method ...");
		require_once('modules/SalesOrder/SalesOrder.php');
	        $focus = new SalesOrder();
 
		$button = '';

		$returnset = '&return_module=Quotes&return_action=CallRelatedList&return_id='.$id;

		$query = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename, vtiger_account.accountname from vtiger_salesorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid left join vtiger_sogrouprelation on vtiger_salesorder.salesorderid=vtiger_sogrouprelation.salesorderid left join vtiger_groups on vtiger_groups.groupname=vtiger_sogrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_salesorder.quoteid = ".$id;
		$log->debug("Exiting get_salesorder method ...");
		return GetRelatedList('Quotes','SalesOrder',$focus,$query,$button,$returnset);
	}

	/**	function used to get the list of activities which are related to the Quotes
	 *	@param int $id - quote id
	 *	return array - array which will be returned from the function GetRelatedList
	 */
	function get_activities($id)
	{	
		global $log;
		$log->debug("Entering get_activities(".$id.") method ...");
		global $app_strings;
		require_once('modules/Activities/Activity.php');
	        $focus = new Activity();

		$button = '';

		$returnset = '&return_module=Quotes&return_action=CallRelatedList&return_id='.$id;

		$query = "SELECT vtiger_contactdetails.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_activity.*,vtiger_seactivityrel.*,vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_users.user_name,vtiger_recurringevents.recurringtype from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_seactivityrel.crmid=".$id." and (activitytype='Task' or activitytype='Call' or activitytype='Meeting') and (vtiger_activity.status is not NULL && vtiger_activity.status != 'Completed') and (vtiger_activity.status is not NULL && vtiger_activity.status != 'Deferred') or (vtiger_activity.eventstatus !='' && vtiger_activity.eventstatus = 'Planned')";
		$log->debug("Exiting get_activities method ...");
		return GetRelatedList('Quotes','Activities',$focus,$query,$button,$returnset);
	}

	/**	function used to get the the activity history related to the quote
	 *	@param int $id - quote id
	 *	return array - array which will be returned from the function GetHistory
	 */
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
			vtiger_activity.eventstatus, vtiger_activity.activitytype, vtiger_contactdetails.contactid,
			vtiger_contactdetails.firstname,vtiger_contactdetails.lastname, vtiger_crmentity.modifiedtime,
			vtiger_crmentity.createdtime, vtiger_crmentity.description, vtiger_users.user_name
			from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid
				left join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid
				inner join vtiger_users on vtiger_crmentity.smcreatorid= vtiger_users.id
				left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
                                left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname
			where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
  				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus !='Planned' and vtiger_activity.eventstatus != ''))
	 	        	and vtiger_seactivityrel.crmid=".$id;
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

		$log->debug("Exiting get_history method ...");
		return getHistory('Quotes',$query,$id);	
	}


	/**	Function used to get the Quote Stage history of the Quotes
	 *	@param $id - quote id
	 *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
	 */
	function get_quotestagehistory($id)
	{	
		global $log;
		$log->debug("Entering get_quotestagehistory(".$id.") method ...");

		global $adb;
		global $mod_strings;
		global $app_strings;

		$query = 'select vtiger_quotestagehistory.*, vtiger_quotes.subject from vtiger_quotestagehistory inner join vtiger_quotes on vtiger_quotes.quoteid = vtiger_quotestagehistory.quoteid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_quotes.quoteid where vtiger_crmentity.deleted = 0 and vtiger_quotes.quoteid = '.$id;
		$result=$adb->query($query);
		$noofrows = $adb->num_rows($result);

		$header[] = $app_strings['Quote Id'];
		$header[] = $app_strings['LBL_ACCOUNT_NAME'];
		$header[] = $app_strings['LBL_AMOUNT'];
		$header[] = $app_strings['Quote Stage'];
		$header[] = $app_strings['LBL_LAST_MODIFIED'];

		while($row = $adb->fetch_array($result))
		{
			$entries = Array();

			$entries[] = $row['quoteid'];
			$entries[] = $row['accountname'];
			$entries[] = $row['total'];
			$entries[] = $row['quotestage'];
			$entries[] = getDisplayDate($row['lastmodified']);

			$entries_list[] = $entries;
		}

		$return_data = Array('header'=>$header,'entries'=>$entries_list);

	 	$log->debug("Exiting get_quotestagehistory method ...");

		return $return_data;
	}

}

?>
