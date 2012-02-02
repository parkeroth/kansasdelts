<?php
session_start();
$authUsers = array('admin', 'secretary', 'pres');
include_once($_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php');

require_once 'classes/Chapter_Attendance.php';
require_once 'classes/Meeting.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
	
if($_SERVER['REQUEST_METHOD'] == "POST") {
	$meeting_id = $_POST[meeting_id];
	$member_id = $_POST[member_id];
	$status = $_POST[status];
	$attendance_manager = new Chapter_Attendance_Manager();
	$attendance_record = $attendance_manager->get_record_by_meeting_member($member_id, $meeting_id);
	
	if($status != 'select' && !$attendance_record->id){
		$attendance_record = new Chapter_Attendance();
		$attendance_record->meeting_id = $meeting_id;
		$attendance_record->member_id = $member_id;
		$attendance_record->status = $_POST[status];
		$attendance_record->insert();

		$meeting_date = $attendance_record->get_meeting_date();
	}
	
} else if($_GET[action] == 'remove'){
	$attendance_id = $_GET[id];
	$attendance_record = new Chapter_Attendance($attendance_id);
	$meeting_date = $attendance_record->get_meeting_date();
	$attendance_record->delete();
	
} else if($_GET[action] == 'toggle'){
	$attendance_id = $_GET[id];
	$attendance_record = new Chapter_Attendance($attendance_id);
	$meeting_date = $attendance_record->get_meeting_date();
	if($attendance_record->status == 'absent'){
		$attendance_record->status = 'excused';
	} else {
		$attendance_record->status = 'absent';
	}
	$attendance_record->save();
}

// Set get date variables based on $meeting_date
if($meeting_date){
	$month = date('n', strtotime($meeting_date));
	$year = date('Y', strtotime($meeting_date));
	$_GET[year] = $year;
	if($month < 8){
		$_GET[term] = 'spring';
	} else {
		$_GET[term] = 'fall';
	}
}


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>
	
<script language="JavaScript" src="js/gen_validatorv31.js" type="text/javascript"></script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<div style="text-align:center;">
	<?php
if(isset($_GET['term']) && isset($_GET['year']))
{
	$year = $_GET['year'];
	$term = $_GET['term'];
} else {
	$year = date(Y);
	$month = date(n);

	if($month < 8){
		$term = "spring";
	} else {
		$term = "fall";
	}
}
	
	
	
	echo "<h1>Chapter Absenses - ".ucwords($term)." ".$year."</h1>";
	?>
	
	<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($term == "fall"){
				echo "attendanceRecords.php?year=$year&amp;term=spring"; 
			} else {
				$lastYear = $year-1;
				echo "attendanceRecords.php?year=$lastYear&amp;term=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($term == "fall"){
		  		echo "<option value=\"attendanceRecords.php?year=$year&amp;term=spring\" >Spring</option>\n";
				echo "<option value=\"attendanceRecords.php?year=$year&amp;term=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"attendanceRecords.php?year=$year&amp;term=spring\" selected>Spring</option>\n";
				echo "<option value=\"attendanceRecords.php?year=$year&amp;term=fall\" >Fall</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  $yearLoop = $year;
		  
		  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"attendanceRecords.php?term=$term&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td>
				<div align="left"><a href="<? 
	  	
		if($term == "fall"){
			$nextYear = $year+1;
			echo "attendanceRecords.php?year=$nextYear&amp;term=spring"; 
		} else {
			echo "attendanceRecords.php?year=$year&amp;term=fall"; 
		}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
</table>
</div>
	<?php 
		if($term == 'spring'){
			$date = $year.'-01-01';
		} else {
			$date = $year.'-08-01';
		}
		$date = date('Y-m-d', strtotime($date));
		
		$member_manager = new Member_Manager();
		$all_members = $member_manager->get_all_members();
		
		$attendance_manager = new Chapter_Attendance_Manager();
		$meeting_manager = new Meeting_Manager();
		$meeting_list = $meeting_manager->get_meetings_by_type('chapter', $limit=NULL, $date);
		
		foreach($meeting_list as $meeting){
			echo "<h2>".date("F j, Y",strtotime($meeting->date))."</h2>";
			echo "<table align=\"center\">";
			
			$members_with_records = array();	// Keep track of who already has a record for the meeting
			
			$attendance_list = $attendance_manager->get_list_by_meeting($meeting->id);
			foreach($attendance_list as $attendance_record){
				$member = new Member($attendance_record->member_id);
				$members_with_records[] = $member->id;	// Add id to tracking array
							
				echo "<tr><th>";
				echo $member->first_name." ".$member->last_name.": </th>\n";
				echo "<td ";
				
				
				if($attendance_record->status == 'absent')
				{
					echo "class=\"redHeading\" ";	
				}
				
				
				echo ">";
				echo ucwords($attendance_record->status);
				echo "</td><td>";
				echo "<input 	type=\"button\" 
								name=\"remove-".$attendance_record->id."\" 
								value=\"Remove\"
								onclick=\"window.location.href='".$_SERVER['PHP_SELF']."?action=remove&amp;id=".$attendance_record->id."'\" />";
				echo "<input 	type=\"button\" 
								name=\"toggle-".$attendance_record->id."\" 
								value=\"Toggle\"
								onclick=\"window.location.href='".$_SERVER['PHP_SELF']."?action=toggle&amp;id=".$attendance_record->id."'\" />";
				echo "</td></tr>\n";
				$member->__destruct();
			}
			echo "</table>";
			
			echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">";
			
			echo "<table align=\"center\">";
			echo "<tr>";
			
			echo "<td>";
			echo "<select name=\"member_id\">";
			foreach($all_members as $member){
				if(!in_array($member->id, $members_with_records)){
					echo "<option value=\"".$member->id."\">".ucwords($member->first_name)." ".ucwords($member->last_name)."</option>";
				}
			}
			echo "</select>:";
			echo "</td><td>";
			?>
			
			<select name="status">
				<option value="select">Select One</option>
				<option value="excused">Excused</option>
				<option value="absent">Absent</option>
			</select>
			
			<?php
			echo "</td><td>";
			?>
			
			<input name="Submit" type="submit" />
			
			<input type="hidden" name="action" value="add" />
			<input type="hidden" name="meeting_id" value="<?php echo $meeting->id; ?>" />
			
			<?php
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "</form>";
		}
		
	?>
	<p>&nbsp;</p>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>