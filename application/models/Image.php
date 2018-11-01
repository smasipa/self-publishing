<?php
class Image extends MY_Model{
	
	public $id;
	
	public $name;
	
	public $location;
	
	public $created;
	
	public $modified;
	
	public $item_id;
	
	public $item_type;
	
	public $user_id;
	
	public $crop_width = 95;
	
	public $crop_height = 140;
	
	public $default_image = 'default.jpg';
	
	public $table = 'images';
	
	public function __construct($options = array())
	{
		$this->load->database();
		
		// Can be overriden by 'item_type' in options
		$this->item_type = $this->get_calling_class();
		
		$this->_populate($options);
	}
	
	public function save()
	{
		$this->load->library('Upload');
		
		if(isset($_FILES['image']))
		{
			$handle = new Upload($_FILES['image']);
			
			// Is file on server?
			if ($handle->uploaded){

				$images_dir = ".".$this->get_dir();
				
				$file_name = $this->user_id ." ".date('YmdGIs',time()). rand(0, 7632)." ". $this->item_id;
				$handle->file_new_name_body = $file_name;
				// $handle->allowed = array('application/pdf','application/msword', 'image/*');
				
				$handle->allowed = array('image/*');
				$handle->file_max_size = 2*1024*1024; // 2mb
				$handle->Process($images_dir);

				// we check if everything went OK
				if ($handle->processed) {
					
					// Save file name in database
					if($this->item_id)
					{
							$data = array(
							'item_type' => $this->item_type,
							'item_id' => $this->item_id,
							'name' => $handle->file_dst_name,
							'type' => $handle->file_dst_name_ext
							);
							
							$this->db->insert($this->table, $data);
					}
					
					// Do some cropping
					$handle->file_new_name_body = $file_name.'_sm';
					$handle->image_resize     = true;
					$handle->image_ratio_crop = true;
					$handle->image_x          = 95; // width
					$handle->image_y          = 140; // height
					
					$handle->Process($images_dir);
					
					return $this->db->insert_id();
					
				}else {
					// one error occured
					return $handle->error;
				}			
			}
		}
	}

	public function get_dir()
	{
		if($this->item_type)
		{
			switch($this->item_type)
			{
				case 'folder' : 
				return '/assets/uploads/thumbs/publications/';				
				
				case 'book' : 
				return '/assets/uploads/thumbs/books/';				
				
				case 'user' : 
				return '/assets/uploads/thumbs/avatars/';
			}
		}
	}
	
	public function get_image_link($size = 'medium')
	{
		$this->item_type = strtolower($this->get_calling_class());
		$orig_image = new Image();
		$image  = $orig_image->get_first(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
		
		if(isset($image->name))
		{
			if($size == 'small')
			{
				preg_match('#(\.\w+)#', $image->name, $ext);
				$val = preg_replace('#(\.\w+)#', '_sm',$image->name);
				$image->name = $val.$ext[0];
			}
			
			$image->location = $this->get_dir(). $image->name;
			return $image;
		}
		else // Get default display picture
		{
			$orig_image->location = $this->get_dir(). $this->default_image;
			return $orig_image;
		}
	}
	
	public function delete()
	{
		if($this->id)
		{
			// Implementation
			$this->db->where(array('id' => $this->id, 'item_type' => $this->item_type, 'item_id' => $this->item_id));
			
			// var_dump($this);
			
			// Delete image files associated with item
			if($this->db->delete($this->table))
			{
				
				preg_match('#(\.\w+)#', $this->name, $ext);
				
				$val = preg_replace('#(\.\w+)#', '_sm',$this->name);
				
				$this->location = preg_replace('#^/#', '', $this->get_dir());
				
				$large_img = $this->location .$this->name;
				if(file_exists($large_img))
				{
					unlink($large_img);
					
					$this->name = $val.$ext[0];
					
					if(file_exists($this->location . $this->name))
					{
						unlink($this->location . $this->name);
					}
				}
			}
		}
	}
}
?>