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
require_once('Smarty_setup.php');
require_once('include/utils/utils.php');
require_once('modules/Products/Product.php');
require_once('include/utils/utils.php');

$focus = new Product();

if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
	//Display the error message
	if($_SESSION['image_type_error'] != '')
	{
		echo '<font color="red">'.$_SESSION['image_type_error'].'</font>';
		session_unregister('image_type_error');
	}

	$focus->retrieve_entity_info($_REQUEST['record'],"Products");
	$focus->id = $_REQUEST['record'];
	$focus->name=$focus->column_fields['productname'];		
}

if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
        $focus->id = "";
}

global $app_strings,$currentModule;
global $mod_strings;

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);

if (isset($focus->name)) $smarty->assign("NAME", $focus->name);
else $smarty->assign("NAME", "");

$smarty->assign("BLOCKS", getBlocks($currentModule,"detail_view",'',$focus->column_fields));
$category = getParentTab();
$smarty->assign("CATEGORY",$category);
$smarty->assign("UPDATEINFO",updateInfo($focus->id));


$smarty->assign("CUSTOMFIELD", $cust_fld);
$smarty->assign("SINGLE_MOD",$app_strings['Product']);

if(isPermitted("Products","EditView",$_REQUEST['record']) == 'yes')
	$smarty->assign("EDIT_DUPLICATE","permitted");

if(isPermitted("Products","Delete",$_REQUEST['record']) == 'yes')
	$smarty->assign("DELETE","permitted");

$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);
$smarty->assign("ID", $_REQUEST['record']);

//Added to display the Tax informations
$vat_tax = getProductTaxPercentage('VAT',$focus->id);
$sales_tax = getProductTaxPercentage('Sales',$focus->id);
$service_tax = getProductTaxPercentage('Service',$focus->id);

$smarty->assign("VAT_TAX", $vat_tax);
$smarty->assign("SALES_TAX", $sales_tax);
$smarty->assign("SERVICE_TAX", $service_tax);

$check_button = Button_Check($module);
$smarty->assign("CHECK", $check_button);

 $product_tables = Array('products','productcf','productcollaterals');
 $validationData = getDBValidationData($product_tables);
 $data = split_validationdataArray($validationData);
 $smarty->assign("VALIDATION_DATA_FIELDNAME",$data['fieldname']);
 $smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$data['datatype']);
 $smarty->assign("VALIDATION_DATA_FIELDLABEL",$data['fieldlabel']);

//Security check for related list
$smarty->assign("MODULE", $currentModule);
$smarty->display("InventoryDetailView.tpl");

?>
