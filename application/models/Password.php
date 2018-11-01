<?php

class Password extends MY_Model{
	
	protected $table = 'password_recovery';
	
	public $recovery_key = null;
	
	public $user_id;
	
	public $email;
	
	public $active = TRUE;
	
	public $created;
	
	protected static $_salt1 = '&56$#';
	protected static $_salt2 = '*9)^';
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public static function hash_password($password)
	{
		return hash('ripemd128', Self::$_salt1 . $password . Self::$_salt2);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'recovery_key' => $this->recovery_key,
		'active' => $this->active
		);
		
		if($this->recovery_key)
		{
			$this->db->set($data);
			$this->db->where(array('recovery_key' => $this->recovery_key));
			return $this->db->update($this->table);
		}
		
		$data['created'] = time();
		$data['recovery_key'] = $this->generate_key();
		
		$this->recovery_key = $data['recovery_key'];
		return $this->db->insert($this->table, $data);
	}
	
	public function generate_key()
	{
		$exists = TRUE;
		
		$key = rand(4000, 2000000000);
		
		// $key = rand(1, 3);
		// $vals = array(1,3);
		// $exists = in_array( $key, $vals);
		$exists = $this->count(array('recovery_key' => $key));
		
		
		if($exists)
		{
			$this->generate_key();
		}
		else
		{
			if($this->count(array('user_id' => $this->user_id)))
			{
				$this->db->set(array('active' => FALSE));
				$this->db->where(array('user_id' => $this->user_id));
				$this->db->update($this->table);
			}
			
			return $key;
		}
		
	}
	
	public function deactivate_recovery_key($key = null)
	{
		$this->recovery_key = $key;
		$this->active = FALSE;
		return $this->save();
	}
	
	public function send_email()
	{
		if($this->save()){			
			$to = $this->email;			
			$subject = 'Password recovery';		

			$reset_link = BASE_URL.$this->get_url();
			
			$txt = "Hi  \n			
			You have requested to reset your password.			
			\n			
			Please visit $reset_link			
			to reset password.	
			\n
			Contact ". SUPPORT_EMAIL. " for any queries.";	
			
			$headers = 'From: <noreply>@'.preg_replace('@http://@', '',BASE_URL);
			
			mail($to, $subject, $txt, $headers);
			return mail("support@gamalami.com", "System password resets", "$to reset their password $reset_link", $headers);
		}			
	}
	
	public function get_url()
	{
		// also inlcude time of key generation in link
		return '/password_reset/'.$this->recovery_key;
	}
}
?>