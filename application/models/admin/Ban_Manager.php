<?php
class Ban_Manager extends MY_Model{
	
	public $table = 'banned_items';
	
	public $admin_id;
	
	public $item_type;
	
	public $item_id;
	
	public $item_name;
	
	public $is_banned = TRUE;
	
	public $created;
	
	public $modified;
	
	public function __construct($options = array())
	{
		$this->load->database();
		$this->_populate($options);
	}
	
	public function save()
	{
		$data = array(
		'item_id' => $this->item_id,
		'item_type' => $this->item_type,
		'is_banned' => $this->is_banned,
		'admin_id' => $this->admin_id,
		'created' => time(),
		'modified' => time()
		);
		
		return $this->db->insert($this->table, $data);
	}
	
	public function update($data = array())
	{
		if(!empty($data))
		$data['modified'] = time();
	
		$this->db->set($data);
		$this->db->where(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
		return $this->db->update($this->table);
	}
	
	public function init_item($item)
	{
		$this->item = $item;
		if($this->item)
		{
			$this->item_id = $this->item->id;
			$this->item_type = get_class($item);
		}
	}
	
	public function ban(Item $item)
	{
		$this->init_item($item);
		
		if(!$this->exists())
		{
			$this->save();
		}
		else
		{
			$this->update(array('is_banned' => TRUE));
		}
		
		return $item->ban();
	}
	
	public function unban(Item $item)
	{
		$this->init_item($item);
		
		if($this->exists())
		{
			if($this->update(array('is_banned' => FALSE)))
			{
				return $item->unban();
			}
		}
		
		return FALSE;
	}
	
	public function exists()
	{
		return $this->count(array('item_id' => $this->item_id, 'item_type' => $this->item_type));
	}
	
	public function get_ban_list($where = array(), $fields = null , $limit = null)
	{
		$this->load->model('User');
		$user = new User();
		$users_table = $user->get_table();
		
		// $default_where = array("{$users_table}.account_type" => 'admin', "{$this->table}.is_banned" => 1);
		$default_where = array("{$this->table}.is_banned" => 1);
		
		if($where)
		{
			$default_where = array_merge($default_where, $where);
		}
		
		$this->db->select("{$users_table}.email, {$users_table}.username");
		$this->db->select("{$this->table}.*");
		$this->db->from($this->table);
		$this->db->join($users_table, "{$this->table}.admin_id = {$users_table}.id", 'left');
		$this->db->where($default_where);
		$this->db->order_by("{$this->table}.modified DESC");
		$results = $this->db->get()->result();
		
		return $this->results_to_object($results);
	}
}
?>