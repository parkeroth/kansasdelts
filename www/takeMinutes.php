<?
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

$date = $_GET['date'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Edit Agenda Details</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<style>
	th {
		text-align: right;
	}
	
	a:link {
		font-weight: bold;
		color: #FDC029;
		text-decoration: none;
	}
	
	a:visited {
		font-weight: bold;
		color: #CF9200;
		text-decoration: none;
	}
	
	a:hover {
		font-weight: bold;
		color: #FFD46F;
		text-decoration: none;
	}
	
	
	.header {
		text-align: center;
		font-size: 32px;
	}
	
	
</style>

</head>
<body>
<div class="header">
	<p>Chapter Minutes</p>
</div>
<br>
<form action="php/modifyMinutes.php" method="post">
<?php

$formattedDate = date("F j, Y", strtotime($date));

$minuteData = "
	SELECT * 
	FROM chapterMinutes
	WHERE meetingDate = '$date'
	LIMIT 1";
$getMinuteData = mysqli_query($mysqli, $minuteData);
$minuteArray = mysqli_fetch_array($getMinuteData, MYSQLI_ASSOC);
	
	$chapterDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime($date)) . " +1 day"));
	
	// Get number of members in roster
	$query = "SELECT COUNT(username) AS numUsers FROM members WHERE residency != 'limbo'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numMembers = $row->numUsers;
		
		mysqli_free_result($result);
	}
	
	// Get number of members absent
	$query = "SELECT COUNT(username) AS numAbsent FROM attendance WHERE status = 'absent' AND date = '$chapterDate'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numAbsent = $row->numAbsent;
		
		mysqli_free_result($result);
	}
	
	// Get number of members excused
	$query = "SELECT COUNT(username) AS numExcused FROM attendance WHERE status = 'excused' AND date = '$chapterDate'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$numExcused = $row->numExcused;
		
		mysqli_free_result($result);
	}
	
	$numPresent = $numMembers - ($numAbsent + $numExcused);
	
	// Get number of voting members
	$query = "	SELECT COUNT(username) AS numUsers 
				FROM members 
				WHERE residency != 'limbo'
				AND standing = 'good'
				AND memberStatus = 'active'";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$totalVoting = $row->numUsers;
		
		mysqli_free_result($result);
	}
	
	// Get number of present voting members
	$query = "	SELECT COUNT(username) AS numUsers 
				FROM members 
				WHERE residency != 'limbo'
				AND standing = 'good'
				AND memberStatus = 'active'
				AND username NOT IN(
					SELECT username
					FROM attendance
					WHERE date = '$chapterDate'
				)";
	if($result = mysqli_query($mysqli, $query)){
		
		$row = mysqli_fetch_object($result);
		$votingPresent = $row->numUsers;
		
		mysqli_free_result($result);
	}
	
	if($votingPresent / $totalVoting >= .5 + 1){
		$quorum = "Yes";
	} else {
		$quorum = "No";
	}

?>
<table>
<tr>
	<th>Attendance:</th>
	<td>
		Present:<b><?php echo $numPresent;?></b> Absent:<b><?php echo $numAbsent;?></b> Excused:<b><?php echo $numExcused;?></b> 
	</td>
</tr>
<tr>
	<th>Voting Members:</th>
	<td>
		Total:<b><?php echo $totalVoting;?></b> Present:<b><?php echo $votingPresent;?></b> 
	</td>
</tr>
<tr>
	<th>Quorum:</th>
	<td>
		<?php echo $quorum; ?>
	</td>
</tr>
<tr>
	<th>Meeting Type:</th>
	<td>
		<select name="type">
			<option value="regular">Regular</option>
			<option value="formal">Formal</option>
		</select>
	</td>
</tr>
<tr>
	<th>Presiding Officer:</th>
	<td>
		<select name="officer">
		<?
		
		echo "<p><b>Date:</b> $formattedDate</p>";
		
			$userData = "
				SELECT * 
				FROM members
				ORDER BY lastName";
			$getUserData = mysqli_query($mysqli, $userData);
		
			while($userArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)) {
				
				if(strpos($userArray[accountType],"pres")){
					echo $userArray[lastName]."FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF";
					$selected = "selected";
				} else {
					$selected = "";
				}
				
				echo "<option value=\"$userArray[username]\" $selected>$userArray[firstName] $userArray[lastName]</option>";
			}
		?>
		
		</select>
	</td>
</tr>
<tr>
	<th>Start Time:</th>
	<td>
		<input name="startTime" type="text" value="<?php echo $minuteArray[startTime]; ?>">
	</td>
</tr>
<tr>
	<th>End Time:</th>
	<td>
		<input name="endTime" type="text" value="<?php echo $minuteArray[endTime]; ?>">
	</td>
</tr>
<tr>
	<th>Old Business:</th>
	<td>
		<textarea name="oldBusiness" cols="40" rows="10"><?php echo $minuteArray[oldBusiness]; ?></textarea>
	</td>
</tr>
<tr>
	<th>New Business:</th>
	<td>
		<textarea name="newBusiness" cols="40" rows="10"><?php echo $minuteArray[newBusiness]; ?></textarea>
	</td>
</tr>
<tr>
	<th>For the Good of the Order:</th>
	<td>
		<textarea name="goodOfOrder" cols="40" rows="10"><?php echo $minuteArray[goodOfOrder]; ?></textarea>
	</td>
</tr>
</table>

<input type="hidden" name="date" value="<?php echo $date; ?>" >
<input type="submit" value="Submit Changes">
</form>
</body>
</html>