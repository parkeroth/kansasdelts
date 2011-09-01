<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		$time = time() + (60 * 60 * 24 * 7)*0;
	$now = date("Y-m-d");
	
	$ServiceEvents = "
		SELECT * 
		FROM events 
		WHERE dateAdded <='".$now."'
		AND eventDate >='".$now."'";
	
	$getServiceEvents = mysqli_query($mysqli, $ServiceEvents);
	
	$first=true;
	echo '<div style="float:right;">';
	while ($serviceEventsArray = mysqli_fetch_array($getServiceEvents, MYSQLI_ASSOC)){
		
		$invited = strpos($serviceEventsArray['invited'], $_SESSION['username']);
		$attending = strpos($serviceEventsArray['attending'], $_SESSION['username']);
		$notAttending = strpos($serviceEventsArray['notAttending'], $_SESSION['username']);
		
		if($invited === false){
			//Do Nothing
		} else if($attending === false && $notAttending === false) {
			
			$date = $serviceEventsArray['eventDate'];
			$date = strtotime($date);
			$date = date("D n/j", $date);
			
			if($first == true){
				echo "<table><tr><td><h4>Available events:</h4></td></tr> \n";
			}
			
			if($serviceEventsArray['type'] == "house"){ //If it is a house event
				if(strpos($serviceEventsArray['limbo'], $_SESSION['username'])){ //If user is in limbo
					echo "<tr><td>";
					echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
					echo $serviceEventsArray['description']." <span style=\"color: red;\">Pending Moderation</span>";
					echo "<p>&nbsp;</p>";
					echo "</td></tr>";
				} else if(strpos($serviceEventsArray['forced'], $_SESSION['username'])){ //If user is in forced
					echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
					echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
					echo "<span style=\"color: red;\">Excuse Rejected!</span> ".$serviceEventsArray['description']."<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse for not attending: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"forced\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /><p>&nbsp;</p></form></td></tr>";
				} else if(strpos($serviceEventsArray['excused'], $_SESSION['username'])){
					//Output nothing					   
				} else if($serviceEventsArray['mandatory'] == 1){
					echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
					echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
					echo $serviceEventsArray['description']."<br>";
					echo "<b>Mandatory</b><br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"firstTime\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /><p>&nbsp;</p></form></td></tr>";
				} else {
					echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
					echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
					echo $serviceEventsArray['description']."<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /><p>&nbsp;</p></form></td></tr>";
				}
			} else {
				echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
				echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
				echo $serviceEventsArray['description']."<br>";
				echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> Attending <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> Not Attending";
				echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
				echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
				echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /><p>&nbsp;</p></form></td></tr>";
			}
			if($first == true){
				$first = false;
			}
		}
	}
	echo "</table>";
	echo '</div>';
	$time = time() + (60 * 60 * 24 * 7)*0;
	$now = date("Y-m-d", $time);
	
	$ServiceEvents = "
		SELECT * 
		FROM events 
		WHERE type='communityService'
		AND dateAdded <='".$now."'
		AND eventDate >='".$now."'
		ORDER BY dateAdded ASC";
	
	$getServiceEvents = mysqli_query($mysqli, $ServiceEvents);
	
	$first = true;
	echo '<div style="float: left;">';
	
	while ($serviceEventsArray = mysqli_fetch_array($getServiceEvents, MYSQLI_ASSOC)){
		
		$invited = strpos($serviceEventsArray['invited'], $_SESSION['username']);
		$attending = strpos($serviceEventsArray['attending'], $_SESSION['username']);
		$notAttending = strpos($serviceEventsArray['notAttending'], $_SESSION['username']);
		
		if($attending != NULL){
			$date = $serviceEventsArray['eventDate'];
			$date = strtotime($date);;
			$date = date("D n/j", $date);
			
			if($first == true){
				echo "<table><tr><td><h4>My Schedule:</h4></td></tr> \n";
			}
			
			echo "<tr><td>".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."</td></tr>\n";
			
			if($first == true){
				$first = false;
				
			}
		}
	}
	
	if($first == false){
		echo "</table>\n";
	}
		echo '</div>';
	?>
    <div style="clear:both"><p>&nbsp;</p></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>