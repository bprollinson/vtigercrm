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
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/CommonUtils.php');

$idlist = $_REQUEST['idlist'];
$viewid = $_REQUEST['viewname'];
$returnmodule=$_REQUEST['return_module'];
//split the string and store in an array
$storearray = explode(";",$idlist);
array_filter($storearray);
$ids_list = array();
foreach($storearray as $id)
{
        if(isPermitted($returnmodule,'Delete',$id) == 'yes')
        {
                $sql="update crmentity set deleted=1 where crmid='" .$id ."'";
                $result = $adb->query($sql);
        }
        else
        {
                $ids_list[] = $id;
        }
}
$ret = getEntityName($returnmodule,$ids_list);
if(count($ret) > 0)
{
       $errormsg = implode(',',$ret);
}else
{
       $errormsg = '';
}

if(isset($_REQUEST['smodule']) && ($_REQUEST['smodule']!=''))
{
	$smod = "&smodule=".$_REQUEST['smodule'];
}
if($returnmodule == 'Emails')
{
	if(isset($_REQUEST['folderid']) && $_REQUEST['folderid'] != '')
	{
		$folderid = $_REQUEST['folderid'];
	}else
	{
		$folderid = 1;
	}
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&folderid=".$folderid."&ajax=delete&file=ListView");
}
elseif($returnmodule!='Faq')
{
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&ajax=delete&file=ListView&viewname=".$viewid);
}
else
{
	header("Location: index.php?module=".$returnmodule."&action=".$returnmodule."Ajax&ajax=delete&file=ListView");
}
?>

