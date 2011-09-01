<?php

	/**
	* @package Tree
	* @todo move function to move a node elsewhere in the tree? How to denote locations (paths)?
	*/
	
	/**
	* PHP4 Tree class
	*
	*<code>
	* //create a root node by passing a variable with value null, 
	* //returns a reference to the root node
	*$null = null;
	*$data = array("dummykey"=>"dummyvalue");
	*$tree = new Tree($null);
	*
	* // you can add your data load also:
	* //$tree = new Tree($null,$data);
	*
	* // create a node, $node is a reference to a Tree object, use '&'!
	*$node = &$tree->addChild($data);
	* // create another node as subnode of $node
	*$subnode = &$node->addChild($data);
	*
	* // get the level of this node:
	*echo 'subnode is on level'.$subnode->getLevel();
	*
	* // have a look on the structure
	*echo '<pre>';
	*$tree->echoStructure();
	*echo '</pre>';
	*
	* // delete a node
	*$subnode->delete();
	*
	*</code>
	*
	* @author Martin Weis <tree@datenroulette.de>
   * @license http://opensource.org/licenses/gpl-license.php GNU Public License
   * @version early release, version 0.5
   * @copyright Copyright 2005, Martin Weis	
   */	
class Tree {
	/**
	* @package Tree
	* @todo move function to move a node elsewhere in the tree?
	*/
		
	/**
	* Variable declaration
	*/
	
	/**
	* ID of this Node
	* @var int 
	* @access private
	*/
	var $_id;
	
	/**
	* level of this Node
	* @var int 
	* @access private
	* @deprecated use function {@link getLevel()}
	*/
	var $_level;

	/**
	* parent node (reference)
	* @var Tree 
	* @access private
	*/
	var $_parent;

	/**
	* array of children, with references
	* @var array 
	* @access private
	*/
	var $_children;

	/**
	* data load of this node
	* @access private
	*/
	var $data;

	/**
	* default constructor
	* 
	* creates a node
	* @access public
	* @param &Tree or null, set parent or null for root node  
	* @param mixed data load
	* @return Tree
	*/
	   function Tree(&$_parent,$data=null) {
	   	
	   	if ($_parent===null){
	   		// this is a root node
	   		$this->_level=0;
	   	}
	   	$this->data=$data;
			$this->_children=array(); //initialize with no children			
			// for the root node parent will be null 
			$this->_parent=&$_parent;
			//$this->_id=$id_to_set;
			/*
			echo "created node with id:".$this->_id." and _parent:".$_parent->id;
			echo "children:";
			echo "<pre>";
			print_r($this->_children);
			echo "</pre>";
			echo "data: ".$this->data;
			*/
	}
	
	/**
	* add a child
	* @access public
	* @param mixed data load
	* @return Tree reference to the child
	*/
	function &addChild($data) {
		
		$this->_children[]=&new Tree(&$this,$data);
		//get the automatically set id (=array key) - it is the last element now
		end($this->_children);
		$key=key($this->_children);
		$this->_children[$key]->_setId($key);
		$this->_children[$key]->level = $this->_level + 1;
			/*
			echo "\n<br>added child with id ".$key;
			echo "children:";
			echo "<pre>";
			print_r($this->_children);
			echo "</pre>";
			*/
		return $this->_children[$key];
	}
	
	
	/**
	* removes a child
	* @param int id
	* @return boolean success (true/false)
	*/
	function removeChild($id) {
		if (!array_key_exists($id,$this->_children)){
		//echo "removal of child with key ".$id." failed: does not exist";
		return false;
		}
		else{
		unset($this->_children[$id]);
		return true;
		}
	}

	/**
	* delete this node
	* @return boolean success state
	*/

	function delete(){
		if (!$this->isRoot){
			// remove using the _parent
			return $this->_parent->removeChild($this->_id);
		}
		else {
			// in the root node unset object
			unset ($this); //->_children=array();
			return true;
		}
	}
		
	/**
	* get object id
	* @return int
	*/
	/*	
	function returnChild($id){
		$ret=&$children[$id];
		echo "return child: ".$ret;
		return $ret;
	}
	*/
		
	/**
	* get object id
	* @return int id
	*/
	function getId(){
		return $this->_id;
	}
	
	/**
	* set object id
	* @access private
	* @return void
	*/
	function _setId($id){
		 $this->_id=$id;
	}
	
	/**
	* get number of children
	* @return int number of children	
	*/
	function numChildren(){
		return count($this->_children);
	}

	/**
	* get IDs of the children
	* @return array of IDs
	*/
	function getChildrenIds(){
		return array_keys($this->_children);
	}
	
	/**
	* check if this is the root node
	* @return boolean
	*/
	function isRoot(){
		if ($this->_parent==null){
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	* get root Node
	* @return Tree object reference to root node
	*/
	function &getRoot(){
			$tmp=&$this;
			while (!$tmp->isRoot()){
				// iterate through parents, add IDs to (begin of) array
				$tmp=&$tmp->_parent;
			}
			return $tmp;
		
	}
	
	/**
	* get the 'path' of the root node to this node (IDs)
	* @return array of ancestors IDs
	*/
	function getPath(){
			$idarray=array();
			$tmp=&$this;
			while (!$tmp->isRoot()){
				// iterate through parents, add IDs to (begin of) array
				array_unshift ($idarray, $tmp->_id);
				$tmp=&$tmp->_parent;
			}
			return $idarray;
		
	}
	
	/**
	* get Level
	* @return int level
	*/
	function getLevel(){
			$level=0;
			$tmp=&$this;
			while (!$tmp->isRoot()){
			//echo "adding level for parent, id :".$this->_id;
				$tmp=&$tmp->_parent;
				$level++;
			}
			return $level;
		/*
		if (!$this->isRoot()){
			// iterate through _parents and count			
			
			return $level;
		}
			else{
				return 0;				
		}
		*/
	}
	
	/*
	* print the Tree
	*/
	/*	
	function printTree($prefix='') {
	$prefix.=(string)$this->_id.'.';
	echo "\n<br>";
	echo '<font color="red">';
	if ($this->isRoot()){
		echo "ROOT";
	}
	else{
	echo "I am child node ".$prefix;//$this->getLevel().".".$this->_id;
	}
	echo "\n<br>";
	echo "I am on Level: ".$this->getLevel();
	echo "\n<br>";
	if ($this->numChildren()==0){
		echo "I have NO childs (leaf node)";
	}
	else
	{
		echo "I have ".$this->numChildren()." childs";
			echo ":<br><pre>";
			print_r($this->getChildrenIds());
			echo ":</pre>";
		//echo "Klassentyp child: ".is_object($this->_children).get_class($this->_children);
		echo "data: <pre>";
	}
		echo "\n<br>";
	print_r($this->data);
	echo "</pre>";
	echo "</font>";
	echo "\n<br>";
	
		if ($this->numChildren()>0){
			foreach ($this->_children as $child) {
	    		$child->printTree($prefix);
			}
		}
	}
	*/
	
	/**
	* echo the structure of the subnodes of our tree/node
	* @param string prefix (used for the recursive output)
	* @return void
	*/
	function echoStructure($pre='') {
		for ($i=0;$i<$this->getLevel();$i++){
			$pre.='|';//$this->getLevel();
		}
		// output this node
		/*
		if ($this->numChildren()>0){
			$sig='+';
		}
		else{
			$sig='-';
		}
		*/
		echo $pre."+[".$this->_id.']';
		if (is_array($this->data)){
			foreach ($this->data as $key=>$value) {
		    		 echo '('.$key.'|'.$value.')';
	    	}
    	}
    		echo "\n";
		if ($this->numChildren()>0){
			
			foreach ($this->_children as $child) {
		    	$child->echoStructure($prefix);
			}
		}
	}
	/*
	* 
	*/
/*	function (){
	}
*/	
}

?>
