<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('authenticate.php');

require_once 'classes/Member.php';
require_once 'classes/Position.php';
require_once 'classes/Position_Log.php';

$member_manager  = new Member_Manager();
$position_manager = new Position_Manager();
$position_log_manager = new Position_Log_Manager();

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$year = $_POST[year];	//TODO: Should escape this	
	$term = $_POST[term];	//TODO: Should escape this
	
	$member_list = $member_manager->get_all_members();
	$position_list = $position_manager->get_all_positions($include_committees = false);
	
	foreach($member_list as $member){
		$new_position_id = $_POST[$member->id]; //TODO: escape this
		$current_entries =	$position_log_manager->
						get_logs_by_member($member->id, $term, $year);
		if(count($current_entries) == 1){
			$current_entry = $current_entries[0];
		} else {
			$current_entry = NULL;
		}
		
		if($new_position_id != 'none'){
			if($current_entry->position_id){
				// Swapping positions
				// Need to UPDATE log
				$current_entry->position_id = $new_position_id;
				$current_entry->save();
			} else {
				// Getting new position
				// Need to INSERT new log
				$log_entry = new Position_Log();
				$log_entry->member_id = $member->id;
				$log_entry->position_id = $new_position_id;
				$log_entry->term = $term;
				$log_entry->year = $year;
				$log_entry->insert();
			}
		} else if($current_entry) {
			// Will no longer have a position
			// Need to DELETE existing log
			$current_entry->delete();
		}
		$member->__destruct();
		
	}
	
	//header("location: ../account.php");
	
} else {
	if($_GET[year] && $_GET[term]){
		$year = $_GET[year]; //TODO: Add escape string
		$term = $_GET[term]; //TODO: Add escape string
	} else {
		$current_year = date('Y');
		$current_month = date('n');
		if($current_month > 8) {
			$year = $current_year + 1;
			$term = 'spring';
		} else {
			$year = $current_year;
			$term = 'fall';
		}
	}
}
	
/**
 * Form Section
 */


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="jscript" type="text/javascript">
	function Confirm()
	{
		return confirm ("Are you sure you want want to make these changes?");
	}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Election Update</h1>

<h4>Select position start date:</h4>
	<form id="electionUpdate" name="electionUpdate" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
		
		<p>
			
			Term:
			<select name="term" id="term">
<?php
		  if($term == 'fall'){
			echo '<option selected="selected" value="fall">Fall</option>\n';
		  } else {
			echo '<option value="fall">Fall</option>\n';
		  }
		  
		  if($term == 'spring'){
			echo '<option selected="selected" value="spring">Spring</option>\n';
		  } else {
			echo '<option value="spring">Spring</option>\n';
		  }
?>
			</select>
			
			Year: 
			<select name="year" id="year">
<?php
		  $current_year = date('Y');
		  for ($i = $current_year - 2; $i <= $current_year + 1; $i++) {
		  	if($i == $i){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"$i\" $selected>$i</option>\n";
		  }
?>
				</select>
			</p>
		
			<p>Be sure to <strong>set the correct semester</strong> above. Any changes will be made for <strong>that</strong> semester.</p>
		  
<?php		
		$member_list = $member_manager->get_all_members();
		$position_list = $position_manager->get_all_positions($include_committees = false);
		
		echo "<table>";
		foreach($member_list as $member){
			$position_logs = $position_log_manager->get_logs_by_member($member->id, $term, $year);
			$current_positions = array();
			foreach($position_logs as $entry){
				$current_positions[] = $entry->position_id;
			}
			
			echo '<tr>';
			echo '<th>'.$member->first_name.' '.$member->last_name;
			
			if($member->id == 38){
				echo count($current_positions);
			}
			
			echo '</th>';
			echo "<td><select name=\"".$member->id."\">";
			echo '<option value="none">None</option>';
			foreach($position_list as $position)
			{
				echo '<option value="'.$position->id.'" ';
				if(in_array($position->id, $current_positions))
				{
					echo " selected ";
				}
				echo " >";
				echo $position->title;
				echo "</option>";
			}
			echo "</select>";
			echo '</tr>';
			$member->__destruct();
		}
		echo "</table>";
		
		echo 'END';
?>
		<p>&nbsp;</p>
		<input type="hidden" name="term" value="<?php echo $term; ?>" />
		<input type="hidden" name="year" value="<?php echo $year; ?>" />
		<input type="submit" name="submit" id="submit" value="Submit" />
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>