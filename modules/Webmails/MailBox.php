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

include_once('config.php');
require_once('include/logging.php');
require_once('include/database/PearDatabase.php');
require_once('include/utils/utils.php');

class MailBox {

	var $mbox;
	var $db;
	var $boxinfo;
	var $readonly='false';
	var $enabled;

	var $login_username;
	var $secretkey;
	var $imapServerAddress;
	var $ssltype;
	var $sslmeth;
	var $box_refresh;
	var $mails_per_page;
	var $mail_protocol;
	var $account_name;
	var $display_name;
	var $mailbox;
	var $mailList;

	function MailBox($mailbox = '') {
		global $current_user;
		$this->db = new PearDatabase();
		$this->db->println("Entering MailBox($mailbox)");

		$this->mailbox = $mailbox;
		$tmp = getMailServerInfo($current_user);

		if($this->db->num_rows($tmp) < 1)
			$this->enabled = 'false';
		else
			$this->enabled = 'true';

		$this->boxinfo = $this->db->fetch_array($tmp);

		$this->login_username=trim($this->boxinfo["mail_username"]); 
		$this->secretkey=trim($this->boxinfo["mail_password"]); 
		$this->imapServerAddress=gethostbyname(trim($this->boxinfo["mail_servername"])); 
		$this->mail_protocol=$this->boxinfo["mail_protocol"]; 
		$this->ssltype=$this->boxinfo["ssltype"]; 
		$this->sslmeth=$this->boxinfo["sslmeth"]; 

		$this->box_refresh=trim($this->boxinfo["box_refresh"]);
		$this->mails_per_page=trim($this->boxinfo["mails_per_page"]);
		if($this->mails_per_page < 1)
        		$this->mails_per_page=20;

		$this->account_name=$this->boxinfo["account_name"];
		$this->display_name=$this->boxinfo["display_name"];
		//$this->imapServerAddress=$this->boxinfo["mail_servername"];

		$this->db->println("Setting Mailbox Name");
		if($this->mailbox != "") 
			$this->mailbox=$mailbox;

		$this->db->println("Opening Mailbox");
		if(!$this->mbox && $this->mailbox != "")
			$this->getImapMbox();

		$this->db->println("Loading mail list");
		if($this->mbox)
			$this->mailList = $this->fullMailList();

		$this->db->println("Exiting MailBox($mailbox)");
	}

	function fullMailList() {
		$mailHeaders = @imap_headers($this->mbox);
		$numEmails = sizeof($mailHeaders);
		$mailOverviews = @imap_fetch_overview($this->mbox, "1:$numEmails", 0);
		$out = array("headers"=>$mailHeaders,"overview"=>$mailOverviews,"count"=>$numEmails);
		return $out;
	}

	function isBase64($iVal){
		$_tmp=preg_replace("/[^A-Z0-9\+\/\=]/i","",$iVal);
		return (strlen($_tmp) % 4 == 0 ) ? "y" : "n";
	}

	function getImapMbox() {
		$this->db->println("Entering getImapMbox()");
		$mods = parsePHPModules();
		$this->db->println("Parsing PHP Modules");
	 	 
		// first we will try a regular old IMAP connection: 
		if($this->ssltype == "") {$this->ssltype = "notls";} 
		if($this->sslmeth == "") {$this->sslmeth = "novalidate-cert";} 

		if($this->mail_protocol == "pop3")
			$port = "110";
		else
		{
	    		if($mods["imap"]["SSL Support"] == "enabled" && $this->ssltype == "tls")
				$port = "993";
			else
				$port = "143";
		}

		$this->db->println("Building connection string");
                if(preg_match("/@/",$this->login_username)) 
		{
                        $mailparts = split("@",$this->login_username);
                        $user="".trim($mailparts[0])."";
                        $domain="".trim($mailparts[1])."";

			// This section added to fix a bug when connecting as user@domain.com
			if($this->readonly == "true") 
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
                                	$connectString = "/".$this->ssltype."/".$this->sslmeth."/user={$user}@{$domain}/readonly";
				else
                                	$connectString = "/notls/novalidate-cert/user={$user}@{$domain}/readonly";
			}
			else
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
                                	$connectString = "/".$this->ssltype."/".$this->sslmeth."/user={$user}@{$domain}";
				else
                                	$connectString = "/notls/novalidate-cert/user={$user}@{$domain}";
			}
		}
		else
		{
			if($this->readonly == "true")
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
					$connectString = "/".$this->ssltype."/".$this->sslmeth."/readonly";
	    			else
					$connectString = "/notls/novalidate-cert/readonly";
			}
			else
			{
	    			if($mods["imap"]["SSL Support"] == "enabled")
					$connectString = "/".$this->ssltype."/".$this->sslmeth;
	    			else
					$connectString = "/notls/novalidate-cert";
			}
		}

		$connectString = "{".$this->imapServerAddress."/".$this->mail_protocol.":".$port.$connectString."}".$this->mailbox;
		//Reference - http://forums.vtiger.com/viewtopic.php?p=33478#33478 - which has no tls or validate-cert
		$connectString1 = "{".$this->imapServerAddress."/".$this->mail_protocol.":".$port."}".$this->mailbox; 

		$this->db->println("Done Building Connection String.. Connecting to box");

		if(!$this->mbox = @imap_open($connectString, $this->login_username, $this->secretkey))
		{
			//try second string which has no tls or validate-cert
			if(!$this->mbox = @imap_open($connectString1, $this->login_username, $this->secretkey))
			{
				global $current_user;
				$this->db->println("CONNECTION ERROR - Could not be connected to the server using imap_open function through the connection strings $connectString and $connectString1");
				echo "<br>&nbsp;<b>Could not connect to the server. Please check the server details <a href='index.php?module=Settings&action=AddMailAccount&record=".$current_user->id."'> Here </a></b>";
				exit;
			}
		}

		$this->db->println("Done connecting to box");
	}
} // END CLASS


function parsePHPModules() {
 ob_start();
 phpinfo(INFO_MODULES);
 $s = ob_get_contents();
 ob_end_clean();

 $s = strip_tags($s,'<h2><th><td>');
 $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
 $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
 $vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
 $vModules = array();
 for ($i=1;$i<count($vTmp);$i++) {
  if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
   $vName = trim($vMat[1]);
   $vTmp2 = explode("\n",$vTmp[$i+1]);
   foreach ($vTmp2 AS $vOne) {
   $vPat = '<info>([^<]+)<\/info>';
   $vPat3 = "/$vPat\s*$vPat\s*$vPat/";
   $vPat2 = "/$vPat\s*$vPat/";
   if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
     $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
   } elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols
     $vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
   }
   }
  }
 }
 return $vModules;
}
?>
