<?
session_start();
$authUsers = array('admin', 'secretary', 'pres', 'vpInternal', 'vpExternal');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Chapter_Attendance.php';
require_once 'classes/Minutes.php';
require_once 'classes/Meeting.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';

$super_list = array('admin', 'secretary', 'pres');
$haz_super_powers = $session->isAuth($super_list);

$meeting_manager = new Meeting_Manager();
$report_manager = new ReportManager();
$position_manager = new Position_Manager();

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$chapter_id = $_POST['id'];
	
	$chapter_meeting = new Meeting($chapter_id);
	$previous_meeting = $meeting_manager->get_previous_meeting($chapter_meeting->id);
	$exec_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'exec');
	$internal_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'internal');
	$external_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'external');
	
	$exec_list = $position_manager->get_positions_by_board('exec');
	foreach($exec_list as $exec_position){
		$report_list = $report_manager->get_reports_by_meeting($exec_meeting->id, $exec_position->id);
		$report = $report_list[0];
		
		$report->agenda = $_POST[$exec_position->type];
		$report->save();
		
		if($exec_position->type != 'pres' && in_array($exec_position->type, Meeting::$PRESIDING_OFFICERS)){
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
		}
	}

	if($previous_meeting != NULL){
		$previous_minutes = $minutes_manager->get_by_meeting($previous_meeting->id);
		$previous_minutes->unfinished_business = $_POST[old-business];
		$previous_minutes->save();
	}
	
	$_GET['id'] = $chapter_id;
	$_GET['action'] = 'edit';
}

$meeting_id = $_GET[id];
$action = $_GET[action];

// Check for authorized to edit
if($action == 'edit' && !$haz_super_powers){
	header('location: ../error.php?page=unauthorized');
}

$chapter_meeting = new Meeting($meeting_id);
$previous_meeting = $meeting_manager->get_previous_meeting($chapter_meeting->id);
$exec_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'exec');
$internal_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'internal');
$external_meeting = $meeting_manager->get_meetings_by_chapter($chapter_meeting->id, 'external');

$minutes_manager = new Minutes_Manager();

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<style>
	.execPosition {
		font-weight: bold;
		color: #DDAB26;
	}
	
	.adminSection {
		padding: 20px;
	}
	
	.adminPosition {
		font-weight: bold;
		color: #DDAB26;
	}
	.agenda-heading{
		font-weight: bold;
		color: #DDAB26;
	}
	.report {
		padding: 10px;
	}
</style>

<script type="text/javascript" src="../../js/jquery-1.4.2.min.js"></script>
<script language="JavaScript" type="text/JavaScript">
	
	var submitFormOkay = false;
	
	function submitted() {
		submitFormOkay = true;
	}
	
	$(document).ready(function() {		
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

<h1>Chapter Agenda</h1>

<p><span class="agenda-heading">Date: </span><?php echo date('M j, Y', strtotime($chapter_meeting->date)); ?><br>
	<span class="agenda-heading">Formal: </span>
		<?php if ($chapter_meeting->formal) {
			echo 'Yes';
		} else {
			echo 'No';
		}	?>
</p>

<?php if($action == 'edit'){ ?>

<form id="minutes" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitted()">
<?php 
	}
	
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
		}
		echo '</div>';
	}

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
	
	if($action == 'edit'){
		
	?>

<p><input type="submit" value="Save Changes"  /></p>

<input type="hidden" name="id" value="<?php echo $chapter_meeting->id; ?>" >

</form>

	<?php } ?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>