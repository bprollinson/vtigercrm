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

$idlist = $_REQUEST['idlist'];
$id_array=explode(';', $idlist);

for($i=0;$i < count($id_array)-1;$i++)
{
		$sql = "delete from emailtemplates where templateid =".$id_array[$i];
		$adb->query($sql);
}

header("Location:index.php?module=Users&action=listemailtemplates");


?>


