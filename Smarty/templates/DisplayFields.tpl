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

<!-- Added this file to display the fields in Create Entity page based on ui types  -->
{foreach key=label item=subdata from=$data}
	{if $header eq 'Product Details'}
		<tr>
	{else}
		<tr style="height:25px">
	{/if}
	{foreach key=mainlabel item=maindata from=$subdata}
		{assign var="uitype" value="$maindata[0][0]"}
		{assign var="fldlabel" value="$maindata[1][0]"}
		{assign var="fldlabel_sel" value="$maindata[1][1]"}
		{assign var="fldlabel_combo" value="$maindata[1][2]"}
		{assign var="fldname" value="$maindata[2][0]"}
		{assign var="fldvalue" value="$maindata[3][0]"}
		{assign var="secondvalue" value="$maindata[3][1]"}
		{assign var="thirdvalue" value="$maindata[3][2]"}
		{assign var="vt_tab" value="$maindata[4][0]"}

		{* vtlib customization *}
		{if $uitype eq '10'}
			<td width=20% class="dvtCellLabel" align=right>
			{$fldlabel.displaylabel}<br>

			{if count($fldlabel.options) eq 1}
				{assign var="use_parentmodule" value=$fldlabel.options.0}
				<input type='hidden' class='small' name="{$fldname}_type" value="{$use_parentmodule}">{$APP.$use_parentmodule}
			{else}
			<select id="{$fldname}_type" class="small" name="{$fldname}_type" onChange='document.EditView.{$fldname}_display.value=""; document.EditView.{$fldname}.value="";$("qcform").innerHTML=""'>
			{foreach item=option from=$fldlabel.options}
				<option value="{$option}" 
				{if $fldlabel.selected == $option}selected{/if}>
				{if $APP.$option neq ''}{$APP.$option}{else}{$option}{/if}
				</option> 
			{/foreach}
			</select>
			{/if}
			{if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input id="{$fldname}" name="{$fldname}" type="hidden" value="{$fldvalue.entityid}" id="{$fldname}">
				<input id="{$fldname}_display" name="{$fldname}_display" id="edit_{$fldname}_display" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue.displayvalue}">&nbsp;
				<img src="{$IMAGE_PATH}select.gif" tabindex="{$vt_tab}" 
alt="Select" title="Select" LANGUAGE=javascript  onclick='return window.open("index.php?module="+ document.EditView.{$fldname}_type.value +"&action=Popup&html=Popup_picker&form=vtlibPopupView&forfield={$fldname}&srcmodule={$MODULE}&forrecord={$ID}","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
				<input type="image" src="{$IMAGE_PATH}clear_field.gif" 
alt="Clear" title="Clear" LANGUAGE=javascript	onClick="this.form.{$fldname}.value=''; this.form.{$fldname}_display.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;
			</td>
		{* END *}
		{elseif $uitype eq 2}
			<td width=20% class="dvtCellLabel" align=right>
				<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small">{/if}
			</td>
			<td width=30% align=left class="dvtCellInfo">
				<input type="text" name="{$fldname}" tabindex="{$vt_tab}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</td>
		{elseif $uitype eq 3 || $uitype eq 4}<!-- Non Editable field, only configured value will be loaded -->
				<td width=20% class="dvtCellLabel" align=right>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small">{/if}</td>
                                <td width=30% align=left class="dvtCellInfo"><input readonly type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" {if $MODE eq 'edit'} value="{$fldvalue}" {else} value="{$MOD_SEQ_ID}" {/if} class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></td>
		{elseif $uitype eq 11 || $uitype eq 1 || $uitype eq 13 || $uitype eq 7 || $uitype eq 9}
			<td width=20% class="dvtCellLabel" align=right>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}</td>

			{if $fldname eq 'tickersymbol' && $MODULE eq 'Accounts'}
				<td width=30% align=left class="dvtCellInfo">
					<input type="text" name="{$fldname}" tabindex="{$vt_tab}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn';" onBlur="this.className='detailedViewTextBox';{if $fldname eq 'tickersymbol' && $MODULE eq 'Accounts'}sensex_info(){/if}">
					<span id="vtbusy_info" style="display:none;">
						<img src="{$IMAGE_PATH}vtbusy.gif" border="0"></span>
				</td>

			{else}
				<td width=30% align=left class="dvtCellInfo"><input type="text" tabindex="{$vt_tab}" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></td>
			{/if}
		{elseif $uitype eq 19 || $uitype eq 20}
			<!-- In Add Comment are we should not display anything -->
			{if $fldlabel eq $MOD.LBL_ADD_COMMENT}
				{assign var=fldvalue value=""}
			{/if}
			<td width=20% class="dvtCellLabel" align=right>
				{if $uitype eq 20}
					<font color="red">*</font>
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td colspan=3>
				<textarea class="detailedViewTextBox" tabindex="{$vt_tab}" onFocus="this.className='detailedViewTextBoxOn'" name="{$fldname}"  onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$fldvalue}</textarea>
				{if $fldlabel eq $MOD.Solution}
				<input type = "hidden" name="helpdesk_solution" value = '{$fldvalue}'>
				{/if}
			</td>
		{elseif $uitype eq 21 || $uitype eq 24}
			<td width=20% class="dvtCellLabel" align=right>
				{if $uitype eq 24}
					<font color="red">*</font>
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width=30% align=left class="dvtCellInfo">
				<textarea value="{$fldvalue}" name="{$fldname}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" rows=2>{$fldvalue}</textarea>
			</td>
		{elseif $uitype eq 15 || $uitype eq 16} <!-- uitype 111 added for noneditable existing picklist values - ahmed -->
			<td width="20%" class="dvtCellLabel" align=right>
				{if $uitype eq 16}
					<font color="red">*</font>
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			   <select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{foreach item=arr from=$fldvalue}
					{if $arr[0] eq $APP.LBL_NOT_ACCESSIBLE}
					<option value="{$arr[0]}" {$arr[2]}>
						{$arr[0]}
					</option>
					{else}
					<option value="{$arr[1]}" {$arr[2]}>
                                                {$arr[0]}
                                        </option>
					{/if}
				{/foreach}
			   </select>
			</td>
		{elseif $uitype eq 33}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			   <select MULTIPLE name="{$fldname}[]" size="4" style="width:160px;" tabindex="{$vt_tab}" class="small">
				{foreach item=arr from=$fldvalue}
					<option value="{$arr[1]}" {$arr[2]}>
                                                {$arr[0]}
                                        </option>
				{/foreach}
			   </select>
			</td>

		{elseif $uitype eq 53}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				{assign var=check value=1}
				{foreach key=key_one item=arr from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						{if $value ne ''}
							{assign var=check value=$check*0}
						{else}
							{assign var=check value=$check*1}
						{/if}
					{/foreach}
				{/foreach}

				{if $check eq 0}
					{assign var=select_user value='checked'}
					{assign var=style_user value='display:block'}
					{assign var=style_group value='display:none'}
				{else}
					{assign var=select_group value='checked'}
					{assign var=style_user value='display:none'}
					{assign var=style_group value='display:block'}
				{/if}

				<input type="radio" tabindex="{$vt_tab}" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)" >&nbsp;{$APP.LBL_USER}

				{if $secondvalue neq ''}
					<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;{$APP.LBL_GROUP}
				{/if}
				
				<span id="assign_user" style="{$style_user}">
					<select name="assigned_user_id" class="small">
						{foreach key=key_one item=arr from=$fldvalue}
							{foreach key=sel_value item=value from=$arr}
								<option value="{$key_one}" {$value}>{$sel_value}</option>
							{/foreach}
						{/foreach}
					</select>
				</span>

				{if $secondvalue neq ''}
					<span id="assign_team" style="{$style_group}">
						<select name="assigned_group_id" class="small">';
							{foreach key=key_one item=arr from=$secondvalue}
								{foreach key=sel_value item=value from=$arr}
									<option value="{$key_one}" {$value}>{$sel_value}</option>
								{/foreach}
							{/foreach}
						</select>
					</span>
				{/if}
			</td>
		{elseif $uitype eq 52 || $uitype eq 77}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				{if $uitype eq 52}
					<select name="assigned_user_id" class="small">
				{elseif $uitype eq 77}
					<select name="assigned_user_id1" tabindex="{$vt_tab}" class="small">
				{else}
					<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
				{/if}

				{foreach key=key_one item=arr from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						<option value="{$key_one}" {$value}>{$sel_value}</option>
					{/foreach}
				{/foreach}
				</select>
			</td>
		{elseif $uitype eq 51}
			{if $MODULE eq 'Accounts'}
				{assign var='popuptype' value = 'specific_account_address'}
			{elseif $MODULE eq 'Products'}
				{assign var='popuptype' value = 'inventory_mo'}
			{else}
				{assign var='popuptype' value = 'specific_contact_account_address'}
			{/if}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			{if $MODULE neq 'Products'}
			<td width="30%" align=left class="dvtCellInfo">
				<input readonly name="account_name" style="border:1px solid #bababa;" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype={$popuptype}&form=TasksEditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.account_id.value=''; this.form.account_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>
			{else}
				{if $RETURN_MODULE eq 'Products' && ($RETURN_ID neq '' && $RETURN_ID neq $ID)}
				<td width="30%" align=left class="dvtCellInfo">
					<input readonly name="product_name" style="border:1px solid #bababa;" type="text" value="{$RETURN_NAME}"><input name="{$fldname}" type="hidden" value="{$RETURN_ID}">&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Products&return_module=Products&record_id={$ID}&action=Popup&popuptype={$popuptype}&form=TasksEditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.product_id.value=''; this.form.product_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				</td>
				{else}
				<td width="30%" align=left class="dvtCellInfo">
					<input readonly name="product_name" style="border:1px solid #bababa;" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='if({$IS_PARENT}==0)return window.open("index.php?module=Products&return_module=Products&record_id={$ID}&action=Popup&popuptype={$popuptype}&form=TasksEditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0"); else alert(alert_arr.IS_PARENT);' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.product_id.value=''; this.form.product_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				</td>
				{/if}
			{/if}
		{elseif $uitype eq 50}
			<td width="20%" class="dvtCellLabel" align=right>
				<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input readonly name="account_name" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific&form=TasksEditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>
		{elseif $uitype eq 73}
			<td width="20%" class="dvtCellLabel" align=right>
				<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input readonly name="account_name" id = "single_accountid" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific_account_address&form=TasksEditView&form_submit=false","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 75 || $uitype eq 81}
			<td width="20%" class="dvtCellLabel" align=right>
				{if $uitype eq 81}
					<font color="red">*</font>
					{assign var="pop_type" value="specific_vendor_address"}
					{else}{assign var="pop_type" value="specific"}
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="vendor_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Vendors&action=Popup&html=Popup_picker&popuptype={$pop_type}&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>
				{if $uitype eq 75}
					&nbsp;<input type="image" tabindex="{$vt_tab}" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.vendor_id.value='';this.form.vendor_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
				{/if}
			</td>
		{elseif $uitype eq 57}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			
				<input name="contact_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='selectContact("false","general",document.EditView)' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.contact_id.value=''; this.form.contact_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>
		
		{elseif $uitype eq 58}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="campaignname" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Campaigns&action=Popup&html=Popup_picker&popuptype=specific_campaign&form=EditView","test","width=640,height=602,resizable=0,scrollbars=0");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.campaignid.value=''; this.form.campaignname.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 80}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="salesorder_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='selectSalesOrder();' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" tabindex="{$vt_tab}" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.salesorder_id.value=''; this.form.salesorder_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 78}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small">{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="quote_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}" >&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='selectQuote()' align="absmiddle" style='cursor:hand;cursor:pointer' >&nbsp;<input type="image" tabindex="{$vt_tab}" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.quote_id.value=''; this.form.quote_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 76}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="potential_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='selectPotential()' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.potential_id.value=''; this.form.potential_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 17}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				&nbsp;&nbsp;http://
			<input style="width:74%;" class = 'detailedViewTextBoxOn' type="text" tabindex="{$vt_tab}" name="{$fldname}" style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" onkeyup="validateUrl('{$fldname}');" value="{$fldvalue}">
			</td>

		{elseif $uitype eq 85}
                        <td width="20%" class="dvtCellLabel" align=right>
                                {$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
                        </td>
                        <td width="30%" align=left class="dvtCellInfo">
                                <img src="{$IMAGE_PATH}skype.gif" alt="Skype" title="Skype" LANGUAGE=javascript align="absmiddle"></img><input type="text" tabindex="{$vt_tab}" name="{$fldname}" style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" value="{$fldvalue}">
                        </td>

		{elseif $uitype eq 71 || $uitype eq 72}
			<td width="20%" class="dvtCellLabel" align=right>
				{if $uitype eq 72}
					<font color="red">*</font>
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">				
				{if $fldname eq "unit_price"}
					<span id="multiple_currencies">
						<input name="{$fldname}" id="{$fldname}" tabindex="{$vt_tab}" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'; updateUnitPrice('unit_price', '{$BASE_CURRENCY}');"  value="{$fldvalue}" style="width:60%;">
					{if $MASS_EDIT neq 1}
						&nbsp;<a href="javascript:void(0);" onclick="updateUnitPrice('unit_price', '{$BASE_CURRENCY}'); toggleShowHide('currency_class','multiple_currencies');">{$APP.LBL_MORE_CURRENCIES} &raquo;</a>
					{/if}
					</span>
					{if $MASS_EDIT neq 1}
					<div id="currency_class" class="multiCurrencyEditUI" width="350">
						<input type="hidden" name="base_currency" id="base_currency" value="{$BASE_CURRENCY}" />
						<input type="hidden" name="base_conversion_rate" id="base_currency" value="{$BASE_CURRENCY}" />
						<table width="100%" height="100%" class="small" cellpadding="5">
						<tr class="detailedViewHeader">
							<th colspan="4">
								<b>{$MOD.LBL_PRODUCT_PRICES}</b>
							</th>
							<th align="right">
								<img border="0" style="cursor: pointer;" onclick="toggleShowHide('multiple_currencies','currency_class');" src="{$IMAGE_PATH}/close.gif"/>
							</th>
						</tr>
						<tr class="detailedViewHeader">
							<th>{$APP.LBL_CURRENCY}</th>
							<th>{$APP.LBL_PRICE}</th>
							<th>{$APP.LBL_CONVERSION_RATE}</th>
							<th>{$APP.LBL_RESET_PRICE}</th>							
							<th>{$APP.LBL_BASE_CURRENCY}</th>
						</tr>
						{foreach item=price key=count from=$PRICE_DETAILS}
							<tr>
								{if $price.check_value eq 1 || $price.is_basecurrency eq 1}
									{assign var=check_value value="checked"}
									{assign var=disable_value value=""}
								{else}
									{assign var=check_value value=""}
									{assign var=disable_value value="disabled=true"}
								{/if}
								
								{if $price.is_basecurrency eq 1}
									{assign var=base_cur_check value="checked"}
								{else}
									{assign var=base_cur_check value=""}
								{/if}
								
								{if $price.curname eq $BASE_CURRENCY}
									{assign var=call_js_update_func value="updateUnitPrice('$BASE_CURRENCY', 'unit_price');"}
								{else}
									{assign var=call_js_update_func value=""}
								{/if}
								
								<td align="right" class="dvtCellLabel">
									{$price.currencylabel} ({$price.currencysymbol})
									<input type="checkbox" name="cur_{$price.curid}_check" id="cur_{$price.curid}_check" class="small" onclick="fnenableDisable(this,'{$price.curid}'); updateCurrencyValue(this,'{$price.curname}','{$BASE_CURRENCY}','{$price.conversionrate}');" {$check_value}>
								</td>
								<td class="dvtCellInfo" align="left">
									<input {$disable_value} type="text" size="10" class="small" name="{$price.curname}" id="{$price.curname}" value="{$price.curvalue}" onBlur="{$call_js_update_func} fnpriceValidation('{$price.curname}');">
								</td>
								<td class="dvtCellInfo" align="left">
									<input disabled=true type="text" size="10" class="small" name="cur_conv_rate{$price.curid}" value="{$price.conversionrate}">
								</td>
								<td class="dvtCellInfo" align="center">
									<input {$disable_value} type="button" class="crmbutton small edit" id="cur_reset{$price.curid}"  onclick="updateCurrencyValue(this,'{$price.curname}','{$BASE_CURRENCY}','{$price.conversionrate}');" value="{$APP.LBL_RESET}"/>
								</td>
								<td class="dvtCellInfo">
									<input {$disable_value} type="radio" class="detailedViewTextBox" id="base_currency{$price.curid}" name="base_currency_input" value="{$price.curname}" {$base_cur_check} onchange="updateBaseCurrencyValue()" />
								</td>
							</tr>
						{/foreach}
						</table>
					</div>
					{/if}
				{else}
					<input name="{$fldname}" tabindex="{$vt_tab}" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value="{$fldvalue}">
				{/if}
			</td>

		{elseif $uitype eq 56}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>

			{if $fldname eq 'notime' && $ACTIVITY_MODE eq 'Events'}
				{if $fldvalue eq 1}
					<td width="30%" align=left class="dvtCellInfo">
						<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" onclick="toggleTime()" checked>
					</td>
				{else}
					<td width="30%" align=left class="dvtCellInfo">
						<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox" onclick="toggleTime()" >
					</td>
				{/if}
			<!-- For Portal Information we need a hidden field existing_portal with the current portal value -->
			{elseif $fldname eq 'portal'}
				<td width="30%" align=left class="dvtCellInfo">
					<input type="hidden" name="existing_portal" value="{$fldvalue}">
					<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" {if $fldvalue eq 1}checked{/if}>
				</td>
			{else}
				{if $fldvalue eq 1}
					<td width="30%" align=left class="dvtCellInfo">
						<input name="{$fldname}" type="checkbox" tabindex="{$vt_tab}" checked>
					</td>
				{else}
					<td width="30%" align=left class="dvtCellInfo">
						<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox" {if ( $PROD_MODE eq 'create' &&  $fldname|substr:0:3 neq 'cf_') ||( $fldname|substr:0:3 neq 'cf_' && $PRICE_BOOK_MODE eq 'create' ) || $USER_MODE eq 'create'}checked{/if}>
					</td>
				{/if}
			{/if}
		{elseif $uitype eq 23 || $uitype eq 5 || $uitype eq 6}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				{foreach key=date_value item=time_value from=$fldvalue}
					{assign var=date_val value="$date_value"}
					{assign var=time_val value="$time_value"}
				{/foreach}

				<input name="{$fldname}" tabindex="{$vt_tab}" id="jscal_field_{$fldname}" type="text" style="border:1px solid #bababa;" size="11" maxlength="10" value="{$date_val}">
				<img src="{$IMAGE_PATH}calendar.gif" id="jscal_trigger_{$fldname}">

				{if $uitype eq 6}
					<input name="time_start" tabindex="{$vt_tab}" style="border:1px solid #bababa;" size="5" maxlength="5" type="text" value="{$time_val}">
				{/if}

				{foreach key=date_format item=date_str from=$secondvalue}
					{assign var=dateFormat value="$date_format"}
					{assign var=dateStr value="$date_str"}
				{/foreach}

				{if $uitype eq 5 || $uitype eq 23}
					<br><font size=1><em old="(yyyy-mm-dd)">({$dateStr})</em></font>
				{else}
					<br><font size=1><em old="(yyyy-mm-dd)">({$dateStr})</em></font>
				{/if}

				<script type="text/javascript" id='massedit_calendar_{$fldname}'>
					Calendar.setup ({ldelim}
						inputField : "jscal_field_{$fldname}", ifFormat : "{$dateFormat}", showsTime : false, button : "jscal_trigger_{$fldname}", singleClick : true, step : 1
					{rdelim})
				</script>


			</td>

		{elseif $uitype eq 63}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="{$fldname}" type="text" size="2" value="{$fldvalue}" tabindex="{$vt_tab}" >&nbsp;
				<select name="duration_minutes" tabindex="{$vt_tab}" class="small">
					{foreach key=labelval item=selectval from=$secondvalue}
						<option value="{$labelval}" {$selectval}>{$labelval}</option>
					{/foreach}
				</select>

		{elseif $uitype eq 68 || $uitype eq 66 || $uitype eq 62}
			<td width="20%" class="dvtCellLabel" align=right>
				<select class="small" name="parent_type" onChange='document.EditView.parent_name.value=""; document.EditView.parent_id.value=""'>
					{section name=combo loop=$fldlabel}
						<option value="{$fldlabel_combo[combo]}" {$fldlabel_sel[combo]}>{$fldlabel[combo]} </option>
					{/section}
				</select>
				{if $MASS_EDIT eq '1'}<input type="checkbox" name="parent_id_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}			
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="{$fldname}" type="hidden" value="{$secondvalue}">
				<input name="parent_name" readonly id = "parentid" type="text" style="border:1px solid #bababa;" value="{$fldvalue}">
				&nbsp;<img src="{$IMAGE_PATH}select.gif" tabindex="{$vt_tab}" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 357}
			<td width="20%" class="dvtCellLabel" align=right>To:&nbsp;</td>
			<td width="90%" colspan="3">
				<input name="{$fldname}" type="hidden" value="{$secondvalue}">
				<textarea readonly name="parent_name" cols="70" rows="2">{$fldvalue}</textarea>&nbsp;
				<select name="parent_type" class="small">
					{foreach key=labelval item=selectval from=$fldlabel}
						<option value="{$labelval}" {$selectval}>{$labelval}</option>
					{/foreach}
				</select>
				&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>
		   <tr style="height:25px">
			<td width="20%" class="dvtCellLabel" align=right>CC:&nbsp;</td>	
			<td width="30%" align=left class="dvtCellInfo">
				<input name="ccmail" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value="">
			</td>
			<td width="20%" class="dvtCellLabel" align=right>BCC:&nbsp;</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="bccmail" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value="">
			</td>
		   </tr>

		{elseif $uitype eq 59}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<input name="{$fldname}" type="hidden" value="{$secondvalue}">
				<input name="product_name" readonly type="text" value="{$fldvalue}">&nbsp;<img tabindex="{$vt_tab}" src="{$IMAGE_PATH}select.gif" alt="{$APP.LBL_SELECT}" title="{$APP.LBL_SELECT}" LANGUAGE=javascript onclick='return window.open("index.php?module=Products&action=Popup&html=Popup_picker&form=HelpDeskEditView&popuptype=specific","test","width=640,height=602,resizable=0,scrollbars=0,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="{$APP.LBL_CLEAR}" title="{$APP.LBL_CLEAR}" LANGUAGE=javascript onClick="this.form.product_id.value=''; this.form.product_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'>
			</td>

		{elseif $uitype eq 55 || $uitype eq 255} 
			{if $uitype eq 55}	
				<td width="20%" class="dvtCellLabel" align=right>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}</td>
			{elseif $uitype eq 255}	
				<td width="20%" class="dvtCellLabel" align=right><font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}</td>
			{/if}
			
			<td width="30%" align=left class="dvtCellInfo">
			{if $fldvalue neq ''}
			<select name="salutationtype" class="small">
				{foreach item=arr from=$fldvalue}
						<option value="{$arr[1]}" {$arr[2]}>
                                                	{$arr[0]}
                                                </option>
				{/foreach}
			</select>
			{/if}
			<input type="text" name="{$fldname}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" style="width:58%;" value= "{$secondvalue}" >
			</td>

		{elseif $uitype eq 22}
			<td width="20%" class="dvtCellLabel" align=right>
				<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				<textarea name="{$fldname}" cols="30" tabindex="{$vt_tab}" rows="2">{$fldvalue}</textarea>
			</td>

		{elseif $uitype eq 69}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel}
				{if $MASS_EDIT eq '1'}
					<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small"  >
				{/if}
			</td>
			<td colspan="3" width="30%" align=left class="dvtCellInfo">
				{if $MODULE eq 'Products'}
					<input name="del_file_list" type="hidden" value="">
					<div id="files_list" style="border: 1px solid grey; width: 500px; padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">{$APP.Files_Maximum_6}
						<input id="my_file_element" type="file" name="file_1" tabindex="{$vt_tab}"  onchange="validateFilename(this)"/>
						<!--input type="hidden" name="file_1_hidden" value=""/-->
						{assign var=image_count value=0}
						{if $maindata[3].0.name neq '' && $DUPLICATE neq 'true'}
						   {foreach name=image_loop key=num item=image_details from=$maindata[3]}
							<div align="center">
								<img src="{$image_details.path}{$image_details.name}" height="50">&nbsp;&nbsp;[{$image_details.orgname}]<input id="file_{$num}" value="Delete" type="button" class="crmbutton small delete" onclick='this.parentNode.parentNode.removeChild(this.parentNode);delRowEmt("{$image_details.orgname}")'>
							</div>
					   	   {assign var=image_count value=$smarty.foreach.image_loop.iteration}
					   	   {/foreach}
						{/if}
					</div>

					<script>
						{*<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->*}
						var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 6 );
						multi_selector.count = {$image_count}
						{*<!-- Pass in the file element -->*}
						multi_selector.addElement( document.getElementById( 'my_file_element' ) );
					</script>
				{else}
					<input name="{$fldname}"  type="file" value="{$maindata[3].0.name}" tabindex="{$vt_tab}" onchange="validateFilename(this);" />
					<input name="{$fldname}_hidden"  type="hidden" value="{$maindata[3].0.name}" />
					<input type="hidden" name="id" value=""/>
					{if $maindata[3].0.name != "" && $DUPLICATE neq 'true'}
						<div id="replaceimage">[{$maindata[3].0.orgname}] <a href="javascript:;" onClick="delimage({$ID})">Del</a></div>
					{/if}
				{/if}
			</td>

		{elseif $uitype eq 61}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel}
				{if $MASS_EDIT eq '1'}
					<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small"  disabled >
				{/if}
			</td>
			<td colspan="3" width="30%" align=left class="dvtCellInfo">
				{if $MODULE neq 'Documents'}
					<input name="{$fldname}"  type="file" value="{$secondvalue}" tabindex="{$vt_tab}" onchange="validateFilename(this)"/>
				{else}
					<input type="text" style="border: 1px solid rgb(186, 186, 186);" readonly name="{$fldname}" id="{$fldname}" value="{$fldvalue}" /> &nbsp;
					<input type="hidden" name="fileid" id="fileid" value="" />
					{if $ID eq ''}
						{assign var=id value=0}
					{else}
						{assign var=id value=$ID}
					{/if}
					<span id="fileaddbutton">
						<a href="javascript:;" onclick="FileAdd(this,'fileLay','EditView',{$id});"><img id="FileAdd_img_id" src="{$IMAGE_PATH}select.gif" alt="Add File..." title="Add File..." border=0></a>
					</span>
				{/if}
				<!-- Start: Popup layer to add file in Documents module -->
				<div id="fileLay" class="layerPopup" style="height:345px;width:500px;z-index:2000;display:none;">
        			<iframe height="345" width="500" name="AddFile" id="AddFile_id"  frameborder="0" scrolling="no" src="index.php?module=Documents&action=DocumentsAjax&file=AddFile" style="margin: 0;" allowTransparency="true"></iframe>
				</div>
				<!-- End: Popup layer to add file in Documents module -->

			</td>
		{elseif $uitype eq 156}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
				{if $fldvalue eq 'on'}
					<td width="30%" align=left class="dvtCellInfo">
						{if ($secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record) || ($MODE == 'create')}
							<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox" checked>
						{else}
							<input name="{$fldname}" type="hidden" value="on">
							<input name="{$fldname}" disabled tabindex="{$vt_tab}" type="checkbox" checked>
						{/if}	
					</td>
				{else}
					<td width="30%" align=left class="dvtCellInfo">
						{if ($secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record) || ($MODE == 'create')}
							<input name="{$fldname}" tabindex="{$vt_tab}" type="checkbox">
						{else}
							<input name="{$fldname}" disabled tabindex="{$vt_tab}" type="checkbox">
						{/if}	
					</td>
				{/if}
		{elseif $uitype eq 98}<!-- Role Selection Popup -->		
			<td width="20%" class="dvtCellLabel" align=right>
			<font color="red">*</font>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			{if $thirdvalue eq 1}
				<input name="role_name" id="role_name" readonly class="txtBox" tabindex="{$vt_tab}" value="{$secondvalue}" type="text">&nbsp;
				<a href="javascript:openPopup();"><img src="{$IMAGE_PATH}select.gif" align="absmiddle" border="0"></a>
			{else}	
				<input name="role_name" id="role_name" tabindex="{$vt_tab}" class="txtBox" readonly value="{$secondvalue}" type="text">&nbsp;
			{/if}	
			<input name="user_role" id="user_role" value="{$fldvalue}" type="hidden">
			</td>
		{elseif $uitype eq 104}<!-- Mandatory Email Fields -->			
			 <td width=20% class="dvtCellLabel" align=right>
			 <font color="red">*</font>
			{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			 </td>
    	     <td width=30% align=left class="dvtCellInfo"><input type="text" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></td>
			{elseif $uitype eq 115}<!-- for Status field Disabled for nonadmin -->
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			   {if $secondvalue eq 1 && $CURRENT_USERID != $smarty.request.record}
			   	<select id="user_status" name="{$fldname}" tabindex="{$vt_tab}" class="small">
			   {else}
			   	<select id="user_status" disabled name="{$fldname}" class="small">
			   {/if} 
				{foreach item=arr from=$fldvalue}
                                        <option value="{$arr[1]}" {$arr[2]} >
                                                {$arr[0]}
                                        </option>
				{/foreach}
			   </select>
			</td>
			{elseif $uitype eq 105}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
				{if $MODE eq 'edit' && $IMAGENAME neq ''}
					<input name="{$fldname}"  type="file" value="{$maindata[3].0.name}" tabindex="{$vt_tab}" onchange="validateFilename(this);" />[{$IMAGENAME}]<br>{$APP.LBL_IMG_FORMATS}
					<input name="{$fldname}_hidden"  type="hidden" value="{$maindata[3].0.name}" />
				{else}
					<input name="{$fldname}"  type="file" value="{$maindata[3].0.name}" tabindex="{$vt_tab}" onchange="validateFilename(this);" /><br>{$APP.LBL_IMG_FORMATS}
					<input name="{$fldname}_hidden"  type="hidden" value="{$maindata[3].0.name}" />
				{/if}
					<input type="hidden" name="id" value=""/>
					{$maindata[3].0.name}
			</td>
			{elseif $uitype eq 103}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" colspan="3" align=left class="dvtCellInfo">
				<input type="text" name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
			</td>	
			{elseif $uitype eq 101}<!-- for reportsto field USERS POPUP -->
				<td width="20%" class="dvtCellLabel" align=right>
			      {$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
	            </td>
				<td width="30%" align=left class="dvtCellInfo">
				<input readonly name='reports_to_name' class="small" type="text" value='{$fldvalue}' tabindex="{$vt_tab}" ><input name='reports_to_id' type="hidden" value='{$secondvalue}'>&nbsp;<input title="Change [Alt+C]" accessKey="C" type="button" class="small" value='{$UMOD.LBL_CHANGE}' name=btn1 LANGUAGE=javascript onclick='return window.open("index.php?module=Users&action=Popup&form=UsersEditView&form_submit=false","test","width=640,height=603,resizable=0,scrollbars=0");'>
	            </td>
			{elseif $uitype eq 116 || $uitype eq 117}<!-- for currency in users details-->	
			<td width="20%" class="dvtCellLabel" align=right>
				{if $uitype eq 117}
					<font color="red">*</font>
				{/if}
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width="30%" align=left class="dvtCellInfo">
			   {if $secondvalue eq 1 || $uitype eq 117}
			   	<select name="{$fldname}" tabindex="{$vt_tab}" class="small">
			   {else}
			   	<select disabled name="{$fldname}" tabindex="{$vt_tab}" class="small">
			   {/if} 

				{foreach item=arr key=uivalueid from=$fldvalue}
					{foreach key=sel_value item=value from=$arr}
						<option value="{$uivalueid}" {$value}>{$sel_value}</option>
						<!-- code added to pass Currency field value, if Disabled for nonadmin -->
						{if $value eq 'selected' && $secondvalue neq 1}
							{assign var="curr_stat" value="$uivalueid"}
						{/if}
						<!--code ends -->
					{/foreach}
				{/foreach}
			   </select>
			<!-- code added to pass Currency field value, if Disabled for nonadmin -->
			{if $curr_stat neq '' && $uitype neq 117}
				<input name="{$fldname}" type="hidden" value="{$curr_stat}">
			{/if}
			<!--code ends -->
			</td>
			{elseif $uitype eq 106}
			<td width=20% class="dvtCellLabel" align=right>
				<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td width=30% align=left class="dvtCellInfo">
				{if $MODE eq 'edit'}
				<input type="text" readonly name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				{else}
				<input type="text" name="{$fldname}" value="{$fldvalue}" tabindex="{$vt_tab}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				{/if}
			</td>
			{elseif $uitype eq 99}
				{if $MODE eq 'create'}
				<td width=20% class="dvtCellLabel" align=right>
					<font color="red">*</font>{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
				</td>
				<td width=30% align=left class="dvtCellInfo">
					<input type="password" name="{$fldname}" tabindex="{$vt_tab}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'">
				</td>
				{/if}
		{elseif $uitype eq 30}
			<td width="20%" class="dvtCellLabel" align=right>
				{$fldlabel} {if $MASS_EDIT eq '1'}<input type="checkbox" name="{$fldname}_mass_edit_check" id="{$fldname}_mass_edit_check" class="small" >{/if}
			</td>
			<td colspan="3" width="30%" align=left class="dvtCellInfo">
				{assign var=check value=$secondvalue[0]}
				{assign var=yes_val value=$secondvalue[1]}
				{assign var=no_val value=$secondvalue[2]}

				<input type="radio" name="set_reminder" tabindex="{$vt_tab}" value="Yes" {$check}>&nbsp;{$yes_val}&nbsp;
				<input type="radio" name="set_reminder" value="No">&nbsp;{$no_val}&nbsp;

				{foreach item=val_arr from=$fldvalue}
					{assign var=start value="$val_arr[0]"}
					{assign var=end value="$val_arr[1]"}
					{assign var=sendname value="$val_arr[2]"}
					{assign var=disp_text value="$val_arr[3]"}
					{assign var=sel_val value="$val_arr[4]"}
					<select name="{$sendname}" class="small">
						{section name=reminder start=$start max=$end loop=$end step=1 }
							{if $smarty.section.reminder.index eq $sel_val}
								{assign var=sel_value value="SELECTED"}
							{else}
								{assign var=sel_value value=""}
							{/if}
							<OPTION VALUE="{$smarty.section.reminder.index}" "{$sel_value}">{$smarty.section.reminder.index}</OPTION>
						{/section}
					</select>
					&nbsp;{$disp_text}
				{/foreach}
			</td>
		{elseif $uitype eq 83} <!-- Handle the Tax in Inventory -->
			{foreach item=tax key=count from=$TAX_DETAILS}
				{if $tax.check_value eq 1}
					{assign var=check_value value="checked"}
					{assign var=show_value value="visible"}
				{else}
					{assign var=check_value value=""}
					{assign var=show_value value="hidden"}
				{/if}
				<td align="right" class="dvtCellLabel" style="border:0px solid red;">
					{$tax.taxlabel} {$APP.COVERED_PERCENTAGE}
					<input type="checkbox" name="{$tax.check_name}" id="{$tax.check_name}" class="small" onclick="fnshowHide(this,'{$tax.taxname}')" {$check_value}>
				</td>
				<td class="dvtCellInfo" align="left" style="border:0px solid red;">
					<input type="text" class="detailedViewTextBox" name="{$tax.taxname}" id="{$tax.taxname}" value="{$tax.percentage}" style="visibility:{$show_value};" onBlur="fntaxValidation('{$tax.taxname}')">
				</td>
			   </tr>
			{/foreach}

			<td colspan="2" class="dvtCellInfo">&nbsp;</td>
		{/if}
	{/foreach}
   </tr>
{/foreach}

<script language="javascript">
	function fnshowHide(currObj,txtObj)
	{ldelim}
			if(currObj.checked == true)
				document.getElementById(txtObj).style.visibility = 'visible';
			else
				document.getElementById(txtObj).style.visibility = 'hidden';
	{rdelim}
	
	function fntaxValidation(txtObj)
	{ldelim}
			if (!numValidate(txtObj,"Tax","any"))
				document.getElementById(txtObj).value = 0;
	{rdelim}	
	
	function fnpriceValidation(txtObj)
	{ldelim}
		if (!numValidate(txtObj,"Price","any"))
			document.getElementById(txtObj).value = 0;
	{rdelim}	

function delimage(id)
{ldelim}
	new Ajax.Request(
		'index.php',
		{ldelim}queue: {ldelim}position: 'end', scope: 'command'{rdelim},
			method: 'post',
			postBody: 'module=Contacts&action=ContactsAjax&file=DelImage&recordid='+id,
			onComplete: function(response)
				    {ldelim}
					if(response.responseText.indexOf("SUCCESS")>-1)
						$("replaceimage").innerHTML='{$APP.LBL_IMAGE_DELETED}';
					else
						alert("{$APP.ERROR_WHILE_EDITING}")
				    {rdelim}
		{rdelim}
	);

{rdelim}

// Function to enable/disable related elements based on whether the current object is checked or not
function fnenableDisable(currObj,enableId)
{ldelim}
	var disable_flag = true;
	if(currObj.checked == true)
		disable_flag = false;
	
	document.getElementById('curname'+enableId).disabled = disable_flag;
	document.getElementById('cur_reset'+enableId).disabled = disable_flag;
	document.getElementById('base_currency'+enableId).disabled = disable_flag;	
{rdelim}

// Update current value with current value of base currency and the conversion rate
function updateCurrencyValue(currObj,txtObj,base_curid,conv_rate)
{ldelim}
	var unit_price = $(base_curid).value;
	//if(currObj.checked == true)
	//{ldelim}
		document.getElementById(txtObj).value = unit_price * conv_rate;
	//{rdelim}
{rdelim}

// Synchronize between Unit price and Base currency value.
function updateUnitPrice(from_cur_id, to_cur_id)
{ldelim}
    var from_ele = document.getElementById(from_cur_id);
    if (from_ele == null) return;
    
    var to_ele = document.getElementById(to_cur_id);
    if (to_ele == null) return;

    to_ele.value = from_ele.value;
{rdelim}

// Update hidden base currency value, everytime the base currency value is changed in multi-currency UI
function updateBaseCurrencyValue()
{ldelim}
    var cur_list = document.getElementsByName('base_currency_input');
    if (cur_list == null) return;
    
    var base_currency_ele = document.getElementById('base_currency');
    if (base_currency_ele == null) return;
    
    for(var i=0; i<cur_list.length; i++) 
    {ldelim}	
		var cur_ele = cur_list[i];
		if (cur_ele != null && cur_ele.checked == true)
    		base_currency_ele.value = cur_ele.value;
	{rdelim}
{rdelim}

function FileAdd(obj,Lay,return_action,crm_id)
{ldelim}
	fnvshobj(obj,Lay);
	window.frames['AddFile'].document.getElementById('divHeader').innerHTML="Add file";
	window.frames['AddFile'].document.FileAdd.return_action.value=return_action;
	window.frames['AddFile'].document.FileAdd.crm_id.value=crm_id;
	positionDivToCenter(Lay);
{rdelim}

</script>

