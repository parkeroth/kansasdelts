<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'recruitment', 'recruitCom');
include_once('../php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 

if(strpos($session->accountType, "admin") || strpos($session->accountType, "recruitment")) {
	$super = true;
} else {
	$super = false;
}

?>

<link type="text/css" href="css/styles.css" rel="stylesheet" />

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="/styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$(".view").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popups/view.php?ID=' + id, function(data){
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
	
	$(".history").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/history.php?ID=' + id, function(data){
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
	
	$(".invite").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/invite.php?ID=' + id, function(data){
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
	
	$(".editCall").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/call.php?ID=' + id, function(data){
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
		
		$type = mysql_real_escape_string($_GET[type]);
		$value = mysql_real_escape_string($_GET[value]);
		
		if($type == 'primary' ){
			
			$field = 'primaryContact';
			
			if($value == 'all'){
				$pageHeader = 'All Recruits';
				$subHeader = 'By Primary Contact';
				
				// Get recruits without a primary contact
				$ownerQuery = "
					SELECT DISTINCT primaryContact AS owner
					FROM recruits
					WHERE primaryContact IS NOT NULL";
				$getOwners = mysqli_query($mysqli, $ownerQuery);
			
			} else {
				
				// Get recruits without a primary contact
				$ownerQuery = "
					SELECT DISTINCT primaryContact AS owner
					FROM recruits
					WHERE primaryContact = '$value'";
				$getOwners = mysqli_query($mysqli, $ownerQuery);
				
			}
		
		} else if($type = 'original') {
			
			$field = 'referredBy';
			
			if($value == 'all'){
				$pageHeader = 'All Recruits';
				$subHeader = 'By Referrer';
				
				// Get recruits without a primary contact
				$ownerQuery = "
					SELECT DISTINCT referredBy AS owner
					FROM recruits
					WHERE primaryContact IS NOT NULL";
				$getOwners = mysqli_query($mysqli, $ownerQuery);
			
			} else {
				
				// Get recruits without a primary contact
				$ownerQuery = "
					SELECT DISTINCT referredBy AS owner
					FROM recruits
					WHERE referredBy = '$value'";
				$getOwners = mysqli_query($mysqli, $ownerQuery);
				
			}
		}
		
		if($value == 'all'){
		
	?>
	<div style="text-align:center">	
		<h1><?php echo $pageHeader; ?></h1>
		<h4><?php echo $subHeader; ?></h4>
	</div>	
	<?php
	
		}
		
		while($ownerArray = mysqli_fetch_array($getOwners, MYSQLI_ASSOC)){
			
			if($ownerArray[owner] == 'self')
			{
				$title = 'Self Submit';
				
			} else if($ownerArray[owner] == 'alum') {
				
				$title = 'Alumni Submit';
				
			} else {
				
				$memberQuery = "
					SELECT firstName, lastName
					FROM members
					WHERE username = '$ownerArray[owner]'";
				$getMember = mysqli_query($mysqli, $memberQuery);
				$memberArray = mysqli_fetch_array($getMember, MYSQLI_ASSOC);
				
				$title = $memberArray[firstName].' '.$memberArray[lastName];
			}
			
			if($value == 'all'){
				
				echo '<a href="#"><h3 style="text-align:center;">'.$title.'</h3></a>';
			
			} else {
				
				if($type == 'primary'){
					echo '<h1 style="text-align:center;">Recruits Owned By</h1>';
				} else {
					echo '<h1 style="text-align:center;">Recruits Referred By</h1>';
				}
				
				echo '<h2 style="text-align:center;">'.$title.'</h2>';
			}
			
			$recruitQuery = "
				SELECT 
					r.firstName AS firstName,
					r.lastName AS lastName,
					r.ID AS ID,
					s.status AS status
				FROM recruits r
				JOIN recruitStatus s
				ON r.status = s.ID
				WHERE $field = '$ownerArray[owner]'
				AND primaryContact IS NOT NULL
				ORDER BY lastName ASC";
			$getRecruits = mysqli_query($mysqli, $recruitQuery);
			
			
			echo '<table cellspacing="0" align="center">';
			
			$rowColor = "white";
			$count = 1;
			
			echo '<tr class="tableHeader"><td width="120">Name</td><td width="80">Status</td><td> </td></tr>';
			
			while($recruitArray = mysqli_fetch_array($getRecruits, MYSQLI_ASSOC)){
				
				echo "<tr class=\"$rowColor recruit\">";
				echo "<td><b>$recruitArray[firstName] $recruitArray[lastName]</b></td>";
				echo "<td>$recruitArray[status]</td>";
				echo '<td><input class="view" id="'.$recruitArray[ID].'" type="button" value="View" /> ';
				echo '<input class="history" id="'.$recruitArray[ID].'" type="button" value="History" /> ';
				if($super){
					echo '<input class="invite" id="'.$recruitArray[ID].'" type="button" value="Invite" /> ';
				}
				echo '</td>';
				echo "</tr>";
				
				$outstandingQuery = "
					SELECT * 
					FROM recruitCalls
					WHERE (status = 'pending' OR status = 'leftMessage')
					AND recruitID = '$recruitArray[ID]'";
				$getOutstanding = mysqli_query($mysqli, $outstandingQuery);
				while($outstandingArray = mysqli_fetch_array($getOutstanding, MYSQLI_ASSOC)) {
					
					echo '<tr class="'.$rowColor.' task">';
					echo '<td colspan="2" class="indent"><b>Pending Call:</b> ';
					
					if($outstandingArray[type] == 'initial') {
				
						echo 'Initial Contact';
					
					} else if($outstandingArray[type] == 'invite') {
						
						echo 'Event Invite ';
						
					} else if($outstandingArray[type] == 'dinnerIn') {
						
						echo 'Dinner In ';
						
					} else if($outstandingArray[type] == 'dinnerOut') {
						
						echo 'Dinner Out ';
						
					} else if($outstandingArray[type] == 'houseVisit') {
						
						echo 'House Visit ';
						
					} else if($outstandingArray[type] == 'other') {
						
						echo 'Other ';
						
					}
					
					echo '</td>';
					echo '<td><input class="editCall" id="'.$outstandingArray[ID].'" type="button" value="Edit" /></td>';
					echo '</tr>';
				}
				
				$count++;
				
				if($rowColor == "white"){
					$rowColor = "black";
				}
				else
				{
					$rowColor = "white";
				}
			}
			
			if($count == 0){
				echo "<p>No Events Scheduled</p>";
			}
			
			echo "</table>";
		}
		
		
	?>
	<div id="generalPopup">
		<div id="popupBody"></div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>