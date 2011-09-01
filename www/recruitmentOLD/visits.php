<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'recruitment');
include_once('../php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="/styles/popUp.css" rel="stylesheet" />
<link type="text/css" href="css/styles.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$("a.members").click(function(){
		
		$.get('attendancePopup.php?type=member', function(data){
			$("#popupBody").html(data);
			
			$(".closeWindow").click(function(){
				disablePopup('#generalPopup');
			});
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".assign").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('assignPopup.php?ID=' + id, function(data){
			$("#popupBody").html(data);
			
			$(".submitAssign").click(function(){
				var assignID = $(this).attr('id');
				var assignTo = $("#assignTo").val();
				
				window.location = 'recruitAction.php?action=assign&id=' + assignID + '&value=' + assignTo;
			});
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
	
	$(".remove").click(function(){
		var id = $(this).attr('id');
		
		window.location = 'recruitAction.php?action=remove&id=' + id;
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
		
		if(isset($_GET['term']) && isset($_GET['year']))
		{
			$year = $_GET['year'];
			$term = $_GET['term'];
		} else {
			$year = date(Y);
			$month = date(n);
			
			if($month <= 7){
				$term = "spring";
				$min = $year.'-01-01';
				$max = $year.'-07-31';
			} else {
				$term = "fall";
				$min = $year.'-08-01';
				$max = $year.'-12-31';
			}
		}
		
		$visitQuery = "
			SELECT type, l.status, firstName, lastName, date, l.ID as visitID
			FROM recruitLog l
			JOIN recruits r
			ON l.recruitID = r.ID
			WHERE date
			BETWEEN '$min' AND '$max'
			ORDER BY date DESC";
		$getVisits = mysqli_query($mysqli, $visitQuery);
	?>
		
	<div style="text-align:center;">
		
		 <h2>Dinners &amp; Visits - <?php echo ucwords($term)." ".$year; ?></h2>
		
		<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr> 
				<td><div align="right"><a href="<? 
	  		
			if($term == "fall"){
				echo "events.php?year=$year&amp;term=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "events.php?year=$lastYear&amp;term=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
				<td width="200"><div align="center">
					
					<select name="term" id="month" onChange="MM_jumpMenu('parent',this,0)">
						<?
			if($term == "fall"){
		  		echo "<option value=\"events.php?year=$year&amp;term=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"events.php?year=$year&amp;term=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"events.php?year=$year&amp;term=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"events.php?year=$year&amp;term=fall&amp;type=$type\" >Fall</option>\n";
			}
			?>
						</select>
					<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
						<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"events.php?term=$term&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
						</select>
					</div></td>
				<td><div align="left"><a href="<? 
	  	
		if($term == "fall"){
				$nextYear = $year+1;
				echo "events.php?year=$nextYear&amp;term=spring&amp;type=$type"; 
			} else {
				echo "events.php?year=$year&amp;term=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
				</tr>
			</table>
	</div>
	
	<?php
		echo '<table cellspacing="0" align="center" style="text-align:center;">';
		
		echo '<tr class="tableHeader"><td>Recruit</td><td>Type</td><td>Date</td><td>Totals</td><td> </td><td> </td></tr>';
		$count=0;
		while($visitArray = mysqli_fetch_array($getVisits, MYSQLI_ASSOC)){
			
			echo '<tr>';
			
			echo '<td>'.$visitArray[firstName].' '.$visitArray[lastName].'</td>';
			
			echo '<td>';
			if($visitArray[type] == 'initial') {
				
				echo 'Initial Contact';
			
			} else if($visitArray[type] == 'invite') {
				
				echo 'Event Invite';
				
			} else if($visitArray[type] == 'dinnerIn') {
						
				echo 'Dinner In ';
				
			} else if($visitArray[type] == 'dinnerOut') {
				
				echo 'Dinner Out ';
				
			} else if($visitArray[type] == 'houseVisit') {
				
				echo 'House Visit ';
				
			} else if($visitArray[type] == 'other') {
				
				echo 'Other ';
				
			}
			echo '</td>';
			
			if(strtotime($visitArray['date']) > strtotime(date('Y-m-d'))){
				echo '<td><strong>'.date('M j',strtotime($visitArray['date'])).'</strong></td>';
			} else {
				echo '<td>'.date('M j',strtotime($visitArray['date'])).'</td>';
			}
			
			$invited = 0;
			$attending = 0;
			
			$totalsQuery = "
				SELECT COUNT(ID) as total, status
				FROM eventAttendance
				WHERE eventID = '$visitArray[visitID]'
				GROUP BY status";
			$getTotals = mysqli_query($mysqli, $totalsQuery);
			
			while($totalsArray = mysqli_fetch_array($getTotals, MYSQLI_ASSOC)){
				if($totalsArray[status] == 'attending'){
					$attending = $totalsArray[total];
				} else if($totalsArray[status] == 'invited'){
					$invited = $totalsArray[total];
				}
			}
			
			echo '<td><b>I:</b> '.$invited.' | <b>A:</b> '.$attending.'</td>';
			
			echo '<td><input type="button" value="View" /></td>';
			echo '<td><input type="button" value="Invite Members" /></td>';
			
			$count++;
		}
		
		if($count == 0){
			echo "<p>No Events Scheduled</p>";
		}
		
		echo "</table>"
	?>
	<p style="text-align:center; padding-top: 20px;">
		<b>A:</b> Attending | <b>I:</b> Not Attending<br />
		<br />
		If a vist has <strong>already happend</strong> that you would like <br />
		to add to the system, click the button below.<br /><br />
		<input type="button" value="Add Visit" />
	</p>
	
	<div id="generalPopup">
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>