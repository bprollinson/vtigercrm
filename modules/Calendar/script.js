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
                        alert("Please select at least one user");
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
                        alert("Please select at least one user");
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



function gshow(argg1,type,startdate,enddate,starthr,startmin,startfmt,endhr,endmin,endfmt,viewOption,subtab)
{
	var y=document.getElementById(argg1).style;
	
	if (y.display=="none") 
	{
		if(type == 'call' || type == 'meeting')
		{
			if(type == 'call')
	                        document.EditView.activitytype[0].checked = true;
	                if(type == 'meeting')
        	                document.EditView.activitytype[1].checked = true;

			document.EditView.date_start.value = startdate;
			document.EditView.due_date.value = enddate;
			document.EditView.starthr.value = starthr;
			document.EditView.startmin.value = startmin;
			document.EditView.startfmt.value = startfmt;
			document.EditView.endhr.value = endhr;
			document.EditView.endmin.value = endmin;
			document.EditView.endfmt.value = endfmt;
			document.EditView.viewOption.value = viewOption;
                        document.EditView.subtab.value = subtab;
		}
		if(type == 'todo')
		{
			document.createTodo.task_date_start.value = startdate;
			document.createTodo.starthr.value = starthr;
                        document.createTodo.startmin.value = startmin;
                        document.createTodo.startfmt.value = startfmt;
			document.createTodo.viewOption.value = viewOption;
                        document.createTodo.subtab.value = subtab;
		}
		y.display="block";
	}
}

function Taskshow(argg1,type,startdate,starthr,startmin,startfmt)
{
	var y=document.getElementById(argg1).style;
	if (y.display=="none")
        {
                document.EditView.date_start.value = startdate;
                document.EditView.starthr.value = starthr;
                document.EditView.startmin.value = startmin;
                document.EditView.startfmt.value = startfmt;
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
function maincheck_form()
{
	formSelectColumnString('inviteesid');
	starthour = document.EditView.starthr.value;
        startmin  = document.EditView.startmin.value;
        startformat = document.EditView.startfmt.value;
	endhour = document.EditView.endhr.value;
	endmin  = document.EditView.endmin.value;
	endformat = document.EditView.endfmt.value;	
	if(formValidate())
	{
		if(startformat != '')
		{
			if(startformat == 'pm')
			{
				if(starthour == '12')
					starthour = 12;
				else
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
				if(endhour == '12')
					endhour = 12;
				else
					endhour = eval(endhour) + 12;
				endmin = endmin;
			}
			else
			{
				endhour = endhour;
				endmin = endmin;
			}
		}
		var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	        var dateval2=getObj('due_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
		var dateelements1=splitDateVal(dateval1)
      		var dateelements2=splitDateVal(dateval2)

	        dd1=dateelements1[0]
        	mm1=dateelements1[1]
	        yyyy1=dateelements1[2]

        	dd2=dateelements2[0]
	        mm2=dateelements2[1]
        	yyyy2=dateelements2[2]
		var date1=new Date()
	        var date2=new Date()

        	date1.setYear(yyyy1)
	        date1.setMonth(mm1-1)
        	date1.setDate(dd1)

	        date2.setYear(yyyy2)
        	date2.setMonth(mm2-1)
	        date2.setDate(dd2)
		if (date2<=date1)
		{
			if((eval(endhour)*60+eval(endmin)) <= (eval(starthour)*60+eval(startmin)))
			{
				alert("End Time should be greater than Start Time ");
				document.EditView.endhr.focus();
				return false;
			}
			else
			{
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
				document.EditView.duration_hours.value = hour;
                		document.EditView.duration_minutes.value = minute;
			}
		}
		document.EditView.time_start.value = starthour+':'+startmin;
		document.EditView.time_end.value = endhour+':'+endmin;
		return true;
	}
	else return false;


}
function check_form()
{
	formSelectColumnString('inviteesid');
        if(trim(document.EditView.subject.value) == "")
        {
                alert("Missing Event Name");
                document.EditView.subject.focus()
                return false;
        }
        else
        {
		if(document.EditView.recurringcheck.checked == false)
		{
			document.EditView.recurringtype.value = '--None--';
		}
		if(document.EditView.record.value != '')
                {
                        document.EditView.mode.value = 'edit';
                }
		else
		{
			document.EditView.mode.value = 'create';
		}
		starthour = document.EditView.starthr.value;
		startmin  = document.EditView.startmin.value;
		startformat = document.EditView.startfmt.value;
		endhour = document.EditView.endhr.value;
                endmin  = document.EditView.endmin.value;
                endformat = document.EditView.endfmt.value;
		if(startformat != '')
		{
			if(startformat == 'pm')
			{
				if(starthour == '12')
					starthour = 12;
				else
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
				if(endhour == '12')
                                        endhour = 12;
                                else
                                        endhour = eval(endhour) + 12;
				endmin = endmin;
                        }
			else
			{
				endhour = endhour;
				endmin = endmin;
			}
		}
		if(!dateValidate('date_start','Start date','OTH'))
		{
			return false;
		}
		if(!dateValidate('due_date','End date','OTH'))
		{
			return false;
		}
		if(dateComparison('due_date','End date','date_start','Start date','GE'))
		{
			var dateval1=getObj('date_start').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
        	        var dateval2=getObj('due_date').value.replace(/^\s+/g, '').replace(/\s+$/g, '');
	                var dateelements1=splitDateVal(dateval1)
                	var dateelements2=splitDateVal(dateval2)

	                dd1=dateelements1[0]
        	        mm1=dateelements1[1]
                	yyyy1=dateelements1[2]

	                dd2=dateelements2[0]
        	        mm2=dateelements2[1]
                	yyyy2=dateelements2[2]
	                var date1=new Date()
        	        var date2=new Date()

                	date1.setYear(yyyy1)
	                date1.setMonth(mm1-1)
        	        date1.setDate(dd1)

        	        date2.setYear(yyyy2)
	                date2.setMonth(mm2-1)
                	date2.setDate(dd2)
                	if (date2<=date1)
                	{
                        	if((eval(endhour)*60+eval(endmin)) <= (eval(starthour)*60+eval(startmin)))
          	        	{
                	                alert("End Time should be greater than Start Time ");
                                	document.EditView.endhr.focus();
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
				document.EditView.duration_hours.value = hour;
	                        document.EditView.duration_minutes.value = minute;

           		}
		}	
		else
			return false;
		document.EditView.time_start.value = starthour+':'+startmin;
		document.EditView.time_end.value = endhour+':'+endmin;
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
	if(document.createTodo.record.value != '')
        {
        	document.createTodo.mode.value = 'edit';
        }
        else
        {
        	document.createTodo.mode.value = 'create';
        }

}


function maintask_check_form()
{
        starthour = document.EditView.starthr.value;
        startmin  = document.EditView.startmin.value;
        startformat = document.EditView.startfmt.value;
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
        document.EditView.time_start.value = starthour+':'+startmin;
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

function fnRedirect() {
        var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
	if(OptionData == 'listview')
	{
		document.EventViewOption.action.value = "index";
		window.document.EventViewOption.submit();
	}
	if(OptionData == 'hourview')
	{
		document.EventViewOption.action.value = "index";
		window.document.EventViewOption.submit();
	}
}

function fnAddEvent(obj,CurrObj,start_date,end_date,start_hr,start_min,start_fmt,end_hr,end_min,end_fmt,viewOption,subtab){
	var tagName = document.getElementById(CurrObj);
	var left_Side = findPosX(obj);
	var top_Side = findPosY(obj);
	tagName.style.left= left_Side  + 'px';
	tagName.style.top= top_Side + 22+ 'px';
	tagName.style.display = 'block';
	document.getElementById("addcall").href="javascript:gshow('addEvent','call','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"');fnRemoveEvent();";
	document.getElementById("addmeeting").href="javascript:gshow('addEvent','meeting','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"');fnRemoveEvent();";
	document.getElementById("addtodo").href="javascript:gshow('createTodo','todo','"+start_date+"','"+end_date+"','"+start_hr+"','"+start_min+"','"+start_fmt+"','"+end_hr+"','"+end_min+"','"+end_fmt+"','"+viewOption+"','"+subtab+"');fnRemoveEvent();";
	
}
	
function fnRemoveEvent(){
	var tagName = document.getElementById('addEventDropDown').style.display= 'none';
}

function fnShowEvent(){
		var tagName = document.getElementById('addEventDropDown').style.display= 'block';
}

function getMiniCal(url){
	if(url == undefined)
		url = 'module=Calendar&action=ActivityAjax&type=minical&ajax=true';
	else
		 url = 'module=Calendar&action=ActivityAjax&'+url+'&type=minical&ajax=true';
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: url,
                        onComplete: function(response) {
                                $("miniCal").innerHTML=response.responseText;
                        }
                }

          );
}

function getCalSettings(url){
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: 'module=Calendar&action=ActivityAjax&'+url+'&type=settings&ajax=true',
                        onComplete: function(response) {
                                $("calSettings").innerHTML=response.responseText;
                        }
                }

          );
}

function updateStatus(record,status,view,hour,day,month,year,type){
	if(type == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
		
		new Ajax.Request(
                	'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                        	postBody: 'module=Calendar&action=ActivityAjax&record='+record+'&'+status+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_status&viewOption='+OptionData+'&subtab=event&ajax=true',
                        	onComplete: function(response) {
					if(OptionData == 'listview')
						$("listView").innerHTML=response.responseText;
                                	if(OptionData == 'hourview')
                        			$("hrView").innerHTML=response.responseText;
                        	}
                	}
		);
	}
	if(type == 'todo')
        {
		new Ajax.Request(
                        'index.php',
			{queue: {position: 'end', scope: 'command'},
                                method: 'post',
				postBody: 'module=Calendar&action=ActivityAjax&record='+record+'&'+status+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_status&subtab=todo&ajax=true',
                                onComplete: function(response) {
                                        $("mnuTab2").innerHTML=response.responseText;
                                }
                        }
                )
	}
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
	var complete = document.getElementById("complete");
	var pending = document.getElementById("pending");
	var postpone = document.getElementById("postpone");
	var actdelete =	document.getElementById("actdelete");
	var changeowner = document.getElementById("changeowner");
	var OptionData = document.getElementById('view_Option').options[document.getElementById('view_Option').selectedIndex].value;
	
    }
    if(type == 'todo')
    {
	var heldstatus = "status=Completed";
        var notheldstatus = "status=Deferred";
	var activity_mode = "Task";
	var complete = document.getElementById("taskcomplete");
        var pending = document.getElementById("taskpending");
        var postpone = document.getElementById("taskpostpone");
        var actdelete = document.getElementById("taskactdelete");
        var changeowner = document.getElementById("taskchangeowner");
	var OptionData = '';
    }
    document.getElementById("idlist").value = id;
    document.change_owner.hour.value = hour;
    document.change_owner.day.value = day;
    document.change_owner.view.value = view;
    document.change_owner.month.value = month;
    document.change_owner.year.value = year;
    document.change_owner.subtab.value = type;
    complete.href="javascript:updateStatus("+id+",'"+heldstatus+"','"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";
    pending.href="javascript:updateStatus("+id+",'"+notheldstatus+"','"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";
    postpone.href="index.php?module=Calendar&action=EditView&record="+id+"&activity_mode="+activity_mode;
    actdelete.href="javascript:delActivity("+id+",'"+view+"',"+hour+","+day+","+month+","+year+",'"+type+"')";
    changeowner.href="javascript:dispLayer('act_changeowner');";

}

function dispLayer(lay)
{
	var tagName = document.getElementById(lay);
        tagName.style.visibility = 'visible';
        tagName.style.display = 'block';
}

function calendarChangeOwner()
{
	var user_id = document.getElementById('activity_owner').options[document.getElementById('activity_owner').options.selectedIndex].value;
	var idlist = document.change_owner.idlist.value;
        var view   = document.change_owner.view.value;
        var day    = document.change_owner.day.value;
        var month  = document.change_owner.month.value;
        var year   = document.change_owner.year.value;
        var hour   = document.change_owner.hour.value;
	var subtab = document.change_owner.subtab.value;
	if(subtab == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
	 	new Ajax.Request(
                	'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                        	postBody: 'module=Users&action=updateLeadDBStatus&return_module=Calendar&return_action=ActivityAjax&user_id='+user_id+'&idlist='+idlist+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_owner&viewOption='+OptionData+'&subtab=event&ajax=true',
                        	onComplete: function(response) {
					if(OptionData == 'listview')
						 $("listView").innerHTML=response.responseText;
					if(OptionData == 'hourview')
                                        	$("hrView").innerHTML=response.responseText;
                        	}
                	}
		);
	}
	if(subtab == 'todo')
        {
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Users&action=updateLeadDBStatus&return_module=Calendar&return_action=ActivityAjax&user_id='+user_id+'&idlist='+idlist+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=change_owner&subtab=todo&ajax=true',
                                onComplete: function(response) {
                                        $("mnuTab2").innerHTML=response.responseText;
                                }
                        }
                );
        }

}

function delActivity(id,view,hour,day,month,year,subtab)
{
	if(subtab == 'event')
	{
		var OptionData = $('view_Option').options[$('view_Option').selectedIndex].value;
         	new Ajax.Request(
                	'index.php',
                	{queue: {position: 'end', scope: 'command'},
                        	method: 'post',
                        	postBody: 'module=Users&action=massdelete&return_module=Calendar&return_action=ActivityAjax&idlist='+id+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=activity_delete&viewOption='+OptionData+'&subtab=event&ajax=true',
                        	onComplete: function(response) {
					if(OptionData == 'listview')
                                        	$("listView").innerHTML=response.responseText;
                                	if(OptionData == 'hourview')
                                        	$("hrView").innerHTML=response.responseText;
                        	}
                	}
		);
	}
	if(subtab == 'todo')
        {
                new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Users&action=massdelete&return_module=Calendar&return_action=ActivityAjax&idlist='+id+'&view='+view+'&hour='+hour+'&day='+day+'&month='+month+'&year='+year+'&type=activity_delete&subtab=todo&ajax=true',
                                onComplete: function(response) {
                                        $("mnuTab2").innerHTML=response.responseText;
                                }
                        }
                );
        }

}


/*
* javascript function to display the div tag
* @param divId :: div tag ID
*/
function cal_show(divId)

{

    var id = document.getElementById(divId);

    id.style.visibility = 'visible';

}

function fnAssignTo(){
		var option_Box = document.getElementById('parent_type');
		var option_select = option_Box.options[option_Box.selectedIndex].value;
		if(option_select == "Leads" || option_select == "Leads&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Accounts" || option_select == "Accounts&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Potentials" || option_select == "Potentials&action=Popup")
		{
			document.getElementById('leadLay').style.visibility = 'visible';
		}
		else if(option_select == "Quotes&action=Popup" || option_select == "Quotes&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "PurchaseOrder" || option_select == "PurchaseOrder&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "SalesOrder" || option_select == "SalesOrder&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "Invoice" || option_select == "Invoice&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else if(option_select == "Campaigns" || option_select == "Campaigns&action=Popup")
                {
                        document.getElementById('leadLay').style.visibility = 'visible';
                }
		else{
			document.getElementById('leadLay').style.visibility = 'hidden';
		}
	}
	
function fnShowPopup(){
	document.getElementById('popupLay').style.display = 'block';
}
	
function fnHidePopup(){
	document.getElementById('popupLay').style.display = 'none';
}

function getValidationarr(id,activity_mode,opmode,subtab,viewOption)
{
	 new Ajax.Request(
                        'index.php',
                        {queue: {position: 'end', scope: 'command'},
                                method: 'post',
                                postBody: 'module=Calendar&action=ActivityAjax&record='+id+'&activity_mode='+activity_mode+'&ajax=true&type=view&file=DetailView',
                                onComplete: function(response) {
                                        $("dataArray").innerHTML=response.responseText;
					setFieldvalues(opmode,subtab,viewOption);
                                }
                        }
                );

}

function setFieldvalues(opmode,subtab,viewOption)
{
	var st = document.getElementById('activity_cont');
	eval(st.innerHTML);
	if(activity_type == 'Events')
	{
		document.EditView.viewOption.value = viewOption;
                document.EditView.subtab.value = subtab;
		for(x=0;x<key.length;x++)
		{	
			if(document.EditView[key[x]] != undefined)
			{

				if(key[x] == 'visibility' && data[x] == 'Public')
					document.EditView.visibility.checked = true;
				if(key[x] == 'visibility' && data[x] == 'Private')
					document.EditView.visibility.checked = false;
				if(key[x] == 'activitytype' && data[x] == 'Call')
				{
					document.EditView.activitytype[0].checked = true;
				}
				else
				{
					document.EditView.activitytype[1].checked = true;
				}
				if(key[x] == 'set_reminder' && data[x] == 'Yes')
				{
					document.EditView.remindercheck.checked = true;
					document.getElementById('reminderOptions').style.display = 'block';
				}
				if(key[x] == 'recurringcheck' && data[x] == 'on')
				{
					document.EditView.recurringcheck.checked = true;
					document.getElementById('repeatOptions').style.display = 'block';
				}
				if(key[x] == 'recurringtype')
				{	
					if(data[x] == 'Weekly')
						document.getElementById('repeatWeekUI').style.display = 'block';
					else
						document.getElementById('repeatWeekUI').style.display = 'none';
					if(data[x] == 'Monthly')
						document.getElementById('repeatMonthUI').style.display = 'block';
					else
						document.getElementById('repeatMonthUI').style.display = 'none';
				}
				if(key[x] == 'parent_name')
				{
					if(data[x] != '')
						document.getElementById('leadLay').style.visibility = 'visible';
					else
						document.getElementById('leadLay').style.display = 'hidden';
				}
				document.EditView[key[x]].value = data[x];
			//}	
			}
		}
		document.getElementById('addEvent').style.display = 'block';
	}
	else
	{
		document.createTodo.viewOption.value = viewOption;
                document.createTodo.subtab.value = subtab;
		for(x=0;x<key.length;x++)
                {
			if(document.createTodo[key[x]] != undefined)
			{
                                document.createTodo[key[x]].value = data[x];
			}
		}
		document.getElementById('createTodo').style.display = 'block';
	}
}

function doNothing()
{
}
