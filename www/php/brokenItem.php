<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$date = date("Y-m-d");

$add_sql = "INSERT INTO brokenStuff (item, description, dateReported, reportedBy) VALUES ('$_POST[item]', '$_POST[description]', '$date', '$_SESSION[username]')";

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../account.php?from=brokenItem");

?>