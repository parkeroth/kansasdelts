<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$hours = $_POST[hours];
$ID = $_POST[ID];
$date = date("Y-m-d");	 
			 
$eventData = "
	SELECT * 
	FROM events
	WHERE ID='$ID'";
$getEventData = mysqli_query($mysqli, $eventData);		
$eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);

if($eventDataArray['type'] == "communityService"){
	$hourType = "serviceHours";
} else if($eventDataArray['type'] == "house"){
	$hourType = "houseHours";
} else if($eventDataArray['type'] == "philanthropy"){
	$hourType = "philanthropyHours";
}

$rawTerm = $eventDataArray[term];
if(strpos($rawTerm, "fall") > -1)
{
	$term = "fall";
	$year = substr($rawTerm, 4, 4);
}
else if(strpos($rawTerm, "spring") > -1)
{
	$term = "spring";
	$year = substr($rawTerm, 6, 4);
}

$userData = "
	SELECT * 
	FROM members
	ORDER BY lastName";
$getUserData = mysqli_query($mysqli, $userData);

$memberCount = 0;
while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
{
	$members[$memberCount]['username'] = $userDataArray['username'];
	$members[$memberCount]['firstName'] = $userDataArray['firstName'];
	$members[$memberCount]['lastName'] = $userDataArray['lastName'];
	$members[$memberCount]['accountType'] = $userDataArray['accountType'];
	$members[$memberCount]['class'] = $userDataArray['class'];
	$members[$memberCount]['major'] = $userDataArray['major'];
	$memberCount++;
}

for($i = 0; $i < $memberCount; $i++)
{
	if( isset($_POST[$members[$i]['username']]) && $_POST[$members[$i]['username']] == "true" )
	{
		$modify = "INSERT INTO hourLog
			(username, term, year, hours, type, eventID, dateAdded)
			VALUES
			('".$members[$i]['username']."', '$term', '$year', '$hours', '$hourType', '$ID', '$date')";
		$doModification = mysqli_query($mysqli, $modify);
	}
}

$modify = "UPDATE events
			SET dateAwarded = '".date('Y-m-d')."'
			WHERE ID = '".$_GET['id']."'";
$doModification = mysqli_query($mysqli, $modify);

header("location: ../manageEvents.php?type=".$eventDataArray['type']);
?>