<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
include_once('login.php');

$ID = $_GET['id'];

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$db_connection = mysql_connect ($db_host, $db_username, $db_password) OR die (mysql_error());  
$db_select = mysql_select_db ($db_database) or die (mysql_error());
$db_table = $TBL_PR . "events";

$query = "SELECT * FROM $db_table WHERE ID='$_GET[id]' LIMIT 1";
$query_result = mysql_query ($query);
while ($info = mysql_fetch_array($query_result)){
	
	$modify = "	UPDATE eventAttendance
				SET status='invited'
				WHERE eventID = '$ID'
				AND username = '$_SESSION[username]'";

	$doModify = mysqli_query($mysqli, $modify);
	
	header("location: ../viewCalEvent.php?ID=$ID&status=mia");
}
?>