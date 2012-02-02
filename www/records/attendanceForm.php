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
		
		if($attendance_record->status != 'excused'){
			// Case 1: was absent and is no longer
			if($attendance_record->status == 'absent' && !$_POST[$member->id]){
				$attendance_record->delete();

			// Case 2: was not absent but is now	
			} else if($attendance_record->status != 'absent' && $_POST[$member->id]){
				$attendance_record = new Chapter_Attendance();
				$attendance_record->meeting_id = $meeting->id;
				$attendance_record->member_id = $member->id;
				$attendance_record->status = 'absent';
				$attendance_record->insert();

			// Case 3: was not absent but should not be now
			} else {
				// Do nothing
			}
		}		
	}
	$_GET[id] = $meeting_id;
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

<style>
tr.black {
	background-color: #CCC;
	color:#000;
}

tr.white {
	
}
</style>

<link type="text/css" href="css/layout.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		
		
		
	});
	
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

	<h1 class="center">Excuse Members From Chapter - <?php echo date('M j, Y', strtotime($meeting->date));?></h1>
	
	<form id="attendance" name="attendance" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center" cellspacing="0">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Absent</strong></td></tr>
			<?php 
		
		$rowColor = "white";
		$count = 1;
		
		foreach($member_list as $member){
			$attendance_record = $attendance_manager->get_record_by_meeting_member($member->id, $meeting->id);
			
			if($attendance_record->status != 'excused'){
				echo "<tr class=\"$rowColor\">";
				echo "<td style=\"text-align: left;\">";
				echo 	"<label>".$member->first_name." ".$member->last_name." </td>\n";

				if($attendance_record->status == 'absent')
				{
					$checked = "checked=\"checked\"";
				} else {
					$checked = "";
				}
				echo "<td><input type=\"checkbox\" name=\"".$member->id."\" value=\"1\" $checked /></label></td>";
				echo "</tr>\n";

				if($rowColor == "white"){
					$rowColor = "black";
				}
				else
				{
					$rowColor = "white";
				}

				if($count % 4 == 0)
				{
					echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
					echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";
				}
				$count++;
			}
		}
	?>
			</table>
		<p style="text-align:center;">
			<input type="hidden" name="meeting_id" value="<?php echo $meeting_id;?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>