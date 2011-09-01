<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'recruitment', 'recruitCom');
include_once('../php/authenticate.php');
include_once('classes/Recruit.php');

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
		window.location = 'recruitDetail.php?ID=' + $(this).attr('id')
	});
	
	$(".history").click(function(){
		var id = $(this).attr('id');
		
		$.get('popups/history.php?ID=' + id, function(data){
			$("#popupBody").html(data);
			
			$(".closeWindow").click(function(){
				disablePopup('#generalPopup');
			});
			
			$(".editCall").click(function(){
				var id = $(this).attr('id');
				
				$.get('popups/call.php?ID=' + id, function(data){
					$("#popupBody").html(data);
					
					$(".closeWindow").click(function(){
						//disablePopup('#generalPopup');
					});
					
				});
				
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
				//disablePopup('#generalPopup');
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

<h1 style="text-align:center;">Master Recruit List</h1>

<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$statusQuery = 'SELECT DISTINCT r.status AS statusNum, s.status AS status 
						FROM recruits r
						JOIN recruitStatus s
						ON r.status = s.ID';
		$getStatus = mysqli_query($mysqli, $statusQuery);		
		while($statusArray = mysqli_fetch_array($getStatus, MYSQLI_ASSOC)){
			
			echo '<h2 style="text-align:center;">'.$statusArray[status].'</h2>';
			
			$recruitList = array();
			
			$recruitQuery = "
				SELECT ID FROM recruits
				WHERE status = '$statusArray[statusNum]'
				AND primaryContact IS NOT NULL
				ORDER BY lastName ASC";
			$getRecruits = mysqli_query($mysqli, $recruitQuery);
			while($recruitArray = mysqli_fetch_array($getRecruits, MYSQLI_ASSOC)){
				$recruitList[] = new Recruit($mysqli, $recruitArray[ID]);
			}
			
			echo '<table cellspacing="0" align="center">';
			
			$rowColor = "white";
			$count = 1;
			
			echo '<tr class="tableHeader"><td width="120">Name</td><td width="120">Owner</td><td width="100">Last Contact</td><td> </td></tr>';
			
			foreach($recruitList as $recruit){
				$numOutstandingCalls = sizeof($recruit->get_calls('pending'));
				
				if($numOutstandingCalls > 0) {
					$nameClass = 'redHeading';
				} else {
					$nameClass = '';
				}
				
				echo "<tr class=\"$rowColor recruit\">";
				echo "<td class='$nameClass'><b>$recruit->firstName $recruit->lastName</b></td>";
				echo "<td>$recruit->get_owener()</td>";
				echo "<td>$recruit->last_contact_date()</td>";
				echo '<td><input class="view" id="'.$recruit->id.'" type="button" value="View" /> ';
				echo '<input class="history" id="'.$recruit->id.'" type="button" value="History" /> ';
				if($super){
					echo '<input class="invite" id="'.$recruit->id.'" type="button" value="Invite" /> ';
				}
				echo '</td>';
				echo "</tr>";
				echo "<tr class=\"$rowColor recruit stats\"><td></td>";
				echo "<td colspan=\"5\">Calls: <b>5</b> | Dinners: <b>2</b> | Visits: <b>1</b></td>";
				echo "</tr>";
						
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
				echo "<p>Why aren't there any recruits?</p>";
			}
			
			echo "</table>";
		}
		
		
	?>
	<div id="generalPopup">
		<div id="popupBody"></div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>