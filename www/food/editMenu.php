<?php
$realm = 'Restricted Area';

//Our authentication data
//user => password
$users = array('stan' => 'nattyLight', 'admin' => 'DTD2006GT');


if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Unautorized to edit the menu');
}


// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
    !isset($users[$data['username']]))
    die('Wrong Credentials!');


// generate the valid response
$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response)
    die('Wrong Credentials!');

// ok, valid username & password
//***************This is where a bulk of the code goes**************
include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');


//Lets get the current menu
//We'll progagate all our table fields with the current menu
//and leave it up to the user to delete it if staring over

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
if (!$mysqli)
  {
  die('Could not connect: ' . mysql_error());
  }
//Query to get the current menu
$getMenuQ = "
	SELECT *
	FROM mealMenu
	ORDER BY id";
$curMenu = mysqli_query($mysqli, $getMenuQ);

$dayCount = 0;
while($dayIterator = mysqli_fetch_array($curMenu, MYSQLI_ASSOC))
{
	$dailyMenu[$dayCount]['weekday'] = $dayIterator['weekday'];
	$dailyMenu[$dayCount]['lunch'] = stripslashes(str_replace("<br />","",$dayIterator['lunch']));
	$dailyMenu[$dayCount]['dinner'] = stripslashes(str_replace("<br />","",$dayIterator['dinner']));
	$dailyMenu[$dayCount]['showDay'] = $dayIterator['showDay'];
	$dayCount++;
}

echo '<script language="jscript" type="text/javascript">
	function Confirm()
	{
		return confirm ("Are you sure you want want to make these changes?");
	}
	</script>
	<script language="jscript" type="text/javascript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>
	<script language="jscript" type="text/javascript">
	function clearMenu() {';
		for($i=0; $i<$dayCount; $i++)
		{
			echo 'document.mealMenu.' . $dailyMenu[$i]['weekday'] . 'Lunch.value=\'\';
			';
			echo 'document.mealMenu.' . $dailyMenu[$i]['weekday'] . 'Dinner.value=\'\';
			';
		}
echo	'}
	</script>';
//Custom CSS
echo '<style type="text/css">
	table {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
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
		width: 50px;
		padding: 10px;
		}
	.loggedIn {
		text-align: center;
		font-size: 20px;
		color: red;
		text-decoration: underline;
		}
	.submit {
		margin-left: auto;
		margin-right: auto;
		text-align: center;
		}
	.showDay {
		width: 50px;
		padding: 10px;
		}
	</style>
	';

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");
//echo '</head><body>';
echo '<p class="loggedIn">You are logged in as: ' . $data['username'] . '</p>';

//Provide button to clear menu
echo '<br /><br />
<form> 
<p class="submit"><input type="button" value="  Clear the Menu  " name="clrMenu" onClick="clearMenu();"> </p>
</form><br /><br />';

//Cool, now lets make a form and propagate the data
echo '
	<form id="mealMenu" name="mealMenu" method="POST" action="'.$_SERVER['PHP_SELF'].'" onSubmit="return Confirm();">
	<table border="1">
	<tr>
		<th class="weekday">Weekday</th>
		<th class="menuItems">Lunch</th>
		<th class="menuItems">Dinner</th>
		<th class="showDay">Show<br />Day</th>
	</tr>';

for($i=0; $i<$dayCount; $i++)
{
	echo '
		<tr>
			<td class="weekday">' . $dailyMenu[$i]['weekday'] . '<input type="hidden" name="weekday" value="' . $dailyMenu[$i]['weekday'] . '" /></td>
			<td class="menuItems"><textarea name="' . $dailyMenu[$i]['weekday'] . 'Lunch" rows="7" cols="25">' . $dailyMenu[$i]['lunch'] . '</textarea></td>
			<td class="menuItems"><textarea name="' . $dailyMenu[$i]['weekday'] . 'Dinner" rows="7" cols="25">' . $dailyMenu[$i]['dinner'] . '</textarea></td>';
	if($dailyMenu[$i]['showDay'] == 'Y') 
	{
		echo '
			<td class="showDay"><input type="checkbox" name="show' . $dailyMenu[$i]['weekday'] . '" value="Y" checked="checked" /></td>
		</tr>';
	} else {
		echo '
			<td class="showDay"><input type="checkbox" name="show' . $dailyMenu[$i]['weekday'] . '" value="Y" /></td>
		</tr>';	
	}
}
echo '</table>
	<p class="submit"><input type="submit" name="submit" id="submit" value="  Submit!  " /></p>
	</form>';
	

//We have to do some post processing here
if(isset($_POST['submit']))
{
	//Get all our post variables
	/*for($day=0; $day<$dayCount; $day++)
	{
		$curDay = $_POST['weekday'];
		$curDayLunch = $curDay.'Lunch';
		$curDayDinner = $curDay.'Dinner';
		$newMenu[$day]['weekday'] = $curDay;
		$newMenu[$day]['lunch'] = $_POST[$curDayLunch];
		$newMenu[$day]['dinner'] = $_POST[$curDayDinner];
		echo "Pre-Vars: ".$curDay." - ".$curDayLunch." - ".$curDayDinner."\n";
		echo 'Vars: '.$newMenu[$day]['weekday'].' & '.$newMenu[$day]['lunch'].' & '.$newMenu[$day]['dinner'];
	}*/
	
	for($day=0; $day<$dayCount; $day++)
	{
		$curDay = $dailyMenu[$day]['weekday'];
		$curDayLunch = $curDay.'Lunch';
		$curDayDinner = $curDay.'Dinner';
		$showCurDay = 'show'.$curDay;
		if(isset($_POST[$showCurDay]))
		{
			$showDay = 'Y';
		} else {
			$showDay = 'N';
		}
		$dailyMenu[$day]['lunch'] = addslashes(nl2br($_POST[$curDayLunch]));
		$dailyMenu[$day]['dinner'] = addslashes(nl2br($_POST[$curDayDinner]));
		$dailyMenu[$day]['showDay'] = $showDay;
	}
	
	//Now save it to the MySQL database
	for($day=0; $day<$dayCount; $day++)
	{
		//Setup our query
		$setMenuQ = '
			UPDATE mealMenu
			SET lunch="'.$dailyMenu[$day]['lunch'].'", dinner="'.$dailyMenu[$day]['dinner'].'", showDay="'.$dailyMenu[$day]['showDay'].'"
			WHERE weekday="'.$dailyMenu[$day]['weekday'].'"';
		//and execute said query
		$doModification = mysqli_query($mysqli, $setMenuQ);
	}
	//Should be done
	//refresh the page
	echo '<body onload="javascript:timedRefresh(500);">';
}


// function to parse the http auth header
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php");
?>