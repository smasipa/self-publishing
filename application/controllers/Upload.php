<?php
class Upload extends CI_Controller{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}
	
	public function index()
	{
		$data['title'] = 'Upload an image';
		$data['error'] = ' ';
		$this->load->view('templates/header',$data);
		$this->load->view('upload/index', $data);
		$this->load->view('templates/footer');
	}
	
	public function do_upload()
	{
		$config['upload_path']  = './uploads/';
		$config['allowed_types']= 'jpg|png';
		// $config['max_size']     = 100;
		// $config['max_width']	= 1024;
		// $config['max_height']	= 768;
		
		$this->load->library('upload', $config);
		
		if(!$this->upload->do_upload('userfile'))
		{
			$error = array('error' => $this->upload->display_errors());
			
			$this->load->view('upload/upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			
			$this->load->view('upload/upload_success', $data);
		}
	}
}
?>