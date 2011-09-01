<?php
session_start();
include_once('php/login.php');
$authUsers = array('admin', 'saa');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<script language="jscript" type="text/javascript">
function Confirm()
{
return confirm ("Are you sure you want want to make these changes?");
}
</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Election Update</h1>
	<form id="honorBoard" name="honorBoard" method="post" action="php/honorBoard.php" onSubmit="return Confirm();">
		
		<p>
			Put a check next to any user on honor board. This will give them access to view write ups but not change them.
			</p>
		<p>&nbsp;</p>
		<?php
		include_once('php/login.php');
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
		
		echo "<table align=\"center\">";
		for($i=0; $i < $memberCount; $i++)
		{
			echo "<tr><td><label>".$members[$i]['firstName']." ".$members[$i]['lastName']." </td>\n";
			echo "<td><input name=\"".$members[$i]['username']."\" type=\"checkbox\" value=\"checked\" ";
			
			if( strpos($members[$i]['accountType'], "honorBoard") ){
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