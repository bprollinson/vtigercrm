
<script language="javascript">
        var fieldname = new Array({$VALIDATION_DATA_FIELDNAME})
        var fieldlabel = new Array({$VALIDATION_DATA_FIELDLABEL})
        var fielddatatype = new Array({$VALIDATION_DATA_FIELDDATATYPE})
</script>


<body class=small>
{include file='QuickCreateHidden.tpl'}
<table border=0 cellspacing=0 cellpadding=0 width=400 class=qcWindow>
<tr>
<td>
	<table border=0 cellspacing=0 cellpadding=0 width=400 class=small>
	<tr>	<td class="qcHeader" background="{$IMAGE_PATH}qcBg.gif"><b >Create {$MODULE}</b></td><td class="qcHeader" align=right><img src="{$IMAGE_PATH}qcName.gif"></td></tr>
	</table>

	<table border=0 cellspacing=0 cellpadding=5 width=400 class=small>
	<tr>
		<td>
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=small>
				{foreach item=subdata from=$QUICKCREATE}
		<tr>
			
				{foreach key=mainlabel item=maindata from=$subdata}
		<td>
							   {assign var="uitype" value="$maindata[0][0]"}


                                                           {assign var="fldlabel" value="$maindata[1][0]"}
							   {assign var="fldlabel_sel" value="$maindata[1][1]"}
                                                           {assign var="fldlabel_combo" value="$maindata[1][2]"}
                                                           {assign var="fldname" value="$maindata[2][0]"}
                                                           {assign var="fldvalue" value="$maindata[3][0]"}
                                                           {assign var="secondvalue" value="$maindata[3][1]"}
							{if $uitype eq 2}
							<td width=20% class="dvtCellLabel" align=right><font color="red">*</font>{$fldlabel}</td>
							<td width=30% align=left class="dvtCellInfo"><input type="text" name="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></td>
							{elseif $uitype eq 11 || $uitype eq 1 || $uitype eq 13 || $uitype eq 7 || $uitype eq 9}
							<td width=20% class="dvtCellLabel" align=right>{$fldlabel}</td>
							{if $fldname eq 'tickersymbol' && $MODULE eq 'Accounts'}
                                                        <td width=30% align=left class="dvtCellInfo"><input type="text" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn';" onBlur="this.className='detailedViewTextBox';{if $fldname eq 'tickersymbol' && $MODULE eq 'Accounts'}sensex_info(){/if}"><span id="vtbusy_info" style="display:none;"><img src="themes/blue/images/vtbusy.gif" border="0"></span></td>
                                                        {else}
                                                        <td width=30% align=left class="dvtCellInfo"><input type="text" name="{$fldname}" id ="{$fldname}" value="{$fldvalue}" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"></td>
                                                        {/if}
							{elseif $uitype eq 19 || $uitype eq 20}
							 <td width=20% class="dvtCellLabel" align=right>
							    {if $uitype eq 20}<font color="red">*</font>{/if}
								{$fldlabel}</td>
							 <td colspan=3><textarea class="detailedViewTextBox" onFocus="this.className='detailedViewTextBoxOn'" name="{$fldname}"  onBlur="this.className='detailedViewTextBox'" cols="90" rows="8">{$fldvalue}</textarea>
                                                         </td>
							{elseif $uitype eq 21 || $uitype eq 24}
							  <td width=20% class="dvtCellLabel" align=right>
							     {if $uitype eq 24}
								<font color="red">*</font>
							     {/if}
							     {$fldlabel}
							  </td>
                                                          <td width=30% align=left class="dvtCellInfo"><textarea value="{$fldvalue}" name="{$fldname}"  class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" rows=2>{$fldvalue}</textarea></td>
							{elseif $uitype eq 15 || $uitype eq 16}
							<td width="20%" class="dvtCellLabel" align=right>
								{if $uitype eq 16}
							           <font color="red">*</font>
								{/if}
								{$fldlabel}
							</td>
							<td width="30%" align=left class="dvtCellInfo">
							   <select name="{$fldname}">
								{foreach item=arr from=$fldvalue}
									{foreach key=sel_value item=value from=$arr}
										<option value="{$sel_value}" {$value}>{$sel_value}</option>
									{/foreach}
									
								{/foreach}
							   </select>
							</td>

							{elseif $uitype eq 53}
                                                        <td width="20%" class="dvtCellLabel" align=right>
                                                           {$fldlabel}
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
																<input type="radio" name="assigntype" {$select_user} value="U" onclick="toggleAssignType(this.value)">&nbsp;User
     {if $secondvalue neq ''}
	<input type="radio" name="assigntype" {$select_group} value="T" onclick="toggleAssignType(this.value)">&nbsp;Team
     {/if}									
	<span id="assign_user" style="{$style_user}">
        <select name="assigned_user_id">
        {foreach key=key_one item=arr from=$fldvalue}
        {foreach key=sel_value item=value from=$arr}
        <option value="{$key_one}" {$value}>{$sel_value}</option>
        {/foreach}
        {/foreach}
        </select></span>
        {if $secondvalue neq ''}
        <span id="assign_team" style="{$style_group}">
        <select name="assigned_group_name">';
        {foreach key=key_one item=arr from=$secondvalue}
        {foreach key=sel_value item=value from=$arr}
        <option value="{$sel_value}" {$value}>{$sel_value}</option>
        {/foreach}
        {/foreach}
        </select></span>
        {/if}
        </td>
		{elseif $uitype eq 52 || $uitype eq 77}
                                                        <td width="20%" class="dvtCellLabel" align=right>
							   {$fldlabel}
							</td>
                                                        <td width="30%" align=left class="dvtCellInfo">
								{if $uitype eq 52}
                                                           	   <select name="assigned_user_id">
								{elseif $uitype eq 77}
								   <select name="assigned_user_id1">
								{else}
								   <select name="{$fldname}">
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
								{else}
									{assign var='popuptype' value = 'specific_contact_account_address'}
								{/if}
							<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input readonly name="account_name" style="border:1px solid #bababa;" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype={$popuptype}&form=TasksEditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.account_id.value=''; this.form.account_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							
							{elseif $uitype eq 50}
							<td width="20%" class="dvtCellLabel" align=right><font color="red">*</font>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input readonly name="account_name" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific&form=TasksEditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							{elseif $uitype eq 73}
                                                        <td width="20%" class="dvtCellLabel" align=right><font color="red">*</font>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input readonly name="account_name" type="text" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Accounts&action=Popup&popuptype=specific_account_address&form=TasksEditView&form_submit=false","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							
							{elseif $uitype eq 75 || $uitype eq 81}
                                                          <td width="20%" class="dvtCellLabel" align=right>
                                                                {if $uitype eq 81}
								   <font color="red">*</font>
									{assign var="pop_type" value="specific_vendor_address"}
								{else}{assign var="pop_type" value="specific"}
                                                                {/if}
                                                                {$fldlabel}
                                                          </td>
                                                          <td width="30%" align=left class="dvtCellInfo"><input name="vendor_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Vendors&action=Popup&html=Popup_picker&popuptype={$pop_type}&form=EditView","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>
							  {if $uitype eq 75}
                                                           &nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.vendor_id.value='';this.form.vendor_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							  {/if}
							{elseif $uitype eq 57}
							<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input name="contact_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Contacts&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.contact_id.value=''; this.form.contact_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>

							{elseif $uitype eq 80}
							<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input name="salesorder_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=SalesOrder&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.salesorder_id.value=''; this.form.salesorder_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							
							 {elseif $uitype eq 78}
							 <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							 <td width="30%" align=left class="dvtCellInfo"><input name="quote_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$ID}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Quotes&action=Popup&html=Popup_picker&popuptype=specific&form=EditView","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.quote_id.value=''; this.form.quote_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>

							{elseif $uitype eq 76}
                                                        <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
                                                        <td width="30%" align=left class="dvtCellInfo"><input name="potential_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}"><input name="{$fldname}" type="hidden" value="{$secondvalue}">&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module=Potentials&action=Popup&html=Popup_picker&popuptype=specific_potential_account_address&form=EditView","test","width=600,height=400,resizable=1,scrollbars=1");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.potential_id.value=''; this.form.potential_name.value='';return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>

							{elseif $uitype eq 17}
							<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo">&nbsp;&nbsp;http://&nbsp;<input type="text" name="{$fldname}" style="border:1px solid #bababa;" size="27" onFocus="this.className='detailedViewTextBoxOn'"onBlur="this.className='detailedViewTextBox'" value="{$fldvalue}"></td>
							
							{elseif $uitype eq 71 || $uitype eq 72}
							<td width="20%" class="dvtCellLabel" align=right>
							   {if $uitype eq 72}
								<font color="red">*</font>
							   {/if}
							   {$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo"><input name="{$fldname}" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value="{$fldvalue}"></td>
							
							{elseif $uitype eq 56}
                                                        <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							{if $fldname eq 'notime' && $ACTIVITY_MODE eq 'Events'}
                                                                {if $fldvalue eq 1}
                                                                <td width="30%" align=left class="dvtCellInfo"><input name="{$fldname}" type="checkbox"  onclick="toggleTime()" checked></td>
                                                                {else}
                                                                <td width="30%" align=left class="dvtCellInfo"><input name="{$fldname}" type="checkbox" onclick="toggleTime()" ></td>
                                                                {/if}
                                                        {else}
                                                                {if $fldvalue eq 1}
                                                        <td width="30%" align=left class="dvtCellInfo"><input name="{$fldname}" type="checkbox"  checked></td>
                                                                {else}
                                                        <td width="30%" align=left class="dvtCellInfo"><input name="{$fldname}" type="checkbox"></td>
                                                                {/if}
                                                        {/if}
							{elseif $uitype eq 23 || $uitype eq 5 || $uitype eq 6}
							<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							<td width="30%" align=left class="dvtCellInfo">
							   {foreach key=date_value item=time_value from=$fldvalue}
								{assign var=date_val value="$date_value"}
								{assign var=time_val value="$time_value"}
							   {/foreach}
							<input name="{$fldname}" id="jscal_field_{$fldname}" type="text" style="border:1px solid #bababa;" size="11" maxlength="10" value="{$date_val}">
							<img src="{$IMAGE_PATH}calendar.gif" id="jscal_trigger_{$fldname}">
							{if $uitype eq 6}
							   <input name="time_start" style="border:1px solid #bababa;" size="5" maxlength="5" type="text" value="{$time_val}">
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
							<script type="text/javascript">
                					Calendar.setup ({ldelim}
								inputField : "jscal_field_{$fldname}", ifFormat : "{$dateFormat}", showsTime : false, button : "jscal_trigger_{$fldname}", singleClick : true, step : 1
									{rdelim})
     						        </script>

							
							</td>

							{elseif $uitype eq 63}
							  <td width="20%" class="dvtCellLabel" align=right>
							        {$fldlabel}
							  </td>
							  <td width="30%" align=left class="dvtCellInfo">
							        <input name="{$fldname}" type="text" size="2" value="{$fldvalue}">&nbsp;
							        <select name="duration_minutes">
						        	{foreach key=labelval item=selectval from=$secondvalue}
								<option value="{$labelval}" {$selectval}>{$labelval}</option>
								{/foreach}
								</select>

							{elseif $uitype eq 68 || $uitype eq 66 || $uitype eq 62}
							  <td width="20%" class="dvtCellLabel" align=right>
								<select name="parent_type" onChange='document.EditView.parent_name.value=""; document.EditView.parent_id.value=""'>
								{section name=combo loop=$fldlabel}
                                                                <option value="{$fldlabel_combo[combo]}" {$fldlabel_sel[combo]}>{$fldlabel[combo]}</option>
                                                                {/section}
								</select>
							  </td>
							<td width="30%" align=left class="dvtCellInfo">
							<input name="{$fldname}" type="hidden" value="{$secondvalue}"><input name="parent_name" readonly type="text" style="border:1px solid #bababa;" value="{$fldvalue}">
						&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView","test","width=600,height=400,resizable=1,scrollbars=1,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>
							
							{elseif $uitype eq 357}
								<td width="20%" class="dvtCellLabel" align=right>To:&nbsp;</td>
								<td width="90%" colspan="3">
								<input name="{$fldname}" type="hidden" value="{$secondvalue}">
								<textarea readonly name="parent_name" cols="70" rows="2">{$fldvalue}</textarea>&nbsp;
								<select name="parent_type">
									{foreach key=labelval item=selectval from=$fldlabel}
		                                                                <option value="{$labelval}" {$selectval}>{$labelval}</option>
                	                                                {/foreach}
                                                                </select>
								&nbsp;<img src="{$IMAGE_PATH}select.gif" alt="Select" title="Select" LANGUAGE=javascript onclick='return window.open("index.php?module="+ document.EditView.parent_type.value +"&action=Popup&html=Popup_picker&form=HelpDeskEditView","test","width=600,height=400,resizable=1,scrollbars=1,top=150,left=200");' align="absmiddle" style='cursor:hand;cursor:pointer'>&nbsp;<input type="image" src="{$IMAGE_PATH}clear_field.gif" alt="Clear" title="Clear" LANGUAGE=javascript onClick="this.form.parent_id.value=''; this.form.parent_name.value=''; return false;" align="absmiddle" style='cursor:hand;cursor:pointer'></td>
								<tr style="height:25px">
								<td width="20%" class="dvtCellLabel" align=right>CC:&nbsp;</td>	
								<td width="30%" align=left class="dvtCellInfo">
								<input name="ccmail" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value=""></td>
								<td width="20%" class="dvtCellLabel" align=right>BCC:&nbsp;</td>
                                                                <td width="30%" align=left class="dvtCellInfo">
                                                                <input name="bccmail" type="text" class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'"  value=""></td>
								</tr>
				
 	                                                {elseif $uitype eq 59}
                                                          <td width="20%" class="dvtCellLabel" align=right>
                                                           {$fldlabel}</td>
                                                          <td width="30%" align=left class="dvtCellInfo">
		
							{elseif $uitype eq 55} 
                                                          <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
							  <td width="30%" align=left class="dvtCellInfo">
							<input type="text" name="{$fldname}"  class=detailedViewTextBox onFocus="this.className='detailedViewTextBoxOn'" onBlur="this.className='detailedViewTextBox'" value= "{$secondvalue}">
                                                 </td>
						 
						{elseif $uitype eq 22}
                                                          <td width="20%" class="dvtCellLabel" align=right><font color="red">*</font>{$fldlabel}</td>
							  <td width="30%" align=left class="dvtCellInfo">
								<textarea name="{$fldname}" cols="30" rows="2">{$fldvalue}</textarea>
                                                 </td>

						{elseif $uitype eq 69}
						<td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
						<td colspan="3" width="30%" align=left class="dvtCellInfo">
						{if $MODULE eq 'Products'}
							<input name="imagelist" type="hidden" value="">
						    <div id="files_list" style="border: 1px solid grey; width: 500px; padding: 5px; background: rgb(255, 255, 255) none repeat scroll 0%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; font-size: x-small">Files Maximum 6
						    <input id="my_file_element" type="file" name="file_1" >
                            </div>
                            <script>
                            {*<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->*}
                            var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 6 );
                            {*<!-- Pass in the file element -->*}
                            multi_selector.addElement( document.getElementById( 'my_file_element' ) );
                            </script>
	                     </td>
                         {else}
                         <input name="{$fldname}"  type="file" value="{$secondvalue}"/><input type="hidden" name="id" value=""/>{$fldvalue}</td>
                         {/if}
				
                         {elseif $uitype eq 61}
                         <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
						 <td colspan="3" width="30%" align=left class="dvtCellInfo"><input name="{$fldname}"  type="file" value="{$secondvalue}"/><input type="hidden" name="id" value=""/>{$fldvalue}</td>
						{elseif $uitype eq 30}
                                                <td width="20%" class="dvtCellLabel" align=right>{$fldlabel}</td>
                                                <td colspan="3" width="30%" align=left class="dvtCellInfo">
							{assign var=check value=$secondvalue[0]}
                                                        {assign var=yes_val value=$secondvalue[1]}
                                                        {assign var=no_val value=$secondvalue[2]}
                                                <input type="radio" name="set_reminder" value="Yes" {$check}>&nbsp;{$yes_val}&nbsp;<input type="radio" name="set_reminder" value="No">&nbsp;{$no_val}&nbsp;
                                                {foreach item=val_arr from=$fldvalue}
                                                        {assign var=start value="$val_arr[0]"}
                                                        {assign var=end value="$val_arr[1]"}
                                                        {assign var=sendname value="$val_arr[2]"}
                                                        {assign var=disp_text value="$val_arr[3]"}
                                                        {assign var=sel_val value="$val_arr[4]"}
                                                          <select name="{$sendname}">
                                                                {section name=reminder start=$start max=$end loop=$end step=1 }
                                                                {if $smarty.section.reminder.index eq $sel_val}
                                                                        {assign var=sel_value value="SELECTED"}
                                                                {/if}
                                                                <OPTION VALUE="{$smarty.section.reminder.index}" "{$sel_value}">{$smarty.section.reminder.index}</OPTION>
                                                                {/section}
                                                          </select>
                                                        &nbsp;{$disp_text}
                                                {/foreach}
                                                </td>

							{/if}
							{/foreach}
						</tr>
							{/foreach}
					
					
					</table>	
		
		<!-- save cancel buttons -->
		<table border=0 cellspacing=0 cellpadding=5 width=100% class=qcTransport>
		<tr>
			<td width=50% align=right><input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="small" type="submit" name="button" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " style="width:70px" ></td>
			<td width=50% align=left>
				<input title="{$APP.LBL_CANCEL_BUTTON_TITLE}" accessKey="{$APP.LBL_CANCEL_BUTTON_KEY}" class="small" onclick="fninvsh('qcform');" type="button" name="button" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " style="width:70px">
			</td>
		</tr>
		</table>

		</td>
	<tr>
	</table>
</td>
</tr>
</table>
</form>
</body>
