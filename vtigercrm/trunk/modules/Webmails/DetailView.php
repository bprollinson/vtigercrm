<?php
global $current_user;
require_once('include/utils/UserInfoUtil.php');
require_once('MailParse.php');
require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/upload_file.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');
require_once('modules/Webmails/Webmail.php');
require_once('modules/Webmails/MailParse.php');

global $log;
global $app_strings;
global $mod_strings;
if($_REQUEST["record"]) {$mailid=$_REQUEST["record"];} else {$mailid=$_REQUEST["mailid"];}
$mailInfo = getMailServerInfo($current_user);
$temprow = $adb->fetch_array($mailInfo);
$imapServerAddress=$temprow["mail_servername"];
$start_message=$_REQUEST["start_message"];
$box_refresh=$temprow["box_refresh"];
$mails_per_page=$temprow["mails_per_page"];

if($_REQUEST["mailbox"] && $_REQUEST["mailbox"] != "") {$mailbox=$_REQUEST["mailbox"];} else {$mailbox="INBOX";}
global $mbox;
$mbox = getImapMbox($mailbox,$temprow);


echo ' after  in DetailView ';
$email = new Webmail($mbox, $mailid);
$from = $email->from;
$subject=$email->subject;
$date=$email->date;

$to = $email->to_name[0]." &lt;".$email->to[0]."&gt;";
for($l=1;$l<count($email->to);$l++) {
	$to .= "; ".$email->to_name[$l]." &lt;".$email->to[$l]."&gt;";
}
$cc_list = $email->cc_list_name[0]." &lt;".$email->cc_list[0]."&gt;";
for($l=1;$l<count($email->cc_list);$l++) {
	$cc_list .= "; ".$email->cc_list_name[$l]." &lt;".$email->cc_list[$l]."&gt;";
}
$bcc_list = $email->bcc_list_name[0]." &lt;".$email->bcc_list[0]."&gt;";
for($l=1;$l<count($email->bcc_list);$l++) {
	$bcc_list .= "; ".$email->bcc_list_name[$l]." &lt;".$email->bcc_list[$l]."&gt;";
}
$reply_to = $from." <".$email->reply_to[0].">";

for($l=1;$l<count($email->reply_to);$l++) {
	$reply_to .= "; ".$email->reply_to_name[$l]." &lt;".$email->reply_to[$l]."&gt;";
}

$email->loadMail();
$body = $email->body;
$attachments=$email->attachments;
$inline=$email->inline;

$atL="Attachments";
$at="<i>None</i>";
$atd="";
$cnt=1;
if($attachments || $inline) {
    $at='<table width="100%" cellpadding="0" cellspacing="0">';
    for($i=0;$i<count($attachments);$i++) {
	if(strlen($attachments[$i]["filename"]) > 25)
		$fname=substr($attachments[$i]["filename"],0,25)."...";
	else
		$fname=$attachments[$i]["filename"];

	$filesize = $attachments[$i]["filesize"]." bytes";
	if($attachments[$i]["filesize"] > 1000000)
		$filesize= substr((($attachments[$i]["filesize"]/1024)/1024),0,5)." megabytes";
	elseif ($attachments[$i]["filesize"] > 1024)
		$filesize= substr(($attachments[$i]["filesize"]/1024),0,5)." kilobytes";

	$at.=" <tr><td width='100%' nowrap>".$cnt.") <a target='_blank' href='index.php?module=Webmails&action=dlAttachments&mailid=".$mailid."&num=".$i."'>".$fname."</a></b><br><i>".$filesize."</i></td></tr>";
	$cnt++;
    }
    for($i=0;$i<count($inline);$i++) {
	$at.=" <tr><td width='100%' nowrap>".$cnt.") <a href='modules/Webmails/tmp/".$_SESSION["authenticated_user_id"]."/".$inline[$i]["filename"]."'>".$inline[$i]["filename"]."</a></td></tr>";
	$cnt++;
    }
    $at.="</table>";
    $tmp = (count($inline)+count($attachments));
    $atL= $tmp." Attachment(s)";

?>
<script type="text/javascript">
function check_tags() {
	var el = document.getElementById("tagfields");
	if(el) {
		window.setTimeout(function() {
			el.innerHTML = 'Attachments: <?php echo addslashes($at);?>';
		},2000);
	} else {
		window.setTimeout(function() {
			check_tags();
		},1000);
	}
}
check_tags();
</script>
<?
}

$block["Email Information"][] = array("From:"=>array("value"=>"<a href='mailto:".$from."'>".$from."</a>"),"Date &amp; Time Sent:"=>array("value"=>$date));
$block["Email Information"][] = array("To:"=>array("value"=>$to),"CC:"=>array("value"=>$cc_list));
$block["Email Information"][] = array("Reply To:"=>array("value"=>"<a href='mailto:".$reply_to."'>".$reply_to."</a>"));
$block["Email Information"][] = array("Subject:"=>array("value"=>$subject));
//$block["Email Information"][] = array("Related To:"=>array("value"=>$email->relationship['id']));
//$block["Email Information"][] = array("Email Body:"=>array("ui"=>19,,"value"=>'<iframe src="index.php?module=Webmails&action=body&mailid='.$mailid.'&login_username='.$login_username.'&secretkey='.$secretkey.'&imapServerAddress='.$imapServerAddress.'&mailbox='.$mailbox.'" width="100%" height="350">'.$body.'body</iframe>'));

$block["Email Information"][] = array("Email Body:"=>array("ui"=>82,"value"=>'<iframe src="index.php?module=Webmails&action=body&mailid='.$mailid.'&mailbox='.$mailbox.'" width="100%" height="100%" frameborder="0">You must enabled iframes</iframe>'));


echo '<input type="hidden" name="mailid" value="'.$mailid.'">';
//echo '<pre>';print_r($block);echo "</pre>";

$repbutton = '<table border="0" width="25%"><tr>';
$repbutton .= '<td><a href="index.php?module=Webmails&action=EditView&mailbox='.$mailbox.'&mailid='.$mailid.'&reply=single"><img src="modules/Webmails/images/stock_mail-reply.png" alt="reply" width="16" height="16"  border="0"><br>Reply</a></td>';
$repbutton .= '<td><a href="index.php?module=Webmails&action=EditView&mailbox='.$mailbox.'&mailid='.$mailid.'&reply=all"><img src="modules/Webmails/images/stock_mail-reply.png" alt="reply" width="16" height="16"  border="0"><br>Reply To All</a></td>';
$delbutton = '<td><a href="index.php?module=Webmails&action=index&delete_msg='.$mailid.'"><img src="modules/Webmails/images/stock_trash_full.png" border="0" width="14" height="14" alt="del"><br>Delete</a></td></tr></table>';


$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH", $image_path);
$smarty->assign("CATEGORY","My Home Page");
$smarty->assign("SINGLE_MOD","Webmails");
$smarty->assign("NAME", "From: ".$from);
$smarty->assign("ID", $mailid);
$smarty->assign("UPCOMING_ACTIVITIES",$atL);
$smarty->assign("ACTIVITY_TITLE",$at);
$smarty->assign("BLOCKS",$block);
$smarty->assign("EDITBUTTON",$repbutton);
$smarty->assign("DELETEBUTTON",$delbutton);
$smarty->assign("SINGLE_MOD","Webmails");
$smarty->assign("MODULE","Webmails");
$smarty->display("DetailView.tpl");
imap_close($mbox);
?>
