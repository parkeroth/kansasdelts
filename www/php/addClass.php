<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$department = $_POST['department'];
$department = strtoupper($department);
$add_sql = "INSERT INTO classes (username, termSeason, termYear, department, section, hours) VALUES ('".$_SESSION['username']."', '".$_POST['term']."', '".$_POST['year']."', '".$department."', '".$_POST['section']."', '".$_POST['hours']."')";

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../schedule.php");

?>