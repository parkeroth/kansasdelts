<?php
session_start();
$authUsers = array('treasurer', 'admin');
include_once('authenticate.php');
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$action = $_GET[action];

if($action == 'accept')
{
	$query = "	UPDATE fines SET status='applied' WHERE ID = '$_GET[id]'";
	$result = mysqli_query($mysqli, $query);
	
	header("location: ../manageFines.php");
	
}
else if($action == 'reject')
{
	$query = "	UPDATE fines SET status='rejected' WHERE ID = '$_GET[id]'";
	$result = mysqli_query($mysqli, $query);
	
	header("location: ../manageFines.php");
	
}


?>