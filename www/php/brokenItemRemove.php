<?php
session_start();
$authUsers = array('admin', 'houseManager');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$ID = $_GET[id];

$add_sql = "DELETE FROM brokenStuff WHERE ID='$ID'";

$add_res = mysqli_query($mysqli, $add_sql);

header("location: ../manageBrokenItems.php");

?>