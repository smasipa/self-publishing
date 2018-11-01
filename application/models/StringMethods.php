<?php
class User extends MY_Model{
	public $username;
	public $email;
	public $id;
	public $first_name;
	public $last_name;
	public $account_type = 'standard';
	public $password;
	public $activated = 1;
	public $created;
	public $modified;
	public $table = 'users';
	
	public function _populate($data = array()){
		foreach($data as $key =>$value)
		{
			if(property_exists($this,$key))
			{
				$this->$key = $value;
			}
		}
	}
	
	public function __construct($data = array()){
		$this->load->database();
		
		if(!empty($data)){
			$this->_populate($data);
		}
		$this->load();
	}
	
	public function load()
	{
		if($this->email)
		{
			$query = $this->db->where('email',$this->email)->get($this->table,1,0);
			if($row = $query->row())
			{
				$this->id = $row->id;
				$this->username = $row->username;
				$this->first_name = $row->first_name;
				$this->last_name = $row->last_name;
				$this->account_type = $row->account_type;
				$this->activated = $row->activated;
				$this->created = $row->created;
				$this->modified = $row->modified;
			}
		}		
		
		if($this->id)
		{
			$query = $this->db->where('id',$this->id)->get($this->table,1,0);
			if($row = $query->row())
			{
				$this->id = $row->id;
				$this->username = $row->username;
				$this->first_name = $row->first_name;
				$this->last_name = $row->last_name;
				$this->account_type = $row->account_type;
				$this->activated = $row->activated;
				$this->created = $row->created;
				$this->modified = $row->modified;
			}
		}
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
		'account_type' => $this->account_type,
		'password' => $this->password,
		'modified' => time(),
		'expires' => time(),
		'expired' => time()
		);
		
		if($this->id)
		{
		//update
			$query = $this->db->where('id', $this->id);
			$query->update('users', $data);
		}
		
		//insert
		$data['created'] = time();
		$this->id = $this->db->insert($this->table, $data);
		return $this->id;
	}
}
?>