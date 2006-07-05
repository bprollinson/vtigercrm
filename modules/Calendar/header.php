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
global $theme,$app_strings;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$html_string = '<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>

<tr><td style="height:2px"></td></tr>
<tr>
	<td width="10%" style="padding-left:10px;padding-right:30px" class="moduleName" nowrap>'.$app_strings["My Home Page"].' > <a class="hdrLink" href="index.php?action=index&module=Calendar&parenttab=My Home Page">'.$app_strings["Calendar"].'</a></td>

	<td  nowrap width="8%">
		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td class="sep1" style="width:1px;"></td>
			<td class=small>
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
					 <td style="padding-left:10px"><img src="'.$image_path.'btnL3Add-Faded.gif" border=0></td>
					 <td style="padding-right:10px"><img src="'.$image_path.'btnL3Search-Faded.gif" border=0></td>
		</tr>
		</table>
	</td>
			</tr>
			</table>
	</td>
	<td width="20">&nbsp;</td>
                <td class="small" width="10%" align="left">

				<table border=0 cellspacing=0 cellpadding=5>

				<tr>
					<td style="padding-right:0px;padding-left:10px;"><a href="javascript:;" onClick=\'fnvshobj(this,"miniCal");getMiniCal();\'><img src="'.$image_path.'btnL3Calendar.gif" alt="'.$app_strings['LBL_CALENDAR_ALT'].'" title="'.$app_strings['LBL_CALENDAR_TITLE'].'" border=0></a></a></td>
					<td style="padding-right:0px"><a href="javascript:;"><img src="'.$image_path.'btnL3Clock.gif" alt="'.$app_strings['LBL_CLOCK_ALT'].'" title="'.$app_strings['LBL_CLOCK_TITLE'].'" border=0 onClick="fnvshobj(this,\'wclock\');"></a></a></td>
					<td style="padding-right:0px"><a href="#"><img src="'.$image_path.'btnL3Calc.gif" alt="'.$app_strings['LBL_CALCULATOR_ALT'].'" title="'.$app_strings['LBL_CALCULATOR_TITLE'].'" border=0 onClick="fnvshobj(this,\'calculator_cont\');fetch_calc();"></a></td>
					<td style="padding-right:10px"><a href="javascript:;" onClick=\'return window.open("index.php?module=Contacts&action=vtchat","Chat","width=450,height=400,resizable=1,scrollbars=1");\'><img src="'.$image_path.'tbarChat.gif" alt="'.$app_strings['LBL_CHAT_ALT'].'" title="'.$app_strings['LBL_CHAT_TITLE'].'" border=0></a></td>
					<td style="padding-right:10px"><img src="'.$image_path.'btnL3Tracker.gif" alt="'.$app_strings['LBL_LAST_VIEWED'].'" title="'.$app_strings['LBL_LAST_VIEWED'].'" border=0 onClick="fnvshobj(this,\'tracker\');"></td>
				</tr>
				</table>
	</td>
	<td width="20">&nbsp;</td>
               <td class="small" align="left" width="5%">

		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
				<td style="padding-right:0px;padding-left:10px;"><img src="'.$image_path.'tbarImport-Faded.gif" alt="{$APP.LBL_IMPORT} {$APP.$MODULE}" title="{$APP.LBL_IMPORT} {$APP.$MODULE}" border="0"></td>
                <td style="padding-right:10px"><img src="'.$image_path.'tbarExport-Faded.gif" alt="{$APP.LBL_EXPORT} {$APP.$MODULE}" title="{$APP.LBL_EXPORT} {$APP.$MODULE}" border="0"></td>
			</tr>
		</table>	
	</td>
	<td width="20">&nbsp;</td>
                <td class="small" align="left">	
				<table border=0 cellspacing=0 cellpadding=5>
				<tr>
				<td style="padding-left:10px;"><a href="javascript:;" onmouseout="fninvsh(\'allMenu\');" onclick="fnvshobj(this,\'allMenu\')"><img src="'.$image_path.'btnL3AllMenu.gif" alt="{$APP.LBL_ALL_MENU_ALT}" title="{$APP.LBL_ALL_MENU_TITLE}" border="0"></a></td>
				</tr>
				</table>
	</td>			
	</tr>
	</table></td>
	</tr>
	<tr><td style="height:2px"></td></tr>
	</TABLE>';
	echo $html_string;
?>	
