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
 * $Header$
 * Description:  returns HTML for client-side image map.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/utils/utils.php');
require_once('include/logging.php');
require_once("modules/Potentials/Charts.php");
require_once('include/ComboUtil.php');
global $app_list_strings, $current_language, $tmp_dir, $currentModule, $action;
$current_module_strings = return_module_language($current_language, 'Dashboard');

$log = LoggerManager::getLogger('pipeline_by_sales_stage');

if (isset($_REQUEST['pbss_refresh'])) { $refresh = $_REQUEST['pbss_refresh']; }
else { $refresh = false; }

// added for auto refresh
$refresh = true;
//

// Get _dom Arrays from Database
$comboFieldNames = Array('sales_stage'=>'sales_stage_dom');
$comboFieldArray = getComboArray($comboFieldNames);

//get the dates to display
if (isset($_SESSION['pbss_date_start']) && $_SESSION['pbss_date_start'] != '' && !isset($_REQUEST['pbss_date_start'])) {
	$date_start = $_SESSION['pbss_date_start'];
	$log->debug("_SESSION['pbss_date_start'] is:");
	$log->debug($_SESSION['pbss_date_start']);
}
elseif (isset($_REQUEST['pbss_date_start']) && $_REQUEST['pbss_date_start'] != '') {
	$date_start = $_REQUEST['pbss_date_start'];
	$current_user->setPreference('pbss_date_start', $_REQUEST['pbss_date_start']);
	$log->debug("_REQUEST['pbss_date_start'] is:");
	$log->debug($_REQUEST['pbss_date_start']);
	$log->debug("_SESSION['pbss_date_start'] is:");
	$log->debug($_SESSION['pbss_date_start']);
}
else {
	$date_start = "2001-01-01";
}

if (isset($_SESSION['pbss_date_end']) && $_SESSION['pbss_date_end'] != '' && !isset($_REQUEST['pbss_date_end'])) {
	$date_end = $_SESSION['pbss_date_end'];
	$log->debug("_SESSION['pbss_date_end'] is:");
	$log->debug($_SESSION['pbss_date_end']);
}
elseif (isset($_REQUEST['pbss_date_end']) && $_REQUEST['pbss_date_end'] != '') {
	$date_end = $_REQUEST['pbss_date_end'];
	$current_user->setPreference('pbss_date_end', $_REQUEST['pbss_date_end']);
	$log->debug("_REQUEST['pbss_date_end'] is:");
	$log->debug($_REQUEST['pbss_date_end']);
	$log->debug("_SESSION['pbss_date_end'] is:");
	$log->debug($_SESSION['pbss_date_end']);
}
else {
	$date_end = '2100-01-01';
}

$tempx = array();
$datax = array();
//get list of sales stage keys to display
if (isset($_SESSION['pbss_sales_stages']) && count($_SESSION['pbss_sales_stages']) > 0 && !isset($_REQUEST['pbss_sales_stages'])) {
	$tempx = $_SESSION['pbss_sales_stages'];
	$log->debug("_SESSION['pbss_sales_stages'] is:");
	$log->debug($_SESSION['pbss_sales_stages']);
}
elseif (isset($_REQUEST['pbss_sales_stages']) && count($_REQUEST['pbss_sales_stages']) > 0) {
	$tempx = $_REQUEST['pbss_sales_stages'];
	$current_user->setPreference('pbss_sales_stages', $_REQUEST['pbss_sales_stages']);
	$log->debug("_REQUEST['pbss_sales_stages'] is:");
	$log->debug($_REQUEST['pbss_sales_stages']);
	$log->debug("_SESSION['pbss_sales_stages'] is:");
	$log->debug($_SESSION['pbss_sales_stages']);
}

//set $datax using selected sales stage keys 
if (count($tempx) > 0) {
	foreach ($tempx as $key) {
		$datax[$key] = $comboFieldArray['sales_stage_dom'][$key];
	}
}
else {
	$datax = $comboFieldArray['sales_stage_dom'];
}
$log->debug("datax is:");
$log->debug($datax);

$ids = array();
//get list of user ids for which to display data
if (isset($_SESSION['pbss_ids']) && count($_SESSION['pbss_ids']) != 0 && !isset($_REQUEST['pbss_ids'])) {
	$ids = $_SESSION['pbss_ids'];
	$log->debug("_SESSION['pbss_ids'] is:");
	$log->debug($_SESSION['pbss_ids']);
}
elseif (isset($_REQUEST['pbss_ids']) && count($_REQUEST['pbss_ids']) > 0) {
	$ids = $_REQUEST['pbss_ids'];
	$current_user->setPreference('pbss_ids', $_REQUEST['pbss_ids']);
	$log->debug("_REQUEST['pbss_ids'] is:");
	$log->debug($_REQUEST['pbss_ids']);
	$log->debug("_SESSION['pbss_ids'] is:");
	$log->debug($_SESSION['pbss_ids']);
}
else {
	$ids = get_user_array(false);
	$ids = array_keys($ids);
}

//create unique prefix based on selected users for image files
$id_hash = '';
if (isset($ids)) {
	sort($ids);
	$id_hash = crc32(implode('',$ids));
}
$log->debug("ids is:");
$log->debug($ids);

$cache_file_name = $id_hash."_pipeline_".$current_language."_".crc32(implode('',$datax)).$date_start.$date_end.".png";
$log->debug("cache file name is: $cache_file_name");

if (substr(phpversion(), 0, 1) == "5") { // php5 }
	echo "<em>Charts not supported in PHP 5.</em>";
}
else {
$draw_this = new jpgraph();
echo $draw_this->pipeline_by_sales_stage($datax, $date_start, $date_end, $ids, $tmp_dir.$cache_file_name, $refresh);
echo "<P><font size='1'><em>".$current_module_strings['LBL_SALES_STAGE_FORM_DESC']."</em></font></P>";
if (isset($_REQUEST['pbss_edit']) && $_REQUEST['pbss_edit'] == 'true') {
	$cal_lang = "en";
	$cal_dateformat = parse_calendardate($app_strings['NTC_DATE_FORMAT']);
	$cal_dateformat = '%Y-%m-%d'; // fix providedd by Jlee for date bug in Dashboard

?>
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-<?php echo $cal_lang ?>.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<form name='pipeline_by_sales_stage' action="index.php" method="post" >
<input type="hidden" name="module" value="<?php echo $currentModule;?>">
<input type="hidden" name="action" value="<?php echo $action;?>">
<input type="hidden" name="pbss_refresh" value="true">
<table cellpadding="2" border="0"><tbody>
<tr>


<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_START']?> <br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>

<td valign='top' ><input class="text" name="pbss_date_start" size='12' maxlength='10' id='date_start' value='<?php if (isset($_SESSION['pbss_date_start'])) echo $_SESSION['pbss_date_start']?>'>  <img src="themes/<?php echo $theme ?>/images/calendar.gif" id="date_start_trigger"> </td>
</tr><tr>
<tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_DATE_END'];?><br><em><?php echo $app_strings['NTC_DATE_FORMAT']?></em></td>
<td valign='top' ><input class="text" name="pbss_date_end" size='12' maxlength='10' id='date_end' value='<?php if (isset($_SESSION['pbss_date_end'])) echo $_SESSION['pbss_date_end']?>'>  <img src="themes/<?php echo $theme ?>/images/calendar.gif" id="date_end_trigger"> </td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_SALES_STAGES'];?></td>
<td valign='top' ><select name="pbss_sales_stages[]" multiple size='3'><?php echo get_select_options_with_id($comboFieldArray['sales_stage_dom'],$_SESSION['pbss_sales_stages']); ?></select></td>
</tr><tr>
<td valign='top' nowrap><?php echo $current_module_strings['LBL_USERS'];?></td>
<td valign='top' ><select name="pbss_ids[]" multiple size='3'><?php echo get_select_options_with_id(get_user_array(false),$_SESSION['pbss_ids']); ?></select></td>
</tr><tr>
<td align="right"><br /> <input class="button" onclick="return verify_chart_data(pipeline_by_sales_stage);" type="submit" title="<?php echo $app_strings['LBL_SELECT_BUTTON_TITLE']; ?>" accessKey="<?php echo $app_strings['LBL_SELECT_BUTTON_KEY']; ?>" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL']?>" /></td>
</tr></table>
</form>
<script type="text/javascript">
Calendar.setup ({
	inputField : "date_start", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_start_trigger", singleClick : true, step : 1
});
Calendar.setup ({
	inputField : "date_end", ifFormat : "<?php echo $cal_dateformat ?>", showsTime : false, button : "date_end_trigger", singleClick : true, step : 1
});
</script>

<?php } 
else {
	if (file_exists($tmp_dir.$cache_file_name)) {
		$file_date = getDisplayDate(date('Y-m-d H:i', filemtime($tmp_dir.$cache_file_name)));
	}
	else {
		$file_date = '';
	}
?>
<div align=right><FONT size='1'>
<em><?php  echo $current_module_strings['LBL_CREATED_ON'].' '.$file_date; ?> 
</em>[<a href="index.php?module=<?php echo $currentModule;?>&action=<?php echo $action;?>&pbss_refresh=true"><?php echo $current_module_strings['LBL_REFRESH'];?></a>]
[<a href="index.php?module=<?php echo $currentModule;?>&action=<?php echo $action;?>&pbss_edit=true"><?php echo $current_module_strings['LBL_EDIT'];?></a>]
</FONT></div>
<?php } 
//echo get_validate_chart_js();
}?>
