<?php
class Errors extends CI_Controller{
	/* Function to provide 404 reponse */
	public function show_404()
	{
		// Set response header to 404
		$this->output->set_status_header('404');
		// $this->load->view('errors/html/errror_404');
		show_404();
	}
}


?>