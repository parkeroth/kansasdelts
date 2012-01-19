<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once 'classes/Report.php';
require_once 'classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';


/**
 * Processing Section
*/

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$report_id = $_POST[report_id];
	$valid_input = true;
	$errors = array();
	
	if($_POST[status] == 'select'){
		$errors[] = "Please select the status of this report.";
		$valid_input = false;
	} else {
		$status = $_POST[status];
	}
	if($_POST[agenda] == ''){
		$agenda = NULL;
	} else {
		$agenda = $_POST[agenda];
	}
	if(!isset($_POST[tasks])){
		$errors[] = "Please commit to at least one task for next week.<br>";
		$valid_input = false;
	}
	$task_manager = new TaskManager();
	$previous_tasks = $task_manager->get_previous_tasks($report_id);
	foreach($previous_tasks as $task){
		$progress = $_POST['progress-'.$task->id];
		if($progress == 'select'){
			$errors[] = 'Please set the progress of all of your tasks from last week.';
			$valid_input = false;
			break;
		}
		if( ($progress != 'completed' && $progress != 'cancelled') && !in_array($task->id, $_POST[tasks])){
			$errors[] = "Task: $task->title has not been marked complete/cancelled and has not been assigned to next week!";
			$valid_input = false;
		}
	}
	if($valid_input){
		$report = new Report($report_id);
		$report->agenda = $agenda;
		$report->status = $status;
		$report->save();
		
		foreach($previous_tasks as $task){
			$progress = $_POST['progress-'.$task->id];
			$notes = $_POST['notes-'.$task->id];
			$task->progress = $progress;
			$task->notes = $notes;
			if($progress == 'completed' || $progress == 'cancelled'){
				$task->status = 'closed';
			} else {
				$task->status = 'committed';
			}
			$task->save();
		}
		$report->assign_tasks('committed', $_POST[tasks]);
		$meeting_date = $report->meeting_date;
		$position = new Position($report->position_id);
		$board = $position->board;
		header("location: manageMeeting.php?board=$board&meeting_date=$meeting_date");
	} else {
		$_GET[id] = $report_id;
	}
} 

$report_id = $_GET[id];
$report = new Report($report_id);
$extra = $report->extra;
$status = $report->status;
$discussion = $report->discussion;
if($discussion == ''){
	$discussion = 'None';
}
if($extra == ''){
	$extra = 'None';
}
$agenda = $report->agenda;
$meeting_date = date('m/d/Y', strtotime($report->meeting_date));

$position = new Position($report->position_id);


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

	<h1><?php echo $position->title; ?> Report Details</h1>

	<p><a id="show-tips" href="#">How</a> do I fill out this form?</p>

	<div class="tips-form">
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

		<table class="centered" align="center">
			<tr>
				<th>Pass/Fail:</th>
				<td class="center">
					<select name="status">
						<option value="select" <?php if($status == 'select') echo 'selected="selected"'; ?>>Select One</option>;
						<option value="complete" <?php if($status == 'complete') echo 'selected="selected"'; ?>>Complete</option>;
						<option value="incomplete" <?php if($status == 'incomplete') echo 'selected="selected"'; ?>>Incomplete</option>;
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Tasks from <strong>last</strong> week:</th>
				<td style="text-align: center;">
<?php
	$previous_tasks = $task_manager->get_previous_tasks($report_id);
	if($previous_tasks){
		echo '<table cellspacing="0" align="center">';
		foreach($previous_tasks as $task){
			echo '<tr>';
			echo '<td class="left">'.$task->title.'</td>';
			echo '<td class="right"><select name="progress-'.$task->id.'">';
				echo '<option value="select">Select One</option>';
				foreach(TASK::$TASK_PROGRESS as $key => $value){
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
				<td><?php echo $extra; ?></td>
				</tr>
			<tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Tasks for <strong>next</strong> week:</th>
				<td style="text-align: center;">
<?php
	$task_list_new = $task_manager->get_tasks_by_position($position->id, 'new');
	$task_list_proposed = $task_manager->get_tasks_by_position($position->id, 'proposed');
	$task_list_committed = $task_manager->get_tasks_by_position($position->id, 'committed');
	$task_list = array();
	$task_list = array_merge($task_list_new, $task_list);
	$task_list = array_merge($task_list_proposed, $task_list);

	if($task_list){
		echo '<table cellspacing="0" align="center">';
		$first = True;
		foreach($task_list as $task){
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
			echo '<td>';
			if(!in_array($task, $task_list_committed)){
				echo '<input type="checkbox" name="tasks[]" value="'.$task->id.'" '.$checked.' />';
			}
			echo '</td>';
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
		echo '<p>You have no tasks in your list. Please add some ASAP.</p>';
	}

?>
					<br/><a href="taskForm.php?position=<?php echo $position->id; ?>">Add New Task</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Items for discussion: </th>
				<td><?php echo $discussion; ?></td>
				</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th>Items for agenda: </th>
				<td><textarea name="agenda" cols="48" rows="5"><?php echo $agenda; ?></textarea></td>
				</tr>
			<tr>
				<th></th>
				<td>  <input type="hidden" name="report_id" value="<?php echo $report_id; ?>" />
					<input type="submit" value="Submit" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>