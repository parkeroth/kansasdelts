<?php

session_start();
$authUsers = array('admin');
include_once('php/authenticate.php');
include_once('php/login.php');

$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);

/**
 * Processing Section
 */

if($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$allowed_filetypes = array('.jpg', '.png', '.gif', '.tiff', '.pdf'); // These will be the types of file that will pass the validation.
	$max_filesize = 10000000; // Maximum filesize in BYTES (currently 0.5MB).
	$upload_path = 'proofs/'; // The place the files will be uploaded to (currently a 'files' directory).
	 
	$filename = $_FILES['userfile']['name']; // Get the name of the file (including file extension).
	
	$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
	
	// Check if the filetype is allowed, if not DIE and inform the user.
	if(!in_array($ext,$allowed_filetypes))
	  die('The file you attempted to upload is not allowed.');
	
	// Now check the filesize, if it is too large then DIE and inform the user.
	if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
	  die('The file you attempted to upload is too large.');
	
	// Check if we can upload to the specified path, if not DIE and inform the user.
	if(!is_writable($upload_path))
	  die('You cannot upload to the specified directory, please CHMOD it to 777.');
	  
	$target_path = $target_path . basename($_FILES['uploadedfile']['name']);
	  
	$deadline = date("Y-m-d",strtotime($_POST[deadline]));
	  
	
	// Insert order into database
	$query = "INSERT INTO apparelOrders (name, description, status, proofExt, dueDate) 
				VALUES ('$_POST[name]', '$_POST[description]', 'concept', '$ext', '$deadline')";
	
	$mysqli->query($query);

	$uploadName = $mysqli->insert_id . $ext;
	
	
	
	// Upload the file to your specified path.
	if(!move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . $uploadName))
		 echo 'There was an error during the file upload.  Please try again.';
	
	
	
	header("location: manageApparelOrders.php");

} 

include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerFirst.php");?>

<link type="text/css" href="styles/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/headerLast.php");?>

<h2 align="center">New Apparel Order</h2>
    
    <form enctype="multipart/form-data" name="newOrder" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    	<table border="0" cellpadding="4" align="center">
    		<tr>
    			<th>Name: </th>
    			<td><input name="name" type="text" size="40" /></td>
    			</tr>
    		
    		<script type="text/javascript">
				$(function() {
					$("#datepicker").datepicker();
				});
			</script>
    		<tr>
				<th>Proof File: </th>
				<td><input style="color:#fff;" name="userfile" type="file" />
				</tr>
    		<tr>
    			<th>Order Deadline: </th>
    			<td><input name="deadline" type="text" id="datepicker" size="10" /></td>
    			</tr>
    		<tr>
    			<th>Notes: </th>
    			<td><textarea name="description" cols="40" rows="10"></textarea></td>
    			</tr>
    		<tr>
    			<td colspan="2"></td>
    			</tr>
    		</table>
    	<p style="text-align:center">
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			
    		<input type="submit" name="submit" id="submit" value="Submit" />
    		<input type="reset" name="Reset" id="Reset" value="Reset" />
    		</p>
    	
    </form>
	
<?php include_once($_SERVER['DOCUMENT_ROOT']."/includes/footer.php"); ?>