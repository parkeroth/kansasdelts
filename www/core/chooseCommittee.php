<?php
session_start();
$authUsers = array('admin', 'academics');
include_once 'authenticate.php';

require_once 'classes/Member.php';
require_once 'classes/Position_Log.php';
require_once 'classes/Position.php';

$committee_data = array(	'proctor' => array(	'title' => 'Study Hour Proctors',
								'authUsers' => array('admin', 'academics')
							),
				'honor_board' => array(	'title' => 'Honor Board',
									'authUsers' => array('admin', 'saa')
							)
				);


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script language="jscript" type="text/javascript">
	function Confirm()
	{
	return confirm ("Are you sure you want want to make these changes?");
	}

	function RefreshPage(){
		var term = $("#term").val();
		var year = $("#year").val();
		var committee = $("#committee").val();
		var url = window.location.href;
		url = url.substring(0, url.indexOf('?'));
		url = url + '?committee=' + committee + '&term=' + term + '&year=' + year;
		window.location = url;
	}
	
	$(document).ready(function() {
		$("#term").change(RefreshPage);
		$("#year").change(RefreshPage);
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
	$position_log_manager = new Position_Log_Manager();
	$position_manager = new Position_Manager();
	$member_manager = new Member_Manager();
	
	$member_list = $member_manager->get_all_members();

	//Lets do our POST processing here
	if(isset($_POST['submit']))
	{
		$year = $_POST[year];
		$term = $_POST[term];
		$current_committee = $_POST[committee];
		$position = $position_manager->get_position_by_type($current_committee);
		foreach($member_list as $member)
		{	
			$was_in_committee = $position_log_manager->member_in_committee(	$member->id, 
																$position->id, 
																$term, $year);
			$is_in_committee = $_POST[$member->id];
			
			if( $is_in_committee && !$was_in_committee )
			{
				$log_entry = new Position_Log();
				$log_entry->member_id = $member->id;
				$log_entry->position_id = $position->id;
				$log_entry->term = $term;
				$log_entry->year = $year;
				$log_entry->insert();
			}
			else if(!$is_in_committee && $was_in_committee)
			{
				$entry = $position_log_manager->get_logs_by_member($member->id, $term, $year, $position->id);
				echo $entry;
			}
		}
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
		
		$current_committee = $_GET[committee];
		$current_data = $committee_data[$current_committee];
		$position = $position_manager->get_position_by_type($current_committee);
	}

?>

<h1>Choose <?php echo $current_data[title]; ?></h1>

	<form id="chooseProctor" name="chooseProctor" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
		
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
		  	if($i == $year){
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
		
		<p>
			Put a check next to any user you would like to assign to this committee.
		</p>
		<p>&nbsp;</p>
		<?php
				
		echo "<table align=\"center\">";
		foreach($member_list as $member)
		{
			echo "<tr><td><label>".$member->first_name." ".$member->last_name." </td>\n";
			echo "<td><input name=\"".$member->id."\" type=\"checkbox\" value=\"checked\" ";
			
			if( $position_log_manager->member_in_committee($member->id, $position->id, $term, $year) ){
				echo "checked=\"checked\"";
			}
			
			echo ">";
			echo "</td></tr>\n";
		}
		echo "</table>";
	?>
		<p>&nbsp;</p>
		<input id="committee" type="hidden" name="committee" value="<? echo $current_committee; ?>" />
		<input type="hidden" name="term" value="<? echo $term; ?>" />
		<input type="hidden" name="year" value="<? echo $year; ?>" />
		<div style="text-align:center;"><input type="submit" name="submit" id="submit" value="Submit" /></div>
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>