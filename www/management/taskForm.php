<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Task.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Member.php';

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$position_id = $_POST[position_id];
	$task_id = $_POST[task_id];
	$referrer_url = $_POST[referrer_url];

	$valid_input = true;
	$errors = array();

	if($_POST[title] == ''){
		$errors[] = "Please give the task a title.<br>";
		$valid_input = false;
	} else {
		$title = mysql_real_escape_string($_POST[title]);
	}

	if($_POST[priority] == 'select'){
		$errors[] = "Please select the priority.<br>";
		$valid_input = false;
	} else {
		$priority = mysql_real_escape_string($_POST[priority]);
	}

	if($_POST[deadline] == ''){
		$errors[] = "Please provide a deadline for the task.<br>";
		$valid_input = false;
	} else {
		$str = mysql_real_escape_string($_POST[deadline]);
		$deadline = date('Y-m-d', strtotime($str));
	}

	if($_POST[notes] == ''){
		$notes = NULL;
	} else {
		$notes = mysql_real_escape_string($_POST[notes]);
	}

	if($valid_input){
		if($task_id){ //Editing existing task
			$task = new Task($mysqli, $task_id);
			$task->saveVal('title', $title);
			$task->saveVal('deadline', $deadline);
			$task->saveVal('priority', $priority);
			$task->saveVal('notes', $notes);
		} else { //Creating new task
			$task = new Task($mysqli);
			$task->title = $title;
			$task->deadline = $deadline;
			$task->priority = $priority;
			$task->notes = $notes;
			$task->position_id = $position_id;
			$task->insert();
		}
		header("location: $referrer_url");
	} else {
		$_GET[position] = $position_id;
	}
} else {
	$position_id = $_GET[position];
	$task_id = $_GET[id];
	
	if(isset($task_id)){
		$task = new Task($mysqli, $task_id);
		$title = $task->title;
		$deadline = date('m/d/Y', strtotime($task->deadline));
		$priority = $task->priority;
		$notes = $task->notes;
		$position_id = $task->position_id;
	} else {
		$title = NULL;
		$deadline = date('m/d/Y');
		$priority = NULL;
		$notes = NULL;
	}
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="/styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="css/layout.css" rel="stylesheet" />

<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.1.custom.min.js"></script>

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

	<h1>New Task Form</h1>

	<p>Please provide the title, deadline, and priority of the tasks. If you would like to leave details about the task please fill in the notes section.</p>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

		<table class="centered" align="center">
			<tr>
				<th>Title: </th>
				<td><input name="title" type="text" size="32" value="<?php echo $title; ?>" /></td>
				</tr>
			<tr>
				<th>Deadline: </th>
				<td><input name="deadline" type="text" id="datepicker" size="10" value="<?php 
				echo date('m/d/Y', strtotime($deadline)); ?>" /> (optional)</td>
				</tr>
			<tr>
				<th>Priority: </th>
				<td><select name="priority">
						<option value="select">Select One</option>
<?php
	foreach(Task::$TASK_PRIORITY as $option){
		echo '<option value="'.$option.'"';
		if($option == $priority){
			echo 'selected="selected"';
		}
		echo '>'.ucwords($option);
		echo '</option>';
	}
?>
					</select></td>
				</td>
				</tr>
			<tr>
				<th>Notes: </th>
				<td><textarea name="notes" cols="40" rows="10"><?php echo $notes; ?></textarea></td>
				</tr>
			<tr>
				<th></th>
				<td><input type="hidden" name="position_id" value="<?php echo $position_id; ?>" />
					<input type="hidden" name="task_id" value="<?php echo $task_id; ?>" />
					<input type="hidden" name="referrer_url" value="<?php echo $_SERVER[HTTP_REFERER]; ?>" />
					<input type="submit" value="Submit" /></td>
				</tr>
			</table>


		</form>

</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>