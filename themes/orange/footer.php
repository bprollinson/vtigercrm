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
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/themes/orange/footer.php,v 1.10 2005/03/02 20:08:13 jack Exp $
 * Description:  Contains a variety of utility functions used to display UI 
 * components such as form headers and footers.  Intended to be modified on a per 
 * theme basis.
 ********************************************************************************/
global $app_strings;
?>
<!--end body panes-->
</td></tr>
<tr><td colspan="2" align="center">
	<table CELLSPACING=3 border=0><tr>
      <td align=center noWrap colSpan=4>
      <?php
	      $counter = 1;
	      $sep = '';
	      foreach($moduleList as $module_name)
	      {
		      echo $sep.'<a href="index.php?module='.$module_name.'&action=index">'.$app_list_strings['moduleList'][$module_name].'</a>';
		      $sep = ' | ';
		      if($counter == 11) {
			      echo "<br />";
			      $counter = 0;
			      $sep = '';
		      }
		      $counter++;
	      }

      ?>
	  </td>
    </tr></table>
</td></tr></table>
</body>
</html>
