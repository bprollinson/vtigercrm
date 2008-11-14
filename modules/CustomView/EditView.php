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
require_once('data/Tracker.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;
$focus = 0;
global $theme;
global $log,$default_charset;

//<<<<<>>>>>>
global $oCustomView;
//<<<<<>>>>>>

$error_msg = '';
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once('modules/CustomView/CustomView.php');

$cv_module = $_REQUEST['module'];

$recordid = $_REQUEST['record'];

$smarty->assign("MOD", $mod_strings);
$smarty->assign("CATEGORY", htmlspecialchars($_REQUEST['parenttab'],ENT_QUOTES,$default_charset));
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("MODULE",$cv_module);
$smarty->assign("CVMODULE", $cv_module);
$smarty->assign("CUSTOMVIEWID",$recordid);
$smarty->assign("DATEFORMAT",$current_user->date_format);
$smarty->assign("JS_DATEFORMAT",parse_calendardate($app_strings['NTC_DATE_FORMAT']));
$smarty->assign("DATE_JS", '<script>userDateFormat = "'.$current_user->date_format.'" </script>');
if($recordid == "")
{
	$oCustomView = new CustomView();
	$modulecollist = $oCustomView->getModuleColumnsList($cv_module);
	$log->info('CustomView :: Successfully got ColumnsList for the module'.$cv_module);
	if(isset($modulecollist))
	{
		$choosecolhtml = getByModule_ColumnsHTML($cv_module,$modulecollist);
	}
	//step2
	$stdfilterhtml = $oCustomView->getStdFilterCriteria();
	$log->info('CustomView :: Successfully got StandardFilter for the module'.$cv_module);
	$stdfiltercolhtml = getStdFilterHTML($cv_module);
	$stdfilterjs = $oCustomView->getCriteriaJS();

	//step4
	$advfilterhtml = getAdvCriteriaHTML();
	for($i=1;$i<10;$i++)
	{
		$smarty->assign("CHOOSECOLUMN".$i,$choosecolhtml);
	}
	$log->info('CustomView :: Successfully got AdvancedFilter for the module'.$cv_module);
	for($i=1;$i<6;$i++)
	{
		$smarty->assign("FOPTION".$i,$advfilterhtml);
		$smarty->assign("BLOCK".$i,$choosecolhtml);
	}

	$smarty->assign("STDFILTERCOLUMNS",$stdfiltercolhtml);
	$smarty->assign("STDCOLUMNSCOUNT",count($stdfiltercolhtml));
	$smarty->assign("STDFILTERCRITERIA",$stdfilterhtml);
	$smarty->assign("STDFILTER_JAVASCRIPT",$stdfilterjs);

	$smarty->assign("MANDATORYCHECK",implode(",",array_unique($oCustomView->mandatoryvalues)));
	$smarty->assign("SHOWVALUES",implode(",",$oCustomView->showvalues));
        $data_type[] = $oCustomView->data_type;
        $smarty->assign("DATATYPE",$data_type);
}
else
{
	$oCustomView = new CustomView();

	$customviewdtls = $oCustomView->getCustomViewByCvid($recordid);
	$log->info('CustomView :: Successfully got ViewDetails for the Viewid'.$recordid);
	$modulecollist = $oCustomView->getModuleColumnsList($cv_module);

	$selectedcolumnslist = $oCustomView->getColumnsListByCvid($recordid);
	$log->info('CustomView :: Successfully got ColumnsList for the Viewid'.$recordid);

	$smarty->assign("VIEWNAME",$customviewdtls["viewname"]);

	if($customviewdtls["setdefault"] == 1)
	{
		$smarty->assign("CHECKED","checked");
	}
	if($customviewdtls["setmetrics"] == 1)
	{
		$smarty->assign("MCHECKED","checked");
	}
	for($i=1;$i<10;$i++)
	{
		$choosecolhtml = getByModule_ColumnsHTML($cv_module,$modulecollist,$selectedcolumnslist[$i-1]);
		$smarty->assign("CHOOSECOLUMN".$i,$choosecolhtml);
	}

	$stdfilterlist = $oCustomView->getStdFilterByCvid($recordid);
	$log->info('CustomView :: Successfully got Standard Filter for the Viewid'.$recordid);
	$stdfilterlist["stdfilter"] = ($stdfilterlist["stdfilter"] != "") ? ($stdfilterlist["stdfilter"]) : ("custom");
	$stdfilterhtml = $oCustomView->getStdFilterCriteria($stdfilterlist["stdfilter"]);
	$stdfiltercolhtml = getStdFilterHTML($cv_module,$stdfilterlist["columnname"]);
	$stdfilterjs = $oCustomView->getCriteriaJS();

	if(isset($stdfilterlist["startdate"]) && isset($stdfilterlist["enddate"]))
	{
		$smarty->assign("STARTDATE",getDisplayDate($stdfilterlist["startdate"]));
		$smarty->assign("ENDDATE",getDisplayDate($stdfilterlist["enddate"]));
	}else{
		$smarty->assign("STARTDATE",$stdfilterlist["startdate"]);
		$smarty->assign("ENDDATE",$stdfilterlist["enddate"]);
	}	

	$advfilterlist = $oCustomView->getAdvFilterByCvid($recordid);
	$log->info('CustomView :: Successfully got Advanced Filter for the Viewid'.$recordid,'info');
	for($i=1;$i<6;$i++)
	{
		$advfilterhtml = getAdvCriteriaHTML($advfilterlist[$i-1]["comparator"]);
		$advcolumnhtml = getByModule_ColumnsHTML($cv_module,$modulecollist,$advfilterlist[$i-1]["columnname"]);
		$smarty->assign("FOPTION".$i,$advfilterhtml);
		$smarty->assign("BLOCK".$i,$advcolumnhtml);
		$col = explode(":",$advfilterlist[$i-1]["columnname"]);
		$temp_val = explode(",",$advfilterlist[$i-1]["value"]);
		$and_text = "&nbsp;".$mod_strings['LBL_AND'];
		if($col[4] == 'D' || ($col[4] == 'T' && $col[1] != 'time_start' && $col[1] != 'time_end') || $col[4] == 'DT')
		{
			$val = Array();
			for($x=0;$x<count($temp_val);$x++)
			if(trim($temp_val[$x] != ""))
				$val[$x] = getDisplayDate(trim($temp_val[$x]));
			$advfilterlist[$i-1]["value"] = implode(", ",$val);
			$and_text = "<em old='(yyyy-mm-dd)'>(".$current_user->date_format.")</em>&nbsp;".$mod_strings['LBL_AND'];
		}
		$smarty->assign("VALUE".$i,$advfilterlist[$i-1]["value"]);
		$smarty->assign("AND_TEXT".$i,$and_text);
	}

	$smarty->assign("STDFILTERCOLUMNS",$stdfiltercolhtml);
	$smarty->assign("STDCOLUMNSCOUNT",count($stdfiltercolhtml));
	$smarty->assign("STDFILTERCRITERIA",$stdfilterhtml);
	$smarty->assign("STDFILTER_JAVASCRIPT",$stdfilterjs);
	$smarty->assign("MANDATORYCHECK",implode(",",array_unique($oCustomView->mandatoryvalues)));
	$smarty->assign("SHOWVALUES",implode(",",$oCustomView->showvalues));
	$smarty->assign("EXIST","true");
	$cactionhtml = "<input name='customaction' class='button' type='button' value='Create Custom Action' onclick=goto_CustomAction('".$cv_module."');>";

	if($cv_module == "Leads" || $cv_module == "Accounts" || $cv_module == "Contacts")
	{
		$smarty->assign("CUSTOMACTIONBUTTON",$cactionhtml);
	}
        $data_type[] = $oCustomView->data_type;
        $smarty->assign("DATATYPE",$data_type);
}

$smarty->assign("RETURN_MODULE", $cv_module);
if($cv_module == "Calendar")
        $return_action = "ListView";
else
        $return_action = "index";
	
$smarty->assign("RETURN_ACTION", $return_action);

$smarty->display("CustomView.tpl");

/** to get the custom columns for the given module and columnlist  
  * @param $module (modulename):: type String 
  * @param $columnslist (Module columns list):: type Array 
  * @param $selected (selected or not):: type String (Optional)
  * @returns  $advfilter_out array in the following format 
  *	$advfilter_out = Array ('BLOCK1 NAME'=>
  * 					Array(0=>
  *						Array('value'=>$tablename:$colname:$fieldname:$fieldlabel:$typeofdata,
  *						      'text'=>$fieldlabel,
  *					      	      'selected'=><selected or ''>),
  *			      		      1=>
  *						Array('value'=>$tablename1:$colname1:$fieldname1:$fieldlabel1:$typeofdata1,
  *						      'text'=>$fieldlabel1,
  *					      	      'selected'=><selected or ''>)
  *					      ),
  *								|
  *								|
  *					      n=>
  *						Array('value'=>$tablenamen:$colnamen:$fieldnamen:$fieldlabeln:$typeofdatan,
  *						      'text'=>$fieldlabeln,
  *					      	      'selected'=><selected or ''>)
  *					      ), 
  *				'BLOCK2 NAME'=>
  * 					Array(0=>
  *						Array('value'=>$tablename:$colname:$fieldname:$fieldlabel:$typeofdata,
  *						      'text'=>$fieldlabel,
  *					      	      'selected'=><selected or ''>),
  *			      		      1=>
  *						Array('value'=>$tablename1:$colname1:$fieldname1:$fieldlabel1:$typeofdata1,
  *						      'text'=>$fieldlabel1,
  *					      	      'selected'=><selected or ''>)
  *					      )
  *								|
  *								|
  *					      n=>
  *						Array('value'=>$tablenamen:$colnamen:$fieldnamen:$fieldlabeln:$typeofdatan,
  *						      'text'=>$fieldlabeln,
  *					      	      'selected'=><selected or ''>)
  *					      ), 
  *
  *					||
  *					||
  *				'BLOCK_N NAME'=>
  * 					Array(0=>
  *						Array('value'=>$tablename:$colname:$fieldname:$fieldlabel:$typeofdata,
  *						      'text'=>$fieldlabel,
  *					      	      'selected'=><selected or ''>),
  *			      		      1=>
  *						Array('value'=>$tablename1:$colname1:$fieldname1:$fieldlabel1:$typeofdata1,
  *						      'text'=>$fieldlabel1,
  *					      	      'selected'=><selected or ''>)
  *					      )
  *								|
  *								|
  *					      n=>
  *						Array('value'=>$tablenamen:$colnamen:$fieldnamen:$fieldlabeln:$typeofdatan,
  *						      'text'=>$fieldlabeln,
  *					      	      'selected'=><selected or ''>)
  *					      ), 
  *
  */

function getByModule_ColumnsHTML($module,$columnslist,$selected="")
{
	global $oCustomView, $current_language;
	global $app_list_strings;
	$advfilter = array();
	$mod_strings = return_specified_module_language($current_language,$module);
	
	$check_dup = Array();
	foreach($oCustomView->module_list[$module] as $key=>$value)
	{
		$advfilter = array();			
		$label = $key;
		if(isset($columnslist[$module][$key]))
		{
			foreach($columnslist[$module][$key] as $field=>$fieldlabel)
			{
				if(!in_array($fieldlabel,$check_dup))
				{
					if(isset($mod_strings[$fieldlabel]))
					{
						if($selected == $field)
						{
							$advfilter_option['value'] = $field;
							$advfilter_option['text'] = $mod_strings[$fieldlabel];
							$advfilter_option['selected'] = "selected";
						}else
						{
							$advfilter_option['value'] = $field;
							$advfilter_option['text'] = $mod_strings[$fieldlabel];
							$advfilter_option['selected'] = "";
						}
					}else
					{
						if($selected == $field)
						{
							$advfilter_option['value'] = $field;
							$advfilter_option['text'] = $fieldlabel;
							$advfilter_option['selected'] = "selected";
						}else
						{
							$advfilter_option['value'] = $field;
							$advfilter_option['text'] = $fieldlabel;
							$advfilter_option['selected'] = "";
						}
					}
					$advfilter[] = $advfilter_option;
					$check_dup [] = $fieldlabel;
				}
			}
			$advfilter_out[$label]= $advfilter;
		}
	}
	
	$finalfield = Array();
	foreach($advfilter_out as $header=>$value)
	{
		if($header == $mod_strings['LBL_TASK_INFORMATION'])
		{
			$newLabel = $mod_strings['LBL_CALENDAR_INFORMATION'];
		    	$finalfield[$newLabel] = $advfilter_out[$header];
		    	
		}
		elseif($header == $mod_strings['LBL_EVENT_INFORMATION'])
		{
			$index = count($finalfield[$newLabel]);
			foreach($value as $key=>$result)
			{
				$finalfield[$newLabel][$index]=$result;
				$index++;
			}
		}
		else
		{
			$finalfield = $advfilter_out;
		}

		$advfilter_out=$finalfield;
	}
	return $advfilter_out;
}

       /** to get the standard filter criteria  
	* @param $module(module name) :: Type String 
	* @param $elected (selection status) :: Type String (optional)
	* @returns  $filter Array in the following format
	* $filter = Array( 0 => array('value'=>$tablename:$colname:$fieldname:$fieldlabel,'text'=>$mod_strings[$field label],'selected'=>$selected),
	* 		     1 => array('value'=>$$tablename1:$colname1:$fieldname1:$fieldlabel1,'text'=>$mod_strings[$field label1],'selected'=>$selected),	
	*/	
function getStdFilterHTML($module,$selected="")
{
	global $app_list_strings, $current_language,$app_strings,$current_user;
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	global $oCustomView;
	$stdfilter = array();
	$result = $oCustomView->getStdCriteriaByModule($module);
	$mod_strings = return_module_language($current_language,$module);

	if(isset($result))
	{
		foreach($result as $key=>$value)
		{
			if($value == 'Start Date & Time')
			{
				$value = 'Start Date';
			}
			if(isset($mod_strings[$value]))
			{
				if($key == $selected)
				{

					$filter['value'] = $key;
					$filter['text'] = $app_list_strings['moduleList'][$module]." - ".$mod_strings[$value];
					$filter['selected'] = "selected";
				}else
				{
						$filter['value'] = $key;
						$filter['text'] = $app_list_strings['moduleList'][$module]." - ".$mod_strings[$value];
						$filter['selected'] ="";
				}
			}
			else
			{
				if($key == $selected)
				{
					$filter['value'] = $key;
					$filter['text'] = $app_list_strings['moduleList'][$module]." - ".$value;
					$filter['selected'] = 'selected';
				}else
				{
					$filter['value'] = $key;
					$filter['text'] = $app_list_strings['moduleList'][$module]." - ".$value;
					$filter['selected'] ='';
				}
			}
			$stdfilter[]=$filter;
			//added to fix ticket #5117. If a user doesn't have permission for a field and it has been used to fileter a custom view, it should be get displayed to him as Not Accessible.
			if(!$is_admin && $selected != '' && $filter['selected'] == '')
			{
				$keys = explode(":",$selected);
				if(getFieldVisibilityPermission($module,$current_user->id,$keys[2]) != '0')
				{
					$filter['value'] = "not_accessible";
					$filter['text'] = $app_strings["LBL_NOT_ACCESSIBLE"];
					$filter['selected'] = "selected";
					$stdfilter[]=$filter;
				}
			}

		}

	}
	return $stdfilter;
}

      /** to get the Advanced filter criteria  
	* @param $selected :: Type String (optional)
	* @returns  $AdvCriteria Array in the following format
	* $AdvCriteria = Array( 0 => array('value'=>$tablename:$colname:$fieldname:$fieldlabel,'text'=>$mod_strings[$field label],'selected'=>$selected),
	* 		     1 => array('value'=>$$tablename1:$colname1:$fieldname1:$fieldlabel1,'text'=>$mod_strings[$field label1],'selected'=>$selected),	
	*		                             		|	
	* 		     n => array('value'=>$$tablenamen:$colnamen:$fieldnamen:$fieldlabeln,'text'=>$mod_strings[$field labeln],'selected'=>$selected))	
	*/
function getAdvCriteriaHTML($selected="")
{
	global $adv_filter_options;
	global $app_list_strings;
	$AdvCriteria = array();
	foreach($adv_filter_options as $key=>$value)
	{
		if($selected == $key)
		{
			$advfilter_criteria['value'] = $key;
			$advfilter_criteria['text'] = $value; 
			$advfilter_criteria['selected'] = "selected";
		}else
		{
			$advfilter_criteria['value'] = $key;
			$advfilter_criteria['text'] = $value;
			$advfilter_criteria['selected'] = "";
		}
		$AdvCriteria[] = $advfilter_criteria;
	}

	return $AdvCriteria;
}
?>
