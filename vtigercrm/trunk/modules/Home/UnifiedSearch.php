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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Home/UnifiedSearch.php,v 1.4 2005/02/21 07:02:49 jack Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/logging.php');
require_once('modules/Contacts/Contact.php');
require_once('modules/Accounts/Account.php');
require_once('modules/Potentials/Opportunity.php');
require_once('modules/Leads/Lead.php');
require_once('modules/Faq/Faq.php');
require_once('modules/Vendors/Vendor.php');
require_once('modules/PriceBooks/PriceBook.php');
require_once('modules/Quotes/Quote.php');
require_once('modules/PurchaseOrder/PurchaseOrder.php');
require_once('modules/SalesOrder/SalesOrder.php');
require_once('modules/Invoice/Invoice.php');
require_once('modules/Campaigns/Campaign.php');
require_once('modules/Home/language/en_us.lang.php');
require_once('include/database/PearDatabase.php');
require_once('modules/CustomView/CustomView.php');

require_once('Smarty_setup.php');
global $mod_strings;

$total_record_count = 0;
//echo get_module_title("", "Search Results", true); 
if(isset($_REQUEST['query_string']) && preg_match("/[\w]/", $_REQUEST['query_string'])) {

	//module => object
	$object_array = Array(
				'Potentials'=>'Potential',
				'Accounts'=>'Account',
				'Contacts'=>'Contact',
				'Leads'=>'Lead',
				'Notes'=>'Note',
				'Activities'=>'Activity',
				'Emails'=>'Email',
				'HelpDesk'=>'HelpDesk',
				'Products'=>'Product',
				'Faq'=>'Faq',
				//'Events'=>'',
				'Vendors'=>'Vendor',
				'PriceBooks'=>'PriceBook',
				'Quotes'=>'Quote',
				'PurchaseOrder'=>'Order',
				'SalesOrder'=>'SalesOrder',
				'Invoice'=>'Invoice',
				'Campaigns'=>'Campaign'
			     );
	global $adb;
	global $current_user;
	global $theme;
	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";

	$search_val = $_REQUEST['query_string'];
	$search_module = $_REQUEST['search_module'];

	getSearchModulesComboList($search_module);

	foreach($object_array as $module => $object_name)
	{
		$focus = new $object_name();

		$smarty = new vtigerCRM_Smarty;

		require_once("modules/$module/language/en_us.lang.php");
		global $mod_strings;
		global $app_strings;

		$smarty->assign("MOD", $mod_strings);
		$smarty->assign("APP", $app_strings);
		$smarty->assign("IMAGE_PATH",$image_path);
		$smarty->assign("MODULE",$module);
		$smarty->assign("SEARCH_MODULE",$_REQUEST['search_module']);
		$smarty->assign("SINGLE_MOD",$module);

	
		$listquery = getListQuery($module);
		//Avoided the modules Faq and PriceBooks. we should remove this if when change the customview function
		$oCustomView = '';
		if($module != 'Faq' && $module != 'PriceBooks')
		{
			//Added to get the default 'All' customview query
			$oCustomView = new CustomView($module);
			$viewid = $oCustomView->getViewId($module);

			$listquery = $oCustomView->getModifiedCvListQuery($viewid,$listquery,$module);
		}
		
		if($search_module != '')//This is for Tag search
		{
		
			$where = getTagWhere($search_val,$current_user->id);
			$search_msg =  $app_strings['LBL_TAG_SEARCH'];				       	$search_msg .=	"<b>".$search_val."</b>";
		}
		else			//This is for Global search
		{
			$where = getUnifiedWhere($listquery,$module,$search_val);
			$search_msg = $app_strings['LBL_SEARCH_RESULTS_FOR'];
			$search_msg .=	"<b>".$search_val."</b>";
		}

		if($where != '')
			$listquery .= ' and ('.$where.')';
		
		$list_result = $adb->query($listquery);
		$noofrows = $adb->num_rows($list_result);

		if($noofrows >= 1)
			$list_max_entries_per_page = $noofrows;
		//Here we can change the max list entries per page per module
		$navigation_array = getNavigationValues(1, $noofrows, $list_max_entries_per_page);

		$listview_header = getListViewHeader($focus,$module,"","","","global",$oCustomView);
		$listview_entries = getListViewEntries($focus,$module,$list_result,$navigation_array,"","","","",$oCustomView);

		//Do not display the Header if there are no entires in listview_entries
		if(count($listview_entries) > 0)
		{
			$display_header = 1;
		}
		else
		{
			$display_header = 0;
		}
		
		$smarty->assign("LISTHEADER", $listview_header);
		$smarty->assign("LISTENTITY", $listview_entries);
		$smarty->assign("DISPLAYHEADER", $display_header);
		$smarty->assign("HEADERCOUNT", count($listview_header));

		$total_record_count = $total_record_count + $noofrows;

		$smarty->assign("SEARCH_CRITERIA","( $noofrows )".$search_msg);
		$smarty->assign("MODULES_LIST", $object_array);

		$smarty->display("GlobalListView.tpl");
		unset($_SESSION['lvs'][$module]);
	}

	//Added to display the Total record count
?>
	<script>
document.getElementById("global_search_total_count").innerHTML = " <? echo $app_strings['LBL_TOTAL_RECORDS_FOUND'] ?><b><?php echo $total_record_count; ?></b>";
	</script>
<?php

}
else {
	echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>".$mod_strings['ERR_ONE_CHAR']."</em>";
}

/**	Function to get the where condition for a module based on the field table entries
  *	@param  string $listquery  -- ListView query for the module 
  *	@param  string $module     -- module name
  *	@param  string $search_val -- entered search string value
  *	@return string $where      -- where condition for the module based on field table entries
  */
function getUnifiedWhere($listquery,$module,$search_val)
{
	global $adb;

	$query = "SELECT * FROM vtiger_field WHERE tabid = ".getTabid($module);
	$result = $adb->query($query);
	$noofrows = $adb->num_rows($result);

	$where = '';
	for($i=0;$i<$noofrows;$i++)
	{
		$columnname = $adb->query_result($result,$i,'columnname');
		$tablename = $adb->query_result($result,$i,'tablename');

		//Before form the where condition, check whether the table for the field has been added in the listview query
		if(strstr($listquery,$tablename))
		{
			if($where != '')
				$where .= " OR ";
				$where .= $tablename.".".$columnname." LIKE ".$adb->quote("%$search_val%");
		}
	}

	return $where;
}

/**	Function to get the Tags where condition
  *	@param  string $search_val -- entered search string value
  *	@param  string $current_user_id     -- current user id
  *	@return string $where      -- where condition with the list of crmids, will like vtiger_crmentity.crmid in (1,3,4,etc.,)
  */
function getTagWhere($search_val,$current_user_id)
{
	require_once('include/freetag/freetag.class.php');

	$freetag_obj = new freetag();

	$crmid_array = $freetag_obj->get_objects_with_tag_all($search_val,$current_user_id);

	$where = '';
	if(count($crmid_array) > 0)
	{
		$where = " vtiger_crmentity.crmid IN (";
		foreach($crmid_array as $index => $crmid)
		{
			$where .= $crmid.',';
		}
		$where = trim($where,',').')';
	}

	return $where;
}


/**	Function to get the the List of Searchable Modules as a combo list which will be displayed in right corner under the Header
  *	@param  string $search_module -- search module, this module result will be shown defaultly 
  */
function getSearchModulesComboList($search_module)
{
	global $object_array;
	global $app_strings;
	global $mod_strings;
	
	?>
		<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
		<script>
		function displayModuleList(selectmodule_view)
		{
			<?php
			foreach($object_array as $module => $object_name)
			{
				?>
				mod = "global_list_"+"<?php echo $module; ?>";
				if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value == "All")
					show(mod);
				else
					hide(mod);
				<?php
			}
			?>
			
			if(selectmodule_view.options[selectmodule_view.options.selectedIndex].value != "All")
			{
				selectedmodule="global_list_"+selectmodule_view.options[selectmodule_view.options.selectedIndex].value;
				show(selectedmodule);
			}
		}
		</script>
		 <table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		     <tr>
		        <td colspan="3" id="global_search_total_count" style="padding-left:30px">&nbsp;</td>
		<td nowrap align="right"><? echo $app_strings['LBL_SHOW_RESULTS'] ?>&nbsp;
		                <select id="global_search_module" name="global_search_module" onChange="displayModuleList(this);">
			<option value="All"><? echo $app_strings['COMBO_ALL'] ?></option>
						<?php
						foreach($object_array as $module => $object_name)
						{
							$selected = '';
							if($search_module != '' && $module == $search_module)
								$selected = 'selected';
							if($search_module == '' && $module == 'All')
								$selected = 'selected';
							?>
							<option value="<?php echo $module; ?>" <?php echo $selected; ?> ><?php echo $app_strings[$module]; ?></option>
							<?php
						}
						?>
		     		</select>
		        </td>
		     </tr>
		</table>
	<?php
}
?>
