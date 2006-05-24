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
                var ajaxObj = new Ajax(ajaxResponse);
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
	<td valign=top>
		<img src="{$IMAGE_PATH}showPanelTopLeft.gif">
	</td>

	<td class="showPanelBg" valign=top width=100%>
	     {*<!-- PUBLIC CONTENTS STARTS-->*}
	     <div class="small" style="padding:20px">
		
		 {if $OP_MODE eq 'edit_view'}   
			 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$APP.LBL_EDITING} {$SINGLE_MOD} {$APP.LBL_INFORMATION}</span> <br>
			{$UPDATEINFO}	 
		 {/if}
		 {if $OP_MODE eq 'create_view'}
			<span class="lvtHeaderText">{$APP.LBL_CREATING} {$APP.LBL_NEW} {$SINGLE_MOD}</span> <br>
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

					{if $BLOCKS_COUNT eq 2}	
						<td width=75 style="width:15%" align="center" nowrap="nowrap" class="dvtSelectedCell" id="bi" onclick="fnLoadValues('bi','mi','basicTab','moreTab')"><b>{$APP.LBL_BASIC} {$APP.LBL_INFORMATION}</b></td>
                    				<td class="dvtUnSelectedCell" style="width: 100px;" align="center" nowrap="nowrap" id="mi" onclick="fnLoadValues('mi','bi','moreTab','basicTab')"><b>{$APP.LBL_MORE} {$APP.LBL_INFORMATION} </b></td>
                   				<td class="dvtTabCache" style="width:100%" nowrap="nowrap">&nbsp;</td>
					{else}
						<td class="dvtSelectedCell" align=center nowrap>{$APP.LBL_BASIC} {$APP.LBL_INFORMATION}</td>
	                                        <td class="dvtTabCache" style="width:100%">&nbsp;</td>
					{/if}
				   <tr>
				</table>
			</td>
		   </tr>
		   <tr>
			<td valign=top align=left >

			    {foreach item=blockInfo key=divName from=$BLOCKS}
			    <!-- Basic and More Information Tab Opened -->
			    <div id="{$divName}">

				<table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
				   <tr>
					<!--this td is to display the entity details -->
					<td align=left>
					<!-- content cache -->

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
										<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small" onclick="this.form.action.value='Save';  return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
                                                                 		<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
									   </div>
									</td>
								   </tr>

								   {foreach key=header item=data from=$blockInfo}
								   <tr>
						         		<td colspan=4 class="detailedViewHeader">
                                                	        		<b>{$header}</b>
									</td>
								   </tr>

								   <!-- Here we should include the uitype handlings-->
								   {include file="DisplayFields.tpl"}

								   <tr style="height:25px"><td>&nbsp;</td></tr>
								   {/foreach}

								   <!-- This if is added to restrict display in more tab-->
								   {if $divName eq 'basicTab'}
								   {if $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Quotes' || $MODULE eq 'Invoice'}
								   	<!-- Added to display the product details -->
									<!-- This if is added when we want to populate product details from the related entity  for ex. populate product details in new SO page when select Quote -->
									{if $AVAILABLE_PRODUCTS eq true}
										{include file="ProductDetailsEditView.tpl"}
									{else}
										{include file="ProductDetails.tpl"}
									{/if}

								   {/if}
								   {/if}

								   <tr>
									<td  colspan=4 style="padding:5px">
									   <div align="center">
										<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small" onclick="this.form.action.value='Save'; return formValidate()" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" >
										<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" onclick="window.history.back()" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
									   </div>
									</td>
								   </tr>
								</table>
								<!-- General details - end -->
							</td>
						   </tr>
						</table>
					</td>
				   </tr>
				</table>
					
			    </div>
			    {/foreach}
			</td>
		   </tr>
		</table>
		</form>
	 </div>
	</td>
   </tr>
</table>

