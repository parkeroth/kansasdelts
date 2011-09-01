<?php
	function sortArrayofObjectByProperty( $array, $property )
	{
		$cur = 1;
		$stack[1]['l'] = 0;
		$stack[1]['r'] = count($array)-1;
	
		do
		{
			$l = $stack[$cur]['l'];
			$r = $stack[$cur]['r'];
			$cur--;
	
			do
			{
				$i = $l;
				$j = $r;
				$tmp = $array[(int)( ($l+$r)/2 )];
	
				// split the array in to parts
				// first: objects with "smaller" property $property
				// second: objects with "bigger" property $property
				do
				{
					while( $array[$i]->{$property} < $tmp->{$property} ) $i++;
					while( $tmp->{$property} < $array[$j]->{$property} ) $j--;
	
					// Swap elements of two parts if necesary
					if( $i <= $j)
					{
						$w = $array[$i];
						$array[$i] = $array[$j];
						$array[$j] = $w;
	
						$i++;
						$j--;
					}
	
				} while ( $i <= $j );
	
				if( $i < $r ) {
					$cur++;
					$stack[$cur]['l'] = $i;
					$stack[$cur]['r'] = $r;
				}
				$r = $j;
	
			} while ( $l < $r );
	
		} while ( $cur != 0 );
	
		return $array;
	
	}
	
	function formatPhone($num) 
	{ 
		$num = ereg_replace('[^0-9]', '', $num); 
	
		$len = strlen($num); 
		if($len == 7) 
			$num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1-$2', $num); 
		elseif($len == 10) 
			$num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $num); 
	
		return $num; 
	} 
	
	function strip_phone($num)
	{
		return preg_replace('/\D/', '', $num);
	}
	
	function make_null($value){
		if($value == NULL){
			return 'NULL';
		} else {
			return "'".$value."'";
		}
	}
	
?>