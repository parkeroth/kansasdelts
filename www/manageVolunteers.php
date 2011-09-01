<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'houseManager');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1 align="center">House Hour Volunteer List</h1>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$year = date("Y");
		$month = date("n");
		
		if($month < 6)
		{
			$term = "Spring";
		}
		else
		{
			$term = "Fall";
		}
		
		$volunteerData = "
			SELECT * 
			FROM volunteer 
			WHERE type='house'";
		$getVolunteerData = mysqli_query($mysqli, $volunteerData);
		
		$userData = "
			SELECT * 
			FROM members
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
			
		$memberCount = 0;
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$memberCount++;
		}
		
		echo "<p align=\"center\">Hours will be applied to $term $year term.</p>";
		
		echo "<table align=\"center\" style=\"text-align: center;\">";
		echo "<tr style=\"font-weight: bold;\"><td>&nbsp;</td><td width=\"200\">&nbsp;</td><td>&nbsp;</td></tr>";
		$count=0;
		while($volDataArray = mysqli_fetch_array($getVolunteerData, MYSQLI_ASSOC)){
			
			for($i=0; $i<$memberCount; $i++)
			{
				if($members[$i][username] == $volDataArray[username])
				{
					echo "<tr>";
					echo "<td>".$members[$i][firstName]." ".$members[$i][lastName]."</td>";
					
					echo "<td><form action=\"php/volunteerAward.php\" method=\"POST\">";
					echo "<input type=\"text\" size=\"1\" name=\"hours\" value=\"0\">";
					echo "<input type=\"hidden\" name=\"user\" value=\"".$members[$i][username]."\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"award\">";
					echo "<input type=\"submit\" value=\"Award Hours\"></form></td>";
					
					echo "<td><form action=\"php/volunteerAward.php\" method=\"POST\">";
					echo "<input type=\"hidden\" name=\"user\" value=\"".$members[$i][username]."\">";
					echo "<input type=\"hidden\" name=\"action\" value=\"remove\">";
					echo "<input type=\"submit\" value=\"Remove\"></form></td>";
					echo "</tr>";
				}
			}
			
			
			
			$count++;
		}
		
		if($count == 0){
			echo "<p align=\"center\">No people have volunteered for work.</p>";
		}
		
		echo "</table>"
	?>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>