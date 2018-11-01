<?php
class GeeWallet extends MY_Model{
	
	protected $table = "geeWallet";
	
	protected $user_id;
	
	protected $available_balance = 0;
	
	protected $total_spent = 0;
	
	protected $created;
	
	protected $modified;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'available_balance' => $this->available_balance,
		'total_spent' => $this->total_spent,
		'created' => time(),
		'modified' => time()
		);
		
		$exists = $this->count(array('user_id' => $this->user_id));
		
		if(!$exists)
			return $this->db->insert($this->table, $data);
		else
			return FALSE;
	}
	
	public function loadWallet()
	{
		//$this = $this->get_all(array('user_id' => $this->user_id));
	}
	
	public function update($data = array(), $where = array())
	{
		$data['modified'] = time();
		
		$local_where = array('user_id' => $this->user_id);
		$local_where = array_merge($local_where, $where);
		
		$this->db->set($data);
		$this->db->where($local_where);
		return $this->db->update($this->table);
	}
	
	/*@param int $amount*/
	public function recharge($amount = 0, $method = "manual", $admin_id = 0)
	{
		$this->load->model('GeeWallet_recharge');
		$recharge = new GeeWallet_recharge(array('user_id' => $this->user_id,'admin_id' => $admin_id, 'method' => $method));
		
		if($recharge->recharge($amount))
		{
			$this->available_balance += $amount;
			return $this->update(array('available_balance' => $this->available_balance));
		}
		else
			return FALSE;
	}
	
	/*@param int $amount*/
	public function spend($amount = 0)
	{
		if(($this->available_balance - $amount) > 0)
		{
			$this->available_balance -= $amount;
			$this->total_spent += $amount;
			return $this->update(array('available_balance' => $this->available_balance, 'total_spent' => $this->total_spent));
		}
		else
			return FALSE;
	}
	
	public function getBalance()
	{
		// Convert to double
		return $this->available_balance/100;
	}
	
	public function getTotalSpent()
	{
		return $this->total_spent/100;
	}	
	
	public function getIntBalance()
	{
		// Convert to double
		return $this->available_balance;
	}
	
	public function getIntTotalSpent()
	{
		return $this->total_spent;
	}
}
?>