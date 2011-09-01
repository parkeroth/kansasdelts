<?php
include_once('/php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
// Get number of members in roster not in limbo
function getMemberCount(){
	echo "1";
	$query = "SELECT COUNT(username) AS numUsers FROM members WHERE residency != 'limbo'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numMembers = $row->numUsers;
		
		mysqli_free_result($result);
		
		return $numMembers;
	
	} else {
		
		return -1;
		
	}
	
	echo "TAG".$numMembers;
}
	
?>