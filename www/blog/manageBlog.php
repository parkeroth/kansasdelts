<?php
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/login.php');
	$authUsers = array('admin', 'publicRel', 'pres');
	include_once($_SERVER['DOCUMENT_ROOT'].'/php/authenticate.php');
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	//get all the old entries
	$getEntryQ = '
		SELECT *
		FROM blogContent
		ORDER BY id
	';
	$getEntry = mysqli_query($mysqli, $getEntryQ);
	$entryCount = 0;
	while($blogData = mysqli_fetch_array($getEntry, MYSQLI_ASSOC))
	{
		$entryData[$entryCount]['header'] = stripslashes($blogData['header']);
		$entryData[$entryCount]['content'] = html_entity_decode($blogData['content']);
		$entryData[$entryCount]['category'] = stripslashes($blogData['category']);
		$entryData[$entryCount]['date'] = $blogData['date'];
		$entryData[$entryCount]['id'] = $blogData['id'];
		$entryData[$entryCount]['submitter'] = $blogData['submitter'];
		$entryCount++;
	}
	
	
	include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php"); 
?>
<style type="text/css">
	#postContent {
		width: 550px;
		margin-left:auto;
		margin-right:auto;
	}
	.contentArea {
		width: 450px;
		font-size:14px;
		text-align: center;
	}
	.editCrap {
		width: 150px;
		font-size:14px;
		text-align: center;
	}
	.formLabel {
		font-size:18px;
	}
	.editButton {
		width: 100px;
		margin-left: auto;
		margin-right: auto;
	}
</style>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php"); ?>

<h1>Manage Blog</h1>

<h2>Add Entry</h2>
<a href="addEntry.php" title="[ Add Blog Entry ]" style="font-size:16px;">Click Here To Add Blog Entry</a> 

<h2>Previous Entries</h2>
<div id="postContent">
<table border="1">
	<tr>
    	<th class="contentArea">
        	Content
        </th>
        <th class="editCrap">
        	Changes
        </th>
    </tr>
    
<?php 
	
	//here's where we loop over our data
	for($i=0;$i<$entryCount;$i++)
	{
		$editURL = 'editEntry.php?blogID='.$entryData[$i]['id'];
		$deleteURL = 'deleteEntry.php?blogID='.$entryData[$i]['id'];
		echo '
		<tr>
			<td>';
		//now here's were the content goes
		echo '
			<h3>
				'.$entryData[$i]['header'].'
			</h3>
			<span style="text-align:right; font-style:italic;">
				'.$entryData[$i]['date'].'
			</span><br />
			'.$entryData[$i]['content'].'
			';
	
		echo '
			</td>
			<td> 
				<div style="margin-left: auto; margin-right: auto;">
				<input type="button" name="'.$entryData[$i]['id'].'" value="Edit Entry" class="editButton" onclick="javascript: window.location.href=\''.$editURL.'\'" /><br /><br />
				<input type="button" name="'.$entryData[$i]['id']."\" value=\"Delete Entry\" class=\"editButton\" onclick=\"javascript: window.location.href='$deleteURL'\" />
				</div>
			</td>
		</tr>
		";
	}
?>
</table>
</div>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>