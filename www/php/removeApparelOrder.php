<?php
session_start();
$authUsers = array('admin');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$ID = $_GET[id];

$query = "	DELETE FROM apparelOrders WHERE ID = '$ID'";
	
$mysqli->query($query);


// SHOULD AT REMOVE PROOF IMAGE HERE


header("location: ../manageApparelOrders.php");

?>