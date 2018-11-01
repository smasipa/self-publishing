<?php

class Writers extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->library('form_validation');
		$this->load->view('templates/header', array('title' => 'Get verified and start selling', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('account/writers/index');
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}
	
/* 	public function get_books_downloads()
	{
		$this->db->select('purchases.seller_id');
		$this->db->select('users.username');
		$this->db->select('users.email');
		
		$this->db->select_sum('purchases.payment_amount');
		$this->db->from($this->table);
		$this->db->join('users', "users.id = purchases.seller_id", 'left');
		
		$this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => TRUE));
		$this->db->group_by('seller_id');
		$result = $this->db->get()->result('User');
		if(is_array($result))
		{
			foreach($result as $user)
			{
				$user->payment_amount = App\Helper\String::price_to_float($this->get_amount_cut($user->payment_amount));
			}
		}
		return $result;
	} */	
	
	public function get_books_downloads($user_id)
	{
		// $this->db->select('sum(books.num_downloads) as total');
		$this->db->select('SUM(docs.num_downloads) AS total');
		
		$this->db->from('books');
		$this->db->join('documents AS docs', "books.id = docs.item_id AND docs.item_type = 'Book'", 'INNER');
		
		$this->db->where(array('books.user_id' => $user_id));
		$result = $this->db->get()->result();
		return $result;
	}	
	
	
	public function get_top_folders($user_id)
	{
		$this->db->select('folders.id');
		$this->db->select('folders.name');
		$this->db->select('sum(publications.num_views) as total_views');
		$this->db->from('folders');
		$this->db->join('publications', "folders.id = publications.folder_id", 'left');
		
		$this->db->where(array('folders.user_id' => $user_id));
		$this->db->order_by('total_views DESC');
		$this->db->group_by('folders.name');
		$result = $this->db->get()->result('Folder');
		
		return $result;
	}	
	
	public function books_sold($user_id)
	{
		$this->db->select('books.id');
		$this->db->select('books.title');
		$this->db->select('count(purchases.item_id) as total_sales');
		$this->db->from('purchases');
		$this->db->join('books', "purchases.item_id = books.id AND purchases.item_type = 'book'", 'left');
		
		// $this->db->where(array('purchases.status' => 'approved', 'is_seller_paid' => TRUE));
		$this->db->where(array('purchases.seller_id' => $user_id, 'purchases.status' => 'approved'));
		$this->db->group_by('books.title');
		$result = $this->db->get()->result('Book');
		
		return $result;
	}
	
	public function get_stats($username = null)
	{
		// Top 3 performing publications item
		// Top 3 performing folders
		// Add a notification feature for comments etc
		
		
		// Limit this feature to verified writers in future
		if(!$this->is_logged_in)
			App\Activity\Access::login();
		
		$user = $this->logged_in_user;
		
		if(!is_null($username) && $this->logged_in_user->is_admin())
		{
			$temp = $user->get_first(array('username' => $username));
			
			if($temp)
				$user = $temp;
		}
		elseif(!is_null($username))
		{
			App\Activity\Access::show_404();
		}
		
		
		$total_downloads;
		$data = array();
		$this->load->model('Purchase');
		$this->load->model('Book');
		$this->load->model('Publication');
		$this->load->model('document/Document');
		$this->load->model('Folder');
		$purchase = new Purchase();
		$document = new Document();
		
		$publication = new Publication();
		$folder = new Folder();
		$book = new Book();
		
		$data ['performance']['total_views'] = $user->num_views;
		$books_downloaded = $this->get_books_downloads($user->id);
		
		// Revisit
		$data ['performance']['books_downloaded'] = isset($books_downloaded[0]) && $books_downloaded[0]->total ? $books_downloaded[0]->total : 0;
		
		$data ['performance']['total_sales'] = $purchase->count(array('seller_id' => $user->id, 'approved'));
		
		$data ['performance']['total_due'] = $purchase->get_total_due($user->id);
		

		$top_publications = $publication-> get_all(array('user_id' => $user->id), 'id, title, num_views',array(40, 0), 'num_views DESC');
		
		
		
		$arr_pubs = array();
		
		if(is_array($top_publications))
		{
			foreach($top_publications as $pub)
			{
				$arr_pubs[] = array('name' => $pub->title, 'url' => $pub->get_url(), 'num_views' => $pub->num_views);
			}	
		}
	
		
		
		$arr_folders = array();
		$top_folders = $this->get_top_folders($user->id);
		if(is_array($top_folders))
		{
			foreach($top_folders as $folder)
			{
				$arr_folders[] = array('name' => $folder->name, 'url' => $folder->get_url(), 'num_views' => $folder->total_views);
			}
		}		
		
		$arr_books = array();
		$sold_books = $this->books_sold($user->id);
		
		if(is_array($sold_books))
		{
			foreach($sold_books as $book)
			{
				$arr_books[] = array('name' => $book->title, 'url' => $book->get_url(), 'total_sales' => $book->total_sales);
			}
		}
		
		
		$data['top_3']['publications'] = $arr_pubs;
		$data['sold_books'] = $arr_books;
		$data['top_3']['folders'] = $arr_folders;
		
		$this->load->view('templates/header', array('title' => 'Writer stats', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('writers/index', $data);
		
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}

}
?>