<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('/php/authenticate.php');

$db_connection = mysql_connect ($db_host, $db_username, $db_password) OR die (mysql_error());  
$db_select = mysql_select_db ($db_database) or die (mysql_error());
$db_table = $TBL_PR . "events";


// Get roster info for logged in user
// Store in $userDataArray
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
$userData = "
	SELECT * 
	FROM members 
	WHERE username='".$_SESSION['username']."'";
$getUserData = mysqli_query($mysqli, $userData);
$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

// Set month and year GET variables
IF(!isset($_GET['year'])){
    $_GET['year'] = date("Y");
}
IF(!isset($_GET['month'])){
    $_GET['month'] = date("n")+1;
}

// Set month and year variables
$month = addslashes($_GET['month'] - 1);
$year = addslashes($_GET['year']);

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

$todays_date = date("j");
$todays_month = date("n");

$days_in_month = date ("t", mktime(0,0,0,$_GET['month'],0,$_GET['year']));
$first_day_of_month = date ("w", mktime(0,0,0,$_GET['month']-1,1,$_GET['year']));
$first_day_of_month = $first_day_of_month + 1;
$count_boxes = 0;
$days_so_far = 0;

// Set info for top shuttle nav next
if($_GET['month'] == 13){
    $next_month = 2;
    $next_year = $_GET['year'] + 1;
} else {
    $next_month = $_GET['month'] + 1;
    $next_year = $_GET['year'];
}

// Set info for top shuttle nav previous
if($_GET['month'] == 2){
    $prev_month = 13;
    $prev_year = $_GET['year'] - 1;
} else {
    $prev_month = $_GET['month'] - 1;
    $prev_year = $_GET['year'];
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

<div align="center"><span class="currentdate"><? echo date ("F Y", mktime(0,0,0,$_GET['month']-1,1,$_GET['year'])); ?></span><br>
	<br>
</div>
<div align="center"><br>

	<!-- Start Shuttle Menu -->

	<table width="600" border="0" cellspacing="0" cellpadding="0">
		<tr> 
			<td><div align="right">
				<a href="<? echo "calendar.php?month=$prev_month&amp;year=$prev_year"; ?>">&lt;&lt;</a>
			</div></td>
			<td width="200"><div align="center">
				
				<select name="month" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
					for ($i = 1; $i <= 12; $i++) {
						$link = $i+1;
						IF($_GET['month'] == $link){
							$selected = "selected";
						} ELSE {
							$selected = "";
						}
						echo "<option value=\"calendar.php?month=$link&amp;year=$_GET[year]\" $selected>";
						echo date ("F", mktime(0,0,0,$i,1,$_GET['year'])) . "</option>\n";
					}
					?>
				</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
					  $yearLoop = date("Y");
					  
					  for ($i = $yearLoop; $i <= $yearLoop+1; $i++) {
						IF($i == $_GET['year']){
							$selected = "selected";
						} ELSE {
							$selected = "";
						}
						echo "<option value=\"calendar.php?month=$_GET[month]&amp;year=$i\" $selected>$i</option>\n";
					  }
					?>
				</select>
				</div></td>
			<td><div align="left">
				<a href="<? echo "calendar.php?month=$next_month&amp;year=$next_year"; ?>">&gt;&gt;</a>
			</div></td>
		</tr>
	</table>
	
	<!-- Start Shuttle Menu -->
		
	<br>
</div>
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#000000">
	<tr>
		<td><table width="100%" border="0" cellpadding="0" cellspacing="1">
			<tr class="topdays"> 
				<td><div align="center">Sunday</div></td>
				<td><div align="center">Monday</div></td>
				<td><div align="center">Tuesday</div></td>
				<td><div align="center">Wednesday</div></td>
				<td><div align="center">Thursday</div></td>
				<td><div align="center">Friday</div></td>
				<td><div align="center">Saturday</div></td>
				</tr>
			<tr valign="top" bgcolor="#FFFFFF"> 
				<?
				
		// Fill calendar with boxes before 1st
		for ($i = 1; $i <= $first_day_of_month-1; $i++) {
			$days_so_far = $days_so_far + 1;
			$count_boxes = $count_boxes + 1;
			echo "<td width=\"100\" height=\"100\" class=\"beforedayboxes\"></td>\n";
		}
		
		// Fill calendar with boxes for each day
		for ($i = 1; $i <= $days_in_month; $i++) {
   			$days_so_far = $days_so_far + 1;
    		$count_boxes = $count_boxes + 1;
			
			// If box is for today  set class
			if($_GET['month'] == $todays_month+1 && $i == $todays_date){
				$class = "highlighteddayboxes";
			} else {
				$class = "dayboxes";
			}
			
			echo "<td width=\"100\" height=\"100\" class=\"$class\">\n";
			$link_month = $_GET['month'] - 1;
			
			// If authorized set event add link
			if(	strpos($userDataArray['accountType'], "admin") || 
				strpos($userDataArray['accountType'], "houseManager") || 
				strpos($userDataArray['accountType'], "brotherhood") || 
				strpos($userDataArray['accountType'], "secretary") || 
				strpos($userDataArray['accountType'], "communityService") || 
				strpos($userDataArray['accountType'], "recruitment") || 
				strpos($userDataArray['accountType'], "pledgeEd") || 
				strpos($userDataArray['accountType'], "homecoming") || 
				strpos($userDataArray['accountType'], "vpInternal") || 
				strpos($userDataArray['accountType'], "vpExternal") || 
				strpos($userDataArray['accountType'], "pres") || 
				strpos($userDataArray['accountType'], "drm") || 
				strpos($userDataArray['accountType'], "social") || 
				strpos($userDataArray['accountType'], "philanthropy") )
			{
				echo "<div align=\"right\"><span class=\"toprightnumber\">\n";
				echo "<a class=\"topRightNum\" href=\"javascript:MM_openBrWindow('AddCalEvent.php?";
				echo "day=$i&amp;month=$link_month&amp;year=$_GET[year]&amp;type=$userDataArray[accountType]";
				echo "','','width=500,height=400, scrollbars=1');\">$i</a>";
				echo "&nbsp;</span></div>\n";
			}
			else
			{
				echo "<div align=\"right\"><span class=\"toprightnumber\">\n$i&nbsp;</span></div>\n";
			}
			
			// If event in day
			if(isset($events[$i])){
				echo "<div align=\"left\"><span class=\"eventinbox\">\n";
				
				// Iterate through all events in array
				while (list($key, $value) = each ($events[$i])) {
					
					// Set event link
					if($event_info[$value]['3'] == "general" || $event_info[$value]['3'] == "social" || $event_info[$value]['3'] == "pr")
					{
						echo "<a class=\"eventLinkgeneral ".$type[$value]."Filter\"";
						echo "href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=$value";
						echo "', '', 'width=500, height=200');\">";
					}
					else if($event_info[$value]['3'] != "notInvited")
					{
						echo "<a class=\"eventLink".$event_info[$value]['3']." ".$type[$value]."Filter\"";
						echo "href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=$value&amp;";
						echo "status=".$event_info[$value]['3']."','','width=500,height=220');\">";
					}
					
					// If Mandatory
					if($event_info[$value]['4']){echo "<b>";}
					
					echo $event_info[$value]['0'];	// Print event title
					echo "<br>&nbsp;".$event_info[$value][1];
					
					// If Mandatory
					if($event_info[$value]['4']){echo "</b>";}
					
					echo "</a>\n<br>\n";		
			
				}
				echo "</span></div>\n";
			}
			echo "</td>\n";
			
			// End row if end of week
			if(($count_boxes == 7) && ($days_so_far != (($first_day_of_month-1) + $days_in_month))){
				$count_boxes = 0;
				echo "</TR><TR valign=\"top\">\n";
			}
		}
		
		// Fill boxes with days after 30/31st
		$extra_boxes = 7 - $count_boxes;
		for ($i = 1; $i <= $extra_boxes; $i++) {
			echo "<td width=\"100\" height=\"100\" class=\"afterdayboxes\"></td>\n";
		}
		
		?>
				</tr>
			</table></td>
		</tr>
</table>
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
	</tr></table>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>