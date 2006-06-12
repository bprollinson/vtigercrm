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
function getTopAccounts()
{
	$log = LoggerManager::getLogger('top vtiger_accounts_list');
	$log->debug("Entering getTopAccounts() method ...");
	require_once("data/Tracker.php");
	require_once('modules/Potentials/Opportunity.php');
	require_once('include/logging.php');
	require_once('include/ListView/ListView.php');
	global $app_strings;
	global $adb;
	global $current_language;
	global $current_user;
	$current_module_strings = return_module_language($current_language, "Accounts");

	$list_query = "select vtiger_account.accountid, vtiger_account.accountname, vtiger_account.tickersymbol, sum(vtiger_potential.amount) as amount from vtiger_potential inner join vtiger_crmentity on (vtiger_potential.potentialid=vtiger_crmentity.crmid) inner join vtiger_account on (vtiger_potential.accountid=vtiger_account.accountid) where vtiger_crmentity.deleted=0 AND vtiger_crmentity.smownerid='".$current_user->id."' and vtiger_potential.sales_stage <> '".$app_strings['LBL_CLOSE_WON']."' and vtiger_potential.sales_stage <> '".$app_strings['LBL_CLOSE_LOST']."' group by vtiger_account.accountid, vtiger_account.accountname, vtiger_account.tickersymbol order by amount desc;";
	$list_result=$adb->query($list_query);
	$open_accounts_list = array();
	$noofrows = min($adb->num_rows($list_result),7);
	if (count($list_result)>0)
		for($i=0;$i<$noofrows;$i++) 
		{
			$open_accounts_list[] = Array('accountid' => $adb->query_result($list_result,$i,'accountid'),
					'accountname' => $adb->query_result($list_result,$i,'accountname'),
					'amount' => $adb->query_result($list_result,$i,'amount'),
					'tickersymbol' => $adb->query_result($list_result,$i,'tickersymbol'),
					);								 
		}

	$title=array();
	$title[]='myTopAccounts.gif';
	$title[]=$current_module_strings['LBL_TOP_ACCOUNTS'];
	$title[]='home_myaccount';
	
	$header=array();
	$header[]=$current_module_strings['LBL_LIST_ACCOUNT_NAME'];
	$currencyid=fetchCurrency($current_user->id);
	$rate_symbol = getCurrencySymbolandCRate($currencyid);
	$rate = $rate_symbol['rate'];
	$curr_symbol = $rate_symbol['symbol'];
        $header[]=$current_module_strings['LBL_LIST_AMOUNT'].'('.$curr_symbol.')';
	
	$entries=array();
	foreach($open_accounts_list as $account)
	{
		$value=array();
		$account_fields = array(
				'ACCOUNT_ID' => $account['accountid'],
				'ACCOUNT_NAME' => $account['accountname'],
				'AMOUNT' => ($account['amount']),
				);

		$value[]='<a href="index.php?action=DetailView&module=Accounts&record='.$account['accountid'].'" onMouseOver=getHeadLines("'.$account['tickersymbol'].'")>'.$account['accountname'].'</a>';
		$value[]=convertFromDollar($account['amount'],$rate);
		$entries[$account['accountid']]=$value;	
	}
	$values=Array('Title'=>$title,'Header'=>$header,'Entries'=>$entries);
	$log->debug("Exiting getTopAccounts method ...");
	if (($display_empty_home_blocks && count($entries) == 0 ) || (count($entries)>0))
		return $values;
}
?>
