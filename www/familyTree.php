<?php
	$authUsers = array('brother');
	include_once('php/authenticate.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Gamma Tau Pledge Family Trees</title>
<style>

</style>
</head>

<body style="background-color: #2b2b2b; color: #FFF; width: 100%;">

<?php
	$col=0;
	$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
	
	function tree($mysqli, $level, $parent, &$marker, &$col){
		$level++;
		
		$treeLevel = "
			SELECT * 
			FROM members 
			WHERE treeParent like '%".$parent."%'
			ORDER BY lastName";
		$getTreeLevel = mysqli_query($mysqli, $treeLevel);
		
		$memberCount = 0;
		while($userDataArray = mysqli_fetch_array($getTreeLevel, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$members[$memberCount]['standing'] = $userDataArray['standing'];
			$memberCount++;
		}
		
		$treeLevel = "
			SELECT * 
			FROM alumni 
			WHERE treeParent like '%".$parent."%'
			ORDER BY lastName";
		$getTreeLevel = mysqli_query($mysqli, $treeLevel);
		
		while($userDataArray = mysqli_fetch_array($getTreeLevel, MYSQLI_ASSOC))
		{
			$members[$memberCount]['username'] = $userDataArray['username'];
			$members[$memberCount]['firstName'] = $userDataArray['firstName'];
			$members[$memberCount]['lastName'] = $userDataArray['lastName'];
			$members[$memberCount]['standing'] = $userDataArray['standing'];
			$memberCount++;
		}
		
		for($i=0; $i < $memberCount; $i++){
			
			if($marker == 1)
			{
				$marker = 0;
				$temp = $level;
				while($temp > 1){
					echo "<div style=\"height: 210px; text-align: center; margin: 5px;\"></div>";
					$temp--;
				}
			}
			
			echo "<div style=\"height: 210px; text-align: center; margin: 5px;\">\n";
			
			echo "<img src=\"photos/composite/";
			if(file_exists("photos/composite/".$members[$i]['username'].".jpg") && $members[$i][standing] != 'bad')
			{
				echo $members[$i]['username'].".jpg";
			}
			else
			{
				echo "mystery.png";
			}
			echo "\"><br>\n";
			if($members[$i]['standing'] != "bad")
			{
				echo $members[$i]['firstName']." ".$members[$i]['lastName']."\n";
			} else {
				echo "Joe Delt\n";
			}
			echo "</div>\n";
			
			$childrenCount = 0;
			
			$checkChildren = "SELECT treeParent from members WHERE treeParent like '%".$members[$i]['username']."%'";
			$getChildren = mysqli_query($mysqli, $checkChildren);
			while($childrenArray = mysqli_fetch_array($getChildren, MYSQLI_ASSOC))
			{
				$childrenCount++;
			}
			
			$checkChildren = "SELECT treeParent from alumni WHERE treeParent like '%".$members[$i]['username']."%'";
			$getChildren = mysqli_query($mysqli, $checkChildren);
			while($childrenArray = mysqli_fetch_array($getChildren, MYSQLI_ASSOC))
			{
				$childrenCount++;
			}
			
			if($childrenCount > 0)
			{
				tree($mysqli, $level, $members[$i]['username'], &$marker, $col);
			}
			else
			{
				$col++;
				echo "</div><div style=\"position: absolute; width: 135px; top: 0px; left: ". $col*135 ."px;\">\n";
				$marker = 1;
			}
			
		}
		$level--;
		
	}
	
	$initial = 0;
	echo "<div style=\"position: absolute; width: 135px; top: 0px; left: ". $col*135 ."px;\">\n";
	tree($mysqli, 0, "ROOT", $initial, $col);
	echo "</div>\n";
	
	?>
</body>
</html>