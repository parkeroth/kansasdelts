/*
  -------------------------------------------------------------------------
		      JavaScript Form Validator (gen_validatorv31.js)
              Version 3.1.2
	Copyright (C) 2003-2008 JavaScript-Coder.com. All rights reserved.
	You can freely use this script in your Web pages.
	You may adapt this script for your own needs, provided these opening credit
    lines are kept intact.
		
	The Form validation script is distributed free from JavaScript-Coder.com
	For updates, please visit:
	http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
	
	Questions & comments please send to form.val at javascript-coder.com
  -------------------------------------------------------------------------  
*/
function Validator(frmname)
{
  this.formobj=document.forms[frmname];
	if(!this.formobj)
	{
	  alert("Error: couldnot get Form object "+frmname);
		return;
	}
	if(this.formobj.onsubmit)
	{
	 this.formobj.old_onsubmit = this.formobj.onsubmit;
	 this.formobj.onsubmit=null;
	}
	else
	{
	 this.formobj.old_onsubmit = null;
	}
	this.formobj._sfm_form_name=frmname;
	this.formobj.onsubmit=form_submit_handler;
	this.addValidation = add_validation;
	this.setAddnlValidationFunction=set_addnl_vfunction;
	this.clearAllValidations = clear_all_validations;
    this.disable_validations = false;//new
    document.error_disp_handler = new sfm_ErrorDisplayHandler();
    this.EnableOnPageErrorDisplay=validator_enable_OPED;
	this.EnableOnPageErrorDisplaySingleBox=validator_enable_OPED_SB;
    this.show_errors_together=true;
    this.EnableMsgsTogether=sfm_enable_show_msgs_together;
    document.set_focus_onerror=true;
    this.EnableFocusOnError=sfm_validator_enable_focus;

}

function sfm_validator_enable_focus(enable)
{
    document.set_focus_onerror = enable;
}

function set_addnl_vfunction(functionname)
{
  this.formobj.addnlvalidation = functionname;
}

function sfm_set_focus(objInput)
{
    if(document.set_focus_onerror)
    {
        objInput.focus();
    }
}

function sfm_enable_show_msgs_together()
{
    this.show_errors_together=true;
    this.formobj.show_errors_together=true;
}
function clear_all_validations()
{
	for(var itr=0;itr < this.formobj.elements.length;itr++)
	{
		this.formobj.elements[itr].validationset = null;
	}
}

function form_submit_handler()
{
   var bRet = true;
    document.error_disp_handler.clear_msgs();
	for(var itr=0;itr < this.elements.length;itr++)
	{
		if(this.elements[itr].validationset &&
	   !this.elements[itr].validationset.validate())
		{
		  bRet = false;
		}
        if(!bRet && !this.show_errors_together)
        {
          break;

        }
	}

	if(this.addnlvalidation)
	{
	  str =" var ret = "+this.addnlvalidation+"()";
	  eval(str);

     if(!ret) 
     {
       bRet=false; 
     }

	}

   if(!bRet)
    {
      document.error_disp_handler.FinalShowMsg();
      return false;
    }
	return true;
}

function add_validation(itemname,descriptor,errstr)
{
	var condition = null;
	if(arguments.length > 3)
	{
	 condition = arguments[3]; 
	}
  if(!this.formobj)
	{
		alert("Error: The form object is not set properly");
		return;
	}//if
	var itemobj = this.formobj[itemname];
    if(itemobj.length && isNaN(itemobj.selectedIndex) )
    //for radio button; don't do for 'select' item
	{
		itemobj = itemobj[0];
	}	
  if(!itemobj)
	{
		alert("Error: Couldnot get the input object named: "+itemname);
		return;
	}
	if(!itemobj.validationset)
	{
		itemobj.validationset = new ValidationSet(itemobj,this.show_errors_together);
	}
	itemobj.validationset.add(descriptor,errstr,condition);
    itemobj.validatorobj=this;
}
function validator_enable_OPED()
{
    document.error_disp_handler.EnableOnPageDisplay(false);
}

function validator_enable_OPED_SB()
{
	document.error_disp_handler.EnableOnPageDisplay(true);
}
function sfm_ErrorDisplayHandler()
{
  this.msgdisplay = new AlertMsgDisplayer();
  this.EnableOnPageDisplay= edh_EnableOnPageDisplay;
  this.ShowMsg=edh_ShowMsg;
  this.FinalShowMsg=edh_FinalShowMsg;
  this.all_msgs=new Array();
  this.clear_msgs=edh_clear_msgs;
}
function edh_clear_msgs()
{
    this.msgdisplay.clearmsg(this.all_msgs);
    this.all_msgs = new Array();
}
function edh_FinalShowMsg()
{
    this.msgdisplay.showmsg(this.all_msgs);
}
function edh_EnableOnPageDisplay(single_box)
{
	if(true == single_box)
	{
		this.msgdisplay = new SingleBoxErrorDisplay();
	}
	else
	{
		this.msgdisplay = new DivMsgDisplayer();		
	}
}
function edh_ShowMsg(msg,input_element)
{
	
   var objmsg = new Array();
   objmsg["input_element"] = input_element;
   objmsg["msg"] =  msg;
   this.all_msgs.push(objmsg);
}
function AlertMsgDisplayer()
{
  this.showmsg = alert_showmsg;
  this.clearmsg=alert_clearmsg;
}
function alert_clearmsg(msgs)
{

}
function alert_showmsg(msgs)
{
    var whole_msg="";
    var first_elmnt=null;
    for(var m=0;m < msgs.length;m++)
    {
        if(null == first_elmnt)
        {
            first_elmnt = msgs[m]["input_element"];
        }
        whole_msg += msgs[m]["msg"] + "\n";
    }
	
    alert(whole_msg);

    if(null != first_elmnt)
    {
        sfm_set_focus(first_elmnt);
    }
}
function sfm_show_error_msg(msg,input_elmt)
{
    document.error_disp_handler.ShowMsg(msg,input_elmt);
}
function SingleBoxErrorDisplay()
{
 this.showmsg=sb_div_showmsg;
 this.clearmsg=sb_div_clearmsg;
}

function sb_div_clearmsg(msgs)
{
	var divname = form_error_div_name(msgs);
	show_div_msg(divname,"");
}

function sb_div_showmsg(msgs)
{
	var whole_msg="<ul>\n";
	for(var m=0;m < msgs.length;m++)
    {
        whole_msg += "<li>" + msgs[m]["msg"] + "</li>\n";
    }
	whole_msg += "</ul>";
	var divname = form_error_div_name(msgs);
	show_div_msg(divname,whole_msg);
}
function form_error_div_name(msgs)
{
	var input_element= null;

	for(var m in msgs)
	{
	 input_element = msgs[m]["input_element"];
	 if(input_element){break;}
	}

	var divname ="";
	if(input_element)
	{
	 divname = input_element.form._sfm_form_name + "_errorloc";
	}

	return divname;
}
function DivMsgDisplayer()
{
 this.showmsg=div_showmsg;
 this.clearmsg=div_clearmsg;
}
function div_clearmsg(msgs)
{
    for(var m in msgs)
    {
        var divname = element_div_name(msgs[m]["input_element"]);
        show_div_msg(divname,"");
    }
}
function element_div_name(input_element)
{
  var divname = input_element.form._sfm_form_name + "_" + 
                   input_element.name + "_errorloc";

  divname = divname.replace(/[\[\]]/gi,"");

  return divname;
}
function div_showmsg(msgs)
{
    var whole_msg;
    var first_elmnt=null;
    for(var m in msgs)
    {
        if(null == first_elmnt)
        {
            first_elmnt = msgs[m]["input_element"];
        }
        var divname = element_div_name(msgs[m]["input_element"]);
        show_div_msg(divname,msgs[m]["msg"]);
    }
    if(null != first_elmnt)
    {
        sfm_set_focus(first_elmnt);
    }
}
function show_div_msg(divname,msgstring)
{
	if(divname.length<=0) return false;

	if(document.layers)
	{
		divlayer = document.layers[divname];
        if(!divlayer){return;}
		divlayer.document.open();
		divlayer.document.write(msgstring);
		divlayer.document.close();
	}
	else
	if(document.all)
	{
		divlayer = document.all[divname];
        if(!divlayer){return;}
		divlayer.innerHTML=msgstring;
	}
	else
	if(document.getElementById)
	{
		divlayer = document.getElementById(divname);
        if(!divlayer){return;}
		divlayer.innerHTML =msgstring;
	}
	divlayer.style.visibility="visible";	
}

function ValidationDesc(inputitem,desc,error,condition)
{
  this.desc=desc;
	this.error=error;
	this.itemobj = inputitem;
	this.condition = condition;
	this.validate=vdesc_validate;
}
function vdesc_validate()
{
	if(this.condition != null )
	{
		if(!eval(this.condition))
		{
			return true;
		}
	}
	if(!validateInput(this.desc,this.itemobj,this.error))
	{
		this.itemobj.validatorobj.disable_validations=true;

		sfm_set_focus(this.itemobj);

		return false;
	}
	return true;
}
function ValidationSet(inputitem,msgs_together)
{
    this.vSet=new Array();
	this.add= add_validationdesc;
	this.validate= vset_validate;
	this.itemobj = inputitem;
    this.msgs_together = msgs_together;
}
function add_validationdesc(desc,error,condition)
{
  this.vSet[this.vSet.length]= 
  new ValidationDesc(this.itemobj,desc,error,condition);
}
function vset_validate()
{
    var bRet = true;
    for(var itr=0;itr<this.vSet.length;itr++)
    {
        bRet = bRet && this.vSet[itr].validate();
        if(!bRet && !this.msgs_together)
        {
            break;
        }
    }
    return bRet;
}
function validateEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if(splitted == null) return false;
    if(splitted[1] != null )
    {
      var regexp_user=/^\"?[\w-_\.]*\"?$/;
      if(splitted[1].match(regexp_user) == null) return false;
    }
    if(splitted[2] != null)
    {
      var regexp_domain=/^[\w-\.]*\.[A-Za-z]{2,4}$/;
      if(splitted[2].match(regexp_domain) == null) 
      {
	    var regexp_ip =/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
	    if(splitted[2].match(regexp_ip) == null) return false;
      }// if
      return true;
    }
return false;
}

function IsCheckSelected(objValue,chkValue)
{
    var selected=false;
	var objcheck = objValue.form.elements[objValue.name];
    if(objcheck.length)
	{
		var idxchk=-1;
		for(var c=0;c < objcheck.length;c++)
		{
		   if(objcheck[c].value == chkValue)
		   {
		     idxchk=c;
			 break;
		   }//if
		}//for
		if(idxchk>= 0)
		{
		  if(objcheck[idxchk].checked=="1")
		  {
		    selected=true;
		  }
		}//if
	}
	else
	{
		if(objValue.checked == "1")
		{
			selected=true;
		}//if
	}//else	

	return selected;
}
function TestDontSelectChk(objValue,chkValue,strError)
{
	var pass = true;
	pass = IsCheckSelected(objValue,chkValue)?false:true;

	if(pass==false)
	{
     if(!strError || strError.length ==0) 
        { 
        	strError = "Can't Proceed as you selected "+objValue.name;  
        }//if			  
	  sfm_show_error_msg(strError,objValue);
	  
	}
    return pass;
}
function TestShouldSelectChk(objValue,chkValue,strError)
{
	var pass = true;

	pass = IsCheckSelected(objValue,chkValue)?true:false;

	if(pass==false)
	{
     if(!strError || strError.length ==0) 
        { 
        	strError = "You should select "+objValue.name;  
        }//if			  
	  sfm_show_error_msg(strError,objValue);
	  
	}
    return pass;
}
function TestRequiredInput(objValue,strError)
{
 var ret = true;
 var val = objValue.value;
 val = val.replace(/^\s+|\s+$/g,"");//trim
    if(eval(val.length) == 0) 
    { 
       if(!strError || strError.length ==0) 
       { 
         strError = objValue.name + " : Required Field"; 
       }//if 
       sfm_show_error_msg(strError,objValue); 
       ret=false; 
    }//if 
return ret;
}
function TestMaxLen(objValue,strMaxLen,strError)
{
 var ret = true;
    if(eval(objValue.value.length) > eval(strMaxLen)) 
    { 
      if(!strError || strError.length ==0) 
      { 
        strError = objValue.name + " : "+ strMaxLen +" characters maximum "; 
      }//if 
      sfm_show_error_msg(strError,objValue); 
      ret = false; 
    }//if 
return ret;
}
function TestMinLen(objValue,strMinLen,strError)
{
 var ret = true;
    if(eval(objValue.value.length) <  eval(strMinLen)) 
    { 
      if(!strError || strError.length ==0) 
      { 
        strError = objValue.name + " : " + strMinLen + " characters minimum  "; 
      }//if               
      sfm_show_error_msg(strError,objValue); 
      ret = false;   
    }//if 
return ret;
}
function TestInputType(objValue,strRegExp,strError,strDefaultError)
{
   var ret = true;

    var charpos = objValue.value.search(strRegExp); 
    if(objValue.value.length > 0 &&  charpos >= 0) 
    { 
     if(!strError || strError.length ==0) 
      { 
        strError = strDefaultError;
      }//if 
      sfm_show_error_msg(strError,objValue); 
      ret = false; 
    }//if 
 return ret;
}
function TestEmail(objValue,strError)
{
var ret = true;
     if(objValue.value.length > 0 && !validateEmail(objValue.value)	 ) 
     { 
       if(!strError || strError.length ==0) 
       { 
          strError = objValue.name+": Enter a valid Email address "; 
       }//if                                               
       sfm_show_error_msg(strError,objValue); 
       ret = false; 
     }//if 
return ret;
}
function TestLessThan(objValue,strLessThan,strError)
{
var ret = true;
	  if(isNaN(objValue.value)) 
	  { 
	    sfm_show_error_msg(objValue.name +": Should be a number ",objValue); 
	    ret = false; 
	  }//if 
	  else
	  if(eval(objValue.value) >=  eval(strLessThan)) 
	  { 
	    if(!strError || strError.length ==0) 
	    { 
	      strError = objValue.name + " : value should be less than "+ strLessThan; 
	    }//if               
	    sfm_show_error_msg(strError,objValue); 
	    ret = false;                 
	   }//if   
return ret;          
}
function TestGreaterThan(objValue,strGreaterThan,strError)
{
var ret = true;
     if(isNaN(objValue.value)) 
     { 
       sfm_show_error_msg(objValue.name+": Should be a number ",objValue); 
       ret = false; 
     }//if 
	 else
     if(eval(objValue.value) <=  eval(strGreaterThan)) 
      { 
        if(!strError || strError.length ==0) 
        { 
          strError = objValue.name + " : value should be greater than "+ strGreaterThan; 
        }//if               
        sfm_show_error_msg(strError,objValue);  
        ret = false;
      }//if  
return ret;           
}
function TestRegExp(objValue,strRegExp,strError)
{
var ret = true;
    if( objValue.value.length > 0 && 
        !objValue.value.match(strRegExp) ) 
    { 
      if(!strError || strError.length ==0) 
      { 
        strError = objValue.name+": Invalid characters found "; 
      }//if                                                               
      sfm_show_error_msg(strError,objValue); 
      ret = false;                   
    }//if 
return ret;
}
function TestDontSelect(objValue,dont_sel_index,strError)
{
var ret = true;
    if(objValue.selectedIndex == null) 
    { 
      sfm_show_error_msg("ERROR: dontselect command for non-select Item"); 
      ret =  false; 
    } 
    if(objValue.selectedIndex == eval(dont_sel_index)) 
    { 
     if(!strError || strError.length ==0) 
      { 
      strError = objValue.name+": Please Select one option "; 
      }//if                                                               
      sfm_show_error_msg(strError,objValue); 
      ret =  false;                                   
     } 
return ret;
}
function TestSelectOneRadio(objValue,strError)
{
	var objradio = objValue.form.elements[objValue.name];
	var one_selected=false;
	for(var r=0;r < objradio.length;r++)
	{
	  if(objradio[r].checked)
	  {
	  	one_selected=true;
		break;
	  }
	}
	if(false == one_selected)
	{
      if(!strError || strError.length ==0) 
       {
	    strError = "Please select one option from "+objValue.name;
	   }	
	  sfm_show_error_msg(strError,objValue);
	}
return one_selected;
}

function validateInput(strValidateStr,objValue,strError) 
{ 
    var ret = true;
    var epos = strValidateStr.search("="); 
    var  command  = ""; 
    var  cmdvalue = ""; 
    if(epos >= 0) 
    { 
     command  = strValidateStr.substring(0,epos); 
     cmdvalue = strValidateStr.substr(epos+1); 
    } 
    else 
    { 
     command = strValidateStr; 
    } 
    switch(command) 
    { 
        case "req": 
        case "required": 
         { 
		   ret = TestRequiredInput(objValue,strError)
           break;             
         }//case required 
        case "maxlength": 
        case "maxlen": 
          { 
			 ret = TestMaxLen(objValue,cmdvalue,strError)
             break; 
          }//case maxlen 
        case "minlength": 
        case "minlen": 
           { 
			 ret = TestMinLen(objValue,cmdvalue,strError)
             break; 
            }//case minlen 
        case "alnum": 
        case "alphanumeric": 
           { 
				ret = TestInputType(objValue,"[^A-Za-z0-9]",strError, 
						objValue.name+": Only alpha-numeric characters allowed ");
				break; 
           }
        case "alnum_s": 
        case "alphanumeric_space": 
           { 
				ret = TestInputType(objValue,"[^A-Za-z0-9\\s]",strError, 
						objValue.name+": Only alpha-numeric characters and space allowed ");
				break; 
           }		   
        case "num": 
        case "numeric": 
           { 
                ret = TestInputType(objValue,"[^0-9]",strError, 
						objValue.name+": Only digits allowed ");
                break;               
           }
        case "dec": 
        case "decimal": 
           { 
                ret = TestInputType(objValue,"[^0-9\.]",strError, 
						objValue.name+": Only numbers allowed ");
                break;               
           }
        case "alphabetic": 
        case "alpha": 
           { 
                ret = TestInputType(objValue,"[^A-Za-z]",strError, 
						objValue.name+": Only alphabetic characters allowed ");
                break; 
           }
        case "alphabetic_space": 
        case "alpha_s": 
           { 
                ret = TestInputType(objValue,"[^A-Za-z\\s]",strError, 
						objValue.name+": Only alphabetic characters and space allowed ");
                break; 
           }
        case "email": 
          { 
			   ret = TestEmail(objValue,strError);
               break; 
          }
        case "lt": 
        case "lessthan": 
         { 
    	      ret = TestLessThan(objValue,cmdvalue,strError);
              break; 
         }
        case "gt": 
        case "greaterthan": 
         { 
			ret = TestGreaterThan(objValue,cmdvalue,strError);
            break; 
         }//case greaterthan 
        case "regexp": 
         { 
			ret = TestRegExp(objValue,cmdvalue,strError);
           break; 
         }
        case "dontselect": 
         { 
			 ret = TestDontSelect(objValue,cmdvalue,strError)
             break; 
         }
		case "dontselectchk":
		{
			ret = TestDontSelectChk(objValue,cmdvalue,strError)
			break;
		}
		case "shouldselchk":
		{
			ret = TestShouldSelectChk(objValue,cmdvalue,strError)
			break;
		}
		case "selone_radio":
		{
			ret = TestSelectOneRadio(objValue,strError);
		    break;
		}		 
    }//switch 
	return ret;
}
function VWZ_IsListItemSelected(listname,value)
{
 for(var i=0;i < listname.options.length;i++)
 {
  if(listname.options[i].selected == true &&
   listname.options[i].value == value) 
   {
     return true;
   }
 }
 return false;
}
function VWZ_IsChecked(objcheck,value)
{
 if(objcheck.length)
 {
     for(var c=0;c < objcheck.length;c++)
     {
       if(objcheck[c].checked == "1" && 
	     objcheck[c].value == value)
       {
        return true; 
       }
     }
 }
 else
 {
  if(objcheck.checked == "1" )
   {
    return true; 
   }    
 }
 return false;
}

function DoCustomValidation()
{
	var departments=new Array();
	 departments[0]="ACMP";
 	 departments[1]="ACCT";
	 departments[2]="ABDS";
	 departments[3]="ADS";
	 departments[4]="AE";
	 departments[5]="AIR";
	 departments[6]="AAAS";
	 departments[7]="AMS";
	 departments[8]="AMHR";
	 departments[9]="ANTH";
	 departments[10]="ABSC";
	 departments[11]="AEC";
	 departments[12]="ARAB";
	 departments[13]="ARCE";
	 departments[14]="ARCH";
	 departments[15]="ARMY";
	 departments[16]="ART";
	 departments[17]="AFND";
	 departments[18]="HA";
	 departments[19]="ASTR";
	 departments[20]="ATMO";
	 departments[21]="AUD";
	 departments[22]="BAND";
	 departments[23]="BASN";
	 departments[24]="BINF";
	 departments[25]="BIOL";
	 departments[26]="BCRS";
	 departments[27]="BRSS";
	 departments[28]="BUS";
	 departments[29]="BE";
	 departments[30]="BLAW";
	 departments[31]="CARI";
	 departments[32]="CER";
	 departments[33]="CHAM";
	 departments[34]="C&PE";
	 departments[35]="CHEM";
	 departments[36]="CHIN";
	 departments[37]="CHOR";
	 departments[38]="CHUR";
	 departments[39]="CE";
	 departments[40]="CLAR";
	 departments[41]="CLSX";
	 departments[42]="CLS";
	 departments[43]="COMS";
	 departments[44]="COND";
	 departments[45]="CMGT";
	 departments[46]="C&T";
	 departments[47]="CYTO";
	 departments[48]="CZCH";
	 departments[49]="DANC";
	 departments[50]="DANE";
	 departments[51]="DSCI";
	 departments[52]="DBS";
	 departments[53]="DFND";
	 departments[54]="CSON";
	 departments[55]="UTEC";
	 departments[56]="DN,DIET";
	 departments[57]="DBBS";
	 departments[58]="DRWG";
	 departments[59]="DTCH";
	 departments[60]="EALC";
	 departments[61]="ECIV";
	 departments[62]="ECON";
	 departments[63]="ELPS";
	 departments[64]="EECS";
	 departments[65]="ENGR";
	 departments[66]="EMGT";
	 departments[67]="EPHX";
	 departments[68]="ENGL";
	 departments[69]="ESLP";
	 departments[70]="ENTR";
	 departments[71]="EVRN";
	 departments[72]="EUPH";
	 departments[73]="EURS";
	 departments[74]="EXM";
	 departments[75]="FIN";
	 departments[76]="FLUT";
	 departments[77]="FREN";
	 departments[78]="FRHN";
	 departments[79]="GEOG";
	 departments[80]="GEOL";
	 departments[81]="GERM";
	 departments[82]="GRK";
	 departments[83]="GUIT";
	 departments[84]="HAIT";
	 departments[85]="HARP";
	 departments[86]="HPCD";
	 departments[87]="HAUS";
	 departments[88]="HSES";
	 departments[89]="HEIM";
	 departments[90]="HEBR";
	 departments[91]="HIST";
	 departments[92]="HA";
	 departments[93]="HNRS";
	 departments[94]="HWC";
	 departments[95]="HNGR";
	 departments[96]="INS";
	 departments[97]="INDD";
	 departments[98]="IPS";
	 departments[99]="IST";
	 departments[100]="INTD";
	 departments[101]="IBUS";
	 departments[102]="ITAL";
	 departments[103]="JPN";
	 departments[104]="JAZZ";
	 departments[105]="JWSH";
	 departments[106]="JOUR";
	 departments[107]="KISW";
	 departments[108]="KOR";
	 departments[109]="LAT";
	 departments[110]="LAA";
	 departments[111]="LA&S";
	 departments[112]="LING";
	 departments[113]="MGMT";
	 departments[114]="MCOR";
	 departments[115]="MKTG";
	 departments[116]="MATH";
	 departments[117]="ME";
	 departments[118]="MDCM";
	 departments[119]="METL";
	 departments[120]="MUS";
	 departments[121]="MEMT";
	 departments[122]="MTHC";
	 departments[123]="MUSC";
	 departments[124]="NAVY";
	 departments[125]="NORW";
	 departments[126]="NMED";
	 departments[127]="NURS";
	 departments[128]="NRSG";
	 departments[129]="OBOE";
	 departments[130]="OCTH";
	 departments[131]="ORCH";
	 departments[132]="ORGN";
	 departments[133]="PNTG";
	 departments[134]="PCUS";
	 departments[135]="PENS";
	 departments[136]="PHCH";
	 departments[137]="P&TX";
	 departments[138]="PHAR";
	 departments[139]="PHPR";
	 departments[140]="PHIL";
	 departments[141]="PHMD";
	 departments[142]="PHSX";
	 departments[143]="PIAN";
	 departments[144]="PLSH";
	 departments[145]="POLS";
	 departments[146]="PORT";
	 departments[147]="PRNT";
	 departments[148]="PSYC";
	 departments[149]="PRE";
	 departments[150]="PUAD";
	 departments[151]="REC";
	 departments[152]="RECO";
	 departments[153]="REL";
	 departments[154]="RESP";
	 departments[155]="RUSS";
	 departments[156]="REES";
	 departments[157]="SAXO";
	 departments[158]="SCAN";
	 departments[159]="SCUL";
	 departments[160]="SLAV";
	 departments[161]="SW";
	 departments[162]="SOC";
	 departments[163]="SPAN";
	 departments[164]="SPED";
	 departments[165]="SPLH";
	 departments[166]="STRG";
	 departments[167]="SCM";
	 departments[168]="KISW";
	 departments[169]="SWED";
	 departments[170]="SA&D";
	 departments[171]="TD";
	 departments[172]="TH&F";
	 departments[173]="TROM";
	 departments[174]="TRUM";
	 departments[175]="TUBA";
	 departments[176]="TUEU";
	 departments[177]="TURK";
	 departments[178]="UKRA";
	 departments[179]="UTEC";
	 departments[180]="UBPL";
	 departments[181]="UYGR";
	 departments[182]="VIOA";
	 departments[183]="VION";
	 departments[184]="VNCL";
	 departments[185]="VAE";
	 departments[186]="VISC";
	 departments[187]="VOIC";
	 departments[188]="W&P";
	 departments[189]="WENS";
	 departments[190]="WOLO";
	 departments[191]="WS";
	 departments[192]="YDSH";
	 
	 var valid = false;
	 
	 
	 for(i=0; i < departments.length; i++){
		var str = document.forms.form1.department.value;
		
		if(departments[i] == str.toUpperCase()){
			valid = 1;
			break;
		}
	 }
	 
  if(valid == false)
  {
    sfm_show_error_msg('Invalid Department Code!',document.forms.form1.department);
    return false;
  }
  else
  {
    return true;
  }
}