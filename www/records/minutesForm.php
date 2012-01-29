<?
session_start();
$authUsers = array('admin', 'secretary');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Chapter_Attendance.php';
require_once 'classes/Minutes.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';



if($_SERVER['REQUEST_METHOD'] == "POST") {
	$meeting_date = $_POST['date'];
	$meeting_type = $_POST['type'];
	$minutes = new Minutes($meeting_date, $meeting_type);
	
	$minutes->meeting_type = $meeting_type;
	$minutes->presiding_officer_id = $_POST[officer];
	$minutes->start_time = $_POST[start_time];
	$minutes->formal = $_POST[formal];
	$minutes->end_time = $_POST[end_time];
	$minutes->old_business = mysql_real_escape_string($_POST[old_business]);
	$minutes->new_business = mysql_real_escape_string($_POST[new_business]);
	$minutes->unfinished_business = mysql_real_escape_string($_POST[unfinished_business]);
	$minutes->good_of_order = mysql_real_escape_string($_POST[good_of_order]);
	
	if($minutes->id == NULL){
		$minutes->insert();
	} else {
		$minutes->save();
	}
	
	$_GET['date'] = $meeting_date;
	$_GET['type'] = $meeting_type;
} else {
	$meeting_date = $_GET['date'];
	$meeting_type = $_GET['type'];
	$minutes = new Minutes($meeting_date, $meeting_type);
	
	if($minutes == NULL){
		echo 'AGSDGFA';
	}
	
}

$attendance = new Chapter_Attendance($meeting_date);
$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members();

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script type="text/javascript" src="../../js/jquery-1.4.2.min.js"></script>
<script language="JavaScript" type="text/JavaScript">
	function pad(number, length) {
		var str = '' + number;
		while (str.length < length) {
			str = '0' + str;
		}

		return str;
	}

	function date_string(date){
		var str = '';
		var year = date.getFullYear();
		var month = pad(date.getMonth()+1, 2);
		var day = pad(date.getDate(), 2);
		
		var hour = pad(date.getHours(), 2);
		var minutes = pad(date.getMinutes(), 2);
		var seconds = pad(date.getSeconds(), 2);
		
		str = year + '-' + month + '-' + day;
		str = str + ' ' + hour + ':' + minutes + ':' + seconds;
		return str;
	}
	
	var submitFormOkay = false;
	
	function submitted() {
		submitFormOkay = true;
	}
	
	$(document).ready(function() {
		// Fill start time with current time string
		$("#start_button").click(function() {
			var now = new Date();
			$("#start_time").val(date_string(now));
		});
		
		// Fill end time with current time string
		$("#end_button").click(function() {
			var now = new Date();
			$("#end_time").val(date_string(now));
		});
		
		// Load all initial field values
		$(':input').each(function() {
			$(this).data('initialValue', $(this).val());
		});
		
		

		// Check for dirty fields on navigate away
		
		
		window.onbeforeunload = function(){
			var msg = 'You haven\'t saved your changes.';
			var isDirty = false;

			$(':input').each(function () {
				if($(this).data('initialValue') != $(this).val()){
					isDirty = true;
				}
			});

			if(isDirty == true && !submitFormOkay){
				return msg;
			}
		};
	});
	
	
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Chapter Minutes</h1>

<form id="minutes" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitted()">
<table>
<tr>
	<th>Attendance:</th>
	<td>
		Present:<b><?php echo $attendance->get_count('present'); ?></b> Absent:<b><?php echo $attendance->get_count('absent');?></b> Excused:<b><?php echo $attendance->get_count('excused');?></b> 
		<a href="#">Edit</a>
	</td>
</tr>
<tr>
	<th>Quorum:</th>
	<td>
		<?php 
			if($attendance->haz_quorum()){
				echo 'Yes';
			} else {
				echo 'No';
			}
		?>
	</td>
</tr>
<tr>
	<th>Meeting Type:</th>
	<td>
		<select name="formal">
			<option value="0" <?php if($minutes->meeting_type == 'regular') echo 'selected="selected"'; ?> >Regular</option>
			<option value="1" <?php if($minutes->meeting_type == 'formal') echo 'selected="selected"'; ?> >Formal</option>
		</select>
	</td>
</tr>
<tr>
	<th>Presiding Officer:</th>
	<td>
		<select name="officer">
		<?
			$position = new Position(NULL, 'pres');
			foreach($member_list as $member){
				if($member->is_position($position->id)){
					$selected = "selected";
				} else {
					$selected = "";
				}
				
				echo "<option value=\"$member->id\" $selected>$member->first_name $member->last_name</option>";
			}
		?>
		
		</select>
	</td>
</tr>
<tr>
	<th>Start Time:</th>
	<td>
		<input id="start_time" name="start_time" type="text" value="<?php echo $minutes->start_time; ?>">
		<input id="start_button" type="button" value="Now" />
	</td>
</tr>
<tr>
	<th>End Time:</th>
	<td>
		<input id="end_time" name="end_time" type="text" value="<?php echo $minutes->end_time; ?>">
		<input id="end_button" type="button" value="Now" />
	</td>
</tr>
<tr>
	<th>Old Business:</th>
	<td>
		<textarea name="old_business" cols="40" rows="10"><?php echo $minutes->old_business; ?></textarea>
	</td>
</tr>
<tr>
	<th>New Business:</th>
	<td>
		<textarea name="new_business" cols="40" rows="10"><?php echo $minutes->new_business; ?></textarea>
	</td>
</tr>
<tr>
	<th>Unfinished Business:</th>
	<td>
		<textarea name="unfinished_business" cols="40" rows="10"><?php echo $minutes->unfinished_business; ?></textarea>
	</td>
</tr>
<tr>
	<th>For the Good of the Order:</th>
	<td>
		<textarea name="good_of_order" cols="40" rows="10"><?php echo $minutes->good_of_order; ?></textarea>
	</td>
</tr>
<tr>
	<th></th>
	<td><input type="submit" value="Save Changes"  /></td>
</tr>
</table>

<input type="hidden" name="type" value="<?php echo $meeting_type; ?>" >
<input type="hidden" name="date" value="<?php echo $meeting_date; ?>" >

</form>
</body>
</html>