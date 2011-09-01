<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('authenticate.php');
	
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$date = date("Y-m-d");

$userData = "
	SELECT username 
	FROM members
	WHERE residency != 'limbo'
	ORDER BY lastName";
$getUserData = mysqli_query($mysqli, $userData);

$memberCount = 0;
while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
{
	$members[$memberCount]['username'] = $userDataArray['username'];
	$memberCount++;
}

for($i = 0; $i < $memberCount; $i++)
{
	if( $_POST[$members[$i]['username']] != "present" )
	{
		$status = $_POST[$members[$i]['username']];
		
		$modify = "INSERT INTO attendance
			(username, status, date)
			VALUES
			('".$members[$i]['username']."', '$status', '$date')";
		$doModification = mysqli_query($mysqli, $modify);
	}
}

header("location: ../account.php");
?>