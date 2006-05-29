/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

function DisableSharing()
{

        x = document.SharedList.selected_id.length;
        idstring = "";
        xx = 0;
        if ( x == undefined)
        {

                if (document.SharedList.selected_id.checked)
                {
                        document.SharedList.idlist.value=document.SharedList.selected_id.value;
                }
                else
                {
                        alert("Please select atleast one user");
                        return false;
                }
        }
        else
        {
                for(i = 0; i < x ; i++)
                {
                        if(document.SharedList.selected_id[i].checked)
                        {
                                idstring = document.SharedList.selected_id[i].value +";"+idstring
                        xx++
                        }
                }
                if (xx != 0)
                {
                        document.SharedList.idlist.value=idstring;
                }
                else
                {
                        alert("Please select atleast one user");
                        return false;
                }
        }
        if(confirm("Are you sure you want to disable sharing for selected "+xx+" user(s) ?"))
        {
                document.SharedList.action="index.php?module=Calendar&action=disable_sharing&return_module=Calendar&return_action=calendar_share";
        }
        else
        {
                return false;
        }
}



function showhide(argg)
{
	var x=document.getElementById(argg).style;
	if (x.display=="none") 
	{
		x.display="block"
	
	}
	else {
			x.display="none"
		  }
}


function showhideRepeat(argg1,argg2)
{
	var x=document.getElementById(argg2).style;
	var y=document.getElementById(argg1).checked;
	
	if (y)
	{
		x.display="block";
	}
	else {
		x.display="none";
	}
	
}



function gshow(argg1,type,startdate,enddate,starthr,startmin,startfmt,endhr,endmin,endfmt)
{
	var y=document.getElementById(argg1).style;
	
	if (y.display=="none") 
	{
		if(type == 'call' || type == 'meeting')
		{
			if(type == 'call')
	                        document.appSave.activitytype[0].checked = true;
	                if(type == 'meeting')
        	                document.appSave.activitytype[1].checked = true;

			document.appSave.date_start.value = startdate;
			document.appSave.starthr.value = starthr;
			document.appSave.startmin.value = startmin;
			document.appSave.startfmt.value = startfmt;
			document.appSave.endhr.value = endhr;
			document.appSave.endmin.value = endmin;
			document.appSave.endfmt.value = endfmt;
		}
		if(type == 'todo')
		{
			document.createTodo.task_date_start.value = startdate;
			document.createTodo.starthr.value = starthr;
                        document.createTodo.startmin.value = startmin;
                        document.createTodo.startfmt.value = startfmt;
		}
		y.display="block";
	}
}

function ghide(argg2)
{
	var z=document.getElementById(argg2).style;
	if (z.display=="block" ) 
	{
		z.display="none"
	
	}
}

 function moveMe(arg1) {
	var posx = 0;
	var posy = 0;
	var e=document.getElementById(arg1);
	
	if (!e) var e = window.event;
	
	if (e.pageX || e.pageY)
	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY)
	{
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}
 }

function switchClass(myModule,toStatus) {
	var x=document.getElementById(myModule);
	if (toStatus=="on") {
		x.className="dvtSelectedCell";
		}
	if (toStatus=="off") {
		x.className="dvtUnSelectedCell";
		}
		
}

function enableCalstarttime()
{
	if(document.SharingForm.sttime_check.checked == true)
		document.SharingForm.start_hour.disabled = false;
	else	
		document.SharingForm.start_hour.disabled = true;
}

function check_form()
{
	formSelectColumnString('inviteesid');
        if(document.appSave.subject.value == "")
        {
                alert("Missing Event Name");
                document.appSave.subject.focus()
                return false;
        }
        else
        {
		if(document.appSave.remindercheck.checked == true)
			document.appSave.set_reminder.value = 'Yes';
		else
			document.appSave.set_reminder.value = 'No';
		if(document.appSave.recurringcheck.checked == false)
		{
			document.appSave.recurringtype.value = '--None--';
		}
		else
		{
			document.appSave.recurringtype.value = document.appSave.repeat_option.value;
		}
		starthour = document.appSave.starthr.value;
		startmin  = document.appSave.startmin.value;
		startformat = document.appSave.startfmt.value;
		endhour = document.appSave.endhr.value;
                endmin  = document.appSave.endmin.value;
                endformat = document.appSave.endfmt.value;
		if(startformat != '')
		{
			if(startformat == 'pm')
			{
				starthour = eval(starthour) + 12;
				startmin  = startmin;
			}
			else
			{
				starthour = starthour;
				startmin  = startmin;
			}
		}
		if(endformat != '')
		{
			if(endformat == 'pm')
                        {
                                endhour = eval(endhour) + 12;
				endmin = endmin;
                        }
			else
			{
				endhour = endhour;
				endmin = endmin;
			}
		}
		if((eval(endhour)*60+eval(endmin)) < (eval(starthour)*60+eval(startmin)))
		{
			alert("End Time should be greater than Start Time ");
	                document.appSave.endhr.focus();
        	        return false;
		}
			
		durationinmin = (eval(endhour)*60+eval(endmin)) - (eval(starthour)*60+eval(startmin));
		if(durationinmin >= 60)
		{
			hour = durationinmin/60;
			minute = durationinmin%60;
		}
		else
		{
			hour = 0;
			minute = durationinmin;
		}
		document.appSave.duration_hours.value = hour;
		document.appSave.duration_minutes.value = minute;
		document.appSave.time_start.value = starthour+':'+startmin;
                return true;
        }
}

function task_check_form()
{
	starthour = document.createTodo.starthr.value;
	startmin  = document.createTodo.startmin.value;
        startformat = document.createTodo.startfmt.value;
	if(startformat != '')
	{
        	if(startformat == 'pm')
                {
                	starthour = eval(starthour) + 12;
                        startmin  = startmin;
                }
                else
                {
                	starthour = starthour;
                        startmin  = startmin;
                }
        }
	document.createTodo.task_time_start.value = starthour+':'+startmin;
}


var moveupLinkObj,moveupDisabledObj,movedownLinkObj,movedownDisabledObj;
function setObjects()
{
        availListObj=getObj("availableusers")
        selectedColumnsObj=getObj("selectedusers")

}



function addColumn()
{
        var selectlength=selectedColumnsObj.length
        var availlength=availListObj.length
        var s=0
        for (i=0;i<selectlength;i++)
        {
                selectedColumnsObj.options[i].selected=false
        }
        for (i=0;i<availlength;i++)
        {
                if (availListObj.options[s].selected==true)
                {
                        for (j=0;j<selectlength;j++)
                        {
                                if (selectedColumnsObj.options[j].value==availListObj.options[s].value)
                                {
                                        var rowFound=true
                                        var existingObj=selectedColumnsObj.options[j]
                                        breaK;
                                }
                        }
                        if (rowFound!=true)
                        {
                                var newColObj=document.createElement("OPTION")
                                        newColObj.value=availListObj.options[s].value
                                        if (browser_ie) newColObj.innerText=availListObj.options[s].innerText
                                        else if (browser_nn4 || browser_nn6) newColObj.text=availListObj.options[s].text
                                                selectedColumnsObj.appendChild(newColObj)
                                        availListObj.removeChild(availListObj.options[s])
                                        newColObj.selected=true
                                        rowFound=false
                        }
                        else
                        {
                                existingObj.selected=true
                        }
                }
		else
                        s++
        }
}

function delColumn()
{
        var selectlength=selectedColumnsObj.length
        var availlength=availListObj.length
        var s=0
        for (i=0;i<availlength;i++)
        {
                availListObj.options[i].selected=false
        }
        for (i=0;i<selectlength;i++)
        {
                if (selectedColumnsObj.options[s].selected==true)
                {
                        for (j=0;j<availlength;j++)
                        {
                                if (availListObj.options[j].value==selectedColumnsObj.options[s].value)
                                {
                                        var rowFound=true
                                        var existingObj=availListObj.options[j]
                                        breaK;
                                }
                        }

                        if (rowFound!=true)
                        {
                                var newColObj=document.createElement("OPTION")
                                        newColObj.value=selectedColumnsObj.options[s].value
                                        if (browser_ie) newColObj.innerText=selectedColumnsObj.options[s].innerText
                                        else if (browser_nn4 || browser_nn6) newColObj.text=selectedColumnsObj.options[s].text
                                                availListObj.appendChild(newColObj)
                                        selectedColumnsObj.removeChild(selectedColumnsObj.options[s])
                                        newColObj.selected=true
                                        rowFound=false
                        }
                        else
                        {
                                existingObj.selected=true
                        }
                }
		else
                        s++
        }
}

function formSelectColumnString(usr)
{
	usr_id = document.getElementById(usr);
	var selectedColStr = "";
        for (i=0;i<selectedColumnsObj.options.length;i++)
        {
        	selectedColStr += selectedColumnsObj.options[i].value + ";";
        }
	usr_id.value = selectedColStr;
}

function ajaxCalSaveResponse(response)
{
	document.getElementById("hrView_default").style.display = "none";
        document.getElementById("toggleDiv").innerHTML=response.responseText;
}

function fnRedirect(view,hour,day,month,year){
	var ajaxObj = new VtigerAjax(ajaxCalSaveResponse);
	var tagName =  document.getElementById('viewBox');
	var OptionData = tagName.options[tagName.selectedIndex].value;
	if(OptionData == 'hourview'){
		var urlstring ="module=Calendar&action=CalendarAjax&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&type="+OptionData+"&parenttab=My Home Page&ajax=true";
	}
	else if(OptionData == 'listview'){
		var urlstring ="module=Calendar&action=CalendarAjax&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&type="+OptionData+"&parenttab=My Home Page&ajax=true";
	}
	ajaxObj.process("index.php?",urlstring);
}

function fnAddEvent(obj,CurrObj,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt){
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side  + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	document.getElementById("addcall").href="javascript:gshow('addEvent','call','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"');fnRemoveEvent();";
	document.getElementById("addmeeting").href="javascript:gshow('addEvent','meeting','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"');fnRemoveEvent();";
	document.getElementById("addtodo").href="javascript:gshow('createTodo','todo','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"');fnRemoveEvent();";
	
}
	
function fnRemoveEvent(){
	var tagName = document.getElementById('addEventDropDown').style.display= 'none';
}

function fnShowEvent(){
		var tagName = document.getElementById('addEventDropDown').style.display= 'block';
}

function ajaxMiniCalSaveResponse(response)
{
        document.getElementById("miniCal").innerHTML=response.responseText;
}

function getMiniCal()
{
	var ajaxObj = new VtigerAjax(ajaxMiniCalSaveResponse);
	var urlstring ="module=Calendar&action=CalendarAjax&type=minical&parenttab=My Home Page&ajax=true";
	ajaxObj.process("index.php?",urlstring);
	
}

function getCalSettings()
{
	 var ajaxObj = new VtigerAjax(ajaxCalSettingsSaveResponse);
	 var urlstring ="module=Calendar&action=CalendarAjax&type=settings&parenttab=My Home Page&ajax=true";
	 ajaxObj.process("index.php?",urlstring);
}

function ajaxCalSettingsSaveResponse(response)
{
        document.getElementById("calSettings").innerHTML=response.responseText;
}

function getcalAction(obj,Lay,id,view,hour,day,month,year,type){
    var tagName = document.getElementById(Lay);
    var leftSide = findPosX(obj);
    var topSide = findPosY(obj);
    var maxW = tagName.style.width;
    var widthM = maxW.substring(0,maxW.length-2);
    var getVal = eval(leftSide) + eval(widthM);
    if(getVal  > window.innerWidth ){
        leftSide = eval(leftSide) - eval(widthM);
        tagName.style.left = leftSide + 'px';
    }
    else
        tagName.style.left= leftSide + 'px';
    tagName.style.top= topSide + 'px';
    tagName.style.display = 'block';
    tagName.style.visibility = "visible";
    if(type == 'event')
    {
	var heldstatus = "eventstatus=Held";
	var notheldstatus = "eventstatus=Not Held";
        var activity_mode = "Events";
    }
    if(type == 'todo')
    {
	var heldstatus = "status=Completed";
        var notheldstatus = "status=Deferred";
	var activity_mode = "Task";
    }
	
    document.getElementById("complete").href="index.php?return_module=Calendar&return_action=index&action=Save&module=Activities&record="+id+"&change_status=true&"+heldstatus+"&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&parenttab=My Home Page";
    document.getElementById("pending").href="index.php?return_module=Calendar&return_action=index&action=Save&module=Activities&record="+id+"&change_status=true&"+notheldstatus+"&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&parenttab=My Home Page";
    document.getElementById("postpone").href="index.php?action=EditView&module=Activities&record="+id+"&activity_mode="+activity_mode;
    document.getElementById("actdelete").href="index.php?return_module=Calendar&return_action=index&action=massdelete&module=Users&idlist="+id+"&view="+view+"&hour="+hour+"&day="+day+"&month="+month+"&year="+year+"&parenttab=My Home Page";
    document.getElementById("changeowner").href="javascript:fnvshobj(this,'act_changeowner');";

}

