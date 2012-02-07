<?php
session_start();
$authUsers = array('admin', 'secretary', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/Meeting.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");

$REQUIRE_NOT_PASSED = false;

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if($_POST[action] == 'meeting_add'){
		$meeting_manager = new Meeting_Manager();
		$chapter_date = date('Y-m-d', strtotime($_POST['chapter_date']));
		$board_date = date('Y-m-d', strtotime($_POST['board_date']));
		
		$chapter_meeting = $meeting_manager->get_meeting('chapter', date('Y-m-d', strtotime($chapter_date)));
		if(!$chapter_meeting){
			$chapter_meeting = new Meeting();
			$chapter_meeting->date = $chapter_date;
			$chapter_meeting->type = 'chapter';
			$chapter_meeting->formal = $_POST[formal];
			$chapter_meeting->insert();
			$chapter_meeting->associate_board_meetings($board_date); // Not sure this is the best place for this
		}
		
	
	} else if($_POST[action] == 'meeting_remove'){
		$meeting_id = $_POST[id];
		$meeting = new Meeting($meeting_id);
		$meeting->delete();
	}
	
}

$meeting_manager = new Meeting_Manager();
$board_meeting_dates = $meeting_manager->get_board_meeting_dates();

function print_meeting_row($meeting){
	$date_str = date('M j, Y', strtotime($meeting->date));
	echo '<tr class="bold">';
	echo '<td width="90">';
	if($meeting->has_past()){
		echo "<strong>$date_str</string>";
	} else {
		echo $date_str;
	}
	echo '</td>';
	echo '<td>';
	if($meeting->can_remove()){
		echo '<form class="remove"  action="'.$_SERVER['PHP_SELF'].'" method="POST">';
		echo '<input type="hidden" name="action" value="meeting_remove" />';
		echo '<input type="hidden" name="id" value="'.$meeting->id.'" />';
		echo '<input class="edit" id="'.$meeting->id.'" type="button" value="Edit" />';
		echo '<input type="submit" value="Remove" />';
		echo '</form>';
	} else {
		echo '<input class="edit" id="'.$meeting->id.'" type="button" value="Edit" />';
	}
	echo '</td>';
	echo '</tr>';
}

?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">
	$(function() {
		$("#datepickerCurrent").datepicker();

		$("#updateButtonCurrent").click(function() {
			var date = $("#datepickerCurrent").val();
			var month = date.substr(0,2);
			var day = date.substr(3,2);
			var year = date.substr(6,4);
			var URL = 'manageReports.php?meeting_date=' + year + '-' + month + '-' + day;
			URL += '&board=<?php echo $board ?>';

			window.location.href=URL
		});
		
		$("form.remove").submit(function(){
			return confirm('Are you sure you want to delete this meeting. This could cause some reports to be removed from the system.')
		})
		
		$("input.edit-agenda").click(function(event){
			window.location.href = 'chapterAgenda.php?action=edit&id=' + event.target.id;
		})
		$("input.excuse-member").click(function(event){
			window.location.href = 'attendanceExcused.php?id=' + event.target.id;
		})
		$("input.edit-minutes").click(function(event){
			window.location.href = 'chapterMinutes.php?action=edit&id=' + event.target.id;
		})
		$("input.take-attendance").click(function(event){
			window.location.href = 'attendanceForm.php?id=' + event.target.id;
		})
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1>Manage Chapter Meetings</h1>

<table>
<?php 
	$chapter_list = $meeting_manager->get_meetings_by_type('chapter', $limit);
	
	echo '<table>';
	foreach($chapter_list as $meeting){
		$date_str = date('M j, Y', strtotime($meeting->date));
		echo '<tr>';
		echo '<td width="120"><strong>'.$date_str.'</strong></td>';
		if($meeting->has_past() && $REQUIRE_NOT_PASSED){
			echo '<td><input id="'.$meeting->id.'" class="edit-minutes" type="button" value="Edit Minutes" /></td>';
		} else {
			echo '<td><input id="'.$meeting->id.'" class="edit-agenda" type="button" value="Edit Agenda" /></td>';
			echo '<td><input id="'.$meeting->id.'" class="excuse-member" type="button" value="Excuse Members" /></td>';
			echo '<td><input id="'.$meeting->id.'" class="take-attendance" type="button" value="Take Attendance" /></td>';
			echo '<td><input id="'.$meeting->id.'" class="edit-minutes" type="button" value="Edit Minutes" /></td>';
		}
		echo '</tr>';
	}
	echo '</table>';
?>
</table>

<?php
	if(count($chapter_list) == 0){
		echo '<p>No chapter meetings for this semester</p>';
	}
?>

<p>&nbsp;</p>
<h3>Add Chapter Meeting</h3>
<p>Be sure to select the correct date of the applicable series of board meetings. This is usually the day prior to the chapter meeting.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	<table>
		<tr>
			<th>Formal? </th>
			<td>
				Yes<input type="radio" name="formal" value="1" /> No<input type="radio" name="formal" value="0" checked />
			</td>
		</tr>
		<tr>
			<th>Board Meeting Date</th>
			<td>
				<select name="board_date">
					<option value="select">Select One</option>
<?php
		foreach($board_meeting_dates as $date){
			$date_str = date('M j, Y', strtotime($date));
			echo '<option value="'.$date.'">'.$date.'</option>';
		}
?>
				</select> <a href="manageBoard.php">Manage Board Meetings</a>
			</td>
		</tr>
		<tr>
			<th>Chapter Meeting Date</th>
			<td>
				<input name="chapter_date" type="text" id="datepickerCurrent" size="8"
							value="<?php echo date('m/d/Y', strtotime('this Monday')); ?>" />
				<input type="hidden" name="action" value="meeting_add" />
				<input type="submit" value="Add" />
			</td>
		</tr>
	</table>
</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>