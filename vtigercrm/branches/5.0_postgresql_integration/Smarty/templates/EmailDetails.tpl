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
<table width="100%" border="0" cellpadding="0" cellspacing="0" valign="top">
<tr>
    <td class="forwardBg">
  		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr>
						{if $BLOCKS neq ''}
			<td width="75%">
						  <input type="button" name="forward" value=" {$MOD.LBL_FORWARD_BUTTON} " class="classWebBtn" onClick=OpenCompose('{$ID}','forward')>&nbsp;
						  <input type="button" name="Send" value=" {$MOD.LBL_SEND} " class="classWebBtn" onClick=OpenCompose('{$ID}','edit')>&nbsp;
						{foreach item=row from=$BLOCKS}	
						{foreach item=elements key=title from=$row}	
						{if $title eq 'Attachment'}
							{if $elements.value ne ''}
								<input type="button" name="download" value=" {$MOD.LBL_DOWNLOAD_ATTCH_BUTTON} " class="classWebBtn" onclick="fnvshobj(this,'reportLay')"/>
							{/if}
						{/if}
						{/foreach}
						{/foreach}
			</td>
						<td width="25%" align="right"><input type="button" name="Button" value=" {$APP.LBL_DELETE_BUTTON} "  class="classWebBtn" onClick="DeleteEmail('{$ID}')"/></td>
						{else}
						<td colspan="2">&nbsp;</td>
						{/if}
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td height="300" bgcolor="#FFFFFF" valign="top" style="padding-top:10px;">
	{foreach item=row from=$BLOCKS}	
	{foreach item=elements key=title from=$row}	
		{if $title eq 'Subject'}
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td width="20%" align="right"><b>{$MOD.LBL_TO}</b></td><td width="2%">&nbsp;</td><td>{$TO_MAIL}&nbsp;</td></tr>
	<tr><td align="right">{$MOD.LBL_CC}</td><td>&nbsp;</td><td>&nbsp;{$CC_MAIL}</td></tr>
	<tr><td align="right">{$MOD.LBL_BCC}</td><td>&nbsp;</td><td>&nbsp;{$BCC_MAIL}</td></tr>
	<tr><td align="right"><b>{$MOD.LBL_SUBJECT}</b></td><td>&nbsp;</td><td>{$elements.value}</td></tr>
			<tr><td align="right" style="border-bottom:1px solid #666666;" colspan="3">&nbsp;</td></tr>
		</table>
		{elseif $title eq 'Description'}
		<div>
			{$BLOCKS.4.Description.value}
		</div>
		{/if}
	{/foreach}
	{/foreach}
	</td>
</tr>
</table>
{foreach item=row from=$BLOCKS}	
	{foreach item=elements key=title from=$row}	
	{if $title eq 'Attachment'}
	<div id="reportLay" onmouseout="fninvsh('reportLay')" onmouseover="fnvshNrm('reportLay')">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
		<tr>
			<td>
				{$elements.value}
			</td>
		</tr>
		<tr><td style="padding:5px;">&nbsp;</td></tr>
		</table>
	</div>
	{/if}
	{/foreach}
{/foreach}

