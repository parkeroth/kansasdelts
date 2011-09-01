<?php


include("Tree.inc.php");

/*
$ddata=array("testkey"=>"testcontent");

$null=null;
$tree = new Tree($ddata,$null);
$node=&$tree->addChild($ddata);
$tmp=&$tree->addChild($ddata);
$tmp->delete();
$tmp=&$tree->addChild($ddata);
$cnode=&$node->addChild($ddata);
			echo "<pre>";
			print_r($tree->getChildrenIds());
			echo "</pre>";
*/
     //create a root node by passing a variable with value null,


//returns a reference to the root node

  $null = null;

// generate a new tree, 
// the root elements are identified by null value 
// there is no data (second parameter)
    $tree = new Tree($null);
// another possibility is to hand over a reference to a parent


// always use references (&)! 
     $node = &$tree->addChild(array("key0"=>"Home"));

  // create another node as subnode of $node
    $subnode = &$node->addChild(array("key0"=>"Home sub1"));
    $subnode = &$node->addChild(array("key1"=>"Home sub2"));

  // create another node as subnode of root node 
    $node = &$tree->addChild(array("key1"=>"Work"));
    $subnode = &$node->addChild(array("key0"=>"Work sub1"));
    $subnode = &$node->addChild(array("key1"=>"Work sub2"));


  		$dummydata = array("dummykey"=>"dummyvalue");
    	// data load may be anything, here we use the dummydata array 
    	$subsubnode = &$subnode->addChild($dummydata);
    	// some information about the node:
    	echo 'the added node has the ID: '.$subsubnode->getId().'<br>';
    	
    	// lets print the level :
    	echo ' created node is on level: '.$subsubnode->getLevel().'<br>';

		// path 
		echo 'path to it from root node is (Array of ids): ';
		print_r($subsubnode->getPath());
		echo '<br>';

    	//get the  parent
		$parent=&$subsubnode->_parent;

	// get the root node (reference!) 
		$rootnode=&$subnode->getRoot();

    // have a look on the structure
echo '<h1>Tree structure</h1>';
echo '<pre>';
    $rootnode->echoStructure();
echo '</pre>';

echo 'delete a node (and child nodes!), path to it from root node is: ';
print_r($subnode->getPath());

		$subnode->delete();

echo 'the new structure: ';
echo '<pre>';
    $rootnode->echoStructure();
echo '</pre>';

?>
