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
	
	$(".eval").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('snippet/dutyPopup.php?type=eval&ID=' + id, function(data){
			$("#popupBody").html(data);
			
			$(".auth").click(function(){
				window.location = 'php/missedDuty.php?type=auth&status=approved&id=' + id;
			});
			
			$(".reject").click(function(){
				window.location = 'php/missedDuty.php?type=auth&status=rejected&id=' + id;
			});
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

<h2>Pending Missed Duties</h2>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$pendingMisses = "
			SELECT l.ID, t.name, m.firstName, m.lastName, l.dateOccured, l.type, l.offender
			FROM infractionLog l
			JOIN infractionTypes t
			ON l.type = t.code
			JOIN members m
			ON l.offender = m.username
			WHERE status='pending'";
		$getPending = mysqli_query($mysqli, $pendingMisses);
		
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
				echo "<tr style=\"font-weight: bold;\"><td width=\"150\">Party Responible</td><td width=\"160\">Date of Occurance</td><td>Type</td><td>Occurance #</td><td></td></tr>\n";
				$first = false;
			}
			
			$numOccurance = 1 + checkOccurance($mysqli, $pendingArray[type], $pendingArray[offender], $startDate, $endDate);
			
			echo "<tr>\n";
			echo "<td>$pendingArray[firstName] $pendingArray[lastName]</td><td>$pendingArray[dateOccured]</td><td>$pendingArray[name]</td><td style=\"text-align:center;\">$numOccurance</td>";
			echo "<td><input id=\"$pendingArray[ID]\" class=\"eval\" type=\"button\" value=\"Evaluate\" /></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		
		if($numRWR == 0)
		{
			echo "<p>No pending missed duties.</p>";
		} ?>
		
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>		
		
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>