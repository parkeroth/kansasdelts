<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'saa', 'honor-board');
include_once('php/authenticate.php');


function getCategoryTitle($mysqli, $code){

	$catQuery = "SELECT title FROM writeUpCategories WHERE code = '$code'";
	$getCatTitle = mysqli_query($mysqli, $catQuery);
	
	if($titleArray = mysqli_fetch_array($getCatTitle, MYSQLI_ASSOC))
	{
		return $titleArray[title];
		
	} else {
		
		return "Other";
		
	}
}



include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>


<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2>Honor Board Write Ups</h2>
	<?php
		$auth_list = array('admin', 'saa');
		$haz_super_powers = $session->isAuth($auth_list);
		
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
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
			$members[$memberCount]['accountType'] = $userDataArray['accountType'];
			$members[$memberCount]['class'] = $userDataArray['class'];
			$members[$memberCount]['major'] = $userDataArray['major'];
			$memberCount++;
		}
		
		$revieWriteUps = "
		SELECT * 
		FROM writeUps 
		WHERE status='review'";
	
		$getReviewWriteUps = mysqli_query($mysqli, $revieWriteUps);
		
		$numRWR=0;
		$first=true;
		
		if($haz_super_powers){
		
			echo "<h3>Awaiting Review</h3>\n";

			echo "<table>\n";
			while ($reviewArray = mysqli_fetch_array($getReviewWriteUps, MYSQLI_ASSOC)){
				$numRWR++;

				for($i=0; $i<$memberCount; $i++)
				{
					if($members[$i]['username'] == $reviewArray[partyResponsible])
					{
						$partyFiling = $members[$i]['firstName']." ".$members[$i]['lastName'];
					}
				}

				if($first)
				{
					echo "<tr style=\"font-weight: bold;\"><td width=\"150\">Party Responible</td><td width=\"160\">Date of Occurance</td><td width=\"80\">Urgency</td><td>Category</td><td></td></tr>\n";
					$first = false;
				}

				$categoryTitle = getCategoryTitle($mysqli, $reviewArray[category]);

				echo "<tr>\n";
				echo "<td>$partyFiling</td><td>$reviewArray[dateOccured]</td><td>$reviewArray[urgency]</td><td>$categoryTitle</td>";
				echo "<td><a href=\"javascript:MM_openBrWindow('writeUpDetail.php?ID=$reviewArray[ID]','','width=500,height=440, scrollbars=1');\">Details</a></td>";
				echo "</tr>\n";
			}
			echo "</table>\n";

			if($numRWR == 0)
			{
				echo "<p>No new write ups.</p>";
			}
		}
		
		$activeWriteUps = "
		SELECT * 
		FROM writeUps
		WHERE status = 'active'";
	
		$getActiveWriteUps = mysqli_query($mysqli, $activeWriteUps);
		
		$numAWR=0;
		$first=true;
		
		echo "<h3>Active Cases</h3>\n";
		
		echo "<table>\n";
		while ($activeArray = mysqli_fetch_array($getActiveWriteUps, MYSQLI_ASSOC)){
			$numAWR++;
			
			for($i=0; $i<$memberCount; $i++)
			{
				if($members[$i]['username'] == $activeArray[partyResponsible])
				{
					$partyFiling = $members[$i]['firstName']." ".$members[$i]['lastName'];
				}
			}
			
			if($first)
			{
				echo "<tr style=\"font-weight: bold;\"><td width=\"150\">Party Responsible</td><td width=\"160\">Date of Occurance</td><td width=\"80\">Urgency</td><td>Category</td><td></td></tr>\n";
				$first = false;
			}
			
			$categoryTitle = getCategoryTitle($mysqli, $activeArray[category]);
			
			echo "<tr>\n";
			echo "<td>$partyFiling</td><td>$activeArray[dateOccured]</td><td>$activeArray[urgency]</td><td>$categoryTitle</td>";
			echo "<td><a href=\"javascript:MM_openBrWindow('writeUpDetail.php?ID=$activeArray[ID]','','width=500,height=440, scrollbars=1');\">Details</a></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		
		if($numAWR == 0)
		{
			echo "<p>No active write ups.</p>";
		}
		
		
		if($haz_super_powers){
		
		?>
		<p>&nbsp;</p>
		<h2>Upload Bylaws</h2>
		
		<form enctype="multipart/form-data" action="php/uploadBylaws.php" method="POST">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			Choose File: <input style="color:#fff;" name="userfile" type="file" />
			<input type="submit" value="Upload Bylaws" />
			<input type="hidden" name="type" value="bylaws" />
		</form>
		
		<h2>Upload House Rules</h2>
		
		<form enctype="multipart/form-data" action="php/uploadBylaws.php" method="POST">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			Choose File: <input style="color:#fff;" name="userfile" type="file" />
			<input type="submit" value="Upload House Rules" />
			<input type="hidden" name="type" value="rules" />
		</form>
		
		<p>
			Files must be .docx and less than 10 MB.
		</p>

<?php 
		}
include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>