<?php 
class GeeWallet_controller extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
	}
	
	// Add password verification for geeWallet money spending
	public function index()
	{
		// $this->load->model('Visit');
		if(!$this->is_logged_in)
		App\Activity\Access::login();
	
		$this->load->library('form_validation');	
		$this->load->model('User');		
		
		$user = $this->logged_in_user;	
			
		if($user)	
		{	
			$this->load->model('geeWallet');
			
			$wallet = new GeeWallet();
			
			
			$geeWallet = $wallet->get_first(array('user_id' => $user->getId()));
			
			// $geeWallet->recharge(98.65 * 100, 'manual', 1);
			if($geeWallet)
			{
				$this->load->view('templates/header', array('title' => 'geeWallet - Virtual funds', 'is_logged_in' => $this->is_logged_in));
				$this->load->view('geeWallet/index', array('ref_code' => $user->getId(), 
				'balance' => $geeWallet->getBalance(), 'total_spent' => $geeWallet->getTotalSpent()));
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			}
			else
			{
				$geeWallet = new GeeWallet(array('user_id' => $user->getId()));
				$geeWallet->save();
			}
		}
	}
	
	public function recharge()
	{
		
		if(!$this->is_logged_in)
			App\Activity\Access::show_404();
		
		// Only admin access
		if(!$this->logged_in_user->is_admin())
		{
			$this->load->model('access/Activity');
			$activity = new Activity(array('user' => $this->logged_in_user));
			$activity->forbidden_admin_access();
			App\Activity\Access::show_404();
		}
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ref_code', 'Reference Code',"regex_match[/[a-zA-Z][0-9]+/]");
		
		$matched_details = null;
		
		$success = false;
		
		if($this->form_validation->run() == TRUE)
		{
			preg_match("/[0-9]+/",$this->input->post('ref_code'), $ref_code);
			
			$ref_code = implode($ref_code);
			
			if($ref_code)
			{
				$user = new User();
				$valid_user = $user->get_first(array('id' => $ref_code));
				
				$this->load->model('Password');
				$pass = $this->input->post('password');
				
				if(!$valid_user)
					$this->form_validation->set_rules('ref_code', 'Reference Code',"rule1", array('rule1' => 'Invalid reference code'));
				
				if($valid_user)
				{
					$matched_details = "<br /><span class = 'success'> Email " . $valid_user->getEmail() . " <br />" .
					$valid_user->getFirstName() .
					"  " . $valid_user->getLastName();
					
					// Amount validation
					$this->form_validation->set_rules('amount', 'Amount',"numeric|required");
					
					$is_valid_pass = $this->logged_in_user->getPassword() === Password::hash_password($pass);
					
					if(!$is_valid_pass)
					{
						$this->form_validation->set_rules('password', 'Password',"rule1", 
						array('rule1' => 'Invalid password'));					
					}
			
					if($this->form_validation->run() === TRUE && $is_valid_pass)
					{
						
						$this->load->model('geeWallet');
						
						$wallet = new GeeWallet();
						
						
						$geeWallet = $wallet->get_first(array('user_id' => $valid_user->getId()));
						
						$geeWallet->recharge($this->input->post('amount') * 100, 'manual', $this->logged_in_user->getId());				
						
						$success = true;
						// Send email...
					}					
				}else
				{
					// To notify admin that the reference code is not valid
					$this->form_validation->run();
				}
			}
			
		}
		
		// $this->load->view('admin/templates/header', array('title' => 'Admin | Banned Items', 'is_logged_in' => $this->is_logged_in));
		// $this->load->view('templates/header', array('title' => 'geeWallet - Virtual funds', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/header', array('title' => 'geeWallet - Virtual funds', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar');
		$this->load->view('admin/geeWallet/admin_recharge', array('matched_details' => $matched_details, 'success' => $success));		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));		
	}
	
	public function insufficientFunds()
	{
		$purchase_url = $this->session->geeWallet_url ? $this->session->geeWallet_url : null;
		
		if(is_null($purchase_url))
		{
			redirect('account/geeWallet');
		}
		
		$this->session->geeWallet_url = null;
		$this->load->view('templates/header', array('title' => 'geeWallet - Virtual funds', 'is_logged_in' => $this->is_logged_in));		
		$this->load->view('geeWallet/insufficient_funds', array('purchase_url' => $purchase_url));		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function allRecharges()
	{
		$this->load->model('GeeWallet_recharge');
		
		$geeWalletRecharge = new GeeWallet_recharge();
		
		$recharges = $geeWalletRecharge->getAllRecharges();
		
		
		if(is_array($recharges))
		{
			foreach($recharges as &$recharge)
			{
				$recharge['created'] = date('d/m/Y @ i:g a', $recharge['created']);
				$recharge['amount'] = $recharge['amount'] / 100;
			}
		}
		
		$data['recharges'] = $recharges;
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | All geeWallet Recharges', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar');
		$this->load->view('admin/geeWallet/all_recharges', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
}
?>