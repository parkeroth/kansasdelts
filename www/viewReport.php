<?
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$ID = $_POST[ID];
	
	if($_POST[type] == "completed")
	{
		$add_sql = "UPDATE reports
			SET completed='$_POST[completed]'
			WHERE ID='$_POST[ID]'";
	
		$add_res = mysqli_query($mysqli, $add_sql);
		
		header("location: viewReport.php?ID=$ID&previousDate=$_POST[previousDate]");
		
	} else {
		$add_sql = "UPDATE reports
			SET task='$_POST[task]'
			WHERE ID='$_POST[ID]'";
	
		$add_res = mysqli_query($mysqli, $add_sql);
		
		header("location: viewReport.php?ID=$ID&previousDate=$_POST[previousDate]");
		
	}
}
 
/**
 * Form Section
 */

$ID = $_GET[ID];

$report = "
	SELECT * 
	FROM reports 
	WHERE ID='$ID'";
$getReport = mysqli_query($mysqli, $report);
$reportData = mysqli_fetch_array($getReport, MYSQLI_ASSOC);

$userData = "
	SELECT * 
	FROM members
	WHERE username='$reportData[username]'";
$getUserData = mysqli_query($mysqli, $userData);
$userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC);

$name = $userDataArray[firstName]." ".$userDataArray[lastName];


$positionData = "
	SELECT * 
	FROM positions
	WHERE type='$reportData[type]'";
$getPositionData = mysqli_query($mysqli, $positionData);
$positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC);

$position = $positionDataArray[title];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Member Report Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

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
	
	
</style>

</head>
<body>
<h2><?php echo $position?> Report</h2>
<p>Submitted by <?php echo $name." on ".date("F j, Y g:i:s A", strtotime($reportData[dateSubmitted])); ?></p>
<table>
	<tr>
		<th width="180">Task for previous week: </th>
		
		<?php
		$previousData = "
			SELECT task
			FROM reports
			WHERE 	type='$reportData[type]'
				AND dateMeeting='$_GET[previousDate]'";
		$getPrevData = mysqli_query($mysqli, $previousData);
		$prevArray = mysqli_fetch_array($getPrevData, MYSQLI_ASSOC);
		
		?>
		
		<td><?php echo $prevArray[task];?></td>
	</tr>
	
	<?php if(strpos($session->accountType,"admin")) { ?>
	<tr>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<th>Completed: </th>
		<td><input name="completed" type="radio" value="yes" <?php if($reportData[completed] == "yes") { echo "checked"; }?> > Yes
			<input name="completed" type="radio" value="no" <?php if($reportData[completed] == "no") { echo "checked"; }?>> No
			<input name="completed" type="radio" value="na" <?php if($reportData[completed] == "na") { echo "checked"; }?>> NA
			<input name="submit" type="submit">
		</td>
		<input type="hidden" name="previousDate" value="<?php echo $_GET[previousDate]; ?>" />
		<input type="hidden" name="ID" value="<?php echo $ID; ?>" />
		<input type="hidden" name="type" value="completed" />
	</form>
	</tr>
	<?php } ?>
	
	
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	
	<?php if(strpos($session->accountType,"admin")) { ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<tr>
		<th width="180">Task for next week: </th>
		<td><textarea name="task" cols="30" rows="4"><?php echo $reportData[task];?></textarea></td>
	</tr>
	<tr>
		<th>&nbsp;</th>
		<td><input name="submit" type="submit"></td>
	</tr>
		<input type="hidden" name="previousDate" value="<?php echo $_GET[previousDate]; ?>" />
		<input type="hidden" name="ID" value="<?php echo $ID; ?>">
		<input type="hidden" name="type" value="nextTask">
	</form>
	<?php } else { ?>
	<tr>
		<th width="180">Task for next week: </th>
		<td><?php echo $reportData[task];?></td>
	</tr>
	<?php } ?>
	
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<tr>
		<th>Discussion Topics: </th>
		<td><?php echo $reportData[discussion];?></td>
	</tr>
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<tr>
		<th>Current project(s)/goal(s): </th>
		<td><?php echo $reportData[projectsGoals];?></td>
	</tr>
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<tr>
		<th>Agenda: </th>
		<td><?php echo $reportData[agenda];?></td>
	</tr>
	
</table>

</body>
</html>