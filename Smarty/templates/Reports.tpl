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

{*<!-- module header -->*}
<script language="JavaScript" type="text/javascript" src="modules/Reports/Report.js"></script>

<TABLE border=0 cellspacing=0 cellpadding=0 width=100% class=small>
	<tr><td style="height:2px"></td></tr>
	<tr>
	<td style="padding-left:10px;padding-right:10px" class="moduleName" nowrap>{$APP.$CATEGORY} > {$APP.$MODULE}</td>
	<td class="sep1" style="width:1px"></td>
	<td class=small width="60%">
	<table border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td>
		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
            <td style="padding-right:0px"><a href="javascript:;" onclick="fnvshobj(this,'reportLay');"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="Create {$MODULE}..." title="Create {$MODULE}..." border=0></a></td>
			<td>&nbsp;</td>
            <td style="padding-right:0px"><a href="javascript:;" onclick="fnvshobj(this,'orgLay');"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="Create New Folder..." title="Create New Folder..." border=0></a></td>
			<td>&nbsp;</td>
            <td style="padding-right:0px"><a href="javascript:;" onclick="fnvshobj(this,'folderLay');"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="Move Reports..." title="Move Reports..." border=0></a></td>
			<td>&nbsp;</td>
            <td style="padding-right:0px"><a href="javascript:;" onClick="massDeleteReport();"><img src="{$IMAGE_PATH}btnL3Add.gif" alt="Delete Report..." title="Delete Report..." border=0></a></td>
			</tr>
		</table>
		</td>
		<td nowrap width=50>&nbsp;</td>
		<td>
		&nbsp;
		</td>
		<td>
		<table border=0 cellspacing=0 cellpadding=5>
			<tr>
			</tr>
		</table>
		</td>
		</tr>
	</table>
	</td>
	</tr>
	<tr><td style="height:2px"></td></tr>
</TABLE>


<div id="reportContents">
	{include file="ReportContents.tpl"}
</div>
<!-- Reports Table Ends Here -->

<!-- POPUP LAYER FOR CREATE NEW REPORT -->
<div style="display: none; left: 193px; top: 106px;" id="reportLay" onmouseout="fninvsh('reportLay')" onmouseover="fnvshNrm('reportLay')">
<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody><tr><td style="border-bottom: 1px solid rgb(204, 204, 204); padding: 5px;"><b>{$MOD.LBL_CREATE_NEW} :</b></td></tr>
	<tr>
	<td>
	{foreach item=modules from=$REPT_MODULES}
	<a href="javascript:CreateReport('{$modules}');" class="reportMnu">- {$modules}</a>
	{/foreach}
	</td>
	</tr>
	<tr><td style="padding: 5px;">&nbsp;</td></tr>
	</tbody>
</table>
</div>
<!-- END OF POPUP LAYER -->

<div id="orgLay" style="display:none;">
	<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tbody>
	<tr>
	<td class="genHeaderSmall" align="left" width="40%">{$MOD.LBL_ADD_NEW_GROUP}</td>
	<td align="right" width="60%"><a href="javascript:fninvsh('orgLay');"><img src="{$IMAGE_PATH}close.gif" align="absmiddle" border="0"></a></td>
	</tr>
	<tr><td colspan="2"><hr></td></tr>
	<tr>
	<td align="right" width="50%"><b>{$MOD.LBL_REP_FOLDER_NAME} : </b></td>
	<td align="left"><input id="folder_name" name="folderName" class="txtBox" type="text"></td>
	</tr>
	<input id="folder_id" name="folderId" type="hidden" value=''>
	<input id="fldrsave_mode" name="folderId" type="hidden" value='save'>
	<tr>
	<td align="right" width="50%"><b>{$MOD.LBL_REP_FOLDER_DESC} : </b></td>
	<td align="left"><input id="folder_desc" name="folderDesc" class="txtBox" type="text"></td>
	</tr>
	<tr><td style="border-bottom: 1px dashed rgb(204, 204, 204);" colspan="2">&nbsp;</td></tr>
	<tr>
	<td colspan="2" align="center">
	<input name="save" value=" &nbsp;{$APP.LBL_SAVE_BUTTON_LABEL}&nbsp; " class="classBtn" onClick="AddFolder();" type="button">&nbsp;&nbsp;
	<input name="cancel" value=" {$APP.LBL_CANCEL_BUTTON_LABEL} " class="classBtn" onclick="fninvsh('orgLay');" type="button">
	</td>
	</tr>
	<tr><td colspan="2" style="border-top: 1px dashed rgb(204, 204, 204);">&nbsp;</td></tr>
	</tbody>
	</table>
</div>

{*<!-- Contents -->*}
<div id="status" style="display:none;position:absolute;background-color:#bbbbbb;left:887px;top:0px;height:17px;white-space:nowrap;"">Processing Request...</div>
{literal}
<script>
function ajaxDelFolderResp(response)
{
	var item = response.responseText;
	getObj('customizedrep').innerHTML = item;
	
}
function DeleteFolder(id)
{
	var title = 'folder'+id;
	var fldr_name = getObj(title).innerHTML;
	if(confirm("Are you sure you want to delete the folder  "+fldr_name +" ?"))
	{
		var ajaxObj = new Ajax(ajaxDelFolderResp);
		url ='action=ReportsAjax&mode=ajax&file=DeleteReportFolder&module=Reports&record='+id;
		ajaxObj.process("index.php?",url);
	}
	else
	{
		return false;
	}
}
function AddFolder()
{
	if(getObj('folder_name').value.replace(/^\s+/g, '').replace(/\s+$/g, '').length==0)
	{
		alert('The Folder name cannot be empty');
		return false;
	}
	else
	{
		fninvsh('orgLay');
		var ajaxObj = new Ajax(ajaxDelFolderResp);
		var foldername = getObj('folder_name').value;
		var folderdesc = getObj('folder_desc').value;
		getObj('folder_name').value = '';
		getObj('folder_desc').value = '';
		foldername = foldername.replace(/&/gi,'*amp*')
			folderdesc = folderdesc.replace(/&/gi,'*amp*')
			var mode = getObj('fldrsave_mode').value;
		if(mode == 'save')
		{
			url ='action=ReportsAjax&mode=ajax&file=SaveReportFolder&module=Reports&savemode=Save&foldername='+foldername+'&folderdesc='+folderdesc;
		}
		else
		{
			var folderid = getObj('folder_id').value;
			url ='action=ReportsAjax&mode=ajax&file=SaveReportFolder&module=Reports&savemode=Edit&foldername='+foldername+'&folderdesc='+folderdesc+'&record='+folderid;
		}
		getObj('fldrsave_mode').value = 'save';
		ajaxObj.process("index.php?",url);
	}
}
function EditFolder(id,name,desc)
{
	getObj('folder_name').value = name;
	getObj('folder_desc').value = desc;
	getObj('folder_id').value = id;
	getObj('fldrsave_mode').value = 'Edit';
}
function massDeleteReport()
{
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			}else
			{	
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{
		if(confirm("Are you sure you want to delete the selected "+count+" reports ?"))
        {
			var ajaxObj = new Ajax(ajaxDelFolderResp);
			url ='action=ReportsAjax&mode=ajax&file=Delete&module=Reports&idlist='+idstring;
			ajaxObj.process("index.php?",url);
		}else
		{
			return false;
		}
			
	}else
	{
		alert('Please select atleast one Report');
		return false;
	}
}
function DeleteReport(id)
{
	if(confirm("Are you sure you want to delete this report ?"))
	{
		var ajaxObj = new Ajax(ajaxDelReportResp);
		url ='action=ReportsAjax&file=Delete&module=Reports&record='+id;
		ajaxObj.process("index.php?",url);
	}else
	{
		return false;
	}
}
function ajaxDelReportResp(response)
{
	getObj('reportContents').innerHTML = response.responseText;
}
function MoveReport(id,foldername)
{
	fninvsh('folderLay');
	var folderids = getObj('folder_ids').value;
	var folderid_array = folderids.split(',')
	var idstring = '';
	var count = 0;
	for(i=0;i < folderid_array.length;i++)
	{
		var selectopt_id = 'selected_id'+folderid_array[i];
		var objSelectopt = getObj(selectopt_id);
		if(objSelectopt != null)
		{
			var length_folder = getObj(selectopt_id).length;
			if(length_folder != undefined)
			{
				var cur_rep = getObj(selectopt_id);
				for(row = 0; row < length_folder ; row++)
				{
					var currep_id = cur_rep[row].value;
					if(cur_rep[row].checked)
					{
						count++;
						idstring = currep_id +':'+idstring;
					}
				}
			}else
			{	
				if(getObj(selectopt_id).checked)
				{
					count++;
					idstring = getObj(selectopt_id).value +':'+idstring;
				}
			}
		}
	}
	if(idstring != '')
	{
		if(confirm("Are you sure you want to move this report to "+foldername+" folder ?"))
        {
			var ajaxObj = new Ajax(ajaxDelReportResp);
			url ='action=ReportsAjax&file=ChangeFolder&module=Reports&folderid='+id+'&idlist='+idstring;
			ajaxObj.process("index.php?",url);
		}else
		{
			return false;
		}
			
	}else
	{
		alert('Please select atleast one Report');
		return false;
	}
}
</script>
{/literal}
