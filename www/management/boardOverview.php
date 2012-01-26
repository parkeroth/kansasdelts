<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/BusinessItem.php';
require_once 'classes/Meeting.php';


if(isset($_GET[board])){
	$board =  $_GET[board];
} else {
	header("location: /error.php");
}


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>

<script type="text/javascript">
	$(function() {
		$("#timepicker").datetimepicker({
			ampm: true
		});

		$("#updateButtonCurrent").click(function() {
			var date = $("#datepickerCurrent").val();
			var month = date.substr(0,2);
			var day = date.substr(3,2);
			var year = date.substr(6,4);
			var URL = 'manageReports.php?meeting_date=' + year + '-' + month + '-' + day;
			URL += '&board=<?php echo $board ?>';

			window.location.href=URL
		});
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1><?php echo Position::$BOARD_ARRAY[$board]; ?> Overview</h1>
<div id="position_list">
	<h2>Positions</h2>
	<?php
		$position_manager = new Position_Manager();
		$task_maanger = new TaskManager();

		$position_list = $position_manager->get_positions_by_board($board);

		echo "<table class=\"task_list\">\n";
		echo "<tr><td></td><td></td><td></td></tr>";

		foreach($position_list as $position){
			$task_list = $task_maanger->get_tasks_by_position($position->id, 'committed');

			echo "<tr>\n";
			echo "<th><a href=\"positionOverview.php?position=$position->id\">$position->title:</a> </th>\n";

			if($task_list)
			{
				echo '<td>';
				foreach($task_list as $task){
					echo '<div class="'.$task->get_progress_class().'">'.$task->title;
					echo " <a href=\"taskForm.php?id=$task->id\">Edit</a></div>";
				}
				echo "</td>";
			} else {
				echo "<td>No Tasks</td>";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
?>
</div>
<div id="meeting_list">
	<h2>Meetings</h2>
	<ul id="item_list">
<?php
	$meeting_manager = new Meeting_Manager();
	$meeting_list = $meeting_manager->get_meetings_by_type($board);
	if($meeting_list){
		foreach($meeting_list as $meeting){
			$date_str = date('M j, Y', strtotime($meeting->date));
?>
			<li>
				<b><?php echo $date_str; ?></b>
				<a href="manageMeeting.php?id=<?php echo $meeting->id; ?>">View</a><br>
			</li>
<?php		
			
		}
	} else {
		echo '<p>No previous meetings.</p>';
	}
	
?>
	</ul>
</div>
<div class="clear_block"></div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>