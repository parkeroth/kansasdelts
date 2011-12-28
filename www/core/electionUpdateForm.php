<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('authenticate.php');

require_once 'classes/Member.php';


/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	
	
	// Set $year to correct time based on month
	if(isset($_POST[year]))
	{
		$year = $_POST[year];
	}
	else if(date("j") < 10)
	{
		$year = date("Y")+1;
	}
	else
	{
		$year = date("Y");
	}
	
	$month = date(n);
	$season = $_POST[term];
	
	// Check if position logs must be updated
	if( ($season == "both") || ($season == "fall" && $month > 4) || ($season == "spring" && $month < 5) )
	{
		$update = true;	
	}
	else
	{
		$update = false;	
	}
	
	// Load position info into $positions
	$positionData = "
		SELECT * 
		FROM positions
		ORDER BY ID";
	$getPositionData = mysqli_query($mysqli, $positionData);
	
	$positionCount = 0;
	while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
	{
		$positions[$positionCount]['type'] = $positionDataArray['type'];
		$positions[$positionCount]['title'] = $positionDataArray['title'];
		$positions[$positionCount]['board'] = $positionDataArray['board'];
		$positionCount++;
	}
	
	// Load members info into $members
	$userData = "
		SELECT * 
		FROM members
		ORDER BY lastName";
	$getUserData = mysqli_query($mysqli, $userData);
	
	$memberCount = 0;
	while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
	{
		$members[$memberCount]['username'] = $userDataArray['username'];
		$members[$memberCount]['accountType'] = $userDataArray['accountType'];
		$memberCount++;
	}
	
	
	////////////////////////    UPDATE POSITION LOGS     ///////////////////////
	
	for($i = 0; $i < $positionCount; $i++)
	{
		if($season == "both")
		{
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = 'spring'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = 'spring'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, 'spring')";
				$doModification = mysqli_query($mysqli, $modify);
			}
			
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = 'fall'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = 'fall'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, 'fall')";
				$doModification = mysqli_query($mysqli, $modify);
			}
		}
		else
		{
			$check = "SELECT ID 
				FROM positionLog
				WHERE year = '$year'
				AND season = '$season'
				AND position = '".$positions[$i][type]."'";
			$checkTable = mysqli_query($mysqli, $check);
			
			if(mysqli_fetch_row($checkTable))
			{
				$modify = "UPDATE positionLog
					SET username = '".$_POST['NEW'.$positions[$i]['type']]."'
					WHERE year = '$year'
					AND season = '$season'
					AND position = '".$positions[$i][type]."'";
				$doModification = mysqli_query($mysqli, $modify);
			}
			else
			{
				$modify = "INSERT INTO positionLog
					(username, position, year, season)
					VALUES ('".$_POST['NEW'.$positions[$i]['type']]."', '".$positions[$i][type]."', $year, '$season')";
				$doModification = mysqli_query($mysqli, $modify);
			}
		}
	}
	
	////////////////////////    UPDATE MEMBER RECORDS     ////////////////////////
	if($update)
	{
			
		for($i = 0; $i < $positionCount; $i++)
		{
			if($positions[$i]['type'] == "webmaster" || $positions[$i]['type'] == "pres" || $positions[$i]['type'] == "vicePres")
			{
				$admin = "|admin";
			}
			else
			{
				$admin = "";
			}
			
			$newType[$_POST['NEW'.$positions[$i]['type']]] = $admin.'|'.$positions[$i]['type'];
			
		}
		
		for($i = 0; $i < $memberCount; $i++)
		{
						
			if(!isset($newType[$members[$i][username]])){
				$newType[$members[$i][username]] = "|brother";
			}
			
			if(strpos($members[$i][accountType], "proctor"))
			{
				$newType[$members[$i][username]] .= "|proctor";
				
			}
			
			if(strpos($members[$i][accountType], "honorBoard"))
			{
				$newType[$members[$i][username]] .= "|honorBoard";
			}
			
			//echo $members[$i][username]." ".$newType[$members[$i][username]]."<br>";
			
			$modify = "	UPDATE members
						SET accountType = '".$newType[$members[$i][username]]."'
						WHERE username = '".$members[$i]['username']."'";
			$doModification = mysqli_query($mysqli, $modify);
		}
		
	}
	header("location: ../account.php");
	
}
	


 
/**
 * Form Section
 */


include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="jscript" type="text/javascript">
	function Confirm()
	{
		return confirm ("Are you sure you want want to make these changes?");
	}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Election Update</h1>
	<form id="electionUpdate" name="electionUpdate" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
		
		<p>
			Year: 
			<select name="year" id="year">
				<?
		  $yearLoop = date("Y");
		  
		  for ($i = $yearLoop; $i <= $yearLoop+1; $i++) {
		  	IF($i == $_GET['year']){
				$selected = "selected";
			} ELSE {
				$selected = "";
			}
		  	echo "<option value=\"$i\" $selected>$i</option>\n";
		  }
		  ?>
				</select>
			
			<?php 
			$month = date(n);
		
			if($month > 0 && $month < 6){
				$season = "spring";
			} else if($month > 7 && $month < 13){
				$season = "fall";
			}
			
		?>
			
			Term:
			<select name="term" id="term">
				<?
		  echo "<option value=\"both\" selected >Both</option>\n";
		  echo "<option value=\"fall\">Fall</option>\n";
		  echo "<option value=\"spring\">Spring</option>\n";
		  ?>
				</select>
			
			</p>
		<p>
			If you are updating the records following the main election select the year the officials will be in office and the term <strong>both</strong>.</p>
		<p>If you are updating the records during any other time select the term the person will be serving in <strong>both</strong>, <strong>spring</strong>, or <strong>fall</strong>.</p>
		<p>&nbsp;</p>
		<?php
		include_once('php/login.php');
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
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
			$members[$memberCount]['accountType'] = $userDataArray['accountType'];
			$members[$memberCount]['class'] = $userDataArray['class'];
			$members[$memberCount]['major'] = $userDataArray['major'];
			$memberCount++;
		}
		
		$positionData = "
			SELECT * 
			FROM positions
			ORDER BY ID";
		$getPositionData = mysqli_query($mysqli, $positionData);
		
		$positionCount = 0;
		while($positionDataArray = mysqli_fetch_array($getPositionData, MYSQLI_ASSOC))
		{
			$positions[$positionCount]['type'] = $positionDataArray['type'];
			$positions[$positionCount]['title'] = $positionDataArray['title'];
			$positions[$positionCount]['board'] = $positionDataArray['board'];
			$positionCount++;
		}
		
		echo "<table>";
		for($i=0; $i < $positionCount; $i++)
		{
			echo "<tr><td><label>".$positions[$i]['title']." </td>\n";
			echo "<td><select name=\""."NEW".$positions[$i]['type']."\">";
			for($j=0; $j < $memberCount; $j++)
			{
				echo "<option value=\"".$members[$j]['username']."\"";
				if(strpos($members[$j]['accountType'], $positions[$i]['type']))
				{
					echo " selected ";
				}
				echo ">";
				echo $members[$j]['firstName']." ".$members[$j]['lastName'];
				echo "</option>";
				
				if($members[$j]['accountType'] == $positions[$i]['type'])
				{
					$string = " <input type=\"hidden\" value=\"".$members[$j]['username']."\" name=\""."OLD".$positions[$i]['type']."\" >";
				}
			}
			echo "</select>";
			echo $string;
			echo "</td></tr>\n";
		}
		echo "</table>";
	?>
		<p>&nbsp;</p>
		<input type="submit" name="submit" id="submit" value="Submit" />
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>