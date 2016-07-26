<?php

// require lodash here

class Lodash extends __{

    public static function groupBy($data,$key){
        $result = array();
        foreach ($data as $row) {
            $result[$row[$key]][] = $row;
        }
        return $result;
    }

    // filter aray value by keys
	public static function pull($arr,$keys){
		$result = [];
		foreach ($keys as $key) {
			if(isset($arr[$key])){
				$result[$key] = $arr[$key];
			}else{
				$result[$key] = null;
			}
		}
		return $result;
	}

	// create array from array with key is $key and value is array of value has key is $valueKey
	public static function keysValues($arr,$key,$valueKey){
		$result = [];

		foreach ($arr as $row) {
			$result[$row[$key]][] = $row[$valueKey];
		}

		return $result;
	}
}