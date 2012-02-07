<?
session_start();
$authUsers = array('admin', 'secretary', 'pres', 'vpInternal', 'vpExternal');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Chapter_Attendance.php';
require_once 'classes/Minutes.php';
require_once 'classes/Meeting.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';

function haz_quorum($present, $total){
	$num_needed = ceil($total / 2) + 1;
	return $present >= $num_needed;
}

$super_list = array('admin', 'secretary', 'pres');
$haz_super_powers = $session->isAuth($super_list);

$meeting_manager = new Meeting_Manager();
$report_manager = new ReportManager();
$position_manager = new Position_Manager();
$minutes_manager = new Minutes_Manager();

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$chapter_id = $_POST['id'];
	
	$chapter_meeting = new Meeting($chapter_id);
	$previous_meeting = $meeting_manager->get_previous_meeting($chapter_meeting->id);
	$exec_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'exec');
	$internal_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'internal');
	$external_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'external');
	
	$prev_minutes = $minutes_manager->get_by_meeting($previous_meeting->id);
	
	$minutes = $minutes_manager->get_by_meeting($chapter_id);
	$needs_insert = false;
	if($minutes == NULL){
		$minutes = new Minutes();
		$needs_insert = true;
	}
	
	$minutes->meeting_id = $chapter_id;
	$minutes->presiding_officer_id = $_POST[officer];
	$minutes->start_time = $_POST[start_time];
	$minutes->end_time = $_POST[end_time];
	$minutes->new_business = addslashes($_POST['new-business']);
	$minutes->unfinished_business = addslashes($_POST['unfinished-business']);
	$minutes->good_of_order = addslashes($_POST['good-of-order']);
	
	if($needs_insert){
		echo 'asdf';
		$minutes->insert();
	} else {
		$minutes->save();
	}
	
	$exec_list = $position_manager->get_positions_by_board('exec');
	foreach($exec_list as $exec_position){
		$report_list = $report_manager->get_reports_by_meeting($exec_meeting->id, $exec_position->id);
		$report = $report_list[0];
		
		$report->agenda = $_POST[$exec_position->type];
		$report->save();
		
		/*if($exec_position->type != 'pres' && in_array($exec_position->type, Meeting::$PRESIDING_OFFICERS)){
			// Get the board the person oversees
			$board = array_search($exec_position->type, Meeting::$PRESIDING_OFFICERS);
			$admin_list = $position_manager->get_positions_by_board($board);
			foreach($admin_list as $admin_position){
				$admin_meeting = ${$board.'_meeting'};
				$report_list = $report_manager->get_reports_by_meeting($admin_meeting->id, $admin_position->id);
				$report = $report_list[0];
				
				$report->agenda = $_POST[$admin_position->type];
				$report->save();
			}
		}*/
	}

	if($prev_minutes != NULL){
		$prev_minutes->unfinished_business = $_POST[old-business];
		$prev_minutes->save();
	}
	
	header('location: manageChapter.php');
}

$meeting_id = $_GET[id];
$action = $_GET[action];

// Check for authorized to edit
if($action == 'edit' && !$haz_super_powers){
	header('location: ../error.php?page=unauthorized');
}

$member_manager = new Member_Manager();
$member_list  = $member_manager->get_all_members();

$chapter_meeting = new Meeting($meeting_id);
$previous_meeting = $meeting_manager->get_previous_meeting($chapter_meeting->id);
$exec_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'exec');
$internal_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'internal');
$external_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'external');

$minutes_manager = new Minutes_Manager();
$current_minutes = $minutes_manager->get_by_meeting($meeting_id);

$attendance_manager = new Chapter_Attendance_Manager();
$number_absent = $attendance_manager->get_total_by_meeting($meeting_id, 'absent');
$number_excused  = $attendance_manager->get_total_by_meeting($meeting_id, 'excused');
$number_present = count($member_list) - $number_absent - $number_excused;

$voting_absent = $attendance_manager->get_total_by_meeting($meeting_id, 'absent', true);
$voting_excused = $attendance_manager->get_total_by_meeting($meeting_id, 'excused', true);
$voting_total = $member_manager->get_total_voting();
$voting_present = $voting_total - $voting_absent - $voting_excused;

$haz_quorum = haz_quorum($voting_present, $voting_total);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style>
	.execPosition {
		font-weight: bold;
		color: #DDAB26;
		padding-left: 50px;
	}
	
	.adminSection {
		padding: 20px;
	}
	
	.adminPosition {
		font-weight: bold;
		color: #DDAB26;
		padding-left: 50px;
	}
	.agenda-heading{
		font-weight: bold;
		color: #DDAB26;
	}
	.report {
		padding: 10px;
		padding-left: 60px;
	}
</style>

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

<h1>Chapter Minutes - <?php echo date('M j, Y', strtotime($chapter_meeting->date)); ?></h1>

<?php if($action == 'edit'){ ?>
<form id="minutes" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitted()">
<?php } ?>

<table>
<tr>
	<th>Attendance:</th>
	<td>Present: <strong><?php echo $number_present; ?></strong> Absent: <strong><?php echo $number_absent; ?></strong> Excused: <strong><?php echo $number_excused; ?></strong> 
		| <a href="attendanceRecords.php">View Records</a></td>
</tr>
<tr>
	<th>Voting Members:</th>
	<td>Present: <strong><?php echo $voting_present; ?></strong> Total: <strong><?php echo $voting_total; ?></strong></td>
</tr>
<tr>
	<th>Quorum:</th>
	<td><?php if($haz_quorum){echo 'Yes';} else {echo 'No';} ?></td>
</tr>
<tr>
	<th>Presiding Officer:</th>
	<td>
		<select name="officer">
		<?php
			$slug = Meeting::$PRESIDING_OFFICERS[$meeting->type];
			$position = new Position(NULL, $slug);
			echo $position->id;
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
</table>

<h2>Officer Reports</h2>
<?php
	
	$exec_list = $position_manager->get_positions_by_board('exec');
	
	foreach($exec_list as $exec_position){
		$officer = $exec_position->get_current_member_id();
		$report_list = $report_manager->get_reports_by_meeting($exec_meeting->id, $exec_position->id);
		$report = $report_list[0];
		
		echo '<span class="execPosition">'.$exec_position->title.': </span>';
		echo $officer->first_name.' '.$officer->last_name.'<br>';						//TODO: show all names not just the first
		echo '<div class="report">';
		if($action == 'edit'){
			echo '<textarea name="'.$exec_position->type.'" cols="44" rows="5">'.$report->agenda.'</textarea>';
		} else {
			if($report->agenda){
				echo $report->agenda;
			} else {
				echo 'Proud to be Delt.';
			}
		}
		/*
		if($exec_position->type != 'pres' && in_array($exec_position->type, Meeting::$PRESIDING_OFFICERS)){
			// Get the board the person oversees
			$board = array_search($exec_position->type, Meeting::$PRESIDING_OFFICERS);
			$admin_list = $position_manager->get_positions_by_board($board);
			
			echo '<div class="adminSection">';
			foreach($admin_list as $admin_position){
				$officer = $admin_position->get_current_member_id();
				$admin_meeting = ${$board.'_meeting'};
				$report_list = $report_manager->get_reports_by_meeting($admin_meeting->id, $admin_position->id);
				$report = $report_list[0];
				echo '<span class="adminPosition">'.$admin_position->title.': </span>';
				echo $officer->first_name.' '.$officer->last_name.'<br>';				//TODO: show all names not just the first
				echo '<div class="report">';
					if($action == 'edit'){
						echo '<textarea name="'.$admin_position->type.'" cols="40" rows="5">'.$report->agenda.'</textarea>';
					} else {
						if($report->agenda){
							echo $report->agenda;
						} else {
							echo 'Proud to be Delt.';
						}
					}
				echo '</div>';
			}
			echo '</div>';
		}*/
		echo '</div>';
	}
?>
<h2>Meeting Notes</h2>
<?php
	
	if($previous_meeting != NULL){
		$previous_minutes = $minutes_manager->get_by_meeting($previous_meeting->id);
		
		echo '<span class="execPosition">Old Business: </span>';
		echo '<div class="report">';
		
			if($action == 'edit'){
				echo '<textarea name="old-business" cols="44" rows="10">';
				echo $previous_minutes->unfinished_business;
				echo '</textarea>';
			} else {
				echo $previous_minutes->unfinished_business;
			}
			
		echo '</div>';
	}	
	
	echo '<span class="execPosition">New Business: </span>';
	echo '<div class="report">';

		if($action == 'edit'){
			echo '<textarea name="new-business" cols="44" rows="10">';
			echo $current_minutes->new_business;
			echo '</textarea>';
		} else {
			echo $current_minutes->new_business;
		}

	echo '</div>';
	
	echo '<span class="execPosition">Unfinished Business: </span>';
	echo '<div class="report">';

		if($action == 'edit'){
			echo '<textarea name="unfinished-business" cols="44" rows="10">';
			echo $current_minutes->unfinished_business;
			echo '</textarea>';
		} else {
			echo $current_minutes->unfinished_business;
		}

	echo '</div>';
	
	echo '<span class="execPosition">Good of Order: </span>';
	echo '<div class="report">';

		if($action == 'edit'){
			echo '<textarea name="good-of-order" cols="44" rows="10">';
			echo $current_minutes->good_of_order;
			echo '</textarea>';
		} else {
			echo $current_minutes->good_of_order;
		}

	echo '</div>';
	
	if($action == 'edit'){
		
	?>

<p><input type="submit" value="Save Changes"  /></p>

<input type="hidden" name="id" value="<?php echo $chapter_meeting->id; ?>" >

</form>

	<?php } ?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>