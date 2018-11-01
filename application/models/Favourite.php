<?php
class Favourite extends MY_Model{
	
	public $table = 'favourites';
	
	public $user_id;
	
	public $item_id;
	
	public $item_type;
	
	public $created;
	
	public $item;
	
	public function __construct($options = array())
	{
		$this->load->database();
		
		$this->_populate($options);
		
		if(is_object($this->item))
		{
			$this->item_type = get_class($this->item);
			$this->item_id = $this->item->id;
		}
	}
	
	public function add_item()
	{	
		if(!$this->exists())
		{
			$data = array(
			'item_id' => $this->item_id, 
			'item_type' => $this->item_type, 
			'user_id' => $this->user_id,
			'created' => time()
			);
				
			$this->db->insert($this->table, $data);
			return $this->db->insert_id();
		}
		// Do nothing
	}
	
	public function exists()
	{
		$exists = $this->count(array(
				'item_id' => $this->item_id, 
				'item_type' => $this->item_type, 
				'user_id' => $this->user_id));
				
		return $exists;
	}
	
	public function get()
	{
		$favourites = $this->get_all(array('user_id' => $this->user_id), null, null, 'created DESC');
		
		$favourites = !empty($favourites) ? App\Helper\Array_Methods::objects_to_array($favourites) : null;
		
		return !empty($favourites) ? $this->convert_to_objects($favourites) : null;
	}
	
	public function remove_item()
	{
		if($this->exists())
		{
			$data = array(
			'item_id' => $this->item_id, 
			'item_type' => $this->item_type, 
			'user_id' => $this->user_id);
			
			$this->db->where($data);
			return $this->db->delete($this->table);
		}
	}
	

	public function remove_all()
	{
		if($this->count(array('user_id' => $this->user_id)))
		{
			$this->db->where(array('user_id' => $this->user_id));
			return $this->delete($this->table);
		}
	}
}

?>