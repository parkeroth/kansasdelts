<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$action = $_GET[action];

if($action == "remove")
{
	$add_sql = "DELETE FROM attendance WHERE ID='".$_GET[ID]."'";
} else if($action == "toggle") {
	
	if($_GET[status] == 'absent') {
		$status = 'excused';
	} else {
		$status = 'absent';
	}
	
	$add_sql = "UPDATE attendance SET status = '$status' WHERE ID = '$_GET[ID]'";
} else if($action = "add") {
	
	$add_sql = "INSERT INTO attendance (username, status, date) VALUES ('$_POST[name]', '$_POST[status]', '$_POST[date]')";
	
}

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../attendanceRecords.php");

?>