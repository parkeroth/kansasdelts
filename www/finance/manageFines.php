<?php
session_start();
$authUsers = array('admin', 'treasurer', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once 'classes/Fine.php';

$action = $_GET[action];

if($action == 'accept')
{
	$fine_id = $_GET[id];
	$fine = new Fine($fine_id);
	$fine->accept();	
}
else if($action == 'reject')
{
	$fine_id = $_GET[id];
	$fine = new Fine($fine_id);
	$fine->reject();
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); ?>

<link type="text/css" href="../styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />

<script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.1.custom.min.js"></script>

<script>

$(document).ready(function(){
	
	$(".accept").click(function(){
		var id = $(this).attr('id');
		window.location = 'manageFines.php?action=accept&id=' + id;	
	});
	
	$(".reject").click(function(){
		var id = $(this).attr('id');
		window.location = 'manageFines.php?action=reject&id=' + id;	
	});
});

</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h2>Pending Missed Duties</h2>
	<?php
		$fine_manager = new Fine_Manager();
		$fine_list = $fine_manager->get_all('pending');
		
		$sem = new Semester();
		
		$first=true;				
		echo "<table>\n";
		foreach($fine_list as $record){
			$offender = new Member($record->member_id);
			if($first){
				echo "<tr style=\"font-weight: bold;\"><td width=\"120\">Member</td><td width=\"80\">Fine</td><td width=\"200\">Reason</td><td  width=\"120\">Date</td></tr>\n";
				$first = false;
			}
						
			echo "<tr>\n";
			echo "<td>$offender->first_name $offender->last_name</td>";
			echo "<td>$".number_format($record->amount, 2)."</td>";
			echo "<td>$record->description</td>";
			echo "<td>".date('M j, Y', strtotime($record->date))."</td>";
			echo "<td>";
			echo "<input id=\"$record->id\" class=\"accept\" type=\"button\" value=\"Accept\" />";
			echo "<input id=\"$record->id\" class=\"reject\" type=\"button\" value=\"Reject\" />";
			echo "</td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
		
		if(count($fine_list) == 0)
		{
			echo "<p>No pending fines.</p>";
		} ?>
		
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>