<?php
class Access extends MY_Controller
{
	private $heading;
	private $message;
	private $links = array();
	private $redirect_state;
	
	public function log_access_error()
	{
		
	}
	
	public function redirect_state($error_state)
	{
		switch($error_state)
		{
			
			case ACCESS_LOGIN:
				$this->login_required();
			break;
			
			case ACCESS_PAGE_NOT_FOUND:
				$this->page_not_found();
			break;			
			
			case ACCESS_FILE_NOT_FOUND:
				$this->file_not_found();
			break;
			
			case ACCESS_PREMIUM_MEMBER:
				$this->go_premium();
			break;
			
			case ACCESS_GET_APPROVED:
				$this->get_approved();
			break;			
			
			case ACCESS_BLOCKED_ITEM:
				$this->blocked_url();
			break;
			
			default:
			show_404();
			break;
		}
	}
	
	// Limits access to features until logged
	public function login_required()
	{
		$this->heading = 'Please login';
		$this->message = 'For you to access this feature you must be a member. <a href ="/login">Login</a> using your existing account details or <a href ="/register">register</a> a new account.';
		$this->load->view('templates/header', array('title' => 'Login required', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('access/index', array('heading' => $this->heading, 'message' => $this->message,'links' => $this->links));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function go_premium()
	{
		redirect('/premium');
		
		$this->heading = 'Go premium';
		$this->message = 'This item is for premium members';
		$this->load->view('templates/header', array('title' => 'Go premium', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('access/index', array('heading' => $this->heading, 'message' => $this->message,'links' => $this->links));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	// Serious writers must apply to get approved
	public function get_approved()
	{
		redirect('/get_verified/info');
	}
	
	// Applies to pdf documents
	public function file_not_found()
	{
		$this->heading = 'File not found';
		$this->message = 'The file you requested does not exist';
		$this->load->view('templates/header', array('title' => 'File not found', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('access/index', array('heading' => $this->heading, 'message' => $this->message,'links' => $this->links));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}	
	
	// 404 error
	public function page_not_found()
	{
		$this->heading = 'Oops! 404';
		$this->message = 'The page you requested was not found on this server.';
		$this->load->view('templates/header', array('title' => 'Page not found', 'is_logged_in' => $this->is_logged_in));
		
		$this->load->view('access/index', array('heading' => $this->heading, 'message' => $this->message,'links' => $this->links));
		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function buy()
	{
		
	}
	
	// Applies to pdf documents
	public function blocked_url()
	{
		// $this->load->model('access/Activity');
		
		// $activity = new Activity();
		// $activity = banned_item_access(Item $item)
		
		$this->heading = 'Blocked link';
		$this->message = 'The url you requested has been blocked/deleted by system administrator.';
		$this->load->view('templates/header', array('title' => 'Missing item', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('access/index', array('heading' => $this->heading, 'message' => $this->message,'links' => $this->links));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
}
?>