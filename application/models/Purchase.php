<?php
class Purchase extends MY_Model{
	
	public $id;

	public $user_id;
	
	public $seller_id;

	public $user_account_type;

	public $item;
	
	public $item_type;

	public $item_id;

	public $payment_amount;

	public $transaction_id;

	// external api or wallet
	public $method;
	
	public $created;
	
	public $modified;

	public $status = 'pending';
	
	public $is_seller_paid = 0;
	
	public $table = 'purchases';
	
	public function __construct($options = array())
	{
		$this->_populate($options);
		$this->load->database();
		
		if(is_object($this->item))
		{
			$this->item_id = $this->item->id;
			$this->item_type = get_class($this->item);
		}
	}
	
	public function save()
	{
		$data = array(
			'user_id' => $this->user_id,
			'user_account_type' => $this->user_account_type,
			'item_type' => $this->item_type,
			'item_id' => $this->item_id,
			'method' => $this->method,
			'seller_id' => $this->seller_id,
			'payment_amount' => $this->payment_amount,
			'status' => $this->status,
			'modified' => time()
		);
		
		// Do update 
		if($this->id && $this->transaction_id)
		{
			$data = $this->filled_fields($data);
			$this->db->set($data);	
			$this->db->where(array('id' => $this->id , 'transaction_id' => $this->transaction_id));	
			return $this->db->update($this->table);	
		}
		
		$data['created'] = time();
		$data['transaction_id'] = $this->generate_transaction_id();
		
		$this->db->insert($this->table, $data);
		
		 $this->id = $this->db->insert_id();
		return $this->id;
	}
	
	public function update($data = array(), $where = array())
	{
		$default_where = !empty($where) ? $where : array('id' => $this->id);
		if($data)
		{
			$data['modified'] = time();
			$this->db->set($data);
			$this->db->where($default_where);
			$this->_populate($data);
			return $this->db->update($this->table);
		}
	}
	
	// Mark seller as paid if 10 days have past
	
	public function mark_as_paid($seller_id)
	{
		$past_days = strtotime('-5 days');
		
		return $this->update(array('is_seller_paid' => TRUE), array('seller_id' => $seller_id, 'created < ' => $past_days));
	}
	
	public function generate_transaction_id()
	{
		return (hash('ripemd128', "prchse&*#".time().$this->user_id."lr*h^()"));
	}
	
	public function add_item($item = null)
	{
		if(!is_object($item))
			$item = $this->item;
		
		if(is_object($item) && $this->user_id && $item->id)
		{
			$item_type = get_class($item);
			
			$data = array('item_id' => $item->id, 'item_type' => $item_type, 'user_id' => $this->user_id);
			
			$old_purchase = $this->get_first($data);
			
			// Do an update on the payment_amount if the purchase has not been approved
			if($old_purchase && $old_purchase->status == 'pending')
			{
				$old_purchase->payment_amount = $item->price;
				$old_purchase->user_account_type = $this->user_account_type;
				
				return $old_purchase->save() ? $old_purchase : null;
			}
			elseif($old_purchase && $old_purchase->status == 'approved')
			{
				// No repeat purchases
				return FALSE;
			}
			elseif(!$old_purchase)
			{
				$data['payment_amount'] = $item->price;
				$data['seller_id'] = $item->user_id;
				$data['user_account_type'] = $this->user_account_type;
				$new_purchase = new Purchase($data);
				$new_purchase->method = $this->method;
				
				return $new_purchase->save() ? $new_purchase : null;
			}
		}
	}
	
	public function is_approved()
	{
		return $this->count(array('user_id' => $this->user_id, 'item_id' => $this->item_id, 'item_type' => $this->item_type, 'status' => 'approved'));
	}
	
	public function is_pending()
	{
		return $this->count(array('user_id' => $this->user_id, 'item_id' => $this->item_id, 'item_type' => $this->item_type, 'status' => 'pending'));
	}	
	
	public function is_cancelled()
	{
		return $this->count(array('user_id' => $this->user_id, 'item_id' => $this->item_id, 'item_type' => $this->item_type, 'status' => 'cancelled'));
	}	
	
	public function get_this_order()
	{
		return $this->get_first(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
	}
	
	
	public function approve()
	{
		return $this->update(array('status' => 'approved'));
		//removed item from cart if present
	}
	
	public function cancel()
	{
		return $this->update(array('status' => 'cancelled'));
	}
	
	public function get_my_purchases()
	{
		return $this->get_all(array('user_id' => $this->user_id));
	}
	
	public function get_history($item = null)
	{
		$this->get_all(array('user_id'));
	}
	
	public function remove_purchase($item)
	{
		
	}
	
	public function admin_get_all($where = array(), $fields = null, $limit = null, $order_by = null)
	{
		$default_where = array('user_id' => $this->user_id);
		$default_select = array($this->table.'.id as purchase_id');
		
		if($where)
			$default_where = $where;
		
		$this->db->select("users.email AS cust_email, users.username AS cust_username, {$this->table}.*");
		$this->db->from($this->table);
		$this->db->join("users", "users.id = {$this->table}.user_id", "left");
		$all_purchases = $this->db->get();
		$items = $all_purchases->result('Purchase');
		
		$class_types = array();	
		$ret_items = array();	
		if($items)	
		{	
			foreach($items as &$item)	
			{	
				if(!array_key_exists($item->item_type, $class_types))	
				{	
					$class_types[$item->item_type] = TRUE;	
					$this->load->model(ucfirst($item->item_type));	
				}	
					
				$class = $item->item_type;	
					
				$item_obj = new $class();	
				$item_obj = $item_obj->get_first(array('id' => $item->item_id), 'id,title');	
					
				$item->name = $item_obj ? $item_obj->get_name() : null;	
					
				$item->url = $item_obj ? $item_obj->get_url() : null;	
				
				$item->payment_amount = App\Helper\String::price_to_float($item->payment_amount);
				
				$item->transaction_id = $item->transaction_id;
					
				$item->created  = date('j/m/Y', $item->created)." at ".date('g:ia', $item->created);	
			}	
		}	
			
		return $items;
	}
	
	public function get_all_items($where = array(), $fields = null, $limit = null, $order_by = null)
	{
		$default_where = array('user_id' => $this->user_id);
		$default_select = array($this->table.'.id as purchase_id');
		
		if($where)
			$default_where = $where;
		
		$items = $this->get_all($default_where, $fields, $limit, $order_by);	
		//Convert items to their specific objects
		
		$class_types = array();	
		$ret_items = array();	
		if($items)	
		{	
			foreach($items as &$item)	
			{	
				if(!array_key_exists($item->item_type, $class_types))	
				{	
					$class_types[$item->item_type] = TRUE;	
					$this->load->model(ucfirst($item->item_type));	
				}	
					
				$class = $item->item_type;	
					
				$item_obj = new $class();	
				$item_obj = $item_obj->get_first(array('id' => $item->item_id), 'id,title');	
					
				$item->name = $item_obj ? $item_obj->get_name() : null;	
					
				$item->url = $item_obj ? $item_obj->get_url() : null;	
				
				$item->payment_amount = App\Helper\String::price_to_float($item->payment_amount);
				
				$item->transaction_id = $item->transaction_id;
					
				$item->created  = date('j/m/Y', $item->created)." at ".date('g:ia', $item->created);	
			}	
		}	
			
		return $items;
	}
	
	public function get_owed_writers()
	{
		$this->db->select('purchases.seller_id');
		$this->db->select('users.username');
		$this->db->select('users.email');
		
		$this->db->select_sum('purchases.payment_amount');
		$this->db->from($this->table);
		$this->db->join('users', "users.id = purchases.seller_id", 'left');
		
		// only payments that were maid over the last 10 or more days.
		//$cutoff_day = time();
		$cutoff_day = strtotime('-7 days');
		$this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => FALSE, 'purchases.created < ' => $cutoff_day));
		$this->db->group_by('seller_id');
		$result = $this->db->get()->result('User');
		
		if(is_array($result))
		{
			foreach($result as $user)
			{
				$user->payment_amount = App\Helper\String::price_to_float($this->get_amount_cut($user->payment_amount));
			}
		}
		return $result;
	}
	
	public function get_paid_writers()
	{
		$this->db->select('purchases.seller_id');
		$this->db->select('users.username');
		$this->db->select('users.email');
		
		$this->db->select_sum('purchases.payment_amount');
		$this->db->from($this->table);
		$this->db->join('users', "users.id = purchases.seller_id", 'left');
		
		$this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => TRUE));
		$this->db->group_by('seller_id');
		$result = $this->db->get()->result('User');
		if(is_array($result))
		{
			foreach($result as $user)
			{
				$user->payment_amount = App\Helper\String::price_to_float($this->get_amount_cut($user->payment_amount));
			}
		}
		return $result;
	}
	
	public function get_total_writers_owed()
	{
		$this->db->select('seller_id');
		$this->db->distinct();
		$this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => 0));
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}	
	
	public function get_total_writers_paid()
	{
		$this->db->select('seller_id');
		$this->db->distinct();
		$this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => 1));
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}
	
	
	public function get_total_due($seller_id = null)
	{
		$this->db->select_sum('payment_amount');
		$this->db->from($this->table);
		
		if(!$seller_id)
		{
			// Where sellers have not yet been paid
			$this->db->where(array('status' => 'approved', 'is_seller_paid' => 0));
		}
		else
		{
			$this->db->where(array('status' => 'approved', 'is_seller_paid' => 0, 'seller_id' => $seller_id));
		}

		$result = App\Helper\Array_Methods::flatten($this->db->get()->result());
		
		$total_amount = 0;
		if(is_array($result))
		{
			
			$total_amount = $result[0]->payment_amount;
			$total_amount = App\Helper\String::price_to_float($this->get_amount_cut($total_amount));
			
			return $total_amount;
		}
	}
	
	public function get_total_paid()
	{
		$this->db->select_sum('payment_amount');
		$this->db->from($this->table);
		
		// Where sellers have not yet been paid
		$this->db->where(array('status' => 'approved', 'is_seller_paid' => 1));
		$result = App\Helper\Array_Methods::flatten($this->db->get()->result());
		
		$total_amount = 0;
		if(is_array($result))
		{
			
			$total_amount = $result[0]->payment_amount;
			$total_amount = App\Helper\String::price_to_float($this->get_amount_cut($total_amount));
	
			return $total_amount;
		}
	}
	
	public function get_amount_cut($amount)
	{
		return $amount * 0.7;
	}
}
?>