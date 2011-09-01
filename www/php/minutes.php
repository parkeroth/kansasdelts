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
$startTime = $_POST[startHour].":".$_POST[startMinute]." ".$_POST[startAmpm];
$endTime = $_POST[endHour].":".$_POST[endMinute]." ".$_POST[endAmpm];

$query = "
	INSERT INTO minutes
	(dateSubmitted, username, type,  dateMeeting, attendance, topics, startTime, endTime)
	VALUES ('".date('Y-m-d H:i:s')."', '$_SESSION[username]', '$type', '$meetingDate', '$_POST[attendance]', '$_POST[topics]', '$startTime', '$endTime')";

$doQuery = mysqli_query($mysqli, $query);


header("location: ../account.php");

?>