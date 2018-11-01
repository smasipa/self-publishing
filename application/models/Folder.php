<?php
include_once(INTERFACES_DIR.'Item.php');
class Folder extends MY_Model implements Item{
	
	public $id;
	
	public $user_id;
	
	public $name;
	
	public $created;
	
	public $modified;
	
	public $num_items = 0;
	
	public $table = 'folders';
	
	public $description;
	
	// image object
	public $load_cover_image = FALSE;	
	
	// @image Image
	public $image = FALSE;
	
	public $load_object = FALSE;
	
	public $cover_image = null;
	
	public $is_banned;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
		
		if($this->load_object == TRUE)
		{
			$this->_populate($this->get_first(array('id' => $options['id'], 'user_id' => $options['user_id'])));
		}
		
		if($this->load_cover_image == TRUE)
		{
			$size = isset($options['image_size']) ? $options['image_size'] : 'medium';
			$this->cover_image = $this->get_cover_image($size);
		}
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'modified' => time(),
		'description' => $this->sanitize_string($this->description),
		
		'num_items' => $this->num_items
		);
		
		if($this->name)
		$data['name'] = $this->name;
	
		if($this->id)
		{
			$data = $this->filled_fields($data);
			$this->db->set($data);
			$this->db->where(array('id' => $this->id));
			$this->db->update($this->table);
		}
		else
		{
			$data['created'] = time();
			$this->db->insert($this->table, $data);
			$this->id = $this->db->insert_id();
		}
		return $this->id;
	}
	
	public function ban()
	{
		return $this->update(array('is_banned' => TRUE));
	}
	
	public function unban()
	{
		return $this->update(array('is_banned' => FALSE));
	}
	
	/* @item object */
	public function add_item(&$item)
	{
		// Does it belongs to a folder
		if($this->num_items > 0)
			$this->remove_old_item($item);
	
		// Throw item into a new folder
		$item->folder_id = $this->id;
		
		if($item->save())
		{
			return $this->update(array('num_items' => $this->num_items + 1));
		}
	}	
	
	/* @item object */
	public function remove_old_item(&$item)
	{
		$folder = new Folder(array('load_object' => TRUE, 'id' => $item->folder_id));
		if($folder)
		{
			$folder->update(array('num_items' => $folder->num_items--));
			$item->folder_id = 0;
			$item->save();
			return TRUE;
		}
	}
	
	public function initialize_image()
	{
		$this->load->model('Image');
		
		$this->image = new Image(array('item_id'=> $this->id, 'user_id'=> $this->id));
		
		if($this->id)
		{
			$old_image = $this->image->get_first(array('item_id' => $this->id, 'item_type' => 'folder'));
			
			if($old_image)
			{
				$this->image = $old_image;
				$this->image->user_id = $this->id;
			}
		}
	}
	
	public function upload_image()
	{
		$this->initialize_image();
		if($this->image && $this->image->save())
		{
			$this->image->delete();
			return TRUE;
		}
	}	
	
	public function get_cover_image($size = 'small')
	{
		$this->initialize_image();
		return $this->image->get_image_link($size);
	}	
	
	public function delete_image()
	{
		if($this->image)
			$this->image->delete();
	}
	
	public function get_name()
	{
		return $this->name;
	}
	
	public function get_db_name()
	{
		if($this->id)
		{
			$book = $this->get_first(array('id' => $this->id), 'name');
			
			return $book ? $book->name : null;
		}
	}
	
	public function get_author_name()
	{
		$this->load->model('User');
		$user = new User();
		$user = $user->get_first(array('id' => $this->user_id));
		return $user ? $user->username : 'Admin';
	}
	
	public function get_url()
	{
		return 'publications/'.$this->id.'/'.App\Utility\StringMethods::make_slug($this->name);
	}
}
?>