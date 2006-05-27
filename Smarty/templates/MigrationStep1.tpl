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


<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>
<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
<style type="text/css">@import url(themes/blue/style.css);</style>

<form name="Migration" method="POST" action="index.php" enctype="multipart/form-data">
<input type="hidden" name="module" value="Migration">
<input type="hidden" name="action" value="MigrationCheck">
<input type="hidden" name="migration_option" value="">
<input type="hidden" name="parenttab" value="Settings">

<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%">
   <tr>
	<td class="showPanelBg" valign="top" width="95%"  style="padding-left:20px; "><br />
		<span class="lvtHeaderText"> Settings &gt; Migrate from Previous Version </span>
		<hr noshade="noshade" size="1" />
	</td>
	<td width="5%" class="showPanelBg">&nbsp;</td>
   </tr>
   <tr>
	<td width="98%" style="padding-left:20px;" valign="top">
		<!-- module Select Table -->
		<table width="95%"  border="0" cellspacing="0" cellpadding="0" align="center">
		   <tr>
			<td width="7" height="6" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}top_left.jpg" align="top"  /></td>
			<td bgcolor="#EBEBEB" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;height:6px;"></td>
			<td width="8" height="6" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}top_right.jpg" width="8" height="6" align="top" /></td>
		   </tr>
		   <tr>
			<td bgcolor="#EBEBEB" width="7"></td>
			<td bgcolor="#ECECEC" style="padding-left:10px;padding-top:10px;vertical-align:top;">
				<table width="100%"  border="0" cellpadding="5" cellspacing="0">
				   <tr>
					<td width="10%"><img src="{$IMAGE_PATH}MigrationIcon.jpg" width="66" height="61"  align="absmiddle"/></td>
					<td width="90%">
						<span class="genHeaderBig">Migrate From Previous Version</span><br />
						Update your new vtiger CRM 5 database with the data from previous installation<br />
						To Start, follow the instructions below
					</td>
				   </tr>
				   <tr bgcolor="#FFFFFF">
					<td colspan="2">
						<span class="genHeaderGray">Step 1 : </span>
				  		<span class="genHeaderSmall">Select Source</span><br />
						To Start Migration, you must specify the format in which the old data is Available<br /><br />
					</td>
				   </tr>
				   <tr bgcolor="#FFFFFF">
					<td align="right" valign="top"><input type="radio" name="radio" id="db_details" value="db_details" onclick="fnChangeMigrate()" "{$DB_DETAILS_CHECKED}" /></td>
					<td><b>I Have the Data Base Format</b> ( Live Data )<br />
						This option requires you to have the host machine's ( where the DB is stored ) address and DB access  details.
						Both local and remote systems are supported in this method. Refer documentation for Help.
					</td>
				   </tr>
				   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
				   <tr bgcolor="#FFFFFF">
					<td align="right" valign="top"><input type="radio" name="radio" id="dump_details" value="dump_details" onclick="fnChangeMigrate()" "{$DUMP_DETAILS_CHECKED}"/></td>
					<td><b>I Have a Data Base as a Database Dump</b> ( Usually archived )<br />
						This option requires you to have the dump file, in this local system.
						You cannot specify a remote machine. Refer documentation for Help.
					</td>
				   </tr>
				   <tr><td colspan="2" height="10"></td></tr>
				   <tr bgcolor="#FFFFFF">
					<td colspan="2">
						<!-- OPTION 1 -->
						<div id="mnuTab" style="display:{$SHOW_DB_DETAILS}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							   <tr bgcolor="#FFFFFF">
								<td colspan="2">
									<span class="genHeaderGray">Step 2 : </span>
									<span class="genHeaderSmall">Host Database Access Details</span><br /><br />
								</td>
							   </tr>
							   <tr>
								<td width="30%" align="right">Source MySQL Host Name or IP Address : </td>
								<td width="70%"><input type="text" name="old_host_name" class="importBox" value="{$OLD_HOST_NAME}" /></td>
							   </tr>
							   <tr>
								<td align="right">Source MySQL Port Number : </td>
								<td><input type="text" name="old_port_no" class="importBox" value="{$OLD_PORT_NO}" /></td>
							   </tr>
							   <tr>
								<td align="right">Source MySql User Name : </td>
								<td><input type="text" name="old_mysql_username" class="importBox" value="{$OLD_MYSQL_USERNAME}" /></td>
							   </tr>
							   <tr>
								<td align="right">Source MySql Password : </td>
								<td><input type="text" name="old_mysql_password" class="importBox" value="{$OLD_MYSQL_PASSWORD}" /></td>
							   </tr>
							   <tr>
								<td align="right">Source Database Name : </td>
								<td><input type="text" name="old_dbname" class="importBox" value="{$OLD_DBNAME}" /></td>
							   </tr>
							</table>
						</div>

						<!-- OPTION 2 -->
						<div id="mnuTab2" style="display:{$SHOW_DUMP_DETAILS}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0">
							   <tr bgcolor="#FFFFFF">
								<td colspan="2">
									<span class="genHeaderGray">Step 2 : </span>
									<span class="genHeaderSmall">Locate Database Dump File</span><br /><br />
								</td>
							   </tr>
							   <tr>
								<td width="10%">&nbsp;</td>
								<td width="90%">
									Dump File Location : 
									<input type="file" name="old_dump_filename" class="txtBox" />
								</td>
							   </tr>
							</table>
						</div>

					</td>
				   </tr>
				   <tr>
					<td colspan="2" style="padding:10px;" align="center">
						<input type="submit" name="migrate" value=" Test &amp; Migrate "  class="classBtn" onclick="return validate_migration(Migration);"/>
						&nbsp;<input type="submit" name="cancel" value=" &nbsp;Cancel&nbsp; "  class="classBtn" onclick="this.form.module.value='Settings';this.form.action.value='index';"/>
 					</td>
				   </tr>
				</table>
			</td>
			<td bgcolor="#EBEBEB" width="8"></td>
		   </tr>
		   <tr>
			<td width="7" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}bottom_left.jpg" align="bottom"  /></td>
			<td bgcolor="#ECECEC" height="8" style="font-size:1px;" ></td>
			<td width="8" height="8" style="font-size:1px;font-family:Arial, Helvetica, sans-serif;"><img src="{$IMAGE_PATH}bottom_right.jpg" align="bottom" /></td>
		   </tr>
		</table>
		<br />
	</td>
	<td>&nbsp;</td>
   </tr>
</table>
<!-- END -->
</form>

<script language="javascript" type="text/javascript">
	//function to show and hide the db_details or dump_details details based on the radio option selected
	function fnChangeMigrate()
	{ldelim}
		var opt_one = document.getElementById('db_details').checked;
		var opt_two = document.getElementById('dump_details').checked;
		if(opt_one)
		{ldelim}
			document.getElementById('mnuTab').style.display = 'block';
			document.getElementById('mnuTab2').style.display = 'none';
		{rdelim}
		else
		{ldelim}
			document.getElementById('mnuTab').style.display = 'none';
			document.getElementById('mnuTab2').style.display = 'block';
		{rdelim}
	{rdelim}

	//function to validate the input values based on the radio option selected
	function validate_migration(formname)
	{ldelim}

		var error = false;
		var mig_option = '';

		if(document.getElementById("db_details").checked == true)
		{ldelim}
			formname.migration_option.value = 'db_details';
			//check whether the user entered the valid Source MySQL database details when db details selected
			if(trim(formname.old_host_name.value) == '')
			{ldelim}
				error_msg = "Please enter the Source Host Name";
				error = true;
			{rdelim}
			else if(trim(formname.old_port_no.value) == '')
			{ldelim}
				error_msg = "Please enter the Source MySql Port Number";
				error = true;
			{rdelim}
			else if(trim(formname.old_mysql_username.value) == '')
			{ldelim}
				error_msg = "Please enter the Source MySql User Name";
				error = true;
			{rdelim}
			else if(trim(formname.old_dbname.value) == '')
			{ldelim}
				error_msg = "Please enter the Source Database Name";
				error = true;
			{rdelim}
		{rdelim}
		else if(document.getElementById("dump_details").checked == true)
		{ldelim}
			formname.migration_option.value = 'dump_details';
			//check whether the user entered the MySQL File when dump file details selected
			if(trim(formname.old_dump_filename.value) == '')
			{ldelim}
				error_msg = "Please enter the Valid MySQL Dump File";
				error = true;
			{rdelim}
		{rdelim}
		else
		{ldelim}
			formname.migration_option.value = '';
			error_msg = "Please select any one option";
			error = true;
		{rdelim}

		//if there is any error then alert the user and return false;
		if(error == true)
		{ldelim}
			alert(error_msg);
			return false;
		{rdelim}
		else
		{ldelim}
			return true;
		{rdelim}

		return false;
	{rdelim}
</script>

