<?php

class Authenticate extends MY_Model{
	
	private $logged_in;
	
	private $email;
	
	private $unique_key;
	
	private $user_id;
	
	private $username;
	
	public $user;
	
	public function log_user()
	{	
		// $user = $this->user->get_first(array('unique_key' => $this->unique_key), 'id, username');
		$user = $this->user->get_first(array('unique_key' => $this->unique_key));

		if($user)
		{
			$this->user = $user;
		}
	}
	
	public function __construct()
	{
		$this->load->database();
		
		$this->load->library('session');
		
		$this->load->model('User');
		
		$this->user = new User();
		
		if($uid = $this->session->logged_user_id)
		{
			$this->user->id = $uid;
		}
		elseif(isset($_COOKIE['mkey']) && $mkey = $_COOKIE['mkey'])
		{
			$this->unique_key = $this->sanitize_string($mkey);
			$this->log_user();
		}
	}
	
	public function log_in($data = array())
	{
		foreach($data as $key => $value)
		{
			setcookie($key, $value, time() + (60*60*24*30));
		}
	}
	
	public function get_user_id()
	{
		return $this->user->id;
	}

	public function get_user()
	{
		if($this->unique_key | !$this->user->id)
		{
			return $this->user;
		}
		elseif($this->user->id > 0)
		{
			$user = $this->user->get_first(array('id' => $this->user->id));
			return $user;
		}
	}
	
	public function get_username()
	{
		return $this->user->username;
	}
	
	public function log_out()
	{
		$this->session->unset_userdata('logged_user_id');
		setcookie('mkey', $this->unique_key,time() - 1);
		$this->user->id = null;
	}
}
?>