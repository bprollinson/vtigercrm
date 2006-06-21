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
global $calpath;
global $app_strings,$mod_strings;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

require_once('data/CRMEntity.php');
require_once('include/database/PearDatabase.php');
//require_once($theme_path."layout_utils.php");
// Require the xmlParser class
//require_once('include/feedParser/xmlParser.php');

// Require the feedParser class
//require_once('include/feedParser/feedParser.php');

require_once('include/magpierss/rss_fetch.inc');

class vtigerRSS extends CRMEntity
{
	var $rsscache_time = 1200;
	
	/** Function to get the Rss Feeds from the Given URL 
	  * This Function accepts the url string as the argument 
	  * and assign the value for the class variables correspondingly
	 */
	function setRSSUrl($url)
	{
		global $cache_dir;
		$this->rss = fetch_rss($url);
		// Read in our sample feed file
		//$data = @implode("",@file($url));
		// Tell feedParser to parse the data
		$info = $this->rss->items;

		if(isset($info))
		{
			$this->rss_object = $info;
		}else
		{
			return false;
		}	
		if(isset($this->rss))
		{
			$this->rss_title = $this->rss->channel["title"];
			$this->rss_link = $this->rss->channel["link"];
			$this->rss_object = $info;
			return true;
		}else
		{
			return false;
		}

	}
	
	/** Function to get the List of Rss feeds  
	  * This Function accepts no arguments and returns the listview contents on Sucess
	  * returns "Sorry: It's not possible to reach RSS URL" if fails
	 */

	function getListViewRSSHtml()
	{
		if(isset($this->rss_object))
		{
			$i = 0;
			foreach($this->rss_object as $key=>$item)
			{
				$i = $i + 1;	   
				$shtml .= "<tr class='prvPrfHoverOff' onmouseover=\"this.className='prvPrfHoverOn'\" onmouseout=\"this.className='prvPrfHoverOff'\"><td><a href=\"javascript:display('".$item[link]."','feedlist_".$i."')\"; id='feedlist_".$i."' class=\"rssNews\">".$item[title]."</a></td><td>".$this->rss_title."</td></tr>";
				if($i == 10)
				{
					return $shtml;
				}
			}

			return $shtml;

		}else
		{
			$shtml = "Sorry: It's not possible to reach RSS URL";
		}
	}

	/** Function to save the Rss Feeds  
	  * This Function accepts the RssURl,Starred Status as arguments and 
	  * returns true on sucess 
	  * returns false if fails
	 */
	function saveRSSUrl($url,$makestarred=0)
	{
		global $adb;

		if ($url != "")
		{
			$rsstitle = $this->rss_title;
			if($rsstitle == "")
			{
				$rsstitle = $url;
			}
			$genRssId = $adb->getUniqueID("rss");
			$sSQL = "insert into vtiger_rss (RSSID,RSSURL,RSSTITLE,RSSTYPE,STARRED) values (".$genRssId.",'".addslashes($url);
			$sSQL .= "','".addslashes($rsstitle)."',0,".$makestarred.")";
			$result = $adb->query($sSQL);
			if($result)
			{
				return $genRssId;
			}else
			{
				return false;
			}
		}
	}
	function getCRMRssFeeds()
	{
		global $adb;
		global $image_path;

		$sSQL = "select * from vtiger_rss where rsstype=1";
		$result = $adb->query($sSQL);
		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<tr>";
			if($allrssrow["starred"] == 1)
			{
				$shtml .= "<td width=\"15\">
					<img src=\"".$image_path."onstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\"></td>";
			}else
			{
				$shtml .= "<td width=\"15\">
					<img src=\"".$image_path."offstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\" onClick=\"makedefaultRss($allrssrow[rssid])\"></td>";
			}
			$shtml .= "<td class=\"rssTitle\"><a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]
				\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td>";
			$shtml .= "<td>&nbsp;</td></tr>";

		}
		return $shtml;

	}

	function getAllRssFeeds()
	{
		global $adb;
		global $image_path;

		$sSQL = "select * from vtiger_rss where rsstype <> 1";
		$result = $adb->query($sSQL);
		while($allrssrow = $adb->fetch_array($result))	
		{
			$shtml .= "<tr>";
			if($allrssrow["starred"] == 1)
			{
				$shtml .= "<td width=\"15\">
					<img src=\"".$image_path."onstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\"></td>";
			}else
			{		
				$shtml .= "<td width=\"15\">
					<img src=\"".$image_path."offstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\" onClick=\"makedefaultRss($allrssrow[rssid])\"></td>";
			}
			$shtml .= "<td class=\"rssTitle\"><a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td><td>&nbsp;</td>";
			$shtml .= "</tr>";

		}

		return $shtml;
	}

	/** Function to get the vtiger_rssurl for the given id  
	  * This Function accepts the vtiger_rssid as argument and returns the vtiger_rssurl for that id
	 */
	function getRssUrlfromId($rssid)
	{
		global $adb;

		if($rssid != "")
		{
			$sSQL = "select * from vtiger_rss where rssid=".$rssid;
			$result = $adb->query($sSQL);
			$rssrow = $adb->fetch_array($result);

			if(count($rssrow) > 0)
			{
				$rssurl = $rssrow[rssurl];
			}
		}
		return $rssurl;
	}

	function getRSSHeadings($rssid)
	{
		global $image_path;
		global $adb;

		if($rssid != "")
		{
			$sSQL = "select * from vtiger_rss where rssid=".$rssid;
			$result = $adb->query($sSQL);
			$rssrow = $adb->fetch_array($result);

			if(count($rssrow) > 0)
			{
				$shtml = "<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"4\">
					<tr>
					<td class=\"rssPgTitle\">";
				if($rssrow[starred] == 1)
				{
					$shtml .= "<img src=\"".$image_path."starred.gif\" align=\"absmiddle\">";
				}else
				{
					$shtml .= "<img src=\"".$image_path."unstarred.gif\" align=\"absmiddle\">";
				}		
				$shtml .= "<a href=\"".$this->rss_object[link]."\">  ".$rssrow[rsstitle]."</a>
					</td>
					</tr>
					</table>";

			}
		}
		return $shtml;
	}

	function getTopStarredRSSFeeds()
	{
		global $adb;
		global $image_path;

		$sSQL = "select * from vtiger_rss where starred=1";
		$result = $adb->query($sSQL);
		$shtml .= "<img src=\"".$image_path."rss.gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\"><a href=\"#\" onclick='window.open(\"index.php?module=Rss&action=Popup\",\"new\",\"width=500,height=300,resizable=1,scrollbars=1\");'>Add New Rss</a>";

		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<img src=\"".$image_path."rss.gif\" border=\"0\" align=\"absmiddle\" hspace=\"2\">"; 
			$shtml .= "<a href=\"index.php?module=Rss&action=ListView&record=$allrssrow[rssid]\" class=\"rssFavLink\">				 ".substr($allrssrow['rsstitle'],0,10)."...</a></img>";
		}
		return $shtml;
	}
	
	/** Function to get the vtiger_rssfeed lists for the starred Rss feeds  
	  * This Function accepts no argument and returns the vtiger_rss feeds of 
	  * the starred Feeds as HTML strings
	 */
	function getStarredRssHTML()
	{
		global $adb;
		global $image_path,$mod_strings;

		$sSQL = "select * from vtiger_rss where starred=1";
		$result = $adb->query($sSQL);
		while($allrssrow = $adb->fetch_array($result))
		{
			if($this->setRSSUrl($allrssrow["rssurl"]))
			{
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if(isset($this->rss_object))
			{
				if(count($this->rss_object) > 10)
				{
					$shtml .= "<tr><td colspan='3' align=\"right\">
						<a target=\"_BLANK\" href=\"$this->rss_link\">".$mod_strings['LBL_MORE']."</a>
						</td></tr>";
				}
			}
			$sreturnhtml[] = $shtml;
			$shtml = "";
		}

		$recordcount = round((count($sreturnhtml))/2);
		$j = $recordcount;
		for($i=0;$i<$recordcount;$i++)
		{
			$starredhtml .= $sreturnhtml[$i].$sreturnhtml[$j];
			$j = $j + 1;
		}
		$starredhtml  = "<table class='rssTable' cellspacing='0' cellpadding='0'>
						<tr>
       	                <th width='75%'>".$mod_strings['LBL_SUBJECT']."</th>
           	            <th width='25%'>".$mod_strings['LBL_SENDER']."</th>
                   	    </tr>".$starredhtml."</table>";

		return $starredhtml;

	}

	/** Function to get the vtiger_rssfeed lists for the given vtiger_rssid  
	  * This Function accepts the vtiger_rssid as argument and returns the vtiger_rss feeds as HTML strings
	 */
	function getSelectedRssHTML($rssid)
	{
		global $adb;
		global $image_path, $mod_strings;

		$sSQL = "select * from vtiger_rss where rssid=".$rssid;
		$result = $adb->query($sSQL);
		while($allrssrow = $adb->fetch_array($result))
		{
			if($this->setRSSUrl($allrssrow["rssurl"]))
			{
				$rss_html = $this->getListViewRSSHtml();
			}
			$shtml .= $rss_html;
			if(isset($this->rss_object))
			{
				if(count($this->rss_object) > 10)
				{
					$shtml .= "<tr><td colspan='3' align=\"right\">
							<a target=\"_BLANK\" href=\"$this->rss_link\">".$mod_strings['LBL_MORE']."</a>
							</td></tr>";
				}
			}
			$sreturnhtml[] = $shtml;
			$shtml = "";
		}

		$recordcount = round((count($sreturnhtml))/2);
		$j = $recordcount;
		for($i=0;$i<$recordcount;$i++)
		{
			$starredhtml .= $sreturnhtml[$i].$sreturnhtml[$j];
			$j = $j + 1;
		}
		$starredhtml  = "<table class='rssTable' cellspacing='0' cellpadding='0'>
						<tr>
       	                <th width='75%'>".$mod_strings['LBL_SUBJECT']."</th>
           	            <th width='25%'>".$mod_strings['LBL_SENDER']."</th>
                   	    </tr>".$starredhtml."</table>";

		return $starredhtml;

	}

	/** Function to get the Rss Feeds by Category 
	  * This Function accepts the RssCategory as argument 
	  * and returns the html string for the Rss feeds lists
	 */
	function getRSSCategoryHTML()
	{
		global $adb;
		global $image_path;
			$shtml .= "<tr>
						<td colspan=\"3\">
						<table width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"2\" style=\"margin:5 0 0 35\">".$this->getRssFeedsbyCategory()."</table>
						</td>
					  </tr>";

		return $shtml;
	}

	/** Function to get the Rss Feeds for the Given Category 
	  * This Function accepts the RssCategory as argument 
	  * and returns the html string for the Rss feeds lists
	 */
	function getRssFeedsbyCategory()
	{

		global $adb;
		global $image_path;

		$sSQL = "select * from vtiger_rss";
		$result = $adb->query($sSQL);
		while($allrssrow = $adb->fetch_array($result))
		{
			$shtml .= "<tr id='feed_".$allrssrow[rssid]."'>";
			$shtml .= "<td width=\"15\">";
			if($allrssrow["starred"] == 1)
			{
				 	   $shtml .= "<img src=\"".$image_path."onstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\">";
			}else
			{
				 	   $shtml .= "<img src=\"".$image_path."offstar.gif\" align=\"absmiddle\" onMouseOver=\"this.style.cursor='pointer'\" id=\"star-$allrssrow[rssid]\"  onClick=\"makedefaultRss($allrssrow[rssid])\">";
			}
					   $shtml .= "</td>";
			$shtml .= "<td class=\"rssTitle\" width=\"10%\" nowrap><a href=\"javascript:GetRssFeedList('$allrssrow[rssid]')\" class=\"rssTitle\">".$allrssrow[rsstitle]."</a></td><td>&nbsp;</td>";
			$shtml .= "</tr>";
		}
		return $shtml;

	}

	/** Function to get the vtiger_rsscategories   
	* This Function accepts no argument and returns the categorylist as an array
	*/

	function getRsscategory()
	{
		global $adb;
		global $image_path;
		$sSQL = "select * from vtiger_rsscategory where presence = 1 order by sortorderid";
		$result = $adb->query($sSQL);

		while($categoryrow = $adb->fetch_array($result))
		{
			$rsscategories[] = $categoryrow["rsscategory"];
		}

		return $rsscategories;
	}
	
	function getRsscategory_html()
	{
		$rsscategory = $this->getRsscategory();
		if(isset($rsscategory)) 
		{
			for($i=0;$i<count($rsscategory);$i++)
			{
				$shtml .= "<option value=\"$rsscategory[$i]\">$rsscategory[$i]</option>";
			}
		}
		return $shtml;
	}
}

/** Function to get the vtiger_rsstitle for the given vtiger_rssid  
 * This Function accepts the vtiger_rssid as an optional argument and returns the title
 * if no id is passed it will return the tittle of the starred vtiger_rss
 */
function gerRssTitle($id='')
{
	global $adb;
	if($id == '')
		$query = 'select * from vtiger_rss where starred=1';	 
	else		
		$query = 'select * from vtiger_rss where rssid ='.$id;	 
	$result = $adb->query($query);	
	$title = $adb->query_result($result,0,'rsstitle');
	return $title;
	
}



?>
