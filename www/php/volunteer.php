<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$type = $_GET['type'];
$action = $_GET[action];
$date = date("Y-m-d");

if($action == "add")
{
	$add_sql = "INSERT INTO volunteer (username, type, dateVolunteered) VALUES ('".$_SESSION[username]."', '$type', '$date')";
}
else if($action == "remove")
{
	$add_sql = "DELETE FROM volunteer WHERE username='".$_SESSION[username]."' AND type='$type'";
}

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../account.php");

?>