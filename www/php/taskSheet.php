<?php


session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$positionData = "
	SELECT * 
	FROM positions
	ORDER BY ID";
$getPositionData = mysqli_query($mysqli, $positionData);

while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
{
	
	if(strpos($session->accountType, $positionDataArray['type']))
	{
		$type = $positionDataArray['type'];
	}

}

$meetingDate = date('Y-m-d', strtotime($_POST[dateMeeting]));

$check = "SELECT ID 
	FROM reports
	WHERE type = '$type'
	AND dateMeeting = '$meetingDate'";
$checkTable = mysqli_query($mysqli, $check);

if(!mysqli_fetch_row($checkTable))
{
	$query = "
		INSERT INTO reports
		(dateSubmitted, username, type, completed, dateMeeting, task, discussion, projectsGoals, agenda)
		VALUES ('".date('Y-m-d H:i:s')."', '$_SESSION[username]', '$type', 'na', '$meetingDate', '$_POST[task]', '$_POST[discussion]', '$_POST[projectsGoals]', '$_POST[agenda]')";
	
	$doQuery = mysqli_query($mysqli, $query);
}


header("location: ../account.php");

?>