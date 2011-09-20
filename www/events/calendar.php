<?php
session_start();
include_once('../php/login.php');
$authUsers = array('brother');
include_once('../php/authenticate.php');

require_once 'classes/Calendar.php';


// Set month and year GET variables
if(!isset($_GET['year']))
    $_GET['year'] = date("Y");

if(!isset($_GET['month']))
    $_GET['month'] = date("n");

// Set month and year variables
$month = addslashes($_GET['month']);
$year = addslashes($_GET['year']);

$calendar = new Calendar($month, $year, $session->member_id);

/*
$startDate = "$year-$month-01";
$nextMonth = $month + 1;
$stopDate = "$year-$nextMonth-01";

// Get all the events for the month and place in events array
$query = "SELECT * FROM $db_table WHERE eventDate >= '$startDate' AND eventDate < '$stopDate' ORDER BY time";

$query_result = mysql_query ($query);
while ($info = mysql_fetch_array($query_result))
{
	// Get RSVP status of current user
	$query = "	SELECT status 
				FROM eventAttendance 
				WHERE username='$_SESSION[username]' 
				AND eventID='$info[ID]'";
	$result = mysql_query ($query);
	if( $data = mysql_fetch_array($result) ){
		
		$userStatus = $data[status];
		
	} else {
		
		$userStatus = 'notInvited';
	}
	
	// Get number of people attending the event
	$query = "	SELECT COUNT(ID) AS num
				FROM eventAttendance 
				WHERE username='$_SESSION[username]' 
				AND eventID='$info[ID]'
				AND status='attending'";
	$result = mysql_query ($query);
	
	$data = mysql_fetch_array($result);
	
	$numAttending = $data[num];
	
	
	// Check if there are open spots for the event
	if($info['maxAttendance'] > 0 && $numAttending >= $info['maxAttendance'])
	{
		$openSpots = false;
	} else {
		$openSpots = true;	
	}
	
	// Types of events that have no attendance settings 
	$generalEvents = array(
		'general',
		'education',
		'recruitment',
		'pr');
	
	// Set attendance status
	if( in_array($info[type], $generalEvents) ){
		
		$status = 'general';
		
	} else if($info['type'] == "social") {
		
		$status = 'social';
		
	} else if($userStatus == 'notInvited') {
		
		$status = 'notInvited';
		
	} else if($openSpots || $userStatus == 'attending') {
		
		$status = $userStatus;
		
	} else {
		
		$status = 'full';
	
	}
	
	$day = date('j',strtotime($info['eventDate']));
    $event_id = $info['ID'];
    $events[$day][] = $info['ID'];
    $event_info[$event_id]['0'] = substr($info['title'], 0, 12);;
   	$event_info[$event_id]['1'] = $info['time'];
	$event_info[$event_id]['2'] = $info['type'];
	$event_info[$event_id]['3'] = $status;
	$event_info[$event_id]['4'] = $info['mandatory'];
	$type[$event_id] = $info['type'];
	
} //end while
*/

// Set info for top shuttle nav next
if($month == 12){
    $next_month = 1;
    $next_year = $year + 1;
} else {
    $next_month = $month + 1;
    $next_year = $year;
}

// Set info for top shuttle nav previous
if($month == 1){
    $prev_month = 12;
    $prev_year = $year - 1;
} else {
    $prev_month = $month - 1;
    $prev_year = $year;
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link href="/styles/cal.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<script type="text/javascript" src="../../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/calendarFilters.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div align="center"><span class="currentdate"><? echo date ("F Y", strtotime("$year-$month-01")); ?></span><br>
	<br>
</div>
<div align="center"><br>

	<!-- Start Shuttle Menu -->

	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td><div align="right">
				<a href="<? echo "calendar.php?month=$prev_month&amp;year=$prev_year"; ?>"><strong>&lt;&lt;</strong></a>
			</div></td>
			<td width="200"><div align="center">
				
				<select name="month" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
					for ($i = 1; $i <= 12; $i++) {
						if($month == $i){
							$selected = "selected";
						} else {
							$selected = "";
						}
						echo "<option value=\"calendar.php?month=$i&amp;year=$year\" $selected>";
						echo date ("F", strtotime("$year-$i-01")) . "</option>\n";
					}
					?>
				</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
					 $this_year = date('Y');
					  
					  for ($i = $this_year-2; $i <= $this_year+1; $i++) {
						if($i == $year){
							$selected = "selected";
						} else {
							$selected = "";
						}
						echo "<option value=\"calendar.php?month=$month&amp;year=$i\" $selected>$i</option>\n";
					  }
					?>
				</select>
				</div></td>
			<td><div align="left">
				<a href="<? echo "calendar.php?month=$next_month&amp;year=$next_year"; ?>"><strong>&gt;&gt;</strong></a>
			</div></td>
		</tr>
	</table>
	
	<br>
</div>

<?php $calendar->draw_calendar('general'); ?>

<table border="0px" cellpadding="0" cellspacing="1" style="margin-left: 25px; margin-top: 20px;">
	<tr class="topdays"> 
		<td><div align="center">Key</div></td>
		<td><div align="center">Filters</div></td>
		</tr>
	<tr style="background-color:white;">
		<td style="color:#000;"><b>Mandatory</b> - (bold) Required for all members <br />
			<span style="color: #609">Full Event</span> - No open spots<br />
			<span style="color: #999">Not Attending or Excused</span> - Excused from event<br />
			<span style="color: #900">Action Required</span> - RSVP to these events<br />
			<span style="color: #C60">Awaiting Moderation</span> - Waiting for confirmation<br />
			<span style="color: #060">Attending Event</span> - Optional event you are attending<br />
			</td>
		<td style="color: #000;">
			Community Service <input type="checkbox" id="filterCommunityService" checked="checked" /><br />
			Member Education <input type="checkbox" id="filterMemberEducation" checked="checked" /><br />
			Social <input type="checkbox" id="filterSocial" checked="checked" /><br />
			</td>
	</tr>
</table>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>