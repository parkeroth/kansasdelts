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

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");

		$positionData = "
			SELECT * 
			FROM positions
			ORDER BY ID";
		$getPositionData = mysqli_query($mysqli, $positionData);
		
		while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
		{
			
			if(strpos($session->accountType, $positionDataArray['type']))
			{
				$type = $positionDataArray['type'];
				$title = $positionDataArray['title'];
			}
		
		}
		
		$accountType = "
			SELECT accountType 
			FROM members
			WHERE username='".$_SESSION['username']."'";
		$getAccountType = mysqli_query($mysqli, $accountType);
		$accountTypeArray = mysqli_fetch_array($getAccountType, MYSQLI_ASSOC);
		
		if(strpos($accountTypeArray["accountType"], "admin") || strpos($accountTypeArray["accountType"], "secretary") )
		{
			$canChange = true;
			
		} else {
			
			$canChange = false;
		}
		
	?>
	
	<h1>Chapter Meeting Agendas</h1>
	<table>
		<tr style="font-weight: bold;">
			<td width="160">Date of Meeting</td><td width="40"></td><td width="40"></td>
			</tr>
		<?php
		
		$reportData = "
			SELECT DISTINCT dateMeeting
			FROM reports
			ORDER BY dateMeeting DESC";
		$getReportData = mysqli_query($mysqli, $reportData);
		
		while($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
		{
			echo "<tr>\n";
			echo "<td>".date("F j, Y", strtotime($reportArray[dateMeeting]))."</td>\n";
			echo "<td><input type=\"button\" value=\"View\" onclick=\"javascript:MM_openBrWindow('viewAgenda.php?date=$reportArray[dateMeeting]','','width=500,height=400, scrollbars=1');\"></td>\n";
			
			if($canChange)
			{
				echo "<td><input type=\"button\" value=\"Edit\" onclick=\"javascript:MM_openBrWindow('editAgenda.php?date=$reportArray[dateMeeting]','','width=500,height=400, scrollbars=1');\"></td>\n";
			}
			
			echo "</tr>\n";
		
		}
		
		?>
	</table>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>