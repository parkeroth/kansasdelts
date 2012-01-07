<?php
session_start();
include_once('../php/login.php');
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Position.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Report.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/BusinessItem.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Meeting.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$position_manager = new PositionManager($mysqli);
		$meeting_manager = new MeetingManager($mysqli);
		$report_manager = new ReportManager($mysqli);
		
		$meeting_date = $meeting_manager->get_latest_date();

		$exec_list = $position_manager->get_positions_by_board('exec');
	?>
<h1>Chapter Agenda - <?php echo date('F j, Y', strtotime($meeting_date)); ?></h1>
<div>	
	<?php
		echo "<table class=\"task_list\">\n";
		echo "<tr><td width=\"140\"></td><td></td><td></td></tr>";

		foreach($exec_list as $exec_position){
			$report_list = $report_manager->get_reports_by_date_position($meeting_date, $exec_position->id);
			$report = $report_list[0];
			
			echo "<tr>\n";
			echo "<th><a href=\"positionOverview.php?position=$exec_position->id\">$exec_position->title:</a> </th>\n";
			
			if($report->agenda)
			{
				echo '<td>';
				echo $report->agenda;
				echo "</td>";
			} else {
				echo "<td>Proud to be Delt.</td>";
			}
			echo "</tr>\n";
			
			if($exec_position->id == 2 || $exec_position->id == 4){
				if($exec_position->id == 2){
					$admin_board = 'internal';
				} else {
					$admin_board = 'external';
				}
				echo '<tr><td colspan="2">';
				echo '<div style="padding-left: 20px;">';
				echo '<table>';
				$admin_list = $position_manager->get_positions_by_board($admin_board);
				foreach($admin_list as $admin_position){
					$report_list = $report_manager->get_reports_by_date_position($meeting_date, $admin_position->id);
					$admin_report = $report_list[0];
					
					echo "<tr>\n";
					echo "<th width=\"160\"><a href=\"positionOverview.php?position=$admin_position->id\">$admin_position->title:</a> </th>\n";

					if($admin_report->agenda)
					{
						echo '<td>';
						echo $admin_report->agenda;
						echo "</td>";
					} else {
						echo "<td>Proud to be Delt.</td>";
					}
					echo "</tr>\n";
				}
				echo '</table>';
				echo '</div>';
				echo '</td></tr>';
			}
		}
		echo "</table>\n";
?>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>