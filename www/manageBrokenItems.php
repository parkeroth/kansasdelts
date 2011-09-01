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

<h1 align="center">Broken Items List</h1>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		$query = "
			SELECT * 
			FROM brokenStuff";
		$result = mysqli_query($mysqli, $query);
		
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
		
		echo "<table align=\"center\" style=\"text-align: left;\">";
		
		$first = true;
		
		$count=0;
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			
			for($i=0; $i<$memberCount; $i++)
			{
				if($members[$i][username] == $row[reportedBy])
				{
					if($first)
					{
						echo "<tr style=\"font-weight: bold;\"><td width=\"160\">Item</td><td width=\"240\">Description</td><td width=\"100\">Reported By</td><td width=\"120\">&nbsp;</td></tr>";
						$first = false;
					}
					echo "<tr>";
					echo "<td>$row[item]</td>";
					echo "<td>$row[description]</td>";
					echo "<td>".$members[$i][firstName]." ".$members[$i][lastName]."</td>";
					echo "<td><input type=\"button\" name=\"remove\" value=\"Remove\" ONCLICK=\"window.location.href='php/brokenItemRemove.php?id=$row[ID]'\"></td>";
					echo "</tr>";
				}
			}
			
			
			
			$count++;
		}
		
		if($count == 0){
			echo "<p align=\"center\">No problems have been reported.</p>";
		}
		
		echo "</table>"
	?>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>