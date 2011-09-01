<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'recruitment');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

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
	
	$(".assign").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popups/assign.php?ID=' + id, function(data){
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
		
		// Get recruits without a primary contact
		$recruitQuery = "
			SELECT * 
			FROM recruits 
			WHERE primaryContact IS NULL
			ORDER BY lastName ASC";
		$getRecruits = mysqli_query($mysqli, $recruitQuery);
		
	?>
		
	<h1 style="text-align:center">New Recruits</h1>
		
	<?php
		echo '<table cellspacing="0" align="center">';
		
		$rowColor = "white";
		$count = 1;
		
		echo '<tr class="tableHeader"><td>Name</td><td>Current School</td><td> </td><td> </td><td> </td></tr>';
		
		while($recruitArray = mysqli_fetch_array($getRecruits, MYSQLI_ASSOC)){
			
			echo "<tr class=\"$rowColor\">";
			echo "<td>$recruitArray[firstName] $recruitArray[lastName]</td>";
			echo "<td>$recruitArray[currentSchool]</td>";
			echo '<td><input class="view" id="'.$recruitArray[ID].'" type="button" value="View" /></td>';
			echo '<td><input class="assign" id="'.$recruitArray[ID].'" type="button" value="Assign" /></td>';
			echo '<td><input class="remove" id="'.$recruitArray[ID].'" type="button" value="Remove" /></td>';
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
			echo "<p>No Events Scheduled</p>";
		}
		
		echo "</table>"
	?>
	<div id="generalPopup">
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>