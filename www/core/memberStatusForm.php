<?php
session_start();
$authUsers = array('admin', 'pres', 'secretary');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';

include_once 'classes/Member.php';

$member_manager = new Member_Manager();
$member_list = $member_manager->get_all_members();

/**
 * Processing Section
 */
 
if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	foreach($member_list as $member){
		$member->standing = $_POST[$member->id.'-standing'];
		$member->residency = $_POST[$member->id.'-residency'];
		$member->status = $_POST[$member->id.'-status'];
		$member->excused = $_POST[$member->id.'-excused'];
		$member->save();
	}
}
 
/**
 * Form Section
 */

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
?>

<div style="text-align:center;">
	
	<h2>Member Status</h2>
	
</div>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<table style="text-align:center;" align="center">
			<tr>
				<td><strong>Member</strong></td><td width="100"px><strong>Residency</strong></td><td width="100"px><strong>Standing</strong></td><td width="100"px><strong>Status</strong></td><td width="100"px><strong>Excused</strong></td></tr>
			<?php 
		
		
		foreach($member_list as $member){
			
			echo "<tr>";
			echo "<td style=\"text-align: left;\">";
			echo 	"<label>".$member->first_name." ".$member->last_name." </td>\n";
			echo "<td><select name=\"".$member->id."-residency\">";
			
			echo "<option ";
			if($member->residency == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($member->residency  == "in"){ echo "selected=\"selected\" "; }
			echo "value=\"in\">Live In</option>";
			
			echo "<option ";
			if($member->residency  == "out"){ echo "selected=\"selected\" "; }
			echo "value=\"out\">Live Out</option>";
			
			echo "<option ";
			if($member->residency  == "limbo"){ echo "selected=\"selected\" "; }
			echo "value=\"limbo\">Limbo</option>";
			
			echo "</select></label></td>";
			echo "<td><select name=\"".$member->id ."-standing\">";
			
			echo "<option ";
			if($member->standing  == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($member->standing == "good"){ echo "selected=\"selected\" "; }
			echo "value=\"good\">Good</option>";
			
			echo "<option ";
			if($member->standing == "suspended"){ echo "selected=\"selected\" "; }
			echo "value=\"suspended\">Suspended</option>";
			
			echo "</select></label></td>";
			echo "<td><select name=\"".$member->id."-status\">";
			
			echo "<option ";
			if($member->status == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($member->status == "active"){ echo "selected=\"selected\" "; }
			echo "value=\"active\">Active</option>";
			
			echo "<option ";
			if($member->status == "pledge"){ echo "selected=\"selected\" "; }
			echo "value=\"pledge\">Pledge</option>";
			
			echo "</select></label></td>";
			echo "<td><select name=\"".$member->id."-excused\">";
			
			echo "<option ";
			if($member->excused == "select"){ echo "selected=\"selected\" "; }
			echo "value=\"select\">Select One</option>";
			
			echo "<option ";
			if($member->excused == "1"){ echo "selected=\"selected\" "; }
			echo "value=\"1\">Yes</option>";
			
			echo "<option ";
			if($member->excused == "0"){ echo "selected=\"selected\" "; }
			echo "value=\"0\">No</option>";
			
			echo "</select></label></td>";
			echo "</tr>\n";
		}
	?>
			</table>
		<p style="text-align:center;">
			<input type="submit" name="submit" id="submit" value="Submit" />
		</p>
</form>
	<p>&nbsp;</p>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>