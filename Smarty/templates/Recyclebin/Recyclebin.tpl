{*<!--

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

-->*}
<script language="JavaScript" type="text/javascript" src="include/js/search.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
{include file='Buttons_List.tpl'}
                                <div id="searchingUI" style="display:none;">
                                        <table border=0 cellspacing=0 cellpadding=0 width=100%>
                                        <tr>
                                                <td align=center>
                                                <img src="{'searching.gif'|@vtiger_imageurl:$THEME}" alt="{$APP.LBL_SEARCHING}"  title="{$APP.LBL_SEARCHING}">
                                                </td>
                                        </tr>
                                        </table>

                                </div>
                        </td>
                </tr>
                </table>
        </td>
</tr>
</table>

{*<!-- Contents -->*}

<table border=0  cellspacing=0 cellpadding=0 width=98% align=center>

<tr><td valign=top><img src="{'showPanelTopLeft.gif'|@vtiger_imageurl:$THEME}"></td>
	<td class="showPanelBg" valign="top" width=100% style="padding:10px;">

		<form name="basicSearch" action="index.php" onsubmit="return false;">
		<div id="searchAcc" style="z-index:1;display:none;position:relative;">
			<table width="80%" cellpadding="5" cellspacing="0"  class="searchUIBasic small" align="center" border=0>
				<tr>
					<td class="searchUIName small" nowrap align="left">
						<span class="moduleName">{$APP.LBL_SEARCH}</span><br>		
					</td>
					<td class="small" nowrap align=right><b>{$APP.LBL_SEARCH_FOR}</b></td>
					<td class="small"><input type="text"  class="txtBox" style="width:120px" name="search_text"></td>
					<td class="small" nowrap><b>{$APP.LBL_IN}</b>&nbsp;</td>
					<td class="small" nowrap>
						<div id="basicsearchcolumns_real">
							<select name="search_field" id="bas_searchfield" class="txtBox" style="width:150px">
							{html_options  options=$SEARCHLISTHEADER }
							</select>
						</div>
						<input type="hidden" name="searchtype" value="BasicSearch">
						<input type="hidden" name="module" value="{$SELECTED_MODULE}">
						<input type="hidden" name="parenttab" value="{$CATEGORY}">
						<input type="hidden" name="action" value="index">
						<input type="hidden" name="query" value="true">
						<input type="hidden" name="search_cnt">
					</td>
					<td class="small" nowrap colspan=2>
						<input name="submit" type="button" class="crmbutton small create" onClick="callRBSearch('Basic');" value=" {$APP.LBL_SEARCH_NOW_BUTTON} ">&nbsp;
					
					</td>
					<td class="small" valign="top" onMouseOver="this.style.cursor='pointer';" onclick="moveMe('searchAcc');searchshowhide('searchAcc','')">[x]</td>
				</tr>
				<tr>
					<td colspan="7" align="center" class="small">
						<table border=0 cellspacing=0 cellpadding=0 width=100%>
							<tr>
								{$ALPHABETICAL}
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		</form>

{*<!-- Searching UI -->*}

	  <div id="modules_datas" class="small" style="width:100%;position:relative;">
			{include file="Recyclebin/RecyclebinContents.tpl"}
	</div>
</tr></td>


</div>
</td>
</tr>
</table>
</td>
</tr>
</table>

	</td>
        <td valign="top"><img src="{'showPanelTopRight.gif'|@vtiger_imageurl:$THEME}"></td>
   </tr>
</tbody>
</table>

