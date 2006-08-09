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
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title>vtiger CRM - Create Report</title>
	<link href="{$THEME_PATH}style.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript" src="include/js/general.js"></script>
	<script language="JavaScript" type="text/javascript" src="modules/Reports/Report.js"></script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="mailClient mailClientBg">
<tr>
	<td>
		<form name="NewRep" method="POST" ENCTYPE="multipart/form-data" action="index.php" style="margin:0px">
		<input type="hidden" name="module" value="Reports">
		<input type="hidden" name="primarymodule" value="{$REP_MODULE}">
		<input type="hidden" name="file" value="NewReport1">
		<input type="hidden" name="action" value="ReportsAjax">

		<table width="100%" border="0" cellspacing="0" cellpadding="5" >
			<tr>
				<td  class="moduleName" width="80%">Create Report </td>
				<td  width=30% nowrap class="componentName" align=right>Custom Reports</td>
			</tr>
		</table>
	
	
		<table width="100%" border="0" cellspacing="0" cellpadding="5" class="homePageMatrixHdr"> 
		<tr>
		<td>
		
					<table width="100%" border="0" cellspacing="0" cellpadding="0" > 
						<tr>
							<td width="25%" valign="top" >
								<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
									<tr><td id="step1label" class="settingsTabSelected" style="padding-left:10px;">1. {$MOD.LBL_REPORT_DETAILS}</td></tr>
									<tr><td id="step2label" class="settingsTabList" style="padding-left:10px;">2. {$MOD.LBL_RELATIVE_MODULE} </td></tr>
									<tr><td class="settingsTabList" style="padding-left:10px;">3. {$MOD.LBL_REPORT_TYPE} </td></tr>
									<tr><td class="settingsTabList" style="padding-left:10px;">4. {$MOD.LBL_SELECT_COLUMNS}</td></tr>
									<tr><td class="settingsTabList" style="padding-left:10px;">5. {$MOD.LBL_SPECIFY_GROUPING}</td></tr>
									<tr><td class="settingsTabList" style="padding-left:10px;">6. {$MOD.LBL_CALCULATIONS}</td></tr>
									<tr><td class="settingsTabList" style="padding-left:10px;">7. {$MOD.LBL_FILTERS} </td></tr>
								</table>
							</td>
							<td width="75%" valign="top"  bgcolor=white >
								<!-- STEP 1 -->
								<div id="step1" style="display:block;">
									<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#FFFFFF" class="small">
										<tr>
											<td colspan="2">
												<span class="genHeaderGray">{$MOD.LBL_REPORT_DETAILS}</span><br>
												{$MOD.LBL_TYPE_THE_NAME} &amp; {$MOD.LBL_DESCRIPTION_FOR_REPORT}<hr>
											</td>
										</tr>
										<tr>
											<td width="25%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REPORT_NAME} : </b></td>
											<td width="75%" align="left" style="padding-left:5px;"><input type="text" name="reportname" class="txtBox" value="{$REPORTNAME}"></td>
										</tr>
										<tr>
											<td width="25%" align="right" style="padding-right:5px;"><b>{$MOD.LBL_REP_FOLDER} : </b></td>
											<td width="75%" align="left" style="padding-left:5px;">
												<select name="reportfolder" class="txtBox">
												{foreach item=folder from=$REP_FOLDERS}
												{if $FOLDERID eq $folder.id}
													<option value="{$folder.id}" selected>{$folder.name}</option>
												{else}
													<option value="{$folder.id}">{$folder.name}</option>
												{/if}
												{/foreach}
												</select>
											</td>
										</tr>
										<tr>
											<td align="right" style="padding-right:5px;" valign="top"><b>{$MOD.LBL_DESCRIPTION}: </b></td>
											<td align="left" style="padding-left:5px;"><textarea name="reportdes" class="txtBox" rows="5">{$REPORTDESC}</textarea></td>
										</tr>
									</table>
								</div>
								<!-- STEP 2 -->
								<div id="step2" style="display:none;">
									<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" width="100%">
										<tr>
										<td colspan="2">
											<span class="genHeaderGray">{$MOD.LBL_RELATIVE_MODULE}</span><br>
											{$MOD.LBL_SELECT_RELATIVE_MODULE_FOR_REPORT}<hr>
										</td>
										</tr>
										<tr>
											<td style="padding-right: 5px;" align="right" nowrap width="25%"><b>{$MOD.LBL_NEW_REP0_HDR2}</b></td>
											<td style="padding-left: 5px;" align="left" width="75%">
												<select name="secondarymodule" class="txtBox">
												<option value="">--None--</option>
												{foreach item=relmod from=$RELATEDMODULES}
												{if $SEC_MODULE eq $relmod}
													<option selected value="{$relmod}">{$APP.$relmod}</option>
												{else}
													<option value="{$relmod}">{$APP.$relmod}</option>
												{/if}
												{/foreach}
												</select>
											</td>
										</tr>
									</table>
							</div>
						</td>
					</tr>
				</table>


			</td>
		</tr>
		</table>
		
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reportCreateBottom">
		<tr>
			<td align="right" style="padding:10px;">
			<input type="button" name="back_rep" id="back_rep" value=" &nbsp;&lt;&nbsp;{$APP.LBL_BACK}&nbsp; " disabled="disabled" class="classBtn" onClick="changeStepsback();">
			&nbsp;<input type="button" name="next" id="next" value=" &nbsp;{$APP.LNK_LIST_NEXT}&nbsp;&rsaquo;&nbsp; " onClick="changeSteps();" class="classBtn">
			&nbsp;<input type="button" name="cancel" value=" &nbsp;{$APP.LBL_CANCEL_BUTTON_LABEL}&nbsp; " class="classBtn" onClick="self.close();">
			</td>
		</tr>
	</table>
		</form>	

</td>
</tr>
</table>
	
	
</body>
</html>
{if $BACK_WALK eq 'true'}
{literal}
<script>
	hide('step1');
	show('step2');
	document.getElementById('back_rep').disabled = false;
	getObj('step1label').className = 'settingsTabList'; 
	getObj('step2label').className = 'settingsTabSelected';
</script>
{/literal}
{/if}
