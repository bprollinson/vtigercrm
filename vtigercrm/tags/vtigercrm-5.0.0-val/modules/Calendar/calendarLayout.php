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

require_once('include/database/PearDatabase.php');
require_once('include/utils/CommonUtils.php');
require_once('include/utils/UserInfoUtil.php');
require_once('modules/Calendar/CalendarCommon.php');


/**
 *  Function creates HTML to display Events and  Todos div tags
 *  @param array    $param_arr      - collection of objects and strings
 *  @param string   $viewBox        - string 'listview' or 'hourview' or may be empty. if 'listview' means get Events ListView.if 'hourview' means gets Events HourView. if empty means get Todos ListView
 *  @param string   $subtab         - string 'todo' or 'event'. if 'todo' means Todos View else Events View
 */
function calendar_layout(& $param_arr,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering calendar_layout() method");
	$cal_header = array ();
	if (isset($param_arr['size']) && $param_arr['size'] == 'small')
		$param_arr['calendar']->show_events = false;

	$cal_header['view'] = $param_arr['view'];
	$cal_header['IMAGE_PATH'] = $param_arr['IMAGE_PATH'];
        $cal_header['calendar'] = $param_arr['calendar'];
	$eventlabel = $mod_strings['LBL_EVENTS'];
	$todolabel = $mod_strings['LBL_TODOS'];
	//if $param_arr['size'] is set to 'small', get small(mini) calendar
	if(isset($param_arr['size']) && $param_arr['size'] == 'small')
	{
		get_mini_calendar($param_arr);
	}
	else
	{
		//To differentiate selected subtab from unselected one - Starts
		if($subtab == 'event')
		{
			$eventtab_class = 'dvtSelectedCell';
			$todotab_class = 'dvtUnSelectedCell';
		        $event_anchor = $eventlabel;
			$todo_anchor = "<a href='index.php?module=Calendar&action=index&view=".$cal_header['view']."".$cal_header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=todo&parenttab=".$category."'>".$todolabel."</a>";
					
		}
		elseif($subtab == 'todo')
		{
			$eventtab_class = 'dvtUnSelectedCell';
			$todotab_class = 'dvtSelectedCell';
			$event_anchor = "<a href='index.php?module=Calendar&action=index&view=".$cal_header['view']."".$cal_header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=event&parenttab=".$category."'>".$eventlabel."</a>";
			$todo_anchor = $todolabel;
		}
		//Ends
		//To get calendar header and its links(like Day,Week,Month,Year and etc.)
		get_cal_header_tab($cal_header,$viewBox,$subtab);
		$subheader = "";
		$subheader .=<<<EOQ
			<tr>
				<td colspan="8" class="calBorder">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td>
							
								<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td class="$eventtab_class" id="pi" align="center" nowrap="nowrap" width="75">$event_anchor</td>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td class="$todotab_class" style="width:100px;" id="mi" align="center" nowrap="nowrap">$todo_anchor</td>
										<td class="dvtTabCache" nowrap="nowrap">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="calInnerBorder" align="left" bgcolor="#ffffff" valign="top">
EOQ;
		echo $subheader;
		if($viewBox == 'hourview' && $subtab == 'event')
		{
			get_cal_header_data($param_arr,$viewBox,$subtab);
			getHourView($param_arr);
		}
		elseif($viewBox == 'listview' && $subtab == 'event')
		{
			get_cal_header_data($param_arr,$viewBox,$subtab);
			getEventListView($param_arr);
		}
		elseif($subtab == 'todo')
		{
			$todo_list = "";
			$todo_list .= getTodosListView($param_arr,'',$subtab);
			$todo_list .= '</td></tr></table></td></tr></table><br>';
			echo $todo_list;
		}
	}
	
	$cal_log->debug("Exiting calendar_layout() method");	
	
}

/**
 * Function creates HTML to display small(mini) Calendar 
 * @param array   $cal    - collection of objects and strings
 */
function get_mini_calendar(& $cal)
{
	global $current_user,$adb,$cal_log;
	$category = getParentTab();
	$cal_log->debug('Entering get_mini_calendar() method...');
	$count = 0;
	//To decide number of rows(weeks) in a month
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
                $rows = 5;
        } else {
                $rows = 6;
        }
	$minical = "";
	$minical .= "<table class='mailClient ' bgcolor='white' border='0' cellpadding='2' cellspacing='0' width='98%'>
                        <tr>
				<td class='calHdr'>&nbsp;</td>
				<td style='padding:5px' colspan='6' class='calHdr' align='center'>".get_previous_cal($cal)."&nbsp;";
				$minical .= "<a style='text-decoration: none;' href='index.php?module=Calendar&action=index&view=".$cal['view']."".$cal['calendar']->date_time->get_date_str()."&parenttab=".$category."'><b>".display_date($cal['view'],$cal['calendar']->date_time)."</b></a>&nbsp;".get_next_cal($cal)."</td>";
				$minical .= "<td class='calHdr' align='right'><a href='javascript:ghide(\"miniCal\");'><img src='".$cal['IMAGE_PATH']."close.gif' align='right' border='0'></a>
	                     </td></tr>";
	$minical .= "<tr class='hdrNameBg'>";
	//To display days in week 
	$minical .= '<th width="12%">Week</th>';
	for ($i = 0; $i < 7; $i ++)
        {
                $weekdays_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
                $weekday = $weekdays_row->start_time->getdayofWeek_inshort();
                $minical .= '<th width="12%">'.$weekday.'</th>';
        }
	$minical .= "</tr>";	
	$event_class = '';
	$class = '';
	for ($i = 0; $i < $rows; $i ++)
        {
                $minical .= "<tr>";
                for ($j = 0; $j < 7; $j ++)
                {
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$class = dateCheck($cal['slice']->start_time->get_formatted_date());
			if($count%7 == 0)
				$minical .= "<td style='text-align:center' ><a href='index.php?module=Calendar&action=index&view=week".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>".$cal['slice']->start_time->week."</td>";
			//To differentiate day having events from other days
			if(count($cal['slice']->activities) != 0 && ($cal['slice']->start_time->get_formatted_date() == $cal['slice']->activities[0]->start_time->get_formatted_date()))
			{
					$event_class = 'class="eventDay"';
			}
			else
			{
                       		$event_class = '';
                        }
			//To differentiate current day from other days
			if($class != '' )
				$class = 'class="'.$class.'"';
			else
				$class = $event_class;
			
			//To display month dates
                        if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth())
                        {
				$minical .= "<td ".$class." style='text-align:center' >";
				$minical .= "<a href='index.php?module=Calendar&action=index&view=".$cal['slice']->getView()."".$cal['slice']->start_time->get_date_str()."&parenttab=".$category."'>";
				$minical .= $cal['slice']->start_time->get_Date()."</a></td>";
                        }
			else
			{
				$minical .= "<td style='text-align:center' ></td>";
			}
                        $count++;
                }
                $minical .= '</tr>';
	}
	
        $minical .= "</table>";
	echo $minical;
	$cal_log->debug("Exiting get_mini_calendar() method...");
}

/**
 * Function creates HTML to display Calendar Header and its Links
 * @param array    $header   - collection of objects and strings
 * @param string   $viewBox  - string 'listview' or 'hourview' or may be empty. if 'listview' means Events ListView.if 'hourview' means Events HourView. if empty means get Todos ListView
 * @param string   $subtab   - string 'todo' or 'event'. if 'todo' means Todos View else Events View
 */
function get_cal_header_tab(& $header,$viewBox,$subtab)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering get_cal_header_tab() method...");
	$tabhtml = "";
	$count = 1;
	include_once 'modules/Calendar/addEventUI.php';
	include_once 'modules/Calendar/header.php';
	$eventlabel = $mod_strings['LBL_EVENTS'];
	$todolabel = $mod_strings['LBL_TODOS'];
	$div = "<div id='miniCal' style='width:300px; position:absolute; display:none; left:100px; top:100px; z-index:100000; background-color:white'></div>
		<div id='calSettings' class='layerPopup calSettings' style='display:none;width:500px;' align=center ></div>
		<div id='dataArray'></div>
		";
	echo $div;
	$tabhtml .= "<table class='small calHdr' align='center' border='0' cellpadding='5' cellspacing='0' width='100%'><tr>";
        $links = array ('day','week','month','year');
	//To differentiate the selected link from unselected links
	foreach ($links as $link)
	{
		if ($header['view'] == $link)
		{
			$class = 'calSel';
			$anchor = $mod_strings["LBL_".$header['calendar']->getCalendarView($link)];
		}
		else
		{
			$class = 'calUnSel';
			$anchor = "<a href='index.php?module=Calendar&action=index&view=".$link."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."'>".$mod_strings["LBL_".$header['calendar']->getCalendarView($link)]."</a>";
		}
	
		if($count == 1)
			$tabhtml .= "<!-- day week month buttons --> <td style='border-left: 1px solid #666666;' class=".$class.">".$anchor."</td>";
		else
			$tabhtml .= "<td class=".$class.">".$anchor."</td>";
		$count++;
	}
	//To get Navigation(next&previous) links and display Date info
	$tabhtml .= "<td width='30%'>
			<table border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td>".get_previous_cal($header,$viewBox,$subtab)."
				</td>";
	$tabhtml .= "<td class='calendarNav'>".display_date($header['view'],$header['calendar']->date_time)."</td>";
	$tabhtml .= "<td>".get_next_cal($header,$viewBox,$subtab)."
		     </td></tr>
		    </table>
		</td>";
		$tabhtml .= "<td width='2%'><img onClick='fnvshobj(this,\"miniCal\"); getMiniCal(\"view=".$header['calendar']->view."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."\");' src='".$header['IMAGE_PATH']."btnL3Calendar.gif' alt='".$mod_strings['LBL_OPENCAL']."...' title='".$mod_strings['LBL_OPENCAL']."...' align='absmiddle' border='0'></td>";
		$tabhtml .= "<td width=20% ><img onClick='fnvshobj(this,\"calSettings\"); getCalSettings(\"view=".$header['calendar']->view."".$header['calendar']->date_time->get_date_str()."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."\");' src='".$header['IMAGE_PATH']."tbarSettings.gif' alt='".$mod_strings['LBL_SETTINGS']."' title='".$mod_strings['LBL_SETTINGS']."' align='absmiddle' border='0'></td>";
	$tabhtml .= "<td class='calHdr calTopRight componentName'>".$app_strings[Calendar]."</td>";	
	$tabhtml .= "</tr>";
	echo $tabhtml;
	$cal_log->debug("Exiting get_cal_header_tab() method...");
}

/**
 * Function creates HTML to display number of Events, Todos and pending list in calendar under header(Eg:Total Events : 5, 2 Pending / Total To Dos: 4, 1 Pending)
 * @param array  $cal_arr   - collection of objects and strings
 * @param string $viewBox   - string 'listview' or 'hourview'. if 'listview' means Events ListView.if 'hourview' means Events HourView.
 */
function get_cal_header_data(& $cal_arr,$viewBox,$subtab)
{
	global $mod_strings,$cal_log;
	$cal_log->debug("Entering get_cal_header_data() method...");
	global $current_user,$app_strings;
        $date_format = $current_user->date_format;
	$format = $cal_arr['calendar']->hour_format;
	$hour_startat = convertTime2UserSelectedFmt($format,$cal_arr['calendar']->day_start_hour,false); 
	$hour_endat = convertTime2UserSelectedFmt($format,($cal_arr['calendar']->day_start_hour+1),false);
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$temp_ts = $cal_arr['calendar']->date_time->ts;
	//To get date in user selected format
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	$headerdata = "";
	$headerdata .="
			<div style='display: block;' id='mnuTab'>
			<form name='EventViewOption' method='POST' action='index.php'>
			<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>";
			if(isPermitted("Calendar","EditView") == "yes")
			{
			$headerdata .="<tr>
				<td>
				<table><tr><td class='calAddButton' style='cursor:pointer;height:30px' align='center' width='15%' onMouseOver='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"".$viewBox."\",\"".$subtab."\");'>
					".$mod_strings['LBL_ADD']."
					<img src='".$cal_arr['IMAGE_PATH']."menuDnArrow.gif' style='padding-left: 5px;' border='0'>
				</td></tr></table> </td>";
			}
			else
			{
				$headerdata .="<tr><td>&nbsp;</td>";
			}
			$headerdata .="<td align='center' width='53%'>";
	$headerdata .= getEventTodoInfo($cal_arr,'listcnt'); 
	$headerdata .= "	</td>
				<td align='center' width='30%'><table border=0 cellspacing=0 cellpadding=2><tr><td class=small><b>".$mod_strings['LBL_VIEW']." : </b></td><td>";
	$view_options = getEventViewOption($cal_arr,$viewBox);
	$headerdata .=$view_options."</td></tr></form></table>
				</td>
			</tr>
		</table>";
	echo $headerdata;	
	$cal_log->debug("Exiting get_cal_header_data() method...");
}

/**
 * Function creates HTML select statement to display View selection box
 * @param array  $cal     - collection of objects and strings 
 * @param string $viewBox - string 'listview' or 'hourview'. if 'listview' means get Events ListView.if 'hourview' means get Events HourView.
 * return string $view   - html selection box
 */
function getEventViewOption(& $cal,$viewBox)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	if($viewBox == 'listview')
	{
		$list_sel = 'selected';
		$hr_sel = '';
	}
	else
	{
		$list_sel = '';
		$hr_sel = 'selected';
	}
	$cal_log->debug("Entering getEventViewOption() method...");
	$view = "<input type='hidden' name='view' value='".$cal['calendar']->view."'>
			<input type='hidden' name='hour' value='".$cal['calendar']->date_time->hour."'>
			<input type='hidden' name='day' value='".$cal['calendar']->date_time->day."'>
			<input type='hidden' name='week' value='".$cal['calendar']->date_time->week."'>
			<input type='hidden' name='month' value='".$cal['calendar']->date_time->month."'>
			<input type='hidden' name='year' value='".$cal['calendar']->date_time->year."'>
			<input type='hidden' name='parenttab' value='".$category."'>
			<input type='hidden' name='module' value='Calendar'>
			<input type='hidden' name='return_module' value='Calendar'>
			<input type='hidden' name='action' value=''>
			<input type='hidden' name='return_action' value=''>
							 
		        <select name='viewOption' class='importBox' id='view_Option' onChange='fnRedirect();'>";
	if($cal['view'] == 'day')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_HRVIEW']."</option>";	
	}
	elseif($cal['view'] == 'week')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_WEEKVIEW']."</option>";
	}
	elseif($cal['view'] == 'month')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_MONTHVIEW']."</option>";
	}
	elseif($cal['view'] == 'year')
	{
		$view .="<option value='listview' ".$list_sel.">".$mod_strings['LBL_LISTVIEW']."</option>
			 <option value='hourview' ".$hr_sel.">".$mod_strings['LBL_YEARVIEW']."</option>";
	}
	else
		die("view is not defined");
	$view .="</select>
		</form>";
	$cal_log->debug("Exiting getEventViewOption() method...");
	return $view;
}

/**
 * Function creates HTML anchor tag to get previous-day/week/month/year view
 * @param array  $cal        - collection of objects and strings
 * @param string $viewBox    - string 'listview' or 'hourview' or may be empty. if 'listview' means previous link in Events ListView.if 'hourview' means previous link in Events HourView. if empty means previous link in Todos ListView
 * @param string   $subtab   - string 'todo' or 'event' or may be empty. if 'todo' means Todos View. if 'event' means Events View. if empty means small calendar view. 
 * return string $link       - html tags in string format
 */
function get_previous_cal(& $cal,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering get_previous_cal() method...");
	if(isset($cal['size']) && $cal['size'] == 'small')
        {
		$link = "<a href='javascript:getMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."&parenttab=".$category."\")'><img src='".$cal['IMAGE_PATH']."small_left.gif' border='0' align='absmiddle' /></a>";
	}
	else
	{
		$link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."'><img src='".$cal['IMAGE_PATH']."cal_prev_nav.gif' border='0' align='absmiddle' /></a>";
	}
	$cal_log->debug("Exiting get_previous_cal() method...");
	return $link;
}

/**
 * Function creates HTML anchor tag to get next-day/week/month/year view
 * @param array  $cal        - collection of objects and strings
 * @param string $viewBox    - string 'listview' or 'hourview' or may be empty. if 'listview' means next link in Events ListView.if 'hourview' means next link in Events HourView. if empty means next link in Todos ListView
 * @param string $subtab     - string 'todo' or 'event' or may be empty. if 'todo' means Todos View. if 'event' means Events View. if empty means small calendar view. 
 * return string $link       - html tags in string format
 */
function get_next_cal(& $cal,$viewBox='',$subtab='')
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering get_next_cal() method...");
	if(isset($cal['size']) && $cal['size'] == 'small')
	{
		$link = "<a href='javascript:getMiniCal(\"view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."&parenttab=".$category."\")' ><img src='".$cal['IMAGE_PATH']."small_right.gif' border='0' align='absmiddle' /></a>";
	}
	else
	{
		$link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."&viewOption=".$viewBox."&subtab=".$subtab."&parenttab=".$category."'><img src='".$cal['IMAGE_PATH']."cal_next_nav.gif' border='0' align='absmiddle' /></a>";
	}
	$cal_log->debug("Exiting get_next_cal() method...");
	return $link;

}

/**
 * Function to get date info depending upon on the calendar view(Eg: 21 July 2000)
 * @param string  $view        - calendar view(day/week/month/year)
 * @param array   $date_time   - contains DateTime object
 * return string  $label       - date info(Eg for dayview : 13 July 2000)
 */
function display_date($view,$date_time)
{
	global $cal_log;
	$cal_log->debug("Entering display_date() method...");
	if ($view == 'day')
        {
		//$label = $date_time->getdayofWeek()." ";
		$label = $date_time->get_Date()." ";
		$label .= $date_time->getmonthName()." ";
		$label .= $date_time->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }
	elseif ($view == 'week')
        {
                $week_start = $date_time->getThisweekDaysbyIndex(0);
                $week_end = $date_time->getThisweekDaysbyIndex(6);
                $label = $week_start->get_Date()." ";
                $label .= $week_start->getmonthName()." ";
                $label .= $week_start->year;
                $label .= " - ";
                $label .= $week_end->get_Date()." ";
                $label .= $week_end->getmonthName()." ";
                $label .= $week_end->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }

	elseif ($view == 'month')
	{
		$label = $date_time->getmonthName()." ";
		$label .= $date_time->year;
		$cal_log->debug("Exiting display_date() method...");
		return $label;
        }
	elseif ($view == 'year')
	{
		$cal_log->debug("Exiting display_date() method...");
		return $date_time->year;
        }

}
/**
 *  Function to get css class name for date
 *  @param   string  $slice_date    - date
 *  returns  string                 - css class name or empty string 
 */
function dateCheck($slice_date)
{
	global $cal_log;
	$cal_log->debug("Entering dateCheck() method...");
	$today = date('Y-m-d');
	if($today == $slice_date)
	{
		$cal_log->debug("Exiting dateCheck() method...");
		//css class for day having event(s)
		return 'currDay';
	}
	else
	{
		$cal_log->debug("Exiting dateCheck() method...");
		return '';
	}
}

/**
 * Function to construct respective calendar layout depends on the calendar view
 * @param  array     $view      -  collection of objects and strings
 */
function getHourView(& $view)
{
	global $cal_log;
	$hourview_layout = '';
	$cal_log->debug("Entering getHourView() method...");
	$hourview_layout .= '<br /><!-- HOUR VIEW LAYER STARTS HERE -->
		<div id="hrView" align=center>';
		
	if($view['view'] == 'day')
		$hourview_layout .= getDayViewLayout($view);
	elseif($view['view'] == 'week')
		$hourview_layout .= getWeekViewLayout($view);
	elseif($view['view'] == 'month')
		 $hourview_layout .= getMonthViewLayout($view);
	elseif($view['view'] == 'year')
		 $hourview_layout .= getYearViewLayout($view);
	else
		die("view:".$view['view']." is not defined");
		
	$hourview_layout .= '<br></div>
		</div>';
	$hourview_layout .= '<br></td></tr></table></td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</div>
		</td>
	        <td valign=top><img src="'.$view['IMAGE_PATH'].'showPanelTopRight.gif"></td>
		</tr>
		   </table>
	<br>';
	echo $hourview_layout;
	$cal_log->debug("Exiting getHourView() method...");
}

/**
 * Fuction constructs Events ListView depends on the view
 * @param   array  $cal            - collection of objects and strings
 * @param   string $mode           - string 'listcnt' or empty. if empty means get Events ListView else get total no. of events and no. of pending events Info.
 * returns  string $activity_list  - total no. of events and no. of pending events Info(Eg: Total Events : 2, 1 Pending).
 */
function getEventListView(& $cal,$mode='')
{
	global $cal_log;
	$list_view = "";
        $cal_log->debug("Entering getEventListView() method...");
	if($cal['calendar']->view == 'day')
	{
		$start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
	}
	elseif($cal['calendar']->view == 'week')
	{
		$start_date = $cal['calendar']->slices[0];
		$end_date = $cal['calendar']->slices[6];
	}
	elseif($cal['calendar']->view == 'month')
        {
		$start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
		$end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'year')
        {
		$start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
		$end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
	}
	else
        {
		die("view:".$cal['calendar']->view." is not defined");
        }
	//if $mode value is empty means get Events list in array format else get the count of total events and pending events in array format.
	$activity_list = getEventList($cal, $start_date, $end_date,$mode);
	if($mode != '')
	{
		$cal_log->debug("Exiting getEventListView() method...");
		return $activity_list;
	}
	//To get Events listView
	$list_view .="<br><div id='listView'>";
	$list_view .=constructEventListView($cal,$activity_list);
	$list_view .="<br></div>
		</div>";
	$list_view .="<br></td></tr></table></td></tr></table>
			</td></tr></table>
		</td></tr></table>
		</div>
		</td></tr></table>
		</td>
		<td valign=top><img src='".$cal['IMAGE_PATH']."showPanelTopRight.gif'></td>
		</tr>
	</table>
	<br>";
	echo $list_view;
	$cal_log->debug("Exiting getEventListView() method...");
}


/**
 * Fuction constructs Todos ListView depends on the view
 * @param   array  $cal            - collection of objects and strings
 * @param   string $check          - string 'listcnt' or empty. if empty means get Todos ListView else get total no. of Todos and no. of pending todos Info.
 * returns  string $todo_list      - total no. of todos and no. of pending todos Info(Eg: Total Todos : 2, 1 Pending).
 */
function getTodosListView($cal, $check='',$subtab='')
{
	global $cal_log;
	$list_view = "";
        $cal_log->debug("Entering getTodosListView() method...");
	if($cal['calendar']->view == 'day')
        {
                $start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'week')
        {
                $start_date = $cal['calendar']->slices[0];
                $end_date = $cal['calendar']->slices[6];
        }
        elseif($cal['calendar']->view == 'month')
        {
                $start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
                $end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
	elseif($cal['calendar']->view == 'year')
        {
                $start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
                $end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$start_date = $start_date->get_formatted_date();
		$end_date = $end_date->get_formatted_date();
        }
        else
        {
                die("view:".$cal['calendar']->view." is not defined");
        }
	//if $check value is empty means get Todos list in array format else get the count of total todos and pending todos in array format.
	$todo_list = getTodoList($cal, $start_date, $end_date,$check);
	if($check != '')
	{
		$cal_log->debug("Exiting getTodosListView() method...");
		return $todo_list;
	}
	$cal_log->debug("Exiting getTodosListView() method...");
	$list_view .="<div id='mnuTab2' style='background-color: rgb(255, 255, 215); display:block;'>";
	//To get Todos listView
	$list_view .= constructTodoListView($todo_list,$cal,$subtab);
	$list_view .="</div></div></td></tr></table></td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</td></tr></table>
		</div>
		</td>
		<td valign=top><img src='".$cal['IMAGE_PATH']."showPanelTopRight.gif'></td>
	</tr>
	</table>

	";
	echo $list_view;
}

/**
 * Function creates HTML to display Calendar DayView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $dayview_layout - html tags in string format
 */
function getDayViewLayout(& $cal)
{
	//echo '<pre>';print_r($cal);echo '</pre>';
	global $current_user,$app_strings,$cal_log,$adb;
	$no_of_rows = 1;
	$cal_log->debug("Entering getDayViewLayout() method...");
	$shared_ids = getSharedCalendarId($current_user->id);
	$user_details = getAllUserName();
	$usersid = $current_user->id.','.$shared_ids;
	$userid_arr = explode(",",$usersid);
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$dayview_layout = '';
	$dayview_layout .= '<!-- Day view layout starts here --> <table border="0" cellpadding="10" cellspacing="0" width="100%">';
	$dayview_layout .= '<tr>
				<td id="mainContent" style="border-top: 1px solid rgb(204, 204, 204);">
					<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	for($j=0;$j<24;$j++)
	{
		$slice = $cal['calendar']->slices[$j];
		$act = $cal['calendar']->day_slice[$slice]->activities;
		if(!empty($act))
		{
			$temprows = count($act);
			$no_of_rows = ($no_of_rows>$temprows)?$no_of_rows:$temprows;
		}
	}
	for($i=$day_start_hour;$i<=$day_end_hour;$i++)
	{
		
		if($cal['calendar']->hour_format == 'am/pm')
		{
			if($i == 12)
			{
				$hour = $i;
				$sub_str = 'pm';
			}
			elseif($i>12)
			{
				$hour = $i - 12;
				$sub_str = 'pm';
			}
			else
			{
				if($i == 0)
					$hour = 12;
				else
					$hour = $i;
				$sub_str = 'am';
			}
			
		}
		else
		{
			$hour = $i;
			if($hour <= 9 && strlen(trim($hour)) < 2)
				$hour = "0".$hour;
			$sub_str = ':00';
		}
		$y = $i+1;
		$hour_startat = convertTime2UserSelectedFmt($format,$i,false);
		$hour_endat = convertTime2UserSelectedFmt($format,$y,false);
		$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
		$temp_ts = $cal['calendar']->date_time->ts;
		$sttemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
		if($i != 23)
		{
			$endtemp_date = $sttemp_date;
		}
		else
		{
			$addday = $temp_ts + (1 * 24 * 60 * 60);
			$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$addday)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$addday)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $addday)):(''))));
		}

		$js_string = "";
		if(isPermitted("Calendar","EditView") == "yes")
		              $js_string = 'onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'call\',\''.$sttemp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')"';
		$dayview_layout .= '<tr>
					<td style="cursor:pointer;" class="lvtCol" valign=top height="75"  width="10%" '.$js_string.'>'.$hour.''.$sub_str.'</td>';
		//To display events in Dayview
		$dayview_layout .= getdayEventLayer($cal,$cal['calendar']->slices[$i],$no_of_rows);
		$dayview_layout .= '</tr>';
	}
	$dayview_layout .= '</table>
			</td></tr></table>';
	$cal_log->debug("Exiting getDayViewLayout() method...");
	return $dayview_layout;		
}

/**
 * Function creates HTML to display Calendar WeekView
 * @param  array     $cal             - collections of objects and strings.
 * return  string    $weekview_layout - html tags in string format
 */
function getWeekViewLayout(& $cal)
{
	global $current_user,$app_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getWeekViewLayout() method...");
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$weekview_layout = '';
        $weekview_layout .= '<table border="0" cellpadding="10" cellspacing="0" width="98%" class="calDayHour" style="background-color: #dadada">';
	for ($col=0;$col<=7;$col++)
        {
        	if($col==0)
                {
                	$weekview_layout .= '<tr>';
                	$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>&nbsp;</td>';
		}
		else
		{
			//To display Days in Week
			$cal['slice'] = $cal['calendar']->week_array[$cal['calendar']->slices[$col-1]];
			$date = $cal['calendar']->date_time->getThisweekDaysbyIndex($col-1);
			$day = $date->getdayofWeek_inshort();
			$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>';
			$weekview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().'&'.$cal['slice']->start_time->get_date_str().'&parenttab='.$category.'">';
			$weekview_layout .= $date->get_Date().' - '.$day;
			$weekview_layout .= "</a>";
			$weekview_layout .= '</td>';
		}
	}
	$weekview_layout .= '</tr></table>';
	$weekview_layout .= '<table border="0" cellpadding="10" cellspacing="1" width="98%" class="calDayHour" style="background-color: #dadada">';
	//To display Hours in User selected format
	for($i=$day_start_hour;$i<=$day_end_hour;$i++)
	{
		$count = $i;
		$hour_startat = convertTime2UserSelectedFmt($format,$i,false);
	        $hour_endat = convertTime2UserSelectedFmt($format,($i+1),false);
		$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
		$weekview_layout .= '<tr>';
		for ($column=1;$column<=1;$column++)
        	{
        	       	if($cal['calendar']->hour_format == 'am/pm')
                	{
                       		if($i == 12)
                       		{
                               		$hour = $i;
	                               	$sub_str = 'pm';
	                        }
        	       	        elseif($i>12)
                        	{
                       	        	$hour = $i - 12;
	                       	        $sub_str = 'pm';
	                        }
        	       	        else
                        	{
					if($i == 0)
						$hour = 12;
					else
                       	        		$hour = $i;
	                       	        $sub_str = 'am';
        	                }

       	        	}
       			else
           		{
                       		$hour = $i;
				if($hour <= 9 && strlen(trim($hour)) < 2)
		                        $hour = "0".$hour;
                        	$sub_str = ':00';
       	        	}

			$weekview_layout .= '<td style="border-top: 1px solid rgb(239, 239, 239); background-color: rgb(234, 234, 234); height: 40px;" valign="top" width="12%">';
			$weekview_layout .=$hour.''.$sub_str;
	                $weekview_layout .= '</td>';
		}
		for ($column=0;$column<=6;$column++)
		{
			$temp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column]]->start_time->ts;
			$sttemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
			if($i != 23)
				$endtemp_date = $sttemp_date;
			else
			{
				$endtemp_ts = $cal['calendar']->week_array[$cal['calendar']->slices[$column+1]]->start_time->ts;
				$endtemp_date =  (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
			}

			$weekview_layout .= '<td class="cellNormal" onMouseOver="cal_show(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')" onMouseOut="fnHide_Event(\'create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')"  style="height: 40px;" bgcolor="white" valign="top" width="12%" align=right vlign=top>';
			$weekview_layout .= '<div id="create_'.$sttemp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'" style="visibility: hidden;">';
			if(isPermitted("Calendar","EditView") == "yes")
		                        $weekview_layout .='<img onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'call\',\''.$sttemp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')" src="'.$cal['IMAGE_PATH'].'cal_add.gif" border="0">';
					
                        $weekview_layout .='</div>';
			//To display events in WeekView
			$weekview_layout .=getweekEventLayer($cal,$cal['calendar']->week_hour_slices[$count]);
			$weekview_layout .= '</td>';
			$count = $count+24;
		}
		$weekview_layout .= '</tr>';
	}
	$weekview_layout .= '</table>';
	return $weekview_layout;
	$cal_log->debug("Exiting getWeekViewLayout() method...");
		
}

/**
 * Function creates HTML to display Calendar MonthView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $monthview_layout - html tags in string format
 */
function getMonthViewLayout(& $cal)
{
	global $current_user,$app_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getMonthViewLayout() method...");
	$date_format = $current_user->date_format;
	$count = 0;
	//To get no. of rows(weeks) in month
        if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
                $rows = 5;
        } else {
                $rows = 6;
        }
	$format = $cal['calendar']->hour_format;
        $hour_startat = convertTime2UserSelectedFmt($format,$cal['calendar']->day_start_hour,false);
        $hour_endat = convertTime2UserSelectedFmt($format,($cal['calendar']->day_start_hour+1),false);
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$monthview_layout = '';
	$monthview_layout .= '<table class="calDayHour" style="background-color: rgb(218, 218, 218);" border="0" cellpadding="5" cellspacing="1" width="98%"><tr>';
	//To display days in week 
	for ($i = 0; $i < 7; $i ++)
	{
		$first_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
		$weekday = $first_row->start_time->getdayofWeek();
		$monthview_layout .= '<td class="lvtCol" valign="top" width="14%">'.$weekday.'</td>';
	}
	$monthview_layout .= '</tr></table>';
	$monthview_layout .= '<!-- month headers --> <table border=0 cellspacing=1 cellpadding=5 width=98% class="calDayHour" >';
	$cnt = 0;
	for ($i = 0; $i < $rows; $i ++)
	{
	        $monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
                {
			$temp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count]]->start_time->ts;
	                $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
			if($cal['calendar']->day_start_hour != 23)
				$endtemp_date = $temp_date;
			else
			{
				$endtemp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$count+1]]->start_time->ts;
				$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
			}
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$monthclass = dateCheck($cal['slice']->start_time->get_formatted_date());
			if($monthclass != '')
				$monthclass = 'calSel';
			else
				$monthclass = 'dvtCellLabel';
			//to display dates in month
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth())
			{
				$monthview_layout .= '<td style="text-align:left;" class="'.$monthclass.'" width="14%" onMouseOver="cal_show(\'create_'.$temp_date.''.$time_arr['starthour'].'\')" onMouseOut="fnHide_Event(\'create_'.$temp_date.''.$time_arr['starthour'].'\')">';
				$monthview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().''.$cal['slice']->start_time->get_date_str().'&parenttab='.$category.'">';
				$monthview_layout .= $cal['slice']->start_time->get_Date();
				$monthview_layout .= '</a>';
				$monthview_layout .= '<div id="create_'.$temp_date.''.$time_arr['starthour'].'" style="visibility:hidden;">';
				if(isPermitted("Calendar","EditView") == "yes")
                                $monthview_layout .='<a onClick="fnvshobj(this,\'addEvent\'); gshow(\'addEvent\',\'call\',\''.$temp_date.'\',\''.$endtemp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\',\'hourview\',\'event\')" href="javascript:void(0)"><img src="'.$cal['IMAGE_PATH'].'cal_add.gif" border="0"></a>';
                                $monthview_layout .= '  </div></td>';
			}
			else
			{
				$monthview_layout .= '<td class="dvtCellLabel" width="14%">&nbsp;</td>';
			}
			$count++;
		}
		$monthview_layout .= '</tr>';
		$monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
		{
			$monthview_layout .= '<td bgcolor="white" height="90" valign="top" width="200" align=right>';
			$monthview_layout .= getmonthEventLayer($cal,$cal['calendar']->slices[$cnt]);
			$monthview_layout .= '</td>';
			$cnt++;
		}
		$monthview_layout .= '</tr>';
	}
	$monthview_layout .= '</table>';
	return $monthview_layout;
	$cal_log->debug("Exiting getMonthViewLayout() method...");
		
}

/**
 * Function creates HTML to display Calendar YearView
 * @param  array     $cal            - collections of objects and strings.
 * return  string    $yearview_layout - html tags in string format
 */
function getYearViewLayout(& $cal)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getYearViewLayout() method...");
	$yearview_layout = '';
	$yearview_layout .= '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	$count = 0;
	//year view divided as 4 rows and 3 columns
	for($i=0;$i<4;$i++)
	{
		$yearview_layout .= '<tr>';
		for($j=0;$j<3;$j++)
        	{
			$cal['slice'] = $cal['calendar']->year_array[$cal['calendar']->slices[$count]];
			$yearview_layout .= '<td width="33%">
						<table class="mailClient " border="0" cellpadding="2" cellspacing="0" width="98%">
							<tr>
								<td colspan="7" class="calHdr" style="padding:5px">
								<a style="text-decoration: none;" href="index.php?module=Calendar&action=index&view=month&hour=0&day=1&month='.($count+1).'&year='.$cal['calendar']->date_time->year.'&parenttab='.$category.'"><b>
									'.$cal['slice']->start_time->month_inlong.'
									</b></a>
								</td>
							</tr><tr class="hdrNameBg">';
			for($w=0;$w<7;$w++)
			{
				$yearview_layout .= '<th width="14%">'.$mod_strings['cal_weekdays_short'][$w].'</th>';
			}
			$yearview_layout .= '</tr>';
			list($_3rdyear,$_3rdmonth,$_3rddate) = explode("-",$cal['calendar']->month_day_slices[$count][35]);
			list($_2ndyear,$_2ndmonth,$_2nddate) = explode("-",$cal['calendar']->month_day_slices[$count][6]);
			//to get no. of rows(weeks) in month
			if ($_3rdmonth != $_2ndmonth) {
	        	        $rows = 5;
        		} else {
		                $rows = 6;
		        }
			$cnt = 0;
			$date_stack = Array();
			for ($k = 0; $k < 5; $k ++)
        		{
				$yearview_layout .= '<tr>';
				for ($mr = 0; $mr < 7; $mr ++)
				{
					list($_1styear,$_1stmonth,$_1stdate) = explode("-",$cal['calendar']->month_day_slices[$count][$cnt]);
					if(count($cal['slice']->activities) != 0)
					{
						for($act_count = 0;$act_count<count($cal['slice']->activities);$act_count++)
						{
							array_push($date_stack,$cal['slice']->activities[$act_count]->start_time->get_formatted_date());
						}
					}
					if(in_array($cal['calendar']->month_day_slices[$count][$cnt],$date_stack))
						$event_class = 'class="eventDay"'; 
					else
						$event_class = '';
					if($_1stmonth == $_2ndmonth)
						$curclass = dateCheck($cal['calendar']->month_day_slices[$count][$cnt]);
					if($curclass != '')
					{
						$class = 'class="'.$curclass.'"';
						$curclass = '';
					}
					else
					{
						$class = $event_class;
						$event_class = '';
					}
					$date = $_1stdate + 0;
					$month = $_1stmonth + 0;
					$yearview_layout .= '<td '.$class.' style="text-align:center">';
					if($rows == 6 && $k==0)
					{
						list($tempyear,$tempmonth,$tempdate) = explode("-",$cal['calendar']->month_day_slices[$count][35+$mr]);
						if($tempmonth == $_2ndmonth)
							$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$tempdate.'&month='.$tempmonth.'&year='.$tempyear.'&parenttab='.$category.'">'.$tempdate;
					}
					if($_1stmonth == $_2ndmonth)
					{
						$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$date.'&month='.$month.'&year='.$_1styear.'&parenttab='.$category.'">'.$date;
					}
					$yearview_layout .= '</a></td>';
				$cnt++;
				}
	                	$yearview_layout .= '</tr>';
			}
			$yearview_layout .= '
						</table>		
						

						';
			$count++;	
		}
		$yearview_layout .= '</tr>';
	}
	$yearview_layout .= '</table>';
	return $yearview_layout;
	$cal_log->debug("Exiting getYearViewLayout() method...");
        
	
}

/**
 * Function creates HTML To display events in day view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * returns string    $eventlayer  - hmtl in string format
 */
function getdayEventLayer(& $cal,$slice,$rows)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getdayEventLayer() method...");
	$eventlayer = '';
	$arrow_img_name = '';
	$rows = $rows + 1;
	$last_colwidth = 100 / $rows;
	$width = 100 / $rows ;
	$act = $cal['calendar']->day_slice[$slice]->activities;
	//echo '<pre>';print_r($act);echo '</pre>';
	if(!empty($act))
	{
		for($i=0;$i<count($act);$i++)
		{
			$rowspan = 1;
			$arrow_img_name = 'event'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i;
			$subject = $act[$i]->subject;
			$id = $act[$i]->record;
			if(strlen($subject)>25)
				$subject = substr($subject,0,25)."...";
			$start_time = $act[$i]->start_time->hour.':'.$act[$i]->start_time->minute;
			$end_time = $act[$i]->end_time->hour.':'.$act[$i]->end_time->minute;
			$format = $cal['calendar']->hour_format;
			$duration_hour = $act[$i]->duration_hour;
			$duration_min =$act[$i]->duration_minute;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
			if($duration_min != '00')
				$rowspan = $duration_hour+$rowspan;
			elseif($duration_hour != '0')
			{
				$rowspan = $duration_hour;
			}
			$row_cnt = $rowspan;
			//$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
			//$start_hour = $st_end_time['starttime'];
			//$end_hour = $st_end_time['endtime'];
			$value = getaddEventPopupTime($start_time,$end_time,$format);
			$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
			$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
			$account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$shared = $act[$i]->shared;
			$color = $act[$i]->color;
			$image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
			$height = $rowspan * 75;
			$javacript_str = '';
			/*if($eventstatus != 'Held')
			{*/
				if(isPermitted("Calendar","EditView") == "yes")
					$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
				$action_str = '<img src="'.$cal['IMAGE_PATH'].'cal_event.jpg" id="'.$arrow_img_name.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.',\''.$cal['view'].'\',\''.$cal['calendar']->date_time->hour.'\',\''.$cal['calendar']->date_time->day.'\',\''.$cal['calendar']->date_time->month.'\',\''.$cal['calendar']->date_time->year.'\',\'event\');" align="middle" border="0">';
			/*}
			else
			{
				$javacript_str = '';
				$eventlayer .= '&nbsp;';
			}*/
			$eventlayer .= '<td class="dvtCellInfo" rowspan="'.$rowspan.'" colspan="1" width="'.$width.'%" >';
			$eventlayer .= '<div id="event_'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'" class="event" style="height:'.$height.'px;" '.$javacript_str.'>
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td><img src="'.$image.'" align="middle" border="0"></td>
					<td width="100%"><b>'.$start_hour.' - '.$end_hour.'</b></td>
				</tr>';
			$eventlayer .= '<tr><td>';
			if($shared)
				$eventlayer .= '<img src="'.$cal['IMAGE_PATH'].'cal12x12Shared.gif" align="middle" border="0">';
			else
				$eventlayer .= '&nbsp;';
			$eventlayer .= '</td>
				<td><a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab">'.$subject.'</span></a></td>
				</tr>
				<tr><td>'.$action_str.'</td><td>('.$user.' | '.$eventstatus.' | '.$priority.')</td>
			</table>
			
			</div>';
			$eventlayer .= '</td>';
		}
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	}
	else
	{
		$eventlayer .= '<td class="dvtCellInfo" colspan="'.($rows - 1).'" width="'.($last_colwidth * ($rows - 1)).'%" rowspan="1">&nbsp;</td>';
		$eventlayer .= '<td class="dvtCellInfo" rowspan="1" width="'.$last_colwidth.'%">&nbsp;</td>';
	}
	$cal_log->debug("Exiting getdayEventLayer() method...");
	return $eventlayer;
}

/**
 * Function creates HTML To display events in week view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date:time(eg: 2006-07-13:10)
 * returns string    $eventlayer  - hmtl in string format
 */
function getweekEventLayer(& $cal,$slice)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getweekEventLayer() method...");
        $eventlayer = '';
        $arrow_img_name = '';
	$act = $cal['calendar']->week_slice[$slice]->activities;
	if(!empty($act))
        {
		for($i=0;$i<count($act);$i++)
                {
			$arrow_img_name = 'weekevent'.$cal['calendar']->week_slice[$slice]->start_time->get_formatted_date().'_'.$i;
			$id = $act[$i]->record;
                        $subject = $act[$i]->subject;
			if(strlen($subject)>25)
				$subject = substr($subject,0,25)."...";
			$format = $cal['calendar']->hour_format;
                        $duration_hour = $act[$i]->duration_hour;
                        $duration_min = $act[$i]->duration_minute;
			$start_time = $act[$i]->start_time->hour.':'.$act[$i]->start_time->minute;
			$end_time = $act[$i]->end_time->hour.':'.$act[$i]->end_time->minute;
                        //$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
			//$start_hour = $st_end_time['starttime'];
                        //$end_hour = $st_end_time['endtime'];
			$value = getaddEventPopupTime($start_time,$end_time,$format);
			$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
			$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
                        $account_name = $act[$i]->accountname;
			$eventstatus = $act[$i]->eventstatus;
			$shared = $act[$i]->shared;
			$user = $act[$i]->owner;
			$priority = $act[$i]->priority;
                        $image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
                        $color = $act[$i]->color;
			if(isPermitted("Calendar","EditView") == "yes")
				$javacript_str = 'onMouseOver="cal_show(\''.$arrow_img_name.'\');" onMouseOut="fnHide_Event(\''.$arrow_img_name.'\');"';
			$action_str = '<img src="'.$cal['IMAGE_PATH'].'cal_event.jpg" id="'.$arrow_img_name.'" style="visibility: hidden;" onClick="getcalAction(this,\'eventcalAction\','.$id.',\''.$cal['view'].'\',\''.$cal['calendar']->date_time->hour.'\',\''.$cal['calendar']->date_time->day.'\',\''.$cal['calendar']->date_time->month.'\',\''.$cal['calendar']->date_time->year.'\',\'event\');" align="middle" border="0">';
												 
			$eventlayer .='<div class ="event" '.$javacript_str.' id="event_'.$cal['calendar']->week_slice[$slice]->start_time->get_formatted_date().'_'.$i.'">
			<table border="0" cellpadding="1" cellspacing="0" width="100%">
				<tr>
					<td><img src="'.$image.'" align="middle" border="0"></td>
					<td width="100%"><b>'.$start_hour.' - '.$end_hour.'</b></td>
				</tr>
				<tr>
					<td>';
			if($shared)
				$eventlayer .= '<img src="'.$cal['IMAGE_PATH'].'cal12x12Shared.gif" align="middle" border="0">';
			else
				$eventlayer .= '&nbsp;';
			$eventlayer .= '	
				</td>
					<td><a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab">'.$subject.'</span></a></td>
				</tr>
				<tr><td>'.$action_str.'</td><td>('.$user.' | '.$eventstatus.' | '.$priority.')</td>
			</table>
		        </div><br>';
                }
		$cal_log->debug("Exiting getweekEventLayer() method...");
		return $eventlayer;
	}
			
}

/**
 * Function creates HTML To display events in month view
 * @param  array     $cal         - collection of objects and strings
 * @param  string    $slice       - date(eg: 2006-07-13)
 * returns string    $eventlayer  - hmtl in string format
 */
function getmonthEventLayer(& $cal,$slice)
{
	global $mod_strings,$cal_log;
	$category = getParentTab();
	$cal_log->debug("Entering getmonthEventLayer() method...");
	$eventlayer = '';
	$arrow_img_name = '';
	$act = $cal['calendar']->month_array[$slice]->activities;
	if(!empty($act))
        {
		$no_of_act = count($act);
		if($no_of_act>2)
		{
			$act_row = 2;
			$remin_list = $no_of_act - $act_row;
		}
		else
		{
			$act_row = $no_of_act;
			$remin_list = null;
		}
                for($i=0;$i<$act_row;$i++)
                {
                        $arrow_img_name = 'event'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i;
			$id = $act[$i]->record;
                        $subject = $act[$i]->subject;
                        if(strlen($subject)>10)
                                $subject = substr($subject,0,10)."...";
			$start_time = $act[$i]->start_time->hour.':'.$act[$i]->start_time->minute;
			$end_time = $act[$i]->end_time->hour.':'.$act[$i]->end_time->minute;
			$format = $cal['calendar']->hour_format;
                        $duration_hour = $act[$i]->duration_hour;
                        $duration_min = $act[$i]->duration_minute;
                        //$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
                        //$start_hour = $st_end_time['starttime'];
                        //$end_hour = $st_end_time['endtime'];
			$value = getaddEventPopupTime($start_time,$end_time,$format);
			$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
			$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
                        $account_name = $act[$i]->accountname;
                        $image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
			$color = $act[$i]->color;
			$eventlayer .='<div class ="event" id="event_'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
					<table border="0" cellpadding="1" cellspacing="0" width="100%">
						<tr>
							<td><img src="'.$image.'" align="middle" border="0"></td>
							<td width="100%"><a href="index.php?action=DetailView&module=Calendar&record='.$id.'&activity_mode=Events&viewtype=calendar&parenttab='.$category.'"><span class="orgTab"><small>'.$start_hour.' - '.$end_hour.'</small></span></td>
						</tr>
					</table>
                                </div><br>';
                }
		if($remin_list != null)
		{
			$eventlayer .='<div valign=bottom align=right width=10%>
					<a href="index.php?module=Calendar&action=index&view='.$cal['calendar']->month_array[$slice]->getView().'&'.$cal['calendar']->month_array[$slice]->start_time->get_date_str().'&parenttab='.$category.'" class="webMnu">
					+'.$remin_list.'&nbsp;'.$mod_strings['LBL_MORE'].'</a></div>';
		}
		$cal_log->debug("Exiting getmonthEventLayer() method...");
                return $eventlayer;
        }

}

/**
 * Function to get events list scheduled between specified dates
 * @param array   $calendar              -  collection of objects and strings
 * @param string  $start_date            -  date string
 * @param string  $end_date              -  date string
 * @param string  $info                  -  string 'listcnt' or empty string. if 'listcnt' means it returns no. of events and no. of pending events in array format else it returns events list in array format
 * return array  $Entries               -  eventslists in array format
 */
function getEventList(& $calendar,$start_date,$end_date,$info='')
{
	global $log;
	$Entries = Array();
	$category = getParentTab();
	global $adb,$current_user,$mod_strings,$cal_log;
	$cal_log->debug("Entering getEventList() method...");
	$shared_ids = getSharedCalendarId($current_user->id);
	if(empty($shared_ids))
		$shared_ids = $current_user->id;
	$query = "SELECT vtiger_groups.groupname, vtiger_users.user_name,
       		vtiger_activity.* FROM vtiger_activity
		INNER JOIN vtiger_crmentity
			ON vtiger_crmentity.crmid = vtiger_activity.activityid
		LEFT JOIN vtiger_activitygrouprelation
	       		ON vtiger_activitygrouprelation.activityid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_groups
	       		ON vtiger_groups.groupname = vtiger_activitygrouprelation.groupname
		LEFT JOIN vtiger_users
	       		ON vtiger_users.id = vtiger_crmentity.smownerid 
		LEFT OUTER JOIN vtiger_recurringevents
			ON vtiger_recurringevents.activityid = vtiger_activity.activityid
		WHERE vtiger_crmentity.deleted = 0
			AND (vtiger_activity.activitytype = 'Meeting' OR vtiger_activity.activitytype = 'Call')
			AND (vtiger_activity.date_start BETWEEN '".$start_date."' AND '".$end_date."'
				OR vtiger_recurringevents.recurringdate BETWEEN '".$start_date."' AND '".$end_date."') ";
	if($info != '')
	{
		$pending_query = $query." AND (vtiger_activity.eventstatus = 'Planned')
			AND vtiger_crmentity.smownerid = ".$current_user->id." 
		GROUP BY vtiger_activity.activityid 
		ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";
		$res = $adb->query($pending_query);
		$pending_rows = $adb->num_rows($res);
	}
	if(!is_admin($current_user))
		$query .= " AND vtiger_crmentity.smownerid in (".$shared_ids.") ";
		
	$query .= "GROUP BY vtiger_activity.activityid ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";
 	if( $adb->dbType == "pgsql")
 	    $query = fixPostgresQuery( $query, $log, 0);
	$result = $adb->query($query);
	$rows = $adb->num_rows($result);
	if($info != '')
        {
		$cal_log->debug("Exiting getEventList() method...");
		return Array('totalevent'=>$rows,'pendingevent'=>$pending_rows);
        }
	for($i=0;$i<$rows;$i++)
	{
		$element = Array();
		$element['no'] = $i+1;
		$image_tag = "";
		$contact_data = "";
		$more_link = "";
		$duration_hour = $adb->query_result($result,$i,"duration_hours");
                $duration_min = $adb->query_result($result,$i,"duration_minutes");
		$start_time = $adb->query_result($result,$i,"time_start");
		$end_time = $adb->query_result($result,$i,"time_end");
		$format = $calendar['calendar']->hour_format;
		//$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
		$value = getaddEventPopupTime($start_time,$end_time,$format);
		$start_hour = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$end_hour = $value['endhour'] .':'.$value['endmin'].''.$value['endfmt'];
		if($calendar['view'] == 'day')
		{
			$element['starttime'] = $start_hour;
                	$element['endtime'] = $end_hour;
		}
		else
		{
			$date_start = $adb->query_result($result,$i,"date_start");
			$due_date = $adb->query_result($result,$i,"due_date");
			$element['starttime'] = getDisplayDate($date_start);
			$element['endtime'] = getDisplayDate($due_date);
		}
		$contact_id = $adb->query_result($result,$i,"contactid");
		$id = $adb->query_result($result,$i,"activityid");
		$subject = $adb->query_result($result,$i,"subject");
		$eventstatus = $adb->query_result($result,$i,"eventstatus");
                if(strlen($subject)>25)
	                $subject = substr($subject,0,25)."...";
		if($contact_id != '')
		{
			$contactname = getContactName($contact_id);
			$contact_data = "<b>".$contactname."</b>,";
		}
		$more_link = "<a href='index.php?action=DetailView&module=Calendar&record=".$id."&activity_mode=Events&viewtype=calendar&parenttab=".$category."' class='webMnu'>[".$mod_strings['LBL_MORE']."...]</a>";
		$type = $adb->query_result($result,$i,"activitytype");
		if($type == 'Call')
			$image_tag = "<img src='".$calendar['IMAGE_PATH']."Calls.gif' align='middle'>&nbsp;".$type;
		if($type == 'Meeting')
			$image_tag = "<img src='".$calendar['IMAGE_PATH']."Meetings.gif' align='middle'>&nbsp;".$type;
        	$element['eventtype'] = $image_tag;
		$element['eventdetail'] = $contact_data." ".$subject."&nbsp;".$more_link;
		if(isPermitted("Calendar","EditView") == "yes")
			$element['action'] ="<img onClick='getcalAction(this,\"eventcalAction\",".$id.",\"".$calendar['view']."\",\"".$calendar['calendar']->date_time->hour."\",\"".$calendar['calendar']->date_time->day."\",\"".$calendar['calendar']->date_time->month."\",\"".$calendar['calendar']->date_time->year."\",\"event\");' src='".$calendar['IMAGE_PATH']."cal_event.jpg' border='0'>";
		else
			$element['action'] ="&nbsp;";
        	$element['status'] = $adb->query_result($result,$i,"eventstatus");
		$assignedto = $adb->query_result($result,$i,"user_name");
		if(!empty($assignedto))
			$element['assignedto'] = $assignedto;
		else
			$element['assignedto'] = $adb->query_result($result,$i,"groupname");
	$Entries[] = $element;
	}
	$cal_log->debug("Exiting getEventList() method...");
	return $Entries;
}

/**
 * Function to get todos list scheduled between specified dates
 * @param array   $calendar              -  collection of objects and strings
 * @param string  $start_date            -  date string
 * @param string  $end_date              -  date string
 * @param string  $info                  -  string 'listcnt' or empty string. if 'listcnt' means it returns no. of todos and no. of pending todos in array format else it returns todos list in array format
 * return array   $Entries               -  todolists in array format
 */
function getTodoList(& $calendar,$start_date,$end_date,$info='')
{
	global $log;
        $Entries = Array();
	$category = getParentTab();
	global $adb,$current_user,$mod_strings,$cal_log;
	$cal_log->debug("Entering getTodoList() method...");
	$shared_ids = getSharedCalendarId($current_user->id);
	if(empty($shared_ids))
		$shared_ids = $current_user->id;
        $query = "SELECT vtiger_groups.groupname, vtiger_users.user_name, vtiger_cntactivityrel.contactid, 
		vtiger_activity.* FROM vtiger_activity
                INNER JOIN vtiger_crmentity
                        ON vtiger_crmentity.crmid = vtiger_activity.activityid
                LEFT JOIN vtiger_cntactivityrel
                        ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
		LEFT JOIN vtiger_activitygrouprelation
		        ON vtiger_activitygrouprelation.activityid = vtiger_crmentity.crmid
		LEFT JOIN vtiger_groups
		        ON vtiger_groups.groupname = vtiger_activitygrouprelation.groupname
		LEFT JOIN vtiger_users
			ON vtiger_users.id = vtiger_crmentity.smownerid
                WHERE vtiger_crmentity.deleted = 0
                        AND vtiger_activity.activitytype = 'Task'
                        AND (vtiger_activity.date_start BETWEEN '".$start_date."' AND '".$end_date."')";
        if($info != '')
        {
                $pending_query = $query." AND (vtiger_activity.status != 'Completed')
                        AND vtiger_crmentity.smownerid = ".$current_user->id."
                ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";
		if( $adb->dbType == "pgsql")
 		    $pending_query = fixPostgresQuery( $pending_query, $log, 0);
                $res = $adb->query($pending_query);
                $pending_rows = $adb->num_rows($res);
        }
	
	if(!is_admin($current_user))
                $query .= " AND vtiger_crmentity.smownerid in (".$shared_ids.")";
        $query .= " ORDER BY vtiger_activity.date_start,vtiger_activity.time_start ASC";
	if( $adb->dbType == "pgsql")
 	    $query = fixPostgresQuery( $query, $log, 0);

        $result = $adb->query($query);
        $rows = $adb->num_rows($result);
        if($info != '')
        {
		$cal_log->debug("Exiting getTodoList() method...");
                return Array('totaltodo'=>$rows,'pendingtodo'=>$pending_rows);
        }
	for($i=0;$i<$rows;$i++)
        {
                $element = Array();
                $element['no'] = $i+1;
                $more_link = "";
                $start_time = $adb->query_result($result,$i,"time_start");
                $format = $calendar['calendar']->hour_format;
                //$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time);
		$value = getaddEventPopupTime($start_time,$start_time,$format);
                $element['starttime'] = $value['starthour'].':'.$value['startmin'].''.$value['startfmt'];
		$date_start = $adb->query_result($result,$i,"date_start");
		$due_date = $adb->query_result($result,$i,"due_date");
		if($calendar['view'] != 'day')
			$element['startdate'] = getDisplayDate($date_start);
		$element['duedate'] = getDisplayDate($due_date);

                $id = $adb->query_result($result,$i,"activityid");
                $subject = $adb->query_result($result,$i,"subject");
		$status = $adb->query_result($result,$i,"status");
		$more_link = "<a href='index.php?action=DetailView&module=Calendar&record=".$id."&activity_mode=Task&viewtype=calendar&parenttab=".$category."' class='webMnu'>".$subject."</a>";
		$element['tododetail'] = $more_link;
		$element['status'] = $adb->query_result($result,$i,"status");
		if(isPermitted("Calendar","EditView") == "yes")
			$element['action'] ="<img onClick='getcalAction(this,\"taskcalAction\",".$id.",\"".$calendar['view']."\",\"".$calendar['calendar']->date_time->hour."\",\"".$calendar['calendar']->date_time->day."\",\"".$calendar['calendar']->date_time->month."\",\"".$calendar['calendar']->date_time->year."\",\"todo\");' src='".$calendar['IMAGE_PATH']."cal_event.jpg' border='0'>";
		else
			$element['action'] ="&nbsp;";
		$assignedto = $adb->query_result($result,$i,"user_name");
		if(!empty($assignedto))
			$element['assignedto'] = $assignedto;
		else
			$element['assignedto'] = $adb->query_result($result,$i,"groupname");
		$Entries[] = $element;
	}
	$cal_log->debug("Exiting getTodoList() method...");
	return $Entries;
}

/**
 * Function to get number of Events and Todos Info
 * @param array    $cal              - collection of objects and strings 
 * @param string   $mode             - string 'listcnt' or may be empty. if empty means get Events/Todos ListView else get total events/todos and no. of pending events/todos Info.
 * return array    $event_todo_info  - collection of events/todos info.
 */
function getEventTodoInfo(& $cal, $mode)
{
	global $mod_strings,$cal_log;
	$cal_log->debug("Entering getEventTodoInfo() method...");
	$event_todo = Array();
	$event_todo['event']=getEventListView($cal, $mode);
	$event_todo['todo'] = getTodosListView($cal, $mode);
	$event_todo_info = "";
	$event_todo_info .= $mod_strings['LBL_TOTALEVENTS']."&nbsp;".$event_todo['event']['totalevent'];
	if($event_todo['event']['pendingevent'] != null)
		 $event_todo_info .= ", ".$event_todo['event']['pendingevent']."&nbsp;".$mod_strings['LBL_PENDING'];
	$event_todo_info .=" / ";
	$event_todo_info .=$mod_strings['LBL_TOTALTODOS']."&nbsp;".$event_todo['todo']['totaltodo'];
	if($event_todo['todo']['pendingtodo'] != null)
		$event_todo_info .= ", ".$event_todo['todo']['pendingtodo']."&nbsp;".$mod_strings['LBL_PENDING'];
	$cal_log->debug("Exiting getEventTodoInfo() method...");
	
	return $event_todo_info;
}

/**
 * Function creates HTML to display Events ListView
 * @param array  $entry_list    - collection of strings(Event Information)
 * return string $list_view     - html tags in string format
 */
function constructEventListView(& $cal,$entry_list)
{
	global $mod_strings,$app_strings,$cal_log,$current_user;
	$cal_log->debug("Entering constructEventListView() method...");
	$format = $cal['calendar']->hour_format;
	$date_format = $current_user->date_format;
	$hour_startat = convertTime2UserSelectedFmt($format,$cal['calendar']->day_start_hour,false);
	$hour_endat = convertTime2UserSelectedFmt($format,($cal['calendar']->day_start_hour+1),false);
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$temp_ts = $cal['calendar']->date_time->ts;
	//to get date in user selected date format
	$temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	if($cal['calendar']->day_start_hour != 23)
		$endtemp_date = $temp_date;
	else
	{
		$endtemp_ts = $temp_ts + (1 * 24 * 60 * 60);
		$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
	}
	$list_view = "";
	if($cal['view'] == 'day')
	{
		$start_datetime = $mod_strings['LBL_APP_START_TIME'];
		$end_datetime = $mod_strings['LBL_APP_END_TIME'];
	}
	else
	{
		 $start_datetime = $mod_strings['LBL_APP_START_DATE'];
		 $end_datetime = $mod_strings['LBL_APP_END_DATE'];
				 
	}
	//Events listview header labels
	$header = Array('0'=>'#',
                        '1'=>$start_datetime,
                        '2'=>$end_datetime,
                        '3'=>$mod_strings['LBL_EVENTTYPE'],
                        '4'=>$mod_strings['LBL_EVTDTL'],
                        '5'=>$mod_strings['LBL_ACTION'],
                        '6'=>$mod_strings['LBL_STATUS'],
			'7'=>$mod_strings['LBL_ASSINGEDTO'],
                        );
        $header_width = Array('0'=>'5%',
                              '1'=>'10%',
                              '2'=>'10%',
                              '3'=>'10%',
                              '4'=>'28%',
                              '5'=>'10%',
                              '6'=>'10%',
			      '7'=>'15%'
                             );
        $list_view .="<table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        <tr>";
	$header_rows = count($header);
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td nowrap='nowrap' class='lvtCol' width='".$header_width[$i]."'>".$header[$i]."</td>";
        }
        $list_view .="</tr>";
	$rows = count($entry_list);
	if($rows != 0)
	{
		for($i=0;$i<count($entry_list);$i++)
		{
			$list_view .="<tr class='lvtColData' onmouseover='this.className=\"lvtColDataHover\"' onmouseout='this.className=\"lvtColData\"' bgcolor='white'>";
			foreach($entry_list[$i] as $key=>$entry)
			{
				$list_view .="<td nowrap='nowrap'>".$entry."</td>";
			}
			$list_view .="</tr>";
		}
	}
	else
	{
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='8'>
				";
			$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
					<table border='0' cellpadding='5' cellspacing='0' width='98%'>
						<tr>
							<td rowspan='2' width='25%'>
								<img src='".$cal['IMAGE_PATH']."empty.jpg' height='60' width='61'></td>
							<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO']." ".$app_strings['Events']." ".$app_strings['LBL_FOUND']." !</span></td>
						</tr>
						<tr>";
			//checking permission for Create/Edit Operation
			if(isPermitted("Calendar","EditView") == "yes")
                        {
                                $list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE']."&nbsp;".$app_strings['LBL_AN']."&nbsp;".$app_strings['Event']."&nbsp;".$app_strings['LBL_NOW'].".&nbsp;".$app_strings['LBL_CLICK_THE_LINK']."&nbsp;:<br>
					&nbsp;&nbsp;-<a href='javascript:void(0)' onClick='gshow(\"addEvent\",\"meeting\",\"".$temp_date."\",\"".$endtemp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"listview\",\"event\");'>".$app_strings['LBL_CREATE']." ".$app_strings['LBL_A']." ".$app_strings['Meeting']."</a><br>
					&nbsp;&nbsp;-<a href='javascript:void(0);' onClick='gshow(\"addEvent\",\"call\",\"".$temp_date."\",\"".$endtemp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"listview\",\"event\");'>".$app_strings['LBL_CREATE']."&nbsp;".$app_strings['LBL_A']."&nbsp;".$app_strings['Call']."</a><br>
					</td>";
			}
			else
			{
				$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE']."&nbsp;".$app_strings['LBL_AN']."&nbsp;".$app_strings['Event']."<br></td>";
			}
			$list_view .="</tr>
                                        </table>
				</div>";
			$list_view .="</td></tr>";			
	}
	$list_view .="</table>";
	$cal_log->debug("Exiting constructEventListView() method...");
	return $list_view;
}

/**
 * Function creates HTML to display Todos ListView
 * @param array  $todo_list     - collection of strings(Todo Information)
 * @param array  $cal           - collection of objects and strings 
 * return string $list_view     - html tags in string format
 */
function constructTodoListView($todo_list,$cal,$subtab)
{
	global $mod_strings,$cal_log;
	$cal_log->debug("Entering constructTodoListView() method...");
        global $current_user,$app_strings;
        $date_format = $current_user->date_format;
        $format = $cal['calendar']->hour_format;
        $hour_startat = convertTime2UserSelectedFmt($format,$cal['calendar']->day_start_hour,false);
        $hour_endat = convertTime2UserSelectedFmt($format,($cal['calendar']->day_start_hour+1),false);
        $time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
        $temp_ts = $cal['calendar']->date_time->ts;
	//to get date in user selected date format
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	if($cal['calendar']->day_start_hour != 23)
		$endtemp_date = $temp_date;
	else
	{
		$endtemp_ts = $temp_ts + (1 * 24 * 60 * 60);
		$endtemp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$endtemp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$endtemp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $endtemp_ts)):(''))));
	}
        $list_view = "";
	//labels of listview header
	if($cal['view'] == 'day')
	{
		$colspan = 7;
		$header = Array('0'=>'#','1'=>$mod_strings['LBL_TIME'],'2'=>$mod_strings['LBL_LIST_DUE_DATE'],
				'3'=>$mod_strings['LBL_TODO'],'4'=>$mod_strings['LBL_STATUS'],'5'=>$mod_strings['LBL_ACTION'],'6'=>$mod_strings['LBL_ASSINGEDTO'],);
		$header_width = Array('0'=>'5%','1'=>'10%','2'=>'10%','3'=>'38%','4'=>'10%','5'=>'10%', '6'=>'15%', );
	}
	else
	{
		$colspan = 8;
	        $header = Array('0'=>'#',
                        '1'=>$mod_strings['LBL_TIME'],
			'2'=>$mod_strings['LBL_APP_START_DATE'],
			'3'=>$mod_strings['LBL_LIST_DUE_DATE'],
                        '4'=>$mod_strings['LBL_TODO'],
                        '5'=>$mod_strings['LBL_STATUS'],
                        '6'=>$mod_strings['LBL_ACTION'],
			'7'=>$mod_strings['LBL_ASSINGEDTO'],
                       );
        	$header_width = Array('0'=>'5%',
                              '1'=>'10%',
                              '2'=>'10%',
                              '3'=>'10%',
                              '4'=>'28%',
			      '5'=>'10%',
			      '6'=>'10%',
			      '7'=>'15%',
                             );
	}
	$list_view .="<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>";
			//checking permission for Create/Edit Operation
			if(isPermitted("Calendar","EditView") == "yes")
			{
			$list_view .="<tr>
				<td class='calAddButton' onMouseOver='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$endtemp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"\",\"".$subtab."\");'style='border: 1px solid #666666;cursor:pointer;height:30px' align='center' width='10%'>
                                        ".$mod_strings['LBL_ADD']."
                                        <img src='".$cal['IMAGE_PATH']."menuDnArrow.gif' style='padding-left: 5px;' border='0'>                                                                                                                         </td>";
			}
			else
			{
				$list_view .="<tr><td>&nbsp;</td>";
			}
			$list_view .="<td align='center' width='60%'>&nbsp;</td>
				<td align='right' width='28%'>&nbsp;</td>
			</tr>
		</table>

			<br><table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        <tr>";
        $header_rows = count($header);
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td class='lvtCol' width='".$header_width[$i]."' nowrap='nowrap'>".$header[$i]."</td>";
        }
        $list_view .="</tr>";
	$rows = count($todo_list);
        if($rows != 0)
        {
                for($i=0;$i<count($todo_list);$i++)
                {
                        $list_view .="<tr style='height: 25px;' bgcolor='white'>";
                        foreach($todo_list[$i] as $key=>$entry)
                        {
                                $list_view .="<td>".$entry."</td>";
                        }
                        $list_view .="</tr>";
                }
        }
        else
        {
		$list_view .="<tr><td style='background-color:#efefef;height:340px' align='center' colspan='".$colspan."'>";
		$list_view .="<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 45%; position: relative; z-index: 5000;'>
			<table border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr>
				<td rowspan='2' width='25%'>
					<img src='".$cal['IMAGE_PATH']."empty.jpg' height='60' width='61'></td>
				<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='75%'><span class='genHeaderSmall'>".$app_strings['LBL_NO']." ".$app_strings['Todo']."s ".$app_strings['LBL_FOUND']." !</span></td>
			</tr>
			<tr>";
		//checking permission for Create/Edit Operation
		if(isPermitted("Calendar","EditView") == "yes")
		{
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_CAN_CREATE']."&nbsp;".$app_strings['LBL_A']."&nbsp;".$app_strings['Todo']."&nbsp;".$app_strings['LBL_NOW'].".&nbsp;".$app_strings['LBL_CLICK_THE_LINK']."&nbsp;:<br>
					&nbsp;&nbsp;-<a href='javascript:void(0);' onClick='gshow(\"createTodo\",\"todo\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\",\"listview\",\"todo\");'>".$app_strings['LBL_CREATE']." ".$app_strings['LBL_A']." ".$app_strings['Todo']."</a>
					</td>";
		}
		else
		{
			$list_view .="<td class='small' align='left' nowrap='nowrap'>".$app_strings['LBL_YOU_ARE_NOT_ALLOWED_TO_CREATE']."&nbsp;".$app_strings['LBL_A']."&nbsp;".$app_strings['Todo']."<br></td>";
		}
										 
                $list_view .="</tr>
			</table>
			</div>";
		$list_view .="</td></tr>";
        }
	$list_view .="</table><br>";
	$cal_log->debug("Exiting constructTodoListView() method...");
        return $list_view;
}

/**
 * Function to convert time to user selected format
 * @param  string    $format                      - hour format. either 'am/pm' or '24'
 * @param  string    $time                        - time
 * @param  boolean   $format_check                - true/false
 * return  string    $hour                        - time string
 */
function convertTime2UserSelectedFmt($format,$time,$format_check)
{
	global $cal_log;
	$cal_log->debug("Entering convertTime2UserSelectedFmt() method...");
	if($format == 'am/pm' && $format_check)
        {
		if($time>='12')
                {
			if($time == '12')
				$hour = $time;
			else
				$hour = $time - 12;
			$hour = $hour.":00pm";
		}
		else
                {
                        $hour = $time;
			$hour = $hour.":00am";
		}
		$cal_log->debug("Exiting convertTime2UserSelectedFmt() method...");
		return $hour;
	}
	else
        {
                $hour = $time;
		if($hour <= 9 && strlen(trim($hour)) < 2)
                                $hour = "0".$hour;
		$hour = $hour.":00";
		$cal_log->debug("Exiting convertTime2UserSelectedFmt() method...");
		return $hour;
	}
}

/**
 * Function to convert events/todos start and endtime to user selected format
 * @param  string    $format                      - hour format. either 'am/pm' or '24'
 * @param  string    $start_time                  - time
 * @param  string    $duration_hr                 - duration in hours or empty string
 * @param  string    $duration_min                - duration in minutes or empty string
 * return  array     $return_data                 - start and end time in array format
 */
function convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hr='',$duration_min='')
{
	global $cal_log;
	$cal_log->debug("Entering convertStEdTime2UserSelectedFmt() method...");
	list($hour,$min) = explode(":",$start_time);
	if($format == 'am/pm')
        {
                if($hour>'12')
		{
			$hour = $hour - 12;
                        $start_hour = $hour;
			$start_time = $start_hour.":".$min."pm";
                        $end_min = $min+$duration_min;
                        $end_hour = $hour+$duration_hr;
                        if($end_min>=60)
                        {
	                        $end_min = $end_min%60;
                                $end_hour++;
                        }
                        if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
                                $end_hour = "0".$end_hour;
                        if($end_min <= 9 && strlen(trim($end_min)) < 2)
                                $end_min = "0".$end_min;
                        $end_time = $end_hour.":".$end_min."pm";
		}
		elseif($hour == '12')
		{
			$start_hour = $hour;
			$start_time = $start_hour.":".$min."pm";
			$end_min = $min+$duration_min;
			$end_hour = $hour+$duration_hr;
			if($end_min>=60)
			{
				$end_min = $end_min%60;
				$end_hour++;
			}
			if($end_hour>'12')
			{
				$end_hour = $end_hour - 12;
				if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
					$end_hour = "0".$end_hour;
				if($end_min <= 9 && strlen(trim($end_min)) < 2)
					$end_min = "0".$end_min;
				$end_time = $end_hour.":".$end_min."pm";
			}
			else
			{
				if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
					$end_hour = "0".$end_hour;
				if($end_min <= 9 && strlen(trim($end_min)) < 2)
					$end_min = "0".$end_min;
				$end_time  = $end_hour.":".$end_min."am";
			}
		}
		else
		{
			$start_hour = $hour;
			$start_time = $start_hour.":".$min."am";
			$end_min = $min+$duration_min;
			$end_hour = $hour+$duration_hr;
			if($end_min>=60)
			{
				$end_min = $end_min%60;
				$end_hour++;
			}
			if($end_hour>='12')
			{
				if($end_hour == '12' && $end_hour > '00')
					$end_hour = $end_hour;
				else
					$end_hour = $end_hour - 12;
				if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
					$end_hour = "0".$end_hour;
				if($end_min <= 9 && strlen(trim($end_min)) < 2)
					$end_min = "0".$end_min;
				$end_time = $end_hour.":".$end_min."pm";
			}
			else
			{
				if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
					$end_hour = "0".$end_hour;
				if($end_min <= 9 && strlen(trim($end_min)) < 2)
					$end_min = "0".$end_min;
				$end_time  = $end_hour.":".$end_min."am";
			}

		}
		$return_data = Array(
					'starttime'=>$start_time,
					'endtime'  =>$end_time
				    );
	}
	else
	{
		$hour = $hour;
		$min = $min;
		$end_min = $min+$duration_min;
		$end_hour = $hour+$duration_hr;
		if($end_min>=60)
		{
			$end_min = $end_min%60;
			$end_hour++;
		}
		if($end_hour <= 9 && strlen(trim($end_hour)) < 2)
			$end_hour = "0".$end_hour;
		if($end_min <= 9 && strlen(trim($end_min)) < 2)
			$end_min = "0".$end_min;
		$end_time  = $end_hour.":".$end_min;
		if($hour <= 9 && strlen(trim($hour)) < 2)
                                $hour = "0".$hour;
                $start_time = $hour.":".$min;
		$return_data = Array(
                                        'starttime'=>$start_time,
                                        'endtime'  =>$end_time
                                    );
	}
	$cal_log->debug("Exiting convertStEdTime2UserSelectedFmt() method...");
	return $return_data;


}

		
?>
