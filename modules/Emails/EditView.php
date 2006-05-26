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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Emails/EditView.php,v 1.25 2005/04/18 10:37:49 samk Exp $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Emails/Email.php');
require_once('modules/Emails/Forms.php');
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/FormValidationUtil.php');

global $log;
global $app_strings;
global $app_list_strings;
global $mod_strings;
global $current_user;
global $currentModule;

$focus = new Email();
$smarty = new vtigerCRM_Smarty();

if($_REQUEST['upload_error'] == true)
{
        echo '<br><b><font color="red"> The selected file has no data or a invalid file.</font></b><br>';
}

//Email Error handling
if($_REQUEST['mail_error'] != '') 
{
	require_once("modules/Emails/mail.php");
	echo parseEmailErrorString($_REQUEST['mail_error']);
}


if(isset($_REQUEST['record']) && $_REQUEST['record'] !='') 
{
	$focus->id = $_REQUEST['record'];
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($_REQUEST['record'],"Emails");
	if(isset($_REQUEST['forward']) && $_REQUEST['forward'] != '')
	{
		$focus->mode = '';
	}else
	{
		$query = 'select idlists,from_email,to_email,cc_email,bcc_email from emaildetails where emailid ='.$focus->id;
		$result = $adb->query($query);
		$smarty->assign('FROM_MAIL',$adb->query_result($result,0,'from_email'));	
		$to_email = ereg_replace('###',',',$adb->query_result($result,0,'to_email'));
		$smarty->assign('TO_MAIL',$to_email);	
		$smarty->assign('CC_MAIL',ereg_replace('###',',',$adb->query_result($result,0,'cc_email')));	
		$smarty->assign('BCC_MAIL',ereg_replace('###',',',$adb->query_result($result,0,'bcc_email')));	
		$smarty->assign('IDLISTS',ereg_replace('###',',',$adb->query_result($result,0,'idlists')));	
	}
    $log->info("Entity info successfully retrieved for EditView.");
	$focus->name=$focus->column_fields['name'];		
}
elseif(isset($_REQUEST['sendmail']) && $_REQUEST['sendmail'] !='')
{
	$mailids = get_to_emailids($_REQUEST['pmodule']);
	$smarty->assign('TO_MAIL',$mailids['mailds']);
	$smarty->assign('IDLISTS',$mailids['idlists']);	
	$focus->mode = '';
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$disp_view = getView($focus->mode);
$details = getBlocks($currentModule,$disp_view,$mode,$focus->column_fields);
if(isset($_REQUEST['templateid']) && $_REQUEST['templateid'] !='')
{
	$templatedetails = getTemplateDetails($_REQUEST['templateid']);
	$details['Email Information'][2][0][3][0] = $templatedetails[2];	
	$details['Email Information'][3][0][3][0] = $templatedetails[1];	
}
$smarty->assign("BLOCKS",$details['Email Information']);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",$app_strings['Email']);
//Display the FCKEditor or not? -- configure $FCKEDITOR_DISPLAY in config.php 
$smarty->assign("FCKEDITOR_DISPLAY",$FCKEDITOR_DISPLAY);


//needed when creating a new email with default values passed in
if (isset($_REQUEST['contact_name']) && is_null($focus->contact_name)) 
{
	$focus->contact_name = $_REQUEST['contact_name'];
}
if (isset($_REQUEST['contact_id']) && is_null($focus->contact_id)) 
{
	$focus->contact_id = $_REQUEST['contact_id'];
}
if (isset($_REQUEST['parent_name']) && is_null($focus->parent_name)) 
{
	$focus->parent_name = $_REQUEST['parent_name'];
}
if (isset($_REQUEST['parent_id']) && is_null($focus->parent_id)) 
{
	$focus->parent_id = $_REQUEST['parent_id'];
}
if (isset($_REQUEST['parent_type'])) 
{
	$focus->parent_type = $_REQUEST['parent_type'];
}
if (isset($_REQUEST['filename']) && $_REQUEST['isDuplicate'] != 'true') 
{
        $focus->filename = $_REQUEST['filename'];
}
elseif (is_null($focus->parent_type)) 
{
	$focus->parent_type = $app_list_strings['record_type_default_key'];
}

$log->info("Email detail view");

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");


//Added to set the cc when click reply all
if(isset($_REQUEST['msg_cc']) && $_REQUEST['msg_cc'] != '')
{
        $smarty->assign("MAIL_MSG_CC", $_REQUEST['msg_cc']);
}

if($focus->mode == 'edit')
{
	$smarty->assign("UPDATEINFO",updateInfo($focus->id));
        $smarty->assign("MODE", $focus->mode);
}

// Unimplemented until jscalendar language files are fixed

$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

if(isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", $_REQUEST['return_module']);
else $smarty->assign("RETURN_MODULE",'Emails');
if(isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", $_REQUEST['return_action']);
else $smarty->assign("RETURN_ACTION",'index');
if(isset($_REQUEST['return_id'])) $smarty->assign("RETURN_ID", $_REQUEST['return_id']);
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", $_REQUEST['return_viewname']);


$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("JAVASCRIPT", get_set_focus_js().get_validate_record_js());
$smarty->assign("ID", $focus->id);
$smarty->assign("ENTITY_ID", $_REQUEST["record"]);
$smarty->assign("ENTITY_TYPE",$_REQUEST["email_directing_module"]);
$smarty->assign("OLD_ID", $old_id );

if(empty($focus->filename))
{
        $smarty->assign("FILENAME_TEXT", "");
        $smarty->assign("FILENAME", "");
}
else
{
        $smarty->assign("FILENAME_TEXT", "(".$focus->filename.")");
        $smarty->assign("FILENAME", $focus->filename);
}

if(isset($focus->parent_type) && $focus->parent_type != "") 
{
	$change_parent_button = "<input title='".$app_strings['LBL_CHANGE_BUTTON_TITLE']."' tabindex='2' accessKey='".$app_strings['LBL_CHANGE_BUTTON_KEY']."' type='button' class='button' value='".$app_strings['LBL_CHANGE_BUTTON_LABEL']."' name='button' LANGUAGE=javascript onclick='return window.open(\"index.php?module=\"+ document.EditView.parent_type.value + \"&action=Popup&html=Popup_picker&form=TasksEditView\",\"test\",\"width=600,height=400,resizable=1,scrollbars=1\");'>";
	$smarty->assign("CHANGE_PARENT_BUTTON", $change_parent_button);
}

$email_tables = Array('emails','crmentity','activity'); 
$tabid = getTabid("Emails");

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

$smarty->display("ComposeEmail.tpl");
?>
<script type="text/javascript" defer="1">
addOnloadEvent(function () {
var oFCKeditor = null;
oFCKeditor = new FCKeditor( "description" ) ;
oFCKeditor.BasePath  = "include/fckeditor/" ;
oFCKeditor.Height = 500;
oFCKeditor.Width = "100%";
oFCKeditor.ReplaceTextarea() ;
});
</script>
