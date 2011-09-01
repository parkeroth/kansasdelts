<?php
session_start();
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
$authUsers = array('brother');
include_once($_SERVER['DOCUMENT_ROOT'].'php/authenticate.php');

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");

//Custom CSS
echo '<style type="text/css">
	table {
		margin-left: auto;
		margin-right: auto;
		}
	th {
		font-size: 14px;
		text-align: center;
		}
	.menuItems {
		width: 200px;
		padding: 10px;
		}
	.weekday {
		width: 60px;
		padding: 10px;
		text-align: center;
		}
	</style>
	';
	
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
if (!$mysqli)
  {
  die('Could not connect: ' . mysql_error());
  }

//Query to get the current menu
$getMenuQ = "
	SELECT *
	FROM mealMenu
	WHERE showDay=\"Y\"
	ORDER BY id";
$curMenu = mysqli_query($mysqli, $getMenuQ);

$dayCount = 0;
while($dayIterator = mysqli_fetch_array($curMenu, MYSQLI_ASSOC))
{
	$dailyMenu[$dayCount]['weekday'] = $dayIterator['weekday'];
	$dailyMenu[$dayCount]['lunch'] = stripslashes($dayIterator['lunch']);
	$dailyMenu[$dayCount]['dinner'] = stripslashes($dayIterator['dinner']);
	$dayCount++;
}

echo '<h1>Weekly Meal Menu</h1>';

//Cool, now lets propagate the menu over a table
echo '
	<table border="1">
	<tr>
		<th class="weekday">Weekday</th>
		<th class="menuItems">Lunch</th>
		<th class="menuItems">Dinner</th>
	</tr>';

for($i=0; $i<$dayCount; $i++)
{
	echo '
		<tr>
			<td class="weekday">' . $dailyMenu[$i]['weekday'] . '</td>
			<td class="menuItems"><p>' . $dailyMenu[$i]['lunch'] . '</p></td>
			<td class="menuItems"><p>' . $dailyMenu[$i]['dinner'] . '</p></td>
		</tr>';
}
echo '</table>';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); 

?>