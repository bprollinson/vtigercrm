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



function gshow(argg1)
{
	var y=document.getElementById(argg1).style;
	
	if (y.display=="none") 
	{
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

function check_form()
{
        if(trim(document.appSave.subject.value) == "")
        {
                alert("Missing Event Name");
                document.appSave.subject.focus()
                return false;
        }
        else
        {
                if (document.appSave.activitytype[0].checked==true)
                {
                        document.appSave.duration_minutes.value = "15";
                }
                else if (document.appSave.activitytype[1].checked == true)
                {
                        document.appSave.duration_minutes.value = "45";
                }
                return true;
        }
}
