<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'secretary');
include_once('../php/authenticate.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Position.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Report.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/BusinessItem.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Meeting.php';

if(isset($_GET[board])){
	$board =  mysql_real_escape_string($_GET[board]);
} else {
	header("location: /error.php");
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
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
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1><?php echo Position::$BOARD_ARRAY[$board]; ?> Overview</h1>
<div id="position_list">
	<h2>Positions</h2>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$position_manager = new PositionManager($mysqli);
		$task_maanger = new TaskManager($mysqli);

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
					echo '<span class="'.$task->get_progress_class().'">'.$task->title.'</span>';
					echo " <a href=\"taskForm.php?id=$task->id\">Edit</a><br>";
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
	$meeting_manager = new MeetingManager($mysqli);
	$meeting_list = $meeting_manager->get_meetings($board);
	if($meeting_list){
		foreach($meeting_list as $meeting){
			$date_str = date('M j, Y', strtotime($meeting->meeting_date));
			if($meeting->has_been_processed()){
				$link_str = 'View';
			} else {
				$link_str = 'Edit';
			}
			echo '<li>';
			echo '<b>'.$date_str.'</b> ';
			echo '<a href="manageMeeting.php?board='.$board.'&meeting_date='.$meeting->meeting_date.'">'.$link_str.'</a><br>';
			echo '</li>';
		}
	} else {
		echo '<p>No previous meetings.</p>';
	}
	
?>
	</ul>
</div>
<div class="clear_block"></div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>