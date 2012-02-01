<?
session_start();
$authUsers = array('admin', 'saa', 'honor-board', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

$ID = $_GET[ID];

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

$writeUp = "
SELECT * 
FROM writeUps 
WHERE ID='$ID'";
$getWriteUp = mysqli_query($mysqli, $writeUp);
$writeUpData = mysqli_fetch_array($getWriteUp, MYSQLI_ASSOC);

for($i=0; $i<$memberCount; $i++)
{
	if($members[$i]['username'] == $writeUpData[partyFiling])
	{
		$partyFiling = $members[$i]['firstName']." ".$members[$i]['lastName'];
	}
	
	if($members[$i]['username'] == $writeUpData[partyResponsible])
	{
		$partyResponsible = $members[$i]['firstName']." ".$members[$i]['lastName'];
	}
}

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

$super_list = array('admin', 'saa');
$haz_super_powers = $session->isAuth($super_list);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Member Report Form</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="images/cal.css" rel="stylesheet" type="text/css">

<style>
	th {
		text-align: right;
	}
	
	a:link {
		font-weight: bold;
		color: #FDC029;
		text-decoration: none;
	}
	
	a:visited {
		font-weight: bold;
		color: #CF9200;
		text-decoration: none;
	}
	
	a:hover {
		font-weight: bold;
		color: #FFD46F;
		text-decoration: none;
	}
	
	
</style>

</head>
<body>
<h2>Honor Board - Member Report Form</h2>
<form action="writeUpAction.php" method="post">
<table>
	<tr>
		<th width="160">Party Filing:</th>
		<td><?php echo $partyFiling;?></td>
	</tr>
	<tr>
		<th>Date Reported:</th>
		<td><?php echo $writeUpData[dateFiled];?></td>
	</tr>
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<tr>
		<th>Party Responsible:</th>
		<td><?php echo $partyResponsible;?></td>
	</tr>
	<tr>
		<th>Date of Complaint:</th>
		<td><?php echo $writeUpData[dateOccured];?></td>
	</tr>
	<tr>
		<th>Urgency:</th>
		<td><?php echo $writeUpData[urgency];?></td>
	</tr>
	<tr>
		<th>Category:</th>
		<td>
		
		<select name="category">
			
			<?php
				$catQuery = "SELECT * FROM writeUpCategories ORDER BY ID DESC";
				$getCat = mysqli_query($mysqli, $catQuery);

				while($catArray = mysqli_fetch_array($getCat, MYSQLI_ASSOC)){
					echo "<option value=\"$catArray[code]\"";
					
					if($writeUpData[category] == $catArray[code]) { echo " selected "; }
					
					echo ">$catArray[title]</option>";
				}
				
			?>
			
		</select>
		
		</td>
	</tr>
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<tr>
		<th>Description:</th>
		<td><?php echo $writeUpData[description];?></td>
	</tr>
	<tr><th>&nbsp;</th><td>&nbsp;</td></tr>
	<?php
		
		if($writeUpData[status] == "active" && $haz_super_powers)
		{
			?>
			<tr>
				<th>Action Taken:</th>
				<td><textarea name="actionTaken" cols="30" rows="5"><?php echo $writeUpData[actionTaken];?></textarea></td>
			</tr>
			<tr>
				<th>Verdict:</th>
				<td>Guilty <input name="verdict" type="radio" value="guilty" <?php if($writeUpData[verdict] == "guilty") { ?> checked <?php } ?> > 
					Innocent <input name="verdict" type="radio" value="innocent" <?php if($writeUpData[verdict] == "innocent") { ?> checked <?php } ?> ></td>
			</tr>
			<tr>
				<th>Settle Case:</th>
				<td>Yes <input name="settled" type="radio" value="yes"> No <input name="settled" type="radio" value="no" checked></td>
			</tr>
			<tr>
				<th></th>
				<td><input name="submit" type="submit"></td>
			</tr>
			<?php
		}
		else if($writeUpData[status] == "review" && $haz_super_powers )
		{
			
			?>
			<tr style="text-align:center;">
				<td colspan="2"><hr>
				
					
					<a href="writeUpAction.php?ID=<?php echo $ID;?>&amp;action=discard">Discard</a> | 
					<a href="writeUpAction.php?ID=<?php echo $ID;?>&amp;action=consider">Consider Case</a> | 
					<a href="javascript: window.close();">Close</a>
				</td>
			</tr>
			<?php	
			
		}
	?>
</table>
<input type="hidden" name="ID" value="<?php echo $ID; ?>">
</form>

</body>
</html>