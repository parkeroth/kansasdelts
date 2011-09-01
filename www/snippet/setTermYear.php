<?php

//Sets $year and $term vars

	if(isset($_GET['term']) && isset($_GET['year']))
	{
		$year = $_GET['year'];
		$term = $_GET['term'];
	} else {
		$year = date(Y);
		$month = date(n);
		
		if($month > 0 && $month < 7){
			$term = "spring";
		} else {
			$term = "fall";
		}
	}

?>