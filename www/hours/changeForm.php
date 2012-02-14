<?php
session_start();
$authUsers = array('admin', 'communityService', 'houseManager', 'philanthropy', 'vpInternal', 'vpExternal', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Position.php';
include_once 'classes/Hour_Log.php';

if(isset($_GET[type])){
	$type = $_GET[type];
} else if(isset($_POST[type])){
	$type = $_POST[type];
} else {
	header('location: ../error.php');
}

$page_auth = array(	'house' => array('admin', 'houseManager', 'vpInternal', 'pres'),
				'service' => array('admin', 'communityService', 'vpInternal', 'pres'),
				'philanthropy' => array('admin', 'philanthropy', 'vpExternal', 'pres'));
$authorized = second_stage_auth($page_auth, $type, $session->member_id);

if($authorized == false){
	header('location: ../error.php?page=unauthorized');
}

$hour_manager = new Hour_Log_Manager();
$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members($sem);

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$descriptionError = false;

	$year = $_POST[year];
	$term = $_POST[term];
	
	$sem = new Semester();
	$sem->year = $year;
	$sem->term = $term;
	
	$_GET[type] = $type;
	
	foreach($member_list as $member)
	{
		$change = $_POST[$member->id];
		
		if( $change != NULL )
		{
			if($change != 0 && $_POST[$member->id."Description"] == ""){
				$descriptionError = true;
				$errors[] = "<b>".$member->first_name." ".$member->last_name."'s</b> hours were not changed. Need a description.<br>";
			} else {
				$description = $_POST[$member->id."Description"];
				$hour_log = new Hour_Log();
				$hour_log->member_id = $member->id;
				$hour_log->term = $term;
				$hour_log->year = $year;
				$hour_log->hours = $change;
				$hour_log->type = $type;
				$hour_log->notes = $description;
				$hour_log->insert();
			}
		}
	}
}
	


 
/**
 * Form Section
 */
	
	if(isset($sem)){
		// Do nothing
	} else if(isset($_GET['term']) || isset($_GET['year'])){
		$sem = new Semester();
		$sem->term = $_GET['term'];
		$sem->year = $_GET['year'];
	} else {
		$sem = new Semester();
	}
	
	$total_hours = $hour_manager->get_total($type, $sem);
	$hours_per_man = round($total_hours/count($member_list), 2);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />
<link type="text/css" href="../styles/popUp.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" src="../js/popup.js"></script>

<script>

//Create popups for Notification, Service, and House Hours
$(document).ready(function(){
	
	$("a.hour").click(function(){
		
		var id = $(this).attr('id');
		
		$.get('popup/details.php?type=<?php echo $type; ?>&member_id=' + id + '&year=<?php echo $sem->year; ?>&term=<?php echo $sem->term; ?>', function(data){
			$("#popupBody").html(data);
		});
		
		//$('#generalPopup').css('width', '420px');
		
		//centering with css
		centerPopup('#generalPopup');
		//load popup
		loadPopup('#generalPopup');
	});
		
	//CLOSING  POPUP
	//Click the x event!
	$('#popupClose').click(function(){
		disablePopup('#generalPopup');
	});
	
	//Click out event!
	$("#backgroundPopup").click(function(){
		disablePopup('#generalPopup');
	});
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<div style="text-align:center;">
	<?php echo '<h2>'.Hour_Log::$HOUR_TYPES[$type].' - '.ucwords($sem->term).' '.$sem->year.'</h2>'; ?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($sem->term == "fall"){
				echo "changeForm.php?year=$sem->year&amp;term=spring&amp;type=$type"; 
			} else {
				$lastYear = $sem->year-1;
				echo "changeForm.php?year=$lastYear&amp;term=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($sem->term == "fall"){
		  		echo "<option value=\"changeForm.php?year=$sem->year&amp;term=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"changeForm.php?year=$sem->year&amp;term=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"changeForm.php?year=$sem->year&amp;term=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"changeForm.php?year=$sem->year&amp;term=fall&amp;type=$type\" >Fall</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  
		  for ($i = $sem->year +1; $i >= $sem->year - 3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"changeForm.php?term=$sem->term&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($sem->term == "fall"){
				$nextYear = $sem->year+1;
				echo "changeForm.php?year=$nextYear&amp;term=spring&amp;type=$type"; 
			} else {
				echo "changeForm.php?year=$sem->year&amp;term=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>
		
	<p style="text-align:center;">
		Total hours per member: <b><?php echo $hours_per_man; ?></b>
	</p>
	
	<p style="text-align:center;">
		If you are assigning hours to multiple people, please create an event and add the events through the event details page.
	</p>
	
	<div class="errorBlock">
	<?php 
	
		if($descriptionError){
			foreach($errors as $value){
				echo $value;
			}
		}
	
	?>
	</div>
</div>
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Hours</strong></td><td width="100"px><strong>Adjustment</strong></td><td><strong>Description</strong></td></tr>
			<?php 
			
		foreach($member_list as $member){
			
			$hours = $hour_manager->get_total($type, $sem, $member->id);
			
			if($hours <= 0){
				$class="redHeading";
			} else {
				$class="normal";
			}
			
			echo "<tr>";
			echo "<td style=\"text-align: left;\">";
			echo 	"<label>".$member->first_name." ".$member->last_name." </td>\n";
			echo "<td class=\"$class\"><a class=\"hour\" id=\"$member->id\" href=\"#\">$hours</a></td>\n";
			echo "<td><input type=\"text\" name=\"".$member->id."\" size=\"2\"/></label></td>";
			echo "<td><input type=\"text\" name=\"".$member->id."Description\" size=\"24\"/></td>";
			echo "</tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input  type="hidden" name="term" value="<?php echo $sem->term; ?>" />
			<input  type="hidden" name="year" value="<?php echo $sem->year; ?>" />
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
	</form>
	<p>&nbsp;</p>
	<div id="generalPopup">
		<div id="popupClose"><a href="#">x</a></div>
		<div id="popupBody">Body</div>
	</div>
	
	<div id="backgroundPopup"></div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>