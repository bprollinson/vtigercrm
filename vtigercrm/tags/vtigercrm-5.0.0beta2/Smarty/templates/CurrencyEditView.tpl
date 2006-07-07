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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
			{include file='SetMenu.tpl'}
			<!-- DISPLAY -->
			<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
			<form action="index.php" method="post" name="index" id="form">
			<input type="hidden" name="module" value="Settings">
			<input type="hidden" name="parenttab" value="{$PARENTTAB}">
			<input type="hidden" name="action" value="index">
			<input type="hidden" name="record" value="{$ID}">
			<tr>
				<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}currency.gif" alt="Users" width="48" height="48" border=0 title="Users"></td>
				<td class="heading2" valign="bottom" ><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > <a href="index.php?module=Settings&action=CurrencyListView&parenttab=Settings">{$MOD.LBL_CURRENCY_SETTINGS}</a> > {$MOD.LBL_EDIT} &quot;{$CURRENCY_NAME}&quot; </b></td>
			</tr>
			<tr>
				<td valign=top class="small">{$MOD.LBL_CURRENCY_DESCRIPTION}</td>
			</tr>
			</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>{$MOD.LBL_SETTINGS} {$APP.LBL_FOR} &quot;{$CURRENCY_NAME}&quot;  </strong></td>
						<td class="small" align=right>
							<input title="{$APP.LBL_SAVE_BUTTON_LABEL}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="crmButton small save" onclick="this.form.action.value='SaveCurrencyInfo'; return validate()" type="submit" name="button" value="{$APP.LBL_SAVE_BUTTON_LABEL}" >&nbsp;&nbsp;
							<input title="{$APP.LBL_CANCEL_BUTTON_LABEL}>" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="crmButton small cancel" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
						</td>
					</tr>
					</table>
					
			<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
			<tr>
			<td class="small" valign=top >
			<table width="100%"  border="0" cellspacing="0" cellpadding="5">
			  <tr>
                            <td width="20%" nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_NAME}</strong></td>
                            <td width="80%" class="small cellText"><input type="text" class="detailedViewTextBox small" value="{$CURRENCY_NAME}" name="currency_name"></td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CODE}</strong></td>
                            <td class="small cellText"><input type="text" class="detailedViewTextBox small" value="{$CURRENCY_CODE}" name="currency_code"></td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_SYMBOL}</strong></td>
                            <td class="small cellText"><input type="text" class="detailedViewTextBox small" value="{$CURRENCY_SYMBOL}" name="currency_symbol"></td>
                          </tr>
                          <tr valign="top">
                            <td nowrap class="small cellLabel"><font color="red">*</font><strong>{$MOD.LBL_CURRENCY_CRATE}</strong></td>

                            <td class="small cellText"><input type="text" class="detailedViewTextBox small" value="{$CONVERSION_RATE}" name="conversion_rate"></td>
                          </tr>
                          <tr>
                            <td nowrap class="small cellLabel"><strong>{$MOD.LBL_CURRENCY_STATUS}</strong></td>
                            <td class="small cellText">
				<select name="currency_status" {$STATUS_DISABLE} class="importBox">
					<option value="Active"  {$ACTSELECT}>{$MOD.LBL_ACTIVE}</option>
		        	        <option value="Inactive" {$INACTSELECT}>{$MOD.LBL_INACTIVE}</option>
              	                </select>
			    </td>
                          </tr>	
                        </table>
						
						</td>
					  </tr>
					</table>
					<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr>
					  <td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
		
	</div>
</td>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
   </tr>
</tbody>
</table>
{literal}
<script>
        function validate() {
                if (!emptyCheck("currency_name","Currency Name","text")) return false
                        if (!emptyCheck("currency_code","Currency Code","text")) return false
                                if (!emptyCheck("currency_symbol","Currency Symbol","text")) return false
                                        if (!emptyCheck("conversion_rate","Conversion Rate","text")) return false
                                                if (!emptyCheck("currency_status","Currency Status","text")) return false
                                                        return true;

        }
</script>
{/literal}
