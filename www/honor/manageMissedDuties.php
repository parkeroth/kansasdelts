<?php
session_start();
$authUsers = array('admin', 'saa', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once 'classes/Infraction_Log.php';

$super_list = array('admin', 'saa');
$haz_super_powers = $session->isAuth($super_list);


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="../styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$(".eval").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popup/dutyPopup.php?type=eval&ID=' + id, function(data){
			$("#popupBody").html(data);
			
			$(".auth").click(function(){
				window.location = 'popup/missedDuty.php?type=auth&status=approved&id=' + id;
			});
			
			$(".reject").click(function(){
				window.location = 'popup/missedDuty.php?type=auth&status=rejected&id=' + id;
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
		$log_manager = new Infraction_Log_Manager();
		$pending_list = $log_manager->get_pending();
		
		$sem = new Semester();
				
		echo "<table>\n";
		$first = true;
		foreach($pending_list as $record){
			if($first){
				echo "<tr style=\"font-weight: bold;\"><td width=\"150\">Party Responible</td><td width=\"160\">Date of Occurance</td><td>Type</td><td>Occurance</td><td></td></tr>\n";
				$first = false;
			}
			$offender = new Member($record->offender_id);
			$occurance_number = $record->get_occurance_num();
			
			echo "<tr>\n";
			echo "<td>$offender->first_name $offender->last_name</td>";
			echo "<td>$record->date_occured</td>";
			echo "<td>".Infraction_Log::$INFRACTION_TYPES[$record->type]."</td>";
			echo "<td style=\"text-align:center;\">$occurance_number</td>";
			echo "<td><input id=\"$record->id\" class=\"eval\" type=\"button\" value=\"Evaluate\" /></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		
		if(count($pending_list) == 0)
		{
			echo "<p>No pending missed duties.</p>";
		} ?>
		
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>		
		
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>