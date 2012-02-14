<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';	

include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$username = $_POST[user];

$member = new Member(NULL, $username);

if($_POST[action] == "award")
{
	$hours = $_POST[hours];
	
	$dateAdded = date("Y-m-d");
	$year = date("Y");
	$month = date("n");
	
	if($month < 6)
	{
		$term = "spring";
	}
	else
	{
		$term = "fall";
	}
	
	$add_sql = "INSERT INTO hourLog (member_id, term, year, hours, type, eventID, dateAdded) VALUES ('$member->id', '$term', '$year', '$hours', 'house', '-1', '$dateAdded')";
	$add_res = mysqli_query($mysqli, $add_sql);
	
	$add_sql = "DELETE FROM volunteer WHERE username='$username' AND type='house'";
	$add_res = mysqli_query($mysqli, $add_sql);
}
else if($_POST[action] == "remove")
{	
	$add_sql = "DELETE FROM volunteer WHERE username='$username' AND type='house'";
	$add_res = mysqli_query($mysqli, $add_sql);
}

header("location: ../manageVolunteers.php");

?>