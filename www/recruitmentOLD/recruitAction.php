<?php
session_start();
$authUsers = array('recruitment', 'admin');
include_once('../php/authenticate.php');
include_once('../php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$action = mysql_real_escape_string($_GET[action]);
$id = mysql_real_escape_string($_GET[id]);
$value = mysql_real_escape_string($_GET[value]);

if($action == 'assign')
{
	$query = "	UPDATE recruits 
				SET primaryContact = '$value'
				WHERE ID = '$id'";
	$result = mysqli_query($mysqli, $query);
	
	$query = "	INSERT INTO recruitCalls (
				dateRequested, memberID, recruitID,
				type, status)
				VALUES (
				'".date('Y-m-d')."', '$value', '$id',
				'initial', 'pending')";
	$result = mysqli_query($mysqli, $query);
	
	header("location: newList.php");
	
} else if($action == 'remove') {
	
	$query = "	DELETE
				FROM recruits 
				WHERE ID = '$id'";
	$result = mysqli_query($mysqli, $query);
	
	header("location: newList.php");
	
}


?>