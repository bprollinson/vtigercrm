<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('include/language/en_us.lang.php');
require_once('include/database/PearDatabase.php');
require_once('include/ComboStrings.php');
/**
 *  Class which handles the population of the combo values
 * 
 *
 */
class PopulateComboValues
{
	var $app_list_strings;


	/** 
	 * To populate the default combo values for the combo vtiger_tables
	 */
	function insertComboValues($values, $tableName)
	{
		global $log;
		$log->debug("Entering insertComboValues(".$values.", ".$tableName.") method ...");
		global $adb;
		$i=0;
		foreach ($values as $val => $cal)
		{
			$id = $adb->getUniqueID($tableName);
			if($val != '')
			{
				$adb->query("insert into vtiger_".$tableName. " values(".$id.",'".$val."',".$i.",1)");
			}
			else
			{
				$adb->query("insert into vtiger_".$tableName. " values(".$id.",'--None--',".$i.",1)");
			}
			$i++;
		}
		$log->debug("Exiting insertComboValues method ...");
	}


	/** 
	 * To populate the combo vtiger_tables at startup time
	 */

	function create_tables () 
	{
		global $log;
		$log->debug("Entering create_tables () method ...");
				
		global $app_list_strings,$adb;
		global $combo_strings;
		$comboTables = Array('leadsource','accounttype','industry','leadstatus','rating','licencekeystatus','opportunity_type','salutationtype','sales_stage','ticketstatus','ticketpriorities','ticketseverities','ticketcategories','duration_minutes','eventstatus','taskstatus','taskpriority','manufacturer','productcategory','activitytype','currency','faqcategories','usageunit','glacct','quotestage','carrier','taxclass','recurringtype','faqstatus','invoicestatus','postatus','sostatus','visibility','campaigntype','campaignstatus','expectedresponse','status','activity_view','lead_view','date_format');

		foreach ($comboTables as $comTab)
		{
			$this->insertComboValues($combo_strings[$comTab."_dom"],$comTab);
		}
		$log->debug("Exiting create_tables () method ...");
	}
}
?>
