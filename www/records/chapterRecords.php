<?php
session_start();
$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once 'classes/Meeting.php';


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<link type="text/css" href="css/layout.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("input.agenda").click(function(event){
			window.location.href = 'chapterAgenda.php?id=' + event.target.id;
		})
	});
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h1 class="center">Chapter Meeting Records</h1>

<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($season == "fall"){
				echo "chapterRecords.php?year=$year&amp;term=spring&amp;type=$type"; 
			} else {
				$lastYear = $year-1;
				echo "chapterRecords.php?year=$lastYear&amp;term=fall&amp;type=$type"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($season == "fall"){
		  		echo "<option value=\"chapterRecords.php?year=$year&amp;term=spring&amp;type=$type\" >Spring</option>\n";
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=fall&amp;type=$type\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=spring&amp;type=$type\" selected>Spring</option>\n";
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=fall&amp;type=$type\" >Fall</option>\n";
			}
			?>
					</select>
				<select name="year" id="year" onChange="MM_jumpMenu('parent',this,0)">
					<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop+1; $i >= $yearLoop-3; $i--) {
		  	if($i == $year){
				$selected = "selected";
			} else {
				$selected = "";
			}
		  	echo "<option value=\"chapterRecords.php?term=$season&amp;year=$i&amp;type=$type\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td><div align="left"><a href="<? 
	  	
		if($season == "fall"){
				$nextYear = $year+1;
				echo "changeHoursForm.php?year=$nextYear&amp;season=spring&amp;type=$type"; 
			} else {
				echo "changeHoursForm.php?year=$year&amp;season=fall&amp;type=$type"; 
			}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
		</table>

<h3>Chapter Meetings</h3>

<table>
<?php 
	$date = date('Y-m-d');
	$meeting_manager = new Meeting_Manager();
	$chapter_list = $meeting_manager->get_meetings_by_type('chapter', $limit = NULL, $date);
	foreach($chapter_list as $meeting){
		$date_str = date('M j, Y', strtotime($meeting->date));
		echo '<tr>';
		echo '<th width="120">'.$date_str.'</th>';
		echo '<td><input type="button" id="'.$meeting->id.'" class="agenda" value="View Agenda" /></td>';
		echo '<td><input type="button" id="'.$meeting->id.'" class="minutes" value="View Minutes" /></td>';
		echo '</tr>';
	}
?>
</table>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>