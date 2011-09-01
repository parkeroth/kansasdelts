<?php

$query = "
	SELECT soberGentEvents.eventDate AS eventDate, events.title AS title
	FROM soberGentEvents
	JOIN events
	ON soberGentEvents.eventID=events.ID
	JOIN soberGentLog
	ON soberGentEvents.ID=soberGentLog.eventID
	WHERE soberGentLog.username='".$_SESSION[username]."'
	AND soberGentEvents.eventDate > '".date("Y-m-d")."'";
$result = mysqli_query($mysqli, $query);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
{
	echo '<div class="ui-widget"><div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">';
	echo "<p>You are a sober gent for <b>".$row[title]."</b> on <b>".$row[eventDate]."</b></p>";
	echo '</div></div>';
}

?>