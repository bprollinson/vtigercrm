function splitValues() {
        var picklistObj=getObj("fldPickList")
        var pickListContent=picklistObj.value
        var pickListAry=new Array()
        var i=0;

        //Splitting up of Values
        if (pickListContent.indexOf("\n")!=-1) {
                while(pickListContent.length>0) {
                        if(pickListContent.indexOf("\n")!=-1) {
                                if (pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
                                        pickListAry[i]=pickListContent.substr(0,pickListContent.indexOf("\n")).replace(/^\s+/g, '').replace(/\s+$/g, '')
                                        pickListContent=pickListContent.substr(pickListContent.indexOf("\n")+1,pickListContent.length)
                                        i++
                                } else break;
                        } else {
                                pickListAry[i]=pickListContent.substr(0,pickListContent.length)
                                break;
                        }
                }
        } else if (pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0) {
                pickListAry[0]=pickListContent.replace(/^\s+/g, '').replace(/\s+$/g, '')
        }

        return pickListAry;
}


function validate() {
	 lengthLayer=getObj("lengthdetails")
        decimalLayer=getObj("decimaldetails")
        pickListLayer=getObj("picklist")
        var str = getObj("fldLabel").value;
        if (!emptyCheck("fldLabel","Label"))
                return false

        var re1=/^[a-z\d\_ ]+$/i
        if (!re1.test(str))
        {
                alert("Special characters are not allowed in Label field")
                return false;
        }

        if (lengthLayer.style.visibility=="visible") {
                if (!emptyCheck("fldLength","Length"))
                        return false

                if (!intValidate("fldLength","Length"))
                        return false

                if (!numConstComp("fldLength","Length","GT",0))
                        return false

                if (!numConstComp("fldLength","Length","LE",255))
                        return false
        }

        if (decimalLayer.style.visibility=="visible") {
                if (getObj("fldDecimal").value.replace(/^\s+/g, '').replace(/\s+$/g, '').length>0)
                        if (!intValidate("fldDecimal","Decimal"))
                                return false
                if (!numConstComp("fldDecimal","Decimal","GE",0))
                        return false

                if (!numConstComp("fldDecimal","Decimal","LE",30))
                        return false
        }
var picklistObj=getObj("fldPickList")
        if (pickListLayer.style.visibility=="visible") {
                if (emptyCheck("fldPickList","Picklist values"))        {
                        var pickListAry=new Array()
                        pickListAry=splitValues()

                        //Empty Check validation
                        for (i=0;i<pickListAry.length;i++) {
                                if (pickListAry[i]=="") {
                                        alert("Picklist value cannot be empty")
                                        picklistObj.focus()
                                        return false
                                }
                        }

                        //Duplicate Values' Validation
                        for (i=0;i<pickListAry.length;i++) {
                                for (j=i+1;j<pickListAry.length;j++) {
                                        if (pickListAry[i]==pickListAry[j]) {
                                                alert("Duplicate Values found")
                                                picklistObj.focus()
                                                return false
                                        }
                                }
                        }

                        return true
                } else return false
        }
}
var fieldValueArr=new Array('Text','Number','Percent','Currency','Date','Email','Phone','Picklist','URL','Checkbox','TextArea','MultiSelectCombo');
var fieldTypeArr=new Array('text','number','percent','currency','date','email','phone','picklist','url','checkbox','textarea','multiselectcombo');
var currFieldIdx=0,totFieldType;
var focusFieldType;

function init() {
        lengthLayer=getObj("lengthdetails")
        decimalLayer=getObj("decimaldetails")
        pickListLayer=getObj("picklist")
        totFieldType=fieldTypeArr.length-1
}


function setVisible() {
        if (focusFieldType==true) {
                var selFieldType=fieldLayer.getObj("field"+currFieldIdx)
                var height=findPosY(selFieldType)+selFieldType.offsetHeight

                if (currFieldIdx==0) {
                        fieldLayer.document.body.scrollTop=0
                } else if (height>220) {
                        fieldLayer.document.body.scrollTop+=height-220
                } else {
                        fieldLayer.document.body.scrollTop-=220-height
                }

                if (window.navigator.appName.toUpperCase()=="OPERA") {
                                var newDiv=fieldLayer.document.createElement("DIV")
                                newDiv.style.zIndex="-1"
                                newDiv.style.position="absolute"
                                newDiv.style.top=findPosY(selFieldType)+"px"
                                newDiv.style.left="25px"

                                var newObj=fieldLayer.document.createElement("INPUT")
                                newObj.type="text"

                                fieldLayer.document.body.appendChild(newDiv)
                                newDiv.appendChild(newObj)
                                newObj.focus()

                                fieldLayer.document.body.removeChild(newDiv)
                }
        }
}

function selFieldType(id,scrollLayer,bool) {
        currFieldIdx=id
        var type=fieldTypeArr[id]
	var lengthLayer=getObj("lengthdetails");
	var decimalLayer=getObj("decimaldetails");
	var pickListLayer=getObj("picklist");
        if (type=='text') {
                lengthLayer.style.visibility="visible"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="hidden"
        } else if (type=='date' || type=='email' || type=='phone' || type=='url' || type=='checkbox' || type=='textarea') {
                getObj("lengthdetails").style.visibility="hidden"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="hidden"
        } else if (type=='number' || type=='percent' || type=='currency') {
                lengthLayer.style.visibility="visible"
                decimalLayer.style.visibility="visible"
                pickListLayer.style.visibility="hidden"
        } else if (type=='picklist' || type=='multiselectcombo') {
                lengthLayer.style.visibility="hidden"
                decimalLayer.style.visibility="hidden"
                pickListLayer.style.visibility="visible"
        }


        parent.getObj("fieldType").value = fieldValueArr[id];
}

function srchFieldType(ev) {
        if (browser_ie) {
                var keyCode=window.fieldLayer.event.keyCode
                var currElement=window.fieldLayer.event.srcElement
                if (currElement.id.indexOf("field")>=0) var doSearch=true
                else var doSearch=false
                window.fieldLayer.event.cancelBubble=true
        } else if (browser_nn4 || browser_nn6) {
                var keyCode=ev.which
                var currElement=ev.target
                if (currElement.type) doSearch=false
                else doSearch=true
        }

        if (doSearch==true) {
                switch (keyCode) {
                        case 9  : //Reset Field Type
                                                resetFieldTypeHilite();break;
                        case 33 : //Page Up
                        case 36 : //Home
                                                selFieldType(0);break;
                        case 34 : //Page Down
                        case 35 : //End
                                                selFieldType(totFieldType);break;
                        case 38 : //Up
                                                if (currFieldIdx!=0)
                                                        selFieldType(currFieldIdx-1);
                                                else
                                                        selFieldType(totFieldType,"yes");
                                                break;
                        case 40 : //Down
                                                if (currFieldIdx!=totFieldType)
                                                        selFieldType(currFieldIdx+1);
                                                else
                                                        selFieldType(0,"yes");
default : //Character Search
                                                if (keyCode>=65 && keyCode<=90) {
                                                        var srchChar=String.fromCharCode(keyCode)
                                                        if (currFieldIdx==totFieldType) var startIdx=0
                                                        else var startIdx=currFieldIdx+1

                                                        var loop=1
                                                        for (i=startIdx;i<=totFieldType;) {
                                                                currFieldStr=fieldLayer.getObj("field"+i).innerHTML
                                                                currFieldStr=currFieldStr.replace(/^\s+/g, '').replace(/\s+$/g, '').substr(0,1)
                                                                if (currFieldStr==srchChar) {
                                                                        selFieldType(i,"yes")
                                                                        i++
                                                                } else if (i==totFieldType && loop<=2) {
                                                                        i=0
                                                                        loop++
                                                                } else i++
                                                        }
                                           }
                }
        }
}
function resetFieldTypeHilite() {
        fieldLayer.getObj("field"+currFieldIdx).className="fieldType sel"
}
function validateCustomFieldAccounts()
        {
                var obj=document.getElementsByTagName("SELECT");
                var i,j=0,k=0,l=0;
                var n=obj.length;
                account = new Array;
                contact =  new Array;
                potential = new Array;
                for( i = 0; i < n; i++)
                {
                        if(obj[i].name.indexOf("_account")>0)
                        {
                                account[j]=obj[i].value;
                                j++;
                        }
                        if(obj[i].name.indexOf("_contact")>0)
                        {
                                contact[k]=obj[i].value;
                                k++;
                        }
                        if(obj[i].name.indexOf("_potential")>0)
                        {
                                potential[l]=obj[i].value;
                                l++;
                        }
                }
                for( i = 0; i < account.length; i++)
                {
                        for(j=i+1; j<account.length; j++)
                        {
                                if( account[i] == account[j] && account[i]!="None" && account[j] !="None")
                                {
                                        alert("Duplicate mapping for accounts!!");
                                        return false;
                                }
                        }
                }
for( i = 0; i < contact.length; i++)
                {
                        for(k=i+1; k< contact.length; k++)
                        {
                                if( contact[i] == contact[k] && contact[i]!="None" && contact[k]!="None")
                                {
                                        alert("Duplicate mapping for Contacts!!");
                                        return false;
                                }
                        }
                }
                for( i = 0; i < potential.length; i++)
                {
                        for(l=i+1; l<potential.length; l++)
                        {
                                if( potential[i] == potential[l] && potential[i]!="None" && potential[l]!="None")
                                {
                                        alert("Duplicate mapping for Potential!!");
                                        return false;
                                }
                        }

                }
        }


function gotourl(url)
{
                document.location.href=url;
}

