<h1>{L_TITLE}</h1>

<p>{L_TITLE_EXPLAIN}</p>

<form action="{S_ACTION}" name="post" method="post">
<table cellpadding="4" cellspacing="1" border="0" align="center" class="forumline">
<tr>
	<th nowrap="nowrap" colspan="4">{L_SEARCH_RESULTS}</th>
</tr>
<tr>
	<td class="cat" nowrap="nowrap" align="center"><span class="cattitle">{L_PACK}</span></td>
	<td class="cat" nowrap="nowrap"><span class="cattitle">{L_KEY}</span></td>
	<td class="cat" nowrap="nowrap"><span class="cattitle">{L_VALUE}</span></td>
	<td class="cat" nowrap="nowrap" align="center"><span class="cattitle">{L_LEVEL}</span></td>
</tr>
<!-- BEGIN row -->
<tr>
	<td class="{row.CLASS}"><a href="{row.U_PACK}" class="gen">{row.PACK}</a></td>
	<td class="{row.CLASS}" nowrap="nowrap"><a href="{row.U_KEY}" class="gen">{row.KEY_MAIN}{row.KEY_SUB}</a>{row.STATUS}</td>
	<td class="{row.CLASS}"><span class="gen">{row.VALUE}</span></td>
	<td class="{row.CLASS}" align="center"><span class="gen">{row.LEVEL}</span></td>
</tr>
<!-- END row -->
<!-- BEGIN none -->
<tr>
	<td class="row1" align="center" colspan="4"><span class="gen">{L_NONE}</span>
</tr>
<!-- END none -->
<tr>
	<td class="catBottom" align="center" colspan="4">
		<input type="submit" name="cancel" class="liteoption" value="{L_CANCEL}" />
	</td>
</tr>
</table>
{S_HIDDEN_FIELDS}</form>