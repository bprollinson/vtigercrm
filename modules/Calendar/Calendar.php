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

require_once('modules/Calendar/Appointment.php');
require_once('modules/Calendar/Date.php');
class Calendar 
{
	var $view='day';
	var $date_time;
	var $hour_format = 'am/pm';
	var $day_slice;
	var $week_slice;
	var $week_array;
	var $month_array;
	var $week_hour_slices = Array();
	var $slices = Array();
	/* for dayview */
	var $day_start_hour=0;
	var $day_end_hour=23;
	var $sharedusers=Array();
	/*
	constructor
	*/
	function Calendar($view='',$data=Array())
	{
		$this->view = $view;
		$this->date_time = new DateTime($data,true);
		$this->constructLayout();
	}
	/**
	 * Function to get calendarview Label
	 * @param string  $view   - calendarview
	 * return string  - calendarview Label 
	*/
	function getCalendarView($view)
	{
		switch($view)
                {
			case 'day':
				return "DAY";
			case 'week':
				return "WEEK";
			case 'month':
				return "MON";
			case 'year':
				return "YEAR";
		}
	}

	/**
	 * Function to set values for calendar object depends on calendar view
	*/
	function constructLayout()
	{
		global $current_user;
		switch($this->view)
		{
			case 'day':
				for($i=0;$i<=23;$i++)
				{
					$layout = new Layout('hour',$this->date_time->getTodayDatetimebyIndex($i));
					$this->day_slice[$layout->start_time->get_formatted_date().':'.$layout->start_time->z_hour] = $layout;
					array_push($this->slices,  $layout->start_time->get_formatted_date().":".$layout->start_time->z_hour);
				}
				break;
			case 'week':
				$weekview_days = 7;
				for($i=0;$i<$weekview_days;$i++)
				{
					$layout = new Layout('day',$this->date_time->getThisweekDaysbyIndex($i));
					$this->week_array[$layout->start_time->get_formatted_date()] = $layout;
					for($h=0;$h<=23;$h++)
					{
                                        	$hour_list = new Layout('hour',$this->date_time->getTodayDatetimebyIndex($h,$layout->start_time->day,$layout->start_time->month,$layout->start_time->year));
						$this->week_slice[$layout->start_time->get_formatted_date().':'.$hour_list->start_time->z_hour] = $hour_list;
						array_push($this->week_hour_slices,  $layout->start_time->get_formatted_date().":".$hour_list->start_time->z_hour);
					}
					array_push($this->slices,  $layout->start_time->get_formatted_date());
					
				}
				break;
			case 'month':
				$monthview_days = $this->date_time->daysinmonth;
				$firstday_of_month = $this->date_time->getThismonthDaysbyIndex(0);
                                $num_of_prev_days = $firstday_of_month->dayofweek;
				for($i=0;$i<42;$i++)
                                {
					$layout = new Layout('day',$this->date_time->getThismonthDaysbyIndex($i-$num_of_prev_days));
					$this->month_array[$layout->start_time->get_formatted_date()] = $layout;
					array_push($this->slices,  $layout->start_time->get_formatted_date());
				}
				break;
			case 'year':
				$this->month_day_slices = Array();
				for($i=0;$i<12;$i++)
				{
					$layout = new Layout('month',$this->date_time->getThisyearMonthsbyIndex($i));
					$this->year_array[$layout->start_time->z_month] = $layout;
					$daysinmonth = $this->year_array[$layout->start_time->z_month]->start_time->daysinmonth;
					$firstday_of_month = $this->year_array[$layout->start_time->z_month]->start_time->getThismonthDaysbyIndex(0);
					$noof_prevdays = $firstday_of_month->dayofweek;
					$year_monthdays = Array();
					for($m=0;$m<42;$m++)
                                        {
                                                $mday_list = new Layout('day',$this->year_array[$layout->start_time->z_month]->start_time->getThismonthDaysbyIndex($m-$noof_prevdays));
						$year_monthdays[] = $mday_list->start_time->get_formatted_date(); 
                                        }
					$this->month_day_slices[$i] = $year_monthdays;
					array_push($this->slices,  $layout->start_time->z_month);
				}
				break;
			case 'share':
		}
	}

	/**
	 * Function to get date info depends on calendarview
	 * @param  string   $type  - string 'increment' or 'decrment'
	 */

	function get_datechange_info($type)
	{
		if($type == 'next')
			$mode = 'increment';	
		if($type == 'prev')
			$mode = 'decrment';
		switch($this->view)
		{
			case 'day':
				$day = $this->date_time->get_changed_day($mode);
				break;
			case 'week':
				$day = $this->date_time->get_first_day_of_changed_week($mode);
				break;
			case 'month':
				$day = $this->date_time->get_first_day_of_changed_month($mode);
				break;
			case 'year':
				$day = $this->date_time->get_first_day_of_changed_year($mode);
				break;
			default:
				return "view is not supported";
		}
		return $day->get_date_str();
	}
	
	/**
	 * Function to get activities
	 * @param  array $current_user  - user data
	 * @param  string $free_busy    - 
	 */
	function add_Activities($current_user,$free_busy='')
	{
		if($current_user->hour_format == '')
			$this->hour_format = 'am/pm';
		else
			$this->hour_format = $current_user->hour_format;
		if(isset($current_user->start_hour) && $current_user->start_hour !='')
		{
			list($sthour,$stmin)= explode(":",$current_user->start_hour);
			$hr = $sthour+0;
			list($endhour,$endmin)=explode(":",$current_user->end_hour);
			$this->day_start_hour=$hr;
			$this->day_end_hour=$endhour;
		}
		if ( $this->view == 'week')
		{
			$start_datetime = $this->date_time->getThisweekDaysbyIndex(0);
			$end_datetime = $this->date_time->get_first_day_of_changed_week('increment');
                } elseif($this->view == 'month') {
			$start_datetime = $this->date_time->getThismonthDaysbyIndex(0);
			$end_datetime = $this->date_time->get_first_day_of_changed_month('increment');
		} elseif($this->view == 'year'){
			$start_datetime = $this->date_time->getThisyearMonthsbyIndex(0);
			$end_datetime = $this->date_time->get_first_day_of_changed_year('increment');
		}else {
			$start_datetime = $this->date_time;
                        $end_datetime = $this->date_time->get_changed_day('increment');
                }
		
		$activities = Array();
		$activities = Appointment::readAppointment($current_user->id,$start_datetime,$end_datetime,$this->view);
		if(!empty($activities))
		{
			foreach($activities as $key=>$value)
			{
				if($this->view == 'day')
				{
					array_push($this->day_slice[$value->formatted_datetime]->activities, $value);
				}
				elseif($this->view == 'week')
				{
					array_push($this->week_slice[$value->formatted_datetime]->activities, $value);
				}
				elseif($this->view == 'month')
				{
					array_push($this->month_array[$value->formatted_datetime]->activities,$value);
				}
				elseif($this->view == 'year')
				{
					array_push($this->year_array[$value->formatted_datetime]->activities,$value);
				}
				else
					die("view:".$this->view." is not defined");

			}
		}
		
	}
	

}

class Layout
{
	var $view = 'day';
	var $start_time;
        var $end_time;
	var $activities = Array();
	
	/**
	* Constructor for Layout class
	* @param  string   $view - calendarview
	* @param  string   $time - time string 
	*/

	function Layout($view,$time)
        {
                $this->view = $view;
                $this->start_time = $time;
		if ( $view == 'month')
			 $this->end_time = $this->start_time->getMonthendtime();
                if ( $view == 'day')
                        $this->end_time = $this->start_time->getDayendtime();
                if ( $view == 'hour')
                        $this->end_time = $this->start_time->getHourendtime();
        }

	/**
	* Function to get view 
	* return currentview
	*/

	function getView()
	{
		return $this->view;
	}
}
?>
