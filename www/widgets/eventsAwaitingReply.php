<?php
	$time = time() + (60 * 60 * 24 * 7)*0;
	$now = date("Y-m-d");
	
	$ServiceEvents = "
		SELECT * 
		FROM events 
		WHERE dateAdded <='".$now."'
		AND eventDate >='".$now."'
		ORDER BY eventDate";
	
	$getServiceEvents = mysqli_query($mysqli, $ServiceEvents);
	$first=true;
	while ($serviceEventsArray = mysqli_fetch_array($getServiceEvents, MYSQLI_ASSOC)){
		
		// Get RSVP status of current user
		$query = "	SELECT status 
					FROM eventAttendance 
					WHERE username='$_SESSION[username]' 
					AND eventID='$serviceEventsArray[ID]'";
		$result = mysqli_query($mysqli, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$invited = false;
		
		// Set if invited	
		if($data[status] == 'invited'){
			$invited = true;
		}
		
		// Get number of people attending the event
		$query = "	SELECT COUNT(ID) AS num
					FROM eventAttendance 
					WHERE username='$_SESSION[username]' 
					AND eventID='$serviceEventsArray[ID]'
					AND status='attending'";
		$result = mysqli_query($mysqli, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$numAttending = $data[num];
		
		if($serviceEventsArray['maxAttendance'] > 0 && $numAttending >= $serviceEventsArray['maxAttendance'])
		{
			$openSpots = false;
		} else {
			$openSpots = true;	
		}
		
		if($invited && $openSpots){
			
			$date = $serviceEventsArray['eventDate'];
			$date = strtotime($date);
			$date = date("D n/j", $date);
			
			if($first == true){
				echo "<table><tr><td><h2>Events Awaiting Reply</h2></td></tr> \n";
			}
			
			echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php?source=account\">";
			echo " <b>".$serviceEventsArray['title']."</b> - ".$date." ".$serviceEventsArray['time']."<br>";
			
			if($serviceEventsArray['type'] == "house"){ //If it is a house event
				if(strpos($serviceEventsArray['limbo'], $_SESSION['username'])){ //If user is in limbo
					
					echo $serviceEventsArray['description']." <span class=\"redHeading\">Pending Moderation</span>";
					
				} else if(strpos($serviceEventsArray['forced'], $_SESSION['username'])){ //If user is in forced
					
					echo "<span style=\"color: red;\">Excuse Rejected!</span> ".$serviceEventsArray['description']."<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse for not attending: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"forced\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
			
				} else if(strpos($serviceEventsArray['excused'], $_SESSION['username'])){
					//Output nothing					   
				} else if($serviceEventsArray['mandatory'] == 1){
					
					echo $serviceEventsArray['description']."<br>";
					echo "<b>Mandatory</b><br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict <br>";
					echo " Excuse: <input type=\"text\" name=\"reason\" />";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"hidden\" name=\"type\" value=\"firstTime\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
				
				} else {
					
					echo $serviceEventsArray['description']."<br>";
					echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> See You There ";
					echo " <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> I Have A Conflict";
					echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
					echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
					echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
				
				}
			} else {
				
				echo $serviceEventsArray['description']."<br>";
				echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> Attending <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> Not Attending";
				echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
				echo " <input type=\"hidden\" name=\"mandatory\" value=\"".$serviceEventsArray['mandatory']."\" />";
				echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />";
			
			}
			
			echo "</form></td></tr>";
			echo "<tr><td>&nbsp;</td></tr>"; //Insert blank line for padding
			
			if($first == true){
				$first = false;
			}
		}
	}
	echo "</table>";
?>