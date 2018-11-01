<?php
class Carts extends CI_Controller{
	private $user_id;
	private $logged_in_user;
	public $Cart;
	
	public function __construct()
	{
		parent::__construct();
		$this->user_id = $this->authenticate->get_user_id();
		$this->logged_in_user = $this->authenticate->get_user();
		
		if(!$this->logged_in_user->id)
			App\Activity\Access::login();
		
		$this->load->model('Cart');
		$this->Cart = new Cart(array('user_id' => $this->user_id));
		$this->Cart->initialize();
	}
	
	public function get_cart()
	{
		$this->load->model('Purchase');
		
		// Remove items that are in purchases
		
		$this->load->view('templates/header');
		if($this->logged_in_user->account_type == 'premium')
		{
			$this->load->view('account/premium_benefit', array('heading' => 'You do not need a cart!', 'message' => "Since you are a premium member everything is available to you."));
		}
		else{
			$cart = $this->Cart->get_items();
			if($cart)
			{
				foreach($cart as &$c)
				{
					$purchase = new Purchase(array(
					'item_type' => get_class($c), 
					'item_id' => $c->id, 
					'user_id' => $this->logged_in_user->id,
					'user_account_type' => $this->logged_in_user->account_type,
					'payment_amount' => $c->price
					));
					
					// $purchase->save();
					$c->created = date('D M Y @ G:i:s', $c->created);
				}
			}
			
			if($this->uri->segment(2, 0) === 'edit')
			{
				$this->load->library('form_validation');
				$this->load->view('account/cart/edit', array('cart' => $cart, 'amount_due' => $this->Cart->total_amount ));
			}
			else
			{
				$this->load->view('account/cart/index', array('cart' => $cart, 'amount_due' => $this->Cart->total_amount ));
			}
		}
		$this->load->view('templates/header');
	}
	
	public function add_item($item_type, $item_name, $item_id)
	{
		$this->action_allowed();
		
		$allowed_items = array('p' => 'Publication', 'f' => 'Folder');
		
		if(array_key_exists($item_type, $allowed_items) && $item_name && $item_id)
		{
			$class = $allowed_items[$item_type];
			
			$this->load->model($class);
			// Change folder name to 'title'
			$object = new $class();
			$item = $object->get_first(array('id' => $item_id, 'title' => App\Utility\StringMethods::unslug($item_name)), 'id, title, price');
			
			if($this->Cart->add_item($item))
			{
				if(isset($_SERVER['HTTPS_REFERER']))
					redirect($_SERVER['HTTPS_REFERER']);
						
				if(isset($_SERVER['HTTP_REFERER']))
					redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				// Can't add to cart error
				// redirect('cart');
			}
			
			redirect('cart');
		}
	}	
	
	public function action_allowed()
	{
		// Premium members do not need a cart
		if($this->logged_in_user->account_type == 'premium')
			redirect('cart');
	}
	
	public function add_items()
	{
		$this->load->model('Publication');
		$P = new Publication();
		
		$Publications = $P->get_all(array('user_id' => $this->logged_in_user->id));
		
		foreach($Publications as $Publication)
		$this->Cart->add_item($Publication);
	}
	
	public function remove_items()
	{
		$this->action_allowed();
		
		if($this->input->post('products') && is_array($this->input->post('products')))
		{
			$this->load->model('Publication');
			
			$allowed_items = array(1 => 'Publication', 2 => 'Document');
			
			foreach($this->input->post('products') as $product)
			{
				
				$product_info = array_combine(array('id', 'type'), explode(',', $product));
				
				if(array_key_exists($product_info['type'], $allowed_items))
				{
					$object = new $allowed_items[$product_info['type']]($product_info);
					$this->Cart->remove_item($object);
				}
			}
		}
		redirect('cart');
	}
	
	public function empty_cart()
	{
		$this->action_allowed();
		
		$this->Cart->make_empty();
	}
}
?>