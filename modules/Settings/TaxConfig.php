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

require_once('Smarty_setup.php');
global $mod_strings;
global $app_strings;
global $adb;
global $log;

$smarty = new vtigerCRM_Smarty;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$tax_details = getAllTaxes();
$sh_tax_details = getAllTaxes('sh');

//To save the edited value
if($_REQUEST['save_tax'] == 'true')
{
	for($i=0;$i<count($tax_details);$i++)
	{
		$new_percentages[$tax_details[$i]['taxid']] = $_REQUEST[$tax_details[$i]['taxname']];
	}
	updateTaxPercentages($new_percentages);
	$getlist = true;
}
elseif($_REQUEST['sh_save_tax'] == 'true')
{
	for($i=0;$i<count($sh_tax_details);$i++)
	{
		$new_percentages[$sh_tax_details[$i]['taxid']] = $_REQUEST[$sh_tax_details[$i]['taxname']];
	}
	updateTaxPercentages($new_percentages,'sh');
	$getlist = true;
}

//To edit
if($_REQUEST['edit_tax'] == 'true')
{
	$smarty->assign("EDIT_MODE", 'true');
}
elseif($_REQUEST['sh_edit_tax'] == 'true')
{
	$smarty->assign("SH_EDIT_MODE", 'true');
}

//To add tax
if($_REQUEST['add_tax_type'] == 'true')
{
	//Add the given tax name and value as a new tax type
	addTaxType($_REQUEST['addTaxLabel'],$_REQUEST['addTaxValue']);
	$getlist = true;
}
elseif($_REQUEST['sh_add_tax_type'] == 'true')
{
	addTaxType($_REQUEST['sh_addTaxLabel'],$_REQUEST['sh_addTaxValue'],'sh');
	$getlist = true;
}

//To Disable ie., delete or enable
if(($_REQUEST['disable'] == 'true' || $_REQUEST['enable'] == 'true') && $_REQUEST['taxname'] != '')
{
	if($_REQUEST['disable'] == 'true')
		changeDeleted($_REQUEST['taxname'],1);
	else
		changeDeleted($_REQUEST['taxname'],0);
	$getlist = true;
}
elseif(($_REQUEST['sh_disable'] == 'true' || $_REQUEST['sh_enable'] == 'true') && $_REQUEST['sh_taxname'] != '')
{
	if($_REQUEST['sh_disable'] == 'true')
		changeDeleted($_REQUEST['sh_taxname'],1,'sh');
	else
		changeDeleted($_REQUEST['sh_taxname'],0,'sh');
	$getlist = true;
}

//after done save or enable/disable or added new tax the list will be retrieved again from db
if($getlist)
{
	$tax_details = getAllTaxes();
	$sh_tax_details = getAllTaxes('sh');
}

$smarty->assign("TAX_COUNT", count($tax_details));
$smarty->assign("SH_TAX_COUNT", count($sh_tax_details));

if(count($tax_details) == 0)
	$smarty->assign("TAX_COUNT", 0);
if(count($sh_tax_details) == 0)
	$smarty->assign("SH_TAX_COUNT", 0);
	
$smarty->assign("TAX_VALUES", $tax_details);
$smarty->assign("SH_TAX_VALUES", $sh_tax_details);

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("APP", $app_strings);
$smarty->assign("MOD", $mod_strings);
$smarty->display("Settings/TaxConfig.tpl");


/**	Function to update the list of Tax percentages for the passed tax types
 *	@param array $new_percentages - array of tax types and the values like [taxid]=new value ie., [1]=3.56, [2]=11.45
 *      @param string $sh - sh or empty, if sh passed then update will be done in shipping and handling related table
 *      @return void
 */
function updateTaxPercentages($new_percentages, $sh='')
{
	global $adb, $log;
	$log->debug("Entering into the function updateTaxPercentages");

	$tax_percentage = Array();

	foreach($new_percentages as $taxid => $new_val)
	{
		if($new_val != '')
		{
			if($sh != '' && $sh == 'sh')
				$query = "update vtiger_shippingtaxinfo set percentage = \"$new_val\" where taxid=\"$taxid\"";
			else
				$query = "update vtiger_inventorytaxinfo set percentage = \"$new_val\" where taxid=\"$taxid\"";
			$adb->query($query);
		}
	}

	$log->debug("Exiting from the function updateTaxPercentages");
}

/**	Function used to add the tax type which will do database alterations
 *	@param string $taxlabel - tax label name to be added
 *	@param string $taxvalue - tax value to be added
 *      @param string $sh - sh or empty , if sh passed then the tax will be added in shipping and handling related table
 *      @return void
 */
function addTaxType($taxlabel, $taxvalue, $sh='')
{
	global $adb, $log;
	$log->debug("Entering into function addTaxType($taxlabel, $taxvalue, $sh)");

	if($sh != '' && $sh == 'sh')
		$query = "alter table vtiger_inventoryshippingrel add column $taxlabel int(19) default NULL";
	else
		$query = "alter table vtiger_inventoryproductrel add column $taxlabel int(19) default NULL";

	$res = $adb->query($query);
	if($res)
	{
		if($sh != '' && $sh == 'sh')
			$query1 = "insert into vtiger_shippingtaxinfo values('','".$taxlabel."','".$taxvalue."',0)";
		else
			$query1 = "insert into vtiger_inventorytaxinfo values('','".$taxlabel."','".$taxvalue."',0)";

		$res1 = $adb->query($query1);
	}
	
	$log->debug("Exit from function addTaxType($taxlabel, $taxvalue)");
	if($res1)
		return '';
	else
		return "There may be some problem in adding the Tax type. Please try again";
}

/**	Function used to Enable or Disable the tax type 
 *	@param string $taxname - taxname to enable or disble
 *	@param int $deleted - 0 or 1 where 0 to enable and 1 to disable
 *	@param string $sh - sh or empty, if sh passed then the enable/disable will be done in shipping and handling tax table ie.,vtiger_shippingtaxinfo  else this enable/disable will be done in Product tax table ie., in vtiger_inventorytaxinfo
 *	@return void
 */
function changeDeleted($taxname, $deleted, $sh='')
{
	global $log, $adb;
	$log->debug("Entering into function changeDeleted($taxname, $deleted, $sh)");

	if($sh == 'sh')
		$adb->query("update vtiger_shippingtaxinfo set deleted=$deleted where taxname=\"$taxname\"");
	else
		$adb->query("update vtiger_inventorytaxinfo set deleted=$deleted where taxname=\"$taxname\"");
	$log->debug("Exit from function changeDeleted($taxname, $deleted, $sh)");
}

?>
