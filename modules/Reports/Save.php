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
require_once('modules/Reports/Reports.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
global $adb;
global $log;
$reportid = $_REQUEST["record"];

//<<<<<<<selectcolumn>>>>>>>>>
$selectedcolumnstring = $_REQUEST["selectedColumnsString"];
//<<<<<<<selectcolumn>>>>>>>>>

//<<<<<<<reportsortcol>>>>>>>>>
$sort_by1 = $_REQUEST["Group1"];
$sort_order1 = $_REQUEST["Sort1"];
$sort_by2 = $_REQUEST["Group2"];
$sort_order2 = $_REQUEST["Sort2"];
$sort_by3 = $_REQUEST["Group3"];
$sort_order3 = $_REQUEST["Sort3"];
//<<<<<<<reportsortcol>>>>>>>>>

//<<<<<<<reportmodules>>>>>>>>>
$pmodule = $_REQUEST["primarymodule"];
$smodule = $_REQUEST["secondarymodule"];
//<<<<<<<reportmodules>>>>>>>>>

//<<<<<<<report>>>>>>>>>
$reportname = $_REQUEST["reportName"];
$reportdescription = $_REQUEST["reportDesc"];
$reporttype = $_REQUEST["reportType"];
$folderid = $_REQUEST["folder"];
//<<<<<<<report>>>>>>>>>

//<<<<<<<standarfilters>>>>>>>>>
$stdDateFilterField = $_REQUEST["stdDateFilterField"];
$stdDateFilter = $_REQUEST["stdDateFilter"];
$startdate = getDBInsertDateValue($_REQUEST["startdate"]);
$enddate = getDBInsertDateValue($_REQUEST["enddate"]);
//<<<<<<<standardfilters>>>>>>>>>

//<<<<<<<columnstototal>>>>>>>>>>
$allKeys = array_keys($HTTP_POST_VARS);
for ($i=0;$i<count($allKeys);$i++)
{
   $string = substr($allKeys[$i], 0, 3);
   if($string == "cb:")
   {
	   $columnstototal[] = $allKeys[$i];
   }
}
//<<<<<<<columnstototal>>>>>>>>>

//<<<<<<<advancedfilter>>>>>>>>
//$adv_filter_col = "kcol";
//$allKeys = array_keys($HTTP_POST_VARS);
$allKeys = array_keys($_REQUEST);
for ($i=0;$i<count($allKeys);$i++)
{
   $string = substr($allKeys[$i], 0, 4);
   if($string == "fcol")
   {
	   $adv_filter_col[] = $_REQUEST[$allKeys[$i]];
   }
}
for ($i=0;$i<count($allKeys);$i++)
{
   $string = substr($allKeys[$i], 0, 3);
   if($string == "fop")
   {
           $adv_filter_option[] = $_REQUEST[$allKeys[$i]];
   }
}
for ($i=0;$i<count($allKeys);$i++)
{
   $string = substr($allKeys[$i], 0, 4);
   if($string == "fval")
   {
           $adv_filter_value[] = $_REQUEST[$allKeys[$i]];
   }
}
//<<<<<<<advancedfilter>>>>>>>>
if($reportid == "")
{
	$genQueryId = $adb->getUniqueID("vtiger_selectquery");
	if($genQueryId != "")
	{
		$iquerysql = "insert into vtiger_selectquery (QUERYID,STARTINDEX,NUMOFOBJECTS) values (?,?,?)";
		$iquerysqlresult = $adb->pquery($iquerysql, array($genQueryId,0,0));
		$log->info("Reports :: Save->Successfully saved vtiger_selectquery");
		if($iquerysqlresult!=false)
		{
			//<<<<step2 vtiger_selectcolumn>>>>>>>>
			if($selectedcolumnstring != "")
			{
				$selectedcolumns = explode(";",$selectedcolumnstring);
				for($i=0 ;$i< count($selectedcolumns) -1 ;$i++)
				{
					$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
					$icolumnsqlresult = $adb->pquery($icolumnsql, array($genQueryId,$i,$selectedcolumns[$i]));
				}
			}
			$log->info("Reports :: Save->Successfully saved vtiger_selectcolumn");
			//<<<<step2 vtiger_selectcolumn>>>>>>>>

			if($genQueryId != "")
			{
				$ireportsql = "insert into vtiger_report (REPORTID,FOLDERID,REPORTNAME,DESCRIPTION,REPORTTYPE,QUERYID,STATE) values (?,?,?,?,?,?,?)";
				$ireportparams = array($genQueryId, $folderid, $reportname, $reportdescription, $reporttype, $genQueryId,'CUSTOM');
				$ireportresult = $adb->pquery($ireportsql, $ireportparams);
				$log->info("Reports :: Save->Successfully saved vtiger_report");
				if($ireportresult!=false)
				{
					//<<<<reportmodules>>>>>>>
					$ireportmodulesql = "insert into vtiger_reportmodules (REPORTMODULESID,PRIMARYMODULE,SECONDARYMODULES) values (?,?,?)";
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $pmodule, $smodule));
					$log->info("Reports :: Save->Successfully saved vtiger_reportmodules");
					//<<<<reportmodules>>>>>>>

					//<<<<step3 vtiger_reportsortcol>>>>>>>
					if($sort_by1 != "")
					{
						$sort_by1sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by1result = $adb->pquery($sort_by1sql, array(1, $genQueryId, $sort_by1, $sort_order1));
					}
					if($sort_by2 != "")
					{
						$sort_by2sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by2result = $adb->pquery($sort_by2sql, array(2,$genQueryId,$sort_by2,$sort_order2));
					}
					if($sort_by3 != "")
					{
						$sort_by3sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
						$sort_by3result = $adb->pquery($sort_by3sql, array(3,$genQueryId,$sort_by3,$sort_order3));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
					//<<<<step3 vtiger_reportsortcol>>>>>>>

					//<<<<step5 standarfilder>>>>>>>
					$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
					$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($genQueryId, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
					$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
					//<<<<step5 standarfilder>>>>>>>

					//<<<<step4 columnstototal>>>>>>>
					for ($i=0;$i<count($columnstototal);$i++)
					{
						$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
						$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($genQueryId, $i, $columnstototal[$i]));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
					//<<<<step4 columnstototal>>>>>>>

					//<<<<step5 advancedfilter>>>>>>>
					for ($i=0;$i<count($adv_filter_col);$i++)
					{
						$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE) values (?,?,?,?,?)";
						$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($genQueryId, $i, $adv_filter_col[$i], $adv_filter_option[$i], $adv_filter_value[$i]));
					}
					$log->info("Reports :: Save->Successfully saved vtiger_relcriteria");
					//<<<<step5 advancedfilter>>>>>>>

				}else
				{
					$errormessage = "<font color='red'><B>Error Message<ul>
						<li><font color='red'>Error while inserting the record</font>
						</ul></B></font> <br>" ;
					echo $errormessage;
					die;
				}
			}
		}else
		{
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}
		echo '<script>window.opener.location.href =window.opener.location.href;self.close();</script>';
	}
}else
{
	if($reportid != "")
	{
		if($selectedcolumnstring != "")
		{
			$idelcolumnsql = "delete from vtiger_selectcolumn where queryid=?";
			$idelcolumnsqlresult = $adb->pquery($idelcolumnsql, array($reportid));
			if($idelcolumnsqlresult != false)
			{
				$selectedcolumns = explode(";",$selectedcolumnstring);
				for($i=0 ;$i< count($selectedcolumns) -1 ;$i++)
				{
					$icolumnsql = "insert into vtiger_selectcolumn (QUERYID,COLUMNINDEX,COLUMNNAME) values (?,?,?)";
					$icolumnsqlresult = $adb->pquery($icolumnsql, array($reportid, $i, $selectedcolumns[$i]));
				}
			}
		}

		$ireportsql = "update vtiger_report set REPORTNAME=?, DESCRIPTION=?, REPORTTYPE=? where REPORTID=?";
		$ireportparams = array($reportname, $reportdescription, $reporttype, $reportid);
		$ireportresult = $adb->pquery($ireportsql, $ireportparams);
		$log->info("Reports :: Save->Successfully saved vtiger_report");

		$idelreportsortcolsql = "delete from vtiger_reportsortcol where reportid=?";
		$idelreportsortcolsqlresult = $adb->pquery($idelreportsortcolsql, array($reportid));
		$log->info("Reports :: Save->Successfully deleted vtiger_reportsortcol");

		if($idelreportsortcolsqlresult!=false)
		{
			//<<<<step3 vtiger_reportsortcol>>>>>>>
			if($sort_by1 != "")
			{
				$sort_by1sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by1result = $adb->pquery($sort_by1sql, array(1, $reportid, $sort_by1, $sort_order1));
			}
			if($sort_by2 != "")
			{
				$sort_by2sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by2result = $adb->pquery($sort_by2sql, array(2, $reportid, $sort_by2, $sort_order2));
			}
			if($sort_by3 != "")
			{
				$sort_by3sql = "insert into vtiger_reportsortcol (SORTCOLID,REPORTID,COLUMNNAME,SORTORDER) values (?,?,?,?)";
				$sort_by3result = $adb->pquery($sort_by3sql, array(3, $reportid, $sort_by3, $sort_order3));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsortcol");
			//<<<<step3 vtiger_reportsortcol>>>>>>>

			$idelreportdatefiltersql = "delete from vtiger_reportdatefilter where datefilterid=?";
			$idelreportdatefiltersqlresult = $adb->pquery($idelreportdatefiltersql, array($reportid));

			//<<<<step5 standarfilder>>>>>>>
			$ireportmodulesql = "insert into vtiger_reportdatefilter (DATEFILTERID,DATECOLUMNNAME,DATEFILTER,STARTDATE,ENDDATE) values (?,?,?,?,?)";
			$ireportmoduleresult = $adb->pquery($ireportmodulesql, array($reportid, $stdDateFilterField, $stdDateFilter, $startdate, $enddate));
			$log->info("Reports :: Save->Successfully saved vtiger_reportdatefilter");
			//<<<<step5 standarfilder>>>>>>>

			//<<<<step4 columnstototal>>>>>>>
			$idelreportsummarysql = "delete from vtiger_reportsummary where reportsummaryid=?";
			$idelreportsummarysqlresult = $adb->pquery($idelreportsummarysql, array($reportid));

			for ($i=0;$i<count($columnstototal);$i++)
			{
				$ireportsummarysql = "insert into vtiger_reportsummary (REPORTSUMMARYID,SUMMARYTYPE,COLUMNNAME) values (?,?,?)";
				$ireportsummaryresult = $adb->pquery($ireportsummarysql, array($reportid, $i, $columnstototal[$i]));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_reportsummary");
			//<<<<step4 columnstototal>>>>>>>


			//<<<<step5 advancedfilter>>>>>>>

			$idelrelcriteriasql = "delete from vtiger_relcriteria where queryid=?";
			$idelrelcriteriasqlresult = $adb->pquery($idelrelcriteriasql, array($reportid));

			for ($i=0;$i<count($adv_filter_col);$i++)
			{
				$irelcriteriasql = "insert into vtiger_relcriteria(QUERYID,COLUMNINDEX,COLUMNNAME,COMPARATOR,VALUE) values (?,?,?,?,?)";
				$irelcriteriaresult = $adb->pquery($irelcriteriasql, array($reportid, $i, $adv_filter_col[$i], $adv_filter_option[$i], $adv_filter_value[$i]));
			}
			$log->info("Reports :: Save->Successfully saved vtiger_relcriteria");
			//<<<<step5 advancedfilter>>>>>>>

		}else
		{
			$errormessage = "<font color='red'><B>Error Message<ul>
				<li><font color='red'>Error while inserting the record</font>
				</ul></B></font> <br>" ;
			echo $errormessage;
			die;
		}
	}else
	{
		$errormessage = "<font color='red'><B>Error Message<ul>
			<li><font color='red'>Error while inserting the record</font>
			</ul></B></font> <br>" ;
		echo $errormessage;
		die;
	}
	echo '<script>window.opener.location.href = window.opener.location.href;self.close();</script>';
}
?>
