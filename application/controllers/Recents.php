<?php 
class Recents extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
		if(!$this->logged_in_user->id)
			App\Activity\Access::login();
	}
	
	public function index()
	{
		$this->load->model('Recent_Item');
		$recent_item = new Recent_Item(array('user_id' => $this->logged_in_user->id));
		
		$recent_items = $recent_item->get();
		
		if($recent_items)
		{
			$this->load->model('Folder');
			
			$cover_images_cache = array();
			
			foreach($recent_items as $fav_types)
			{
				foreach($fav_types as $key => $recent_item)
				{
					if($recent_item->folder_id > 0)
					{
						$folder = new Folder(array('id' => $recent_item->folder_id));
						$recent_item->name = $recent_item->get_name();
						$recent_item->url = $recent_item->get_url();
						
						// Stop repeat database folder_image requests to save time
						if(!array_key_exists($recent_item->folder_id, $cover_images_cache))
						{
							$cover_images_cache[$recent_item->folder_id] = $folder->get_cover_image('small');
						}
						$recent_item->cover_image = $cover_images_cache[$recent_item->folder_id];
					}
					else
					{
					    $recent_item->name = null;
					    $recent_item = null;
					}
				}
			}
		}
		
		$this->load->view('templates/header', array('title' => 'Recently Viewed', 'is_logged_in' => $this->is_logged_in));
	
    	$this->load->view('recent_items/index', array('recent_items' => $recent_items));
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
				$this->load->model('Recent_Item');
				$recent_item = new Recent_Item(array('user_id' => $this->logged_in_user->id, 'item' => $object));
				
				switch($this->uri->segment(2, 0))
				{
					case 'add':
						$recent_item->add_item();
					break;
					
					case 'remove':
						$recent_item->remove_item();
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
		
		// show_404();
	}
	
	public function remove_all()
	{
		$this->load->model('Recent_Item');
		$recent_item = new Recent_Item(array('user_id' => $this->logged_in_user->id));
		if($Recent_Item->remove_all())
			$this->sessiont->set_flashdata('favourites_deleted', 'Your recents list has been emptied.');
		redirect('favourites');
	}
}
?>