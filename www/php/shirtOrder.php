<?php
session_start();
$authUsers = array('brother');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

if($_POST[action] == 'add')
{
	$query = "SELECT ID FROM apparelLog WHERE username = '$_SESSION[username]' AND orderID = '$_POST[ID]'";
	$getData = mysqli_query($mysqli, $query);
	
	if ($data = mysqli_fetch_array($getData, MYSQLI_ASSOC)) 
	{
		
		$add_sql = "UPDATE apparelLog 
					SET paid = '0', pickedUp = '0', small = '$_POST[small]', medium = '$_POST[medium]', large = '$_POST[large]', xLarge = '$_POST[xLarge]', xxLarge = '$_POST[xxLarge]'
					WHERE ID = '$data[ID]'";
	
	} else {
		
		$add_sql = "INSERT INTO apparelLog (orderID, username, paid, pickedUp, small, medium, large, xLarge, xxLarge) 
					VALUES ('$_POST[ID]', '$_SESSION[username]', '0', '0', '$_POST[small]', '$_POST[medium]', '$_POST[large]', '$_POST[xLarge]', '$_POST[xxLarge]')";
			
	}
		
	$add_res = mysqli_query($mysqli, $add_sql);
	
	header("location: ../account.php");
	
}
else if($_POST[action] == 'remove')
{
	
	$query = "DELETE FROM apparelLog WHERE ID = '$_POST[id]'";
	$getData = mysqli_query($mysqli, $query);
	
	header("location: ../account.php");
}
else if($_POST[action] == 'placeOrder')
{
	
	$query = "UPDATE apparelOrders SET status = 'ordered' WHERE ID = '$_POST[id]'";
	$getData = mysqli_query($mysqli, $query);
	
	header("location: ../manageApparelOrders.php");
}
else if($_POST[action] == 'update')
{
	$orderQuery = "
		SELECT username
		FROM apparelLog
		WHERE orderID = '$_POST[id]'";
	$getOrders = mysqli_query($mysqli, $orderQuery);
	
	while($orderArray = mysqli_fetch_array($getOrders, MYSQLI_ASSOC)) {
		
		if( in_array($orderArray[username], $_POST[paid]) ){
			$paid = 1;
		} else {
			$paid = 0;
		}
		
		if( in_array($orderArray[username], $_POST[pickedUp]) ){
			$pickedUp = 1;
		} else {
			$pickedUp = 0;
		}
		
		$query = "UPDATE apparelLog SET paid = '$paid', pickedUp = '$pickedUp' WHERE username = '$orderArray[username]' AND orderID = '$_POST[id]'";
		$updateData = mysqli_query($mysqli, $query);
		
	}
	
	
	header("location: ../manageApparelOrders.php");
}



?>