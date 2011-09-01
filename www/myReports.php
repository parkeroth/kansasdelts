<?php
session_start();
include_once('php/login.php');
$authUsers = array('brother');
include_once('php/authenticate.php');

if(!isset($_GET[month])) { $_GET[month] = date("m"); }
if(!isset($_GET[day])) { $_GET[day] = date("d"); }
if(!isset($_GET[year])) { $_GET[year] = date("Y"); }

$timeString = $_GET[year]."-".$_GET[month]."-".$_GET[day];
$time = strtotime($timeString);
$date = date("M j, Y",$time);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		$("#updateButton").click(function() {
			var date = $("#datepicker").val();
			var month = date.substr(0,2);
			var day = date.substr(3,2);
			var year = date.substr(6,4);
			var URL = 'manageReports.php?month=' + month + '&day=' + day + '&year=' + year;
			
			window.location.href=URL
		});
	});
	
	$(function() {
		$("#datepicker").datepicker();
	});
	
	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  	window.open(theURL,winName,features);
	}
</script>
<style>
	.viewReport {
		padding-left: 20px;
		padding-right: 20px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
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
		
		
	?>
	
	<h1><?php echo $title;?> Reports</h1>
	<table width="400">
		<tr style="font-weight: bold;">
			<td width="120">Date of Meeting</td><td>Task Complete?</td>
			</tr>
		<?php
		
		$reportData = "
			SELECT * 
			FROM reports
			WHERE type = '$type'
			ORDER BY dateMeeting DESC";
		$getReportData = mysqli_query($mysqli, $reportData);
		
		while($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
		{
		
			echo "<tr>\n";
			echo "<td><a href=\"javascript:MM_openBrWindow('viewReport.php?ID=$reportArray[ID]','','width=500,height=400, scrollbars=1');\">$reportArray[dateMeeting]</a></td>\n";
			echo "<td>".ucwords($reportArray[completed])."</td>\n";
			echo "</tr>\n";
		
		}
		
		?>
	</table>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>