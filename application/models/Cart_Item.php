<?php
class Cart_Item extends MY_Model{
	public $cart_id;
	public $item_type;
	public $item_id;
	public $payment_amount;
	protected $table = 'cart_items';
	protected $created;
	
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'cart_id' => $this->cart_id,
		'item_type' => $this->item_type,
		'item_id' => $this->item_id
		);
		
		// Do not create create if one exact same already exists
		if($this->count($data))
		{
			$this->db->set(array('payment_amount' => $this->payment_amount));
			$this->db->where($data);
			return $this->db->update($this->table);
		}
			
		$data['payment_amount'] = $this->payment_amount;
		$data['created'] = time();
		$this->db->insert($this->table, $data);
		return TRUE;
	}
	
	public function delete()
	{
		if($this->item_id)
		{
			$data = array(
			'cart_id' => $this->cart_id,
			'item_type' => $this->item_type,
			'item_id' => $this->item_id
			);
			$this->db->where($data);
			return $this->db->delete($this->table);
		}
	}
	
	public function empty_cart()
	{
		$this->db->where(array('cart_id' => $this->cart_id));
		return $this->db->delete($this->table);
	}
}
?>