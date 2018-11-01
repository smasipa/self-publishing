<?php
include_once(INTERFACES_DIR.'Item.php');
class User extends MY_Model implements Item{
	
	public $username;
	
	public $email;
	
	public $id;
	
	public $first_name;
	
	public $last_name;
	
	public $unique_key;
	
	public $account_type = 'standard';
	
	public $about;
	
	public $approved_writer = 0;
	
	public $password;
	
	public $activated = 1;
	
	/* @cover_image Image */
	public $cover_image;
	
	public $created;
	
	public $modified;
	
	public $expires;
	
	public $expired;
	
	public $num_views;
	
	public $is_banned;
	
	public $table = 'users';
	
	public function __construct($options = array()){
		$this->load->database();
		
		if(!empty($options)){
			$this->_populate($options);
		}
		
		$this->_init_update_data($options);
		
	}
	
	public function is_valid()
	{
		return $this->id;
	}
	
	public function save()
	{
		$data = array(
		'username' => $this->username,
		'first_name' => $this->first_name,
		'last_name' => $this->last_name,
		'email' => $this->email,
		'about' => $this->sanitize_string($this->about),
		'approved_writer' => $this->approved_writer,
		'account_type' => $this->account_type,
		'password' => $this->password,
		'modified' => time(),
		'expires' => $this->expires,
		'expired' => $this->expired
		);
		
		if($this->id)
		{
			//update data fields that are not empty
			$data = $this->filled_fields($data);
			
			$this->db->set($data);
			$this->db->where('id', $this->id);
			return $this->db->update($this->table);
		}
		
		//insert
		$data['unique_key'] = $this->unique_key;
		
		$data['created'] = time();
		$data['expires'] = time();
		$data['expired'] = time();
		$this->db->insert($this->table, $data);
		$this->id = $this->db->insert_id();
		return $this->id;
	}
	
	public function update($data = array())
	{
		if($data)
		{
			if(isset($data['about']))
			{
				$data['about'] = $this->sanitize_string($data['about']);
			}
			
			$data['modified'] = time();
			$this->db->set($data);
			$this->db->where(array('id' => $this->id));
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	public function ban()
	{
		return $this->update(array('is_banned' => TRUE));
	}
	
	public function unban()
	{
		return $this->update(array('is_banned' => FALSE));
	}
	
	public function get_name()
	{
		return $this->username;
	}
	
	public function get_db_name()
	{
		if($this->id)
		{
			$user = $this->get_first(array('id' => $this->id), 'username');
			
			return $user ? $user->username : null;
		}
	}
	
	public function get_url()
	{
		return $this->username;
	}
	
	public function initialize_image()
	{
		$this->load->model('Image');
		
		$this->cover_image = new Image(array('item_id'=> $this->id, 'user_id'=> $this->id));
		
		if($this->id)
		{
			$old_image = $this->cover_image->get_first(array('item_id' => $this->id, 'item_type' => 'user'));
			
			if($old_image)
			{
				$this->cover_image = $old_image;
				$this->cover_image->user_id = $this->id;
			}
		}
	}
	
	public function upload_image()
	{
		if($this->cover_image && $this->cover_image->save())
		{
			$this->cover_image->delete();
			return TRUE;
		}
	}	
	
	public function get_cover_image($size = 'small')
	{
		$this->initialize_image();
		return $this->cover_image->get_image_link($size);
	}	
	
	public function delete_image()
	{
		if($this->image)
			$this->cover_image->delete();
	}
	
	public function is_admin()
	{
		return $this->account_type == 'admin' ? TRUE : FALSE;
	}
	
	public function is_approved()
	{
		return $this->approved_writer == 1 || $this->is_admin() ? TRUE : FALSE;
	}
	
	public function incr_num_views()
	{
		$num_views = $this->num_views + 1;
		$this->update(array('num_views' => $num_views));
	}
	
	// Membership
	
	public function save_new_membership(Membership $membership)
	{
		if($this->id)
		{
			$membership->user_id = $this->id;
			if($membership->save())
			{
				return $this->update(array(
				'expires' => $membership->expiration_date,
				'expired' => $membership->expired,
				'account_type' => 'premium'));
			}
		}
	}	
	
	public function renew_membership(Membership $membership)
	{
		if($this->id)
		{
			$membership->user_id = $this->id;
			if($membership->renew())
			{
				return $this->update(array(
				'expires' => $membership->expiration_date,
				'expired' => $membership->expired,
				'account_type' => 'premium'));
			}
		}
	}	
	
	public function expire_membership()
	{
		if($this->id)
		{
			$this->load->model('Membership');
			$membership = new Membership();
			$membership = $membership->get_first(array('user_id' => $this->id));
			
			if($membership)
			{
				$class = ucfirst(get_class($membership));
				$current_membership = new $class(array('user_id' => $this->id));
				if($current_membership->expire())
				{
					return $this->update(array(
					'expired' => $current_membership->expired,
					'account_type' => 'standard'));
				}
			}
		}
	}
	
	public function is_premium()
	{
		if(!$this->expired && $this->expires > time())
		{
			return TRUE;
		}
		elseif(!$this->expired && $this->expires < time())
		{
			$this->expire_membership();
			return FALSE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function getFirstName()
	{
		return $this->first_name;
	}
	
	public function getLastName()
	{
		return $this->last_name;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
}
?>