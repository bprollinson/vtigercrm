	
<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
<tr>
	<td class="big"><strong>{$MOD.LBL_USERS_LIST}</strong></td>
	<td class="small" align=right>&nbsp;</td>
</tr>
</table>
					
<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTableTopButtons">
<tr>
	<td class=small align=right><input title="{$CMOD.LBL_NEW_USER_BUTTON_TITLE}" accessyKey="{$CMOD.LBL_NEW_USER_BUTTON_KEY}" type="submit" name="button" value="{$CMOD.LBL_NEW_USER_BUTTON_LABEL}" class="crmButton create small"></td>
</tr>
</table>
						
<table border=0 cellspacing=0 cellpadding=5 width=100% class="listTable">
<tr>
	<td class="colHeader small" valign=top>#</td>
	<td class="colHeader small" valign=top>{$APP.Tools}</td>
	<td class="colHeader small" valign=top>{$LIST_HEADER.3}</td>
	<td class="colHeader small" valign=top>{$LIST_HEADER.5}</td>
	<td class="colHeader small" valign=top>{$LIST_HEADER.7}</td>
	<td class="colHeader small" valign=top>{$LIST_HEADER.6}</td>
	<td class="colHeader small" valign=top>{$LIST_HEADER.4}</td>
</tr>
	{foreach name=userlist item=listvalues key=userid from=$LIST_ENTRIES}
<tr>
	<td class="listTableRow small" valign=top>{$smarty.foreach.userlist.iteration}</td>
	<td class="listTableRow small" nowrap valign=top><a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}"><img src="{$IMAGE_PATH}settingsActBtnEdit.gif" alt="{$APP.LBL_EDIT_BUTTON}" title="{$APP.LBL_EDIT_BUTTON}" border="0"></a>
	{if $userid neq 1 && $userid neq 2}	
	<img src="{$IMAGE_PATH}settingsActBtnDelete.gif" onclick="deleteUser('{$userid}')" border="0"  alt="{$APP.LBL_DELETE_BUTTON}" title="{$APP.LBL_DELETE_BUTTON}" style="cursor:pointer;"/></a>
	{/if}
	<a href="index.php?action=EditView&return_action=ListView&return_module=Users&module=Users&parenttab=Settings&record={$userid}&isDuplicate=true"><img src="{$IMAGE_PATH}settingsActBtnDuplicate.gif" alt="{$APP.LBL_DUPLICATE_BUTTON}" title="{$APP.LBL_DUPLICATE_BUTTON}" border="0"></a>
</td>
	<td class="listTableRow small" valign=top><b><a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.3} </a></b><br><a href="index.php?module=Users&action=DetailView&parenttab=Settings&record={$userid}"> {$listvalues.1} {$listvalues.0}</a> ({$listvalues.2})</td>
	<td class="listTableRow small" valign=top>{$listvalues.5}</td>
	<td class="listTableRow small" valign=top>{$listvalues.7}</td>
	<td class="listTableRow small" valign=top>{$listvalues.6}</td>
	{if $listvalues.4 eq 'Active'}
	<td class="listTableRow small active" valign=top>{$APP[$listvalues.4]}</td>
	{else}
	<td class="listTableRow small inactive" valign=top>{$APP[$listvalues.4]}</td>
	{/if}	

</tr>
	{/foreach}
</table>
<table border=0 cellspacing=0 cellpadding=5 width=100% >
	<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
</table>

