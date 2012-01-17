<?php
session_start();
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Task.php';
require_once 'classes/Report.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';


/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$position_id = $_POST[position_id];
	$report_id = $_POST[report_id];

	$valid_input = true;
	$errors = array();

	if($_POST[meeting_date] == ''){
		$errors[] = "Please provide the date of the meeting.<br>";
		$valid_input = false;
	} else {
		$meeting_date = date('Y-m-d', strtotime($_POST[meeting_date]));
	}
	$task_manager = new TaskManager();
	$task_list_old = $task_manager->get_previous_tasks($report_id, $position_id);
	foreach($task_list_old as $task){
		if($_POST['progress-'.$task->id] == 'select'){
			$errors[] = 'Please set the progress of all of your tasks from last week.';
			$valid_input = false;
			break;
		}
	}
	if($_POST[extra] == ''){
		$extra = NULL;
	} else {
		$extra = mysql_real_escape_string($_POST[extra]);
	}
	if($_POST[discussion] == ''){
		$discussion = NULL;
	} else {
		$discussion = mysql_real_escape_string($_POST[discussion]);
	}
	if($_POST[agenda] == ''){
		$agenda = NULL;
	} else {
		$agenda = mysql_real_escape_string($_POST[agenda]);
	}
	if(!isset($_POST[tasks])){
		//$errors[] = "Please commit to at least one task for next week.<br>";
		//$valid_input = false;
	}

	if($valid_input){
		if($report_id){ //Editing existing report
			$report = new Report($report_id);
			$report->extra = $extra;
			$report->discussion = $discussion;
			$report->agenda = $agenda;
			$report->save();
		} else { //Creating new report
			$report = new Report();
			$report->meeting_date = $meeting_date;
			$report->position_id = $position_id;
			$report->extra = $extra;
			$report->discussion = $discussion;
			$report->agenda = $agenda;
			$report->status = 'pending';
			$report->insert();
		}
		if($report->id){
			$previously_committed_tasks = $task_manager->get_tasks_by_report_id($report_id);
			if($previously_committed_tasks){
				foreach($previously_committed_tasks as $task){
					$task->status = 'new';
					$task->report_id = NULL;
					$task->save();
				}
			}
			foreach($task_list_old as $task){
				$progress = $_POST['progress-'.$task->id];
				$notes = $_POST['notes-'.$task->id];
				$task->progress = $progress;
				$task->notes = $notes;
				$task->save();
			}
			foreach($_POST[tasks] as $task_id){
				$task = new Task($task_id);
				$task->report_id = $report->id;
				$task->status = 'proposed';
				$task->save();
			}
			header("location: positionOverview.php?position=$position_id");
		} else {
			header("location: /error.php?page=newReport");
		}
	} else {
		$_GET[position] = $position_id;
		$_GET[id] = $report_id;
	}
} else {
	$position_id = $_GET[position];
	$report_id = $_GET[id];

	if(isset($report_id)){
		$report = new Report($report_id);
		$extra = $report->extra;
		$discussion = $report->discussion;
		$agenda = $report->agenda;
		$meeting_date = date('M j, Y', strtotime($report->meeting_date));
	} else {
		$report_id  = NULL; 
		$extra = NULL;
		$discussion = NULL; 
		$agenda = NULL;
		$report_manager = new ReportManager(); 
		$meeting_date = date('M j, Y', $report_manager->get_next_meeting_date($position_id)); 
	}
}

$task_manager = new TaskManager();

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="css/layout.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="/js/jkvkContBubbles.js"></script>
<script type="text/javascript" src="js/newReport_toolTips.js"></script>

<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker();
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">

<?php
	if(!$valid_input && $_SERVER['REQUEST_METHOD'] == "POST"){
		foreach($errors as $value){ ?>
			<div class="ui-widget">
				<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
					<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
					<?php echo $value; ?> </p>
				</div>
			</div>
<?php }
	}
?>

	<h1>New Report Form</h1>

	<p><a id="show-tips" href="#">How</a> do I fill out this form?</p>

	<div class="tips-form">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

		<table class="centered" align="center">
			<tr>
				<th>Date of meeting: </th>
				<td><input name="deadline" type="text" id="datepicker" size="10" value="<?php 
				echo date('m/d/Y', strtotime($meeting_date)); ?>" /></td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Tasks from <strong>last</strong> week:</th>
				<td style="text-align: center;">
<?php
	$task_list_old = $task_manager->get_previous_tasks($report_id, $position_id);
	if($task_list_old){
		echo '<table cellspacing="0" align="center">';
		foreach($task_list_old as $task){
			echo '<tr>';
			echo '<td class="left">'.$task->title.'</td>';
			echo '<td class="right"><select name="progress-'.$task->id.'">';
				echo '<option value="select">Select One</option>';
				foreach(Task::$TASK_PROGRESS as $key => $value){
					if($task->progress == $key)
						$selected = 'selected="selected"';
					else
						$selected = NULL;
					echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
			echo '</select>';
			echo '</tr>';
			echo '<tr>';
			echo '<td colspan="2"><textarea name="notes-'.$task->id.'" cols="48" rows="5">';
			echo $task->notes;
			echo '</textarea>';
			echo '</tr>';
			echo '<tr><td colspan="2">&nbsp;</td></tr>';
		}

		echo '</table>';
	} else {
		echo '<p>You have no tasks from last week. Odd...</p>';
	}
?>
				</td>
			</tr>
			<tr>
				<th>Extra work done: </th>
				<td><textarea name="extra" cols="48" rows="5"><?php echo $extra; ?></textarea></td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Tasks for <strong>next</strong> week:</th>
				<td style="text-align: center;">
<?php
	$task_list_new = $task_manager->get_tasks_by_position($position_id, 'new');

	if(isset($report_id)){
		$task_list_committed = $task_manager->get_tasks_by_report_id($report_id);
		$task_list_new = array_merge($task_list_committed, $task_list_new);
	}

	if($task_list_new){
		echo '<table cellspacing="0" align="center">';
		$first = True;
		foreach($task_list_new as $task){
			if($first){
				echo '<tr class="tableHeader">';
				echo '<td></td><td>Task</td><td>Priority</td><td>Deadline</td><td></td>';
				echo '</tr>';
				$first = False;
			}
			if(isset($report_id) && $task->report_id == $report_id){
				$checked = 'checked="yes"';
			} else {
				$checked = NULL;
			}
			echo '<tr class="'.$task->get_row_class().'">';
			echo '<td><input type="checkbox" name="tasks[]" value="'.$task->id.'" '.$checked.' /></td>';
			echo '<td class="left">'.$task->title.'</td>';
			echo '<td>'.ucwords($task->priority).'</td>';
			echo '<td>'.$task->get_deadline().'</td>';

			echo '<td>';
			echo '<a href="editTask.php?id='.$task->id.'">Edit</a>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>You have no tasks in your list. Please add some ASAP.</p>';
	}

?>
					<br/><a href="taskForm.php?position=<?php echo $position_id; ?>">Add New Task</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Items for discussion: </th>
				<td><textarea name="discussion" cols="48" rows="5"><?php echo $discussion; ?></textarea></td>
				</tr>
			<tr>
				<th>Items for agenda: </th>
				<td><textarea name="agenda" cols="48" rows="5"><?php echo $agenda; ?></textarea></td>
				</tr>
			<tr>
				<th></th>
				<td>	<input type="hidden" name="position_id" value="<?php echo $position_id; ?>" />
					<input type="hidden" name="report_id" value="<?php echo $report_id; ?>" />
					<input type="hidden" name="meeting_date" value="<?php echo $meeting_date; ?>" />
					<input type="submit" value="Submit" /></td>
				</tr>
			</table>


		</form>
	</div>

</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>