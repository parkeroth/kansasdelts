<?php 

	function filesInDir($dir)
	{
        // Open a known directory, and proceed to read its contents
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				
				//Set first tag
				$first = true;
				
				// For each file in directory
				while (($file = readdir($dh)) !== false) {
					
					// Check if it's a real file
					if($file != '.' && $file != '..'){
						
						// Unset first tag
						if($first) { 
							$first = false; 
							
							echo "<tr><th>File Name</th><th></th></tr>";
						}
						
						$downloadLink = substr($dir,9).$file;
						$removeLink = "javascript:myfunct('$dir$file');";
						
						echo "<tr>";
						echo "<td>$file</td>";
						echo "<td><a href=\"$removeLink\">remove</a> | <a href=\"$downloadLink\">download</a></td>";
						echo "</tr>\n";
						
					}
				}
				closedir($dh);
				
				// If first is still set...
				if($first){
					echo "<tr><td colspan=\"2\">No files have been uploaded!</td></tr>";
				}
			}
		} else {
			
			// Create the directory
			mkdir($dir);
			
			echo "<tr><td colspan=\"2\">No files have been uploaded!</td></tr>";
		}
	}
	
	function cleanInput($str)
	{
		return str_replace(' ','-',$str);
	}

	$authUsers = array('brother');
	include_once('php/authenticate.php');
	
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	$ID = $_GET[ID];
	
	$completedQuery = "SELECT section, document FROM reportingTasks WHERE ID = '$ID'";
					
	$result = mysqli_query($mysqli, $completedQuery);
	$row = mysqli_fetch_object($result);
		
	$section = cleanInput($row->section);
	$document = cleanInput($row->document);
	
	$dir = "/var/www/reporting/$document/$section/"; ?>
	
<div id="fileList">
	
	<table class="fileList" width="320">
		
		<?php filesInDir($dir); ?>
		
	</table>
	
</div>
