/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
var globaldtlviewspanid = "";
var globaleditareaspanid = ""; 
var globaltxtboxid = "";
var itsonview=false;
// to retain the old value if we cancel the ajax edit
var globaltempvalue = '';
var globaluitype = '';
function showHide(showId, hideId)
{
	show(showId);
	fnhide(hideId);
}

function hndCancel(valuespanid,textareapanid,fieldlabel)
{

  showHide(valuespanid,textareapanid);
  if(globaluitype == '56')
  {	  
	  if(globaltempvalue == 1)
	  	getObj(globaltxtboxid).checked = true; 
	  else		
	  	getObj(globaltxtboxid).checked = false; 
  }
  else if(globaluitype != '53')	  
	  getObj(globaltxtboxid).value = globaltempvalue; 
  globaltempvalue = '';
  itsonview=false;
  return false;
}

function hndMouseOver(uitype,fieldLabel)
{
      var mouseArea="";
      mouseArea="mouseArea_"+ fieldLabel;
      if(itsonview)
      {
            return;
      }
      
      show("crmspanid");
	  globaluitype = uitype;
      globaldtlviewspanid= "dtlview_"+ fieldLabel;//valuespanid;
      globaleditareaspanid="editarea_"+ fieldLabel;//textareapanid;
	  globalfieldlabel = fieldLabel;
	  if(globaluitype == 53)
	  {
		  if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		  {
			  var assign_type_U = document.DetailView.assigntype[0].checked;
			  var assign_type_G = document.DetailView.assigntype[1].checked;
			  if(assign_type_U == true)
				  globaltxtboxid= 'txtbox_U'+fieldLabel;
			  else if(assign_type_G == true)
				  globaltxtboxid= 'txtbox_G'+fieldLabel;
		  }else
		  {
			  globaltxtboxid= 'txtbox_U'+fieldLabel;
		  }
	  }else
	  {
      	  globaltxtboxid="txtbox_"+ fieldLabel;//textboxpanid;
	  }
      divObj = getObj('crmspanid'); 
      crmy = findPosY(getObj(mouseArea));
      crmx = findPosX(getObj(mouseArea));
      if(document.all)
      {
          divObj.onclick=handleEdit;
      }
      else
      {
          divObj.setAttribute('onclick','handleEdit();');
      }
      divObj.style.left=(crmx+getObj(mouseArea).offsetWidth -divObj.offsetWidth)+"px";
      divObj.style.top=crmy+"px";
}

function handleEdit()
{
     show(globaleditareaspanid) ;
     fnhide(globaldtlviewspanid);
	 if(globaluitype != 53)
	 {
		globaltempvalue = getObj(globaltxtboxid).value;
		if(getObj(globaltxtboxid).type != 'hidden')
	     	 	getObj(globaltxtboxid).focus();
	 }
     fnhide('crmspanid');
     itsonview=true;
     return false;
}

function trim(str)
{
	return(str.replace(/\s+$/,''));
}

var genUiType = "";
var genFldValue = "";

function dtlViewAjaxSave(fieldLabel,module,uitype,tableName,fieldName,crmId)
{
	var dtlView = "dtlview_"+ fieldLabel;
	var editArea = "editarea_"+ fieldLabel;
	var groupurl = "";
	
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
		}else
		{
			var assign_type_U = document.DetailView.assigntype.checked;
		}
		if(assign_type_U == true)
		{
			var txtBox= 'txtbox_U'+fieldLabel;
		}
		else if(assign_type_G == true)
		{
			var txtBox= 'txtbox_G'+fieldLabel;
			var group_name = $(txtBox).options[$(txtBox).selectedIndex].text; 
			var groupurl = "&assigned_group_name="+group_name+"&assigntype=T"
		}

	}else if(globaluitype == 33)
	{
	  var txtBox= "txtbox_"+ fieldLabel;
	  var oMulSelect = $(txtBox);
	  var r = new Array();
	  for (iter=0;iter < oMulSelect.options.length ; iter++)
	  {
      if (oMulSelect.options[iter].selected)
        r[r.length] = oMulSelect.options[iter].value;
      }
	}else
	{
		var txtBox= "txtbox_"+ fieldLabel;
	}
	
	var popupTxt= "popuptxt_"+ fieldLabel;      
	var hdTxt = "hdtxt_"+ fieldLabel;

	if(formValidate() == false)
	{
		return false;
	}


	$("vtbusy_info").style.display="inline";
	var isAdmin = document.getElementById("hdtxt_IsAdmin").value; 


	//overriden the tagValue based on UI Type for checkbox 
	if(uitype == '56')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			tagValue = "1";
		}else
		{
			tagValue = "0";
		}
	}else	if(uitype == '156')
	{
		if(document.getElementById(txtBox).checked == true)
		{
			tagValue = "on";
		}else
		{
			tagValue = "off";
		}
	}else if(uitype == '33')
	{
		tagValue = r.join(" |##| ");
  	}else
	{
		tagValue = trim(document.getElementById(txtBox).value);
	}


	var data = "file=DetailViewAjax&module=" + module + "&action=" + module + "Ajax&record=" + crmId+"&recordid=" + crmId ;
	data = data + "&fldName=" + fieldName + "&fieldValue=" + escape(tagValue) + "&ajxaction=DETAILVIEW"+groupurl;
	new Ajax.Request(
		'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: data,
                        onComplete: function(response) {
                  				if(response.responseText.indexOf(":#:FAILURE")>-1)
                  				{
                  					alert("Error while Editing");
                  				}
                  				else if(response.responseText.indexOf(":#:SUCCESS")>-1)
                       		{
                  					$("vtbusy_info").style.display="none";
                       		}
                        }
                }
            );
	if(uitype == '13' || uitype == '104')
	{
		getObj(dtlView).innerHTML = "<a href=\"mailto:"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
	}else if(uitype == '17')
	{
		getObj(dtlView).innerHTML = "<a href=\"http://"+ tagValue+"\" target=\"_blank\">"+tagValue+"&nbsp;</a>";
	}else if(uitype == '53')
	{
		var hdObj = getObj(hdTxt);
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
        {
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
		}else
		{
			var assign_type_U = document.DetailView.assigntype.checked;
		}
		if(isAdmin == "0")
		{
			getObj(dtlView).innerHTML = hdObj.value;
		}else if(isAdmin == "1" && assign_type_U == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}else if(isAdmin == "1" && assign_type_G == true)
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=GroupDetailView&groupId="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";
		}
	}else if(uitype == '56')
	{
		if(tagValue == '1')
		{
			getObj(dtlView).innerHTML = "yes";
		}else
		{
			getObj(dtlView).innerHTML = "";
		}

	}else if(uitype == 116)
	{
			getObj(dtlView).innerHTML = document.getElementById(txtBox).options[document.getElementById(txtBox).selectedIndex].text; 
	}
	else if(getObj(popupTxt))
	{
		var popObj = getObj(popupTxt);
		if(uitype == '50' || uitype == '73' || uitype == '51')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Accounts&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '57')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Contacts&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '59')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Products&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '75' || uitype == '81' )
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Vendors&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";

		}
		else if(uitype == '76')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Potentials&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '78')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=Quotes&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '80')
		{
			getObj(dtlView).innerHTML = "<a href=\"index.php?module=SalesOrder&action=DetailView&record="+tagValue+"\">"+popObj.value+"&nbsp;</a>";
		}
		else if(uitype == '53')
		{
			var hdObj = getObj(hdTxt);
			if(isAdmin == "0")
			{
				getObj(dtlView).innerHTML = hdObj.value;
			}else if(isAdmin == "1")
			{
				getObj(dtlView).innerHTML = "<a href=\"index.php?module=Users&action=DetailView&record="+tagValue+"\">"+hdObj.value+"&nbsp;</a>";;
			}
		}
		else if(uitype == '56')
		{
			if(tagValue == '1')
			{
				getObj(dtlView).innerHTML = "yes";
			}else
			{
				getObj(dtlView).innerHTML = "";
			}

		}
		else
		{
			getObj(dtlView).innerHTML = popObj.value;
		}
	}else if(uitype == '33')
  	{
       		getObj(dtlView).innerHTML = r.join(", ");
	}else
	{
		getObj(dtlView).innerHTML = tagValue;
	}
	showHide(dtlView,editArea);  //show,hide
	itsonview=false;
}

function SaveTag(txtBox,crmId,module)
{
	var tagValue = document.getElementById(txtBox).value;
	document.getElementById(txtBox).value ='';
	$("vtbusy_info").style.display="inline";
	new Ajax.Request(
		'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "file=TagCloud&module=" + module + "&action=" + module + "Ajax&recordid=" + crmId + "&ajxaction=SAVETAG&tagfields=" +tagValue,
                        onComplete: function(response) {
				        getObj('tagfields').innerHTML = response.responseText;
					$("vtbusy_info").style.display="none";
                        }
                }
        );
    
}
function setSelectValue(fieldLabel)
{
	if(globaluitype == 53)
	{
		if(typeof(document.DetailView.assigntype[0]) != 'undefined')
		{
			var assign_type_U = document.DetailView.assigntype[0].checked;
			var assign_type_G = document.DetailView.assigntype[1].checked;
			if(assign_type_U == true)
				var selCombo= 'txtbox_U'+fieldLabel;
			else if(assign_type_G == true)	
				var selCombo= 'txtbox_G'+fieldLabel;
		}else
		{
			var selCombo= 'txtbox_U'+fieldLabel;
		}
	}else
	{
			var selCombo= 'txtbox_'+fieldLabel;
	}
	var hdTxtBox = 'hdtxt_'+fieldLabel;
	var oHdTxtBox = document.getElementById(hdTxtBox);
	var oSelCombo = document.getElementById(selCombo);

	oHdTxtBox.value = oSelCombo.options[oSelCombo.selectedIndex].text;
}

