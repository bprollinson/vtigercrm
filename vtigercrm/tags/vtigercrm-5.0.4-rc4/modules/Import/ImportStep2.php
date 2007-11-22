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
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('Smarty_setup.php');
require_once('modules/Import/ImportLead.php');
require_once('modules/Import/ImportAccount.php');
require_once('modules/Import/ImportContact.php');
require_once('modules/Import/ImportOpportunity.php');
require_once('modules/Import/ImportProduct.php');
require_once('modules/Import/Forms.php');
require_once('modules/Import/parse_utils.php');
require_once('modules/Import/ImportMap.php');
//Pavani: Import this file to Support Imports for Trouble tickets and vendors
require_once('modules/Import/ImportTicket.php');
require_once('modules/Import/ImportVendors.php');
require_once('include/database/PearDatabase.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/CommonUtils.php');

@session_unregister('column_position_to_field');
@session_unregister('totalrows');
@session_unregister('recordcount');
@session_unregister('startval');
@session_unregister('return_field_count');
$_SESSION['totalrows'] = '';
$_SESSION['recordcount'] = 500;
$_SESSION['startval'] = 0;

global $mod_strings;
global $mod_list_strings;
global $app_strings;
global $app_list_strings;
global $current_user;
global $import_file_name;
global $upload_maxsize;

global $import_dir;
$focus = 0;
$delimiter = ',';
$max_lines = 3;

$has_header = 0;

if ( isset($_REQUEST['has_header']))
{
	$has_header = 1;
}

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

if (!is_uploaded_file($_FILES['userfile']['tmp_name']) )
{
	show_error_import($mod_strings['LBL_IMPORT_MODULE_ERROR_NO_UPLOAD']);
	exit;
}
else if ($_FILES['userfile']['size'] > $upload_maxsize)
{
	show_error_import( $mod_strings['LBL_IMPORT_MODULE_ERROR_LARGE_FILE'] . " ". $upload_maxsize. " ". $mod_strings['LBL_IMPORT_MODULE_ERROR_LARGE_FILE_END']);
	exit;
}
if( !is_writable( $import_dir ))
{
	show_error_import($mod_strings['LBL_IMPORT_MODULE_NO_DIRECTORY'].$import_dir.$mod_strings['LBL_IMPORT_MODULE_NO_DIRECTORY_END']);
	exit;
}

$tmp_file_name = $import_dir. "IMPORT_".$current_user->id;

move_uploaded_file($_FILES['userfile']['tmp_name'], $tmp_file_name);


// Now parse the file and look for errors
$ret_value = 0;

if ($_REQUEST['source'] == 'act')
{
	$ret_value = parse_import_act($tmp_file_name,$delimiter,$max_lines,$has_header);
} 
else
{
	$ret_value = parse_import($tmp_file_name,$delimiter,$max_lines,$has_header);
}

if ($ret_value == -1)
{
	show_error_import( $mod_strings['LBL_CANNOT_OPEN'] );
	exit;
} 
else if ($ret_value == -2)
{
	show_error_import( $mod_strings['LBL_NOT_SAME_NUMBER'] );
	exit;
}
else if ( $ret_value == -3 )
{
	show_error_import( $mod_strings['LBL_NO_LINES'] );
	exit;
}

$rows = $ret_value['rows'];
$ret_field_count = $ret_value['field_count'];

$smarty =  new vtigerCRM_Smarty;

$smarty->assign("TMP_FILE", $tmp_file_name );
$smarty->assign("SOURCE", $_REQUEST['source'] );

$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

if(isset($_REQUEST['return_module'])) $smarty->assign("RETURN_MODULE", $_REQUEST['return_module']);
if(isset($_REQUEST['return_action'])) $smarty->assign("RETURN_ACTION", $_REQUEST['return_action']);

$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);

$smarty->assign("HEADER", $app_strings['LBL_IMPORT']." ". $mod_strings['LBL_MODULE_NAME']);
$smarty->assign("HASHEADER", $has_header);

$import_object_array = Array(
				"Leads"=>"ImportLead",
				"Accounts"=>"ImportAccount",
				"Contacts"=>"ImportContact",
				"Potentials"=>"ImportOpportunity",
				"Products"=>"ImportProduct",
				"HelpDesk"=>"ImportTicket",
                                "Vendors"=>"ImportVendors"
			    );

if(isset($_REQUEST['module']) && $_REQUEST['module'] != '')
{
	$object_name = $import_object_array[$_REQUEST['module']];
	$focus = new $object_name();
}
else
{
	$focus = new ImportContact();
}


$total_num_rows=sizeof($rows);	
$firstrow = $rows[0];
if($total_num_rows >1 )
{
	$secondrow = $rows[1];
}		
if($total_num_rows >2)
{
	$thirdrow = $rows[2];
}

//If the cell value is very large then UI mapping will be collpased. So we will display partial text
foreach($firstrow as $ind => $val)
{
	if(strlen($val) > 30)
		$firstrow[$ind] = substr($val,0,30)." ..........";
}
if (isset($secondrow)) { //Asha: Fix for ticket #4432
	foreach($secondrow as $ind => $val)
	{
		if(strlen($val) > 30)
			$secondrow[$ind] = substr($val,0,30)." ..........";
	}
	if (isset($thirdrow)) { //Asha: Fix for ticket #4432
		foreach($thirdrow as $ind => $val)
		{
			if(strlen($val) > 30)
				$thirdrow[$ind] = substr($val,0,30)." ..........";
		}
	}
}

$field_map = $outlook_contacts_field_map;

$mapping_file = new ImportMap();
$saved_map_lists = $mapping_file->getSavedMappingsList($_REQUEST['return_module']);
$map_list_combo = '<select name="source" id="saved_source" disabled onchange="getImportSavedMap(this)">';
$map_list_combo .= '<OPTION value="-1" selected>--Select--</OPTION>';
if(is_array($saved_map_lists))
{
	foreach($saved_map_lists as $mapid => $mapname)
	{
		$map_list_combo .= '<OPTION value='.$mapid.'>'.$mapname.'</OPTION>';
	}
}
$map_list_combo .= '</select>';
//This link is Delete link for the selected mapping
$map_list_combo .= "&nbsp;&nbsp;&nbsp;<span id='delete_mapping' style='visibility:hidden;'><a href='javascript:; deleteMapping();'>Del</a></span>";
$smarty->assign("SAVED_MAP_LISTS",$map_list_combo);


if ( count($mapping_arr) > 0)
{
	$field_map = &$mapping_arr;
}
else if ($_REQUEST['source'] == 'other')
{
	if ($_REQUEST['module'] == 'Contacts')
	{
		$field_map = $outlook_contacts_field_map;
	} 
	else if ($_REQUEST['module'] == 'Accounts')
	{
		$field_map = $outlook_accounts_field_map;
	}
	else if ($_REQUEST['module'] == 'Potentials')
	{
		$field_map = $salesforce_opportunities_field_map;
	}
} 


$add_one = 1;
$start_at = 0;

if($has_header)
{
	$add_one = 0;
	$start_at = 1;
} 

for($row_count = $start_at; $row_count < count($rows); $row_count++ )
{
	$smarty->assign("ROWCOUNT", $row_count + $add_one);
}

$list_string_key = strtolower($_REQUEST['module']);
$list_string_key .= "_import_fields";

//Now we are getting the import fields from DB instead of hard coded array $mod_list_strings
$translated_column_fields = getImportFieldsList($_REQUEST['module']);//$mod_list_strings[$list_string_key];

// adding custom vtiger_fields translations
//getCustomFieldTrans($_REQUEST['module'],&$translated_column_fields);

$cnt=1;
for($field_count = 0; $field_count < $ret_field_count; $field_count++)
{

	$smarty->assign("COLCOUNT", $field_count + 1);
	$suggest = "";

	/*
	if ($has_header && isset( $field_map[$firstrow[$field_count]] ) )
	{
		$suggest = $field_map[$firstrow[$field_count]];	
	}
	else if (isset($field_map[$field_count]))
	{
		$suggest = $field_map[$field_count];	
	}
	*/

	if($_REQUEST['module']=='Accounts')
	{
		$tablename='account';
		$focus1=new Accounts();
	}
	if($_REQUEST['module']=='Contacts')
	{
		$tablename='contactdetails';
		$focus1=new Contacts();
 	}
	if($_REQUEST['module']=='Leads')
 	{
		$tablename='leaddetails';
		$focus1=new Leads();
	}
	if($_REQUEST['module']=='Potentials')
 	{
		$tablename='potential';
		$focus1=new Potentials();
	}
	if($_REQUEST['module']=='Products')
 	{
 		$tablename='products';
 		$focus1=new Products();
 	}
	//Pavani: checking for HelpDesk
        if($_REQUEST['module']=='HelpDesk')
        {
                $tablename='troubletickets';
                $focus1=new HelpDesk();
        }
        //end checking

	
	$smarty->assign("FIRSTROW",$firstrow);
	$smarty->assign("SECONDROW",$secondrow);
	$smarty->assign("THIRDROW",$thirdrow);
	$smarty_array[$field_count + 1] = getFieldSelect(	$focus->importable_fields,
							$field_count,
							$focus1->required_fields,
							$suggest,
							$translated_column_fields,
							$tablename
						   );

	$pos = 0;

	foreach ( $rows as $row ) 
	{
		
		if( isset($row[$field_count]) && $row[$field_count] != '')
		{
			$smarty->assign("CELL",htmlspecialchars($row[$field_count]));
//			$smarty->parse("main.table.row.cell");
		} 
		else
		{
//			$smarty->parse("main.table.row.cellempty");
		}

		$cnt++;
	}
}
@session_unregister('import_has_header');
@session_unregister('import_firstrow');
@session_unregister('import_field_map');
@session_unregister('import_module_object_column_fields');
@session_unregister('import_module_field_count');
@session_unregister('import_module_object_required_fields');
@session_unregister('import_module_translated_column_fields');
$_SESSION['import_has_header'] = $has_header;
$_SESSION['import_firstrow'] = $firstrow;
$_SESSION['import_field_map'] = $field_map;
$_SESSION['import_module_object_column_fields'] = $focus->importable_fields;
$_SESSION['import_module_field_count'] = $field_count;
$_SESSION['import_module_object_required_fields'] = $focus1->required_fields;
$_SESSION['import_module_translated_column_fields'] = $translated_column_fields;


//echo '<pre>Default array ==> '; print_r($smarty_array); echo '</pre>';

$smarty->assign("SELECTFIELD",$smarty_array);
$smarty->assign("ROW", $row);

$module_key = "LBL_".strtoupper($_REQUEST['module'])."_NOTE_";

for ($i = 1;isset($mod_strings[$module_key.$i]);$i++)
{
	$smarty->assign("NOTETEXT", $mod_strings[$module_key.$i]);
}

if($has_header)
{
	$smarty->assign("HAS_HEADER", 'on');
} 
else
{
	$smarty->assign("HAS_HEADER", 'off');
}

$smarty->assign("MODULE", $_REQUEST['module']);
$smarty->assign('CATEGORY' , $_REQUEST['parenttab']);
@$_SESSION['import_parenttab'] = $_REQUEST['parenttab'];
$smarty->assign("JAVASCRIPT2", get_readonly_js() );

$smarty->display('ImportStep2.tpl');

?>
<script language="javascript" type="text/javascript">
function validate_import_map()
{
	var tagName;
	var count = 0;
	var field_count = "<?php echo $field_count; ?>";
	var required_fields = new Array();
	var required_fields_name = new Array();
	var seq_string = '';

	<?php 
		foreach($focus->required_fields as $name => $index)
		{
			?>
			required_fields[count] = "<?php echo $name; ?>";
			required_fields_name[count] = "<?php echo $translated_column_fields[$name]; ?>";
			count = count + 1;
			<?php 
		} 
	?>		
	for(loop_count = 0; loop_count<field_count;loop_count++)
	{
		tagName = document.getElementById('colnum'+loop_count);
		optionData = tagName.options[tagName.selectedIndex].value;

		if(optionData != -1)
		{
			tmp = seq_string.indexOf("\""+optionData+"\"");
			if(tmp == -1)
			{
				seq_string = seq_string + "\""+optionData+"\"";
			}
			else
			{
				//if a vtiger_field mapped more than once, alert the user and return
				alert("'"+tagName.options[tagName.selectedIndex].text+"<?php echo $mod_strings['PLEASE_CHECK_MAPPING']?>");
				return false;
			}
		}

	}

	//check whether the mandatory vtiger_fields have been mapped.
	for(inner_loop = 0; inner_loop<required_fields.length;inner_loop++)
	{
		if(seq_string.indexOf(required_fields[inner_loop]) == -1)
		{
			alert('<?php echo $mod_strings['MAP_MANDATORY_FIELD']?>'+required_fields_name[inner_loop]+'"');
			return false;
		}
	}

	//This is to check whether the save map name has been given or not when save map check box is checked
	if(document.getElementById("save_map").checked == true)
	{
		if(trim(document.getElementById("save_map_as").value) == '')
		{
			alert("<?php echo $mod_strings['ENTER_SAVEMAP_NAME'] ?>");
			return false;
		}
	}

	return true;
}
</script>
