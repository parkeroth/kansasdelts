<?php
$authUsers = array('admin', 'saa', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once '../classes/Infraction_Log.php';
include_once '../classes/Punishment.php';


$super_list = array('admin', 'saa');
$haz_super_powers = $session->isAuth($super_list);

	
$type = $_GET[type];

if($type == 'eval'){
	$sem = new Semester();
	$infraction_id = $_GET[ID]; 
	
	$infraction = new Infraction_Log($infraction_id);
	$offender = new Member($infraction->offender_id);
	$reporter = new Member($infraction->reporter_id);
	 ?>

	<h2>Missed Duty Details</h2>

	<table align="center">
		<tr>
			<th width="120">Offender: </th>
			<td><?php echo $offender->first_name.' '.$offender->last_name; ?>
	
			</td>
		</tr><tr>
			<th>Reported By: </th>
			<td>
	<?php 
		if($infraction->reporter_id){
			echo $reporter->first_name.' '.$reporter->last_name;
		} else {
			echo '<i>Automatic</i>';
		}
	?>
			</td>
		</tr><tr>
			<th colspan="2">&nbsp;</th>
		</tr><tr>
			<th>Infraction: </th>
			<td><?php echo Infraction_Log::$INFRACTION_TYPES[$infraction->type]; ?></td>
		</tr><tr>
			<th>Date Occured: </th>
			<td><?php echo date('l M j, Y', strtotime($infraction->date_occured)); ?></td>
		</tr><tr>
			<th>Date Reported: </th>
			<td><?php echo date('l M j, Y', strtotime($infraction->date_reported)); ?></td>
		</tr><tr>
			<th colspan="2">&nbsp;</th>
		</tr><tr>
			<th>Notes: </th>
			<td><?php echo $infraction->description; ?></td>
		</tr><tr>
			<th colspan="2">&nbsp;</th>
		</tr><tr>
			<th>Punishment: </th>
			<td><?php 

			$offence_num = $infraction->get_occurance_num();

			$punishment_manager = new Punishment_Manager();
			$list = $punishment_manager->get_by_type($infraction->type, $offence_num);
			$punishment = $list[0];
			
			if($punishment->hours > 0)
			{
				if($punishment->hour_type == 'houseHours'){
					$hourType = 'House';
				} else if($punishment->hour_type == 'serviceHours'){
					$hourType = 'Service';
				}

				echo 'Hours: '.$punishment->hours." $hourType<br>";
			}

			if($punishment->fine > 0)
			{
				echo 'Fine: $'.$punishment->fine."<br>";
			}

			if($punishment->suspension != 'NULL' && $punishment->suspension != 'none')
			{
				echo 'Suspension: '.ucwords($punishment->suspension).'<br>';
			}

			if($punishment->expel)
			{
				echo 'Expulsion!<br>';
			}

			 ?></td>
		</tr><tr>
			<th colspan="2">&nbsp;</th>
		</tr>
		<?php if($haz_super_powers){ ?>
		<tr>
			<th>&nbsp;</th>
			<td><input id="<?php echo $id; ?>" class="auth" type="button" value="Authorize" />
				<input id="<?php echo $id; ?>" class="reject" type="button" value="Reject" /></td>
		</tr>
		<?php } ?>
		</tr>
	</table>

	<?php 
} 
?>