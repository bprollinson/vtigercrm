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
{strip}
<div class="listViewPageDiv">
	<div class="listViewTopMenuDiv">
		<span class="foldersContainer">{include file='ListViewFolders.tpl'|@vtemplate_path:$MODULE}</span>
		{include file='ListViewActions.tpl'|@vtemplate_path:$MODULE}
	</div>	
<div class="listViewContentDiv" id="listViewContents">
{/strip}