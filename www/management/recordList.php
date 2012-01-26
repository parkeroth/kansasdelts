<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/BusinessItem.php';
require_once 'classes/Meeting.php';
require_once 'classes/Minutes.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

	<?php		
		$meeting_manager = new MeetingManager();
		$meeting_list = $meeting_manager->get_chapters();
		
		$minutes_manager = new Minutes_Manager();
		$minutes_list = $minutes_manager->get_all_minutes();
	?>
<h1>Chapter Meeting Records</h1>
<div style="float: left; width: 60%">
	<h2>Agendas</h2>
	<table>
		<tr style="font-weight: bold;">
			<td width="140">Date of Meeting</td><td></td>
		</tr>
	<?php
		foreach($meeting_list as $meeting){
			$date_str = date('M j, Y', strtotime($meeting->meeting_date));
			echo '<tr>';
			echo "<td>$date_str</td>";
			echo '<td>';
			echo "<a href=\"agendaView.php?date=$meeting->meeting_date&type=chapter\">View</a>";
			echo " | <a href=\"agendaEdit.php?date=$meeting->meeting_date&type=chapter\">Edit</a>";
			if($minutes_manager->need_minutes($meeting->meeting_date, 'chapter')){
				echo " | <a href=\"minutesForm.php?date=$meeting->meeting_date&type=chapter\">Take Minutes</a>";
			}
			echo '</td>';
			echo '</tr>';
		}
	?>
	</table>
</div>
<div style="float: Right; width: 40%">
	<h2>Minutes</h2>
	<table>
		<tr style="font-weight: bold;">
			<td width="140">Date of Meeting</td><td>
		</tr>
	<?php
		foreach($minutes_list as $minutes){
			$date_str = date('M j, Y', strtotime($minutes->meeting_date));
			echo '<tr>';
			echo "<td>$date_str</td>";
			echo '<td>';
			echo "<a href=\"?date=$minutes->meeting_date&type=chapter\">View</a>";
			echo " | <a href=\"minutesForm.php?date=$minutes->meeting_date&type=chapter\">Edit</a>";
			echo '</td>';
			echo '</tr>';
		}
	?>
	</table>
</div>
<div class="clear_block">&nbsp;</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>