<?php
class Payment extends MY_Model{
	
	protected $table = 'payments';
	
	public $id;
	
	public $seller_id;
	
	public $admin_id;
	
	public $amount_paid;
	
	public $status = 'confirmed';
	
	public $created;
	
	public $modified;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'seller_id' => $this->seller_id,
		'admin_id' => $this->admin_id,
		'amount_paid' => App\Helper\String::price_to_int($this->amount_paid),
		'status' => $this->status,
		'created' => time(),
		'modified' => time()
		);
		
		$this->db->insert($this->table, $data);
		
		$this->id = $this->db->insert_id();
		
		return $this->id;
	}
	
	public function make_payment()
	{
		if($this->save())
		{
			$this->load->model('Purchase');
			$purchase = new Purchase();
			$purchase->mark_as_paid($this->seller_id);
		}
	}
	
	public function confirm_payment()
	{
		$this->update(array('status' => 'confirmed'));
	}
	
	public function is_confirmed()
	{
		return $this->status == 'confirmed' ? TRUE : FALSE;
	}	
	
	public function cancel_payment()
	{
		$this->update(array('status' => 'cancelled'));
	}
	
	public function get_amount()
	{
		return App\Helper\String::price_to_float($this->amount_paid);
	}
	
	public function get_total_paid()
	{
		$this->db->select_sum('amount_paid');
		$this->db->where(array('status' => 'confirmed'));
		$result = App\Helper\Array_Methods::flatten($this->db->get($this->table)->result());
		
		return !empty($result[0]) ? App\Helper\String::price_to_float($result[0]->amount_paid) : 0;
	}
	
	public function get_successful_payments($where, $fields = null, $limit = null, $order_by = null)
	{
		$this->db->select('payments.*');
		$this->db->select('users.email');
		
		$this->db->from($this->table);
		$this->db->join('users', "payments.admin_id = users.id", 'left');
		$this->db->where(array('payments.seller_id' => $this->seller_id));
		// $this->db->group_by('payments.seller_id');
		
		$result = $this->db->get()->result(get_class($this));
		
		if($result)
		{
			foreach($result as $payment)
			{
				$payment->modified = date('j/m/Y @ G:i:s', $payment->modified);
				$payment->amount_paid = App\Helper\String::price_to_float($payment->amount_paid);
			}
		}
		
		return $result;
	}
	
	public function get_user_total()
	{
	}
	
	public function get_ammount_due()
	{
		// $this->db->query
	}
	
}
?>