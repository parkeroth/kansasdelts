<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if($_POST['decision'] == "accept"){
	
	$eventData = "
			SELECT * 
			FROM events 
			WHERE ID='".$_POST['eventID']."'";
	
	$getEventData = mysqli_query($mysqli, $eventData);
	$eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
	
	$excused = $eventDataArray['excused'];
	$limbo = $eventDataArray['limbo'];
	$string = "|".$_SESSION['username'];
	
	$limbo = str_replace($string, "", $limbo);
	
	$modifyLimbo = "
		UPDATE events
		SET limbo = '".$limbo."'
		WHERE ID = '".$_POST['eventID']."'";
		
	$doLimboModification = mysqli_query($mysqli, $modifyLimbo);
	
	$excused = $excused.$string;
	
	$modifyExcused = "
		UPDATE events
		SET excused = '".$excused."'
		WHERE ID = '".$_POST['eventID']."'";
		
	$doExcusedModification = mysqli_query($mysqli, $modifyExcused);
	
	
	
	$removeMessage = "
		DELETE
		FROM messages
		WHERE ID = '".$_POST['messageID']."'";
		
	$doRemoveMessage = mysqli_query($mysqli, $removeMessage);
	
} else if($_POST['decision'] == "reject"){
	$eventData = "
			SELECT * 
			FROM events 
			WHERE ID='".$_POST['eventID']."'";
	
	$getEventData = mysqli_query($mysqli, $eventData);
	$eventDataArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
	
	$forced = $eventDataArray['forced'];
	$limbo = $eventDataArray['limbo'];
	$string = "|".$_SESSION['username'];
	
	$limbo = str_replace($string, "", $limbo);
	
	$modifyLimbo = "
		UPDATE events
		SET limbo = '".$limbo."'
		WHERE ID = '".$_POST['eventID']."'";
		
	$doLimboModification = mysqli_query($mysqli, $modifyLimbo);
	
	$forced = $forced.$string;
	
	$modifyForced = "
		UPDATE events
		SET forced = '".$forced."'
		WHERE ID = '".$_POST['eventID']."'";
		
	$doForcedModification = mysqli_query($mysqli, $modifyForced);
	
	
	$removeMessage = "
		DELETE
		FROM messages
		WHERE ID = '".$_POST['messageID']."'";
		
	$doRemoveMessage = mysqli_query($mysqli, $removeMessage);
}

header("location: ../eventDetail.php?id=".$_POST['eventID']);

?>