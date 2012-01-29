<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once 'classes/BusinessItem.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

/**
 * Processing Section
 */
if($_SERVER['REQUEST_METHOD'] == "POST") {
	$referrer_url = $_POST[referrer_url];
	$submitted_by = $session->member_id;
	
	$valid_input = true;
	$errors = array();

	if($_POST[title] == ''){
		$errors[] = "Please give the item a title.<br>";
		$valid_input = false;
	} else {
		$title = mysql_real_escape_string($_POST[title]);
	}
	
	if($_POST[item_type] == 'select'){
		$errors[] = "Please select type of the item.<br>";
		$valid_input = false;
	} else {
		$item_type = mysql_real_escape_string($_POST[item_type]);
	}
	
	if($_POST[meeting_type] == 'select'){
		$errors[] = "Please select type of the meeting.<br>";
		$valid_input = false;
	} else {
		$meeting_type = mysql_real_escape_string($_POST[meeting_type]);
	}
	
	function is_valid_date($date){
		$day_of_week = date('N', strtotime($date));
		return $day_of_week == 7;
	}
	
	$date = $_POST[meeting_date];
	if($date == ''){
		$errors[] = "Please provide the date of the meeting.<br>";
		$valid_input = false;
	} if(!is_valid_date($date)){
		$errors[] = "Please select a Sunday for the meeting date.<br>";
		$valid_input = false;
	}else {
		$str = mysql_real_escape_string($date);
		$meeting_date = date('Y-m-d', strtotime($str));
	}

	if($_POST[details] == ''){
		$details = NULL;
	} else {
		$details = mysql_real_escape_string($_POST[details]);
	}

	if($valid_input){
		$business_item = new BusinessItem();
		$business_item->title = $title;
		$business_item->item_type = $item_type;
		$business_item->meeting_type = $meeting_type;
		$business_item->meeting_date = $meeting_date;
		$business_item->details = $details;
		$business_item->submitted_by = $submitted_by;
		$business_item->insert();
		
		//header("location: $referrer_url");
	} else {
		
	}
} else {
	if(isset($_GET[meeting_date])){
		$meeting_date = date('Y-m-d', strtotime($_GET[meeting_date]));
	} else {
		$meeting_date = date('Y-m-d', strtotime('this Sunday'));
	}
	
	if(isset($_GET[type])){
		$meeting_type = $_GET[type];
	} else {
		$meeting_type = NULL;
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
	} ?>

	<h1>New Business Item</h1>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

		<table class="centered" align="center">
			<tr>
				<th>Title: </th>
				<td><input name="title" type="text" size="32" value="<?php echo $title; ?>" /></td>
				</tr>
			<tr>
				<th>Details: </th>
				<td><textarea name="details" cols="40" rows="5"><?php echo $details; ?></textarea></td>
				</tr>
			<tr>
				<th>Item Type: </th>
				<td><select name="item_type">
						<option value="select">Select One</option>
<?php
	foreach(BusinessItem::$ITEM_TYPES as $type){
		echo '<option value="'.$type.'"';
		if($type == $meeting_type){
			echo 'selected="selected"';
		}
		echo '>'.ucwords($type);
		echo '</option>';
	}
?>
					</select></td>
				</td>
				</tr>
			<tr>
				<th>Meeting Date: </th>
				<td><input name="meeting_date" type="text" id="datepicker" size="10" value="<?php 
				echo date('m/d/Y', strtotime($meeting_date)); ?>" /></td>
				</tr>
			<tr>
				<th>Meeting Type: </th>
				<td><select name="meeting_type">
						<option value="select">Select One</option>
<?php
	foreach(BusinessItem::$MEETING_TYPES as $type){
		echo '<option value="'.$type.'"';
		if($type == $meeting_type){
			echo 'selected="selected"';
		}
		echo '>'.ucwords($type);
		echo '</option>';
	}
?>
					</select></td>
				</td>
				</tr>
			<tr>
				<th></th>
				<td>
					<input type="hidden" name="referrer_url" value="<?php echo $_SERVER[HTTP_REFERER]; ?>" />
					<input type="submit" value="Submit" /></td>
				</tr>
			</table>


		</form>

</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>