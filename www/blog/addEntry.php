<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'publicRel', 'pres');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	//Lets do our post processing here
	if(isset($_POST['submit'])){
		//let's clean our input, so none of our inserts are vunerable to a mysql exploit
		$entry = htmlentities(nl2br($_POST['newContent']));
		$header = addslashes($_POST['contentHeader']);
		$category = addslashes($_POST['contentCategory']);
			
		$curTime = date('Y-m-d H:i:s');
		$submittedBy = $_SESSION['username'];
		
		//now lets set up our insert query
		$insertEntryQ = '
			INSERT INTO blogContent
			(header, content, category, date, submitter)
			VALUES("'.$header.'","'.$entry.'","'.$category.'","'.$curTime.'","'.$submittedBy.'")
		';
		$insertEntry = mysqli_query($mysqli, $insertEntryQ);
		if(!$insertEntry)
		{
			//there was an error writing, echo the message out to the user
			$dataErrorMsg = '<p class="dataError">Error: failed to insert into the database.  Insert query failed. Database error.  Probably should look into that.  We love you anyway.<br />
			Table: blogContent<br />
			Error message thrown: '.mysqli_error().'<br />
			Query: '.$insertEntryQ.'</p>';
		}
	
	}
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>
<style type="text/css">
	#postContent {
		width: 550px;
		margin-left:auto;
		margin-right:auto;
	}
	textarea {
        width: 500px;
        height: 200px;
        border: 3px solid #cccccc;
        padding: 5px;
        font-family: Tahoma, sans-serif;
	}
	.formLabel {
		font-size:18px;
	}
	.nicEdit-panelContain {
		color: #000;
	}
</style>

<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Add New Blog Entry</h1>

<?php
	if($dataErrorMsg)
	{
		echo $dataErrorMsg;
	}
?>

<div id="postContent">
	<form id="postContent" name="postContent" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return Confirm();">
    <p><span class="formLabel">Title:<br /></span>
    <input type="text" name="contentHeader" id="contentHeader" style="width:200px;" /></p>
    <p><span class="formLabel">Category:</span><br />
    <select name="contentCategory" style="width:200px;">
        <option value="general" selected="selected">General</option>
        <option value="alumni">Alumni</option>
        <option value="service">Service</option>
        <option value="brotherhood">Brotherhood</option>
    </select></p>
    <p><span class="formLabel">Content:<br /></span>
	<textarea name="newContent"></textarea></p>
    <input name="submit" type="submit" value=" [ Post Content ] " />
</div>


<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>