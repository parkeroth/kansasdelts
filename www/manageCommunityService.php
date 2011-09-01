<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'communityService', 'houseManager', 'brotherhood');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php 
		$type = $_GET['type'];
		
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$time = time() + (60 * 60 * 24 * 7)*0;
		$now = date("Y-m-d", $time);
	
		$month = date("n", $time);
		$year = date("Y", $time);
		
		$nMonth = (int)$month;
		
		if($nMonth >= 1 && $nMonth <= 5){
			$term = "spring".$year;
		} else if($nMonth >= 8 && $nMonth <= 12){
			$term = "fall".$year;
		}
	
		$eventData = "
			SELECT * 
			FROM events 
			WHERE type='".$type."'
			AND term ='".$term."'
			ORDER BY eventDate ASC";
	
		$getEventData = mysqli_query($mysqli, $eventData);
		if($type == 'communityService'){
			echo "<p><h2>Community Service Events</h2></p>";
		} else if($type == 'communityService'){
			echo "<p><h2>Brotherhood Events</h2></p>";
		}
		
		echo "<table>";
		$count=0;
		while($eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC)){
			$date = $eventDataArray['eventDate'];
			$date = strtotime($date);;
			$date = date("D n/j", $date);
			
			$attending = substr_count($eventDataArray['attending'],"|");
			$notAttending = substr_count($eventDataArray['notAttending'],"|");
			$invited = substr_count($eventDataArray['invited'],"|")-($attending - $notAttending);
			$limbo = substr_count($eventDataArray['limbo'],"|");
			
			echo "<tr><td width=\"380px\"><a href=\"eventDetail.php?id=".$eventDataArray['ID']."\">".$eventDataArray['title']."</a> - ".$date;
			
			if($eventDataArray['dateAwarded'] != "0000-00-00")
			{
				echo " <b>Hours Awarded</b>";
			}
			
			echo "</td>";
			echo "<td>Attend: ".$attending." Not Attend: ".$notAttending." MIA: ".$invited." ?: ".$limbo."</td></tr>";
			$count++;
		}
		
		if($count == 0){
			echo "<p>No Events Scheduled</p>";
		}
		
		echo "</table>";
		echo "<p><a href=\"javascript:MM_openBrWindow('AddCalEvent.php?type=".$type."','','width=500,height=400, scrollbars=1');\">Click here</a> to add an event.<br>";
		if($type == "communityService" || $type == "house"){
			echo "<a href=\"serviceHourForm.php\">Click here</a> to edit service hours.</p>";
		}
	?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>