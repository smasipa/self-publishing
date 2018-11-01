<?php

class Memberships extends MY_Controller{
	
	public $allowed_subscription = array('monthly' => 'Monthly', 'semi_annually' => 'Semi_Annually', 'annually' => 'Annually');
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_offers()
	{
		$this->load->view('templates/header', array('title' => 'Go premium','is_logged_in' => $this->is_logged_in));
		$this->load->view('account/membership/index');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function checkout($purchase_type, $type = null)
	{
		$this->load->model('Membership');
		
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		if(!array_key_exists($type, $this->allowed_subscription))
			App\Activity\Access::show_404();
		
		if($purchase_type != 'membership')
			App\Activity\Access::show_404();
		
		$class = ucfirst($this->allowed_subscription[$type]);
		
		$this->load->model('membership/'.$class);
		
		$membership = new $class();
		
		$this->load->model('payment_integration/Payfast_button');			
								
		$pf_data = array(			
		'return_url' => BASE_URL.'premium',			
		'cancel_url' => BASE_URL."membership/cancelled/?pf_key=".$membership->pf_key,			
		'notify_url' => BASE_URL.'notify/membership',			
		'name_first' => $this->logged_in_user->first_name,			
		'name_last' => $this->logged_in_user->last_name,			
		'email_address' => $this->logged_in_user->email,
		'amount' => $membership->price,		
		'item_name' => $class. ' premium subscription',			
		'item_description' => 'Valid for '.$membership->get_duration(),			
		'custom_int1' => $this->logged_in_user->id,			
		'subscription_type' => 1,	
		'recurring_amount' => $membership->price, // Amount needs to be in ZAR			
		'frequency' => $membership->pf_key,
		'cycles' => 0	
		);			
					
		$pf_btn = new Payfast_button(null, $pf_data);			
		$pf_btn->create_button(array('class' => 'btn btn-check_out btn-success', 'value' => 'check out'), "<a class = 'btn btn-danger' href ='/premium'>Cancel</a>");			
					
		$data['chech_out_btn'] = $pf_btn->get_button();			
					
		
		$old_membership = $membership->get_first(array('user_id' => $this->logged_in_user->id));
		
		$msg = 'Premium membership subscription';
		// Move to membership notify 
		
		if($old_membership && $old_membership->expired)
		{
			$msg = 'You are renewing your membership subscription';
			// $this->logged_in_user->renew_membership($membership);
		}
		elseif(!$old_membership)
		{
			// $this->logged_in_user->save_new_membership($membership);
			$msg = 'You are renewing your membership subscription';
		}
		
		$membership->type = $class;

		$membership->created = date('j/m/Y', time());
		
		$data['item'] = $membership;
		$data['msg'] = $msg;
		
		$this->load->view('templates/header', array('title' => 'Check Out | ' .ucfirst($type), 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/membership/check_out', $data);
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
			$allowed_items = array(3 => 'Monthly', 5 => 'Semi_Annually', 6 => 'Annually');	
			
			$this->load->model('Purchase');	
			
			$type = (int)$payfast_data['frequency'];
			
			$class = ucfirst($this->allowed_subscription[$type]);
			
			$this->load->model('membership/'.$class);
		
			$membership = new $class();
			
			if($membership->price != $payfast_data['recurring_amount'])
				die('Amount mismatch');
			
			$this->load->model('User');
			
			$user = new User();
			
			$buyer_user = $user->get_first(array('id' => $payfast_data['custom_int1']), 'email, id, is_banned, account_type');
			
			// Member exists
			if($buyer_user)
			{
				switch($payfast_data['payment_status'])			
				{			
					case 'COMPLETE':			
					// If complete, update your application, email the buyer and process the transaction as paid  			
						$old_membership = $membership->get_first(array('user_id' => $buyer_user->id));
						
						if($old_membership)
						{
							$msg = 'Renew subscription';
							$this->logged_in_user->renew_membership($membership);
						}
						else
						{
							$this->logged_in_user->save_new_membership($membership);
							$msg = 'New subscription';
						}	
					break;			
					case 'FAILED':                    			
					// There was an error, update your application and contact a member of PayFast's support team for further assistance			
					log_message('error', 'Membership purchase failed');
					
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
				log_message('error', 'User not found on server');
			}
		}
	}
	
	public function cancelled()
	{
		$pf_key = (int)$this->input->get('pf_key');
		
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$allowed_items = array(3 => 'Monthly', 5 => 'Semi_Annually', 6 => 'Annually');
		var_dump($pf_key );
		if(array_key_exists($pf_key, $allowed_items))
		{
			$title = $allowed_items[$pf_key];
			
			$title = App\Utility\StringMethods::unslug($title);
			
			$this->load->view('templates/header', array('title' => "Purchase cancelled | {$title}", 'is_logged_in' => $this->is_logged_in));
			$this->load->view('account/purchases/cancelled', array('item_name' => $title. ' premium subscription' , 'item_url' => 'premium'));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
		App\Activity\Access::show_404();
	}
}
?>