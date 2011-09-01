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
			var URL = 'manageReports.php?currentDate=' + year + '-' + month + '-' + day + '&previousDate=<?php echo $_GET[previousDate]; ?>';

			window.location.href=URL
		});

		$("#updateButtonPrevious").click(function() {
			var date = $("#datepickerPrevious").val();
			var month = date.substr(0,2);
			var day = date.substr(3,2);
			var year = date.substr(6,4);
			var URL = 'manageReports.php?previousDate=' + year + '-' + month + '-' + day + '&currentDate=<?php echo $_GET[currentDate]; ?>';

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
<style>
	.viewReport {
		padding-left: 20px;
		padding-right: 20px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Position Reports - <?php echo $date;?></h1>
	<form>
		<p>
			<input name="dateMeeting" type="text" id="datepickerCurrent" size="11" value="<?php echo $_GET[currentDate]; ?>" />
			<input id="updateButtonCurrent" type="button" value="Update" />
			Select the date of the current exec/admin meeting.
			</p>
	</form>

	<form>
		<p>
			<input name="dateMeeting" type="text" id="datepickerPrevious" size="11" value="<?php echo $_GET[previousDate]; ?>" />
			<input id="updateButtonPrevious" type="button" value="Update" />
			Select the date of the previous exec/admin meeting.
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
				SELECT *
				FROM reports
				WHERE dateMeeting = '$_GET[currentDate]'
				AND type = '$positionDataArray[type]'
				ORDER BY ID";
			$getReportData = mysqli_query($mysqli, $reportData);


			echo "<tr>\n";
			echo "<th>$positionDataArray[title]: </th>\n";

			if($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
			{
				echo "<td>";
				if(strtotime($reportArray[dateSubmitted]) > $lateTime) {
					//echo "<span class=\"redHeading\">LATE </span>";
				}

				if($reportArray[completed] == "yes"){
					echo "<span class=\"rankGreen\">Complete</span>";
				} else if($reportArray[completed] == "no"){
					echo "<span class=\"rankRed\">Incomplete</span>";
				} else if($reportArray[completed] == "na"){
					echo "<span class=\"rankYellow\">NA</span>";
				}

				echo "</td>";
				echo "<td><div class=\"viewReport\"><input type=\"button\" onclick=\"javascript:MM_openBrWindow('viewReport.php?ID=$reportArray[ID]&previousDate=".$_GET[previousDate]."','','width=500,height=400, scrollbars=1');\" value=\"View\"></div></td>\n";
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
				SELECT *
				FROM reports
				WHERE dateMeeting = '$_GET[currentDate]'
				AND type = '$positionDataArray[type]'
				ORDER BY ID";
			$getReportData = mysqli_query($mysqli, $reportData);

			echo "<tr>\n";
			echo "<th>$positionDataArray[title]: </th>\n";

			if($reportArray = mysqli_fetch_array($getReportData, MYSQLI_ASSOC))
			{
				echo "<td>";
				if(strtotime($reportArray[dateSubmitted]) > $lateTime) {
					//echo "<span class=\"redHeading\">LATE </span>";
				}

				if($reportArray[completed] == "yes"){
					echo "<span class=\"rankGreen\">Complete</span>";
				} else if($reportArray[completed] == "no"){
					echo "<span class=\"rankRed\">Incomplete</span>";
				} else if($reportArray[completed] == "na"){
					echo "<span class=\"rankYellow\">NA</span>";
				}

				echo "</td>";

				echo "<td><div class=\"viewReport\"><input type=\"button\" onclick=\"javascript:MM_openBrWindow('viewReport.php?ID=$reportArray[ID]&previousDate=".$_GET[previousDate]."','','width=500,height=400, scrollbars=1');\" value=\"View\"></div></td>\n";
			} else {
				echo "<td colspan=\"2\">&nbsp;</td>";
			}

			echo "</tr>\n";

		}

		echo "</table>\n";


		?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>