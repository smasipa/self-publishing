<?php 
class Purchases extends MY_Controller{
	
	public function __construct()
	{
		parent::__construct();
	}	
	
	public function checkout($item_type, $item_title_slug, $item_id = null)
	{
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$allowed_items = array('book' => 'Book');
		
		
		$item_id = intval($item_id);
		if(array_key_exists($item_type, $allowed_items))
		{
			$data = array('chech_out_btn' => null);
			$class = $allowed_items[$item_type];
			
			$this->load->model($class);
			
			$object = new $class();
			
			$title = App\Utility\StringMethods::unslug($item_title_slug);
			
			$item_to_buy = $object->get_first(
			array('id' => $item_id, 'title' => $title));	
			
			// Check if user has already purchased this item.
			
			if($item_to_buy)
			{
				$this->load->model('Purchase');
				
				if($item_to_buy->is_banned)
					App\Activity\Access::blocked_url();

				$purchase = new Purchase(array('user_id' => $this->logged_in_user->id, 
				'user_account_type' => $this->logged_in_user->account_type,
				'item' => $item_to_buy
				));
				
				$item_to_buy->url = $item_to_buy->get_url();
				
				if($item_to_buy->price == 0)
					redirect($item_to_buy->url);
				
				$msg = null;
				if($purchase->is_pending())
				{
					
					// can only do one order at a time
					$msg = "Order is pending for  \"{$item_to_buy->title}\" , we can only process one order at a time.";
					
				}
				elseif($purchase->is_approved())
				{
					$msg = "You have already purchased  \"{$item_to_buy->title}\", click <a href = {$item_to_buy->url}>here</a> to download it.";
				}
				else
				{
					$this->load->model('payment_integration/Payfast_button');
					
					//$purchase->add_item($item_to_buy);  
					$data = array(
					'return_url' => BASE_URL.$item_to_buy->get_url(),
					'cancel_url' => BASE_URL."purchase/cancel/?type=".$class."&name=".$item_title_slug."&id=".$item_id,
					'notify_url' => BASE_URL."notify/purchase",
					'name_first' => "{$this->logged_in_user->first_name}",
					'name_last' => "{$this->logged_in_user->last_name}",
					'email_address' => "{$this->logged_in_user->email}",
					
					'amount' => "{$item_to_buy->get_price()}", // Amount needs to be in ZAR
					'item_name' => "{$item_to_buy->get_name()}",
					'item_description' => "a {$class}",
					'custom_int1' => $item_to_buy->id,
					'custom_int3' => $this->logged_in_user->id,
					'custom_str1' => 'item_id',	
					'custom_str2' => $class,
					'custom_str3' => 'uid'
					);
					
					$data['chech_out_btn'] = null;
					
					$this->load->model('geeWallet');
			
					$wallet = new GeeWallet();
					
					$geeWallet = $wallet->get_all(array('user_id' => $this->logged_in_user->getId()))[0];
					
					if($geeWallet && $geeWallet->getBalance() >= $item_to_buy->get_price())
					{
						$data['chech_out_btn'] = "<a class = 'btn btn-lg btn-default' href = 'buy/book/{$item_title_slug}/{$item_to_buy->id}'>
						<span style = 'color:#E78D2B'>gee</span><span style = 'color:rgb(137, 192, 12)'>Wallet</span></a>";
					}
					else
					{
						$pf_btn = new Payfast_button(null, $data);
						$pf_btn->create_button(array('class' => 'btn btn-check_out btn-success', 'value' => 'Check out'), "<a class = 'btn btn-danger' href ='{$item_to_buy->url}'>Cancel</a>");
		
						$data['chech_out_btn'] = $pf_btn->get_button();
					}
				}
				
				$item_to_buy->price = $item_to_buy->get_price();
				$item_to_buy->created = date('d/m/Y @ G:i:s', time());
				$item_to_buy->type = $class;
				
				$data['item'] = $item_to_buy;
				$data['msg'] = $msg;
				
				$this->load->view('templates/header', array('title' => 'Check Out | ' .$item_to_buy->title, 'is_logged_in' => $this->is_logged_in));
				$this->load->view('account/purchases/check_out', $data);
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
				return;
			}
		}
		
		App\Activity\Access::show_404();
	}
	
	public function view()
	{
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		$this->load->view('templates/header', array('title' => 'Purchases', 'is_logged_in' => $this->is_logged_in));
		
		// Get user's purchase history
		
		$this->load->model('Purchase');
		
		$purchase = new Purchase(array('user_id' => $this->logged_in_user->id));
		
		$purchases = $purchase->get_all_items();
		
		// $purchases[0]->approve();
		
		$this->load->view('account/purchases/index', array('purchases' => $purchases));
		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function notify()
	{
		$this->load->model('payment_integration/Notifier');
		
		$notifier = new Notifier();
		
		$notifier->init();
		
		$payfast_data = $notifier->get_posted_data();
		
		
		if(!empty($payfast_data))
		{
			$allowed_items = array('book' => 'Book', 'f' => 'Folder');	
			
			$this->load->model('Purchase');	
			
			$item_type = strtolower($payfast_data['custom_str2']);	
			
			$item_name_slug = $payfast_data['item_name'];	
			
			$this->load->model('User');
			
			$user = new User();
			
			$buyer_user = $user->get_first(array('id' => $payfast_data['custom_int3']), 'email, id, is_banned, account_type');
			
			if($buyer_user)
			{
				$item_id = $payfast_data['custom_int1'];	
				$item_price = $payfast_data['amount_gross'];	
				
				if(array_key_exists($item_type, $allowed_items))	
				{	
					$class = $allowed_items[$item_type];	
						
					$this->load->model($class);	
						
					// Use exceptions	
					$object = new $class();	
						
					$name = App\Utility\StringMethods::unslug($item_name_slug);	
						
					$item = $object->get_first(array('id' => $item_id, 'title' => $name));	
					
					if($item)	
					{	
						// Check for item's existence
						if(abs( floatval($item->get_price()) - floatval($item_price) ) > 0.01)
						{
							log_message('error', 'payfast: amount mismatch');
							die('Amounts Mismatch');
						}
						
						$notifier->init_curl();
						
						
						// $pfPaymentId = $pfData['pf_payment_id'];
						//query your database and compare in order to ensure you have not processed this payment allready
						$purchase = new Purchase(array('user_id' => $buyer_user->id, 
						'item' => $item,
						'user_account_type' => $buyer_user->account_type,
						'method' => 'external'
						));
						/*$pending_purchase = $purchase->get_first(array('item_id' => $item_id,
						'item_type' => $item_type, 
						'user_id' => $buyer_user->id));*/
						
						$pending_purchase = $purchase->add_item();
						if($pending_purchase)
						{
							switch($payfast_data['payment_status'])
							{
								case 'COMPLETE':
								// If complete, update your application, email the buyer and process the transaction as paid  
								$pending_purchase->approve();
								break;
								case 'FAILED':                    
								// There was an error, update your application and contact a member of PayFast's support team for further assistance
								$pending_purchase->cancel();
								break;
								case 'PENDING':
								// The transaction is pending, please contact a member of PayFast's support team for further assistance
								break;
								default:
								// If unknown status, do nothing (safest course of action)
								break;
							}
						}
						else
						{
							log_message('debug', 'payfast: Purchase does not exist '.implode(' ', $payfast_data));
							die('Purchase does not exist');
						}
					}
					else
					{
						log_message('debug', 'payfast: Item does not exist '.implode(' ', $payfast_data));
						die('Item does not exist');
					}
				}
			}
			else
			{
				log_message('debug', 'payfast: User does not exist '.implode(' ', $payfast_data));
				die('User does not exist');
			}
		}
	}
	
	public function geeWalletPurchase($item_title, $item_id)
	{
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		// Check for item's existence and sufficient balance
		
		$this->load->model('geeWallet');
		
		$wallet = new GeeWallet();	
		
		$title = App\Utility\StringMethods::unslug($item_title);
		
		$geeWallet = $wallet->get_all(array('user_id' => $this->logged_in_user->id))[0];			
		
		$this->load->model('Book');
		$bookObj = new Book();
		$item = $bookObj->get_first(array('id' => $item_id, 'title' => $title));
		
		
		//var_dump($geeWallet->getIntBalance() - $item->getIntPrice());	
	
		//&& instanceof($item, 'Book')
		if(is_object($item)  && $geeWallet)			
		{
			// Does account have sufficient funds?
			if($geeWallet->getIntBalance() >= $item->getIntPrice())
			{
				$this->load->model('Purchase');
				$purchase = new Purchase(array('user_id' => $this->logged_in_user->id, 		
				'item' => $item,		
				'user_account_type' => 'standard',
				'method' => 'wallet'
				));		
		
				$this->load->model('Password');
				$pass = $this->input->post('password');
				if($pass && $this->input->post('confirm'))
				{
					//$hash_password = Password::hash_password("Captivate1");
					if($this->logged_in_user->getPassword() === Password::hash_password($pass))
					{
						$pending_purchase = $purchase->add_item();	
						if(is_object($pending_purchase))		
						{
							// If complete, update your application, email the buyer and process the transaction as paid  		
							$pending_purchase->approve();	
							$geeWallet->spend($item->getIntPrice());
							
							//redirect("/".$item->get_url());
							// Redirect to successful purchase page and store book url
						}	
						// This is a repeat purchase do not deduct funds
						redirect("/".$item->get_url());
					}
				}
				
				$this->load->view('templates/header', array('title' => "geeWallet - Confirm Purchase", 'is_logged_in' => $this->is_logged_in));
				$this->load->view('geeWallet/confirm_purchase', array('url' => $this->uri->uri_string));
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			}
			else
			{
				$this->session->geeWallet_url = "check_out/book/{$item_title}/{$item_id}";
				
				redirect('account/geeWallet/insufficient_funds');
				
				return FALSE;
				
			}
		}	
		else
		{
			App\Activity\Access::show_404();
		}
	}
	
	public function cancel()
	{
		$item_id = $this->input->get('id');
		$item_title_slug = $this->input->get('name');
		$item_type = strtolower($this->input->get('type'));
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$allowed_items = array('book' => 'Book');
		
		
		$item_id = intval($item_id);
		
		if(array_key_exists($item_type, $allowed_items))
		{
			$class = $allowed_items[$item_type];
			
			$this->load->model($class);
			
			$object = new $class();
			
			$title = App\Utility\StringMethods::unslug($item_title_slug);
			
			$item_to_buy = $object->get_first(
			array('id' => $item_id, 'title' => $title));	
			
			// Check if user has already purchased this item.
			if($item_to_buy)
			{
				//print "purchase was cancelled";
				$this->load->view('templates/header', array('title' => "Purchase cancelled | {$title}", 'is_logged_in' => $this->is_logged_in));
				$this->load->view('account/purchases/cancelled', array('item_name' => $title, 'item_url' => $item_to_buy->get_url()));
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
				
				return;
			}
		}
		App\Activity\Access::show_404();
	}
	
	public function success()
	{
		
	}
}
?>