<?php
require_once('include/database/PearDatabase.php');
require_once('include/utils/CommonUtils.php');

/** To construct calendar subtabs
  * @param $param_arr -- The calendar object:: Type class Calendar
 */
function calendar_layout(& $param_arr)
{
	global $mod_strings;
	$cal_header = array ();
	if (isset($param_arr['size']) && $param_arr['size'] == 'small')
		$param_arr['calendar']->show_events = false;

	$cal_header['view'] = $param_arr['view'];
	$cal_header['IMAGE_PATH'] = $param_arr['IMAGE_PATH'];
        $cal_header['calendar'] = $param_arr['calendar'];
	$eventlabel = $mod_strings['LBL_EVENTS'];
	$todolabel = $mod_strings['LBL_TODOS'];
	if(isset($param_arr['size']) && $param_arr['size'] == 'small')
	{
		get_mini_calendar($param_arr);
	}
	else
	{
		get_cal_header_tab($cal_header);
		$subheader = "";
		$subheader .=<<<EOQ
			<tr>
				<td colspan="8" class="tabBorder">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%">
						<tr>
							<td>
								<table class="small" border="0" cellpadding="3" cellspacing="0" width="100%">
									<tr>
										<td class="dvtTabCache" style="width: 10px;" nowrap="nowrap">&nbsp;</td>
										<td class="dvtSelectedCell" id="pi" onclick="fnLoadValues('pi','mi','mnuTab','mnuTab2')" align="center" nowrap="nowrap" width="75"><b>$eventlabel</b></td>
										<td class="dvtUnSelectedCell" style="width: 100px;" id="mi" onclick="fnLoadValues('mi','pi','mnuTab2','mnuTab')" align="center" nowrap="nowrap"><b>$todolabel</b></td>
										<td class="dvtTabCache" nowrap="nowrap">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="border-left: 1px solid rgb(204, 204, 204); border-right: 1px solid rgb(204, 204, 204); border-bottom: 1px solid rgb(204, 204, 204);" align="left" bgcolor="#ffffff" valign="top">
						<!-- Events Layer Starts Here -->
						<div style='display: block;' id='mnuTab'>

EOQ;
		echo $subheader;
		get_cal_header_data($param_arr);
		$div = "<div id='toggleDiv'></div>";
		echo $div;
		getHourView($param_arr);
	}
	
	
	
}

/** To construct small calendar 
  * @param $cal -- The calendar object:: Type class Calendar
 */
function get_mini_calendar(& $cal)
{
	global $current_user,$adb;
	$count = 0;
	if ($cal['calendar']->month_array[$cal['calendar']->slices[35]]->start_time->month != $cal['calendar']->date_time->month) {
                $rows = 5;
        } else {
                $rows = 6;
        }
	$minical = "";
	$minical .= "<table class='month_table' border='0' cellpadding='0' cellspacing='3' width='98%'>
			<tr><td colspan='7' align='right'>
			<a href='javascript:ghide(\"miniCal\");'><img src='".$cal['IMAGE_PATH']."close.gif' align='right' border='0'></a></td>
			</tr>
                        <tr>
				<td colspan='7' class='cal_Hdr'>
                                <!--td>".get_previous_cal($cal)."
                                </td-->";
        $minical .= "<a style='text-decoration: none;' href='index.php?module=Calendar&action=index&view=".$cal['view']."&".$cal['calendar']->date_time->get_date_str()."'>".display_date($cal['view'],$cal['calendar']->date_time)."</a></td></tr>";
	$minical .= "<tr>";
	for ($i = 0; $i < 7; $i ++)
        {
                $weekdays_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
                $weekday = $weekdays_row->start_time->getdayofWeek_inshort();
                $minical .= '<th>'.$weekday.'</th>';
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
			if(count($cal['slice']->activities) != 0 && ($cal['slice']->start_time->get_formatted_date() == $cal['slice']->activities[0]->start_time->get_formatted_date()))
			{
					$event_class = 'class="eventDay"';
			}
			else
			{
                       		$event_class = '';
                        }
			if($class != '' )
				$class = 'class="'.$class.'"';
			else
				$class = $event_class;
                        $minical .= "<td ".$class.">";
                        $minical .= "<a href='index.php?module=Calendar&action=index&view=".$cal['slice']->getView()."&".$cal['slice']->start_time->get_date_str()."'>";
                        if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth())
                        {
                                $minical .= $cal['slice']->start_time->get_Date();
                        }
                        $monthview_layout .= '</a></td>';
                        $count++;
                }
                $minical .= '</tr>';
	}
	
        $minical .= "<!--td>".get_next_cal($cal)."
                     </td></tr-->
                </table>";
	echo $minical;
	
}

/*To construct calendar headertabs
 * @param $header -- The calendar object:: Type class Calendar
*/
function get_cal_header_tab(& $header)
{
	global $mod_strings;
	$tabhtml = "";
	$count = 1;
	include_once 'modules/Calendar/addEventUI.php';
	$div = "<br><div id='miniCal' style='width:300px; position:absolute; display:none; left:100px; top:100px; z-index:100000;'></div>
		<div id='calSettings' class='calSettings' style='display:none;width:500px;' align=center ></div>
		";
	echo $div;
	$tabhtml .= "<table class='small calHdr' align='center' border='0' cellpadding='5' cellspacing='0' width='90%'><tr>";
        $links = array ('day','week','month','year');
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
			$anchor = "<a href='index.php?module=Calendar&action=index&view=".$link."".$header['calendar']->date_time->get_date_str()."'>".$mod_strings["LBL_".$header['calendar']->getCalendarView($link)]."</a>";
		}
	
		if($count == 1)
			$tabhtml .= "<td style='border-left: 1px solid rgb(102, 102, 102);' class=".$class.">".$anchor."</td>";
		else
			$tabhtml .= "<td class=".$class.">".$anchor."</td>";
		$count++;
	}
	$tabhtml .= "<td width='30%'>
			<table border='0' cellpadding='0' cellspacing='0'>
			<tr>
				<td>".get_previous_cal($header)."
				</td>";
	$tabhtml .= "<td class='calendarNav'>".display_date($header['view'],$header['calendar']->date_time)."</td>";
	$tabhtml .= "<td>".get_next_cal($header)."
		     </td></tr>
		    </table>
		</td>";
	$tabhtml .= "<td width='2%'><a href='#' onClick='fnvshobj(this,\"miniCal\");getMiniCal();'><img src='".$header['IMAGE_PATH']."btnL3Calendar.gif' alt='".$mod_strings['LBL_OPENCAL']."...' title='".$mod_strings['LBL_OPENCAL']."...' align='middle' border='0'></a></td>";
	$tabhtml .= "<td><a href='#' onClick='fnvshobj(this,\"calSettings\");getCalSettings();'><img src='".$header['IMAGE_PATH']."tbarSettings.gif' alt='".$mod_strings['LBL_SETTINGS']."' title='".$mod_strings['LBL_SETTINGS']."' align='middle' border='0'></a></td>";
	$tabhtml .= "<td class='calTitle'>&nbsp;</td>";	
	$tabhtml .= "</tr>";
	echo $tabhtml;
}

/*To display events/todos detail in calendar header
*/
function get_cal_header_data(& $cal_arr)
{
	global $mod_strings;
	global $current_user,$app_strings;
        $date_format = $current_user->date_format;
	$format = $cal_arr['calendar']->hour_format;
	$hour_startat = convertTime2UserSelectedFmt($format,$cal_arr['calendar']->day_start_hour,false); 
	$hour_endat = convertTime2UserSelectedFmt($format,($cal_arr['calendar']->day_start_hour+1),false);
	$time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
	$temp_ts = $cal_arr['calendar']->date_time->ts;
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
	$headerdata = "";
	$headerdata .="<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>
			<tr>
				<td class='tabSelected' style='border: 1px solid #666666;' align='center' width='10%' onClick='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\");'>
					".$mod_strings['LBL_ADD_EVENT']."
					<img src='".$cal_arr['IMAGE_PATH']."menuDnArrow.gif' style='padding-left: 5px;' border='0'>
				</td>
				<td align='center' width='65%'>";
	$headerdata .= getEventTodoInfo($cal_arr,'listcnt'); 
	$headerdata .= "	</td>
				<td align='right' width='25%'><b>View : </b>";
	$view_options = getEventViewOption($cal_arr);
	$headerdata .=$view_options."
				</td>
			</tr>
		</table>";
	echo $headerdata;	
}
/*To get View Combo box
*/
function getEventViewOption(& $cal)
{
	global $mod_strings;
	$view = "<select name='view' class='importBox' id='viewBox' onChange='fnRedirect(\"".$cal['calendar']->view."\",\"".$cal['calendar']->date_time->hour."\",\"".$cal['calendar']->date_time->day."\",\"".$cal['calendar']->date_time->month."\",\"".$cal['calendar']->date_time->year."\")'>";
	$view .="<option value='hourview' selected='selected'>".$mod_strings['LBL_HRVIEW']."</option>
		<option value='listview'>".$mod_strings['LBL_LISTVIEW']."</option>
		</select>";
	return $view;
}

/*link to previous day/week/month/year view
*/
function get_previous_cal(& $cal)
{
        global $mod_strings;
	$link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('prev')."'><img src='".$cal['IMAGE_PATH']."cal_prev_nav.gif' border='0'></a>";
	return $link;
}

/*link to next day/week/month view
*/
function get_next_cal(& $cal)
{
        global $mod_strings;
        $link = "<a href='index.php?action=index&module=Calendar&view=".$cal['calendar']->view."".$cal['calendar']->get_datechange_info('next')."'><img src='".$cal['IMAGE_PATH']."cal_next_nav.gif' border='0'></a>";
	return $link;

}

/*To display date info in calendar header
*/
function display_date($view,$date_time)
{
	if ($view == 'day')
        {
		//$label = $date_time->getdayofWeek()." ";
		$label = $date_time->get_Date()." ";
		$label .= $date_time->getmonthName()." ";
		$label .= $date_time->year;
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
		return $label;
        }

	elseif ($view == 'month')
	{
		$label = $date_time->getmonthName()." ";
		$label .= $date_time->year;
		return $label;
        }
	elseif ($view == 'year')
	{
		return $date_time->year;
        }

}

function dateCheck($slice_date)
{
	$today = date('Y-m-d');
	if($today == $slice_date)
	{
		return 'currDay';
	}
	else
	{
		return '';
	}
}

/*To get day/week/month events hourview
*/
function getHourView(& $view,$type = 'default' )
{
	if($view['view'] == 'day')
	{
		getDayViewLayout($view,$type);
	}
	elseif($view['view'] == 'week')
	{
		 getWeekViewLayout($view,$type);
	}
	elseif($view['view'] == 'month')
	{
		 getMonthViewLayout($view,$type);
	}
	elseif($view['view'] == 'year')
	{
		 getYearViewLayout($view,$type);
	}
	else
	{
		die("view:".$view['view']." is not defined");
	}
}

/*To get day/week/month events listview
*/
function getEventListView(& $cal,$mode='')
{
	if($cal['calendar']->view == 'day')
	{
		$start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
		$activity_list = getEventList($cal, $start_date, $end_date,$mode);
		if($mode != '')
		{
			return $activity_list;
		}
		constructEventListView($activity_list);
	}
	elseif($cal['calendar']->view == 'week')
	{
		$start_date = $cal['calendar']->slices[0];
		$end_date = $cal['calendar']->slices[6];
		$activity_list = getEventList($cal, $start_date, $end_date,$mode);
		if($mode != '')
                {
                        return $activity_list;
                }
		constructEventListView($activity_list);
	}
	elseif($cal['calendar']->view == 'month')
        {
		$start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
		$end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
		$activity_list = getEventList($cal, $start_date->get_formatted_date(), $end_date->get_formatted_date(),$mode);
		if($mode != '')
                {
                        return $activity_list;
                }
		constructEventListView($activity_list);
        }
	elseif($cal['calendar']->view == 'year')
        {
		$start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
		$end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
		$activity_list = getEventList($cal,$start_date->get_formatted_date(), $end_date->get_formatted_date(),$mode);
		if($mode != '')
                {
                        return $activity_list;
                }
                constructEventListView($activity_list);
	}
	else
        {
		die("view:".$cal['calendar']->view." is not defined");
        }
	
}


function getTodosListView($cal, $check='')
{
	if($cal['calendar']->view == 'day')
        {
                $start_date = $end_date = $cal['calendar']->date_time->get_formatted_date();
                $todo_list = getTodoList($cal, $start_date, $end_date,$check);
                if($check != '')
                {
                        return $todo_list;
                }
                return constructTodoListView($todo_list,$cal);
        }
	elseif($cal['calendar']->view == 'week')
        {
                $start_date = $cal['calendar']->slices[0];
                $end_date = $cal['calendar']->slices[6];
                $todo_list = getTodoList($cal, $start_date, $end_date,$check);
                if($check != '')
                {
                        return $todo_list;
                }
                return constructTodoListView($todo_list,$cal);
        }
        elseif($cal['calendar']->view == 'month')
        {
                $start_date = $cal['calendar']->date_time->getThismonthDaysbyIndex(0);
                $end_date = $cal['calendar']->date_time->getThismonthDaysbyIndex($cal['calendar']->date_time->daysinmonth - 1);
                $todo_list = getTodoList($cal, $start_date->get_formatted_date(), $end_date->get_formatted_date(),$check);
                if($check != '')
                {
                        return $todo_list;
                }
                return constructTodoListView($todo_list,$cal);
        }
	elseif($cal['calendar']->view == 'year')
        {
                $start_date = $cal['calendar']->date_time->getThisyearMonthsbyIndex(0);
                $end_date = $cal['calendar']->date_time->get_first_day_of_changed_year('increment');
                $todo_list = getTodoList($cal,$start_date->get_formatted_date(), $end_date->get_formatted_date(),$check);
                if($check != '')
                {
                        return $todo_list;
                }
                return constructTodoListView($todo_list,$cal);
        }
        else
        {
                die("view:".$cal['calendar']->view." is not defined");
        }
}

function getDayViewLayout(& $cal,$type)
{
	global $current_user,$app_strings;
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$dayview_layout = '';
	$dayview_layout .= '<br><!-- HOUR VIEW LAYER STARTS HERE -->
                <div id="hrView_'.$type.'">
                        <table border="0" cellpadding="10" cellspacing="0" width="98%">';
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
		//echo '<pre>';print_r($time_arr);echo '</pre>';
		$temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
		$dayview_layout .= '<tr>
					<td style="border-right: 1px solid rgb(102, 102, 102);" align="right" width="10%" onMouseOver="show(\''.$hour.''.$sub_str.'\')" onMouseOut="hide(\''.$hour.''.$sub_str.'\')">
						<span class="genHeaderBig">'.$hour.'</span>
						<span class="genHeaderGray">'.$sub_str.'</span><br>
						<div id="'.$hour.''.$sub_str.'" style="display: none;">
                                                        <a onClick="gshow(\'addEvent\',\'call\',\''.$temp_date.'\',\''.$temp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\')" href="javascript:void(0)"><img src="'.$cal['IMAGE_PATH'].'cal_add.jpg" border="0"></a>
                                                </div>
					</td>
					<td style="border-bottom: 1px solid rgb(204, 204, 204);" height="65">';
		
		$dayview_layout .= getdayEventLayer($cal,$cal['calendar']->slices[$i]);
		$dayview_layout .=' </td>		
				    </tr>';
	}
	$dayview_layout .= '<tr><td style="border-right: 1px solid rgb(102, 102, 102);">&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td colspan="2">&nbsp;</td></tr>
                                    </table>
			</div>
		</div>';
	if($type != 'ajax')
        {
		$dayview_layout .= getTodosListView($cal);
	}
	$dayview_layout .= '</td></tr></table></td></tr></table><br>';
	echo $dayview_layout;		
}

function getWeekViewLayout(& $cal,$type)
{
	global $current_user,$app_strings;
        $date_format = $current_user->date_format;
	$day_start_hour = $cal['calendar']->day_start_hour;
	$day_end_hour = $cal['calendar']->day_end_hour;
	$format = $cal['calendar']->hour_format;
	$weekview_layout = '';
        $weekview_layout .= '<br><!-- HOUR VIEW LAYER STARTS HERE -->
		<div id="hrView_'.$type.'" style = "padding:5px">
                        <table border="0" cellpadding="10" cellspacing="0" width="98%" class="calDayHour" style="background-color: #dadada">';
	for ($col=0;$col<=7;$col++)
        {
        	if($col==0)
                {
                	$weekview_layout .= '<tr>';
                	$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>&nbsp;</td>';
		}
		else
		{
			$cal['slice'] = $cal['calendar']->week_array[$cal['calendar']->slices[$col-1]];
			$date = $cal['calendar']->date_time->getThisweekDaysbyIndex($col-1);
			$day = $date->getdayofWeek_inshort();
			$weekview_layout .= '<td width=12% class="lvtCol" bgcolor="blue" valign=top>';
			$weekview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().'&'.$cal['slice']->start_time->get_date_str().'">';
			$weekview_layout .= $date->get_Date().' - '.$day;
			$weekview_layout .= "</a>";
			$weekview_layout .= '</td>';
		}
	}
	$weekview_layout .= '</tr></table>';
	$weekview_layout .= '<table border="0" cellpadding="10" cellspacing="1" width="98%" class="calDayHour" style="background-color: #dadada">';
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
			$temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));

			$weekview_layout .= '<td class="cellNormal" onMouseOver="show(\''.$temp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')" onMouseOut="hide(\''.$temp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'\')"  style="height: 40px;" bgcolor="white" valign="top" width="12%" align=right>';
			$weekview_layout .= '<div id="'.$temp_date.''.$time_arr['starthour'].''.$time_arr['startfmt'].'" style="display: none;">
						<a onClick="gshow(\'addEvent\',\'call\',\''.$temp_date.'\',\''.$temp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\')" href="javascript:void(0)"><img src="'.$cal['IMAGE_PATH'].'cal_add.jpg" border="0"></a>
                                                </div>';
			$weekview_layout .=getweekEventLayer($cal,$cal['calendar']->week_hour_slices[$count]);
			$weekview_layout .= '</td>';
			$count = $count+24;
		}
		$weekview_layout .= '</tr>';
	}
	$weekview_layout .= '</table></div>
			 </div>';
	if($type != 'ajax')
        {
		$weekview_layout .= getTodosListView($cal);
	}
	$weekview_layout .= '</td></tr></table></td></tr></table><br>';
	echo $weekview_layout;
		
}
	
function getMonthViewLayout(& $cal,$type)
{
	global $current_user,$app_strings;
	$date_format = $current_user->date_format;
	$count = 0;
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
	$monthview_layout .= '<br><!-- HOUR VIEW LAYER STARTS HERE -->
		<div id="hrView_'.$type.'" style = "padding:5px">
		<table class="calDayHour" style="background-color: rgb(218, 218, 218);" border="0" cellpadding="5" cellspacing="1" width="98%"><tr>';
	for ($i = 0; $i < 7; $i ++)
	{
		$first_row = $cal['calendar']->month_array[$cal['calendar']->slices[$i]];
		$weekday = $first_row->start_time->getdayofWeek();
		$monthview_layout .= '<td class="lvtCol" valign="top" width="14%">'.$weekday.'</td>';
	}
	$monthview_layout .= '</tr></table>';
	$monthview_layout .= '<table border=0 cellspacing=1 cellpadding=5 width=98% class="calDayHour" style="background-color: #dadada">';
	$cnt = 0;
	for ($i = 0; $i < $rows; $i ++)
	{
	        $monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
                {
			$monthview_layout .= '<td class="dvtCellLabel" width="14%">';
			$cal['slice'] = $cal['calendar']->month_array[$cal['calendar']->slices[$count]];
			$monthview_layout .= '<a href="index.php?module=Calendar&action=index&view='.$cal['slice']->getView().'&'.$cal['slice']->start_time->get_date_str().'">';
			if ($cal['slice']->start_time->getMonth() == $cal['calendar']->date_time->getMonth())
			{
				$monthview_layout .= $cal['slice']->start_time->get_Date();
			}
			$monthview_layout .= '</a></td>';
			$count++;
		}
		$monthview_layout .= '</tr>';
		$monthview_layout .= '<tr>';
		for ($j = 0; $j < 7; $j ++)
		{
			$temp_ts = $cal['calendar']->month_array[$cal['calendar']->slices[$cnt]]->start_time->ts;
			$temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
			$monthview_layout .= '<td onMouseOver="show(\''.$temp_date.''.$time_arr['starthour'].'\')" onMouseOut="hide(\''.$temp_date.''.$time_arr['starthour'].'\')" bgcolor="white" height="90" valign="top" width="200" align=right>';
			$monthview_layout .= '<div id="'.$temp_date.''.$time_arr['starthour'].'" style="display: none;">
                                                <a onClick="gshow(\'addEvent\',\'call\',\''.$temp_date.'\',\''.$temp_date.'\',\''.$time_arr['starthour'].'\',\''.$time_arr['startmin'].'\',\''.$time_arr['startfmt'].'\',\''.$time_arr['endhour'].'\',\''.$time_arr['endmin'].'\',\''.$time_arr['endfmt'].'\')" href="javascript:void(0)"><img src="'.$cal['IMAGE_PATH'].'cal_add.jpg" border="0"></a>
                                                </div>';
			$monthview_layout .= getmonthEventLayer($cal,$cal['calendar']->slices[$cnt]);
			$monthview_layout .= '</td>';
			$cnt++;

		}
		$monthview_layout .= '</tr>';
	}
	$monthview_layout .= '</table></div>
				</div>';
	if($type != 'ajax')
	{
		$monthview_layout .= getTodosListView($cal);
	}
        $monthview_layout .= '</td></tr></table></td></tr></table><br>';
	echo $monthview_layout;
		
}

function getYearViewLayout(& $cal,$type)
{
	global $mod_strings;
	$yearview_layout = '';
	$yearview_layout .= '<br><!-- HOUR VIEW LAYER STARTS HERE -->
                <div id="hrView_'.$type.'" style = "padding:5px">
		<table border="0" cellpadding="5" cellspacing="0" width="100%">';
	$count = 0;
	for($i=0;$i<4;$i++)
	{
		$yearview_layout .= '<tr>';
		for($j=0;$j<3;$j++)
        	{
			$cal['slice'] = $cal['calendar']->year_array[$cal['calendar']->slices[$count]];
			$yearview_layout .= '<td width="33%">
						<table class="month_table" border="0" cellpadding="0" cellspacing="3" width="98%">
							<tr>
								<td colspan="7" class="cal_Hdr">
									<a style="text-decoration: none;" href="index.php?module=Calendar&action=index&view=month&hour=0&day=1&month='.($count+1).'&year='.$cal['calendar']->date_time->year.'">
									'.$cal['slice']->start_time->month_inlong.'
									</a>
								</td>
							</tr><tr>';
			for($w=0;$w<7;$w++)
			{
				$yearview_layout .= '<th>'.$mod_strings['cal_weekdays_short'][$w].'</th>';
			}
			$yearview_layout .= '</tr>';
			list($_3rdyear,$_3rdmonth,$_3rddate) = explode("-",$cal['calendar']->month_day_slices[$count][35]);
			list($_2ndyear,$_2ndmonth,$_2nddate) = explode("-",$cal['calendar']->month_day_slices[$count][6]);
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
					$class = dateCheck($cal['calendar']->month_day_slices[$count][$cnt]);
					if($class != '')
					{
						$class = 'class="'.$class.'"';
					}
					else
					{
						$class = $event_class;
					}
					$date = $_1stdate + 0;
					$month = $_1stmonth + 0;
					$yearview_layout .= '<td '.$class.''.$event_class.'>';
					if($rows == 6 && $k==0)
					{
						list($tempyear,$tempmonth,$tempdate) = explode("-",$cal['calendar']->month_day_slices[$count][35+$mr]);
						if($tempmonth == $_2ndmonth)
							$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$tempdate.'&month='.$tempmonth.'&year='.$tempyear.'">'.$tempdate;
					}
					if($_1stmonth == $_2ndmonth)
					{
						$yearview_layout .= '<a href="index.php?module=Calendar&action=index&view=day&hour=0&day='.$date.'&month='.$month.'&year='.$_1styear.'">'.$date;
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
	$yearview_layout .= '</table></div>
				</div>';
	if($type != 'ajax')
	{
		$yearview_layout .= getTodosListView($cal);
	}
        $yearview_layout .= '</td></tr></table></td></tr></table><br>';
	echo $yearview_layout;
        
	
}


function getdayEventLayer(& $cal,$slice)
{
	global $mod_strings;
	$eventlayer = '';
	$arrow_img_name = '';
	$act = $cal['calendar']->day_slice[$slice]->activities;
	if(!empty($act))
	{
		for($i=0;$i<count($act);$i++)
		{
			$arrow_img_name = 'event'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i;
			$subject = $act[$i]->subject;
			$id = $act[$i]->record;
			if(strlen($subject)>25)
				$subject = substr($subject,0,25)."...";
			$start_time = $act[$i]->start_time->hour.':'.$act[$i]->start_time->minute;
			$format = $cal['calendar']->hour_format;
			$duration_hour = $act[$i]->duration_hour;
			$duration_min = $act[$i]->duration_minute;
			$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
			$start_hour = $st_end_time['starttime'];
			$end_hour = $st_end_time['endtime'];
			$account_name = $act[$i]->accountname;
			$color = $act[$i]->color;
			$image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
		$eventlayer .='<div class ="eventLay" id="'.$cal['calendar']->day_slice[$slice]->start_time->hour.'_'.$i.'">
					<table border="0" cellpadding="0" cellspacing="0" width="95%">
						<tr onMouseOver="show(\''.$arrow_img_name.'\');" onMouseOut="hide(\''.$arrow_img_name.'\');">
						<td align="left" width="5%"><img src="'.$image.'" align="right top"></td>
						<td align="left" width="85%"><span class="fontBold">'.$account_name.'</span><br>
							<b>'.$start_hour.'</b>&nbsp;,<span class="orgTab">'.$subject.'</span>&nbsp;
							<a href="index.php?action=DetailView&module=Activities&record='.$id.'&activity_mode=Events" class="webMnu">['.$mod_strings['LBL_VIEW'].']</a>
							<a href="index.php?action=EditView&module=Activities&record='.$id.'&activity_mode=Events" class="webMnu">['.$mod_strings['LBL_EDIT'].']</a>
					
						</td>
						<td align="right" width="5%">
							<div id="'.$arrow_img_name.'" style="display: none;">
								<img onClick="getcalAction(this,\'calAction\','.$id.',\''.$cal['view'].'\',\''.$cal['calendar']->date_time->hour.'\',\''.$cal['calendar']->date_time->day.'\',\''.$cal['calendar']->date_time->month.'\',\''.$cal['calendar']->date_time->year.'\');" onMouseout="fninvsh(\'calAction\')" src="'.$cal['IMAGE_PATH'].'cal_event.jpg" border="0">
							</div>
						</td>
						</tr>
					</table>
				</div><br>';
		}
		return $eventlayer;
	}
}

function getweekEventLayer(& $cal,$slice)
{
	global $mod_strings;
        $eventlayer = '';
        $arrow_img_name = '';
	$act = $cal['calendar']->week_slice[$slice]->activities;
	if(!empty($act))
        {
		for($i=0;$i<count($act);$i++)
                {
			$arrow_img_name = 'weekevent'.$cal['calendar']->week_slice[$slice]->start_time->hour.'_'.$i;
			$id = $act[$i]->record;
                        $subject = $act[$i]->subject;
			$format = $cal['calendar']->hour_format;
                        $duration_hour = $act[$i]->duration_hour;
                        $duration_min = $act[$i]->duration_minute;
			$start_time = $act[$i]->start_time->hour.':'.$act[$i]->start_time->minute;
                        $st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
			$start_hour = $st_end_time['starttime'];
                        $end_hour = $st_end_time['endtime'];
                        $account_name = $act[$i]->accountname;
                        $image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
                        $color = $act[$i]->color;
			$eventlayer .='<div id="'.$cal['calendar']->week_slice[$slice]->start_time->hour.'_'.$i.'">
                                        <img src="'.$image.'" valign="absmiddle"><a href="index.php?action=DetailView&module=Activities&record='.$id.'&activity_mode=Events" style="color:#DBF3FD;">&nbsp;<b>'.$start_hour.'</b>&nbsp;'.$subject.'</a>&nbsp;
                                </div><br>';
                }
		return $eventlayer;
	}
			
}

function getmonthEventLayer(& $cal,$slice)
{
	global $mod_strings;
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
			$format = $cal['calendar']->hour_format;
                        $duration_hour = $act[$i]->duration_hour;
                        $duration_min = $act[$i]->duration_minute;
                        $st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
                        $start_hour = $st_end_time['starttime'];
                        $end_hour = $st_end_time['endtime'];
                        $account_name = $act[$i]->accountname;
                        $image = $cal['IMAGE_PATH'].''.$act[$i]->image_name;
			$color = $act[$i]->color;
			$eventlayer .='<div id="'.$cal['calendar']->month_array[$slice]->start_time->hour.'_'.$i.'">
                                        <img src="'.$image.'" valign="absmiddle"><a href="index.php?action=DetailView&module=Activities&record='.$id.'&activity_mode=Events" style="color:#DBF3FD;">&nbsp;<b>'.$start_hour.'</b>&nbsp;'.$subject.'</a>&nbsp;
                                </div><br>';
                }
		if($remin_list != null)
		{
			$eventlayer .='<div valign=bottom align=right width=10%>
					<a href="index.php?module=Calendar&action=index&view='.$cal['calendar']->month_array[$slice]->getView().'&'.$cal['calendar']->month_array[$slice]->start_time->get_date_str().'" class="webMnu">
					+'.$remin_list.'&nbsp;'.$mod_strings['LBL_MORE'].'</a></div>';
		}
                return $eventlayer;
        }

}


function getEventList(& $calendar,$start_date,$end_date,$info='')
{
	$Entries = Array();
	global $adb,$current_user,$mod_strings;
	
	$query = "SELECT cntactivityrel.contactid, activity.*
		FROM activity
		INNER JOIN crmentity
			ON crmentity.crmid = activity.activityid
		LEFT JOIN cntactivityrel
			ON cntactivityrel.activityid = activity.activityid
		LEFT OUTER JOIN recurringevents
			ON recurringevents.activityid = activity.activityid
		WHERE crmentity.deleted = 0
			AND (activity.activitytype = 'Meeting' OR activity.activitytype = 'Call')
			AND (activity.date_start BETWEEN '".$start_date."' AND '".$end_date."'
				OR recurringevents.recurringdate BETWEEN '".$start_date."' AND '".$end_date."')";
	if($info != '')
	{
		$pending_query = $query." AND (activity.eventstatus = 'Planned')
			AND crmentity.smownerid = ".$current_user->id."
		ORDER BY activity.date_start,activity.time_start ASC";
		$res = $adb->query($pending_query);
		$pending_rows = $adb->num_rows($res);
	}
	$query .= " AND crmentity.smownerid = ".$current_user->id."
		ORDER BY activity.date_start,activity.time_start ASC";

	$result = $adb->query($query);
	$rows = $adb->num_rows($result);
	if($info != '')
        {
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
		$format = $calendar['calendar']->hour_format;
		$st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hour,$duration_min);
		$element['starttime'] = $st_end_time['starttime'];
                $element['endtime'] = $st_end_time['endtime'];
		$contact_id = $adb->query_result($result,$i,"contactid");
		$id = $adb->query_result($result,$i,"activityid");
		$subject = $adb->query_result($result,$i,"subject");
                if(strlen($subject)>25)
	                $subject = substr($subject,0,25)."...";
		if($contact_id != '')
		{
			$contactname = getContactName($contact_id);
			$contact_data = "<b>".$contactname."</b>,";
		}
		$more_link = "<a href='index.php?action=DetailView&module=Activities&record=".$id."&activity_mode=Events' class='webMnu'>[".$mod_strings['LBL_MORE']."...]</a>";
		$type = $adb->query_result($result,$i,"activitytype");
		if($type == 'Call')
			$image_tag = "<img src='".$calendar['IMAGE_PATH']."Calls.gif' align='middle'>&nbsp;".$type;
		if($type == 'Meeting')
			$image_tag = "<img src='".$calendar['IMAGE_PATH']."Meetings.gif' align='middle'>&nbsp;".$type;
        	$element['eventtype'] = $image_tag;
		$element['eventdetail'] = $contact_data." ".$subject."&nbsp;".$more_link;
	        $element['action'] = "<img onClick='getcalAction(this,\"calAction\");' onMouseout='fninvsh(\"calAction\")' src='".$calendar['IMAGE_PATH']."cal_event.jpg' border='0'>";
        	$element['status'] = $adb->query_result($result,$i,"eventstatus");
	$Entries[] = $element;
	}
	return $Entries;
}

function getTodoList(& $calendar,$start_date,$end_date,$info='')
{
        $Entries = Array();
        global $adb,$current_user,$mod_strings;

        $query = "SELECT cntactivityrel.contactid, activity.*
                FROM activity
                INNER JOIN crmentity
                        ON crmentity.crmid = activity.activityid
                LEFT JOIN cntactivityrel
                        ON cntactivityrel.activityid = activity.activityid
                WHERE crmentity.deleted = 0
                        AND activity.activitytype = 'Task'
                        AND (activity.date_start BETWEEN '".$start_date."' AND '".$end_date."')";
        if($info != '')
        {
                $pending_query = $query." AND (activity.status != 'Completed')
                        AND crmentity.smownerid = ".$current_user->id."
                ORDER BY activity.date_start,activity.time_start ASC";
                $res = $adb->query($pending_query);
                $pending_rows = $adb->num_rows($res);
        }
        $query .= " AND crmentity.smownerid = ".$current_user->id."
                ORDER BY activity.date_start,activity.time_start ASC";

        $result = $adb->query($query);
        $rows = $adb->num_rows($result);
        if($info != '')
        {
                return Array('totaltodo'=>$rows,'pendingtodo'=>$pending_rows);
        }
	for($i=0;$i<$rows;$i++)
        {
                $element = Array();
                $element['no'] = $i+1;
                $more_link = "";
                $start_time = $adb->query_result($result,$i,"time_start");
                $format = $calendar['calendar']->hour_format;
                $st_end_time = convertStEdTime2UserSelectedFmt($format,$start_time);
                $element['starttime'] = $st_end_time['starttime'];
                $id = $adb->query_result($result,$i,"activityid");
                $subject = $adb->query_result($result,$i,"subject");
		$more_link = "<a href='index.php?action=DetailView&module=Activities&record=".$id."&activity_mode=Task' class='webMnu'>".$subject."</a>";
		$element['tododetail'] = $more_link;
		$element['status'] = $adb->query_result($result,$i,"status");
                $element['action'] = "<img onClick='getcalAction(this,\"calAction\");' onMouseout='fninvsh(\"calAction\")' src='".$calendar['IMAGE_PATH']."cal_event.jpg' border='0'>";
		$Entries[] = $element;
	}
	return $Entries;
}

function getEventTodoInfo(& $cal, $mode)
{
	global $mod_strings;
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
	
	return $event_todo_info;
}

function constructEventListView($entry_list)
{
	global $mod_strings;
	$list_view = "";
	$header = Array('0'=>'#',
                        '1'=>$mod_strings['LBL_APP_START_TIME'],
                        '2'=>$mod_strings['LBL_APP_END_TIME'],
                        '3'=>$mod_strings['LBL_EVENTTYPE'],
                        '4'=>$mod_strings['LBL_EVTDTL'],
                        '5'=>$mod_strings['LBL_ACTION'],
                        '6'=>$mod_strings['LBL_CURSTATUS'],
                        );
        $header_width = Array('0'=>'5',
                              '1'=>'10',
                              '2'=>'10',
                              '3'=>'10',
                              '4'=>'40',
                              '5'=>'10',
                              '6'=>'15',
                             );
	$list_view .="<br><table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        <tr>";
	$header_rows = count($header);
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td class='lvtCol' width='".$header_width[$i]."'>".$header[$i]."</td>";
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
				$list_view .="<td>".$entry."</td>";
			}
			$list_view .="</tr>";
		}
	}
	else
	{
		$list_view .="<tr style='height: 25px;' bgcolor='white'>";
                	$list_view .="<td colspan='".$header_rows."'><i>None Scheduled</i></td>";
                $list_view .="</tr>";
	}
	$list_view .="</table>";
	echo $list_view;
}

function constructTodoListView($todo_list,$cal)
{
	global $mod_strings;
        global $current_user,$app_strings;
        $date_format = $current_user->date_format;
        $format = $cal['calendar']->hour_format;
        $hour_startat = convertTime2UserSelectedFmt($format,$cal['calendar']->day_start_hour,false);
        $hour_endat = convertTime2UserSelectedFmt($format,($cal['calendar']->day_start_hour+1),false);
        $time_arr = getaddEventPopupTime($hour_startat,$hour_endat,$format);
        $temp_ts = $cal['calendar']->date_time->ts;
        $temp_date = (($date_format == 'dd-mm-yyyy')?(date('d-m-Y',$temp_ts)):(($date_format== 'mm-dd-yyyy')?(date('m-d-Y',$temp_ts)):(($date_format == 'yyyy-mm-dd')?(date('Y-m-d', $temp_ts)):(''))));
        $list_view = "";
        $header = Array('0'=>'#',
                        '1'=>$mod_strings['LBL_TIME'],
                        '2'=>$mod_strings['LBL_TODO'],
                        '3'=>$mod_strings['LBL_STATUS'],
                        '4'=>$mod_strings['LBL_ACTION'],
                       );
        $header_width = Array('0'=>'5%',
                              '1'=>'10%',
                              '2'=>'65%',
                              '3'=>'10%',
                              '4'=>'10%',
                             );
	$list_view .="<div id='mnuTab2' style='background-color: rgb(255, 255, 215); display:none;'>
		<table align='center' border='0' cellpadding='5' cellspacing='0' width='98%'>
			<tr><td colspan='3'>&nbsp;</td></tr>
			<tr>
				<td class='tabSelected' onClick='fnAddEvent(this,\"addEventDropDown\",\"".$temp_date."\",\"".$temp_date."\",\"".$time_arr['starthour']."\",\"".$time_arr['startmin']."\",\"".$time_arr['startfmt']."\",\"".$time_arr['endhour']."\",\"".$time_arr['endmin']."\",\"".$time_arr['endfmt']."\");'style='border: 1px solid #666666;' align='center' width='10%'>
                                        ".$mod_strings['LBL_ADD_EVENT']."
                                        <img src='".$cal['IMAGE_PATH']."menuDnArrow.gif' style='padding-left: 5px;' border='0'>                                                                                                                         </td>
				<td align='center' width='65%'>&nbsp;</td>
				<td align='right' width='25%'>&nbsp;</td>
			</tr>
		</table>

			<br><table style='background-color: rgb(204, 204, 204);' class='small' align='center' border='0' cellpadding='5' cellspacing='1' width='98%'>
                        <tr>";
        $header_rows = count($header);
        for($i=0;$i<$header_rows;$i++)
        {
                $list_view .="<td class='lvtCol' width='".$header_width[$i]."'>".$header[$i]."</td>";
        }
        $list_view .="</tr>";
	$rows = count($todo_list);
        if($rows != 0)
        {
                for($i=0;$i<count($todo_list);$i++)
                {
                        $list_view .="<tr bgcolor='#ffffd7'>";
                        foreach($todo_list[$i] as $key=>$entry)
                        {
                                $list_view .="<td>".$entry."</td>";
                        }
                        $list_view .="</tr>";
                }
        }
        else
        {
                $list_view .="<tr style='height: 25px;' bgcolor='white'>";
                        $list_view .="<td colspan='".$header_rows."'><i>None Scheduled</i></td>";
                $list_view .="</tr>";
        }
        $list_view .="</table><br></div>";
        return $list_view;
}

function convertTime2UserSelectedFmt($format,$time,$format_check)
{
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
		return $hour;
	}
	else
        {
                $hour = $time;
		if($hour <= 9 && strlen(trim($hour)) < 2)
                                $hour = "0".$hour;
		$hour = $hour.":00";
		return $hour;
	}
}

function convertStEdTime2UserSelectedFmt($format,$start_time,$duration_hr='',$duration_min='')
{
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
	return $return_data;


}

		
?>
