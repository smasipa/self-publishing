<?php
class Admins extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		if(!$this->is_logged_in)
			App\Activity\Access::show_404();
		
		if(!$this->logged_in_user->is_admin())
		{
			$this->load->model('access/Activity');
			$activity = new Activity(array('user' => $this->logged_in_user));
			$activity->forbidden_admin_access();
			App\Activity\Access::show_404();
		}
	}
	
	public function index()
	{
		$offset_month = strtotime('last month');
		$end_month = strtotime('next month');
		
		$data = array(
		'perfomance' => array(
		'total_sales' => 0, 
		'total_members' => 0, 'total_views' => 0, 'total_signups' => 0),
		'premium_subscribers' => array(),
		'activities' => array()
		);
		
		$this->load->model('access/Visit');
		$this->load->model('access/Activity');
		$this->load->model('Membership');
		
		$visit = new Visit();
		$activity = new Activity();
		
		// return $activity->get_admin_activity(array('activities.action_type' => 'writer_approved'));
		$data['activities'] = $activity->get_admin_activity();
		
		$total_views = $visit->count(array('created'));
		
		$data['perfomance']['total_views'] = $total_views ? $total_views : 0;
		
		
		$this->load->model('Purchase');
		
		$purchase = new Purchase();
		$total_sales = $purchase->count(array('approved'));
		
		$data['perfomance']['total_sales'] = $total_sales ? $total_sales : 0;
		$user = new User();
		$total_members = $user->count(array('id'));
		
		$data['perfomance']['total_members'] = $total_members ? $total_members : 0;
		
		$this->load->view('admin/templates/header', array('title' => 'Admin', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array('title' => 'Admin', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/index', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function menu()
	{
		$this->load->view('admin/templates/header', array('title' => 'Admin', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/mobile/menu');
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function add_admin()
	{
		
	}
	
	public function writers_view()
	{
		$this->load->model('Writer');
		$writer = new Writer();
		
		$data = array('pending_writers' => array(), 'approved_writers' => array(), 'top_writers' => array());
		
		$pending_writers = $writer->get_all(array('status' => 'pending'));
		
		if($pending_writers)
		{
			$user = new User();
			foreach($pending_writers as $p_writer)
			{
				$user = $user->get_first(array('id' => $p_writer->user_id), 'username');
				if($user)
				{
					$user->url = $user->get_url();
					$p_writer->created = date('j/m/y @ G:i:s', $p_writer->created);
					$p_writer->user = $user;
					array_push($data['pending_writers'], $p_writer);
				}
			}
			unset($pending_writers);
		}
		
		$approved_writers = $writer->get_all(array('status' => 'approved'));
		if($approved_writers)
		{
			$user = new User();
			foreach($approved_writers as $writer)
			{
				$user = $user->get_first(array('id' => $writer->user_id), 'username');
				if($user)
				{
					$user->url = $user->get_url();
					$writer->created = date('j/m/y @ G:i:s', $writer->created);
					$writer->user = $user;
					array_push($data['approved_writers'], $writer);
				}
			}
			unset($approved_writers);
		}
		
		$user = new User();
		$top_writers = $user->get_all(array('num_views > ' => 0), 'username, num_views', array(10, 0), 'num_views DESC');
		if($top_writers)
		{
			foreach($top_writers as $writer)
			{
				$writer->url = $writer->get_url();
				
				$writer->created = date('j/m/y @ G:i:s', $writer->created);
				
				array_push($data['top_writers'], $writer);
			}
			unset($approved_writers);
		}
		// var_dump($data);return;
		// var_dump($writers);
		$user = new User();
		$this->load->view('admin/templates/header', array('title' => 'Admin | Writers', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/writers/index', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}	
	
	public function writer_details_view($username)
	{
		$this->load->model('Writer');
		$writer = new Writer();
		$user = new User();
		$user = $user->get_first(array('username' => $username), 'id, approved_writer, first_name, last_name');
		
		if($user)
		{
			$writer = $writer->get_first(array('user_id' => $user->id));
			
			if($writer)
			{
				$writer_data['username'] = ucfirst($username);
				$writer_data['approved'] = $user->is_approved();
				$writer_data['first_name'] = $user->first_name;
				$writer_data['last_name'] = $user->last_name;
				$writer_data['writer'] = $writer;
				$writer_data['payments'] = array();
				$this->load->model('Payment');
				$this->load->model('Purchase');
				
				
				$payment = new Payment(array('seller_id' => $user->id, 'admin_id' => $this->logged_in_user->id));
				
				for($i = 0; $i < 10; $i++)
				{
					$payment->amount_paid = rand(60, 456);
					// $payment->make_payment();
				}
				
				$writer_data['payments'] = $payment->get_successful_payments(array('user_id' => $user->id));
				
				$purchase = new Purchase();
				
				$writer_data['total_owed'] = $purchase->get_total_due($user->id);
				
				// return;
				$this->load->view('admin/templates/header', array('title' => 'Admin | Writers', 'is_logged_in' => $this->is_logged_in));
				$this->load->view('admin/templates/sidebar', array());
			
				$this->load->view('admin/writers/details', $writer_data);
				$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
				
				return;
			}
		}
		
		redirect('/monitor/writers');
	}
	
	public function writers_approve($username, $user_id)
	{
		$this->load->model('access/Activity');
		$this->load->model('Writer');
		
		$activity = new Activity(array('user' => $this->logged_in_user));
		
		$writer = new Writer();
		$user = new User();
		$user = $user->get_first(array('username' => $username, 'id' => $user_id), 'id, approved_writer');
		
		if($user)
		{
			$writer = $writer->get_first(array('user_id' => $user->id));
			
			if($writer)
			{
				if(!$user->is_approved())
				{
					if($writer->approve())
					$activity->writer_approved($user);
				}
				elseif($this->input->get('remove'))
				{
					if($writer->cancel())
					$activity->writer_rejected($user);
				}
				redirect("/monitor/writers/details/{$username}");
			}
		}
		
		App\Activity\Access::show_404();
	}
	
	public function user_payments()
	{
		// $user = $user->get_first(array('username' => $username), 'id, approved_writer');
		
		// if($user)
		// {
			// $writer = $writer->get_first(array('user_id' => $user->id));
			
			// if($writer)
			// {
				// $sell_data['username'] = ucfirst($username);
				// $writer_data['approved'] = $user->is_approved();
				// $writer_data['first_name'] = $user->first_name;
				// $writer_data['last_name'] = $user->last_name;
				// $writer_data['writer'] = $writer;
				
				
				// $this->load->view('admin/templates/header', array('title' => 'Admin', 'is_logged_in' => $this->is_logged_in));
				// $this->load->view('admin/templates/sidebar', array());
			
				// $this->load->view('admin/writers/details', $writer_data);
				// $this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
				
				// return;
			// }
		// }
	}
	
	public function payments()
	{
		$user = new User();
		
		$this->load->model('Payment');
		$this->load->model('Purchase');
		
		$data = array(
		'perfomance' => array('total_paid' => 0, 'total_due' => 0, 'total_writers_paid' => 0, 'total_writers_owed' => 0),
		'owed_writers' => array(),
		'paid_writers' => array()
		);
		
		$purchase = new Purchase();
		$payment = new Payment();
		
		
		$data['perfomance']['total_due'] = $purchase->get_total_due();
		
		$data['perfomance']['total_paid'] = $payment->get_total_paid();
		
		$data['perfomance']['total_writers_owed'] = $purchase->get_total_writers_owed();
		
		$data['perfomance']['total_writers_paid'] = $purchase->get_total_writers_paid();
		
		$data['owed_writers'] = $purchase->get_owed_writers();
		$data['paid_writers'] = $purchase->get_paid_writers();
		
		$payments = $payment->get_all(array('status' => 'confirmed'));

		$this->load->view('admin/templates/header', array('title' => 'Admin | Payments', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar');
		$this->load->view('admin/payments', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function premium_members()
	{
		$data = array('members' => array());
		
		$this->load->model('Membership');
		$member = new Membership();
		
		$data['members'] =  $member->get_premium_members();
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Premium membership', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/premium_members', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function purchases()
	{
		$data = array(
		'purchases_pending' => array(),
		'purchases_approved' => array()
		);
		
		$this->load->model('Purchase');
		$purchase = new Purchase();
		$purchases_approved = $purchase->admin_get_all(array('status' => 'approved'));
		
		// $purchases_pending = $purchase->get_all_items(array('status' => 'pending'));
		
		$data['purchases_approved'] = $purchases_approved;
		// $data['purchases_pending'] = $purchases_pending;
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Premium membership', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/purchases', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function make_payments($username)
	{
		$this->load->model('Writer');
		$this->load->model('Payment');
		
		$writer = new Writer();
		$user = new User();
		if(is_numeric($this->input->get('amount')))
		{
			$amount_due = $this->input->get('amount');
			// Convert float to integer to store in payments table database
			$amount_due = ($amount_due * 100);
			$user = $user->get_first(array('username' => $username), 'id, approved_writer');
			
			if($user)
			{
				$writer = $writer->get_first(array('user_id' => $user->id));
				
				if($writer)
				{
					if($user->is_approved())
					{
						$payment = new Payment(array('seller_id' => $user->id,  'admin_id' => $this->logged_in_user->id, 'amount_paid' => $amount_due));
						$payment->make_payment();
					}
					redirect("/monitor/writers/details/{$username}");
				}
			}
		}

		App\Activity\Access::show_404();
	}
	
	public function get_login_stats()
	{
		$this->load->model('access/Login_Activity');
		$lg_activity = new Login_Activity();
		$logins = $lg_activity->get_all_logins();
		
		$data = array(
		'logins' => $logins
		);
		$this->load->view('admin/templates/header', array('title' => 'Admin | User logins', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/activity/logins', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function get_signups()
	{
		$this->load->model('User');
		$user = new User();
		$users = $user->get_all(array('created'), 'account_type, email, username, id, created', null, 'created DESC');
		
		
		if($users)
		{
			foreach($users as $user)
			{
				$user->created = date('j/m/y', $user->created)." at ". date('g:ia', $user->created);
			}
			
		}
		
		$data = array(
		'signups' => $users
		);
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Sign ups', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/activity/signups', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function get_activity()
	{

		$this->load->model('access/Activity');
		$activity = new Activity();
		$data = array(
		'activities' => $activity->get_all_activity()
		);
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Sign ups', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/activity/index', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	
	public function get_site_content()
	{
		$this->load->model('access/Activity');
		$activity = new Activity();
		$data = array(
		'activities' => $activity->get_all_activity()
		);
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Sign ups', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/remove', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function ban_item($item_type, $item_id)
	{
		$allowed_list = array('User', 'Book', 'Publication', 'Folder');
		if(in_array($item_type, $allowed_list) && $commit = $this->input->get('commit'))
		{
		
			// Self banning not allowed!
			if($item_type == 'User' && $item_id == $this->logged_in_user->id)
			{
				if($url = App\Helper\String::get_referer())
					redirect($url);
				else
					redirect('/monitor/banned');
			}
		
			$this->load->model($item_type);
			$item = new $item_type(array('id' => $item_id));
			
			if($item->count())
			{
				
				$this->load->model('admin/Ban_Manager');
				$ban_manager = new Ban_Manager(array('admin_id' => $this->logged_in_user->id));
				
				if($commit == 1)
					$ban_manager->ban($item);
				elseif($commit == 2)
					$ban_manager->unban($item);
				
				if($url = App\Helper\String::get_referer())
					redirect($url);
				else
					redirect('/monitor/banned');
			}
		}
		
		App\Activity\Access::show_404();
	}
	
	public function get_banned_list()
	{
		$this->load->model('admin/Ban_Manager');
		$ban_manager = new Ban_Manager();
		$data['banned_list'] = $ban_manager->get_ban_list();
		
		$this->load->view('admin/templates/header', array('title' => 'Admin | Banned Items', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/banned_list', $data);
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function delete_user()
	{
		
	}
	
	public function delete_item()
	{
		
	}
}
?>