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
<script type="text/javascript" src="modules/{$MODULE}/{$SINGLE_MOD}.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/dtlviewajax.js"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<span id="crmspanid" style="display:none;position:absolute;"  onmouseover="show('crmspanid');">
   <a class="link"  align="right" href="javascript:;">{$APP.LBL_EDIT_BUTTON}</a>
</span>
<script>
function tagvalidate()
{ldelim}
	if(document.getElementById('txtbox_tagfields').value != '')
		SaveTag('txtbox_tagfields','{$ID}','{$MODULE}');	
	else
	{ldelim}
		alert("Please enter a tag");
		return false;
	{rdelim}
{rdelim}
function DeleteTag(id)
{ldelim}
	$("vtbusy_info").style.display="inline";
	Effect.Fade('tag_'+id);
	new Ajax.Request(
		'index.php',
                {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
                        method: 'post',
                        postBody: "file=TagCloud&module={$MODULE}&action={$MODULE}Ajax&ajxaction=DELETETAG&tagid=" +id,
                        onComplete: function(response) {ldelim}
						getTagCloud();
						$("vtbusy_info").style.display="none";
                        {rdelim}
                {rdelim}
        );
{rdelim}

</script>

<table width="100%" cellpadding="2" cellspacing="0" border="0">
   <form action="index.php" method="post" name="DetailView" id="form">
   <tr>
	<td>&nbsp;</td>
	<td>
                <table cellpadding="0" cellspacing="5" border="0">
			{include file='DetailViewHidden.tpl'}
		</table>	

		{include file='Buttons_List1.tpl'}

		<!-- Contents -->
		<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
		   <tr>
			<td valign=top><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
			<td class="showPanelBg" valign=top width=100%>
			<!-- PUBLIC CONTENTS STARTS-->
			   <div class="small" style="padding:20px" >
		
				<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
				   <tr>
					<td>
						<span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$MOD[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span>
					</td>
					<td>&nbsp;</td>
				   </tr>
				   <tr height=20>
					<td>{$UPDATEINFO}</td>
				   </tr>
				</table>

				<hr noshade size=1>
		
				<!-- Entity and More information tabs -->
				<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
				   <tr>
					<td>
						<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
						   <tr>
							<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
							<td class="dvtSelectedCell" align=center nowrap>{$MOD[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</td>	
							<td class="dvtTabCache" style="width:10px">&nbsp;</td>
							<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>

							<td class="dvtTabCache" style="width:100%">&nbsp;</td>
						   </tr>
						</table>
					</td>
				   </tr>
				   <tr>
					<td valign=top align=left >
						<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
						   <tr>

							<td align=left style="padding:10px;">
							<!-- content cache -->
								<!-- Entity informations display - starts -->	
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                			   <tr>
									<td style="padding:10px;border-right:1px dashed #CCCCCC;" width="80%">



<!-- The following table is used to display the buttons -->
<table border=0 cellspacing=0 cellpadding=0 width=100%>
   {strip}
   <tr nowrap>
	<td  colspan=4 style="padding:5px">
		{if $EDIT_DUPLICATE eq 'permitted'}
			<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">&nbsp;
		<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
		{/if}
		{if $DELETE eq 'permitted'}
			<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
		{/if}
		<!-- Commented the buttons in DetailView because these buttons have been given as links
		{if $MODULE eq 'Quotes' || $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
			{if $CREATEPDF eq 'permitted'}
				<input title="Export To PDF" accessKey="Alt+e" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}'; this.form.module.value='{$MODULE}'; {if $MODULE eq 'SalesOrder'} this.form.action.value='CreateSOPDF'" {else} this.form.action.value='CreatePDF'" {/if} type="submit" name="Export To PDF" value="{$APP.LBL_EXPORT_TO_PDF}">&nbsp;
			{/if}
		{/if}
		{if $MODULE eq 'Quotes'}
			{if $CONVERTSALESORDER eq 'permitted'}
				<input title="{$APP.LBL_CONVERTSO_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTSO_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='SalesOrder'; this.form.return_action.value='DetailView'; this.form.convertmode.value='quotetoso';this.form.module.value='SalesOrder'; this.form.action.value='EditView'" type="submit" name="Convert To SalesOrder" value="{$APP.LBL_CONVERTSO_BUTTON_LABEL}">&nbsp;
			{/if}
		{/if}
		{if $MODULE eq 'Potentials' || $MODULE eq 'Quotes' || $MODULE eq 'SalesOrder'}
			{if $CONVERTINVOICE eq 'permitted'}
				<input title="{$APP.LBL_CONVERTINVOICE_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTINVOICE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.convertmode.value='{$CONVERTMODE}';this.form.module.value='Invoice'; this.form.action.value='EditView'" type="submit" name="Convert To Invoice" value="{$APP.LBL_CONVERTINVOICE_BUTTON_LABEL}">&nbsp;
			{/if}
		{/if}
		-->
	</td>
   </tr>
   {/strip}
</table>
<!-- Button displayed - finished-->

<!-- Entity information(blocks) display - start -->
{foreach key=header item=detail from=$BLOCKS}
	<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
	   <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=right>
		</td>
	   </tr>
	   <tr>
		{strip}
		<td colspan=4 class="dvInnerHeader" >
			<b>
			       	{$header}
			</b>
		</td>
		{/strip}
	   </tr>

	   {foreach item=detail from=$detail}
	   <tr style="height:25px">
		{foreach key=label item=data from=$detail}
			{assign var=keyid value=$data.ui}
			{assign var=keyval value=$data.value}
			{assign var=keytblname value=$data.tablename}
			{assign var=keyfldname value=$data.fldname}
			{assign var=keyoptions value=$data.options}
			{assign var=keysecid value=$data.secid}
			{assign var=keyseclink value=$data.link}
			{assign var=keycursymb value=$data.cursymb}
			{assign var=keysalut value=$data.salut}
			{assign var=keycntimage value=$data.cntimage}
			   {assign var=keyadmin value=$data.isadmin}
							   

				{if $label ne ''}
					{if $keycntimage ne ''}
						<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$keycntimage}</td>
					{elseif $label neq 'Tax Class'}<!-- Avoid to display the label Tax Class -->
						{if $keyid eq '71' || $keyid eq '72'}  <!--CurrencySymbol-->
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label} ({$keycursymb})</td>
						{else}
							<td class="dvtCellLabel" align=right width=25%><input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>{$label}</td>
						{/if}
					{/if}  
					{if $EDIT_PERMISSION eq 'yes'}
						{include file="DetailViewUI.tpl"}
					{else}
						{include file="DetailViewFields.tpl"}
					{/if}
				{else} 
					<td class="dvtCellLabel" align=right>&nbsp;</td>
					<td class="dvtCellInfo" align=left >&nbsp;</td>
				{/if}
		{/foreach}
	   </tr>	
	   {/foreach}	
	</table>
{/foreach}
{*-- End of Blocks--*} 
<!-- Entity information(blocks) display - ends -->

									<br>

										<!-- Product Details informations -->
										{$ASSOCIATED_PRODUCTS}

									</td>
<!-- The following table is used to display the buttons -->
								<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                			   <tr>
									<td style="padding:10px;border-right:1px dashed #CCCCCC;" width="80%">
<table border=0 cellspacing=0 cellpadding=0 width=100%>
   {strip}
   <tr nowrap>
	<td  colspan=4 style="padding:5px">
		{if $EDIT_DUPLICATE eq 'permitted'}
			<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="crmbutton small edit" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">&nbsp;
		<input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
		{/if}
		{if $DELETE eq 'permitted'}
			<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="crmbutton small delete" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
		{/if}
		<!-- Commented the buttons in DetailView because these buttons have been given as links
		{if $MODULE eq 'Quotes' || $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
			{if $CREATEPDF eq 'permitted'}
				<input title="Export To PDF" accessKey="Alt+e" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}'; this.form.module.value='{$MODULE}'; {if $MODULE eq 'SalesOrder'} this.form.action.value='CreateSOPDF'" {else} this.form.action.value='CreatePDF'" {/if} type="submit" name="Export To PDF" value="{$APP.LBL_EXPORT_TO_PDF}">&nbsp;
			{/if}
		{/if}
		{if $MODULE eq 'Quotes'}
			{if $CONVERTSALESORDER eq 'permitted'}
				<input title="{$APP.LBL_CONVERTSO_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTSO_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='SalesOrder'; this.form.return_action.value='DetailView'; this.form.convertmode.value='quotetoso';this.form.module.value='SalesOrder'; this.form.action.value='EditView'" type="submit" name="Convert To SalesOrder" value="{$APP.LBL_CONVERTSO_BUTTON_LABEL}">&nbsp;
			{/if}
		{/if}
		{if $MODULE eq 'Potentials' || $MODULE eq 'Quotes' || $MODULE eq 'SalesOrder'}
			{if $CONVERTINVOICE eq 'permitted'}
				<input title="{$APP.LBL_CONVERTINVOICE_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTINVOICE_BUTTON_KEY}" class="crmbutton small create" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.convertmode.value='{$CONVERTMODE}';this.form.module.value='Invoice'; this.form.action.value='EditView'" type="submit" name="Convert To Invoice" value="{$APP.LBL_CONVERTINVOICE_BUTTON_LABEL}">&nbsp;
			{/if}
		{/if}
		-->
	</td>
   </tr>
   {/strip}
</table>
</td></tr></table>
<!-- Button displayed - finished-->
									<!-- Inventory Actions - ends -->	
									<td width=22% valign=top style="padding:10px;">
										<!-- right side InventoryActions -->
										{include file="Inventory/InventoryActions.tpl"}

										<br>
										<!-- Add Tag link added just above the tag cloud image -->
										<table border=0 cellspacing=0 cellpadding=5 width=100% >
										<tr>
											<td align="left" class="genHeaderSmall" nowrap><div id="addtagdiv"><a href="javascript:;" onClick="show('tagdiv'),fnhide('addtagdiv'),document.getElementById('txtbox_tagfields').focus()"><b>{$APP.LBL_ADD_TAG}</b></a></div><div id="tagdiv" style="display:none;"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value=""></input>&nbsp;&nbsp;<input name="button_tagfileds" type="button" class="crmbutton small save" value="{$APP.LBL_TAG_IT}" onclick="return tagvalidate()"/><input name="close" type="button" class="crmbutton small cancel" value="{$APP.LBL_CLOSE}" onClick="fnhide('tagdiv'),show('addtagdiv')"></div></td>
										</tr>
										</table>
										<!-- Eng Add Tag Link -->
										<br>
										<!-- To display the Tag Clouds -->
										<div>
										      {include file="TagCloudDisplay.tpl"}
										</div>
									</td>
								   </tr>
								</table>
							</td>
						   </tr>
						</table>
					<!-- PUBLIC CONTENTS STOPS-->
					</td>
					<td align=right valign=top>
						<img src="{$IMAGE_PATH}showPanelTopRight.gif">
					</td>
				   </tr>
				</table>
			   </div>
			</td>
		   </tr>
		</table>
		<!-- Contents - end -->

<script>
function getTagCloud()
{ldelim}
new Ajax.Request(
        'index.php',
        {ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
        method: 'post',
        postBody: 'module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD&recordid={$ID}',
        onComplete: function(response) {ldelim}
                                $("tagfields").innerHTML=response.responseText;
                                $("txtbox_tagfields").value ='';
                        {rdelim}
        {rdelim}
);
{rdelim}
getTagCloud();
</script>

	</td>
   </tr>
</form>
</table>
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>

