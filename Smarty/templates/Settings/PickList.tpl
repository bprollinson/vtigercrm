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
<script language="JAVASCRIPT" type="text/javascript" src="include/js/smoothscroll.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script src="include/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="include/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script>
{literal}
function splitvalues() {
	var picklistobj=getobj("listarea")
	var picklistcontent=picklistobj.value
	var picklistary=new array()
	var i=0;
	
	//splitting up of values
	if (picklistcontent.indexof("\n")!=-1) {
		while(picklistcontent.indexof("\n")!=-1) {
			if (picklistcontent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
				picklistary[i]=picklistcontent.substr(0,picklistcontent.indexof("\n")).replace(/^\s+/g, '').replace(/\s+$/g, '')
				picklistcontent=picklistcontent.substr(picklistcontent.indexof("\n")+1,picklistcontent.length)
				i++
			} else break;
		}
	} else if (picklistcontent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
		picklistary[0]=picklistcontent.replace(/^\s+/g, '').replace(/\s+$/g, '')
	}
	
	return picklistary;
}
function setdefaultlist() {
	var picklistary=new array()
	picklistary=splitvalues()
	
	getobj("defaultlist").innerhtml=""
	
	for (i=0;i<picklistary.length;i++) {
		var objoption=document.createelement("option")
		if (browser_ie) {
			objoption.innertext=picklistary[i]
			objoption.value=picklistary[i]
		} else if (browser_nn4 || browser_nn6) {
			objoption.text=picklistary[i]
			objoption.setattribute("value",picklistary[i])
		}
	
		getobj("defaultlist").appendchild(objoption)
	}
}
function validate() {
	if (emptycheck("listarea","picklist values"))	{
		var picklistary=new array()
		picklistary=splitvalues()
		//empty check validation
		for (i=0;i<picklistary.length;i++) {
			if (picklistary[i]=="") {
				alert("picklist value cannot be empty")
				picklistobj.focus()
				return false
			}
		}

		//duplicate values' validation
		for (i=0;i<picklistary.length;i++) {
			for (j=i+1;j<picklistary.length;j++) {
				if (picklistary[i]==picklistary[j]) {
					alert("duplicate values found")
					picklistobj.focus()
					return false
				}
			}
		}

		return true;
	}
}

{/literal}
</script>
<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody><tr>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
        <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">
<br>
	<div align=center>
	
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}picklist.gif" width="48" height="48" border=0 ></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_PICKLIST_EDITOR}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_PICKLIST_DESCRIPTION}</td>
				</tr>
				</table>
				
				
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td valign=top>
				
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
						<td class="big"><strong>1. {$MOD.LBL_SELECT_MODULE}</strong></td>
						<td class="small" align=right>&nbsp;</td>
					</tr>
					</table>
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
						<tr class="small">
                        	<td width="35%" class="small cellLabel"><strong>{$MOD.LBL_SELECT_CRM_MODULE}</strong></td>
	                        <td width="65%" class="cellText" >
					<select name="pickmodule" class="detailedViewTextBox" onChange="changeModule(this);">
					{foreach key=tabid item=module from=$MODULE_LISTS}
						<option value="{$module}">{$APP.$module}</option>
					{/foreach}
					</select>
				</td>
                      </tr>
					</table>
					<br>
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				    <td class="big" rowspan="2">
					<div id="picklist_datas">	
						{include file='Settings/PickListContents.tpl'}
					</div>
				    </td>	
				</td>
				</tr>
			    	</table>
				<table border=0 cellspacing=0 cellpadding=5 width=100% >
					<tr><td class="small" nowrap align=right><a href="#top">{$MOD.LBL_SCROLL}</a></td></tr>
				</table>
				
				</td>
				</tr>
				</table>
			
			
			
			</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
		
	</div>

</td>
        <td valign="top"><img src="{$IMAGE_PATH}showPanelTopRight.gif"></td>
   </tr>
</tbody>
</table>
<div id="editdiv" style="display:block;position:absolute;width:510px;"></div>
{literal}
<script>
function SavePickList(fieldname,module,uitype)
{
	$("status").style.display = "inline";
	Effect.Puff($('editdiv'),{duration:2});
	var body = escape($("picklist_values").value);

	new Ajax.Request(
        	'index.php?action=SettingsAjax&module=Settings&directmode=ajax&file=UpdateComboValues&table_name='+fieldname+'&fld_module='+module+'&listarea='+body+'&uitype='+uitype,
	        {queue: {position: 'end', scope: 'command'},
        		method: 'get',
		        postBody: null,
		        /* postBody: 'action=SettingsAjax&module=Settings&directmode=ajax&file=UpdateComboValues&table_name='+fieldname+'&fld_module='+module+'&listarea='+body, */
		        onComplete: function(response) {
					$("status").style.display="none";
        				$("picklist_datas").innerHTML=response.responseText;
	                        }
        	}
	);
}
function changeModule(pickmodule)
{
	$("status").style.display="inline";
	var module=pickmodule.options[pickmodule.options.selectedIndex].value;
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=SettingsAjax&module=Settings&directmode=ajax&file=PickList&fld_module='+module,
                        onComplete: function(response) {
                                        $("status").style.display="none";
                                        $("picklist_datas").innerHTML=response.responseText;
                                }
                }
        );
}
function fetchEditPickList(module,fieldname,uitype)
{
	$("status").style.display="inline";
	new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'action=SettingsAjax&module=Settings&mode=edit&file=EditComboField&fld_module='+module+'&fieldname='+fieldname+'&uitype='+uitype,
			onComplete: function(response) {
                                        $("status").style.display="none";
                                        $("editdiv").innerHTML=response.responseText;
					Effect.Grow('editdiv');
                	}
                }
        );
}

function picklist_validate(mode,fieldname,module,uitype)
{
	
	//alert(trim($("picklist_values").value));
	
	var pick_arr=new Array();
	pick_arr=trim($("picklist_values").value).split('\n');	
	var len=pick_arr.length;
	for(i=0;i<len;i++)
	{
		var valone;
		curr_iter = i;
		valone=pick_arr[curr_iter];
		for(j=curr_iter+1;j<len;j++)
		{
			var valnext;
			valnext=pick_arr[j];
			if(trim(valone) == trim(valnext))
			{
				alert("Duplicate entries found for the value '"+valone+"'");
				return false;
			}
		}
		i = curr_iter		

	}
	if(mode == 'edit')
	{
		if(trim($("picklist_values").value) == '')
		{
			alert("Picklist value cannot be empty");
			$("picklist_values").focus();	
			return false;
		}
	}
	SavePickList(fieldname,module,uitype)	
}
</script>
{/literal}
