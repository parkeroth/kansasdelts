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
	
	<h1>Minutes Submission</h1>
	
	<p>Fill out this form to submit committee meeting minutes.</p>
	<p>&nbsp;</p>
	<form action="php/minutes.php" method="POST">
		
		<table style="text-align:left;">
			<tr>
				<th>Date of meeting: </th>
				<td><input name="dateMeeting" type="text" id="datepicker" size="10" /></td>
				</tr>
			<tr>
				<th>People in attendance: </th>
				<td><textarea name="attendance" cols="40" rows="10"></textarea></td>
				</tr>
			<tr>
				<th>Topics Covered: </th>
				<td><textarea name="topics" cols="40" rows="10"></textarea></td>
				</tr>
			<tr>
				<th><label>Start Time: </th>
				<td>		
					<select name="startHour">
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
					<select name="startMinute">
						<option value="00">00</option>
						<option value="15">15</option>
						<option value="30">30</option>
						<option value="45">45</option>
						</select>
					<select name="startAmpm">
						<option value="AM">AM</option>
						<option value="PM">PM</option>
						</select>
					</label>
					</td>
				</tr>
			<tr>
				<th><label>End Time: </th>
				<td>		
					<select name="endHour">
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
					<select name="endMinute">
						<option value="00">00</option>
						<option value="15">15</option>
						<option value="30">30</option>
						<option value="45">45</option>
						</select>
					<select name="endAmpm">
						<option value="AM">AM</option>
						<option value="PM">PM</option>
						</select>
					</label>
					</td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
				</tr>
			<tr>
				<td></td>
				<td style="text-align:right"><input type="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>
	</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>