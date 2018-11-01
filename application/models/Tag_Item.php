<?php
class Tag_Item extends MY_Model{
	
	public $table = 'tag_items';
	
	public $tag_id;
	
	public $item_id;
	
	public $item_type;
	
	public $created;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}

	public function add_items($tags = array())
	{
		$this->db->insert_batch($this->table, $tags);
	}
	
	public function get_items($tag_ids = array())
	{
		$this->db->select('item_type, item_id');
		$this->db->distinct();
		$this->db->where_in('tag_id', $tag_ids);
		$result = $this->db->get($this->table)->result('array');
		return $result;
	}
	
	public function remove_from_tags()
	{
		$this->db->where(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
		return $this->db->delete($this->table);
	}
}

?>