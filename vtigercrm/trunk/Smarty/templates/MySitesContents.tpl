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
<!-- BEGIN: main -->
<table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td colwidth=90% align=left class=small>
		<table border=0 cellspacing=0 cellpadding=5>
		<tr>
			<td align=left><a href="#"><img src="{$IMAGE_PATH}webmail_settings.gif" align="absmiddle" border=0 /></a></td>
			<td class=small align=left><a href="#" onclick="fetchContents('manage');">{$MOD.LBL_MANAGE_SITES}</a></td>
		</tr>
		</table>
			
	</td>
	<td align=right width=10%>
		<table border=0 cellspacing=0 cellpadding=0>
		<tr><td nowrap class="componentName">{$MOD.LBL_MY_SITES}</td></tr>
		</table>
	</td>
</tr>
</table>

<table border=0 cellspacing=0 cellpadding=5 width=100% class="mailSubHeader">
<tr>
<td nowrap align=left>{$MOD.LBL_BOOKMARK_LIST} : </span></td>
<td align=left width=90% >
	<select id="urllist" name="urllist" style="width: 99%;" class="small" onChange="setSite(this);">
	{foreach item=portaldetails key=sno from=$PORTALS}
	<option value="{$portaldetails.portalurl}">{$portaldetails.portalname}</option>
	{/foreach}
	</select>
</td>
</tr>
<tr>
	<td bgcolor="#ffffff" colspan=2>
		<iframe id="locatesite" src="{$DEFAULT_URL}" frameborder="0" height="350" scrolling="auto" width="100%"></iframe>
	</td>
</tr>
</table>

