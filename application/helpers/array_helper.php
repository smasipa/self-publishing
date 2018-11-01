<?php
namespace App\Helper{
	class Array_Methods{
		public static function flatten($array, $return = array())
		{
			if(is_array($array)){
				foreach($array as $key => $value)
				{
					if(is_array($value))
					{
						$return = Self::flatten($value, $return);
					}
					else
					{
						$return[] = $value;
					}
				}
			}
			return $return;
		}
		
		public static function objects_to_array($objects)
		{
			$ret = array();
			if(is_array($objects))
			{
				foreach($objects as $obj)
				{
					$ret[] = (array) $obj;
				}	
			}
			return $ret;
		}
		
		public static function to_object($array)
		{
			$result = new \stdClass();
			foreach ($array as $key => $value)
			{
				if (is_array($value))
				{
					$result->{$key} = self::to_object($value);
				}
				else
				{
					$result->{$key} = $value;
				}
			}
			return $result;
		}
		
		// Fills empty values in new array with old values from old array
		public static function fill_with($new_data, $old_data)
		{
			foreach($old_data as $key => $value)
			{
				if(array_key_exists($key, $new_data) && !$new_data[$key])
				{
					$new_data[$key] = $value;
				}
			}
			return $new_data;
		}
	}	
}

?>