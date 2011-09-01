<?php
$authUsers = array('brother');
include_once('../../php/authenticate.php');
include_once('../../php/login.php');
include_once('../classes/Recruit.php');
include_once('../classes/MemberManager.php');
include_once('../classes/RecruitManager.php');
include_once('../classes/RecruitCall.php');

if($_SERVER['REQUEST_METHOD'] == "POST") {
	$recruit_id = $_POST[recruit_id];
	$recruit = new Recruit($mysqli, $recruit_id);
	$recruit->saveVal('primaryContact', $_POST[primaryContact]);
	
	$call = new RecruitCall($mysqli);
	$call->dateRequested = date('Y-m-d');
	$call->memberID = $_POST[primaryContact];
	$call->recruitID = $recruit->id;
	$call->type = 'initial';
	$call->status = 'pending';
	$call->insert();	
	
	header('location: ../newList.php');
}

$id = mysql_real_escape_string($_GET[ID]);

$recruit = new Recruit($mysqli, $id);
$recruit_manager = new RecruitManager($mysqli);
$member_manager = new MemberManager($mysqli);

echo '<h1 style="text-align:center">Recruit Details</h1>';
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="assignForm">
	<table class="details" align="center">
		<tr>
			<th>Name:</th>
			<td><?php echo $recruit->firstName.' '. $recruit->lastName; ?></td>
		</tr>
		<tr>
			<th>Referred By:</th>
			<td><?php echo $recruit->referred_by(); ?></td>
		</tr>
		<tr>
			<th>Assign To:</th>
			<td><select id="assignTo" name="primaryContact">
					<?php
						$member_list = $member_manager->get_members_by_position('recruitComm');
						foreach($member_list as $member){
							$num_recruits = sizeof($recruit_manager->get_recruits_by_owner($member->username));
							
							echo '<option value="'.$member->username.'">';
							echo $member->firstName.' '.$member->lastName.' ('.$num_recruits.')';
							echo '</option>';
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<th> </th>
			<td><input type="hidden" name="recruit_id" value="<?php echo $recruit->id; ?>" />
            	<input type="submit" value="Assign" /></td>
		</tr>
	</table>
</form>