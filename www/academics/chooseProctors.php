<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('admin', 'academics');
include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>

<script language="jscript" type="text/javascript">
function Confirm()
{
return confirm ("Are you sure you want want to make these changes?");
}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<?php
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

	//Lets do our POST processing here
	if(isset($_POST['submit']))
	{
		for($i = 0; $i < $memberCount; $i++) // Remove all people from proctors
		{	
			
			if( strpos($members[$i]['accountType'], "proctor") ){
				
				$str = substr_replace($members[$i]['accountType'], '', strpos($members[$i]['accountType'], '|proctor'), 8);
				
				$modify = "UPDATE members
					SET accountType = '$str'
					WHERE username = '".$members[$i]['username']."'";
				$doModification = mysqli_query($mysqli, $modify);
				
				//echo $modify."<br>";
				
				$members[$i]['accountType'] = $str;
				
			}
			
			if( isset($_POST[$members[$i]['username']]) ){
				
				$str = $members[$i]['accountType']."|proctor";
				
				$modify = "UPDATE members
					SET accountType = '$str'
					WHERE username = '".$members[$i]['username']."'";
				//echo $modify."<br>";
				$doModification = mysqli_query($mysqli, $modify);
				
			}
		}
	}
	
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

?>

<h1>Choose Proctors</h1>

	<form id="chooseProctor" name="chooseProctor" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
		
		<p>
        	Put a check next to any user who is a proctor. This will give them access to the management of study hours.
		</p>
		<p>&nbsp;</p>
		<?php
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
		
		
		echo "<table align=\"center\">";
		for($i=0; $i < $memberCount; $i++)
		{
			echo "<tr><td><label>".$members[$i]['firstName']." ".$members[$i]['lastName']." </td>\n";
			echo "<td><input name=\"".$members[$i]['username']."\" type=\"checkbox\" value=\"checked\" ";
			
			if( strpos($members[$i]['accountType'], "proctor") ){
				echo "checked=\"checked\"";
			}
			
			echo ">";
			echo "</td></tr>\n";
		}
		echo "</table>";
	?>
		<p>&nbsp;</p>
		<div style="text-align:center;"><input type="submit" name="submit" id="submit" value="Submit" /></div>
	</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>