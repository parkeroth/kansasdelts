<?php

// This might not be in use

session_start();
$authUsers = array('admin', 'brotherhood', 'recruitment', 'secretary', 'communityService', 'social', 'houseManager', 'pledgeEd', 'homecoming', 'drm');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$add_sql = "DELETE FROM events WHERE ID='".$_GET['ID']."'";

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../account.php");
?>