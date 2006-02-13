<?php

/**
 * read_body.php
 *
 * Copyright (c) 1999-2005 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file is used for reading the msgs array and displaying
 * the resulting emails in the right frame.
 *
 * @version $Id$
 * @package squirrelmail
 */

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
echo get_module_title("Emails", $mod_strings['LBL_MODULE_TITLE'], true);
global $msgvtSubject ;
$msgvtSubject='';
$submenu = array('LBL_EMAILS_TITLE'=>'index.php?module=Emails&action=ListView.php','LBL_WEBMAILS_TITLE'=>'index.php?module=squirrelmail-1.4.4&action=redirect');
$sec_arr = array('index.php?module=Emails&action=ListView.php'=>'Emails','index.php?module=squirrelmail-1.4.4&action=redirect'=>'Emails');
echo '<br>';
$_REQUEST['smodule'] = "WEBMAILS";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
   <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
     <td class="tabStart">&nbsp;&nbsp;</td>
<?
	if(isset($_REQUEST['smodule']) && $_REQUEST['smodule'] != '')
	{
		$classname = "tabOn";
	}
	else
	{
		$classname = "tabOff";
	}
	$listView = "ListView.php";
	foreach($submenu as $label=>$filename)
	{
		$cur_mod = $sec_arr[$filename];
		$cur_tabid = getTabid($cur_mod);

		if($tab_per_Data[$cur_tabid] == 0)
		{

			list($lbl,$sname,$title)=split("_",$label);
			if(stristr($label,"EMAILS"))
			{

				echo '<td class="tabOff" nowrap><a href="index.php?module=Emails&action=ListView&smodule='.$_REQUEST['smodule'].'" class="tabLink">'.$mod_strings[$label].'</a></td>';

				$listView = $filename;
				$classname = "tabOff";
			}
			elseif(stristr($label,$_REQUEST['smodule']))
			{
				echo '<td class="tabOn" nowrap><a href="index.php?module=squirrelmail-1.4.4&action=redirect&smodule='.$_REQUEST['smodule'].'" class="tabLink">'.$mod_strings[$label].'</a></td>';
				$listView = $filename;
				$classname = "tabOff";
			}
			else
			{
				echo '<td class="'.$classname.'" nowrap><a href="index.php?module=squirrelmail-1.4.4&action=redirect&smodule='.$sname.'" class="tabLink">'.$mod_strings[$label].'</a></td>';
			}
			$classname = "tabOff";
		}

	}
?>
     <td width="100%" class="tabEnd">&nbsp;</td>
   </tr>
 </table></td>
 </tr>
 </table>
 <br>
<?


//define('SM_PATH','../');
define('SM_PATH','modules/squirrelmail-1.4.4/');
/* SquirrelMail required files. */
//echo "<li><a href='index.php?module=squirrelmail-1.4.4&action=redirect'>Fetch Mails</a></li>";

$smoduleurl = "&smodule=WEBMAILS";

echo '<input type="button" class="button" name="fetchmail" value="'.$mod_strings['LBL_FETCH_MY_MAILS'].'" onclick=document.location.href="index.php?module=squirrelmail-1.4.4&action=redirect'.$smoduleurl.'";></input>';
echo '<br><br>';

//echo 'in read body';
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/mime.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/url_parser.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/global.php');
global $msgData ;
$msgData='';

/**
 * Given an IMAP message id number, this will look it up in the cached
 * and sorted msgs array and return the index. Used for finding the next
 * and previous messages.
 *
 * @return the index of the next valid message from the array
 */
function findNextMessage($passed_id) {
    global $msort, $msgs, $sort,
           $thread_sort_messages, $allow_server_sort,
           $server_sort_array;
    if (!is_array($server_sort_array)) {
        $thread_sort_messages = 0;
        $allow_server_sort = FALSE;
    }
    $result = -1;
    if ($thread_sort_messages || $allow_server_sort) {
        $count = count($server_sort_array) - 1;
        foreach($server_sort_array as $key=>$value) {
            if ($passed_id == $value) {
                if ($key == $count) {
                    break;
                }
                $result = $server_sort_array[$key + 1];
                break;
            }
        }
    } else {
        if (is_array($msort)) {
            for (reset($msort); ($key = key($msort)), (isset($key)); next($msort)) {
                if ($passed_id == $msgs[$key]['ID']) {
                    next($msort);
                    $key = key($msort);
                    if (isset($key)){
                        $result = $msgs[$key]['ID'];
                        break;
                    }
                }
            }
        }
    }
    return $result;
}

/** returns the index of the previous message from the array. */
function findPreviousMessage($numMessages, $passed_id) {
    global $msort, $sort, $msgs,
           $thread_sort_messages,
           $allow_server_sort, $server_sort_array;
    $result = -1;
    if (!is_array($server_sort_array)) {
        $thread_sort_messages = 0;
        $allow_server_sort = FALSE;
    }
    if ($thread_sort_messages || $allow_server_sort ) {
        foreach($server_sort_array as $key=>$value) {
            if ($passed_id == $value) {
                if ($key == 0) {
                    break;
                }
                $result = $server_sort_array[$key - 1];
                break;
            }
        }
    } else {
        if (is_array($msort)) {
            for (reset($msort); ($key = key($msort)), (isset($key)); next($msort)) {
                if ($passed_id == $msgs[$key]['ID']) {
                    prev($msort);
                    $key = key($msort);
                    if (isset($key)) {
                        $result = $msgs[$key]['ID'];
                        break;
                    }
                }
            }
        }
    }
    return $result;
}

/**
 * Displays a link to a page where the message is displayed more
 * "printer friendly".
 */
function printer_friendly_link($mailbox, $passed_id, $passed_ent_id, $color) {
    global $javascript_on;
    global $mod_strings;

    $params = '&passed_ent_id=' . urlencode($passed_ent_id) .
              '&mailbox=' . urlencode($mailbox) .
              '&passed_id=' . urlencode($passed_id);

    $print_text = _($mod_strings['LNK_VIEW_PRINTABLE_VERSION']);

    $result = '';
    /* Output the link. */
    if ($javascript_on) {
        $result = '<script language="javascript" type="text/javascript">' . "\n" .
                  '<!--' . "\n" .
                  "  function printFormat() {\n" .
                  '    window.open("index.php?module=squirrelmail-1.4.4&action=printer_friendly_main' .
                  $params . '","Print","width=800,height=600");' . "\n".
                  "  }\n" .
                  "// -->\n" .
                  "</script>\n" .
                  "<a href=\"javascript:printFormat();\">$print_text</a>\n";
    } else {
        $result = '<a target="_blank" href="index.php?module=squirrelmail-1.4.4&action=printer_friendly_bottom' .
                  "$params\">$print_text</a>\n";
    }
    return $result;
}

function ServerMDNSupport($read) {
    /* escaping $ doesn't work -> \x36 */
    $ret = preg_match('/(\x36MDNSent|\\\\\*)/i', $read);
    return $ret;
}

function SendMDN ( $mailbox, $passed_id, $sender, $message, $imapConnection) {
    global $username, $attachment_dir, $color,
           $version, $attachments, $squirrelmail_language, $default_charset,
           $languages, $useSendmail, $domain, $sent_folder,
           $popuser, $data_dir, $username;

    sqgetGlobalVar('SERVER_NAME', $SERVER_NAME, SQ_SERVER);

    $header = $message->rfc822_header;
    $hashed_attachment_dir = getHashedDir($username, $attachment_dir);

    $rfc822_header = new Rfc822Header();
    $content_type  = new ContentType('multipart/report');
    $content_type->properties['report-type']='disposition-notification';

    set_my_charset();
    if ($default_charset) {
        $content_type->properties['charset']=$default_charset;
    }
    $rfc822_header->content_type = $content_type;
    $rfc822_header->to[] = $header->dnt;
    $rfc822_header->subject = _("Read:") . ' ' . encodeHeader($header->subject);

    // FIX ME, use identity.php from SM 1.5. Change this also in compose.php

    $reply_to = '';
    if (isset($identity) && $identity != 'default') {
        $from_mail = getPref($data_dir, $username,
                             'email_address' . $identity);
        $full_name = getPref($data_dir, $username,
                             'full_name' . $identity);
        $from_addr = '"'.$full_name.'" <'.$from_mail.'>';
        $reply_to  = getPref($data_dir, $username,
                             'reply_to' . $identity);
    } else {
        $from_mail = getPref($data_dir, $username, 'email_address');
        $full_name = getPref($data_dir, $username, 'full_name');
        $from_addr = '"'.$full_name.'" <'.$from_mail.'>';
        $reply_to  = getPref($data_dir, $username,'reply_to');
    }

    // Patch #793504 Return Receipt Failing with <@> from Tim Craig (burny_md)
    // This merely comes from compose.php and only happens when there is no
    // email_addr specified in user's identity (which is the startup config)
    if (ereg("^([^@%/]+)[@%/](.+)$", $username, $usernamedata)) {
       $popuser = $usernamedata[1];
       $domain  = $usernamedata[2];
       unset($usernamedata);
    } else {
       $popuser = $username;
    }

    if (!$from_mail) {
       $from_mail = "$popuser@$domain";
       $from_addr = $from_mail;
    }

    $rfc822_header->from = $rfc822_header->parseAddress($from_addr,true);
    if ($reply_to) {
       $rfc822_header->reply_to = $rfc822_header->parseAddress($reply_to,true);
    }

    // part 1 (RFC2298)
    $senton = getLongDateString( $header->date );
    $to_array = $header->to;
    $to = '';
    foreach ($to_array as $line) {
        $to .= ' '.$line->getAddress();
    }
    $now = getLongDateString( time() );
    set_my_charset();
    $body = _("Your message") . "\r\n\r\n" .
            "\t" . _("To") . ': ' . decodeHeader($to,false,false,true) . "\r\n" .
            "\t" . _("Subject") . ': ' . decodeHeader($header->subject,false,false,true) . "\r\n" .
            "\t" . _("Sent") . ': ' . $senton . "\r\n" .
            "\r\n" .
            sprintf( _("Was displayed on %s"), $now );

    $special_encoding = '';
    if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
        function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
        $body = $languages[$squirrelmail_language]['XTRA_CODE']('encode', $body);
        if (strtolower($default_charset) == 'iso-2022-jp') {
            if (mb_detect_encoding($body) == 'ASCII') {
                $special_encoding = '8bit';
            } else {
                $body = mb_convert_encoding($body, 'JIS');
                $special_encoding = '7bit';
            }
        }
    } elseif (sq_is8bit($body)) {
        // detect 8bit symbols added by translations
        $special_encoding = '8bit';
    }
    $part1 = new Message();
    $part1->setBody($body);
    $mime_header = new MessageHeader;
    $mime_header->type0 = 'text';
    $mime_header->type1 = 'plain';
    if ($special_encoding) {
        $mime_header->encoding = $special_encoding;
    } else {
        $mime_header->encoding = 'us-ascii';
    }
    if ($default_charset) {
        $mime_header->parameters['charset'] = $default_charset;
    }
    $part1->mime_header = $mime_header;

    // part2  (RFC2298)
    $original_recipient  = $to;
    $original_message_id = $header->message_id;

    $report = "Reporting-UA : $SERVER_NAME ; SquirrelMail (version $version) \r\n";
    if ($original_recipient != '') {
        $report .= "Original-Recipient : $original_recipient\r\n";
    }
    $final_recipient = $sender;
    $report .= "Final-Recipient: rfc822; $final_recipient\r\n" .
              "Original-Message-ID : $original_message_id\r\n" .
              "Disposition: manual-action/MDN-sent-manually; displayed\r\n";

    $part2 = new Message();
    $part2->setBody($report);
    $mime_header = new MessageHeader;
    $mime_header->type0 = 'message';
    $mime_header->type1 = 'disposition-notification';
    $mime_header->encoding = 'us-ascii';
    $part2->mime_header = $mime_header;

    $composeMessage = new Message();
    $composeMessage->rfc822_header = $rfc822_header;
    $composeMessage->addEntity($part1);
    $composeMessage->addEntity($part2);


    if ($useSendmail) {
        require_once(SM_PATH . 'class/deliver/Deliver_SendMail.class.php');
        global $sendmail_path;
        $deliver = new Deliver_SendMail();
        $stream = $deliver->initStream($composeMessage,$sendmail_path);
    } else {
        require_once(SM_PATH . 'class/deliver/Deliver_SMTP.class.php');
        $deliver = new Deliver_SMTP();
        global $smtpServerAddress, $smtpPort, $smtp_auth_mech, $pop_before_smtp;
        if ($smtp_auth_mech == 'none') {
            $user = '';
            $pass = '';
        } else {
            global $key, $onetimepad;
            $user = $username;
            $pass = OneTimePadDecrypt($key, $onetimepad);
        }
        $authPop = (isset($pop_before_smtp) && $pop_before_smtp) ? true : false;
        $stream = $deliver->initStream($composeMessage,$domain,0,
                                       $smtpServerAddress, $smtpPort, $user, $pass, $authPop);
    }
    $success = false;
    if ($stream) {
        $length  = $deliver->mail($composeMessage, $stream);
        $success = $deliver->finalizeStream($stream);
    }
    if (!$success) {
        $msg  = $deliver->dlv_msg . '<br />' .
                _("Server replied: ") . $deliver->dlv_ret_nr . ' '.
                $deliver->dlv_server_msg;
        require_once(SM_PATH . 'functions/display_messages.php');
        plain_error_message($msg, $color);
    } else {
        unset ($deliver);
        if (sqimap_mailbox_exists ($imapConnection, $sent_folder)) {
            sqimap_append ($imapConnection, $sent_folder, $length);
            require_once(SM_PATH . 'class/deliver/Deliver_IMAP.class.php');
            $imap_deliver = new Deliver_IMAP();
            $imap_deliver->mail($composeMessage, $imapConnection);
            sqimap_append_done ($imapConnection);
            unset ($imap_deliver);
        }
    }
    return $success;
}

function ToggleMDNflag ($set ,$imapConnection, $mailbox, $passed_id, $uid_support) {
    $sg   =  $set?'+':'-';
    $cmd  = 'STORE ' . $passed_id . ' ' . $sg . 'FLAGS ($MDNSent)';
    $read = sqimap_run_command ($imapConnection, $cmd, true, $response,
                                $readmessage, $uid_support);
}

function ClearAttachments() {
    global $username, $attachments, $attachment_dir;

    $hashed_attachment_dir = getHashedDir($username, $attachment_dir);

    $rem_attachments = array();
    if (isset($attachments)) {
        foreach ($attachments as $info) {
            if ($info['session'] == -1) {
                $attached_file = "$hashed_attachment_dir/$info[localfilename]";
                if (file_exists($attached_file)) {
                    unlink($attached_file);
                }
            } else {
                $rem_attachments[] = $info;
            }
        }
    }
    $attachments = $rem_attachments;
}

function formatRecipientString($recipients, $item ) {
    global $show_more_cc, $show_more, $show_more_bcc,
           $PHP_SELF;

    $string = '';
    if ((is_array($recipients)) && (isset($recipients[0]))) {
        $show = false;

        if ($item == 'to') {
            if ($show_more) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more',1);
            }
        } else if ($item == 'cc') {
            if ($show_more_cc) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more_cc',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more_cc',1);
            }
        } else if ($item == 'bcc') {
            if ($show_more_bcc) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more_bcc',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more_bcc',1);
            }
        }

        $cnt = count($recipients);
        foreach($recipients as $r) {
            $add = decodeHeader($r->getAddress(true));
            if ($string) {
                $string .= '<br />' . $add;
            } else {
                $string = $add;
                if ($cnt > 1) {
                    $string .= '&nbsp;(<a href="'.$url;
                    if ($show) {
                       $string .= '">'._("less").'</a>)';
                    } else {
                       $string .= '">'._("more").'</a>)';
                       break;
                    }
                }
            }
        }
    }
    return $string;
}

function formatEnvheader($mailbox, $passed_id, $passed_ent_id, $message,$color, $FirstTimeSee) {
    global $msn_user_support, $default_use_mdn, $default_use_priority,
           $show_xmailer_default, $mdn_user_support, $PHP_SELF, $javascript_on,
           $squirrelmail_language,$msgvtSubject;
    global $mod_strings;

    $header = $message->rfc822_header;
    $env = array();
    $env[_($mod_strings['LBL_SUBJECT'])] = decodeHeader($header->subject);
    $msgvtSubject = $env[_("Subject")];
    $from_name = $header->getAddr_s('from');
    if (!$from_name) {
        $from_name = $header->getAddr_s('sender');
        if (!$from_name) {
            $from_name = _("Unknown sender");
        }
    }
    $env[_($mod_strings['LBL_FROM'])] = decodeHeader($from_name);
    $env[_($mod_strings['LBL_DATE'])] = getLongDateString($header->date);
    $env[_($mod_strings['LBL_TO'])] = formatRecipientString($header->to, "to");
    $env[_($mod_strings['LBL_CC'])] = formatRecipientString($header->cc, "cc");
    $env[_($mod_strings['LBL_BCC']."Bcc")] = formatRecipientString($header->bcc, "bcc");
    if ($default_use_priority) {
        $env[_($mod_strings['LBL_PRIORITY'])] = htmlspecialchars(getPriorityStr($header->priority));
    }
    if ($show_xmailer_default) {
        $env[_("Mailer")] = decodeHeader($header->xmailer);
    }
    if ($default_use_mdn) {
        if ($mdn_user_support) {
            if ($header->dnt) {
                if ($message->is_mdnsent) {
                    $env[_("Read receipt")] = _("sent");
                } else {
                    $env[_("Read receipt")] = _("requested");
                    if (!(handleAsSent($mailbox) ||
                          $message->is_deleted ||
                          $passed_ent_id)) {
                        $mdn_url = $PHP_SELF . '&sendreceipt=1';
                        if ($FirstTimeSee && $javascript_on) {
                            $script  = '<script language="JavaScript" type="text/javascript">' . "\n";
                            $script .= '<!--'. "\n";
                            $script .= 'if(window.confirm("' .
                                       _("The message sender has requested a response to indicate that you have read this message. Would you like to send a receipt?") .
                                       '")) {  '."\n" .
                                       '    sendMDN()'.
                                       '}' . "\n";
                            $script .= '// -->'. "\n";
                            $script .= '</script>'. "\n";
                            echo $script;
                        }
                        $env[_("Read receipt")] .= '&nbsp;<a href="' . $mdn_url . '">[' .
                                                   _("Send read receipt now") . ']</a>';
                    }
                }
            }
        }
    }

	$sphtml = '<br><br><br><table width="90%" border=0 cellPadding="0" cellSpacing="0">
	      <tr>
	        <td><div align="right">'.printer_friendly_link($mailbox, $passed_id, $passed_ent_id, $color).'</td>
	      </tr>
    </table>';

    echo $sphtml;

	$shtml = '<table width="90%" border=0 cellPadding="0" cellSpacing="2" class="formOuterBorder">
		<tr>

		</tr>';

	//<td class="formSecHeader" colspan="2">Mail Details</td>

    foreach ($env as $key => $val) {
        if ($val) {
            $mhtml .= '<tr>';
            $mhtml .= '<td class="dataLabel">'.$key.':</td>'; //html_tag('td', '<b>' . $key . ':&nbsp;&nbsp;</b>', 'right', '', 'valign="top" width="10%"') . "\n";
            $mhtml .= '<td>'.$val.'</td>';
            $mhtml .= '</tr>';
        }
    }

	$shtml.= $mhtml.'</table><br>';

	echo $shtml;

    /*echo '<table width="100%" cellpadding="1" cellspacing="0" border="0" align="centre">'."\n";
    echo '<tr><td height="5" colspan="2" class="formOuterBorder"></td></tr><tr><td align="center">'."\n";
    echo $s;
    do_hook('read_body_header');
    formatToolbar($mailbox, $passed_id, $passed_ent_id, $message, $color);
    echo '</table>';
    echo '</td></tr><tr><td height="5" colspan="2" bgcolor="'.$color[4].'"></td></tr>'."\n";
    echo '</table>';*/
}

function formatMenubar($mailbox, $passed_id, $passed_ent_id, $message, $mbx_response) {
  global $msgData,$msgvtSubject;
        global $base_uri, $draft_folder, $where, $what, $color, $sort,
           $startMessage, $PHP_SELF, $save_as_draft,
           $enable_forward_as_attachment;
	global $mod_strings;

        $header = $message->rfc822_header;
        $env = array();
        $env[_("Subject")] = decodeHeader($header->subject);
        $msgvtSubject = $env[_("Subject")];
         $env[_("Cc")] = formatRecipientString($header->cc, "cc");
	 $msgvtcc = $env[_("Cc")];
         $env[_("To")] = formatRecipientString($header->to, "to");
	 $msgvtTo = $env[_("To")];

    $topbar_delimiter = '&nbsp;|&nbsp;';
    $urlMailbox = urlencode($mailbox);
    $s = '<table width="90%" cellpadding="3" cellspacing="0" align="left" border="0" class="formOuterBorder"style="background-color: #F5F5F5;"><tr>' .
         html_tag( 'td', '', 'left', '', 'width="33%"' ) . '<small>';
    //$msgs_url = $base_uri . 'src/';
    $msgs_url = $base_uri;
    if (isset($where) && isset($what)) {
        $msgs_url .= 'search&where=' . urlencode($where) .
                     '&amp;what=' . urlencode($what) . '&amp;mailbox=' . $urlMailbox;
        $msgs_str  = _("Search Results");
    } else {
        $msgs_url .= 'right_main&sort=' . $sort . '&amp;startMessage=' .$startMessage . '&amp;mailbox=' . $urlMailbox;
        $msgs_str  = _($mod_strings['LNK_MESSAGE_LIST']);
    }
    $s .= '<a href="' . $msgs_url . '">' . $msgs_str . '</a>';

    //$delete_url = $base_uri . 'src/delete_message.php?mailbox=' . $urlMailbox .
    $delete_url = $base_uri . 'delete_message&mailbox=' . $urlMailbox .
                  '&amp;message=' . $passed_id . '&amp;';
    if (!(isset($passed_ent_id) && $passed_ent_id)) {
        if ($where && $what) {
            $delete_url .= 'where=' . urlencode($where) . '&amp;what=' . urlencode($what);
        } else {
            $delete_url .= 'sort=' . $sort . '&amp;startMessage=' . $startMessage;
        }
        $s .= $topbar_delimiter;
        $s .= '<a href="' . $delete_url . '">' . _($mod_strings['LNK_DELETE']) . '</a>';
    }
/*
    $comp_uri = 'compose' .
                '&passed_id=' . $passed_id .
                '&amp;mailbox=' . $urlMailbox .
                '&amp;startMessage=' . $startMessage .
                (isset($passed_ent_id)?'&amp;passed_ent_id='.urlencode($passed_ent_id):'');
*/

/*
		$comp_uri = 'compose' .
		'&passed_id=' . $passed_id .
		'&amp;mailbox=' . $urlMailbox .
		'&amp;startMessage=' . $startMessage .
*/		(isset($passed_ent_id)?'&amp;passed_ent_id='.urlencode($passed_ent_id):'');

		$modifiedcomp_uri='&passed_id=' . $passed_id .
		'&amp;mailbox=' . $urlMailbox .
		'&amp;startMessage=' . $startMessage .
		(isset($passed_ent_id)?'&amp;passed_ent_id='.urlencode($passed_ent_id):'');




    if (($mailbox == $draft_folder) && ($save_as_draft)) {
        $comp_alt_uri = $comp_uri . '&amp;smaction=draft';
        $comp_alt_string = _("Resume Draft");
    } else if (handleAsSent($mailbox)) {
        $comp_alt_uri = $comp_uri . '&amp;smaction=edit_as_new';
        $comp_alt_string = _("Edit Message as New");
    }
    if (isset($comp_alt_uri)) {
        $s .= $topbar_delimiter;
        $s .= makeComposeLink($comp_alt_uri, $comp_alt_string);
    }

    $s .= '</small></td><td align="center" width="33%"><small>';

    if (!(isset($where) && isset($what)) && !$passed_ent_id) {
        $prev = findPreviousMessage($mbx_response['EXISTS'], $passed_id);
        $next = findNextMessage($passed_id);
        if ($prev != -1) {
            $uri = $base_uri . 'read_body&passed_id='.$prev.
                   '&amp;mailbox='.$urlMailbox.'&amp;sort='.$sort.
                   '&amp;startMessage='.$startMessage.'&amp;show_more=0';
            $s .= '<a href="'.$uri.'">'._($mod_strings['LNK_PREVIOUS']).'</a>';
        } else {
            $s .= _($mod_strings['LNK_PREVIOUS']);
        }
        $s .= $topbar_delimiter;
        if ($next != -1) {
            $uri = $base_uri . 'read_body&passed_id='.$next.
                   '&amp;mailbox='.$urlMailbox.'&amp;sort='.$sort.
                   '&amp;startMessage='.$startMessage.'&amp;show_more=0';
            $s .= '<a href="'.$uri.'">'._($mod_strings['LNK_NEXT']).'</a>';
        } else {
            $s .= _($mod_strings['LNK_NEXT']);
        }
    } else if (isset($passed_ent_id) && $passed_ent_id) {
        /* code for navigating through attached message/rfc822 messages */
        $url = set_url_var($PHP_SELF, 'passed_ent_id',0);
//        $s .= '<a href="'.$url.'">'._("View Message").'</a>';
        $entities     = array();
        $entity_count = array();
        $c = 0;

        foreach($message->parent->entities as $ent) {
            if ($ent->type0 == 'message' && $ent->type1 == 'rfc822') {
                $c++;
                $entity_count[$c] = $ent->entity_id;
                $entities[$ent->entity_id] = $c;
            }
        }

        $prev_link = _("Previous");
        if($entities[$passed_ent_id] > 1) {
            $prev_ent_id = $entity_count[$entities[$passed_ent_id] - 1];
            $prev_link   = '<a href="'
                         . set_url_var($PHP_SELF, 'passed_ent_id', $prev_ent_id)
                         . '">' . $prev_link . '</a>';
        }

        $next_link = _("Next");
        if($entities[$passed_ent_id] < $c) {
            $next_ent_id = $entity_count[$entities[$passed_ent_id] + 1];
            $next_link   = '<a href="'
                         . set_url_var($PHP_SELF, 'passed_ent_id', $next_ent_id)
                         . '">' . $next_link . '</a>';
        }
        $s .= $topbar_delimiter . $prev_link;
        $par_ent_id = $message->parent->entity_id;
        if ($par_ent_id) {
            $par_ent_id = substr($par_ent_id,0,-2);
            $s .= $topbar_delimiter;
            $url = set_url_var($PHP_SELF, 'passed_ent_id',$par_ent_id);
            $s .= '<a href="'.$url.'">'._("Up").'</a>';
        }
        $s .= $topbar_delimiter . $next_link;
    }

    $s .= '</small></td>' . "\n" .
          html_tag( 'td', '', 'right', '', 'width="33%" nowrap' ) . '<small>';
    $comp_action_uri = $comp_uri . '&amp;smaction=forward';
   // $s .= makeComposeLink($comp_action_uri, _("Forward"));

    if ($enable_forward_as_attachment) {
        $comp_action_uri = $comp_uri . '&amp;smaction=forward_as_attachment';
    //    $s .= $topbar_delimiter;
     //   $s .= makeComposeLink($comp_action_uri, _("Forward as Attachment"));
    }

    $comp_action_uri = $comp_uri . '&amp;smaction=reply';
    //$s .= $topbar_delimiter;
   // echo $comp_action_uri;
    //$s .= makeComposeLink($comp_action_uri, _("Reply"));
    $s .= '<a href="index.php?module=Emails&action=EditView'.$modifiedcomp_uri.'&msg_to='.$msgvtTo.'&mg_subject='.$msgvtSubject.'&body='.$msgData.'">'.$mod_strings['LNK_REPLY'].'</a>';
    // echo $string;

    $comp_action_uri = $modifiedcomp_uri . '&amp;smaction=reply_all';
    $s .= $topbar_delimiter;
    //$s .= makeComposeLink($comp_action_uri, _("Reply All"));
    $s .= '<a href="index.php?module=Emails&action=EditView'.$modifiedcomp_uri.'&msg_to='.$msgvtTo.'&msg_cc='.$msgvtcc.'&mg_subject='.$msgvtSubject.'&body='.$msgData.'">'.$mod_strings['LNK_REPLY_ALL'].'</a>';
    $s .= '</small></td></tr></table>';
    $ret = concat_hook_function('read_body_menu_top', $s);
    if($ret != '') {
        $s = $ret;
    }
    echo $s;
    do_hook('read_body_menu_bottom');
}

function formatToolbar($mailbox, $passed_id, $passed_ent_id, $message, $color) {
    global $base_uri, $where, $what;

    $urlMailbox = urlencode($mailbox);
    $urlPassed_id = urlencode($passed_id);
    $urlPassed_ent_id = urlencode($passed_ent_id);

    $query_string = 'mailbox=' . $urlMailbox . '&amp;passed_id=' . $urlPassed_id . '&amp;passed_ent_id=' . $urlPassed_ent_id;

    if (!empty($where)) {
        $query_string .= '&amp;where=' . urlencode($where);
    }

    if (!empty($what)) {
        $query_string .= '&amp;what=' . urlencode($what);
    }

    $url = $base_uri.'view_header&'.$query_string;

    //$s  = "<tr>\n" .
     //     html_tag( 'td', '', 'right', '', 'valign="middle" width="20%"' ) . '<b>' . _("Options") . ":&nbsp;&nbsp;</b></td>\n" .
          //html_tag( 'td', '', 'left', '', 'valign="middle" width="80%"' ) . '<small>' .
          //'<a href="'.$url.'">'._("View Full Header").'</a>';

    /* Output the printer friendly link if we are in subtle mode. */
    $s .= '&nbsp;|&nbsp;' .
          printer_friendly_link($mailbox, $passed_id, $passed_ent_id, $color);
    //echo $s;
    do_hook("read_body_header_right");
    $s = "</small></td>\n" .
         "</tr>\n";
    echo $s;

}

/***************************/
/*   Main of read_body.php */
/***************************/

/* get the globals we may need */

sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('base_uri',  $base_uri,      SQ_SESSION);

sqgetGlobalVar('msgs',      $msgs,          SQ_SESSION);
sqgetGlobalVar('msort',     $msort,         SQ_SESSION);
sqgetGlobalVar('lastTargetMailbox', $lastTargetMailbox, SQ_SESSION);
sqgetGlobalVar('server_sort_array', $server_sort_array, SQ_SESSION);
if (!sqgetGlobalVar('messages', $messages, SQ_SESSION) ) {
    $messages = array();
}
global $current_user;
require_once('modules/Users/UserInfoUtil.php');
$mailInfo = getMailServerInfo($current_user);
$temprow = $adb->fetch_array($mailInfo);

$login_username= $temprow["mail_username"];
$secretkey=$temprow["mail_password"];
$imapServerAddress=$temprow["mail_servername"];
$key=OneTimePadEncrypt($secretkey, $onetimepad);

/** GET VARS */
sqgetGlobalVar('sendreceipt',   $sendreceipt,   SQ_GET);
sqgetGlobalVar('where',         $where,         SQ_GET);
sqgetGlobalVar('what',          $what,          SQ_GET);
if ( sqgetGlobalVar('show_more', $temp,  SQ_GET) ) {
    $show_more = (int) $temp;
}
if ( sqgetGlobalVar('show_more_cc', $temp,  SQ_GET) ) {
    $show_more_cc = (int) $temp;
}
if ( sqgetGlobalVar('show_more_bcc', $temp,  SQ_GET) ) {
    $show_more_bcc = (int) $temp;
}
if ( sqgetGlobalVar('view_hdr', $temp,  SQ_GET) ) {
    $view_hdr = (int) $temp;
}

/** POST VARS */
sqgetGlobalVar('move_id',       $move_id,       SQ_POST);

/** GET/POST VARS */
sqgetGlobalVar('passed_ent_id', $passed_ent_id);
sqgetGlobalVar('mailbox',       $mailbox);

if ( sqgetGlobalVar('passed_id', $temp) ) {
    $passed_id = (int) $temp;
}
if ( sqgetGlobalVar('sort', $temp) ) {
    $sort = (int) $temp;
}
if ( sqgetGlobalVar('startMessage', $temp) ) {
    $startMessage = (int) $temp;
}

/* end of get globals */
global $uid_support, $sqimap_capabilities;

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$mbx_response   = sqimap_mailbox_select($imapConnection, $mailbox, false, false, true);


/**
 * $message contains all information about the message
 * including header and body
 */

$uidvalidity = $mbx_response['UIDVALIDITY'];

if (!isset($messages[$uidvalidity])) {
   $messages[$uidvalidity] = array();
}
if (!isset($messages[$uidvalidity][$passed_id]) || !$uid_support) {
   $message = sqimap_get_message($imapConnection, $passed_id, $mailbox);
   $FirstTimeSee = !$message->is_seen;
   $message->is_seen = true;
   $messages[$uidvalidity][$passed_id] = $message;
} else {
//   $message = sqimap_get_message($imapConnection, $passed_id, $mailbox);
   $message = $messages[$uidvalidity][$passed_id];
   $FirstTimeSee = !$message->is_seen;
}

if (isset($passed_ent_id) && $passed_ent_id) {
    $message = $message->getEntity($passed_ent_id);
    if ($message->type0 != 'message'  && $message->type1 != 'rfc822') {
        $message = $message->parent;
    }
    $read = sqimap_run_command ($imapConnection, "FETCH $passed_id BODY[$passed_ent_id.HEADER]", true, $response, $msg, $uid_support);
    $rfc822_header = new Rfc822Header();
    $rfc822_header->parseHeader($read);
    $message->rfc822_header = $rfc822_header;
} else {
    $passed_ent_id = 0;
}
$header = $message->header;

do_hook('html_top');

/****************************************/
/* Block for handling incoming url vars */
/****************************************/

if (isset($sendreceipt)) {
   if ( !$message->is_mdnsent ) {
      if (isset($identity) ) {
         $final_recipient = getPref($data_dir, $username, 'email_address0', '' );
      } else {
         $final_recipient = getPref($data_dir, $username, 'email_address', '' );
      }

      $final_recipient = trim($final_recipient);
      if ($final_recipient == '' ) {
         $final_recipient = getPref($data_dir, $username, 'email_address', '' );
      }
      $supportMDN = ServerMDNSupport($mbx_response["PERMANENTFLAGS"]);
      if ( SendMDN( $mailbox, $passed_id, $final_recipient, $message, $imapConnection ) > 0 && $supportMDN ) {
         ToggleMDNflag( true, $imapConnection, $mailbox, $passed_id, $uid_support);
         $message->is_mdnsent = true;
         $messages[$uidvalidity][$passed_id]=$message;
      }
      ClearAttachments();
   }
}
/***********************************************/
/* End of block for handling incoming url vars */
/***********************************************/

$msgs[$passed_id]['FLAG_SEEN'] = true;

$messagebody = '';
//do_hook('read_body_top');
if ($show_html_default == 1) {
    $ent_ar = $message->findDisplayEntity(array());
} else {
    $ent_ar = $message->findDisplayEntity(array(), array('text/plain'));
}
$cnt = count($ent_ar);
for ($i = 0; $i < $cnt; $i++) {
   $messagebody .= formatBody($imapConnection, $message, $color, $wrap_at, $ent_ar[$i], $passed_id, $mailbox);
       $msgData = $messagebody;
   if ($i != $cnt-1) {
       $messagebody .= '<hr noshade size=1>';
   }
}

displayPageHeader($color, $mailbox);
formatMenuBar($mailbox, $passed_id, $passed_ent_id, $message, $mbx_response);
formatEnvheader($mailbox, $passed_id, $passed_ent_id, $message, $color, $FirstTimeSee);
echo '<br><table width="100%" cellpadding="0" cellspacing="0" align="left" border="0">';
echo '  <tr><td>';
echo '    <table width="90%" cellpadding="1" cellspacing="0" align="left" border="0" class="formOuterBorder"">';
echo '      <tr><td>';
echo '        <table width="100%" cellpadding="3" cellspacing="0" align="center" border="0">';
echo '          <tr bgcolor="'.$color[4].'"><td>';
// echo '            <table cellpadding="1" cellspacing="5" align="left" border="0">';
echo html_tag( 'table' ,'' , 'left', '', 'cellpadding="1" cellspacing="5" border="0"' );
echo '              <tr>' . html_tag( 'td', '<br />'. $messagebody."\n", 'left')
                        . '</tr>';
echo '            </table>';
echo '          </td></tr>';
echo '        </table></td></tr>';
echo '    </table>';
echo '  </td></tr>';

echo '<tr><td height="5" colspan="2" bgcolor="'.$color[4].'"></td></tr>'."\n";

$attachmentsdisplay = formatAttachments($message,$ent_ar,$mailbox, $passed_id);

if ($attachmentsdisplay) {
   echo '  <tr><td>';
   echo '    <br><table width="90%" cellpadding="1" cellspacing="0" align="left" border="0" class="formOuterBorder">';
   echo '     <tr><td>';
   echo '       <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0" bgcolor="'.$color[4].'">';
   echo '        <tr>' . html_tag( 'td', '', 'left', '','class="formSecHeader"' );
   echo '           <b>' . _($mod_strings['LBL_ATTACHMENTS']) . ':</b>';
   echo '        </td></tr>';
   echo '        <tr><td>';
   echo '          <table width="100%" cellpadding="2" cellspacing="2" align="center"'.' border="0" class="formOuterHeader"><tr><td>';
   echo              $attachmentsdisplay;
   echo '          </td></tr></table>';
   echo '       </td></tr></table>';
   echo '    </td></tr></table>';
   echo '  </td></tr>';
   echo '<tr><td height="5" colspan="2" bgcolor="'.
          $color[4].'"></td></tr>';
}
echo '</table>';
/* show attached images inline -- if pref'fed so */



if (($attachment_common_show_images) &&
    is_array($attachment_common_show_images_list)) {
    foreach ($attachment_common_show_images_list as $img) {
        $imgurl = 'index.php?module=squirrelmail-1.4.4&action=download' .
                '&amp;' .
                'passed_id='     . urlencode($img['passed_id']) .
                '&amp;mailbox='       . urlencode($mailbox) .
                '&amp;ent_id=' . urlencode($img['ent_id']) .
                '&amp;absolute_dl=true';

        echo html_tag( 'table', "\n" .
                    html_tag( 'tr', "\n" .
                        html_tag( 'td', '<img src="' . $imgurl . '" />' ."\n", 'left'
                        )
                    ) ,
        'center', '', 'cellspacing="0" border="0" cellpadding="2"');
    }
}

do_hook('read_body_bottom');
do_hook('html_bottom');
sqimap_logout($imapConnection);
/* sessions are written at the end of the script. it's better to register
   them at the end so we avoid double session_register calls */
sqsession_register($messages,'messages');

?>
</body></html>
