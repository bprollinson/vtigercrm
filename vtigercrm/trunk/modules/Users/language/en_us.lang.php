<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Users/language/en_us.lang.php,v 1.18 2005/04/19 14:45:38 ray Exp $
 * Description:  Defines the English language pack for the Account module.
 ********************************************************************************/
 
$mod_strings = Array(
'LBL_MODULE_NAME'=>'Users',
'LBL_MODULE_TITLE'=>'Users: Home',
'LBL_SEARCH_FORM_TITLE'=>'User Search',
'LBL_LIST_FORM_TITLE'=>'User List',
'LBL_NEW_FORM_TITLE'=>'New User',
'LBL_CREATE_NEW_USER'=>'Create New User',
'LBL_USER'=>'Users:',
'LBL_LOGIN'=>'Login',
'LBL_USER_ROLE'=>'Role',
'LBL_LIST_NAME'=>'Name',
'LBL_LIST_LAST_NAME'=>'Last Name',
'LBL_LIST_USER_NAME'=>'UserName',
'LBL_LIST_DEPARTMENT'=>'Department',
'LBL_LIST_EMAIL'=>'Email',
'LBL_LIST_PRIMARY_PHONE'=>'Primary Phone',
'LBL_LIST_ADMIN'=>'Admin',
'LBL_LIST_PASSWORD'=>'Password',
'LBL_LIST_CONFIRM_PASSWORD'=>'Confirm Password',
//added for patch2
'LBL_GROUP_NAME'=>'Group',
'LBL_CURRENCY_NAME'=>'Currency',

'LBL_NEW_USER_BUTTON_TITLE'=>'New User [Alt+N]',
'LBL_NEW_USER_BUTTON_LABEL'=>'New User',
'LBL_NEW_USER_BUTTON_KEY'=>'N',
'LBL_DATE_FORMAT'=>'Date Format',

'LBL_ERROR'=>'Error:',
'LBL_PASSWORD'=>'Password:',
'LBL_USER_NAME'=>'User Name',
'LBL_CRM_ID'=>'CRM ID',
'LBL_FIRST_NAME'=>'First Name',
'LBL_LAST_NAME'=>'Last Name',
'LBL_YAHOO_ID'=>'Yahoo ID',
'LBL_USER_SETTINGS'=>'User Settings',
'LBL_THEME'=>'Theme:',
'LBL_LANGUAGE'=>'Language:',
'LBL_ADMIN'=>'Admin',
'LBL_USER_INFORMATION'=>'User Information',
'LBL_OFFICE_PHONE'=>'Office Phone',
'LBL_REPORTS_TO'=>'Reports to',
'LBL_OTHER_PHONE'=>'Other Phone',
'LBL_OTHER_EMAIL'=>'Other Email',
'LBL_NOTES'=>'Notes',
'LBL_DEPARTMENT'=>'Department',
'LBL_STATUS'=>'Status',
'LBL_TITLE'=>'Title',
'LBL_ANY_PHONE'=>'Any Phone:',
'LBL_ANY_EMAIL'=>'Any Email:',
'LBL_ADDRESS'=>'Street Address',
'LBL_CITY'=>'City',
'LBL_STATE'=>'State',
'LBL_POSTAL_CODE'=>'Postal Code',
'LBL_COUNTRY'=>'Country',
'LBL_NAME'=>'Name:',
'LBL_USER_SETTINGS'=>'User Settings',
'LBL_USER_INFORMATION'=>'User Information',
'LBL_MOBILE_PHONE'=>'Mobile',
'LBL_OTHER'=>'Other',
'LBL_FAX'=>'Fax',
'LBL_EMAIL'=>'E-Mail Id',
'LBL_HOME_PHONE'=>'Home Phone',
'LBL_ADDRESS_INFORMATION'=>'Address Information',
'LBL_PRIMARY_ADDRESS'=>'Primary Address:',

'LBL_CHANGE_PASSWORD_BUTTON_TITLE'=>'Change Password [Alt+P]',
'LBL_CHANGE_PASSWORD_BUTTON_KEY'=>'P',
'LBL_CHANGE_PASSWORD_BUTTON_LABEL'=>'Change Password',
'LBL_LOGIN_BUTTON_TITLE'=>'Login [Alt+L]',
'LBL_LOGIN_BUTTON_KEY'=>'L',
'LBL_LOGIN_BUTTON_LABEL'=>'Login',
'LBL_LOGIN_HISTORY_BUTTON_TITLE'=>'Login History [Alt+H]',
'LBL_LOGIN_HISTORY_BUTTON_KEY'=>'H',
'LBL_LOGIN_HISTORY_BUTTON_LABEL'=>'Login History',
'LBL_LOGIN_HISTORY_TITLE'=>'Users: Login History',
'LBL_RESET_PREFERENCES'=>'Reset To Default Preferences',

'LBL_CHANGE_PASSWORD'=>'Change Password',
'LBL_OLD_PASSWORD'=>'Old Password:',
'LBL_NEW_PASSWORD'=>'New Password:',
'LBL_CONFIRM_PASSWORD'=>'Confirm Password:',
'ERR_ENTER_OLD_PASSWORD'=>'Please enter your old password.',
'ERR_ENTER_NEW_PASSWORD'=>'Please enter your new password.',
'ERR_ENTER_CONFIRMATION_PASSWORD'=>'Please enter your password confirmation.',
'ERR_REENTER_PASSWORDS'=>'Please re-enter passwords.  The \"new password\" and \"confirm password\" values do not match.',
'ERR_INVALID_PASSWORD'=>'You must specify a valid username and password.',
'ERR_PASSWORD_CHANGE_FAILED_1'=>'User password change failed for ',
'ERR_PASSWORD_CHANGE_FAILED_2'=>' failed.  The new password must be set.',
'ERR_PASSWORD_INCORRECT_OLD'=>'Incorrect old password for user $this->user_name. Re-enter password information.',
'ERR_USER_NAME_EXISTS_1'=>'The user name ',
'ERR_USER_NAME_EXISTS_2'=>' already exists.  Duplicate user names are not allowed.<br>Change the user name to be unique.',
'ERR_LAST_ADMIN_1'=>'The user name ',
'ERR_LAST_ADMIN_2'=>' is the last Admin user.  At least one user must be an Admin user.<br>Check the Admin user setting.',

'ERR_DELETE_RECORD'=>"A record number must be specified to delete the account.",

// Additional Fields for i18n --- Release vtigerCRM 3.2 Patch 2
// Users--listroles.php , createrole.php , ListPermissions.php , editpermissions.php

'LBL_ROLES'=>'Roles',
'LBL_ROLES_SUBORDINATES'=>'Roles and Subordinates',
'LBL_ROLE_NAME'=>'Role Name',
'LBL_CREATE_NEW_ROLE'=>'Create New Role',

'LBL_CREATE_NEW_ROLE'=>'Create New Role',
'LBL_INDICATES_REQUIRED_FIELD'=>'Indicates Required Field',
'LBL_NEW_ROLE'=>'New Role',
'LBL_PARENT_ROLE'=>'Parent Role',

'LBL_LIST_ROLES'=>'List Roles',
'LBL_ENTITY_LEVEL_PERMISSIONS'=>'Entity Level Permissions',
'LBL_ENTITY'=>'Entity',
'LBL_CREATE_EDIT'=>'Create/Edit',
'LBL_DELETE'=>'Delete',
'LBL_VIEW'=>'View',
'LBL_LEADS'=>'Leads',
'LBL_ACCOUNTS'=>'Accounts',
'LBL_CONTACTS'=>'Contacts',
'LBL_OPPURTUNITIES'=>'Opportunities',
'LBL_TASKS'=>'Tasks',
'LBL_CASES'=>'Cases',
'LBL_EMAILS'=>'Emails',
'LBL_NOTES'=>'Notes',
'LBL_MEETINGS'=>'Meetings',
'LBL_CALLS'=>'Calls',
'LBL_IMPORT_PERMISSIONS'=>'Import Permissions',
'LBL_IMPORT_LEADS'=>'Import Leads',
'LBL_IMPORT_ACCOUNTS'=>'Import Accounts',
'LBL_IMPORT_CONTACTS'=>'Import Contacts',
'LBL_IMPORT_OPPURTUNITIES'=>'Import Opportunities',

'LBL_ROLE_DETAILS'=>'Role Details',
//added for vtigercrm4 rc
'LBL_FILE'=> 'File Name',
'LBL_FILE_TYPE'=>'File Type',
'LBL_UPLOAD'=>'Upload File',
'LBL_ATTACH_FILE'=>'Attach Mail Merge Template',
'LBL_EMAIL_TEMPLATES'=>'Email Templates',
'LBL_TEMPLATE_NAME'=>'Template Name',
'LBL_TEMPLATE_HEADER'=>'Template',
'LBL_TEMPLATE_DETAILS'=>'Template Details',
'LBL_EDIT_TEMPLATE'=>'Edit Template',
'LBL_DESCRIPTION'=>'Description',
'LBL_EMAIL_TEMPLATES_LIST'=>'Communication Templates > Email Templates',
'LBL_MAILMERGE_TEMPLATES_LIST'=>' > Communication Templates > Mail Merge Templates',
'LBL_MAILMERGE_TEMPLATES_ATTACHMENT' => '> Communication Templates > Attach Mail Merge Template',
'LBL_DOWNLOAD_NOW'=>'Download Now',
'LBL_DOWNLOAD'=>'Download',
'LBL_SELECT_MODULE'=>'Select Module',
'LBL_MERGE_FILE'=>'File : ',
'LBL_MERGE_MSG'=>'Select a module to assign this Template',

'LNK_GO_TO_TOP'=>'Go to Page Top',
'LNK_SAMPLE_EMAIL'=>'View Sample Email',
'LBL_COLON'=>':',
'LBL_EMAIL_TEMPLATE'=>'Email Template',
'LBL_NEW_TEMPLATE'=>'New Template',
'LBL_USE_MERGE_FIELDS_TO_EMAIL_CONTENT'=>'Use merge fields to personalize your email content.',
'LBL_AVAILABLE_MERGE_FIELDS'=>'Available Merge Fields',
'LBL_SELECT_FIELD_TYPE'=>'Select Field Type :',
'LBL_SELECT_FIELD'=>'Select Field :',
'LBL_MERGE_FIELD_VALUE'=>'Copy Merge Field Value :',
'LBL_CONTACT_FIELDS'=>'Contact Fields',
'LBL_LEAD_FIELDS'=>'Lead Fields',
'LBL_COPY_AND_PASTE_MERGE_FIELD'=>'Copy and paste the merge field value into your template below.',
'LBL_EMAIL_TEMPLATE_INFORMATION'=>'Communication Templates > Email Templates > Viewing',
'LBL_FOLDER'=>'Folder',
'LBL_PERSONAL'=>'Personal',
'LBL_PUBLIC'=>'Public',
'LBL_TEMPLATE_NAME'=>'Template Name:',
'LBL_SUBJECT'=>'Subject',
'LBL_BODY'=>'Email Body',
'LBL_TEMPLATE_TOOLS'=>'Tools',
'LBL_TEMPLATE_PUBLIC'=>'Public Access',
'LBL_TEMPLATE_PRIVATE'=>'Private Access',
'LBL_TEMPLATE_SUBJECT'=>'Email Subject',
'LBL_TEMPLATE_MESSAGE'=>'Email Message',


// Added fields in createnewgroup.php
'LBL_CREATE_NEW_GROUP'=>'Create New Group',
'LBL_NEW_GROUP'=>'New Group',
'LBL_EDIT_GROUP'=>'Edit Group',
'LBL_GROUP_NAME'=>'Group Name',
'LBL_GROUP_DETAILS'=>'Group Details',
'LBL_MEMBER_LIST'=>'Member List',
'LBL_MEMBER_AVLBL'=>'Member Available',
'LBL_MEMBER_SELECTED'=>'Selected Member',
'LBL_DESCRIPTION'=>'Description',

// Added fields in detailViewmailtemplate.html,listgroupmembers.php,listgroups.php
'LBL_DETAIL_VIEW_OF_EMAIL_TEMPLATE'=>'Detail View of Email Template',
'LBL_DETAIL_VIEW'=>'Detail View of',
'LBL_EDIT_VIEW'=>'Edit View of',
'LBL_GROUP_MEMBERS_LIST'=>'Group members list',
'LBL_GROUPS'=>'Groups',
'LBL_ADD_GROUP_BUTTON'=>'Add Group',
'LBL_WORD_TEMPLATES'=>'Mail Merge Templates',
'LBL_NEW_WORD_TEMPLATE'=>'New Template',

// Added fields in TabCustomise.php,html and UpdateTab.php,html
'LBL_CUSTOMISE_TABS'=>'Customize Tabs',
'LBL_CHOOSE_TABS'=>'Choose Tabs',
'LBL_AVAILABLE_TABS'=>'Available Tabs',
'LBL_SELECTED_TABS'=>'Selected Tabs',
'LBL_USER'=>'User',
'LBL_TAB_MENU_UPDATED'=>'Tab Menu Updated! kindly go to ',
'LBL_TO_VIEW_CHANGES'=>' to view the changes',

// Added to change homepage order
'LBL_CHANGE_HOMEPAGE_LABEL'=>'Homepage Order',
'LBL_CHANGE_HOMEPAGE_TITLE'=>'Homepage',

// Added fields in binaryfilelist.php
'LBL_OERATION'=>'Operation',

// Added fields in CreateProfile.php
'LBL_PROFILE_NAME'=>'Create New Profile:',
'LBL_NEW_PROFILE'=>'New Profile',
'LBL_NEW_PROFILE_NAME'=>'Profile Name',
'LBL_PARENT_PROFILE'=>'Parent Profile',
'LBL_BASIC_PROFILE_DETAILS'=>'Basic details of Profile',
'LBL_STEP_1_3'=>'Step 1 of 3',
'LBL_STEP_2_3'=>'Step 2 of 3',
'LBL_STEP_3_3'=>'Step 3 of 3',
'LBL_SELECT_BASE_PROFILE'=>'Select Base Profile',
'LBL_PROFILE_PRIVILEGES'=>'Profile Privileges',
'LBL_GLOBAL_PRIVILEGES'=>'Global Privileges',
'LBL_TAB_PRIVILEGES'=>'Tab Privileges',
'LBL_FIELD_PRIVILEGES'=>'Field Privileges',
'LBL_STANDARD_PRIVILEGES'=>'Standard Privileges',
'LBL_UTILITY_PRIVILEGES'=>'Utility Privileges',
'LBL_UTILITIES'=>'Utilities',
'LBL_BASE_PROFILE_MESG'=>'I would like to setup a base profile and edit privileges <b>(Recommended)</b>',
'LBL_BASE_PROFILE'=>'Base Profile:',
'LBL_OR'=>'OR',
'LBL_BASE_PROFILE_MESG_ADV'=>'I will choose the privileges from scratch <b>(Advanced Users)</b>',
'LBL_FOR'=>'for',
'LBL_GLOBAL_MESG_OPTION'=>'Select the options below to change global privileges',
'LBL_VIEW_ALL'=>'View all',
'LBL_EDIT_ALL'=>'Edit all',
'LBL_ALLOW'=>'Allows',
'LBL_MESG_VIEW'=>'to view all information / modules of vtiger CRM',
'LBL_MESG_EDIT'=>'to edit all information / modules of vtiger CRM',
'LBL_STD_MESG_OPTION'=>'Select the standard actions to be permitted',
'LBL_TAB_MESG_OPTION'=>'Select the tabs/modules to be permitted',
'LBL_UTILITY_MESG_OPTION'=>'Select the utility actions to be permitted',
'LBL_FIELD_MESG_OPTION'=>'Select the fields to be permitted',
'LBL_FINISH_BUTTON'=>'Finish',
'LBL_PROFILE_DETAIL_VIEW'=>'Detail View of Profile',


//Added fields in createrole.php
'LBL_HDR_ROLE_NAME'=>'Create New Role:',
'LBL_TITLE_ROLE_NAME'=>'New Role',
'LBL_ROLE_NAME'=>'Role Name',
'LBL_ROLE_PROFILE_NAME'=>'Associate With Profile',
'LBL_SPECIFY_ROLE_NAME'=>'Specify a name for new role :',
'LBL_ASSIGN_PROFILE'=>'Assign Profile(s)',
'LBL_PROFILE_SELECT_TEXT'=>'Select the Profiles below and click on assign button',
'LBL_PROFILES_AVLBL'=>'Profiles Available',
'LBL_ASSIGN_PROFILES'=>'Assigned Profiles',
'LBL_REPORTS_TO_ROLE'=>'Reports to Role',
'LBL_ASSOCIATED_PROFILES'=>'Associated Profiles :',
'LBL_ASSOCIATED_USERS'=>'Associated Users :',


//Added fields in OrgSharingDetailsView.php
'LBL_ORG_SHARING_PRIVILEGES'=>'Organisation Sharing  Privileges',
'LBL_EDIT_PERMISSIONS'=>'Edit Permissions',
'LBL_SAVE_PERMISSIONS'=>'Save Permissions',
'LBL_READ_ONLY'=>'Public: Read Only',
'LBL_EDIT_CREATE_ONLY'=>'Public: Read, Create/Edit',
'LBL_READ_CREATE_EDIT_DEL'=>'Public: Read, Create/Edit, Delete',
'LBL_PRIVATE'=>'Private',

//Added fields in listnotificationschedulers.php
'LBL_HDR_EMAIL_SCHDS'=>'Users : Email Notifications',
'LBL_EMAIL_SCHDS_DESC'=>'The following is the list of notifications that are activated automatically when the corresponding event has happened.',
'LBL_ACTIVE'=>'Active',
'LBL_NOTIFICATION'=>'Notification',
'LBL_DESCRIPTION'=>'Description',
'LBL_TASK_NOTIFICATION'=>'Delayed Task Notification',
'LBL_TASK_NOTIFICATION_DESCRITPION'=>'Notify when a task is delayed beyond 24 hrs',
'LBL_MANY_TICKETS'=>'Too many tickets  Notification',
'LBL_MANY_TICKETS_DESCRIPTION'=>'Notify when a particular entity is allocated too many tickets, might reflect Service Level commitments',
'LBL_PENDING_TICKETS'=>'Pending Tickets Notification',
'LBL_TICKETS_DESCRIPTION'=>'Notify for getting attention to status of tickets which are pending',
'LBL_START_NOTIFICATION'=>'Support Start Notification',
'LBL_START_DESCRIPTION'=>'Notifiy stating the start of support/service',
'LBL_BIG_DEAL'=>'Big Deal Notification',
'LBL_BIG_DEAL_DESCRIPTION'=>'Notify on completion of big deal',
'LBL_SUPPORT_NOTICIATION'=>'Support End Notification',
'LBL_SUPPORT_DESCRIPTION'=>'Notify when support is about to end',
'LBL_BUTTON_UPDATE'=>'Update',
'LBL_MODULENAMES'=>'Module',

//Added fields in ListFieldPermissions.html
'LBL_FIELD_PERMISSION_FIELD_NAME'=>'Field Name',
'LBL_FIELD_PERMISSION_VISIBLE'=>'Visible',
'LBL_FIELD_PERMISSIOM_TABLE_HEADER'=>'Standard Fields',
'LBL_FIELD_LEVEL_ACCESS'=>'Field Level Access',

//Added fields after 4.0.1
'LBL_SIGNATURE'=>'Signature',

//Added for Event Reminder 4.2 Alpha release
'LBL_ACTIVITY_NOTIFICATION'=>'Event Reminder Notification',
'LBL_ACTIVITY_REMINDER_DESCRIPTION'=>'Notify before an event to occur based on the reminder set',
'LBL_MESSAGE'=>'Message',

//Added for Global Privileges

'Public: Read Only'=>'Public: Read Only',
'Public: Read, Create/Edit'=>'Public: Read, Create/Edit',
'Public: Read, Create/Edit, Delete'=>'Public: Read, Create/Edit, Delete',
'Private'=>'Private',
'Hide Details'=>'Hide Details',
'Hide Details and Add Events'=>'Hide Details and Add Events',
'Show Details'=>'Show Details',
'Show Details and Add Events'=>'Show Details and Add Events',

'LBL_USR_CANNOT_ACCESS'=>'Users cannot access other users ',
'LBL_USR_CAN_ACCESS'=>'Users can ',
'LBL_USR_OTHERS'=>' other users ',

'Read Only '=>'Read Only ',
'Read, Create/Edit, Delete '=>'Read, Create/Edit, Delete ',
'Read, Create/Edit '=>'Read, Create/Edit ',
'Read/Write'=>'Read/Write',
'LBL_GO_TO_TOP'=>'Go to Top',
'LNK_CLICK_HERE'=>'Click here',
'LBL_RULE_NO'=>'Rule No.',
'LBL_CAN_BE_ACCESSED'=>'can be accessed by',
'LBL_PRIVILEGES'=>'Privileges',
'LBL_OF'=>'of',



//Added for 4.2GA support for mail server integration
'LBL_ADD_MAILSERVER_BUTTON_TITLE'=>'Add Mail Server',
'LBL_ADD_MAILSERVER_BUTTON_KEY'=>'M',
'LBL_ADD_MAILSERVER_BUTTON_LABEL'=>'Add Mail Server',

'LBL_LIST_MAILSERVER_BUTTON_TITLE'=>'List Mail Server',
'LBL_LIST_MAILSERVER_BUTTON_KEY'=>'L',
'LBL_LIST_MAILSERVER_BUTTON_LABEL'=>'List Mail Server',
//added for inventory terms and conditions
'INV_TANDC'=>'Terms & Coditions',
'INV_TERMSANDCONDITIONS'=>'Inventory Terms & Coditions',
'LBL_INV_TERMSANDCONDITIONS'=>'Inventory Management', 


'INVENTORYNOTIFICATION'=>'Inventory Notifications',
'LBL_INVENTORY_NOTIFICATIONS'=>'Edit Inventory Email Notifications',
'LBL_INV_NOT_DESC'=>'The following are the list of notifications that are sent to the product handler regarding the demand and the current quantity in hand during the creation of a Quote, SalesOrder and Invoice.',

'InvoiceNotification'=>'Product Stock Notification during Invoice Generation',
'InvoiceNotificationDescription'=>'When the product stock level goes below the re-order level, notification will be sent to the product handler',

'QuoteNotification'=>'Product Stock Notification during Quote Generation',
'QuoteNotificationDescription'=>'During quote generation if the product stock in warehouse is lesser than the  quantity mentioned in quote then this notification will be sent to the product handler',

'SalesOrderNotification'=>'Product Stock Notification during Sales Order Generation',
'SalesOrderNotificationDescription'=>'During sales order generation if the product stock in warehouse is lesser than the quantity mentioned in sales order then this notification will be sent to the product handler',

//New addition for 4.2 GA
'LBL_USER_FIELDS'=>'User Fields',
'LBL_NOTE_DO_NOT_REMOVE_INFO'=>'Note: Donot remove or alter the values within {  }',

//Added for patch2
'LBL_FILE_INFORMATION'=>'File Information',

//Added after pathc2
'LBL_LEAD_FIELD_ACCESS'=>'Lead Field Access',

'LBL_ACCOUNT_FIELD_ACCESS'=>'Account Field Access',

'LBL_CONTACT_FIELD_ACCESS'=>'Contact Field Access',

'LBL_OPPORTUNITY_FIELD_ACCESS'=>'Potential Field Access',

'LBL_HELPDESK_FIELD_ACCESS'=>'HelpDesk Field Access',

'LBL_PRODUCT_FIELD_ACCESS'=>'Product Field Access',

'LBL_NOTE_FIELD_ACCESS'=>'Note Field Access',

'LBL_EMAIL_FIELD_ACCESS'=>'Email Field Access',

'LBL_TASK_FIELD_ACCESS'=>'Task Field Access',

'LBL_EVENT_FIELD_ACCESS'=>'Event Field Access',
'LBL_VENDOR_FIELD_ACCESS'=>'Vendor Field Access',
'LBL_PB_FIELD_ACCESS'=>'PriceBook Field Access',
'LBL_QUOTE_FIELD_ACCESS'=>'Quote Field Access',
'LBL_PO_FIELD_ACCESS'=>'Purchase Order Field Access',
'LBL_SO_FIELD_ACCESS'=>'Sales Order Access',
'LBL_INVOICE_FIELD_ACCESS'=>'Invoice Field Access',

//given for calendar color for an user user
'LBL_COLOR'=>'Color in Calendar',
//added for activity view in home page
'LBL_ACTIVITY_VIEW'=>'Default Activity View',
//Added to change Home page order
'LBL_HOMEPAGE_ORDER_UPDATE'=>'Update Home Page Block Order',
'LBL_HOMEPAGE_ID'=>'Block Order',
'ERR_INVALID_USER'=>'Invalid access--Please call from My Accounts',
'ALVT'=>'Top Accounts',
'PLVT'=>'Top Potentials',
'QLTQ'=>'Top Quotes',
'CVLVT'=>'Key Metrics',
'HLT'=>'HelpDesk Top Tickets',
'OLV'=>'Top Open Activities',
'GRT'=>'Top Group Tasks',
'OLTSO'=>'Top Sales Orders',
'ILTI'=>'Top Invoices',
//Added for 5.0 alpha
'LBL_GROUP_NAME_ERROR'=>'Group Name already exists!',
'MNL'=>'My New Leads',
'LBL_LEAD_VIEW'=>'Default Lead View',
'LBL_TAG_CLOUD'=>'Tag Cloud',
'LBL_LIST_TOOLS'=>'Operations',
'LBL_STATISTICS'=>'Statistics',
'LBL_TOTAL'=>'Total :',
'LBL_OTHERS'=>'Others :',
'LBL_USERS'=>'User(s)',
'LBL_USER_LOGIN_ROLE'=>'User Login & Role',
'LBL_USER_MORE_INFN'=>'More Information',
'LBL_USER_ADDR_INFN'=>'Address Information',
'LBL_USER_IMAGE'=>'User Image',
'LBL_USR'=>'Users',

'LBL_MY'=>'My',
'LBL_MY_DEFAULTS'=>'My Defaults',
'LBL_MY_DESG'=>'My Designation & Contact Details',
'LBL_MY_ADDR'=>'My Postal Address',
'LBL_MY_DETAILS'=>'My Details',
'LBL_MY_PHOTO'=>'My Photo',
'LBL_CHANGE_PHOTO'=>'Change Photo...',


//Added for Access Privileges

'LBL_GLOBAL_FIELDS_MANAGER'=>'Global Fields Manager',
'LBL_GLOBAL_ACCESS_PRIVILEGES'=>'Global Access Privileges',
'LBL_CUSTOM_ACCESS_PRIVILEGES'=>'Custom Access Privileges',
'LBL_BOTH'=>'Both',
'LBL_VIEW'=>'view',
'LBL_RECALCULATE_BUTTON'=>'Recalculate',
'LBL_ADD_PRIVILEGES_BUTTON'=>'Add Privileges',
'LBL_CUSTOM_ACCESS_MESG'=>'No Custom Access Rules defined .',
'LBL_CREATE_RULE_MESG'=>'to create a new Rule',
'LBL_SELECT_SCREEN'=>'Select the Screen / Module :',
'LBL_FIELDS_AVLBL'=>'Fields Available in',
'LBL_FIELDS_SELECT_DESELECT'=>'Select or De-Select fields to be shown'


);

?>
