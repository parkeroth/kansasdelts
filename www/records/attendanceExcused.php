<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$date = $_POST['date'];
	
	$removeQuery = "DELETE FROM attendance WHERE date = '$date' AND status='excused'";
	$doRemove = mysqli_query($mysqli, $removeQuery);
	
	$userData = "
		SELECT username
		FROM members
		ORDER BY lastName";
	$getUserData = mysqli_query($mysqli, $userData);
	
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	{
		if($_POST[$userDataArray[username]]) {
			$modify = "INSERT INTO attendance
				(username, date, status)
				VALUES
				('$userDataArray[username]', '$date', 'excused')";
			$doModification = mysqli_query($mysqli, $modify);
			
			//echo $modify."<br>";
		}
	}
}
	


 
/**
 * Form Section
 */
 
if(!isset($_GET[currentDate])) { $_GET[currentDate] = date("Y-m-d"); }

$timeString = $_GET[currentDate];
$time = strtotime($timeString);
$date = date("M j, Y",$time);
$qDate = date("Y-m-d",$time);


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

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
			var URL = 'attendanceExcused.php?currentDate=' + year + '-' + month + '-' + day;
			
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
	
	<h1>Excuse Members From Chapter - <?php echo $date;?></h1>
	<form>
		<p>
			<input name="dateMeeting" type="text" id="datepickerCurrent" size="11" value="<?php echo $_GET[currentDate]; ?>" />
			<input id="updateButtonCurrent" type="button" value="Update" />
			Select the date of chapter meeting.
		</p>
	</form>
	
	<form id="gap" name="gpa" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Excused</strong></td></tr>
			<?php 
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
		
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC)){
			
			echo "<tr>";
			echo "<td style=\"text-align: left;\">";
			echo 	"<label>".$userDataArray['firstName']." ".$userDataArray['lastName']." </td>\n";
			
			
			$check = "	SELECT ID 
						FROM attendance
						WHERE username = '$userDataArray[username]'
						AND date = '$qDate'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$checked = "checked=\"checked\"";
			} else {
				$checked = "";
			}
			echo "<td><input type=\"checkbox\" name=\"".$userDataArray['username']."\" value=\"1\" $checked /></label></td>";
			echo "</tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input  type="hidden" name="date" value="<?php echo $qDate;?>" />
			<input type="submit" name="submit" id="submit" value="Submit" />
			<label>
				<input type="reset" name="Reset" id="Reset" value="Reset" />
				</label>
			</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>