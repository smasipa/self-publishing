<?php 
class Favourites extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
		if(!$this->logged_in_user->id)
			App\Activity\Access::login();
	}
	
	public function index()
	{
		$this->load->model('Favourite');
		$favourite = new Favourite(array('user_id' => $this->logged_in_user->id));
		
		$favourites = $favourite->get();
	
		if($favourites)
		{
			$this->load->model('Folder');
			$cover_images_cache = array();
			foreach($favourites as $fav_types)
			{
				foreach($fav_types as $favourite)
				{
					if($favourite->folder_id > 0)
					{
						$folder = new Folder(array('id' => $favourite->folder_id));
						$favourite->name = $favourite->get_name();
						$favourite->url = $favourite->get_url();
						
						// Stop repeat database folder_image requests to save time
						if(!array_key_exists($favourite->folder_id, $cover_images_cache))
						{
							$cover_images_cache[$favourite->folder_id] = $folder->get_cover_image('small');
						}
						
						$favourite->cover_image = $cover_images_cache[$favourite->folder_id];
					}
				}
			}
		}
		
		
		$this->load->view('templates/header', array('title' => 'Favourites', 'is_logged_in' => $this->is_logged_in));
		$this->load->view('favourites/index', array('favourites' => $favourites));
		$this->load->view('templates/footer', array('is_logged_in' => $this->is_logged_in));
	}

	public function add_remove_item($name_slug, $item_id, $item_type)
	{
		// $this->load->view('templates/header');
		$this->load->view('templates/footer');
		$name = App\Utility\StringMethods::unslug($name_slug);
		
		$allowed_items = array('p' => 'Publication', 'f' => 'Folder');
		
		if(array_key_exists($item_type, $allowed_items))
		{
			$class = $allowed_items[$item_type];
			
			$this->load->model($class);
			
			$object = new $class(array('id' => $item_id, 'title' => $name));
			
			$exists = $object->count();
			
			if($exists)
			{
				$this->load->model('Favourite');
				$favourite = new Favourite(array('user_id' => $this->logged_in_user->id, 'item' => $object));
				
				switch($this->uri->segment(2, 0))
				{
					case 'add':
						$favourite->add_item();
					break;
					
					case 'remove':
						$favourite->remove_item();
						$this->session->set_flashdata('favourite_item_deleted', $name.' has been deleted from your favourites list');
					break;
				}
			}
			
			$referer = isset($_SERVER['HTTPS_REFERER']) ?  $_SERVER['HTTPS_REFERER'] : 'favourites';
			
			if(isset($_SERVER['HTTPS_REFERER']))
			{
				$referer = $_SERVER['HTTPS_REFERER'];
			}
			elseif($_SERVER['HTTP_REFERER'])
			{
				$referer = $_SERVER['HTTP_REFERER'];
			}
			else
			{
				$referer = 'favourites';
			}
			
			redirect($referer);
		}
		
		show_404();
	}
	
	public function remove_all()
	{
		$this->load->model('Favourite');
		$favourite = new Favourite(array('user_id' => $this->logged_in_user->id));
		if($favourite->remove_all())
			$this->sessiont->set_flashdata('favourites_deleted', 'Your favourites has been emptied');
		redirect('favourites');
	}
}
?>