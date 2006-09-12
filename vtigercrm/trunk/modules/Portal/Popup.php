<?
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/

require_once('modules/Portal/Portal.php');
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $adb;
if(isset($_REQUEST['record']) && $_REQUEST['record'] !='')
{
	$portalid = $_REQUEST['record'];
	$query="select * from vtiger_portal where portalid =$portalid";
	$result=$adb->query($query);
	$portalname = $adb->query_result($result,0,'portalname');
        $portalurl = $adb->query_result($result,0,'portalurl');		
}
$portal_inputs='';
$portal_inputs.='<div style="display:block;position:relative;" id="orgLay" class="layerPopup">
		<table border="0" cellpadding="3" cellspacing="0" width="100%" class="layerHeadingULine">
		<tr>
			<td class="layerPopupHeading" align="left" width="60%">' .$mod_strings['LBL_ADD'] .' '.$mod_strings['LBL_BOOKMARK'].'</td>
			<td align="right" width="40%"><a href="javascript:fninvsh(\'orgLay\');"><img src="../themes/blue/images/close.gif" align="absmiddle" border="0"></a></td>
		</tr>
		</table>
<table border="0" cellspacing="0" cellpadding="5" width="95%" align="center"> 
	<tr>
	<td class="small" >
		<table border="0" celspacing="0" cellpadding="5" width="100%" align="center" bgcolor="white">
		
		<tr>

			<td align="right" width="40%" ><b>'.$mod_strings['LBL_BOOKMARK'].' ' .$mod_strings['LBL_URL'] .' </b></td>
			<td align="left" width="60%"><input name="portalurl" id="portalurl" class="txtBox" value="'.$portalurl.'" type="text"></td>
		</tr>
		<tr>
			<td align="right" width="40%"> <b>'.$mod_strings['LBL_BOOKMARK'].' ' .$mod_strings['LBL_NAME'] .' </b></td>
			<td align="left" width="60%"><input name="portalname" id="portalname" value="'.$portalname.'" class="txtBox" type="text"></td>
		</tr>
		</table>
	</td>
	</tr>
</table>
<table border="0" cellspacing="0" cellpadding="5" width="100%" class="layerPopupTransport">
	<tr>
	<td align="center">
			<input name="save" value=" &nbsp;'.$app_strings['LBL_SAVE_BUTTON_LABEL'].'&nbsp; " class="crmbutton small save" onClick="SaveSite(\''.$portalid.'\')" type="button">&nbsp;&nbsp;
			<input name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="crmbutton small cancel" onclick="fninvsh(\'orgLay\');" type="button">
	</td>
	</tr>
</table>
</div>';
	
echo $portal_inputs;


?>
