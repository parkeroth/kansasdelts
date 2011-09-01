<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Filter
{
	private $field = NULL;
	private $compare_operator = NULL;
	private $logic_operator = NULL;

	
}

function build_where_item($field, $values, $equal, $operator){
	$item  = '( ';
	if(!isset($field))
		return -1;
	if(!isset($values))
		return -1;

	if(is_array($values)){
		$first = true;
		if(!isset($equal))
			return -1;
		if(!isset($operator))
			return -1;
		$operator = strtoupper($operator);

		foreach($values as $value){
			if(!$first){
				$item .= $operator.' ';
				$first = false;
			}
			if($equal){
				$item .= "$field='$value' ";
			} else {
				$item .= "$field.='$value'";
			}
			$first = false;
		}
	} else {
		$item = "$field='$value' ";
	}

	$item .= ')';
	return $item;
}
?>
