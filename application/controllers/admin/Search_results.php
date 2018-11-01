<?php
class Search_Results extends MY_Controller
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
		
		if(is_array($results))
		{
			foreach($results as $items_array)
			{
				if(!empty($items_array))
				{
					foreach($items_array as $item)
					{
						$item->item_type = get_class($item);
					}
				}
			}
		}
		
		$this->load->view('admin/templates/header', array('title' => 'Search  '.$search_query, 'is_logged_in' => $this->is_logged_in));
		$this->load->view('admin/templates/sidebar', array());
		$this->load->view('admin/search/index', array('search_results' => $results));
		$this->load->view('admin/templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
}

?>