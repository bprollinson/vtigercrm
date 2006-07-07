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
require_once('include/utils/utils.php');


class HelpDesk extends CRMEntity {
	var $log;
	var $db;

	var $tab_name = Array('vtiger_crmentity','vtiger_troubletickets','vtiger_seticketsrel','vtiger_ticketcf','vtiger_ticketcomments','vtiger_attachments');
	var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_troubletickets'=>'ticketid','vtiger_seticketsrel'=>'ticketid','vtiger_ticketcf'=>'ticketid','vtiger_ticketcomments'=>'ticketid','vtiger_attachments'=>'attachmentsid');
	var $column_fields = Array();

	var $sortby_fields = Array('title','status','priority','crmid','firstname','smownerid');

	var $list_fields = Array(
					'Ticket ID'=>Array('crmentity'=>'crmid'),
					'Subject'=>Array('troubletickets'=>'title'),	  			
					'Related to'=>Array('troubletickets'=>'parent_id'),	  			
					'Status'=>Array('troubletickets'=>'status'),
					'Priority'=>Array('troubletickets'=>'priority'),
					'Assigned To'=>Array('crmentity','smownerid')
				);

	var $list_fields_name = Array(
					'Ticket ID'=>'',
					'Subject'=>'ticket_title',	  			
					'Related to'=>'parent_id',	  			
					'Status'=>'ticketstatus',
					'Priority'=>'ticketpriorities',
					'Assigned To'=>'assigned_user_id'
				     );

	var $list_link_field= 'ticket_title';
			
	var $range_fields = Array(
				        'ticketid',
					'title',
			        	'firstname',
				        'lastname',
			        	'parent_id',
			        	'productid',
			        	'productname',
			        	'priority',
			        	'severity',
				        'status',
			        	'category',
					'description',
					'solution',
					'modifiedtime',
					'createdtime'
				);

	//Added these variables which are used as default order by and sortorder in ListView
	var $default_order_by = 'crmid';
	var $default_sort_order = 'DESC';

	function HelpDesk() 
	{
		$this->log =LoggerManager::getLogger('helpdesk');
		$this->log->debug("Entering HelpDesk() method ...");
		$this->db = new PearDatabase();
		$this->column_fields = getColumnFields('HelpDesk');
		$this->log->debug("Exiting HelpDesk method ...");
	}

	// Mike Crowe Mod --------------------------------------------------------Default ordering for us
	function getSortOrder()
	{
		global $log;
                $log->debug("Entering getSortOrder() method ...");	
		if(isset($_REQUEST['sorder'])) 
			$sorder = $_REQUEST['sorder'];
		else
			$sorder = (($_SESSION['HELPDESK_SORT_ORDER'] != '')?($_SESSION['HELPDESK_SORT_ORDER']):($this->default_sort_order));
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
			$order_by = (($_SESSION['HELPDESK_ORDER_BY'] != '')?($_SESSION['HELPDESK_ORDER_BY']):($this->default_order_by));
		$log->debug("Exiting getOrderBy method ...");
		return $order_by;
	}	

	/**     Function to form the query to get the list of activities
         *      @param  int $id - ticket id
         *      @return void
         *       but this function will call the function renderRelatedActivities with parameters query and ticket id
        **/
	function get_activities($id)
	{
		global $log;
		$log->debug("Entering get_activities(".$id.") method ...");
		global $mod_strings;
		global $app_strings;
		require_once('modules/Activities/Activity.php');
		$focus = new Activity();

		$button = '';

		$returnset = '&return_module=HelpDesk&return_action=CallRelatedList&return_id='.$id;

		$query = "SELECT vtiger_activity.*, vtiger_crmentity.crmid, vtiger_contactdetails.contactid, vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_recurringevents.recurringtype, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime, vtiger_users.user_name from vtiger_activity inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid left join vtiger_contactdetails on vtiger_contactdetails.contactid= vtiger_cntactivityrel.contactid left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_crmentity.crmid left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname where vtiger_seactivityrel.crmid=".$id." and (activitytype='Task' or activitytype='Call' or activitytype='Meeting') AND ( vtiger_activity.status is NULL OR vtiger_activity.status != 'Completed' ) and ( vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus != 'Held')";
		$log->debug("Exiting get_activities method ...");
		
		return GetRelatedList('HelpDesk','Activities',$focus,$query,$button,$returnset);
	}

	/**     Function to get the Ticket History information as in array format
	 *	@param int $ticketid - ticket id
	 *	return array with title and the ticket history informations in the following format
							array(	
								header=>array('0'=>'title'),
								entries=>array('0'=>'info1','1'=>'info2',etc.,)
							     )
	 */
	function get_ticket_history($ticketid)
	{
		global $log, $adb;
		$log->debug("Entering into get_ticket_history($ticketid) method ...");

		$query="select title,update_log from vtiger_troubletickets where ticketid=".$ticketid;
		$result=$adb->query($query);
		$update_log = $adb->query_result($result,0,"update_log");

		$splitval = split('--//--',trim($update_log,'--//--'));

		$header[] = $adb->query_result($result,0,"title");

		$return_value = Array('header'=>$header,'entries'=>$splitval);

		$log->debug("Exiting from get_ticket_history($ticketid) method ...");

		return $return_value;
	}

	/**	Function to form the query to get the list of vtiger_attachments and vtiger_notes
	 *	@param  int $id - ticket id
	 *	@return void
	 *	 but this function will call the function renderRelatedAttachments with parameters query and ticket id
	**/
	function get_attachments($id)
	{
		global $log;
		$log->debug("Entering get_attachments(".$id.") method ...");
		$query = "select vtiger_notes.title,'Notes      '  ActivityType, vtiger_notes.filename,
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

		$query .= "select vtiger_attachments.description title ,'Attachments'  ActivityType,
		vtiger_attachments.name filename, vtiger_attachments.type FileType,crm2.modifiedtime lastmodified,
		vtiger_attachments.attachmentsid attachmentsid, vtiger_seattachmentsrel.attachmentsid crmid,
			crm2.createdtime, vtiger_attachments.description, vtiger_users.user_name
		from vtiger_attachments
			inner join vtiger_seattachmentsrel on vtiger_seattachmentsrel.attachmentsid= vtiger_attachments.attachmentsid
			inner join vtiger_crmentity on vtiger_crmentity.crmid= vtiger_seattachmentsrel.crmid
			inner join vtiger_crmentity crm2 on crm2.crmid=vtiger_attachments.attachmentsid
			left join vtiger_users on crm2.smcreatorid= vtiger_users.id
		where vtiger_crmentity.crmid=".$id;	
		$log->debug("Exiting get_attachments method ...");
		return getAttachmentsAndNotes('HelpDesk',$query,$id);
	}

	/**	Function to get the ticket comments as a array
	 *	@param  int   $ticketid - ticketid
	 *	@return array $output - array(	
						[$i][comments]    => comments
						[$i][owner]       => name of the user or customer who made the comment
						[$i][createdtime] => the comment created time
					     ) 
				where $i = 0,1,..n which are all made for the ticket
	**/
	function get_ticket_comments_list($ticketid)
	{
		global $log;
		$log->debug("Entering get_ticket_comments_list(".$ticketid.") method ...");
		 $sql = "select * from vtiger_ticketcomments where ticketid=".$ticketid." order by createdtime DESC";
		 $result = $this->db->query($sql);
		 $noofrows = $this->db->num_rows($result);
		 for($i=0;$i<$noofrows;$i++)
		 {
			 $ownerid = $this->db->query_result($result,$i,"ownerid");
			 $ownertype = $this->db->query_result($result,$i,"ownertype");
			 if($ownertype == 'user')
				 $name = getUserName($ownerid);
			 elseif($ownertype == 'customer')
			 {
				 $sql1 = 'select * from vtiger_portalinfo where id='.$ownerid;
				 $name = $this->db->query_result($this->db->query($sql1),0,'user_name');
			 }

			 $output[$i]['comments'] = nl2br($this->db->query_result($result,$i,"comments"));
			 $output[$i]['owner'] = $name;
			 $output[$i]['createdtime'] = $this->db->query_result($result,$i,"createdtime");
		 }
		$log->debug("Exiting get_ticket_comments_list method ...");
		 return $output;
	 }
		
	/**	Function to form the query which will give the list of tickets based on customername and id ie., contactname and contactid
	 *	@param  string $user_name - name of the customer ie., contact name
	 *	@param  int    $id	 - contact id 
	 * 	@return array  which is return from the function process_list_query
	**/
	function get_user_tickets_list($user_name,$id,$where='',$match='')
	{
		global $log;
		$log->debug("Entering get_user_tickets_list(".$user_name.",".$id.",".$where.",".$match.") method ...");

		$this->db->println("where ==> ".$where);

		$query = "select vtiger_crmentity.crmid, vtiger_troubletickets.*, vtiger_crmentity.smownerid, vtiger_crmentity.createdtime, vtiger_crmentity.modifiedtime, vtiger_contactdetails.firstname, vtiger_contactdetails.lastname, vtiger_products.productid, vtiger_products.productname, vtiger_ticketcf.* from vtiger_troubletickets inner join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid left join vtiger_contactdetails on vtiger_troubletickets.parent_id=vtiger_contactdetails.contactid left join vtiger_products on vtiger_products.productid = vtiger_troubletickets.product_id left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id  where vtiger_crmentity.deleted=0 and vtiger_contactdetails.email='".$user_name."' and vtiger_troubletickets.parent_id = '".$id."'";

		if(trim($where) != '')
		{
			if($match == 'all' || $match == '')
			{
				$join = " and ";
			}
			elseif($match == 'any')
			{
				$join = " or ";
			}
			$where = explode("&&&",$where);
			$count = count($where);
			$count --;
			$where_conditions = "";
			foreach($where as $key => $value)
			{
				$this->db->println('key : '.$key.'...........value : '.$value);
				$val = explode(" = ",$value);
				$this->db->println('val0 : '.$val[0].'...........val1 : '.$val[1]);
				if($val[0] == 'vtiger_troubletickets.title')
				{
					$where_conditions .= $val[0]."  ".$val[1];
					if($count != $key) 	$where_conditions .= $join;
				}
				elseif($val[1] != '' && $val[1] != 'Any')
				{
					$where_conditions .= $val[0]." = ".$val[1];
					if($count != $key)	$where_conditions .= $join;
				}
			}
			if($where_conditions != '')
				$where_conditions = " and ( ".$where_conditions." ) ";

			$query .= $where_conditions;
			$this->db->println("where condition for customer portal tickets search : ".$where_conditions);
		}

		$query .= " order by vtiger_crmentity.crmid desc";
		$log->debug("Exiting get_user_tickets_list method ...");
		return $this->process_list_query($query);
	}

	/**	Function to process the query and return the result with number of rows
	 *	@param  string $query - query 
	 *	@return array  $response - array(	list           => array(   
											$i => array(key => val)   
									       ),
							row_count      => '',
							next_offset    => '',
							previous_offset	=>''		 
						)
		where $i=0,1,..n & key = ticketid, title, firstname, ..etc(range_fields) & val = value of the key from db retrieved row 
	**/
	function process_list_query($query)
	{
		global $log;
		$log->debug("Entering process_list_query(".$query.") method ...");
	  
   		$result =& $this->db->query($query,true,"Error retrieving $this->object_name list: ");
		$list = Array();
	        $rows_found =  $this->db->getRowCount($result);
        	if($rows_found != 0)
	        {
			$ticket = Array();
			for($index = 0 , $row = $this->db->fetchByAssoc($result, $index); $row && $index <$rows_found;$index++, $row = $this->db->fetchByAssoc($result, $index))
			{
		                foreach($this->range_fields as $columnName)
                		{
		                	if (isset($row[$columnName])) 
					{
			                	$ticket[$columnName] = $row[$columnName];
                    			}
		                       	else     
				        {   
		                        	$ticket[$columnName] = "";
			                }   
	     			}	
    		                $list[] = $ticket;
                	}
        	}   

		$response = Array();
	        $response['list'] = $list;
        	$response['row_count'] = $rows_found;
	        $response['next_offset'] = $next_offset;
        	$response['previous_offset'] = $previous_offset;

		$log->debug("Exiting process_list_query method ...");
	        return $response;
	}

	/**	Function to get the HelpDesk vtiger_field labels in caps letters without space
	 *	return array $mergeflds - array(	key => val	)    where   key=0,1,2..n & val = ASSIGNEDTO,RELATEDTO, .,etc
	**/
	function getColumnNames_Hd()
	{
		global $log;
		$log->debug("Entering getColumnNames_Hd() method ...");
		$sql1 = "select fieldlabel from vtiger_field where tabid=13 and block <> 6 ";
		$result = $this->db->query($sql1);
		$numRows = $this->db->num_rows($result);
		for($i=0; $i < $numRows;$i++)
		{
			$custom_fields[$i] = $this->db->query_result($result,$i,"fieldlabel");
			$custom_fields[$i] = ereg_replace(" ","",$custom_fields[$i]);
			$custom_fields[$i] = strtoupper($custom_fields[$i]);
		}
		$mergeflds = $custom_fields;
		$log->debug("Exiting getColumnNames_Hd method ...");
		return $mergeflds;
	}

	/**     Function to get the list of comments for the given ticket id
	 *      @param  int  $ticketid - Ticket id
	 *      @return list $list - the list of comments which are formed as boxed info with div tags.
	**/
	function getCommentInformation($ticketid)
	{
		global $log;
		$log->debug("Entering getCommentInformation(".$ticketid.") method ...");
		global $adb;
		global $mod_strings;
		$sql = "select * from vtiger_ticketcomments where ticketid=".$ticketid;
		$result = $adb->query($sql);
		$noofrows = $adb->num_rows($result);

		if($noofrows == 0)
		{
			$log->debug("Exiting getCommentInformation method ...");
			return '';
		}

		$list .= '<div style="overflow: auto;height:200px;width:100%;">';
		for($i=0;$i<$noofrows;$i++)
		{
			if($adb->query_result($result,$i,'comments') != '')
			{
				//this div is to display the comment
				$list .= '<div valign="top" style="width:99%;padding-top:10px;" class="dataField">';
				$list .= make_clickable(nl2br($adb->query_result($result,$i,'comments')));

				$list .= '</div>';

				//this div is to display the author and time
				$list .= '<div valign="top" style="width:99%;border-bottom:1px dotted #CCCCCC;padding-bottom:5px;" class="dataLabel"><font color=darkred>';
				$list .= $mod_strings['LBL_AUTHOR'].' : ';

				if($adb->query_result($result,$i,'ownertype') == 'user')
					$list .= getUserName($adb->query_result($result,$i,'ownerid'));
				else
					$list .= $this->getCustomerName($ticketid);

				$list .= ' on '.$adb->query_result($result,$i,'createdtime').' &nbsp;';

				$list .= '</font></div>';
			}
		}
		$list .= '</div>';

		$log->debug("Exiting getCommentInformation method ...");
		return $list;
	}

	/**     Function to get the Customer Name who has made comment to the ticket from the vtiger_portal
	 *      @param  int    $id   - Ticket id
	 *      @return string $customername - The contact name
	**/
	function getCustomerName($id)
	{
		global $log;
		$log->debug("Entering getCustomerName(".$id.") method ...");
        	global $adb;
	        $sql = "select * from vtiger_portalinfo inner join vtiger_troubletickets on vtiger_troubletickets.parent_id = vtiger_portalinfo.id where vtiger_troubletickets.ticketid=".$id;
        	$result = $adb->query($sql);
	        $customername = $adb->query_result($result,0,'user_name');
		$log->debug("Exiting getCustomerName method ...");
        	return $customername;
	}
	
	function get_history($id)
	{
		global $log;
		$log->debug("Entering get_history(".$id.") method ...");
		$query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status, vtiger_activity.eventstatus,
		vtiger_activity.activitytype, vtiger_troubletickets.ticketid, vtiger_troubletickets.title, vtiger_crmentity.modifiedtime,
		vtiger_crmentity.createdtime, vtiger_crmentity.description, vtiger_users.user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid= vtiger_activity.activityid
				inner join vtiger_troubletickets on vtiger_troubletickets.ticketid = vtiger_seactivityrel.crmid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_activitygrouprelation on vtiger_activitygrouprelation.activityid=vtiger_activity.activityid
                                left join vtiger_groups on vtiger_groups.groupname=vtiger_activitygrouprelation.groupname
				inner join vtiger_users on vtiger_crmentity.smcreatorid= vtiger_users.id
				where (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id;
		//Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php
		$log->debug("Entering get_history method ...");
		return getHistory('HelpDesk',$query,$id);
	}



}
?>
