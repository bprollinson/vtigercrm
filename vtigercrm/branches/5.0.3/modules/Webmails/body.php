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
require_once('modules/Webmails/Webmails.php');
require_once('modules/Webmails/MailBox.php');
global $mod_strings;

if(!isset($_SESSION["authenticated_user_id"]) || $_SESSION["authenticated_user_id"] != $current_user->id) {echo "ajax failed";flush();exit();}
$mailid=$_REQUEST["mailid"];
if(isset($_REQUEST["mailbox"]) && $_REQUEST["mailbox"] != "")
{
	$mailbox=$_REQUEST["mailbox"];
}
else
{
	$mailbox="INBOX";
}
$MailBox = new MailBox($mailbox);
$mail = $MailBox->mbox;
$email = new Webmails($MailBox->mbox,$mailid);
$status=imap_setflag_full($MailBox->mbox,$mailid,"\\Seen");
$attach_tab=array();
$email->loadMail($attach_tab);
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=".$email->charsets."\">\n";
$subject = decode_header($email->subject);
$from = decode_header($email->from);
$to = decode_header($email->to_header);
$cc = decode_header($email->cc_header);
$date = decode_header($email->date);
for($i=0;$i<count($email->attname);$i++){
	$attachment_links .= $email->anchor_arr[$i].decode_header($email->attname[$i])."</a></br>";
}
$content['body'] = $email->body;
$content['attachtab'] = $email->attachtab;
if(!$_REQUEST['fullview'])
	$class_str = 'class="tableHeadBg"';
else
	$class_str = 'style="font-size:15px"';
	
?>
<script src="modules/Webmails/Webmails.js" type="text/javascript"></script>
<script src="include/js/general.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="themes/<?php echo $_REQUEST['theme'];?>/webmail.css">
<!-- Table to display the Header details (From, To, Subject and date) - Starts -->
					
                                        <table <?php echo $class_str;?> width="100%" border="0" cellpadding="0" cellspacing="0">
                                                <tr><td width="20%" align="right"><b><?php echo $mod_strings['LBL_FROM'];?></b></td><td id="from_addy"><?php echo $from;?></td></tr>
                                                <tr><td width="20%" align="right"><b><?php echo $mod_strings['LBL_TO'];?></b></td><td id="to_addy"><?php echo $to;?></td></tr>
<tr><td width="20%" align="right"><b><?php echo $mod_strings['LBL_CC'];?></b></td><td id="webmail_cc"><?php echo $cc;?></td></tr>

                                                <tr><td align="right"><b><?php echo $mod_strings['LBL_SUBJECT'];?></b></td><td id="webmail_subject"><?php echo $subject;?></td></tr>
	<tr><td align="right"><b><?php echo $mod_strings['LBL_DATE'];?></b></td><td id="webmail_date"><?php echo $date;?></td>
        <?php if(!$_REQUEST['fullview']) {?>
        <td id="full_view" nowrap><span style="float:right"  colspan="2"><a href="javascript:;" onclick="OpenComposer('<?php echo $mailid;?>','full_view')"> Full Email View</a></span></td>
        <?php } ?>
	<tr>
	<?php if($_REQUEST['fullview'] && $email->has_attachments) {?>
		<td align="right"><b><?php echo $mod_strings['LBL_ATTACHMENT'];?>:</b></td><td id="webmail_attachment"><?php echo $attachment_links;?></td>
	<?php } ?>
	</tr>
                                                <tr><td align="right" style="border-bottom:1px solid #666666;" colspan="3">&nbsp;</td></tr>
                                        </table>
                                        <!-- Table to display the Header details (From, To, Subject and date) - Ends -->
					
<script type="text/javascript">
mailbox = "<?php echo $mailbox;?>";
function show_inline(num) {
	var el = document.getElementById("block_"+num);
	if(el.style.display == 'block')
		el.style.display='none';
	else
		el.style.display='block';
}
</script>
<?php
function view_part_detail($mail,$mailid,$part_no, &$transfer, &$msg_charset, &$charset)
{
	$text = imap_fetchbody($mail,$mailid,$part_no);
	if ($transfer == 'BASE64')
		$str = nl2br(imap_base64($text));
	elseif($transfer == 'QUOTED-PRINTABLE')
		$str = nl2br(quoted_printable_decode($text));
	else
		$str = nl2br($text);
	return ($str);
}
//Need to put this along with the subject block*/
echo $email->att;
if(!$_REQUEST['fullview'])
	echo '<div style="overflow:auto;height:410px;">';
else
	echo '<div style="overflow:auto;height:450px;">';
echo $content['body'];

//test added by Richie
if (!isset($_REQUEST['display_images']) || $_REQUEST['display_images'] != 1)
{
	$content['body'] = eregi_replace('src="[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]"', 'src="none"', $content['body']);
	$content['body'] = eregi_replace('src=[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]', 'src="none"', $content['body']);
}

//Display embedded HTML images
$tmp_attach_tab=$content['attachtab'];
$i = 0;
$conf->display_img_attach = true;
$conf->display_text_attach = true;

while ($tmp = array_pop($tmp_attach_tab)) 
{
	if ($conf->display_img_attach && (eregi('image', $tmp['mime']) && ($tmp['number'] != '')))
	{
		$exploded = explode('/', $tmp['mime']);
		$img_type = array_pop($exploded);
		if (eregi('JPEG', $img_type) || eregi('JPG', $img_type) || eregi('GIF', $img_type) || eregi ('PNG', $img_type))
		{
			$new_img_src = 'src="get_img.php?mail=' . $mailid.'&num=' . $tmp['number'] . '&mime=' . $img_type . '&transfer=' . $tmp['transfer'] . '"';
			$img_id = str_replace('<', '', $tmp['id']);
			$img_id = str_replace('>', '', $img_id);
			$content['body'] = str_replace('src="cid:'.$img_id.'"', $new_img_src, $content['body']);
			$content['body'] = str_replace('src=cid:'.$img_id, $new_img_src, $content['body']);
		}
		}
}
while ($tmp = array_pop($content['attachtab']))
{
	if ((!eregi('ATTACHMENT', $tmp['disposition'])) && $conf->display_text_attach && (eregi('text/plain', $tmp['mime'])))
		echo '<hr />'.view_part_detail($mail, $mailid, $tmp['number'], $tmp['transfer'], $tmp['charset'], $charset);
	if ($conf->display_img_attach && (eregi('image', $tmp['mime']) && ($tmp['number'] != '')))
	{
		$exploded = explode('/', $tmp['mime']);
		$img_type = array_pop($exploded);
		if (eregi('JPEG', $img_type) || eregi('JPG', $img_type) || eregi('GIF', $img_type) || eregi ('PNG', $img_type))
                        {
			echo '<hr />';
			echo '<center>';
			echo '<img src="index.php?module=Webmails&action=get_img&mail=' . $mailid.'&mailbox='.$mailbox.'&num=' . $tmp['number'] . '&mime=' . $img_type . '&transfer=' . $tmp['transfer'] . '" />';
			echo '</center>';
	}                
}                    
}


echo '</div>';
//test ended by Richie

imap_close($MailBox->mbox);
function decode_header($string){
	$elements = imap_mime_header_decode($string);
	for ($i=0; $i<count($elements); $i++) {
    		$result .= $elements[$i]->text;
	}
	return $result;
}

?>
