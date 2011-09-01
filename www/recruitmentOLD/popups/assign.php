<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');

include_once('../../php/login.php');
	
$id = mysql_real_escape_string($_GET[ID]);

$recruitQuery = "
	SELECT *
	FROM recruits 
	WHERE ID = '$id'";
$getRecruit = mysqli_query($mysqli, $recruitQuery);
$recruitArray = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);

echo '<h1 style="text-align:center">Recruit Details</h1>';
?>

	<table class="details" align="center">
		<tr>
			<th>Name:</th>
			<td><?php echo $recruitArray[firstName].' '.$recruitArray[lastName]; ?></td>
		</tr>
		<tr>
			<th>Referred By:</th>
			<td><?php 
				$referredBy = $recruitArray[referredBy];
				
				if($referredBy == 'self'){
					
					echo "Self";
					
				} else if($referredBy == 'alum'){
					
					echo "<b>Alumni</b><br><br>";
					echo nl2br($recruitArray[referrerInfo]);
					
				} else {
					
					$memberQuery = "
						SELECT firstName, lastName
						FROM members
						WHERE username = '$referredBy'";
					$getMember = mysqli_query($mysqli, $memberQuery);
					$memberArray = mysqli_fetch_array($getMember, MYSQLI_ASSOC);
					
					echo $memberArray[firstName].' '.$memberArray[lastName];
					
				} ?></td>
		</tr>
		<tr>
			<th>Assign To:</th>
			<td><select id="assignTo" name="primaryContact">
					<?php
						$memberQuery = "
							SELECT firstName, lastName, username
							FROM members
							WHERE memberStatus != 'limbo'
							AND accountType LIKE '%recruitComm%'";
						$getMember = mysqli_query($mysqli, $memberQuery);
						while($memberArray = mysqli_fetch_array($getMember, MYSQLI_ASSOC)){
							$query = "
								SELECT COUNT(ID) as total
								FROM recruits
								WHERE primaryContact = '$memberArray[username]'";
							$result = mysqli_query($mysqli, $query);
							$totalData = mysqli_fetch_array($result, MYSQLI_ASSOC);
							
							echo '<option value="'.$memberArray[username].'">';
							echo $memberArray[firstName].' '.$memberArray[lastName].' ('.$totalData[total].')';
							echo '</option>';
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th> </th>
			<td><input id="<?php echo $recruitArray[ID]; ?>" class="submitAssign" type="button" value="Assign" /></td>
		</tr>
	</table>
