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

$pmodule=$_REQUEST['pmodule'];
$entityid=$_REQUEST['entityid'];

if ($pmodule=='Accounts')
{
	$querystr="select vtiger_fieldid,fieldlabel,columnname,tablename from vtiger_field where vtiger_tabid=6 and uitype=13;"; 
}
elseif ($pmodule=='Contacts')
{
	$querystr="select vtiger_fieldid,fieldlabel,columnname from vtiger_field where vtiger_tabid=4 and uitype=13;";
}
elseif ($pmodule=='Leads')
{
	$querystr="select vtiger_fieldid,fieldlabel,columnname from vtiger_field where vtiger_tabid=7 and uitype=13;";
}
$result=$adb->query($querystr);
$numrows = $adb->num_rows($result);

if ($pmodule=='Accounts')
{
	require_once('modules/Accounts/Account.php');
	$myfocus = new Account();
	$myfocus->retrieve_entity_info($entityid,"Accounts");
}
elseif ($pmodule=='Contacts')
{
	require_once('modules/Contacts/Contact.php');
	$myfocus = new Contact();
	$myfocus->retrieve_entity_info($entityid,"Contacts");
}
elseif ($pmodule=='Leads')
{
	require_once('modules/Leads/Lead.php');
	$myfocus = new Lead();
	$myfocus->retrieve_entity_info($entityid,"Leads");
}
?>
<script language="javascript">
function passemail()
{		
	y=window.opener.document.EditView.parent_id.value+"<?php echo $entityid;?>";
	z=window.opener.document.EditView.parent_name.value;
	<?php 
	for ($x=0;$x<$numrows;$x++)
	{
		$temp=$adb->query_result($result,$x,'columnname');
		$temp1=br2nl($myfocus->column_fields[$temp]);
		if ($pmodule=='Accounts')
		{
			$fullname=br2nl($myfocus->column_fields['accountname']);
		}
		elseif ($pmodule=='Contacts')
		{
			$fname=br2nl($myfocus->column_fields['firstname']);
			$lname=br2nl($myfocus->column_fields['lastname']);
			$fullname=$lname.' '.$fname;
		}
		elseif ($pmodule=='Leads')
		{
			$fname=br2nl($myfocus->column_fields['firstname']);
			$lname=br2nl($myfocus->column_fields['lastname']);
			$fullname=$lname.' '.$fname;
		}
?>
		if (document.choosemails.emails<?php echo $x;?>.checked)
		{
				y=y+"@<?php echo $adb->query_result($result,$x,'fieldid');?>";
				z=z+"<?php echo $fullname.'<'.$temp1.'> ;';?>";
		}
<?php 
	} 
?>
	
	window.opener.document.EditView.parent_id.value=y+"|";
	window.opener.document.EditView.parent_name.value=z;
	window.close();
}
</script>

<form name="choosemails" method="post">
<h4>The following emails are available for the selected record. Please choose the ones you would like to use.</h4>
<div align="center">
   <table cellpadding="0" cellspacing="0" border="0">
	<?php 
	for ($i=0;$i<$numrows;$i++)
	{
		$temp=$adb->query_result($result,$i,'columnname');
		$temp1=br2nl($myfocus->column_fields[$temp]);
		echo '<tr><td>'.$adb->query_result($result,$i,'fieldlabel').' </td><td>&nbsp;&nbsp;&nbsp;<input name="emails'.$i.'" type="checkbox" title="Raju"></td><td>'.$temp1.'</tr>';
	}
	?>
   </table>

<input type="button" name="OK" onClick="passemail()" value="OK">
</div>
</form>
