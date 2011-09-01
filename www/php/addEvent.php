<?php
session_start();
$authUsers = array('admin', 'communityService', 'houseManager', 'brotherhood', 'social', 'secretary', 'recruitment', 'pledgeEd', 'homecoming', 'publicRel', 'drm', 'philanthropy');
include_once('authenticate.php');
	
include_once('login.php');
$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

$hour = $_POST['hour'];
$minute = $_POST['minute'];
$ampm = $_POST['ampm'];

$day = $_POST[day];
$month = $_POST[month];
$year = $_POST[year];

$time = $hour.":".$minute." ".$ampm;
$date = $year."-".$month."-".$day;
		
if($month >= 1 && $month <= 7){
	$term = "spring".$year;
} else if($month >= 8 && $month <= 12){
	$term = "fall".$year;
}

if($_POST[type] == "communityService")
{
	$subjectType = "Community Service";
}
else if($_POST[type] == "house")
{
	$subjectType = "House";
}
else if($_POST[type] == "brotherhood")
{
	$subjectType = "Brotherhood";
}
else if($_POST[type] == "homecoming")
{
	$subjectType = "Homecoming";
}
else if($_POST[type] == "pr")
{
	$subjectType = "Public Relations";
}
else
{
	$subjectType = "";
}

$userData = "
	SELECT * 
	FROM members
	ORDER BY lastName";
$getUserData = mysqli_query($mysqli, $userData);
	
$memberCount = 0;
while($userDataArray = mysqli_fetch_array($getUserData, MYSQLI_ASSOC))
{
	$members[$memberCount]['username'] = $userDataArray['username'];
	$members[$memberCount]['notifyNewEvent'] = $userDataArray['notifyNewEvent'];
	$members[$memberCount]['email'] = $userDataArray['email'];
	$members[$memberCount]['phone'] = $userDataArray['phone'];
	$members[$memberCount]['carrier'] = $userDataArray['carrier'];
	$members[$memberCount]['firstName'] = $userDataArray['firstName'];
	$members[$memberCount]['lastName'] = $userDataArray['lastName'];
	$memberCount++;
}

/*$invited = $_POST['invited'];
$n = count($invited);

for($i=0; $i< $n; $i++){
	$string = "|".$invited[$i];
	$str = $str.$string;
}*/

$invited = $_POST['invited'];

for($i=0; $i< count($invited); $i++){
	$invitedUsers[] = $invited[$i];
}

/*$pledgeClass = $_POST['classes'];
$m = count($pledgeClass);

for($i=0; $i< $m; $i++){
	$query = "SELECT username FROM members WHERE class = '$pledgeClass[$i]'";
	$result = mysqli_query($mysqli, $query);
	while($classData = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		echo $classData[username]."<br>";
		if(strpos($str, $classData[username]) === false)
		{
			$string = "|".$classData[username];
			$str = $str.$string;
			$n++;
		}
	}
	
}*/

$pledgeClass = $_POST['classes'];

for($i=0; $i< count($pledgeClass); $i++){
	$query = "SELECT username FROM members WHERE class = '".$pledgeClass[$i]."'";
	$result = mysqli_query($mysqli, $query);
	while($classData = mysqli_fetch_array($result, MYSQLI_ASSOC))
	{
		echo $classData[username]."<br>";
		if(!in_array($classData[username],$invitedUsers))
		{
			$invitedUsers[] = $classData[username];
		}
	}
}

if($_POST[notify] == "yes"){
	require '../php/class.phpmailer.php';

	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
	
		$body =  "<html><head></head><body>";
		$body .= "<table>";
		$body .= "<tr><th>Title:</th><td>$_POST[title]</td></tr>";
		$body .= "<tr><th>Description:</th><td>$_POST[description]</td></tr>";
		$body .= "<tr><th>Type:</th><td>$subjectType</td></tr>";
		$body .= "<tr><th>Event Date:</th><td>$date</td></tr>";
		$body .= "<tr><th>Event Time:</th><td>$time</td></tr>";
		if(isset($_POST[maxAttendance])){
			$body .= "<tr><th>People Needed:</th><td>$_POST[maxAttendance]</td></tr>";
		}
		$body .= "</table>";
		$body .= "<p><a href=\"http://kansasdelts.org/loginForm.php\">RSVP</a></p>";
		
			
		$mail->IsSMTP();                           // tell the class to use SMTP
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Port       = 25;                    // set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; // SMTP server
		$mail->Username   = "dtdadmin@kansasdelts.org";     // SMTP server username
		$mail->Password   = "DTD1856GammaTau";            // SMTP server password
	
		$mail->IsSendmail();  // tell the class to use Sendmail
	
		$mail->AddReplyTo("dtdadmin@kansasdelts.org","DTD Webmaster");
	
		$mail->From       = "dtdadmin@kansasdelts.org";
		$mail->FromName   = "DTD Webmaster";
		
		for($i=0; $i< $n; $i++){
			
			for($j = 0; $j < $memberCount; $j++){
				
				if($invited[$i] == $members[$j]['username']){
					if(strpos($members[$j]['notifyNewEvent'], "email") > -1)
					{
						$mail->AddBCC($members[$j]['email'], $members[$j]['firstName']." ".$members[$j]['lastName']);
					}
					if(strpos($members[$j]['notifyNewEvent'], "text") > -1 && $members[$j][carrier] != "none")
					{
						$address = str_replace('-', "", $members[$j]['phone']);
						if($members[$j]['carrier'] == "verizon"){
							$address .= "@vtext.com";
						} else if($members[$j]['carrier'] == "sprint"){
							$address .= "@messaging.sprintpcs.com";
						} else if($members[$j]['carrier'] == "tobile"){
							$address .= "@tmomail.net";
						} else if($members[$j]['carrier'] == "att"){
							$address .= "@txt.att.net";
						}
						
						$mail->AddBCC($address, $members[$j]['firstName']." ".$members[$j]['lastName']);
					}
				}
			}
		}
	
		$mail->Subject  = "New ".$subjectType." Event";
	
		$mail->AltBody    = "Title: ".$_POST[title]; // optional, comment out and test
		$mail->WordWrap   = 80; // set word wrap
	
		$mail->MsgHTML($body);
	
		$mail->IsHTML(true); // send as HTML
	
		$mail->Send();
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
	}
}

echo $to;

if(isset($_POST[mandatory]))
{
	$mandatory = $_POST[mandatory];
	if($mandatory){
		$status = 'attending';
	} else {
		$status = 'invited';
	}
}
else
{
	$mandatory = 0;	
	$status = 'invited';
}

if(isset($_POST[maxAttendance]))
{
	$maxAttendance = $_POST[maxAttendance];
}
else
{
	$maxAttendance = -1;	
}

echo $add_sql;

$add_sql = "INSERT INTO events (title, description, dateAdded, eventDate, term, time, type, mandatory, maxAttendance) VALUES ('$_POST[title]', '$_POST[description]', '$_POST[dateAdded]', '$date', '$term', '$time', '$_POST[type]', '$mandatory', '$maxAttendance')";

$add_res = mysqli_query($mysqli, $add_sql);

$eventID = mysqli_insert_id($mysqli);

if($_POST[type] == "social")
{
	$query = "INSERT INTO soberGentEvents (eventID, title, eventDate, numberOfGents) VALUES ('$eventID', '$_POST[title]', '$date', '0')";
	$result =  mysqli_query($mysqli, $query);
}

foreach($invitedUsers as $value){
	echo $value."<br>";
	$query = "	INSERT INTO eventAttendance (eventID, username, status) 
				VALUES ('$eventID', '$value', '$status')";
	$result =  mysqli_query($mysqli, $query);
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Calendar - Add Event</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language='javascript' type="text/javascript">

                     function redirect_to(where, closewin)
                     {
                             opener.location= '../calendar.php?month=<? echo $_POST['month']+1 . "&year=".$_POST['year']; ?>';
                             
                             if (closewin == 1)
                             {
                                     self.close();
                             }
                     }
                      
</script>
</head>
<body onLoad="javascript:redirect_to('month=<? echo $_POST['month']."&year=".$_POST['year']; ?>',1);">
</body>
</html>