<?php
session_start();
$authUsers = array('admin', 'drm', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
	
include_once('../php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if($_GET[action] == "add")
{
	$add_sql = "INSERT INTO baddDutyDays (date, numPeople, status) VALUES ('$_GET[date]', '2', 'open')";
	$add_res = mysqli_query($mysqli, $add_sql);
}
else if($_GET[action] == "remove")
{
	$add_sql = "DELETE FROM baddDutyDays WHERE date='$_GET[date]'";
	$add_res = mysqli_query($mysqli, $add_sql);
	
	$add_sql = "DELETE FROM baddDutyLog WHERE date='$_GET[date]'";
	$add_res = mysqli_query($mysqli, $add_sql);
	
	$add_sql = "DELETE FROM messages WHERE type='baddTrade' AND (content='$_GET[date]' OR content='$_GET[date]')";
	$add_res = mysqli_query($mysqli, $add_sql);
}


$month = date("n", strtotime($_GET['date']))+1;
$year = date("Y", strtotime($_GET['date']));

header("location: baddDutyDates.php?month=$month&year=$year");

?>