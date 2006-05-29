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

<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$CALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script type="text/javascript" src="modules/{$MODULE}/{$SINGLE_MOD}.js"></script>
<script type="text/javascript">

function ajaxResponse(response)
{ldelim}
        document.getElementById('autocom').innerHTML = response.responseText;
        document.getElementById('autocom').style.display="block";
        hide('vtbusy_info');
{rdelim}

function sensex_info()
{ldelim}
        var Ticker = document.getElementById('tickersymbol').value;
        if(Ticker!='')
        {ldelim}
                show('vtbusy_info');
                var ajaxObj = new VtigerAjax(ajaxResponse);
                //var Ticker = document.getElementById('tickersymbol').value;
                var urlstring = "module={$MODULE}&action=Tickerdetail&tickersymbol="+Ticker;
                ajaxObj.process("index.php?",urlstring);
        {rdelim}
{rdelim}
</script>

		{include file='Buttons_List1.tpl'}	

{*<!-- Contents -->*}
<table border=0 cellspacing=0 cellpadding=0 width=98% align=center>
   <tr>
	<td valign=top><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>

	<td class="showPanelBg" valign=top width=100%>
		{*<!-- PUBLIC CONTENTS STARTS-->*}
		<div class="small" style="padding:20px">
		
			{if $OP_MODE eq 'edit_view'}   
				<span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} - {$APP.LBL_EDITING} {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span> <br>
				{$UPDATEINFO}	 
			{/if}
			{if $OP_MODE eq 'create_view'}
				<span class="lvtHeaderText">{$APP.LBL_CREATING} {$SINGLE_MOD}</span> <br>
			{/if}

			<hr noshade size=1>
			<br> 
		
			{include file='EditViewHidden.tpl'}

			{*<!-- Account details tabs -->*}
			<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
			   <tr>
				<td>
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
					   <tr>
						<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
                                        	<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD} {$APP.LBL_INFORMATION}</td>
	                                        <td class="dvtTabCache" style="width:10px">&nbsp;</td>

        	                                {if $OP_MODE neq 'create_view'}
	                                        <td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}" onclick="getRelatedLink()">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>
        	                                {/if}

                	                        <td class="dvtTabCache" style="width:100%">&nbsp;</td>
					   </tr>
					</table>
				</td>
			   </tr>
			   <tr>
				<td valign=top align=left >
					<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
					   <tr>

						<td align=left style="padding:10px;border-right:1px dashed #CCCCCC;" width=80%>
							{*<!-- content cache -->*}
					
							<table border=0 cellspacing=0 cellpadding=0 width=100%>
							   <tr>
								<td id ="autocom"></td>
							   </tr>
							   <tr>
								<td style="padding:10px">
								<!-- General details -->
									<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
									   <tr>
										<td  colspan=4 style="padding:5px">
										   <div align="center">
											<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small" onclick="this.form.action.value='Save'; displaydeleted(); return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
											<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" onclick="window.history.back()" type="button" name="button" value="{$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
										   </div>
										</td>
									   </tr>

									   <!-- included to handle the edit fields based on ui types -->
									   {foreach key=header item=data from=$BLOCKS}
									      </tr>
										<td colspan=4 class="detailedViewHeader">
											<b>{$header}</b>
										</td>
									      </tr>

										<!-- Handle the ui types display -->
										{include file="DisplayFields.tpl"}

									      <tr style="height:25px"><td>&nbsp;</td></tr>

									   {/foreach}


									   <!-- Added to display the Product Details in Inventory-->
									   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
									   <tr>
										<td colspan=4>
											{include file="Inventory/ProductDetailsEditView.tpl"}
										</td>
									   </tr>
									   {/if}

									   <tr>
										<td  colspan=4 style="padding:5px">
											<div align="center">
												<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small" onclick="this.form.action.value='Save';  displaydeleted();return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
												<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
											</div>
										</td>
									   </tr>
									</table>
								</td>
							   </tr>
							</table>
						</td>
						<!-- Inventory Actions - ends -->
						<td width=20% valign=top style="padding:10px;">
							<!-- right side InventoryActions -->
							{include file="Inventory/InventoryActions.tpl"}
						</td>
					   </tr>
					</table>
				</td>
			   </tr>
			</table>
		</div>
	</td>
   </tr>
</table>
</form>





<script>
	ScrollEffect.limit = 201;
	ScrollEffect.closelimit= 200;
</script>
<script>	
        var fieldname = new Array({$VALIDATION_DATA_FIELDNAME})

        var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL})

        var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE})

	var ProductImages=new Array();
	var count=0;
	function delRowEmt(imagename)
	{ldelim}
		ProductImages[count++]=imagename;
		multi_selector.current_element.disabled = false;
		multi_selector.count--;
	{rdelim}
	function displaydeleted()
	{ldelim}
		if(ProductImages.length > 0)
			document.EditView.del_file_list.value=ProductImages.join('###');
	{rdelim}
</script>
