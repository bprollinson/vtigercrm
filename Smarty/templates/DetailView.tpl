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
{if $MODULE eq 'Leads' or $MODULE eq 'Contacts' or $MODULE eq 'Accounts'}
<div id="sendmail_cont" style="z-index:100001;position:absolute;"></div>
{/if}

<div id="convertleaddiv" style="display:block;position:absolute;left:225px;top:150px;"></div>
<script>
function ajaxSaveResponse(response)
{ldelim}
        document.getElementById("convertleaddiv").innerHTML=response.responseText;
{rdelim}

function callConvertLeadDiv(id)
{ldelim}
        var ajaxObj = new VtigerAjax(ajaxSaveResponse);
        var urlstring = "module=Leads&action=LeadsAjax&file=ConvertLead&record="+id;
        ajaxObj.process("index.php?",urlstring);
{rdelim}
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
</script>

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<form action="index.php" method="post" name="DetailView" id="form">
<tr><td>&nbsp;</td>
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
		
		<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%"><tr><td>		
		 <span class="lvtHeaderText"><font color="purple">[ {$ID} ] </font>{$NAME} -  {$APP[$SINGLE_MOD]} {$APP.LBL_INFORMATION}</span>&nbsp;&nbsp;<span id="vtbusy_info" style="display:none;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span><span id="vtbusy_info" style="visibility:hidden;" valign="bottom"><img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span></td><td>&nbsp;</td></tr>
		 <tr height=20><td>{$UPDATEINFO}</td><td align="right" width="400" nowrap><div id="addtagdiv"><a href="javascript:;" onClick="show('tagdiv'),hide('addtagdiv'),document.getElementById('txtbox_tagfields').focus()">+addtag</a></div><div id="tagdiv" style="display:none;"><input class="textbox"  type="text" id="txtbox_tagfields" name="textbox_First Name" value=""></input>&nbsp;&nbsp;<input name="button_tagfileds" type="button" class="small" value="Tag it" onclick="return tagvalidate()"/><input name="close" type="button" class="small" value="Close" onClick="hide('tagdiv'),show('addtagdiv')"></div></td></tr>
		 </table>			 
		 <hr noshade size=1>
		
		<!-- Account details tabs -->
		<table border=0 cellspacing=0 cellpadding=0 width=95% align=center>
		<tr>
			<td>
				<table border=0 cellspacing=0 cellpadding=3 width=100% class="small">
				<tr>
					<td class="dvtTabCache" style="width:10px" nowrap>&nbsp;</td>
					{if $MODULE eq 'Notes' || $MODULE eq 'Faq' || $MODULE eq 'Webmails' || ($MODULE eq 'Activities' && $ACTIVITY_MODE eq 'Task')}
					<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD} {$APP.LBL_INFORMATION}</td>
					<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					{else}
					<td class="dvtSelectedCell" align=center nowrap>{$SINGLE_MOD} {$APP.LBL_INFORMATION}</td>	
					<td class="dvtTabCache" style="width:10px">&nbsp;</td>
					<td class="dvtUnSelectedCell" align=center nowrap><a href="index.php?action=CallRelatedList&module={$MODULE}&record={$ID}&parenttab={$CATEGORY}">{$APP.LBL_MORE} {$APP.LBL_INFORMATION}</a></td>

					<td class="dvtTabCache" style="width:100%">&nbsp;</td>
					{/if}
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign=top align=left >
                           <table border=0 cellspacing=0 cellpadding=3 width=100% class="dvtContentSpace">
				<tr>

					<td align=left>
					<!-- content cache -->
					
					<table border=0 cellspacing=0 cellpadding=0 width=100%>
			                  <tr>
					     <td style="padding:10px">
						     <!-- General details -->
				                     <table border=0 cellspacing=0 cellpadding=0 width=100%>
						     {strip}<tr nowrap>
							<td  colspan=4 style="padding:5px">
								{if $EDIT_DUPLICATE eq 'permitted'}
                                                                <input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.module.value='{$MODULE}';this.form.action.value='EditView'" type="submit" name="Edit" value="{$APP.LBL_EDIT_BUTTON_LABEL}">&nbsp;
                                                                <input title="{$APP.LBL_DUPLICATE_BUTTON_TITLE}" accessKey="{$APP.LBL_DUPLICATE_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.isDuplicate.value='true';this.form.module.value='{$MODULE}'; this.form.action.value='EditView'" type="submit" name="Duplicate" value="{$APP.LBL_DUPLICATE_BUTTON_LABEL}">&nbsp;
                                                                {/if}
								{if $DELETE eq 'permitted'}
                                                                <input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='index'; this.form.action.value='Delete'; return confirm('{$APP.NTC_DELETE_CONFIRMATION}')" type="submit" name="Delete" value="{$APP.LBL_DELETE_BUTTON_LABEL}">&nbsp;
                                                                {/if}

                                                        {if $MODULE eq 'Webmails'}
                                                                <input title="Add to CRM" class="small" onclick="window.location='index.php?module={$MODULE}&action=Save&mailid={$ID}';return false;" type="submit" name="addtocrm" value="Add to CRM">&nbsp;
                                                                <input title="Reply to Sender" class="small" onclick="window.location='index.php?module={$MODULE}&action=EditView&mailid={$ID}&reply=single&return_action=DetailView&return_module=Webmails&return_id={$ID}';return false;" type="submit" name="replytosender" value="Reply to Sender">&nbsp;
                                                                <input title="Reply to All" class="small" onclick="window.location='index.php?module={$MODULE}&action=EditView&mailid={$ID}&reply=all&return_action=DetailView&return_module=Webmails&return_id={$ID}';return false;" type="submit" name="replytosender" value="Reply to All">&nbsp;
                                                        {/if}
                                                        {if $MODULE eq 'Leads' || $MODULE eq 'Contacts'}
                                                                {if $SENDMAILBUTTON eq 'permitted'}
                                                                <input title="{$APP.LBL_SENDMAIL_BUTTON_TITLE}" accessKey="{$APP.LBL_SENDMAIL_BUTTON_KEY}" class="small" onclick="fnvshobj(this,'sendmail_cont');sendmail('{$MODULE}',{$ID});" type="button" name="SendMail" value="{$APP.LBL_SENDMAIL_BUTTON_LABEL}">&nbsp;
                                                                {/if}
                                                        {/if}
                                                        {if $MODULE eq 'Quotes' || $MODULE eq 'PurchaseOrder' || $MODULE eq 'SalesOrder' || $MODULE eq 'Invoice'}
                                                                {if $CREATEPDF eq 'permitted'}
                                                                <input title="Export To PDF" accessKey="Alt+e" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}'; this.form.module.value='{$MODULE}'; {if $MODULE eq 'SalesOrder'} this.form.action.value='CreateSOPDF'" {else} this.form.action.value='CreatePDF'" {/if} type="submit" name="Export To PDF" value="{$APP.LBL_EXPORT_TO_PDF}">&nbsp;
                                                                {/if}
                                                        {/if}
                                                        {if $MODULE eq 'Quotes'}
                                                                {if $CONVERTSALESORDER eq 'permitted'}
                                                                <input title="{$APP.LBL_CONVERTSO_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTSO_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='SalesOrder'; this.form.return_action.value='DetailView'; this.form.convertmode.value='quotetoso';this.form.module.value='SalesOrder'; this.form.action.value='EditView'" type="submit" name="Convert To SalesOrder" value="{$APP.LBL_CONVERTSO_BUTTON_LABEL}">&nbsp;
                                                                {/if}
                                                        {/if}
							{if $MODULE eq 'HelpDesk'}
                                                                {if $CONVERTASFAQ eq 'permitted'}
                                                                <input title="{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_TITLE}" accessKey="{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='Faq'; this.form.return_action.value='DetailView'; this.form.action.value='ConvertAsFAQ';" type="submit" name="ConvertAsFAQ" value="{$MOD.LBL_CONVERT_AS_FAQ_BUTTON_LABEL}">&nbsp;
                                                                {/if}
                                                        {/if}

                                                        {if $MODULE eq 'Potentials' || $MODULE eq 'Quotes' || $MODULE eq 'SalesOrder'}
                                                                {if $CONVERTINVOICE eq 'permitted'}
                                                                <input title="{$APP.LBL_CONVERTINVOICE_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERTINVOICE_BUTTON_KEY}" class="small" onclick="this.form.return_module.value='{$MODULE}'; this.form.return_action.value='DetailView'; this.form.return_id.value='{$ID}';this.form.convertmode.value='{$CONVERTMODE}';this.form.module.value='Invoice'; this.form.action.value='EditView'" type="submit" name="Convert To Invoice" value="{$APP.LBL_CONVERTINVOICE_BUTTON_LABEL}">&nbsp;
                                                                {/if}
                                                        {/if}
                                                        {if $MODULE eq 'Leads'}
                                                                {if $CONVERTLEAD eq 'permitted'}
                                                                <input title="{$APP.LBL_CONVERT_BUTTON_TITLE}" accessKey="{$APP.LBL_CONVERT_BUTTON_KEY}" class="small" onclick="callConvertLeadDiv('{$ID}');" type="button" name="Convert" value="{$APP.LBL_CONVERT_BUTTON_LABEL}">&nbsp;
                                                                {/if}
                                                        {/if}
							</td>


						     </tr>{/strip}	
							{foreach key=header item=detail from=$BLOCKS}
							<table border=0 cellspacing=0 cellpadding=0 width=100% class="small">
							<tr>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                        <td align=right>
							{if $header eq 'Address Information' && ($MODULE eq 'Accounts' || $MODULE eq 'Contacts' || $MODULE eq 'Leads') }
                                                        {if $MODULE eq 'Leads'}
                                                        <input id="locateMap" name="locateMap" value="Locate Map" class="small" type="button" onClick="searchMapLocation( 'Main' )" title="Locate Map">
                                                        {else}
                                                                {if $MODULE eq 'Accounts'}
                                                                       {assign var=address1 value='Billing'}
                                                                       {assign var=address2 value='Shipping'}
                                                                {/if}
                                                                {if $MODULE eq 'Contacts'}
                                                                       {assign var=address1 value='Mailing'}
                                                                       {assign var=address2 value='Other'}
                                                                {/if}
                                                                <input id="locateMap" name="locateMap" value="Locate Map" class="small" type="button" onClick="javascript:showLocateMapMenu()" title="Locate Map">
                                                        <div id="dropDownMenu" style="position:absolute;display:none;z-index:60">
							<table border="0" cellspacing="0" cellpadding="4">
                                                <tr bgcolor=white class="lvtColData" onMouseOver="this.className='lvtColDataHover'" style="cursor:pointer;" onMouseOut="this.className='lvtColData'" onClick="searchMapLocation( 'Main' )">
                                                <td>{$address1} Address</td>
                                                </tr>
                                                <tr bgcolor=white class="lvtColData" onMouseOver="this.className='lvtColDataHover'" style="cursor:pointer;" onMouseOut="this.className='lvtColData'"  onClick="searchMapLocation( 'Other' )">
                                                <td>{$address2} Address</td>
                                                </tr>
                                                </table>
                                                </div>
                                                {/if}
                                                <script>
                                                        document.onclick=hideLocateMapMenu;
                                                </script>
                                               {/if}
                                                        </td>
                                                        </tr>




							<!-- This is added to display the existing comments -->
							{if $header eq 'Comments' || $header eq 'Comment Information'}
							   <tr>
								<td colspan=4 style="border-bottom:1px solid #999999;padding:5px;" bgcolor="#e5e5e5">
						        	<b>{$MOD.LBL_COMMENT_INFORMATION}</b>
								</td>
							   </tr>
							   <tr>
							   			<td colspan=4 class="dvtCellInfo">{$COMMENT_BLOCK}</td>
							   </tr>
							   <tr><td>&nbsp;</td></tr>
							{/if}





						     <tr>{strip}
						     <td colspan=4 style="border-bottom:1px solid #999999;padding:5px;" bgcolor="#e5e5e5">
							<b>
						        	{$header}
	  			     			</b>
						     </td>{/strip}
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
							   
							   <input type="hidden" id="hdtxt_IsAdmin" value={$keyadmin}></input>
							   
                           {if $label ne ''}
	                        {if $keycntimage ne ''}
					<td class="dvtCellLabel" align=right width=25%>{$keycntimage}</td>
				{elseif $keyid eq '71' || $keyid eq '72'}<!-- Currency symbol -->
					<td class="dvtCellLabel" align=right width=25%>{$label} ({$keycursymb})</td>
				{else}
					<td class="dvtCellLabel" align=right width=25%>{$label}</td>
				{/if}  

										{include file="DetailViewUI.tpl"}
						   {else} 
                                          <td class="dvtCellLabel" align=right>&nbsp;</td>
                                           <td class="dvtCellInfo" align=left >&nbsp;</td>
							   {/if}
                                   {/foreach}
						      </tr>	
						   {/foreach}	
						     </table>
                     	                      </td>
					   </tr>
		<tr>                                                                                                               <td style="padding:10px">
			{/foreach}
                    {*-- End of Blocks--*} 
			</td>
                </tr>
		<!-- Inventory - Product Details informations -->
		   <tr>
			{$ASSOCIATED_PRODUCTS}
		   </tr>
		</table>
		</td>
		<td width=20% valign=top style="border-left:2px dashed #cccccc;padding:13px">
						<!-- right side relevant info -->

					<!-- Mail Merge-->
					<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
    					<tr>
    					   <td class="rightMailMergeHeader"><b>Tag Cloud</b></td>
    					</tr>
    					<tr style="height:25px">
    						<td class="rightMailMergeContent">
        						<table border=0 cellspacing=0 cellpadding=0 width=100% >
          						<tr><td>
          						<table width="250" border="0" cellspacing="0" cellpadding="0">
            						<tr>
            						  <td colspan="3"><img src="{$IMAGE_PATH}cloud_top.gif" width=250 height=38 alt=""></td>
            						</tr>
            						<tr>
              						<td width="16" height="10"><img src="{$IMAGE_PATH}cloud_top_left.gif" width="16" height="10"></td>
              						<td width="221" height="10"><img src="{$IMAGE_PATH}tagcloud_03.gif" width="221" height="10"></td>
              						<td width="13" height="10"><img src="{$IMAGE_PATH}cloud_top_right.gif" width="13" height="10"></td>
            						</tr>
            						<tr>
            						  <td class="cloudLft"></td>
            						  <td><span id="tagfields"></span></td>
            						  <td class="cloudRht"></td>
            						</tr>
            						<tr>
            						<td width="16" height="13"><img src="{$IMAGE_PATH}cloud_btm_left.gif" width="16" height="13"></td>
            						<td width="221" height="13"><img src="{$IMAGE_PATH}cloud_btm_bdr.gif" width="221" height="13"></td>
            						<td width="13" height="13"><img src="{$IMAGE_PATH}cloud_btm_right.gif" width="13" height="13"></td>
            						</tr>
          						</table>
      						    </td></tr>
    						    </table>
    					</td>
    				</tr>
				</table>
				<br>
				{if $MERGEBUTTON eq 'permitted'}
  				<table border=0 cellspacing=0 cellpadding=0 width=100% class="rightMailMerge">
      				<tr>
      					   <td class="rightMailMergeHeader"><b>{$WORDTEMPLATEOPTIONS}</b></td>
      				</tr>
      				<tr style="height:25px">
      						<td class="rightMailMergeContent">
          						<select name="mergefile">{foreach key=templid item=tempflname from=$TOPTIONS}<option value="{$templid}">{$tempflname}</option>{/foreach}</select>
          						<input value="Merge" onclick="this.form.action.value='Merge';" type="submit"></input>
      					  </td>
      				</tr>
  				</table>
				{/if}
			</td>
		</tr>
		</table>
		
			
			
		
		</div>
		<!-- PUBLIC CONTENTS STOPS-->
	</td>
</tr>
</table>

{if $MODULE eq 'Products'}
<script language="JavaScript" type="text/javascript" src="modules/Products/Productsslide.js"></script>
<script language="JavaScript" type="text/javascript">Carousel();</script>
{/if}

<script>
var data = "module={$MODULE}&action={$MODULE}Ajax&file=TagCloud&ajxaction=GETTAGCLOUD";
var ajaxObj = new VtigerAjax(ajaxTagCloudResp);
ajaxObj.process("index.php?",data);
function ajaxTagCloudResp(response)
{ldelim}
	var item = response.responseText;
	getObj('tagfields').innerHTML = item;
	document.getElementById('txtbox_tagfields').value ='';	
{rdelim}
</script>
<!-- added for validation -->
<script language="javascript">
  var fieldname = new Array({$VALIDATION_DATA_FIELDNAME});
  var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL});
  var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE});
</script>
</td>

	<td align=right valign=top><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
</tr></table></form>

