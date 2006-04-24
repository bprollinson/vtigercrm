{*
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/ *}
				<table width="100%"><tr>
					<td align="right" valign="top" width="5%" rowspan="2"><img src="{$IMAGE_PATH}two.gif" width="29" height="31" /></td>
					<td>
						<b class="lvtHeaderText">Custom Fields in {$MODULE}</b>
					</td>
				</tr>
				<tr>
					<td>
						<form action="index.php" method="post" name="new" id="form">
						<input type="hidden" name="fld_module" value="{$MODULE}">
						<input type="hidden" name="module" value="Settings">
						<input type="hidden" name="parenttab" value="Settings">
						<input type="hidden" name="mode">
						<input type="hidden" name="action">
						<table width="95%" border="0" cellpadding="5" cellspacing="0">
							<tr><td align="right"><input type="button" value=" New Custom Field " onClick="getCreateCustomFieldForm('{$MODULE}','','','')" class="classBtn"/></td></tr>
						</table>
						<table style="background-color: rgb(204, 204, 204);" class="small" border="0" cellpadding="5" cellspacing="1" width="95%">
						<tbody>
							<tr>
								<td class="lvtCol" width="5%">#</td>
							        <td class="lvtCol" width="35%">Field Label </td>
							        <td class="lvtCol" width="50%">Field Type </td>
								<td class="lvtCol" width="10%">Tools</td>
							</tr>
							{foreach item=entries key=id from=$CFENTRIES}
							<tr class="lvtColData" onmouseover="this.className='lvtColDataHover'" onmouseout="this.className='lvtColData'" bgcolor="white">
								{foreach item=value from=$entries}
									<td nowrap>{$value}</td>
								{/foreach}
							</tr>
							{/foreach}
						</tbody>
						</table><br />
						{if $MODULE eq 'Leads'}
						<table width="35%" style="border:1px dashed #CCCCCC;background-color:#FFFFEC;" cellpadding="5" cellspacing="0">
							<tr>
								<td style="padding:5px;" width="5%" >
								<img src="{$IMAGE_PATH}mapping.gif" align="absmiddle" /> </td>
								<td><span  class="genHeaderSmall">Field Mapping</span><br />
								Field Mapping allows you to ....
								</td>
							</tr>
							<tr><td colspan="2" align="right"><input input title="{$MOD.CUSTOMFIELDMAPPING}"  class="classBtn" onclick="this.form.action.value='LeadCustomFieldMapping';" type="submit" name="ListLeadCustomFieldMapping" value="{$MOD.CUSTOMFIELDMAPPING}"></td></tr>
						</table> 
						{/if}
					</form>
					</td>
				</tr></table>
{*<div id="orgLay" style="top:175px;left:275px; ">
	{include file='CreateCustomField.tpl'}
</div>*}

