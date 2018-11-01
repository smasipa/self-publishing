<?php
if(!function_exists('is_logged_in'))
{
	if(isset($_COOKIE['email']))
	{
		return TRUE;
	}
}
?>