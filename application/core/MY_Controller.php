<?php
class MY_Controller extends CI_Controller
{
	protected $logged_in_user;
	protected $is_logged_in = FALSE;
	public function __construct()
	{
		parent::__construct();
		$this->logged_in_user = $this->authenticate->get_user();
		
		if($this->logged_in_user->is_banned)
		{
			$this->log_out();
			App\Activity\Access::blocked_url();
		}
			
		
		if($this->logged_in_user->id)
		{
			$this->is_logged_in = TRUE;
		}
	}
	
	public function log_out()
	{
		$this->authenticate->log_out();
		$this->logged_in_user = $this->authenticate->get_user();
		$this->is_logged_in = FALSE;
	}
} 
?>