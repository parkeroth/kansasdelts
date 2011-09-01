<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

if(!isset($_GET[currentDate])) { $_GET[currentDate] = date("Y-m-d"); }
if(!isset($_GET[previousDate])) { $_GET[previousDate] = date("Y-m-d", strtotime("-1 week")); }

$timeString = $_GET[currentDate];
$time = strtotime($timeString);
$date = date("M j, Y",$time);

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<style>
	.viewReport {
		padding-left: 20px;
		padding-right: 20px;
	}
	td.taskCell {
		padding: 10px;
	}
</style>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		$("#updateButtonCurrent").click(function() {
			var date = $("#datepickerCurrent").val();
			var month = date.substr(0,2);
			var day = date.substr(3,2);
			var year = date.substr(6,4);
			var URL = 'boardMinutes.php?currentDate=' + year + '-' + month + '-' + day;
			
			window.location.href=URL
		});
		
	});
	
	$(function() {
		$("#datepickerCurrent").datepicker();
		$("#datepickerPrevious").datepicker();
	});
	
	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  	window.open(theURL,winName,features);
	}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Board Minutes - <?php echo $date;?></h1>
	<form>
		<p>
			<input name="dateMeeting" type="text" id="datepickerCurrent" size="11" value="<?php echo $_GET[currentDate]; ?>" />
			<input id="updateButtonCurrent" type="button" value="Update" />
			Select the date of the current exec/admin meeting.
			</p>
	</form>
	
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$lateDate = $_GET['currentDate'];
		$lateTime = strtotime($lateDate." 17:00:00");
		
		echo "<h3>Exec Board</h3>\n";
		
		echo "<table>\n";
		
		echo "<tr><td width=\"240\"></td><td></td><td></td></tr>";
		
		$positionData = "
			SELECT * 
			FROM positions
			WHERE board='exec'
			ORDER BY ID";
		$getPositionData = mysqli_query($mysqli, $positionData);
		
		while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
		{
			
			$reportData = "
				SELECT minutes 
				FROM reports
				WHERE dateMeeting = '$_GET[currentDate]'
				AND type = '$positionDataArray[type]'
				ORDER BY ID";
			$getReportData = mysqli_query($mysqli, $reportData);
			
			
			echo "<tr>\n";
			echo "<th>$positionDataArray[title]: </th>\n";
			
			if($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
			{
				echo "<td class=\"taskCell\">";
								
				echo $reportArray[minutes];
				
				echo "</td>";
			} else {
				echo "<td colspan=\"3\">&nbsp;</td>";
			}
			
			
			echo "</tr>\n";
		
		}
		
		echo "</table>\n";
		
		
		
		echo "<h3>Admin Board</h3>\n";
		
		echo "<table>\n";
		
		echo "<tr><td width=\"240\"></td><td></td><td></td></tr>";
		
		$positionData = "
			SELECT * 
			FROM positions
			WHERE board='admin'
			ORDER BY ID";
		$getPositionData = mysqli_query($mysqli, $positionData);
		
		while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
		{
			
			$reportData = "
				SELECT minutes 
				FROM reports
				WHERE dateMeeting = '$_GET[currentDate]'
				AND type = '$positionDataArray[type]'
				ORDER BY ID";
			$getReportData = mysqli_query($mysqli, $reportData);
			
			echo "<tr>\n";
			echo "<th>$positionDataArray[title]: </th>\n";

			if($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
			{
				echo "<td class=\"taskCell\">";
								
				echo $reportArray[minutes];
				
				echo "</td>";
			} else {
				echo "<td colspan=\"3\">&nbsp;</td>";
			}
					
			echo "</tr>\n";
		
		}
		
		echo "</table>\n";
		
		
		?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>