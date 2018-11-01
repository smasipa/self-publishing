<?php
class Recent_Item extends MY_Model{
	
	public $table = 'recent_items';
	
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
		$data = array(
			'item_id' => $this->item_id, 
			'item_type' => $this->item_type, 
			'user_id' => $this->user_id
			);
			
			
		if(!$this->exists())
		{
			$data['created'] = time();
			$this->db->insert($this->table, $data);
			return $this->db->insert_id();
		}
		else
		{
			$this->db->set(array('created' => time()));
			$this->db->where($data);
			return $this->db->update($this->table);
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
		$recents = $this->get_all(array('user_id' => $this->user_id), null, null);
		
		$recents = !empty($recents) ? App\Helper\Array_Methods::objects_to_array($recents) : null;
		
		// Search for a way to order items in a where_in statement
		return !empty($recents) ? $this->convert_to_objects($recents, null, 'created DESC') : null;
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