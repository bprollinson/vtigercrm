<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
$languageStrings = array(
	'MailConverter' => 'Mail Converter',
	'MailConverter_Description' => 'Convert emails to respective records',
	'MAILBOX' => 'MailBox',
	'RULE' => 'Rule',
	'LBL_ADD_RECORD' => 'Add MailBox',
	'ALL' => 'All',
	'UNSEEN' => 'Unread',
	'LBL_MARK_READ' => 'Mark Read',
	'SEEN' => 'Read',
	'LBL_EDIT_MAILBOX' => 'Edit MailBox',
	'LBL_CREATE_MAILBOX' => 'Create MailBox',
	'LBL_BACK_TO_MAILBOXES' => 'Back to MailBoxes',
	'LBL_MARK_MESSAGE_AS' => 'Mark message as',
	'LBL_CREATE_MAILBOX_NOW' => 'Create Mailbox now',
	'LBL_ADDING_NEW_MAILBOX' => 'Adding New Mail Box',
	'MAILBOX_DETAILS' => 'Mail Box Details',
	'SELECT_FOLDERS' => 'Select Folders',
	'ADD_RULES' => 'Add Rules',
	'CREATE_Leads_SUBJECT' => 'Create Lead',
	'CREATE_Contacts_SUBJECT' => 'Create Contact',
	'CREATE_Accounts_SUBJECT' => 'Create Organization',
	'LBL_ACTIONS' => 'Actions',
	'LBL_MAILBOX' => 'Mail Box',
	'LBL_RULE' => 'Rule',
	'LBL_CONDITIONS' => 'Conditions',
	'LBL_FOLDERS_SCANNED' => 'Folders Scanned',
	'LBL_NEXT' => 'Next',
	'LBL_FINISH' => 'Finish',
	'TO_CHANGE_THE_FOLDER_SELECTION_DESELECT_ANY_OF_THE_SELECTED_FOLDERS' => 'To change the folder selection deselect any of the selected folders',
	'LBL_MAILCONVERTER_DESCRIPTION' => "Mail Converter enables you to configure your mailbox to scan your emails and create appropriate entities in Vtiger CRM.<br />You'll also need to define rules to specify what actions should be performed on your emails.<br />Your emails are scanned automatically, unless you've disabled Mail Scanner task in Scheduler. <br /><br /><br />",
	
	//Server Messages
	'LBL_MAX_LIMIT_ONLY_TWO' => 'You can configure only two mailboxes',
	'LBL_IS_IN_RUNNING_STATE' => 'In running state',
	'LBL_SAVED_SUCCESSFULLY' => 'Saved successfully',
	'LBL_CONNECTION_TO_MAILBOX_FAILED' => 'Connecting to mailbox failed!<br>Special characters are not allowed for servername and username.',
	'LBL_DELETED_SUCCESSFULLY' => 'Deleted Successfully',
	'LBL_RULE_DELETION_FAILED' => 'Rule deletion failed',
	'LBL_RULES_SEQUENCE_INFO_IS_EMPTY' => 'Rules sequence info is empty',
	'LBL_SEQUENCE_UPDATED_SUCCESSFULLY' => 'Sequence updated successfully',
	'LBL_SCANNED_SUCCESSFULLY' => 'Scanned successfully',

	//Field Names
        
	'Scanner Name'                 => 'Scanner Name'                , 
	'Server'                       => 'Server Name'                 , 
	'Protocol'                     => 'Protocol'                    , 
	'User Name'                    => 'User Name'                   , 
	'Password'                     => 'Password'                    , 
	'SSL Type'                     => 'SSL Type'                    , 
	'SSL Method'                   => 'SSL Method'                  , 
	'Connect URL'                  => 'Connect URL'                 , 
	'Look For'                     => 'Look For'                    , 
	'After Scan'                   => 'After Scan'                  , 
        'Status'                       => 'Status'                      ,
        'Time Zone'                    => 'Time Zone'                   ,
        'Validate SSL Certificate'     => 'Validate SSL Certificate'    ,
        'Do Not Validate SSL Certificate'=> 'Do Not Validate SSL Certificate',
	'markas'                       => 'After Scan'                  ,
        //Ends
	//Field values & Messages
	'LBL_ENABLE' => 'Enable',
	'LBL_DISABLE' =>'Disable',
	'LBL_STATUS_MESSAGE' => 'Check To make active',
	'LBL_VALIDATE_SSL_CERTIFICATE' => 'Validate SSL Certificate',
	'LBL_DO_NOT_VALIDATE_SSL_CERTIFICATE' => 'Do Not Validate SSL Certificate',
	'LBL_ALL_MESSAGES_FROM_LAST_SCAN' => 'All messages from last scan',
	'LBL_UNREAD_MESSAGES_FROM_LAST_SCAN' => 'Unread messages from last scan',
	'LBL_MARK_MESSAGES_AS_READ' => 'Mark messages as read',
	'LBL_I_DONT_KNOW' => "I don't know",

	//Mailbox Actions
	'LBL_SCAN_NOW' => 'Scan Now',
	'LBL_RULES_LIST' => 'Rules List',
	'LBL_SELECT_FOLDERS' => 'Select Folders',

	//Action Messages
	'LBL_DELETED_SUCCESSFULLY' => 'Deleted successfully',
	'LBL_RULE_DELETION_FAILED' => 'Rule deletion failed',
	'LBL_SAVED_SUCCESSFULLY' => 'Saved successfully',
	'LBL_SCANED_SUCCESSFULLY' => 'Scanned successfully',
	'LBL_IS_IN_RUNNING_STATE' => 'is in running state',
	'LBL_FOLDERS_INFO_IS_EMPTY' => 'Folders information is empty',
	'LBL_RULES_SEQUENCE_INFO_IS_EMPTY' => 'Rules sequnce information is empty',

	//Folder Actions
	'LBL_UPDATE_FOLDERS' => 'Update Folders',

	//Rule Fields
	'fromaddress' => 'From',
	'toaddress' => 'To',
	'subject' => 'Subject',
	'body' => 'Body',
	'matchusing' => 'Match',
	'action' => 'Action',

	//Rules List View labels
	'LBL_PRIORITY' => 'Priority',
	'PRIORITISE_MESSAGE' => 'Drag and drop block to prioritise the rule',

	//Rule Field values & Messages
	'LBL_ALL_CONDITIONS' => 'All Conditions',
	'LBL_ANY_CONDITIOn' => 'Any Condition',

	//Rule Conditions
	'Contains' => 'Contains',
	'Not Contains' => 'Not Contains',
	'Equals' => 'Equals',
	'Not Equals' => 'Not Equals',
	'Begins With' => 'Begin',
	'Ends With' => 'End',
	'Regex' => 'Regex',

	//Rule Actions
	'CREATE_HelpDesk_FROM' => 'Create Ticket',
	'UPDATE_HelpDesk_SUBJECT' => 'Update Ticket',
	'LINK_Contacts_FROM' => 'Add to Contact [FROM]',
	'LINK_Contacts_TO' => 'Add to Contact [TO]',
	'LINK_Accounts_FROM' => 'Add to Organization [FROM]',
	'LINK_Accounts_TO' => 'Add to Organization [TO]',
	'LINK_Leads_FROM' => 'Add to Lead [FROM]',
	'LINK_Leads_TO' => 'Add to Lead [TO]',
    
    //Select Folder
    'LBL_UPDATE_FOLDERS' => 'Update Folders',
    'LBL_UNSELECT_ALL' => 'Unselect All',
	
	//Setup Rules
	'LBL_CONVERT_EMAILS_TO_RESPECTIVE_RECORDS' => 'Convert emails to respective records',
	'LBL_DRAG_AND_DROP_BLOCK_TO_PRIORITISE_THE_RULE' => 'The rule number indicates the priority. Drag and drop to change priority.',
	'LBL_ADD_RULE' => 'Add Rule',
	'LBL_PRIORITY' => 'Priority',
	'LBL_DELETE_RULE' => 'Delete rule',
	'LBL_BODY' => 'Body',
	'LBL_MATCH' => 'Match',
	'LBL_ACTION' => 'Action',
	'LBL_FROM' => 'From',
        'LBL_CONNECTION_ERROR' => 'Connecting to Mailbox failed. Check network connection and try again.'
       
);
$jsLanguageStrings = array(
	'JS_MAILBOX_DELETED_SUCCESSFULLY' => 'MailBox deleted Successfully',
	'JS_MAILBOX_LOADED_SUCCESSFULLY' => 'MailBox loaded Successfully'
);	
