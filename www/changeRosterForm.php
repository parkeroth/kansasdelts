<?php
session_start();
$authUsers = array('brother');
include_once('php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<?php
	
		$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		$memberInfo = "
			SELECT * 
			FROM members 
			WHERE username = '".$_SESSION['username']."'";
	
		$getMemberInfo = mysqli_query($mysqli, $memberInfo);
	
		$memberInfoArray = mysqli_fetch_array($getMemberInfo, MYSQLI_ASSOC);
		
		echo '<form name="rosterChange" action="php/updateRoster.php" method="post">';
		
		echo '<div style="float:left;">';
    	echo "<p><h2>Contact Info</h2><table width=\"300px\">";
		echo "<tr><td>Email: </td><td><input name=\"email\" type=\"text\" value=\"".$memberInfoArray['email']."\" /><br></td></tr>";
		echo "<tr><td>Phone Number: </td><td><input name=\"phoneNumber\" type=\"text\" value=\"".$memberInfoArray['phone']."\" /><br></td></tr>";
		echo "<tr><td>Carrier: </td><td><select name=\"carrier\" />";
		
		echo "<option value=\"none\" ";
		if($memberInfoArray['carrier'] == NULL){echo "selected";}
		echo "></option>";
		echo "<option value=\"verizon\" ";
		if($memberInfoArray['carrier'] == "verizon"){echo "selected";}
		echo ">Verizon</option>";
		echo "<option value=\"sprint\" ";
		if($memberInfoArray['carrier'] == "sprint"){echo "selected";}
		echo ">Sprint</option>";
		echo "<option value=\"tmobile\" ";
		if($memberInfoArray['carrier'] == "tmobile"){echo "selected";}
		echo ">T-Mobile</option>";
		echo "<option value=\"att\" ";
		if($memberInfoArray['carrier'] == "att"){echo "selected";}
		echo ">AT&amp;T</option>";
		
		echo "</select><br></td></tr>";
		echo "</table></p>";
		
		echo "<p><h2>Personal Info</h2><table width=\"300px\">";
		echo "<tr><td>Major: </td><td><input name=\"major\" type=\"text\" value=\"".$memberInfoArray['major']."\" /><br></td></tr>";
		echo "<tr><td>School: </td><td><select name=\"school\" />";
		
		$query = "
			SELECT * 
			FROM schools";
	
		$get = mysqli_query($mysqli, $query);
	
		while($result = mysqli_fetch_array($get, MYSQLI_ASSOC)){
			
			echo "<option value=\"$result[code]\" ";
			if($memberInfoArray['school'] == $result[code]){echo "selected";}
			echo ">$result[name]</option>";
			
		}
		
		echo "</select><br></td></tr>";
		echo "<tr><td>Graduation Year: </td><td><input name=\"gradYear\" type=\"text\" value=\"".$memberInfoArray['gradYear']."\" /><br></td></tr>";
		echo "<tr><td>Home Town: </td><td><input name=\"homeTown\" type=\"text\" value=\"".$memberInfoArray['homeTown']."\" /><br></td></tr>";
		echo "<tr><td>Shirt Size: </td><td><select name=\"shirtSize\" />";
		
		echo "<option value=\"none\" ";
		if($memberInfoArray['shirtSize'] == NULL){echo "selected";}
		echo "></option>";
		echo "<option value=\"s\" ";
		if($memberInfoArray['shirtSize'] == "s"){echo "selected";}
		echo ">S</option>";
		echo "<option value=\"m\" ";
		if($memberInfoArray['shirtSize'] == "m"){echo "selected";}
		echo ">M</option>";
		echo "<option value=\"l\" ";
		if($memberInfoArray['shirtSize'] == "l"){echo "selected";}
		echo ">L</option>";
		echo "<option value=\"xl\" ";
		if($memberInfoArray['shirtSize'] == "xl"){echo "selected";}
		echo ">XL</option>";
		echo "<option value=\"xxl\" ";
		if($memberInfoArray['shirtSize'] == "xxl"){echo "selected";}
		echo ">XXL</option>";
		
		echo "</select><br></td></tr>";
		echo "</table></p>";
		
		echo '<p><input type="submit" name="submit" id="submit" value="Submit" /></p>';
		echo '</div>';
		
		echo '<div style="float: right;">';
		echo "<p><h2>Parent's Info</h2><table width=\"300px\">";
		echo "<tr><td>Name: </td><td><input name=\"parentName\" type=\"text\" value=\"".$memberInfoArray['parentName']."\" /><br></td></tr>";
		echo "<tr><td>Address: </td><td><input name=\"parentAddress\" type=\"text\" value=\"".$memberInfoArray['parentAddress']."\" /><br></td></tr>";
		echo "<tr><td>Email: </td><td><input name=\"parentEmail\" type=\"text\" value=\"".$memberInfoArray['parentEmail']."\" /><br></td></tr>";
		echo "</table></p>";
		echo '</div>';
		
		echo '<div style="clear:both;"><p>&nbsp;</p></div>';
		echo '</form>';

	?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>