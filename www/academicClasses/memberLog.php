<?php
        session_start();
        include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
        $authUsers = array('admin', 'academics');
        include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
        include_once 'classes/studyHours.php';
        include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
        include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
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
	.timeStamp {
		width: 200px;
	}
	.proctor {
		width: 125px;
	}
	.duration {
		width: 80px;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>
<h1>Individual Study Hour Logs</h1>

<?php
if(empty($_GET) || !isset($_GET['uid']))
{
	//No GET variables!  How are we supposed to know who da hell they're talking about?
	echo '<p class="dataError">No user specified!  Can\'t get logs!</p>';
} else {
	//username specifed, let's see if the user actually exists or if someone is fucking with us
	$userID = htmlspecialchars($_GET['uid']);
        $week_offset = isset($_GET['week_offset']) ? htmlspecialchars($_GET['week_offset']) : 0;    //if week_offset not set, set to 0

        $sh_log_manager = new Study_Hour_Log_Manager();
        $user_week_data = $sh_log_manager->get_week_data($userID, $week_offset);                //get desired week data
        $week_block_complete = $sh_log_manager->get_weekly_block_complete($userID, $week_offset);

        $sh_manager = new Study_Hour_Manager();
        $user_requirements = $sh_manager->get_user_sh_requirements($userID);

        //initialize a new member class for the study hour user in question
        $sh_member = new Member($userID);
?>
	<h2>Member: <?php echo $sh_member->first_name.' '.$sh_member->last_name; ?></h2>
        <table border="1">
        	<tr>
        	<th class="timeStamp">
                	Timestamp
                </th>
                <th class="proctor">
                	Proctor In
                </th>
                <th class="proctor">
                	Proctor Out
                </th>
                <th class="duration">
                	Duration (Hrs)
                </th>
		<th class="action">
                	Action
                </th>
        	</tr>
        </table>
<?php
	$weekStart = mktime(1, 0, 0, date('m'), date('d')-date('w'), date('Y'));        //start of current week
        $weekStart = $weekStart - ($week_offset * 7 * 24 * 60 * 60);                    //subtract week offset
	$weeklyHrs = 0.0;
	echo '<h2>Week of '.date('m-d-Y',$weekStart).'</h2>';
	echo '<table border="1">';

        foreach($user_week_data as $sh_log) {
                //some new member instances for proctor name info
                $proctor_in = new Member($sh_log->proctorIn);
                $proctor_out = new Member($sh_log->proctorOut);

                //update the weekly hours counter
                $weeklyHrs += $sh_log->duration;
            
                //and now echo table data
		echo '
		<tr>
			<td class="timeStamp">
				'.date("m-d-Y h:i:s A", strtotime($sh_log->timeIn)).'
			</td>
			<td class="proctor">
				'.$proctor_in->first_name.' '.$proctor_in->last_name.'
			</td>
			<td class="proctor">
				'.$proctor_out->first_name.' '.$proctor_out->last_name.'
			</td>
			<td class="duration">
				'.round($sh_log->duration,2).'
			</td>';

		echo '<td class="action">';
                echo "<input 	type=\"button\"
				name=\"remove-".$sh_log->id."\"
				value=\"Remove\"
				onclick=\"window.location.href='action.php?type=remove&amp;ID=".$sh_log->id."&amp;uid=".$sh_log->userID."'\" />";
                echo '</td>';

		echo '</tr>';
        }

        echo '</table>';



        if($week_block_complete < $user_requirements->week_required)
	{
		//didn't meet the requirement
		$hrStatement = '<h3 style="color: #E00000 !important;">User Short '.round(floatval($user_requirements->week_required-$week_block_complete),2).' Blocks</h3>';
	} else {
		//did meet the requirement
		$hrStatement = '<h3 style="color: #00CC33 !important;">User Over '.round(floatval($week_block_complete-$user_requirements->week_required),2).' Blocks</h3>';
	}
	//lets echo the hour information out to the user
	echo "
		<h3>Completed Hours for Week: ".round($weeklyHrs,2)."</h3>
                <h3>Completed Blocks for Week: ".$week_block_complete."</h3>
		<h3>Required per Week: ".$user_requirements->week_required."</h3>
		".$hrStatement."<br />";

        echo '<p style="text-align: center;"><a href="memberLog.php?uid='.$userID.'&amp;week_offset='.($week_offset+1).'">Previous Week</a></p>';
}   //end if(empty($_GET) || !isset($_GET['uid']))
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>