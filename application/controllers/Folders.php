<?php
class Folders extends MY_Controller{
	
	public $user_id;
	public $username;
	public $form_url;
	public $own_folder;
	
	public function __construct()
	{
		parent::__construct();
		$this->user_id = $this->authenticate->get_user_id();
		$this->username = $this->authenticate->get_username();
		$this->form_url = $this->uri->uri_string;
	}
	
	public function get_folder_items()
	{
		$folder_id = $this->uri->segment(2, 0);
		$folder_name = $this->uri->segment(3, 0);
		$folder_name = App\Utility\StringMethods::unslug($folder_name);
		
		$this->load->model('Folder');
		
		$this->load->model('User');
		
		$folder = new Folder();
		
		$folder = $folder->get_first(array('id'=> $folder_id, 'name' => $folder_name));
		
		// $this->load->library('Sitemap');
		// $sitemap = new Sitemap($folder);
		if($folder)
		{
			
			if($folder->is_banned)
				App\Activity\Access::blocked_url();
			
			$folder->cover_image = $folder->get_cover_image();
			
			$folder->author = $folder->get_author_name();
			
			$folder->created = date('j M Y', $folder->created);
			$this->load->model('Publication');
			$publication = new Publication();
			
			$this->own_folder = $folder->user_id == $this->user_id ? TRUE : FALSE;
			
			
			// $publications = $publication->get_all(array('folder_id' => $folder_id, 'id, title, price, accessibility'));
			$publications = $publication->get_all(array('folder_id' => $folder_id), 'id, title, accessibility');
			
			if($publications)
			{
				foreach($publications as &$pub_item)
				{
					$pub_item->url = App\Utility\StringMethods::make_slug($pub_item->title)."/".$pub_item->id;
					if($this->own_folder)
					{
						$pub_item->edit = "publications/edit/".$pub_item->url;
					}
				}
			}

			$this->load->view('templates/header', array('title' => $folder->name, 'is_logged_in' => $this->is_logged_in));
			
			// $this->load->view('publications/posts', array('folder_name' => $folder_name, 
			// 'edit' => $this->own_folder ? 'folders/edit/'.$folder->id : null, 
			// 'posts' => $publications));
			$folder->description = nl2br($folder->description);
			
			
			$data = array('folder' => $folder, 
			'publications' => $publications, 
			'edit' => $this->own_folder ? 'folders/edit/'.$folder->id : null);
			
			$this->load->library('Social_media');
			$social_media = new Social_media($folder->name, $folder->get_url(), $folder->cover_image->location, 'Check out this publication ');
			$data['social_media'] = $social_media->get_all_btns();	
			
			$this->load->view('folders/info', $data);
			
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in, 'facebook_js_btn' => $social_media->init_fb_sharescript()));
			return;
		}
		App\Activity\Access::show_404();
	}
	
	public function get_folders()
	{
		$this->load->model('Folder');
		$this->load->model('User');
		
		$folder = new Folder();
		
		$this->load->library('Pagination');
		$per_page = 20;
		$total_num_folders = $folder->count(array('created < ' => time(), 'num_items > ' => 0));
		
		$init_page = array(
		'base_url'=> BASE_URL.'publications/page/',
		'total_rows' => $total_num_folders, 'per_page' => $per_page,
		'full_tag_open' => '<li>',
		'full_tag_close' => '</li>',
		'num_tag_open' => '<li>',
		'num_tag_close' => '</li>',
		'cur_tag_open' => "<li class = 'active'><a>",
		'prev_tag_open' => "<li class = 'prev'>",	
		'prev_tag_close' => "</li'>",		
		'next_tag_open' => '<li>',	
		'next_tag_close' => '</li>',	
		'cur_tag_close' => '</a></li>',	
		'first_tag_open' => "<li class = 'first'>",
		'first_tag_close' => '</li>',	
		'last_tag_open' => "<li class = 'last'>",
		'last_tag_close' => '</li>'
		);		
	
		$user = null;
		
		if($this->uri->segment(2, 0) === 'publications' && $author_name = $this->uri->segment(1, 0))
		{
			
			$this->load->model('User');
			$user = new User();
			$user = $user->get_first(array('username' => $author_name));
			
			if(!$user)
			{
				show_404();
			}
			
			$init_page['base_url'] = BASE_URL.$user->username .'/publications/page/';
		}
		
		$this->pagination->initialize($init_page);
		
		$offset = $this->uri->segment(3, 0);
		
		if($offset > 0)
			$offset = $offset - 1;
		
		$end_point = $offset  + $per_page;
		
		if($user && $user->id)
		{
			$folders = $folder->get_all(array('user_id' => $user->id, 'num_items > ' => 0), null, array($end_point, $offset), 'created DESC');
		}
		else
		{
			$folders = $folder->get_all(array('created < ' => time(), 'num_items > ' => 0), null, array($end_point, $offset), 'created DESC');
		}
		
		if($folders)
		{
			$author = new User();
			$valid_folders = array();
			
			$logged_user_id = $this->authenticate->get_user_id();
			$own_item = $user &&  $user->id == $logged_user_id ? TRUE : FALSE;
			$author_name = $user ? $user->username : null;
			
			$author_name = $author_name && $own_item ? "You" : $author_name;
			
			foreach($folders as &$folder)
			{
				$folder->cover_image = $folder->get_cover_image('small');
				$folder->created = date('j M y', $folder->created);
				$author = $user ? $user : $author->get_first(array('id' => $folder->user_id));
				$folder->username = $user ? '' : $author->username;
				$this->load->model('Publication');
				
				$publication = new Publication();
				
				if($folder->num_items == 1)
				{
					
					// $publication = new Publication();
					$publication = $publication->get_first(array('folder_id' => $folder->id), 'title, id');
					
					// Does this folder have really a publication
					if($publication)
					{
						$folder->name = $publication->title;
						$folder->url = App\Utility\StringMethods::make_slug($publication->title)."/".$publication->id;
						if($own_item)
						{
							$folder->edit = "publications/edit/".$folder->url;
						}
						array_push($valid_folders, $folder);
					}
					else
					{
						// Nothing yet
					}
				}
				else
				{
					$folder->url = 'publications/'.$folder->id."/".App\Utility\StringMethods::make_slug($folder->name);
					array_push($valid_folders, $folder);
				}
				
			}
			
			$this->load->view('templates/header', array('title' => 'Publications', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('publications/index', array('author_name' => $author_name, 'folders' => $valid_folders, 'pagination' => $this->pagination->create_links()));
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
		
		// No publications yet
		
		App\Activity\Access::nothing_found();
	}
	
	public function page()
	{
		$this->load->library('Pagination');
	}
	
	public function save()
	{
		$folder_id = $this->uri->segment(3, 0);
		
		$this->load->model('Folder');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'callback_valid_title');
		$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
		
		// Edit old folder
		if($this->user_id && $folder_id && $this->uri->segment(2, 0) == 'edit')
		{
			$folder = new Folder(array('load_object' => TRUE, 'id' => $folder_id, 'user_id' => $this->user_id));
			
			// Folder exists
			if($folder->name)
			{
				$publications = null;
				
				// Not empty, get folder's publications
				if($folder->num_items)
				{
					$this->load->model('Publication');
					$publications = new Publication();
					$publications = $publications->get_all(array('folder_id' => $folder->id, 'user_id' => $folder->user_id), 'id, title');
				}
				
				// Save changes
				if($this->input->post('save') && $this->form_validation->run() == TRUE)
				{
					$folder->name = $this->input->post('name');
					$folder->description = $this->input->post('description');
					
					if($folder->save() && isset($_FILES['image']) && $_FILES['image']['type'])
					$folder->upload_image();
					redirect('publications/'.$folder->id.'/'.App\Utility\StringMethods::make_slug($folder->name));
				}
				else
				{
					$folder_name = $folder->name;
					$folder->name = $this->input->post('name') ? $this->input->post('name') : $folder->name;
					
					$this->load->view('templates/header', array('title' => 'Edit | '.$folder_name, 'is_logged_in' => $this->is_logged_in));
					$this->load->view('folders/edit',array('form_url' => $this->form_url, 'folder' => $folder, 'publications' => $publications));
					$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
					return;
				}
			}
		}
		
		// Creating new folder
		if($this->user_id && $this->uri->segment(2, 0) == 'create')
		{
			if($this->input->post('save') && $this->form_validation->run() == TRUE)
			{
				$folder = new Folder(array('name' => $this->input->post('name'), 'user_id' => $this->user_id));
				if($folder->save())
				$folder->save_cover_image();
			
				// var_dump($folder);
			}
			else
			{
				$this->load->view('templates/header', array('title' => 'Save Folder', 'is_logged_in' => $this->is_logged_in));	
				$this->load->view('folders/edit',array('form_url' => $this->form_url));	
				$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			}
			return;
		}
		
		show_404();
	}
	
	public function add_item()
	{
		$pub_id = $this->uri->segment(4, 0);
		$pub_title = $this->uri->segment(3, 0);	
		$pub_title = App\Utility\StringMethods::unslug($pub_title);
		
		$this->load->model('publication');
		
		// Check if publication belongs to logged in user
		$publication = new Publication();
		$publication = $publication->get_first(array('user_id' => $this->user_id, 'id' => $pub_id, 'title' => $pub_title));
		
		if($publication)
		{
			$this->load->library('form_validation');
			$this->load->model('Folder');
			$this->form_validation->set_error_delimiters("<div class = 'form-errors alert-danger'>","</div>");
			
			$folder = new Folder();
			
			$folders = $folder->get_all(array('user_id' => $this->user_id),null,null,'name, num_items');
			
			$data = array(
				'form_url' => $this->uri->uri_string,
				'pub_id' => $pub_id,
				'publication_title' => $pub_title,
				'folders' => array()
			);
			
			$existing_folders = array();
			
			// If folder id provided , check for its existence
			if($folders)
			{
				foreach($folders as $folder)	
				{	
					array_push($data['folders'], array('id' => $folder->id, 'name' => $folder->name));
					
					$existing_folders [$folder->id] = $folder;
				}
			}
	
			
			if($this->input->post())
			{
				$chosen_fol_id = $this->input->post('main_folder');
				
				// Put publication in old folder
				if(array_key_exists($chosen_fol_id, $existing_folders) && $publication->folder_id != $chosen_fol_id)
				{
					$fol_edit = $existing_folders[$chosen_fol_id];
					$fol_edit->add_item($publication);
					redirect($fol_edit->get_url());
				}
			}
			
			$this->load->view('templates/header', array('title' => 'Add items to folder', 'is_logged_in' => $this->is_logged_in));
			$this->load->view('folders/add_item', $data);
			$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
			return;
		}
		
		// If all else fails
		App\Activty\Access::show_404();
	}
	
	public function valid_title($value)
	{
		$this->form_validation->set_message('valid_title', 'Only letters, numbers, and spaces allowed. Please re-edit Title.');
		$eval = preg_match("#^[^\d][a-z0-9\s]+$#i", $value);
		return $eval ? TRUE : FALSE;
	}
	
	public function valid_name($value)
	{
		$this->form_validation->set_message('valid_name', 'Only numbers, spaces, and letters allowed.');
		$eval = preg_match("/^[^_][a-z0-9_]{2,24}[^_]$/i", $value);
		$eval = $eval == 1 ? true : false;
		return $eval;
	}
}
?>