<h2>BADD Duty</h2>
	<table cellspacing="5">
	<?php
		$dateArray = array();
		$today = date("Y-m-d");
		$baddDateQuery = "
			SELECT DISTINCT date 
			FROM `baddDutyDays`
			WHERE date >= '$today'
			ORDER BY date ASC 
			LIMIT 2";
		$getBaddDates = mysqli_query($mysqli, $baddDateQuery);
		
		// Push the two upcoming dates onto a stack
		while($baddDateArray = mysqli_fetch_array($getBaddDates, MYSQLI_ASSOC))
		{
			array_push($dateArray, $baddDateArray['date']);
		}
		
		// Get the users for each date
		foreach($dateArray as $date)
		{
			$baddDate = date("l M j",strtotime($date));
			echo "<tr><td valign=\"top\"><b>".$baddDate."</b></td><td>";
			
			$baddDutyUserQuery = "
				SELECT username
				FROM baddDutyLog
				WHERE date='$date'";
			$getBaddUsers = mysqli_query($mysqli, $baddDutyUserQuery);
			while($baddUsersArray = mysqli_fetch_array($getBaddUsers, MYSQLI_ASSOC))
			{
				for($i=0; $i < count($members); $i++)
				{
					if($members[$i]['username'] == $baddUsersArray['username'])
					{
						echo $members[$i][firstName]." ".$members[$i][lastName]."<br>";
					}
				}
			}
		}
		
		echo "</td></tr>";
		
	?>
	</table>