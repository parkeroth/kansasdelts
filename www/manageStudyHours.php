<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 

// This is going to be one mother of a page
// Sit down class and take notes
// Cause the show's about to start
// Let's begin
?>

<style type="text/css">
	table {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	th {
		font-size: 14px;
		text-align: center;
		padding: 5px;
		}
	td {
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

	//set up our user info
	$getSHUsersQ = '
		SELECT *
		FROM studyHourRequirements
		';
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
		echo '<table border="1">';
		echo '
			<tr>
				<th style="width: 250px;">
					Name:
				</th>
				<th style="width: 50px;">
					Required Hours:
				</th>
				<th style="width: 60px;">
					Check In:
				</th>
				<th style="width: 60px;">
					Check Out:
				</th>
			</tr>
			';
		while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
		{
			echo '
			<tr>
				<td style="width: 250px;">
					'.$SHuserDataArray[$lineCnt]['firstName'].' '.$SHuserDataArray[$lineCnt]['lastName'].' 
				</td>
				<td style="width: 50px;">
					'.$SHuserDataArray[$lineCnt]['hoursRequired'].'
				</td>';
			if($SHuserDataArray[$lineCnt]['status'] == "in")
			{
				//
				echo '
					<td>
						<input type="submit" name="'.$SHuserDataArray[$lineCnt]['username'].'_in" value="Log In" class="studyHrButton" />
					</td>
					<td>
						
					</td>
					';
			} else {
				//
				echo '
					<td>
						
					</td>
					<td>
						<input type="submit" name="'.$SHuserDataArray[$lineCnt]['username'].'_out" value="Log Out" class="studyHrButton" />
					</td>
					';
			}
			echo "</tr>\n";
			$lineCnt++;
		} //end while($SHuserDataArray = mysqli_fetch_array($getSHUsers, MYSQLI_ASSOC))
		echo '</table>
	</form>';
		
	} //end if(!$getSHUsers)
?>
    
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>