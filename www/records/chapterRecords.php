<?php
session_start();
$authUsers = array('brother');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
require_once 'classes/Meeting.php';

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

<h1 class="center">Chapter Meeting Records - <?php echo ucwords($term)." ".$year; ?></h1>

<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr> 
			<td><div align="right"><a href="<? 
	  		
			if($term == "fall"){
				echo "chapterRecords.php?year=$year&amp;term=spring"; 
			} else {
				$lastYear = $year-1;
				echo "chapterRecords.php?year=$lastYear&amp;term=fall"; 
			}
			
			?>">&lt;&lt;</a></div></td>
			<td width="200"><div align="center">
				
				<select name="season" id="month" onChange="MM_jumpMenu('parent',this,0)">
					<?
			if($term == "fall"){
		  		echo "<option value=\"chapterRecords.php?year=$year&amp;term=spring\" >Spring</option>\n";
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=fall\" selected>Fall</option>\n";
			} else {
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=spring\" selected>Spring</option>\n";
				echo "<option value=\"chapterRecords.php?year=$year&amp;term=fall\" >Fall</option>\n";
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
		  	echo "<option value=\"chapterRecords.php?term=$term&amp;year=$i\" $selected>$i</option>\n";
		  }
		  ?>
					</select>
				</div></td>
			<td>
				<div align="left"><a href="<? 
	  	
		if($term == "fall"){
			$nextYear = $year+1;
			echo "chapterRecords.php?year=$nextYear&amp;term=spring"; 
		} else {
			echo "chapterRecords.php?year=$year&amp;term=fall"; 
		}
		
		?>">&gt;&gt;</a></div></td>
			</tr>
</table>

<h3>Chapter Meetings</h3>

<table>
<?php 
	if($term == 'spring'){
		$date = $year.'-01-01';
	} else {
		$date = $year.'-08-01';
	}
	$date = date('Y-m-d', strtotime($date));
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
	
	if(count($chapter_list) == 0){
		echo '<p>No records for this semester.</p>';
	}
?>
</table>
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>