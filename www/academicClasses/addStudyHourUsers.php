<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/studyHours.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script type="text/javascript" src="/js/simpleCalendarWidget.js"></script>

<script language="jscript" type="text/javascript">
function Confirm()
{
return confirm ("Are you sure you want want to make these changes?");
}
</script>
<script language="jscript" type="text/javascript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>

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
	.submit {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	.dataError {
		border-style: solid;
		border-width: 2px;
		background-color: red;
		font-size: 16px;
		text-align: center;
		text-transform: uppercase;
		}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>


<h1>Add User to Study Hours</h1>

		<p>Put a check next to any member who has required study hours.<br />
        In the textbox, place the required number of hours per week.<br />
        Use the datepicker to pick the time period for the required study hours.</p>
		<p>&nbsp;</p>

		<form enctype="multipart/form-data" id="shUsers" name="shUsers" method="post" action="'.$_SERVER['PHP_SELF'].'" onSubmit="return Confirm();">
		<table border="1">
                    <tr>
			<th style="width: 100px;">
				Member Name
			</th>
			<th style="width: 35px;">
				Required?
			</th>
			<th style="width: 30px;">
				Study<br />Hours/Week
			</th>
			<th style="width: 120px;">
				Start Date
			</th>
			<th style="width: 120px;">
				Stop Date
			</th>
                    </tr>
                <?php
                    $memManager = new Member_Manager();
                    $memList = $memManager->get_all_members();

                    foreach($memList as $member) {
                        $userSHData = new Study_Hour_Requirements($member->id);
			echo "<tr>
					<td>
						<label>$member->first_name $member->last_name</label>
					</td>\n";
			if ($userSHData->hoursRequired != 0 || $userSHData->hoursRequired != NULL || $userSHData->hoursRequired != '')
			{
				//User DOES have required hours, so study hour stuff should be set up
				echo '
					<td>
						<input type="checkbox" name="'.$member->id.'" id="'.$member->id.'" value="Y" checked="checked" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_hrs" id="'.$member->id.'_hrs" value="'.$userSHData[$i]['hoursRequired'].'" size="5" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_start" id="datepicker" value="'.$userSHData->startDate.'" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_start,event);" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_stop" id="datepicker" value="'.$userSHData->stopDate.'" size="10" readonly="readonly" /> <input type="button" value="    " onclick=scwShow('.$member->id.'_stop,event);" />
					</td>
					';
			} else {
				echo '
					<td>
						<input type="checkbox" name="'.$member->id.'" id="'.$member->id.'" value="Y" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_hrs" id="'.$member->id.'_hrs" value=""size="5" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_start" id="datepicker" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_start,event);" />
					</td>
					<td>
						<input type="text" name="'.$member->id.'_stop" id="datepicker" size="10" readonly="readonly" /> <input type="button" value="    " onclick="scwShow('.$member->id.'_stop,event);" />
					</td>
					';
			}
			echo '</tr>';
                    }
                ?>
		</table>
		<p>&nbsp;</p>
		<div style="text-align: center;"><input type="submit" name="submit" id="submit" value="  Submit  " /></div>
	</form>

        <?php
       //Now process the shit that needs updating
	if(isset($_POST['submit']))
        {
            foreach($memList as $member) {
                if(isset($_POST[$member->id]))
                {
                    $userSHData = new Study_Hour_Requirements($member->id);
                }
            }
        }
        ?>

        <?php
	//Now process the shit that needs updating
	if(isset($_POST['submit']))
	{
		//gotta loop through and get our data
		//may as well use the $userSHData array since it's already got the usernames
		for($x=1; $x < $memberCount; $x++)
		{
			//the UPDATE statement we'll run will be based on whether or not
			//a user is checked
			$memberUName = $userSHData[$x]['username'];
			if(isset($_POST[$memberUName]))
			{
				//user has required hours
				//check to make sure other fields are okay
				if(!isset($_POST[$memberUName.'_hrs']) || !isset($_POST[$memberUName.'_start']) || !isset($_POST[$memberUName.'_stop']))
				{
					//required variables not set, echo message to user
					$dataErrorMsg = '<p class="dataError">Error: study hour data for '.$userSHData[$x]['firstName'].' '.$userSHData[$x]['lastName'].' is incompletely filled out</p>';
					echo $dataErrorMsg;
				} else {
					//things check out, data all filled
					//did user have study hours before? this will determine if we run
					//an update or an insert
					if ($userSHData[$x]['hoursRequired'] != 0 || $userSHData[$x]['hoursRequired'] != NULL || $userSHData[$x]['hoursRequired'] != '')
					{
						//user did have study hours, so an update is needed
						$studyHourQuery = '
							UPDATE studyHourRequirements
							SET startDate="'.$_POST[$memberUName.'_start'].'", stopDate="'.$_POST[$memberUName.'_stop'].'", hoursRequired="'.$_POST[$memberUName.'_hrs'].'"
							WHERE username="'.$memberUName.'"
							';
							echo 'Upating DB<br />Query: '.$studyHourQuery;
					} else {
						//user had no study hours, so an insertino is needed
						$studyHourQuery = "
							INSERT INTO studyHourRequirements
							(username, startDate, stopDate, hoursRequired, hoursCompleted, status)
							VALUES (\"".$memberUName."\", \"".$_POST[$memberUName.'_start']."\", \"".$_POST[$memberUName.'_stop']."\", ".$_POST[$memberUName.'_hrs'].", 0, \"out\")";
							echo 'Inserting into DB<br />Query: '.$studyHourQuery;

					}
					//have query. now execute that shit
					$writeToDB = mysqli_query($mysqli, $studyHourQuery);
					if(!$writeToDB)
					{
 						$dataErrorMsg = '<p class="dataError">Error: failed to write data for '.$userSHData[$x]['firstName'].' '.$userSHData[$x]['lastName'].'.  Database error.  Probably should look into that.  We love you anyway.<br />
						Error message thrown: '.mysqli_error().'</p>';
						echo $dataErrorMsg;
					}
				}
			} else {
				//user doesn't have required hours
				//so other fields don't matter. FUCK THEM.

				//member uName is NOT set, so they're NOT checked
				//we need to see if we need to delete the user from studyHourRequirements
				//or if the user already isn't in there, in which case we don't need to
				//do anything
				if ($userSHData[$x]['hoursRequired'] != 0 || $userSHData[$x]['hoursRequired'] != NULL || $userSHData[$x]['hoursRequired'] != '')
				{
					//user used to have required hours, so we need to delete them
					//from the studyHourRequirements table
					$deleteSHUser = '
						DELETE FROM studyHourRequirements
						WHERE username="'.$memberUName.'"
						';
					echo 'Removing from DB<br />Query: '.$deleteSHUser;
					//and execute the query
					$clrUserFromDB = mysqli_query($mysqli, $deleteSHUser);
					if(!$clrUserFromDB)
					{
 						$dataErrorMsg = '<p class="dataError">Error: failed to remove study hour user '.$userSHData[$x]['firstName'].' '.$userSHData[$x]['lastName'].'.  Database error.  Probably should look into that.  We love you anyway.<br />
						Error message thrown: '.mysqli_error().'</p>';
						echo $dataErrorMsg;
					} //end error message if
				} //end if there ARE hoursRequired
				//no need for an else statement, cause we don't need to do anything if the user isn't in there
			}
		}
		echo '<body onload="javascript:timedRefresh(500);">';
	} //End if(issset

	//boom goes the dynamite
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>