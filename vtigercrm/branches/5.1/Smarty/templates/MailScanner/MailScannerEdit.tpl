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

<br>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%">
<tbody>
<tr>
	<td valign="top"><img src="{$IMAGE_PATH}showPanelTopLeft.gif"></td>
    <td class="showPanelBg" style="padding: 10px;" valign="top" width="100%">

	<form action="index.php" method="post" id="form">
		<input type='hidden' name='module' value='Settings'>
		<input type='hidden' name='action' value='MailScanner'>
		<input type='hidden' name='mode' value='save'>
		<input type='hidden' name='return_action' value='MailScanner'>
		<input type='hidden' name='return_module' value='Settings'>
		<input type='hidden' name='parenttab' value='Settings'>

        <br>

		<div align=center>
			{include file='SetMenu.tpl'}
				<!-- DISPLAY -->
				<table border=0 cellspacing=0 cellpadding=5 width=100% class="settingsSelUITopLine">
				<tr>
					<td width=50 rowspan=2 valign=top><img src="{$IMAGE_PATH}mailScanner.gif" alt="{$MOD.LBL_MAIL_SCANNER}" width="48" height="48" border=0 title="{$MOD.LBL_MAIL_SCANNER}"></td>
					<td class=heading2 valign=bottom><b><a href="index.php?module=Settings&action=index&parenttab=Settings">{$MOD.LBL_SETTINGS}</a> > {$MOD.LBL_MAIL_SCANNER}</b></td>
				</tr>
				<tr>
					<td valign=top class="small">{$MOD.LBL_MAIL_SCANNER_DESCRIPTION}</td>
				</tr>
				</table>
				
				<br>
				<table border=0 cellspacing=0 cellpadding=10 width=100% >
				<tr>
				<td>

				{if $CONNECTFAIL neq ''}
					<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
					<tr>
							<td align="center" width="100%"><font color='red'><b>{$CONNECTFAIL}</b></font></td>
					</tr>
					</table>
				{/if}

				<table border=0 cellspacing=0 cellpadding=5 width=100% class="tableHeading">
				<tr>
				<td class="big" width="70%"><strong>{$MOD.LBL_MAILBOX} {$MOD.LBL_INFORMATION}</strong></td>
				</tr>
				</table>
				
				<table border=0 cellspacing=0 cellpadding=0 width=100% class="listRow">
				<tr>
	         	    <td class="small" valign=top ><table width="100%"  border="0" cellspacing="0" cellpadding="5">
						<tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SCANNER} {$MOD.LBL_NAME}</strong></td>
                            <td width="80%"><input type="text" name="mailboxinfo_scannername" class="small" value="DEFAULT" size=50 readonly></td>
                        </tr>
                        <tr>
                            <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SERVER} {$MOD.LBL_NAME}</strong></td>
                            <td width="80%"><input type="text" name="mailboxinfo_server" class="small" value="{$SCANNERINFO.server}" size=50></td>
                        </tr>
                        <tr>
							<td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_PROTOCOL}</strong></td>
                            <td width="80%">
								{assign var="imapused" value=""}
								{assign var="imap4used" value=""}

								{if $SCANNERINFO.protocol eq 'imap4'}
									{assign var="imap4used" value="checked='true'"}
								{else}
									{assign var="imapused" value="checked='true'"}
								{/if}
							
								<input type="radio" name="mailboxinfo_protocol" class="small" value="imap" {$imapused}> {$MOD.LBL_IMAP2}
								<input type="radio" name="mailboxinfo_protocol" class="small" value="imap4" {$imap4used}> {$MOD.LBL_IMAP4}
							</td>
						</tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_USERNAME}</strong></td>
                            <td width="80%"><input type="text" name="mailboxinfo_username" class="small" value="{$SCANNERINFO.username}" size=50></td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_PASSWORD}</strong></td>
                            <td width="80%"><input type="password" name="mailboxinfo_password" class="small" value="{$SCANNERINFO.password}" size=50></td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_TYPE}</strong></td>
               				<td width="80%" class="small cellText">
								{assign var="notls_type" value=""}
								{assign var="tls_type" value=""}
								{assign var="ssl_type" value=""}

								{if $SCANNERINFO.ssltype eq 'notls'}
									{assign var="notls_type" value="checked='true'"}
								{elseif $SCANNERINFO.ssltype eq 'tls'}
									{assign var="tls_type" value="checked='true'"}
								{elseif $SCANNERINFO.ssltype eq 'ssl'}
									{assign var="ssl_type" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_ssltype" class="small" value="notls" {$notls_type}> {$MOD.LBL_NO} {$MOD.LBL_TLS}
								<input type="radio" name="mailboxinfo_ssltype" class="small" value="tls" {$tls_type}> {$MOD.LBL_TLS}
								<input type="radio" name="mailboxinfo_ssltype" class="small" value="ssl" {$ssl_type}> {$MOD.LBL_SSL}
							</td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_SSL} {$MOD.LBL_METHOD}</strong></td>
							<td width="80%" class="small cellText">
								{assign var="novalidatecert_type" value=""}
								{assign var="validatecert_type" value=""}

								{if $SCANNERINFO.sslmethod eq 'validate-cert'}
									{assign var="validatecert_type" value="checked='true'"}
								{else}
									{assign var="novalidatecert_type" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_sslmethod" class="small" value="validate-cert" {$validatecert_type}> {$MOD.LBL_VALIDATE} {$MOD.LBL_SSL} {$MOD.LBL_CERTIFICATE}
								<input type="radio" name="mailboxinfo_sslmethod" class="small" value="novalidate-cert" {$novalidatecert_type}> {$MOD.LBL_DO} {$MOD.LBL_NOT} {$MOD.LBL_SSL} {$MOD.LBL_CERTIFICATE}
							</td>
                        </tr>
						<tr>
			                <td width="20%" nowrap class="small cellLabel"><strong>{$MOD.LBL_STATUS}</strong></td>
							<td width="80%" class="small cellText">
								{assign var="mailbox_enable" value=""}
								{assign var="mailbox_disable" value=""}

								{if $SCANNERINFO.isvalid eq false}
									{assign var="mailbox_disable" value="checked='true'"}
								{else}
									{assign var="mailbox_enable" value="checked='true'"}
								{/if}

								<input type="radio" name="mailboxinfo_enable" class="small" value="true" {$mailbox_enable}> {$MOD.LBL_ENABLE}
								<input type="radio" name="mailboxinfo_enable" class="small" value="false" {$mailbox_disable}> {$MOD.LBL_DISABLE}
							</td>
                        </tr>
				    </td>
            	</tr>
				<tr>
					<td colspan=2 nowrap align="center">
						<input type="submit" class="crmbutton small save" value="{$APP.LBL_SAVE_LABEL}" />
						<input type="button" class="crmbutton small cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}" onclick="history.go(-1)"/>
					</td>
				</tr>
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
</form>
</table>

