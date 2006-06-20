<?php

/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/


require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
//Constructing the Role Array
$roleDetails=getAllRoleDetails();
$output='';

//Constructing the Group Array
$grpDetails=getAllGroupName();
$combovalues='';

global $adb;
$mode = $_REQUEST['mode'];
if(isset($_REQUEST['shareid']) && $_REQUEST['shareid'] != '')
{	
	$shareid=$_REQUEST['shareid'];
	$shareInfo=getSharingRuleInfo($shareid);
	$tabid=$shareInfo[1];
	$sharing_module=getTabModuleName($tabid);

}
else
{
	$sharing_module=$_REQUEST['sharing_module'];
	$tabid=getTabid($sharing_module);
}

if($mode == 'create')
{
	foreach($roleDetails as $roleid=>$rolename)
	{
		$combovalues .='<option value="roles::'.$roleid.'">Roles::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{
		$combovalues .='<option value="rs::'.$roleid.'">Roles and Subordinates::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$combovalues .='<option value="groups::'.$groupid.'">Group::'.$groupname.'</option>';
	}

	$fromComboValues=$combovalues;
	$toComboValues=$combovalues;

}
elseif($mode == 'edit')
{


	//constructing the from combo values
	$fromtype=$shareInfo[3];
	$fromid=$shareInfo[5];


	foreach($roleDetails as $roleid=>$rolename)
	{
		$selected='';

		if($fromtype == 'roles')
		{
			if($roleid == $fromid)
			{
				$selected='selected';	
			}	
		}
		$fromComboValues .='<option value="roles::'.$roleid.'" '.$selected.'>Roles::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{

		$selected='';
		if($fromtype == 'rs')
		{
			if($roleid == $fromid)
			{
				$selected='selected';	
			}	
		}	
	
		$fromComboValues .='<option value="rs::'.$roleid.'" '.$selected.'>Roles and Subordinates::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$selected='';
		if($fromtype == 'groups')
		{
			if($groupid == $fromid)
			{
				$selected='selected';	
			}	
		}	
		

		$fromComboValues .='<option value="groups::'.$groupid.'" '.$selected.'>Group::'.$groupname.'</option>';
	}

	//constructing the to combo values
	$totype=$shareInfo[4];
	$toid=$shareInfo[6];


	foreach($roleDetails as $roleid=>$rolename)
	{
		$selected='';

		if($totype == 'roles')
		{
			if($roleid == $toid)
			{
				$selected='selected';	
			}	
		}
		$toComboValues .='<option value="roles::'.$roleid.'" '.$selected.'>Roles::'.$rolename[0].'</option>';
	}

	foreach($roleDetails as $roleid=>$rolename)
	{

		$selected='';
		if($totype == 'rs')
		{
			if($roleid == $toid)
			{
				$selected='selected';	
			}	
		}	
	
		$toComboValues .='<option value="rs::'.$roleid.'" '.$selected.'>Roles and Subordinates::'.$rolename[0].'</option>';
	}

	foreach($grpDetails as $groupid=>$groupname)
	{
		$selected='';
		if($totype == 'groups')
		{
			if($groupid == $toid)
			{
				$selected='selected';	
			}	
		}	
		

		$toComboValues .='<option value="groups::'.$groupid.'" '.$selected.'>Group::'.$groupname.'</option>';
	}

}



$relatedmodule='';	
$relatedlistscombo='';
$relatedModuleSharingArr=getRelatedSharingModules($tabid);
$size=sizeof($relatedModuleSharingArr);
if($size > 0)
{
	if($mode=='edit')
	{
		$relatedModuleSharingPerrArr=getRelatedModuleSharingPermission($shareid);
	}
	foreach($relatedModuleSharingArr as $reltabid=>$relmode_shareid)
	{
		$rel_module=getTabModuleName($reltabid);
		$relatedmodule .=$rel_module.'###'; 						
	}
	foreach($relatedModuleSharingArr as $reltabid=>$relmode_shareid)
	{
		$ro_selected='';
		$rw_selected='';
		$rel_module=getTabModuleName($reltabid);
		if($mode=='create')
		{
			$ro_selected='selected';
		}
		elseif($mode=='edit')
		{
			$perr=$relatedModuleSharingPerrArr[$reltabid];
			if($perr == 0)
			{
				$ro_selected='selected';
			}
			elseif($perr == 1)
			{
				$rw_selected='selected';
			}
		}	

		$relatedlistscombo.='<tr><td align="right" nowrap style="padding-right:10px;"><b>'.$rel_module.' :</b></td>
			<td width="70%">';
		$relatedlistscombo.='<select id="'.$rel_module.'_accessopt" name="'.$rel_module.'_accessopt" onChange=fnwriteRules("'.$sharing_module.'","'.$relatedmodule.'")>
			<option value="0" '.$ro_selected.' >Read Only</option>
			<option value="1" '.$rw_selected.' >Read/Write</option>
			</select></td></tr>';


	}
}


if($mode == 'create')
{
	$sharPerCombo = '<option value="0" selected>Read Only</option>';
        $sharPerCombo .= '<option value="1">Read/Write</option>';
}
elseif($mode == 'edit')
{
	$selected1='';
	$selected2='';
	if($shareInfo[7] == 0)
	{
		$selected1='selected';
	}
	elseif($shareInfo[7] == 1)
	{
		$selected2='selected';
	}

	$sharPerCombo = '<option value="0" '.$selected1.'>Read Only</option>';
        $sharPerCombo .= '<option value="1" '.$selected2.'>Read/Write</option>';	
}

	
$output.='<form name="newGroupForm" action="index.php" method="post">
<input type="hidden" name="module" value="Users">
<input type="hidden" name="parenttab" value="Settings">	
<input type="hidden" name="action" value="SaveSharingRule">
<input type="hidden" name="sharing_module" value="'.$sharing_module.'">
<input type="hidden" name="shareId" value="'.$shareid.'">
<input type="hidden" name="mode" value="'.$mode.'">

<div id="sharingRule" class="fixedLay">
<table width="100%" border="0" cellpadding="3" cellspacing="0">
<tr>
<td class="genHeaderSmall" align="left" style="border-bottom:1px solid #CCCCCC;" width="60%">'.$sharing_module.' - Add Custom Privilege Rule</td>
<td align="right" style="border-bottom:1px solid #CCCCCC;" width="40%"><a href="javascript:onClick=hide(\'sharingRule\')";>Close</a></td>

</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td><b>Step 1 :'.$sharing_module.'  of </b>(Select an entity below)</td>
<td>&nbsp;</td>

</tr>
<tr>
<td style="padding-left:20px;text-align:left;">';
//combovalues

$output.='<select id="'.$sharing_module.'_share" name="'.$sharing_module.'_share" onChange=fnwriteRules("'.$sharing_module.'","'.$relatedmodule.'")>'.$fromComboValues.'</select>';	
$output.='</td>

<td>&nbsp;</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>

<td style="text-align:left;"><b>Step 2 : Can be accessed by</b>(Select an entity below)</td>
<td align="left"><b>Permissions</b></td>
</tr>
<tr>
<td style="padding-left:20px;text-align:left;">

<select id="'.$sharing_module.'_access" name="'.$sharing_module.'_access" onChange=fnwriteRules("'.$sharing_module.'","'.$relatedmodule.'")>';

$output.=$toComboValues.'</select>

</td><td>

<select	id="share_memberType" name="share_memberType" onChange=fnwriteRules("'.$sharing_module.'","'.$relatedmodule.'")>';
$output .= $sharPerCombo;
$output .= '</select>

</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
<td style="text-align:left;"><b>Step 3 : Access rights for relative modules </b></td>
<td>&nbsp;</td>

</tr>
<tr>
<td style="padding-left:20px;text-align:left;">
<table width="75%"  border="0" cellspacing="0" cellpadding="0">';



$output .=$relatedlistscombo.'</table>
</td>
<td>&nbsp;</td>
</tr>
<tr><td colspan="2" align="left">&nbsp;</td></tr>
<tr>
<td colspan="2" class="detailedViewHeader"><b>Rule Construction Display</b></td>

</tr>
<tr>
<td  style="white-space:normal;" colspan="2" class="dvtCellLabel" id="rules">&nbsp;
</td>
</tr>
<tr>
<td  style="white-space:normal;" colspan="2" class="dvtCellLabel" id="relrules">&nbsp;
</td>
</tr>
<tr>
<td colspan="2" align="center">
<input type="submit" class="crmButton small save" name="add" value="Add Rule" onClick="return validate()">&nbsp;&nbsp;
</td>
</tr>
</table>
</div>';

$output.='</form>';
echo $output;
?>
