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

require_once('modules/Leads/Lead.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');

$local_log =& LoggerManager::getLogger('index');
global $log,$adb;
$focus = new Lead();
global $current_user;
$currencyid=fetchCurrency($current_user->id);
$rate_symbol = getCurrencySymbolandCRate($currencyid);
$rate = $rate_symbol['rate'];
$curr_symbol=$rate_symbol['symbol'];

if(isset($_REQUEST['record']))
{
	$focus->id = $_REQUEST['record'];
}
if(isset($_REQUEST['mode']))
{
	$focus->mode = $_REQUEST['mode'];
}

//$focus->retrieve($_REQUEST['record']);

foreach($focus->column_fields as $fieldname => $val)
{
    	if(isset($_REQUEST[$fieldname]))
	{
          $value = $_REQUEST[$fieldname];
            $log->info("the value is ".$value);
          $focus->column_fields[$fieldname] = $value;
        }
	if(isset($_REQUEST['annualrevenue']))
        {
                        $value = convertToDollar($_REQUEST['annualrevenue'],$rate);
                        $focus->column_fields['annualrevenue'] = $value;
        }
        
}

$focus->save("Leads");

$return_id = $focus->id;
	 $log->info("the return id is ".$return_id);
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] != "") $return_module = $_REQUEST['return_module'];
else $return_module = "Leads";
if(isset($_REQUEST['return_action']) && $_REQUEST['return_action'] != "") $return_action = $_REQUEST['return_action'];
else $return_action = "DetailView";
if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "") $return_id = $_REQUEST['return_id'];

$local_log->debug("Saved record with id of ".$return_id);
//code added for returning back to the current view after edit from list view
if($_REQUEST['return_viewname'] == '') $return_viewname='0';
if($_REQUEST['return_viewname'] != '')$return_viewname=$_REQUEST['return_viewname'];

if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] == "Campaigns")
{
	if(isset($_REQUEST['return_id']) && $_REQUEST['return_id'] != "")
	{
		$sql = "insert into vtiger_campaignleadrel values (".$_REQUEST['return_id'].",".$focus->id.")";
		$adb->query($sql);
	}
}


header("Location: index.php?action=$return_action&module=$return_module&record=$return_id&viewname=$return_viewname");

/** Function to save the Lead custom fields info into database
 *  @param integer $entity_id - leadid
*/
function save_customfields($entity_id)
{
	$log->debug("Entering save_customfields(".$entity_id.") method ...");
	 $log->debug("save custom vtiger_field invoked ".$entity_id);
	global $adb;
	$dbquery="select * from customfields where module='Leads'";
	$result = $adb->query($dbquery);
	$custquery = "select * from leadcf where leadid='".$entity_id."'";
        $cust_result = $adb->query($custquery);
	if($adb->num_rows($result) != 0)
	{
		
		$columns='';
		$values='';
		$update='';
		$noofrows = $adb->num_rows($result);
		for($i=0; $i<$noofrows; $i++)
		{
			$fldName=$adb->query_result($result,$i,"fieldlabel");
			$colName=$adb->query_result($result,$i,"column_name");
			if(isset($_REQUEST[$colName]))
			{
				$fldvalue=$_REQUEST[$colName];
				 $log->info("the columnName is ".$fldvalue);
				if(get_magic_quotes_gpc() == 1)
                		{
                        		$fldvalue = stripslashes($fldvalue);
                		}
			}
			else
			{
				$fldvalue = '';
			}
			if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
			{
				//Update Block
				if($i == 0)
				{
					$update = $colName.'="'.$fldvalue.'"';
				}
				else
				{
					$update .= ', '.$colName.'="'.$fldvalue.'"';
				}
			}
			else
			{
				//Insert Block
				if($i == 0)
				{
					$columns='leadid, '.$colName;
					$values='"'.$entity_id.'", "'.$fldvalue.'"';
				}
				else
				{
					$columns .= ', '.$colName;
					$values .= ', "'.$fldvalue.'"';
				}
			}
			
				
		}
		if(isset($_REQUEST['record']) && $_REQUEST['record'] != '' && $adb->num_rows($cust_result) !=0)
		{
			//Update Block
			$query = 'update leadcf SET '.$update.' where leadid="'.$entity_id.'"'; 
			$adb->query($query);
		}
		else
		{
			//Insert Block
			$query = 'insert into leadcf ('.$columns.') values('.$values.')';
			$adb->query($query);
		}
		
	}
	$log->debug("Exiting save_customfields method ...");	
}
?>
