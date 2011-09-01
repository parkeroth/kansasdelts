<?php
		
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	$time = time() + (60 * 60 * 24 * 7)*0;
	$now = date("Y-m-d");
	
	// Types of events that have no attendance settings 
	$generalEvents = array(
		'general',
		'education',
		'recruitment',
		'pr');
	
	$ServiceEvents = "
		SELECT * 
		FROM events 
		WHERE dateAdded <='".$now."'
		AND eventDate >='".$now."'
		ORDER BY eventDate";
	
	$getServiceEvents = mysqli_query($mysqli, $ServiceEvents);
	
	$displayToday = true;
	$displayWeek = true;
	$displayMonth = true;
	
	echo "<h2>My Schedule</h2>\n";
	
	while ($serviceEventsArray = mysqli_fetch_array($getServiceEvents, MYSQLI_ASSOC)){
		$date = $serviceEventsArray['eventDate'];
		$date = strtotime($date);
		$date = date("D n/j", $date);
		
		// Get RSVP status of current user
		$query = "	SELECT status 
					FROM eventAttendance 
					WHERE username='$_SESSION[username]' 
					AND eventID='$serviceEventsArray[ID]'";
		$result = mysqli_query($mysqli, $query);
		$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$attending = false;
		
		// Set if attend	
		if($data[status] == 'invited'){
			$attending = true;
		} else if($data[status] == 'attending'){
			$attending = true;
		} else if($data[status] == 'notAttending'){
			$attending = false;
		}
		
		
		if(in_array($serviceEventsArray[type], $generalEvents)){
			$attending = true;
		}
		
		
		if($attending){
			if($serviceEventsArray['eventDate'] == date("Y-m-d")){
				if($displayToday){
					echo "<h4>Today: </h4>\n";
					$displayToday = false;
				}
				
				echo " <a href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=";
				echo $serviceEventsArray['ID']."&status=attending','','width=500,height=220');\">";
				echo $serviceEventsArray['title']."</a> - ".$date." - ".$serviceEventsArray['time']."<br>\n";
			}
			if( strtotime("today") < strtotime($serviceEventsArray['eventDate']) && strtotime($serviceEventsArray['eventDate'])  < strtotime("next Sunday") ){
				if($displayWeek){
					echo "<h4>This Week: </h4>\n";
					$displayWeek = false;
				}
				
				echo " <a href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=";
				echo $serviceEventsArray['ID']."&status=attending','','width=500,height=220');\">";
				echo $serviceEventsArray['title']."</a> - ".$date." - ".$serviceEventsArray['time']."<br>\n";
			}
			if( strtotime("next Sunday") < strtotime($serviceEventsArray['eventDate']) && strtotime($serviceEventsArray['eventDate'])  < strtotime("+30 Days") ){
				if($displayMonth){
					echo "<h4>Next 30 Days: </h4>\n";
					$displayMonth = false;
				}
				
				echo " <a href=\"javascript:MM_openBrWindow('viewCalEvent.php?ID=";
				echo $serviceEventsArray['ID']."&status=attending','','width=500,height=220');\">";
				echo $serviceEventsArray['title']."</a> - ".$date." - ".$serviceEventsArray['time']."<br>\n";
			}
		}
	}	
	if($displayToday && $displayWeek && $displayMonth){
		echo "<p>You have no upcoming events.</p>";
	}
	
	echo '<p>&nbsp;</p>';

?>