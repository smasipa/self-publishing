<?php
namespace App\Helper
{
	class String{
		
		public static function price_to_int($price)
		{
			return $price * 100;
		}
		
		public static function price_to_float($price)
		{
			return $price / 100;
		}
		
		public static function nl2br($string)
		{
			return str_replace(array('\r\n', '\r', '\n'), "<br/>", $string);
		}
		
		public static function fix_nl($string)
		{
			$string = str_replace(array('\r\n', '\r', '\n'), "\r\n", $string);
			
			// Multiple spaces
			$string = preg_replace('/[ \t]+/', ' ', $string);
			
			// Multiple lines
			return preg_replace('/(\r|\n|\r\n){2,}/', "<br/><br/>", $string);
		}
		
		public static function fix_string($string)
		{
			$string = str_replace(array('\r\n', '\r', '\n'), "\r\n", $string);
			
			// Multiple spaces
			$string = preg_replace('/[ \t]+/', ' ', $string);
			// Replace multiple new lines with 2 line feeds
			// return preg_replace('/(\r|\n|\r\n){2,}/', "$1", $string);
			return preg_replace('/(\r\n){2,}/', "\r\n\r\n", $string);
		}
		
		public static function ellipsis($string)
		{
			return strlen($string) > 24 ? substr($string, 0, 22).'...' : $string;
		}
		
		public static function get_referer()
		{
			if(isset($_SERVER['HTTPS_REFERER']))
				return $_SERVER['HTTPS_REFERER'];
			
			elseif($_SERVER['HTTP_REFERER'])
				return $_SERVER['HTTP_REFERER'];
				
			else 
				return null;
		}
	}
}
?>