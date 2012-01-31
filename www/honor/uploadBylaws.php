<?php

$allowed_filetypes = array('.docx'); // These will be the types of file that will pass the validation.
$max_filesize = 10000000; // Maximum filesize in BYTES (currently 0.5MB).
$upload_path = '../docs/'; // The place the files will be uploaded to (currently a 'files' directory).
 
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
  
if($_POST[type] == "bylaws") {
	
	$uploadName = "bylaws".$ext; 
	
} else if($_POST[type] == "rules") {

	$uploadName = "houseRules".$ext; 
	
}

// Upload the file to your specified path.
if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . $uploadName))
	 header("location: ../success.php?page=Bylaws");
  else
	 echo 'There was an error during the file upload.  Please try again.'; // It failed :(.


?>