<?php
/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): mmbrich
 ********************************************************************************/
      
require_once('modules/CustomView/CustomView.php');
require_once('user_privileges/default_module_view.php');

global $singlepane_view;
$cvObj = new CustomView($_REQUEST["return_type"]);

$listquery = getListQuery($_REQUEST["list_type"]);
$rs = $adb->query($cvObj->getModifiedCvListQuery($_REQUEST["cvid"],$listquery,$_REQUEST["list_type"]));

if($_REQUEST["list_type"] == "Leads")
		$reltable = "vtiger_campaignleadrel";
elseif($_REQUEST["list_type"] == "Contacts")
		$reltable = "vtiger_campaigncontrel";

while($row=$adb->fetch_array($rs)) {
	$adb->query("INSERT INTO ".$reltable." VALUES('".$_REQUEST["return_id"]."','".$row["crmid"]."')");
}

if ($singlepane_view == 'true')
{
?>
<script>
addOnloadEvent(function() {
	window.location.href = "index.php?action=DetailView&module=Campaigns&record=<? echo $_REQUEST['return_id'];?>";
});
</script>
<?php
}
else
{
?>
<script>
addOnloadEvent(function() {
	window.location.href = "index.php?action=CallRelatedList&module=Campaigns&record=<? echo $_REQUEST['return_id'];?>";
});
</script>
<?php
}
?>
