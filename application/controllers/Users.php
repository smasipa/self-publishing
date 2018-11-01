<?php
class Users extends MY_Controller{
	protected $_salt1 = '&56$#';
	protected $_salt2 = '*9)^';
	private $user_id;
	private $edit = null;
	private $user;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('User');
		$this->user = new User();
	}
	
	protected function hash_password($password)
	{
		return hash('ripemd128', $this->_salt1 . $password . $this->_salt2);
	}
	
	public function register()
	{
		$this->authenticate->log_out();
		
		$this->load->library('form_validation');
		
		$data = array(
		'username' => $this->input->post('username'),
		'email' => $this->input->post('email')
		);
		
		$this->form_validation->set_rules('username', 'Username', 'callback_valid_username|callback_username_taken');
		$this->form_validation->set_rules('email', 'Email', 'callback_valid_email|callback_email_taken');
		$this->form_validation->set_rules('password', 'Password', 'required|callback_valid_password');
		
		// Put these settings in config file to avoid repetition
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
		
		if($this->form_validation->run() == TRUE)
		{
			$form_data = $this->input->post();
			$form_data['password'] = $this->hash_password($this->input->post('password'));
			$form_data['unique_key'] = $this->hash_password($this->input->post('email').time());
			$user = new User($form_data);
			
			if($user->save())
			{
				// Add geeWallet account
				$this->load->model('geeWallet');
				$geeWallet = new GeeWallet(array('user_id' => $user->getId()));
				$geeWallet->save();
				
				// Add link to users sitemap file	
				$this->load->library('Sitemap');		
				$sitemap = new Sitemap($user);		
				$sitemap->create_new();	
				
				$this->session->logged_user_id = $user->id;
				$this->authenticate->log_in(array('mkey' => $user->unique_key));
				
				redirect(BASE_URL.$user->username);
			}
		}
		
		$this->load->view('templates/header', array('title' => 'Register'));
		$this->load->view('/register',$data);
		$this->load->view('templates/footer');			
	}
	
	public function valid_username($value)
	{
		$this->form_validation->set_message('valid_username', 'Only letters, underscores and/or numbers allowed');
		
		//Cannot begin with a underscore, and/or end with one either
		$eval = preg_match("/^[^_][a-z0-9_]{2,24}[^_]$/i", $value);
		$eval = $eval == 1 ? true : false;
		return $eval;
	}	
	
	public function valid_name($value)
	{
		$this->form_validation->set_message('valid_name', 'Only letters allowed, should be more than 2 letters.');
		$eval = preg_match("/^[a-zA-Z\s+]{2,}$/i", trim($value));
		$eval = $eval == 1 ? true : false;
		return $eval;
	}
	
	public function valid_email($value)
	{
		$this->form_validation->set_message('valid_email','Please provide a valid email.');
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}	
	
	public function valid_password($value)
	{
		$this->form_validation->set_message('valid_password','Password must be atleast 5 characters long, and have a combination of both letters and numbers.');
		$eval = preg_match("/^[a-z0-9]{5,}$/i", $value);
		$eval = $eval == 1 ? true : false;
		return $eval;
	}
	
	public function email_taken($value)
	{
		$this->form_validation->set_message('email_taken', 'Sorry this email address is already registered, try a different one.');
		$user = new User();
		$exists = $user->get_first(array('email' => $value));
		
		return empty($exists);
	}
	
	public function username_taken($value)
	{
		$this->form_validation->set_message('username_taken', 'Sorry this username is already in use, try a different one.');
		$user = new User();
		$exists = $user->get_first(array('username' => $value));
		return empty($exists);
	}
	
	public function login()
	{
		$this->authenticate->log_out();
		
		$this->load->model('User'); 
		$this->load->library('form_validation');
		
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
		
		$data = array(
		'email' => $this->input->post('email'),
		'password' => $this->input->post('password')
		);
		
		if($this->input->post('login'))
		{
			$form_data = array(
			'email' => $this->input->post('email'),
			'password' => $this->hash_password($this->input->post('password'))
			);			
			
			
			$user = new User();
			$user = $user->get_first($form_data);
			if($user)
			{
				$this->session->logged_user_id = $user->id;
				$this->authenticate->log_in(array('mkey' => $user->unique_key));
				
				
				// Store login in database
				$this->load->model('access/Login_Activity');
				$lg_activity = new Login_Activity(array('user_id' => $user->id));
				$lg_activity->log();
				
				redirect('/recent');
			}
			else
			{
				$this->form_validation->set_rules('email', 'Email', 'callback_invalid_login');
				$this->form_validation->run();
			}
		}
		$this->load->view('templates/header', array('title' => 'Login'));
		$this->load->view('login', $data);
		$this->load->view('templates/footer');
	}	
	
	public function invalid_login()
	{
		$this->form_validation->set_message('invalid_login', 'Invalid email/password combination.Please try again.');
		return FALSE;	
	}
	
	public function settings()
	{
		// $this->load->model('Visit');
		if(!$this->is_logged_in)
		App\Activity\Access::login();
	
		$this->load->library('form_validation');	
		$this->load->model('User');		
		
		$user = $this->logged_in_user;	
			
		if($user)	
		{	
			$user->created = date('d/m/Y @ G:i:s', $user->created);	
			$this->load->view('templates/header', array('title' => 'Settings', 'is_logged_in' => $this->is_logged_in));	
			$this->load->view('account/settings',array('user' => $user,'updated' => $this->session->flashdata('settings_updated'), 'edit' => $this->edit));	
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));	
		}	
	}
	
	function email_mismatch($value)					
	{					
		$this->form_validation->set_message('email_mismatch', 'Repeat email must match New email.');					
		return ($this->input->post('new_email') !== $this->input->post('repeat_email')) ? FALSE : TRUE;					
	}	
	
	function password_mismatch($value)					
	{					
		$this->form_validation->set_message('password_mismatch', 'Repeat password must match New password.');
		return ($this->input->post('new_password') !== $this->input->post('repeat_password')) ? FALSE : TRUE;					
	}	
	
	function password_incorrect($value)					
	{					
		$this->form_validation->set_message('password_incorrect', 'Password incorrect.');
		return FALSE;					
	}
	
	public function edit_settings($item_to_edit)
	{
		if(!$this->is_logged_in)
		App\Activity\Access::login();
	
		$allowed_edits = array('email', 'password', 'username');
		if(in_array($item_to_edit, $allowed_edits))
		{
			$this->edit = $item_to_edit;
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
			$this->load->model('User');
			$user = $this->logged_in_user;
			
			$data = array(
			'old_username' => $user->username,
			'old_email' => $user->email
			);
			
			if($user)
			{
				switch($item_to_edit)
				{
					case 'username':
						$this->form_validation->set_rules('username', 'Username', 'callback_valid_username|callback_username_taken');
						$data['username'] = $this->input->post('username');
						$user->username = $this->input->post('username');
					break;
					
					case 'email':
						$this->form_validation->set_rules('new_email', 'New email', 'callback_valid_email|callback_email_taken');
						$this->form_validation->set_rules('repeat_email', 'Repeat email', 'callback_email_mismatch');
						
						if($user->password !== $this->hash_password($this->input->post('password')))
							$this->form_validation->set_rules('password', 'Provide valid password', 'callback_password_incorrect');
						
						$data['new_email'] = $this->input->post('new_email');
						$data['repeat_email'] = $this->input->post('repeat_email');
						$user->email = $this->input->post('new_email');
					break;
					
					case 'password':
						if($user->password !== $this->hash_password($this->input->post('old_password')))
						{
							$this->form_validation->set_rules('old_password'
							, 'Old password','callback_password_incorrect');
						}
						
						$this->form_validation->set_rules('new_password', 'New password', 
						'callback_valid_password|callback_password_mismatch');	
						
						$data['old_password'] = $this->input->post('old_password');
						$data['new_password'] = $this->input->post('new_password');
						$data['repeat_password'] = $this->input->post('repeat_password');
						
						$user->password = $this->hash_password($this->input->post('new_password'));
					break;
					
					default:
					break;
				}
				
				if($this->input->post('save') && $this->form_validation->run() && $user->save())
				{
					// Refresh email
					$this->session->set_flashdata('settings_updated', ucfirst($item_to_edit). " was successfully changed!");
					
					// Store name change in database
					
					if(isset($data['username']) || $data['new_email'])
					{
						$this->load->model('access/Activity');
						$lg_activity = new Activity(array('user' => $this->logged_in_user));
					
						if(strtolower($data['old_username']) != strtolower($user->username))
						{
							$lg_activity->username_changed($user, $data['old_username']);
							
							// Add link to users sitemap file	
							$this->load->library('Sitemap');		
							$sitemap = new Sitemap($user);		
							$sitemap->create_new();
						}					
						
						// Store name change in database
						if(strtolower($data['old_email']) != strtolower($data['new_email']))
						{
							$lg_activity->email_changed($user, $data['old_email']);
						}
					}

					redirect("/settings");
				}
				else
				{
					$this->load->view('templates/header', array('title' => 'Settings', 'is_logged_in' => $this->is_logged_in));
					$this->load->view('account/settings',array('posted' => $data, 'user' => $user, 'edit' => $this->edit));
					$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
					return;
				}
			}
		}
		show_404();
	}
	
	public function get_profile($username = null)
	{
		$this->load->model('User');
		$user = new User();
		
		if(!$username && $this->logged_in_user->id)
		{
			$user = $this->logged_in_user;
		}
		elseif($username)
		{
			$user = $user->get_first(array('username' => $username));
		}
		else{
			App\Activity\Access::login();
		}

		if(!$user)
			App\Activity\Access::show_404();
	
		if($user->is_banned)
			App\Activity\Access::blocked_url();
		
		$this->load->model('Publication');
		
		$publication = new Publication();
		
		$publications = $publication->get_all(array('user_id' => $user->id), 'title, id', array(7, 0), 'created DESC');
		
		if($publications)
		{
			foreach($publications as $publication)
			{
				$publication->url = App\Utility\StringMethods::make_slug($publication->title)."/".$publication->id;
			}
		}
		
		$data = array();
		
		$user->about = nl2br($user->about);

		$this->load->library('Social_media');
		
		$data['profile_image'] = $user->get_cover_image()->location;
		
		$social_media = new Social_media($user->username, $user->get_url(), $data['profile_image'], 'Check out this account');
		
		$data['social_media'] = $social_media->get_all_btns();				
		
		$data['user'] = $user;
		
		$data['publications'] = $publications;
		
		$this->load->model('Book');
		
		$books = null;
		
		if($user->approved_writer)
		{
			$book = new Book();
			$books = $book->get_all(array('user_id' => $user->id), 'title, id', array(7, 0), 'created DESC');
			
			if($books)
			{
				foreach($books as $book)
				{
					$book->url = App\Utility\StringMethods::make_slug($book->title)."/".$book->id;
				}
			}
		}
		
		$data['books'] = $publications;
		
		$data['publications'] = $publications;
		
		$data['updated'] = $this->session->flashdata('profile_updated');
		
		
	
		$data['edit'] = $user->id == $this->logged_in_user->id ? TRUE : FALSE;
		
		
		$this->load->view('templates/header', array('title' => 'Profile | '.$user->username, 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/profile', $data);
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in, 'facebook_js_btn' => $social_media->init_fb_sharescript()));
	}
	
	public function edit_profile($item_to_edit)
	{
		$allowed_edits = array('profile_image', 'first_name', 'last_name', 'about');
		
		if(!$this->is_logged_in)
		App\Activity\Access::login();
		
		if(!in_array($item_to_edit, $allowed_edits))
		show_404();
	
		$user = $this->logged_in_user;
		
		$this->edit = $item_to_edit;
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");

		$new_data = array();
		
		$initial_data = array(
		'first_name' => $user->first_name,
		'last_name' => $user->last_name,
		'about' => $user->about
		);
		
		switch($item_to_edit)
		{
			case 'first_name':
				$this->form_validation->set_rules('first_name', 'First Name', 'callback_valid_name');
				
				$new_data['first_name'] = $this->input->post('first_name')  ? $this->input->post('first_name'): '';
			break;
			
			case 'last_name':
				$this->form_validation->set_rules('last_name', 'Last Name', 'callback_valid_name');
				$new_data['last_name'] = $this->input->post('last_name') ? $this->input->post('last_name') : '';
			break;
			
			case 'about':
				$this->form_validation->set_rules('about', 'about', 'regex_match[/(.*)/]');
				$new_data['about'] = $this->input->post('about') ? $this->input->post('about') : '';
			break;
			
			default:
				$user->initialize_image();
				if($user->upload_image())
				redirect('/profile');
			break;
		}
		
		if($this->input->post('save') && $this->form_validation->run() && $user->update($new_data))
		{
			$this->session->set_flashdata('profile_updated', preg_replace('#_#', ' ', "Your ". ucfirst($item_to_edit)). " was successfully changed!");
			
			// User profile
			redirect('/profile');
		}
		else
		{
			$data = App\Helper\Array_Methods::fill_with($new_data, $initial_data);
			
			$this->load->view('templates/header', array('title' => 'Edit Profile', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('account/edit_profile', array('username' => $this->logged_in_user->username, 'posted' => $data, 'edit' => $this->edit));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
	}
	
	public function get_verified()
	{
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$data = array();
		$data['verification_status'] = null;
		
		if($this->logged_in_user->approved_writer)
		{
			$data['verification_status'] = 'Congradulations your account has been verified! <br/>
			You can now start selling items.';
		}
		
		$this->load->model('Writer');
		
		$this->load->library('form_validation');
		
		$data['posted'] = array(
		'id_number' => null,
		'account_number' => null,
		'bank_name' => null,
		'phone_number' => null
		);
		
		$data['request_recieved'] = $this->session->flashdata('writer_verification_request');
		
		if(!$data['request_recieved'])
		{
			$old_writer = new Writer();
			$old_writer = $old_writer->get_first(array('user_id' => $this->logged_in_user->id));
			if($old_writer)
			{
				$date = date('d M Y @ G:i:s', $old_writer->created);
				switch($old_writer->status)
				{
					case 'approved':
					break;
					case 'pending':
						$data['verification_status'] = 'Your verification request that was submitted on '.$date . ' is still being processed.';
					break;
					case 'cancelled':
						$data['verification_status'] = 'Your verification request that was submitted on '.$date . 
						' has been cancelled. For any questions feel free to contact us on '.ADMIN_EMAIL.'.';
					break;
				}
			}
		}
		
		if(!$data['verification_status'] && $this->input->post('save'))
		{
			$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
			$this->form_validation->set_rules('id_number', 'ID Number', 'integer|exact_length[13]|required');
			$this->form_validation->set_rules('account_number', 'Bank Account Number', 'integer|required');
			$this->form_validation->set_rules('bank_name', 'Bank Name', 'required|callback_valid_name');
			$this->form_validation->set_rules('phone_number', 'Phone Number', 'integer');
			
			if($this->form_validation->run() == TRUE)
			{
				$writer = new Writer(array(
					'user_id' =>  $this->logged_in_user->id,
					'id_number' => $this->input->post('id_number'),
					'account_number' => $this->input->post('account_number'),
					'bank_name' => $this->input->post('bank_name'),
					'cellphone_number' => $this->input->post('phone_number')
					));
					
				if($writer->save())	
				{	
					$this->session->set_flashdata('writer_verification_request', 'Your request for verification has been recieved, 	
					<br/> you will be notified by sms or email once it has been processed. For any questions feel free to contact us on '.ADMIN_EMAIL.'.');	
						
					redirect('get_verified');	
				}	
			}
			
			$data['posted'] = $this->input->post();
		}
		
		$this->load->view('templates/header', array('title' => 'Get verified and start selling', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/writers/get_verified', $data);
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function account()
	{
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$user = $this->logged_in_user;
		
		$user->url = $user->get_url();
		
		$this->load->view('templates/header', array('title' => 'Account', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/index', array('user' => $user, 'approved_writer' => $user->approved_writer));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function view_all_users()
	{
		$authors = $this->user->get_all(array('approved_writer' => TRUE));
		
		if($authors)
		{
			foreach($authors as $author)
			{
				$author->name = $author->get_name();
				$author->url = $author->get_url();
				$author->cover_image = $author->get_cover_image();
			}
			
			$this->load->view('templates/header', array('title' => 'Authors', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('users/index', array('authors' => $authors, 'pagination' => null));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
		App\Activity\Access::nothing_found();
	}
	
	public function password_reset($key = null)
	{
		$this->load->model('Password');
		$password = new Password(array('user_id' => $this->logged_in_user->id));
		
		// $password->save();
		$password->deactivate_recovery_key(123355804);
	}
	
}
?>