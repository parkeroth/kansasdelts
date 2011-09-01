<?php
$authUsers = array('admin', 'saa');
include_once('../php/authenticate.php');

include_once('../snippet/missedDuties.php');

include_once('../php/login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

//Sets $year and $term vars
	
	$type = $_GET[type];
	
	if($type == 'eval'){
		
		$month = date('n');
		$year = date('Y');
		
		if($month < 6){
			$startDate = "$year-01-01";
			$endDate = "$year-05-31";
		} else {
			$startDate = "$year-08-01";
			$endDate = "$year-12-31";
		}
	
		$id = $_GET[ID]; 
		
		$query = "
			SELECT 
				mo.firstName AS offenderFirst, 
				mo.lastName AS offenderLast, 
				l.offender,
				t.name, 
				mr.firstName as reporterFirst, 
				mr.lastName as reporterLast,
				l.dateReported,
				l.dateOccured,
				l.description,
				l.type
			FROM infractionLog as l
			JOIN members as mo
			ON l.offender = mo.username
			JOIN members as mr
			ON l.reporter = mr.username
			JOIN infractionTypes as t
			ON l.type = t.code
			WHERE l.ID = '$id'";
		$result = mysqli_query($mysqli, $query);
		$missData = mysqli_fetch_array($result, MYSQLI_ASSOC); ?>
		
		<h2>Missed Duty Details</h2>
		
		<table align="center">
			<tr>
				<th>Offender: </th>
				<td><?php echo $missData[offenderFirst].' '.$missData[offenderLast]; ?></td>
			</tr><tr>
				<th>Reported By: </th>
				<td><?php echo $missData[reporterFirst].' '.$missData[reporterLast]; ?></td>
			</tr><tr>
				<th colspan="2">&nbsp;</th>
			</tr><tr>
				<th>Infraction: </th>
				<td><?php echo $missData[name]; ?></td>
			</tr><tr>
				<th>Date Occured: </th>
				<td><?php echo date('l M j, Y', strtotime($missData[dateOccured])); ?></td>
			</tr><tr>
				<th>Date Reported: </th>
				<td><?php echo date('l M j, Y', strtotime($missData[dateReported])); ?></td>
			</tr><tr>
				<th colspan="2">&nbsp;</th>
			</tr><tr>
				<th>Notes: </th>
				<td><?php echo $missData[description]; ?></td>
			</tr><tr>
				<th colspan="2">&nbsp;</th>
			</tr><tr>
				<th>Punishment: </th>
				<td><?php 
				
				$numOccurance = 1 + checkOccurance($mysqli, $missData[type], $missData[offender], $startDate, $endDate);
				
				$maxQuery = "SELECT MAX(offenceNum) AS max FROM `punishments` WHERE type = '$missData[type]'";
				$result = mysqli_query($mysqli, $maxQuery);
				$maxData = mysqli_fetch_array($result, MYSQLI_ASSOC);
				
				$max = $maxData['max'];
				
				if($max == NULL)
				{
					echo "<span class=\"redHeading\">ERROR: No punishments set for $missData[name]</span>";
				} 
				else 
				{
					if($numOccurance > $max) {
						$numOccurance = $max;
					}
					
					$punishmentQuery = "
						SELECT *
						FROM punishments
						WHERE type = '$missData[type]'
						AND offenceNum = '$numOccurance'";
					$result = mysqli_query($mysqli, $punishmentQuery);
					$punishData = mysqli_fetch_array($result, MYSQLI_ASSOC);
					
					if($punishData[hours] > 0)
					{
						if($punishData[hourType] == 'houseHours'){
							$hourType = 'House';
						} else if($punishData[hourType] == 'serviceHours'){
							$hourType = 'Service';
						}
						
						echo 'Hours: '.$punishData[hours]." $hourType<br>";
					}
					
					if($punishData[fine] > 0)
					{
						echo 'Fine: $'.$punishData[fine]."<br>";
					}
					
					if($punishData[suspension] != 'NULL' && $punishData[suspension] != 'none')
					{
						echo 'Suspension: '.ucwords($punishData[suspension]);
					}
					
					if($punishData[expel])
					{
						echo 'Expulsion!';
					}
				}
				
				 ?></td>
			</tr><tr>
				<th colspan="2">&nbsp;</th>
			</tr><tr>
				<th>&nbsp;</th>
				<td><input id="<?php echo $id; ?>" class="auth" type="button" value="Authorize" />
					<input id="<?php echo $id; ?>" class="reject" type="button" value="Reject" /></td>
			</tr>
				
			</tr>
		</table>
		
		<?php 
	} 
?>