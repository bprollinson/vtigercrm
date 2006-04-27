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


require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
	
$column_array=array('accountid','contact_id');
$table_col_array=array('account.accountname','contactdetails.firstname,contactdetails.lastname');




function getSearchListHeaderValues($focus, $module,$sort_qry='',$sorder='',$order_by='',$relatedlist='',$oCv='')
{
	global $log;
	$log->debug("Entering getSearchListHeaderValues(".$focus.",". $module.",".$sort_qry.",".$sorder.",".$order_by.",".$relatedlist.",".$oCv.") method ...");
        global $adb;
        global $theme;
        global $app_strings;
        global $mod_strings,$current_user;

        $arrow='';
        $qry = getURLstring($focus);
        $theme_path="themes/".$theme."/";
        $image_path=$theme_path."images/";
        $search_header = Array();

        //Get the tabid of the module
        //require_once('include/utils/UserInfoUtil.php')
        $tabid = getTabid($module);
        //added for customview 27/5
        if($oCv)
        {
                if(isset($oCv->list_fields))
		{
                        $focus->list_fields = $oCv->list_fields;
                }
        }
	//Added to reduce the no. of queries logging for non-admin users -- by Minnie-start
	$field_list ='(';
	$j=0;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	foreach($focus->list_fields as $name=>$tableinfo)
	{
		$fieldname = $focus->list_fields_name[$name];
		if($oCv)
		{
			if(isset($oCv->list_fields_name))
			{
				$fieldname = $oCv->list_fields_name[$name];
			}
		}
		if($j != 0)
		{
			$field_list .= ', ';
		}
		$field_list .= "'".$fieldname."'";
		$j++;
	}
	$field_list .=')';
	//Getting the Entries from Profile2 field table
	if($is_admin == false)
	{
		$profileList = getCurrentUserProfileList();
		//changed to get field.fieldname
		$query  = "select profile2field.*,field.fieldname from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid where field.tabid=".$tabid." and profile2field.visible=0 and def_org_field.visible=0  and profile2field.profileid in ".$profileList." and field.fieldname in ".$field_list." group by field.fieldid";
		$result = $adb->query($query);
		$field=Array();
		for($k=0;$k < $adb->num_rows($result);$k++)
		{
			$field[]=$adb->query_result($result,$k,"fieldname");
		}
	}

        //modified for customview 27/5 - $app_strings change to $mod_strings
        foreach($focus->list_fields as $name=>$tableinfo)
        {
                //$fieldname = $focus->list_fields_name[$name];  //commented for customview 27/5
                //added for customview 27/5
                if($oCv)
                {
                        if(isset($oCv->list_fields_name))
                        {
				if( $oCv->list_fields_name[$name] == '')
					$fieldname = 'crmid';
				else
					$fieldname = $oCv->list_fields_name[$name];
                        }else
                        {
				if( $focus->list_fields_name[$name] == '')
					$fieldname = 'crmid';
				else
					$fieldname = $focus->list_fields_name[$name];
					
                        }
                }
		else
                {
                        $fieldname = $focus->list_fields_name[$name];
                }

                if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] ==0 || in_array($fieldname,$field))
		{
                        if(isset($focus->sortby_fields) && $focus->sortby_fields !='')
                        {
                                //Added on 14-12-2005 to avoid if and else check for every list field for arrow image and change order

                                foreach($focus->list_fields[$name] as $tab=>$col)
                                {
                                        if(in_array($col,$focus->sortby_fields))
                                        {
                                                if($relatedlist !='')
                                                {
                                                        if($app_strings[$name])
                                                        {
                                                                $name = $app_strings[$name];
                                                        }
                                                        else
                                                        {
                                                                $name = $mod_strings[$name];
                                                        }
                                                }
                                                else
                                                {
                                                        if($app_strings[$name])
                                                        {
                                                                $lbl_name = $app_strings[$name];
                                                        }
                                                        else
                                                        {
								 $lbl_name = $mod_strings[$name];
                                                        }
                                                        $name = $lbl_name;
                                                }
                                        }
                                        else
                                        {       if($app_strings[$name])
                                                {
                                                        $name = $app_strings[$name];
                                                }
                                                elseif($mod_strings[$name])
                                                {
                                                        $name = $mod_strings[$name];
                                                }
                                        }
                                }
                        }
                        //Added condition to hide the close column in Related Lists
                        //if($name == 'Close' && $relatedlist != '')
                        if($name == 'Close')
                        {
                                //$list_header .= '';
                                // $list_header[] = '';
			}
                        else
                        {
				if($fieldname!='parent_id')
				{
					$fld_name=$fieldname;
                                	$search_header[$fld_name]=$name;
				}
                        }
                }
        }
	$log->debug("Exiting getSearchListHeaderValues method ...");	
        return $search_header;

}


function Search($module)
{
	global $log;
        $log->debug("Entering Search(".$module.") method ...");
	$url_string='';	
	if(isset($_REQUEST['search_field']) && $_REQUEST['search_field'] !="")
        {
                $search_column=$_REQUEST['search_field'];
        }
        if(isset($_REQUEST['search_text']) && $_REQUEST['search_text']!="")
        {
                $search_string=ltrim(rtrim($_REQUEST['search_text']));
        }
        if(isset($_REQUEST['searchtype']) && $_REQUEST['searchtype']!="")
        {


                $search_type=$_REQUEST['searchtype'];

                if($search_type == "BasicSearch")
                {
                        $where=BasicSearch($module,$search_column,$search_string);
                }
                else if ($search_type == "AdvanceSearch")
                {
                }
                else //Global Search
                {
                }
		$url_string = "&search_field=".$search_column."&search_text=".$search_string."&searchtype=BasicSearch";
		return $where."#@@#".$url_string;
		$log->debug("Exiting Search method ...");
        }

}

function get_usersid($table_name,$column_name,$search_string)
{

	global $log;
        $log->debug("Entering get_usersid(".$table_name.",".$column_name.",".$search_string.") method ...");
	global $adb;
	$user_qry="select distinct(users.id)from users inner join crmentity on crmentity.smownerid=users.id where users.user_name like '%".$search_string."%' ";
	$user_result=$adb->query($user_qry);
	$noofuser_rows=$adb->num_rows($user_result);
	$x=$noofuser_rows-1;
	if($noofuser_rows!=0)
	{
		$where="(";
		for($i=0;$i<$noofuser_rows;$i++)
		{
			$user_id=$adb->query_result($user_result,$i,'id');
			$where .= "$table_name.$column_name =".$user_id;
			if($i != $x)
			{
				$where .= " or ";
			}
		}
		$where.=" or groups.groupname like '%".$search_string."%')";
	}
	else
	{
		//$where="$table_name.$column_name =''";
		$where="groups.groupname like '%".$search_string."%' ";
	}	
	$log->debug("Exiting get_usersid method ...");
	return $where;	
}

function getValuesforColumns($column_name,$search_string)
{
	global $log;
	$log->debug("Entering getValuesforColumns(".$column_name.",".$search_string.") method ...");
	global $column_array,$table_col_array;
	for($i=0; $i<count($column_array);$i++)
	{
		
		if($column_name == $column_array[$i])
		{
			$val=$table_col_array[$i];
			$explode_column=explode(",",$val);
			$x=count($explode_column);	
			if($x == 1 )
			{
				$where="$val like '%".$search_string ."%'";
			}
			else 
			{
				$where="(";
				for($j=0;$j<count($explode_column);$j++)
				{
					$where .= $explode_column[$j]." like '%".$search_string."%'";
					if($j != $x-1)
					{
						$where .= " or ";
					}
				}
				$where.=")";
			}
			break 1;
		}
	}
	$log->debug("Exiting getValuesforColumns method ...");
	return $where;
}

function BasicSearch($module,$search_field,$search_string)
{
	 global $log;
         $log->debug("Entering BasicSearch(".$module.",".$search_field.",".$search_string.") method ...");
	global $adb;
	global $column_array,$table_col_array;

	if($search_field =='crmid')
	{
		$column_name='crmid';
		$table_name='crmentity';
		$where="$table_name.$column_name like '%".$search_string."%'";	
	}else
	{	
		$qry="select field.columnname,tablename from tab inner join field on field.tabid=tab.tabid where name='".$module."' and fieldname='".$search_field."'";
		$result = $adb->query($qry);
		$noofrows = $adb->num_rows($result);
		if($noofrows!=0)
		{
			$column_name=$adb->query_result($result,0,'columnname');
			$table_name=$adb->query_result($result,0,'tablename');

			if($table_name == "crmentity" && $column_name == "smownerid")
			{
				$where = get_usersid($table_name,$column_name,$search_string);
			}
			elseif($table_name == "activity" && $column_name == "status")
			{
				$where="$table_name.$column_name like '%".$search_string."%' or activity.eventstatus like '%".$search_string."%'";
			}
			else if(in_array($column_name,$column_array))
			{
				$where = getValuesforColumns($column_name,$search_string);
			}
			else
			{
				$where="$table_name.$column_name like '%".$search_string."%'";
			}
		}
	}
	$log->debug("Exiting BasicSearch method ...");
	return $where;
}

function getAdvSearchfields($module)
{
	global $log;
        $log->debug("Entering getAdvSearchfields(".$module.") method ...");
	global $adb;
	global $current_user;	
	require('user_privileges/user_privileges_'.$current_user->id.'.php');

	$tabid = getTabid($module);

	if($is_admin == true || $profileGlobalPermission[1] == 0 || $profileGlobalPermission[2] == 0)
	{
		$sql = "select * from field ";
		$sql.= " where field.tabid=".$tabid." and";
		$sql.= " field.displaytype in (1,2)";
		$sql.= " order by block,sequence";
	}
	else
	{
		$profileList = getCurrentUserProfileList();
		$sql = "select * from field inner join profile2field on profile2field.fieldid=field.fieldid inner join def_org_field on def_org_field.fieldid=field.fieldid ";
		$sql.= " where field.tabid=".$tabid." and";
		$sql.= " field.displaytype in (1,2) and profile2field.visible=0";
		$sql.= " and def_org_field.visible=0  and profile2field.profileid in ".$profileList." order by block,sequence";
	}


	$result = $adb->query($sql);
	$noofrows = $adb->num_rows($result);
	$block = '';

	for($i=0; $i<$noofrows; $i++)
	{
		$fieldtablename = $adb->query_result($result,$i,"tablename");
		$fieldcolname = $adb->query_result($result,$i,"columnname");
		$block = $adb->query_result($result,$i,"block");
		$fieldtype = explode("~",$fieldtype);
		$fieldtypeofdata = $fieldtype[0];
		$fieldlabel = $adb->query_result($result,$i,"fieldlabel");
		//Added on 14-10-2005 -- added ticket id in list
		if($module == 'HelpDesk' && $block == 25)
		{
			$module_columnlist['crmentity:crmid::HelpDesk_Ticket ID:I'] = 'Ticket ID';
		}
		//Added to include activity type in activity customview list
		if($module == 'Activities' && $block == 19)
		{
			$module_columnlist['activity:activitytype:activitytype:Activities_Activity Type:C'] = 'Activity Type';
		}
		if($fieldlabel == "Related To")
		{
			$fieldlabel = "Related to";
		}
		if($fieldlabel == "Start Date & Time")
		{
			$fieldlabel = "Start Date";
			if($module == 'Activities' && $block == 19)
				$module_columnlist['activity:time_start::Activities_Start Time:I'] = 'Start Time';

		}
		$fieldlabel1 = str_replace(" ","_",$fieldlabel);
		if ($i==0)
			$OPTION_SET .= "<option value=\'".$fieldtablename.".".$fieldcolname."\' selected>".$fieldlabel."</option>";
		else
			$OPTION_SET .= "<option value=\'".$fieldtablename.".".$fieldcolname."\'>".$fieldlabel."</option>";
	}
	$log->debug("Exiting getAdvSearchfields method ...");
	return $OPTION_SET;
}

function getcriteria_options()
{
	global $log;
	$log->debug("Entering getcriteria_options() method ...");
	$CRIT_OPT = "<option value=\'cts\'>Contains</option><option value=\'dcts\'>does not Contains</option><option value=\'is\'>is</option><option value=\'isn\'>is not</option><option value=\'bwt\'>Begins With</option><option value=\'ewt\'>Ends With</option>";
	$log->debug("Exiting getcriteria_options method ...");
	return $CRIT_OPT;
}
function getSearch_criteria($criteria,$searchstring,$searchfield)
{
	global $log;
	$log->debug("Entering getSearch_criteria(".$criteria.",".$searchstring.",".$searchfield.") method ...");
	$where_string = '';
	switch($criteria)
	{
		case 'cts':
			$where_string = $searchfield." like '%".$searchstring."%' ";
			break;
		
		case 'dcts':
			$where_string = $searchfield." not like '%".$searchstring."%' ";
			break;
			
		case 'is':
			$where_string = $searchfield." = '".$searchstring."' ";
			break;
			
		case 'isn':
			$where_string = $searchfield." <> '".$searchstring."' ";
			break;
			
		case 'bwt':
			$where_string = $searchfield." like '".$searchstring."%' ";
			break;

		case 'ewt':
			$where_string = $searchfield." like '%".$searchstring."' ";
			break;

	}
	$log->debug("Exiting getSearch_criteria method ...");
	return $where_string;
}								
function getWhereCondition($currentModule)
{
	global $log;
       $log->debug("Entering getWhereCondition(".$currentModule.") method ...");
	
	if($_REQUEST['searchtype']=='advance')
	{
		$adv_string='';
		$url_string='';
		if(isset($_REQUEST['search_cnt']))
		$tot_no_criteria = $_REQUEST['search_cnt'];
		if($_REQUEST['matchtype'] == 'all')
			$matchtype = "and";
		else
			$matchtype = "or";
		for($i=0; $i<$tot_no_criteria; $i++)
		{
			if($i == $tot_no_criteria-1)
			$matchtype= "";
			
			$table_colname = 'Fields'.$i;
			$search_condition = 'Condition'.$i;
			$search_value = 'Srch_value'.$i;

			$tab_col = str_replace('\'','',stripslashes($_REQUEST[$table_colname]));
			$srch_cond = str_replace('\'','',stripslashes($_REQUEST[$search_condition]));
			$srch_val = $_REQUEST[$search_value];
			$url_string .="&Fields".$i."=".$tab_col."&Condition".$i."=".$srch_cond."&Srch_value".$i."=".$srch_val;
			if($tab_col == "crmentity.smownerid")
			{
				$adv_string .= " (".getSearch_criteria($srch_cond,$srch_val,'users.user_name')." or";	
				$adv_string .= " ".getSearch_criteria($srch_cond,$srch_val,'groups.groupname')." )".$matchtype;	
			}
			elseif($tab_col == "activity.status")
			{
				$adv_string .= " (".getSearch_criteria($srch_cond,$srch_val,'activity.status')." or";	
				$adv_string .= " ".getSearch_criteria($srch_cond,$srch_val,'activity.eventstatus')." )".$matchtype;	
			}
			else
			{
				$adv_string .= " ".getSearch_criteria($srch_cond,$srch_val,$tab_col)." ".$matchtype;	
			}
		}
		$where=$adv_string."#@@#".$url_string."&searchtype=advance&search_cnt=".$tot_no_criteria."&matchtype=".$_REQUEST['matchtype'];
	}
	else
	{
 		$where=Search($currentModule);
	}
	$log->debug("Exiting getWhereCondition method ...");
	return $where;

}
?>
