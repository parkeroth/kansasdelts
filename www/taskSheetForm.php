<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker();
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">
	
	<h1>Task Sheet</h1>
	
	<p>Fill this form out before every admin/exec meeting. If you had a committee meeting this week email them to reports@kansasdelts.org</p>
	<p>&nbsp;</p>
	<form enctype="multipart/form-data" action="php/taskSheet.php" method="POST">
		
		<table style="text-align:left;">
			<tr>
				<td colspan="2"><h2>Task Section</h2></td>
				</tr>
			<tr>
				<th>Date of Meeting: </th>
				<td><input name="dateMeeting" type="text" id="datepicker" size="10" /></td>
				</tr>
			<tr>
				<th>Task for the Week: </th>
				<td><textarea name="task" cols="40" rows="10"></textarea></td>
				</tr>
			<tr>
				<td colspan="2"><h2>Report Section</h2></td>
				</tr>
			<tr>
				<th>Items for group discussion: </th>
				<td><textarea name="discussion" cols="40" rows="10"></textarea></td>
				</tr>
			<tr>
				<th>Current project(s)/goal(s): </th>
				<td><textarea name="projectsGoals" cols="40" rows="10"></textarea></td>
				</tr>
			<tr>	
				<th>Include in agenda: </th>
				<td><textarea name="agenda" cols="40" rows="10"></textarea></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				</tr>
			<tr>
				<td></td>
				<td style="text-align:right"><input type="submit" value="Submit" /></td>
				</tr>
			</table>
		
		<input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
		</form>
	
</div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>