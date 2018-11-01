<?php
class Cart extends MY_Model{
	public $id;
	public $user_id;
	public $total_amount;
	
	/* Objects*/
	public $items = array();
	
	public $created;
	public $modified;
	
	protected $table = 'carts';
	
	public function __construct($options = array())
	{
		$this->load->database();
		if($options)
			$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'created' => time()
		);
		
		// Do not create new cart if one already exists
		if($this->count(array('user_id' => $this->user_id)))
			return;
		
		// Else create new cart
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function initialize()
	{
		if($this->user_id)
		{
			$result = $this->get_first(array('user_id' => $this->user_id));
			if($result)
			{
				$this->id = $result->id;
				$this->created = $result->created;
				return TRUE;
			}
			
			// If cart does not exist
			$this->save();
		}
	}
	
	public function get_items()
	{
		if($this->id)
		{
			$this->load->model('Cart_Item');
			$Cart_Item = new Cart_Item();
			
			$results = $Cart_Item->get_all(array('cart_id' => $this->id), '*', null, 'created DESC');
			
			if($results)
			{
				$loaded_models = array();
				foreach($results as $item)
				{
					if(!isset($loaded_models[$item->item_type]))
						$loaded_models[$item->item_type] = $this->load->model($item->item_type);
					
					$class = new $item->item_type();
					$item_obj = $class->get_first(array('id' => $item->item_id));
					
					
					if(is_null($item_obj))
					{
						$class->id = $item->item_id;
						$this->remove_item($class);
					}
					else
					{
						array_push($this->items, $item_obj);
						$this->total_amount += $item->payment_amount;
					}
				}
				return $this->items;
			}
		}
	}
	
	public function add_item($item)
	{
		$item_type = get_class($item);
		
		if(is_object($item) && $item->id && $item->price > 1)
		{
			if($item->count())
			{
				$this->load->model('Cart_Item');
				$Cart_Item = new Cart_Item(array(
				'cart_id' => $this->id,
				'item_type' => $item_type,
				'item_id' => $item->id,
				'payment_amount' => $item->price
				));
				return ($Cart_Item->save());
			}
		}
	}
	
	public function in_cart($item)
	{
		$item_type = get_class($item);
		if(is_object($item) && $item->id)
		{
			$this->load->model('Cart_Item');
			
			$data = array(
			'cart_id' => $this->id,
			'item_type' => $item_type,
			'item_id' => $item->id
			);
			$Cart_Item = new Cart_Item();
			
			return ($Cart_Item->count($data));
		}
	}
	
	public function remove_item($item)
	{
		$item_type = get_class($item);
		if(is_object($item) && $item->id)
		{
			$this->load->model('Cart_Item');
			$Cart_Item = new Cart_Item(array(
			'cart_id' => $this->id,
			'item_type' => $item_type,
			'item_id' => $item->id
			));
			return ($Cart_Item->delete());
		}
	}
	
	public function make_empty()
	{
		$this->load->model('Cart_Item');
		$Cart_Item = new Cart_Item(array('cart_id' => $this->id));
		$Cart_Item->empty_cart();
	}
}
?>