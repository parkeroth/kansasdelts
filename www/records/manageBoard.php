<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/Meeting.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if($_POST[action] == 'meeting_add'){
		$meeting_manager = new Meeting_Manager();
		$boards = $_POST[boards];
		$datetime = $_POST[meeting_datetime];
		$date = date('Y-m-d', strtotime($datetime));
		$time = date('h:i:s', strtotime($datetime));
		
		foreach(Position::$BOARD_ARRAY as $board => $title){
			if($board != 'committee' && in_array($board, $boards)){
				$meeting = $meeting_manager->get_meeting($board, $date);
				if(!$meeting){
					$meeting = new Meeting();
					$meeting->date = $date;
					$meeting->time = NULL;		//TODO: Fix this to use a time
					$meeting->type = $board;
					$meeting->insert();
					$meeting->create_reports();
				}
			}
		}
	
	} else if($_POST[action] == 'meeting_remove'){
		$meeting_id = $_POST[id];
		$meeting = new Meeting($meeting_id);
		$meeting->delete();
	}
	
}

$meeting_manager = new Meeting_Manager();

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
		
		$("input.edit").click(function(event){
			window.location.href = 'boardMinutes.php?id=' + event.target.id;
		})
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1>Manage Board Meetings</h1>
<?php 
	foreach(Position::$BOARD_ARRAY as $board => $title){
		if($board != 'committee'){		// Meh. Wish I could do better
			$next_meeting = $meeting_manager->get_next_meeting($board);
			$meeting_list = $meeting_manager->get_meetings_by_type($board, NULL, date('Y-m-d'));
			
			echo '<div class="three_col">';
			echo '<h2 class="center">'.$title.'</h2>';
?>
	<table align="center">
<?php
			if($next_meeting){
				print_meeting_row($next_meeting);
			} else {
				echo '<tr>';
				echo '<td colspan="2">No Meeting This Week</td>';
				echo '</tr>';
			}
?>
	</table>
	<p>&nbsp;</p>
	<table align="center">
<?php
			foreach($meeting_list as $meeting){
				if($meeting->id != $next_meeting->id)
					print_meeting_row($meeting);
			}
?>
	</table>
<?php
			echo '</div>';
		}
	}
?>
<div class="clear_block">
	<p>&nbsp;</p>
	<h3>Add Meeting</h3>
	<p>Remember to add the admin/exec meeting well enough in advance for people to fill out their weekly reports.</p>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
		<table>
			<tr>
				<th>Boards</th>
				<td>
<?php
		foreach(Position::$BOARD_ARRAY as $board => $title){
			if($board != 'committee'){		// Meh. Wish I could do better
				echo '<input type="checkbox" name="boards[]" value="'.$board.'" checked="checked" /> '.$title;
			}
		}
?>					
				</td>
			</tr>
			<tr>
				<th>Meeting Date</th>
				<td>
					<input name="meeting_datetime" type="text" id="datepickerCurrent" size="8"
						     value="<?php echo date('m/d/Y', strtotime('this Sunday')); ?>" />
					<input type="hidden" name="action" value="meeting_add" />
					<input type="hidden" name="meeting_type" value="<?php echo $board; ?>" />
					<input type="submit" value="Add" />
				</td>
			</tr>
		</table>
	</form>
</div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>