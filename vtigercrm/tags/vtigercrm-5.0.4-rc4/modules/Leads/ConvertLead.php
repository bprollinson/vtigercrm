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
require_once('include/utils/utils.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/UserInfoUtil.php');

global $mod_strings,$app_strings,$log,$current_user,$theme;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

if(isset($_REQUEST['record']))
{
	$id = $_REQUEST['record'];
	$log->debug(" the id is ".$id);
}
$category = getParentTab();
//Retreive lead details from database
$sql = "SELECT firstname, lastname, company, smownerid from vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid where vtiger_leaddetails.leadid =?";
$result = $adb->pquery($sql, array($id));
$row = $adb->fetch_array($result);

$firstname = $row["firstname"];
$log->debug(" the firstname is ".$firstname);
$lastname = $row["lastname"];
$log->debug(" the lastname is ".$lastname);
$company = $row["company"];
$log->debug(" the company is  ".$company);
$potentialname = $row["company"] ."-";
$log->debug(" the vtiger_potentialname is ".$potentialname);
$userid = $row["smownerid"];
$log->debug(" the userid is ".$userid);

//Retreiving the current user id
$modified_user_id = $current_user->id;
$log->info("Convert Lead view");

$date_format = parse_calendardate($app_strings['NTC_DATE_FORMAT']);

$sales_stage_query="select * from vtiger_sales_stage";
$sales_stage_result = $adb->pquery($sales_stage_query, array());
$noofsalesRows = $adb->num_rows($sales_stage_result);
$sales_stage_fld = '';
for($j = 0; $j < $noofsalesRows; $j++)
{

        $sales_stageValue=$adb->query_result($sales_stage_result,$j,strtolower(sales_stage));

        if($value == $sales_stageValue)
        {
                $chk_val = "selected";
        }
        else
        {
                $chk_val = '';
        }

        $sales_stage_fld.= '<OPTION value="'.$sales_stageValue.'" '.$chk_val.'>'.getTranslatedString($sales_stageValue).'</OPTION>';
}
$convertlead = '<form name="ConvertLead" method="POST" action="index.php">
	<input type="hidden" name="module" value="Leads">
	<input type="hidden" name="record" value="'.$id.'">
	<input type="hidden" name="action">
	<input type="hidden" name="parenttab" value="'.$category.'">
	<input type="hidden" name="current_user_id" value="'.$modified_user_id.'">
	
	<div id="orgLay" style="display: block;" class="layerPopup" >
	<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
		<tr>
			<td class="genHeaderSmall" align="left"><IMG src="'.$image_path.'Leads.gif">'.$mod_strings['LBL_CONVERT_LEAD'].' '.$firstname.' '.$lastname.'</td>
			<td align="right"><a href="javascript:fninvsh(\'orgLay\');"><img src="'.$image_path.'close.gif" align="absmiddle" border="0"></a></td>
		</tr>
		</table>
	
	<table border=0 cellspacing=0 cellpadding=0 width=95% align=center> 
	<tr>
		<td class=small >
			<table border=0 celspacing=0 cellpadding=5 width=100% align=center bgcolor=white>
				<tr>
						<td colspan="2" class="detailedViewHeader"><b>'.$mod_strings['LBL_CONVERT_LEAD_INFORMATION'].'</b></td>
				</tr>
                <tr>
						<td align="right" class="dvtCellLabel" width="50%">'.$app_strings['LBL_ASSIGNED_TO'].'</td>
                       	<td class="dvtCellInfo" width="50%">
                        		<select name="assigned_user_id" class="detailedViewTextBox">'.get_select_options_with_id(get_user_array(false), $userid).'</select>
						</td>
				</tr>
				<tr>
					<td align="right" class="dvtCellLabel">'.$mod_strings['LBL_ACCOUNT_NAME'].'</td>
					<td class="dvtCellInfo"><input type="text" name="account_name" class="detailedViewTextBox" value="'.$company.'" readonly="readonly"></td>
			</tr>';

if(isPermitted("Potentials",'EditView') == 'yes')
{
$convertlead .='<tr>
			<td align="right" class="dvtCellLabel">'.$mod_strings['LBL_DO_NOT_CREATE_NEW_POTENTIAL'].'</td>
			<td class="dvtCellInfo"><input type="checkbox" name="createpotential" onClick="fnSlide2(\'ch\',\'cc\')"></td>
		</tr>
		<tr>
			<td colspan="2" id="ch" height="100" style="padding:0px;" >
				<div style="display:block;" id="cc"  >
					<table width="100%" border="0" cellpadding="5" cellspacing="0" >
						<tr>
							<td align="right" class="dvtCellLabel" width="53%"><font color="red">*</font>'.$mod_strings['LBL_POTENTIAL_NAME'].'</td>
							<td class="dvtCellInfo" width="47%">
							<input name="potential_name" class="detailedViewTextBox" value="'.$potentialname.'" tabindex="3">
                                                        </td>
						</tr>
						<tr>
							<td align="right" class="dvtCellLabel"><font color="red">*</font>'.$mod_strings['LBL_POTENTIAL_CLOSE_DATE'].'</td>
							<td class="dvtCellInfo">
								<input name="closedate" style="border: 1px solid rgb(186, 186, 186);" id="jscal_field_closedate" type="text" tabindex="4" size="10" maxlength="10" value="'.$focus->closedate.'">
								<img src="'.$image_path.'calendar.gif" id="jscal_trigger_closedate" >
								<font size=1><em old="(yyyy-mm-dd)">('.$current_user->date_format.')</em></font>
							<script id="conv_leadcal">
								getCalendarPopup(\'jscal_trigger_closedate\',\'jscal_field_closedate\',\''.$date_format.'\')
							</script>
							</td>
						</tr>
						<tr>
							<td align="right" class="dvtCellLabel">'.$mod_strings['LBL_POTENTIAL_AMOUNT'].'</td>
							<td class="dvtCellInfo"><input type="text" name="potential_amount" class="detailedViewTextBox">'.$potential_amount.'</td>
						</tr>
						<tr>
							<td align="right" class="dvtCellLabel"><font color="red">*</font>'.$mod_strings['LBL_POTENTIAL_SALES_STAGE'].'</td>
							<td class="dvtCellInfo"><select name="potential_sales_stage" class="detailedViewTextBox">'.$sales_stage_fld.'</select></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>';
}
$convertlead .='</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=5 width=100% class="layerPopupTransport">
	<tr>
			<td align="center">
				<input name="Save" value=" '.$app_strings['LBL_SAVE_BUTTON_LABEL'].' " onclick="this.form.action.value=\'LeadConvertToEntities\'; return verify_data(ConvertLead)" type="submit"  class="crmbutton save small">&nbsp;&nbsp;
				<input type="button" name=" Cancel " value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " onClick="hide(\'orgLay\')" class="crmbutton cancel small">
			</td>
		</tr>
	</table>
</div></form>';
echo $convertlead;

?>
