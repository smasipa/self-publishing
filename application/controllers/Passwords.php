<?php

class Passwords extends MY_Controller{
	
	public $user;
	
	protected $_salt1 = '&56$#';
	
	protected $_salt2 = '*9)^';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Password');
		$this->user = new User();
	}

	protected function hash_password($password)
	{
		return hash('ripemd128', $this->_salt1 . $password . $this->_salt2);
	}
	
	public function valid_email($value)
	{
		$this->form_validation->set_message('valid_email','Please provide a valid email address.');
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
	
	public function view()
	{
		$this->log_out();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'callback_valid_email');
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
		
		$email = null;
		$sent = null;
		if($this->input->post('save') && $this->form_validation->run())
		{
			$email = $this->input->post('email');
			
			if($user = $this->user->get_first(array('email' => $email), 'id'))
			{
				if($user->is_banned)
					App\Activity\Access::blocked_item();
				
				$password = new Password(array('user_id' => $user->id, 'email' => $email));
				
				// Generate new recovery_key
				$password->send_email();
				$sent = TRUE;
			}
		}
		
		$this->load->view('templates/header', array('title' => 'Reset password', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/passwords/index', array('email' => $sent ? $email : null));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	function password_mismatch($value)					
	{					
		$this->form_validation->set_message('password_mismatch', 'Repeat password must match New password.');
		return ($this->input->post('new_password') !== $this->input->post('repeat_password')) ? FALSE : TRUE;					
	}
	
	public function valid_password($value)
	{
		$this->form_validation->set_message('valid_password','Password must be atleast 5 characters long, and have a combination of both letters and numbers.');
		$eval = preg_match("/^[a-z0-9]{5,}$/i", $value);
		$eval = $eval == 1 ? true : false;
		return $eval;
	}
	
	public function valid_username($value)
	{
		$this->form_validation->set_message('valid_username', 'Incorrect username.');
		
		//Cannot begin with a underscore, and/or end with one either
		$eval = preg_match("/^[^_][a-z0-9_]{2,24}[^_]$/i", $value);
		$eval = $eval == 1 ? true : false;
		return $eval;
	}
	
	public function reset_password($recovery_key)
	{
		$this->log_out();
		
		$this->load->library('form_validation');
		
		$data['username'] = $this->input->post('username');			
		$data['new_password'] = $this->input->post('new_password');
		$data['repeat_password'] = $this->input->post('repeat_password');
		
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
		
		$password = new Password();
		
		$password_reset = $password->get_first(array('recovery_key' => $recovery_key, 'active' => TRUE));
		
		if(!$password_reset)
			App\Activity\Access::show_404();
	
		$user = $this->user->get_first(array('id' => $password_reset->user_id), 'id,username,unique_key');
		
		if($user)
		{
			if($user->is_banned)
				App\Activity\Access::blocked_item();
			
			if($user->username !== $data['username'])
			{
				$this->form_validation->set_rules('username', 'Username', 'required|callback_valid_username');
			}
			
			$this->form_validation->set_rules('new_password', 'new_password', 'callback_valid_password');
			
			if($data['new_password'] !== $data['repeat_password'])
			{
				$this->form_validation->set_rules('new_password', 'New password', 			
				'callback_password_mismatch|required');
			}
			
			if($this->input->post('reset') && $this->form_validation->run())
			{			
				$password_reset->deactivate_recovery_key($recovery_key);
				
				// update user password
				$user->update(array('password' => $this->hash_password($data['new_password'])));
				
				$this->session->set_flashdata('settings_updated', "Password was successfully changed!");
				
				$this->authenticate->log_in(array('mkey' => $user->unique_key));
				$this->session->logged_user_id = $user->id;
				redirect('/settings');
			}
			
			$this->load->view('templates/header', array('title' => 'Reset password', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('account/passwords/reset', array('form_url' => $this->uri->uri_string, 'posted' => $data));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
		}
	}
}

?>