<?php
class Login_Activity extends MY_Model{

	public $table = 'logins';
	
	public $user_id;
	
	public $created;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function log()
	{
		$data = array(
		'user_id' => $this->user_id,
		'created' => time()
		);
		return $this->db->insert($this->table, $data);
	}
	
	public function get_all_logins($where = array(), $limit = array(), $order_by = array())
	{
		$this->load->model('User');
		$user = new User();
		$this->db->select("{$this->table}.*");
		$this->db->select("{$user->table}.username, {$user->table}.email, {$user->table}.account_type");
		$this->db->from($this->table);
		$this->db->join($user->table, "{$this->table}.user_id = {$user->table}.id");
		$this->db->where($where);
		$this->db->order_by("{$this->table}.created DESC");
		$result = $this->db->get()->result('Login_Activity');
		
		if(!empty($result))
		{
			foreach($result as $login)
			{
				$login->created = date('j/m/y', $login->created)." at ". date('g:ia', $login->created);		
			}
		}
		return $result;
	}
}

?>