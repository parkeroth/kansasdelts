<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'drm');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1 align="center">Sober Gent Events</h1>
	<p>Future events and events from the last 30 days that require sober gents are listed below.</p>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$dateConstraint = date("Y-m-d", time()-2592000); //Find date of one month ago
		
		$eventData = "
			SELECT * 
			FROM soberGentEvents 
			WHERE eventDate > '$dateConstraint'";
		$getEventData = mysqli_query($mysqli, $eventData);
		
		echo "<table align=\"center\" style=\"text-align: center;\" width=\"80%\">";
		
		$first = true;
		
		$count=0;
		while($eventArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC)){
			
			if($first)
			{
				echo "<tr style=\"font-weight: bold;\"><td>Title</td><td>Event Date</td><td>Gents Assigned</td><td>&nbsp;</td></tr>";
				$first = false;
			}
			
			$query = "
				SELECT COUNT(soberGentLog.ID) AS numberOfGents
				FROM soberGentLog
				RIGHT JOIN soberGentEvents
				ON soberGentLog.eventID = soberGentEvents.ID
				WHERE soberGentEvents.ID = '$eventArray[ID]'
				GROUP BY soberGentLog.eventID";
			
			$result = mysqli_query($mysqli, $query);
			
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$numGents = $row[numberOfGents];
			
			echo "<tr>\n";
			echo "<td>".$eventArray[title]."</td>\n";
			echo "<td>".$eventArray[eventDate]."</td>\n";
			echo "<td>$numGents</td>\n";
			echo "<td>	<input type=\"button\" value=\"Edit\" ONCLICK=\"window.location.href='editSoberGentEvent.php?id=$eventArray[ID]'\"></td>\n";
			echo "</tr>\n";
			
			$count++;
		}
		
		if($count == 0){
			echo "<p align=\"center\">No Events Scheduled</p>";
		}
		
		echo "</table>"
	?>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>