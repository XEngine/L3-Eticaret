<?php 

class PriceRange {
	
	public static function create($array = NULL)
	{
		if ( $array == null ) return;
		
		$range = static::createRange($array);
		if(static::hasLimit($range)){
			$range[static::getLast($range)]['str'] = '2500TL ve üzeri';
		}
		if(static::hasBelowLimit($range)){
			$range[static::getFirst($range)]['str'] = "50TL'den düşük";
		}
		return $range;
	}
	protected static function getFirst($array){
		reset($array);
		$first_key = key($array);
		return $first_key;
	}
	protected static function getLast($array){
		end($array);
		$key = key($array);
		return $key;
	}
	public static function hasBelowLimit($array){
		if(isset($array[0]['values']))
			return true;
		return false;

	}
	public static function hasLimit($array)
	{
		$lastElem = end($array);
		if(!empty($lastElem['values'])){
			return TRUE;
		}
		return FALSE;
	}
	protected static function createRange($array){
		sort($array);
		$ranges = array();
		//Setting range limits.
		//Check if array has 5 digit number.
		$countDigitedNumbers = preg_grep('/\d{5}/',$array);
		if(count($countDigitedNumbers) > 3){
			$rangeLimits = array(0,1000,2500,5000,10000,20000,25000,999999);
		}else{
			$rangeLimits = array(0,50,250,500,1000,2000,2500,999999);
		}

		for($i = 0; $i < count($rangeLimits); $i++){
			if($i == count($rangeLimits)-1){
				break;
			}
			$lowLimit = $rangeLimits[$i];
			$highLimit = $rangeLimits[$i+1];
		
			$ranges[$i]['ranges']['min'] = $lowLimit;
			$ranges[$i]['ranges']['max'] = $highLimit;
			$ranges[$i]['str'] = $lowLimit . ' - ' . $highLimit;
			$ranges[$i]['count'] = 0;
			foreach($array as $perPrice){
				if($perPrice >= $lowLimit && $perPrice < $highLimit){!
					$ranges[$i]['values'][] = $perPrice;
				}
			}
			if(isset($ranges[$i]['values'])){
				$ranges[$i]['count'] = count($ranges[$i]['values']);
			}
		}
		return $ranges;
	}
}