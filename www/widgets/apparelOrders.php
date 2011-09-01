<?php
		
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$orderQuery = "
		SELECT ID, name, dueDate
		FROM apparelOrders
		WHERE dueDate >= '".date("Y-m-d")."'
		AND status = 'concept'
		AND ID NOT IN (
			SELECT orderID
			FROM apparelLog
			WHERE username = '$_SESSION[username]'
		)";
	$getOrders = mysqli_query($mysqli, $orderQuery);
	
	$firstAvailable = true;
	$firstOrdered = true; 
	
	while ($orderArray = mysqli_fetch_array($getOrders, MYSQLI_ASSOC)){
		
		
			if($firstAvailable){
				echo "<h2>Available T-Shirts</h2>\n";	
				$firstAvailable = false;		
			}
			
			echo "<p><a class=\"avaiableShirt\" id=\"$orderArray[ID]\" href=\"#\">
						$orderArray[name]</a> - ".date("D n/j", strtotime($orderArray[dueDate]))."</p>";
		
	}
	
	$statusQuery = "
		SELECT paid, orderID, name
		FROM apparelLog
		JOIN apparelOrders
		ON orderID = apparelOrders.ID
		WHERE username = '$_SESSION[username]'
		AND pickedUp != '1'";
	$getStatus = mysqli_query($mysqli, $statusQuery);
	
	while($statusArray = mysqli_fetch_array($getStatus, MYSQLI_ASSOC)) {
		
		if($firstOrdered){
			echo "<h2>Your Orders</h2>\n";	
			$firstOrdered = false;		
		}
		
		if($statusArray[paid]){
			$status = 'Paid';
		} else {
			$status = 'Not Paid';
		}
		
		echo "<p><a class=\"orderedShirt\" id=\"$statusArray[orderID]\" href=\"#\">
					$statusArray[name]</a> - $status</p>";
	}
?>	