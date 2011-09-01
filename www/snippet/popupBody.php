<?php
$authUsers = array('brother');
include_once('../php/authenticate.php');

include_once('../php/login.php');

//Sets $year and $term vars
	
	$type = $_GET[type];
	
	if($type == 'hours'){
	
		$hoursQuery = "
			SELECT eventID, hours, hourLog.dateAdded AS dateAdded, notes, firstName, lastName
			FROM hourLog 
			JOIN members 
			ON hourLog.username = members.username
			WHERE type='$_GET[hourType]'
			AND term ='$_GET[term]'
			AND year = '$_GET[year]'
			AND hourLog.username = '$_GET[username]'";
		$getEventData = mysqli_query($mysqli, $hoursQuery);
		
		$hours = 0;
		
		echo "<h2>Member Records</h2>";
		?>
		
		<table style="text-align:center;" align="center">
		<?php
		echo "<tr style=\"text-align: center; font-weight: bold;\"><td>Event</td><td>Change</td><td>Date</td></tr>\n";
		
		$count=0;
		while($serviceHourArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC))
		{
			echo "<tr>\n";
			
			if($serviceHourArray[eventID] == 0)
			{
				echo "<td>$serviceHourArray[notes]</td>\n";
			}
			else if($serviceHourArray[eventID] == -1)
			{
				echo "<td>Volunteer Task</td>\n";
			}
			else
			{
				$eventQuery = "
					SELECT title 
					FROM events 
					WHERE ID='$serviceHourArray[eventID]'";
				$getEventQuery = mysqli_query($mysqli, $eventQuery);
				$eventData = mysqli_fetch_array($getEventQuery, MYSQLI_ASSOC);
			
			echo "<td>$eventData[title]</td>\n";
			}
			
			echo "<td>$serviceHourArray[hours]</td>\n";
			echo "<td>$serviceHourArray[dateAdded]</td>\n";
			echo "</tr>\n";
			$count++;
		}
		if($count == 0)
		{
			echo "<tr><td colspan=\"2\"><p>No Records</p></td></tr>\n";
		}
		?> </table> <?php
		
	} 
	else if($type == 'new')
	{
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$orderQuery = "
			SELECT *
			FROM apparelOrders
			WHERE ID = '$_GET[ID]'";
		$getOrder = mysqli_query($mysqli, $orderQuery);
		$orderArray = mysqli_fetch_array($getOrder, MYSQLI_ASSOC);
		?>
		
		<h2><?php echo $orderArray[name]; ?></h2>
		
		<p><img width="400" src="/proofs/<?php echo $orderArray[ID].$orderArray[proofExt]; ?>" /></p>
		<p><?php echo $orderArray[description]; ?></p>
		
		
		<form name="shirtOrder" action="php/shirtOrder.php" method="POST">
			<table align="center" style="text-align:center;">
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<th>Small: </th>
					<td><select name="small">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Medium: </th>
					<td><select name="medium">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Large: </th>
					<td><select name="large">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>X Large: </th>
					<td><select name="xLarge">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>XX Large: </th>
					<td><select name="xxLarge">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2"><input type="submit" name="submit"/></td></tr>
			</table>
			<input type="hidden" name="ID" value="<?php echo $_GET[ID]; ?>" />
			<input type="hidden" name="action" value="add" />
		</form>
		<?php
		
	} else if($type == 'order'){
		
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$itemQuery = "
			SELECT *
			FROM apparelOrders
			WHERE ID = '$_GET[ID]'";
		$getItem = mysqli_query($mysqli, $itemQuery);
		$itemArray = mysqli_fetch_array($getItem, MYSQLI_ASSOC);
		
		$orderQuery = "
			SELECT *
			FROM apparelLog
			WHERE orderID = '$_GET[ID]'
			AND username = '$_SESSION[username]'";
		$getOrder = mysqli_query($mysqli, $orderQuery);
		$orderArray = mysqli_fetch_array($getOrder, MYSQLI_ASSOC);
		
		?>
		
		<h2><?php echo $itemArray[name]; ?></h2>
		<table align="center">
		<?php 
		if($orderArray[small] > 0) {
			echo "<tr><th>Small:</th><td>$orderArray[small]</td></tr>";
		}
		if($orderArray[medium] > 0) {
			echo "<tr><th>Medium:</th><td>$orderArray[medium]</td></tr>";
		}
		if($orderArray[large] > 0) {
			echo "<tr><th>Large:</th><td>$orderArray[large]</td></tr>";
		}
		if($orderArray[xLarge] > 0) {
			echo "<tr><th>X Large:</th><td>$orderArray[xLarge]</td></tr>";
		}
		if($orderArray[xxLarge] > 0) {
			echo "<tr><th>XX Large:</th><td>$orderArray[xxLarge]</td></tr>";
		}
		
		if($orderArray[$paid]) {$paid = 'Paid';}
		else {$paid = 'Not Paid';}
		
		echo "<tr><th>&nbsp;</th><td>&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\" style=\"text-align: center;\">$paid</td></tr>"; // YES ? NO
		
		
		if($itemArray[status] == 'concept') { ?>
			
			
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" style="text-align:center;">
				<form action="php/shirtOrder.php" method="POST">
					<input type="submit" name="remove" value="Remove" />
					<input type="hidden" name="action" value="remove" />
					<input type="hidden" name="id" value="<?php echo $orderArray[ID] ; ?>" /> 
				</form>
					</td></tr>
		
		</table>
		
	<?php
		}
	} else if($type == 'viewOrder'){
		
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$itemQuery = "
			SELECT *
			FROM apparelOrders
			WHERE ID = '$_GET[ID]'";
		$getItem = mysqli_query($mysqli, $itemQuery);
		$itemArray = mysqli_fetch_array($getItem, MYSQLI_ASSOC);
		
		$orderQuery = "
			SELECT firstName, lastName, SUM(small) AS small, SUM(medium) AS medium, SUM(large) AS large, SUM(xLarge) AS xLarge, SUM(xxLarge) AS xxLarge, members.username AS username, paid, pickedUp
			FROM members
			JOIN apparelLog
			ON members.username = apparelLog.username
			WHERE orderID = '$_GET[ID]'
			GROUP BY apparelLog.username
			ORDER BY lastName";
			
		$getOrders = mysqli_query($mysqli, $orderQuery); ?>
		
		<h2><?php echo $itemArray[name]; ?></h2>
		
		
		<table align="center" cellspacing="0">
		<form action="php/shirtOrder.php" method="POST">
		<?php
		
		$firstRow = true;
		$rowCount = 0;
		
		while($orderArray = mysqli_fetch_array($getOrders, MYSQLI_ASSOC)) {
			
			if($firstRow){
				echo "<tr class=\"heading\">";
				echo "<td>Name</td><td>Quantity</td>";
				if($itemArray[status] == 'ordered') {
					echo "<td width=\"60\">Paid</td><td width=\"80\">Picked Up</td>";
				}
				echo "</tr>";
				$firstRow = false;
			}
			
			if($rowCount % 2 == 0) {
				echo "<tr class=\"black\">";
			} else {
				echo "<tr class=\"white\">";
			}
			
			echo "<td style=\"text-align: right;\" valign=\"top\">$orderArray[firstName] $orderArray[lastName]</td>";
			echo "<td style=\"text-align: center;\">";
			
			if($orderArray[small] > 0) {
				echo "S: $orderArray[small]<br />";
			}
			if($orderArray[medium] > 0) {
				echo "M: $orderArray[medium]<br />";
			}
			if($orderArray[large] > 0) {
				echo "L: $orderArray[large]<br />";
			}
			if($orderArray[xLarge] > 0) {
				echo "XL: $orderArray[xLarge]<br />";
			}
			if($orderArray[xxLarge] > 0) {
				echo "XXL: $orderArray[xxLarge]<br />";
			}
			
			echo "</td>";
			
			if($itemArray[status] == 'ordered') {
				
				if($orderArray[paid]){
					$paidChecked = 'checked="checked"';
				} else {
					$paidChecked = '';
				}
				
				if($orderArray[pickedUp]){
					$pickedUpChecked = 'checked="checked"';
				} else {
					$pickedUpChecked = '';
				}
				
				echo "<td style=\"text-align: center; padding-top: 3px;\"><input type=\"checkbox\" name=\"paid[]\" value=\"$orderArray[username]\" $paidChecked /></td>";
				echo "<td style=\"text-align: center; padding-top: 3px;\"><input type=\"checkbox\" name=\"pickedUp[]\" value=\"$orderArray[username]\" $pickedUpChecked /></td>";
			}
			
			echo "</tr>";
			$rowCount++;
		}
		
		if($itemArray[status] == 'concept') { ?>
			
			
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2" style="text-align:center;">
				<form action="php/shirtOrder.php" method="POST">
					<input type="submit" name="remove" value="Place Order" />
					<input type="hidden" name="action" value="placeOrder" />
					<input type="hidden" name="id" value="<?php echo $_GET[ID] ; ?>" /> 
				</form>
					</td></tr>
		<?php
		
		} else if($itemArray[status] == 'ordered') { ?>
		
			<tr><td colspan="4">&nbsp;</td></tr>
			<tr><td colspan="4" style="text-align:center;">
					<input type="submit" name="update" value="Update Records" />
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="id" value="<?php echo $_GET[ID] ; ?>" /> 
			</td></tr>
		<?php } ?>
		
		 </form>
		</table>
		
	<?php	
	}

?>