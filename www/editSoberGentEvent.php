<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'drm');
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

<h1 align="center">Event Assignments</h1>
	<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
		if(date("n") > 5)
		{
			$year = date("Y")+1;
		}
		else
		{
			$year = date("Y");
		}
		
		$dateConstraint = date("Y-m-d", time()-2592000); //Find date of one month ago
		
		$eventData = "
			SELECT * 
			FROM soberGentEvents 
			WHERE ID = '".$_GET[id]."'";
		$getEventData = mysqli_query($mysqli, $eventData);
		$eventArray = mysqli_fetch_array($getEventData, MYSQLI_ASSOC);
		
		$assignedListQuery = "
			SELECT *
			FROM soberGentLog
			WHERE eventID = '".$_GET[id]."'
			ORDER BY username";
		$getAssignedList = mysqli_query($mysqli, $assignedListQuery);
		while($assignedListArray = mysqli_fetch_array($getAssignedList, MYSQLI_ASSOC))
		{
			$assignedArray[] = $assignedListArray[username];
		}
		
		$shortListQuery = "
			SELECT *
			FROM volunteer
			WHERE type = 'sober'";
		$getShortList = mysqli_query($mysqli, $shortListQuery);
		$shortListLength=0;
		while($shortListArray = mysqli_fetch_array($getShortList, MYSQLI_ASSOC))
		{
			if(array_search($shortListArray[username], $assignedArray) === false)
			{
				$query = "
					SELECT * 
					FROM soberGentLog
					WHERE username = '".$shortListArray[username]."'
					AND year = '$year'";
				$result = mysqli_query($mysqli, $query);
				$count=0;
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$count++;
				}
				
				$shortList[$shortListLength][username] = $shortListArray[username];
				$shortList[$shortListLength][count] = $count;
				$shortListLength++;
			}
		}
		
		if(date("n") > 5)
		{
			$schoolYear = date("Y")+1;
		}
		else
		{
			$schoolYear = date("Y");
		}
		
		$userData = "
			SELECT * 
			FROM members
			WHERE residency != 'limbo'
			ORDER BY lastName";
		$getUserData = mysqli_query($mysqli, $userData);
			
		$memberCount = 0;
		while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$memberCount++;
			
			$query = "
				SELECT ID
				FROM soberGentLog
				WHERE username = '".$userDataArray[username]."'
				AND eventID = '".$eventArray[ID]."'";
			
			$result = mysqli_query($mysqli, $query);
			if(!mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				$query = "
					SELECT * 
					FROM soberGentLog
					WHERE username = '".$userDataArray[username]."'
					AND year = '$year'";
				$result = mysqli_query($mysqli, $query);
				$count=0;
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					$count++;
				}
				
				for($i=0; $i < $shortListLength; $i++)
				{
					$assigned = false;
					if($shortList[$i][username] == $userDataArray[username])
					{
						$assigned = true;
						break;
					}
				}
				
				if($assigned == false)
				{
					switch ($count) {
						case 0:
							$zeroList[] = $userDataArray[username];
							break;
						case 1:
							$oneList[] = $userDataArray[username];
							break;
						case 2:
							$twoList[] = $userDataArray[username];
							break;
						default:
							$extraList[] = $userDataArray[username];
					}
				}
			}
		}
		
		
		
		?>
		<h2>People Assigned</h2>
			<table>
				<?php
					for($i=0; $i < count($assignedArray); $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $assignedArray[$i])
							{
								echo "<tr><td>".$members[$j][firstName]." ".$members[$j][lastName]."</td>\n";
								echo "<td><form action=\"php/soberGent.php\" method=\"post\">
											<input type=\"hidden\" name=\"user\" value=\"".$members[$j][username]."\"
											<input type=\"submit\" value=\"Remove\">
											<input type=\"hidden\" name=\"action\" value=\"remove\">
											<input type=\"hidden\" name=\"id\" value=\"".$_GET[id]."\">
											</form></td></tr>\n";
							}
						}
					}
					if(count($assignedArray) == 0)
					{
						echo "<p>No people assigned to the event.</p>";
					}
				?>
			</table>
		<h2>People Available</h2>
		<form action="php/soberGent.php" method="post">
			<p>
				To assign selected people to the event click the assign button. <input type="submit" value="Assign"/>
			</p>
			<table style="width: 100%">
				<tr style="font-weight:bold;">
					<td>Volunteers</td>
					<td>Never</td>
					<td>Once</td>
					<td>Twice</td>
					<td>Three or More</td>
				</tr>
				<tr valign="top">
					
					<td>
						<?php
					for($i=0; $i < $shortListLength; $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $shortList[$i][username])
							{
								echo "<input name=\"".$members[$j][username]."\" type=\"checkbox\" value=\"1\" /> ".$members[$j][firstName]." ".$members[$j][lastName]." (".$shortList[$i][count].")<br>";
							}
						}
					}
				?>
					</td>
					<td>
						<?php
					for($i=0; $i < count($zeroList); $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $zeroList[$i])
							{
								echo "<input name=\"".$members[$j][username]."\" type=\"checkbox\" value=\"1\" /> ".$members[$j][firstName]." ".$members[$j][lastName]."<br>";
							}
						}
					}
				?>
					</td>
					<td>
						<?php
					for($i=0; $i < count($oneList); $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $oneList[$i])
							{
								echo "<input name=\"".$members[$j][username]."\" type=\"checkbox\" value=\"1\" /> ".$members[$j][firstName]." ".$members[$j][lastName]."<br>";
							}
						}
					}
				?>
					</td>
					<td>
						<?php
					for($i=0; $i < count($twoList); $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $twoList[$i])
							{
								echo "<input name=\"".$members[$j][username]."\" type=\"checkbox\" value=\"1\" /> ".$members[$j][firstName]." ".$members[$j][lastName]."<br>";
							}
						}
					}
				?>
					</td>
					<td>
						<?php
					for($i=0; $i < count($extraList); $i++)
					{
						for($j=0; $j < count($members); $j++)
						{
							if($members[$j][username] == $extraList[$i])
							{
								echo "<input name=\"".$members[$j][username]."\" type=\"checkbox\" value=\"1\" /> ".$members[$j][firstName]." ".$members[$j][lastName]."<br>";
							}
						}
					}
				?>
					</td>
					
				</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo $_GET[id]; ?>"  />
			<input type="hidden" name="action" value="assign" />
		</form>
	<p><a href="manageSoberGentEvents.php">Back to event list.</a></p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>