<?
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once('include/database/PearDatabase.php');

$customviews = Array(Array('viewname'=>'All',
			   'setdefault'=>'1','setmetrics'=>'0',
			   'cvmodule'=>'Leads','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Hot Leads',
			   'setdefault'=>'0','setmetrics'=>'1',
			   'cvmodule'=>'Leads','stdfilterid'=>'','advfilterid'=>'0'),

		     Array('viewname'=>'This Month Leads',
			   'setdefault'=>'0','setmetrics'=>'0',
			   'cvmodule'=>'Leads','stdfilterid'=>'0','advfilterid'=>''),
			
		     Array('viewname'=>'All',
                           'setdefault'=>'1','setmetrics'=>'0',
                           'cvmodule'=>'Accounts','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Prospect Accounts',
                           'setdefault'=>'0','setmetrics'=>'1',
                           'cvmodule'=>'Accounts','stdfilterid'=>'','advfilterid'=>'1'),
		     
		     Array('viewname'=>'New This Week',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'Accounts','stdfilterid'=>'1','advfilterid'=>''),

		     Array('viewname'=>'All',
                           'setdefault'=>'1','setmetrics'=>'0',
                           'cvmodule'=>'Contacts','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Contacts Address',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'Contacts','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Todays Birthday',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'Contacts','stdfilterid'=>'2','advfilterid'=>''),

		     Array('viewname'=>'All',
                           'setdefault'=>'1','setmetrics'=>'0',
                           'cvmodule'=>'Potentials','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Potentials Won',
                           'setdefault'=>'0','setmetrics'=>'1',
                           'cvmodule'=>'Potentials','stdfilterid'=>'','advfilterid'=>'2'),

		     Array('viewname'=>'Prospecting',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'Potentials','stdfilterid'=>'','advfilterid'=>'3'),
 	 	     
		     Array('viewname'=>'All',
                           'setdefault'=>'1','setmetrics'=>'0',
                           'cvmodule'=>'HelpDesk','stdfilterid'=>'','advfilterid'=>''),
	
	             Array('viewname'=>'Open Tickets',
                           'setdefault'=>'0','setmetrics'=>'1',
                           'cvmodule'=>'HelpDesk','stdfilterid'=>'','advfilterid'=>'4'),
       	             
		     Array('viewname'=>'High Prioriy Tickets',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'HelpDesk','stdfilterid'=>'','advfilterid'=>'5'),

		     Array('viewname'=>'All',
                           'setdefault'=>'1','setmetrics'=>'0',
                           'cvmodule'=>'Quotes','stdfilterid'=>'','advfilterid'=>''),

		     Array('viewname'=>'Open Quotes',
                           'setdefault'=>'0','setmetrics'=>'1',
                           'cvmodule'=>'Quotes','stdfilterid'=>'','advfilterid'=>'6'),

		     Array('viewname'=>'Rejected Quotes',
                           'setdefault'=>'0','setmetrics'=>'0',
                           'cvmodule'=>'Quotes','stdfilterid'=>'','advfilterid'=>'7'),

		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Activities','stdfilterid'=>'','advfilterid'=>''),
		    
		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Emails','stdfilterid'=>'','advfilterid'=>''),
	
		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Invoice','stdfilterid'=>'','advfilterid'=>''),

		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Notes','stdfilterid'=>'','advfilterid'=>''),
		    
	            Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'PriceBooks','stdfilterid'=>'','advfilterid'=>''),	
	
	            Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Products','stdfilterid'=>'','advfilterid'=>''),

	            Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'PurchaseOrder','stdfilterid'=>'','advfilterid'=>''),

	            Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'SalesOrder','stdfilterid'=>'','advfilterid'=>''),

	            Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Vendors','stdfilterid'=>'','advfilterid'=>''),

		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Faq','stdfilterid'=>'','advfilterid'=>''),

		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Campaigns','stdfilterid'=>'','advfilterid'=>''),

		    Array('viewname'=>'All',
                          'setdefault'=>'1','setmetrics'=>'0',
                          'cvmodule'=>'Webmails','stdfilterid'=>'','advfilterid'=>''),

		    );


$cvcolumns = Array(Array('vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V',
                         'vtiger_leaddetails:firstname:firstname:Leads_First_Name:V',
                         'vtiger_leaddetails:company:company:Leads_Company:V',
			 'vtiger_leadaddress:phone:phone:Leads_Phone:V',
                         'vtiger_leadsubdetails:website:website:Leads_Website:V',
                         'vtiger_leaddetails:email:email:Leads_Email:E',
			 'vtiger_crmentity:smownerid:assigned_user_id:Leads_Assigned_To:V'),

	           Array('vtiger_leaddetails:firstname:firstname:Leads_First_Name:V',
                         'vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V',
                         'vtiger_leaddetails:company:company:Leads_Company:V',
                         'vtiger_leaddetails:leadsource:leadsource:Leads_Lead_Source:V',
                         'vtiger_leadsubdetails:website:website:Leads_Website:V',
                         'vtiger_leaddetails:email:email:Leads_Email:E'),

		   Array('vtiger_leaddetails:firstname:firstname:Leads_First_Name:V',
                         'vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V',
                         'vtiger_leaddetails:company:company:Leads_Company:V',
                         'vtiger_leaddetails:leadsource:leadsource:Leads_Lead_Source:V',
                         'vtiger_leadsubdetails:website:website:Leads_Website:V',
                         'vtiger_leaddetails:email:email:Leads_Email:E'),
	
		   Array('vtiger_account:accountname:accountname:Accounts_Account_Name:V',
                         'vtiger_accountbillads:city:bill_city:Accounts_City:V',
                         'vtiger_account:website:website:Accounts_Website:V',
                         'vtiger_account:phone:phone:Accounts_Phone:V',
                         'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),

		   Array('vtiger_account:accountname:accountname:Accounts_Account_Name:V',
			 'vtiger_account:phone:phone:Accounts_Phone:V',
			 'vtiger_account:website:website:Accounts_Website:V',
			 'vtiger_account:rating:rating:Accounts_Rating:V',
			 'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),

		   Array('vtiger_account:accountname:accountname:Accounts_Account_Name:V',
                         'vtiger_account:phone:phone:Accounts_Phone:V',
                         'vtiger_account:website:website:Accounts_Website:V',
                         'vtiger_accountbillads:city:bill_city:Accounts_City:V',
                         'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),

		   Array('vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V',
                         'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V',
                         'vtiger_contactdetails:title:title:Contacts_Title:V',
                         'vtiger_contactdetails:accountid:account_id:Contacts_Account_Name:I',
                         'vtiger_contactdetails:email:email:Contacts_Email:E',
                         'vtiger_contactdetails:phone:phone:Contacts_Office_Phone:V',
			 'vtiger_crmentity:smownerid:assigned_user_id:Contacts_Assigned_To:V'),

		   Array('vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V',
                         'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V',
                         'vtiger_contactaddress:mailingstreet:mailingstreet:Contacts_Mailing_Street:V',
                         'vtiger_contactaddress:mailingcity:mailingcity:Contacts_City:V',
                         'vtiger_contactaddress:mailingstate:mailingstate:Contacts_State:V',
			 'vtiger_contactaddress:mailingzip:mailingzip:Contacts_Zip:V',
			 'vtiger_contactaddress:mailingcountry:mailingcountry:Contacts_Country:V'),
		   
		   Array('vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V',
                         'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V',
                         'vtiger_contactdetails:title:title:Contacts_Title:V',
                         'vtiger_contactdetails:accountid:account_id:Contacts_Account_Name:I',
                         'vtiger_contactdetails:email:email:Contacts_Email:E',
			 'vtiger_contactsubdetails:otherphone:otherphone:Contacts_Phone:V',
			 'vtiger_crmentity:smownerid:assigned_user_id:Contacts_Assigned_To:V'),
		  
		   Array('vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V',
                         'vtiger_potential:accountid:account_id:Potentials_Account_Name:V',
                         'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V',
                         'vtiger_potential:leadsource:leadsource:Potentials_Lead_Source:V',
                         'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D',
                         'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),

                   Array('vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V',
                         'vtiger_potential:accountid:account_id:Potentials_Account_Name:V',
                         'vtiger_potential:amount:amount:Potentials_Amount:N',
                         'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D',
                         'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),

		   Array('vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V',
                         'vtiger_potential:accountid:account_id:Potentials_Account_Name:V',
                         'vtiger_potential:amount:amount:Potentials_Amount:N',
                         'vtiger_potential:leadsource:leadsource:Potentials_Lead_Source:V',
                         'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D',
                         'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),

		   Array('vtiger_crmentity:crmid::HelpDesk_Ticket_ID:I',
			 'vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V',
                         'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_to:I',
                         'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V',
                         'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V',
                         'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),

		   Array('vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V',
                         'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_to:I',
                         'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V',
                         'vtiger_troubletickets:product_id:product_id:HelpDesk_Product_Name:I',
                         'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),

		   Array('vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V',
                         'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_to:I',
                         'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V',
                         'vtiger_troubletickets:product_id:product_id:HelpDesk_Product_Name:I',
                         'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),

		   Array('vtiger_crmentity:crmid::Quotes_Quote_ID:I',
			 'vtiger_quotes:subject:subject:Quotes_Subject:V',
                         'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V',
                         'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I',
                         'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I',
                         'vtiger_quotes:total:hdnGrandTotal:Quotes_Total:I',
			 'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),

		   Array('vtiger_quotes:subject:subject:Quotes_Subject:V',
                         'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V',
                         'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I',
                         'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I',
                         'vtiger_quotes:validtill:validtill:Quotes_Valid_Till:D',
			 'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),

		   Array('vtiger_quotes:subject:subject:Quotes_Subject:V',
                         'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I',
                         'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I',
                         'vtiger_quotes:validtill:validtill:Quotes_Valid_Till:D',
                         'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),
			
		   Array('vtiger_activity:status:taskstatus:Activities_Status:V',
                         'vtiger_activity:activitytype:activitytype:Activities_Type:V',
                         'vtiger_activity:subject:subject:Activities_Subject:V',
                         'vtiger_cntactivityrel:contactid:contact_id:Activities_Contact_Name:I',
                         'vtiger_seactivityrel:crmid:parent_id:Activities_Related_to:V',
                         'vtiger_activity:date_start:date_start:Activities_Start_Date:D',
                         'vtiger_activity:due_date:due_date:Activities_End_Date:D',
                         'vtiger_crmentity:smownerid:assigned_user_id:Activities_Assigned_To:V'),

		   Array('vtiger_activity:subject:subject:Emails_Subject:V',
				'vtiger_crmentity:smownerid:assigned_user_id:Emails_Sender:V',
                 'vtiger_activity:date_start:date_start:Emails_Date_Sent:D'),
		
		   Array('vtiger_crmentity:crmid::Invoice_Invoice_Id:I',
                         'vtiger_invoice:subject:subject:Invoice_Subject:V',
                         'vtiger_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I',
                         'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V',
                         'vtiger_invoice:total:hdnGrandTotal:Invoice_Total:I',
                         'vtiger_crmentity:smownerid:assigned_user_id:Invoice_Assigned_To:V'),
		
		  Array('vtiger_notes:title:notes_title:Notes_Title:V',
                        'vtiger_notes:contact_id:contact_id:Notes_Contact_Name:I',
                        'vtiger_senotesrel:crmid:parent_id:Notes_Related_to:I',
                        'vtiger_notes:filename:filename:Notes_File:V',
                        'vtiger_crmentity:modifiedtime:modifiedtime:Notes_Modified_Time:V'),
		
		  Array('vtiger_pricebook:bookname:bookname:PriceBooks_Price_Book_Name:V',
                        'vtiger_pricebook:active:active:PriceBooks_Active:V'),
		  
		  Array('vtiger_products:productname:productname:Products_Product_Name:V',
                        'vtiger_products:productcode:productcode:Products_Product_Code:V',
                        'vtiger_products:commissionrate:commissionrate:Products_Commission_Rate:V',
                        'vtiger_products:qty_per_unit:qty_per_unit:Products_Qty/Unit:V',
                        'vtiger_products:unit_price:unit_price:Products_Unit_Price:V'),
		  
		  Array('vtiger_crmentity:crmid::PurchaseOrder_Order_Id:I',
                        'vtiger_purchaseorder:subject:subject:PurchaseOrder_Subject:V',
                        'vtiger_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I',
                        'vtiger_purchaseorder:tracking_no:tracking_no:PurchaseOrder_Tracking_Number:V',
                        'vtiger_crmentity:smownerid:assigned_user_id:PurchaseOrder_Assigned_To:V'),
		  
	          Array('vtiger_crmentity:crmid::SalesOrder_Order_Id:I',
                        'vtiger_salesorder:subject:subject:SalesOrder_Subject:V',
                        'vtiger_account:accountid:account_id:SalesOrder_Account_Name:V',
                        'vtiger_quotes:quoteid:quote_id:SalesOrder_Quote_Name:I',
                        'vtiger_salesorder:total:hdnGrandTotal:SalesOrder_Total:V',
                        'vtiger_crmentity:smownerid:assigned_user_id:SalesOrder_Assigned_To:V'),

	          Array('vtiger_vendor:vendorname:vendorname:Vendors_Vendor_Name:V',
			'vtiger_vendor:phone:phone:Vendors_Phone:V',
			'vtiger_vendor:email:email:Vendors_Email:E',
                        'vtiger_vendor:category:category:Vendors_Category:V'),




		 Array('vtiger_faq:id::Faq_FAQ_Id:I',
		       'vtiger_faq:question:question:Faq_Question:V',
		       'vtiger_faq:category:faqcategories:Faq_Category:V',
		       'vtiger_faq:product_id:product_id:Faq_Product_Name:I',
		       'vtiger_crmentity:createdtime:createdtime:Faq_Created_Time:D',
                       'vtiger_crmentity:modifiedtime:modifiedtime:Faq_Modified_Time:D'),
		      //this sequence has to be maintained 
		 Array('vtiger_campaign:campaignname:campaignname:Campaigns_Campaign_Name:V',
		       'vtiger_campaign:campaigntype:campaigntype:Campaigns_Campaign_Type:N',
		       'vtiger_campaign:campaignstatus:campaignstatus:Campaigns_Campaign_Status:N',
		       'vtiger_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:V',
		       'vtiger_campaign:closingdate:closingdate:Campaigns_Expected_Close_Date:D',
		       'vtiger_crmentity:smownerid:assigned_user_id:Campaigns_Assigned_To:V'),


		 Array('subject:subject:subject:Subject:V',
		       'from:fromname:fromname:From:N',
		       'to:tpname:toname:To:N',
		       'body:body:body:Body:V'),
                  );



$cvstdfilters = Array(Array('columnname'=>'vtiger_crmentity:modifiedtime:modifiedtime:Leads_Modified_Time',
                            'datefilter'=>'thismonth',
                            'startdate'=>'2005-06-01',
                            'enddate'=>'2005-06-30'),

		      Array('columnname'=>'vtiger_crmentity:createdtime:createdtime:Accounts_Created_Time',
                            'datefilter'=>'thisweek',
                            'startdate'=>'2005-06-19',
                            'enddate'=>'2005-06-25'),

		      Array('columnname'=>'vtiger_contactsubdetails:birthday:birthday:Contacts_Birthdate',
                            'datefilter'=>'today',
                            'startdate'=>'2005-06-25',
                            'enddate'=>'2005-06-25')
                     );

$cvadvfilters = Array(
                	Array(
               			 Array('columnname'=>'vtiger_leaddetails:leadstatus:leadstatus:Leads_Lead_Status:V',
		                      'comparator'=>'e',
        		              'value'=>'Hot'
                     			)
                     	 ),
		      		Array(
                          Array('columnname'=>'vtiger_account:account_type:accounttype:Accounts_Type:V',
                                'comparator'=>'e',
                                 'value'=>'Prospect'
                                 )
                           ),
				     Array(
                            Array('columnname'=>'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V',
                                  'comparator'=>'e',
                                  'value'=>'Closed Won'
                                 )
                           ),
				     Array(
                            Array('columnname'=>'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V',
                                  'comparator'=>'e',
                                  'value'=>'Prospecting'
                                 )
                           ),
				     Array(
                            Array('columnname'=>'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V',
                                  'comparator'=>'n',
                                  'value'=>'Closed'
                                 )
                           ),
				     Array(
                            Array('columnname'=>'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V',
                                  'comparator'=>'e',
                                  'value'=>'High'
                                 )
                           ),
				     Array(
	                        Array('columnname'=>'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V',
                                  'comparator'=>'n',
                                  'value'=>'Accepted'
                                 ),
						    Array('columnname'=>'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V',
                                  'comparator'=>'n',
                                  'value'=>'Rejected'
                                 )
                           ),
				     Array(
                            Array('columnname'=>'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V',
                                  'comparator'=>'e',
                                  'value'=>'Rejected'
                                 )
                           )
                     );

foreach($customviews as $key=>$customview)
{
	$queryid = insertCustomView($customview['viewname'],$customview['setdefault'],$customview['setmetrics'],$customview['cvmodule']);
	insertCvColumns($queryid,$cvcolumns[$key]);

	if(isset($cvstdfilters[$customview['stdfilterid']]))
	{
		$i = $customview['stdfilterid'];
		insertCvStdFilter($queryid,$cvstdfilters[$i]['columnname'],$cvstdfilters[$i]['datefilter'],$cvstdfilters[$i]['startdate'],$cvstdfilters[$i]['enddate']);
	}
	if(isset($cvadvfilters[$customview['advfilterid']]))
	{
		insertCvAdvFilter($queryid,$cvadvfilters[$customview['advfilterid']]);
	}
}

function insertCustomView($viewname,$setdefault,$setmetrics,$cvmodule)
{
	global $adb;

	$genCVid = $adb->getUniqueID("vtiger_customview");

	if($genCVid != "")
	{

		$customviewsql = "insert into vtiger_customview(cvid,viewname,setdefault,setmetrics,entitytype)";
		$customviewsql .= " values(".$genCVid.",'".$viewname."',".$setdefault.",".$setmetrics.",'".$cvmodule."')";
		$customviewresult = $adb->query($customviewsql);
	}
	return $genCVid;
}

function insertCvColumns($CVid,$columnslist)
{
	global $adb;
	if($CVid != "")
	{
		for($i=0;$i<count($columnslist);$i++)
		{
			$columnsql = "insert into vtiger_cvcolumnlist (cvid,columnindex,columnname)";
			$columnsql .= " values (".$CVid.",".$i.",'".$columnslist[$i]."')";
			$columnresult = $adb->query($columnsql);
		}
	}
}

function insertCvStdFilter($CVid,$filtercolumn,$filtercriteria,$startdate,$enddate)
{
	global $adb;
	if($CVid != "")
	{
		$stdfiltersql = "insert into vtiger_cvstdfilter(cvid,columnname,stdfilter,startdate,enddate)";
		$stdfiltersql .= " values (".$CVid.",'".$filtercolumn."',";
		$stdfiltersql .= "'".$filtercriteria."',";
		$stdfiltersql .= "'".$startdate."',";
		$stdfiltersql .= "'".$enddate."')";
		$stdfilterresult = $adb->query($stdfiltersql);
	}
}

function insertCvAdvFilter($CVid,$filters)
{
	global $adb;
	if($CVid != "")
	{
		foreach($filters as $i=>$filter)
		{
			$advfiltersql = "insert into vtiger_cvadvfilter(cvid,columnindex,columnname,comparator,value)";
			$advfiltersql .= " values (".$CVid.",".$i.",'".$filter['columnname']."',";
			$advfiltersql .= "'".$filter['comparator']."',";
			$advfiltersql .= "'".$filter['value']."')";
			$advfilterresult = $adb->query($advfiltersql);
		}
	}
}
?>
