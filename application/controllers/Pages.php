<?php 
class Pages extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function home()
	{
		$this->load->view('templates/header_home', array('title' => 'Gamalami', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/home');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));	
	}
	
	public function about()
	{
		$this->load->view('templates/header', array('title' => 'About us', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/about');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function terms()
	{
		$this->load->view('templates/header', array('title' => 'Terms of use', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/terms');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function help()
	{
		$this->load->view('templates/header', array('title' => 'Help', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/help');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function contact()
	{
		$this->load->view('templates/header', array('title' => 'Help', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/contact');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
	public function nothing_found()
	{
		$this->load->view('templates/header', array('title' => 'Nothing found', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('pages/nothing_found');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
}
?>