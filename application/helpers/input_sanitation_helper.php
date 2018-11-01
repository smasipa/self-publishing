<?php
namespace App\Utility
{
	class StringMethods
	{
		
		public static function make_slug($value)
		{
			$slug = preg_replace("#[^\w]#", '-', $value);
			$slug = preg_replace("#[-]{2,}#", '-', $slug);
			return preg_replace("#^[-]|[-]$#", '', $slug);
		}
		
		public static function unslug($value)
		{
			return preg_replace("#[-]#", ' ', $value);
		}
		
		public static function is_valid_slug($value)
		{
			return preg_match("#[^\d][a-z0-9?-]#i", $value);
		}

		public static function is_valid_name($value)
		{
			return preg_match("#[^\d][a-z0-9\s?-]#i", $value);
		}
	}
}

namespace App\Utility
{
	class Url
	{
		
		public static function redirect($url)
		{
			header("Location:".$ulr);
			exit();
		}
	}
}
?>