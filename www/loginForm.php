<?php
	ob_start(); 
	include_once($_SERVER['DOCUMENT_ROOT'].'/loginSystem/session.php');
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");
?>

<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript">

	$(function() {
		
		document.getElementById('username').focus();
		
	});
	
</script>

<style type="text/css">
	.loginMessage {
		text-align: center;
		width: 300px;
		border-bottom-style: groove;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<div align="center">
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<p><strong>Username:</strong><br />
			<input id="username" type="text" name="username" <?php if(isset($_GET[user])){ echo "value=\"".$_GET[user]."\""; } ?> /></p>
		<p><strong>Password:</strong><br />
			<input type="password" name="password" /></p>
        <p><strong>Remember Me: </strong>
        	<input type="checkbox" name="remember" checked="checked" /></p>    
        <input type="hidden" name="referrer" value="<?php echo $session->referrer; ?>" />
		<p><input type="submit" name="submit" value="login" /></p>
		</form>
</div>
		<p>&nbsp;</p>
        
<?php
	//lets look into success or error messages of login
	//after we process the login data
	if(isset($_POST['submit']))
	{
		//echo "submitted<br />";
		$postName = htmlspecialchars($_POST['username']);
		$postPass = htmlspecialchars($_POST['password']);
		$postReferrer = addslashes($_POST['referrer']);
		//echo "post vars: ".$postName." ".$postPass." ".isset($_POST['remember'])."<br />";
		$loginErrors = $session->login($postName, $postPass, isset($_POST['remember']));
		//echo $loginErrors;
		if($loginErrors == "")
		{
			//echo "Referrer: ".$session->referrer;
			//echo "<br />URL: ".$session->url;
			echo '<p class="loginMessage">
				<span style="font-size: 16px; color: #00CC33 !important;">Login Success!</span><br />
				You\'ll be redirected in about 3 secs. If not, click <a href="'.stripslashes($postReferrer).'">here</a>.</p>';
			header(stripslashes($postReferrer));
		} else {
			//we have errors associated with the login, we need to echo that stuff out to 
			//the user so he can fix it
			echo '<p class="loginMessage"><span style="font-size: 16px; color: red !important;">Login Error(s):</span><br />'.$loginErrors.'</p>';
		}
	}
	
?>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>