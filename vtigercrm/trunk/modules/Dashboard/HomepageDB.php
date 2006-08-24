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
require_once("modules/Dashboard/Entity_charts.php");
/**
 * Function to get Dashboard in homepage
 * return the graph - $sHTML
 */
	global $current_user,$user_id,$date_start,$end_date,$tmp_dir,$mod_strings;
	$type='recordsforuser';
	//require('user_privileges/user_privileges_'.$current_user->id.'.php');
	//if($is_admin)
	//	$homepagedb_query = "select vtiger_crmentity.* from vtiger_crmentity where vtiger_crmentity.setype in ('Accounts','Contacts','Leads','Potentials','Quotes','Invoice','PurchaseOrder','SalesOrder','Activities','HelpDesk','Campaigns') and vtiger_crmentity.deleted=0";
	//else	
	$homepagedb_query = "select * from vtiger_crmentity se left join vtiger_leaddetails le on le.leadid=se.crmid left join vtiger_activity act on act.activityid=se.crmid where se.deleted=0 and (le.converted=0 or le.converted is null) and ((act.status!='Completed' and act.status!='Deferred') or act.status is null) and ((act.eventstatus!='Held' and act.eventstatus!='Not Held') or act.eventstatus is null) and setype in ('Accounts','Contacts','Leads','Potentials','Quotes','Invoice','PurchaseOrder', 'SalesOrder','Calendar','HelpDesk','Campaigns') and se.deleted=0 and se.smownerid=".$current_user->id;
	$graph_by="setype";
	$graph_title=$mod_strings['recordsforuser'].' '.$current_user->user_name;
	$module="Home";
	$where="";
	$query=$homepagedb_query;

	//Giving the Cached image name	
	$cache_file_name=abs(crc32($current_user->id))."_".$type."_".crc32($date_start.$end_date).".png";
        $html_imagename=$graph_by; //Html image name for the graph
	$graph_details=module_Chart($current_user->id,$date_start,$end_date,$query,$graph_by,$graph_title,$where,$module,$type);
	if($graph_details!=0)
        {
                $name_val=$graph_details[0];
                $cnt_val=$graph_details[1];
                $graph_title=$graph_details[2];
                $target_val=$graph_details[3];
                $graph_date=$graph_details[4];
                $urlstring=$graph_details[5];
                $cnt_table=$graph_details[6];
	       	$test_target_val=$graph_details[7];

                $width=425;
                $height=225;
                $top=30;
                $left=140;
                $bottom=120;
                $title=$graph_title;
		$sHTML = render_graph($tmp_dir."vert_".$cache_file_name,$html_imagename."_vert",$cnt_val,$name_val,$width,$height,$left,$right,$top,$bottom,$title,$target_val,"vertical");
		echo $sHTML;
		
        }
	else
	{
		echo $mod_strings[LBL_NO_DATA];	
	}


?>
