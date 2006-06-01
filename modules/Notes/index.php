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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Notes/index.php,v 1.3 2005/03/17 15:42:56 samk Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once ($theme_path."layout_utils.php");
global $mod_strings;

echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_TITLE'], true); 
echo "\n<BR>\n";
include ('modules/Notes/ListView.php');

echo "<br><table width='250' cellpadding=0 cellspacing=0><tr><td>";
echo get_form_header($mod_strings['LBL_TOOL_FORM_TITLE'], "", false);
echo "</td></tr>";
echo "<tr><td class='formOuterBorder' style='padding: 10px'>";
echo "<ul>";
include('modules/Import/ImportButton.php');
echo "</ul>";
echo "</td></tr></table>";
 
?>
