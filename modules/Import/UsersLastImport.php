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
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('data/SugarBean.php');

$imported_ids = array();

// Contact is used to store customer information.
class UsersLastImport extends SugarBean 
{
	var $log;
	var $db;

	// Stored vtiger_fields
	var $id;
	var $assigned_user_id;
	var $bean_type;
	var $bean_id;

	var $table_name = "vtiger_users_last_import";
	var $object_name = "UsersLastImport";
	var $column_fields = Array(
					"id"
					,"assigned_user_id"
					,"bean_type"
					,"bean_id"
					,"deleted"
				  );

	var $new_schema = true;

	var $additional_column_fields = Array();

	var $list_fields = Array();
	var $list_fields_name = Array();
	var $list_link_field;
	
	/**	Constructor
	 */	
	function UsersLastImport() {
		$this->log = LoggerManager::getLogger('UsersLastImport');
		$this->db = new PearDatabase();
	}

	/**	function used to delete ie., update the deleted as 1 in vtiger_users_last_import table
	 *	@param int $user_id - user id to whom's last imported records to delete
	 *	@return void
	 */
	function mark_deleted_by_user_id($user_id)
        {
                $query = "UPDATE $this->table_name set deleted=1 where assigned_user_id='$user_id'";
                $this->db->query($query,true,"Error marking last imported vtiger_accounts deleted: ");
        }

	/**	function used to get the list query of the imported records
	 *	@param reference &$order_by - reference of the variable order_by to add with the query
	 *	@param reference &$where - where condition to add with the query
	 *	@return string $query - return the list query to get the imported records list
	 */
	function create_list_query(&$order_by, &$where)
	{
		global $current_user;
		$query = '';

		$this->db->println("create list bean_type = ".$this->bean_type." where = ".$where);

		if ($this->bean_type == 'Contacts')
		{
				$query = "SELECT distinct crmid,
			vtiger_account.accountname as vtiger_account_name,
			vtiger_contactdetails.contactid,
			vtiger_contactdetails.accountid,				
			vtiger_contactdetails.yahooid,
			vtiger_contactdetails.firstname,
			vtiger_contactdetails.lastname,
			vtiger_contactdetails.phone,
			vtiger_contactdetails.title,
			vtiger_contactdetails.email,
			vtiger_users.id as assigned_user_id,
				smownerid,
                                vtiger_users.user_name as assigned_user_name
				FROM vtiger_contactdetails
				left join vtiger_users_last_import on vtiger_users_last_import.bean_id=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_users ON vtiger_contactdetails.contactid=vtiger_users.id 
				LEFT JOIN vtiger_account  ON vtiger_account.accountid=vtiger_contactdetails.accountid 
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_contactdetails.contactid  
				WHERE vtiger_users_last_import.assigned_user_id= '{$current_user->id}'  
				AND vtiger_users_last_import.bean_type='Contacts' 
				AND vtiger_users_last_import.deleted=0  AND vtiger_crmentity.deleted=0";
			
		} 
		else if ($this->bean_type == 'Accounts')
		{
				$query = "SELECT distinct vtiger_account.*, vtiger_accountbillads.city,
                                vtiger_users.user_name assigned_user_name,
				crmid, smownerid 
				FROM vtiger_account
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid
				inner join vtiger_accountbillads on vtiger_crmentity.crmid=vtiger_accountbillads.accountaddressid
				left join vtiger_users_last_import on vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
			       	left join vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				WHERE 
			vtiger_users_last_import.assigned_user_id=
					'{$current_user->id}'
				AND vtiger_users_last_import.bean_type='Accounts'
				AND vtiger_users_last_import.deleted=0
				AND vtiger_crmentity.deleted=0
				AND vtiger_users.status='Active'";
		} 
		else if ($this->bean_type == 'Potentials')
		{
		
			$query = "SELECT distinct
                                vtiger_account.accountid vtiger_account_id,
                                vtiger_account.accountname vtiger_account_name,
                                vtiger_users.user_name assigned_user_name,
			vtiger_crmentity.crmid, smownerid,
			vtiger_potential.*
                               FROM vtiger_potential 
			       inner join vtiger_account on vtiger_account.accountid=vtiger_potential.accountid 
			       inner join  vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid 
			       left join vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id 
			       left join vtiger_users_last_import on vtiger_users_last_import.assigned_user_id=vtiger_users.id 
			       where vtiger_users_last_import.assigned_user_id='{$current_user->id}'
				AND vtiger_users_last_import.bean_type='Potentials'
				AND vtiger_users_last_import.bean_id=vtiger_crmentity.crmid
				AND vtiger_users_last_import.deleted=0
				AND vtiger_crmentity.deleted=0 
				AND vtiger_users.status='Active'";

		}
		else if($this->bean_type == 'Leads')
		{
			$query = "SELECT distinct vtiger_leaddetails.*, vtiger_crmentity.crmid, vtiger_leadaddress.phone,vtiger_leadsubdetails.website,
                                vtiger_users.user_name assigned_user_name,
				smownerid 
				FROM vtiger_leaddetails 
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid 
				inner join vtiger_leadaddress on vtiger_crmentity.crmid=vtiger_leadaddress.leadaddressid 
				inner join vtiger_leadsubdetails on vtiger_crmentity.crmid=vtiger_leadsubdetails.leadsubscriptionid 
				left join vtiger_users_last_import on vtiger_users_last_import.bean_id=vtiger_crmentity.crmid			       	
				left join vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				WHERE 
			vtiger_users_last_import.assigned_user_id=
					'{$current_user->id}'
				AND vtiger_users_last_import.bean_type='Leads'
				AND vtiger_users_last_import.deleted=0
				AND vtiger_crmentity.deleted=0
				AND vtiger_users.status='Active'";
		}

		
		return $query;

	}

	/*
	function list_view_parse_additional_sections(&$list_form)
	{
		if ($this->bean_type == "Contacts")
		{
                	if( isset($this->yahoo_id) && $this->yahoo_id != '')
			{
                        	$list_form->parse("main.row.yahoo_id");
			}
                	else
			{
                        	$list_form->parse("main.row.no_yahoo_id");
			}
		}
                return $list_form;

        }
	*/
	
	/**	function used to delete (update deleted=1 in crmentity table) the last imported records of the current user
	 *	@param int $user_id - user id, whose last imported records want to be deleted
	 *	@return int $count - return the number of total deleted records (contacts, accounts, opportunities, leads and products)
	 */
	function undo($user_id)
	{
		$count = 0;

		$count += $this->undo_contacts($user_id);
		$count += $this->undo_accounts($user_id);
		$count += $this->undo_opportunities($user_id);
		$count += $this->undo_leads($user_id);
		$count += $this->undo_products($user_id);

		return $count;
	}

	/**	function used to delete (update deleted=1 in crmentity table) the last imported contacts of the current user
	 *	@param int $user_id - user id, whose last imported contacts want to be deleted
	 *	@return int $count - return the number of deleted contacts 
	 */
	function undo_contacts($user_id)
	{
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id='$user_id' AND bean_type='Contacts' AND deleted=0";

		$this->log->info($query1); 

		$result1 = $this->db->query($query1) or die("Error getting last import for undo: ".mysql_error()); 

		while ( $row1 = $this->db->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid='{$row1['bean_id']}'";

			$this->log->info($query2); 

			$result2 = $this->db->query($query2) or die("Error undoing last import: ".mysql_error()); 

			$count++;
			
		}
		return $count;
	}

	/**	function used to delete (update deleted=1 in crmentity table) the last imported leads of the current user
	 *	@param int $user_id - user id, whose last imported leads want to be deleted
	 *	@return int $count - return the number of deleted leads
	 */
	function undo_leads($user_id)
	{
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id='$user_id' AND bean_type='Leads' AND deleted=0";

		$this->log->info($query1); 

		$result1 = $this->db->query($query1) or die("Error getting last import for undo: ".mysql_error()); 

		while ( $row1 = $this->db->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid='{$row1['bean_id']}'";

			$this->log->info($query2); 

			$result2 = $this->db->query($query2) or die("Error undoing last import: ".mysql_error()); 

			$count++;
			
		}
		return $count;
	}

	/**	function used to delete (update deleted=1 in crmentity table) the last imported accounts of the current user
	 *	@param int $user_id - user id, whose last imported accounts want to be deleted
	 *	@return int $count - return the number of deleted accounts
	 */
	function undo_accounts($user_id)
	{
		// this should just be a loop foreach module type
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id='$user_id' AND bean_type='Accounts' AND deleted=0";

		$this->log->info($query1); 

		$result1 = $this->db->query($query1) or die("Error getting last import for undo: ".mysql_error()); 

		while ( $row1 = $this->db->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid='{$row1['bean_id']}'";

			$this->log->info($query2); 

			$result2 = $this->db->query($query2) or die("Error undoing last import: ".mysql_error()); 

			$count++;

		}
		return $count;
	}

	/**	function used to delete (update deleted=1 in crmentity table) the last imported potentials of the current user
	 *	@param int $user_id - user id, whose last imported potentials want to be deleted
	 *	@return int $count - return the number of deleted potentials
	 */
	function undo_opportunities($user_id)
	{
		// this should just be a loop foreach module type
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id='$user_id' AND bean_type='Potentials' AND deleted=0";

		$this->log->info($query1); 

		$result1 = $this->db->query($query1) or die("Error getting last import for undo: ".mysql_error()); 

		while ( $row1 = $this->db->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid='{$row1['bean_id']}'";

			$this->log->info($query2); 

			$result2 = $this->db->query($query2) or die("Error undoing last import: ".mysql_error()); 

			$count++;

		}
		return $count;
	}

	/**	function used to delete (update deleted=1 in crmentity table) the last imported products of the current user
	 *	@param int $user_id - user id, whose last imported products want to be deleted
	 *	@return int $count - return the number of deleted products
	 */
	function undo_products($user_id)
	{
		$count = 0;
		$query1 = "select bean_id from vtiger_users_last_import where assigned_user_id='$user_id' AND bean_type='Products' AND deleted=0";

		$this->log->info($query1); 

		$result1 = $this->db->query($query1) or die("Error getting last import for undo: ".mysql_error()); 

		while ( $row1 = $this->db->fetchByAssoc($result1))
		{
			$query2 = "update vtiger_crmentity set deleted=1 where crmid='{$row1['bean_id']}'";

			$this->log->info($query2); 

			$result2 = $this->db->query($query2) or die("Error undoing last import: ".mysql_error()); 

			$count++;
		}
		return $count;
	}

}


?>
