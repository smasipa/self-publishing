<?php

class Comment extends MY_Model{
	
	public $user_id;
	
	public $id;
	
	public $item_id;
	
	public $item_type;
	
	public $text;
	
	public $created;
	
	public $modified;
	
	public $table = 'comments';
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
		$this->item_type = $this->get_calling_class();
	}
	
	public function save()
	{
		$data = array(
		'user_id' => $this->user_id,
		'item_id' => $this->item_id,
		'item_type' => $this->item_type,
		'text' => $this->sanitize_string($this->text),
		'modified' => time()
		);
		
		if($this->id)
		{
			$data = filled_fields($data);
			$this->db->set($data);
			$this->where(array('id'=>$this->id, 'user_id' => $this->user_id));
			return $this->update($this->table);
		}
		
		$data['created'] = time();
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}
	
	public function delete()
	{
		if($this->id)
		{
			// $this->db->where(array('id' => $this->id, 'user_id' => $this->user_id));
			$this->db->where(array('id' => $this->id, 'item_type' => $this->item_type));
			return $this->db->delete($this->table);
		}
	}
}
?>