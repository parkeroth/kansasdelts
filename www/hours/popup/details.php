<?php
$authUsers = array('admin', 'communityService', 'houseManager', 'philanthropy', 'vpInternal', 'vpExternal', 'pres');
include_once $_SERVER['DOCUMENT_ROOT'].'/core/authenticate.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/util.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/core/classes/Member.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/events/classes/Event.php';
include_once '../classes/Hour_Log.php';


$member_id = $_GET[member_id];
$type = $_GET[type];
$term = $_GET[term];
$year = $_GET[year];
	
$hour_manager = new Hour_Log_Manager();
$logs = $hour_manager->get_by_term($member_id, $type, $term, $year);

echo "<h2>Member Records</h2>";
?>

<table style="text-align:center;" align="center">
<?php
echo "<tr style=\"text-align: center; font-weight: bold;\"><td>Event</td><td>Change</td><td>Date</td></tr>\n";

foreach($logs as $record)
{
	echo "<tr>\n";

	if($record->event_id == 0)
	{
		echo "<td>$record->notes</td>\n";
	}
	else if($record->event_id == -1)
	{
		echo "<td>Volunteer Task</td>\n";
	}
	else
	{
		$event = new Event($record->event_id);
		echo "<td>$event->title</td>\n";
	}

	echo "<td>$record->hours</td>\n";
	echo "<td>".$record->get_date_added()."</td>\n";
	echo "</tr>\n";
}
if(count($logs) == 0)
{
	echo "<tr><td colspan=\"2\"><p>No Records</p></td></tr>\n";
}
?> </table>