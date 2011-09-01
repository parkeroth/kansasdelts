<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker();
	});
	
	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  	window.open(theURL,winName,features);
	}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Chapter Meeting Minutes</h1>
	<table>
		<tr style="font-weight: bold;">
			<td width="160">Date of Meeting</td><td width="40"></td>
			</tr>
		<?php
		
		$reportData = "
			SELECT DISTINCT meetingDate
			FROM chapterMinutes
			ORDER BY meetingDate DESC";
		$getReportData = mysqli_query($mysqli, $reportData);
		
		while($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
		{
			echo "<tr>\n";
			echo "<td>".date("F j, Y", strtotime($reportArray[meetingDate]))."</td>\n";
			echo "<td><input type=\"button\" value=\"View\" onclick=\"javascript:MM_openBrWindow('viewChapterMinutes.php?date=$reportArray[meetingDate]','','width=500,height=400, scrollbars=1');\"></td>\n";
			echo "</tr>\n";
		
		}
		
		?>
	</table>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>