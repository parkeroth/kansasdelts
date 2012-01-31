<?
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'pres', 'vpInternal', 'vpExternal', 'brotherhood', 'recruitment', 'secretary', 'communityService', 'social', 'houseManager', 'pledgeEd', 'homecoming', 'publicRel', 'drm', 'philanthropy');
include_once('php/authenticate.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Calendar - Add Event</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript">

function ShowContent(d) {
if(d.length < 1) { return; }
document.getElementById(d).style.display = "block";
//alert("SHOW " + d);
}

function HideContent(d) {
if(d.length < 1) { return; }
document.getElementById(d).style.display = "none";
//alert("HIDE " + d);
}


function Check(field) {
	var myTextField = document.getElementById(field);
	if(field == "type"){
		if(myTextField.value != "general" || myTextField.value != "social"){ShowContent('invite');}
		if(myTextField.value == "general" || myTextField.value == "social"){HideContent('invite');}
		if(myTextField.value != "general" || myTextField.value != "brotherhood" || myTextField.value != "social"){ShowContent('limitQuestion');}
		if(myTextField.value == "general" || myTextField.value == "brotherhood" || myTextField.value == "social"){HideContent('limitQuestion');}
	} else if(field == "limit"){
		if(document.form.limit[0].checked){ShowContent('maxAttendance');}
		if(document.form.limit[1].checked){HideContent('maxAttendance');}
	}
}

function select(a) {
    var theForm = document.form;
    for (i=0; i<theForm.elements.length; i++) {
        if (theForm.elements[i].name=='invited[]')
            theForm.elements[i].checked = a;
    }
}

</script>


</head>
<body onLoad="Check('type');">
<form id="form" name="form" method="post" action="php/addEvent.php" onSubmit="window.close()">
  <p>
    <label>Title:
      <input id='title' type="text" name="title" />
    </label>
  </p>
  <p>
    <label>Description:
      <input id="description" type="text" name="description" />
    </label>
  </p>
  <p>
  	<label>Event Type:
      <select id="type" name="type" onChange="Check('type');">
	  	
		<?php
			$auth_list = array('admin', 'pres', 'vpInternal', 'communityService');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"communityService\">Community Service</option>";
			}
			$auth_list = array('admin', 'pres', 'vpInternal', 'houseManager');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"house\">House Cleaning</option>";
			}
			$auth_list = array('admin', 'pres', 'vpInternal', 'brotherhood');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"brotherhood\">Brotherhood</option>";
			}
			$auth_list = array('admin', 'pres', 'vpInternal', 'vpExternal', 'secretary');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"general\">General</option>";
			}
			$auth_list = array('admin', 'pres', 'vpInternal', 'social', 'drm');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"social\">Social</option>";
			}
			$auth_list = array('admin', 'pres', 'recruitment');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"recruitment\">Recruitment</option>";
			}
			$auth_list = array('admin', 'pres', 'pledgeEd');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"education\">Member Education</option>";
			}
			$auth_list = array('admin', 'pres', 'vpExternal', 'homecoming');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"homecoming\">Homecoming</option>";
			}
			$auth_list = array('admin', 'pres', 'vpExternal');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"pr\">Public Relations</option>";
			}
			$auth_list = array('admin', 'philanthropy', 'vpExternal', 'pres');
			if($session->isAuth($auth_list))
			{
				echo "<option value=\"philanthropy\">Philanthropy</option>";
			}
			
		?>
                
      </select>
    </label>
  </p>
  <p>
    <label>Event Date:
      <select name="month">
        <option value="01"<?php if($_GET['month'] == "1"){ echo selected; } ?>>January</option>
        <option value="02"<?php if($_GET['month'] == "2"){ echo selected; } ?>>February</option>
        <option value="03"<?php if($_GET['month'] == "3"){ echo selected; } ?>>March</option>
        <option value="04"<?php if($_GET['month'] == "4"){ echo selected; } ?>>April</option>
        <option value="05"<?php if($_GET['month'] == "5"){ echo selected; } ?>>May</option>
        <option value="06"<?php if($_GET['month'] == "6"){ echo selected; } ?>>June</option>
        <option value="07"<?php if($_GET['month'] == "7"){ echo selected; } ?>>July</option>
        <option value="08"<?php if($_GET['month'] == "8"){ echo selected; } ?>>August</option>
        <option value="09"<?php if($_GET['month'] == "9"){ echo selected; } ?>>September</option>
        <option value="10"<?php if($_GET['month'] == "10"){ echo selected; } ?>>October</option>
        <option value="11"<?php if($_GET['month'] == "11"){ echo selected; } ?>>November</option>
        <option value="12"<?php if($_GET['month'] == "12"){ echo selected; } ?>>December</option>
      </select>
      <select name="day">
        <option value="01"<?php if($_GET['day'] == "1"){ echo selected; } ?>>1</option>
        <option value="02"<?php if($_GET['day'] == "2"){ echo selected; } ?>>2</option>
        <option value="03"<?php if($_GET['day'] == "3"){ echo selected; } ?>>3</option>
        <option value="04"<?php if($_GET['day'] == "4"){ echo selected; } ?>>4</option>
        <option value="05"<?php if($_GET['day'] == "5"){ echo selected; } ?>>5</option>
        <option value="06"<?php if($_GET['day'] == "6"){ echo selected; } ?>>6</option>
        <option value="07"<?php if($_GET['day'] == "7"){ echo selected; } ?>>7</option>
        <option value="08"<?php if($_GET['day'] == "8"){ echo selected; } ?>>8</option>
        <option value="09"<?php if($_GET['day'] == "9"){ echo selected; } ?>>9</option>
        <option value="10"<?php if($_GET['day'] == "10"){ echo selected; } ?>>10</option>
        <option value="11"<?php if($_GET['day'] == "11"){ echo selected; } ?>>11</option>
        <option value="12"<?php if($_GET['day'] == "12"){ echo selected; } ?>>12</option>
        <option value="13"<?php if($_GET['day'] == "13"){ echo selected; } ?>>13</option>
        <option value="14"<?php if($_GET['day'] == "14"){ echo selected; } ?>>14</option>
        <option value="15"<?php if($_GET['day'] == "15"){ echo selected; } ?>>15</option>
        <option value="16"<?php if($_GET['day'] == "16"){ echo selected; } ?>>16</option>
        <option value="17"<?php if($_GET['day'] == "17"){ echo selected; } ?>>17</option>
        <option value="18"<?php if($_GET['day'] == "18"){ echo selected; } ?>>18</option>
        <option value="19"<?php if($_GET['day'] == "19"){ echo selected; } ?>>19</option>
        <option value="20"<?php if($_GET['day'] == "20"){ echo selected; } ?>>20</option>
        <option value="21"<?php if($_GET['day'] == "21"){ echo selected; } ?>>21</option>
        <option value="22"<?php if($_GET['day'] == "22"){ echo selected; } ?>>22</option>
        <option value="23"<?php if($_GET['day'] == "23"){ echo selected; } ?>>23</option>
        <option value="24"<?php if($_GET['day'] == "24"){ echo selected; } ?>>24</option>
        <option value="25"<?php if($_GET['day'] == "25"){ echo selected; } ?>>25</option>
        <option value="26"<?php if($_GET['day'] == "26"){ echo selected; } ?>>26</option>
        <option value="27"<?php if($_GET['day'] == "27"){ echo selected; } ?>>27</option>
        <option value="28"<?php if($_GET['day'] == "28"){ echo selected; } ?>>28</option>
        <option value="29"<?php if($_GET['day'] == "29"){ echo selected; } ?>>29</option>
        <option value="30"<?php if($_GET['day'] == "30"){ echo selected; } ?>>30</option>
        <option value="31"<?php if($_GET['day'] == "31"){ echo selected; } ?>>31</option>
      </select>
      <select name="year">
      	<?php
		$now = date("Y");
		echo "<option value=\"".$now."\">".$now."</option>\n";
		$now++;
		echo "<option value=\"".$now."\">".$now."</option>\n";
		$now++;
		echo "<option value=\"".$now."\">".$now."</option>\n";
		$now++;
		?>
      </select>
    </label>
  </p>
  <p>
  	<label>Event Time:
      <select name="hour">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
      </select>
      :
      <select name="minute">
        <option value="00">00</option>
        <option value="15">15</option>
        <option value="30">30</option>
        <option value="45">45</option>
      </select>
      <select name="ampm">
        <option value="AM">AM</option>
        <option value="PM">PM</option>
      </select>
    </label>
  </p>
	<div id="limitQuestion" style="display:none;">
 		<p>Limit number of attendees? 
			Yes <input id="limit" name="limit" type="radio" value="yes" onChange="Check('limit');"> 
			No <input id="limit" name="limit" type="radio" value="no" onChange="Check('limit');" checked> 
		</p>
    </div>
    
    <div id="maxAttendance" style="display:none;">
        	<p>
    			<label>Max number of attendees:
      				<input id="maxAttendance" type="text" name="maxAttendance" size="4" />
    			</label>
  			</p>
    </div>
  	
	<div id="mandatory" style="display:block;">
		<p>
			Mandatory: <input name="mandatory" type="radio" value="1" /> Yes <input name="mandatory" type="radio" value="0" checked /> No
		</p>
	</div>
    
	<p>Send new event notification? 
		Yes <input name="notify" type="radio" value="yes"> 
		No <input name="notify" type="radio" value="no" checked> 
	</p>

<div id="invite" style="float:left;">

<p>To invite people to the event check their name or to invite an entire pledge class check the box next to it. Feel free to mix section options as it is impossible for someone to be invited more than once.</p>

<div style="float:left">
	<?php
  echo "<p>";
    echo "<label>Invited: <br>";
	
	include_once('php/login.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$userData = "
		SELECT * 
		FROM members
		ORDER BY lastName";
	
	$getUserData = mysqli_query($mysqli, $userData);
	echo "<table>";
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
	
      echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;".$userDataArray['firstName']." ".$userDataArray['lastName'].": </td><td><input type=\"checkbox\" name=\"invited[]\" value=\"".$userDataArray['username']."\"/></td></tr>";
	  
	}
	echo "</table>";
    echo "</label>";
  echo "</p>";
  ?>
</div>
<div style="float:left; padding-left:20px;">
	<?php
	  echo "<p>";
		echo "<label>Classes: <br>";
		
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData2 = "
			SELECT DISTINCT class FROM `members` ORDER BY ID";
		
		$getUserData2 = mysqli_query($mysqli, $userData2);
		echo "<table>";
		while($userDataArray2 = mysqli_fetch_array($getUserData2, MYSQLI_ASSOC)){
		
		  echo "<tr><td>".ucwords($userDataArray2['class'])."</td><td><input type=\"checkbox\" name=\"classes[]\" value=\"".$userDataArray2['class']."\"/></td></tr>";
		  
		}
		echo "</table>";
		echo "</label>";
	  echo "</p>";
  ?>
</div>
<div style="clear:both;">
	<p>
  	
	<a href="javascript:select(1)">Check all</a> |
	<a href="javascript:select(0)">Uncheck all</a>
	
	 </p>
</div>
  
</div>
<div style="clear:both">
  <p>
  		<input type="hidden" name="dateAdded" value="<?PHP echo date("Y-m-d"); ?>" />
      <input type="submit" name="submit" id="submit" value="Submit" />
    <label>
        <input type="reset" name="Reset" id="Reset" value="Reset" />
    </label>
  </p>
 </div>
</form>
<script language="JavaScript" type="text/javascript">
 var frmvalidator  = new Validator("form");
 frmvalidator.addValidation("title","req","Please input a title!");
 
 frmvalidator.addValidation("description","req","Please input a description!");
 
 frmvalidator.addValidation("type","req","Please input an event type!");
 
 frmvalidator.addValidation("eventDate","req","Please input an event date!");
</script>
</body>
</html>