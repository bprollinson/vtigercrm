<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/install/1checkSystem.php,v 1.16 2005/03/08 12:01:36 samk Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/

//get php configuration settings.  requires elaborate parsing of phpinfo() output
?>
<html>
<?php

if(isset($_REQUEST['install'])){
	$form_name = 'installform';
	$file_name = '4setConfig.php';
	$source_file = 'licenceAgreement.html';
}
elseif(isset($_REQUEST['migrate'])){
	$form_name = 'migrateform';
	$file_name = '4setMigrationConfig.php';
	$source_file = 'migrationInstructions.html';
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - Installation Check</title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br>
	<!-- Table for cfgwiz starts -->

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="Configuration Wizard" hspace="20" title="Configuration Wizard"></td>
		<td class="cwHeadBg1" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
		<td class="cwHeadBg1" width=2%></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" colspan=2 align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
						<br>
						</td>
					</tr>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
						<!-- Right side tabs -->
						    <table cellspacing=0 cellpadding=10 width=90% align=center class='level3'>
								<tr>
									<td align=center>
										<iframe class='licence' frameborder=0 src='<?php echo $source_file ?>' marginwidth=20 scrolling='auto'>
										</iframe>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
						<!-- Right side tabs -->
						    <table cellspacing=0 cellpadding=10 width=90% align=center class='cwContentDisplay'>
								<tr>
						<td align=left width=50% valign=top ><br>
							<input type="image" src="include/install/images/cwBtnBack.gif" alt="Back" border="0" title="Back" onClick="window.history.back();">
						</td>
						<td width=50% valign=top align=right>
										<br>
							<form action="install.php" method="post" name="form" id="form">
							<input type="hidden" name="filename" value="<?php echo $file_name?>" />	
							<input type="hidden" name="file" value="2checkSystem.php" />	
					        <input type="image" src="include/install/images/cwBtnNext.gif" value='Agree' alt="Agree" border="0" title="migrate" style="cursor:pointer;" onClick="window.document.form.submit();">
							</form>
						</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>

		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
	</table>	
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>

      	<tr>
        	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
      	</tr>
    	</table>
</body>
</html>	
