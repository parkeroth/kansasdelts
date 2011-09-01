<?
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

$date = $_GET['date'];
$timeStamp = strtotime($date) + 86400;
$chapterDate = date("Y-m-d", $timeStamp);

$report = "
	SELECT * 
	FROM reports 
	WHERE dateMeeting='$date'";
$getReport = mysqli_query($mysqli, $report);
$reportData = mysqli_fetch_array($getReport, MYSQLI_ASSOC);

$minutes = "
	SELECT * 
	FROM chapterMinutes 
	WHERE meetingDate='$date'";
$getMinutes = mysqli_query($mysqli, $minutes);
$minutesArray = mysqli_fetch_array($getMinutes, MYSQLI_ASSOC);

$userData = "
	SELECT * 
	FROM members
	WHERE username='$reportData[username]'";
$getUserData = mysqli_query($mysqli, $userData);
$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

$name = $userDataArray[firstName]." ".$userDataArray[lastName];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>View Meeting Agenda</title>
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
	
	.execPosition {
		font-weight: bold;
	}
	
	.adminSection {
		padding-top, padding-bottom: 20px;
		padding-left: 40px;
	}
	
	.adminPosition {
		font-weight: bold;
	}
	
	.noReport {
		padding: 10px;
	}
	
	.header {
		text-align: center;
		font-size: 32px;
	}
	
	.content {
		padding-left: 20px;
	}
	
	.reports {
		padding-top: 20px;
		padding-left: 40px;
	}
	
	.minutesInfo {
		font-weight: normal;
		padding-left: 40px;
	}
	
	
</style>

</head>
<body>
<div class="header">
<p>Delta Tau Delta<br>
Gamma Tau<br>
Chapter Minutes</p>
</div>
<?php

$formattedDate = date("F j, Y", strtotime($chapterDate));

echo "<p><b>Date:</b> $formattedDate</p>";

echo "<p><b>Meeting Type:</b> ".ucwords($minutesArray[type])."</p>";

$officerData="
	SELECT firstName, lastName
	FROM members
	WHERE username = '$minutesArray[officer]'";
$getOfficerData = mysqli_query($mysqli, $officerData);
$officerArray = mysqli_fetch_array($getOfficerData, MYSQLI_ASSOC);

echo "<p><b>Officer Presiding:</b> $officerArray[firstName] $officerArray[lastName]</p>";
	
	echo "<div class=\"content\">";
	
	?>
		<table style="font-weight:bold;">
		<tr>
			<td>I.</td><td>Meeting to Order - <?php echo $minutesArray[startTime]; ?></td>
		</tr>
		<tr>
			<td>II.</td><td>Members excused from Chapter</td>
		</tr>
		
		<?php 
		
		$excusedData = "
			SELECT username
			FROM attendance
			WHERE status = 'excused'
			AND date = '$chapterDate'";
		$getExcusedData = mysqli_query($mysqli, $excusedData);
		
		$memberCount = 0;
		while($excusedArray = mysqli_fetch_array($getExcusedData, MYSQLI_ASSOC))
		{
			$userData = "
				SELECT firstName, lastName
				FROM members
				WHERE username='$excusedArray[username]'";
			$getUserData = mysqli_query($mysqli, $userData);
			$userArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

			$members[$memberCount]['firstName'] = $userArray['firstName'];
			$members[$memberCount]['lastName'] = $userArray['lastName'];
			$memberCount++;
		}
		
			if($memberCount){
				echo "<tr><td colspan=\"2\">";
				echo "<div class=\"minutesInfo\">";
				
				for($i=0; $i < $memberCount; $i++) {
					echo $members[$i]['firstName']." ".$members[$i]['lastName']."<br>";
				}
				
				echo "</div>";
				echo "</td></tr>";
			}
		
		?>
		
		<tr>
			<td>III.</td><td>Reading &amp; Approval of Minutes from the Previous Meeting</td>
		</tr>
		<tr>
			<td>IV.</td><td>Reports of Officers</td>
		</tr>
		</table>
		
	<?php
	
	echo "<div class=\"reports\">";
	
	function getReport($type, $date, $mysqli){
		$reportData = "
			SELECT agenda
			FROM reports
			WHERE type = '$type'
			AND dateMeeting = '$date'";
		$getReportData = mysqli_query($mysqli, $reportData);
		$reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC);
		
		if($reportArray[agenda] == "") {
			echo "<div class=\"noReport\">Proud to be Delt.</div>";
		} else {
			echo "<div class=\"report\">";
			echo "<ul><li>";
			echo str_replace("\n", "\n</li><li>", $reportArray[agenda]);
			echo "</li></ul>";
			echo "</div>";
		}
	}
	
	$execData = "
		SELECT * 
		FROM positions
		WHERE board = 'exec'
		AND type != 'secretary'";
	$getExecData = mysqli_query($mysqli, $execData);

	while($execDataArray = mysqli_fetch_array($getExecData, MYSQLI_ASSOC)) {
		
		$nameQuery = "
			SELECT * 
			FROM members
			WHERE accountType LIKE '%|".$execDataArray[type]."%'";
		$getNameData = mysqli_query($mysqli, $nameQuery);
		$nameDataArray = mysqli_fetch_array($getNameData, MYSQLI_ASSOC);
		
		if($execDataArray[type] == "vicePres") {
			
			echo "<span class=\"execPosition\">".$execDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
			
			getReport($execDataArray[type], $date, $mysqli);
			
			$adminData = "
				SELECT * 
				FROM positions
				WHERE board = 'admin'";
			$getAdminData = mysqli_query($mysqli, $adminData);
			
			echo "<div class=\"adminSection\">\n";
			while($adminDataArray = mysqli_fetch_array($getAdminData, MYSQLI_ASSOC)) {
				
				$nameQuery = "
					SELECT * 
					FROM members
					WHERE accountType LIKE '%|".$adminDataArray[type]."%'";
				$getNameData = mysqli_query($mysqli, $nameQuery);
				$nameDataArray = mysqli_fetch_array($getNameData, MYSQLI_ASSOC);
				
				echo "<span class=\"adminPosition\">".$adminDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
				
				getReport($adminDataArray[type], $date, $mysqli);
			
			}
			echo "</div>\n";
			
		} else {
			
			echo "<span class=\"execPosition\">".$execDataArray[title].":</span> $nameDataArray[firstName] $nameDataArray[lastName]<br>";
			
			getReport($execDataArray[type], $date, $mysqli);
		
		}
	}
	echo "</div>";
	
	?>
		<table style="font-weight:bold;">
		<tr>
			<td>V.</td><td>Old Business</td>
		</tr>
		
		<?php 
		
			if($minutesArray[oldBusiness] != ""){
				echo "<tr><td colspan=\"2\">";
				echo "<div class=\"minutesInfo\">";
				echo str_replace("\n", "\n<br>", $minutesArray[oldBusiness]);
				echo "</div>";
				echo "</td></tr>";
			}
		
		?>
		
		<tr>
			<td>VI.</td><td>New Business</td>
		</tr>
		
		<?php 
		
			if($minutesArray[newBusiness] != ""){
				echo "<tr><td colspan=\"2\">";
				echo "<div class=\"minutesInfo\">";
				echo str_replace("\n", "\n<br>", $minutesArray[newBusiness]);
				echo "</div>";
				echo "</td></tr>";
			}
		
		?>
		
		<tr>
			<td>VII.</td><td>For the Good of the Order</td>
		</tr>
		
		<?php 
		
			if($minutesArray[goodOfOrder] != ""){
				echo "<tr><td colspan=\"2\">";
				echo "<div class=\"minutesInfo\">";
				echo str_replace("\n", "\n<br>", $minutesArray[goodOfOrder]);
				echo "</div>";
				echo "</td></tr>";
			}
		
		?>
		
		<tr>
			<td>VIII.</td><td>Meeting Adjourned - <?php echo $minutesArray[endTime]; ?></td>
		</tr>
		</table>
	<?php
	echo "</div>";
?>

</body>
</html>