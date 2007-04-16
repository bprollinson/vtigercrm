<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
require_once("modules/Dashboard/Entity_charts.php");
global $tmp_dir;
global $mod_strings,$app_strings;
global $current_user;
require('user_privileges/user_privileges_'.$current_user->id.'.php');

$period=($_REQUEST['period'])?$_REQUEST['period']:"tmon"; // Period >> lmon- Last Month, tmon- This Month, lweek-LastWeek, tweek-ThisWeek; lday- Last Day 
$type=($_REQUEST['type'])?$_REQUEST['type']:"leadsource";
$dates_values=start_end_dates($period); //To get the stating and End dates for a given period 
$date_start=$dates_values[0]; //Starting date 
$end_date=$dates_values[1]; // Ending Date
$period_type=$dates_values[2]; //Period type as MONTH,WEEK,LDAY
$width=$dates_values[3];
$height=$dates_values[4];

//It gives all the dates in between the starting and ending dates and also gives the number of days,declared in utils.php
$no_days_dates=get_days_n_dates($date_start,$end_date);
$days=$no_days_dates[0];
$date_array=$no_days_dates[1]; //Array containig all the dates 
$user_id=$current_user->id;

// Query for Leads
$leads_query="select vtiger_crmentity.crmid,vtiger_crmentity.createdtime, vtiger_leaddetails.*, vtiger_crmentity.smownerid, vtiger_leadscf.* from vtiger_leaddetails inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_leaddetails.leadid inner join vtiger_leadsubdetails on vtiger_leadsubdetails.leadsubscriptionid=vtiger_leaddetails.leadid inner join vtiger_leadaddress on vtiger_leadaddress.leadaddressid=vtiger_leadsubdetails.leadsubscriptionid inner join vtiger_leadscf on vtiger_leaddetails.leadid = vtiger_leadscf.leadid left join vtiger_leadgrouprelation on vtiger_leadscf.leadid=vtiger_leadgrouprelation.leadid left join vtiger_groups on vtiger_groups.groupname=vtiger_leadgrouprelation.groupname where vtiger_crmentity.deleted=0 and vtiger_leaddetails.converted=0 ";


//Query for Accounts
$account_query="select vtiger_crmentity.*, vtiger_account.*, vtiger_accountscf.* from vtiger_account inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_account.accountid inner join vtiger_accountbillads on vtiger_account.accountid=vtiger_accountbillads.accountaddressid inner join vtiger_accountshipads on vtiger_account.accountid=vtiger_accountshipads.accountaddressid inner join vtiger_accountscf on vtiger_account.accountid = vtiger_accountscf.accountid left join vtiger_accountgrouprelation on vtiger_accountscf.accountid=vtiger_accountgrouprelation.accountid left join vtiger_groups on vtiger_groups.groupname=vtiger_accountgrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";


//Query For Products qty in stock
$products_query="select distinct(vtiger_crmentity.crmid),vtiger_crmentity.createdtime,vtiger_products.* from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_products.productid left join vtiger_inventoryproductrel on vtiger_products.productid = vtiger_inventoryproductrel.id where vtiger_crmentity.deleted=0 ";

//Query for Potential
$potential_query= "select  vtiger_crmentity.*,vtiger_account.accountname, vtiger_potential.*, vtiger_potentialscf.*, vtiger_potentialgrouprelation.groupname from vtiger_potential inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid inner join vtiger_account on vtiger_potential.accountid = vtiger_account.accountid inner join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid left join vtiger_potentialgrouprelation on vtiger_potential.potentialid=vtiger_potentialgrouprelation.potentialid left join vtiger_groups on vtiger_groups.groupname=vtiger_potentialgrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";

//Query for Sales Order
$so_query="select vtiger_crmentity.*,vtiger_salesorder.*,vtiger_account.accountid,vtiger_quotes.quoteid from vtiger_salesorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid inner join vtiger_sobillads on vtiger_salesorder.salesorderid=vtiger_sobillads.sobilladdressid inner join vtiger_soshipads on vtiger_salesorder.salesorderid=vtiger_soshipads.soshipaddressid left join vtiger_salesordercf on vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid left join vtiger_sogrouprelation on vtiger_salesorder.salesorderid=vtiger_sogrouprelation.salesorderid left join vtiger_groups on vtiger_groups.groupname=vtiger_sogrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";


//Query for Purchase Order

$po_query="select vtiger_crmentity.*,vtiger_purchaseorder.* from vtiger_purchaseorder inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_purchaseorder.purchaseorderid left outer join vtiger_vendor on vtiger_purchaseorder.vendorid=vtiger_vendor.vendorid inner join vtiger_pobillads on vtiger_purchaseorder.purchaseorderid=vtiger_pobillads.pobilladdressid inner join vtiger_poshipads on vtiger_purchaseorder.purchaseorderid=vtiger_poshipads.poshipaddressid left join vtiger_purchaseordercf on vtiger_purchaseordercf.purchaseorderid = vtiger_purchaseorder.purchaseorderid left join vtiger_pogrouprelation on vtiger_purchaseorder.purchaseorderid=vtiger_pogrouprelation.purchaseorderid left join vtiger_groups on vtiger_groups.groupname=vtiger_pogrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";

// Query for Quotes
$quotes_query="select vtiger_crmentity.*,vtiger_quotes.* from vtiger_quotes inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid inner join vtiger_quotesbillads on vtiger_quotes.quoteid=vtiger_quotesbillads.quotebilladdressid inner join vtiger_quotesshipads on vtiger_quotes.quoteid=vtiger_quotesshipads.quoteshipaddressid left join vtiger_quotescf on vtiger_quotes.quoteid = vtiger_quotescf.quoteid left outer join vtiger_account on vtiger_account.accountid=vtiger_quotes.accountid left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid left join vtiger_quotegrouprelation on vtiger_quotes.quoteid=vtiger_quotegrouprelation.quoteid left join vtiger_groups on vtiger_groups.groupname=vtiger_quotegrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";

//Query for Invoice
$invoice_query="select vtiger_crmentity.*,vtiger_invoice.* from vtiger_invoice inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_invoice.invoiceid inner join vtiger_invoicebillads on vtiger_invoice.invoiceid=vtiger_invoicebillads.invoicebilladdressid inner join vtiger_invoiceshipads on vtiger_invoice.invoiceid=vtiger_invoiceshipads.invoiceshipaddressid left outer join vtiger_salesorder on vtiger_salesorder.salesorderid=vtiger_invoice.salesorderid inner join vtiger_invoicecf on vtiger_invoice.invoiceid = vtiger_invoicecf.invoiceid left join vtiger_invoicegrouprelation on vtiger_invoice.invoiceid=vtiger_invoicegrouprelation.invoiceid left join vtiger_groups on vtiger_groups.groupname=vtiger_invoicegrouprelation.groupname left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid where vtiger_crmentity.deleted=0 ";

//Query for tickets
$helpdesk_query=" select vtiger_troubletickets.status AS ticketstatus, vtiger_ticketgrouprelation.groupname AS ticketgroupname, vtiger_troubletickets.*,vtiger_crmentity.* from vtiger_troubletickets inner join vtiger_ticketcf on vtiger_ticketcf.ticketid = vtiger_troubletickets.ticketid inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_troubletickets.ticketid left join vtiger_ticketgrouprelation on vtiger_troubletickets.ticketid=vtiger_ticketgrouprelation.ticketid left join vtiger_groups on vtiger_groups.groupname=vtiger_ticketgrouprelation.groupname left join vtiger_contactdetails on vtiger_troubletickets.parent_id=vtiger_contactdetails.contactid left join vtiger_account on vtiger_account.accountid=vtiger_troubletickets.parent_id left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id and vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid where vtiger_crmentity.deleted=0";

//Query for campaigns
$campaign_query=" select vtiger_campaign.*,vtiger_crmentity.* from vtiger_campaign inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_campaign.campaignid inner join vtiger_campaigncontrel where vtiger_campaigncontrel.campaignid=vtiger_campaign.campaignid and vtiger_crmentity.deleted=0";


//Query for tickets by account
$tickets_by_account="select vtiger_troubletickets.*, vtiger_account.* from vtiger_troubletickets inner join vtiger_account on vtiger_account.accountid=vtiger_troubletickets.parent_id";
 
//Query for tickets by contact
$tickets_by_contact="select vtiger_troubletickets.*, vtiger_contactdetails.* from vtiger_troubletickets inner join vtiger_contactdetails on vtiger_contactdetails.contactid=vtiger_troubletickets.parent_id";

//Query for product by category

$product_category = "select vtiger_products.*,vtiger_crmentity.deleted from vtiger_products inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_products.productid where vtiger_crmentity.deleted=0";

/**This function generates the security parameters for a given module based on the assigned profile 
*Param $module - module name
*Returns an string value
*/

function dashboard_check($module)
{
	global $current_user;
	$sec_parameter = '';
	$tab_id = getTabid($module);
	require('user_privileges/user_privileges_'.$current_user->id.'.php');
	require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
	if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1 && $defaultOrgSharingPermission[$tab_id] == 3)
	{
		$sec_parameter=getListViewSecurityParameter($module);
	}
	return $sec_parameter;
}

$graph_array = Array(
	  "DashboardHome" => $mod_strings['DashboardHome'],
          "leadsource" => $mod_strings['leadsource'],
          "leadstatus" => $mod_strings['leadstatus'],
          "leadindustry" => $mod_strings['leadindustry'],
          "salesbyleadsource" => $mod_strings['salesbyleadsource'],
          "salesbyaccount" => $mod_strings['salesbyaccount'],
	  "salesbyuser" => $mod_strings['salesbyuser'],
	  "salesbyteam" => $mod_strings['salesbyteam'],
          "accountindustry" => $mod_strings['accountindustry'],
          "productcategory" => $mod_strings['productcategory'],
	  "productbyqtyinstock" => $mod_strings['productbyqtyinstock'],
	  "productbypo" => $mod_strings['productbypo'],
	  "productbyquotes" => $mod_strings['productbyquotes'],
	  "productbyinvoice" => $mod_strings['productbyinvoice'],
          "sobyaccounts" => $mod_strings['sobyaccounts'],
          "sobystatus" => $mod_strings['sobystatus'],
          "pobystatus" => $mod_strings['pobystatus'],
          "quotesbyaccounts" => $mod_strings['quotesbyaccounts'],
          "quotesbystage" => $mod_strings['quotesbystage'],
          "invoicebyacnts" => $mod_strings['invoicebyacnts'],
          "invoicebystatus" => $mod_strings['invoicebystatus'],
          "ticketsbystatus" => $mod_strings['ticketsbystatus'],
          "ticketsbypriority" => $mod_strings['ticketsbypriority'],
	  "ticketsbycategory" => $mod_strings['ticketsbycategory'], 
	  "ticketsbyuser" => $mod_strings['ticketsbyuser'],
	  "ticketsbyteam" => $mod_strings['ticketsbyteam'],
	  "ticketsbyproduct"=> $mod_strings['ticketsbyproduct'],
	  "contactbycampaign"=> $mod_strings['contactbycampaign'],
	  "ticketsbyaccount"=> $mod_strings['ticketsbyaccount'],
	  "ticketsbycontact"=> $mod_strings['ticketsbycontact'],
          );

?>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<!--char goes here-->
				<?php 
				//Charts for Lead Source
                    if($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadsource") && (getFieldVisibilityPermission('Leads',$user_id,'leadsource') == "0"))
                    {
                    	$graph_by="leadsource";
                    	$graph_title= $mod_strings['leadsource'];
                    	$module="Leads";
                    	$where="";
                    	$query=$leads_query." ".dashboard_check($module);                   
                    	echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    
                    }
                    // To display the charts  for Lead status                   
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadstatus")&& (getFieldVisibilityPermission('Leads',$user_id,'leadstatus') == "0"))
                    {
                    	$graph_by="leadstatus";
                    	$graph_title= $mod_strings['leadstatus'];
                    	$module="Leads";
                    	$where="";
                    	$query=$leads_query." ".dashboard_check($module);
                    	echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Charts for Lead Industry
                    elseif ($profileTabsPermission[getTabid("Leads")] == 0 && ($type == "leadindustry") && (getFieldVisibilityPermission('Leads',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['leadindustry'];
                            $module="Leads";
                            $where="";
                            $query=$leads_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales by Lead Source
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyleadsource")&& (getFieldVisibilityPermission('Potentials',$user_id,'leadsource') == "0"))
                    {
                            $graph_by="leadsource";
                            $graph_title=$mod_strings['salesbyleadsource'];
                            $module="Potentials";
                            $where=" and vtiger_potential.sales_stage like '%Closed Won%' ";
                            $query=$potential_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales by Account
                    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyaccount") && (getFieldVisibilityPermission('Potentials',$user_id,'account_id') == "0"))
                    {
                    	 $graph_by="accountid";
                         $graph_title=$mod_strings['salesbyaccount'];
                         $module="Potentials";
                         $where=" and vtiger_potential.sales_stage like '%Closed Won%' ";
                         $query=$potential_query." ".dashboard_check($module);
                         echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Sales by User
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyuser"))
		    {
			$graph_by="smownerid";
			$graph_title=$mod_strings['salesbyuser'];
			$module="Potentials";
			$where=" and vtiger_potential.sales_stage like '%Closed Won%' and (vtiger_crmentity.smownerid != NULL || vtiger_crmentity.smownerid != ' ')";
			$query=$potential_query." ".dashboard_check($module);
			echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Sales by team
		    elseif ($profileTabsPermission[getTabid("Potentials")] == 0 && ($type == "salesbyteam"))
		    {
			$graph_by="groupname";
			$graph_title=$mod_strings['salesbyteam'];
			$module="Potentials";
			$where=" and vtiger_potential.sales_stage like '%Closed Won%' and (vtiger_potentialgrouprelation.groupname != NULL || vtiger_potentialgrouprelation.groupname != '')";
			$query=$potential_query." ".dashboard_check($module);
			echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
                    //Charts for Account by Industry
                    elseif ($profileTabsPermission[getTabid("Accounts")] == 0 && ($type == "accountindustry") && (getFieldVisibilityPermission('Accounts',$user_id,'industry') == "0"))
                    {
                    	$graph_by="industry";
                            $graph_title=$mod_strings['accountindustry'];
                            $module="Accounts";
                            $where="";
                            $query=$account_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Charts for Products by Category
                    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productcategory") && (getFieldVisibilityPermission('Products',$user_id,'productcategory') == "0"))
                    {
                    	$graph_by="productcategory";
                            $graph_title=$mod_strings['productcategory'];
                            $module="Products";
                            $where="";
                            $query=$product_category." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Charts for Products by Quantity in stock
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyqtyinstock") && (getFieldVisibilityPermission('Products',$user_id,'qtyinstock') == "0"))
		    {
			$graph_by="productname";
			    $graph_title=$mod_strings['productbyqtyinstock'];
			    $module="Products";
			    $where="";
			    $query=$products_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by PO
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbypo"))
		    { 
			$graph_by="";
			    $graph_title=$mod_strings['productbypo'];
			    $module="Products";
			    $where="";
			    $query=$products_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by Quotes
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyquotes"))
		    { 
                        $graph_by="";
   			    $graph_title=$mod_strings['productbyquotes'];
			    $module="Products";
			    $where=""; 
			    $query=$products_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Charts for Products by Invoice
		    elseif ($profileTabsPermission[getTabid("Products")] == 0 && ($type == "productbyinvoice"))
		    {
		        $graph_by="invoiceid";
			    $graph_title=$mod_strings['productbyinvoice'];
			    $module="Products";
			    $where="";
			    $query=$products_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }

                    // Sales Order by Accounts
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobyaccounts") && (getFieldVisibilityPermission('SalesOrder',$user_id,'account_id') == "0"))
                    {
                    	$graph_by="accountid";
                            $graph_title=$mod_strings['sobyaccounts'];
                            $module="SalesOrder";
                            $where="";
                            $query=$so_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Sales Order by Status
                    elseif ($profileTabsPermission[getTabid("SalesOrder")] == 0 && ($type == "sobystatus") && (getFieldVisibilityPermission('SalesOrder',$user_id,'sostatus') == "0"))
                    {
                            $graph_by="sostatus";
                            $graph_title=$mod_strings['sobystatus'];
                            $module="SalesOrder";
                            $where="";
                            $query=$so_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Purchase Order by Status
                    elseif ($profileTabsPermission[getTabid("PurchaseOrder")] == 0 && ($type == "pobystatus") && (getFieldVisibilityPermission('PurchaseOrder',$user_id,'postatus') == "0"))
                    {
                            $graph_by="postatus";
                            $graph_title=$mod_strings['pobystatus'];
                            $module="PurchaseOrder";
                            $where="";
                            $query=$po_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Quotes by Accounts
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbyaccounts") && (getFieldVisibilityPermission('Quotes',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title= $mod_strings['quotesbyaccounts'];
                            $module="Quotes";
                            $where="";
                            $query=$quotes_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Quotes by Stage
                    elseif ($profileTabsPermission[getTabid("Quotes")] == 0 && ($type == "quotesbystage") && (getFieldVisibilityPermission('Quotes',$user_id,'quotestage') == "0"))
                    {
                            $graph_by="quotestage";
                            $graph_title=$mod_strings['quotesbystage'];
                            $module="Quotes";
                            $where="";
                            $query=$quotes_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Invoice by Accounts
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebyacnts") && (getFieldVisibilityPermission('Invoice',$user_id,'account_id') == "0"))
                    {
                            $graph_by="accountid";
                            $graph_title=$mod_strings['invoicebyacnts'];
                            $module="Invoice";
                            $where="";
                            $query=$invoice_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Invoices by status
                    elseif ($profileTabsPermission[getTabid("Invoice")] == 0 && ($type == "invoicebystatus") && (getFieldVisibilityPermission('Invoice',$user_id,'invoicestatus') == "0"))
                    {
                            $graph_by="invoicestatus";
                            $graph_title=$mod_strings['invoicebystatus'];
                            $module="Invoice";
                            $where="";
                            $query=$invoice_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Tickets by Status
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbystatus") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketstatus') == "0"))
                    {
                            $graph_by="ticketstatus";
                            $graph_title=$mod_strings['ticketsbystatus'];
                            $module="HelpDesk";
                            $where="";
                            $query=$helpdesk_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
                    //Tickets by Priority
                    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbypriority") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketpriorities') == "0"))
                    {
                            $graph_by="priority";
                            $graph_title=$mod_strings['ticketsbypriority'];
                            $module="HelpDesk";
                            $where="";
                            $query=$helpdesk_query." ".dashboard_check($module);
                            echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
                    }
		    //Tickets by Category
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycategory") && (getFieldVisibilityPermission('HelpDesk',$user_id,'ticketcategories') == "0"))
		    {
			    $graph_by="category";
			    $graph_title=$mod_strings['ticketsbycategory'];
			    $module="HelpDesk";
			    $where="";
			    $query=$helpdesk_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by User   
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyuser"))
		    {
			    $graph_by="smownerid";
			    $graph_title=$mod_strings['ticketsbyuser'];
			    $module="HelpDesk";
			    $where=" and (vtiger_crmentity.smownerid != NULL || vtiger_crmentity.smownerid != ' ')";
			    $query=$helpdesk_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Team
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyteam"))
		    {
			    $graph_by="ticketgroupname";
			    $graph_title=$mod_strings['ticketsbyteam'];
			    $module="HelpDesk";
			    $where=" and (vtiger_ticketgrouprelation.groupname != NULL || vtiger_ticketgrouprelation.groupname != ' ')";
			    $query=$helpdesk_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }    
		    //Tickets by Product
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyproduct") && (getFieldVisibilityPermission('HelpDesk',$user_id,'product_id') == "0"))
		    {
			    $graph_by="product_id";
			    $graph_title=$mod_strings['ticketsbyproduct'];
			    $module="HelpDesk";
			    $where="";
			    $query=$helpdesk_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Campaigns by Contact
		    elseif ($profileTabsPermission[getTabid("Contacts")] == 0 && ($type == "contactbycampaign") && $profileTabsPermission[getTabid("Campaigns")] == 0)
		    {
			    $graph_by="campaignname";
			    $graph_title=$mod_strings['contactbycampaign'];
			    $module="Contacts";
			    $where="";
			    $query=$campaign_query." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Account
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbyaccount") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
		    {
			    $graph_by="accountid";
			    $graph_title=$mod_strings['ticketsbyaccount'];
			    $module="HelpDesk";
			    $where="";
			    $query=$tickets_by_account." ".dashboard_check($module);
			    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
		    }
		    //Tickets by Contact
		    elseif ($profileTabsPermission[getTabid("HelpDesk")] == 0 && ($type == "ticketsbycontact") && (getFieldVisibilityPermission('HelpDesk',$user_id,'parent_id') == "0"))
			    {
				    $graph_by="contactid";
				    $graph_title=$mod_strings['ticketsbycontact'];
				    $module="HelpDesk";
				    $where="";
				    $query=$tickets_by_contact." ".dashboard_check($module);
				    echo get_graph_by_type($graph_by,$graph_title,$module,$where,$query);
				    }
		    else
                    {
                        //echo $mod_strings['LBL_NO_PERMISSION_FIELD'];
			sleep(1);
                        echo '<h3>'.$mod_strings['LBL_NO_PERMISSION_FIELD'].'</h3>';
                    }

	?>

	</table>
	<script id="dash_script">
		var gdash_display_type = '<?php echo $_REQUEST['display_view'];?>';
	</script>
