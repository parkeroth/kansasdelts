<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');

include_once('../../php/login.php');


if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	// Validate Data
	$valid = true;
	$errors = array();
	
	if($_POST[type] == 'select'){
		$errors[] = 'Please select a type of invite!';
		$valid = false;
	}
	
	if($valid) {
		$id = $_POST[id];
		
		$recruitQuery = "
			SELECT primaryContact
			FROM recruits c
			WHERE c.ID = '$id'";
		$getRecruit = mysqli_query($mysqli, $recruitQuery);
		$recruitData = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);
		
		$query = "	INSERT INTO recruitCalls (
					dateRequested, memberID, recruitID,
					type, status, notes)
					VALUES (
					'".date('Y-m-d')."', '$recruitData[primaryContact]', '$id',
					'$_POST[type]', 'pending', '$_POST[notes]')";
		$result = mysqli_query($mysqli, $query);
		
		header("location: $_POST[referrer]");
	}
	
}

$id =mysql_real_escape_string($_GET[id]);
$type =mysql_real_escape_string($_GET[type]);

$eventQuery = "
	SELECT * 
	FROM events e
	WHERE e.ID = '$id'";
$getEvent = mysqli_query($mysqli, $eventQuery);
$eventData = mysqli_fetch_array($getEvent, MYSQLI_ASSOC);

if($type == 'member'){
	
	$title = 'Member Attendance';
	
	$attendanceQuery = "
		SELECT firstName, lastName, username as id
		FROM eventAttendance
		WHERE eventID = '$id'";
	$getAttendance = mysqli_query($mysqli, $attendanceQuery);
	
}

?>

<div>
	<form action="<?php echo $_SERVER['../PHP_SELF']; ?>" method="post">
	
	<h1 style="text-align:center"><?php echo $title; ?></h1>
	
	<table class="details" align="center" cellspacing="0">
		<tr>
			<th>Name:</th>
			<td><?php
			
			echo $recruitData[firstName].' '.$recruitData[lastName];
			
			?></td>
		</tr>
		<tr>
			<th>Type:</th>
			<td><select name="type">
					<option value="select">Select One</option>
					<option value="dinnerIn">Dinner In</option>
					<option value="dinnerOut">Dinner Out</option>
					<option value="houseVisit">House Visit</option>
					<option value="other">Other</option>
				</select></td>
		</tr>
		<tr>
			<th>Notes:</th>
			<td><textarea name="notes" cols="18" rows="3"></textarea></td>
		</tr>	
	</table>
	
	<div id="viewControls">
		<input type="submit" value="Update" onclick="return checkSelect(document.getElementById('statusSelect'), 'Please select a new status!')" />
		<input class="closeWindow" type="button" value="Close" />
	</div>
	
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="referrer" value="<?php echo $referrer; ?>" />
	
	</form>
</div>