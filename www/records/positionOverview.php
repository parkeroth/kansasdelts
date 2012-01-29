<?php
session_start();

$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
require_once 'classes/Report.php';
require_once 'classes/ReportingTask.php';
require_once 'classes/Task.php';
require_once 'classes/Meeting.php';

$position_id = $_GET[position];

if(!isset($position_id)){
    header("location: /error.php?page=newReport");
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />
<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		// Fill start time with current time string
		$("#start_button").click(function() {
			var now = new Date();
			$("#start_time").val(date_string(now));
		});
		
		$("a.delete").click(function(){
			var answer = confirm('Are you sure you want to remove this task?');
			return answer;
		})
	});
</script>

<?php	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
		
		$reporting_task_manager = new ReportingTaskManager();
		$task_manager = new TaskManager();
		$report_manager = new ReportManager();
		$position = new Position($position_id);
?>

<h1 style="text-align: center;"><?php echo $position->title; ?> Overview</h1>

<div id="overview_task">
	<h3>Current Tasks</h3>
	<?php
		$task_list_current = $task_manager->get_tasks_by_position($position->id, 'committed');
		
		if($task_list_current){
			echo '<table cellspacing="0" align="center">';
			$first = True;

			foreach($task_list_current as $task){
				if($first){
					echo '<tr class="tableHeader">';
					echo '<td>Task</td><td>Priority</td><td>Deadline</td><td></td>';
					echo '</tr>';
					$first = False;
				}
				echo '<tr class="'.$task->get_row_class().'">';
				echo '<td class="left">'.$task->title.'</td>';
				echo '<td>'.ucwords($task->priority).'</td>';
				echo '<td>'.$task->get_deadline().'</td>';

				echo '<td>';
				echo '<a href="taskForm.php?id='.$task->id.'">Edit</a>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
		} else {
			echo '<p>Yay! No outstanding tasks.<br> Should you add some?</p>';
		}
	?>
	<p>&nbsp;</p>
	<h3>Outstanding Tasks</h3>
	<?php
		$new_tasks = $task_manager->get_tasks_by_position($position->id, 'new');
		$proposed_tasks = $task_manager->get_tasks_by_position($position->id, 'proposed');
		$task_list_outstanding = array();
		$task_list_outstanding = array_merge($new_tasks, $task_list_outstanding);
		$task_list_outstanding = array_merge($proposed_tasks, $task_list_outstanding);

		if($task_list_outstanding){
			echo '<table cellspacing="0" align="center">';
			$first = True;

			foreach($task_list_outstanding as $task){
				if($first){
					echo '<tr class="tableHeader">';
					echo '<td>Task</td><td>Priority</td><td>Deadline</td><td></td>';
					echo '</tr>';
					$first = False;
				}
				echo '<tr class="'.$task->get_row_class().'">';
				echo '<td class="left">'.$task->title.'</td>';
				echo '<td>'.ucwords($task->priority).'</td>';
				echo '<td>'.$task->get_deadline().'</td>';

				echo '<td>';
				echo '<a href="taskForm.php?id='.$task->id.'">Edit</a>';
				echo '<strong> | </strong>';
				echo '<a class="delete" href="removeTask.php?id='.$task->id.'">X</a>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</table>';
		} else {
			echo '<p>Yay! No outstanding tasks.<br> Should you add some?</p>';
		}
	?>
	<br />
	<p><a href="taskForm.php?position=<?php echo $position_id; ?>">Create New Task</a></p>
</div>
<div id="overview_reports">
	<h3 style="text-align: center;">Reports</h3>
	<?php 
		$meeting_type = $position->board;
		$meeting_manager = new Meeting_Manager();
		$meeting_list = $meeting_manager->get_meetings_by_type($meeting_type, NULL, date('Y-m-d'));

		// Initialize variables to see if a report has been submitted for a meeting this Sunday
		$meeting_date_this_week = date('Y-m-d', strtotime('this Sunday'));
		$missing_this_week = false;
		
		if($meeting_list){
			echo '<table cellspacing="0" align="center">';
			$first = True;

			foreach($meeting_list as $meeting){
				// Only show meetings that this week or in the past
				if($meeting->date <= $meeting_date_this_week){
					$report_list = $report_manager->get_reports_by_meeting($meeting->id, $position->id);
					$report = $report_list[0];
					if($meeting->date == $meeting_date_this_week){
						$missing_this_week = true;
					}

					if($missing_this_week && ($meeting->date == $meeting_date_this_week))
						$missing_this_week = false;

					$status = $report->status;

					if($first){
						echo '<tr class="tableHeader">';
						echo '<td width="60">Date</td><td width="40">Status</td><td></td>';
						echo '</tr>';
						$first = False;
					}
					echo '<tr>';
					echo '<td>'.date('M j, Y', strtotime($meeting->date)).'</td>';
					echo '<td class="center">'.ucwords($status).'</td>';

					echo '<td>';
					if($report->can_edit()){
						echo '<a href="reportForm.php?position='.$position_id.'&report='.$report->id.'">Edit</a>';
					}
					echo '</td>';
					echo '</tr>';
				}
//			}

			echo '</table>';
		} else {
			echo '<p style="text-align:center;">No Reports</p>';
		}
		
		if($missing_this_week){ ?>
			<p>&nbsp;</p>
			<div class="ui-widget">
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
					<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
					You have not submitted a report for this coming Sunday! </p>
				</div>
			</div>
<?php	} ?>
	<br />
	<h4 style="text-align: center;">Submit New Report</h4>
	<p style="text-align: center;"> 
		
<?php
		$meeting_list = $meeting_manager->get_meetings_missing_report($position_id);
		$this_sunday = date('Y-m-d', strtotime('this Sunday'));
		$selected = '';
		foreach($meeting_list as $meeting){
			if($meeting->id == $meeting_id){			// If already stored as meeting_id
				$selected = 'selected="selected" ';
			} else if($meeting->date == $this_sunday){	// If it is the meeting next Sunday
				$selected = 'selected="selected" ';
			} else {
				$selected = '';
			}
			echo date('M jS, Y',strtotime($meeting->date)).' <a href="reportForm.php?position='.$position_id.'&meeting='.$meeting->id.'">Submit</a><br />';
		}
?>	</p>
</div>
<div class="clear_block">&nbsp;</div>
<?php
	//TODO: Finsish reporting task section
	foreach(ReportingTask::$DOCUMENTS as $document){
		$reporting_task_list = $reporting_task_manager->get_tasks_owned_by_position_document($position->type, $documen);

		if($reporting_task_list){
			echo '<h2>'.$document.' Tasks</h2>';
		}
	}
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>