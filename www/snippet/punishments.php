<?php

// Get number of previous infractions
function getNumPrevious($mysqli, $term, $year, $type, $user) {
	
	$infractionQuery = "
		SELECT * 
		FROM infractionTypes
		ORDER BY ID";
	$getInfraction = mysqli_query($mysqli, $infractionQuery);
	
	while($infractionArray = mysqli_fetch_array($getInfraction, MYSQLI_ASSOC))
	{
		echo "<option value=\"$infractionArray[code]\">$infractionArray[name]";
	}
	
}

?>