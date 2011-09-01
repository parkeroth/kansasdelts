<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');

include_once('../../php/login.php');
	
$id =mysql_real_escape_string( $_GET[ID]);

$recruitQuery = "
	SELECT *
	FROM recruits 
	WHERE ID = '$id'";
$getRecruit = mysqli_query($mysqli, $recruitQuery);
$recruitArray = mysqli_fetch_array($getRecruit, MYSQLI_ASSOC);

echo '<h1 style="text-align:center">Recruit Details</h1>';
?>

<div id="generalInfo">

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
	</table>
</div>

<div id="schoolInfo">

	<h3>School Info</h3>
	
	<table class="details">
		<tr>
			<th>Current School:</th>
			<td><?php echo $recruitArray[currentSchool]; ?></td>
		</tr>
		<tr>
			<th>HS Grad Year:</th>
			<td><?php echo $recruitArray[hsGradYear]; ?></td>
		</tr>
		<tr>
			<th>GPA:</th>
			<td><?php 
			
				// TODO: color red if below min
				echo $recruitArray[gpa]
				
			?></td>
		</tr>
		<tr>
			<th>ACT Score:</th>
			<td><?php echo $recruitArray[actScore]; ?></td>
		</tr>
		<tr>
			<th>Major:</th>
			<td><?php echo $recruitArray[intendedMajor]; ?></td>
		</tr>
	</table>
</div>

<div id="contactInfo">

	<h3>Contact Info</h3>
	
	<table class="details">
		<tr>
			<th>Phone:</th>
			<td><?php echo $recruitArray[phoneNumber]; ?></td>
		</tr>
		<tr>
			<th>Email:</th>
			<td><?php echo $recruitArray[email]; ?></td>
		</tr>
		<tr>
			<th>Address:</th>
			<td>
				<?php echo $recruitArray[address]; ?><br />
				<?php echo $recruitArray[city]; ?><br />
				<?php echo $recruitArray[state]; ?><br />
				<?php echo $recruitArray[zip]; ?><br />
			</td>
		</tr>
	</table>
</div>

<div id="otherInfo">

	<h3>Other Info</h3>
	
	<table class="details" align="center">
		<tr>
			<th>Bio:</th>
			<td><?php echo nl2br($recruitArray[bio]); ?></td>
		</tr>
		<tr>
			<th>Interests:</th>
			<td><?php echo nl2br($recruitArray[interests]); ?></td>
		</tr>
	</table>
	
	<div id="viewControls">
		<input type="button" value="Edit"  />
		<input class="closeWindow" type="button" value="Close" />
	</div>
	
</div>