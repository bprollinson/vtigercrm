<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
global $calpath;
global $app_strings,$mod_strings;
global $theme;
global $log;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('include/database/PearDatabase.php');
require_once ($theme_path."layout_utils.php");
require_once('data/CRMEntity.php');
require_once("modules/Reports/Reports.php");

class ReportRun extends CRMEntity
{

	var $primarymodule;
	var $secondarymodule;
	var $orderbylistsql;
	var $orderbylistcolumns;

	var $selectcolumns;
	var $groupbylist;
	var $reporttype;
	var $reportname;
	var $totallist;

	/** Function to set reportid,primarymodule,secondarymodule,reporttype,reportname, for given reportid
	 *  This function accepts the $reportid as argument
	 *  It sets reportid,primarymodule,secondarymodule,reporttype,reportname for the given reportid
	 */
	function ReportRun($reportid)
	{
		$oReport = new Reports($reportid);
		$this->reportid = $reportid;
		$this->primarymodule = $oReport->primodule;
		$this->secondarymodule = $oReport->secmodule; 
		$this->reporttype = $oReport->reporttype;
		$this->reportname = $oReport->reportname;
	}

	/** Function to get the columns for the reportid
	 *  This function accepts the $reportid
	 *  This function returns  $columnslist Array($tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname As Header value,
	 *					      $tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 As Header value,
	 *					      					|
 	 *					      $tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen As Header value
	 *				      	     )
	 *
	 */
	function getQueryColumnsList($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$ssql = "select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid";
		$ssql .= " left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid";
		$ssql .= " where vtiger_report.reportid =".$reportid;
		$ssql .= " order by vtiger_selectcolumn.columnindex";

		$result = $adb->query($ssql);

		while($columnslistrow = $adb->fetch_array($result))
		{
			$fieldcolname = $columnslistrow["columnname"];
			$selectedfields = explode(":",$fieldcolname);

			$querycolumns = $this->getEscapedColumns($selectedfields);
			if($querycolumns == "")
			{
				$columnslist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1].' AS "'.$selectedfields[2].'"';
			}else
			{
				$columnslist[$fieldcolname] = $querycolumns;
			}
		}
		$log->info("ReportRun :: Successfully returned getQueryColumnsList".$reportid);
		return $columnslist;		
	}

	/** Function to get Escapedcolumns for the field in case of multiple parents 
	 *  @ param $selectedfields : Type Array 
	 *  returns the case query for the escaped columns	
	 */
	function getEscapedColumns($selectedfields)
	{
		$fieldname = $selectedfields[3];
		if($fieldname == "parent_id")
		{
			if($this->primarymodule == "HelpDesk" && $selectedfields[0] == "vtiger_crmentityRelHelpDesk")
			{
				$querycolumn = "case vtiger_crmentityRelHelpDesk.setype when 'Accounts' then vtiger_accountRelHelpDesk.accountname when 'Contacts' then vtiger_contactdetailsRelHelpDesk.lastname End"." '".$selectedfields[2]."', vtiger_crmentityRelHelpDesk.setype 'Entity_type'";
				return $querycolumn;
			}
			if($this->primarymodule == "Products" || $this->secondarymodule == "Products")
			{
				$querycolumn = "case vtiger_crmentityRelProducts.setype when 'Accounts' then vtiger_accountRelProducts.accountname when 'Leads' then vtiger_leaddetailsRelProducts.lastname when 'Potentials' then vtiger_potentialRelProducts.potentialname End"." '".$selectedfields[2]."', vtiger_crmentityRelProducts.setype 'Entity_type'";
			}
			if($this->primarymodule == "Activities" || $this->secondarymodule == "Activities")
			{
				$querycolumn = "case vtiger_crmentityRelActivities.setype when 'Accounts' then vtiger_accountRelActivities.accountname when 'Leads' then vtiger_leaddetailsRelActivities.lastname when 'Potentials' then vtiger_potentialRelActivities.potentialname when 'Quotes' then vtiger_quotesRelActivities.subject when 'PurchaseOrder' then vtiger_purchaseorderRelActivities.subject when 'Invoice' then vtiger_invoiceRelActivities.subject End"." '".$selectedfields[2]."', vtiger_crmentityRelActivities.setype 'Entity_type'";
			}
		}
		return $querycolumn;
	}

	/** Function to get selectedcolumns for the given reportid  
	 *  @ param $reportid : Type Integer 
	 *  returns the query of columnlist for the selected columns	
	 */
	function getSelectedColumnsList($reportid)
	{

		global $adb;
		global $modules;
		global $log;

		$ssql = "select vtiger_selectcolumn.* from vtiger_report inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid"; 
		$ssql .= " left join vtiger_selectcolumn on vtiger_selectcolumn.queryid = vtiger_selectquery.queryid where vtiger_report.reportid =".$reportid; 
		$ssql .= " order by vtiger_selectcolumn.columnindex";

		$result = $adb->query($ssql);
		$noofrows = $adb->num_rows($result);

		if ($this->orderbylistsql != "")
		{
			$sSQL .= $this->orderbylistsql.", ";	
		}

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");
			$ordercolumnsequal = true;
			if($fieldcolname != "")
			{
				for($j=0;$j<count($this->orderbylistcolumns);$j++)
				{
					if($this->orderbylistcolumns[$j] == $fieldcolname)
					{
						$ordercolumnsequal = false;
						break;
					}else
					{
						$ordercolumnsequal = true;
					}
				}
				if($ordercolumnsequal)
				{
					$selectedfields = explode(":",$fieldcolname);
					$sSQLList[] = $selectedfields[0].".".$selectedfields[1]." '".$selectedfields[2]."'";
				}
			}
		}
		$sSQL .= implode(",",$sSQLList);

		$log->info("ReportRun :: Successfully returned getSelectedColumnsList".$reportid);
		return $sSQL;
	}

	/** Function to get advanced comparator in query form for the given Comparator and value   
	 *  @ param $comparator : Type String  
	 *  @ param $value : Type String  
	 *  returns the check query for the comparator 	
	 */
	function getAdvComparator($comparator,$value)
	{

		global $log,$adb;

		if($comparator == "e")
		{
			if(trim($value) != "")
			{
				$rtvalue = " = ".$adb->quote($value);
			}else
			{
				$rtvalue = " is NULL";
			}
		}
		if($comparator == "n")
		{
			if(trim($value) != "")
			{
				$rtvalue = " <> ".$adb->quote($value);
			}else
			{
				$rtvalue = " is NOT NULL";
			}
		}
		if($comparator == "s")
		{
			$rtvalue = " like ".$adb->quote($value."%");
		}
		if($comparator == "c")
		{
			$rtvalue = " like ".$adb->quote("%".$value."%");
		}
		if($comparator == "k")
		{
			$rtvalue = " not like ".$adb->quote("%".$value."%");
		}
		if($comparator == "l")
		{
			$rtvalue = " < ".$adb->quote($value);
		}
		if($comparator == "g")
		{
			$rtvalue = " > ".$adb->quote($value);
		}
		if($comparator == "m")
		{
			$rtvalue = " <= ".$adb->quote($value);
		}
		if($comparator == "h")
		{
			$rtvalue = " >= ".$adb->quote($value);
		}

		$log->info("ReportRun :: Successfully returned getAdvComparator");
		return $rtvalue;
	}

	/** Function to get the advanced filter columns for the reportid
	 *  This function accepts the $reportid
	 *  This function returns  $columnslist Array($columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					      $tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 *					      					|
 	 *					      $tablenamen:$columnnamen:$fieldlabeln:$fieldnamen:$typeofdatan=>$tablenamen.$columnnamen filtercriteria 
	 *				      	     )
	 *
	 */


	function getAdvFilterList($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$advfiltersql =  "select vtiger_relcriteria.* from vtiger_report";
		$advfiltersql .= " inner join vtiger_selectquery on vtiger_selectquery.queryid = vtiger_report.queryid";
		$advfiltersql .= " left join vtiger_relcriteria on vtiger_relcriteria.queryid = vtiger_selectquery.queryid";
		$advfiltersql .= " where vtiger_report.reportid =".$reportid;
		$advfiltersql .= " order by vtiger_relcriteria.columnindex";

		$result = $adb->query($advfiltersql);
		while($advfilterrow = $adb->fetch_array($result))
		{
			$fieldcolname = $advfilterrow["columnname"];
			$comparator = $advfilterrow["comparator"];
			$value = $advfilterrow["value"];

			if($fieldcolname != "" && $comparator != "")
			{
				$selectedfields = explode(":",$fieldcolname);
				$valuearray = explode(",",trim($value));
				if(isset($valuearray) && count($valuearray) > 1)
				{
					$advorsql = "";
					for($n=0;$n<count($valuearray);$n++)
					{
						$advorsql[] = $selectedfields[0].".".$selectedfields[1].$this->getAdvComparator($comparator,trim($valuearray[$n]));
					}
					$advorsqls = implode(" or ",$advorsql);
					$fieldvalue = " (".$advorsqls.") ";
				}else
				{
					$fieldvalue = $selectedfields[0].".".$selectedfields[1].$this->getAdvComparator($comparator,trim($value));
				}
				$advfilterlist[$fieldcolname] = $fieldvalue;		
			}

		}
		$log->info("ReportRun :: Successfully returned getAdvFilterList".$reportid);
		return $advfilterlist;
	}	

	/** Function to get the Standard filter columns for the reportid
	 *  This function accepts the $reportid datatype Integer
	 *  This function returns  $stdfilterlist Array($columnname => $tablename:$columnname:$fieldlabel:$fieldname:$typeofdata=>$tablename.$columnname filtercriteria,
	 *					      $tablename1:$columnname1:$fieldlabel1:$fieldname1:$typeofdata1=>$tablename1.$columnname1 filtercriteria,
	 *				      	     )
	 *
	 */
	function getStdFilterList($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$stdfiltersql = "select vtiger_reportdatefilter.* from vtiger_report";
		$stdfiltersql .= " inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid";
		$stdfiltersql .= " where vtiger_report.reportid = ".$reportid;

		$result = $adb->query($stdfiltersql);
		$stdfilterrow = $adb->fetch_array($result);
		if(isset($stdfilterrow))
		{
			$fieldcolname = $stdfilterrow["datecolumnname"];
			$datefilter = $stdfilterrow["datefilter"];
			$startdate = $stdfilterrow["startdate"];
			$enddate = $stdfilterrow["enddate"];

			if($fieldcolname != "none")
			{
				if($datefilter == "custom")
				{
					if($startdate != "0000-00-00" && $enddate != "0000-00-00")
					{
						$selectedfields = explode(":",$fieldcolname);
						$stdfilterlist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				}else
				{
					$selectedfields = explode(":",$fieldcolname);
					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);
					if($startenddate[0] != "" && $startenddate[1] != "")
					{
						$stdfilterlist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." between '".$startenddate[0]." 00:00:00' and '".$startenddate[1]." 23:59:00'";
					}
				}

			}		
		}
		$log->info("ReportRun :: Successfully returned getStdFilterList".$reportid);
		return $stdfilterlist;
	}

	/** Function to get the RunTime filter columns for the given $filtercolumn,$filter,$startdate,$enddate 
	 *  @ param $filtercolumn : Type String
	 *  @ param $filter : Type String
	 *  @ param $startdate: Type String
	 *  @ param $enddate : Type String
	 *  This function returns  $stdfilterlist Array($columnname => $tablename:$columnname:$fieldlabel=>$tablename.$columnname 'between' $startdate 'and' $enddate)
	 *
	 */
	function RunTimeFilter($filtercolumn,$filter,$startdate,$enddate)
	{
		if($filtercolumn != "none")
		{
			if($filter == "custom")
			{
				if($startdate != "" && $enddate != "")
				{
					$selectedfields = explode(":",$filtercolumn);
					$stdfilterlist[$filtercolumn] = $selectedfields[0].".".$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
				}
			}else
			{
				if($startdate != "" && $enddate != "")
				{
					$selectedfields = explode(":",$filtercolumn);
					$startenddate = $this->getStandarFiltersStartAndEndDate($filter);
					if($startenddate[0] != "" && $startenddate[1] != "")
					{
						$stdfilterlist[$filtercolumn] = $selectedfields[0].".".$selectedfields[1]." between '".$startenddate[0]." 00:00:00' and '".$startenddate[1]." 23:59:00'";
					}
				}
			}

		}
		return $stdfilterlist;

	}

	/** Function to get standardfilter for the given reportid  
	 *  @ param $reportid : Type Integer 
	 *  returns the query of columnlist for the selected columns	
	 */

	function getStandardCriterialSql($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$sreportstdfiltersql = "select vtiger_reportdatefilter.* from vtiger_report"; 
		$sreportstdfiltersql .= " inner join vtiger_reportdatefilter on vtiger_report.reportid = vtiger_reportdatefilter.datefilterid"; 
		$sreportstdfiltersql .= " where vtiger_report.reportid =".$reportid;

		$result = $adb->query($sreportstdfiltersql);
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"datecolumnname");
			$datefilter = $adb->query_result($result,$i,"datefilter");
			$startdate = $adb->query_result($result,$i,"startdate");
			$enddate = $adb->query_result($result,$i,"enddate");

			if($fieldcolname != "none")
			{
				if($datefilter == "custom")
				{
					if($startdate != "0000-00-00" && $enddate != "0000-00-00")
					{
						$selectedfields = explode(":",$fieldcolname);
						$sSQL .= $selectedfields[0].".".$selectedfields[1]." between '".$startdate."' and '".$enddate."'";
					}
				}else
				{
					$selectedfields = explode(":",$fieldcolname);
					$startenddate = $this->getStandarFiltersStartAndEndDate($datefilter);
					if($startenddate[0] != "" && $startenddate[1] != "")
					{
						$sSQL .= $selectedfields[0].".".$selectedfields[1]." between '".$startenddate[0]."' and '".$startenddate[1]."'";
					}
				}
			}
		}
		$log->info("ReportRun :: Successfully returned getStandardCriterialSql".$reportid);
		return $sSQL;
	}

	/** Function to get standardfilter startdate and enddate for the given type   
	 *  @ param $type : Type String 
	 *  returns the $datevalue Array in the given format
	 * 		$datevalue = Array(0=>$startdate,1=>$enddate)	 
	 */


	function getStandarFiltersStartAndEndDate($type)
	{
		$today = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$tomorrow  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
		$yesterday  = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));

		$currentmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
		$currentmonth1 = date("Y-m-t");
		$lastmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")-1, "01",   date("Y")));
		$lastmonth1 = date("Y-m-t", strtotime("-1 Month"));
		$nextmonth0 = date("Y-m-d",mktime(0, 0, 0, date("m")+1, "01",   date("Y")));
		$nextmonth1 = date("Y-m-t", strtotime("+1 Month"));

		$lastweek0 = date("Y-m-d",strtotime("-2 week Sunday"));
		$lastweek1 = date("Y-m-d",strtotime("-1 week Saturday"));

		$thisweek0 = date("Y-m-d",strtotime("-1 week Sunday"));
		$thisweek1 = date("Y-m-d",strtotime("this Saturday"));

		$nextweek0 = date("Y-m-d",strtotime("this Sunday"));
		$nextweek1 = date("Y-m-d",strtotime("+1 week Saturday"));

		$next7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+6, date("Y")));
		$next30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+29, date("Y")));
		$next60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+59, date("Y")));
		$next90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+89, date("Y")));
		$next120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+119, date("Y")));

		$last7days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		$last30days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-29, date("Y")));
		$last60days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-59, date("Y")));
		$last90days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-89, date("Y")));
		$last120days = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-119, date("Y")));

		$currentFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")));
		$currentFY1 = date("Y-m-t",mktime(0, 0, 0, "12", date("d"),   date("Y")));
		$lastFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")-1));
		$lastFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")-1));
		$nextFY0 = date("Y-m-d",mktime(0, 0, 0, "01", "01",   date("Y")+1));
		$nextFY1 = date("Y-m-t", mktime(0, 0, 0, "12", date("d"), date("Y")+1));

		if($type == "today" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $today;
		}
		elseif($type == "yesterday" )
		{

			$datevalue[0] = $yesterday;
			$datevalue[1] = $yesterday;
		}
		elseif($type == "tomorrow" )
		{

			$datevalue[0] = $tomorrow;
			$datevalue[1] = $tomorrow;
		}        
		elseif($type == "thisweek" )
		{

			$datevalue[0] = $thisweek0;
			$datevalue[1] = $thisweek1;
		}                
		elseif($type == "lastweek" )
		{

			$datevalue[0] = $lastweek0;
			$datevalue[1] = $lastweek1;
		}                
		elseif($type == "nextweek" )
		{

			$datevalue[0] = $nextweek0;
			$datevalue[1] = $nextweek1;
		}                
		elseif($type == "thismonth" )
		{

			$datevalue[0] =$currentmonth0;
			$datevalue[1] = $currentmonth1;
		}                

		elseif($type == "lastmonth" )
		{

			$datevalue[0] = $lastmonth0;
			$datevalue[1] = $lastmonth1;
		}             
		elseif($type == "nextmonth" )
		{

			$datevalue[0] = $nextmonth0;
			$datevalue[1] = $nextmonth1;
		}           
		elseif($type == "next7days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next7days;
		}                
		elseif($type == "next30days" )
		{

			$datevalue[0] =$today;
			$datevalue[1] =$next30days;
		}                
		elseif($type == "next60days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next60days;
		}                
		elseif($type == "next90days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next90days;
		}        
		elseif($type == "next120days" )
		{

			$datevalue[0] = $today;
			$datevalue[1] = $next120days;
		}        
		elseif($type == "last7days" )
		{

			$datevalue[0] = $last7days;
			$datevalue[1] = $today;
		}                        
		elseif($type == "last30days" )
		{

			$datevalue[0] = $last30days;
			$datevalue[1] =  $today;
		}                
		elseif($type == "last60days" )
		{

			$datevalue[0] = $last60days;
			$datevalue[1] = $today;
		}        
		else if($type == "last90days" )
		{

			$datevalue[0] = $last90days;
			$datevalue[1] = $today;
		}        
		elseif($type == "last120days" )
		{

			$datevalue[0] = $last120days;
			$datevalue[1] = $today;
		}        
		elseif($type == "thisfy" )
		{

			$datevalue[0] = $currentFY0;
			$datevalue[1] = $currentFY1;
		}                
		elseif($type == "prevfy" )
		{

			$datevalue[0] = $lastFY0;
			$datevalue[1] = $lastFY1;
		}                
		elseif($type == "nextfy" )
		{

			$datevalue[0] = $nextFY0;
			$datevalue[1] = $nextFY1;
		}                
		elseif($type == "nextfq" )
		{

			$datevalue[0] = "2005-07-01";
			$datevalue[1] = "2005-09-30";
		}                        
		elseif($type == "prevfq" )
		{

			$datevalue[0] = "2005-01-01";
			$datevalue[1] = "2005-03-31";
		}                
		elseif($type == "thisfq" )
		{
			$datevalue[0] = "2005-04-01";
			$datevalue[1] = "2005-06-30";
		}                
		else
		{
			$datevalue[0] = "";
			$datevalue[1] = "";
		}

		return $datevalue;
	}

	/** Function to get getGroupingList for the given reportid  
	 *  @ param $reportid : Type Integer 
	 *  returns the $grouplist Array in the following format	
	 *  		$grouplist = Array($tablename:$columnname:$fieldlabel:fieldname:typeofdata=>$tablename:$columnname $sorder,
	 *				   $tablename1:$columnname1:$fieldlabel1:fieldname1:typeofdata1=>$tablename1:$columnname1 $sorder,
	 *				   $tablename2:$columnname2:$fieldlabel2:fieldname2:typeofdata2=>$tablename2:$columnname2 $sorder)
	 * This function also sets the return value in the class variable $this->groupbylist
	 */


	function getGroupingList($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$sreportsortsql = "select vtiger_reportsortcol.* from vtiger_report";
		$sreportsortsql .= " inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid";
		$sreportsortsql .= " where vtiger_report.reportid =".$reportid." order by vtiger_reportsortcol.sortcolid";

		$result = $adb->query($sreportsortsql);

		while($reportsortrow = $adb->fetch_array($result))
		{
			$fieldcolname = $reportsortrow["columnname"];
			$sortorder = $reportsortrow["sortorder"];

			if($sortorder == "Ascending")
			{
				$sortorder = "ASC";

			}elseif($sortorder == "Descending")
			{
				$sortorder = "DESC";
			}

			if($fieldcolname != "none")
			{
				$selectedfields = explode(":",$fieldcolname);
				$sqlvalue = $selectedfields[0].".".$selectedfields[1]." ".$sortorder;
				$grouplist[$fieldcolname] = $sqlvalue;
				$this->groupbylist[$fieldcolname] = $selectedfields[0].".".$selectedfields[1]." ".$selectedfields[2];
			}
		}
		$log->info("ReportRun :: Successfully returned getGroupingList".$reportid);
		return $grouplist;
	}

	/** function to get the selectedorderbylist for the given reportid  
	 *  @ param $reportid : type integer 
	 *  this returns the columns query for the sortorder columns
	 *  this function also sets the return value in the class variable $this->orderbylistsql
	 */


	function getSelectedOrderbyList($reportid)
	{

		global $adb;
		global $modules;
		global $log;

		$sreportsortsql = "select vtiger_reportsortcol.* from vtiger_report"; 
		$sreportsortsql .= " inner join vtiger_reportsortcol on vtiger_report.reportid = vtiger_reportsortcol.reportid"; 
		$sreportsortsql .= " where vtiger_report.reportid =".$reportid." order by vtiger_reportsortcol.sortcolid";

		$result = $adb->query($sreportsortsql);
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");
			$sortorder = $adb->query_result($result,$i,"sortorder");

			if($sortorder == "Ascending")
			{
				$sortorder = "ASC";
			}
			elseif($sortorder == "Descending")
			{
				$sortorder = "DESC";
			}

			if($fieldcolname != "none")
			{
				$this->orderbylistcolumns[] = $fieldcolname;
				$n = $n + 1;
				$selectedfields = explode(":",$fieldcolname);
				if($n > 1)
				{
					$sSQL .= ", ";
					$this->orderbylistsql .= ", ";
				}
				$sSQL .= $selectedfields[0].".".$selectedfields[1]." ".$sortorder;
				$this->orderbylistsql .= $selectedfields[0].".".$selectedfields[1]." ".$selectedfields[2];
			}
		}
		$log->info("ReportRun :: Successfully returned getSelectedOrderbyList".$reportid);
		return $sSQL;
	}

	/** function to get secondary Module for the given Primary module and secondary module   
	 *  @ param $module : type String 
	 *  @ param $secmodule : type String 
	 *  this returns join query for the given secondary module
	 */

	function getRelatedModulesQuery($module,$secmodule)
	{
		global $log;

		if($module == "Contacts")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
			if($secmodule == "Potentials")
			{
				$query = "left join  vtiger_potential on vtiger_potential.accountid = vtiger_contactdetails.accountid
					left join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid
					left join vtiger_account as vtiger_accountPotentials on vtiger_potential.accountid = vtiger_accountPotentials.accountid
					left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid
					left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid ";
			}
			if($secmodule == "Quotes")
			{
				$query = "left join vtiger_quotes on vtiger_quotes.contactid = vtiger_contactdetails.contactid
					left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid 
					left join vtiger_crmentity as vtiger_crmentityQuotes on vtiger_crmentityQuotes.crmid=vtiger_quotes.quoteid
					left join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid
					left join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid
					left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentityQuotes.smownerid
					left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager
					left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_quotes.potentialid
					left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid
					left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid ";
			}
			if($secmodule == "PurchaseOrder")
			{
				$query = "left join vtiger_purchaseorder on vtiger_purchaseorder.contactid = vtiger_contactdetails.contactid
					left join vtiger_purchaseordercf on vtiger_purchaseorder.purchaseorderid = vtiger_purchaseordercf.purchaseorderid  
					left join vtiger_crmentity as vtiger_crmentityPurchaseOrder on vtiger_crmentityPurchaseOrder.crmid=vtiger_purchaseorder.purchaseorderid
					left join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid
					left join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid
					left join vtiger_users as vtiger_usersPurchaseOrder on vtiger_usersPurchaseOrder.id = vtiger_crmentityPurchaseOrder.smownerid
					left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_purchaseorder.vendorid
					left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid ";
			}

		}

		if($module == "Accounts")
		{
			if($secmodule == "Potentials")
			{
				$query = "left join vtiger_potential on vtiger_potential.accountid = vtiger_account.accountid
					left join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid
					left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid
					left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid ";

			}
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.accountid = vtiger_account.accountid
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";
			}
			if($secmodule == "Quotes")
			{
				$query = "left join vtiger_quotes on vtiger_quotes.accountid = vtiger_account.accountid
					left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid 
					left join vtiger_crmentity as vtiger_crmentityQuotes on vtiger_crmentityQuotes.crmid=vtiger_quotes.quoteid
					left join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid
					left join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid
					left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentityQuotes.smownerid
					left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager
					left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_quotes.potentialid
					left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid
					left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid ";
			}
			if($secmodule == "PurchaseOrder")
			{
				$query = "left join vtiger_purchaseorder on vtiger_purchaseorder.accountid = vtiger_account.accountid
					left join vtiger_purchaseordercf on vtiger_purchaseorder.purchaseorderid = vtiger_purchaseordercf.purchaseorderid  
					left join vtiger_crmentity as vtiger_crmentityPurchaseOrder on vtiger_crmentityPurchaseOrder.crmid=vtiger_purchaseorder.purchaseorderid
					left join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid
					left join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid
					left join vtiger_users as vtiger_usersPurchaseOrder on vtiger_usersPurchaseOrder.id = vtiger_crmentityPurchaseOrder.smownerid
					left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_purchaseorder.vendorid
					left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid ";
			}
			if($secmodule == "Invoice")
			{
				$query = "left join vtiger_invoice on vtiger_invoice.accountid = vtiger_account.accountid
					left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid 
					left join vtiger_crmentity as vtiger_crmentityInvoice on vtiger_crmentityInvoice.crmid=vtiger_invoice.invoiceid
					left join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid
					left join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid
					left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentityInvoice.smownerid
					left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid ";
			}
			if($secmodule == "Products")
			{
				$query = "left join vtiger_seproductsrel on vtiger_seproductsrel.crmid = vtiger_account.accountid
					left join vtiger_products on vtiger_products.productid = vtiger_seproductsrel.productid
					left join vtiger_crmentity as vtiger_crmentityProducts on vtiger_crmentityProducts.crmid=vtiger_products.productid
					left join vtiger_productcf on vtiger_products.productid = vtiger_productcf.productid
					left join vtiger_users as vtiger_usersProducts on vtiger_usersProducts.id = vtiger_crmentityProducts.smownerid
					left join vtiger_contactdetails as vtiger_contactdetailsProducts on vtiger_contactdetailsProducts.contactid = vtiger_products.contactid
					left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_products.vendor_id
					left join vtiger_crmentity as vtiger_crmentityRel on vtiger_crmentityRel.crmid = vtiger_seproductsrel.crmid
					left join vtiger_account as vtiger_accountRel on vtiger_accountRel.accountid=crmentityRel.crmid
					left join vtiger_leaddetails as vtiger_leaddetailsRel on vtiger_leaddetailsRel.leadid = vtiger_crmentityRel.crmid
					left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_crmentityRel.crmid ";
			}
		}
		if($module == "Quotes")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_quotes.accountid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
			if($secmodule == "Potentials")
			{
				$query = "left join vtiger_potential on vtiger_potential.potentialid = vtiger_quotes.potentialid
					left join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid 
					left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid
					left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid ";

			}
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_quotes.contactid
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid

					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";
			}

		}
		if($module == "PurchaseOrder")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_purchaseorder.accountid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_purchaseorder.contactid
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid

					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";
			}
		}
		if($module == "Invoice")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid
					left join vtiger_contactdetails as vtiger_contactdetailsInvoice on vtiger_contactdetailsInvoice.contactid = vtiger_invoice.contactid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
		}
		if($module == "Products")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_crmentityRel.crmid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_products.contactid
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";

			}

		}
		if($module == "Potentials")
		{
			if($secmodule == "Accounts")
			{
				$query = "left join vtiger_account on vtiger_account.accountid = vtiger_potential.accountid
					left join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid
					left join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid
					left join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid
					left join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid
					left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
					left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid ";
			}
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.accountid = vtiger_potential.accountid
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";

			}
			if($secmodule == "Quotes")
			{
				$query = "left join vtiger_quotes on vtiger_quotes.potentialid = vtiger_potential.potentialid
					left join vtiger_crmentity as vtiger_crmentityQuotes on vtiger_crmentityQuotes.crmid=vtiger_quotes.quoteid
					left join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid
					left join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid
					left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentityQuotes.smownerid
					left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager
					left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_quotes.potentialid
					left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid
					left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid ";
			}
		}
		if($module == "HelpDesk")
		{
			if($secmodule == "Products")
			{
				$query = "left join vtiger_products on vtiger_products.productid = vtiger_troubletickets.product_id
					left join vtiger_crmentity as vtiger_crmentityProducts on vtiger_crmentityProducts.crmid=vtiger_products.productid
					left join vtiger_productcf on vtiger_products.productid = vtiger_productcf.productid
					left join vtiger_users as vtiger_usersProducts on vtiger_usersProducts.id = vtiger_crmentityProducts.smownerid
					left join vtiger_contactdetails as vtiger_contactdetailsProducts on vtiger_contactdetailsProducts.contactid = vtiger_products.contactid 
					left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_products.vendor_id
					left join vtiger_seproductsrel on vtiger_seproductsrel.productid = vtiger_products.productid
					left join vtiger_crmentity as vtiger_crmentityRelProducts on vtiger_crmentityRelProducts.crmid = vtiger_seproductsrel.crmid
					left join vtiger_account as vtiger_accountRelProducts on vtiger_accountRelProducts.accountid=vtiger_seproductsrel.crmid
					left join vtiger_leaddetails as vtiger_leaddetailsRelProducts on vtiger_leaddetailsRelProducts.leadid = vtiger_seproductsrel.crmid
					left join vtiger_potential as vtiger_potentialRelProducts on vtiger_potentialRelProducts.potentialid = vtiger_seproductsrel.crmid ";
			}
		}
		if($module == "Activities")
		{
			if($secmodule == "Contacts")
			{
				$query = "left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid 
					left join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid
					left join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					left join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
					left join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
					left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid
					left join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid ";
			}
		}
		$log->info("ReportRun :: Successfully returned getRelatedModulesQuery".$secmodule);
		return $query;
	}
	/** function to get report query for the given module    
	 *  @ param $module : type String 
	 *  this returns join query for the given module
	 */

	function getReportsQuery($module)
	{
		global $log;
		if($module == "Leads")
		{
			$query = "from vtiger_leaddetails 
				inner join vtiger_crmentity as vtiger_crmentityLeads on vtiger_crmentityLeads.crmid=vtiger_leaddetails.leadid 
				inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid 
				inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid 
				inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid 
				left join vtiger_users as vtiger_usersLeads on vtiger_usersLeads.id = vtiger_crmentityLeads.smownerid
				where vtiger_crmentityLeads.deleted=0 and vtiger_leaddetails.converted=0";
		}
		if($module == "Accounts")
		{
			$query = "from vtiger_account 
				inner join vtiger_crmentity as vtiger_crmentityAccounts on vtiger_crmentityAccounts.crmid=vtiger_account.accountid 
				inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid 
				inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid 
				inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid 
				left join vtiger_account as vtiger_accountAccounts on vtiger_accountAccounts.accountid = vtiger_account.parentid
				left join vtiger_users as vtiger_usersAccounts on vtiger_usersAccounts.id = vtiger_crmentityAccounts.smownerid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityAccounts.deleted=0 ";
		}

		if($module == "Contacts")
		{
			$query = "from vtiger_contactdetails
				inner join vtiger_crmentity as vtiger_crmentityContacts on vtiger_crmentityContacts.crmid = vtiger_contactdetails.contactid 
				inner join vtiger_contactaddress on vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid 
				inner join vtiger_customerdetails on vtiger_customerdetails.customerid = vtiger_contactdetails.contactid
				inner join vtiger_contactsubdetails on vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid 
				inner join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
				left join vtiger_contactdetails as vtiger_contactdetailsContacts on vtiger_contactdetailsContacts.contactid = vtiger_contactdetails.reportsto
				left join vtiger_account as vtiger_accountContacts on vtiger_accountContacts.accountid = vtiger_contactdetails.accountid 
				left join vtiger_users as vtiger_usersContacts on vtiger_usersContacts.id = vtiger_crmentityContacts.smownerid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)." 
				where vtiger_crmentityContacts.deleted=0";
		}

		if($module == "Potentials")
		{
			$query = "from vtiger_potential 
				inner join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid 
				inner join vtiger_account as vtiger_accountPotentials on vtiger_potential.accountid = vtiger_accountPotentials.accountid 
				inner join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid
				left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid  
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityPotentials.deleted=0 ";
		}

		if($module == "Products")
		{
			$query = "from vtiger_products 
				inner join vtiger_crmentity as vtiger_crmentityProducts on vtiger_crmentityProducts.crmid=vtiger_products.productid 
				left join vtiger_productcf on vtiger_products.productid = vtiger_productcf.productid 
				left join vtiger_users as vtiger_usersProducts on vtiger_usersProducts.id = vtiger_crmentityProducts.smownerid 
				left join vtiger_contactdetails as vtiger_contactdetailsProducts on vtiger_contactdetailsProducts.contactid = vtiger_products.contactid
				left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_products.vendor_id  
				left join vtiger_seproductsrel on vtiger_seproductsrel.productid = vtiger_products.productid 
				left join vtiger_crmentity as vtiger_crmentityRelProducts on vtiger_crmentityRelProducts.crmid = vtiger_seproductsrel.crmid 
				left join vtiger_account as vtiger_accountRelProducts on vtiger_accountRelProducts.accountid=vtiger_crmentityRelProducts.crmid 
				left join vtiger_leaddetails as vtiger_leaddetailsRelProducts on vtiger_leaddetailsRelProducts.leadid = vtiger_crmentityRelProducts.crmid 
				left join vtiger_potential as vtiger_potentialRelProducts on vtiger_potentialRelProducts.potentialid = vtiger_crmentityRelProducts.crmid 
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityProducts.deleted=0 ";
		}

		if($module == "HelpDesk")
		{
			$query = "from vtiger_troubletickets 
				inner join vtiger_crmentity as vtiger_crmentityHelpDesk 
				on vtiger_crmentityHelpDesk.crmid=vtiger_troubletickets.ticketid 
				inner join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid
				left join vtiger_crmentity as vtiger_crmentityRelHelpDesk on vtiger_crmentityRelHelpDesk.crmid = vtiger_troubletickets.parent_id
				left join vtiger_account as vtiger_accountRelHelpDesk on vtiger_accountRelHelpDesk.accountid=vtiger_crmentityRelHelpDesk.crmid 
				left join vtiger_contactdetails as vtiger_contactdetailsRelHelpDesk on vtiger_contactdetailsRelHelpDesk.contactid= vtiger_crmentityRelHelpDesk.crmid
				left join vtiger_products as vtiger_productsRel on vtiger_productsRel.productid = vtiger_troubletickets.product_id 
				left join vtiger_users as vtiger_usersHelpDesk on vtiger_crmentityHelpDesk.smownerid=vtiger_usersHelpDesk.id 
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityHelpDesk.deleted=0 ";
		}

		if($module == "Activities")
		{
			$query = "from vtiger_activity 
				inner join vtiger_crmentity as vtiger_crmentityActivities on vtiger_crmentityActivities.crmid=vtiger_activity.activityid 
				left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid= vtiger_activity.activityid 
				left join vtiger_contactdetails as vtiger_contactdetailsActivities on vtiger_contactdetailsActivities.contactid= vtiger_cntactivityrel.contactid
				left join vtiger_users as vtiger_usersActivities on vtiger_usersActivities.id = vtiger_crmentityActivities.smownerid
				left join vtiger_seactivityrel on vtiger_seactivityrel.activityid = vtiger_activity.activityid
				left join vtiger_crmentity as vtiger_crmentityRelActivities on vtiger_crmentityRelActivities.crmid = vtiger_seactivityrel.crmid
				left join vtiger_account as vtiger_accountRelActivities on vtiger_accountRelActivities.accountid=vtiger_crmentityRelActivities.crmid
				left join vtiger_leaddetails as vtiger_leaddetailsRelActivities on vtiger_leaddetailsRelActivities.leadid = vtiger_crmentityRelActivities.crmid
				left join vtiger_potential as vtiger_potentialRelActivities on vtiger_potentialRelActivities.potentialid = vtiger_crmentityRelActivities.crmid
				left join vtiger_quotes as vtiger_quotesRelActivities on vtiger_quotesRelActivities.quoteid = vtiger_crmentityRelActivities.crmid
				left join vtiger_purchaseorder as vtiger_purchaseorderRelActivities on vtiger_purchaseorderRelActivities.purchaseorderid = vtiger_crmentityRelActivities.crmid
				left join vtiger_invoice as vtiger_invoiceRelActivities on vtiger_invoiceRelActivities.invoiceid = vtiger_crmentityRelActivities.crmid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				WHERE vtiger_crmentityActivities.deleted=0 and (vtiger_activity.activitytype = 'Meeting' or vtiger_activity.activitytype='Call' or vtiger_activity.activitytype='Task')";
		}

		if($module == "Quotes")
		{
			$query = "from vtiger_quotes 
				inner join vtiger_crmentity as vtiger_crmentityQuotes on vtiger_crmentityQuotes.crmid=vtiger_quotes.quoteid 
				inner join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid 
				inner join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid  
				left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid 
				left join vtiger_users as vtiger_usersQuotes on vtiger_usersQuotes.id = vtiger_crmentityQuotes.smownerid
				left join vtiger_users as vtiger_usersRel1 on vtiger_usersRel1.id = vtiger_quotes.inventorymanager
				left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_quotes.potentialid
				left join vtiger_contactdetails as vtiger_contactdetailsQuotes on vtiger_contactdetailsQuotes.contactid = vtiger_quotes.contactid
				left join vtiger_account as vtiger_accountQuotes on vtiger_accountQuotes.accountid = vtiger_quotes.accountid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityQuotes.deleted=0";
		}

		if($module == "PurchaseOrder")
		{
			$query = "from vtiger_purchaseorder 
				inner join vtiger_crmentity as vtiger_crmentityPurchaseOrder on vtiger_crmentityPurchaseOrder.crmid=vtiger_purchaseorder.purchaseorderid 
				inner join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid 
				inner join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid 
				left join vtiger_purchaseordercf on vtiger_purchaseorder.purchaseorderid = vtiger_purchaseordercf.purchaseorderid  
				left join vtiger_users as vtiger_usersPurchaseOrder on vtiger_usersPurchaseOrder.id = vtiger_crmentityPurchaseOrder.smownerid 
				left join vtiger_vendor as vtiger_vendorRel on vtiger_vendorRel.vendorid = vtiger_purchaseorder.vendorid 
				left join vtiger_contactdetails as vtiger_contactdetailsPurchaseOrder on vtiger_contactdetailsPurchaseOrder.contactid = vtiger_purchaseorder.contactid 
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityPurchaseOrder.deleted=0";
		}

		if($module == "Invoice")
		{
			$query = "from vtiger_invoice 
				inner join vtiger_crmentity as vtiger_crmentityInvoice on vtiger_crmentityInvoice.crmid=vtiger_invoice.invoiceid 
				inner join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid 
				inner join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid 
				left join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid 
				left join vtiger_users as vtiger_usersInvoice on vtiger_usersInvoice.id = vtiger_crmentityInvoice.smownerid
				left join vtiger_account as vtiger_accountInvoice on vtiger_accountInvoice.accountid = vtiger_invoice.accountid
				".$this->getRelatedModulesQuery($module,$this->secondarymodule)."
				where vtiger_crmentityInvoice.deleted=0";
		}
		if($module == "SalesOrder")
		{
			$query = "from vtiger_salesorder 
				inner join vtiger_crmentity as vtiger_crmentitySalesOrder on vtiger_crmentitySalesOrder.crmid=vtiger_salesorder.salesorderid 
				inner join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid 
				inner join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid 
				left join vtiger_salesordercf on vtiger_salesorder.salesorderid = vtiger_salesordercf.salesorderid  
				left join vtiger_contactdetails as vtiger_contactdetailsSalesOrder on vtiger_contactdetailsSalesOrder.contactid = vtiger_salesorder.contactid 
				left join vtiger_quotes as vtiger_quotesSalesOrder on vtiger_quotesSalesOrder.quoteid = vtiger_salesorder.quoteid				
				left join vtiger_account as vtiger_accountSalesOrder on vtiger_accountSalesOrder.accountid = vtiger_salesorder.accountid
				left join vtiger_potential as vtiger_potentialRel on vtiger_potentialRel.potentialid = vtiger_salesorder.potentialid 
				left join vtiger_users as vtiger_usersSalesOrder on vtiger_usersSalesOrder.id = vtiger_crmentitySalesOrder.smownerid 
				where vtiger_crmentitySalesOrder.deleted=0";


		}	
		$log->info("ReportRun :: Successfully returned getReportsQuery".$module);
		return $query;
	}
 

	/** function to get query for the given reportid,filterlist,type    
	 *  @ param $reportid : Type integer
	 *  @ param $filterlist : Type Array
	 *  @ param $module : Type String 
	 *  this returns join query for the report 
	 */

	function sGetSQLforReport($reportid,$filterlist,$type='')
	{
		global $log;

		$columnlist = $this->getQueryColumnsList($reportid);
		$groupslist = $this->getGroupingList($reportid);
		$stdfilterlist = $this->getStdFilterList($reportid);
		$columnstotallist = $this->getColumnsTotal($reportid);
		$advfilterlist = $this->getAdvFilterList($reportid);
		$this->totallist = $columnstotallist;

		if($this->reporttype == "summary")
		{
			if(isset($this->groupbylist))
			{
				$newcolumnlist = array_diff($columnlist, $this->groupbylist);
				$selectlist = array_merge($this->groupbylist,$newcolumnlist);
			}else
			{
				$selectlist = $columnlist;
			}
		}else
		{
			$selectlist = $columnlist;
		}

		//columns list
		if(isset($selectlist))
		{
			$selectedcolumns =  implode(", ",$selectlist);
		}
		//groups list
		if(isset($groupslist))
		{
			$groupsquery = implode(", ",$groupslist);
		}

		//standard list
		if(isset($stdfilterlist))
		{
			$stdfiltersql = implode(", ",$stdfilterlist);
		}
		if(isset($filterlist))
		{
			$stdfiltersql = implode(", ",$filterlist);
		}
		//columns to total list
		if(isset($columnstotallist))
		{
			$columnstotalsql = implode(", ",$columnstotallist);
		}
		//advanced filterlist
		if(isset($advfilterlist))
		{
			$advfiltersql = implode(" and ",$advfilterlist);
		}
		if($stdfiltersql != "")
		{
			$wheresql = " and ".$stdfiltersql;
		}
		if($advfiltersql != "")
		{
			$wheresql .= " and ".$advfiltersql;
		}

		$reportquery = $this->getReportsQuery($this->primarymodule);

		if($type == 'COLUMNSTOTOTAL')
		{
			if(trim($groupsquery) != "")
			{
				if($columnstotalsql != '')
				{
					$reportquery = "select ".$columnstotalsql." ".$reportquery." ".$wheresql. " order by ".$groupsquery;
				}
			}else
			{
				if($columnstotalsql != '')
				{
					$reportquery = "select ".$columnstotalsql." ".$reportquery." ".$wheresql;
				}
			}
		}else
		{
			if(trim($groupsquery) != "")
			{
				$reportquery = "select ".$selectedcolumns." ".$reportquery." ".$wheresql. " order by ".$groupsquery;
			}else
			{
				$reportquery = "select ".$selectedcolumns." ".$reportquery." ".$wheresql;
			}
		}
		$log->info("ReportRun :: Successfully returned sGetSQLforReport".$reportid);
		return $reportquery;

	}

	/** function to get the report output in HTML,PDF,TOTAL,PRINT,PRINTTOTAL formats depends on the argument $outputformat    
	 *  @ param $outputformat : Type String (valid parameters HTML,PDF,TOTAL,PRINT,PRINT_TOTAL)
	 *  @ param $filterlist : Type Array
	 *  This returns HTML Report if $outputformat is HTML
         *  		Array for PDF if  $outputformat is PDF
	 *		HTML strings for TOTAL if $outputformat is TOTAL
	 *		Array for PRINT if $outputformat is PRINT
	 *		HTML strings for TOTAL fields  if $outputformat is PRINTTOTAL
	 *		HTML strings for 
	 */

	function GenerateReport($outputformat,$filterlist)
	{
		global $adb;
		global $modules;
		global $mod_strings;

		if($outputformat == "HTML")
		{
			$sSQL = $this->sGetSQLforReport($this->reportid,$filterlist);
			$result = $adb->query($sSQL);
			$y=$adb->num_fields($result);

			if($result)
			{
				for ($x=0; $x<$y; $x++)
				{
					$fld = $adb->field_name($result, $x);
					$header .= "<td class='rptCellLabel'>".str_replace($modules," ",$fld->name)."</td>";
				}

				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);

				do
				{
					$arraylists = Array();
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}

					if($newvalue == "") $newvalue = "-";

					if($snewvalue == "") $snewvalue = "-";

					if($tnewvalue == "") $tnewvalue = "-";

					$valtemplate .= "<tr>";

					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						$fieldvalue = $custom_field_values[$i];

						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";									
							}else
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}
						}else if(($secondvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue == $newvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";	
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue == $snewvalue)
							{
								$valtemplate .= "<td class='rptEmptyGrp'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td class='rptData'>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td class='rptGrpHead'>".$fieldvalue."</td>";
							}
						}
					}
					$valtemplate .= "</tr>";
					$lastvalue = $newvalue;
					$secondvalue = $snewvalue;
					$thirdvalue = $tnewvalue;
					$arr_val[] = $arraylists;
				}while($custom_field_values = $adb->fetch_array($result));

				$sHTML ='<table cellpadding="5" cellspacing="0" align="center" class="rptTable">
					<tr>'. 
					$header
					.'<!-- BEGIN values -->
					<tr>'. 
					$valtemplate
					.'</tr>
					</table>';
				//<<<<<<<<construct HTML>>>>>>>>>>>>
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				$return_data[] = $sSQL;
				return $return_data;
			}
		}elseif($outputformat == "PDF")
		{

			$sSQL = $this->sGetSQLforReport($this->reportid,$filterlist);
			$result = $adb->query($sSQL);
			$y=$adb->num_fields($result);

			if($result)
			{
				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);

				do
				{
					$arraylists = Array();
					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						$fieldvalue = $custom_field_values[$i];
						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						$arraylists[str_replace($modules," ",$fld->name)] = $fieldvalue;
					}
					$arr_val[] = $arraylists;
				}while($custom_field_values = $adb->fetch_array($result));

				return $arr_val;
			}
		}elseif($outputformat == "TOTALHTML")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid,$filterlist,"COLUMNSTOTOTAL");
			if(isset($this->totallist))
			{
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);

					$coltotalhtml .= "<table align='center' width='60%' cellpadding='3' cellspacing='0' border='0' class='rptTable'><tr><td class='rptCellLabel'>Totals</td><td class='rptCellLabel'>SUM</td><td class='rptCellLabel'>AVG</td><td class='rptCellLabel'>MIN</td><td class='rptCellLabel'>MAX</td></tr>";

					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$totclmnflds[str_replace($escapedchars," ",$fieldlist[3])] = str_replace($escapedchars," ",$fieldlist[3]);
					}

					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];
					}
					foreach($totclmnflds as $key=>$value)
					{

						$coltotalhtml .= '<tr class="rptGrpHead" valign=top><td class="rptData">'.str_replace($modules," ",$value).'</td>';
						$arraykey = trim($value).'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td class="rptTotal">'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = trim($value).'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td class="rptTotal">'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = trim($value).'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td class="rptTotal">'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$arraykey = trim($value).'_MAX';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td class="rptTotal">'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td class="rptTotal">&nbsp;</td>';
						}

						$coltotalhtml .= '<tr>';
					}

					$coltotalhtml .= "</table>";
				}
			}			
			return $coltotalhtml;
		}elseif($outputformat == "PRINT")
		{
			$sSQL = $this->sGetSQLforReport($this->reportid,$filterlist);
			$result = $adb->query($sSQL);
			$y=$adb->num_fields($result);

			if($result)
			{
				for ($x=0; $x<$y; $x++)
				{
					$fld = $adb->field_name($result, $x);
					$header .= "<th>".str_replace($modules," ",$fld->name)."</th>";
				}
				
				$noofrows = $adb->num_rows($result);
				$custom_field_values = $adb->fetch_array($result);
				$groupslist = $this->getGroupingList($this->reportid);

				do
				{
					$arraylists = Array();
					if(count($groupslist) == 1)
					{
						$newvalue = $custom_field_values[0];
					}elseif(count($groupslist) == 2)
					{
						$newvalue = $custom_field_values[0];
						$snewvalue = $custom_field_values[1];
					}elseif(count($groupslist) == 3)
					{
						$newvalue = $custom_field_values[0];
                                                $snewvalue = $custom_field_values[1];
						$tnewvalue = $custom_field_values[2];
					}
					
					if($newvalue == "") $newvalue = "-";

					if($snewvalue == "") $snewvalue = "-";

					if($tnewvalue == "") $tnewvalue = "-";
 
					$valtemplate .= "<tr>";
					
					for ($i=0; $i<$y; $i++)
					{
						$fld = $adb->field_name($result, $i);
						$fieldvalue = $custom_field_values[$i];

						if($fieldvalue == "" )
						{
							$fieldvalue = "-";
						}
						if(($lastvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($this->reporttype == "summary")
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";									
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}else if(($secondvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($lastvalue == $newvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";	
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else if(($thirdvalue == $fieldvalue) && $this->reporttype == "summary")
						{
							if($secondvalue == $snewvalue)
							{
								$valtemplate .= "<td style='border-top:1px dotted #FFFFFF;'>&nbsp;</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
						else
						{
							if($this->reporttype == "tabular")
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}else
							{
								$valtemplate .= "<td>".$fieldvalue."</td>";
							}
						}
					  }
					 $valtemplate .= "</tr>";
					 $lastvalue = $newvalue;
					 $secondvalue = $snewvalue;
					 $thirdvalue = $tnewvalue;
				$arr_val[] = $arraylists;
				}while($custom_field_values = $adb->fetch_array($result));
				
				$sHTML = '<tr>'.$header.'</tr>'.$valtemplate;	
				$return_data[] = $sHTML;
				$return_data[] = $noofrows;
				return $return_data;
			}
		}elseif($outputformat == "PRINT_TOTAL")
		{
			$escapedchars = Array('_SUM','_AVG','_MIN','_MAX');
			$sSQL = $this->sGetSQLforReport($this->reportid,$filterlist,"COLUMNSTOTOTAL");
			if(isset($this->totallist))
			{
				if($sSQL != "")
				{
					$result = $adb->query($sSQL);
					$y=$adb->num_fields($result);
					$custom_field_values = $adb->fetch_array($result);

					$coltotalhtml .= '<table width="100%" border="0" cellpadding="5" cellspacing="0" align="center" class="printReport" ><tr><th>Totals</th><th>SUM</th><th>AVG</th><th>MIN</th><th>MAX</th></tr>';

					foreach($this->totallist as $key=>$value)
					{
						$fieldlist = explode(":",$key);
						$totclmnflds[str_replace($escapedchars," ",$fieldlist[3])] = str_replace($escapedchars," ",$fieldlist[3]);
					}

					for($i =0;$i<$y;$i++)
					{
						$fld = $adb->field_name($result, $i);
						$keyhdr[$fld->name] = $custom_field_values[$i];
					}
					foreach($totclmnflds as $key=>$value)
					{

						$coltotalhtml .= '<tr valign=top><td>'.str_replace($modules," ",$value).'</td>';
						$arraykey = trim($value).'_SUM';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td>'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_AVG';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td>'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_MIN';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td>'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$arraykey = trim($value).'_MAX';
						if(isset($keyhdr[$arraykey]))
						{
							$coltotalhtml .= '<td>'.$keyhdr[$arraykey].'</td>';
						}else
						{
							$coltotalhtml .= '<td>&nbsp;</td>';
						}

						$coltotalhtml .= '<tr>';
					}

					$coltotalhtml .= "</table>";
				}
			}			
			return $coltotalhtml;
		}
	}

	//<<<<<<<new>>>>>>>>>>
	function getColumnsTotal($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$coltotalsql = "select vtiger_reportsummary.* from vtiger_report";
		$coltotalsql .= " inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid";
		$coltotalsql .= " where vtiger_report.reportid =".$reportid;

		$result = $adb->query($coltotalsql);

		while($coltotalrow = $adb->fetch_array($result))
		{
			$fieldcolname = $coltotalrow["columnname"];

			if($fieldcolname != "none")
			{
				$fieldlist = explode(":",$fieldcolname);
				if($fieldlist[4] == 2)
				{
					$stdfilterlist[$fieldcolname] = "sum(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 3)
				{
					$stdfilterlist[$fieldcolname] = "avg(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 4)
				{
					$stdfilterlist[$fieldcolname] = "min(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 5)
				{
					$stdfilterlist[$fieldcolname] = "max(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
			}
		}
		$log->info("ReportRun :: Successfully returned getColumnsTotal".$reportid);
		return $stdfilterlist;
	}
	//<<<<<<new>>>>>>>>>


	/** function to get query for the columns to total for the given reportid    
	 *  @ param $reportid : Type integer
	 *  This returns columnstoTotal query for the reportid 
	 */

	function getColumnsToTotalColumns($reportid)
	{
		global $adb;
		global $modules;
		global $log;

		$sreportstdfiltersql = "select vtiger_reportsummary.* from vtiger_report"; 
		$sreportstdfiltersql .= " inner join vtiger_reportsummary on vtiger_report.reportid = vtiger_reportsummary.reportsummaryid"; 
		$sreportstdfiltersql .= " where vtiger_report.reportid =".$reportid;

		$result = $adb->query($sreportstdfiltersql);
		$noofrows = $adb->num_rows($result);

		for($i=0; $i<$noofrows; $i++)
		{
			$fieldcolname = $adb->query_result($result,$i,"columnname");

			if($fieldcolname != "none")
			{
				$fieldlist = explode(":",$fieldcolname);
				if($fieldlist[4] == 2)
				{
					$sSQLList[] = "sum(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 3)
				{
					$sSQLList[] = "avg(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 4)
				{
					$sSQLList[] = "min(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
				if($fieldlist[4] == 5)
				{
					$sSQLList[] = "max(".$fieldlist[1].".".$fieldlist[2].") ".$fieldlist[3];
				}
			}
		}
		if(isset($sSQLList))
		{
			$sSQL = implode(",",$sSQLList);
		}
		$log->info("ReportRun :: Successfully returned getColumnsToTotalColumns".$reportid);
		return $sSQL;
	}

}
?>
