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
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('modules/Import/ImportContact.php');
require_once('modules/Import/ImportAccount.php');
require_once('modules/Import/ImportOpportunity.php');
require_once('modules/Import/ImportLead.php');
require_once('modules/Import/UsersLastImport.php');
require_once('modules/Import/parse_utils.php');
require_once('include/ListView/ListView.php');
require_once('modules/Contacts/Contact.php');
require_once('include/utils/utils.php');

global $mod_strings;
global $app_list_strings;
global $app_strings;
global $current_user;

if (! isset( $_REQUEST['module']))
{
	$_REQUEST['module'] = 'Home';
}

if (! isset( $_REQUEST['return_id']))
{
	$_REQUEST['return_id'] = '';
}
if (! isset( $_REQUEST['return_module']))
{
	$_REQUEST['return_module'] = '';
}

if (! isset( $_REQUEST['return_action']))
{
	$_REQUEST['return_action'] = '';
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$log->info("Import Step last");

$parenttab = getParenttab();
//This Buttons_List1.tpl is is called to display the add, search, import and export buttons ie., second level tabs
$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMP", $import_mod_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);

$smarty->assign("MODULE", $_REQUEST['modulename']);
$smarty->assign("SINGLE_MOD", $_REQUEST['modulename']);
$smarty->assign("CATEGORY", $_SESSION['import_parenttab']);
//@session_unregister("import_parenttab");

$smarty->display("Buttons_List1.tpl");


if ( isset($_REQUEST['message']))
{
	?>
	<br>

	<table align="center" cellpadding="5" cellspacing="0" width="95%" class="mailClient importLeadUI small">
	   <tr>
		<td height="50" valign="middle" align="left" class="mailClientBg genHeaderSmall">
			 <?php echo $mod_strings['LBL_MODULE_NAME']; ?> <?php echo $app_strings[$_REQUEST['modulename']]; ?> 
		</td>
	   </tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr>
		<td align="left"  style="padding-left:40px;width:75%;" >
			<span class="genHeaderGray"><?php echo $mod_strings['LBL_STEP_3_3']; ?></span>&nbsp; 
			<span class="genHeaderSmall"><?php echo $mod_strings['LBL_MAPPING_RESULTS']; ?></span>
		</td>
	   </tr>	
	   <tr>
		<td style="padding-left:140px;">
			<?php 
				echo $_REQUEST['message']; 
			?>
		 <br><br><br> 		 </td>
       </tr>
	   <tr>
	     <td class="reportCreateBottom" >
		 <table width="100%" border="0" cellpadding="5" cellspacing="0" >
           <tr>
             <td align="right" valign="top"><form enctype="multipart/form-data" name="Import" method="POST" action="index.php" style="float:right; ">
                 <input type="hidden" name="module" value="<?php echo $_REQUEST['modulename']; ?>">
                 <input type="hidden" name="action" value="Import">
                 <input type="hidden" name="step" value="1">
                 <input type="hidden" name="return_id" value="<?php echo $_REQUEST['return_id']; ?>">
                 <input type="hidden" name="return_module" value="<?php echo $_REQUEST['return_module']; ?>">
                 <input type="hidden" name="return_action" value="<?php echo (($_REQUEST['return_action'] != '')?$_REQUEST['return_action']:'index'); ?>">
                 <input type="hidden" name="parenttab" value="<?php echo $parenttab; ?>">
                 <input title="<?php echo $mod_strings['LBL_FINISHED'] ?>" accessKey="" class="crmbutton small save" type="submit" name="button" value="  <?php echo $mod_strings['LBL_FINISHED'] ?>  "  onclick="this.form.action.value=this.form.return_action.value;this.form.return_module.value=this.form.return_module.value;return true;">
                 <input title="<?php echo $mod_strings['LBL_IMPORT_MORE'] ?>" accessKey="" class="crmbutton small save" type="submit" name="button" value="  <?php echo $mod_strings['LBL_IMPORT_MORE'] ?>  "  onclick="this.form.return_module.value=this.form.module.value; return true;">
             </form>
			 <form name="Import" method="POST" action="index.php" style="float:right; ">
                 <input type="hidden" name="module" value="<?php echo $_REQUEST['modulename']; ?>">
                 <input type="hidden" name="action" value="Import">
                 <input type="hidden" name="step" value="undo">
                 <input type="hidden" name="return_module" value="<?php echo $_REQUEST['return_module']; ?>">
                 <input type="hidden" name="return_id" value="<?php echo $_REQUEST['return_id']; ?>">
                 <input type="hidden" name="return_action" value="<?php echo $_REQUEST['return_action']; ?>">
                 <input type="hidden" name="parenttab" value="<?php echo $parenttab; ?>">
                 <input title="<?php echo $mod_strings['LBL_UNDO_LAST_IMPORT']; ?>" accessKey="" class="crmbutton small cancel" type="submit" name="button" value="  <?php echo $mod_strings['LBL_UNDO_LAST_IMPORT'] ?>  ">
             </form></td>
           </tr>
         </table></td>
      </tr>
	</table>
	<?php 
}

echo "<br><br>";

$currentModule = "Import";

global $limit;
global $list_max_entries_per_page;

$implict_account = false;

$import_modules_array = Array(
				"Leads"=>"Lead",
				"Accounts"=>"Account",
				"Contacts"=>"Contact",
				"Potentials"=>"Potential",
				"Products"=>"Product" 
			     );

foreach($import_modules_array as $module_name => $object_name)
{

	$seedUsersLastImport = new UsersLastImport();
	$seedUsersLastImport->bean_type = $module_name;
	$list_query = $seedUsersLastImport->create_list_query($o,$w);
	$current_module_strings = return_module_language($current_language, $module_name);

	$object = new $object_name();
	$seedUsersLastImport->list_fields = $object->list_fields;

	$list_result = $adb->query($list_query);
	//Retreiving the no of rows
	$noofrows = $adb->num_rows($list_result);

	if($noofrows>=1) 
	{
		if($module_name != 'Accounts')
		{
			$implict_account=true;
		}

		if($module_name == 'Accounts' && $implict_account==true)
			$display_header_msg = "Newly created Accounts";
		else
			$display_header_msg = "".$mod_strings['LBL_LAST_IMPORTED']." ".$app_strings[$module_name]."";
		
		//Display the Header Message	
		echo "
			<table width='100%' border='0' cellpadding='5' cellspacing='0'>
			   <tr>
				<td class='dvtCellLabel' align='left'>
					<b>".$mod_strings['LBL_LAST_IMPORTED']." ".$app_strings[$module_name]." </b>
				</td>
			   </tr>
			</table>
		      ";

		$smarty = new vtigerCRM_Smarty;

		$smarty->assign("MOD", $mod_strings);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("IMAGE_PATH",$image_path);
		$smarty->assign("MODULE",$module_name);
		$smarty->assign("SINGLE_MOD",$module_name);
		$smarty->assign("SHOW_MASS_SELECT",'false');

		//Retreiving the start value from request
		if($module_name == $_REQUEST['nav_module'] && isset($_REQUEST['start']) && $_REQUEST['start'] != '')
		{
			$start = $_REQUEST['start'];
		}
		else
		{
			$start = 1;
		}

		$info_message='&recordcount='.$_REQUEST['recordcount'].'&noofrows='.$_REQUEST['noofrows'].'&message='.$_REQUEST['message'].'&skipped_record_count='.$_REQUEST['skipped_record_count'];
		$url_string = '&modulename='.$_REQUEST['modulename'].'&nav_module='.$module_name.$info_message;
		$viewid = '';

		//Retreive the Navigation array
		$navigation_array = getNavigationValues($start, $noofrows, $list_max_entries_per_page);
		$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string,"Import","ImportSteplast",$viewid);

		//Retreive the List View Header and Entries
		$listview_header = getListViewHeader($object,$module_name);
		$listview_entries = getListViewEntries($object,$module_name,$list_result,$navigation_array,"","","EditView","Delete","");

		$smarty->assign("NAVIGATION", $navigationOutput);
		$smarty->assign("HIDE_CUSTOM_LINKS", 1);//Added to hide the CustomView links in imported records ListView
		$smarty->assign("LISTHEADER", $listview_header);
		$smarty->assign("LISTENTITY", $listview_entries);

		$smarty->display("ListViewEntries.tpl");
		echo "<BR>";
	}
}

?>
