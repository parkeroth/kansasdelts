<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'saa', 'honorBoard');
include_once('php/authenticate.php');

include_once('snippet/missedDuties.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$(".accept").click(function(){
		var id = $(this).attr('id');
		window.location = 'php/fines.php?action=accept&id=' + id;	
	});
	
	$(".reject").click(function(){
		var id = $(this).attr('id');
		window.location = 'php/fines.php?action=reject&id=' + id;	
	});
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2>Pending Missed Duties</h2>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$pendingFines = "
			SELECT f.ID, m.firstName, m.lastName, f.description, f.amount, f.description, f.date
			FROM fines f
			JOIN members m
			ON f.username = m.username
			WHERE status='pending'";
		$getPending = mysqli_query($mysqli, $pendingFines);
		
		$numRWR=0;
		$first=true;
		
		$month = date('n');
		$year = date('Y');
		
		if($month < 6){
			$startDate = "$year-01-01";
			$endDate = "$year-05-31";
		} else {
			$startDate = "$year-08-01";
			$endDate = "$year-12-31";
		}
				
		echo "<table>\n";
		while ($pendingArray = mysqli_fetch_array($getPending, MYSQLI_ASSOC)){
			$numRWR++;
			
			if($first)
			{
				echo "<tr style=\"font-weight: bold;\"><td width=\"120\">Member</td><td width=\"80\">Fine</td><td width=\"200\">Reason</td><td  width=\"120\">Date</td></tr>\n";
				$first = false;
			}
			
			$numOccurance = 1 + checkOccurance($mysqli, $pendingArray[type], $pendingArray[offender], $startDate, $endDate);
			
			echo "<tr>\n";
			echo "<td>$pendingArray[firstName] $pendingArray[lastName]</td><td>$$pendingArray[amount].00</td><td>$pendingArray[description]</td><td>$pendingArray[date]</td>";
			echo "<td>";
			echo "<input id=\"$pendingArray[ID]\" class=\"accept\" type=\"button\" value=\"Accept\" />";
			echo "<input id=\"$pendingArray[ID]\" class=\"reject\" type=\"button\" value=\"Reject\" />";
			echo "</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		
		if($numRWR == 0)
		{
			echo "<p>No pending fines.</p>";
		} ?>
		
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>		
		
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>