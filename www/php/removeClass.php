<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);


$MyClasses = "
		SELECT * 
		FROM classes 
		WHERE termSeason='".$_POST['season']."'
		AND termYear='".$_POST['year']."'
		AND username='".$_SESSION['username']."'";
	
	
$getMyClasses = mysqli_query($mysqli, $MyClasses); 
	
	
while ($classArray = mysqli_fetch_array($getMyClasses, MYSQLI_ASSOC)){
	$class = $classArray['department'].$classArray['section'];
	if($_POST[$class] == "remove"){
		$query = "DELETE FROM classes WHERE ID='".$classArray['ID']."'";
		$result = mysqli_query($mysqli, $query);
	}
}

header("location: ../schedule.php");

?>