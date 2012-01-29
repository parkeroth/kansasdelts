<?php
session_start();
echo $session->member_id;
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/BusinessItem.php';
require_once 'classes/Meeting.php';

if(isset($_GET[id])){
	$meeting_id = $_GET[id];
	$meeting = new Meeting($meeting_id);
	$board = $meeting->type;
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
			var URL = 'manageMeeting.php?meeting_date=' + year + '-' + month + '-' + day;
			URL += '&board=<?php echo $board ?>';

			window.location.href=URL
		});
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1><?php echo Position::$BOARD_ARRAY[$board]; ?> Meeting - <?php echo date('M j, Y', strtotime($meeting->date));?></h1>
<div id="report_list">
	<h2>Reports</h2>
	<?php		
		$position_manager = new Position_Manager();
		$report_manager = new ReportManager();

		$position_list = $position_manager->get_positions_by_board($board);

		echo "<table>\n";
		echo "<tr><td></td><td></td><td></td></tr>";

		foreach($position_list as $position){
			$report_list = $report_manager->get_reports_by_meeting($meeting->id, $position->id);

			echo "<tr>\n";
			echo "<th>$position->title: </th>\n";

			if($report_list)
			{
				$report = $report_list[0];
				echo "<td>";
				if($report->is_late()) {
					echo "<span class=\"redHeading\">LATE </span>";
				}

				if($report->status == 'complete'){
					echo "<span class=\"rankGreen\">Complete</span>";
				} else if($report->status == "incomplete"){
					echo "<span class=\"rankRed\">Incomplete</span>";
				} else if($report->status == 'pending'){
					echo "<span class=\"rankYellow\">Submitted</span>";
				}

				echo "</td>";
				echo "<td><div><a href=\"processReport.php?id=$report->id\">Edit</a></div></td>\n";
			} else {
				echo "<td colspan=\"3\">None</td>";
			}
			echo "</tr>\n";
		}
		echo "</table>\n";
?>
</div>
<div id="business_item_list">
	<h2>Business Items</h2>
	<ul id="item_list">
<?php
	$business_item_manager = new BusinessItemManager();
	$item_list = $business_item_manager->get_items_by_meeting($meeting->id);
	if($item_list){
		foreach($item_list as $item){
			echo '<li>';
			// TODO: Implement Edit feature
			echo '<b>'.$item->title.'</b> <a href="#">Edit</a><br>';
			if($item->details)
				echo $item->details.'<br>';
			$item->list_row();
			echo '</li>';
		}
	} else {
		echo '<p>No items for this meeting.</p>';
	}
	
?>
	</ul>
	<p class="center"><a href="itemForm.php?meeting_date=<?php echo $meeting_date; ?>&type=<?php echo $board; ?>">Add New Item</a></p>
</div>
<div class="clear_block"></div>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>