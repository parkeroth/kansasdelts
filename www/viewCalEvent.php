<?
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
$userData = "
	SELECT * 
	FROM members 
	WHERE username='".$_SESSION['username']."'";
$getUserData = mysqli_query($mysqli, $userData);
$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

$db_connection = mysql_connect ($db_host, $db_username, $db_password) OR die (mysql_error());  
$db_select = mysql_select_db ($db_database) or die (mysql_error());
$db_table = $TBL_PR . "events";

$query = "SELECT * FROM $db_table WHERE ID='$_GET[ID]' LIMIT 1";
$query_result = mysql_query ($query);
while ($info = mysql_fetch_array($query_result)){
    $date = date ("l, jS F Y", strtotime($info[eventDate]));
    $time_array = split(":", $info['time']);
    $time = date ("g:ia", mktime($time_array['0'],$time_array['1'],0,$info['event_month'],date('j',strtotime($info['eventDate'])),$info['event_year']));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>PHPCalendar - <? echo $info['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">
<link href="images/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="480" height="180" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td height="100">
<table width="480" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td><span class="eventwhen"><b><? echo $date . " at " . $time; ?></b></span><br> 
            <br> </td>
        </tr>
        <tr> 
          <td><b><span class="event">Event Title: </span></b> <span class="eventdetail"><? echo $info['title']; ?></span><br><br></td>
        </tr>
        <tr> 
          <td><b><span class="event">Event Description: </span></b> <span class="eventdetail"><? echo $info['description']; ?></span><br></td>
        </tr>
        <?php
			echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
			if($_GET['status'] == "view"){
			
				// Skip
			
			} else if($_GET['status'] == "attending"){
			
				echo '<p>&nbsp;</p>';
				echo '<p>You <b>are</b> attending this event. ';
				echo '<a href="php/eventResetSignup.php?id='.$info['ID'].'">Change</a></p>';
			
			} else if($_GET['status'] == "notAttending"){
					
				echo '<p>&nbsp;</p>';
				echo '<p>You <b>are not</b> attending this event. ';
				echo '<a href="php/eventResetSignup.php?id='.$info['ID'].'">Change</a></p>';
				
			} else if($_GET['status'] == "excused"){
				
				echo '<p>&nbsp;</p>';
				echo '<p>You are <b>excused</b> from this event. ';
				echo '<a href="php/eventResetSignup.php?id='.$info['ID'].'">Change</a></p>';
				
			} else if($_GET['status'] == "full"){
					
				echo '<p>&nbsp;</p><h3>This event is full.</h3>';
				
			} else if($info['type'] == "house"){ //If it is a house event
				if(strpos($info['limbo'], $_SESSION['username'])){ //If user is in limbo
					
					echo "<span style=\"color: red;\">Pending Moderation</span>";
					echo "<p>&nbsp;</p>";
					
				} else if(strpos($info['forced'], $_SESSION['username'])){ //If user is in forced
					
					echo "<span style=\"color: red;\">Excuse Rejected!</span> ".$info['description']."<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse for not attending: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$info['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$info['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"forced\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
				} else if(strpos($info['excused'], $_SESSION['username'])){ //If user is excused
					//Output nothing					   
				} else if($info['mandatory'] == 1){ //If event is mandatory
					echo "<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$info['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$info['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"firstTime\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
				} else {
					
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> Attending ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> Not Attending";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$info['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$info['mandatory']."\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
				}
			} else if($info['type'] == "general" || $info['type'] == "social"){
				//Output nothing
			} else {
				echo "<br>";
				echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> Attending";
				echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> Not Attending";
				echo " <input type=\"hidden\" name=\"eventID\" value=\"".$info['ID']."\" />";
				echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$info['mandatory']."\" />";
				echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
			}
		echo "</form></td></tr>";
	?>
      
  	<?php if(	($info['type'] == "house" && strpos($userDataArray['accountType'],"houseManager") ) ||
				($info['type'] == "communityService" && strpos($userDataArray['accountType'],"communityService") ) ||	
				($info['type'] == "general" && strpos($userDataArray['accountType'],"secretary") ) ||
				($info['type'] == "recruitment" && strpos($userDataArray['accountType'],"recruitment") ) || 
				($info['type'] == "pr" && strpos($userDataArray['accountType'],"publicRel") ) || 
				($info['type'] == "education" && strpos($userDataArray['accountType'],"pledgeEd") ) || 
				($info['type'] == "brotherhood" && strpos($userDataArray['accountType'],"brotherhood") ) || 
				($info['type'] == "homecoming" && strpos($userDataArray['accountType'],"homecoming") ) || 
				($info['type'] == "social" && (strpos($userDataArray['accountType'],"social") || strpos($userDataArray['accountType'],"drm")) ) ||
				strpos($userDataArray['accountType'], "admin") ){ ?>
  <tr>
    <td align="left" valign="bottom"><a href="DeleteCalEvent.php?date=<?php echo $info[eventDate]; ?>&ID=<?php echo $info[ID]; ?>">Delete</a></td>
  </tr>
  <?php } ?>
  </table></td>
  </tr>
</table>
</body>
</html>
<? } ?>