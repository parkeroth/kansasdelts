<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'drm');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1 align="center">Sober Gent Log</h1>
	
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if(date("n") > 5)
		{
			$schoolYear = date("Y")+1;
			echo "<p align=\"center\">Totals for Fall ". date("Y") ." and Spring $schoolYear</p>";
		}
		else
		{
			$schoolYear = date("Y");
			echo "<p align=\"center\">Totals for Fall $schoolYear and Spring ";
			echo $schoolYear + 1;
			echo "</p>";
		}
		
		?>
	<table align="center" width="30%">
		<?php
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
			
		$memberCount = 0;
		$first=true;
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$memberCount++;
			
			$query = "
				SELECT * 
				FROM soberGentLog
				WHERE username = '".$userDataArray[username]."'
				AND year = '$schoolYear'";
				
			$result = mysqli_query($mysqli, $query);
			$count=0;
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				$count++;
			}
			if($first)
			{
				echo "<tr style=\"font-weight: bold; text-align: center;\"><td>Member</td><td>Hours</td></tr>\n";
				$first=false;
			}
			echo "<tr><td>$userDataArray[firstName] $userDataArray[lastName]</td><td style=\"text-align: center;\">";
			if($count == 0)
			{
				echo "<span class=\"redHeading\">$count</span>";
			}
			else
			{
				echo "$count";
			}
			echo "</td></tr>";
		} ?>
	</table>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>