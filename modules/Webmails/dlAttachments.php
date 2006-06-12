<?php
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Initial Developer of the Original Code is FOSS Labs.
  * Portions created by FOSS Labs are Copyright (C) FOSS Labs.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  *
  ********************************************************************************/


include('config.php');
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');
require_once('modules/Webmails/Webmail.php');
require_once('modules/Webmails/MailParse.php');

$mailInfo = getMailServerInfo($current_user);
$temprow = $adb->fetch_array($mailInfo);
$imapServerAddress=$temprow["mail_servername"];
$box_refresh=$temprow["box_refresh"];
$mails_per_page=$temprow["mails_per_page"];

$mbox = getImapMbox($mailbox,$temprow);

$mailid=$_REQUEST["mailid"];
$num=$_REQUEST["num"];

$email = new Webmail($mbox,$mailid);
$attachments=$email->downloadAttachments();
$inline=$email->downloadInlineAttachments();

if($num == "" || !isset($num) && count($attachments) >0 ) {
	echo "<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr><td align='center'>There are ".count($attachments)." attachment(s) to choose from:</td></tr>";
	for($i=0;$i<count($attachments);$i++) {
		echo "<tr><td align='center'>".count($attachments).") &nbsp; <a href='index.php?module=Webmails&action=dlAttachments&mailid=".$mailid."&num=".$i."'>".$attachments[$i]["filename"]."</td></tr>";
	}
	echo "</table><br>";
	echo "<table width='100%' cellspacing='1' cellpadding='0' border='0'><tr><td align='center'>There are ".count($inline)." <b>inline</b> attachment(s) to choose from:</td></tr>";
	for($i=0;$i<count($inline);$i++) {
		echo "<tr><td align='center'>".count($inline).") &nbsp; <a href='index.php?module=Webmails&action=dlAttachments&mailid=".$mailid."&num=".$i."&inline=true'>".$inline[$i]["filename"]."</td></tr>";
	}
	echo "</table><br><br>";

} elseif (count(attachments) == 0 && count($inline) == 0) {
	echo "<center><strong>No vtiger_attachments for this email</strong></center><br><br>";
} else {

global $root_directory;
$save_path=$root_directory.'/modules/Webmails/tmp';
if(!is_dir($save_path))
	mkdir($save_path);

$user_dir=$save_path."/".$_SESSION["authenticated_user_id"];
if(!is_dir($user_dir))
	mkdir($user_dir);

if(isset($_REQUEST["inline"]) && $_REQUEST["inline"] == "true") {
	$fp = fopen($user_dir.'/'.$inline[$num]["filename"], "w") or die("Can't open file");
	fputs($fp, base64_decode($inline[$num]["filedata"]));
	$filename = 'modules/Webmails/tmp/'.$_SESSION['authenticated_user_id'].'/'.$inline[$num]['filename'];
} else {
	$fp = fopen($user_dir.'/'.$attachments[$num]["filename"], "w") or die("Can't open file");
	fputs($fp, base64_decode($attachments[$num]["filedata"]));
	$filename = 'modules/Webmails/tmp/'.$_SESSION['authenticated_user_id'].'/'.$attachments[$num]['filename'];
}
fclose($fp);
imap_close($mbox);

?>
<center><h2>File Download</h2></center>
<META HTTP-EQUIV="Refresh"
CONTENT="0; URL=<?php echo $filename;?>"
]"
<?
}
?>
