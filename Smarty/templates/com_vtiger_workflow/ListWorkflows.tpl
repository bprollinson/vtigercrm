{include file='com_vtiger_workflow/Header.tpl'}
<script src="modules/{$module->name}/resources/jquery-1.2.6.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/functional.js" type="text/javascript" charset="utf-8"></script>
<script src="modules/{$module->name}/resources/workflowlistscript.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	fn.addStylesheet('modules/{$module->name}/resources/style.css');
</script>
<div id="new_workflow_popup" class="layerPopup" style="display:none;">
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerHeadingULine">
		<tr>
			<td width="80%" align="left" class="layerPopupHeading">
				Create Workflow
				</td>
			<td width="20%" align="right">
				<a href="javascript:void(0);" id="new_workflow_popup_close">
					<img border="0" align="absmiddle" src="{'close.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
			</td>
		</tr>
	</table>
	<form action="index.php" method="get" accept-charset="utf-8">
		<div class="popup_content">
			Create a workflow for  
			<select name="module_name">
{foreach item=moduleName from=$moduleNames}
				<option>
					{$moduleName}
				</option>
{/foreach}
			</select>
			<input type="hidden" name="save_type" value="new" id="save_type_new">
			<input type="hidden" name="module" value="{$module->name}" id="save_module">
			<input type="hidden" name="action" value="editworkflow" id="save_action">
		</div>
	<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
		<tr><td align="center">
			<input type="submit" class="crmButton small save" value="{$APP.LBL_CREATE_BUTTON_LABEL}" name="save" id='new_workflow_popup_save'/> 
			<input type="button" class="crmButton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL} " name="cancel" id='new_workflow_popup_cancel'/>
		</td></tr>
	</table>
	</form>
</div>

{include file='SetMenu.tpl'}
<div id="view">
	{include file='com_vtiger_workflow/ModuleTitle.tpl'}
	<table class="tableHeading" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="big" nowrap="">
				<strong><span id="module_info"></span></strong>
			</td>
			<td class="small" align="right">
				<form action="index.php" method="get" accept-charset="utf-8" id="filter_modules">
					<b>{$MOD.LBL_SELECT_MODULE}: </b>
					<select class="importBox" name="list_module" id='pick_module'>
						<option value="All">All</a>
							<option value="All">-----------------------------</a>
{foreach  item=moduleName from=$moduleNames}
						<option value="{$moduleName}" {if $moduleName eq $listModule}selected{/if}>
							{$moduleName}
						</option>
{/foreach}
					</select>
					<input type="hidden" name="module" value="{$module->name}">
					<input type="hidden" name="action" value="workflowlist">
				</form>

			</td>
		</tr>
	</table>
			
	<table class="listTableTopButtons" width="100%" border="0" cellspacing="0" cellpadding="5">
		<tr>
			<td class="small"> <span id="status_message"></span> </td>
			<td class="small" align="right">
				<input type="button" class="crmButton create small" 
					value="New Workflow" id='new_workflow'/>
			</td>
		</tr>
	</table>
	<table class="listTable" width="100%" border="0" cellspacing="0" cellpadding="5" id='expressionlist'>
		<tr>
			<td class="colHeader small" width="20%">
				Module
			</td>
			<td class="colHeader small" width="65">
				Description
			</td>
			<td class="colHeader small" width="15%">
				Tools
			</td>
		</tr>
{foreach item=workflow from=$workflows}
		<tr>
			<td>{$workflow->moduleName}</td>
			<td>{$workflow->description}</td>
			<td>
				<a href="{$module->editWorkflowUrl($workflow->id)}">
					<img border="0" title="Edit" alt="Edit" \
						style="cursor: pointer;" id="expressionlist_editlink_{$workflow->id}" \
						src="{'editfield.gif'|@vtiger_imageurl:$THEME}"/>
				</a>
				<a href="{$module->deleteWorkflowUrl($workflow->id)}">
					<img border="0" title="Delete" alt="Delete"\
			 			src="{'delete.gif'|@vtiger_imageurl:$THEME}" \
						style="cursor: pointer;" id="expressionlist_deletelink_{$workflow->id}"/>
				</a>
			</td>
		</tr>
{/foreach}
	</table>
</div>
{include file='com_vtiger_workflow/Footer.tpl'}

