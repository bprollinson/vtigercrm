<?php
/***************************************************************************
 *							pm_attachments.php
 *                            -------------------
 *   begin                : Monday, Jul 15, 2002
 *   copyright            : (C) 2002 Meik Sievertsen
 *   email                : acyd.burn@gmx.de
 *
 *   $Id: pm_attachments.php,v 1.9 2005/01/04 15:04:14 saraj Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

if ( !defined('IN_PHPBB') )
{
	die('Hacking attempt');
	exit;
}

//
// Class for Private Messaging
//
class attach_pm extends attach_parent
{
	var $pm_delete_attachments = FALSE;

	//
	// Constructor
	//
	function attach_pm()
	{
		global $HTTP_POST_VARS;

		$this->attach_parent();
		$this->pm_delete_attachments = ( isset($HTTP_POST_VARS['pm_delete_attach']) ) ? TRUE : FALSE;
		$this->page = PAGE_PRIVMSGS;
	}

	//
	// Preview Attachments in PM's
	//
	function preview_attachments()
	{
		global $attach_config, $userdata;

		if (!intval($attach_config['allow_pm_attach']))
		{
			return FALSE;
		}
	
		display_attachments_preview($this->attachment_list, $this->attachment_filesize_list, $this->attachment_filename_list, $this->attachment_comment_list, $this->attachment_extension_list, $this->attachment_thumbnail_list);
	}

	//
	// Insert an Attachment into a private message
	//
	function insert_attachment_pm($a_privmsgs_id)
	{
		global $db, $mode, $attach_config, $privmsg_sent_id, $userdata, $to_userdata;

		//
		// Insert Attachment ?
		//
		if (empty($a_privmsgs_id))
		{
			$a_privmsgs_id = $privmsg_sent_id;
		}
		
		if (!empty($a_privmsgs_id) && ($mode == 'post' || $mode == 'reply' || $mode == 'edit') && intval($attach_config['allow_pm_attach']))
		{
			$this->do_insert_attachment('attach_list', 'pm', $a_privmsgs_id);
			$this->do_insert_attachment('last_attachment', 'pm', $a_privmsgs_id);

			if ((count($this->attachment_list) > 0 || $this->post_attach) && !isset($HTTP_POST_VARS['update_attachment']))
			{
				$sql = "UPDATE " . PRIVMSGS_TABLE . "
					SET privmsgs_attachment = 1
					WHERE privmsgs_id = " . $a_privmsgs_id;

				if (!$db->sql_query($sql))
				{
					message_die(GENERAL_ERROR, 'Unable to update Private Message Table.', '', __LINE__, __FILE__, $sql);
				}
			}
		}
	}

	//
	// Duplicate Attachment for sent PM
	//
	function duplicate_attachment_pm($switch_attachment, $original_privmsg_id, $new_privmsg_id)
	{
		global $db, $privmsg, $folder;

		if ( ( $privmsg['privmsgs_type'] == PRIVMSGS_NEW_MAIL || $privmsg['privmsgs_type'] == PRIVMSGS_UNREAD_MAIL ) && $folder == 'inbox' && intval($switch_attachment) == 1)
		{
			$sql = 'SELECT *
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE privmsgs_id = ' . $original_privmsg_id;

			if ( !($result = $db->sql_query($sql)) )
			{
				message_die(GENERAL_ERROR, 'Couldn\'t query Attachment Table', '', __LINE__, __FILE__, $sql);
			}

			if ( ($db->sql_numrows($result)) > 0 )
			{
				$rows = $db->sql_fetchrowset($result);
				$num_rows = $db->sql_numrows($result);

				for ($i = 0; $i < $num_rows; $i++)
				{
					$sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' (attach_id, post_id, privmsgs_id, user_id_1, user_id_2) 
					VALUES ( ' . $rows[$i]['attach_id'] . ', ' . $rows[$i]['post_id'] . ', ' . $new_privmsg_id . ', ' . $rows[$i]['user_id_1'] . ', ' . $rows[$i]['user_id_2'] . ')';

					if ( !($result = $db->sql_query($sql)) )
					{
						message_die(GENERAL_ERROR, 'Couldn\'t store Attachment for sent Private Message', '', __LINE__, __FILE__, $sql);
					}
				}

				$sql = "UPDATE " . PRIVMSGS_TABLE . "
				SET privmsgs_attachment = 1
				WHERE privmsgs_id = " . $new_privmsg_id;

				if ( !($db->sql_query($sql)) )
				{
					message_die(GENERAL_ERROR, 'Unable to update Private Message Table.', '', __LINE__, __FILE__, $sql);
				}
			}
		}
	}

	//
	// Delete Attachments out of selected Private Message(s)
	//
	function delete_all_pm_attachments($mark_list)
	{
		global $confirm, $delete_all;

		if (count($mark_list))
		{
			$delete_sql_id = '';
			for ($i = 0; $i < count($mark_list); $i++)
			{
				$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
			}

			if ( ($delete_all) && ($confirm) )
			{
				delete_attachment($delete_sql_id, -1, PAGE_PRIVMSGS);
			}
		}
	}

	//
	// Display the Attach Limit Box (move it to displaying.php ?)
	// 
	function display_attach_box_limits()
	{
		global $folder, $attach_config, $board_config, $template, $lang, $userdata, $db;

		$this->get_quota_limits($userdata);

		if ( intval($attach_config['pm_filesize_limit']) == 0 )
		{
			$pm_filesize_limit = intval($attach_config['attachment_quota']);
		}
		else
		{
			$pm_filesize_limit = intval($attach_config['pm_filesize_limit']);
		}

		$pm_filesize_total = get_total_attach_pm_filesize('to_user', $userdata['user_id']);

		$attach_limit_pct = ( $pm_filesize_limit > 0 ) ? round(( $pm_filesize_total / $pm_filesize_limit ) * 100) : 0;
		$attach_limit_img_length = ( $pm_filesize_limit > 0 ) ? round(( $pm_filesize_total / $pm_filesize_limit ) * $board_config['privmsg_graphic_length']) : 0;
		if ($attach_limit_pct > 100)
		{
			$attach_limit_img_length = $board_config['privmsg_graphic_length'];
		}
		$attach_limit_remain = ( $pm_filesize_limit > 0 ) ? $pm_filesize_limit - $pm_filesize_total : 100;

		$l_box_size_status = sprintf($lang['Attachbox_limit'], $attach_limit_pct);

		$template->assign_vars(array(
			'ATTACHBOX_LIMIT_IMG_WIDTH' => $attach_limit_img_length, 
			'ATTACHBOX_LIMIT_PERCENT' => $attach_limit_pct, 

			'ATTACH_BOX_SIZE_STATUS' => $l_box_size_status)
		);
	}
	
	//
	// For Private Messaging
	//
	function privmsgs_attachment_mod($mode)
	{
		global $attach_config, $template, $lang, $userdata, $HTTP_POST_VARS, $phpbb_root_path, $phpEx, $db;
		global $confirm, $delete, $delete_all, $post_id, $privmsgs_id, $privmsg_id, $submit, $refresh, $mark_list, $folder;

		if ($folder != 'outbox')
		{
			$this->display_attach_box_limits();
		}

		if (!intval($attach_config['allow_pm_attach']))
		{
			return;
		}

		if (!$refresh)
		{
			$add_attachment_box = ( !empty($HTTP_POST_VARS['add_attachment_box']) ) ? TRUE : FALSE;
			$posted_attachments_box = ( !empty($HTTP_POST_VARS['posted_attachments_box']) ) ? TRUE : FALSE;

			$refresh = $add_attachment_box || $posted_attachments_box;
		}

		$post_id = $privmsgs_id;

		$result = $this->handle_attachments($mode, PAGE_PRIVMSGS);

		if ($result == FALSE)
		{
			return;
		}

		$mark_list = ( !empty($HTTP_POST_VARS['mark']) ) ? $HTTP_POST_VARS['mark'] : array();

		if ( ( (($this->pm_delete_attachments) || ($delete)) && (count($mark_list)) ) )
		{
			if ( !$userdata['session_logged_in'] )
			{
				$header_location = ( @preg_match('/Microsoft|WebSTAR|Xitami/', getenv('SERVER_SOFTWARE')) ) ? 'Refresh: 0; URL=' : 'Location: ';
				header($header_location . append_sid("login.$phpEx?redirect=privmsg.$phpEx&folder=inbox", true));
				exit;
			}
			
			if (count($mark_list))
			{
				$delete_sql_id = '';
				for ($i = 0; $i < count($mark_list); $i++)
				{
					$delete_sql_id .= (($delete_sql_id != '') ? ', ' : '') . intval($mark_list[$i]);
				}

				if ( ( ($this->pm_delete_attachments) || ($confirm) ) && (!$delete_all) )
				{
					delete_attachment($delete_sql_id, -1, PAGE_PRIVMSGS);
				}
			}
		}

		if ( $submit || $refresh || $mode != '' )
		{
			$this->display_attachment_bodies();
		}
	}
}

//
// Entry Point
//
function execute_privmsgs_attachment_handling($mode)
{
	global $attachment_mod;

	$attachment_mod['pm'] = new attach_pm();
	
	if ($mode != 'read')
	{
		$attachment_mod['pm']->privmsgs_attachment_mod($mode);
	}
}

?>