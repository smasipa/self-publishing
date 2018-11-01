<?php
class Downloads extends MY_Controller{
	
	public $purchases;
	
	public function __construct()
	{
		parent::__construct();
		if(!$this->is_logged_in)
			App\Activity\Access::login();
	}
	
	public function get($item_type, $item_title_slug, $item_id)
	{
		$item_id = intval($item_id);
		
		$allowed_items = array('p' => 'Publication', 'f' => 'Folder', 'book' => 'Book');
		
		if(array_key_exists($item_type, $allowed_items))
		{
			$class = $allowed_items[$item_type];
			
			$this->load->model($class);
			
			$object = new $class();
			
			$title = App\Utility\StringMethods::unslug($item_title_slug);
			
			$item_to_download = $object->get_first(
			array('id' => $item_id, 'title' => $title));			
			
			if($item_to_download)
			{
				if($item_to_download->is_banned)
					App\Activity\Access::blocked_url();
				
				$item_to_download->init_document();
				$file = $item_to_download->get_document();
				
				// Has a valid document file
				if($file)
				{
					if($this->logged_in_user->is_admin() // Admin
					|| isset($item_to_download->user_id) 
				
					&& $this->logged_in_user->id == $item_to_download->user_id // Owner
					|| $this->logged_in_user->is_premium() // Premium member
					)
					{
						// continue
					}
					else
					{
						if(isset($item_to_download->price))	
						{	
							//Check if item has already been purchased by logged_in_user	
							
							$this->load->model('Purchase');	
								
							$purchase = new Purchase(array('user_id' => $this->logged_in_user->id, 'item' => $item_to_download));	
								
							$item_to_download->is_purchased = null;	
								
							$document = null;	
								
							if(get_class($item_to_download) == 'Book' && $item_to_download->price && !$purchase->is_approved())	
							{	
								// Only if a price was set	
								redirect('/check_out/'.$item_to_download->get_url());	
							}	
						}	
							
						if(isset($item_to_download->accessibility) 
						&& $item_to_download->accessibility == 'premium'
						&& $this->logged_in_user->account_type != 'premium')	
						{	
							App\Activity\Access::go_premium();	
						}
					} 	
					
					$this->load->model('Downloader');
					$Downloader = new Downloader(array('item' => $file, 'title' => $title));
					
					$Downloader->download();
					return;					
				}
			}
			App\Activity\Access::show_404();
		}
	}
}
?>