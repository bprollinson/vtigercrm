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

global $current_user;
require_once('include/utils/utils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('modules/Webmails/Webmail.php');
require_once('modules/Webmails/MailParse.php');

if(!isset($_SESSION["authenticated_user_id"]) || $_SESSION["authenticated_user_id"] != $current_user->id) {echo "ajax failed";flush();exit();}

$mailInfo = getMailServerInfo($current_user);
$temprow = $adb->fetch_array($mailInfo);
$imapServerAddress=$temprow["mail_servername"];
$box_refresh=$temprow["box_refresh"];
$mails_per_page=$temprow["mails_per_page"];

$mailid=$_REQUEST["mailid"];
if(isset($_REQUEST["mailbox"]) && $_REQUEST["mailbox"] != "") {$mailbox=$_REQUEST["mailbox"];} else {$mailbox="INBOX";}

global $mbox;
$mbox = getImapMbox($mailbox,$temprow);

$email = new Webmail($mbox,$mailid);
$email->loadMail();

if(isset($_POST["command"])) {
	$command = $_POST["command"];
	if($command == "expunge")
		imap_expunge($mbox);
	if($command == "delete_msg")
		 $email->delete();
	if($command == "undelete_msg")
		 $email->unDeleteMsg();
	if($command == "set_flag")
		 $email->setFlag();
	if($command == "clear_flag")
		 $email->delFlag();
} else {
?>
<script type="text/javascript">
function show_inline(num) {
	var el = document.getElementById("block_"+num);
	if(el.style.display == 'block')
		el.style.display='none';
	else
		el.style.display='block';
}
</script>
<?
	echo $email->body;
	echo "<br><br>";
	if(is_array($email->inline)) {
		$inline = $email->inline;
		$num=sizeof($inline);
		echo "<p style='border-bottom:1px solid black;font-weight:bold'>Inline Attachments:</p>";
		for($i=0;$i<$num;$i++) {
				//var_dump($inline[$i]);
				// PLAIN TEXT
				if($inline[$i]["subtype"] == "RFC822") {
					echo "<a href='javascript:show_inline(".$i.");'>".$inline[$i]["filename"]."</a><blockquote id='block_".$i."' style='border:1px solid gray;padding:6px;background-color:#FFFFCC;display:none'>";
					echo nl2br($inline[$i]["filedata"]);
					echo "</blockquote>";
				} elseif($inline[$i]["subtype"] == "JPEG") {
					echo "<a href='javascript:show_inline(".$i.");'>".$inline[$i]["filename"]."</a><br><br><div id='block_".$i."' style='border:1px solid gray;padding:6px;background-color:#FFFFCC;display:none;width:95%'>";
					global $root_directory;
					$save_path=$root_directory.'/modules/Webmails/tmp';
					if(!is_dir($save_path))
       		 				mkdir($save_path);
					$save_dir=$save_path."/cache";
					if(!is_dir($save_dir))
       		 				mkdir($save_dir);
		
        				$fp = fopen($save_dir.'/'.$inline[$i]["filename"], "w") or die("Can't open file");
        				fputs($fp, base64_decode($inline[$i]["filedata"]));
        				$filename = 'modules/Webmails/tmp/cache/'.$inline[$i]['filename'];
					fclose($fp);
					echo '<img src="'.$filename.'" border="0" width="100%">';
					echo '</div>';
				} else 
					echo "<br>".($i+1).") <a target='_BLANK' href='index.php?module=Webmails&action=dlAttachments&inline=true&num=".$i."&mailid=".$mailid."'>".$inline[$i]["filename"]."</a>";
		}
	}
} 
imap_close($mbox);

?>
