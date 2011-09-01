<?php

	$authUsers = array('brother');
	include_once('../php/authenticate.php');

//include GD rendering class
require_once('classes_GDRenderer.php');

//create new GD renderer, optinal parameters: LevelSeparation,  SiblingSeparation, SubtreeSeparation, defaultNodeWidth, defaultNodeHeight
$objTree = new GDRenderer(30, 10, 30, 50, 20);


$mysqli = mysqli_connect($db_host, $db_username, $db_password, $db_database);
		
function tree($mysqli, $parent, $parentIndex, &$index, $objTree){
	
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
		$members[$memberCount]['treeChild'] = $userDataArray['treeChild'];
		$members[$memberCount]['treeParent'] = $userDataArray['treeChild'];
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
		$members[$memberCount]['treeChild'] = $userDataArray['treeChild'];
		$members[$memberCount]['treeParent'] = $userDataArray['treeChild'];
		$memberCount++;
	}
	
	for($i=0; $i < $memberCount; $i++){
		
		$img = "../photos/composite/";
		if(file_exists("../photos/composite/".$members[$i]['username'].".jpg"))
		{
			$img .= $members[$i]['username'].".jpg";
		}
		else
		{
			$img .= "mystery.png";
		}
		
		$str = $members[$i]['firstName']." ".$members[$i]['lastName'];
		
		$objTree->add($index,$parentIndex,$str, 135, 210, $img);
		$index++;
					
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
			tree($mysqli, $members[$i]['username'], $index-1, $index, $objTree);
		}
		
	}
}

$index=1;
tree($mysqli, "ROOT", 0, $index, $objTree);

$objTree->stream();

?>