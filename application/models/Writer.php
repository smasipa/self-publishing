<?php
/* Approve writers and grants them to have access to exlcusive writer features */

class Writer extends MY_Model{
	
	public $table = 'writers';
	
	public $id;
	
	public $user_id;
	
	public $admin_id;
	
	public $id_number;
	
	public $account_number;
	
	public $bank_name;
	
	public $cellphone_number;
	
	public $status = 'pending';
	
	public $created;
	
	public $modified;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
		// $this->_init_update_data($options, array('user_id', 'id'));
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'id_number' => $this->id_number,
		'account_number' => $this->account_number,
		'bank_name' => $this->bank_name,
		'cellphone_number' => $this->cellphone_number,
		'modified' => time(),
		'status' => $this->status
		);
		
		if($this->id)
		{
			// Update
			$data = $this->filled_fields($data);
			$this->db->set($data);
			return $this->db->update($this->table);
		}
		
		$data['created'] = time();
		return $this->db->insert($this->table, $data);
	}
	
	public function approve()
	{
		if($this->user_id)
		{
			$data = array('status' => 'approved');
			if($this->update($data))
			{
				$this->load->model('User');
				$user = new User(array('id' => $this->user_id));
				$user_data = array('approved_writer' => 1);
				return $user->update($user_data);
			}
		}
	}
	
	public function update($data = array())
	{
		if($data)
		{
			$this->db->set($data);
			$this->db->where(array('user_id' => $this->user_id));
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	public function cancel()
	{
		if($this->user_id)
		{
			$data = array('status' => 'cancelled');
			if($this->update($data))
			{
				$this->load->model('User');
				$user = new User(array('id' => $this->user_id));
				$user_data = array('approved_writer' => 0);
				return $user->update($user_data);
			}
		}
	}
}
?>