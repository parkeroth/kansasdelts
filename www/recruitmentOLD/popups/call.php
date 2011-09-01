<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');

include_once('../../php/login.php');

include_once('../classes/Recruit.php');
include_once('../classes/RecruitCall.php');
include_once('../classes/RecruitActivity.php');
include_once('../classes/RecruitAttendance.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$call = new RecruitCall($mysqli, $_POST[callID]);
	$recruit = $call->get_recruit();
	
	$referrer = $_SERVER['HTTP_REFERER'];
	
	if($_POST[status] == 'pending' || $_POST[status] == 'leftMessage'){
		$call->saveVal('status', $_POST[status]);
		$call->saveVal('notes', $_POST[notes]);
		
		header("location: $_POST[referrer]");
	
	} else if($_POST[status] == 'completed' || $_POST[status] == 'giveUp'){
		
		$today = date('Y-m-d');
		
		if($_POST[type] == 'initial') {
			$recruit->saveVal('status',$_POST[interestLevel]);
		}
		
		$call->saveVal('status', $_POST[status]);
		$call->saveVal('notes', $_POST[notes]);
		$call->saveVal('dateCompleted', $today);
		$call->saveVal('completedBy', $_POST[caller]);
		
		if(	$_POST[type] == 'dinnerIn' || 
			$_POST[type] == 'dinnerOut' || 
			$_POST[type] == 'houseVisit' ) {
			
			$time = $_POST[hour].':'.$_POST[minute].' '.$_POST[ampm];
			$date = date('Y-m-d',strtotime($_POST['date']));
			
			$activity = new RecruitActivity($mysqli, NULL);
			$activity->type = $_POST[type];
			$activity->time = $time;
			$activity->date = $date;
			$activity->location = $_POST[location];
			$activity->recruitID = $_POST[recruitID];
			$activity->callID = $_POST[callID];
			$activity->status = 'pending';
		}
		
		header("location: $_POST[referrer]");
	
	} else if($_POST[status] == 'attending' || $_POST[status] == 'notAttending'){
		
		$today = date('Y-m-d');
		
		$call->saveVal('status', $_POST[status]);
		$call->saveVal('notes', $_POST[notes]);
		$call->saveVal('dateCompleted', $today);
		$call->saveVal('completedBy', $_POST[caller]);
		
		$attendance = new RecruitAttendance($mysqli, $call->eventID, $call->recruitID);
		$attendance->saveVal('status', $_POST[status]);
		$attendance->saveVal('eventID', $_POST[eventID]);
		$attendance->saveVal('recruitID', $_POST[recruitID]);
		
		echo $call->status;
		
		//header("location: $_POST[referrer]");
	}
}

$id =mysql_real_escape_string($_GET[ID]);

$call = new RecruitCall($mysqli, $id);
$recruit = $call->get_recruit();
$owner = $call->get_owner();

$referrer = $_SERVER['HTTP_REFERER'];

?>

<script>

function checkSelect(elem, helperMsg){
	if(elem.value == 'select'){
		alert(helperMsg);
		elem.focus(); // set the focus to this input
		return false;
	}
	return true;
}


$(function() {
	$("#datepicker").datepicker();
	
	$("#callForm").submit(function(){
		
		var errors = '';
		var valid = true;
		
		if( $("#locationField").val() == '') {
			errors += 'Please provide the location of the meeting!\n';
			valid = false;
		}
		
		if( $("#hourField").val() == 'select' || 
			$("#minuteField").val() == 'select' || 
			$("#ampmField").val() == 'select') {
			errors += 'Please provide the time of the meeting!\n';
			valid = false;
		}
		
		if( $("#datepicker").val() == '') {
			errors += 'Please provide the date the meeting!\n';
			valid = false;
		}
		
		if( $("#statusSelect").val() == 'select') {
			errors += 'You did not indicate the status of the call!\n';
			valid = false;
		}
		
		if( $("#interestSelect").val() == 'select') {
			errors += 'You did not indicate the recruit\'s interest level!\n';
			valid = false;
		}
		
		if(valid) {
			var status = $("#statusSelect").val();
			var message = '';
			
			if( status == 'giveUp' ) {
				message = 'Are you sure you want to give up on this call?';
				return confirm(message);
			} else {
				return true;
			}
			
			
		} else {
			alert(errors);
			return false;
		}
		
	});
});
		
</script>

<div>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="callForm">
	
	<h1 style="text-align:center">Call Details</h1>
	
	<table align="center" cellspacing="0">
		<tr>
			<th>Name:</th>
			<td><div id="recruitName"><?php echo $recruit->firstName.' '.$recruit->lastName; ?></div></td>
		</tr>
		<tr>
			<th>Phone #:</th>
			<td><?php echo $recruit->get_phone(); ?></td>
		</tr>
		<tr>
			<th>Reason:</th>
			<td><div id="reason"><?php echo $call->get_formatted_type(); ?></div></td>
		</tr>
	<?php if($call->type == 'initial') { ?>
		
		<tr>
			<th>Interest Level:</th>
			<td><select id="interestSelect" name="interestLevel">
					<option value="select">Select One</option>
					<option value="6">Interested</option>
                    <option value="6">Not Sure</option>
					<option value="8">Not Interested</option>
            </select></td>
        </tr>
			
	<?php }
	
	if($call->eventID != 0) {
		
		echo '<tr><td colspan="2">';
		
		echo '<h3>Event Details</h3>';
		
		$eventQuery = "
			SELECT *
			FROM events
			WHERE ID = '$call->eventID'";
		$getEvent = mysqli_query($mysqli, $eventQuery);
		$eventArray = mysqli_fetch_array($getEvent, MYSQLI_ASSOC);
		
		$date = date('l M j, Y', strtotime($eventArray[eventDate]));
		
		echo '<table class="eventDetails">';
			echo "<tr><th>Title:</th><td>$eventArray[title]</td></tr>";
			echo "<tr><th>Description:</th><td>$eventArray[description]</td></tr>";
			echo "<tr><th>Date:</th><td>$date</td></tr>";
			echo "<tr><th>Time:</th><td>$eventArray[time]</td></tr>";
			
			echo '</td>';
			echo '</tr>';
		echo '</table>';
		
		echo '</td></tr>';
	} ?>
		
	<?php
	
	if(	$call->type == 'dinnerIn' || 
		$call->type == 'dinnerOut' || 
		$call->type == 'houseVisit' ) {
			
	?>
		<tr><td colspan="2"><p> </p></td></tr>
		<tr>
			<th>Date:</th>
			<td><input name="date" type="text" id="datepicker" size="10" /></td>
    	</tr>
		<tr>
			<th>Time:</th>
			<td>  <select id="hourField" name="hour">
					<option value="select"></option>
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="12">12</option>
				  </select>
				  :
				  <select id="minuteField" name="minute">
				  	<option value="select"></option>
					<option value="00">00</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="45">45</option>
				  </select>
				  <select id="ampmField" name="ampm">
				    <option value="select"></option>
					<option value="AM">AM</option>
					<option value="PM">PM</option>
				  </select></td>
    	</tr>
		
		<?php
		
		if(	$call->type == 'dinnerOut' ) {
				
		?>
		
		<tr>
			<th>Location:</th>
			<td><input id="locationField" name="location" type="text" size="20" /></td>
    	</tr>
		
	
		<?php } ?>
		
		<tr><td colspan="2"><p> </p></td></tr>
		
	<?php } ?>
			</td>
		</tr>
		<tr>
			<th>Comments:</th>
			<td><textarea name="notes" cols="18" rows="3"><?php echo $call->notes; ?></textarea></td>
		</tr>
		<tr>
			<th>Call Status:</th>
			<td><select id="statusSelect" name="status">
					<option value="select" <?php if ($call->status == 'select') echo 'selected="selected"'; ?> >
                    	Select One</option>
					<option value="pending" <?php if ($call->status == 'pending') echo 'selected="selected"'; ?> >
                    	Pending</option>
					<option value="leftMessage" <?php if ($call->status == 'leftMessage') echo 'selected="selected"'; ?> >	
                    	Left Message</option>
				<?php
				
				if($call->eventID != 0) {
					
					echo '<option value="attending">Attending</option>';
					echo '<option value="notAttending">Not Attending</option>';
					
				} else {
					
					echo '<option value="completed">Completed</option>';
					echo '<option value="giveUp">Give Up</option>';
				}
								
				?>
			</select></td>
		</tr>
		<tr>
			<th>Caller:</th>
			<td><select id='callerSelect' name="caller">
					<option value="select">Select One</option>
				<?php
				
				$memberQuery = "
					SELECT firstName, lastName, username
					FROM members
					WHERE memberStatus != 'limbo'
					ORDER BY lastName";
				$getMembers = mysqli_query($mysqli, $memberQuery);
				
				while($memberArray = mysqli_fetch_array($getMembers, MYSQLI_ASSOC)) {
				
					echo '<option value="'.$memberArray[username].'"';
					
					if($memberArray[username] == $call->memberID) {
						echo 'selected="selected"';
					}
					
					echo '>';
					echo $memberArray[firstName].' '.$memberArray[lastName];
					echo '</option>';
				}
								
				?>
			</select></td>
		</tr>	
	</table>
	
	<div id="viewControls">
		<input type="submit" value="Submit" />
		<input class="closeWindow" type="button" value="Close" />
	</div>
	
		<input type="hidden" name="callID" value="<?php echo $id; ?>" />
		<input type="hidden" name="referrer" value="<?php echo $referrer; ?>" />
		<input type="hidden" name="type" value="<?php echo $callArray[type]; ?>" />
		
	
	</form>
</div>