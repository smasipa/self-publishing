<?php
class search_results extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		
	}
	
	public function index()
	{
		$search_query = $this->input->get('q');
		$tag = $this->input->get('tag');
		
		$this->load->model('Search');
		$search = new Search(array('search_query' => $search_query));
		
		if($tag)
		{
			$search->search_type = $tag ? 'tag' : null;
			$search->search_query = $tag;
		}
		$results = $search->get_results();
		
		// var_dump($results);
		$this->load->view('templates/header', array('title' => 'Search  '.$search_query, 'is_logged_in' => $this->is_logged_in));
		$this->load->view('search/index', array('search_results' => $results));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
}

?>