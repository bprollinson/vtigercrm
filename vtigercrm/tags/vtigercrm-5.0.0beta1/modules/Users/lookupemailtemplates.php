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
require_once('include/utils/utils.php');

global $theme;
$theme_path="themes/".$theme."/";

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="en">
<head>
  <title><?php echo $mod_strings['LBL_EMAIL_TEMPLATES_LIST']; ?></title>
  <link type="text/css" rel="stylesheet" href="<?php echo $theme_path ?>/style.css"/>
</head>
<body>
            <form action="index.php">
	     <div class="lvtHeaderText"><?php echo $mod_strings['LBL_EMAIL_TEMPLATES']; ?></div>
		<hr noshade="noshade" size="1">
		
             <input type="hidden" name="module" value="Users">
		<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="100%">
		<tr>
		<th width="35%" class="lvtCol"><b><?php echo $mod_strings['LBL_TEMPLATE_NAME']; ?></b></th>
                <th width="65%" class="lvtCol"><b><?php echo $mod_strings['LBL_DESCRIPTION']; ?></b></td>
                </tr>
<?php
   $sql = "select * from emailtemplates order by templateid desc";
   $result = $adb->query($sql);
   $temprow = $adb->fetch_array($result);
$cnt=1;

require_once('include/utils/UserInfoUtil.php');
do
{
  printf("<tr class='lvtColData' onmouseover=\"this.className='lvtColDataHover'\" onmouseout=\"this.className='lvtColData'\" bgcolor='white'> <td height='25'>");
 $templatename = $temprow["templatename"]; 
  echo "<a href='javascript:;' onclick=\"submittemplate(".$temprow['templateid'].");\">".$temprow["templatename"]."</a></td>";
   printf("<td height='25'>%s</td>",$temprow["description"]);
  $cnt++;
}while($temprow = $adb->fetch_array($result));
?>
</table>
</body>
<script>
function submittemplate(templateid)
{
	window.opener.location.href = window.opener.location.href +'&templateid='+templateid;
	self.close();
}
</script>
</html>
