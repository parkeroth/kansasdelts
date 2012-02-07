<?php
session_start();
$authUsers = array('admin', 'secretary', 'pres');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Chapter_Attendance.php';
require_once 'classes/Meeting.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members();
$attendance_manager = new Chapter_Attendance_Manager();

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$meeting_id = $_POST['meeting_id'];
	$meeting = new Meeting($meeting_id);
	
	foreach($member_list as $member){
		$attendance_record = $attendance_manager->get_record_by_meeting_member($member->id, $meeting->id);
		
		// Case 1: was excused and is no longer
		if($attendance_record->status == 'excused' && !$_POST[$member->id]){
			$attendance_record->delete();
			
		// Case 2: was not excused but is now	
		} else if($attendance_record->status != 'excused' && $_POST[$member->id]){
			$attendance_record = new Chapter_Attendance();
			$attendance_record->meeting_id = $meeting->id;
			$attendance_record->member_id = $member->id;
			$attendance_record->status = 'excused';
			$attendance_record->insert();
			
		// Case 3: was not excused but should not be now
		} else {
			// Do nothing
		}
	}
	
	$_GET[id] = $meeting_id;
	header('location: manageChapter.php');
}
	


 
/**
 * Form Section
 */
 
$meeting_id = $_GET[id];
if(!isset($meeting_id)){
	header('location: ../error.php');
}

$meeting = new Meeting($meeting_id);



include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		$("#toggle-longterm").click(function(){
			$(".excused").attr("checked",'checked');
		})
	});
	
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
	
	<h1 class="center">Excuse Members From Chapter - <?php echo date('M j, Y', strtotime($meeting->date));?></h1>
	
	<p class="center"><input type="button" id="toggle-longterm" value="Check Pre-Excused" /></p>
	
	<form id="excused" name="excused" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Excused</strong></td></tr>
			<?php 
			
		foreach($member_list as $member){
			$attendance_record = $attendance_manager->get_record_by_meeting_member($member->id, $meeting->id);
			if($member->excused){
				$class = 'class="excused"';
			} else {
				$class = '';
			}
			if($attendance_record->status == 'excused'){
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
			
			echo '<tr>';
			echo '<th>'.$member->first_name.' '.$member->last_name.'</th>';
			echo '<td><input type="checkbox" name="'.$member->id.'" value="1" '.$class.'" '.$checked,' /></td>';
			echo '</tr>';
		}
	?>
			</table>
		<p style="text-align:center;">
			<input  type="hidden" name="meeting_id" value="<?php echo $meeting->id;?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>