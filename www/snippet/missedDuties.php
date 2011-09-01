<?php

function checkOccurance($mysqli, $type, $offender, $startDate, $stopDate) {
	
	$query = "
		SELECT COUNT(ID) AS num
		FROM infractionLog
		WHERE offender = '$offender'
		AND type = '$type'
		AND status = 'approved'
		AND dateOccured BETWEEN '$startDate' AND '$stopDate'";
	$result = mysqli_query($mysqli, $query);
	$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
	return $data[num];
}

function getPunishCode($mysqli, $type, $occurance) {
	
	$query = "
		SELECT ID AS code
		FROM punishments
		WHERE offenceNum = '$occurance'
		AND type = '$type'";
	$result = mysqli_query($mysqli, $query);
	$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
	return $data[code];
}

function assignHours($mysqli, $username, $hourType, $numHours, $infractionType) {
	
	$query = "
		SELECT name
		FROM infractionTypes
		WHERE code = '$infractionType'";
	$result = mysqli_query($mysqli, $query);
	$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$reason = $data[name];
	
	include_once('/snippet/setTermYear.php');
	
	$add_sql = "INSERT INTO hourLog (username, term, year, hours, type, eventID, dateAdded, notes) 
				VALUES ('$username', '$term', '$year', '-$numHours', '$hourType', '0', '".date("Y-m-d")."', '$reason')";
	
	echo $add_sql.'<br>';
	
	//$add_res = mysqli_query($mysqli, $add_sql);	
}

function assignFines($mysqli, $username, $fine, $infractionID){
	
	
	
}

function performPunishments($mysqli, $username, $punishCode){
	
	$query = "
		SELECT *
		FROM punishments
		WHERE ID = '$punishCode'";
	$result = mysqli_query($mysqli, $query);
	$data = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	if($data[hours] > 0) {
		echo "Adding Hours:<br>";
		assignHours($mysqli, $username, $data[hourType], $data[hours], $data[type]);
	}
	
	if($data[fine] > 0) {
		echo "Assigning Fines:<br>";
		
	}
} 

?>