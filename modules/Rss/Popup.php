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
require_once("data/Tracker.php");
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/logging.php');
require_once('include/utils/utils.php');
require_once('modules/Rss/Rss.php');

$log = LoggerManager::getLogger('rss_save');

if(isset($_REQUEST["record"]))
{
	global $adb;
	$query = 'update rss set starred=0';
	$adb->query($query);
	$query = 'update rss set starred=1 where rssid ='.$_REQUEST["record"]; 
	$adb->query($query);
	echo $_REQUEST["record"];
}
elseif(isset($_REQUEST["rssurl"]))
{
	$newRssUrl = $_REQUEST["rssurl"];
	$rsscategory = $_REQUEST["rsscategory"];
	
	$setstarred = 0;
	$oRss = new vtigerRSS();
	if($oRss->setRSSUrl($newRssUrl))
	{
			$result = $oRss->saveRSSUrl($newRssUrl,$setstarred,$rsscategory);
        	if($result == false)
        	{
				echo "Unable to save the RSS Feed URL" ;
        	}else
        	{
				echo $result;
        	}
	}else
	{
		echo "Not a valid RSS Feed URL" ;

	}
}

?>
