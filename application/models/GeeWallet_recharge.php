<?php
class GeeWallet_recharge extends MY_Model{
	
	protected $table = "geeWallet_recharge";
	
	protected $user_id;
	
	protected $admin_id = 0;
	
	protected $id;
	
	protected $amount = 0;
	
	protected $method = 'manual';
	
	protected $created;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'admin_id' => $this->admin_id,
		'amount' => $this->amount,
		'method' => $this->method,
		'created' => time()
		);
		
		
		if($this->method == 'manual' && $this->admin_id == 0)
			return false;

		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	/*@param int $amount*/
	public function recharge($amount = 0)
	{
		if($amount > 0)
		{
			$this->amount = $amount;
			return $this->save();
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getAllRecharges()
	{
		$this->db->select("custs.email AS cust_email, custs.username AS cust_username, admins.email AS admin_email, admins.username AS admin_username,
		geewallet_recharge.*, (geewallet_recharge.amount / 100) AS calc_amount");
		// $this->db->select("*");
		$this->db->from($this->table);
		$this->db->join("users AS custs", "custs.id = {$this->table}.user_id", "inner");
		$this->db->join("users AS admins", "admins.id = {$this->table}.admin_id", "left");
		$this->db->order_by("created DESC");
		$results = $this->db->get();
		
		//var_dump($results->result('array'));
		//exit();
		return $results->result('array');
	}
}
?>