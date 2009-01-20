<?PHP
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/CustomFieldUtil.php');
require_once('Smarty_setup.php');

global $mod_strings,$app_strings,$app_list_strings,$theme,$adb;
global $log;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
$visible = $_REQUEST['visible'];
$disable = $_REQUEST['disable'];
$label = getTranslatedString($_REQUEST['label']);
require_once($theme_path.'layout_utils.php');

$output  .= '<div class="layerPopup" style="position:relative; display:block">' .
		'	<form action="index.php" method="post" name="fieldinfoform"> 
			<input type="hidden" name="module" value="Settings">
	  		<input type="hidden" name="action" value="SettingsAjax">
	  		<input type="hidden" name="fld_module" value="'.$_REQUEST['fld_module'].'">
	  		<input type="hidden" name="parenttab" value="Settings">
          	<input type="hidden" name="file" value="UpdateMandatoryFields">
 			<input type="hidden" name="fieldid" value="'.$_REQUEST['fieldid'].'">
 			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="layerHeadingULine">
				<tr>
					<td width="95%" align="left" class="layerPopupHeading">'.$label.'</td>
			
					<td width="5%" align="right"><a href="javascript:fninvsh(\'fieldInfo\');"><img src="'.$image_path.'close.gif" border="0"  align="absmiddle" /></a></td>
				</tr>
			</table>
			<table border=0 cellspacing=0 cellpadding="5" width=99%> 
				<tr>
					<td valign="top">
						<input name="mandatory"  type="checkbox" '.$visible.'  '.$disable.' >
						&nbsp;<b>Mandatory Field</b>
					</td>
				</tr>
				<tr>
					<td valign="top">
					<input name="presence" value="" type="checkbox" >
					&nbsp;<b>Show</b>
					</td>
				</tr>
				<tr>
				<td align="center">
					<input type="submit" name="save"  value=" &nbsp; '.$app_strings['LBL_SAVE_BUTTON_LABEL'].'&nbsp; " class="crmButton small save" "/>&nbsp;
					<input type="button" name="cancel" value=" '.$app_strings['LBL_CANCEL_BUTTON_LABEL'].' " class="crmButton small cancel" onclick="fninvsh(\'fieldInfo\');" />
				</td>
			</tr>
			</table>
		</form></div>';
		
		echo $output;
?>
