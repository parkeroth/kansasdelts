<?php
session_start();
$authUsers = array('admin', 'drm');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if(date("n") > 5)
{
	$year = date("Y")+1;
}
else
{
	$year = date("Y");
}

if($_GET[action] == "add")
{
	$add_sql = "INSERT INTO baddDutyLog (username, date, year) VALUES ('$_SESSION[username]', '$_GET[date]', '$year')";
}
else if($_GET[action] == "remove")
{
	$add_sql = "DELETE FROM baddDutyLog WHERE date='$_GET[date]' AND username='$_SESSION[username]'";
}
else if($_GET[action] == "cancel")
{
	$add_sql = "DELETE FROM `messages` WHERE `from`='$_SESSION[username]' AND `type`='baddTrade' ";
}

$add_res = mysqli_query($mysqli, $add_sql);

$month = date("n", strtotime($_GET['date']))+1;
$year = date("Y", strtotime($_GET['date']));

header("location: ../baddDutyDates.php?month=$month&year=$year");

?>