<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'treasurer');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$("a.order").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/popupBody.php?type=viewOrder&ID=' + id, function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
		
	//CLOSING  POPUP
	//Click the x event!
	$('#popupClose').click(function(){
		disablePopup('#generalPopup');
	});
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#generalPopup');
	});
});

</script>


<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$oderData = "
			SELECT * 
			FROM apparelOrders 
			ORDER BY dueDate ASC";
		$getOrderData = mysqli_query($mysqli, $oderData);
	?>
		
		<h2 style="text-align: center;">Apparel Orders</h2>
		
		<table align="center">
	<?php
		$first = true;
		
		while($orderArray = mysqli_fetch_array($getOrderData, MYSQLI_ASSOC)){
			
			if($first) {
				echo "<tr style=\"font-weight: bold;\"><td width=\"120\">Name</td><td>Totals</td><td width=\"80\">Status</td><td>&nbsp;</tr>";
				$first = false;
			}
			
			echo "<tr>";
			
			echo "<td><a class=\"order\" href=\"#\" id=\"$orderArray[ID]\">$orderArray[name]</a></td>";
			
			$totalQuery = "
				SELECT SUM(small) AS small, SUM(medium) AS medium, SUM(large) AS large, SUM(xLarge) AS xLarge, SUM(xxLarge) AS xxLarge
				FROM apparelOrders
				JOIN apparelLog
				ON apparelOrders.ID = apparelLog.orderID
				WHERE apparelOrders.ID = '$orderArray[ID]'";
			$getTotals = mysqli_query($mysqli, $totalQuery);
			$totalArray = mysqli_fetch_array($getTotals, MYSQLI_ASSOC);
			
			$totalQuery2 = "
				SELECT SUM(small) AS small, SUM(medium) AS medium, SUM(large) AS large, SUM(xLarge) AS xLarge, SUM(xxLarge) AS xxLarge
				FROM apparelOrders
				JOIN apparelLog
				ON apparelOrders.ID = apparelLog.orderID
				WHERE apparelOrders.ID = '$orderArray[ID]'
				AND apparelLog.pickedUp = '1'";
			$getTotals2 = mysqli_query($mysqli, $totalQuery2);
			$pickedUpArray = mysqli_fetch_array($getTotals2, MYSQLI_ASSOC);
			// TODO: Change to make it number of shirts remaining once order is submitted
			
			?>
			<td>
				Small: <?php echo $totalArray[small] - $pickedUpArray[small]; ?> <br />
				Medium: <?php echo $totalArray[medium] - $pickedUpArray[medium]; ?> <br />
				Large: <?php echo $totalArray[large] - $pickedUpArray[large]; ?> <br />
				X Large: <?php echo $totalArray[xLarge] - $pickedUpArray[xLarge]; ?> <br />
				XX Large: <?php echo $totalArray[xxLarge] - $pickedUpArray[xxLarge]; ?>
			</td>
			
			<?php
			
			echo "<td>".ucwords($orderArray[status])."</td>"; ?>
			<td><input type="button" value="Remove" name="remove" 
						onclick="window.location = 'php/removeApparelOrder.php?id=<?php echo $orderArray[ID]?>'" /></td>
			
			<?php echo "</tr>";
			
			echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
			
			$count++;
		}
		
		if($first){
			echo "<p style=\"text-align: center;\">No Orders to Manage</p>";
		}
		
		echo "</table>"
	?>
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>