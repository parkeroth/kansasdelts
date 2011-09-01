<?php
session_start();
$authUsers = array('admin', 'secretary');
include_once('php/authenticate.php');

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
	$memberCount++;
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<form id="form1" name="form1" method="post" action="php/deleteUser.php">
	<table style="text-align:center;">
		<tr><td width="100px;"><b>Remove</b></td><td style="text-align:left;"><b>User</b></td></tr>
		<?php
  	for($i = 0; $i < $memberCount; $i++)
	{
		echo '<tr><td><input name="'.$members[$i]['username'].'" type="checkbox" value="1" /></td>';
		echo '<td style="text-align: left;">'.$members[$i]['firstName']." ".$members[$i]['lastName']."</td></tr>\n";
	}
  ?>
		</table>
	<br />
	<input type="submit" name="submit" id="submit" value="Submit" />
	<label>
		<input type="reset" name="Reset" id="Reset" value="Reset" />
		</label>
	</p>
</form>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>