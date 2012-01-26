<?php
	$authUsers = array('admin', 'brother');
	include_once('/php/authenticate.php');
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Media Server Setup</title>
<style type="text/css">
	body 
		{
		background-image:url('background.jpg');
		}
	.thing
		{
		position: relative;
		width: 600px;
		background-color:#ffffff;
		border:1px solid black;
		/* for IE */
		filter:alpha(opacity=90);
		/* CSS3 standard */
		opacity:0.9;
		margin: 0px auto;
		padding: 10px;
		}
	#votePony
		{
		position: absolute;
		top: 350px;
		left: 50px;
		}
	#bbq
		{
		position: absolute;
		top: 600px;
		left: 100px;
		}
	
</style>
</head>
<body>
<br /><br />
<div style="position: relative; margin-right: auto; margin-left: auto;"><img src="banner.gif" alt="[ You Won! ]" /></div>
<br /><br />
<marquee><img src="skeleton.gif" alt="[ moonwalk ]" /></marquee>
<br /><br />


<div class="thing">
<h2>Connecting to the Media Server</h2>
<p>Please keep in mind the purpose of this media server is to limit the amount of downloading/streaming from the web by providing it on our local network.  All 60+ people in the house share a 9Mb pipe, so these bandwidth intensive activities can have a big impact on the house's network preformace.  Please used these media shares whenever possible.</p>
<h3>Follow the following steps to connect to the TV and Movies servers</h3>
<p style="color: red;">Note: You will only be able to access the media shares while you are on the GammaTau network.  This will not work outside of the house.</p>
<p style="background-color: black; color: #FFCC33;">Note: By default the share is read-only.  If you would like to be an uploader, contact me and I'll grant you access.</p>
<h3>For Windows:</h3>
<ol>
	<li>Click Start->Run and type in \\10.0.0.1\</li>
	<li>When asked for credentials, users of Vista/7 will need to click "Login using Different Credientials".<br />
	The username is: KANSASDELTS\mediaaccess<br />
	The password is: DTD2006GT<br />
	Check the "Reconnect at Login" box to always have the drive accessible</li>
	<li>In the Windows Explorer window that is now visible, you should see two folders, tv-share and movie-share.  These house the file shares.  You can map these folders to your computer by right clicking the folder and clicking the "Map Network Drive" option.  This will create a shorcut to this location which can be found when browsing "My Computer".</li>
</ol>

<h3>For Macs:</h3>
<p>Follow the guide on <a href="https://engineering.purdue.edu/ECN/Support/KB/Docs/MacOSXConnectingToSMB" title="[ Mac Samba Share Setup ]">this website</a>, noting the changes below:</p>
<ul>
	<li>The path to the Delt Movie share is: smb://10.0.0.1/movie-server</li>
	<li>The Workgroup/Domain is: KANSASDELTS</li>
	<li>The username is: mediaaccess</li>
	<li>The password is: DTD2006GT</li>
</ul>
<p>Repeat this process to gain access to the TV share using this path instead: smb://10.0.0.1/tv-server</p>

</div>

<div id="votePony"><img src="vote_pony.gif" alt="[ vote pony ]" /></div>
<div id="bbq"> <img src="bbq.gif" alt="[ bbq ]" /></div>

</body>
</html>
