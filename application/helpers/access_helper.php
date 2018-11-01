<?php
namespace App\Activity
{
	class Access{
		private static $controller = 'access';
		public static function login()
		{
			redirect(Self::$controller.'/'.ACCESS_LOGIN);
		}
		
		public static function go_premium()
		{
			redirect(Self::$controller.'/'.ACCESS_PREMIUM_MEMBER);
		}
		
		// Serious writers must apply to get approved
		public static function get_approved()
		{
			redirect(Self::$controller.'/'.ACCESS_GET_APPROVED);
		}
		
		// Applies to pdf documents
		public static function file_not_found()
		{
			redirect(Self::$controller.'/'.ACCESS_FILE_NOT_FOUND);
		}	
		
		// Applies to empty results search
		public static function nothing_found()
		{
			redirect('/nothing_found');
		}
		
		// Deleted or blocked urls/items
		public static function blocked_url()
		{
			redirect(Self::$controller.'/'.ACCESS_BLOCKED_ITEM);
		}		
		
		// Applies to pdf documents
		public static function show_404()
		{
			redirect(Self::$controller.'/'.ACCESS_PAGE_NOT_FOUND);
		}
		
		public static function buy()
		{
			
		}
		
		
	}
}
?>