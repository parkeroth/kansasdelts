<?php
//include_once($_SERVER['DOCUMENT_ROOT'].'/testScripts/showDebug.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics', 'proctor');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$studyDate = date( 'Y-m-d H:i:s', strtotime( $_POST['date'] ) );
	$proctor = $_SESSION[username];
	
	$modify = "	INSERT INTO studyHourLogs (username, timeStamp, proctorIn, proctorOut, open, duration)
				VALUES ('$_POST[username]', '$studyDate', '$proctor', '$proctor', 'no', '$_POST[hours]')";
	
	$doModify = mysqli_query($mysqli, $modify);
				
	$successMessage = $_POST[hours]." hour(s) added to logs.";
		
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 

// This is going to be one mother of a page
// Sit down class and take notes
// Cause the show's about to start
// Let's begin
?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script type="text/javascript">

	$(function() {
		$("#datepicker").datepicker();
	});
	
</script>

<style type="text/css">
	table.proctor {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	table.proctor th {
		font-size: 14px;
		text-align: center;
		padding: 5px;
		}
	table.proctor td {
		padding: 5px;
		}
	.dataError {
		border-style: solid;
		border-width: 2px;
		background-color: #990000;
		color: white;
		font-size: 16px;
		text-align: center;
		text-transform: uppercase;
		padding: 10px;
		}
	.studyHrButton {
		width: 50px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Manage User Study Hours</h1>

<?php
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	//set up our user info
	$getSHUsersQ = '
		SELECT members.username, members.firstName, members.lastName, studyHourRequirements.hoursRequired, studyHourRequirements.status
		FROM studyHourRequirements LEFT JOIN members
		ON studyHourRequirements.username = members.username
		WHERE "'.date('Y-m-d').'" BETWEEN studyHourRequirements.startDate AND studyHourRequirements.stopDate
		ORDER BY members.lastName 
		';
	//echo $getSHUsersQ;
	$getSHUsers = mysqli_query($mysqli, $getSHUsersQ);
	if(!$getSHUsers)
	{
		//
		$dataErrorMsg .= '<p class="dataError">Error: failed getting study hour users.  Select query failed. Database error.  Probably should look into that.  We love you anyway.<br />
			Table: studyHourRequirements<br />
			Error message thrown: '.mysqli_error().'</p>';
	} else {
		//now we can finally start with the form
		$lineCnt = 0;
		echo '<form id="chooseProctor" name="chooseProctor" method="post" action="logStudyHourSession.php" onSubmit="return Confirm();">';
		echo '<table class="proctor" border="1">';
		echo '
			<tr>
				<th style="width: 250px;">
					Name
				</th>
				<th style="width: 50px;">
					Required Hours
				</th>
				<th style="width: 60px;">
					Check Out
				</th>
				<th style="width: 60px;">
					Check In
				</th>
			</tr>
			';
		while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
		{
			echo '
			<tr>
				<td style="width: 250px;">
					'.$SHuserDataArray['firstName'].' '.$SHuserDataArray['lastName'].' 
				</td>
				<td style="width: 50px;">
					'.$SHuserDataArray['hoursRequired'].'
				</td>';
			if($SHuserDataArray['status'] == "in")
			{
				//Set up our variables for redirect
				$curUser = $SHuserDataArray['username'];
				$URL = 'logStudyHourSession.php?user='.$curUser.'&action=out';
				echo '
				<td>
					<input type="button" name="'.$SHuserDataArray['username']."\" value=\"Log Out\" class=\"studyHrButton\" onclick=\"javascript: window.location.href='$URL'\" />
				</td>
				<td>
					
				</td>
				";
				
			} else {
				//Set up our variables for redirect
				$curUser = $SHuserDataArray['username'];
				$URL = 'logStudyHourSession.php?user='.$curUser.'&action=in';
				echo '
				<td>
					
				</td>
				<td>
					<input type="button" name="'.$SHuserDataArray['username']."\" value=\"Log In\" class=\"studyHrButton\" onclick=\"javascript: window.location.href='$URL'\" />
				</td>
				";
			}
			
			
			echo "</tr>\n";
			$lineCnt++;
		} //end while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
		echo '</table>
	</form>';
		//echo $URL;
		
	} //end if(!$getSHUsers)
	
	
	// Give Director of Academic Affairs the ability to make an acception on hours
	
	if(strpos($session->accountType, 'academics') /*|| strpos($session->accountType, 'admin') */) {
		
	?>
		<h2>Make Adjustment</h2>
		
		<?php if(isset($successMessage)) { 
		
			echo "<p align=\"center\">$successMessage</p>";
		
		 } ?>
		
		<form name="manualAdjustment" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
		<table align="center">
			<tr>
				<th>Name:</th>
				<td><select name="username">
						<option name="select">Select One</option><?php
					
					$getSHUsers = mysqli_query($mysqli, $getSHUsersQ);
					while($userArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
					{
						echo "<option value=\"$userArray[username]\">$userArray[firstName] $userArray[lastName]</option>";
					}
						
				?></select></td>
			</tr>
			<tr>
				<th>Hour Adjustment:</th>
				<td>
					<input name="hours" type="text" size="3" />
				</td>
			</tr>
			<tr>
				<th>Date of studying:</th>
				<td>
					<input name="date" type="text" id="datepicker" size="11" />
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<input name="submit" type="submit" />
				</td>
		</table>	
		</form>
	
	<?php } ?>
    
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>