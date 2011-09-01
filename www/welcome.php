<?php
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php include_once('php/login.php');
			$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
			$userData = "
				SELECT * 
				FROM members 
				WHERE username='".$_SESSION['username']."'";
	
			$getUserData = mysqli_query($mysqli, $userData);
	
			$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);
			
			echo "<p>Hello ".$userDataArray['firstName']."!</p>";
			echo"<p>Academic System Menu:</p>
				<ul>
				<li><a href=\"schedule.php\">Vew Schedule Info</a></li>";
				
			if($userDataArray['accountType']=="admin"){echo "
				<li><a href=\"addUserForm.php\">Add a user</a></li>";
			}
			echo "<li><a href=\"addClassForm.php\">Add a class</a></li>
				<li><a href=\"classSearchForm.php\">Search a class</a></li>
				</ul>";
			
			$communityService = "
				SELECT *
				FROM members
				WHERE accountType='communityService'";
				
			$getCommunityService = mysqli_query($mysqli, $communityService);
			
			$communityServiceArray = mysqli_fetch_array($getCommunityService, MYSQLI_ASSOC);
			echo "<p>";
			echo "Service hours: ".$userDataArray['serviceHours']." &nbsp;&nbsp;<a href=\"mailto:".$communityServiceArray['email']."\">Disagree?</a>";
			echo "<br>";
			echo "House hours: ".$userDataArray['houseHours']." &nbsp;&nbsp;Disagree?";
			echo "<br>";
			echo "</p>";

	$time = time() + (60 * 60 * 24 * 7)*0;
	$now = date("Y-m-d");
	
	$ServiceEvents = "
		SELECT * 
		FROM events 
		WHERE type='communityService'
		AND dateAdded <='".$now."'
		AND eventDate >='".$now."'";
	
	$getServiceEvents = mysqli_query($mysqli, $ServiceEvents);
	
	$first=true;
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
			echo "<tr><td><form method=\"post\" action=\"php/eventSignup.php\">";
			echo " ".$serviceEventsArray['title']." - ".$date." ".$serviceEventsArray['time']."<br>";
			echo " <input type=\"radio\" name=\"attending\" value=\"attending\" /> Attending <input type=\"radio\" name=\"attending\" value=\"notAttending\" /> Not Attending";
			echo " <input type=\"hidden\" name=\"eventID\" value=\"".$serviceEventsArray['ID']."\" />";
			echo " <input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" /></form></td></tr>";
			if($first == true){
				$first = false;
			}
		}
	}
	if($first == false){
		echo "</table>";
	}
	
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
	
	while ($serviceEventsArray = mysqli_fetch_array($getServiceEvents, MYSQLI_ASSOC)){
		
		$invited = strpos($serviceEventsArray['invited'], $_SESSION['username']);
		$attending = strpos($serviceEventsArray['attending'], $_SESSION['username']);
		$notAttending = strpos($serviceEventsArray['notAttending'], $_SESSION['username']);
		
		if($attending != NULL){
			$date = $serviceEventsArray['eventDate'];
			$date = strtotime($date);;
			$date = date("D n/j", $date);
			
			if($first == true){
				echo "<table>\n<tr><td><h4>My Schedule:</h4></td></tr> \n";
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
	
	if($userDataArray['accountType']=="admin" || $userDataArray['accountType']=="communityService"){
		echo "<p><a href=\"manageCommunityService.php\">Community service control pannel</a></p>";
	} ?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>