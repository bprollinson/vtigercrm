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
 * $Header: /cvsroot/vtigercrm/vtiger_crm/themes/Aqua/layout_utils.php,v 1.18 2005/12/20 03:51:08 mangai Exp $
 * Description:  Contains a variety of utility functions used to display UI 
 * components such as form headers and footers.  Intended to be modified on a per 
 * theme basis.
 ********************************************************************************/

require_once('include/logging.php');
global $app_strings;
global $theme;
global $image_path;
$image_path = 'themes/'.$theme.'/images/';

$log = LoggerManager::getLogger('layout_utils');	

/**
 * Create HTML to display formatted form title of a form in the left pane
 * param $left_title - the string to display as the title in the header
 */
function get_left_form_header ($left_title) 
{
global $image_path;

$the_header = <<<EOQ
       <table width="100%" cellpadding="0" cellspacing="0" border="0"><tbody><tr>
		<td class="leftFormHeader" vAlign="middle" align="left" noWrap height="20">$left_title</td>
        </tr></tbody></table>
		<table width="100%" cellpadding="3" cellspacing="0" border="0" class="formOuterBorder"><tbody><tr><td align="left">
EOQ;

return $the_header;
}

/**
 * Create HTML to display formatted form footer of form in the left pane.
 */
function get_left_form_footer() {
return ("</td></tr></tbody></table>\n");
}

/**
 * Create HTML to display formatted form title.
 * param $form_title - the string to display as the title in the header
 * param $other_text - the string to next to the title.  Typically used for form buttons.
 * param $show_help - the boolean which determines if the print and help links are shown.
 */
function get_form_header ($form_title, $other_text, $show_help) 
{
global $image_path;
global $app_strings;

$the_form = <<<EOQ
<!--table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #000"><tbody><tr>
	  <td>
       <table cellpadding="0" cellspacing="0" border="0"><tbody><tr>
		<td vAlign="top" align="left" height="20"> 
         <IMG src="$image_path/header_start.gif" border="0"></td>
        <td class="formHeader" background="$image_path/header_tile.gif" vAlign="middle" align="left" noWrap width="100%" height="20">$form_title</td>
        <td vAlign="top" align="right" height="20">
	  	  <IMG src="$image_path/header_end.gif" border="0"></td>
		</tr></tbody></table>
      </td-->
EOQ;

if ($other_text) {
	$the_form .= "<td width='20'><IMG height='1' src='include/images/blank.gif'></td>\n";
	$the_form .= "<td width='100%' align='left' valign='bottom'>$other_text</td>\n";
}
else {
	$the_form .= "<td width='100%'><IMG height='1' src='include/images/blank.gif'></td>\n";
}

if ($show_help==true) {
     $the_form .= "<td class='bodySmall' align='right'>[ <A href='phprint.php?jt=".session_id().$GLOBALS['request_string']."'>".$app_strings['LNK_PRINT']."</A> ]</td>\n";
     $the_form .= "<td class='bodySmall' align='right'>[ <A href='http://www.vtiger.com/products/crm/document.html' target='_blank'>".$app_strings['LNK_HELP']."</A> ]</td>\n";
}

$the_form .= <<<EOQ
	  </tr>
</tbody></table>
EOQ;

return $the_form;
}

/**
 * Create HTML to display formatted form footer
 */
function get_form_footer() {

}

/**
 * Create HTML to display formatted module title.
 * param $module - the string to next to the title.  Typically used for form buttons.
 * param $module_title - the string to display as the module title
 * param $show_help - the boolean which determines if the print and help links are shown.
 */
function get_module_title ($module, $module_title, $show_help) {
global $image_path;
global $app_strings;

$the_title = "<table width='100%' cellpadding='0' cellspacing='0' border='0'><tbody><tr><td>\n";
$the_title .= "<table cellpadding='0' cellspacing='0' border='0'><tbody><tr>\n";
$the_title .= "<td vAlign='middle' align='center'>\n";
		
if (is_file($image_path.$module.".gif")) {
	$the_title .= "<IMG src='".$image_path.$module.".gif' border='0'>\n";
}

$the_title .= "</td><td class='moduleTitle' vAlign='middle' align='left' noWrap width='100%'>&nbsp;";
$the_title .= $module_title."</td></tr></tbody></table></td>\n";
$the_title .= "<td width='100%'><IMG height='1' src='include/images/blank.gif'></td>";

if ($show_help) {
//	$the_title .= "<td class='bodySmall' nowrap align='right'> <A href='phprint.php?jt=".session_id().$GLOBALS['request_string']."'><img align=absmiddle hspace=3 border=0 src='".$image_path."print.gif'>".$app_strings['LNK_PRINT']."</A> &nbsp;</td>\n";
//	$the_title .= "<td class='bodySmall' nowrap align='right'> <A href='http://www.vtiger.com/products/crm/document.html' target='_blank'><img align=absmiddle hspace=3 border=0 src='".$image_path."help_icon.gif'>".$app_strings['LNK_HELP']."</A></td>\n";
}
else {
	$the_title .= "<td class='bodySmall' align='right'>&nbsp;</td>\n";
	$the_title .= "<td class='bodySmall' align='right'>&nbsp;</td>\n";
}

$the_title .= "</tr><tr><td colspan='4' width='100%' class='hline'><IMG width='100%' height='2' src='".$image_path."blank.gif'></td>";
$the_title .= "</tr></tbody></table>\n";

return $the_title;

}

/**
 * Create a header for a popup.
 * param $theme - The name of the current theme
 */
function insert_popup_header($theme)
{
global $app_strings, $default_charset;
$charset = $default_charset;

if(isset($app_strings['LBL_CHARSET']))
{
	$charset = $app_strings['LBL_CHARSET'];
}

$out  = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
$out .=	'<HTML><HEAD>';
$out .=	'<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'">';
$out .=	'<title>'.$app_strings['LBL_BROWSER_TITLE'].'</title>';
$out .=	'<style type="text/css">@import url("themes/'.$theme.'/style.css"); </style>';
$out .=	'</HEAD><BODY leftMargin="5" topMargin="5" MARGINHEIGHT="0" MARGINWIDTH="0">';

echo $out;
}

/**
 * Create a footer for a popup.
 */
function insert_popup_footer()
{
echo <<< EOQ
	</BODY>
	</HTML>
EOQ;
}

?>
